<?php 
class Mpayment extends CI_Model{

	function __construct(){
	
		parent::__construct();
	
	}
	
	function get_available_payment_methods($all=null){
		
		if(!empty($all)){
			
			$query = $this->db->get_where('available_payment_method');
		}else{
			$query = $this->db->get_where('available_payment_method',array('available'=>1));
		}
		
		$result = $query->result_array();
		return $result;
		
	}	
	
	
	function update_availabilty($selected){
	
		$ids = array();
		$all_id = $this->Mpayment->get_payment_method_ids();
		foreach($all_id as $value){
				$ids[] = $value['id'];
		}
		
		$remaining_id = array_diff($ids, $selected);
		
		/* $selected_id = $this->Mpayment->prepare_for_db($selected);
		$not_selected_id = $this->Mpayment->prepare_for_db($remaining_id); */
		
		if(!empty($selected)){
			$this->db->where_in('id', array_values($selected));
			$this->db->update('available_payment_method',array('available'=>1));
			//echo '<br/>'.$this->db->last_query();
		}
		
		
		if(!empty($remaining_id)){
			
			$this->db->where_in('id', array_values($remaining_id));
			$this->db->update('available_payment_method',array('available'=>0));
		}
		
		//echo '<br/>'.$this->db->last_query();
	//	die("//");

	}
	
	function get_payment_method_ids(){
	
		$this->db->select('id');
		$result = $this->db->get('available_payment_method')->result_array();
		return $result;
	}
	
	
	function prepare_for_db($selected){
		
	/*	$string = '';
		foreach($selected as $key=>$value) { 
			$string .= $value.','; 
		}
		return $string = rtrim($string, ','); */
		
	}
}