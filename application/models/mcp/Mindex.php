<?php
class Mindex extends CI_Model{
	function __construct(){
		parent::__construct();
	 	
	}	
//function to match username and password//
	function match($params){
		$query=$this->db->Query("SELECT * FROM `admin` WHERE login_username=".$this->db->escape($params['login_username'])." && login_password=".$this->db->escape($params['login_password'])."");
	 	return $query->result(); 
	}
//function to veryfy that the email is of valid admin or not//	
	function valid_email($email){
		$query=$this->db->get_where("admin",array('email'=>$email));
	    return $query->result(); 
	}
//function to validate that the user that has looged in is admin or not//
	function session_validate($session_id,$current_user){
		$this->db->select('user_data');
	    $query=$this->db->get_where('ci_sessions',array('session_id'=>$session_id));
	    $user_data=$query->result();
	    $session_data=unserialize($user_data['0']->user_data);
		if(array_key_exists('permission',$session_data)){
			if(($session_data['username']==$current_user)&&($session_data['permission']=='yes')){
				return true;
		    }
	       }
	   return false;
	   }
	   
	   //function to update master admin ip 
	   function update_admin_ip($ip_address = NULL){
		   	$this->db->where('id','1');
		   	$this->db->set('login_ip',$ip_address);
		   	$this->db->update('admin');

		   	return $this->db->affected_rows()?true:false;
	   }
}   
?>