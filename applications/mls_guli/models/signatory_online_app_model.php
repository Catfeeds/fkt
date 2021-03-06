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
 * signatory_online_app_model CLASS
 *
 * 登录在线通信业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models signatory_online_pc_state
 * @author          fisher
 */
load_m("signatory_online_app_base_model");

class signatory_online_app_model extends signatory_online_app_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }
}

/* End of file signatory_online_app.php */
/* Location: ./app/models/signatory_online_app.php */
