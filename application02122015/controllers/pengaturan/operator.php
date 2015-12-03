<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/OperatorBase.php' );

class operator extends ApplicationBase {

    //contructor
    public function __construct() {
        //parent contructor
        parent::__construct();
        //load model
        $this->load->model('m_operator');
        $this->load->model('m_account');
        //load library
        $this->load->library('tnotification');
        $this->load->library('pagination');
    }

    // list view
    public function index() {
        //set rule
        $this->_set_page_rule("R");
        //set template content
        $this->smarty->assign("template_content", "pengaturan/operator/list.html");
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        
        // load css 
        $this->smarty->load_style("select2/select2.css");
		// get search parameter
        $search = $this->tsession->userdata('search_operator');
        // search parameters
        $operator_name = empty($search['operator_name']) ? '%' : '%' . $search['operator_name'] . '%';
        $pelabuhan_id = empty($search['pelabuhan_id']) ? '%' : '%' . $search['pelabuhan_id'] . '%';
        $this->smarty->assign("search", $search);
        /* start of pagination --------------------- */
        // pagination
        $config['base_url'] = site_url("pengaturan/operator/index/");
        $config['total_rows'] = $this->m_operator->get_total_operator(array($operator_name, $pelabuhan_id));
        $config['uri_segment'] = 4;
        $config['per_page'] = 50;
        $this->pagination->initialize($config);
        $pagination['data'] = $this->pagination->create_links();
        // pagination attribute
        $start = $this->uri->segment(4, 0) + 1;
        $end = $this->uri->segment(4, 0) + $config['per_page'];
        $end = (($end > $config['total_rows']) ? $config['total_rows'] : $end);
        $pagination['start'] = ($config['total_rows'] == 0) ? 0 : $start;
        $pagination['end'] = $end;
        $pagination['total'] = $config['total_rows'];
        // pagination assign value
        $this->smarty->assign("pagination", $pagination);
        $this->smarty->assign("no", $start);
        /* end of pagination ---------------------- */
        // get list
        $params = array($operator_name, $pelabuhan_id, ($start - 1), $config['per_page']);
        $this->smarty->assign("rs_id", $this->m_operator->get_list_operator($params));
        // load sub direktorat
        $this->smarty->assign("rs_pelabuhan_id", $this->m_operator->get_all_pelabuhan());
        //notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // proses pencarian
    public function proses_cari() {
        // set page rules
        $this->_set_page_rule("R");
        // data
        if ($this->input->post('save') == "Reset") {
            $this->tsession->unset_userdata('search_operator');
        } else {
            $params = array(
                "operator_name" => $this->input->post("operator_name"),
                "pelabuhan_id" => $this->input->post("pelabuhan_id")
            );
            $this->tsession->set_userdata("search_operator", $params);
        }
        // redirect
        redirect("pengaturan/operator");
    }

    // add form
    public function add() {
        // set page rules
        $this->_set_page_rule("C");
        // set template content
        $this->smarty->assign("template_content", "pengaturan/operator/add.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
		$this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        $this->smarty->load_style("select2/select2.css");
		
        // load pelabuhan
        $this->smarty->assign("rs_sub", $this->m_operator->get_all_pelabuhan());
        // load roles
        $this->smarty->assign("rs_roles", $this->m_operator->get_all_roles_by_portal(6));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // untuk hak akses
        $data = $this->tnotification->get_field_data();
        if (isset($data['roles[]']['postdata'])) {
            if (!empty($data['roles[]']['postdata'])) {
                // hak akses
                $this->smarty->assign('roles_selected', $data['roles[]']['postdata']);
            }
        }
        
        // output
        parent::display();
    }

    // add process
    public function add_process() {
        // set page rules
        $this->_set_page_rule("C");
        // cek input
        $this->tnotification->set_rules('operator_name', 'Nama Lengkap', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('operator_gender', 'Jenis Kelamin', 'trim');
        $this->tnotification->set_rules('operator_birth_place', 'Tempat Lahir', 'trim|maxlength[50]');
        $this->tnotification->set_rules('operator_birth_day', 'Tanggal Lahir', 'trim|maxlength[10]');
        $this->tnotification->set_rules('operator_address', 'Alamat', 'trim|maxlength[100]');
        $this->tnotification->set_rules('operator_phone', 'Nomor Telepon', 'trim|required|maxlength[30]');
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]|valid_email');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('pelabuhan_id', 'Pelabuhan', 'trim|required|maxlength[100]');
        // user account
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[30]');
        // airlines
        // user access
        $this->tnotification->set_rules('roles[]', 'Permissions', 'trim|required');
        // check email
        $email = trim($this->input->post('user_mail'));
        if ($this->m_account->is_exist_email($email)) {
            $this->tnotification->set_error_message('Email is not available');
        }
		
		// check username
        $username = trim($this->input->post('user_name'));
        if ($this->m_account->is_exist_username($username)) {
            $this->tnotification->set_error_message('Username is not available');
        }
		
		
        // proses
        if ($this->tnotification->run() !== FALSE) {
            // user account
            $password_key = abs(crc32($this->input->post('user_pass')));
            $password = $this->encrypt->encode(md5($this->input->post('user_pass')), $password_key);
            $params = array(
                $this->input->post('user_name'), $password, $password_key, NULL, '0', $this->com_user['user_id']
            );
            // insert com_users
            if ($this->m_operator->insert_user($params)) {
                // user id
                $user_id = $this->m_operator->get_last_inserted_id();
                // params operator
                $tgl_lahir = $this->input->post('operator_birth_day');
                $tgl_lahir = empty($tgl_lahir) ? NULL : $tgl_lahir;
                $params = array(
                    $this->input->post('operator_name'), $this->input->post('operator_phone'),
                    $this->input->post('user_mail'), $this->input->post('operator_address'),
                    $this->input->post('operator_birth_place'), $tgl_lahir,
                    $this->input->post('operator_gender'), '',
                    $this->input->post('jabatan'), $this->input->post('pelabuhan_id'),
                    $user_id
                );
                // insert users
                if ($this->m_operator->insert_operator($params)) {
                    // upload foto
                    if (!empty($_FILES['operator_photo']['tmp_name'])) {
                        // load
                        $this->load->library('tupload');
                        // upload config
                        $config['upload_path'] = 'resource/doc/images/users/';
                        $config['allowed_types'] = 'gif|jpg|png';
                        $config['file_name'] = $user_id;
                        $this->tupload->initialize($config);
                        // process upload images
                        if ($this->tupload->do_upload_image('operator_photo', false, 160)) {
                            $data = $this->tupload->data();
                            $this->m_operator->update_foto_pribadi(array($data['file_name'], $user_id));
                        } else {
                            // jika gagal
                            $this->tnotification->set_error_message($this->tupload->display_errors());
                        }
                    }
                    // insert permissions
                    $roles = $this->input->post('roles');
                    if (!empty($roles)) {
                        // delete
                        $this->m_operator->delete_user_role(array($user_id, 6));
                        // insert
                        foreach ($roles as $role_id) {
                            $this->m_operator->insert_user_role(array($role_id, $user_id));
                        }
                    }
					
                    // success
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
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        //default redirect
        redirect("pengaturan/operator/add");
    }

    // edit users
    public function edit($user_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set templates
        $this->smarty->assign("template_content", "pengaturan/operator/edit.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        $this->smarty->load_style("select2/select2.css");
		
        // load pelabuhan
        $this->smarty->assign("rs_sub", $this->m_operator->get_all_pelabuhan());
        // get detail operator by id
        $result = $this->m_operator->get_operator_detail_by_id($user_id);
        // images
        $filepath = 'resource/doc/images/users/' . $result['operator_photo'];
        if (!is_file($filepath)) {
            $filepath = 'resource/doc/images/users/default.png';
        }
        $operator_img = base_url() . $filepath;
        $this->smarty->assign("operator_photo", $operator_img);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        
        // output
        parent::display();
    }

    // edit process
    public function edit_process() {
        // set page rules
        $this->_set_page_rule("U");
        // cek input
        $this->tnotification->set_rules('user_id', 'ID', 'trim|required');
        $this->tnotification->set_rules('operator_name', 'Nama Lengkap', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('operator_gender', 'Jenis Kelamin', 'trim');
        $this->tnotification->set_rules('operator_birth_place', 'Tempat Lahir', 'trim|maxlength[50]');
        $this->tnotification->set_rules('operator_birth_day', 'Tanggal Lahir', 'trim|maxlength[10]');
        $this->tnotification->set_rules('operator_address', 'Alamat', 'trim|maxlength[100]');
        $this->tnotification->set_rules('operator_phone', 'Nomor Telepon', 'trim|required|maxlength[30]');
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]|valid_email');
        $this->tnotification->set_rules('jabatan', 'Jabatan', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('pelabuhan_id', 'Pelabuhan', 'trim|required|maxlength[100]');
        // check email
        $email = trim($this->input->post('user_mail'));
        $email_old = trim($this->input->post('operator_email_old'));
        if ($email <> $email_old) {
            if ($this->m_account->is_exist_email($email)) {
                $this->tnotification->set_error_message('Email is not available');
            }
        }
        // airlines
        // proses
        if ($this->tnotification->run() !== FALSE) {
            // params operator
            $tgl_lahir = $this->input->post('operator_birth_day');
            $tgl_lahir = empty($tgl_lahir) ? NULL : $tgl_lahir;
            $params = array(
                $this->input->post('operator_name'), $this->input->post('operator_gender'),
                $this->input->post('operator_birth_place'), $tgl_lahir,
                $this->input->post('operator_address'), $this->input->post('operator_phone'),
                $this->input->post('user_mail'), '',
                $this->input->post('jabatan'), $this->input->post('pelabuhan_id'),
                $this->input->post('user_id')
            );
            if ($this->m_operator->update_data_pribadi($params)) {
                // upload foto
                if (!empty($_FILES['operator_photo']['tmp_name'])) {
                    // load
                    $this->load->library('tupload');
                    // upload config
                    $config['upload_path'] = 'resource/doc/images/users/';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['file_name'] = $this->input->post('user_id');
                    $this->tupload->initialize($config);
                    // process upload images
                    if ($this->tupload->do_upload_image('operator_photo', false, 160)) {
                        $data = $this->tupload->data();
                        $this->m_operator->update_foto_pribadi(array($data['file_name'], $this->input->post('user_id')));
                    } else {
                        // jika gagal
                        $this->tnotification->set_error_message($this->tupload->display_errors());
                    }
                }
                // insert airlines
                $airlines = $this->input->post('airlines');
                if (!empty($airlines)) {
                    // delete
                    $this->m_operator->delete_user_airlines(array($this->input->post('user_id')));
                    // insert
                    foreach ($airlines as $airlines_id) {
                        echo $this->input->post('user_id');
                        echo $airlines_id;
                        $this->m_operator->insert_user_airlines(array($this->input->post('user_id'), $airlines_id));
                    }
                }
                // success
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil disimpan");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/operator/edit/" . $this->input->post('user_id'));
    }

    // delete users
    public function delete($user_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set templates
        $this->smarty->assign("template_content", "pengaturan/operator/delete.html");
        // get detail operator by id
        $result = $this->m_operator->get_operator_detail_by_id($user_id);
        // images
        $filepath = 'resource/doc/images/users/' . $result['operator_photo'];
        if (!is_file($filepath)) {
            $filepath = 'resource/doc/images/users/default.png';
        }
        $operator_img = base_url() . $filepath;
        $this->smarty->assign("operator_photo", $operator_img);
        $this->smarty->assign("result", $result);
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

    // process hapus
    public function delete_process() {
        // cek input
        $this->tnotification->set_rules('user_id', 'ID User', 'trim|required');
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('user_id'));
            // update
            if ($this->m_operator->delete_operator($params)) {
                // unlink
                // --
                // notification
                $this->tnotification->delete_last_field();
                $this->tnotification->sent_notification("success", "Data berhasil dihapus");
                // default redirect
                redirect("pengaturan/operator/");
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
            }
        } else {
            // default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
        }
        // default redirect
        redirect("pengaturan/operator/delete/" . $this->input->post('user_id'));
    }

    // user account
    public function account($user_id = "") {
        // set page rules
        $this->_set_page_rule("U");
        // set templates
        $this->smarty->assign("template_content", "pengaturan/operator/account.html");
        // list roles
        $this->smarty->assign("rs_roles", $this->m_operator->get_all_roles_by_portal(6));
        // get detail operator
        $operator = $this->m_operator->get_operator_detail_by_id($user_id);
        $this->smarty->assign("operator", $operator);
        // get detail account by id
        $result = $this->m_account->get_user_account($user_id);
        // password
        //$result['user_pass'] = $this->encrypt->decode($result['user_pass'], $result['user_key']);
        // assign account
        $this->smarty->assign("result", $result);
        // roles selected
        $this->smarty->assign("roles_selected", $this->m_operator->get_all_roles_by_user(array($user_id, 6)));
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // untuk hak akses
        $data = $this->tnotification->get_field_data();
        if (isset($data['roles[]']['postdata'])) {
            if (!empty($data['roles[]']['postdata'])) {
                // hak akses
                $this->smarty->assign('roles_selected', $data['roles[]']['postdata']);
            }
        }
        // output
        parent::display();
    }

    // process update account
    public function process_update_account() {
        // cek input
        $this->tnotification->set_rules('user_id', 'ID User', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_name_old', 'Username lama', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('lock_st', 'Lock Status', 'trim|required');
        // user access
        $this->tnotification->set_rules('roles[]', 'Permissions', 'trim|required');
        // check user name
        $username = $this->input->post('user_name');
        $username_old = $this->input->post('user_name_old');
        if ($username <> $username_old) {
            if ($this->m_account->is_exist_username($username)) {
                $this->tnotification->set_error_message("Username not available!");
            }
        }
        // process
        if ($this->tnotification->run() !== FALSE) {
            $params = array($this->input->post('user_name'), md5($this->input->post('user_pass')),
                $this->input->post('lock_st'), $this->input->post('user_id'));
            // update
            if ($this->m_operator->update_data_account($params)) {
                // insert permissions
                $roles = $this->input->post('roles');
                if (!empty($roles)) {
                    // delete
                    $this->m_operator->delete_user_role(array($this->input->post('user_id'), 6));
                    // insert
                    $log_file = fopen("resource/doc/log/log.txt", "w") or die("File tidak bisa dibuka");
                    foreach ($roles as $role_id) {
                        $this->m_operator->insert_user_role(array($role_id, $this->input->post('user_id')));
                        $role = $this->m_operator->get_detail_role(array($role_id));
                        $operator = $this->m_operator->get_detail_operator(array($this->input->post('user_id')));
                        $text = $role['role_nm'] . ", " . $operator['operator_name'] . ", " . date('Y-m-d H:i:s') . "\r\n";
                        fwrite($log_file, $text);
                    }
                    fclose($log_file);
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
        redirect("pengaturan/operator/account/" . $this->input->post('user_id'));
    }

}
