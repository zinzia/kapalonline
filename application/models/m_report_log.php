<?php

class m_report_log extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get total report log
    function get_total_report_log($params) {
        $sql = "SELECT COUNT(*)'total' 
            FROM 
            (
                SELECT a.*, b.user_name, b.operator_name, b.user_mail, b.lock_st, c.airlines_id, d.airlines_nm 
                FROM com_user_login a 
                LEFT JOIN com_user b ON b.user_id = a.user_id 
                LEFT JOIN com_user_airlines c ON c.user_id = b.user_id 
                LEFT JOIN airlines d ON d.airlines_id = c.airlines_id 
                WHERE b.user_st = 'member' AND member_status = 'operator' AND b.operator_name LIKE ? AND d.airlines_nm LIKE ? 
                ORDER BY a.login_date DESC 
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

    // get list report log
    function get_list_report_log($params) {
        $sql = "SELECT a.*, b.user_name, b.operator_name, b.user_mail, b.lock_st, c.airlines_id, d.airlines_nm 
            FROM com_user_login a 
            LEFT JOIN com_user b ON b.user_id = a.user_id 
            LEFT JOIN com_user_airlines c ON c.user_id = b.user_id 
            LEFT JOIN airlines d ON d.airlines_id = c.airlines_id 
            WHERE b.user_st = 'member' AND member_status = 'operator' AND b.operator_name LIKE ? AND d.airlines_nm LIKE ? 
            ORDER BY a.login_date DESC 
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

    // get all report log
    function get_all_report_log($params) {
        $sql = "SELECT a.*, b.user_name, b.operator_name, b.user_mail, b.lock_st, c.airlines_id, d.airlines_nm 
            FROM com_user_login a 
            LEFT JOIN com_user b ON b.user_id = a.user_id 
            LEFT JOIN com_user_airlines c ON c.user_id = b.user_id 
            LEFT JOIN airlines d ON d.airlines_id = c.airlines_id 
            WHERE b.user_st = 'member' AND member_status = 'operator' AND b.operator_name LIKE ? AND d.airlines_nm LIKE ? 
            ORDER BY a.login_date DESC";
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
        $sql = "SELECT a.*, b.airlines_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
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

    // get all airlines list
    function get_all_airlines() {
        $sql = "SELECT airlines_id, airlines_nm 
            FROM airlines 
            ORDER BY airlines_nm";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
