<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class log extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_report_log');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "report/log/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_report_log');
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
        $operator_name = empty($search['operator_name']) ? '%' : '%' . $search['operator_name'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("report/log/index/");
        $config['total_rows'] = $this->m_report_log->get_total_report_log(array($operator_name, $airlines_nm));
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
        $params = array($operator_name, $airlines_nm, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_report_log->get_list_report_log($params));
        // assign
        $rs_airlines = $this->m_report_log->get_all_airlines();
        $this->smarty->assign("rs_airlines", $rs_airlines);
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_report_log');
        } else {
            $params = array(
                "operator_name" => $this->input->post("operator_name"),
                "airlines_nm" => $this->input->post("airlines_nm"),
            );
            $this->tsession->set_userdata("search_report_log", $params);
        }
        // redirect
        redirect("report/log");
    }

    // download
    public function download() {
        // set page rules
        $this->_set_page_rule("R");
        //load library
        $this->load->library('phpexcel');
        // --
        // error_reporting(0);
        // set_time_limit(0);
        // ini_set('memory_limit', '-1');
        // get search parameter
        $search = $this->tsession->userdata('search_report_log');
        // search parameters
        $operator_name = empty($search['operator_name']) ? '%' : '%' . $search['operator_name'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';

        // get list
        $params = array($operator_name, $airlines_nm);
        $rs_data = $this->m_report_log->get_all_report_log($params);

        // get data
        // create excel
        $filepath = "resource/doc/template/TEMPLATE_REPORT_LOG.xlsx";
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
        $objWorksheet->setCellValue('G3', $this->datetimemanipulation->get_full_date(date('Y-m-d')));

        $no = 1;
        $i = 6;
        $kolom = array('B', 'C', 'D', 'E', 'F', 'G');
        foreach ($rs_data as $data) {
            $objWorksheet->setCellValue('B' . $i, $no++);
            $objWorksheet->setCellValue('C' . $i, $data['operator_name']);
            $objWorksheet->setCellValue('D' . $i, $data['airlines_nm']);
            $objWorksheet->setCellValue('E' . $i, $this->datetimemanipulation->get_full_date($data['login_date']));
            $objWorksheet->setCellValue('F' . $i, $this->datetimemanipulation->get_full_date($data['logout_date']));
            $objWorksheet->setCellValue('G' . $i, $data['ip_address']);

            // style
            $objWorksheet->getStyle($kolom[0] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[1] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[2] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[3] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[4] . $i)->applyFromArray($styleBorder);
            $objWorksheet->getStyle($kolom[5] . $i)->applyFromArray($styleBorder);
            // --
            $i++;
        }

        // output file
        $file_name = 'REKAPITULASI_USER_LOG';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

}
