<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class slot extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_airport');
        $this->load->model('m_airlines');
         // load library
        $this->load->library('tnotification');
        $this->load->library('score_services');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/slot/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("select2/select2.css");
		// get search parameter
        $search = $this->tsession->userdata('search_slot_time');
        // search parameters
        $airlines_iata_cd = empty($search['airlines_iata_cd']) ? '' : trim($search['airlines_iata_cd']);
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
                    $params = new getConfirmedSlot($airlines_iata_cd, $rute_from, $rute_to, $season_code, $services_code, $flight_no);
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
        // get list airlines
        $this->smarty->assign("airlines", $this->m_airlines->get_all_airlines_nolimit());
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
            $airlines_iata_cd = $this->input->post("airlines_iata_cd");
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
                "airlines_iata_cd" => strtoupper(trim($airlines_iata_cd)),
                "flight_no" => strtoupper(trim($flight_no)),
                "rute_from" => strtoupper(trim($rute_from)),
                "rute_to" => strtoupper(trim($rute_to)),
                "services_code" => strtoupper(trim($services_code)),
                "season_code" => strtoupper(trim($season_code)),
            );
            $this->tsession->set_userdata("search_slot_time", $params);
        }
        // redirect
        redirect("task/slot");
    }

}
