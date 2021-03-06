<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Broker_base_model CLASS
 *
 * 经纪人信息基础类 提供挂靠公司的关系
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Signatory_info_base_model extends MY_Model
{

  /**
   * 经纪人表名
   * @var string
   */
  private $_tbl = 'signatory_info';

  /*
    导出数据
    */
  public function count_data_by_cond($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from('signatory_info as b');
    $this->dbback_city->join('register_signatory as r', 'b.id = r.signatory_info_id', 'left');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  public function get_data_by_cond($where, $start = 0, $limit = 20,
                                   $order_key = 'id', $order_by = 'ASC')
  {
    $this->dbback_city->select('r.id, r.signatory_info_id, r.status, r.corpname, r.storename, r.ip, b.phone, b.truename');
    $this->dbback_city->from('signatory_info as b');
    $this->dbback_city->join('register_signatory as r', 'b.id = r.signatory_info_id', 'left');
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_all_signatory($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from($this->_tbl);
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  protected $signatory_info_config = array(
    'package' => array(1 => '总经理', 2 => '经纪人'),
    'group' => array(1 => '未认证', 2 => '已认证', 3 => '付费'),
  );

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('blacklist_base_model');
    $this->agent_blacklist = 'agent_blacklist';
    //$this->load->model('newhouse_sync_account_base_model');
  }

  /**
   * 获取表名
   * @return string
   */
  public function get_tbl()
  {
    return $this->_tbl;
  }

  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }
    $this->_select_fields = $select_fields_str;
  }


  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }

  /**
   * 批量注册，注册后直接可登录，默认门店正式经纪人角色
   * @param int $signatory_id 经纪人编号
   * @return boolean
   */
  public function batch_init_signatory($signatory_id, $phone, $department_id = 0, $role_id = 0, $role_level = 0, $truename = '', $city_spell = '')
  {
    if ($city_spell == '') {
      $city = $_SESSION[WEB_AUTH]["city"];
    } else {
      $city = $city_spell;
    }
    $insert_data = array();
    $insert_data['signatory_id'] = $signatory_id;
    $insert_data['phone'] = $phone;
    //如果不填姓名则默认为手机号码
    if ($truename == '') {
      $truename = $phone;
    }
    $insert_data['truename'] = $truename;
    $insert_data['register_time'] = time();//默认有效期为一年
    //查看经纪人有效时间
    $this->load->model('signatory_base_model');
    $signatory = $this->signatory_base_model->get_by_id($signatory_id);
    if ($signatory) {
      $insert_data['expiretime'] = $signatory['expiretime'];
    }
    $insert_data['group_id'] = 1;
    $insert_data['package_id'] = 2;
    //查找门店和公司编号
    $this->load->model('department_base_model');
    if ($department_id == 0) {
      $insert_data['department_id'] = 0;
      $dist_id = 0;
      $insert_data['company_id'] = 0;
    } else {
      $insert_data['department_id'] = $department_id;
      $department = $this->department_base_model->get_by_id($department_id);
      $insert_data['company_id'] = $department['company_id'];
      $dist_id = $department['dist_id'];
    }
    //角色编号
    $insert_data['role_id'] = $role_id;
    //角色等级
    $insert_data['role_level'] = $role_level;
    //公司的名义注册的用户是有效帐号
    $insert_data['status'] = 1;
    $insert_data['area_id'] = 1;

    //单条插入
    $insertid = $this->insert($insert_data);
    //同步插入到黑名单
    $insert_blacklist_data = array();
    $insert_blacklist_data['username'] = $truename;
    $insert_blacklist_data['tel'] = $phone;
    $insert_blacklist_data['addtime'] = time();
    $this->blacklist_base_model->add_agent_blacklist($insert_blacklist_data);

    //同步到房管家
    /***
     * $xffx = $this->get_signatory_info($insertid);
     * if (isset($xffx) && !empty($xffx)) {
     * $xffxdata = array(
     * 'ks_id' => $insert_data['department_id'],
     * 'kcp_id' => $insert_data['company_id'],
     * 'ag_dist' => $dist_id,
     * 'ag_id' => $xffx['signatory_id'],
     * 'ag_name' => $insert_data['truename'],
     * 'ag_phone' => $xffx['phone'],
     * 'city' => $city,
     * 'password' => md5('123456'),
     * 'sex' => 0,
     * 'ag_status' => 1,
     * 'addtime' => time(),
     * 'update_time' => time()
     * );
     * //$this->newhouse_sync_account_base_model->addonedepartment($xffxdata);
     * }
     **/
    return $insertid > 0 ? $insertid : false;
  }

  /**
   * 注册成功后初始化经纪人数据
   * @param int $signatory_id 经纪人编号
   * @return boolean
   */
  public function init_signatory($signatory_id, $phone, $department_id = 0, $role_id = 0, $truename = '', $city_spell = '')
  {
    if ($city_spell == '') {
      $city = $_SESSION[WEB_AUTH]["city"];
    } else {
      $city = $city_spell;
    }
    $insert_data = array();
    $insert_data['signatory_id'] = $signatory_id;
    $insert_data['phone'] = $phone;
    //如果不填姓名则默认为手机号码
    if ($truename == '') {
      $truename = $phone;
    }
    $insert_data['truename'] = $truename;
    $insert_data['register_time'] = time();//默认有效期为一年
    //查看经纪人有效时间
    $this->load->model('signatory_base_model');
    $signatory = $this->signatory_base_model->get_by_id($signatory_id);
    if ($signatory) {
      $insert_data['expiretime'] = $signatory['expiretime'];
    }
    $insert_data['group_id'] = 1;
    $insert_data['package_id'] = 2;
    //查找门店和公司编号
    $this->load->model('department_base_model');
    if ($department_id == 0) {
      $insert_data['department_id'] = 0;
      $dist_id = 0;
      $insert_data['company_id'] = 0;
    } else {
      $insert_data['department_id'] = $department_id;
      $department = $this->department_base_model->get_by_id($department_id);
      $insert_data['company_id'] = $department['company_id'];
      $dist_id = $department['dist_id'];
    }
    //角色编号
    $insert_data['role_id'] = $role_id;
    //公司的名义注册的用户是有效帐号
    $insert_data['status'] = 1;
    $insert_data['area_id'] = 1;

    //单条插入
    $insertid = $this->insert($insert_data);
    //同步插入到黑名单
    $insert_blacklist_data = array();
    $insert_blacklist_data['username'] = $truename;
    $insert_blacklist_data['tel'] = $phone;
    $insert_blacklist_data['addtime'] = time();
    $this->blacklist_base_model->add_agent_blacklist($insert_blacklist_data);

    //同步到房管家
    /**
     * $xffx = $this->get_signatory_info($insertid);
     * if (isset($xffx) && !empty($xffx)) {
     * $xffxdata = array(
     * 'ks_id' => $insert_data['department_id'],
     * 'kcp_id' => $insert_data['company_id'],
     * 'ag_dist' => $dist_id,
     * 'ag_id' => $xffx['signatory_id'],
     * 'ag_name' => $insert_data['truename'],
     * 'ag_phone' => $xffx['phone'],
     * 'city' => $city,
     * 'password' => md5('123456'),
     * 'sex' => 0,
     * 'ag_status' => 1,
     * 'addtime' => time(),
     * 'update_time' => time()
     * );
     * //11
     * //$this->newhouse_sync_account_base_model->addonedepartment($xffxdata);
     *
     * $url = 'http://adminxffx.fang100.com/fktdata/addonedepartment';
     * $this->load->library('Curl');
     * Curl::fktdata($url, $xffxdata);
     * }
     **/
    //增加积分
    $this->load->model('api_signatory_credit_base_model');
    $this->api_signatory_credit_base_model->set_signatory_param(array('signatory_id' => $signatory_id), 1);
    $this->api_signatory_credit_base_model->register();
    //增加等级分值
    $this->load->model('api_signatory_level_base_model');
    $this->api_signatory_level_base_model->set_signatory_param(array('signatory_id' => $signatory_id), 1);
    $this->api_signatory_level_base_model->register();
    return $insertid > 0 ? $insertid : false;
  }
    /**
     * 注册成功后初始化经纪人数据
     * @param int $signatory_id 经纪人编号
     * @return boolean
     */
    public function init_signatory_admin($signatory_id, $phone, $department_id = 0, $role_id = 1, $truename = '', $city_spell = '')
    {
        if ($city_spell == '') {
            $city = $_SESSION[WEB_AUTH]["city"];
        } else {
            $city = $city_spell;
        }
        $insert_data = array();
        $insert_data['signatory_id'] = $signatory_id;
        $insert_data['phone'] = $phone;
        //如果不填姓名则默认为手机号码
        if ($truename == '') {
            $truename = $phone;
        }
        $insert_data['truename'] = $truename;
        $insert_data['register_time'] = time();//默认有效期为一年
        //查看经纪人有效时间
        $this->load->model('signatory_base_model');
        $signatory = $this->signatory_base_model->get_by_id($signatory_id);
        if ($signatory) {
            $insert_data['expiretime'] = $signatory['expiretime'];
        }
        $insert_data['group_id'] = 2;
        $insert_data['package_id'] = 2;
        //查找门店和公司编号
        $this->load->model('department_base_model');
        if ($department_id == 0) {
            $insert_data['department_id'] = 0;
            $dist_id = 0;
            $insert_data['company_id'] = 1;
        } else {
            $insert_data['department_id'] = $department_id;
            $department = $this->department_base_model->get_by_id($department_id);
            $insert_data['company_id'] = $department['company_id'];
            $dist_id = $department['dist_id'];
        }
        //角色编号
        $insert_data['role_id'] = $role_id;
        $insert_data['role_level'] = 1;
        //公司的名义注册的用户是有效帐号
        $insert_data['status'] = 1;
        $insert_data['area_id'] = 1;

        //单条插入
        $insertid = $this->insert($insert_data);
        //同步插入到黑名单
      //  $insert_blacklist_data = array();
      //  $insert_blacklist_data['username'] = $truename;
      //  $insert_blacklist_data['tel'] = $phone;
     //   $insert_blacklist_data['addtime'] = time();
     //   $this->blacklist_base_model->add_agent_blacklist($insert_blacklist_data);

        //同步到房管家
        /**
         * $xffx = $this->get_signatory_info($insertid);
         * if (isset($xffx) && !empty($xffx)) {
         * $xffxdata = array(
         * 'ks_id' => $insert_data['department_id'],
         * 'kcp_id' => $insert_data['company_id'],
         * 'ag_dist' => $dist_id,
         * 'ag_id' => $xffx['signatory_id'],
         * 'ag_name' => $insert_data['truename'],
         * 'ag_phone' => $xffx['phone'],
         * 'city' => $city,
         * 'password' => md5('123456'),
         * 'sex' => 0,
         * 'ag_status' => 1,
         * 'addtime' => time(),
         * 'update_time' => time()
         * );
         * //11
         * //$this->newhouse_sync_account_base_model->addonedepartment($xffxdata);
         *
         * $url = 'http://adminxffx.fang100.com/fktdata/addonedepartment';
         * $this->load->library('Curl');
         * Curl::fktdata($url, $xffxdata);
         * }
         **/
        //增加积分
        //$this->load->model('api_signatory_credit_base_model');
       // $this->api_signatory_credit_base_model->set_signatory_param(array('signatory_id' => $signatory_id), 1);
      //  $this->api_signatory_credit_base_model->register();
        //增加等级分值
      //  $this->load->model('api_signatory_level_base_model');
     //   $this->api_signatory_level_base_model->set_signatory_param(array('signatory_id' => $signatory_id), 1);
       // $this->api_signatory_level_base_model->register();
        return $insertid > 0 ? $insertid : false;
    }
  /**
   * 注册成功后初始化经纪人数据
   * @param int $signatory_id 经纪人编号
   * @return boolean
   */
  public function new_init_signatory($signatory_id, $phone, $department_id = 0, $role_id = 0, $role_level = 0, $truename = '', $city_spell = '', $sex = '')
  {
    if ($city_spell == '') {
      $city = $_SESSION[WEB_AUTH]["city"];
    } else {
      $city = $city_spell;
    }
    $insert_data = array();
    $insert_data['signatory_id'] = $signatory_id;
    $insert_data['phone'] = $phone;
    //如果不填姓名则默认为手机号码
    if ($truename == '') {
      $truename = $phone;
    }
    $insert_data['truename'] = $truename;
    $insert_data['register_time'] = time();//默认有效期为一年
    //查看经纪人有效时间
    $this->load->model('signatory_base_model');
    $signatory = $this->signatory_base_model->get_by_id($signatory_id);
    if ($signatory) {
      $insert_data['expiretime'] = $signatory['expiretime'];
    }
    $insert_data['group_id'] = 2;//1：未认证 2：已认证
    $insert_data['package_id'] = 2;
    //查找门店和公司编号
    $this->load->model('department_base_model');
    if ($department_id == 0) {
      $insert_data['department_id'] = 0;
      $dist_id = 0;
      $insert_data['company_id'] = 0;
    } else {
      $insert_data['department_id'] = $department_id;
      $department = $this->department_base_model->get_by_id($department_id);
      $insert_data['company_id'] = $department['company_id'];
      $insert_data['master_id'] = $department['master_id'];
      $dist_id = $department['dist_id'];
    }
    //角色性别
    if ($sex == 0) {
      $insert_data['sex'] = 0;
    } else if ($sex == 1) {
      $insert_data['sex'] = 1;
    }
    //角色编号
    $insert_data['role_id'] = $role_id;
    //角色等级
    $insert_data['role_level'] = $role_level;
    //公司的名义注册的用户是有效帐号
    $insert_data['status'] = 1;
    $insert_data['area_id'] = 1;
    //单条插入
    $insertid = $this->insert($insert_data);
    return $insertid > 0 ? $insertid : false;
  }

  public function get_signatory_info($insertid)
  {
    $xffx = array();
    //查询字段
    if ($this->_select_fields) {
      $this->db_city->select($this->_select_fields);
    }
    if (is_array($insertid)) {
      $this->db_city->where_in('id', $insertid);
      $xffx = $this->db_city->get('signatory_info')->result_array();
    } else if (intval($insertid) > 0) {
      $this->db_city->where('id', $insertid);
      $xffx = $this->db_city->get('signatory_info')->row_array();
    }
    return $xffx;
  }

    public function get_signatory_by_role_level($company_id, $role_level)
    {
        $signatorys = array();
        $this->db_city
            ->where('company_id', $company_id)
            ->where('status', 1);
        //查询字段
        if ($this->_select_fields) {
            $this->db_city->select($this->_select_fields);
        }
        $rolewhere = [];
        if (is_array($role_level)) {
            foreach ($role_level as $key => $value) {
                $rolewhere[] = '(role_level REGEXP  ",' . $value . ',|^' . $value . ',|,' . $value . '$|^' . $value . '$")';
            }
        } else if (intval($role_level) > 0) {
            $rolewhere[] = '(role_level REGEXP  ",' . $role_level . ',|^' . $role_level . ',|,' . $role_level . '$|^' . $role_level . '$")';
        }
        $this->db_city->where(implode('or', $rolewhere));
        $signatorys = $this->db_city->get('signatory_info')->result_array();
        return $signatorys;
    }
  //添加官网注册的经纪人信息
  public function add_register($signatory_info_id, $corpName, $storeName, $ip)
  {
    $insert_data = array();
    $insert_data['corpname'] = $corpName;
    $insert_data['storename'] = $storeName;
    $insert_data['signatory_info_id'] = $signatory_info_id;
    $insert_data['status'] = 1;
    $insert_data['ip'] = $ip;
    //单条插入
    $this->db_city->insert('register_signatory', $insert_data);
    return $this->db_city->insert_id();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function web_count_by($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from('signatory_info as b');
    $this->dbback_city->join('register_signatory as r', 'b.id = r.signatory_info_id', 'left');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_signatory_login_log($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from('login_log');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  /**
   * 门店有多少有效经纪人
   * @param array $department_id 门店编号
   * @return int
   */
  public function count_by_department_id($department_id)
  {
    if (is_array($department_id)) {
      $this->dbback_city->where_in('department_id', $department_id);
    } else if (intval($department_id) > 0) {
      $this->dbback_city->where('department_id', $department_id);
    }
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('expiretime >= ', time());
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 通过门店编号获取经纪人记录
   * @param int $department_id 门店编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_department_id($department_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //判断查询多个门店
    if (is_array($department_id)) {
      $this->dbback_city->where_in('department_id', $department_id);
    } else if (intval($department_id) > 0) {
      $this->dbback_city->where('department_id', $department_id);
    }
    //查询条件
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('expiretime >= ', time());
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 通过公司编号获取经纪人记录
   * @param int $company_id 公司编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_company_id($company_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //判断查询多个公司
    if (is_array($company_id)) {
      $this->dbback_city->where_in('company_id', $company_id);
    } else if (intval($company_id) > 0) {
      $this->dbback_city->where('company_id', $company_id);
    }
    //查询条件
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('expiretime >= ', time());
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 获取经纪人列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by($where, $start = 0, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 获取经纪人列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function web_get_all_by($where, $start = 0, $limit = 20,
                                 $order_key = 'id', $order_by = 'DESC')
  {
    $this->dbback_city->select('r.id, r.signatory_info_id, r.status, r.corpname, r.storename, r.ip, b.phone, b.truename');
    $this->dbback_city->from('signatory_info as b');
    $this->dbback_city->join('register_signatory as r', 'b.id = r.signatory_info_id', 'left');
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  //获取官网注册的经纪人信息
  public function get_register_info($id)
  {
    $where = 'id = ' . $id;
    $this->dbback_city->select('*');
    $this->dbback_city->from('register_signatory');
    $this->dbback_city->where($where);
    return $this->dbback_city->get()->row_array();
  }

  //根据经纪人id获取官网注册的经纪人信息
  public function get_register_info_by_signatoryid($id)
  {
    $where = 'signatory_info_id = ' . $id;
    $this->dbback_city->select('*');
    $this->dbback_city->from('register_signatory');
    $this->dbback_city->where($where);
    return $this->dbback_city->get()->row_array();
  }

  /**
   * 获取经纪人登录日志
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条登录日志组成的二维数组
   */
  public function get_signatory_login_log($where, $start = 0, $limit = 20,
                                          $order_key = 'dateline', $order_by = 'DESC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('login_log');
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 根据查询条件返回一条经纪人表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的公司表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 公司编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过经纪人编号获取记录
   * @param int $signatory_id 经纪人编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_signatory_id($signatory_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }

    if (is_array($signatory_id)) {
      $this->dbback_city->where_in('signatory_id', $signatory_id);
      $signatorys = $this->dbback_city->get($this->_tbl)->result_array();
    } else if (intval($signatory_id) > 0) {
      $this->dbback_city->where('signatory_id', $signatory_id);
      $signatorys = $this->dbback_city->get($this->_tbl)->row_array();
    }
    return $signatorys;
  }

  /**
   * 返回经纪人积分
   * @param int $signatory_id 经纪人编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_credit_by_signatory_id($signatory_id)
  {
    //查询字段
    $this->db_city->select('credit');
    //查询条件
    $this->db_city->where('signatory_id', $signatory_id);
    $signatory = $this->db_city->get($this->_tbl)->row_array();
    return $signatory['credit'];
  }

  /**
   * 返回经纪人等级分值
   * @param int $signatory_id 经纪人编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_level_by_signatory_id($signatory_id)
  {
    //查询字段
    $this->db_city->select('level');
    //查询条件
    $this->db_city->where('signatory_id', $signatory_id);
    $signatory = $this->db_city->get($this->_tbl)->row_array();
    return $signatory['level'];
  }

  /**
   * 返回经纪人合作成功率
   * @param int $signatory_id 经纪人编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_cop_suc_ratio_by_signatory_id($signatory_id)
  {
    //查询字段
    $this->db_city->select('cop_suc_ratio');
    //查询条件
    $this->db_city->where('signatory_id', $signatory_id);
    $signatory = $this->db_city->get($this->_tbl)->row_array();
    return $signatory['cop_suc_ratio'];
  }

  /**
   * 更新经纪人积分值数据
   * @param array $update_data 更新的数据源数组
   * @param array $signatory_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_self_credit_by_signatory_id($signatory_id, $credit)
  {
    $this->db_city->set('credit', "credit + " . $credit, false);
    $this->db_city->where('signatory_id', $signatory_id);
    $this->db_city->update($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 更新经纪人等级分值数据
   * @param array $update_data 更新的数据源数组
   * @param array $signatory_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_self_level_by_signatory_id($signatory_id, $level)
  {
    $this->db_city->set('level', "level + " . $level, false);
    $this->db_city->where('signatory_id', $signatory_id);
    $this->db_city->update($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 更新经纪人 role_id
   * @param int $company_id 经纪人所在公司的编号
   * @param int $signatory_id 经纪人编号
   * @return int 成功后返回更新结果 true | false
   */
  public function update_agent_roleid($signatory_id, $company_id)
  {
    $sql = "select id from purview_company_group where company_id =" . $company_id . " and system_group_id = 1";
    $result = $this->db_city->query($sql)->result_array();
    $sql_up = "update `signatory_info` set `role_id` = " . (intval($result[0]['id'])) . " where `signatory_id` = " . (intval($signatory_id));
    $rel = $this->db_city->query($sql_up);
  }

  /**
   * 更新经纪人 查看保密信息次数 +1
   * @param int $id
   */
  public function update_read_secrecy_num($id)
  {
    $sql_up = "update `signatory_info` set `read_secrecy_num` =  `read_secrecy_num`+1 where `id` = '" . $id . "'";
    $rel = $this->db_city->query($sql_up);
    return $rel;
  }

  /**
   * 当前城市所有经纪人查看保密信息次数归零
   */
  public function update_read_secrecy_num_zero()
  {
    $sql_up = "update `signatory_info` set `read_secrecy_num` = 0 ";
    $rel = $this->db_city->query($sql_up);
    return $rel;
  }

  /**
   * 返回经纪人信用
   * @param int $signatory_id 经纪人编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_trust_by_signatory_id($signatory_id)
  {
    //查询字段
    $this->db_city->select('trust');
    //查询条件
    $this->db_city->where('signatory_id', $signatory_id);
    $signatory = $this->db_city->get($this->_tbl)->row_array();
    return is_full_array($signatory) ? $signatory['trust'] : 0;
  }

  /**
   * 更新经纪人积分值数据
   * @param array $update_data 更新的数据源数组
   * @param array $signatory_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_self_trust_by_signatory_id($signatory_id, $trust)
  {
    $this->db_city->set('trust', "trust + " . $trust, false);
    $this->db_city->where('signatory_id', $signatory_id);
    $this->db_city->update($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 根据经纪人编号更新细信息数据
   * @param array $update_data 更新的数据源数组
   * @param array $signatory_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_signatory_id($update_data, $signatory_id)
  {
    $this->db_city->where_in('signatory_id', $signatory_id);
    $this->db_city->update($this->_tbl, $update_data);
    $this->load->model('api_signatory_base_model');
    $this->api_signatory_base_model->delete_memcache_signatory_id($signatory_id);
    return $this->db_city->affected_rows();
  }

  /**
   * 根据编号更新经纪人的详细信息数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 根据编号更新经纪人的详细信息数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 门店编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_department_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('department_id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 插入经纪人数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 删除经纪人
   * @param array $signatory_id 经济人编号
   * @return int 成功后返回受影响的行数
   */
  public function delete_by_signatory_id($signatory_id)
  {
    if (is_array($signatory_id)) {
      $signatory_ids = $signatory_id;
    } else {
      $signatory_ids[0] = $signatory_id;
    }
    $this->db_city->where_in('signatory_id', $signatory_ids);
    return $this->db_city->delete($this->_tbl);
  }

  /**
   * 通过编号获取记录
   * @param int $id 公司编号
   * @return array 经纪人
   */
  public function get_department_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('department_id', $id);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  //更新官网注册的经纪人状态
  public function update_register_signatory_status($id, $status = 2)
  {
    $this->db_city->where('id', $id);
    $this->db_city->set('status', $status, false);
    $this->db_city->update('register_signatory');
    return $this->db_city->affected_rows();
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_signatory_group_publish_log($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from('stat_group_publish');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  /**
   * 获取经纪人群发总数日志
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条登录日志组成的二维数组
   */
  public function get_signatory_group_publish_log($where, $start = 0, $limit = 20,
                                                  $order_key = 'ymd', $order_by = 'DESC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('stat_group_publish');
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }


  public function get_all_group_log($start_time, $end_time)
  {
    $where = "gpl.id > 0";
    //设置时间条件
    if ($start_time && $end_time == null) {
      $where .= " and gpl.ymd >= '" . $start_time . "'";
    } else if ($start_time == null && $end_time) {
      $where .= " and gpl.ymd <= '" . $end_time . "'";
    } else if ($start_time && $end_time) {
      $where .= " and gpl.ymd >= '" . $start_time . "' and gpl.ymd <= '" . $end_time . "'";
    }

    $sql = 'SELECT gpl.ymd, gpl.sell_type, bi.truename, a.`name` as agname, ac.`name` as acname FROM `group_publish_log` AS gpl LEFT JOIN `signatory_info` as bi ON gpl.signatory_id = bi.signatory_id LEFT JOIN `department` as a ON bi.department_id = a.id LEFT JOIN (select id,`name` FROM department WHERE company_id = 0) as ac ON bi.company_id = ac.id where ' . $where;

    $query = $this->dbback_city->query($sql);
    return $query->result_array();
  }

  /**
   * 获取权限组列表
   */
  public function get_purview_group()
  {
    $sql = "select id,name,level from purview_system_group ";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;

  }

  /**
   * 根据身份权限role_id获取职务等级与本身对应signatory_id的组合数组
   */
  public function get_system_group_id_by($role_id, $signatory_id)
  {
    $sql = "select p.id,p.system_group_id,b.signatory_id from purview_company_group p left join signatory_info b on p.id =b.role_id where p.id = " . $role_id . " AND b.signatory_id = " . $signatory_id;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;

  }

  /**
   * 根据经纪人门店编号，更新客户经理编号
   * @param type $department_id
   */
  public function update_master_by_department_id($department_id, $master_id)
  {
    $this->db_city->set('master_id', $master_id);
    $this->db_city->where('department_id', $department_id);
    return $this->db_city->update($this->_tbl);
  }


  /**
   * 根据客户经理编号，查找信息
   * @param type $department_id
   */
  public function get_master_info($master_id)
  {
    $this->dbback->where('uid', $master_id);
    $this->dbback->from('admin_user');
    return $this->dbback->get()->row_array();
  }
}

/* End of file signatory_info_base_model.php */
/* Location: ./application/models/signatory_info_base_model.php */
