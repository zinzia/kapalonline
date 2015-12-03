<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class izin_dirjen extends ApplicationBase {

    private $flow_id = 5;
    private $prev_id = 4;

    // put your code here
    public function __construct() {
        parent::__construct();
        // load model
        $this->load->model("regulator/m_task");
        $this->load->model("m_email");
        // load library
        $this->load->library("tnotification");
    }

    /*
     * GROUP
     */

    // BARU
    public function baru($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/baru.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/baru");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // detail telaah
        $this->smarty->assign("telaah", $this->m_task->get_detail_telaah_by_id(array($registrasi_id)));
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        // list slot by iasm
        $total = count($airport_iasm);
        if ($total > 0) {
            $rs_slot_iasm = $this->m_task->get_list_slot_iasm_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
            $this->smarty->assign("rs_slot_iasm", $rs_slot_iasm);
        }
        // files
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 1, $detail['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        $this->smarty->assign("total_files", count($rs_files));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 1));
        $this->smarty->assign("action", $action);
        /*
         * TARIF
         */
        $tarif = $this->m_task->get_tarif_rute_baru();
        $this->smarty->assign("tarif", $tarif);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // PERUBAHAN
    public function perubahan($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/perubahan.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/perubahan");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // detail telaah
        $this->smarty->assign("telaah", $this->m_task->get_detail_telaah_by_id(array($registrasi_id)));
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        // list slot by iasm
        $total = count($airport_iasm);
        if ($total > 0) {
            $rs_slot_iasm = $this->m_task->get_list_slot_iasm_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
            $this->smarty->assign("rs_slot_iasm", $rs_slot_iasm);
        }
        // files
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 4, $detail['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        $this->smarty->assign("total_files", count($rs_files));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $izin_st = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // status
            $izin_st[$izin_rute['izin_id']] = $izin_rute['izin_st'];
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
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("izin_st", $izin_st);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);
        $this->smarty->assign("total_approved", $total_approved);
        // get total frekuensi di cabut
        $total_approved_pencabutan = $this->m_task->get_total_frekuensi_approved_pencabutan_by_registrasi_id($registrasi_id);
        $this->smarty->assign("total_approved_pencabutan", $total_approved_pencabutan);
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
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        // perhitungan selisih
        $selisih = $total_approved['frekuensi'] - $total_approved_pencabutan['frekuensi'] - $total_old;
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 5));
        $this->smarty->assign("action", $action);
        /*
         * TARIF
         */
        $tarif = $this->m_task->get_tarif_rute_frekuensi();
        $this->smarty->assign("tarif", $tarif);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // PENAMBAHAN FREKUENSI
    public function frekuensi_add($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/frekuensi_add.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/frekuensi_add");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // detail telaah
        $this->smarty->assign("telaah", $this->m_task->get_detail_telaah_by_id(array($registrasi_id)));
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        // list slot by iasm
        $total = count($airport_iasm);
        if ($total > 0) {
            $rs_slot_iasm = $this->m_task->get_list_slot_iasm_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
            $this->smarty->assign("rs_slot_iasm", $rs_slot_iasm);
        }
        // files
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 5, $detail['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        $this->smarty->assign("total_files", count($rs_files));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $rs_old = $this->m_task->get_list_izin_rute_aktif_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $no = 1;
        $pairing_old = array();
        $frekuensi_old = array();
        $data = array();
        foreach ($rs_old as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        $total_old = $this->m_task->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $this->smarty->assign("total_old", $total_old['frekuensi']);
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 5));
        $this->smarty->assign("action", $action);
        /*
         * TARIF
         */
        $tarif = $this->m_task->get_tarif_rute_frekuensi();
        $this->smarty->assign("tarif", $tarif);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // PENGURANGAN FREKUENSI
    public function frekuensi_delete($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/frekuensi_delete.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/frekuensi_delete");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // detail telaah
        $this->smarty->assign("telaah", $this->m_task->get_detail_telaah_by_id(array($registrasi_id)));
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        // list slot by iasm
        $total = count($airport_iasm);
        if ($total > 0) {
            $rs_slot_iasm = $this->m_task->get_list_slot_iasm_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
            $this->smarty->assign("rs_slot_iasm", $rs_slot_iasm);
        }
        // files
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 6, $detail['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        $this->smarty->assign("total_files", count($rs_files));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $rs_old = $this->m_task->get_list_izin_rute_aktif_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $no = 1;
        $pairing_old = array();
        $frekuensi_old = array();
        $data = array();
        foreach ($rs_old as $izin_rute) {
            $izin_data = $this->m_task->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        $total_old = $this->m_task->get_total_frekuensi_existing_by_kode_izin(array($detail['kode_izin'], $detail['airlines_id']));
        $this->smarty->assign("total_old", $total_old['frekuensi']);
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 6));
        $this->smarty->assign("action", $action);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // PERPANJANGAN
    public function perpanjangan($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/perpanjangan.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/perpanjangan");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // detail telaah
        $this->smarty->assign("telaah", $this->m_task->get_detail_telaah_by_id(array($registrasi_id)));
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        // list slot by iasm
        $total = count($airport_iasm);
        if ($total > 0) {
            $rs_slot_iasm = $this->m_task->get_list_slot_iasm_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
            $this->smarty->assign("rs_slot_iasm", $rs_slot_iasm);
        }
        // files
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 2, $detail['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        $this->smarty->assign("total_files", count($rs_files));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
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
        }
        $this->smarty->assign("rs_old", $data);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // get total frekuensi
        $this->smarty->assign("total_old", $total_old);
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 2));
        $this->smarty->assign("action", $action);
        /*
         * TARIF
         */
        $tarif = $this->m_task->get_tarif_rute_frekuensi();
        $this->smarty->assign("tarif", $tarif);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // PENGHENTIAN
    public function penghentian($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/penghentian.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/penghentian");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // detail telaah
        $this->smarty->assign("telaah", $this->m_task->get_detail_telaah_by_id(array($registrasi_id)));
        // list slot by attachment
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_task->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        // list slot by iasm
        $total = count($airport_iasm);
        if ($total > 0) {
            $rs_slot_iasm = $this->m_task->get_list_slot_iasm_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
            $this->smarty->assign("rs_slot_iasm", $rs_slot_iasm);
        }
        // files
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 7, $detail['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        $this->smarty->assign("total_files", count($rs_files));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 7));
        $this->smarty->assign("action", $action);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    /*
     * GROUP PENCABUTAN
     */

    // PENGHENTIAN
    public function pencabutan_all($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/pencabutan_all.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/pencabutan_all");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // list pencabutan by attachment
        $rs_pencabutan = $this->m_task->get_list_file_pencabutan_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_pencabutan", $rs_pencabutan);
        $this->smarty->assign("total_pencabutan", count($rs_pencabutan));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 7));
        $this->smarty->assign("action", $action);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // PENGHENTIAN
    public function pencabutan_rute($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_dirjen/pencabutan_rute.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_dirjen/pencabutan_rute");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * TASK
         */
        // detail task
        $task = $this->m_task->get_detail_task_by_id($this->flow_id);
        $this->smarty->assign("task", $task);
        // prev task
        $prev = $this->m_task->get_detail_task_by_id(array($this->prev_id));
        $this->smarty->assign("prev", $prev);
        // next task
        $next = $this->m_task->get_detail_task_by_id(array($this->next_id));
        $this->smarty->assign("next", $next);
        // list process
        $this->smarty->assign("rs_process", $this->m_task->get_list_process_by_id(array($registrasi_id)));
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        $this->smarty->assign("detail", $detail);
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $row_style[$detail['izin_rute_start']] = 'class="start-row"';
        $row_style[$detail['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        // total catatan permohonan
        $this->smarty->assign("total_catatan", $this->m_task->get_total_catatan_by_registrasi($registrasi_id));
        // list pencabutan by attachment
        $rs_pencabutan = $this->m_task->get_list_file_pencabutan_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_pencabutan", $rs_pencabutan);
        $this->smarty->assign("total_pencabutan", count($rs_pencabutan));
        // -----------------------
        /*
         * RUTE PENERBANGAN
         */
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_task->get_list_izin_rute_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
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
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("notes", $notes);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_task->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get total frekuensi disetujui
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 7));
        $this->smarty->assign("action", $action);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    /*
     * END OF GROUP
     */

    /*
     * ACTION
     */

    // back process
    public function back_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_rollback'] == '0') {
            redirect('task/izin_rute');
        }
        // update
        $params = array('reject', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            $next_flow = ($detail['flow_id'] - 1);
            $process_id = $this->m_task->get_process_id();
            // process flow
            $params = array($process_id, $registrasi_id, $next_flow, $this->com_user['user_id']);
            $this->m_task->insert_process($params);
            // get role next flow
            $next_role = $this->m_task->get_role_next_from(array($next_flow));
            // send mail back flow
            $this->m_email->mail_izin_to_back_flow($registrasi_id, $next_role['role_id'], $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // reject process
    public function reject_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_send'] == '0') {
            redirect('task/izin_rute');
        }
        // update
        $params = array('reject', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // update data izin registrasi
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) { // DIRJEN
                $an = 'DRJU-DAU';
            }
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'rejected',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'payment_st' => '22',
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // update data izin
                $params = array(
                    'izin_completed' => '1',
                    'izin_approval' => 'rejected',
                );
                $where = array(
                    'izin_id' => $data['izin_id'],
                    'registrasi_id' => $data['registrasi_id'],
                );
                $this->m_task->update_izin($params, $where);
            }
            // send mail reject
            $this->m_email->mail_izin_reject($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    /*
     * FINISH GROUP
     */

    // finish process baru
    public function finish_baru_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            /*
             * update data rute
             */
            $kode_izin = $this->m_task->get_kode_izin_domestik(array($detail['airlines_id'], 'domestik'));
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // kode frekuensi
                    $kode_frekuensi = $this->m_task->get_kode_frekuensi($kode_izin);
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '1',
                        'kode_izin' => $kode_izin,
                        'kode_frekuensi' => $kode_frekuensi,
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            // get table tarif izin rute
            $tarif = $this->m_task->get_tarif_rute_baru();
            // get total frekuensi disetujui
            $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);
            // params
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'izin_valid_start' => $total_approved['valid_start_date'],
                'izin_valid_end' => $total_approved['valid_end_date'],
                'izin_valid_st' => 'yes',
                'izin_valid_by' => $this->com_user['user_id'],
                'kode_izin' => $kode_izin,
                'payment_st' => '00',
                'total_invoice' => $tarif,
                'payment_due_date' => date('Y-m-d', strtotime(date('Y-m-d') . ' + 7 days')),
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // finish process tambah frekuensi
    public function finish_frekuensi_add_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // kode frekuensi
                    $kode_frekuensi = $this->m_task->get_kode_frekuensi($detail['kode_izin']);
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '1',
                        'kode_izin' => $detail['kode_izin'],
                        'kode_frekuensi' => $kode_frekuensi,
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            // get table tarif izin rute
            $tarif = $this->m_task->get_tarif_rute_frekuensi();
            // get total frekuensi disetujui
            $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);
            $total_tarif = $tarif * $total_approved['frekuensi'];
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'izin_valid_start' => $total_approved['valid_start_date'],
                'izin_valid_end' => $total_approved['valid_end_date'],
                'izin_valid_st' => 'yes',
                'izin_valid_by' => $this->com_user['user_id'],
                'kode_izin' => $detail['kode_izin'],
                'payment_st' => '00',
                'total_invoice' => $total_tarif,
                'payment_due_date' => date('Y-m-d', strtotime(date('Y-m-d') . ' + 7 days')),
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // finish process kurangi frekuensi 
    public function finish_frekuensi_delete_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($data['kode_frekuensi'], $detail['airlines_id']));
                    // inactive kode frekuensi semua menjadi 0
                    $this->m_task->update_st_by_kode_frekuensi(array('0', $data['kode_frekuensi']));
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '0',
                        'izin_payment_st' => '1',
                        'izin_references' => $izin_rute['registrasi_id'],
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'payment_st' => '22',
                'payment_due_date' => NULL,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // finish process perpanjangan
    public function finish_perpanjangan_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        // $this->m_task->action_update($params)
        if ($this->m_task->action_update($params)) {
            // kode izin
            $kode_izin = $this->m_task->get_kode_izin_domestik(array($detail['airlines_id'], 'domestik'));
            // jika sudah ada kode izin
            $params = array(
                $detail['airlines_id'],
                $detail['izin_rute_start'], $detail['izin_rute_start'],
                $detail['izin_rute_end'], $detail['izin_rute_end'],
                $detail['izin_season'], $registrasi_id
            );
            $kode_izin_terdaftar = $this->m_task->get_kode_izin_terdaftar($params);
            if (!empty($kode_izin_terdaftar)) {
                $kode_izin = $kode_izin_terdaftar;
            }
            // total sebelumnya
            $total_old = 0;
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // kode frekuensi
                    $kode_frekuensi = $this->m_task->get_kode_frekuensi($kode_izin);
                    // total frekuensi rute sebelumnya
                    $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($data['kode_frekuensi'], $detail['airlines_id']));
                    $total_old += $izin_rute['total_frekuensi'];
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '1',
                        'kode_izin' => $kode_izin,
                        'kode_frekuensi' => $kode_frekuensi,
                        'izin_references' => $izin_rute['registrasi_id'],
                        'izin_kode_old' => $izin_rute['kode_frekuensi'],
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            // get table tarif izin rute
            $tarif = $this->m_task->get_tarif_rute_frekuensi();
            // get total frekuensi disetujui
            $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);
            // tarif
            $total_tarif = NULL;
            $payment_due_date = NULL;
            if ($total_approved['frekuensi'] > $total_old) {
                // harus membayar
                $payment_st = '00';
                $total_tarif = ($total_approved['frekuensi'] - $total_old) * $tarif;
                $payment_due_date = date('Y-m-d', strtotime(date('Y-m-d') . ' + 7 days'));
            } else {
                // tidak membayar
                $payment_st = '22';
                // update semua rute yang terbit menjadi '1'
                $params = array(
                    'izin_payment_st' => '1',
                );
                $where = array(
                    'registrasi_id' => $registrasi_id,
                    'airlines_id' => $detail['airlines_id'],
                );
                $this->m_task->update_status_bayar_approved($params, $where);
            }
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'izin_valid_start' => $total_approved['valid_start_date'],
                'izin_valid_end' => $total_approved['valid_end_date'],
                'izin_valid_st' => 'yes',
                'izin_valid_by' => $this->com_user['user_id'],
                'kode_izin' => $kode_izin,
                'payment_st' => $payment_st,
                'total_invoice' => $total_tarif,
                'payment_due_date' => $payment_due_date,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // finish process kurangi frekuensi 
    public function finish_penghentian_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // total frekuensi rute sebelumnya
                    $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($data['kode_frekuensi'], $detail['airlines_id']));
                    // inactive kode frekuensi semua menjadi 0
                    $this->m_task->update_st_by_kode_frekuensi(array('0', $data['kode_frekuensi']));
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '0',
                        'izin_payment_st' => '1',
                        'izin_references' => $izin_rute['registrasi_id'],
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'payment_st' => '22',
                'payment_due_date' => NULL,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // finish process perubahan
    public function finish_perubahan_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            $total_old = 0;
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // get izin rute
                    $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($data['kode_frekuensi'], $detail['airlines_id']));
                    $total_old += $izin_rute['total_frekuensi'];
                    // inactive kode frekuensi semua menjadi 0
                    $this->m_task->update_st_by_kode_frekuensi(array('0', $data['kode_frekuensi']));
                    /*
                     *  baru
                     */
                    if ($data['izin_st'] == 'baru') {
                        // kode frekuensi
                        $kode_frekuensi = $this->m_task->get_kode_frekuensi($detail['kode_izin']);
                        // update data izin
                        $params = array(
                            'izin_completed' => '1',
                            'izin_approval' => 'approved',
                            'izin_active' => '1',
                            'kode_izin' => $detail['kode_izin'],
                            'kode_frekuensi' => $kode_frekuensi,
                        );
                        $where = array(
                            'izin_id' => $data['izin_id'],
                            'registrasi_id' => $data['registrasi_id'],
                        );
                    }
                    /*
                     *  pencabutan
                     */
                    if ($data['izin_st'] == 'pencabutan') {
                        // update data izin
                        $params = array(
                            'izin_completed' => '1',
                            'izin_approval' => 'approved',
                            'izin_active' => '0',
                            'izin_payment_st' => '1',
                            'izin_references' => $izin_rute['registrasi_id'],
                        );
                        $where = array(
                            'izin_id' => $data['izin_id'],
                            'registrasi_id' => $data['registrasi_id'],
                        );
                    }
                    /*
                     * perubahan
                     */
                    if ($data['izin_st'] == 'perubahan') {
                        // update data izin
                        $params = array(
                            'izin_completed' => '1',
                            'izin_approval' => 'approved',
                            'izin_active' => '1',
                            'izin_references' => $izin_rute['registrasi_id'],
                        );
                        $where = array(
                            'izin_id' => $data['izin_id'],
                            'registrasi_id' => $data['registrasi_id'],
                        );
                    }
                    // exec
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            // get table tarif izin rute
            $tarif = $this->m_task->get_tarif_rute_frekuensi();
            // get total frekuensi disetujui
            $total_approved = $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id);
            $total_approved_pencabutan = $this->m_task->get_total_frekuensi_approved_pencabutan_by_registrasi_id($registrasi_id);
            // tarif
            $total_tarif = NULL;
            $payment_due_date = NULL;
            if (($total_approved['frekuensi'] - $total_approved_pencabutan['frekuensi']) > $total_old) {
                $payment_st = '00';
                $total_tarif = ($total_approved['frekuensi'] - $total_approved_pencabutan['frekuensi'] - $total_old) * $tarif;
                $payment_due_date = date('Y-m-d', strtotime(date('Y-m-d') . ' + 7 days'));
            } else {
                // tidak membayar
                $payment_st = '22';
                // update semua rute yang terbit menjadi '1'
                $params = array(
                    'izin_payment_st' => '1',
                );
                $where = array(
                    'registrasi_id' => $registrasi_id,
                    'airlines_id' => $detail['airlines_id'],
                );
                $this->m_task->update_status_bayar_approved($params, $where);
            }
            // params
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'izin_valid_start' => $total_approved['valid_start_date'],
                'izin_valid_end' => $total_approved['valid_end_date'],
                'izin_valid_st' => 'yes',
                'izin_valid_by' => $this->com_user['user_id'],
                'payment_st' => $payment_st,
                'total_invoice' => $total_tarif,
                'payment_due_date' => $payment_due_date,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    /*
     * FINISH PENCABUTAN
     */

    // finish process pencabutan all
    public function finish_pencabutan_all_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // total frekuensi rute sebelumnya
                    $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($data['kode_frekuensi'], $detail['airlines_id']));
                    // inactive kode frekuensi semua menjadi 0
                    $this->m_task->update_st_by_kode_frekuensi(array('0', $data['kode_frekuensi']));
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '0',
                        'izin_payment_st' => '1',
                        'izin_references' => $izin_rute['registrasi_id'],
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'payment_st' => '22',
                'payment_due_date' => NULL,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            /*
             * SIMPAN PADA CATATAN PENCABUTAN
             */
            $params = array(
                'registrasi_id' => $registrasi_id,
                'izin_rute_start' => $detail['izin_rute_start'],
                'izin_rute_end' => $detail['izin_rute_end'],
                'blacklist_start' => date('Y-m-d'),
                'blacklist_start' => date('Y-m-d', strtotime('+1 year')),
                'mdd' => date('Y-m-d H:i:s'),
                'mdb' => $this->com_user['user_id'],
            );
            $this->m_task->insert_izin_blacklist($params);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // finish process pencabutan rute
    public function finish_pencabutan_rute_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_publish'] == '0') {
            redirect('task/izin_rute');
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data yang belum di approve!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, gunakan tombol Tolak Semua Permohonan!");
            redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            // get list all rute by registrasi
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // total frekuensi rute sebelumnya
                    $izin_rute = $this->m_task->get_list_izin_rute_aktif_by_kode_frekuensi(array($data['kode_frekuensi'], $detail['airlines_id']));
                    // inactive kode frekuensi semua menjadi 0
                    $this->m_task->update_st_by_kode_frekuensi(array('0', $data['kode_frekuensi']));
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '0',
                        'izin_payment_st' => '1',
                        'izin_references' => $izin_rute['registrasi_id'],
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                } else {
                    // update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'rejected',
                        'izin_active' => '0',
                    );
                    $where = array(
                        'izin_id' => $data['izin_id'],
                        'registrasi_id' => $data['registrasi_id'],
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            /*
             * update data izin registrasi
             */
            $an = 'DJPU-DAU';
            if ($this->com_user['role_id'] == 63) {
                $an = 'DRJU-DAU';
            }
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_letter' => $this->m_task->get_published_number_dom($an),
                'izin_published_date' => date('Y-m-d H:i:s'),
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'payment_st' => '22',
                'payment_due_date' => NULL,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
            /*
             * SIMPAN PADA CATATAN PENCABUTAN
             */
            $params = array(
                'registrasi_id' => $registrasi_id,
                'izin_rute_start' => $detail['izin_rute_start'],
                'izin_rute_end' => $detail['izin_rute_end'],
                'blacklist_start' => date('Y-m-d'),
                'blacklist_start' => date('Y-m-d', strtotime('+1 year')),
                'mdd' => date('Y-m-d H:i:s'),
                'mdb' => $this->com_user['user_id'],
            );
            $this->m_task->insert_izin_blacklist($params);
            // send mail finish process
            $this->m_email->mail_izin_finish_process($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute/");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_dirjen/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

}
