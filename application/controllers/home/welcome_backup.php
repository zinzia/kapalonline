<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class welcome extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_home');
        $this->load->model('m_news');
        //library
        $this->load->library("tsession");
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "home/welcome/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.10.2.min.js");
        // load style ui
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        $this->smarty->load_javascript("resource/js/messi/messi.min.js");
        $this->smarty->load_style("messi/messi.min.css");
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
        // news
        $rs_news = $this->m_home->get_list_news(array($this->bahasa['lang_id'], 0, 2));
        $this->smarty->assign("rs_news", $rs_news);
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'home_%'));
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token",  $csrf_token);
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
        $this->tsession->set_userdata("data",$data);
        $this->smarty->assign("captcha", $captcha);
        // output
        parent::display();
    }

    // lang
    public function change_lang($lang_id = "", $nav_id = 248) {
        // get lang
        $result = $this->m_lang->get_lang_detail_by_id($lang_id);
        if (!empty($result)) {
            // set session
            $this->tsession->set_userdata('session_lang', $result);
        }
        $menu = $this->m_site->get_menu_by_id($nav_id);
        // output
        redirect($menu['nav_url']);
    }

}
