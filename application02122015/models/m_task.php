<?php

class m_task extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get process id
    function get_process_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get detail task by id
    function get_detail_task_by_id($params) {
        $sql = "SELECT a.flow_id, task_nm, role_nm 
                FROM fa_flow a
                INNER JOIN com_role b On a.role_id = b.role_id
                WHERE flow_id = ?";
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
    function get_list_my_task_waiting($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, b.process_st, b.catatan'notes', airlines_nm, c.task_link,
                    DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                    TIMEDIFF(CURTIME(), SUBSTR(b.mdd, 12, 8)) AS selisih_waktu
                    FROM fa_data a
                    INNER JOIN fa_process b ON a.data_id = b.data_id 
                    INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                    INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                    INNER JOIN com_role_user e ON e.role_id = c.role_id
                    INNER JOIN com_user f ON f.user_id = e.user_id
                    INNER JOIN com_user_airlines g ON g.user_id = f.user_id AND g.airlines_id = a.airlines_id
                    WHERE a.data_st = 'waiting' AND c.role_id = ? AND b.action_st = 'process' AND g.user_id = ? 
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

    // get detail task by id
    function get_detail_my_task_by_id($params) {
        $sql = "SELECT * FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN fa_group d ON c.group_id = d.group_id
                WHERE a.data_id = ? AND c.role_id = ? AND b.action_st = 'process'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail data
    function get_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, process_id, services_nm, u.operator_name, b.catatan'notes', IF(ISNULL(a.remark_final), a.catatan, a.remark_final)'remark_final', v.operator_name'user_remark'
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                INNER JOIN services_code sc ON a.services_cd = sc.services_cd
                LEFT JOIN com_user u ON a.mdb = u.user_id
                LEFT JOIN com_user v ON v.user_id = a.remark_by
                WHERE a.data_st = 'waiting' AND a.data_id = ? AND c.flow_id = ? AND b.action_st = 'process'
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

    // edit process
    function update($params, $where) {
        $this->db->where($where);
        return $this->db->update('fa_data', $params);
    }

    // edit process
    function update_process($params, $where) {
        $this->db->where($where);
        return $this->db->update('fa_process', $params);
    }

    // update process
    function action_update($params) {
        $sql = "UPDATE fa_process SET process_st = ?, action_st = ? , mdb_finish = ?, mdd_finish = NOW()          
                WHERE process_id = ?";
        return $this->db->query($sql, $params);
    }

    // done process
    function done_process($params) {
        $sql = "UPDATE fa_data SET data_st = ?WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // done process all
    function done_process_all($params) {
        $sql = "UPDATE fa_data SET data_st = ?, published_no = ?, payment_st = ?, published_date = NOW(), payment_due_date = DATE_ADD(NOW(), INTERVAL 7 DAY), mdb_finish = ? WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // add process
    function insert_process($params) {
        $sql = "INSERT INTO fa_process (process_id, data_id, flow_id, mdb, mdd)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
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

    // get fa group
    function get_fa_group($params) {
        $sql = "SELECT * FROM fa_group WHERE group_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['group_link'];
        } else {
            return array();
        }
    }

    // generate nomor dokumen terbit
    function get_nomor_dokumen_terbit($params, $trim) {
        $sql = "SELECT LEFT(published_no, " . $trim . ")'last_number' 
                FROM fa_data a 
                WHERE a.data_type = ? AND a.data_flight = ? AND RIGHT(a.published_no, 4) = ? AND a.published_no LIKE ? AND !ISNULL(a.published_no) 
                ORDER BY LEFT(published_no, " . $trim . ") DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < $trim; $i++) {
                $zero .= '0';
            }
            return $zero . $number;
        } else {
            $zero = '';
            for ($i = 1; $i < $trim; $i++) {
                $zero .= '0';
            }
            return $zero . '1';
        }
    }

    // get all regis
    function get_all_regis($params) {
        $sql = "SELECT * FROM fa_data WHERE registration_code = ?";
        $query = $this->db->query($sql, $params);
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

    // // get verifier
    // function get_verifier($params) {
    //     $sql = "SELECT * FROM fa_rules_registration WHERE data_type = ? AND data_flight = ? AND services_cd = ?";
    //     $query = $this->db->query($sql, $params);
    //     if ($query->num_rows() > 0) {
    //         $result = $query->row_array();
    //         $query->free_result();
    //         return $result;
    //     } else {
    //         return array();
    //     }
    // }

    // get role next flow
    function get_role_next_from($params) {
        $sql = "SELECT role_id 
            FROM fa_flow 
            WHERE flow_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail da data
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, operator_name, services_nm
                FROM fa_data a
                INNER JOIN airlines b On a.airlines_id = b.airlines_id
                INNER JOIN services_code c ON a.services_cd = c.services_cd
                LEFT JOIN com_user u ON a.mdb = u.user_id
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

    // action control
    function get_action_control($params) {
        $sql = "SELECT action_reject, action_revisi, action_send, action_rollback, action_publish 
                FROM com_role_action
                WHERE role_id = ? AND data_type = ? AND data_flight = ? AND services_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // GET SERVICES BERBAYAR
    function get_payment_services($params) {
        $sql = "SELECT * 
            FROM fa_rules_services 
            WHERE data_type = ? AND data_flight = ? AND services_cd = ?";
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
