<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class report extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_member');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/report/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_report');
        // search parameters
        $document_no = empty($search['document_no']) ? '' : '%' . $search['document_no'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/report/index/");
        $config['total_rows'] = $this->m_member->get_total_finished_task_report(array($document_no, $this->com_user['airlines_id']));
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
        $params = array($document_no, $this->com_user['airlines_id'], ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_member->get_list_finished_task_report($params));
        // output
        parent::display();
    }

}
