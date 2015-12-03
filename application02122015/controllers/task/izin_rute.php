<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class izin_rute extends ApplicationBase {

    // --
    public function __construct() {
        parent::__construct();
        //load model
        $this->load->model("regulator/m_task");
        //load library
        $this->load->library("tnotification");
        $this->load->library("doslibrary");
    }

    // list data waiting
    public function index() {
        $this->_set_page_rule("R");
        // set template
        $this->smarty->assign("template_content", "task/izin_rute/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_izin');
        // search parameters
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $group_nm = empty($search['group_nm']) ? '%' : '%' . $search['group_nm'] . '%';
        // assign search
        $this->smarty->assign("search", $search);
        // get list data
        $rs_id = $this->m_task->get_list_my_task_waiting(array($this->com_user['role_id'], $this->com_user['user_id'], $airlines_nm, $group_nm));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // search process
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_izin');
        } else {
            // data
            $params = array(
                "airlines_nm" => $this->input->post("airlines_nm"),
                "group_nm" => $this->input->post("group_nm"),
            );
            $this->tsession->set_userdata("search_izin", $params);
        }
        // redirect
        redirect("task/izin_rute");
    }

    // telaah process
    public function telaah_process() {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('url_path', 'URL', 'trim|required');
        // id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $result = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($result)) {
            redirect('task/izin_rute');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $file = $_FILES['telaah_file'];
            // upload 1 per 1
            if (!empty($file['tmp_name'])) {
                // upload config
                $config['upload_path'] = 'resource/doc/telaah/' . $registrasi_id . '/';
                $config['allowed_types'] = '*';
                $this->tupload->initialize($config);
                // process upload
                if ($this->tupload->do_upload('telaah_file')) {
                    // jika berhasil
                    $data = $this->tupload->data();
                    // delete
                    $this->m_task->delete_telaah(array($registrasi_id));
                    // update
                    $params = array(
                        'registrasi_id' => $registrasi_id,
                        'telaah_file' => $data['file_name'],
                        'telaah_by' => $this->com_user['user_id'],
                        'telaah_date' => date('Y-m-d h:i:s'),
                    );
                    $this->m_task->insert_telaah($params);
                    // notification
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                } else {
                    // jika gagal
                    $this->tnotification->set_error_message($this->tupload->display_errors());
                }
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan, hasil telaah staff wajib di upload!");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect($this->input->post('url_path') . "/" . $registrasi_id);
    }

    // download attachment
    public function files_download($file_id = "") {
        // get detail data
        $params = array($file_id);
        $result = $this->m_task->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("task/izin_rute/");
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
            redirect('task/izin_rute/');
        }
    }

    // file proses
    public function file_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('izin_files[]', 'Checklist Attahcment', 'trim|required');
        $this->tnotification->set_rules('url_path', 'URL', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $result = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($result)) {
            redirect('task/izin_rute');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // udpate all
            $this->m_task->check_list_files_all(array('0', NULL, NULL, $registrasi_id));
            // update
            $izin_files = $this->input->post('izin_files');
            foreach ($izin_files as $file_id => $files) {
                $this->m_task->check_list_files(array($this->com_user['user_id'], '1', $file_id));
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect($this->input->post('url_path') . "/" . $registrasi_id);
    }

    // download
    public function slot_download($registrasi_id = "", $slot_id = "") {
        // get detail data
        $params = array($registrasi_id, $slot_id);
        $result = $this->m_task->get_detail_slot_by_id($params);
        if (empty($result)) {
            redirect("task/izin_rute/");
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
            redirect("task/izin_rute/");
        }
    }

    // download telaah
    public function telaah_download($registrasi_id = "") {
        // get detail data
        $result = $this->m_task->get_detail_telaah_by_id(array($registrasi_id));
        // filepath
        $file_path = 'resource/doc/telaah/' . $registrasi_id . '/' . $result['telaah_file'];
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . end(explode('/', $result['telaah_file'])) . '"');
            readfile($file_path);
            exit();
        } else {
            redirect("task/izin_rute/");
        }
    }

    // download
    public function letter_download($registrasi_id = "", $letter_id = "") {
        // get detail data
        $params = array($registrasi_id, $letter_id);
        $result = $this->m_task->get_detail_file_pencabutan_by_id($params);
        if (empty($result)) {
            redirect("task/izin_rute/");
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
            redirect("task/izin_rute/");
        }
    }

    /*
     * AJAX
     */

    // detail data notes
    public function detail_notes_by_id() {
        // parameter
        $izin_id = $this->input->post('izin_id');
        // data pemohon
        $result = $this->m_task->get_rute_notes_by_id($izin_id);
        // return data
        $data = array(
            "izin_id" => $result['izin_id'],
            "notes" => $result['notes'],
        );
        echo json_encode($result);
    }

    // save notes
    public function save_notes() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('izin_id', 'Nama Pemohon', 'required');
        $this->tnotification->set_rules('notes', 'Catatan', 'trim|max_length[100]');
        // params
        $izin_id = $this->input->post('izin_id');
        $notes = $this->input->post('notes');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                'notes' => $notes,
            );
            $where = array(
                'izin_id' => $izin_id,
            );
            // execute update
            if ($this->m_task->update_izin($params, $where)) {
                $message = array(
                    'success' => 'Catatan berhasil disimpan!',
                    'izin_id' => $izin_id,
                    'notes' => $notes,
                );
                echo json_encode($message);
            } else {
                $message = array(
                    'success' => 'Catatan gagal disimpan!',
                    'izin_id' => $izin_id,
                    'notes' => $notes,
                );
                echo json_encode($message);
            }
        } else {
            $message = array(
                'success' => 'Catatan gagal disimpan!',
                'izin_id' => $izin_id,
                'notes' => $notes,
            );
            echo json_encode($message);
        }
    }

    // save catatan
    public function save_catatan() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Pendaftaran', 'required');
        $this->tnotification->set_rules('process_id', 'ID Proses', 'required');
        $this->tnotification->set_rules('catatan', 'Catatan', 'trim|max_length[5000]');
        // params
        $registrasi_id = $this->input->post('registrasi_id');
        $process_id = $this->input->post('process_id');
        $catatan = $this->input->post('catatan');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                "catatan" => $catatan,
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
                'process_id' => $process_id,
            );
            // execute update
            if ($this->m_task->update_process($params, $where)) {
                $message = array('success' => 'Catatan berhasil disimpan!');
                echo json_encode($message);
            } else {
                $message = array('success' => 'Catatan gagal disimpan!');
                echo json_encode($message);
            }
        } else {
            $message = array('success' => 'Catatan gagal disimpan!');
            echo json_encode($message);
        }
    }

    // save perihal
    public function save_perihal() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Pendaftaran', 'required');
        $this->tnotification->set_rules('perihal', 'Perihal', 'trim|required|max_length[255]');
        // params
        $registrasi_id = $this->input->post('registrasi_id');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                "izin_perihal" => $this->input->post('perihal'),
            );
            $where = array(
                'registrasi_id' => $registrasi_id,
            );
            // execute update
            if ($this->m_task->update_registrasi($params, $where)) {
                $message = array(
                    'success' => '1',
                    'perihal' => $this->input->post('perihal'),
                );
                echo json_encode($message);
            } else {
                $message = array('success' => '0');
                echo json_encode($message);
            }
        } else {
            $message = array('success' => '0');
            echo json_encode($message);
        }
    }

    // save memo
    public function save_memo() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('url_path', 'URL', 'trim|required');
        $this->tnotification->set_rules('memo', 'Catatan Tambahan', 'trim|required|max_length[5000]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array(
                "memo_id" => $this->m_task->get_process_id(),
                "registrasi_id" => $this->input->post('registrasi_id'),
                "memo" => $this->input->post('memo'),
                "memo_by" => $this->com_user['user_id'],
                "memo_date" => date('Y-m-d h:i:s'),
            );
            // execute update
            if ($this->m_task->insert_memo_process($params)) {
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
        redirect($this->input->post('url_path') . "/" . $this->input->post('registrasi_id'));
    }

    // delete memo
    public function delete_memo() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('memo_id', 'ID', 'required');
        // params
        $memo_id = $this->input->post('memo_id');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $where = array(
                'memo_id' => $memo_id,
            );
            // execute update
            if ($this->m_task->delete_memo_process($where)) {
                $message = array(
                    'success' => '1',
                );
                echo json_encode($message);
            } else {
                $message = array(
                    'success' => '0',
                );
                echo json_encode($message);
            }
        } else {
            $message = array(
                'success' => '0',
            );
            echo json_encode($message);
        }
    }

    // save editorial
    public function save_editorial() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('url_path', 'URL', 'trim|required');
        $this->tnotification->set_rules('redaksional_id', 'Redaksional', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // delete tembusan
            $params = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "tembusan_value" => $this->input->post('redaksional_id'),
            );
            $this->m_task->delete_tembusan($params);
            // insert tembusan
            $params = array(
                "tembusan_id" => $this->m_task->get_process_id(),
                "registrasi_id" => $this->input->post('registrasi_id'),
                "tembusan_value" => $this->input->post('redaksional_id'),
                "tembusan_by" => $this->com_user['user_id'],
                "tembusan_date" => date("Y-m-d H:i:s"),
            );
            // execute update
            if ($this->m_task->insert_tembusan($params)) {
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
        redirect($this->input->post('url_path') . "/" . $this->input->post('registrasi_id'));
    }

    // delete tembusan
    public function delete_tembusan() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('tembusan_id', 'ID', 'required');
        // params
        $tembusan_id = $this->input->post('tembusan_id');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $where = array(
                'tembusan_id' => $tembusan_id,
            );
            // execute update
            if ($this->m_task->delete_tembusan($where)) {
                $message = array(
                    'success' => '1',
                );
                echo json_encode($message);
            } else {
                $message = array(
                    'success' => '0',
                );
                echo json_encode($message);
            }
        } else {
            $message = array(
                'success' => '0',
            );
            echo json_encode($message);
        }
    }

    // list data catatan
    public function list_catatan_by_id() {
        $result = array();
        echo json_encode($result);
    }

    /*
     * DRAFT SURAT PENERBITAN
     */

    // BARU
    public function preview_baru($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/baru.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_baru");
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // FREKUENSI ADD
    public function preview_frekuensi_add($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/frekuensi_add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_frekuensi_add");
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
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // PENGHENTIAN
    public function preview_penghentian($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/penghentian.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_penghentian");
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
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // PERPANJANGAN
    public function preview_perpanjangan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/perpanjangan.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_perpanjangan");
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
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // FREKUENSI DELETE
    public function preview_frekuensi_delete($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/frekuensi_delete.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_frekuensi_delete");
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
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // PERUBAHAN
    public function preview_perubahan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/perubahan.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_perubahan");
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
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // PENUNDAAN
    public function preview_penundaan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/penundaan.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_penundaan");
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
        // // get surat persetujuan sebelumnya untuk setiap frekuensi
        // $surat_persetujuan = array();
        // if (!empty($kode_frekuensi)) {
        //     foreach ($kode_frekuensi as $kode => $st) {
        //         // get last penerbitan by kode izin
        //         $surat_persetujuan[] = $kode;
        //     }
        // }
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
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // PENCABUTAN ALL
    public function preview_pencabutan_all($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/izin_rute/pencabutan_all.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
        }
        $this->smarty->assign("detail", $detail);
        // url path
        $this->smarty->assign("url_path", "task/izin_rute/preview_penghentian");
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
        $rs_files = $this->m_task->get_list_file_pencabutan_uploaded(array($registrasi_id));
        $this->smarty->assign("rs_files", $rs_files);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }
    
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
//        foreach ($airport_iasm as $data) {
//            $html .= '<li style="line-height:150%;">Slot Clearance dari Bandar Udara' . $data["airport_nm"] . ', ' . $data["airport_region"] . ' ( sesuai data dari IASM );</li>';
//        }
//        $count = 1;
//        foreach ($rs_slot as $data) {
//            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor: ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
//            if ($count < $total_rs_slot) {
//                $html .= ';</li>';
//            } else {
//                $html .= ',</li>';
//            }
//            $count++;
//        }
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $detail = $this->m_task->get_detail_registrasi_waiting_by_id(array($registrasi_id, $this->com_user['role_id']));
        if (empty($detail)) {
            redirect('task/izin_rute');
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
        $rs_files = $this->m_task->get_list_file_pencabutan_uploaded(array($registrasi_id));
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

}
