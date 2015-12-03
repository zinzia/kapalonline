<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class organization extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // exit
        redirect('home/welcome');
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/organization/index.html");
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'org_%'));
        // output
        parent::display();
    }

}
