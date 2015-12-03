<?php

class m_dashboard_stakeholder extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get user stakeholder iata
    function get_user_stakeholder_iata($params) {
        $sql = "SELECT a.* 
            FROM airport a 
            LEFT JOIN com_user_bandara b ON b.airport_id = a.airport_id 
            WHERE b.user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task
    function get_list_berjadwal($params, $airport) {
        $function = "WHERE";
        $sql = "SELECT * 
            FROM (
                SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd ";
        foreach ($airport as $value) {
            $sql .= $function . " rute_all LIKE '%" . $value . "%' ";
            $function = "OR";
        }
        $sql .= " GROUP BY a.data_id)result 
                WHERE ((DATE(NOW()) BETWEEN DATE(result.date_start) AND DATE(result.date_end)) OR (DATE(NOW()) BETWEEN DATE(result.date_end) AND DATE(result.date_start)))
                AND published_no LIKE ? 
                AND airlines_nm LIKE ?
                AND data_type = 'berjadwal' 
                AND result.data_st = 'approved' 
                AND result.rute_all LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list my task
    function get_list_tidak_berjadwal($params, $airport) {
        $function = "WHERE";
        $sql = "SELECT * 
            FROM (
                SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd ";
        foreach ($airport as $value) {
            $sql .= $function . " rute_all LIKE '%" . $value . "%' ";
            $function = "OR";
        }
        $sql .= " GROUP BY a.data_id)result 
                WHERE ((DATE(NOW()) BETWEEN DATE(result.date_start) AND DATE(result.date_end)) OR (DATE(NOW()) BETWEEN DATE(result.date_end) AND DATE(result.date_start)))
                AND published_no LIKE ? 
                AND airlines_nm LIKE ?
                AND (data_type = 'tidak berjadwal' OR data_type = 'bukan niaga') 
                AND result.data_st = 'approved' 
                AND result.rute_all LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total report
    function get_total_report($airport, $params) {
        $function = "WHERE";
        $sql = "SELECT COUNT(*)'total' 
            FROM 
            (
                SELECT * 
                FROM (
                SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd ";
        foreach ($airport as $value) {
            $sql .= $function . " rute_all LIKE '%" . $value . "%' ";
            $function = "OR";
        }
        $sql .= " GROUP BY a.data_id)result 
                WHERE (? BETWEEN date_start AND date_end OR ? BETWEEN date_start AND date_end OR ? BETWEEN date_end AND date_start OR ? BETWEEN date_end AND date_start)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type = ?
                AND data_flight LIKE ?
                AND airlines_nm LIKE ?
                AND services_cd LIKE ?
                AND result.data_st = 'approved'
            )result";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list my task
    function get_list_fa($airport, $params) {
        $function = "WHERE";
        $sql = "SELECT * 
            FROM (
                SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd ";
        foreach ($airport as $value) {
            $sql .= $function . " rute_all LIKE '%" . $value . "%' ";
            $function = "OR";
        }
        $sql .= " GROUP BY a.data_id)result 
                WHERE (? BETWEEN date_start AND date_end OR ? BETWEEN date_start AND date_end OR ? BETWEEN date_end AND date_start OR ? BETWEEN date_end AND date_start)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type = ?
                AND data_flight LIKE ?
                AND airlines_nm LIKE ?
                AND services_cd LIKE ?
                AND result.data_st = 'approved' 
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

    // get list report
    function get_all_data_report($airport, $params) {
        $function = "WHERE";
        $sql = "SELECT * 
            FROM (
                SELECT a.*, airlines_nm, e.services_nm 
                FROM fa_data a 
                LEFT JOIN airlines b ON a.airlines_id = b.airlines_id
                LEFT JOIN fa_process c ON c.data_id = a.data_id 
                LEFT JOIN fa_flow d ON d.flow_id = c.flow_id 
                LEFT JOIN services_code e ON e.services_cd = a.services_cd ";
        foreach ($airport as $value) {
            $sql .= $function . " rute_all LIKE '%" . $value . "%' ";
            $function = "OR";
        }
        $sql .= " GROUP BY a.data_id)result 
                WHERE (? BETWEEN date_start AND date_end OR ? BETWEEN date_start AND date_end OR ? BETWEEN date_end AND date_start OR ? BETWEEN date_end AND date_start)
                AND (published_no LIKE ? OR document_no LIKE ?)
                AND data_type = ?
                AND data_flight LIKE ?
                AND airlines_nm LIKE ?
                AND services_cd LIKE ?
                AND result.data_st = 'approved'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail by data id
    function get_detail_data_by_id($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.operator_name, c.jabatan, d.services_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id 
            LEFT JOIN com_user c ON c.user_id = a.mdb_finish 
            LEFT JOIN services_code d ON d.services_cd = a.services_cd
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
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

    // chart
    function get_data_chart_fa($params) {
        $sql = "SELECT MONTH(a.mdd)'bulan', COUNT(data_id)'total'
                FROM fa_data a
                WHERE a.data_st <> 'open' AND data_type = ? AND rute_all LIKE ?
                GROUP BY YEAR(a.mdd), MONTH(a.mdd)";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update
    function update($params, $where) {
        return $this->db->update("fa_data", $params, $where);
    }

    // get list airlines
    function get_list_airlines() {
        $sql = "SELECT * 
            FROM airlines
            ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list services_cd
    function get_list_services() {
        $sql = "SELECT * 
            FROM services_code
            ORDER BY services_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
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

    /* ================================ IZIN RUTE ================================ */

    // get list rute
    function get_list_rute($params, $airport) {
        $function = "WHERE";
        $sql = "SELECT a.*, b.izin_rute_start, d.airlines_nm, e.group_nm, e.group_alias, c.start_date, c.end_date, c.tipe, c.capacity, c.roon 
            FROM izin_registrasi a 
            LEFT JOIN izin_rute b ON b.registrasi_id = a.registrasi_id 
            LEFT JOIN izin_data c ON c.izin_id = b.izin_id 
            LEFT JOIN airlines d ON d.airlines_id = a.airlines_id 
            LEFT JOIN izin_group e ON e.group_id = a.izin_group ";
        foreach ($airport as $value) {
            $sql .= $function . " b.izin_rute_start LIKE '%" . $value . "%' ";
            $function = "OR";
        }
        $sql .= "AND a.izin_approval = 'approved' AND b.izin_active = '1' AND a.izin_published_letter LIKE ? AND d.airlines_nm LIKE ? AND b.izin_rute_start LIKE ? AND a.izin_flight = ? 
            GROUP BY a.registrasi_id";
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
