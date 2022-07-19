<?php
class Morder_settings extends CI_Model{
	var $company_id="";
	function __construct(){
	
		parent::__construct();
		
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}

	function get_order_settings($param=array(), $select = ''){
		
		if($select != '')
			$this->db->select($select);
		
		if($param){
			foreach($param as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}else{
			$this->db->where(array('company_id'=>$this->company_id));
		}
	
		$query=$this->db->get('order_settings');
		return ($query->result());
	}
	
	function get_custom_order_settings($param=array(), $select = ''){
	
		if($select != '')
			$this->db->select($select);
	
		if($param){
			foreach($param as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}else{
			$this->db->where(array('company_id'=>$this->company_id));
		}
	
		$query=$this->db->get('custom_order_settings');
		return ($query->result());
	}
	
	
	
	/**
	 * This model function is used to update advance payment settings
	 * @param array $update_order_settings Its the array of col and values
	 * @return array $return_data Its the array of error/success message
	 */
	function update_order_settings($update_order_settings = array()){
	
		$return_data =  array('error' => _('error occured while updating order settings!') );
		
		if(!empty($update_order_settings)){
			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('order_settings',$update_order_settings);
			if($result){
				$return_data =  array('success' => _('order settings has been updated successfully.') );
			}
		}
		
		return $return_data;
	}
	
	
	function update_custom_order_settings($update_custom_order_settings = array(),$upd_empty = 0){
	
		$return_data =  array('error' => _('error occured while updating order settings!') );
	
		if(!empty($update_custom_order_settings)){
			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('custom_order_settings',$update_custom_order_settings);
			if($result){
				$return_data =  array('success' => _('order settings has been updated successfully.') );
			}
		}
		
		if ($upd_empty)
		{
			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('custom_order_settings',$update_custom_order_settings);
			if($result){
				$return_data =  array('success' => _('order settings has been updated successfully.') );
			}
		}
		
	
		return $return_data;
	}
	
	function get_holiday_timings(){
			$holiday_timings=$this->get_order_settings();
			if($holiday_timings){			
				$holiday_timing=explode(',',$holiday_timings[0]->holiday_timings);
				//print_r($holiday_timing);
				return($holiday_timing);
			}else{
			
				return _('orders settings do not exist');
			}	
					
	}
	
	function do_order_settings($order_settings)
	{
	   $this->db->insert('order_settings',$order_settings);
	   $this->db->insert('custom_order_settings',$order_settings);
	   return $insert_id = $this->db->insert_id();
	}
	
}
?>