<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class pembayaran extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_pembayaran');
        // load library
        $this->load->library('pagination');
        $this->load->library('tnotification');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/pembayaran/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_pembayaran');
        // search parameters
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("task/pembayaran/index/");
        $config['total_rows'] = $this->m_pembayaran->get_total_awaiting_task(array($published_no, $airlines_nm));
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
        $params = array($published_no, $airlines_nm, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_pembayaran->get_list_awaiting_task($params));
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
            $this->tsession->unset_userdata('search_pembayaran');
        } else {
            $params = array(
                "published_no" => $this->input->post("published_no"),
                "airlines_nm" => $this->input->post("airlines_nm")
            );
            $this->tsession->set_userdata("search_pembayaran", $params);
        }
        // redirect
        redirect("task/pembayaran");
    }

    // proses pembayaran
    function proses_pembayaran($data_id) {
        // set page rules
        $this->_set_page_rule("U");
        $result = $this->m_pembayaran->get_detail_data_by_id(array($data_id));
        if ($result) {
            $this->m_pembayaran->update_pembayaran(array($data_id));
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data pembayaran berhasil disimpan");
        }
        // redirect
        redirect("task/pembayaran");
    }
}
