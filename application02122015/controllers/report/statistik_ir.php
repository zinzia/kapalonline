<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class statistik_ir extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_report_statistik');
        $this->load->model('m_sla');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "report/statistik_ir/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        $this->smarty->load_javascript('resource/js/fusioncharts/fusioncharts.js');
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // bulan
        $month = array(
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
        $this->smarty->assign("rs_bulan", $month);
        // tahun
        $default = date('Y');
        $tahun = array();
        for ($i = $default - 4; $i <= $default; $i++) {
            $tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $tahun);
        // search parameters
        $search = $this->tsession->userdata('search_report_statistik');
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $airlines_id = empty($search['airlines_id']) ? '%' : $search['airlines_id'];
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;
        $this->smarty->assign("search", $search);
        // statistik
        $params = array($airlines_id, $tahun, $bulan);
        $rs_id = $this->m_report_statistik->get_data_statistik_pengajuan_ir($params);
        $data = array();
        $total_waiting = 0;
        $total_process = 0;
        $total_terbit = 0;
        $total_reject = 0;
        $total = 0;
        foreach ($rs_id as $key => $value) {
            $data[$value['group']][$value['izin_flight']][$value['izin_approval']][$value['is_process']] = $value['total'];
        }
        $this->smarty->assign("data", $data);
        $this->smarty->assign("total_waiting", $total_waiting);
        $this->smarty->assign("total_process", $total_process);
        $this->smarty->assign("total_terbit", $total_terbit);
        $this->smarty->assign("total_reject", $total_reject);
        $this->smarty->assign("total", $total);
        
        // SLA Operator
        $rs_id = array();
        $params = array($tahun, $bulan);
        $rs_id = $this->m_sla->get_sla_ir($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("periode", (strtoupper($month[$bulan]) . " " . strtoupper($tahun)));
        // get list airlines
        $this->smarty->assign("rs_airlines", $this->m_report_statistik->get_list_airlines());
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
                "airlines_id" => $this->input->post("airlines_id"),
            );
            $this->tsession->set_userdata("search_report_statistik", $params);
        }
        // redirect
        redirect("report/statistik_ir");
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
        $month = array(
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
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $airlines_id = empty($search['airlines_id']) ? '%' : $search['airlines_id'];
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;
        $this->smarty->assign("search", $search);
        // statistik
        $params = array($airlines_id, $tahun, $bulan);
        $rs_id = $this->m_report_statistik->get_data_statistik_pengajuan_ir($params);
        $data = array();
        $total_waiting = 0;
        $total_process = 0;
        $total_terbit = 0;
        $total_reject = 0;
        $total = 0;
        foreach ($rs_id as $key => $value) {
            $data[$value['group']][$value['izin_flight']][$value['izin_approval']][$value['is_process']] = $value['total'];
        }

        // get data
        // create excel
        $filepath = "resource/doc/template/TEMPLATE_STATISTIK_IR.xlsx";
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
        $airlines_nm = $this->m_report_statistik->get_airlines_nm_by_id(array($airlines_id));
        if (!empty($airlines_nm)) {
            $airlines = $airlines_nm[0]['airlines_nm'];
        }
        //print_r($month[$bulan]); die;
        $objWorksheet->setCellValue('B3', strtoupper($airlines));
        $objWorksheet->setCellValue('R4', strtoupper($this->datetimemanipulation->get_full_date(date('Y-m-d'))));
        $objWorksheet->setCellValue('B4', "PERIODE : " . strtoupper($month[$bulan]) . " " . strtoupper($tahun));

        $objWorksheet->setCellValue('C8', !empty($data['baru']['domestik']['waiting']['in_airlines']) ? $data['baru']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('D8', !empty($data['baru']['internasional']['waiting']['in_airlines']) ? $data['baru']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('E8', !empty($data['perpanjangan']['domestik']['waiting']['in_airlines']) ? $data['perpanjangan']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('F8', !empty($data['perpanjangan']['internasional']['waiting']['in_airlines']) ? $data['perpanjangan']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('G8', !empty($data['penundaan']['domestik']['waiting']['in_airlines']) ? $data['penundaan']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('H8', !empty($data['penundaan']['internasional']['waiting']['in_airlines']) ? $data['penundaan']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('I8', !empty($data['perubahan']['domestik']['waiting']['in_airlines']) ? $data['perubahan']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('J8', !empty($data['perubahan']['internasional']['waiting']['in_airlines']) ? $data['perubahan']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('K8', !empty($data['frekuensi_add']['domestik']['waiting']['in_airlines']) ? $data['frekuensi_add']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('L8', !empty($data['frekuensi_add']['internasional']['waiting']['in_airlines']) ? $data['frekuensi_add']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('M8', !empty($data['frekuensi_delete']['domestik']['waiting']['in_airlines']) ? $data['frekuensi_delete']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('N8', !empty($data['frekuensi_delete']['internasional']['waiting']['in_airlines']) ? $data['frekuensi_delete']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('O8', !empty($data['penghentian']['domestik']['waiting']['in_airlines']) ? $data['penghentian']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('P8', !empty($data['penghentian']['internasional']['waiting']['in_airlines']) ? $data['penghentian']['internasional']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('Q8', !empty($data['migrasi']['domestik']['waiting']['in_airlines']) ? $data['migrasi']['domestik']['waiting']['in_airlines'] : '0');
        $objWorksheet->setCellValue('R8', !empty($data['migrasi']['internasional']['waiting']['in_airlines']) ? $data['migrasi']['internasional']['waiting']['in_airlines'] : '0');

        $objWorksheet->setCellValue('C9', !empty($data['baru']['domestik']['waiting']['in_staff']) ? $data['baru']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('D9', !empty($data['baru']['internasional']['waiting']['in_staff']) ? $data['baru']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('E9', !empty($data['perpanjangan']['domestik']['waiting']['in_staff']) ? $data['perpanjangan']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('F9', !empty($data['perpanjangan']['internasional']['waiting']['in_staff']) ? $data['perpanjangan']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('G9', !empty($data['penundaan']['domestik']['waiting']['in_staff']) ? $data['penundaan']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('H9', !empty($data['penundaan']['internasional']['waiting']['in_staff']) ? $data['penundaan']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('I9', !empty($data['perubahan']['domestik']['waiting']['in_staff']) ? $data['perubahan']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('J9', !empty($data['perubahan']['internasional']['waiting']['in_staff']) ? $data['perubahan']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('K9', !empty($data['frekuensi_add']['domestik']['waiting']['in_staff']) ? $data['frekuensi_add']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('L9', !empty($data['frekuensi_add']['internasional']['waiting']['in_staff']) ? $data['frekuensi_add']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('M9', !empty($data['frekuensi_delete']['domestik']['waiting']['in_staff']) ? $data['frekuensi_delete']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('N9', !empty($data['frekuensi_delete']['internasional']['waiting']['in_staff']) ? $data['frekuensi_delete']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('O9', !empty($data['penghentian']['domestik']['waiting']['in_staff']) ? $data['penghentian']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('P9', !empty($data['penghentian']['internasional']['waiting']['in_staff']) ? $data['penghentian']['internasional']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('Q9', !empty($data['migrasi']['domestik']['waiting']['in_staff']) ? $data['migrasi']['domestik']['waiting']['in_staff'] : '0');
        $objWorksheet->setCellValue('R9', !empty($data['migrasi']['internasional']['waiting']['in_staff']) ? $data['migrasi']['internasional']['waiting']['in_staff'] : '0');

        $objWorksheet->setCellValue('C10', !empty($data['baru']['domestik']['approved']['in_staff']) ? $data['baru']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('D10', !empty($data['baru']['internasional']['approved']['in_staff']) ? $data['baru']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('E10', !empty($data['perpanjangan']['domestik']['approved']['in_staff']) ? $data['perpanjangan']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('F10', !empty($data['perpanjangan']['internasional']['approved']['in_staff']) ? $data['perpanjangan']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('G10', !empty($data['penundaan']['domestik']['approved']['in_staff']) ? $data['penundaan']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('H10', !empty($data['penundaan']['internasional']['approved']['in_staff']) ? $data['penundaan']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('I10', !empty($data['perubahan']['domestik']['approved']['in_staff']) ? $data['perubahan']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('J10', !empty($data['perubahan']['internasional']['approved']['in_staff']) ? $data['perubahan']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('K10', !empty($data['frekuensi_add']['domestik']['approved']['in_staff']) ? $data['frekuensi_add']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('L10', !empty($data['frekuensi_add']['internasional']['approved']['in_staff']) ? $data['frekuensi_add']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('M10', !empty($data['frekuensi_delete']['domestik']['approved']['in_staff']) ? $data['frekuensi_delete']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('N10', !empty($data['frekuensi_delete']['internasional']['approved']['in_staff']) ? $data['frekuensi_delete']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('O10', !empty($data['penghentian']['domestik']['approved']['in_staff']) ? $data['penghentian']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('P10', !empty($data['penghentian']['internasional']['approved']['in_staff']) ? $data['penghentian']['internasional']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('Q10', !empty($data['migrasi']['domestik']['approved']['in_staff']) ? $data['migrasi']['domestik']['approved']['in_staff'] : '0');
        $objWorksheet->setCellValue('R10', !empty($data['migrasi']['internasional']['approved']['in_staff']) ? $data['migrasi']['internasional']['approved']['in_staff'] : '0');

        $objWorksheet->setCellValue('C11', !empty($data['baru']['domestik']['rejected']['in_staff']) ? $data['baru']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('D11', !empty($data['baru']['internasional']['rejected']['in_staff']) ? $data['baru']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('E11', !empty($data['perpanjangan']['domestik']['rejected']['in_staff']) ? $data['perpanjangan']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('F11', !empty($data['perpanjangan']['internasional']['rejected']['in_staff']) ? $data['perpanjangan']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('G11', !empty($data['penundaan']['domestik']['rejected']['in_staff']) ? $data['penundaan']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('H11', !empty($data['penundaan']['internasional']['rejected']['in_staff']) ? $data['penundaan']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('I11', !empty($data['perubahan']['domestik']['rejected']['in_staff']) ? $data['perubahan']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('J11', !empty($data['perubahan']['internasional']['rejected']['in_staff']) ? $data['perubahan']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('K11', !empty($data['frekuensi_add']['domestik']['rejected']['in_staff']) ? $data['frekuensi_add']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('L11', !empty($data['frekuensi_add']['internasional']['rejected']['in_staff']) ? $data['frekuensi_add']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('M11', !empty($data['frekuensi_delete']['domestik']['rejected']['in_staff']) ? $data['frekuensi_delete']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('N11', !empty($data['frekuensi_delete']['internasional']['rejected']['in_staff']) ? $data['frekuensi_delete']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('O11', !empty($data['penghentian']['domestik']['rejected']['in_staff']) ? $data['penghentian']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('P11', !empty($data['penghentian']['internasional']['rejected']['in_staff']) ? $data['penghentian']['internasional']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('Q11', !empty($data['migrasi']['domestik']['rejected']['in_staff']) ? $data['migrasi']['domestik']['rejected']['in_staff'] : '0');
        $objWorksheet->setCellValue('R11', !empty($data['migrasi']['internasional']['rejected']['in_staff']) ? $data['migrasi']['internasional']['rejected']['in_staff'] : '0');

        // style
        for ($i = 6; $i <= 12; $i++) {
            $objWorksheet->getStyle('C' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('D' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('E' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('F' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('G' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('H' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('I' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('J' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('K' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('L' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('M' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('N' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('O' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('P' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('Q' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('R' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('S' . $i)->applyFromArray($styleBorder);
        }

        // output file
        $file_name = 'REKAPITULASI_STATISTIK_IR';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

    // chart statistik permohonan IR
    public function data_chart_ir() {
        echo "<?xml version='1.0' encoding='iso-8859-1''?>";
        // bulan
        $bulan = array(
            '1' => 'Jan',
            '2' => 'Feb',
            '3' => 'Mar',
            '4' => 'Apr',
            '5' => 'Mei',
            '6' => 'Jun',
            '7' => 'Jul',
            '8' => 'Agu',
            '9' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        );
        // DATA DOMESTIK DAN INTERNASIONAL APPROVED
        $rs_id = $this->m_report_statistik->get_data_chart_ir();
        $data_domestik = array();
        $data_internasional = array();
        foreach ($rs_id as $data) {
            if ($data['izin_flight'] == 'domestik') {
                $data_domestik[$data['bulan']] = $data['total'];
            }
            if ($data['izin_flight'] == 'internasional') {
                $data_internasional[$data['bulan']] = $data['total'];
            }
        }
        // strxml
        $str_xml = "<chart caption='' subcaption='' yaxisname='Jumlah' xaxisname='' plotgradientcolor='' 
                bgcolor='FFFFFF' showalternatehgridcolor='0' showplotborder='0'
                divlinecolor='#CCCCCC' showvalues='1' showborder='0' showcanvasborder='0'
                canvasbordercolor='#CCCCCC' canvasborderthickness='1' captionpadding='30' linethickness='3' 
                yaxisvaluespadding='5' showshadow='1' 
                animation='1' palettecolors='e44a00' labeldisplay='ROTATE' slantlabels='1'>";
        $str_xml .= "<categories>
                        <category label='Jan' />
                        <category label='Feb' />
                        <category label='Mar' />
                        <category label='Apr' />
                        <category label='May' />
                        <category label='Jun' />
                        <category label='Jul' />
                        <category label='Aug' />
                        <category label='Sep' />
                        <category label='Oct' />
                        <category label='Nov' />
                        <category label='Dec' />
                     </categories>";
        // BERJADWAL
        $str_xml .= "<dataset seriesName='Domestik' color='A5D05A'>";
        foreach ($bulan as $key => $value) {
            $total = isset($data_domestik[$key]) ? $data_domestik[$key] : '';
            $str_xml .= "<set label='" . $value . "' value='" . $total . "' color='A5D05A' />";
        }
        $str_xml .= "</dataset>";
        // TIDAK BERJADWAL
        $str_xml .= "<dataset seriesName='Internasional' color='FF6C39'>";
        foreach ($bulan as $key => $value) {
            $total = isset($data_internasional[$key]) ? $data_internasional[$key] : '';
            $str_xml .= "<set label='" . $value . "' value='" . $total . "' color='FF6C39' />";
        }
        $str_xml .= "</dataset>";
        $str_xml .= "</chart>";
        echo $str_xml;
    }
}
