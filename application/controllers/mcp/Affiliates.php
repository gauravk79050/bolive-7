<?php

	class Affiliates extends CI_Controller
	{		
		var $tempUrl = '';
		var $template = '';
		
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			
			$this->tempUrl = base_url().'application/views/mcp';
			$this->template = "/mcp";
			
			$this->load->model('mcp/MAffiliates');			
			$this->load->model('mcp/Mindex');
			
			/*if($this->session->userdata('username')||$this->session->userdata('is_logged_in'))
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
		    $this->manage_affiliates();
		}
		
		function manage_affiliates()
		{
		    $affiliates = $this->MAffiliates->get_affiliates();
			
			$start = 0;
			$limit = 20;
			
			$page = ($this->uri->segment(4))?($this->uri->segment(4)):1;
			
		    $this->load->library('pagination');

			$config['base_url'] = base_url().'/mcp/affiliates/manage_affiliates/';
			$config['total_rows'] = count($affiliates);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$config['first_link'] = _('First');
			$config['last_link'] = _('Last');
			$config['next_link'] = _('Next').' &raquo;';
			$config['prev_link'] = '&laquo; '._('Prev');
			
			$this->pagination->initialize($config);
			
			if($page)
			{
			   $start = ($page-1)*$limit;
			}
			
			$data['affiliates'] = $this->MAffiliates->get_affiliates( '', NULL, NULL, $start, $limit);
			
			$data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/affiliates';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');  
		}
		
		function add_affiliate()
		{
		    if($this->input->post('add_affiliate'))		
			{
			   $affiliate_id = $this->MAffiliates->add_affiliate($this->input->post());
			   
			   redirect('mcp/affiliates/manage_affiliates');
			}
		
		    $data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/add_affiliate';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		function edit_affiliate($affiliate_id)
		{
		    if($this->input->post('update_affiliate'))		
			{
			   $isUpd = $this->MAffiliates->update_affiliate($affiliate_id,$this->input->post());
			   
			   redirect('mcp/affiliates/manage_affiliates');
			}
			
			$affiliate = $this->MAffiliates->get_affiliates( array( 'id' => $affiliate_id ) );
			$data['affiliate'] = $affiliate[0];
			
			$data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/edit_affiliate';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		function delete_affiliate($affiliate_id)
		{
		   if(!empty($affiliate_id))
		   {
		      $isDel = $this->MAffiliates->delete_affiliate( array('id' => $affiliate_id) );
			  
			  redirect('mcp/affiliates/manage_affiliates');
		   }
		}
		
		function validate()
		{
			 //print_r($this->input->post());
			
			 $data = '';
			 
			 if($this->input->post('check') == 'email')
			 {
			    $affiliates = $this->MAffiliates->get_affiliates( array('a_email'=>$this->input->post('email')) );
				
				if(empty($affiliates))
				{
				   $data = 'notexist';
				}
				else
				{
				   $data = 'duplicate';
				}
			 }
							
			 if($this->input->post('check') == 'username')
			 {
			    $affiliates = $this->MAffiliates->get_affiliates( array('a_username'=>$this->input->post('username')) );
				
				if(empty($affiliates))
				{
				   $data = 'notexist';
				}
				else
				{
				   $data = 'duplicate';
				}
			 }
			 
			 echo json_encode(array("RESULT"=>$data));
		}
	}
?>