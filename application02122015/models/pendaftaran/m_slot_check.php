<?php

class m_slot_check extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // load library
        $this->load->library('doslibrary');
    }

    // check slot used
    function is_slot_used_departure($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                INNER JOIN izin_data_slot c ON b.rute_id = c.rute_id
                WHERE a.airlines_id = ? 
                AND c.rute_all = ? 
                AND c.tipe = ?
                AND c.capacity = ? 
                AND c.flight_no = ? 
                AND c.etd = ?
                AND c.doop = ? 
                AND c.roon = ? 
                AND c.start_date = ? 
                AND c.end_date = ?
                AND izin_approval <> 'rejected'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // check slot used by rute
    function is_slot_used_departure_by_rute($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                INNER JOIN izin_data_slot c ON b.rute_id = c.rute_id
                WHERE a.airlines_id = ? 
                AND a.izin_id = ? 
                AND c.rute_all = ? 
                AND c.tipe = ?
                AND c.capacity = ? 
                AND c.flight_no = ? 
                AND c.etd = ?
                AND c.doop = ? 
                AND c.roon = ? 
                AND c.start_date = ? 
                AND c.end_date = ?
                AND izin_approval <> 'rejected'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // check slot used
    function is_slot_used_arrival($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                INNER JOIN izin_data_slot c ON b.rute_id = c.rute_id
                WHERE a.airlines_id = ? 
                AND c.rute_all = ? 
                AND c.tipe = ?
                AND c.capacity = ? 
                AND c.flight_no = ? 
                AND c.eta = ?
                AND c.doop = ? 
                AND c.roon = ? 
                AND c.start_date = ? 
                AND c.end_date = ?
                AND izin_approval <> 'rejected'";
        $query = $this->db->query($sql, $params);
        // return $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // check slot used
    function is_slot_used_arrival_by_rute($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                INNER JOIN izin_data_slot c ON b.rute_id = c.rute_id
                WHERE a.airlines_id = ? 
                AND a.izin_id = ? 
                AND c.rute_all = ? 
                AND c.tipe = ?
                AND c.capacity = ? 
                AND c.flight_no = ? 
                AND c.eta = ?
                AND c.doop = ? 
                AND c.roon = ? 
                AND c.start_date = ? 
                AND c.end_date = ?
                AND izin_approval <> 'rejected'";
        $query = $this->db->query($sql, $params);
        // return $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get slot local time
    function get_slot_local_time($utc_time, $dos, $ron = '0', $airport_utc_sign, $airport_utc, $start_date, $end_date) {
        $data = array(
            'local_time' => '',
            'dos' => '',
            'start_date' => '',
            'end_date' => '',
        );
        // --
        // $utc_time = "0730";
        // $dos = "1234567";
        // $airport_utc_sign = + untuk penambahan;
        // $airport_utc = "07:00:00";
        // $start_date = "2015-10-20";
        // $end_date = "2015-10-20";
        /*
         * TIME
         */
        $utc_time = trim($utc_time);
        $utc_time = substr($utc_time, 0, 2) . ':' . substr($utc_time, 2, 2) . ':00';
        // -
        $airport_utc = $airport_utc . ':00';
        // RON
        $ron = empty($ron) ? '0' : $ron;
        $dos = $this->doslibrary->reverse_dos($dos, $ron);
        // DATE
        $start_date = date('Y-m-d', strtotime("+" . $ron . " days", strtotime($start_date)));
        $end_date = date('Y-m-d', strtotime("+" . $ron . " days", strtotime($end_date)));
        // UTC to LOCAL TIME
        $secs = strtotime($airport_utc) - strtotime("00:00:00");
        if ($airport_utc_sign == '+') {
            // jika +
            $local_time = date("H:i:s", strtotime($utc_time) + $secs);
        } elseif ($airport_utc_sign == '-') {
            // jika -
            $local_time = date("H:i:s", strtotime($utc_time) - $secs);
        }
        // Validate local time and utc
        if ($utc_time > $local_time) {
            $dos = $this->doslibrary->reverse_dos($dos, 1);
            $start_date = date('Y-m-d', strtotime("+" . 1 . " days", strtotime($start_date)));
            $end_date = date('Y-m-d', strtotime("+" . 1 . " days", strtotime($end_date)));
        }
        // result
        $data = array(
            'local_time' => $local_time,
            'dos' => $dos,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
        // --
        return $data;
    }

    // check slot
    function check_slot_local_pairing($departure, $arrival) {
        $st = true;
        // check aircraft type
        if ($departure['tipe'] <> $arrival['tipe']) {
            $st = false;
            // notification
            $this->tnotification->set_error_message('Jenis Pesawat pada departure dan arrival tidak sama!');
        }
        // check capacity
        if ($departure['capacity'] <> $arrival['capacity']) {
            $st = false;
            // notification
            $this->tnotification->set_error_message('Kapasitas Pesawat pada departure dan arrival tidak sama!');
        }
        // check dos
        if ($departure['dos'] <> $arrival['dos']) {
            $st = false;
            // notification
            $this->tnotification->set_error_message('Day of services pada departure dan arrival tidak sama!');
        }
        // check start date
        $days = abs(strtotime($departure['start_date']) - strtotime($arrival['start_date'])) / (60 * 60 * 24);
        if ($days > 7) {
            $st = false;
            // notification
            $this->tnotification->set_error_message('Periode mulai pada departure dan arrival tidak sama!');
        }
        // check end date
        $days = abs(strtotime($departure['end_date']) - strtotime($arrival['end_date'])) / (60 * 60 * 24);
        if ($days > 7) {
            $st = false;
            // notification
            $this->tnotification->set_error_message('Periode mulai pada departure dan arrival tidak sama!');
        }
        // result
        if ($st) {
            $result['start_date'] = ($departure['start_date'] <= $arrival['start_date']) ? $departure['start_date'] : $arrival['start_date'];
            $result['end_date'] = ($departure['end_date'] >= $arrival['end_date']) ? $departure['end_date'] : $arrival['end_date'];
            return $result;
        }
        // return
        return $st;
    }

}
