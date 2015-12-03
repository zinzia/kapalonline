<?php

class m_service_code extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get all service code
    function get_all_service_code() {
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

}
