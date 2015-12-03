<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class fa_aubndn_staff extends ApplicationBase {

    private $flow_id = 51;
    private $next_id = 52;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_task');
        $this->load->model('m_airlines');
        $this->load->model('m_airport');
        $this->load->model('m_files');
        $this->load->model('m_email');
        // load library
        $this->load->library('tnotification');
    }

    // detail
    public function index($data_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "task/fa_aubndn_staff/detail.html");
        // get detail task
        $task = $this->m_task->get_detail_my_task_by_id(array($data_id, $this->com_user['role_id']));
        if (empty($task)) {
            redirect('task/manager');
        }
        $this->smarty->assign("task", $task);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // get detail data
        $params = array($data_id, $task['flow_id']);
        $result = $this->m_task->get_data_by_id($params);
        $this->smarty->assign("result", $this->m_task->get_data_by_id($params));
        $this->smarty->assign("result_rute", $this->m_task->get_data_rute_by_id($params));
        $this->smarty->assign("total_rute", COUNT($this->m_task->get_data_rute_by_id($params)));
        $this->smarty->assign("no", 1);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($data_id)));
        // files
        $files = $this->m_files->get_list_file_download(array($data_id));
        $this->smarty->assign("rs_files", $files);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_task->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        /*
         * Action Control
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $result['data_type'], $result['data_flight'], $result['services_cd']));
        $this->smarty->assign("action", $action);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('document_no', 'Nomor Dokumen', 'trim|required|maxlength[35]');
        $this->tnotification->set_rules('airlines_id', 'Operator Penerbangan', 'trim|required');
        $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('registration', 'Tanda Pendaftaran', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('flight_no', 'Nama Panggilan', 'trim|required');
        $this->tnotification->set_rules('date_start', 'Tanggal', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('date_end', 'Tanggal', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('waktu', 'Waktu', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('rute_from', 'Rute Asal', 'trim|required');
        $this->tnotification->set_rules('rute_to', 'Rute Tujuan', 'trim|required');
        $this->tnotification->set_rules('technical_landing', 'Pendaratan Teknis Di', 'trim');
        $this->tnotification->set_rules('niaga_landing', 'Pendaratan Niaga Di', 'trim');
        $this->tnotification->set_rules('flight_pilot', 'Nama Pilot', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('flight_crew', 'Awak Pesawat Udara Lainnya', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('flight_goods', 'Penumpang/Barang', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('remark', 'Keterangan', 'trim|required|maxlength[50]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $data_id = $this->input->post('data_id');
            $params = array(
                "document_no" => $this->input->post('document_no'),
                "airlines_id" => $this->input->post('airlines_id'),
                "data_type" => 'berjadwal',
                "data_flight" => 'domestik',
                "aircraft_type" => $this->input->post('aircraft_type'),
                "registration" => $this->input->post('registration'),
                "country" => 'INDONESIA',
                "flight_no" => $this->input->post('flight_no'),
                "date_start" => $this->input->post('date_start'),
                "date_end" => $this->input->post('date_end'),
                "waktu" => $this->input->post('waktu'),
                "rute_from" => $this->input->post('rute_from'),
                "rute_to" => $this->input->post('rute_to'),
                "rute_all" => $this->input->post('rute_from') . '-' . $this->input->post('rute_to'),
                "technical_landing" => $this->input->post('technical_landing'),
                "niaga_landing" => $this->input->post('niaga_landing'),
                "flight_pilot" => $this->input->post('flight_pilot'),
                "flight_crew" => $this->input->post('flight_crew'),
                "flight_goods" => $this->input->post('flight_goods'),
                "remark" => $this->input->post('remark'),
                "mdb" => $this->com_user['user_id']
            );
            // insert
            if ($this->m_task->update($params, $data_id)) {
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
        redirect("task/fa_aubndn_staff/index/" . $data_id);
    }

    // process notes
    public function catatan_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('process_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('notes', 'Catatan', 'trim|maxlength[5000]');
        // id
        $data_id = $this->input->post('data_id');
        $process_id = $this->input->post('process_id');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                "catatan" => $this->input->post('notes')
            );
            // where
            $where = array('data_id' => $data_id, 'process_id' => $process_id);
            // update
            if ($this->m_task->update_process($params, $where)) {
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
        redirect("task/fa_aubndn_staff/index/" . $data_id);
    }

    // process remark
    public function remark_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('catatan', 'Catatan', 'trim|maxlength[5000]');
        // id
        $data_id = $this->input->post('data_id');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                "remark_final" => $this->input->post('catatan'),
                "remark_by" => $this->com_user['user_id'],
                "remark_date" => date('Y-m-d H:i:s')
            );
            // where
            $where = array('data_id' => $data_id);
            // update
            if ($this->m_task->update($params, $where)) {
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
        redirect("task/fa_aubndn_staff/index/" . $data_id);
    }

    // send process
    public function send_process($data_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $task = $this->m_task->get_detail_my_task_by_id(array($data_id, $this->com_user['role_id']));
        if (empty($task)) {
            redirect('task/manager');
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            $next_flow = ($task['flow_id'] + 1);
            $process_id = $this->m_task->get_process_id();
            // process flow
            $params = array($process_id, $data_id, $next_flow, $this->com_user['user_id']);
            $this->m_task->insert_process($params);
            // get role next flow
            $next_role = $this->m_task->get_role_next_from(array($next_flow));
            // send mail to next flow
            $this->m_email->mail_to_next_flow($data_id, $next_role['role_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/manager/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/fa_aubndn_staff/index/" . $data_id);
    }

    // pending process
    public function pending_process($data_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $task = $this->m_task->get_detail_my_task_by_id(array($data_id, $this->com_user['role_id']));
        if (empty($task)) {
            redirect('task/manager');
        }
        // update
        $params = array('reject', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            $next_flow = 95; // member flow;
            $process_id = $this->m_task->get_process_id();
            // process flow
            $params = array($process_id, $data_id, $next_flow, $this->com_user['user_id']);
            $this->m_task->insert_process($params);
            // send mail
            $this->m_email->mail_pending_reject($data_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/manager/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/fa_aubndn_staff/index/" . $data_id);
    }

    // reject process
    public function reject_process($data_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $task = $this->m_task->get_detail_my_task_by_id(array($data_id, $this->com_user['role_id']));
        if (empty($task)) {
            redirect('task/manager');
        }
        // update
        $params = array('reject', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // update done
            $params = array('rejected', $data_id);
            $this->m_task->done_process($params);
            // send mail
            $this->m_email->mail_pending_reject($data_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/manager/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/fa_aubndn_staff/index/" . $data_id);
    }

}
