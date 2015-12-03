<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class regulation extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_home');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/regulation/index.html");
        // regulation
        $rs_regulation = $this->m_home->get_list_regulation(array(0, 100));
        $this->smarty->assign("rs_regulation", $rs_regulation);
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'reg_%'));
        // output
        parent::display();
    }

    // download
    public function download($data_id = "") {
        // get detail data
        $result = $this->m_home->get_detail_regulation_by_id(array($data_id));
        if (empty($result)) {
            redirect('information/regulation/');
        }
        // filepath
        $file_path = 'resource/doc/files/' . $result['data_id'] . '.pdf';
        if (is_file($file_path)) {
            // update
            $this->m_home->update_regulation_download(array($data_id));
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . $result['file_name'] . '"');
            readfile($file_path);            
            exit();
        } else {
            redirect('information/regulation/');
        }
    }

}
