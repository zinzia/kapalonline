<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class payment extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('m_payment');
        $this->load->model("m_preferences");
        // load library
        $this->load->library('pagination');
        $this->load->library('email');
        $this->load->library('tnotification');
        $this->load->library('datetimemanipulation');
        $this->load->library('phpexcel');
        //load helper
        $this->load->helper("terbilang_helper");
        //load js
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.3.js");
        $this->smarty->load_javascript('resource/js/jquery.blockui/jquery.blockui.js');
        $this->smarty->load_javascript('resource/js/jquery.runningdot/jquery.runningdot.js');
    }

    /*
     * WAITING
     */

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_payment');
        // search parameters
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['published_no'] . '%';
        $data_flight = empty($search['data_flight']) ? 'domestik' : '%' . $search['data_flight'] . '%';
        $this->smarty->assign("search", $search);
        // list waiting
        $rs_id = $this->m_payment->get_list_awaiting_task_berjadwal(array($published_no, $this->com_user['airlines_id'], $data_flight));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", count($rs_id));
        // tarif
        $tarif = $this->m_payment->get_tabel_tarif_fa(array($this->com_user['airlines_id']));
        $this->smarty->assign("tarif", $tarif);
        // draft
        $total_draft = $this->m_payment->get_total_invoices_open_by_airlines(array($this->com_user['airlines_id']));
        $this->smarty->assign("total_draft", $total_draft);
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
            $this->tsession->unset_userdata('search_payment');
        } else {
            $params = array(
                "published_no" => $this->input->post("published_no"),
                "data_flight" => $this->input->post("data_flight"),
            );
            $this->tsession->set_userdata("search_payment", $params);
        }
        // redirect
        redirect("member/payment");
    }

    // process invoices 
    public function create_invoices_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('data_id', '/ pilihan FA', 'required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $data = $this->input->post('data_id');
            // tarif
            $tarif = $this->m_payment->get_tabel_tarif_fa(array($this->com_user['airlines_id']));
            $search = $this->tsession->userdata('search_payment');
            $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
            // get last invoice no transaction
            $invoice_no = $this->m_payment->get_last_fa_invoice_no($this->com_user["airlines_id"]);
            // get inv id
            $inv_id = $this->m_payment->get_invoice_id();
            $this->load->library("simponi");
            //get kode billing
            try {
                //get preferences
                $apps_id_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "fa_app_id"));
                $route_id_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "default_channel"));
                $kode_kl_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_kl"));
                $kode_es1_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_eselon1"));
                $kode_satker_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_satker"));
                $kode_tarif_fa_pref_domestik = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_tarif_fa_domestik"));
                $kode_tarif_fa_pref_internasional = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_tarif_fa_internasional"));
                $kode_pp_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_pp"));
                $kode_akun_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_akun"));
                $tarif_fa_domestik = $this->m_preferences->get_preferences_by_group_and_name(array("tarif_fa", "domestik"));
                $tarif_fa_internasional = $this->m_preferences->get_preferences_by_group_and_name(array("tarif_fa", "internasional"));

                $user_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "user_id"));
                $pass_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "password"));

                //get total invoice
                $total = 0;
                $kode_tarif_fa = "";
                $tarif_fa = 0;
                //set payment detail list
                $paymentDetails = new PaymentDetailList();
                if ($data_flight == "domestik") {
                    $kode_tarif_fa = $kode_tarif_fa_pref_domestik["pref_value"];
                    $tarif_fa = $tarif_fa_domestik["pref_value"];
                } else {
                    $kode_tarif_fa = $kode_tarif_fa_pref_internasional["pref_value"];
                    $tarif_fa = $tarif_fa_internasional["pref_value"];
                }
                $arr_due_date = array();
                foreach ($data as $data_id) {
                    $total+=$tarif[$data_flight];
                    //set payment detail
                    $paymentDetail = new PaymentDetail();
                    $paymentDetail->NamaWajibBayar = $this->com_user["airlines_nm"];
                    $paymentDetail->KodeTarifSimponi = $kode_tarif_fa;
                    $paymentDetail->KodePPSimponi = $kode_pp_pref["pref_value"];
                    $paymentDetail->KodeAkun = $kode_akun_pref["pref_value"];
                    $paymentDetail->TarifPNBP = $tarif_fa;
                    $paymentDetail->Volume = 1;
                    $paymentDetail->Satuan = "per flight approval";
                    $paymentDetail->TotalTarifPerRecord = $tarif_fa;
                    //add payment detail to list
                    $paymentDetails->PaymentDetail[] = $paymentDetail;
                }
                //create ws client
                $wsClient = new SimponiBRIService();
                //set request params
                $requestParams = new PaymentRequest();
                $requestParams->appsId = $apps_id_pref["pref_value"];
                $requestParams->invoiceNo = $invoice_no;
                $requestParams->routeId = $route_id_pref["pref_value"];
                //set payment header
                $date = new DateTime();
                $currdate = $date->format("Y-m-d H:i:s");
                $extdate = $date->add(new DateInterval('P2D'));
                $expdate = $extdate->format("Y-m-d H:i:s");

                $paymentHeader = new PaymentHeader();
                $paymentHeader->TrxId = $inv_id;
                $paymentHeader->UserId = $user_pref["pref_value"];
                $paymentHeader->Password = $pass_pref["pref_value"];
                $paymentHeader->ExpiredDate = $expdate;
                $paymentHeader->DateSent = $currdate;
                $paymentHeader->KodeKL = $kode_kl_pref["pref_value"];
                $paymentHeader->KodeEselon1 = $kode_es1_pref["pref_value"];
                $paymentHeader->KodeSatker = $kode_satker_pref["pref_value"];
                $paymentHeader->JenisPNBP = "F";
                $paymentHeader->KodeMataUang = "1";
                $paymentHeader->TotalNominalBilling = $total;
                $paymentHeader->NamaWajibBayar = $this->com_user["airlines_nm"];
                $paymentData = new requestData($paymentHeader, $paymentDetails);
                //set request params : payment data
                $requestParams->data = $paymentData;
                //get responses
                $simponiResponse = $wsClient->PaymentRequest($requestParams);
                $response = $simponiResponse->response;
                //get simponi data
                $simponiData = $response->simponiData;
                if ($response->code == "00") {
                    // params invoices
                    $params = array(
                        "inv_id" => $inv_id,
                        "airlines_id" => $this->com_user['airlines_id'],
                        "category" => "1",
                        "virtual_account" => $simponiData->KodeBillingSimponi,
                        "invoice_no" => $invoice_no,
                        "inv_st" => "open",
                        "inv_date" => $currdate,
                        "inv_due_date" => $expdate,
                        "generate_st" => "no",
                        "mdb" => $this->com_user['user_id'],
                        "mdd" => date('Y-m-d H:i:s'),
                    );

                    // insert invoices
                    $total_inv = 0;
                    if ($this->m_payment->insert_invoice($params)) {
                        foreach ($data as $value) {
                            $params = array(
                                "00",
                                $currdate,
                                $simponiData->KodeBillingSimponi,
                                $tarif[$data_flight],
                                $this->com_user['user_id'],
                                $value
                            );
                            $total_inv += $tarif[$data_flight];
                            if ($this->m_payment->is_fa_already_billed($value) == "0") {
                                $this->m_payment->update_fa_kode_billing($params);
                                //set invoice detail params
                                $detail_params = array(
                                    "detail_id" => $this->m_payment->get_invoice_detail_id(),
                                    "register_id" => $value,
                                    "virtual_account" => $simponiData->KodeBillingSimponi,
                                    "amount" => $tarif[$data_flight],
                                    "mdd" => date("Y-m-d h:i:s")
                                );
                                $this->m_payment->insert_invoice_detail($detail_params);
                            } else {
                                $this->tnotification->sent_notification("error", "Permohonan sudah memiliki kode billing !");
                                redirect("member/payment");
                            }
                        }
                        // update invoices
                        $params = array(
                            "inv_st" => "pending",
                            "inv_total" => $total_inv,
                        );
                        $where = array(
                            "inv_id" => $inv_id,
                            "airlines_id" => $this->com_user['airlines_id'],
                            "inv_st" => 'open',
                        );
                        if (intval($total_inv) > 0) {
                            $this->m_payment->update_invoices($params, $where);
                        }
                        //send notification by email
                        $this->m_payment->send_email_tagihan($inv_id, $this->com_user["user_mail"], "Tagihan Pembayaran FA Online");
                        // success
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                        // redirect
                        redirect("member/payment/cetak_penagihan/" . $inv_id);
                    } else {
                        // default error
                        $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
                    }
                } else {
                    $this->tnotification->sent_notification("error", "gagal generate kode billing karena : <b>" . $response->code . "</b> : " . $response->message);
                }
            } catch (Exception $exc) {
                $this->tnotification->sent_notification("error", "gagal generate kode billing karena :" . $exc->getMessage());
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
        }
        // redirect
        redirect("member/payment");
    }

    // open list invoices
    public function open() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment/open.html");
        // list open
        $rs_id = $this->m_payment->get_list_invoices_open_by_airlines(array($this->com_user['airlines_id']));
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total_draft", count($rs_id));

        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // cancel_invoices
    public function cancel_invoices($inv_id = "") {
        // set page rules
        $this->_set_page_rule("D");
        // delete
        $params = array($inv_id, $this->com_user['airlines_id'], 'open');
        if ($this->m_payment->cancel_invoices($params)) {
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil dihapus");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("member/payment/open");
    }

    // detail opened invoices
    public function open_invoices($inv_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment/open_invoices.html");
        // detail invoices
        $detail = $this->m_payment->get_detail_invoices_open_by_airlines(array($inv_id, $this->com_user['airlines_id']));
        $this->smarty->assign("detail", $detail);
        // tarif
        $tarif = $this->m_payment->get_tabel_tarif_fa(array($this->com_user['airlines_id']));
        $this->smarty->assign("tarif", $tarif);
        // list waiting
        $rs_id = $this->m_payment->get_list_fa_invoices_open_by_airlines(array($detail['virtual_account'], $this->com_user['airlines_id']));
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process invoices open
    public function open_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('inv_id', 'ID Invoices', 'required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // update invoices
            $params = array(
                "inv_date" => date('Y-m-d H:i:s'),
                "inv_st" => "pending",
                "inv_total" => $this->input->post('total_inv'),
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s'),
            );
            $where = array(
                "inv_id" => $this->input->post('inv_id'),
                "airlines_id" => $this->com_user['airlines_id'],
                "inv_st" => 'open',
            );
            // update
            $this->m_payment->update_invoices($params, $where);
            // kasih email disini!
            $inv_id = $this->input->post('inv_id');
            $this->m_payment->send_email_tagihan($inv_id, $this->com_user["user_mail"], "Tagihan Pembayaran FA Online");
            // success
            $this->tnotification->delete_last_field();
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            // redirect
            redirect("member/payment/cetak_penagihan/" . $this->input->post('inv_id'));
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
        }
        // redirect
        redirect("member/payment");
    }

    // cetak payment
    function cetak_penagihan($inv_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment/invoice.html");
        // detail invoice
        $result = $this->m_payment->get_detail_invoices_by_id(array($inv_id, $this->com_user['airlines_id']));
        if (empty($result)) {
            $this->tnotification->sent_notification("error", "Invoice tidak ditemukan");
            redirect('member/payment');
        }
        $this->smarty->assign("result", $result);
        // list detail
        $rs_id = $this->m_payment->get_rincian_invoices(array($result['virtual_account']));
        $this->smarty->assign("rs_id", $rs_id);
        // tarif
        $tarif = $this->m_payment->get_tabel_tarif_fa(array($this->com_user['airlines_id']));
        $this->smarty->assign("tarif", $tarif);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    /*
     * end of WAITING
     */

    /*
     * PENDING
     */

    // list pending
    public function pending() {
        // set rules
        $this->_set_page_rule("R");
        // set templates
        $this->smarty->assign("template_content", "member/payment/pending.html");
        // get search parameter
        $search = $this->tsession->userdata('search_pending');
        // search parameters
        $virtual_account = empty($search['virtual_account']) ? '%' : '%' . $search['virtual_account'] . '%';
        $this->smarty->assign("search", $search);
        // list pending
        $params = array($this->com_user['airlines_id'], $virtual_account);
        $rs_id = $this->m_payment->get_list_pending_invoice($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", COUNT($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari_pending() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_pending');
        } else {
            $params = array(
                "virtual_account" => $this->input->post("virtual_account"),
            );
            $this->tsession->set_userdata("search_pending", $params);
        }
        // redirect
        redirect("member/payment/pending");
    }

    /*
     * end of PENDING
     */

    /*
     * FAILED
     */

    // list failed
    public function failed() {
        // set rules
        $this->_set_page_rule("R");
        // set templates
        $this->smarty->assign("template_content", "member/payment/failed.html");
        // get search parameter
        $search = $this->tsession->userdata('search_failed');
        // search parameters
        $virtual_account = empty($search['virtual_account']) ? '%' : '%' . $search['virtual_account'] . '%';
        $this->smarty->assign("search", $search);
        // list pending
        $params = array($this->com_user['airlines_id'], $virtual_account);
        $rs_id = $this->m_payment->get_list_failed_invoice($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", COUNT($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari_failed() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_failed');
        } else {
            $params = array(
                "virtual_account" => $this->input->post("virtual_account"),
            );
            $this->tsession->set_userdata("search_failed", $params);
        }
        // redirect
        redirect("member/payment/failed");
    }

    /*
     * end of FAILED
     */

    /*
     * SUCCESS
     */

    // list success
    public function success() {
        // set rules
        $this->_set_page_rule("R");
        // set templates
        $this->smarty->assign("template_content", "member/payment/success.html");
        // get search parameter
        $search = $this->tsession->userdata('search_success');
        // search parameters
        $virtual_account = empty($search['virtual_account']) ? '%' : '%' . $search['virtual_account'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/payment/success");
        $config['total_rows'] = $this->m_payment->get_total_success_invoice(array($this->com_user['airlines_id'], $virtual_account));
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
        // list pending
        $params = array($this->com_user['airlines_id'], $virtual_account, ($start - 1), $config['per_page']);
        $rs_id = $this->m_payment->get_list_success_invoice($params);
        $this->smarty->assign("rs_id", $rs_id);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari_success() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_success');
        } else {
            $params = array(
                "virtual_account" => $this->input->post("virtual_account"),
            );
            $this->tsession->set_userdata("search_success", $params);
        }
        // redirect
        redirect("member/payment/success");
    }

    /*
     * end of FAILED
     */

    /*
     * CETAK INVOICE
     */

    function cetak_invoice($va) {
        // set page rules
        $this->_set_page_rule("R");
        // load library
        $this->load->library('tcpdf');
        // get all fa by invoice no
        $rs_id = $this->m_payment->get_detail_invoice(array($va));
        $rs_rincian = $this->m_payment->get_rincian_invoices($va);
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);
        //set font
        $this->tcpdf->SetFont('helvetica', 5);
        // add a page
        $this->tcpdf->AddPage();
        // create pdf
        $html = '';
        //QR Style
        $style = array(
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(232, 76, 61),
            'bgcolor' => array(255, 255, 255),
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //QR
        $params = $this->tcpdf->serializeTCPDFtagParameters(array($rs_id["virtual_account"], 'QRCODE,H', '', '', 20, 20, $style, 'N'));
        $html .= '<style ="text/css">'
                . '.table-input {
                    margin: 0 0 5px 0;
                    padding: 2px;
                    background-color: #fff;
                    border-collapse: collapse;
                    color: #666;
                    font-size: 20px;
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
                    background-color:#ECEDEF;
                    margin: 0;
                    padding: 0;
                    border-collapse: collapse;
                    text-align: left;
                    font-family: tahoma;
                    font-size: 23px;
                    line-height:5px;
                }

                .table-form td {
                    margin: 0;
                    background-color:#ECEDEF;
                    vertical-align: middle;
                    font-size: 23px;
                    color : #525252;
                }
                
                .table-form td.flight-tp {
                    height: 50px;
                    vertical-align: middle;
                    text-align:center;
                    background-color:#E84C3D;
                    color:#FFF;
                    font-size: 20px;
                    line-height:3px;
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
                }';
        $html .= '</style>';
        $no = 1;
        // content
        $html.='<table width="100%">
                <tr>
                    <td width="10%" rowspan="6"><img src="resource/doc/images/logo/logo.jpg" width="48px" style="vertical-align:middle"></td>
                    <td width="90%" style="font-size:30px"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                </tr>
                <tr>
                    <td style="font-size:28px">DIREKTORAT JENDERAL PERHUBUNGAN UDARA</td>
                </tr>
                <tr>
                    <td style="font-size:25px">PENDAFTARAN KAPAL ONLINE</td>
                </tr>
                <tr>
                    <td style="font-size:25px"></td>
                </tr>
                <tr>
                    <td style="font-size:20px">Jl. Medan Merdeka Barat No. 8, Jakarta 10110</td>
                </tr>
                <tr>
                    <td style="font-size:20px">Telp : 151 / (021) 151 email : info151@dephub.go.id</td>
                </tr>
            </table>
        <div style="font-size:25px;text-align:center;padding:2px">
           <div style="border:1px solid #000;padding:2px;text-align:center;"><b>INVOICE #' . $rs_id["invoice_no"] . '</b></div><br/>
            <table border="0" style="background-color:#ECEDEF;"> 
                <table class="table-form" border="0" cellspacing="3">
                    <tr>
                        <td width="24%">Kode Billing</td><td width="1%" align="center">:</td><td width="50%">' . $rs_id["virtual_account"] . '</td><td rowspan="6" class="flight-tp"><br/><small>Jenis Permohonan :</small><br/><b>FLIGHT APPROVAL </b><br/>' .
                strtoupper($rs_id["data_flight"]) . '<br/>' . '<br/>
                    <table>
                        <tr>
                        <td></td>
                        <td>' . '<tcpdf method="write2DBarcode" params="' . $params . '" />' . '</td>
                        <td></td>
                        </tr>
                    </table>
                   </td>
                    </tr>
                    <tr>
                        <td width="24%">Nama Airlines</td><td width="1%" align="center">:</td><td width="50%"><b>' . $rs_id["airlines_nm"] . '</b></td>
                    </tr>
                    <tr>
                        <td width="24%">Tanggal Invoice</td><td width="1%" align="center">:</td><td width="50%">' . $this->datetimemanipulation->get_full_date($rs_id["inv_date"]) . '</td>
                    </tr>
                    <tr>
                        <td width="24%">No Invoice</td><td width="1%" align="center">:</td><td width="50%">' . $rs_id["invoice_no"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Total Tagihan</td><td width="1%" align="center">:</td><td width="50%">Rp. ' . number_format($rs_id["inv_total"], 0, ",", ".") . '</td>
                    </tr>
                </table>
            </table>
            </div>
        <table width="100%" cellspacing="2" cellpadding="2" border="0" style="font-size:20px;padding:10px">
                        <tbody>
                            <tr>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="5%" align="center"><b>No</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="25%" align="center"><b>Published No</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="15%" align="center"><b>Aircraft Type</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="15%" align="center"><b>Flight No</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="15%" align="center"><b>Rute</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="15%" align="center"><b>Expired Kode Billing</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF" width="10%" align="center"><b>Jumlah Tagihan</b></td>
                            </tr>';
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.='<tr nobr="true">';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . ($index + 1) . '</td><td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["published_no"] . '</td><td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["aircraft_type"] . '</td>';
            $html.='<td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["flight_no"] . '</td>' . '<td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["rute_all"] . '</td>' . '<td style="background-color:#ffffed;padding:5px;" align="center">' . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"], "ins") . '</td><td align="right" style="background-color:#ffffed;padding:5px;">' . number_format($rincian["payment_tarif"], 0, '.', ',') . '</td>';
            $html.='</tr>';
            $total+=$rincian["payment_tarif"];
        }
        $html.='<tr><td colspan="5" align="left" style="background-color:#ffffed;padding:5px;"><b>TOTAL</b></td>' . '<td align="right" style="background-color:#FCDF5A;padding:5px;font-size:35px;color:#000" colspan="2"><b>' . number_format($total, 0, ",", ".") . '</b></td></tr>';
        $html.='</tbody>
                </table><br/>
                        <span style="font-size:20px">
                            <strong>PERHATIAN :</strong> 
                        </span><br/>
                        <span style="font-size:20px">
                            * Pastikan kode billing sesuai dengan yang tercantum pada invoice 
                        </span><br/>
                        <span style="font-size:20px">
                            * Harap diperhatikan batas pembayaran yang tercantum pada invoice, diharapkan agar membayar <u>sebelum tanggal jatuh tempo</u> 
                        </span><br/>
                        <span style="font-size:20px">
                            * Jika membutuhkan informasi, bantuan, dan petunjuk teknis terkait penggunaan sistem billing serta pembayaran dan penyetoran PNBP,
                              dapat menghubungi :
                            <ul>
                                <li>Call Center Kementerian Perhubungan : (021) 151 (24 jam, setiap hari)</li>
                                <li>Call Center Direktorat Jenderal Anggaran : (021) 34832511 ( Jam & Hari Kerja )</li>
                                <li>Customer Service Direktorat Jenderal Anggaran : (021) 34832516 ( Jam & Hari Kerja )</li>
                            </ul>
                            </span><br/><br/>
                         <span style="font-size:20px;text-align:center"><b><u>Silakan Ikuti Langkah Pembayaran Sesuai dengan Kartu ATM / Bank yang digunakan</u></b></span><br/>
                        <br/>
                        <span style="font-size:20px">
                            Cara Pembayaran PNBP via ATM BRI :
                            <ol>
                                <li>Pilih menu : Transaksi Lain; Pembayaran; Lainnya; Lainnya; MPN;</li>
                                <li>Masukkan 15 digit Kode Pembayaran; kemudian tekan "Benar"</li>
                                <li>Muncul layar konfirmasi pembayaran, bila setuju bayar, tekan "YA";</li>
                                <li>Simpan Struk bukti pembayaran PNBP.</li>
                                <li>Selesai</li>
                            </ol>
                        </span><br/>
                        <span style="font-size:20px">
                            Cara Pembayaran PNBP via Internet Banking BRI :
                            <ol>
                                <li>Pilih menu : Pembayaran; MPN;</li>
                                <li>Masukkan PIN</li>
                                <li>Masukkan Kode Billing;</li>
                                <li>Ikuti langkah selanjutnya.</li>
                                <li>Selesai</li>
                            </ol>
                        </span>';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $va;
        $this->tcpdf->Output($filename . ".pdf", 'D');
    }

    function cetak_kwitansi($va = "") {
        $this->_set_page_rule("R");
        $rs_kwitansi = $this->m_payment->get_kwitansi($va);
        $rs_rincian = $this->m_payment->get_rincian_invoices_by_id($va);
        // load library
        $this->load->library('tcpdf');
        // create new PDF document
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margins
        $this->tcpdf->SetMargins(10, 10, 10);
        // add a page
        $this->tcpdf->AddPage();
        // create pdf
        $html = '';
        $no = 1;
        //QR Style
        $style = array(
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 112, 196),
            'bgcolor' => array(255, 255, 255),
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        //QR
        $params = $this->tcpdf->serializeTCPDFtagParameters(array($rs_kwitansi["ntpn"], 'QRCODE,H', '', '', 40, 40, $style, 'N'));
        // content
        $html .= '
            <style type="text/css">
                .table-input {
                    margin: 0 0 5px 0;
                    padding: 2px;
                    background-color: #fff;
                    border-collapse: collapse;
                    color: #666;
                    font-size: 20px;
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
                    background-color:#ECEDEF;
                    margin: 0;
                    padding: 0;
                    border-collapse: collapse;
                    text-align: left;
                    font-family: tahoma;
                    font-size: 23px;
                    line-height:5px;
                }

                .table-form td {
                    margin: 0;
                    background-color:#ECEDEF;
                    vertical-align: middle;
                    font-size: 23px;
                    color : #525252;
                }
                
                .table-form td.flight-tp {
                    height: 50px;
                    vertical-align: middle;
                    text-align:center;
                    background-color:#0070C4;
                    color:#FFF;
                    font-size: 20px;
                    line-height:3px;
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
            <table width="100%">
                <tr>
                    <td width="10%" rowspan="6"><img src="resource/doc/images/logo/logo.jpg" width="48px" style="vertical-align:middle"></td>
                    <td width="90%" style="font-size:30px"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                </tr>
                <tr>
                    <td style="font-size:28px">DIREKTORAT JENDERAL PERHUBUNGAN UDARA</td>
                </tr>
                <tr>
                    <td style="font-size:25px">PENDAFTARAN KAPAL ONLINE</td>
                </tr>
                <tr>
                    <td style="font-size:25px"></td>
                </tr>
                <tr>
                    <td style="font-size:20px">Jl. Medan Merdeka Barat No. 8, Jakarta 10110</td>
                </tr>
                <tr>
                    <td style="font-size:20px">Telp : 151 / (021) 151 email : info151@dephub.go.id</td>
                </tr>
            </table>
           <div style="font-size:25px;text-align:center;padding:2px">
           <div style="border:1px solid #000;padding:2px;text-align:center;"><b>BUKTI PEMBAYARAN #' . $rs_kwitansi["no_kuitansi"] . '</b></div><br/>
            <table border="0" style="background-color:#ECEDEF;"> 
                <table class="table-form" border="0" cellspacing="3">
                    <tr>
                        <td width="24%">Nama Airlines</td><td width="1%" align="center">:</td><td width="50%"><b>' . $rs_kwitansi["airlines_nm"] . '</b></td><td rowspan="7" class="flight-tp"><br/><small>Jenis Permohonan :</small><br/><b>FLIGHT APPROVAL </b><br/>' .
                strtoupper($rs_kwitansi["data_flight"]) . '<br/>' . '<br/>
                    <table>
                        <tr>
                        <td></td>
                        <td>' . '<tcpdf method="write2DBarcode" params="' . $params . '" />' . '</td>
                        <td></td>
                        </tr>
                    </table>
                   </td></tr>
                    <tr>
                        <td width="24%">Tanggal Invoice</td><td width="1%" align="center">:</td><td width="50%">' . $rs_kwitansi["inv_date"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">No Invoice</td><td width="1%" align="center">:</td><td width="50%">' . $rs_kwitansi["invoice_no"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Kode Billing</td><td width="1%" align="center">:</td><td width="50%">' . $rs_kwitansi["virtual_account"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">NTB</td><td width="1%" align="center">:</td><td width="50%">' . $rs_kwitansi["ntb"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">NTPN</td><td width="1%" align="center">:</td><td width="50%">' . $rs_kwitansi["ntpn"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Total Tagihan</td><td width="1%" align="center">:</td><td width="50%">Rp. ' . number_format($rs_kwitansi["inv_total"], 0, ",", ".") . '</td>
                    </tr>
                </table>
            </table>
            </div>
        <table width="100%" cellspacing="2" cellpadding="2" border="0" style="font-size:20px;padding:10px">
                        <tbody>
                            <tr>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="5%" align="center"><b>No</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="25%" align="center"><b>Published No</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="15%" align="center"><b>Aircraft Type</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="15%" align="center"><b>Flight No</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="15%" align="center"><b>Rute</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="15%" align="center"><b>Expired Kode Billing</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="10%" align="center"><b>Jumlah Tagihan</b></td>
                            </tr>';
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.='<tr nobr="true">';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . ($index + 1) . '</td><td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["published_no"] . '</td><td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["aircraft_type"] . '</td>';
            $html.='<td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["flight_no"] . '</td>' . '<td style="background-color:#ffffed;padding:5px;" align="center">' . str_replace("-", " - ", $rincian["rute_all"]) . '</td>' . '<td style="background-color:#ffffed;padding:5px;" align="center">' . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"], "ins") . '</td><td align="right" style="background-color:#ffffed;padding:5px;">' . number_format($rincian["payment_tarif"], 0, '.', ',') . '</td>';
            $html.='</tr>';
            $total+=$rincian["payment_tarif"];
        }
        $html.='<tr><td colspan="5" align="left" style="background-color:#ffffed;padding:5px;"><b>TOTAL</b></td>' . '<td align="right" style="background-color:#FCDF5A;padding:5px;font-size:35px;color:#FFF" colspan="2"><b>' . number_format($total, 0, ",", ".") . '</b></td></tr>';
        $html.='</tbody>
                </table>';
        $html.='<div style="font-size:25px;padding:2px">';
        $html.='Silakan simpan bukti pembayaran ini sebagai salah satu bukti pembayaran yang SAH. Terima Kasih';
        $html.='</div>';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = "BUKTI.BAYAR." . $va;
        $this->tcpdf->Output($filename . ".pdf", 'I');
    }

    public function testSimponi() {
        try {
            $this->load->library("simponi");
            //create ws client
            $wsClient = new SimponiBRIService();
            //set request params
            $requestParams = new PaymentRequest();
            $requestParams->appsId = "002";
            $requestParams->invoiceNo = "002";
            $requestParams->routeId = "003";
            //set payment header
            $paymentHeader = new PaymentHeader();
            $paymentHeader->TrxId = "";
            $paymentHeader->UserId = "";
            $paymentHeader->Password = "";
            $paymentHeader->ExpiredDate = "2015-09-28 10:10:00";
            $paymentHeader->DateSent = "";
            $paymentHeader->KodeKL = "022";
            $paymentHeader->KodeEselon1 = "05";
            $paymentHeader->KodeSatker = "288042";
            $paymentHeader->JenisPNBP = "F";
            $paymentHeader->KodeMataUang = "1";
            $paymentHeader->TotalNominalBilling = 100000;
            $paymentHeader->NamaWajibBayar = "MORAJIMMY";
            //set payment detail
            $paymentDetail = new PaymentDetail();
            $paymentDetail->NamaWajibBayar = "MORAJIMMY";
            $paymentDetail->KodeTarifSimponi = "003896";
            $paymentDetail->KodePPSimponi = "2015011";
            $paymentDetail->KodeAkun = "423214";
            $paymentDetail->TarifPNBP = 100000;
            $paymentDetail->Volume = 1;
            $paymentDetail->Satuan = "per flight approval";
            $paymentDetail->TotalTarifPerRecord = 100000;
            //set payment detail list
            $paymentDetails = new PaymentDetailList();
            //add payment detail to list
            $paymentDetails->PaymentDetail[] = $paymentDetail;
            $paymentData = new requestData($paymentHeader, $paymentDetails);
            //set request params : payment data
            $requestParams->data = $paymentData;
            //get responses
            $simponiResponse = $wsClient->PaymentRequest($requestParams);
            $response = $simponiResponse->response;
            //get simponi data
            $simponiData = $response->simponiData;
            echo($simponiData->KodeBillingSimponi);
        } catch (Exception $exc) {
            echo "gagal request billing karena :<br/>" . $exc->getMessage();
        }
    }

}
