<?php
if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Products
 * 
 * This model is specially built for Add/Update/Delete Products
 * 
 * @package Mproducts
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
Class Mproducts extends CI_Model{
	
	function __construct(){
		parent:: __construct();
	}
	
	/**
	 * Function to get all Products
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
			if(isset($param['letter'])){
				$this->db->like('kp_name',$param['letter'],'after');
				unset($param['letter']);
			}
			$this->db->where($param);
		}
		
		$this->db->join('k_suppliers', 'k_products.kp_s_id = k_suppliers.s_id');
		
		if($count){
			$products = $this->db->count_all_results('k_products');
		}else{
			if($start != null && $limit != null)
				$this->db->limit($limit, $start);
			
			$products = $this->db->get('k_products')->result();
			
			if(!empty($products)){
				foreach ($products as $key => $product){
					$products[$key]->ingredients = $this->getIngredients(array('p_id' => $product->kp_id));
					$products[$key]->allergence = $this->getAllergence(array('p_id' => $product->kp_id));
					$products[$key]->traces = $this->getTraces(array('p_id' => $product->kp_id));
				}
			}
		}
		
		return $products;
	}
	
	/**
	 * This model function is used to insert product into db
	 * @param array $insert_array Its the array of inerting values
	 * @return mixed Either ID of last supplier or False
	 */
	function insert($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('k_products', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to update Product's Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 * @param array $update_array Its the array ofc ols and vals to update
	 */
	function update($where_array = array(), $update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			
			// Transaction start
			$this->db->trans_start();
			
			// Updating product info for KCP
			$this->db->where($where_array);
			$this->db->update('k_products', $update_array);
			
			// Updating product name for all admins
			$this->db->where($where_array);
			$this->db->where('ki_id',0);
			$this->db->update('products_ingredients', array('ki_name' => $update_array['kp_name']));
			
			// Transaction complete
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				return false;
			}
			else
			{
				$this->db->trans_commit();
				return true;
			}
			
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to update Product's allergence Infos
	 * @param array $update_array Its the array ofc ols and vals to update
	 * @param Int $product_id Its the Id of product for which allergence to be added
	 */
	function updateAllergence($update_array = array(), $product_id = 0){
		
		// Fetching all product Ids and Max display order (CP) to update new allergence
		$this->db->select('product_id, MAX(`display_order`) as max_disp', FALSE);
		$this->db->group_by('product_id');
		$cp_prod_ids = $this->db->get('products_allergence')->result();
		
		// Fetching old allergence associated with keurslager products
		$this->db->select('a_id');
		$get_allergence = $this->db->get_where(' k_prod_allergence', array('p_id' => $product_id))->result();
		$old_allergence = array();
		if(!empty($get_allergence)){
			foreach ($get_allergence as $a_id)
				$old_allergence[] = $a_id->a_id;
		}
		
		// Allergence Ids to delete
		$allergence_to_add = array_diff($update_array,$old_allergence);
		
		// Allergence Ids to add
		$allergence_to_del = array_diff($old_allergence, $update_array);
		
		$this->db->trans_start();
		
		// Adding Allergence
		if(!empty($allergence_to_add)){
			
			// Getting Name of Allergence to insert in CP products
			$this->db->select('a_id,a_name');
			$this->db->where_in('a_id', $allergence_to_add);
			$allergence_info_to_add = $this->db->get('k_allergence')->result();
			
			if(!empty($allergence_info_to_add)){
				// Looping through -allergence with name- to be added
				foreach ($allergence_info_to_add as $a_info_to_add){
					
					// Adding allergence in Keurslager Products
					$this->db->insert('k_prod_allergence', array('p_id' => $product_id, 'a_id' => $a_info_to_add->a_id, 'date_added' => date('Y-m-d H:i:s')));
					
					// Adding allergence to the CP products
					if(!empty($cp_prod_ids)){
						foreach ($cp_prod_ids as $cp_prod_id){
							$is_assoc_with_cur_cp_prod = $this->db->get_where('products_allergence', array('product_id' => $cp_prod_id->product_id, 'ka_id' => $a_info_to_add->a_id) )->result();
							if(empty($is_assoc_with_cur_cp_prod)){
								$insert_array = array(
										'product_id' => $cp_prod_id->product_id,
										'kp_id' => $product_id,
										'ka_id' => $a_info_to_add->a_id,
										'ka_name' => $a_info_to_add->a_name,
										'display_order' => ($cp_prod_id->max_disp + 1),
										'date_added' => date('Y-m-d H:i:s')
								);
								$this->db->insert('products_allergence', $insert_array);
							}
						}
					}
					
				}
			}
			
		}
		
		// Deleting Allergence
		if(!empty($allergence_to_del)){
			foreach($allergence_to_del as $a_to_del){
				$this->db->delete('k_prod_allergence', array('p_id' => $product_id, 'a_id' => $a_to_del));
				$this->db->delete('products_allergence', array('kp_id' => $product_id, 'ka_id' => $a_to_del));
			}
		}
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
		
	}
	
	/**
	 * This model function is used to update Product's ingredients Infos
	 * @param array $update_array Its the array of cols and vals to update
	 * @param Int $product_id Its the Id of product for which ingredients to be added
	 */
	function updateIngredients($update_array = array(), $product_id = 0){
		
		// Fetching all product Ids and Max display order (CP) to update new ingredients
		$this->db->select('product_id, MAX(`display_order`) as max_disp', FALSE);
		$this->db->group_by('product_id');
		$cp_prod_ids = $this->db->get('products_ingredients')->result();
		
		// Fetching old ingredients associated with keurslager products
		$this->db->select('i_id');
		$get_ingredients = $this->db->get_where('k_prod_ingredients', array('p_id' => $product_id))->result();
		$old_ingredients = array();
		if(!empty($get_ingredients)){
			foreach ($get_ingredients as $ing_id)
				$old_ingredients[] = $ing_id->i_id;
		}
		
		// Ingredient Ids to delete
		$ingredient_to_add = array_diff($update_array,$old_ingredients);
		
		// Ingredient Ids to add
		$ingredient_to_del = array_diff($old_ingredients, $update_array);
		
		$this->db->trans_start();
		
		// Adding Ingredients
		if(!empty($ingredient_to_add)){
			
			// Getting Name of ingredients to insert in CP products
			$this->db->select('ing_id,ing_name');
			$this->db->where_in('ing_id', $ingredient_to_add);
			$ingredient_info_to_add = $this->db->get('k_ingredients')->result();
			
			if(!empty($ingredient_info_to_add)){
				// Looping through -ingredients with name- to be added
				foreach ($ingredient_info_to_add as $ing_info_to_add){
					
					// Adding ingredients in Keurslager Products
					$this->db->insert('k_prod_ingredients', array('p_id' => $product_id, 'i_id' => $ing_info_to_add->ing_id, 'date_added' => date('Y-m-d H:i:s')));
					
					// Adding ingredients to the CP products
					if(!empty($cp_prod_ids)){
						foreach ($cp_prod_ids as $cp_prod_id){
							$is_assoc_with_cur_cp_prod = $this->db->get_where('products_ingredients', array('product_id' => $cp_prod_id->product_id, 'ki_id' => $ing_info_to_add->ing_id) )->result();
							if(empty($is_assoc_with_cur_cp_prod)){
								$insert_array = array(
										'product_id' => $cp_prod_id->product_id,
										'kp_id' => $product_id,
										'ki_id' => $ing_info_to_add->ing_id,
										'ki_name' => $ing_info_to_add->ing_name,
										'display_order' => ($cp_prod_id->max_disp + 1),
										'date_added' => date('Y-m-d H:i:s')
								);
								$this->db->insert('products_ingredients', $insert_array);
							}
						}
					}
					
				}
			}
			
		}
		
		// Deleting Ingredients
		if(!empty($ingredient_to_del)){
			foreach($ingredient_to_del as $ing_to_del){
				$this->db->delete('k_prod_ingredients', array('p_id' => $product_id, 'i_id' => $ing_to_del));
				$this->db->delete('products_ingredients', array('kp_id' => $product_id, 'ki_id' => $ing_to_del));
			}
		}
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
		
	}
	
	/**
	 * This model function is used to update Product's traces Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 * @param Int $product_id Its the Id of product for which traces to be added
	 */
	function updateTraces($update_array = array(), $product_id = 0){
		
		// Fetching all product Ids and Max display order (CP) to update new traces
		$this->db->select('product_id, MAX(`display_order`) as max_disp', FALSE);
		$this->db->group_by('product_id');
		$cp_prod_ids = $this->db->get('products_traces')->result();
		
		// Fetching old traces associated with keurslager products
		$this->db->select('t_id');
		$get_traces = $this->db->get_where('k_prod_traces', array('p_id' => $product_id))->result();
		$old_traces = array();
		if(!empty($get_traces)){
			foreach ($get_traces as $t_id)
				$old_traces[] = $t_id->t_id;
		}
		
		// Trace Ids to delete
		$traces_to_add = array_diff($update_array,$old_traces);
		
		// Trace Ids to add
		$traces_to_del = array_diff($old_traces, $update_array);
		
		$this->db->trans_start();
		
		// Adding Traces
		if(!empty($traces_to_add)){
			
			// Getting Name of traces to insert in CP products
			$this->db->select('t_id,t_name');
			$this->db->where_in('t_id', $traces_to_add);
			$traces_info_to_add = $this->db->get('k_traces')->result();
			
			if(!empty($traces_info_to_add)){
				// Looping through -traces with name- to be added
				foreach ($traces_info_to_add as $t_info_to_add){
					
					// Adding traces in Keurslager Products
					$this->db->insert('k_prod_traces', array('p_id' => $product_id, 't_id' => $t_info_to_add->t_id, 'date_added' => date('Y-m-d H:i:s')));
					
					// Adding traces to the CP products
					if(!empty($cp_prod_ids)){
						foreach ($cp_prod_ids as $cp_prod_id){
							$is_assoc_with_cur_cp_prod = $this->db->get_where('products_traces', array('product_id' => $cp_prod_id->product_id, 'kt_id' => $t_info_to_add->t_id) )->result();
							if(empty($is_assoc_with_cur_cp_prod)){
								$insert_array = array(
										'product_id' => $cp_prod_id->product_id,
										'kp_id' => $product_id,
										'kt_id' => $t_info_to_add->t_id,
										'kt_name' => $t_info_to_add->t_name,
										'display_order' => ($cp_prod_id->max_disp + 1),
										'date_added' => date('Y-m-d H:i:s')
								);
								$this->db->insert('products_traces', $insert_array);
							}
						}
					}
					
				}
			}
			
		}
		
		// Deleting Traces
		if(!empty($traces_to_del)){
			foreach($traces_to_del as $t_to_del){
				$this->db->delete('k_prod_traces', array('p_id' => $product_id, 't_id' => $t_to_del));
				$this->db->delete('products_traces', array('kp_id' => $product_id, 'kt_id' => $t_to_del));
			}
		}
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}
		
	}
		
	/**
	 * This model function is used to update Product's Infos
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 */
	function delete($where_array = array()){
		if(!empty($where_array)){
			return $this->db->delete('k_products', $where_array);
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to delete Product's other data
	 * @param table - table name
	 * @param array $where_array Its the array of cols and vals to used in Where clause
	 */
	function deleteProductdata($table='', $where_array = array()){
		if(!empty($where_array)){
			return $this->db->delete($table, $where_array);
		}else{
			return false;
		}
	}
	
	
	/**
	 * This model function is used to insert Ingredients with respect to specific product
	 * @param array $insert_array Its the array of inerting values
	 * @return mixed Either ID of last supplier or False
	 */
	function insertIngredients($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('k_prod_ingredients', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to insert Traces with respect to specific product
	 * @param array $insert_array Its the array of inerting values
	 * @return mixed Either ID of last supplier or False
	 */
	function insertTraces($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('k_prod_traces', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to insert Allergence with respect to specific product
	 * @param array $insert_array Its the array of inerting values
	 * @return mixed Either ID of last supplier or False
	 */
	function insertAllergence($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('k_prod_allergence', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else{
			return false;
		}
	}
	
	/**
	 * This model function is used to fetch ingredients
	 * @param array $param Array of columns and values to be used in where clause
	 */
	function getIngredients($param = array(), $select = ''){
		$ingredients = array();
		
		if($select != ''){
			$this->db->select($select);
		}	
		else 
		{
			$this->db->select('k_prod_ingredients.* , k_ingredients.ing_id as ing_id, k_ingredients.ing_name as ing_name');
		}	
		
		if(!empty($param))
			$this->db->where($param);
		
		$this->db->join('k_ingredients','k_prod_ingredients.i_id = k_ingredients.ing_id');
		
		$ingredients = $this->db->get('k_prod_ingredients')->result();
		
		
		return $ingredients;
	}
	
	/**
	 * This model function is used to fetch traces
	 * @param array $param Array of columns and values to be used in where clause
	 */
	function getTraces($param = array(), $select = ''){
		$traces = array();
		
		if($select != ''){
			$this->db->select($select);
		}
		else
		{
			$this->db->select('k_prod_traces.* , k_traces.t_id as t_id, k_traces.t_name as t_name');
		}
	
		if($select != ''){
			$this->db->select($select);
		}
	
		if(!empty($param))
			$this->db->where($param);
	
		$this->db->join('k_traces','k_prod_traces.t_id = k_traces.t_id');
		
		$traces = $this->db->get('k_prod_traces')->result();
	
		return $traces;
	}
	
	/**
	 * This model function is used to fetch Allergence
	 * @param array $param Array of columns and values to be used in where clause
	 */
	function getAllergence($param = array(), $select = ''){
		$allergence = array();
		
		if($select != ''){
			$this->db->select($select);
		}
		else
		{
			$this->db->select('k_prod_allergence.* , k_allergence.a_id as a_id, k_allergence.a_name as a_name');
		}
		if(!empty($param))
			$this->db->where($param);
	
		$this->db->join('k_allergence','k_prod_allergence.a_id = k_allergence.a_id');
		
		$allergence = $this->db->get('k_prod_allergence')->result();
	
		return $allergence;
	}
	
	/**
	 * This model function is used to get all allergence
	 */
	function getAllergenceAll(){
		return $this->db->get('k_allergence')->result();
	}
	
	/**
	 * This model function is used to get all allergence
	 */
	function getIngredientsAll(){
		$this->db->order_by('ing_name','ASC');
		return $this->db->get('k_ingredients')->result();		
	}
	
	/**
	 * This model function is used to get all allergence
	 */
	function getTracesAll(){
		$this->db->order_by('t_name','ASC');
		return $this->db->get('k_traces')->result();
	}
	
	/**
	 * Function to get data from given table according to parameters
	 *
	 * @param string $table_name Table Name
	 * @param array $params - conditions
	 * @param int $start - Starting index
	 * @param int $limit - limit
	 * @param string $order_by - column name to do order by
	 * @param string $order - asc or desc
	 */
	function get_data( $table_name=NULL , $params=array(), $start=NULL, $limit=NULL, $order_by=NULL, $order=NULL )
	{
		$this->db->select($table_name.'.*');
	
		if(!empty($params))
		foreach($params as $col=>$val)
			$this->db->where($col,$val);
	
		if($limit && $start)
			$this->db->limit($limit,$start);
		else
		if($limit)
			$this->db->limit($limit);
	
		if($order_by && $order)
			$this->db->order_by($order_by,$order);
	
		$query = $this->db->get($table_name)->result();
	
		//echo $this->db->last_query();
	
		return $query;
	}
	
	/**
	 * Function to ceate new row in given table
	 *
	 * @param string $table_name Tablename
	 * @param array $data Post values
	 * @return integer $row_id
	 */
	function create($table_name = NULL,  $data = array()){
	
		$result = 0;
	
		if(isset($table_name) && $table_name != '' && is_array($data) && !empty($data)){
			$this->db->insert($table_name, $data);
			$result = $this->db->insert_id();
		}
	
		//echo $this->db->last_query();
	
		return $result;
	}
}
?>