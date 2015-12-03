<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class internasional extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('regulator/m_registrasi');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list
    public function index() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pencabutan/internasional/registrasi.html");
        // list draft
        $rs_id = $this->m_registrasi->get_list_draft_registrasi(array('internasional'));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // delete process
    public function delete_process($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array($registrasi_id);
        // execute
        if ($this->m_registrasi->delete_registrasi($params)) {
            // delete izin rute
            $this->m_registrasi->delete_rute_by_registrasi($registrasi_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pencabutan/internasional");
    }

    // create registration
    public function create($group_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // detail group
        $group = $this->m_registrasi->get_detail_group_by_id(array($group_id));
        if (empty($group)) {
            redirect('pencabutan/internasional');
        }
        // create id
        $registrasi_id = $this->m_registrasi->get_data_id();
        // params
        $params = array(
            "registrasi_id" => $registrasi_id,
            "izin_flight" => 'internasional',
            "input_by" => 'operator',
            'izin_group' => $group_id,
            'payment_st' => NULL,
            "mdb" => $this->com_user['user_id'],
            "mdd" => date('Y-m-d H:i:s'),
        );
        if ($this->m_registrasi->insert_registrasi($params)) {
            // notification
            $this->tnotification->sent_notification("success", "Data has been created!");
            redirect($group['group_link'] . '/index/' . $registrasi_id);
        } else {
            // notification
            $this->tnotification->sent_notification("error", "An unexpected error has occurred");
            redirect('pencabutan/internasional');
        }
    }

}
