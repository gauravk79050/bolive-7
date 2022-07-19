<?php 
class Mpickup_delivery_timings extends CI_Model{
	function __construct(){
			parent::__construct();
	        $this->company_id=$this->session->userdata('cp_user_id');
	}

	function get_pickup_delivery_timings($param=array()){
	
		if($param){
			foreach($param as $key=>$val){
				$this->db->where(array($key=>$val));
			
			}
		}else{
		
			$this->db->where(array('company_id'=>$this->company_id));
		}
		
		$this->db->order_by('id','asc');
		
		$query=$this->db->get('pickup_delivery_timings');
		return($query->result());
		
	}
	
	function update_pickup_delivery_timings(){
		
		if($this->input->post('act')=="edit_pickup_settings"){
			
			for($pickup_counter=1;$pickup_counter<=4;$pickup_counter++){
		
		
				foreach($this->input->post('p'.$pickup_counter) as $key=>$val){
					$this->db->where(array('day_id'=>$key));
					$this->db->where(array('company_id'=>$this->company_id));
					$this->db->update('pickup_delivery_timings',array('pickup'.$pickup_counter=>$val));
					
				}
			}
		
		}else if($this->input->post('act')=="edit_delivery_settings"){
			
			for($delivery_counter=1;$delivery_counter<=4;$delivery_counter++){
		
		
				foreach($this->input->post('d'.$delivery_counter) as $key=>$val){
					$this->db->where(array('day_id'=>$key));
					$this->db->where(array('company_id'=>$this->company_id));
					$this->db->update('pickup_delivery_timings',array('delivery'.$delivery_counter=>$val));
					
				}//end of foreach
				
			}//end of for
		
		}//end of else if
		
	}//end of ofunction
	
	function update_custom_pickup_timings(){
		if($this->input->post('act')=="edit_pickup_settings"){
			$custom_new_pickup_hours = $this->input->post('custom_new_pickup_hours');
			$q1 = array_values($this->input->post('q1'));
			$q2 = array_values($this->input->post('q2'));
			$q3 = array_values($this->input->post('q3'));
			$q4 = array_values($this->input->post('q4'));
			
			
			if ((isset($custom_new_pickup_hours) && !empty($custom_new_pickup_hours)))
			{
				$searchObject = '';
				$keys = array();
				foreach($custom_new_pickup_hours as $k => $v) {
					if($v == $searchObject) $keys[] = $k;
				}
				if (!empty($keys))
				{
					foreach ($keys as $val)
					{
						unset($custom_new_pickup_hours[$val]);
						unset($q1[$val]);
						unset($q2[$val]);
						unset($q3[$val]);
						unset($q4[$val]);
					}	
				}
				
				$update_custom_new_pickup_hours=array(
						'pickup_days'=>implode("#",$custom_new_pickup_hours),
						'pickup1'=>implode("#",$q1),
						'pickup2'=>implode("#",$q2),
						'pickup3'=>implode("#",$q3),
						'pickup4'=>implode("#",$q4)
				);
				$this->db->where(array('company_id'=>$this->company_id));
				$this->db->update('custom_pickup_timing',$update_custom_new_pickup_hours);
			}
			else
			{
				$update_custom_new_pickup_hours=array(
						'pickup_days'=>'',
						'pickup1'=>'',
						'pickup2'=>'',
						'pickup3'=>'',
						'pickup4'=>''
				);
				$this->db->where(array('company_id'=>$this->company_id));
				$this->db->update('custom_pickup_timing',$update_custom_new_pickup_hours);
			}
		}
	}
	
	function pickup_timings(){
	
		$this->db->select('pickup_delivery_timings.*,days.name');
		$this->db->from('pickup_delivery_timings');
		$this->db->join('days','pickup_delivery_timings.day_id=days.id','inner');
		$this->db->where(array('company_id'=>$this->company_id));
		
		$this->db->order_by('pickup_delivery_timings.id','asc');
		
		$query=$this->db->get();
	   	return($query->result());
	}
	
	//sid start
	function custom_pickup_timings(){
	
		$this->db->select('custom_pickup_timing.*');
		$this->db->from('custom_pickup_timing');
		$this->db->where(array('company_id'=>$this->company_id));
		$query=$this->db->get();
		return($query->result());
	}//sid end
	
	function do_pickup_delivery_timings_settings($pickup_delivery_timings)
	{
	   $this->db->insert('pickup_delivery_timings',$pickup_delivery_timings);
	   return $insert_id = $this->db->insert_id();
	}
	
	function do_pickup_delivery_timings_settings_custom($pickup_delivery_timings)
	{
		$this->db->insert('custom_pickup_timing',$pickup_delivery_timings);
		return $insert_id = $this->db->insert_id();
	}
}

?>