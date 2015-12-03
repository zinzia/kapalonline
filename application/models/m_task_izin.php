<?php

class m_task_izin extends CI_Model {

    //put your code here
    public function __construct() {
        parent::__construct();
    }

    // list waiting
    function get_list_my_task_waiting($params) {
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
                AND c.izin_completed = '0' AND c.izin_request_st = '1' 
                GROUP BY c.registrasi_id 
                ORDER BY selisih_hari DESC, selisih_waktu DESC";
        $query = $this->db->query($sql, $params);
        //echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get published number domestik
    function get_published_number_dom($kode = "DRJU-DAU") {
        // -- AU.008/page/dossier/DRJU-DAU-TAHUN
        $sql = "SELECT SPLIT_STRING(izin_published_letter, '/', 2)'pages', SPLIT_STRING(izin_published_letter, '/', 3)'number'
                FROM izin_registrasi a 
                WHERE RIGHT(SPLIT_STRING(izin_published_letter, '/', 4), 4) = YEAR(CURRENT_DATE) AND SPLIT_STRING(izin_published_letter, '/', 1) = 'AU.012'
                ORDER BY ABS(SPLIT_STRING(izin_published_letter, '/', 2)) DESC, ABS(SPLIT_STRING(izin_published_letter, '/', 3)) DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $pages = intval($result['pages']);
            $number = intval($result['number']) + 1;
            if ($number > 25) {
                $number = 1;
                $pages++;
            }
            return 'AU.012/' . $pages . '/' . $number . '/' . $kode . '-' . date('Y');
        } else {
            return 'AU.012/1/1/' . $kode . '-' . date('Y');
        }
    }

