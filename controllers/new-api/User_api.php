<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class User_api extends REST_Controller
{
	var $company_id;
	
	function __construct(){

		parent::__construct();
	}
	
	function verify_user_post()
	{	
		//$this->db->select( "clients.*, client_numbers.discount_card_number as dcn,client_numbers.newsletter,client_numbers.disc_per_client");   
	   	$this->db->where( 'clients.email_c', $this->input->post('obs_username') );
	   	$this->db->where( 'clients.password_c', $this->input->post('obs_password') );
	   	
	   	//$this->db->join( "client_numbers", "clients.id = client_numbers.client_id", "left" );
	   
	   	$client = $this->db->get('clients')->row();
	   	
	   	if( !empty($client) && isset($client->id) )
	   	{
	   		$this->db->where( 'client_numbers.company_id', $this->input->post('company_id') );
	   		$this->db->where( 'client_numbers.client_id', $client->id );
	   		$clientInfo = $this->db->get('client_numbers')->result_array();
	   		if(!empty($clientInfo)){
	   			$clientInfo = $clientInfo['0'];
	   			$client->dcn = $clientInfo['discount_card_number'];
	   			$client->newsletter = $clientInfo['newsletter'];
	   			$client->disc_per_client = $clientInfo['disc_per_client'];
	   		}
		  	$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $client ), 200 );
	   	}
	   	else
	   	{
	      	$this->response( array('error' => 1, 'message'=>_('Invalid username or password !'), 'data' => '' ), 404 );
	   	}
	   
	   	exit;
	}
	
	function get_user_post()
	{
	    $user_id = $this->input->post('user_id');
	   
	    $this->db->where( 'clients.id', $user_id);
	    $user = $this->db->get('clients')->row();
			
		if( !empty($user) )
		{
			$this->response( array('error' => 0, 'message'=> '' , 'data' => $user ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Error - No user details found !'), 'data' => '' ), 404 );
		}
	}	
	
	function verify_email_address_post()
	{
		$email_c = $this->input->post('email_c');
		
		$this->db->where( 'clients.email_c', $email_c);
	    $user = $this->db->get('clients')->row();
			
		if( !empty($user) )
		{
			$this->response( array('error' => 0, 'message'=> '' , 'data' => 'exists' ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Error - No user details found !'), 'data' => '' ), 404 );
		}
	}
	
	function register_user_post()
	{
		if(  $this->input->post('action') == 'register_user' )
		{
			$newsletters = $this->input->post('newsletters');
		
			$registration_array=array(
			                            'firstname_c' => $this->input->post('firstname_c'),
										'lastname_c' => $this->input->post('lastname_c'),
										'address_c' => $this->input->post('address_c'),
										'housenumber_c' => $this->input->post('housenumber_c'),
										'postcode_c' => $this->input->post('postcode_c'),
										'city_c' => $this->input->post('city_c'),
										'country_id' => $this->input->post('country_id'),
										'phone_c' => $this->input->post('phone_c'),
										'mobile_c' => $this->input->post('mobile_c'),
										'fax_c' => $this->input->post('fax_c'),
										'email_c' => $this->input->post('email_c'),
										'password_c' => $this->input->post('password_c'),
										'newsletters' => ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe',										
										'notifications' => $this->input->post('notifications'),
										'company_c' => $this->input->post('company_c'),
										'vat_c' => $this->input->post('vat_c'),										
										'company_id' => $this->input->post('company_id'),
										'created_c' => date('Y-m-d H:i:s',time()),
										'updated_c' => date('Y-m-d H:i:s',time()),
									);
			
			$this->db->insert('clients',$registration_array);
			$client_id = $this->db->insert_id();
			
			if($this->db->affected_rows())
			{

				$discount_card_array=array('discount_card_number' => $this->input->post('discount_card_number'),
						'client_id'=>$client_id,
						'newsletter' => ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe',
						'company_id' => $this->input->post('company_id'));
				$this->db->insert('client_numbers',$discount_card_array);
				
			    //Admin Details	
				$admin_info = $this->db->get("admin")->result_array();
				$admin_email = $admin_info['0']['email'];
				$admin_name = $admin_info['0']['admin_name'];
				
				//Company Details				
				$this->db->where(array('id'=>$this->input->post('company_id')));
			    $company = $this->db->get('company')->row();
				$company_email = $company->email;
			
				//Client Details				
				$this->db->where('email_c',$this->input->post('email_c'));
				$client = $this->db->get('clients')->row();
				$client_email = $client->email_c;
				
				if($this->input->post("language_id")){
					if($this->input->post("language_id") == 1)
						$this->lang->load('mail', 'english' );
					elseif($this->input->post("language_id") == 2)
						$this->lang->load('mail', 'dutch' );
					elseif($this->input->post("language_id") == 3)
						$this->lang->load('mail', 'french' );
				}else{
					$this->lang->load('mail', 'dutch' );
				}
				
				$this->load->library('utilities');
				$this->load->helper('phpmailer');
				
				$cln_mail_subject = $this->lang->line('mail_welcome_webshop').' '.$company->company_name;		  
				$cln_mail_template = '<p>'.$this->lang->line('mail_dear').' '.$client->firstname_c.',</p>
									  <p>'.$this->lang->line('mail_welcome_msg').'</p>
									  <p></p>
									  <p><strong>'.$this->lang->line('mail_login_detail').' : </strong></p>
									  <p><strong>-------------------------------------</strong></p>
									  <p><strong>'.$this->lang->line('mail_username').' : '.$client->email_c.'</strong></p>
									  <p><strong>'.$this->lang->line('mail_password').' : '.$client->password_c.'</strong></p>
									  <p><strong>-------------------------------------</strong></p>
									  <p></p>									
									  <p>'.$this->lang->line('mail_sincerely').',</p>
									  <p>'.$company->first_name.' '.$company->last_name.'</p>
									  <p>'.$company->company_name.'</p>
									  <p></p>
									  <p></p>									
									  <p><a href="http://www.onlinebestelsysteem.net" target="_blank">'.$this->lang->line('mail_powered_obs').'</a></p>';
									  				  		
				$mail_body = $this->utilities->parseMailText( $cln_mail_template, $client );
				/*if($_SERVER['REMOTE_ADDR'] == "122.161.99.84")
					send_email( "shyammishra@cedcoss.com", $this->config->item('site_admin_email'), $cln_mail_subject, $mail_body);
				else*/
				send_email( $client_email, $admin_email, $cln_mail_subject, $mail_body, $admin_name);
				
				$this->response( array('error' => 0, 'message'=> '', 'data' => $client ), 200 );
				
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be registered.'), 'data' => '' ), 404 );
			}
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be registered.'), 'data' => '' ), 404 );
		}
	}
	
	function forget_password_post()
	{
		$email_c = $this->input->post('email_c');
		
		$this->db->where( 'clients.email_c', $email_c);
	    $user = $this->db->get('clients')->row();
			
		if( !empty($user) )
		{
			$password_c = $this->generate_password( 8 );
			$user_id = $user->id;
			
			$this->db->where( 'email_c' , $email_c );
			$changed = $this->db->update('clients',array('password_c'=> $password_c ));
			
			if( $changed )
			{
				if($this->input->post("language_id")){
					if($this->input->post("language_id") == 1)
						$this->lang->load('mail', 'english' );
					elseif($this->input->post("language_id") == 2)
						$this->lang->load('mail', 'dutch' );
					elseif($this->input->post("language_id") == 3)
						$this->lang->load('mail', 'french' );
				}else{
					$this->lang->load('mail', 'dutch' );
				}
				
				//$this->load->library('utilities');
				$this->load->helper('phpmailer');
				
				// ---- >>> Sending Mail to Client <<< ---- //
				
				$mail_subject = 'OBS - '.$this->lang->line("mail_pswd_change");		
				$mail_body = '<p>'.$this->lang->line("mail_dear").' '.$user->firstname_c.',</p>
								  <p>'.$this->lang->line("mail_pswd_change_msg").'</p>
								  <p></p>
								  <p>'.$this->lang->line("mail_new_password").' : <strong>'.$password_c.'</strong></p>							  
								  <p></p>	
								  <p></p>							
								  <p>'.$this->lang->line("mail_powered_obs").' - <a href="http://www.onlinebestelsysteem.net">OnlineBestelSysteem</a></p>
								  <p>'.$this->config->item('site_admin_email').'</p>';
		
				//$mail_body = $this->utilities->parseMailText( $mail_template, $user );				
				$mail_sent = send_email( $email_c, $this->config->item('site_admin_email'), $mail_subject, $mail_body);
				
				if( $mail_sent )
				{
				    $this->response( array('error' => 0, 'message'=>_('Your password has been changed and sent to your email address successfully !') , 'data' => '' ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=>_('Your password has been set sucessfully, but can\'t send you mail. So please try again !'), 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Can\'t change your password !'), 'data' => '' ), 404 );
			}
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Incorrect email address entered !'), 'data' => '' ), 404 );
		}
	}
	
	function generate_password( $length = 20 )
	{
		  $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
		  $str = '';
		  $max = strlen($chars) - 1;
		
		  for ($i=0; $i < $length; $i++)
			$str .= $chars[rand(0, $max)];
		
		  return $str;
	}
	
	function change_password_post()
	{
		$client_id = $this->input->post('client_id');
		$password = $this->input->post('password');
		$new_password = $this->input->post('new_password');
		
		$this->db->where( 'id', $client_id );
		$this->db->where( 'password_c', $password );
		
	    $isChanged = $this->db->update('clients', array( 'password_c' => $new_password ) );	
				
		if( $this->db->affected_rows() != 0 )
		{
			$this->response( array('error' => 0, 'message'=>_('Password has been changed successfully !'), 'data' => '' ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Incorrect password entered !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function edit_user_profile_post()
	{
		$client_id = $this->input->post('client_id');
		$company_id=$this->input->post('company_id');
		
		if( !$client_id )
		{
			$this->response( array('error' => 1, 'message'=>_('Can\'t update your data !'), 'data' => '' ), 404 );
		}
		else
		{
			$newsletters = $this->input->post('newsletters');
			$notifications = $this->input->post('notifications');
			
			$update_array = array(
									'firstname_c' => $this->input->post('firstname_c'),
									'lastname_c' => $this->input->post('lastname_c'),
									'address_c' => $this->input->post('address_c'),
									'housenumber_c' => $this->input->post('housenumber_c'),
									'postcode_c' => $this->input->post('postcode_c'),
									'city_c' => $this->input->post('city_c'),
									'country_id' => $this->input->post('country_id'),
									'phone_c' => $this->input->post('phone_c'),
									'mobile_c' => $this->input->post('mobile_c'),
									'fax_c' => $this->input->post('fax_c'),
									'newsletters' => ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe',										
									'notifications' => ( $notifications == 'subscribe' )?'subscribe':'unsubscribe',		
									'company_c' => $this->input->post('company_c'),
									'vat_c' => $this->input->post('vat_c'),										
									'updated_c' => date('Y-m-d H:i:s',time())									
							   );
									   
			$this->db->where( 'id', $client_id );			
			$isChanged = $this->db->update('clients', $update_array );
			
			if( $this->db->affected_rows() != 0 )
			{
				$update_discount_array = array();
				$insert_array = array();
				if($this->input->post('discount_card_number')){
					$update_discount_array['discount_card_number'] = $this->input->post('discount_card_number');
					$insert_array['discount_card_number'] = $this->input->post('discount_card_number');
				}
				
				$update_discount_array['newsletter'] = ( ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe');
				
				$is_peresent = $this->db->get_where("client_numbers" , array("client_id" => $client_id, "company_id" => $company_id))->result_array();
				if(!empty($is_peresent)){
					$this->db->where( 'client_id', $client_id );
					$this->db->where( 'company_id', $company_id );
					$isChanged = $this->db->update('client_numbers', $update_discount_array );
				}else{
					$insert_array["client_id"] = $client_id;
					$insert_array["company_id"] = $company_id;
					$insert_array["newsletter"] = ( ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe');
					$this->db->insert("client_numbers" , $insert_array);
				}
				
				$this->response( array('error' => 0, 'message'=>_('Your profile has been updated successfully ! You may continue ...'), 'data' => '' ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Can\'t update your data !'), 'data' => $update_discount_array ), 404 );
			}	
	    }
		
		exit;
	}
	
	function get_client_orders_post()
	{
		$clients_id = $this->input->post('clients_id');
		$company_id = $this->input->post('company_id');
		$company_role = $this->input->post('company_role');
		
		if( !$clients_id || !$company_id )
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! Can\'t fetch your orders.'), 'data' => '' ), 404 );
		}
		else
		{			
			if( $company_role == 'master' )
			{
			    $this->db->select( '*, `orders`.id AS order_id, DATE_FORMAT( orders.created_date, "%e/%c/%Y") AS created_date', FALSE); 	
				$this->db->where( 'orders.clients_id', $clients_id );
				$this->db->where( 'orders.company_id', $company_id );
                $this->db->join( 'company', 'company.id = orders.company_id' );
                $this->db->order_by('orders.created_date','DESC');
				$orders = $this->db->get('orders')->result();
			}
			elseif( $company_role == 'super' )
			{
			    $orders = array();
				
				$this->db->select( 'company.id' );
				$this->db->where( 'company.parent_id', $company_id );
				$this->db->where( 'company.role', 'sub' );
				$sub_admins = $this->db->get('company')->result();
				
				if( !empty( $sub_admins ) )
				{
					foreach( $sub_admins as $sa )
					{
					    $sub_admin_id = $sa->id;
						
						$this->db->select( '*, `orders`.id AS order_id, DATE_FORMAT( orders.created_date, "%e/%c/%Y") AS created_date', FALSE); 						
						$this->db->where( 'orders.clients_id', $clients_id );
						$this->db->where( 'orders.company_id', $sub_admin_id );
						$this->db->join( 'company', 'company.id = orders.company_id' );
						$this->db->order_by('orders.created_date','DESC');
						$sub_orders = $this->db->get('orders')->result();
												
						$orders = array_merge( $orders, $sub_orders );
					}
				}
			}
			
			if( !empty( $orders ) )
			{
			    $this->response( array('error' => 0, 'message'=> '', 'data' => $orders ), 200 );
			}
			else
			{
			    $this->response( array('error' => 1, 'message'=>_('Sorry ! You haven\'t placed any order yet.'), 'data' => '' ), 404 );
			} 
		}
		
		exit;
	}
	
	function get_order_details_post()
	{
		$order_id = $this->input->post('order_id');
		
		$this->db->select( '*, DATE_FORMAT( created_date, "%e/%c/%Y") AS created_date, DATE_FORMAT( order_pickupdate, "%e/%c/%Y") AS order_pickupdate, DATE_FORMAT( delivery_date, "%e/%c/%Y") AS delivery_date', FALSE);	
		$this->db->where( 'orders.id', $order_id );
				
		$order = $this->db->get('orders')->row();
		
		if( !empty($order) )
		{
		    $this->db->select('area_name');
			$this->db->where( 'id', $order->delivery_area );
			$area_name = $this->db->get('delivery_areas')->row();	
			
			if( !empty( $area_name ) && isset( $area_name->area_name ) )	
			$order->area_name = $area_name->area_name;
			else
			$order->area_name = '';
			
			$this->db->select('city_name');
			$this->db->where( 'id', $order->delivery_city );
			$city_name = $this->db->get('delivery_settings')->row();
		   
		    if( !empty( $city_name ) && isset( $city_name->city_name ) )	
			$order->city_name = $city_name->city_name;
			else
			$order->city_name = '';
		   
		    $order_detail_arr = array();
			$order_detail_arr['order'] = $order;
			$order_detail_arr['order_prod'] = array();
			
			$this->db->select( '*, products.id AS product_id, products.discount AS discount_unit, order_details.id AS order_detail_id', FALSE); 
			$this->db->where( 'order_details.orders_id', $order_id );
			$this->db->join( 'products', 'products.id = order_details.products_id' );
		    $order_details = $this->db->get('order_details')->result();
			
			$order_detail_arr['order_prod'] = $order_details;
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $order_detail_arr ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Can\'t fetch this order detail.'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	function get_discount_data_post()
	{
		$clients_id = $this->input->post('clients_id');
		$company_id = $this->input->post('company_id');
		if($this->input->post())
		{
				$discount_details = array();
				//$this->db->select( 'discount_card_number' );
				$this->db->where( 'company_id', $company_id );
				$this->db->where( 'client_id', $clients_id );
				$discount_details = $this->db->get('client_numbers')->row_array();
				$fd = $this->db->last_query();
				if( !empty( $discount_details ) )
				{
					$this->response( array('error' => 0, 'message'=> '', 'data' => $discount_details ));
				}
				else
				{
					//$this->response( array('error' => 1, 'message'=>_('Sorry ! You haven\'t have any discount card number.'), 'data' => '' ), 404 );
					$this->response( array('error' => 1, 'message'=>$fd, 'data' => '' ), 404 );
				}
		
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! Can\'t fetch your dicount details.'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function verify_fb_user_post()
	{
		$this->db->where( 'clients.email_c', $this->input->post('email_c') );
		$this->db->where( 'clients.fb_id', $this->input->post('fb_id') );
	
		$client = $this->db->get('clients')->row();
	
		if( !empty($client) && isset($client->id) )
		{
			$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $client ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid username or password !'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	function register_fb_user_post()
	{
		$registration_array=array(
				'firstname_c' => $this->input->post('firstname_c'),
				'lastname_c' => $this->input->post('lastname_c'),
				'address_c' => $this->input->post('address_c'),
				'email_c' => $this->input->post('email_c'),
				'fb_id' => $this->input->post('fb_id'),
				'created_c' => date('Y-m-d H:i:s',time()),
				'updated_c' => date('Y-m-d H:i:s',time())
		);
		
		$this->db->insert('clients',$registration_array);
		$client_id = $this->db->insert_id();
	
		if($this->db->affected_rows())
		{

			$discount_card_array=array('discount_card_number' => '',
					'client_id'=>$client_id,
					'company_id' => $this->input->post('company_id'));
			$this->db->insert('client_numbers',$discount_card_array);
			
		    //Admin Details				
			$admin_email = $this->config->item('site_admin_email');
			
			//Company Details				
			$this->db->where(array('id'=>$this->input->post('company_id')));
		    $company = $this->db->get('company')->row();
			$company_email = $company->email;
		
			//Client Details				
			$this->db->where('email_c',$this->input->post('email_c'));
			$client = $this->db->get('clients')->row();
			$client_email = $client->email_c;
			
			
			if($this->input->post("language_id")){
				if($this->input->post("language_id") == 1)
					$this->lang->load('mail', 'english' );
				elseif($this->input->post("language_id") == 2)
				$this->lang->load('mail', 'dutch' );
				elseif($this->input->post("language_id") == 3)
				$this->lang->load('mail', 'french' );
			}else{
				$this->lang->load('mail', 'dutch' );
			}
			
			$this->load->library('utilities');
			$this->load->helper('phpmailer');
			
			$cln_mail_subject = $this->lang->line('mail_welcome_webshop').' '.$company->company_name;
			$cln_mail_template = '<p>'.$this->lang->line('mail_dear').' '.$client->firstname_c.',</p>
									  <p>'.$this->lang->line('mail_welcome_msg').'</p>
									  <p></p>
									  <p><strong>'.$this->lang->line('mail_login_detail').' : </strong></p>
									  <p><strong>-------------------------------------</strong></p>
									  <p><strong>'.$this->lang->line('mail_username').' : '.$client->email_c.'</strong></p>
									  <p><strong>'.$this->lang->line('mail_password').' : '.$client->password_c.'</strong></p>
									  <p><strong>-------------------------------------</strong></p>
									  <p></p>
									  <p>'.$this->lang->line('mail_sincerely').',</p>
									  <p>'.$company->first_name.' '.$company->last_name.'</p>
									  <p>'.$company->company_name.'</p>
									  <p></p>
									  <p></p>
									  <p><a href="http://www.onlinebestelsysteem.net" target="_blank">'.$this->lang->line('mail_powered_obs').'</a></p>';
								  				  		
			$mail_body = $this->utilities->parseMailText( $cln_mail_template, $client );
			/*if($_SERVER['REMOTE_ADDR'] == "122.161.99.84")
				send_email( "shyammishra@cedcoss.com", $this->config->item('site_admin_email'), $cln_mail_subject, $mail_body);
			else*/
			send_email( $client_email, $this->config->item('site_admin_email'), $cln_mail_subject, $mail_body);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $client ), 200 );
			
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be registered.'), 'data' => '' ), 404 );
		}
	}
	
	/**
	 * Function: to verify user from cookie values
	 */
	function verify_user_cookie_post(){
		$username = $this->input->post('obs_user_name');
		$userpass = $this->input->post('obs_user_pass');
		
		$userInfo = $this->db->get_where('clients',array('email_c' => $username))->result();
		
		if(!empty($userInfo)){
			if(md5($userInfo['0']->password_c) == $userpass){
				$client = $userInfo['0']; 
				$this->db->where( 'client_numbers.company_id', $this->input->post('company_id') );
		   		$this->db->where( 'client_numbers.client_id', $client->id );
		   		$clientInfo = $this->db->get('client_numbers')->result_array();
		   		if(!empty($clientInfo)){
		   			$clientInfo = $clientInfo['0'];
		   			$client->dcn = $clientInfo['discount_card_number'];
		   			$client->newsletter = $clientInfo['newsletter'];
		   			$client->disc_per_client = $clientInfo['disc_per_client'];
		   		}
			  	$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $client ), 200 );
		   	}
		   	else
		   	{
		      	$this->response( array('error' => 1, 'message'=>_('Invalid username or password !'), 'data' => '' ), 404 );
		   	}
		}else{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be found.'), 'data' => '' ), 404 );
		}
		exit;
	}
}
?>