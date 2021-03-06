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
 * Credit_record_base_model CLASS
 *
 * 积分操作记录管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Credit_record_base_model extends MY_Model
{

  /**
   * 积分方法表
   * @var string
   */
  private $_tbl = 'credit_record';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
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
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->db_city->where($where);
    }
    return $this->db_city->count_all_results($this->_tbl);
  }

  /**
   * 统计某个类型的分值
   * @param string $where 查询条件
   * @return int
   */
  public function sum_score_by($where)
  {
    $this->db_city->select("sum(score) as score");
    $this->db_city->where($where);
    $sum_score = $this->db_city->get($this->_tbl)->row_array();
    return $sum_score['score'] == true ? $sum_score['score'] : 0;
  }

  /**
   * 根据经纪人编号和获取行为获取最新一条记录
   * @param int $broker_id
   * @param type $type
   * @return type
   */
  public function get_by_broker_id_type($broker_id, $type)
  {
    $this->db_city->where('broker_id', $broker_id);
    $this->db_city->where('type', $type);
    $this->db_city->order_by('id', 'DESC');
    $this->db_city->limit(1);
    return $this->db_city->get($this->_tbl)->row_array();
  }

  /**
   * 获取积分方法列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
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
   * 插入数据
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
   * 更新数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号数组
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $broker_id)
  {
    /*if (is_array($id))
    {
        $ids = $id;
    }
    else
    {
        $ids[0] = $id;
    }*/
    $this->db_city->where('broker_id', $broker_id);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  //清空数据库
  public function truncate()
  {
    $this->db_city->from($this->_tbl);
    $this->db_city->truncate();
  }

}

/* End of file Credit_record_base_model.php */
/* Location: ./applications/models/Credit_record_base_model.php */
