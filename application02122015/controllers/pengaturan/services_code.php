<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class services_code extends ApplicationBase {

    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model("m_preferences");
        // load library
        $this->load->library("tnotification");
        $this->load->library("pagination");
    }

    // SVC
    public function index() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/services_code/svc.html");
        // get data
        $rs_id = $this->m_preferences->get_list_services();
        $this->smarty->assign('rs_id', $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // SVC variables add
    public function svc_add() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/services_code/svc_add.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add svc process
    public function svc_add_process() {
        // cek input
        $this->tnotification->set_rules('services_cd', 'Services Code', 'trim|required|max_length[1]');
        $this->tnotification->set_rules('services_nm', 'Services Name', 'trim|required|max_length[50]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('services_cd'), $this->input->post('services_nm'), $this->com_user['user_id']);
            // insert
            if ($this->m_preferences->insert_svc($params)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/services_code/svc_add/");
    }

    // svc variables edit
    public function svc_edit($pref_id = "") {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/services_code/svc_edit.html");
        // get detail data
        $result = $this->m_preferences->get_detail_services($pref_id);
        $result['services_cd_old'] = $result['services_cd'];
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit svc process
    public function svc_edit_process() {
        // cek input
        $this->tnotification->set_rules('services_cd_old', 'ID', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Services Code', 'trim|required|max_length[1]');
        $this->tnotification->set_rules('services_nm', 'Services Name', 'trim|required|max_length[50]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('services_cd'), $this->input->post('services_nm'),
                $this->input->post('services_cd_old'));
            // insert
            if ($this->m_preferences->update_svc($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("pengaturan/services_code/svc_edit/" . $this->input->post('services_cd'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/services_code/svc_edit/" . $this->input->post('services_cd_old'));
    }

    // hapus svc process
    public function svc_hapus_process($pref_id = "") {
        $params = array($pref_id);
        // delete pic
        if ($this->m_preferences->delete_svc($params)) {
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/services_code");
    }

}
