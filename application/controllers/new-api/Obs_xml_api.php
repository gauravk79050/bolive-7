<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Obs_xml_api extends REST_Controller
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
			// create doctype
			$dom = new DOMDocument("1.0");
			header("Content-Type: text/xml");
			
			// create root element
			$root = $dom->createElement("order_details");
			$dom->appendChild($root);
			
			if($order_id && is_numeric($order_id)){
				$this->db->select('orders.* , client_numbers.client_number ,clients.firstname_c , clients.lastname_c ');
				$this->db->where( 'orders.company_id' , 105 );
				$this->db->where( 'orders.id' , $order_id );
				$this->db->join( 'clients', 'clients.id = orders.clients_id' );
				$this->db->join( 'client_numbers', 'clients.id = client_numbers.client_id' );
				$order_details_data = $this->db->get( 'orders' )->row_array();
				
				if(!empty($order_details_data)){
					$detail = $dom->createElement("detail");
					$root->appendChild($detail);
					
					// create details childs element
					$order_number = $dom->createElement("Nummer");
					$detail->appendChild($order_number);
					
					// create text node for order number
					$text = $dom->createTextNode($order_details_data['id']);
					$order_number->appendChild($text);
					
					
					// create details childs element
					$order_date = $dom->createElement("Datum");
					$detail->appendChild($order_date);
					
					if($order_details_data['order_pickupdate'] != ''){
						// create text node for order number
						$text = $dom->createTextNode($order_details_data['order_pickupdate']);
						$order_date->appendChild($text);
					}else{
						$text = $dom->createTextNode('--');
						$order_date->appendChild($text);
					}
					
					// create details childs element
					$order_time = $dom->createElement("Uur");
					$detail->appendChild($order_time);
						
					if($order_details_data['order_pickuptime']){
						$pickup_time = explode(':' , $order_details_data['order_pickuptime']);
						$pickup_hour = $pickup_time['0'];
						$pickup_minute = $pickup_time['1'];
						if(strlen($pickup_minute) == 1){
							$pickup_minute = '0'.$pickup_minute;
						}
					
						$final_time = $pickup_hour.':'.$pickup_minute;
					
						// create text node for order number
						$text = $dom->createTextNode($final_time);
						$order_time->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$order_time->appendChild($text);
					}
					
					// create details childs element
					$client_number = $dom->createElement("Klantnr");
					$detail->appendChild($client_number);
					
					if($order_details_data['client_number'] != ''){
						$client_num = $order_details_data['client_number'];
					
						$length =  strlen($client_num);
						if($length < 8){
							$required_zeros = (int)(8-$length);
							for($j = 0; $j < $required_zeros; $j++){
								$client_num = '0'.$client_num;
							}
						}
						//echo $client_number; die();
						// create text node for order number
						$text = $dom->createTextNode($client_num);
						$client_number->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$client_number->appendChild($text);
					}
					
					// create details childs element
					$client_name = $dom->createElement("Naam");
					$detail->appendChild($client_name);
					
					if(isset($order_details_data['lastname_c']) && isset($order_details_data['firstname_c'])){
						// create text node for order number
						$text = $dom->createTextNode($order_details_data['lastname_c'].' '.$order_details_data['firstname_c']);
						$client_name->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$client_name->appendChild($text);
					}
					
					// create details childs element
					$order_status = $dom->createElement("Status");
					$detail->appendChild($order_status);
					
					// create text node for order number
					$text = $dom->createTextNode($order_details_data['order_status']);
					$order_status->appendChild($text);
					
					
					// create details childs element
					$order_date = $dom->createElement("Best.Date");
					$detail->appendChild($order_date);
					
					// create text node for order number
					$text = $dom->createTextNode(date("d/m/Y", strtotime($order_details_data['created_date'])));
					$order_date->appendChild($text);
					
					
					// create details childs element
					$order_mode = $dom->createElement("Betaling");
					$detail->appendChild($order_mode);
					
					// create text node for order number
					$text = $dom->createTextNode(($order_details_data['payment_via_paypal'])?'Paypal':'Cash');
					$order_mode->appendChild($text);
				}
			}else{
				
				$this->db->select('orders.* , client_numbers.client_number ,clients.firstname_c , clients.lastname_c ');
				$this->db->where( 'orders.company_id' , 105 );
				$this->db->join( 'clients', 'clients.id = orders.clients_id' );
				$this->db->join( 'client_numbers', 'clients.id = client_numbers.client_id' );
				$order_details_data = $this->db->get( 'orders' )->result_array();
				//print_r($order_details_data); die();
				
				foreach($order_details_data as $order_detail){
					// create detail element
					$detail = $dom->createElement("detail");
					$root->appendChild($detail);
						
					// create details childs element
					$order_number = $dom->createElement("Nummer");
					$detail->appendChild($order_number);
						
					// create text node for order number
					$text = $dom->createTextNode($order_detail['id']);
					$order_number->appendChild($text);
						
						
					// create details childs element
					$order_date = $dom->createElement("Datum");
					$detail->appendChild($order_date);
						
					if($order_detail['order_pickupdate'] != ''){
						// create text node for order number
						$text = $dom->createTextNode($order_detail['order_pickupdate']);
						$order_date->appendChild($text);
					}else{
						$text = $dom->createTextNode('--');
						$order_date->appendChild($text);
					}
						
					// create details childs element
					$order_time = $dom->createElement("Uur");
					$detail->appendChild($order_time);
					
					if($order_detail['order_pickuptime']){
						$pickup_time = explode(':' , $order_detail['order_pickuptime']);
						$pickup_hour = $pickup_time['0'];
						$pickup_minute = $pickup_time['1'];
						if(strlen($pickup_minute) == 1){
							$pickup_minute = '0'.$pickup_minute;
						}
						
						$final_time = $pickup_hour.':'.$pickup_minute;
						
						// create text node for order number
						$text = $dom->createTextNode($final_time);
						$order_time->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$order_time->appendChild($text);
					}
						
					// create details childs element
					$client_number = $dom->createElement("Klantnr");
					$detail->appendChild($client_number);

					if($order_detail['client_number'] != ''){
						$client_num = $order_detail['client_number'];
						
						$length =  strlen($client_num);
						if($length < 8){
							$required_zeros = (int)(8-$length);
							for($j = 0; $j < $required_zeros; $j++){
								$client_num = '0'.$client_num;
							}
						}
						//echo $client_number; die();
						// create text node for order number
						$text = $dom->createTextNode($client_num);
						$client_number->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$client_number->appendChild($text);
					}
						
					// create details childs element
					$client_name = $dom->createElement("Naam");
					$detail->appendChild($client_name);
						
					if(isset($order_detail['lastname_c']) && isset($order_detail['firstname_c'])){
						// create text node for order number
						$text = $dom->createTextNode($order_detail['lastname_c'].' '.$order_detail['firstname_c']);
						$client_name->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$client_name->appendChild($text);
					}
						
					// create details childs element
					$order_status = $dom->createElement("Status");
					$detail->appendChild($order_status);
						
					// create text node for order number
					$text = $dom->createTextNode($order_detail['order_status']);
					$order_status->appendChild($text);
						
						
					// create details childs element
					$order_date = $dom->createElement("Best.Date");
					$detail->appendChild($order_date);
						
					// create text node for order number
					$text = $dom->createTextNode(date("d/m/Y", strtotime($order_detail['created_date'])));
					$order_date->appendChild($text);
						
						
					// create details childs element
					$order_mode = $dom->createElement("Betaling");
					$detail->appendChild($order_mode);
						
					// create text node for order number
					$text = $dom->createTextNode(($order_detail['payment_via_paypal'])?'Paypal':'Cash');
					$order_mode->appendChild($text);
				}
				
			}

			echo $dom->saveXML();
			exit;
			
		}
		
	}
	
}
?>