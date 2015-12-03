<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

// --

class adminpermissions extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load
        $this->load->model('m_settings');
        $this->load->library('tnotification');
        // load javascript
        $this->smarty->load_javascript("resource/js/jquery/jquery-1.10.2.min.js");
        // set global variable
    }

    // list role
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "settings/role/access.html");
        // get data
        $this->smarty->assign("rs_id", $this->m_settings->get_all_roles());
        // url
        $this->smarty->assign("url_list", site_url("settings/adminpermissions"));
        // output
        parent::display();
    }

    // list menu by role
    public function access_update($role_id) {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "settings/role/access_update.html");
        // get detail role
        $result = $this->m_settings->get_detail_role_by_id($role_id);
        if (!empty($result)) {
            $this->smarty->assign("result", $result);
            // get data menu
            $list_menu = self::_display_menu($result['portal_id'], $role_id, 0, "");
            $this->smarty->assign("list_menu", $list_menu);
        }
        // notification
        $this->tnotification->display_notification();
        // output
        parent::display();
    }

    private function _display_menu($portal_id, $role_id, $parent_id, $indent) {
        $html = "";
        // get data
        $params = array($role_id, $portal_id, $parent_id);
        $rs_id = $this->m_settings->get_all_menu_selected_by_parent($params);
        if (!empty($rs_id)) {
            $no = 0;
            $indent .= "--- ";
            foreach ($rs_id as $rec) {
                $role_tp = array("C" => "0", "R" => "0", "U" => "0", "D" => "0");
                $i = 0;
                foreach ($role_tp as $rule => $val) {
                    $N = substr($rec['role_tp'], $i, 1);
                    $role_tp[$rule] = $N;
                    $i++;
                }
                $checked = "";
                if (array_sum($role_tp) > 0) {
                    $checked = "checked='true'";
                }
                // parse
                $html .= "<tr>";
                $html .= "<td align='center'><input type='checkbox' class='checked-all' value='" . $rec['nav_id'] . "' " . $checked . " /></td>";
                $html .= "<td>" . $indent . $rec['nav_title'] . "</td>";
                $html .= "<td align='center'><input class='r" . $rec['nav_id'] . "' type='checkbox' name='rules[" . $rec['nav_id'] . "][C]' value='1' " . ($role_tp['C'] == "1" ? 'checked="true"' : '') . " /></td>";
                $html .= "<td align='center'><input class='r" . $rec['nav_id'] . "' type='checkbox' name='rules[" . $rec['nav_id'] . "][R]' value='1' " . ($role_tp['R'] == "1" ? 'checked="true"' : '') . " /></td>";
                $html .= "<td align='center'><input class='r" . $rec['nav_id'] . "' type='checkbox' name='rules[" . $rec['nav_id'] . "][U]' value='1' " . ($role_tp['U'] == "1" ? 'checked="true"' : '') . " /></td>";
                $html .= "<td align='center'><input class='r" . $rec['nav_id'] . "' type='checkbox' name='rules[" . $rec['nav_id'] . "][D]' value='1' " . ($role_tp['D'] == "1" ? 'checked="true"' : '') . " /></td>";
                $html .= "</tr>";
                $html .= $this->_display_menu($portal_id, $role_id, $rec['nav_id'], $indent);
                $no++;
            }
        }
        return $html;
    }

    // process update
    public function process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('role_id', 'Role ID', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // delete
            $params = array($this->input->post('role_id'));
            $this->m_settings->delete_role_menu($params);
            // insert
            $rules = $this->input->post('rules');
            if (is_array($rules)) {
                foreach ($rules as $nav => $rule) {
                    // get rule tipe
                    $role_tp = array("C" => "0", "R" => "0", "U" => "0", "D" => "0");
                    $i = 0;
                    foreach ($role_tp as $tp => $val) {
                        if (isset($rule[$tp])) {
                            $role_tp[$tp] = $rule[$tp];
                        }
                        $i++;
                    }
                    $result = implode("", $role_tp);
                    // insert
                    $params = array($this->input->post('role_id'), $nav, $result);
                    $this->m_settings->insert_role_menu($params);
                }
            }
            $this->tnotification->sent_notification("success", "Data berhasil disimpan");
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("settings/adminpermissions/access_update/" . $this->input->post('role_id'));
    }

}