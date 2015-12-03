<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );
require_once (BASEPATH . 'plugins/jcryption/JCryption.php');
require_once (BASEPATH . 'plugins/jcryption/sqAES.php');

// --

class member extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_member');
        // load library
        $this->load->library('tnotification');
        $this->load->library('session');
        $this->load->library('tsession');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index($status = '') {
        // set template content
        $this->smarty->assign("template_content", "information/member/form.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.10.2.min.js");
        // status
        $this->smarty->assign("login_st", $status);
        // get user login
        $session = $this->tsession->userdata('session_pk_member');
        if (!empty($session)) {
            $result = $this->m_account->get_user_profil_airlines(array($session['user_id'], $session['role_id'], $session['airlines_id']));
            // images
            $filepath = 'resource/doc/images/users/' . $result['operator_photo'];
            if (!is_file($filepath)) {
                $filepath = 'resource/doc/images/users/default.png';
            }
            $result['operator_photo'] = base_url() . $filepath;
            $this->smarty->assign("com_user", $result);
        }
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'member_%'));
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token", $csrf_token);
        $this->smarty->assign("token_nm", $csrf_token_nm);
        $this->smarty->assign("token", $csrf_token);
        //set captcha
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
        $this->tsession->set_userdata("data",$data);
        $this->smarty->assign("captcha", $captcha);
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
            // JCryption::decrypt();
            // 
            // params
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('pass'));
            // get user detail
            $result = $this->m_account->get_user_login_auto_role($username, $password, $this->config->item('portal_member'));
            //CSRF token
            $token_nm = $this->security->get_csrf_token_name();
            $csrf_token = $this->tsession->userdata("token");

            $captcha = $this->input->post('captcha');
            $captcha_data = $this->tsession->userdata('data');
            
            $expiration = time() - 7200;

            if ($this->input->post($token_nm) == $csrf_token) {
                //check captcha
                if ($captcha_data['word'] == $captcha
                        AND $captcha_data['ip_address'] == $_SERVER["REMOTE_ADDR"]
                        AND $captcha_data['captcha_time'] > $expiration) {
                    // check
                    if (!empty($result)) {
                        // cek lock status
                        if ($result['lock_st'] == '1') {
                            // output
                            redirect('information/member/index/locked');
                        }
                        // get airlines
                        $airlines = $this->m_account->get_default_airlines(array($result['user_id']));
                        // set session
                        $this->tsession->set_userdata('session_pk_member', array('user_id' => $result['user_id'], 'role_id' => $result['role_id'],
                            'default_page' => $result['default_page'], 'airlines_id' => $airlines));
                        // insert login time
                        $this->m_account->save_user_login($result['user_id'], $_SERVER['REMOTE_ADDR']);
                        $this->tsession->unset_userdata("token");
                        // redirect
                        redirect($result['default_page']);
                    } else {
                        // output
                        redirect('information/member/index/error');
                    }
                } else {
                    redirect('information/member/index/error');
                }
            } else {
                redirect('information/member/index/error');
            }
        } else {
            // default error
            redirect('information/member/index/error');
        }
        // output
        redirect('information/member');
    }

    // logout process
    public function logout_process() {
        // user id
        $user_id = $this->tsession->userdata('session_pk_member');
        // insert logout time
        $this->m_account->update_user_logout($user_id);
        // unset session
        $this->tsession->unset_userdata('session_pk_member');
        // output
        redirect('home/welcome');
    }

    // forget
    public function forget_password() {
        // set template content
        $this->smarty->assign("template_content", "information/member/forget_password.html");
        //set captcha
        $this->load->helper("captcha");
        $vals = array(
            'img_path' => FCPATH . '/resource/doc/captcha/',
            'img_url' => base_url() . '/resource/doc' . '/captcha/',
            'img_width' => '150',
            'font_path' => FCPATH . '/resource/doc/font/CONSOLAS.TTF',
            'font_size' => 45,
            'img_height' => 50,
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

    // send email
    public function forget_process() {
        // set rules
        $this->tnotification->set_rules('email_address', 'Email', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get email preferences
            $this->load->model("m_preferences");
            $mail = $this->m_preferences->get_mail();
            $detail = explode(",", $mail['pref_value']);
            $host = $mail['pref_nm'];
            $port = $detail[0];
            $user = $detail[1];
            $pass = $detail[2];
            // load email
            $this->load->library('email');
            // init
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = str_replace(" ", "", $host);
            $config['smtp_port'] = str_replace(" ", "", $port);
            $config['smtp_timeout'] = '7';
            $config['smtp_user'] = str_replace(" ", "", $user);
            $config['smtp_pass'] = str_replace(" ", "", $pass);
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['validation'] = TRUE; // bool whether to validate email or not
            $config['mailtype'] = 'html'; // bool whether to validate email or not
            $this->email->initialize($config);
            // get detail
            $result = $this->m_member->get_member_detail_by_email($this->input->post('email_address'));
            if (!empty($result)) {
                $html = "<b>Here your login in Flight Approval Online System</b><br />";
                $html = "<b>Pendaftaran Kapal Online - Kementerian Perhubungan</b><br />";
                $html .= "Username : " . $result['user_name'] . '<br />';
                $html .= "Password : " . $result['user_pass'] . '<br />';
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($result['user_mail']);
                $this->email->subject('Forget Password');
                $this->email->message($html);
                $cap = $this->input->post("captcha");
                $expiration = time() - 7200;
                if ($this->session->userdata('word') == $cap AND $this->session->userdata('ip_address') == $_SERVER["REMOTE_ADDR"] AND $this->session->userdata('captcha_time') > $expiration) {
                    $this->email->send();
                    // send
                    $this->tnotification->sent_notification("success", "Data already send");
                } else {
                    $this->tnotification->sent_notification("error", "Captcha doesn't match");
                }
            } else {
                $this->tnotification->sent_notification("error", "Data failed to send2");
            }
        } else {
            $this->tnotification->sent_notification("error", "Data failed to send");
        }
        //delete captcha img
        unlink(FCPATH . "/resource/doc/captcha/" . $this->session->userdata('captcha_time') . ".jpg");
        // output
        redirect('information/member/forget_password');
    }

    public function crypt() {
        $jc = new JCryption(BASEPATH . 'plugins/jcryption/key/rsa_1024_pub.pem', BASEPATH . 'plugins/jcryption/key/rsa_1024_priv.pem');
        $jc->go();
        header('Content-type: text/plain');
        print_r($_POST);
    }

}
