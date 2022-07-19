<?php 
class Maddons extends CI_model{
	function __construct(){
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	}
	
	function get_addons(){
		
		return $this->db->get('addon_manager')->result();
		
	}
	
	function get_activated_addons(){

		$this->db->select('activated_addons');
		$this->db->where(array('company_id'=>$this->company_id));
		$query=$this->db->get('general_settings')->result();
		return($query);
		
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
}
?>