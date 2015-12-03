<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class penghentian extends ApplicationBase {

    private $group_id = NULL;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('regulator/m_registrasi');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    /*
     * PENCABUTAN IZIN
     */

    // pencabutan
    public function index($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pencabutan/penghentian/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
        // list airlines
        $rs_id = $this->m_registrasi->get_list_airlines_have_rute();
        $this->smarty->assign("rs_id", $rs_id);
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
        $this->tnotification->set_rules('airlines_id', 'Operator penerbangan', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // delete rute
            $airlines_id_old = $this->input->post('airlines_id_old');
            $airlines_id = $this->input->post('airlines_id');
            if ($airlines_id_old <> $airlines_id) {
                // delete rute by registrasi
                $this->m_registrasi->delete_rute_by_registrasi(array($this->input->post('registrasi_id')));
            }
            // update registrasi
            $params = array(
                "airlines_id" => $this->input->post('airlines_id'),
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "input_by" => 'operator',
                "izin_request_st" => '0',
            );
            // insert
            if ($this->m_registrasi->update_registrasi($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
                // default redirect
                redirect("pencabutan/penghentian/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/penghentian/index/" . $this->input->post('registrasi_id'));
    }

    // list data rute
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/penghentian/list.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        if (empty($result)) {
            redirect('pencabutan/domestik');
        }
        $this->smarty->assign("detail", $result);
        $this->smarty->assign("result", $result);
        // list izin rute
        $rs_id = $this->m_registrasi->get_list_izin_rute_all_by_airlines(array($result['airlines_id'], $result['izin_flight']));
        $this->smarty->assign("rs_rute", $rs_id);
        // list rute
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_waiting_by_id(array($registrasi_id, $result['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
        }
        $this->smarty->assign("rs_id", $data);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
        $this->smarty->assign("frekuensi", $frekuensi);
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
        $this->tnotification->set_rules('registrasi_id', 'ID Registrasi', 'trim|required');
        $this->tnotification->set_rules('airlines_id', 'ID Airlines', 'trim|required');
        $this->tnotification->set_rules('izin_flight', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('izin_rute_start', 'Rute Pairing', 'trim|required');
        $this->tnotification->set_rules('izin_rute_end', 'Rute Pairing', 'trim|required');
        $this->tnotification->set_rules('input_pax_cargo', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('input_masa_berlaku', 'Masa Berlaku');
        // detail
        $detail = $this->m_registrasi->get_detail_izin_rute_by_rute_all(array($this->input->post('airlines_id'), $this->input->post('izin_flight'), $this->input->post('izin_rute_start')));
        // di cek pada pengajuan yang waiting
        $params = array(
            $this->input->post('airlines_id'),
            $detail['izin_rute_start'], $detail['izin_rute_start'],
            $detail['izin_rute_end'], $detail['izin_rute_end'],
            $this->input->post('registrasi_id'),
        );
        $total = $this->m_registrasi->get_total_rute_process_by_new_rute($params);
        if ($total > 0) {
            $this->tnotification->set_error_message('Pencabutan rute yang anda ajukan sedang dalam pengajuan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // delete rute dalam registrasi ini
            $where = array(
                'registrasi_id' => $this->input->post('registrasi_id'),
                'airlines_id' => $this->input->post('airlines_id'),
            );
            $this->m_registrasi->delete_izin_rute($where);
            // get list rute aktif by kode izin
            $rs_id = $this->m_registrasi->get_list_izin_rute_aktif_by_rute(array($detail['izin_rute_start'], $this->input->post('airlines_id')));
            $no_rute = 1;
            foreach ($rs_id as $izin_rute) {
                // insert into izin_rute
                $izin_id = $this->m_registrasi->get_data_id() . $no_rute;
                // params 
                $params = array(
                    'izin_id' => $izin_id,
                    'airlines_id' => $this->input->post('airlines_id'),
                    'kode_izin' => $izin_rute['kode_izin'],
                    'kode_frekuensi' => $izin_rute['kode_frekuensi'],
                    'registrasi_id' => $this->input->post('registrasi_id'),
                    'izin_completed' => '0',
                    'izin_approval' => 'waiting',
                    'izin_type' => 'berjadwal',
                    'izin_flight' => $detail['izin_flight'],
                    'izin_active' => '0',
                    'izin_st' => 'pencabutan',
                    'izin_start_date' => $izin_rute['izin_start_date'],
                    'izin_expired_date' => $izin_rute['izin_expired_date'],
                    'izin_penundaan_start' => $izin_rute['izin_penundaan_start'],
                    'izin_penundaan_end' => $izin_rute['izin_penundaan_end'],
                    'izin_rute_start' => $izin_rute['izin_rute_start'],
                    'izin_rute_end' => $izin_rute['izin_rute_end'],
                    'pairing' => $izin_rute['pairing'],
                    'mdb' => $this->com_user['user_id'],
                    'mdd' => date('Y-m-d H:i:s'),
                );
                // insert izin data
                if ($this->m_registrasi->insert_izin_rute($params)) {
                    $i = 1;
                    // insert izin data
                    $izin_data = $this->m_registrasi->get_list_izin_data_existing_by_kode_frekuensi(array($izin_rute['kode_frekuensi']));
                    foreach ($izin_data as $data) {
                        // id
                        $rute_id = $this->m_registrasi->get_data_id() . $i++;
                        // overwrite
                        $data['izin_id'] = $izin_id;
                        $data['rute_id'] = $rute_id;
                        unset($data['kode_izin']);
                        unset($data['kode_frekuensi']);
                        unset($data['frekuensi']);
                        // exec
                        $this->m_registrasi->insert_izin_data($data);
                    }
                }
            }
            // update registrasi
            $params = array(
                "izin_request_letter_date" => $this->input->post('izin_request_letter_date'),
                "izin_request_letter" => $this->input->post('izin_request_letter'),
                "izin_rute_start" => $detail['izin_rute_start'],
                "izin_rute_end" => $detail['izin_rute_end'],
                "pax_cargo" => $detail['pax_cargo'],
                "penanggungjawab" => $this->input->post('penanggungjawab'),
                "jabatan" => $this->input->post('jabatan'),
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "airlines_id" => $this->input->post('airlines_id'),
                "izin_request_st" => '0',
            );
            // insert
            if ($this->m_registrasi->update_izin_registrasi($params, $where)) {
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan!");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/penghentian/list_rute/" . $this->input->post('registrasi_id'));
    }

    // next process
    public function list_rute_next_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('airlines_id', 'ID Airliens', 'trim|required');
        // validasi total
        $params = array($this->input->post('registrasi_id'), $this->input->post('airlines_id'));
        $total = $this->m_registrasi->get_total_izin_data_by_registrasi($params);
        if (empty($total)) {
            $this->tnotification->set_error_message('Data rute penerbangan belum diinput!');
        }
        // check jika msh ada yang kosong
        $params = array($this->input->post('registrasi_id'), $this->input->post('airlines_id'));
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
            // redirect
            redirect("pencabutan/penghentian/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/penghentian/list_rute/" . $this->input->post('registrasi_id'));
    }

    /*
     * FILES
     */

    // list data files
    public function list_files($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/penghentian/files_list.html");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        $this->smarty->assign("detail", $result);
        // list files
        $rs_id = $this->m_registrasi->get_list_data_files_by_id(array($registrasi_id));
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add files
    public function files_add($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/penghentian/files_add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        $this->smarty->assign("detail", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add files process
    public function add_files_process() {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('letter_subject', 'Subyek Surat', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('letter_number', 'Nomor Surat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('letter_date', 'Tanggal Surat', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('letter_desc', 'Perihal', 'trim|required|maxlength[50]');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get id
        $letter_id = $this->m_registrasi->get_data_id();
        // upload foto
        if (!empty($_FILES['letter_file_name']['tmp_name'])) {
            // upload config
            $config['upload_path'] = 'resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id;
            $config['allowed_types'] = 'pdf|jpeg|jpg|docx|doc|xls|xlsx';
            $config['max_size'] = 1024;
            $this->tupload->initialize($config);
            // process upload
            if ($this->tupload->do_upload('letter_file_name')) {
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
            $params = array(
                'letter_id' => $letter_id,
                'registrasi_id' => $this->input->post('registrasi_id'),
                'letter_subject' => $this->input->post('letter_subject'),
                'letter_number' => $this->input->post('letter_number'),
                'letter_date' => $this->input->post('letter_date'),
                'letter_desc' => $this->input->post('letter_desc'),
                'letter_file_name' => $data['file_name'],
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            // update
            if ($this->m_registrasi->insert_files($params)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // remove dir
                $this->tupload->remove_dir('resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id);
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // remove dir
            $this->tupload->remove_dir('resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id);
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/penghentian/files_add/" . $this->input->post('registrasi_id'));
    }

    // edit files
    public function files_edit($registrasi_id = "", $letter_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/domestik/penghentian/files_edit.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        $this->smarty->assign("detail", $result);
        // detail files
        $result = $this->m_registrasi->get_detail_files_by_id(array($registrasi_id, $letter_id));
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit files process
    public function edit_files_process() {
        // set page rules
        $this->_set_page_rule("U");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('letter_id', 'ID Files', 'trim|required');
        $this->tnotification->set_rules('letter_subject', 'Subyek Surat', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('letter_number', 'Nomor Surat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('letter_date', 'Tanggal Surat', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('letter_desc', 'Perihal', 'trim|required|maxlength[50]');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get id
        $letter_id = $this->input->post('letter_id');
        // detail slot
        $result = $this->m_registrasi->get_detail_files_by_id(array($registrasi_id, $letter_id));
        if (empty($result)) {
            redirect('pencabutan/penghentian/list_files/' . $registrasi_id);
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                'letter_subject' => $this->input->post('letter_subject'),
                'letter_number' => $this->input->post('letter_number'),
                'letter_date' => $this->input->post('letter_date'),
                'letter_desc' => $this->input->post('letter_desc'),
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            $where = array(
                "registrasi_id" => $registrasi_id,
                "letter_id" => $letter_id,
            );
            // update
            if ($this->m_registrasi->update_files($params, $where)) {
                // upload files
                if (!empty($_FILES['letter_file_name']['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id;
                    $config['allowed_types'] = 'pdf|jpeg|jpg|docx|doc|xls|xlsx';
                    $config['max_size'] = 1024;
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload('letter_file_name')) {
                        $data = $this->tupload->data();
                        // --
                        $params = array(
                            'letter_file_name' => $data['file_name'],
                        );
                        $where = array(
                            "registrasi_id" => $registrasi_id,
                            "letter_id" => $letter_id,
                        );
                        $this->m_registrasi->update_files($params, $where);
                        // remove file
                        $this->tupload->remove_file('resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id . '/' . $result['letter_file_name']);
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
        redirect("pencabutan/penghentian/files_edit/" . $registrasi_id . '/' . $letter_id);
    }

    // download
    public function files_download($registrasi_id = "", $letter_id = "") {
        // get detail data
        $params = array($registrasi_id, $letter_id);
        $result = $this->m_registrasi->get_detail_files_by_id($params);
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

    // delete slot
    public function files_delete($registrasi_id = "", $letter_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // load
        $this->load->library('tupload');
        // detail slot
        $result = $this->m_registrasi->get_detail_files_by_id(array($registrasi_id, $letter_id));
        if (empty($result)) {
            redirect('pencabutan/penghentian/list_files/' . $registrasi_id);
        }
        // delete
        $params = array($letter_id, $registrasi_id);
        // execute
        if ($this->m_registrasi->delete_files($params)) {
            // remove file
            $this->tupload->remove_dir('resource/doc/pencabutan/' . $registrasi_id . '/' . $letter_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pencabutan/penghentian/list_files/" . $registrasi_id);
    }

    // list files process
    public function list_files_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        // validasi
        $rs_id = $this->m_registrasi->get_list_data_files_by_id(array($this->input->post('registrasi_id')));
        if (empty($rs_id)) {
            // default error
            $this->tnotification->sent_notification("error", "Files belum diinputkan!");
            // redirect
            redirect("pencabutan/penghentian/list_files/" . $this->input->post('registrasi_id'));
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/penghentian/review/" . $this->input->post('registrasi_id'));
    }

    // review
    public function review($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/penghentian/review.html");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        $this->smarty->assign("detail", $result);
        // list rute
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_waiting_by_id(array($registrasi_id, $result['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
        }
        $this->smarty->assign("rs_id", $data);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
        $this->smarty->assign("frekuensi", $frekuensi);
        // list files
        $rs_id = $this->m_registrasi->get_list_data_files_by_id(array($registrasi_id));
        $this->smarty->assign("rs_files", $rs_id);
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
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        // files
        $rs_files = $this->m_registrasi->get_list_data_files_by_id(array($registrasi_id));
        if (empty($rs_files)) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // list rute
        $rs_id = $this->m_registrasi->get_list_izin_rute_waiting_by_id(array($registrasi_id, $result['airlines_id']));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Data rute belum diinput!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $flow_id = ($result['izin_flight'] == 'domestik') ? 2 : 12;
            // process flow
            $process_id = $this->m_registrasi->get_data_id();
            $params = array(
                'process_id' => $process_id,
                'registrasi_id' => $registrasi_id,
                'flow_id' => $flow_id,
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
                'airlines_id' => $result['airlines_id'],
            );
            $this->m_registrasi->update_izin_registrasi($params, $where);
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Terima kasih telah melakukan pendaftaran Permohonan " . $result['group_nm'] . ' melalui sistem pelayanan berbasis online.');
            // default redirect
            redirect("pencabutan/" . $result['izin_flight']);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/penghentian/review/" . $registrasi_id);
    }

    /*
     * AJAX
     */

    // ajax request rute by all
    function get_rute_all_by_rute() {
        // --
        $airlines_id = $this->input->post('airlines_id');
        $izin_rute_start = $this->input->post('izin_rute_start');
        $izin_flight = $this->input->post('izin_flight');
        // --
        $result = $this->m_registrasi->get_detail_izin_rute_by_rute_all(array($airlines_id, $izin_flight, $izin_rute_start));
        if (!empty($result)) {
            $result['pax_cargo'] = strtoupper($result['pax_cargo']);
            $result['masa_berlaku'] = $this->datetimemanipulation->get_full_date($result['izin_start_date'], 'ins') . ' / ' . $this->datetimemanipulation->get_full_date($result['izin_expired_date'], 'ins');
            $result['masa_berlaku'] = strtoupper($result['masa_berlaku']);
        }
        echo json_encode($result);
    }

}
