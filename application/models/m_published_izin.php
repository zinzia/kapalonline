<?php

class m_published_izin extends CI_Model {

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
                        WHERE airlines_id = ? AND izin_completed = '1' AND izin_approval = 'approved'
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
                WHERE airlines_id = ? 
                AND YEAR(izin_published_date) = ? AND MONTH(izin_published_date) = ?
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
        $sql = "SELECT a.*, group_nm, group_alias, operator_name
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN com_user u ON a.izin_request_by = u.user_id
                WHERE airlines_id = ? 
                AND YEAR(izin_published_date) = ? AND MONTH(izin_published_date) = ?
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
                ((SPLIT_STRING(izin_published_letter, '/', 1)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number', a.notes, b.doop, b.start_date, b.end_date, b.tipe, b.capacity, b.roon, d.airlines_iata_cd 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                LEFT JOIN airlines d ON d.airlines_id = c.airlines_id
                WHERE a.izin_completed = '1' AND a.izin_approval = 'approved' 
                AND a.registrasi_id = ? 
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
                ((SPLIT_STRING(izin_published_letter, '/', 1)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_group c ON b.izin_group = c.group_id
                WHERE kode_frekuensi = ? AND a.registrasi_id <> ?
                AND ((SPLIT_STRING(izin_published_letter, '/', 1)*25) + SPLIT_STRING(izin_published_letter, '/', 3)) <= ?
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

    // get list rute by kode frekuensi
    function get_list_data_rute_by_kode_frekuensi_old($params) {
        $sql = "SELECT a.*, b.kode_izin, b.kode_frekuensi, izin_published_letter,
                (
                IF(SUBSTRING(a.doop, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.doop, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(a.doop, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                ((SPLIT_STRING(izin_published_letter, '/', 1)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                WHERE b.kode_frekuensi = ? AND c.registrasi_id <> ?
                ORDER BY izin_published_letter DESC, rute_id ASC 
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
                ((SPLIT_STRING(izin_published_letter, '/', 1)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON b.registrasi_id = c.registrasi_id
                WHERE b.kode_frekuensi = ? AND c.registrasi_id <> ? 
                ORDER BY izin_published_letter DESC, rute_id ASC 
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
}
