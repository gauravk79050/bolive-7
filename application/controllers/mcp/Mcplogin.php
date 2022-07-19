<?php
class Mcplogin extends CI_Controller
{
	var $template="";
	
	function __construct()
	{
		parent::__construct();
	    $this->template = "/mcp";
	    	    
	    ini_set('session.gc_maxlifetime', 3600); // server should keep session data for AT LEAST 1 hour	    
	    session_set_cookie_params(3600); // each client should remember their session id for EXACTLY 1 hour
	    //session_start();
		
	    $this->load->model($this->template.'/Mindex');
	    $this->load->helper('form');
	    $this->load->helper('url');
	    $this->load->helper('phpmailer');
	    $this->load->library('email');
	    //$this->load->library('session');
		$this->load->library('messages');
	}
    
	//--default function--//
	function index()
	{ 
			if( $this->session->userdata('username') || $this->session->userdata('is_logged_in') )
			{
				// $current_session_id = $this->session->userdata('session_id');//--getting current session id--//
		  //       $current_user = $this->session->userdata('username');//--getting current session user--//
				
			 //  	if(!$this->Mindex->session_validate($current_session_id,$current_user))
				// {
				// 	$data['header']=$this->template.'/header';
			 //        $data['main']=$this->template.'/index';
			 //        $data['footer']=$this->template.'/footer';
			 //        $this->load->vars($data);
			 //        $this->load->view($this->template.'/mcp_view');
				// }else{
					 redirect( base_url().'mcp/companies','refresh' );
				//}
				   
			} else{
			    $data['hide_header_menu']=true;
				$data['header']=$this->template.'/header';
				$data['main']=$this->template.'/index';
			    $data['footer']=$this->template.'/footer';
			    $this->load->vars($data);
			    $this->load->view($this->template.'/mcp_view');
			}
	}
	/* match the username and password of admin*/
		
	function match(){
		
		if($this->input->post('btn_submit')){
			$this->messages->clear();
			$dummy['login_username']=$this->input->post('login_username');
			$dummy['login_password']=$this->input->post('login_password');
			$result=$this->Mindex->match($dummy);
			if($result){
				/*UPDATE CURRENT IP ADDRESS IN DB*/
				$this->Mindex->update_admin_ip($_SERVER['REMOTE_ADDR']);
				
				/*$data=array(
						'username'=>$this->input->post('login_username'),	
		    			'is_logged_in'=>true,'permission'=>'yes');				
				 $this->session->set_userdata($data);*/
				 
				 $this->session->set_userdata( 'username', $this->input->post('login_username') );
				 $this->session->set_userdata( 'is_logged_in', true );
				 $this->session->set_userdata( 'permission', 'yes' );
				 $this->session->set_userdata( 'admin_role', (isset($result[0]->admin_role)?$result[0]->admin_role:''));
				 $this->session->set_userdata( 'admin_id', (isset($result[0]->id)?$result[0]->id:''));
				 
				 /* ##################### FORUM INTEGRATION CODE: STARTS ###################### */
				 
				 //check if the user detail is present in the sforum_users table..
				 $_SESSION['username'] = $this->session->userdata('username');
				 $qry_sforum = $this->db->query('SELECT * FROM `sforum_users` WHERE `username` = "'.$this->input->post('login_username').'"');
				 $res_sforum = $qry_sforum->result_array();
			
				 if(is_array($res_sforum) && !empty($res_sforum)){
				 		
    
				 	$_SESSION['help_logged_in'] = true;
				 	
				 	$_SESSION['forum_admin_logged_in'] = true;
				 	$_SESSION['sforum_logged_in'] = true;
				 	$_SESSION['sforum_user_id'] = $res_sforum[0]['id'];
				 	$_SESSION['sforum_user_role'] = 'admin';
				 	$_SESSION['sforum_user_username'] = $this->input->post('login_username');
				 		
				 		
				 } else {
    
				 	$param_sforum = array("username" => $this->input->post('login_username'), 'role' => 'admin');
				 		
				 	$this->db->insert('sforum_users',$param_sforum);
				 		
				 	$sfroum_id = $this->db->insert_id();
    
				 	$_SESSION['help_logged_in'] = true;
				 		
				 	$_SESSION['forum_admin_logged_in'] = true;
				 	$_SESSION['sforum_logged_in'] = true;
				 	$_SESSION['sforum_user_id'] = $sfroum_id;
				 	$_SESSION['sforum_user_role'] = 'admin';
				 	$_SESSION['sforum_user_username'] = $this->input->post('login_username');
				 }
				 
				 /* ##################### FORUM INTEGRATION CODE: ENDS ###################### */
				 
				 redirect('mcp/companies','refresh');
				 
			}else{
		    	
				$this->messages->add(_('This combination does not exist in our database!Please enter a valid username and password'),'error');
				redirect('mcp/mcplogin','refresh');
			}
		}
	}
	function forgot_password(){   
		if($this->input->post('reset_password')){ 
			$this->messages->clear();
			// Set email preferences
			$information = $this->Mindex->valid_email($this->input->post('email'));
			if($information && !empty($information)){
             	$parse_email_array = array('username' =>$information[0]->login_username,
				 							'password' =>$information[0]->login_password,
											'email' =>$information[0]->email
										);
				$mail_template = $this->load->view('mail_templates/admin_forgot_password_mail_template',$parse_email_array,true);
				$query = send_email($this->input->post('email'), $this->config->item('no_reply_email'), 'Forgot Password', $mail_template, 'OBS Administrator', NULL, NULL, 'no_reply', 'site_admin', 'forget_password');
				if (!$query) {//sending email
					$this->messages->add(_('can not send mail.!!please try again'),'error');// Show error notification
					show_error($this->email->print_debugger());
           	 	}else {
					$this->messages->add(_('mail has been send successfully to your mail id'),'success');// Show success notification

		     	}
			}else{
				$this->messages->add(_('this email id does not exists in our database'),'error');// Show error notification
			}
			redirect('mcp/mcplogin/forgot_password');		 
		}else{
		    $data['hide_header_menu']=true;
			$data['header']=$this->template.'/header';
	        $data['main']=$this->template.'/forgot_password';
	        $data['footer']=$this->template.'/footer';
		    $this->load->vars($data);
		    $this->load->view($this->template.'/mcp_view');
	   }
	}//--end of forgot password--//
	
