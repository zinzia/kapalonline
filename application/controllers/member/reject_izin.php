<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class reject_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model("reject/m_reject_izin");
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/index.html");
        // get tahun
        $this->smarty->assign("rs_tahun", $this->m_reject_izin->get_list_tahun_report($this->com_user['airlines_id']));
        // bulan
        $bulan = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
        $this->smarty->assign("rs_bulan", $bulan);
        // get search parameter
        $search = $this->tsession->userdata('search_reject');
        // search parameters
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $data_flight = empty($search['data_flight']) ? '%' : $search['data_flight'];
        // default
        $search['bulan'] = empty($search['bulan']) ? date('m') : $search['bulan'];
        $search['tahun'] = empty($search['tahun']) ? date('Y') : $search['bulan'];
        // assign
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/reject_izin/index/");
        $config['total_rows'] = $this->m_reject_izin->get_total_finished_izin_registrasi(array($this->com_user['airlines_id'], $tahun, $bulan, $data_flight));
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
        // get list
        $params = array($this->com_user['airlines_id'], $tahun, $bulan, $data_flight, ($start - 1), $config['per_page']);
        $rs_id = $this->m_reject_izin->get_list_finished_izin_registrasi($params);
        $this->smarty->assign("rs_id", $rs_id);

        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_reject');
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun"),
                "data_flight" => $this->input->post("data_flight"),
            );
            $this->tsession->set_userdata("search_reject", $params);
        }
        // redirect
        redirect("member/reject_izin");
    }

    /*
     * BARU
     */

    // view penerbitan
    public function baru($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/baru.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        //print_r($detail);
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // download penerbitan
    public function download_baru($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
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
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            // kode izin
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('baru'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '  <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Diajukan Dan Ditolak</h4>
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
                        $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                        $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
//         echo $html;
//         exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output("PENOLAKAN_". str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * PERPANJANGAN
     */

    // perpanjangan
    public function perpanjangan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/perpanjangan.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
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
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // download penerbitan
    public function download_perpanjangan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
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
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            // kode izin
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
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
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('perpanjangan'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Diperpanjang Dan Ditolak</h4>
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
//         echo $html;
//         exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output("PENOLAKAN_".str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * PENUNDAAN
     */

    // view penundaan
    public function penundaan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/penundaan.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);      
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        parent::display();
    }

       // download penerbitan
    public function download_penundaan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('report/published_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
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
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $old['izin_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $lampiran_old = ceil(count($rs_old) / 20);
        $total_lampiran = $lampiran + $lampiran_old + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
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
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('penundaan'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin penundaan pelaksanaan rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '                          <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Diajukan Dan Ditolak</h4>
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
            $last_izin_id = "";
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($total_approved["valid_start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
//         echo $html;
//         exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output("PENOLAKAN_".str_replace("/", "-", $filename) . ".pdf", 'D');
    }
    
    /*
     * PENCABUTAN / PENGHENTIAN
     */

    // view penghentian
    public function penghentian($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/penghentian.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
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
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // download penerbitan
    public function download_penghentian($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));        
        if (empty($detail)) {
            redirect('member/reject_izin');
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
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            // kode izin
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));       
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('penghentian'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin pencabutan rute';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '                          <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Dihentikan Dan Ditolak</h4>
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
        $this->tcpdf->Output("PENOLAKAN_".str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * PENAMBAHAN FREKUENSI
     */

    // view penerbitan
    public function frekuensi_add($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/frekuensi_add.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // download penerbitan
    public function download_frekuensi_add($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
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
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            // kode izin
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('frekuensi_add'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin penambahan frekuensi penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '                          <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Diajukan Dan Ditolak</h4>
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
//         echo $html;
//         exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output("PENOLAKAN_".str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * PENGURANGAN FREKUENSI
     */

    // view penerbitan
    public function frekuensi_delete($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/frekuensi_delete.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);        
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // download penerbitan
    public function download_frekuensi_delete($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
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
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
            // total frekuensi
            $frekuensi[$new['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$new['izin_id']] = $izin_rute['pairing'];
            // kode izin
            if (!empty($new['kode_frekuensi'])) {
                $kode_frekuensi[$new['kode_frekuensi']] = true;
            }
            $no++;
        }
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $rs_old = array();
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));       
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('perpanjangan'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin pengurangan frekuensi penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '                          <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.</li>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Diajukan Dan Ditolak</h4>
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
        $this->tcpdf->Output("PENOLAKAN_".str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * PERUBAHAN
     */

    // perubahan
    public function perubahan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/perubahan.html");
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        $rs_id = $this->m_reject_izin->get_list_izin_rute_reject_by_id(array($registrasi_id, $detail['airlines_id']));
        $no = 1;
        $total_rows = 0;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
        $this->smarty->assign("total_approved", $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id));
        /*
         * RUTE PENERBANGAN SEBELUMNYA
         */
        $no = 1;
        $data = array();
        $pairing_old = array();
        $frekuensi_old = array();
        $total_old = 0;
        foreach ($kode_frekuensi as $kode => $st) {
            // get izin rute
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
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
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        $this->smarty->assign("an", $an);
        $this->smarty->assign("direktur", $users);
        // lampiran dan total
        $lampiran = ceil($total_rows / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->m_reject_izin->terbilang($lampiran));
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        // list slot by IASM
        $airport_iasm = array();
        $airport = explode('-', $detail['izin_rute_start']);
        foreach ($airport as $iata_code) {
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $this->smarty->assign("rs_slot_iasm", $airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $this->smarty->assign("rs_slot_non", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        // kepada yang terpilih
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        $this->smarty->assign("rs_kepada", $rs_kepada);
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        $this->smarty->assign("rs_tembusan", $rs_tembusan);
        // output
        parent::display();
    }

    // download penerbitan
    public function download_perubahan($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);
        /*
         * REGISTRASI
         */
        // detail registrasi
        $detail = $this->m_reject_izin->get_detail_registrasi_by_id(array($registrasi_id));
        if (empty($detail)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("detail", $detail);
        /*
         * RUTE PENERBANGAN
         */
        $kode_frekuensi = '';
        $rs_id = $this->m_reject_izin->get_izin_data_by_registrasi_id(array($registrasi_id, $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $new['izin_id']));
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
            $izin_rute = $this->m_reject_izin->get_history_izin_rute_old_by_kode_frekuensi(array($kode, $detail['airlines_id'], $detail['published_number'], $detail['registrasi_id'], $registrasi_id));
            // get izin data
            $izin_data = $this->m_reject_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            $rs_old = array_merge($rs_old, $izin_data);
            // nomor surat sebelumnya
            $surat_persetujuan = $this->m_reject_izin->get_published_letter_old(array($izin_rute['registrasi_id'], $detail['airlines_id']));
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
            $izin_rute = $this->m_reject_izin->get_total_frekuensi(array($registrasi_id, $detail['airlines_id'], $old['izin_id']));
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
            $users = $this->m_reject_izin->get_com_user_by_role(array(63));
        } else {
            $an = 'DJPU-DAU';
            // get user direktur 45
            $users = $this->m_reject_izin->get_com_user_by_role(array(45));
        }
        // lampiran dan total
        $lampiran = ceil(count($rs_id) / 20);
        $lampiran_old = ceil(count($rs_old) / 20);
        $total_lampiran = $lampiran + $lampiran_old + 1;
        $terbilang = $this->m_reject_izin->terbilang($total_lampiran);
        if ($detail["izin_flight"] == "domestik") {
            $nomor_surat = $this->m_reject_izin->get_published_number_dom($an);
        } else {
            $nomor_surat = $this->m_reject_izin->get_published_number_int($an);
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('published_izin', 'KM'));
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
            $data = $this->m_reject_izin->get_airport_score_by_code(array($iata_code));
            if ($data['is_used_score'] == '1') {
                $airport_iasm[] = $data;
            }
        }
        $total_airport_iasm = count($airport_iasm);
        // list slot by attachment
        $rs_slot = $this->m_reject_izin->get_list_slot_time_by_id(array($registrasi_id, $detail['airlines_id']));
        $total_rs_slot = count($rs_slot);
        // catatan tambahan
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional_by_registrasi(array($registrasi_id));
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
        $rs_kepada = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'kepada'));
        // tembusan yang terpilih
        $rs_tembusan = $this->m_reject_izin->get_list_editorial_by_registrasi(array($registrasi_id, 'tembusan'));
        // total approved
        $total_approved = $this->m_reject_izin->get_total_frekuensi_reject_by_registrasi_id($registrasi_id);

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
        $barcode_url = $this->m_reject_izin->get_barcode_value(array('perubahan'));
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($detail["izin_published_date"]) . '</td>
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
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['izin_perihal']) . '</td>';
        } else {
            $html .= '<td style="vertical-align: top;" align="left">PENOLAKAN ' . strtoupper($detail['group_nm']) . ' ' . strtoupper($detail['izin_flight']) . '</td>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara belum dapat memberikan izin perubahan jadwal rute penerbangan';
        if ($detail["pax_cargo"] == "cargo") {
            $html .= ' untuk cargo';
        }
        $html .= '
                                                berjadwal kepada ' . $detail["airlines_nm"] . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>';
        if ($rs_memos != null) {
            foreach ($rs_memos as $data) {
                $html .= '<li style="line-height:150%;">' . $data["memo"] . '</li>';
            }
        }
        $html .= '                                           
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
            $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
        } else {
            $html .= '<td width="49%">' . $nomor_surat . '</td>';
        }
        $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
                $html .= '<td width="49%">' . $detail["izin_published_letter"] . '</td>';
            } else {
                $html .= '<td width="49%">' . $nomor_surat . '</td>';
            }
            $html .= '
                                    <td width="5%"></td>
                                    <td width="20%"></td>
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
                            <h4 style="font-size: 20px; font-family: times; color: #333;">Rute Penerbangan Yang Diajukan Dan Ditolak</h4>
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
            $last_izin_id = "";
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
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
                            $html .= '
                                                    <td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>
                                                ';
                        $html .= '</tr>';
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
            }
            if ($i == $total_lampiran) {
                $html .= '<tr>
                                    <td colspan="8" align="right">Jumlah Frekuensi / Minggu</td>
                                    <td align="center"><b>' . $total_approved["frekuensi"] . 'X</b></td>
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
                                        <tcpdf method="write2DBarcode" params="' . $params_barcode . '" />
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
//         echo $html;
//         exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $perihal) . "_" . str_replace(" ", "_", $detail["airlines_nm"]);
        $this->tcpdf->Output("PENOLAKAN_".str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * FREKUENSI
     */

    // frekuensi
    public function frekuensi($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/frekuensi.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $result = $this->m_reject_izin->get_reject_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('reject_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_reject_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_reject_izin->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_reject_izin->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // memo
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // get list frekuensi
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $rs_id = $this->m_reject_izin->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/reject_izin');
        }
        foreach ($rs_id as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id as $k2 => $v2) {
                if ($last_key == $v2['izin_id'])
                    $group_izin++;
            }
            $rs_id[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_id", $rs_id);
        // total frek
        $this->smarty->assign("tot_frek", $this->m_reject_izin->get_total_frekuensi_by_registrasi_id(array($registrasi_id)));
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
                $old = $this->m_reject_izin->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $this->com_user['airlines_id'], $new['reject_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['reject_number']);
            $result = $this->m_reject_izin->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_reject_date'];
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

    // download frekuensi
    public function download_frekuensi($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $result = $this->m_reject_izin->get_reject_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/reject_izin');
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('reject_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_reject_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_reject_izin->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_reject_izin->get_list_editorial_tembusan(array($registrasi_id));
        // memo
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $rs_id = $this->m_reject_izin->get_izin_rute_data_by_kode_izin($params);
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
            redirect('member/reject_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_id as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_reject_izin->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $this->com_user['airlines_id'], $new['reject_number']));
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
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['reject_number']);
            $results = $this->m_reject_izin->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_reject_date'];
            $surat_persetujuan[$results['registrasi_id']]['perihal'] = $results['group_nm'];
        }
        // get total lampiran
        $total_data = count($rs_id);
        $per_page = 40;
        $lampiran = ceil($total_data / $per_page);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->terbilang($total_lampiran);
        $count = 1;
        $no = 0;
        $temp = "";
        $frekuensi = 0;
        $izin = 0;
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
        $url = base_url() . 'index.php/information/document_izin/frekuensi/' . $result['registrasi_id'];
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
                        </table><br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>
                                    <td width="49%">' . $result["izin_published_letter"] . '</td>
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
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
                                    <td style="vertical-align: top;">:</td>
                                    <td style="vertical-align: top;">' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri';
        } else {
            $html .= ' Luar Negeri';
        }
        $html .= '
                                    </td>
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
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ';</li>;';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri';
        } else {
            $html .= ' Luar Negeri';
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($result["mdd"]) . ';</li>';
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html.= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan ' . strtolower($result["group_nm"]) . ' rute penerbangan';
        if ($result["pax_cargo"] == "cargo") {
            $html .= ' cargo';
        }
        $html .= '                                        berjadwal
                                                kepada ' . strtoupper($result["airlines_nm"]) . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>';
        foreach ($rs_memos as $data) {
            $html .= '<li style="line-height:150%;">' . $data["memo"] . ';</li>';
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.
                                            </li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.
                                            </li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">';
        if ($result["izin_reject_role"] == "63") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '
                                        <b>
                                            a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA
                                            <br />
                                            DIREKTUR ANGKUTAN UDARA
                                        </b>
                                        ';
        }
        $html .= '
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="60%"></td>
                                    <td align="center" width="40%" colspan="2"><tcpdf method="write2DBarcode" params="' . $params_barcode . '" /></td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
                                        <br />
                                        <b style="text-decoration: underline;">' . $result["reject_by"] . '</b>
                                        <br />
                                        ' . $result["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $result["operator_nip"] . '
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_editorial_tembusan as $value) {
            $html .= '<li style="line-height:150%;">' . $value['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br /><table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>
                                    <td width="49%">' . $result["izin_published_letter"] . '</td>
                                    <td width="5%"></td>
                                    <td width="20%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($result['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . strtoupper($result["airlines_nm"]) . '</li>';
        }
        foreach ($rs_editorial_kepada as $value) {
            $html .= '<li style="line-height:150%;">' . $value['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                                    <tcpdf method="AddPage"><br /><br /><table class="table-form" width="100%" cellpadding="1">
                                        <tr>
                                            <td colspan="3">Lampiran ' . $romawi[$i] . ' Surat Direktorat Jenderal Perhubungan Udara</td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Nomor</td>
                                            <td width="1%">:</td>
                                            <td width="84%">' . $result["izin_published_letter"] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>:</td>
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="13%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="10%" rowspan="2" align="center">Status Perubahan</td>
                                            <td width="10%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="10%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="10%" rowspan="2" align="center">Frek</td>
                                            <td width="10%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                            <td width="17%" rowspan="2" align="center">Periode</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            $kode_frekuensi = "";
            $temp_rute = "";
            $no = 1;
            $y = 0;
            for ($x = $izin; $x < $per_page; $x++) {
                if ($rs_id[$x]["izin_id"] != "") {
                    if ($rs_old[$y] != "") {
                        if ($temp_rute != $rs_id[$x]["rute_all"]) {
                            $html .= '<tr>';
                            if ($kode_frekuensi != $rs_id[$x]["kode_frekuensi"]) {
                                $html .= '<td align="center" rowspan="' . ($rs_id[$x]["rowspan"] + $rs_old[$y]["rowspan"]) . '"> ' . $no++ . '.</td>';
                                $kode_frekuensi = $rs_id[$x]["kode_frekuensi"];
                            }
                            if ($rs_old[$y]["rute_all"] == $rs_id[$x]["rute_all"]) {
                                $html .= '<td align="center" rowspan="2"> ' . $rs_old[$y]["rute_all"] . '</td>';
                            } else {
                                $html .= '<td align="center"> ' . $rs_old[$y]["rute_all"] . '</td>';
                            }
                            $html .= '
                                                        <td align="center">SEMULA</td>
                                                        <td align="center">' . $rs_old[$y]["aircraft_type"] . '</td>
                                                        <td align="center">' . $rs_old[$y]["flight_no"] . '</td>
                                                        <td align="center">' . substr($rs_old[$y]["etd"], 0, 5) . '</td>
                                                        <td align="center">' . substr($rs_old[$y]["eta"], 0, 5) . '</td>
                            ';
                            $html .= '<td align="center"> ' . $rs_old[$y]["frekuensi"] . 'X</td>';
                            $html .= '<td align="center"> ' . $rs_old[$y]["doop"] . '</td>';
                            $html .= '<td align="center"> ' . $this->datetimemanipulation->get_full_date($rs_old[$y]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                            $html .= '</tr>';
                        }
                    }
                    $html .= '<tr>';
                    $html .= '<td align="center"> ' . $rs_id[$x]["rute_all"] . '</td>';
                    $html .= '
                        <td align="center">MENJADI</td>
                        <td align="center" ';
                    ($rs_old[$y]["aircraft_type"] != $rs_id[$x]["aircraft_type"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["aircraft_type"] . '</td>
                        <td align="center" ';
                    ($rs_old[$y]["flight_no"] != $rs_id[$x]["flight_no"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["flight_no"] . '</td>
                        <td align="center" ';
                    ($rs_old[$y]["etd"] != $rs_id[$x]["etd"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                        <td align="center" ';
                    ($rs_old[$y]["eta"] != $rs_id[$x]["eta"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["eta"], 0, 5) . '</td>
                    ';
                    if ($rs_old[$y]["frekuensi"] != $rs_id[$x]["frekuensi"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$y]["frekuensi"] != $rs_id[$x]["frekuensi"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    } else {
                        $html .= '<td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    }
                    if ($rs_old[$y]["doop"] != $rs_id[$x]["doop"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$y]["doop"] != $rs_id[$x]["doop"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["doop"] . '</td>';
                    } else {
                        $html .= '<td align="center">' . $rs_id[$x]["doop"] . '</td>';
                    }
                    if ($rs_old[$y]["start_date"] != $rs_id[$x]["start_date"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$y]["start_date"] != $rs_id[$x]["start_date"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                    } else {
                        $html .= '<td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                    }
                    $html .= '</tr>';
                }
                if ($temp_rute != $rs_id[$x]["rute_all"]) {
                    $y = $y + 1;
                }
                $temp_rute = $rs_id[$x]["rute_all"];
            }
            $izin = $izin + 40;
            $per_page = $per_page + 40;
            $html .= '
                                    </table>
                                ';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        </html>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", strtoupper($result["group_nm"])) . "_" . str_replace(" ", "_", strtoupper($result["airlines_nm"]));
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    /*
     * NOTAM
     */

    // notam
    public function notam($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/notam.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $result = $this->m_reject_izin->get_reject_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('reject_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_reject_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_reject_izin->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_reject_izin->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // memo
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        $this->smarty->assign("rs_memos", $rs_memos);
        // get list frekuensi
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $rs_id = $this->m_reject_izin->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/reject_izin');
        }
        foreach ($rs_id as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id as $k2 => $v2) {
                if ($last_key == $v2['izin_id'])
                    $group_izin++;
            }
            $rs_id[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_id", $rs_id);
        // total frek
        $this->smarty->assign("tot_frek", $this->m_reject_izin->get_total_frekuensi_by_registrasi_id(array($registrasi_id)));
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
                $old = $this->m_reject_izin->get_list_data_rute_by_kode_frekuensi_old_preview(array($new['kode_frekuensi'], $registrasi_id, $this->com_user['airlines_id'], $new['reject_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['reject_number']);
            $result = $this->m_reject_izin->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_reject_date'];
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

    // download penerbitan
    public function download_notam($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        // error_reporting(0);
        set_time_limit(10);

        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $result = $this->m_reject_izin->get_reject_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/reject_izin');
        }
        // get preferences
        $km = $this->m_reject_izin->get_preferences_by_group_and_name(array('reject_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_reject_izin->get_list_data_slot_by_id(array($registrasi_id, $this->com_user['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_reject_izin->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_reject_izin->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_reject_izin->get_list_editorial_tembusan(array($registrasi_id));
        // memo
        $rs_memos = $this->m_reject_izin->get_list_memos_by_izin(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $rs_id = $this->m_reject_izin->get_izin_rute_data_by_kode_izin($params);
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
            redirect('member/reject_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        // data lama ambil dari kode_frekuensi
        $rs_old = array();
        $temp = '';
        foreach ($rs_id as $new) {
            if ($temp <> $new['kode_frekuensi']) {
                $old = $this->m_reject_izin->get_list_data_rute_by_kode_frekuensi_old_preview(array($new['kode_frekuensi'], $registrasi_id, $this->com_user['airlines_id'], $new['reject_number']));
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
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['reject_number']);
            $results = $this->m_reject_izin->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_reject_date'];
            $surat_persetujuan[$results['registrasi_id']]['perihal'] = $results['group_nm'];
        }
        // get total lampiran
        $total_data = count($rs_id);
        $per_page = 40;
        $lampiran = ceil($total_data / $per_page);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->terbilang($total_lampiran);
        $count = 1;
        $no = 0;
        $temp = "";
        $frekuensi = 0;
        $izin = 0;
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
        $url = base_url() . 'index.php/information/document_izin/notam/' . $result['registrasi_id'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);
        // create pdf
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(20, 0, 30);
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
            r.symbol {
                padding: 0;
                border: none;
                border-top: medium double #333;
                color: #333;
                text-align: center;
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
                        </table><br/>
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="10%">Nomor</td>
                                    <td width="1%">:</td>
                                    <td width="49%">' . $result["izin_published_letter"] . '</td>
                                    <td width="5%"></td>
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
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
                                    <td style="vertical-align: top;">:</td>
                                    <td style="vertical-align: top;">' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri';
        } else {
            $html .= ' Luar Negeri';
        }
        $html .= '
                                    </td>
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
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ';</li>;';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri';
        } else {
            $html .= ' Luar Negeri';
        }
        $html .= ', yang diajukan pada tanggal ' . $this->datetimemanipulation->get_date_only($result["mdd"]) . ';</li>';
        foreach ($rs_slot as $data) {
            $html .= '<li style="line-height:150%;">' . $data["slot_subject"] . ' Nomor ' . $data["slot_number"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["slot_date"]) . ' perihal ' . $data["slot_desc"];
            if ($count < $total_slot) {
                $html .= ';</li>';
            } else {
                $html .= ',</li>';
            }
            $count++;
        }
        $html.= '
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin perubahan jadwal rute penerbangan';
        if ($result["pax_cargo"] == "cargo") {
            $html .= ' cargo';
        }
        $html .= '                                        berjadwal
                                                kepada ' . strtoupper($result["airlines_nm"]) . ', sesuai dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan dan ketentuan perundang - undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>';
        foreach ($rs_memos as $data) {
            $html .= '<li style="line-height:150%;">' . $data["memo"] . ';</li>';
        }
        $html .= '
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya untuk dilakukan pengawasan dalam pelaksanaannya.
                                            </li>
                                            <li style="line-height:150%;">Demikian disampaikan, atas perhatiannya diucapkan terima kasih.
                                            </li>
                                        </ol>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">';
        if ($result["izin_reject_role"] == "63") {
            $html .= '<b>DIREKTUR JENDERAL PERHUBUNGAN UDARA</b>';
        } else {
            $html .= '
                                        <b>
                                            a.n. DIREKTUR JENDERAL PERHUBUNGAN UDARA
                                            <br />
                                            DIREKTUR ANGKUTAN UDARA
                                        </b>
                                        ';
        }
        $html .= '
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="60%"></td>
                                    <td align="center" width="40%" colspan="2"><tcpdf method="write2DBarcode" params="' . $params_barcode . '" /></td>
                                </tr>
                                <tr>
                                    <td colspan="3" width="40%"></td>
                                    <td align="center" width="60%" colspan="2">
                                        <br />
                                        <b style="text-decoration: underline;">' . $result["reject_by"] . '</b>
                                        <br />
                                        ' . $result["operator_pangkat"] . '
                                        <br />
                                        NIP. ' . $result["operator_nip"] . '
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b style="text-decoration: underline;">Tembusan :</b>
                                        <ol>';
        foreach ($rs_editorial_tembusan as $value) {
            $html .= '<li style="line-height:150%;">' . $value['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>
                            <tcpdf method="AddPage"><br /><br /><table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td width="25%">Lampiran ' . $romawi[1] . ' Surat Nomor</td>
                                    <td width="1%">:</td>
                                    <td width="49%">' . $result["izin_published_letter"] . '</td>
                                    <td width="5%"></td>
                                    <td width="20%"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <br />
                            <br />
                            <br />
                            <table class="table-form" width="100%" cellpadding="1">
                                <tr>
                                    <td><b style="text-decoration: underline;">Kepada Yth :</b>
                                        <ol>';
        if ($result['airlines_nationality'] == "nasional") {
            $html .= '<li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
        } else {
            $html .= '<li style="line-height:150%;">Kepala Perwakilan ' . strtoupper($result["airlines_nm"]) . '</li>';
        }
        foreach ($rs_editorial_kepada as $value) {
            $html .= '<li style="line-height:150%;">' . $value['redaksional_nm'] . '</li>';
        }
        $html .= '</ol>
                                    </td>
                                </tr>
                            </table>';
        for ($i = 2; $i <= $total_lampiran; $i++) {
            $html .= '
                                    <tcpdf method="AddPage"><br /><br /><table class="table-form" width="100%" cellpadding="1">
                                        <tr>
                                            <td colspan="3">Lampiran ' . $romawi[$i] . ' Surat Direktorat Jenderal Perhubungan Udara</td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Nomor</td>
                                            <td width="1%">:</td>
                                            <td width="84%">' . $result["izin_published_letter"] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>:</td>
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="13%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="10%" rowspan="2" align="center">Status Perubahan</td>
                                            <td width="10%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="10%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="10%" rowspan="2" align="center">Frek</td>
                                            <td width="10%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                            <td width="17%" rowspan="2" align="center">Periode</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            $kode_frekuensi = "";
            $temp_rute = "";
            $no = 1;
            $y = 0;
            for ($x = $izin; $x < $per_page; $x++) {
                if ($rs_id[$x]["izin_id"] != "") {
                    if ($rs_old[$y] != "") {
                        if ($temp_rute != $rs_id[$x]["rute_all"]) {
                            $html .= '<tr>';
                            if ($kode_frekuensi != $rs_id[$x]["kode_frekuensi"]) {
                                $html .= '<td align="center" rowspan="' . ($rs_id[$x]["rowspan"] + $rs_old[$y]["rowspan"]) . '"> ' . $no++ . '.</td>';
                                $kode_frekuensi = $rs_id[$x]["kode_frekuensi"];
                            }
                            $html .= '<td align="center"> ' . $rs_old[$y]["rute_all"] . '</td>';
                            $html .= '
                                                        <td align="center">SEMULA</td>
                                                        <td align="center">' . $rs_old[$y]["aircraft_type"] . '</td>
                                                        <td align="center">' . $rs_old[$y]["flight_no"] . '</td>
                                                        <td align="center">' . substr($rs_old[$y]["etd"], 0, 5) . '</td>
                                                        <td align="center">' . substr($rs_old[$y]["eta"], 0, 5) . '</td>
                            ';
                            $html .= '<td align="center"> ' . $rs_old[$y]["frekuensi"] . 'X</td>';
                            $html .= '<td align="center"> ' . $rs_old[$y]["doop"] . '</td>';
                            $html .= '<td align="center"> ' . $this->datetimemanipulation->get_full_date($rs_old[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                            $html .= '</tr>';
                        }
                    }
                    $html .= '<tr>';
                    $html .= '<td align="center"> ' . $rs_id[$x]["rute_all"] . '</td>';
                    $html .= '
                        <td align="center">MENJADI</td>
                        <td align="center" ';
                    ($rs_old[$y]["aircraft_type"] != $rs_id[$x]["aircraft_type"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["aircraft_type"] . '</td>
                        <td align="center" ';
                    ($rs_old[$y]["flight_no"] != $rs_id[$x]["flight_no"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["flight_no"] . '</td>
                        <td align="center" ';
                    ($rs_old[$y]["etd"] != $rs_id[$x]["etd"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                        <td align="center" ';
                    ($rs_old[$y]["eta"] != $rs_id[$x]["eta"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["eta"], 0, 5) . '</td>
                    ';
                    if ($rs_old[$y]["frekuensi"] != $rs_id[$x]["frekuensi"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$y]["frekuensi"] != $rs_id[$x]["frekuensi"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    } else {
                        $html .= '<td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    }
                    if ($rs_old[$y]["doop"] != $rs_id[$x]["doop"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$y]["doop"] != $rs_id[$x]["doop"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["doop"] . '</td>';
                    } else {
                        $html .= '<td align="center">' . $rs_id[$x]["doop"] . '</td>';
                    }
                    if ($rs_old[$y]["start_date"] != $rs_id[$x]["start_date"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$y]["start_date"] != $rs_id[$x]["start_date"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                    } else {
                        $html .= '<td align="center">' . $this->datetimemanipulation->get_full_date($rs_id[$x]["start_date"]) . ' <br /> ' . $this->datetimemanipulation->get_full_date($rs_id[$x]["end_date"]) . '</td>';
                    }
                    $html .= '</tr>';
                }
                if ($temp_rute != $rs_id[$x]["rute_all"]) {
                    $y = $y + 1;
                }
                $temp_rute = $rs_id[$x]["rute_all"];
            }
            $izin = $izin + 40;
            $per_page = $per_page + 40;
            $html .= '
                                    </table>
                                ';
        }
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        </html>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", strtoupper($result["group_nm"])) . "_" . str_replace(" ", "_", strtoupper($result["airlines_nm"]));
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

    // view penerbitan
    public function migrasi($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject_izin/migrasi.html");
        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $result = $this->m_reject_izin->get_reject_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/reject_izin');
        }
        $this->smarty->assign("result", $result);
        // get list frekuensi
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $rs_id = $this->m_reject_izin->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/reject_izin');
        }
        foreach ($rs_id as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id as $k2 => $v2) {
                if ($last_key == $v2['izin_id'])
                    $group_izin++;
            }
            $rs_id[$k1]['rowspan'] = $group_izin;
        }
        $this->smarty->assign("rs_id", $rs_id);
        // total frek
        $this->smarty->assign("tot_frek", $this->m_reject_izin->get_total_frekuensi_by_registrasi_id(array($registrasi_id)));
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        // get total lampiran
        $total_data = count($rs_id);
        $lampiran = ceil($total_data / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->terbilang($lampiran));
        // output
        parent::display();
    }

    // download penerbitan
    public function download_migrasi($registrasi_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $result = $this->m_reject_izin->get_reject_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/reject_izin');
        }
        // get list frekuensi
        $params = array($registrasi_id, $this->com_user['airlines_id']);
        $rs_id = $this->m_reject_izin->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/reject_izin');
        }
        foreach ($rs_id as $k1 => $v1) {
            $last_key = $v1['izin_id'];
            $group_izin = 0;
            foreach ($rs_id as $k2 => $v2) {
                if ($last_key == $v2['izin_id'])
                    $group_izin++;
            }
            $rs_id[$k1]['rowspan'] = $group_izin;
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        // get total lampiran
        $total_data = count($rs_id);
        $per_page = 40;
        $lampiran = ceil($total_data / $per_page);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->terbilang($total_lampiran);
        $count = 1;
        $no = 0;
        $temp = "";
        $frekuensi = 0;
        $izin = 0;
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
        $url = base_url() . 'index.php/information/document_izin/migrasi/' . $result['registrasi_id'];
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
                                    <table class="table-form" width="100%" cellpadding="1">
                                        <tr>
                                            <td colspan="3">Lampiran Surat Direktorat Jenderal Perhubungan Udara</td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Nomor</td>
                                            <td width="1%">:</td>
                                            <td width="84%">' . $result["izin_published_letter"] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>:</td>
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_reject_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 20px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frek</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
        for ($x = $izin; $x < $total_data; $x++) {
            if ($rs_id[$x]["izin_id"] != "") {
                if ($rs_id[$x]["izin_id"] != $temp) {
                    $no = $no + 1;
                }
                $html .= '<tr>';
                if ($rs_id[$x]["izin_id"] != $temp) {
                    if ($rs_id[$x]["pairing"] == "VV") {
                        $html .= '<td align="center" rowspan="2"> ' . $no . '.</td>';
                    } else {
                        $html .= '<td align="center"> ' . $no . '.</td>';
                    }
                }
                $html .= '
                                                <td align="center">' . $rs_id[$x]["rute_all"] . '</td>
                                                <td align="center">' . $rs_id[$x]["aircraft_type"] . '</td>
                                                <td align="center">' . $rs_id[$x]["flight_no"] . '</td>
                                                <td align="center">' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                                                <td align="center">' . substr($rs_id[$x]["eta"], 0, 5) . '</td>
                                                ';

                if ($rs_id[$x]["izin_id"] != $temp) {
                    if ($rs_id[$x]["pairing"] == "VV") {
                        $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                        $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    } else {
                        $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                        $html .= '<td align="center">' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    }
                }

                if ($rs_id[$x]["izin_id"] != $temp) {
                    if ($rs_id[$x]["pairing"] == "VV") {
                        if ($rs_id[$x]["ron"] == 0) {
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                        }
                    } else {
                        $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                    }
                } else if ($rs_id[$x]["ron"] != 0) {
                    $html .= '<td align="center">' . $this->doslibrary->reverse_dos($rs_id[$x]["dos"], $rs_id[$x]["ron"]) . '</td>';
                }
                $html .= '</tr>';
                if ($rs_id[$x]["izin_id"] != $temp) {
                    $temp = $rs_id[$x]["izin_id"];
                }
            }
        }
        $html .= '
                                    </table>
                                ';
        $html .= '
                        </div>
                        <div class="clear"></div>
                    </div>
                    <br />
                </div>
            </body>
        </html>
        ';
        // echo $html;
        // exit;
        $this->tcpdf->setListIndentWidth(4);
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", strtoupper($result["group_nm"])) . "_" . str_replace(" ", "_", strtoupper($result["airlines_nm"]));
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
