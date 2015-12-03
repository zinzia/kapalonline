<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class monitoring_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('monitoring/m_monitoring_izin');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_monitoring_izin');
        // search parameters
        $izin_request_letter = empty($search['izin_request_letter']) ? '%' : '%' . $search['izin_request_letter'] . '%';
        $izin_flight = empty($search['izin_flight']) ? '%' : $search['izin_flight'];
        $izin_st = empty($search['izin_st']) ? '%' : $search['izin_st'];
        // assign search
        $this->smarty->assign("search", $search);
        // get list data
        $rs_id = $this->m_monitoring_izin->get_list_my_task_waiting(array($this->com_user['airlines_id'], $izin_request_letter, $izin_flight, $izin_st));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_monitoring_izin');
        } else {
            $params = array(
                "izin_request_letter" => $this->input->post("izin_request_letter"),
                "izin_flight" => $this->input->post("izin_flight"),
                "izin_st" => $this->input->post("izin_st"),
            );
            $this->tsession->set_userdata("search_monitoring_izin", $params);
        }
        // redirect
        redirect("member/monitoring_izin");
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_monitoring_izin->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("member/monitoring_izin");
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
            redirect('member/monitoring_izin');
        }
    }

    // download
    public function slot_download($registrasi_id = "", $slot_id = "") {
        // get detail data
        $params = array($registrasi_id, $slot_id);
        $result = $this->m_monitoring_izin->get_detail_slot_by_id($params);
        if (empty($result)) {
            redirect("member/monitoring_izin");
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
            redirect("member/monitoring_izin");
        }
    }

    /*
     * DISPLAY
     * 
     */

    // baru
    public function baru($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/baru.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // list rute
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get sub total frekuensi
        $subcek = array();
        $subtotal = array();
        $rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);

        foreach ($rs_subtotal as $rs_sub) {
                $subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
                $subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
                $subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
                array_push($subcek,$rs_sub['kode_frekuensi']);
        }

        $this->smarty->assign("subtotal", $subtotal);

        // get total frekuensi yang tidak diubah
        $frek_not_change = 0;
        $rs_ex = $rs_subtotal_existing; 
        foreach ($rs_ex as $rs_exs) {
                if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
                        $frek_not_change += $rs_exs['frekuensi'];
                }
        }
        $this->smarty->assign("frek_not_change", $frek_not_change);
        // jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_monitoring_izin->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // penundaan
    public function penundaan($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/penundaan.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // notes
            $notes[$izin_rute['izin_id']] = $izin_rute['notes'];
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        $data = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute           
            $izin_rute = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // total
            $total_old += $izin_rute['total_frekuensi'];
        }
        
        $this->smarty->assign("rs_existing", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("total_old", $total_old);
                
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izini data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("rute_selected", $rute_selected);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get sub total frekuensi
        $subcek = array();
        $subtotal = array();
        $rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);

        foreach ($rs_subtotal as $rs_sub) {
                $subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
                $subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
                $subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
                array_push($subcek,$rs_sub['kode_frekuensi']);
        }

        $this->smarty->assign("subtotal", $subtotal);

        // get total frekuensi yang tidak diubah
        $frek_not_change = 0;
        $rs_ex = $rs_subtotal_existing; 
        foreach ($rs_ex as $rs_exs) {
                if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
                        $frek_not_change += $rs_exs['frekuensi'];
                }
        }
        $this->smarty->assign("frek_not_change", $frek_not_change);
        // jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_monitoring_izin->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // perubahan
    public function perubahan($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/perubahan.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // notes
            $notes[$izin_rute['izin_id']] = $izin_rute['notes'];
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        $data = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute           
            $izin_rute = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // total
            $total_old += $izin_rute['total_frekuensi'];
        }
        
        $this->smarty->assign("rs_existing", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("total_old", $total_old);

        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izini data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("rute_selected", $rute_selected);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_perubahan_by_registrasi_id($registrasi_id));
        
		// get sub total frekuensi
		$subcek = array();
		$subtotal = array();
		$rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);
		
		foreach ($rs_subtotal as $rs_sub) {
			$subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
			$subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
			$subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
                        $subtotal[$rs_sub['kode_frekuensi']]['izin_st'] = $rs_sub['izin_st'];
			array_push($subcek,$rs_sub['kode_frekuensi']);
		}
		
		$this->smarty->assign("subtotal", $subtotal);
		
		// get total frekuensi yang tidak diubah
		$frek_not_change = 0;
		$rs_ex = $rs_subtotal_existing; 
		foreach ($rs_ex as $rs_exs) {
			if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
				$frek_not_change += $rs_exs['frekuensi'];
			}
		}
		$this->smarty->assign("frek_not_change", $frek_not_change);
		
		// jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_monitoring_izin->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // frekuensi
    public function frekuensi($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/frekuensi.html");
        // detail izin
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // get kode izin
        $izin = $this->m_monitoring_izin->get_rute_by_kode_izin($detail['kode_izin']);
        $this->smarty->assign("izin", $izin);
        // data sekarang
        $rs_new = $this->m_monitoring_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_new", $rs_new);
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_new as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_monitoring_izin->get_list_data_rute_by_kode_frekuensi(array($new['kode_frekuensi'], $this->com_user['airlines_id']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        foreach ($rs_old as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_old as $k2 => $v2) {
                if ($last_key == $v2['izin_id'])
                    $group_izin++;
            }
            $rs_old[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_old", $rs_old);
        // list slot
        $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        // list persyaratan
        if ($this->com_user['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // izin
        $result = $this->m_monitoring_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $result[0]);
        // display
        parent::display();
    }

    // frekuensi_add
    public function frekuensi_add($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/frekuensi_add.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // data sebelumnya
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            }
        $this->smarty->assign("rs_existing", $data);
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izini data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("rute_selected", $rute_selected);
        // get sub total frekuensi        
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        
        // get sub total frekuensi
        $subtotal_existing = array();
        $rs_subtotal_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_frekuensi(array($detail['kode_izin'], $this->com_user['airlines_id']));

        foreach ($rs_subtotal_existing as $rs_sub_existing) {
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['frekuensi'] = $rs_sub_existing['frekuensi'];
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['start_date'] = $rs_sub_existing['start_date'];
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['end_date'] = $rs_sub_existing['end_date'];
        }

        $this->smarty->assign("subtotal_existing", $subtotal_existing);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get sub total frekuensi
        $subcek = array();
        $subtotal = array();
        $rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);

        foreach ($rs_subtotal as $rs_sub) {
                $subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
                $subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
                $subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
                array_push($subcek,$rs_sub['kode_frekuensi']);
        }

        $this->smarty->assign("subtotal", $subtotal);

        // get total frekuensi yang tidak diubah
        $frek_not_change = 0;
        $rs_ex = $rs_subtotal_existing; 
        foreach ($rs_ex as $rs_exs) {
                if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
                        $frek_not_change += $rs_exs['frekuensi'];
                }
        }
        $this->smarty->assign("frek_not_change", $frek_not_change);
        // jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_monitoring_izin->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // frekuensi delete
    public function frekuensi_delete($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/frekuensi_delete.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // data sebelumnya
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            }
        $this->smarty->assign("rs_existing", $data);
        // get sub total frekuensi        
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        
        // get sub total frekuensi
        $subtotal_existing = array();
        $rs_subtotal_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_frekuensi(array($detail['kode_izin'], $this->com_user['airlines_id']));

        foreach ($rs_subtotal_existing as $rs_sub_existing) {
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['frekuensi'] = $rs_sub_existing['frekuensi'];
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['start_date'] = $rs_sub_existing['start_date'];
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['end_date'] = $rs_sub_existing['end_date'];
        }

        $this->smarty->assign("subtotal_existing", $subtotal_existing);
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izini data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("rute_selected", $rute_selected);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get sub total frekuensi
        $subcek = array();
        $subtotal = array();
        $rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);

        foreach ($rs_subtotal as $rs_sub) {
                $subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
                $subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
                $subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
                array_push($subcek,$rs_sub['kode_frekuensi']);
        }

        $this->smarty->assign("subtotal", $subtotal);

        // get total frekuensi yang tidak diubah
        $frek_not_change = 0;
        $rs_ex = $rs_subtotal_existing; 
        foreach ($rs_ex as $rs_exs) {
                if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
                        $frek_not_change += $rs_exs['frekuensi'];
                }
        }
        $this->smarty->assign("frek_not_change", $frek_not_change);
        // jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_monitoring_izin->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // perpanjangan
    public function perpanjangan($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/perpanjangan.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // data sebelumnya
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);       	        
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // notes
            $notes[$izin_rute['izin_id']] = $izin_rute['notes'];
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        $data = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute           
            $izin_rute = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // total
            $total_old += $izin_rute['total_frekuensi'];
        }
        
        $this->smarty->assign("rs_existing", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("total_old", $total_old);
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        
		// get sub total frekuensi
		$subcek = array();
		$subtotal = array();
		$rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);
		
		foreach ($rs_subtotal as $rs_sub) {
			$subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
			$subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
			$subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
			array_push($subcek,$rs_sub['kode_frekuensi']);
		}
		
		$this->smarty->assign("subtotal", $subtotal);
		
		// get total frekuensi yang tidak diubah
		$frek_not_change = 0;
		$rs_ex = $rs_subtotal_existing; 
		foreach ($rs_ex as $rs_exs) {
			if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
				$frek_not_change += $rs_exs['frekuensi'];
			}
		}
		$this->smarty->assign("frek_not_change", $frek_not_change);
        
		// jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_monitoring_izin->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // penghentian
    public function penghentian($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/penghentian.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);        
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get sub total frekuensi
        $subcek = array();
        $subtotal = array();
        $rs_subtotal = $this->m_monitoring_izin->get_total_frekuensi_by_kode_frekuensi($registrasi_id);

        foreach ($rs_subtotal as $rs_sub) {
                $subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
                $subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
                $subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
                array_push($subcek,$rs_sub['kode_frekuensi']);
        }

        $this->smarty->assign("subtotal", $subtotal);

        // get total frekuensi yang tidak diubah
        $frek_not_change = 0;
        $rs_ex = $rs_subtotal_existing; 
        foreach ($rs_ex as $rs_exs) {
                if (!in_array($rs_exs['kode_frekuensi'], $subcek)){
                        $frek_not_change += $rs_exs['frekuensi'];
                }
        }
        $this->smarty->assign("frek_not_change", $frek_not_change);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // display
        parent::display();
    }

    // notam
    public function notam($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/notam.html");
        // detail izin
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // get kode izin
        $izin = $this->m_monitoring_izin->get_rute_by_kode_izin($detail['kode_izin']);
        $this->smarty->assign("izin", $izin);
        // data sekarang
        $rs_new = $this->m_monitoring_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_new", $rs_new);
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_new as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_monitoring_izin->get_list_data_rute_by_kode_frekuensi(array($new['kode_frekuensi'], $this->com_user['airlines_id']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        foreach ($rs_old as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_old as $k2 => $v2) {
                if ($last_key == $v2['izin_id'])
                    $group_izin++;
            }
            $rs_old[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_old", $rs_old);
        // list slot
        $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        // list persyaratan
        if ($this->com_user['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $this->com_user['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_monitoring_izin->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // izin
        $result = $this->m_monitoring_izin->get_list_data_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("result", $result[0]);
        // display
        parent::display();
    }

    // migrasi
    public function migrasi($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/monitoring_izin/migrasi.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('member/monitoring_izin');
        }
        // list rute
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_monitoring_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_id", $data);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_monitoring_izin->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // display
        parent::display();
    }

}
