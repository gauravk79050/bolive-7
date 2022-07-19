<?php

class MFtp_settings extends CI_Model
{
	var $company_id;

	function __construct()
	{
		parent::__construct();	
		$this->company_id=$this->session->userdata('cp_user_id');
	}
	
	function get_ftp_settings( $where = array(), $where_in = array() )
	{
	    if( !empty($where) )
		  $this->db->where( $where );
		  
		if( !empty($where_in) )
		  foreach( $where_in as $key => $in_arr )
		    $this->db->where_in( $key, $in_arr );
		
		$ftp_settings = $this->db->get('company_ftp_details');
		$settings = $ftp_settings->result();
		
		return $settings;	
	}
	
	function update_ftp_settings( $upd_arr = array(), $where = array() )
	{
	    if( empty($upd_arr) )
		  return false;

		$isUpdated = false;
		  
		if( !empty($where) ){
		  $this->db->where( $where );

		  $isUpdated = $this->db->update('company_ftp_details',$upd_arr);
		}

		return $isUpdated;	
	}
	
	function add_ftp_settings( $ins_arr = array() )
	{
	    if( empty($ins_arr) )
		  return false;
		  		
		$isInserted = $this->db->insert('company_ftp_details',$ins_arr);
		$new_insert_id = $this->db->insert_id();
		
		return $new_insert_id;	
	}
}

?>