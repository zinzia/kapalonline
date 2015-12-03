<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class report_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('report/m_report_izin');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/report_izin/index.html");
        // get tahun
        $tahun = $this->m_report_izin->get_list_tahun_report(array($this->com_user['airlines_id']));
        $rs_tahun = array();
        for ($i = $tahun['mins']; $i <= $tahun['maks']; $i++) {
            $rs_tahun[] = $i;
        }
        $this->smarty->assign("rs_tahun", $rs_tahun);
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
        // get search parameter
        $search = $this->tsession->userdata('search_published');
        // search parameters
        $season_cd = empty($search['season_cd']) ? 'S15' : $search['season_cd'];
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
        $airport_iata_cd = empty($search['airport_iata_cd']) ? '%' : '%' . $search['airport_iata_cd'] . '%';
        // default
        $search['season_cd'] = empty($search['season_cd']) ? 'S15' : $search['season_cd'];
        $search['bulan'] = empty($search['bulan']) ? date('m') : $search['bulan'];
        $search['tahun'] = empty($search['tahun']) ? date('Y') : $search['bulan'];
        $search['data_flight'] = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
        // assign search
        $this->smarty->assign("search", $search);
        // no
        $no = 1;
        $this->smarty->assign("no", $no);
        // tanggal
        $tanggal = $tahun . '-' . $bulan . '-01';
        $tanggal = date("Y-m-t", strtotime(date($tanggal)));
        // params
        $params = array($this->com_user['airlines_id'], $data_flight, $season_cd, $tanggal, $airport_iata_cd, $airport_iata_cd);
        // list        
        $rs_id = $this->m_report_izin->get_izin_rute_data_by_airlines($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
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
                "season_cd" => $this->input->post("season_cd"),
                "bulan" => $this->input->post("bulan"),
                "tahun" => $this->input->post("tahun"),
                "data_flight" => $this->input->post("data_flight"),
                "airport_iata_cd" => $this->input->post("airport_iata_cd"),
            );
            $this->tsession->set_userdata("search_published", $params);
        }
        // redirect
        redirect("member/report_izin");
    }

    // detail
    public function detail($izin_rute_start = "", $izin_rute_end = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/report_izin/detail.html");
        $this->smarty->assign("izin_rute_start", $izin_rute_start);
        $this->smarty->assign("izin_rute_end", $izin_rute_end);
        // bulan
        $bulans = array(
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
        // parameter
        $search = $this->tsession->userdata('search_published');
        // search parameters
        $season_cd = empty($search['season_cd']) ? 'S15' : $search['season_cd'];
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
        // assign search
        $search['season_cd'] = $season_cd;
        $search['bulan_label'] = $bulans[$bulan];
        $search['tahun'] = $tahun;
        $this->smarty->assign("search", $search);
        // tanggal
        $tanggal = $tahun . '-' . $bulan . '-01';
        $tanggal = date("Y-m-t", strtotime(date($tanggal)));
        // styles
        $row_style[$izin_rute_start] = 'class="start-row"';
        $row_style[$izin_rute_end] = 'class="end-row"';
        // parse
        $this->smarty->assign("row_style", $row_style);
        /*
         * RUTE PENERBANGAN
         */
        $pairing = array();
        $frekuensi = array();
        $notes = array();
        $data = array();
        // params
        $params = array(
            $this->com_user['airlines_id'],
            $data_flight,
            $season_cd,
            $tanggal,
            $izin_rute_start, $izin_rute_start,
            $izin_rute_end, $izin_rute_end,
        );
        $rs_id = $this->m_report_izin->get_list_detail_izin_rute_by_params($params);
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_report_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // izin_published_letter
            $izin_published_letter[$izin_rute['izin_id']] = $izin_rute['izin_published_letter'];
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("frekuensi", $frekuensi);
        $this->smarty->assign("pairing", $pairing);
        $this->smarty->assign("izin_published_letter", $izin_published_letter);
        // output
        parent::display();
    }

    // download
    public function download($izin_rute_start = "", $izin_rute_end = "") {
        // set page rules
        $this->_set_page_rule("R");
        //load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->tcpdf->setPageOrientation('L');
        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);
        // add a page
        $this->tcpdf->AddPage();
        // bulan
        $bulans = array(
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
        // parameter
        $search = $this->tsession->userdata('search_published');
        // search parameters
        $season_cd = empty($search['season_cd']) ? 'S15' : $search['season_cd'];
        $bulan = empty($search['bulan']) ? date('m') : $search['bulan'];
        $tahun = empty($search['tahun']) ? date('Y') : $search['tahun'];
        $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
        // tanggal
        $tanggal = $tahun . '-' . $bulan . '-01';
        $tanggal = date("Y-m-t", strtotime(date($tanggal)));
        // styles
        $row_style[$izin_rute_start] = 'class="start-row"';
        $row_style[$izin_rute_end] = 'class="end-row"';
        /*
         * RUTE PENERBANGAN
         */
        $pairing = array();
        $frekuensi = array();
        $data = array();
        // params
        $params = array(
            $this->com_user['airlines_id'],
            $data_flight,
            $season_cd,
            $tanggal,
            $izin_rute_start, $izin_rute_start,
            $izin_rute_end, $izin_rute_end,
        );
        $rs_id = $this->m_report_izin->get_list_detail_izin_rute_by_params($params);
        $no = 1;
        foreach ($rs_id as $izin_rute) {
            $izin_data = $this->m_report_izin->get_list_izin_data_by_id(array($izin_rute['izin_id']));
            if (!empty($izin_data)) {
                // lebih dari 1 data
                $data[$no++] = $izin_data;
            } else {
                // hanya ada 1 data
                $data[$no++][0] = array('izin_id' => $izin_rute['izin_id']);
            }
            // total frekuensi
            $frekuensi[$izin_rute['izin_id']] = $izin_rute['total_frekuensi'];
            // pairing
            $pairing[$izin_rute['izin_id']] = $izin_rute['pairing'];
            // notes
            $izin_published_letter[$izin_rute['izin_id']] = $izin_rute['izin_published_letter'];
        }
        /*
         * HTML
         */
        $html = '<p style="font-size: 24px;"><b>PERIODE : ' . $season_cd . ' / ' . strtoupper($bulans[$bulan]) . ' ' . $tahun . '</b></p>';        // content
        $html .= '<table class="table-view" width="100%" style="font-size: 20px; font-family: arial; color: #333;" border="0.5" cellpadding="2">
                    <tr style="font-weight: bold; vertical-align: middle;">
                        <td width="4%" align="center">No</td>
                        <td width="8%" align="center">Rute</td>
                        <td width="7%" align="center">Tipe<br />Pesawat</td>
                        <td width="8%" align="center">Nomor<br />Penerbangan</td>
                        <td width="7%" align="center">ETD <br />( LT )</td>
                        <td width="7%" align="center">ETA <br />( LT )</td>
                        <td width="8%" align="center">Hari <br />Operasi</td>
                        <td width="8%" align="center">Frekuensi</td>
                        <td width="17%" align="center">Tanggal <br />Efektif</td>
                        <td width="8%" align="center">Total <br />Frekuensi</td>
                        <td width="18%" align="center">Nomor Penerbitan</td>
                    </tr>';
        // list rute {
        $total_frekuensi = 0;
        $kapasitas_minggu = 0;
        $kapasitas_tahun = 0;
        foreach ($data as $no => $izin_rute) {
            $i = 1;
            $rowspan = count($izin_rute);
            foreach ($izin_rute as $izin_data) {
                $kapasitas_minggu += $izin_data['frekuensi'] * $izin_data['capacity'];
                $kapasitas_tahun += $izin_data['frekuensi'] * $izin_data['capacity'] * 52;
                $html .= '<tr ' . $row_style[$izin_data['rute_all']] . '>';
                // no
                if ($rowspan <= 1) {
                    $html .= '<td align="center" style="color: black;">' . $no . '.</td>';
                } elseif ($i == 1) {
                    $html .= '<td align="center" rowspan="' . $rowspan . '" style="color: black;">' . $no . '.</td>';
                }
                // data
                $html .= '<td align="center">' . $izin_data['rute_all'] . '</td>';
                $html .= '<td align="center">' . $izin_data['tipe'] . '</td>';
                $html .= '<td align="center">' . $izin_data['flight_no'] . '</td>';
                $html .= '<td align="center">' . substr($izin_data['etd'], 0, 5) . '</td>';
                $html .= '<td align="center">' . substr($izin_data['eta'], 0, 5) . '</td>';
                $html .= '<td align="center">' . $izin_data['doop'] . '</td>';
                $html .= '<td align="center">' . $izin_data['frekuensi'] . 'X</td>';
                $html .= '<td align="center">' . strtoupper($this->datetimemanipulation->get_full_date($izin_data['start_date'], 'ins')) . ' / ' . strtoupper($this->datetimemanipulation->get_full_date($izin_data['end_date'], 'ins')) . '</td>';
                // total frekuensi
                if ($rowspan <= 1) {
                    $total_frekuensi += $frekuensi[$izin_data['izin_id']];
                    $html .= '<td align="center" style="color: blue;">' . $pairing[$izin_data['izin_id']] . ' / ' . $frekuensi[$izin_data['izin_id']] . 'X</td>';
                } elseif ($i == 1) {
                    $total_frekuensi += $frekuensi[$izin_data['izin_id']];
                    $html .= '<td align="center" style="color: blue;" rowspan="' . $rowspan . '" >' . $pairing[$izin_data['izin_id']] . ' / ' . $frekuensi[$izin_data['izin_id']] . 'X</td>';
                }
                // total frekuensi
                if ($rowspan <= 1) {
                    $html .= '<td align="center" style="color: blue;">' . $izin_published_letter[$izin_data['izin_id']] . '</td>';
                } elseif ($i == 1) {
                    $html .= '<td align="center" style="color: blue;" rowspan="' . $rowspan . '" >' . $izin_published_letter[$izin_data['izin_id']] . '</td>';
                }
                // --
                $html .= '</tr>';
                $i++;
            }
        }
        // total
        $html .= '<tr style="color: blue; background-color: #FFEFEF;">
                    <td colspan="9" align="center"><b>TOTAL</b></td>
                    <td align="center"><b>' . number_format($total_frekuensi, 0, ',', '.') . 'X</b></td>
                    <td align="center"></td>
                  </tr>';
        $html .= '</table>';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = str_replace('-', '_', $izin_rute_start) . '_' . $season_cd . '_' . $bulan . '_' . $tahun;
        $this->tcpdf->Output($filename . ".pdf", 'D');
    }

}
