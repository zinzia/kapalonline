<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class bandara extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_dashboard_bandara');
        // load library
        $this->load->library('tnotification');
    }

    // index / list task for me
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "task/manager/index.html");
        // get user bandara 3 letter code
        $iata = $this->m_dashboard_bandara->get_user_bandara_iata(array($this->com_user['user_id']));
        // list waiting
        $rs_id = $this->m_dashboard_bandara->get_list_fa(array($this->com_user['user_id']));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // download
    public function files_download($data_id = "") {
        // get detail data
        $params = array($data_id);
        $result = $this->m_task->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect('task/manager/');
        }
        // filepath
        $file_path = 'resource/doc/fa/' . $data_id . '/' . $result['file_id'] . '.pdf';
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $result['file_path']) . '"');
            readfile($file_path);
            exit();
        } else {
            redirect('task/manager');
        }
    }

}
