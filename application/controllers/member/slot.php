<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class slot extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_airport');
        // load library
        $this->load->library('tnotification');
        $this->load->library('score_services');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/slot/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_slot_time');
        // search parameters
        $rute_from = empty($search['rute_from']) ? '' : trim($search['rute_from']);
        $rute_to = empty($search['rute_to']) ? '' : trim($search['rute_to']);
        $services_code = empty($search['services_code']) ? '' : trim($search['services_code']);
        $season_code = empty($search['season_code']) ? '' : trim($search['season_code']);
        $flight_no = empty($search['flight_no']) ? '' : trim($search['flight_no']);
        // --
        $this->smarty->assign("search", $search);
        // validate input
        if (!empty($rute_from) && !empty($rute_to) && !empty($season_code)) {
            // cek semua bandara harus berada pada IASM
            if ($this->m_airport->is_airport_using_score_by_iata_cd(array($rute_from, $rute_to))) {
                try {
                    // request
                    $params = new getConfirmedSlot($this->com_user['airlines_iata_cd'], $rute_from, $rute_to, $season_code, $services_code, $flight_no);
                    // class object response
                    $response = $this->score_services->__soapCall('getConfirmedSlotSeasonal', array($params));
                    // get data from function
                    $rs_id = $response->confirmedSlots->confirmedScheduleList->confirmedSchedules;
                    // parse
                    if (count($rs_id) == 1) {
                        // object to list
                        $rs_id = array($rs_id);
                    }
                    $this->smarty->assign("rs_id", $rs_id);
                    $this->smarty->assign("total", count($rs_id));
                    // get airport detail
                    $local_time = $this->m_airport->get_local_time_airport_by_code($rute_from);
                    $this->smarty->assign("local_time", $local_time);
                } catch (Exception $error) {
                    /*
                      echo "<pre>";
                      var_dump($error);
                      echo "</pre>";
                      die;
                     * 
                     */
                }
            }
        }
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_slot_time');
        } else {
            if ($this->input->post('save') == 'View') {
                $rute_from = $this->input->post("rute_from");
                $rute_to = $this->input->post("rute_to");
            } else {
                $rute_from = $this->input->post("rute_to");
                $rute_to = $this->input->post("rute_from");
            }
            $services_code = $this->input->post("services_code");
            $season_code = $this->input->post("season_code");
            $flight_no = $this->input->post("flight_no");
            // cek semua bandara harus berada pada IASM
            $rute_from = empty($rute_from) ? '' : trim($rute_from);
            $rute_to = empty($rute_to) ? '' : trim($rute_to);
            // check
            if (!$this->m_airport->is_airport_using_score_by_iata_cd(array($rute_from, $rute_to))) {
                // notification
                $this->tnotification->set_error_message("Bandara yang dicari belum ada didalam sistem SCORE!");
                $this->tnotification->sent_notification("error", "Parameter not match!");
            }
            // set session
            $params = array(
                "flight_no" => strtoupper(trim($flight_no)),
                "rute_from" => strtoupper(trim($rute_from)),
                "rute_to" => strtoupper(trim($rute_to)),
                "services_code" => strtoupper(trim($services_code)),
                "season_code" => strtoupper(trim($season_code)),
            );
            $this->tsession->set_userdata("search_slot_time", $params);
        }
        // redirect
        redirect("member/slot");
    }

}
