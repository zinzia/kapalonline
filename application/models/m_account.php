<?php

class m_account extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // load encrypt
        $this->load->library('encrypt');
    }

    // get user profil
    function get_user_profil($params) {
        $sql = "SELECT * FROM 
                (
                        SELECT a.*, c.role_id, c.role_nm, login_date, ip_address
                        FROM com_user a
                        INNER JOIN com_role_user b ON a.user_id = b.user_id
                        INNER JOIN com_role c ON c.role_id = b.role_id
                        LEFT JOIN com_user_login d ON a.user_id = d.user_id
                        WHERE a.user_id = ? AND c.role_id = ?
                        ORDER BY login_date DESC
                ) result 
                GROUP BY user_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get user profil
    function get_user_profil_airlines($params) {
        $sql = "SELECT * FROM 
                (
                        SELECT a.*, c.role_id, c.role_nm, login_date, ip_address, e.airlines_id, airlines_nm, 
                        f.airlines_flight_type, airlines_nationality, f.airlines_iata_cd, f.airlines_type
                        FROM com_user a
                        INNER JOIN com_role_user b ON a.user_id = b.user_id
                        INNER JOIN com_role c ON c.role_id = b.role_id
                        INNER JOIN com_user_airlines e ON a.user_id = e.user_id
                        INNER JOIN airlines f ON e.airlines_id = f.airlines_id
                        LEFT JOIN com_user_login d ON a.user_id = d.user_id
                        WHERE a.user_id = ? AND c.role_id = ? AND e.airlines_id = ?
                        ORDER BY login_date DESC
                ) result 
                GROUP BY user_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }
	
	// get user profil kapal
    function get_user_profil_kapal($params) {
        $sql = "SELECT * FROM 
                (
                        SELECT a.*, c.role_id, c.role_nm, login_date, ip_address
                        FROM com_user a
                        INNER JOIN com_role_user b ON a.user_id = b.user_id
                        INNER JOIN com_role c ON c.role_id = b.role_id
                        LEFT JOIN com_user_login d ON a.user_id = d.user_id
                        WHERE a.user_id = ? AND c.role_id = ?
                        ORDER BY login_date DESC
                ) result 
                GROUP BY user_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get user detail
    function get_user_detail_by_username($params) {
        $sql = "SELECT a.*, c.role_id, c.role_nm, c.default_page
                FROM com_user a
                LEFT JOIN com_role_user b ON a.user_id = b.user_id
                LEFT JOIN com_role c ON b.role_id = c.role_id
                WHERE user_name = ? AND c.portal_id = ? 
                AND c.role_id = ? 
                LIMIT 0, 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get user detail with auto role
    function get_user_detail_by_username_auto_role($params) {
        $sql = "SELECT a.*, c.role_id, c.role_nm, c.default_page
                FROM com_user a
                INNER JOIN com_role_user b ON a.user_id = b.user_id
                INNER JOIN com_role c ON b.role_id = c.role_id
                WHERE user_name = ? AND c.portal_id = ? AND reg_st = 1 ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return false;
        }
    }

    // get login
    function get_user_login($username, $password, $role_id, $portal) {
        // get hash key
        $result = $this->get_user_detail_by_username(array($username, $portal, $role_id));
        if (!empty($result)) {
            $password_decode = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            // get user
            if ($password_decode === md5($password)) {
                // cek authority then return id
                return $result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    // get login auto role
    function get_user_login_auto_role($username, $password, $portal) {
        // load encrypt
        $this->load->library('encrypt');
        // process
        // get hash key
        $result = $this->get_user_detail_by_username_auto_role(array($username, $portal));

		if (!empty($result)) {
            $password_decode = $this->encrypt->decode($result['user_pass'], $result['user_key']);
            // get user
            if ($password_decode === md5($password)) {
                // remove failed login session
                $this->tsession->unset_userdata('failed_login');
                // cek authority then return id
                return $result;
            } else {
                // jika salah password 3x lock status user tersebut
                $failed = $this->tsession->userdata('failed_login');
                if ($failed) {
                    $user_name = $failed['username'];
                    $count = intval($failed['count']) + 1;
                    $this->tsession->set_userdata('failed_login', array('username' => $user_name, 'count' => $count));
                    if ($count >= 3) {
                        // lock user account
                        // $this->db->update('com_user', array('lock_st' => 1), array('user_name' => $username));
                        // remove failed login session
                        $this->tsession->unset_userdata('failed_login');
                    }
                } else {
                    $this->tsession->set_userdata('failed_login', array('username' => $username, 'count' => '1'));
                }
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    // save user login
    function save_user_login($user_id, $remote_address) {
        // get today login
        $sql = "SELECT * FROM com_user_login WHERE user_id = ? AND DATE(login_date) = CURRENT_DATE";
        $query = $this->db->query($sql, array($user_id));
        if ($query->num_rows() > 0) {
            // tidak perlu diinputkan lagi
            return false;
        } else {
            $sql = "INSERT INTO com_user_login (user_id, login_date, ip_address) VALUES (?, NOW(), ?)";
            return $this->db->query($sql, array($user_id, $remote_address));
        }
    }

    // save user logout
    function update_user_logout($user_id) {
        // update by this date
        $sql = "UPDATE com_user_login SET logout_date = NOW() WHERE user_id = ? AND DATE(login_date) = CURRENT_DATE";
        return $this->db->query($sql, $user_id);
    }

    /*
     * Data Pribadi Pengguna
     */

    // check airlines
    function get_default_airlines($params) {
        $sql = "SELECT * FROM com_user_airlines WHERE user_id = ? LIMIT 0, 1";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['airlines_id'];
        } else {
            return array();
        }
    }

    // check username
    function is_exist_username($params) {
        $sql = "SELECT * FROM com_user WHERE user_name = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }

    // check mail
    function is_exist_email($params) {
        $sql = "SELECT * FROM com_user WHERE user_mail = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }
	
	// check mail
    function is_notexist_email($params) {
        $sql = "SELECT * FROM com_user WHERE user_mail = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return false;
        } else {
            return true;
        }
    }
	
	// check key
    function is_exist_mailkey($params) {
        $sql = "SELECT user_mail FROM com_user WHERE user_mail = ? AND user_key = ? ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }

	// check active
    function is_active_mailkey($params) {
        $sql = "SELECT user_mail FROM com_user WHERE user_mail = ? AND reg_st = ? ";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $query->free_result();
            return false;
        } else {
            return true;
        }
    }
	
    // check password
    function is_exist_password($user_id, $password) {
        $sql = "SELECT * FROM com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $user_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // --
        $password_decode = $this->encrypt->decode($result['user_pass'], $result['user_key']);
        if ($password_decode == md5($password)) {
            return true;
        } else {
            return false;
        }
    }

    // get user account
    function get_user_account($params) {
        $sql = "SELECT * FROM com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get data pribadi
    function get_data_pribadi($params) {
        $sql = "SELECT a.*, c.role_id, c.role_nm
                FROM com_user a 
                INNER JOIN com_role_user b ON a.user_id = b.user_id
                INNER JOIN com_role c ON b.role_id = c.role_id  
                WHERE a.user_id = ?
                GROUP BY a.user_id";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update data account
    function update_data_account($params) {
        $sql = "SELECT * FROM com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $params[2]);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // encode password
        $params[1] = $this->encrypt->encode($params[1], $result['user_key']);
        // update 
        $sql = "UPDATE com_user SET user_name = ?, user_pass = ? WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }
	
	// update data account
    function reset_password($params) {
        $sql = "SELECT * FROM com_user WHERE user_mail = ?";
        $query = $this->db->query($sql, $params[1]);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // encode password
        $params[0] = $this->encrypt->encode($params[0], $result['user_key']);
        // update 
        $sql = "UPDATE com_user SET user_pass = ? WHERE user_mail = ?";
        return $this->db->query($sql, $params);
    }

    // roles
    function get_all_roles_by_portal($portal_id) {
        $sql = "SELECT * FROM com_role WHERE portal_id = ?";
        $query = $this->db->query($sql, $portal_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail roles
    function get_detail_roles_by_id($params) {
        $sql = "SELECT * FROM com_role WHERE role_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update permissions
    function update_permissions($params) {
        // delete by user & portal
        $sql = "DELETE a.* FROM com_role_user a
                INNER JOIN com_role b ON a.role_id = b.role_id
                WHERE a.user_id = ? AND b.portal_id = 2";
        $this->db->query($sql, $params);
        // insert
        $sql = "INSERT INTO com_role_user (user_id, role_id) VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    // roles
    function get_all_roles_by_users($params) {
        $sql = "SELECT * FROM com_role a
                INNER JOIN com_role_user b ON a.role_id = b.role_id
                WHERE portal_id = ? AND b.user_id = ?
                ORDER BY a.role_nm ASC";
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
