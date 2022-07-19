<?php 
class Mmail_queue extends CI_Model{

	function __construct(){
	
		parent::__construct();
	
	}
	function insert_mail($params = array()){
	
		$result = $this->db->insert('mail_queue',$params);
		return $result;
	}
	
	function get_mail($params = array()){
	
		if($params != array()){
			foreach($params as $key =>$val){
				$this->db->where($key,$val);
			}
		}
		$result = $this->db->get('mail_queue')->result();
		return $result;
	}
	
	function update_mail($where_params = array(),$params = array())
	{
		$result = array();
		if(!empty($where_params)){
			foreach($where_params as $key=>$val){
				
				$this->db->where($key,$val);
			
			}
			$result = $this->db->update('mail_queue',$params);
		}
		return $result;
	
	}
	
	function delete_mail($where_params = array())
	{
	    if(!empty($where_params))
		foreach($where_params as $key=>$val){
			
			$this->db->where($key,$val);
		
		}
		
		$result = $this->db->delete('mail_queue');
		return $result;
	}


}
?>