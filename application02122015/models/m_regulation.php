<?php

class m_regulation extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // get list regulation
    function get_list_regulation() {
        $sql = "SELECT * FROM regulation ORDER BY judul ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail regulation
    function get_data_by_id($params) {
        $sql = "SELECT * FROM regulation WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert
    function insert($params) {
        $sql = "INSERT INTO regulation (judul, deskripsi, download, mdb, mdd)
                VALUES (?, ?, 0, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // update
    function update($params) {
        $sql = "UPDATE regulation SET judul = ?, deskripsi = ?, download = ?, mdb = ?, mdd = NOW()
                WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // update file
    function update_file($params) {
        $sql = "UPDATE regulation SET file_name = ? WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete
    function delete($params) {
        $sql = "DELETE FROM regulation WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

}
