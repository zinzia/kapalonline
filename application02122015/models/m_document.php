<?php

class m_document extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get detail by data id
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.operator_name, c.jabatan, d.services_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
            LEFT JOIN com_user c ON c.user_id = a.mdb_finish 
            LEFT JOIN services_code d ON d.services_cd = a.services_cd
            WHERE a.published_no LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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

    // get izin by data id
    function get_data_izin_by_id($params) {
        $sql = "SELECT * 
            FROM izin_registrasi 
            WHERE izin_published_letter = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail
    function get_published_izin_by_registrasi($params) {
        $sql = "SELECT a.*, group_nm, group_alias, u.operator_name'pengirim', airlines_nm, airlines_address, 
                p.operator_name'published_by', p.operator_nip, p.operator_pangkat
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

    // get list redaksional
    function get_list_redaksional($params) {
        $sql = "SELECT a.pref_id, a.pref_group, a.pref_nm, a.pref_value 
            FROM com_preferences a 
            RIGHT JOIN izin_tembusan b ON b.tembusan_value = a.pref_value
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
            LEFT JOIN redaksional b ON b.redaksional_nm = a.tembusan_value 
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
            LEFT JOIN redaksional b ON b.redaksional_nm = a.tembusan_value 
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
                        IF(SUBSTRING(b.doop, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(b.doop, 7, 1) = 0, 0, 1) 
                )'frekuensi',
                b.rute_all, b.flight_no, b.etd, b.eta,
                (((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3)) * (ABS(RIGHT(SPLIT_STRING(izin_published_letter, '/', 1), 3)) * 10))'published_number', a.notes, b.doop, b.start_date, b.end_date, b.tipe, b.capacity, b.roon, c.izin_published_letter 
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                LEFT JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                WHERE a.izin_completed = '1' AND a.izin_approval = 'approved' 
                AND a.registrasi_id = ? AND a.airlines_id = ? 
                ORDER BY kode_frekuensi ASC, b.rute_id ASC";
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

    // get list surat ijin rute by kode frekuensi
    function get_surat_ijin_by_kode_frekuensi($params) {
        $sql = "SELECT b.registrasi_id, izin_published_letter, izin_published_date, group_nm, 
                ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3))'published_number'
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                INNER JOIN izin_group c ON b.izin_group = c.group_id
                WHERE kode_frekuensi = ? AND a.registrasi_id <> ?
                AND ((SPLIT_STRING(izin_published_letter, '/', 2)*25) + SPLIT_STRING(izin_published_letter, '/', 3)) < ?
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
