<?php 
class Mopening_hours extends CI_Model{

	var $company_id;

	function __construct(){
	
		parent::__construct();
		
		$this->company_id = $this->session->userdata('cp_user_id');
	
	}
	
	function get_opening_hours( $company_id = NULL, $params = array() )
	{
	    if(!$company_id)
		  $company_id = $this->company_id;
		
		$this->db->where('company_id',$company_id);
		
		if(!empty($params))
		  foreach($params as $col=>$val)
		    $this->db->where($col,$val);
		  
		$opening_hours = $this->db->get('opening_hours');
		$opening_hours = $opening_hours->result();
		
		return $opening_hours;
	}
	
	function update_opening_hours( $company_id = NULL, $params = array() )
	{
	    if(!$company_id)
		  $company_id = $this->company_id;
	
		$days = $this->get_days();
		$opening_hours = $this->get_opening_hours();
		
		if(!empty($opening_hours))
		{
		   if( count($days) != count($opening_hours) )
	       {
		      //Delete nd Add
			  $this->db->where( 'company_id', $company_id );
		      $this->db->delete('opening_hours');
			  
			   $insert_ids = array();
		   
			   foreach($params as $id=>$index)
			   {
				   $row = array(
								  'company_id' => $company_id,
								  'day_id' => $id,
								  'time_1' => $index['time_1'],
								  'time_2' => $index['time_2'],
								  'time_3' => $index['time_3'],
								  'time_4' => $index['time_4']
							   );
				   $this->db->insert('opening_hours',$row);
				   $insert_ids[] = $this->db->insert_id();
			   }
			   
			   return $insert_ids;
		   }	
		   else
		   {
		       //Update
			  
			   foreach($params as $id=>$index)
			   {
				   $row = array(
								  'time_1' => $index['time_1'],
								  'time_2' => $index['time_2'],
								  'time_3' => $index['time_3'],
								  'time_4' => $index['time_4']
							   );
				   
				   $this->db->where( 'company_id', $company_id );
				   $this->db->where( 'day_id', $id );
		           $this->db->update('opening_hours',$row);
			   }
			   
			   return true;
		   }
		}
		else
		{
		   //Add
		   
		   $insert_ids = array();
		   
		   foreach($params as $id=>$index)
		   {
		       $row = array(
			                  'company_id' => $company_id,
							  'day_id' => $id,
							  'time_1' => $index['time_1'],
							  'time_2' => $index['time_2'],
							  'time_3' => $index['time_3'],
							  'time_4' => $index['time_4']
						   );
			   $this->db->insert('opening_hours',$row);
	           $insert_ids[] = $this->db->insert_id();
		   }
		   
		   return $insert_ids;
		}
	}	
	
	function get_days()
	{
	    return $days = $this->db->get('days')->result();
	}
}
?>