<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class pembayaran_aunb extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_pembayaran');
        // load library
        $this->load->library('pagination');
        $this->load->library('tnotification');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/pembayaran_aunb/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_pembayaran');
        // search parameters
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $airlines_nm = empty($search['airlines_nm']) ? '%' : '%' . $search['airlines_nm'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("task/pembayaran_aunb/index/");
        $config['total_rows'] = $this->m_pembayaran->get_total_awaiting_task_berjadwal(array($published_no, $airlines_nm, "berjadwal"));
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
        $params = array($published_no, $airlines_nm, "berjadwal", ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_pembayaran->get_list_awaiting_task_berjadwal($params));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_pembayaran');
        } else {
            $params = array(
                "published_no" => $this->input->post("published_no"),
                "airlines_nm" => $this->input->post("airlines_nm")
            );
            $this->tsession->set_userdata("search_pembayaran", $params);
        }
        // redirect
        redirect("task/pembayaran_aunb");
    }

    // form pembayaran
    function form_pembayaran() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/pembayaran_aunb/form.html");
        // published no
        $x = 1;
        $data_id = $this->input->post('data_id');
        foreach ($data_id as $value) {
            $result = $this->m_pembayaran->get_published_no_detail(array($value));
            if ($result) {
                $data[] = array(
                    "data_id"       => $result['data_id'],
                    "published_no"  => $result['published_no'],
                    "airlines_nm"   => $result['airlines_nm'],
                    "data_type"     => $result['data_type'],
                    "data_flight"   => $result['data_flight'],
                );
            }
        }
        $this->smarty->assign("rs_id", $data);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pembayaran
    function pembayaran_process($data_id) {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('payment_invoice', 'Nomor Nota Pembayaran', 'trim|required|maxlength[100]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $data_id = $this->input->post('data_id');
            foreach ($data_id as $value) {
                $params = array(
                    "payment_st"        => "lunas",
                    "payment_date"      => date('Y-m-d H:i:s'),
                    "payment_invoice"   => $this->input->post('payment_invoice'),
                    "mdb_payment"       => $this->com_user['user_id']
                );
                $where = array(
                    "data_id"       => $value
                );
                $this->m_pembayaran->update_pembayaran($params, $where);
            }
            // cetak invoice
            // $this->cetak_invoice($this->input->post('payment_invoice'));
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data pembayaran berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data pembayaran gagal disimpan");
            // redirect
            redirect("task/pembayaran_aunb/form_pembayaran/");
        }
        // redirect
        redirect("task/pembayaran_aunb/cetak_pembayaran/" . $this->input->post('payment_invoice'));
    }

    // cetak pembayaran
    function cetak_pembayaran($payment_invoice) {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "task/pembayaran_aunb/invoice.html");
        // detail invoice
        $result = $this->m_pembayaran->get_detail_invoice(array($payment_invoice));
        $this->smarty->assign("result", $result);
        // rs id
        $rs_id = $this->m_pembayaran->get_fa_by_invoice_no(array($payment_invoice));
        foreach ($rs_id as $value) {
            // get detail rute
            $rs_rute = $this->m_pembayaran->get_data_rute_by_id(array($value['data_id']));
            $total_rute = COUNT($rs_rute);
            $list_rute = "";
            $x = 1;
            foreach ($rs_rute as $rute) {
                $x++;
                $list_rute .= $rute['airport_iata_cd'];
                if ($x <= $total_rute) {
                    $list_rute .= "-";
                }
            }
            $data[] = array(
                "data_id"       => $value['data_id'],
                "published_no"  => $value['published_no'],
                "aircraft_type" => $value['aircraft_type'],
                "flight_no"     => $value['flight_no'],
                "rute_all"      => $list_rute,
                "services_nm"   => $value['services_nm'],
            );
        }
        $this->smarty->assign("rs_id", $data);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // cetak invoice
    function cetak_invoice($payment_invoice) {
        // set page rules
        $this->_set_page_rule("C");
        // load library
        $this->load->library('tcpdf');
        // get all fa by invoice no
        $result = $this->m_pembayaran->get_detail_invoice(array($payment_invoice));
        $rs_id = $this->m_pembayaran->get_fa_by_invoice_no(array($payment_invoice));
        foreach ($rs_id as $value) {
            // get detail rute
            $rs_rute = $this->m_pembayaran->get_data_rute_by_id(array($value['data_id']));
            $total_rute = COUNT($rs_rute);
            $list_rute = "";
            $x = 1;
            foreach ($rs_rute as $rute) {
                $x++;
                $list_rute .= $rute['airport_iata_cd'];
                if ($x <= $total_rute) {
                    $list_rute .= "-";
                }
            }
            $data[] = array(
                "data_id"       => $value['data_id'],
                "published_no"  => $value['published_no'],
                "aircraft_type" => $value['aircraft_type'],
                "flight_no"     => $value['flight_no'],
                "rute_all"      => $list_rute,
                "services_nm"   => $value['services_nm'],
            );
        }
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);
        // add a page
        $this->tcpdf->AddPage();
        // create pdf
        $html = '';
        $no = 1;
        // content
        $html .= '
            <style type="text/css">
                .table-input {
                    margin: 0 0 5px 0;
                    padding: 5px;
                    background-color: #fff;
                    border-collapse: collapse;
                    color: #666;
                    font-size: 26px;
                    text-align: left;
                    vertical-align: top;
                }

                .table-input th {
                    margin: 0;
                    padding: 5px 10px;
                    background-color: #4EA1D3;
                    border-bottom: 1px solid #5699C3;
                    border-top: 1px solid #7EA9BA;
                    color: #fff;
                    font-size: 28px;
                    vertical-align: top;
                }

                .table-form {
                    margin: 0;
                    padding: 0;
                    background-color: #fff;
                    border-collapse: collapse;
                    text-align: left;
                    font-family: tahoma;
                    font-size: 20px;
                }

                .table-form td {
                    margin: 0;
                    background-color: #FFFFFF;
                    vertical-align: middle;
                    font-size: 20px;
                }

                .table-signature {
                    margin: 0 0 5px 0;
                    padding: 0 0 30px 0;
                    background-color: #fff;
                    border-collapse: collapse;
                    color: #666;
                    font-size: 26px;
                    text-align: left;
                    vertical-align: top;
                }
            </style>

            <table class="table-input" width="100%">
                <tr class="headrow">
                    <th colspan="2">Nota Pembayaran</th>
                </tr>
                <tr>
                    <td width="25%"><b>Nomor Nota Pembayaran</b></td>
                    <td>: ' . $result['payment_invoice'] . '</td>
                </tr>
                <tr>
                    <td><b>Batas Pembayaran</b></td>
                    <td>: ' . $this->datetimemanipulation->get_full_date($result['payment_due_date']) . '</td>
                </tr>
                <tr>
                    <td><b>Waktu Dibayar</b></td>
                    <td>: ' . $this->datetimemanipulation->get_full_date($result['payment_date']) . '</td>
                </tr>
                <tr>
                    <td><b>Petugas</b></td>
                    <td>: ' . $result['operator_name'] . '</td>
                </tr>
            </table>

            <br />

            <table class="table-form" width="100%" cellpadding="1" border="1">
                <tr>
                    <td align="center" width="4%">No</td>
                    <td align="center" width="20%">Nomor FA</td>
                    <td align="center" width="16%">Tipe Pesawat</td>
                    <td align="center" width="20%">Nomor Penerbangan</td>
                    <td align="center" width="20%">Rute</td>
                    <td align="center" width="20%">Remark</td>
                </tr>
        ';
            foreach ($data as $value) {
                $html .= '
                    <tr>
                        <td align="center">' . $no++ . ' </td>
                        <td align="center">' . $value["published_no"] . ' </td>
                        <td align="center">' . $value["aircraft_type"] . ' </td>
                        <td align="center">' . $value["flight_no"] . ' </td>
                        <td align="center">' . $value["rute_all"] . ' </td>
                        <td align="center">' . $value["services_nm"] . ' </td>
                    </tr>
                ';
            }
        $html .= '
            </table>

            <br />

            <table class="table-signature" width="100%">
                <tr>
                    <td align="right"><b>' . $this->datetimemanipulation->get_full_date(date("Y-m-d")) . '</b></td>
                </tr>
                <tr>
                    <td align="right">' . $result['operator_name'] . '</td>
                </tr>
            </table>
        ';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $payment_invoice;
        $this->tcpdf->Output($filename . ".pdf", 'D');
        exit;
    }

}
