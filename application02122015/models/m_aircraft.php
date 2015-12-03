<?php

class m_aircraft extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get total aircraft
    function get_total_aircraft($params) {
        $sql = "SELECT count(*)'total' FROM aircraft
                WHERE aircraft_manufacture LIKE ? AND aircraft_model LIKE ?";
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
    function get_all_aircraft($params) {
        $sql = "SELECT * FROM aircraft
                WHERE aircraft_manufacture LIKE ? AND aircraft_model LIKE ?
                ORDER BY aircraft_manufacture ASC, aircraft_model ASC
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

    // get list data aircraft
    function get_list_aircraft() {
        $sql = "SELECT * FROM aircraft ORDER BY aircraft_model ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

// get all data airport by id
    function get_aircraft_by_id($params) {
        $sql = "SELECT * FROM aircraft 
                WHERE aircraft_id = ?";
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
        $sql = "INSERT INTO aircraft (aircraft_manufacture, aircraft_model, aircraft_product_year, aircraft_std_capacity, aircraft_desc, mdb, mdd) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

// update
    function update($params) {
        $sql = "UPDATE aircraft set aircraft_manufacture = ?, aircraft_model = ?,
               aircraft_product_year = ?, aircraft_std_capacity = ?, aircraft_desc = ?, mdd = NOW(), mdb = ? 
               WHERE aircraft_id = ?";
        return $this->db->query($sql, $params);
    }

// delete
    function delete($params) {
        $sql = "DELETE FROM aircraft WHERE aircraft_id = ? ";
        return $this->db->query($sql, $params);
    }

    function is_exist_aircraft($params) {
        $sql = "SELECT * FROM aircraft WHERE aircraft_model = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            // return
            return true;
        } else {
            return false;
        }
    }

}
