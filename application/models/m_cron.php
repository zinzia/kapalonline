<?php

class m_cron extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get member mail
    function get_registration_overdue() {
        $sql = "SELECT * 
            FROM 
            (
                SELECT a.data_id, c.document_no, b.role_id, b.sla_time, b.sla_st, c.mdd, a.mdd + INTERVAL b.sla_time HOUR'sla' 
                FROM fa_process a 
                LEFT JOIN fa_flow b ON b.flow_id = a.flow_id 
                LEFT JOIN fa_data c ON c.data_id = a.data_id 
                WHERE a.process_st = 'waiting'
            )rs1 
            LEFT JOIN 
            (
                SELECT rs2.* 
                FROM 
                (
                    SELECT a.user_id, a.user_mail, b.role_id 
                    FROM com_user a 
                    LEFT JOIN com_role_user b ON b.user_id = a.user_id 
                    LEFT JOIN com_user_airlines c ON c.user_id = a.user_id 
                    WHERE !ISNULL(b.role_id) AND a.user_st = 'operator' 
                    GROUP BY a.user_id, b.role_id
                )rs2 
            )rs2 ON rs2.role_id = rs1.role_id 
            WHERE !ISNULL(rs2.user_id) AND NOW() > rs1.sla 
            GROUP BY rs1.data_id, rs2.user_id";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_task");
            $this->load->model("m_preferences");
            $mail = $this->m_preferences->get_mail();
            $detail = explode(",", $mail['pref_value']);
            $host = $mail['pref_nm'];
            $port = $detail[0];
            $user = $detail[1];
            $pass = $detail[2];
            // load email
            $this->load->library('email');
            // init
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = $host;
            $config['smtp_port'] = $port;
            $config['smtp_timeout'] = '7';
            $config['smtp_user'] = $user;
            $config['smtp_pass'] = $pass;
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['validation'] = TRUE; // bool whether to validate email or not
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $old_data_id = "";
            foreach ($result as $value) {
                $html = "<b>PERMOHONAN FLIGHT APPROVAL</b><br />";
                $html .= "<b>NOMOR DOKUMEN : " . $value['document_no'] . "</b><br />";
                $html .= "<b>DIAJUKAN PADA TANGGAL : " . $this->datetimemanipulation->get_full_date($value['mdd']) . "</b><br />";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($value['user_mail']);
                $this->email->subject($value['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // update izin active
    function update_izin_active() {
        $sql = "UPDATE izin_rute SET izin_active = '0' 
            WHERE izin_expired_date < DATE(NOW())";
        $query = $this->db->query($sql);
    }

}
