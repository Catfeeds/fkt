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
 * Key_log_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Key_log_base_model");

class Key_log_model extends Key_log_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'key_log';
    $this->_tbl2 = 'signatory_info';
    $this->_tbl3 = 'department';
  }

  //列表数据
  public function get_list_by($where, $start = 0, $limit = 20,
                              $order_key = 'time', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl1}.*,{$this->_tbl2}.truename,{$this->_tbl3}.name AS department_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    $this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.signatory_id = {$this->_tbl2}.signatory_id");
    $this->dbback_city->join($this->_tbl3, "{$this->_tbl2}.department_id = {$this->_tbl3}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1 . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }
}

/* End of file Key_log_model.php */
/* Location: ./app/models/Key_log_model.php */
