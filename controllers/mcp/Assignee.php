<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Assignee extends CI_Controller
	{		
		var $tempUrl = '';
		var $template = '';
		
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			
			$this->tempUrl = base_url().'application/views/mcp';
			$this->template = "/mcp";
			
			$this->load->model('mcp/Mcompanies');			
			/*$this->load->model('mcp/Mindex');
			
			if($this->session->userdata('username')||$this->session->userdata('is_logged_in'))
			{
			   $current_session_id=$this->session->userdata('session_id');//--getting current session id--//
			   $current_user=$this->session->userdata('username');//--getting current session user--//
			   
			   if(!$this->Mindex->session_validate($current_session_id,$current_user))
				 redirect('mcp/mcplogin','refresh');
			}
			else
			   redirect('mcp/mcplogin','refresh');	*/		
			   
			$current_user = $this->session->userdata('username');
		    $is_logged_in = $this->session->userdata('is_logged_in');
			
		    if( !$current_user || !$is_logged_in )
			  redirect('mcp/mcplogin','refresh');
		}
		
		function index() {	
		    $this->manage_assignee();
		}
		
		function manage_assignee() {
			$assignee = $this->Mcompanies->get_assignee();
			$start = 0;
			$limit = 20;
			
			$page = ( $this->uri->segment(4) ) ? ( $this->uri->segment(4) ) : 1;
			
		    $this->load->library('pagination');

			$config['base_url'] = base_url().'/mcp/assignee/manage_assignee/';
			$config['total_rows'] = count($assignee);
			$config['per_page'] = $limit;
			$config['use_page_numbers'] = TRUE;
			$config['first_link'] = _('First');
			$config['last_link'] = _('Last');
			$config['next_link'] = _('Next').' &raquo;';
			$config['prev_link'] = '&laquo; '._('Prev');
			$config['num_links'] = 2;
			
			$this->pagination->initialize($config);
			
			if( $page ) {
			   $start = ( $page - 1 ) * $limit;
			}
			
			$data['assignee'] = $this->Mcompanies->get_assignee( $start, $limit );
			
			$data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/assignee';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
	}
?>