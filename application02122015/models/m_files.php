<?php

class m_files extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get file id
    function get_file_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get list files references
    function get_list_file_required($params) {
        $sql = "SELECT a.* FROM fa_file_reference a
                INNER JOIN fa_rules_files b ON a.ref_id = b.ref_id
                WHERE b.data_type = ? AND b.data_flight = ? AND b.services_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list files uploaded
    function get_list_file_uploaded($params) {
        $sql = "SELECT * FROM fa_files  WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list files download
    function get_list_file_download($params) {
        $sql = "SELECT a.file_id, b.ref_name 
                FROM fa_files a
                INNER JOIN fa_file_reference b ON a.ref_id = b.ref_id
                WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update files
    function update_files($delete, $insert) {
        // delete
        $sql = "DELETE FROM fa_files  WHERE data_id = ? AND ref_id = ?";
        $this->db->query($sql, $delete);
        // insert
        $sql = "INSERT INTO fa_files (file_id, data_id, file_path, file_name, ref_id, mdd)
                VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $insert);
    }

    // get detail files  by id
    function get_detail_files_by_id($params) {
        $sql = "SELECT * FROM fa_files WHERE data_id = ? AND ref_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail files  by id
    function get_detail_files_download_by_id($params) {
        $sql = "SELECT * FROM fa_files WHERE file_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list files completed
    function is_file_completed($params) {
        $sql = "SELECT data_id
                FROM fa_rules_files a
                LEFT JOIN fa_files b ON a.ref_id = b.ref_id AND data_id = ?
                WHERE a.data_type = ? AND a.data_flight = ? 
                AND a.services_cd = ? AND data_id IS NULL AND a.ref_id != '10' AND a.ref_id != '4' AND a.ref_id != '5'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    // check is_file_completed_by_id
    function is_file_completed_by_id($params) {
        $sql = "SELECT data_id
                FROM fa_files
                WHERE ref_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
