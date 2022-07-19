<?php 
class Mcompany_type extends CI_Model{

	function __construct(){
	
		parent::__construct();
	
	}
	function get_company_type($params = array()){
	
		if($params){
			
			foreach($params as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}
		$this->db->where(array('status' => 'ACTIVE'));
		$query=$this->db->get('company_type');
		//echo $this->db->last_query();
		//print_r($query->result());
		return ($query->result());
	
	}
	
}
?>