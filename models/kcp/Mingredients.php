<?php
if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Mingredients
 * 
 * This model is specially built for Add/Update/Delete Ingredients
 * 
 * @package Mproducts
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
Class Mingredients extends CI_Model{
	
	function __construct(){
		parent:: __construct();
	}
	
	/**
	 * Function to get all Ingredients
	 * @param string $select Comma seperated string of column names to be selected
	 * @param array $param Array of columns and values to be used in where clause
	 * @param mixed $start Its the start value in case of limited members to be fetched
	 * @param mixed $limit Its the limit value in case of limited members to be fetched
	 * @param boolean $count Its the boolean variable which when set then this model function will return total count
	 * @return mixed $suppliers Its the array of Products OR total count of Products
	 */
	function get( $select = '', $param = array(), $start = null, $limit = null, $count = false){
		
		if($select != ''){
			$this->db->select($select);
		}
		
		if(!empty($param)){
			$this->db->where($param);
		}
		
		if($count){
			$ingredients = $this->db->count_all_results('k_ingredients');
		}else{
			if($start != null && $limit != null)
				$this->db->limit($limit, $start);
			
			$ingredients = $this->db->get('k_ingredients')->result();

		}
		
		return $ingredients;
	}
	
	/**
	 * This model function is used to insert ingredient into db
	 * @param array $insert_array Its the array of inerting values
	 * @return mixed Either ID of last inserted Ingredient or False
	 */
	function insert($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('k_ingredients', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to update Ingredient's Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 * @param array $update_array Its the array ofc ols and vals to update
	 */
	function update($where_array = array(), $update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			
			$this->db->trans_start();
			
			if(isset($where_array['ing_id']) && isset($update_array['ing_name'])){
				$this->db->where('ki_id',$where_array['ing_id']);
				$this->db->update('products_ingredients', array('ki_name' => $update_array['ing_name']));
			}
			
			$this->db->where($where_array);
			$this->db->update('k_ingredients', $update_array);
			
			$this->db->trans_complete();
		
			if ($this->db->trans_status() === FALSE)
			{
			    $this->db->trans_rollback();
			}
			else
			{
			    $this->db->trans_commit();
			}
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to delete Ingredients's Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 */
	function delete($where_array = array()){
		if(!empty($where_array)){
			if(isset($where_array['ing_id'])){
				$query = 'DELETE `k_ingredients`, `k_prod_ingredients`, `products_ingredients`
							FROM `k_ingredients`
							LEFT JOIN `k_prod_ingredients` ON `k_ingredients`.`ing_id` = `k_prod_ingredients`.`i_id`
							LEFT JOIN  `products_ingredients` ON `k_ingredients`.`ing_id` = `products_ingredients`.`ki_id`
							WHERE `k_ingredients`.`ing_id` = '.$where_array['ing_id'];
				
				return $this->db->query($query);
			}else{
				return $this->db->delete('k_ingredients', $where_array);				
			}

		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to delete Ingredients's Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 */
	function delete_prod($i_id){
		if(isset($i_id)){
			$query = $this->db->get_where('k_prod_ingredients', array('i_id' => $i_id));
			$result = $query->result();
			if(!empty($result))
			{
				return $this->db->delete('k_prod_ingredients', array('i_id' => $i_id));
			}			
		}else{
			return false;
		}
	}
	
}
?>