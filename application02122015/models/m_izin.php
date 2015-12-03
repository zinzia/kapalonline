<?php

class m_izin extends CI_Model {

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

    // get list my task berjadwal
    function get_list_my_task_waiting_izin($params) {
        $sql = "SELECT a.*, airlines_nm, c.task_link,
                DATEDIFF(CURDATE(), a.izin_request_date) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.izin_request_date, 12, 8)) AS selisih_waktu
                FROM izin_rute a
                INNER JOIN izin_process b ON a.izin_id = b.izin_id 
                INNER JOIN izin_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                WHERE a.izin_st = 'waiting' AND a.airlines_id = ? AND a.izin_flight = ? AND b.action_st = 'process'
                ORDER BY a.izin_request_date ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list group request
    function get_list_group_by_flight($params) {
        $sql = "SELECT * FROM izin_group WHERE data_flight = ? AND group_st = 'show' ORDER BY group_id ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
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

    // create new request
    function create_group_data($params) {
        $data_id = $this->get_data_id();
        // insert
        $sql = "INSERT INTO izin_registrasi (registrasi_id, airlines_id, izin_flight, izin_group, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, NOW())";
        if ($this->db->query($sql, array(
                    $data_id,
                    $params['airlines_id'],
                    $params['izin_flight'],
                    $params['izin_group'],
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
        $sql = "DELETE FROM izin_registrasi WHERE registrasi_id = ? AND airlines_id = ? AND izin_request_st = '0' AND input_by = 'member'";
        return $this->db->query($sql, $params);
    }

    // delete izin rute
    function delete_izin_rute($params) {
        $sql = "DELETE FROM izin_rute WHERE izin_id = ? AND airlines_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // get list open
    function get_list_registration_open($params) {
        $sql = "SELECT a.*, group_nm, operator_name, group_alias, group_link,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu
                FROM izin_registrasi a
                LEFT JOIN izin_group b ON a.izin_group = b.group_id
                LEFT JOIN com_user c ON c.user_id = a.mdb
                WHERE izin_request_st = '0' AND izin_flight = ? AND airlines_id = ? AND input_by = 'member'
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

    // get detail by id
    function get_registrasi_by_id($params) {
        $sql = "SELECT a.*, group_nm
                FROM izin_registrasi a
                LEFT JOIN izin_group b ON a.izin_group = b.group_id
                WHERE registrasi_id = ? AND airlines_id = ? AND izin_request_st = '0' AND izin_group = ?";
        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get pending by id
    function get_pending_registrasi_by_id($params) {
        $sql = "SELECT a.*, group_nm
                FROM izin_registrasi a
                LEFT JOIN izin_group b ON a.izin_group = b.group_id
                WHERE registrasi_id = ? AND airlines_id = ? AND izin_request_st = '1' AND izin_group = ?";
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
    function get_izin_rute_by_id($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE a.izin_id = ? AND airlines_id = ? AND a.izin_completed = '0'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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
                WHERE a.kode_frekuensi = ? AND airlines_id = ? AND a.izin_completed = '0' AND izin_st = ?";
        if (count($params) == 4) {
            $sql .= " AND registrasi_id = ?";
        }
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail izin rute below 1 years
    function get_total_rute_existing_canceled($params) {
        $sql = "SELECT COUNT(*) 'total', (DATEDIFF(b.izin_published_date, NOW()) <= 365)'diff'
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id
                WHERE a.airlines_id = ? AND (a.izin_rute_start = ? OR a.izin_rute_start = ? OR a.izin_rute_end = ? OR a.izin_rute_end = ?) 
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

    // get detail izin rute by id
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

    // get detail izin rute by id waiting on proses
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
                WHERE b.izin_id = ? AND airlines_id = ? AND b.izin_completed = '0'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail by number
    function get_list_rute_by_kode_izin($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE kode_izin = ? AND airlines_id = ?  
                AND a.izin_completed = '1' AND a.izin_approval = 'approved' AND izin_active = '1'
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

    // get detail rute by kode frekuensi
    function get_detail_rute_by_kode_frekuensi($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE kode_frekuensi = ? AND airlines_id = ?  
                AND a.izin_completed = '1' AND a.izin_approval = 'approved' AND izin_active = '1'
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

    // UPDATE
    function update_izin_permohonan($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_registrasi', $params);
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
                WHERE a.izin_id = ? AND airlines_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // INSERT RUTE DATA
    function insert_rute_data($params) {
        // execute
        return $this->db->insert('izin_data', $params);
    }

    // UPDATE RUTE
    function update_rute($params, $where) {
        // where
        $this->db->where($where);
        // execute
        return $this->db->update('izin_rute', $params);
    }

    // DELETE RUTE BY REGISTRASI
    function delete_rute_by_registrasi($params) {
        $sql = "DELETE FROM izin_rute WHERE registrasi_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
    }

    // DELETE RUTE BY REGISTRASI AND KODE FREKUENSI
    function delete_rute_by_registrasi_kode_frekuensi($params) {
        $sql = "DELETE FROM izin_rute WHERE registrasi_id = ? AND kode_frekuensi = ?
                AND airlines_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
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
                izin_penundaan_start, izin_penundaan_end, b.selected,
                IF(c.code_id IS NOT NULL,COUNT(c.code_id),0) AS 'total_codeshare', b.notes
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                LEFT JOIN izin_codeshare c ON a.rute_id=c.izin_data_id
                WHERE b.registrasi_id = ? AND airlines_id = ? AND b.izin_completed = '0'
                GROUP BY a.rute_id ORDER BY rute_id ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute selected by id
    function get_list_data_rute_by_id_selected($params) {
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
                WHERE b.registrasi_id = ? AND airlines_id = ? AND b.izin_completed = '0' AND b.selected = ?
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
                WHERE b.kode_izin = ? AND airlines_id = ? AND b.izin_active = '1'
                ORDER BY kode_frekuensi ASC";
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

    // get izin rute by kode frekuensi
    function get_izin_rute_by_kode_frekuensi_active($params) {
        $sql = "SELECT *,
                (
                IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(dos, 7, 1) = 0, 0, 1)
                )'frekuensi'
                FROM izin_rute WHERE kode_frekuensi = ? AND airlines_id = ? AND izin_active = '1'";
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

    // update status data
    function update_status_data($params) {
        $sql = "UPDATE izin_registrasi SET izin_request_st = ?, izin_request_by = ?, izin_request_date = NOW()
                WHERE registrasi_id = ? AND airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    // add process
    function insert_process($params) {
        $sql = "INSERT INTO izin_process (process_id, registrasi_id, flow_id, mdb, mdd)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // get list izin rute
    function get_list_izin_rute_by_perusahaan($params) {
        $sql = "SELECT kode_izin, izin_rute_start, izin_rute_end, izin_expired_date
                FROM izin_rute a 
                WHERE airlines_id = ? AND a.izin_completed = '1' 
                AND a.izin_approval = 'approved' AND a.izin_flight = ?
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

    // get detail izin rute
    function get_detail_data_by_izin_number($params) {
        $sql = "SELECT izin.*, rute_all, flight_no, etd, eta 
                FROM 
                (
                        SELECT a.izin_id, izin_number, izin_expired_date, aircraft_type, aircraft_capacity, dos, ron, pairing
                        FROM izin_rute a 
                        WHERE airlines_id = ? AND a.izin_completed = '1' 
                        AND a.izin_st = 'approved' AND a.izin_number = ?
                        AND payment_st = '11'
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

    // update status into no
    function update_status_no($params, $where) {
        // $sql = "UPDATE izin_data a, (SELECT * FROM izin_rute WHERE registrasi_id = ?)b
        //     SET a.selected = 'no' WHERE b.registrasi_id = ?";
        return $this->db->update('izin_rute', $params, $where);
    }

    // update status into yes
    function update_status_yes($params, $where) {
        return $this->db->update('izin_rute', $params, $where);
    }

    // delete rute not selected
    function delete_rute_not_selected($params) {
        $sql = "DELETE FROM izin_rute 
            WHERE registrasi_id = ? AND selected = 'no'";
        return $this->db->query($sql, $params);
    }

    function get_detail_izin_data($rute_id = "") {
        $sql = "SELECT * FROM izin_data a INNER JOIN izin_rute b
                ON a.izin_id=b.izin_id INNER JOIN izin_registrasi c
                ON b.registrasi_id=c.registrasi_id
                WHERE a.rute_id = ?";
        $query = $this->db->query($sql, $rute_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function insert_codeshare($params) {
        $sql = "INSERT INTO izin_codeshare VALUES (?,?,?,?,?,?,NOW(),?)";
        return $this->db->query($sql, $params);
    }

    function update_codeshare($params) {
        $sql = "UPDATE izin_codeshare SET airlines_mkt = ?,mkt_cxr = ?,mdd=NOW(),mdb=? WHERE code_id = ?";
        return $this->db->query($sql, $params);
    }

    function delete_codeshare($params) {
        $sql = "DELETE FROM izin_codeshare WHERE code_id = ?";
        return $this->db->query($sql, $params);
    }

    function get_codeshare_by_data_id($data_id = "") {
        $sql = "SELECT a.code_id,a.izin_data_id,a.mkt_cxr,a.ope_cxr,
                b.rute_all,e.airlines_nm,b.etd,b.eta FROM izin_codeshare a 
                INNER JOIN izin_data b ON a.izin_data_id=b.rute_id
                INNER JOIN izin_rute c ON b.izin_id=c.izin_id
                INNER JOIN izin_registrasi d ON c.registrasi_id=d.registrasi_id
                INNER JOIN airlines e ON a.airlines_mkt=e.airlines_id
                WHERE a.izin_data_id=?";
        $query = $this->db->query($sql, $data_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_codeshare_by_reg_id($register_id = "") {
        $sql = "SELECT a.mkt_cxr,a.ope_cxr,b.rute_all,e.airlines_nm,b.etd,b.eta 
                FROM izin_codeshare a 
                INNER JOIN izin_data b ON a.izin_data_id=b.rute_id
                INNER JOIN izin_rute c ON b.izin_id=c.izin_id
                INNER JOIN izin_registrasi d ON c.registrasi_id=d.registrasi_id
                INNER JOIN airlines e ON a.airlines_mkt=e.airlines_id
                WHERE d.registrasi_id = ?";
        $query = $this->db->query($sql, $register_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_codeshare_by_code_id($code_id = "") {
        $sql = "SELECT a.*,b.rute_all,e.airlines_nm,b.etd,b.eta,
                d.registrasi_id,b.rute_id,d.izin_group
                FROM izin_codeshare a 
                INNER JOIN izin_data b ON a.izin_data_id=b.rute_id
                INNER JOIN izin_rute c ON b.izin_id=c.izin_id
                INNER JOIN izin_registrasi d ON c.registrasi_id=d.registrasi_id
                INNER JOIN airlines e ON a.airlines_mkt=e.airlines_id
                WHERE a.code_id=?";
        $query = $this->db->query($sql, $code_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function count_codeshare_by_rute_id() {
        $sql = "SELECT b.rute_id,COUNT(a.code_id) AS 'total_codeshare' FROM izin_codeshare a
                LEFT JOIN izin_data b ON a.izin_data_id=b.rute_id
                INNER JOIN airlines c ON a.airlines_mkt=c.airlines_id 
                GROUP BY b.rute_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_codeshare_by_izin_id($izin_id = "") {
        $sql = "SELECT 
                b.code_id,b.izin_data_id,b.mkt_cxr,b.ope_cxr,
                a.rute_all,d.airlines_nm,a.etd,a.eta
                FROM izin_data a 
                LEFT JOIN izin_codeshare b 
                ON a.rute_id=b.izin_data_id
                LEFT JOIN izin_rute c ON a.izin_id=c.izin_id
                LEFT JOIN airlines d ON b.airlines_mkt=d.airlines_id
                WHERE a.izin_id=?";
        $query = $this->db->query($sql, $izin_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // total perubahan frekuensi
    function get_total_perubahan_frekuensi($params) {
        // $sql = "SELECT rs1.range_waktu, IF(ISNULL(rs2.total), 0, rs2.total)'total', IF(rs1.range_waktu < 180 AND IF(ISNULL(rs2.total), 0, rs2.total) < 2, 'valid', 'reject')'status' 
        //     FROM 
        //     (
        //         SELECT a.izin_id, a.kode_izin, a.kode_frekuensi, a.registrasi_id, a.izin_approval, a.izin_type, a.izin_flight, a.izin_st, b.izin_published_date, DATEDIFF(NOW(), b.izin_published_date)'range_waktu' 
        //         FROM izin_rute a 
        //         LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id 
        //         WHERE (a.izin_st = 'baru' OR a.izin_st = 'perpanjangan') AND a.izin_approval = 'approved' AND a.kode_frekuensi = ?
        //         GROUP BY a.izin_st 
        //         ORDER BY b.izin_published_date DESC 
        //         LIMIT 1
        //     )rs1 
        //     LEFT JOIN 
        //     (
        //         SELECT rs2.kode_frekuensi, COUNT(*)'total' 
        //         FROM 
        //         (
        //             SELECT a.kode_frekuensi 
        //             FROM izin_rute a 
        //             LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id 
        //             WHERE a.izin_st = 'perubahan' AND a.izin_approval = 'approved' AND a.kode_frekuensi = ? 
        //             ORDER BY b.izin_published_date DESC
        //             LIMIT 2
        //         )rs2
        //     )rs2 ON rs2.kode_frekuensi = rs1.kode_frekuensi";
        $sql = "SELECT rs1.range_waktu, COUNT(*)'total', IF(rs1.range_waktu < 180 AND COUNT(*) < 2, 'valid', 'reject')'status' 
            FROM 
            (
                SELECT a.izin_id, a.kode_izin, a.kode_frekuensi, a.registrasi_id, a.izin_approval, a.izin_type, a.izin_flight, a.izin_st, b.izin_published_date, DATEDIFF(NOW(), b.izin_published_date)'range_waktu' 
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id 
                WHERE (a.izin_st = 'baru' OR a.izin_st = 'perpanjangan') AND a.izin_approval = 'approved' AND a.kode_frekuensi = ? 
                GROUP BY a.izin_st 
                ORDER BY b.izin_published_date DESC 
                LIMIT 1
            )rs1 
            LEFT JOIN 
            (
                SELECT a.kode_frekuensi, b.izin_published_date 
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id 
                WHERE a.izin_st = 'perubahan' AND a.izin_approval = 'approved' AND a.kode_frekuensi = ? 
                ORDER BY b.izin_published_date DESC
                LIMIT 2
            )rs2 ON rs2.kode_frekuensi = rs1.kode_frekuensi 
            WHERE DATEDIFF(rs2.izin_published_date, rs1.izin_published_date) > 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // total notamfrekuensi
    function get_total_notam_frekuensi($params) {
        $sql = "SELECT rs1.range_waktu, COUNT(*)'total', IF(rs1.range_waktu < 180 AND COUNT(*) < 2, 'valid', 'reject')'status' 
            FROM 
            (
                SELECT a.izin_id, a.kode_izin, a.kode_frekuensi, a.registrasi_id, a.izin_approval, a.izin_type, a.izin_flight, a.izin_st, b.izin_published_date, DATEDIFF(NOW(), b.izin_published_date)'range_waktu' 
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id 
                WHERE (a.izin_st = 'baru' OR a.izin_st = 'perpanjangan') AND a.izin_approval = 'approved' AND a.kode_frekuensi = ? 
                GROUP BY a.izin_st 
                ORDER BY b.izin_published_date DESC 
                LIMIT 1
            )rs1 
            LEFT JOIN 
            (
                SELECT a.kode_frekuensi, b.izin_published_date 
                FROM izin_rute a 
                LEFT JOIN izin_registrasi b ON b.registrasi_id = a.registrasi_id 
                WHERE a.izin_st = 'perubahan' AND a.izin_approval = 'approved' AND a.kode_frekuensi = ? 
                ORDER BY b.izin_published_date DESC
                LIMIT 2
            )rs2 ON rs2.kode_frekuensi = rs1.kode_frekuensi 
            WHERE DATEDIFF(rs2.izin_published_date, rs1.izin_published_date) > 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
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

    // get rute by rute & flight no & aircraft type & dos
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 4-Aug-2015
    // reason: to accommodate sync process with SCORE slot time database using SOA
    function get_list_rute_by_rute_flightno_aircraft_dos($params, $where = "") {
        $sql = "SELECT a.*,
                (
                IF(SUBSTRING(b.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id WHERE ((b.izin_approval = 'approved' AND b.izin_active = '1') OR b.izin_approval = 'waiting') AND a.rute_all = ? AND a.flight_no = ? AND b.aircraft_type = ?";
        if (count($params) == 4) {
            $sql .= " AND b.dos = ?";
        }
        if ($where != "") {
            $sql .= " AND " . $where;
        }
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by izin_id
    // add & modified by: sanjaya.im@gmail.com
    // modified on: 10-May-2015
    // reason: to accommodate sync process with SCORE slot time database using SOA
    function get_list_data_rute_by_izin_id($params) {
        $sql = "SELECT a.*, kode_izin, kode_frekuensi,
                (
                IF(SUBSTRING(b.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                izin_penundaan_start, izin_penundaan_end, b.selected, b.notes
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE a.izin_id = ? AND b.izin_completed = '0'
                GROUP BY a.rute_id ORDER BY rute_id ASC";
        $query = $this->db->query($sql, $params);
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

    function get_higher_dos($dos1, $dos2) {
        $frek1 = 0;
        $dd1 = str_split($dos1, 1);
        for ($x = 0; $x < count($dd1); $x++)
            if ($dd1[$x] != "0")
                $frek1++;
        $frek2 = 0;
        $dd2 = str_split($dos2, 1);
        for ($x = 0; $x < count($dd2); $x++)
            if ($dd2[$x] != "0")
                $frek2++;
        return ($frek1 > $frek2) ? $dos1 : $dos2;
    }

    /*====================== REVISI ======================*/
    function check_revisi($params) {
        $sql = "SELECT COUNT(*)'total' 
            FROM izin_process a 
            WHERE a.registrasi_id = ? AND a.flow_id = ? AND a.process_st = ? AND a.action_st = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    function update_status_registrasi($params, $where) {
        $this->db->update('izin_registrasi', $params, $where);
        return true;
    }

    function update_status_izin_rute($params, $where) {
        $this->db->update('izin_rute', $params, $where);
        return true;
    }

}
