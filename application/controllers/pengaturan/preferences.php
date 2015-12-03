<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class preferences extends ApplicationBase {

    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model("m_preferences");
        // load library
        $this->load->library("tnotification");
        $this->load->library("pagination");
    }

    /*
     * TEMBUSAN
     */

    // list tembusan
    public function index() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/tembusan_list.html");
        // get data
        $rs_id = $this->m_preferences->get_list_redaksional(array('tembusan'));
        $this->smarty->assign('rs_id', $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // tembusan variables add
    public function tembusan_add() {
        // set page rule
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/tembusan_add.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add tembusan process
    public function tembusan_add_process() {
        // set page rule
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('redaksional_nm', 'Nama', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('redaksional_mail', 'Email', 'trim|required|valid_email|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "redaksional_nm" => $this->input->post('redaksional_nm'),
                "redaksional_mail" => $this->input->post('redaksional_mail'),
                "redaksional_group" => 'tembusan',
            );
            // insert
            if ($this->m_preferences->insert_redaksional($params)) {
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
        redirect("pengaturan/preferences/tembusan_add/");
    }

    // tembusan variables edit
    public function tembusan_edit($redaksional_id = "") {
        // set page rule
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/tembusan_edit.html");
        // get detail data
        $result = $this->m_preferences->get_redaksional_by_id($redaksional_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit tembusan process
    public function tembusan_edit_process() {
        // set page rule
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('redaksional_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('redaksional_nm', 'Nama', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('redaksional_mail', 'Email', 'trim|required|valid_email|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "redaksional_nm" => $this->input->post('redaksional_nm'),
                "redaksional_mail" => $this->input->post('redaksional_mail'),
            );
            $where = array(
                "redaksional_id" => $this->input->post('redaksional_id'),
            );
            // insert
            if ($this->m_preferences->update_redaksional($params, $where)) {
                // notifikasi
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("pengaturan/preferences/tembusan_edit/" . $this->input->post('redaksional_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/preferences/tembusan_edit/" . $this->input->post('redaksional_id'));
    }

    // hapus tembusan process
    public function tembusan_hapus_process($redaksional_id = "") {
        // set page rule
        $this->_set_page_rule("D");
        // params
        $params = array(
            "redaksional_id" => $redaksional_id,
        );
        // delete redaksional
        if ($this->m_preferences->delete_redaksional($params)) {
            // notifikasi
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/preferences");
    }

    /*
     * KEPADA
     */

    // list kepada
    public function kepada() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/kepada_list.html");
        // get data
        $rs_id = $this->m_preferences->get_list_redaksional(array('kepada'));
        $this->smarty->assign('rs_id', $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // kepada variables add
    public function kepada_add() {
        // set page rule
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/kepada_add.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add kepada process
    public function kepada_add_process() {
        // set page rule
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('redaksional_nm', 'Nama', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('redaksional_mail', 'Email', 'trim|required|valid_email|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "redaksional_nm" => $this->input->post('redaksional_nm'),
                "redaksional_mail" => $this->input->post('redaksional_mail'),
                "redaksional_group" => 'kepada',
            );
            // insert
            if ($this->m_preferences->insert_redaksional($params)) {
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
        redirect("pengaturan/preferences/kepada_add/");
    }

    // kepada variables edit
    public function kepada_edit($redaksional_id = "") {
        // set page rule
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/kepada_edit.html");
        // get detail data
        $result = $this->m_preferences->get_redaksional_by_id($redaksional_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit kepada process
    public function kepada_edit_process() {
        // set page rule
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('redaksional_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('redaksional_nm', 'Nama', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('redaksional_mail', 'Email', 'trim|required|valid_email|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "redaksional_nm" => $this->input->post('redaksional_nm'),
                "redaksional_mail" => $this->input->post('redaksional_mail'),
            );
            $where = array(
                "redaksional_id" => $this->input->post('redaksional_id'),
            );
            // insert
            if ($this->m_preferences->update_redaksional($params, $where)) {
                // notifikasi
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("pengaturan/preferences/kepada_edit/" . $this->input->post('redaksional_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/preferences/kepada_edit/" . $this->input->post('redaksional_id'));
    }

    // hapus kepada process
    public function kepada_hapus_process($redaksional_id = "") {
        // set page rule
        $this->_set_page_rule("D");
        // params
        $params = array(
            "redaksional_id" => $redaksional_id,
        );
        // delete redaksional
        if ($this->m_preferences->delete_redaksional($params)) {
            // notifikasi
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/preferences");
    }

    /*
     *  EMAIL SETTINGS
     */

    // view email
    public function mail() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/email.html");
        // get detail data
        $result = $this->m_preferences->get_preferences_by_id(13);
        $email = explode(',', $result['pref_value']);
        $result['address'] = $result['pref_nm'];
        $result['port'] = $email[0];
        $result['user_name'] = $email[1];
        $result['user_pass'] = $email[2];
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit kepada process
    public function mail_edit_process() {
        // set page rule
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('address', 'Alamat', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('port', 'Port', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_name', 'User', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[50]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $pref_value = implode(',', array($this->input->post('port'), $this->input->post('user_name'), $this->input->post('user_pass')));
            $params = array('mail', $this->input->post('address'), $pref_value, $this->com_user['user_id'], 13);
            // insert
            if ($this->m_preferences->update($params)) {
                // notifikasi
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("pengaturan/preferences/mail/");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/preferences/mail/");
    }

    /*
     *  INVOCIES SETTINGS
     */

    // view invoices
    public function invoices() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/invoices.html");

        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    /*
     *  TARIF SETTINGS
     */

    // view tarif
    public function tarif() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/tarif.html");

        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    /*
     *  PERATURAN SETTINGS
     */

    // view peraturan
    public function peraturan() {
        // set page rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/preferences/peraturan.html");

        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

}
