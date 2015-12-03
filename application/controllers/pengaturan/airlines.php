<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class airlines extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_airlines');
        // load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // view
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airlines/list.html");
        // get search parameter
        $search = $this->tsession->userdata('airlines_search');
        // search parameters
        $airlines = empty($search['airlines']) ? '%' : '%' . $search['airlines'] . '%';
        $brand = empty($search['brand']) ? '%' : '%' . $search['brand'] . '%';
        $iata = empty($search['iata']) ? '%' : '%' . $search['iata'] . '%';
        $icao = empty($search['icao']) ? '%' : '%' . $search['icao'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/airlines/index/");
        $config['total_rows'] = $this->m_airlines->get_total_airlines(array($airlines, $brand, $iata, $icao));
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
        $params = array($airlines, $brand, $iata, $icao, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_airlines", $this->m_airlines->get_all_airlines($params));
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('airlines_search');
        } else {
            $params = array(
                "airlines" => $this->input->post("airlines"),
                "brand" => $this->input->post("brand"),
                "iata" => $this->input->post("iata"),
                "icao" => $this->input->post("icao")
            );
            $this->tsession->set_userdata("airlines_search", $params);
        }
        // redirect
        redirect("pengaturan/airlines");
    }

    // add airlines
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airlines/add.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        //get data
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // untuk hak akses
        $data = $this->tnotification->get_field_data();
        if (isset($data['airlines_siup[]']['postdata'])) {
            if (!empty($data['airlines_siup[]']['postdata'])) {
                // airlines_siup
                $this->smarty->assign("airlines_siup", $data['airlines_siup[]']['postdata']);
            }
        }
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('airlines_nm', 'Nama Operator Penerbangan', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airlines_brand', 'Nama Brand', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airlines_iata_cd', 'IATA Code', 'trim|required|max_length[10]');
        $this->tnotification->set_rules('airlines_icao_cd', 'ICAO Code', 'trim|required|max_length[10]');
        $this->tnotification->set_rules('airlines_contact', 'Nomor Kontak', 'trim');
        $this->tnotification->set_rules('airlines_website', 'Website', 'trim');
        $this->tnotification->set_rules('airlines_address', 'Alamat', 'trim');
        $this->tnotification->set_rules('airlines_st', 'Status Pengoperasian', 'trim|required');
        $this->tnotification->set_rules('airlines_type', 'Tipe', 'trim|required');
        $this->tnotification->set_rules('airlines_flight_type', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('airlines_nationality', 'Milik Perusahaan', 'trim|required');
        $this->tnotification->set_rules('airlines_siup[]', 'Jenis SIUP', 'trim|required');

        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('airlines_nm'),
                $this->input->post('airlines_brand'),
                $this->input->post('airlines_iata_cd'),
                $this->input->post('airlines_icao_cd'),
                $this->input->post('airlines_contact'),
                $this->input->post('airlines_website'),
                $this->input->post('airlines_address'),
                $this->input->post('airlines_st'),
                $this->input->post('airlines_type'),
                $this->input->post('airlines_flight_type'),
                $this->input->post('airlines_nationality'),
                $this->com_user['user_id']);
            // insert
            if ($this->m_airlines->insert($params)) {
                // get last id
                $last_id = $this->m_airlines->get_last_inserted_id();
                $zero = "";
                for ($i = strlen($last_id); $i < 3; $i++) { 
                    $zero .= "0";
                }
                $new_last_id = $zero . $last_id;
                // update va number
                $params = array(
                    "virtual_account"   => '8022' . $new_last_id . '000000000',
                );
                $where = array(
                    "airlines_id"   => $new_last_id
                );
                $this->m_airlines->update_va($params, $where);
                // airlines siup
                $airlines_siup = $this->input->post('airlines_siup');
                foreach ($airlines_siup as $value) {
                    $params_siup[] = array(
                        "airlines_id"   => $last_id,
                        "airlines_type" => $value,
                    );
                }
                // delete airlines siup
                $this->m_airlines->delete_siup(array($this->input->post('airlines_id')));
                // insert airlines siup
                $this->m_airlines->insert_siup($params_siup);
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/airlines/add/");
    }

    // edit airlines
    public function edit($airlines_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airlines/edit.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load style ui
        $this->smarty->load_style("select2/select2.css");
        // get data
        $this->smarty->assign("result", $this->m_airlines->get_airlines_by_id($airlines_id));
        // get airlines siup
        $rs_siup = $this->m_airlines->get_airlines_siup_by_id($airlines_id);
        foreach ($rs_siup as $value) {
            $data[$value['idx']] = $value['airlines_type'];
        }
        $result['rs_siup'] = $data;
        $this->smarty->assign("airlines_siup", $result['rs_siup']);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // untuk hak akses
        $data = $this->tnotification->get_field_data();
        if (isset($data['airlines_siup[]']['postdata'])) {
            if (!empty($data['airlines_siup[]']['postdata'])) {
                // airlines_siup
                $this->smarty->assign("airlines_siup", $data['airlines_siup[]']['postdata']);
            }
        }
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('airlines_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('airlines_nm', 'Nama Operator Penerbangan', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airlines_brand', 'Nama Brand', 'trim|required|max_length[50]');
        $this->tnotification->set_rules('airlines_iata_cd', 'IATA Code', 'trim|required|max_length[10]');
        $this->tnotification->set_rules('airlines_icao_cd', 'ICAO Code', 'trim|required|max_length[10]');
        $this->tnotification->set_rules('airlines_contact', 'Nomor Kontak', 'trim');
        $this->tnotification->set_rules('airlines_website', 'Website', 'trim');
        $this->tnotification->set_rules('airlines_address', 'Alamat', 'trim');
        $this->tnotification->set_rules('airlines_st', 'Status Pengoperasian', 'trim|required');
        $this->tnotification->set_rules('airlines_type', 'Tipe', 'trim|required');
        $this->tnotification->set_rules('airlines_flight_type', 'Jenis Penerbangan', 'trim|required');
        $this->tnotification->set_rules('airlines_nationality', 'Milik Perusahaan', 'trim|required');
        $this->tnotification->set_rules('airlines_siup[]', 'Jenis SIUP', 'trim|required');

        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array(
                $this->input->post('airlines_nm'),
                $this->input->post('airlines_brand'),
                $this->input->post('airlines_iata_cd'),
                $this->input->post('airlines_icao_cd'),
                $this->input->post('airlines_address'),
                $this->input->post('airlines_contact'),
                $this->input->post('airlines_website'),
                $this->input->post('airlines_st'),
                $this->input->post('airlines_type'),
                $this->input->post('airlines_flight_type'),
                $this->input->post('airlines_nationality'),
                $this->com_user['user_id'],
                $this->input->post('airlines_id'));
            if ($this->m_airlines->update($params)) {
                // airlines siup
                $airlines_siup = $this->input->post('airlines_siup');
                foreach ($airlines_siup as $value) {
                    $params_siup[] = array(
                        "airlines_id"   => $this->input->post('airlines_id'),
                        "airlines_type" => $value,
                    );
                }
                // delete airlines siup
                $this->m_airlines->delete_siup(array($this->input->post('airlines_id')));
                // insert airlines siup
                $this->m_airlines->insert_siup($params_siup);
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                redirect("pengaturan/airlines/edit/" . $this->input->post('airlines_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/airlines/edit/" . $this->input->post('airlines_id'));
    }

    // hapus form
    public function delete($airlines_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/airlines/delete.html");
        // get data
        $this->smarty->assign("result", $this->m_airlines->get_airlines_by_id($airlines_id));
        // get airlines siup
        $rs_siup = $this->m_airlines->get_airlines_siup_by_id($airlines_id);
        $i = 1;
        foreach ($rs_siup as $value) {
            $data[$i++] = $value['airlines_type'];
        }
        $result['rs_siup'] = $data;
        $this->smarty->assign("airlines_siup", $result['rs_siup']);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // hapus process
    public function delete_process() {
        // set page rules
        $this->_set_page_rule("D");
        // cek input
        $this->tnotification->set_rules('airlines_id', 'Airlines ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('airlines_id'));
            // insert
            if ($this->m_airlines->delete($params)) {
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/airlines");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("pengaturan/airlines/delete/" . $this->input->post('airlines_id'));
    }

    // download format
    public function download() {
        // set page rules
        $this->_set_page_rule("R");
        //load library
        $this->load->library('phpexcel');
        // --
        error_reporting(0);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        // get data
        // create excel
        $filepath = "resource/doc/template/CETAK_OPERATOR.xlsx";
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
        // parameters
        $no = 1;
        $row = 4;

        $kolom = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');

        // get search parameter
        $search = $this->tsession->userdata('airlines_search');
        // search parameters
        $airlines = empty($search['airlines']) ? '%' : '%' . $search['airlines'] . '%';
        $brand = empty($search['brand']) ? '%' : '%' . $search['brand'] . '%';
        $iata = empty($search['iata']) ? '%' : '%' . $search['iata'] . '%';
        $icao = empty($search['icao']) ? '%' : '%' . $search['icao'] . '%';

        $total_data = intval($this->m_airlines->get_total_airlines(array($airlines, $brand, $iata, $icao)));
        $params = array($airlines, $brand, $iata, $icao, 0, $total_data);
        $rs_data = $this->m_airlines->get_all_airlines($params);


        foreach ($rs_data as $rec) {
            for ($j = 0; $j < 8; $j++) {
                $objWorksheet->getStyle($kolom[$j] . $row)->applyFromArray($styleBorder);
            }

            $airlines_nm = empty($rec['airlines_nm']) ? '' : $rec['airlines_nm'];
            $airlines_brand = empty($rec['airlines_brand']) ? '' : $rec['airlines_brand'];
            $airlines_iata_cd = empty($rec['airlines_iata_cd']) ? '' : $rec['airlines_iata_cd'];
            $airlines_icao_cd = empty($rec['airlines_icao_cd']) ? '' : $rec['airlines_icao_cd'];
            //
            $airlines_flight_type = empty($rec['airlines_flight_type']) ? '' : $rec['airlines_flight_type'];
            $airlines_flight_type = strtoupper(str_replace('_', ' ', $airlines_flight_type));
            //
            $airlines_type = empty($rec['airlines_type']) ? '' : $rec['airlines_type'];
            //
            $airlines_st = empty($rec['airlines_st']) ? '' : $rec['airlines_st'];
            $airlines_st = strtoupper(str_replace('_', ' ', $airlines_st));

            $objWorksheet->setCellValue('B' . $row, $no);
            $objWorksheet->setCellValue('C' . $row, $airlines_nm);
            $objWorksheet->setCellValue('D' . $row, $airlines_brand);
            $objWorksheet->setCellValue('E' . $row, $airlines_iata_cd);
            $objWorksheet->setCellValue('F' . $row, $airlines_icao_cd);
            $objWorksheet->setCellValue('G' . $row, $airlines_flight_type);
            $objWorksheet->setCellValue('H' . $row, strtoupper($airlines_type));
            $objWorksheet->setCellValue('I' . $row, $airlines_st);

            $no++;
            $row++;
        }

        // output file
        $file_name = 'DAFTAR_OPERATOR_PENERBANGAN';
        //--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name . '.xlsx');
        header('Cache-Control: max-age=0');
        // output
        $obj_writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $obj_writer->save('php://output');
    }

}
