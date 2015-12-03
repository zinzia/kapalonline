<?php

class m_dashboard extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get detail task
    function get_detail_my_task_by_role_berjadwal($params) {
        $sql = "SELECT * FROM fa_flow WHERE role_id = ? AND group_id IN (1, 2) LIMIT 0, 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail task
    function get_detail_my_task_by_role_tidak_berjadwal($params) {
        $sql = "SELECT * FROM fa_flow WHERE role_id = ? AND group_id IN (3, 4, 5, 6) LIMIT 0, 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list task
    function get_list_all_task_waiting() {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_nm,
                    DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN fa_process b ON a.data_id = b.data_id 
                    INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                    INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                    WHERE a.data_st = 'waiting'
                    GROUP BY a.data_id
                ) result
                ORDER BY mdd ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task
    function get_list_my_task_waiting_berjadwal($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_nm, c.task_link, h.services_nm,
                    DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN fa_process b ON a.data_id = b.data_id 
                    INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                    INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                    INNER JOIN com_role_user e ON e.role_id = c.role_id
                    INNER JOIN com_user f ON f.user_id = e.user_id
                    INNER JOIN com_user_airlines g ON g.user_id = f.user_id AND g.airlines_id = a.airlines_id 
                    INNER JOIN services_code h ON h.services_cd = a.services_cd 
                    WHERE a.data_st = 'waiting' AND c.role_id = ? AND c.group_id IN (1, 2) AND b.action_st = 'process' AND g.user_id = ? 
                    GROUP BY registration_code
                ) result
                ORDER BY date_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task
    function get_list_my_task_waiting_tidak_berjadwal($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_nm, c.task_link,
                    DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, h.services_nm,
                    TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN fa_process b ON a.data_id = b.data_id 
                    INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                    INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                    INNER JOIN com_role_user e ON e.role_id = c.role_id
                    INNER JOIN com_user f ON f.user_id = e.user_id
                    INNER JOIN com_user_airlines g ON g.user_id = f.user_id AND g.airlines_id = a.airlines_id
                    INNER JOIN services_code h ON h.services_cd = a.services_cd 
                    WHERE a.data_st = 'waiting' AND c.role_id = ? AND c.group_id IN (3, 4, 5, 6) AND b.action_st = 'process' AND g.user_id = ? 
                    GROUP BY registration_code
                ) result
                ORDER BY date_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // chart
    function get_data_chart_fa($params) {
        $sql = "SELECT MONTH(a.mdd)'bulan', COUNT(data_id)'total'
                FROM fa_data a
                WHERE a.data_completed = '1' AND data_st <> 'open' AND data_type = ?
                AND data_id NOT IN('00000001', '00000002', '00000003', '00000004')
                AND YEAR(a.mdd) = YEAR(CURRENT_DATE)
                GROUP BY YEAR(a.mdd), MONTH(a.mdd)";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // chart
    function get_data_chart_pie() {
        $sql = "SELECT data_st, COUNT(*)'total' 
                FROM fa_data WHERE data_st <> 'open'
                AND YEAR(mdd) = YEAR(CURRENT_DATE)
                GROUP BY data_st";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // statistik
    function get_data_statistik_pelayanan() {
        $sql = "SELECT data_st, data_type, data_flight, COUNT(*)'total' 
                FROM fa_data 
                WHERE data_st <> 'open'
                AND YEAR(mdd) = YEAR(CURRENT_DATE)
                GROUP BY data_st, data_type, data_flight";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // statistik
    function get_data_statistik_pelayanan_belum_proses() {
        $sql = "SELECT data_st, data_type, data_flight, COUNT(*)'total' 
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id
                WHERE data_st = 'waiting' AND b.action_st = 'process' 
                AND b.flow_id IN(11, 21, 31, 41, 51, 61)
                AND YEAR(a.mdd) = YEAR(CURRENT_DATE)
                GROUP BY data_st, data_type, data_flight";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /* =================================== IZIN RUTE =================================== */

    // get detail task
    function get_detail_my_task_rute($params) {
        $sql = "SELECT c.*, group_nm, airlines_nm, task_link, d.group_alias,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                INNER JOIN com_role_user f ON f.role_id = b.role_id
                INNER JOIN com_user g ON g.user_id = f.user_id
                INNER JOIN com_user_airlines h ON h.user_id = g.user_id AND h.airlines_id = c.airlines_id
                WHERE b.role_id = ? AND g.user_id = ? AND action_st = 'process' 
                AND c.izin_completed = '0' AND c.izin_request_st = '1' AND c.izin_flight = ? 
                GROUP BY c.registrasi_id 
                ORDER BY selisih_hari DESC, selisih_waktu DESC";
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
    function get_detail_my_task_rute_internasional($params) {
        $sql = "SELECT c.*, group_nm, airlines_nm, task_link, d.group_alias,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                INNER JOIN com_role_user f ON f.role_id = b.role_id
                INNER JOIN com_user g ON g.user_id = f.user_id
                INNER JOIN com_user_airlines h ON h.user_id = g.user_id AND h.airlines_id = c.airlines_id
                WHERE b.role_id = ? AND g.user_id = ? AND action_st = 'process' 
                AND c.izin_completed = '0' AND c.izin_request_st = '1' AND c.izin_flight = 'internasional' 
                GROUP BY c.registrasi_id 
                ORDER BY selisih_hari DESC, selisih_waktu DESC";
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
