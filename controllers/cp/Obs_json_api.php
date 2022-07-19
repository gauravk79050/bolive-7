<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*require APPPATH.'/libraries/REST_Controller.php';*/

class Obs_json_api extends CI_Controller
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
			return false;		
		}	
		else
		{						
			return true;
		}
	}
	
	function company($company_id = null , $attribute = null , $attribute_id = null){
		
		$json_array = array();
		if($company_id && is_numeric($company_id)){
			$found = $this->db->get_where('company' , array('id' => $company_id))->result_array();
			if(!empty($found)){
				$parentCompanyId = '';
				if($found['0']['role'] == "sub"){
					$parentCompanyId = $found['0']['parent_id'];
				}
				$json_array[_('company_id')] = $company_id;
				$json_array[_('company_name')] = $found['0']['company_name'];
				if($attribute && ($attribute == 'products' || $attribute == 'orders' || $attribute == 'categories' || $attribute == 'subcategories' || $attribute == 'clients')){
					$json_array[_('attribute_name')] = $attribute;
					if($attribute == 'orders'){
						if($attribute_id && is_numeric($attribute_id)){
							$json_array[_('order_detail_id')] = $attribute_id;
							$this->db->select('order_details.*,products.id as proid,products.company_id,products.categories_id,products.subcategories_id,products.pro_art_num,products.proname,products.price_per_unit,products.price_per_person,products.price_weight,products.type,products.discount,categories.name as cat_name');
							$this->db->join('products','order_details.products_id=products.id');
							$this->db->join('categories','categories.id=products.categories_id');
							$this->db->where(array('order_details.orders_id'=>$attribute_id));
							$order_details_datas = $this->db->get('order_details')->result_array();
							
							if(!empty($order_details_datas)){
								foreach($order_details_datas as $key=>$order_details_data){
									$json_array[_('order_detail')][$key][_('Antal')] = $order_details_data['quantity'];	

									if($order_details_data['pro_art_num'] != ''){
										$art_num = $order_details_data['pro_art_num'];
						
										$length =  strlen($art_num);
										if($length < 8){
											$required_zeros = (int)(8-$length);
											for($j = 0; $j < $required_zeros; $j++){
												$art_num = '0'.$art_num;
											}
										}
										$json_array[_('order_detail')][$key][_('Ref.Art')] = $art_num;
									}else{
										$json_array[_('order_detail')][$key][_('Ref.Art')] = "--";
									}

									$json_array[_('order_detail')][$key][_('Benaming')] = $order_details_data['proname'];
						
									$json_array[_('order_detail')][$key][_('Groep')] = $order_details_data['cat_name'];
										
									$per_price = round($order_details_data['sub_total'],2);
									$per_price = str_replace('.',',',$per_price);
									$json_array[_('order_detail')][$key][_('Prijs')] = $per_price;
						
									$json_array[_('order_detail')][$key][_('Opt.Prijs')] = "--";
										
									
										
									if($order_details_data['pro_remark'] != ''){
										$json_array[_('order_detail')][$key][_('Memo')] = $order_details_data['pro_remark'];
									}else{
										$json_array[_('order_detail')][$key][_('Memo')] = $order_details_data['pro_remark'];
									}
						
									$json_array[_('order_detail')][$key][_('Art.Id')] = $order_details_data['products_id'];
						
									$json_array[_('order_detail')][$key][_('Order.Id')] = $order_details_data['orders_id'];
						
									$json_array[_('order_detail')][$key][_('Id')] = $order_details_data['id'];
						
									$json_array[_('order_detail')][$key][_('Id')] = "--";
						
									$json_array[_('order_detail')][$key][_('PP')] = "--";
						
								}
							}else{
								$json_array[_('order_detail')][_('error')] = _("No data for this order ID");
							}
						}else{
						
							$this->db->select('orders.* , client_numbers.client_number ,clients.firstname_c , clients.lastname_c ');
							$this->db->where( 'orders.company_id' , $company_id );
							$this->db->join( 'clients', 'clients.id = orders.clients_id' );
							$this->db->join( 'client_numbers', 'clients.id = client_numbers.client_id', 'left' );
							$order_details_data = $this->db->get( 'orders' )->result_array();
							//print_r($order_details_data); die();
						
							foreach($order_details_data as $key => $order_detail){
								
								$json_array[_('order')][$key][_('Nummer')] = $order_detail['id'];
						
								$json_array[_('order')][$key][_('Datum')] = $order_detail['created_date'];
								
								if($order_detail['order_pickuptime']){
									$pickup_time = explode(':' , $order_detail['order_pickuptime']);
									$pickup_hour = $pickup_time['0'];
									$pickup_minute = $pickup_time['1'];
									if(strlen($pickup_minute) == 1){
										$pickup_minute = '0'.$pickup_minute;
									}
						
									$final_time = $pickup_hour.':'.$pickup_minute;
						
									$json_array[_('order')][$key][_('Uur')] = $final_time;
								}else{
									$json_array[_('order')][$key][_('Uur')] = "--";
								}
						
								if($order_detail['client_number'] != ''){
									$client_num = $order_detail['client_number'];
						
									$length =  strlen($client_num);
									if($length < 8){
										$required_zeros = (int)(8-$length);
										for($j = 0; $j < $required_zeros; $j++){
											$client_num = '0'.$client_num;
										}
									}
									$json_array[_('order')][$key][_('Klantnr')] = $client_num;
								}else{
									$json_array[_('order')][$key][_('Klantnr')] = "";
								}
						
								$json_array[_('order')][$key][_('Naam')] = $order_detail['lastname_c'].' '.$order_detail['firstname_c'];
								
								if($order_detail['order_pickupdate'] != "0000-00-00"){
									$json_array[_('order')][$key][_('Afhalen.datum')] = $order_detail['order_pickupdate'];
								}elseif($order_detail['delivery_date'] != "0000-00-00"){
									$json_array[_('order')][$key][_('Levering.datum')] = $order_detail['delivery_date'];
								}
								
								$json_array[_('order')][$key][_('Status')] = $order_detail['order_status'];
								
								$json_array[_('order')][$key][_('Best.Date')] = date("d/m/Y", strtotime($order_detail['created_date']));
						
								$json_array[_('order')][$key][_('Betaling')] = ($order_detail['payment_via_paypal'])?_('Paypal'):_('Cash');
							}
						}
					}elseif($attribute == 'categories'){
						if($attribute_id && is_numeric($attribute_id)){
							$json_array[_('category_id')] = $attribute_id;
							if($parentCompanyId != '')
								$this->db->where( 'categories.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'categories.company_id' , $company_id );
							$this->db->where( 'categories.id' , $attribute_id );
							$category_details_data = $this->db->get( 'categories' )->result_array();
							//$order_details_data = $this->db->get( 'orders' )->row_array();
							//print_r($order_details_datas); die();
							if(!empty($category_details_data)){
								foreach($category_details_data as $key => $category_detail){
									$json_array[_('category_details')][$key][_('Nummer')] = $category_detail['id'];

									$json_array[_('category_details')][$key][_('Naam')] = $category_detail['name'];
										
									$json_array[_('category_details')][$key][_('Status')] = $category_detail['status'];
										
									$json_array[_('category_details')][$key][_('Description')] = $category_detail['description'];
								}
							}else{
								$json_array[_('category_details')][_('error')] = "No data for this Category ID";
							}
						}else{
						
							if($parentCompanyId != '')
								$this->db->where( 'categories.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'categories.company_id' , $company_id );
							$category_details_data = $this->db->get( 'categories' )->result_array();
							//print_r($order_details_data); die();
						
							foreach($category_details_data as $key => $category_data){
								
								$json_array[_('category')][$key][_('Nummer')] = $category_data['id'];
						
								$json_array[_('category')][$key][_('Naam')] = $category_data['name'];
										
								$json_array[_('category')][$key][_('Status')] = $category_data['status'];
									
								$json_array[_('category')][$key][_('Description')] = $category_data['description'];
									
							}
						}
					}elseif($attribute == 'products'){
						if($attribute_id && is_numeric($attribute_id)){
							$json_array[_('product_id')] = $attribute_id;
							$this->db->select('products.* , categories.name as cat_name');
							if($parentCompanyId != '')
								$this->db->where( 'products.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'products.company_id' , $company_id );
							$this->db->where( 'products.id' , $attribute_id );
							$this->db->join( 'categories' , 'products.categories_id = categories.id' );
							$product_details_data = $this->db->get( 'products' )->result_array();
							//$order_details_data = $this->db->get( 'orders' )->row_array();
							//print_r($order_details_datas); die();
							if(!empty($product_details_data)){
								foreach($product_details_data as $k => $product_details){
									$json_array[_('product_details')][$k][_('Nummer')] = $product_details['id'];
						
									$json_array[_('product_details')][$k][_('Naam')] = $product_details['proname'];
									
									$json_array[_('product_details')][$k][_('Groep')] = $product_details['cat_name'];
									
									$json_array[_('product_details')][$k][_('Description')] = $product_details['prodescription'];
									
									$json_array[_('product_details')][$k][_('Selling.Option')] = $product_details['sell_product_option'];
						
									$price = '';
									if($product_details['sell_product_option'] == 'per_unit'){
										$price = round($product_details['price_per_unit'],2);
									}elseif($product_details['sell_product_option'] == 'per_person'){
										$price = round($product_details['price_per_person'],2).'/pp';
									}elseif($product_details['sell_product_option'] == 'weight_wise'){
										$price = round($product_details['price_weight'],2).'/Kg';
									}elseif($product_details['sell_product_option'] == 'client_may_choose'){
										$price = round($product_details['price_per_unit'],2).'/unit-';
										$price .= round($product_details['price_weight'],2).'/Kg';
									}
									
									$price = str_replace('.',',',$price);
									$json_array[_('product_details')][$k][_('Prijs')] = $price;
									
									$discount = '';
									if($product_details['sell_product_option'] == 'per_unit'){
										$discount = round($product_details['discount'],2);
									}elseif($product_details['sell_product_option'] == 'per_person'){
										$discount = round($product_details['discount_person'],2);
									}elseif($product_details['sell_product_option'] == 'weight_wise'){
										$discount = round($product_details['discount_wt'],2);
									}elseif($product_details['sell_product_option'] == 'client_may_choose'){
										$discount = round($product_details['discount'],2).'-';
										$discount .= round($product_details['discount_wt'],2);
									}
									if($discount != ''){
										$discount = str_replace('.',',',$discount);
									}else{
										$discount = '-';
									}
									
									$json_array[_('product_details')][$k][_('Discount')] = $discount;
									
									$multi_discount = array();
									$is_multi = false;
									if($product_details['sell_product_option'] == 'per_unit'){
										if($product_details['discount'] == 'multi' ){
											$is_multi = true;
										}
									}elseif($product_details['sell_product_option'] == 'per_person'){
										if($product_details['discount_person'] == 'multi' ){
											$is_multi = true;
										}
									}elseif($product_details['sell_product_option'] == 'weight_wise'){
										if($product_details['discount_wt'] == 'multi' ){
											$is_multi = true;
										}
									}elseif($product_details['sell_product_option'] == 'client_may_choose'){
										if($product_details['discount'] == 'multi' || $product_details['discount_wt'] == 'multi' ){
											$is_multi = true;
										}
									}
									
									if($is_multi){
										$discount_details = $this->db->get_where('products_discount' , array('products_id'  => $product_details['id']))->result_array();
										if(!empty($discount_details)){
											foreach($discount_details as $k_d=>$discount_detail){
												$multi_discount[$k_d]['Antal'] = $discount_detail['quantity'];
													
												$multi_discount[$k_d]['Discount'] = str_replace('.',',',round($discount_detail['discount_per_qty'],2));
													
												$multi_discount[$k_d]['Prijs'] = str_replace('.',',',round($discount_detail['price_per_qty'],2));
													
												$type = '-';
												if($discount_detail['type'] == 1){
													$type = 'weight wise';
												}elseif($discount_detail['type'] == 2){
													$type = 'per personen';
												}else{
													$type = 'per unit';
												}
												$multi_discount[$k_d]['Type'] = $discount_detail['$type'];
											}
										}
									}
									
									$json_array[_('product_details')][$k][_('Multi.Discount')] = $multi_discount;
									
									$json_array[_('product_details')][$k][_('Status')] = $product_details['status'];
									
									$json_array[_('product_details')][$k][_('Availability')] = $product_details['availability'];
									
								}
							}else{
								$json_array[_('category_details')][_('error')] = "No product found for this id";
							}
						}else{
						
							$this->db->select('products.* , categories.name as cat_name');
							if($parentCompanyId != '')
								$this->db->where( 'products.company_id' , $parentCompanyId );	
							else 
								$this->db->where( 'products.company_id' , $company_id );
							$this->db->join( 'categories' , 'products.categories_id = categories.id' );
							$product_details_data = $this->db->get( 'products' )->result_array();
							//$order_details_data = $this->db->get( 'orders' )->row_array();
							//print_r($product_details_data); die();
							if(!empty($product_details_data)){
								foreach($product_details_data as $k => $product_details){
									$json_array[_('product_details')][$k][_('Nummer')] = $product_details['id'];
						
									$json_array[_('product_details')][$k][_('Naam')] = $product_details['proname'];
									
									$json_array[_('product_details')][$k][_('Groep')] = $product_details['cat_name'];
									
									$json_array[_('product_details')][$k][_('Description')] = $product_details['prodescription'];
									
									$json_array[_('product_details')][$k][_('Selling.Option')] = $product_details['sell_product_option'];
						
									$price = '';
									if($product_details['sell_product_option'] == 'per_unit'){
										$price = round($product_details['price_per_unit'],2);
									}elseif($product_details['sell_product_option'] == 'per_person'){
										$price = round($product_details['price_per_person'],2).'/pp';
									}elseif($product_details['sell_product_option'] == 'weight_wise'){
										$price = round($product_details['price_weight'],2).'/Kg';
									}elseif($product_details['sell_product_option'] == 'client_may_choose'){
										$price = round($product_details['price_per_unit'],2).'/unit-';
										$price .= round($product_details['price_weight'],2).'/Kg';
									}
									
									$price = str_replace('.',',',$price);
									$json_array[_('product_details')][$k][_('Prijs')] = $price;
									
									$discount = '';
									if($product_details['sell_product_option'] == 'per_unit'){
										$discount = round($product_details['discount'],2);
									}elseif($product_details['sell_product_option'] == 'per_person'){
										$discount = round($product_details['discount_person'],2);
									}elseif($product_details['sell_product_option'] == 'weight_wise'){
										$discount = round($product_details['discount_wt'],2);
									}elseif($product_details['sell_product_option'] == 'client_may_choose'){
										$discount = round($product_details['discount'],2).'-';
										$discount .= round($product_details['discount_wt'],2);
									}
									if($discount != ''){
										$discount = str_replace('.',',',$discount);
									}else{
										$discount = '-';
									}
									
									$json_array[_('product_details')][$k][_('Discount')] = $discount;
									
									$multi_discount = array();
									$is_multi = false;
									if($product_details['sell_product_option'] == 'per_unit'){
										if($product_details['discount'] == 'multi' ){
											$is_multi = true;
										}
									}elseif($product_details['sell_product_option'] == 'per_person'){
										if($product_details['discount_person'] == 'multi' ){
											$is_multi = true;
										}
									}elseif($product_details['sell_product_option'] == 'weight_wise'){
										if($product_details['discount_wt'] == 'multi' ){
											$is_multi = true;
										}
									}elseif($product_details['sell_product_option'] == 'client_may_choose'){
										if($product_details['discount'] == 'multi' || $product_details['discount_wt'] == 'multi' ){
											$is_multi = true;
										}
									}
									
									if($is_multi){
										$discount_details = $this->db->get_where('products_discount' , array('products_id'  => $product_details['id']))->result_array();
										if(!empty($discount_details)){
											foreach($discount_details as $k_d=>$discount_detail){
												
												$multi_discount[$k_d]['Antal'] = $discount_detail['quantity'];
													
												$multi_discount[$k_d]['Discount'] = str_replace('.',',',round($discount_detail['discount_per_qty'],2));
													
												$multi_discount[$k_d]['Prijs'] = str_replace('.',',',round($discount_detail['price_per_qty'],2));
													
												$type = '-';
												if($discount_detail['type'] == 1){
													$type = 'weight wise';
												}elseif($discount_detail['type'] == 2){
													$type = 'per personen';
												}else{
													$type = 'per unit';
												}
												$multi_discount[$k_d]['Type'] = $type;
											}
										}
									}
									
									$json_array[_('product_details')][$k][_('Multi.Discount')] = $multi_discount;
									
									$json_array[_('product_details')][$k][_('Status')] = $product_details['status'];
									
									$json_array[_('product_details')][$k][_('Availability')] = $product_details['availability'];
								}
							}
						}
					}elseif($attribute == 'subcategories'){
						if($attribute_id && is_numeric($attribute_id)){
							$json_array[_('subcategory_id')] = $attribute_id;
							
							$this->db->select('subcategories.*');
							$this->db->join('categories','categories.id = subcategories.categories_id');
							if($parentCompanyId != '')
								$this->db->where( 'categories.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'categories.company_id' , $company_id );
							$this->db->where( 'subcategories.id' , $attribute_id );
							$subcategory_details_data = $this->db->get( 'subcategories' )->result_array();
							
							if(!empty($subcategory_details_data)){
								foreach($subcategory_details_data as $key => $subcategory_detail){
									$json_array[_('subcategory_details')][$key][_('Nummer')] = $subcategory_detail['id'];

									$json_array[_('subcategory_details')][$key][_('category_id')] = $subcategory_detail['categories_id'];
									
									$json_array[_('subcategory_details')][$key][_('Naam')] = $subcategory_detail['subname'];
										
									$json_array[_('subcategory_details')][$key][_('Status')] = $subcategory_detail['status'];
										
									$json_array[_('subcategory_details')][$key][_('Description')] = $subcategory_detail['subdescription'];
								}
							}else{
								$json_array[_('subcategory_details')][_('error')] = "No data for this Subcategory ID";
							}
						}else{
						
							$this->db->select('subcategories.*');
							$this->db->join('categories','categories.id = subcategories.categories_id');
							if($parentCompanyId != '')
								$this->db->where( 'categories.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'categories.company_id' , $company_id );
							
							$subcategory_details_data = $this->db->get( 'subcategories' )->result_array();
							
							if(!empty($subcategory_details_data)){
								foreach($subcategory_details_data as $key => $subcategory_detail){
									$json_array[_('subcategory_details')][$key][_('Nummer')] = $subcategory_detail['id'];

									$json_array[_('subcategory_details')][$key][_('category_id')] = $subcategory_detail['categories_id'];
									
									$json_array[_('subcategory_details')][$key][_('Naam')] = $subcategory_detail['subname'];
										
									$json_array[_('subcategory_details')][$key][_('Status')] = $subcategory_detail['status'];
										
									$json_array[_('subcategory_details')][$key][_('Description')] = $subcategory_detail['subdescription'];
								}
							}else{
								$json_array[_('subcategory_details')][_('error')] = "No data found";
							}
						}
					}elseif($attribute == 'clients'){
						if($attribute_id && is_numeric($attribute_id)){
							$json_array[_('client_id')] = $attribute_id;
							
							$this->db->select('clients.*,client_numbers.client_number,client_numbers.discount_card_number,client_numbers.newsletter,client_numbers.disc_per_client');
							$this->db->join('client_numbers','client_numbers.client_id = clients.id');
							
							if($parentCompanyId != '')
								$this->db->where( 'client_numbers.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'client_numbers.company_id' , $company_id );
							$this->db->where( 'clients.id' , $attribute_id );
							
							$client_details_data = $this->db->get( 'clients' )->result_array();
							
							if(!empty($client_details_data)){
								foreach($client_details_data as $key => $client_data){
									
									$c_num = '--';
									if($client_data['client_number'] != ''){
										$c_num = $client_data['client_number'];
										$length = strlen($c_num);
										if($length < 8){
											for($i = 0 ; $i < (8-$length) ; $i++){
												$c_num = '0'.$c_num;
											}
										}
									}
									
									$json_array[_('client_detail')][$key][_('Nummer')] = $c_num;

									$json_array[_('client_detail')][$key][_('Naam')] = $client_data['firstname_c'].' '.$client_data['lastname_c'];
										
									$c_addr = $client_data['housenumber_c'].' '.$client_data['address_c'].' '.$client_data['city_c'].' '.$client_data['postcode_c'];
									if($client_data['country_id'] == 21){
										$c_addr .= ' BELGIE';
									}elseif($client_data['country_id'] == 150){
										$c_addr .= ' NEDERLAND';
									}
									$json_array[_('client_detail')][$key][_('Address')] = $c_addr;
										
									$json_array[_('client_detail')][$key][_('Phone')] = $client_data['phone_c'];
									
									$json_array[_('client_detail')][$key][_('email')] = $client_data['email_c'];
									
									$json_array[_('client_detail')][$key][_('Newsletter')] = $client_data['newsletter'];
									
									$json_array[_('client_detail')][$key][_('Loyality-Discount')] = $client_data['disc_per_client'];
								}
							}else{
								$json_array[_('category_details')][_('error')] = "No data for this Client ID";
							}
						}else{
						
							$this->db->select('clients.*,client_numbers.client_number,client_numbers.discount_card_number,client_numbers.newsletter,client_numbers.disc_per_client');
							$this->db->join('client_numbers','client_numbers.client_id = clients.id');
							
							if($parentCompanyId != '')
								$this->db->where( 'client_numbers.company_id' , $parentCompanyId );
							else 
								$this->db->where( 'client_numbers.company_id' , $company_id );
							
							$client_details_data = $this->db->get( 'clients' )->result_array();
						
							foreach($client_details_data as $key => $client_data){
									
								$c_num = '--';
								if($client_data['client_number'] != ''){
									$c_num = $client_data['client_number'];
									$length = strlen($c_num);
									if($length < 8){
										for($i = 0 ; $i < (8-$length) ; $i++){
											$c_num = '0'.$c_num;
										}
									}
								}
								
								$json_array[_('client_detail')][$key][_('Nummer')] = $c_num;

								$json_array[_('client_detail')][$key][_('Naam')] = $client_data['firstname_c'].' '.$client_data['lastname_c'];
									
								$c_addr = $client_data['housenumber_c'].' '.$client_data['address_c'].' '.$client_data['city_c'].' '.$client_data['postcode_c'];
								if($client_data['country_id'] == 21){
									$c_addr .= ' BELGIE';
								}elseif($client_data['country_id'] == 150){
									$c_addr .= ' NEDERLAND';
								}
								$json_array[_('client_detail')][$key][_('Address')] = $c_addr;
									
								$json_array[_('client_detail')][$key][_('Phone')] = $client_data['phone_c'];
								
								$json_array[_('client_detail')][$key][_('email')] = $client_data['email_c'];
								
								$json_array[_('client_detail')][$key][_('Newsletter')] = $client_data['newsletter'];
								
								$json_array[_('client_detail')][$key][_('Loyality-Discount')] = $client_data['disc_per_client'];
							}
						}
					}
				}else{
					$json_array = array(_("error")=>_("Please specify any attribute name to find."));
				}
			}else{
				$json_array = array(_("error")=>_("No Company Found."));
			}
		}else{
			$json_array = array(_("error")=>_("Company Id Not Found"));			
		}
		
		$fileName = _('Company').'_'.$company_id;
		if($attribute && $attribute_id){
			$fileName .= '_'.$attribute.'_'.$attribute_id;
		}elseif($attribute && $attribute_id == null){
			$fileName .= '_'.$attribute;
		}
		header('Content-disposition: attachment; filename='.$fileName.'.json');
		header('Content-Type: application/json');
		echo json_encode($json_array);
		exit;
	}
	
	/*function get_orders( $order_id = null )
	{
		//$api_verified = $this->verify_api_request_get();
		$api_verified = true;
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			// create doctype
			$dom = new DOMDocument("1.0");
			header("Content-Type: text/xml");
			
			// create root element
			$root = $dom->createElement("order_details");
			$dom->appendChild($root);
			
			if($order_id && is_numeric($order_id)){
				$this->db->select('order_details.*,products.id as proid,products.company_id,products.categories_id,products.subcategories_id,products.pro_art_num,products.proname,products.price_per_unit,products.price_per_person,products.price_weight,products.type,products.discount,categories.name as cat_name');
				$this->db->join('products','order_details.products_id=products.id');
				$this->db->join('categories','categories.id=products.categories_id');
				$this->db->where(array('order_details.orders_id'=>$order_id));
				$order_details_datas = $this->db->get('order_details')->result_array();
				//$order_details_data = $this->db->get( 'orders' )->row_array();
				//print_r($order_details_datas); die();
				if(!empty($order_details_datas)){
					foreach($order_details_datas as $order_details_data){
						$detail = $dom->createElement("detail");
						$root->appendChild($detail);
							
						// create details childs element
						$quantity = $dom->createElement("Aantal");
						$detail->appendChild($quantity);
							
						// create text node for quanity number
						$text = $dom->createTextNode($order_details_data['quantity']);
						$quantity->appendChild($text);
							
							
						// create details childs element
						$article_num = $dom->createElement("Ref.Art");
						$detail->appendChild($article_num);
							
						if($order_details_data['pro_art_num'] != ''){
							$art_num = $order_details_data['pro_art_num'];
								
							$length =  strlen($art_num);
							if($length < 8){
								$required_zeros = (int)(8-$length);
								for($j = 0; $j < $required_zeros; $j++){
									$art_num = '0'.$art_num;
								}
							}
							//echo $client_number; die();
							// create text node for order number
							$text = $dom->createTextNode($art_num);
							$article_num->appendChild($text);
						}else{
							// create text node for order number
							$text = $dom->createTextNode('-');
							$article_num->appendChild($text);
						}
							
						// create details childs element
						$art_name = $dom->createElement("Benaming");
						$detail->appendChild($art_name);
							
						$text = $dom->createTextNode($order_details_data['proname']);
						$art_name->appendChild($text);
						
						// create details childs element
						$grp_name = $dom->createElement("Groep");
						$detail->appendChild($grp_name);
							
						$text = $dom->createTextNode($order_details_data['cat_name']);
						$grp_name->appendChild($text);
							
						// create details childs element
						$price = $dom->createElement("Prijs");
						$detail->appendChild($price);
							
						$per_price = round($order_details_data['sub_total'],2);
						$per_price = str_replace('.',',',$per_price);
						// create text node for order number
						$text = $dom->createTextNode($per_price);
						$price->appendChild($text);
												
						// create details childs element
						$extra_price = $dom->createElement("Opt.Prijs");
						$detail->appendChild($extra_price);
							
						// create text node for order number
						$text = $dom->createTextNode('-');
						$extra_price->appendChild($text);
							
							
						// create details childs element
						$order_mode = $dom->createElement("Memo");
						$detail->appendChild($order_mode);
							
						if($order_details_data['pro_remark'] != ''){
							// create text node for order number
							$text = $dom->createTextNode($order_details_data['pro_remark']);
							$order_mode->appendChild($text);
						}else{
							// create text node for order number
							$text = $dom->createTextNode('-');
							$order_mode->appendChild($text);
						}
						
						$article_id = $dom->createElement("Art.Id");
						$detail->appendChild($article_id);
							
						// create text node for order number
						$text = $dom->createTextNode($order_details_data['products_id']);
						$article_id->appendChild($text);
						
						$order_id = $dom->createElement("Order.Id");
						$detail->appendChild($order_id);
						
						// create text node for order number
						$text = $dom->createTextNode($order_details_data['orders_id']);
						$order_id->appendChild($text);
						
						$order_detail_id = $dom->createElement("Id");
						$detail->appendChild($order_detail_id);
							
						// create text node for order number
						$text = $dom->createTextNode($order_details_data['id']);
						$order_detail_id->appendChild($text);
						
						$option_id = $dom->createElement("option.Id");
						$detail->appendChild($option_id);
							
						// create text node for order number
						$text = $dom->createTextNode('-');
						$option_id->appendChild($text);
						
						$pp = $dom->createElement("PP");
						$detail->appendChild($pp);
							
						// create text node for order number
						$text = $dom->createTextNode('-');
						$pp->appendChild($text);
						
					}
				}
			}else{
				
				$this->db->select('orders.* , client_numbers.client_number ,clients.firstname_c , clients.lastname_c ');
				$this->db->where( 'orders.company_id' , 105 );
				$this->db->join( 'clients', 'clients.id = orders.clients_id' );
				$this->db->join( 'client_numbers', 'clients.id = client_numbers.client_id' );
				$order_details_data = $this->db->get( 'orders' )->result_array();
				//print_r($order_details_data); die();
				
				foreach($order_details_data as $order_detail){
					// create detail element
					$detail = $dom->createElement("detail");
					$root->appendChild($detail);
						
					// create details childs element
					$order_number = $dom->createElement("Nummer");
					$detail->appendChild($order_number);
						
					// create text node for order number
					$text = $dom->createTextNode($order_detail['id']);
					$order_number->appendChild($text);
						
						
					// create details childs element
					$order_date = $dom->createElement("Datum");
					$detail->appendChild($order_date);
						
					if($order_detail['order_pickupdate'] != ''){
						// create text node for order number
						$text = $dom->createTextNode($order_detail['order_pickupdate']);
						$order_date->appendChild($text);
					}else{
						$text = $dom->createTextNode('--');
						$order_date->appendChild($text);
					}
						
					// create details childs element
					$order_time = $dom->createElement("Uur");
					$detail->appendChild($order_time);
					
					if($order_detail['order_pickuptime']){
						$pickup_time = explode(':' , $order_detail['order_pickuptime']);
						$pickup_hour = $pickup_time['0'];
						$pickup_minute = $pickup_time['1'];
						if(strlen($pickup_minute) == 1){
							$pickup_minute = '0'.$pickup_minute;
						}
						
						$final_time = $pickup_hour.':'.$pickup_minute;
						
						// create text node for order number
						$text = $dom->createTextNode($final_time);
						$order_time->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$order_time->appendChild($text);
					}
						
					// create details childs element
					$client_number = $dom->createElement("Klantnr");
					$detail->appendChild($client_number);

					if($order_detail['client_number'] != ''){
						$client_num = $order_detail['client_number'];
						
						$length =  strlen($client_num);
						if($length < 8){
							$required_zeros = (int)(8-$length);
							for($j = 0; $j < $required_zeros; $j++){
								$client_num = '0'.$client_num;
							}
						}
						//echo $client_number; die();
						// create text node for order number
						$text = $dom->createTextNode($client_num);
						$client_number->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$client_number->appendChild($text);
					}
						
					// create details childs element
					$client_name = $dom->createElement("Naam");
					$detail->appendChild($client_name);
						
					if(isset($order_detail['lastname_c']) && isset($order_detail['firstname_c'])){
						// create text node for order number
						$text = $dom->createTextNode($order_detail['lastname_c'].' '.$order_detail['firstname_c']);
						$client_name->appendChild($text);
					}else{
						// create text node for order number
						$text = $dom->createTextNode('--');
						$client_name->appendChild($text);
					}
						
					// create details childs element
					$order_status = $dom->createElement("Status");
					$detail->appendChild($order_status);
						
					// create text node for order number
					$text = $dom->createTextNode($order_detail['order_status']);
					$order_status->appendChild($text);
						
						
					// create details childs element
					$order_date = $dom->createElement("Best.Date");
					$detail->appendChild($order_date);
						
					// create text node for order number
					$text = $dom->createTextNode(date("d/m/Y", strtotime($order_detail['created_date'])));
					$order_date->appendChild($text);
						
						
					// create details childs element
					$order_mode = $dom->createElement("Betaling");
					$detail->appendChild($order_mode);
						
					// create text node for order number
					$text = $dom->createTextNode(($order_detail['payment_via_paypal'])?'Paypal':'Cash');
					$order_mode->appendChild($text);
				}
				
			}

			echo $dom->saveXML();
			exit;
			
		}
		
	}
	
	function get_categories( $cat_id = null )
	{
		//$api_verified = $this->verify_api_request_get();
		$api_verified = true;
		if( !$api_verified )
		{
			$this->response( array('error' => 1, 'message'=>_('Invalid Credentials'), 'data' => '' ), 404 );
		}
		else
		{
			// create doctype
			$dom = new DOMDocument("1.0");
			header("Content-Type: text/xml");
			
			// create root element
			$root = $dom->createElement("category_details");
			$dom->appendChild($root);
			
			if($cat_id && is_numeric($cat_id)){
				
				$this->db->where( 'categories.company_id' , 105 );
				$this->db->where( 'categories.id' , $cat_id );
				$category_details_data = $this->db->get( 'categories' )->result_array();
				//$order_details_data = $this->db->get( 'orders' )->row_array();
				//print_r($order_details_datas); die();
				if(!empty($category_details_data)){
					foreach($category_details_data as $category_detail){
						$detail = $dom->createElement("detail");
						$root->appendChild($detail);
							
						// create details childs element
						$cat_id = $dom->createElement("Nummer");
						$detail->appendChild($cat_id);
							
						// create text node for cat number
						$text = $dom->createTextNode($category_detail['id']);
						$cat_id->appendChild($text);
							
						// create details childs element
						$cat_name = $dom->createElement("Naam");
						$detail->appendChild($cat_name);
												
						$text = $dom->createTextNode($category_detail['name']);
						$cat_name->appendChild($text);
							
						// create details childs element
						$cat_status = $dom->createElement("Status");
						$detail->appendChild($cat_status);
							
						// create text node for order number
						$text = $dom->createTextNode($category_detail['status']);
						$cat_status->appendChild($text);
							
							
						// create details childs element
						$cat_desc = $dom->createElement("Description");
						$detail->appendChild($cat_desc);
							
						// create text node for order number
						$text = $dom->createTextNode($category_detail['description']);
						$cat_desc->appendChild($text);
					}
				}
			}else{
				
				$this->db->where( 'categories.company_id' , 105 );
				$category_details_data = $this->db->get( 'categories' )->result_array();
				//print_r($order_details_data); die();
				
				foreach($category_details_data as $category_data){
					// create detail element
					$detail = $dom->createElement("detail");
					$root->appendChild($detail);
						
					// create details childs element
					$cat_id = $dom->createElement("Nummer");
					$detail->appendChild($cat_id);
						
					// create text node for cat number
					$text = $dom->createTextNode($category_data['id']);
					$cat_id->appendChild($text);
						
					// create details childs element
					$cat_name = $dom->createElement("Naam");
					$detail->appendChild($cat_name);
											
					$text = $dom->createTextNode($category_data['name']);
					$cat_name->appendChild($text);
						
					// create details childs element
					$cat_status = $dom->createElement("Status");
					$detail->appendChild($cat_status);
						
					// create text node for order number
					$text = $dom->createTextNode($category_data['status']);
					$cat_status->appendChild($text);
						
						
					// create details childs element
					$cat_desc = $dom->createElement("Description");
					$detail->appendChild($cat_desc);
						
					// create text node for order number
					$text = $dom->createTextNode($category_data['description']);
					$cat_desc->appendChild($text);
					
				}
				
			}

			echo $dom->saveXML();
			exit;
			
		}
		
	}*/
	
	function test(){
		return time();
	}
}
?>