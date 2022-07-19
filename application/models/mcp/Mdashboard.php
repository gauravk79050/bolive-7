<?php 
class Mdashboard extends CI_Model
  {
    function __construct()
      {
	      parent::__construct();
		  
	  }  
	  
	  
	  function select()
       {
	      $query=$this->db->get('company');
		  return $query->result();
	   } 
	   
	   //--function to select pending companies--//
	  function pending()
	   {
		 $query=$this->db->get_where('company',array('approved'=>'0'));
		 return $query->result();
		
	   }
    function approve($id)
	 {
	  $this->db->query("UPDATE company SET approved='1' WHERE id='".$id."'");
	  return true;
	 }
  function delete($id)
	 {
	  $this->db->query("DELETE FROM company WHERE id='".$id."'");
	  return true;
	 }
	 
	 /**
	  * @name bckup_table_truncate
	  * @author Amit Sahu
	  */
	 function bckup_table_truncate()
	 {
	 	$this->db->truncate('ci_bp_sessions');
	 	$this->db->truncate('ci_desk_sessions');
	 	$this->db->truncate('ci_sessions');
	 	$this->db->truncate('log_api');
	 }
	 
  }

?>