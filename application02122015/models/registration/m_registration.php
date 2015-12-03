<?php

class m_registration extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // load encrypt
        $this->load->library('encrypt');
    }

    // get last inserted id
    function get_last_inserted_id() {
        return $this->db->insert_id();
    }

    // update data account
    function update_data_account($params) {
        $sql = "SELECT * FROM com_user WHERE user_id = ?";
        $query = $this->db->query($sql, $params[3]);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
        } else {
            return false;
        }
        // encode password
        $params[1] = $this->encrypt->encode($params[1], $result['user_key']);
        // update 
        $sql = "UPDATE com_user SET user_name = ?, user_pass = ?, lock_st = ?
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }


	// get user detail
    function get_user_detail_by_email($params) {
        $sql = "SELECT a.user_key
                FROM com_user a
				WHERE user_mail = ?
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
	
	// update registration status
    function update_reg_status($params) {
        $sql = "UPDATE com_user SET reg_st = 1
                WHERE user_mail = ?";
        return $this->db->query($sql, $params);
    }
	
    // list all airlines asing
    function get_all_airlines_asing() {
        $sql = "SELECT * FROM airlines WHERE airlines_nationality = 'asing' ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list all airlines nasional
    function get_all_airlines_nasional() {
        $sql = "SELECT * FROM airlines WHERE airlines_nationality = 'nasional' ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list all airport
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

    // list operator by status
    function get_all_operator_by_st($params) {
        $sql = "SELECT * FROM com_user b
                INNER JOIN com_role_user c ON b.user_id = c.user_id
                INNER JOIN com_role d ON c.role_id = d.role_id
                WHERE d.portal_id = ? AND b.user_st = ?
                GROUP BY b.user_id
                ORDER BY b.operator_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // list operator
    function get_all_operator($params) {
        $sql = "SELECT * FROM com_user b
                INNER JOIN com_role_user c ON b.user_id = c.user_id
                INNER JOIN com_role d ON c.role_id = d.role_id
                WHERE d.portal_id = 6
                GROUP BY b.user_id
                ORDER BY b.operator_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list airlines by user
    function get_all_airlines_by_user($params) {
        $sql = "SELECT airlines_id FROM com_user_airlines a
                INNER JOIN com_user b ON a.user_id = b.user_id
                WHERE a.user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            $airlines_selected = array();
            foreach ($result as $rec) {
                $airlines_selected[] = $rec['airlines_id'];
            }
            return $airlines_selected;
        } else {
            return array();
        }
    }

    // get list airport by user
    function get_all_airport_by_user($params) {
        $sql = "SELECT airport_id FROM com_user_bandara a
                INNER JOIN com_user b ON a.user_id = b.user_id
                WHERE a.user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            $airlines_selected = array();
            foreach ($result as $rec) {
                $airlines_selected[] = $rec['airport_id'];
            }
            return $airlines_selected;
        } else {
            return array();
        }
    }

    // get list airlines by user
    function get_list_airlines_by_user($params) {
        $sql = "SELECT c.airlines_id, c.airlines_nm, c.airlines_brand, c.airlines_iata_cd 
                FROM com_user_airlines a
                INNER JOIN com_user b ON a.user_id = b.user_id
                INNER JOIN airlines c ON a.airlines_id = c.airlines_id
                WHERE a.user_id = ?
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

    // check_airlines_user
    function check_airlines_user($params) {
        $sql = "SELECT c.airlines_id, c.airlines_nm, c.airlines_iata_cd 
                FROM com_user_airlines a
                INNER JOIN com_user b ON a.user_id = b.user_id
                INNER JOIN airlines c ON a.airlines_id = c.airlines_id
                WHERE a.user_id = ? AND c.airlines_id = ?
                ORDER BY airlines_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return true;
        } else {
            return false;
        }
    }

    // get list roles by user
    function get_all_roles_by_user($params) {
        $sql = "SELECT a.* FROM com_role a
                INNER JOIN com_role_user b ON a.role_id = b.role_id
                WHERE user_id = ? AND portal_id = ? 
                ORDER BY role_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            $role_selected = array();
            foreach ($result as $rec) {
                $role_selected[] = $rec['role_id'];
            }
            return $role_selected;
        } else {
            return array();
        }
    }

    // list role
    function get_all_roles_by_portal($params) {
        $sql = "SELECT * FROM com_role 
                WHERE portal_id = ?
                ORDER BY role_nm ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // detail
    function get_operator_detail_by_id($params) {
        $sql = "SELECT a.*, role_id FROM com_user a
                LEFT JOIN com_role_user b ON a.user_id = b.user_id
                WHERE a.user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // insert users
    function insert_user($params) {
        $sql = "INSERT INTO com_user (user_name, user_pass, user_key, user_mail, lock_st, mdb, mdd)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // insert member
    function insert_member($params) {
        $sql = "INSERT INTO com_user (user_name, user_pass, user_key, user_st, lock_st, mdb, mdd)
                VALUES (?, ?, ?, 'member', '0', ?, NOW())";
        return $this->db->query($sql, $params);
    }

    // insert operator
    function insert_operator($params) {
        $sql = "UPDATE com_user SET operator_name = ?, operator_phone = ?, user_mail = ?, operator_address = ?, 
                operator_birth_place = ?, operator_birth_day = ?, operator_gender = ?, member_status = ?,
                jabatan = ?, sub_direktorat = ?
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert stakeholder
    function insert_stakeholder($params) {
        $sql = "UPDATE com_user SET operator_name = ?, operator_phone = ?, user_mail = ?, operator_address = ?, 
                operator_birth_place = ?, operator_birth_day = ?, operator_gender = ?, member_status = ?,
                jabatan = ?, sub_direktorat = ?
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete user roles
    function delete_user_role($params) {
        $sql = "DELETE a.* FROM com_role_user a
                INNER JOIN com_role b ON a.role_id = b.role_id
                WHERE user_id = ? AND portal_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert user roles
    function insert_user_role($params) {
        $sql = "INSERT INTO com_role_user VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }
	
    // update operator
    function update_data_pribadi($params) {
        $sql = "UPDATE com_user SET operator_name = ?, operator_gender = ?, 
                operator_birth_place = ?, operator_birth_day = ?,
                operator_address = ?, operator_phone = ?,
                user_mail = ?, member_status = ?, jabatan = ?, sub_direktorat = ?
                WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // update operator foto
    function update_foto_pribadi($params) {
        $sql = "UPDATE com_user SET operator_photo = ? WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete operator
    function delete_operator($params) {
        $sql = "DELETE FROM com_user WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete user airlines
    function delete_user_airlines($params) {
        $sql = "DELETE FROM com_user_airlines WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // delete user airport
    function delete_user_airport($params) {
        $sql = "DELETE FROM com_user_bandara WHERE user_id = ?";
        return $this->db->query($sql, $params);
    }

    // insert user airlines
    function insert_user_airlines($params) {
        $sql = "INSERT INTO com_user_airlines VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    // insert user airport
    function insert_user_airport($params) {
        $sql = "INSERT INTO com_user_bandara VALUES (?, ?)";
        return $this->db->query($sql, $params);
    }

    // get total member
    function get_total_member($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM 
                (
                    SELECT a.user_id
                    FROM com_user a
                    INNER JOIN com_user_airlines b ON a.user_id = b.user_id
                    WHERE operator_name LIKE ? AND member_status LIKE ? AND b.airlines_id LIKE ?
                    AND user_st = 'member'
                    GROUP BY a.user_id
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

    // get list member
    function get_list_member($params) {
        $sql = "SELECT *
                FROM 
                (
                    SELECT a.*, COUNT(airlines_id)'total_airlines'
                    FROM com_user a
                    INNER JOIN com_user_airlines b ON a.user_id = b.user_id
                    WHERE operator_name LIKE ? AND member_status LIKE ? AND b.airlines_id LIKE ?
                    AND user_st = 'member'
                    GROUP BY a.user_id
                ) result
                ORDER BY operator_name ASC
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

    // get total operator
    function get_total_operator($params) {
        $sql = "SELECT COUNT(*)'total' 
                FROM com_user a WHERE operator_name LIKE ? 
                AND sub_direktorat LIKE ? 
                AND user_st = 'operator'";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get total stakeholder
    function get_total_stakeholder($params) {
        $sql = "SELECT COUNT(*)'total'
                FROM 
                (
                    SELECT a.*, COUNT(airport_id)'total_airport'
                    FROM com_user a
                    INNER JOIN com_user_bandara b ON a.user_id = b.user_id
                    WHERE operator_name LIKE ? AND member_status LIKE ? AND b.airport_id LIKE ?
                    AND user_st = 'member'
                    GROUP BY a.user_id
                ) result
                ORDER BY operator_name ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result['total'];
        } else {
            return 0;
        }
    }

    // get list operator
    function get_list_operator($params) {
        $sql = "SELECT * 
                FROM 
                (
                    SELECT a.*, COUNT(airlines_id)'total_airlines'
                    FROM com_user a
                    LEFT JOIN com_user_airlines b ON a.user_id = b.user_id
                    WHERE operator_name LIKE ? AND sub_direktorat LIKE ? AND user_st = 'operator' AND operator_name != '' 
                    GROUP BY a.user_id
                ) result
                ORDER BY operator_name ASC
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

    // get list stakeholder
    function get_list_stakeholder($params) {
        $sql = "SELECT result.*, COUNT(airport_id) 'total_airport' 
                FROM 
                (
                    SELECT a.*, COUNT(airport_id)'total_airport'
                    FROM com_user a
                    INNER JOIN com_user_bandara b ON a.user_id = b.user_id
                    WHERE operator_name LIKE ? AND member_status LIKE ? AND b.airport_id LIKE ?
                    AND user_st = 'member'
                    GROUP BY a.user_id
                ) result
                LEFT JOIN com_user_bandara b ON b.user_id = result.user_id
                GROUP BY result.user_id
                ORDER BY operator_name ASC
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

    // get list sub direktorat
    function get_all_direktorat_angkutan_udara() {
        $sql = "SELECT * FROM com_preferences WHERE pref_group = 'direktorat' AND pref_nm = 'sub_direktorat'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail operator
    function get_detail_operator($params) {
        $sql = "SELECT * 
            FROM com_user 
            WHERE user_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail role
    function get_detail_role($params) {
        $sql = "SELECT * 
            FROM com_role 
            WHERE role_id = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // update all password
    function update_password() {
        // get all user
        $sql = "SELECT * FROM com_user";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $rs_id = $query->result_array();
            $query->free_result();
            // update 1 per 1
            foreach ($rs_id as $result) {
                // decode
                $password_decode = $this->encrypt->decode($result['user_pass'], $result['user_key']);
                // encode
                $password_encode = $this->encrypt->encode(md5($password_decode), $result['user_key']);
                // update
                $params = array(
                    'user_pass' => $password_encode,
                );
                // where
                $this->db->where(
                        array(
                            'user_id' => $result['user_id'],
                        )
                );
                // execute
                $this->db->update('com_user', $params);
            }
        }
        // return false
        return false;
    }

}
