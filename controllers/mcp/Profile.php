<?php

class Profile extends CI_Controller
{	
	var $tempUrl="";
	var $template="";
	function __construct()
	{
	   	parent::__construct();
		$this->load->helper('url');
		 $this->load->helper('form');
		$this->tempUrl = base_url().'application/views/mcp';
		$this->template="/mcp";
		$this->load->model($this->template.'/Mprofile');
		$this->load->model($this->template.'/Mindex');
		 //--for the session authentication--//
			
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
	function index($msg=NULL)
	{ 
		$data['tempUrl'] = $this->tempUrl;
		$this->load->library('form_validation');
		if($msg == 'update')
			{
				$data['message']=_('UPDATE SUCCESSFULLY....!!!');
			}
			$data['content']=$this->Mprofile->select();
			$data['header']=$this->template.'/header';
			$data['main']=$this->template.'/profile';
			$data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
			
	}
	/* Update the admin information */
	function update()
	{
		
		$data['tempUrl'] = $this->tempUrl;
		$this->load->library('form_validation');
			if($this->input->post('btn_update'))
			{  
				$this->form_validation->set_rules('login_username', 'login_username', 'required');	
				$this->form_validation->set_rules('login_password', 'login_password', 'required');	
				$this->form_validation->set_rules('email', 'email', 'required|valid_email');	 
				if($this->form_validation->run()==FALSE)
				{
					$data['content']=$this->Mprofile->select();
					$data['header']=$this->template.'/header';
					$data['main']=$this->template.'/profile';
					$data['footer']=$this->template.'/footer';
					$this->load->vars($data);
					$this->load->view($this->template.'/mcp_view');
				}
				else
				{
					$a = 0;
					$dummy['login_username'] = $this->input->post('login_username');
					$dummy['login_password'] = $this->input->post('login_password');
					$dummy['email'] = $this->input->post('email');
					$dummy['admin_name'] = $this->input->post('admin_name');
					//$a=$this->Mprofile->update($dummy);
					
					if($a==1)
					{ 
					redirect('mcp/profile/index/update','refresh');
					}
				}
			}
		}
	}
?>