<?php

class m_registrasi extends CI_Model {

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

    // get detail group
    function get_detail_group_by_id($params) {
        $sql = "SELECT * FROM izin_group WHERE group_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list draft
    function get_list_draft_registrasi($params) {
        $sql = "SELECT a.*, group_nm, operator_name, group_alias, group_link, airlines_nm,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN com_user c ON c.user_id = a.mdb
                LEFT JOIN airlines ar ON a.airlines_id = ar.airlines_id
                WHERE izin_request_st = '0' AND izin_flight = ? AND input_by = 'operator'
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

    // DELETE REGISTRASI
    function delete_registrasi($params) {
        $sql = "DELETE FROM izin_registrasi WHERE registrasi_id = ? AND izin_completed = '0' AND input_by = 'operator'";
        return $this->db->query($sql, $params);
    }

    // DELETE RUTE BY REGISTRASI
    function delete_rute_by_registrasi($params) {
        $sql = "DELETE FROM izin_rute WHERE registrasi_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // create new request
    function insert_registrasi($params) {
        return $this->db->insert('izin_registrasi', $params);
    }

    // update
    function update_registrasi($params, $where) {
        return $this->db->update('izin_registrasi', $params, $where);
    }

    // update
    function update_izin_registrasi($params, $where) {
        return $this->db->update('izin_registrasi', $params, $where);
    }

    // get detail registrasi by id
    function get_registrasi_waiting_by_id($params) {
        $sql = "SELECT a.*, pax_cargo'input_pax_cargo', airlines_nm,
                group_nm, c.operator_name
                FROM izin_registrasi a
                INNER JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN airlines ar ON a.airlines_id = ar.airlines_id
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE registrasi_id = ? AND izin_request_st = '0' AND input_by = 'operator'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list airlines
    function get_list_airlines_have_rute($params) {
        $sql = "SELECT a.airlines_id, airlines_nm, airlines_iata_cd
                FROM airlines a 
                INNER JOIN izin_rute b ON a.airlines_id = b.airlines_id
                WHERE b.izin_active = '1'
                GROUP BY a.airlines_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute aktif by rute
    function get_list_izin_rute_all_by_airlines($params) {
        $sql = "SELECT 
                izin_rute_start, izin_rute_end, 
                MIN(izin_start_date)'izin_start_date', MAX(izin_expired_date)'izin_expired_date',
                izin_season, pax_cargo
                FROM 
                (
                        SELECT a.kode_izin, a.izin_rute_start, a.izin_rute_end, 
                        a.izin_start_date, a.izin_expired_date,
                        izin_season, pax_cargo
                        FROM izin_rute a 
                        LEFT JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                        WHERE a.airlines_id = ? AND a.izin_completed = '1'
                        AND a.izin_approval = 'approved' AND a.izin_payment_st = '1' 
                        AND a.izin_flight = ?
                        AND a.izin_active = '1'
                        ORDER BY SUBSTRING(izin_season, 2, 2) DESC, SUBSTRING(izin_season, 1, 1) DESC
                ) result 
                GROUP BY izin_rute_start";
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

    // get list rute aktif by rute
    function get_detail_izin_rute_by_rute_all($params) {
        $sql = "SELECT 
                izin_rute_start, izin_rute_end, 
                MIN(izin_start_date)'izin_start_date', MAX(izin_expired_date)'izin_expired_date',
                izin_season, pax_cargo
                FROM 
                (
                        SELECT a.kode_izin, a.izin_rute_start, a.izin_rute_end, 
                        a.izin_start_date, a.izin_expired_date,
                        izin_season, pax_cargo
                        FROM izin_rute a 
                        LEFT JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                        WHERE a.airlines_id = ? AND a.izin_completed = '1'
                        AND a.izin_approval = 'approved' AND a.izin_payment_st = '1' 
                        AND a.izin_flight = ?
                        AND a.izin_active = '1'
                        AND a.izin_rute_start = ?
                        ORDER BY SUBSTRING(izin_season, 2, 2) DESC, SUBSTRING(izin_season, 1, 1) DESC
                ) result 
                GROUP BY izin_rute_start";
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

    // get list izin rute all
    function get_list_izin_rute_aktif_by_rute($params) {
        $sql = "SELECT izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, 
                izin_start_date, izin_expired_date, izin_penundaan_start, izin_penundaan_end,
                izin_rute_start, izin_rute_end, pairing, kode_izin, kode_frekuensi
                FROM izin_rute a
                WHERE a.izin_rute_start = ? AND a.airlines_id = ? 
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

    // get list izin data aktif by id
    function get_list_izin_data_existing_by_kode_frekuensi($params) {
        $sql = "SELECT rute_id, a.izin_id, rute_all, tipe, capacity, flight_no, etd, eta, doop, roon, 
                start_date, end_date, a.is_used_score, GET_FREKUENSI_FROM_DOS(doop)'frekuensi',
                kode_izin, kode_frekuensi
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.kode_frekuensi = ?
                AND b.izin_completed = '1'
                AND b.izin_approval = 'approved'
                AND b.izin_active = '1' 
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

    // get list izin rute waiting by id
    function get_list_izin_rute_waiting_by_id($params) {
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

    // get list izin data waiting by id
    function get_list_izin_data_waiting_by_id($params) {
        $sql = "SELECT rute_id, a.izin_id, rute_all, tipe, capacity, flight_no, etd, eta, doop, roon, 
                start_date, end_date, a.is_used_score, GET_FREKUENSI_FROM_DOS(doop)'frekuensi',
                kode_izin, kode_frekuensi
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
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

    // get list rute aktif
    function get_list_izin_rute_by_airlines($params) {
        $sql = "SELECT a.kode_izin, a.izin_rute_start, a.izin_rute_end, 
                MAX(a.izin_start_date)'izin_start_date', MAX(a.izin_expired_date)'izin_expired_date', 
                izin_season, pax_cargo, 
                CONCAT(
                IF(SUBSTRING(izin_season, 1, 1) = 'S', 'W', 'S'), 
                IF(SUBSTRING(izin_season, 1, 1) = 'S', SUBSTRING(izin_season, 2, 2), SUBSTRING(izin_season, 2, 2)+1)
                )'izin_season_next'
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE a.airlines_id = ? AND a.izin_completed = '1'
                AND a.izin_approval = 'approved' AND a.izin_payment_st = '1' AND a.izin_flight = ?
                AND a.izin_active = '1'
                GROUP BY kode_izin";
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

    // get list rute aktif by kode
    function get_detail_izin_rute_by_kode_izin($params) {
        $sql = "SELECT a.kode_izin, a.izin_rute_start, a.izin_rute_end, 
                MAX(a.izin_start_date)'izin_start_date', MAX(a.izin_expired_date)'izin_expired_date', 
                izin_season, pax_cargo, 
                CONCAT(
                IF(SUBSTRING(izin_season, 1, 1) = 'S', 'W', 'S'), 
                IF(SUBSTRING(izin_season, 1, 1) = 'S', SUBSTRING(izin_season, 2, 2), SUBSTRING(izin_season, 2, 2)+1)
                )'izin_season_next'
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE a.airlines_id = ? AND a.izin_completed = '1'
                AND a.izin_approval = 'approved' AND a.izin_flight = ?
                AND a.izin_active = '1' AND a.kode_izin = ?
                GROUP BY kode_izin";
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
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, 
                izin_start_date, izin_expired_date, izin_penundaan_start, izin_penundaan_end,
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

    // get total frekuensi existing by kode_izin
    function get_total_frekuensi_existing_by_kode_izin_v2($params) {
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

    // get list izin rute by kode frekuensi
    function get_detail_izin_rute_aktif_by_kode_frekuensi($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE a.kode_frekuensi = ? AND a.airlines_id = ? 
                AND a.izin_completed = '1'
                AND a.izin_approval = 'approved'
                AND a.izin_active = '1'
                ORDER BY izin_rute_start ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // DELETE RUTE
    function delete_izin_rute($where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->delete('izin_rute', $where);
    }

    // INSERT RUTE
    function insert_izin_rute($params) {
        // execute
        return $this->db->insert('izin_rute', $params);
    }

    // INSERT DATA RUTE
    function insert_izin_data($params) {
        // execute
        return $this->db->insert('izin_data', $params);
    }

    // get list files references
    function get_list_file_required($params) {
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

    /*
     * FILES
     */

    // get list files by id
    function get_list_data_files_by_id($params) {
        $sql = "SELECT a.* FROM izin_file_pencabutan a
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

    // get detail files  by id
    function get_detail_files_by_id($params) {
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

    // INSERT FILES
    function insert_files($params) {
        // execute
        return $this->db->insert('izin_file_pencabutan', $params);
    }

    // UPDATE FILES
    function update_files($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_file_pencabutan', $params);
    }

    // DELETE FILES
    function delete_files($params) {
        $sql = "DELETE a.* FROM izin_file_pencabutan a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE letter_id = ? AND a.registrasi_id = ?";
        return $this->db->query($sql, $params);
    }

    // INSERT PROCESS
    function insert_izin_process($params) {
        // execute
        return $this->db->insert('izin_process', $params);
    }

}
