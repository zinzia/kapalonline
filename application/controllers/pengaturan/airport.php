<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class airport extends ApplicationBase {

    //contructor
    public function __construct() {
        //parent contructor
        parent::__construct();
        //load model
        $this->load->model('m_airport');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list view
    public function index() {
        //set rule
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/airport/list.html");
        // get search parameter
        $search = $this->tsession->userdata('search_airport');
        // search parameters
        $airport_nm = empty($search['airport_nm']) ? '%' : '%' . $search['airport_nm'] . '%';
        $airport_st = empty($search['airport_st']) ? '%' : $search['airport_st'];
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/airport/index/");
        $config['total_rows'] = $this->m_airport->get_total_airport(array($airport_nm, $airport_st));
        $config['uri_segment'] = 4;
        $config['per_page'] = 50;
        $this->pagination->initialize($config);
        $pagination['data'] = $this->pagination->create_links();
        // pagination attribute
        $start = $this->uri->segment(4, 0) + 1;
        $end = $this->uri->segment(4, 0) + $config['per_page'];
        $end = (($end > $config['total_rows']) ? $config['total_rows'] : $end);
        $pagination['start'] = ($config['total_rows'] == 0) ? 0 : $start;
        $pagination['end'] = $end;
        $pagination['total'] = $config['total_rows'];
        // pagination assign value
        $this->smarty->assign("pagination", $pagination);
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list
        $params = array($airport_nm, $airport_st, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_airport->get_all_airport($params));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    //proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_airport');
        } else {
            $params = array(
                "airport_nm" => $this->input->post("airport_nm"),
                "airport_st" => $this->input->post("airport_st")
            );
            $this->tsession->set_userdata("search_airport", $params);
        }
        // redirect
        redirect("pengaturan/airport");
    }

    // add airport
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airport/add.html");
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('airport_nm', 'Nama Bandar Udara', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('airport_iata_cd', 'IATA Code', 'trim|required|max_length[3]');
        $this->tnotification->set_rules('airport_icao_cd', 'ICAO Code', 'trim|required|max_length[4]');
        $this->tnotification->set_rules('airport_st', 'Domestik/Internasional', 'trim|required');
        $this->tnotification->set_rules('airport_region', 'Region', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airport_country', 'Country', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airport_owner', 'Penyelenggara Bandara', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post("airport_nm"),
                $this->input->post('airport_iata_cd'),
                $this->input->post('airport_icao_cd'),
                $this->input->post('airport_st'),
                $this->input->post('airport_region'),
                $this->input->post('airport_country'),
                $this->input->post('airport_owner'),
                $this->com_user['user_id']
            );
            // insert
            if ($this->m_airport->insert($params)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/airport/add/");
    }

    // edit airport
    public function edit($airport_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airport/edit.html");
        // get data
        $this->smarty->assign("result", $this->m_airport->get_airport_by_id($airport_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('airport_id', 'ID BANDARA', 'trim|required]');
        $this->tnotification->set_rules('airport_nm', 'Nama Bandar Udara', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('airport_iata_cd', 'IATA Code', 'trim|required|max_length[3]');
        $this->tnotification->set_rules('airport_icao_cd', 'ICAO Code', 'trim|required|max_length[4]');
        $this->tnotification->set_rules('airport_st', 'Domestik/Internasional', 'trim|required');
        $this->tnotification->set_rules('airport_region', 'Region', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airport_country', 'Country', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airport_owner', 'Penyelenggara Bandara', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post("airport_nm"),
                $this->input->post('airport_iata_cd'),
                $this->input->post('airport_icao_cd'),
                $this->input->post('airport_st'),
                $this->input->post('airport_region'),
                $this->input->post('airport_country'),
                $this->input->post('airport_owner'),
                $this->com_user['user_id'],
                $this->input->post('airport_id'));
            // --
            if ($this->m_airport->update($params)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/airport/edit/" . $this->input->post('airport_id'));
    }

    // hapus form
    public function delete($airport_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airport/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_airport->get_airport_by_id($airport_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // hapus process
    public function delete_process() {
        // set page rules
        $this->_set_page_rule("D");
        // cek input
        $this->tnotification->set_rules('airport_id', 'Airport ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('airport_id'));
            // insert
            if ($this->m_airport->delete($params)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/airport");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/airport/delete/" . $this->input->post('airport_id'));
    }

}
