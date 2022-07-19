<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MODEL : Notifications
 * 
 * This model is used to interact with Notifications from Portal
 * @package Mnotification
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */

class Mnotifications extends CI_model{
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * This model function is used to fetch all notification from Portal
	 * @param array $where_array It is the array for Where clause
	 */
	function get_notifications($where_array = array()){
		
		$this->db->select("order_request_portal.*, company.company_name, company.email, company.city");
		if(!empty($where_array)){
			$this->db->where($where_array);
		}
		
		$this->db->order_by("date","desc");
		$this->db->join("company","order_request_portal.company_id = company.id");
		return $this->db->get("order_request_portal")->result();
	}
	
	/**
	 * This model function is used to delete specific notification
	 * @param int $id ID to delete
	 */
	function delete_notifications($id = null){
		if($id)
			$this->db->delete("order_request_portal",array("id" => $id));
	}
	
	/**
	 * This model function is used to fetch all notification from Portal
	 * @param array $where_array It is the array for Where clause
	 */
	function get_contact_requests($where_array = array()){
	
		$this->db->select("contact_requests.*, company.company_name, company.city,company.id as company_id,company.email as company_email");
		if(!empty($where_array)){
			$this->db->where($where_array);
		}
	
		//$this->db->order_by("date","desc");
		$this->db->join("company","contact_requests.company_slug = company.company_slug");
		return $this->db->get("contact_requests")->result();
	}
	
	/**
	 * This model function is used to delete specific notification
	 * @param int $id ID to delete
	 */
	function delete_contact_requests($id = null){
		if($id)
			$this->db->delete("contact_requests",array("id" => $id));
	}
}
?>