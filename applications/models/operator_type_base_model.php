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
 * operator_type_base_model CLASS
 *
 * 操作日志类型添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Operator_type_base_model extends MY_Model
{

  /**
   * 操作日志类型表
   * @var string
   */
  private $_tbl = 'operator_type';

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
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 通过功能查找所属的子菜单
   * @param int $func_id 功能编号
   */
  public function get_by_module_id($module_id)
  {
    if (is_array($module_id)) {
      $ids = $module_id;
    } else {
      $ids[0] = $module_id;
    }
    $this->dbback_city->where_in('module_id', $ids);
    $this->dbback_city->order_by('order', 'desc');
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 获取操作日志类型列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'ASC')
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
   * 根据查询条件返回一条表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id=1');
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  public function get_by_url($url)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('url', $url);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过操作日志类型编号获取记录
   * @param int $id 编号
   * @return array 记录组成的一维数组
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
   * 插入操作日志类型数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的权限组id 失败 false
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
   * 更新操作日志类型数据
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
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
   * 删除操作日志类型数据
   * @param int $id 编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_id($id)
  {
    //多条删除
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    if ($ids) {
      $this->db_city->where_in('id', $ids);
      $this->db_city->delete($this->_tbl);
    }
    if ($this->db_city->affected_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }

  //清空数据库
  public function truncate()
  {
    $this->db_city->from($this->_tbl);
    $this->db_city->truncate();
  }
}

/* End of file operator_type_base_model.php */
/* Location: ./applications/models/operator_type_base_model.php */
