<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class izin_staff extends ApplicationBase {

    private $flow_id = 1;
    private $next_id = 2;

    //put your code here
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

    // MIGRASI
    public function migrasi($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_staff/migrasi.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/migrasi");
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
        // slot
        $rs_slot = $this->m_task->get_list_slot_time_by_id(array($detail['registrasi_id'], $detail['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
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
        /*
         * ACTION
         */
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 10));
        $this->smarty->assign("action", $action);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // display
        parent::display();
    }

    // BARU
    public function baru($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_staff/baru.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/baru");
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        /*
         * TARIF
         */
        $tarif = $this->m_task->get_tarif_rute_baru();
        $this->smarty->assign("tarif", $tarif);
        // display
        parent::display();
    }

    // PENUNDAAN
    public function penundaan($registrasi_id = "") {
        // set rule
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_staff/penundaan.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/penundaan");
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
        $rs_files = $this->m_task->get_list_file_uploaded(array($detail['registrasi_id'], 3, $detail['izin_flight']));
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
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 3));
        $this->smarty->assign("action", $action);
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
        $this->smarty->assign("template_content", "task/izin_staff/perubahan.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/perubahan");
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
        $this->smarty->assign("total_approved", $this->m_task->get_total_frekuensi_approved_by_registrasi_id($registrasi_id));
        $this->smarty->assign("total_approved_pencabutan", $this->m_task->get_total_frekuensi_approved_pencabutan_by_registrasi_id($registrasi_id));
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
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], 4));
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
        $this->smarty->assign("template_content", "task/izin_staff/frekuensi_add.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/frekuensi_add");
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
        $this->smarty->assign("template_content", "task/izin_staff/frekuensi_delete.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/frekuensi_delete");
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
        $this->smarty->assign("template_content", "task/izin_staff/perpanjangan.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/perpanjangan");
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
        $this->smarty->assign("template_content", "task/izin_staff/penghentian.html");
        // url path
        $this->smarty->assign("url_path", "task/izin_staff/penghentian");
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
     * END OF GROUP
     */

    /*
     * ACTION
     */

    // approval
    public function approved_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Registrasi', 'required');
        $this->tnotification->set_rules('izin_approval[]', 'Status', 'required');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $result = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($result)) {
            redirect('task/izin_rute');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // approve selected
            $izin_approval = $this->input->post('izin_approval');
            // --
            if (!empty($izin_approval)) {
                foreach ($izin_approval as $izin_id => $value) {
                    // update
                    $params = array(
                        'izin_approval' => $value,
                    );
                    $where = array(
                        'izin_id' => $izin_id,
                        'izin_completed' => '0',
                        'registrasi_id' => $registrasi_id,
                    );
                    $this->m_task->update_izin($params, $where);
                }
            }
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_staff/" . $result['group_alias'] . "/" . $registrasi_id);
    }

    // pending process
    public function pending_process($registrasi_id = "", $process_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // get task
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        // action
        $action = $this->m_task->get_action_control(array($this->com_user['role_id'], $detail['izin_group']));
        if ($action['action_revisi'] == '0') {
            redirect('task/izin_rute');
        }
        // catatan
        if (empty($detail['catatan'])) {
            // default error
            $this->tnotification->sent_notification("error", "Catatan kepada Airlines belum diisikan!");
            // default redirect
            redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('reject', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            $next_flow = 6; // member flow;
            $process_id = $this->m_task->get_process_id();
            // process flow
            $params = array($process_id, $registrasi_id, $next_flow, $this->com_user['user_id']);
            $this->m_task->insert_process($params);
            // send mail pending
            $this->m_email->mail_izin_pending($registrasi_id, $this->com_user['user_id']);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("task/izin_rute");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    // send process
    public function send_process($registrasi_id = "", $process_id = "") {
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
        // files attachment
        $total_check_files = $this->m_task->check_list_file_uploaded(array($registrasi_id, $detail['izin_group'], $detail['izin_flight']));
        if ($total_check_files > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat file yang belum di verifikasi!");
            redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // telaah staf
        $telaah = $this->m_task->get_detail_telaah_by_id(array($registrasi_id));
        if (empty($telaah)) {
            // default error
            $this->tnotification->sent_notification("error", "File telaah staf belum diupload!");
            redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // get list waiting
        $total_waiting = $this->m_task->get_list_waiting_approval_by_registrasi($registrasi_id);
        if ($total_waiting > 0) {
            // default error
            $this->tnotification->sent_notification("error", "Terdapat data rute yang belum di checklist ( Valid / Reject )!");
            redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            $next_flow = ($detail['flow_id'] + 1);
            $process_id = $this->m_task->get_process_id();
            // process flow
            $params = array($process_id, $registrasi_id, $next_flow, $this->com_user['user_id']);
            $this->m_task->insert_process($params);
            // get role next flow
            $next_role = $this->m_task->get_role_next_from(array($next_flow));
            // send mail to next flow
            $this->m_email->mail_izin_to_next_flow($registrasi_id, $next_role['role_id'], $this->com_user['user_id']);
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
        redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

    /*
     * FINISH GROUP
     */

    // finish process migrasi
    public function finish_migrasi_process($registrasi_id = "", $process_id = "") {
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
            redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // apakah semua di reject statusnya?
        $reject_all = $this->m_task->get_list_reject_all_by_registrasi($registrasi_id);
        if ($reject_all == '0') {
            // default error
            $this->tnotification->sent_notification("error", "Semua data izin rute di TOLAK, kembalikan pada airlines untuk diperbaiki!");
            redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
        }
        // update
        $params = array('approve', 'done', $this->com_user['user_id'], $process_id);
        if ($this->m_task->action_update($params)) {
            /*
             * update data rute
             */
            if ($detail['kode_izin'] != "") {
                $kode_izin = $detail['kode_izin'];
            } else {
                $kode_izin = $this->m_task->get_kode_izin_domestik(array($detail['airlines_id'], 'domestik'));
            }
            $rs_id = $this->m_task->get_list_rute_by_registrasi(array($registrasi_id));
            foreach ($rs_id as $data) {
                // approved
                if ($data['izin_approval'] == 'approved') {
                    // kode frekuensi
                    if ($detail['kode_frekuensi'] != "") {
                        $kode_frekuensi = $detail['kode_frekuensi'];
                    } else {
                        $kode_frekuensi = $this->m_task->get_kode_frekuensi($kode_izin);
                    }// update data izin
                    $params = array(
                        'izin_completed' => '1',
                        'izin_approval' => 'approved',
                        'izin_active' => '1',
                        'kode_izin' => $kode_izin,
                        'kode_frekuensi' => $kode_frekuensi,
                        'izin_payment_st' => '1',
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
            $params = array(
                'izin_completed' => '1',
                'izin_approval' => 'approved',
                'izin_published_by' => $this->com_user['user_id'],
                'izin_published_role' => $this->com_user['role_id'],
                'izin_valid_st' => 'yes',
                'izin_valid_by' => $this->com_user['user_id'],
                'payment_st' => '22',
                'payment_due_date' => NULL,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            $this->m_task->registrasi_done_process($params, $where);
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
        redirect("task/izin_staff/" . $detail['group_alias'] . "/" . $registrasi_id);
    }

}
