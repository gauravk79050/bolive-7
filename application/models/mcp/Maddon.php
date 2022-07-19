<?php 
class Maddon extends CI_model{
	function __construct(){
		parent::__construct();
	}
	
	function get_addons(){
		$this->db->order_by("addon_display_order", "desc");
		return $this->db->get('addon_manager')->result();
	}
	
	function select($addon_id = null){
		return $this->db->get_where('addon_manager',array('addon_id'=>$addon_id))->result();
	}
	
	function update($adon_id = null, $update = array()){
		$this->db->where('addon_id',$adon_id);
		if($this->db->update('addon_manager',$update))
			return true;
		else
			return false;
	}
	
	function insert_addon($insert_data = array()){
		if(!empty($insert_data)){
			if($this->db->insert("addon_manager" , $insert_data)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
}
?>