<?php

class m_report_posisi extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get total report
    function get_total_report($params) {
        $sql = "SELECT COUNT(*)'total' 
            FROM 
            (
                SELECT d.* 
                FROM fa_process a 
                LEFT JOIN fa_flow b ON b.flow_id = a.flow_id 
                LEFT JOIN com_role c ON c.role_id = b.role_id 
                LEFT JOIN fa_data d ON d.data_id = a.data_id 
                LEFT JOIN airlines e ON e.airlines_id = d.airlines_id 
                WHERE a.process_st = 'waiting' AND a.action_st = 'process' AND c.role_id = ?
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
        $sql = "SELECT d.*, b.role_id, b.task_nm, c.role_nm, e.airlines_nm 
            FROM fa_process a 
            LEFT JOIN fa_flow b ON b.flow_id = a.flow_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN fa_data d ON d.data_id = a.data_id 
            LEFT JOIN airlines e ON e.airlines_id = d.airlines_id 
            WHERE a.process_st = 'waiting' AND a.action_st = 'process' AND c.role_id = ?
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
                WHERE MONTH(a.mdd) = ? AND YEAR(a.mdd) = ? AND a.data_type = ? AND a.data_flight = ? AND (d.group_id = ? OR d.group_id = ?) AND a.data_st = 'approved'";
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
        $sql = "SELECT a.*, b.airlines_nm, c.services_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
            LEFT JOIN services_code c ON c.services_cd = a.services_cd
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

    // get role user
    function get_role_user() {
        $sql = "SELECT role_id, role_nm 
            FROM com_role 
            WHERE portal_id = '6' AND role_id != '41'";
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

}
