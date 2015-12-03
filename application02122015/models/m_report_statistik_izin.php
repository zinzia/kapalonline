<?php

class m_report_statistik_izin extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get total pengajuan fa
    function get_total_pengajuan() {
        $sql = "SELECT data_type, data_flight, COUNT(*)'total' 
            FROM fa_data 
            GROUP BY data_type, data_flight";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return 0;
        }
    }

    // statistik
    function get_data_statistik_pelayanan_izin($params) {
        $sql = "SELECT a.izin_group, c.group_nm, a.izin_approval, a.izin_flight, COUNT(*)'total' 
                FROM izin_registrasi a 
                LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
                LEFT JOIN izin_group c ON c.group_id = a.izin_group 
                WHERE a.izin_request_st = '1'
                AND b.airlines_nm LIKE ? AND MONTH(a.mdd) = ? AND YEAR(a.mdd) = ? 
                GROUP BY a.izin_group, a.izin_approval, a.izin_flight";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // statistik
    function get_data_statistik_pelayanan_izin_belum_proses($params) {
        $sql = "SELECT a.izin_group, c.group_nm, a.izin_approval, a.izin_flight, COUNT(*)'total' 
                FROM izin_registrasi a 
                LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
                LEFT JOIN izin_group c ON c.group_id = a.izin_group 
                WHERE a.izin_request_st = '1' 
                AND b.airlines_nm LIKE ? AND MONTH(a.mdd) = ? AND YEAR(a.mdd) = ? 
                GROUP BY a.izin_group, a.izin_approval, a.izin_flight";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // chart
    function get_data_chart_pie($params) {
        $sql = "SELECT a.data_st, COUNT(*)'total' 
                FROM fa_data a 
                LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
                WHERE a.data_st <> 'open'
                AND b.airlines_nm LIKE ? AND MONTH(a.mdd) = ? AND YEAR(a.mdd) = ? 
                GROUP BY a.data_st";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // chart
    function get_data_chart_fa($params) {
        $sql = "SELECT MONTH(a.mdd)'bulan', COUNT(data_id)'total'
                FROM fa_data a
                WHERE a.data_completed = '1' AND data_st <> 'open' AND data_type = ?
                AND data_id NOT IN('00000001', '00000002', '00000003', '00000004')
                AND YEAR(a.mdd) = YEAR(CURRENT_DATE)
                GROUP BY YEAR(a.mdd), MONTH(a.mdd)";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list airlines
    function get_list_airlines() {
        $sql = "SELECT * 
            FROM airlines
            ORDER BY airlines_nm ASC";
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
