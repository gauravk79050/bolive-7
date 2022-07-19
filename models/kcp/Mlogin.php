<?php
if(! defined('BASEPATH') ) exit(_('No direct script access allowed'));

/**
 * Model: Login
 * 
 * This model deals with the login section for Keurslager association
 * @package Mlogin
 * 
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */

Class Mlogin extends CI_Model{
	
	function __construct(){
		parent::__construct();
		
	}
	
	/**
	 * This model function is used to validate username and password of keurslager association
	 * @param string $username Entered Username
	 * @param string $password Entered Password
	 * @return array $userdata Data of keurslager if valid
	 */
	function validate($username = '', $password = ''){
		$userdata = array();
		
		if($username && $password){
			$userdata = $this->db->get_where('k_association', array('k_username' => $username, 'k_password' => $password))->result();
		}
		
		return $userdata;
	}
	
	/**
	 * This model function is used to update k_association table, specially at the time of login to update Last login and Login IP
	 * @param array $where_array Its the array of col-value for where clause
	 * @param array $update_array Its the array of col-value that is going to be update
	 */
	function update($where_array = array(), $update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			$this->db->where($where_array);
			return $this->db->update('k_association', $update_array);
		}
	}
	
	/**
	 * This model function is used to check for the password recovery
	 * @param string $email It is the email entered by user
	 */
	function forgot_password($email = '') {
		if ($email) {
			$result = $this->db->where ( 'k_email', $email )->get ( 'k_association' )->result ();
			if ($result != array ()) {
				$details_array = array (
						'first_name' => $result [0]->k_firstname,
						'last_name' => $result [0]->k_lastname,
						'username' => $result [0]->k_username,
						'password' => $result [0]->k_password,
						'email' => $result [0]->k_email
				);
				$return_data = array (
						'success' => $details_array
				);
			} else {
				$return_data = array (
						'error' => _ ( 'The specified email address is not in our database.' )
				);
			}
		} else {
			$return_data = array (
					'error' => _ ( 'Didn\'t Receive Any Email id!Plese enter Email Id Again' )
			);
		}
		return $return_data;
	}
}
?>