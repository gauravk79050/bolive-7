<?php
class Mreports extends CI_Model {

    function __construct()
    {
        parent::__construct();
		
    }
	
	function get_block_ips( $array = false )
	{
	   $ips = $this->db->get('block_ips')->result();
	   
	   if( $array && !empty($ips))
	   {
	      $ip_arr = array();
	      foreach($ips as $ip)
		     $ip_arr[] = $ip->ip_address;
			 
		  return $ip_arr;
	   }
	   else
	     return $ips;
	   
	}
	
	function insert_report( $ins_arr = array()  )
	{
	    if( empty($ins_arr) )
		  return false;
	
	    $query = $this->db->insert('correction_reports',$ins_arr);
		return $query;
	}
	function get_report()
	{
		$query= $this->db->get('correction_reports')->result_array();
		return $query;
	}
	function update_suggestion($data)
	{
		$status=array('status'=>1);
		if(isset($data['update_reports']))
			unset($data['update_reports']);
		$this->db->where_in('id',$data['status']);
		$this->db->update('correction_reports',$status);
		//return true;
	}
	function get_suggestion()
	{
		$this->db->where('status',0);
		$query=$this->db->get('suggested_correction')->result_array();
		return $query;
	}
	function disapprove_suggestion($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('suggested_correction');
		return true;
	}
	function get_suggestion_approval_data($id)
	{
		$this->db->where('id',$id);
		$query=$this->db->get('suggested_correction')->row_array();
		return $query;
	}
	function update_company($data_company,$company_id)
	{
		$this->db->where('id',$company_id);
		$this->db->update('company',$data_company);
		return true;
	}
	function update_suggestion_status($id)
	{
		$data = array('status'=>1); 
		$this->db->where('id',$id);
		$this->db->update('suggested_correction',$data);
		return true;
	}
	function update_opening_hrs($company_id,$update_array,$i)
	{
		$this->db->where('company_id',$company_id);
		$this->db->where('day_id',$i);
		$this->db->update('opening_hours',$update_array);
		return true;
	}
}
?>