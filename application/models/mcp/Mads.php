<?php
class Mads extends CI_Model
{ 
     
	 function __construct()
     {
        parent::__construct();
	    
     } 
	 
	 function update_email_messages($id,$option,$value)
	 {
	    $this->db->query("UPDATE email_messages SET `".$option."`=".$this->db->escape($value)." WHERE `id`='".$id."' ");
		return true;
	 }
	 
	 //--function to list  emails--//
	 function select($arr=array())
	 {
	     if($arr)//--for the update form--//
	     {
		    $query=$this->db->get_where('email_messages',array('id'=>$arr['id']));
	        return $query->result();
		 }
		 else//--for the simple listing--//
		 {
		    $query=$this->db->get('email_messages');
	        return $query->result();
		 }
	 }
}
?>