<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ajaxprocess extends CI_Controller
{		
	var $tempUrl = '';
	var $template = '';

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		
		$this->load->model('mcp/Mcompanies');

     }
	
	 function index()
	 {
	 }
	 
  	 function ajax_login()
	 {		
		$username=$this->input->post('l_username');
		$password=$this->input->post('l_password');
		
		if($username && $password)
		{
		  $company = $this->Mcompanies->get_company(array( 'username'=>$username, 'password'=>$password ));
		  
		  if(!empty($company) && $company[0]->id)
		  {
		        $company = $company[0];
		        $data = array(
					'cp_user_id' => $company->id,
					'cp_username' => $company->username,
					'cp_user_role' => $company->role,
					'cp_user_parent_id' => $company->parent_id,
					'cp_is_logged_in' => true/*,
					'ip_address' => $this->input->post('ip_address')*/
				);
				$this->session->set_userdata($data);
								
				//print_r($this->session->all_userdata());
				
				if( $company->role == 'master' || $company->role == 'super' )
				{
				  $response = array('error'=>0,'message'=>_('Login Successful !'),'redirect_to'=>base_url('').'cp/cdashboard');
				}
				elseif( $company->role == 'sub' )
				{
				  $response = array('error'=>0,'message'=>_('Login Successful !'),'redirect_to'=>base_url('').'cp/cdashboard/orders');		        }
		  }
		  else
		  {
		     $response = array('error'=>1,'message'=>_('Invalid username or password. Please try again!'));
		  }
		}
		else
		{
		   $response = array('error'=>1,'message'=>_('Please enter your correct username and password.'));
		}
		
		echo json_encode($response);
		exit;		
	 }
}
?>