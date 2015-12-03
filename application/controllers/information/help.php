<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class help extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // exit
        redirect('home/welcome');
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/help/index.html");

        // output
        parent::display();
    }

    // download
    public function download($files = "") {
        if (!in_array($files, array('aunbdn', 'aunbln', 'auntbdn', 'auntbln', 'aubndn', 'aubnln', 'bni', 'migrasi'))) {
            redirect('information/help');
        } else {
            // filepath
            $file_path = 'resource/doc/help/' . $files . '.pdf';
            if (is_file($file_path)) {
                // download
                header('Content-Description: Download Files');
                header('Content-Type: application/octet-stream');
                header('Content-Length: ' . filesize($file_path));
                header('Content-Disposition: attachment; filename="' . 'USER_GUIDE_FA_' . strtoupper($files) . '.pdf"');
                readfile($file_path);
                exit();
            } else {
                redirect('information/help');
            }
        }
    }

}
