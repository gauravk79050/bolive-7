<?php
if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Suppliers
 * 
 * This model is specially built for Add/Update/Delete Suppliers
 * 
 * @package Msuppliers
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
Class Msuppliers extends CI_Model{
	
	function __construct(){
		parent:: __construct();
	}
	
	
	/**
	 * Function to get all Suppliers
	 * @param string $select Comma seperated string of column names to be selected
	 * @param array $param Array of columns and values to be used in where clause
	 * @param mixed $start Its the start value in case of limited members to be fetched
	 * @param mixed $limit Its the limit value in case of limited members to be fetched
	 * @param boolean $count Its the boolean variable which when set then this model function will return total count
	 * @return mixed $suppliers Its the array of Suppliers OR total count of Suppliers
	 */
	function get( $select = '', $param = array(), $start = null, $limit = null, $count = false){
		
		if($select != ''){
			$this->db->select($select);
		}
		
		if(!empty($param))
			$this->db->where($param);
		
		if($count){
			$suppliers = $this->db->count_all_results('k_suppliers');
		}else{
			if($start != null && $limit != null)
				$this->db->limit($limit, $start);
			
			$suppliers = $this->db->get('k_suppliers')->result();
		}
		
		return $suppliers;
	}
	
	/**
	 * This model function is used to insert suppliers into db
	 * @param array $insert_array Its the array of inerting values
	 * @return mixed Either ID of last supplier or False
	 */
	function insert($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('k_suppliers', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to update Supplier's Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 * @param array $update_array Its the array ofc ols and vals to update
	 */
	function update($where_array = array(), $update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			$this->db->where($where_array);
			return $this->db->update('k_suppliers', $update_array);
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to delete Supplier and its related infos
	 * @param int $supplier_id Its the ID of Supplier
	 */
	function delete($supplier_id = 0){
		if($supplier_id){
			
			$delete_query = "DELETE `k_suppliers`, `k_products`, `k_prod_ingredients`, `products_ingredients`, `k_prod_allergence`, `products_allergence`, `k_prod_traces`, `products_traces`
							FROM `k_suppliers`
							LEFT JOIN `k_products` ON `k_suppliers`.`s_id` = `k_products`.`kp_s_id`
							LEFT JOIN `k_prod_ingredients` ON `k_prod_ingredients`.`p_id` = `k_products`.`kp_id`
							LEFT JOIN `products_ingredients` ON `products_ingredients`.`kp_id` = `k_products`.`kp_id`
							LEFT JOIN `k_prod_allergence` ON `k_prod_allergence`.`p_id` = `k_products`.`kp_id`
							LEFT JOIN `products_allergence` ON `products_allergence`.`kp_id` = `k_products`.`kp_id`
							LEFT JOIN `k_prod_traces` ON `k_prod_traces`.`p_id` = `k_products`.`kp_id`
							LEFT JOIN `products_traces` ON `products_traces`.`kp_id` = `k_products`.`kp_id` 
							WHERE `k_suppliers`.`s_id` = ".$supplier_id;
			
			return $this->db->query($delete_query);
			
		}else{
			return false;
		}
	}
}
?>