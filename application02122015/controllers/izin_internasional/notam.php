<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class notam extends ApplicationBase {

    private $group_id = 29;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_izin');
        $this->load->model('m_email');
        $this->load->model('m_airport');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
        //load helper
        $this->load->library('doslibrary');
        $this->smarty->assign("dos",  $this->doslibrary);
        $this->load->library("score");
    }

    // add
    // modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function index($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['izin_rute_start']);
        $params = array($orig.','.$dest);
        $rs_airport = $this->m_airport->get_airport_score_by_code($params);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("airport", $rs_airport);
        $this->smarty->assign("detail", $result);
        // list izin rute
        $rs_id = $this->m_izin->get_list_izin_rute_by_perusahaan(array($this->com_user['airlines_id'], 'internasional'));
        foreach ($rs_id as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id as $k2 => $v2) {
                if ($last_key == $v2['izin_id']) $group_izin++;
            }
            $rs_id[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_id", $rs_id);
        // total frek
        $this->smarty->assign("tot_frek", $this->m_izin->get_total_frekuensi(array($registrasi_id)));
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
        $this->tnotification->set_rules('registrasi_id', 'ID Registrasi', 'trim|required');
        $this->tnotification->set_rules('izin_request_letter_date', 'Tanggal Surat Permohonan', 'trim|required');
        $this->tnotification->set_rules('izin_request_letter', 'Nomor Surat Permohonan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('penanggungjawab', 'Penanggungjawab', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('kode_izin', 'Kode Rute', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('registrasi_id'), $this->com_user['airlines_id'], $this->group_id);
            $result = $this->m_izin->get_registrasi_by_id($params);
            // jika sama
            if ($result['kode_izin'] != $this->input->post('kode_izin')) {
                // delete izin rute
                $this->m_izin->delete_rute_by_registrasi($this->input->post('registrasi_id'));
            }
            // get kode izin
            $izin = $this->m_izin->get_rute_by_kode_izin($this->input->post('kode_izin'));
            $this->smarty->assign("izin", $izin);
            // update registrasi
            $params = array(
                "izin_request_letter_date" => $this->input->post('izin_request_letter_date'),
                "izin_request_letter" => $this->input->post('izin_request_letter'),
                "izin_rute_start" => $izin['izin_rute_start'],
                "izin_rute_end" => $izin['izin_rute_end'],
                "kode_izin" => $this->input->post('kode_izin'),
                "penanggungjawab" => $this->input->post('penanggungjawab'),
                "jabatan" => $this->input->post('jabatan'),
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "airlines_id" => $this->com_user['airlines_id'],
                "izin_request_st" => '0',
            );

            // insert
            if ($this->m_izin->update_izin_permohonan($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("izin_internasional/notam/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/index/" . $this->input->post('registrasi_id'));
    }

    // list data rute
    // modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/list.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['izin_rute_start']);
        $params = array($orig.','.$dest);
        $rs_airport = $this->m_airport->get_airport_score_by_code($params);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("airport", $rs_airport);
        $this->smarty->assign("detail", $result);
        // data sebelumnya
        $rs_id = $this->m_izin->get_list_data_rute_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        foreach ($rs_id as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id as $k2 => $v2) {
                if ($last_key == $v2['izin_id']) $group_izin++;
            }
            $rs_id[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_id", $rs_id);
		// total frek
        $this->smarty->assign("tot_frek", $this->m_izin->get_total_frekuensi(array($registrasi_id)));
        
        // data terpilih
        $izin_selected = array();
        $rs_selected = $this->m_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        foreach ($rs_selected as $selected) {
            $rs_new[$selected['rute_all']] = array(
                "rute_all"              => $selected['rute_all'],
                "aircraft_type"         => $selected['aircraft_type'],
                "aircraft_capacity"     => $selected['aircraft_capacity'],
                "flight_no"             => $selected['flight_no'],
                "etd"                   => $selected['etd'],
                "eta"                   => $selected['eta'],
                "dos"                   => $selected['dos'],
                "frekuensi"             => $selected['frekuensi'],
                "pairing"             => $selected['pairing'],
                "izin_id"             => $selected['izin_id'],
            );
            $izin_selected[str_replace('-', '_', $selected['kode_frekuensi'])] = $selected['kode_frekuensi'];
        }
        foreach ($rs_new as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_new as $k2 => $v2) {
                if ($last_key == $v2['izin_id']) $group_izin++;
            }
            $rs_new[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_new", $rs_new);
        $this->smarty->assign("izin_selected", $izin_selected);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    // modified by: sanjaya.im@gmail.com
    // modified on: 15-May-2015
    // reason modified: add some logic to prevent entering step "Slot Clearance" when already use SCORE service
    public function list_rute_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        // validasi
        $rs_id = $this->m_izin->get_list_data_rute_by_id(array($this->input->post('registrasi_id'), $this->com_user['airlines_id']));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Belum ada data rute yang diinputkan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('registrasi_id'), $this->com_user['airlines_id'], $this->group_id);
            $rs_izin = $this->m_izin->get_registrasi_by_id($params);
            if (empty($rs_izin)) {
                redirect('member/registration_internasional');
            }
            // get detail data airport registration
            list($orig,$dest) = explode("-", $rs_izin['izin_rute_start']);
            $params = array($orig.','.$dest);
            $rs_airport = $this->m_airport->get_airport_score_by_code($params);
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect
            if (@intval($rs_airport['is_all_used_score']) !== 1){
                redirect("izin_internasional/notam/list_slot/" . $this->input->post('registrasi_id'));
            }else{
                redirect("izin_internasional/notam/list_files/" . $this->input->post('registrasi_id'));
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/list_rute/" . $this->input->post('registrasi_id'));
    }

    // add rute
    // modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function rute_edit($registrasi_id = "", $kode_frekuensi = "", $mode = "") {
        // set rules
        $this->_set_page_rule('R');
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("select2/select2.css");
        // get total notam frekuensi
        $params = array($kode_frekuensi, $kode_frekuensi);
        $result = $this->m_izin->get_total_notam_frekuensi($params);
        if (empty($result) OR ($result['status'] == 'reject' && @intval($result['total'])>0)) {
            // default error
            $this->tnotification->sent_notification("error", "Frekuensi sudah dirubah sebanyak 2 kali");
            redirect('izin_internasional/notam/list_rute/' . $registrasi_id);
        }
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        $izin_request_date = str_replace("-","",$result['izin_request_letter_date']);
        $this->smarty->assign("detail", $result);
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['izin_rute_start']);
        $params = array($orig);
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $this->smarty->assign("airport1", $rs_airport1);
        
		// tambah pengecekan apabila ada airport yang belum terdaftar pada database
		if($rs_airport1['airport_iata_cd'] == '')
			$rs_airport1['airport_iata_cd'] = $orig;
		
        $params = array($dest);
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);
        $this->smarty->assign("airport2", $rs_airport2);
        /*
         * LAMA
         */
        // detail lama
        $result_lama = $this->m_izin->get_izin_rute_by_kode_frekuensi_active(array($kode_frekuensi, $this->com_user['airlines_id']));
        if (empty($result_lama)) {
            redirect('izin_internasional/notam/list_rute/' . $registrasi_id);
        }
        $this->smarty->assign("izin_start_date", $result_lama['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $result_lama['izin_expired_date']);
        // rute lama
        $rs_id_lama = $this->m_izin->get_list_data_rute_by_kode_frekuensi(array($kode_frekuensi, $this->com_user['airlines_id']));
        foreach ($rs_id_lama as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id_lama as $k2 => $v2) {
                if ($last_key == $v2['izin_id']) $group_izin++;
            }
            $rs_id_lama[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_id_lama", $rs_id_lama);
        /*
         * PERUBAHAN
         */
        // detail rute by frekuensi yang akan diubah
        $result = $this->m_izin->get_izin_rute_by_kode_frekuensi(array($kode_frekuensi, $this->com_user['airlines_id'], 'notam', $registrasi_id));
        if (empty($result)) {
            $result = $result_lama;
        }
        // detail rute
        $lama = 0;
        $rs_id = $this->m_izin->get_izin_rute_data_by_id(array($result['izin_id'], $this->com_user['airlines_id']));
        if (empty($rs_id)) {
            $rs_id = $rs_id_lama;
            $lama = 1;
        } else {
            foreach ($rs_id as $k1 => $v1) {
                $last_key = $v1['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $k2 => $v2) {
                    if ($last_key == $v2['izin_id']) $group_izin++;
                }
                $rs_id[$k1]['rowspan'] = $group_izin;
            }
        }
        // SCORE Service
        $this->smarty->assign("mode", $mode);
        if ($mode != "form" && ($rs_airport1['is_used_score'] == 1 || $rs_airport2['is_used_score'] == 1)){
            $received = true;
            try {
                $client = new SCORE_Service();
                //request
                $params = new getConfirmedSlot($this->com_user['airlines_iata_cd'], $orig, $dest, "", $izin_request_date, "*");
                $score = $client->getConfirmedFullSlot($params, @intval($rs_airport1['airport_utc']), @intval($rs_airport2['airport_utc']));
                // $score_tmp = $score;
                // unset($score);
                // foreach ($score_tmp as $k => $v) {
                //     $params = array($v['rute_all'], $v['flight_no'], $v['aircraft_type'], $v['dos']);
                //     $rs = $this->m_izin->get_list_rute_by_rute_flightno_aircraft_dos($params, "NOT FIND_IN_SET(a.izin_id,'".$result_lama['izin_id'].",".$result['izin_id']."')");
                //     if (empty($rs)){
                //         // rule: each DOS same with old DOS or ZERO
                //         $dos = str_split($v['dos'], 1);
                //         $dos_lama = str_split($result_lama['dos'], 1);
                //         $valid_dos = 0;
                //         for ($x=0; $x < count($dos); $x++)
                //             if ($dos[$x]==$dos_lama[$x] || ($dos[$x]!=$dos_lama[$x] && $dos[$x]=="0")) $valid_dos++;
                //         if ($valid_dos == 7) $score[] = $v;
                //     }
                // }
            } catch (Exception $error) {
                if (ENVIRONMENT == "testing") {
                    var_dump($error); die;
                }
                unset($score);
                $received = false;
            }
            $this->smarty->assign("template_content", "izin_internasional/notam/edit_score.html");
            $this->smarty->assign("score", $score);
        } else {
            if ($rs_airport1['is_used_score'] == 0 && $rs_airport2['is_used_score'] == 0){
                $this->smarty->assign("template_content", "izin_internasional/notam/edit.html");
                // dos
                $dos = array();
                for ($i = 1; $i <= 7; $i++) {
                    $dos[$i] = substr($result['dos'], $i - 1, 1);
                }
                $result['dos'] = $dos;
                // assign
                $this->smarty->assign("dos", $result['dos']);
                if ($result['pairing'] == 'VV') {
                    $no = 11;
                    if ($rs_id[0]['rute_all'] != $result['izin_rute_start']){
                        // switch
                        $tmp = $rs_id[0];
                        $rs_id[0] = $rs_id[1];
                        $rs_id[1] = $tmp;
                    }
                } else {
                    $no = '';
                }
                foreach ($rs_id as $data) {
                    $result['rute_all' . $no] = $data['rute_all'];
                    $result['flight_no' . $no] = $data['flight_no'];
                    $result['etd' . $no] = $data['etd'];
                    $result['eta' . $no] = $data['eta'];
                    if ($lama == 1) {
                        $result['slot_number' . $no] = '';
                        $result['slot_date' . $no] = '';
                    } else {
                        $result['slot_number' . $no] = $data['slot_number'];
                        $result['slot_date' . $no] = $data['slot_date'];
                    }
                    list($orig,$dest) = explode("-", $data['rute_all']);
                    if ($rs_airport1['airport_iata_cd'] == $orig) {
                        $result['ao_is_used_score' . $no] = $rs_airport1['is_used_score'];
                        $result['ad_is_used_score' . $no] = $rs_airport2['is_used_score'];
                    }
                    if ($rs_airport2['airport_iata_cd'] == $dest) {
                        $result['ao_is_used_score' . $no] = $rs_airport2['is_used_score'];
                        $result['ad_is_used_score' . $no] = $rs_airport1['is_used_score'];
                    }
                    if ($result['pairing'] == 'VV') {
                        $no++;
                    }
                }
            } else {
                $this->smarty->assign("template_content", "izin_internasional/notam/edit_multi.html");
                foreach ($rs_id as $k1 => $v1) {
                    list($orig,$dest) = explode("-", $v1['rute_all']);
                    if ($rs_airport1['airport_iata_cd'] == $orig) {
                        $rs_id[$k1]['ao_used_score'] = $rs_airport1['is_used_score'];
                        $rs_id[$k1]['ad_used_score'] = $rs_airport2['is_used_score'];
                    } else {
                        $rs_id[$k1]['ao_used_score'] = $rs_airport2['is_used_score'];
                        $rs_id[$k1]['ad_used_score'] = $rs_airport1['is_used_score'];
                    }
                    // doop
                    $doop = array();
                    for ($i = 1; $i <= 7; $i++) {
                        $doop[$i] = substr($v1['doop'], $i - 1, 1);
                    }
                    $rs_id[$k1]['doop'] = $doop;
                }
            }
        }
        $this->smarty->assign("result", $result);
        $this->smarty->assign("rute", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // untuk hak akses
        $data = $this->tnotification->get_field_data();
        if (isset($data['dos[]']['postdata'])) {
            if (!empty($data['dos[]']['postdata'])) {
                // dos
                $this->smarty->assign("dos", $data['dos[]']['postdata']);
            }
        }
        // output
        parent::display();
    }

    // edit rute next
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function edit_rute_score_next(){
        // set rules
        $this->_set_page_rule('U');
        // cek input
        // get detail data
        $registrasi_id = $this->input->post('registrasi_id');
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $rs_izin = $this->m_izin->get_registrasi_by_id($params);
        if (empty($rs_izin)) {
            redirect('member/registration_internasional');
        }
        // get detail data airport registration
        list($orig,$dest) = explode("-", $rs_izin['izin_rute_start']);
        $params = array($orig);
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $params = array($dest);
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);

        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('kode_izin', 'Kode Izin', 'trim|required');
        $this->tnotification->set_rules('kode_frekuensi', 'Kode Frekuensi', 'trim|required');
        $this->tnotification->set_rules('izin_rute_start', 'Rute Pergi');
        $this->tnotification->set_rules('izin_rute_end', 'Rute Pulang');
        
        $kode_izin = $this->input->post('kode_izin');
        $kode_frekuensi = $this->input->post('kode_frekuensi');
        $izin_rute_start = $this->input->post('izin_rute_start');
        $izin_rute_end = $this->input->post('izin_rute_end');
        // rute wajib diisi
        $this->tnotification->set_rules('pairing', 'required');
        $this->tnotification->set_rules('slot_selected', 'required');
        $this->tnotification->set_rules('aircraft_type[]', 'Tipe Pesawat', 'trim|required');
        $this->tnotification->set_rules('rute_all[]', 'Rute', 'trim|required');
        $this->tnotification->set_rules('flight_no[]', 'Nomor Penerbangan', 'trim|required');
        $this->tnotification->set_rules('etd[]', 'ETD', 'trim');
        $this->tnotification->set_rules('eta[]', 'ETA', 'trim');
        $this->tnotification->set_rules('dos[]', 'DOS', 'trim|required');
        $this->tnotification->set_rules('izin_start_date[]', 'Tanggal Mulai Efektif', 'trim|required');
        $this->tnotification->set_rules('izin_expired_date[]', 'Tanggal Akhir Efektif', 'trim|required');
        $this->tnotification->set_rules('ron[]', 'RON', 'trim');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $pairing = $this->input->post('pairing');
            $slot_selected = $this->input->post('slot_selected');
            $rute_all = $this->input->post('rute_all');
            $aircraft_type = $this->input->post('aircraft_type');
            $aircraft_capacity = $this->input->post('aircraft_capacity');
            $izin_start_date = $this->input->post('izin_start_date');
            $izin_expired_date = $this->input->post('izin_expired_date');
            $izin_rute_start = $this->input->post('izin_rute_start');
            $izin_rute_end = $this->input->post('izin_rute_end');
            $flight_no = $this->input->post('flight_no');
            $etd = $this->input->post('etd');
            $eta = $this->input->post('eta');
            $dos_input = $this->input->post('dos');
            $ron = $this->input->post('ron');
            $schedule = array();
            foreach ($slot_selected as $k => $v) {
                $index = $v - 1;
                $schedule[] = array(
                    "row_selected" => $index,
                    "aircraft_type" => $aircraft_type[$index],
                    "aircraft_capacity" => $aircraft_capacity[$index],
                    "izin_start_date" => $izin_start_date[$index],
                    "izin_expired_date" => $izin_expired_date[$index],
                    "izin_rute_start" => $izin_rute_start,
                    "izin_rute_end" => $izin_rute_end,
                    "dos" => $dos_input[$index],
                    "ron" => @intval($ron[$index]),
                    "pairing" => $pairing,
                    "rute_all" => $rute_all[$index],
                    "flight_no" => $flight_no[$index],
                    "etd" => $etd[$index],
                    "eta" => $eta[$index]
                );
            }
            if ($pairing == "VV"){
                $score = array();
                // rebuild SCORE list
                foreach ($flight_no as $k1 => $v1) {
                    foreach ($schedule as $k2 => $v2) {
                        if ($v2['flight_no'] == $flight_no[$k1]) continue;
                        // if ($v2['izin_start_date'] != $izin_start_date[$k1]) continue;
                        // if ($v2['izin_expired_date'] != $izin_expired_date[$k1]) continue;
                        if ($v2['rute_all'] == $rute_all[$k1]) continue;                        
                        $score[] = array(
                            'aircraft_type' => $aircraft_type[$k1],
                            'aircraft_capacity' => $aircraft_capacity[$k1],
                            'izin_start_date' => $izin_start_date[$k1],
                            'izin_expired_date' => $izin_expired_date[$k1],
                            'dos' => $dos_input[$k1],
                            'ron' => $ron[$k1],
                            'rute_all' => $rute_all[$k1],
                            'flight_no' => $flight_no[$k1],
                            'etd' => $etd[$k1],
                            'eta' => $eta[$k1]
                            );
                    }
                }
                $score = array_map("unserialize", array_unique(array_map("serialize", $score)));
                // show next step
                $this->smarty->assign("template_content", "izin_internasional/notam/edit_score_next.html");
                // load javascript
                $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
                $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
                $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
                // load style ui
                $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
                $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
                $this->smarty->load_style("select2/select2.css");
                // get detail data
                $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
                $result = $this->m_izin->get_registrasi_by_id($params);
                if (empty($result)) {
                    redirect('member/registration_internasional');
                }
                $this->smarty->assign("detail", $result);   
                /*
                 * LAMA
                 */
                // detail lama
                $result_lama = $this->m_izin->get_izin_rute_by_kode_frekuensi_active(array($kode_frekuensi, $this->com_user['airlines_id']));
                if (empty($result_lama)) {
                    redirect('izin_internasional/notam/list_rute/' . $registrasi_id);
                }
                $this->smarty->assign("izin_start_date", $result_lama['izin_start_date']);
                $this->smarty->assign("izin_expired_date", $result_lama['izin_expired_date']);
                // rute lama
                $rs_id_lama = $this->m_izin->get_list_data_rute_by_kode_frekuensi(array($kode_frekuensi, $this->com_user['airlines_id']));
                foreach ($rs_id_lama as $k1 => $v1) {
                    $last_key = $v1['izin_id'];
                    $group_izin = 0;
                    foreach ($rs_id_lama as $k2 => $v2) {
                        if ($last_key == $v2['izin_id']) $group_izin++;
                    }
                    $rs_id_lama[$k1]['rowspan'] = $group_izin;
                }
                $this->smarty->assign("rs_id_lama", $rs_id_lama);        
                /*
                 * PERUBAHAN
                 */
                // detail rute by frekuensi yang akan diubah
                $result = $this->m_izin->get_izin_rute_by_kode_frekuensi(array($kode_frekuensi, $this->com_user['airlines_id'], 'notam', $registrasi_id));
                if (empty($result)) {
                    $result = $result_lama;
                }
                $this->smarty->assign("schedule", $schedule);
                $this->smarty->assign("score", $score);
                $this->smarty->assign("result", $result);
                // notification
                $this->tnotification->display_notification();
                $this->tnotification->display_last_field();
                // untuk hak akses
                $data = $this->tnotification->get_field_data();
                if (isset($data['dos[]']['postdata'])) {
                    if (!empty($data['dos[]']['postdata'])) {
                        // dos
                        $this->smarty->assign("dos", $data['dos[]']['postdata']);
                    }
                }
                // output
                parent::display();
            } elseif ($pairing == "OW"){
                // delete izin rute by frekuensi and registrasi id
                $params = array($registrasi_id, $kode_frekuensi, $this->com_user['airlines_id']);
                $this->m_izin->delete_rute_by_registrasi_kode_frekuensi($params);
                $izin_id = $this->m_izin->get_data_id();
                unset($rute_schedule);
                foreach ($schedule as $k => $v) {
                    if ($k == 0) {
                        $rute_schedule = $v;
                        continue;
                    }
                    $dos = str_split($rute_schedule['dos'], 1);
                    $dos_tmp = str_split($v['dos'], 1);
                    for ($x=0; $x < count($dos_tmp); $x++)
                        if ($dos_tmp[$x]!="0") $dos[$x]=$dos_tmp[$x];
                    $rute_schedule['dos'] = implode('',$dos);
                }
                $params = array(
                    'izin_id' => $izin_id,
                    'airlines_id' => $this->com_user['airlines_id'],
                    'registrasi_id' => $this->input->post('registrasi_id'),
                    'izin_flight' => 'internasional',
                    'izin_st' => 'notam',
                    'kode_izin' => $kode_izin,
                    'kode_frekuensi' => $kode_frekuensi,
                    'aircraft_type' => $rute_schedule['aircraft_type'],
                    'aircraft_capacity' => $rute_schedule['aircraft_capacity'],
                    "izin_start_date" => $rute_schedule['izin_start_date'],
                    "izin_expired_date" => $rute_schedule['izin_expired_date'],
                    'izin_rute_start' => $rute_schedule['izin_rute_start'],
                    'izin_rute_end' => $rute_schedule['izin_rute_end'],
                    'dos' => $rute_schedule['dos'],
                    'ron' => $rute_schedule['ron'],
                    'pairing' => "OW",
                    'mdb' => $this->com_user['user_id'],
                    'mdd' => date('Y-m-d h:i:s'),
                    'is_used_score' => 1
                );
                // update
                if ($this->m_izin->insert_rute($params)) {
                    // delete
                    $this->m_izin->delete_rute_data(array($izin_id, $this->com_user['airlines_id']));
                    // Only OW when using SCORE
                    foreach ($schedule as $k => $v) {
                        unset($params);
                        $params = array(
                            'rute_id' => $izin_id . '1' . $k,
                            'izin_id' => $izin_id,
                            'rute_all' => $v['rute_all'],
                            'flight_no' => $v['flight_no'],
                            'doop' => $v['dos'],
                            'start_date' => $v['izin_start_date'],
                            'end_date' => $v['izin_expired_date'],
                            'is_used_score' => 1
                        );
                        if ($v['etd'] != "") $params['etd']=$v['etd'];
                        else $params['is_used_score']=2;
                        if ($v['eta'] != "") $params['eta']=$v['eta'];
                        else $params['is_used_score']=2;
                        $this->m_izin->insert_rute_data($params);
                    }
                    // notification
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    if ($rs_airport1['is_used_score'] != 1 || $rs_airport2['is_used_score'] != 1) redirect("izin_internasional/notam/rute_edit/" . $registrasi_id . "/" . $kode_frekuensi . "/form");
                } else {
                    // default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                }
                // redirect
                redirect("izin_internasional/notam/list_rute/" . $registrasi_id);
            }            
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/rute_edit/" . $registrasi_id . '/' . $kode_frekuensi);
    }

    // edit rute score process
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function edit_score_process(){
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('kode_izin', 'Kode Izin', 'trim|required');
        $this->tnotification->set_rules('kode_frekuensi', 'Kode Frekuensi', 'trim|required');
        $this->tnotification->set_rules('izin_rute_start', 'Rute Pergi');
        $this->tnotification->set_rules('izin_rute_end', 'Rute Pulang');
        $registrasi_id = $this->input->post('registrasi_id');
        $kode_izin = $this->input->post('kode_izin');
        $kode_frekuensi = $this->input->post('kode_frekuensi');
        $izin_rute_start = $this->input->post('izin_rute_start');
        $izin_rute_end = $this->input->post('izin_rute_end');

        // rute wajib diisi
        $this->tnotification->set_rules('vv_aircraft_type', 'required');
        $this->tnotification->set_rules('vv_aircraft_capacity', 'required');
        $this->tnotification->set_rules('vv_izin_start_date', 'required');
        $this->tnotification->set_rules('vv_izin_expired_date', 'required');
        $this->tnotification->set_rules('vv_dos', 'required');
        $this->tnotification->set_rules('vv_ron', 'required');
        $this->tnotification->set_rules('vv_pairing', 'required');
        $this->tnotification->set_rules('vv_rute_all', 'required');
        $this->tnotification->set_rules('vv_flight_no', 'required');
        $this->tnotification->set_rules('vv_etd', 'required');
        $this->tnotification->set_rules('vv_eta', 'required');

        $this->tnotification->set_rules('slot_selected', 'required');
        $this->tnotification->set_rules('aircraft_type[]', 'Tipe Pesawat', 'trim|required');
        $this->tnotification->set_rules('rute_all[]', 'Rute', 'trim|required');
        $this->tnotification->set_rules('flight_no[]', 'Nomor Penerbangan', 'trim|required');
        $this->tnotification->set_rules('etd[]', 'ETD', 'trim');
        $this->tnotification->set_rules('eta[]', 'ETA', 'trim');
        $this->tnotification->set_rules('dos[]', 'DOS', 'trim|required');
        $this->tnotification->set_rules('izin_start_date[]', 'Tanggal Mulai Efektif', 'trim|required');
        $this->tnotification->set_rules('ron[]', 'RON', 'trim');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // insert izin rute
            $vv_aircraft_type = $this->input->post('vv_aircraft_type');
            $vv_aircraft_capacity = $this->input->post('vv_aircraft_capacity');
            $vv_izin_start_date = $this->input->post('vv_izin_start_date');
            $vv_izin_expired_date = $this->input->post('vv_izin_expired_date');
            $vv_dos = $this->input->post('vv_dos');
            $vv_ron = $this->input->post('vv_ron');
            $vv_pairing = $this->input->post('vv_pairing');
            $vv_rute_all = $this->input->post('vv_rute_all');
            $vv_flight_no = $this->input->post('vv_flight_no');
            $vv_etd = $this->input->post('vv_etd');
            $vv_eta = $this->input->post('vv_eta');

            $slot_selected = $this->input->post('slot_selected');
            $rute_all = $this->input->post('rute_all');
            $aircraft_type = $this->input->post('aircraft_type');
            $aircraft_capacity = $this->input->post('aircraft_capacity');
            $izin_start_date = $this->input->post('izin_start_date');
            $izin_expired_date = $this->input->post('izin_expired_date');
            $izin_rute_start = $this->input->post('izin_rute_start');
            $izin_rute_end = $this->input->post('izin_rute_end');
            $flight_no = $this->input->post('flight_no');
            $etd = $this->input->post('etd');
            $eta = $this->input->post('eta');
            $dos_input = $this->input->post('dos');
            $ron = $this->input->post('ron');

            // get detail data airport registration
            list($orig,$dest) = explode("-", $izin_rute_start);
            $params = array($orig);
            $rs_airport1 = $this->m_airport->get_airport_by_code($params);
            $params = array($dest);
            $rs_airport2 = $this->m_airport->get_airport_by_code($params);
            
            // pergi
            foreach ($vv_izin_start_date as $index => $v) {
                $schedule1[] = array(
                    "row_selected" => $index,
                    "aircraft_type" => $vv_aircraft_type[$index],
                    "aircraft_capacity" => $vv_aircraft_capacity[$index],
                    "izin_start_date" => $vv_izin_start_date[$index],
                    "izin_expired_date" => $vv_izin_expired_date[$index],
                    "izin_rute_start" => $vv_izin_start_date[$index],
                    "izin_rute_end" => $vv_izin_expired_date[$index],
                    "dos" => $vv_dos[$index],
                    "ron" => @intval($vv_ron[$index]),
                    "pairing" => $pairing,
                    "rute_all" => $vv_rute_all[$index],
                    "flight_no" => $vv_flight_no[$index],
                    "etd" => $vv_etd[$index],
                    "eta" => $vv_eta[$index]
                );
            }
            unset($rute_schedule1);
            foreach ($schedule1 as $k => $v) {
                if ($k == 0) {
                    $rute_schedule1 = $v;
                    continue;
                }
                $dos = str_split($rute_schedule1['dos'], 1);
                $dos_tmp = str_split($v['dos'], 1);
                for ($x=0; $x < count($dos_tmp); $x++)
                    if ($dos_tmp[$x]!="0") $dos[$x]=$dos_tmp[$x];
                $rute_schedule1['dos'] = implode('',$dos);
                if(strtotime($rute_schedule1['izin_start_date']) > strtotime($v['izin_start_date'])) $rute_schedule1['izin_start_date']=$v['izin_start_date'];
                if(strtotime($rute_schedule1['izin_expired_date']) < strtotime($v['izin_expired_date'])) $rute_schedule1['izin_expired_date']=$v['izin_expired_date'];
            }
            // pulang
            foreach ($slot_selected as $k => $v) {
                $index = $v - 1;
                $schedule2[] = array(
                    "row_selected" => $index,
                    "aircraft_type" => $aircraft_type[$index],
                    "aircraft_capacity" => $aircraft_capacity[$index],
                    "izin_start_date" => $izin_start_date[$index],
                    "izin_expired_date" => $izin_expired_date[$index],
                    "izin_rute_start" => $izin_rute_start,
                    "izin_rute_end" => $izin_rute_end,
                    "dos" => $dos_input[$index],
                    "ron" => @intval($ron[$index]),
                    "pairing" => $pairing,
                    "rute_all" => $rute_all[$index],
                    "flight_no" => $flight_no[$index],
                    "etd" => $etd[$index],
                    "eta" => $eta[$index]
                );
            }
            unset($rute_schedule2);
            foreach ($schedule2 as $k => $v) {
                if ($k == 0) {
                    $rute_schedule2 = $v;
                    continue;
                }
                $dos = str_split($rute_schedule2['dos'], 1);
                $dos_tmp = str_split($v['dos'], 1);
                for ($x=0; $x < count($dos_tmp); $x++)
                    if ($dos_tmp[$x]!="0") $dos[$x]=$dos_tmp[$x];
                $rute_schedule2['dos'] = implode('',$dos);
                if(strtotime($rute_schedule2['izin_start_date']) > strtotime($v['izin_start_date'])) $rute_schedule2['izin_start_date']=$v['izin_start_date'];
                if(strtotime($rute_schedule2['izin_expired_date']) < strtotime($v['izin_expired_date'])) $rute_schedule2['izin_expired_date']=$v['izin_expired_date'];
            }

            // delete izin rute by frekuensi and registrasi id
            $params = array($registrasi_id, $kode_frekuensi, $this->com_user['airlines_id']);
            $this->m_izin->delete_rute_by_registrasi_kode_frekuensi($params);
            $izin_id = $this->m_izin->get_data_id();
            $params = array(
                'izin_id' => $izin_id,
                'airlines_id' => $this->com_user['airlines_id'],
                'registrasi_id' => $this->input->post('registrasi_id'),
                'izin_flight' => 'internasional',
                'izin_st' => 'notam',
                'kode_izin' => $kode_izin,
                'kode_frekuensi' => $kode_frekuensi,
                'aircraft_type' => $rute_schedule1['aircraft_type'],
                'aircraft_capacity' => (@intval($rute_schedule1['aircraft_capacity']) > @intval($rute_schedule2['aircraft_capacity'])) ? $rute_schedule1['aircraft_capacity'] : $rute_schedule2['aircraft_capacity'],
                'izin_start_date' => ($rute_schedule1['izin_start_date'] > $rute_schedule2['izin_start_date']) ? $rute_schedule2['izin_start_date'] : $rute_schedule1['izin_start_date'],
                'izin_expired_date' => ($rute_schedule1['izin_expired_date'] < $rute_schedule2['izin_expired_date']) ? $rute_schedule2['izin_expired_date'] : $rute_schedule1['izin_expired_date'],
                'izin_rute_start' => $izin_rute_start,
                'izin_rute_end' => $izin_rute_end,
                'dos' => $this->m_izin->get_higher_dos($rute_schedule1['dos'], $rute_schedule2['dos']),
                'ron' => $rute_schedule1['ron'],
                'pairing' => $rute_schedule1['pairing'],
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
                'is_used_score' => 1
            );
            // update
            if ($this->m_izin->insert_rute($params)) {
                // delete
                $this->m_izin->delete_rute_data(array($izin_id, $this->com_user['airlines_id']));
                // pergi
                foreach ($schedule1 as $k => $v) {
                    $params = array(
                        'rute_id' => $izin_id . '1' . $k,
                        'izin_id' => $izin_id,
                        'rute_all' => $v['rute_all'],
                        'flight_no' => $v['flight_no'],
                        'is_used_score' => 1,
                        'doop' => $v['dos'],
                        'start_date' => $v['izin_start_date'],
                        'end_date' => $v['izin_expired_date']
                    );
                    if ($v['etd'] != "") $params['etd']=$v['etd'];
                    else $params['is_used_score']=2;
                    if ($v['eta'] != "") $params['eta']=$v['eta'];
                    else $params['is_used_score']=2;
                    $this->m_izin->insert_rute_data($params);
                }
                // pulang
                foreach ($schedule2 as $k => $v) {
                    $params = array(
                        'rute_id' => $izin_id . '2' . $k,
                        'izin_id' => $izin_id,
                        'rute_all' => $v['rute_all'],
                        'flight_no' => $v['flight_no'],
                        'is_used_score' => 1,
                        'doop' => $v['dos'],
                        'start_date' => $v['izin_start_date'],
                        'end_date' => $v['izin_expired_date']
                    );
                    if ($v['etd'] != "") $params['etd']=$v['etd'];
                    else $params['is_used_score']=2;
                    if ($v['eta'] != "") $params['eta']=$v['eta'];
                    else $params['is_used_score']=2;
                    $this->m_izin->insert_rute_data($params);
                }
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                if ($rs_airport1['is_used_score'] != 1 || $rs_airport2['is_used_score'] != 1) redirect("izin_internasional/notam/rute_edit/" . $registrasi_id . "/" . $kode_frekuensi . "/form");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }

        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/list_rute/" . $this->input->post('registrasi_id'));
    }

    // edit process
    // modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function edit_rute_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        list($orig,$dest) = explode("-", $this->input->post('izin_rute_start'));
        $params = array($orig);
        $rs_airport1 = $this->m_airport->get_airport_by_code($params);
        $params = array($dest);
        $rs_airport2 = $this->m_airport->get_airport_by_code($params);

        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('kode_izin', 'Kode Izin', 'trim|required');
        $this->tnotification->set_rules('kode_frekuensi', 'Kode Frekuensi', 'trim|required');
        $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('aircraft_capacity', 'Kapasitas Pesawat', 'trim|required|maxlength[3]');
        $this->tnotification->set_rules('pairing', 'Pairing', 'trim|required');        
        $this->tnotification->set_rules('ron', 'RON', 'trim|required|maxlength[1]|is_natural');        
        $this->tnotification->set_rules('izin_rute_start', 'Rute Pergi');
        $this->tnotification->set_rules('izin_rute_end', 'Rute Pulang');
        
        $mode = $this->input->post('mode');
        $registrasi_id = $this->input->post('registrasi_id');
        $kode_frekuensi = $this->input->post('kode_frekuensi');
        $pairing = $this->input->post('pairing');
        if ($rs_airport1['is_used_score'] == 0 && $rs_airport2['is_used_score'] == 0){
            $this->tnotification->set_rules('izin_start_date', 'Mulai Penundaan', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('izin_expired_date', 'Berakhir Penundaan', 'trim|required|maxlength[10]');
            $this->tnotification->set_rules('dos[]', 'DOS', 'trim|required');
            // rute pulang tergantung pairing
            if ($pairing == 'VV') {
                // rute pergi wajib diisi
                $this->tnotification->set_rules('rute_all11', 'Rute', 'trim|required|maxlength[20]');
                $this->tnotification->set_rules('flight_no11', 'Nomor Penerbangan', 'trim|required|maxlength[10]');
                $this->tnotification->set_rules('etd11', 'ETD', 'trim|required|maxlength[5]');
                $this->tnotification->set_rules('eta11', 'ETA', 'trim|required|maxlength[5]');
                // rute pulang wajib diisi
                $this->tnotification->set_rules('rute_all12', 'Rute', 'trim|required|maxlength[20]');
                $this->tnotification->set_rules('flight_no12', 'Nomor Penerbangan', 'trim|required|maxlength[10]');
                $this->tnotification->set_rules('etd12', 'Departure Time', 'trim|required|maxlength[5]');
                $this->tnotification->set_rules('eta12', 'Arriving Time', 'trim|required|maxlength[5]');
            } else {
                // rute wajib diisi
                $this->tnotification->set_rules('rute_all', 'Rute', 'trim|required|maxlength[20]');
                $this->tnotification->set_rules('flight_no', 'Nomor Penerbangan', 'trim|required|maxlength[10]');
                $this->tnotification->set_rules('etd', 'ETD', 'trim|required|maxlength[5]');
                $this->tnotification->set_rules('eta', 'ETA', 'trim|required|maxlength[5]');
            }
        }
        // validasi jumlah frekuensi
        // detail lama
        $result_lama = $this->m_izin->get_izin_rute_by_kode_frekuensi_active(array($kode_frekuensi, $this->com_user['airlines_id']));
        if (empty($result_lama)) {
            redirect('izin_internasional/perubahan/list_rute/' . $registrasi_id);
        }
        $dos = $this->input->post('dos');
        $start_date = $this->input->post('izin_start_date');
        $end_date = $this->input->post('izin_expired_date');
        if ($rs_airport1['is_used_score'] == 0 && $rs_airport2['is_used_score'] == 0){
            $izin_start_date = $start_date;
            $izin_expired_date = $end_date;
            $dos_input = '';
            for ($i = 1; $i <= 7; $i++) {
                $dos_input .= empty($dos[$i]) ? 0 : $dos[$i];
            }
            if ($result_lama['frekuensi'] <> count($dos)) {
                $this->tnotification->set_error_message('Jumlah Frekuensi Harus Sama!');
            }
        } else {
            $doop = array("0","0","0","0","0","0","0");
            $izin_start_date = $start_date[0];
            $izin_expired_date = $end_date[0];
            foreach ($dos as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    $doop[($k2-1)]=$v2;                    
                }
                if(strtotime($izin_start_date) > strtotime($start_date[$k])) $izin_start_date=$start_date[$k];
                if(strtotime($izin_expired_date) < strtotime($end_date[$k])) $izin_expired_date=$end_date[$k];
            }
            $frekuensi = 0;
            foreach ($doop as $k => $v) {
                if ($v!="0") $frekuensi++;
            }
            $dos_input = implode('',$doop);
            if ($result_lama['frekuensi'] <> $frekuensi) {
                $this->tnotification->set_error_message('Jumlah Frekuensi Harus Sama!');
            }
        }

        // process
        if ($this->tnotification->run() !== FALSE) {
            // delete izin rute by frekuensi and registrasi id                        
            if ($rs_airport1['is_used_score'] == 0 && $rs_airport2['is_used_score'] == 0){
                $params = array($registrasi_id, $kode_frekuensi, $this->com_user['airlines_id']);
                $this->m_izin->delete_rute_by_registrasi_kode_frekuensi($params);
                // insert izin rute baru
                $izin_id = $this->m_izin->get_data_id();
                $params = array(
                    'izin_id' => $izin_id,
                    'airlines_id' => $this->com_user['airlines_id'],
                    'registrasi_id' => $this->input->post('registrasi_id'),
                    'izin_flight' => 'internasional',
                    'izin_st' => 'notam',
                    'aircraft_type' => $this->input->post('aircraft_type'),
                    'kode_izin' => $this->input->post('kode_izin'),
                    'kode_frekuensi' => $this->input->post('kode_frekuensi'),
                    'aircraft_capacity' => $this->input->post('aircraft_capacity'),
                    "izin_start_date" => $izin_start_date,
                    "izin_expired_date" => $izin_expired_date,
                    'izin_rute_start' => $this->input->post('izin_rute_start'),
                    'izin_rute_end' => $this->input->post('izin_rute_end'),
                    'dos' => $dos_input,
                    'ron' => $this->input->post('ron'),
                    'pairing' => $this->input->post('pairing'),
                    'mdb' => $this->com_user['user_id'],
                    'mdd' => date('Y-m-d h:i:s'),
                );
                // insert
                if ($this->m_izin->insert_rute($params)) {
                    // pulang
                    if ($pairing == 'VV') {
                        // pergi
                        $params = array(
                            'rute_id' => $izin_id . '1',
                            'izin_id' => $izin_id,
                            'rute_all' => $this->input->post('rute_all11'),
                            'flight_no' => $this->input->post('flight_no11'),
                            'etd' => $this->input->post('etd11'),
                            'eta' => $this->input->post('eta11'),
                            'doop' => $dos_input,
                            'start_date' => $izin_start_date,
                            'end_date' => $izin_expired_date
                        );
                        $this->m_izin->insert_rute_data($params);
                        // pulang
                        $params = array(
                            'rute_id' => $izin_id . '2',
                            'izin_id' => $izin_id,
                            'rute_all' => $this->input->post('rute_all12'),
                            'flight_no' => $this->input->post('flight_no12'),
                            'etd' => $this->input->post('etd12'),
                            'eta' => $this->input->post('eta12'),
                            'doop' => $dos_input,
                            'start_date' => $izin_start_date,
                            'end_date' => $izin_expired_date
                        );
                        $this->m_izin->insert_rute_data($params);
                    } else {
                        // pergi
                        $params = array(
                            'rute_id' => $izin_id . '1',
                            'izin_id' => $izin_id,
                            'rute_all' => $this->input->post('rute_all'),
                            'flight_no' => $this->input->post('flight_no'),
                            'etd' => $this->input->post('etd'),
                            'eta' => $this->input->post('eta'),
                            'doop' => $dos_input,
                            'start_date' => $izin_start_date,
                            'end_date' => $izin_expired_date
                        );
                        $this->m_izin->insert_rute_data($params);
                    }
                    // notification
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                } else {
                    // default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                }
            } else {
                $izin_id = $this->input->post('izin_id');
                $params = array(
                    'airlines_id' => $this->com_user['airlines_id'],
                    'registrasi_id' => $this->input->post('registrasi_id'),
                    'aircraft_type' => $this->input->post('aircraft_type'),
                    'aircraft_capacity' => $this->input->post('aircraft_capacity'),
                    "izin_start_date" => $izin_start_date,
                    "izin_expired_date" => $izin_expired_date,
                    'izin_rute_start' => $this->input->post('izin_rute_start'),
                    'izin_rute_end' => $this->input->post('izin_rute_end'),
                    'dos' => $dos_input,
                    'ron' => $this->input->post('ron'),
                    'pairing' => $this->input->post('pairing'),
                    'mdb' => $this->com_user['user_id'],
                    'mdd' => date('Y-m-d h:i:s'),
                );
                $where = array(
                    'izin_id' => $izin_id,
                    'airlines_id' => $this->com_user['airlines_id'],
                    'registrasi_id' => $this->input->post('registrasi_id'),
                );
                // update
                if ($this->m_izin->update_rute($params, $where)) {
                    $this->m_izin->delete_rute_data(array($izin_id, $this->com_user['airlines_id']));
                    $rute_all = $this->input->post('rute_all');
                    $flight_no = $this->input->post('flight_no');
                    $is_used_score = $this->input->post('is_used_score');
                    $etd = $this->input->post('etd');
                    $eta = $this->input->post('eta');
                    $izin_rute_start = $this->input->post('izin_rute_start');
                    $izin_rute_end = $this->input->post('izin_rute_end');
                    $has_error = false;
                    foreach ($rute_all as $k => $v) {
                        $rute_id = ($v == $izin_rute_start) ? $izin_id . '1' . $k : $izin_id . '2' . $k;
                        $dos_input = '';
                        for ($i = 1; $i <= 7; $i++) {
                            $dos_input .= empty($dos[$k][$i]) ? 0 : $dos[$k][$i];
                        }
                        $params = array(
                            'rute_id' => $rute_id,
                            'izin_id' => $izin_id,
                            'rute_all' => $v,
                            'flight_no' => $flight_no[$k],
                            'etd' => $etd[$k],
                            'eta' => $eta[$k],
                            'is_used_score' => $is_used_score[$k],
                            'doop' => $dos_input,
                            'start_date' => $start_date[$k],
                            'end_date' => $end_date[$k]
                        );
                        if (!$this->m_izin->insert_rute_data($params)) {
                            $has_error=true;
                            // var_dump($params);die;
                        }
                    }
                    if ($has_error) {
                        // default error
                        $this->tnotification->sent_notification("error", "Data gagal disimpan");
                    } else {
                        // notification
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                    }
                } else {
                    // default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                }
            }//eif
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        if ($mode == "form")
            redirect("izin_internasional/notam/rute_edit/" . $this->input->post('registrasi_id') . '/' . $kode_frekuensi . '/form');
        else
            redirect("izin_internasional/notam/rute_edit/" . $this->input->post('registrasi_id') . '/' . $kode_frekuensi);
    }

    // delete process
    public function rute_delete($registrasi_id = "", $kode_frekuensi = "") {
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array($registrasi_id, $kode_frekuensi, $this->com_user['airlines_id']);
        // execute
        if ($this->m_izin->delete_rute_by_registrasi_kode_frekuensi($params)) {
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("izin_internasional/notam/list_rute/" . $registrasi_id);
    }

    /*
     * SLOT CLEARANCE
     */

    // list data slot
    public function list_slot($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/list_slot.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        $this->smarty->assign("detail", $result);
        // list slot
        $rs_id = $this->m_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add slot
    public function slot_add($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/slot_add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        $this->smarty->assign("detail", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add slot process
    public function add_slot_process() {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('slot_subject', 'Subyek Surat', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('slot_number', 'Nomor Surat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('slot_date', 'Tanggal Surat', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('slot_desc', 'Perihal', 'trim|required|maxlength[50]');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get id
        $slot_id = $this->m_izin->get_data_id();
        // upload foto
        if (!empty($_FILES['slot_file_name']['tmp_name'])) {
            // upload config
            $config['upload_path'] = 'resource/doc/slot/' . $registrasi_id . '/' . $slot_id;
            $config['allowed_types'] = 'pdf|jpeg|jpg|docx|doc|xls|xlsx';
            $config['max_size'] = 1024;
            $this->tupload->initialize($config);
            // process upload
            if ($this->tupload->do_upload('slot_file_name')) {
                $data = $this->tupload->data();
            } else {
                // jika gagal
                $this->tnotification->set_error_message($this->tupload->display_errors());
            }
        } else {
            $this->tnotification->set_error_message('File wajib di upload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // insert slot clearance
            // slot_id, registrasi_id, slot_subject, slot_number, slot_date, slot_desc, slot_file_name, slot_file_path, mdb, mdd
            $params = array(
                'slot_id' => $slot_id,
                'registrasi_id' => $this->input->post('registrasi_id'),
                'slot_subject' => $this->input->post('slot_subject'),
                'slot_number' => $this->input->post('slot_number'),
                'slot_date' => $this->input->post('slot_date'),
                'slot_desc' => $this->input->post('slot_desc'),
                'slot_file_name' => $data['file_name'],
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            // update
            if ($this->m_izin->insert_slot($params)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // remove dir
                $this->tupload->remove_dir('resource/doc/slot/' . $registrasi_id . '/' . $slot_id);
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // remove dir
            $this->tupload->remove_dir('resource/doc/slot/' . $registrasi_id . '/' . $slot_id);
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/slot_add/" . $this->input->post('registrasi_id'));
    }

    // edit slot
    public function slot_edit($registrasi_id = "", $slot_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/slot_edit.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        $this->smarty->assign("detail", $result);
        // detail slot
        $result = $this->m_izin->get_detail_slot_by_id(array($registrasi_id, $slot_id));
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit slot process
    public function edit_slot_process() {
        // set page rules
        $this->_set_page_rule("U");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('slot_id', 'ID slot', 'trim|required');
        $this->tnotification->set_rules('slot_subject', 'Subyek Surat', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('slot_number', 'Nomor Surat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('slot_date', 'Tanggal Surat', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('slot_desc', 'Perihal', 'trim|required|maxlength[50]');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get id
        $slot_id = $this->input->post('slot_id');
        // detail slot
        $result = $this->m_izin->get_detail_slot_by_id(array($registrasi_id, $slot_id));
        if (empty($result)) {
            redirect('izin_internasional/notam/list_slot/' . $registrasi_id);
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // update slot clearance
            // slot_id, registrasi_id, slot_subject, slot_number, slot_date, slot_desc, slot_file_name, slot_file_path, mdb, mdd
            $params = array(
                'slot_subject' => $this->input->post('slot_subject'),
                'slot_number' => $this->input->post('slot_number'),
                'slot_date' => $this->input->post('slot_date'),
                'slot_desc' => $this->input->post('slot_desc'),
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            $where = array(
                "registrasi_id" => $registrasi_id,
                "slot_id" => $slot_id,
            );
            // update
            if ($this->m_izin->update_slot($params, $where)) {
                // upload files
                if (!empty($_FILES['slot_file_name']['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/slot/' . $registrasi_id . '/' . $slot_id;
                    $config['allowed_types'] = 'pdf|jpeg|jpg|docx|doc|xls|xlsx';
                    $config['max_size'] = 1024;
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload('slot_file_name')) {
                        $data = $this->tupload->data();
                        // --
                        $params = array(
                            'slot_file_name' => $data['file_name'],
                        );
                        $where = array(
                            "registrasi_id" => $registrasi_id,
                            "slot_id" => $slot_id,
                        );
                        $this->m_izin->update_slot($params, $where);
                        // remove file
                        $this->tupload->remove_file('resource/doc/slot/' . $registrasi_id . '/' . $slot_id . '/' . $result['slot_file_name']);
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
                // notification
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
        redirect("izin_internasional/notam/slot_edit/" . $registrasi_id . '/' . $slot_id);
    }

    // download
    public function slot_download($registrasi_id = "", $slot_id = "") {
        // get detail data
        $params = array($registrasi_id, $slot_id);
        $result = $this->m_izin->get_detail_slot_by_id($params);
        if (empty($result)) {
            redirect("izin_internasional/notam/list_slot/" . $registrasi_id);
        }
        // filepath
        $file_path = 'resource/doc/slot/' . $registrasi_id . '/' . $slot_id . '/' . $result['slot_file_name'];
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . $result['slot_file_name'] . '"');
            readfile($file_path);
            exit();
        } else {
            redirect("izin_internasional/notam/list_slot/" . $registrasi_id);
        }
    }

    // delete slot
    public function slot_delete($registrasi_id = "", $slot_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // load
        $this->load->library('tupload');
        // detail slot
        $result = $this->m_izin->get_detail_slot_by_id(array($registrasi_id, $slot_id));
        if (empty($result)) {
            redirect('izin_internasional/notam/list_slot/' . $registrasi_id);
        }
        // delete
        $params = array($slot_id, $this->com_user['airlines_id']);
        // execute
        if ($this->m_izin->delete_slot($params)) {
            // remove file
            $this->tupload->remove_dir('resource/doc/slot/' . $registrasi_id . '/' . $slot_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("izin_internasional/notam/list_slot/" . $registrasi_id);
    }

    // list_slot_process
    public function list_slot_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        // validasi
        $rs_id = $this->m_izin->get_list_data_slot_by_id(array($this->input->post('registrasi_id'), $this->com_user['airlines_id']));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Belum ada data rute yang diinputkan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect
            redirect("izin_internasional/notam/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/list_slot/" . $this->input->post('registrasi_id'));
    }

    // files attachment
    // modified by: sanjaya.im@gmail.com
    // modified on: 15-May-2015
    // reason modified: change step by step base on airport that used SCORE slot time database
    public function list_files($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/files.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['izin_rute_start']);
        $params = array($orig.','.$dest);
        $rs_airport = $this->m_airport->get_airport_score_by_code($params);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("airport", $rs_airport);
        $this->smarty->assign("detail", $result);
        // list persyaratan
        $rs_files = $this->m_izin->get_list_file_required_internasional(array($result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_izin->get_list_file_uploaded(array($registrasi_id));
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
    // modified on: 16-May-2015
    // reason modified: change get new id logic to prevent duplicate primary key when upload multiple file in the same time
    public function files_process() {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // upload 1 per 1
            $rs_files = $this->m_izin->get_list_file_required_internasional(array($result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']));
            foreach ($rs_files as $k=>$files) {
                $file = $_FILES[$files['ref_field']];
                // upload 1 per 1
                if (!empty($file['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/izin/' . $registrasi_id . '/' . $files['ref_id'];
                    $config['allowed_types'] = $files['ref_allowed'];
                    $config['max_size'] = $files['ref_size'];
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload($files['ref_field'])) {
                        // jika berhasil
                        $data = $this->tupload->data();
                        // update
                        $file_id = $this->m_izin->get_file_id() + $k;
                        $filepath = 'resource/doc/izin/' . $registrasi_id . '/' . $files['ref_id'] . '/' . $data['file_name'];
                        $this->m_izin->update_files(array($registrasi_id, $files['ref_id']), array($file_id, $registrasi_id, $filepath, $data['file_name'], $files['ref_id']));
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
        redirect("izin_internasional/notam/list_files/" . $registrasi_id);
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_izin->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("izin_internasional/notam/list_files/" . $data_id);
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
            redirect('member/registration_internasional');
        }
    }

    // file process
    public function files_next() {
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // validation
        if (!$this->m_izin->is_file_completed(array($registrasi_id, $result['izin_group'], $result['izin_flight']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("izin_internasional/notam/review/" . $registrasi_id);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/list_files/" . $registrasi_id);
    }

    // review
    // modified by: sanjaya.im@gmail.com
    // modified on: 26-Jul-2015
    // reason modified: accomodate multi pick slot SCORE
    public function review($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_internasional/notam/review.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // get detail data airport registration
        list($orig,$dest) = explode("-", $result['izin_rute_start']);
        $params = array($orig.','.$dest);
        $rs_airport = $this->m_airport->get_airport_score_by_code($params);
        // assign
        $this->smarty->assign("detail", $result);
        $this->smarty->assign("airport", $rs_airport);
        // get kode izin
        $izin = $this->m_izin->get_rute_by_kode_izin($result['kode_izin']);
        $this->smarty->assign("izin", $izin);
        // data sekarang
        $rs_new = $this->m_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        foreach ($rs_new as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_new as $k2 => $v2) {
                if ($last_key == $v2['izin_id']) $group_izin++;
            }
            $rs_new[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_new", $rs_new);
        // data lama ambil dari kode_frekuensi
        // $rs_old = array();
        // $temp = '';
        // foreach ($rs_new as $new) {
        //     if ($temp <> $new['kode_frekuensi']) {
        //         $old = $this->m_izin->get_list_data_rute_by_kode_frekuensi(array($new['kode_frekuensi'], $this->com_user['airlines_id']));
        //         $rs_old = array_merge($rs_old, $old);
        //     }
        //     $temp = $new['kode_frekuensi'];
        // }
        // foreach ($rs_old as $k1 => $v1) {
        //     $last_key = $v['izin_id'];
        //     $group_izin = 0;
        //     foreach ($rs_old as $k2 => $v2) {
        //         if ($last_key == $v['izin_id']) $group_izin++;
        //     }
        //     $rs_old[$k1]['rowspan'] = $group_izin;
        // }
        $rs_old = $this->m_izin->get_list_data_rute_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        foreach ($rs_old as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_old as $k2 => $v2) {
                if ($last_key == $v2['izin_id']) $group_izin++;
            }
            $rs_old[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_old", $rs_old);
        // list slot
        $rs_slot = $this->m_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        // list persyaratan
        $rs_files = $this->m_izin->get_list_file_required_internasional(array($result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // izin
        $result = $this->m_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $result[0]);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // send process
    public function send_process() {
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_izin->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('member/registration_internasional');
        }
        // validation
        if (!$this->m_izin->is_file_completed(array($registrasi_id, $result['izin_group'], $result['izin_flight']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        $rs_id = $this->m_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Data rute belum diinput!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // process flow
            $process_id = $this->m_izin->get_data_id();
            $params = array($process_id, $registrasi_id, 1, $this->com_user['user_id']);
            $this->m_izin->insert_process($params);
            // update status
            $this->m_izin->update_status_data(array('1', $this->com_user['user_id'], $registrasi_id, $this->com_user['airlines_id']));
            // send mail
            if ($result['izin_flight'] == 'internasional') {
                $this->m_email->mail_izin_to_all_aunbdn($result['registrasi_id'], $this->com_user['airlines_id']);
            } else {
                $this->m_email->mail_izin_to_all_aunbln($result['registrasi_id'], $this->com_user['airlines_id']);
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("member/registration_internasional");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_internasional/notam/review/" . $registrasi_id);
    }

}