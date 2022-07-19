<?php
class Mindex extends CI_Model
{
    function __construct()
	{
	 parent::__construct();
	 
	}	
	//--function to match username and password--//
	function match($params)
	{
	 $content=array();
	 $query=$this->db->Query("SELECT * FROM `admin` WHERE login_username=".$this->db->escape($params['login_username'])." && login_password=".$this->db->escape($params['login_password'])."");
	 return $query->result(); 
	}
	function valid_email($email)
	{
	 
	 $query=$this->db->get_where("admin",array('email'=>$email));
	 return $query->result(); 
	}
}
?>