<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class dashboard extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_dashboard_helpdesk');
    }

    // routes
    public function index() {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "helpdesk/dashboard/default.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        $this->smarty->load_javascript('resource/js/fusioncharts/fusioncharts.js');
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // date
        $now['tanggal'] = $this->datetimemanipulation->get_full_date(date('Y-m-d'));
        $now['hari'] = date('l');
        $now['tahun'] = date('Y');
        $this->smarty->assign("tanggal", $now);
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
        $search = $this->tsession->userdata('search_helpdesk_dashboard');
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $airlines_nm = empty($search['airlines_nm']) ? "%" : "%" . $search['airlines_nm'] . "%";
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;
        $this->smarty->assign("search", $search);
        // statistik
        $params = array($airlines_nm, $bulan, $tahun);
        $rs_id = $this->m_dashboard_helpdesk->get_data_statistik_pelayanan($params);
        $data = array();
        $total = 0;
        $total_awaiting = 0;
        $total_approved = 0;
        $total_rejected = 0;
        foreach ($rs_id as $value) {
            $data[$value['data_st']][str_replace(' ', '_', $value['data_type'])][$value['data_flight']] = $value['total'];
            $total += $value['total'];
            // waiting
            if ($value['data_st'] == 'waiting') {
                $total_awaiting += $value['total'];
            }
            // approved
            if ($value['data_st'] == 'approved') {
                $total_approved += $value['total'];
            }
            // rejected
            if ($value['data_st'] == 'rejected') {
                $total_rejected += $value['total'];
            }
        }
        $this->smarty->assign("data", $data);
        $this->smarty->assign("total", $total);
        $this->smarty->assign("total_awaiting", $total_awaiting);
        $this->smarty->assign("total_approved", $total_approved);
        $this->smarty->assign("total_rejected", $total_rejected);
        // belum diproses
        // statistik
        $rs_id = $this->m_dashboard_helpdesk->get_data_statistik_pelayanan_belum_proses($params);
        $data = array();
        $total = 0;
        foreach ($rs_id as $value) {
            $data[str_replace(' ', '_', $value['data_type'])][$value['data_flight']] = $value['total'];
            $total += $value['total'];
        }
        $this->smarty->assign("data_belum", $data);
        $this->smarty->assign("total_belum", $total);
        // get list airlines
        $this->smarty->assign("rs_airlines", $this->m_dashboard_helpdesk->get_list_airlines());
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_helpdesk_dashboard');
        } else {
            $params = array(
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun"),
                "airlines_nm" => $this->input->post("airlines_nm"),
            );
            $this->tsession->set_userdata("search_helpdesk_dashboard", $params);
        }
        // redirect
        redirect("helpdesk/dashboard");
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
        $search = $this->tsession->userdata('search_helpdesk_dashboard');
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $airlines_nm = empty($search['airlines_nm']) ? "%" : "%" . $search['airlines_nm'] . "%";
        $search['bulan'] = $bulan;
        $search['tahun'] = $tahun;
        $this->smarty->assign("search", $search);
        // statistik
        $params = array($airlines_nm, $bulan, $tahun);
        $rs_id = $this->m_dashboard_helpdesk->get_data_statistik_pelayanan($params);
        $data = array();
        $total = 0;
        $total_awaiting = 0;
        $total_approved = 0;
        $total_rejected = 0;
        foreach ($rs_id as $value) {
            $data[$value['data_st']][str_replace(' ', '_', $value['data_type'])][$value['data_flight']] = $value['total'];
            $total += $value['total'];
            // waiting
            if ($value['data_st'] == 'waiting') {
                $total_awaiting += $value['total'];
            }
            // approved
            if ($value['data_st'] == 'approved') {
                $total_approved += $value['total'];
            }
            // rejected
            if ($value['data_st'] == 'rejected') {
                $total_rejected += $value['total'];
            }
        }
        // belum diproses
        // statistik
        $rs_id = $this->m_dashboard_helpdesk->get_data_statistik_pelayanan_belum_proses($params);
        $data_belum = array();
        $total_belum = 0;
        foreach ($rs_id as $value) {
            $data_belum[str_replace(' ', '_', $value['data_type'])][$value['data_flight']] = $value['total'];
            $total_belum += $value['total'];
        }

        // get data
        // create excel
        $filepath = "resource/doc/template/TEMPLATE_STATISTIK_FA.xlsx";
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
        $airlines = "";
        if ($airlines_nm != "%") {
            $airlines = $search['airlines_nm'];
        }
        $objWorksheet->setCellValue('B2', "REKAPITULASI FLIGHT APPROVAL " . $airlines);
        $objWorksheet->setCellValue('G3', $this->datetimemanipulation->get_full_date(date('Y-m-d')));

        $objWorksheet->setCellValue('C7', $data_belum['berjadwal']['domestik']);
        $objWorksheet->setCellValue('D7', $data_belum['berjadwal']['internasional']);
        $objWorksheet->setCellValue('E7', $data_belum['tidak_berjadwal']['domestik']);
        $objWorksheet->setCellValue('F7', $data_belum['tidak_berjadwal']['internasional']);
        $objWorksheet->setCellValue('G7', $data_belum['bukan_niaga']['domestik']);
        $objWorksheet->setCellValue('H7', $data_belum['bukan_niaga']['internasional']);
        $objWorksheet->setCellValue('I7', $total_belum);

        $objWorksheet->setCellValue('C8', ($data['waiting']['berjadwal']['domestik'] - $data_belum['berjadwal']['domestik']));
        $objWorksheet->setCellValue('D8', ($data['waiting']['berjadwal']['internasional'] - $data_belum['berjadwal']['internasional']));
        $objWorksheet->setCellValue('E8', ($data['waiting']['tidak_berjadwal']['domestik'] - $data_belum['tidak_berjadwal']['domestik']));
        $objWorksheet->setCellValue('F8', ($data['waiting']['tidak_berjadwal']['internasional'] - $data_belum['tidak_berjadwal']['internasional']));
        $objWorksheet->setCellValue('G8', ($data['waiting']['bukan_niaga']['domestik'] - $data_belum['bukan_niaga']['domestik']));
        $objWorksheet->setCellValue('H8', ($data['waiting']['bukan_niaga']['internasional'] - $data_belum['bukan_niaga']['internasional']));
        $objWorksheet->setCellValue('I8', ($total_awaiting - $total_belum));

        $objWorksheet->setCellValue('C9', $data['approved']['berjadwal']['domestik']);
        $objWorksheet->setCellValue('D9', $data['approved']['berjadwal']['internasional']);
        $objWorksheet->setCellValue('E9', $data['approved']['tidak_berjadwal']['domestik']);
        $objWorksheet->setCellValue('F9', $data['approved']['tidak_berjadwal']['internasional']);
        $objWorksheet->setCellValue('G9', $data['approved']['bukan_niaga']['domestik']);
        $objWorksheet->setCellValue('H9', $data['approved']['bukan_niaga']['internasional']);
        $objWorksheet->setCellValue('I9', $total_approved);

        $objWorksheet->setCellValue('C10', $data['rejected']['berjadwal']['domestik']);
        $objWorksheet->setCellValue('D10', $data['rejected']['berjadwal']['internasional']);
        $objWorksheet->setCellValue('E10', $data['rejected']['tidak_berjadwal']['domestik']);
        $objWorksheet->setCellValue('F10', $data['rejected']['tidak_berjadwal']['internasional']);
        $objWorksheet->setCellValue('G10', $data['rejected']['bukan_niaga']['domestik']);
        $objWorksheet->setCellValue('H10', $data['rejected']['bukan_niaga']['internasional']);
        $objWorksheet->setCellValue('I10', $total_rejected);

        $objWorksheet->setCellValue('C11', ($data['waiting']['berjadwal']['domestik'] + $data['approved']['berjadwal']['domestik'] + $data['rejected']['berjadwal']['domestik']));
        $objWorksheet->setCellValue('D11', ($data['waiting']['berjadwal']['internasional'] + $data['approved']['berjadwal']['internasional'] + $data['rejected']['berjadwal']['internasional']));
        $objWorksheet->setCellValue('E11', ($data['waiting']['tidak_berjadwal']['domestik'] + $data['approved']['tidak_berjadwal']['domestik'] + $data['rejected']['tidak_berjadwal']['domestik']));
        $objWorksheet->setCellValue('F11', ($data['waiting']['tidak_berjadwal']['internasional'] + $data['approved']['tidak_berjadwal']['internasional'] + $data['rejected']['tidak_berjadwal']['internasional']));
        $objWorksheet->setCellValue('G11', ($data['waiting']['bukan_niaga']['domestik'] + $data['approved']['bukan_niaga']['domestik'] + $data['rejected']['bukan_niaga']['domestik']));
        $objWorksheet->setCellValue('H11', ($data['waiting']['bukan_niaga']['internasional'] + $data['approved']['bukan_niaga']['internasional'] + $data['rejected']['bukan_niaga']['internasional']));
        $objWorksheet->setCellValue('I11', $total);

        // style
        for ($i = 7; $i <= 11; $i++) { 
            $objWorksheet->getStyle('C' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('D' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('E' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('F' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('G' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('H' . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle('I' . $i)->applyFromArray($styleBorder);
        }

        // output file
        $file_name = 'REKAPITULASI_STATISTIK_FA';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

    /*
     * XML Data
     */

    // chart statistik permohonan
    public function data_chart() {
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
        // BERJADWAL
        $rs_id = $this->m_dashboard_helpdesk->get_data_chart_fa(array('berjadwal'));
        $data_berjadwal = array();
        foreach ($rs_id as $data) {
            $data_berjadwal[$data['bulan']] = $data['total'];
        }
        // TIDAK BERJADWAL
        $rs_id = $this->m_dashboard_helpdesk->get_data_chart_fa(array('tidak berjadwal'));
        $data_tidak_berjadwal = array();
        foreach ($rs_id as $data) {
            $data_tidak_berjadwal[$data['bulan']] = $data['total'];
        }
        // BUKAN NIAGA
        $rs_id = $this->m_dashboard_helpdesk->get_data_chart_fa(array('bukan niaga'));
        $data_bukan_niaga = array();
        foreach ($rs_id as $data) {
            $data_bukan_niaga[$data['bulan']] = $data['total'];
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
        $str_xml .= "<dataset seriesName='Berjadwal' color='A5D05A'>";
        foreach ($bulan as $key => $value) {
            $total = isset($data_berjadwal[$key]) ? $data_berjadwal[$key] : '';
            $str_xml .= "<set label='" . $value . "' value='" . $total . "' color='A5D05A' />";
        }
        $str_xml .= "</dataset>";
        // TIDAK BERJADWAL
        $str_xml .= "<dataset seriesName='Tidak berjadwal' color='FF6C39'>";
        foreach ($bulan as $key => $value) {
            $total = isset($data_tidak_berjadwal[$key]) ? $data_tidak_berjadwal[$key] : '';
            $str_xml .= "<set label='" . $value . "' value='" . $total . "' color='FF6C39' />";
        }
        $str_xml .= "</dataset>";
        // BUKAN NIAGA
        $str_xml .= "<dataset seriesName='Bukan Niaga' color='F13D6A'>";
        foreach ($bulan as $key => $value) {
            $total = isset($data_bukan_niaga[$key]) ? $data_bukan_niaga[$key] : '';
            $str_xml .= "<set label='" . $value . "' value='" . $total . "' color='F13D6A' />";
        }
        $str_xml .= "</dataset>";
        $str_xml .= "</chart>";
        echo $str_xml;
    }

    // chart statistik permohonan
    public function data_chart_pie() {
        // search parameters
        $search = $this->tsession->userdata('search_helpdesk_dashboard');
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $airlines_nm = empty($search['airlines_nm']) ? "%" : "%" . $search['airlines_nm'] . "%";
        // statistik belum
        $params = array($airlines_nm, $bulan, $tahun);
        $rs_id = $this->m_dashboard_helpdesk->get_data_statistik_pelayanan_belum_proses($params);
        $data = array();
        $total = 0;
        foreach ($rs_id as $value) {
            $total += $value['total'];
        }
        // BUKAN NIAGA
        $rs_id = $this->m_dashboard_helpdesk->get_data_chart_pie($params);
        $data = array();
        foreach ($rs_id as $value) {
            $data[$value['data_st']] = $value['total'];
        }
        $waiting = (empty($data['waiting']) ? 0 : $data['waiting']);
        $rejected = (empty($data['rejected']) ? 0 : $data['rejected']);
        $approved = (empty($data['approved']) ? 0 : $data['approved']);
        // chart
        echo "<?xml version='1.0' encoding='iso-8859-1''?>";
        // Data
        // strxml
        $str_xml = "<chart showLabels='0' showValues='0' baseFontSize='9'  plotgradientcolor='' showZeroPies='1' 
                bgcolor='FFFFFF' showplotborder='0' showborder='0' showcanvasborder='0'>";
        $str_xml .= "<set label='" . $total . " belum diproses' value='" . $total . "' color='FF0000' />";
        $str_xml .= "<set label='" . ($waiting - $total) . " sedang diproses' value='" . ($waiting - $total) . "' color='F2C95C' />";
        $str_xml .= "<set label='" . $rejected . " ditolak' value='" . $rejected . "' color='F13D6A'  />";
        $str_xml .= "<set label='" . $approved . " telah terbitkan' value='" . $approved . "' color='2EABE5' />";
        $str_xml .= "</chart>";
        echo $str_xml;
    }

}
