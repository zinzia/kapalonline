<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorLoginBase.php' );
require_once (BASEPATH . 'plugins/jcryption/JCryption.php');
require_once (BASEPATH . 'plugins/jcryption/sqAES.php');

// --

class operatorlogin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load global
        $this->load->library('tnotification');
        $this->load->library('tsession');
        $this->smarty->load_javascript("resource/js/jcryption/jquery.min.js");
        $this->smarty->load_javascript("resource/js/jcryption/jquery.jcryption.3.1.0.js");
    }

    // view
    public function index($status = "") {
        // set template content
        $this->smarty->assign("template_content", "login/operator/form.html");
        // role
        $this->load->model('m_settings');
        $this->smarty->assign("rs_role", $this->m_settings->get_all_roles());
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token", $csrf_token);
        $this->smarty->assign("token_nm", $csrf_token_nm);
        $this->smarty->assign("token", $csrf_token);
        //set captcha
        $this->load->helper("captcha");
        $vals = array(
            'img_path' => FCPATH . '/resource/doc/captcha/',
            'img_url' => base_url() . '/resource/doc' . '/captcha/',
            'img_width' => '150',
            'font_path' => FCPATH . '/resource/doc/font/COURIER.TTF',
            'font_size' => 60,
            'img_height' => 50,
            'expiration' => 7200
        );
        $captcha = create_captcha($vals);
        $data = array(
            'captcha_time' => $captcha['time'],
            'ip_address' => $_SERVER["REMOTE_ADDR"],
            'word' => $captcha['word']
        );
        $this->tsession->set_userdata("data", $data);
        $this->smarty->assign("captcha", $captcha);
        // bisnis proses
        if (!empty($this->com_user)) {
            // still login
            redirect('dashboard/welcome');
        } else {
            $this->smarty->assign("login_st", $status);
        }
        // output
        parent::display();
    }

    // login process
    public function login_process() {
        // JCryption
        JCryption::decrypt();
        // cek input
        $this->tnotification->set_rules('username', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('pass', 'Password', 'trim|required|max_length[30]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $username = trim($this->input->post('username', true));
            $password = trim($this->input->post('pass', true));
            // CSRF token
            $token_nm = $this->security->get_csrf_token_name();
            $csrf_token = $this->tsession->userdata("token", true);
            // captcha
            $captcha = $this->input->post('captcha', true);
            $captcha_data = $this->tsession->userdata('data');
            $expiration = time() - 7200;
            // check token
            if ($this->input->post($token_nm) == $csrf_token) {
                if ($captcha_data['word'] == $captcha AND $captcha_data['ip_address'] == $_SERVER["REMOTE_ADDR"] AND $captcha_data['captcha_time'] > $expiration) {
                    // get user detail
                    $result = $this->m_account->get_user_login_auto_role($username, $password, $this->portal_id);
                    // check
                    if (!empty($result)) {
                        // cek lock status
                        if ($result['lock_st'] == '1') {
                            // output
                            redirect('login/operatorlogin/index/locked');
                        }
                        // set session
                        $this->tsession->set_userdata('session_pk_online', array('user_id' => $result['user_id'], 'role_id' => $result['role_id']));
                        // insert login time
                        $this->m_account->save_user_login($result['user_id'], $_SERVER['REMOTE_ADDR']);
                        // redirect
                        redirect($result['default_page']);
                    }
                }
            }
        }
        // output
        redirect('login/operatorlogin/index/error');
    }

    // logout process
    public function logout_process() {
        // user id
        $user_id = $this->tsession->userdata('session_pk_online');
        // insert logout time
        $this->m_account->update_user_logout($user_id);
        // unset session
        $this->tsession->unset_userdata('session_pk_online');
        // output
        redirect('login/operatorlogin');
    }

    public function crypt() {
        $jc = new JCryption(BASEPATH . 'plugins/jcryption/key/rsa_1024_pub.pem', BASEPATH . 'plugins/jcryption/key/rsa_1024_priv.pem');
        $jc->go();
        header('Content-type: text/plain');
        print_r($_POST);
    }

}

