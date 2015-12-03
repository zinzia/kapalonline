<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class published extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_member');
        $this->load->model('m_published');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/published/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get tahun
        $this->smarty->assign("rs_tahun", $this->m_member->get_list_tahun_report($this->com_user['airlines_id']));
        // bulan
        $bulan = array(
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
        $this->smarty->assign("rs_bulan", $bulan);
        // get search parameter
        $search = $this->tsession->userdata('search_published');
        // search parameters
        $tanggal_from = empty($search['tanggal_from']) ? date("Y-m-d") : $search['tanggal_from'];
        $tanggal_to = empty($search['tanggal_to']) ? date("Y-m-d") : $search['tanggal_to'];
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_type = empty($search['data_type']) ? '%' : '%' . $search['data_type'] . '%';
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        $payment_st = empty($search['payment_st']) ? '%' : '%' . $search['payment_st'] . '%';
        $search['tanggal_from'] = $tanggal_from;
        $search['tanggal_to'] = $tanggal_to;
        // assign
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/published/index/");
        $config['total_rows'] = $this->m_member->get_total_finished_task(array($this->com_user['airlines_id'], $tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $published_no, $data_type, $data_flight, $payment_st));
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
        $params = array($this->com_user['airlines_id'], $tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $published_no, $data_type, $data_flight, $payment_st, ($start - 1), $config['per_page']);
        $rs_id = $this->m_member->get_list_finished_task($params);
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
            $this->tsession->unset_userdata('search_published');
        } else {
            $params = array(
                "tanggal_from" => $this->input->post("tanggal_from"),
                "tanggal_to" => $this->input->post("tanggal_to"),
                "published_no" => $this->input->post("published_no"),
                "data_type" => $this->input->post("data_type"),
                "data_flight" => $this->input->post("data_flight"),
                "payment_st" => $this->input->post("payment_st"),
            );
            $this->tsession->set_userdata("search_published", $params);
        }
        // redirect
        redirect("member/published");
    }

    // laporan project
    public function print_out() {
        //set page rule
        $this->_set_page_rule("R");
        // get search parameter
        $search = $this->tsession->userdata('search_published');
        // search parameters
        $tanggal_from = empty($search['tanggal_from']) ? date("Y-m-d") : $search['tanggal_from'];
        $tanggal_to = empty($search['tanggal_to']) ? date("Y-m-d") : $search['tanggal_to'];
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_type = empty($search['data_type']) ? '%' : '%' . $search['data_type'] . '%';
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        $payment_st = empty($search['payment_st']) ? '%' : '%' . $search['payment_st'] . '%';
        // get list
        $params = array($this->com_user['airlines_id'], $tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $published_no, $data_type, $data_flight, $payment_st);
        $rs_id = $this->m_member->get_list_finished_task_print($params);
        if (empty($rs_id)) {
            // default redirect
            redirect("member/published");
        }
        // excel download
        // load excel
        $this->load->library('phpexcel');
        // create excell
        $filepath = "resource/doc/template/TEMPLATE_REPORT_FA_AIRLINES.xlsx";
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $this->phpexcel = $objReader->load($filepath);
        $objWorksheet = $this->phpexcel->setActiveSheetIndex(0);

        $styleBorder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
        ));
        /*
         * SET DATA EXCELL
         */
        $objWorksheet->setCellValue('H3', date('d-m-Y'));
        // list project
        $no = 1;
        $i = 6;
        foreach ($rs_id as $data) {
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['document_no']);
            $objWorksheet->setCellValue('D' . $i, $data['published_no']);
            $objWorksheet->setCellValue('E' . $i, $data['aircraft_type']);
            $objWorksheet->setCellValue('F' . $i, strtoupper($data['data_type']) . ' ' . strtoupper($data['data_flight']));
            $objWorksheet->setCellValue('G' . $i, $data['date_start'] . ' - ' . $data['date_end']);
            $objWorksheet->setCellValue('H' . $i, $data['registration']);
            $objWorksheet->setCellValue('I' . $i, $data['flight_no']);
            $objWorksheet->setCellValue('J' . $i, strtoupper($data['rute_all']));
            $objWorksheet->setCellValue('K' . $i, $data['payment_st']);
            // style
            $objWorksheet->getStyle('B' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('C' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('D' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('E' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('F' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('G' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('H' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('I' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('J' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('K' . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }
 
        // file_name
        $file_name = "LAPORAN_FLIGHT_APPROVAL";
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
        exit();
    }

    // barcode
    public function barcode($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // get detail message
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        if (empty($result)) {
            // notification
            $this->tnotification->sent_notification("error", "Data not found!");
            redirect('member/published');
        }
        // create pdf
        $this->download($data_id);
        // load library
        $this->load->library('tcpdf');
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
        // print
        // page setting
        $this->tcpdf->SetPrintHeader(false);
        $this->tcpdf->SetPrintFooter(false);
        $this->tcpdf->SetDisplayMode('real');
        $this->tcpdf->SetMargins(5, 5, 5, false);
        $this->tcpdf->AddPage('P', 'A4');
        $this->tcpdf->SetFont('helvetica', '', 8, '', 'false');
        // html
        $html = '';
        $html .= '<table width="100%" border="0" cellpadding="0">';
        $html .= '<tr>';
        $html .= '<td width="100%"><tcpdf method="write2DBarcode" params="' . $params_barcode . '" /></td>';
        $html .= '</tr>';
        $html .= '</table>';
        // write
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // download output
        $this->tcpdf->Output($result['published_no'] . '.pdf', 'D');
    }

    // download
    public function download($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // load library
        $this->load->library('tcpdf');
        // --
        error_reporting(0);
        set_time_limit(0);
        // get detail message
        $result = $this->m_member->get_detail_data_by_id(array($data_id, $this->com_user['airlines_id']));
        $result_rute = $this->m_member->get_data_rute_by_id(array($data_id));
        $total_rute = COUNT($result_rute);
        $no = 1;
        if (empty($result)) {
            // notification
            $this->tnotification->sent_notification("error", "Data not found!");
            redirect('member/published');
        }
        // get remark field
        $remark_field = $this->m_member->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd']));
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
        $params_barcode = $this->tcpdf->serializeTCPDFtagParameters(array($url, 'QRCODE,H', '', '', 25, 25, $style, 'N'));
        // unset tcpdf
        unset($this->tcpdf);
        // load library
        $this->load->library('tcpdf');

        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);

        // add a page
        $this->tcpdf->AddPage();

        $html = '';
        $no = 1;

        // explode rute
        $rute = explode("-", $result["rute_all"]);

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
                    <td width="40%"><span style="text-decoration: underline;">Kementerian Perhubungan Republik Indonesia</span><br />
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
                    <td><span style="text-decoration: underline;">Direktorat Jenderal Perhubungan Udara </span><br />
                        <i>Directorate General of Civil Aviation</i>
                    </td>
                </tr>
                <tr>
                    <td><span style="text-decoration: underline;">Persetujuan terbang untuk wilayah Indonesia</span><br />
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
                        ' . $result["registration"] . ' / ' . $result["call_sign"] . ' ' . $result["flight_no"] . '-' . $result["flight_no_2"] . '
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
                        ' . $this->datetimemanipulation->get_full_date($result["date_start"]) . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $this->datetimemanipulation->get_full_date($result["date_end"]) . '
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
                    <td colspan="3"><span style="text-decoration: underline;">Berlaku untuk 1 (satu) kali penerbangan</span>
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
                            <td width="65%">';
                            if ($value["rules_type"] == "area") {
                                $html .= $result[$value["rules_field"]];
                            } else {
                                if ($value["rules_field"] == "etd") {
                                    $html .= $rute[0] . " : " . $result[$value["rules_field"]];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_utc"];
                                    }
                                    $html .= '<br />';
                                    $html .= $rute[1] . " : " . $result[$value["rules_field"] . "_2"];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_2_utc"];
                                    }
                                } else if ($value["rules_field"] == "eta"){
                                    $html .= $rute[1] . " : " . $result[$value["rules_field"]];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_utc"];
                                    }
                                    $html .= '<br />';
                                    $html .= $rute[0] . " : " . $result[$value["rules_field"] . "_2"];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_2_utc"];
                                    }
                                } else {
                                    $html .= $rute[1] . " : " . $result[$value["rules_field"] . "_2"];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_2_utc"];
                                    }
                                    $html .= '<br />';
                                    $html .= $rute[0] . " : " . $result[$value["rules_field"]];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_utc"];
                                    }
                                }
                            }
                            $html .= '</td>
                            </tr>';
                        }
                $html .= '</table>
                    </td>
                    <td rowspan="3" width="10%"><span style="text-decoration: underline;">Pemohon</span><br /><i>Applicant</i>
                    </td>
                    <td width="10%"><span style="text-decoration: underline;">Tandatangan </span><br /><i>Signature</i>
                    </td>
                </tr>
                <tr>
                    <td><span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td width="70%">
                        ' . $result["applicant"] . '
                    </td>
                </tr>
                <tr>
                    <td><span style="text-decoration: underline;">Penunjukan  </span><br /><i>Designation</i>
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
                        ' . $result["registration"] . ' / ' . $result["call_sign"] . ' ' . $result["flight_no"] . '-' . $result["flight_no_2"] . '
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
                        ' . $this->datetimemanipulation->get_full_date($result["date_start"]) . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $this->datetimemanipulation->get_full_date($result["date_start_upto"]) . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>c)</td>
                    <td><span style="text-decoration: underline;">Tanggal Keluar Indonesia</span><br /><i>Date Leaving Indonesia</i></td>
                    <td>:</td>
                    <td colspan="3">
                        ' . $this->datetimemanipulation->get_full_date($result["date_end"]) . '
                        &nbsp;&nbsp;s/d&nbsp;&nbsp;
                        ' . $this->datetimemanipulation->get_full_date($result["date_end_upto"]) . '
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
                    <td colspan="3"><span style="text-decoration: underline;">Berlaku untuk 1 (satu) kali penerbangan</span>
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
                            <td width="65%">';
                            if ($value["rules_type"] == "area") {
                                $html .= $result[$value["rules_field"]];
                            } else {
                                if ($value["rules_field"] == "etd") {
                                    $html .= $rute[0] . " : " . $result[$value["rules_field"]];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_utc"];
                                    }
                                    $html .= '<br />';
                                    $html .= $rute[1] . " : " . $result[$value["rules_field"] . "_2"];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_2_utc"];
                                    }
                                } else if ($value["rules_field"] == "eta"){
                                    $html .= $rute[1] . " : " . $result[$value["rules_field"]];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_utc"];
                                    }
                                    $html .= '<br />';
                                    $html .= $rute[0] . " : " . $result[$value["rules_field"] . "_2"];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_2_utc"];
                                    }
                                } else {
                                    $html .= $rute[1] . " : " . $result[$value["rules_field"] . "_2"];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_2_utc"];
                                    }
                                    $html .= '<br />';
                                    $html .= $rute[0] . " : " . $result[$value["rules_field"]];
                                    if ($value["rules_class"] == "waktu") {
                                        $html .= ', UTC ' . $result[$value["rules_field"] . "_utc"];
                                    }
                                }
                            }
                            $html .= '</td>
                            </tr>';
                        }
                $html .= '</table>
                    </td>
                    <td rowspan="3" width="10%"><span style="text-decoration: underline;">Pemohon</span><br /><i>Applicant</i>
                    </td>
                    <td width="10%"><span style="text-decoration: underline;">Tandatangan </span><br /><i>Signature</i>
                    </td>
                </tr>
                <tr>
                    <td><span style="text-decoration: underline;">Nama  </span><br /><i>Name</i>
                    </td>
                    <td width="70%">
                        ' . $result["applicant"] . '
                    </td>
                </tr>
                <tr>
                    <td><span style="text-decoration: underline;">Penunjukan  </span><br /><i>Designation</i>
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
            <table class="table-form" width="100%" cellpadding="5">
                <tr>
                    <td colspan="3"><span style="text-decoration: underline;">Penerbangan tidak berjadwal tersebut di atas telah diizinkan oleh Pemerintah Republik Indonesia   </span><br /><i>The above mentioned non scheduled flight has been approved by the Goverment of the Republic of Indonesia</i>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">A.n. Direktur Jenderal Perhubungan Udara</td>
                </tr>
                <tr>
                    <td width="15%">Nomor Izin</td>
                    <td width="35%">: ' . $result['published_no'] . '</td>
                    <td width="50%" rowspan="5"><tcpdf method="write2DBarcode" params="' . $params_barcode . '" /></td>
                </tr>
                <tr>
                    <td width="15%">Tanggal</td>
                    <td width="85%">: ' . $this->datetimemanipulation->get_full_date($result['published_date']) . '</td>
                </tr>
                <tr>
                    <td width="15%">Nama</td>
                    <td width="85%">: ' . $result['operator_name'] . '</td>
                </tr>
                <tr>
                    <td>Tanda Tangan</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td width="15%">Jabatan</td>
                    <td width="85%">: ' . $result['jabatan'] . '</td>
                </tr>
            </table>
        ';

        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $result['published_no'];
        $this->tcpdf->Output(str_replace("/", "-", $filename) . ".pdf", 'D');
        // $this->tcpdf->Output($filename.".pdf", 'D');
    }

}
