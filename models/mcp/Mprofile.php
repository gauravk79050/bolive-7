<?php
	class Mprofile extends CI_Model
	{
		function construct()
		{
		parent::__construct();
			
		}
	/* Fetch the admin record from database */	
	
	function select($params=NULL)
	{
	$content=array();
	$query=$this->db->query("SELECT * FROM `admin`");
	if($query->num_rows())
	{
	   foreach($query->result() as $row)
	   {
	    $content[]=$row;
	   }
	}
	return $content;
	}
	/* Update the admin record */
	function update($params=array())
	{
	 $this->db->query("UPDATE `admin` SET
					`login_username` = ".$this->db->escape($params['login_username']).",
                    `login_password` = ".$this->db->escape($params['login_password']).",
	 				`admin_name` = ".$this->db->escape($params['admin_name']).",
					`email` = ".$this->db->escape($params['email'])."");
		return 1;			
	}
}	
?>