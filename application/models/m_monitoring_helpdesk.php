<?php

class m_monitoring_helpdesk extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get list role
    function get_list_role($params) {
        $sql = "SELECT * FROM com_role WHERE portal_id = 6 AND role_id <> 41 ORDER BY role_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail task
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, u.operator_name, c.services_nm, d.operator_name'operator_nm', d.jabatan
                FROM fa_data a
                INNER JOIN airlines b On a.airlines_id = b.airlines_id
                LEFT JOIN com_user u ON a.mdb = u.user_id 
                LEFT JOIN services_code c ON c.services_cd = a.services_cd
                LEFT JOIN com_user d ON d.user_id = a.mdb_finish 
                WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list task process
    function get_list_process_by_id($params) {
        $sql = "SELECT a.*, role_nm, operator_name, task_nm
                FROM fa_process a
                INNER JOIN fa_flow b ON a.flow_id = b.flow_id
                INNER JOIN com_role c ON b.role_id = c.role_id
                LEFT JOIN com_user u ON a.mdb_finish = u.user_id
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

    // get total task
    function get_total_awaiting_task($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM 
                (
                    SELECT a.data_id
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    INNER JOIN fa_process c ON a.data_id = c.data_id
                    INNER JOIN fa_flow d ON c.flow_id = d.flow_id
                    WHERE ( airlines_brand LIKE ? OR airlines_nm LIKE ?) 
                    AND document_no LIKE ? AND (SUBSTR(a.mdd, 1, 10) BETWEEN ? AND ?) AND ((date_start BETWEEN ? AND ?) OR (date_end BETWEEN ? AND ?)) 
                    AND services_cd LIKE ? AND data_type LIKE ? AND data_flight LIKE ? AND data_st LIKE ?
                    GROUP BY a.data_id
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list task
    function get_list_awaiting_task($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_brand, airlines_nm, d.task_nm, group_nm, group_link, role_nm,
                    DATEDIFF(CURDATE(), c.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(c.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    INNER JOIN fa_process c ON a.data_id = c.data_id
                    INNER JOIN fa_flow d ON c.flow_id = d.flow_id
                    INNER JOIN fa_group e ON d.group_id = e.group_id
                    INNER JOIN com_role f ON d.role_id = f.role_id
                    WHERE ( airlines_brand LIKE ? OR airlines_nm LIKE ?) 
                    AND document_no LIKE ? AND (SUBSTR(a.mdd, 1, 10) BETWEEN ? AND ?) AND ((date_start BETWEEN ? AND ?) OR (date_end BETWEEN ? AND ?)) 
                    AND services_cd LIKE ? AND data_type LIKE ? AND data_flight LIKE ? AND data_st LIKE ?
                    ORDER BY c.mdd DESC
                ) result
                GROUP BY data_id
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

    // get list airlines
    function get_list_airlines() {
        $sql = "SELECT * 
            FROM airlines
            ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get services cd
    function get_services_cd($params) {
        $sql = "SELECT a.*, b.services_cd, b.services_nm 
            FROM fa_rules_services a 
            LEFT JOIN services_code b ON b.services_cd = a.services_cd 
            WHERE a.data_type = ? AND data_flight = ?";
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
