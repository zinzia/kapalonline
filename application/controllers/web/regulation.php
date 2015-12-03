<?php

if (!defined("BASEPATH"))
    exit("No direct script access allowed");
// load base class if needed
require_once( APPPATH . "controllers/base/OperatorBase.php" );

class regulation extends ApplicationBase {

    //constructor
    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model('m_regulation');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list projects
    public function index() {
        //set page rules
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "web/regulation/list.html");
        // get list
        $rs_id = $this->m_regulation->get_list_regulation();
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
        $this->smarty->assign("template_content", "web/regulation/add.html");

        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('judul', 'Judul', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('deskripsi', 'Deskripsi', 'trim|required|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('judul'), $this->input->post('deskripsi'), $this->com_user['user_id']);
            // insert
            if ($this->m_regulation->insert($params)) {
                // load
                $this->load->library('tupload');
                // last id
                $doc_id = $this->m_regulation->get_last_inserted_id();
                // upload file
                if (!empty($_FILES['file_name']['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/files/';
                    $config['allowed_types'] = 'pdf';
                    $config['file_name'] = $doc_id;
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload('file_name')) {
                        $data = $this->tupload->data();
                        $this->m_regulation->update_file(array($_FILES['file_name']['name'], $doc_id));
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
        redirect("web/regulation/add/");
    }

    // delete process
    public function delete_process($data_id = "") {
        $params = array($data_id);
        // delete
        if ($this->m_regulation->delete($params)) {
            // unlink
            if (is_file('resource/doc/files/' . $data_id . '.pdf')) {
                unlink('resource/doc/files/' . $data_id . '.pdf');
            }
            // --
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("web/regulation/");
    }

    // edit
    public function edit($data_id = "") {
        //set rules
        $this->_set_page_rule("U");
        //set template content
        $this->smarty->assign("template_content", "web/regulation/edit.html");
        // detail
        $result = $this->m_regulation->get_data_by_id(array($data_id));
        $this->smarty->assign("result", $result);
        $this->smarty->assign("regulation", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        //output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('judul', 'Judul', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('deskripsi', 'Deskripsi', 'trim|required|max_length[255]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('judul'), $this->input->post('deskripsi'),
                $this->input->post('download'), $this->com_user['user_id'],
                $this->input->post('data_id'));
            // update
            if ($this->m_regulation->update($params)) {
                // load
                $this->load->library('tupload');
                // last id
                $doc_id = $this->input->post('data_id');
                // upload file
                if (!empty($_FILES['file_name']['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/files/';
                    $config['allowed_types'] = 'pdf';
                    $config['file_name'] = $doc_id;
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload('file_name')) {
                        $data = $this->tupload->data();
                        $this->m_regulation->update_file(array($_FILES['file_name']['name'], $doc_id));
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
        redirect("web/regulation/edit/" . $this->input->post('data_id'));
    }

    // download
    public function download($data_id = "") {
        // get detail data
        $result = $this->m_regulation->get_data_by_id(array($data_id));
        if (empty($result)) {
            redirect('web/regulation/');
        }
        // filepath
        $file_path = 'resource/doc/files/' . $result['data_id'] . '.pdf';
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $result['file_name']) . '"');
            readfile($file_path);
            exit();
        } else {
            redirect('web/regulation/');
        }
    }

}
