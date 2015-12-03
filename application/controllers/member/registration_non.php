<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class registration_non extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_registration');
        $this->load->model('m_airlines');
        $this->load->model('m_airport');
        $this->load->model('m_service_code');
        $this->load->model('m_files');
        $this->load->model('m_email');
        $this->load->model('m_disclaimer');
        $this->load->model('m_block');
        // load library
        $this->load->library('tnotification');
        $this->load->library('session');
        $this->load->library('pagination');
        // check status bayar
        $result = $this->m_registration->get_fa_unpaid(array($this->com_user['airlines_id'], date('Y-m-d')));
        if ($result > 0) {
            // notification
            $this->tnotification->sent_notification("error", "Silahkan Membayar FA yang telah terbit terlebih dahulu");
            redirect('member/welcome');
        }
        // get airlines type
        $result = $this->m_registration->get_airlines_type(array($this->com_user['airlines_id']));
        foreach ($result as $value) {
            $data[] = $value['airlines_type'];
        }
        if (!in_array("bukan niaga", $data)) {
            // notification
            $this->tnotification->sent_notification("error", "Anda Tidak Dapat Melakukan Registrasi Bukan Niaga");
            redirect('member/welcome');
        }
    }

    // create registration
    public function create() {
        // set page rules
        $this->_set_page_rule("C");
        // create data_id, data_st, data_type, airlines_id, mdb, mdd
        $params = array(
            "airlines_id" => $this->com_user['airlines_id'],
            "data_st" => 'open',
            'data_type' => 'bukan niaga',
            "mdb" => $this->com_user['user_id']
        );
        $id = $this->m_registration->create_group_data($params);
        if (!empty($id)) {
            // notification
            $this->tnotification->sent_notification("success", "Data has been created!");
            redirect('member/registration_non/add/' . $id);
        } else {
            // notification
            $this->tnotification->sent_notification("error", "An unexpected error has occurred");
            redirect('member/registration_non');
        }
    }

    // list
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/registration_non/list.html");
        // list opened form
        $rs_id = $this->m_registration->get_list_registration_open(array('bukan niaga', $this->com_user['airlines_id']));
        $data = array();
        foreach ($rs_id as $value) {
            // get detail rute
            $rs_rute = $this->m_registration->get_data_rute_by_id(array($value['data_id']));
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
                "published_no"  => $value['published_no'],
                "data_type"     => $value['data_type'],
                "data_flight"   => $value['data_flight'],
                "date_start"    => $value['date_start'],
                "date_end"      => $value['date_end'],
                "rute_all"      => $list_rute,
                "services_nm"   => $value['services_nm'],
                "flight_no"     => $value['flight_no'],
                "selisih_hari"  => $value['selisih_hari'],
                "selisih_waktu" => $value['selisih_waktu'],
            );
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add
    public function add($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // set template content
        $this->smarty->assign("template_content", "member/registration_non/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_registration->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('member/registration_non');
        }
        // assign
        $this->smarty->assign("result", $result);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_registration->get_services_cd(array($result['data_type'], $result['data_flight'])));
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
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('data_flight', 'Domestik / Internasional', 'trim|required');
        $this->tnotification->set_rules('services_cd', 'Service Code', 'trim|required');
        $this->tnotification->set_rules('registration_total', 'Jumlah Permohonan', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // data
            $params = array(
                "data_flight" => $this->input->post('data_flight'),
                "services_cd" => $this->input->post('services_cd'),
                "registration_total" => $this->input->post('registration_total')
            );
            // where
            $where = array(
                "data_id" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id']
            );
            // insert
            if ($this->m_registration->update($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("member/registration_non/form/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/registration_non/add/" . $this->input->post('data_id'));
    }

    // form fa
    public function form($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // set template content
        $this->smarty->assign("template_content", "member/registration_non/form.html");
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
        $result = $this->m_registration->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('member/registration_non');
        }
        // assign
        $result['data_flight'] = empty($result['data_flight']) ? 'domestik' : $result['data_flight'];
        $this->smarty->assign("result", $result);
        // --
        $this->smarty->assign("rs_airlines", $this->m_registration->get_all_airlines());
        $rs_airport = $this->m_registration->get_all_airport();
        $this->smarty->assign("rs_airport", $rs_airport);
        // data
        $data = "";
        foreach ($rs_airport as $value) {
            $data .= "{ id: '" . $value['airport_iata_cd'] . "', text: '" . $value['airport_iata_cd'] . " | " . $value['airport_nm'] . "'},";
        }
        $this->smarty->assign("data", $data);
        // get service code
        $this->smarty->assign("rs_service_code", $this->m_registration->get_all_service_code());
        // get hari pengajuan
        $this->smarty->assign("hari_pengajuan", $this->m_registration->get_hari_pengajuan(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // get remark field
        $this->smarty->assign("remark_field", $this->m_registration->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function form_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
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
            $this->tnotification->set_rules('date_start', 'Tanggal Masuk Indonesia ( dari )', 'trim');
            $this->tnotification->set_rules('date_start_upto', 'Tanggal Masuk Indonesia ( sampai )', 'trim');
            $this->tnotification->set_rules('date_end', 'Tanggal Keluar Indonesia ( dari )', 'trim');
            $this->tnotification->set_rules('date_end_upto', 'Tanggal Keluar Indonesia ( sampai )', 'trim');
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
        $this->tnotification->set_rules('catatan', 'Catatan', 'trim|maxlength[255]');
        $this->tnotification->set_rules('applicant', 'Pemohon', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('designation', 'Penunjukan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('registration_total', 'Jumlah Permohonan', 'trim|required|maxlength[2]');
        // rules for remark
        $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
        $result = $this->m_registration->get_detail_data_by_id($params);
        $rs_remark = $this->m_registration->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        foreach ($rs_remark as $value) {
            $this->tnotification->set_rules($value['rules_field'], $value['rules_name'], 'trim|required|maxlength[' . $value['rules_length'] . ']');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // $rute_all = $this->input->post('rute_all');
            // $total_rute = COUNT(explode(",", $rute_all));
            // if ($total_rute < 2) {
            //     // default error
            //     $this->tnotification->sent_notification("error", "Rute minimal 2 bandara");
            //     // default redirect
            //     redirect("member/registration_non/form/" . $this->input->post('data_id'));
            // }
            // get hari pengajuan
            $hari_pengajuan = $this->m_registration->get_hari_pengajuan(array($result['data_type'], $result['data_flight'], $result['services_cd']));
            if (!empty($hari_pengajuan)) {
                $date_now = new DateTime(date('Y-m-d'));
                $date_start = new DateTime($this->input->post('date_start'));
                $interval = $date_now->diff($date_start);
                $diff = $interval->format('%a');
                if (intval($diff) < $hari_pengajuan['batasan']) {
                    // default error
                    $this->tnotification->sent_notification("error", "Pengajuan minimal 3 hari sebelum penerbangan");
                    // default redirect
                    redirect("member/registration_non/form/" . $this->input->post('data_id'));
                }
            }
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
                "catatan" => $this->input->post('catatan'),
                "applicant" => $this->input->post('applicant'),
                "designation" => $this->input->post('designation'),
                "registration_total" => $this->input->post('registration_total'),
                "data_completed" => '1',
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s')
            );
            $where = array(
                "data_id" => $this->input->post('data_id'),
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update
            if ($this->m_registration->update($params, $where)) {
                $this->m_registration->delete_rute(array($this->input->post('data_id')));
                // update rute batch
                $id = $this->m_registration->get_data_id();
                $rute_all = $this->input->post('rute_all');
                $rute_all = explode("-", $rute_all);
                $x = 1;
                foreach ($rute_all as $value) {
                    $data[] = array(
                        "id" => $id . $x,
                        "data_id" => $this->input->post('data_id'),
                        "seq" => $x++,
                        "airport_id" => $value
                    );
                }
                $this->m_registration->update_rute($data);
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect`
                redirect("member/registration_non/files/" . $this->input->post('data_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("member/registration_non/form/" . $this->input->post('data_id'));
    }

    // files attachment
    public function files($data_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // set template content
        $this->smarty->assign("template_content", "member/registration_non/files.html");
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_registration->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('member/registration_non');
        }
        $this->smarty->assign("result", $result);
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
    public function files_process() {
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // data id
        $data_id = $this->input->post('data_id');
        // get detail data
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_registration->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('member/registration_non');
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
        redirect("member/registration_non/files/" . $data_id);
    }

    // send process
    public function send_process() {
        // cek input
        $this->tnotification->set_rules('data_id', 'ID', 'trim|required');
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // data id
        $data_id = $this->input->post('data_id');
        $params = array($data_id, $this->com_user['airlines_id']);
        $result = $this->m_registration->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('member/registration_non');
        }
        // validation
        if (!$this->m_files->is_file_completed(array($data_id, $result['data_type'], $result['data_flight'], $result['services_cd']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('data_id'), $this->com_user['airlines_id']);
            $result = $this->m_registration->get_detail_data_by_id($params);
            if ($result['data_flight'] == 'domestik') {
                $flow_id = 51;
            } else {
                $flow_id = 61;
            }
            // jalankan
            if ($result['data_completed'] == '1') {
                // process flow
                $process_id = $this->m_registration->get_data_id();
                $params = array($process_id, $data_id, $flow_id, $this->com_user['user_id']);
                $this->m_registration->insert_process($params);
                // update status & create number
                $code = ($result['data_flight'] == 'domestik') ? 'AUBNDN' : 'AUBNLN';
                $doc_no = $this->m_registration->get_document_number_bukan_niaga($code);
                $this->m_registration->update_status_data(array('waiting', $doc_no, $data_id, $this->com_user['airlines_id']));
                // digandakan sisanya
                for ($i = 1; $i < $result['registration_total']; $i++) {
                    // data id
                    $new_data_id = $this->m_registration->get_data_id() . $i;
                    $doc_no = $this->m_registration->get_document_number_bukan_niaga($code);
                    // insert new data
                    $this->m_registration->insert_data_tidak_berjadwal(array($new_data_id, $doc_no, $data_id));
                }
                // send mail
                if ($result['data_flight'] == 'domestik') {
                    $this->m_email->mail_to_all_auntbdn($this->input->post('data_id'), $this->com_user['airlines_id']);
                } else {
                    $this->m_email->mail_to_all_auntbln($this->input->post('data_id'), $this->com_user['airlines_id']);
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
        redirect("member/registration_non/files/" . $data_id);
    }

    // delete process
    public function delete($data_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // delete
        $params = array($data_id, $this->com_user['airlines_id']);
        if ($this->m_registration->delete($params)) {
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("member/registration_non");
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_files->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect('member/registration_non');
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
            redirect('member/registration_non');
        }
    }

    // disclaimer
    public function disclaimer() {
        // set page rules
        $this->_set_page_rule("R");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // set template content
        $this->smarty->assign("template_content", "member/registration_non/disclaimer.html");
        // get disclaimer list
        $this->smarty->assign("rs_id", $this->m_disclaimer->get_list_disclaimer());
        //set captcha
        $this->load->helper("captcha");
        $vals = array(
            'img_path' => FCPATH . '/resource/doc/captcha/',
            'img_url' => base_url() . '/resource/doc' . '/captcha/',
            'img_width' => '150',
            'font_path' => FCPATH . '/resource/doc/font/COURIER.TTF',
            'font_size' => 60,
            'img_height' => 70,
            'expiration' => 7200
        );
        $captcha = create_captcha($vals);
        $data = array(
            'captcha_time' => $captcha['time'],
            'ip_address' => $_SERVER["REMOTE_ADDR"],
            'word' => $captcha['word']
        );
        $this->session->set_userdata($data);
        $this->smarty->assign("captcha", $captcha);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // disclaimer process
    public function disclaimer_process() {
        // set page rules
        $this->_set_page_rule("R");
        // cek block status
        $block_st = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id']));
        if ($block_st) {
            $this->tnotification->sent_notification("error", "Anda tidak dapat melakukan proses pada halaman registrasi karena Airlines anda dalam status blokir");
            // default redirect
            redirect("member/registration_non");
        }
        // cek input
        $this->tnotification->set_rules('agree', 'Agreement', 'trim|required');
        $this->tnotification->set_rules('captcha', 'Captcha', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $captcha = $this->input->post('captcha');
            $expiration = time() - 7200;
            if ($this->session->userdata('word') == $captcha AND $this->session->userdata('ip_address') == $_SERVER["REMOTE_ADDR"] AND $this->session->userdata('captcha_time') > $expiration) {
                // send
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                // redirect
                redirect("member/registration_non/create/");
            } else {
                $this->tnotification->sent_notification("error", "Captcha tidak sesuai");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // redirect
        redirect("member/registration_non/disclaimer/");
    }

    /* ---------- ajax services code ---------- */

    // get services code
    public function get_services_cd() {
        // set page rules
        $this->_set_page_rule("R");
        // get services code
        $params = array(
            "data_type"     => $this->input->post('data_type'),
            "data_flight"   => $this->input->post('data_flight'),
        );
        $result = $this->m_registration->get_services_cd($params);
        echo json_encode($result);
    }

}
