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
 * purview_department_role_model CLASS
 *
 * 门店权限角色添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Purview_department_group_base_model");

class Purview_department_group_model extends Purview_department_group_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

}

/* End of file purview_department_role_model.php */
/* Location: ./app/models/purview_department_role_model.php */