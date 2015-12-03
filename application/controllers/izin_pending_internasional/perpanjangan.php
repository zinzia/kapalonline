<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class perpanjangan extends ApplicationBase {

    private $group_id = 22;
    private $flow_id = 16;
    private $next_id = 11;

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
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/index.html");
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

    // Update Process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Pendaftaran', 'trim|required');
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
        }
        // khusus pembatalan
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
                redirect("izin_pending_internasional/perpanjangan/index/" . $this->input->post('registrasi_id'));
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
                redirect("izin_pending_internasional/perpanjangan/list_rute/" . $this->input->post('registrasi_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/index/" . $this->input->post('registrasi_id'));
    }

    /*
     * STEP 2 : RUTE PENERBANGAN
     */

    // list data rute
    public function list_rute($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/list.html");
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
        // catatan perbaikan
        $this->smarty->assign("catatan", $this->m_registrasi->get_catatan_perbaikan_by_registrasi($registrasi_id));
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
            $izin_data = $this->m_registrasi->get_list_izin_data_pending_by_id(array($izin_rute['izin_id']));
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
            // check is already perpanjangan
            $params = array($izin_rute['kode_frekuensi'], $this->com_user['airlines_id']);
            $selected[$izin_rute['kode_frekuensi']] = $this->m_registrasi->check_izin_rute_by_perpanjangan($params);
        }
        $this->smarty->assign("rs_existing", $data);
        $this->smarty->assign("selected_perpanjangan", $selected);
        $this->smarty->assign("masa_berlaku", $masa_berlaku);
        $this->smarty->assign("frekuensi_old", $frekuensi_old);
        $this->smarty->assign("pairing_old", $pairing_old);
        // total frekuensi sebelumnya
        $total_existing = $this->m_registrasi->get_total_frekuensi_existing_by_kode_izin(array($result['kode_izin'], $this->com_user['airlines_id']));
        $this->smarty->assign("total_existing", $total_existing);
        /*
         * RUTE PENERBANGAN YANG DIPERPANJANG
         */
        $pairing = array();
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_pending_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izin dat
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
        $this->smarty->assign("rute_selected", $rute_selected);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // step 2 : process
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
            // jika 2 : IASM semua
            // check bandara
            $is_used_score = 0;
            $airport = explode('-', $result['izin_rute_start']);
            foreach ($airport as $iata_code) {
                $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
                $is_used_score += $data['is_used_score'];
            }
            // redirect to file attachment
            if ($is_used_score <> 2) {
                redirect("izin_pending_internasional/perpanjangan/list_slot/" . $this->input->post('registrasi_id'));
            } else {
                // redirect
                redirect("izin_pending_internasional/perpanjangan/list_files/" . $this->input->post('registrasi_id'));
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/list_rute/" . $this->input->post('registrasi_id'));
    }

    // create rute id
    public function rute_add_new_process($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");        
        // check registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        // check jika msh ada yang kosong
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $total = $this->m_registrasi->get_izin_rute_empty_data_by_id($params);
        if (empty($total)) {
            // notification
            $this->tnotification->sent_notification("error", "Terdapat data rute yang belum dilengkapi!");
            // redirect
            redirect('izin_pending_internasional/perpanjangan/list_rute/' . $registrasi_id);
        }
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // add izin rute
        $izin_id = $this->m_registrasi->get_data_id();
        $params = array(
            "izin_id" => $izin_id,
            "airlines_id" => $this->com_user['airlines_id'],
            "registrasi_id" => $registrasi_id,
            "izin_completed" => '0',
            "izin_st" => 'baru',
            'izin_approval' => 'waiting',
            'izin_type' => 'berjadwal',
            'izin_flight' => 'domestik',
            'izin_active' => '0',
            'izin_rute_start' => $result['izin_rute_start'],
            'izin_rute_end' => $result['izin_rute_end'],
            'pairing' => NULL,
            'is_used_score' => $is_used_score,
            'mdb' => $this->com_user['user_id'],
            'mdd' => date('Y-m-d H:i:s'),
        );
        if ($this->m_registrasi->insert_izin_rute($params)) {
            // notification
            $this->tnotification->sent_notification("success", "Data has been created!");
            redirect('izin_pending_internasional/perpanjangan/rute_data/' . $registrasi_id . '/' . $izin_id);
        } else {
            // notification
            $this->tnotification->sent_notification("error", "An unexpected error has occurred");
            redirect('izin_pending_internasional/perpanjangan/list_rute/' . $registrasi_id);
        }
    }
    
    // create rute id
    public function rute_add_process($registrasi_id = "", $kode_frekuensi = "") {
        // set page rules
        $this->_set_page_rule("C");
        // check registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        // check jika msh ada yang kosong
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $total = $this->m_registrasi->get_izin_rute_empty_data_by_id($params);
        if (empty($total)) {
            // notification
            $this->tnotification->sent_notification("error", "Terdapat data rute yang belum dilengkapi!");
            // redirect
            redirect('izin_pending_internasional/perpanjangan/list_rute/' . $registrasi_id);
        }
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // add izin rute
        $izin_id = $this->m_registrasi->get_data_id();
        $kode_izin = explode('-', $kode_frekuensi, 3);
        $params = array(
            "izin_id" => $izin_id,
            "airlines_id" => $this->com_user['airlines_id'],
            "registrasi_id" => $registrasi_id,
            "izin_completed" => '0',
            "izin_st" => 'perpanjangan',
            'izin_approval' => 'waiting',
            'izin_type' => 'berjadwal',
            'izin_flight' => 'internasional',
            'izin_active' => '0',
            'izin_rute_start' => $result['izin_rute_start'],
            'izin_rute_end' => $result['izin_rute_end'],
            'pairing' => NULL,
            'is_used_score' => $is_used_score,
            'kode_izin' => $kode_izin[0] . '-' . $kode_izin[1],
            'kode_frekuensi' => $kode_frekuensi,
            'mdb' => $this->com_user['user_id'],
            'mdd' => date('Y-m-d H:i:s'),
        );
        if ($this->m_registrasi->insert_izin_rute($params)) {
            // notification
            $this->tnotification->sent_notification("success", "Data has been created!");
            redirect('izin_pending_internasional/perpanjangan/rute_data/' . $registrasi_id . '/' . $izin_id);
        } else {
            // notification
            $this->tnotification->sent_notification("error", "An unexpected error has occurred");
            redirect('izin_pending_internasional/perpanjangan/list_rute/' . $registrasi_id);
        }
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
        redirect("izin_pending_internasional/perpanjangan/list_rute/" . $registrasi_id);
    }

    // STEP 2 : IZIN DATA
    // list izin data
    public function rute_data($registrasi_id = "", $izin_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/list_izin_data.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        // get detail data registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        $this->smarty->assign("detail", $result);
        // default season
        $this->season_cd = $result['izin_season'];
        $this->smarty->assign("season_cd", $this->season_cd);
        // services type
        $this->service_type = ($result['pax_cargo'] == 'cargo') ? 'F' : 'J';
        $this->smarty->assign("service_type", $this->service_type);
        // get detail data registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $izin_id);
        $rute = $this->m_registrasi->get_izin_rute_pending_by_id($params);
        if (empty($rute)) {
            redirect('izin_pending_internasional/perpanjangan/list_rute/' . $registrasi_id);
        }
        $this->smarty->assign("rute", $rute);
        // jika salah satu atau keduanya IASM
        if ($rute['is_used_score'] <> 0) {
            $this->iasm_view($rute);
        }
        // list izin data existing
        $rs_id = $this->m_registrasi->get_list_izin_data_existing_by_kode_frekuensi(array($rute['kode_frekuensi']));
        $this->smarty->assign("rs_existing", $rs_id);
        // frekuensi existing
        $this->smarty->assign("total_existing", $this->m_registrasi->get_total_frekuensi_existing_by_kode_frekuensi($rute['kode_frekuensi']));
        // list izin data sekarang
        $rs_id = $this->m_registrasi->get_list_izin_data_pending_by_id(array($izin_id));
        $this->smarty->assign("rs_id", $rs_id);
        // frekuensi sekarang
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_izin_id($izin_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // untuk dos selected
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

    // private
    private function iasm_view($rute) {
        // load library
        $this->load->library('score_services');
        // get search parameter
        $search = $this->tsession->userdata('search_slot_rute');
        // search parameters
        $rute_all = empty($search['rute_all']) ? $rute['izin_rute_start'] : trim($search['rute_all']);
        $flight_no = empty($search['flight_no']) ? '' : trim($search['flight_no']);
        // --
        $this->smarty->assign("search", $search);
        // params
        list($rute_from, $rute_to) = explode("-", $rute_all);
        $this->smarty->assign("rute_from", $rute_from);
        $this->smarty->assign("rute_to", $rute_to);
        try {
            /*
             *  request departure
             */
            $params = new getConfirmedSlot($this->com_user['airlines_iata_cd'], $rute_from, $rute_to, $this->season_cd, $this->service_type, $flight_no);
            // class object response
            $response = $this->score_services->__soapCall('getConfirmedSlotSeasonal', array($params));
            // get data from function
            $rs_id = $response->confirmedSlots->confirmedScheduleList->confirmedSchedules;
            // parse
            if (count($rs_id) == 1) {
                // object to list
                $rs_id = array($rs_id);
            }
            $this->smarty->assign("rs_depart", $rs_id);
            /*
             *  request arrival
             */
            $params = new getConfirmedSlot($this->com_user['airlines_iata_cd'], $rute_to, $rute_from, $this->season_cd, $this->service_type, $flight_no);
            // class object response
            $response = $this->score_services->__soapCall('getConfirmedSlotSeasonal', array($params));
            // get data from function
            $rs_id = $response->confirmedSlots->confirmedScheduleList->confirmedSchedules;
            // parse
            if (count($rs_id) == 1) {
                // object to list
                $rs_id = array($rs_id);
            }
            $this->smarty->assign("rs_arrive", $rs_id);
            // get airport detail
            $local_time_from = $this->m_airport->get_local_time_airport_by_code($rute_from);
            $this->smarty->assign("local_time_from", $local_time_from);
            $local_time_to = $this->m_airport->get_local_time_airport_by_code($rute_to);
            $this->smarty->assign("local_time_to", $local_time_to);
        } catch (Exception $error) {
            /*
              echo "<pre>";
              var_dump($error);
              echo "</pre>";
              die;
             * 
             */
        }
    }

    // add process 0 : non iasm
    public function rute_add_process_non_iasm() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('izin_id', 'ID Izin', 'trim|required');
        $this->tnotification->set_rules('rute_all', 'Rute', 'required');
        $this->tnotification->set_rules('flight_no', 'Nomor Penerbangan', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('tipe', 'Tipe Pesawat', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('capacity', 'Kapasitas Pesawat', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('etd', 'ETD', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('eta', 'ETA', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('dos[]', 'DOS', 'trim|required');
        $this->tnotification->set_rules('roon', 'RON', 'trim|required|maxlength[1]|is_natural');
        $this->tnotification->set_rules('start_date', 'Tanggal Mulai', 'trim|required');
        $this->tnotification->set_rules('end_date', 'Tanggal Berakhir', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            list($rute_from, $rute_to) = explode('-', $this->input->post('rute_all'));
            // validasi untuk rute yang sama, nomor penerbangan tidak boleh beda
            $flight_no_from = $this->m_registrasi->get_flight_no_by_izin_id(array($this->input->post('izin_id'), $rute_from . '-' . $rute_to));
            if (!empty($flight_no_from)) {
                if ($flight_no_from <> $this->input->post('flight_no')) {
                    // notification
                    $this->tnotification->sent_notification("error", "Nomor penerbangan harus sama dengan sebelumnya!");
                    // redirect
                    redirect('izin_pending_internasional/perpanjangan/rute_data/' . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
                }
            }

            // validasi untuk rute sebaliknya, nomor penerbangan juga tidak boleh sama
            $flight_no_to = $this->m_registrasi->get_flight_no_by_izin_id(array($this->input->post('izin_id'), $rute_to . '-' . $rute_from));
            if (!empty($flight_no_to)) {
                if ($flight_no_to == $this->input->post('flight_no')) {
                    // notification
                    $this->tnotification->sent_notification("error", "Nomor penerbangan tidak boleh sama dengan rute!" . $rute_to . '-' . $rute_from);
                    // redirect
                    redirect('izin_pending_internasional/perpanjangan/rute_data/' . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
                }
            }
            // dos
            $dos = $this->input->post('dos');
            $dos_input = '';
            for ($i = 1; $i <= 7; $i++) {
                $dos_input .= empty($dos[$i]) ? 0 : $dos[$i];
            }
            // insert izin data
            // izin_id, rute_all, aircraft_type, capacity, flight_no, etd, eta, doop, roon, start_date, end_date, edited, is_used_score
            $rute_id = $this->m_registrasi->get_data_id();
            $params = array(
                'rute_id' => $rute_id,
                'izin_id' => $this->input->post('izin_id'),
                'rute_all' => $this->input->post('rute_all'),
                'tipe' => $this->input->post('tipe'),
                'capacity' => $this->input->post('capacity'),
                'flight_no' => $this->input->post('flight_no'),
                'etd' => $this->input->post('etd'),
                'eta' => $this->input->post('eta'),
                "start_date" => $this->input->post('start_date'),
                "end_date" => $this->input->post('end_date'),
                'doop' => $dos_input,
                'roon' => $this->input->post('roon'),
                'is_used_score' => '0',
            );
            // insert
            if ($this->m_registrasi->insert_izin_data($params)) {
                // get periode & frekuensi
                $frekuensi = $this->m_registrasi->get_total_frekuensi_by_izin_id(array($this->input->post('izin_id')));
                // update status layanan penerbangan OQ / VV
                $total = $this->m_registrasi->get_services_flight(array($this->input->post('izin_id')));
                if ($total == 2) {
                    // VV
                    $params = array(
                        'pairing' => 'VV',
                        'izin_start_date' => $frekuensi['start_date'],
                        'izin_expired_date' => $frekuensi['end_date'],
                    );
                } else {
                    // OW
                    $params = array(
                        'pairing' => 'OW',
                        'izin_start_date' => $frekuensi['start_date'],
                        'izin_expired_date' => $frekuensi['end_date'],
                    );
                }
                $where = array(
                    'registrasi_id' => $this->input->post('registrasi_id'),
                    'izin_id' => $this->input->post('izin_id'),
                );
                $this->m_registrasi->update_izin_rute($params, $where);
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
        redirect("izin_pending_internasional/perpanjangan/rute_data/" . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
    }

    // add process 1 : mixed
    public function rute_add_process_mixed() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('izin_id', 'ID Izin', 'trim|required');
        // lines
        $lines = $this->input->post('save');
        $index = key($lines);
        $services = $this->input->post('services_cd');
        // check slot parameter
        $this->tnotification->set_rules('slot_rute_all_' . $index, '', 'required');
        $this->tnotification->set_rules('slot_aircraft_type_' . $index, '', 'required');
        $this->tnotification->set_rules('slot_capacity_' . $index, '', 'required');
        $this->tnotification->set_rules('slot_flight_no_' . $index, '', 'required');
        if ($services == 'departure') {
            $this->tnotification->set_rules('slot_etd_' . $index, '', 'required');
        }
        if ($services == 'arrival') {
            $this->tnotification->set_rules('slot_eta_' . $index, '', 'required');
        }
        $this->tnotification->set_rules('slot_doop_' . $index, '', 'required');
        $this->tnotification->set_rules('slot_roon_' . $index, '', 'required');
        $this->tnotification->set_rules('slot_start_date_' . $index, '', 'required');
        $this->tnotification->set_rules('slot_end_date_' . $index, '', 'required');
        // slot parameter
        $slot_rute_all = $this->input->post('slot_rute_all_' . $index);
        $slot_aircraft_type = $this->input->post('slot_aircraft_type_' . $index);
        $slot_capacity = $this->input->post('slot_capacity_' . $index);
        $slot_flight_no = $this->input->post('slot_flight_no_' . $index);
        if ($services == 'departure') {
            $slot_etd = $this->input->post('slot_etd_' . $index);
            $slot_etd = substr($slot_etd, 0, 2) . ':' . substr($slot_etd, 2, 2);
        } else {
            $slot_etd = NULL;
        }
        if ($services == 'arrival') {
            $slot_eta = $this->input->post('slot_eta_' . $index);
            $slot_eta = substr($slot_eta, 0, 2) . ':' . substr($slot_eta, 2, 2);
        } else {
            $slot_eta = NULL;
        }
        $slot_doop = $this->input->post('slot_doop_' . $index);
        $slot_roon = $this->input->post('slot_roon_' . $index);
        $slot_start_date = $this->input->post('slot_start_date_' . $index);
        $slot_end_date = $this->input->post('slot_end_date_' . $index);
        // check local parameter
        $this->tnotification->set_rules('rute_all_' . $index, 'Rute', 'required');
        $this->tnotification->set_rules('flight_no_' . $index, 'Nomor Penerbangan', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('aircraft_type_' . $index, 'Tipe Pesawat', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('capacity_' . $index, 'Kapasitas Pesawat', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('etd_' . $index, 'ETD', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('eta_' . $index, 'ETA', 'trim|required|maxlength[5]');
        $this->tnotification->set_rules('doop_' . $index, 'DOS', 'trim|required');
        $this->tnotification->set_rules('roon_' . $index, 'RON', 'trim|required|maxlength[1]');
        $this->tnotification->set_rules('start_date_' . $index, 'Tanggal Mulai', 'trim|required');
        $this->tnotification->set_rules('end_date_' . $index, 'Tanggal Berakhir', 'trim|required');
        // local time parameters
        $rute_all = $this->input->post('rute_all_' . $index);
        $aircraft_type = $this->input->post('aircraft_type_' . $index);
        $capacity = $this->input->post('capacity_' . $index);
        $flight_no = $this->input->post('flight_no_' . $index);
        $etd = $this->input->post('etd_' . $index);
        $eta = $this->input->post('eta_' . $index);
        $doop = $this->input->post('doop_' . $index);
        $roon = $this->input->post('roon_' . $index);
        $start_date = $this->input->post('start_date_' . $index);
        $end_date = $this->input->post('end_date_' . $index);
        // process
        if ($this->tnotification->run() !== FALSE) {
            // from to
            list($rute_from, $rute_to) = explode('-', $rute_all);
            // validasi untuk rute yang sama, nomor penerbangan tidak boleh beda
            $flight_no_from = $this->m_registrasi->get_flight_no_by_izin_id(array($this->input->post('izin_id'), $rute_from . '-' . $rute_to));
            if (!empty($flight_no_from)) {
                if ($flight_no_from <> $flight_no) {
                    // notification
                    $this->tnotification->sent_notification("error", "Nomor penerbangan harus sama dengan sebelumnya!");
                    // redirect
                    redirect('izin_pending_internasional/perpanjangan/rute_data/' . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
                }
            }
            // validasi untuk rute sebaliknya, nomor penerbangan juga tidak boleh sama
            $flight_no_to = $this->m_registrasi->get_flight_no_by_izin_id(array($this->input->post('izin_id'), $rute_to . '-' . $rute_from));
            if (!empty($flight_no_to)) {
                if ($flight_no_to == $flight_no) {
                    // notification
                    $this->tnotification->sent_notification("error", "Nomor penerbangan tidak boleh sama dengan rute!" . $rute_to . '-' . $rute_from);
                    // redirect
                    redirect('izin_pending_internasional/perpanjangan/rute_data/' . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
                }
            }
            // insert izin data
            // izin_id, rute_all, aircraft_type, capacity, flight_no, etd, eta, doop, roon, start_date, end_date, edited, is_used_score
            $rute_id = $this->m_registrasi->get_data_id();
            $params = array(
                'rute_id' => $rute_id,
                'izin_id' => $this->input->post('izin_id'),
                'rute_all' => $rute_all,
                'tipe' => $aircraft_type,
                'capacity' => $capacity,
                'flight_no' => $flight_no,
                'etd' => $etd,
                'eta' => $eta,
                "start_date" => $start_date,
                "end_date" => $end_date,
                'doop' => $doop,
                'roon' => $roon,
                'is_used_score' => '1',
            );
            // insert
            if ($this->m_registrasi->insert_izin_data($params)) {
                // get periode & frekuensi
                $frekuensi = $this->m_registrasi->get_total_frekuensi_by_izin_id(array($this->input->post('izin_id')));
                // update status layanan penerbangan OQ / VV
                $total = $this->m_registrasi->get_services_flight(array($this->input->post('izin_id')));
                if ($total == 2) {
                    // VV
                    $params = array(
                        'pairing' => 'VV',
                        'izin_start_date' => $frekuensi['start_date'],
                        'izin_expired_date' => $frekuensi['end_date'],
                    );
                } else {
                    // OW
                    $params = array(
                        'pairing' => 'OW',
                        'izin_start_date' => $frekuensi['start_date'],
                        'izin_expired_date' => $frekuensi['end_date'],
                    );
                }
                $where = array(
                    'registrasi_id' => $this->input->post('registrasi_id'),
                    'izin_id' => $this->input->post('izin_id'),
                );
                $this->m_registrasi->update_izin_rute($params, $where);
                /*
                 * slot update
                 */
                // delete
                $where = array(
                    'rute_id' => $rute_id,
                );
                $this->m_registrasi->delete_izin_data_slot($where);
                // insert slot
                $slot_id = $this->m_registrasi->get_data_id() + 1;
                $services_cd = $this->input->post('services_cd');
                $params = array(
                    'slot_id' => $slot_id,
                    'rute_id' => $rute_id,
                    'rute_all' => $slot_rute_all,
                    'tipe' => $slot_aircraft_type,
                    'capacity' => $slot_capacity,
                    'flight_no' => $slot_flight_no,
                    'etd' => ($services_cd == 'departure') ? $slot_etd : NULL,
                    'eta' => ($services_cd == 'arrival') ? $slot_eta : NULL,
                    "start_date" => $slot_start_date,
                    "end_date" => $slot_end_date,
                    'doop' => $slot_doop,
                    'roon' => $slot_roon,
                    'services_cd' => $services_cd,
                );
                $this->m_registrasi->insert_izin_data_slot($params);
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
        redirect("izin_pending_internasional/perpanjangan/rute_data/" . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
    }

    // add process 2 : iasm
    public function rute_add_process_iasm() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('izin_id', 'ID Izin', 'trim|required');
        $this->tnotification->set_rules('departure_izin_data_slot', 'Departure', 'trim|required');
        $this->tnotification->set_rules('arrival_izin_data_slot', 'Arrival', 'trim|required');
        // departure
        $index_departure = $this->input->post('departure_izin_data_slot');
        $this->tnotification->set_rules('departure_rute_all_' . $index_departure . '', 'Rute');
        $this->tnotification->set_rules('departure_aircraft_type_' . $index_departure . '', 'Tipe Pesawat');
        $this->tnotification->set_rules('departure_capacity_' . $index_departure . '', 'Kapasitas Pesawat');
        $this->tnotification->set_rules('departure_flight_no_' . $index_departure . '', 'Nomor Penerbangan');
        $this->tnotification->set_rules('departure_etd_' . $index_departure . '', 'ETD');
        $this->tnotification->set_rules('departure_doop_' . $index_departure . '', 'DOS');
        $this->tnotification->set_rules('departure_roon_' . $index_departure . '', 'RON');
        $this->tnotification->set_rules('departure_start_date_' . $index_departure . '', 'Tanggal Mulai');
        $this->tnotification->set_rules('departure_end_date_' . $index_departure . '', 'Tanggal Berakhir');
        // arrival
        $index_arrival = $this->input->post('arrival_izin_data_slot');
        $this->tnotification->set_rules('arrival_rute_all_' . $index_arrival . '', 'Rute');
        $this->tnotification->set_rules('arrival_aircraft_type_' . $index_arrival . '', 'Tipe Pesawat');
        $this->tnotification->set_rules('arrival_capacity_' . $index_arrival . '', 'Kapasitas Pesawat');
        $this->tnotification->set_rules('arrival_flight_no_' . $index_arrival . '', 'Nomor Penerbangan');
        $this->tnotification->set_rules('arrival_eta_' . $index_arrival . '', 'ETA');
        $this->tnotification->set_rules('arrival_doop_' . $index_arrival . '', 'DOS');
        $this->tnotification->set_rules('arrival_roon_' . $index_arrival . '', 'RON');
        $this->tnotification->set_rules('arrival_start_date_' . $index_arrival . '', 'Tanggal Mulai');
        $this->tnotification->set_rules('arrival_end_date_' . $index_arrival . '', 'Tanggal Berakhir');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // slot parameter departure
            $rute_all_departure = $this->input->post('departure_rute_all_' . $index_departure . '');
            $aircraft_type_departure = $this->input->post('departure_aircraft_type_' . $index_departure . '');
            $capacity_departure = $this->input->post('departure_capacity_' . $index_departure . '');
            $flight_no_departure = $this->input->post('departure_flight_no_' . $index_departure . '');
            $etd_departure = $this->input->post('departure_etd_' . $index_departure . '');
            $start_date_departure = $this->input->post('departure_start_date_' . $index_departure . '');
            $end_date_departure = $this->input->post('departure_end_date_' . $index_departure . '');
            $doop_departure = $this->input->post('departure_doop_' . $index_departure . '');
            $roon_departure = $this->input->post('departure_roon_' . $index_departure . '');
            // params
            list($rute_from, $rute_to) = explode("-", $rute_all_departure);
            // get airport detail
            $local_time_from = $this->m_airport->get_local_time_airport_by_code($rute_from);
            $local_time_to = $this->m_airport->get_local_time_airport_by_code($rute_to);
            // --
            $etd_departure_input = substr($etd_departure, 0, 2) . substr($etd_departure, 3, 2);
            $local_departure = $this->m_slot_check->get_slot_local_time($etd_departure_input, $doop_departure, $roon_departure, $local_time_from['airport_utc_sign'], $local_time_from['airport_utc'], $start_date_departure, $end_date_departure);
            // slot parameter arrival
            $rute_all_arrival = $this->input->post('arrival_rute_all_' . $index_arrival . '');
            $aircraft_type_arrival = $this->input->post('arrival_aircraft_type_' . $index_arrival . '');
            $capacity_arrival = $this->input->post('arrival_capacity_' . $index_arrival . '');
            $flight_no_arrival = $this->input->post('arrival_flight_no_' . $index_arrival . '');
            $eta_arrival = $this->input->post('arrival_eta_' . $index_arrival . '');
            $start_date_arrival = $this->input->post('arrival_start_date_' . $index_arrival . '');
            $end_date_arrival = $this->input->post('arrival_end_date_' . $index_arrival . '');
            $doop_arrival = $this->input->post('arrival_doop_' . $index_arrival . '');
            $roon_arrival = $this->input->post('arrival_roon_' . $index_arrival . '');
            // --
            $eta_arrival_input = substr($eta_arrival, 0, 2) . substr($eta_arrival, 3, 2);
            $local_arrival = $this->m_slot_check->get_slot_local_time($eta_arrival_input, $doop_arrival, 0, $local_time_to['airport_utc_sign'], $local_time_to['airport_utc'], $start_date_arrival, $end_date_arrival);
            // parameters
            $departure = array(
                'tipe' => $aircraft_type_departure,
                'capacity' => $capacity_arrival,
                'flight_no' => $flight_no_departure,
                'dos' => $local_departure['dos'],
                'start_date' => $local_departure['start_date'],
                'end_date' => $local_departure['end_date'],
            );
            $arrival = array(
                'tipe' => $aircraft_type_arrival,
                'capacity' => $capacity_arrival,
                'flight_no' => $flight_no_arrival,
                'dos' => $local_arrival['dos'],
                'start_date' => $local_arrival['start_date'],
                'end_date' => $local_arrival['end_date'],
            );
            // HARUS TEPAT BERPASANGAN DENGAN PERIODE BERGESER +- 5 days
            $result = $this->m_slot_check->check_slot_local_pairing($departure, $arrival);
            if (empty($result)) {
                // jika pasangan tidak tepat
                $this->tnotification->sent_notification("error", "Slot Departure dan Slot Arrival tidak berpasangan dengan tepat. Perbaiki slot yang anda miliki!");
            } else {
                // validasi untuk rute yang sama, nomor penerbangan tidak boleh beda
                $flight_no_from = $this->m_registrasi->get_flight_no_by_izin_id(array($this->input->post('izin_id'), $rute_from . '-' . $rute_to));
                if (!empty($flight_no_from)) {
                    if ($flight_no_from <> $flight_no_departure) {
                        // notification
                        $this->tnotification->sent_notification("error", "Nomor penerbangan harus sama dengan sebelumnya!");
                        // redirect
                        redirect('izin_pending_internasional/perpanjangan/rute_data/' . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
                    }
                }
                // validasi untuk rute sebaliknya, nomor penerbangan juga tidak boleh sama
                $flight_no_to = $this->m_registrasi->get_flight_no_by_izin_id(array($this->input->post('izin_id'), $rute_to . '-' . $rute_from));
                if (!empty($flight_no_to)) {
                    if ($flight_no_to == $flight_no_departure) {
                        // notification
                        $this->tnotification->sent_notification("error", "Nomor penerbangan tidak boleh sama dengan rute!" . $rute_to . '-' . $rute_from);
                        // redirect
                        redirect('izin_pending_internasional/perpanjangan/rute_data/' . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
                    }
                }
                // insert izin data
                // izin_id, rute_all, aircraft_type, capacity, flight_no, etd, eta, doop, roon, start_date, end_date, edited, is_used_score
                $rute_id = $this->m_registrasi->get_data_id();
                $params = array(
                    'rute_id' => $rute_id,
                    'izin_id' => $this->input->post('izin_id'),
                    'rute_all' => $rute_all_departure,
                    'tipe' => $aircraft_type_departure,
                    'capacity' => $capacity_departure,
                    'flight_no' => $flight_no_departure,
                    'etd' => $local_departure['local_time'],
                    'eta' => $local_arrival['local_time'],
                    "start_date" => $result['start_date'],
                    "end_date" => $result['end_date'],
                    'doop' => $local_departure['dos'],
                    'roon' => $roon_departure,
                    'is_used_score' => '2',
                );
                // insert
                if ($this->m_registrasi->insert_izin_data($params)) {
                    // get periode & frekuensi
                    $frekuensi = $this->m_registrasi->get_total_frekuensi_by_izin_id(array($this->input->post('izin_id')));
                    // update status layanan penerbangan OQ / VV
                    $total = $this->m_registrasi->get_services_flight(array($this->input->post('izin_id')));
                    if ($total == 2) {
                        // VV
                        $params = array(
                            'pairing' => 'VV',
                            'izin_start_date' => $frekuensi['start_date'],
                            'izin_expired_date' => $frekuensi['end_date'],
                        );
                    } else {
                        // OW
                        $params = array(
                            'pairing' => 'OW',
                            'izin_start_date' => $frekuensi['start_date'],
                            'izin_expired_date' => $frekuensi['end_date'],
                        );
                    }
                    $where = array(
                        'registrasi_id' => $this->input->post('registrasi_id'),
                        'izin_id' => $this->input->post('izin_id'),
                    );
                    $this->m_registrasi->update_izin_rute($params, $where);
                    /*
                     * slot update
                     */
                    // delete
                    $where = array(
                        'rute_id' => $rute_id,
                    );
                    $this->m_registrasi->delete_izin_data_slot($where);
                    // insert departure
                    $slot_id = $this->m_registrasi->get_data_id() + 1;
                    $params = array(
                        'slot_id' => $slot_id,
                        'rute_id' => $rute_id,
                        'rute_all' => $rute_all_departure,
                        'tipe' => $aircraft_type_departure,
                        'capacity' => $capacity_departure,
                        'flight_no' => $flight_no_departure,
                        'etd' => $etd_departure,
                        'eta' => NULL,
                        "start_date" => $start_date_departure,
                        "end_date" => $end_date_departure,
                        'doop' => $doop_departure,
                        'roon' => $roon_departure,
                        'services_cd' => 'departure',
                    );
                    $this->m_registrasi->insert_izin_data_slot($params);
                    // insert arrival
                    $slot_id = $this->m_registrasi->get_data_id() + 1;
                    $params = array(
                        'slot_id' => $slot_id,
                        'rute_id' => $rute_id,
                        'rute_all' => $rute_all_arrival,
                        'tipe' => $aircraft_type_arrival,
                        'capacity' => $capacity_arrival,
                        'flight_no' => $flight_no_arrival,
                        'etd' => NULL,
                        'eta' => $eta_arrival,
                        "start_date" => $start_date_arrival,
                        "end_date" => $end_date_arrival,
                        'doop' => $doop_arrival,
                        'roon' => $roon_arrival,
                        'services_cd' => 'arrival',
                    );
                    $this->m_registrasi->insert_izin_data_slot($params);
                    // notification
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                } else {
                    // default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
                }
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/rute_data/" . $this->input->post('registrasi_id') . '/' . $this->input->post('izin_id'));
    }

    // proses pencarian
    public function rute_data_search_process() {
        // set page rules
        $this->_set_page_rule("R");
        // params
        $registrasi_id = $this->input->post("registrasi_id");
        $izin_id = $this->input->post("izin_id");
        // action
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_slot_rute');
        } else {
            $rute_all = $this->input->post("rute_all");
            $flight_no = $this->input->post("flight_no");
            // set session
            $params = array(
                "rute_all" => strtoupper(trim($rute_all)),
                "flight_no" => strtoupper(trim($flight_no)),
            );
            $this->tsession->set_userdata("search_slot_rute", $params);
        }
        // redirect
        redirect("izin_pending_internasional/perpanjangan/rute_data/" . $registrasi_id . '/' . $izin_id);
    }

    // delete process izin data
    public function izin_data_delete_process($registrasi_id = "", $izin_id = "", $rute_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array(
            'izin_id' => $izin_id,
            'rute_id' => $rute_id,
        );
        // execute
        if ($this->m_registrasi->delete_izin_data($params)) {
            // update status layanan penerbangan OQ / VV
            $total = $this->m_registrasi->get_services_flight(array($izin_id));
            if ($total == 2) {
                // VV
                $params = array(
                    'pairing' => 'VV',
                );
            } elseif ($total == 1) {
                // OW
                $params = array(
                    'pairing' => 'OW',
                );
            } else {
                // NULL
                $params = array(
                    'pairing' => NULL,
                );
            }
            $where = array(
                'registrasi_id' => $registrasi_id,
                'izin_id' => $izin_id,
            );
            $this->m_registrasi->update_izin_rute($params, $where);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/rute_data/" . $registrasi_id . '/' . $izin_id);
    }

    /*
     * STEP 3 : SLOT CLEARANCE
     */

    // list data slot
    public function list_slot($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/list_slot.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        $this->smarty->assign("detail", $result);
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // jika 2 : IASM semua
        // redirect to file attachment
        if ($is_used_score == 2) {
            // redirect
            redirect("izin_pending_internasional/perpanjangan/list_files/" . $registrasi_id);
        }
        // list slot
        $rs_id = $this->m_registrasi->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add slot
    public function slot_add($registrasi_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/slot_add.html");
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
        $this->smarty->assign("detail", $result);
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // jika 2 : IASM semua
        // redirect to file attachment
        if ($is_used_score == 2) {
            // redirect
            redirect("izin_pending_internasional/perpanjangan/list_files/" . $registrasi_id);
        }
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // add slot process
    public function add_slot_process() {
        // set page rules
        $this->_set_page_rule("C");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('slot_subject', 'Subyek Surat', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('slot_number', 'Nomor Surat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('slot_date', 'Tanggal Surat', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('slot_desc', 'Perihal', 'trim|required|maxlength[50]');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get id
        $slot_id = $this->m_registrasi->get_data_id();
        // upload foto
        if (!empty($_FILES['slot_file_name']['tmp_name'])) {
            // upload config
            $config['upload_path'] = 'resource/doc/slot/' . $registrasi_id . '/' . $slot_id;
            $config['allowed_types'] = 'pdf|jpeg|jpg|docx|doc|xls|xlsx';
            $config['max_size'] = 1024;
            $this->tupload->initialize($config);
            // process upload
            if ($this->tupload->do_upload('slot_file_name')) {
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
            // slot_id, registrasi_id, slot_subject, slot_number, slot_date, slot_desc, slot_file_name, slot_file_path, mdb, mdd
            $params = array(
                'slot_id' => $slot_id,
                'registrasi_id' => $this->input->post('registrasi_id'),
                'slot_subject' => $this->input->post('slot_subject'),
                'slot_number' => $this->input->post('slot_number'),
                'slot_date' => $this->input->post('slot_date'),
                'slot_desc' => $this->input->post('slot_desc'),
                'slot_file_name' => $data['file_name'],
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            // update
            if ($this->m_registrasi->insert_slot($params)) {
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // remove dir
                $this->tupload->remove_dir('resource/doc/slot/' . $registrasi_id . '/' . $slot_id);
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // remove dir
            $this->tupload->remove_dir('resource/doc/slot/' . $registrasi_id . '/' . $slot_id);
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/slot_add/" . $this->input->post('registrasi_id'));
    }

    // edit slot
    public function slot_edit($registrasi_id = "", $slot_id = "") {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/slot_edit.html");
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
        $this->smarty->assign("detail", $result);
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // jika 2 : IASM semua
        // redirect to file attachment
        if ($is_used_score == 2) {
            // redirect
            redirect("izin_pending_internasional/perpanjangan/list_files/" . $registrasi_id);
        }
        // detail slot
        $result = $this->m_registrasi->get_detail_slot_by_id(array($registrasi_id, $slot_id));
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit slot process
    public function edit_slot_process() {
        // set page rules
        $this->_set_page_rule("U");
        // load
        $this->load->library('tupload');
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        $this->tnotification->set_rules('slot_id', 'ID slot', 'trim|required');
        $this->tnotification->set_rules('slot_subject', 'Subyek Surat', 'trim|required|maxlength[100]');
        $this->tnotification->set_rules('slot_number', 'Nomor Surat', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('slot_date', 'Tanggal Surat', 'trim|required|maxlength[10]');
        $this->tnotification->set_rules('slot_desc', 'Perihal', 'trim|required|maxlength[50]');
        // registrasi id
        $registrasi_id = $this->input->post('registrasi_id');
        // get id
        $slot_id = $this->input->post('slot_id');
        // detail slot
        $result = $this->m_registrasi->get_detail_slot_by_id(array($registrasi_id, $slot_id));
        if (empty($result)) {
            redirect('izin_pending_internasional/perpanjangan/list_slot/' . $registrasi_id);
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // update slot clearance
            // slot_id, registrasi_id, slot_subject, slot_number, slot_date, slot_desc, slot_file_name, slot_file_path, mdb, mdd
            $params = array(
                'slot_subject' => $this->input->post('slot_subject'),
                'slot_number' => $this->input->post('slot_number'),
                'slot_date' => $this->input->post('slot_date'),
                'slot_desc' => $this->input->post('slot_desc'),
                'mdb' => $this->com_user['user_id'],
                'mdd' => date('Y-m-d h:i:s'),
            );
            $where = array(
                "registrasi_id" => $registrasi_id,
                "slot_id" => $slot_id,
            );
            // update
            if ($this->m_registrasi->update_slot($params, $where)) {
                // upload files
                if (!empty($_FILES['slot_file_name']['tmp_name'])) {
                    // upload config
                    $config['upload_path'] = 'resource/doc/slot/' . $registrasi_id . '/' . $slot_id;
                    $config['allowed_types'] = 'pdf|jpeg|jpg|docx|doc|xls|xlsx';
                    $config['max_size'] = 1024;
                    $this->tupload->initialize($config);
                    // process upload
                    if ($this->tupload->do_upload('slot_file_name')) {
                        $data = $this->tupload->data();
                        // --
                        $params = array(
                            'slot_file_name' => $data['file_name'],
                        );
                        $where = array(
                            "registrasi_id" => $registrasi_id,
                            "slot_id" => $slot_id,
                        );
                        $this->m_registrasi->update_slot($params, $where);
                        // remove file
                        $this->tupload->remove_file('resource/doc/slot/' . $registrasi_id . '/' . $slot_id . '/' . $result['slot_file_name']);
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
        redirect("izin_pending_internasional/perpanjangan/slot_edit/" . $registrasi_id . '/' . $slot_id);
    }

    // download
    public function slot_download($registrasi_id = "", $slot_id = "") {
        // get detail data
        $params = array($registrasi_id, $slot_id);
        $result = $this->m_registrasi->get_detail_slot_by_id($params);
        if (empty($result)) {
            redirect("izin_pending_internasional/perpanjangan/list_slot/" . $registrasi_id);
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
            redirect("izin_pending_internasional/perpanjangan/list_slot/" . $registrasi_id);
        }
    }

    // delete slot
    public function slot_delete($registrasi_id = "", $slot_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // load
        $this->load->library('tupload');
        // detail slot
        $result = $this->m_registrasi->get_detail_slot_by_id(array($registrasi_id, $slot_id));
        if (empty($result)) {
            redirect('izin_pending_internasional/perpanjangan/list_slot/' . $registrasi_id);
        }
        // delete
        $params = array($slot_id, $this->com_user['airlines_id']);
        // execute
        if ($this->m_registrasi->delete_slot($params)) {
            // remove file
            $this->tupload->remove_dir('resource/doc/slot/' . $registrasi_id . '/' . $slot_id);
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/list_slot/" . $registrasi_id);
    }

    // list_slot_process
    public function list_slot_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('registrasi_id', 'ID Permohonan', 'trim|required');
        // validasi
        $rs_id = $this->m_registrasi->get_list_data_slot_by_id(array($this->input->post('registrasi_id'), $this->com_user['airlines_id']));
        if (empty($rs_id)) {
            $this->tnotification->set_error_message('Belum ada data rute yang diinputkan!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect
            redirect("izin_pending_internasional/perpanjangan/list_files/" . $this->input->post('registrasi_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/list_slot/" . $this->input->post('registrasi_id'));
    }

    /*
     * STEP 4 : FILE ATTACHMENT
     */

    // files attachment
    public function list_files($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/files.html");
        // get detail data registrasi
        $params = array($registrasi_id, $this->com_user['airlines_id'], $this->group_id, $this->flow_id);
        $result = $this->m_registrasi->get_registrasi_pending_by_id($params);
        if (empty($result)) {
            redirect('member/pending_izin');
        }
        $this->smarty->assign("detail", $result);
        // list persyaratan
        $rs_files = $this->m_registrasi->get_list_file_required_internasional(array($result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']));
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
            $rs_files = $this->m_registrasi->get_list_file_required_internasional(array($result ['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']));
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
        redirect("izin_pending_internasional/perpanjangan/list_files/" . $registrasi_id);
    }

    // download
    public function files_download($data_id = "", $ref_id = "") {
        // get detail data
        $params = array($data_id, $ref_id);
        $result = $this->m_registrasi->get_detail_files_by_id($params);
        if (empty($result)) {
            redirect("izin_pending_internasional/perpanjangan/list_files/" . $data_id);
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
        if (!$this->m_registrasi->is_file_completed_int(array($registrasi_id, $result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']))) {
            $this->tnotification->set_error_message('File persyaratan belum diupload!');
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // notification
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // default redirect
            redirect("izin_pending_internasional/perpanjangan/review/" . $registrasi_id);
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("izin_pending_internasional/perpanjangan/list_files/" . $registrasi_id);
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
        $this->smarty->assign("template_content", "izin_pending_internasional/perpanjangan/review.html"); // get detail data
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
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = array();
        $pairing = array();
        $frekuensi = array();
        $data = array();
        $rs_id = $this->m_registrasi->get_list_izin_rute_pending_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $no = 1;
        foreach ($rs_id as $izin_rute) {            
            // selected
            $rute_selected[$izin_rute['kode_frekuensi']] = '1';
            // izin dat
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
            // rute sebelumnya
            if (!empty($izin_rute['kode_frekuensi'])) {
                $kode_frekuensi[$izin_rute['kode_frekuensi']] = true;
            }
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        // frekuensi
        $this->smarty->assign("total", $this->m_registrasi->get_total_frekuensi_by_registrasi_id($registrasi_id));
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
            $izin_rute = $this->m_registrasi->get_list_izin_rute_aktif_by_kode_frekuensi(array($kode, $this->com_user['airlines_id']));
            // get izin data
            $izin_data = $this->m_registrasi->get_list_izin_data_pending_by_id(array($izin_rute['izin_id']));
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
        // jika 2 : IASM semua
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // list slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_registrasi->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            $this->smarty->assign("rs_slot", $rs_slot);
        }
        $this->smarty->assign("is_used_score", $is_used_score);
        // list persyaratan
        $rs_files = $this->m_registrasi->get_list_file_required_internasional(array($result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']));
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
        // jika 2 : IASM semua, tidak perlu upload
        // check bandara
        $is_used_score = 0;
        $airport = explode('-', $result['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_registrasi->get_airport_score_by_code(array($iata_code));
            $is_used_score += $data['is_used_score'];
        }
        // validate slot
        if ($is_used_score <> 2) {
            $rs_slot = $this->m_registrasi->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
            if (empty($rs_slot)) {
                $this->tnotification->set_error_message('Slot Clearance belum diupload!');
            }
        }
        // validation
        if (!$this->m_registrasi->is_file_completed_int(array($registrasi_id, $result['izin_group'], $result['izin_flight'], $this->com_user['airlines_nationality']))) {
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
                    'flow_id' => 11,
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
        redirect("izin_pending_internasional/perpanjangan/review/" . $registrasi_id);
    }

}
