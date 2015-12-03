<?php

class m_lang extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get default lang
    function get_default_lang() {
        $sql = "SELECT * FROM languages WHERE lang_default = 1 LIMIT 0, 1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list lang
    function get_list_lang() {
        $sql = "SELECT * FROM languages ORDER BY lang_name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail
    function get_lang_detail_by_id($params) {
        $sql = "SELECT * FROM languages WHERE lang_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail
    function get_web_content_by_lang_group($params) {
        $sql = "SELECT content_title, content_value
                FROM web_content a
                INNER JOIN web_content_lang b ON a.data_id = b.data_id
                WHERE lang_id = ? AND content_title LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            // parsing
            foreach ($result as $data) {
                $this->smarty->assign($data['content_title'], $data['content_value']);
            }
            // free result
            $query->free_result();
        }
    }

}
