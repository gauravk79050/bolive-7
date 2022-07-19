<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cardgate extends CI_Controller {

	var $template = NULL;

	function __construct()
    {
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('captcha');
		$this->load->helper('phpmailer');
		$this->load->helper('curo');

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

       	$select = array( 
                    'orders.id',
                    'orders.disc_amount',
                    'orders.delivery_cost',
                    'orders.order_total',
                    'orders.disc_client_amount',
                    'orders.order_pickupdate',
                    'orders.delivery_date',
                    'orders.company_id',
                    'orders.del_tax_amount_added',
                    'orders.pic_tax_amount_added',
                    'clients.email_c',
                    'clients.clients_id',
                    'clients.shop_url'
        );

        $orderdetails = $this->Morders->get_orders( $select, $orderid );

    	$company_id = $orderdetails[0]->company_id;
    	$paypalTax = 0;
    	$merchant_info = $this->Mpayment->get_merchant_info($company_id);

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
    	//$data['iSiteID'] = $merchant_info[0]['site_id'];
    	# Hash-check value
    	$data['sAPIKey'] = 'p7z3V_wh' ;
    	// $data['sAPIKey'] = $merchant_info[0]['site_hash_key'];
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

    	$this->load->model('Mpayment');

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
    	$transition_data 		= $this->Mpayment->get_transition_info(trim($ref));

    	if(!empty($transition_data)){

    		$order_id 				=  $transition_data[0]['order_id'];
    		$client_id 				=  $transition_data[0]['client_id'];

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

    		$text = date('Y-m-d H:i:s').' => '.json_encode($data)."\n";
	    	$file = fopen(dirname(__FILE__)."/../../payment-log.txt","a");
	    	fwrite($file,$text);
	    	fclose($file);

    		$this->Mpayment->save_transaction($data);

    		if($status == 200){
    			$payment_status = '1';
    			$orderdetails = $this->Morders->get_orders( $select = array( 'orders.company_id','orders.lang_id' ), $order_id );
    			$company_id = $orderdetails[0]->company_id;
    			//	$client_id = $orderdetails[0]->clients_id;
    			$this->db->select('company_name');
    			$this->db->where('id',$company_id);
    			$company_data = $this->db->get('company')->row();
    			//$company_data = $this->Mcompany->get_company(array('id'=>$company_id));

    			$this->db->select('emailid');
    			$this->db->where('company_id',$company_id);
    			$general_settings = $this->db->get('general_settings')->row();

    			$client = $this->Mclients->get_clients(array('id'=>$client_id));
    		//	echo "<pre>";print_r($client);die("//");
    			//$getMail = file_get_contents( base_url().'assets/mail_templates/after_payment_success_mail.html' );
    			$getMail = $this->load->view( 'mail_templates/after_payment_success_mail.html',null,true );
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
    					"CompanyName"=>$company_data->company_name

    			);
    			//	echo "<pre>";print_r($parse_email_array);die('/////');
    			$cmp_mail_subject = _("company Payment Confirmation");
    			$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );
    			$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';
    			// echo $mail_body;die("/");
    			$company_email = $general_settings->emailid;
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
    					"CompanyName"=>$company_data->company_name

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
    	}
    }

    function callback_url_new(){

    	$this->load->model('Mpayment');

    	$transaction_id 		= $this->input->get('transaction');
    	$site_id 				= $this->input->get('site');
    	$is_test				= $this->input->get('testmode');
    	$ref 					= $this->input->get('reference');
    	$status 				= $this->input->get('code');
    	$amount 				= $this->input->get('amount');
    	$customer_email 		= $this->input->get('customer_email');
    	$customer_ip_address 	= $this->input->get('ip');
    	$billing_option			= $this->input->get('pt');
    	$processor_ref  		= $this->input->get('transaction');
    	$hash 					= $this->input->get('hash');

    	$transition_data 		= $this->Mpayment->get_transition_info(trim($ref));

    	if(!empty($transition_data)){

    		$order_id 				=  $transition_data[0]['order_id'];
    		$client_id 				=  $transition_data[0]['client_id'];

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

    		$text = date('Y-m-d H:i:s').' => '.json_encode($data)."\n";
    		$file = fopen(dirname(__FILE__)."/../../payment-log.txt","a");
    		fwrite($file,$text);
    		fclose($file);

    		$this->Mpayment->save_transaction($data);

    		if($status == 200){
    			$payment_status = '1';
    		}elseif($status == 300 ){
    			$payment_status = '0';
    		}elseif($status == 0){
    			$payment_status = '2';
    		}else{
    			$payment_status = '0';
    		}

    		$this->Mpayment->update_order_info($transaction_id,$payment_status,$order_id);

    		$this->db->select('orders.*,delivery_country.country_name as delivery_country_name');
    		$this->db->join('country as delivery_country','orders.delivery_country = delivery_country.id','left');//used in delivery address
    		$real_order = $this->db->where( array('temp_id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders' )->row_array();

    		if(empty($real_order)){

    			$this->db->select('orders_tmp.*,delivery_country.country_name as delivery_country_name');
    			$this->db->join('country as delivery_country','orders_tmp.delivery_country = delivery_country.id','left');//used in delivery address
    			$order_data = $this->db->where( array('orders_tmp.id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders_tmp' )->row_array();

    			// Copying order and order details from temporary table to original table
    			if(!empty($order_data)){

    				$insert_array = array(
    						'clients_id' => $order_data['clients_id'],
    						'company_id' => $order_data['company_id'],
    						'order_total' => $order_data['order_total'],
    						'order_status' => $order_data['order_status'],
    						'order_remarks' => $order_data['order_remarks'],
    						'order_pickupdate' => $order_data['order_pickupdate'],
    						'order_pickupday' => $order_data['order_pickupday'],
    						'order_pickuptime' => $order_data['order_pickuptime'],
    						'delivery_streer_address' => $order_data['delivery_streer_address'],
    						'delivery_busnummer' => $order_data['delivery_streer_address'],
    						'delivery_area' => $order_data['delivery_area'],
    						'delivery_city' => $order_data['delivery_city'],
    						'delivery_zip' => $order_data['delivery_zip'],
    						'delivery_day' => $order_data['delivery_day'],
    						'delivery_hour' => $order_data['delivery_hour'],
    						'delivery_minute' => $order_data['delivery_minute'],
    						'delivery_date' => $order_data['delivery_date'],
    						'delivery_remarks' => $order_data['delivery_remarks'],
    						'delivery_cost' => $order_data['delivery_cost'],
    						'del_apply_tax' => $order_data['del_apply_tax'],
    						'del_tax_amount_added' => $order_data['del_tax_amount_added'],
    						'pic_apply_tax' => $order_data['pic_apply_tax'],
    						'pic_tax_amount_added' => $order_data['pic_tax_amount_added'],
    						'ok_msg' => $order_data['ok_msg'],
    						'hold_msg' => $order_data['hold_msg'],
    						'completed' => $order_data['completed'],
    						'option' => $order_data['option'],
    						'created_date' => $order_data['created_date'],
    						'subadmin_id' => $order_data['subadmin_id'],
    						'shop_url' => $order_data['shop_url'],
    						'payment_via_paypal' => $order_data['payment_via_paypal'],
    						'payment_status' => $payment_status,
    						'transaction_id' => $order_data['transaction_id'],
    						'lang_id' => $order_data['lang_id'],
                            'disc_after_amount' => $order_data['disc_after_amount'],
                            'disc_promo_perc' => $order_data['disc_promo_perc'],
                            'disc_promo_price' => $order_data['disc_promo_price'],
    						'printed' => $order_data['printed'],
    						'disc_percent' => $order_data['disc_percent'],
    						'disc_price' => $order_data['disc_price'],
    						'disc_amount' => $order_data['disc_amount'],
    						'disc_client' => $order_data['disc_client'],
    						'disc_client_amount' => $order_data['disc_client_amount'],
    						'disc_other_code' => $order_data['disc_other_code'],
    						'other_code_type' => $order_data['other_code_type'],
    						'labeler_printed' => $order_data['labeler_printed'],
    						'from_bo' => $order_data['from_bo'],
    						'get_invoice' => $order_data['get_invoice'],
    						'name' => $order_data['name'],
    						'delivery_country' => $order_data['delivery_country'],
    						'phone_reciever' => $order_data['phone_reciever'],
    						'ip_address' => $order_data['ip_address'],
    						'temp_id' => $order_data['id']
    				);

    				if($this->db->insert('orders', $insert_array)){
    					$order_data['temp_id'] = $order_data['id'];
    					$order_data['id'] = $new_order_id = $this->db->insert_id();

    					// associating client
    					$this->associate_client_with_company( $client_id, $order_data['company_id'] );

    					if(isset($order_data['applied_code']) && $order_data['applied_code'] == 'introcode') {
    						$this->db->where(array('company_id'=>$order_data['company_id'], 'client_id' => $client_id));
    						$this->db->update('client_numbers', array('introcode_applied' => 1));
    					}

    					$this->db->where( array('orders_id'=>$order_id) );
    					$this->db->join( 'products', 'products.id = order_details_tmp.products_id' );
    					$order_details_data = $this->db->get( 'order_details_tmp' )->result_array();

    					if(!empty($order_details_data)){
    						foreach ($order_details_data as $details){
    							$insert_array = array(
    									'orders_id' => $new_order_id,
    									'products_id' => $details['products_id'],
    									'default_price' => $details['default_price'],
    									'discount' => $details['discount'],
    									'add_costs' => $details['add_costs'],
    									'content_type' => $details['content_type'],
    									'sub_total' => $details['sub_total'],
    									'quantity' => $details['quantity'],
    									'total' => $details['total'],
    									'pro_remark' => $details['pro_remark'],
    									'image' => ( ($details['image'])?$details['image']:'' ),
    									'weight_unit' => ( ($details['weight_unit'])?$details['weight_unit']:'' ),
    									'extra_field' => $details['extra_field'],
    									'extra_name' => $details['extra_name']
    							);

    							$this->db->insert('order_details', $insert_array);
    						}
    					}

    					// Sending Mails
    					$order_data['billing_option'] = $billing_option;
    					$order_data['payment_status'] = $payment_status;
    					if($this->send_order_details_post( $new_order_id, $client_id, $order_data, $order_details_data, $order_data['ip_address']))
    						$this->response('OK',200);
    					else
    						$this->response('Error',400);
    				}
    			}
    		}
    		else{
    			// Updating payment status
    			$this->db->where('id', $real_order['id']);
    			$this->db->update('orders', array('payment_status' => $payment_status));

    			// Have to add code to send a mail to admin and client that payment has been made..
    			if($payment_status == "1"){
    				$real_order['billing_option'] = $billing_option;
    				$real_order['payment_status'] = $payment_status;

    				$this->db->where( array('orders_id'=>$real_order['id']) );
    				$this->db->join( 'products', 'products.id = order_details.products_id' );
    				$order_details_data = $this->db->get( 'order_details' )->result_array();

    				if($this->send_order_details_post( $real_order['id'], $client_id, $real_order, $order_details_data, $real_order['ip_address']))
    					$this->response('OK',200);
    				else
    					$this->response('Error',400);
    			}else{
    				$this->response('Error',200);
    			}
    		}

    		//$this->Mpayment->update_order_info($transaction_id,$payment_status,$order_id);
    	}
    }

    function associate_client_with_company( $client_id, $company_id )
    {
    	if( !$client_id || !$company_id )
    		return false;

    	$this->db->select('first_name');
    	$this->db->where('id',$company_id);
    	$company = $this->db->get('company')->row();
    	//$company_email = $company->email;

    	$this->db->select('emailid,activate_discount_card');
    	$company_gs = $this->db->where( array('company_id'=>$company_id) )->get( 'general_settings' )->row();
    	$company_email = $company_gs->emailid;

    	$new_client = 0;
    	//$clients_associated = $this->db->get_where('client_numbers', array('company_id'=>$company_id, 'client_id' => $client_id, 'associated' => 0))->result();
    	$clients_associated = $this->db->get_where('client_numbers', array('company_id'=>$company_id, 'client_id' => $client_id))->result();
    	if(!empty($clients_associated)){
    		$clients_associated = $clients_associated['0'];
    		if(!$clients_associated->associated){
    			$new_client = 1;
    			$this->db->where(array('company_id'=>$company_id, 'client_id' => $client_id));
    			$this->db->update('client_numbers', array('associated' => '1'));
    		}
    	}else{
    		$new_client = 1;
    		$insert_array = array(
    				'company_id' => $company_id,
    				'client_id'  => $client_id,
    				'newsletter' => 'subscribe',
    				'associated' => '1'
    		);
    		$this->db->insert('client_numbers', $insert_array);
    	}

    	if($new_client){

    		$client = $this->db->where( array('id'=>$client_id) )->get( 'clients' )->row();
    		// ---- >>> Sending Mail to Company <<< ---- //

    		$cmp_mail_subject = 'OBS - '._('New Client Registered');

    		$discount_num = '';
    		if($company_gs->activate_discount_card != 0){
    			$discount_card_info = $this->db->get_where("client_numbers", array("client_id" => $client_id, 'company_id'=>$company_id))->result_array();
    			$fp = fopen(APPPATH . "/controllers/new-api/logs-disc.txt","a");
    			fwrite($fp,"general settings: ".json_encode($discount_card_info));
    			fclose($fp);
    			$discount_num = '--';
    			if(!empty($discount_card_info)){
    				if($discount_card_info['0']['discount_card_number'] && $discount_card_info['0']['discount_card_number'] != 0)
    					$discount_num = $discount_card_info['0']['discount_card_number'];
    			}
    		}

    		$mail_data = array();
    		$mail_data['first_name'] = $company->first_name;
    		$mail_data['firstname_c'] = $client->firstname_c;
    		$mail_data['lastname_c'] = $client->lastname_c;
    		$mail_data['company_c'] = $client->company_c;
    		$mail_data['notifications'] = $client->notifications;
    		$mail_data['vat_c'] = $client->vat_c;
    		$mail_data['address_c'] = $client->address_c;
    		$mail_data['housenumber_c'] = $client->housenumber_c;
    		$mail_data['postcode_c'] = $client->postcode_c;
    		$mail_data['city_c'] = $client->city_c;
    		$mail_data['phone_c'] = $client->phone_c;
    		$mail_data['mobile_c'] = $client->mobile_c;
    		$mail_data['email_c'] = $client->email_c;
    		$mail_data['discount_num'] = $discount_num;

    		$fp = fopen(APPPATH . "/controllers/new-api/logs-md.txt","a");
    		fwrite($fp,"Mail data1: ".json_encode($mail_data));
    		fclose($fp);

    		$mail_body = $this->load->view( 'mail_templates/client_associated', $mail_data, true);

    		send_email( $company_email, $this->config->item('no_reply_email'), $cmp_mail_subject, $mail_body, NULL, NULL, NULL, 'no_reply', 'company', 'api_client_associated');
    		//send_email( "shyammishra@cedcoss.com", $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
    		return true;
    	}

    	return true;
    }

    function send_order_details_post( $order_id = 0, $client_id = 0, $order_data = array(), $order_details_data = array(), $ip_address = '' )
    {
    	if( !$order_id || !$client_id )
    		return false;

    	$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
    	$this->db->join('country','country.id = clients.country_id','left');
    	$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
    	// $company = $this->db->where( array('id'=>$this->company_id) )->get( 'company' )->row();
    	$company = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row();

    	$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order_data['company_id']) )->get( 'client_numbers' )->result();
    	$c_discount_number = '--';
    	if(!empty($client_discount_number)){
    		$c_discount_number = $client_discount_number['0']->discount_card_number;
    	}

    	$sub_admin = array();
    	if($company->role == 'super'){
    		$this->db->select("company_name,email_ads");
    		$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
    	}

    	$this->db->select('emailid, subject_emails, orderreceived_msg, payment_cancel_msg, disable_price, activate_discount_card, disc_per_amount, disc_after_amount');
    	$company_gs = $this->db->where( array('company_id'=>$order_data['company_id']) )->get( 'general_settings' )->result();
    	if(!empty($company_gs))
    		$company_gs = $company_gs['0'];

    	// Fetching Delivery settings
    	$delivery_settings = array();
    	if($order_data['option'] == '2'){
    		$delivery_settings = $this->get_delivery_settings($order_data['company_id']);
    	}

    	$fp = fopen(APPPATH . "/controllers/new-api/logs.txt","a");

    	fwrite($fp,json_encode(array('client'=>$client,'company'=>$company,'company_gs'=>$company_gs,'order_data'=>$order_data,'order_details_data'=>$order_details_data)));

    	fclose($fp);

    	if( !empty($client) && !empty($company) && !empty($company_gs) ){
    		$this->load->library('utilities');
    		$this->load->helper('phpmailer');

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
    			if($sub_admin['company_name'] == "1"){
    				$email_messages = $this->db->get('email_messages')->row();
    				$Options4 = $email_messages->emailads_text_message;
    			}
    		}else{
    			if($company->email_ads == "1"){
    				$email_messages = $this->db->get('email_messages')->row();
    				$Options4 = $email_messages->emailads_text_message;
    			}
    		}

    		$mail_data['order_id'] = $order_id;
    		$mail_data['order_data'] = $order_data;
    		$mail_data['ip_address'] = $ip_address;
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
    		$mail_data['is_international'] = false;
    		$mail_data['is_send_order_mail'] = true;

    		//countries details for international delivery
    		if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
    			$mail_data['is_international'] = true;
    		}
    		/* $countries = array();
    			if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
    		$mail_data['is_international'] = true;
    		$this->db->select('id,country_name');
    		$country_arr = $this->db->get('country')->result();
    		foreach($country_arr as $country){
    		$countries[$country->id] = $country;
    		}
    		}
    		$mail_data['countries'] = $countries; */

    		if($order_data['payment_status'] == 1){
	    		$mail_body = '';
	    		if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
	    			$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin_int', $mail_data, true );
	    		}
	    		else{
	    			$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
	    		}

	    		$flag = send_email( $company_email, $this->config->item('no_reply_email'), stripslashes($orderreceived_subject), $mail_body, _('Order').' '.$company_name, NULL, NULL, 'no_reply', 'company', 'api_new_order_details',1,$client->email_c);
    		}
    		//----------------------------------------------------------------------------------------------------------------------------------//
    		// Sending to Client

    		if($order_data['lang_id'] == 1)
    			$this->lang->load('mail', 'english' );
    		elseif($order_data['lang_id'] == 2)
    			$this->lang->load('mail', 'dutch' );
    		elseif($order_data['lang_id'] == 3)
    			$this->lang->load('mail', 'french' );
    		else
    			$this->lang->load('mail', 'dutch' );

    		if($mail_data['order_data']['payment_via_paypal'] == '2' && isset($mail_data['order_data']['billing_option'])){

    			if(!(isset($mail_data['order_data']['payment_status']) && $mail_data['order_data']['payment_status'] == 1) && $company_gs->payment_cancel_msg != ''){
    				$mail_data['orderreceived_msg'] = $company_gs->payment_cancel_msg;
    			}
    		}
    		$mail_body = '';
	    	if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
	    		$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_client_int', $mail_data, true );
	    	}
	    	else{
	    		$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_client', $mail_data, true );
	    	}

    		$flag = send_email( $client->email_c, $company_email, stripslashes($orderreceived_subject), $mail_body, _('Order').' '.$company_name, NULL, NULL, 'no_reply', 'client', 'api_new_order_details' );

    		$this->db->where('id',$order_id);
    		$this->db->update('orders', array('print_ready' => 1));

    		return true;
    	}
    	else{
    		$this->response( array('error' => 1, 'message'=> _('Can\'t send mail to company & clients !'), 'data' => $order_id ), 404 );
    	}
    }

    /* function test(){

    	$merchant_id = 'sitematic';
		$api_key = 'Y8f0cnqB0WKAKZ1WwHTFNL2jge2pzOzXuEnBNHfWAuLNroo3Fjbm4iTJzcWqrR7l';
		$ch = curl_init ();
		curl_setopt($ch, CURLOPT_URL, "https://api.cardgate.com/rest/v1/ideal/issuers/");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $merchant_id.':'.$api_key);
		curl_setopt($ch, CURLOPT_SSLVERSION,3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/xml'));
		curl_setopt ($ch, CURLOPT_POST, 0);
		curl_setopt ($ch, CURLOPT_VERBOSE, 1);
		$response = curl_exec($ch);
		// close cURL resource, and free up system resources
		curl_close($ch);
		//print response
		echo "TEST<pre>";print_r(json_decode($response));
    } */

    function get_available_payment_methods(){

    	$company_id = $this->input->post('company_id');

    	$payment_methods = $this->Mpayment->get_selected_payment_methods($company_id);
    	$new_html = '';
    	foreach($payment_methods as $method){
    		$gateway = $this->Mpayment->get_payment_method_info($method);
    		$new_html .= '<input type="checkbox" id="'.$gateway[0]['value'].'" class="avail_payment" value="'.$gateway[0]['value'].'" name="payment_method">&nbsp;'.$gateway[0]['payment_method'];
    		$new_html .= '<br/>';
    	}
    	echo $new_html;
    	// die("/");
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
}