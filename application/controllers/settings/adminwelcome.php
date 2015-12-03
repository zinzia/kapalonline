<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

class adminwelcome extends ApplicationBase {

    // constructor
    public function __construct() {
        parent::__construct();
        // load model
        // load library
        // load helpers
    }

    // welcome administrator
    public function index() {
        // set page rules (all admin)
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "settings/welcome/welcome.html");

        // output
        parent::display();
    }

}