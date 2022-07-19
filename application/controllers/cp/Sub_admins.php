<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class sub_admins extends CI_Controller{
	
	var $rows_per_page = '';
	var $company_id = '';
	var $ibsoft_active = false;
	
	function __construct(){
	
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->helper('phpmailer');
		$this->load->helper('cookie');
		
		$this->load->library('ftp');
		$this->load->library('Messages');
		$this->load->library('session');
        $this->load->library('messages'); 
		$this->load->library('utilities');
		$this->load->library('pagination');	
		$this->load->library('form_validation');
		//$this->load->library('upload');
		
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mpackages');
		$this->load->model('Mcategories');
		$this->load->model('Mcalender');
		$this->load->model('Mclients');
		$this->load->model('mcp/Mcompanies');
		$this->load->model('M_fooddesk');
		
		if($this->input->get('token')){
			$privateKey = 'o!s$_7e(g5xx_p(thsj*$y&27qxgfl(ifzn!a63_25*-s)*sv8';
			$userId = $this->input->get('name');
			$hash = $this->createToken($privateKey, current_url(), $userId);
			
			if($this->input->get('token') == $hash){
				$this->product_validate($userId);
			}
		}
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}
		
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		/*if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}*/
		$this->load->model('MFtp_settings');
		$this->rows_per_page = 20;
		
		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}

	function index(){
		$this->lijst();
	}

	function lijst()
	{
	    if( $this->company_role == 'super' )
		{
		   if($this->input->post('act') == 'edit_access_code')
		   {
		      $result = $this->Mgeneral_settings->update_general_settings();
		   }
		   	   
	  	   $data['sub_companies'] = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );
		   
		   $companies = $this->Mcompany->get_company( array( 'id' => $this->company_id ) );
		   
		   if(!empty($companies))
		     $data['super_admin_login_code'] = $companies[0]->access_super;
		   else
		     $data['super_admin_login_code'] = '';
			 
		   $data['content'] = 'cp/sub_admins_new';
		   $this->load->view('cp/cp_view',$data);
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
		
	}


}