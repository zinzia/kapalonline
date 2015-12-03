<?php

class m_pencabutan extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get file id
    function get_file_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get data id
    function get_data_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get process id
    function get_process_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get list draft
    function get_list_draft_registration() {
        $sql = "SELECT a.*, group_nm, group_alias, airlines_nm, b.group_short_link
                FROM izin_registrasi a
                LEFT JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN airlines c ON a.airlines_id = c.airlines_id
                WHERE input_by = 'operator' AND izin_request_st = '0'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // create new request
    function create_group_data($params) {
        $data_id = $this->get_data_id();
        // insert
        $sql = "INSERT INTO izin_registrasi (registrasi_id, izin_flight, izin_group, input_by, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, NOW())";
        if ($this->db->query($sql, array(
                    $data_id,
                    $params['izin_flight'],
                    $params['izin_group'],
                    $params['input_by'],
                    $params['mdb'],
                ))) {
            // return
            return $data_id;
        } else {
            return false;
        }
    }

    // delete process
    function delete_izin($params) {
        $sql = "DELETE FROM izin_registrasi WHERE registrasi_id = ? AND izin_request_st = '0' AND input_by = 'operator'";
        return $this->db->query($sql, $params);
    }

    // DELETE RUTE BY REGISTRASI
    function delete_rute_by_registrasi($params) {
        $sql = "DELETE FROM izin_rute WHERE registrasi_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // delete izin rute
    function delete_izin_rute($params) {
        $sql = "DELETE FROM izin_rute WHERE izin_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // get detail by id
    function get_registrasi_by_id($params) {
        $sql = "SELECT a.*, group_nm, airlines_nm
                FROM izin_registrasi a
                LEFT JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN airlines c ON a.airlines_id = c.airlines_id
                WHERE registrasi_id = ? AND izin_request_st = '0' AND izin_group = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list izin rute
    function get_list_izin_rute($params) {
        $sql = "SELECT airlines_nm, kode_izin, izin_rute_start, izin_rute_end, izin_expired_date
                FROM izin_rute a 
                INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                WHERE a.izin_completed = '1' AND a.izin_approval = 'approved' 
                AND a.izin_active = '1' AND a.izin_flight = ?
                GROUP BY kode_izin
                ORDER BY airlines_nm ASC, kode_izin ASC";
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

    // get detail kode izin by registrasi
    function get_kode_izin_by_registrasi($params) {
        $sql = "SELECT kode_izin, izin_expired_date FROM izin_rute WHERE registrasi_id = ?";
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

    // get detail izin rute by id waiting on proses
    function get_total_rute_process_by_kode_izin($params) {
        $sql = "SELECT COUNT(*)'total' FROM izin_registrasi 
                WHERE kode_izin = ? AND izin_completed = '0' AND registrasi_id <> ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get detail by number
    function get_list_rute_by_kode_izin($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE kode_izin = ? AND a.izin_completed = '1' 
                AND a.izin_approval = 'approved' AND izin_active = '1'
                ORDER BY izin_expired_date DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // INSERT RUTE
    function insert_rute($params) {
        // execute
        return $this->db->insert('izin_rute', $params);
    }

    // DELETE RUTE DATA
    function delete_rute_data($params) {
        $sql = "DELETE a.* FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE a.izin_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // get detail data by id izin
    function get_detail_rute_by_id($params) {
        $sql = "SELECT * FROM izin_data WHERE izin_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // INSERT RUTE DATA
    function insert_rute_data($params) {
        // execute
        return $this->db->insert('izin_data', $params);
    }

    // UPDATE
    function update_izin_permohonan($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_registrasi', $params);
    }

    // get list rute by id
    function get_list_data_rute_by_id($params) {
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
                WHERE b.registrasi_id = ? AND b.izin_completed = '0'
                ORDER BY kode_frekuensi ASC";
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

    // get_rute_by_kode_izin
    function get_rute_by_kode_izin($params) {
        $sql = "SELECT izin.*, MIN(rute_all)'izin_rute_start', MAX(rute_all)'izin_rute_end' FROM
                (
                        SELECT a.izin_id, kode_izin, kode_frekuensi, a.dos,
                        SUM(
                                IF(SUBSTRING(a.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 3, 1) = 0, 0, 1) +
                                IF(SUBSTRING(a.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(a.dos, 6, 1) = 0, 0, 1) +
                                IF(SUBSTRING(a.dos, 7, 1) = 0, 0, 1)
                        )'frekuensi', airlines_id
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

    // get list rute by kode izin
    function get_list_data_rute_by_kode_izin($params) {
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
                WHERE b.kode_izin = ? AND b.izin_active = '1'
                ORDER BY kode_frekuensi ASC";
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

    // get detail rute by kode frekuensi
    function get_detail_rute_by_kode_frekuensi($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE kode_frekuensi = ? AND a.izin_completed = '1' 
                AND a.izin_approval = 'approved' AND izin_active = '1'
                ORDER BY izin_expired_date DESC
                LIMIT 1";
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
                WHERE b.group_id = ? AND b.data_flight = ? AND a.ref_id = '1'";
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

    // update files
    function update_files($delete, $insert) {
        // delete
        $sql = "DELETE FROM izin_files  WHERE registrasi_id = ? AND ref_id = ?";
        $this->db->query($sql, $delete);
        // insert
        $sql = "INSERT INTO izin_files (file_id, registrasi_id, file_path, file_name, ref_id, file_check, check_by, check_date, mdd)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        return $this->db->query($sql, $insert);
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

    // get list files completed
    function is_file_completed($params) {
        $sql = "SELECT registrasi_id
                FROM izin_rules_files a
                INNER JOIN izin_file_references r ON a.ref_id = r.ref_id
                LEFT JOIN izin_files b ON a.ref_id = b.ref_id AND registrasi_id = ?
                WHERE a.group_id = ? AND a.data_flight = ? AND registrasi_id IS NULL AND b.ref_id = '1' 
                AND ref_required = '1'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    // add process
    function insert_process($params) {
        $sql = "INSERT INTO izin_process (process_id, registrasi_id, flow_id, mdb, mdd)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // update status data
    function update_status_data($params) {
        $sql = "UPDATE izin_registrasi SET izin_request_st = ?, izin_request_by = ?, izin_request_date = NOW()
                WHERE registrasi_id = ?";
        return $this->db->query($sql, $params);
    }

    /* PENGURANGAN FREKUENSI */

    // get list editorial uploaded
    function get_list_editorial($params) {
        $sql = "SELECT * 
            FROM (
                SELECT a.pref_id, a.pref_group, a.pref_nm, a.pref_value 
                FROM com_preferences a 
                WHERE a.pref_group = 'published_izin' AND (a.pref_nm = 'tembusan' OR a.pref_nm = 'kepada') 
                GROUP BY a.pref_id 
                ORDER BY a.pref_id ASC 
            )rs1
            LEFT JOIN 
            (
                SELECT b.*, c.operator_name 
                FROM izin_tembusan b 
                LEFT JOIN com_user c ON c.user_id = b.tembusan_by
                WHERE b.registrasi_id = ?
            )rs2 ON rs2.tembusan_value = rs1.pref_value
            ORDER BY rs1.pref_nm ASC, rs1.pref_value ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get izin rute by kode frekuensi
    function get_izin_rute_by_kode_frekuensi_active($params) {
        $sql = "SELECT *,
                (
                IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(dos, 7, 1) = 0, 0, 1)
                )'frekuensi'
                FROM izin_rute WHERE kode_frekuensi = ? AND izin_active = '1'";
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

    // get detail izin rute by kode frekuensi
    function get_izin_rute_by_kode_frekuensi($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE a.kode_frekuensi = ? AND a.izin_completed = '0' AND izin_st = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail izin rute by id
    function get_izin_rute_data_by_id($params) {
        $sql = "SELECT a.*, 
                (
                IF(SUBSTRING(b.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.izin_id = ? AND b.izin_completed = '0'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // DELETE RUTE BY REGISTRASI AND KODE FREKUENSI
    function delete_rute_by_registrasi_kode_frekuensi($params) {
        $sql = "DELETE FROM izin_rute WHERE registrasi_id = ? AND kode_frekuensi = ?
                AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // get pref value
    function get_pref_value($params) {
        $sql = "SELECT * 
            FROM com_preferences 
            WHERE pref_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['pref_value'];
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

}
