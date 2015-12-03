<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class published extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_published');
        // load library
        $this->load->library('tnotification');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index($document_no = '') {
        // set template content
        $this->smarty->assign("template_content", "information/published/form.html");
        // get detail fa by document no
        $result = $this->m_published->get_detail_fa_by_document_no(array($document_no));
        $this->smarty->assign("result", $result);
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

}
