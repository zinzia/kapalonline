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
				AND mdd_finish IS NOT NULL
                ORDER BY mdd_finish DESC 
                LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return trim($result['catatan']);
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
                AND izin_completed = '0' 
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
                INNER JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id
                WHERE a.airlines_id = ? 
                AND (a.izin_rute_start = ? OR a.izin_rute_start = ? OR a.izin_rute_end = ? OR a.izin_rute_end = ?) 
                AND a.izin_completed = '1' AND a.izin_approval = 'approved' AND a.izin_st = 'pencabutan' AND (DATEDIFF(b.izin_published_date, NOW()) <= 365) AND (b.izin_group = '7' OR b.izin_group = '27') 
                AND b.input_by = 'operator'
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
        $sql = "SELECT a.izin_id, airlines_id, kode_izin, kode_frekuensi, registrasi_id, 
                izin_completed, izin_approval, izin_type, izin_flight, izin_st, izin_start_date, izin_expired_date, 
                izin_rute_start, izin_rute_end, pairing, 
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

    // get list rute aktif by kode
    function get_flight_no_izin_rute_by_kode_frekuensi($params) {
        $sql = "SELECT flight_no
                FROM izin_rute a 
                INNER JOIN izin_data b ON a.izin_id = b.izin_id
                WHERE a.airlines_id = ? AND a.izin_completed = '1'
                AND a.izin_approval = 'approved' AND a.izin_flight = ?
                AND a.izin_active = '1' AND a.kode_frekuensi = ?
                GROUP BY b.flight_no";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            $data = array();
            foreach ($result as $rec) {
                $data[] = $rec['flight_no'];
            }
            return $data;
        } else {
            return array();
        }
    }

    // get list rute aktif by kode frekuensi
    function get_detail_izin_rute_by_id($params) {
        $sql = "SELECT * FROM izin_rute WHERE izin_id = ?";
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

    // get flight no izin data aktif by id
    function get_flight_no_izin_data_existing_by_kode_frekuensi($params) {
        $sql = "SELECT flight_no
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.kode_frekuensi = ?
                AND b.izin_completed = '1'
                AND b.izin_approval = 'approved'
                AND b.izin_active = '1'
                GROUP BY flight_no";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            $flight_no = array(
                '1' => '',
                '2' => '',
            );
            $i = 1;
            foreach ($result as $data) {
                $flight_no[$i++] = $data['flight_no'];
            }
            return $flight_no;
        } else {
            $flight_no = array(
                '1' => '',
                '2' => '',
            );
            return $flight_no;
        }
    }

    // get list izin rute by kode izin
    function get_list_izin_rute_aktif_by_kode_izin($params) {
        $sql = "SELECT a.izin_id, a.airlines_id, a.kode_izin, a.kode_frekuensi, a.registrasi_id, 
                a.izin_completed, a.izin_approval, a.izin_type, a.izin_flight, a.izin_st, 
                a.izin_rute_start, a.izin_rute_end, pairing, 
                MAX(b.izin_valid_start)'izin_start_date', MAX(b.izin_valid_end)'izin_expired_date',
                MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'total_frekuensi'
                FROM izin_rute a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                LEFT JOIN izin_data c On a.izin_id = c.izin_id 
                WHERE a.kode_izin = ? AND a.airlines_id = ?
                AND a.izin_completed = '1'
                AND a.izin_approval = 'approved'
                AND a.izin_active = '1'
                GROUP BY a.kode_frekuensi
                ORDER BY a.kode_frekuensi ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total frekuensi by kode_frekuensi
    function get_total_frekuensi_existing_by_kode_frekuensi($params) {
        $sql = "SELECT COUNT(izin_id)'total_rute', MIN(start_date)'start_date', MAX(end_date)'end_date', SUM(frekuensi)'frekuensi' 
                FROM 
                (
                        SELECT b.izin_id, MIN(start_date)'start_date', MAX(end_date)'end_date', MAX(GET_FREKUENSI_TOTAL(a.izin_id, flight_no))'frekuensi'
                        FROM izin_data a
                        INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                        WHERE b.kode_frekuensi = ? AND b.izin_completed = '1'
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

    /*
     * SLOT
     */

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

    // INSERT SLOT
    function insert_slot($params) {
        // execute
        return $this->db->insert('izin_slot_time', $params);
    }

    // UPDATE SLOT
    function update_slot($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_slot_time', $params);
    }

    // DELETE SLOT
    function delete_slot($params) {
        $sql = "DELETE a.* FROM izin_slot_time a
                INNER JOIN izin_registrasi b ON a.registrasi_id = b.registrasi_id
                WHERE slot_id = ? AND airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    /*
     * FILES ATTACHMENT
     */

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

    // get list files completed
    function is_file_completed($params) {
        $sql = "SELECT registrasi_id
                FROM izin_rules_files a
                INNER JOIN izin_file_references r ON a.ref_id = r.ref_id
                LEFT JOIN izin_files b ON a.ref_id = b.ref_id AND registrasi_id = ?
                WHERE a.group_id = ? AND a.data_flight = ? AND registrasi_id IS NULL 
                AND ref_required = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    // update files
    function update_files($delete, $insert) {
        // delete
        $sql = "DELETE FROM izin_files  WHERE registrasi_id = ? AND ref_id = ?";
        $this->db->query($sql, $delete);
        // insert
        $sql = "INSERT INTO izin_files (file_id, registrasi_id, file_path, file_name, ref_id, mdd)
                VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $insert);
    }

    // get list files completed
    function is_file_completed_int($params) {
        $sql = "SELECT registrasi_id
                FROM izin_rules_files a
                INNER JOIN izin_file_references r ON a.ref_id = r.ref_id
                LEFT JOIN izin_files b ON a.ref_id = b.ref_id AND registrasi_id = ?
                WHERE a.group_id = ? AND a.data_flight = ? AND registrasi_id IS NULL AND a.airlines_st = ?
                AND ref_required = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
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

    // check izin rute aktif selected
    function check_izin_rute_selected($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute
                WHERE kode_frekuensi = ?
                AND airlines_id = ?
                AND izin_approval = 'waiting'
                AND izin_active = '0'
                AND izin_st = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
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
    
    // get total izin rute terpilih
    function get_total_izin_rute_terpilih($params) {
        $sql = "SELECT izin_id FROM izin_rute a
                WHERE a.registrasi_id = ? AND a.airlines_id = ? AND a.izin_completed = '0'
                AND kode_izin IS NOT NULL AND kode_frekuensi IS NOT NULL";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }
    
    // check izin rute aktif by perpanjangan
    function check_izin_rute_by_perpanjangan($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM izin_rute
                WHERE izin_kode_old = ?
                AND airlines_id = ?
                AND izin_completed = '1'
                AND izin_approval = 'approved'
                AND izin_active = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }
}
