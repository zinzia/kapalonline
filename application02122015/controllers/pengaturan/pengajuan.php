<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class pengajuan extends ApplicationBase {

    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model("m_pengajuan");
        // load library
        $this->load->library("tnotification");
        $this->load->library("pagination");
    }

    /*
     * TEMBUSAN
     */

    // list pengajuan
    public function index() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/pengajuan/index.html");
        // get data
        $rs_id = $this->m_pengajuan->get_list_pengajuan();
        $this->smarty->assign('rs_id', $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // pengajuan variables add
    public function pengajuan_add() {
        // set page rule
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/pengajuan/pengajuan_add.html");
        // get data
        $rs_id = $this->m_pengajuan->get_list_services();
        $this->smarty->assign('rs_id', $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add pengajuan process
    public function pengajuan_add_process() {
        // set page rule
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('data_type', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Remark', 'trim|required');
        $this->tnotification->set_rules('batasan', 'Batasan', 'trim|required|max_length[3]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "data_type" => $this->input->post('data_type'),
                "data_flight" => $this->input->post('data_flight'),
                "services_cd" => $this->input->post('services_cd'),
                "batasan" => $this->input->post('batasan'),
            );
            // insert
            if ($this->m_pengajuan->insert($params)) {
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
        redirect("pengaturan/pengajuan/pengajuan_add/");
    }

    // pengajuan variables edit
    public function pengajuan_edit($field_id = "") {
        // set page rule
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/pengajuan/pengajuan_edit.html");
        // get detail data
        $result = $this->m_pengajuan->get_pengajuan_by_id($field_id);
        $this->smarty->assign("result", $result);
        // get data
        $rs_id = $this->m_pengajuan->get_list_services();
        $this->smarty->assign('rs_id', $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit pengajuan process
    public function pengajuan_edit_process() {
        // set page rule
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('field_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_type', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Remark', 'trim|required');
        $this->tnotification->set_rules('batasan', 'Batasan', 'trim|required|max_length[3]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "data_type" => $this->input->post('data_type'),
                "data_flight" => $this->input->post('data_flight'),
                "services_cd" => $this->input->post('services_cd'),
                "batasan" => $this->input->post('batasan'),
            );
            $where = array(
                "field_id" => $this->input->post('field_id'),
            );
            // insert
            if ($this->m_pengajuan->update($params, $where)) {
                // notifikasi
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("pengaturan/pengajuan/pengajuan_edit/" . $this->input->post('field_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/pengajuan/pengajuan_edit/" . $this->input->post('field_id'));
    }

    // hapus pengajuan process
    public function pengajuan_hapus_process($field_id = "") {
        // set page rule
        $this->_set_page_rule("D");
        // params
        $params = array(
            "field_id" => $field_id,
        );
        // delete pengajuan
        if ($this->m_pengajuan->delete($params)) {
            // notifikasi
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/pengajuan");
    }

}
