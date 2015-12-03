<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OnlineBase.php' );

// --

class statistik extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_dashboard');
        // exit
        redirect('home/welcome');
    }

    // view
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "information/statistik/index.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/fusioncharts/fusioncharts.js');
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'home_%'));
        // date
        $now['tanggal'] = $this->datetimemanipulation->get_full_date(date('Y-m-d'));
        $now['hari'] = date('l');
        $now['tahun'] = date('Y');
        $this->smarty->assign("tanggal_statistik", $now);
        // statistik
        $rs_id = $this->m_dashboard->get_data_statistik_pelayanan();
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
        $rs_id = $this->m_dashboard->get_data_statistik_pelayanan_belum_proses();
        $data = array();
        $total = 0;
        foreach ($rs_id as $value) {
            $data[str_replace(' ', '_', $value['data_type'])][$value['data_flight']] = $value['total'];
            $total += $value['total'];
        }
        $this->smarty->assign("data_belum", $data);
        $this->smarty->assign("total_belum", $total);
        //set CSRF token
        $csrf_token_nm = $this->security->get_csrf_token_name();
        $csrf_token = $this->security->get_csrf_hash();
        $this->tsession->set_userdata("token", $csrf_token);
        $this->smarty->assign("token_nm", $csrf_token_nm);
        $this->smarty->assign("token", $csrf_token);
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
        // statistik belum
        $rs_id = $this->m_dashboard->get_data_statistik_pelayanan_belum_proses();
        $data = array();
        $total = 0;
        foreach ($rs_id as $value) {
            $total += $value['total'];
        }
        // BUKAN NIAGA
        $rs_id = $this->m_dashboard->get_data_chart_pie();
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
