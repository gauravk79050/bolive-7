<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MODEL : Notify
 *
 * This model is used to interact with Notifications from Portal
 * @package Mnotify
 * @author Aniket Singh <aniketsingh@cedcoss.com>
*/

class Mnotify extends CI_model{

	function __construct(){
		parent::__construct();
	}

	/**
	 * This model function is used to fetch all notification from Portal
	 * @param array $where_array It is the array for Where clause
	 */
	function get_notifications($n_id = NULL, $today = NULL, $company_lang = NULL ){
		$this->db->select("notifications.*");
		if($n_id != NULL)
		{
			$this->db->where('notifications.id',$n_id);
		}
		if($today != NULL){
			$this->db->where('notifications.upto_date >', $today);
		}
		if($company_lang != NULL){
			$this->db->where('notifications.companies_lang', $company_lang );
		}
		
		$this->db->order_by("notifications.created_date","desc");
		return $this->db->get("notifications")->result_array();
	}
	function insert($dummy = NULL){
 		return $this->db->insert('notifications',$dummy);
 		
 	}
 	
 	function get_account($id = NULL){
 		
 		if($id != NULL){
 			$this->db->where('id',$id);
 		}
 		$result = $this->db->get('account_type');
 		return $result->result();
 	}

 	function get_types($id = NULL){
 		
 		if($id != NULL){
 			$this->db->where('id',$id);
 		}
 		$result = $this->db->get('company_type');
 		return $result->result();
 	}
	
 	function update($dummy = NULL, $n_id = NULL){
 		$this->db->where('id',$n_id);
 		$this->db->update('notifications',$dummy);
 		$affected_rows  = $this->db->affected_rows();
 		if( $affected_rows ){
 			$this->db->where('notification_id',$n_id );
 			$this->db->delete('closed_notifications');
 		}
 		return $affected_rows;
 	}
 	function delete_this($n_id = NULL){
 		$this->db->where('id',$n_id);
 		return $this->db->delete('notifications');
 	}
 	

 	function insert_into_close_notification($insert_array = array()){
 		return  $this->db->insert('closed_notifications',$insert_array);
 	}
 	
 	function get_closed_noti($c_id = NULL){
 		if($c_id != NULL){
 			$this->db->where('company_id',$c_id);
 		}
 		return $this->db->get('closed_notifications')->result();
 	}
}
