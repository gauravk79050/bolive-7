<?php
session_start();
class Home extends CI_Controller
{
    var $template="";
	var $tempUrl="";
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->tempUrl = base_url().'application/views/mcp';
		$this->template="/mcp";
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
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/home';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
		}
	}
}
?>