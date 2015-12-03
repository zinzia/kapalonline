<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

class adminsettings extends ApplicationBase {

    // constructor
    public function __construct() {
        parent::__construct();
        // load model

        // load library

        // load helpers

    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "settings/default/index.html");

        // output
        parent::display();
    }
}