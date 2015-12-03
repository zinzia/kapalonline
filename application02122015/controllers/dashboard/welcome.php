<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

// --

class welcome extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_dashboard');
    }

    // routes
    public function index() {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "dashboard/welcome/default.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.10.2.min.js");
        $this->smarty->load_javascript('resource/js/fusioncharts/fusioncharts.js');
        // load style ui
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        $this->smarty->load_javascript("resource/js/messi/messi.min.js");
        $this->smarty->load_style("messi/messi.min.css");
        // date
        $now['tanggal'] = $this->datetimemanipulation->get_full_date(date('Y-m-d'));
        $now['hari'] = date('l');
        $now['tahun'] = date('Y');
        $this->smarty->assign("tanggal", $now);
        // get detail task
        $task = $this->m_dashboard->get_detail_my_task_by_role_berjadwal(array($this->com_user['role_id']));
        $this->smarty->assign("task_berjadwal", $task);
        $task = $this->m_dashboard->get_detail_my_task_by_role_tidak_berjadwal(array($this->com_user['role_id']));
        $this->smarty->assign("task_tidak_berjadwal", $task);
        // list my task flight approval berjadwal
        $list_waiting = $this->m_dashboard->get_list_my_task_waiting_berjadwal(array($this->com_user['role_id'], $this->com_user['user_id']));
        $this->smarty->assign("list_waiting_berjadwal", $list_waiting);
        $this->smarty->assign("total_waiting_berjadwal", count($list_waiting));
        // list my task flight approval tidak berjadwal
        $list_waiting = $this->m_dashboard->get_list_my_task_waiting_tidak_berjadwal(array($this->com_user['role_id'], $this->com_user['user_id']));
        $this->smarty->assign("list_waiting_tidak_berjadwal", $list_waiting);
        $this->smarty->assign("total_waiting_tidak_berjadwal", count($list_waiting));
        // list my task flight approval berjadwal
        $list_waiting = $this->m_dashboard->get_detail_my_task_rute(array($this->com_user['role_id'], $this->com_user['user_id'], 'domestik'));
        $this->smarty->assign("list_waiting_domestik", $list_waiting);
        $this->smarty->assign("total_waiting_domestik", count($list_waiting));
        // list my task flight approval tidak berjadwal
        $list_waiting = $this->m_dashboard->get_detail_my_task_rute(array($this->com_user['role_id'], $this->com_user['user_id'], 'internasional'));
        $this->smarty->assign("list_waiting_internasional", $list_waiting);
        $this->smarty->assign("total_waiting_internasional", count($list_waiting));
        // output
        parent::display();
    }

    /*
     * XML Data
     */

    // chart statistik permohonan
    public function data_chart() {
        echo "<?xml version='1.0' encoding='iso-8859-1''?>";
        // bulan
        $bulan = array(
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        );
        // BERJADWAL
        $rs_id = $this->m_dashboard->get_data_chart_fa(array('berjadwal'));
        $data_berjadwal = array();
        foreach ($rs_id as $data) {
            $data_berjadwal[$data['bulan']] = $data['total'];
        }
        // TIDAK BERJADWAL
        $rs_id = $this->m_dashboard->get_data_chart_fa(array('tidak berjadwal'));
        $data_tidak_berjadwal = array();
        foreach ($rs_id as $data) {
            $data_tidak_berjadwal[$data['bulan']] = $data['total'];
        }
        // BUKAN NIAGA
        $rs_id = $this->m_dashboard->get_data_chart_fa(array('bukan niaga'));
        $data_bukan_niaga = array();
        foreach ($rs_id as $data) {
            $data_bukan_niaga[$data['bulan']] = $data['total'];
        }
        // strxml
        $str_xml = "<chart caption='' subcaption='' yaxisname='Jumlah' xaxisname='" . date('Y') . "' plotgradientcolor='' 
                bgcolor='FFFFFF' showalternatehgridcolor='0' showplotborder='0'
                divlinecolor='#CCCCCC' showvalues='0' showborder='0' showcanvasborder='0'
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

}
