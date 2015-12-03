<?php

class m_block extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get block id
    function get_block_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get fa belum terbayar
    function get_fa_unpaid($params) {
        $sql = "SELECT COUNT(*)'total' FROM fa_data WHERE airlines_id = ? AND payment_due_date < ? AND published_date != '' AND data_st != 'rejected' AND payment_st = '00'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get izin belum terbayar
    function get_izin_unpaid($params) {
        $sql = "SELECT COUNT(*)'total' FROM fa_data WHERE airlines_id = ? AND payment_due_date < ? AND published_date != '' AND payment_st = '00'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get total data airport
    function get_total_block($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM com_block a 
                INNER JOIN airlines b ON b.airlines_id = a.airlines_id 
                WHERE a.block_st = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }

    // get all data airlines
    function get_all_block($params) {
        $sql = "SELECT a.*, b.airlines_nm, b.airlines_iata_cd, c.operator_name
                FROM com_block a 
                INNER JOIN airlines b ON b.airlines_id = a.airlines_id 
                LEFT JOIN com_user c ON c.user_id = a.block_by 
                WHERE a.block_st = ?
                ORDER BY a.block_date ASC
                LIMIT ?, ?";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail block by id
    function get_detail_block_by_id($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.operator_name 
                FROM com_block a 
                LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
                LEFT JOIN com_user c ON c.user_id = a.block_by 
                WHERE block_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list all airlines
    function get_all_airlines() {
        $sql = "SELECT * FROM airlines ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert
    function insert($params) {
        return $this->db->insert("com_block", $params);
    }

    // update
    function update($params, $where) {
        return $this->db->update("com_block", $params, $where);
    }

    // delete
    function delete($params) {
        $sql = "DELETE FROM airlines WHERE airlines_id = ? ";
        return $this->db->query($sql, $params);
    }

    // get block st by airlines id
    function get_block_st_by_airlines_id($params) {
        $sql = "SELECT * FROM com_block WHERE airlines_id = ? AND block_st = '1' ORDER BY block_date ASC LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
