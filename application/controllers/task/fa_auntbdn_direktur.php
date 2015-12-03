<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class fa_auntbdn_direktur extends ApplicationBase {

    private $prev_id = 33;
    private $flow_id = 34;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_task');
        $this->load->model('m_airlines');
        $this->load->model('m_airport');
        $this->load->model('m_published');
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
        $this->smarty->assign("template_content", "task/fa_auntbdn_direktur/detail.html");
        // get detail task
        $task = $this->m_task->get_detail_my_task_by_id(array($data_id, $this->com_user['role_id']));
        if (empty($task)) {
            redirect('task/manager');
        }
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
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
            $this->m_email->mail_reject($data_id);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
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
            $next_flow = 93; // member flow;
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
    }

    // back process
    public function back_process($data_id = "", $process_id = "") {
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
            $next_flow = ($task['flow_id'] - 1);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
    }

    // finish process
    public function finish_process($data_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $task = $this->m_task->get_detail_my_task_by_id(array($data_id, $this->com_user['role_id']));
        if (empty($task)) {
            redirect('task/manager');
        }
        // get services payment
        $payment_services = $this->m_task->get_payment_services(array($task['data_type'], $task['data_flight'], $task['services_cd']));
        $trim = "5";
        // get fa group
        $group = $this->m_task->get_fa_group('3');
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // total registration
            $total_regis = $task['registration_total'];
            // get all regis
            $result = $this->m_task->get_all_regis(array($task['data_id']));
            foreach ($result as $value) {
                // cek nomor fa
                if ($value['published_no'] == "") {
                    // get last dokumen id
                    $params = array($task['data_type'], $task['data_flight'], date('Y'), '%' . $group . '%');
                    $nomor_dokumen_terbit = $this->m_task->get_nomor_dokumen_terbit($params, $trim);
                    $nomor_dokumen_terbit = $nomor_dokumen_terbit . "/" . strtoupper($group) . "/DAU/" . date('dm') . "/" . date('Y');
                    $data = array('approved', $nomor_dokumen_terbit, $payment_services['services_payment'], $this->com_user['user_id'], $value['data_id']);
                    // update done
                    $this->m_task->done_process_all($data);
                    // create pdf file
                    $this->create_pdf($value['data_id']);
                } else {
                    $data = array('approved', $value['published_no'], $payment_services['services_payment'], $this->com_user['user_id'], $value['data_id']);
                    // update done
                    $this->m_task->done_process_all($data);
                    // create pdf file
                    $this->create_pdf($value['data_id']);
                }
            }
            // kirim email dan cetak pdf persetujuan ( buatkan di model saja )
            $this->m_email->mail_finish_process($data_id);
            $this->m_email->mail_to_stakeholder($data_id);
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
        redirect("task/fa_auntbdn_direktur/index/" . $data_id);
    }

    // create pdf
    public function create_pdf($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // load library
        $this->load->library('tcpdf');

        // get detail data
        $params = array($data_id);
        $result = $this->m_published->get_detail_data_by_id($params);
        $result_rute = $this->m_published->get_data_rute_by_id(array($data_id));
        $total_rute = COUNT($result_rute);
        $no = 1;

        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);

        // add a page
        $this->tcpdf->AddPage();

        $html = '';
        $no = 1;

        // content
        $html .= '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: left;
                font-family: tahoma;
                font-size: 20px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 20px;
            }
            </style>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="40%">
                        <span style="text-decoration: underline;">Kementerian Perhubungan Republik Indonesia</span><br />
                        <i>Ministry of Transportation Of the Republic of Indonesia</i>
                    </td>
                    <td rowspan="3" width="25%" align="center"><br><br><br><br><b style="font-size:30px;">FLIGHT APPROVAL</b></td>
                    <td rowspan="3" width="35%" align="center">
                        <br><br><br>
                        <b>' . strtoupper($result['data_type']) . ' ' . strtoupper($result['data_flight']) . '</b>
                        <br><br>
                        <b>' . strtoupper($result['document_no']) . '</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="text-decoration: underline;">Direktorat Jenderal Perhubungan Udara </span><br />
                        <i>Directorate General of Civil Aviation</i>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="text-decoration: underline;">Persetujuan terbang untuk wilayah Indonesia</span><br />
                        <i>Flight Approval for Indonesia territory</i>
                    </td>
                </tr>
            </table>
            <br>
        ';
        if ($result['data_flight'] == 'domestik') {
        $html .= '
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="3%">1.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Pesawat Udara</span> <br /><i>Aircraft</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Operator (Pemilik / Penyewa)</span><br /><i>Operator (Owner / Charterer)</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["airlines_nm"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Tipe</span><br /><i>Type</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["aircraft_type"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanda Pendaftaran dan Nama Panggilan</span><br /><i>Registrations and Call Signs</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_no"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">2.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Penerbangan </span> <br /><i>Flight</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Tanggal dan Jam</span><br /><i>Date and Time</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $this->datetimemanipulation->get_full_date($result["date_start"]) . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $this->datetimemanipulation->get_full_date($result["date_end"]) . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Rute</span><br /><i>Routes</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["rute_all"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Teknis di</span><br /><i>Technical Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["technical_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>d)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Niaga di</span><br /><i>Commercial Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["niaga_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">3.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Jumlah orang dalam Pesawat udara</span> <br /><i>Total number of person on board</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["flight_pilot"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Awak pesawat udara lainnya</span> *1)<br /><i>Other crew members</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_crew"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Penumpang/ Barang </span> *2)<br /><i>Passengers / Cargo</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_goods"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">4.</td>
                    <td colspan="2"><span style="text-decoration: underline;">Keterangan</span> <br /><i>Remark</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["remark"] . '
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="3">
                        <span style="text-decoration: underline;">Berlaku untuk 1 (satu) kali penerbangan</span>
                        <br />
                        <i>Valid for one flight </i>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="3" style="vertical-align:top; padding-left:40px;">';
                        if ($result["catatan"] != "") {
                            $html .= $result["catatan"] . '<br /><br />';
                        }
                        if ($result["remark_final"] != "") {
                            $html .= $result["remark_final"] . '<br /><br />';
                        }
                $html .= '
                        <table style="margin-left:-10px;">';
                        foreach ($remark_field as $value) {
                            $html .= '<tr>
                            <td width="35%">' . $value["rules_name"] . '</td>
                            <td width="65%">' . $result[$value["rules_field"]] . '</td>
                            </tr>';
                        }
                $html .= '</table>
                    </td>
                    <td rowspan="3" width="10%">
                        <span style="text-decoration: underline;">Pemohon</span><br /><i>Applicant</i>
                    </td>
                    <td width="10%"> 
                        <span style="text-decoration: underline;">Tandatangan </span><br /><i>Signature</i>
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td>
                        ' . $result["applicant"] . '
                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Penunjukan  </span><br /><i>Designation</i>
                    </td>
                    <td>
                        ' . $result["designation"] . '
                    </td>
                </tr>
            </table>
            <br>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="50%" align="justify">
                        Nota :<br />
                        *1) dan *2) Nama-nama supaya dicantumkan / dilampirkan<br />
                        Pesawat udara, awak pesawat udara, penumpang, dan muatan berdasarkan pada istilah dari Konvensi Chicago serta mentaati peraturan-peraturan Indonesia mengenai penerbangan ini. Memiliki persetujuan terbang ini tidak membebaskan operator dari melaksanakan setiap aturan operasi teknis atau persyaratan kelaikan udara dari Direktorat Jenderal Perhubungan Udara. Persetujuan terbang ini dapat dicabut tanpa pemberitahuan terlebih dahulu. Apabila terjadi keterlambatan pada tanggal tersebut dalam butir 2a) diatas, maka penerbangan dianggap batal.    
                    </td>
                    <td width="2%"></td>
                    <td width="50%" align="justify">
                        Notes :<br />
                        *1) and *2) Names should be written/attached <br />
                        Aircraft, crew, passengers and load are subject to the terms of Chicago Convention and have to comply with the Indonesian Regulations, concerning this flight. Posession of this flight approval does not exempt an operator from compliance with any of the technical operations ruler or airworthines requirement of the Directorate General of Civil Aviation. This Flight approval can be withdrawn without previous notice. Should delay exceed the date as prescribed in point 2a) above this flight will be regarded as cancelled.
                    </td>
                </tr>
            </table>
        ';
        } else {
        $html .= '
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="3%">1.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Pesawat Udara</span> <br /><i>Aircraft</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Operator (Pemilik / Penyewa)</span><br /><i>Operator (Owner / Charterer)</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["airlines_nm"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Tipe</span><br /><i>Type</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["aircraft_type"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanda Pendaftaran dan Nama Panggilan</span><br /><i>Registrations and Call Signs</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_no"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">2.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Penerbangan </span> <br /><i>Flight</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Rute</span><br /><i>Routes</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["rute_all"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Tanggal Masuk Indonesia</span><br /><i>Date Entering Indonesia</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $this->datetimemanipulation->get_full_date($result["date_start"]) . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $this->datetimemanipulation->get_full_date($result["date_start_upto"]) . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanggal Keluar Indonesia</span><br /><i>Date Leaving Indonesia</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $this->datetimemanipulation->get_full_date($result["date_end"]) . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $this->datetimemanipulation->get_full_date($result["date_end_upto"]) . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>d)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Teknis di</span><br /><i>Technical Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["technical_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>e)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Niaga di</span><br /><i>Commercial Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["niaga_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>f)</td>
                    <td><span style="text-decoration: underline;">Sifat / Tujuan Penerbangan</span><br /><i>Purpose Of The Flight</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_purpose"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">g)</td>
                    <td width="28%"><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["flight_pilot"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>h)</td>
                    <td><span style="text-decoration: underline;">Awak pesawat udara lainnya</span> *1)<br /><i>Other crew members</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_crew"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>i)</td>
                    <td><span style="text-decoration: underline;">Penumpang/ Barang </span> *2)<br /><i>Passengers / Cargo</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_goods"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">3.</td>
                    <td colspan="2"><span style="text-decoration: underline;">Keterangan</span> <br /><i>Remark</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["remark"] . '
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="3">
                        <span style="text-decoration: underline;">Berlaku untuk 1 (satu) kali penerbangan</span>
                        <br />
                        <i>Valid for one flight </i>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="3" style="vertical-align:top; padding-left:40px;">';
                        if ($result["catatan"] != "") {
                            $html .= $result["catatan"] . '<br /><br />';
                        }
                        if ($result["remark_final"] != "") {
                            $html .= $result["remark_final"] . '<br /><br />';
                        }
                $html .= '
                        <table style="margin-left:-10px;">';
                        foreach ($remark_field as $value) {
                            $html .= '<tr>
                            <td width="35%">' . $value["rules_name"] . '</td>
                            <td width="65%">' . $result[$value["rules_field"]] . '</td>
                            </tr>';
                        }
                $html .= '</table>
                    </td>
                    <td rowspan="3" width="10%">
                        <span style="text-decoration: underline;">Pemohon</span><br /><i>Applicant</i>
                    </td>
                    <td width="10%"> 
                        <span style="text-decoration: underline;">Tandatangan </span><br /><i>Signature</i>
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td>
                        ' . $result["applicant"] . '
                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Penunjukan  </span><br /><i>Designation</i>
                    </td>
                    <td>
                        ' . $result["designation"] . '
                    </td>
                </tr>
            </table>
            <br>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="49%" align="justify">
                        Nota :<br />
                        *1) dan *2) Nama-nama supaya dicantumkan / dilampirkan<br />
                        Pesawat udara, awak pesawat udara, penumpang, dan muatan berdasarkan pada istilah dari Konvensi Chicago serta mentaati peraturan-peraturan Indonesia mengenai penerbangan ini. Memiliki persetujuan terbang ini tidak membebaskan operator dari melaksanakan setiap aturan operasi teknis atau persyaratan kelaikan udara dari Direktorat Jenderal Perhubungan Udara. Persetujuan terbang ini dapat dicabut tanpa pemberitahuan terlebih dahulu. Apabila terjadi keterlambatan pada tanggal tersebut dalam butir 2a) diatas, maka penerbangan dianggap batal.    
                    </td>
                    <td width="2%"></td>
                    <td width="49%" align="justify">
                        Notes :<br />
                        *1) and *2) Names should be written/attached <br />
                        Aircraft, crew, passengers and load are subject to the terms of Chicago Convention and have to comply with the Indonesian Regulations, concerning this flight. Posession of this flight approval does not exempt an operator from compliance with any of the technical operations ruler or airworthines requirement of the Directorate General of Civil Aviation. This Flight approval can be withdrawn without previous notice. Should delay exceed the date as prescribed in point 2a) above this flight will be regarded as cancelled.
                    </td>
                </tr>
            </table>
        ';
        }
        $html .= '
            <br>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td colspan="2">
                        <span style="text-decoration: underline;">Penerbangan tidak berjadwal tersebut di atas telah diizinkan oleh Pemerintah Republik Indonesia   </span><br /><i>The above mentioned non scheduled flight has been approved by the Goverment of the Republic of Indonesia</i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        A.n. Direktur Jenderal Perhubungan Udara
                    </td>
                </tr>
                <tr>
                    <td width="15%">Nomor Izin</td>
                    <td width="85%">: ' . $result['published_no'] . '</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: ' . $result['mdd'] . '</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>: ' . $result['operator_name'] . '</td>
                </tr>
                <tr>
                    <td>Tanda Tangan</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: ' . $result['jabatan'] . '</td>
                </tr>
            </table>
        ';

        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $result['published_no'];
        $this->tcpdf->Output('resource/doc/published/' . str_replace("/", "-", $filename) . ".pdf", 'F');
    }

}
