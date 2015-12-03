<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class block extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_block');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/block/list.html");
        // get search parameter
        $search = $this->tsession->userdata('block_search');
        // search parameters
        $block_st = !isset($search['block_st']) ? '1' : $search['block_st'];
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/block/index/");
        $config['total_rows'] = $this->m_block->get_total_block(array($block_st));
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
        $params = array($block_st, ($start - 1), $config['per_page']);
        $rs_id = $this->m_block->get_all_block($params);
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('block_search');
        } else {
            $params = array(
                "block_st" => $this->input->post("block_st"),
            );
            $this->tsession->set_userdata("block_search", $params);
        }
        // redirect
        redirect("pengaturan/block");
    }

    // add airlines
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/block/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        //get data
        // load airlines
        $this->smarty->assign("rs_airlines", $this->m_block->get_all_airlines());
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
        $this->tnotification->set_rules('block_reason', 'Alasan Pemblokiran', 'trim|required|max_length[5000]');
        $this->tnotification->set_rules('airlines_id', 'Operator / Airlines', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // airlines
            $airlines_id = $this->input->post('airlines_id');
            // block id
            $block_id = $this->m_block->get_block_id();
            $params = array(
                "block_id" => $block_id,
                "airlines_id" => $airlines_id,
                "block_reason" => $this->input->post('block_reason'),
                "block_st" => '1',
                "block_by" => $this->com_user['user_id'],
                "block_date" => date('Y-m-d H:i:s'),
            );
            $this->m_block->insert($params);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/block/add/");
    }

    // edit airlines
    public function edit($block_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/block/edit.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // get data
        $result = $this->m_block->get_detail_block_by_id($block_id);
        if ($result['block_st'] == "open") {
            redirect('pengaturan/block/');
        }
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
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
        $this->tnotification->set_rules('block_id', 'ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "block_st" => '0',
            );
            $where = array(
                "block_id" => $this->input->post('block_id')
            );
            if ($this->m_block->update($params, $where)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                redirect("pengaturan/block/");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/block/edit/" . $this->input->post('block_id'));
    }

    // hapus form
    public function delete($block_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/block/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_block->get_block_by_id($airlines_id));
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
        $this->tnotification->set_rules('airlines_id', 'Airlines ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('airlines_id'));
            // insert
            if ($this->m_block->delete($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/block");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/block/delete/" . $this->input->post('airlines_id'));
    }

}
