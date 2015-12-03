<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/MemberBase.php' );

// --

class welcome extends ApplicationBase {

    // private $berjadwal = false;
    // private $tidak_berjadwal = false;
    // private $bukan_niaga = false;

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        // $this->load->model('m_member');
        // $this->load->model('m_izin');
        // $this->load->model('m_block');
        // load library
        $this->load->library('tnotification');
        // get airlines type
        // $result = $this->m_member->get_airlines_type(array($this->com_user['airlines_id']));
        // foreach ($result as $value) {
            // $data[] = $value['airlines_type'];
        // }
        // // airlines berjadwal
        // if (in_array("berjadwal", $data)) {
            // $this->berjadwal = true;
        // }
        // // airlines tidak berjadwal
        // if (in_array("tidak berjadwal", $data)) {
            // $this->tidak_berjadwal = true;
        // }
        // // airlines bukan niaga
        // if (in_array("bukan niaga", $data)) {
            // $this->bukan_niaga = true;
        // }
    }

    // routes
    public function index() {
        // set rules
        $this->_set_page_rule('R');
        // set template content
        $this->smarty->assign("template_content", "member/welcome/default.html");
        // load javascript
        $this->smarty->load_javascript("resource/js/countdown/jquery.knob.js");
        $this->smarty->load_javascript("resource/js/countdown/jquery.throttle.js");
        $this->smarty->load_javascript("resource/js/countdown/jquery.classycountdown.min.js");
        // load style ui
        $this->smarty->load_style("countdown/jquery.classycountdown.css");
		$this->smarty->load_javascript("resource/js/messi/messi.min.js");
		$this->smarty->load_style("messi/messi.min.css");
        // // block status
        // $block = $this->m_block->get_block_st_by_airlines_id(array($this->com_user['airlines_id'], '1'));
        // $this->smarty->assign("block", $block);
        // // fa unpaid
        // $unpaid = $this->m_block->get_fa_unpaid(array($this->com_user['airlines_id'], date('Y-m-d')));
        // $this->smarty->assign("unpaid", $unpaid);
        // // date
        $now['tanggal'] = $this->datetimemanipulation->get_full_date(date('Y-m-d'));
        $now['hari'] = date('l');
        $this->smarty->assign("tanggal", $now);
        /*
         * FA
         */
        // list my task flight approval berjadwal
        // if ($this->berjadwal == true) {
        // $list_waiting = $this->m_member->get_list_my_task_waiting_berjadwal(array($this->com_user['airlines_id']));
            // $this->smarty->assign("list_waiting_sch", $list_waiting);
            // $this->smarty->assign("total_waiting_sch", count($list_waiting));
        // } else {
            // $this->smarty->assign("list_waiting_sch", null);
            // $this->smarty->assign("total_waiting_sch", 0);
        // }
        // if ($this->tidak_berjadwal == true OR $this->bukan_niaga == true) {
        // // list my task flight approval tidak berjadwal
        // $list_waiting = $this->m_member->get_list_my_task_waiting_tidak_berjadwal(array($this->com_user['airlines_id']));
            // $this->smarty->assign("list_waiting_unsch", $list_waiting);
            // $this->smarty->assign("total_waiting_unsch", count($list_waiting));
        // } else {
            // $this->smarty->assign("list_waiting_unsch", null);
            // $this->smarty->assign("total_waiting_unsch", 0);
        // }
        // $this->smarty->assign("berjadwal", $this->berjadwal);
        // $this->smarty->assign("tidak_berjadwal", $this->tidak_berjadwal);
        // $this->smarty->assign("bukan_niaga", $this->bukan_niaga);
        // // get last day fa payment
        // $last_fa = $this->m_member->get_last_fa_payment(array($this->com_user['airlines_id']));
        // if ($last_fa) {
            // $date = strtotime("+" . (intval($last_fa['selisih_hari']) + 1) . " day");
            // $due_date = date('Y-m-d', $date);
        // } else {
            // $due_date = date('Y-m-d');
        // }
        // $this->smarty->assign("last_fa", $last_fa);
        // $this->smarty->assign("now", strtotime("now"));
        // $this->smarty->assign("end", strtotime($due_date));
        /*
         * IZIN RUTE
         */
        /*
          // list my task izin rute domestik
          $list_waiting = $this->m_izin->get_list_my_task_waiting_izin(array($this->com_user['airlines_id'], 'domestik'));
          $this->smarty->assign("list_waiting_domestik", $list_waiting);
          $this->smarty->assign("total_waiting_domestik", count($list_waiting));
          // list my task izin rute internasional
          $list_waiting = $this->m_izin->get_list_my_task_waiting_izin(array($this->com_user['airlines_id'], 'internasional'));
          $this->smarty->assign("list_waiting_internasional", $list_waiting);
          $this->smarty->assign("total_waiting_internasional", count($list_waiting));
         */
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

}
