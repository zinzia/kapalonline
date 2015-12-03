<?php

class m_reject extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get detail task
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, operator_name, jabatan, services_nm
                FROM fa_data a
                INNER JOIN airlines b On a.airlines_id = b.airlines_id
                INNER JOIN services_code c ON a.services_cd = c.services_cd
                LEFT JOIN com_user u ON a.mdb_finish = u.user_id
                WHERE data_id = ? AND a.airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total task
    function get_total_finished_task($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'rejected'
                AND airlines_id = ?
                AND (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND document_no LIKE ?
                AND data_type LIKE ?
                AND data_flight LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list task
    function get_list_finished_task($params) {
        $sql = "SELECT a.*, services_nm
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'rejected'
                AND airlines_id = ?
                AND (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND document_no LIKE ?
                AND data_type LIKE ?
                AND data_flight LIKE ?
                ORDER BY published_date DESC
                LIMIT ?, ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail rute data
    function get_data_rute_by_id($params) {
        $sql = "SELECT a.*, b.airport_iata_cd 
            FROM fa_data_rute a 
            LEFT JOIN airport b ON b.airport_id = a.airport_id
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list task
    function get_list_finished_task_print($params) {
        $sql = "SELECT a.*, services_nm
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'rejected'
                AND airlines_id = ?
                AND (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND document_no LIKE ?
                AND data_type LIKE ?
                AND data_flight LIKE ?
                ORDER BY published_date DESC";
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
