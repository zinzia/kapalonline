<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class document extends ApplicationBase {

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
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/document/form.html");
        // get search parameter
        $search = $this->tsession->userdata('search_fa');
        // search parameters
        $published_no = empty($search['published_no']) ? '' : $search['published_no'];
        // assign
        $this->smarty->assign("search", $search);
        // get data
        $result = $this->m_document->get_detail_data_by_id(array($published_no));
        $this->smarty->assign("result", $result);
        // get remark field
        $remark_field = $this->m_document->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        $this->smarty->assign("remark_field", $remark_field);
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'document_%'));
        //notification
        $this->tnotification->display_notification();
        //commented due security issue
        $this->tnotification->display_last_field();
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token", $csrf_token);
        $this->smarty->assign("token_nm", $csrf_token_nm);
        $this->smarty->assign("token", $csrf_token);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // data
        $token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->tsession->userdata("token");

        if ($this->input->post($token_nm) == $csrf_token) {
            if ($this->input->post('save') == "Reset") {
                $this->tsession->unset_userdata('search_fa');
            } else {
                $params = array(
                    "published_no" => $this->input->post("published_no")
                );
                $this->tsession->set_userdata("search_fa", $params);
            }
            // redirect
            redirect("information/document");
        } else {
            redirect("information/document");
        }
    }

    // download pdf
    public function download_pdf($data_id) {
        // load library
        $this->load->library('tcpdf');

        // get detail data
        $params = array(str_replace("-", "/", $data_id));
        $result = $this->m_document->get_detail_data_by_id($params);

        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);

        // add a page
        $this->tcpdf->AddPage();

        $html = '';
        $no = 1;

        // content
        $html .= '
            <style type="text/css">
            .table-form {
                margin: 0;
                padding: 0;
                background-color: #fff;
                border-collapse: collapse;
                text-align: left;
                font-family: tahoma;
                font-size: 20px;
            }

            .table-form td {
                margin: 0;
                background-color: #FFFFFF;
                vertical-align: middle;
                font-size: 20px;
            }
            </style>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="40%">
                        <span style="text-decoration: underline;">Kementerian Perhubungan Republik Indonesia</span><br />
                        <i>Ministry of Transportation Of the Republic of Indonesia</i>
                    </td>
                    <td rowspan="3" width="25%" align="center"><br><br><br><br><b style="font-size:30px;">FLIGHT APPROVAL</b></td>
                    <td rowspan="3" width="35%" align="center">
                        <br><br><br>
                        <b>' . strtoupper($result['data_type']) . ' ' . strtoupper($result['data_flight']) . '</b>
                        <br><br>
                        <b>' . strtoupper($result['document_no']) . '</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="text-decoration: underline;">Direktorat Jenderal Perhubungan Udara </span><br />
                        <i>Directorate General of Civil Aviation</i>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="text-decoration: underline;">Persetujuan terbang untuk wilayah Indonesia</span><br />
                        <i>Flight Approval for Indonesia territory</i>
                    </td>
                </tr>
            </table>
            <br>
        ';
        if ($result['data_flight'] == 'domestik') {
            $html .= '
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="3%">1.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Pesawat Udara</span> <br /><i>Aircraft</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Operator (Pemilik / Penyewa)</span><br /><i>Operator (Owner / Charterer)</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["airlines_nm"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Tipe</span><br /><i>Type</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["aircraft_type"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanda Pendaftaran dan Nama Panggilan</span><br /><i>Registrations and Call Signs</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_no"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">2.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Penerbangan </span> <br /><i>Flight</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Tanggal dan Jam</span><br /><i>Date and Time</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["date_start"] . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $result["date_end"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Rute</span><br /><i>Routes</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["rute_all"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Teknis di</span><br /><i>Technical Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["technical_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>d)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Niaga di</span><br /><i>Commercial Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["niaga_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">3.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Jumlah orang dalam Pesawat udara</span> <br /><i>Total number of person on board</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["flight_pilot"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Awak pesawat udara lainnya</span> *1)<br /><i>Other crew members</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_crew"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Penumpang/ Barang </span> *2)<br /><i>Passengers / Cargo</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_goods"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">4.</td>
                    <td colspan="2"><span style="text-decoration: underline;">Keterangan</span> <br /><i>Remark</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["remark"] . '
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="3">
                        <span style="text-decoration: underline;">Berlaku untuk 1 (satu) kali penerbangan</span>
                        <br />
                        <i>Valid for one flight </i>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="3" style="vertical-align:top; padding-left:40px;">';
            if ($result["catatan"] != "") {
                $html .= $result["catatan"] . '<br /><br />';
            }
            if ($result["remark_final"] != "") {
                $html .= $result["remark_final"] . '<br /><br />';
            }
            $html .= '
                        <table style="margin-left:-10px;">';
            foreach ($remark_field as $value) {
                $html .= '<tr>
                            <td width="35%">' . $value["rules_name"] . '</td>
                            <td width="65%">' . $result[$value["rules_field"]] . '</td>
                            </tr>';
            }
            $html .= '</table>
                    </td>
                    <td rowspan="3" width="10%">
                        <span style="text-decoration: underline;">Pemohon</span><br /><i>Applicant</i>
                    </td>
                    <td width="10%"> 
                        <span style="text-decoration: underline;">Tandatangan </span><br /><i>Signature</i>
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td>
                        ' . $result["applicant"] . '
                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Penunjukan  </span><br /><i>Designation</i>
                    </td>
                    <td>
                        ' . $result["designation"] . '
                    </td>
                </tr>
            </table>
            <br>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="50%" align="justify">
                        Nota :<br />
                        *1) dan *2) Nama-nama supaya dicantumkan / dilampirkan<br />
                        Pesawat udara, awak pesawat udara, penumpang, dan muatan berdasarkan pada istilah dari Konvensi Chicago serta mentaati peraturan-peraturan Indonesia mengenai penerbangan ini. Memiliki persetujuan terbang ini tidak membebaskan operator dari melaksanakan setiap aturan operasi teknis atau persyaratan kelaikan udara dari Direktorat Jenderal Perhubungan Udara. Persetujuan terbang ini dapat dicabut tanpa pemberitahuan terlebih dahulu. Apabila terjadi keterlambatan pada tanggal tersebut dalam butir 2a) diatas, maka penerbangan dianggap batal.    
                    </td>
                    <td width="2%"></td>
                    <td width="50%" align="justify">
                        Notes :<br />
                        *1) and *2) Names should be written/attached <br />
                        Aircraft, crew, passengers and load are subject to the terms of Chicago Convention and have to comply with the Indonesian Regulations, concerning this flight. Posession of this flight approval does not exempt an operator from compliance with any of the technical operations ruler or airworthines requirement of the Directorate General of Civil Aviation. This Flight approval can be withdrawn without previous notice. Should delay exceed the date as prescribed in point 2a) above this flight will be regarded as cancelled.
                    </td>
                </tr>
            </table>
        ';
        } else {
            $html .= '
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="3%">1.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Pesawat Udara</span> <br /><i>Aircraft</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Operator (Pemilik / Penyewa)</span><br /><i>Operator (Owner / Charterer)</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["airlines_nm"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Tipe</span><br /><i>Type</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["aircraft_type"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanda Pendaftaran dan Nama Panggilan</span><br /><i>Registrations and Call Signs</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_no"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">2.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Penerbangan </span> <br /><i>Flight</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="28%"><span style="text-decoration: underline;">Rute</span><br /><i>Routes</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["rute_all"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>b)</td>
                    <td><span style="text-decoration: underline;">Tanggal Masuk Indonesia</span><br /><i>Date Entering Indonesia</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["date_start"] . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $result["date_start_upto"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanggal Keluar Indonesia</span><br /><i>Date Leaving Indonesia</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["date_end"] . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $result["date_end_upto"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>d)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Teknis di</span><br /><i>Technical Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["technical_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>e)</td>
                    <td><span style="text-decoration: underline;">Pendaratan Niaga di</span><br /><i>Commercial Landing at</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["niaga_landing"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>f)</td>
                    <td><span style="text-decoration: underline;">Sifat / Tujuan Penerbangan</span><br /><i>Purpose Of The Flight</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_purpose"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">g)</td>
                    <td width="28%"><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
                    <td width="1%">:</td>
                    <td width="65%" colspan="3">
                        ' . $result["flight_pilot"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>h)</td>
                    <td><span style="text-decoration: underline;">Awak pesawat udara lainnya</span> *1)<br /><i>Other crew members</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_crew"] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>i)</td>
                    <td><span style="text-decoration: underline;">Penumpang/ Barang </span> *2)<br /><i>Passengers / Cargo</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["flight_goods"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">3.</td>
                    <td colspan="2"><span style="text-decoration: underline;">Keterangan</span> <br /><i>Remark</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $result["remark"] . '
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="3">
                        <span style="text-decoration: underline;">Berlaku untuk 1 (satu) kali penerbangan</span>
                        <br />
                        <i>Valid for one flight </i>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="3" style="vertical-align:top; padding-left:40px;">';
            if ($result["catatan"] != "") {
                $html .= $result["catatan"] . '<br /><br />';
            }
            if ($result["remark_final"] != "") {
                $html .= $result["remark_final"] . '<br /><br />';
            }
            $html .= '
                        <table style="margin-left:-10px;">';
            foreach ($remark_field as $value) {
                $html .= '<tr>
                            <td width="35%">' . $value["rules_name"] . '</td>
                            <td width="65%">' . $result[$value["rules_field"]] . '</td>
                            </tr>';
            }
            $html .= '</table>
                    </td>
                    <td rowspan="3" width="10%">
                        <span style="text-decoration: underline;">Pemohon</span><br /><i>Applicant</i>
                    </td>
                    <td width="10%"> 
                        <span style="text-decoration: underline;">Tandatangan </span><br /><i>Signature</i>
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td>
                        ' . $result["applicant"] . '
                    </td>
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Penunjukan  </span><br /><i>Designation</i>
                    </td>
                    <td>
                        ' . $result["designation"] . '
                    </td>
                </tr>
            </table>
            <br>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td width="49%" align="justify">
                        Nota :<br />
                        *1) dan *2) Nama-nama supaya dicantumkan / dilampirkan<br />
                        Pesawat udara, awak pesawat udara, penumpang, dan muatan berdasarkan pada istilah dari Konvensi Chicago serta mentaati peraturan-peraturan Indonesia mengenai penerbangan ini. Memiliki persetujuan terbang ini tidak membebaskan operator dari melaksanakan setiap aturan operasi teknis atau persyaratan kelaikan udara dari Direktorat Jenderal Perhubungan Udara. Persetujuan terbang ini dapat dicabut tanpa pemberitahuan terlebih dahulu. Apabila terjadi keterlambatan pada tanggal tersebut dalam butir 2a) diatas, maka penerbangan dianggap batal.    
                    </td>
                    <td width="2%"></td>
                    <td width="49%" align="justify">
                        Notes :<br />
                        *1) and *2) Names should be written/attached <br />
                        Aircraft, crew, passengers and load are subject to the terms of Chicago Convention and have to comply with the Indonesian Regulations, concerning this flight. Posession of this flight approval does not exempt an operator from compliance with any of the technical operations ruler or airworthines requirement of the Directorate General of Civil Aviation. This Flight approval can be withdrawn without previous notice. Should delay exceed the date as prescribed in point 2a) above this flight will be regarded as cancelled.
                    </td>
                </tr>
            </table>
        ';
        }
        $html .= '
            <br>
            <table class="table-form" width="100%" cellpadding="1">
                <tr>
                    <td colspan="2">
                        <span style="text-decoration: underline;">Penerbangan tidak berjadwal tersebut di atas telah diizinkan oleh Pemerintah Republik Indonesia   </span><br /><i>The above mentioned non scheduled flight has been approved by the Goverment of the Republic of Indonesia</i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        A.n. Direktur Jenderal Perhubungan Udara
                    </td>
                </tr>
                <tr>
                    <td width="15%">Nomor Izin</td>
                    <td width="85%">: ' . $result['published_no'] . '</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: ' . $result['published_date'] . '</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>: ' . $result['operator_name'] . '</td>
                </tr>
                <tr>
                    <td>Tanda Tangan</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: ' . $result['jabatan'] . '</td>
                </tr>
            </table>
            <br>
            <table width="100%" border="0" cellpadding="0">
                <tr>
                    <td width="100%"><tcpdf method="write2DBarcode" params="' . $params_barcode . '" /></td>
                </tr>
            </table>
        ';

        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $result['published_no'];
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
    }

}
