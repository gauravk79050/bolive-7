<?php
class Payment extends CI_Controller
{
	var $template='mcp/';
	
	var $arr=array();
	
		
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->model($this->template.'Mpayment');
			
		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');
	
		if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
			
	}
		
	
	function index()
	{
		$data['header']= $this->template.'header';
		$data['main']= $this->template.'payment';
		$data['footer']= $this->template.'footer';
		
		if($this->input->post('submit')){
			
			$selected = $this->input->post('payment_method');
			$this->Mpayment->update_availabilty($selected);
			$data['msg'] = 'Have enabled the selected payment methods';
		}
		$data['available_methods'] = $this->Mpayment->get_available_payment_methods('all');
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
}