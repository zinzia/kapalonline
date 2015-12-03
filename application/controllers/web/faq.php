<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class faq extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_faq');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "web/faq/index.html");
        // get list
        $rs_id = $this->m_faq->get_list_faq_category();
        // --
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add
    public function add() {
        //set rules
        $this->_set_page_rule("C");
        //set template content
        $this->smarty->assign("template_content", "web/faq/add.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('category_nm', 'Nama Kategori', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('category_desc', 'Deskripsi Kategori', 'trim|required|max_length[150]');
        $this->tnotification->set_rules('category_seq', 'Urutan Kategori', 'trim|required|max_length[11]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "category_nm"   => $this->input->post('category_nm'),
                "category_desc"   => $this->input->post('category_desc'),
                "category_seq"   => $this->input->post('category_seq'),
                "mdb"   => $this->com_user['user_id'],
                "mdd"   => date('Y-m-d H:i:s'),
            );
            // insert
            if ($this->m_faq->insert($params)) {
                // user id
                $category_id = $this->m_faq->get_last_inserted_id();
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("web/faq/detail/" . $category_id);
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("web/faq/add/");
    }

    // add
    public function detail($category_id = "") {
        //set rules
        $this->_set_page_rule("C");
        //set template content
        $this->smarty->assign("template_content", "web/faq/detail.html");
        // get detail
        $result = $this->m_faq->get_detail_faq_category_by_id($category_id);
        $this->smarty->assign("result", $result);
        // get detail question
        $rs_id = $this->m_faq->get_detail_question_by_category($category_id);
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // add question
    public function add_question($category_id = "") {
        //set rules
        $this->_set_page_rule("C");
        //set template content
        $this->smarty->assign("template_content", "web/faq/add_question.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // $this->smarty->load_javascript("resource/js/wysiwyg/wysihtml5-0.3.0.js");
        // $this->smarty->load_javascript("resource/js/wysiwyg/prettify.js");
        // $this->smarty->load_javascript("resource/js/wysiwyg/bootstrap.min.js");
        // $this->smarty->load_javascript("resource/js/wysiwyg/bootstrap-wysihtml5.js");
        $this->smarty->load_javascript("resource/js/wysiwyg2/ckeditor.js");
        $this->smarty->load_javascript("resource/js/wysiwyg2/jquery.js");
        // load style ui
        $this->smarty->load_style("wysiwyg/bootstrap.min.css");
        $this->smarty->load_style("wysiwyg/bootstrap-wysihtml5.css");
        // get detail
        $result = $this->m_faq->get_detail_faq_category_by_id($category_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // add process
    public function add_question_process() {
        // cek input
        $this->tnotification->set_rules('category_id', 'Id', 'trim|required');
        $this->tnotification->set_rules('faq_title', 'Judul', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('faq_question', 'Pertanyaan', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('faq_answer', 'Jawaban', 'trim|required');
        $this->tnotification->set_rules('faq_seq', 'Urutan', 'trim|required|max_length[11]');
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "category_id"   => $this->input->post('category_id'),
                "faq_title"   => $this->input->post('faq_title'),
                "faq_question"   => $this->input->post('faq_question'),
                "faq_answer"   => $this->input->post('faq_answer'),
                "faq_seq"   => $this->input->post('faq_seq'),
                "mdb"   => $this->com_user['user_id'],
                "mdd"   => date('Y-m-d H:i:s'),
            );
            // insert
            if ($this->m_faq->insert_question($params)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("web/faq/add_question/" . $this->input->post('category_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("web/faq/add_question/" . $this->input->post('category_id'));
    }

    // edit question
    public function edit_question($faq_id = "") {
        //set rules
        $this->_set_page_rule("C");
        //set template content
        $this->smarty->assign("template_content", "web/faq/edit_question.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/wysiwyg2/ckeditor.js");
        $this->smarty->load_javascript("resource/js/wysiwyg2/jquery.js");
        // load style ui
        $this->smarty->load_style("wysiwyg/bootstrap.min.css");
        $this->smarty->load_style("wysiwyg/bootstrap-wysihtml5.css");
        // get detail
        $result = $this->m_faq->get_detail_faq_by_id($faq_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // edit process
    public function edit_question_process() {
        // cek input
        $this->tnotification->set_rules('faq_id', 'Id', 'trim|required');
        $this->tnotification->set_rules('category_id', 'Id', 'trim|required');
        $this->tnotification->set_rules('faq_title', 'Judul', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('faq_question', 'Pertanyaan', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('faq_answer', 'Jawaban', 'trim|required');
        $this->tnotification->set_rules('faq_seq', 'Urutan', 'trim|required|max_length[11]');
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "faq_title"   => $this->input->post('faq_title'),
                "faq_question"   => $this->input->post('faq_question'),
                "faq_answer"   => $this->input->post('faq_answer'),
                "faq_seq"   => $this->input->post('faq_seq'),
                "mdb"   => $this->com_user['user_id'],
                "mdd"   => date('Y-m-d H:i:s'),
            );
            $where = array(
                "faq_id"    => $this->input->post('faq_id'),
            );
            // insert
            if ($this->m_faq->update_question($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("web/faq/edit_question/" . $this->input->post('faq_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("web/faq/edit_question/" . $this->input->post('faq_id'));
    }

    // edit
    public function edit($category_id = "") {
        //set rules
        $this->_set_page_rule("U");
        //set template content
        $this->smarty->assign("template_content", "web/faq/edit.html");
        // get detail
        $result = $this->m_faq->get_detail_faq_category_by_id($category_id);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // cek input
        $this->tnotification->set_rules('category_nm', 'Nama Kategori', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('category_desc', 'Deskripsi Kategori', 'trim|required|max_length[150]');
        $this->tnotification->set_rules('category_seq', 'Urutan Kategori', 'trim|required|max_length[11]');
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "category_nm"   => $this->input->post('category_nm'),
                "category_desc"   => $this->input->post('category_desc'),
                "category_seq"   => $this->input->post('category_seq'),
                "mdb"   => $this->com_user['user_id'],
                "mdd"   => date('Y-m-d H:i:s'),
            );
            $where = array(
                "category_id"   => $this->input->post('category_id'),
            );
            // insert
            if ($this->m_faq->update($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // default redirect
                redirect("web/faq/edit/" . $this->input->post('category_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("web/faq/edit/" . $this->input->post('category_id'));
    }

    // delete process
    public function delete_process($category_id = "") {
        $params = array(
            "category_id"   => $category_id
        );
        // delete
        if ($this->m_faq->delete($params)) {
            // --
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("web/faq/");
    }

    // delete question process
    public function delete_question_process($data_id = "", $category_id = "") {
        $params = array(
            "faq_id"    => $data_id,
        );
        // delete
        if ($this->m_faq->delete_question($params)) {
            // --
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("web/faq/detail/" . $category_id);
    }

}
