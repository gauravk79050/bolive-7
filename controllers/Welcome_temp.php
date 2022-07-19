<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Welcome_temp extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	 
	var $template = NULL;
	
	public function __construct()
    {
		parent::__construct();
		// $this->load->library('recaptcha');
		
		$this->load->helper('url');
		$this->load->helper('captcha');
		$this->load->helper('phpmailer');

		
		$this->load->library('messages');
		$this->load->library('form_validation');
		
		$this->mail_template = 'mail_templates/'; // base_url().'application/views/mail_templates/';
		
		$this->load->model('mcp/Mcompanies');
		$this->load->model('Madmin');
    } 

	
	/**
	 * Function used to generate mail content on any order for a company admin and then passed to a JAVA App to genrate the mail without showing window print dialogue box.
	 * 
	 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
	 * 
	 * @param mixed $api_key api_id provided to any company admin
	 * 
	 * @return null returns nothing but echo the whole mail content for an order for a company admin
	 * 
	 */
	public function print_cp_mails($api_key = null, $font_size = 12, $today_or_all = 1, $update_db = "check" ){
		
		$fp = fopen(dirname(__FILE__).'/../../mail2print.txt', 'a');
		fwrite($fp, date('Y-m-d H:i:s').'--> '.$api_key.'--'.$font_size.'--'.$today_or_all.'--'.$update_db."\n\n");
		fclose($fp);
		
		header('Content-Type: text/html; charset=utf-8');
		
		//checking for an apikey..
		if($api_key != null){
			$result = $this->db->query("SELECT `company_id` FROM `api` WHERE `api_id` = '".$api_key."' LIMIT 1");
			
			//if result is found..
			if ($result->num_rows() > 0)
			{
				foreach ($result->result() as $row)
				{
					//fetching order if company id found..
					if($row->company_id > 0){
						
						$res_order = array();
						
						// Fetching ROLE of company
						$companyInfo = $this->db->query("SELECT `role` FROM `company` WHERE `id` = '".$row->company_id."'")->result();
						if(!empty($companyInfo)){
							
							// If ROLE is SUPER then
							if($companyInfo[0]->role == 'super'){
								
								// Fetching sub-shop's ID
								$subadminsIds = $this->db->query("SELECT `id` FROM `company` WHERE `parent_id` = '".$row->company_id."' AND `role` = 'sub'")->result_array();
								if(!empty($subadminsIds)){

									$subadminsId = implode(',', array_map(array("Welcome","get_id"), $subadminsIds));
									
									//foreach ($subadminsIds as $subadminsId){
										if($today_or_all == 2){
											//$res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
											$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
										}else{
											// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
											$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 ORDER BY `id` DESC LIMIT 1")->result_array();
										}
										//$res_order = array_merge($res_order, $res_order_tmp);
									//}
									
									$fp = fopen(dirname(__FILE__).'/../../mail2printd.txt', 'a');
									fwrite($fp, date('Y-m-d H:i:s').'--> '.json_encode($res_order)."\n\n");
									fclose($fp);
								}
							}else{
								if($today_or_all == 2){
									$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
								}else{
									// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
									$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 ORDER BY `id` DESC LIMIT 1")->result_array();
								}
							}
						}
						
						
						if (!empty($res_order))
						{
							$order_data = array();
							$order_details_data = array();
							
							foreach ($res_order as $order)
							{
								$order_data = $order;
																
								//if order id found
								if($order['id'] > 0){

									//fetching order details for this order id..
									$res_order_details = $this->db->query("SELECT * FROM `order_details`,`products` WHERE `orders_id` = '".$order['id']."' AND `order_details`.`products_id` = `products`.`id`");
									
									//if order details found for this order id..
									//if order found..
									if ($res_order_details->num_rows() > 0)
									{
										//$order_details_data = $res_order_details;
										foreach ($res_order_details->result_array() as $order_details)
										{											
											//$res_disc = $this->db->query("SELECT * FROM `products_discount` WHERE `products_id` = '".$order_details['products_id']."'");
											$res_disc = $this->db->get_where('products_discount', array('products_id' => $order_details['products_id']), 1, 0);
											$res_disc_arr = $res_disc->result_array();
											if(isset($res_disc_arr[0])){
												$order_details['product_discount'] = $res_disc_arr[0]['discount_per_qty'];
											} else {
												$order_details['product_discount'] = 0;
											}
											
											$order_details_data[] = $order_details;											
										}
									}
								}
								
								//preparing mail content..
								$order_id = $order['id'];
								$client_id = $order['clients_id'];
								
								if( !$order_id || !$client_id ){									
									return false;
								}
								
								$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
								$this->db->join('country','country.id = clients.country_id','left');
								$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
								$company = $this->db->where( array('id'=>$order['company_id']) )->get( 'company' )->row();
																
								$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order['company_id']) )->get( 'client_numbers' )->result();
								$c_discount_number = '--';
								if(!empty($client_discount_number)){
									$c_discount_number = $client_discount_number['0']->discount_card_number;
								}
								
								$sub_admin = array();
								if($company->role == 'super'){
									$this->db->select("company_name,email_ads");
									$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
								}
								
								$this->db->select('emailid, subject_emails, orderreceived_msg, disable_price, activate_discount_card, disc_per_amount, disc_after_amount');
								$company_gs = $this->db->where( array('company_id'=>$order['company_id']) )->get( 'general_settings' )->result();
								if(!empty($company_gs))
									$company_gs = $company_gs['0'];								
								
								if( !empty($client) && !empty($company) && !empty($company_gs) )
								{
									$company_email = $company_gs->emailid;
									$company_name = $company->company_name;
									if(!empty($sub_admin))
										$company_name = $sub_admin['company_name'];
									
									$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
									$orderreceived_msg = $company_gs->orderreceived_msg;
									$products_pirceshow_status = $company_gs->disable_price;
									$is_set_discount_card = $company_gs->activate_discount_card;
										
									$Options4 = '';
										
									if(!empty($sub_admin)){
										if($sub_admin['company_name'] == "1")
										{
											$email_messages = $this->db->get('email_messages')->row();
											$Options4 = $email_messages->emailads_text_message;
										}
									}else{
										if($company->email_ads == "1")
										{
											$email_messages = $this->db->get('email_messages')->row();
											$Options4 = $email_messages->emailads_text_message;
										}
									}
										
									// CARDGATE PAYMENT
									$order_data['billing_option'] = 'Cardgate';
									if($order_data['payment_via_paypal'] == 2){
										$this->db->select('billing_option');
										$transaction_info = $this->db->get_where('payment_transaction', array('order_id' => $order_data['temp_id']))->result();
										if(!empty($transaction_info))
											$order_data['billing_option'] = $transaction_info['0']->billing_option;
									}
									
									
									$mail_data['order_id'] = $order_id;
									$mail_data['order_data'] = $order_data;
									$mail_data['ip_address'] = $order_data['ip_address'];
									$mail_data['client'] = $client;
									$mail_data['company_name'] = $company_name;
									$mail_data['products_pirceshow_status'] = $products_pirceshow_status;
									$mail_data['order_details_data'] = $order_details_data;
									//$mail_data['disc_per_client_amount'] = $disc_per_client_amount;
									$mail_data['company_gs'] = $company_gs;
									$mail_data['c_discount_number'] = $c_discount_number;
									$mail_data['Options4'] = $Options4;
									$mail_data['orderreceived_msg'] = $orderreceived_msg;
									$mail_data['is_set_discount_card'] = $is_set_discount_card;
									$mail_data['font_size'] = $font_size;
									$mail_data['is_international'] = false;
									$mail_data['is_send_order_mail'] = true;
										
									//countries details for international delivery
									if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
										$mail_data['is_international'] = true;
									}
									/* $countries = array();
									if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
										$mail_data['is_international'] = true;
										$this->db->select('id,country_name');
										$country_arr = $this->db->get('country')->result();
										foreach($country_arr as $country){
											$countries[$country->id] = $country;
										}
									}
									$mail_data['countries'] = $countries; */
									
									//$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
									if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
										$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin_int', $mail_data, true );
									}
									else{
										$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
									}
									


									$insert_log = array(
											'order_id' => $order_id,
											'template' => htmlspecialchars($mail_body),
											'meta' => $api_key.'=='.$font_size.'=='.$today_or_all.'=='.$update_db,
											'order_details' => json_encode($order_details_data),
											'date' => date("Y-m-d H:i:s")
									);
									
									$this->db->insert('print_master_log', $insert_log);
									
									header("Content-Type: text/html; charset=UTF-8"); // For showing dutch accent as it is..
									echo $mail_body;
										
								}
								
								//updating the order table to set "printed" column value as 1 for the current row..
								/*if($api_key != 874187){*/
									//$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order['id']);							
								/*}*/
									
								//update db row depending on the parameter passed..
								/*
								if((!isset($update_db)) || ($update_db == 0) || ($update_db == null) || ($update_db == '')){									
									$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order['id']);
								}*/
								if($update_db == "print"){									
									$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order['id']);
								}
							}
						}else{
							// IF NO RECORDS FOUND TO PRINT
							show_404();
							exit;
							
						}				
						
					}
				} 
			}
		} else {
				//nothing found for this api id..
				//echo "nothing found";
		}
	}
	
	/**
	 * This private function is used to fetch delivery settings
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $delivery_settings Array of delivery areas
	 */
	private function get_delivery_settings($company_id = null){
		$delivery_settings = array();
	
		if($company_id && is_numeric($company_id)){
	
			$this->db->where('company_id',$company_id);
			$delivery_settings = $this->db->get('company_delivery_settings')->result();
		}
	
		return $delivery_settings;
	}
	
	
	function get_id($entry) {
		return $entry['id'];
	}
	
	/**
	 * Function to print pdf version of order
	 * @param $today_or_all if 1 = all or 2 = today only
	 */
	function test_print($order_id = 0, $company_id = 0){
		
		//$res_order = $this->db->query("SELECT * FROM `orders` WHERE `id` = '".$order_id."' LIMIT 1")->result_array();
		$res_order = $this->db->query("SELECT orders.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `orders`.`id` = '".$order_id."' LIMIT 1")->result_array();
		//checking for an apikey..
		// Fetching ROLE of company
		// $companyInfo = $this->db->query("SELECT `role` FROM `company` WHERE `id` = ".$company_id)->result();
		//print_r($res_order); die;
		if (!empty($res_order))
		{
			$order_data = array();
			$order_details_data = array();
				
			foreach ($res_order as $order)
			{
				$order_data = $order;
		
				//if order id found
				if($order['id'] > 0){
		
					//fetching order details for this order id..
					$res_order_details = $this->db->query("SELECT * FROM `order_details`,`products` WHERE `orders_id` = '".$order['id']."' AND `order_details`.`products_id` = `products`.`id`");
					$order_detail_query = $this->db->last_query();
					//if order details found for this order id..
					//if order found..
					if ($res_order_details->num_rows() > 0)
					{
						//$order_details_data = $res_order_details;
						foreach ($res_order_details->result_array() as $order_details)
						{
							//$res_disc = $this->db->query("SELECT * FROM `products_discount` WHERE `products_id` = '".$order_details['products_id']."'");
							$res_disc = $this->db->get_where('products_discount', array('products_id' => $order_details['products_id']), 1, 0);
							$res_disc_arr = $res_disc->result_array();
							if(isset($res_disc_arr[0])){
								$order_details['product_discount'] = $res_disc_arr[0]['discount_per_qty'];
							} else {
								$order_details['product_discount'] = 0;
							}
								
							$order_details_data[] = $order_details;
						}
					}
				}
		
				//preparing mail content..
				$order_id = $order['id'];
				$client_id = $order['clients_id'];
		
				if( !$order_id || !$client_id ){
					return false;
				}
		
				$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
				$this->db->join('country','country.id = clients.country_id','left');
				$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
				$company = $this->db->where( array('id'=>$order['company_id']) )->get( 'company' )->row();
		
				$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order['company_id']) )->get( 'client_numbers' )->result();
				$c_discount_number = '--';
				if(!empty($client_discount_number)){
					$c_discount_number = $client_discount_number['0']->discount_card_number;
				}
		
				$sub_admin = array();
				if($company->role == 'super'){
					$this->db->select("company_name,email_ads");
					$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
				}
		
				$this->db->select('emailid, subject_emails, orderreceived_msg, disable_price, activate_discount_card, disc_per_amount, disc_after_amount');
				$company_gs = $this->db->where( array('company_id'=>$order['company_id']) )->get( 'general_settings' )->result();
				if(!empty($company_gs))
					$company_gs = $company_gs['0'];
		
				if( !empty($client) && !empty($company) && !empty($company_gs) )
				{
					$company_email = $company_gs->emailid;
					$company_name = $company->company_name;
					if(!empty($sub_admin))
						$company_name = $sub_admin['company_name'];
						
					$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
					$orderreceived_msg = $company_gs->orderreceived_msg;
					$products_pirceshow_status = $company_gs->disable_price;
					$is_set_discount_card = $company_gs->activate_discount_card;
		
					$Options4 = '';
		
					if(!empty($sub_admin)){
						if($sub_admin['company_name'] == "1")
						{
							$email_messages = $this->db->get('email_messages')->row();
							$Options4 = $email_messages->emailads_text_message;
						}
					}else{
						if($company->email_ads == "1")
						{
							$email_messages = $this->db->get('email_messages')->row();
							$Options4 = $email_messages->emailads_text_message;
						}
					}
		
					// CARDGATE PAYMENT
					$order_data['billing_option'] = 'Cardgate';
					if($order_data['payment_via_paypal'] == 2){
						$this->db->select('billing_option');
						$transaction_info = $this->db->get_where('payment_transaction', array('order_id' => $order_data['temp_id']))->result();
						if(!empty($transaction_info))
							$order_data['billing_option'] = $transaction_info['0']->billing_option;
					}
						
						
					$mail_data['order_id'] = $order_id;
					$mail_data['order_data'] = $order_data;
					$mail_data['ip_address'] = $order_data['ip_address'];
					$mail_data['client'] = $client;
					$mail_data['company_name'] = $company_name;
					$mail_data['products_pirceshow_status'] = $products_pirceshow_status;
					$mail_data['order_details_data'] = $order_details_data;
					//$mail_data['disc_per_client_amount'] = $disc_per_client_amount;
					$mail_data['company_gs'] = $company_gs;
					$mail_data['c_discount_number'] = $c_discount_number;
					$mail_data['Options4'] = $Options4;
					$mail_data['orderreceived_msg'] = $orderreceived_msg;
					$mail_data['is_set_discount_card'] = $is_set_discount_card;
					$mail_data['font_size'] = 12;
					$mail_data['is_international'] = false;
					$mail_data['is_send_order_mail'] = true;
		
					
					//echo "<pre>"; print_r($order_details_data); die;
					
					//countries details for international delivery
					if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
						$mail_data['is_international'] = true;
					}
						
					//$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
					if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
						$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin_int', $mail_data, true );
					}
					else{
						$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
					}
					
					$insert_log = array(
							'order_id' => $order_id,
							'template' => htmlspecialchars($mail_body),
							'meta' => "===",
							'order_detail_query' => $order_detail_query,
							'order_details' => json_encode($order_details_data),
							'date' => date("Y-m-d H:i:s")
					);
						
					$this->db->insert('print_master_log', $insert_log);
						
					header("Content-Type: text/html; charset=UTF-8"); // For showing dutch accent as it is..
					echo $mail_body;
				}
		
			}
		}else{
			// IF NO RECORDS FOUND TO PRINT
			show_404();
			exit;
		}
	}
	
	/**
	 * Function used to generate mail content on any order for a company admin and then passed to a JAVA App to genrate the mail without showing window print dialogue box.
	 *
	 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
	 *
	 * @param mixed $api_key api_id provided to any company admin
	 *
	 * @return null returns nothing but echo the whole mail content for an order for a company admin
	 *
	 */
	public function print_cp_mails_v_3($api_key = null, $font_size = 12, $today_or_all = 1, $order_id = 0 ){
	
	
		if($api_key && !$order_id){
	
			$result = $this->db->query("SELECT `company_id` FROM `api` WHERE `api_id` = '".$api_key."' LIMIT 1");
	
			//if result is found..
			if ($result->num_rows() > 0)
			{
				foreach ($result->result() as $row)
				{
	
					//fetching order if company id found..
					if($row->company_id > 0){
							
						$res_order = array();
							
						// Fetching ROLE of company
						$companyInfo = $this->db->query("SELECT `role` FROM `company` WHERE `id` = '".$row->company_id."'")->result();
						if(!empty($companyInfo)){
	
							// If ROLE is SUPER then
							if($companyInfo[0]->role == 'super'){
									
								// Fetching sub-shop's ID
								$subadminsIds = $this->db->query("SELECT `id` FROM `company` WHERE `parent_id` = '".$row->company_id."' AND `role` = 'sub'")->result_array();
								if(!empty($subadminsIds)){
	
									$subadminsId = implode(',', array_map(array("Welcome","get_id"), $subadminsIds));
	
									//foreach ($subadminsIds as $subadminsId){
									if($today_or_all == 2){
										//$res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
										$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `print_ready` = 1 AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
									}else{
										// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
										$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `print_ready` = 1 ORDER BY `id` DESC LIMIT 1")->result_array();
									}
	
								}
							}else{
								if($today_or_all == 2){
									$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `print_ready` = 1 AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
								}else{
									// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
									$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `print_ready` = 1 ORDER BY `id` DESC LIMIT 1")->result_array();
								}
							}
						}
							
							
						if (!empty($res_order))
						{
							// Sending order id as response
							echo $res_order['0']['id'];
							exit();
						}else{
							// IF NO RECORDS FOUND TO PRINT
							show_404();
							exit;
						}
					}else{
						// IF NO RECORDS FOUND TO PRINT
						show_404();
						exit;
					}
				}
			}else{
				// IF NO RECORDS FOUND TO PRINT
				show_404();
				exit;
			}
	
		}
		elseif ($api_key && $order_id && is_numeric($order_id)){
	
			$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `orders`.`id` = ".$order_id." LIMIT 1")->result_array();
	
			if (!empty($res_order)){
	
				$order_data = array();
				$order_details_data = array();
	
				foreach ($res_order as $order)
				{
					$order_data = $order;
	
					//if order id found
					if($order['id'] > 0){
							
						//fetching order details for this order id..
						$res_order_details = $this->db->query("SELECT * FROM `order_details`,`products` WHERE `orders_id` = '".$order['id']."' AND `order_details`.`products_id` = `products`.`id`");
						$query_log = $this->db->last_query();
	
						//if order details found for this order id..
						//if order found..
						if ($res_order_details->num_rows() > 0)
						{
							//$order_details_data = $res_order_details;
							foreach ($res_order_details->result_array() as $order_details)
							{
								//$res_disc = $this->db->query("SELECT * FROM `products_discount` WHERE `products_id` = '".$order_details['products_id']."'");
								$res_disc = $this->db->get_where('products_discount', array('products_id' => $order_details['products_id']), 1, 0);
								$res_disc_arr = $res_disc->result_array();
								if(isset($res_disc_arr[0])){
									$order_details['product_discount'] = $res_disc_arr[0]['discount_per_qty'];
								} else {
									$order_details['product_discount'] = 0;
								}
	
								$order_details_data[] = $order_details;
							}
						}
					}
	
					//preparing mail content..
					$order_id = $order['id'];
					$client_id = $order['clients_id'];
	
					if( !$order_id || !$client_id ){
						return false;
					}
	
					$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
					$this->db->join('country','country.id = clients.country_id','left');
					$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
					$company = $this->db->where( array('id'=>$order['company_id']) )->get( 'company' )->row();
	
					$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order['company_id']) )->get( 'client_numbers' )->result();
					$c_discount_number = '--';
					if(!empty($client_discount_number)){
						$c_discount_number = $client_discount_number['0']->discount_card_number;
					}
	
					$sub_admin = array();
					if($company->role == 'super'){
						$this->db->select("company_name,email_ads");
						$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
					}
	
					$this->db->select('emailid, subject_emails, orderreceived_msg, disable_price, activate_discount_card, disc_per_amount, disc_after_amount');
					$company_gs = $this->db->where( array('company_id'=>$order['company_id']) )->get( 'general_settings' )->result();
					if(!empty($company_gs))
						$company_gs = $company_gs['0'];
	
					if( !empty($client) && !empty($company) && !empty($company_gs) )
					{
						$company_email = $company_gs->emailid;
						$company_name = $company->company_name;
						if(!empty($sub_admin))
							$company_name = $sub_admin['company_name'];
	
						$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
						$orderreceived_msg = $company_gs->orderreceived_msg;
						$products_pirceshow_status = $company_gs->disable_price;
						$is_set_discount_card = $company_gs->activate_discount_card;
							
						$Options4 = '';
							
						if(!empty($sub_admin)){
							if($sub_admin['company_name'] == "1")
							{
								$email_messages = $this->db->get('email_messages')->row();
								$Options4 = $email_messages->emailads_text_message;
							}
						}else{
							if($company->email_ads == "1")
							{
								$email_messages = $this->db->get('email_messages')->row();
								$Options4 = $email_messages->emailads_text_message;
							}
						}
							
						// CARDGATE PAYMENT
						$order_data['billing_option'] = 'Cardgate';
						if($order_data['payment_via_paypal'] == 2){
							$this->db->select('billing_option');
							$transaction_info = $this->db->get_where('payment_transaction', array('order_id' => $order_data['temp_id']))->result();
							if(!empty($transaction_info))
								$order_data['billing_option'] = $transaction_info['0']->billing_option;
						}
	
	
						$mail_data['order_id'] = $order_id;
						$mail_data['order_data'] = $order_data;
						$mail_data['ip_address'] = $order_data['ip_address'];
						$mail_data['client'] = $client;
						$mail_data['company_name'] = $company_name;
						$mail_data['products_pirceshow_status'] = $products_pirceshow_status;
						$mail_data['order_details_data'] = $order_details_data;
						//$mail_data['disc_per_client_amount'] = $disc_per_client_amount;
						$mail_data['company_gs'] = $company_gs;
						$mail_data['c_discount_number'] = $c_discount_number;
						$mail_data['Options4'] = $Options4;
						$mail_data['orderreceived_msg'] = $orderreceived_msg;
						$mail_data['is_set_discount_card'] = $is_set_discount_card;
						$mail_data['font_size'] = $font_size;
						$mail_data['is_international'] = false;
						$mail_data['is_send_order_mail'] = true;
							
						//countries details for international delivery
						if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
							$mail_data['is_international'] = true;
						}
	
						//$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
						if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
							$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin_int', $mail_data, true );
						}
						else{
							$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
						}
	
						/*$insert_log = array(
								'order_id' => $order_id,
								'template' => htmlspecialchars($mail_body),
								'order_detail_query' => $query_log,
								'meta' => $api_key.'=='.$font_size.'=='.$today_or_all.'=='.$action,
								'order_details' => json_encode($order_details_data),
								'date' => date("Y-m-d H:i:s")
						);
							
						$this->db->insert('print_master_log', $insert_log);*/
	
						//$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order_id);
	
						//echo "succcess";
						header("Content-Type: text/html; charset=UTF-8"); // For showing dutch accent as it is..
						echo $mail_body;
					}
				}
			}else{
				// IF NO RECORDS FOUND TO PRINT
				show_404();
				exit;
			}
		}else{
			show_404();
			exit;
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */