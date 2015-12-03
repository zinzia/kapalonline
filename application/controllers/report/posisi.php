<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class posisi extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_report_posisi');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "report/posisi/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_report_posisi');
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        }
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
        // tahun
        $default = date('Y');
        $tahun = array();
        for ($i = $default - 4; $i <= $default; $i++) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // search parameters
        $role_id = empty($search['role_id']) ? '%' : $search['role_id'];
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("report/posisi/index/");
        $config['total_rows'] = $this->m_report_posisi->get_total_report(array($role_id));
        $config['uri_segment'] = 4;
        $config['per_page'] = 100;
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
        $params = array($role_id, ($start - 1), $config['per_page']);
        $rs_id = $this->m_report_posisi->get_list_report($params);
        foreach ($rs_id as $value) {
            // get detail rute
            $rs_rute = $this->m_report_posisi->get_data_rute_by_id(array($value['data_id']));
            $total_rute = COUNT($rs_rute);
            $list_rute = "";
            $x = 1;
            foreach ($rs_rute as $rute) {
                $x++;
                $list_rute .= $rute['airport_iata_cd'];
                if ($x <= $total_rute) {
                    $list_rute .= "-";
                }
            }
            $data[] = array(
                "data_id"       => $value['data_id'],
                "document_no"   => $value['document_no'],
                "published_no"  => $value['published_no'],
                "airlines_nm"   => $value['airlines_nm'],
                "data_type"     => $value['data_type'],
                "data_flight"   => $value['data_flight'],
                "rute_all"      => $list_rute,
                "date_start"    => $value['date_start'],
                "date_end"      => $value['date_end'],
            );
        }
        $this->smarty->assign("rs_id", $rs_id);
        // assign
        $rs_role = $this->m_report_posisi->get_role_user();
        $this->smarty->assign("rs_role", $rs_role);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_report_posisi');
        } else {
            $params = array(
                "role_id" => $this->input->post("role_id"),
            );
            $this->tsession->set_userdata("search_report_posisi", $params);
        }
        // redirect
        redirect("report/posisi");
    }

    // form
    public function form($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "report/posisi/form.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get detail data
        $params = array($data_id);
        $result = $this->m_report_posisi->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('task/registration_fa');
        }
        // assign
        $this->smarty->assign("result", $result);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_report_posisi->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // download
    public function download() {
        // set page rules
        $this->_set_page_rule("R");
        //load library
        $this->load->library('phpexcel');
        // --
        error_reporting(0);
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        // get search parameter
        $search = $this->tsession->userdata('search_report_nb');
        // search parameters
        $bulan = empty($search['bulan']) ? '%' : $search['bulan'];
        $tahun = empty($search['tahun']) ? '%' : $search['tahun'];
        $data_type = empty($search['data_type']) ? '%' : $search['data_type'];
        $data_flight = empty($search['data_flight']) ? '%' : $search['data_flight'];

        // get list
        $params = array($bulan, $tahun, $data_type, $data_flight, 1, 2);
        $rs_data = $this->m_report_posisi->get_all_data_report($params);

        // get data
        // create excel
        $filepath = "resource/doc/template/TEMPLATE_REPORT_FA.xlsx";
        // ---
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $this->phpexcel = $objReader->load($filepath);
        // set active sheet
        $objWorksheet = $this->phpexcel->setActiveSheetIndex(0);

        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
        ));

        $sheet_name = strtoupper('SHEET');
        $objWorksheet->setTitle($sheet_name);

        // keterangan lain
        $objWorksheet->setCellValue('H3', $this->datetimemanipulation->get_full_date(date('Y-m-d')));

        $no = 1;
        $i = 6;
        $kolom = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        foreach ($rs_data as $data) {
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['document_no']);
            $objWorksheet->setCellValue('D' . $i, $data['published_no']);
            $objWorksheet->setCellValue('E' . $i, $data['airlines_nm']);
            $objWorksheet->setCellValue('F' . $i, $data['aircraft_type']);
            $objWorksheet->setCellValue('G' . $i, $data['flight_no']);
            $objWorksheet->setCellValue('H' . $i, $data['rute_all']);
            $objWorksheet->setCellValue('I' . $i, $data['services_nm']);

            // style
            $objWorksheet->getStyle($kolom[0] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[1] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[2] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[3] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[4] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[5] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[6] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[7] . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }

        // output file
        $file_name = 'REKAPITULASI_FLIGHT_APPROVAL_NIAGA_BERJADWAL';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

    // download detail
    public function download_detail($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // load library
        $this->load->library('tcpdf');

        // get detail data
        $params = array($data_id);
        $result = $this->m_report_posisi->get_detail_data_by_id($params);
        // get remark field
        $remark_field = $this->m_report_posisi->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
        // create new PDF document
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
        $url = base_url() . 'index.php/information/published/index/' . $result['document_no'];
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 15, 15, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);

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
                    <td width="38%"><span style="text-decoration: underline;">Operator (Pemilik / Penyewa)</span><br /><i>Operator (Owner / Charterer)</i></td>
                    <td width="1%">:</td>
                    <td width="55%" colspan="3">
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
                        ' . $result["flight_no"] . ' / ' . $result["registration"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">2.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Penerbangan </span> <br /><i>Flight</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="38%"><span style="text-decoration: underline;">Tanggal dan Jam</span><br /><i>Date and Time</i></td>
                    <td width="1%">:</td>
                    <td width="55%" colspan="3">
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
                    <td width="38%"><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
                    <td width="1%">:</td>
                    <td width="55%" colspan="3">
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
                        ' . strtoupper($result["services_nm"]) . '
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
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td width="70%">
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
                    <td width="38%"><span style="text-decoration: underline;">Operator (Pemilik / Penyewa)</span><br /><i>Operator (Owner / Charterer)</i></td>
                    <td width="1%">:</td>
                    <td width="55%" colspan="3">
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
                        ' . $result["flight_no"] . ' / ' . $result["registration"] . '
                    </td>
                </tr>
                <tr>
                    <td width="3%">2.</td>
                    <td width="97%" colspan="6"><span style="text-decoration: underline;">Penerbangan </span> <br /><i>Flight</i></td>
                </tr>
                <tr>
                    <td></td>
                    <td width="3%">a)</td>
                    <td width="38%"><span style="text-decoration: underline;">Rute</span><br /><i>Routes</i></td>
                    <td width="1%">:</td>
                    <td width="55%" colspan="3">
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
                    <td width="38%"><span style="text-decoration: underline;">Nama Pilot </span><br /><i>Name of Pilot in Command</i></td>
                    <td width="1%">:</td>
                    <td width="55%" colspan="3">
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
                        ' . strtoupper($result["services_nm"]) . '
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
                </tr>
                <tr>
                    <td> 
                        <span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td width="70%">
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
                    <td width="35%">: ' . $result['published_no'] . '</td>
                    <td width="50%" rowspan="5"><tcpdf method="write2DBarcode" params="' . $params_barcode . '" /></td>
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
        ';

        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace(" ", "_", $result['airlines_nm']) . '_' . str_replace("/", "-", $result['published_no']);
        $this->tcpdf->Output($filename.".pdf", 'D');
    }

}
