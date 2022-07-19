<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Desk Library
 *
 * This is a for desk json files update
 *
 * @package Libraries
 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
*/
class Desk {

	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->helper('default_setting_helper');
		$this->CI->load->helper('url');
		$this->CI->load->library('session');
		$this->CI->load->database();
	}

	function update_desk_files($infodesk_status = 0,$company_id = 0,$action = 'category_json'){
		if ($this->CI->input->post('action') != '')
		{
			$action = $this->CI->input->post('action');
		}

		if($action){
			/**
			 * Fetching Company Details
			 */
			$company_details = $this->get_companies( 'k_assoc,ac_type_id', array('id' => $company_id, 'approved' => '1', 'obsdesk_status' => '1') );

			if(!empty($company_details)){
				/**
				 * Fetching Desk Settings
				 */
				$desk_settings = $this->get_desk_settings( '', array('company_id' => $company_id) );

				/**
				 * Fetching Categories
				*/
				$categories = $this->get_categories( array('company_id' => $company_id, 'status' => '1'), array('order_display'=>'ASC') );
				$cat_ids = array();
				if(!empty($categories)){
					foreach ($categories as $category){
						$cat_ids[] = $category->id;
					}
				}
				/**
				 * Fetching Subcategories
				*/
				$subcategories = $this->get_subcategory( $company_id );
				$subcat_ids = array();
				if(!empty($subcategories)){
					foreach ($subcategories as $subcategory){
						$subcat_ids[] = $subcategory->id;
					}
				}
				/**
				 * Get language
				 */
				$lang_id = '_dch';
				if( $desk_settings[0]['lang_id'] == '1' ){
					$lang_id = '_dch';
				}elseif( $desk_settings[0]['lang_id'] == '2' ){
					$lang_id = '_dch';
				}else{
					$lang_id = '_fr';
				}

				/**
				 * Fetching Products
				 */
				$products = $this->get_products( $company_id, $company_details[0]->ac_type_id, $cat_ids, $subcat_ids, $lang_id );

				/**
				 * Fetching General Settings
				*/
				//$general_settings = $this->Company->get_general_settings( '', $company_id);

				/**
				 * Fetching Days
				*/
				$days = $this->get_days();

				/**
				 * Fetching Countries
				*/
				$countries = $this->get_countries();

				// Fetching Opening hours
				$general_settings = $this->get_general_settings('calendar_country,hide_availability,comp_default_image',$company_id);

				// Fetching Opening hours
				$order_settings = $this->get_order_settings($company_id,$general_settings[0]->calendar_country);

				// Fetching Opening hours
				$opening_hours = $this->get_opening_hours($company_id);

				// Fetching all allergence words
				$allergence_words = $this->get_admin_defined_allergence();

				$response_arr = array(
						//'company_details' => $company_details,
						'desk_settings' => $desk_settings,
						'products' => $products,
						'categories' => $categories,
						'subcategroies' => $subcategories,
						'countries' => $countries,
						'order_settings' => $order_settings,
						'opening_hours' => $opening_hours,
						'general_settings' => $general_settings,
						'days' => $days,
						'allergence_words' => $allergence_words
				);
				$fp = fopen(dirname(__FILE__).'/../../../infodesk/assets/json/desk_content_'.$company_id.'.json', 'w');
				fwrite($fp, json_encode($response_arr));
				fclose($fp);
			}
		}
	}

	function update_desk_files_via_script($infodesk_status = 0,$company_id = 0){
		/**
		 * Fetching Company Details
		 */
		$company_details = $this->get_companies( 'k_assoc,ac_type_id', array('id' => $company_id, 'approved' => '1', 'obsdesk_status' => '1') );

		if(!empty($company_details)){
			/**
			 * Fetching Desk Settings
			 */
			$desk_settings = $this->get_desk_settings( '', array('company_id' => $company_id) );

			/**
			 * Fetching Categories
			*/
			$categories = $this->get_categories( array('company_id' => $company_id, 'status' => '1'), array('order_display'=>'ASC') );
			$cat_ids = array();
			if(!empty($categories)){
				foreach ($categories as $category){
					$cat_ids[] = $category->id;
				}
			}
			/**
			 * Fetching Subcategories
				*/
			$subcategories = $this->get_subcategory( $company_id );
			$subcat_ids = array();
			if(!empty($subcategories)){
				foreach ($subcategories as $subcategory){
					$subcat_ids[] = $subcategory->id;
				}
			}

			/**
			 * Fetching Products
			 */
			$products = $this->get_products( $company_id, $company_details[0]->ac_type_id, $cat_ids, $subcat_ids );

			/**
			 * Fetching General Settings
			*/
			//$general_settings = $this->Company->get_general_settings( '', $company_id);

			/**
			 * Fetching Days
			*/
			$days = $this->get_days();

			/**
			 * Fetching Countries
			*/
			$countries = $this->get_countries();

			// Fetching Opening hours
			$general_settings = $this->get_general_settings('calendar_country,hide_availability,comp_default_image',$company_id);

			// Fetching Opening hours
			$order_settings = $this->get_order_settings($company_id,$general_settings[0]->calendar_country);

			// Fetching Opening hours
			$opening_hours = $this->get_opening_hours($company_id);

			// Fetching all allergence words
			$allergence_words = $this->get_admin_defined_allergence();

			$response_arr = array(
					//'company_details' => $company_details,
					'desk_settings' => $desk_settings,
					'products' => $products,
					'categories' => $categories,
					'subcategroies' => $subcategories,
					'countries' => $countries,
					'order_settings' => $order_settings,
					'opening_hours' => $opening_hours,
					'general_settings' => $general_settings,
					'days' => $days,
					'allergence_words' => $allergence_words
			);
			$fp = fopen(dirname(__FILE__).'/../../../infodesk/assets/json1/desk_content_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($response_arr));
			fclose($fp);
		}
	}

	function get_companies( $select = '', $where = array(), $order = array(), $limit = '', $offset = '' ){

		if($select != '')
			$this->CI->db->select($select);

		if( !empty($where) ){
			foreach( $where as $key => $val )
				$this->CI->db->where( $key, $val );
		}

		if( !empty($order) ){
			foreach( $order as $ob => $o )
				$this->CI->db->order_by( $ob, $o );
		}

		if( $limit && $offset )
			$this->CI->db->limit( $limit, $offset );
		elseif( $limit )
			$this->CI->db->limit( $limit );

		$companies = $this->CI->db->get('company');
		return( $companies->result() );
	}

	function get_desk_settings( $select = '', $where = array()){
		if($select != '')
			$this->CI->db->select($select);

		if( !empty($where) ){
			foreach( $where as $key => $val )
				$this->CI->db->where( $key, $val );
		}

		$desk_settings = $this->CI->db->get('desk_settings');
		return( $desk_settings->result_array() );
	}

	function get_categories( $where = array(), $order = array('id'=>'ASC'), $limit = '', $offset = '' ){
		if( !empty($where) ){
			foreach( $where as $key => $val )
				$this->CI->db->where( $key, $val );
		}

		if( !empty($order) ){
			foreach( $order as $ob => $o )
				$this->CI->db->order_by( $ob, $o );
		}

		if( $limit && $offset )
			$this->CI->db->limit( $limit, $offset );
		elseif( $limit )
			$this->CI->db->limit( $limit );

		$categories = $this->CI->db->get('categories');
		return( $categories->result() );
	}

	function get_subcategory($company_id = null){
		$subcategories = array();

		if($company_id){
			$this->CI->db->where( '`categories_id` IN (SELECT `categories`.`id` FROM `categories` WHERE `company_id` = '.$company_id.')' );
			$this->CI->db->where( 'status', 1 );

			$this->CI->db->order_by( 'suborder_display', 'ASC' );

			$subcategories = $this->CI->db->get('subcategories')->result();
		}

		return $subcategories;
	}

	function get_products($company_id = null, $is_k_assoc = 0, $cat_ids = array(), $subcat_ids = array() , $lang_id = '_dch' ){

		$products = array();
		if($company_id && is_numeric($company_id) && !empty($cat_ids)){

			$this->CI->db->select('display_fixed');
			$display_fixed_result = $this->CI->db->get_where('general_settings', array('company_id' => $company_id))->result();

			$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
			$this->CI->db->where($where);
			$this->CI->db->where(array('company_id' => $company_id, 'status' => 1));
			$this->CI->db->where_in('categories_id',$cat_ids);
			$subcat_ids[] = '-1';
			$this->CI->db->where_in('subcategories_id',$subcat_ids);
			$this->CI->db->order_by('pro_display','asc');
			$this->CI->db->order_by('id','asc');
			$products = $this->CI->db->get('products')->result();

			if(!empty($products)){
				foreach ($products as $key => $product){
					if(($is_k_assoc == 4) || ($is_k_assoc == 5) || ($is_k_assoc == 6)){
						$complete = 1;
						if($product->direct_kcp == 1){
							$this->CI->db->where(array('obs_pro_id'=>$product->id,'is_obs_product'=>0));
							$result = $this->CI->db->get('fdd_pro_quantity')->result_array();
							if(empty($result)){
								$complete = 0;
							}
						}
						else{
							$this->CI->db->where(array('obs_pro_id'=>$product->id));
							$result_custom = $this->CI->db->get('fdd_pro_quantity')->result_array();
							if(!empty($result_custom)){
								$this->CI->fdb = $this->CI->load->database('fdb', true);
								foreach ($result_custom as $val){
									if($val['is_obs_product'] == 1){
										$complete = 0;
										break;
									}
									else {
										
										$this->CI->fdb->where( 'p_id', $val[ 'fdd_pro_id' ] );
										$this->CI->fdb->where( 'approval_status', 1 );
										$count = $this->CI->fdb->count_all_results( 'products' );
										if( $count == 0 ) {
											$complete = 0;
											break;
										}
										
									}
								}
								$this->CI->fdb->close();
							}
							else{
								$complete = 0;
							}
						}
						if(!$complete){
							if($display_fixed_result[0]->display_fixed){
								unset($products[$key]);
								continue;
							}
							else{
								$products[$key]->product_fixed = 'No';
							}
						}
						else{
							$products[$key]->product_fixed = 'Yes';
						}
					}
					else{
						$products[$key]->product_fixed = 'Yes';
					}
					/**
					 * Checking if product's image exist
					 */
					/*if($product->image != ''){
					 $products[$key]->image = $this->existing_product_image($product->image);
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

					if($is_k_assoc == 4 || $is_k_assoc == 5 || $is_k_assoc == 6){
						/*$products[$key]->k_ingredients = $this->get_k_ingredients($product->id);
							$products[$key]->k_traces = $this->get_k_traces($product->id);
						$products[$key]->k_allergence = $this->get_k_allergence($product->id);*/
						/*$k_ingredients = $this->get_k_ingredients($product->id);
						if(!empty($k_ingredients)){
							$ing_str = '';
							$add_comma = true;
							foreach ($k_ingredients as $k_ingredient){
								if($k_ingredient->ki_name == '(' || $k_ingredient->ki_name == ')'){
									$ing_str .= ' '.$k_ingredient->ki_name;
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
							//if($product->ingredients != '')
							//	$product->ingredients = $product->ingredients.$ing_str;
							//else
								$product->ingredients = substr($ing_str,2);
						}*/

//						$k_traces = $this->get_k_traces($product->id);
// 						if(!empty($k_traces)){
// 							$tra_str = '';
// 							$add_comma = true;
// 							foreach ($k_traces as $k_trace){
// 								if($k_trace->kt_name == '(' || $k_trace->kt_name == ')'){
// 									$tra_str .= ' '.$k_trace->kt_name;
// 									if($k_trace->kt_name == '(')
// 										$add_comma = false;
// 								}else{
// 									if($add_comma)
// 										$tra_str .= ', '.$k_trace->prefix.' '.$k_trace->kt_name;
// 									else
// 										$tra_str .= $k_trace->prefix.' '.$k_trace->kt_name;
// 									$add_comma = true;
// 								}
// 							}
// 							//if($product->traces_of != '')
// 							//	$product->traces_of = $product->traces_of.$tra_str;
// 							//else
// 								$product->traces_of = substr($tra_str,2);
// 							/*foreach ($k_traces as $k_trace){
// 								$tra_str .= ', '.$k_trace->prefix.' '.$k_trace->kt_name;
// 							}
// 							if($product->traces_of != '')
// 								$product->traces_of = $product->traces_of.$tra_str;
// 							else
// 								$product->traces_of = substr($tra_str,2);*/
// 						}

						$k_allergence = $this->get_k_allergence($product->id , $lang_id );
						$k_sub_allergence = $this->get_k_sub_allergence($product->id , $lang_id);
						if(!empty($k_allergence)){
							$allrg_str = '';
							$add_comma = true;
							foreach ($k_allergence as $k_allerg){
								//$allrg_str .= ', '.$k_allerg->prefix.' '.$k_allerg->ka_name;
								if($k_allerg->ka_name == '(' || $k_allerg->ka_name == ')'){
									$allrg_str .= ' '.$k_allerg->ka_name;
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
							//if($product->allergence != '')
							//	$product->allergence = $product->allergence.$allrg_str;
							//else
								$product->allergence = substr($allrg_str,2);
						}
						else{
							$product->allergence = '';
						}
					}
				}
			}
		}
		return array_values($products);
	}

	function get_days(){
		$days = $this->CI->db->get('days')->result();
		return $days;
	}

	function get_countries( $where = array()){
		if( !empty($where) ){
			foreach( $where as $key => $val )
				$this->CI->db->where( $key, $val );
		}

		$this->CI->db->where('(id = 21 OR id = 150)');

		$countries = $this->CI->db->get('country');
		return( $countries->result() );
	}

	function get_general_settings( $select = '', $company_id = null){
		$response = array();
		if($company_id){
			if($select != ''){
				$this->CI->db->select($select);
			}
			$response = $this->CI->db->get_where('general_settings',array('company_id' => $company_id))->result();
		}
		return $response;
	}

	/**
	 * This private function is used to fetch Order Settings
	 * @param Integer $company_id Company ID
	 * @return array $order_settings Array of Order Settings
	 */
	function get_order_settings($company_id = null, $calendar_country = 'calendar_belgium'){

		$order_settings = array();

		if($company_id){
			$this->CI->db->where( 'company_id', $company_id);
			$order_settings = $this->CI->db->get('order_settings')->result();
			if(!empty($order_settings)){
				$order_settings[0]->holiday_dates = $this->get_company_holiday_dates($company_id, $calendar_country);
				$order_settings[0]->shop_close_dates = $this->get_company_close_dates($company_id);
			}
		}

		return $order_settings;
	}

	/**
	 * This function is used to fetch company holidays (including pre-assigned holidays by MCP)
	 */
	function get_company_holiday_dates($company_id = 0, $calendar_country = 'calendar_belgium'){
		$holidays = array();
		if($company_id){
			$this->CI->db->select('day,month,year');
			$this->CI->db->where('company_id', $company_id);
			$this->CI->db->where("( `calendar` = 'own' OR `calendar` = '".$calendar_country."' )");
			$holidays_dates = $this->CI->db->get('company_holidays')->result();
			if(!empty($holidays_dates)){
				foreach($holidays_dates as $holiday_date){
					$holidays[] = ( (strlen($holiday_date->day) == 1)?'0'.$holiday_date->day:$holiday_date->day ).'/'.( (strlen($holiday_date->month) == 1)?'0'.$holiday_date->month:$holiday_date->month ).'/'.$holiday_date->year;
				}
			}
		}
		return implode(',',$holidays);
	}

	/**
	 * This function is used to fetch company closing dates
	 */
	function get_company_close_dates($company_id){
		$holidays = array();
		if($company_id){
			$this->CI->db->select('day,month,year');
			$this->CI->db->where('company_id', $company_id);
			$holidays_dates = $this->CI->db->get('company_closedays')->result();
			if(!empty($holidays_dates)){
				foreach($holidays_dates as $holiday_date){
					$holidays[] = ( (strlen($holiday_date->day) == 1)?'0'.$holiday_date->day:$holiday_date->day ).'/'.( (strlen($holiday_date->month) == 1)?'0'.$holiday_date->month:$holiday_date->month ).'/'.$holiday_date->year;
				}
			}
		}
		return implode(',',$holidays);
	}

	/**
	 * This private function is used to fetch Opening Hours
	 * @param Integer $company_id Company ID
	 * @return array $opening_hours Array of Opening hours  (pickup and delivery)
	 */
	function get_opening_hours($company_id = null){
		$opening_hours = array();

		if($company_id && is_numeric($company_id)){

			$this->CI->db->join('days','pickup_delivery_timings.day_id = days.id');
			$this->CI->db->where('pickup_delivery_timings.company_id', $company_id);
			$this->CI->db->order_by('pickup_delivery_timings.day_id','ASC');
			$opening_hours = $this->CI->db->get('pickup_delivery_timings')->result();
		}

		return $opening_hours;
	}

	/**
	 * This private function is used to fetch all expected allergence words defined in admin settings
	 * @access private
	 * @return array $allergence_words Array of allergence words
	 */
	function get_admin_defined_allergence(){
		$results = array();
		$allergence_words = array();

		$this->CI->db->select('allergens_word');
		$results = $this->CI->db->get('allergens_words')->result();

		if(!empty($results))
		foreach($results as $row){
			$allergence_words[] = $row->allergens_word;
		}
		return $allergence_words;
	}

	private function get_product_groups( $product_id = null){
		$grps_arr = array();
		if($product_id && is_numeric($product_id)){
			$this->CI->db->where( 'groups_products.products_id', $product_id );
			$this->CI->db->join('groups', 'groups.id = groups_products.groups_id');
			$this->CI->db->order_by( 'groups_products.groups_id', 'ASC' );
			$this->CI->db->order_by( 'groups_products.type', 'ASC' );

			$product_grps = $this->CI->db->get('groups_products')->result();

			if( !empty($product_grps) ){
				$hold_grp_id = '';
				$index = -1;

				foreach( $product_grps as $grp ){
					if( $hold_grp_id != $grp->groups_id ){
						$hold_grp_id = $grp->groups_id;
						$index = $index+1;

						$grps_arr[$index] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );

					}
					elseif( $hold_grp_id == $grp->groups_id ){
						$grps_arr[$index]['attributes_arr'][] = array( $grp->attribute_name, $grp->attribute_value );
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
	private function get_product_multi_discounts($product_id = null, $discount_type = null){
		$products_discount = array();

		if($product_id && is_numeric($product_id)){

			if( ($discount_type == 0 || $discount_type == 1 || $discount_type == 2) && $discount_type != null ){
				$this->CI->db->where( 'type', $discount_type );
			}

			$this->CI->db->order_by('quantity','ASC');
			$this->CI->db->where( 'products_id', $product_id );
			$products_discount = $this->CI->db->get('products_discount')->result();
		}

		return $products_discount;
	}

	/**
	 * This private function is used to fetch Keurslager Allergence related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which allergence have to be fetch
	 * @return array $traces It is the array if allergence associated with the given product
	 */
	function get_k_allergence($product_id = 0, $lang_id ){
		$allergence = array();
		if($product_id){
			$this->CI->db->select('prefix,ka_id, all_name'.$lang_id.' as ka_name');
			$this->CI->db->join('allergence', 'allergence.all_id = products_allergence.ka_id');
			$this->CI->db->order_by('display_order', 'ASC');
			$this->CI->db->group_by('ka_id');
			$allergence = $this->CI->db->get_where('products_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	/**
	 * This private function is used to fetch Keurslager Sub Allergence related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which sub allergence have to be fetched
	 * @return array $traces It is the array if sub allergence associated with the given product
	 */
	function get_k_sub_allergence($product_id = 0 , $lang_id ){
		$allergence = array();
		if($product_id){
			$this->CI->db->select('parent_ka_id, all_name'.$lang_id.' as sub_ka_name');
			$this->CI->db->join('sub_allergence', 'sub_allergence.all_id = product_sub_allergence.sub_ka_id');
			$this->CI->db->order_by('display_order', 'ASC');
			$this->CI->db->group_by('sub_ka_id');
			$allergence = $this->CI->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}
}