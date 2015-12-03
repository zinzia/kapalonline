<?php

class m_task extends CI_Model {

    // constructor
    public function __construct() {
        parent::__construct();
    }

    // list group
    function get_list_group() {
        $sql = "SELECT group_id, group_nm FROM izin_group WHERE group_st = 'show'";
        $query = $this->db->query($sql);
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
     * TASK
     */

    // list waiting
    function get_list_my_task_waiting($params) {
        $sql = "SELECT c.*, group_nm, airlines_nm, task_link, d.group_alias,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu,
                u.operator_name'pengirim'
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                INNER JOIN com_role_user f ON f.role_id = b.role_id
                INNER JOIN com_user g ON g.user_id = f.user_id
                INNER JOIN com_user_airlines h ON h.user_id = g.user_id AND h.airlines_id = c.airlines_id
                LEFT JOIN com_user u ON c.izin_request_by = u.user_id
                WHERE b.role_id = ? AND g.user_id = ? AND action_st = 'process' 
                AND c.izin_completed = '0' AND c.izin_request_st = '1' 
                AND airlines_nm LIKE ? AND d.group_nm LIKE ?
                GROUP BY c.registrasi_id 
                ORDER BY selisih_hari DESC, selisih_waktu DESC";
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

    // detail task sesuai flow
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

    // detail notes
    function get_rute_notes_by_id($params) {
        $sql = "SELECT izin_id, notes FROM izin_rute WHERE izin_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail catatan permohonan
    function get_total_catatan_by_registrasi($params) {
        $sql = "SELECT COUNT(process_id)'total' FROM izin_process WHERE registrasi_id = ?
                AND catatan IS NOT NULL AND mdd_finish IS NOT NULL";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
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

    /*
     * REGISTRASI dan RUTE PENERBANGAN
     */

    // get detail airport
    function get_airport_score_by_code($params) {
        $sql = "SELECT airport_nm, is_used_score, airport_region FROM airport WHERE airport_iata_cd = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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

    // get kode izin yang terdaftar
    function get_kode_izin_terdaftar($params) {
        // airlines id $detail['izin_rute_start'], $detail['izin_rute_end'], $detail['izin_season']
        $sql = "SELECT a.kode_izin 
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE a.airlines_id = ?
                AND (a.izin_rute_start = ? OR a.izin_rute_start = ?) 
                AND (a.izin_rute_end = ? OR a.izin_rute_end = ?) 
                AND izin_season = ?
                AND b.registrasi_id <> ?
                GROUP BY a.kode_izin";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['kode_izin'];
        } else {
            return false;
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

    // get detail registrasi waiting by id
    function get_detail_registrasi_waiting_by_id($params) {
        $sql = "SELECT a.*, b.*, 
                task_link, d.group_alias, d.group_nm, izin_perihal,
                u.operator_name'pengirim', airlines_nm, airlines_iata_cd, 
                airlines_nationality, f.operator_name AS 'izin_verified_by',
                IF(izin_published_date IS NULL, CURRENT_DATE, izin_published_date)'izin_published_date'
                FROM izin_registrasi a
                INNER JOIN izin_process b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_flow c ON b.flow_id = c.flow_id
                INNER JOIN izin_group d ON a.izin_group = d.group_id
                INNER JOIN airlines e ON a.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                LEFT JOIN com_user f ON a.izin_valid_by = f.user_id
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

    // get list izin rute by id
    function get_list_izin_rute_by_id($params) {
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, notes,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id
                WHERE a.registrasi_id = ? AND a.airlines_id = ?
                GROUP BY a.izin_id
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by id
    function get_list_izin_rute_approved_by_id($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, notes,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi' 
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id 
                WHERE a.registrasi_id = ? AND a.airlines_id = ? AND a.izin_approval = 'approved'
                GROUP BY a.izin_id
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin data by id
    function get_list_izin_data_by_id($params) {
        $sql = "SELECT rute_id, a.izin_id, rute_all, tipe, capacity, 
                IF(LENGTH(TRIM(flight_no)) > 4, TRIM(flight_no), CONCAT(airlines_iata_cd, TRIM(flight_no)))'flight_no', 
                etd, eta, doop, roon, 
                start_date, end_date, a.is_used_score, GET_FREKUENSI_FROM_DOS(doop)'frekuensi',
                kode_izin, kode_frekuensi, izin_approval, c.airlines_iata_cd
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id 
                INNER JOIN airlines c ON c.airlines_id = b.airlines_id 
                WHERE a.izin_id = ?
                ORDER BY rute_all ASC, etd ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total frekuensi by registrasi
    function get_total_frekuensi_by_registrasi_id($params) {
        $sql = "SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi' 
                FROM 
                (
                        SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.registrasi_id = ?
                        GROUP BY a.izin_id
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total frekuensi approved by registrasi
    function get_total_frekuensi_approved_by_registrasi_id($params) {
        $sql = "SELECT 
                MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi',
                IF(MIN(start_date) < CURRENT_DATE, CURRENT_DATE, MIN(start_date))'valid_start_date',
                IF(MAX(end_date) < CURRENT_DATE, CURRENT_DATE, MAX(end_date))'valid_end_date'
                FROM 
                (
                        SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.registrasi_id = ? AND b.izin_approval = 'approved'
                        GROUP BY a.izin_id
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total frekuensi by kode_izin
    function get_total_frekuensi_existing_by_kode_izin($params) {
        $sql = "SELECT COUNT(izin_id)'total_rute', MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi' 
                FROM 
                (
                        SELECT b.izin_id, MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.kode_izin = ? AND b.izin_completed = '1'
                        AND b.izin_approval = 'approved'
                        AND b.izin_active = '1'
                        GROUP BY a.izin_id
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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

    // update izin
    function update_izin($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_rute', $params);
    }

    // edit process
    function update_process($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_process', $params);
    }

    // update registrasi
    function update_registrasi($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_registrasi', $params);
    }

    // done process registrasi
    function registrasi_done_process($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_registrasi', $params);
    }

    // update st active
    function update_st_by_kode_frekuensi($params) {
        $sql = "UPDATE izin_rute SET izin_active = ? WHERE kode_frekuensi = ?";
        return $this->db->query($sql, $params);
    }

    // update st bayar
    function update_status_bayar_approved($params, $where) {
        $this->db->where($where);
        return $this->db->update('izin_rute', $params);
    }

    /*
     * RUTE SEBELUMNYA
     */

    // get list izin rute by kode frekuensi
    function get_list_izin_rute_aktif_by_kode_frekuensi($params) {
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, notes,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id
                WHERE a.kode_frekuensi = ? AND a.airlines_id = ?
                AND a.izin_completed = '1'
                AND a.izin_approval = 'approved'
                AND a.izin_active = '1'
                GROUP BY a.izin_id
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by kode izin
    function get_list_izin_rute_aktif_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, notes,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id
                WHERE a.kode_izin = ? AND a.airlines_id = ?
                AND a.izin_completed = '1'
                AND a.izin_approval = 'approved'
                AND a.izin_active = '1'
                GROUP BY a.izin_id
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /*
     * FILES DOWNLOAD
     */

    // check list files uploaded
    function check_list_file_uploaded($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_file_references a
                INNER JOIN izin_rules_files b ON a.ref_id = b.ref_id
                LEFT JOIN izin_files c ON a.ref_id = c.ref_id AND c.registrasi_id = ?
                LEFT JOIN com_user d ON c.check_by = d.user_id
                WHERE b.group_id = ? AND b.data_flight = ? 
                AND file_check IS NOT NULL AND file_check = '0'";
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
                WHERE b.group_id = ? AND b.data_flight = ? 
                AND b.airlines_st = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list file pencabutan
    function get_list_file_pencabutan_by_id($params) {
        $sql = "SELECT a.* FROM izin_file_pencabutan a
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

    // get list slot iasm
    function get_list_slot_iasm_by_id($params) {
        $sql = "SELECT a.* 
                FROM izin_data_slot a
                INNER JOIN izin_data b ON a.rute_id = b.rute_id
                INNER JOIN izin_rute c ON b.izin_id = c.izin_id
                WHERE c.registrasi_id = ? AND c.airlines_id = ?
                ORDER BY a.flight_no ASC, a.services_cd ASC";
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

    // get detail telaah
    function get_detail_telaah_by_id($params) {
        $sql = "SELECT a.*, operator_name 
                FROM izin_telaah a
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

    // get detail file pencabutan  by id
    function get_detail_file_pencabutan_by_id($params) {
        $sql = "SELECT * FROM izin_file_pencabutan WHERE registrasi_id = ? AND letter_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // add process
    function insert_telaah($params) {
        $sql = "INSERT INTO izin_telaah (registrasi_id, telaah_file, telaah_by, telaah_date)
                VALUES (?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // delete telaah
    function delete_telaah($params) {
        $sql = "DELETE FROM izin_telaah WHERE registrasi_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert memo process
    function insert_memo_process($params) {
        return $this->db->insert('izin_memos', $params);
    }

    // delete memo process
    function delete_memo_process($params) {
        return $this->db->delete('izin_memos', $params);
    }

    // delete tembusan
    function delete_tembusan($params) {
        return $this->db->delete('izin_tembusan', $params);
    }

    // insert tembusan
    function insert_tembusan($params) {
        return $this->db->insert('izin_tembusan', $params);
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

    /*
     * ACTION
     */

    // get process id
    function get_process_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
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

    /*
     * TARIF
     */

    // get tarif baru
    function get_tarif_rute_baru() {
        $sql = "SELECT pref_value FROM com_preferences WHERE pref_group = 'tarif_rute' AND pref_nm = 'baru'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['pref_value'];
        } else {
            return 0;
        }
    }

    // get tarif per frekuensi
    function get_tarif_rute_frekuensi() {
        $sql = "SELECT pref_value FROM com_preferences WHERE pref_group = 'tarif_rute' AND pref_nm = 'frekuensi'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['pref_value'];
        } else {
            return 0;
        }
    }

    // utilities
    function terbilang($x) {
        $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($x < 12)
            return " " . $abil[$x];
        elseif ($x < 20)
            return Terbilang($x - 10) . "belas";
        elseif ($x < 100)
            return Terbilang($x / 10) . " puluh" . Terbilang($x % 10);
        elseif ($x < 200)
            return " seratus" . Terbilang($x - 100);
        elseif ($x < 1000)
            return Terbilang($x / 100) . " ratus" . Terbilang($x % 100);
        elseif ($x < 2000)
            return " seribu" . Terbilang($x - 1000);
        elseif ($x < 1000000)
            return Terbilang($x / 1000) . " ribu" . Terbilang($x % 1000);
        elseif ($x < 1000000000)
            return Terbilang($x / 1000000) . " juta" . Terbilang($x % 1000000);
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

    // get surat penerbitan by kode frekuensi
    function get_surat_penerbitan_existing_by_kode_frekuensi($params) {
        $sql = "SELECT b.registrasi_id, izin_published_letter, izin_published_date, group_nm, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_group c ON b.izin_group = c.group_id
                WHERE kode_frekuensi = ? AND a.registrasi_id <> ?
                AND ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3)) <= ?
                ORDER BY izin_published_letter DESC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list editorial
    function get_list_editorial($params) {
        $sql = "SELECT * 
                FROM redaksional 
                WHERE redaksional_group = ? 
                ORDER BY redaksional_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list editorial by registrasi
    function get_list_editorial_by_registrasi($params) {
        $sql = "SELECT a.tembusan_id, redaksional_nm 
                FROM izin_tembusan a 
                INNER JOIN redaksional b ON b.redaksional_id = a.tembusan_value 
                WHERE a.registrasi_id = ? AND b.redaksional_group = ? 
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

    // get list redaksional by registrasi
    function get_list_redaksional_by_registrasi($params) {
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

    // get user by role
    function get_com_user_by_role($params) {
        $sql = "SELECT operator_name, operator_nip, operator_pangkat
                FROM com_user a
                INNER JOIN com_role_user b ON a.user_id = b.user_id
                WHERE role_id = ? AND operator_pangkat IS NOT NULL
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

    // get barcode value
    function get_barcode_value($params) {
        $sql = "SELECT * 
            FROM com_preferences a 
            WHERE a.pref_group = 'barcode' AND a.pref_nm = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get nomor surat sebelumnya
    function get_published_letter_old($params) {
        $sql = "SELECT a.izin_published_letter, a.izin_published_date, a.izin_perihal, a.izin_flight, b.group_nm 
        FROM izin_registrasi a 
        LEFT JOIN izin_group b ON b.group_id = a.izin_group 
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

    /* ==================== DOWNLOAD PUBLISHED LETTER ==================== */

    // get list izin rute by kode izin
    function get_izin_data_by_registrasi_id($params) {
        $sql = "SELECT rute_id, a.izin_id, rute_all, tipe, capacity, flight_no, etd, eta, doop, roon, 
                start_date, end_date, a.is_used_score, GET_FREKUENSI_FROM_DOS(doop)'frekuensi',
                kode_izin, kode_frekuensi, izin_approval, b.izin_penundaan_start, b.izin_penundaan_end, c.airlines_iata_cd, b.izin_st
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id 
                LEFT JOIN airlines c ON c.airlines_id = b.airlines_id 
                WHERE b.registrasi_id = ? AND b.izin_approval = 'approved' 
                ORDER BY b.izin_id ASC, a.rute_all ASC, a.etd ASC";
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

    // get total frekuensi
    function get_total_frekuensi($params) {
        $sql = "SELECT a.izin_id, pairing, MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi' 
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id 
                WHERE a.registrasi_id = ? AND a.airlines_id = ? AND a.izin_id = ? AND a.izin_approval = 'approved'
                GROUP BY a.izin_id
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // add blacklist
    function insert_izin_blacklist($params) {
        return $this->db->insert('izin_pencabutan', $params);
    }

    // get list files pencabutan
    function get_list_file_pencabutan_uploaded($params) {
        $sql = "SELECT * FROM izin_file_pencabutan WHERE registrasi_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail registrasi waiting by registrasi_id 
    function get_detail_registrasi_waiting_by_registrasi_id($params) {
        $sql = "SELECT a.*, b.*, 
                task_link, d.group_alias, d.group_nm, izin_perihal,
                u.operator_name'pengirim', airlines_nm, airlines_iata_cd, 
                airlines_nationality, f.operator_name AS 'izin_verified_by',
                IF(izin_published_date IS NULL, CURRENT_DATE, izin_published_date)'izin_published_date'
                FROM izin_registrasi a
                INNER JOIN izin_process b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_flow c ON b.flow_id = c.flow_id
                INNER JOIN izin_group d ON a.izin_group = d.group_id
                INNER JOIN airlines e ON a.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                LEFT JOIN com_user f ON a.izin_valid_by = f.user_id
                WHERE a.registrasi_id = ? AND b.action_st = 'process'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /*     * * PERUBAHAN ** */

    // get total frekuensi approved by registrasi
    function get_total_frekuensi_approved_perubahan_by_registrasi_id($params) {
        $sql = "SELECT 
                MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi',
                IF(MIN(start_date) < CURRENT_DATE, CURRENT_DATE, MIN(start_date))'valid_start_date',
                IF(MAX(end_date) < CURRENT_DATE, CURRENT_DATE, MAX(end_date))'valid_end_date'
                FROM 
                (
                        SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.registrasi_id = ? AND b.izin_approval = 'approved' AND b.izin_st <> 'pencabutan'
                        GROUP BY a.izin_id
                ) result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total frekuensi approved by registrasi
    function get_total_frekuensi_approved_pencabutan_by_registrasi_id($params) {
        $sql = "SELECT 
                MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi',
                IF(MIN(start_date) < CURRENT_DATE, CURRENT_DATE, MIN(start_date))'valid_start_date',
                IF(MAX(end_date) < CURRENT_DATE, CURRENT_DATE, MAX(end_date))'valid_end_date'
                FROM 
                (
                        SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.registrasi_id = ? AND b.izin_approval = 'approved' AND b.izin_st = 'pencabutan'
                        GROUP BY a.izin_id
                ) result";
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
