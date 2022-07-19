<?php 
class Mcountry extends CI_Model{

	function __construct(){
	
		parent::__construct();
	
	}
	function get_country($id=null){
	
		if($id){
		
			$this->db->where(array('id'=>$id));
		
		}
		$query=$this->db->get('country');
	
		return $query->result();
	}

}


?>