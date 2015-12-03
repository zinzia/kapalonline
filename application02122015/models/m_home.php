<?php

class m_home extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // regulation
    function get_list_regulation($params) {
        $sql = "SELECT * FROM regulation
                ORDER BY judul ASC
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

    // detail regulation
    function get_detail_regulation_by_id($params) {
        $sql = "SELECT * FROM regulation WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // download
    function update_regulation_download($params) {
        $sql = "UPDATE regulation SET download = download + 1 WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // news
    function get_list_news($params) {
        $sql = "SELECT *, SUBSTR(news_lang_content, 1, 100)'intro' 
                FROM news a
                INNER JOIN news_lang b ON a.news_id = b.news_id
                WHERE lang_id = ?
                ORDER BY news_post_date DESC
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

}
