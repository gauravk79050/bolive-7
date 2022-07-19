<?php
	class Ads extends CI_Controller
	{
		   var $template='mcp/';
		   var $result='';
	
		   function __construct()
	  	   {
			  parent::__construct();
			  $this->load->helper('url');
			  $this->load->helper('form');
			  $this->load->model('mcp/Mads');
			  $is_logged_in = $this->session->userdata('is_logged_in');
			  
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
		   
		 
		   //--default function--//
		   function index()
		   {
		      //for the session validation
			  $data['ads']=$this->Mads->select();
	 	      $data['header']= $this->template.'header';
		      $data['main']= $this->template.'ads';
 		      $data['footer']= $this->template.'footer';
		      $this->load->vars($data);
		      $this->load->view($this->template.'mcp_view');	
		   } 
		   
		
		   function update()
		   {
		      if($this->input->post('update_emailads_text_message'))
		      {
				   $params=$this->input->post();
				   $this->Mads->update_email_messages($params['id'],'emailads_text_message',$params['emailads_text_message']);
			  }
			  
			  if($this->input->post('update_footer_text_message'))
		      {
				   $params=$this->input->post();
				   
				    /*$this->Mads->update_email_messages($params['id'],'frontend_footer_text_message',$params['frontend_footer_text_message']);*/
					 $this->Mads->update_email_messages($params['id'],'frontend_footer_copyright_link_text',$params['frontend_footer_copyright_link_text']);				   
				   $this->Mads->update_email_messages($params['id'],'frontend_footer_copyright_link_url',$params['frontend_footer_copyright_link_url']);
			  }
			  
			  redirect('mcp/ads','refresh');
			  /*
			   $data['ads']=$this->Mads->select();
			   $data['header']= $this->template.'header';
			   $data['main']= $this->template.'ads';
			   $data['footer']= $this->template.'footer';
			   $this->load->vars($data);
			   $this->load->view($this->template.'mcp_view');*/	
			  
		  }
	
	}
?>