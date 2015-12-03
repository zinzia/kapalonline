<?php

class m_user extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get user id
    function get_user_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get total users
    function get_total_user($params) {
        $sql = "SELECT COUNT(*)'total' 
            FROM
            (
                SELECT a.* 
                FROM com_user a 
                LEFT JOIN com_user_airlines b ON b.user_id = a.user_id
                WHERE a.user_st = 'member' AND a.member_status = 'operator' AND a.user_name LIKE ? AND a.operator_name LIKE ? 
                AND b.airlines_id = ?
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

    // get list users
    function get_list_user($params) {
        $sql = "SELECT a.* 
            FROM com_user a 
            LEFT JOIN com_user_airlines b ON b.user_id = a.user_id
            WHERE a.user_st = 'member' AND a.member_status = 'operator' AND a.user_name LIKE ? AND a.operator_name LIKE ? 
            AND b.airlines_id = ? 
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

    // list role
    function get_all_roles_by_portal($params) {
        $sql = "SELECT * FROM com_role 
                WHERE portal_id = ?
                ORDER BY role_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list all airlines
    function get_all_airlines($params) {
        $sql = "SELECT * FROM airlines WHERE airlines_id = ? ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list airlines by user
    function get_all_airlines_by_user($params) {
        $sql = "SELECT airlines_id FROM com_user_airlines a
                INNER JOIN com_user b ON a.user_id = b.user_id
                WHERE a.user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            $airlines_selected = array();
            foreach ($result as $rec) {
                $airlines_selected[] = $rec['airlines_id'];
            }
            return $airlines_selected;
        } else {
            return array();
        }
    }

}
