<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class pengurangan extends ApplicationBase {

    private $group_id = 6;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_pencabutan');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // create pengurangan
    public function create_pengurangan() {
        // set page rules
        $this->_set_page_rule("C");
        // create izin_id, airlines_id, izin_flight, izin_group, mdb, mdd
        $params = array(
            "izin_flight" => 'domestik',
            'izin_group' => 6,
            'input_by' => 'operator',
            "mdb" => $this->com_user['user_id']
        );
        $id = $this->m_pencabutan->create_group_data($params);
        if (!empty($id)) {
            // notification
            $this->tnotification->sent_notification("success", "Data has been created!");
            redirect('pencabutan/pengurangan/index/' . $id);
        } else {
            // notification
            $this->tnotification->sent_notification("error", "An unexpected error has occurred");
            redirect('pencabutan/pengurangan');
        }
    }

    /*
     * PENCABUTAN IZIN
     */

    // pencabutan
    public function index($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pencabutan/domestik/pengurangan/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik/');
        }
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
        // list izin rute
        $rs_id = $this->m_pencabutan->get_list_izin_rute(array('domestik'));
        $this->smarty->assign("rs_id", $rs_id);
        // get kode izin
        $kode_izin = $this->m_pencabutan->get_kode_izin_by_registrasi($registrasi_id);
        $this->smarty->assign("kode_izin", $kode_izin);
        // editorial
        $rs_editorial = $this->m_pencabutan->get_list_editorial(array($registrasi_id));
        $this->smarty->assign("rs_editorial", $rs_editorial);
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
        $this->tnotification->set_rules('kode_izin', 'Kode Rute', 'trim|required');
        // validate rute on process
        $params = array($this->input->post('kode_izin'), $this->input->post('registrasi_id'));
        $total = $this->m_pencabutan->get_total_rute_process_by_kode_izin($params);
        if (!empty($total)) {
            $this->tnotification->set_error_message('Permohonan Rute Penerbangan sedang dalam proses permohonan, mohon dilakukan penolakan terhadap permohonan tersebut!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // get detail data
            $params = array($this->input->post('registrasi_id'), $this->group_id);
            $result = $this->m_pencabutan->get_registrasi_by_id($params);
            // jika sama
            if ($result['kode_izin'] != $this->input->post('kode_izin')) {
                // delete izin rute
                $this->m_pencabutan->delete_rute_by_registrasi($this->input->post('registrasi_id'));
            }
            // get kode izin
            $izin = $this->m_pencabutan->get_rute_by_kode_izin($this->input->post('kode_izin'));
            $this->smarty->assign("izin", $izin);
            // update registrasi
            $params = array(
                "airlines_id" => $izin['airlines_id'],
                "izin_rute_start" => $izin['izin_rute_start'],
                "izin_rute_end" => $izin['izin_rute_end'],
                "kode_izin" => $this->input->post('kode_izin'),
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "izin_request_st" => '0',
            );
            // insert
            if ($this->m_pencabutan->update_izin_permohonan($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("pencabutan/pengurangan/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pengurangan/index/" . $this->input->post('registrasi_id'));
    }

    // list data rute
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/domestik/pengurangan/list.html");
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        // assign
        $this->smarty->assign("detail", $result);
        // data sebelumnya
        $rs_id = $this->m_pencabutan->get_list_data_rute_by_kode_izin(array($result['kode_izin']));
        $this->smarty->assign("rs_id", $rs_id);
        // data terpilih
        $izin_selected = array();
        $rs_selected = $this->m_pencabutan->get_list_data_rute_by_id(array($registrasi_id));
        foreach ($rs_selected as $selected) {
            $izin_selected[str_replace('-', '_', $selected['kode_frekuensi'])] = $selected['kode_frekuensi'];
        }
        $this->smarty->assign("izin_selected", $izin_selected);
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
        // validasi
        $rs_id = $this->m_pencabutan->get_list_data_rute_by_id(array($this->input->post('registrasi_id'), $this->com_user['airlines_id']));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Belum ada data rute yang diinputkan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect
            redirect("pencabutan/pengurangan/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pengurangan/list_rute/" . $this->input->post('registrasi_id'));
    }

    // add rute
    public function rute_edit($registrasi_id = "", $kode_frekuensi = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/domestik/pengurangan/edit.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/pengurangan');
        }
        $this->smarty->assign("detail", $result);
        /*
         * LAMA
         */
        // detail lama
        $result_lama = $this->m_pencabutan->get_izin_rute_by_kode_frekuensi_active(array($kode_frekuensi));
        if (empty($result_lama)) {
            redirect('pencabutan/pengurangan/list_rute/' . $registrasi_id);
        }
        $this->smarty->assign("izin_start_date", $result_lama['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $result_lama['izin_expired_date']);
        // rute lama
        $rs_id_lama = $this->m_pencabutan->get_list_data_rute_by_kode_frekuensi(array($kode_frekuensi));
        $this->smarty->assign("rs_id_lama", $rs_id_lama);
        /*
         * PERUBAHAN
         */
        // detail rute by frekuensi yang akan diubah
        $result = $this->m_pencabutan->get_izin_rute_by_kode_frekuensi(array($kode_frekuensi, 'perubahan'));
        if (empty($result)) {
            $result = $result_lama;
        }
        // dos
        $dos = array();
        for ($i = 1; $i <= 7; $i++) {
            $dos[$i] = substr($result['dos'], $i - 1, 1);
        }
        $result['dos'] = $dos;
        // assign
        $this->smarty->assign("dos", $result['dos']);
        // detail rute
        $lama = 0;
        $rs_id = $this->m_pencabutan->get_izin_rute_data_by_id(array($result['izin_id']));
        if (empty($rs_id)) {
            $rs_id = $rs_id_lama;
            $lama = 1;
        }
        if ($result['pairing'] == 'VV') {
            $no = 11;
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
            if ($result['pairing'] == 'VV') {
                $no++;
            }
        }
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
    }

    // edit process
    public function edit_rute_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('kode_izin', 'Kode Izin', 'trim|required');
        $this->tnotification->set_rules('kode_frekuensi', 'Kode Frekuensi', 'trim|required');
        $this->tnotification->set_rules('airlines_id', 'ID Airlines', 'trim|required');
        $this->tnotification->set_rules('aircraft_type', 'Tipe Pesawat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('aircraft_capacity', 'Kapasitas Pesawat', 'trim|required|maxlength[3]');
        $this->tnotification->set_rules('pairing', 'Pairing', 'trim|required');
        $this->tnotification->set_rules('dos[]', 'DOS', 'trim|required');
        $this->tnotification->set_rules('ron', 'RON', 'trim|required|maxlength[1]|is_natural');
        $this->tnotification->set_rules('izin_rute_start', 'Rute Pergi');
        $this->tnotification->set_rules('izin_rute_end', 'Rute Pulang');
        // pairing
        $registrasi_id = $this->input->post('registrasi_id');
        $kode_frekuensi = $this->input->post('kode_frekuensi');
        $pairing = $this->input->post('pairing');
        $dos = $this->input->post('dos');
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
        // process
        if ($this->tnotification->run() !== FALSE) {
            // dos
            $dos_input = '';
            for ($i = 1; $i <= 7; $i++) {
                $dos_input .= empty($dos[$i]) ? 0 : $dos[$i];
            }
            // delete izin rute by frekuensi and registrasi id
            $params = array($registrasi_id, $kode_frekuensi);
            $this->m_pencabutan->delete_rute_by_registrasi_kode_frekuensi($params);
            // insert izin rute baru
            $izin_id = $this->m_pencabutan->get_data_id();
            $params = array(
                'izin_id' => $izin_id,
                'airlines_id' => $this->input->post('airlines_id'),
                'registrasi_id' => $this->input->post('registrasi_id'),
                'izin_approval' => 'approved',
                'izin_flight' => 'domestik',
                'izin_st' => 'perubahan',
                'aircraft_type' => $this->input->post('aircraft_type'),
                'kode_izin' => $this->input->post('kode_izin'),
                'kode_frekuensi' => $this->input->post('kode_frekuensi'),
                'aircraft_capacity' => $this->input->post('aircraft_capacity'),
                'izin_start_date' => $this->input->post('izin_start_date'),
                'izin_expired_date' => $this->input->post('izin_expired_date'),
                'izin_rute_start' => $this->input->post('izin_rute_start'),
                'izin_rute_end' => $this->input->post('izin_rute_end'),
                'dos' => $dos_input,
                'ron' => $this->input->post('ron'),
                'pairing' => $this->input->post('pairing'),
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            // update
            if ($this->m_pencabutan->insert_rute($params)) {
                // delete
                $this->m_pencabutan->delete_rute_data(array($izin_id, $this->com_user['airlines_id']));
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
                    );
                    $this->m_pencabutan->insert_rute_data($params);
                    // pulang
                    $params = array(
                        'rute_id' => $izin_id . '2',
                        'izin_id' => $izin_id,
                        'rute_all' => $this->input->post('rute_all12'),
                        'flight_no' => $this->input->post('flight_no12'),
                        'etd' => $this->input->post('etd12'),
                        'eta' => $this->input->post('eta12'),
                    );
                    $this->m_pencabutan->insert_rute_data($params);
                } else {
                    // pergi
                    $params = array(
                        'rute_id' => $izin_id . '1',
                        'izin_id' => $izin_id,
                        'rute_all' => $this->input->post('rute_all'),
                        'flight_no' => $this->input->post('flight_no'),
                        'etd' => $this->input->post('etd'),
                        'eta' => $this->input->post('eta'),
                    );
                    $this->m_pencabutan->insert_rute_data($params);
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
        redirect("pencabutan/pengurangan/rute_edit/" . $this->input->post('registrasi_id') . '/' . $kode_frekuensi);
    }

    // delete process
    public function rute_delete($registrasi_id = "", $kode_frekuensi = "") {
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array($registrasi_id, $kode_frekuensi);
        // execute
        if ($this->m_pencabutan->delete_rute_by_registrasi_kode_frekuensi($params)) {
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pencabutan/pengurangan/list_rute/" . $registrasi_id);
    }

    // files attachment
    public function list_files($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pencabutan/domestik/pengurangan/files.html");
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        // assign
        $this->smarty->assign("detail", $result);
        // list persyaratan
        $rs_files = $this->m_pencabutan->get_list_file_required_domestik(array($result['izin_group'], $result['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_pencabutan->get_list_file_uploaded(array($registrasi_id));
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
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // upload 1 per 1
            $rs_files = $this->m_pencabutan->get_list_file_required_domestik(array($result['izin_group'], $result['izin_flight']));
            foreach ($rs_files as $files) {
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
                        $file_id = $this->m_pencabutan->get_file_id();
                        $filepath = 'resource/doc/izin/' . $registrasi_id . '/' . $files['ref_id'] . '/' . $data['file_name'];
                        $this->m_pencabutan->update_files(array($registrasi_id, $files['ref_id']), array($file_id, $registrasi_id, $filepath, $data['file_name'], $files['ref_id'], '1', $this->com_user['user_id']));
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
        redirect("pencabutan/pengurangan/list_files/" . $registrasi_id);
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_pencabutan->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("pencabutan/pengurangan/list_files/" . $data_id);
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
            redirect('pencabutan/domestik');
        }
    }

    // file process
    public function files_next() {
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID', 'trim|required');
        // data id
        $registrasi_id = $this->input->post('registrasi_id');
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        // validation
        if (!$this->m_pencabutan->is_file_completed(array($registrasi_id, $result['izin_group'], $result['izin_flight']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("pencabutan/pengurangan/review/" . $registrasi_id);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pengurangan/list_files/" . $registrasi_id);
    }

    // review
    public function review($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/domestik/pengurangan/review.html");
        // get detail data
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        // assign
        $this->smarty->assign("detail", $result);
        // get kode izin
        $izin = $this->m_pencabutan->get_rute_by_kode_izin($result['kode_izin']);
        $this->smarty->assign("izin", $izin);
        // list data
        $rs_id = $this->m_pencabutan->get_list_data_rute_by_id(array($registrasi_id));
        $this->smarty->assign("rs_id", $rs_id);
        // list persyaratan
        $rs_files = $this->m_pencabutan->get_list_file_required_domestik(array($result['izin_group'], $result['izin_flight']));
        $this->smarty->assign("rs_files", $rs_files);
        // get uploaded files
        $file_uploaded = array();
        $rs_uploaded = $this->m_pencabutan->get_list_file_uploaded(array($registrasi_id));
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
        $params = array($registrasi_id, $this->group_id);
        $result = $this->m_pencabutan->get_registrasi_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        // validation
        if (!$this->m_pencabutan->is_file_completed(array($registrasi_id, $result['izin_group'], $result['izin_flight']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // list rute
        $rs_id = $this->m_pencabutan->get_list_data_rute_by_id(array($registrasi_id));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Data rute belum diinput!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // process flow
            $process_id = $this->m_pencabutan->get_data_id();
            $params = array($process_id, $registrasi_id, 2, $this->com_user['user_id']);
            $this->m_pencabutan->insert_process($params);
            // update status
            $this->m_pencabutan->update_status_data(array('1', $this->com_user['user_id'], $registrasi_id));
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("pencabutan/domestik");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pengurangan/review/" . $registrasi_id);
    }

}
