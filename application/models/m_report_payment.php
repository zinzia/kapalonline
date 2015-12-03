<?php

class m_report_payment extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get invoice id
    function get_invoice_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        return $id;
    }

    // get invoice detail id
    function get_invoice_detail_id() {
        $time = microtime(true);
        $id = str_replace('.', '', $time);
        $id = str_replace(',', '', $id);
        return $id;
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

    // get list task berjadwal
    function get_list_awaiting_task_berjadwal($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_brand, airlines_nm
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    WHERE a.data_st = 'approved' AND payment_st = '00' AND (payment_invoice ='' OR payment_invoice IS NULL) 
                    AND published_no LIKE ? AND a.data_flight LIKE ?
                    ORDER BY a.payment_due_date DESC
                ) result
                GROUP BY data_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list invoice izin rute
    function get_list_invoice_izinrute($params) {
        $sql = "SELECT * FROM (
			    SELECT a.*, airlines_brand, airlines_nm,c.group_nm,d.dos,
			    ( 
                        IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 7, 1) = 0, 0, 1))'frekuensi',
                        d.izin_st
			    FROM izin_registrasi a
			    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
			    INNER JOIN izin_group c ON a.izin_group=c.group_id
			    INNER JOIN izin_rute d ON a.registrasi_id=d.registrasi_id
			    INNER JOIN izin_group e ON a.izin_group=e.group_id
			    WHERE a.izin_approval = 'approved' AND a.payment_st = '00' AND (payment_invoice ='' OR payment_invoice IS NULL) AND (izin_request_letter LIKE ? OR izin_request_letter IS NULL)
			    AND a.payment_st = '00'
			    AND b.airlines_id=?		
			    ORDER BY a.payment_due_date DESC
			) result
			GROUP BY registrasi_id";
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
                    WHERE a.data_st = 'approved' AND payment_st = '00' AND published_no LIKE ? 
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
                    WHERE a.data_st = 'approved' AND payment_st = '00' AND published_no LIKE ? 
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
            WHERE data_id = ? AND payment_st = '00' AND (ISNULL(payment_date) OR payment_date = '' OR payment_date = '0000-00-00 00:00:00')";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get published no
    function get_published_izin_detail($params) {
        $sql = "SELECT a.*, b.airlines_nm,c.group_nm,d.dos,
            ( 
                        IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 7, 1) = 0, 0, 1))'frekuensi'
            FROM izin_registrasi a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            LEFT JOIN izin_group c ON a.izin_group=c.group_id
            LEFT JOIN izin_rute d ON a.registrasi_id=d.registrasi_id
            WHERE a.registrasi_id = ? AND payment_st = '00' AND (ISNULL(payment_date) OR payment_date = '' OR payment_date = '0000-00-00 00:00:00')";
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
        $sql = "SELECT a.payment_st, a.payment_due_date, a.payment_date, a.payment_invoice, b.operator_name,
            c.inv_total,d.airlines_nm
            FROM fa_data a 
            LEFT JOIN com_user b ON b.user_id = a.mdb_payment
            LEFT JOIN invoice c ON a.payment_invoice=c.virtual_account
            LEFT JOIN airlines d ON d.airlines_id = a.airlines_id
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

    function get_detail_invoice_rute($params) {
        $sql = "SELECT a.payment_st, a.payment_due_date, a.payment_date, a.payment_invoice, b.operator_name,
            c.inv_total,a.izin_group,e.airlines_nm,
            ( 
                        IF(SUBSTRING(dos, 1, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 2, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 3, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 4, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 5, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 6, 1) = 0, 0, 1) + 
                        IF(SUBSTRING(dos, 7, 1) = 0, 0, 1))'frekuensi'
            FROM izin_registrasi a
            LEFT JOIN izin_rute d ON a.registrasi_id=d.registrasi_id
            LEFT JOIN airlines e ON a.airlines_id=e.airlines_id
            LEFT JOIN com_user b ON b.user_id = a.payment_by
            LEFT JOIN invoice c ON a.payment_invoice=c.virtual_account
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

    // get fa by invoice no
    function get_rute_by_invoice_no($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.group_nm,d.dos 
            FROM izin_registrasi a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            LEFT JOIN izin_group c ON c.group_id=a.izin_group
            LEFT JOIN izin_rute d ON d.registrasi_id=a.registrasi_id
            WHERE a.payment_invoice = ?";
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

    // insert invoice
    function insert_invoice($params) {
        return $this->db->insert('invoice', $params);
    }

    // insert invoice detail
    function insert_invoice_detail($params) {
        return $this->db->insert('invoice_detail', $params);
    }

    // update
    function update_payment($params, $where) {
        return $this->db->update('fa_data', $params, $where);
    }

    // update
    function update_payment_rute($params) {
        $sql = "UPDATE izin_registrasi SET payment_st=?,payment_date=?,payment_invoice=?,payment_by=? WHERE registrasi_id=?";
        return $this->db->query($sql, $params);
    }

    // get virtual account
    function get_virtual_account($params) {
        $sql = "SELECT * 
            FROM airlines 
            WHERE airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get last virtual account
    function get_last_virtual_account($params) {
        $sql = "SELECT * 
            FROM invoice 
            WHERE LEFT(virtual_account, 7) = ? AND category='1'
            ORDER BY virtual_account DESC
            LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create number
            $number = intval(substr($result['virtual_account'], 8)) + 1;
            $zero = "";
            for ($i = strlen($number); $i < 8; $i++) {
                $zero .= "0";
            }
            return $params . '1' . $zero . $number;
        } else {
            $virtual_account = $params . '100000001';
            return $virtual_account;
        }
    }

    // get last virtual account
    function get_last_virtual_account_rute($params) {
        $sql = "SELECT * 
            FROM invoice 
            WHERE LEFT(virtual_account, 7) = ? AND category='2'
            ORDER BY virtual_account DESC
            LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create number
            $number = intval(substr($result['virtual_account'], 8)) + 1;
            $zero = "";
            for ($i = strlen($number); $i < 8; $i++) {
                $zero .= "0";
            }
            return $params . '2' . $zero . $number;
        } else {
            $virtual_account = $params . '200000001';
            return $virtual_account;
        }
    }

    function get_detail_payment($params) {
        $sql = "SELECT a.*, b.airlines_nm 
            FROM fa_data a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
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

    function get_detail_payment_rute($params) {
        $sql = "SELECT a.*, b.airlines_nm 
            FROM izin_registrasi a
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            WHERE a.registrasi_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_detail_payment_inv($params) {
        $sql = "SELECT a.*, b.airlines_nm 
            FROM izin_registrasi a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            WHERE a.payment_invoice = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_inv_detail_by_va($params) {
        $sql = "SELECT b.published_no,UPPER(b.aircraft_type) AS 'aircraft_tp',
                b.flight_no,UPPER(c.services_nm) AS 'service_nm',
                a.amount FROM invoice_detail a
                LEFT JOIN fa_data b ON a.register_id=b.data_id
                LEFT JOIN services_code c ON b.services_cd=c.services_cd
                WHERE a.virtual_account=?
                GROUP BY a.register_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_inv_rute_detail_by_va($params) {
        $sql = "SELECT a.*, b.airlines_nm, c.group_nm, 
               d.amount FROM izin_registrasi a 
               LEFT JOIN airlines b ON a.airlines_id=b.airlines_id
               LEFT JOIN izin_group c ON a.izin_group=c.group_id
               LEFT JOIN invoice_detail d ON a.payment_invoice=d.virtual_account
               WHERE a.payment_invoice=?
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

    // get list task berjadwal
    function get_list_paid_invoice_berjadwal($params) {
        $sql = "SELECT * FROM
                (
                    SELECT a.*, airlines_brand, airlines_nm
                    FROM fa_data a
                    INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                    WHERE a.data_st = 'approved' AND a.payment_st = 'lunas' AND (payment_invoice IS NOT NULL OR payment_invoice !='') AND published_no LIKE ? 
                    ORDER BY a.payment_due_date DESC
                ) result
                GROUP BY data_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get issued
    function get_list_issued_invoice($params) {
        $sql = "SELECT b.inv_id,b.virtual_account,b.inv_date,b.tgl_transaksi,b.inv_total,
                b.inv_st,b.tgl_update,b.remark, c.airlines_nm,
                COUNT(a.data_id) AS 'jml_dibayar'
                FROM fa_data a JOIN invoice b
                ON a.payment_invoice=b.virtual_account
                LEFT JOIN airlines c ON c.airlines_id = b.airlines_id
                WHERE b.virtual_account LIKE ? AND b.category=? AND b.inv_st LIKE ?
                GROUP BY b.inv_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_izin_rute($izin_id = "") {
        $sql = "SELECT * FROM izin_data WHERE izin_id=?";
        $query = $this->db->query($sql, $izin_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_kwitansi($invoice_id = "") {
        $sql = "SELECT c.airlines_nm,a.inv_total,b.data_flight,a.virtual_account,
              DATE(a.tgl_update) AS 'tgl_update',a.no_kuitansi
              FROM invoice a 
              LEFT JOIN fa_data b ON b.payment_invoice=a.virtual_account
              LEFT JOIN airlines c ON a.airlines_id=c.airlines_id
              WHERE a.inv_id=?";
        $query = $this->db->query($sql, $invoice_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    function get_rincian_kwitansi($params = "") {
        $sql = "SELECT c.category,a.amount,c.no_kuitansi,b.* FROM invoice_detail a 
                INNER JOIN invoice c ON a.virtual_account=c.virtual_account
                INNER JOIN fa_data b ON a.register_id=b.data_id
                WHERE c.category=? AND c.inv_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
    
    function get_rekap_pembayaran() {
        $sql="SELECT 
              SUM(IF(b.payment_st='00' AND b.data_flight='domestik',1,0)) AS 'pending_dom',
              SUM(IF(b.payment_st='01' AND b.data_flight='domestik',1,0)) AS 'kurang_bayar_dom',
              SUM(IF(b.payment_st='02' AND b.data_flight='domestik',1,0)) AS 'lebih_bayar_dom',
              SUM(IF(b.payment_st='11' AND b.data_flight='domestik',1,0)) AS 'success_dom',
              SUM(IF(b.payment_st='00' AND b.data_flight='internasional',1,0)) AS 'pending_int',
              SUM(IF(b.payment_st='01' AND b.data_flight='internasional',1,0)) AS 'kurang_bayar_int',
              SUM(IF(b.payment_st='02' AND b.data_flight='internasional',1,0)) AS 'lebih_bayar_int',
              SUM(IF(b.payment_st='11' AND b.data_flight='internasional',1,0)) AS 'success_int'
              FROM invoice a 
              LEFT JOIN fa_data b ON a.virtual_account=b.payment_invoice";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

}