	function valid_email(){
		if($this->input->post('email')){
			$valid_email=$this->Mindex->valid_email($this->input->post('email'));
	        if($valid_email){
				  echo 'valid';
	    	 }else{
			 	echo 'invalid';
	         }
	    }
	}
	
	function logout()
	{
		/*REMOVE CURRENT IP ADDRESS IN DB*/
		$this->Mindex->update_admin_ip();
		
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('is_logged_in');
		$this->session->unset_userdata(array('username'=>'','is_logged_in'=>''));
		
		//echo base_url().'mcp/mcplogin';
		unset($_SESSION['help_logged_in']);
		unset($_SESSION['forum_admin_logged_in']);
		unset($_SESSION['sforum_logged_in']);
		unset($_SESSION['sforum_user_id']);
		unset($_SESSION['sforum_user_role']);
		unset($_SESSION['sforum_user_username']);
		unset($_SESSION['username']);
		
        redirect( base_url().'mcp/mcplogin', 'refresh');
	}  

	function login_fdd2_via_mcp() {
		$comp_id = $this->input->post( 'comp_id' );
		if( $comp_id ) {
			$ran_str = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
			$unique_arr = array(
							'company_id' 	=> $comp_id,
							'unique_str' 	=> $ran_str,
							'datetime' 		=> date("Y-m-d H:i:s")
						);
			$this->db->insert('login_via_newobs',$unique_arr);
			echo $ran_str;
		} else {
			return false;
		}
	} 

	function login_tv_via_mcp() {
		$comp_id = $this->input->post( 'comp_id' );
		if( $comp_id ) {
			$ran_str = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
			$unique_arr = array(
							'company_id' 	=> $comp_id,
							'unique_str' 	=> $ran_str,
							'datetime' 		=> date("Y-m-d H:i:s")
						);
			
			$this->db->insert('login_via_newobs',$unique_arr);
			echo $ran_str;
		} else {
			return false;
		}
	} 
  
}

?>