<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class m_pengajuan extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // get list pengajuan
    function get_list_pengajuan() {
        $sql = "SELECT a.*, b.services_nm 
            FROM fa_rules_registration a
            LEFT JOIN services_code b ON b.services_cd = a.services_cd";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list services
    function get_list_services() {
        $sql = "SELECT * 
            FROM services_code";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get pengajuan by id
    function get_pengajuan_by_id($params) {
        $sql = "SELECT a.*, b.services_nm 
            FROM fa_rules_registration a
            LEFT JOIN services_code b ON b.services_cd = a.services_cd 
            WHERE a.field_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert
    function insert($params) {
        return $this->db->insert("fa_rules_registration", $params);
    }

    // update
    function update($params, $where) {
        return $this->db->update("fa_rules_registration", $params, $where);
    }

    // delete
    function delete($params) {
        return $this->db->delete("fa_rules_registration", $params);
    }

}
