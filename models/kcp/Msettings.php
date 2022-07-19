<?php
if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Msettings
 * 
 * This model is specially built for Update KCP Settings
 * 
 * @package Msettings
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
Class Msettings extends CI_Model{
	
	function __construct(){
		parent:: __construct();
	}
	
	/**
	 * Function to get settings
	 * @param string $select Comma seperated string of column names to be selected
	 * @param array $param Array of columns and values to be used in where clause
	 * @return mixed $settings Its the array of settings
	 */
	function get( $select = '', $param = array()){
		
		if($select != ''){
			$this->db->select($select);
		}
		
		if(!empty($param)){
			$this->db->where($param);
		}
		
		$settings = $this->db->get('k_settings')->result(); 
		
		return $settings;
	}

	/**
	 * This model function is used to update Settings Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 * @param array $update_array Its the array ofc ols and vals to update
	 */
	function update($where_array = array(), $update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			
			$this->db->where($where_array);
			$this->db->update('k_settings', $update_array);
			
		}else{
			return false;
		}
	}
}
?>