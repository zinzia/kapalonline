<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

class payment_izin extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model("m_preferences");
        $this->load->model("m_payment");
        //load lib
        $this->load->library("tnotification");
        $this->load->library("email");
        $this->load->library("pagination");
        //load helper
        $this->load->helper("terbilang_helper");
        //load js
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.3.js");
        $this->smarty->load_javascript('resource/js/jquery.blockui/jquery.blockui.js');
        $this->smarty->load_javascript('resource/js/jquery.runningdot/jquery.runningdot.js');
    }

    // index
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment_izin/index.html");
        // get search parameter
        $search = $this->tsession->userdata('search_payment');
        // search parameters
        $published_no = empty($search['published_no']) ? '%' : '%' . $search['izin_number'] . '%';
        $this->smarty->assign("search", $search);
        // get list
        $params = array($published_no, $this->com_user["airlines_id"]);
        // tarif
        $tarif = $this->m_payment->get_tabel_tarif_rute();
        $this->smarty->assign("tarif", $tarif);
        $this->smarty->assign("rs_rekap_bayar", $this->m_payment->get_rekap_pembayaran_rute(array($this->com_user["airlines_id"])));
        $this->smarty->assign("rs_id", $this->m_payment->get_list_invoice_izinrute($params));
        $this->smarty->assign("total", COUNT($this->m_payment->get_list_invoice_izinrute($params)));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process invoices 
    public function create_invoices_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('izin_id', '/ pilihan Ijin Rute', 'required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $data = $this->input->post('izin_id');
            // tarif
            $tarif = $this->m_payment->get_tabel_tarif_rute();
            $search = $this->tsession->userdata('search_payment');
            $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];

            //get last invoice no transaction
            $invoice_no = $this->m_payment->get_last_rute_invoice_no($this->com_user["airlines_id"]);
            // get inv id
            $inv_id = $this->m_payment->get_invoice_id();
            //load library
            $this->load->library("simponi");
            //get kode billing
            try {
                //get preferences
                $apps_id_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "rute_app_id"));
                $route_id_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "default_channel"));
                $kode_kl_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_kl"));
                $kode_es1_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_eselon1"));
                $kode_satker_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_satker"));
                $kode_tarif_rute_baru_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_tarif_rute_baru"));
                $kode_tarif_rute_add_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_tarif_rute_add"));
                $kode_pp_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_pp"));
                $kode_akun_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "kode_akun_rute"));
                $tarif_freq_add = $this->m_preferences->get_preferences_by_group_and_name(array("tarif_rute", "frekuensi_add"));
                $tarif_freq_baru = $this->m_preferences->get_preferences_by_group_and_name(array("tarif_rute", "baru"));
                $user_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "user_id"));
                $pass_pref = $this->m_preferences->get_preferences_by_group_and_name(array("simponi", "password"));
                //get total invoice
                $total = 0;
                $kode_tarif_rute = "";
                $satuan = "";
                $nominal = 0;
                $paymentDetails = new PaymentDetailList();
                $tarif_rute = 0;

                foreach ($data as $value) {
                    // total inv
                    $detail_registrasi = $this->m_payment->get_detail_izin_by_id($value);
                    $total = $total + $detail_registrasi["total_invoice"];
                    if ($detail_registrasi["group_alias"] == "baru") {
                        $tarif_rute = $tarif_freq_baru["pref_value"];
                        $kode_tarif_rute = $kode_tarif_rute_baru_pref["pref_value"];
                        $satuan = "per penggal rute";
                    } else {
                        $tarif_rute = $tarif_freq_add["pref_value"];
                        $kode_tarif_rute = $kode_tarif_rute_add_pref["pref_value"];
                        $satuan = "per frekuensi";
                    }

                    //set payment detail
                    $paymentDetail = new PaymentDetail();
                    $paymentDetail->NamaWajibBayar = $this->com_user["airlines_nm"];
                    $paymentDetail->KodeTarifSimponi = $kode_tarif_rute;
                    $paymentDetail->KodePPSimponi = $kode_pp_pref["pref_value"];
                    $paymentDetail->KodeAkun = $kode_akun_pref["pref_value"];
                    $paymentDetail->TarifPNBP = $tarif_rute;
                    $paymentDetail->Volume = 1;
                    $paymentDetail->Satuan = $satuan;
                    $paymentDetail->TotalTarifPerRecord = $detail_registrasi["total_invoice"];
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
                        "category" => "2",
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
                                $expdate,
                                $simponiData->KodeBillingSimponi,
                                $this->com_user['user_id'],
                                $value
                            );

                            if ($this->m_payment->is_rute_already_billed($value) == "0") {
                                $this->m_payment->update_rute_kode_billing($params);
                            } else {
                                $this->tnotification->sent_notification("error", "Permohonan sudah memiliki kode billing !");
                                redirect("member/payment_izin");
                            }

                            // total inv
                            $detail_registrasi = $this->m_payment->get_detail_izin_by_id($value);
                            $total_inv = $total_inv + $detail_registrasi["total_invoice"];
                            //set invoice detail params
                            $detail_params = array(
                                "detail_id" => $this->m_payment->get_invoice_detail_id(),
                                "register_id" => $value,
                                "virtual_account" => $simponiData->KodeBillingSimponi,
                                "amount" => $detail_registrasi["total_invoice"],
                                "mdd" => date("Y-m-d h:i:s")
                            );
                            $this->m_payment->insert_invoice_detail($detail_params);
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
                        // kasih email disini!
                        $this->m_payment->send_email_tagihan_izin($inv_id, $this->com_user["user_mail"], "Tagihan Pembayaran Ijin Rute Online");
                        // success
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data berhasil disimpan");
                        // redirect
                        redirect("member/payment_izin/cetak_penagihan/" . $inv_id);
                    } else {
                        // default error
                        $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
                    }
                } else {
                    $this->tnotification->sent_notification("error", "gagal generate kode billing karena : " . $response->code . " - " . $response->message);
                }
            } catch (Exception $exc) {
                $this->tnotification->sent_notification("error", "gagal generate kode billing karena :" . $exc->getMessage());
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
        }
        // redirect
        redirect("member/payment_izin");
    }

    // cetak payment
    function cetak_penagihan($inv_id = "") {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment_izin/invoice.html");
        // detail invoice
        $result = $this->m_payment->get_detail_invoices_by_id(array($inv_id, $this->com_user['airlines_id']));
        if (empty($result)) {
            $this->tnotification->sent_notification("error", "Invoice tidak ditemukan");
            redirect('member/payment_izin');
        }
        $this->smarty->assign("result", $result);
        // list detail
        $rs_id = $this->m_payment->get_ijin_by_invoice_no(array($result['virtual_account']));
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
     * PENDING
     */

    // list pending
    public function pending() {
        // set rules
        $this->_set_page_rule("R");
        // set templates
        $this->smarty->assign("template_content", "member/payment_izin/pending.html");
        // get search parameter
        $search = $this->tsession->userdata('search_pending');
        // search parameters
        $virtual_account = empty($search['virtual_account']) ? '%' : '%' . $search['virtual_account'] . '%';
        $this->smarty->assign("search", $search);
        // list pending
        $params = array($this->com_user['airlines_id'], $virtual_account);
        $rs_id = $this->m_payment->get_list_pending_invoice_ijin($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", COUNT($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    /*
     * FAILED
     */

    // list failed
    public function failed() {
        // set rules
        $this->_set_page_rule("R");
        // set templates
        $this->smarty->assign("template_content", "member/payment_izin/failed.html");
        // get search parameter
        $search = $this->tsession->userdata('search_failed');
        // search parameters
        $virtual_account = empty($search['virtual_account']) ? '%' : '%' . $search['virtual_account'] . '%';
        $this->smarty->assign("search", $search);
        // list pending
        $params = array($this->com_user['airlines_id'], $virtual_account);
        $rs_id = $this->m_payment->get_list_failed_invoice_ijin($params);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", COUNT($rs_id));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    /*
     * SUCCESS
     */

    // list success
    public function success() {
        // set rules
        $this->_set_page_rule("R");
        // set templates
        $this->smarty->assign("template_content", "member/payment_izin/success.html");
        // get search parameter
        $search = $this->tsession->userdata('search_success');
        // search parameters
        $virtual_account = empty($search['virtual_account']) ? '%' : '%' . $search['virtual_account'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/payment_izin/success");
        $config['total_rows'] = $this->m_payment->get_total_success_invoice_ijin(array($this->com_user['airlines_id'], $virtual_account));
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
        $rs_id = $this->m_payment->get_list_success_invoice_ijin_success($params);
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

    // form payment
    function form_payment() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment_izin/form.html");
        // get virtual account
        $result = $this->m_payment->get_virtual_account(array($this->com_user['airlines_id']));
        $this->smarty->assign("result", $result);
        // published no
        $x = 1;
        $data_id = $this->input->post('izin_id');
        $data = array();
        $total_amount = 0;
        $tarif = 0;
        $rs_tarif = $this->m_preferences->get_preferences_by_group(array("tarif_rute"));
        $arr_tarif = array();
        foreach ($rs_tarif as $tarif) {
            $arr_tarif[$tarif["pref_nm"]] = $tarif["pref_value"];
        }
        if (!empty($data_id)) {
            foreach ($data_id as $value) {
                $result = $this->m_payment->get_published_izin_detail(array($value));
                $tarif = $arr_tarif[$result["group_alias"]];
                if ($result["group_alias"] == 'baru') {
                    $tarif = $tarif;
                } else if ($result["group_alias"] == 'frekuensi') {
                    $tarif = $result["total_invoice"];
                }
                //get total
                $total_amount = $total_amount + $tarif;
                if ($result) {
                    $data[] = array(
                        "izin_request_letter" => $result['izin_request_letter'],
                        "registrasi_id" => $result['registrasi_id'],
                        "group_alias" => $result['group_alias'],
                        "group_nm" => $result['group_nm'],
                        "izin_frekuensi_add" => $result['izin_frekuensi_add'],
                        "izin_tarif" => $result['izin_tarif'],
                        "airlines_nm" => $result['airlines_nm'],
                        "izin_request_date" => $result['izin_request_date'],
                        "dos" => $result['dos'],
                        "payment_due_date" => $result['payment_due_date'],
                        "amount" => $tarif
                    );
                }
            }
        } else {
            $this->tnotification->sent_notification("error", "Maaf tidak ada tagihan yg dipilih untuk diproses");
            redirect("member/payment_izin");
        }
        $this->smarty->assign("rs_id", $data);
        $this->smarty->assign("izin_id", $data_id);
        $this->smarty->assign("total", $total_amount);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses payment
    function payment_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('virtual_account', 'Nomor Virtual Account', 'trim|required|maxlength[16]');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // trim virtual account
            $virtual_account = substr($this->input->post('virtual_account'), 0, 7);
            // get last virtual account transaction
            $va = $this->m_payment->get_last_virtual_account_rute($virtual_account);
            $data_id = $this->input->post('izin_id');
            $rs_tarif = $this->m_preferences->get_preferences_by_group(array("tarif_rute"));
            $arr_tarif = array();
            foreach ($rs_tarif as $tarif) {
                $arr_tarif[$tarif["pref_nm"]] = $tarif["pref_value"];
            }
            //load model
            $this->load->model("m_report_fa_nb");
            $total_inv = 0;
            $tarif = 0;
            foreach ($data_id as $value) {
                $result = $this->m_payment->get_published_izin_detail(array($value));
                // update fa data
                $tarif = $arr_tarif[$result["group_alias"]];
                if ($result["group_alias"] == 'baru') {
                    $tarif = $tarif;
                } else if ($result["group_alias"] == 'frekuensi' || $result["group_alias"] == 'frekuensi_add') {
                    $tarif = $result["total_invoice"];
                }
                //get total
                $total_inv = $total_inv + $tarif;
                $params = array("00", date('Y-m-d H:i:s'), $va, $this->com_user['user_id'], $value);
                $this->m_payment->update_payment_rute($params);
            }
            //insert invoice
            // get inv id
            $inv_id = $this->m_payment->get_invoice_id();
            $params = array(
                "inv_id" => $inv_id,
                "airlines_id" => $this->com_user['airlines_id'],
                "category" => "2",
                "virtual_account" => $va,
                "inv_st" => "pending",
                "inv_total" => $total_inv,
                "inv_date" => date('Y-m-d H:i:s'),
                "tgl_update" => null,
                "tgl_transaksi" => null,
                "nama_file" => null,
                "generate_st" => "no",
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s'),
            );

            if ($this->m_payment->insert_invoice($params)) {
                $tarif = 0;
                foreach ($data_id as $value) {
                    $detail_id = $this->m_payment->get_invoice_detail_id();
                    $rs_rute = $this->m_payment->get_izin_rute($value);
                    $result = $this->m_payment->get_detail_payment_rute(array($value));
                    // update fa data
                    $tarif = $arr_tarif[$result["group_alias"]];
                    if ($result["group_alias"] == 'baru') {
                        $tarif = $tarif;
                    } else if ($result["group_alias"] == 'frekuensi' || $result["group_alias"] == 'frekuensi_add') {
                        $tarif = $result["total_invoice"];
                    }
                    $params = array(
                        "detail_id" => $detail_id,
                        "register_id" => $value,
                        "virtual_account" => $va,
                        "amount" => $tarif,
                        "mdd" => date('Y-m-d H:i:s')
                    );
                    if ($this->m_payment->insert_invoice_detail($params)) {
                        // success
                        $this->tnotification->delete_last_field();
                        $this->tnotification->sent_notification("success", "Data invoice berhasil disimpan");
                    } else {
                        $this->tnotification->sent_notification("error", "Data invoice gagal disimpan");
                    }
                }
                //get detail invoice
                $rs_detail_inv = $this->m_payment->get_inv_rute_detail_by_va($va);
                //sent email notification
                $mail = $this->m_preferences->get_mail();
                $detail = explode(",", $mail['pref_value']);
                $host = $mail['pref_nm'];
                $port = $detail[0];
                $user = $detail[1];
                $pass = $detail[2];
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = str_replace(" ", "", $host);
                $config['smtp_port'] = str_replace(" ", "", $port);
                $config['smtp_timeout'] = '7';
                $config['smtp_user'] = str_replace(" ", "", $user);
                $config['smtp_pass'] = str_replace(" ", "", $pass);
                $config['charset'] = 'utf-8';
                $config['newline'] = "\r\n";
                $config['validation'] = FALSE; // bool whether to validate email or not
                $config['mailtype'] = 'html'; // bool whether to validate email or not
                $this->email->initialize($config);
                // get detail
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
    <tbody>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><table cellspacing='0' cellpadding='0' align='center'>
                    <tbody><tr>
                            <td width='600' valign='top' style='color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:34px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                <div align='center'>
                                    <a target='_blank' style='text-decoration:none' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'><img width='600px' height='45px' border='0' title='&nbsp;&nbsp; Kementerian Perhubungan Republik Indonesia' style='display:block;color:#ffffff;font-size:15px;font-weight:bold;background-color:#5F7B93;text-align:center;line-height:45px' alt='&nbsp;&nbsp;Kementerian Perhubungan Republik Indonesia' src='" . base64_encode(base_url() . "resource/doc/images/logo/" . "email-head.jpg'") . "class='CToWUd'></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $this->com_user["airlines_nm"] . "</b></span><br><br>
                                <span style='font:normal 11px Tahoma'>Terima kasih anda telah melakukan pembayaran permohonan izin rute<br><br>
                                <span style='font:normal 11px Tahoma'>Email ini <strong>BUKAN BUKTI PEMBAYARAN YANG SAH</strong>. Silakan selesaikan transaksi anda sesuai dengan informasi dibawah ini.</span><br/>
                                <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                    Airlines        : <strong>" . $this->com_user["airlines_nm"] . "</strong><br>
                                    VA number       : <strong>" . $va . "</strong><br>
                                    Jumlah          : <strong>Rp. " . number_format($total_inv, 0, ",", ".") . "</strong><br>
                                    Tanggal Invoice : <strong>" . date("d M Y h:i:s") . "</strong><br/>
                                    <table width='100%' cellspacing='0' cellpadding='2' border='0' style='font:normal 11px Arial'>
                                        <tbody><tr>
                                                <th style='border:1px solid #DAD5BA;background-color:#ECEADC'>No</th>
                                                <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC'>Route No</th>
                                                <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC'>Group</th>
                                                <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC'>Operator</th>
                                                <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC'>Day of Service</th>
                                                <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC'>Route</th>
                                                <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC'>Amount</th>
                                            </tr>";
                $total = 0;
                foreach ($rs_detail_inv as $i => $detail) {
                    $html.="<tr>";
                    $html.="<td style='border:1px solid #DAD5BA;border-top-width:0; background-color: #FFF;padding: 5px;'>" . ($i + 1) . "</td>";
                    $html.="<td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>";
                    $html.=$detail["izin_number"] . "</td>";
                    $html.="<td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>";
                    $html.=$detail["group_nm"] . "</td>";
                    $html.="<td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>";
                    $html.=$detail["airlines_nm"] . "</td>";
                    $html.="<td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>";
                    $html.=$detail["dos"] . "</td>";

                    $html.="<td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>";
                    $html.="<ol>";
                    foreach ($rs_rute as $rute) {
                        $html.="<li>" . $rute["rute_all"] . "<br/>FLIGHT NO : " . $rute["flight_no"] . "(ETD : " . $rute["etd"] . " ETA : " . $rute["eta"] . ")" . "</li>";
                    }
                    $html.="<ol>";
                    $html.="</td>";
                    $html.="<td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>";
                    $html.=number_format($detail["amount"], 0, ",", ".") . "</td>";
                    $html.="</tr>";
                    $total = $total + $detail["amount"];
                }
                $html.="<tr><td colspan='6' style='border:1px solid #DAD5BA;border-left-width:1;border-top-width:0;background-color: #FFF;padding: 5px;'>Total</td><td style='border:1px solid #DAD5BA;border-left-width:0;border-top-width:0;background-color: #FFF;padding: 5px;'>" . number_format($total, 0, ",", ".") . "</td>";
                $html.="</tbody></table><br>
                                </div>
                                <span style='font:normal 11px Tahoma'>
                                * <strong>PERHATIAN :</strong> Pembayaran yg akan diproses adalah pembayaran
                                yg dilakukan pada jam 7:00 s.d 18:00 pada hari kerja,pembayaran yg dilakukan di luar jam tsb
                                akan diproses pada <u>hari berikutnya</u>
                                </span><br><br>
                                    * Akan dikenakan biaya 5000,-/transaksi (tidak termasuk di total transaksi) untuk pembayaran melalui jaringan ATM bersama, Prima, biaya yang dikenakan sesuai dengan ketetapan ATM Bersama, Prima dan Alto. Untuk transaksi di ATM Bank BNI dengan menggunakan Kartu Bank BNI tidak dikenakan biaya
                                </span><br><br>
                                <span style='font:normal 11px Tahoma;text-decoration:underline'>
                                    <strong><div align='center'><u>Ikuti Langkah Pembayaran Sesuai dengan Kartu ATM yang digunakan</u></div></strong>
                                </span>
                                <br>
                                <span style='font:normal 11px Tahoma'>
                                    <strong>PERHATIAN :</strong> Pembayaran yg akan diproses adalah pembayaran
                                    yg dilakukan pada jam 7:00 s.d 18:00 pada hari kerja,pembayaran yg dilakukan di luar jam tsb
                                    akan diproses pada <u>hari berikutnya</u>
                                    <strong>Pembayaran melalui ATM BCA/Bank sejenis dalam Jaringan PRIMA</strong><br>
                                    1. Masukkan PIN<br>
                                    2. Pilih 'Transaksi Lainnya'<br>
                                    3. Pilih 'Transfer'<br>
                                    4. Pilih 'Ke Rek Bank Lain'<br>
                                    5. Masukkan Jumlah pembayaran sesuai dengan yang ditagihkan dalam <span class='il'>invoice</span> (Jumlah yang ditransfer harus sama persis dengan yang ada pada invoice, tidak lebih dan tidak kurang).<strong>Penting</strong>: Jumlah nominal yang tidak sesuai dengan tagihan pada invoice akan menyebabkan transaksi gagal<br>
                                    6. Masukkan 16 Nomor Virtual Account<br>
                                    7. Muncul Layar Konfirmasi Transfer yang berisi nomor rekening tujuan Bank BNI beserta jumlah yang dibayar, jika sudah benar, Tekan 'Benar'<br>
                                    8. Selesai<br>
                                    <br>
                                    <br><br>
                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br><br/>
                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td width='570' bgcolor='ECE9D8' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                            <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p></td>
                                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>";
                // send
                $this->email->from(str_replace(" ", "", $user), 'Izin Rute Online (no reply)');
                $this->email->to($this->com_user["user_mail"]);
                $this->email->subject('Invoice Izin Rute No.' . $va);
                $this->email->message($html);
                $this->email->send();
            } else {
                $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data payment gagal disimpan");
            // redirect
            redirect("member/payment_izin/form_payment/");
        }
        // redirect
        redirect("member/payment_izin/cetak_payment/" . $va);
    }

    // cetak payment
    function cetak_payment($va) {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "member/payment_izin/invoice.html");
        // detail invoice
        $result = $this->m_payment->get_detail_invoice_rute(array($va));
        $this->smarty->assign("result", $result);
        // rs id
        $rs_id = $this->m_payment->get_rute_by_invoice_no(array($va));
        $rs_tarif = $this->m_preferences->get_preferences_by_group(array("tarif_rute"));
        $arr_tarif = array();
        $tarif = 0;
        foreach ($rs_tarif as $tarif) {
            $arr_tarif[$tarif["pref_nm"]] = $tarif["pref_value"];
        }
        foreach ($rs_id as $value) {
            //get tarif
            $tarif = $arr_tarif[$value["group_alias"]];
            if ($value["group_alias"] == 'baru') {
                $tarif = $tarif;
            } else if ($value["group_alias"] == 'frekuensi' || $value["group_alias"] == 'frekuensi_add') {
                $tarif = $result["total_invoice"];
            }
            $data[] = array(
                "registrasi_id" => $value['registrasi_id'],
                "izin_request_letter" => $value['izin_request_letter'],
                "izin_published_letter" => $value['izin_published_letter'],
                "group_nm" => $value['group_nm'],
                "airlines_nm" => $value['airlines_nm'],
                "izin_request_date" => $value['izin_request_date'],
                "dos" => $value['dos'],
                "payment_due_date" => $value['payment_due_date'],
                "amount" => $tarif
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
    function cetak_invoice($va) {
        // set page rules
        $this->_set_page_rule("R");
        // load library
        $this->load->library('tcpdf');
        // get all fa by invoice no
        $rs_id = $this->m_payment->get_detail_invoices_by_va(array($va));
        $rs_rincian = $this->m_payment->get_rincian_invoices_ijin($va);
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
                        <td width="24%">Kode Billing</td>
                        <td width="1%">:</td>
                        <td width="50%">' . $rs_id["virtual_account"] . '</td><td rowspan="6" class="flight-tp"><br/><small>Jenis Permohonan :</small><br/><b>IZIN RUTE PENERBANGAN </b><br/>' .
                strtoupper($rs_id["izin_flight"]) . '<br/>' . '<br/>
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
                        <td width="24%">Nama Airlines</td><td width="1%" align="center">:</td><td width="50%"><b>' . $rs_id["airlines_nm"] . '</b></td></tr>
                    <tr>
                        <td width="24%">Tanggal Invoice</td><td width="1%" align="center">:</td><td width="50%">' . $rs_id["inv_date"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">No Invoice</td><td width="1%" align="center">:</td><td width="50%">' . $rs_id["invoice_no"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Total Tagihan</td><td width="1%" align="center">:</td><td width="50%">Rp. ' . number_format($rs_id["inv_total"], 0, ",", ".") . '</td>
                    </tr>
                </table>
            </table>
            </div>';
        $html.='<table width="100%" cellspacing="2" cellpadding="2" border="0" style="font-size:20px;padding:10px">
                        <tbody>
                            <tr>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF"  width="5%" align="center"><b>No</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF"  width="25%" align="center"><b>No Surat Izin</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF"  width="15%" align="center"><b>Group</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF"  width="20%" align="center"><b>Tanggal Permohonan</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF"  width="15%" align="center"><b>Expired Kode Billing</b></td>
                                <td style="background-color:#E84C3D;padding:5px;color:#FFF"  width="20%" align="center"><b>Jumlah Tagihan</b></td>
                            </tr>';
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.='<tr>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . ($index + 1) . '. </td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . $rincian["izin_published_letter"] . '</td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . strtoupper($rincian["group_nm"]) . '</td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . $this->datetimemanipulation->get_full_date($rincian["izin_request_date"], 'ins') . '</td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"], 'ins') . '</td>';
            $html.='<td align="right" style="background-color:#ffffed;padding:5px;">' . number_format($rincian["total_invoice"], 0, ',', '.') . '</td>';
            $html.="</tr>";
            $total+=$rincian["total_invoice"];
        }
        $html.='<tr><td colspan="4" align="left" style="background-color:#ffffed;padding:5px;"><b>TOTAL</b></td>' . '<td align="right" style="background-color:#FCDF5A;padding:5px;font-size:35px;color:#000" colspan="2"><b>' . number_format($total, 0, ",", ".") . '</b></td></tr>';
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
                        </span><br/>';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $va;
        $this->tcpdf->Output($filename . ".pdf", 'D');
    }

    public function history() {
        $this->_set_page_rule("R");
        $this->smarty->assign("template_content", "member/payment_izin/history.html");
        // get search parameter
        $search = $this->tsession->userdata('search_history');
        // search parameters
        $published_no = empty($search['va']) ? '%' : '%' . $search['va'] . '%';
        $status = empty($search['status']) ? '%' : '%' . $search['status'] . '%';
        $this->smarty->assign("search", $search);
        // get list
        $params = array($published_no, '2', $this->com_user['airlines_id'], $status);
        //pagination
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("member/payment_izin/history/");
        $config['total_rows'] = $this->m_payment->count_list_issued_invoice($params);
        $config['uri_segment'] = 4;
        $config['per_page'] = 10;
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
        // get data
        $params = array($published_no, '2', $this->com_user['airlines_id'], $status, ($start - 1), $config['per_page']);
        $arr_tarif = $this->m_preferences->get_preferences_by_group_and_name(array("tarif_rute", "rute"));
        $tarif = $arr_tarif["pref_value"];
        $this->smarty->assign("rs_rekap_bayar", $this->m_payment->get_rekap_pembayaran_rute(array($this->com_user["airlines_id"])));
        $this->smarty->assign("rs_id", $this->m_payment->get_list_issued_invoice($params));
        $this->smarty->assign("total", COUNT($this->m_payment->get_list_issued_invoice($params)));
        $this->smarty->assign("tarif", $tarif);
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
        redirect("member/payment_izin/index");
    }

    // proses pencarian
    public function proses_cari_history() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_history');
        } else {
            $params = array(
                "va" => $this->input->post("va"),
                "status" => $this->input->post("status")
            );
            $this->tsession->set_userdata("search_history", $params);
        }
        // redirect
        redirect("member/payment_izin/history");
    }

    function cetak_kwitansi($inv_id = "") {
        $this->_set_page_rule("R");
        $rs_kwitansi = $this->m_payment->get_kwitansi_izin($inv_id);
        $rs_rincian = $this->m_payment->get_rincian_invoices_ijin_by_id($inv_id);
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
                        <td width="24%">Nama Airlines</td><td width="1%" align="center">:</td><td width="50%"><b>' . $rs_kwitansi["airlines_nm"] . '</b></td><td rowspan="7" class="flight-tp"><br/><small>Jenis Permohonan :</small><br/><b>IZIN RUTE PENERBANGAN </b><br/>' .
                strtoupper($rs_kwitansi["izin_flight"]) . '<br/>' . '<br/>
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
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="25%" align="center"><b>No Surat Izin</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="25%" align="center"><b>Jenis Permohonan</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="15%" align="center"><b>Tanggal Permohonan</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="20%" align="center"><b>Expired Kode Billing</b></td>
                                <td style="background-color:#0070C4;padding:5px;color:#FFF" width="10%" align="center"><b>Jumlah Tagihan</b></td>
                            </tr>';
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.='<tr nobr="true">';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . ($index + 1) . '</td>'
                    . '<td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["izin_published_letter"] . '</td>'
                    . '<td style="background-color:#ffffed;padding:5px;" align="center">' . strtoupper($rincian["group_nm"]) . '</td>';
            $html.='<td style="background-color:#ffffed;padding:5px;" align="center">' . $this->datetimemanipulation->get_full_date($rincian["izin_request_date"]) . '</td>' .
                    '<td style="background-color:#ffffed;padding:5px;" align="center">' . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"]) . '</td>' .
                    '<td align="right" style="background-color:#ffffed;padding:5px;">' . number_format($rincian["total_invoice"], 0, ',', '.') . '</td>';
            $html.='</tr>';
            $total+=$rincian["total_invoice"];
        }
        $html.='<tr><td colspan="4" align="left" style="background-color:#ffffed;padding:5px;"><b>TOTAL</b></td>' . '<td align="right" style="background-color:#FCDF5A;padding:5px;font-size:35px;color:#FFF" colspan="2"><b>' . number_format($total, 0, ",", ".") . '</b></td></tr>';
        $html.='</tbody>
                </table>';
        $html.='<div style="font-size:25px;padding:2px">';
        $html.='Silakan simpan bukti pembayaran ini sebagai salah satu bukti pembayaran yang SAH. Terima Kasih';
        $html.='</div>';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $inv_id;
        $this->tcpdf->Output($filename . ".pdf", 'D');
    }

    public function historydetail($va) {
        $this->_set_page_rule("R");
        $this->smarty->assign("template_content", "member/payment_izin/history-detail.html");
        if (empty($va)) {
            redirect("member/payment_izin");
        }
        $result = $this->m_payment->get_detail_invoice_rute(array($va));
        $rs_id = $this->m_payment->get_rute_by_invoice_no(array($va));
        $arr_tarif = $this->m_preferences->get_preferences_by_group("tarif_rute");
        $rs_tarif = array();
        foreach ($arr_tarif as $tarif) {
            $rs_tarif[$tarif["pref_nm"]] = $tarif["pref_value"];
        }

        $this->smarty->assign("rs_tarif", $rs_tarif);
        $this->smarty->assign("result", $result);
        $this->smarty->assign("rs_id", $rs_id);
        $this->smarty->assign("total", COUNT($rs_id));
        parent::display();
    }

    public function get_last_inv($airlines_id = '') {
        $inv_no = $this->m_payment->get_last_rute_invoice_no($airlines_id);
        echo $inv_no;
    }

}
