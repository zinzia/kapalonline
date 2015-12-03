<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class pending extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_member');
        $this->load->model('m_task');
        $this->load->model('m_pending');
        $this->load->model('m_pending');
        $this->load->model('m_block');
        $this->load->model('m_files');
        $this->load->model('m_email');
        $this->load->model('m_airport');
        $this->load->model('m_registration');
        // load library
        $this->load->library('tnotification');
        $this->load->library("score");
    }

    // routes
    public function index() {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "member/pending/index.html");
        // list waiting
        $rs_id = $this->m_member->get_list_pending_task_waiting(array($this->com_user['role_id'], $this->com_user['airlines_id']));
        foreach ($rs_id as $value) {
            // get detail rute
            $rs_rute = $this->m_member->get_data_rute_by_id(array($value['data_id']));
            $total_rute = COUNT($rs_rute);
            $list_rute = "";
            $x = 1;
            foreach ($rs_rute as $rute) {
                $x++;
                $list_rute .= $rute['airport_iata_cd'];
                if ($x <= $total_rute) {
                    $list_rute .= "-";
                }
            }
            $data[] = array(
                "data_id"       => $value['data_id'],
                "document_no"   => $value['document_no'],
                "data_type"     => $value['data_type'],
                "data_flight"   => $value['data_flight'],
                "date_start"    => $value['date_start'],
                "date_end"      => $value['date_end'],
                "rute_all"      => $list_rute,
                "services_nm"   => $value['services_nm'],
                "task_nm"       => $value['task_nm'],
                "selisih_hari"  => $value['selisih_hari'],
                "selisih_waktu" => $value['selisih_waktu'],
                "group_link"    => $value['group_link'],
            );
        }
        $this->smarty->assign("rs_id", $rs_id);
        // $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // detail
    public function detail($group_id = "", $data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // get group link
        $group = $this->m_pending->get_detail_group_by_id(array($group_id));
        if (empty($group)) {
            redirect('member/pending');
        }
        $length = intval(strlen($group['group_link'])) - 2;
        $group_link = substr($group['group_link'], 0, $length);
        // default redirect
        redirect('member/pending/' . $group_link . '/' . $data_id);
    }

    // aunbdn
    public function aunb($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // assign
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_pending->get_services_cd(array($result['data_type'], $result['data_flight'])));
        // airlines flight type
        $rs_flight_type = explode("_", $this->com_user['airlines_flight_type']);
        $this->smarty->assign("rs_flight_type", $rs_flight_type);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function aunb_add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // data
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "services_cd" => $this->input->post('services_cd')
            );
            // where
            $where = array(
                "data_id" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id']
            );
            // insert
            if ($this->m_pending->update($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("member/pending/aunb_form/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aunb_add/" . $this->input->post('data_id'));
    }

    // form fa
    public function aunb_form($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/form.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // assign
        $result['data_flight'] = empty($result['data_flight']) ? 'domestik' : $result['data_flight'];
        // var_dump($result);die;
        $this->smarty->assign("result", $result);
        // --
        $this->smarty->assign("rs_airlines", $this->m_pending->get_all_airlines());
        $rs_airport = $this->m_pending->get_all_airport();
        $this->smarty->assign("rs_airport", $rs_airport);
        // data
        $data = "";
        foreach ($rs_airport as $value) {
            $data .= "{ id: '" . $value['airport_iata_cd'] . "', text: '" . $value['airport_iata_cd'] . " | " . $value['airport_nm'] . "'},";
        }
        $this->smarty->assign("data", $data);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_pending->get_all_service_code());
        // get hari pengajuan
        $this->smarty->assign("hari_pengajuan", $this->m_pending->get_hari_pengajuan(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // get remark field
        // $this->smarty->assign("remark_field", $this->m_pending->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // get izin rute
        $this->smarty->assign("rs_rute", $this->m_registration->get_izin_rute(array($result['airlines_id'], $result['data_flight'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    // modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason modified: add new step for slot time assignment
    public function aunb_form_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('registration', 'Tanda Pendaftaran', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('call_sign', 'Nama Panggilan', 'trim|required');
        // $this->tnotification->set_rules('flight_no', 'Nomor Keberangkatan', 'trim|required');
        // domestik / internasional
        $data_flight = $this->input->post('data_flight');
        if ($data_flight == 'domestik') {
            $this->tnotification->set_rules('date_start', 'Tanggal Datang (Arrival)', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end', 'Tanggal Pergi (Departure)', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('waktu', 'Waktu', 'trim|maxlength[5]');
        } else {
            $this->tnotification->set_rules('date_start', 'Tanggal Masuk Indonesia ( dari )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_start_upto', 'Tanggal Masuk Indonesia ( sampai )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end', 'Tanggal Keluar Indonesia ( dari )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end_upto', 'Tanggal Keluar Indonesia ( sampai )', 'trim|required|maxlength[10]');
        }
        // --
        $this->tnotification->set_rules('rute_all', 'Rute', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('technical_landing', 'Pendaratan Teknis Di', 'trim');
        $this->tnotification->set_rules('niaga_landing', 'Pendaratan Niaga Di', 'trim');
        $this->tnotification->set_rules('flight_purpose', 'Tujuan Penerbangan', 'trim|maxlength[50]');
        $this->tnotification->set_rules('flight_pilot', 'Nama Pilot', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('flight_crew', 'Awak Pesawat Udara Lainnya', 'trim|required|maxlength[50]');
        $services_cd = $this->input->post('services_cd');
        if ($services_cd != "F") {
            if ($services_cd != "P") {
                $this->tnotification->set_rules('flight_goods', 'Penumpang/Barang', 'trim|required|maxlength[50]');
            }
        }
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        $this->tnotification->set_rules('catatan', 'Keterangan', 'trim|maxlength[255]');
        $this->tnotification->set_rules('notes', 'Catatan', 'trim');
        $this->tnotification->set_rules('applicant', 'Pemohon', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('designation', 'Penunjukan', 'trim|required|maxlength[50]');
        $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
        $result = $this->m_pending->get_detail_data_by_id($params);
        // $rs_remark = $this->m_pending->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        // foreach ($rs_remark as $value) {
        //     $this->tnotification->set_rules($value['rules_field'], $value['rules_name'], 'trim|required|maxlength[' . $value['rules_length'] . ']');
        // }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // clear input
            if ($data_flight == 'domestik') {
                $waktu = $this->input->post('waktu');
                $waktu = empty($waktu) ? NULL : $waktu;
                $date_start = $this->input->post('date_start');
                $date_end = $this->input->post('date_end');
                $date_start_upto = NULL;
                $date_end_upto = NULL;
                $flight_purpose = NULL;
            } else {
                $waktu = NULL;
                $date_start = $this->input->post('date_start');
                $date_end = $this->input->post('date_end');
                $date_start_upto = $this->input->post('date_start_upto');
                $date_end_upto = $this->input->post('date_end_upto');
                $flight_purpose = $this->input->post('flight_purpose');
            }
            // params
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "aircraft_type" => $this->input->post('aircraft_type'),
                "registration" => $this->input->post('registration'),
                "country" => 'INDONESIA',
                "call_sign" => $this->input->post('call_sign'),
                // "flight_no" => $this->input->post('flight_no'),
                // "flight_no_2" => $this->input->post('flight_no_2'),
                "date_start" => $date_start,
                "date_start_upto" => $date_start_upto,
                "date_end" => $date_end,
                "date_end_upto" => $date_end_upto,
                "waktu" => $waktu,
                "rute_all" => $this->input->post('rute_all'),
                "technical_landing" => $this->input->post('technical_landing'),
                "niaga_landing" => $this->input->post('niaga_landing'),
                "flight_purpose" => $flight_purpose,
                "flight_pilot" => $this->input->post('flight_pilot'),
                "flight_crew" => $this->input->post('flight_crew'),
                "flight_goods" => ($this->input->post('flight_goods') == "") ? null : $this->input->post('flight_goods'),
                "services_cd" => $this->input->post('services_cd'),
                "remark" => $this->input->post('remark'),
                // "nomor_slot" => $this->input->post('nomor_slot'),
                // "nomor_izin" => $this->input->post('nomor_izin'),
                // "nomor_prinsip" => $this->input->post('nomor_prinsip'),
                // "nomor_kontrak" => $this->input->post('nomor_kontrak'),
                // "nomor_pengadaan" => $this->input->post('nomor_pengadaan'),
                // "nomor_dkuppu" => $this->input->post('nomor_dkuppu'),
                "eta" => "",
                "eta_utc" => "",
                "eta_2" => "",
                "eta_2_utc" => "",
                "etd" => "",
                "etd_utc" => "",
                "etd_2" => "",
                "etd_2_utc" => "",
                "catatan" => $this->input->post('catatan'),
                "applicant" => $this->input->post('applicant'),
                "designation" => $this->input->post('designation'),
                "data_completed" => '0',
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s')
            );
            $where = array(
                "data_id" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update
            if ($this->m_pending->update($params, $where)) {
                $this->m_pending->delete_rute(array($this->input->post('data_id')));
                // update rute batch
                $id = $this->m_pending->get_data_id();
                $rute_all = $this->input->post('rute_all');
                $rute_all = explode(",", $rute_all);
                $x = 1;
                foreach ($rute_all as $value) {
                    $data[] = array(
                        "id" => $id . $x,
                        "data_id" => $this->input->post('data_id'),
                        "seq" => $x++,
                        "airport_id" => $value
                    );
                }
                $this->m_pending->update_rute($data);
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                // redirect("member/pending/aunb_files/" . $this->input->post('data_id'));
                redirect("member/pending/aunb_rute_fa/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aunb_form/" . $this->input->post('data_id'));
    }

    // rute fa
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason: add sync process with SCORE slot time database using SOA step 1
    public function aunb_rute_fa($data_id = "", $mode = "") {
        // set page rules
        $this->_set_page_rule("C");
        
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['rute_all']);
        $params = array(trim($orig));
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $params = array(trim($dest));
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);
        // setup filter by Service Code: Charter Flight (C), Extra Flight (G) >> if no row >> manual entry slot
        $rules_sc = array("C","G");
        $is_filter_by_rule_sc = in_array($result['services_cd'], $rules_sc);
        // SCORE Service
        $this->smarty->assign("mode", $mode);
        if ($is_filter_by_rule_sc && $mode != "form" && ($rs_airport1['is_used_score'] == 1 || $rs_airport2['is_used_score'] == 1)){
            $received = true;
            try {
                $client = new SCORE_Service();
                //request
                if ($result['data_flight'] == "domestik") {
                    $date_start = str_replace("-","",$result['date_start']);
                    $date_end = str_replace("-","",$result['date_end']);
                } else {
                    $date_start = str_replace("-","",$result['date_start']);
                    $date_end = str_replace("-","",$result['date_start_upto']);
                }
                if ($date_end == "") $date_end=$date_start;
                if ($date_start == "") $date_start=$date_end;
                $params = new getConfirmedSlot($this->com_user['airlines_iata_cd'], $orig, $dest, $date_start, $date_end, "*");
                $score = $client->getConfirmedFullSlot($params, @intval($rs_airport1['airport_utc']), @intval($rs_airport2['airport_utc']));
            } catch (Exception $error) {
                if (ENVIRONMENT == "testing") {
                    var_dump($error); die;
                }
                unset($score);
                $received = false;
            }            
            if (!is_array($score)){
                $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/form_next_def.html");
            } else {
                $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/form_next.html");
                $this->smarty->assign("score", $score);
            }
        } else {
            $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/form_next_def.html");
        }
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // assign
        $result['eta'] = substr($result['eta'], 0, 5);
        $result['eta_utc'] = substr($result['eta_utc'], 0, 5);
        $result['etd'] = substr($result['etd'], 0, 5);
        $result['etd_utc'] = substr($result['etd_utc'], 0, 5);
        $result['eta_2'] = substr($result['eta_2'], 0, 5);
        $result['eta_2_utc'] = substr($result['eta_2_utc'], 0, 5);
        $result['etd_2'] = substr($result['etd_2'], 0, 5);
        $result['etd_2_utc'] = substr($result['etd_2_utc'], 0, 5);
        $result['is_used_score'] = $rs_airport1['is_used_score'] && $is_filter_by_rule_sc && is_array($score);
        $result['is_used_score_2'] = $rs_airport2['is_used_score'] && $is_filter_by_rule_sc && is_array($score);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("orig", $orig);
        $this->smarty->assign("dest", $dest);
        // var_dump($result);die;
        // get remark field
        $this->smarty->assign("remark_field", $this->m_registration->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // var_dump($result);die;
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // form next
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason: add sync process with SCORE slot time database using SOA step 2
    public function aunb_rute_fa_process(){
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        $data_id = $this->input->post('data_id');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // assign
        $this->smarty->assign("result", $result);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_registration->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));

        // rute wajib diisi
        $this->tnotification->set_rules('pairing', 'required');
        $this->tnotification->set_rules('slot_selected', 'required');
        $this->tnotification->set_rules('aircraft_type[]', 'Tipe Pesawat', 'trim|required');
        $this->tnotification->set_rules('rute_all[]', 'Rute', 'trim|required');
        $this->tnotification->set_rules('flight_no[]', 'Nomor Penerbangan', 'trim|required');
        $this->tnotification->set_rules('etd[]', 'ETD', 'trim');
        $this->tnotification->set_rules('eta[]', 'ETA', 'trim');
        $this->tnotification->set_rules('etd_utc[]', 'ETD', 'trim');
        $this->tnotification->set_rules('eta_utc[]', 'ETA', 'trim');
        $this->tnotification->set_rules('dos[]', 'DOS', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $pairing = $this->input->post('pairing');
            $slot_selected = $this->input->post('slot_selected');
            $rute_all = $this->input->post('rute_all');
            $aircraft_type = $this->input->post('aircraft_type');
            $izin_start_date = $this->input->post('izin_start_date');
            $izin_expired_date = $this->input->post('izin_expired_date');
            $flight_no = $this->input->post('flight_no');
            $etd = $this->input->post('etd');
            $eta = $this->input->post('eta');
            $etd_utc = $this->input->post('etd_utc');
            $eta_utc = $this->input->post('eta_utc');
            $dos = $this->input->post('dos');
            $index = $slot_selected - 1;
            $schedule = array(
                "row_selected" => $slot_selected,
                "aircraft_type" => $aircraft_type[$index],
                "izin_start_date" => $izin_start_date[$index],
                "izin_expired_date" => $izin_expired_date[$index],
                "dos" => $dos[$index],
                "pairing" => $pairing,
                "rute_all" => $rute_all[$index],
                "flight_no" => $flight_no[$index],
                "etd" => $etd[$index],
                "eta" => $eta[$index],
                "etd_utc" => $etd_utc[$index],
                "eta_utc" => $eta_utc[$index]
            );
            if ($pairing == "VV"){
                $score = array();
                // rebuild SCORE list
                foreach ($flight_no as $k => $v) {
                    if ($schedule['flight_no'] == $flight_no[$k]) continue;
                    if ($schedule['dos'] != $dos[$k]) continue;
                    // if ($schedule['izin_start_date'] != $izin_start_date[$k]) continue;
                    // if ($schedule['izin_expired_date'] != $izin_expired_date[$k]) continue;
                    if ($schedule['rute_all'] == $rute_all[$k]) continue;
                    $score[] = array(
                        'aircraft_type' => $aircraft_type[$k],
                        'izin_start_date' => $izin_start_date[$k],
                        'izin_expired_date' => $izin_expired_date[$k],
                        'dos' => $dos[$k],
                        'rute_all' => $rute_all[$k],
                        'flight_no' => $flight_no[$k],
                        'etd' => $etd[$k],
                        'eta' => $eta[$k],
                        'etd_utc' => $etd_utc[$k],
                        'eta_utc' => $eta_utc[$k]
                        );
                }
                // show next step
                $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/form_next_vv.html");
                // load javascript
                $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
                $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
                $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
                // load style ui
                $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
                $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
                $this->smarty->load_style("select2/select2.css");
                $this->smarty->assign("schedule", $schedule);
                $this->smarty->assign("score", $score);
                // notification
                $this->tnotification->display_notification();
                $this->tnotification->display_last_field();
                // output
                parent::display();
            } elseif ($pairing == "OW"){
                $params = array(
                    "aircraft_type" => $schedule['aircraft_type'],
                    "flight_no" => $schedule['flight_no'],
                    // "flight_no_2" => $this->input->post('flight_no_2'),
                    // "nomor_slot" => $this->input->post('nomor_slot'),
                    // "nomor_slot_2" => $this->input->post('nomor_slot_2'),
                    // "nomor_izin" => $this->input->post('nomor_izin'),
                    // "nomor_prinsip" => $this->input->post('nomor_prinsip'),
                    // "nomor_kontrak" => $this->input->post('nomor_kontrak'),
                    // "nomor_kontrak_2" => $this->input->post('nomor_kontrak_2'),
                    // "nomor_pengadaan" => $this->input->post('nomor_pengadaan'),
                    // "nomor_dkuppu" => $this->input->post('nomor_dkuppu'),
                    // "eta" => $this->input->post('eta'),
                    // "eta_utc" => $this->input->post('eta_utc'),
                    // "eta_2" => $this->input->post('eta_2'),
                    // "eta_2_utc" => $this->input->post('eta_2_utc'),
                    // "etd" => $this->input->post('etd'),
                    // "etd_utc" => $this->input->post('etd_utc'),
                    // "etd_2" => $this->input->post('etd_2'),
                    // "etd_2_utc" => $this->input->post('etd_2_utc'),
                    "data_completed" => '1',
                    "mdb" => $this->com_user['user_id'],
                    "mdd" => date('Y-m-d H:i:s'),
                    'pairing' => "OW",
                    'is_used_score' => 1
                );
                if ($schedule['etd'] != "") {
                    $params['etd']=$schedule['etd'];
                    $params['etd_utc']=$schedule['etd_utc'];
                } else $params['is_used_score']=2;
                if ($schedule['eta'] != "") {
                    $params['eta']=$schedule['eta'];
                    $params['eta_utc']=$schedule['eta_utc'];
                } else $params['is_used_score']=2;
                $where = array(
                    "data_id" => $data_id,
                    "airlines_id" => $this->com_user['airlines_id'],
                );
                // update
                if ($this->m_registration->update($params, $where)) {
                    // notification
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    if ($params['is_used_score'] == 2) redirect("member/pending/aunb_rute_fa/" . $this->input->post('data_id') . "/form");
                } else {
                    // default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                }
                // redirect
                redirect("member/pending/aunb_files/" . $this->input->post('data_id'));
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aunb_rute_fa/" . $this->input->post('data_id'));
    }

    // rute_fa_vv_process
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason: add sync process with SCORE slot time database using SOA step default
    public function aunb_rute_fa_vv_process(){
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        $data_id = $this->input->post('data_id');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        $this->tnotification->set_rules('vv_aircraft_type', 'required');
        $this->tnotification->set_rules('vv_izin_start_date', 'required');
        $this->tnotification->set_rules('vv_izin_expired_date', 'required');
        $this->tnotification->set_rules('vv_dos', 'required');
        $this->tnotification->set_rules('vv_pairing', 'required');
        $this->tnotification->set_rules('vv_rute_all', 'required');
        $this->tnotification->set_rules('vv_flight_no', 'required');
        $this->tnotification->set_rules('vv_etd', 'required');
        $this->tnotification->set_rules('vv_eta', 'required');
        $this->tnotification->set_rules('slot_selected', 'required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // insert izin rute
            $data_flight = $this->input->post('data_flight');
            $vv_aircraft_type = $this->input->post('vv_aircraft_type');
            $vv_izin_start_date = $this->input->post('vv_izin_start_date');
            $vv_izin_expired_date = $this->input->post('vv_izin_expired_date');
            $vv_dos = $this->input->post('vv_dos');
            $vv_pairing = $this->input->post('vv_pairing');
            $vv_rute_all = $this->input->post('vv_rute_all');
            $vv_flight_no = $this->input->post('vv_flight_no');
            $vv_etd = $this->input->post('vv_etd');
            $vv_eta = $this->input->post('vv_eta');
            $vv_etd_utc = $this->input->post('vv_etd_utc');
            $vv_eta_utc = $this->input->post('vv_eta_utc');

            $slot_selected = $this->input->post('slot_selected');
            $rute_all = $this->input->post('rute_all');
            $aircraft_type = $this->input->post('aircraft_type');
            $izin_start_date = $this->input->post('izin_start_date');
            $izin_expired_date = $this->input->post('izin_expired_date');
            $flight_no = $this->input->post('flight_no');
            $etd = $this->input->post('etd');
            $eta = $this->input->post('eta');
            $etd_utc = $this->input->post('etd_utc');
            $eta_utc = $this->input->post('eta_utc');
            $dos = $this->input->post('dos');

            $index = $slot_selected - 1;
            $params = array(
                "aircraft_type" => $vv_aircraft_type,
                "flight_no" => $vv_flight_no,
                "flight_no_2" => $flight_no[$index],
                // "nomor_slot" => $this->input->post('nomor_slot'),
                // "nomor_slot_2" => $this->input->post('nomor_slot_2'),
                // "nomor_izin" => $this->input->post('nomor_izin'),
                // "nomor_prinsip" => $this->input->post('nomor_prinsip'),
                // "nomor_kontrak" => $this->input->post('nomor_kontrak'),
                // "nomor_kontrak_2" => $this->input->post('nomor_kontrak_2'),
                // "nomor_pengadaan" => $this->input->post('nomor_pengadaan'),
                // "nomor_dkuppu" => $this->input->post('nomor_dkuppu'),
                // "eta" => $this->input->post('eta'),
                // "eta_utc" => $this->input->post('eta_utc'),
                // "eta_2" => $eta[$index],
                // "eta_2_utc" => $eta_utc[$index],
                // "etd" => $this->input->post('etd'),
                // "etd_utc" => $this->input->post('etd_utc'),
                // "etd_2" => $etd[$index],
                // "etd_2_utc" => $etd_utc[$index],
                "data_completed" => '1',
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s'),
                'pairing' => "VV",
                'is_used_score' => 1
            );
            if ($data_flight == "domestik") {
                $params["date_start"] = $vv_izin_start_date;
                $params["date_end"] = $vv_izin_expired_date;
            } else {
                $params["date_start"] = $vv_izin_start_date;
                $params["date_start_upto"] = $vv_izin_expired_date;
                $params["date_end"] = $izin_start_date[$index];
                $params["date_end_upto"] = $izin_expired_date[$index];
            }
            if ($vv_etd != "") {
                $params['etd']=$vv_etd;
                $params['etd_utc']=$vv_etd_utc;
            } else $params['is_used_score']=2;
            if ($vv_eta != "") {
                $params['eta']=$vv_eta;
                $params['eta_utc']=$vv_eta_utc;
            } else $params['is_used_score']=2;
            if ($etd[$index] != "") {
                $params['etd_2']=$etd[$index];
                $params['etd_2_utc']=$etd_utc[$index];
            } else $params['is_used_score']=2;
            if ($eta[$index] != "") {
                $params['eta_2']=$eta[$index];
                $params['eta_2_utc']=$eta_utc[$index];
            } else $params['is_used_score']=2;
            $where = array(
                "data_id" => $data_id,
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update
            if ($this->m_registration->update($params, $where)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                if ($params['is_used_score'] == 2) redirect("member/pending/aunb_rute_fa/" . $this->input->post('data_id') . "/form");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
            // redirect
            redirect("member/pending/aunb_files/" . $this->input->post('data_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aunb_rute_fa/" . $this->input->post('data_id'));
    }

    // rute_fa_done
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason: add sync process with SCORE slot time database using SOA step default
    public function aunb_rute_fa_done(){
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        $data_id = $this->input->post('data_id');
        $mode = $this->input->post('mode');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['rute_all']);
        $params = array($orig);
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $params = array($dest);
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);        
        // cek input
        $pairing = $this->input->post('pairing');
        $remarks = $this->m_registration->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('flight_no', 'Nomor Keberangkatan Pergi', 'trim|required');
        if ($pairing == "VV"){
            $this->tnotification->set_rules('flight_no_2', 'Nomor Keberangkatan Pulang', 'trim|required');
        }
        foreach ($remarks as $k => $v) {
            switch ($v['rules_field']) {
                case 'nomor_slot':
                    if (@intval($rs_airport1['is_used_score']) !== 1){
                        $this->tnotification->set_rules('nomor_slot', 'Nomor Surat Slot Clearance ('.$orig.')', 'trim|required');
                    }
                    if ($pairing == "VV" && @intval($rs_airport2['is_used_score']) !== 1){
                        $this->tnotification->set_rules('nomor_slot_2', 'Nomor Surat Slot Clearance ('.$dest.')', 'trim|required');
                    }
                    break;
                case 'eta':
                    $this->tnotification->set_rules('eta', 'ETA Keberangkatan', 'trim|required');
                    $this->tnotification->set_rules('eta_utc', 'ETA Keberangkatan (UTC)', 'trim|required');
                    if ($pairing == "VV"){
                        $this->tnotification->set_rules('eta_2', 'ETA Kedatangan', 'trim|required');
                        $this->tnotification->set_rules('eta_2_utc', 'ETA Kedatangan (UTC)', 'trim|required');
                    }
                    break;
                case 'etd':
                    $this->tnotification->set_rules('etd', 'ETD Keberangkatan', 'trim|required');                    
                    $this->tnotification->set_rules('etd_utc', 'ETD Keberangkatan (UTC)', 'trim|required');                    
                    if ($pairing == "VV"){
                        $this->tnotification->set_rules('etd_2', 'ETD Kedatangan', 'trim|required');
                        $this->tnotification->set_rules('etd_2_utc', 'ETD Kedatangan (UTC)', 'trim|required');
                    }
                    break;
                default:
                    $this->tnotification->set_rules($v['rules_field'], $v['rules_name'], 'trim|required|maxlength[' . $v['rules_length'] . ']');
                    break;
            }
        }

        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                "flight_no" => $this->input->post('flight_no'),
                "flight_no_2" => $this->input->post('flight_no_2'),
                "nomor_slot" => $this->input->post('nomor_slot'),
                "nomor_slot_2" => $this->input->post('nomor_slot_2'),
                "nomor_izin" => $this->input->post('nomor_izin'),
                "nomor_prinsip" => $this->input->post('nomor_prinsip'),
                "nomor_kontrak" => $this->input->post('nomor_kontrak'),
                "nomor_kontrak_2" => $this->input->post('nomor_kontrak_2'),
                "nomor_pengadaan" => $this->input->post('nomor_pengadaan'),
                "nomor_dkuppu" => $this->input->post('nomor_dkuppu'),
                "eta" => $this->input->post('eta'),
                "eta_utc" => $this->input->post('eta_utc'),
                "eta_2" => $this->input->post('eta_2'),
                "eta_2_utc" => $this->input->post('eta_2_utc'),
                "etd" => $this->input->post('etd'),
                "etd_utc" => $this->input->post('etd_utc'),
                "etd_2" => $this->input->post('etd_2'),
                "etd_2_utc" => $this->input->post('etd_2_utc'),
                "data_completed" => '1',
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s'),
                'is_used_score' => 1
            );
            $where = array(
                "data_id" => $data_id,
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update
            if ($this->m_registration->update($params, $where)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
            // redirect
            redirect("member/pending/aunb_files/" . $data_id);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        if ($mode == "form")
            redirect("member/pending/aunb_rute_fa/" . $data_id . '/form');
        else
            redirect("member/pending/aunb_rute_fa/" . $data_id);
    }

    // files attachment
    // modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason: add new logic to prevent upload file for slot clearance shows-up when an airport already use SCORE services
    public function aunb_files($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_scheduled/files.html");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // setup filter by Service Code: Charter Flight (C), Extra Flight (G) >> if no row >> manual entry slot
        $rules_sc = array("C","G");
        $is_filter_by_rule_sc = in_array($result['services_cd'], $rules_sc);
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['rute_all']);
        $params = array($orig);
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $params = array($dest);
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // list persyaratan
        $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        if ($rs_airport1['is_used_score'] == 1 && $rs_airport2['is_used_score'] == 1 && $is_filter_by_rule_sc){
            $t = $rs_files;
            unset($rs_files);
            foreach ($t as $k => $v) {
                if ($v['ref_field']=="file_slot") continue;
                $rs_files[] = $v;
            }
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_files->get_list_file_uploaded(array($data_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // file process
    // modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason modified: 
    // - change get new id logic to prevent duplicate primary key when upload multiple file in the same time
    public function aunb_files_process() {
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // data id
        $data_id = $this->input->post('data_id');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // upload 1 per 1
            $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
            foreach ($rs_files as $k=>$files) {
                $file = $_FILES[$files['ref_field']];
                // upload 1 per 1
                if (!empty($file['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/fa/' . $data_id . '/' . $files['ref_id'];
                    $config['allowed_types'] = '*';
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload($files['ref_field'])) {
                        // jika berhasil
                        $data = $this->tupload->data();
                        // update
                        $file_id = $this->m_files->get_file_id() + $k;
                        $filepath = 'resource/doc/fa/' . $data_id . '/' . $files['ref_id'] . '/' . $data['file_name'];
                        $this->m_files->update_files(array($data_id, $files['ref_id']), array($file_id, $data_id, $filepath, $data['file_name'], $files['ref_id']));
                        // notification
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aunb_files/" . $data_id);
    }

    // send process
    // modified by: sanjaya.im@gmail.com
    // modified on: 20-May-2015
    // reason modified:     
    // - change logic for validation file attachment
    public function aunb_send_process() {
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // data id
        $data_id = $this->input->post('data_id');
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // setup filter by Service Code: Charter Flight (C), Extra Flight (G) >> if no row >> manual entry slot
        $rules_sc = array("C","G");
        $is_filter_by_rule_sc = in_array($result['services_cd'], $rules_sc);
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['rute_all']);
        $params = array($orig);
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $params = array($dest);
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);
        // validation
        $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        if ($rs_airport1['is_used_score'] == 1 && $rs_airport2['is_used_score'] == 1 && $is_filter_by_rule_sc){
            foreach ($rs_files as $k => $v) {
                if ($v['ref_field']=="file_slot") continue;
                if (!$this->m_files->is_file_completed_by_id(array($v['ref_id']))) {
                    $this->tnotification->set_error_message('File persyaratan ('.$v['ref_name'].') belum diupload!');
                }
            }
        } else {
            if (!$this->m_files->is_file_completed(array($data_id, $result['data_type'], $result['data_flight'], $result['services_cd']))) {
                $this->tnotification->set_error_message('File persyaratan belum diupload!');
            }
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
            $result = $this->m_pending->get_data_by_id($params);
            if ($result['data_flight'] == 'domestik') {
                $flow_id = 11;
            } else {
                $flow_id = 21;
            }
            // jalankan
            if ($result['data_completed'] == '1') {
                // process flow
                $process_id = $this->m_pending->get_data_id();
                $params = array($process_id, $data_id, $flow_id, $this->com_user['user_id']);
                $this->m_pending->insert_process($params);
                // update status & create number
                $code = ($result['data_flight'] == 'domestik') ? 'AUNBDN' : 'AUNBLN';
                $doc_no = $this->m_pending->get_document_number_berjadwal($code);
                $this->m_pending->update_status_data(array('waiting', $result['document_no'], $data_id, $this->com_user['airlines_id']));
                $params = array('approve', 'done', $this->com_user['user_id'], $result['process_id']);
                $this->m_task->action_update($params);
                // send mail
                if ($result['data_flight'] == 'domestik') {
                    $this->m_email->mail_to_all_aunbdn($this->input->post('data_id'), $this->com_user['airlines_id']);
                } else {
                    $this->m_email->mail_to_all_aunbln($this->input->post('data_id'), $this->com_user['airlines_id']);
                }
                // default redirect
                redirect("member/pending/");
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aunb_files/" . $data_id);
    }

    // auntb
    public function auntb($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_unscheduled/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // assign
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_pending->get_services_cd(array($result['data_type'], $result['data_flight'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function auntb_add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // data
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "services_cd" => $this->input->post('services_cd'),
            );
            // where
            $where = array(
                "data_id" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id']
            );
            // insert
            if ($this->m_pending->update($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("member/pending/auntb_form/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/auntb_add/" . $this->input->post('data_id'));
    }

    // form fa
    public function auntb_form($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_unscheduled/form.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // assign
        $result['data_flight'] = empty($result['data_flight']) ? 'domestik' : $result['data_flight'];
        $this->smarty->assign("result", $result);
        // --
        $this->smarty->assign("rs_airlines", $this->m_pending->get_all_airlines());
        $rs_airport = $this->m_pending->get_all_airport();
        $this->smarty->assign("rs_airport", $rs_airport);
        // data
        $data = "";
        foreach ($rs_airport as $value) {
            $data .= "{ id: '" . $value['airport_iata_cd'] . "', text: '" . $value['airport_iata_cd'] . " | " . $value['airport_nm'] . "'},";
        }
        $this->smarty->assign("data", $data);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_pending->get_all_service_code());
        // get hari pengajuan
        $this->smarty->assign("hari_pengajuan", $this->m_pending->get_hari_pengajuan(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // get remark field
        $this->smarty->assign("remark_field", $this->m_pending->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function auntb_form_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('registration', 'Tanda Pendaftaran', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('call_sign', 'Nama Panggilan', 'trim|required');
        $this->tnotification->set_rules('flight_no', 'Nomor Keberangkatan', 'trim|required');
        // domestik / internasional
        $data_flight = $this->input->post('data_flight');
        if ($data_flight == 'domestik') {
            $this->tnotification->set_rules('date_start', 'Tanggal Datang (Arrival)', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end', 'Tanggal Pergi (Departure)', 'trim|required|maxlength[10]');
        } else {
            $this->tnotification->set_rules('date_start', 'Tanggal Masuk Indonesia ( dari )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_start_upto', 'Tanggal Masuk Indonesia ( sampai )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end', 'Tanggal Keluar Indonesia ( dari )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end_upto', 'Tanggal Keluar Indonesia ( sampai )', 'trim|required|maxlength[10]');
        }
        // --
        $this->tnotification->set_rules('rute_all', 'Rute', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('technical_landing', 'Pendaratan Teknis Di', 'trim');
        $this->tnotification->set_rules('niaga_landing', 'Pendaratan Niaga Di', 'trim');
        $this->tnotification->set_rules('flight_purpose', 'Tujuan Penerbangan', 'trim|maxlength[50]');
        $this->tnotification->set_rules('flight_pilot', 'Nama Pilot', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('flight_crew', 'Awak Pesawat Udara Lainnya', 'trim|required|maxlength[50]');
        $services_cd = $this->input->post('services_cd');
        if ($services_cd != "F") {
            if ($services_cd != "P") {
                $this->tnotification->set_rules('flight_goods', 'Penumpang/Barang', 'trim|required|maxlength[50]');
            }
        }
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        $this->tnotification->set_rules('remark', 'Keterangan', 'trim|maxlength[255]');
        $this->tnotification->set_rules('notes', 'Catatan', 'trim|maxlength[255]');
        $this->tnotification->set_rules('applicant', 'Pemohon', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('designation', 'Penunjukan', 'trim|required|maxlength[50]');
        $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
        $result = $this->m_pending->get_detail_data_by_id($params);
        $rs_remark = $this->m_pending->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        foreach ($rs_remark as $value) {
            $this->tnotification->set_rules($value['rules_field'], $value['rules_name'], 'trim|required|maxlength[' . $value['rules_length'] . ']');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // clear input
            if ($data_flight == 'domestik') {
                $waktu = $this->input->post('waktu');
                $waktu = empty($waktu) ? NULL : $waktu;
                $date_start = $this->input->post('date_start');
                $date_end = $this->input->post('date_end');
                $date_start_upto = NULL;
                $date_end_upto = NULL;
                $flight_purpose = NULL;
            } else {
                $waktu = NULL;
                $date_start = $this->input->post('date_start');
                $date_end = $this->input->post('date_end');
                $date_start_upto = $this->input->post('date_start_upto');
                $date_end_upto = $this->input->post('date_end_upto');
                $flight_purpose = $this->input->post('flight_purpose');
            }
            // params
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "aircraft_type" => $this->input->post('aircraft_type'),
                "registration" => $this->input->post('registration'),
                "country" => 'INDONESIA',
                "call_sign" => $this->input->post('call_sign'),
                "flight_no" => $this->input->post('flight_no'),
                "flight_no_2" => $this->input->post('flight_no_2'),
                "date_start" => $date_start,
                "date_start_upto" => $date_start_upto,
                "date_end" => $date_end,
                "date_end_upto" => $date_end_upto,
                "waktu" => NULL,
                "rute_all" => $this->input->post('rute_all'),
                "technical_landing" => $this->input->post('technical_landing'),
                "niaga_landing" => $this->input->post('niaga_landing'),
                "flight_purpose" => $flight_purpose,
                "flight_pilot" => $this->input->post('flight_pilot'),
                "flight_crew" => $this->input->post('flight_crew'),
                "flight_goods" => ($this->input->post('flight_goods') == "") ? null : $this->input->post('flight_goods'),
                "services_cd" => $this->input->post('services_cd'),
                "remark" => $this->input->post('remark'),
                "nomor_slot" => $this->input->post('nomor_slot'),
                "nomor_izin" => $this->input->post('nomor_izin'),
                "nomor_prinsip" => $this->input->post('nomor_prinsip'),
                "nomor_kontrak" => $this->input->post('nomor_kontrak'),
                "nomor_pengadaan" => $this->input->post('nomor_pengadaan'),
                "nomor_dkuppu" => $this->input->post('nomor_dkuppu'),
                "eta" => $this->input->post('eta'),
                "etd" => $this->input->post('etd'),
                "catatan" => $this->input->post('notes'),
                "applicant" => $this->input->post('applicant'),
                "designation" => $this->input->post('designation'),
                "data_completed" => '1',
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s')
            );
            $where = array(
                "registration_code" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update
            if ($this->m_pending->update($params, $where)) {
                $this->m_pending->delete_rute(array($this->input->post('data_id')));
                // update rute batch
                $id = $this->m_pending->get_data_id();
                $rute_all = $this->input->post('rute_all');
                $rute_all = explode(",", $rute_all);
                $x = 1;
                foreach ($rute_all as $value) {
                    $data[] = array(
                        "id" => $id . $x,
                        "data_id" => $this->input->post('data_id'),
                        "seq" => $x++,
                        "airport_id" => $value
                    );
                }
                $this->m_pending->update_rute($data);
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("member/pending/auntb_files/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/auntb_form/" . $this->input->post('data_id'));
    }

    // files attachment
    public function auntb_files($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_unscheduled/files.html");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // list persyaratan
        $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_files->get_list_file_uploaded(array($data_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // file process
    public function auntb_files_process() {
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // data id
        $data_id = $this->input->post('data_id');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // upload 1 per 1
            $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
            foreach ($rs_files as $files) {
                $file = $_FILES[$files['ref_field']];
                // upload 1 per 1
                if (!empty($file['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/fa/' . $data_id . '/' . $files['ref_id'];
                    $config['allowed_types'] = '*';
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload($files['ref_field'])) {
                        // jika berhasil
                        $data = $this->tupload->data();
                        // update
                        $file_id = $this->m_files->get_file_id();
                        $filepath = 'resource/doc/fa/' . $data_id . '/' . $files['ref_id'] . '/' . $data['file_name'];
                        $this->m_files->update_files(array($data_id, $files['ref_id']), array($file_id, $data_id, $filepath, $data['file_name'], $files['ref_id']));
                        // notification
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/auntb_files/" . $data_id);
    }

    // send process
    public function auntb_send_process() {
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // data id
        $data_id = $this->input->post('data_id');
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // validation
        if (!$this->m_files->is_file_completed(array($data_id, $result['data_type'], $result['data_flight'], $result['services_cd']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
            $result = $this->m_pending->get_data_by_id($params);
            if ($result['data_flight'] == 'domestik') {
                $flow_id = 31;
            } else {
                $flow_id = 41;
            }
            // jalankan
            if ($result['data_completed'] == '1') {
                // process flow
                $process_id = $this->m_pending->get_data_id();
                $params = array($process_id, $data_id, $flow_id, $this->com_user['user_id']);
                $this->m_pending->insert_process($params);
                // update status & create number
                $code = ($result['data_flight'] == 'domestik') ? 'AUNTBDN' : 'AUNTBLN';
                $doc_no = $this->m_pending->get_document_number_tidak_berjadwal($code);
                $this->m_pending->update_status_data(array('waiting', $result['document_no'], $data_id, $this->com_user['airlines_id']));
                $params = array('approve', 'done', $this->com_user['user_id'], $result['process_id']);
                $this->m_task->action_update($params);
                // send mail
                if ($result['data_flight'] == 'domestik') {
                    $this->m_email->mail_to_all_aunbdn($this->input->post('data_id'), $this->com_user['airlines_id']);
                } else {
                    $this->m_email->mail_to_all_aunbln($this->input->post('data_id'), $this->com_user['airlines_id']);
                }
                // default redirect
                redirect("member/pending/");
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/auntb_files/" . $data_id);
    }

    // aubn
    public function aubn($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_non/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // assign
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_pending->get_services_cd(array($result['data_type'], $result['data_flight'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function aubn_add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // data
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "services_cd" => $this->input->post('services_cd'),
            );
            // where
            $where = array(
                "data_id" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id']
            );
            // insert
            if ($this->m_pending->update($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("member/pending/aubn_form/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aubn_add/" . $this->input->post('data_id'));
    }

    // form fa
    public function aubn_form($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_non/form.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // assign
        $result['data_flight'] = empty($result['data_flight']) ? 'domestik' : $result['data_flight'];
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // --
        $this->smarty->assign("rs_airlines", $this->m_pending->get_all_airlines());
        $rs_airport = $this->m_pending->get_all_airport();
        $this->smarty->assign("rs_airport", $rs_airport);
        // data
        $data = "";
        foreach ($rs_airport as $value) {
            $data .= "{ id: '" . $value['airport_iata_cd'] . "', text: '" . $value['airport_iata_cd'] . " | " . $value['airport_nm'] . "'},";
        }
        $this->smarty->assign("data", $data);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_pending->get_all_service_code());
        // get hari pengajuan
        $this->smarty->assign("hari_pengajuan", $this->m_pending->get_hari_pengajuan(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // get remark field
        $this->smarty->assign("remark_field", $this->m_pending->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function aubn_form_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('registration', 'Tanda Pendaftaran', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('call_sign', 'Nama Panggilan', 'trim|required');
        $this->tnotification->set_rules('flight_no', 'Nomor Keberangkatan', 'trim|required');
        // domestik / internasional
        $data_flight = $this->input->post('data_flight');
        if ($data_flight == 'domestik') {
            $this->tnotification->set_rules('date_start', 'Tanggal Datang (Arrival)', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end', 'Tanggal Pergi (Departure)', 'trim|required|maxlength[10]');
        } else {
            $this->tnotification->set_rules('date_start', 'Tanggal Masuk Indonesia ( dari )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_start_upto', 'Tanggal Masuk Indonesia ( sampai )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end', 'Tanggal Keluar Indonesia ( dari )', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('date_end_upto', 'Tanggal Keluar Indonesia ( sampai )', 'trim|required|maxlength[10]');
        }
        // --
        $this->tnotification->set_rules('rute_all', 'Rute', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('technical_landing', 'Pendaratan Teknis Di', 'trim');
        $this->tnotification->set_rules('niaga_landing', 'Pendaratan Niaga Di', 'trim');
        $this->tnotification->set_rules('flight_purpose', 'Tujuan Penerbangan', 'trim|maxlength[50]');
        $this->tnotification->set_rules('flight_pilot', 'Nama Pilot', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('flight_crew', 'Awak Pesawat Udara Lainnya', 'trim|required|maxlength[50]');
        $services_cd = $this->input->post('services_cd');
        if ($services_cd != "F") {
            if ($services_cd != "P") {
                $this->tnotification->set_rules('flight_goods', 'Penumpang/Barang', 'trim|required|maxlength[50]');
            }
        }
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        $this->tnotification->set_rules('remark', 'Keterangan', 'trim|maxlength[255]');
        $this->tnotification->set_rules('notes', 'Catatan', 'trim|maxlength[255]');
        $this->tnotification->set_rules('applicant', 'Pemohon', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('designation', 'Penunjukan', 'trim|required|maxlength[50]');
        $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
        $result = $this->m_pending->get_detail_data_by_id($params);
        $rs_remark = $this->m_pending->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        foreach ($rs_remark as $value) {
            $this->tnotification->set_rules($value['rules_field'], $value['rules_name'], 'trim|required|maxlength[' . $value['rules_length'] . ']');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // clear input
            if ($data_flight == 'domestik') {
                $waktu = $this->input->post('waktu');
                $waktu = empty($waktu) ? NULL : $waktu;
                $date_start = $this->input->post('date_start');
                $date_end = $this->input->post('date_end');
                $date_start_upto = NULL;
                $date_end_upto = NULL;
                $flight_purpose = NULL;
            } else {
                $waktu = NULL;
                $date_start = $this->input->post('date_start');
                $date_end = $this->input->post('date_end');
                $date_start_upto = $this->input->post('date_start_upto');
                $date_end_upto = $this->input->post('date_end_upto');
                $flight_purpose = $this->input->post('flight_purpose');
            }
            // params
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "aircraft_type" => $this->input->post('aircraft_type'),
                "registration" => $this->input->post('registration'),
                "country" => 'INDONESIA',
                "call_sign" => $this->input->post('call_sign'),
                "flight_no" => $this->input->post('flight_no'),
                "flight_no_2" => $this->input->post('flight_no_2'),
                "date_start" => $date_start,
                "date_start_upto" => $date_start_upto,
                "date_end" => $date_end,
                "date_end_upto" => $date_end_upto,
                "waktu" => NULL,
                "rute_all" => $this->input->post('rute_all'),
                "technical_landing" => $this->input->post('technical_landing'),
                "niaga_landing" => $this->input->post('niaga_landing'),
                "flight_purpose" => $flight_purpose,
                "flight_pilot" => $this->input->post('flight_pilot'),
                "flight_crew" => $this->input->post('flight_crew'),
                "flight_goods" => ($this->input->post('flight_goods') == "") ? null : $this->input->post('flight_goods'),
                "services_cd" => $this->input->post('services_cd'),
                "remark" => $this->input->post('remark'),
                "nomor_slot" => $this->input->post('nomor_slot'),
                "nomor_izin" => $this->input->post('nomor_izin'),
                "nomor_prinsip" => $this->input->post('nomor_prinsip'),
                "nomor_kontrak" => $this->input->post('nomor_kontrak'),
                "nomor_pengadaan" => $this->input->post('nomor_pengadaan'),
                "nomor_dkuppu" => $this->input->post('nomor_dkuppu'),
                "eta" => $this->input->post('eta'),
                "etd" => $this->input->post('etd'),
                "catatan" => $this->input->post('notes'),
                "applicant" => $this->input->post('applicant'),
                "designation" => $this->input->post('designation'),
                "data_completed" => '1',
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s')
            );
            $where = array(
                "registration_code" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update
            if ($this->m_pending->update($params, $where)) {
                $this->m_pending->delete_rute(array($this->input->post('data_id')));
                // update rute batch
                $id = $this->m_pending->get_data_id();
                $rute_all = $this->input->post('rute_all');
                $rute_all = explode(",", $rute_all);
                $x = 1;
                foreach ($rute_all as $value) {
                    $data[] = array(
                        "id" => $id . $x,
                        "data_id" => $this->input->post('data_id'),
                        "seq" => $x++,
                        "airport_id" => $value
                    );
                }
                $this->m_pending->update_rute($data);
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect`
                redirect("member/pending/aubn_files/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aubn_form/" . $this->input->post('data_id'));
    }

    // files attachment
    public function aubn_files($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // set template content
        $this->smarty->assign("template_content", "member/pending/form/registration_non/files.html");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        $this->smarty->assign("result", $result);
        // get pesan revisi
        $message = $this->m_pending->get_revision($params);
        $this->smarty->assign("message", $message);
        // list persyaratan
        $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_files->get_list_file_uploaded(array($data_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // file process
    public function aubn_files_process() {
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // data id
        $data_id = $this->input->post('data_id');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // upload 1 per 1
            $rs_files = $this->m_files->get_list_file_required(array($result['data_type'], $result['data_flight'], $result['services_cd']));
            foreach ($rs_files as $files) {
                $file = $_FILES[$files['ref_field']];
                // upload 1 per 1
                if (!empty($file['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/fa/' . $data_id . '/' . $files['ref_id'];
                    $config['allowed_types'] = '*';
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload($files['ref_field'])) {
                        // jika berhasil
                        $data = $this->tupload->data();
                        // update
                        $file_id = $this->m_files->get_file_id();
                        $filepath = 'resource/doc/fa/' . $data_id . '/' . $files['ref_id'] . '/' . $data['file_name'];
                        $this->m_files->update_files(array($data_id, $files['ref_id']), array($file_id, $data_id, $filepath, $data['file_name'], $files['ref_id']));
                        // notification
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aubn_files/" . $data_id);
    }

    // send process
    public function aubn_send_process() {
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/pending");
        }
        // data id
        $data_id = $this->input->post('data_id');
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // validation
        if (!$this->m_files->is_file_completed(array($data_id, $result['data_type'], $result['data_flight'], $result['services_cd']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
            $result = $this->m_pending->get_data_by_id($params);
            if ($result['data_flight'] == 'domestik') {
                $flow_id = 51;
            } else {
                $flow_id = 61;
            }
            // jalankan
            if ($result['data_completed'] == '1') {
                // process flow
                $process_id = $this->m_pending->get_data_id();
                $params = array($process_id, $data_id, $flow_id, $this->com_user['user_id']);
                $this->m_pending->insert_process($params);
                // update status & create number
                $code = ($result['data_flight'] == 'domestik') ? 'AUBNDN' : 'AUBNLN';
                $doc_no = $this->m_pending->get_document_number_bukan_niaga($code);
                $this->m_pending->update_status_data(array('waiting', $result['document_no'], $data_id, $this->com_user['airlines_id']));
                $params = array('approve', 'done', $this->com_user['user_id'], $result['process_id']);
                $this->m_task->action_update($params);
                // send mail
                if ($result['data_flight'] == 'domestik') {
                    $this->m_email->mail_to_all_aunbdn($this->input->post('data_id'), $this->com_user['airlines_id']);
                } else {
                    $this->m_email->mail_to_all_aunbln($this->input->post('data_id'), $this->com_user['airlines_id']);
                }
                // default redirect
                redirect("member/pending/");
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/pending/aubn_files/" . $data_id);
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_files->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
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
            redirect('member/pending');
        }
    }

    // batalkan process
    public function batalkan($data_id = "") {
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_pending->get_data_by_id($params);
        if (empty($result)) {
            redirect('member/pending');
        }
        // params
        $params = array(
            "data_st"       => "rejected",
            "mdb_finish"    => $this->com_user['user_id'],
            "note"          => "rejected by airline",
        );
        $where = array(
            "registration_code" => $data_id,
        );
        // update
        if ($this->m_pending->cancel($params, $where)) {
            // update
            $params = array('approve', 'done', $this->com_user['user_id'], $result['process_id']);
            $this->m_task->action_update($params);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dibatalkan");
            redirect('member/pending');
        } else {
            // default error
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        redirect('member/pending/');
    }

    /* ---------- ajax services code ---------- */

    // get services code
    public function get_services_cd() {
        // set page rules
        $this->_set_page_rule("R");
        // get services code
        $params = array(
            "data_type" => $this->input->post('data_type'),
            "data_flight" => $this->input->post('data_flight'),
        );
        $result = $this->m_pending->get_services_cd($params);
        echo json_encode($result);
    }

    // // add process
    // public function form_process() {
    //     // set page rules
    //     $this->_set_page_rule("U");
    //     // cek input
    //     $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
    //     $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
    //     $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
    //     $this->tnotification->set_rules('flight_no', 'Nama Panggilan', 'trim|required');
    //     // domestik / internasional
    //     $data_flight = $this->input->post('data_flight');
    //     if ($data_flight == 'domestik') {
    //         $this->tnotification->set_rules('date_start', 'Tanggal Datang (Arrival)', 'trim|required|maxlength[10]');
    //         $this->tnotification->set_rules('date_end', 'Tanggal Pergi (Departure)', 'trim|required|maxlength[10]');
    //     } else {
    //         $this->tnotification->set_rules('date_start', 'Tanggal Masuk Indonesia ( dari )', 'trim|required|maxlength[10]');
    //         $this->tnotification->set_rules('date_start_upto', 'Tanggal Masuk Indonesia ( sampai )', 'trim|required|maxlength[10]');
    //         $this->tnotification->set_rules('date_end', 'Tanggal Keluar Indonesia ( dari )', 'trim|required|maxlength[10]');
    //         $this->tnotification->set_rules('date_end_upto', 'Tanggal Keluar Indonesia ( sampai )', 'trim|required|maxlength[10]');
    //     }
    //     // --
    //     $this->tnotification->set_rules('rute_all', 'Rute', 'trim|required|maxlength[100]');
    //     $this->tnotification->set_rules('technical_landing', 'Pendaratan Teknis Di', 'trim');
    //     $this->tnotification->set_rules('niaga_landing', 'Pendaratan Niaga Di', 'trim');
    //     $this->tnotification->set_rules('flight_purpose', 'Tujuan Penerbangan', 'trim|maxlength[50]');
    //     $this->tnotification->set_rules('flight_pilot', 'Nama Pilot', 'trim|required|maxlength[50]');
    //     $this->tnotification->set_rules('flight_crew', 'Awak Pesawat Udara Lainnya', 'trim|required|maxlength[50]');
    //     $this->tnotification->set_rules('flight_goods', 'Penumpang/Barang', 'trim|required|maxlength[50]');
    //     $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
    //     $this->tnotification->set_rules('remark', 'Keterangan', 'trim|maxlength[255]');
    //     $this->tnotification->set_rules('notes', 'Catatan', 'trim|maxlength[255]');
    //     $this->tnotification->set_rules('applicant', 'Pemohon', 'trim|required|maxlength[50]');
    //     $this->tnotification->set_rules('designation', 'Penunjukan', 'trim|required|maxlength[50]');
    //     // process
    //     if ($this->tnotification->run() !== FALSE) {
    //         $rute_all = $this->input->post('rute_all');
    //         $total_rute = COUNT(explode(",", $rute_all));
    //         if ($total_rute < 2) {
    //             // default error
    //             $this->tnotification->delete_last_field();
    //             $this->tnotification->sent_notification("error", "Rute minimal 2 bandara");
    //             // default redirect
    //             redirect("member/pending/detail/" . $this->input->post('data_id'));
    //         }
    //         $services_cd = $this->input->post('services_cd');
    //         if ($services_cd != "C" || $services_cd != "E" || $services_cd != "M") {
    //             $date_now = new DateTime(date('Y-m-d'));
    //             $date_start = new DateTime($this->input->post('date_start'));
    //             $interval = $date_now->diff($date_start);
    //             $diff = $interval->format('%a');
    //             if (intval($diff) > 3) {
    //                 // default error
    //                 $this->tnotification->sent_notification("error", "Tanggal Penerbangan Yang Diajukan Minimal 3 Hari Kedepan, Mohon Periksa Kembali Tanggal Penerbangan Anda");
    //                 // default redirect
    //                 redirect("member/pending/detail/" . $this->input->post('data_id'));
    //             }
    //         }
    //         // clear input
    //         if ($data_flight == 'domestik') {
    //             $waktu = $this->input->post('waktu');
    //             $date_start = $this->input->post('date_start');
    //             $date_end = $this->input->post('date_end');
    //             $date_start_upto = NULL;
    //             $date_end_upto = NULL;
    //             $flight_purpose = NULL;
    //         } else {
    //             $date_start = $this->input->post('date_start');
    //             $date_end = $this->input->post('date_end');
    //             $date_start_upto = $this->input->post('date_start_upto');
    //             $date_end_upto = $this->input->post('date_end_upto');
    //             $flight_purpose = $this->input->post('flight_purpose');
    //         }
    //         // params
    //         $params = array(
    //             "data_flight" => $this->input->post('data_flight'),
    //             "aircraft_type" => $this->input->post('aircraft_type'),
    //             "country" => 'INDONESIA',
    //             "flight_no" => $this->input->post('flight_no'),
    //             "date_start" => $date_start,
    //             "date_start_upto" => $date_start_upto,
    //             "date_end" => $date_end,
    //             "date_end_upto" => $date_end_upto,
    //             "rute_all" => $this->input->post('rute_all'),
    //             "technical_landing" => $this->input->post('technical_landing'),
    //             "niaga_landing" => $this->input->post('niaga_landing'),
    //             "flight_purpose" => $flight_purpose,
    //             "flight_pilot" => $this->input->post('flight_pilot'),
    //             "flight_crew" => $this->input->post('flight_crew'),
    //             "flight_goods" => $this->input->post('flight_goods'),
    //             "services_cd" => $this->input->post('services_cd'),
    //             "remark" => $this->input->post('remark'),
    //             "catatan" => $this->input->post('notes'),
    //             "applicant" => $this->input->post('applicant'),
    //             "designation" => $this->input->post('designation'),
    //             "data_completed" => '1',
    //             "mdb" => $this->com_user['user_id'],
    //             "mdd" => date('Y-m-d H:i:s')
    //         );
    //         $where = array(
    //             "data_id" => $this->input->post('data_id'),
    //             "airlines_id" => $this->com_user['airlines_id'],
    //         );
    //         // update
    //         if ($this->m_pending->update($params, $where)) {
    //             $this->m_pending->delete_rute(array($this->input->post('data_id')));
    //             // update rute batch
    //             $id = $this->m_pending->get_data_id();
    //             $rute_all = $this->input->post('rute_all');
    //             $rute_all = explode(",", $rute_all);
    //             $x = 1;
    //             foreach ($rute_all as $value) {
    //                 $data[] = array(
    //                     "id"            => $id . $x,
    //                     "data_id"       => $this->input->post('data_id'),
    //                     "seq"           => $x++,
    //                     "airport_id"    => $value
    //                 );
    //             }
    //             $this->m_pending->update_rute($data);
    //             // success
    //             $this->tnotification->delete_last_field();
    //             $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
    //         } else {
    //             // default error
    //             $this->tnotification->delete_last_field();
    //             $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //         }
    //     } else {
    //         // default error
    //         $this->tnotification->delete_last_field();
    //         $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //     }
    //     // default redirect
    //     redirect("member/pending/detail/" . $this->input->post('data_id'));
    // }

    // // process notes
    // public function catatan_process() {
    //     // set page rules
    //     $this->_set_page_rule("U");
    //     // cek input
    //     $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
    //     $this->tnotification->set_rules('process_id', 'ID', 'trim|required');
    //     $this->tnotification->set_rules('notes', 'Catatan', 'trim|maxlength[5000]');
    //     // id
    //     $data_id = $this->input->post('data_id');
    //     $process_id = $this->input->post('process_id');
    //     // process
    //     if ($this->tnotification->run() !== FALSE) {
    //         // params
    //         $params = array(
    //             "catatan" => $this->input->post('notes')
    //         );
    //         // where
    //         $where = array('data_id' => $data_id, 'process_id' => $process_id);
    //         // update
    //         if ($this->m_task->update_process($params, $where)) {
    //             // success
    //             $this->tnotification->delete_last_field();
    //             $this->tnotification->sent_notification("success", "Data berhasil disimpan");
    //         } else {
    //             // default error
    //             $this->tnotification->delete_last_field();
    //             $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //         }
    //     } else {
    //         // default error
    //         $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //     }
    //     // default redirect
    //     redirect("member/pending/detail/" . $data_id);
    // }

    // // download
    // public function files_download($data_id = "") {
    //     // get detail data
    //     $params = array($data_id, $this->com_user['airlines_id']);
    //     $result = $this->m_task->get_detail_files_by_id($params);
    //     if (empty($result)) {
    //         redirect('member/pending/detail/' . $data_id);
    //     }
    //     // filepath
    //     $file_path = 'resource/doc/fa/' . $data_id . '/' . $result['file_id'] . '.pdf';
    //     if (is_file($file_path)) {
    //         // download
    //         header('Content-Description: Download Files');
    //         header('Content-Type: application/octet-stream');
    //         header('Content-Length: ' . filesize($file_path));
    //         header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $result['file_path']) . '"');
    //         readfile($file_path);
    //         exit();
    //     } else {
    //         redirect('member/pending/detail/' . $data_id);
    //     }
    // }

    // // file process
    // public function files_process() {
    //     // cek input
    //     $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
    //     $this->tnotification->set_rules('file_title', 'Judul', 'trim|required|max_length[50]');
    //     // cek files
    //     if (empty($_FILES['file_path']['tmp_name'])) {
    //         $this->tnotification->set_error_message("File is not selected!");
    //     }
    //     // data id
    //     $data_id = $this->input->post('data_id');
    //     // last id
    //     $files_id = $this->m_pending->get_data_id();
    //     // process
    //     if ($this->tnotification->run() !== FALSE) {
    //         // delete ( sementara )
    //         $this->m_pending->delete_all_files(array($this->input->post('data_id'), $this->com_user['airlines_id']));
    //         // insert
    //         $params = array($files_id, $this->input->post('data_id'), $this->input->post('file_title'));
    //         if ($this->m_pending->insert_files($params)) {
    //             // load
    //             $this->load->library('tupload');
    //             // upload file
    //             if (!empty($_FILES['file_path']['tmp_name'])) {
    //                 // upload config
    //                 $config['upload_path'] = 'resource/doc/fa/' . $data_id;
    //                 $config['allowed_types'] = 'pdf';
    //                 $config['file_name'] = $files_id;
    //                 $this->tupload->initialize($config);
    //                 // process upload images
    //                 if ($this->tupload->do_upload('file_path')) {
    //                     // jika berhasil
    //                     $data = $this->tupload->data();
    //                     $this->m_pending->update_files(array($_FILES['file_path']['name'], $files_id));
    //                     // notification
    //                     $this->tnotification->delete_last_field();
    //                     $this->tnotification->sent_notification("success", "Data berhasil disimpan");
    //                 } else {
    //                     // jika gagal
    //                     $this->tnotification->set_error_message($this->tupload->display_errors());
    //                 }
    //             }
    //         } else {
    //             // default error
    //             $this->tnotification->delete_last_field();
    //             $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //         }
    //     } else {
    //         // default error
    //         $this->tnotification->delete_last_field();
    //         $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //     }
    //     // default redirect
    //     redirect("member/pending/detail/" . $data_id);
    // }

    // // send process
    // public function send_process($data_id = "", $process_id = "") {
    //     // set page rules
    //     $this->_set_page_rule("U");
    //     // get detail data
    //     $params = array($data_id, $this->com_user['airlines_id']);
    //     $result = $this->m_member->get_data_by_id($params);
    //     $this->smarty->assign("result", $result);
    //     if (empty($result)) {
    //         redirect('member/pending');
    //     }
    //     $next_flow = array(1 => 11, 2 => 21, 3 => 31, 4 => 41, 5 => 51, 6 => 61);
    //     // update
    //     $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
    //     if ($this->m_task->action_update($params)) {
    //         $process_id = $this->m_task->get_process_id();
    //         // process flow
    //         $params = array($process_id, $data_id, $next_flow[$result['group_id']], $this->com_user['user_id']);
    //         $this->m_task->insert_process($params);
    //         // success
    //         $this->tnotification->delete_last_field();
    //         $this->tnotification->sent_notification("success", "Data berhasil disimpan");
    //         // default redirect
    //         redirect("member/pending/");
    //     } else {
    //         // default error
    //         $this->tnotification->sent_notification("error", "Data gagal disimpan");
    //     }
    //     // default redirect
    //     redirect("member/pending/" . $this->input->post('link') . "/" . $data_id);
    // }

}
