<?php

class m_pending_izin extends CI_Model {

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

    // get revision
    function get_revision($params) {
        $sql = "SELECT * 
            FROM izin_process 
            WHERE registrasi_id = ? AND process_st = 'reject' AND catatan != '' 
            ORDER BY mdd DESC 
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

    // get list my task
    function get_list_pending_task_waiting($params) {
        $sql = "SELECT c.*, group_nm, airlines_nm, task_link, d.group_alias,
                DATEDIFF(CURDATE(), a.mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(a.mdd, 12, 8)) AS selisih_waktu,
                a.mdd'tgl_kembali'
                FROM izin_process a
                INNER JOIN izin_flow b ON a.flow_id = b.flow_id
                INNER JOIN izin_registrasi c ON a.registrasi_id = c.registrasi_id
                INNER JOIN izin_group d ON c.izin_group = d.group_id
                INNER JOIN airlines e ON c.airlines_id = e.airlines_id
                WHERE b.role_id = ? AND c.airlines_id = ?
                AND action_st = 'process' AND c.izin_completed = '0' AND c.izin_request_st = '1'";
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
        $sql = "SELECT a.*, a.mdd'tgl_kembali', group_nm, p.process_id
                FROM izin_registrasi a
                INNER JOIN izin_process p ON a.registrasi_id = p.registrasi_id
                INNER JOIN izin_flow r ON p.flow_id = r.flow_id
                LEFT JOIN izin_group b ON a.izin_group = b.group_id
                WHERE a.registrasi_id = ? AND a.airlines_id = ? 
                AND izin_completed = '0' AND izin_request_st = '1' 
                AND izin_group = ? AND r.role_id = ? AND action_st = 'process'";
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
        $sql = "SELECT a.*, group_nm, c.process_id
                FROM izin_registrasi a
                LEFT JOIN izin_group b ON a.izin_group = b.group_id 
                LEFT JOIN izin_process c ON c.registrasi_id = a.registrasi_id
                WHERE a.registrasi_id = ? AND airlines_id = ? AND izin_request_st = '1' AND izin_group = ? AND c.process_st = 'waiting'";
        $query = $this->db->query($sql, $params);

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }


    // get list notes airlines
    function get_list_notes_by_izin($params) {
        $sql = "SELECT a.*, operator_name 
                FROM izin_notes a
                LEFT JOIN com_user b On a.note_by = b.user_id
                WHERE registrasi_id = ?
                ORDER BY note_date DESC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
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
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
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

    // get detail izin rute by kode frekuensi
    function get_izin_rute_by_kode_frekuensi($params) {
        $sql = "SELECT a.*
                FROM izin_rute a
                WHERE a.kode_frekuensi = ? AND airlines_id = ? AND a.izin_completed = '0' AND izin_st = ?";
        $query = $this->db->query($sql, $params);
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

    // delete izin rute
    function delete_izin_rute($params) {
        $sql = "DELETE FROM izin_rute WHERE izin_id = ? AND airlines_id = ? AND izin_completed = '0'";
        return $this->db->query($sql, $params);
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
        $this->db->query($sql, $params);
        return $this->db->last_query();
    }

    // update status data
    function update_status_data($params) {
        $sql = "UPDATE izin_registrasi SET izin_request_st = ?, izin_request_by = ?, izin_request_date = NOW()
                WHERE registrasi_id = ? AND airlines_id = ?";
        return $this->db->query($sql, $params);
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

    // cancel pengajuan
    function cancel($params, $where) {
        return $this->db->update('izin_registrasi', $params, $where);
    }

}
