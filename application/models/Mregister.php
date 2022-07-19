<?php
class Mregister extends CI_Model{
    
	var $template = NULL;

	function __contruct(){
	
		parent::__construct();
		
		$this->load->helper('url');
	}
	
	function register()
	{		
		$password = $this->input->post('password');
		$confirm_password = $this->input->post('confirm_password');
	
	    if($password == $confirm_password)
		{
			$register = array(
			                  'company_slug'=>$this->create_slug($this->input->post('company_name')),
							  'company_name'=>$this->input->post('company_name'),
							  'type_id'=>$this->input->post('type_id'),
							  'first_name'=>$this->input->post('first_name'),
							  'last_name'=>$this->input->post('last_name'),
							  'email'=>$this->input->post('email'),
							  'phone'=>$this->input->post('phone'),
							  'website'=>$this->input->post('website'),
							  'address'=>$this->input->post('address'),
							  'zipcode'=>$this->input->post('zipcode'),
							  'city'=>$this->input->post('city'),
							  'country_id'=>$this->input->post('country_id'),
							  'vat'=>$this->input->post('vat'),
							  'username'=>$this->input->post('username'),
							  'password'=>$password,
							  'expiry_date' => "0000-00-00",
							  'registration_date' => "0000-00-00",
							  'link' => site_url()."/Klant_login.html"
							 );
							  
			//return json_encode($register);
			
			$this->db->insert('company',$register);
			return $insert_id = $this->db->insert_id();
			
		}
		else
		{
		   $_SESSION['message'] = array('status'=>'error','response'=>'Password doesn\'t match.'); 
		   return false;
		}

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


}


?>