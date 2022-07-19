<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This controller is for Share Product Module between companies
 *
 * @package         OBS
 * @category        Controller
 * @author          Abhay Hayaran <abhayhayaran@cedcoss.com>, Ankush Katiyar <ankushkatiyar@cedcoss.com>
 */
class Shared extends CI_Controller{
	
	function __construct(){
	
		parent::__construct();
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}
		
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->load->model('Mcompany');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}
	
	/**
	 * This function is used to get list of products, categories, subcategories
	 * @name index
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function index()
	{
		$this->ljist();
	}
	function ljist(){
		$this->load->model('Mcategories');
		$this->load->model('Mproducts');
		$this->load->model('Mcalender');
		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();
		$url_variable = $this->uri->uri_to_assoc(4);
			
		$data['category_data'] = $this->Mcategories->get_categories();
		if(array_key_exists('category_id', $url_variable)){
			$data['cat_id']=$url_variable['category_id'];
			$this->load->model('Msubcategories');
			$data['subcategory_data']=$this->Msubcategories->get_sub_category($url_variable['category_id']);
			
			$products2 = array();
			
			$data['sub_cat_id']='-1';
			$products1 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],'-1',1);//getting product of related to category
			
			$this->db->select('id');
			$subcategory_data = $this->db->get_where('subcategories',array('categories_id'=>$url_variable['category_id']))->result_array();
			
			$id = array();
			foreach($subcategory_data as $var) {
				$id[] = $var['id'];
			}
			if(!empty($id)){
				$products2 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],$id, 1);//getting product of related to category
			}
			$data['products'] = array_merge($products1,$products2);
			
		} else {
			$data['products'] = $this->Mproducts->get_products_to_assign('','',1);
		}
		$data['category_data']=$this->Mcategories->get_categories();
			
		$this->db->select(array( 'subcategories.id','subcategories.subname','subcategories.categories_id'));
		$this->db->join('categories','subcategories.categories_id=categories.id');
		$this->db->where('categories.company_id',$this->company_id);
		$data['subcategory_data'] = $this->db->get('subcategories')->result_array();
	
		$data['content'] = 'cp/shared_products';
		$this->load->view('cp/cp_view', $data);
	}
	
	/**
	 * This function is used to get list of companies except current company
	 * @name get_all_companies
	 * @param $search_str
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function get_all_companies($search_str = ''){
		if($search_str != ''){
			$company_list = $this->Mcompany->get_company_name($search_str);
			echo json_encode($company_list);
		}
	}
	
	/**
	 * This function is used to insert details of product which is shared to other companies
	 * @name update_shared_product
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function update_shared_product(){
		$details_array = $this->input->post('details_arr');
		$product_id = $this->input->post('product_id');
		$prod_share_status = false;
		
		if (!empty($details_array))	{
			foreach ($details_array as $key=>$val){
				
				$company_name = $val['company'];
				if ($company_name != ''){
					
					$this->db->select('id');
					$comp_result = $this->db->get_where('company',array('company_name'=>$company_name))->result_array();
					if (!empty($comp_result)){
						
						$details_array[$key]['company'] = $comp_result[0]['id'];
						$alr_shared_prod = $this->db->get_where('products_shared',array('proid'=>$details_array[$key]['product_id'], 'from_comp_id'=>$this->company_id, 'to_comp_id'=>$details_array[$key]['company']))->result_array();
						if (empty($alr_shared_prod)){
							
							$data = array(
									'proid' => $details_array[$key]['product_id'],
									'share' => $details_array[$key]['share_option'],
									'from_comp_id' => $this->company_id,
									'to_comp_id' => $details_array[$key]['company'],
									'remark' => $details_array[$key]['remark'],
									'datetime' => date('Y-m-d',time())
							);
							$this->db->insert('products_shared', $data);
							$prod_share_status = true;
						}
					}
				}
			}
			echo json_encode(array('product_id' => $product_id,'prod_share_status' => $prod_share_status));
		}
	}
	
	/**
	 * This function is used to get details of shared product
	 * @name get_shared_product_details
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function get_shared_product_details(){
		$product_id = $this->input->post('product_id');
		$product_details = array();
		if($product_id)
		{
			$product_details = $this->db->get_where('products_shared',array('proid'=>$product_id))->result_array();
			if (!empty($product_details)){
				foreach ($product_details as $key=>$val){
					$this->db->select('company_name');
					$company_name = $this->db->get_where('company',array('id'=> $val['to_comp_id']))->result_array();
					$product_details[$key]['to_company_name'] = $company_name[0]['company_name'];
				}
			}
		}
		echo json_encode($product_details);
	}
	
	/**
	 * This function is used to delete the product which shared to other company
	 * @name delete_shared_pro_comp
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function delete_shared_pro_comp(){
		$pro_id = $this->input->post('product_id');
		$to_company_id = $this->input->post('to_comp_id');
		
		if ($pro_id && $to_company_id){
			$this->load->model('Mproducts');
			
			$res = $this->db->delete('products_shared',array('proid'=>$pro_id,'to_comp_id'=>$to_company_id,'from_comp_id'=>$this->company_id));
			
			$this->db->select('id');
			$prod_status = $this->db->get_where('products',array('parent_proid'=>$pro_id,'company_id'=>$to_company_id))->result_array();
			if (!empty($prod_status)){
				$this->Mproducts->delete_product($prod_status[0]->id);
			}
			
			$this->db->select('proid');
			$prod_count = $this->db->get_where('products_shared',array('proid'=>$pro_id))->row_array();
			if(!empty($prod_count))	{
				echo json_encode (array("success"=>true));
			}
			else{
				echo json_encode (array("success"=>false));
			}
		}
		else{
			echo json_encode (array("success"=>false));
		}
	}
	
	/**
	 * This function is used to assign product to other company
	 * @name assign_share_product
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function assign_share_product(){
		$this->load->model('Mproducts');
		
		$parent_product_id = $this->input->post('parent_product_id');
		$cat_id = $this->input->post('cat_id');
		$sub_cat_id = $this->input->post('sub_cat_id');
		$product_name = $this->input->post('product_name');
		
		$this->db->select('share');
		$share_val = $this->db->get_where('products_shared',array('proid'=>$parent_product_id,'to_comp_id'=>$this->company_id))->result_array();
		
		if (isset($share_val) && $share_val[0]['share'] == "recipe"){
			$product_update = array('parent_proid'=>$parent_product_id,'categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id,'proname'=>$product_name,'direct_kcp'=>0,'company_id'=>$this->company_id);
		}
		else {
			$product_update = array('parent_proid'=>$parent_product_id,'categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id,'proname'=>$product_name,'direct_kcp'=>1,'company_id'=>$this->company_id);
		}
		
		$product_clone = $this->Mproducts->add_product_shared($parent_product_id,$product_update);
		$from_comp_id = $product_clone['from_company'];
		
		$this->db->where(array('proid'=>$parent_product_id,'from_comp_id'=>$from_comp_id,'to_comp_id'=>$this->company_id));
		$this->db->update('products_shared',array('status'=>"1"));
	}
	
	/**
	 * This function is used to reject product for shared
	 * @name reject_share_product
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function reject_share_product(){
		$proid = $this->input->post('proid');
		$from_comp_id = $this->input->post('from_comp_id');
		$this->db->where(array('proid'=>$proid,'from_comp_id'=>$from_comp_id,'to_comp_id'=>$this->company_id));
		$this->db->update('products_shared',array('status'=>"2"));
	}
}