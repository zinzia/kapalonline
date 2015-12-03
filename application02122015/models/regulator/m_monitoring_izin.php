<?php

class m_monitoring_izin extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get list waiting
    function get_list_my_task_waiting($params) {
        $sql = "SELECT c.registrasi_id, c.izin_flight, c.izin_rute_start, c.izin_rute_end,
                c.izin_request_letter, c.izin_request_letter_date, c.izin_request_date,
                group_nm, airlines_nm, task_link, operator_name,
                DATEDIFF(CURDATE(), a.mdd)'selisih_hari', 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8))'selisih_waktu', 
                group_alias
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_rute r ON c.registrasi_id = r.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON c.izin_request_by = u.user_id
                WHERE action_st = 'process' AND c.izin_completed = '0' AND c.izin_request_st = '1' 
                AND c.airlines_id = ? AND c.izin_request_letter LIKE ?
                AND c.izin_flight LIKE ? AND r.izin_st LIKE ?
                AND input_by = 'member'
                GROUP BY c.registrasi_id
                ORDER BY c.izin_request_date ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail registrasi by id
    function get_detail_registrasi_data_by_id($params) {
        $sql = "SELECT a.registrasi_id, a.airlines_id, a.izin_group, a.izin_completed, a.izin_approval, a.izin_type, a.izin_flight, 
                a.izin_request_letter, a.izin_request_letter_date, a.izin_request_st, a.izin_request_date, a.izin_request_by, 
                a.izin_rute_start, a.izin_rute_end, a.kode_izin, a.penanggungjawab, a.jabatan, izin_published_letter, izin_published_date, c.flow_id,
                task_nm, b.mdd'tanggal_proses', d.group_nm, u.operator_name'pengirim', u.jabatan'jabatan_operator', izin_season, 
                e.airlines_nm, pax_cargo, airlines_nationality
                FROM izin_registrasi a
                INNER JOIN izin_process b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_flow c ON b.flow_id = c.flow_id
                INNER JOIN izin_group d ON a.izin_group = d.group_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                LEFT JOIN airlines e ON a.airlines_id=e.airlines_id
                WHERE a.registrasi_id = ? AND izin_request_st = '1'  AND b.action_st = 'process'
                GROUP BY a.registrasi_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute by id
    function get_list_izin_rute_by_id($params) {
        $sql = "SELECT izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing 
                FROM izin_rute a
                WHERE a.registrasi_id = ? AND a.airlines_id = ?
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
                kode_izin, kode_frekuensi, b.izin_st
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                INNER JOIN airlines c ON b.airlines_id = c.airlines_id
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

    // get total frekuensi by izin_id
    function get_total_frekuensi_by_izin_id($params) {
        $sql = "SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(izin_id, flight_no))'frekuensi'
                FROM izin_data a
                WHERE izin_id = ?
                GROUP BY a.izin_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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

    // get list files references
    function get_list_file_required_internasional($params) {
        $sql = "SELECT a.* FROM izin_file_references a
                INNER JOIN izin_rules_files b ON a.ref_id = b.ref_id
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

    // get list files uploaded
    function get_list_file_uploaded($params) {
        $sql = "SELECT * FROM izin_files WHERE registrasi_id = ?";
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
        $sql = "SELECT * FROM izin_files WHERE registrasi_id = ? AND ref_id = ?";
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
                WHERE a.registrasi_id = ? AND airlines_id = ?";
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

    // get list rute by id
    function get_list_data_rute_by_id($params) {
        $sql = "SELECT a.*, kode_izin, kode_frekuensi, izin_approval,
                (
                IF(SUBSTRING(b.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                izin_penundaan_start, izin_penundaan_end
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.registrasi_id = ? AND airlines_id = ? AND b.izin_completed = '0'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get_rute_by_kode_izin
    function get_rute_by_kode_izin($params) {
        $sql = "SELECT izin.*, MIN(rute_all)'izin_rute_start', MAX(rute_all)'izin_rute_end' FROM
                (
                        SELECT a.izin_id, kode_izin, kode_frekuensi, a.dos,
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

    // get list waiting
    function get_total_my_task_waiting_opr($params) {
        $sql = "SELECT COUNT(*) AS 'jumlah'
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON c.izin_request_by = u.user_id
                WHERE action_st = 'process' AND c.izin_completed = '0' AND c.izin_request_st = '1' 
                AND c.izin_request_letter LIKE ? AND c.izin_flight LIKE ? AND b.role_id LIKE ?
                AND c.airlines_id = ?
                ORDER BY c.izin_request_date ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result["jumlah"];
        } else {
            return 0;
        }
    }

    function get_list_my_task_waiting_opr($params) {
        $sql = "SELECT c.*, group_nm, e.airlines_nm, task_link, operator_name,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu, group_alias
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                LEFT JOIN com_user u ON c.izin_request_by = u.user_id
                WHERE action_st = 'process' AND c.izin_completed = '0' AND c.izin_request_st = '1' 
                AND c.izin_request_letter LIKE ? AND c.izin_flight LIKE ? AND b.role_id LIKE ?
                AND c.airlines_id = ?
                ORDER BY c.izin_request_date ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
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
                IF(SUBSTRING(b.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date,
                izin_penundaan_start, izin_penundaan_end
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.kode_frekuensi = ? AND airlines_id = ? AND b.izin_active = '1'";
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

    // get list izin rute by kode izin
    function get_list_izin_rute_aktif_by_kode_izin($params) {
        $sql = "SELECT izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, kode_izin, kode_frekuensi
                FROM izin_rute a
                WHERE a.kode_izin = ? AND a.airlines_id = ? 
                AND a.izin_completed = '1'
                AND a.izin_approval = 'approved'
                AND a.izin_active = '1'
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

    // get total frekuensi by registrasi by kode frekuensi
    function get_total_frekuensi_by_kode_frekuensi($params) {
        $sql = "SELECT b.kode_frekuensi, MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi', b.izin_st
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.registrasi_id = ?
                        GROUP BY a.izin_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total frekuensi existing by kode_frekuensi
    function get_total_frekuensi_existing_by_kode_frekuensi($params) {
        $sql = " SELECT b.kode_frekuensi, MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.kode_izin = ? AND b.izin_completed = '1'
                        AND b.izin_approval = 'approved'
                        AND b.izin_active = '1'
                        GROUP BY a.izin_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

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
    
    // get detail files pencabutan by id
    function get_detail_files_pencabutan_by_id($params) {
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

    /***** PERUBAHAN *****/
    // get total frekuensi by registrasi
    function get_total_frekuensi_perubahan_by_registrasi_id($params) {
        $sql = "SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi' 
                FROM 
                (
                        SELECT MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.registrasi_id = ?
                        AND b.izin_st <> 'pencabutan'
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
