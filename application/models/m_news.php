<?php

class m_news extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // get list news
    function get_list_news() {
        $sql = "SELECT a.*, b.operator_name 
                FROM news a
                LEFT JOIN com_user b ON a.news_post_by = b.user_id
                ORDER BY news_post_date DESC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list news content
    function get_list_lang_content($params) {
        $sql = "SELECT a.*, news_lang_title, news_lang_content
                FROM languages a
                LEFT JOIN (SELECT * FROM news_lang WHERE news_id = ?) b ON a.lang_id = b.lang_id
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

    // get detail news
    function get_detail_news_by_id($params) {
        $sql = "SELECT * FROM news WHERE news_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail news content
    function get_detail_lang_content($params) {
        $sql = "SELECT *
                FROM languages a
                LEFT JOIN (SELECT * FROM news_lang WHERE news_id = ?) b ON a.lang_id = b.lang_id
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

    // delete
    function delete($params) {
        $sql = "DELETE FROM news WHERE news_id = ?";
        return $this->db->query($sql, $params);
    }

    // unsert
    function insert($params) {
        $sql = "INSERT INTO news (news_title, news_post_by, news_post_date) VALUES (?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // insert_news_lang
    function insert_news_lang($params) {
        $sql = "INSERT INTO news_lang (news_id, lang_id, news_lang_title, news_lang_content)
                VALUES (?, ?, ?, ?)";
        return $this->db->query($sql, $params);
    }

    // update
    function update($params) {
        $sql = "UPDATE news SET news_title = ?, news_post_by = ? WHERE news_id = ?";
        return $this->db->query($sql, $params);
    }

    // update content
    function update_content($params) {
        // detail
        $result = $this->get_detail_lang_content($params);
        if (empty($result['news_id'])) {
            // insert
            $sql = "INSERT INTO news_lang (news_id, lang_id, news_lang_title, news_lang_content)
                VALUES (?, ?, ?, ?)";
            return $this->db->query($sql, $params);
        } else {
            $params = array($params[2], $params[3], $params[0], $params[1]);
            // update
            $sql = "UPDATE news_lang SET news_lang_title = ?, news_lang_content = ? WHERE news_id = ? AND lang_id = ?";
            return $this->db->query($sql, $params);
        }
    }

    // update images
    function update_news_img($params) {
        $sql = "UPDATE news_lang SET news_lang_img = ? WHERE news_id = ? AND lang_id = ?";
        return $this->db->query($sql, $params);
    }

    /*
     * PUBLIC
     */

    // latest news
    function get_latest_news($params) {
        $sql = "SELECT *, a.news_id
                FROM news a
                INNER JOIN news_lang b ON a.news_id = b.news_id
                WHERE lang_id = ?
                ORDER BY news_post_date DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail news
    function get_detail_news_published_by_id($params) {
        $sql = "SELECT * FROM news a
                INNER JOIN news_lang b ON a.news_id = b.news_id
                WHERE b.news_id = ? AND lang_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list news
    function get_list_news_published($params) {
        $sql = "SELECT *
                FROM news a
                INNER JOIN news_lang b ON a.news_id = b.news_id
                WHERE lang_id = ? AND a.news_id <> ?
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
