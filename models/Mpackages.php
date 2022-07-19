<?php 
class Mpackages extends CI_Model{
	
	var $comapny_id;
	
	function __construct(){
	
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}
	
	function get_packages( $params = array() ){
	
	    if(!empty($params))
		{
		   foreach($params as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}
	
		$packages=$this->db->get('packages')->result();
		return $packages;
	
	}
	
	function order_packages(){
		if($this->input->post('act')=='order_package'){
			$update_package=array('packages_id'=>$this->input->post('package_id'));
			$this->db->where(array('id'=>$this->company_id));
			$this->db->update('company',$update_package);
		}
	}
	
	function get_account_types( $params = array() ){
	
	    if(!empty($params))
		{
		   foreach($params as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}
	
		$account_types=$this->db->get('account_type')->result();
		return $account_types;
	
	}

}
?>