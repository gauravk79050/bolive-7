<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_printer extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->lang_u = get_lang( $_COOKIE['locale'] );
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	public function label_export_order($type = 'per_ordered_product', $key = ''){
		$response = array();
		$this->db->select('company_id');
		$comp_det = $this->db->get_where('api',array('api_secret'=>$key))->result();

		if(!empty($comp_det)){
			$comp_id = $comp_det[0]->company_id;

			$general_settings = $this->db->get_where('general_settings',array('company_id'=>$comp_id))->result();

			$this->db->select('orders.*,clients.id AS client_id,clients.firstname_c,clients.lastname_c,clients.company_c,clients.address_c,clients.housenumber_c,clients.postcode_c,clients.phone_c,clients.mobile_c,clients.city_c');
			$this->db->join('clients','clients.id=orders.clients_id');
			$this->db->where('orders.labeler_printed', 0);
			$this->db->where('orders.company_id', $comp_id);
			if($general_settings[0]->printer_orders == 1){
				$this->db->where("( order_pickupdate = '".date('Y-m-d')."' OR delivery_date = '".date('Y-m-d')."')" );
				//$this->db->where("( order_pickupdate = '2016-02-25' OR delivery_date = '2016-02-25')" );
			}
			$orders = $this->db->get('orders')->result();
			//echo '<pre>';print_r($general_settings);print_r($orders);die;
			if(!empty($orders)){

				if($general_settings['0']->activate_labeler){

					
					$this->fdb->select('all_id,all_name_dch');
					$aller_arr = $this->fdb->get('allergence')->result_array();

					$labeler_array = array();
					$order_id_array = array();
					$counting = 0;

					$this->db->select('company.company_name,company.address,company.zipcode,company.city,company.phone,company.website,general_settings.labeler_logo');
					$this->db->join('general_settings','general_settings.company_id=company.id');
					$this->db->where('company.id',$comp_id);
					$company = $this->db->get('company')->row_array();

					foreach ($orders as $order){
						$order_id_array[] = $order_id = $order->id;

						if($type == 'per_order'){
							$sent_arr = array();
							// Adding Order ID
							$sent_arr['order_id'] = $order_id;

							// Adding user name and total amount
							$sent_arr['name'] = str_replace('&','',$order->firstname_c." ".$order->lastname_c);

							//Adding company name
							$sent_arr['company_c'] = $order->company_c;

							// Adding Total amount
							$total_price = round($order->order_total,2);
							$total_price = (string)$total_price;
							//$total_price = str_replace('.',',',$total_price);
							$sent_arr['amount'] = $total_price;

							// Adding address
							$add_text = '';
							$add_text .= addslashes($order->address_c)." ".addslashes($order->housenumber_c)."\n".addslashes($order->postcode_c)." ".$order->city_c."\n";

							// Adding phone number
							if($order->phone_c){
								$add_text .= $order->phone_c."\n";
							}elseif($order->mobile_c){
								$add_text .= $order->mobile_c."\n";
							}

							$sent_arr['address'] = $add_text;

							$remark = "";

							// Adding pickup date
							$date_content = '--';
							if($order->option == 1){

								$remark = str_replace('&','',$order->order_remarks);

								$pickup_content = date("d / m",strtotime($order->order_pickupdate))." om ".$order->order_pickuptime;

								$pickup_day = date("D",strtotime($order->order_pickupdate));

								if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
								{
									if( $pickup_day == 'Mon' )
										$pickup_day = 'Ma';
									if( $pickup_day == 'Tue' )
										$pickup_day = 'Di';
									if( $pickup_day == 'Wed' )
										$pickup_day = 'Wo';
									if( $pickup_day == 'Thu' )
										$pickup_day = 'Do';
									if( $pickup_day == 'Fri' )
										$pickup_day = 'Vr';
									if( $pickup_day == 'Sat' )
										$pickup_day = 'Za';
									if( $pickup_day == 'Sun' )
										$pickup_day = 'Zo';
								}

								$date_content = _("Pickup")."\n".$pickup_day.' '.$pickup_content;

							}elseif($order->option == 2){

								$remark = str_replace('&','',$order->delivery_remarks);

								$delivery_date_content =date("d/m",strtotime($order->delivery_date))." om ".$order->delivery_hour.":".$order->delivery_minute;

								$delivery_day = date("D",strtotime($order->delivery_date));

								if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
								{
									if( $delivery_day == 'Mon' )
										$delivery_day = 'Ma';
									if( $delivery_day == 'Tue' )
										$delivery_day = 'Di';
									if( $delivery_day == 'Wed' )
										$delivery_day = 'Wo';
									if( $delivery_day == 'Thu' )
										$delivery_day = 'Do';
									if( $delivery_day == 'Fri' )
										$delivery_day = 'Vr';
									if( $delivery_day == 'Sat' )
										$delivery_day = 'Za';
									if( $delivery_day == 'Sun' )
										$delivery_day = 'Zo';
								}

								$date_content = _("Delivery")."\n".$delivery_day.' '.$delivery_date_content;
							}
							$sent_arr['date'] = $date_content;

							if(strlen($remark) >= 100){
								$remark = substr($remark,0,100)."...";
							}
							$remark = addslashes($remark);

							$sent_arr['remark'] = '';
							if($remark != ''){
								$sent_arr['remark'] = _("Opmerking").": ".$remark;
							}

							$labeler_array[$counting] = json_encode($sent_arr);
							$counting++;
						}
						elseif($type == 'per_ordered_product'){
							$this->load->model('Morder_details');
							$this->load->model('Morders');
							// $order_data_pre = $this->Morders->get_orders($order_id);
							$order_data = $this->Morder_details->get_order_details($order_id);

							if(!empty($order_data)){
								foreach($order_data as $values){

									$sent_arr = array();
									$pro_id = $values->proid;

									$this->load->model('Mproducts');

									$product_ingredients = $this->Mproducts->get_product_ingredients_dist($pro_id);
									$product_ingredients_vetten = $this->Mproducts->get_product_ingredients_vetten_dist($pro_id);
									$product_additives = $this->Mproducts->get_product_additives_dist($pro_id);

									$product_allergences = $this->Mproducts->get_product_allergence_dist($pro_id);
									$product_sub_allergences = $this->Mproducts->get_product_sub_allergence_dist($pro_id);

									$all_id_arr = array();
									if(!empty($product_allergences)){
										foreach ($product_allergences as $aller){
											$all_id_arr[] = $aller->ka_id;
										}
									}

									$allergence_words = array();
									foreach ($aller_arr as $val){
										if(in_array($val['all_id'],$all_id_arr))
											$allergence_words[strtolower($val['all_name_dch'])] = $val['all_id'];
									}

									$ing = '';
									foreach ($product_ingredients as $ingredients){

										if($ingredients->ki_name == ')' ){
											$ing = substr($ing, 0, -2);
											$ing .= ' ';
										}
										if($ingredients->ki_name == '(' ){
											$ing = substr($ing, 0, -2);
											$ing .= ' ';
										}

										if($ingredients->ki_id != 0){
											if($ingredients->prefix == ''){
												$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
											}else{
												$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words,$ingredients->prefix ).'('.$ingredients->prefix.')'.', ';
											}
										}else if($ingredients->ki_name == ')'){

											$ing .= $ingredients->ki_name.', ';

										}else if($ingredients->ki_name == '('){

											$ing .= $ingredients->ki_name.' ';

										}else{
											if($ingredients->prefix == ''){
												$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
											}else{
												$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words,$ingredients->prefix).'('.$ingredients->prefix.')'.', ';
											}
										}
									}

									$ing = substr($ing, 0, -2);

									$ing_end = "";
									if(!empty($product_ingredients_vetten)){
										$ing_end .= "Plantaardige vetstof (";
										foreach ($product_ingredients_vetten as $vetten){
											$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words);
										}
										$ing_end = rtrim(trim($ing_end),",");
										$ing_end .= ")";
									}

									if(!empty($product_additives)){
										$add_name = 'add_name'.$this->lang_u;
										$additive_arr = array();
										foreach ($product_additives as $add){
											if(!in_array($add->$add_name,$additive_arr)){
												$additive_arr[] = $add->$add_name;
											}
										}

										for($i = 0; $i < count($additive_arr); $i++){
											if($ing_end != ""){
												$ing_end .= ", ";
											}
											if($additive_arr[$i] != "others")
												$ing_end .= stripslashes($additive_arr[$i]);

											$count = 0;
											$add_ing = "";
											foreach ($product_additives as $add){
												if(($add->$add_name == $additive_arr[$i]) && ($add->ki_name != "")){
													$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words);
													$count = $count+1;
												}
											}
											$add_ing = rtrim(trim($add_ing),",");
											if($count == 1){
												if($additive_arr[$i] == "others"){
													$ing_end .= " ".$add_ing;
												}else{
													$ing_end .= ": ".$add_ing;
												}
											}
											elseif ($count >1 ){
												if($additive_arr[$i] == "others"){
													$ing_end .= $add_ing;
												}else{
													$ing_end .= " (".$add_ing.")";
												}
											}
										}
									}
									if($ing_end != ""){
										$ing_end = ", ".$ing_end;
									}

									$ing .= $ing_end;
									$ing = str_replace('<b>', '', $ing);
									$ing = str_replace('</b>', '', $ing);

									$all = '';

									foreach ($product_allergences as $allergence){
										$all .= $allergence->ka_name;

										if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
											$a1 = '';
											if(!empty($product_sub_allergences)){
												$a1 .= ' (';
												foreach ($product_sub_allergences as $sub_allergence){
													if($sub_allergence->parent_ka_id == $allergence->ka_id){
														$a1 .=  $sub_allergence->sub_ka_name.', ';
													}
												}
												$a1 = rtrim($a1,', ');
												$a1 .= ')';
												$a1 = str_replace('()', '', $a1);
											}
											$all .= $a1;
										}
										$all .=  ', ';
									}
									if(strpos($all,'Melk') !== false && strpos($all,'Lactose') !== false){
										$all = str_replace('Melk', 'Melk(incl. lactose)', $all);
										$all = str_replace('Lactose, ', '', $all);
									}
									$all = ($all != '')?substr($all, 0, -2):'';

									$recipe_wt = $values->recipe_weight;
									if($recipe_wt != 0){
										$recipe_wt = $recipe_wt*1000;
									}else{
										$recipe_wt = 100;
									}
									$this->load->model('M_fooddesk');
									$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($pro_id);
									$nutri_values = array();
									$nutri_str = '';
									if (!empty($has_fdd_quant)){
										$nutri_values['e_val_1'] = 0;
										$nutri_values['e_val_2'] = 0;
										$nutri_values['protiens'] = 0;
										$nutri_values['carbo'] = 0;
										$nutri_values['sugar'] = 0;
										$nutri_values['poly'] = 0;
										$nutri_values['farina'] = 0;
										$nutri_values['fats'] = 0;
										$nutri_values['sat_fats'] = 0;
										$nutri_values['single_fats'] = 0;
										$nutri_values['multi_fats'] = 0;
										$nutri_values['salt'] = 0;
										$nutri_values['fibers'] = 0;

										foreach ($has_fdd_quant as $has_fdd_qu){
											$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
											if( !empty( $fdd_pro_info ) ){
												$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
												$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
												$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
												$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
												$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
												$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
												$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
												$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
											}
										}
										$nutri_str = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'])."kj/ ".defined_money_format($nutri_values['e_val_1'])."kcal. vetten ".defined_money_format($nutri_values['fats'])."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'])."g. koolhydraten ".defined_money_format($nutri_values['carbo'])."g. waarvan - suikers ".defined_money_format($nutri_values['sugar'])."g. eiwitten ".defined_money_format($nutri_values['protiens'])."g. zout ".defined_money_format($nutri_values['salt'])."g";
									}

									$this->db->select('duedate,conserve_min,conserve_max');
									$this->db->where('product_id',$pro_id);
									$product = $this->db->get('products_labeler')->row_array();

									$zipcity = trim($company['zipcode']." ".$company['city']);
									$labeler_logo = ($company['labeler_logo'] != '')?base_url()."assets/cp/labeler_logo/".$company['labeler_logo']:"";
									$duedate = (!empty($product) && $product['duedate'] != '')?date('d/m/Y', strtotime('+'.$product['duedate'].' days')):'';
									$bewaren = (!empty($product) && $product['conserve_min'] != '' && $product['conserve_max'] != '')?"Bewaren tussen ".$product['conserve_min']." en ".$product['conserve_max']."°C":'';

									date_default_timezone_set('Europe/Brussels');
									$year = date('y');
									$day_num = str_pad(date('z') + 1, 3, "0", STR_PAD_LEFT);
									$hour = date('H');
									$min = date('i');
									$lotnr = $year.$day_num.$hour.$min;

									$sent_arr['plu'] = $values->pro_art_num;
									$sent_arr['pro_name'] = stripslashes($values->proname);
									$sent_arr['ingre'] = $ing;
									$sent_arr['aller'] = $all;
									$sent_arr['nutri'] = $nutri_str;
									$sent_arr['tht'] = $duedate;
									$sent_arr['lotnr'] = $lotnr;
									$sent_arr['bewaren'] = $bewaren;
									$sent_arr['comp_name'] = $company['company_name'];
									$sent_arr['address'] = trim($company['address']);
									$sent_arr['zip_city'] = $zipcity;
									$sent_arr['phone'] = $company['phone'];
									$sent_arr['url'] = $company['website'];
									$sent_arr['logo'] = $labeler_logo;

									$quantity_unit = '';
									if($values->content_type != 1 && $values->content_type != 2){
										for($j = 0; $j < (int)$values->quantity; $j++){
											$total_price = round($values->default_price,2);
											$total_price = (string)$total_price;
											$total_price = $total_price.' €';
											$sent_arr['tarief'] = str_replace('.',',',$total_price);
											$labeler_array[$counting] = json_encode($sent_arr);
											$counting++;
										}
									}
									else{
										$total_price = round($values->default_price,2);
										$total_price = (string)$total_price;
										$total_price = $total_price.' €';
										$sent_arr['tarief'] = str_replace('.',',',$total_price);
										$labeler_array[$counting] = json_encode($sent_arr);
										$counting++;
									}
								}
							}
						}
					}

					$response = array('success'=>1,'message'=>'','data'=>$labeler_array,'ids'=> $order_id_array);
				}
				else{
					$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
				}
			}
			else{
				$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
			}
		}
		else{
			$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
		}
		echo json_encode($response);
	}

	function label_export_result(){
		$response = json_decode($_POST['orderids']);
		if(is_array($response)){
			$this->db->where_in('id',$response);
			$this->db->update('orders', array('labeler_printed' => 1));
		}
	}

	function print_product_label($key = ''){
		$response = array();
		$this->db->select('company_id');
		$comp_det = $this->db->get_where('api',array('api_secret'=>$key))->result();

		if(!empty($comp_det)){
			$comp_id = $comp_det[0]->company_id;

			$this->db->where(array('company_id'=>$comp_id));
			$is_num = $this->db->get('products_label_count')->result();

			if(!empty($is_num)){
				$num_lab = $is_num[0]->label_count;

				$this->db->select('company.company_name,company.address,company.zipcode,company.city,company.phone,company.website,general_settings.labeler_logo');
				$this->db->join('general_settings','general_settings.company_id=company.id');
				$this->db->where('company.id',$comp_id);
				$company = $this->db->get('company')->row_array();

				$this->db->select('products.id,pro_art_num,proname,prodescription,sell_product_option,price_per_person,price_per_unit,price_weight,recipe_weight,duedate,conserve_min,conserve_max');
				$this->db->join('products_labeler','products_labeler.product_id=products.id');
				$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
				$this->db->where($where);
				$this->db->where('products.id',$is_num[0]->product_id);
				$product = $this->db->get('products')->row_array();

				$sent_arr = array();
				if(!empty($product)){
					$this->load->model('Mproducts');
					$this->fdb->select('all_id,all_name_dch');
					$aller_arr = $this->fdb->get('allergence')->result_array();

					$tarief = '';
					if ($product['sell_product_option'] == 'per_unit')
						$tarief = ($product['price_per_unit'] != '' && $product['price_per_unit'] != null)?defined_money_format($product['price_per_unit']).' €':'0 €';
					elseif ($product['sell_product_option'] == 'per_person')
					$tarief = ($product['price_per_person'] != '' && $product['price_per_person'] != null)?defined_money_format($product['price_per_person']).' €/P.':'0 €/P.';
					elseif ($product['sell_product_option'] == 'weight_wise')
					$tarief = ($product['price_weight'] != '' && $product['price_weight'] != null)?defined_money_format($product['price_weight']*1000).' €/'._('kg'):'0 €/'._('kg');
					elseif ($product['sell_product_option'] == 'client_may_choose'){
						$tarief = ($product['price_per_unit'] != '' && $product['price_per_unit'] != null)?defined_money_format($product['price_per_unit']).' €':'0 €';
						$tarief .= "\n";
						$tarief .= ($product['price_weight'] != '' && $product['price_weight'] != null)?defined_money_format($product['price_weight']*1000).' €/'._('kg'):'0 €/'._('kg');
					}
					$product_ingredients = $this->Mproducts->get_product_ingredients_dist($product['id']);
					$product_ingredients_vetten = $this->Mproducts->get_product_ingredients_vetten_dist($product['id']);
					$product_additives = $this->Mproducts->get_product_additives_dist($product['id']);

					$product_allergences = $this->Mproducts->get_product_allergence_dist($product['id']);
					$product_sub_allergences = $this->Mproducts->get_product_sub_allergence_dist($product['id']);

					$all_id_arr = array();
					if(!empty($product_allergences)){
						foreach ($product_allergences as $aller){
							$all_id_arr[] = $aller->ka_id;
						}
					}

					$allergence_words = array();
					foreach ($aller_arr as $val){
						if(in_array($val['all_id'],$all_id_arr))
							$allergence_words[strtolower($val['all_name_dch'])] = $val['all_id'];
					}

					$ing = '';
					foreach ($product_ingredients as $ingredients){

						if($ingredients->ki_name == ')' ){
							$ing = substr($ing, 0, -2);
							$ing .= ' ';
						}
						if($ingredients->ki_name == '(' ){
							$ing = substr($ing, 0, -2);
							$ing .= ' ';
						}

						if($ingredients->ki_id != 0){
							if($ingredients->prefix == ''){
								$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
							}else{
								$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words, $ingredients->prefix).'('.$ingredients->prefix.')'.', ';
							}
						}else if($ingredients->ki_name == ')'){

							$ing .= $ingredients->ki_name.', ';

						}else if($ingredients->ki_name == '('){

							$ing .= $ingredients->ki_name.' ';

						}else{
							if($ingredients->prefix == ''){
								$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
							}else{
								$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words,$ingredients->prefix).'('.$ingredients->prefix.')'.', ';
							}
						}
					}

					$ing = substr($ing, 0, -2);

					$ing_end = "";
					if(!empty($product_ingredients_vetten)){
						$ing_end .= "Plantaardige vetstof (";
						foreach ($product_ingredients_vetten as $vetten){
							$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words);
						}
						$ing_end = rtrim(trim($ing_end),",");
						$ing_end .= ")";
					}

					if(!empty($product_additives)){
						$add_name = 'add_name'.$this->lang_u;
						$additive_arr = array();
						foreach ($product_additives as $add){
							if(!in_array($add->$add_name,$additive_arr)){
								$additive_arr[] = $add->$add_name;
							}
						}

						for($i = 0; $i < count($additive_arr); $i++){
							if($ing_end != ""){
								$ing_end .= ", ";
							}
							if($additive_arr[$i] != "others")
								$ing_end .= stripslashes($additive_arr[$i]);

							$count = 0;
							$add_ing = "";
							foreach ($product_additives as $add){
								if(($add->$add_name == $additive_arr[$i]) && ($add->ki_name != "")){
									$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words);
									$count = $count+1;
								}
							}
							$add_ing = rtrim(trim($add_ing),",");
							if($count == 1){
								if($additive_arr[$i] == "others"){
									$ing_end .= " ".$add_ing;
								}else{
									$ing_end .= ": ".$add_ing;
								}
							}
							elseif ($count >1 ){
								if($additive_arr[$i] == "others"){
									$ing_end .= $add_ing;
								}else{
									$ing_end .= ": ".$add_ing."";
								}
							}
						}
					}
					if($ing_end != ""){
						$ing_end = ", ".$ing_end;
					}

					$ing .= $ing_end;
					$ing = str_replace('<b>', '', $ing);
					$ing = str_replace('</b>', '', $ing);

					$all = '';

					foreach ($product_allergences as $allergence){
						$all .= $allergence->ka_name;

						if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
							$a1 = '';
							if(!empty($product_sub_allergences)){
								$a1 .= ' (';
								foreach ($product_sub_allergences as $sub_allergence){
									if($sub_allergence->parent_ka_id == $allergence->ka_id){
										$a1 .=  $sub_allergence->sub_ka_name.', ';
									}
								}
								$a1 = rtrim($a1,', ');
								$a1 .= ')';
								$a1 = str_replace('()', '', $a1);
							}
							$all .= $a1;
						}
						$all .=  ', ';
					}
					if(strpos($all,'Melk') !== false && strpos($all,'Lactose') !== false){
						$all = str_replace('Melk', 'Melk(incl. lactose)', $all);
						$all = str_replace('Lactose, ', '', $all);
					}
					$all = ($all != '')?substr($all, 0, -2):'';

					$recipe_wt = $product['recipe_weight'];
					if($recipe_wt != 0){
						$recipe_wt = $recipe_wt*1000;
					}else{
						$recipe_wt = 100;
					}
					$this->load->model('M_fooddesk');
					$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($product['id']);
					$nutri_values = array();
					$nutri_str = '';
					if (!empty($has_fdd_quant)){
						$nutri_values['e_val_1'] = 0;
						$nutri_values['e_val_2'] = 0;
						$nutri_values['protiens'] = 0;
						$nutri_values['carbo'] = 0;
						$nutri_values['sugar'] = 0;
						$nutri_values['poly'] = 0;
						$nutri_values['farina'] = 0;
						$nutri_values['fats'] = 0;
						$nutri_values['sat_fats'] = 0;
						$nutri_values['single_fats'] = 0;
						$nutri_values['multi_fats'] = 0;
						$nutri_values['salt'] = 0;
						$nutri_values['fibers'] = 0;

						foreach ($has_fdd_quant as $has_fdd_qu){
							$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
							if( !empty( $fdd_pro_info ) ){
								$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
								$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
								$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
								$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
								$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
								$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
								$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
								$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
							}
						}
						$fibers = ($nutri_values['fibers'] != 0)?". Vezels ".defined_money_format($nutri_values['fibers'],1)."g":"";
						$nutri_str = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. waarvan - suikers ".defined_money_format($nutri_values['sugar'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
					}

					$zipcity = trim($company['zipcode']." ".$company['city']);
					$labeler_logo = ($company['labeler_logo'] != '')?base_url()."assets/cp/labeler_logo/".$company['labeler_logo']:"";
					$duedate = ($product['duedate'] != '')?date('d/m/Y', strtotime('+'.$product['duedate'].' days')):'';
					$bewaren = ($product['conserve_min'] != '' && $product['conserve_max'] != '')?"Bewaren tussen ".$product['conserve_min']." en ".$product['conserve_max']."°C":"";

					date_default_timezone_set('Europe/Brussels');
					$year = date('y');
					$day_num = str_pad(date('z') + 1, 3, "0", STR_PAD_LEFT);
					$hour = date('H');
					$min = date('i');
					$lotnr = $year.$day_num.$hour.$min;

					$sent_arr['plu'] = $product['pro_art_num'];
					$sent_arr['pro_name'] = stripslashes($product['proname']);
					$sent_arr['tarief'] = $tarief;
					$sent_arr['ingre'] = $ing;
					$sent_arr['aller'] = $all;
					$sent_arr['nutri'] = $nutri_str;
					$sent_arr['tht'] = $duedate;
					$sent_arr['lotnr'] = $lotnr;
					$sent_arr['bewaren'] = $bewaren;
					$sent_arr['comp_name'] = $company['company_name'];
					$sent_arr['address'] = trim($company['address']);
					$sent_arr['zip_city'] = $zipcity;
					$sent_arr['phone'] = $company['phone'];
					$sent_arr['url'] = $company['website'];
					$sent_arr['logo'] = $labeler_logo;

					$response = array('success'=>1,'message'=>'','data'=>json_encode($sent_arr),'count'=>$num_lab);
				}
				else{
					$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
				}
			}
			else{
				$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
			}
		}
		else{
			$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
		}
	}
}