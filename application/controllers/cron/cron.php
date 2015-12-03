<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class cron extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_cron');
    }

    // routes
    public function index() {
        $this->m_cron->get_registration_overdue();
    }

    // update izin active
    public function update_izin_active() {
        $this->m_cron->update_izin_active();
    }
}
