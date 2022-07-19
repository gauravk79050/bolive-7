<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Obs_xmlss_api extends REST_Controller
{
	var $company_id;
	
	function __construct(){

		parent::__construct();
	}
	
	/*
	*function to verify company api request
	*/
	
	function verify_api_request_get()
	{
	    $this->load->library('api_verification');
		$this->load->library('user_agent');
			
		$verification_data = array( 
		                            'api_id' => $this->get('api_id'),
									'hash' => $this->get('hash'),
									'timestamp' => $this->get('timestamp'),
									'domain' => $this->agent->referrer()  //$_SESSION['HTTP_REFERER']
								  );
        
		$this->company_id = $this->api_verification->authenticate_login($verification_data);    
		
		if( !$this->company_id )
		{		
			return false;		
		}	
		else
		{						
			return true;
		}
	}
	
	function get_orders( $order_id = null )
	{
		//$api_verified = $this->verify_api_request_get();
		$api_verified = true;
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			if($order_id && is_numeric($order_id)){
				
			}else{
				$this->db->select('orders.* , client_numbers.client_number ,clients.firstname_c , clients.lastname_c ');
				$this->db->where( 'company_id' , 105 );
				$this->db->join( 'clients', 'clients.id = orders.client_id' );
				$this->db->join( 'client_numbers', 'clients.id = client_numbers.client_id' );
				$order_details_data = $this->db->get( 'orders' )->result_array();
				print_r($order_details_data); die();
			}
			
		}
		
		exit;
	}
	
	function submit_order_detail_post()
	{
		$api_verified = $this->verify_api_request_get();
		$inser_odet_arr = $this->input->post();

		$send_mail = $inser_odet_arr['send_mail'];
		
		$order_id = $inser_odet_arr['orders_id'];
		$client_id = $inser_odet_arr['client_id'];
		
		unset( $inser_odet_arr['send_mail'] );
		unset( $inser_odet_arr['client_id'] );
		
		$order_data = array();
		$order_details_data = array();
		
		if( isset($inser_odet_arr['order_data']) )
		{
		  //$order_data = $inser_odet_arr['order_data'];
		  unset( $inser_odet_arr['order_data'] );
		}
		  
		if( isset($inser_odet_arr['order_details_data']) )
		{
		  //$order_details_data = $inser_odet_arr['order_details_data'];
		  unset( $inser_odet_arr['order_details_data'] );
		}
		
		$this->db->insert( 'order_details' , $inser_odet_arr );	
	    $order_details_id = $this->db->insert_id();
		
		if( $order_details_id )
		{
		    if( $send_mail )
			{  			
				$order_data = $this->db->where( array('id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders' )->row_array();
				
				$this->db->where( array('orders_id'=>$order_id) );
				$this->db->join( 'products', 'products.id = order_details.products_id' );
				$order_details_data = $this->db->get( 'order_details' )->result_array();
				
				$this->send_order_details_post( $order_id, $client_id, $order_data, $order_details_data );
			}
			  
			$this->response( array('error' => 0, 'message'=> '', 'data' => $order_details_id ), 200 );
		}
		else
		{
		    $this->response( array('error' => 1, 'message'=> _('Some error ocured ! Can\'t place your order successfully.'), 'data' => '' ), 404 );
		}
				
		exit;
	}
	
	function associate_client_with_company( $client_id, $company_id )
	{
		if( !$client_id || !$company_id )
		   return false;
		   
		$this->db->where('id',$company_id);
		$company = $this->db->get('company')->row();
		$company_gs = $this->db->where( array('company_id'=>$company_id) )->get( 'general_settings' )->row();
		$clients_associated = $company->clients_associated;
		$company_email = $company->email;
		$new_client = 0;
		if($clients_associated)
		{
		   $clients_associated_arr = explode(',',$clients_associated);
		   
		   if(!empty($clients_associated_arr) && in_array($client_id,$clients_associated_arr))
		   {
			  // Already Associated
		   }
		   else
		   {
			  // Associate
		   	  $new_client = 1;
			  $clients_associated_arr[] = $client_id;
			  $clients_associated = implode(',',$clients_associated_arr);
			  
			  $this->db->where('id',$company_id);
			  $query_run = $this->db->update('company',array('clients_associated'=>$clients_associated));				  
		   }
		}
		else
		{ 
		   // Associate
		   $this->db->where('id',$company_id);
		   $query_run = $this->db->update('company',array('clients_associated'=>$client_id));	
		   $new_client = 1;
		}
		
		if($new_client){
				
			$client = $this->db->where( array('id'=>$client_id) )->get( 'clients' )->row();
			// ---- >>> Sending Mail to Company <<< ---- //
			$this->load->library('utilities');
			$this->load->helper('phpmailer');
		
			$cmp_mail_subject = 'OBS - '._('New Client Registered');
			$cmp_mail_template = '<p>'._('Dear').' '.$company->first_name.',</p>
								   <p>'._('A new client has registered via your online shop.').'</p>
								   <p></p>
								   <p><strong>'._('Client Details').' :</strong></p>
								   <p><strong>-------------------------</strong></p>
								   <p>'._('Name').' : '.$client->firstname_c.' '.$client->lastname_c.'</p>
								   <p>'._('Company').' : '.$client->company_c.'</p>
								   <p>'._('Address').' : '.$client->address_c.' '.$client->housenumber_c.', '.$client->postcode_c.' '.$client->city_c.'</p>
								   <p>'._('Telephone').' : '.$client->phone_c.'</p>
								   <p>'._('GSM').' : '.$client->mobile_c.'</p>
								   <p>'._('Email').' : '.$client->email_c.'</p>';
			
			if($company_gs->activate_discount_card != 0){
				$cmp_mail_template .= '<p>'._('Discount Card Number').' : '.(($client->discount_card_number)?$client->discount_card_number:'--').'</p>';
			}			
			
			$cmp_mail_template .= '<p></p>
								   <p></p>
								   <p>'._('Powered by OBS').' - <a href="http://www.onlinebestelsysteem.net/obs/cp">OnlineBestelSysteem</a></p>
								   <p>'.$this->config->item('site_admin_email').'</p>';
		
			$mail_body = $this->utilities->parseMailText( $cmp_mail_template, $client );
			//send_email( $company_email, $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
			send_email( "shyammishra@cedcoss.com", $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
			return true;
		}
		 
		return true;
	}
	
	function send_order_details_post( $order_id = 0, $client_id = 0, $order_data = array(), $order_details_data = array() )
	{
		if( !$order_id || !$client_id )
		  return false;
		
		$client = $this->db->where( array('id'=>$client_id) )->get( 'clients' )->row();
		$company = $this->db->where( array('id'=>$this->company_id) )->get( 'company' )->row();
		$company_gs = $this->db->where( array('company_id'=>$this->company_id) )->get( 'general_settings' )->row();
		
		
		$fp = fopen(APPPATH . "/controllers/new-api/logs.txt","a");
		
		fwrite($fp,json_encode(array('client'=>$client,'company'=>$company,'company_gs'=>$company_gs,'order_data'=>$order_data,'order_details_data'=>$order_details_data)));
		
		fclose($fp);
		
		if( !empty($client) && !empty($company) && !empty($company_gs) )
		{		  
			$this->load->library('utilities');
			$this->load->helper('phpmailer');
			
			$company_email = $company_gs->emailid;
			//$company_email = 'shyammishra@cedcoss.com';
			$company_name = $company->company_name;
			$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
			$orderreceived_msg = $company_gs->orderreceived_msg;
			$products_pirceshow_status = $company_gs->disable_price;
			$is_set_discount_card = $company_gs->activate_discount_card;
			/*$Tarief = '';
			$Totaal = '';
			
			if($products_pirceshow_status == '0'){
				$Tarief = '<td width="15%" align="left" bgcolor="#EAEAEA"><strong>Tarief</strong></td>';
				$Totaal = '<td width="8%" align="left" bgcolor="#EAEAEA"><strong>Totaal</strong></td>';
			}*/
			$Tarief = '<td width="15%" align="left" bgcolor="#EAEAEA"><strong>Tarief</strong></td>';
			$Totaal = '<td width="8%" align="left" bgcolor="#EAEAEA"><strong>Totaal</strong></td>';
			
			
			$Options = '';
			
			if( $order_data['option'] == '1' )
			{
			    $Options .= '<ul>';
				$Options .= '<li><b>'._('Pickup Date').' :</b> '.date('d/m/Y',strtotime($order_data['order_pickupdate'].' 00:00:00')).'</li>';
				$Options .= '<li><b>'._('Pickup Day').' :</b> '.$order_data['order_pickupday'].' '._('on').'</li>';
				$Options .= '<li><b>'._('Pickup Time').' :</b> '.$order_data['order_pickuptime'].' '._('hr').'</li>';
				$Options .= '<li><b>'._('Pickup Note').' :</b> '.$order_data['order_remarks'].'</li>';
				$Options .= '<li><b>'._('Shop').' :</b> '.$company_name.'</li>';
				$Options .= '</ul>';
			}
			elseif( $order_data['option'] == '2' )
			{
				$Options .= '<ul>';
				$Options .= '<li><b>'._('Delivery Date').' :</b> '.date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')).'</li>';
				$Options .= '<li><b>'._('Delivery Day').' :</b> '.$order_data['delivery_day'].' '._('on').' '.$order_data['delivery_hour'].':'.$order_data['delivery_minute']._('hr').'</li>';
				$Options .= '<li><b>'._('Delivery Address').' :</b> '.$order_data['delivery_streer_address'].'</li>';
				//$Options .= '<li><b>'._('Delivery Time').' :</b> '.$order_data['delivery_hour'].':'.$order_data['delivery_minute'].'</li>';
				$Options .= '<li><b>'._('Delivery Note').' :</b> '.$order_data['delivery_remarks'].'</li>';
				$Options .= '</ul>';
			}
			
			$Options2 = '';
			
			if( $order_data['option'] == '1' )
			{		
				$total_cost = $order_data['order_total'];
				
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right">'._('Order Total').'</td><td align="right">'.round($order_data['order_total'],2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right"><b>'._('Grand Total').'</b></td><td align="right">'.round($total_cost,2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'._('Discount Card Number').':</b>&nbsp;'.(($client->discount_card_number)?$client->discount_card_number:'--').'</td>';					
					$Options2 .= '</tr>';
				}			
			}
			elseif( $order_data['option'] == '2' )
			{
				$total_cost = (float)$order_data['delivery_cost']+(float)$order_data['order_total'];
						
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';				
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right">'._('Order Total').'</td><td align="right">'.round($order_data['order_total'],2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';				
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right">'._('Delivery Cost').'</td><td align="right">'.round($order_data['delivery_cost'],2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';				
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right"><b>'._('Grand Total').'</b></td><td align="right">'.round($total_cost,2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'._('Discount Card Number').':</b>&nbsp;'.(($client->discount_card_number)?$client->discount_card_number:'--').'</td>';					
					$Options2 .= '</tr>';
				}
			}
			
			$Options3 = _('Order Status').': <b>'._('IN PROCESSING').'</b><br />'._('We will send you an email as soon as possible, with more information.').'<br />';
			
			$Options4 = '';
			
			if($company->email_ads == "1")
			{	
				$email_messages = $this->db->get('email_messages')->row();
				$Options4 = $email_messages->emailads_text_message;
			}
						
			$MailBody = '';
			
			$shop_cart = $order_details_data;
			
			if( !empty($shop_cart) )
			{
			   foreach( $shop_cart as $item )
			   {					
					$MailBody .= '<tr>';
					$MailBody .= '<td valign="top" width="30%">'.$item['proname'].'</td>';
					$MailBody .= '<td valign="top" width="10%" align="center">'.($item['quantity']).' '.(($item['content_type']==1)?'Gm.':'').'</td>';
					if($products_pirceshow_status == '0' ){
						$unit = '';
						if($item['content_type'] == 0){
							$unit = '&euro;'; 
						}else if( $item['content_type'] == 2 ){
							$unit = '&euro;/Per p.';
						}else{
							$unit = '&euro;/Kg';
						}
						$MailBody .= '<td valign="top" width="15%" align="center">'.( ($item['content_type']==1)?(round($item['default_price'],2)):(round($item['default_price'],2)) ).'&nbsp;'.$unit.'</td>';
					}else{
						$MailBody .= '<td valign="top" width="15%" align="center">nvt.</td>';
					}				    
				    $MailBody .= '<td width="35%" align="left">';
					
					// TempExtracosts
					
					$rsExtracosts = explode("#",$item['add_costs']);
					for($j = 0; $j < count($rsExtracosts); $j++){
						$hold_arr = explode("_",$rsExtracosts[$j]);
						
						if( !empty( $hold_arr ) )
						{
							if( isset($hold_arr[0]) && isset($hold_arr[1]) && isset($hold_arr[2]) )
							{
								$MailBody .='<span style="color:red;">'.$hold_arr[0].': </span> '.$hold_arr[1].' = '.$hold_arr[2].'<br/>';
					        }
						}
					}
					
					$MailBody .='<br />';
					
					if($products_pirceshow_status == '0' ){
						if( $item['product_discount'] != '' || (float)$item['product_discount'] != 0 )
						{
							$MailBody .='<b>'._('Extra Discount').': </b>&nbsp;'.round((float)$item['discount'],2).'&euro;';
						}
					}					
					
					$MailBody .='<br />';
				
					if($item['pro_remark'])
					  $MailBody .='<b>'._('Remark').': </b> '.$item['pro_remark'];
					else
					  $MailBody .=' -- ';
			   
					$MailBody .='</td>';
					if($products_pirceshow_status == '0' ){
						$MailBody .='<td width="8%" valign="top" align="center">'.round($item['total'],2).'&euro;</td>';
					}else{
						$MailBody .='<td width="8%" valign="top" align="center">nvt.</td>';
					}
					
					$MailBody .='</tr>';						
			   }
			}
			
			//$getMail = file_get_contents( base_url().'assets/mail_templates/order_success_mail.html' );
			$getMail = file_get_contents( base_url().'assets/mail_templates/new_order_success_mail.html' );
			
			// Sending to Company
					
			$parse_email_array = array(
										"Message" => _('A new order has been placed on your shop.').'<br /><br />'._('Here are some details of it').' :<br /><br />',
										"Name"=>$client->firstname_c." ".$client->lastname_c,
										"order_no"=>$order_id,
										"date_created"=>date('d-m-Y',strtotime($order_data['created_date'])),
										"client_company"=>$client->company_c,
										"first_name"=>$client->firstname_c,
										"last_name"=>$client->lastname_c,
										"house_no"=>$client->housenumber_c,
										"address"=>$client->address_c,
										"city"=>$client->city_c,
										"postcode"=>$client->postcode_c,
										"country"=>'',//$client->country_name,
										"mobile"=>$client->mobile_c,
										"phone"=>$client->phone_c,
										"email"=>$client->email_c,
										"Tarief"=>$Tarief,
										"Totaal"=>$Totaal,
										"Options"=>$Options,
										"Options2"=>$Options2,
										"Options3"=>$Options3,
										"Options4"=>$Options4,
										"MailBody"=>$MailBody,
										"CompanyName"=>$company_name,
										"total"=>$order_data['order_total']
									  );
			
			$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );	
			$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
			
			//$flag = send_email( $company_email, $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name);
			$flag = send_email( "shyammishra@cedcoss.com", $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name);
			
		//----------------------------------------------------------------------------------------------------------------------------------//
			// Sending to Client

			if($order_data['lang_id'] == 1)
				$this->lang->load('mail', 'english' );
			elseif($order_data['lang_id'] == 2)
				$this->lang->load('mail', 'dutch' );
			elseif($order_data['lang_id'] == 3)
				$this->lang->load('mail', 'french' );
			
			$Tarief = '<td width="15%" align="left" bgcolor="#EAEAEA"><strong>'.$this->lang->line('mail_rate').'</strong></td>';
			$Totaal = '<td width="8%" align="left" bgcolor="#EAEAEA"><strong>'.$this->lang->line('mail_total').'</strong></td>';
				
				
			$Options = '';
				
			if( $order_data['option'] == '1' )
			{
				$Options .= '<ul>';
				$Options .= '<li><b>'.$this->lang->line('mail_pickup_date').' :</b> '.date('d/m/Y',strtotime($order_data['order_pickupdate'].' 00:00:00')).'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_pickup_day').' :</b> '.$order_data['order_pickupday'].' '.$this->lang->line('mail_day_on').'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_pickup_time').' :</b> '.$order_data['order_pickuptime'].' '.$this->lang->line('mail_time_hour').'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_pickup_note').' :</b> '.$order_data['order_remarks'].'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_shop').' :</b> '.$company_name.'</li>';
				$Options .= '</ul>';
			}
			elseif( $order_data['option'] == '2' )
			{
				$Options .= '<ul>';
				$Options .= '<li><b>'.$this->lang->line('mail_delivery_date').' :</b> '.date('d/m/Y',strtotime($order_data['delivery_date'].' 00:00:00')).'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_delivery_day').' :</b> '.$order_data['delivery_day'].' '.$this->lang->line('mail_day_on').' '.$order_data['delivery_hour'].':'.$order_data['delivery_minute'].$this->lang->line('mail_time_hour').'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_delivery_address').' :</b> '.$order_data['delivery_streer_address'].'</li>';
				//$Options .= '<li><b>'._('Delivery Time').' :</b> '.$order_data['delivery_hour'].':'.$order_data['delivery_minute'].'</li>';
				$Options .= '<li><b>'.$this->lang->line('mail_delivery_note').' :</b> '.$order_data['delivery_remarks'].'</li>';
				$Options .= '</ul>';
			}
				
			$Options2 = '';
				
			if( $order_data['option'] == '1' )
			{
				$total_cost = $order_data['order_total'];
			
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right">'.$this->lang->line('mail_order_total').'</td><td align="right">'.round($order_data['order_total'],2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
			
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right"><b>'.$this->lang->line('mail_grand_total').'</b></td><td align="right">'.round($total_cost,2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.(($client->discount_card_number)?$client->discount_card_number:'--').'</td>';
					$Options2 .= '</tr>';
				}
			}
			elseif( $order_data['option'] == '2' )
			{
				$total_cost = (float)$order_data['delivery_cost']+(float)$order_data['order_total'];
			
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right">'.$this->lang->line('mail_order_total').'</td><td align="right">'.round($order_data['order_total'],2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
			
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right">'.$this->lang->line('mail_delivery_cost').'</td><td align="right">'.round($order_data['delivery_cost'],2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
			
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right"><b>'.$this->lang->line('mail_grand_total').'</b></td><td align="right">'.round($total_cost,2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.(($client->discount_card_number)?$client->discount_card_number:'--').'</td>';
					$Options2 .= '</tr>';
				}
			}
				
			$Options3 = $this->lang->line('mail_order_status').': <b>'.$this->lang->line('mail_in_processing').'</b><br />'.$this->lang->line('mail_send_text').'<br />';
				
			$Options4 = '';
				
			if($company->email_ads == "1")
			{
				$email_messages = $this->db->get('email_messages')->row();
				$Options4 = $email_messages->emailads_text_message;
			}
			
			$MailBody = '';
				
			$shop_cart = $order_details_data;
				
			if( !empty($shop_cart) )
			{
				foreach( $shop_cart as $item )
				{
					$MailBody .= '<tr>';
					$MailBody .= '<td valign="top" width="30%">'.$item['proname'].'</td>';
					$MailBody .= '<td valign="top" width="10%" align="center">'.($item['quantity']).' '.(($item['content_type']==1)?'Gm.':'').'</td>';
					if($products_pirceshow_status == '0' ){
						$unit = '';
						if($item['content_type'] == 0){
							$unit = '&euro;';
						}else if( $item['content_type'] == 2 ){
							$unit = '&euro;/Per p.';
						}else{
							$unit = '&euro;/Kg';
						}
						$MailBody .= '<td valign="top" width="15%" align="center">'.( ($item['content_type']==1)?(round($item['default_price'],2)):(round($item['default_price'],2)) ).'&nbsp;'.$unit.'</td>';
					}else{
						$MailBody .= '<td valign="top" width="15%" align="center">nvt.</td>';
					}
					$MailBody .= '<td width="35%" align="left">';
						
					// TempExtracosts
						
					$rsExtracosts = explode("#",$item['add_costs']);
					for($j = 0; $j < count($rsExtracosts); $j++){
						$hold_arr = explode("_",$rsExtracosts[$j]);
			
						if( !empty( $hold_arr ) )
						{
							if( isset($hold_arr[0]) && isset($hold_arr[1]) && isset($hold_arr[2]) )
							{
								$MailBody .='<span style="color:red;">'.$hold_arr[0].': </span> '.$hold_arr[1].' = '.$hold_arr[2].'<br/>';
							}
						}
					}
						
					$MailBody .='<br />';
						
					if($products_pirceshow_status == '0' ){
						if( $item['product_discount'] != '' || (float)$item['product_discount'] != 0 )
						{
							$MailBody .='<b>'.$this->lang->line('mail_extra_discount').': </b>&nbsp;'.round((float)$item['discount'],2).'&euro;';
						}
					}
						
					$MailBody .='<br />';
			
					if($item['pro_remark'])
						$MailBody .='<b>'.$this->lang->line('mail_remark').': </b> '.$item['pro_remark'];
					else
						$MailBody .=' -- ';
			
					$MailBody .='</td>';
					if($products_pirceshow_status == '0' ){
						$MailBody .='<td width="8%" valign="top" align="center">'.round($item['total'],2).'&euro;</td>';
					}else{
						$MailBody .='<td width="8%" valign="top" align="center">nvt.</td>';
					}
						
					$MailBody .='</tr>';
				}
			}
			
			$parse_email_array = array(
										"Message"=>$orderreceived_msg,
										"Name"=>$client->firstname_c." ".$client->lastname_c,
										"order_no"=>$order_id,
										"date_created"=>date('d-m-Y',strtotime($order_data['created_date'])),
										"client_company"=>$client->company_c,
										"first_name"=>$client->firstname_c,
										"last_name"=>$client->lastname_c,
										"house_no"=>$client->housenumber_c,
										"address"=>$client->address_c,
										"city"=>$client->city_c,
										"postcode"=>$client->postcode_c,
										"country"=>'',//$client->country_name,
										"mobile"=>$client->mobile_c,
										"phone"=>$client->phone_c,
										"email"=>$client->email_c,
										"Tarief"=>$Tarief,
										"Totaal"=>$Totaal,
										"Options"=>$Options,
										"Options2"=>$Options2,										
										"Options3"=>$Options3,
										"Options4"=>$Options4,
										"MailBody"=>$MailBody,
										"CompanyName"=>$company_name,
										"total"=>$order_data['order_total']
									  );
			
			$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );	
			$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
			
			//$flag = send_email( $client->email_c, $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name );
			$flag = send_email( 'shyammishra@cedcoss.com', $company_email, $orderreceived_subject, $mail_body, $this->lang->line('mail_order').' '.$company_name );
			
			return true;
	    }
		else
		{
			$this->response( array('error' => 1, 'message'=> _('Can\'t send mail to company & clients !'), 'data' => $order_id ), 404 );
		}		
	}
}
?>