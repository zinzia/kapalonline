<?php

class m_airlines extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // get total data airport
    function get_total_airlines($params) {
        $sql = "SELECT count(*)'total' FROM airlines
                WHERE airlines_nm LIKE ? AND airlines_brand LIKE ?   
                AND airlines_iata_cd LIKE ? AND airlines_icao_cd LIKE ?";
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
    function get_all_airlines($params) {
        $sql = "SELECT * FROM airlines
                WHERE airlines_nm LIKE ? AND airlines_brand LIKE ?   
                AND airlines_iata_cd LIKE ? AND airlines_icao_cd LIKE ?
                ORDER BY airlines_nm ASC
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
    
    // get all data airlines
    function get_all_airlines_nolimit($params) {
        $sql = "SELECT * FROM airlines
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

    // get all data airport by id
    function get_airport_by_id($params) {
        $sql = "SELECT * FROM airport
                WHERE airport_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all data airlines by id
    function get_airlines_by_id($params) {
        $sql = "SELECT * FROM airlines WHERE airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get airlines siup by id
    function get_airlines_siup_by_id($params) {
        $sql = "SELECT *, IF(airlines_type = 'berjadwal', '1', IF(airlines_type = 'tidak berjadwal', '2', '3'))idx FROM com_airlines_type WHERE airlines_id = ?";
        $query = $this->db->query($sql, $params);
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
        $sql = "INSERT INTO airlines (airlines_id, airlines_nm,airlines_brand, airlines_iata_cd, airlines_icao_cd,
                airlines_contact,airlines_website,airlines_address,airlines_st,airlines_type,
                airlines_flight_type, airlines_nationality, mdb, mdd) 
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // insert siup
    function insert_siup($params) {
        return $this->db->insert_batch('com_airlines_type', $params);
    }

    // update
    function update($params) {
        $sql = "UPDATE airlines set airlines_nm=?, airlines_brand = ?, airlines_iata_cd=?, airlines_icao_cd=?,
            airlines_address=?, airlines_contact=?, airlines_website = ?, airlines_st=?, airlines_type = ?,
            airlines_flight_type = ?, airlines_nationality = ?, mdd=?, mdd= NOW() WHERE airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    // update va
    function update_va($params, $where) {
        return $this->db->update("airlines", $params, $where);
    }

    // delete
    function delete($params) {
        $sql = "DELETE FROM airlines WHERE airlines_id = ? ";
        return $this->db->query($sql, $params);
    }

    // delete siup
    function delete_siup($params) {
        $sql = "DELETE FROM com_airlines_type WHERE airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    // cek airlines yang tersedia
    function is_available_airline($params) {
        $sql = "SELECT * FROM airlines WHERE airlines_nm LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }

    // get airport by name
    function get_airline_byname($params) {
        $sql = "SELECT * FROM airlines WHERE airlines_nm LIKE ?";
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
