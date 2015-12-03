<?php

class m_published_izin_regulator extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get list tahun
    function get_list_tahun_report($params) {
        $sql = "SELECT DISTINCT tahun FROM
                (
                        SELECT YEAR(izin_published_date)'tahun'
                        FROM izin_registrasi 
                        WHERE izin_completed = '1' AND izin_approval = 'approved'
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

    // get total task
    function get_total_finished_izin_registrasi($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_registrasi 
                WHERE YEAR(izin_published_date) = ? AND MONTH(izin_published_date) = ?
                AND izin_flight LIKE ? AND payment_st LIKE ? 
                AND izin_completed = '1' AND izin_approval = 'approved'";
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
    function get_list_finished_izin_registrasi($params) {
        $sql = "SELECT a.*, group_nm, group_alias, operator_name, c.airlines_nm
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id 
                LEFT JOIN airlines c ON c.airlines_id = a.airlines_id
                WHERE YEAR(izin_published_date) = ? AND MONTH(izin_published_date) = ?
                AND izin_flight LIKE ? AND payment_st LIKE ? 
                AND izin_completed = '1' AND izin_approval = 'approved'
                ORDER BY izin_published_date ASC
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

    // get detail
    function get_published_izin_by_registrasi($params) {
        $sql = "SELECT a.*, group_nm, group_alias, u.operator_name'pengirim', airlines_nm, airlines_address, airlines_nationality, 
                p.operator_name'published_by', p.operator_nip, p.operator_pangkat, p.jabatan
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                INNER JOIN airlines c ON a.airlines_id = c.airlines_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                LEFT JOIN com_user p ON a.izin_published_by = p.user_id
                WHERE a.registrasi_id = ? AND (payment_st = '11' OR payment_st = '22') 
                AND izin_completed = '1' AND izin_approval = 'approved'";
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
    function get_izin_rute_data_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, a.kode_frekuensi, a.izin_start_date, a.izin_expired_date, 
                aircraft_type, aircraft_capacity, dos, ron, pairing, a.izin_rute_start, a.izin_rute_end,
                a.izin_penundaan_start, a.izin_penundaan_end,
                ( 
                        IF(SUBSTRING(b.doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                (((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3)) * (ABS(RIGHT(SPLIT_STRING(izin_published_letter, '/', 1), 3)) * 10))'published_number', a.notes, b.doop, b.start_date, b.end_date, b.tipe, b.capacity, b.roon, c.izin_published_letter, d.airlines_iata_cd 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                LEFT JOIN airlines d ON d.airlines_id = c.airlines_id
                WHERE a.izin_completed = '1' AND a.izin_approval = 'approved' 
                AND a.registrasi_id = ? 
                ORDER BY a.kode_frekuensi ASC, b.izin_id ASC";
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
                WHERE b.kode_frekuensi = ? AND c.registrasi_id <> ? AND (((SPLIT_STRING (izin_published_letter, '/', 2) * 25) + SPLIT_STRING (izin_published_letter, '/', 3)) + ABS(RIGHT(SPLIT_STRING(izin_published_letter, '/', 1), 3))) < ? 
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
                WHERE b.kode_frekuensi = ? AND c.registrasi_id <> ? AND (((SPLIT_STRING (izin_published_letter, '/', 2) * 25) + SPLIT_STRING (izin_published_letter, '/', 3)) + ABS(RIGHT(SPLIT_STRING(izin_published_letter, '/', 1), 3))) < ? 
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

    // get list redaksional
    function get_list_redaksional($params) {
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

    /* ================================= MEMO ================================= */

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
        $sql = "SELECT rute_id, a.izin_id, rute_all, tipe, capacity, flight_no, etd, eta, doop, roon, 
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
    
    /*
     * RUTE SEBELUMNYA
     */

    // get list izin rute old by kode frekuensi
    function get_list_izin_rute_old_by_kode_frekuensi($params) {
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, notes,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id
                WHERE a.registrasi_id = (
                SELECT 
                    izin_references
                  FROM
                    izin_rute a 
                    INNER JOIN izin_data b 
                      ON a.izin_id = b.izin_id 
                  WHERE a.kode_frekuensi = ? 
                    AND a.airlines_id = ? 
                    AND a.registrasi_id = ?
                    AND a.izin_completed = '1' 
                    AND a.izin_approval = 'approved' 
                  GROUP BY a.izin_id 
                  ORDER BY izin_rute_start ASC
                )
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
    
    // get list izin rute old by kode frekuensi
    function get_list_izin_rute_pencabutan_old_by_kode_frekuensi($params) {
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, notes,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                INNER JOIN izin_data b On a.izin_id = b.izin_id
                WHERE a.registrasi_id = (
                SELECT 
                    izin_references
                  FROM
                    izin_rute a 
                    INNER JOIN izin_data b 
                      ON a.izin_id = b.izin_id 
                  WHERE a.kode_frekuensi = ? 
                    AND a.airlines_id = ? 
                    AND a.registrasi_id = ? 
                    AND a.izin_completed = '1' 
                    AND a.izin_approval = 'approved' 
                    AND a.izin_active = '0' 
                  GROUP BY a.izin_id 
                  ORDER BY izin_rute_start ASC
                )
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
}
