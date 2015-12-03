<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

// --

class adminrole extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load
        $this->load->model('m_settings');
        $this->load->library('tnotification');
    }

    // list data
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "settings/role/list.html");
        // get data
        $this->smarty->assign("rs_id", $this->m_settings->get_all_roles());
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    // add form
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "settings/role/add.html");
        // get data portal
        $this->smarty->assign("rs_id", $this->m_settings->get_all_portal());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function process_add() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('portal_id', 'Web Portal', 'trim|required|max_length[5]');
        $this->tnotification->set_rules('role_nm', 'Role Name', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('role_desc', 'Role Description', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('default_page', 'Halaman Default', 'trim|required|max_length[50]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('portal_id'), $this->input->post('role_nm'),
                $this->input->post('role_desc'), $this->input->post('default_page'));
            // insert
            if ($this->m_settings->insert_role($params)) {
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
        redirect("settings/adminrole/add");
    }

    // edit form
    public function edit($id_role = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "settings/role/edit.html");
        // get data
        $result = $this->m_settings->get_detail_role_by_id($id_role);
        $this->smarty->assign("result", $result);
        // get data portal
        $this->smarty->assign("rs_id", $this->m_settings->get_all_portal());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function process_update() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('role_id', 'Role ID', 'trim|required');
        $this->tnotification->set_rules('portal_id', 'Web Portal', 'trim|required');
        $this->tnotification->set_rules('role_nm', 'Role Name', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('role_desc', 'Role Description', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('default_page', 'Halaman Default', 'trim|required|max_length[50]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('portal_id'), $this->input->post('role_nm'),
                $this->input->post('role_desc'), $this->input->post('default_page'),
                $this->input->post('role_id'));
            // insert
            if ($this->m_settings->update_role($params)) {
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
        redirect("settings/adminrole/edit/" . $this->input->post('role_id'));
    }

    // hapus form
    public function hapus($id_role = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "settings/role/hapus.html");
        // get data
        $result = $this->m_settings->get_detail_role_by_id($id_role);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // hapus process
    public function process_delete() {
        // set page rules
        $this->_set_page_rule("D");
        // cek input
        $this->tnotification->set_rules('role_id', 'Role ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('role_id'));
            // insert
            if ($this->m_settings->delete_role($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("settings/adminrole");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("settings/adminrole/hapus/" . $this->input->post('role_id'));
    }

}