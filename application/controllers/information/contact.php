<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class contact extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_contact');
        // load library
        $this->load->library('tnotification');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/contact/index.html");
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'contact_%'));
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('contact_name', 'Name', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('contact_from', 'From', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('contact_message', 'Message', 'trim|maxlength[100]');
        $this->tnotification->set_rules('contact_email', 'Email', 'trim|required|maxlength[100]|valid_email');
        // proses
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('contact_name'), $this->input->post('contact_from'),
                $this->input->post('contact_message'), $this->input->post('contact_email')
            );
            // send contact
            if ($this->m_contact->send($params)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data already send");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data failed to send");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data failed to send");
        }
        //default redirect
        redirect("information/contact");
    }

}
