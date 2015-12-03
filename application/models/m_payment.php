<?php

class m_payment extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->model("m_preferences");
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
        $sql = "SELECT data_id, published_no, data_type, data_flight, payment_due_date, DATE(published_date)'published_date'
                FROM fa_data a
                INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                WHERE a.data_st = 'approved' AND payment_st = '00' AND (payment_invoice = '' OR payment_invoice IS NULL) 
                AND published_no LIKE ? AND a.airlines_id = ? AND a.data_flight LIKE ?
                ORDER BY published_date ASC, payment_due_date ASC, published_no ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total invoices open
    function get_total_invoices_open_by_airlines($params) {
        $sql = "SELECT COUNT(*)'total' FROM invoice WHERE airlines_id = ? AND inv_st = 'open'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list invoices open
    function get_list_invoices_open_by_airlines($params) {
        $sql = "SELECT a.*, COUNT(b.data_id)'total_fa', operator_name
                FROM invoice a
                LEFT JOIN fa_data b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND inv_st = 'open'
                GROUP BY a.inv_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail invoices 
    function get_detail_invoices($params) {
        $sql = "SELECT b.airlines_nm,a.virtual_account,a.inv_total,a.inv_date,SUBSTR(a.virtual_account,5) AS 'invoice_no' 
                FROM invoice a LEFT JOIN airlines b ON a.airlines_id=b.airlines_id WHERE inv_id = ? ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail invoices 
    function get_detail_invoices_by_va($params) {
        $sql = "SELECT b.airlines_nm,a.virtual_account,a.inv_total,a.inv_date,a.inv_due_date,a.invoice_no
                FROM invoice a LEFT JOIN airlines b ON a.airlines_id=b.airlines_id 
                WHERE a.virtual_account = ? ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get rincian invoice
    function get_rincian_invoices($params) {
        $sql = "SELECT a.virtual_account,c.published_no,c.aircraft_type,c.flight_no,
                c.rute_all,a.inv_due_date AS 'payment_due_date',c.payment_tarif,c.data_flight,
                d.services_nm
                FROM invoice a INNER JOIN invoice_detail b  ON a.virtual_account=b.virtual_account
                INNER JOIN fa_data c ON b.register_id=c.data_id
                LEFT JOIN services_code d ON d.services_cd = c.services_cd
                WHERE a.virtual_account=? ORDER BY c.payment_due_date ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get rincian invoice
    function get_rincian_invoices_by_id($params) {
        $sql = "SELECT a.virtual_account,c.published_no,c.aircraft_type,c.flight_no,
                c.rute_all,a.inv_due_date AS 'payment_due_date',c.payment_tarif,c.data_flight 
                FROM invoice a LEFT JOIN invoice_detail b  ON a.virtual_account=b.virtual_account
                LEFT JOIN fa_data c ON a.virtual_account=c.payment_invoice
                WHERE a.inv_id=? GROUP BY c.data_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    //get rincian invoice ijin
    function get_rincian_invoices_ijin($params) {
        $sql = "SELECT *
                FROM invoice a LEFT JOIN invoice_detail d ON a.virtual_account=d.virtual_account
                INNER JOIN izin_registrasi b ON d.register_id=b.registrasi_id
                LEFT JOIN izin_group c ON b.izin_group=c.group_id
                WHERE a.virtual_account=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_rincian_invoices_ijin_by_id($params) {
        $sql = "SELECT *
                FROM invoice a LEFT JOIN invoice_detail d ON a.virtual_account=d.virtual_account
                LEFT JOIN izin_registrasi b ON d.register_id=b.registrasi_id
                LEFT JOIN izin_group c ON b.izin_group=c.group_id
                WHERE a.inv_id=? GROUP BY b.registrasi_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail invoices open
    function get_detail_invoices_by_id($params) {
        $sql = "SELECT * FROM invoice WHERE inv_id = ? AND airlines_id = ? AND inv_st <> 'open'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail invoices open
    function get_detail_invoice_by_id($params) {
        $sql = "SELECT * FROM invoice WHERE inv_id = ? AND inv_st <> 'open'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail invoices open
    function get_detail_invoices_open_by_airlines($params) {
        $sql = "SELECT * FROM invoice WHERE inv_id = ? AND airlines_id = ? AND inv_st = 'open'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list fa invoices open
    function get_list_fa_invoices_open_by_airlines($params) {
        $sql = "SELECT data_id, published_no, data_type, data_flight, payment_due_date, DATE(published_date)'published_date'
                FROM fa_data a
                INNER JOIN airlines b ON a.airlines_id = b.airlines_id
                WHERE a.data_st = 'approved' AND payment_st = '00'
                AND payment_invoice = ? AND a.airlines_id = ?
                ORDER BY published_date ASC, payment_due_date ASC, published_no ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list invoices pending
    function get_list_pending_invoice($params) {
        $sql = "SELECT a.*, COUNT(b.data_id)'total_fa', operator_name, 
                MIN(a.inv_due_date)'batas_bayar', 
                IF((MIN(a.inv_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
                LEFT JOIN fa_data b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'pending'
                AND a.category = '1'
                GROUP BY a.inv_id
                ORDER BY MIN(a.inv_due_date) ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list invoices ijin pending
    function get_list_pending_invoice_ijin($params) {
        $sql = "SELECT a.*, COUNT(b.registrasi_id)'total_ijin', operator_name, 
                MIN(payment_due_date)'batas_bayar', 
                IF((MIN(payment_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
                LEFT JOIN izin_registrasi b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'pending' AND category='2'
                GROUP BY a.inv_id
                ORDER BY MIN(payment_due_date) ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list invoices ijin failed
    function get_list_failed_invoice_ijin($params) {
        $sql = "SELECT a.*, COUNT(d.register_id)'total_ijin', operator_name, 
                MIN(a.inv_due_date)'batas_bayar', 
                IF((MIN(a.inv_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
		LEFT JOIN invoice_detail d ON a.virtual_account=d.virtual_account
                LEFT JOIN izin_registrasi b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND a.virtual_account LIKE ? AND a.inv_st = 'failed' AND a.category=2
                GROUP BY a.inv_id
                ORDER BY MIN(a.inv_due_date) ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list invoices ijin success
    function get_list_success_invoice_ijin($params) {
        $sql = "SELECT a.*, COUNT(b.registrasi_id)'total_ijin', operator_name, 
                MIN(payment_due_date)'batas_bayar', 
                IF((MIN(payment_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
                LEFT JOIN izin_registrasi b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'success' AND category=2
                GROUP BY a.virtual_account
                ORDER BY MIN(payment_due_date) ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list invoices failed
    function get_list_failed_invoice($params) {
        $sql = "SELECT a.*, COUNT(d.register_id)'total_fa', operator_name, 
                MIN(a.inv_due_date)'batas_bayar', 
                IF((MIN(a.inv_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
		LEFT JOIN invoice_detail d ON a.virtual_account=d.virtual_account
                LEFT JOIN fa_data b ON b.data_id = d.register_id
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND a.virtual_account LIKE ? AND a.inv_st = 'failed'
                AND a.category=1
                GROUP BY a.virtual_account
                ORDER BY MIN(a.inv_due_date) ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get total invoices success
    function get_total_success_invoice($params) {
        $sql = "SELECT COUNT(*)'total' FROM
                (
                    SELECT a.*, COUNT(b.data_id)'total_fa', operator_name, 
                    MIN(payment_due_date)'batas_bayar', 
                    IF((MIN(payment_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                    FROM invoice a
                    LEFT JOIN fa_data b ON a.virtual_account = b.payment_invoice
                    LEFT JOIN com_user c ON a.mdb = c.user_id
                    WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'success'
                    AND a.category='1'
                    GROUP BY a.inv_id
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

    // get total invoices ijin success
    function get_total_success_invoice_ijin($params) {
        $sql = "SELECT COUNT(*)'total' FROM
                (
                    SELECT a.*, COUNT(b.registrasi_id)'total_ijin', operator_name, 
                    MIN(payment_due_date)'batas_bayar', 
                    IF((MIN(payment_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                    FROM invoice a
                    LEFT JOIN izin_registrasi b ON a.virtual_account = b.payment_invoice
                    LEFT JOIN com_user c ON a.mdb = c.user_id
                    WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'success' AND category=2
                    GROUP BY a.inv_id
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

    // get list invoices success
    function get_list_success_invoice($params) {
        $sql = "SELECT a.*, COUNT(b.data_id)'total_fa', operator_name, 
                MIN(payment_due_date)'batas_bayar', 
                IF((MIN(payment_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
                LEFT JOIN fa_data b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'success'
                AND category=1
                GROUP BY a.inv_id
                ORDER BY MIN(payment_due_date) ASC
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

    // get list invoices success ijin
    function get_list_success_invoice_ijin_success($params) {
        $sql = "SELECT a.*, COUNT(b.registrasi_id)'total_ijin', operator_name, 
                MIN(payment_due_date)'batas_bayar', 
                IF((MIN(payment_due_date) < CURRENT_DATE), 1, 0)'status_due_date'
                FROM invoice a
                LEFT JOIN izin_registrasi b ON a.virtual_account = b.payment_invoice
                LEFT JOIN com_user c ON a.mdb = c.user_id
                WHERE a.airlines_id = ? AND virtual_account LIKE ? AND inv_st = 'success' AND a.category='2'
                GROUP BY a.inv_id
                ORDER BY MIN(payment_due_date) ASC
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

    // get list invoice izin rute
    function get_list_invoice_izinrute($params) {
        $sql = "SELECT * FROM (
			    SELECT a.*, airlines_brand, airlines_nm,c.group_nm,c.group_alias,d.dos,
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
        $sql = "SELECT a.*, b.airlines_nm,c.group_nm,c.group_alias,d.dos,
                d.izin_frekuensi_add,d.izin_tarif,
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
            WHERE a.registrasi_id = ? AND payment_st = '00' AND (ISNULL(payment_date) OR payment_date = '' OR payment_date = '0000-00-00 00:00:00')
            GROUP BY a.registrasi_id";
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
        $sql = "SELECT b.airlines_nm,a.virtual_account,a.inv_total,c.data_flight,
                a.inv_date,a.invoice_no,a.inv_due_date 
                FROM invoice a LEFT JOIN airlines b ON a.airlines_id=b.airlines_id 
		LEFT JOIN invoice_detail e ON a.virtual_account=e.virtual_account
                LEFT JOIN fa_data c ON e.register_id=c.data_id
                LEFT JOIN airlines d ON a.airlines_id=d.airlines_id
                WHERE a.virtual_account = ?
		GROUP BY a.inv_id";
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
            c.inv_total,a.izin_group,e.airlines_nm,a.total_invoice,
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
        $sql = "SELECT a.*, c.services_nm
                FROM fa_data a 
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
    function get_ijin_by_invoice_no($params) {
        $sql = "SELECT a.*, c.group_nm
                FROM izin_registrasi a 
                LEFT JOIN izin_group c ON a.izin_group = c.group_id
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
        $sql = "SELECT a.*, b.airlines_nm, c.group_nm,c.group_alias,d.dos 
            FROM izin_registrasi a 
            LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            LEFT JOIN izin_group c ON c.group_id=a.izin_group
            LEFT JOIN izin_rute d ON d.registrasi_id=a.registrasi_id
            WHERE a.payment_invoice = ? GROUP BY a.registrasi_id";
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
    function update_invoices($params, $where) {
        return $this->db->update('invoice', $params, $where);
    }

    // update
    function update_payment($params, $where) {
        return $this->db->update('fa_data', $params, $where);
    }

    //update
    function update_fa_kode_billing($params) {
        $sql = "UPDATE fa_data SET 
				payment_st = ?,
				payment_date = ?,
				payment_invoice = ?,
				payment_tarif = ?,
				mdb_payment = ?
				WHERE data_id = ?
				AND (ISNULL(payment_invoice) OR payment_invoice = '')";
        return $this->db->query($sql, $params);
    }

    //validasi kode billing rute
    function is_rute_already_billed($params = '') {
        $sql = "SELECT 
				IF( (ISNULL(a.payment_invoice) OR a.payment_invoice=''),'0','1') AS 'billing_st'
				FROM izin_registrasi a
				WHERE a.registrasi_id =  ?";
		$query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['billing_st'];
        } else {
            return '0';
        }
    }

    //validasi kode billing rute
    function is_fa_already_billed($params = '') {
        $sql = "SELECT 
				IF( (ISNULL(a.payment_invoice) OR a.payment_invoice=''),'0','1') AS 'billing_st'
				FROM fa_data a
				WHERE a.data_id =  ?";
		$query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['billing_st'];
        } else {
            return '0';
        }
    }

    // update
    function update_payment_rute($params, $where) {
        return $this->db->update('izin_registrasi', $params, $where);
    }

    //update rute kode billing
    function update_rute_kode_billing($params) {
        $sql = "UPDATE izin_registrasi SET 
				payment_st = ?,
				payment_date = ?,
				payment_due_date = ?,
				payment_invoice = ?,
				payment_by = ?
				WHERE registrasi_id = ?
				AND (ISNULL(payment_invoice) OR payment_invoice = '')";
        return $this->db->query($sql, $params);
    }

    // cancel_invoices
    function cancel_invoices($params) {
        $sql = "UPDATE invoice SET inv_st = 'cancel' WHERE inv_id = ? AND airlines_id = ? AND inv_st = 'open'";
        return $this->db->query($sql, $params);
    }

    // get virtual account
    function get_virtual_account_by_airlines($params) {
        $sql = "SELECT virtual_account FROM airlines WHERE airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['virtual_account'];
        } else {
            return '';
        }
    }

    // get airlines code
    function get_airlinescode_by_airlines($params) {
        $sql = "SELECT SUBSTR(virtual_account,5,3) AS 'airlines_code' FROM airlines WHERE airlines_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['airlines_code'];
        } else {
            return '';
        }
    }

    // get tabel tarif fa
    function get_tabel_tarif_fa($params) {
        // default
        $tarif['domestik'] = 0;
        $tarif['internasional'] = 0;
        // query
        $sql = "SELECT * FROM com_preferences WHERE pref_group = 'tarif_fa'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            foreach ($result as $data) {
                $tarif[$data['pref_nm']] = $data['pref_value'];
            }
        }
        // return tarif
        return $tarif;
    }

    // get tabel tarif rute
    function get_tabel_tarif_rute() {
        // default
        $tarif['baru'] = 0;
        $tarif['frekuensi'] = 0;
        $tarif['notam'] = 0;
        // query
        $sql = "SELECT * FROM com_preferences WHERE pref_group = 'tarif_rute'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            foreach ($result as $data) {
                $tarif[$data['pref_nm']] = $data['pref_value'];
            }
        }
        // return tarif
        return $tarif;
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

    //get last fa invoice no
    function get_last_fa_invoice_no($params) {
        $sql = "SELECT * 
            FROM invoice 
            WHERE airlines_id = ? AND category='1'
            ORDER BY invoice_no DESC
            LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create number
            $number = intval(substr($result['invoice_no'], 8)) + 1;
            $zero = "";
            for ($i = strlen($number); $i < 8; $i++) {
                $zero .= "0";
            }
            return str_pad($params, 3, '0', STR_PAD_LEFT) . '1' . $zero . $number;
        } else {
            $invoice_no = $params . '100000001';
            return $invoice_no;
        }
    }

    //get last fa invoice no
    function get_last_rute_invoice_no($params) {
        $sql = "SELECT * 
            FROM invoice 
            WHERE airlines_id = ? AND category='2'
            ORDER BY invoice_no DESC
            LIMIT 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            // create number
            $number = intval(substr($result['invoice_no'], 8)) + 1;
            $zero = "";
            for ($i = strlen($number); $i < 8; $i++) {
                $zero .= "0";
            }
            return str_pad($params, 3, '0', STR_PAD_LEFT) . '2' . $zero . $number;
        } else {
            $invoice_no = $params . '200000001';
            return $invoice_no;
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
        $sql = "SELECT a.*, b.airlines_nm,c.group_alias 
            FROM izin_registrasi a LEFT JOIN airlines b ON b.airlines_id = a.airlines_id
            LEFT JOIN izin_group c ON a.izin_group=c.group_id
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
        $sql = "SELECT b.inv_id,b.virtual_account,b.inv_date,b.inv_total,
                b.inv_st,b.tgl_update,b.remark,
                COUNT(a.detail_id) AS 'jml_dibayar'
                FROM invoice_detail a JOIN invoice b
                ON a.virtual_account=b.virtual_account
                WHERE b.virtual_account LIKE ? AND b.category=? AND b.airlines_id=? AND b.inv_st LIKE ?
                GROUP BY b.inv_id LIMIT ?,?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function count_list_issued_invoice($params) {
        $sql = "SELECT COUNT(*) AS 'jumlah' FROM invoice b "
                . " WHERE b.virtual_account LIKE ? AND b.category=? AND b.airlines_id=? AND b.inv_st LIKE ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result["jumlah"];
        } else {
            return 0;
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
                DATE(a.tgl_update) AS 'tgl_update',a.no_kuitansi,a.inv_date,a.ntpn,a.ntb,
                a.invoice_no
                FROM invoice a 
                LEFT JOIN fa_data b ON b.payment_invoice=a.virtual_account
                LEFT JOIN airlines c ON a.airlines_id=c.airlines_id
                WHERE a.inv_id=? GROUP BY a.inv_id";
        $query = $this->db->query($sql, $invoice_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_kwitansi_izin($invoice_id = "") {
        $sql = "SELECT c.airlines_nm,a.inv_total,b.izin_flight,a.virtual_account,
                DATE(a.tgl_update) AS 'tgl_update',a.no_kuitansi,a.inv_date,a.ntpn,a.ntb,
                a.invoice_no FROM invoice a LEFT JOIN izin_registrasi b ON b.payment_invoice=a.virtual_account
                LEFT JOIN airlines c ON b.airlines_id=c.airlines_id
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
        $sql = "SELECT c.category,a.amount,b.* FROM invoice_detail a 
                INNER JOIN invoice c ON a.virtual_account=c.virtual_account
                INNER JOIN fa_data b ON a.register_id=b.data_id
                WHERE b.airlines_id=? AND c.category=? AND c.inv_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_rincian_kwitansi_rute($params = "") {
        $sql = "SELECT c.category,a.amount,b.* FROM invoice_detail a 
                INNER JOIN invoice c ON a.virtual_account=c.virtual_account
                INNER JOIN izin_registrasi b ON a.register_id=b.registrasi_id
                WHERE b.airlines_id=? AND c.category=? AND c.inv_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_rekap_pembayaran($params) {
        $sql = "SELECT 
              SUM(IF(b.payment_st='00' AND b.data_flight='domestik',1,0)) AS 'pending_dom',
              SUM(IF(b.payment_st='01' AND b.data_flight='domestik',1,0)) AS 'kurang_bayar_dom',
              SUM(IF(b.payment_st='02' AND b.data_flight='domestik',1,0)) AS 'lebih_bayar_dom',
              SUM(IF(b.payment_st='11' AND b.data_flight='domestik',1,0)) AS 'success_dom',
              SUM(IF(b.payment_st='00' AND b.data_flight='internasional',1,0)) AS 'pending_int',
              SUM(IF(b.payment_st='01' AND b.data_flight='internasional',1,0)) AS 'kurang_bayar_int',
              SUM(IF(b.payment_st='02' AND b.data_flight='internasional',1,0)) AS 'lebih_bayar_int',
              SUM(IF(b.payment_st='11' AND b.data_flight='internasional',1,0)) AS 'success_int'
              FROM invoice a 
              LEFT JOIN fa_data b ON a.virtual_account=b.payment_invoice
              WHERE b.airlines_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_rekap_pembayaran_rute($params) {
        $sql = "SELECT 
              SUM(IF(b.payment_st='00' AND b.izin_flight='domestik',1,0)) AS 'pending_dom',
              SUM(IF(b.payment_st='01' AND b.izin_flight='domestik',1,0)) AS 'kurang_bayar_dom',
              SUM(IF(b.payment_st='02' AND b.izin_flight='domestik',1,0)) AS 'lebih_bayar_dom',
              SUM(IF(b.payment_st='11' AND b.izin_flight='domestik',1,0)) AS 'success_dom',
              SUM(IF(b.payment_st='00' AND b.izin_flight='internasional',1,0)) AS 'pending_int',
              SUM(IF(b.payment_st='01' AND b.izin_flight='internasional',1,0)) AS 'kurang_bayar_int',
              SUM(IF(b.payment_st='02' AND b.izin_flight='internasional',1,0)) AS 'lebih_bayar_int',
              SUM(IF(b.payment_st='11' AND b.izin_flight='internasional',1,0)) AS 'success_int'
              FROM invoice a 
              LEFT JOIN izin_registrasi b ON a.virtual_account=b.payment_invoice
              WHERE b.airlines_id=?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_detail_izin_by_id($izin_id = "") {
        $sql = "SELECT * FROM izin_registrasi a LEFT JOIN izin_group b ON a.izin_group=b.group_id WHERE registrasi_id=?";
        $query = $this->db->query($sql, $izin_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_payment_due_date_fa($data_id) {
        $sql = "SELECT payment_due_date FROM fa_data WHERE data_id = ?";
        $query = $this->db->query($sql, $data_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function get_payment_due_date_izin($reg_id) {
        $sql = "SELECT CONCAT(payment_due_date,' ',TIME(NOW())) AS payment_due_date FROM izin_registrasi WHERE registrasi_id = ?";
        $query = $this->db->query($sql, $reg_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    function send_email_tagihan($invoice_id = "", $email = "", $subject = "") {
        //get invoice detail
        $rs_id = $this->m_payment->get_detail_invoices($invoice_id);
        $rs_rincian = $this->m_payment->get_rincian_invoices($rs_id["virtual_account"]);
        //load lib email
        $this->load->library("email");
        $this->load->library("datetimemanipulation");
        $mail = $this->m_preferences->get_mail();
        $detail = explode(",", $mail['pref_value']);
        $host = $mail['pref_nm'];
        $port = $detail[0];
        $user = $detail[1];
        $pass = $detail[2];
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = str_replace(" ", "", $host);
        $config['smtp_port'] = str_replace(" ", "", $port);
        $config['smtp_timeout'] = '7';
        $config['smtp_user'] = str_replace(" ", "", $user);
        $config['smtp_pass'] = str_replace(" ", "", $pass);
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $config['validation'] = FALSE;
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $html = "";
        $html.="<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>                
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                            <table cellspacing='0' cellpadding='0' align='center'>
                                <tbody>
                                <tr>
                                    <td width='700' valign='top' style='color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:50px;line-height:0%;max-width:700px;background-color:#5F7B93'>
                                        <div align='left'>
                                            <a target='_blank' style='text-decoration:none;align:left;padding:3px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'><img src='http://aol.dephub.go.id/angudonline/resource/doc/images/logo/invoice.png' style='padding:10px' alt='Kementerian Perhubungan Republik Indonesia' /></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:10px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                        <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $rs_id["airlines_nm"] . "</b></span><br><br>
                                        <span style='font:normal 11px Tahoma'>Berikut disampaikan tagihan atas pembayaran permohonan flight approval<br>
                                        <span style='font:normal 11px Tahoma'>Email ini <strong>BUKAN BUKTI PEMBAYARAN YANG SAH</strong>. Silakan selesaikan transaksi anda dengan detail sebagai berikut :</span><br/>
                                        <div style='padding:11px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                            <table width='100%' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;'>
                                                <tr>
                                                    <td width='30%'>Nomor Tagihan</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . $rs_id["invoice_no"] . "</b></td>
                                                </tr>
                                                <tr>
                                                    <td width='30%'>Kode Billing</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . $rs_id["virtual_account"] . "</b></td>
                                                </tr>
                                                <tr>
                                                    <td width='30%'>Jumlah Tagihan</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . number_format($rs_id["inv_total"], 0, ",", ".") . "</b></td>
                                                </tr>
                                                <tr>
                                                    <td width='30%'>Tanggal Tagihan</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . $this->datetimemanipulation->get_full_date($rs_id["inv_date"]) . "</b></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style='padding:11px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 10px/200% Helvetica,sans-serif,Arial;'>   
                                        <table width='100%' cellspacing='0' cellpadding='2' border='0' style='font:normal 10px Arial'>
                                            <tbody>
                                                <tr>
                                                    <th style='border:1px solid #DAD5BA;background-color:#ECEADC;padding:5px'>No</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Published No</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Aircraft Type</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Flight No</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Batas Bayar</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Tarif</th>
                                                </tr>";
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.="<tr>";
            $html.="<td align='center' style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;;border-top-width:0;'>" . ($index + 1) . "</td><td style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0'>" . $rincian["published_no"] . "</td><td style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0;'>" . $rincian["aircraft_type"] . "</td>";
            $html.="<td style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0'>" . $rincian["flight_no"] . "</td><td style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0;'>" . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"], 'ins') . "</td><td align='right' style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0;'>" . number_format($rincian["payment_tarif"], 0, ".", ",") . "</td>";
            $html.="</tr>";
            $total+=$rincian["payment_tarif"];
        }
        $html.="<tr><td colspan='5' align='left' style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;;border-top-width:0;'><b>TOTAL</b></td>" . "<td align='right' style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0;'>" . number_format($total, 0, ",", ".") . "</td></tr>";
        $html.="</tbody>
                                        </table>
                                        </div>
                                        <span style='font-size:11px'>
                                        <strong>PERHATIAN :</strong> 
                                        </span><br/>
                                        <span style='font-size:11px'>
                                            * Pastikan kode billing sesuai dengan yang tercantum pada invoice 
                                        </span><br/>
                                        <span style='font-size:11px'>
                                            * Harap diperhatikan batas pembayaran yang tercantum pada invoice, diharapkan agar membayar sebelum tanggal jatuh tempo 
                                        </span><br/>
                                        <span style='font-size:11px'>
                                            * Jika membutuhkan informasi, bantuan, dan petunjuk teknis terkait penggunaan sistem billing serta pembayaran dan penyetoran PNBP,
                                              dapat menghubungi :
                                            <ul>
                                                <li>Call Center Kementerian Perhubungan : (021) 151 (24 jam, setiap hari)</li>
                                                <li>Call Center Direktorat Jenderal Anggaran : (021) 34832511 ( Jam & Hari Kerja )</li>
                                                <li>Customer Service Direktorat Jenderal Anggaran : (021) 34832516 ( Jam & Hari Kerja )</li>
                                            </ul>
                                        </span><br/><br/>
                                        <br/>
                                       <span style='font-size:11px'>
                                           Cara Pembayaran PNBP via ATM BRI :
                                           <ol>
                                               <li>Pilih menu : Transaksi Lain; Pembayaran; Lainnya; Lainnya; MPN;</li>
                                               <li>Masukkan 15 digit Kode Pembayaran; kemudian tekan 'Benar'</li>
                                               <li>Muncul layar konfirmasi pembayaran, bila setuju bayar, tekan 'YA';</li>
                                               <li>Simpan Struk bukti pembayaran PNBP.</li>
                                               <li>Selesai</li>
                                           </ol>
                                       </span><br/>
                                       <span style='font-size:11px'>
                                           Cara Pembayaran PNBP via Internet Banking BRI :
                                           <ol>
                                               <li>Pilih menu : Pembayaran; MPN;</li>
                                               <li>Masukkan PIN</li>
                                               <li>Masukkan Kode Billing;</li>
                                               <li>Ikuti langkah selanjutnya.</li>
                                               <li>Selesai</li>
                                           </ol>
                                       </span><br/><br/><br/>
                                    <center style='font:bold 11px Tahoma'><b>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</b></center><br>
                            </td>
                        </tr>
                        <tr>
                            <td width='570' bgcolor='ECE9D8' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>Jl. Merdeka Barat, No. 8 Jakarta Pusat, Indonesia +62 21 3811308<br/><a target='_blank' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                            <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://www.facebook.com/pages/Kemenhub151/364857507021671'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                    </table>";
        $useremail = str_replace(" ", "", $user);
        $this->email->from($useremail, 'Flight Approval Online (no reply)');
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($html);
        $this->email->send();
    }

    function send_email_tagihan_izin($invoice_id = "", $email = "", $subject = "") {
        //get invoice detail
        $rs_id = $this->m_payment->get_detail_invoices(array($invoice_id));
        $rs_rincian = $this->m_payment->get_rincian_invoices_ijin_by_id($invoice_id);
        //load lib email
        $this->load->library("email");
        $this->load->library("datetimemanipulation");
        $mail = $this->m_preferences->get_mail();
        $detail = explode(",", $mail['pref_value']);
        $host = $mail['pref_nm'];
        $port = $detail[0];
        $user = $detail[1];
        $pass = $detail[2];
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = str_replace(" ", "", $host);
        $config['smtp_port'] = str_replace(" ", "", $port);
        $config['smtp_timeout'] = '7';
        $config['smtp_user'] = str_replace(" ", "", $user);
        $config['smtp_pass'] = str_replace(" ", "", $pass);
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $config['validation'] = FALSE;
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $html = "";
        $html.="<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>                
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                            <table cellspacing='0' cellpadding='0' align='center'>
                                <tbody>
                                <tr>
                                    <td width='700' valign='top' style='color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:50px;line-height:0%;max-width:700px;background-color:#5F7B93'>
                                        <div align='left'>
                                            <a target='_blank' style='text-decoration:none;align:left;padding:3px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'><img src='http://aol.dephub.go.id/angudonline/resource/doc/images/logo/invoice.png' style='padding:10px' alt='Kementerian Perhubungan Republik Indonesia' /></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:10px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                        <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $rs_id["airlines_nm"] . "</b></span><br><br>
                                        <span style='font:normal 11px Tahoma'>Berikut disampaikan tagihan atas pembayaran ijin rute penerbangan<br>
                                        <span style='font:normal 11px Tahoma'>Email ini <strong>BUKAN BUKTI PEMBAYARAN YANG SAH</strong>. Silakan selesaikan transaksi anda dengan detail sebagai berikut :</span><br/>
                                        <div style='padding:11px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                            <table width='100%' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;'>
                                                <tr>
                                                    <td width='30%'>Nomor Tagihan</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . $rs_id["invoice_no"] . "</b></td>
                                                </tr>
                                                <tr>
                                                    <td width='30%'>Kode Billing</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . $rs_id["virtual_account"] . "</b></td>
                                                </tr>
                                                <tr>
                                                    <td width='30%'>Jumlah Tagihan</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . number_format($rs_id["inv_total"], 0, ",", ".") . "</b></td>
                                                </tr>
                                                <tr>
                                                    <td width='30%'>Tanggal Tagihan</td>
                                                    <td width='2%'>:</td>
                                                    <td width='68%'><b>" . $this->datetimemanipulation->get_full_date($rs_id["inv_date"]) . "</b></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style='padding:11px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 10px/200% Helvetica,sans-serif,Arial;'>   
                                        <table width='100%' cellspacing='0' cellpadding='2' border='0' style='font:normal 10px Arial'>
                                            <tbody>
                                                <tr>
                                                    <th style='border:1px solid #DAD5BA;background-color:#ECEADC;padding:5px'>No</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>No Surat Izin</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Jenis Pengajuan</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Tanggal Permohonan</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Batas Bayar</th>
                                                    <th style='border:1px solid #DAD5BA;border-left-width:0px;background-color:#ECEADC;padding:5px'>Jumlah Tagihan</th>
                                                </tr>";
        $total = 0;
        foreach ($rs_rincian as $index => $rincian) {
            $html.='<tr>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;border:1px solid #DAD5BA;padding:5px;border-top-width:0;">' . ($index + 1) . '. </td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;border:1px solid #DAD5BA;padding:5px;border-top-width:0;">' . $rincian["izin_published_letter"] . '</td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;border:1px solid #DAD5BA;padding:5px;border-top-width:0;">' . strtoupper($rincian["group_nm"]) . '</td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;border:1px solid #DAD5BA;padding:5px;border-top-width:0;">' . $this->datetimemanipulation->get_full_date($rincian["payment_date"], 'ins') . '</td>';
            $html.='<td align="center" style="background-color:#ffffed;padding:5px;border:1px solid #DAD5BA;padding:5px;border-top-width:0;">' . $this->datetimemanipulation->get_full_date($rincian["payment_due_date"], 'ins') . '</td>';
            $html.='<td align="right" style="background-color:#ffffed;padding:5px;border:1px solid #DAD5BA;padding:5px;border-top-width:0;">' . number_format($rincian["total_invoice"], 0, ',', '.') . '</td>';
            $html.="</tr>";
            $total+=$rincian["total_invoice"];
        }
        $html.="<tr><td colspan='5' align='left' style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;;border-top-width:0;'><b>TOTAL</b></td>" . "<td align='right' style='border:1px solid #DAD5BA;background-color:#FFF;padding:5px;border-left-width:0;border-top-width:0;'>" . number_format($total, 0, ",", ".") . "</td></tr>";
        $html.="                            </tbody>
                                        </table>
                                        </div>
                                        <span style='font-size:11px'>
                                        <strong>PERHATIAN :</strong> 
                                        </span><br/>
                                        <span style='font-size:11px'>
                                            * Pastikan kode billing sesuai dengan yang tercantum pada invoice 
                                        </span><br/>
                                        <span style='font-size:11px'>
                                            * Harap diperhatikan batas pembayaran yang tercantum pada invoice, diharapkan agar membayar sebelum tanggal jatuh tempo 
                                        </span><br/>
                                        <span style='font-size:11px'>
                                            * Jika membutuhkan informasi, bantuan, dan petunjuk teknis terkait penggunaan sistem billing serta pembayaran dan penyetoran PNBP,
                                              dapat menghubungi :
                                            <ul>
                                                <li>Call Center Kementerian Perhubungan : (021) 151 (24 jam, setiap hari)</li>
                                                <li>Call Center Direktorat Jenderal Anggaran : (021) 34832511 ( Jam & Hari Kerja )</li>
                                                <li>Customer Service Direktorat Jenderal Anggaran : (021) 34832516 ( Jam & Hari Kerja )</li>
                                            </ul>
                                        </span><br/><br/>
                                        <br/>
                                       <span style='font-size:11px'>
                                           Cara Pembayaran PNBP via ATM BRI :
                                           <ol>
                                               <li>Pilih menu : Transaksi Lain; Pembayaran; Lainnya; Lainnya; MPN;</li>
                                               <li>Masukkan 15 digit Kode Pembayaran; kemudian tekan 'Benar'</li>
                                               <li>Muncul layar konfirmasi pembayaran, bila setuju bayar, tekan 'YA';</li>
                                               <li>Simpan Struk bukti pembayaran PNBP.</li>
                                               <li>Selesai</li>
                                           </ol>
                                       </span><br/>
                                       <span style='font-size:11px'>
                                           Cara Pembayaran PNBP via Internet Banking BRI :
                                           <ol>
                                               <li>Pilih menu : Pembayaran; MPN;</li>
                                               <li>Masukkan PIN</li>
                                               <li>Masukkan Kode Billing;</li>
                                               <li>Ikuti langkah selanjutnya.</li>
                                               <li>Selesai</li>
                                           </ol>
                                       </span><br/><br/><br/>
                                    <center style='font:bold 11px Tahoma'><b>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</b></center><br>
                            </td>
                        </tr>
                        <tr>
                            <td width='570' bgcolor='ECE9D8' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>Jl. Merdeka Barat, No. 8 Jakarta Pusat, Indonesia +62 21 3811308<br/><a target='_blank' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                            <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://www.facebook.com/pages/Kemenhub151/364857507021671'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                    </table>";
        $useremail = str_replace(" ", "", $user);
        $this->email->from($useremail, 'Izin Rute Online (no reply)');
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($html);
        $this->email->send();
    }

}
