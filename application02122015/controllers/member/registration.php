<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// load base class if needed
require_once( APPPATH . 'controllers/base/RegisterBase.php' );

class registration extends ApplicationBase {

    // constructor
    public function __construct() {
        // parent constructor
        parent::__construct();
        // load model
        $this->load->model('registration/m_registration');
		$this->load->model('m_email');
        
        // load library
        $this->load->library('tnotification');
        $this->load->library('session');
        $this->load->library('pagination');
        $this->load->library('converttime');
        
    }


    // list
    public function index() {
        // set template content
        $this->smarty->assign("template_content", "member/registration/add.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // $this->smarty->load_javascript("resource/js/select2-3.4.5/select2.min.js");
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        // $this->smarty->load_style("select2/select2.css");
		//set captcha
        $this->load->helper("captcha");
        $vals = array(
            'img_path' => FCPATH . '/resource/doc/captcha/',
            'img_url' => base_url() . '/resource/doc' . '/captcha/',
            'img_width' => '150',
            'font_path' => FCPATH . '/resource/doc/font/COURIER.TTF',
            'font_size' => 60,
            'img_height' => 70,
            'expiration' => 7200
        );
        $captcha = create_captcha($vals);
        $data = array(
            'captcha_time' => $captcha['time'],
            'ip_address' => $_SERVER["REMOTE_ADDR"],
            'word' => $captcha['word']
        );
        $this->session->set_userdata($data);
        $this->smarty->assign("captcha", $captcha);
        
		
        // // load airlines
        // $this->smarty->assign("rs_pelabuhan", $this->m_registration->get_all_pelabuhan());
        
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
    }

	// add process
    public function add_process() {
        // cek input
        $this->tnotification->set_rules('operator_name', 'Nama Lengkap', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('operator_gender', 'Jenis Kelamin', 'trim');
        $this->tnotification->set_rules('operator_birth_place', 'Tempat Lahir', 'trim|maxlength[50]');
        $this->tnotification->set_rules('operator_birth_day', 'Tanggal Lahir', 'trim|maxlength[10]');
        $this->tnotification->set_rules('operator_address', 'Alamat', 'trim|maxlength[100]');
        $this->tnotification->set_rules('operator_phone', 'Nomor Telepon', 'trim|required|maxlength[30]');
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]|valid_email');
        $this->tnotification->set_rules('captcha', 'Captcha', 'trim|required');
        
		// user account
        $this->tnotification->set_rules('user_name', 'Username', 'trim|required|max_length[30]');
        $this->tnotification->set_rules('user_pass', 'Password', 'trim|required|max_length[30]');
        
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
			$captcha = $this->input->post('captcha');
            $this->session->userdata('word');
            $expiration = time() - 7200;
            if ($this->session->userdata('word') == $captcha AND $this->session->userdata('ip_address') == $_SERVER["REMOTE_ADDR"] AND $this->session->userdata('captcha_time') > $expiration) {
            
            // user account
            $password_key = abs(crc32($this->input->post('user_pass')));
            $password = $this->encrypt->encode(md5($this->input->post('user_pass')), $password_key);
            $params = array(
                $this->input->post('user_name'), $password, $password_key, $this->com_user['user_id']
            );
            // insert com_users
            if ($this->m_registration->insert_member($params)) {
                // user id
                $user_id = $this->m_registration->get_last_inserted_id();
                // params operator
                $tgl_lahir = $this->input->post('operator_birth_day');
                $tgl_lahir = empty($tgl_lahir) ? NULL : $tgl_lahir;
                $params = array(
                    $this->input->post('operator_name'), $this->input->post('operator_phone'),
                    $this->input->post('user_mail'), $this->input->post('operator_address'),
                    $this->input->post('operator_birth_place'), $tgl_lahir,
                    $this->input->post('operator_gender'), $this->input->post('member_status'), '', '',
                    $user_id
                );
                // insert users
                if ($this->m_registration->insert_operator($params)) {
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
                            $this->m_registration->update_foto_pribadi(array($data['file_name'], $user_id));
                        } else {
                            // jika gagal
                            $this->tnotification->set_error_message($this->tupload->display_errors());
                        }
                    }
                    // insert permissions
                    $roles = '49';
                    if (!empty($roles)) {
                        // delete
                        $this->m_registration->delete_user_role(array($user_id, 8));
                        // insert
                            $this->m_registration->insert_user_role(array($roles, $user_id));
                    }
                    
					$this->m_email->mail_to_user($user_id);
					
					// success
                    $this->tnotification->delete_last_field();
                    $this->tnotification->sent_notification("success", "Data berhasil disimpan");
					// send mail
					
                } else {
                    // default error
                    $this->tnotification->sent_notification("error", "Data gagal disimpan");
				redirect("member/registration");
                }
            } else {
                // default error
                $this->tnotification->sent_notification("error", "Data gagal disimpan");
				redirect("member/registration");
            }
			} else {
				//default redirect
                $this->tnotification->sent_notification("error", "Captcha tidak sesuai");
				redirect("member/registration");
            }
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
				redirect("member/registration");
        }
        //default redirect
        redirect("member/registration/email");
    }
	
	
	// add process
    public function confirmation() {
        // set template content
        $this->smarty->assign("template_content", "member/registration/confirmation.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        
		$email = $this->input->get('email');
		if($email != ''){
			$result = $this->m_registration->get_user_detail_by_email($email);
			$validationKey = str_replace(" ","+",$this->input->get_post('validationKey'));
			
			$key = $this->encrypt->decode($validationKey, $result['user_key']);
			// load variable
			$this->smarty->assign("user_mail", $email);
			$this->smarty->assign("confirmation_kd", $key);
		}
        
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
	}
	
	// add process
    public function confirmation_process() {
        // cek input
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]');
        $this->tnotification->set_rules('confirmation_kd', 'Kode Konfirmasi', 'trim|required|maxlength[50]');
		
		// check email
        $email = trim($this->input->post('user_mail'));
        if ($this->m_account->is_notexist_email($email)) {
            $this->tnotification->set_error_message('Email tidak ditemukan');
        }
		
		// proses
        if ($this->tnotification->run() !== FALSE) {
			 
			$active = $this->m_account->is_active_mailkey(array($this->input->post('user_mail'),'1'));
			if($active){
				$result = $this->m_account->is_exist_mailkey(array($this->input->post('user_mail'),$this->input->post('confirmation_kd')));
				
				if($result){
					$this->m_registration->update_reg_status($email);
					redirect("member/registration/confirmation_success");
				}
				else {
						// default error
				$this->tnotification->sent_notification("error", "Kode Konfirmasi Anda Salah");
					redirect("member/registration/confirmation");
				}
			}
			else{
						// default error
				$this->tnotification->sent_notification("error", "Akun anda sudah terdaftar");
					redirect("member/registration/confirmation");
			}
        } else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
				redirect("member/registration/confirmation");
        }
	}
	
	// add process
    public function confirmation_success() {
        // set template content
        $this->smarty->assign("template_content", "member/registration/success.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
	}
	
	// add process
    public function email() {
        // set template content
        $this->smarty->assign("template_content", "member/registration/email.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
	}
	
		// add process
    public function reset() {
        // set template content
        $this->smarty->assign("template_content", "member/registration/reset.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        
		$this->load->helper("captcha");
        $vals = array(
            'img_path' => FCPATH . '/resource/doc/captcha/',
            'img_url' => base_url() . '/resource/doc' . '/captcha/',
            'img_width' => '150',
            'font_path' => FCPATH . '/resource/doc/font/COURIER.TTF',
            'font_size' => 60,
            'img_height' => 70,
            'expiration' => 7200
        );
        $captcha = create_captcha($vals);
        $data = array(
            'captcha_time' => $captcha['time'],
            'ip_address' => $_SERVER["REMOTE_ADDR"],
            'word' => $captcha['word']
        );
        $this->session->set_userdata($data);
        $this->smarty->assign("captcha", $captcha);
        
		
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
	}
	
	// add process
    public function reset_process() {
        // cek input
        $this->tnotification->set_rules('user_mail', 'Email', 'trim|required|maxlength[50]');
        
		// check email
        $email = trim($this->input->post('user_mail'));
        if ($this->m_account->is_notexist_email($email)) {
            $this->tnotification->set_error_message('Email tidak ditemukan');
        }
		
		// proses
        if ($this->tnotification->run() !== FALSE) {
				
			$captcha = $this->input->post('captcha');
            $this->session->userdata('word');
            $expiration = time() - 7200;
            if ($this->session->userdata('word') == $captcha AND $this->session->userdata('ip_address') == $_SERVER["REMOTE_ADDR"] AND $this->session->userdata('captcha_time') > $expiration) {
            
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$passwordrand = substr( str_shuffle( $chars ), 0, 7 );
				$new_password = md5($passwordrand);
				
				$result = $this->m_account->reset_password(array($new_password,$this->input->post('user_mail')));
				
				if($result){
					
					$this->m_email->mail_to_reset_user($passwordrand,$this->input->post('user_mail'));
					redirect("member/registration/reset_success");
				}
				else {//default error
					$this->tnotification->sent_notification("error", "Data gagal disimpan");
					redirect("member/registration/reset");
				}
			} else {
				//default redirect
                $this->tnotification->sent_notification("error", "Captcha tidak sesuai");
				redirect("member/registration/reset");
            }
		} else {
            //default error
            $this->tnotification->sent_notification("error", "Data gagal disimpan");
				redirect("member/registration/reset");
        }
	}
	
	// add process
    public function reset_success() {
        // set template content
        $this->smarty->assign("template_content", "member/registration/reset_success.html");
        // load javascript
        $this->smarty->load_javascript('resource/js/jquery/jquery-ui-1.9.2.custom.min.js');
        // load css 
        $this->smarty->load_style('jquery.ui/redmond/jquery-ui-1.8.13.custom.css');
        
        // notification
        $this->tnotification->display_notification();
        $this->tnotification->display_last_field();
        // output
        parent::display();
	}
}
