<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Keurslager Association
 * 
 * This is a controller for fetching Keurslager Products and related things
 * 
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 * @package Keurslager
 */
class Keurslager extends CI_Controller{
	
	var $company_id = '';
	
	function __construct(){
	
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('cookie');
		
		$this->load->library('session');
		
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mcalender');
		$this->load->model('kcp/mproducts');
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}

		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		/*$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}*/
		
		//$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}
	
	/**
	 * This function is used to fetch and show all saved report on the server for current company
	 */
	function products(){
				
		$this->load->model('kcp/msuppliers');
		
		$data['products'] = $this->mproducts->get('s_name,kp_id,kp_name,kp_description');
		$data['suppliers'] = $this->msuppliers->get('s_id,s_name');
		
		$this->load->view('cp/k_products', $data);
	}
	
	/**
	 * This function is used to fetch products via ajax request
	 */
	function getAjaxProducts(){
	
		$s_id = $this->input->post('s_id');
		if($s_id)
			$products = $this->mproducts->get('s_name,kp_id,kp_name,kp_description', array('k_products.kp_s_id' => $s_id));
		else
			$products = $this->mproducts->get('s_name,kp_id,kp_name,kp_description');
		echo json_encode($products);
	}
	
	/**
	 * This function takes parameters via post request and adds keurslager product into current admin's account
	 */
	function add_k_prods(){
		$cat_id = $this->input->post('cat_id');
		$subcat_id = $this->input->post('subcat_id');
		$kp_id = $this->input->post('kp_id');
		
		if(!$cat_id || !$subcat_id || !$kp_id)
			echo false;
		else{
			$kp_detail = $this->mproducts->get('',array('kp_id' => $kp_id));
			
			if(!empty($kp_detail)){
				$kp_detail = $kp_detail[0];
				
				$insert_array = array(
					'company_id' => $this->company_id,
					'categories_id' => $cat_id,
					'subcategories_id' => $subcat_id,
					'proname' => $kp_detail->kp_name,
					'prodescription' => $kp_detail->kp_description,
					'sell_product_option' => 'per_unit',
					'procreated' => date('Y-m-d H:i:s'),
					'direct_kcp' => 1
				);
				
				if($kp_detail->image != ''){
					$image_file = file_get_contents(base_url().'assets/kcp/images/products/'.$kp_detail->image);
					file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/product/'.$kp_detail->image, $image_file);
					$insert_array['image'] = $kp_detail->image;
				}
				
				if($this->db->insert('products', $insert_array)){
					
					$prod_id = $this->db->insert_id();
					
					// Now inserting Ingredients
					$ingredients = $kp_detail->ingredients;
					if(!empty($ingredients)){
						$insert_array = array(
								'product_id' => $prod_id,
								'kp_id' => $kp_detail->kp_id,
								'ki_id' => 0,
								'ki_name' => $kp_detail->kp_name,
								'display_order' => 1,
								'date_added' => date('Y-m-d H:i:s')
						);
						$this->db->insert('products_ingredients', $insert_array);
						foreach ($ingredients as $key => $ingredient){
							$insert_array = array(
								'product_id' => $prod_id,
								'kp_id' => $ingredient->p_id,
								'ki_id' => $ingredient->i_id,
								'ki_name' => $ingredient->ing_name,
								'display_order' => ($key+2),
								'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_ingredients', $insert_array);
						}
					}
					
					// Now inserting Traces
					$traces = $kp_detail->traces;
					if(!empty($traces)){
						$insert_array = array(
								'product_id' => $prod_id,
								'kp_id' => $kp_detail->kp_id,
								'kt_id' => 0,
								'kt_name' => $kp_detail->kp_name,
								'display_order' => 1,
								'date_added' => date('Y-m-d H:i:s')
						);
						$this->db->insert('products_traces', $insert_array);
						foreach ($traces as $key => $trace){
							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $trace->p_id,
									'kt_id' => $trace->t_id,
									'kt_name' => $trace->t_name,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_traces', $insert_array);
						}
					}
					
					// Now inserting Allergence
					$allergences = $kp_detail->allergence;
					if(!empty($allergences)){
						$insert_array = array(
								'product_id' => $prod_id,
								'kp_id' => $kp_detail->kp_id,
								'ka_id' => 0,
								'ka_name' => $kp_detail->kp_name,
								'display_order' => 1,
								'date_added' => date('Y-m-d H:i:s')
						);
						$this->db->insert('products_allergence', $insert_array);
						foreach ($allergences as $key => $allergence){
							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $allergence->p_id,
									'ka_id' => $allergence->a_id,
									'ka_name' => $allergence->a_name,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_allergence', $insert_array);
						}
					}
					
					echo $prod_id;
				}else{
					echo false;
				}
			}else{
				echo false;
			}
		}
	}
	
	/**
	 * This function is used to fetch ingredients of particluar product
	 * @param int $prod_id It is the ID of product for which ingredients are to be fetched
	 */
	function get_ing_traces_allergence($prod_id = 0){
		$ingredients = array();
		$traces = array();
		$allergence = array();
		$products = array();
		if($prod_id){
			$products = $this->mproducts->get('kp_id,kp_name', array('k_products.kp_id' => $prod_id));
			$ingredients = $this->mproducts->getIngredients(array('k_prod_ingredients.p_id' => $prod_id), 'k_prod_ingredients.p_id, k_ingredients.ing_id, k_ingredients.ing_name');
			$traces = $this->mproducts->getTraces(array('k_prod_traces.p_id' => $prod_id), 'k_prod_traces.p_id, k_traces.t_id, k_traces.t_name,');
			$allergence = $this->mproducts->getAllergence(array('k_prod_allergence.p_id' => $prod_id), 'k_prod_allergence.p_id, k_allergence.a_id, k_allergence.a_name');
		}
		
		$response = array( 'product' => $products, 'ingredients' => $ingredients, 'traces' => $traces, 'allergence' => $allergence);
		echo json_encode($response);
	}
}			

?>