<?php

class m_test extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // load encrypt
        $this->load->library('encrypt');
    }

    function delete() {
        $sql = "DELETE FROM test";
        return $this->db->query($sql);
    }

    function delete2() {
        $sql = "DELETE FROM test2";
        return $this->db->query($sql);
    }

    function delete3() {
        $sql = "DELETE FROM test3";
        return $this->db->query($sql);
    }

    function delete4() {
        $sql = "DELETE FROM airport";
        return $this->db->query($sql);
    }

    function insert($params) {
        return $this->db->insert('test', $params);
    }

    function insert2($params) {
        return $this->db->insert('test2', $params);
    }

    function insert3($params) {
        return $this->db->insert('test3', $params);
    }

    function insert4($params) {
        return $this->db->insert('airport', $params);
    }

    function get_all_user_aol() {
        $sql = "SELECT *
            FROM test";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return 0;
        }
    }

    function update_pass($params) {
        $sql = "UPDATE test SET password = ?, password_key = ? WHERE id = ?";
        return $this->db->query($sql, $params);
    }

    function get_all_airlines_aol() {
        $sql = "SELECT *
            FROM test2";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return 0;
        }
    }

    function update_process($params) {
        $sql = "UPDATE test SET id_perusahaan = ? WHERE id_perusahaan = ?";
        return $this->db->query($sql, $params);
    }

}
