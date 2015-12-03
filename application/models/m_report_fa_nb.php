<?php

class m_report_fa_nb extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get total report
    function get_total_report($params) {
        $sql = "SELECT COUNT(*)'total' 
            FROM 
            (
                SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd
                WHERE (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type LIKE ?
                AND data_flight LIKE ?
                AND payment_st LIKE ?
                AND airlines_nm LIKE ?
                AND a.services_cd LIKE ?
                AND (d.group_id = ? OR d.group_id = ?)
                AND a.data_st = 'approved'
                GROUP BY a.data_id
            )result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list report
    function get_list_report($params) {
        $sql = "SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd
                WHERE (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type LIKE ?
                AND data_flight LIKE ?
                AND payment_st LIKE ?
                AND airlines_nm LIKE ?
                AND a.services_cd LIKE ?
                AND (d.group_id = ? OR d.group_id = ?)
                AND a.data_st = 'approved'
                GROUP BY a.data_id
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

    // get list report
    function get_all_data_report($params) {
        $sql = "SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd
                WHERE (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type LIKE ?
                AND data_flight LIKE ?
                AND payment_st LIKE ?
                AND airlines_nm LIKE ?
                AND a.services_cd LIKE ?
                AND (d.group_id = ? OR d.group_id = ?)
                AND a.data_st = 'approved'
                GROUP BY a.data_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail by data id
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.operator_name, c.jabatan, d.services_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
            LEFT JOIN com_user c ON c.user_id = a.mdb_finish 
            LEFT JOIN services_code d ON d.services_cd = a.services_cd
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all airport
    function get_all_airport() {
        $sql = "SELECT * FROM airport ORDER BY airport_iata_cd";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all serivces code
    function get_all_services_code() {
        $sql = "SELECT * FROM services_code";
        $query = $this->db->query($sql);
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

    // get remark field
    function get_remark_field($params) {
        $sql = "SELECT b.* 
            FROM fa_rules_field a 
            LEFT JOIN fa_rules b ON b.rules_id = a.rules_id
            WHERE data_type = ? AND data_flight = ? AND services_cd = ?";
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

    // get list services_cd
    function get_list_services() {
        $sql = "SELECT * 
            FROM services_code
            ORDER BY services_nm ASC";
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
