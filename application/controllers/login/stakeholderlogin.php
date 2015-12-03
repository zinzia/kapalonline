<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/StakeholderLoginBase.php' );
require_once (BASEPATH . 'plugins/jcryption/JCryption.php');
require_once (BASEPATH . 'plugins/jcryption/sqAES.php');

// --

class stakeholderlogin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load global
        $this->load->library('tnotification');
        $this->smarty->load_javascript("resource/js/jcryption/jquery.min.js");
        $this->smarty->load_javascript("resource/js/jcryption/jquery.jcryption.3.1.0.js");
    }

    // view
    public function index($status = "") {
        // set template content
        $this->smarty->assign("template_content", "login/stakeholder/form.html");
        // role
        $this->load->model('m_settings');
        $this->smarty->assign("rs_role", $this->m_settings->get_all_roles());
        // bisnis proses
        if (!empty($this->com_user)) {
            // still login
            redirect('stakeholder/welcome');
        } else {
            $this->smarty->assign("login_st", $status);
        }
        // output
        parent::display();
    }

    // login process
    public function login_process() {
		
                $this->tsession->set_userdata('session_pk_stakeholder', array('user_id' => 350, 'role_id' => 59));
        // set rules
        /*$this->tnotification->set_rules('username', 'Username', 'trim|required|max_length[30]');
          $this->tnotification->set_rules('pass', 'Password', 'trim|required|max_length[30]');*/
        JCryption::decrypt();
        // process
        //if ($this->tnotification->run() !== FALSE) {
            // params
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('pass'));
            // get user detail
            // $result = $this->m_account->get_user_login_auto_role($username, $password, $this->portal_id);
            // // check
            // if (!empty($result)) {
                // // cek lock status
                // if ($result['lock_st'] == '1') {
                    // // output
                    // redirect('login/stakeholderlogin/index/locked');
                // }
                // // set session
                // $this->tsession->set_userdata('session_pk_stakeholder', array('user_id' => $result['user_id'], 'role_id' => $result['role_id']));
                // // insert login time
                // $this->m_account->save_user_login($result['user_id'], $_SERVER['REMOTE_ADDR']);
                // // redirect
                redirect($result['default_page']);
            // } else {
                // // output
                // redirect('login/stakeholderlogin/index/error');
            // }
        /*} else {
            // default error
            redirect('login/stakeholderlogin/index/error');
        }
        // output
        redirect('login/stakeholderlogin');*/
    }

    // logout process
    public function logout_process() {
        // user id
        $user_id = $this->tsession->userdata('session_pk_stakeholder');
        // insert logout time
        $this->m_account->update_user_logout($user_id);
        // unset session
        $this->tsession->unset_userdata('session_pk_stakeholder');
        // output
        redirect('login/stakeholderlogin');
    }
    
    public function crypt() {
        $jc = new JCryption(BASEPATH . 'plugins/jcryption/key/rsa_1024_pub.pem', BASEPATH . 'plugins/jcryption/key/rsa_1024_priv.pem');
        $jc->go();
        header('Content-type: text/plain');
        print_r($_POST);
    }

}
