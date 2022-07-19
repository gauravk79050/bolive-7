<?php
class Rplogin extends CI_Controller
{
	var $template = "";
	
	function __construct()
	{
		parent::__construct();
		
	    $this->template="/rp";
	    
		$this->load->model('mcp/MPartners');
		
	    $this->load->helper('form');
	    $this->load->helper('url');
	    //$this->load->library('email');
	    $this->load->library('session');
		$this->load->library('messages');
		
	}

	function index()
	{
		if($this->input->is_ajax_request()){
			$array_items = array('rp_username', 'rp_user_id', 'is_rp_user_logged_in', 'permission');
			$this->session->unset_userdata($array_items);
		}
		
	    if( $this->session->userdata('rp_username') && $this->session->userdata('is_rp_user_logged_in') )
		{
		   redirect('rp/reseller','refresh');
		}

	    if($this->input->post('btn_submit'))
	    {
		    $this->messages->clear();
			
			$dummy['p_username']=$this->input->post('p_username');
			$dummy['p_password']=$this->input->post('p_password');
			
			$partner = $this->MPartners->get_partners( $dummy );
			
			if( !empty($partner) ){
				
				$data=array(
				
						'rp_username'=> $partner[0]->p_username,
						'rp_user_id'=> $partner[0]->id ,	
		    			'is_rp_user_logged_in'=>true,
						'permission'=>'yes'
				);
				$this->session->set_userdata($data);
				 
				redirect('rp/reseller','refresh');
				
			}else{
			
		    	$this->messages->add(_('This combination does not exist in our database! Please enter a valid username and password'),'error');
			}
		   
		}
		
	    $data['hide_header_menu'] = true ;
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/rplogin';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/rp_view');

	}
	
	function logout()
	{
	    $this->session->unset_userdata('rp_username');
		$this->session->unset_userdata('rp_user_id');
		$this->session->unset_userdata('is_rp_user_logged_in');
		$this->session->unset_userdata('permission');
		
        redirect('rp/rplogin','refresh');
	}		
}
?>