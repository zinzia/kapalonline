<?php

class m_contact extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // send
    function send($params) {
        $sql = "INSERT INTO contact (contact_name, contact_from, contact_message, contact_email, post_date)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // get list contact
    function get_list_contact() {
        $sql = "SELECT * FROM contact ORDER BY post_date ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // delete
    function delete($params) {
        $sql = "DELETE FROM contact WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

}