    // get published number internasional
    function get_published_number_int($kode = "DRJU-DAU") {
        // -- AU.008/page/dossier/DRJU-DAU-TAHUN
        $sql = "SELECT SPLIT_STRING(izin_published_letter, '/', 2)'pages', SPLIT_STRING(izin_published_letter, '/', 3)'number'
                FROM izin_registrasi a 
                WHERE RIGHT(SPLIT_STRING(izin_published_letter, '/', 4), 4) = YEAR(CURRENT_DATE) AND SPLIT_STRING(izin_published_letter, '/', 1) = 'AU.013'
                ORDER BY ABS(SPLIT_STRING(izin_published_letter, '/', 2)) DESC, ABS(SPLIT_STRING(izin_published_letter, '/', 3)) DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $pages = intval($result['pages']);
            $number = intval($result['number']) + 1;
            if ($number > 25) {
                $number = 1;
                $pages++;
            }
            return 'AU.013/' . $pages . '/' . $number . '/' . $kode . '-' . date('Y');
        } else {
            return 'AU.013/1/1/' . $kode . '-' . date('Y');
        }
    }

    // get kode izin
    function get_kode_izin_domestik($params) {
        // D001-001
        $sql = "SELECT RIGHT(kode_izin, 3)'last_number', airlines_id, UPPER(SUBSTRING(izin_flight, 1, 1))'kode_flight'
                FROM izin_rute 
                WHERE airlines_id = ? AND kode_izin IS NOT NULL AND izin_flight = ?
                ORDER BY RIGHT(kode_izin, 3) DESC
                LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < 3; $i++) {
                $zero .= '0';
            }
            return $result['kode_flight'] . $params[0] . '-' . $zero . $number;
        } else {
            $zero = '';
            for ($i = 1; $i < 3; $i++) {
                $zero .= '0';
            }
            return strtoupper(substr($params[1], 0, 1)) . $params[0] . '-' . $zero . '1';
        }
    }

    // get kode izin
    function get_kode_izin_internasional($params) {
        // D001-001
        $sql = "SELECT RIGHT(kode_izin, 3)'last_number', airlines_id
                FROM izin_rute 
                WHERE airlines_id = ? AND kode_izin IS NOT NULL AND izin_flight = ?
                ORDER BY RIGHT(kode_izin, 3) DESC
                LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < 3; $i++) {
                $zero .= '0';
            }
            return 'I' . $params[0] . '-' . $zero . $number;
        } else {
            $zero = '';
            for ($i = 1; $i < 3; $i++) {
                $zero .= '0';
            }
            return 'I' . $params[0] . '-' . $zero . '1';
        }
    }

    // get kode frekuensi
    function get_kode_frekuensi($kode_izin) {
        // D001-001
        $sql = "SELECT RIGHT(kode_frekuensi, 3)'last_number'
                FROM izin_rute 
                WHERE kode_izin = ?
                ORDER BY RIGHT(kode_frekuensi, 3) DESC
                LIMIT 1";
        $query = $this->db->query($sql, $kode_izin);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < 3; $i++) {
                $zero .= '0';
            }
            return $kode_izin . '-' . $zero . $number;
        } else {
            $zero = '';
            for ($i = 1; $i < 3; $i++) {
                $zero .= '0';
            }
            return $kode_izin . '-' . $zero . '1';
        }
    }

    // get process id
    function get_process_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // detail task
    function get_detail_task_by_id($params) {
        $sql = "SELECT a.flow_id, task_nm, role_nm 
                FROM izin_flow a
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

    // get detail registrasi by id
    function get_detail_registrasi_data_by_id($params) {
        $sql = "SELECT a.*, b.*, d.group_alias, d.group_nm, 
                u.operator_name'pengirim', airlines_nm, airlines_iata_cd, airlines_nationality,f.operator_name AS 'izin_verified_by'
                FROM izin_registrasi a
                INNER JOIN izin_process b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_flow c ON b.flow_id = c.flow_id
                INNER JOIN izin_group d ON a.izin_group = d.group_id
                INNER JOIN airlines e ON a.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                LEFT JOIN com_user f ON a.izin_valid_by=f.user_id
                WHERE a.registrasi_id = ? AND c.role_id = ? AND b.action_st = 'process'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail izin rute
    function get_detail_data_by_izin_number($params) {
        $sql = "SELECT izin.*, rute_all, flight_no, etd, eta 
                FROM 
                (
                        SELECT a.izin_id, izin_number, izin_expired_date, aircraft_type, aircraft_capacity, dos, ron, pairing
                        FROM izin_rute a 
                        WHERE a.izin_completed = '1' AND a.izin_st = 'approved' AND a.izin_number = ? AND payment_st = '11'
                        ORDER BY izin_expired_date DESC
                        LIMIT 1
                ) izin
                INNER JOIN izin_data b ON izin.izin_id = b.izin_id";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail airport
    function get_airport_score_by_code($params) {
        $sql = "SELECT airport_iata_cd, is_used_score, airport_utc_sign, airport_utc FROM airport WHERE airport_iata_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by id
    function get_list_data_rute_by_id($params) {
        $sql = "SELECT a.*, 
            IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1)'doop1', IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1)'doop2', 
            IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1)'doop3', IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1)'doop4', 
            IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1)'doop5', IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1)'doop6', 
            IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)'doop7', 
            kode_izin, kode_frekuensi,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                izin_penundaan_start, izin_penundaan_end, izin_approval, notes
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.registrasi_id = ? AND b.izin_completed = '0'
                ORDER BY b.kode_frekuensi ASC, a.izin_id ASC, a.etd ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by kode izin
    function get_published_letter_by_kode_izin($params) {
        $sql = "SELECT a.kode_frekuensi, b.izin_published_letter, izin_published_date, a.notes 
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE a.izin_completed = '1' AND izin_active = '1' AND a.izin_approval = 'approved' 
                AND a.kode_izin = ?";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by id
    function get_list_data_rute_approved_by_id($params) {
        $sql = "SELECT a.*, kode_izin, kode_frekuensi,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                izin_penundaan_start, izin_penundaan_end, izin_approval
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.registrasi_id = ? AND b.izin_completed = '0' AND b.izin_approval = 'approved'";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get masa berlaku by kode izin
    function get_masa_berlaku_by_kode_izin($params) {
        $sql = "SELECT izin_expired_date 
                FROM izin_rute a
                WHERE airlines_id = ?
                AND a.izin_completed = '1'
                AND a.izin_approval = 'approved' 
                AND a.izin_flight = ?
                AND a.izin_active = '1'
                AND kode_izin = ?
                LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['izin_expired_date'];
        } else {
            return '';
        }
    }

    // get list files references
    function get_list_file_required_domestik($params) {
        $sql = "SELECT a.* FROM izin_file_references a
                INNER JOIN izin_rules_files b ON a.ref_id = b.ref_id
                WHERE b.group_id = ? AND b.data_flight = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list slot uploaded
    function get_list_slot_time_by_id($params) {
        $sql = "SELECT a.* FROM izin_slot_time a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE a.registrasi_id = ? AND airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail slot  by id
    function get_detail_slot_by_id($params) {
        $sql = "SELECT * FROM izin_slot_time WHERE registrasi_id = ? AND slot_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total files uploaded
    function get_total_file_uploaded($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_files a
                WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list files uploaded
    function get_list_file_uploaded($params) {
        $sql = "SELECT a.*, c.file_id, file_path, file_name, file_check, check_date, d.operator_name'check_by'
                FROM izin_file_references a
                INNER JOIN izin_rules_files b ON a.ref_id = b.ref_id
                LEFT JOIN izin_files c ON a.ref_id = c.ref_id AND c.registrasi_id = ?
                LEFT JOIN com_user d ON c.check_by = d.user_id
                WHERE b.group_id = ? AND b.data_flight = ?";
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
    function get_list_file_uploaded_int($params) {
        $sql = "SELECT a.*, c.file_id, file_path, file_name, file_check, check_date, d.operator_name'check_by'
                FROM izin_file_references a
                INNER JOIN izin_rules_files b ON a.ref_id = b.ref_id
                LEFT JOIN izin_files c ON a.ref_id = c.ref_id AND c.registrasi_id = ?
                LEFT JOIN com_user d ON c.check_by = d.user_id
                WHERE b.group_id = ? AND b.data_flight = ? AND b.airlines_st = ?";
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
        $sql = "SELECT * FROM izin_files WHERE file_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list editorial uploaded
    function get_list_editorial($params) {
        $sql = "SELECT * 
            FROM (
                SELECT redaksional_id, redaksional_nm, redaksional_mail, redaksional_group 
                FROM redaksional 
                ORDER BY redaksional_group DESC 
            )rs1
            LEFT JOIN 
            (
                SELECT b.*, c.operator_name 
                FROM izin_tembusan b 
                LEFT JOIN com_user c ON c.user_id = b.tembusan_by
                WHERE b.registrasi_id = ? 
            )rs2 ON rs2.tembusan_value = rs1.redaksional_nm
            ORDER BY rs1.redaksional_group DESC, rs1.redaksional_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list redaksional
    function get_list_redaksional($params) {
        $sql = "SELECT * 
                FROM izin_tembusan a 
                LEFT JOIN redaksional b ON b.redaksional_id = a.tembusan_value
                WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total editorial
    function get_total_editorial($params) {
        $sql = "SELECT COUNT(*)'total' 
            FROM com_preferences 
            WHERE pref_group = 'published_izin' AND (pref_nm = 'tembusan' OR pref_nm = 'kepada')";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get pref value
    function get_pref_value($params) {
        $sql = "SELECT redaksional_nm 
            FROM redaksional 
            WHERE redaksional_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['redaksional_nm'];
        } else {
            return 0;
        }
    }

    // delete tembusan
    function delete_tembusan($params) {
        return $this->db->delete("izin_tembusan", $params);
    }

    // insert tembusan
    function insert_tembusan($params) {
        return $this->db->insert("izin_tembusan", $params);
    }

    // checklist files
    function check_list_files_all($params) {
        $sql = "UPDATE izin_files SET file_check = ?, check_by = ?, check_date = ? WHERE registrasi_id = ?";
        return $this->db->query($sql, $params);
    }

    // checklist files
    function check_list_files($params) {
        $sql = "UPDATE izin_files SET check_by = ?, check_date = NOW(), file_check = ? WHERE file_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert catatan airlines
    function update_catatan_airlines($params) {
        return $this->db->insert('izin_notes', $params);
    }

    // get list notes airlines
    function get_list_notes_by_izin($params) {
        $sql = "SELECT a.*, operator_name 
                FROM izin_notes a
                LEFT JOIN com_user b On a.note_by = b.user_id
                WHERE registrasi_id = ?
                ORDER BY note_date DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // delete telaah
    function delete_telaah($params) {
        $sql = "DELETE FROM izin_telaah WHERE registrasi_id = ?";
        return $this->db->query($sql, $params);
    }

    // add process
    function insert_telaah($params) {
        $sql = "INSERT INTO izin_telaah (registrasi_id, telaah_file, telaah_by, telaah_date)
                VALUES (?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // get detail telaah
    function get_detail_telaah_by_id($params) {
        $sql = "SELECT a.*, operator_name FROM izin_telaah a
                LEFT JOIN com_user b ON a.telaah_by = b.user_id 
                WHERE registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list proses
    function get_list_process_by_id($params) {
        $sql = "SELECT a.*, role_nm, operator_name, task_nm
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN com_role c ON b.role_id = c.role_id
                LEFT JOIN com_user u ON a.mdb_finish = u.user_id
                WHERE registrasi_id = ? AND catatan IS NOT NULL AND mdd_finish IS NOT NULL
                ORDER BY mdd_finish DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list waiting by registrasi
    function get_list_waiting_approval_by_registrasi($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM izin_rute 
                WHERE registrasi_id = ? AND izin_completed = '0' AND izin_approval = 'waiting'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list all rejected by registrasi
    function get_list_reject_all_by_registrasi($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute 
                WHERE registrasi_id = ?
                AND izin_completed = '0' 
                AND izin_approval <> 'rejected'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // edit process
    function update_process($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_process', $params);
    }

    // update izin
    function update_izin($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_rute', $params);
    }

    // update registrasi
    function update_registrasi($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_registrasi', $params);
    }

    // action control
    function get_action_control($params) {
        $sql = "SELECT action_reject, action_revisi, action_send, action_rollback, action_publish 
                FROM com_role_action
                WHERE role_id = ? AND services_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update process
    function action_update($params) {
        $sql = "UPDATE izin_process SET process_st = ?, action_st = ? , mdb_finish = ?, mdd_finish = NOW()          
                WHERE process_id = ?";
        return $this->db->query($sql, $params);
    }

    // add process
    function insert_process($params) {
        $sql = "INSERT INTO izin_process (process_id, registrasi_id, flow_id, mdb, mdd)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // get role next flow
    function get_role_next_from($params) {
        $sql = "SELECT role_id 
                FROM izin_flow 
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

    // done process registrasi
    function registrasi_done_process($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_registrasi', $params);
    }

    // get list izin rute by registrasi
    function get_list_rute_by_registrasi($params) {
        $sql = "SELECT * FROM izin_rute WHERE registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update st active
    function update_st_by_kode_frekuensi($params) {
        $sql = "UPDATE izin_rute SET izin_active = ? WHERE kode_frekuensi = ?";
        return $this->db->query($sql, $params);
    }

    // get_rute_by_kode_izin
    function get_rute_by_kode_izin($params) {
        $sql = "SELECT izin.*, MIN(rute_all)'izin_rute_start', MAX(rute_all)'izin_rute_end', b.start_date, b.end_date, b.tipe, b.capacity, b.roon FROM
                (
                        SELECT a.izin_id, kode_izin, kode_frekuensi, a.dos, a.izin_expired_date, 
                        SUM(
                                IF(SUBSTRING(a.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 3, 1) = 0, 0, 1) +
                                IF(SUBSTRING(a.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 6, 1) = 0, 0, 1) +
                                IF(SUBSTRING(a.dos, 7, 1) = 0, 0, 1)
                        )'frekuensi'
                        FROM izin_rute a
                        WHERE kode_izin = ? AND izin_active = '1'
                        GROUP BY kode_izin
                ) izin
                LEFT JOIN izin_data b ON izin.izin_id = b.izin_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by kode frekuensi
    function get_list_data_rute_by_kode_frekuensi($params) {
        $sql = "SELECT a.*, kode_izin, kode_frekuensi,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.kode_frekuensi = ? AND b.izin_active = '1'";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /*
      // previev surat
     */

    // get detail
    function get_preview_izin_by_registrasi($params) {
        $sql = "SELECT a.*, group_nm, group_alias, u.operator_name'pengirim', airlines_nm, airlines_address, airlines_nationality 
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                INNER JOIN airlines c ON a.airlines_id = c.airlines_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get by id for searching
    function get_preferences_by_group_and_name($params) {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = ? AND pref_nm = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list slot by id
    function get_list_data_slot_by_id($params) {
        $sql = "SELECT a.* FROM izin_slot_time a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list redaksional
    function get_list_redaksional_preview($params) {
        $sql = "SELECT a.pref_id, a.pref_group, a.pref_nm, a.pref_value 
            FROM com_preferences a 
            RIGHT JOIN izin_tembusan b ON b.tembusan_value = a.pref_nm
            WHERE a.pref_group = 'redaksional' AND b.registrasi_id = ?
            ORDER BY a.pref_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list editorial kepada
    function get_list_editorial_kepada($params) {
        $sql = "SELECT redaksional_nm 
            FROM izin_tembusan a 
            LEFT JOIN redaksional b ON b.redaksional_id = a.tembusan_value 
            WHERE a.registrasi_id = ? AND b.redaksional_group = 'kepada' 
            ORDER BY a.tembusan_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list editorial tembusan
    function get_list_editorial_tembusan($params) {
        $sql = "SELECT redaksional_nm 
            FROM izin_tembusan a 
            LEFT JOIN redaksional b ON b.redaksional_id = a.tembusan_value 
            WHERE a.registrasi_id = ? AND b.redaksional_group = 'tembusan' 
            ORDER BY a.tembusan_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by kode izin
    function get_izin_rute_data_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, a.kode_frekuensi, a.izin_start_date, a.izin_expired_date, 
                aircraft_type, aircraft_capacity, dos, ron, pairing, a.izin_rute_start, a.izin_rute_end,
                a.izin_penundaan_start, a.izin_penundaan_end,
                ( 
                        IF(SUBSTRING(doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                ((SPLIT_STRING(izin_published_letter, '/', 1)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number', 
                a.notes, b.doop, b.start_date, b.end_date, b.tipe, b.capacity, b.roon, d.airlines_iata_cd 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                LEFT JOIN airlines d ON d.airlines_id = c.airlines_id
                WHERE a.registrasi_id = ? 
                ORDER BY a.kode_frekuensi ASC, b.izin_id ASC, b.etd ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list surat ijin rute by kode frekuensi
    function get_surat_ijin_by_kode_frekuensi($params) {
        $sql = "SELECT b.registrasi_id, izin_published_letter, izin_published_date, group_nm, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_group c ON b.izin_group = c.group_id
                WHERE kode_frekuensi = ? AND a.registrasi_id <> ?
                AND ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3)) <= ?
                ORDER BY izin_published_letter DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by kode frekuensi
    function get_list_data_rute_by_kode_frekuensi_old($params) {
        $sql = "SELECT a.*, b.kode_izin, b.kode_frekuensi, izin_published_letter,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                WHERE b.kode_frekuensi = ? AND c.registrasi_id <> ? AND (((SPLIT_STRING (izin_published_letter, '/', 2) * 25) + SPLIT_STRING (izin_published_letter, '/', 3)) + ABS(RIGHT(SPLIT_STRING(izin_published_letter, '/', 1), 3))) < IF(ISNULL(?), 10000000, ?) 
                ORDER BY a.etd ASC, izin_published_letter DESC, rute_id ASC 
                LIMIT 2";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by kode frekuensi
    function get_list_data_rute_by_kode_frekuensi_old_preview($params) {
        $sql = "SELECT a.*, b.kode_izin, b.kode_frekuensi, izin_published_letter,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                WHERE b.kode_frekuensi = ? AND c.registrasi_id <> ? 
                ORDER BY b.kode_frekuensi ASC, a.izin_id ASC, a.etd 
                LIMIT 2";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /* ================================= MEMO ================================= */

    // get detail memo
    function get_detail_memo_by_id($params) {
        $sql = "SELECT a.* 
                FROM izin_memos a
                WHERE memo_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list memos
    function get_list_memos_by_izin($params) {
        $sql = "SELECT a.*, operator_name 
                FROM izin_memos a
                LEFT JOIN com_user b On a.memo_by = b.user_id
                WHERE registrasi_id = ?
                ORDER BY memo_date DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert memo process
    function insert_memo_process($params) {
        return $this->db->insert('izin_memos', $params);
    }

    // delete memo process
    function delete_memo_process($params) {
        return $this->db->delete('izin_memos', $params);
    }

    // total frekuensi
    function get_total_frekuensi($params) {
        $sql = "SELECT SUM(result.total)'total' 
            FROM 
            (
            SELECT a.registrasi_id, MAX(SPLIT_STRING(GET_FREKUENSI_TOTAL(b.izin_id, b.flight_no), '/', 1))'total', b.* 
            FROM izin_rute a 
            LEFT JOIN izin_data b ON b.izin_id = a.izin_id
            WHERE a.registrasi_id = ? 
            GROUP BY a.izin_id 
            ORDER BY b.rute_id ASC, b.etd ASC
            )result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }

    /* ================================= MIGRASI ================================= */

    // get detail migrasi by id
    function get_detail_migrasi_data_by_id($params) {
        $sql = "SELECT a.*, b.*, d.group_alias, d.group_nm, 
                u.operator_name'pengirim', airlines_nm, airlines_iata_cd, airlines_nationality,f.operator_name AS 'izin_verified_by', g.kode_izin, g.kode_frekuensi
                FROM izin_registrasi a
                INNER JOIN izin_process b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_flow c ON b.flow_id = c.flow_id
                INNER JOIN izin_group d ON a.izin_group = d.group_id
                INNER JOIN airlines e ON a.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                LEFT JOIN com_user f ON a.izin_valid_by=f.user_id
                LEFT JOIN izin_rute g ON g.registrasi_id = a.registrasi_id
                WHERE a.registrasi_id = ? AND c.role_id = ? AND b.action_st = 'process'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by kode frekuensi
    function get_list_data_rute_by_kode_frekuensi_old_preview2($params) {
        $sql = "SELECT a.*, b.kode_izin, b.kode_frekuensi, izin_published_letter,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                WHERE c.registrasi_id <> ? AND (a.rute_all = ?)
                ORDER BY b.kode_frekuensi ASC, a.izin_id ASC, a.etd 
                LIMIT 1";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list surat ijin rute by kode frekuensi
    function get_surat_ijin_by_kode_frekuensi2($params) {
        $sql = "SELECT b.registrasi_id, izin_published_letter, izin_published_date, group_nm, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_group c ON b.izin_group = c.group_id
                WHERE a.registrasi_id <> ? AND (b.izin_rute_start = ? OR b.izin_rute_end = ?) AND a.airlines_id = ?
                GROUP BY izin_published_letter 
                ORDER BY izin_published_letter DESC";
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
