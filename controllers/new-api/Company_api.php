<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Company_api extends REST_Controller
{
	var $company_id;
	
	function __construct(){

		parent::__construct();
	}
	
	/*
	*function to verify company api request
	*/
	
	function verify_api_request_get()
	{
	    $this->load->library('api_verification');
		$this->load->library('user_agent');
			
		$verification_data = array( 
		                            'api_id' => $this->get('api_id'),
									'hash' => $this->get('hash'),
									'timestamp' => $this->get('timestamp'),
									'domain' => $this->agent->referrer()  //$_SESSION['HTTP_REFERER']
								  );
        
		$this->company_id = $this->api_verification->authenticate_login($verification_data);    
		
		if( !$this->company_id )
		{		
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );			
		}	
		else
		{
			//$request_url = $_SERVER['REQUEST_URI'];
		    //$this->db->insert('log_api',array('request_uri'=>$request_url,'date_time'=>time(),'ip_address'=>$this->input->ip_address()));
						
			$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $this->company_id ), 200 );
			
		}
		
		exit;
	}
	
	function company_settings_post()
	{
		$company_id = $this->input->post( 'company_id' );
		
		if($company_id)
		{			
		    /*$this->db->select( 'id, company_id, language_id, emailid, orderreceived_msg, ok_msg, hold_msg, completedpickup_msg, completeddelivery_msg, shop_offline, shop_offline_message, delivery_service, pickup_service, disable_price, message_front, show_message_front, shop_visible, shop_password, shop_visible_message, category_feature, hide_intro, theme_id, pay_option, online_payment, paypal_address, apply_tax, tax_percentage, tax_amount, payment_instructions, pay_complete_msg, pay_incomplete_msg, hide_bp_intro, pay_methods, company_account_num, set_terms_and_conditions, terms_and_conditions, company_gallery_imgs, parent_id, role, type_id, ac_type_id, packages_id, company_name, company_slug, company_img, company_desc, company_fb_url, vat, first_name, last_name, email, phone, website, address, zipcode, city, username, password, admin_remarks, expiry_date, registration_date, earnings_year, registered_by, approved, link, status, invoice_made, payment_received, email_ads, footer_text, themes.id AS theme_id' );*/
		
			$this->db->select( '*, themes.id AS theme_id' );
			$this->db->where( 'general_settings.company_id', $company_id);
	
			$this->db->join('company', 'company.id = general_settings.company_id');
			$this->db->join('themes', 'themes.id = general_settings.theme_id');
			$this->db->join('language', 'language.id = general_settings.language_id');			
			$this->db->join('company_css', 'company_css.theme_id = general_settings.theme_id AND company_css.company_id = general_settings.company_id AND company_css.use_own_css = 1','left');
			
			$general_settings = $this->db->get('general_settings')->row_array();
			
			unset($general_settings['username']);
			unset($general_settings['password']);
			unset($general_settings['emailid']);
			unset($general_settings['email']);
			
			if( !empty($general_settings) )
			{
				$this->response( array('error' => 0, 'message'=> '' , 'data' => $general_settings ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Error - No company settings found !'), 'data' => '' ), 404 );
			}
		}
		else
		{		
			$this->response( array('error' => 1, 'message'=>_('Error - Did\\\'t get the Company ID.'), 'data' => '' ), 404 );
		}	
		
		exit;
	}
	
	function get_categories_post()
	{
		$company_id = $this->input->post('company_id');
		$service_type = $this->input->post('service_type');
		
		$query = '';
		
		$query = " Select * FROM `categories` WHERE ( `company_id` = ".$company_id." AND `status` = 1 ) ";
		
		//$this->db->where( 'company_id', $company_id );
		//$this->db->where( 'status', 1 );
		
		if( !$service_type || $service_type == 'both' )
		{
		}
		elseif( $service_type == 'pickup' )
		{
			$query .= " AND ( `service_type` = '1' OR `service_type` = '0' ) ";
			
			//$this->db->where( 'service_type', '1' );
			//$this->db->or_where( 'service_type', '0' );
		}
		elseif( $service_type == 'delivery' )
		{
			$query .= " AND ( `service_type` = '2' OR `service_type` = '0' ) ";
			
			//$this->db->where( 'service_type', '2' );
			//$this->db->or_where( 'service_type', '0' );
		}
		
		$query .= " ORDER BY `order_display` ASC ";
		
		//$this->db->order_by( 'order_display', 'ASC' );
		//$categories = $this->db->get('categories')->result();
		
		$categories = $this->db->query( $query )->result();

		if( !empty($categories) )
		{
			$categories = $this->check_category_image_exist($categories);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $categories ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No categories found !'), 'data' => '' ), 404 );
			//$this->response( array('error' => 1, 'message'=>$query, 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	/**
	 * This function is used to check whether category image saved in database is actually exists in desired location
	 * @param array $checking_array An input array of products
	 * @return array Finalized array.
	 */
	function fetch_search_product_post(){
	
		$pro_query = $this->input->post('query');
		$company_id = $this->input->post('company_id');
		
		$this->db->like( 'proname', $pro_query );
		$this->db->where( 'company_id', $company_id );
		$search_products = $this->db->get('products')->result();
		$this->response( array('error' => 0, 'message'=> '', 'data' => $search_products ), 200 );
	}
	
	
	function servicetype_product_post()
	{
		$service_type = $this->input->post('service_type');
		$company_id = $this->input->post('company_id');
		
		$query = '';
		
		$query = " Select * FROM `categories` WHERE ( `company_id` = ".$company_id." AND `status` = 1 ) ";
		
		//$this->db->where( 'company_id', $company_id );
		//$this->db->where( 'status', 1 );
		
		if( !$service_type || $service_type == 'both' )
		{
		}
		elseif( $service_type == 'pickup' )
		{
			$query .= " AND ( `service_type` = '1' OR `service_type` = '0' ) ";
				
			//$this->db->where( 'service_type', '1' );
			//$this->db->or_where( 'service_type', '0' );
		}
		elseif( $service_type == 'delivery' )
		{
			$query .= " AND ( `service_type` = '2' OR `service_type` = '0' ) ";
				
			//$this->db->where( 'service_type', '2' );
			//$this->db->or_where( 'service_type', '0' );
		}
		
		$query .= " ORDER BY `order_display` ASC ";
		
		//$this->db->order_by( 'order_display', 'ASC' );
		//$categories = $this->db->get('categories')->result();
		
		$product_cat_subcat = $this->db->query( $query )->result();
		foreach($product_cat_subcat as $k=>$product_cat)
		{
			$product_cat = (array)$product_cat;
			$categories_id = $product_cat['id'];
			$this->db->where( 'company_id', $company_id );
			$this->db->where( 'status', 1 );
			$this->db->where( 'categories_id', $categories_id );
			$this->db->order_by( 'pro_display', 'ASC' );
			
			$category_products = $this->db->get('products')->result();
			
			$all_product[] = $category_products;
			
			$this->db->where( 'categories_id', $categories_id );
			$this->db->where( 'status', 1 );
			
			$this->db->order_by( 'suborder_display', 'ASC' );
			
			$subcategories = $this->db->get('subcategories')->result();
			$subcategory = $subcategories->data;
			foreach($subcategory as $key=>$subcategory_product)
			{
				$subcategory_product = (array)$subcategory_product;
				$this->db->where( 'company_id', $company_id );
				//$this->db->where( 'categories_id', $categories_id );
				$this->db->where( 'subcategories_id', $subcategory_product['id'] );
				$this->db->where( 'status', 1 );
				
				$this->db->order_by( 'pro_display', 'ASC' );
				
				$subcategory_products = $this->db->get('products')->result();
				$all_product[] = $subcategory;
			}
		} 
		
		$this->response( array('error' => 0, 'message'=> 'all_product', 'data' => $category_products ), 200 );
			
	}
	
	function get_subcategories_post()
	{
		$categories_id = $this->input->post('categories_id');
				
		$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'status', 1 );
		
		$this->db->order_by( 'suborder_display', 'ASC' );
		
		$subcategories = $this->db->get('subcategories')->result();
		
		if( !empty($subcategories) )
		{
			$subcategories = $this->check_subcategory_image_exist($subcategories);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $subcategories ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No sub-categories found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_category_products_post()
	{
		$company_id = $this->input->post('company_id');
		$categories_id = $this->input->post('categories_id');
		$subcategories_id = $this->input->post('subcategories_id');
		
		$this->db->where( 'company_id', $company_id );		
		$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'subcategories_id', $subcategories_id );
		$this->db->where( 'status', 1 );
		
		$this->db->order_by( 'pro_display', 'ASC' );
		
		$category_products = $this->db->get('products')->result();
		
		$sorted_p = array();
		$unsorted_p = array();
		foreach($category_products as $prod) {
			if($prod->pro_display != 0){
				$sorted_p[] = $prod;
			}else{
				$unsorted_p[] = $prod;
			}
		
		}
		$category_products = array_merge($sorted_p,$unsorted_p);
		
		if( !empty($category_products) )
		{
			$category_products = $this->check_products_image_exist($category_products);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $category_products ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No products found in this category !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_subcategory_products_post()
	{
		$company_id = $this->input->post('company_id');
		//$categories_id = $this->input->post('categories_id');
		$subcategories_id = $this->input->post('subcategories_id');
		
		$this->db->where( 'company_id', $company_id );		
		//$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'subcategories_id', $subcategories_id );
		$this->db->where( 'status', 1 );
		
		$this->db->order_by( 'pro_display', 'ASC' );
		
		$subcategory_products = $this->db->get('products')->result();
		
		$sorted_p = array();
		$unsorted_p = array();
		foreach($subcategory_products as $prod) {
			if($prod->pro_display != 0){
				$sorted_p[] = $prod;
			}else{
				$unsorted_p[] = $prod;
			}
				
		}
		$subcategory_products = array_merge($sorted_p,$unsorted_p);
		
		if( !empty($subcategory_products) )
		{
			$subcategory_products = $this->check_products_image_exist($subcategory_products);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $subcategory_products ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No products found in this sub-category !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_products_post()
	{
		$post = $this->input->post();
		
		$this->db->where( $post );		
		$this->db->where( 'status', 1 );		
		$this->db->order_by( 'pro_display', 'ASC' );
		
		$products = $this->db->get('products')->result();
		
		if( !empty($products) )
		{
			$products = $this->check_products_image_exist($products);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $products ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No products found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_product_details_post()
	{
		$product_id = $this->input->post('product_id');
		
		$this->db->where( 'id', $product_id );		
		$product = $this->db->get('products')->row();
		
		if( !empty($product) )
		{
			$product = $this->check_product_image_exist($product);
			
			$this->response( array('error' => 0, 'message'=> '', 'data' => $product ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No products details found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_product_multi_discounts_post()
	{
		$product_id = $this->input->post('product_id');
		$discount_type = $this->input->post('discount_type');
				
		if( ($discount_type == 0 || $discount_type == 1 || $discount_type == 2) && $discount_type != '' )
		{
			$this->db->where( 'type', $discount_type );		
		}
		
		$this->db->where( 'products_id', $product_id );		
		$products_discount = $this->db->get('products_discount')->result();
		
		if( !empty($products_discount) )
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $products_discount ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No product discount found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_product_groups_post()
	{
		
		
		$product_id = $this->input->post('product_id');
		$this->db->where( 'groups_products.products_id', $product_id );		
		$this->db->join('groups', 'groups.id = groups_products.groups_id');
		$this->db->order_by( 'groups_products.groups_id', 'ASC' );
		$this->db->order_by( 'groups_products.type', 'ASC' );
		
		$product_grps = $this->db->get('groups_products')->result();
		
		if( !empty($product_grps) )
		{
			
			
			
		    $grps_arr = array();
			$hold_grp_id = '';
			$index = -1;
			
			foreach( $product_grps as $grp )
			{
			    if( $hold_grp_id != $grp->groups_id )
				{
				    $hold_grp_id = $grp->groups_id;
					$index = $index+1;
					
					$grps_arr[$index] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );
					
				}
				elseif( $hold_grp_id == $grp->groups_id )
				{
					$grps_arr[$index]['attributes_arr'][] = array( $grp->attribute_name, $grp->attribute_value );
				}
			}
		
			$this->response( array('error' => 0, 'message'=> '', 'data' => $grps_arr ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No product groups found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_countries_post()
	{
		$countries = $this->db->get('country')->result();
		
		if( !empty($countries))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $countries ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No countries found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}	
	
	function get_companies_post()
	{
		$companies = array();
		$where_arr = $this->input->post();
				
		if( !empty($where_arr) )
		  foreach( $where_arr as $col => $val )
		    $this->db->where( $col, $val );
		  
		$companies = $this->db->get('company')->result();

		if( !empty($companies) )
		{
			$this->response( array('error' => 0, 'message'=> '' , 'data' => $companies ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Error - No companies found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_company_settings_post()
	{
		$company_id = $this->input->post( 'company_id' );
		
		if($company_id)
		{			
			$this->db->where( 'general_settings.company_id', $company_id);	
			$this->db->join('company', 'company.id = general_settings.company_id');			
			$general_settings = $this->db->get('general_settings')->row();
			
			if( !empty($general_settings) )
			{
				$this->response( array('error' => 0, 'message'=> '' , 'data' => $general_settings ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Error - No company settings found !'), 'data' => '' ), 404 );
			}
		}
		else
		{		
			$this->response( array('error' => 1, 'message'=>_('Error - Did\\\'t get the Company ID.'), 'data' => '' ), 404 );
		}	
		
		exit;
	}
	
	function get_discount_card_setting_post()
	{
		$company_id = $this->input->post( 'company_id' );
	
		if($company_id)
		{
			$this->db->select('general_settings.activate_discount_card');
			$this->db->select('general_settings.discount_card_message');			
			$this->db->where( 'general_settings.company_id', $company_id);
			$this->db->join('company', 'company.id = general_settings.company_id');
			$general_settings = $this->db->get('general_settings')->row();
				
			if( !empty($general_settings) )
			{
				$this->response( array('error' => 0, 'message'=> '' , 'data' => $general_settings ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Error - No company settings found !'), 'data' => '' ), 404 );
			}
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Error - Did\\\'t get the Company ID.'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	function get_days_post()
	{
	    $where_arr = $this->input->post();
		
		if( !empty($where_arr) )
		  foreach( $where_arr as $col => $val )
		    $this->db->where( $col, $val );
		  
		$days = $this->db->get('days')->result();
		
		if( !empty($days))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $days), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No days found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_order_settings_post()
	{
		$where_arr = $this->input->post();
		
		if( !empty($where_arr) )
		  foreach( $where_arr as $col => $val )
		    $this->db->where( 'order_settings.'.$col, $val );
			  
		$order_settings = $this->db->get('order_settings')->row();
		
		if( !empty($order_settings))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $order_settings), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No order settings found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_pre_assigned_holdays_post()
	{
		$company_id = $this->input->post('company_id');
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		$day = $this->input->post('day');
		
		if($year && $month && $company_id && is_numeric($year) && is_numeric($month) && is_numeric($company_id)){
			$this->db->select('calendar_country');
			$calendar_of_country = $this->db->get_where('general_settings',array('company_id'=>$company_id))->result_array();
			
			//$this->db->where('day >=',$day);
			$this->db->where('month >=',$month);
			$this->db->where('year >=',$year);
			$holidays = $this->db->get($calendar_of_country['0']['calendar_country'])->result_array();
			
			if( !empty($holidays))
			{
				$this->response( array('error' => 0, 'message'=> '', 'data' => $holidays), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('No holiday found !'), 'data' => '' ), 404 );
			}
		}else{
			$this->response( array('error' => 1, 'message'=>_('Not found necessary data !'), 'data' => '' ), 404 );
		}		
		
		exit;
	}
	
	function get_pickup_delivery_timings_post()
	{
		$where_arr = $this->input->post();
		  
		$this->db->join('days','pickup_delivery_timings.day_id = days.id');
		
		if(!empty($where_arr))
		foreach($where_arr as $col=>$val)
		  $this->db->where('pickup_delivery_timings.'.$col,$val);
		
		$this->db->order_by('pickup_delivery_timings.day_id','ASC');
		$pickup_delivery_timings = $this->db->get('pickup_delivery_timings')->result();
		
		if( !empty($pickup_delivery_timings))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $pickup_delivery_timings), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No pickup delivery time settings found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_provinces_post()
	{
		$company_id = $this->input->post( 'company_id' );
		
		/*$this->db->where( 'company_id', $company_id);	
		$delivery_areas = $this->db->get('delivery_areas')->result();*/
		//$delivery_areas = $this->db->get('states')->result();
		$delivery_areas = array();
		
		$state_ids = $this->db->query("SELECT * FROM `company_delivery_areas` GROUP BY `state_id` HAVING `company_id` = ".$company_id)->result();
		
		if(!empty($state_ids)){
			$stateIds = array();
			foreach($state_ids as $state_id){
				$stateIds[] = $state_id->state_id;
			}
			
			$this->db->where_in('state_id', $stateIds);
			$delivery_areas = $this->db->get("states")->result();
		}
		
		
		if( !empty($delivery_areas))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $delivery_areas), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No provinces found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_cities_post()
	{
	    $delivery_areas_id = $this->input->post('delivery_areas_id');
		$company_id = $this->input->post('company_id');
		/*$this->db->where( 'delivery_areas_id', $delivery_areas_id);	
		$delivery_settings = $this->db->get('delivery_settings')->result();*/
	    
	    $this->db->join("postcodes","postcodes.id = company_delivery_areas.postcode_id");
	    $this->db->where( 'company_delivery_areas.state_id', $delivery_areas_id);
	    $this->db->where( 'company_delivery_areas.company_id', $company_id);
	    $delivery_settings = $this->db->get('company_delivery_areas')->result();
		
		if( !empty($delivery_settings))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $delivery_settings), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_city_details_post()
	{
		$id = $this->input->post('id');
		
		/*$this->db->where( 'id', $id);	
		$city_details = $this->db->get('delivery_settings')->row();*/
		$this->db->where( 'id', $id);
		$city_details = $this->db->get('postcodes')->row();
		
		if( !empty($city_details))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $city_details), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_city_state_detail_post(){
		
		$post_code = $this->input->post("postCode");
		$companyId = $this->input->post("company_id");
		
		$this->db->select("postcodes.*,states.state_name");
		$this->db->join("states","states.state_id = postcodes.state_id");
		$postcode_info = $this->db->get_where('postcodes',array( "id" => $post_code ))->result_array();
		$city_details = array();
		if(!empty($postcode_info)){
			$post_code_id = $postcode_info['0']['id'];
			$city_details['state_id'] = $state_id = $postcode_info['0']['state_id'];
			$city_details['area_name'] = $postcode_info['0']['area_name'];
			$city_details['post_code'] = $postcode_info['0']['post_code'];
			$city_details['state_name'] = $postcode_info['0']['state_name'];
			
			$company_delivery_info = $this->db->get_where("company_delivery_areas",array("company_id" => $companyId, "state_id" => $state_id, "postcode_id" =>$post_code_id ))->result_array();
			
			if(!empty($company_delivery_info)){
				$this->response( array('error' => 0, 'message'=> '', 'data' => $city_details), 200 );
			}else{
				$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
			}
		}else{
			$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
		}
		/*$this->db->select("postcodes.*,states.state_name");
		$this->db->join("postcodes","postcodes.id = company_delivery_areas.postcode_id");
		$this->db->join("states","states.state_id = company_delivery_areas.state_id");
		
		$this->db->where( 'postcodes.post_code', $post_code);
		$this->db->where( 'company_delivery_areas.company_id', $companyId);
		$city_details = $this->db->get('postcodes')->result();
		 
		if( !empty($city_details))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $city_details), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
		}*/
		
		exit;
		
	}
	
	function get_section_design_settings_post()
	{
		$where_arr = $this->input->post();
		  		
		if(!empty($where_arr))
		foreach($where_arr as $col=>$val)
		  $this->db->where( $col, $val );
		
		$this->db->order_by('section_id','ASC');
		$section_designs = $this->db->get('section_designs')->result();
		
		if( !empty($section_designs))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $section_designs), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No settings found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function chk_restricted_timings_post(){
		$date = $this->input->post('date');
		$company_id = $this->input->post('company_id');
		
		$this->db->select('time_restriction_p');
		$order_setting = $this->db->get_where('order_settings',array('company_id'=>$company_id))->result_array();
		if(!empty($order_setting)){
			$time_to_restrict = array();
			$time_frame = $order_setting['0']['time_restriction_p'];
			$number_of_frame = ($time_frame/5);
			
			$date = str_replace('/','-',$date);
			$timestamp = strtotime($date);
			$pickup_date = date('Y-m-d',$timestamp);
			
			$orders = $this->db->get_where('orders',array('company_id'=>$company_id,'order_pickupdate'=>$pickup_date))->result_array();
			if(!empty($orders)){
				foreach($orders as $order){
					$time_stamp = $order['order_pickuptime'];
					$time_to_restrict[] = $time_stamp;
					for($i = 1; $i < $number_of_frame ; $i++){
						$time_to_restrict[] = date('H:i',strtotime($time_stamp.'+'.(5*$i).' minutes'));
					}					
				}
				//$this->response( array('error' => 0, 'message'=>'', 'data' => $time_to_restrict ), 200 );
			}	
			$this->response( array('error' => 0, 'message'=>'', 'data' => $time_to_restrict ), 200 );
		}else{
			$this->response( array('error' => 1, 'message'=>_('No settings found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	function get_products_search_post()
	{
		$company_id = $this->input->post('company_id');
		$search_value = $this->input->post('search_value');
	
		//$this->db->where( 'company_id', $company_id );
		//$this->db->where( 'status', 1 );
		$this->db->where( 'company_id = "'.$company_id.'" AND status = 1 AND (proname LIKE "%'.$search_value.'%" OR prodescription LIKE "%'.$search_value.'%")');
		
		$this->db->order_by( 'pro_display', 'ASC' );
	
		$searched_products = $this->db->get('products')->result();
	
		if( !empty($searched_products) )
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $searched_products ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No products found !'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	function get_related_products_post()
	{
		$company_id = $this->input->post('company_id');
		$products_ids = $this->input->post('products_ids');
		$num_of_products = $this->input->post('num_of_products');
		$products_ids = substr($products_ids,0,-1);
		$products_ids = explode("#" , $products_ids);
		$products = array();
		
		if(count($products_ids) > 0){
			$limit = floor($num_of_products/count($products_ids));
			
			$this->db->select('id,categories_id,subcategories_id');
			$this->db->where_in("id",$products_ids);
			$products_array = $this->db->get("products")->result_array();

			$total_products = 0;
			$cat_subcat_id = array();
			if(!empty($products_array)){
				foreach($products_array as $product){
					$this->db->order_by('id', 'RANDOM');
					$this->db->limit($limit);
					//$this->db->where("company_id",$company_id);
					$this->db->where("categories_id",$product['categories_id']);
					//$this->db->where("subcategories_id",$product['subcategories_id']);
					$this->db->where_not_in("id",$products_ids);
					$products[] = $prod_array = $this->db->get('products')->result();
					$total_products = $total_products + count($prod_array);
					foreach($prod_array as $product){
						$products_ids[] = $product->id;
					}
					//$cat_subcat_id[$product['id']] = array($product['categories_id'],$product['subcategories_id']);
				}
			}
			
			if($total_products < $num_of_products){
				$this->db->order_by('id', 'RANDOM');
				$this->db->limit($num_of_products-$total_products);
				$this->db->where("company_id",$company_id);
				$this->db->where_not_in("id",$products_ids);
				$products[] = $this->db->get('products')->result();
			}
		}
	
		if( !empty($products) )
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $products ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No related products found !'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	function get_company_delivery_settings_post(){
		$where_arr = $this->input->post();
		
		if(!empty($where_arr))
			foreach($where_arr as $col=>$val)
			$this->db->where( $col, $val );
		
		$delivery_settings = $this->db->get('company_delivery_settings')->result();
		
		if( !empty($delivery_settings))
		{
			$this->response( array('error' => 0, 'message'=> '', 'data' => $delivery_settings), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No settings found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
	
	/**
	 * This function is used to check whether category image saved in database is actually exists in desired location
	 * @param array $checking_array An input array of products
	 * @return array Finalized array.
	 */
	private function check_category_image_exist($checking_array){
	
		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->image){
					$path = dirname(__FILE__)."/../../../";
					if(!file_exists($path.$items->image))
						$checking_array[$key]->image = '';
				}
	
			}
		}
		return $checking_array;
	}
	
	
	/**
	 * This function is used to check whether sub-category image saved in database is actually exists in desired location
	 * @param array $checking_array An input array of products
	 * @return array Finalized array.
	 */
	private function check_subcategory_image_exist($checking_array){
	
		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->subimage){
					$path = dirname(__FILE__)."/../../../";
					if(!file_exists($path.$items->subimage))
						$checking_array[$key]->subimage = '';
				}
	
			}
		}
		return $checking_array;
	}
	
	
	/**
	 * This function is used to check whether product image saved in database is actually exists in desired location
	 * @param array $checking_array An input array of products
	 * @return array Finalized array.
	 */
	private function check_products_image_exist($checking_array){
		
		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->image){
					$path = dirname(__FILE__)."/../../../assets/cp/images/product/";
					if(!file_exists($path.$items->image))
						$checking_array[$key]->image = '';
				}
				
			}
		}
		return $checking_array;
	}
	
	/**
	 * This function is used to check whether product image saved in database is actually exists in desired location
	 * @param array $checking_array An input array of products
	 * @return array Finalized array.
	 */
	private function check_product_image_exist($checking_array){
	
		if(!empty($checking_array)){
			
			if($checking_array->image){
				$path = dirname(__FILE__)."/../../../assets/cp/images/product/";
				if(!file_exists($path.$checking_array->image))
					$checking_array->image = '';
			}
	
		}
		return $checking_array;
	}
	
	
	function get_post_code_id_post(){
		$postcode = $this->input->post("postCode");
		$company_id = $this->input->post("company_id");
		
		$postcodeIds = $this->db->get_where("postcodes" , array("post_code" => $postcode))->result_array();
		
		if( !empty($postcodeIds) )
		{
			foreach($postcodeIds as $postcodeId){
				$is_selected = $this->db->get_where("company_delivery_areas" , array("company_id" => $company_id,"postcode_id" =>$postcodeId['id']))->result_array();
				if(!empty($is_selected)){
					$postcode_info = $is_selected['0'];
					$this->response( array('error' => 0, 'message'=> '', 'data' => $postcode_info), 200 );
					break;
				}
			}
			$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No cities found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
	
}
?>