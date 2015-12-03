<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class monitoring extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_member');
        $this->load->model('m_files');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_monitoring');
        // search parameters
        $document_no = empty($search['document_no']) ? '%' : '%' . $search['document_no'] . '%';
        $data_type = empty($search['data_type']) ? '%' : $search['data_type'];
        $data_flight = empty($search['data_flight']) ? '%' : $search['data_flight'];
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/monitoring/index/");
        $config['total_rows'] = $this->m_member->get_total_awaiting_task(array($document_no, $data_type, $data_flight, $this->com_user['airlines_id']));
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
        $params = array($document_no, $data_type, $data_flight, $this->com_user['airlines_id'], ($start - 1), $config['per_page']);
        $rs_id = $this->m_member->get_list_awaiting_task($params);
        $this->smarty->assign("rs_id", $rs_id);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_monitoring');
        } else {
            $params = array(
                "document_no" => $this->input->post("document_no"),
                "data_type" => $this->input->post("data_type"),
                "data_flight" => $this->input->post("data_flight")
            );
            $this->tsession->set_userdata("search_monitoring", $params);
        }
        // redirect
        redirect("member/monitoring");
    }

    // detail aunbdn
    public function aunbdn($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/aunbdn.html");
        // get detail data
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id'])));
        $this->smarty->assign("result_rute", $this->m_member->get_data_rute_by_id(array($data_id)));
        $this->smarty->assign("total_rute", COUNT($this->m_member->get_data_rute_by_id(array($data_id))));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_member->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_member->get_all_service_code());
        // get remark field
        $this->smarty->assign("remark_field", $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // detail aunbln
    public function aunbln($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/aunbln.html");
        // get detail data
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id'])));
        $this->smarty->assign("result_rute", $this->m_member->get_data_rute_by_id(array($data_id)));
        $this->smarty->assign("total_rute", COUNT($this->m_member->get_data_rute_by_id(array($data_id))));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_member->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // detail auntbdn
    public function auntbdn($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/auntbdn.html");
        // get detail data
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id'])));
        $this->smarty->assign("result_rute", $this->m_member->get_data_rute_by_id(array($data_id)));
        $this->smarty->assign("total_rute", COUNT($this->m_member->get_data_rute_by_id(array($data_id))));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_member->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // detail auntbln
    public function auntbln($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/auntbln.html");
        // get detail data
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id'])));
        $this->smarty->assign("result_rute", $this->m_member->get_data_rute_by_id(array($data_id)));
        $this->smarty->assign("total_rute", COUNT($this->m_member->get_data_rute_by_id(array($data_id))));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_member->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // detail aubnln
    public function aubnln($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/aubnln.html");
        // get detail data
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id'])));
        $this->smarty->assign("result_rute", $this->m_member->get_data_rute_by_id(array($data_id)));
        $this->smarty->assign("total_rute", COUNT($this->m_member->get_data_rute_by_id(array($data_id))));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_member->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // detail aubndn
    public function aubndn($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring/aubndn.html");
        // get detail data
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id'])));
        $this->smarty->assign("result_rute", $this->m_member->get_data_rute_by_id(array($data_id)));
        $this->smarty->assign("total_rute", COUNT($this->m_member->get_data_rute_by_id(array($data_id))));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_member->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // download
    public function files_download($data_id = "") {
        // get detail data
        $result = $this->m_files->get_detail_files_download_by_id($file_id);
        if (empty($result)) {
            redirect('member/monitoring/');
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
            redirect('member/monitoring/');
        }
    }

}
