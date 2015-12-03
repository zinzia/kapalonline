<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class akta_pendaftaran extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_daftar');
        $this->load->model('m_block');
        $this->load->model('m_disclaimer');
        // load library
        $this->load->library('tnotification');
        $this->load->library('session');
        $this->load->library('pagination');
    }

    // routes
    public function index() {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "member/akta_pendaftaran/index.html");
        // list group
        $rs_id = $this->m_daftar->get_list_group_by_id(1);
		$this->smarty->assign("rs_group", $rs_id);
        // list opened form
        $rs_id = $this->m_daftar->get_list_registration_open(array('domestik', $this->com_user['airlines_id']));
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
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array($data_id, $this->com_user['airlines_id']);
        // execute
        if ($this->m_daftar->delete_izin($params)) {
            // delete izin rute
            $this->m_daftar->delete_rute_by_registrasi($data_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("member/akta_pendaftaran");
    }

    // disclaimer
    public function disclaimer($group_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/akta_pendaftaran/disclaimer.html");
        // detail group
        $group = $this->m_daftar->get_detail_group_by_id(array($group_id));
        if (empty($group)) {
            redirect('member/akta_pendaftaran');
        }
        $this->smarty->assign("group", $group);
        // get disclaimer list
        $this->smarty->assign("rs_id", $this->m_disclaimer->get_list_disclaimer());
        //set captcha
        $this->load->helper("captcha");
        $vals = array(
            'img_path' => FCPATH . '/resource/doc/captcha/',
            'img_url' => base_url() . '/resource/doc' . '/captcha/',
            'img_width' => '150',
            'font_path' => FCPATH . '/resource/doc/font/COURIER.TTF',
            'font_size' => 60,
            'img_height' => 70,
            'expiration' => 7200
        );
        $captcha = create_captcha($vals);
        $data = array(
            'captcha_time' => $captcha['time'],
            'ip_address' => $_SERVER["REMOTE_ADDR"],
            'word' => $captcha['word']
        );
        $this->session->set_userdata($data);
        $this->smarty->assign("captcha", $captcha);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // disclaimer process
    public function disclaimer_process() {
        // set page rules
        $this->_set_page_rule("R");
        // cek input
        $this->tnotification->set_rules('group_id', 'GROUP', 'trim|required');
        $this->tnotification->set_rules('agree', 'Agreement', 'trim|required');
        $this->tnotification->set_rules('captcha', 'Captcha', 'trim|required');
        // group
        $group_id = $this->input->post('group_id');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $captcha = $this->input->post('captcha');
            $this->session->userdata('word');
            $expiration = time() - 7200;
            if ($this->session->userdata('word') == $captcha AND $this->session->userdata('ip_address') == $_SERVER["REMOTE_ADDR"] AND $this->session->userdata('captcha_time') > $expiration) {
                // send
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // redirect
                redirect("member/akta_pendaftaran/create/" . $group_id);
            } else {
                $this->tnotification->sent_notification("error", "Captcha tidak sesuai");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // redirect
        redirect("member/akta_pendaftaran/disclaimer/" . $group_id);
    }

    // create registration
    public function create($group_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // detail group
        $group = $this->m_daftar->get_detail_group_by_id(array($group_id));
        if (empty($group)) {
            redirect('member/akta_pendaftaran');
        }
        // create izin_id, airlines_id, izin_flight, izin_group, mdb, mdd
        $params = array(
            "airlines_id" => $this->com_user['airlines_id'],
            "izin_flight" => 'domestik',
            'izin_group' => $group_id,
            "mdb" => $this->com_user['user_id']
        );
        $id = $this->m_daftar->create_group_data($params);
        if (!empty($id)) {
            // notification
            $this->tnotification->sent_notification("success", "Data has been created!");
            redirect($group['group_link'] . '/index/' . $id);
        } else {
            // notification
            $this->tnotification->sent_notification("error", "An unexpected error has occurred");
            redirect('member/akta_pendaftaran');
        }
    }

}
