<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class telaah_regulator extends ApplicationBase {

    // --
    public function __construct() {
        parent::__construct();
        //load model
        $this->load->model("regulator/m_task");
        //load library
        $this->load->library("tnotification");
        $this->load->library("doslibrary");
    }

    // list data waiting
    public function index() {
        $this->_set_page_rule("R");
        // set template
        $this->smarty->assign("template_content", "task/telaah_regulator/index.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // download draft telaah
    public function download() {
        // set page rules
        $this->_set_page_rule("R");
        // file path
        $file_path = "resource/doc/draft_telaah/draft_telaah.docx";
        // read data
        if(is_file($file_path)) {
            header('Content-type: application/octet-stream');
            header("Content-Length:" . filesize($file_path));
            header("Content-Disposition: attachment; filename=draft_telaah.docx");
            readfile($file_path);
            exit();
        } else {
            // default redirect
            redirect("task/telaah_regulator/");
        }
    }

    // upload process
    public function upload_process() {
        // set page rules
        $this->_set_page_rule("U");
        // load
        $this->load->library('tupload');
        // upload config
        $config['upload_path'] = 'resource/doc/draft_telaah';
        $config['allowed_types'] = 'docx';
        $config['file_name'] = 'draft_telaah';
        $this->tupload->initialize($config);
        if ( ! $this->tupload->do_upload('draft_telaah')) {
            // jika gagal (kembalikan pesan)
            $this->tnotification->set_error_message($this->tupload->display_errors());
            $this->tnotification->sent_notification("error", "File dokumen tidak berhasil di upload");
        } else {
            // // update memo
            // $params = array($this->com_user['user_id'], 'memo', 'last_update');
            // $this->m_preferences->update_preferences_memo_last_update($params);
            // --
            $this->tnotification->sent_notification("success", "File dokumen berhasil di upload");
        }
        // default redirect
        redirect("task/telaah_regulator/");
    }

}
