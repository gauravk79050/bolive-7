<?php
class Competitor extends CI_Controller
{
	var $template='mcp/';
	var $search='';
	function __construct()
	{

		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->template = 'mcp/';
		$this->load->model('mcp/Mcompetitor');
			
		//--for the session authentication--//
		/*$this->load->model($this->template.'Mindex');
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
		if($this->input->post('search'))
		{
			$this->search = $this->input->post();
			$data['competitor']=$this->Mcompetitor->select($this->search);
		}
		else
		{
			$data['competitor']=$this->Mcompetitor->select();
		}
			
		$data['header']= $this->template.'header';
		$data['main']= $this->template.'competitor';
		$data['footer']= $this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	
	}
	function delete()
	{
	
		$id= $this->uri->segment(4);
		$this->Mcompetitor->delete($id);
		redirect($this->template.'competitor', 'location');
	}
	
	
	//--function to add value--//
	function add()
	{
		if($this->input->post())
		{
			$url=$this->input->post('competitor_url');
			
			
			if(!filter_var($url, FILTER_VALIDATE_URL,FILTER_FLAG_HOST_REQUIRED))
			{
				$data['host']='1';
			}
			else
			{
				$this->Mcompetitor->add($this->input->post('competitor_url'));
				redirect($this->template.'competitor', 'location');
			}
			
			
		}
	
		$data['header']= $this->template.'header';
		$data['main']= $this->template.'competitor_add';
		$data['footer']= $this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
	}
	
	
	//--function to update  value--//
	function update()
	{
	
		$this->load->model($this->template.'Mindex');
		$data['id']= $this->uri->segment(4);
		/*if($this->session_validate())
		 {*/
		if($this->input->post())
		{
			if($this->input->post('update'))
			{
				
				$url=$this->input->post('competitor_url');
					
					
				if(!filter_var($url, FILTER_VALIDATE_URL,FILTER_FLAG_HOST_REQUIRED))
				{
					
					$data['host']='1';
					$data['id']= $this->input->post('competitor_id');
					
				}
				else
				{
					
					$this->Mcompetitor->update($this->input->post('competitor_id'),$this->input->post('competitor_url'));
					redirect('mcp/competitor','location');
					//redirect($this->template.'competitor', 'location');
				}
			}
				//$this->Mcompetitor->update($this->input->post('competitor_id'),$this->input->post('competitor_url'));
			else if($this->input->post('delete'))
			{
				$this->Mcompetitor->delete($this->input->post('competitor_id'));
			    redirect('mcp/competitor','location');
			}
		}
		
		$data['competitor_url']=$this->uri->segment(5);
		$data['header']= $this->template.'header';
		$data['main']= $this->template.'competitor_edit';
		$data['footer']= $this->template.'footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
		/*}*/
	}
	
} 
?>