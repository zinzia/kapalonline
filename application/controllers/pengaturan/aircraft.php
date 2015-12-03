<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class aircraft extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_aircraft');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/aircraft/list.html");
        // get search parameter
        $search = $this->tsession->userdata('aircraft_search');
        // search parameters
        $manufacture = empty($search['manufacture']) ? '%' : '%' . $search['manufacture'] . '%';
        $model = empty($search['model']) ? '%' : '%' . $search['model'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/aircraft/index/");
        $config['total_rows'] = $this->m_aircraft->get_total_aircraft(array($manufacture, $model));
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
        $params = array($manufacture, $model, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_aircraft", $this->m_aircraft->get_all_aircraft($params));
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    //proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('aircraft_search');
        } else {
            $params = array(
                "manufacture" => $this->input->post("manufacture"),
                "model" => $this->input->post("model"),
                "registration" => $this->input->post("registration"),
            );
            $this->tsession->set_userdata("aircraft_search", $params);
        }
        // redirect
        redirect("pengaturan/aircraft");
    }

    // add airlines
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/aircraft/add.html");
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
        $this->tnotification->set_rules('aircraft_manufacture', 'Manufacture', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('aircraft_model', 'Model', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('aircraft_product_year', 'Tahun Produksi', 'trim');
        $this->tnotification->set_rules('aircraft_std_capacity', 'Kapasitas Standard', 'trim');
        $this->tnotification->set_rules('aircraft_desc', 'Deskripsi', 'trim');
        // run
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('aircraft_manufacture'),
                $this->input->post('aircraft_model'),
                $this->input->post('aircraft_product_year'),
                $this->input->post('aircraft_std_capacity'),
                $this->input->post('aircraft_desc'),
                $this->com_user['user_id']);
            // insert
            if ($this->m_aircraft->insert($params)) {
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
        redirect("pengaturan/aircraft/add/");
    }

    // edit aircraft
    public function edit($aircraft_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/aircraft/edit.html");
        // get data
        $result = $this->m_aircraft->get_aircraft_by_id($aircraft_id);
        $this->smarty->assign("result", $result);
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
        $this->tnotification->set_rules('aircraft_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('aircraft_manufacture', 'Manufacture', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('aircraft_model', 'Model', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('aircraft_product_year', 'Tahun Produksi', 'trim');
        $this->tnotification->set_rules('aircraft_std_capacity', 'Kapasitas Standard', 'trim');
        $this->tnotification->set_rules('aircraft_desc', 'Deskripsi', 'trim');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('aircraft_manufacture'),
                $this->input->post('aircraft_model'),
                $this->input->post('aircraft_product_year'),
                $this->input->post('aircraft_product_year'),
                $this->input->post('aircraft_desc'),
                $this->com_user['user_id'], $this->input->post('aircraft_id')
            );
            if ($this->m_aircraft->update($params)) {
                // notifikasi
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
        redirect("pengaturan/aircraft/edit/" . $this->input->post('aircraft_id'));
    }

    // hapus form
    public function delete($aircraft_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/aircraft/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_aircraft->get_aircraft_by_id($aircraft_id));
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
        $this->tnotification->set_rules('aircraft_id', 'Airlines ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('aircraft_id'));
            // insert
            if ($this->m_aircraft->delete($params)) {
                // notifikasi
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/aircraft");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/aircraft/delete/" . $this->input->post('aircraft_id'));
    }

}
