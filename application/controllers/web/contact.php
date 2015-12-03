<?php

if (!defined("BASEPATH"))
    exit("No direct script access allowed");
// load base class if needed
require_once( APPPATH . "controllers/base/OperatorBase.php" );

class contact extends ApplicationBase {

    //constructor
    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model('m_contact');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list projects
    public function index() {
        //set page rules
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "web/contact/list.html");
        // get list
        $rs_id = $this->m_contact->get_list_contact();
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // delete process
    public function delete_process($data_id = "") {
        $params = array($data_id);
        // delete
        if ($this->m_contact->delete($params)) {
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("web/contact/");
    }

}
