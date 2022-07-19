<?php
class Aplogin extends CI_Controller
{
	var $template = "";
	
	function __construct()
	{
		parent::__construct();
		
	    $this->template="/ap";
	    
		$this->load->model('mcp/MAffiliates');
		
	    $this->load->helper('form');
	    $this->load->helper('url');
	    $this->load->library('email');
	    $this->load->library('session');
		$this->load->library('messages');
		
	}

	function index()
	{
	    if( $this->session->userdata('ap_username') && $this->session->userdata('is_ap_user_logged_in') )
		{
		   redirect('ap/affiliate','refresh');
		}
	
	    if($this->input->post('btn_submit'))
	    {
		    $this->messages->clear();
			
			$dummy['a_username']=$this->input->post('a_username');
			$dummy['a_password']=$this->input->post('a_password');
			
			$affiliate = $this->MAffiliates->get_affiliates( $dummy );
			
			if( !empty($affiliate) ){
				
				$data=array(
				
						'ap_username'=> $affiliate[0]->a_username,
						'ap_user_id'=> $affiliate[0]->id ,	
		    			'is_ap_user_logged_in'=>true,
						'permission'=>'yes'
				);
				
				$this->session->set_userdata($data);
				 
				redirect('ap/affiliate','refresh');
			}else{
			
		    	$this->messages->add(_('This combination does not exist in our database! Please enter a valid username and password'),'error');
			}
		   
		}
		
	    $data['hide_header_menu'] = true ;
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/aplogin';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/ap_view');
	}
	
	function logout()
	{
	    $this->session->unset_userdata('ap_username');
		$this->session->unset_userdata('ap_user_id');
		$this->session->unset_userdata('is_ap_user_logged_in');
		$this->session->unset_userdata('permission');
		
        redirect('ap/aplogin','refresh');
	}		
}
?>