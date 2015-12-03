<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class registration_scheduled extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_registration');
        $this->load->model('m_airlines');
        $this->load->model('m_airport');
        $this->load->model('m_service_code');
        $this->load->model('m_files');
        $this->load->model('m_email');
        $this->load->model('m_disclaimer');
        $this->load->model('m_block');
        // load library
        $this->load->library('tnotification');
        $this->load->library('session');
        $this->load->library('pagination');
        $this->load->library('converttime');
        try{
            $this->load->library("score");
        } catch (Exception $error) {
        }        
        // // check status bayar
        // $result = $this->m_registration->get_fa_unpaid(array($this->com_user['airlines_id'], date('Y-m-d')));
        // if ($result > 0) {
            // // notification
            // $this->tnotification->sent_notification("error", "Silahkan Membayar FA yang telah terbit terlebih dahulu");
            // redirect('member/welcome');
        // }
        // // get airlines type
        // $result = $this->m_registration->get_airlines_type(array($this->com_user['airlines_id']));
        // foreach ($result as $value) {
            // $data[] = $value['airlines_type'];
        // }
        // if (!in_array("berjadwal", $data)) {
            // // notification
            // $this->tnotification->sent_notification("error", "Anda Tidak Dapat Melakukan Registrasi Berjadwal");
            // redirect('member/welcome');
        // }
    }

    // list
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/registration_scheduled/list.html");
        // list opened form
        // $rs_id = $this->m_registration->get_list_registration_open(array('berjadwal', $this->com_user['airlines_id']));
        // $data = array();
        // foreach ($rs_id as $value) {
            // // get detail rute
            // $rs_rute = $this->m_registration->get_data_rute_by_id(array($value['data_id']));
            // $total_rute = COUNT($rs_rute);
            // $list_rute = "";
            // $x = 1;
            // foreach ($rs_rute as $rute) {
                // $x++;
                // $list_rute .= $rute['airport_iata_cd'];
                // if ($x <= $total_rute) {
                    // $list_rute .= "-";
                // }
            // }
            // $data[] = array(
                // "data_id" => $value['data_id'],
                // "published_no" => $value['published_no'],
                // "data_type" => $value['data_type'],
                // "data_flight" => $value['data_flight'],
                // "date_start" => $value['date_start'],
                // "date_end" => $value['date_end'],
                // "rute_all" => $list_rute,
                // "services_nm" => $value['services_nm'],
                // "flight_no" => $value['flight_no'],
                // "selisih_hari" => $value['selisih_hari'],
                // "selisih_waktu" => $value['selisih_waktu'],
            // );
        // }
        // $this->smarty->assign("rs_id", $rs_id);
        // $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }


}
