<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

// --

class adminportal extends ApplicationBase {

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
        $this->smarty->assign("template_content", "settings/portal/list.html");
        // get data
        $this->smarty->assign("rs_id", $this->m_settings->get_all_portal());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add form
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "settings/portal/add.html");

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
        $this->tnotification->set_rules('portal_nm', 'Nama Portal', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('site_title', 'Judul Web', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('site_desc', 'Deskripsi Web', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('meta_desc', 'Meta Deskripsi', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('meta_keyword', 'Meta Keyword', 'trim|required|max_length[100]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('portal_nm'), $this->input->post('site_title'), $this->input->post('site_desc'),
                $this->input->post('meta_desc'), $this->input->post('meta_keyword'), $this->com_user['user_id']);
            // insert
            if ($this->m_settings->insert_portal($params)) {
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
        redirect("settings/adminportal/add");
    }

    // edit form
    public function edit($portal_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "settings/portal/edit.html");
        // get data
        $result = $this->m_settings->get_portal_by_id($portal_id);
        $this->smarty->assign("result", $result);
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
        $this->tnotification->set_rules('portal_id', 'Group ID', 'trim|required|max_length[20]');
        $this->tnotification->set_rules('portal_nm', 'Nama Portal', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('site_title', 'Judul Web', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('site_desc', 'Deskripsi Web', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('meta_desc', 'Meta Deskripsi', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('meta_keyword', 'Meta Keyword', 'trim|required|max_length[100]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('portal_nm'), $this->input->post('site_title'), $this->input->post('site_desc'),
                $this->input->post('meta_desc'), $this->input->post('meta_keyword'), $this->com_user['user_id'], $this->input->post('portal_id'));
            // update
            if ($this->m_settings->update_portal($params)) {
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
        redirect("settings/adminportal/edit/" . $this->input->post('portal_id'));
    }

    // hapus form
    public function hapus($portal_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "settings/portal/hapus.html");
        // get data
        $result = $this->m_settings->get_portal_by_id($portal_id);
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
        $this->tnotification->set_rules('portal_id', 'Portal ID', 'trim|required|max_length[20]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('portal_id'));
            // insert
            if ($this->m_settings->delete_portal($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("settings/adminportal");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("settings/adminportal/hapus/" . $this->input->post('portal_id'));
    }

}

