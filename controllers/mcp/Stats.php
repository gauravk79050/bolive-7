<?php

class Stats extends CI_Controller
{	
	var $tempUrl="";
	var $template="";
	function __construct()
	{
	   	parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		//$this->tempUrl = base_url().'application/views/mcp';
		$this->template="mcp/";
		
		$this->load->model($this->template.'mstats');
		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');
			
		if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
		 
	}
	
	function index($msg=NULL)
	{ 
		$data['latest_order_obs'] = $this->mstats->get_latest_order("obs", 11);
		$data['latest_order_bo'] = $this->mstats->get_latest_order("bo", 11);
		$data['latest_order_bo_free'] = $this->mstats->get_latest_order_free(11);
		
		$data['latest_mails_sent'] = $this->mstats->get_latest_mail_sent("obs", 11);
		$data['latest_mails_sent_bo'] = $this->mstats->get_latest_mail_sent("bo", 11);
		
		$data['top_order_company_last_30_days'] = $this->mstats->get_top_order_company(true, 11);
		$data['top_order_company'] = $this->mstats->get_top_order_company(false, 11);
		
		$data['last_login_companies'] = $this->mstats->get_last_login_companies(11);
		
		$data['header']=$this->template.'header';
		$data['main']=$this->template.'stats';
		$data['footer']=$this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}

	/**
	 * Function used to show 1st 100 latest orders in OBS
	 */
	function orders_obs(){
		$data['latest_order_obs'] = $this->mstats->get_latest_order("obs", 100);
		$data['header']=$this->template.'header';
		$data['main']=$this->template.'stats_orders_obs';
		$data['footer']=$this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
	
	/**
	 * Function used to show 1st 100 latest orders in Bestelonline.nu
	 */
	function orders_bo($isfree = null){
		if($isfree && $isfree == 'free'){
			$data['latest_order_bo_free'] = $this->mstats->get_latest_order_free(100);
			$data['main']=$this->template.'stats_orders_bo_free';
		}
		else{
			$data['latest_order_bo'] = $this->mstats->get_latest_order("bo", 100);
			$data['main']=$this->template.'stats_orders_bo';
		}
		$data['header']=$this->template.'header';
		$data['footer']=$this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
	
	/**
	 * Function used to show 1st 100 latest mail sent in the system
	 */
	function latest_mail_sent($isbo = null){
		if($isbo && $isbo == 'bo'){
			$data['latest_mails_sent_bo'] = $this->mstats->get_latest_mail_sent("bo", 100);
			$data['main']=$this->template.'stats_latest_mail_sent_bo';
		}
		else{
			$data['latest_mails_sent'] = $this->mstats->get_latest_mail_sent("obs", 100);
			$data['main']=$this->template.'stats_latest_mail_sent';
		}
		$data['header']=$this->template.'header';
		$data['footer']=$this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
	
	/**
	 * Function used to show 1st 100 latest mail sent in the system
	 */
	function top_order_company($is_last_30_days = null){
		if($is_last_30_days && $is_last_30_days == '30'){
			$data['top_order_company_last_30_days'] = $this->mstats->get_top_order_company(true, 100);
			$data['main']=$this->template.'stats_top_order_company_30_days';
		}
		else{
			$data['top_order_company'] = $this->mstats->get_top_order_company(false, 100);
			$data['main']=$this->template.'stats_top_order_company';
		}
		$data['header']=$this->template.'header';
		$data['footer']=$this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
	
	/**
	 * Function used to show list of last logins of cp's
	 */
	function last_login_company(){
		$data['last_login_companies'] = $this->mstats->get_last_login_companies(100);
		$data['header']=$this->template.'header';
		$data['main']=$this->template.'stats_last_login_company';
		$data['footer']=$this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
}
?>