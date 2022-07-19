<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Dashboard extends CI_Controller
	{		
		var $template='mcp/';
		
		function __construct()
		{
			parent::__construct();
			//session_start();
			
			$this->load->library('image_lib');
			
			$this->load->helper('html');  
			$this->load->helper('url');
			$this->load->helper('phpmailer');
			
		    $this->load->model($this->template.'Mdashboard');
			$this->load->model($this->template.'Mcompanies');
			$this->load->model($this->template.'Mcompany_type');
			$this->load->model($this->template.'MReports');
			$this->load->model('Mopening_hours');
			
			$current_user = $this->session->userdata('username');
			$is_logged_in = $this->session->userdata('is_logged_in');
			
			if( !$current_user || !$is_logged_in )
			  redirect('mcp/mcplogin','refresh');
		}
		
		function index()
		{	 
		     $this->load->model('MUpgrade');
		     $this->load->model('mcp/Mnotifications');
		     $this->load->model('mcp/Mcomments');
		     
		     $data['days'] = $days = $this->Mopening_hours->get_days();
		     
			 $data['all_companies']=$this->Mcompanies->get_company(array('approved'=>1));
			 $data['all_company_types']=$this->Mcompany_type->select();
			 $data['pending'] = $this->Mcompanies->get_company(array('approved'=>'0'), array('id'=>'ASC'));
			
			 $data['expiring_this_month'] = $this->Mcompanies->get_companies_expiring_this_month();
			 $data['expiring_next_month'] = $this->Mcompanies->get_companies_expiring_next_month();
			 
			 $data['upgrade_requests'] =  $this->MUpgrade->get_upgrade_request( array( 'request_approved' => 0 ) );
			 $data['suggestions'] = $this->MReports->get_suggestion();
			 $data['notifications'] = $this->Mnotifications->get_notifications();
			 $data['contact_requests'] = $this->Mnotifications->get_contact_requests();
			 $data['pending_comments'] = $this->Mcomments->get_comments('company.company_name,comments.*',array('comments.approved' => 0));
			 /*$star_comp_list = array();
			 foreach($data['contact_requests'] as $req){
			 	if(!in_array($req->company_id,$star_comp_list)){
			 		$star_comp_list[] = $req->company_id;
			 	}
			 }
			$comp_stars = array();
			foreach($star_comp_list as $comp_id){
				$comp_stars[$comp_id] = $this->get_company_avg_rating($comp_id);
			}			
			//print_r($comp_stars);die;
				
			 $data['comp_stars'] = $comp_stars;*/
			
			 $this->load->model('Morders');
			 $data['todays_order'] = $this->Morders->count_orders_all(date("Y-m-d 00:00:00"),date("Y-m-d 00:00:00", strtotime(date("Y-m-d")."+1 days")));
			 
			 $data['header'] = $this->template.'/header';
	   		 $data['main'] = $this->template.'/dashboard';
	    	 $data['footer'] = $this->template.'/footer';	
	    	 $this->load->vars($data);
			 $this->load->view($this->template.'/mcp_view');
		}	
		
		function approve()
		{
			$this->load->library('utilities');
			$this->load->model($this->template.'Memail_message');
			$id = $this->input->post('id');
			$ac_type_id = $this->input->post('ac_type_id');
			$update_array = array();
			if($ac_type_id == 2 || $ac_type_id == 3){
				$update_array = array('id'=>$id,'approved'=>'1','trial'=>date("Y-m-d H:i:s",strtotime("+1 month", time())),'on_trial'=>"1");
			}else{
				$update_array = array('id'=>$id,'approved'=>'1');
			}

			$app = $this->Mcompanies->update($update_array);
					
			$updated_company = $this->Mcompanies->get_company(array('id'=>$id),null,'ARRAY');
			
			if($app && !empty($updated_company))
			{
				/**
				 * Doing default settings for the company
				 */
				//$this->load->helper('default_setting');
				do_settings($id,$updated_company[0]['company_name']);
				
				/* -------------------------------------Get direct login link -------------------------------------
				$direct_login_id = NULL;
				$updated_company[0]['direct_login_link'] = '';
				if($updated_company[0]['direct_login_id'] == NULL){
						do{
							$direct_login_id = $this->generate_unique_md5_string();
							$this->db->where('direct_login_id',$direct_login_id);
							$match_result = $this->db->get('company')->result_array();
						}while(!empty($match_result));
						$this->db->where('id',$id);
						$this->db->set('direct_login_id',$direct_login_id);
						$this->db->update('company');
				}
				else{
					$direct_login_id = $updated_company[0]['direct_login_id'];
				}
				$login_link = base_url().'cp/login/validate?direct_login='.$direct_login_id;
				$updated_company[0]['direct_login_link'] = $data['direct_login_link'] = "<a href='".$login_link ."'>".$login_link."</a>";
				/*------------------------------------- End direct login link -------------------------------------*/
				
				$email_templates = $this->Memail_message->select();
				
				if($updated_company[0]['from_bo'] == 1){
					
					$this->lang->load('mail','dutch');
					
					$data['username'] = $updated_company[0]['username'];
					$data['password'] = $updated_company[0]['password'];
					$data['company_slug'] = $updated_company[0]['company_slug'];
					$data['trialEndDate'] = date("d-m-Y", strtotime($updated_company[0]['trial']));
					$companyTypeIds = explode('#',$updated_company[0]['type_id']);
					$company_type_slug = $this->Mcompany_type->select(array('id' => $companyTypeIds['0']));
					if(!empty($company_type_slug))
						$data['company_type_slug'] = $company_type_slug[0]->slug;
					else 
						$data['company_type_slug'] = 'bakker';
					
					if($updated_company[0]['ac_type_id'] == 1){
						$mail_subject = _('Aanmelding succes: pakket-FREE');
						$mail_body = $this->load->view('mail_templates/company_approval_mail_free', $data, true);
					}elseif($updated_company[0]['ac_type_id'] == 2){
						$mail_subject = _('Aanmelding succes: pakket-BASIC');
						$mail_body = $this->load->view('mail_templates/company_approval_mail_basic', $data, true);
					}elseif($updated_company[0]['ac_type_id'] == 3){
						$mail_subject = _('Aanmelding succes: pakket-PRO');
						$mail_body = $this->load->view('mail_templates/company_approval_mail_pro', $data, true);
					}
				}else{
					$mail_template = $email_templates[0]->company_approval_message;
					$mail_subject = $email_templates[0]->company_approval_subject;
					$mail_body = $this->utilities->parseMailText($mail_template, $updated_company[0]);
				}
				send_email($updated_company[0]['email'], $this->config->item('site_admin_email'), $mail_subject, $mail_body,NULL,NULL,NULL,'site_admin','company','company_approval');
				//send_email("rishabhchauhan@cedcoss.com", $this->config->item('site_admin_email'), $mail_subject, $mail_body,NULL,NULL,NULL,'site_admin','company','company_approval');
				echo json_encode(array('message'=>'success'));
					
			}else{
				echo 'failed';
			}
		}
		
		
		function disapprove()
		{
			$id=$this->input->post('id');
			$app=$this->Mcompanies->delete($id);
			
			 if($app)
			 echo 'deleted';
			 else
			 echo 'not deleted';
		}
		
		function renewal_info($id)
		{  
		   if($this->input->post()){
		   
		   	$sql = "INSERT INTO `company_renewal` (`company_id`,`registration_date`,`expiry_date`,`invoice_made`,`date_of_invoice`) 
			        VALUES (
					'$id',
					'".$this->input->post('registration_date')."',
					'".$this->input->post('expiry_date')."',
					'".$this->input->post('invoice_made')."',
					'".$this->input->post('date_of_invoice')."'
					)";
		     
			 $query=$this->db->query($sql);
			 $record['id'] = $id;
			 $record['expiry_date'] = $this->input->post('expiry_date');
			  //print_r($record);
			$this->Mcompanies->update($record);
		   }
		  
		   else {
		  
		  $data['company'] = $this->Mcompanies->get_company(array('id'=>$id));
		   
		  $data['header'] = '';
		  $data['main'] = $this->template.'/renewal_info';
		  $data['footer'] = '';
		  $this->load->vars($data);
		  $this->load->view($this->template.'/mcp_view');
		  
		  } 
		}
		
		
		/*function block_ip( $ip_address )
		{
		    $this->Mcompanies->block_ip( $ip_address );
			redirect('mcp/dashboard','refresh');
		}*/
		
		function delete_report( $report_id )
		{
		    $this->Mcompanies->delete_report( $report_id );
		    redirect('mcp/dashboard','refresh');
		}
		
		function approve_req( $request_id )
		{
		    $this->load->model('MUpgrade');
			$this->MUpgrade->approve_upgrade_request( $request_id );
		    redirect('mcp/dashboard','refresh');
		}
		
		/**
		 * This public function is used to send notifiction to respected Admin
		 * @param int $id It is the ID of otification
		 */
		public function send_notification($id = null){
			if($id && is_numeric($id)){
				$this->load->library('utilities');
				$this->lang->load('mail','dutch');
				$this->load->model('mcp/Mnotifications');
				$notification = $this->Mnotifications->get_notifications(array("order_request_portal.id" => $id));
				
				if(!empty($notification)){
					$notification = $notification[0];
					$data['clientname'] = $notification->clientname;
					$data['city'] = $notification->clientcity;
					$data['company_portal_link'] = $notification->company_portal_link;
					$data['portal_link'] = $this->config->item("portal_url");
					$data['company_name'] = $notification->company_name;
					
					$mail_body = $this->load->view("mail_templates/mail_order_free_request",$data,true);
					if(send_email($notification->email, "info@bestelonline.nu", $this->lang->line('contact_subject_txt'), $mail_body, "Bestelonline.nu", NULL, NULL, 'no_reply_bo', 'company', 'request_order_portal'))
						$this->Mnotifications->delete_notifications($id);
				}
				
			}
			
			redirect(base_url()."mcp/dashboard");
			
		}
		
		/**
		 * This function is used to delete notification
		 * @param int $id ID to be deleted
		 */
		function delete_notification($id = null){
			if($id){
				$this->load->model('mcp/Mnotifications');
				$this->Mnotifications->delete_notifications($id);
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		
		/**
		 * This public function is used to show particular corrections in details
		 * @access public
		 */
		public function suggested_corrections_detail($id = null){
			$data['days'] = $days = $this->Mopening_hours->get_days();
			if($id){
				$data['suggestion'] = $this->MReports->get_suggestion_approval_data($id);
				$company = $this->Mcompanies->get_company(array('id'=>$data['suggestion']['company_id']),array('id'=>'DESC'));
				$data['companyInfo'] = $company[0];
				$data['openingHours'] = $this->Mopening_hours->get_opening_hours($data['suggestion']['company_id']);
		
			}
			$this->load->vars($data);
			$this->load->view($this->template.'/suggested_corrections_detail');
		}
		
		/**
		 * This public function is used to block IP of particular suggestions
		 * @access public
		 */
		public function block_ip($id = null){
			if($id){
				$suggestion = $this->MReports->get_suggestion_approval_data($id);
				$ip = $suggestion['ip_address'];
				//$this->db->insert("block_ips",array('ip_address' => $ip));
				$this->block_ip_address($ip);
				$this->MReports->disapprove_suggestion($id);
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This public function is used to block IP of particular suggestions
		 * @access public
		 */
		public function delete_company($id = null){
			if($id){
				$suggestion = $this->MReports->get_suggestion_approval_data($id);
				$company_id = $suggestion['company_id'];
				$this->Mcompanies->delete($company_id);
				$this->MReports->disapprove_suggestion($id);
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This public function is used to approve suggested corrections
		 * @access public
		 */
		function approve_suggestion()
		{
			if($this->input->post('suggestion_id'))
			{
				$id = $this->input->post('suggestion_id');
				$suggestion_info = $this->MReports->get_suggestion_approval_data($id);
				$company_id = $suggestion_info['company_id'];
				$update_array = array();
				if($this->input->post('company_name') && $this->input->post('company_name') != '')
					$update_array['company_name'] = $this->input->post('company_name');
				if($this->input->post('address') && $this->input->post('address') != '')
					$update_array['address'] = $this->input->post('address');
				if($this->input->post('zipcode') && $this->input->post('zipcode') != '')
					$update_array['zipcode'] = $this->input->post('zipcode');
				if($this->input->post('city') && $this->input->post('city') != '')
					$update_array['city'] = $this->input->post('city');
				if($this->input->post('website') && $this->input->post('website') != '')
					$update_array['website'] = $this->input->post('website');
				if($this->input->post('description') && $this->input->post('description') != '')
					$update_array['company_desc'] = $this->input->post('description');
					
				$this->db->where('id' , $company_id);
				$this->db->update('company',$update_array);
		
				$days = $this->Mopening_hours->get_days();
		
				$time_1 = $this->input->post('time_1');
				$time_2 = $this->input->post('time_2');
				$time_3 = $this->input->post('time_3');
				$time_4 = $this->input->post('time_4');
				foreach($days as $d)
				{
					$opening_hours = array(
							'time_1'=>$time_1[$d->id],
							'time_2'=>$time_2[$d->id],
							'time_3'=>$time_3[$d->id],
							'time_4'=>$time_4[$d->id]
					);
					 
					$this->db->where('company_id' , $company_id);
					$this->db->where('day_id' , $d->id);
					$this->db->update('opening_hours',$opening_hours);
		
				}
				$flag=$this->MReports->disapprove_suggestion($id);
			}
			 
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This function is used to disapprove the suggestion
		 */
		function disapprove_suggestion($id = null)
		{
			//$id=$this->input->post('id');
			if($id && is_numeric($id)){
				$this->MReports->disapprove_suggestion($id);
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * @name faq_new_admin
		 * @property Function to load help file- Integrating CI and Help and supportscript of Codecanyon
		 * @access public
		 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
		 * 
		 */
		function faq_new_admin(){
			 $_SESSION['help_logged_in'] = true;
			 
		     $this->load->model('MUpgrade');
		     $this->load->model('mcp/Mnotifications');
		     
		     $data['days'] = $days = $this->Mopening_hours->get_days();
		     
			 $data['all_companies']=$this->Mcompanies->get_company(array('approved'=>1));
			 $data['all_company_types']=$this->Mcompany_type->select();
			 $data['pending'] = $this->Mcompanies->get_company(array('approved'=>'0'), array('id'=>'ASC'));
			
			 $data['expiring_this_month'] = $this->Mcompanies->get_companies_expiring_this_month();
			 $data['expiring_next_month'] = $this->Mcompanies->get_companies_expiring_next_month();
			 
			 $data['upgrade_requests'] =  $this->MUpgrade->get_upgrade_request( array( 'request_approved' => 0 ) );
			 $data['suggestions'] = $this->MReports->get_suggestion();
			 $data['notifications'] = $this->Mnotifications->get_notifications();
			 
			 $this->load->model('Morders');
			 $data['todays_order'] = $this->Morders->count_orders_all(date("Y-m-d 00:00:00"),date("Y-m-d 00:00:00", strtotime(date("Y-m-d")."+1 days")));
			 
			 $data['header'] = $this->template.'/header';
	   		 $data['main'] = $this->template.'/faq_new_admin';
	    	 $data['footer'] = $this->template.'/footer';	
	    	 $this->load->vars($data);
			 $this->load->view($this->template.'/mcp_view');
		}
		
		/**
		 * @name forum_admin
		 * @property Function to load forum admin file- Integrating CI and Forum script of Codecanyon
		 * @access public
		 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
		 *
		 */
		function forum(){
			$this->redirect_login();
			//$_SESSION['forum_admin_logged_in'] = true;
			$this->load->model('MUpgrade');
			$this->load->model('mcp/Mnotifications');
			 
			$data['days'] = $days = $this->Mopening_hours->get_days();
			 
			$data['all_companies']=$this->Mcompanies->get_company(array('approved'=>1));
			$data['all_company_types']=$this->Mcompany_type->select();
			$data['pending'] = $this->Mcompanies->get_company(array('approved'=>'0'), array('id'=>'ASC'));
				
			$data['expiring_this_month'] = $this->Mcompanies->get_companies_expiring_this_month();
			$data['expiring_next_month'] = $this->Mcompanies->get_companies_expiring_next_month();
		
			$data['upgrade_requests'] =  $this->MUpgrade->get_upgrade_request( array( 'request_approved' => 0 ) );
			$data['suggestions'] = $this->MReports->get_suggestion();
			$data['notifications'] = $this->Mnotifications->get_notifications();
		
			$this->load->model('Morders');
			$data['todays_order'] = $this->Morders->count_orders_all(date("Y-m-d 00:00:00"),date("Y-m-d 00:00:00", strtotime(date("Y-m-d")."+1 days")));
		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/forum_admin';
			$data['footer'] = $this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		/**
		 * unset client session variables for forum and set corresponding admin session variables
		 */
		function redirect_login(){
			//session_start();
			
			unset($_SESSION['forum_user_logged_in']);
			unset($_SESSION['sforum_logged_in']);
			unset($_SESSION['sforum_user_id']);
			unset($_SESSION['sforum_user_role']);
			unset($_SESSION['sforum_user_username']);
			
			/* ##################### FORUM INTEGRATION CODE: STARTS ###################### */
				
			//check if the user detail is present in the sforum_users table..
				
			$qry_sforum = $this->db->query('SELECT * FROM `sforum_users` WHERE `username` = "'.$this->session->userdata('username').'"');
			$res_sforum = $qry_sforum->result_array();
			//echo "forum users";
			//print_r($res_sforum);
			if(is_array($res_sforum) && !empty($res_sforum)){
					
				//$_SESSION[TABLES_PREFIX.'logged_in'] = true;
				$_SESSION['help_logged_in'] = true;
			
				$_SESSION['forum_admin_logged_in'] = true;
				$_SESSION['sforum_logged_in'] = true;
				$_SESSION['sforum_user_id'] = $res_sforum[0]['id'];
				$_SESSION['sforum_user_role'] = 'admin';
				$_SESSION['sforum_user_username'] = $this->session->userdata('username');
					
				//echo "id 11111".$res_sforum[0]['id']; die;
					
			} else {
				//$qry_sforum = $this->db->query('INSERT INTO `sforum_users` (`username`,`role`) VALUES("'.$this->input->post('username').'", "user" )');
				$param_sforum = array("username" => $this->session->userdata('username'), 'role' => 'admin');
					
				$this->db->insert('sforum_users',$param_sforum);
					
				$sfroum_id = $this->db->insert_id();
				//echo "iddddddd".$sfroum_id; die;
				$_SESSION['help_logged_in'] = true;
					
				$_SESSION['forum_admin_logged_in'] = true;
				$_SESSION['sforum_logged_in'] = true;
				$_SESSION['sforum_user_id'] = $sfroum_id;
				$_SESSION['sforum_user_role'] = 'admin';
				$_SESSION['sforum_user_username'] = $this->session->userdata('username');
			}
			/* ##################### FORUM INTEGRATION CODE: ENDS ###################### */
		}
		
		function get_company_avg_rating( $company_id = NULL)
		{
			if( !$company_id )
				return false;
		
			$company_avg_rating = array();
			 
			//$this->load->model('MComments');
			//$ratings = $this->MComments->get_comments( array( 'company_id' => $company_id)  );
			$this->db->where('company_id',$company_id);			
			$ratings = $this->db->get('comments')->result();
			
			if( !empty( $ratings ) )
			{
				$total_nor = count($ratings);
				$total_rates = 0;
				 
				foreach( $ratings as $r )
				{
					$total_rates += $r->rate;
					$company_avg_rating['latest_comment'] = $r->comment;
				}
				 
				$company_avg_rating['rate'] = $total_rates/$total_nor;
				$company_avg_rating['count'] = $total_nor;
				 
			}
			return $company_avg_rating;
		}
		
		function appr_contact_req( $request_id = NULL)
		{
			//die("asdads");
			$this->lang->load('mail','dutch');
			$this->load->model('mcp/Mnotifications');
				
			if( !$request_id )
				redirect(base_url()."mcp/dashboard");
		
			$where_array = array(
					'contact_requests.id' => $request_id
			);
			$contact_requests = $this->Mnotifications->get_contact_requests($where_array);
			if(!empty($contact_requests)){
				$email = $contact_requests[0]->email;
				$company_email = $contact_requests[0]->company_email;
				
				$company_type_slug = '';
				$company_info = $this->Mcompanies->get_company(array( 'company_slug' =>$contact_requests[0]->company_slug, 'approved'=>1));
				if(!empty($company_info)){
					$all_company_types = $this->Mcompany_type->select();
					$company_types = $company_info['0']->type_id;
					$company_type_array = explode("#",$company_types);
					foreach ($all_company_types as $all_company_type){
						if($all_company_type->id == $company_type_array[0]){
							$company_type_slug = $all_company_type->slug;
							break; 
						}
					}
				}
				
				$data['company_url_portal'] = '';
				if($company_type_slug != ''){
					$data['company_url_portal'] = $this->config->item('portal_url').$company_type_slug.'/'.$contact_requests[0]->company_slug;
				}
				
				$message = '';
					
				$data['fname'] = $contact_requests[0]->first_name;
				$data['lname'] = $contact_requests[0]->last_name;
				$data['email'] = $email;
				$data['phone'] = $contact_requests[0]->phone;
				$data['user_message'] = $contact_requests[0]->feedback_msg;
				$data['register_user'] = $this->input->post('register_true');
				$message = $this->load->view('mail_templates/contact_form',$data,true);
					
				$query = send_email($company_email, "info@bestelonline.nu", $this->lang->line('contact_subject_txt'), $message, "Bestelonline.nu", NULL, NULL, 'no_reply_bo', 'company', 'forward_contact_mail');
				if($query){
					$this->Mnotifications->delete_contact_requests($request_id);
				}
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		function disappr_contact_req( $request_id = NULL)
		{
			$this->load->model('mcp/Mnotifications');
			if( !$request_id )
				redirect(base_url()."mcp/dashboard");
				
			$where_array = array(
					'contact_requests.id' => $request_id
			);
			$contact_requests = $this->Mnotifications->get_contact_requests($where_array);
			if(!empty($contact_requests)){
				$this->Mnotifications->delete_contact_requests($request_id);
			}
		
				
			redirect(base_url()."mcp/dashboard");
		}
		
		function block_cr_ip($request_id = NULL){
			$this->load->model('mcp/Mnotifications');
			if( !$request_id )
				redirect(base_url()."mcp/dashboard");
			
			$where_array = array(
					'contact_requests.id' => $request_id
			);
			$contact_requests = $this->Mnotifications->get_contact_requests($where_array);
			if(!empty($contact_requests)){
				$this->block_ip_address($contact_requests[0]->ip_address);
				$this->Mnotifications->delete_contact_requests($request_id);
			}
			
			
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This function inserts an ip address in blocked ip's list
		 */
		private function block_ip_address($ip_address = NULL){
			if(!$ip_address){
				return false;
			}
			
			$this->db->insert("block_ips",array('ip_address' => $ip_address));
			if($this->db->insert_id()){
				return true;
			}
		}
		
		/**
		 * This function generates a unique md5 string
		 */
		private function generate_unique_md5_string(){
			$this->load->helper('string');
			return random_string('unique');
		}
		
		/**
		 * Function to generate random string and returned for being used anywhere in captcha or code.
		 * @param integer $length String length for random word
		 * @return string $randomString randomly generated word
		 */
		private function generateRandomString($length = 10) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}
		
		/**
		 * This public function is used to approve pending comments
		 * @param int $comment_id ID of comment that has to be approved
		 */
		function approve_comment($comment_id = 0){
			if($comment_id){
				$this->load->model('mcp/Mcomments');
				$this->Mcomments->update_comment(array('comment_id' => $comment_id), array('approved' => 1));
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This public function is used to disapprove pending comments
		 * @param int $comment_id ID of comment that has to be disapproved
		 */
		function disapprove_comment($comment_id = 0){
			if($comment_id){
				$this->load->model('mcp/Mcomments');
				$this->Mcomments->delete_comment(array('comment_id' => $comment_id));
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This public function is used to block IP from where comments is placed
		 * @param int $comment_id ID of comment whose IP has to be blocked
		 */
		function block_comment_ip($comment_id = 0){
			if($comment_id){
				$this->load->model('mcp/Mcomments');
				$ip_info = $this->Mcomments->get_comments('comments.user_ip', array('comments.comment_id' => $comment_id));
				if(!empty($ip_info)){
					$this->block_ip_address($ip_info['0']->user_ip);
					$this->Mcomments->delete_comment(array('comment_id' => $comment_id));
				}
			}
			redirect(base_url()."mcp/dashboard");
		}
		
		/**
		 * This method takes the backup of 'ci_bp_sessions','ci_desk_sessions','ci_sessions','log_api' tables , and then truncates them.
		 * @name tables_backup
		 * @access public
		 * @param none
		 * @author Amit Sahu <amitkumarsahu@cedcoss.com>
		 */
		function tables_backup()
		{		
			$mysqlExportPath= dirname(__FILE__)."/../../../bckup-".date("d-m-Y").'.gz';				
			
			$this->load->dbutil();
			$prefs = array(
					'tables'      => array('ci_bp_sessions','ci_desk_sessions','ci_sessions','log_api'),  // Array of tables to backup.
					'ignore'      => array(),           // List of tables to omit from the backup
					'format'      => 'zip',             // gzip, zip, txt
					'filename'    => 'bckup-'.date("d-m-Y"),    // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
					'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
					'newline'     => "\n"               // Newline character used in backup file
			);
			
			$backup =& $this->dbutil->backup($prefs);
			$this->load->helper('file');
			$res=write_file($mysqlExportPath, $backup);
	
			if($res)
			{
				$this->Mdashboard->bckup_table_truncate();
				echo true;
			}else{
				echo false;
			}
			
		}
	}
?>