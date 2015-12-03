<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class news extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_news');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "web/news/index.html");
        // get list
        $rs_id = $this->m_news->get_list_news();
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
        $this->smarty->assign("template_content", "web/news/add.html");
        // get default languages
        $lang_id = $this->m_news->get_default_lang();
        $this->smarty->assign("lang_id", $lang_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('news_title', 'News', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('news_lang_title', 'Title', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('news_lang_content', 'Content', 'trim|required');
        $this->tnotification->set_rules('lang_id', 'Languages', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('news_title'), $this->com_user['user_id']);
            // insert
            if ($this->m_news->insert($params)) {
                // user id
                $news_id = $this->m_news->get_last_inserted_id();
                // params operator
                $params = array(
                    $news_id, $this->input->post('lang_id'), $this->input->post('news_lang_title'),
                    $this->input->post('news_lang_content')
                );
                // insert content
                if ($this->m_news->insert_news_lang($params)) {
                    // upload foto
                    if (!empty($_FILES['news_lang_img']['tmp_name'])) {
                        // load
                        $this->load->library('tupload');
                        // upload config
                        $config['upload_path'] = 'resource/doc/news/' . $news_id;
                        $config['allowed_types'] = 'gif|jpg|png';
                        $config['file_name'] = $this->input->post('lang_id');
                        $this->tupload->initialize($config);
                        // process upload images
                        if ($this->tupload->do_upload_image('news_lang_img')) {
                            $data = $this->tupload->data();
                            $this->m_news->update_news_img(array($data['file_name'], $news_id, $this->input->post('lang_id')));
                        } else {
                            // jika gagal
                            $this->tnotification->set_error_message($this->tupload->display_errors());
                        }
                    }
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
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("web/news/add/");
    }

    // delete process
    public function delete_process($data_id = "") {
        $params = array($data_id);
        // delete
        if ($this->m_news->delete($params)) {
            // library
            $this->load->library('tupload');
            // unlink
            $this->tupload->remove_dir('resource/doc/news/' . $data_id);
            // --
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("web/news/");
    }

    // edit
    public function edit($news_id = "") {
        //set rules
        $this->_set_page_rule("U");
        //set template content
        $this->smarty->assign("template_content", "web/news/edit.html");
        // get detail
        $result = $this->m_news->get_detail_news_by_id($news_id);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("news", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // cek input
        $this->tnotification->set_rules('news_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('news_title', 'Judul', 'trim|required|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('news_title'), $this->com_user['user_id'], $this->input->post('news_id'));
            // update
            if ($this->m_news->update($params)) {
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
        redirect("web/news/edit/" . $this->input->post('news_id'));
    }

    // content
    public function content($news_id = "") {
        // set template content
        $this->smarty->assign("template_content", "web/news/content.html");
        // get detail
        $result = $this->m_news->get_detail_news_by_id($news_id);
        $this->smarty->assign("news", $result);
        // list lang content
        $rs_id = $this->m_news->get_list_lang_content($news_id);
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // content edit
    public function content_edit($news_id = "", $lang_id = "") {
        // set template content
        $this->smarty->assign("template_content", "web/news/content_edit.html");
        // get detail
        $result = $this->m_news->get_detail_news_by_id($news_id);
        $this->smarty->assign("news", $result);
        // detail lang content
        $result = $this->m_news->get_detail_lang_content(array($news_id, $lang_id));
        $this->smarty->assign("result", $result);
        $this->smarty->assign("lang_id", $lang_id);
        // images
        $filepath = 'resource/doc/news/' . $news_id . '/' . $result['news_lang_img'];
        if (!is_file($filepath)) {
            $filepath = 'resource/doc/images/users/default.png';
        }
        $news_lang_img = base_url() . $filepath;
        $this->smarty->assign("news_lang_img", $news_lang_img);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit content process
    public function edit_content_process() {
        // cek input
        $this->tnotification->set_rules('news_id', 'News ID', 'trim|required');
        $this->tnotification->set_rules('lang_id', 'Languages', 'trim|required');
        $this->tnotification->set_rules('news_lang_title', 'Title', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('news_lang_content', 'Content', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('news_id'), $this->input->post('lang_id'),
                $this->input->post('news_lang_title'), $this->input->post('news_lang_content')
            );
            // insert
            if ($this->m_news->update_content($params)) {
                // upload foto
                if (!empty($_FILES['news_lang_img']['tmp_name'])) {
                    $news_id = $this->input->post('news_id');
                    // load
                    $this->load->library('tupload');
                    // upload config
                    $config['upload_path'] = 'resource/doc/news/' . $news_id;
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['file_name'] = $this->input->post('lang_id');
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload_image('news_lang_img')) {
                        $data = $this->tupload->data();
                        $this->m_news->update_news_img(array($data['file_name'], $news_id, $this->input->post('lang_id')));
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
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
        redirect("web/news/content_edit/" . $this->input->post('news_id') . '/' . $this->input->post('lang_id'));
    }

}
