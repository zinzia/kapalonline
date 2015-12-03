<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/StakeholderBase.php' );

// --

class rekapitulasi extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_dashboard_stakeholder');
        $this->load->model('m_account');
        $this->load->model('m_operator');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // routes
    public function index() {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "stakeholder/rekapitulasi/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
        $this->smarty->load_style("select2/select2.css");
        // get search parameter
        $search = $this->tsession->userdata('search_rekapitulasi');
        if (!empty($search)) {
            $this->smarty->assign("search", $search);
        }
        // search parameters
        $tanggal_from = empty($search['tanggal_from']) ? date("Y-m-d") : $search['tanggal_from'];
        $tanggal_to = empty($search['tanggal_to']) ? date("Y-m-d") : $search['tanggal_to'];
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_type = empty($search['data_type']) ? 'berjadwal' : $search['data_type'];
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $services_cd = empty($search['services_cd']) ? '%' : '%' . $search['services_cd'] . '%';
        $search['tanggal_from'] = $tanggal_from;
        $search['tanggal_to'] = $tanggal_to;
        $search['data_type'] = $data_type;
        // assign
        $this->smarty->assign("search", $search);
        // get stakeholder airport
        $result = $this->m_dashboard_stakeholder->get_user_stakeholder_iata(array($this->com_user['user_id']));
        foreach ($result as $value) {
            $airport[] = $value['airport_iata_cd'];
        }
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("report/fa_nb/index/");
        $config['total_rows'] = $this->m_dashboard_stakeholder->get_total_report($airport, array($tanggal_from, $tanggal_to, $tanggal_to, $tanggal_from, $published_no, $published_no, $data_type, $data_flight, $airlines_nm, $services_cd));
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
        // list my list fa
        $rs_id = $this->m_dashboard_stakeholder->get_list_fa($airport, array($tanggal_from, $tanggal_to, $tanggal_to, $tanggal_from, $published_no, $published_no, $data_type, $data_flight, $airlines_nm, $services_cd, ($start - 1), $config['per_page']));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("no", 1);
        // get list airlines
        $this->smarty->assign("rs_airlines", $this->m_dashboard_stakeholder->get_list_airlines());
        // get list services
        $this->smarty->assign("rs_services", $this->m_dashboard_stakeholder->get_list_services());
        // output
        parent::display();
    }

    // proses pencarian rekapitulasi
    public function proses_cari_rekapitulasi() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_rekapitulasi');
        } else {
            $params = array(
                "tanggal_from" => $this->input->post("tanggal_from"),
                "tanggal_to" => $this->input->post("tanggal_to"),
                "published_no" => $this->input->post("published_no"),
                "data_type" => $this->input->post("data_type"),
                "data_flight" => $this->input->post("data_flight"),
                "airlines_nm" => $this->input->post("airlines_nm"),
                "services_cd" => $this->input->post("services_cd"),
            );
            $this->tsession->set_userdata("search_rekapitulasi", $params);
        }
        // redirect
        redirect("stakeholder/rekapitulasi/rekapitulasi");
    }

    // form
    public function form($data_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "stakeholder/realisasi/form.html");
        // get detail data
        $params = array($data_id);
        $result = $this->m_dashboard_stakeholder->get_detail_data_by_id($params);
        if (empty($result)) {
            redirect('stakeholder/welcome');
        }
        // assign
        $this->smarty->assign("result", $result);
        $this->smarty->assign("result_rute", $this->m_dashboard_stakeholder->get_data_rute_by_id($params));
        $this->smarty->assign("total_rute", COUNT($this->m_dashboard_stakeholder->get_data_rute_by_id($params)));
        $this->smarty->assign("no", 1);
        // get remark field
        $this->smarty->assign("remark_field", $this->m_dashboard_stakeholder->get_remark_field(array($result['data_type'], $result['data_flight'], $result['services_cd'])));
        // output
        parent::display();
    }

    // download
    public function download_report() {
        // set page rules
        $this->_set_page_rule("R");
        //load library
        $this->load->library('phpexcel');
        // --
        error_reporting(0);
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        // get search parameter
        $search = $this->tsession->userdata('search_rekapitulasi');
        // search parameters
        $tanggal_from = empty($search['tanggal_from']) ? date("Y-m-d") : $search['tanggal_from'];
        $tanggal_to = empty($search['tanggal_to']) ? date("Y-m-d") : $search['tanggal_to'];
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_type = empty($search['data_type']) ? '%berjadwal%' : '%' . $search['data_type'] . '%';
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $services_cd = empty($search['services_cd']) ? '%' : '%' . $search['services_cd'] . '%';
        // get stakeholder airport
        $result = $this->m_dashboard_stakeholder->get_user_stakeholder_iata(array($this->com_user['user_id']));
        foreach ($result as $value) {
            $airport[] = $value['airport_iata_cd'];
        }

        // get list
        $rs_id = $this->m_dashboard_stakeholder->get_all_data_report($airport, array($tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $published_no, $data_type, $data_flight, $airlines_nm, $services_cd, 1, 2));

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
        foreach ($rs_id as $data) {
            switch ($data['payment_st']) {
                case '00':
                    $payment_st = "Belum Bayar";
                    break;
                case '01':
                    $payment_st = "Kurang Bayar";
                    break;
                case '02':
                    $payment_st = "Lebih Bayar";
                    break;
                case '11':
                    $payment_st = "Lunas";
                    break;
                default:
                    $payment_st = "Tidak Bayar";
                    break;
            }
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['published_no']);
            $objWorksheet->setCellValue('D' . $i, $data['airlines_nm']);
            $objWorksheet->setCellValue('E' . $i, strtoupper($data['data_type']) . " " . strtoupper($data['data_flight']));
            $objWorksheet->setCellValue('F' . $i, $data['date_start'] . ' / ' . $data['date_end']);
            $objWorksheet->setCellValue('G' . $i, $data['rute_all']);
            $objWorksheet->setCellValue('H' . $i, $data['services_nm']);
            $objWorksheet->setCellValue('I' . $i, $data['registration'] . " / " . $data['flight_no']);
            $objWorksheet->setCellValue('J' . $i, $payment_st);

            // style
            $objWorksheet->getStyle($kolom[0] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[1] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[2] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[3] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[4] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[5] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[6] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[7] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[8] . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }

        // output file
        $file_name = 'REKAPITULASI_FLIGHT_APPROVAL';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

}
