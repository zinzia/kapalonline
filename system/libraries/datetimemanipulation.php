<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class datetimemanipulation {

    // init var
    public $arr_lang = array();
    public $arr_hari = array();

    function __construct() {
        // indonesia
        $this->arr_lang['in'] = array('01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember', '00' => '--');
        // english
        $this->arr_lang['en'] = array('01' => 'January', '02' => 'February', '03' => 'Maret', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December', '00' => '--');
        // indonesia short date
        $this->arr_lang['ins'] = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Ags', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des', '00' => '--');
        // english short date
        $this->arr_lang['ens'] = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ags', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec', '00' => '--');
        // hari indonesia
        $this->arr_hari = array('1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu', '7' => 'Minggu');
    }

    function __destruct() {
        
    }

    public function get_full_date($tgl, $lang = "in") {
        // check input tanggal
        $tgl = explode(' ', $tgl);
        $tgl_day = isset($tgl[0]) ? $tgl[0] : '00-00-0000';
        $tgl_jam = isset($tgl[1]) ? $tgl[1] : '';
        // tanggal
        $tgl_day = explode('-', $tgl_day);
        if (count($tgl_day) != 3) {
            return '';
        }
        // jam
        $tgl_jam = explode(':', $tgl_jam);
        if (count($tgl_jam) != 3) {
            $tgl_jam = '';
        }
        // parse date
        $month_label = isset($this->arr_lang[$lang][$tgl_day[1]]) ? $this->arr_lang[$lang][$tgl_day[1]] : $this->arr_lang[$lang]['00'];
        $date_label = $tgl_day[2] . ' ' . $month_label . ' ' . $tgl_day[0];
        // parse time
        if (!empty($tgl_jam)) {
            $tgl_jam = $tgl_jam[0] . ':' . $tgl_jam[1] . ':' . $tgl_jam[2];
            $date_label .= ' Jam ' . $tgl_jam;
        }
        // return
        return $date_label;
    }

    public function get_short_date($tgl) {
        // check input tanggal
        $tgl = explode(' ', $tgl);
        $tgl_day = isset($tgl[0]) ? $tgl[0] : '00-00-0000';
        $tgl_jam = isset($tgl[1]) ? $tgl[1] : '';
        // tanggal
        $tgl_day = explode('-', $tgl_day);
        if (count($tgl_day) != 3) {
            return '';
        }
        // jam
        $tgl_jam = explode(':', $tgl_jam);
        if (count($tgl_jam) != 3) {
            $tgl_jam = '';
        }
        // parse date
        $date_label = $tgl_day[0] . '/' . $tgl_day[1] . '/' . $tgl_day[2];
        // parse time
        if (!empty($tgl_jam)) {
            $tgl_jam = $tgl_jam[0] . ':' . $tgl_jam[1] . ':' . $tgl_jam[2];
            $date_label .= ' ' . $tgl_jam;
        }
        // return
        return $date_label;
    }

    // get date now
    public function get_date_now() {
        // hari ind
        $date['hari'] = $this->arr_hari[date('N')];
        // tanggal
        $date['tanggal'] = date('d');
        // bulan ind
        $date['bulan'] = $this->arr_lang['in'][date('m')];
        // tahun
        $date['tahun'] = date('Y');
        // return
        return $date;
    }

    // get date now
    public function get_date_indonesia($date_indo) {
        // hari ind
        $hari = date("N", strtotime($date_indo));
        $date['hari'] = $this->arr_hari[$hari];
        // tanggal
        $tanggal = date("d", strtotime($date_indo));
        $date['tanggal'] = $tanggal;
        // bulan ind
        $bulan = date("m", strtotime($date_indo));
        $date['bulan'] = $this->arr_lang['in'][$bulan];
        $date['numeric_bulan'] = date("m", strtotime($date_indo));
        // tahun
        $tahun = date("Y", strtotime($date_indo));
        $date['tahun'] = $tahun;
        // return
        return $date;
    }

    function nicetime($date) {
        if (empty($date)) {
            return "";
        }

        $periods = array("detik", "menit", "jam", "hari", "minggu", "bulan", "tahun", "dekade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();
        $unix_date = strtotime($date);

        // check validity of date
        if (empty($unix_date)) {
            return "";
        }

        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            $tense = "yang lalu";
        } else {
            $difference = $unix_date - $now;
            $tense = "yang lalu";
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j].= "";
        }

        return "$difference $periods[$j] {$tense}";
    }

    /* ==================== EDIT ==================== */

    // get date only
    public function get_date_only($tgl, $lang = "in") {
        // check input tanggal
        $tgl = explode(' ', $tgl);
        $tgl_day = isset($tgl[0]) ? $tgl[0] : '00-00-0000';
        // tanggal
        $tgl_day = explode('-', $tgl_day);
        if (count($tgl_day) != 3) {
            return '';
        }
        // parse date
        $month_label = isset($this->arr_lang[$lang][$tgl_day[1]]) ? $this->arr_lang[$lang][$tgl_day[1]] : $this->arr_lang[$lang]['00'];
        $date_label = $tgl_day[2] . ' ' . $month_label . ' ' . $tgl_day[0];
        // return
        return $date_label;
    }

    // frekuensi dos
    public function get_frequensi_dos($dos = '0000000') {
        // check input tanggal
        $frekuensi = 0;
        for ($i = 0; $i <= 6; $i++) {
            $sign = substr($dos, $i, 1);
            if ($sign <> '0') {
                $frekuensi++;
            }
        }
        // return
        return $frekuensi;
    }

    // convert UTC to local time
    public function get_local_time_from_utc($utc = '', $local_sign = '', $local_time = '') {
        // $utc = "06:58:00";
        // $local_sign = + untuk penambahan;
        // $local_time = "07:00:00" untuk jakarta;
        $utc = trim($utc);
        if (!empty($utc)) {
            $utc = substr($utc, 0, 2) . ':' . substr($utc, 2, 2) . ':00';
            $local_time = $local_time . ':00';
            $result = '00:00';
            $secs = strtotime($local_time) - strtotime("00:00:00");
            if ($local_sign == '+') {
                // jika +
                $result = date("H:i:s", strtotime($utc) + $secs);
            } elseif ($local_sign == '-') {
                // jika -
                $result = date("H:i", strtotime($utc) - $secs);
            }
            return substr($result, 0, 5);
        }
    }

}
