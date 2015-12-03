<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/StakeholderBase.php' );

// --

class stakeholderforbidden extends ApplicationBase {

    // constructor
    public function __construct() {
        parent::__construct();
    }

    // forbidden page
    public function page($nav_id = "") {
        // set template content
        $this->smarty->assign("template_content", "login/stakeholder/forbidden.html");
        // get navigation info
        $result = $this->m_site->get_menu_by_id($nav_id);
        if (!empty($result)) {
            $this->smarty->assign("nav", $result);
        } else {
            $result['nav_url'] = 'login/stakeholderlogin';
            $this->smarty->assign("nav", $result);
        }
        // output
        parent::display();
    }

}
