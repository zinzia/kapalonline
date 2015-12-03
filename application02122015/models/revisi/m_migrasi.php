<?php

class m_migrasi extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get data id
    function get_data_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get default season
    function get_default_season() {
        $sql = "SELECT pref_value FROM com_preferences WHERE pref_group = 'season' AND pref_nm = 'def'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['pref_value'];
        } else {
            return 'S15';
        }
    }

    // get detail registrasi by id
    function get_registrasi_pending_by_id($params) {
        $sql = "SELECT a.*, group_nm, c.process_id, c.catatan
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                INNER JOIN izin_process c ON c.registrasi_id = a.registrasi_id
                WHERE a.registrasi_id = ? AND a.airlines_id = ? 
                AND a.izin_request_st = '1' AND a.izin_completed = '0' AND a.izin_approval = 'waiting'
                AND a.izin_group = ? AND c.process_st = 'waiting' AND c.flow_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get catatatan permohonan
    function get_catatan_perbaikan_by_registrasi($params) {
        $sql = "SELECT * 
                FROM izin_process 
                WHERE registrasi_id = ? 
                AND process_st = 'reject' AND catatan IS NOT NULL 
                ORDER BY mdd_finish DESC 
                LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['catatan'];
        } else {
            return '';
        }
    }

    // get detail izin rute by id
    function get_izin_rute_pending_by_id($params) {
        $sql = "SELECT izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, is_used_score 
                FROM izin_rute
                WHERE registrasi_id = ? AND airlines_id = ? 
                AND izin_approval = 'waiting' AND izin_completed = '0' 
                AND izin_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get validasi total rute yang sudah terdaftar
    function get_total_rute_existing_by_migrasi($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute 
                WHERE airlines_id = ? AND registrasi_id <> ? AND izin_approval <> 'rejected'
                AND (izin_rute_start = ? OR izin_rute_start = ? OR izin_rute_end = ? OR izin_rute_end = ?)";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get validasi total rute yang masih aktif
    function get_total_rute_existing_by_new_rute($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute 
                WHERE airlines_id = ? AND (izin_rute_start = ? OR izin_rute_start = ? OR izin_rute_end = ? OR izin_rute_end = ?)
                AND izin_completed = '1' AND izin_approval = 'approved' AND izin_active = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get validasi total rute yang masih waiting on proses
    function get_total_rute_process_by_new_rute($params) {
        $sql = "SELECT COUNT(*)'total' FROM izin_registrasi 
                WHERE airlines_id = ? AND (izin_rute_start = ? OR izin_rute_start = ? OR izin_rute_end = ? OR izin_rute_end = ?)
                AND izin_completed = '0' AND registrasi_id <> ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get izin rute dicabut
    function get_total_rute_existing_canceled($params) {
        $sql = "SELECT COUNT(*) 'total', 
                (DATEDIFF(b.izin_published_date, NOW()) <= 365)'diff'
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id
                WHERE a.airlines_id = ? 
                AND (a.izin_rute_start = ? OR a.izin_rute_start = ? OR a.izin_rute_end = ? OR a.izin_rute_end = ?) 
                AND a.izin_completed = '1' AND a.izin_approval = 'approved' AND a.izin_st = 'pencabutan' AND (DATEDIFF(b.izin_published_date, NOW()) <= 365) AND (b.izin_group = '7' OR b.izin_group = '27') 
                GROUP BY b.registrasi_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get validasi total rute yang masih sudah diinputkan
    function get_total_rute_by_registrasi_id($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM izin_rute
                WHERE registrasi_id = ?
                AND izin_rute_start <> ? AND izin_rute_end <> ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list izin rute pending by id
    function get_list_izin_rute_pending_by_id($params) {
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, a.registrasi_id, 
                a.izin_completed, a.izin_approval, a.izin_type, a.izin_flight, a.izin_st, 
                a.izin_start_date, a.izin_expired_date, 
                a.izin_rute_start, a.izin_rute_end, a.pairing,
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                LEFT JOIN izin_data b On a.izin_id = b.izin_id    
                WHERE a.registrasi_id = ? AND a.airlines_id = ? AND a.izin_completed = '0'
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

    // get list izin data pending by id
    function get_list_izin_data_pending_by_id($params) {
        $sql = "SELECT rute_id, izin_id, rute_all, tipe, capacity, flight_no, etd, eta, doop, roon, 
                start_date, end_date, is_used_score, GET_FREKUENSI_FROM_DOS(doop)'frekuensi'
                FROM izin_data a
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

    // get izin rute empty data by id
    function get_izin_rute_empty_data_by_id($params) {
        $sql = "SELECT IFNULL(MIN(total), 1)'total' 
                FROM 
                (
                        SELECT a.izin_id, COUNT(b.rute_id)'total'
                        FROM izin_rute a
                        LEFT JOIN izin_data b ON a.izin_id = b.izin_id
                        WHERE registrasi_id = ? AND airlines_id = ?
                        GROUP BY a.izin_id
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

    // get izin rute empty data by id
    function get_total_izin_data_by_registrasi($params) {
        $sql = "SELECT COUNT(b.rute_id)'total'
                FROM izin_rute a
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                WHERE registrasi_id = ? AND airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
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

    // check layanan
    function get_services_flight($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM 
                (
                        SELECT rute_all FROM izin_data WHERE izin_id = ? GROUP BY rute_all
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

    // check nomor penerbangan pada izin data
    function get_flight_no_by_izin_id($params) {
        $sql = "SELECT flight_no 
                FROM izin_data 
                WHERE izin_id = ? AND rute_all = ?
                GROUP BY flight_no";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['flight_no'];
        } else {
            return '';
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

    /*
     * EXEC
     */

    // UPDATE REGISTRASI
    function update_izin_registrasi($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_registrasi', $params);
    }

    // INSERT RUTE
    function insert_izin_rute($params) {
        // execute
        return $this->db->insert('izin_rute', $params);
    }

    // UPDATE RUTE
    function update_izin_rute($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_rute', $params);
    }

    // INSERT DATA RUTE
    function insert_izin_data($params) {
        // execute
        return $this->db->insert('izin_data', $params);
    }

    // DELETE RUTE
    function delete_izin_rute($where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->delete('izin_rute', $where);
    }

    // DELETE DATA
    function delete_izin_data($where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->delete('izin_data', $where);
    }

    // DELETE DATA SLOT
    function delete_izin_data_slot($where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->delete('izin_data_slot', $where);
    }

    // INSERT DATA SLOT
    function insert_izin_data_slot($params) {
        // execute
        return $this->db->insert('izin_data_slot', $params);
    }

    // INSERT PROCESS
    function insert_izin_process($params) {
        // execute
        return $this->db->insert('izin_process', $params);
    }

    // UPDATE PROCESS
    function update_izin_process($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_process', $params, $where);
    }

    // get total frekuensi by registrasi by kode frekuensi
    function get_total_frekuensi_by_kode_frekuensi($params) {
        $sql = "SELECT b.kode_frekuensi, MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
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

}
