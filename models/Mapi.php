<?php 
class Mapi extends CI_Model{

	function __construct(){
	
		parent::__construct();
		  $this->company_id = $this->session->userdata('cp_user_id');
	
	}
	
	function get_api(){
	
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		
		if(!isset($is_logged_in) || $is_logged_in != true){
			
			return false;
		
		}else{
			$this->db->where(array('company_id' => $this->company_id));
			$result = $this->db->get('api')->result();		
			if(!empty($result)){
				return $result;
			
			}else{
				return false;
			}
		}
	}
	
	function get_company_api($company_id=NULL)
	{
	    $this->db->where(array('company_id' => $company_id));
		$result = $this->db->get('api')->row();	
			
		if(!empty($result))		
		  return $result;
		else
		  return false;
		
	}
}
?>