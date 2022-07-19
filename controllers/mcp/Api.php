<?php

	class Api extends CI_Controller
	{		
		var $tempUrl = '';
		var $template = '';
		
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			
			//$this->load->library('form_validation'); 
			$this->tempUrl = base_url().'application/views/mcp';
			$this->template = "/mcp";
			$this->load->model('mcp/Mcompanies');
			$this->load->model('mcp/Mpackage');

			$this->temp="/mcp/companies";
			
			//--for the session authentication--//
			/*$this->load->model('mcp/Mindex');
			if($this->session->userdata('username')||$this->session->userdata('is_logged_in'))
			{
			   $current_session_id=$this->session->userdata('session_id');//--getting current session id--//
			   $current_user=$this->session->userdata('username');//--getting current session user--//
			   
			   if(!$this->Mindex->session_validate($current_session_id,$current_user))
				 redirect('mcp/mcplogin','refresh');
			}
			else
			   redirect('mcp/mcplogin','refresh');*/
			   
			$current_user = $this->session->userdata('username');
			$is_logged_in = $this->session->userdata('is_logged_in');
			
			if( !$current_user || !$is_logged_in )
			  redirect('mcp/mcplogin','refresh');
		}
		
		function index()
		{
			if($this->input->post('btn_search'))		
			{
			   $data['content']=$this->Mcompanies->search_approved_company_api($this->input->post());
			}
			else
			{
			   $data['content'] = $this->Mcompanies->get_approved_company_api();
			}
			
			/*$this->load->library('pagination');

			$config['base_url'] = base_url().'/api/page/';
			$config['total_rows'] = count($data['content']);
			$config['per_page'] = 10;
			
			$this->pagination->initialize($config);
			
			echo $this->pagination->create_links();*/
			
			$data['tempUrl']=$this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/api_manager';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view'); 
		}
		
		function generate_api($company_id)
		{
		    $response = $this->Mcompanies->generateApi($company_id);
			
			if($response)
			{
			    $msg = array('success' => _('Success - API created successfully !'));
			}else{
				$msg = array('error'=> _('Error - Some error occurred !'));
			}
			
			redirect('mcp/api','refresh');
		}
	}
?>