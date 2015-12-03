<?php

class m_pending extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get detail group
    function get_detail_group_by_id($params) {
        $sql = "SELECT * FROM fa_group WHERE group_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail by id
    function get_detail_data_by_id($params) {
        $sql = "SELECT * FROM fa_data WHERE data_id = ? AND airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail data
    function get_data_by_id($params) {
        $sql = "SELECT a.*, airlines_nm, process_id, services_nm, operator_name, b.catatan'notes', 
                c.group_id, group_link
                FROM fa_data a
                INNER JOIN fa_process b ON a.data_id = b.data_id 
                INNER JOIN fa_flow c ON b.flow_id = c.flow_id
                INNER JOIN airlines d ON a.airlines_id = d.airlines_id
                INNER JOIN services_code sc ON a.services_cd = sc.services_cd
                INNER JOIN fa_group e ON c.group_id = e.group_id
                LEFT JOIN com_user u ON a.mdb = u.user_id
                WHERE a.data_st = 'waiting' AND a.data_id = ? AND c.flow_id IN (91, 92, 93, 94, 95, 96)
                AND a.airlines_id = ? AND b.action_st = 'process'
                ORDER BY a.mdd ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get revision
    function get_revision($params) {
        $sql = "SELECT * 
            FROM fa_process 
            WHERE data_id = ? AND process_st = 'reject' 
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

    // update process
    function action_update($params) {
        $sql = "UPDATE fa_process SET process_st = ?, action_st = ? , mdb_finish = ?, mdd_finish = NOW()          
                WHERE process_id = ?";
        return $this->db->query($sql, $params);
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

    // get document id berjadwal dom dan int
    function get_document_number_berjadwal($document_code) {
        if ($document_code == 'AUNBDN') {
            $data_flight = 'domestik';
        } else {
            $data_flight = 'internasional';
        }
        $sql = "SELECT SUBSTRING(document_no, -5)'last_number',
                RIGHT(SUBSTRING_INDEX(document_no, '.', 2), 2)'tahun'
                FROM fa_data 
                WHERE data_type = 'berjadwal' AND data_flight = ?
                AND RIGHT(SUBSTRING_INDEX(document_no, '.', 2), 2) = SUBSTRING(YEAR(CURRENT_DATE), 3, 2)
                ORDER BY SUBSTRING(document_no, -5) DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql, $data_flight);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < 5; $i++) {
                $zero .= '0';
            }
            return $document_code . '.' . $result['tahun'] . '.' . $zero . $number;
        } else {
            // create new number
            return $document_code . '.' . date('y') . '.' . '00001';
        }
    }

    // get document id tidak berjadwal dan non niaga dom dan int
    function get_document_number_tidak_berjadwal($document_code) {
        if ($document_code == 'AUNTBDN') {
            $data_flight = 'domestik';
        } else {
            $data_flight = 'internasional';
        }
        $sql = "SELECT SUBSTRING(document_no, -5)'last_number',
                RIGHT(SUBSTRING_INDEX(document_no, '.', 2), 2)'tahun'
                FROM fa_data 
                WHERE data_type = 'tidak berjadwal' AND data_flight = ?
                AND RIGHT(SUBSTRING_INDEX(document_no, '.', 2), 2) = SUBSTRING(YEAR(CURRENT_DATE), 3, 2)
                ORDER BY SUBSTRING(document_no, -5) DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql, $data_flight);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < 5; $i++) {
                $zero .= '0';
            }
            return $document_code . '.' . $result['tahun'] . '.' . $zero . $number;
        } else {
            // create new number
            return $document_code . '.' . date('y') . '.' . '00001';
        }
    }

    // get document id non niaga dom dan int
    function get_document_number_bukan_niaga($document_code) {
        if ($document_code == 'AUBNDN') {
            $data_flight = 'domestik';
        } else {
            $data_flight = 'internasional';
        }
        $sql = "SELECT SUBSTRING(document_no, -5)'last_number',
                RIGHT(SUBSTRING_INDEX(document_no, '.', 2), 2)'tahun'
                FROM fa_data 
                WHERE data_type = 'bukan niaga' AND data_flight = ?
                AND RIGHT(SUBSTRING_INDEX(document_no, '.', 2), 2) = SUBSTRING(YEAR(CURRENT_DATE), 3, 2)
                ORDER BY SUBSTRING(document_no, -5) DESC
                LIMIT 0, 1";
        $query = $this->db->query($sql, $data_flight);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create next number
            $number = intval($result['last_number']) + 1;
            $zero = '';
            for ($i = strlen($number); $i < 5; $i++) {
                $zero .= '0';
            }
            return $document_code . '.' . $result['tahun'] . '.' . $zero . $number;
        } else {
            // create new number
            return $document_code . '.' . date('y') . '.' . '00001';
        }
    }

    // get all service code
    function get_all_service_code() {
        $sql = "SELECT * FROM services_code";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all data airport
    function get_all_airport() {
        $sql = "SELECT * FROM airport ORDER BY airport_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get all data airlines
    function get_all_airlines() {
        $sql = "SELECT * FROM airlines ORDER BY airlines_nm ASC";
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
    function create($params) {
        // data_id, data_st, data_type, airlines_id, mdb, mdd
        // data id
        $data_id = $this->get_data_id();
        // insert
        $sql = "INSERT INTO fa_data (data_id, data_st, data_type, airlines_id, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, NOW())";
        if ($this->db->query($sql, array(
                    $data_id,
                    $params['data_st'],
                    $params['data_type'],
                    $params['airlines_id'],
                    $params['mdb']
                ))) {
            // return
            return $data_id;
        } else {
            return false;
        }
    }

    // create new request tidak berjadwal dan non niaga
    function create_group_data($params) {
        // data_id, data_st, data_type, airlines_id, mdb, mdd
        // data id
        $data_id = $this->get_data_id();
        // insert
        $sql = "INSERT INTO fa_data (data_id, data_st, data_type, airlines_id, 
                registration_code, registration_total, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        if ($this->db->query($sql, array(
                    $data_id,
                    $params['data_st'],
                    $params['data_type'],
                    $params['airlines_id'],
                    $data_id,
                    1,
                    $params['mdb']
                ))) {
            // return
            return $data_id;
        } else {
            return false;
        }
    }

    // get list open
    function get_list_registration_open($params) {
        $sql = "SELECT a.*, services_nm,
                DATEDIFF(CURDATE(), mdd) AS selisih_hari, 
                TIMEDIFF(CURTIME(), SUBSTR(mdd, 12, 8)) AS selisih_waktu
                FROM fa_data a
                LEFT JOIN services_code b ON a.services_cd = b.services_cd
                WHERE data_st = 'open' AND data_type = ? AND airlines_id = ?
                ORDER BY mdd ASC";
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
        $sql = "SELECT * FROM fa_files WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get fa belum terbayar
    function get_fa_unpaid($params) {
        $sql = "SELECT COUNT(*)'total' FROM fa_data WHERE airlines_id = ? AND payment_due_date < ? AND published_date != '' AND data_st != 'rejected' AND payment_st = '00'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return array();
        }
    }

    // update fa
    function update($params, $where) {
        $this->db->where($where);
        return $this->db->update('fa_data', $params);
    }

    // delete process
    function delete($params) {
        $sql = "DELETE FROM fa_data WHERE data_id = ? AND airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert files process
    function insert_files($params) {
        $sql = "INSERT INTO fa_files (file_id, data_id, file_title, mdd)
                VALUES (?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // update files process
    function update_files($params) {
        $sql = "UPDATE fa_files SET file_path = ?
                WHERE file_id = ?";
        return $this->db->query($sql, $params);
    }

    // update rute process
    function update_rute($params) {
        return $this->db->insert_batch('fa_data_rute', $params);
    }

    // delete files process
    function delete_all_files($params) {
        $sql = "DELETE a.* FROM fa_files a
                INNER JOIN fa_data b On a.data_id = b.data_id
                WHERE a.data_id = ? AND b.airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete rute process
    function delete_rute($params) {
        $sql = "DELETE FROM fa_data_rute WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // update status data
    function update_status_data($params) {
        $sql = "UPDATE fa_data SET data_st = ?, document_no = ?
                WHERE data_id = ? AND airlines_id = ?";
        return $this->db->query($sql, $params);
    }

    // cancel pengajuan
    function cancel($params, $where) {
        return $this->db->update('fa_data', $params, $where);
    }

    // add process
    function insert_process($params) {
        $sql = "INSERT INTO fa_process (process_id, data_id, flow_id, mdb, mdd)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // insert duplilasi
    function insert_data_tidak_berjadwal($params) {
        $sql = "INSERT INTO fa_data (data_id, data_completed, data_st, data_type, 
                data_flight, document_no, airlines_id, aircraft_type, registration, 
                country, flight_no, rute_all, rute_from, rute_to, flight_purpose, 
                technical_landing, niaga_landing, overnight_landing, date_start, 
                date_start_upto, date_end, date_end_upto, waktu, flight_pilot, flight_crew, 
                flight_goods, remark, services_cd, catatan, applicant, designation, 
                registration_code, registration_total, mdb, mdd)
                SELECT ?, data_completed, data_st, data_type, 
                data_flight, ?, airlines_id, aircraft_type, registration, 
                country, flight_no, rute_all, rute_from, rute_to, flight_purpose, 
                technical_landing, niaga_landing, overnight_landing, date_start, 
                date_start_upto, date_end, date_end_upto, waktu, flight_pilot, flight_crew, 
                flight_goods, remark, services_cd, catatan, applicant, designation, 
                registration_code, registration_total, mdb, mdd FROM fa_data 
                WHERE data_id = ?";
        return $this->db->query($sql, $params);
    }

    // get minimal hari pengajuan
    function get_hari_pengajuan($params) {
        $sql = "SELECT * FROM fa_rules_registration WHERE data_type = ? AND data_flight = ? AND services_cd = ?";
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

    // get detail rute data
    function get_data_rute_by_id($params) {
        $sql = "SELECT a.*, b.airport_iata_cd 
            FROM fa_data_rute a 
            LEFT JOIN airport b ON b.airport_id = a.airport_id
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    /* ---------- ajax services code ---------- */

    // get services cd
    function get_services_cd($params) {
        $sql = "SELECT a.*, b.services_cd, b.services_nm 
            FROM fa_rules_services a 
            LEFT JOIN services_code b ON b.services_cd = a.services_cd 
            WHERE a.data_type = ? AND data_flight = ?";
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
