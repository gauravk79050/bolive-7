<?php 

class Memail_messages extends CI_model{

	function __construct(){
		parent::__construct();
	}
	
	function get_email_messages($param = array()){
		if($param != array()){
			foreach($param as $key=>$val){
				$this->db->where($key,$val);
			}
	
		}
		$result = $this->db->get('email_messages')->result();
		return $result;
	}
}
?>