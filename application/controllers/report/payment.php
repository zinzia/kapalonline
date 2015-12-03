<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class payment extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_report_payment');
        $this->load->model("m_preferences");
        // load library
        $this->load->library('pagination');
        $this->load->library('email');
        $this->load->library('tnotification');
        //load helper
        $this->load->helper("terbilang_helper");
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "report/payment/index.html");
        // get search parameter
        $search = $this->tsession->userdata('report_search_payment');
        // search parameters
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_flight = empty($search['data_flight']) ? 'domestik' : '%' . $search['data_flight'] . '%';
        $this->smarty->assign("search", $search);
        // get list
        $params = array($published_no, $data_flight);
        $arr_tarif = $this->m_preferences->get_preferences_by_group("tarif_fa");
        $tarif = array();
        foreach ($arr_tarif as $t) {
            $tarif[$t["pref_nm"]] = $t["pref_value"];
        }

        $this->smarty->assign("rs_rekap_bayar", $this->m_report_payment->get_rekap_pembayaran());
        $this->smarty->assign("rs_id", $this->m_report_payment->get_list_awaiting_task_berjadwal($params));
        $this->smarty->assign("tarif", $tarif);
        $this->smarty->assign("total", COUNT($this->m_report_payment->get_list_awaiting_task_berjadwal($params)));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('report_search_payment');
        } else {
            $params = array(
                "published_no" => $this->input->post("published_no"),
                "data_flight" => $this->input->post("data_flight"),
            );
            $this->tsession->set_userdata("report_search_payment", $params);
        }
        // redirect
        redirect("report/payment");
    }

    // proses pencarian
    public function proses_cari_history() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('report_search_history');
        } else {
            $params = array(
                "va" => $this->input->post("va"),
                "status" => $this->input->post("status")
            );
            $this->tsession->set_userdata("report_search_history", $params);
        }
        // redirect
        redirect("report/payment/history");
    }

    function cetak_kwitansi($inv_id = "") {
        $this->_set_page_rule("R");
        $rs_kwitansi = $this->m_report_payment->get_kwitansi($inv_id);
        $rs_rincian = $this->m_report_payment->get_rincian_kwitansi(array('1', $inv_id));
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);
        // add a page
        $this->tcpdf->AddPage();
        // create pdf
        $html = '';
        $no = 1;
        // content
        $html .= '
            <style type="text/css">
                .table-input {
                    margin: 0 0 5px 0;
                    padding: 2px;
                    background-color: #fff;
                    border-collapse: collapse;
                    color: #666;
                    font-size: 20px;
                    text-align: left;
                    vertical-align: top;
                }

                .table-input th {
                    margin: 0;
                    padding: 5px 10px;
                    background-color: #4EA1D3;
                    border-bottom: 1px solid #5699C3;
                    border-top: 1px solid #7EA9BA;
                    color: #fff;
                    font-size: 28px;
                    vertical-align: top;
                }

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

                .table-signature {
                    margin: 0 0 5px 0;
                    padding: 0 0 30px 0;
                    background-color: #fff;
                    border-collapse: collapse;
                    color: #666;
                    font-size: 26px;
                    text-align: left;
                    vertical-align: top;
                }
            </style>
            <table width="100%">
                <tr>
                    <td width="20%" rowspan="6"><img src="resource/doc/images/logo/logo.jpg" width="48px" style="vertical-align:middle"></td>
                    <td width="60%" style="font-size:30px" align="center"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                    <td width="20%" rowspan="6" align="right"><br/><br/><img src="resource/doc/images/logo/logo-bni.jpg" width="75px" style="vertical-align:middle"></td>
                </tr>
                <tr>
                    <td style="font-size:28px" align="center">DIREKTORAT JENDERAL PERHUBUNGAN UDARA</td>
                </tr>
                <tr>
                    <td style="font-size:25px" align="center">PENDAFTARAN KAPAL ONLINE</td>
                </tr>
                <tr>
                    <td style="font-size:25px"></td>
                </tr>
                <tr>
                    <td style="font-size:20px" align="center">Jl. Medan Merdeka Barat No. 8, Jakarta 10110</td>
                </tr>
                <tr>
                    <td style="font-size:20px" align="center">Telp : 021-3811308 Faks : 021-3451657 email : info151@dephub.go.id</td>
                </tr>
            </table><hr/>
            <br/>
            <div style="font-size:25px;text-align:center"><b><u>BUKTI PEMBAYARAN</u></b><br/>NO :'.$rs_kwitansi["no_kuitansi"].' </div>
            <div style="font-size:25px;text-align:center;border-style: solid;border-width: 1px 1px 1px 1px;">
                <table class="table-form">
                    <tr>
                        <td width="24%">Sudah diterima dari</td><td width="1%">:</td><td width="75%">' . $rs_kwitansi["airlines_nm"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Perusahaan/Instansi</td><td width="1%">:</td><td width="75%">' . $rs_kwitansi["airlines_nm"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Banyaknya Uang</td><td width="1%">:</td><td width="75%">' . trim(ucwords(numb_to_alphabet($rs_kwitansi["inv_total"]))) . " Rupiah" . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Untuk pembayaran</td><td width="1%">:</td>
                        <td width="75%"><br/><table class="table-form" cellpadding="1" cellspacing="1">';
                        foreach ($rs_rincian as $rincian) {
                            $html.='<tr><td width="65%">' . $rincian["published_no"] . '</td>'.'<td width="5%">Rp. </td><td width="30%">'.number_format($rincian["amount"], 0, ".", ",").'</td></tr>';
                        }
        $html.='</table></td>
                    </tr>
                    <tr>
                        <td width="24%">Keterangan</td><td width="1%">:</td><td width="75%">Telah diterima pembayaran FA ' . ucwords($rs_kwitansi["data_flight"]) . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Jumlah</td><td width="1%">:</td><td width="75%">Rp ' . number_format($rs_kwitansi["inv_total"], 0, ".", ",") . '</td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="24%">Denda</td><td width="1%">:</td><td width="75%">___________________________________</td>
                    </tr>
                    <tr>
                        <td width="24%">Jumlah</td><td width="1%">:</td><td width="75%">Rp ' . number_format($rs_kwitansi["inv_total"], 0, ".", ",") . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Terbilang</td><td width="1%">:</td><td width="75%">' . trim(ucwords(numb_to_alphabet($rs_kwitansi["inv_total"]))) . " Rupiah" . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Virtual Account</td><td width="1%">:</td><td width="75%">' . $rs_kwitansi["virtual_account"] . '</td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3" style="text-align:right">Jakarta, ' . $this->datetimemanipulation->get_full_date($rs_kwitansi["tgl_update"]) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>Petugas PNBP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="24%"><small><b>Catatan</b></small>
                        <ol><small>
                            <li>Lembar 1 : untuk Wajib Bayar</li>
                            <li>Lembar 2 : untuk Bendahara Penerima</li>
                            <li>Lembar 3 : untuk Untuk Petugas Bank (Kasir)</li>
                            <li>Lembar 4 : untuk Petugas PNBP</li>
                            </small>
                        </ol>
                        </td>
                    </tr>
                </table>
            </div>
            ';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $inv_id;
        $this->tcpdf->Output($filename . ".pdf", 'D');
    }

    // download payment
    public function download_payment() {
        //set page rule
        $this->_set_page_rule("R");
        // get search parameter
        $search = $this->tsession->userdata('report_search_payment');
        // search parameters
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_flight = empty($search['data_flight']) ? 'domestik' : '%' . $search['data_flight'] . '%';
        $this->smarty->assign("search", $search);
        // get list
        $params = array($published_no, $data_flight);
        $arr_tarif = $this->m_preferences->get_preferences_by_group("tarif_fa");
        $tarif = array();
        foreach ($arr_tarif as $t) {
            $tarif[$t["pref_nm"]] = $t["pref_value"];
        }
        $rs_id = $this->m_report_payment->get_list_awaiting_task_berjadwal($params);
        $tarif = $tarif;
        // excel download
        // load excel
        $this->load->library('phpexcel');
        // create excell
        $filepath = "resource/doc/template/TEMPLATE_REPORT_PAYMENT.xlsx";
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
        $objWorksheet->setCellValue('G3', $this->datetimemanipulation->get_full_date(date('d-m-Y')));
        // list project
        $no = 1;
        $i = 6;
        foreach ($rs_id as $data) {
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['published_no']);
            $objWorksheet->setCellValue('D' . $i, strtoupper($data['airlines_nm']));
            $objWorksheet->setCellValue('E' . $i, strtoupper($data['data_type']) . ' ' . strtoupper($data['data_flight']));
            $objWorksheet->setCellValue('F' . $i, $tarif[$data['data_flight']]);
            $objWorksheet->setCellValue('G' . $i, $this->datetimemanipulation->get_full_date($data['payment_due_date']));
            // style
            $objWorksheet->getStyle('B' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('C' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('D' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('E' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('F' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('G' . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }
 
        // file_name
        $file_name = "LAPORAN_PAYMENT_" . date('dmY');
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
        exit();
    }

    public function history() {
        $this->_set_page_rule("R");
        $this->smarty->assign("template_content", "report/payment/history.html");
        // get search parameter
        $search = $this->tsession->userdata('report_search_history');
        // search parameters
        $va_no = empty($search['va']) ? '%' : '%' . $search['va'] . '%';
        $status = empty($search['status']) ? '%' : '%' . $search['status'] . '%';
        $this->smarty->assign("search", $search);
        $arr_st = array("00" => "Blm Bayar", "01" => "Kurang Bayar", "02" => "Lebih Bayar", "11" => "Sukses");
        // get list
        $params = array($va_no, '1', $status);
        $arr_tarif = $this->m_preferences->get_preferences_by_group("tarif_fa");
        $tarif = array();
        foreach ($arr_tarif as $t) {
            $tarif[$t["pref_nm"]] = $t["pref_value"];
        }
        $this->smarty->assign("rs_rekap_bayar", $this->m_report_payment->get_rekap_pembayaran());
        $this->smarty->assign("rs_id", $this->m_report_payment->get_list_issued_invoice($params));
        $this->smarty->assign("total", COUNT($this->m_report_payment->get_list_issued_invoice($params)));
        $this->smarty->assign("tarif", $tarif);
        $this->smarty->assign("status", $arr_st);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // download history
    public function download_history() {
        //set page rule
        $this->_set_page_rule("R");
        // get search parameter
        $search = $this->tsession->userdata('report_search_history');
        // search parameters
        $va_no = empty($search['va']) ? '%' : '%' . $search['va'] . '%';
        $status = empty($search['status']) ? '%' : '%' . $search['status'] . '%';
        $this->smarty->assign("search", $search);
        $arr_st = array("00" => "Blm Bayar", "01" => "Kurang Bayar", "02" => "Lebih Bayar", "11" => "Sukses");
        // get list
        $params = array($va_no, '1', $status);
        $arr_tarif = $this->m_preferences->get_preferences_by_group("tarif_fa");
        $tarif = array();
        foreach ($arr_tarif as $t) {
            $tarif[$t["pref_nm"]] = $t["pref_value"];
        }
        $rs_id = $this->m_report_payment->get_list_issued_invoice($params);
        $tarif = $tarif;
        $status = $arr_st;
        // excel download
        // load excel
        $this->load->library('phpexcel');
        // create excell
        $filepath = "resource/doc/template/TEMPLATE_REPORT_HISTORY.xlsx";
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
        $objWorksheet->setCellValue('I3', $this->datetimemanipulation->get_full_date(date('d-m-Y')));
        // list project
        $no = 1;
        $i = 6;
        foreach ($rs_id as $data) {
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['virtual_account']);
            $objWorksheet->setCellValue('D' . $i, strtoupper($data['airlines_nm']));
            $objWorksheet->setCellValue('E' . $i, $this->datetimemanipulation->get_full_date($data['inv_date']));
            $objWorksheet->setCellValue('F' . $i, $data['inv_st']);
            $objWorksheet->setCellValue('G' . $i, $data['jml_dibayar']);
            $objWorksheet->setCellValue('H' . $i, ($data['remark']) ? $data['remark'] : '-');
            $objWorksheet->setCellValue('I' . $i, number_format($data['inv_total']));
            // style
            $objWorksheet->getStyle('B' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('C' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('D' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('E' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('F' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('G' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('H' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('I' . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }
 
        // file_name
        $file_name = "LAPORAN_HISTORY_" . date('dmY');
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
        exit();
    }

}
