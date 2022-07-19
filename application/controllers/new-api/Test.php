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
class Test extends REST_Controller
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
			$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $this->company_id ), 200 );
			
		}
		
		exit;
	}
	
	function company_settings_post()
	{
		$company_id = $this->post( 'company_id' );
		
		if($company_id)
		{
			$this->db->select('
					language_id,
					shop_offline,
					shop_offline_message,
					message_front,
					show_message_front,
					shop_visible,
					shop_visible_message,
					disable_price,
					hide_price_login,
					activated_addons,
					frontend_languages,
					company.parent_id,
					company.role,
					company.company_footer_text,
					company.company_footer_link,
					company.text_bg_color,
					company.text_color,
					text_color,
					company.phone,
					company.approved,
					company.status,
					company_css.use_own_css,
					company.footer_text,
					themes.theme_css,
					company_css.theme_custom_css,
					general_settings.theme_id,
					general_settings.category_feature,
					general_settings.delivery_service,
					general_settings.pickup_service,
					general_settings.hide_register,
					general_settings.faq_showhide,
					general_settings.faq_txt
					');
			
			$this->db->where( 'general_settings.company_id', $company_id);
			$this->db->where_in( 'company.ac_type_id', array(3,5,6));//3-Pro,5-FDD Pro,6-FDD Premium
			$this->db->where('company.ingredient_system','0');
			$this->db->join('company', 'company.id = general_settings.company_id');
			$this->db->join('themes', 'themes.id = general_settings.theme_id');
			//$this->db->join('themes', 'themes.id = 4');
			$this->db->join('language', 'language.id = general_settings.language_id');			
			$this->db->join('company_css', 'company_css.theme_id = general_settings.theme_id AND company_css.company_id = general_settings.company_id AND company_css.use_own_css = 1','left');
			//$this->db->join('company_css', 'company_css.theme_id = 4 AND company_css.company_id = general_settings.company_id AND company_css.use_own_css = 1','left');
			
			$general_settings = $this->db->get('general_settings')->row_array();
			
			if( !empty($general_settings) )
			{
				$this->response( array('error' => 0, 'message'=> '' , 'data' => $general_settings ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Error - No company settings found !'), 'data' => $this->db->last_query() ), 404 );
			}
		}
		else
		{		
			$this->response( array('error' => 1, 'message'=>_('Error - Did\\\'t get the Company ID.'), 'data' => '1' ), 404 );
		}	
		
		exit;
	}
	
	function get_section_design_settings_post()
	{
		$where_arr = $this->post();
		  		
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
	
	/**
	 * This function is used to check for the restricted timings.
	 * 		It is only for pickup services. It respond with array of Hours:Minute that are restricted.
	 * 		Restricted timing means the time at which any user has already ordered.
	 */
	function chk_restricted_timings_post(){
		$date = $this->post('date');
		$company_id = $this->post('company_id');
		
		$this->db->select('time_restriction_p');
		$order_setting = $this->db->get_where('order_settings',array('company_id'=>$company_id))->result_array();
		if(!empty($order_setting)){
			$time_to_restrict = array();
			if($order_setting['0']['time_restriction_p'] != 0){
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
				}
			}
			$this->response( array('error' => 0, 'message'=>'', 'data' => $time_to_restrict ), 200 );
		}else{
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
	 * This function is used to fetch the details of logged in user
	 * @access public
	 */
	public function get_user_post()
	{
	    $user_id = $this->post('user_id');
	   
	    $this->db->where( 'clients.id', $user_id);
	    $user = $this->db->get('clients')->row();
			
		if( !empty($user) )
		{
			$this->response( array('error' => 0, 'message'=> '' , 'data' => $user ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Error - No user details found !'), 'data' => '' ), 404 );
		}
	}
	
	function get_categories_json($company_id, $service_type)
	{
	
		$query = '';
	
		$query = " Select * FROM `categories` WHERE ( `company_id` = ".$company_id." AND `status` = 1 ) ";
	
		if( !$service_type || $service_type == 'both' )
		{
		}
		elseif( $service_type == 'pickup' )
		{
			$query .= " AND ( `service_type` = '1' OR `service_type` = '0' ) ";
		}
		elseif( $service_type == 'delivery' )
		{
			$query .= " AND ( `service_type` = '2' OR `service_type` = '0' ) ";
		}
	
		$query .= " ORDER BY `order_display` ASC ";
	
		$categories = $this->db->query( $query )->result();
	
		if( !empty($categories) )
		{
			$categories = $this->check_category_image_exist($categories);
			foreach($categories as $category)
			{
				$new_categories[$category->id] = $category;
			}
			return $new_categories;
		}
		else
		{
			return null;
		}
	
		exit;
	}
	
	function get_subcategories_json($categories_id)
	{
		$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'status', 1 );
	
		$this->db->order_by( 'suborder_display', 'ASC' );
	
		$subcategories = $this->db->get('subcategories')->result();
	
		if( !empty($subcategories) )
		{
			$subcategories = $this->check_subcategory_image_exist($subcategories);
			foreach($subcategories as $subcategory)
			{
				$new_subcategories[$subcategory->id] = $subcategory;
			}
			return $new_subcategories;
				
		}
		else
		{
			return null;
		}
	}
	
	function get_category_products_json($company_id, $categories_id, $subcategories_id)
	{
		$this->db->where( 'company_id', $company_id );
		$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'subcategories_id', $subcategories_id );
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where);
		$this->db->where( 'status', 1 );
		$this->db->order_by('pro_display','asc');
		$this->db->order_by('id','asc');
	
		/*
	
		$this->db->select('products.*,categories.service_type as category_service_type');
			
		$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
		$this->db->where($where);
			
		$this->db->order_by('pro_display','asc');
		$this->db->order_by('id','asc');
			
		$this->db->join('categories','categories.id = products.categories_id','left');
			
		$products = $this->db->get_where('products', array('products.company_id' => $company_id, 'products.status' => 1, 'products.sell_product_option !=' => '' ))->result();	
		*/
	
		$products = $this->db->get_where('products', array('sell_product_option !=' => '' ))->result();
		
		$is_k_assoc = true;
		if(!empty($products)){
			foreach ($products as $key => $product){
						
					/**
					 * Checking if product's image exist
					 */
					if($product->image != '' && $product->image != null){
						$products[$key]->image = $this->existing_product_image($product->image);
					}else{
						$products[$key]->image = '';
					}
						
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
										}*/
			
						$complete = 1;
						if($product->direct_kcp == 1){
							$this->db->where(array('obs_pro_id'=>$product->id,'is_obs_product'=>0));
							$result = $this->db->get('fdd_pro_quantity')->result_array();
							if(empty($result)){
								$complete = 0;
							}
						}
						else{
							$this->db->where(array('obs_pro_id'=>$product->id));
							$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
							if(!empty($result_custom)){
								foreach ($result_custom as $val){
									if($val['is_obs_product'] == 1){
										$complete = 0;
										break;
									}
								}
							}
							else{
								$complete = 0;
							}
						}
			
						if($complete){
							$k_allergence = $this->get_k_allergence($product->id);
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
									}
								}
			
								//if($product->allergence && trim($product->allergence) != '')//product 5340 in cedcoss shop was prefixing 0
								//	$product->allergence = $product->allergence.$allrg_str;
								//else
								$product->allergence = substr($allrg_str,2);
			
							}
						}
					}
				}
			}
		
		$category_products = $products;
		
		$sorted_p = array();
		$unsorted_p = array();
		if(isset($category_products))
		{
			foreach($category_products as $prod) {
				if($prod->pro_display != 0){
					$sorted_p[] = $prod;
				}else{
					$unsorted_p[] = $prod;
				}
	
			}
			$category_products = array_merge($sorted_p,$unsorted_p);
		}
		if( !empty($category_products) )
		{
			$category_products = $this->check_products_image_exist($category_products);
			foreach($category_products as $category_product)
			{
				$category_product->grp_arr = $this->get_product_groups($category_product->id);
				$new_category_products[$category_product->id] = $category_product;
	
			}
			return $new_category_products;
				
		}
		else
		{
			return null;
		}
	}
	
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
	
	function get_shop_content_json_new_post(){
	
		$company_id = $this->post('company_id');
		$service_type = $this->post('service_type');
	
		$categories = $this->get_categories_json($company_id, $service_type);
		$categories = (array)$categories;
		foreach($categories as $k=>$category)
		{
			$category = (array)$category;
			$categories_id = $category['id'];
			$subcategories_id = -1;
			$subcats = $this->get_subcategories_json($categories_id);
			$categories[$k]->sub_category = $subcats;
			$categories[$k]->product = $this->get_category_products_json($company_id, $categories_id, $subcategories_id);
	
			foreach($subcats as $key=>$subcat)
			{
				$subcategories_id = $subcat->id;
				$subcats[$key]->product = $this->get_category_products_json($company_id, $categories_id, $subcategories_id);
			}
				
			$categories[$k]->sub_category = $subcats;
				
		}
		$result['category'] = $categories;
		$this->response( array('error' => 0, 'message'=> '', 'data' => $result ), 200 );
	}
	
	/**
	 * This function is used to get shop's all contents
	 * @access public
	 */
	function get_shop_content_all_new_post(){
	
		$company_id = $this->post('company_id');
		$year = $this->post('year');
		$month = $this->post('month');
		$day = $this->post('day');
	
	
		if($this->post('field') == 'cats'){
			$categories = $this->get_categories($company_id);
			$subcategories = $this->get_subcategories($company_id);
			$result = array('categories' => $categories, 'subcategories' => $subcategories);
			$this->response( array('error' => 0, 'message'=> '' , 'data' => $result ), 200 );
		}
	
		if($this->post('field') == 'products'){
			// Fetching company account type. This is for checking whether company is attached with FoodDESK
			$this->db->select("ac_type_id");
			$company_info = $this->db->get_where('company',array("id" => $company_id))->result();
			$is_fdd_associated = 0;
			if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
				$is_fdd_associated = 1;
				
			// Fetching Products
			$products = $this->get_products($company_id, $is_fdd_associated);
			$this->response( array('error' => 0, 'message'=> '' , 'data' => $products ), 200 );
		}
	
		// Fetching rest useful data
		if($this->post('field') == 'rest'){
			$general_settings = $this->get_general_settings($company_id);
				
			// Fetching Delivery settings
			$delivery_settings = $this->get_delivery_settings($company_id);
				
			// Fetching Delivery areas
			$delivery_areas = $this->get_delivery_areas($company_id);
				
			// Fetching International Delivery settings
			$international = array();
			$company_countries = array();
			$company_countries_int = array();
			$all_countries = array();
			if(!empty($delivery_settings)){
				if($delivery_settings[0]->type == 'international'){
					$international = $this->get_international_settings($company_id);
						
					//Fetching international countries delivery settings
					$company_countries = $this->get_company_countries($company_id);
						
					//Fetching international countries delivery costs
					$company_countries_int = $this->get_company_countries_int($company_id);
						
					//all countries list for register page dropdown
					$all_countries = $this->get_all_countries();
				}
			}
				
			// Fetching Opening hours
			$opening_hours = $this->get_opening_hours($company_id);
				
			// Fetching Opening hours
			$sub_admins = array();
			$this->db->select('role');
			$is_super = $this->db->get_where('company', array('id' => $company_id))->result();
			if(!empty($is_super) && $is_super['0']->role == 'super')
				$sub_admins = $this->get_sub_admins($company_id);
				
			// Fetching Opening hours
			$order_settings = $this->get_order_settings($company_id,$general_settings[0]->calendar_country);
				
			// Fetching Opening hours
			//$pre_assigned_holidays = $this->get_pre_assigned_holidays($company_id,$year,$month);
				
			// Fetching Opening hours
			$days = $this->get_days();
				
			// Fetching Countries
			$countries = $this->get_countries();
				
			// Fetching Available Payment Options for Cardgate
			$cardgate = $this->cardgate_payment_option($company_id);
				
			// Fetching all allergence words
			$allergence_words = $this->get_admin_defined_allergence();
				
			$response_arr = array(
					'delivery_areas'		=> $delivery_areas,
					'delivery_settings'		=> $delivery_settings,
					'opening_hours'			=> $opening_hours,
					'countries'				=> $countries,
					'sub_admins'			=> $sub_admins,
					'general_settings'		=> $general_settings,
					'order_settings'		=> $order_settings,
					'international'			=> $international,
					'company_countries'		=> $company_countries,
					'company_countries_int'	=> $company_countries_int,
					'all_countries'			=> $all_countries,
					//'pre_assigned_holidays' => $pre_assigned_holidays,
					'days' => $days,
					'cardgate'=>$cardgate,
					'allergence_words' => $allergence_words
			);
				
			$this->response( array('error' => 0, 'message'=> '' , 'data' => $response_arr ), 200 );
		}
	}
	
	/**
	 * This function is used to get shop's all contents
	 * @access public
	 */
	function get_shop_content_all_post(){
		
		$company_id = $this->post('company_id');

		/*if($company_id == 3988)
			$company_id = 87;*/
		
		// Fetching company account type. This is for checking whether company is attached with FoodDESK
		//$this->db->select("ac_type_id");
		//$company_info = $this->db->get_where('company',array("id" => $company_id))->result();
		//$is_fdd_associated = 0;
		//if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
			$is_fdd_associated = 1;
		
		// Fetching Opening hours
		/*$general_settings = $this->get_general_settings($company_id);
		
		// Fetching Categories
		$categories = $this->get_categories($company_id);
		
		// Fetching Subcategories
		$subcategories = $this->get_subcategories($company_id);*/
		
		// Fetching Products
		$products = $this->get_products($company_id, $is_fdd_associated);
		
		$this->response($products,200);
		
		// Fetching Delivery settings
		/*$delivery_settings = $this->get_delivery_settings($company_id);

		// Fetching Delivery areas
		$delivery_areas = $this->get_delivery_areas($company_id);
		
		// Fetching International Delivery settings
		$international = array();
		$company_countries = array();
		$company_countries_int = array();
		$all_countries = array();
		if(!empty($delivery_settings)){
			if($delivery_settings[0]->type == 'international'){
				$international = $this->get_international_settings($company_id);
				
				//Fetching international countries delivery settings
				$company_countries = $this->get_company_countries($company_id);
				
				//Fetching international countries delivery costs
				$company_countries_int = $this->get_company_countries_int($company_id);
				
				//all countries list for register page dropdown
				$all_countries = $this->get_all_countries();
			}
		}
		
		// Fetching Opening hours
		$opening_hours = $this->get_opening_hours($company_id);
		
		// Fetching Opening hours
		$sub_admins = array();
		$this->db->select('role');
		$is_super = $this->db->get_where('company', array('id' => $company_id))->result();
		if(!empty($is_super) && $is_super['0']->role == 'super')
			$sub_admins = $this->get_sub_admins($company_id);
		
		// Fetching Opening hours
		$order_settings = $this->get_order_settings($company_id,$general_settings[0]->calendar_country);
		
		// Fetching Opening hours
		//$pre_assigned_holidays = $this->get_pre_assigned_holidays($company_id,$year,$month);
		
		// Fetching Opening hours
		$days = $this->get_days();
		
		// Fetching Countries
		$countries = $this->get_countries();
		
		// Fetching Available Payment Options for Cardgate
		$cardgate = $this->cardgate_payment_option($company_id);
		
		// Fetching all allergence words
		$allergence_words = $this->get_admin_defined_allergence();
		
		$response_arr = array(
				'products'				=> $products,
				'categories'			=> $categories,
				'subcategroies'			=> $subcategories,
				'delivery_areas'		=> $delivery_areas,
				'delivery_settings'		=> $delivery_settings,
				'opening_hours'			=> $opening_hours,
				'countries'				=> $countries,
				'sub_admins'			=> $sub_admins,
				'general_settings'		=> $general_settings,
				'order_settings'		=> $order_settings,
				'international'			=> $international,
				'company_countries'		=> $company_countries,
				'company_countries_int'	=> $company_countries_int,
				'all_countries'			=> $all_countries,
				//'pre_assigned_holidays' => $pre_assigned_holidays,
				'days' => $days,
				'cardgate'=>$cardgate,
				'allergence_words' => $allergence_words
		);
		*/
		//$this->response( array('error' => 0, 'message'=> '' , 'data' => $response_arr ), 200 );
	}
	
	private function get_company_countries($company_id){
	
		$companies_country_int = array();
		$this->db->select('company_countries.company_id,company_countries.country_id,country.country_name');
		$this->db->where('company_id',$company_id);
		$this->db->join('country','country.id =  company_countries.country_id','left');
		$companies_country_int = $this->db->get(' company_countries')->result();
	
		return $companies_country_int;
	}
	
	private function get_company_countries_int($company_id){
		
		$companies_country_int = array();
		$this->db->order_by('company_countries_int.lower_range','asc');
		$this->db->order_by('company_countries_int.upper_range','asc');
		$this->db->where('company_id',$company_id);
		$this->db->join('country','country.id =  company_countries_int.country_id','left');
		$companies_country_int = $this->db->get(' company_countries_int')->result();
		
		return $companies_country_int;
	}
	
	private function get_all_countries(){
		$this->db->select('id as country_id,country_name');
		$all_countries = $this->db->get('country')->result();
		return $all_countries;
	} 
	
	private function cardgate_payment_option($company_id){
	
		$this->load->model('Mpayment');
		$this->load->helper('curo');
		$data = array();
		
		// Checking for cardgate enable or not
		$merchant_info = $this->db->get_where('cp_merchant_info', array('company_id' => $company_id))->result_array();
		$cardgate_setting = $this->Mpayment->get_cardgate_setting(array('company_id' => $company_id));
	
		if(!empty($merchant_info) && !empty($cardgate_setting) && $cardgate_setting[0]->cardgate_payment == 1){
			//$merchant_status = get_status($merchant_info[0]['curo_id']);//echo $merchant_status;die;
			//if($merchant_status == 'Approved' || $merchant_status == 'approved')
				$data['enabled'] = 1;
			//else
				//$data['enabled'] = 0;
		}else{
			$data['enabled'] = 0;
		}
		
		// Temporary code for Delrey
		/*if($company_id == 26)
			$data['enabled'] = 1;*/
	
		if($data['enabled']){
			
			$data['minimum_amount_cardgate'] = $cardgate_setting[0]->minimum_amount_cardgate;
			$data['c_apply_tax'] = $cardgate_setting[0]->c_apply_tax;
			$data['c_tax_percentage'] = $cardgate_setting[0]->c_tax_percentage;
			$data['c_tax_amount'] = $cardgate_setting[0]->c_tax_amount;
			
			$get_banks = 0;
			$merchant_info = $this->Mpayment->get_merchant_info($company_id);
			
			$payment_methods = $this->Mpayment->get_selected_payment_methods($company_id);
			$i=0;
			$payment_gateway = array();
			foreach($payment_methods as $method){
				$get_info = $this->Mpayment->get_payment_method_info($method);
				if(!empty($get_info)){
					$payment_gateway[$i] = $get_info[0];
					if($get_info[0]['value']=='ideal'){
						$get_banks = 1;
					}
					$i++;
				}
			}
			
			$data['available_options'] = $payment_gateway;
			if($get_banks==1){
				$issuer = cargate_curl('https://api.cardgate.com/rest/v1/ideal/issuers/');
				$data['issuer'] = $issuer->issuers;
			}
			
			$data['merchant_curo_id'] = $merchant_info[0]['site_id'];
			
			// $data['merchant_name'] = 'sitematic';
			// $data['merchant_hash'] = 'Y8f0cnqB0WKAKZ1WwHTFNL2jge2pzOzXuEnBNHfWAuLNroo3Fjbm4iTJzcWqrR7l';
		}
		
		return $data;
	}
	
	/**
	 * This private function is used to get Categories of given company
	 * @access private
	 */
	private function get_categories($company_id = null){
		
		$categories = array();
		
		$this->db->select('delivery_service,pickup_service');
		$this->db->where('company_id', $company_id);
		$service_types = $this->db->get('general_settings')->result();
		
		$service_type = 'both';
		if(!empty($service_types)){
			$service_types = $service_types[0];
			if( $service_types->delivery_service == 1 && $service_types->pickup_service == 1 )
				$service_type = 'both';
			else
			if( $service_types->pickup_service == 1 )
				$service_type = 'pickup';
			else
			if( $service_types->delivery_service == 1 )
				$service_type = 'delivery';
		}
		
		$query = '';
		
		$query = " Select * FROM `categories` WHERE ( `company_id` = ".$company_id." AND `status` = 1 ) ";
		
		if( !$service_type || $service_type == 'both' )
		{
			$query .= " AND ( `service_type` = '1' OR `service_type` = '2' OR `service_type` = '0' ) ";
		}
		elseif( $service_type == 'pickup' )
		{
			$query .= " AND ( `service_type` = '1' OR `service_type` = '0' ) ";
				
		}
		elseif( $service_type == 'delivery' )
		{
			$query .= " AND ( `service_type` = '2' OR `service_type` = '0' ) ";
				
		}
		
		$query .= " ORDER BY `order_display` ASC ";
		
		$categories = $this->db->query( $query )->result();
		
		if( !empty($categories) )
		{
			$categories = $this->check_category_image_exist($categories);
		}
		
		return $categories;
	}
	
	/**
	 * This private function is used to get Subcategories of given company
	 * @access private
	 */
	private function get_subcategories($company_id = null){
	
		$subcategories = array();
		
		$this->db->where( '`categories_id` IN (SELECT `categories`.`id` FROM `categories` WHERE `company_id` = '.$company_id.')' );
		$this->db->where( 'status', 1 );
		
		$this->db->order_by( 'suborder_display', 'ASC' );
		
		$subcategories = $this->db->get('subcategories')->result();
		
		if( !empty($subcategories) )
		{
			$subcategories = $this->check_subcategory_image_exist($subcategories);
		
		}
		
		return $subcategories;
	}
	
	/**
	 * This private function is used to fetch all products of given company
	 * @access private
	 */
	public function get_products_get($company_id = null, $is_k_assoc = 0){
		$products = array();
		// $this->response($company_id,200);
		if($company_id && is_numeric($company_id)){
			
			$this->db->select('display_fixed');
			$display_fixed_result = $this->db->get_where('general_settings', array('company_id' => $company_id))->result();
			
			$this->db->select('products.*,categories.service_type as category_service_type');
			
			$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
			$this->db->where($where);
			
			$this->db->order_by('pro_display','asc');
			$this->db->order_by('id','asc');
			
			$this->db->join('categories','categories.id = products.categories_id','left');
			
			//$this->db->order_by('products.id','ASC');
			// $this->db->limit(500);
			$products = $this->db->get_where('products', array('products.company_id' => $company_id, 'products.status' => 1, 'products.sell_product_option !=' => '' ))->result();
			
			$complete = 0;
			if(!empty($products)){
				
				foreach ($products as $key => $product){
					
					if($is_k_assoc){
						$complete = $this->get_fixed_status($product->id,$product->direct_kcp);
						
						if($display_fixed_result[0]->display_fixed && !$complete){					
							$fp = fopen(APPPATH . "/controllers/new-api/logs-products.txt","a");
							fwrite($fp,$key.json_encode($product)."\n");
							fclose($fp);
							unset($products[$key]);
							continue;
						}
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
						$k_allergence = $this->get_k_allergence($product->id);					
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
								}
							}
							
							$product->allergence = substr($allrg_str,2);
								
							$fp = fopen(APPPATH . "/controllers/new-api/logs-productss.txt","a");
							fwrite($fp,json_encode($product)."\n");
							fclose($fp);
							
						}
					}
				}
			}
		}
		$this->response(array_values($products),200);
		// return array_values($products);
	}
	
	private function get_products($company_id = null, $is_k_assoc = 0){
		$products = array();
		// $this->response($company_id,200);
		if($company_id && is_numeric($company_id)){
				
			$this->db->select('display_fixed');
			$display_fixed_result = $this->db->get_where('general_settings', array('company_id' => $company_id))->result();
				
			$this->db->select('products.*,categories.service_type as category_service_type');
				
			$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
			$this->db->where($where);
				
			$this->db->order_by('pro_display','asc');
			$this->db->order_by('id','asc');
				
			$this->db->join('categories','categories.id = products.categories_id','left');
				
			//$this->db->order_by('products.id','ASC');
			// $this->db->limit(500);
			$products = $this->db->get_where('products', array('products.company_id' => $company_id, 'products.status' => 1, 'products.sell_product_option !=' => '' ))->result();
				
			$complete = 0;
			if(!empty($products)){
	
				foreach ($products as $key => $product){
						
					if($is_k_assoc){
						$complete = $this->get_fixed_status($product->id,$product->direct_kcp);
	
						if($display_fixed_result[0]->display_fixed && !$complete){
							$fp = fopen(APPPATH . "/controllers/new-api/logs-products.txt","a");
							fwrite($fp,$key.json_encode($product)."\n");
							fclose($fp);
							unset($products[$key]);
							continue;
						}
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
						$k_allergence = $this->get_k_allergence($product->id);
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
								}
							}
								
							$product->allergence = substr($allrg_str,2);
	
							$fp = fopen(APPPATH . "/controllers/new-api/logs-productss.txt","a");
							fwrite($fp,json_encode($product)."\n");
							fclose($fp);
								
						}
					}
				}
			}
		}
		
		return array_values($products);
	}
	
	private function get_fixed_status($id = 0, $direct_kcp = 0){
		$complete = 1;
		if($id){
			if($direct_kcp == 1){
				$this->db->where(array('obs_pro_id'=>$id,'is_obs_product'=>0));
				$result = $this->db->get('fdd_pro_quantity')->result_array();
				if(empty($result)){
					$complete = 0;
				}
			}
			else{
				$this->db->where(array('obs_pro_id'=>$id));
				$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
				if(!empty($result_custom)){
					foreach ($result_custom as $val){
						if($val['is_obs_product'] == 1){
							$complete = 0;
							break;
						}
					}
				}
				else{
					$complete = 0;
				}
			}
		}
		return $complete;
	}
	
	/**
	 * This function is used to check whether product image saved in database is actually exists in desired location
	 * @param string $image image name
	 * @return string $final_image Final image name if exist otherwise null.
	 */
	private function existing_product_image($image = ''){
	
		$final_image = '';
		if($image != ''){
			$path = dirname(__FILE__)."/../../../assets/cp/images/product/";
			if(@file_exists($path.$image))
				$final_image = $image;
		}
		return $final_image;
	}
	
	/**
	 * This private function is used to fetch extras of given product
	 * @access private
	 * @param integer $product_id Product ID
	 * @return array $grps_arr Array of extras
	 */
	private function get_product_groups( $product_id = null)
	{
		$grps_arr = array();
		if($product_id && is_numeric($product_id)){
			$this->db->where( 'groups_products.products_id', $product_id );
			$this->db->join('groups', 'groups.id = groups_products.groups_id');
			$this->db->order_by( 'groups.display_order', 'ASC' );
			$this->db->order_by( 'groups_products.display_order', 'ASC' );
			$this->db->order_by( 'groups_products.type', 'ASC' );
				
			$product_grps = $this->db->get('groups_products')->result();
			//$this->response($product_grps);
			if( !empty($product_grps) )
			{
				$hold_grp_id = array();
				$index = -1;
					
				foreach( $product_grps as $grp )
				{
					//if( $hold_grp_id != $grp->groups_id )
					if(!in_array($grp->groups_id,$hold_grp_id))
					{
						$hold_grp_id[] = $grp->groups_id;
						//$index = $index+1;
							
						//$grps_arr[$index] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'required' => $grp->required, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );
						$grps_arr[] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'required' => $grp->required, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );
							
					}
					elseif( in_array($grp->groups_id, $hold_grp_id) )
					{
						$key = array_search($grp->groups_id,$hold_grp_id);
						$grps_arr[$key]['attributes_arr'][] = array( $grp->attribute_name, $grp->attribute_value );
					}
				}
			}
		}
		
		return $grps_arr;
	}
	
	/**
	 * This private function is used to get multidiscount
	 * @access private
	 * @param integer $product_id Product ID
	 * @param integer $discount_type It is the type of discount to be fetched (0 => Unit Wise, 1 => Weight wise, 2 => Per Person wise)
	 * @return array $products_discount Array of multi discounts
	 */
	private function get_product_multi_discounts($product_id = null, $discount_type = null)
	{
		$products_discount = array();
		
		if($product_id && is_numeric($product_id)){
			
			if( ($discount_type == 0 || $discount_type == 1 || $discount_type == 2) && $discount_type != null )
			{
				$this->db->where( 'type', $discount_type );
			}
			
			$this->db->order_by('quantity','ASC');
			$this->db->where( 'products_id', $product_id );
			$products_discount = $this->db->get('products_discount')->result();
		}
		
		return $products_discount;
	}
	
	/**
	 * This private function is used to fetch Keurslager Ingredients related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which ingredients have to be fetch
	 * @return array $ingredients It is the array if ingredients associated with the given product
	 */
	private function get_k_ingredients($product_id = 0){
		$ingredients = array();
		if($product_id){
			/*$this->db->select('prefix,ki_name');
			$this->db->order_by('kp_display_order', 'ASC');
			$this->db->order_by('display_order', 'ASC');
			$ingredients = $this->db->get_where('products_ingredients', array('product_id' => $product_id))->result();*/
			$ingredients = $this->db->query(
				"SELECT *
				FROM products_ingredients
				WHERE product_id = ".$product_id."
				AND ki_name!= '('
				AND ki_name!= ')'
				AND ((display_order=1 AND is_obs_ing=1) OR (display_order !=1 AND is_obs_ing=0))
				GROUP BY ki_name
				ORDER BY kp_display_order DESC, display_order ASC")->result();
		}
		return $ingredients;
	}
	
	/**
	 * This private function is used to fetch Keurslager Traces related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which traces have to be fetch
	 * @return array $traces It is the array if traces associated with the given product
	 */
	private function get_k_traces($product_id = 0){
		$traces = array();
		if($product_id){
			$this->db->select('prefix,kt_name');
			$this->db->order_by('display_order', 'ASC');
			$this->db->group_by('kt_id');
			$traces = $this->db->get_where('products_traces', array('product_id' => $product_id))->result();
		}
		return $traces;
	}
	
	/**
	 * This private function is used to fetch Keurslager Allergence related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which allergence have to be fetch
	 * @return array $traces It is the array if allergence associated with the given product
	 */
	private function get_k_allergence($product_id = 0){
		$allergence = array();
		if($product_id){
			$this->db->select('prefix,ka_name');
			$this->db->order_by('display_order', 'ASC');
			$this->db->group_by('ka_id');
			$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}
	
	/**
	 * This private function is used to fetch delivery areas including provinces and cities with zipcode
	 * @access private
	 * @param integer $company_id Company ID
	 * @return array $delivery_areas Array of delivery areas
	 */
	private function get_delivery_areas($company_id = null){
		
		$delivery_areas = array();
		
		if($company_id && is_numeric($company_id)){
			
			// Fetching countries names
			$countries = array();
			$this->db->select('country.id, country.country_name');
			$this->db->join('country', 'company_delivery_areas.country_id = country.id');
			$this->db->group_by('company_delivery_areas.country_id');
			$this->db->where('company_delivery_areas.company_id',$company_id);
			$countries = $this->db->get('company_delivery_areas')->result();
			
			// Fetching Provinces
			$provinces = array();
			$this->db->distinct();
			$this->db->select('`company_delivery_areas`.`state_id`, `company_delivery_areas`.`company_id`, `states`.`state_name`, `states`.`country_id`');
			$this->db->join('states','states.state_id = company_delivery_areas.state_id' );
			//$this->db->group_by('company_delivery_areas.state_id');
			$this->db->where('company_delivery_areas.company_id',$company_id);
			$provinces = $this->db->get('company_delivery_areas')->result();
			
			// Fetching Cities
			$cities = array();
			$this->db->select('postcodes.*');
			$this->db->join('postcodes','postcodes.id = company_delivery_areas.postcode_id' );
			$this->db->where('company_delivery_areas.company_id',$company_id);
			$cities = $this->db->get('company_delivery_areas')->result();
			
			$delivery_areas = array( 'countries' => $countries, 'provinces' => $provinces, 'cities' => $cities);
		}
		
		return $delivery_areas;
	}
	
	/**
	 * This private function is used to fetch delivery settings
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $delivery_settings Array of delivery areas
	 */
	private function get_delivery_settings($company_id = null){
		$delivery_settings = array();
		
		if($company_id && is_numeric($company_id)){
			
			$this->db->where('company_id',$company_id);
			$delivery_settings = $this->db->get('company_delivery_settings')->result();
		}
		
		return $delivery_settings;
	}
	
	/**
	 * This private function is used to fetch International Delivery areas with their corresponding delivery costs
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $international_areas Array of countries and their costs 
	 */
	private function get_international_settings($company_id = null){
		$international_areas = array();
		
		if($company_id){
// 			$this->db->select('country.country_name, company_countries.country_id, company_countries.country_cost');
// 			$this->db->join('country', 'country.id = company_countries.country_id');
// 			$this->db->where('company_id',$company_id);
// 			$international_areas = $this->db->get('company_countries')->result();
			$this->db->select('country.country_name, company_countries_int.country_id');
			$this->db->join('country', 'country.id = company_countries_int.country_id','left');
			$this->db->where('company_id',$company_id);
			$this->db->distinct('country_id');
			$international_areas = $this->db->get('company_countries_int')->result();
		}
		
		return $international_areas;
	}
	
	/**
	 * This private function is used to fetch Opening Hours
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $opening_hours Array of Opening hours  (pickup and delivery)
	 */
	private function get_opening_hours($company_id = null){
		$opening_hours = array();
		
		if($company_id && is_numeric($company_id)){
			
			$this->db->join('days','pickup_delivery_timings.day_id = days.id');
			$this->db->where('pickup_delivery_timings.company_id', $company_id);
			$this->db->order_by('pickup_delivery_timings.day_id','ASC');
			$opening_hours = $this->db->get('pickup_delivery_timings')->result();
		}
		
		return $opening_hours;
	}
	
	/**
	 * This private function is used to fetch Pre-assigned Holidays
	 * @access private
	 * @param Integer $company_id Company ID
	 * @param Integer $year Year
	 * @param Integer $month Month
	 * @return array $pre_assigned_holidays Array of Pre-assigned Holidays
	 */
	private function get_pre_assigned_holidays($company_id = null, $year = null, $month = null){
	
		$pre_assigned_holidays = array();
	
		if($company_id && $year && $month){
			$this->db->select('calendar_country');
			$calendar_of_country = $this->db->get_where('general_settings',array('company_id'=>$company_id))->result_array();
			$this->db->where('month >=',$month);
			$this->db->where('year >=',$year);
			$pre_assigned_holidays = $this->db->get($calendar_of_country['0']['calendar_country'])->result();
		}
	
		return $pre_assigned_holidays;
	}
	
	/**
	 * This private function is used to fetch Sub-Admins
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $sub_admins Array of Subadmins
	 */
	private function get_sub_admins($company_id = null){
	
		$sub_admins = array();
	
		if($company_id){
			$where_arr['parent_id'] = $company_id;
			$where_arr['role'] = 'sub';
			$where_arr['status'] = '1';
			$where_arr['approved'] = '1';
			
			$this->db->select('company.company_name,general_settings.delivery_service,general_settings.pickup_service,general_settings.company_id,general_settings.calendar_country,order_settings.*');
			$this->db->join('order_settings','order_settings.company_id = company.id');
			$this->db->join('general_settings','general_settings.company_id = company.id');
		    $this->db->where( $where_arr );
			$sub_admins = $this->db->get('company')->result();
			if(!empty($sub_admins)){
				foreach ($sub_admins as $key => $sub_admin){
					$sub_admins[$key]->holiday_dates = $this->get_company_holiday_dates($sub_admin->company_id, $sub_admin->calendar_country);
					$sub_admins[$key]->shop_close_dates = $this->get_company_close_dates($sub_admin->company_id);
				}
			}
		}
	
		return $sub_admins;
	}
	
	/**
	 * This private function is used to fetch General Settings
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $general_settings Array of General Settings
	 */
	private function get_general_settings($company_id = null){
	
		$general_settings = array();
	
		if($company_id){
			$this->db->select( '
								company.address, 
								company.zipcode, 
								company.city, 
								company.country_id,
								company.show_bo_link_in_shop,
								company.k_assoc,
								activate_discount_card, 
								discount_card_message, 
								pay_option, online_payment, 
								paypal_address, 
								apply_tax, 
								tax_percentage, 
								tax_amount, 
								minimum_amount_paypal, 
								disc_per_amount, 
								disc_after_amount, 
								disc_percent, 
								disc_price, 
								hide_availability, 
								activate_suggetions, 
								num_of_suggetions,
								calendar_country,
								extra_field_popup,
								extra_field_popup_name,
								tnc_txt,
								order_timing_info,
								shop_view,
								shop_view_default,
								amt_row_page
							' );
			
			$this->db->where( 'general_settings.company_id', $company_id);
			$this->db->where_in( 'company.ac_type_id', array(3,5,6));//3-Pro,5-FDD Pro,6-FDD Premium
			$this->db->where('company.ingredient_system','0');
			$this->db->join('company', 'company.id = general_settings.company_id');
			
			$general_settings = $this->db->get('general_settings')->result();
		}
	
		return $general_settings;
	}
	
	/**
	 * This private function is used to fetch Order Settings
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $order_settings Array of Order Settings
	 */
	private function get_order_settings($company_id = null, $calendar_country = 'calendar_belgium'){
	
		$order_settings = array();
	
		if($company_id){
			$this->db->where( 'company_id', $company_id);
			$order_settings = $this->db->get('order_settings')->result();
			if(!empty($order_settings)){
				$order_settings[0]->holiday_dates = $this->get_company_holiday_dates($company_id, $calendar_country);
				$order_settings[0]->shop_close_dates = $this->get_company_close_dates($company_id);
			}
		}
	
		return $order_settings;
	}
	
	/**
	 * This private function is used to fetch company holidays (including pre-assigned holidays by MCP)
	 * @access private
	 */
	function get_company_holiday_dates($company_id = 0, $calendar_country = 'calendar_belgium'){
		$holidays = array();
		if($company_id){
			$this->db->select('day,month,year');
			$this->db->where('company_id', $company_id);
			$this->db->where("( `calendar` = 'own' OR `calendar` = '".$calendar_country."' )");
			$holidays_dates = $this->db->get('company_holidays')->result();
			if(!empty($holidays_dates)){
				foreach($holidays_dates as $holiday_date){
					$holidays[] = ( (strlen($holiday_date->day) == 1)?'0'.$holiday_date->day:$holiday_date->day ).'/'.( (strlen($holiday_date->month) == 1)?'0'.$holiday_date->month:$holiday_date->month ).'/'.$holiday_date->year;
				}
			}
		}
		return implode(',',$holidays);
	}
	
	/**
	 * This private function is used to fetch company closing dates
	 * @access private
	 */
	function get_company_close_dates($company_id){
		$holidays = array();
		if($company_id){
			$this->db->select('day,month,year');
			$this->db->where('company_id', $company_id);
			$holidays_dates = $this->db->get('company_closedays')->result();
			if(!empty($holidays_dates)){
				foreach($holidays_dates as $holiday_date){
					$holidays[] = ( (strlen($holiday_date->day) == 1)?'0'.$holiday_date->day:$holiday_date->day ).'/'.( (strlen($holiday_date->month) == 1)?'0'.$holiday_date->month:$holiday_date->month ).'/'.$holiday_date->year;
				}
			}
		}
		return implode(',',$holidays);
	}
	
	/**
	 * This private function is used to fetch Countries
	 * @access private
	 * @return array $countries Array of Countries
	 */
	private function get_countries(){
		$this->db->where('id = 21 OR id = 150');
		$countries = $this->db->get('country')->result();
		return $countries;
	}
	
	/**
	 * This private function is used to fetch all expected allergence words defined in admin settings
	 * @access private
	 * @return array $allergence_words Array of allergence words
	 */
	private function get_admin_defined_allergence(){
		$results = array();
		$allergence_words = array();
		
		$this->db->select('allergens_word');
		$results = $this->db->get('allergens_words')->result();
		
		if(!empty($results))
			foreach($results as $row){
				$allergence_words[] = $row->allergens_word;
			}
		return $allergence_words;
	}

	/**
	 * This private function is used to fetch Days
	 * @access private
	 * @return array $days Array of Days
	 */
	private function get_days(){
		$days = $this->db->get('days')->result();
		return $days;
	}
	
	/**
	 * This public function is used to verify user
	 * @access public
	 */
	public function verify_user_post(){	
		   
	   	$this->db->where( 'clients.email_c', $this->post('obs_username') );
	   	$this->db->where( 'clients.password_c', $this->post('obs_password') );
	   	$client = $this->db->get('clients')->row();
	   	
	   	if( !empty($client) && isset($client->id) )
	   	{
	   		$this->db->where( 'client_numbers.company_id', $this->post('company_id') );
	   		$this->db->where( 'client_numbers.client_id', $client->id );
	   		$clientInfo = $this->db->get('client_numbers')->result_array();
	   		if(!empty($clientInfo)){
	   			$clientInfo = $clientInfo['0'];
	   			$client->dcn = $clientInfo['discount_card_number'];
	   			$client->newsletter = $clientInfo['newsletter'];
	   			$client->disc_per_client = $clientInfo['disc_per_client'];
	   			unset($client->password_c);
	   		}
		  	$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $client ), 200 );
	   	}
	   	else
	   	{
	      	$this->response( array('error' => 1, 'message'=>_('Invalid username or password !'), 'data' => '' ), 404 );
	   	}
	   
	   	exit;
	}
	
	private function verify_api_request_return_get(){
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
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * This public function is used to submit order data into db
	 */
	function submit_order_post()
	{
		//$api_verified = $this->verify_api_request_get();
	
		$api_verified = $this->verify_api_request_return_get();
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>'Invalid Credentials', 'data' => '' ), 404 );
		}
		else
		{
			// -- >> Submit Order
				
			$client_id = $this->post( 'client_id' );
			$email_c = $this->post( 'email_c' );
			$service_select = $this->post( 'service_select' );
			$total_cart_amount = $this->post( 'order_total' );
			$shop_url = $this->post( 'shop_url' );
				
			if( $client_id )
			{
				$post = $this->post();
				$service_select = $this->post( 'service_select' );
	
				if( $service_select == 'pickup' )
				{
					$order_date = explode('/',$post['pickup_date']);
					$order_date_db = date('Y-m-d',strtotime( $order_date[2].'-'.$order_date[1].'-'.$order_date[0].' 00:00:00' ));
					$pickup_minute = $post['pickup_minute'];
					//echo $pickup_minute;
					//die();
					if(strlen($pickup_minute) == 1){
						$pickup_minute = '0'.$pickup_minute;
					}
					$insert_arr = array(    
							'clients_id' => $client_id,
							'company_id' => $post['company_id'],
							'order_total' => $total_cart_amount,
							'order_status' => 'n',
							'order_remarks' => addslashes($post['pickup_remarks']),
							'order_pickupdate' => $order_date_db,
							'order_pickupday' => $post['pickup_day'],
							'order_pickuptime' => $post['pickup_hour'].':'.$pickup_minute,
							'ok_msg' => '0',
							'hold_msg' => '0',
							'completed' => '0',
							'option' => '1',
							'created_date' => date('Y-m-d H:i:s',time()),
							'payment_via_paypal' => ($post['payment_via']?$post['payment_via']:'0'),
							'shop_url' => $shop_url,
							'pic_apply_tax' => $post['pic_apply_tax'],
							'pic_tax_amount_added' => $post['pic_tax_amount_added'],
							'lang_id' => $post['language_id'],
							'disc_percent' => ( isset($post['disc_percent'])?$post['disc_percent']:0 ),
							'disc_price' => ( isset($post['disc_price'])?$post['disc_price']:0 ),
							'disc_amount' => ( isset($post['disc_amount'])?$post['disc_amount']:0 ),
							'disc_client' => ( isset($post['disc_client'])?$post['disc_client']:0 ),
							'disc_client_amount' => ( isset($post['disc_client_amount'])?$post['disc_client_amount']:0 ),
							'get_invoice' => ( isset($post['get_invoice'])?$post['get_invoice']:0 )
					);
	
					$order_data = $insert_arr;
				}
				elseif( $service_select == 'delivery' )
				{
					$order_date = array();
					$order_date_db = '';
					if(isset($post['delivery_date']) && $post['delivery_date'] != ''){
						$order_date = explode('/',$post['delivery_date']);
						$order_date_db = date('Y-m-d',strtotime( $order_date[2].'-'.$order_date[1].'-'.$order_date[0].' 00:00:00' ));
					}
					
					$delivery_minute = ( (isset($post['delivery_minute']))?$post['delivery_minute']:'' );
					
					if(strlen($delivery_minute) == 1){
						$delivery_minute = '0'.$delivery_minute;
					}
						
					$insert_arr = array(    
							'clients_id' => $client_id,
							'company_id' => $post['company_id'],
							'order_total' => $total_cart_amount,
							'order_status' => 'n',
							'delivery_streer_address' => addslashes($post['delivery_streer_address']),
							'delivery_busnummer' => ( (isset($post['delivery_busnummer']))?$post['delivery_busnummer']:'' ),
							'delivery_area'=> addslashes($post['delivery_area']),
							'delivery_city'=> addslashes($post['delivery_city']),
							'delivery_zip' => $post['delivery_zip'],
							'delivery_country' => ( (isset($post['delivery_country']))?$post['delivery_country']:'' ),
							'delivery_day' => ( (isset($post['delivery_day']))?$post['delivery_day']:'' ),
							'delivery_hour' => ( (isset($post['delivery_hour']))?$post['delivery_hour']:'' ),
							'delivery_minute' => $delivery_minute,
							'delivery_date' => $order_date_db,
							'delivery_remarks' => addslashes($post['delivery_remarks']),
							'delivery_cost' => $post['rsDELPRICE'],
							'ok_msg' => '0',
							'hold_msg' => '0',
							'completed' => '0',
							'option' => '2',
							'created_date' => date('Y-m-d H:i:s',time()),
							'payment_via_paypal' => ($post['payment_via']?$post['payment_via']:'0'),
							'shop_url' => $shop_url,
							'del_apply_tax' => $post['del_apply_tax'],
							'del_tax_amount_added' => $post['del_tax_amount_added'],
							'lang_id' => $post['language_id'],
							'disc_percent' => ( isset($post['disc_percent'])?$post['disc_percent']:0 ),
							'disc_price' => ( isset($post['disc_price'])?$post['disc_price']:0 ),
							'disc_amount' => ( isset($post['disc_amount'])?$post['disc_amount']:0 ),
							'disc_client' => ( isset($post['disc_client'])?$post['disc_client']:0 ),
							'disc_client_amount' => ( isset($post['disc_client_amount'])?$post['disc_client_amount']:0 ),
							'get_invoice' => ( isset($post['get_invoice'])?$post['get_invoice']:0 ),
							'name' => ( (isset($post['del_name']))?addslashes($post['del_name']):'' ),
							'phone_reciever' => ( (isset($post['phone_reciever']))?$post['phone_reciever']:'' )
					);
	
					$order_data = $insert_arr;
				}
					
				if(!empty( $insert_arr ))
				{
					
					$this->db->select('labeler_print_type');
					$labeler_data = $this->db->get_where('general_settings', array('company_id' => $post['company_id']))->result();
					if(!empty($labeler_data)){
						if($labeler_data['0']->labeler_print_type == 'manual')
							$insert_arr['labeler_printed'] = 1;
						else 
							$insert_arr['labeler_printed'] = 0;
					}
					
					/*if($client_id == 8){
						$insert_arr['labeler_printed'] = 1;
						$insert_arr['printed'] = 1;
					}*/
					
					$order_id = 0;
					if($insert_arr['payment_via_paypal']){
						$this->db->insert( 'orders_tmp' , $insert_arr );
						$order_id = $this->db->insert_id();
					}else{
						$this->db->insert( 'orders' , $insert_arr );
						$order_id = $this->db->insert_id();
						
						// ---- >> Associate Client
						
						$associate = $this->associate_client_with_company( $client_id, $this->company_id );
					}
					
						
					$this->response( array('error' => 0, 'message'=> '', 'data' => $order_id ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=> _('Can\'t submit your order !'), 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=> _('Couldn\'t get the client ID !'), 'data' => '' ), 404 );
			}
		}
	
		exit;
	}
	
	function submit_order_detail_post()
	{
		$api_verified = $this->verify_api_request_return_get();
		$inser_odet_arr = $this->post();
	
		$send_mail = $inser_odet_arr['send_mail'];
	
		$order_id = $inser_odet_arr['orders_id'];
		$client_id = $inser_odet_arr['client_id'];
	
		unset( $inser_odet_arr['send_mail'] );
		unset( $inser_odet_arr['client_id'] );
	
		if($this->post('ip_address')){
			$ip_address = $inser_odet_arr['ip_address'];
			unset( $inser_odet_arr['ip_address'] );
		}
		
		$order_data = array();
		$order_details_data = array();
	
		if( isset($inser_odet_arr['order_data']) )
		{
			//$order_data = $inser_odet_arr['order_data'];
			unset( $inser_odet_arr['order_data'] );
		}
	
		if( isset($inser_odet_arr['order_details_data']) )
		{
			//$order_details_data = $inser_odet_arr['order_details_data'];
			unset( $inser_odet_arr['order_details_data'] );
		}
	
		$subtotal = 0;
		$defaultPrice = 0;
		if($inser_odet_arr['content_type'] == 0 || $inser_odet_arr['content_type'] == 2){
			$subtotal = $inser_odet_arr['product_price'];
			$defaultPrice = $inser_odet_arr['default_price'];
		}
		else{
			$subtotal = $this->number2db(round($inser_odet_arr['product_price']*1000,2));
			$defaultPrice = $this->number2db(round($inser_odet_arr['default_price']*1000,2));
		}
		
		$order_detail_arr = array(
				'orders_id' => $order_id,
				'products_id' => $inser_odet_arr['product_id'],
				'default_price' => $this->number2db($defaultPrice),
				'discount' => $this->number2db($inser_odet_arr['product_discount']),
				'weight_unit' => ( (isset($inser_odet_arr['weight_unit']))?$inser_odet_arr['weight_unit']:'' ),
				'add_costs' => ( (isset($inser_odet_arr['add_costs']))?$inser_odet_arr['add_costs']:'' ),
				'quantity' => $inser_odet_arr['product_quantity'],
				'sub_total' => $this->number2db($subtotal),
				'total' => $this->number2db($inser_odet_arr['total_amount']),
				'pro_remark' => ( (isset($inser_odet_arr['pro_remark']))?addslashes($inser_odet_arr['pro_remark']):'' ),
				'content_type' => $inser_odet_arr['content_type'],
				'image' => ( (isset($inser_odet_arr['image']))?$inser_odet_arr['image']:'' ),
				'extra_field' => ( (isset($inser_odet_arr['extra_field']))?$inser_odet_arr['extra_field']:'' ),
				'extra_name' => ( (isset($inser_odet_arr['extra_name']))?addslashes($inser_odet_arr['extra_name']):'' ),
				'weight_per_unit' => ( (isset($inser_odet_arr['weight_per_unit']))?$inser_odet_arr['weight_per_unit']:'' )
		);
									
		// Checking whether Order id exist in Temporary table
		$order_data = $this->db->where( array('id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders_tmp' )->row_array();
		
		$order_details_id = 0;
		if(!empty($order_data)){ // If order is made with online payment then inserting in temporary order details
			
			$this->db->insert( 'order_details_tmp' , $order_detail_arr );
			$order_details_id = $this->db->insert_id();
			
			// to be use in sending automail
			if(isset($ip_address)){
				$this->db->where('id', $order_id);
				$this->db->update('orders_tmp', array('ip_address' => $ip_address));
			}
			
			$send_mail = false;
			
		}else{ // If order is made normally without online payment then inserting in normal order details
			
			// Fetching order data from normal table
			$this->db->select('orders.*,delivery_country.country_name as delivery_country_name');
			$this->db->join('country as delivery_country','orders.delivery_country = delivery_country.id','left');//used in delivery address
			$order_data = $this->db->where( array('orders.id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders' )->row_array();
			
			$this->db->insert( 'order_details' , $order_detail_arr );
			$order_details_id = $this->db->insert_id();
		}
		
	
		if( $order_details_id )
		{
			if( $send_mail )
			{
				// When Normal order (without online payment)
				if(!empty($order_data)){
					$this->db->where( array('orders_id'=>$order_id) );
					$this->db->join( 'products', 'products.id = order_details.products_id' );
					$order_details_data = $this->db->get( 'order_details' )->result_array();
					
					// Updating IP address
					$this->db->where('id', $order_id);
					$this->db->update('orders', array('ip_address' => $ip_address));
					
					$this->send_order_details_post( $order_id, $client_id, $order_data, $order_details_data, $ip_address );
				}else{
					// If payment is made online then updating IP address column of temporary order table
					// to be use in sending automail
					/*$this->db->where('id', $order_id);
					$this->db->update('orders_tmp', array('ip_address' => $ip_address));*/
				}
				
			}
				
			$this->response( array('error' => 0, 'message'=> '', 'data' => $order_details_id ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=> _('Some error ocured ! Can\'t place your order successfully.'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	function associate_client_with_company( $client_id, $company_id )
	{
		
		if( !$client_id || !$company_id )
			return false;
		 
		$this->db->select('first_name');
		$this->db->where('id',$company_id);
		$company = $this->db->get('company')->row();
		//$company_email = $company->email;
		
		$this->db->select('emailid,activate_discount_card');
		$company_gs = $this->db->where( array('company_id'=>$company_id) )->get( 'general_settings' )->row();
		$company_email = $company_gs->emailid;
		
		$new_client = 0;
		//$clients_associated = $this->db->get_where('client_numbers', array('company_id'=>$company_id, 'client_id' => $client_id, 'associated' => 0))->result();
		$clients_associated = $this->db->get_where('client_numbers', array('company_id'=>$company_id, 'client_id' => $client_id))->result();
		if(!empty($clients_associated)){
			$clients_associated = $clients_associated['0'];
			if(!$clients_associated->associated){
				$new_client = 1;
				$this->db->where(array('company_id'=>$company_id, 'client_id' => $client_id));
				$this->db->update('client_numbers', array('associated' => '1'));
			}
		}else{
			$new_client = 1;
			$insert_array = array(
				'company_id' => $company_id,
				'client_id'  => $client_id,
				'newsletter' => 'subscribe',
				'associated' => '1'
			);
			$this->db->insert('client_numbers', $insert_array);
		}
		
		if($new_client){
	
			$client = $this->db->where( array('id'=>$client_id) )->get( 'clients' )->row();
			// ---- >>> Sending Mail to Company <<< ---- //
			
			$cmp_mail_subject = 'OBS - '._('New Client Registered');
			
			$discount_num = '';
			if($company_gs->activate_discount_card != 0){
				$discount_card_info = $this->db->get_where("client_numbers", array("client_id" => $client_id, 'company_id'=>$company_id))->result_array();
				$fp = fopen(APPPATH . "/controllers/new-api/logs-disc.txt","a");
				fwrite($fp,"general settings: ".json_encode($discount_card_info));
				fclose($fp);
				$discount_num = '--';
				if(!empty($discount_card_info)){
					if($discount_card_info['0']['discount_card_number'] && $discount_card_info['0']['discount_card_number'] != 0)
						$discount_num = $discount_card_info['0']['discount_card_number'];
				}
			}
			
			$mail_data = array();
			$mail_data['first_name'] = $company->first_name;
			$mail_data['firstname_c'] = $client->firstname_c;
			$mail_data['lastname_c'] = $client->lastname_c;
			$mail_data['company_c'] = $client->company_c;
			$mail_data['notifications'] = $client->notifications;
			$mail_data['vat_c'] = $client->vat_c;
			$mail_data['address_c'] = $client->address_c;
			$mail_data['housenumber_c'] = $client->housenumber_c;
			$mail_data['postcode_c'] = $client->postcode_c;
			$mail_data['city_c'] = $client->city_c;
			$mail_data['phone_c'] = $client->phone_c;
			$mail_data['mobile_c'] = $client->mobile_c;
			$mail_data['email_c'] = $client->email_c;
			$mail_data['discount_num'] = $discount_num;
			
			$fp = fopen(APPPATH . "/controllers/new-api/logs-md.txt","a");
			fwrite($fp,"Mail data1: ".json_encode($mail_data));
			fclose($fp);
			
			$mail_body = $this->load->view( 'mail_templates/client_associated', $mail_data, true);
			
			send_email( $company_email, $this->config->item('no_reply_email'), $cmp_mail_subject, $mail_body, NULL, NULL, NULL, 'no_reply', 'company', 'api_client_associated');
			//send_email( "shyammishra@cedcoss.com", $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
			return true;
		}
			
		return true;
	}
	
	function send_order_details_post( $order_id = 0, $client_id = 0, $order_data = array(), $order_details_data = array(), $ip_address = '' )
	{
		if( !$order_id || !$client_id )
			return false;
	
		$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
		$this->db->join('country','country.id = clients.country_id','left');
		$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
		// $company = $this->db->where( array('id'=>$this->company_id) )->get( 'company' )->row();
		$company = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row();
	
		$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order_data['company_id']) )->get( 'client_numbers' )->result();
		$c_discount_number = '--';
		if(!empty($client_discount_number)){
			$c_discount_number = $client_discount_number['0']->discount_card_number;
		}
	
		$sub_admin = array();
		if($company->role == 'super'){
			$this->db->select("company_name,email_ads");
			$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
		}
	
		$this->db->select('emailid, subject_emails, orderreceived_msg, disable_price, activate_discount_card, disc_per_amount, disc_after_amount');
		$company_gs = $this->db->where( array('company_id'=>$order_data['company_id']) )->get( 'general_settings' )->result();
		if(!empty($company_gs))
			$company_gs = $company_gs['0'];
	
		// Fetching Delivery settings
		$delivery_settings = array();
		if($order_data['option'] == '2'){
			$delivery_settings = $this->get_delivery_settings($order_data['company_id']);
		}
		
		$fp = fopen(APPPATH . "/controllers/new-api/logs.txt","a");
	
		fwrite($fp,json_encode(array('client'=>$client,'company'=>$company,'company_gs'=>$company_gs,'order_data'=>$order_data,'order_details_data'=>$order_details_data)));
	
		fclose($fp);
	
		if( !empty($client) && !empty($company) && !empty($company_gs) )
		{
			$this->load->library('utilities');
			$this->load->helper('phpmailer');
				
			$company_email = $company_gs->emailid;
			$company_name = $company->company_name;
			if(!empty($sub_admin))
				$company_name = $sub_admin['company_name'];
				
			$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
			$orderreceived_msg = $company_gs->orderreceived_msg;
			$products_pirceshow_status = $company_gs->disable_price;
			$is_set_discount_card = $company_gs->activate_discount_card;
			
			$Options4 = '';
			
			if(!empty($sub_admin)){
				if($sub_admin['company_name'] == "1")
				{
					$email_messages = $this->db->get('email_messages')->row();
					$Options4 = $email_messages->emailads_text_message;
				}
			}else{
				if($company->email_ads == "1")
				{
					$email_messages = $this->db->get('email_messages')->row();
					$Options4 = $email_messages->emailads_text_message;
				}
			}
			
			$mail_data['order_id'] = $order_id;
			$mail_data['order_data'] = $order_data;
			$mail_data['ip_address'] = $ip_address;
			$mail_data['client'] = $client;
			$mail_data['company_name'] = $company_name;
			$mail_data['products_pirceshow_status'] = $products_pirceshow_status;
			$mail_data['order_details_data'] = $order_details_data;
			//$mail_data['disc_per_client_amount'] = $disc_per_client_amount;
			$mail_data['company_gs'] = $company_gs;
			$mail_data['c_discount_number'] = $c_discount_number;
			$mail_data['Options4'] = $Options4;
			$mail_data['orderreceived_msg'] = $orderreceived_msg;
			$mail_data['is_set_discount_card'] = $is_set_discount_card;
			$mail_data['is_international'] = false;
			$mail_data['is_send_order_mail'] = true;
			

			//countries details for international delivery
			if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
				$mail_data['is_international'] = true;
			}
			/* $countries = array();
			if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
				$mail_data['is_international'] = true;
				$this->db->select('id,country_name');
				$country_arr = $this->db->get('country')->result();
				foreach($country_arr as $country){
					$countries[$country->id] = $country;
				}
			}
			$mail_data['countries'] = $countries; */
			
			$mail_body = '';
			if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
				$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin_int', $mail_data, true );
			}
			else{
				$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
			}
			/*if($client_id == 8)
				$flag = send_email( "shyammishra@cedcoss.com", $this->config->item('no_reply_email'), $orderreceived_subject, $mail_body, _('Order').' '.$company_name, NULL, NULL, 'no_reply', 'company', 'api_new_order_details');
			else*/
				$flag = send_email( $company_email, $client->email_c, $orderreceived_subject, $mail_body, _('Order').' '.$company_name, NULL, NULL, 'client', 'company', 'api_new_order_details');
				
			//----------------------------------------------------------------------------------------------------------------------------------//
			// Sending to Client
	
			if($order_data['lang_id'] == 1)
				$this->lang->load('mail', 'english' );
			elseif($order_data['lang_id'] == 2)
				$this->lang->load('mail', 'dutch' );
			elseif($order_data['lang_id'] == 3)
				$this->lang->load('mail', 'french' );
			else
				$this->lang->load('mail', 'dutch' );
			
			if(!empty($delivery_settings) && $delivery_settings[0]->type == 'international'){
				$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_client_int', $mail_data, true );
			}
			else{
				$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_client', $mail_data, true );
			}
			
			/*if($client_id == 8)
				$flag = send_email( "shyammishra@cedcoss.com", $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name, NULL, NULL, 'company', 'client', 'api_new_order_details' );
			else*/
				$flag = send_email( $client->email_c, $company_email, $orderreceived_subject, $mail_body, _('Order').' '.$company_name, NULL, NULL, 'company', 'client', 'api_new_order_details' );
				
				
			$this->db->where('id',$order_id);
			$this->db->update('orders', array('print_ready' => 1));
				
			return true;
		}
		else
		{
			$this->response( array('error' => 1, 'message'=> _('Can\'t send mail to company & clients !'), 'data' => $order_id ), 404 );
		}
	}
	
	/**
	 * This function is used to change password of given user
	 */
	function change_password_post()
	{
		$client_id = $this->post('client_id');
		$password = $this->post('password');
		$new_password = $this->post('new_password');
	
		$this->db->where( 'id', $client_id );
		$this->db->where( 'password_c', $password );
	
		$isChanged = $this->db->update('clients', array( 'password_c' => $new_password ) );
	
		if( $this->db->affected_rows() != 0 )
		{
			$this->response( array('error' => 0, 'message'=>_('Password has been changed successfully !'), 'data' => '' ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Incorrect password entered !'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	/**
	 * This function is used to update user profile
	 */
	function edit_user_profile_post()
	{
		$client_id = $this->post('client_id');
		$company_id=$this->post('company_id');
	
		$this->db->select('id,email_c');
		$client = $this->db->where( array('id'=>$client_id) )->get( 'clients' )->row();//current client details
		if( !$client_id )
		{
			$this->response( array('error' => 1, 'message'=>_('Can\'t update your data !'), 'data' => '' ), 404 );
		}
		else
		{
			$newsletters = $this->post('newsletters');
			$notifications = $this->post('notifications');

			$email_c = '';
			// Required email check
			if(!$this->post('email_c') || $this->post('email_c') == ''){
				$this->response( array('error' => 1, 'message'=>_('Email is required'), 'data' => '' ), 404 );
			}
			$email_c = $this->post('email_c');
			
			// Valid email check
			$valid_email = ( ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email_c)) ? FALSE : TRUE );
			if(!$valid_email){
				$this->response( array('error' => 1, 'message'=>_('Email must be valid'), 'data' => '' ), 404 );
			}
			
			// Unique email check
			if($client->email_c != $email_c){
				$query = $this->db->limit(1)->get_where('clients', array('email_c' => $email_c));
				if($query->num_rows() !== 0){
					$this->response( array('error' => 1, 'message'=> _('Email already exist!'), 'data' => '' ), 404 );
				}
			}
			
			$update_array = array(
					'firstname_c' => addslashes($this->post('firstname_c')),
					'lastname_c' => addslashes($this->post('lastname_c')),
					'email_c' => $email_c,
					'address_c' => addslashes($this->post('address_c')),
					'housenumber_c' => addslashes($this->post('housenumber_c')),
					'postcode_c' => $this->post('postcode_c'),
					'city_c' => addslashes($this->post('city_c')),
					'country_id' => $this->post('country_id'),
					'phone_c' => $this->post('phone_c'),
					// 'mobile_c' => $this->post('mobile_c'),
					'fax_c' => $this->post('fax_c'),
					'newsletters' => ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe',
					'notifications' => ( $notifications == 'subscribe' )?'subscribe':'unsubscribe',
					'company_c' => $this->post('company_c'),
					'vat_c' => $this->post('vat_c'),
					'updated_c' => date('Y-m-d H:i:s',time())
			);
	
			$this->db->where( 'id', $client_id );
			$isChanged = $this->db->update('clients', $update_array );
				
			if( $this->db->affected_rows() != 0 )
			{
				$update_discount_array = array();
				$insert_array = array();
				if($this->post('discount_card_number')){
					$update_discount_array['discount_card_number'] = $this->post('discount_card_number');
					$insert_array['discount_card_number'] = $this->post('discount_card_number');
				}
	
				$update_discount_array['newsletter'] = ( ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe');
	
				$is_peresent = $this->db->get_where("client_numbers" , array("client_id" => $client_id, "company_id" => $company_id))->result_array();
				if(!empty($is_peresent)){
					$this->db->where( 'client_id', $client_id );
					$this->db->where( 'company_id', $company_id );
					$isChanged = $this->db->update('client_numbers', $update_discount_array );
				}else{
					$insert_array["client_id"] = $client_id;
					$insert_array["company_id"] = $company_id;
					$insert_array["newsletter"] = ( ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe');
					$this->db->insert("client_numbers" , $insert_array);
				}
	
				$this->response( array('error' => 0, 'message'=>_('Your profile has been updated successfully ! You may continue ...'), 'data' => '' ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Can\'t update your data !'), 'data' => $update_discount_array ), 404 );
			}
		}
	
		exit;
	}
	
	/**
	 * This function is used to get orders of 
	 */
	function get_client_orders_post()
	{
		$clients_id = $this->post('clients_id');
		$company_id = $this->post('company_id');
		$company_role = $this->post('company_role');
	
		if( !$clients_id || !$company_id )
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! Can\'t fetch your orders.'), 'data' => '' ), 404 );
		}
		else
		{
			if( $company_role == 'master' )
			{
				$this->db->select( '*, `orders`.id AS order_id, DATE_FORMAT( orders.created_date, "%e/%c/%Y") AS created_date, DATE_FORMAT( orders.order_pickupdate, "%e/%c/%Y") AS order_pickupdate, DATE_FORMAT( orders.delivery_date, "%e/%c/%Y") AS delivery_date', FALSE);
				$this->db->where( 'orders.clients_id', $clients_id );
				$this->db->where( 'orders.company_id', $company_id );
				$this->db->where( 'orders.hidden', false);
				$this->db->join( 'company', 'company.id = orders.company_id' );
				$this->db->order_by('orders.created_date','DESC');
				$this->db->limit(20);
				$orders = $this->db->get('orders')->result();
			}
			elseif( $company_role == 'super' )
			{
				$orders = array();
	
				$this->db->select( 'company.id' );
				$this->db->where( 'company.parent_id', $company_id );
				$this->db->where( 'company.role', 'sub' );
				$sub_admins = $this->db->get('company')->result();
	
				if( !empty( $sub_admins ) )
				{
					foreach( $sub_admins as $sa )
					{
						if(count($orders) < 20){
							$sub_admin_id = $sa->id;
							
							$this->db->select( '*, `orders`.id AS order_id, DATE_FORMAT( orders.created_date, "%e/%c/%Y") AS created_date, DATE_FORMAT( orders.order_pickupdate, "%e/%c/%Y") AS order_pickupdate, DATE_FORMAT( orders.delivery_date, "%e/%c/%Y") AS delivery_date', FALSE);
							$this->db->where( 'orders.clients_id', $clients_id );
							$this->db->where( 'orders.company_id', $sub_admin_id );
							$this->db->where( 'orders.hidden', false);
							$this->db->join( 'company', 'company.id = orders.company_id' );
							$this->db->order_by('orders.created_date','DESC');
							$this->db->limit(20-count($orders));
							$sub_orders = $this->db->get('orders')->result();
							
							$orders = array_merge( $orders, $sub_orders );
						}
						
					}
				}
			}
				
			if( !empty( $orders ) )
			{
				$this->response( array('error' => 0, 'message'=> '', 'data' => $orders ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Sorry ! You haven\'t placed any order yet.'), 'data' => '' ), 404 );
			}
		}
	
		exit;
	}
	
	/**
	 * This function is used to get order detail of given order ID
	 */
	function get_order_details_post()
	{
		$order_id = $this->post('order_id');
	
		$this->db->select( '*, DATE_FORMAT( created_date, "%e/%c/%Y") AS created_date, DATE_FORMAT( order_pickupdate, "%e/%c/%Y") AS order_pickupdate, DATE_FORMAT( delivery_date, "%e/%c/%Y") AS delivery_date', FALSE);
		$this->db->where( 'orders.id', $order_id );
	
		$order = $this->db->get('orders')->row();
	
		if( !empty($order) )
		{
			$this->db->select('area_name');
			$this->db->where( 'id', $order->delivery_area );
			$area_name = $this->db->get('delivery_areas')->row();
				
			if( !empty( $area_name ) && isset( $area_name->area_name ) )
				$order->area_name = $area_name->area_name;
			else
				$order->area_name = '';
				
			$this->db->select('city_name');
			$this->db->where( 'id', $order->delivery_city );
			$city_name = $this->db->get('delivery_settings')->row();
			 
			if( !empty( $city_name ) && isset( $city_name->city_name ) )
				$order->city_name = $city_name->city_name;
			else
				$order->city_name = '';
			 
			$order_detail_arr = array();
			$order_detail_arr['order'] = $order;
			$order_detail_arr['order_prod'] = array();
				
			$this->db->select( '*, products.id AS product_id, products.discount AS discount_unit, order_details.id AS order_detail_id, products.available_after', FALSE);
			$this->db->where( 'order_details.orders_id', $order_id );
			$this->db->join( 'products', 'products.id = order_details.products_id' );
			$order_details = $this->db->get('order_details')->result();
				
			$order_detail_arr['order_prod'] = $order_details;
				
			$this->response( array('error' => 0, 'message'=> '', 'data' => $order_detail_arr ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Can\'t fetch this order detail.'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	/**
	 * Function to verify email address not exist already in our database
	 */
	function verify_email_address_post()
	{
		$email_c = $this->post('email_c');
	
		$this->db->where( 'clients.email_c', $email_c);
		$user = $this->db->get('clients')->row();
			
		if( !empty($user) )
		{
			$this->response( array('error' => 1, 'message'=> '' , 'data' => 'exists' ), 200 );
		}
		else
		{
			$this->response( array('error' => 0, 'message'=>_('Error - No user details found !'), 'data' => '' ), 404 );
		}
	}
	
	/**
	 * Funcion to register client
	 */
	function register_user_post()
	{
		if(  $this->post('action') == 'register_user' )
		{
			$this->db->where( 'clients.email_c', $this->post('email_c'));
			$user = $this->db->get('clients')->row();
				
			if( empty($user) )
			{
				$newsletters = $this->post('newsletters');
		
	// 			$this->load->helper('string');
	// 			$verfication_code = random_string('numeric', 5);
				
				$registration_array=array(
						'company_id' => $this->post('company_id'),
						'firstname_c' => $this->post('firstname_c'),
						'lastname_c' => $this->post('lastname_c'),
						'address_c' => $this->post('address_c'),
						'housenumber_c' => $this->post('housenumber_c'),
						'busnummer' => $this->post('busnummer'),
						'postcode_c' => $this->post('postcode_c'),
						'city_c' => $this->post('city_c'),
						'country_id' => $this->post('country_id'),
						'phone_c' => $this->post('phone_c'),
						// 'mobile_c' => $this->post('mobile_c'),
						'fax_c' => $this->post('fax_c'),
						'email_c' => $this->post('email_c'),
						'password_c' => $this->post('password_c'),
						'newsletters' => ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe',
						'notifications' => $this->post('notifications'),
						'company_c' => $this->post('company_c'),
						'vat_c' => $this->post('vat_c'),
						'company_id' => $this->post('company_id'),
						'created_c' => date('Y-m-d H:i:s',time()),
						'updated_c' => date('Y-m-d H:i:s',time()),
						'verify_code' => '',//$verfication_code
						'verified' => 1
				);
					
				$this->db->insert('clients',$registration_array);
				$client_id = $this->db->insert_id();
					
				if($this->db->affected_rows())
				{
		
					$discount_card_array=array('discount_card_number' => $this->post('discount_card_number'),
							'client_id'=>$client_id,
							'newsletter' => ( $newsletters == 'subscribe' )?'subscribe':'unsubscribe',
							'company_id' => $this->post('company_id'));
					$this->db->insert('client_numbers',$discount_card_array);
		
					//Admin Details
					$admin_info = $this->db->get("admin")->result_array();
					$admin_email = $admin_info['0']['email'];
					$admin_name = $admin_info['0']['admin_name'];
		
					//Company Details
					$this->db->where(array('id'=>$this->post('company_id')));
					$company = $this->db->get('company')->row();
					$company_email = $company->email;
						
					//Client Details
					$this->db->where('email_c',$this->post('email_c'));
					$client = $this->db->get('clients')->row();
					$client_email = $client->email_c;
		
					if($this->post("language_id")){
						if($this->post("language_id") == 1)
							$this->lang->load('mail', 'english' );
						elseif($this->post("language_id") == 2)
							$this->lang->load('mail', 'dutch' );
						elseif($this->post("language_id") == 3)
							$this->lang->load('mail', 'french' );
					}else{
						$this->lang->load('mail', 'dutch' );
					}
		
					$this->load->helper('phpmailer');
		
					$cln_mail_subject = $this->lang->line('mail_welcome_webshop').' '.$company->company_name;
					
	// 				$mail_data['verfication_code'] = $verfication_code;
	// 				$mail_data['first_name'] = $company->first_name;
	// 				$mail_data['last_name'] = $company->last_name;
	// 				$mail_data['company_name'] = $company->company_name;
	// 				$mail_data['firstname_c'] = $client->firstname_c;
					
	// 				$mail_body = $this->load->view('mail_templates/account_verification_to_client', $mail_data, true);
	// 				send_email( $client_email, $this->config->item('no_reply_email'), $cln_mail_subject, $mail_body, $admin_name, NULL, NULL, 'no_reply', 'client', 'api_new_client_verf_code');
					
					$mail_data['firstname_c'] = $client->firstname_c;
					$mail_data['email_c'] = $client->email_c;
					$mail_data['password_c'] = $client->password_c;
					$mail_data['first_name'] = $company->first_name;
					$mail_data['last_name'] = $company->last_name;
					$mail_data['company_name'] = $company->company_name;
						
					$mail_body = $this->load->view( 'mail_templates/register_success_mail_to_client', $mail_data, true );
					send_email( $client_email, $this->config->item('no_reply_email'), $cln_mail_subject, $mail_body, $admin_name, NULL, NULL, 'no_reply', 'client', 'api_client_verified');
						
					$this->db->where( 'client_numbers.company_id', $this->post('company_id') );
					$this->db->where( 'client_numbers.client_id', $client->id );
					$clientInfo = $this->db->get('client_numbers')->result_array();
					if(!empty($clientInfo)){
						$clientInfo = $clientInfo['0'];
						$client->dcn = $clientInfo['discount_card_number'];
						$client->newsletter = $clientInfo['newsletter'];
						$client->disc_per_client = $clientInfo['disc_per_client'];
						unset($client->password_c);
					}
					
					$this->response( array('error' => 0, 'message'=> '', 'data' => $client ), 200 );
		
				}
				else
				{
					$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be registered.'), 'data' => '' ), 404 );
				}
			}
			else{
				$this->response( array('error' => 1, 'message'=> 'email_exists' , 'data' => 'exists' ), 404 );
			}
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be registered.'), 'data' => '' ), 404 );
		}
	}
	
	/**
	 * Function to update new password (when client forget his password)
	 */
	function forget_password_post()
	{
		$email_c = $this->post('email_c');
	
		$this->db->where( 'clients.email_c', $email_c);
		$user = $this->db->get('clients')->row();
			
		if( !empty($user) )
		{
			$password_c = $this->generate_password( 8 );
			$user_id = $user->id;
				
			$this->db->where( 'email_c' , $email_c );
			$changed = $this->db->update('clients',array('password_c'=> $password_c ));
				
			if( $changed )
			{
				if($this->post("language_id")){
					if($this->post("language_id") == 1)
						$this->lang->load('mail', 'english' );
					elseif($this->post("language_id") == 2)
						$this->lang->load('mail', 'dutch' );
					elseif($this->post("language_id") == 3)
						$this->lang->load('mail', 'french' );
				}else{
					$this->lang->load('mail', 'dutch' );
				}
	
				$this->load->helper('phpmailer');
	
				// ---- >>> Sending Mail to Client <<< ---- //
	
				$mail_subject = 'OBS - '.$this->lang->line("mail_pswd_change");
				
				$mail_data['firstname_c'] = $user->firstname_c;
				$mail_data['password_c'] = $password_c;
				$mail_body = $this->load->view('mail_templates/forget_password_to_client', $mail_data, true);
				$mail_sent = send_email( $email_c, $this->config->item('no_reply_email'), $mail_subject, $mail_body, NULL, NULL, NULL, 'no_reply', 'client', 'api_client_forget_password' );
	
				if( $mail_sent )
				{
					$this->response( array('error' => 0, 'message'=>_('Your password has been changed and sent to your email address successfully !') , 'data' => '' ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=>_('Your password has been set sucessfully, but can\'t send you mail. So please try again !'), 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Can\'t change your password !'), 'data' => '' ), 404 );
			}
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Incorrect email address entered !'), 'data' => '' ), 404 );
		}
	}
	
	/**
	 * Function to generate new random string. Especially for passowrd
	 */
	function generate_password( $length = 20 )
	{
		$chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
		$str = '';
		$max = strlen($chars) - 1;
	
		for ($i=0; $i < $length; $i++)
			$str .= $chars[rand(0, $max)];
	
			return $str;
	}
	
	/**
	 * Function to verify Facebook users. whether they are already registered with us
	 */
	function verify_fb_user_post()
	{
		$this->db->where( 'clients.email_c', $this->post('email_c') );
		$this->db->where( 'clients.fb_id', $this->post('fb_id') );
	
		$client = $this->db->get('clients')->row();
	
		if( !empty($client) && isset($client->id) )
		{
			$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $client ), 200 );
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid username or password !'), 'data' => '' ), 404 );
		}
	
		exit;
	}
	
	/**
	 * If they are not register with us then register them
	 */
	function register_fb_user_post()
	{
		$registration_array=array(
				'firstname_c' => $this->post('firstname_c'),
				'lastname_c' => $this->post('lastname_c'),
				'address_c' => $this->post('address_c'),
				'email_c' => $this->post('email_c'),
				'fb_id' => $this->post('fb_id'),
				'created_c' => date('Y-m-d H:i:s',time()),
				'updated_c' => date('Y-m-d H:i:s',time()),
				'verified' => '1'
		);
	
		$this->db->insert('clients',$registration_array);
		$client_id = $this->db->insert_id();
	
		if($this->db->affected_rows())
		{
	
			$discount_card_array=array('discount_card_number' => '',
					'client_id'=>$client_id,
					'company_id' => $this->post('company_id'));
			$this->db->insert('client_numbers',$discount_card_array);
				
			//Admin Details
			$admin_email = $this->config->item('site_admin_email');
				
			//Company Details
			$this->db->where(array('id'=>$this->post('company_id')));
			$company = $this->db->get('company')->row();
			$company_email = $company->email;
	
			//Client Details
			$this->db->where('email_c',$this->post('email_c'));
			$client = $this->db->get('clients')->row();
			$client_email = $client->email_c;
				
				
			if($this->post("language_id")){
				if($this->post("language_id") == 1)
					$this->lang->load('mail', 'english' );
				elseif($this->post("language_id") == 2)
					$this->lang->load('mail', 'dutch' );
				elseif($this->post("language_id") == 3)
					$this->lang->load('mail', 'french' );
			}else{
				$this->lang->load('mail', 'dutch' );
			}
				
			$this->load->helper('phpmailer');
				
			$cln_mail_subject = $this->lang->line('mail_welcome_webshop').' '.$company->company_name;
			
			$mail_data['firstname_c'] = $client->firstname_c;
			$mail_data['email_c'] = $client->email_c;
			$mail_data['password_c'] = $client->password_c;
			$mail_data['first_name'] = $company->first_name;
			$mail_data['last_name'] = $company->last_name;
			$mail_data['company_name'] = $company->company_name;
			
			$mail_body = $this->load->view( 'mail_templates/fb_register_success_to_client', $mail_data, true );
			send_email( $client_email, $this->config->item('no_reply_email'), $cln_mail_subject, $mail_body, NULL, NULL, NULL, 'no_reply', 'client', 'api_client_registered_fb');
				
			$this->response( array('error' => 0, 'message'=> '', 'data' => $client ), 200 );
				
		}
		else
		{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be registered.'), 'data' => '' ), 404 );
		}
	}
	
	/**
	 * Function to save cart into db to use it aftrwards
	 */
	function save_order_post()
	{
	
		$api_verified = $this->verify_api_request_return_get();
		
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			// -- >> Save Order
	
			$client_id = $this->post( 'client_id' );
			$email_c = $this->post( 'email_c' );
			$total_cart_amount = $this->post( 'cart_total' );
			$cart = $this->post( 'cart' );
			$shop_url = $this->post( 'shop_url' );
			$company_id = $this->post( 'company_id' );
			$client_ip = $this->post( 'client_ip' );
			if( $client_id )
			{
				$insert_arr = array(
						'client_id' => $client_id,
						'company_id' => $company_id,
						'cart' => json_encode($cart),
						'cart_total' => $total_cart_amount,
						'client_email' => $email_c,
						'shop_url' => $shop_url,
						'client_ip' => $client_ip,
						'created_date' => date('Y-m-d H:i:s')
				);
	
				if(!empty( $insert_arr ))
				{
					$this->db->insert( 'saved_orders' , $insert_arr );
	
					$this->response( array('error' => 0, 'message'=> '', 'data' => '' ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=> _('Can\'t submit your order !'), 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=> _('Couldn\'t get the client ID !'), 'data' => '' ), 404 );
			}
		}
	
		exit;
	}
	
	function get_saved_order_post()
	{
		$api_verified = $this->verify_api_request_return_get();
	
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			// -- >> Save Order
	
			$client_id = $this->post( 'client_id' );
			$company_id = $this->post( 'company_id' );
				
			if( $client_id )
			{
				$current_time = date("Y-m-d H:i:s");
				$time_before_two_hours = date("Y-m-d H:i:s" , ( strtotime($current_time) - 60 * 60 * 2 ));
				$time_before_two_hours_two_minute = date("Y-m-d H:i:s" , ( strtotime($time_before_two_hours) - 60 * 2 ));
	
				// Getting saved carts since 2 hours and 2 mins
				$this->db->where( "client_id" , $client_id );
				$this->db->where( "company_id" , $company_id );
				$this->db->where( "created_date >=" , $time_before_two_hours_two_minute );
				$saved_carts = $this->db->get("saved_orders")->result_array();
	
				// Deleting saved cart after 2 hours and 2 mins
				$this->db->where( "client_id" , $client_id );
				$this->db->where( "company_id" , $company_id );
				$this->db->where( "created_date <" , $time_before_two_hours_two_minute );
				$this->db->delete("saved_orders");
	
				if(!empty( $saved_carts ))
				{
					$this->response( array('error' => 0, 'message'=> '', 'data' => $saved_carts ), 200 );
				}
				else
				{
					$this->response( array('error' => 1, 'message'=> _('Don\'t have any saved cart!'), 'data' => '' ), 404 );
				}
			}
			else
			{
				$this->response( array('error' => 1, 'message'=> _('Couldn\'t get the client ID !'), 'data' => '' ), 404 );
			}
		}
	
		exit;
	}
	
	/* function resend_code_to_email_post(){
		$user_id = $this->post('user_id');
		
		$user_info = $this->db->get_where('clients', array('id' => $user_id))->result();
		if(!empty($user_info)){

			if(!$user_info[0]->verified){
				
				$client = $user_info[0];
				$this->load->helper('string');
				$verfication_code = random_string('numeric', 5);
					
				$this->db->where('id',$user_id);
				if($this->db->update('clients',array('verify_code' => $verfication_code))){
					//Admin Details
					$admin_info = $this->db->get("admin")->result_array();
					$admin_email = $admin_info['0']['email'];
					$admin_name = $admin_info['0']['admin_name'];
					
					//Company Details
					$this->db->where(array('id'=>$this->post('company_id')));
					$company = $this->db->get('company')->row();
					$company_email = $company->email;
						
					if($this->post("language_id")){
						if($this->post("language_id") == 1)
							$this->lang->load('mail', 'english' );
						elseif($this->post("language_id") == 2)
						$this->lang->load('mail', 'dutch' );
						elseif($this->post("language_id") == 3)
						$this->lang->load('mail', 'french' );
					}else{
						$this->lang->load('mail', 'dutch' );
					}
					
					$this->load->helper('phpmailer');
					
					$cln_mail_subject = $this->lang->line('mail_welcome_webshop').' '.$company->company_name;
					
					$mail_data['verfication_code'] = $verfication_code;
					$mail_data['first_name'] = $company->first_name;
					$mail_data['last_name'] = $company->last_name;
					$mail_data['company_name'] = $company->company_name;
					$mail_data['firstname_c'] = $client->firstname_c;
					
					$mail_body = $this->load->view('mail_templates/account_verification_to_client', $mail_data, true);
					
					$query = send_email( $client->email_c, $this->config->item('no_reply_email'), $cln_mail_subject, $mail_body, $admin_name, NULL, NULL, 'no_reply', 'client', 'api_resend_verf_code');
					
					if($query){
						$this->response( array('error' => 0, 'message'=> '', 'data' => '' ), 200 );
					}else{
						$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
					}
				}else{
					$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
				}
			}else{
				$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
			}
		}else{
			$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
		}
	} */
	
	/* function verify_account_post(){
		
		$user_id = $this->post('user_id');
		$verify_code = $this->post('verification_code');
		$company_id = $this->post('company_id');
		$language_id = $this->post('language_id');
		
		$user_info = $this->db->get_where('clients', array('id' => $user_id))->result();
		if(!empty($user_info)){
			
			if(!$user_info[0]->verified && $user_info[0]->verify_code == $verify_code){
				$this->db->where('id' ,$user_id);
				if($this->db->update('clients', array('verified' => '1'))){
					//Admin Details
					$admin_info = $this->db->get("admin")->result_array();
					$admin_email = $admin_info['0']['email'];
					$admin_name = $admin_info['0']['admin_name'];
					
					//Company Details
					$this->db->where(array('id'=>$company_id));
					$company = $this->db->get('company')->row();
					$company_email = $company->email;
						
					
					$client = $user_info[0];
					$client->verified = "1";
					$client_email = $client->email_c;
					
					if($language_id){
						if($language_id == 1)
							$this->lang->load('mail', 'english' );
						elseif($language_id == 2)
							$this->lang->load('mail', 'dutch' );
						elseif($language_id == 3)
							$this->lang->load('mail', 'french' );
					}else{
						$this->lang->load('mail', 'dutch' );
					}
					
					$this->load->helper('phpmailer');
					
					$cln_mail_subject = $this->lang->line('mail_welcome_webshop').' '.$company->company_name;
					
					$mail_data['firstname_c'] = $client->firstname_c;
					$mail_data['email_c'] = $client->email_c;
					$mail_data['password_c'] = $client->password_c;
					$mail_data['first_name'] = $company->first_name;
					$mail_data['last_name'] = $company->last_name;
					$mail_data['company_name'] = $company->company_name;
					
					$mail_body = $this->load->view( 'mail_templates/register_success_mail_to_client', $mail_data, true );
					send_email( $client_email, $this->config->item('no_reply_email'), $cln_mail_subject, $mail_body, $admin_name, NULL, NULL, 'no_reply', 'client', 'api_client_verified');
					
					$this->db->where( 'client_numbers.company_id', $this->post('company_id') );
					$this->db->where( 'client_numbers.client_id', $client->id );
					$clientInfo = $this->db->get('client_numbers')->result_array();
					if(!empty($clientInfo)){
						$clientInfo = $clientInfo['0'];
						$client->dcn = $clientInfo['discount_card_number'];
						$client->newsletter = $clientInfo['newsletter'];
						$client->disc_per_client = $clientInfo['disc_per_client'];
						unset($client->password_c);
					}
					$this->response( array('error' => 0, 'message'=> '', 'data' => $client ), 200 );
					
				}else{
					$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
				}
			}else{
				$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
			}
		}else{
			$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
		}
	} */
	
	function delete_order_post(){
		
		$order_id = $this->post('order_id');
		$company_id = $this->post('company_id');
		$user_id = $this->post('user_id');
			
		$this->db->where('id' ,$order_id);
		$this->db->where('clients_id' ,$user_id);
		$this->db->where('company_id' ,$company_id);
		if($this->db->update('orders', array('hidden' => true))){
			$this->response( array('error' => 0, 'message'=> '' ), 200 );
			
		}else{
			$this->response( array('error' => 1, 'message'=> '', 'data' => '' ), 404 );
		}
	}
	
	function save_transaction_info_post(){
		
		$this->load->model('Mpayment');
		
		$client_id = $this->post('client_id');
		$order_id 	= $this->post('order_id');
		$ref 		= $this->post('ref');
		$transition_data = array('order_id'=>$order_id,
				'client_id'=>$client_id,
				'ref'=>$ref);
		// echo "<pre>";print_r($transition_data);die("/");
		$this->Mpayment->save_transition_info($transition_data);
		
		$this->response( array('error' => 0, 'message'=> 'OK' ), 200 );
		
	}
	
	function number2db($value)
	{
		$larr = localeconv();
		$search = array(
				$larr['decimal_point'],
				$larr['mon_decimal_point'],
				$larr['thousands_sep'],
				$larr['mon_thousands_sep'],
				$larr['currency_symbol'],
				$larr['int_curr_symbol']
		);
		$replace = array('.', '.', '', '', '', '');
	
		return str_replace($search, $replace, $value);
	}
	
	/**
	 * Function to handle callback url of cardgate payment module
	 */
	function callback_url_post(){
		 
		$text = date('Y-m-d H:i:s').' => POST: '.json_encode($this->post())."\n";
		$file = fopen(dirname(__FILE__)."/../../../payment-log.txt","a");
		fwrite($file,$text);
		fclose($file);
		
		$this->load->model('Mpayment');
		
		$transaction_id 		= $this->post('transaction_id');
		$site_id 				= $this->post('site_id');
		$is_test				= $this->post('is_test');
		$ref 					= $this->post('ref');
		$status 				= $this->post('status');
		$amount 				= $this->post('amount');
		$customer_email 		= $this->post('customer_email');
		$customer_ip_address 	= $this->post('customer_ip_address');
		$billing_option			= $this->post('billing_option');
		$processor_ref  		= $this->post('processor_ref');
		$hash 					= $this->post('hash');
		$transition_data 		= $this->Mpayment->get_transition_info(trim($ref));
		 
		if(!empty($transition_data)){
			 
			$order_id 				=  $transition_data[0]['order_id'];
			$client_id 				=  $transition_data[0]['client_id'];
			 
			$data = array('transaction_id'=>$transaction_id,
					'order_id' => $order_id,
					'client_id' => $client_id,
					'site_id'=> $site_id ,
					'is_test'=> $is_test ,
					'ref'=> $ref,
					'status'=>$status,
					'amount'=> $amount,
					'customer_email'=>$customer_email,
					'customer_ip_address'=>$customer_ip_address,
					'billing_option'=>$billing_option,
					'processor_ref'=>$processor_ref,
					'hash'=>$hash);
			 
			$text = date('Y-m-d H:i:s').' => data: '.json_encode($data)."\n";
			$file = fopen(dirname(__FILE__)."/../../../payment-log.txt","a");
			fwrite($file,$text);
			fclose($file);
			
			$this->Mpayment->save_transaction($data);
	
			if($status == 200){
				$payment_status = '1';
			}elseif($status == 300 ){
				$payment_status = '0';
			}elseif($status == 0){
				$payment_status = '2';
			}else{
				$payment_status = '0';
			}
			
			$this->Mpayment->update_order_info($transaction_id,$payment_status,$order_id);
			
			// Checking if this order has already been copied to real order tables,
			// This is the case when payment has been canceled via cardgate
			$this->db->select('orders.*,delivery_country.country_name as delivery_country_name');
			$this->db->join('country as delivery_country','orders.delivery_country = delivery_country.id','left');//used in delivery address
			$real_order = $this->db->where( array('temp_id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders' )->row_array();
			
			if(empty($real_order)){
				
				$this->db->select('orders_tmp.*,delivery_country.country_name as delivery_country_name');
				$this->db->join('country as delivery_country','orders_tmp.delivery_country = delivery_country.id','left');//used in delivery address
				$order_data = $this->db->where( array('orders_tmp.id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders_tmp' )->row_array();
				
				// Copying order and order details from temporary table to original table
				if(!empty($order_data)){
					$insert_array = array(
							'clients_id' => $order_data['clients_id'],
							'company_id' => $order_data['company_id'],
							'order_total' => $order_data['order_total'],
							'order_status' => $order_data['order_status'],
							'order_remarks' => $order_data['order_remarks'],
							'order_pickupdate' => $order_data['order_pickupdate'],
							'order_pickupday' => $order_data['order_pickupday'],
							'order_pickuptime' => $order_data['order_pickuptime'],
							'delivery_streer_address' => $order_data['delivery_streer_address'],
							'delivery_area' => $order_data['delivery_area'],
							'delivery_city' => $order_data['delivery_city'],
							'delivery_zip' => $order_data['delivery_zip'],
							'delivery_day' => $order_data['delivery_day'],
							'delivery_hour' => $order_data['delivery_hour'],
							'delivery_minute' => $order_data['delivery_minute'],
							'delivery_date' => $order_data['delivery_date'],
							'delivery_remarks' => $order_data['delivery_remarks'],
							'delivery_cost' => $order_data['delivery_cost'],
							'del_apply_tax' => $order_data['del_apply_tax'],
							'del_tax_amount_added' => $order_data['del_tax_amount_added'],
							'pic_apply_tax' => $order_data['pic_apply_tax'],
							'pic_tax_amount_added' => $order_data['pic_tax_amount_added'],
							'ok_msg' => $order_data['ok_msg'],
							'hold_msg' => $order_data['hold_msg'],
							'completed' => $order_data['completed'],
							'option' => $order_data['option'],
							'created_date' => $order_data['created_date'],
							'subadmin_id' => $order_data['subadmin_id'],
							'shop_url' => $order_data['shop_url'],
							'payment_via_paypal' => $order_data['payment_via_paypal'],
							'payment_status' => $payment_status,
							'transaction_id' => $order_data['transaction_id'],
							'lang_id' => $order_data['lang_id'],
							'printed' => $order_data['printed'],
							'disc_percent' => $order_data['disc_percent'],
							'disc_price' => $order_data['disc_price'],
							'disc_amount' => $order_data['disc_amount'],
							'disc_client' => $order_data['disc_client'],
							'disc_client_amount' => $order_data['disc_client_amount'],
							'labeler_printed' => $order_data['labeler_printed'],
							'from_bo' => $order_data['from_bo'],
							'get_invoice' => $order_data['get_invoice'],
							'name' => $order_data['name'],
							'delivery_country' => $order_data['delivery_country'],
							'phone_reciever' => $order_data['phone_reciever'],
							'ip_address' => $order_data['ip_address'],
							'temp_id' => $order_data['id']
					);
						
					if($this->db->insert('orders', $insert_array)){
						$order_data['id'] = $new_order_id = $this->db->insert_id();
						
						// associating client
						$this->associate_client_with_company( $client_id, $order_data['company_id'] );
						
						$this->db->where( array('orders_id'=>$order_id) );
						$this->db->join( 'products', 'products.id = order_details_tmp.products_id' );
						$order_details_data = $this->db->get( 'order_details_tmp' )->result_array();
						
						if(!empty($order_details_data)){
							foreach ($order_details_data as $details){
								$insert_array = array(
										'orders_id' => $new_order_id,
										'products_id' => $details['products_id'],
										'default_price' => $details['default_price'],
										'discount' => $details['discount'],
										'add_costs' => $details['add_costs'],
										'content_type' => $details['content_type'],
										'sub_total' => $details['sub_total'],
										'quantity' => $details['quantity'],
										'total' => $details['total'],
										'pro_remark' => $details['pro_remark'],
										'image' => ( ($details['image'])?$details['image']:'' ),
										'weight_unit' => $details['weight_unit'],
										'extra_field' => $details['extra_field'],
										'extra_name' => $details['extra_name']
								);
								
								$this->db->insert('order_details', $insert_array);
							}
						}
							
						// Sending Mails
						$order_data['billing_option'] = $billing_option;
						$order_data['payment_status'] = $payment_status;
						if($this->send_order_details_post( $new_order_id, $client_id, $order_data, $order_details_data, $order_data['ip_address']))
							$this->response('OK',200);
						else
							$this->response('Error',400);
					}
				}
			}else{
				
				// Updating payment status
				$this->db->where('id', $real_order['id']);
				$this->db->update('orders', array('payment_status' => $payment_status));
				
				// Have to add code to send a mail to admin and client that payment has been made successfully if payment status is 200
				
				if($payment_status == "1"){
					$real_order['billing_option'] = $billing_option;
					$real_order['payment_status'] = $payment_status;
					
					$this->db->where( array('orders_id'=>$real_order['id']) );
					$this->db->join( 'products', 'products.id = order_details.products_id' );
					$order_details_data = $this->db->get( 'order_details' )->result_array();
					
					if($this->send_order_details_post( $real_order['id'], $client_id, $real_order, $order_details_data, $real_order['ip_address']))
						$this->response('OK',200);
					else
						$this->response('Error',400);
				}else{
					$this->response('Error',200);
				}
				
				
			}
			
			// $this->Mpayment->update_order_info($transaction_id,$payment_status,$order_id);
		}
	}
	
	/**
	 * Function to get merchant info for sending aoi requests
	 */
	function merchant_info_post(){
		$api_verified = $this->verify_api_request_return_get();
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>'Invalid Credentials', 'data' => '' ), 404 );
		}
		else
		{
			$company_id = $this->post('company_id');
			$this->db->select('merchant_id as merchant_name,api_key as merchant_hash');
			$merchant_info = $this->db->get_where('cp_merchant_info', array('company_id' => $company_id))->result();
			if(!empty($merchant_info)){
				$merchant_info = $merchant_info[0];
				$this->response( array('error' => 0, 'message'=> '', 'data' => $merchant_info ), 200 );
			}else{
				$this->response( array('error' => 1, 'message'=>'No merchant info found', 'data' => '' ), 404 );
			}
		}
			
	}
	
	/**
	 * Function: to verify user from cookie values
	 */
	function verify_user_cookie_post(){
		$username = $this->post('obs_user_name');
		$userpass = $this->post('obs_user_pass');
	
		$userInfo = $this->db->get_where('clients',array('email_c' => $username))->result();
	
		if(!empty($userInfo)){
			if(md5($userInfo['0']->password_c) == $userpass){
				$client = $userInfo['0'];
				$this->db->where( 'client_numbers.company_id', $this->post('company_id') );
				$this->db->where( 'client_numbers.client_id', $client->id );
				$clientInfo = $this->db->get('client_numbers')->result_array();
				if(!empty($clientInfo)){
					$clientInfo = $clientInfo['0'];
					$client->dcn = $clientInfo['discount_card_number'];
					$client->newsletter = $clientInfo['newsletter'];
					$client->disc_per_client = $clientInfo['disc_per_client'];
				}
				$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $client ), 200 );
			}
			else
			{
				$this->response( array('error' => 1, 'message'=>_('Invalid username or password !'), 'data' => '' ), 404 );
			}
		}else{
			$this->response( array('error' => 1, 'message'=>_('Sorry ! The user could not be found.'), 'data' => '' ), 404 );
		}
		exit;
	}
	
	function test_send_get(){
		
		/*$order_id = 14860;
		$client_id = 4231;
		
		$order_data = $this->db->where( array('id'=>$order_id,'clients_id'=>$client_id) )->get( 'orders' )->row_array();
		
		$this->db->where( array('orders_id'=>$order_id) );
		$this->db->join( 'products', 'products.id = order_details.products_id' );
		$order_details_data = $this->db->get( 'order_details' )->result_array();
			
		$this->send_order_details_post( $order_id, $client_id, $order_data, $order_details_data, "asdassdA" );*/
	}
	
	/**
	 * This private function is used to fetch all products of given company
	 * @access private
	 */
	public function get_products_test_get(){
		$company_id = $this->get('company_id');
		$is_k_assoc = 1;
		$products = array();
		if($company_id && is_numeric($company_id)){
				
			$this->db->select('products.*,categories.service_type as category_service_type');
				
			$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
			$this->db->where($where);
				
			$this->db->order_by('pro_display','asc');
			$this->db->order_by('id','asc');
				
			$this->db->join('categories','categories.id = products.categories_id','left');
				
			$products = $this->db->get_where('products', array('products.company_id' => $company_id, 'products.status' => 1, 'products.sell_product_option !=' => '' ))->result();
				
			/*if($company_id == 32){
			 $this->response($products,200);
			}*/
				
			if(!empty($products)){
				foreach ($products as $key => $product){
						
					/**
					 * Checking if product's image exist
					 */
					if($product->image != '' && $product->image != null){
						$products[$key]->image = $this->existing_product_image($product->image);
					}else{
						$products[$key]->image = '';
					}
						
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
						$k_ingredients = $this->get_k_ingredients($product->id);
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
						}
	
						$complete = 1;
						if($product->direct_kcp == 1){
							$this->db->where(array('obs_pro_id'=>$product->id,'is_obs_product'=>0));
							$result = $this->db->get('fdd_pro_quantity')->result_array();
							if(empty($result)){
								$complete = 0;
							}
						}
						else{
							$this->db->where(array('obs_pro_id'=>$product->id));
							$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
							if(!empty($result_custom)){
								foreach ($result_custom as $val){
									if($val['is_obs_product'] == 1){
										$complete = 0;
										break;
									}
								}
							}
							else{
								$complete = 0;
							}
						}
	
						if($complete){
							$k_allergence = $this->get_k_allergence($product->id);
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
									}
								}
	
								//if($product->allergence && trim($product->allergence) != '')//product 5340 in cedcoss shop was prefixing 0
								//	$product->allergence = $product->allergence.$allrg_str;
								//else
								$product->allergence = substr($allrg_str,2);
	
							}
						}
					}
				}
			}
		}
	
		
		 $this->response($products,200);
		
	}
	
	function pickup_cat_product_json_post()
	{
		$company_id = $this->post('company_id');
		$pickup_data = file_get_contents(dirname(__FILE__).'/../../../online-bestellen/json/pickup_product_cat_'.$company_id.'.json');
		$data['pickup_data'] = json_decode($pickup_data, true);
		$search_pickup_data = file_get_contents(dirname(__FILE__).'/../../../online-bestellen/json/search_pickup_product_'.$company_id.'.json');
		$data['search_pickup_data'] = json_decode($search_pickup_data, true);
		$delivery_data = file_get_contents(dirname(__FILE__).'/../../../online-bestellen/json/delivery_product_cat_'.$company_id.'.json');
		$data['delivery_data'] = json_decode($delivery_data, true);
		$search_delivery_data = file_get_contents(dirname(__FILE__).'/../../../online-bestellen/json/search_delivery_product_'.$company_id.'.json');
		$data['search_delivery_data'] = json_decode($search_delivery_data, true);
		$general_setting_data = file_get_contents(dirname(__FILE__).'/../../../online-bestellen/json/general_settings_'.$company_id.'.json');
		$data['general_setting_data'] = json_decode($general_setting_data, true);
	
		$this->response( array('error' => 0, 'message'=>_('Success'), 'data' => $data ), 200 );
	}
	
}
