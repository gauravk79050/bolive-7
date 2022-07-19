<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {
	 
	var $template = NULL;
	
	function __construct()
    {
		parent::__construct();
		error_reporting(0);
		$this->load->helper('url');
		$this->load->helper('captcha');
		$this->load->helper('phpmailer');
		
		$this->load->config('paypallib_config');
		
		$this->load->library('paypal_lib');
		$this->load->library('messages');			
		$this->mail_template = 'mail_templates/'; // base_url().'application/views/mail_templates/';
		
		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Mcompany');
		$this->load->model('Morder_details');
		$this->load->model('Mcategories');
		$this->load->model('Mdelivery_areas');
		$this->load->model('Mdelivery_settings');
		$this->load->model('Mgeneral_settings');
		/*=================================*/
    } 
	
	function paypal($orderid)
	{
		$this->auto_form($orderid);
	}
	
	function form($orderid)
	{
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
	                'orders.pic_tax_amount_added'
        );

		$orderdetails = $this->Morders->get_orders( $select, $orderid );
		$general_settings = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$orderdetails[0]->company_id));
		$company_data = $this->Mcompany->get_company(array('id'=>$orderdetails[0]->company_id));
		
		$paypalTax = 0;
		if($orderdetails[0]->order_pickupdate != "0000-00-00")
			$paypalTax = $orderdetails[0]->pic_tax_amount_added;
		elseif($orderdetails[0]->delivery_date != "0000-00-00")
			$paypalTax = $orderdetails[0]->del_tax_amount_added;
		
		$finalAmount = (float)( (float)$orderdetails[0]->order_total + (float)$orderdetails[0]->delivery_cost + (float)$paypalTax - ((float)$orderdetails[0]->disc_amount + (float)$orderdetails[0]->disc_client_amount) );
		
		$this->paypal_lib->add_field('business', $general_settings[0]->paypal_address);
	    $this->paypal_lib->add_field('return', site_url('payment/success'));
	    $this->paypal_lib->add_field('cancel_return', site_url('payment/cancel'));
	    $this->paypal_lib->add_field('notify_url', site_url('payment/ipn')); // <-- IPN url
	    //$this->paypal_lib->add_field('custom', $_SERVER['REQUEST_URI']); // <-- Verify return

	    $this->paypal_lib->add_field('item_name', 'Order - '.$company_data[0]->company_name);
	    $this->paypal_lib->add_field('item_number', $orderid);
	    $this->paypal_lib->add_field('amount', round($finalAmount, 2));

		// if you want an image button use this:
		$this->paypal_lib->image('button_03.gif');
		
		// otherwise, don't write anything or (if you want to 
		// change the default button text), write this:
		// $this->paypal_lib->button('Click to Pay!');
		
	    $data['paypal_form'] = $this->paypal_lib->paypal_form();
	
		$this->load->view('paypal/form', $data);
        
	}

	function auto_form($orderid)
	{
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
	                'orders.pic_tax_amount_added'
        );

		$orderdetails = $this->Morders->get_orders( $select, $orderid );
		$general_settings = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$orderdetails[0]->company_id));
		$company_data = $this->Mcompany->get_company(array('id'=>$orderdetails[0]->company_id));
		
		$paypalTax = 0;
		if($orderdetails[0]->order_pickupdate != "0000-00-00")
			$paypalTax = $orderdetails[0]->pic_tax_amount_added;
		elseif($orderdetails[0]->delivery_date != "0000-00-00")
			$paypalTax = $orderdetails[0]->del_tax_amount_added;
		
		$finalAmount = (float)( (float)$orderdetails[0]->order_total + (float)$orderdetails[0]->delivery_cost + (float)$paypalTax - ((float)$orderdetails[0]->disc_amount + (float)$orderdetails[0]->disc_client_amount) );
		
		$this->paypal_lib->add_field('business', $general_settings[0]->paypal_address);
	    $this->paypal_lib->add_field('return', site_url('payment/success/'.$orderid));
	    $this->paypal_lib->add_field('cancel_return', site_url('payment/cancel/'.$orderid));
	    $this->paypal_lib->add_field('notify_url', site_url('payment/ipn')); // <-- IPN url
	    //$this->paypal_lib->add_field('custom', '1234567890'); // <-- Verify return

	    $this->paypal_lib->add_field('item_name', 'Order - '.$company_data[0]->company_name);
	    $this->paypal_lib->add_field('item_number', $orderid);
	    $this->paypal_lib->add_field('amount', round($finalAmount, 2));

	    $this->paypal_lib->paypal_auto_form();
	}
	
	function cancel($orderid)
	{
		$orderdetails = $this->Morders->get_orders( $select = array( 'orders.company_id' ), $orderid);
		$general_settings = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$orderdetails[0]->company_id));
		$data['payment_message'] = $pay_incomplete_msg = $general_settings[0]->pay_incomplete_msg;
		
		// Send Payment Cancelled Mail to Client
		
		$this->load->view('paypal/cancel', $data);
	}
	
	function success($orderid)
	{
		$orderdetails = $order_data = $this->Morders->get_orders( $select = array( 'orders.company_id' ), $orderid );
		//$order_details_data = $this->Morder_details->get_order_details($orderid);
		
		//$Company = $this->Mcompany->get_company(array('id'=>$orderdetails[0]->company_id));
		$general_settings = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$orderdetails[0]->company_id));	
		
			
		$data['payment_message'] = $pay_complete_msg = $general_settings[0]->pay_complete_msg;
		$data['pp_info'] = $_POST;
		$orderdetails = $this->Morders->get_orders( $select = array( 'orders.shop_url' ), @$_POST['item_number']);
		if($orderdetails[0]->shop_url)
		$data['redirect'] = $orderdetails[0]->shop_url;
		
		$this->load->view('paypal/success', $data);
	}
	
	function ipn()
	{
		$to    = $this->config->item('paypal_lib_receiver_email_address');    //  your email

		if ($this->paypal_lib->validate_ipn()) 
		{
			
			$this->Morders->update_order($this->paypal_lib->ipn_data['item_number'],array('payment_status'=>'1','transaction_id'=>$this->paypal_lib->ipn_data['txn_id']));
			
			$mail_data = array();
			$body = $this->load->view('mail_templates/paypal_ipn_recieved_payment', $mail_data, true);
			
			send_email( $to, $this->paypal_lib->ipn_data['payer_email'], 'CI paypal_lib IPN (Received Payment)', $body, $this->paypal_lib->ipn_data['payer_name'], NULL, NULL, 'payer', 'payment_receiver', 'ipn_payment_success_mail');
		}
	}
	
}

?>