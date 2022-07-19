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
			$this->response( array('error' => 1, 'message'=>_('Error - Did\'t get the Company ID.'), 'data' => '' ), 404 );
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
			$this->response( array('error' => 0, 'message'=> '', 'data' => $categories ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('No categories found !'), 'data' => '' ), 404 );
		}
		
		exit;
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
		
		if( !empty($category_products) )
		{
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
		
		if( !empty($subcategory_products) )
		{
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
			$this->response( array('error' => 1, 'message'=>_('Error - Did\'t get the Company ID.'), 'data' => '' ), 404 );
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
			$this->response( array('error' => 1, 'message'=>_('Error - Did\'t get the Company ID.'), 'data' => '' ), 404 );
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
		
		$this->db->where( 'company_id', $company_id);	
		$delivery_areas = $this->db->get('delivery_areas')->result();
		
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
		
		$this->db->where( 'delivery_areas_id', $delivery_areas_id);	
		$delivery_settings = $this->db->get('delivery_settings')->result();
		
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
		
		$this->db->where( 'id', $id);	
		$city_details = $this->db->get('delivery_settings')->row();
		
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
				$this->response( array('error' => 0, 'message'=>'', 'data' => $time_to_restrict ), 200 );
			}else{
				$this->response( array('error' => 0, 'message'=>_('No time found !'), 'data' => '' ), 200 );
			}		
		}else{
			$this->response( array('error' => 1, 'message'=>_('No settings found !'), 'data' => '' ), 404 );
		}
		
		exit;
	}
}
?>