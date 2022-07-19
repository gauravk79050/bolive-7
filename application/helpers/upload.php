<?php
	class Upload extends CI_Controller{
		function __construct(){
			parent::__construct();
			$this->load->helper(array('form', 'url', 'phpmailer'));
		}

		function index()
		{
			$this->load->view('upload_form', array('error' => ' ' ));
		}
		
		function mail_test(){
			$this->load->helper('phpmailer');
			send_email('himanshurauthan@cedcoss.com', 'prernagupta@cedcoss.com', 'test subject', '<h2>This is test.</h2>');
		}

		function do_upload()
		{
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png';
			/*$config['max_size']	= '100';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';*/

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload())
			{
				$error = array('error' => $this->upload->display_errors());

				print_r($error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());

				print_r($data);
			}
		}
		

		function test(){
			$this->db->select('id,clients_associated');
			$companyInfo = $this->db->get('company')->result_array();
			if(!empty($companyInfo)){
				foreach ($companyInfo as $company){
					$companId = $company['id'];
					$associated_clients = $company['clients_associated'];
					$associated_clients = explode(',',$associated_clients);
					if(!empty($associated_clients)){
						foreach ($associated_clients as $client){
							$is_added = $this->db->get_where('client_numbers', array('company_id' => $companId, 'client_id' => $client))->result_array();
							if(empty($is_added)){
								$insert_array = array(
									'company_id' => $companId, 
									'client_id' => $client,
									'newsletter' => 'unsubscribe'
								);
								$this->db->insert('client_numbers', $insert_array);
							}
						}
					}
				}
			}
			
			/*print_r($associated_clients); die;
			$this->db->where_in('id',$associated_clients);
			$this->db->delete("clients");
			echo $this->db->last_query();*/
		}
		
		function trasnsfer_holidays($what = 'holiday'){
			if($what == 'holiday')
				$this->db->select('company_id,holiday_dates');
			elseif ($what == 'close')
				$this->db->select('company_id,shop_close_dates');
			
			$holidays_info = $this->db->get('order_settings')->result();
			foreach ($holidays_info as $holidays){
				$company_id = $holidays->company_id;
				if($what == 'holiday')
					$holiday_dates = $holidays->holiday_dates;
				elseif ($what == 'close')
					$holiday_dates = $holidays->shop_close_dates;
				
				$holiday_dates = explode(',',$holiday_dates);
				if(!Empty($holiday_dates)){
					foreach ($holiday_dates as $holiday_date){
						$hd = explode('/',$holiday_date);
						if(!empty($hd) && isset($hd[0]) && isset($hd[1]) && isset($hd[2])){
							$day = $hd[0];
							if(strlen($day) == 1)
								$day = '0'.$day;
							
							$month = $hd[1];
							$year = $hd[2];
							$timestamp = strtotime($year.'-'.$month.'-'.$day);
							$insert = array(
									'company_id' => $company_id,
									'day' => $day,
									'month' => $month,
									'year' => $year,
									'timestamp' => $timestamp,
									'date_added' =>date('Y-m-d H:i:s')
							);
							
							if($what == 'holiday')
								$this->db->insert('company_holidays', $insert);
							elseif ($what == 'close')
								$this->db->insert('company_closedays', $insert);
							
						}
					}
				}
			}
		}
		
		function transfer_company(){
			error_reporting(E_ALL);
			//$companies = $this->db->query('SELECT * FROM `company_tmp` WHERE `id` NOT IN(SELECT `id` FROM `company`)')->result_array();
			$companies = $this->db->query('SELECT * FROM `company_tmp` WHERE `email` NOT IN(SELECT `email` FROM `company`)')->result_array();
			//echo count($companies); die;
			$company_ids = array();
			foreach ($companies as $key => $company){
				
				if($company['id'] != 3865){
					$exists = $this->db->get_where('company', array('id' => $company['id']))->result();
					if(!empty($exists)){
						$update_array = array(
								'email' => $company['email'],
								'approved' => '1',
								'k_assoc' => 1
						);
						
						$this->db->where('id', $company['id']);
						$this->db->update('company', $update_array);
						echo 'updated ==== '.$company['id']."<br/>";
					}else{
						$insert_array = array(
								'parent_id' => 0,
								'role' => 'master',
								'type_id' => 8,
								'country_id' => 21,
								'ac_type_id' => 3,
								'packages_id' => 0,
								'company_name' => $company['company_name'],
								'company_slug' => $company['company_slug'],
								'company_img' => $company['company_img'],
								'company_desc' => $company['company_desc'],
								'company_fb_url' => $company['company_fb_url'],
								'vat' => $company['vat'],
								'first_name' => $company['first_name'],
								'last_name' => $company['last_name'],
								'email' => $company['email'],
								'phone' => $company['phone'],
								'website' => $company['website'],
								'address' => $company['address'],
								'zipcode' => $company['zipcode'],
								'city' => $company['city'],
								'username' => $company['username'],
								'password' => $company['password'],
								'admin_remarks' => $company['admin_remarks'],
								'expiry_date' => '0000-00-00',
								'registration_date' => '2014-08-12',
								'earnings_year' => $company['earnings_year'],
								'registered_by' => $company['registered_by'],
								'approved' => 1,
								'link' => $company['link'],
								'status' => $company['status'],
								'invoice_made' => $company['invoice_made'],
								'payment_received' => $company['payment_received'],
								'email_ads' => $company['email_ads'],
								'footer_text' => $company['footer_text'],
								'company_footer_text' => $company['company_footer_text'],
								'company_footer_link' => $company['company_footer_link'],
								'text_bg_color' => $company['text_bg_color'],
								'text_color' => $company['text_color'],
								'5year_subscription' => $company['5year_subscription'],
								'clients_associated' => $company['clients_associated'],
								'access_super' => $company['access_super'],
								'have_website' => $company['have_website'],
								'package' => $company['package'],
								'domain' => $company['domain'],
								'canregister' => $company['canregister'],
								'partner_id' => $company['partner_id'],
								'partner_status' => $company['partner_status'],
								'affiliate_id' => $company['affiliate_id'],
								'affiliate_status' => $company['affiliate_status'],
								'ibsoft_active' => $company['ibsoft_active'],
								'email_to_send' => $company['email_to_send'],
								'client_number' => $company['client_number'],
								'existing_order_page' => $company['existing_order_page'],
								'client_register_notification' => $company['client_register_notification'],
								'trial' => $company['trial'],
								'manager_id' => $company['manager_id'],
								'excel_import_file_name' => $company['excel_import_file_name'],
								'trial_mail_sent' => $company['trial_mail_sent'],
								'on_trial' => $company['on_trial'],
								'obsdesk_status' => $company['obsdesk_status'],
								'obsdesk_logo' => $company['obsdesk_logo'],
								'obsdesk_footer_text' => $company['obsdesk_footer_text'],
								'partner_total_amount' => $company['partner_total_amount'],
								'partner_total_commission' => $company['partner_total_commission'],
								'partner_invoice_date' => $company['partner_invoice_date'],
								'partner_message' => $company['partner_message'],
								'email_verification' => $company['email_verification'],
								'login_first_time' => $company['login_first_time'],
								'dep_id' => $company['dep_id'],
								'geo_location' => $company['geo_location'],
								'from_bo' => $company['from_bo'],
								'mail_subscription' => $company['mail_subscription'],
								'show_bo_link_in_shop' => $company['show_bo_link_in_shop'],
								'last_login' => $company['last_login'],
								'k_assoc' => $company['k_assoc']
						);
						
						/*$this->db->insert('company', $insert_array);
						$inserted_id = $this->db->insert_id();
						
						$this->load->helper('default_setting');
						do_settings($inserted_id, $insert_array['company_name']);*/
						
						echo $inserted_id."<br />";
						//$this->
					}
				}
			}
		}
	}