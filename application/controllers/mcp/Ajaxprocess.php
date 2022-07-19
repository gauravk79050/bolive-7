<?php
class Ajaxprocess extends CI_Controller
{		
	var $tempUrl = '';
	var $template = '';

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		
		//$this->load->library('form_validation'); 
		$this->tempUrl = base_url().'application/views/mcp';
		$this->template = "/mcp";
		
		$this->load->model('mcp/Mcompanies');
		$this->load->model('mcp/Mpackage');
		$this->load->model('mcp/Mcountry');
		$this->load->model('mcp/Mcompany_type');
		$this->load->model('Madmin');
		$this->temp="/mcp/companies";
		
		$this->mail_template = 'mail_templates/';
     }
	
	 function index()
	 {
	 }
	
	 function ajax_get_countries()
	 {
		$countries = $this->Mcountry->select();
		echo json_encode($countries);
	 }
	 
	 function ajax_get_company_types()
	 {
	 	$company_types = $this->Mcompany_type->select(array('status'=>'ACTIVE'));
		echo json_encode($company_types);
	 }
	 
	 function ajax_get_packages()
	 {
		$packages = $this->Mpackage->select();
		echo json_encode($packages);
	 }
	 
	 function ajax_check_email()
	 {
	    $company = $this->Mcompanies->get_company(array( 'email'=>$this->input->post('email') ));
		if(!empty($company))
		{
		  $response = array('error'=>_('Please enter a different email. Already in use !'));
		  //echo _('Please enter a different email. Already in use !');
		}
		else
		{
		  $response = array('success'=>true);
		  //echo true;
		}
		  
		echo json_encode($response);
	 }
	 
	 function ajax_check_username()
	 {
	    $company = $this->Mcompanies->get_company(array( 'username'=>$this->input->post('username') ));
		if(!empty($company))
		  $response = array('error'=>_('Please enter a different username. Already registered !'));
		else
		  $response = array('success'=>true);
		
		echo json_encode($response);
	 }
	 
	 function ajax_register_company()
	 {
	    $a = array();
		
		$a['company_slug']=$this->create_slug($this->input->post('company_name'));
		$a['company_name']=$this->input->post('company_name');
		$a['type_id']=$this->input->post('type_id');
		$a['first_name']=$this->input->post('first_name');
		$a['last_name']=$this->input->post('last_name');
		$a['email']=$this->input->post('email');
		$a['phone']=$this->input->post('phone');
		$a['address']=$this->input->post('address');
		$a['admin_remarks']='';
		$a['city']=$this->input->post('city');
		$a['country_id']=$this->input->post('country_id');
		$a['username']=$this->input->post('username');
		$a['password']=$this->input->post('password');
		$a['packages_id'] = '';
		
		if($this->input->post('have_website'))
		{
		   $a['have_website'] = 1;
		}
		else
		{
		   $a['have_website'] = 0;
		   $a['package'] = $this->input->post('package');
		   $a['domain'] = $this->input->post('domain');
		   $a['canregister'] = $this->input->post('canregister');
		}
		
		if(!isset($a['domain']) || $a['domain'] == '')
		{
		   $a['website']=$this->input->post('website');
		}
		
		$a['email_ads'] = 0;
		$a['footer_text'] = 0;
		$a['registration_date'] = $registration_date = date('Y-m-d',time());				
		
		$date = $registration_date;					

		$expiry_date = '';
		if($this->input->post('5year_subscription'))
		{  
		   $a['5year_subscription'] = '1'; 
		   $newdate = strtotime ( '+5 year' , strtotime ( $date ) ) ;
		}
		else
		{  
		   $a['5year_subscription'] = '0'; 
		   $newdate = strtotime ( '+1 year' , strtotime ( $date ) ) ;
		}	
		
		$expiry_date = date ( 'Y-m-d' , $newdate );
		
		$a['expiry_date'] = $expiry_date; //$this->input->post('expiry_date');
		$a['earnings_year'] = '';
		$a['zipcode'] = $this->input->post('zipcode');
		$a['parent_id'] = 0;
		
		if($this->input->post('role'))
		{
		   $a['role'] = 'super';
		}
		elseif($a['parent_id']!=0)
		{
		   $a['role'] = 'sub';
		}
		else
		{
		   $a['role'] = 'master';
		}
		
		$company_id = $this->Mcompanies->insert($a);
		
		if($company_id)
		{
		    $response = array('error'=>0,'message'=>_('Success ! Company registered successfully.'));
		   
		    // Sending Mail to admin
			
			$admin = $this->Madmin->get_admin();
			
			if(!empty($admin))
			  $admin_email = $admin[0]->email;
		    
			if($admin_email)
		    {
				$this->load->library('utilities');
				$this->load->library('parser');
				
				$this->load->helper('email');
				  
				$To = $admin_email; //'keertirastogi@cedcoss.com'; //admin_email
				$insert_id = $company_id;
				
				if($insert_id && valid_email($To) )
				{			   
				   
				   $this->load->helper('phpmailer');
				   $From = $this->config->item("site_admin_email"); 
				   $mailFrom = $this->config->item("site_admin_name");
				   
				   $subject = _('New Company Registered');
		
				   $register = $this->input->post();
				   $register['insert_id'] = $insert_id;
				   $register['REQ_IP_ADD'] = $this->input->post('REQ_IP_ADD');
		
					$mail_body = $this->load->view($this->mail_template.'new_company_registered', $register, true);
					
					send_email($To,$From,$subject,$mail_body,$mailFrom,NULL,NULL,'site_admin','company','new_company_registered');
					//echo $this->email->print_debugger();
					
					$response = array('error'=>0,'message'=>_('You details are been saved successfully. And a mail has been sent to the administrator for your approval. So please wait, he will get back to you soon !'));
		 		} 
		    }
		}
		else
		{
		  $response = array('error'=>1,'message'=>_('Sorry ! Some error occurred.'));
		}
		  
	    echo json_encode($response);
		exit;
	 }
	 
/*===================create slug======================*/

	function create_slug($companyname){
		$slug_str = strtolower(trim($companyname));
		$slug_str = preg_replace("/[^a-z0-9-]/", "-", $slug_str);
		$slug_str = preg_replace("/-+/", "-", $slug_str);
		$slug_str = rtrim($slug_str, "-");
		
		
		$company_slugs_array = $this->db->select('company_slug')->get('company')->result();
		$company_slugs = array();
		foreach($company_slugs_array as $company_slug){
			$company_slugs[] = $company_slug->company_slug;
		}
		$old_str = $slug_str;
		for($company_counter=2;;$company_counter++){
			if(in_array($slug_str,$company_slugs)){
			  $slug_str = $old_str.'-'.$company_counter;
			}else{
			  break;
			 }
		
		}
        return $slug_str;
    }
	 
	 
	 
	 
	 /*function ajax_login()
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
					'cp_is_logged_in' => true
				);
				$this->session->set_userdata($data);
				
				print_r($this->session->all_userdata());
				
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
	 }*/
}