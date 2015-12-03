<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class document_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_document');
        // load library
        $this->load->library('tnotification');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index($izin_published_letter = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/form.html");
        // get search parameter
        $search = $this->tsession->userdata('search_izin');
        if (!empty($search)) {
            // search parameters
            $izin_published_letter = empty($search['izin_published_letter']) ? '' : $search['izin_published_letter'];
            // assign
            $this->smarty->assign("search", $search);
        }
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token", $csrf_token);
        $this->smarty->assign("token_nm", $csrf_token_nm);
        $this->smarty->assign("token", $csrf_token);
        // get data
        $result = $this->m_document->get_data_izin_by_id(array($izin_published_letter));
        $this->smarty->assign("result", $result);
        if (empty($result)) {
            redirect('home/welcome/');
        }
        // group
        switch ($result['izin_group']) {
            case '1':
                redirect('information/document_izin/baru/' . $result['registrasi_id']);
                break;
            case '21':
                redirect('information/document_izin/baru/' . $result['registrasi_id']);
                break;
            case '2':
                redirect('information/document_izin/perpanjangan/' . $result['registrasi_id']);
                break;
            case '22':
                redirect('information/document_izin/perpanjangan/' . $result['registrasi_id']);
                break;
            case '3':
                redirect('information/document_izin/penundaan/' . $result['registrasi_id']);
                break;
            case '23':
                redirect('information/document_izin/penundaan/' . $result['registrasi_id']);
                break;
            case '4':
                redirect('information/document_izin/perubahan/' . $result['registrasi_id']);
                break;
            case '24':
                redirect('information/document_izin/perubahan/' . $result['registrasi_id']);
                break;
            case '5':
                redirect('information/document_izin/frekuensi_add/' . $result['registrasi_id']);
                break;
            case '25':
                redirect('information/document_izin/frekuensi_add/' . $result['registrasi_id']);
                break;
            case '6':
                redirect('information/document_izin/frekuensi_delete/' . $result['registrasi_id']);
                break;
            case '26':
                redirect('information/document_izin/frekuensi_delete/' . $result['registrasi_id']);
                break;
            case '7':
                redirect('information/document_izin/penghentian/' . $result['registrasi_id']);
                break;
            case '27':
                redirect('information/document_izin/penghentian/' . $result['registrasi_id']);
                break;
            case '8':
                redirect('information/document_izin/frekuensi/' . $result['registrasi_id']);
                break;
            case '28':
                redirect('information/document_izin/frekuensi/' . $result['registrasi_id']);
                break;
            case '9':
                redirect('information/document_izin/notam/' . $result['registrasi_id']);
                break;
            case '29':
                redirect('information/document_izin/notam/' . $result['registrasi_id']);
                break;
            case '10':
                redirect('information/document_izin/migrasi/' . $result['registrasi_id']);
                break;
            case '30':
                redirect('information/document_izin/migrasi/' . $result['registrasi_id']);
                break;
            default:
                redirect('information/document_izin/index/');
                break;
        }
    }

    // proses pencarian izin
    public function proses_cari() {
        // data
        $token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->tsession->userdata("token");

        if ($this->input->post($token_nm) == $csrf_token) {
            if ($this->input->post('save') == "Reset") {
                $this->tsession->unset_userdata('search_izin');
            } else {
                $params = array(
                    "izin_published_letter" => $this->input->post("izin_published_letter")
                );
                $this->tsession->set_userdata("search_izin", $params);
            }
            // redirect
            redirect("information/document_izin");
        } else {
            redirect("information/document_izin");
        }
    }

    /*
     * BARU
     */

    // view penerbitan
    public function baru($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/baru.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
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
    public function download_baru($registrasi_id = "") {
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri';
        } else {
            $html .= ' Luar Negeri';
        }
        $html .= ';</li>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin rute penerbangan berjadwal 
                                                kepada ' . strtoupper($result["airlines_nm"]) . ' yang berlaku dari tanggal ' . $this->datetimemanipulation->get_full_date($izin_start_date) . ' hingga tanggal ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . ' dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            for ($x = $izin; $x < $per_page; $x++) {
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
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    if ($rs_id[$x]["izin_id"] != $temp) {
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
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
     * PERPANJANGAN
     */

    // perpanjangan
    public function perpanjangan($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/perpanjangan.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
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
    public function download_perpanjangan($registrasi_id = "") {
        // --
        error_reporting(0);
        set_time_limit(0);
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor:' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>';
        }
        $html.= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri';
        } else {
            $html .= ' Luar Negeri';
        }
        $html .= ';</li>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin perpanjangan rute penerbangan berjadwal kepada ' . strtoupper($result["airlines_nm"]) . ' yang berlaku dari tanggal ' . $this->datetimemanipulation->get_full_date($izin_start_date) . ' hingga tanggal ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . ' dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            for ($x = $izin; $x < $per_page; $x++) {
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
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '</td>';
                        } else {
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    if ($rs_id[$x]["izin_id"] != $temp) {
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
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
     * PENUNDAAN
     */

    // view penundaan
    public function penundaan($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/penundaan.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        $this->smarty->assign("izin_penundaan_start", $rs_id[0]['izin_penundaan_start']);
        $this->smarty->assign("izin_penundaan_end", $rs_id[0]['izin_penundaan_end']);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
            $surat_persetujuan[$result['registrasi_id']]['perihal'] = $result['group_nm'];
        }
        $this->smarty->assign("surat_persetujuan", $surat_persetujuan);
        $this->smarty->assign("total_persetujuan", count($surat_persetujuan));
        // get total lampiran
        $total_data = count($rs_id);
        $lampiran = ceil($total_data / 20);
        $this->smarty->assign("lampiran", $lampiran);
        $this->smarty->assign("terbilang", $this->terbilang($lampiran));
        // output
        parent::display();
    }

    // download penerbitan
    public function download_penundaan($registrasi_id = "") {
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        $izin_penundaan_start = $rs_id[0]['izin_penundaan_start'];
        $izin_penundaan_end = $rs_id[0]['izin_penundaan_end'];
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
            $surat_persetujuan[$results['registrasi_id']]['perihal'] = $results['group_nm'];
        }
        // get total lampiran
        $total_data = count($rs_id);
        $per_page = 40;
        $lampiran = ceil($total_data / $per_page);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->terbilang($total_lampiran);
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri,';
        } else {
            $html .= ' Luar Negeri,';
        }
        $html .= '
                                                    </li>
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin penundaan pelaksanaan rute penerbangan berjadwal ' . strtoupper($result["airlines_nm"]) . ' yang berlaku <b>' . $this->datetimemanipulation->get_full_date($izin_penundaan_start) . '</b> hingga <b> ' . $this->datetimemanipulation->get_full_date($izin_penundaan_end) . '</b> dengan rincian sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Selanjutnya, penundaan pelaksanaan operasi penerbangan yang telah disetujui sebagaimana butir 1 (satu) diatas, sampai batas waktu yang telah ditentukan tidak dilaksanakan penerbangan kembali, maka <kapasitas> penerbangan tersebut dicabut.
                                            </li>
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            for ($x = $izin; $x < $per_page; $x++) {
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
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '<br>' . $rs_id[$x]["notes"] . '</td>';
                        } else {
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '<br>' . $rs_id[$x]["notes"] . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    if ($rs_id[$x]["izin_id"] != $temp) {
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
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
     * PENCABUTAN / PENGHENTIAN
     */

    // view penghentian
    public function penghentian($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/penghentian.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
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
    public function download_penghentian($registrasi_id = "") {
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        $izin_penundaan_start = $rs_id[0]['izin_penundaan_start'];
        $izin_penundaan_end = $rs_id[0]['izin_penundaan_end'];
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
            $surat_persetujuan[$results['registrasi_id']]['perihal'] = $results['group_nm'];
        }
        // get total lampiran
        $total_data = count($rs_id);
        $per_page = 40;
        $lampiran = ceil($total_data / $per_page);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->terbilang($total_lampiran);
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri,';
        } else {
            $html .= ' Luar Negeri,';
        }
        $html .= '
                                                    </li>
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara menghentikan pelaksanaan penerbangan berjadwal ' . strtoupper($result["airlines_nm"]) . ' yang berlaku sejak surat ini diterbitkan dengan rincian sebagaimana terlampir.
                                            </li>
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            for ($x = $izin; $x < $per_page; $x++) {
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
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '</td>';
                        } else {
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    if ($rs_id[$x]["izin_id"] != $temp) {
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
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
     * PENAMBAHAN FREKUENSI
     */

    // view penerbitan
    public function frekuensi_add($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/frekuensi_add.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
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
    public function download_frekuensi_add($registrasi_id = "") {
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        $izin_penundaan_start = $rs_id[0]['izin_penundaan_start'];
        $izin_penundaan_end = $rs_id[0]['izin_penundaan_end'];
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri;';
        } else {
            $html .= ' Luar Negeri;';
        }
        $html .= '
                                                    </li>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin penambahan frekuensi penerbangan ' . strtoupper($result["airlines_nm"]) . ' yang berlaku <b>' . $this->datetimemanipulation->get_full_date($izin_start_date) . '</b> hingga <b> ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . '</b> dengan rincian sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut di atas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            for ($x = $izin; $x < $per_page; $x++) {
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
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '</td>';
                        } else {
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    if ($rs_id[$x]["izin_id"] != $temp) {
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
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
     * PENGURANGAN FREKUENSI
     */

    // view penerbitan
    public function frekuensi_delete($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/frekuensi_delete.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("kode_izin", $rs_id[0]['kode_izin']);
        $this->smarty->assign("izin_rute_start", $rs_id[0]['izin_rute_start']);
        $this->smarty->assign("izin_rute_end", $rs_id[0]['izin_rute_end']);
        $this->smarty->assign("izin_start_date", $rs_id[0]['izin_start_date']);
        $this->smarty->assign("izin_expired_date", $rs_id[0]['izin_expired_date']);
        $this->smarty->assign("izin_penundaan_start", $rs_id[0]['izin_penundaan_start']);
        $this->smarty->assign("izin_penundaan_end", $rs_id[0]['izin_penundaan_end']);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
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
    public function download_frekuensi_delete($registrasi_id = "") {
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $kode_izin = $rs_id[0]['kode_izin'];
        $izin_rute_start = $rs_id[0]['izin_rute_start'];
        $izin_rute_end = $rs_id[0]['izin_rute_end'];
        $izin_start_date = $rs_id[0]['izin_start_date'];
        $izin_expired_date = $rs_id[0]['izin_expired_date'];
        $izin_penundaan_start = $rs_id[0]['izin_penundaan_start'];
        $izin_penundaan_end = $rs_id[0]['izin_penundaan_end'];
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
            $surat_persetujuan[$results['registrasi_id']]['perihal'] = $results['group_nm'];
        }
        // get total lampiran
        $total_data = count($rs_id);
        $per_page = 40;
        $lampiran = ceil($total_data / $per_page);
        $total_lampiran = $lampiran + 1;
        $terbilang = $this->terbilang($total_lampiran);
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>;';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri;';
        } else {
            $html .= ' Luar Negeri;';
        }
        $html .= '
                                                    </li>
                                                </ol>
                                                <br />
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin pengurangan frekuensi penerbangan ' . strtoupper($result["airlines_nm"]) . ' yang berlaku <b>' . $this->datetimemanipulation->get_full_date($izin_start_date) . '</b> hingga <b> ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . '</b> dengan rincian sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut di atas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="15%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            for ($x = $izin; $x < $per_page; $x++) {
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
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center" rowspan="2">' . $rs_id[$x]["dos"] . '</td>';
                        } else {
                            $frekuensi = $frekuensi + $rs_id[$x]["frekuensi"];
                            $html .= '<td align="center">' . $rs_id[$x]["dos"] . '</td>';
                        }
                    }
                    $html .= '</tr>';
                    if ($rs_id[$x]["izin_id"] != $temp) {
                        $temp = $rs_id[$x]["izin_id"];
                    }
                }
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
     * PERUBAHAN
     */

    // perubahan
    public function perubahan($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/perubahan.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
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
                $old = $this->m_document->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
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
    public function download_perubahan($registrasi_id = "") {
        // --
        // error_reporting(0);
        set_time_limit(10);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
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
                $old = $this->m_document->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>;';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri;';
        } else {
            $html .= ' Luar Negeri;';
        }
        $html .= '
                                                    </li>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin perubahan jadwal rute penerbangan berjadwal 
                                                kepada ' . strtoupper($result["airlines_nm"]) . ' yang berlaku dari tanggal ' . $this->datetimemanipulation->get_full_date($izin_start_date) . ' hingga tanggal ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . ' 
                                                dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan
                                                dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya
                                                untuk dilakukan pengawasan dalam pelaksanaannya.
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="14%" rowspan="2" align="center">Status Perubahan</td>
                                            <td width="13%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="13%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="10%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="10%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            $kode_frekuensi = "";
            $no = 1;
            for ($x = $izin; $x < $per_page; $x++) {
                if ($rs_id[$x]["izin_id"] != "") {
                    if ($rs_old[$x] != "") {
                        $html .= '<tr>';
                        if ($kode_frekuensi != $rs_id[$x]["kode_frekuensi"]) {
                            if ($rs_old[$x]["pairing"] == "OW") {
                                $html .= '<td align="center" rowspan="2"> ' . $no++ . '.</td>';
                            } else {
                                $html .= '<td align="center" rowspan="4"> ' . $no++ . '.</td>';
                            }
                            $kode_frekuensi = $rs_id[$x]["kode_frekuensi"];
                        }
                        if ($rs_old[$x]["rute_all"] == $rs_id[$x]["rute_all"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["rute_all"] . '.</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["rute_all"] . '.</td>';
                        }
                        $html .= '
                                                    <td align="center">SEMULA</td>
                                                    <td align="center">' . $rs_old[$x]["aircraft_type"] . '</td>
                                                    <td align="center">' . $rs_old[$x]["flight_no"] . '</td>
                                                    <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                    <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                        ';
                        if ($rs_old[$x]["frekuensi"] == $rs_id[$x]["frekuensi"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        }
                        if ($rs_old[$x]["dos"] == $rs_id[$x]["dos"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["dos"] . '</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["dos"] . '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '<tr>';
                    if ($rs_old[$x]["rute_all"] != $rs_id[$x]["rute_all"]) {
                        $html .= '<td align="center"> ' . $rs_id[$x]["rute_all"] . '.</td>';
                    }
                    $html .= '
                        <td align="center">MENJADI</td>
                        <td align="center" ';
                    ($rs_old[$x]["aircraft_type"] != $rs_id[$x]["aircraft_type"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["aircraft_type"] . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["flight_no"] != $rs_id[$x]["flight_no"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["flight_no"] . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["etd"] != $rs_id[$x]["etd"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["eta"] != $rs_id[$x]["eta"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["eta"], 0, 5) . '</td>
                    ';
                    if ($rs_old[$x]["frekuensi"] != $rs_id[$x]["frekuensi"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$x]["frekuensi"] != $rs_id[$x]["frekuensi"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    }
                    if ($rs_old[$x]["dos"] != $rs_id[$x]["dos"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$x]["dos"] != $rs_id[$x]["dos"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["dos"] . '</td>';
                    }
                    $html .= '</tr>';
                }
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
     * FREKUENSI
     */

    // frekuensi
    public function frekuensi($registrasi_id = "") {
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/frekuensi.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
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
                $old = $this->m_document->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
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
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
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
                $old = $this->m_document->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>;';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri;';
        } else {
            $html .= ' Luar Negeri;';
        }
        $html .= '
                                                    </li>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan ' . strtolower($result["group_nm"]) . ' rute penerbangan berjadwal 
                                                kepada ' . strtoupper($result["airlines_nm"]) . ' yang berlaku dari tanggal ' . $this->datetimemanipulation->get_full_date($izin_start_date) . ' hingga tanggal ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . ' 
                                                dengan jadwal penerbangan sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan
                                                dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya
                                                untuk dilakukan pengawasan dalam pelaksanaannya.
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="14%" rowspan="2" align="center">Status Perubahan</td>
                                            <td width="13%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="13%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="10%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="10%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            $kode_frekuensi = "";
            $no = 1;
            for ($x = $izin; $x < $per_page; $x++) {
                if ($rs_id[$x]["izin_id"] != "") {
                    if ($rs_old[$x] != "") {
                        $html .= '<tr>';
                        if ($kode_frekuensi != $rs_id[$x]["kode_frekuensi"]) {
                            if ($rs_old[$x]["pairing"] == "OW") {
                                $html .= '<td align="center" rowspan="2"> ' . $no++ . '.</td>';
                            } else {
                                $html .= '<td align="center" rowspan="4"> ' . $no++ . '.</td>';
                            }
                            $kode_frekuensi = $rs_id[$x]["kode_frekuensi"];
                        }
                        if ($rs_old[$x]["rute_all"] == $rs_id[$x]["rute_all"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["rute_all"] . '.</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["rute_all"] . '.</td>';
                        }
                        $html .= '
                                                    <td align="center">SEMULA</td>
                                                    <td align="center">' . $rs_old[$x]["aircraft_type"] . '</td>
                                                    <td align="center">' . $rs_old[$x]["flight_no"] . '</td>
                                                    <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                    <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                        ';
                        if ($rs_old[$x]["frekuensi"] == $rs_id[$x]["frekuensi"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        }
                        if ($rs_old[$x]["dos"] == $rs_id[$x]["dos"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["dos"] . '</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["dos"] . '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '<tr>';
                    if ($rs_old[$x]["rute_all"] != $rs_id[$x]["rute_all"]) {
                        $html .= '<td align="center"> ' . $rs_id[$x]["rute_all"] . '.</td>';
                    }
                    $html .= '
                        <td align="center">MENJADI</td>
                        <td align="center" ';
                    ($rs_old[$x]["aircraft_type"] != $rs_id[$x]["aircraft_type"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["aircraft_type"] . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["flight_no"] != $rs_id[$x]["flight_no"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["flight_no"] . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["etd"] != $rs_id[$x]["etd"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["eta"] != $rs_id[$x]["eta"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["eta"], 0, 5) . '</td>
                    ';
                    if ($rs_old[$x]["frekuensi"] != $rs_id[$x]["frekuensi"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$x]["frekuensi"] != $rs_id[$x]["frekuensi"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    }
                    if ($rs_old[$x]["dos"] != $rs_id[$x]["dos"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$x]["dos"] != $rs_id[$x]["dos"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["dos"] . '</td>';
                    }
                    $html .= '</tr>';
                }
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
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/notam.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        $this->smarty->assign("km", $km);
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $this->smarty->assign("rs_slot", $rs_slot);
        $this->smarty->assign("total_slot", count($rs_slot));
        // redaksional
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $this->smarty->assign("rs_redaksional", $rs_redaksional);
        $this->smarty->assign("total_redaksional", count($rs_redaksional));
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        $this->smarty->assign("rs_editorial_kepada", $rs_editorial_kepada);
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        $this->smarty->assign("rs_editorial_tembusan", $rs_editorial_tembusan);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
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
                $old = $this->m_document->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        $this->smarty->assign("rs_old", $rs_old);
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $result = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$result['registrasi_id']]['nomor'] = $result['izin_published_letter'];
            $surat_persetujuan[$result['registrasi_id']]['tanggal'] = $result['izin_published_date'];
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
        // --
        // error_reporting(0);
        set_time_limit(10);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        // get preferences
        $km = $this->m_document->get_preferences_by_group_and_name(array('published_izin', 'KM'));
        // list slot
        $rs_slot = $this->m_document->get_list_data_slot_by_id(array($registrasi_id, $result['airlines_id']));
        $total_slot = count($rs_slot);
        // redaksional
        $redaksional = '';
        $no = 1;
        $rs_redaksional = $this->m_document->get_list_redaksional(array($registrasi_id));
        $total_redaksional = count($rs_redaksional);
        foreach ($rs_redaksional as $data) {
            $redaksional .= $data['pref_value'];
            if ($no < $total_redaksional) {
                $redaksional .= ', ';
            }
            $no++;
        }
        // editorial kepada
        $rs_editorial_kepada = $this->m_document->get_list_editorial_kepada(array($registrasi_id));
        // editorial tembusan
        $rs_editorial_tembusan = $this->m_document->get_list_editorial_tembusan(array($registrasi_id));
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('member/published_izin');
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
                $old = $this->m_document->get_list_data_rute_by_kode_frekuensi_old(array($new['kode_frekuensi'], $registrasi_id, $new['published_number'], $new['published_number']));
                $rs_old = array_merge($rs_old, $old);
            }
            $temp = $new['kode_frekuensi'];
        }
        // get surat persetujuan sebelumnya untuk setiap frekuensi
        $surat_persetujuan = array();
        foreach ($rs_id as $data) {
            // cari nomor surat sebelumnya
            $params = array($data['kode_frekuensi'], $registrasi_id, $data['published_number']);
            $results = $this->m_document->get_surat_ijin_by_kode_frekuensi($params);
            $surat_persetujuan[$results['registrasi_id']]['nomor'] = $results['izin_published_letter'];
            $surat_persetujuan[$results['registrasi_id']]['tanggal'] = $results['izin_published_date'];
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
        $url = base_url() . 'index.php/information/published/index/' . $result['registrasi_id'];
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
                                    <td width="35%">Jakarta, ' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                                    <li style="line-height:150%;">Peraturan Menteri Perhubungan Nomor ' . $km["pref_value"] . ' dan peraturan perundang-undangan yang terkait dengan penerbangan;
                                                    </li>';
        foreach ($surat_persetujuan as $data) {
            $html .= '<li style="line-height:150%;">Surat Direktorat Jenderal Perhubungan Udara Nomor: ' . $data["nomor"] . ' tanggal ' . $this->datetimemanipulation->get_full_date($data["tanggal"]) . ' perihal ' . $data["perihal"] . ';</li>;';
        }
        $html .= '
                                                    <li style="line-height:150%;">Surat dari ' . strtoupper($result["airlines_nm"]) . ' Nomor: ' . $result["izin_request_letter"] . ' Tanggal ' . $this->datetimemanipulation->get_full_date($result["izin_request_letter_date"]) . '
                                                        perihal Permohonan ' . $result["group_nm"];
        if ($result["izin_flight"] == "domestik") {
            $html .= ' Dalam Negeri;';
        } else {
            $html .= ' Luar Negeri;';
        }
        $html .= '
                                                    </li>';
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
                                                dengan hormat disampaikan bahwa Direktorat Jenderal Perhubungan Udara dapat memberikan izin perubahan jadwal rute penerbangan berjadwal ' . strtoupper($result["airlines_nm"]) . ' karena NOTAM, yang berlaku ' . $this->datetimemanipulation->get_full_date($izin_start_date) . ' hingga ' . $this->datetimemanipulation->get_full_date($izin_expired_date) . ' sebagaimana terlampir.
                                            </li>
                                            <li style="line-height:150%;">Dalam pelaksanaan penerbangan sebagaimana tersebut diatas, ' . strtoupper($result["airlines_nm"]) . ' diwajibkan mematuhi peraturan
                                                dan ketentuan perundang-undangan yang berlaku tentang keselamatan dan keamanan penerbangan.
                                            </li>
                                            <li style="line-height:150%;">Dimohon kepada ' . $redaksional . ' untuk meneruskan surat persetujuan ini kepada jajaran di lingkungan wilayah kerjanya
                                                untuk dilakukan pengawasan dalam pelaksanaannya.
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
        if ($result["izin_published_role"] == "63") {
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
                                        <b style="text-decoration: underline;">' . $result["published_by"] . '</b>
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
                                    <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
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
                                        <ol>
                                            <li style="line-height:150%;">Direktur Utama ' . strtoupper($result["airlines_nm"]) . '</li>';
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="14%" rowspan="2" align="center">Status Perubahan</td>
                                            <td width="13%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="13%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="10%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
                                            <td width="10%" rowspan="2" align="center">Hari<br /> Operasi</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center">ETD</td>
                                            <td width="10%" align="center">ETA</td>
                                        </tr>';
            $kode_frekuensi = "";
            $no = 1;
            for ($x = $izin; $x < $per_page; $x++) {
                if ($rs_id[$x]["izin_id"] != "") {
                    if ($rs_old[$x] != "") {
                        $html .= '<tr>';
                        if ($kode_frekuensi != $rs_id[$x]["kode_frekuensi"]) {
                            if ($rs_old[$x]["pairing"] == "OW") {
                                $html .= '<td align="center" rowspan="2"> ' . $no++ . '.</td>';
                            } else {
                                $html .= '<td align="center" rowspan="4"> ' . $no++ . '.</td>';
                            }
                            $kode_frekuensi = $rs_id[$x]["kode_frekuensi"];
                        }
                        if ($rs_old[$x]["rute_all"] == $rs_id[$x]["rute_all"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["rute_all"] . '.</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["rute_all"] . '.</td>';
                        }
                        $html .= '
                                                    <td align="center">SEMULA</td>
                                                    <td align="center">' . $rs_old[$x]["aircraft_type"] . '</td>
                                                    <td align="center">' . $rs_old[$x]["flight_no"] . '</td>
                                                    <td align="center">' . substr($rs_old[$x]["etd"], 0, 5) . '</td>
                                                    <td align="center">' . substr($rs_old[$x]["eta"], 0, 5) . '</td> 
                        ';
                        if ($rs_old[$x]["frekuensi"] == $rs_id[$x]["frekuensi"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["frekuensi"] . 'X</td>';
                        }
                        if ($rs_old[$x]["dos"] == $rs_id[$x]["dos"]) {
                            $html .= '<td align="center" rowspan="2"> ' . $rs_old[$x]["dos"] . '</td>';
                        } else {
                            $html .= '<td align="center"> ' . $rs_old[$x]["dos"] . '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '<tr>';
                    if ($rs_old[$x]["rute_all"] != $rs_id[$x]["rute_all"]) {
                        $html .= '<td align="center"> ' . $rs_id[$x]["rute_all"] . '.</td>';
                    }
                    $html .= '
                        <td align="center">MENJADI</td>
                        <td align="center" ';
                    ($rs_old[$x]["aircraft_type"] != $rs_id[$x]["aircraft_type"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["aircraft_type"] . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["flight_no"] != $rs_id[$x]["flight_no"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . $rs_id[$x]["flight_no"] . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["etd"] != $rs_id[$x]["etd"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["etd"], 0, 5) . '</td>
                        <td align="center" ';
                    ($rs_old[$x]["eta"] != $rs_id[$x]["eta"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                    $html .= '>' . substr($rs_id[$x]["eta"], 0, 5) . '</td>
                    ';
                    if ($rs_old[$x]["frekuensi"] != $rs_id[$x]["frekuensi"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$x]["frekuensi"] != $rs_id[$x]["frekuensi"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["frekuensi"] . 'X</td>';
                    }
                    if ($rs_old[$x]["dos"] != $rs_id[$x]["dos"]) {
                        $html .= '<td align="center" ';
                        ($rs_old[$x]["dos"] != $rs_id[$x]["dos"]) ? $html .= 'style="color: red; font-weight: bold;"' : $html .= '';
                        $html .= '>' . $rs_id[$x]["dos"] . '</td>';
                    }
                    $html .= '</tr>';
                }
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
        // set template content
        $this->smarty->assign("template_content", "information/document_izin/migrasi.html");
        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('member/published_izin');
        }
        $this->smarty->assign("result", $result);
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('information/document_izin');
        }
        $this->smarty->assign("rs_id", $rs_id);
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
        // --
        error_reporting(0);
        set_time_limit(0);

        // get detail data
        $params = array($registrasi_id, $result['airlines_id']);
        $result = $this->m_document->get_published_izin_by_registrasi($params);
        if (empty($result)) {
            redirect('information/document_izin');
        }
        // get list frekuensi
        $params = array($registrasi_id, $result['airlines_id']);
        $rs_id = $this->m_document->get_izin_rute_data_by_kode_izin($params);
        if (empty($rs_id)) {
            redirect('information/document_izin');
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
                                            <td>' . $this->datetimemanipulation->get_full_date($result["izin_published_date"]) . '</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <p class="caption">
                                        <b>' . strtoupper($result["group_nm"]) . '</b>
                                        <br />
                                        <b>' . strtoupper($result["airlines_nm"]) . '</b>
                                        <br />
                                        <b> ' . strtoupper($this->datetimemanipulation->get_full_date($izin_start_date)) . ' hingga ' . strtoupper($this->datetimemanipulation->get_full_date($izin_expired_date)) . ' </b>
                                    </p>
                                    <br />
                                    <table class="table-view" width="100%" style="font-size: 25px; font-family: times; color: #333;" border="1" cellpadding="2">
                                        <tr>
                                            <td width="5%" rowspan="2" align="center">No</td>
                                            <td width="15%" rowspan="2" align="center">Rute<br />Penerbangan</td>
                                            <td width="15%" rowspan="2" align="center">Tipe<br />Pesawat</td>
                                            <td width="15%" rowspan="2" align="center">Nomor<br />Penerbangan</td>
                                            <td width="20%" colspan="2" align="center">Jadwal Penerbangan <br />(Waktu Lokal)</td>
                                            <td width="15%" rowspan="2" align="center">Frekuensi /<br /> Minggu</td>
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
