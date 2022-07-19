<?php
class Mthemes extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function get_themes(){
	
		$result = $this->db->get('themes')->result();
		if($result != array())
		  return $result;
		else
		  return false;
	}
	
	function set_company_css($company_id)
	{
	    $themes = $this->get_themes();
		
		if($company_id && !empty($themes))
		{
		   foreach($themes as $t)
		   {
		      $insert = array();
			  $insert['company_id'] = $company_id;
			  $insert['theme_id'] = $t->id;
			  $insert['theme_custom_css'] = $t->theme_css;
			  $insert['use_own_css'] = 0;
			  
			  $this->db->insert('company_css', $insert);
		   }
		}
	}
	
	function get_company_theme_css($company_id,$theme_id)
	{
	    $this->db->where('company_id', $company_id);
		$this->db->where('theme_id', $theme_id);
	    $result = $this->db->get('company_css')->result();
		
		if(!empty($result))
		  return $result[0];
		else
		  return false;
	}
	
	function update_company_theme_css($company_id,$theme_id)
	{
	    $update = array(
						   'theme_custom_css' => $this->input->post('theme_custom_css'),
						   'use_own_css' => ($this->input->post('use_own_css'))?$this->input->post('use_own_css'):0
					   );
		
		$this->db->where('company_id', $company_id);
		$this->db->where('theme_id', $theme_id);
		$this->db->update('company_css', $update); 
		
		return true;
	}
	
	function restore_company_theme_css($company_id,$theme_id)
	{
	    $this->db->where('id', $theme_id);
	    $result = $this->db->get('themes')->result();
		
		if(!empty($result))
		{
		   $default_theme_css_content = $result[0]->theme_css;
		   
		    $update = array(
							   'theme_custom_css' => $default_theme_css_content
						   );
			
			$this->db->where('company_id', $company_id);
			$this->db->where('theme_id', $theme_id);
			$this->db->update('company_css', $update);
			
			return true;
		}
		
		return false;
	}
}
?>