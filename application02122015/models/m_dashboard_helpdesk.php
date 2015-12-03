<?php

class m_dashboard_helpdesk extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // statistik
    function get_data_statistik_pelayanan($params) {
        $sql = "SELECT a.data_st, a.data_type, a.data_flight, COUNT(*)'total' 
                FROM fa_data a 
                LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
                WHERE a.data_st <> 'open'
                AND b.airlines_nm LIKE ? AND MONTH(a.mdd) = ? AND YEAR(a.mdd) = ?
                GROUP BY a.data_st, a.data_type, a.data_flight";
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
    function get_data_statistik_pelayanan_belum_proses($params) {
        $sql = "SELECT data_st, data_type, data_flight, COUNT(*)'total' 
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                LEFT JOIN airlines c ON c.airlines_id = a.airlines_id 
                WHERE data_st = 'waiting' AND b.action_st = 'process' 
                AND b.flow_id IN(11, 21, 31, 41, 51, 61)
                AND c.airlines_nm LIKE ? AND MONTH(a.mdd) = ? AND YEAR(a.mdd) = ? 
                GROUP BY data_st, data_type, data_flight";
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
