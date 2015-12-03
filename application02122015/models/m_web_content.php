<?php

class m_web_content extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // get list web_content
    function get_list_web_content() {
        $sql = "SELECT a.*, b.operator_name 
                FROM web_content a
                LEFT JOIN com_user b ON a.mdb = b.user_id
                ORDER BY content_title ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list web_content content
    function get_list_lang_content($params) {
        $sql = "SELECT a.*, data_id, content_value
                FROM languages a
                LEFT JOIN (SELECT * FROM web_content_lang WHERE data_id = ?) b ON a.lang_id = b.lang_id
                ORDER BY lang_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail web_content
    function get_detail_web_content_by_id($params) {
        $sql = "SELECT * FROM web_content WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail web_content content
    function get_detail_lang_content($params) {
        $sql = "SELECT *
                FROM languages a
                LEFT JOIN (SELECT * FROM web_content_lang WHERE data_id = ?) b ON a.lang_id = b.lang_id
                WHERE a.lang_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get default lang
    function get_default_lang() {
        $sql = "SELECT lang_id FROM languages WHERE lang_default = 1 LIMIT 0, 1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['lang_id'];
        } else {
            return 1;
        }
    }

    // update content
    function update_content($params) {
        // detail
        $result = $this->get_detail_lang_content($params);
        if (empty($result['data_id'])) {
            // insert
            $sql = "INSERT INTO web_content_lang (data_id, lang_id, content_value)
                VALUES (?, ?, ?)";
            return $this->db->query($sql, $params);
        } else {
            $params = array($params[2], $params[0], $params[1]);
            // update
            $sql = "UPDATE web_content_lang SET content_value = ? WHERE data_id = ? AND lang_id = ?";
            return $this->db->query($sql, $params);
        }
    }

}
