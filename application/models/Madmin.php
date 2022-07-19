<?php 
class Madmin extends CI_model{

	function __construct(){
		parent::__construct();	
	}
	
	/*====== function to get admin =======*/
	function get_admin($params = array()){
		if($params != array()){
			foreach($raprams as $key => $val){
				$this->db->where(array($key =>$val));
			}
		}
		$this->db->select('email, admin_name');
		$result = $this->db->get('admin')->result();
		return $result;	
	}
	/*=====================================*/
}
?>