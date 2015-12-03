<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class reject extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_reject');
        // load library
        $this->load->library('pagination');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/reject/index.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-ui-1.9.2.custom.min.js");
        $this->smarty->load_javascript("resource/js/jquery/jquery.ui.timepicker.js");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("jquery.ui/redmond/jquery.ui.timepicker.css");
        $this->smarty->load_style("jquery.ui/redmond/jquery-ui-1.8.13.custom.css");
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
        // get search parameter
        $search = $this->tsession->userdata('search_reject');
        // search parameters
        $tanggal_from = empty($search['tanggal_from']) ? date("Y-m-d") : $search['tanggal_from'];
        $tanggal_to = empty($search['tanggal_to']) ? date("Y-m-d") : $search['tanggal_to'];
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_type = empty($search['data_type']) ? '%' : '%' . $search['data_type'] . '%';
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        $search['tanggal_from'] = $tanggal_from;
        $search['tanggal_to'] = $tanggal_to;
        // assign
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/reject/index/");
        $config['total_rows'] = $this->m_reject->get_total_finished_task(array($this->com_user['airlines_id'], $tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $data_type, $data_flight));
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
        $params = array($this->com_user['airlines_id'], $tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $data_type, $data_flight, ($start - 1), $config['per_page']);
        $rs_id = $this->m_reject->get_list_finished_task($params);
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
            $this->tsession->unset_userdata('search_reject');
        } else {
            $params = array(
                "tanggal_from" => $this->input->post("tanggal_from"),
                "tanggal_to" => $this->input->post("tanggal_to"),
                "published_no" => $this->input->post("published_no"),
                "data_type" => $this->input->post("data_type"),
                "data_flight" => $this->input->post("data_flight"),
            );
            $this->tsession->set_userdata("search_reject", $params);
        }
        // redirect
        redirect("member/reject");
    }

    // laporan project
    public function print_out() {
        //set page rule
        $this->_set_page_rule("R");
        // get search parameter
        $search = $this->tsession->userdata('search_reject');
        // search parameters
        $tanggal_from = empty($search['tanggal_from']) ? date("Y-m-d") : $search['tanggal_from'];
        $tanggal_to = empty($search['tanggal_to']) ? date("Y-m-d") : $search['tanggal_to'];
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_type = empty($search['data_type']) ? '%' : '%' . $search['data_type'] . '%';
        $data_flight = empty($search['data_flight']) ? '%' : '%' . $search['data_flight'] . '%';
        // get list
        $params = array($this->com_user['airlines_id'], $tanggal_from, $tanggal_to, $tanggal_from, $tanggal_to, $published_no, $data_type, $data_flight);
        $rs_id = $this->m_reject->get_list_finished_task_print($params);
        if (empty($rs_id)) {
            // default redirect
            redirect("member/reject");
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
            $objWorksheet->setCellValue('D' . $i, "-");
            $objWorksheet->setCellValue('E' . $i, $data['aircraft_type']);
            $objWorksheet->setCellValue('F' . $i, strtoupper($data['data_type']) . ' ' . strtoupper($data['data_flight']));
            $objWorksheet->setCellValue('G' . $i, $data['date_start'] . ' - ' . $data['date_end']);
            $objWorksheet->setCellValue('H' . $i, $data['registration']);
            $objWorksheet->setCellValue('I' . $i, $data['flight_no']);
            $objWorksheet->setCellValue('J' . $i, strtoupper($data['rute_all']));
            $objWorksheet->setCellValue('K' . $i, "-");
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
        $file_name = "LAPORAN_FLIGHT_APPROVAL_DITOLAK";
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
