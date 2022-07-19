<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Login extends CI_Controller{

		var $template = '/cp';

		function __construct(){
			parent::__construct();

			ini_set('session.gc_maxlifetime', 3600); // server should keep session data for AT LEAST 1 hour
			session_set_cookie_params(3600); // each client should remember their session id for EXACTLY 1 hour
			//session_start();

			$this->load->helper('form');
			$this->load->helper('phpmailer');

			$this->load->library('messages');
			$this->load->library('utilities');
		}

		function index(){
			$data = array();
			$this->load->vars($data);
			$this->load->view($this->template.'/login_form');
		}

		function validate(){
			$this->load->model('mcp/Mcompanies');
			$this->load->model('Mgeneral_settings');
			if($userdata = $this->Mcompanies->validateCompany()){
				$params = array('company_id'=>$userdata->id);
				$general_settings = $this->Mgeneral_settings->get_general_settings($params);
				if($general_settings['0']->language_id == 1){
					setcookie('locale','en_US',time()+365*24*60*60,'/');
				}elseif($general_settings['0']->language_id == 2){
					setcookie('locale','nl_NL',time()+365*24*60*60,'/');
				}else{
					setcookie('locale','fr_FR',time()+365*24*60*60,'/');
				}

				/* ##################### FORUM INTEGRATION CODE: STARTS ###################### */

				//check if the user detail is present in the sforum_users table..
				$_SESSION['cp_username'] = $this->input->post('username');
				$qry_sforum = $this->db->query('SELECT * FROM `sforum_users` WHERE `username` = "'.$this->input->post('username').'"');
				$res_sforum = $qry_sforum->result_array();
				//echo "forum users";
				//print_r($res_sforum);
				if(is_array($res_sforum) && !empty($res_sforum)){

					// For forum login merging..
					$_SESSION['forum_user_logged_in'] = true;
					$_SESSION['sforum_logged_in'] = true;
					$_SESSION['sforum_user_id'] = $res_sforum[0]['id'];
					$_SESSION['sforum_user_role'] = $res_sforum[0]['role'];
					$_SESSION['sforum_user_username'] = $this->input->post('username');

					//echo "id 11111".$res_sforum[0]['id']; die;

				} else {
					//$qry_sforum = $this->db->query('INSERT INTO `sforum_users` (`username`,`role`) VALUES("'.$this->input->post('username').'", "user" )');
					$param_sforum = array("username" => $this->input->post('username'), 'role' => 'user');

					$this->db->insert('sforum_users',$param_sforum);

					$sfroum_id = $this->db->insert_id();
					//echo "iddddddd".$sfroum_id; die;

					$_SESSION['forum_user_logged_in'] = true;
					$_SESSION['sforum_logged_in'] = true;
					$_SESSION['sforum_user_id'] = $sfroum_id;
					$_SESSION['sforum_user_role'] = 'user';
					$_SESSION['sforum_user_username'] = $this->input->post('username');
				}

				/* ##################### FORUM INTEGRATION CODE: ENDS ###################### */

				$data = array(
					'cp_user_id' => $userdata->id,
					'cp_username' => $this->input->post('username'),
					'cp_user_role' => $userdata->role,
					'cp_user_parent_id' => $userdata->parent_id,
					'cp_is_logged_in' => true,
					'cp_website' => '',
					'login_via' => 'cp'
				);

				if($this->input->post('via') == 'mcp')
					$data['login_via'] = 'mcp';

				if($userdata->ac_type_id == 3){
					$this->load->model('MFtp_settings');
					$ftp_settings = $this->MFtp_settings->get_ftp_settings($params);
					if(!empty($ftp_settings) && isset($ftp_settings['0']->obs_shop) && $ftp_settings['0']->obs_shop != ''){
						$data['cp_website'] = $ftp_settings['0']->obs_shop;
					}
				}else{
					if($userdata->existing_order_page != ''){
						$data['cp_website'] = $userdata->existing_order_page;
					}else{
						$this->load->model('Mcompany_type');
						$company_type_ids = explode("#",$userdata->type_id);
						$company_type = $this->Mcompany_type->get_company_type(array('id' => $company_type_ids[0]));
						if(!empty($company_type)){
							$data['cp_website'] = $this->config->item('portal_url').$company_type['0']->slug.'/'.$userdata->company_slug;
						}
					}
				}

				$this->session->set_userdata($data);

				if($data['login_via'] == 'mcp'){
					echo "1";
					exit;
				}

				/* UPDATING LAST LOGIN  */
				$this->Mcompanies->update(array('id' => $userdata->id, 'last_login' => date("Y-m-d H:i:s")));

				if( $userdata->role == 'master' || $userdata->role == 'super' )
				  redirect('cp');
				else
				if( $userdata->role == 'sub' )
				  redirect('cp/orders');
			}else{
				$this->messages->add('Please enter a valid username and password','error');
				redirect('cp/login');
			}
		}

		function validateUsername(){

		}

		function logout(){
			//$this->session->sess_destroy();
			$data = array(
					'cp_user_id' => 0,
					'cp_username' => '',
					'cp_user_role' => '',
					'cp_user_parent_id' => 0,
					'cp_is_logged_in' => '',
					'cp_website' => '',
					'login_via' => ''
			);
			$this->session->unset_userdata($data);

			unset($_SESSION['forum_user_logged_in']);
			unset($_SESSION['sforum_logged_in']);
			unset($_SESSION['sforum_user_id']);
			unset($_SESSION['sforum_user_role']);
			unset($_SESSION['sforum_user_username']);
			unset($_SESSION['cp_username']);
			redirect('cp/login');
		}

		function forgot_password(){
			$lang = get_lang( $_COOKIE['locale'] );
			if($this->input->post('act') == 'forgot_password'){

				$email = $this->input->post('email');
				$this->load->model('mcp/Mcompanies');
				$result = $this->Mcompanies->forgot_password($email);
				if(array_key_exists('error',$result)){
					$this->messages->clear();
					$this->messages->add($result['error'],'error');
					redirect('cp/login/forgot_password');
				}else{
					$this->messages->clear();
					$parse_data = $result['success'];
					$parse_data['hello_txt'] = _('Hello');
					$parse_data['account_details'] = _('Account Details');
					$parse_data['account_details_below'] = _('Your Account Details are given below');
					$parse_data['Username'] = _('Username');
					$parse_data['Password'] = _('Password');
					$parse_data['Email'] = _('Email');
					$parse_data['thanks_regard'] = _('Thanks and Regards');

					/*=====lines tp parse mail======*/
					//$getMail = file_get_contents(base_url().'assets/mail_templates/forgot_password.html');
					$getMail = $this->load->view('mail_templates/'.$lang.'/forgot_password.php',null,true);
					$message = $this->utilities->parseMailText($getMail,$parse_data);
					/*=============================*/
					if($message){
						/*=====lines to send mail ======*/

						$From = $this->config->item('no_reply_email');
						$To = $email;
						$subject = _('Forgot Password Recovery Message');

						if(send_email($To,$From,$subject,$message,NULL,NULL,NULL,'no_reply','company','forgot_password')){
							$this->messages->add(_('Your Username and Password sent to your Email address'),'success');
						}else{
							$this->messages->add(_('couln\'t send mail.please try again'),'error');
						}
					}else{
						$this->messages->add(_('couln\'t parse mail.please try again'),'error');
					}
					redirect('cp/login/forgot_password');
				}
			}else{
				$this->load->view('cp/forgot_password');
			}
		}

		function unsubscribe($verification_code){
			$this->load->model('Mclients');
			$this->Mclients->unsubscribe($verification_code);
			redirect('cp');
		}

		function loggen_via_fdd($unique = '', $obs_pro_id = 0){
			if(($unique != '') && ($obs_pro_id != 0)){
				$this->load->model('M_fooddesk');
				$login = $this->M_fooddesk->check_via_fdd($unique,$obs_pro_id);

				if($login){
					$result = $this->db->get_where('company',array('id'=>$login))->result_array();
					$data = array(
							'cp_user_id' => $result[0]['id'],
							'cp_username' => $result[0]['username'],
							'cp_user_role' => $result[0]['role'],
							'cp_user_parent_id' => $result[0]['parent_id'],
							'cp_is_logged_in' => true,
							'cp_website' => '',
							'login_via' => 'cp'
					);
					$this->session->set_userdata($data);

					$ac_type_id = $result[0]['ac_type_id'];

					if($ac_type_id == 1)
						$this->session->set_userdata('menu_type','free');
					elseif($ac_type_id == 2)
					$this->session->set_userdata('menu_type','basic');
					elseif($ac_type_id == 3)
					$this->session->set_userdata('menu_type','pro');
					elseif($ac_type_id == 4)
					$this->session->set_userdata('menu_type','fdd_light');
					elseif($ac_type_id == 5)
					$this->session->set_userdata('menu_type','fdd_pro');
					elseif($ac_type_id == 6)
					$this->session->set_userdata('menu_type','fdd_premium');
					elseif($ac_type_id == 7)
					$this->session->set_userdata('menu_type','fooddesk_light');

					redirect(base_url().'cp/products/empty_ingredients');
				}
			}
			$data['login_error']= 'error';
			$this->load->view($this->template."/login_form", $data);
		}
		
		function loggen_via_newobs( $unique = '', $obs_comp_id = 0){
			if(($unique != '') && ($obs_comp_id != 0)){
				$this->load->model('M_fooddesk');
				$login = $this->M_fooddesk->check_via_newobs( urldecode( $unique ),$obs_comp_id );
				if($login){
					$result = $this->db->get_where('company',array('id'=>$login))->result_array();
					$data = array(
							'cp_user_id' => $result[0]['id'],
							'cp_username' => $result[0]['username'],
							'cp_user_role' => $result[0]['role'],
							'cp_user_parent_id' => $result[0]['parent_id'],
							'cp_is_logged_in' => true,
							'cp_website' => '',
							'login_via' => 'cp'
					);
					$this->session->set_userdata($data);
					$ac_type_id = $result[0]['ac_type_id'];

					if($ac_type_id == 1)
						$this->session->set_userdata('menu_type','free');
					elseif($ac_type_id == 2)
					$this->session->set_userdata('menu_type','basic');
					elseif($ac_type_id == 3)
					$this->session->set_userdata('menu_type','pro');
					elseif($ac_type_id == 4)
					$this->session->set_userdata('menu_type','fdd_light');
					elseif($ac_type_id == 5)
					$this->session->set_userdata('menu_type','fdd_pro');
					elseif($ac_type_id == 6)
					$this->session->set_userdata('menu_type','fdd_premium');
					elseif($ac_type_id == 7)
					$this->session->set_userdata('menu_type','fooddesk_light');
					redirect(base_url().'cp/orders');
				}
			}
			$data['login_error']= 'error';
			$this->load->view($this->template."/login_form", $data);
		}

	}