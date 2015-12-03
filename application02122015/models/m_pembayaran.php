<?php

class m_pembayaran extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get detail task
    function get_detail_data_by_id($params) {
        $sql = "SELECT *
                FROM fa_data 
                WHERE data_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total task berjadwal
    function get_total_awaiting_task_berjadwal($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM 
                (
                    SELECT a.data_id
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    WHERE a.data_st = 'approved' AND payment_st = 'belum' AND published_no LIKE ? 
                    AND airlines_nm LIKE ? AND data_type = ? 
                    GROUP BY a.data_id
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

    // get list task berjadwal
    function get_list_awaiting_task_berjadwal($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_brand, airlines_nm
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    WHERE a.data_st = 'approved' AND payment_st = 'belum' AND published_no LIKE ? 
                    AND airlines_nm LIKE ? AND data_type = ? 
                    ORDER BY a.payment_due_date DESC
                ) result
                GROUP BY data_id
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

    // get total task selain berjadwal
    function get_total_awaiting_task_selain_berjadwal($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM 
                (
                    SELECT a.data_id
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    WHERE a.data_st = 'approved' AND payment_st = 'belum' AND published_no LIKE ? 
                    AND airlines_nm LIKE ? AND data_type != ? 
                    GROUP BY a.data_id
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

    // get list task selain berjadwal
    function get_list_awaiting_task_selain_berjadwal($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_brand, airlines_nm
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    WHERE a.data_st = 'approved' AND payment_st = 'belum' AND published_no LIKE ? 
                    AND airlines_nm LIKE ? AND data_type != ? 
                    ORDER BY a.payment_due_date DESC
                ) result
                GROUP BY data_id
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

    // get published no
    function get_published_no_detail($params) {
        $sql = "SELECT a.*, b.airlines_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            WHERE data_id = ? AND payment_st = 'belum' AND (ISNULL(payment_date) OR payment_date = '' OR payment_date = '0000-00-00 00:00:00')";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail invoice
    function get_detail_invoice($params) {
        $sql = "SELECT a.payment_st, a.payment_due_date, a.payment_date, a.payment_invoice, b.operator_name 
            FROM fa_data a 
            LEFT JOIN com_user b ON b.user_id = a.mdb_payment
            WHERE payment_invoice = ?
            GROUP BY payment_invoice";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get fa by invoice no
    function get_fa_by_invoice_no($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.services_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            LEFT JOIN services_code c ON c.services_cd = a.services_cd
            WHERE payment_invoice = ?";
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

    // update
    function update_pembayaran($params, $where) {
        return $this->db->update('fa_data', $params, $where);
    }

}
