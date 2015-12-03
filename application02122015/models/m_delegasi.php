<?php

class m_delegasi extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // load encrypt
        $this->load->library('encrypt');
    }

    // get id
    function get_new_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // list operator
    function get_all_operator_kasubdit($params) {
        $sql = "SELECT b.* FROM com_user b
                INNER JOIN com_role_user c ON b.user_id = c.user_id
                INNER JOIN com_role d ON c.role_id = d.role_id
                WHERE d.portal_id = 6 AND d.role_nm LIKE '%kasubdit%' AND b.user_id != '2' AND b.user_id != '957' AND b.user_id != '966'
                GROUP BY b.user_id
                ORDER BY b.operator_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list operator
    function get_list_delegasi($params) {
        $sql = "SELECT a.*, b.* 
            FROM com_delegation a 
            LEFT JOIN com_user b ON b.user_id = a.user_id 
            WHERE delegation_st = 'open'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get delegasi detail by id
    function get_delegasi_detail_by_id($params) {
        $sql = "SELECT * 
            FROM com_delegation 
            WHERE field_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert delegation
    function insert($params) {
        return $this->db->insert('com_delegation', $params);
    }

    // insert role user
    function insert_role_user($params) {
        return $this->db->insert('com_role_user', $params);
    }

    // update
    function update($params, $where) {
        return $this->db->update('com_delegation', $params, $where);
    }

    // update delegation st
    function update_delegation_st($params) {
        return $this->db->update('com_delegation', $params);
    }

    // delete com role
    function delete_com_role($params) {
        return $this->db->delete('com_role_user', $params);
    }

    // delete
    function delete($params) {
        return $this->db->delete('com_delegation', $params);
    }

}
