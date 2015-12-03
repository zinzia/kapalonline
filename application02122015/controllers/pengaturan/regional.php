<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class regional extends ApplicationBase {

    public function __construct() {
        parent::__construct();
    }

    // list
    public function index() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/regional/index.html");
        // output
        parent::display();
    }

}