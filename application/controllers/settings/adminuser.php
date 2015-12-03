<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

// --

class adminuser extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_settings');
        // load library
        $this->load->library('encrypt');
        $this->load->library('tnotification');
        $this->load->library('pagination');
        // global assign
        $this->smarty->assign("encrypt", $this->encrypt);
    }

    // list data
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "settings/user/list.html");
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("settings/adminuser/index/");
        $config['total_rows'] = $this->m_settings->get_total_users();
        $config['uri_segment'] = 4;
        $config['per_page'] = 50;
        $this->pagination->initialize($config);
        $pagination['data'] = $this->pagination->create_links();
        // pagination attribute
        $start = $this->uri->segment(4, 0) + 1;
        $end = $this->uri->segment(4, 0) + $config['per_page'];
        $end = (($end > $config['total_rows']) ? $config['total_rows'] : $end);
        $pagination['start'] = ($config['total_rows'] == 0) ? 0 : $start;
        $pagination['end'] = $end;
        $pagination['total'] = $config['total_rows'];
        // pagination assign value
        $this->smarty->assign("pagination", $pagination);
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get data
        $params = array(($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_settings->get_all_users($params));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add form
    public function add() {
        // set template content
        $this->smarty->assign("template_content", "settings/user/add.html");
        // role
        $this->smarty->assign("rs_role", $this->m_settings->get_all_roles());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('full_name', 'Nama Lengkap', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('jabatan', 'Alamat', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('telp', 'Nomor Telepon', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_mail', 'User Email', 'trim|required|valid_email|max_length[50]');
        $this->tnotification->set_rules('user_name', 'User Name', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('lock_st', 'Lock Status', 'trim|required|max_length[1]');
        $this->tnotification->set_rules('role_id', 'Hak Akses', 'required');
        // check email
        $email = trim($this->input->post('user_mail'));
        if ($this->m_settings->is_exist_email($email)) {
            $this->tnotification->set_error_message('Email is not available');
        }
        // check username
        $username = trim($this->input->post('user_name'));
        if ($this->m_settings->is_exist_username($username)) {
            $this->tnotification->set_error_message('Username is not available');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $password_key = crc32($this->input->post('user_pass'));
            $password = $this->encrypt->encode($this->input->post('user_pass'), $password_key);
            // parameter
            $params = array($this->input->post('user_name'), $password, $password_key, $this->input->post('lock_st'), $this->input->post('user_mail'), $this->com_user['user_id']);
            // insert
            if ($this->m_settings->insert_user($params)) {
                // get last id
                $user_id = $this->m_settings->get_last_inserted_id();
                // update to users
                $params = array($this->input->post('full_name'), $this->input->post('jabatan'), $this->input->post('telp'), $this->com_user['user_id'], $user_id);
                $this->m_settings->update_user_detail($params);
                // insert hak akses
                $params = array($this->input->post('role_id'), $user_id);
                $this->m_settings->insert_role_user($params);
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
        redirect("settings/adminuser/add");
    }

    // edit form
    public function edit($user_id = "") {
        // set template content
        $this->smarty->assign("template_content", "settings/user/edit.html");
        // role
        $this->smarty->assign("rs_role", $this->m_settings->get_all_roles());
        // get data
        $result = $this->m_settings->get_detail_user_by_id($user_id);
        if (!empty($result)) {
            $result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            $this->smarty->assign("result", $result);
        }
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // cek input
        $this->tnotification->set_rules('user_id', 'User ID', 'trim|required');
        $this->tnotification->set_rules('full_name', 'Nama Lengkap', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('telp', 'Nomor Telepon', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_mail', 'User Email', 'trim|required|valid_email|max_length[50]');
        $this->tnotification->set_rules('user_name', 'User Name', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('lock_st', 'Lock Status', 'trim|required|max_length[1]');
        $this->tnotification->set_rules('role_id', 'Hak Akses', 'required');
        // check email
        $email = trim($this->input->post('user_mail'));
        if ($this->input->post('user_mail') != $this->input->post('user_mail_old')) {
            if ($this->m_settings->is_exist_email($email)) {
                $this->tnotification->set_error_message('Email is not available');
            }
        }
        // check username
        $username = trim($this->input->post('user_name'));
        if ($this->input->post('user_name') != $this->input->post('user_name_old')) {
            if ($this->m_settings->is_exist_username($username)) {
                $this->tnotification->set_error_message('Username is not available');
            }
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $password_key = crc32($this->input->post('user_pass'));
            $password = $this->encrypt->encode($this->input->post('user_pass'), $password_key);
            // parameter
            $params = array($this->input->post('user_name'), $password, $password_key, $this->input->post('lock_st'),
                $this->input->post('user_mail'), $this->com_user['user_id'], $this->input->post('user_id'));
            // update
            if ($this->m_settings->update_user($params)) {
                // update to users
                $params = array($this->input->post('full_name'), $this->input->post('jabatan'), $this->input->post('telp'), $this->com_user['user_id'], $this->input->post('user_id'));
                $this->m_settings->update_user_detail($params);
                // update role
                $params = array($this->input->post('role_id'), $this->input->post('user_id'));
                $this->m_settings->update_role_user($params);
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
        redirect("settings/adminuser/edit/" . $this->input->post('user_id'));
    }

    // hapus form
    public function hapus($user_id = "") {
        // set template content
        $this->smarty->assign("template_content", "settings/user/hapus.html");
        // get data
        $result = $this->m_settings->get_detail_user_by_id($user_id);
        if (!empty($result)) {
            $result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            $this->smarty->assign("result", $result);
        }
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    // hapus process
    public function delete_process() {
        // cek input
        $this->tnotification->set_rules('user_id', 'User ID', 'trim|required|max_length[10]');
        // cek id
        if ($this->input->post('user_id') == $this->com_user['user_id']) {
            $this->tnotification->set_error_message('Tidak bisa menghapus account sendiri, tanyakan pada administrator utama');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('user_id'));
            // insert
            if ($this->m_settings->delete_user($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("settings/adminuser");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("settings/adminuser/hapus/" . $this->input->post('user_id'));
    }

}
