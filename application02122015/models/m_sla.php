<?php

class m_sla extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get statistik fa
    function get_sla($params) {
        $sql = "SELECT result.*, COUNT(*)'total_proses', SEC_TO_TIME(SUM(detik))'total_time', SEC_TO_TIME(ROUND(SUM(detik) / COUNT(*)))'response_time' 
            FROM 
            (
                SELECT a.process_st, a.action_st, a.mdd, a.mdb_finish, a.mdd_finish, b.user_name, b.operator_name, TIME_TO_SEC(TIMEDIFF(a.mdd_finish, a.mdd))'detik' 
                FROM fa_process a 
                LEFT JOIN com_user b ON b.user_id = a.mdb_finish 
                LEFT JOIN fa_data c ON c.data_id = a.data_id 
                WHERE !ISNULL(a.mdb_finish) AND MONTH(c.mdd) LIKE ? AND YEAR(c.mdd) LIKE ? AND (a.flow_id != '91' AND a.flow_id != '92' AND a.flow_id != '93' AND a.flow_id != '94' AND a.flow_id != '95' AND a.flow_id != '96')
            )result
            GROUP BY result.mdb_finish
            ORDER BY result.operator_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get statistik fa
    function get_sla_ir($params) {
        $sql = "
            SELECT 
                result.*,
                COUNT(*) 'total_proses',
                SEC_TO_TIME(SUM(detik)) 'total_time',
                SEC_TO_TIME(ROUND(SUM(detik) / COUNT(*))) 'response_time' 
            FROM
                (SELECT 
                  a.process_st,
                  a.action_st,
                  a.mdd,
                  a.mdb_finish,
                  a.mdd_finish,
                  b.user_name,
                  b.operator_name,
                  TIME_TO_SEC(TIMEDIFF(a.mdd_finish, a.mdd)) 'detik' 
            FROM
                  izin_process a 
                  LEFT JOIN com_user b 
                    ON b.user_id = a.mdb_finish 
                  LEFT JOIN izin_registrasi c 
                    ON c.registrasi_id = a.registrasi_id 
            WHERE a.process_st = 'approve' 
                  AND YEAR(c.mdd) = ?
                  AND MONTH(c.mdd) = ?
                  AND a.flow_id NOT IN (6, 16)
                  AND c.izin_group IN (1, 2, 3, 4, 5, 6, 7, 21, 22, 23, 24, 25, 26, 27) ) result 
            GROUP BY result.mdb_finish 
            ORDER BY SEC_TO_TIME(ROUND(SUM(detik) / COUNT(*))) DESC
            ";
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
