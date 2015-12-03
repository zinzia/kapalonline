<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class frekuensi_delete extends ApplicationBase {

    private $group_id = 6;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('pendaftaran/m_registrasi');
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

    // Form Permohonan
    public function index($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_domestik/frekuensi_delete/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
        } else {
            // kode izin
            $kode_izin = $result['kode_izin'];
            $last_izin = $this->m_registrasi->get_detail_izin_rute_by_kode_izin(array($this->com_user['airlines_id'], 'domestik', $kode_izin));
            $result['input_pax_cargo'] = $last_izin['pax_cargo'];
            $result['input_masa_berlaku'] = strtoupper($this->datetimemanipulation->get_full_date($last_izin['izin_start_date'], 'ins')) . ' / ';
            $result['input_masa_berlaku'] .= strtoupper($this->datetimemanipulation->get_full_date($last_izin['izin_expired_date'], 'ins'));
        }
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
        // list izin rute
        $rs_id = $this->m_registrasi->get_list_izin_rute_by_airlines(array($this->com_user['airlines_id'], 'domestik'));
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // Step 1 : Process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Registrasi', 'trim|required');
        $this->tnotification->set_rules('izin_request_letter_date', 'Tanggal Surat Permohonan', 'trim|required');
        $this->tnotification->set_rules('izin_request_letter', 'Nomor Surat Permohonan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('kode_izin', 'Rute Pairing', 'trim|required');
        $this->tnotification->set_rules('input_pax_cargo', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('input_masa_berlaku', 'Masa Berlaku');
        $this->tnotification->set_rules('penanggungjawab', 'Penanggungjawab', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('izin_season', 'Season Code', 'trim|required|maxlength[3]');
        $this->tnotification->set_rules('kode_izin', 'Kode Rute', 'trim|required');
        // kode izin
        $kode_izin = $this->input->post('kode_izin');
        $detail = $this->m_registrasi->get_detail_izin_rute_by_kode_izin(array($this->com_user['airlines_id'], 'domestik', $kode_izin));
        // di cek pada pengajuan yang waiting
        $params = array(
            $this->com_user['airlines_id'],
            $detail['izin_rute_start'], $detail['izin_rute_start'],
            $detail['izin_rute_end'], $detail['izin_rute_end'],
            $this->input->post('registrasi_id'),
        );
        $total = $this->m_registrasi->get_total_rute_process_by_new_rute($params);
        if ($total > 0) {
            $this->tnotification->set_error_message('Rute yang anda ajukan sedang dalam pengajuan!');
        }
        // Validasi jika rute yang sudah tersimpan, diubah / tidak sama, maka semua izin_rute dan data harus di bersihkan
        $params = array(
            $this->input->post('registrasi_id'),
            $detail['izin_rute_start'],
            $detail['izin_rute_end'],
        );
        $total = $this->m_registrasi->get_total_rute_by_registrasi_id($params);
        if ($total > 0) {
            $this->tnotification->set_error_message('Terdapat rute yang sudah diinputkan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // update registrasi
            $params = array(
                "izin_request_letter_date" => $this->input->post('izin_request_letter_date'),
                "izin_request_letter" => $this->input->post('izin_request_letter'),
                "izin_rute_start" => $detail['izin_rute_start'],
                "izin_rute_end" => $detail['izin_rute_end'],
                "pax_cargo" => $detail['pax_cargo'],
                "penanggungjawab" => $this->input->post('penanggungjawab'),
                "jabatan" => $this->input->post('jabatan'),
                "izin_season" => $this->input->post('izin_season'),
                "kode_izin" => $kode_izin,
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "airlines_id" => $this->com_user['airlines_id'],
                "izin_request_st" => '0',
            );
            // insert
            if ($this->m_registrasi->update_izin_registrasi($params, $where)) {
                // unset session
                $this->tsession->unset_userdata('search_slot_rute');
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("izin_domestik/frekuensi_delete/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_domestik/frekuensi_delete/index/" . $this->input->post('registrasi_id'));
    }

    /*
     * STEP 2 : RUTE PENERBANGAN
     */

    // list data rute
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_domestik/frekuensi_delete/list.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
        }
        $this->smarty->assign("detail", $result);
        // rows color
        $row_style[$result['izin_rute_start']] = 'class="start-row"';
        $row_style[$result['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
                
        /*
         * RUTE PENERBANGAN EXISTING
         */
        $pairing_old = array();
        $frekuensi_old = array();
        $masa_berlaku = array();
        $data = array();
        $selected = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_aktif_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // masa berlaku
            $masa_berlaku[$izin_rute['izin_id']]['start_date'] = $izin_rute['izin_start_date'];
            $masa_berlaku[$izin_rute['izin_id']]['end_date'] = $izin_rute['izin_expired_date'];
        }
        $this->smarty->assign("rs_existing", $data);
        $this->smarty->assign("masa_berlaku", $masa_berlaku);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // data sebelumnya
        $total_existing = $this->m_registrasi->get_total_frekuensi_existing_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
                                
        /*
         * RUTE PENERBANGAN YANG DIHAPUS
         */
        $izin_st = array();
        $pairing = array();
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_waiting_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izin dat
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // status
            $izin_st[$izin_rute['izin_id']] = $izin_rute['izin_st'];
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("izin_st", $izin_st);
        $this->smarty->assign("rute_selected", $rute_selected);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));                
        
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // create rute id
    public function rute_add_process($registrasi_id = "", $kode_frekuensi = "") {
        // set page rules
        $this->_set_page_rule("C");
        // check registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
        }
        // get rute existing by $kode_frekuensi
        $izin_rute = $this->m_registrasi->get_detail_izin_rute_aktif_by_kode_frekuensi(array($kode_frekuensi, $this->com_user['airlines_id']));
        // --
        if (!empty($izin_rute)) {
            // get izin data
            $izin_data = $this->m_registrasi->get_list_izin_data_existing_by_kode_frekuensi(array($kode_frekuensi));
            // delete rute registered
            $where = array(
                'registrasi_id' => $registrasi_id,
                'kode_frekuensi' => $kode_frekuensi,
            );
            $this->m_registrasi->delete_izin_rute($where);
            // add izin rute
            $izin_id = $this->m_registrasi->get_data_id();
            $params = $izin_rute;
            $params['izin_id'] = $izin_id;
            $params['registrasi_id'] = $registrasi_id;
            $params['izin_completed'] = '0';
            $params['izin_st'] = 'pencabutan';
            $params['izin_approval'] = 'waiting';
            $params['izin_active'] = '0';
            $params['mdb'] = $this->com_user['user_id'];
            $params['mdd'] = date('Y-m-d H:i:s');
            // insert izin rute
            if ($this->m_registrasi->insert_izin_rute($params)) {
                $no = 1;
                foreach ($izin_data as $data) {
                    // id
                    $rute_id = $this->m_registrasi->get_data_id() + $no++;
                    // overwrite
                    $data['izin_id'] = $izin_id;
                    $data['rute_id'] = $rute_id;
                    unset($data['kode_izin']);
                    unset($data['kode_frekuensi']);
                    unset($data['frekuensi']);
                    // exec
                    $this->m_registrasi->insert_izin_data($data);
                }
                // notification
                $this->tnotification->sent_notification("success", "Data has been created!");
                redirect('izin_domestik/frekuensi_delete/list_rute/' . $registrasi_id);
            }
        }
        // default notification
        $this->tnotification->sent_notification("error", "An unexpected error has occurred");
        redirect('izin_domestik/frekuensi_delete/list_rute/' . $registrasi_id);
    }

    // delete process
    public function rute_delete($registrasi_id = "", $izin_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array(
            'registrasi_id' => $registrasi_id,
            'izin_id' => $izin_id,
            'airlines_id' => $this->com_user['airlines_id'],
        );
        // execute
        if ($this->m_registrasi->delete_izin_rute($params)) {
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("izin_domestik/frekuensi_delete/list_rute/" . $registrasi_id);
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
        // check apakah semua rute dicabut?
        /*
          if ($total_new == $total_old) {
          $this->tnotification->set_error_message('Gunakan kelompok permohonan PENCABUTAN IZIN RUTE untuk mengurangi seluruh frekuensi penerbangan didalam rute ini!');
          }
         * 
         */
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect
            redirect("izin_domestik/frekuensi_delete/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_domestik/frekuensi_delete/list_rute/" . $this->input->post('registrasi_id'));
    }

    /*
     * STEP 4 : FILE ATTACHMENT
     */

    // files attachment
    public function list_files($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_domestik/frekuensi_delete/files.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
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
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
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
        redirect("izin_domestik/frekuensi_delete/list_files/" . $registrasi_id);
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_registrasi->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("izin_domestik/frekuensi_delete/list_files/" . $data_id);
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
            redirect('member/registration_domestik');
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
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
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
            redirect("izin_domestik/frekuensi_delete/review/" . $registrasi_id);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_domestik/frekuensi_delete/list_files/" . $registrasi_id);
    }

    /*
     * STEP 5 : REVIEW PERMOHONAN
     */

    // review
    public function review($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_domestik/frekuensi_delete/review.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
        }
        $this->smarty->assign("detail", $result);
        // rows color
        $row_style[$result['izin_rute_start']] = 'class="start-row"';
        $row_style[$result['izin_rute_end']] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        
        /*
         * RUTE PENERBANGAN EXISTING
         */
        $pairing_old = array();
        $frekuensi_old = array();
        $masa_berlaku = array();
        $data = array();
        $selected = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_aktif_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi_old[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing_old[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // masa berlaku
            $masa_berlaku[$izin_rute['izin_id']]['start_date'] = $izin_rute['izin_start_date'];
            $masa_berlaku[$izin_rute['izin_id']]['end_date'] = $izin_rute['izin_expired_date'];
        }
        $this->smarty->assign("rs_existing", $data);
        $this->smarty->assign("masa_berlaku", $masa_berlaku);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // data sebelumnya
        $total_existing = $this->m_registrasi->get_total_frekuensi_existing_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
                                
        /*
         * RUTE PENERBANGAN YANG DIHAPUS
         */
        $izin_st = array();
        $pairing = array();
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_waiting_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izin dat
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // status
            $izin_st[$izin_rute['izin_id']] = $izin_rute['izin_st'];
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("izin_st", $izin_st);
        $this->smarty->assign("rute_selected", $rute_selected);
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
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('member/registration_domestik');
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
            // process flow
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
            // send mail
            if ($result['izin_flight'] == 'domestik') {
                $this->m_email->mail_izin_to_all_aunbdn($result['registrasi_id'], $this->com_user['airlines_id']);
            } else {
                $this->m_email->mail_izin_to_all_aunbln($result['registrasi_id'], $this->com_user['airlines_id']);
            }
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Terima kasih telah melakukan pendaftaran Permohonan " . $result['group_nm'] . ' melalui sistem pelayanan berbasis online.');
            // default redirect
            redirect("member/registration_domestik");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_domestik/frekuensi_delete/review/" . $registrasi_id);
    }

    // ajax request rute by kode izin
    function get_rute_by_kode_izin() {
        $kode_izin = $this->input->post('kode_izin');
        $result = $this->m_registrasi->get_detail_izin_rute_by_kode_izin(array($this->com_user['airlines_id'], 'domestik', $kode_izin));
        if (!empty($result)) {
            $result['pax_cargo'] = strtoupper($result['pax_cargo']);
            $result['masa_berlaku'] = $this->datetimemanipulation->get_full_date($result['izin_start_date'], 'ins') . ' / ' . $this->datetimemanipulation->get_full_date($result['izin_expired_date'], 'ins');
            $result['masa_berlaku'] = strtoupper($result['masa_berlaku']);
        }
        echo json_encode($result);
    }

}
