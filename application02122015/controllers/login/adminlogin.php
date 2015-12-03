<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminLoginBase.php' );

// --

class adminlogin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load global
        $this->load->library('tnotification');
    }

    // view
    public function index($status = "") {
		
                $this->tsession->set_userdata('session_admin', 1);
        // set template content
        $this->smarty->assign("template_content", "login/admin/form.html");
        // bisnis proses
        if (!empty($this->com_user)) {
            // still login
            redirect('settings/adminwelcome');
        } else {
            $this->smarty->assign("login_st", $status);
        }
        // output
        parent::display();
    }

    // login process
    public function login_process() {
        // set rules
        $this->tnotification->set_rules('username', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('pass', 'Password', 'trim|required|max_length[30]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('pass'));
            // get user detail
            $result = $this->m_account->get_user_login($username, $password, 1, $this->portal_id);
            // check
            if (!empty($result)) {
                // cek lock status
                if ($result['lock_st'] == '1') {
                    // output
                    redirect('login/adminlogin/index/locked');
                }
                // set session
                $this->tsession->set_userdata('session_admin', $result['user_id']);
                // insert login time
                $this->m_account->save_user_login($result['user_id'], $_SERVER['REMOTE_ADDR']);
                // redirect
                redirect($result['default_page']);
            } else {
                // output
                redirect('login/adminlogin/index/error');
            }
        } else {
            echo "here";
            // default error
            redirect('login/adminlogin/index/error');
        }
        // output
        redirect('login/adminlogin');
    }

    // logout process
    public function logout_process() {
        // user id
        $user_id = $this->tsession->userdata('session_admin');
        // insert logout time
        $this->m_account->update_user_logout($user_id);
        // unset session
        $this->tsession->unset_userdata('session_admin');
        // output
        redirect('login/adminlogin');
    }

}