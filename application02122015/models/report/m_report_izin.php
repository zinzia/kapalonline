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
                        WHERE airlines_id = ? AND izin_completed = '1' AND izin_approval = 'approved'
                        GROUP BY kode_izin
                        UNION ALL
                        SELECT YEAR(CURRENT_DATE)'tahun'
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array('mins' => date('Y-m-d'));
        }
    }

    // get list izin rute by airlines
    function get_izin_rute_data_by_airlines($params) {
        $sql = "SELECT 
                izin_rute_start, izin_rute_end, MAX(masa_berlaku)'masa_berlaku', tipe, capacity, SUM(frekuensi)'total_frekuensi',
                SUM((IF(pairing = 'VV', frekuensi * 2, frekuensi) * capacity))'kapasitas_week',
                SUM(((IF(pairing = 'VV', frekuensi * 2, frekuensi) * capacity) * 52))'kapasitas_year',
                SUM(penerbangan)'total_penerbangan', pairing
                FROM 
                (
                        SELECT b.izin_id, b.izin_rute_start, b.izin_rute_end, MAX(b.izin_expired_date)'masa_berlaku',
                        b.pairing, MAX(a.tipe)'tipe', MAX(a.capacity)'capacity', MAX(GET_FREKUENSI_TOTAL(a.izin_id, a.flight_no))'frekuensi',
                        COUNT(flight_no)'penerbangan'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        INNER JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                        WHERE b.airlines_id = ? AND b.izin_flight = ? AND c.izin_season LIKE ?
                        AND b.izin_completed = '1' AND b.izin_approval = 'approved' AND b.izin_active = '1'
                        GROUP BY a.izin_id
                ) result
                WHERE masa_berlaku >= ? 
                AND (izin_rute_start LIKE ? OR izin_rute_end LIKE ?)
                GROUP BY izin_rute_start, izin_rute_end";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list detail izin rute by airlines
    function get_list_detail_izin_rute_by_params($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, a.kode_frekuensi, a.registrasi_id, 
                a.izin_completed, a.izin_approval, a.izin_type, a.izin_flight, 
                izin_st, a.izin_start_date, a.izin_expired_date, 
                a.izin_rute_start, a.izin_rute_end, a.pairing, 
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi',
                c.izin_published_letter
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.airlines_id = ? AND a.izin_flight = ? 
                AND a.izin_completed = '1' AND a.izin_approval = 'approved' AND a.izin_active = '1'
                AND izin_season = ? AND izin_expired_date >= ?
                AND (a.izin_rute_start = ? OR a.izin_rute_end = ?) 
                AND (a.izin_rute_start = ? OR a.izin_rute_end = ?)
                GROUP BY a.izin_id
                ORDER BY izin_rute_start ASC";
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

    // get list izin data by id
    function get_list_izin_data_by_id($params) {
        $sql = "SELECT rute_id, a.izin_id, rute_all, tipe, capacity, 
                IF(LENGTH(TRIM(flight_no)) > 4, TRIM(flight_no), CONCAT(airlines_iata_cd, TRIM(flight_no)))'flight_no', 
                etd, eta, doop, roon, 
                start_date, end_date, a.is_used_score, GET_FREKUENSI_FROM_DOS(doop)'frekuensi',
                kode_izin, kode_frekuensi, izin_approval, c.airlines_iata_cd, airlines_nm
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id 
                INNER JOIN airlines c ON c.airlines_id = b.airlines_id 
                WHERE a.izin_id = ?
                ORDER BY rute_all ASC, etd ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by airlines regulator
    function get_izin_rute_data_by_airlines_regulator($params) {
        $sql = "SELECT 
                airlines_nm, izin_rute_start, izin_rute_end, MAX(masa_berlaku)'masa_berlaku', tipe, capacity, SUM(frekuensi)'total_frekuensi',
                SUM((IF(pairing = 'VV', frekuensi * 2, frekuensi) * capacity))'kapasitas_week',
                SUM(((IF(pairing = 'VV', frekuensi * 2, frekuensi) * capacity) * 52))'kapasitas_year',
                SUM(penerbangan)'total_penerbangan', pairing
                FROM 
                (
                        SELECT d.airlines_nm, b.izin_id, b.izin_rute_start, b.izin_rute_end, MAX(b.izin_expired_date)'masa_berlaku',
                        b.pairing, MAX(a.tipe)'tipe', MAX(a.capacity)'capacity', MAX(GET_FREKUENSI_TOTAL(a.izin_id, a.flight_no))'frekuensi',
                        COUNT(flight_no)'penerbangan'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        INNER JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                        INNER JOIN airlines d ON b.airlines_id = d.airlines_id
                        WHERE b.airlines_id = ? AND b.izin_flight = ? AND c.izin_season LIKE ?
                        AND b.izin_completed = '1' AND b.izin_approval = 'approved' AND b.izin_active = '1'
                        GROUP BY a.izin_id
                ) result
                WHERE masa_berlaku >= ? 
                AND (izin_rute_start LIKE ? OR izin_rute_end LIKE ?)
                GROUP BY izin_rute_start, izin_rute_end";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
