<?php

class m_airport extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get total data airport
    function get_total_airport($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM airport
                WHERE airport_nm LIKE ? AND airport_st LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get all data airport
    function get_all_airport($params) {
        $sql = "SELECT *
                FROM airport
                WHERE airport_nm LIKE ? AND airport_st LIKE ?
                ORDER BY airport_nm ASC
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

    // get list data airport
    function get_list_airport() {
        $sql = "SELECT * FROM airport ORDER BY airport_nm ASC";
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

    // insert
    function insert($params) {
        $sql = "INSERT INTO airport (airport_nm, airport_iata_cd, airport_icao_cd, airport_st, airport_region, airport_country, airport_owner, mdb, mdd) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // update
    function update($params) {
        $sql = "UPDATE airport SET airport_nm = ?, airport_iata_cd = ?, airport_icao_cd = ?, airport_st = ?, 
                airport_region = ?, airport_country = ?, airport_owner = ?, mdb = ?, mdd = NOW()
                WHERE airport_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete
    function delete($params) {
        $sql = "DELETE FROM airport WHERE airport_id = ? ";
        return $this->db->query($sql, $params);
    }

    // get all data airport by iata code
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 7-May-2015
    // reason: to accommodate sync process with SCORE slot time database
    function get_airport_by_code($params) {
        $sql = "SELECT * FROM airport
                WHERE airport_iata_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all data airport by iata code
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 14-May-2015
    // reason: to accommodate sync process with SCORE slot time database
    function get_airport_score_by_code($params) {
        $sql = "SELECT IF(GROUP_CONCAT(DISTINCT is_used_score)='1',1,0) AS is_all_used_score FROM airport WHERE FIND_IN_SET(airport_iata_cd,?)>0";
        // list($orig,$dest) = explode(",",$params[0]);
        // $params = array($orig);        
        // $sql = "SELECT IF(is_used_score='1',1,0) AS is_all_used_score FROM airport WHERE airport_iata_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all data airport by iata code
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 10-May-2015
    // reason: to accommodate sync process with SCORE slot time database
    function is_airport_using_score_by_code($params) {
        $sql = "SELECT IF(is_used_score='1',1,0) AS is_used_score FROM airport WHERE airport_iata_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return (@intval($result['is_used_score']) === 1);
        } else {
            return false;
        }
    }

    /*
     * WELLY CHOW
     */

    // validate airport
    function is_airport_using_score_by_iata_cd($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM airport 
                WHERE airport_iata_cd = ? OR airport_iata_cd = ? AND is_used_score = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            if ($result['total'] <> 0) {
                return true;
            }
        }
        return false;
    }

    // get local time
    function get_local_time_airport_by_code($params) {
        $sql = "SELECT airport_utc_sign, airport_utc FROM airport WHERE airport_iata_cd = ?";
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
