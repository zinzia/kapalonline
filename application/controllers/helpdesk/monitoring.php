<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class monitoring extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_monitoring_helpdesk');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "helpdesk/monitoring/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        $this->smarty->load_javascript('resource/js/fusioncharts/fusioncharts.js');
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get search parameter
        $search = $this->tsession->userdata('search_monitoring_helpdesk');
        // search parameters
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $document_no = empty($search['document_no']) ? '%' : '%' . $search['document_no'] . '%';
        $tanggal_pengajuan_from = empty($search['tanggal_pengajuan_from']) ? '%' : $search['tanggal_pengajuan_from'];
        $tanggal_pengajuan_to = empty($search['tanggal_pengajuan_to']) ? '%' : $search['tanggal_pengajuan_to'];
        $tanggal_penerbangan_from = empty($search['tanggal_penerbangan_from']) ? '%' : $search['tanggal_penerbangan_from'];
        $tanggal_penerbangan_to = empty($search['tanggal_penerbangan_to']) ? '%' : $search['tanggal_penerbangan_to'];
        $services_cd = empty($search['services_cd']) ? '%' : '%' . $search['services_cd'] . '%';
        $data_type = empty($search['data_type']) ? '%' : '%' . $search['data_type'] . '%';
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        $data_st = empty($search['data_st']) ? '%' : '%' . $search['data_st'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("helpdesk/monitoring/index/");
        $config['total_rows'] = $this->m_monitoring_helpdesk->get_total_awaiting_task(array($airlines_nm, $airlines_nm, $document_no, $tanggal_pengajuan_from, $tanggal_pengajuan_to, $tanggal_penerbangan_from, $tanggal_penerbangan_to, $tanggal_penerbangan_from, $tanggal_penerbangan_to, $services_cd, $data_type, $data_flight, $data_st));
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
        $params = array($airlines_nm, $airlines_nm, $document_no, $tanggal_pengajuan_from, $tanggal_pengajuan_to, $tanggal_penerbangan_from, $tanggal_penerbangan_to, $tanggal_penerbangan_from, $tanggal_penerbangan_to, $services_cd, $data_type, $data_flight, $data_st, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_monitoring_helpdesk->get_list_awaiting_task($params));
        // get list airlines
        $this->smarty->assign("rs_airlines", $this->m_monitoring_helpdesk->get_list_airlines());
        // list role
        $this->smarty->assign("rs_roles", $this->m_monitoring_helpdesk->get_list_role());
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_monitoring_helpdesk');
        } else {
            $params = array(
                "airlines_nm" => $this->input->post("airlines_nm"),
                "document_no" => $this->input->post("document_no"),
                "tanggal_pengajuan_from" => $this->input->post("tanggal_pengajuan_from"),
                "tanggal_pengajuan_to" => $this->input->post("tanggal_pengajuan_to"),
                "tanggal_penerbangan_from" => $this->input->post("tanggal_penerbangan_from"),
                "tanggal_penerbangan_to" => $this->input->post("tanggal_penerbangan_to"),
                "services_cd" => $this->input->post("services_cd"),
                "data_type" => $this->input->post("data_type"),
                "data_flight" => $this->input->post("data_flight"),
                "data_st" => $this->input->post("data_st"),
            );
            $this->tsession->set_userdata("search_monitoring_helpdesk", $params);
        }
        // redirect
        redirect("helpdesk/monitoring");
    }

    // detail
    public function detail($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "helpdesk/monitoring/detail_monitoring.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring_helpdesk->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring_helpdesk->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring_helpdesk->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

    // detail form
    public function form($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "helpdesk/monitoring/detail_form.html");
        // get detail data
        $result = $this->m_monitoring_helpdesk->get_detail_data_by_id($data_id);
        $this->smarty->assign("result", $result);
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring_helpdesk->get_list_process_by_id($data_id));
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_monitoring_helpdesk->get_services_cd(array($result['data_type'], $result['data_flight'])));
        // output
        parent::display();
    }

    // detail files
    public function files($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "helpdesk/monitoring/detail_files.html");
        // get detail data
        $result = $this->m_monitoring_helpdesk->get_detail_data_by_id($data_id);
        $this->smarty->assign("result", $result);
        // files
        $files = $this->m_monitoring_helpdesk->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        $this->smarty->assign("rs_files", $files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_helpdesk->get_list_file_uploaded(array($data_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // output
        parent::display();
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_monitoring_helpdesk->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect('helpdesk/monitoring');
        }
        // filepath
        $file_path = $result['file_path'];
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . end(explode('/', $file_path)) . '"');
            readfile($file_path);
            exit();
        } else {
            redirect('helpdesk/monitoring');
        }
    }

}
