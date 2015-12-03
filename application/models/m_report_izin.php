<?php

class m_report_izin extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get list tahun
    function get_list_tahun_report($params) {
        $sql = "SELECT MIN(tahun)'mins', MAX(tahun)'maks' 
                FROM
                (
                        SELECT YEAR(izin_expired_date)'tahun'
                        FROM izin_rute 
                        WHERE airlines_id = ? AND izin_completed = '1' AND izin_approval = 'approved' AND izin_active = 1
                        GROUP BY kode_izin
                        UNION ALL
                        SELECT YEAR(CURRENT_DATE)'tahun'
                ) rs";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by airlines
    function get_izin_rute_data_by_airlines($params) {
        $sql = "SELECT kode_izin, izin_rute_start, aircraft_type, MAX(aircraft_capacity)'aircraft_capacity', frekuensi,
                SUM(frekuensi)'frekuensi_week',
                SUM((IF(pairing='VV',frekuensi*2,frekuensi)*aircraft_capacity))'kapasitas_week',
                SUM(((IF(pairing='VV',frekuensi*2,frekuensi)*aircraft_capacity)*52))'kapasitas_year',
                MIN(izin_expired_date)'izin_expired_date'
                FROM 
                (
                        SELECT kode_izin, kode_frekuensi, izin_expired_date, aircraft_type, aircraft_capacity, dos, pairing, izin_rute_start, izin_rute_end,
                        (
                        IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) +
                        IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) +
                        IF(SUBSTRING(dos, 7, 1) = 0, 0, 1)
                        )'frekuensi'
                        FROM izin_rute
                        WHERE izin_completed = '1' AND izin_active = '1' AND izin_approval = 'approved' 
                        AND airlines_id = ? AND izin_flight = ? AND ? < izin_expired_date
                        AND izin_payment_st = '1'
                        ORDER BY kode_frekuensi ASC
                ) result
                GROUP BY kode_izin
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by kode izin
    function get_izin_rute_data_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, kode_frekuensi, a.izin_start_date, a.izin_expired_date, 
                aircraft_type, aircraft_capacity, dos, ron, pairing, a.izin_rute_start, a.izin_rute_end,
                ( 
                        IF(SUBSTRING(b.doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                izin_published_letter, izin_published_date, a.izin_active, b.doop, b.start_date, b.end_date, c.izin_group, c.registrasi_id, b.tipe, b.capacity, b.roon  
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.izin_completed = '1' AND (izin_active = '1' OR izin_active = '2') AND a.izin_approval = 'approved' 
                AND a.airlines_id = ? AND a.izin_flight = ? AND a.kode_izin = ? AND ? < izin_expired_date 
                AND izin_payment_st = '1' 
                ORDER BY kode_frekuensi ASC, rute_id ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get selected izin rute by kode izin
    function get_selected_izin_rute_data_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, kode_frekuensi, a.izin_start_date, a.izin_expired_date, 
                aircraft_type, aircraft_capacity, dos, ron, pairing, a.izin_rute_start, a.izin_rute_end,
                ( 
                        IF(SUBSTRING(b.doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                izin_published_letter, izin_published_date, a.izin_active, b.start_date, b.end_date, b.tipe, b.capacity, b.roon 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.izin_completed = '1' AND (izin_active = '1' OR izin_active = '2') AND a.izin_approval = 'approved' 
                AND a.airlines_id = ? AND b.izin_id = ?
                AND izin_payment_st = '1' 
                ORDER BY kode_frekuensi ASC, rute_id ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list pairing izin rute by kode izin
    function get_pairing_izin_rute_data_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, kode_frekuensi, a.izin_start_date, a.izin_expired_date, 
                aircraft_type, aircraft_capacity, dos, ron, pairing, a.izin_rute_start, a.izin_rute_end,
                ( 
                        IF(SUBSTRING(b.doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                izin_published_letter, izin_published_date, a.izin_active, b.start_date, b.end_date, b.tipe, b.capacity, b.roon 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.izin_completed = '1' AND (izin_active = '1' OR izin_active = '2') AND a.izin_approval = 'approved' 
                AND a.airlines_id = ? AND a.izin_flight = ? AND a.kode_izin = ? AND ? < izin_expired_date AND b.izin_id != ?
                AND izin_payment_st = '1' 
                ORDER BY kode_frekuensi ASC, rute_id ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by airlines
    function DASAR($params) {
        $sql = "SELECT *, 
                IF(pairing='VV',frekuensi*2,frekuensi)'frekuensi_week',
                (IF(pairing='VV',frekuensi*2,frekuensi)*aircraft_capacity)'kapasitas_week',
                ((IF(pairing='VV',frekuensi*2,frekuensi)*aircraft_capacity)*52)'kapasitas_year'
                FROM 
                (
                        SELECT kode_izin, kode_frekuensi, izin_expired_date, aircraft_type, aircraft_capacity, dos, pairing, izin_rute_start, izin_rute_end,
                        (
                        IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) +
                        IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) +
                        IF(SUBSTRING(dos, 7, 1) = 0, 0, 1)
                        )'frekuensi'
                        FROM izin_rute
                        WHERE izin_completed = '1' AND izin_active = '1' AND izin_approval = 'approved' AND airlines_id = 136
                        ORDER BY kode_frekuensi ASC
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /* ---------- REPORT REGULATOR ---------- */

    // get list tahun regulator
    function get_list_tahun_report_regulator() {
        $sql = "SELECT MIN(tahun)'mins', MAX(tahun)'maks' 
                FROM
                (
                        SELECT YEAR(izin_expired_date)'tahun'
                        FROM izin_rute 
                        WHERE izin_completed = '1' AND izin_approval = 'approved' AND izin_active = 1 AND izin_payment_st = '1'
                        GROUP BY kode_izin
                        UNION ALL
                        SELECT YEAR(CURRENT_DATE)'tahun'
                ) rs";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by airlines regulator
    function get_izin_rute_data_by_airlines_regulator($params) {
        $sql = "SELECT kode_izin, izin_rute_start, aircraft_type, MAX(aircraft_capacity)'aircraft_capacity', frekuensi,
                SUM(frekuensi)'frekuensi_week',
                SUM((IF(pairing='VV',frekuensi*2,frekuensi)*aircraft_capacity))'kapasitas_week',
                SUM(((IF(pairing='VV',frekuensi*2,frekuensi)*aircraft_capacity)*52))'kapasitas_year',
                MIN(izin_expired_date)'izin_expired_date', airlines_nm
                FROM 
                (
                        SELECT a.kode_izin, a.kode_frekuensi, a.izin_expired_date, a.aircraft_type, a.aircraft_capacity, a.dos, a.pairing, a.izin_rute_start, a.izin_rute_end,
                        (
                        IF(SUBSTRING(a.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 3, 1) = 0, 0, 1) +
                        IF(SUBSTRING(a.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 6, 1) = 0, 0, 1) +
                        IF(SUBSTRING(a.dos, 7, 1) = 0, 0, 1)
                        )'frekuensi', b.airlines_nm
                        FROM izin_rute a 
                        LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
                        WHERE a.izin_completed = '1' AND a.izin_active = '1' AND a.izin_approval = 'approved' 
                        AND b.airlines_nm LIKE ? AND a.izin_flight = ? AND ? < a.izin_expired_date
                        ORDER BY a.kode_frekuensi ASC
                ) result
                GROUP BY kode_izin
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by kode izin regulator
    function get_izin_rute_data_by_kode_izin_regulator($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, kode_frekuensi, a.izin_start_date, a.izin_expired_date, 
                aircraft_type, aircraft_capacity, dos, ron, pairing, a.izin_rute_start, a.izin_rute_end,
                ( 
                        IF(SUBSTRING(b.doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                izin_published_letter, izin_published_date, a.izin_active, b.doop, b.start_date, b.end_date, b.tipe, b.capacity, b.roon 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.izin_completed = '1' AND izin_active = '1' AND a.izin_approval = 'approved' 
                AND a.izin_flight = ? AND a.kode_izin = ? AND ? < izin_expired_date 
                ORDER BY kode_frekuensi ASC, rute_id ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // total frekuensi
    function get_total_frekuensi($params) {
        $sql = "SELECT SUM(result.total)'total' 
            FROM 
            (
                SELECT a.izin_id, MAX(SPLIT_STRING(GET_FREKUENSI_TOTAL(b.izin_id, b.flight_no), '/', 1))'total', a.airlines_id, a.kode_izin, kode_frekuensi, pairing, a.izin_rute_start, a.izin_rute_end, GET_FREKUENSI_FROM_DOS(b.doop)'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                c.izin_published_letter, c.izin_published_date, a.izin_active, b.doop, b.start_date, b.end_date, b.tipe, b.capacity, b.roon 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.izin_completed = '1' AND izin_active = '1' AND a.izin_approval = 'approved' 
                AND a.izin_flight = ? AND a.kode_izin = ? AND ? < end_date 
                ORDER BY kode_frekuensi ASC, rute_id ASC
            )result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }

}
