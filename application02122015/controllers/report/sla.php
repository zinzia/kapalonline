<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class sla extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_sla');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "report/sla/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        $this->smarty->load_javascript('resource/js/fusioncharts/fusioncharts.js');
        // load style ui
        $this->smarty->load_style("select2/select2.css");
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
        // tahun
        $default = date('Y');
        $tahun = array();
        for ($i = $default - 4; $i <= $default; $i++) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // search parameters
        $search = $this->tsession->userdata('search_report_statistik');
        $bulan = empty($search['bulan']) ? date('n') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;
        $this->smarty->assign("search", $search);
        // statistik
        // get statistik sla
        $params = array($bulan, $tahun);
        $rs_pengajuan = $this->m_sla->get_sla($params);
        $this->smarty->assign("rs_pengajuan", $rs_pengajuan);
        $this->smarty->assign("no", 1);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_report_statistik');
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun"),
            );
            $this->tsession->set_userdata("search_report_statistik", $params);
        }
        // redirect
        redirect("report/sla");
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
        // tahun
        $default = date('Y');
        $tahun = array();
        for ($i = $default - 4; $i <= $default; $i++) {
            $tahun[] = $i;
        }
        // search parameters
        $search = $this->tsession->userdata('search_report_statistik');
        $bulan = empty($search['bulan']) ? date('n') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;

        // get statistik sla
        $params = array($bulan, $tahun);
        $rs_pengajuan = $this->m_sla->get_sla($params);

        // get data
        // create excel
        $filepath = "resource/doc/template/TEMPLATE_REPORT_SLA.xlsx";
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

        // dicetak pada
        $objWorksheet->setCellValue('F3', $this->datetimemanipulation->get_full_date(date('Y-m-d')));

        $no = 1;
        $i = 6;
        $kolom = array('B', 'C', 'D', 'E', 'F');
        foreach ($rs_pengajuan as $data) {
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['operator_name']);
            $objWorksheet->setCellValue('D' . $i, $data['total_time']);
            $objWorksheet->setCellValue('E' . $i, $data['total_proses']);
            $objWorksheet->setCellValue('F' . $i, $data['response_time']);
            // style
            $objWorksheet->getStyle($kolom[0] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[1] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[2] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[3] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[4] . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }

        // output file
        $file_name = 'REKAPITULASI_REPORT_SLA';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

    // chart jumlah fa
    public function chart_xml_jumlah_fa() {
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
        // tahun
        $default = date('Y');
        $tahun = array();
        for ($i = $default - 4; $i <= $default; $i++) {
            $tahun[] = $i;
        }
        // search parameters
        $search = $this->tsession->userdata('search_report_statistik');
        $bulan = empty($search['bulan']) ? date('n') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;

        // get statistik sla
        $params = array($bulan, $tahun);
        $rs_pengajuan = $this->m_sla->get_sla($params);

        $str_xml = "<chart showPercentValues='1' showLabels='1' showValues='1' baseFontSize='8'>";
        foreach ($rs_pengajuan as $data) {
            $str_xml .= "<set label='" . $data['operator_name'] . "' value='" . $data['total_proses'] . "'/>";
        }
        $str_xml .= "</chart>";

        echo $str_xml;
    }

    // chart respon
    public function chart_xml_respon($total = '') {
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
        // tahun
        $default = date('Y');
        $tahun = array();
        for ($i = $default - 4; $i <= $default; $i++) {
            $tahun[] = $i;
        }
        // search parameters
        $search = $this->tsession->userdata('search_report_statistik');
        $bulan = empty($search['bulan']) ? date('n') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;

        // get statistik sla
        $params = array($bulan, $tahun);
        $rs_pengajuan = $this->m_sla->get_sla($params);

        $str_xml = "<chart showPercentValues='1' showLabels='1' showValues='1' baseFontSize='8'>";
        foreach ($rs_pengajuan as $data) {
            $str_xml .= "<set label='" . $data['operator_name'] . "' value='" . $data['detik'] . "'/>";
        }
        $str_xml .= "</chart>";

        echo $str_xml;
    }

}
