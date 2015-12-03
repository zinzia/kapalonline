<?php

class ApplicationBase extends CI_Controller {

    // init base variable
    protected $portal_id;
    protected $com_portal;
    protected $com_user;
    protected $nav_id = 0;
    protected $parent_id = 0;
    protected $parent_selected = 0;
    protected $role_tp = array();
    protected $bahasa = array();

    public function __construct() {
        // load basic controller
        parent::__construct();
        // load app data
        $this->base_load_app();
        // view app data
        $this->base_view_app();
    }

    /*
     * Method pengolah base load
     * diperbolehkan untuk dioverride pada class anaknya
     */

    protected function base_load_app() {
        // load themes (themes default : default)
        $this->smarty->load_themes("online");
        // load library
        $this->load->library("datetimemanipulation");
        $this->smarty->assign("dtm", $this->datetimemanipulation);
        // get default lang
        $this->load->model("m_lang");
    }

    /*
     * Method pengolah base view
     * diperbolehkan untuk dioverride pada class anaknya
     */

    protected function base_view_app() {
        $this->smarty->assign("config", $this->config);
        // display site title
        self::_display_site_title();
        // display current page
        self::_display_current_page();
        // display top navigation
        self::_display_top_navigation();
        /// --
        $this->smarty->assign("nav_active", $this->nav_id);
        // tanggal
        $now = $this->datetimemanipulation->get_date_indonesia(date("Y-m-d"), 'in');
        $date_now = $now['hari'] . ", " . $now['tanggal'] . " " . $now['bulan'] . " " . $now['tahun'];
        $this->smarty->assign("tanggal", $date_now);
    }

    /*
     * Method layouting base document
     * diperbolehkan untuk dioverride pada class anaknya
     */

    protected function display($tmpl_name = 'base/online/document.html') {
        // set template
        $this->smarty->display($tmpl_name);
    }

    //
    // base private method here
    // prefix ( _ )
    // site title
    private function _display_site_title() {
        $this->portal_id = $this->config->item('portal_online');
        // site data
        $this->com_portal = $this->m_site->get_site_data_by_id($this->portal_id);
        if (!empty($this->com_portal)) {
            $this->smarty->assign("site", $this->com_portal);
        }
        // languages
        $this->smarty->assign("rs_languages", $this->m_lang->get_list_lang());
        $this->bahasa = $this->m_lang->get_default_lang();
        $lang_selected = $this->tsession->userdata('session_lang');
        if (isset($lang_selected['lang_id'])) {
            $this->bahasa = $this->tsession->userdata('session_lang');
        }
        $this->smarty->assign("languages", $this->bahasa);
        // global variabel web content
        $this->m_lang->get_web_content_by_lang_group(array($this->bahasa['lang_id'], 'header_%'));
    }

    // get current page
    private function _display_current_page() {
        // get current page (segment 1 : folder, segment 2 : controller)
        $url_menu = $this->uri->segment(1) . '/' . $this->uri->segment(2);
        $url_menu = trim($url_menu, '/');
        $url_menu = (empty($url_menu)) ? $this->config->item('default_website') : $url_menu;
        $result = $this->m_site->get_current_page(array($url_menu));
        if (!empty($result)) {
            $this->smarty->assign("page", $result);
            $this->nav_id = $result['nav_id'];
            $this->parent_id = $result['parent_id'];
        }
    }

    // top navigation
    private function _display_top_navigation() {
        // get parent selected
        $this->parent_selected = self::_get_parent_group($this->parent_id, 0);
        if ($this->parent_selected == 0) {
            $this->parent_selected = $this->nav_id;
        }
        // get data
        $params = array($this->portal_id, 0, $this->bahasa['lang_id']);
        $this->smarty->assign("list_top_nav", $this->m_site->get_navigation_by_parent_desc($params));
        $this->smarty->assign("list_bottom_nav", $this->m_site->get_navigation_by_parent($params));
        $this->smarty->assign("top_menu_selected", $this->parent_selected);
    }

    // utility to get parent selected
    private function _get_parent_group($int_nav, $int_limit) {
        $selected_parent = 0;
        $result = $this->m_site->get_menu_by_id($int_nav);
        if (!empty($result)) {
            if ($result['parent_id'] == $int_limit) {
                $selected_parent = $result['nav_id'];
            } else {
                return self::_get_parent_group($result['parent_id'], $int_limit);
            }
        } else {
            $selected_parent = $result['nav_id'];
        }
        return $selected_parent;
    }

}
