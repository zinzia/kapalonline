<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class penghentian extends ApplicationBase {

    private $group_id = 7;
    private $flow_id = 6;
    private $next_id = 1;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('revisi/m_registrasi');
        $this->load->model('m_email');
        $this->load->model('m_airport');
        // load model
        $this->load->model('pendaftaran/m_slot_check');
        $this->smarty->assign("m_slot_check", $this->m_slot_check);
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    /*
     * STEP 1 : DATA PERMOHONAN
     */

    // Update Form
    public function index($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_pending_domestik/penghentian/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        // assign
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
        // catatan perbaikan
        $this->smarty->assign("catatan", $this->m_registrasi->get_catatan_perbaikan_by_registrasi($registrasi_id));
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
        $this->tnotification->set_rules('process_id', 'ID Proses', 'trim|required');
        $this->tnotification->set_rules('izin_request_letter_date', 'Tanggal Surat Permohonan', 'trim|required');
        $this->tnotification->set_rules('izin_request_letter', 'Nomor Surat Permohonan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('penanggungjawab', 'Penanggungjawab', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|maxlength[50]');
        // get detail data
        $registrasi_id = $this->input->post('registrasi_id');
        // -
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }// khusus pembatalan
        $action = $this->input->post('save');
        if ($action == 'Batalkan Permohonan') {
            // params
            $params = array(
                "izin_completed" => "1",
                "izin_approval" => "cancel",
            );
            $where = array(
                "registrasi_id" => $registrasi_id,
                "airlines_id" => $this->com_user['airlines_id'],
            );
            // update izin registrasi
            if ($this->m_registrasi->update_izin_registrasi($params, $where)) {
                // update izin process
                $params = array(
                    'process_st' => 'reject',
                    'action_st' => 'done',
                    'mdb_finish' => $this->com_user['user_id'],
                    'mdd_finish' => date('Y-m-d H:i:s'),
                );
                $where = array(
                    'process_id' => $this->input->post('process_id'),
                    "registrasi_id" => $registrasi_id,
                );
                $this->m_registrasi->update_izin_process($params, $where);
                // update izin rute
                $params = array(
                    'izin_approval' => 'rejected',
                    'izin_completed' => '1',
                    'mdb' => $this->com_user['user_id'],
                    'mdd' => date('Y-m-d H:i:s'),
                );
                $where = array(
                    'airlines_id' => $this->com_user['airlines_id'],
                    "registrasi_id" => $registrasi_id,
                );
                $this->m_registrasi->update_izin_rute($params, $where);
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dibatalkan");
                // redirect
                redirect('member/pending_izin');
            } else {
                // default error
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("error", "Data gagal dibatalkan");
                // default redirect
                redirect("izin_pending_domestik/penghentian/index/" . $this->input->post('registrasi_id'));
            }
        }
        // validasi jika rute sudah / dalam posisi akhir dicabut
        $params = array(
            $this->com_user['airlines_id'],
            $result['izin_rute_start'], $result['izin_rute_start'],
            $result['izin_rute_end'], $result['izin_rute_end'],
        );
        $total = $this->m_registrasi->get_total_rute_existing_canceled($params);
        if ($total > 0) {
            $this->tnotification->set_error_message('Rute telah dicabut, silahkan mengajukan kembali satu tahun setelah tanggal pencabutan!');
        }
        // di cek pada pengajuan yang waiting
        $params = array(
            $this->com_user['airlines_id'],
            $result['izin_rute_start'], $result['izin_rute_start'],
            $result['izin_rute_end'], $result['izin_rute_end'],
            $this->input->post('registrasi_id'),
        );
        $total = $this->m_registrasi->get_total_rute_process_by_new_rute($params);
        if ($total > 0) {
            $this->tnotification->set_error_message('Rute yang anda ajukan sedang dalam pengajuan!');
        }
        // Validasi jika rute yang sudah tersimpan, diubah / tidak sama, maka semua izin_rute dan data harus di bersihkan
        $params = array(
            $registrasi_id,
            $result['izin_rute_start'],
            $result['izin_rute_end'],
        );
        $total = $this->m_registrasi->get_total_rute_by_registrasi_id($params);
        if ($total > 0) {
            $this->tnotification->set_error_message('Terdapat rute yang sudah diinputkan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // data
            $params = array(
                "izin_request_letter_date" => $this->input->post('izin_request_letter_date'),
                "izin_request_letter" => $this->input->post('izin_request_letter'),
                "penanggungjawab" => $this->input->post('penanggungjawab'),
                "jabatan" => $this->input->post('jabatan'),
                "mdd" => date('Y-m-d H:i:s'),
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "airlines_id" => $this->com_user['airlines_id'],
                "izin_request_st" => '1',
                "izin_completed " => '0',
                "izin_approval" => 'waiting',
            );
            // insert
            if ($this->m_registrasi->update_izin_registrasi($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("izin_pending_domestik/penghentian/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_domestik/penghentian/index/" . $this->input->post('registrasi_id'));
    }

    /*
     * STEP 2 : RUTE PENERBANGAN
     */

    // list data rute
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_domestik/penghentian/list.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        $this->smarty->assign("detail", $result);
        // rows color
        $row_style[$result['izin_rute_start']] = 'class="start-row"';
        $row_style[$result['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);

        // list rute
        $pairing = array();
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_pending_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_pending_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        // frekuensi       
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // catatan perbaikan
        $this->smarty->assign("catatan", $this->m_registrasi->get_catatan_perbaikan_by_registrasi($registrasi_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add process
    public function list_rute_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        // validasi total
        $params = array($this->input->post('registrasi_id'), $this->com_user['airlines_id']);
        $total = $this->m_registrasi->get_total_izin_data_by_registrasi($params);
        if (empty($total)) {
            $this->tnotification->set_error_message('Data rute penerbangan belum diinput!');
        }
        // check jika msh ada yang kosong
        $params = array($this->input->post('registrasi_id'), $this->com_user['airlines_id']);
        $total = $this->m_registrasi->get_izin_rute_empty_data_by_id($params);
        if (empty($total)) {
            // notification
            $this->tnotification->set_error_message('Terdapat data rute yang belum dilengkapi!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect to file attachment
            redirect("izin_pending_domestik/penghentian/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_domestik/penghentian/list_rute/" . $this->input->post('registrasi_id'));
    }

    /*
     * STEP 4 : FILE ATTACHMENT
     */

    // files attachment
    public function list_files($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_pending_domestik/penghentian/files.html");
        // get detail data registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        $this->smarty->assign("detail", $result);
        // list persyaratan
        $rs_files = $this->m_registrasi->get_list_file_required_domestik(array($result['izin_group'], $result['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_registrasi->get_list_file_uploaded(array($registrasi_id));
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
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // upload 1 per 1
            $rs_files = $this->m_registrasi->get_list_file_required_domestik(array($result ['izin_group'], $result['izin_flight']));
            foreach ($rs_files as $k => $files) {
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
                        $file_id = $this->m_registrasi->get_data_id() + $k;
                        $filepath = 'resource/doc/izin/' . $registrasi_id . '/' . $files['ref_id'] . '/' . $data['file_name'];
                        $this->m_registrasi->update_files(array($registrasi_id, $files['ref_id']), array($file_id, $registrasi_id, $filepath, $data['file_name'], $files['ref_id']));
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
        redirect("izin_pending_domestik/penghentian/list_files/" . $registrasi_id);
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_registrasi->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("izin_pending_domestik/penghentian/list_files/" . $data_id);
        }
        // filepath
        $file_path = $result['file_path'];
        if (is_file($file_path)) {
            // download
            header('Content-Description: Download Files');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment;
            filename = "' . end(explode('/', $file_path)) . '"');
            readfile($file_path);
            exit();
        } else {
            redirect('member/pending_izin');
        }
    }

    // file process
    public function files_next() {
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        // validation
        if (!$this->m_registrasi->is_file_completed(array($registrasi_id, $result['izin_group'], $result['izin_flight']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("izin_pending_domestik/penghentian/review/" . $registrasi_id);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_domestik/penghentian/list_files/" . $registrasi_id);
    }

    /*
     * STEP 5 : REVIEW
     * 
     */

    // review
    public function review($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_domestik/penghentian/review.html");
        // detail registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        $this->smarty->assign("detail", $result);
        // rows color
        $row_style[$result['izin_rute_start']] = 'class="start-row"';
        $row_style[$result['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);

        // catatan perbaikan
        $this->smarty->assign("catatan", $this->m_registrasi->get_catatan_perbaikan_by_registrasi($registrasi_id));
        
        // list rute
        $pairing = array();
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_pending_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_pending_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // list persyaratan
        $rs_files = $this->m_registrasi->get_list_file_required_domestik(array($result['izin_group'], $result['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_registrasi->get_list_file_uploaded(array($registrasi_id));
        foreach ($rs_uploaded as $uploaded) {
            $file_uploaded[] = $uploaded['ref_id'];
            $name_uploaded[$uploaded['ref_id']] = $uploaded['file_name'];
        }
        $this->smarty->assign("file_uploaded", $file_uploaded);
        $this->smarty->assign("name_uploaded", $name_uploaded);
        // get total frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
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
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        // validasi total
        $params = array($this->input->post('registrasi_id'), $this->com_user['airlines_id']);
        $total = $this->m_registrasi->get_total_izin_data_by_registrasi($params);
        if (empty($total)) {
            $this->tnotification->set_error_message('Data rute penerbangan belum diinput!');
        }
        // check jika msh ada yang kosong
        $params = array($this->input->post('registrasi_id'), $this->com_user['airlines_id']);
        $total = $this->m_registrasi->get_izin_rute_empty_data_by_id($params);
        if (empty($total)) {
            // notification
            $this->tnotification->set_error_message('Terdapat data rute yang belum dilengkapi!');
        }
        // validation file persyaratan
        if (!$this->m_registrasi->is_file_completed(array($registrasi_id, $result['izin_group'], $result['izin_flight']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                'process_st' => 'approve',
                'action_st' => 'done',
                'mdb_finish' => $this->com_user['user_id'],
                'mdd_finish' => date('Y-m-d H:i:s'),
            );
            $where = array(
                'process_id' => $result['process_id'],
                'registrasi_id' => $registrasi_id,
            );
            if ($this->m_registrasi->update_izin_process($params, $where)) {
                // insert next flow
                $process_id = $this->m_registrasi->get_data_id();
                $params = array(
                    'process_id' => $process_id,
                    'registrasi_id' => $registrasi_id,
                    'flow_id' => 1,
                    'mdb' => $this->com_user['user_id'],
                    'mdd' => date('Y-m-d H:i:s'),
                );
                $this->m_registrasi->insert_izin_process($params);
                // update status
                $params = array(
                    'izin_request_st' => '1',
                    'izin_request_by' => $this->com_user['user_id'],
                    'izin_request_date' => date('Y-m-d H:i:s'),
                );
                $where = array(
                    'registrasi_id' => $registrasi_id,
                    'airlines_id' => $this->com_user['airlines_id'],
                );
                $this->m_registrasi->update_izin_registrasi($params, $where);
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Terima kasih telah melakukan pendaftaran Permohonan " . $result['group_nm'] . ' melalui sistem pelayanan berbasis online.');
                // default redirect
                redirect("member/pending_izin");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_domestik/penghentian/review/" . $registrasi_id);
    }

}
