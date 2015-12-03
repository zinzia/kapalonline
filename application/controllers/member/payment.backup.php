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
            // get virtual account
            $virtual_account = $this->m_payment->get_virtual_account_by_airlines(array($this->com_user['airlines_id']));
            if (empty($virtual_account)) {
                // redirect
                redirect("member/payment");
            }
            // tarif
            $tarif = $this->m_payment->get_tabel_tarif_fa(array($this->com_user['airlines_id']));
            $search = $this->tsession->userdata('search_payment');
            $data_flight = empty($search['data_flight']) ? 'domestik' : $search['data_flight'];
            // create invoices
            $virtual_account = substr($virtual_account, 0, 7);
            // get last virtual account transaction
            $virtual_account = $this->m_payment->get_last_virtual_account($virtual_account);
            // get inv id
            $inv_id = $this->m_payment->get_invoice_id();
            // params invoices
            $params = array(
                "inv_id" => $inv_id,
                "airlines_id" => $this->com_user['airlines_id'],
                "category" => "1",
                "virtual_account" => $virtual_account,
                "inv_st" => "open",
                "inv_date" => date('Y-m-d H:i:s'),
                "generate_st" => "no",
                "mdb" => $this->com_user['user_id'],
                "mdd" => date('Y-m-d H:i:s'),
            );
            // insert invoices
            $total_inv = 0;
            if ($this->m_payment->insert_invoice($params)) {
                foreach ($data as $value) {
                    $params = array(
                        "payment_st" => "00",
                        "payment_date" => date('Y-m-d H:i:s'),
                        "payment_invoice" => $virtual_account,
                        "payment_tarif" => $tarif[$data_flight],
                        "mdb_payment" => $this->com_user['user_id'],
                    );
                    $where = array(
                        "data_id" => $value,
                        "payment_invoice" => NULL,
                    );
                    $this->m_payment->update_payment($params, $where);
                    // total inv
                    $total_inv += $tarif[$data_flight];
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
        $rs_id = $this->m_payment->get_fa_by_invoice_no(array($result['virtual_account']));
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
        $htmlx = '';
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
                    <td style="font-size:20px">Telp : 021-3811308 Faks : 021-3451657 email : info151@dephub.go.id</td>
                </tr>
            </table><hr/>';
        $html.='<br/><div style="border:1px solid #000;padding:2px;text-align:center"><b>INVOICE #' . $rs_id["invoice_no"] . '</b></div><br/>';
        $html.='<span style="font-size:20px">Kepada Yang Terhormat,<br/><b>' . $rs_id["airlines_nm"] . '</b></span><br/><br/><span style="font-size:20px">Berikut disampaikan tagihan atas pembayaran permohonan flight approval<br><span style="font-size:20px">Email ini <strong>BUKAN BUKTI PEMBAYARAN YANG SAH</strong>. Silakan selesaikan transaksi anda dengan detail sebagai berikut :</span><br/><br/>';
        $html.='<table width="100%" style="font-family:Arial,Helvetica,sans-serif;font-size:19px;" border="0" cellpadding="2">
                    <tr>
                        <td width="25%" style="padding:5px">Nomor Tagihan</td>
                        <td width="2%"  style="padding:5px">:</td>
                        <td width="73%" style="padding:5px"><b>' . $rs_id["invoice_no"] . '</b></td>
                    </tr>
                    <tr>
                        <td width="25%" style="padding:5px">Nomor VA</td>
                        <td width="2%"  style="padding:5px">:</td>
                        <td width="73%" style="padding:5px"><b>' . $rs_id["virtual_account"] . '</b></td>
                    </tr>
                    <tr>
                        <td width="25%" style="padding:5px">Jumlah Tagihan</td>
                        <td width="2%"  style="padding:5px">:</td>
                        <td width="73%" style="padding:5px"><b>' . number_format($rs_id["inv_total"], 0, ",", ".") . '</b></td>
                    </tr>
                    <tr>
                        <td width="25%">Tanggal Tagihan</td>
                        <td width="2%">:</td>
                        <td width="73%"><b>' . $this->datetimemanipulation->get_full_date($rs_id["inv_date"]) . '</b></td>
                    </tr>
                </table><br/>';
        $html.='<br/><table width="100%" cellspacing="0" cellpadding="2" border="0.5" style="font-size:20px;padding:10px">
                        <tbody>
                            <tr>
                                <td style="background-color:#ECEADC;padding:5px" width="5%" align="center"><b>No</b></td>
                                <td style="background-color:#ECEADC;padding:5px" width="25%" align="center"><b>Published No</b></td>
                                <td style="background-color:#ECEADC;padding:5px" width="15%" align="center"><b>Aircraft Type</b></td>
                                <td style="background-color:#ECEADC;padding:5px" width="15%" align="center"><b>Flight No</b></td>
                                <td style="background-color:#ECEADC;padding:5px" width="15%" align="center"><b>Rute</b></td>
                                <td style="background-color:#ECEADC;padding:5px" width="15%" align="center"><b>Batas Bayar</b></td>
                                <td style="background-color:#ECEADC;padding:5px" width="10%" align="center"><b>Tarif</b></td>
                            </tr>';
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.='<tr nobr="true">';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;">' . ($index + 1) . '</td><td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["published_no"] . '</td><td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["aircraft_type"] . '</td>';
            $html.='<td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["flight_no"] . '</td>' . '<td style="background-color:#ffffed;padding:5px;" align="center">' . $rincian["rute_all"] . '</td>' . '<td style="background-color:#ffffed;padding:5px;" align="center">' . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"], "ins") . '</td><td align="right" style="background-color:#ffffed;padding:5px;">' . number_format($rincian["payment_tarif"], 0, '.', ',') . '</td>';
            $html.='</tr>';
            $total+=$rincian["payment_tarif"];
        }
        $html.='<tr><td colspan="6" align="left" style="background-color:#ffffed;padding:5px;"><b>TOTAL</b></td>' . '<td align="right" style="background-color:#ffffed;padding:5px;">' . number_format($total, 0, ",", ".") . '</td></tr>';
        $html.='        </tbody>
                        </table><br/>
                        <span style="font-size:20px">
                            * <strong>PERHATIAN :</strong> Pembayaran yang akan diproses adalah pembayaran yang dilakukan dan diterima Kementerian Perhubungan pada pukul 07.00 s.d 18.00 WIB setiap hari. Pembayaran yang diterima di luar waktu tsb akan diproses pada hari berikutnya
                        </span><br/>
                        <span style="font-size:20px">
                            * Akan dikenakan biaya Rp 6.500,-/transaksi (tidak termasuk di total transaksi) untuk pembayaran melalui jaringan ATM Bersama dan Prima. Biaya yang dikenakan sesuai dengan ketetapan ATM Bersama dan Prima. Untuk transaksi di channel BNI (Teller, ATM, Internet Banking) tidak dikenakan biaya
                        </span><br/>
                        <span style="font-size:20px">
                            * Pembayaran melalui internet banking BNI harap mencantumkan nomor Virtual Account pada kolom Narasi/Berita
                        </span><br/>
                        <span style="font-size:20px">
                            * Pengembalian pembayaran dikarenakan transaksi gagal (kelebihan/kekurangan bayar) akan dilakukan pada hari kerja berikutnya melalui rekening airlines
                        </span><br/>
                        <span style="font-size:20px">
                            * Jika ada kendala dan pertanyaan dapat menghubungi :
                            <ul>
                                <li>Call Center Kementerian Perhubungan : (021) 151 (24 jam, setiap hari)</li>
                                <li>BNI : (021) 29946046 (06.00 – 18.00 WIB, hari kerja)</li>
                                </ul>
                            </span><br/><br/>
                         <span style="font-size:20px;text-align:center"><b><u>Silakan Ikuti Langkah Pembayaran Sesuai dengan Kartu ATM yang digunakan</u></b></span><br/>
                         <span style="font-size:20px">
                         Pembayaran melalui ATM BCA/Bank sejenis dalam Jaringan PRIMA :
                            <ol>
                                <li>Masukkan PIN</li>
                                <li>Pilih "Transaksi Lainnya"</li>
                                <li>Pilih "Transfer"</li>
                                <li>Pilih "Ke Rek Bank Lain"</li>
                                <li>Masukkan kode sandi Bank BNI (009) kemudian tekan "Benar"</li>
                                <li>Masukkan Jumlah pembayaran sesuai dengan yang ditagihkan dalam invoice (Jumlah yang ditransfer harus sama persis dengan yang ada pada invoice, tidak lebih dan tidak kurang)<br/>
                                    <b>Penting : </b>Jumlah nominal yang tidak sesuai dengan tagihan pada itinerary akan menyebabkan transaksi gagal.</li>
                                <li>Masukkan 16 Nomor Virtual Account (8022 012 100000001)</li>
                                <li>Muncul Layar Konfirmasi Transfer yang berisi nomor Virtual Account dan Nama Maskapai beserta jumlah yang dibayar, jika sudah benar, Tekan "Benar".</li>
                                <li>Selesai</li>
                            </ol>
                        </span><br/>
                        <span style="font-size:20px">
                         Pembayaran melalui ATM Mandiri/Bank sejenis dalam Jaringan ATM Bersama :
                            <ol>
                                <li>Pilih Bahasa</li>
                                <li>Masukkan PIN
                                <li>Pilih "Transaksi Lainnya"
                                <li>Pilih "Transfer"
                                <li>Pilih "Ke Rekening Bank Lain ATM Bersama/Link"
                                <li>Masukkan kode bank (009) + 16 Nomor Virtual Account (8022012 100000001)
                                <li>Masukkan Jumlah pembayaran sesuai dengan yang ditagihkan dalam Invoice (Jumlah yang ditransfer harus sama persis dengan yang ada pada invoice, tidak lebih dan tidak kurang).<br/>
                                    <b>Penting : </b>Jumlah nominal yang tidak sesuai dengan tagihan pada itinerary akan menyebabkan transaksi gagal.</li>
                                <li>Kosongkan nomor referensi transfer kemudian tekan "Benar".</li>
                                <li>Muncul Layar Konfirmasi Transfer yang berisi nomor Virtual Account dan Nama Maskapai beserta jumlah yang dibayar, jika sudah benar, Tekan "Benar".</li>
                                <li>Selesai</li>
                            </ol>
                        </span><br/>
                        <span style="font-size:20px">
                            Pembayaran melalui ATM Bank BNI :
                            <ol>
                                <li>Pilih Bahasa</li>
                                <li>Masukkan PIN</li>
                                <li>Pilih "Transfer"</li>
                                <li>Pilih "ke rekening BNI"</li>
                                <li>Masukkan 16 Nomor Virtual Account (8022012 100000001)
                                <li>Masukkan Jumlah pembayaran sesuai dengan yang ditagihkan dalam Invoice (Jumlah yang ditransfer harus sama persis dengan yang ada pada invoice, tidak lebih dan tidak kurang).<br/>
                                    <b>Penting : </b>Jumlah nominal yang tidak sesuai dengan tagihan pada itinerary akan menyebabkan transaksi gagal.
                                <li>Muncul Layar Konfirmasi yang berisi Nomor Virtual Account dan Nama Maskapai serta jumlah yang dibayar, bila sudah benar pilih "Benar".</li>
                                <li>Selesai</li>
                            </ol>
                            </span><br/><br/>
                            <span style="font:bold 11px Tahoma;text-align:center"><b>"Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami"</b></span><br>
                            </td>
                        </tr>
                    </tbody>
                    </table>';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = $va;
        $this->tcpdf->Output($filename . ".pdf", 'D');
    }

    function cetak_kwitansi($va = "") {
        $this->_set_page_rule("R");
        $rs_kwitansi = $this->m_payment->get_kwitansi($va);
        $rs_rincian = $this->m_payment->get_rincian_invoices($va);
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
            <table width="100%">
                <tr>
                    <td width="20%" rowspan="6"><img src="resource/doc/images/logo/logo.jpg" width="48px" style="vertical-align:middle"></td>
                    <td width="60%" style="font-size:30px" align="center"><b>KEMENTERIAN PERHUBUNGAN</b></td>
                    <td width="20%" rowspan="6" align="right"><br/><br/><img src="resource/doc/images/logo/logo-bni.jpg" width="75px" style="vertical-align:middle"></td>
                </tr>
                <tr>
                    <td style="font-size:28px" align="center">DIREKTORAT JENDERAL PERHUBUNGAN UDARA</td>
                </tr>
                <tr>
                    <td style="font-size:25px" align="center">PENDAFTARAN KAPAL ONLINE</td>
                </tr>
                <tr>
                    <td style="font-size:25px"></td>
                </tr>
                <tr>
                    <td style="font-size:20px" align="center">Jl. Medan Merdeka Barat No. 8, Jakarta 10110</td>
                </tr>
                <tr>
                    <td style="font-size:20px" align="center">Telp : 021-3811308 Faks : 021-3451657 email : info151@dephub.go.id</td>
                </tr>
            </table><hr/>
            <br/>
            <div style="font-size:25px;text-align:center"><b><u>BUKTI PEMBAYARAN</u></b><br/>NO : OL.' . $rs_kwitansi["no_kuitansi"] . ' </div>
            <div style="font-size:25px;text-align:center;border-style: solid;border-width: 1px 1px 1px 1px;">
                <table class="table-form">
                    <tr>
                        <td width="24%">Sudah diterima dari</td><td width="1%">:</td><td width="75%">' . $rs_kwitansi["airlines_nm"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Perusahaan/Instansi</td><td width="1%">:</td><td width="75%">' . $rs_kwitansi["airlines_nm"] . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Banyaknya Uang</td><td width="1%">:</td><td width="75%">' . trim(ucwords(numb_to_alphabet($rs_kwitansi["inv_total"]))) . " Rupiah" . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Untuk pembayaran</td><td width="1%">:</td>
                        <td width="75%"><br/><table class="table-form" cellpadding="1" cellspacing="1">';
        foreach ($rs_rincian as $rincian) {
            $html.='<tr><td width="65%">' . $rincian["published_no"] . '</td>' . '<td width="5%">Rp. </td><td width="30%">' . number_format($rincian["payment_tarif"], 0, ".", ",") . '</td></tr>';
        }
        $html.='</table></td>
                    </tr>
                    <tr>
                        <td width="24%">Keterangan</td><td width="1%">:</td><td width="75%">Telah diterima pembayaran FA ' . ucwords($rs_kwitansi["data_flight"]) . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Jumlah</td><td width="1%">:</td><td width="75%">Rp ' . number_format($rs_kwitansi["inv_total"], 0, ".", ",") . '</td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="24%">Denda</td><td width="1%">:</td><td width="75%">___________________________________</td>
                    </tr>
                    <tr>
                        <td width="24%">Jumlah</td><td width="1%">:</td><td width="75%">Rp ' . number_format($rs_kwitansi["inv_total"], 0, ".", ",") . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Terbilang</td><td width="1%">:</td><td width="75%">' . trim(ucwords(numb_to_alphabet($rs_kwitansi["inv_total"]))) . " Rupiah" . '</td>
                    </tr>
                    <tr>
                        <td width="24%">Virtual Account</td><td width="1%">:</td><td width="75%">' . $rs_kwitansi["virtual_account"] . '</td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3"></td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3" style="text-align:right">Jakarta, ' . $this->datetimemanipulation->get_full_date($rs_kwitansi["tgl_update"]) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>Petugas PNBP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="24%"><small><b>Catatan</b></small>
                        <ol><small>
                            <li>Lembar 1 : untuk Wajib Bayar</li>
                            <li>Lembar 2 : untuk Bendahara Penerima</li>
                            <li>Lembar 3 : untuk Untuk Petugas Bank (Kasir)</li>
                            <li>Lembar 4 : untuk Petugas PNBP</li>
                            </small>
                        </ol>
                        </td>
                    </tr>
                </table>
            </div>
            ';
        $this->tcpdf->writeHTML($html, true, false, true, false, '');
        // output (D : download, I : view)
        $filename = "BUKTI.BAYAR." . $va;
        $this->tcpdf->Output($filename . ".pdf", 'D');
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
            echo "gagal request billing karena :<br/>".$exc->getMessage();
        }
    }

}
