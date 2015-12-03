<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/StakeholderBase.php' );

class report_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_report_izin_stakeholder');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "stakeholder/report_izin/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_report_izin_stakeholder');
        // search parameters
        $airlines_id = empty($search['airlines_id']) ? '%' : $search['airlines_id'];
        $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
        // assign
        $this->smarty->assign("search", $search);
        // list rute
        $no = 1;
        $params = array($airlines_id, $data_flight);
        $rs_id = $this->m_report_izin_stakeholder->get_izin_rute_data_by_airlines($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        $this->smarty->assign("no", $no);
        // get list airlines
        $this->smarty->assign("rs_airlines", $this->m_report_izin_stakeholder->get_list_airlines());
        // output
        parent::display();
    }

    // detail
    public function detail($kode_izin = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "stakeholder/report_izin/detail.html");
        // parameter
        $search = $this->tsession->userdata('search_report_izin_stakeholder');
        // search parameters
        $airlines_id = empty($search['airlines_id']) ? '%' : $search['airlines_id'];
        $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
        // get list frekuensi
        $params = array($airlines_id, $data_flight, $kode_izin);
        $rs_id = $this->m_report_izin_stakeholder->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('stakeholder/report_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_report_izin_stakeholder');
        } else {
            $params = array(
                "airlines_id" => $this->input->post("airlines_id"),
                "data_flight" => $this->input->post("data_flight"),
            );
            $this->tsession->set_userdata("search_report_izin_stakeholder", $params);
        }
        // redirect
        redirect("stakeholder/report_izin");
    }

}
