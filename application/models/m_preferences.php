<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_preferences extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //insert data
    function insert($params) {
        $sql = "INSERT INTO com_preferences (pref_group, pref_nm, pref_value, mdb, mdd)
            VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    //update data
    function update($params) {
        $sql = "UPDATE com_preferences 
            SET pref_group = ?, pref_nm = ?, pref_value = ?,
            mdb = ?, mdd = NOW() WHERE pref_id = ?";
        return $this->db->query($sql, $params);
    }

    //delete data
    function delete($params) {
        $sql = "DELETE FROM com_preferences WHERE pref_id = ?";
        return $this->db->query($sql, $params);
    }

    //get all data
    function get_all_preferences() {
        $sql = "SELECT * FROM com_preferences ";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get by id for searching
    function get_preferences_by_id($id) {
        $sql = "SELECT * FROM com_preferences WHERE pref_id = ?";
        $query = $this->db->query($sql, $id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get by group
    function get_preferences_by_group($params) {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = ? ORDER BY pref_nm ASC, pref_value ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get by id for searching
    function get_list_preferences_by_group_and_name($params) {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = ? AND pref_nm = ? ORDER BY pref_id ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get by id for searching
    function get_preferences_by_group_and_name($params) {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = ? AND pref_nm = ?";
        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get total data
    function get_total_preferences() {
        $sql = "SELECT COUNT(*)'total' FROM com_preferences";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    //get all data for pagination
    function get_all_preferences_limit($params) {
        $sql = "SELECT * FROM com_preferences 
            ORDER BY pref_id LIMIT ?, ?";
        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /*
     * --------------------------------------------------------------
     */

    // update all
    function update_signatures_by_group_name($params) {
        foreach ($params as $key => $value) {
            $data = array($value, $key);
            $sql = "UPDATE com_preferences SET pref_value = ? 
                    WHERE pref_group = 'tanda_tangan' AND pref_nm = ?";
            $this->db->query($sql, $data);
        }
        return true;
    }

    /*
     * SERVICES
     */

    // get all data
    function get_list_services() {
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

    // get detail data
    function get_detail_services($params) {
        $sql = "SELECT * FROM services_code WHERE services_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert data
    function insert_svc($params) {
        $sql = "INSERT INTO services_code (services_cd, services_nm) VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    // update data
    function update_svc($params) {
        $sql = "UPDATE services_code SET services_cd = ?, services_nm = ? WHERE services_cd = ?";
        return $this->db->query($sql, $params);
    }

    // delete data
    function delete_svc($params) {
        $sql = "DELETE FROM services_code WHERE services_cd = ?";
        return $this->db->query($sql, $params);
    }

    /*
     * --------------------------------------------------------------
     */

    /*
     * EMAIL
     */

    // get all data
    function get_list_mail() {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = 'mail'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail mail
    function get_detail_mail($params) {
        $sql = "SELECT * FROM com_preferences WHERE pref_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get mail
    function get_mail() {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = 'mail'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update data
    function update_mail($params) {
        $sql = "UPDATE com_preferences SET pref_value = ? WHERE pref_id = ?";
        return $this->db->query($sql, $params);
    }

    // get redaksional by id
    function get_redaksional_by_id($params) {
        $sql = "SELECT * FROM redaksional WHERE redaksional_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list redaksional
    function get_list_redaksional($params) {
        $sql = "SELECT * FROM redaksional WHERE redaksional_group = ? ORDER BY redaksional_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert data redaksional
    function insert_redaksional($params) {
        return $this->db->insert('redaksional', $params);
    }

    // update data redaksional
    function update_redaksional($params, $where) {
        return $this->db->update('redaksional', $params, $where);
    }

    // delete data redaksional
    function delete_redaksional($params) {
        return $this->db->delete('redaksional', $params);
    }

}
