<?php

class m_email extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // load library
        $this->load->library('doslibrary');
    }
	
    // get member mail
    function get_member_email($params) {
        $sql = "SELECT * FROM com_user WHERE user_mail = ?";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

	// mail izin to user
    function mail_to_user($user_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail, a.user_key 
            FROM com_user a  
            WHERE a.user_id = $user_id";
			
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_preferences");
            $mail = $this->m_preferences->get_mail();
            $detail = explode(",", $mail['pref_value']);
            $host = $mail['pref_nm'];
            $port = $detail[0];
            $user = $detail[1];
            $pass = $detail[2];
			
			//hardcode email
			$host = "ssl://smtp.googlemail.com";
			$port = "465";
			$user = "pendaftarankapal@gmail.com";
			$pass = "pendaftarankapal2015";
			
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
            foreach ($result as $value) {
                $to = $value['user_mail'];
				$key_encode = $this->encrypt->encode($value['user_key'], $value['user_key']);
				$em = $value['user_mail'];
				$key = $value['user_key'];
            }
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                    <span style='font:normal 11px Tahoma'>Terimakasih telah melakukan registrasi akun pada SIM Pendaftaran Kapal. Selanjutnya Silahkan klik link berikut :
											</br>		
													<a target='_blank' href='http://localhost/kapalonline/index.php/member/registration/confirmation?email=".$em."&validationKey=".$key_encode."'>http://localhost/kapalonline/index.php/member/registration/confirmation?email=".$em."&validationKey=".$key_encode."</a>
											</br>		
													atau masukkan kode konfirmasi berikut ke dalam form pada halaman konfirmasi sebelum 12 jam dari sekarang</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Kode Konfirmasi</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $key . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style='width: 30%;'>Halaman Konfirmasi</td>
                                                                    <td>:</td>
                                                                    <td><a target='_blank' href='http://localhost/kapalonline/index.php/member/registration/confirmation'>http://localhost/kapalonline/index.php/member/registration/confirmation</a></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    
													<span style='font:normal 11px Tahoma'>Direktorat Perkapalan dan Kepelautan<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Pendaftaran Kapal Online (no reply)');
                $this->email->to($to);
                $this->email->cc($user);
                $this->email->subject('Registrasi Akun Pendaftaran Kapal');
				// echo $html;
                $this->email->message($html);
				
                $this->email->send();
				// echo ' from '.$user;
				// echo ' to '.$to;
				// echo ' cc '.$user;
				// echo ' host '.$host;
				// echo ' port '.$port;
				// echo ' user '.$user;
				// echo ' pass '.$pass;
				// exit;
            }
            return true;
        } else {
            return array();
        }
    }
	
	function mail_to_reset_user($newpass, $user_mail) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail, a.user_key, a.user_name 
            FROM com_user a  
            WHERE a.user_mail = '$user_mail'";
			var_dump($sql);
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_preferences");
            $mail = $this->m_preferences->get_mail();
            $detail = explode(",", $mail['pref_value']);
            $host = $mail['pref_nm'];
            $port = $detail[0];
            $user = $detail[1];
            $pass = $detail[2];
			
			//hardcode email
			$host = "ssl://smtp.googlemail.com";
			$port = "465";
			$user = "pendaftarankapal@gmail.com";
			$pass = "pendaftarankapal2015";
			
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
            foreach ($result as $value) {
                $to = $user_mail;
				$user_name = $value['user_name'];
            }
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                    <span style='font:normal 11px Tahoma'>Selamat, Perubahan password anda sudah berhasil. Berikut ini adalah username password anda yang baru :
											</br>		
											</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 15%;'>Username</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $user_name . "</strong></td>
                                                                </tr><tr>
                                                                    <td style='width: 15%;'>Password</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $newpass . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    
													<span style='font:normal 11px Tahoma'>Direktorat Perkapalan dan Kepelautan<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Pendaftaran Kapal Online (no reply)');
                $this->email->to($to);
                $this->email->cc($user);
                $this->email->subject('Registrasi Akun Pendaftaran Kapal');
				// echo $html;
                $this->email->message($html);
				
                $this->email->send();
				// echo ' from '.$user;
				// echo ' to '.$to;
				// echo ' cc '.$user;
				// echo ' host '.$host;
				// echo ' port '.$port;
				// echo ' user '.$user;
				// echo ' pass '.$pass;
				// exit;
            }
            return true;
        } else {
            return array();
        }
    }
	
	
    // mail to all aunbdn
    function mail_to_all_aunbdn($data_id, $airlines_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id IN (42, 43, 44, 45) AND d.airlines_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $airlines_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_member");
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_member->get_detail_data_by_id(array($data_id, $airlines_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>REGULATOR ANGKUTAN UDARA</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Mohon diproses pengajuan flight approval sebagai berikut :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['rute_all'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Penerbangan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['date_start']) . " - " . $this->datetimemanipulation->get_full_date($result['date_end']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail to all aunbln
    function mail_to_all_aunbln($data_id, $airlines_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id IN (48, 47, 44, 45) AND d.airlines_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $airlines_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_member");
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_member->get_detail_data_by_id(array($data_id, $airlines_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>REGULATOR ANGKUTAN UDARA</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Mohon diproses pengajuan flight approval sebagai berikut :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['rute_all'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Penerbangan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['date_start']) . " - " . $this->datetimemanipulation->get_full_date($result['date_end']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail to all auntbdn
    function mail_to_all_auntbdn($data_id, $airlines_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id IN (50, 52, 46) AND d.airlines_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $airlines_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_member");
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_member->get_detail_data_by_id(array($data_id, $airlines_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>REGULATOR ANGKUTAN UDARA</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Mohon diproses pengajuan flight approval sebagai berikut :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['rute_all'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Penerbangan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['date_start']) . " - " . $this->datetimemanipulation->get_full_date($result['date_end']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail to all auntbln
    function mail_to_all_auntbln($data_id, $airlines_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id IN (51, 53, 46) AND d.airlines_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $airlines_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_member");
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_member->get_detail_data_by_id(array($data_id, $airlines_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>REGULATOR ANGKUTAN UDARA</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Mohon diproses pengajuan flight approval sebagai berikut :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['rute_all'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Penerbangan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['date_start']) . " - " . $this->datetimemanipulation->get_full_date($result['date_end']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail to next flow
    function mail_to_next_flow($data_id, $role_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail, c.role_nm 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $role_id);
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
            foreach ($result as $value) {
                $role_nm = $value['role_nm'];
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_task->get_detail_data_by_id(array($data_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $role_nm . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang telah di verifikasi :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail to stakeholder
    function mail_to_stakeholder($data_id) {
        $sql = "SELECT rs1.*, a.user_mail, b.airlines_nm, c.services_nm 
            FROM 
            (
                SELECT a.data_id, a.rute_all, a.document_no, a.published_no, a.airlines_id, a.date_start, a.date_end, a.services_cd, b.airport_id, b.seq, c.user_id 
                FROM fa_data a 
                LEFT JOIN fa_data_rute b ON b.data_id = a.data_id 
                LEFT JOIN com_user_bandara c ON c.airport_id = b.airport_id 
                WHERE a.data_id = ?
            )rs1 
            LEFT JOIN com_user a ON a.user_id = rs1.user_id 
            LEFT JOIN airlines b ON b.airlines_id = rs1.airlines_id 
            LEFT JOIN services_code c ON c.services_cd = rs1.services_cd";
        $query = $this->db->query($sql, $data_id);
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
            if (!empty($result)) {
                foreach ($result as $value) {
                    $airlines_nm = $value['airlines_nm'];
                    $date_start = $value['date_start'];
                    $date_end = $value['date_end'];
                    $services_nm = $value['services_nm'];
                    $document_no = $value['document_no'];
                    $published_no = $value['published_no'];
                    $rute_all = $value['rute_all'];
                    $to[] = $value['user_mail'];
                }
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>BAMDARA / OTORITAS BANDARA / AIRNAV</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail flight approval yang telah disetujui :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($airlines_nm) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($rute_all) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Penerbangan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($date_start) . " - " . $this->datetimemanipulation->get_full_date($date_end) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Flight Approval</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($published_no) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($services_nm) . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($published_no);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail finish process
    function mail_finish_process($data_id) {
        $sql = "SELECT b.flow_id, c.user_name, c.user_mail 
            FROM fa_data a 
            LEFT JOIN fa_process b ON b.data_id = a.data_id 
            LEFT JOIN com_user c ON c.user_id = b.mdb
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $data_id);
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_task->get_detail_data_by_id(array($data_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $result['airlines_nm'] . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang telah disetujui :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail pending and reject
    function mail_pending_reject($data_id) {
        $sql = "SELECT b.flow_id, c.user_name, c.user_mail 
            FROM fa_data a 
            LEFT JOIN fa_process b ON b.data_id = a.data_id 
            LEFT JOIN com_user c ON c.user_id = b.mdb
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $data_id);
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_task->get_detail_data_by_id(array($data_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $result['airlines_nm'] . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang harus diperbaiki :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail pending and reject
    function mail_reject($data_id) {
        $sql = "SELECT b.flow_id, c.user_name, c.user_mail 
            FROM fa_data a 
            LEFT JOIN fa_process b ON b.data_id = a.data_id 
            LEFT JOIN com_user c ON c.user_id = b.mdb
            WHERE a.data_id = ?";
        $query = $this->db->query($sql, $data_id);
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->m_task->get_detail_data_by_id(array($data_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $result['airlines_nm'] . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang ditolak :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['document_no'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Services Code</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['services_nm'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Flight Approval Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['document_no']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    /* -------------------- IZIN RUTE -------------------- */

    // get detail izin rute
    function get_detail_data_by_id($registrasi_id) {
        $sql = "SELECT b.airlines_nm, a.registrasi_id, a.airlines_id, a.izin_group, a.izin_type, a.izin_flight, a.izin_request_letter, a.izin_request_letter_date, a.izin_rute_start, a.izin_rute_end, a.mdd 
            FROM izin_registrasi a
			LEFT JOIN airlines b ON b.airlines_id = a.airlines_id  
            WHERE registrasi_id = ?";
        $query = $this->db->query($sql, $registrasi_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get detail izin rute and note
    function get_detail_data_note_by_id($registrasi_id, $user_id) {
        $sql = "SELECT a.registrasi_id, a.airlines_id, a.izin_group, a.izin_type, a.izin_flight, a.izin_request_letter, a.izin_request_letter_date, a.izin_rute_start, a.izin_rute_end, a.mdd, IF(ISNULL(b.catatan), 'Tidak Ada Catatan', b.catatan)'catatan', c.airlines_nm 
            FROM izin_registrasi a 
            LEFT JOIN izin_process b ON b.registrasi_id = a.registrasi_id 
            LEFT JOIN airlines c ON c.airlines_id = a.airlines_id 
            WHERE a.registrasi_id = ? AND b.mdb_finish = ? 
            ORDER BY b.mdd DESC 
            LIMIT 1";
        $query = $this->db->query($sql, $registrasi_id);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // get list rute by id
    function get_list_data_rute_by_id($params) {
        $sql = "SELECT a.*, kode_izin, kode_frekuensi,
                (
                IF(SUBSTRING(b.dos, 1, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 2, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 3, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 4, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 5, 1) = 0, 0, 1) + IF(SUBSTRING(b.dos, 6, 1) = 0, 0, 1) +
                IF(SUBSTRING(b.dos, 7, 1) = 0, 0, 1)
                )'frekuensi', 
                aircraft_type, aircraft_capacity, dos, ron, pairing, izin_start_date, izin_expired_date, 
                izin_penundaan_start, izin_penundaan_end, izin_approval, notes
                FROM izin_data a
                INNER JOIN izin_rute b ON a.izin_id = b.izin_id
                WHERE b.registrasi_id = ? AND b.izin_completed = '0'
                ORDER BY b.kode_frekuensi ASC, a.izin_id ASC";
        $query = $this->db->query($sql, $params);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            return $result;
        } else {
            return array();
        }
    }

    // mail izin to all aunbdn
    function mail_izin_to_all_aunbdn($registrasi_id, $airlines_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id IN (42, 43, 44, 45, 63) 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $airlines_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_izin");
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_by_id(array($registrasi_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>REGULATOR ANGKUTAN UDARA</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan izin rute :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tipe</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail izin to all aunbln
    function mail_izin_to_all_aunbln($registrasi_id, $airlines_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id IN (48, 47, 44, 45, 63) 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $airlines_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
            $this->load->model("m_izin");
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_by_id(array($registrasi_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>REGULATOR ANGKUTAN UDARA</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan izin rute :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tipe</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail izin to next flow
    function mail_izin_to_next_flow($registrasi_id, $role_id, $user_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail, c.role_nm 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $role_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
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
            foreach ($result as $value) {
                $role_nm = $value['role_nm'];
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_note_by_id(array($registrasi_id, $user_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $role_nm . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan izin rute yang telah di verifikasi :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style='width: 30%;'>Tipe</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Catatan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['catatan'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail izin to back flow
    function mail_izin_to_back_flow($registrasi_id, $role_id, $user_id) {
        $sql = "SELECT a.user_id, a.operator_name, a.user_mail, c.role_nm 
            FROM com_user a 
            LEFT JOIN com_role_user b ON b.user_id = a.user_id 
            LEFT JOIN com_role c ON c.role_id = b.role_id 
            LEFT JOIN com_user_airlines d ON d.user_id = a.user_id 
            WHERE c.role_id = ? 
            GROUP BY a.user_id";
        $query = $this->db->query($sql, $role_id);
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $query->free_result();
            // send mail
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
            foreach ($result as $value) {
                $role_nm = $value['role_nm'];
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_note_by_id(array($registrasi_id, $user_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $role_nm . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan izin rute yang telah di verifikasi kembali :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Airlines</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['airlines_nm'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td'>Tipe</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Catatan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['catatan'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail izin finish process
    function mail_izin_finish_process($registrasi_id, $user_id) {
        $params = array($registrasi_id, $registrasi_id);
        $sql = "SELECT b.flow_id, c.user_name, c.user_mail 
            FROM izin_registrasi a 
            LEFT JOIN izin_process b ON b.registrasi_id = a.registrasi_id 
            LEFT JOIN com_user c ON c.user_id = b.mdb
            WHERE a.registrasi_id = ?
            UNION
            SELECT a.tembusan_value, b.redaksional_nm, b.redaksional_mail 
            FROM izin_tembusan a 
            LEFT JOIN redaksional b ON b.redaksional_id = a.tembusan_value 
            WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $params);
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_note_by_id(array($registrasi_id, $user_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $result['airlines_nm'] . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang telah disetujui :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Tipe</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Catatan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['catatan'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail izin reject
    function mail_izin_reject($registrasi_id, $user_id) {
        $sql = "SELECT b.flow_id, c.user_name, c.user_mail 
            FROM izin_registrasi a 
            LEFT JOIN izin_process b ON b.registrasi_id = a.registrasi_id 
            LEFT JOIN com_user c ON c.user_id = b.mdb
            WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $registrasi_id);
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_note_by_id(array($registrasi_id, $user_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $result['airlines_nm'] . "</b></span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang ditolak :</span><br/>
                                                    <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                        <table  style='font:normal 11px Tahoma; width: 100%'>
                                                            <tbody>
                                                                <tr>
                                                                    <td style='width: 30%;'>Tipe</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Tanggal Pengajuan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Nomor Dokumen</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Rute</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Catatan</td>
                                                                    <td>:</td>
                                                                    <td><strong>" . $result['catatan'] . "</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br />                                   
                                                    <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                    <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

    // mail izin pending
    function mail_izin_pending($registrasi_id, $user_id) {
        $sql = "SELECT b.flow_id, c.user_name, c.user_mail 
            FROM izin_registrasi a 
            LEFT JOIN izin_process b ON b.registrasi_id = a.registrasi_id 
            LEFT JOIN com_user c ON c.user_id = b.mdb
            WHERE a.registrasi_id = ?";
        $query = $this->db->query($sql, $registrasi_id);
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
            foreach ($result as $value) {
                $to[] = $value['user_mail'];
            }
            // get detail data
            $result = $this->get_detail_data_note_by_id(array($registrasi_id, $user_id));
            // get detail frekuensi
            $rs_frek = $this->get_list_data_rute_by_id(array($registrasi_id));
            if (!empty($result)) {
                $html = "<table width='100%' cellspacing='0' cellpadding='0' bgcolor='EFEFEF'>
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing='0' cellpadding='0' align='center'>
                                    <tbody>
                                        <tr>
                                            <td width='600' valign='center' style='padding-top: 5px;color:#ffffff;font-family:Arial,Helvetica,sans-serif;height:40px;line-height:0%;max-width:600px;background-color:#5F7B93'>
                                                <div align='center'>
                                                    
                                                    <a target='_blank' style='text-decoration:none; color: white; font-weight: bold; margin-top:20px;' title='Kementerian Perhubungan Republik Indonesia' href='http://www.dephub.go.id/'>
                                                        Kementerian Perhubungan Republik Indonesia
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='560' bgcolor='ffffff' style='font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#333333;padding:20px 15px;border:1px solid #ECE9D8;line-height:18px'>
                                                <span style='font:normal 11px Tahoma'>Kepada Yang Terhormat,<br/><b>" . $result['airlines_nm'] . "</b></span><br/><br/>
                                                <span style='font:normal 11px Tahoma'>Berikut adalah detail pengajuan yang harus diperbaiki :</span><br/>
                                                <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                    <table  style='font:normal 11px Tahoma; width: 100%'>
                                                        <tbody>
                                                            <tr>
                                                                <td style='width: 30%;'>Tipe</td>
                                                                <td>:</td>
                                                                <td><strong>" . strtoupper($result['izin_type']) . " - " . strtoupper($result['izin_flight']) . "</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Tanggal Pengajuan</td>
                                                                <td>:</td>
                                                                <td><strong>" . $this->datetimemanipulation->get_full_date($result['mdd']) . "</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nomor Dokumen</td>
                                                                <td>:</td>
                                                                <td><strong>" . $result['izin_request_letter'] . "</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Rute</td>
                                                                <td>:</td>
                                                                <td><strong>" . $result['izin_rute_start'] . " / " . $result['izin_rute_end'] . "</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Catatan</td>
                                                                <td>:</td>
                                                                <td><strong>" . $result['catatan'] . "</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <br />
                                                <span style='font:normal 11px Tahoma'>Detail Frekuensi :</span><br/>
                                                <div style='padding:10px 20px;margin:10px 0 20px 0;border:1px solid #ECEADC;background:#F5F4EB;font:normal 11px/200% Helvetica,sans-serif,Arial'>
                                                    <table  style='font:normal 11px Tahoma; width: 100%'>
                                                        <tbody>
                                                            <tr>
                                                                <th width='5%'>No</th>
                                                                <th width='10%'>Rute</th>
                                                                <th width='10%'>Tipe Pesawat</th>
                                                                <th width='10%'>Nomor Penerbangan</th>
                                                                <th width='10%'>ETD <br />(Waktu Lokal)</th>
                                                                <th width='10%'>ETA <br />(Waktu Lokal)</th>
                                                                <th width='10%'>DOS</th>
                                                                <th width='10%'>RON</th>
                                                                <th width='10%'>Frekuensi</th>
                                                                <th width='15%'></th>
                                                            </tr>";
                                                            $no = 0;
                                                            $temp = "";
                                                            foreach ($rs_frek as $data) {
                                                                if ($data['izin_id'] != $temp) {
                                                                    $no = $no + 1;
                                                                }
                                                                $html .= "<tr>";
                                                                if ($data['izin_id'] != $temp) {
                                                                    if ($data['pairing'] == "VV") {
                                                                        $html .= "<td align='center' rowspan='2'>" . $no . "</td>";
                                                                    } else {
                                                                        $html .= "<td align='center'>" . $no . "</td>";
                                                                    }
                                                                }
                                                                $html .= "
                                                                    <td align='center'>" . $data['rute_all'] . "</td>
                                                                    <td align='center'>" . $data['aircraft_type'] . "</td>
                                                                    <td align='center'>" . $data['flight_no'] . "</td>
                                                                    <td align='center'>" . substr($data['etd'], 0, 5) . "</td>
                                                                    <td align='center'>" . substr($data['eta'], 0, 5) . "</td>";
                                                                    if ($data['izin_id'] != $temp) {
                                                                        if ($data['pairing'] == "VV") {
                                                                            if ($data['ron'] == 0) {
                                                                                $html .= "<td align='center' rowspan='2'>" . $data['dos'] . "</td>";
                                                                            } else {
                                                                                $html .= "<td align='center'>" . $data['dos'] . "</td>";
                                                                            }
                                                                        } else {
                                                                            $html .= "<td align='center'>" . $data['dos'] . "</td>";
                                                                        }
                                                                    } elseif ($data['ron'] != 0) {
                                                                        $html .= "<td align='center'>" . $this->doslibrary->reverse_dos($data['dos'], $data['ron']) . "</td>";
                                                                    }
                                                                    if ($data['izin_id'] != $temp) {
                                                                        if ($data['pairing'] == "VV") {
                                                                            $html .= "<td align='center' rowspan='2'>" . $data['ron'] . "</td>";
                                                                        } else {
                                                                            $html .= "<td align='center'>" . $data['ron'] . "</td>";
                                                                        }
                                                                    }
                                                                    if ($data['izin_id'] != $temp) {
                                                                        if ($data['pairing'] == "VV") {
                                                                            $html .= "<td align='center' rowspan='2'>" . $data['frekuensi'] . "</td>";
                                                                        } else {
                                                                            $html .= "<td align='center'>" . $data['frekuensi'] . "</td>";
                                                                        }
                                                                    }
                                                                    if ($data['izin_id'] != $temp) {
                                                                        if ($data['pairing'] == "VV") {
                                                                            $html .= "<td align='center' rowspan='2'>" . $data['izin_approval'] . "<br />" . $data['notes'] . "</td>";
                                                                        } else {
                                                                            $html .= "<td align='center'>" . $data['izin_approval'] . "<br />" . $data['notes'] . "</td>";
                                                                        }
                                                                    }
                                                                if ($data['izin_id'] != $temp) {
                                                                    $temp = $data['izin_id'];
                                                                }
                                                                $html .= "</tr>";
                                                            }
                $html .= "
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <br />
                                                <span style='font:normal 11px Tahoma'>Pendaftaran Kapal Online<br/>Kementerian Perhubungan Republik Indonesia</span><br/><br/>
                                                <span style='font:normal 11px Tahoma'>'Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'</span><br />
                                            </td>
                                        </tr>                        
                                        <tr>                                    
                                            <td width='570' bgcolor='#EFEFEF' align='center' style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#666'>
                                                <br />
                                                Jln Medan Merdeka Barat No.20 Jakarta <br/><a target='_blank' style='text-decoration:none;color:#5F7B93' href='mailto:info151@dephub.go.id'>info151@<span class='il'>dephub</span>.<span class='il'>go.id</span></a> </p>
                                                <p align='center'> Kunjungi <a target='_blank' style='text-decoration:none;color:#5F7B93' href='Keselamatan dan Pelayanan Prima merupakan Prioritas Kinerja Kami'><font color='#5F7B93'>Facebook</font></a> &amp; Ikuti kami pada <a target='_blank' style='text-decoration:none;color:#5F7B93' href='https://twitter.com/@kemenhub151'><font color='#5F7B93'>Twitter</font></a> </p>
                                                <br /><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>";
                // send
                $this->email->from($user, 'Izin Rute Online (no reply)');
                $this->email->to($user);
                $this->email->cc($to);
                $this->email->subject($result['izin_request_letter']);
                $this->email->message($html);
                $this->email->send();
            }
            return true;
        } else {
            return array();
        }
    }

}
