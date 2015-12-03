<?php

class m_published extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get detail by data id
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.operator_name, c.jabatan 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
            LEFT JOIN com_user c ON c.user_id = a.mdb_finish 
            WHERE a.data_id LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get fa data data id
    function get_fa_data_by_data_id($params) {
        $sql = "SELECT a.*, b.user_mail 
            FROM fa_data a 
            LEFT JOIN com_user b ON b.user_id = a.mdb 
            WHERE a.registration_code = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get direktur
    function get_direktur_preference() {
        $sql = "SELECT a.*, b.operator_name, b.jabatan 
            FROM com_preferences a 
            LEFT JOIN com_user b ON b.user_id = a.pref_value 
            WHERE pref_group = 'direktur'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail fa by document no
    function get_detail_fa_by_document_no($params) {
        $sql = "SELECT a.*, b.operator_name, b.jabatan 
            FROM fa_data a 
            LEFT JOIN com_user b ON b.user_id = a.mdb_finish 
            WHERE a.document_no = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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

}
