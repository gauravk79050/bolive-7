<?php 
class Mdelivery_settings extends CI_Model{

	function __construct(){
	
		parent::__construct();
	
	}

	function get_delivery_settings($param=array()){
	
		if($param){
		
			foreach($param as $key=>$val){
			
				$this->db->where(array($key=>$val));
			
			}//end of foreach
		
		}
	$query=$this->db->get('delivery_settings');
	return($query->result());
	}
	function update_delivery_settings(){
		//print_r($this->input->post());
		//die();
		$id = $this->input->post('id');
		$update_delivery_settings=array(
		
				'delivery_areas_id'=>$this->input->post('delivery_areas_id'),
			   	'city_name'=>$this->input->post('city_name'),
			   	'zipcode'=>$this->input->post('zipcode'),
			   	'cost'=>$this->input->post('cost'),
				'timerange'=>$this->input->post('timerange')
		
		);
		$this->db->where(array('id'=>$id));
		$result = $this->db->update('delivery_settings',$update_delivery_settings);
		
		if($result){
			return array('success' =>$id);
		}else{
			return array('error' =>$id);
		}
	
	}
	
	function delete_delivery_settings(){
		
		$id=$this->input->post('id');
		$result = $this->db->delete('delivery_settings',array('id'=>$id));
	
	}

	function insert_delivery_settings(){
			//print_r($this->input->post());
		
		$delivery_settings=array(
			   'delivery_areas_id'=>$this->input->post('delivery_areas_id'),
			   'city_name'=>$this->input->post('city_name'),
			   'zipcode'=>$this->input->post('zipcode'),
			   'cost'=>$this->input->post('cost'),
			   'timerange'=>$this->input->post('timerange')
		
		);
	
		$this->db->insert('delivery_settings',$delivery_settings);
		$id = $this->db->insert_id();
		//echo $id;
		if($id){
			return array('success' =>$id);
		}else{
			return array('error' =>$id);
		}
	}
}

?>