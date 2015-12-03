<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class pending_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_pending_izin');
        // load library
        $this->load->library('tnotification');
        //load helper
        $this->load->library('doslibrary');
        $this->smarty->assign("dos",  $this->doslibrary);
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/pending_izin/index.html");
        // list waiting
        $rs_id = $this->m_pending_izin->get_list_pending_task_waiting(array($this->com_user['role_id'], $this->com_user['airlines_id']));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

}
