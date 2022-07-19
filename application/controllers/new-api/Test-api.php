<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';



/**
 * COMPANY API NEW
 * 
 * @package Company_api_new
 * 
 * This package is an API class, it response the api request came via HTTP of cURL 
 */
class Test-api extends REST_Controller
{
	/**
	 * Defining variable
	 */
	var $company_id;
	
	/**
	 * This is the default constructior
	 */
	function __construct(){

		parent::__construct();
		
	}
	
	/**
	* This function is used to verify company api request
	*/
	public function get_products_get(){
		error_reporting(E_ALL);
		$company_id = 113;
		$is_k_assoc = 0;

		$products = array();
		if($company_id && is_numeric($company_id)){
$this->response("Hello", 200);
			
			$this->db->select('display_fixed');
			$display_fixed_result = $this->db->get_where('general_settings', array('company_id' => $company_id))->result();
			
			$this->db->select('products.*,categories.service_type as category_service_type');
			
			$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
			$this->db->where($where);
			
			$this->db->order_by('pro_display','asc');
			$this->db->order_by('id','asc');
			
			$this->db->join('categories','categories.id = products.categories_id','left');
			
			$products = $this->db->get_where('products', array('products.company_id' => $company_id, 'products.status' => 1, 'products.sell_product_option !=' => '' ))->result();
			
			if(!empty($products)){
				foreach ($products as $key => $product){
					
					if($is_k_assoc){
						$complete = $this->get_fixed_status($product->id,$product->direct_kcp);
						if(!$complete){
							if($display_fixed_result[0]->display_fixed){
								unset($products[$key]);
								continue;
							}
						}
						
						$product->complete = $complete;
					}
				
					/**
					 * Checking if product's image exist
					 */
					/*if($product->image != '' && $product->image != null){
						$products[$key]->image = $this->existing_product_image($product->image);
					}else{
						$products[$key]->image = '';
					}*/
					
					/**
					 * Fetching Extras
					 */
					$products[$key]->grp_arr = $this->get_product_groups($product->id);
					
					/**
					 * Fetching multidiscounts
					 */
					if($product->sell_product_option == 'per_unit'){
						if($product->discount == 'multi'){
							$products[$key]->multi_discount = $this->get_product_multi_discounts($product->id,'0');
						}
					}elseif($product->sell_product_option == 'weight_wise'){
						if($product->discount_wt == 'multi'){
							$products[$key]->multi_discount_wt = $this->get_product_multi_discounts($product->id,'1');
						}
					}elseif($product->sell_product_option == 'per_person'){
						if($product->discount_person == 'multi'){
							$products[$key]->multi_discount_person = $this->get_product_multi_discounts($product->id,'2');
						}
					}elseif($product->sell_product_option == 'client_may_choose'){
						if($product->discount == 'multi'){
							$products[$key]->multi_discount = $this->get_product_multi_discounts($product->id,'0');
						}
						if($product->discount_wt == 'multi'){
							$products[$key]->multi_discount_wt = $this->get_product_multi_discounts($product->id,'1');
						}
					}
					
					/**
					 * Fetching Keurslager Ingredients
					 */
					if($is_k_assoc){
						/*$products[$key]->k_ingredients = $this->get_k_ingredients($product->id);
						$products[$key]->k_traces = $this->get_k_traces($product->id);
						$products[$key]->k_allergence = $this->get_k_allergence($product->id);*/
						/*$k_ingredients = $this->get_k_ingredients($product->id);
						if(!empty($k_ingredients)){
							$ing_str = '';
							$add_comma = true;
							foreach ($k_ingredients as $k_ingredient){
								if(trim($k_ingredient->ki_name) != ''){
									if($k_ingredient->ki_name == '(' || $k_ingredient->ki_name == ')'){
										$ing_str .= '  '.$k_ingredient->ki_name;
										if($k_ingredient->ki_name == '(')
											$add_comma = false;
									}else{
										if($add_comma)
											$ing_str .= ', '.$k_ingredient->prefix.' '.$k_ingredient->ki_name;
										else 
											$ing_str .= $k_ingredient->prefix.' '.$k_ingredient->ki_name;
										$add_comma = true;
									}
								}
							}
							//if($product->ingredients && trim($product->ingredients) != '')
							//	$product->ingredients = $product->ingredients.$ing_str;
							//else 
								$product->ingredients = substr($ing_str,2);
						}
						
						$k_traces = $this->get_k_traces($product->id);
						if(!empty($k_traces)){
							$tra_str = '';
							$add_comma = true;
							foreach ($k_traces as $k_trace){
								if(trim($k_trace->kt_name) != ''){
									if($k_trace->kt_name == '(' || $k_trace->kt_name == ')'){
										$tra_str .= '  '.$k_trace->kt_name;
										if($k_trace->kt_name == '(')
											$add_comma = false;
									}else{
										if($add_comma)
											$tra_str .= ', '.$k_trace->prefix.' '.$k_trace->kt_name;
										else
											$tra_str .= $k_trace->prefix.' '.$k_trace->kt_name;
										$add_comma = true;
									}
								}
							}
							//if($product->traces_of && trim($product->traces_of) != '')
							//	$product->traces_of = $product->traces_of.$tra_str;
							//else
								$product->traces_of = substr($tra_str,2);
							/*foreach ($k_traces as $k_trace){
								$tra_str .= ', '.$k_trace->prefix.' '.$k_trace->kt_name;
							}
							if($product->traces_of != '')
								$product->traces_of = $product->traces_of.$tra_str;
							else
								$product->traces_of = substr($tra_str,2);*/
						// }
						
						if($complete){
							$k_allergence = $this->get_k_allergence($product->id);
							$k_sub_allergence = $this->get_k_sub_allergence($product->id);
							if(!empty($k_allergence)){
								$allrg_str = '';
								$add_comma = true;
								$count = 0;
								foreach ($k_allergence as $k_allerg){
									if(trim($k_allerg->ka_name) != ''){
										++$count;
										//$allrg_str .= ', '.$k_allerg->prefix.' '.$k_allerg->ka_name;
										if($k_allerg->ka_name == '(' || $k_allerg->ka_name == ')'){
											$allrg_str .= '  '.$k_allerg->ka_name;
											if($k_allerg->ka_name == '(')
												$add_comma = false;
										}else{
											if($add_comma)
												$allrg_str .= ', '.$k_allerg->prefix.' '.$k_allerg->ka_name;
											else
												$allrg_str .= $k_allerg->prefix.' '.$k_allerg->ka_name;
											$add_comma = true;
										}
										
										if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
											$a1 = '';
											if(!empty($k_sub_allergence)){
												$a1 .= ' (';
												foreach ($k_sub_allergence as $k_sub_allerg){
													if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
														$a1 .=  $k_sub_allerg->sub_ka_name.', ';
													}
												}
												$a1 = rtrim($a1,', ');
												$a1 .= ')';
												$a1 = str_replace('()', '', $a1);
											}
											$allrg_str .= $a1;
										}
									}
								}
								if(strpos($allrg_str,'Melk') !== false && strpos($allrg_str,'Lactose') !== false){
									$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
									$allrg_str = str_replace(',  Lactose', '', $allrg_str);
								}
								//if($product->allergence && trim($product->allergence) != '')//product 5340 in cedcoss shop was prefixing 0
								//	$product->allergence = $product->allergence.$allrg_str;
								//else
									$product->allergence = substr($allrg_str,2);
							}
							else{
								$product->allergence = "Geen";
							}
						}
						else{
							$product->allergence = "Gelieve na te vragen";
						}
					}
				}
			}
		}

		$return_array = array('result' => $products);
		$this->response($return_array, 200);
		// return array_values($products);
	}
	
			
}
