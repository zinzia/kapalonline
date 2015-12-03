<?php

class m_disclaimer extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get list disclaimer
    function get_list_disclaimer() {
        $sql = "SELECT * 
            FROM disclaimer 
            WHERE disclaimer_st = '1'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
