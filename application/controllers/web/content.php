<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class content extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_web_content');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "web/content/index.html");
        // get list
        $rs_id = $this->m_web_content->get_list_web_content();
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // content
    public function web_content_lang($data_id = "") {
        // set template content
        $this->smarty->assign("template_content", "web/content/content.html");
        // get detail
        $result = $this->m_web_content->get_detail_web_content_by_id($data_id);
        $this->smarty->assign("content", $result);
        // list lang content
        $rs_id = $this->m_web_content->get_list_lang_content($data_id);
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // content edit
    public function content_edit($data_id = "", $lang_id = "") {
        // set template content
        $this->smarty->assign("template_content", "web/content/content_edit.html");
        // get detail
        $result = $this->m_web_content->get_detail_web_content_by_id($data_id);
        $this->smarty->assign("content", $result);
        // detail lang content
        $result = $this->m_web_content->get_detail_lang_content(array($data_id, $lang_id));
        $this->smarty->assign("result", $result);
        $this->smarty->assign("lang_id", $lang_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit content process
    public function edit_content_process() {
        // cek input
        $this->tnotification->set_rules('data_id', 'News ID', 'trim|required');
        $this->tnotification->set_rules('lang_id', 'Languages', 'trim|required');
        $this->tnotification->set_rules('content_value', 'Content', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('data_id'), $this->input->post('lang_id'),
                $this->input->post('content_value')
            );
            // insert
            if ($this->m_web_content->update_content($params)) {
                // success
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
        redirect("web/content/content_edit/" . $this->input->post('data_id') . '/' . $this->input->post('lang_id'));
    }

}
