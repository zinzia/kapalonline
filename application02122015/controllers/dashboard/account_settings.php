<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class account_settings extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_operator');
        // load library
        $this->load->library('tnotification');
    }

    // data pribadi
    public function data_pribadi() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "dashboard/account_settings/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // load sub direktorat
        $this->smarty->assign("rs_sub", $this->m_operator->get_all_direktorat_angkutan_udara());
        // get data pribadi
        $result = $this->m_account->get_data_pribadi($this->com_user['user_id']);
        // images
        $filepath = 'resource/doc/images/users/' . $result['operator_photo'];
        if (!is_file($filepath)) {
            $filepath = 'resource/doc/images/users/default.png';
        }
        $operator_photo = base_url() . $filepath;
        // assign
        $this->smarty->assign("operator_photo", $operator_photo);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process update data pribadi
    public function process_data_pribadi() {
        // cek input
        $this->tnotification->set_rules('operator_name', 'Nama Lengkap', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('operator_gender', 'Jenis Kelamin', 'trim|required');
        $this->tnotification->set_rules('operator_birth_place', 'Tempat Lahir', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('operator_birth_day', 'Tanggal Lahir', 'trim|required|max_length[10]');
        $this->tnotification->set_rules('operator_address', 'Alamat', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('operator_phone', 'Nomor Telepon', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('user_mail', 'operator_email', 'trim|required|max_length[50]|valid_email');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('sub_direktorat', 'Sub Direktorat', 'trim|required|maxlength[100]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('operator_name'), $this->input->post('operator_gender'),
                $this->input->post('operator_birth_place'), $this->input->post('operator_birth_day'),
                $this->input->post('operator_address'), $this->input->post('operator_phone'),
                $this->input->post('user_mail'), '',
                $this->input->post('jabatan'), $this->input->post('sub_direktorat'),
                $this->com_user['user_id']);
            // update
            if ($this->m_operator->update_data_pribadi($params)) {
                // upload foto
                if (!empty($_FILES['operator_photo']['tmp_name'])) {
                    // load
                    $this->load->library('tupload');
                    // delete
                    $filepath = 'resource/doc/images/users/' . $this->com_user['operator_photo'];
                    if (is_file($filepath)) {
                        unlink($filepath);
                    }
                    // upload config
                    $config['upload_path'] = 'resource/doc/images/users/';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['file_name'] = $this->com_user['user_id'];
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload_image('operator_photo', false, 160)) {
                        $data = $this->tupload->data();
                        $this->m_operator->update_foto_pribadi(array($data['file_name'], $this->com_user['user_id']));
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
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
        redirect("dashboard/account_settings/data_pribadi");
    }

    // user account
    public function user_account() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "dashboard/account_settings/account.html");
        // get data pribadi
        $result = $this->m_account->get_user_account($this->com_user['user_id']);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process update account
    public function process_update_account() {
        // cek input
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_name_old', 'Username lama', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_pass_old', 'Password lama', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_pass_new', 'Password baru', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_pass_confirm', 'Confirm Password', 'trim|required|max_length[30]');
        // check user name
        $username = $this->input->post('user_name');
        $username_old = $this->input->post('user_name_old');
        if ($username <> $username_old) {
            if ($this->m_account->is_exist_username($username)) {
                $this->tnotification->set_error_message("Username not available!");
            }
        }
        // check old password
        $password_old = $this->input->post('user_pass_old');
        if (!$this->m_account->is_exist_password($this->com_user['user_id'], $password_old)) {
            $this->tnotification->set_error_message("Wrong password!");
        } else {
            // check new password
            $password_new = $this->input->post('user_pass_new');
            $password_confirm = $this->input->post('user_pass_confirm');
            if ($password_new <> $password_confirm) {
                $this->tnotification->set_error_message("Password confirmation is not valid!");
            }
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('user_name'), md5($this->input->post('user_pass_new')), $this->com_user['user_id']);
            // update
            if ($this->m_account->update_data_account($params)) {
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
        redirect("dashboard/account_settings/user_account");
    }

    // change role
    public function change_role() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "dashboard/account_settings/role.html");
        // get data role
        $this->smarty->assign("rs_roles", $this->m_account->get_all_roles_by_users(array(6, $this->com_user['user_id'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process update role
    public function process_update_role() {
        // cek input
        $this->tnotification->set_rules('role_id', 'Role ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get user login
            $session = $this->tsession->userdata('session_fa_online');
            $this->tsession->set_userdata('session_fa_online', array('user_id' => $session['user_id'], 'role_id' => $this->input->post('role_id')));
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("dashboard/account_settings/change_role");
    }

}
