<?php
if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Members
 * 
 * This model is specially built for the listing of Keurslager Association Members
 * 
 * @package Mmembers
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
Class Mmembers extends CI_Model{
	
	function __construct(){
		parent:: __construct();
	}
	
	
	/**
	 * Function to get all keurslager members
	 * @param string $select Comma seperated string of column names to be selected
	 * @param array $param Array of columns and values to be used in where clause
	 * @param mixed $start Its the start value in case of limited members to be fetched
	 * @param mixed $limit Its the limit value in case of limited members to be fetched
	 * @param boolean $count Its the boolean variable which when set then this model function will return total count
	 * @return mixed $members Its the array of members OR total count of members
	 */
	function getMembers( $select = '', $param = array(), $start = null, $limit = null, $count = false){
		
		if($select != ''){
			$this->db->select($select);
		}else{
			$this->db->select('id, company_name, first_name, last_name, email, phone, username, password');
		}
		
		if(!empty($param))
			$this->db->where($param);
		
		if($count){
			$members = $this->db->count_all_results('company');
		}else{
			if($start != null && $limit != null)
				$this->db->limit($limit, $start);
			
			$members = $this->db->get('company')->result();
		}
		
		return $members;
	}
}
?>