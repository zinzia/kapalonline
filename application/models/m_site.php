<?php

// class for core system
class m_site extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    // get site data
    function get_site_data_by_id($id_group) {
        $sql = "SELECT * FROM com_portal WHERE portal_id = ?";
        $query = $this->db->query($sql, $id_group);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get current page
    function get_current_page($params) {
        $sql = "SELECT * FROM com_menu WHERE nav_url = ? ORDER BY nav_no DESC LIMIT 0,1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get menu by id
    function get_menu_by_id($params) {
        $sql = "SELECT * FROM com_menu WHERE nav_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get navigation by user and parent nav
    function get_navigation_user_by_parent($params) {
        $sql = "SELECT * FROM com_menu a
                INNER JOIN com_role_menu b ON a.nav_id = b.nav_id
                INNER JOIN com_role_user c ON b.role_id = c.role_id
                WHERE a.portal_id = ? AND c.role_id = ? AND c.user_id = ? AND parent_id = ? AND active_st = '1' AND display_st = '1'
                ORDER BY nav_no ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get navigation by parent nav
    function get_navigation_by_parent($params) {
        $sql = "SELECT a.*, lang_label FROM com_menu a
                LEFT JOIN com_menu_lang b ON a.nav_id = b.nav_id
                WHERE portal_id = ? AND parent_id = ? AND active_st = '1' AND display_st = '1' AND lang_id = ?
                ORDER BY nav_no ASC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get navigation by parent nav
    function get_navigation_by_parent_desc($params) {
        $sql = "SELECT a.*, lang_label FROM com_menu a
                LEFT JOIN com_menu_lang b ON a.nav_id = b.nav_id
                WHERE portal_id = ? AND parent_id = ? AND active_st = '1' AND display_st = '1' AND lang_id = ?
                ORDER BY nav_no DESC";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get navigation by nav id
    function get_parent_group_by_idnav($int_parent, $limit) {
        $sql = "SELECT a.nav_id, a.parent_id FROM com_menu a WHERE a.nav_id = ?
                ORDER BY a.nav_no DESC LIMIT 0, 1";
        $query = $this->db->query($sql, array($int_parent));
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            if ($result['parent_id'] == $limit) {
                return $result['nav_id'];
            } else {
                return self::get_parent_group_by_idnav($result['parent_id'], $limit);
            }
        } else {
            return $int_parent;
        }
    }

    // get user authority
    function get_user_authority($user_id, $id_group) {
        $sql = "SELECT a.user_id FROM com_user a
                INNER JOIN com_role_user b ON a.user_id = b.user_id
                INNER JOIN com_role c ON b.role_id = c.role_id
                WHERE a.user_id = ? AND c.portal_id = ?";
        $query = $this->db->query($sql, array($user_id, $id_group));
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['user_id'];
        } else {
            return false;
        }
    }

    // get user authority by navigation
    function get_user_authority_by_nav($params) {
        $sql = "SELECT b.* FROM com_menu a
                INNER JOIN com_role_menu b ON a.nav_id = b.nav_id
                INNER JOIN com_role c ON b.role_id = c.role_id
                INNER JOIN com_role_user d ON c.role_id = d.role_id
                WHERE c.role_id = ? AND d.user_id = ? AND b.nav_id = ? AND active_st = '1' AND a.portal_id = ?";
        $query = $this->db->query($sql, $params);
        // echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['role_tp'];
        } else {
            return false;
        }
    }

}
