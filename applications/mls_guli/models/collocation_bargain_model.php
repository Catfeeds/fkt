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
 * bargain_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Collocation_bargain_base_model");

class Collocation_bargain_model extends Collocation_bargain_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'collocation_bargain';
    $this->_tbl2 = 'signatory_info';
    $this->_tbl3 = 'department';
  }

  /**
   * 详情
   */
  public function get_info_by_id($id)
  {
    //查询字段
    $this->dbback_city->select("a.*,b.truename AS signatory_name_a,c.name AS department_name_a,d.truename AS signatory_name_b,e.name AS department_name_b");

    //查询条件
    $this->dbback_city->where("a.id = {$id}");

    $this->dbback_city->from($this->_tbl1 . " AS a");
    $this->dbback_city->join($this->_tbl2 . " AS b", "a.signatory_id = b.signatory_id", "LEFT");
    $this->dbback_city->join($this->_tbl3 . " AS c", "a.department_id = c.id", "LEFT");
    //$this->dbback_city->join($this->_tbl2." AS d", "a.signatory_id = d.signatory_id","LEFT");
    //$this->dbback_city->join($this->_tbl3." AS e", "a.department_id = e.id","LEFT");

    return $this->dbback_city->get()->row_array();
  }

  //列表数据
  public function get_list_by($where, $start = 0, $limit = 20,
                              $order_key = 'signing_time', $order_by = 'DESC')
  {
    //查询字段
    //$this->dbback_city->select("{$this->_tbl1}.*,{$this->_tbl2}.truename AS signatory_name,{$this->_tbl3}.name AS department_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    //$this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.signatory_id = {$this->_tbl2}.signatory_id");
    //$this->dbback_city->join($this->_tbl3, "{$this->_tbl1}.department_id = {$this->_tbl3}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1 . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  //更改成交审核状态
  public function change_status($id, $status)
  {
    $this->db_city->where('id', $id);
    $this->db_city->set('status', $status, false);
    $this->db_city->update($this->_tbl1);
    return $this->db_city->affected_rows();
  }

  //更改房源状态
  public function change_house_status($type, $h_id)
  {
    if ($type == 1) {
      $this->db_city->where('id', $h_id);
      $this->db_city->set('status', '4', false);
      $this->db_city->update('sell_house');
    } else {
      $this->db_city->where('id', $h_id);
      $this->db_city->set('status', '4', false);
      $this->db_city->update('rent_house');
    }
    return $this->db_city->affected_rows();
  }

  //总计数据
  public function get_total($where)
  {
    //查询字段
    $this->dbback_city->select("SUM(commission_total) AS commission_total_total");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    //返回结果
    return $this->dbback_city->get()->row_array();
  }
}

/* End of file bargain_model.php */
/* Location: ./app/models/bargain_model.php */
