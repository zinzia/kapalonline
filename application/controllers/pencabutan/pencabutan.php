<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class pencabutan extends ApplicationBase {

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

    /*
     * INDEX
     */

    // pencabutan
    public function index($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pencabutan/pencabutan/index.html");
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
                redirect("pencabutan/pencabutan/pilih_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pencabutan/index/" . $this->input->post('registrasi_id'));
    }

    /*
     * PILIH RUTE
     */

    // pilih data rute
    public function pilih_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/pencabutan/rute.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        // kode izin
        $kode_izin = $result['kode_izin'];
        $last_izin = $this->m_registrasi->get_detail_izin_rute_by_kode_izin(array($result['airlines_id'], 'domestik', $kode_izin));
        $result['input_pax_cargo'] = $last_izin['pax_cargo'];
        $result['input_masa_berlaku'] = strtoupper($this->datetimemanipulation->get_full_date($last_izin['izin_start_date'], 'ins')) . ' / ';
        $result['input_masa_berlaku'] .= strtoupper($this->datetimemanipulation->get_full_date($last_izin['izin_expired_date'], 'ins'));
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
        // list izin rute
        $rs_id = $this->m_registrasi->get_list_izin_rute_by_airlines(array($result['airlines_id'], 'domestik'));
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // pilih process
    public function pilih_rute_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Registrasi', 'trim|required');
        $this->tnotification->set_rules('airlines_id', 'ID Operator', 'trim|required');
        $this->tnotification->set_rules('kode_izin', 'Rute Pairing', 'trim|required');
        $this->tnotification->set_rules('input_pax_cargo', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('input_masa_berlaku', 'Masa Berlaku');
        $this->tnotification->set_rules('izin_season', 'Season Code', 'trim|required|maxlength[3]');
        $this->tnotification->set_rules('kode_izin', 'Kode Rute', 'trim|required');
        // airlines id
        $kode_izin = $this->input->post('kode_izin');
        $airlines_id = $this->input->post('airlines_id');
        // --
        $detail = $this->m_registrasi->get_detail_izin_rute_by_kode_izin(array($airlines_id, 'domestik', $kode_izin));
        // di cek pada pengajuan yang waiting
        $params = array(
            $airlines_id,
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
                "izin_rute_start" => $detail['izin_rute_start'],
                "izin_rute_end" => $detail['izin_rute_end'],
                "pax_cargo" => $detail['pax_cargo'],
                "izin_season" => $this->input->post('izin_season'),
                "kode_izin" => $kode_izin,
            );
            // where
            $where = array(
                "registrasi_id" => $this->input->post('registrasi_id'),
                "airlines_id" => $airlines_id,
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
                redirect("pencabutan/pencabutan/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pencabutan/pilih_rute/" . $this->input->post('registrasi_id'));
    }

    /*
     * LIST
     */

    // list data rute
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/pencabutan/list.html");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("detail", $result);
        // data sebelumnya
        $total_existing = $this->m_registrasi->get_total_frekuensi_existing_by_kode_izin(array($result['kode_izin'], $result['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_aktif_by_kode_izin(array($result['kode_izin'], $result['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_registrasi->get_list_izin_data_waiting_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                $data[$no++] = $izin_data;
            } else {
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
        }
        $this->smarty->assign("rs_existing", $data);
        // get sub total frekuensi
        $subtotal_existing = array();
        $rs_subtotal_existing = $this->m_registrasi->get_total_frekuensi_existing_by_kode_izin_v2(array($result['kode_izin'], $result['airlines_id']));
        foreach ($rs_subtotal_existing as $rs_sub_existing) {
            $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['frekuensi'] = $rs_sub_existing['frekuensi'];
            $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['start_date'] = $rs_sub_existing['start_date'];
            $subtotal_existing[$rs_sub_existing['kode_frekuensi']]['end_date'] = $rs_sub_existing['end_date'];
        }
        $this->smarty->assign("subtotal_existing", $subtotal_existing);
        // list rute
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
        }
        $this->smarty->assign("rs_id", $data);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // get sub total frekuensi
        $subcek = array();
        $subtotal = array();
        $rs_subtotal = $this->m_registrasi->get_total_frekuensi_by_kode_frekuensi($registrasi_id);
        foreach ($rs_subtotal as $rs_sub) {
            $subtotal[$rs_sub['kode_frekuensi']]['frekuensi'] = $rs_sub['frekuensi'];
            $subtotal[$rs_sub['kode_frekuensi']]['start_date'] = $rs_sub['start_date'];
            $subtotal[$rs_sub['kode_frekuensi']]['end_date'] = $rs_sub['end_date'];
            array_push($subcek, $rs_sub['kode_frekuensi']);
        }
        $this->smarty->assign("subtotal", $subtotal);
        // get total frekuensi yang tidak diubah
        $frek_not_change = 0;
        $rs_ex = $rs_subtotal_existing;
        foreach ($rs_ex as $rs_exs) {
            if (!in_array($rs_exs['kode_frekuensi'], $subcek)) {
                $frek_not_change += $rs_exs['frekuensi'];
            }
        }
        $this->smarty->assign("frek_not_change", $frek_not_change);
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
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        // get rute existing by $kode_frekuensi
        $izin_rute = $this->m_registrasi->get_detail_izin_rute_aktif_by_kode_frekuensi(array($kode_frekuensi, $result['airlines_id']));
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
            $params['mdb'] = $result['user_id'];
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
                redirect('pencabutan/pencabutan/list_rute/' . $registrasi_id);
            }
        }
        // default notification
        $this->tnotification->sent_notification("error", "An unexpected error has occurred");
        redirect('pencabutan/pencabutan/list_rute/' . $registrasi_id);
    }

    // delete process
    public function rute_delete($registrasi_id = "", $izin_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // get detail data
        $params = array($registrasi_id);
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        // delete
        $params = array(
            'registrasi_id' => $registrasi_id,
            'izin_id' => $izin_id,
            'airlines_id' => $result['airlines_id'],
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
        redirect("pencabutan/pencabutan/list_rute/" . $registrasi_id);
    }

    // add process
    public function list_rute_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        // get detail data
        $params = array($this->input->post('registrasi_id'));
        $result = $this->m_registrasi->get_registrasi_waiting_by_id($params);
        // validasi total
        $params = array($this->input->post('registrasi_id'), $result['airlines_id']);
        $total = $this->m_registrasi->get_total_izin_data_by_registrasi($params);
        if (empty($total)) {
            $this->tnotification->set_error_message('Data rute penerbangan belum diinput!');
        }
        // check jika msh ada yang kosong
        $params = array($this->input->post('registrasi_id'), $result['airlines_id']);
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
            redirect("pencabutan/pencabutan/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pencabutan/pencabutan/list_rute/" . $this->input->post('registrasi_id'));
    }

    /*
     * FILES
     */

    // list data files
    public function list_files($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/pencabutan/files_list.html");
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
        $this->smarty->assign("template_content", "pencabutan/pencabutan/files_add.html");
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
                'mdb' => $result['user_id'],
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
        redirect("pencabutan/pencabutan/files_add/" . $this->input->post('registrasi_id'));
    }

    // edit files
    public function files_edit($registrasi_id = "", $letter_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/pencabutan/files_edit.html");
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
            redirect('pencabutan/pencabutan/list_files/' . $registrasi_id);
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                'letter_subject' => $this->input->post('letter_subject'),
                'letter_number' => $this->input->post('letter_number'),
                'letter_date' => $this->input->post('letter_date'),
                'letter_desc' => $this->input->post('letter_desc'),
                'mdb' => $result['user_id'],
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
        redirect("pencabutan/pencabutan/files_edit/" . $registrasi_id . '/' . $letter_id);
    }

    // download
    public function files_download($registrasi_id = "", $letter_id = "") {
        // get detail data
        $params = array($registrasi_id, $letter_id);
        $result = $this->m_registrasi->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("pencabutan/pencabutan/list_files/" . $registrasi_id);
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
            redirect("pencabutan/pencabutan/list_files/" . $registrasi_id);
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
            redirect('pencabutan/pencabutan/list_files/' . $registrasi_id);
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
        redirect("pencabutan/pencabutan/list_files/" . $registrasi_id);
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
            redirect("pencabutan/pencabutan/list_files/" . $this->input->post('registrasi_id'));
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
        redirect("pencabutan/pencabutan/review/" . $this->input->post('registrasi_id'));
    }

    // review
    public function review($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "pencabutan/pencabutan/review.html");
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
                'mdb' => $result['user_id'],
                'mdd' => date('Y-m-d H:i:s'),
            );
            $this->m_registrasi->insert_izin_process($params);
            // update status
            $params = array(
                'izin_request_st' => '1',
                'izin_request_by' => $result['user_id'],
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
        redirect("pencabutan/pencabutan/review/" . $registrasi_id);
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

    // ajax request rute by kode izin
    function get_rute_by_kode_izin() {
        // params
        $kode_izin = $this->input->post('kode_izin');
        $airlines_id = $this->input->post('airlines_id');
        // ---
        $result = $this->m_registrasi->get_detail_izin_rute_by_kode_izin(array($airlines_id, 'domestik', $kode_izin));
        if (!empty($result)) {
            $result['pax_cargo'] = strtoupper($result['pax_cargo']);
            $result['masa_berlaku'] = $this->datetimemanipulation->get_full_date($result['izin_start_date'], 'ins') . ' / ' . $this->datetimemanipulation->get_full_date($result['izin_expired_date'], 'ins');
            $result['masa_berlaku'] = strtoupper($result['masa_berlaku']);
        }
        echo json_encode($result);
    }

}
