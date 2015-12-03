<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class delegasi extends ApplicationBase {

    //contructor
    public function __construct() {
        //parent contructor
        parent::__construct();
        //load model
        $this->load->model('m_delegasi');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list view
    public function index() {
        //set rule
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/delegasi/list.html");
        // get list
        $this->smarty->assign("rs_id", $this->m_delegasi->get_list_delegasi());
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add form
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/delegasi/add.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        $this->smarty->load_style("select2/select2.css");
        // load sub direktorat
        $this->smarty->assign("rs_kasubdit", $this->m_delegasi->get_all_operator_kasubdit());
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
        $this->tnotification->set_rules('user_id', 'Kasubdit', 'trim|required');
        $this->tnotification->set_rules('delegation_reason', 'Alasan Pendelegasian', 'trim');
        // proses
        if ($this->tnotification->run() !== FALSE) {
            // field id
            $field_id = $this->m_delegasi->get_new_id();
            // params
            $params = array(
                "field_id"          => $field_id,
                "user_id"           => $this->input->post('user_id'),
                "delegation_reason" => $this->input->post('delegation_reason'),
                "delegation_st"     => "open",
                "mdb"               => $this->com_user['user_id'],
                "mdd"               => date("Y-m-d H:i:s"),
            );
            $this->m_delegasi->insert($params);
            // insert role direktur
            $params = array(
                "role_id"   => 45,
                "user_id"   => $this->input->post('user_id'),
            );
            $this->m_delegasi->insert_role_user($params);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        //default redirect
        redirect("pengaturan/delegasi/add");
    }

    // edit users
    public function edit($field_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set templates
        $this->smarty->assign("template_content", "pengaturan/delegasi/edit.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        $this->smarty->load_style("select2/select2.css");
        // load sub direktorat
        $this->smarty->assign("rs_kasubdit", $this->m_delegasi->get_all_operator_kasubdit());
        // get detail delegasi by id
        $result = $this->m_delegasi->get_delegasi_detail_by_id($field_id);
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
        $this->tnotification->set_rules('user_id', 'Kasubdit', 'trim|required');
        $this->tnotification->set_rules('delegation_reason', 'Alasan Pendelegasian', 'trim');
        // proses
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                "user_id"           => $this->input->post('user_id'),
                "delegation_reason" => $this->input->post('delegation_reason'),
                "mdb"               => $this->com_user['user_id'],
                "mdd"               => date("Y-m-d H:i:s"),
            );
            $where = array(
                "field_id"          => $this->input->post('field_id'),
            );
            // update
            if ($this->m_delegasi->update($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/delegasi/edit/" . $this->input->post('field_id'));
    }

    // process hapus
    public function delete_process($field_id = "", $user_id = "") {
        $params = array(
            "field_id"  => $field_id
        );
        // delete
        if ($this->m_delegasi->delete($params)) {
            // delete
            $params = array(
                "role_id"   => 45,
                "user_id"   => $user_id,
            );
            $this->m_delegasi->delete_com_role($params);
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
            // default redirect
            redirect("pengaturan/delegasi/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/delegasi/delete/" . $field_id);
    }

}
