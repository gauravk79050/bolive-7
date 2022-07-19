<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Cart_api extends REST_Controller
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
	
	function submit_order_post()
	{
		$api_verified = 1;
		if(!$this->input->post('testdrive')){
			$api_verified = $this->verify_api_request_get();
		}

		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
		    // -- >> Submit Order
			
			$client_id = $this->input->post( 'client_id' );
			$email_c = $this->input->post( 'email_c' );
			$service_select = $this->input->post( 'service_select' );
			$total_cart_amount = $this->input->post( 'order_total' );
			$shop_url = $this->input->post( 'shop_url' );
			
			if( $client_id )
			{
				$post = $this->input->post();
				$service_select = $this->input->post( 'service_select' );
				
				if( $service_select == 'pickup' )
			    {
					$order_date = explode('/',$post['pickup_date']);
				    $order_date_db = date('Y-m-d',strtotime( $order_date[2].'-'.$order_date[1].'-'.$order_date[0].' 00:00:00' ));		   
				    $pickup_minute = $post['pickup_minute'];
				    //echo $pickup_minute;
				    //die(); 
				    if(strlen($pickup_minute) == 1){
				    	$pickup_minute = '0'.$pickup_minute;
				    }
				    $insert_arr = array(    'clients_id' => $client_id,
											'company_id' => $post['company_id'],
											'order_total' => $total_cart_amount,
											'order_status' => 'n',
											'order_remarks' => $post['pickup_remarks'],
											'order_pickupdate' => $order_date_db,
											'order_pickupday' => $post['pickup_day'],
											'order_pickuptime' => $post['pickup_hour'].':'.$pickup_minute,
											'ok_msg' => '0',
											'hold_msg' => '0',
											'completed' => '0',
											'option' => '1',
											'created_date' => date('Y-m-d H:i:s',time()),
											'payment_via_paypal' => ($post['payment_via']?'1':'0'),
											'shop_url' => $shop_url,
											'pic_apply_tax' => $post['pic_apply_tax'],
											'pic_tax_amount_added' => $post['pic_tax_amount_added'],
				    						'lang_id' => $post['language_id'],
								    		'disc_percent' => ( isset($post['disc_percent'])?$post['disc_percent']:0 ),
								    		'disc_price' => ( isset($post['disc_price'])?$post['disc_price']:0 ),
								    		'disc_amount' => ( isset($post['disc_amount'])?$post['disc_amount']:0 ),
								    		'disc_client' => ( isset($post['disc_client'])?$post['disc_client']:0 ),
								    		'disc_client_amount' => ( isset($post['disc_client_amount'])?$post['disc_client_amount']:0 )
									  );
										
					$order_data = $insert_arr;
				}
				elseif( $service_select == 'delivery' )
			    {
					$order_date = explode('/',$post['delivery_date']);
					$order_date_db = date('Y-m-d',strtotime( $order_date[2].'-'.$order_date[1].'-'.$order_date[0].' 00:00:00' ));
					$delivery_minute = $post['delivery_minute'];
					//echo $pickup_minute;
					//die();
					if(strlen($delivery_minute) == 1){
						$delivery_minute = '0'.$delivery_minute;
					}
					
					$insert_arr = array(    'clients_id' => $client_id,
											'company_id' => $post['company_id'],
											'order_total' => $total_cart_amount,
											'order_status' => 'n',
											'delivery_streer_address' => $post['delivery_streer_address'],
											'delivery_area'=> $post['delivery_area'],
											'delivery_city'=> $post['delivery_city'],
											'delivery_zip' => $post['delivery_zip'],
											'delivery_day' => $post['delivery_day'],
											'delivery_hour' => $post['delivery_hour'],
											'delivery_minute' => $delivery_minute,
											'delivery_date' => $order_date_db,
											'delivery_remarks' => $post['delivery_remarks'],
											'delivery_cost' => $post['rsDELPRICE'],
											'ok_msg' => '0',
											'hold_msg' => '0',
											'completed' => '0',
											'option' => '2',
											'created_date' => date('Y-m-d H:i:s',time()),
											'payment_via_paypal' => ($post['payment_via']?'1':'0'),
											'shop_url' => $shop_url,
											'del_apply_tax' => $post['del_apply_tax'],
											'del_tax_amount_added' => $post['del_tax_amount_added'],
											'lang_id' => $post['language_id'],
								    		'disc_percent' => ( isset($post['disc_percent'])?$post['disc_percent']:0 ),
								    		'disc_price' => ( isset($post['disc_price'])?$post['disc_price']:0 ),
								    		'disc_amount' => ( isset($post['disc_amount'])?$post['disc_amount']:0 ),
								    		'disc_client' => ( isset($post['disc_client'])?$post['disc_client']:0 ),
								    		'disc_client_amount' => ( isset($post['disc_client_amount'])?$post['disc_client_amount']:0 )
										);
																		
					$order_data = $insert_arr;	
				}
					
				if(!empty( $insert_arr ))
			    {					
					$this->db->insert( 'orders' , $insert_arr );	
	                $order_id = $this->db->insert_id();
					
					// ---- >> Associate Client
					
					$this->associate_client_with_company( $client_id, $this->company_id );
					
					$this->response( array('error' => 0, 'message'=> '', 'data' => $order_id ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=> _('Can\'t submit your order !'), 'data' => '' ), 404 );
				}		
			}
			else
			{
				$this->response( array('error' => 1, 'message'=> _('Couldn\'t get the client ID !'), 'data' => '' ), 404 );
			}
		}
		
		exit;
	}
	
	function submit_order_detail_post()
	{
		
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
								   <p>'._('I want a invoice').' : '.( ($client->notifications == 'subscribe')?_("Yes"):_("No") ).'</p>
								   <p>'._('VAT').' : '.$client->vat_c.'</p>
								   <p>'._('Address').' : '.$client->address_c.' '.$client->housenumber_c.', '.$client->postcode_c.' '.$client->city_c.'</p>
								   <p>'._('Telephone').' : '.$client->phone_c.'</p>
								   <p>'._('GSM').' : '.$client->mobile_c.'</p>
								   <p>'._('Email').' : '.$client->email_c.'</p>';
			
			if($company_gs->activate_discount_card != 0){
				$discount_card_info = $this->db->get_where("client_numbers", array("client_id" => $client_id))->result_array();
				$discount_num = '--';
				if(!empty($discount_card_info)){
					if($discount_card_info['0']['discount_card_number'] && $discount_card_info['0']['discount_card_number'] != 0)
						$discount_num = $discount_card_info['0']['discount_card_number']; 
				}
				$cmp_mail_template .= '<p>'._('Discount Card Number').' : '.$discount_num.'</p>';
			}			
			
			$cmp_mail_template .= '<p></p>
								   <p></p>
								   <p>'._('Powered by OBS').' - <a href="http://www.onlinebestelsysteem.net/obs/cp">OnlineBestelSysteem</a></p>
								   <p>'.$this->config->item('site_admin_email').'</p>';
		
			$mail_body = $this->utilities->parseMailText( $cmp_mail_template, $client );
			send_email( $company_email, $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
			//send_email( "shyammishra@cedcoss.com", $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
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
		
		$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $this->company_id) )->get( 'client_numbers' )->result();
		$c_discount_number = '--';
		if(!empty($client_discount_number)){
			$c_discount_number = $client_discount_number['0']->discount_card_number;
		}
		
		$sub_admin = array();
		if($company->role == 'super'){
			$this->db->select("company_name,email_ads");
			$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
		}
		
		$company_gs = $this->db->where( array('company_id'=>$this->company_id) )->get( 'general_settings' )->result();
		if(!empty($company_gs))
			$company_gs = $company_gs['0'];
		
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
			if(!empty($sub_admin))
				$company_name = $sub_admin['company_name'];
			
			$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
			$orderreceived_msg = $company_gs->orderreceived_msg;
			$products_pirceshow_status = $company_gs->disable_price;
			$is_set_discount_card = $company_gs->activate_discount_card;

			/**
			 * Discount per Client
			 */
			$disc_per_client_amount = 0;
			$disc_per_client_html = '';
			if($order_data['disc_client_amount'] > 0){
				$disc_per_client_amount = $order_data['disc_client_amount'];
				$disc_per_client_html .= '<tr>';
				$disc_per_client_html .= '<td>&nbsp;</td>';
				$disc_per_client_html .= '<td>&nbsp;</td>';
				$disc_per_client_html .= '<td>&nbsp;</td>';
				$disc_per_client_html .= '<td align="right">'._('Loyalty discount').'('.$order_data['disc_client'].'%)</td><td align="right">-'.round($order_data['disc_client_amount'],2).'&euro;</td>';
				$disc_per_client_html .= '</tr>';
			}
			/* --------------------------------------------------------------------------------------------- */
			
			/**
			 * Discount per Amount
			 */
			$disc_per_amount = 0;
			$disc_per_amount_html = '';
			if($company_gs->disc_per_amount == 1 && $company_gs->disc_after_amount > 0  && $order_data['disc_amount'] > 0 && ($order_data['disc_percent'] > 0 || $order_data['disc_price'] > 0)){
				$disc_per_amount = $order_data['disc_amount'];
				$disc_per_amount_html .= '<tr>';
				$disc_per_amount_html .= '<td>&nbsp;</td>';
				$disc_per_amount_html .= '<td>&nbsp;</td>';
				$disc_per_amount_html .= '<td>&nbsp;</td>';
				if($order_data['disc_percent'] > 0){
					$disc_per_amount_html .= '<td align="right">'._('discount').'('.$order_data['disc_percent'].'%)</td><td align="right">-'.round($disc_per_amount,2).'&euro;</td>';
				}else{
					$disc_per_amount_html .= '<td align="right">'._('discount').'('.$order_data['disc_price'].'&euro;)</td><td align="right">-'.round($disc_per_amount,2).'&euro;</td>';
				}
				
				$disc_per_amount_html .= '</tr>';
			} 
			/* --------------------------------------------------------------------------------------------- */
			
			/*$Tarief = '';
			$Totaal = '';
			
			if($products_pirceshow_status == '0'){
				$Tarief = '<td width="15%" align="left" bgcolor="#EAEAEA"><strong>Tarief</strong></td>';
				$Totaal = '<td width="8%" align="left" bgcolor="#EAEAEA"><strong>Totaal</strong></td>';
			}*/
			$Tarief = '<td width="15%" align="center" bgcolor="#EAEAEA"><strong>Tarief</strong></td>';
			$Totaal = '<td width="8%" align="right" bgcolor="#EAEAEA"><strong>Totaal</strong></td>';
			
			
			$Options = '';
			
			if( $order_data['option'] == '1' )
			{
			    $Options .= '<ul>';
				$Options .= '<li><b>'._('Pickup Date').' :</b> '.$order_data['order_pickupday'].' '.date('d/m/Y',strtotime($order_data['order_pickupdate'].' 00:00:00')).' '._('on').' '.$order_data['order_pickuptime'].' '._('hr').'</li>';
				
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

				if($disc_per_client_amount > 0 && $products_pirceshow_status == '0' ){
					$Options2 .= $disc_per_client_html;
				}
				
				if($company_gs->disc_per_amount == 1 && $disc_per_amount > 0 && $products_pirceshow_status == '0'){
					$Options2 .= $disc_per_amount_html;
				}
				
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right"><b>'._('Grand Total').'</b></td><td align="right">'.round(($total_cost- ($disc_per_amount+$disc_per_client_amount) ),2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'._('Discount Card Number').':</b>&nbsp;'.$c_discount_number.'</td>';					
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

				if($disc_per_client_amount > 0 && $products_pirceshow_status == '0' ){
					$Options2 .= $disc_per_client_html;
				}
				
				if($company_gs->disc_per_amount == 1 && $disc_per_amount > 0 && $products_pirceshow_status == '0'){
					$Options2 .= $disc_per_amount_html;
				}
				
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
					$Options2 .= '<td align="right"><b>'._('Grand Total').'</b></td><td align="right">'.round(($total_cost - ($disc_per_amount+$disc_per_client_amount)) ,2).'&euro;</td>';
				}				
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'._('Discount Card Number').':</b>&nbsp;'.$c_discount_number.'</td>';					
					$Options2 .= '</tr>';
				}
			}
			
			$Options3 = _('Order Status').': <b>'._('IN PROCESSING').'</b><br />'._('We will send you an email as soon as possible, with more information.').'<br />';
			
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
						
			$MailBody = '';
			
			$shop_cart = $order_details_data;
			
			if( !empty($shop_cart) )
			{
			   foreach( $shop_cart as $item )
			   {					
					$MailBody .= '<tr >';
					$MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="30%">'.$item['proname'];
					/*if($item['image'] != ''){
						$MailBody .= '<img src='.base_url().'assets/cp/images/image-x-photo-cd.png" width="15" height="15" />';
					}*/
					$MailBody .= '</td>';
					$MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="10%" align="center">'.ltrim($item['quantity'],"0").' '.(($item['content_type']==1)?'gr.':'').'</td>';
					if($products_pirceshow_status == '0' ){
						$unit = '';
						$o_price = round($item['price_per_unit'],2);
						if($item['content_type'] == 0){
							$unit = ' &euro;'; 
						}else if( $item['content_type'] == 2 ){
							$o_price = round($item['price_per_person'],2);
							$unit = ' &euro;/Per p.';
						}else{
							$unit = ' &euro;/kg';
							$o_price = round( ( $item['price_weight'] * 1000 ),2);
						}	
						//$MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="15%" align="center">'.( ($item['content_type']==1)?(round($item['default_price'],2)):(round($item['default_price'],2)) ).'&nbsp;'.$unit.'</td>';
						$MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="15%" align="center">'.$o_price.'&nbsp;'.$unit.'</td>';
					}else{
						$MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="15%" align="center">nvt.</td>';
					}				    
				    $MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="35%" align="left">';
					
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
						//$MailBody .='<br />';
					}
					
					
					
					if($products_pirceshow_status == '0' ){
						if( $item['product_discount'] != '' || (float)$item['product_discount'] != 0 )
						{
							$MailBody .='<b>'._('Extra Discount').': </b>&nbsp;'.round((float)$item['discount'],2).'&euro;';
						}
						else{}
					//	$MailBody .='<br />';
					}					
					
					
				
					if($item['pro_remark'])
					  $MailBody .='<b>'._('Remark').': </b> '.$item['pro_remark'];
					else
					  $MailBody .=' -- ';
			   
					$MailBody .='</td>';
					if($products_pirceshow_status == '0' ){
						$MailBody .='<td width="8%" valign="top" style="border-bottom:1px solid #ccc; padding:5px 0;" align="right">'.round($item['total'],2).'&euro;</td>';
					}else{
						$MailBody .='<td width="8%" valign="top" style="border-bottom:1px solid #ccc; padding:5px 0;"align="right">nvt.</td>';
					}
					
					$MailBody .='</tr >';	
									
			   }
			}
			
			$invoice_and_vat = '';
			if($client->notifications == "subscribe"){
				$invoice_and_vat .= _("I want an invoice")." : "._("Yes")."<br/>";
				$invoice_and_vat .= _("VAT")." : ".$client->vat_c."<br/>";
			}
			
			//$getMail = file_get_contents( base_url().'assets/mail_templates/order_success_mail.html' );
			$getMail = file_get_contents( base_url().'assets/mail_templates/new_order_success_mail.html' );
			
			// Sending to Company
					
			$parse_email_array = array(
										"order_no_text" => _("Order No."),
										"date_created_text" => _("Order Date"),
										"customer_data_text" => _("Customer Data"),
										"mobile_text" => _("Mobile"),
										"phone_text" => _("Phone"),
										"email_text" => _("Email"),
										"product_text" => _("Product"),
										"qauntity_text" => _("Quantity"),
										"extra_text" => _("Extra"),
										"regard_text" => _("Regards"),
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
										"total"=>$order_data['order_total'],
										"invoice_and_vat" => $invoice_and_vat
									  );
			
			$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );	
			$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
			
			$flag = send_email( $company_email, $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name);
			//$flag = send_email( "shyammishra@cedcoss.com", $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name);
			
		//----------------------------------------------------------------------------------------------------------------------------------//
			// Sending to Client

			if($order_data['lang_id'] == 1)
				$this->lang->load('mail', 'english' );
			elseif($order_data['lang_id'] == 2)
				$this->lang->load('mail', 'dutch' );
			elseif($order_data['lang_id'] == 3)
				$this->lang->load('mail', 'french' );
			
			$Tarief = '<td width="15%" align="center" bgcolor="#EAEAEA"><strong>'.$this->lang->line('mail_rate').'</strong></td>';
			$Totaal = '<td width="8%" align="right" bgcolor="#EAEAEA"><strong>'.$this->lang->line('mail_total').'</strong></td>';
				
				
			/**
			 * Discount per Client
			 */
			$disc_per_client_amount = 0;
			$disc_per_client_html = '';
			if($order_data['disc_client_amount'] > 0){
				$disc_per_client_amount = $order_data['disc_client_amount'];
				$disc_per_client_html .= '<tr>';
				$disc_per_client_html .= '<td>&nbsp;</td>';
				$disc_per_client_html .= '<td>&nbsp;</td>';
				$disc_per_client_html .= '<td>&nbsp;</td>';
				$disc_per_client_html .= '<td align="right">'.$this->lang->line('loyalty_discount').'('.$order_data['disc_client'].'%)</td><td align="right">-'.round($order_data['disc_client_amount'],2).'&euro;</td>';
				$disc_per_client_html .= '</tr>';
			}
			/* --------------------------------------------------------------------------------------------- */
				
			/**
			 * Discount per Amount
			 */
			$disc_per_amount = 0;
			$disc_per_amount_html = '';
			if($company_gs->disc_per_amount == 1 && $company_gs->disc_after_amount > 0  && $order_data['disc_amount'] > 0 && ($order_data['disc_percent'] > 0 || $order_data['disc_price'] > 0)){
				$disc_per_amount = $order_data['disc_amount'];
				$disc_per_amount_html .= '<tr>';
				$disc_per_amount_html .= '<td>&nbsp;</td>';
				$disc_per_amount_html .= '<td>&nbsp;</td>';
				$disc_per_amount_html .= '<td>&nbsp;</td>';
				if($order_data['disc_percent'] > 0){
					$disc_per_amount_html .= '<td align="right">'.$this->lang->line('discount_txt').'('.$order_data['disc_percent'].'%)</td><td align="right">-'.round($disc_per_amount,2).'&euro;</td>';
				}else{
					$disc_per_amount_html .= '<td align="right">'.$this->lang->line('discount_txt').'('.$order_data['disc_price'].'&euro;)</td><td align="right">-'.round($disc_per_amount,2).'&euro;</td>';
				}
			
				$disc_per_amount_html .= '</tr>';
			}
			/* --------------------------------------------------------------------------------------------- */
			
			$Options = '';
				
			/* Edited CARL */	
			if( $order_data['option'] == '1' )
			{
				$Options .= '<ul>';
				$Options .= '<li><b>'.$this->lang->line('mail_pickup_date').' :</b> '.$order_data['order_pickupday'].' '.date('d/m/Y',strtotime($order_data['order_pickupdate'].' 00:00:00')).' '.$this->lang->line('mail_day_on').' '.$order_data['order_pickuptime'].' '.$this->lang->line('mail_time_hour').'</li>';
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
			
				if($disc_per_client_amount > 0 && $products_pirceshow_status == '0' ){
					$Options2 .= $disc_per_client_html;
				}
				
				if($company_gs->disc_per_amount == 1 && $disc_per_amount > 0 && $products_pirceshow_status == '0'){
					$Options2 .= $disc_per_amount_html;
				}
				
				$Options2 .= '<tr>';
				$Options2 .= '<td>&nbsp;</td>';
				$Options2 .= '<td>&nbsp;</td>';
				if($products_pirceshow_status == '0' ){
					$Options2 .= '<td>&nbsp;</td>';
					$Options2 .= '<td align="right"><b>'.$this->lang->line('mail_grand_total').'</b></td><td align="right">'.round(($total_cost- ($disc_per_amount + $disc_per_client_amount) ),2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.$c_discount_number.'</td>';
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
				
				if($disc_per_client_amount > 0 && $products_pirceshow_status == '0' ){
					$Options2 .= $disc_per_client_html;
				}
				
				if($company_gs->disc_per_amount == 1 && $disc_per_amount > 0 && $products_pirceshow_status == '0'){
					$Options2 .= $disc_per_amount_html;
				}
				
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
					$Options2 .= '<td align="right"><b>'.$this->lang->line('mail_grand_total').'</b></td><td align="right">'.round(($total_cost-($disc_per_amount+$disc_per_client_amount)),2).'&euro;</td>';
				}
				$Options2 .= '</tr>';
				if($is_set_discount_card != 0){
					$Options2 .= '<tr>';
					$Options2 .= '<td colspan=4 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.$c_discount_number.'</td>';
					$Options2 .= '</tr>';
				}
			}
				
			$Options3 = $this->lang->line('mail_order_status').': <b>'.$this->lang->line('mail_in_processing').'</b><br />'.$this->lang->line('mail_send_text').'<br />';
				
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
			
			$MailBody = '';
				
			$shop_cart = $order_details_data;
				
			if( !empty($shop_cart) )
			{
				foreach( $shop_cart as $item )
				{
					$MailBody .= '<tr>';
					$MailBody .= '<td valign="top" width="30%" style="border-bottom:1px solid #ccc; padding:5px 0;">'.$item['proname'].'</td>';
					$MailBody .= '<td valign="top" width="10%" align="center" style="border-bottom:1px solid #ccc; padding:5px 0;">'.ltrim($item['quantity'],"0").' '.(($item['content_type']==1)?'gr.':'').'</td>';
					if($products_pirceshow_status == '0' ){
						$unit = '';
						$o_price = round($item['price_per_unit'],2);
						if($item['content_type'] == 0){
							$unit = ' &euro;'; 
						}else if( $item['content_type'] == 2 ){
							$o_price = round($item['price_per_person'],2);
							$unit = ' &euro;/Per p.';
						}else{
							$unit = ' &euro;/kg';
							$o_price = round( ( $item['price_weight'] * 1000 ),2);
						}	
						//$MailBody .= '<td valign="top" width="15%" align="center" style="border-bottom:1px solid #ccc; padding:5px 0;">'.( ($item['content_type']==1)?(round($item['default_price'],2)):(round($item['default_price'],2)) ).'&nbsp;'.$unit.'</td>';
						$MailBody .= '<td style="border-bottom:1px solid #ccc; padding:5px 0;"  valign="top" width="15%" align="center">'.$o_price.'&nbsp;'.$unit.'</td>';
					}else{
						$MailBody .= '<td valign="top" width="15%" align="center" style="border-bottom:1px solid #ccc; padding:5px 0;">nvt.</td>';
					}
					$MailBody .= '<td width="35%" align="left" style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top">';
						
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
						//$MailBody .='<br />';
					}
						
					
						
					if($products_pirceshow_status == '0' ){
						if( $item['product_discount'] != '' || (float)$item['product_discount'] != 0 )
						{
							$MailBody .='<b>'.$this->lang->line('mail_extra_discount').': </b>&nbsp;'.round((float)$item['discount'],2).'&euro;';
						}
						else{}
						//$MailBody .='<br />';
					}
						
					
			
					if($item['pro_remark'])
						$MailBody .='<b>'.$this->lang->line('mail_remark').': </b> '.$item['pro_remark'];
					else
						$MailBody .=' -- ';
			
					$MailBody .='</td>';
					if($products_pirceshow_status == '0' ){
						$MailBody .='<td width="8%" valign="top" align="right" style="border-bottom:1px solid #ccc; padding:5px 0;">'.round($item['total'],2).'&euro;</td>';
					}else{
						$MailBody .='<td width="8%" valign="top" align="right" style="border-bottom:1px solid #ccc; padding:5px 0;">nvt.</td>';
					}
						
					$MailBody .='</tr>';
				}
			}
			
			$invoice_and_vat = '';
			if($client->notifications == "subscribe"){
				$invoice_and_vat .= _("I want an invoice")." : "._("Yes")."<br/>";
				$invoice_and_vat .= _("VAT")." : ".$client->vat_c."<br/>";
			}
			
			$parse_email_array = array(
										"order_no_text" => $this->lang->line('mail_order_no'),
										"date_created_text" => $this->lang->line('mail_order_date'),
										"customer_data_text" => $this->lang->line('mail_customer_data'),
										"mobile_text" => $this->lang->line('mail_mobile'),
										"phone_text" => $this->lang->line('mail_phone'),
										"email_text" => $this->lang->line('mail_email'),
										"product_text" => $this->lang->line('mail_product'),
										"qauntity_text" => $this->lang->line('mail_quantity'),
										"extra_text" => $this->lang->line('mail_extra'),
										"regard_text" => $this->lang->line('mail_regards'),
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
										"total"=>$order_data['order_total'],
										"invoice_and_vat" => $invoice_and_vat
									  );
			
			$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );	
			$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
			
			$flag = send_email( $client->email_c, $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name );
			//$flag = send_email( 'shyammishra@cedcoss.com', $company_email, $orderreceived_subject, $mail_body, $this->lang->line('mail_order').' '.$company_name );
			
			return true;
	    }
		else
		{
			$this->response( array('error' => 1, 'message'=> _('Can\'t send mail to company & clients !'), 'data' => $order_id ), 404 );
		}		
	}
	
	function save_order_post()
	{
		
		$api_verified = 1;
		if(!$this->input->post('testdrive')){
			$api_verified = $this->verify_api_request_get();
		}
	
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			// -- >> Save Order
				
			$client_id = $this->input->post( 'client_id' );
			$email_c = $this->input->post( 'email_c' );
			$total_cart_amount = $this->input->post( 'cart_total' );
			$cart = $this->input->post( 'cart' );
			$shop_url = $this->input->post( 'shop_url' );
			$company_id = $this->input->post( 'company_id' );
			$client_ip = $this->input->post( 'client_ip' );
			if( $client_id )
			{
				$insert_arr = array(    
						'client_id' => $client_id,
						'company_id' => $company_id,
						'cart' => json_encode($cart),
						'cart_total' => $total_cart_amount,
						'client_email' => $email_c,
						'shop_url' => $shop_url,
						'client_ip' => $client_ip,
						'created_date' => date('Y-m-d H:i:s')
				);

				if(!empty( $insert_arr ))
				{
					$this->db->insert( 'saved_orders' , $insert_arr );
						
					$this->response( array('error' => 0, 'message'=> '', 'data' => '' ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=> _('Can\'t submit your order !'), 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=> _('Couldn\'t get the client ID !'), 'data' => '' ), 404 );
			}
		}
	
		exit;
	}
	
	function get_saved_order_post()
	{
		$api_verified = 1;
		if(!$this->input->post('testdrive')){
			$api_verified = $this->verify_api_request_get();
		}
		
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			// -- >> Save Order
	
			$client_id = $this->input->post( 'client_id' );
			$company_id = $this->input->post( 'company_id' );
			
			if( $client_id )
			{
				$current_time = date("Y-m-d H:i:s");
				$time_before_two_hours = date("Y-m-d H:i:s" , ( strtotime($current_time) - 60 * 60 * 2 ));
				$time_before_two_hours_two_minute = date("Y-m-d H:i:s" , ( strtotime($time_before_two_hours) - 60 * 2 ));
				
				// Getting saved carts since 2 hours and 2 mins
				$this->db->where( "client_id" , $client_id );
				$this->db->where( "company_id" , $company_id );
				$this->db->where( "created_date >=" , $time_before_two_hours_two_minute );
				$saved_carts = $this->db->get("saved_orders")->result_array();
				
				// Deleting saved cart after 2 hours and 2 mins
				$this->db->where( "client_id" , $client_id );
				$this->db->where( "company_id" , $company_id );
				$this->db->where( "created_date <" , $time_before_two_hours_two_minute );
				$this->db->delete("saved_orders");
				
				if(!empty( $saved_carts ))
				{
					$this->response( array('error' => 0, 'message'=> '', 'data' => $saved_carts ), 200 );
					//$this->response( array('error' => 0, 'message'=> '', 'data' => 'hello cheking' ), 200 );
				}
				else
				{
					//$this->response( array('error' => 1, 'message'=> _('Don\'t have any saved cart!'), 'data' => '' ), 404 );
					$this->response( array('error' => 1, 'message'=> _('Don\'t have any saved cart!'), 'data' => '' ), 404 );
					//$this->response( array('error' => 1, 'message'=> $current_time."--".$time_before_two_hours_two_minute, 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=> _('Couldn\'t get the client ID !'), 'data' => '' ), 404 );
			}
		}
	
		exit;
	}
	
}
?>