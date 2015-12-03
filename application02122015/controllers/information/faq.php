<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class faq extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_faq');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/faq/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.10.2.min.js");
        // get search parameter
        $search = $this->tsession->userdata('faq_search');
        $this->smarty->assign("search", $search);
        // search parameters
        $search_faq = empty($search['search_faq']) ? '%' : '%' . $search['search_faq'] . '%';
        // get list
        $rs_category = $this->m_faq->get_list_faq_information_category(array($search_faq, $search_faq, $search_faq));
        $this->smarty->assign("rs_category", $rs_category);
        // get list
        $rs_faq = $this->m_faq->get_list_faq_information_question(array($search_faq, $search_faq, $search_faq));
        $this->smarty->assign("rs_faq", $rs_faq);
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token", $csrf_token);
        $this->smarty->assign("token_nm", $csrf_token_nm);
        $this->smarty->assign("token", $csrf_token);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // data
        //CSRF token
        $token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->tsession->userdata("token");

        if ($this->input->post($token_nm) == $csrf_token) {
            if ($this->input->post('save') == "Reset") {
                $this->tsession->unset_userdata('faq_search');
            } else {
                $params = array(
                    "search_faq" => $this->input->post("search_faq"),
                );
                $this->tsession->set_userdata("faq_search", $params);
            }
            // redirect
            redirect("information/faq");
        } else {
            redirect("information/faq");
        }
    }

    // search faq
    public function search_faq() {
        $faq_id = $this->input->post('faq_id');
        //CSRF token
        $token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->tsession->userdata("token");
        
        if ($this->input->post($token_nm) == $csrf_token) {
            if (!empty($faq_id)) {
                // get faq detail
                $result = $this->m_faq->get_detail_faq_information_by_id($faq_id);
                $html = "
                <h1>" . $result['category_nm'] . "</h1>
                <h2>" . $result['faq_title'] . "</h2>
                <div class='faq-sub-content'>
                    <dl>
                        <dt>Tanya :</dt>
                        <dd><b>" . $result['faq_question'] . "</b></dd>
                        <dt>Jawab :</dt>
                        <dd>" . $result['faq_answer'] . "</dd>
                    </dl>
                </div>
            ";
                echo json_encode($html);
            } else {
                redirect('information/faq/');
            }
        } else {
             redirect('information/faq/');
        }
    }

}
