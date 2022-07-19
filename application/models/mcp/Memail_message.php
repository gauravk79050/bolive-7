<?php
class Memail_message extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		
	}
	function select($params=NULL)
	{ 
	$content=array();
	$query=$this->db->query("SELECT * FROM `email_messages`");
    if($query->num_rows()>0)	
	{
	 foreach($query->result() as $rows)
	 {
	  $content[]=$rows;
	 }
	}
	return $content;
	}
}

?>