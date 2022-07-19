<?php
class Email_message extends CI_Controller
{
    var $template="";
	function __construct()
	{
		  parent::__construct();
		  $this->template="/mcp";
		  
		  $this->load->helper('url');
		  $this->load->helper('form');
		  $this->load->model('mcp/Mads');			  
		  $this->load->model($this->template.'/Memail_message');
		  
		  //--for the session authentication--//
		  /*$this->load->model($this->template.'/Mindex');
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
	      if($this->input->post('company_approval_message') && $this->input->post('company_disapproval_message'))
		  {
			   $params=$this->input->post();
			   
			   $this->Mads->update_email_messages($params['id'],'company_approval_subject',$params['company_approval_subject']);
			   $this->Mads->update_email_messages($params['id'],'company_approval_message',$params['company_approval_message']);
		      
			   
			   $this->Mads->update_email_messages($params['id'],'company_disapproval_subject',$params['company_disapproval_subject']);
			   $this->Mads->update_email_messages($params['id'],'company_disapproval_message',$params['company_disapproval_message']);
			   
			   /*$this->Mads->update_email_messages($params['id'],'company_approval_subject_free',$params['company_approval_subject_free']);
			   $this->Mads->update_email_messages($params['id'],'company_approval_message_free',$params['company_approval_message_free']);
			   
			   $this->Mads->update_email_messages($params['id'],'company_approval_subject_basic',$params['company_approval_subject_basic']);
			   $this->Mads->update_email_messages($params['id'],'company_approval_message_basic',$params['company_approval_message_basic']);
			   
			   $this->Mads->update_email_messages($params['id'],'company_approval_subject_pro',$params['company_approval_subject_pro']);
			   $this->Mads->update_email_messages($params['id'],'company_approval_message_pro',$params['company_approval_message_pro']);*/
			   
			   $this->Mads->update_email_messages($params['id'],'company_trial_subject_basic_pro',$params['company_trial_subject_basic_pro']);
			   $this->Mads->update_email_messages($params['id'],'company_trial_message_basic_pro',$params['company_trial_message_basic_pro']);
			   
			   //$this->Mads->update_email_messages($params['id'],'order_online_subject_free',$params['order_online_subject_free']);
			   //$this->Mads->update_email_messages($params['id'],'order_online_message_free',$params['order_online_message_free']);
			   
			   $this->Mads->update_email_messages($params['id'],'company_trial_subject_basic_pro_mcp',$params['company_trial_subject_basic_pro_mcp']);
			   $this->Mads->update_email_messages($params['id'],'company_trial_message_basic_pro_mcp',$params['company_trial_message_basic_pro_mcp']);
		  }
		  
		  /*if($this->input->post('company_disapproval_message'))
		  {
			   $params=$this->input->post();
			   $this->Mads->update_email_messages($params['id'],'company_disapproval_message',$params['company_disapproval_message']);
		  }*/
		
		//$data['content']=$this->Memail_message->select();
		$data['content']=$this->Mads->select();
		
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/email_message';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
	}
}
?>
