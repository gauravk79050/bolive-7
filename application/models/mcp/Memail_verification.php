<?php

	class Memail_verification extends CI_model
	{		
		
		function __construct()
		{
			parent::__construct();
			
		}
		
		function get_company_infos(){
			$this->db->select('company.id, company.company_name, company.first_name, company.last_name, company.email, company.website, company.ac_type_id, company.username, company.password');
			$this->db->join('email_verification','email_verification.email = company.email');
			$this->db->group_by('email_verification.email');
			return $this->db->get('company')->result();
		}
		
		function change_email($new_email = null, $company_id = null){
			if($new_email && $company_id){
				$this->db->where('id',$company_id);
				$result = $this->db->update('company', array('email' =>  $new_email));
				
				$this->db->where('email',$new_email);
				$result = $this->db->update('email_verification', array('email' =>  $new_email));
				
				return $result;
			}else{
				return false;
			}
		}
		
		function delete($email = null){
			return $this->db->delete('email_verification', array('email' => $email));
		}
		
		function get_email($company_id = null){
			if($company_id){
				$this->db->select('email');
				$result = $this->db->get_where('company', array('id' => $company_id))->result();
				if(!empty($result))
					return $result[0]->email;
				else
					return false;
			}else{
				return false;
			}
		}
		
		function get_client_infos(){
			return array();
		}
	}
?>