<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/AdminBase.php' );

// --

class adminmenu extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load
        $this->load->model('m_settings');
        $this->load->library('tnotification');
        // set global variable
    //
    }

    // list portal menu
    public function index() {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "settings/menu/list.html");
        // get data
        $this->smarty->assign("rs_id", $this->m_settings->get_all_portal_menu());
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // list navigasi by portal
    public function navigation($portal_id) {
        // set page rules
        $this->_set_page_rule("R");
        // set template content
        $this->smarty->assign("template_content", "settings/menu/navigation.html");
        // get data portal
        $this->smarty->assign("portal", $this->m_settings->get_portal_by_id($portal_id));
        // get data menu
        $html = $this->_get_menu_by_portal($portal_id, 0, "");
        $this->smarty->assign("rs_id", $html);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    private function _get_menu_by_portal($portal_id, $parent_id, $indent) {
        $html = "";
        $params = array($portal_id, $parent_id);
        $rs_id = $this->m_settings->get_all_menu_by_parent($params);
        if ($rs_id) {
            $no = 0;
            $indent .= "--- ";
            foreach ($rs_id as $rec) {
                // url
                $url_edit = site_url('settings/adminmenu/edit/' . $portal_id . '/' . $rec['nav_id']);
                $url_hapus = site_url('settings/adminmenu/hapus/' . $portal_id . '/' . $rec['nav_id']);
                // icon
                $icon = "resource/doc/images/nav/default.png";
                if (is_file("resource/doc/images/nav/" . $rec['nav_icon'])) {
                    $icon = "resource/doc/images/nav/" . $rec['nav_icon'];
                }
                // parse
                $html .= "<tr>";
                $html .= "<td align='center'><img src='" . base_url() . $icon . "' alt='' height='16px' /></td>";
                $html .= "<td>" . $indent . $rec['nav_title'] . "</td>";
                $html .= "<td>" . $rec['nav_url'] . "</td>";
                $html .= "<td align='center'>" . $rec['active_st'] . "</td>";
                $html .= "<td align='center'>" . $rec['display_st'] . "</td>";
                $html .= "<td align='center'>";
                $html .= "<a href='" . $url_edit . "' class='button-edit'>Edit</a>";
                $html .= "<a href='" . $url_hapus . "' class='button-hapus'>Hapus</a>";
                $html .= "</td>";
                $html .= "</tr>";
                $html .= $this->_get_menu_by_portal($rec['portal_id'], $rec['nav_id'], $indent);
                $no++;
            }
        }
        return $html;
    }

    // form tambah menu
    public function add($portal_id) {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "settings/menu/add.html");
        // get last parent id
        $result = $this->tnotification->get_field_data();
        $parent_selected = isset($result['parent_id']['postdata']) ? $result['parent_id']['postdata'] : 0;
        // get data portal
        $this->smarty->assign("portal", $this->m_settings->get_portal_by_id($portal_id));
        // get list parent
        $html = $this->_get_menu_selectbox_by_portal($portal_id, 0, "", $parent_selected);
        $this->smarty->assign("list_parent", $html);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    private function _get_menu_selectbox_by_portal($portal_id, $parent_id, $indent, $parent_selected) {
        $html = "";
        $params = array($portal_id, $parent_id);
        $rs_id = $this->m_settings->get_all_menu_by_parent($params);
        if ($rs_id) {
            $no = 0;
            $indent .= "--- ";
            foreach ($rs_id as $rec) {
                // selected
                $selected = ($parent_selected == $rec['nav_id']) ? 'selected="selected"' : '';
                // parse
                $html .= "<option value='" . $rec['nav_id'] . "' " . $selected . ">";
                $html .= $indent . $rec['nav_title'];
                $html .= "</option>";
                $html .= $this->_get_menu_selectbox_by_portal($rec['portal_id'], $rec['nav_id'], $indent, $parent_selected);
                $no++;
            }
        }
        return $html;
    }

    // proses tambah
    public function process_add() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('portal_id', 'Web Portal', 'trim|required');
        $this->tnotification->set_rules('parent_id', 'Induk Menu', '');
        $this->tnotification->set_rules('nav_title', 'Judul', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('nav_desc', 'Deskripsi', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('nav_url', 'Alamat Menu', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('nav_no', 'Urutan', 'trim|required|max_length[4]');
        $this->tnotification->set_rules('active_st', 'Digunakan', 'trim|required');
        $this->tnotification->set_rules('display_st', 'Ditampilkan', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array($this->input->post('portal_id'), $this->input->post('parent_id'), $this->input->post('nav_title'),
                $this->input->post('nav_desc'), $this->input->post('nav_url'), $this->input->post('nav_no'),
                $this->input->post('active_st'), $this->input->post('display_st'), $this->com_user['user_id']);
            // insert
            if ($this->m_settings->insert_menu($params)) {
                // upload icon
                if (!empty($_FILES['nav_icon']['tmp_name'])) {
                    // load
                    $this->load->library('tupload');
                    // last id
                    $id_nav = $this->m_settings->get_last_inserted_id();
                    // upload config
                    $config['upload_path'] = 'resource/doc/images/nav/';
                    $config['allowed_types'] = 'gif|jpg|png';
//                    $config['max_size'] = '20';
//                    $config['max_width'] = '36';
//                    $config['max_height'] = '36';
                    $config['file_name'] = $id_nav;
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload_image('nav_icon', false, 36)) {
                        $data = $this->tupload->data();
                        $this->m_settings->update_icon(array($data['file_name'], $id_nav));
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("settings/adminmenu/add/" . $this->input->post('portal_id'));
    }

    // form edit
    public function edit($portal_id, $nav_id) {
        // set page rules
        $this->_set_page_rule("U");
        // set template content
        $this->smarty->assign("template_content", "settings/menu/edit.html");
        // get data portal
        $this->smarty->assign("portal", $this->m_settings->get_portal_by_id($portal_id));
        // get data
        $result = $this->m_settings->get_detail_menu_by_id($nav_id);
        $this->smarty->assign("result", $result);
        // icon
        $icon = "resource/doc/images/nav/default.png";
        if (!empty($result)) {
            if (is_file("resource/doc/images/nav/" . $result['nav_icon'])) {
                $icon = "resource/doc/images/nav/" . $result['nav_icon'];
            }
        }
        $this->smarty->assign("nav_icon", $icon);
        // get last parent id
        if (!empty($result)) {
            $result_field = $this->tnotification->get_field_data();
            $parent_selected = isset($result_field['parent_id']['postdata']) ? $result_field['parent_id']['postdata'] : $result['parent_id'];
        }
        // get list parent
        $html = $this->_get_menu_selectbox_by_portal($portal_id, 0, "", $parent_selected);
        $this->smarty->assign("list_parent", $html);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // edit process
    public function process_update() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('nav_id', 'Menu ID', 'trim|required');
        $this->tnotification->set_rules('portal_id', 'Web Portal', 'trim|required');
        $this->tnotification->set_rules('parent_id', 'Induk Menu', '');
        $this->tnotification->set_rules('nav_title', 'Judul', 'trim|required|max_length[100]');
        $this->tnotification->set_rules('nav_desc', 'Deskripsi', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('nav_url', 'Alamat Menu', 'trim|required|max_length[255]');
        $this->tnotification->set_rules('nav_no', 'Urutan', 'trim|required|max_length[4]');
        $this->tnotification->set_rules('active_st', 'Digunakan', 'trim|required');
        $this->tnotification->set_rules('display_st', 'Ditampilkan', 'trim|required');
        // jika parent dan nav sama
        if ($this->input->post('parent_id') == $this->input->post('nav_id')) {
            $this->tnotification->set_error_message("Induk Menu tidak boleh pada diri sendiri");
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            // params
            $params = array($this->input->post('portal_id'), $this->input->post('parent_id'), $this->input->post('nav_title'),
                $this->input->post('nav_desc'), $this->input->post('nav_url'), $this->input->post('nav_no'),
                $this->input->post('active_st'), $this->input->post('display_st'), $this->com_user['user_id'],
                $this->input->post('nav_id'));
            // update
            if ($this->m_settings->update_menu($params)) {
                // upload icon
                if (!empty($_FILES['nav_icon']['tmp_name'])) {
                    // load
                    $this->load->library('tupload');
                    // last id
                    $id_nav = $this->input->post('nav_id');
                    // upload config
                    $config['upload_path'] = 'resource/doc/images/nav/';
                    $config['allowed_types'] = 'gif|jpg|png';
//                    $config['max_size'] = '20';
//                    $config['max_width'] = '36';
//                    $config['max_height'] = '36';
                    $config['file_name'] = $id_nav;
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload_image('nav_icon', false, 36)) {
                        $data = $this->tupload->data();
                        $this->m_settings->update_icon(array($data['file_name'], $id_nav));
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("settings/adminmenu/edit/" . $this->input->post('portal_id') . '/' . $this->input->post('nav_id'));
    }

    // form hapus
    public function hapus($portal_id, $nav_id) {
        // set page rules
        $this->_set_page_rule("D");
        // set template content
        $this->smarty->assign("template_content", "settings/menu/hapus.html");
        // url
        $this->smarty->assign("url_add", site_url('settings/adminmenu/add/' . $portal_id));
        $this->smarty->assign("url_menu", site_url('settings/adminmenu/navigation/' . $portal_id));
        $this->smarty->assign("url_list", site_url('settings/adminmenu'));
        $this->smarty->assign("url_process", site_url("settings/adminmenu/process_delete"));
        // get data portal
        $this->smarty->assign("portal", $this->m_settings->get_portal_by_id($portal_id));
        // get data
        $result = $this->m_settings->get_detail_menu_by_id($nav_id);
        $this->smarty->assign("result", $result);
        // icon
        $icon = "resource/doc/images/nav/default.png";
        if (!empty($result)) {
            if (is_file("resource/doc/images/nav/" . $result['nav_icon'])) {
                $icon = "resource/doc/images/nav/" . $result['nav_icon'];
            }
        }
        $this->smarty->assign("nav_icon", $icon);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // hapus process
    public function process_delete() {
        // set page rules
        $this->_set_page_rule("D");
        // cek input
        $this->tnotification->set_rules('nav_id', 'Menu ID', 'trim|required');
        $this->tnotification->set_rules('portal_id', 'Web Portal', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('nav_id'));
            // insert
            if ($this->m_settings->delete_menu($params)) {
                // delete icon
                $icon = "resource/doc/images/nav/" . $this->input->post('nav_icon');
                if (is_file($icon)) {
                    unlink($icon);
                }
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("settings/adminmenu/navigation/" . $this->input->post('portal_id'));
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal dihapus");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal dihapus");
        }
        // default redirect
        redirect("settings/adminmenu/hapus/" . $this->input->post('portal_id') . '/' . $this->input->post('nav_id'));
    }

}