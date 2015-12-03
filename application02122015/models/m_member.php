<?php

class m_member extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get list tahun
    function get_list_tahun_report($params) {
        $sql = "SELECT DISTINCT tahun FROM
                (
                        SELECT YEAR(published_date)'tahun'
                        FROM fa_data WHERE airlines_id = ? AND data_st = 'approved'
                        UNION ALL
                        SELECT YEAR(CURRENT_DATE)'tahun'
                ) rs
                ORDER BY tahun ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail
    function get_member_detail_by_email($params) {
        $sql = "SELECT * FROM com_user WHERE user_mail = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            $password_decode = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            $result['user_pass'] = $password_decode;
            return $result;
        } else {
            return array();
        }
    }

    // list waiting task
    function get_list_all_task_waiting($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_nm,
                    DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu, e.group_nm 
                    FROM fa_data a
                    INNER JOIN fa_process b ON a.data_id = b.data_id 
                    INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                    INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                    INNER JOIN fa_group e ON e.group_id = c.group_id 
                    WHERE a.data_st = 'waiting' AND a.airlines_id = ?
                    GROUP BY a.registration_code
                ) result
                ORDER BY mdd ASC";
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
    function get_list_my_task_waiting($params) {
        $sql = "SELECT a.*, airlines_nm, c.task_link,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                WHERE a.data_st = 'waiting' AND a.airlines_id = ? AND c.group_id = ? 
                AND b.action_st = 'process'
                ORDER BY a.mdd ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task berjadwal
    function get_list_my_task_waiting_berjadwal($params) {
        $sql = "SELECT a.*, airlines_nm, c.task_link, e.services_nm,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                INNER JOIN services_code e ON e.services_cd = a.services_cd 
                WHERE a.data_st = 'waiting' AND a.airlines_id = ? AND c.group_id IN(1, 2)
                AND b.action_st = 'process'
                ORDER BY a.date_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task tidak berjadwal
    function get_list_my_task_waiting_tidak_berjadwal($params) {
        $sql = "SELECT a.*, airlines_nm, c.task_link, e.services_nm,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id 
                INNER JOIN services_code e ON e.services_cd = a.services_cd 
                WHERE a.data_st = 'waiting' AND a.airlines_id = ? AND c.group_id IN(3, 4, 5, 6)
                AND b.action_st = 'process'
                GROUP BY a.registration_code
                ORDER BY a.date_start ASC";
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
                    INNER JOIN services_code e ON a.services_cd = e.services_cd
                    WHERE a.data_st = 'waiting' 
                    AND document_no LIKE ? AND data_type LIKE ? AND data_flight LIKE ? 
                    AND a.airlines_id = ?
                    GROUP BY a.registration_code
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
                    SELECT a.*, airlines_brand, airlines_nm, d.task_nm, group_nm, group_link, role_nm, services_nm,
                    DATEDIFF(CURDATE(), c.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(c.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    INNER JOIN fa_process c ON a.data_id = c.data_id
                    INNER JOIN fa_flow d ON c.flow_id = d.flow_id
                    INNER JOIN fa_group e ON d.group_id = e.group_id
                    INNER JOIN com_role f ON d.role_id = f.role_id
                    INNER JOIN services_code g ON a.services_cd = g.services_cd
                    WHERE a.data_st = 'waiting' AND c.process_st = 'waiting' 
                    AND document_no LIKE ? AND data_type LIKE ? AND data_flight LIKE ?
                    AND a.airlines_id = ?
                    ORDER BY c.mdd ASC
                ) result
                GROUP BY registration_code
                ORDER BY mdd ASC
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

    // get detail task
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, operator_name, jabatan, services_nm
                FROM fa_data a
                INNER JOIN airlines b On a.airlines_id = b.airlines_id
                INNER JOIN services_code c ON a.services_cd = c.services_cd
                LEFT JOIN com_user u ON a.mdb_finish = u.user_id
                WHERE data_id = ? AND a.airlines_id = ?";
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
    function get_total_finished_task($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'approved'
                AND airlines_id = ?
                AND (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type LIKE ?
                AND data_flight LIKE ?
                AND payment_st LIKE ?";
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
    function get_list_finished_task($params) {
        $sql = "SELECT a.*, services_nm
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'approved'
                AND airlines_id = ?
                AND (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type LIKE ?
                AND data_flight LIKE ?
                AND payment_st LIKE ?
                ORDER BY published_date DESC
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

    // get detail rute data
    function get_data_rute_by_id($params) {
        $sql = "SELECT a.*, b.airport_iata_cd 
            FROM fa_data_rute a 
            LEFT JOIN airport b ON b.airport_id = a.airport_id
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total task report
    function get_total_finished_task_report($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM  fa_data a 
                LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
                WHERE a.data_st = 'approved'
                AND document_no = ?
                AND a.airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list task report
    function get_list_finished_task_report($params) {
        $sql = "SELECT a.*, b.services_nm
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd 
                WHERE data_st = 'approved' 
                AND document_no = ?
                AND airlines_id = ?
                ORDER BY a.published_date ASC
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

    // get list task
    function get_list_finished_task_print($params) {
        $sql = "SELECT a.*, services_nm
                FROM  fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'approved'
                AND airlines_id = ?
                AND (date_start BETWEEN ? AND ? OR date_end BETWEEN ? AND ?)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type LIKE ?
                AND data_flight LIKE ?
                AND payment_st LIKE ?
                ORDER BY published_date DESC";
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
        $sql = "SELECT * FROM fa_files WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task
    function get_list_pending_task_waiting($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, b.process_st, b.catatan'notes', airlines_nm, c.task_link, services_nm, e.group_id, group_link,
                    DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN fa_process b ON a.data_id = b.data_id 
                    INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                    INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                    INNER JOIN fa_group e ON c.group_id = e.group_id
                    LEFT JOIN services_code s ON a.services_cd = s.services_cd
                    WHERE a.data_st = 'waiting' AND c.role_id = ? AND b.action_st = 'process' AND a.airlines_id = ?
                    GROUP BY registration_code
                ) result
                ORDER BY mdd ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail data
    function get_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, process_id, services_nm, operator_name, b.catatan'notes', 
                c.group_id, group_link
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                INNER JOIN services_code sc ON a.services_cd = sc.services_cd
                INNER JOIN fa_group e ON c.group_id = e.group_id
                LEFT JOIN com_user u ON a.mdb = u.user_id
                WHERE a.data_st = 'waiting' AND a.data_id = ? AND c.flow_id IN (91, 92, 93, 94, 95, 96)
                AND a.airlines_id = ? AND b.action_st = 'process'
                ORDER BY a.mdd ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
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

    // get remark field
    function get_remark_field($params) {
        $sql = "SELECT b.* 
            FROM fa_rules_field a 
            LEFT JOIN fa_rules b ON b.rules_id = a.rules_id
            WHERE data_type = ? AND data_flight = ? AND services_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get airlines type
    function get_airlines_type($params) {
        $sql = "SELECT * 
            FROM com_airlines_type 
            WHERE airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get last fa payment
    function get_last_fa_payment($params) {
        $sql = "SELECT data_id, data_st, data_type, data_flight, document_no, published_no, airlines_id, flight_no, rute_all, date_start, date_end, payment_due_date, DATEDIFF(payment_due_date, CURDATE())selisih_hari 
            FROM fa_data 
            WHERE airlines_id = ? AND data_st = 'approved' AND payment_st != '22' AND payment_st != '11' 
            ORDER BY payment_due_date ASC 
            LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
