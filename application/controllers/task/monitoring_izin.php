<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class monitoring_izin extends ApplicationBase{
    //put your code here
    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model('regulator/m_monitoring_izin');
        $this->load->model('m_monitoring');
        $this->load->model('m_operator');
        $this->load->model('m_izin');
        $this->load->model("regulator/m_task");
        // load library
        $this->load->library('pagination');
    }
        
    public function index() {
        $this->_set_page_rule("R");
        $this->smarty->assign("template_content","task/monitoring_izin/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");        
        // list role
        $this->smarty->assign("rs_roles", $this->m_monitoring->get_list_role());
        // load airlines
        $this->smarty->assign("rs_airlines", $this->m_operator->get_all_airlines());
        // get search parameter
        $search = $this->tsession->userdata('search_monitoring');
        // search parameters
        $document_no = empty($search['izin_request_letter']) ? '%' : '%' . $search['izin_request_letter'] . '%';
        $izin_flight = empty($search['izin_flight']) ? '%' : '%' . $search['izin_flight'] . '%';
        $role_id = empty($search['role_id']) ? '%' : $search['role_id'];
        $airlines_id = empty($search['airlines_id']) ? '%' : $search['airlines_id'];
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("task/monitoring_izin/index/");
        $config['total_rows'] = $this->m_monitoring_izin->get_total_my_task_waiting_opr(array($document_no, $izin_flight, $role_id, $airlines_id));
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
        $params = array($document_no, $izin_flight, $role_id, $airlines_id, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_monitoring_izin->get_list_my_task_waiting_opr($params));
        $this->smarty->assign("total", count($this->m_monitoring_izin->get_list_my_task_waiting_opr($params)));
        parent::display();
    }
    
    // baru
    public function baru($registrasi_id) {
        $this->_set_page_rule("R");
        $this->smarty->assign("template_content","task/monitoring_izin/detail_izin.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // list rute
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
        $this->smarty->assign("template_content", "task/monitoring_izin/penundaan.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
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
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
        $this->smarty->assign("template_content", "task/monitoring_izin/perubahan.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
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
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
        $this->smarty->assign("template_content", "task/monitoring_izin/frekuensi.html");
        // detail izin
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // get kode izin
        $izin = $this->m_monitoring_izin->get_rute_by_kode_izin($detail['kode_izin']);
        $this->smarty->assign("izin", $izin);
        // data sekarang
        $rs_new = $this->m_monitoring_izin->get_list_data_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_new", $rs_new);
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_new as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_monitoring_izin->get_list_data_rute_by_kode_frekuensi(array($new['kode_frekuensi'], $detail['airlines_id']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        $this->smarty->assign("rs_old", $rs_old);
        // list slot
        $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
        }
        $this->smarty->assign("rs_files", $rs_files);
        // total frek
        $this->smarty->assign("tot_frek", $this->m_monitoring_izin->get_total_frekuensi(array($registrasi_id)));
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
        $result = $this->m_monitoring_izin->get_list_data_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("result", $result[0]);
        // display
        parent::display();
    }

    // frekuensi_add
    public function frekuensi_add($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring_izin/frekuensi_add.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // data sebelumnya
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
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
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        
        // get sub total frekuensi
        $subtotal_existing = array();
        $rs_subtotal_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_frekuensi(array($detail['kode_izin'], $detail['airlines_id']));

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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
        $this->smarty->assign("template_content", "task/monitoring_izin/frekuensi_delete.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // data sebelumnya
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_aktif_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
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
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        
        // get sub total frekuensi
        $subtotal_existing = array();
        $rs_subtotal_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_frekuensi(array($detail['kode_izin'], $detail['airlines_id']));

        foreach ($rs_subtotal_existing as $rs_sub_existing) {
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['frekuensi'] = $rs_sub_existing['frekuensi'];
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['start_date'] = $rs_sub_existing['start_date'];
                $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['end_date'] = $rs_sub_existing['end_date'];
        }

        $this->smarty->assign("subtotal_existing", $subtotal_existing);
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
        $this->smarty->assign("template_content", "task/monitoring_izin/perpanjangan.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // data sebelumnya
        $total_existing = $this->m_monitoring_izin->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
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
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
        $this->smarty->assign("template_content", "task/monitoring_izin/penghentian.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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

    // penghentian all
    public function pencabutan_all($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/monitoring_izin/pencabutan_all.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));       
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // list rute yang diajukan
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
        // get uploaded files
        $rs_files = $this->m_monitoring_izin->get_list_file_pencabutan_uploaded(array($registrasi_id));
        $this->smarty->assign("rs_files", $rs_files);
        // display
        parent::display();
    }
    
    // migrasi
    public function migrasi($registrasi_id) {
        $this->_set_page_rule("R");
        $this->smarty->assign("template_content","task/monitoring_izin/migrasi.html");
        // detail registrasi
        $detail = $this->m_monitoring_izin->get_detail_registrasi_data_by_id(array($registrasi_id));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        // list rute
        $data = array();
        $rs_id = $this->m_monitoring_izin->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
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
            $rs_slot = $this->m_monitoring_izin->get_list_data_slot_by_id(array($registrasi_id, $detail['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        if ($detail['izin_flight'] == 'domestik') {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_domestik(array($detail['izin_group'], $detail['izin_flight']));
        } else {
            $rs_files = $this->m_monitoring_izin->get_list_file_required_internasional(array($detail['izin_group'], $detail['izin_flight'], $detail['airlines_nationality']));
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
    
    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_monitoring');
        } else {
            $params = array(
                "izin_request_letter" => $this->input->post("izin_request_letter"),
                "izin_flight" => $this->input->post("izin_flight"),
                "role_id" => $this->input->post("role_id"),
                "airlines_id" => $this->input->post("airlines_id")
            );
            $this->tsession->set_userdata("search_monitoring", $params);
        }
        // redirect
        redirect("task/monitoring_izin");
    }
    
    // file download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_monitoring_izin->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("task/monitoring_izin");
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
            redirect('task/monitoring_izin');
        }
    }

    // download file pencabutan
    public function pencabutan_files_download($registrasi_id = "", $letter_id = "") {
        // get detail data
        $params = array($registrasi_id, $letter_id);
        $result = $this->m_monitoring_izin->get_detail_files_pencabutan_by_id($params);
        if (empty($result)) {
            redirect("pencabutan/penghentian/list_files/" . $registrasi_id);
        }
        // filepath
        $file_path = 'resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id . '/' . $result['letter_file_name'];
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . $result['letter_file_name'] . '"');
            readfile($file_path);
            exit();
        } else {
            redirect("pencabutan/penghentian/list_files/" . $registrasi_id);
        }
    }
    
    // slot download
    public function slot_download($registrasi_id = "", $slot_id = "") {
        // get detail data
        $params = array($registrasi_id, $slot_id);
        $result = $this->m_izin->get_detail_slot_by_id($params);
        if (empty($result)) {
            redirect("izin_domestik/baru/list_slot/" . $registrasi_id);
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
            redirect("izin_domestik/baru/list_slot/" . $registrasi_id);
        }
    }

    /*
     * PREVIEW
     */

    // view baru
    public function preview_baru($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/baru.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * DRAFT SURAT
         */
        // nomor surat penerbitan
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // view perpanjangan
    public function preview_perpanjangan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/perpanjangan.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // view penundaan
    public function preview_penundaan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/penundaan.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);       
        // output
        parent::display();
    }

    // view penghentian
    public function preview_penghentian($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/penghentian.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // view penerbitan
    public function preview_frekuensi_add($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/frekuensi_add.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // view penerbitan
    public function preview_frekuensi_delete($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/frekuensi_delete.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);        
        // output
        parent::display();
    }

    // view perubahan
    public function preview_perubahan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/perubahan.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            if ($izin_rute['izin_st'] != 'pencabutan') {
                $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
                if (!empty($izin_data)) {
                    // lebih dari 1 data
                    $data[$no++] = $izin_data;
                } else {
                    // hanya ada 1 data
                    $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
                }
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // notes
            $notes[$izin_rute['izin_id']] = $izin_rute['notes'];
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_perubahan_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);        
        // output
        parent::display();
    }

    // view frekuensi
    public function preview_frekuensi($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/frekuensi.html");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_task->get_preview_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('task/izin_preview');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_task->get_list_data_slot_by_id(array($registrasi_id));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_preview(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_task->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_task->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // memo
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // get list frekuensi
        $params = array($registrasi_id);
        $rs_id = $this->m_task->get_izin_rute_data_by_kode_izin($params);
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        if (empty($rs_id)) {
            redirect('task/izin_preview');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_id as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_task->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_task->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
            $surat_persetujuan[$result['registrasi_id']]['perihal'] = $result['group_nm'];
        }
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // get total lampiran
        $total_data = count($rs_id);
        $lampiran = ceil($total_data / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->terbilang($lampiran));
        // output
        parent::display();
    }

    // view notam
    public function preview_notam($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/notam.html");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_task->get_preview_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('task/izin_preview');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_task->get_list_data_slot_by_id(array($registrasi_id));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_preview(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_task->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_task->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // memo
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // get list frekuensi
        $params = array($registrasi_id);
        $rs_id = $this->m_task->get_izin_rute_data_by_kode_izin($params);
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        if (empty($rs_id)) {
            redirect('task/izin_preview');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_id as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_task->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_task->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
            $surat_persetujuan[$result['registrasi_id']]['perihal'] = $result['group_nm'];
        }
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // get total lampiran
        $total_data = count($rs_id);
        $lampiran = ceil($total_data / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->terbilang($lampiran));
        // output
        parent::display();
    }
 
    // view pencabutan all
    public function preview_pencabutan_all($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_preview/pencabutan_all.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);        
        // row style
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($detail['flow_id']);
        $this->smarty->assign("task", $task);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_approved_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // total rows
            $total_rows += count($izin_data);
            // kode izin
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        if ($detail["izin_flight"] == "domestik") {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_dom($an));
        } else {
            $this->smarty->assign("nomor_surat", $this->m_task->get_published_number_int($an));
        }
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_task->terbilang($lampiran));
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // get surat persetujuan sebelumnya untuk setiap frekuensi        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada
        $rs_kepada = $this->m_task->get_list_editorial(array('kepada'));
        $this->smarty->assign("rs_kepada_list", $rs_kepada);
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan
        $rs_tembusan = $this->m_task->get_list_editorial(array('tembusan'));
        $this->smarty->assign("rs_tembusan_list", $rs_tembusan);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);        
        // get uploaded files
        $rs_files = $this->m_monitoring_izin->get_list_file_pencabutan_uploaded(array($registrasi_id));
        $this->smarty->assign("rs_files", $rs_files);
        // output
        parent::display();
    }
    
    /*
     * DOWNLOAD
     */
    
    // DOWNLOAD BARU
    public function preview_baru_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $result = array();
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));        
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('baru'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        $no = 1;
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // DOWNLOAD FREKUENSI ADD
    public function preview_frekuensi_add_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $result = array();
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('frekuensi_add'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin penambahan frekuensi penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        $no = 1;
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // DOWNLOAD PENGHENTIAN
    public function preview_penghentian_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $result = array();
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('penghentian'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara menghentikan pelaksanaan penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        $no = 1;
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // DOWNLOAD PERPANJANGAN
    public function preview_perpanjangan_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $result = array();
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('perpanjangan'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin perpanjangan rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
		
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        $no = 1;
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // DOWNLOAD FREKUENSI DELETE
    public function preview_frekuensi_delete_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $result = array();
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            // ROWSPAN
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('frekuensi_delete'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["izin_published_letter"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["izin_published_date"]) . ';</li>';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin pengurangan frekuensi penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        $no = 1;
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // DOWNLOAD PERUBAHAN
    public function preview_perubahan_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            // ROWSPAN
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // total
            $total_old += $izin_rute['total_frekuensi'];
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $old['izin_id']));
            // total frekuensi
            $frekuensi_old[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $lampiran_old = ceil(count($rs_old) / 20);
        $total_lampiran = $lampiran + $lampiran_old + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_perubahan_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('perubahan'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["izin_published_letter"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["izin_published_date"]) . ';</li>';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin perubahan jadwal rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        /* ================= IZIN RUTE SEBELUMNYA ================= */
        $no = 1;
        for ($i = 2; $i <= ($lampiran_old + 1); $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">
                                <b>IZIN RUTE SEBELUMNYA</b>
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_old[$x]["izin_id"])) {
                    if ($rs_old[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_old[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_old[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_old[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_old[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_old[$x]["airlines_iata_cd"] . $rs_old[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_old[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing_old[$rs_old[$x]["izin_id"]] . ' / ' . $frekuensi_old[$rs_old[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($rs_old[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_old[$x]["end_date"]) . '</td>
                                            ';
                        $html .= '</tr>';
                        $temp = $rs_old[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_old[$x]["rowspan"];
                            if (($rs_old[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_old[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_old[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_old[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_old[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_old[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_old[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_old[$x]["airlines_iata_cd"] . $rs_old[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_old[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            $html .= '
                                                    <td align="center" rowspan="' . $rs_old[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_old[$x]["rowspan"] - $min)) . $pairing_old[$rs_old[$x]["izin_id"]] . ' / ' . $frekuensi_old[$rs_old[$x]["izin_id"]] . 'X</td>
                                                ';
                        }
                        $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($rs_old[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_old[$x]["end_date"]) . '</td>
                                            ';
                        $html .= '</tr>';
                        $temp = $rs_old[$x]["izin_id"];
                    }
                }
            }
            if ($i == ($lampiran_old + 1)) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_old . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        /* ================= PERUBAHAN IZIN RUTE ================= */
        $izin = 0;
        $per_page = 20;
        $no = 1;
        for ($i = ($lampiran_old + 2); $i <= (($lampiran + $lampiran_old) + 1); $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_st"] != 'pencabutan') {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // DOWNLOAD PENUNDAAN
    public function preview_penundaan_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            // ROWSPAN
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // total
            $total_old += $izin_rute['total_frekuensi'];
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $old['izin_id']));
            // total frekuensi
            $frekuensi_old[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $lampiran_old = ceil(count($rs_old) / 20);
        $total_lampiran = $lampiran + $lampiran_old + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('penundaan'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["izin_published_letter"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["izin_published_date"]) . ';</li>';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . $detail["airlines_nm"] . ' Nomor: ' . $detail["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($detail["izin_request_letter_date"]) . ' perihal Permohonan ';
        if ($detail["izin_perihal"] != "") {
            $html .= $detail["izin_perihal"];
        } else {
            $html .= $detail["group_nm"];
            if ($detail['izin_flight'] == "domestik") {
                $html .= ' Dalam Negeri';
            } else {
                $html .= ' Luar Negeri';
            }
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($detail["mdd"]) . ';</li>';
        foreach ($airport_iasm as $data) {
            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
        }
        $count = 1;
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_rs_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin penundaan pelaksanaan rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        /* ================= IZIN RUTE SEBELUMNYA ================= */
        $no = 1;
        for ($i = 2; $i <= ($lampiran_old + 1); $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">
                                <b>IZIN RUTE SEBELUMNYA</b>
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_old[$x]["izin_id"])) {
                    if ($rs_old[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_old[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_old[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_old[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_old[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_old[$x]["airlines_iata_cd"] . $rs_old[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_old[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing_old[$rs_old[$x]["izin_id"]] . ' / ' . $frekuensi_old[$rs_old[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($rs_old[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_old[$x]["end_date"]) . '</td>
                                            ';
                        $html .= '</tr>';
                        $temp = $rs_old[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_old[$x]["rowspan"];
                            if (($rs_old[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_old[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_old[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_old[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_old[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_old[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_old[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_old[$x]["airlines_iata_cd"] . $rs_old[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_old[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        if ($rs_old[$x]["izin_id"] != $temp) {
                            $html .= '
                                                    <td align="center" rowspan="' . $rs_old[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_old[$x]["rowspan"] - $min)) . $pairing_old[$rs_old[$x]["izin_id"]] . ' / ' . $frekuensi_old[$rs_old[$x]["izin_id"]] . 'X</td>
                                                ';
                        }
                        $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($rs_old[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_old[$x]["end_date"]) . '</td>
                                            ';
                        $html .= '</tr>';
                        $temp = $rs_old[$x]["izin_id"];
                    }
                }
            }
            if ($i == ($lampiran_old + 1)) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_old . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        /* ================= PERUBAHAN IZIN RUTE ================= */
        $izin = 0;
        $per_page = 20;
        $no = 1;
        for ($i = ($lampiran_old + 2); $i <= (($lampiran + $lampiran_old) + 1); $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        $html .= '<td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        $html .= '<td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }
   
    // DOWNLOAD PENCABUTAN ALL
    public function preview_pencabutan_all_download($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_registrasi_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('task/monitoring_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $result = array();
        $rs_id = $this->m_task->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
        $no = 0;
        $last_key = "";
        foreach ($rs_id as $new) {
            if ($last_key != $new['izin_id']) {
                $last_key = $new['izin_id'];
                $group_izin = 0;
                foreach ($rs_id as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_id[$no]['rowspan'] = $group_izin;
            }
            // get total frekuensi and pairing
            $izin_rute = $this->m_task->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $detail['airlines_id']));
            // get izin data
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_task->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
        }
        $no = 0;
        $last_key = "";
        foreach ($rs_old as $old) {
            if ($last_key != $old['izin_id']) {
                $last_key = $old['izin_id'];
                $group_izin = 0;
                foreach ($rs_old as $temps) {
                    if ($last_key == $temps['izin_id']) {
                        $group_izin++;
                    }
                }
                $rs_old[$no]['rowspan'] = $group_izin;
            }
            $no++;
        }
        // VARIABLES FOR PAGING
        $per_page = 20;
        $izin = 0;
        $temp = "";
        /*
         * DRAFT SURAT
         */
        // domestik
        if ($detail['izin_flight'] == 'domestik') {
            $group = array(1, 2, 5, 6, 7);
        }
        // internasional
        if ($detail['izin_flight'] == 'internasional') {
            $group = array(21, 22, 25, 26, 27);
        }
        if (in_array($detail['izin_group'], $group)) {
            $an = 'DRJU-DAU';
            // get user dirjen 63
            $users = $this->m_task->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_task->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_task->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_task->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_task->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_task->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_task->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_task->get_list_redaksional_by_registrasi(array($registrasi_id));
        $redaksional = '';
        $no = 1;
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // kepada yang terpilih
        $rs_kepada = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_task->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);
        // get uploaded files
        $rs_files = $this->m_monitoring_izin->get_list_file_pencabutan_uploaded(array($registrasi_id));
        $total_rs_files = count($rs_files);

        /*
         * PDF
         */
        // create barcode
        // load library
        $this->load->library('tcpdf');
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //set parameter barcode
        $barcode_url = $this->m_task->get_barcode_value(array('penghentian'));
        $url = base_url() . $barcode_url['pref_value'] . $detail['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 10, 30);
        // add a page
        $this->tcpdf->AddPage("A4");
        $romawi = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX", 10 => "X", 11 => "XI", 12 => "XII", 13 => "XIII", 14 => "XIV", 15 => "XV", 16 => "XVI", 17 => "XVII", 18 => "XVIII", 19 => "XIX", 20 => "XX", 21 => "XXI", 22 => "XXII", 23 => "XXIII", 24 => "XXIV", 25 => "XXV", 26 => "XXVI", 27 => "XXVII", 28 => "XXVIII", 29 => "XXIX", 30 => "XXX");
        $html = '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form-qr {
                margin: 0;
                padding: 0;
                border-collapse: collapse;
                text-align: justify;
                font-family: times;
                font-size: 25px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 25px;
            }

            .content p.caption {
                margin: 0;
                padding: 0;
                text-align: center;
                font-family: times;
                font-size: 24px;
            }
            ol {
                text-align: justify;
            }
            li {
                text-align: justify;
            }
            </style>
            <body class="common">
                <div class="page">
                    <div class="main-content">
                        <div class="content">
                            <table width="100%" cellpadding="2px">
                                <tr>
                                    <td width="15%" rowspan="6" style="border-bottom:2px solid black;"><img src="resource/doc/images/logo/logo.jpg" width="53px" style="vertical-align:middle"></td>
                                    <td width="85%" align="center" colspan="4"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size:38px" align="center" colspan="5"><b>DIREKTORAT JENDERAL PERHUBUNGAN UDARA</b></td>
                                </tr>
                                <tr>
                                    <td width="10%" style="border-bottom:2px solid black;"></td>
                                    <td style="font-size:20px;border-bottom:2px solid black;" width="25%">Jl. Medan Merdeka Barat No. 8<br/>Gedung Karya Lt.21<br/>Jakarta 10110</td>
                                    <td width="25%" style="font-size:20px;border-left: 1px solid black;border-right: 1px solid black;border-bottom:2px solid black;">   Telepon : 3503345</td>
                                    <td width="30%" style="font-size:20px;border-bottom:2px solid black;">   Fax : 3506662</td>
                                </tr>
                            </table>
                            <br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="30%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                </tr>
                                <tr>
                                    <td>Klasifikasi</td>
                                    <td>:</td>
                                    <td>PENTING</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td>:</td>
                                    <td>' . $total_lampiran . ' ( ' . $terbilang . ' ) Lembar</td>
                                    <td></td>
                                    <td>Kepada</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Perihal</td>
                                    <td style="vertical-align: top;">:</td>';
        if ($detail["izin_perihal"]) {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
        }
        $html .= '
                                    <td style="vertical-align: top;">Yth:</td>
                                    <td><u>PERIKSA ALAMAT TERLAMPIR</u></td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td colspan="3" style="text-align:justify; font-size:25px;">
                                        <ol>
                                            <li style="line-height:150%;">Menunjuk :
                                                <ol style="list-style-type: lower-alpha;">
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang - undangan yang terkait dengan penerbangan;
                                                    </li>';
        $count = 1;
        foreach ($rs_files as $data) {
            $html .= '<li style="line-height:150%;">' . $data["letter_subject"] . ' Nomor: ' . $data["letter_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["letter_date"]) . ' perihal ' . $data["letter_desc"];
            if ($count < $total_rs_files) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html .= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara menghentikan pelaksanaan penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . $detail["airlines_nm"] . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.</li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.</li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
        if ($an == "DRJU-DAU") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
        }
        $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
        if ($detail["izin_published_letter"] != "") {
            $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="54%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($detail['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . $detail["airlines_nm"] . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . $detail["airlines_nm"] . '</li>';
        }
        foreach ($rs_kepada as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_tembusan as $data) {
            $html .= '<li style="line-height:150%;">' . $data['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        $no = 1;
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                            <tcpdf method="AddPage"><br /><br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[$i] . ' Surat Nomor</td>
                                    <td width="1%">:</td>';
            if ($detail["izin_published_letter"] != "") {
                $html .= '<td width="54%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="54%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="15%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <hr>
                            <p class="caption">';
            if ($detail["izin_perihal"] == "") {
                $html .= '<b>' . strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]) . '</b>';
                $perihal = strtoupper($detail["group_nm"]) . ' ' . strtoupper($detail["izin_flight"]);
            } else {
                $html .= '<b>' . strtoupper($detail["izin_perihal"]) . '</b>';
                $perihal = strtoupper($detail["izin_perihal"]);
            }
            $html .= '
                                <br />
                                <b>' . $detail["airlines_nm"] . '</b>
                            </p>
                            <br />
                            <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                <tr>
                                    <td width="5%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'No</td>
                                    <td width="15%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Rute<br />Penerbangan</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Tipe<br />Pesawat</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Nomor<br />Penerbangan</td>
                                    <td width="16%" colspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Hari<br />Operasi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Frekuensi</td>
                                    <td width="10%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Total<br />Frekuensi</td>
                                    <td width="14%" rowspan="2" align="center">' . str_repeat('&nbsp;<br/>', 1) . 'Periode<br /> Efektif</td>
                                </tr>
                                <tr>
                                    <td width="8%" align="center">ETD</td>
                                    <td width="8%" align="center">ETA</td>
                                </tr>';
            $temp = "";
            for ($x = $izin; $x < $per_page; $x++) {
                if (isset($rs_id[$x]["izin_id"])) {
                    if ($rs_id[$x]["izin_id"] == $last_izin_id) {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            if ((($last_rowspan - $last_counter) % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . '.</td>';
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . ($last_rowspan - $last_counter) . '">' . (str_repeat('&nbsp;<br/>', ($last_rowspan - $last_counter) - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                        $last_izin_id = "";
                        $last_counter = "";
                        $last_rowspan = "";
                    } else {
                        $html .= '<tr>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $counter = 1;
                            $rowspan = $rs_id[$x]["rowspan"];
                            if (($rs_id[$x]["rowspan"] % 2) == 0) {
                                $min = 1;
                            } else {
                                $min = 0;
                            }
                            $html .= '<td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $no++ . '.</td>';
                        } else {
                            $counter = $counter + 1;
                        }
                        $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["tipe"] . '</td>';
                        if (strlen(trim($rs_id[$x]["flight_no"])) > 4) {
                            $html .= '<td align="center">' . $rs_id[$x]["flight_no"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["airlines_iata_cd"] . $rs_id[$x]["flight_no"] . '</td>';
                        }
                        $html .= '
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td> 
                                                <td align="center">' . $rs_id[$x]["doop"] . '</td>
                                                <td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                        if ($rs_id[$x]["izin_id"] != $temp) {
                            $html .= '
                                                <td align="center" rowspan="' . $rs_id[$x]["rowspan"] . '">' . (str_repeat('&nbsp;<br/>', $rs_id[$x]["rowspan"] - $min)) . $pairing[$rs_id[$x]["izin_id"]] . ' / ' . $frekuensi[$rs_id[$x]["izin_id"]] . 'X</td>
                                            ';
                        }
                        if ($rs_id[$x]["start_date"] < $total_approved["valid_start_date"]) {
                            $html .= '
                                                <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        } else {
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        }
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="7" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    </tr>';
            }
            $last_izin_id = $temp;
            $last_counter = $counter;
            $last_rowspan = $rowspan;
            $izin = $izin + 20;
            $per_page = $per_page + 20;
            $html .= '
                            </table>
        ';
            // Tambahan untuk QRCode
            // >> start
            $html .= '
                            <table class="table-form-qr" width="100%" cellpadding="1">
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
        ';
            if ($an == "DRJU-DAU") {
                $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
            } else {
                $html .= '<b>a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA<br />DIREKTUR ANGKUTAN UDARA</b>';
            }
            $html .= '
                                        <br />
                                        <b style="text-decoration: underline;">' . $users["operator_name"] . '</b>
                                        <br />
                                        ' . $users["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $users["operator_nip"] . '
                                    </td>
                                </tr>
                            </table>';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }
    
    /*
     * TERBILANG
     */

    function terbilang($x) {
        $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($x < 12)
            return " " . $abil[$x];
        elseif ($x < 20)
            return Terbilang($x - 10) . "belas";
        elseif ($x < 100)
            return Terbilang($x / 10) . " puluh" . Terbilang($x % 10);
        elseif ($x < 200)
            return " seratus" . Terbilang($x - 100);
        elseif ($x < 1000)
            return Terbilang($x / 100) . " ratus" . Terbilang($x % 100);
        elseif ($x < 2000)
            return " seribu" . Terbilang($x - 1000);
        elseif ($x < 1000000)
            return Terbilang($x / 1000) . " ribu" . Terbilang($x % 1000);
        elseif ($x < 1000000000)
            return Terbilang($x / 1000000) . " juta" . Terbilang($x % 1000000);
    }

}
