<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cardgate extends CI_Controller {
	 
	var $template = NULL;
	
	function __construct()
    {
		parent::__construct();
		
		$this->load->helper('url');
		$this->load->helper('captcha');
		$this->load->helper('phpmailer');
		
		$this->load->library('utilities');
		$this->load->library('messages');			
		$this->mail_template = 'mail_templates/'; // base_url().'application/views/mail_templates/';
		
		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Mcompany');
		$this->load->model('Mpayment');
		$this->load->model('Morder_details');
		$this->load->model('Mclients');
		$this->load->model('Mgeneral_settings');
		/*=================================*/
    } 
    
    function payment_gateway($orderid,$type=null){
    	
       	$orderdetails = $this->Morders->get_orders($orderid);
	
    	$company_id = $orderdetails[0]->company_id;
    	$paypalTax = 0;
    	$data['merchant_info'] = $this->Mpayment->get_merchant_info($company_id);
    	
    	$payment_methods = $this->Mpayment->get_selected_payment_methods($company_id);
       	foreach($payment_methods as $method){
       		$payment_gateway[] = $this->Mpayment->get_payment_method_info($method);
       	}
    	
    	$data['payment_gateway'] = $payment_gateway;
    	if($orderdetails[0]->order_pickupdate != "0000-00-00")
    		$paypalTax = $orderdetails[0]->pic_tax_amount_added;
    	elseif($orderdetails[0]->delivery_date != "0000-00-00")
    	$paypalTax = $orderdetails[0]->del_tax_amount_added;
    	
    	$finalAmount = (float)( (float)$orderdetails[0]->order_total + (float)$orderdetails[0]->delivery_cost + (float)$paypalTax - ((float)$orderdetails[0]->disc_amount + (float)$orderdetails[0]->disc_client_amount) );
    

    	$data['iSiteID'] = 5272;
    	# Hash-check value
    	$data['sAPIKey'] = 'p7z3V_wh' ;
    	$data['ref'] = $orderdetails[0]->id . date( 'YmdHis' );
    	$data['sControlURL'] =  base_url().'cardgate/callback_url';
    	$data['sPrefix'] = 'TEST'; // 'TEST' for testing
    	$data['pay_amount'] = number_format($finalAmount,2);
    	$data['sAmount'] = number_format($finalAmount,2) * 100;
    	$data['email'] = $orderdetails[0]->email_c;
    	
    	$transition_data = array('user_email'=>$orderdetails[0]->email_c,
				    			'order_id'=>$orderdetails[0]->id,
				    			'client_id'=>$orderdetails[0]->clients_id);
    	$this->Mpayment->save_tranaction_info($transition_data); 

    	if($type == 'portal'){
    		$data['return_url'] = $this->config->item('portal_url');
       	}
    
    	$data['return_url'] = $orderdetails[0]->shop_url;
    	$data['sHash'] = md5( $data['sPrefix'] . $data['iSiteID'] . $data['sAmount'] . $data[ 'ref' ] . $data['sAPIKey'] );
    	# Form target
    	$data['_FormAction'] = 'https://gateway.cardgateplus.com';
    	$data['_FormTarget'] = '';
    	
    	$sBankOptions = file_get_contents( 'https://gateway.cardgateplus.com/cache/idealDirectoryCUROPayments.dat' );
    	if ( empty( $sBankOptions ) || $sBankOptions[ 0 ] != 'a' ) {
    		# Fallback in case of an error
    		$sBankOptions = 'a:11:{i:0;s:0:"";s:8:"ABNANL2A";s:8:"ABN Amro";s:8:"FRBKNL2L";s:14:"Friesland Bank";s:8:"INGBNL2A";s:3:"ING";s:8:"RABONL2U";s:8:"Rabobank";s:8:"SNSBNL2A";s:8:"SNS Bank";s:8:"ASNBNL21";s:8:"ASN Bank";s:8:"KNABNL2H";s:4:"Knab";s:8:"RBRBNL21";s:9:"RegioBank";s:8:"TRIONL2U";s:12:"Triodos Bank";s:8:"FVLBNL22";s:21:"Van Lanschot Bankiers";};';
    	}
    	$aBankOptions = unserialize( $sBankOptions );
    	unset( $aBankOptions[ 0 ] ); # Remove blank option
    	# Convert to HTML
    	$sBankOptions = '<optgroup label="Kies uw bank">';
    	foreach ( $aBankOptions as $k => $v ) {
    		if ( $v[ 0 ] == '-' ) {
    			$sBankOptions .= '<optgroup label="' . str_replace( '-', '', $v ) . '">';
    		} else {
    			$sBankOptions .= '<option value="' . $k . '">' . $v . '</option>';
    		}
    	}
    	$sBankOptions.= '</optgroup>';
    	
    	$data['sBankOptions'] = $sBankOptions;
    	$this->load->view('payment_gateway',$data);
    }
    
    
    function success($param = null){
    	
    	die("Sucess page");
    }

    function failure($param = null){
    	 
    	die("failure page");
    }
    
    function callback_url(){
    	
    	$text = 'test';
    	$transaction_id 		= $this->input->post('transaction_id');
    	$site_id 				= $this->input->post('site_id');
    	$is_test				= $this->input->post('is_test');
    	$ref 					= $this->input->post('ref');
    	$status 				= $this->input->post('status');
    	$amount 				= $this->input->post('amount');
    	$customer_email 		= $this->input->post('customer_email');
    	$customer_ip_address 	= $this->input->post('customer_ip_address');
    	$billing_option			= $this->input->post('billing_option');
    	$processor_ref  		= $this->input->post('processor_ref ');
    	$hash 					= $this->input->post('hash');
    	$transition_data 		= $this->Mpayment->get_transition_info($customer_email);
    	$order_id 				=  $transition_data[0]['order_id'];
    	$client_id 				=  $transition_data[0]['client_id'];
    	$text .= 'order_id---->'.$order_id.'----/client'.$client_id; 
    	
    	$data = array('transaction_id'=>$transaction_id,
    			'order_id' => $order_id,
    			'client_id' => $client_id,
    			'site_id'=> $site_id ,
    			'is_test'=> $is_test ,
		    	'ref'=> $ref,
		    	'status'=>$status,
		    	'amount'=> $amount,
		    	'customer_email'=>$customer_email,
		    	'customer_ip_address'=>$customer_ip_address,
		    	'billing_option'=>$billing_option,
		    	'processor_ref'=>$processor_ref,
		    	'hash'=>$hash);
    	
    	$this->Mpayment->save_transaction($data);    	
  //  	$status = 200;
 //  	$order_id = 2777;
    
    	if($status == 200){
    		$payment_status = '1';
    		$orderdetails = $this->Morders->get_orders($order_id);
    		$company_id = $orderdetails[0]->company_id;
    	//	$client_id = $orderdetails[0]->clients_id;
    		$company_data = $this->Mcompany->get_company(array('id'=>$company_id));
    		$client = $this->Mclients->get_clients(array('id'=>$client_id));
    		$getMail = file_get_contents( base_url().'assets/mail_templates/after_payment_success_mail.html' );
    		
    		
    		/* COMPANY EMAIL */
    		// Sending to Company
  
    		$parse_email_array = array(
    				"order_no_text" => _("Order No."),
    				"transaction_no_text"=> _("Transaction No"),
    				"customer_data_text" => _("Customer Data"),
    				"payment_option_selected"=> _("via: ").$billing_option,
    				"mobile_text" => _("Mobile"),
    				"phone_text" => _("Phone"),
    				"email_text" => _("Email"),
    				"regard_text" => _("Regards"),
    				"Message" => _('Order processing has been completed').'<br /><br />'._('Payment has been successfully made').' <br /><br />',
    				"Name"=>$client[$client_id]->firstname_c." ".$client[$client_id]->lastname_c,
    				"order_no"=> $order_id,
    				"transaction_no"=> $transaction_id,
    				"client_company"=>$client[$client_id]->company_c,
    				"first_name"=>$client[$client_id]->firstname_c,
    				"last_name"=>$client[$client_id]->lastname_c,
    				"house_no"=>$client[$client_id]->housenumber_c,
    				"address"=>$client[$client_id]->address_c,
    				"city"=>$client[$client_id]->city_c,
    				"postcode"=>$client[$client_id]->postcode_c,
    				"country"=>'',//$client->country_name,
    				"mobile"=>$client[$client_id]->mobile_c,
    				"phone"=>$client[$client_id]->phone_c,
    				"email"=>$client[$client_id]->email_c,
    				"CompanyName"=>$company_data[0]->email
    				 
    		);
    		$cmp_mail_subject = _("Payment Confirmation");
    		$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );
    		$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
    		// echo $mail_body;die("/");
    		$company_email = $company_data[0]->email;
    		send_email( $company_email, $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body, NULL, NULL, NULL, 'site_admin', 'company', 'api_client_associated');
    		
    		
    		/* CLIENT MAIL*/
    		// Sending to Client
    		if($orderdetails[0]->lang_id == 1)
    			$this->lang->load('mail', 'english' );
    		elseif($orderdetails[0]->lang_id == 2)
    		$this->lang->load('mail', 'dutch' );
    		elseif($orderdetails[0]->lang_id == 3)
    		$this->lang->load('mail', 'french' );
    		else
    			$this->lang->load('mail', 'dutch' );
    		
    		$parse_email_array = array(
    				"order_no_text" => $this->lang->line('mail_order_no'),
    				"transaction_no_text"=> $this->lang->line('mail_tran_no'),
    				"customer_data_text" => $this->lang->line('mail_customer_data'),
    				"payment_option_selected"=> $this->lang->line("mail_via").$billing_option,
    				"mobile_text" => $this->lang->line("mail_mobile"),
    				"phone_text" => $this->lang->line("mail_phone"),
    				"email_text" => $this->lang->line("mail_email"),
    				"regard_text" => $this->lang->line("mail_regards"),
    				"Message" => _('Order processing has been completed').'<br /><br />'._('Payment has been successfully made').' <br /><br />',
    				"Name"=> $client[$client_id]->firstname_c." ".$client[$client_id]->lastname_c,
    				"order_no"=> $order_id,
    				"transaction_no"=> $transaction_id,
    				"client_company"=>$client[$client_id]->company_c,
    				"first_name"=>$client[$client_id]->firstname_c,
    				"last_name"=>$client[$client_id]->lastname_c,
    				"house_no"=>$client[$client_id]->housenumber_c,
    				"address"=>$client[$client_id]->address_c,
    				"city"=>$client[$client_id]->city_c,
    				"postcode"=>$client[$client_id]->postcode_c,
    				"country"=>'',//$client->country_name,
    				"mobile"=>$client[$client_id]->mobile_c,
    				"phone"=>$client[$client_id]->phone_c,
    				"email"=>$client[$client_id]->email_c,
    				"CompanyName"=>$company_data[0]->email
    					
    		);
    		$cmp_mail_subject = $this->lang->line("mail_payment_subject");
    		$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );
    		$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
    		$client_email = $client[$client_id]->email_c; 
    		send_email( $client_email, $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body, NULL, NULL, NULL, 'site_admin', 'company', 'api_client_associated');
    	
    	}elseif($status == 300 ){
    		$payment_status = '0';
    	}elseif($status == 0){
    		$payment_status = '2';
    	}
    	else{
    		$payment_status = '0';
    	}
    	
		$this->Mpayment->update_order_info($transaction_id,$payment_status,$order_id);
	
	
    	
		$file = fopen("test.txt","w");
		fwrite($file,$text);
		fclose($file);
    	
    	
    }

}