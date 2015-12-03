<?php

class m_faq extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // get list faq category
    function get_list_faq_category() {
        $sql = "SELECT * 
                FROM faq_category 
                ORDER BY category_seq ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list faq question
    function get_list_faq_question() {
        $sql = "SELECT * 
                FROM faq";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail faq category
    function get_detail_faq_category_by_id($params) {
        $sql = "SELECT * FROM faq_category WHERE category_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail question by category
    function get_detail_question_by_category($params) {
        $sql = "SELECT * FROM faq WHERE category_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail faq
    function get_detail_faq_by_id($params) {
        $sql = "SELECT * FROM faq WHERE faq_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list faq category
    function get_list_faq_information_category($params) {
        $sql = "SELECT a.* 
                FROM faq_category a 
                LEFT JOIN faq b ON b.category_id = a.category_id 
                WHERE b.faq_title LIKE ? OR b.faq_question LIKE ? OR b.faq_answer LIKE ? 
                GROUP BY a.category_id 
                ORDER BY category_seq ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list faq question
    function get_list_faq_information_question($params) {
        $sql = "SELECT * 
                FROM faq 
                WHERE faq_title LIKE ? OR faq_question LIKE ? OR faq_answer LIKE ? ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail faq information
    function get_detail_faq_information_by_id($params) {
        $sql = "SELECT a.*, b.category_nm 
            FROM faq a 
            LEFT JOIN faq_category b ON b.category_id = a.category_id 
            WHERE a.faq_id = ?";
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
        return $this->db->insert('faq_category', $params);
    }

    // insert question
    function insert_question($params) {
        return $this->db->insert('faq', $params);
    }

    // update
    function update($params, $where) {
        return $this->db->update('faq_category', $params, $where);
    }

    // update question
    function update_question($params, $where) {
        return $this->db->update('faq', $params, $where);
    }

    // delete
    function delete($params) {
        return $this->db->delete('faq_category', $params);
    }

    // delete question
    function delete_question($params) {
        return $this->db->delete('faq', $params);
    }

}
