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
        $this->load->model('m_monitoring');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/index.html");
        // list role
        $this->smarty->assign("rs_roles", $this->m_monitoring->get_list_role());
        // get search parameter
        $search = $this->tsession->userdata('search_monitoring');
        // search parameters
        $document_no = empty($search['document_no']) ? '%' : '%' . $search['document_no'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $role_id = empty($search['role_id']) ? '%' : $search['role_id'];
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("task/monitoring/index/");
        $config['total_rows'] = $this->m_monitoring->get_total_awaiting_task(array($document_no, $airlines_nm, $airlines_nm, $role_id));
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
        $params = array($document_no, $airlines_nm, $airlines_nm, $role_id, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_monitoring->get_list_awaiting_task($params));
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
                "airlines_nm" => $this->input->post("airlines_nm"),
                "role_id" => $this->input->post("role_id")
            );
            $this->tsession->set_userdata("search_monitoring", $params);
        }
        // redirect
        redirect("task/monitoring");
    }

    // detail aunbdn
    public function detail_aunbdn($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/detail_fa.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

    // detail aunbln
    public function detail_aunbln($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/detail_fa.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

    // detail auntbdn
    public function detail_auntbdn($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/detail_fa.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

    // detail auntbln
    public function detail_auntbln($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/detail_fa.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

    // detail aubdn
    public function detail_aubdn($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/detail_fa.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

    // detail aubln
    public function detail_aubln($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring/detail_fa.html");
        // get detail data
        $this->smarty->assign("result", $this->m_monitoring->get_detail_data_by_id($data_id));
        // list process
        $this->smarty->assign("rs_process", $this->m_monitoring->get_list_process_by_id($data_id));
        // files
        $files = $this->m_monitoring->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // output
        parent::display();
    }

}
