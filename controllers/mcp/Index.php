<?php
session_start();
class Index extends CI_Controller
{
    var $template="";
	function __construct()
	{
	 parent::__construct();
	 $this->template="/mcp";
	 $this->load->model($this->template.'/Mindex');
	 $this->load->helper('form');
	 $this->load->helper('url');
	 $this->load->helper('phpmailer');
	$this->load->library('email');
	
	}
	function index($msg=NULL)
	{
		if(!isset($_SESSION['username']))
		{
			$data['hide_header_menu'] = true;
			$data['header']=$this->template.'/header';
			$data['main']=$this->template.'/index';
			$data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
			$this->load->library('form_validation');
		}	
		else
		{	
			if($msg == 'invalid')
			{
				$data['message']=_('Invalid Username and Password');
			}
			else if($msg == 'logout')
			{
				unset($_SESSION['username']);
				unset($_SESSION['password']);
				$data['message']=_('You are successfully logout...!!');
			}	
			$data['hide_header_menu'] = true;
			$data['header']=$this->template.'/header';
			$data['main']=$this->template.'/index';
			$data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
	}
	/* match the username and password of admin*/
	
	function match()
	{
		if($this->input->post('btn_submit'))
		{
		$dummy['login_username']=$this->input->post('login_username');
		$dummy['login_password']=$this->input->post('login_password');
		$_SESSION['username']=$this->input->post('login_username');
		$_SESSION['password']=$this->input->post('login_password');
		$result=$this->Mindex->match($dummy);
		if($result!="")
			{
			
			redirect('mcp/home','refresh');
			}
			else
			{
		    redirect('mcp/index/index/invalid','refresh');
			}
		}
	}
	
	function forgot_password()
	{   
	
		if($this->input->post('reset_password'))
	    { 
		    // Set email preferences
        	/*$this->email->from('info@onlinebestelsysteem.net', 'OBS');
            $this->email->to($this->input->post('email'));
            $this->email->subject('password');*/
			$information=$this->Mindex->valid_email($this->input->post('email'));
			/* foreach($information as $inform){
            	$this->email->message('username=>'.$inform->login_username."  ".'password=>'.$inform->login_password);
			} */
			$inform = $information[0];
			$query = send_email($this->input->post('email'),$this->config->item('no_reply_email'), 'password', 'username=>'.$inform->login_username."  ".'password=>'.$inform->login_password, NULL, NULL, NULL, 'no_reply', 'site_admin', 'forget_password');
			
            if (!$query) {
            	echo _('Failed to send email');
            }
            else {
            	echo _('Success to send email');
		    }

		    $data['header']=$this->template.'/header';
		    $data['main']=$this->template.'/index';
		    $data['footer']=$this->template.'/footer';
		    $this->load->vars($data);
		    $this->load->view($this->template.'/mcp_view');
		}
		else
		{
			$data['header']=$this->template.'/header';
		    $data['main']=$this->template.'/forgot_password';
		    $data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
	   	}
	}
	
	
	function valid_email()
	{
    	if($this->input->post('email'))
	    {
	     
	      $valid_email=$this->Mindex->valid_email($this->input->post('email'));
	     if($valid_email)
	     echo 'valid';
	     else
	     echo 'invalid';
	  }
	}
 
}

?>