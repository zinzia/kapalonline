<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class manager extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_dashboard');
        $this->load->model('m_task');
        $this->load->model('m_airlines');
        $this->load->model('m_airport');
        $this->load->model('m_files');
        // load library
        $this->load->library('tnotification');
    }

    // index / list task for me
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "task/manager/index.html");
        // list waiting
        $rs_id = $this->m_task->get_list_my_task_waiting(array($this->com_user['role_id'], $this->com_user['user_id']));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // download
    public function files_download($file_id = "") {
        // get detail data
        $result = $this->m_files->get_detail_files_download_by_id($file_id);
        if (empty($result)) {
            redirect('task/manager/');
        }
        // filepath
        $file_path = $result['file_path'];
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . end(explode('/', $file_path)) . '"');
            readfile($file_path);
            exit();
        } else {
            redirect('task/manager/');
        }
    }

}
