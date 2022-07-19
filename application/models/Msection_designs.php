<?php 

class Msection_designs extends CI_Model{

	var $company_id;
	
	function __construct(){
	
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}
	
	function get_section_designs(){
	
		
		$theme_id = $this->db->select('theme_id')->where('company_id',$this->company_id)->get('general_settings')->result();
		$themeId = $theme_id[0]->theme_id;
		//echo $themeId."<br />";
		$this->db->where(array('company_id'=>$this->company_id,'theme_id' => $themeId));
		$section_designs = $this->db->get('section_designs')->result();
		//echo $this->db->last_query();
		//print_r($section_designs); die();
		/*$themes = $this->db->get('themes');
		$themes = $themes->result();*/
		
		$sections = $this->db->get('sections');
		$sections = $sections->result();
		
		if($section_designs == array()){
			
			//foreach($themes as $theme){ //for($i=0;$i<3;$i++){
				foreach($sections as $section){ //for($j=1;$j<=3;$j++){
					$data=array('company_id'=>$this->company_id,'theme_id'=>$themeId,'section_id'=>$section->id);
					$this->db->insert('section_designs',$data);
				}	
			//}
			
			$this->db->where(array('company_id'=>$this->company_id,'theme_id' =>$themeId));	
			$section_designs = $this->db->get('section_designs')->result();
		}	
		
		return $section_designs;
	}
	
	function add_update_section_designs(){
		
		$theme_id = $this->db->select('theme_id')->where('company_id',$this->company_id)->get('general_settings')->result();
		
		/*$sections = $this->db->get('sections');
		$sections = $sections->result();*/
		
		for($i = 1;$i <= 3;$i++){ // Loop for section
			$update_section = array(
				'background'=>$this->input->post('background_'.$i),
				'color'=>$this->input->post('color_'.$i),
				'width'=>$this->input->post('width_'.$i),
				'font-family'=>$this->input->post('font-family_'.$i),
				'font-size'=>$this->input->post('font-size_'.$i),
				'font-style'=>$this->input->post('font-style_'.$i),
				'font-weight'=>$this->input->post('font-weight_'.$i),
				'text-decoration'=>$this->input->post('text-decoration_'.$i),
			);
			$this->db->where(array('company_id'=>$this->company_id,'section_id'=>$i,'theme_id' =>$theme_id[0]->theme_id));		
			$this->db->update('section_designs',$update_section);
		}
	}
}
?>