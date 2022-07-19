<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Shop Library
 *
 * This is a for shop json files update
 *
 * @package Libraries
 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
 */
class Shop {

	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->helper('default_setting_helper');
		$this->CI->load->helper('curo_helper');
		$this->CI->load->helper('url');
		$this->CI->load->library('session');
		$this->CI->load->database();
		$this->CI->fdb = $this->CI->load->database('fdb', true);
	}

	function clean($string){
		$string = strtolower(str_replace(' ', '-', $string)); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}

	function update_json_files($shop_version = 0,$company_id = 0,$company_role = '',$company_parent_id = '',$action= ' category_json',$setting_action = 'general_setting_json'){
		
		$response = array();
		$this->CI->db->where(array('id'=> $company_id));
		$company = $this->CI->db->get('company')->result_array();
		if( $company[0]['ac_type_id'] == 3 || $company[0]['ac_type_id'] == 4 || $company[0]['ac_type_id'] == 5 || $company[0]['ac_type_id'] == 6)
		{
			if($shop_version == 2 || $shop_version == 3){
				$responser = array();
				$seo_response = array();
				$search_products = array();
				$products = array();
				$pickup_url_key = array();
				$delivery_url_key = array();
				$result1 = $result2 = array();

				if( ($this->CI->input->post('action') == 'category_json' || $action == 'category_json') || $this->CI->input->post('action') == 'general_setting_json'){
					if( $company_role == 'master' || $company_role == 'super' ) {
						$responser = $this->get_shop_content_json_new ( $company_id );
						$seo_pickup_data = $responser['pickup'];

						foreach($seo_pickup_data as $key=>$seo_pickup){
							foreach($seo_pickup as $k=>$seo_pick){
								$seo_pickup_cat_product =  $seo_pick->product;
								$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
								$category_seo_key = $this->clean($seo_pick->name);
								$seo_response[$category_seo_key] = $seo_pick;
								$seo_response[$category_seo_key]->slug = $category_seo_key;
								$seo_response[$category_seo_key]->product='';
								$seo_response[$category_seo_key]->sub_category='';

								if(isset($seo_pickup_cat_product)){
									foreach($seo_pickup_cat_product as $k=>$seo_pickup_product){
										if(isset($seo_pickup_product)){
											if(!empty($seo_pickup_product->proname)){
												$category_seo_product_key = $this->clean($seo_pickup_product->proname);

												if (in_array($category_seo_product_key, $pickup_url_key)){
													$i=1;
													while(in_array($category_seo_product_key, $pickup_url_key)){
														$category_seo_product_key = $category_seo_product_key.'-'.$i;
														if($i > 1){
															$tokens = explode('-', $category_seo_product_key);
															array_pop($tokens);
															array_pop($tokens);
															$category_seo_product_key = implode('-', $tokens);
															$category_seo_product_key = $category_seo_product_key.'-'.$i;
														}
														$i++;
													}
													$pickup_url_key[] = $category_seo_product_key;
												}
												else{
													$pickup_url_key[] = $category_seo_product_key;
												}
												if($category_seo_product_key && $seo_pickup_product)
													$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
												$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
											}
										}
									}
								}

								if(isset($seo_pickup_sub_cat_seo)){
									foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat){
										if(isset($seo_pickup_sub_cat)){
											$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
											$category_seo_sub_cat_key = $this->clean($seo_pickup_sub_cat->subname);

											if($category_seo_sub_cat_key && $seo_pickup_sub_cat)
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';
										}

										if(isset($seo_pickup_subcat_product)){
											foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product){
												if(isset($seo_pickup_sub_product)){
													if(!empty($seo_pickup_sub_product->proname)){
														$category_seo_sub_cat_product_key = $this->clean($seo_pickup_sub_product->proname);

														if (in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
															$i=1;
															while(in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
																$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
																if($i > 1){
																	$tokens = explode('-', $category_seo_sub_cat_product_key);
																	array_pop($tokens);
																	array_pop($tokens);
																	$category_seo_sub_cat_product_key = implode('-', $tokens);
																	$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
																}
																$i++;
															}
															$pickup_url_key[] = $category_seo_sub_cat_product_key;
														}
														else{
															$pickup_url_key[] = $category_seo_sub_cat_product_key;
														}

														if($category_seo_sub_cat_product_key && $seo_pickup_sub_product)
															$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'pickup';
														$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
													}
												}
											}
										}
									}
								}
							}
						}

						$response['pickup']['category'] = $seo_response;

						$fp = fopen(dirname(__FILE__).'/../../../obs/online-bestellen/json/pickup_product_cat_'.$company_id.'.json', 'w');
						fwrite($fp, json_encode($response));
						fclose($fp);

						$response = array();

						$pickup_product = $seo_response;
						$pickup_product_id = array();
						$pick_cat = array();
						$searchpickup_product = array();
						foreach($pickup_product as $k=>$val){
							$pick_cat[] = $val->id;
						}

						$all_products = $products;
						foreach($all_products as $k=>$product){
							$categories_id = $product->categories_id;

							if(in_array($categories_id, $pick_cat)){
								if(!in_array($product->id, $pickup_product_id)){
									$pickup_product_id[] = $product->id;
									$searchpickup_product[] = $all_products[$k];
								}
							}
						}

						$response['search_pickup'] = $searchpickup_product;

						$fp = fopen(dirname(__FILE__).'/../../../obs/online-bestellen/json/search_pickup_product_'.$company_id.'.json', 'w');
						fwrite($fp, json_encode($response));
						fclose($fp);

						$response = array();

						$seo_response = array();
						$search_products = array();
						$products = array();

						$seo_pickup_data = $responser['delivery'];

						foreach($seo_pickup_data as $key=>$seo_pickup){
							foreach($seo_pickup as $k=>$seo_pick){
								$seo_pickup_cat_product =  $seo_pick->product;
								$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
								$category_seo_key = $this->clean($seo_pick->name);
								$seo_response[$category_seo_key] = $seo_pick;
								$seo_response[$category_seo_key]->slug = $category_seo_key;
								$seo_response[$category_seo_key]->product='';
								$seo_response[$category_seo_key]->sub_category='';

								if(isset($seo_pickup_cat_product)){
									foreach($seo_pickup_cat_product as $k=>$seo_pickup_product){
										if(isset($seo_pickup_product)){
											if(!empty($seo_pickup_product->proname)){
												$category_seo_product_key = $this->clean($seo_pickup_product->proname);

												if (in_array($category_seo_product_key, $pickup_url_key)){
													$i=1;
													while(in_array($category_seo_product_key, $pickup_url_key)){
														$category_seo_product_key = $category_seo_product_key.'-'.$i;
														if($i > 1){
															$tokens = explode('-', $category_seo_product_key);
															array_pop($tokens);
															array_pop($tokens);
															$category_seo_product_key = implode('-', $tokens);
															$category_seo_product_key = $category_seo_product_key.'-'.$i;
														}
														$i++;
													}
													$pickup_url_key[] = $category_seo_product_key;
												}
												else{
													$pickup_url_key[] = $category_seo_product_key;
												}

												if($category_seo_product_key && $seo_pickup_product)
													$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
												$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
												$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
											}
										}
									}
								}

								if(isset($seo_pickup_sub_cat_seo)){
									foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat){
										if(isset($seo_pickup_sub_cat)){
											$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
											$category_seo_sub_cat_key = $this->clean($seo_pickup_sub_cat->subname);

											if($category_seo_sub_cat_key && $seo_pickup_sub_cat)
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';
										}

										if(isset($seo_pickup_subcat_product)){
											foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product){
												if(isset($seo_pickup_sub_product)){
													if(!empty($seo_pickup_sub_product->proname)){
														$category_seo_sub_cat_product_key = $this->clean($seo_pickup_sub_product->proname);

														if (in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
															$i=1;
															while(in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
																$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
																if($i > 1){
																	$tokens = explode('-', $category_seo_sub_cat_product_key);
																	array_pop($tokens);
																	array_pop($tokens);
																	$category_seo_sub_cat_product_key = implode('-', $tokens);
																	$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
																}
																$i++;
															}
															$pickup_url_key[] = $category_seo_sub_cat_product_key;
														}
														else{
															$pickup_url_key[] = $category_seo_sub_cat_product_key;
														}

														if($category_seo_sub_cat_product_key && $seo_pickup_sub_product)
															$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
														$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'pickup';
														$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
													}
												}
											}
										}
									}
								}
							}
						}

						$response['delivery']['category'] = $seo_response;

						$fp = fopen(dirname(__FILE__).'/../../../obs/online-bestellen/json/delivery_product_cat_'.$company_id.'.json', 'w');
						fwrite($fp, json_encode($response));
						fclose($fp);

						$response = array();

						$pickup_product = $seo_response;
						$pickup_product_id = array();
						$pick_cat = array();
						$searchpickup_product = array();

						foreach($pickup_product as $k=>$val){
							$pick_cat[] = $val->id;
						}

						$all_products = $products;
						foreach($all_products as $k=>$product){
							$categories_id = $product->categories_id;

							if(in_array($categories_id, $pick_cat)){
								if(!in_array($product->id, $pickup_product_id)){
									$pickup_product_id[] = $product->id;
									$searchpickup_product[] = $all_products[$k];
								}
							}
						}
						$response['search_delivery'] = $searchpickup_product;

						$fp = fopen(dirname(__FILE__).'/../../../obs/online-bestellen/json/search_delivery_product_'.$company_id.'.json', 'w');
						fwrite($fp, json_encode($response));
						fclose($fp);

						if($shop_version == 2)
							$result1 = $this->update_files($company_id,array('pickup_product_cat_'.$company_id.'.json','search_pickup_product_'.$company_id.'.json','delivery_product_cat_'.$company_id.'.json','search_delivery_product_'.$company_id.'.json'));
						elseif($shop_version == 3)
							$result1 = $this->update_files_own(array('pickup_product_cat_'.$company_id.'.json','search_pickup_product_'.$company_id.'.json','delivery_product_cat_'.$company_id.'.json','search_delivery_product_'.$company_id.'.json'));

						$response = array();
					}
				}

				if($this->CI->input->post('action') == 'general_setting_json' || $setting_action == 'general_setting_json'){
					if( $company_role == 'sub') {
						$company_id = $company_parent_id;
					}
					$gene_setting = $this->get_shop_content_all_post( $company_id );
					$fp = fopen(dirname(__FILE__).'/../../../obs/online-bestellen/json/general_settings_'.$company_id.'.json', 'w');
					fwrite($fp, json_encode($gene_setting));
					fclose($fp);

					if($shop_version == 2)
						$result2 = $this->update_files($company_id,array('general_settings_'.$company_id.'.json'));
					elseif($shop_version == 3)
						$result2 = $this->update_files_own(array('general_settings_'.$company_id.'.json'));
				}
				echo json_encode(array('category_json'=>$result1,'general_setting_json'=>$result2));
			}
		}
	}

	function update_demo_files($company_id = 0,$company_role = ''){
		$response = array();

		$responser = array();
		$seo_response = array();
		$search_products = array();
		$products = array();
		$pickup_url_key = array();
		$delivery_url_key = array();

		if( $company_role == 'master' || $company_role == 'super' ) {
			$responser = $this->get_shop_content_json_new ( $company_id );
			$seo_pickup_data = $responser['pickup'];

			foreach($seo_pickup_data as $key=>$seo_pickup){
				foreach($seo_pickup as $k=>$seo_pick){
					$seo_pickup_cat_product =  $seo_pick->product;
					$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
					$category_seo_key = $this->clean($seo_pick->name);
					$seo_response[$category_seo_key] = $seo_pick;
					$seo_response[$category_seo_key]->slug = $category_seo_key;
					$seo_response[$category_seo_key]->product='';
					$seo_response[$category_seo_key]->sub_category='';

					if(isset($seo_pickup_cat_product)){
						foreach($seo_pickup_cat_product as $k=>$seo_pickup_product){
							if(isset($seo_pickup_product)){
								if(!empty($seo_pickup_product->proname)){
									$category_seo_product_key = $this->clean($seo_pickup_product->proname);

									if (in_array($category_seo_product_key, $pickup_url_key)){
										$i=1;
										while(in_array($category_seo_product_key, $pickup_url_key)){
											$category_seo_product_key = $category_seo_product_key.'-'.$i;
											if($i > 1){
												$tokens = explode('-', $category_seo_product_key);
												array_pop($tokens);
												array_pop($tokens);
												$category_seo_product_key = implode('-', $tokens);
												$category_seo_product_key = $category_seo_product_key.'-'.$i;
											}
											$i++;
										}
										$pickup_url_key[] = $category_seo_product_key;
									}
									else{
										$pickup_url_key[] = $category_seo_product_key;
									}

									if($category_seo_product_key && $seo_pickup_product)
										$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
									$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
								}
							}
						}
					}

					if(isset($seo_pickup_sub_cat_seo)){
						foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat){
							if(isset($seo_pickup_sub_cat)){
								$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
								$category_seo_sub_cat_key = $this->clean($seo_pickup_sub_cat->subname);

								if($category_seo_sub_cat_key && $seo_pickup_sub_cat)
									$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';
							}

							if(isset($seo_pickup_subcat_product)){
								foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product){
									if(isset($seo_pickup_sub_product)){
										if(!empty($seo_pickup_sub_product->proname)){
											$category_seo_sub_cat_product_key = $this->clean($seo_pickup_sub_product->proname);

											if (in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
												$i=1;
												while(in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
													$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
													if($i > 1){
														$tokens = explode('-', $category_seo_sub_cat_product_key);
														array_pop($tokens);
														array_pop($tokens);
														$category_seo_sub_cat_product_key = implode('-', $tokens);
														$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
													}
													$i++;
												}
												$pickup_url_key[] = $category_seo_sub_cat_product_key;
											}
											else{
												$pickup_url_key[] = $category_seo_sub_cat_product_key;
											}

											if($category_seo_sub_cat_product_key && $seo_pickup_sub_product)
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'pickup';
											$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
										}
									}
								}
							}
						}
					}
				}
			}

			$response['pickup']['category'] = $seo_response;

			$fp = fopen(dirname(__FILE__).'/../../../testdrive/shop_js/json/pickup_product_cat_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($response));
			fclose($fp);

			$response = array();

			$pickup_product = $seo_response;
			$pickup_product_id = array();
			$pick_cat = array();
			$searchpickup_product = array();
			foreach($pickup_product as $k=>$val){
				$pick_cat[] = $val->id;
			}

			$all_products = $products;
			foreach($all_products as $k=>$product){
				$categories_id = $product->categories_id;

				if(in_array($categories_id, $pick_cat)){
					if(!in_array($product->id, $pickup_product_id)){
						$pickup_product_id[] = $product->id;
						$searchpickup_product[] = $all_products[$k];
					}
				}
			}

			$response['search_pickup'] = $searchpickup_product;

			$fp = fopen(dirname(__FILE__).'/../../../testdrive/shop_js/json/search_pickup_product_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($response));
			fclose($fp);

			$response = array();

			$seo_response = array();
			$search_products = array();
			$products = array();

			$seo_pickup_data = $responser['delivery'];

			foreach($seo_pickup_data as $key=>$seo_pickup){
				foreach($seo_pickup as $k=>$seo_pick){
					$seo_pickup_cat_product =  $seo_pick->product;
					$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
					$category_seo_key = $this->clean($seo_pick->name);
					$seo_response[$category_seo_key] = $seo_pick;
					$seo_response[$category_seo_key]->slug = $category_seo_key;
					$seo_response[$category_seo_key]->product='';
					$seo_response[$category_seo_key]->sub_category='';

					if(isset($seo_pickup_cat_product)){
						foreach($seo_pickup_cat_product as $k=>$seo_pickup_product){
							if(isset($seo_pickup_product)){
								if(!empty($seo_pickup_product->proname)){
									$category_seo_product_key = $this->clean($seo_pickup_product->proname);

									if (in_array($category_seo_product_key, $pickup_url_key)){
										$i=1;
										while(in_array($category_seo_product_key, $pickup_url_key)){
											$category_seo_product_key = $category_seo_product_key.'-'.$i;
											if($i > 1){
												$tokens = explode('-', $category_seo_product_key);
												array_pop($tokens);
												array_pop($tokens);
												$category_seo_product_key = implode('-', $tokens);
												$category_seo_product_key = $category_seo_product_key.'-'.$i;
											}
											$i++;
										}
										$pickup_url_key[] = $category_seo_product_key;
									}
									else{
										$pickup_url_key[] = $category_seo_product_key;
									}

									if($category_seo_product_key && $seo_pickup_product)
										$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
									$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
									$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
								}
							}
						}
					}

					if(isset($seo_pickup_sub_cat_seo)){
						foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat){
							if(isset($seo_pickup_sub_cat)){
								$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
								$category_seo_sub_cat_key = $this->clean($seo_pickup_sub_cat->subname);

								if($category_seo_sub_cat_key && $seo_pickup_sub_cat)
									$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';
							}

							if(isset($seo_pickup_subcat_product)){
								foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product){
									if(isset($seo_pickup_sub_product)){
										if(!empty($seo_pickup_sub_product->proname)){
											$category_seo_sub_cat_product_key = $this->clean($seo_pickup_sub_product->proname);

											if (in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
												$i=1;
												while(in_array($category_seo_sub_cat_product_key, $pickup_url_key)){
													$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
													if($i > 1){
														$tokens = explode('-', $category_seo_sub_cat_product_key);
														array_pop($tokens);
														array_pop($tokens);
														$category_seo_sub_cat_product_key = implode('-', $tokens);
														$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
													}
													$i++;
												}
												$pickup_url_key[] = $category_seo_sub_cat_product_key;
											}
											else{
												$pickup_url_key[] = $category_seo_sub_cat_product_key;
											}

											if($category_seo_sub_cat_product_key && $seo_pickup_sub_product)
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
											$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'pickup';
											$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
										}
									}
								}
							}
						}
					}
				}
			}

			$response['delivery']['category'] = $seo_response;

			$fp = fopen(dirname(__FILE__).'/../../../testdrive/shop_js/json/delivery_product_cat_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($response));
			fclose($fp);

			$response = array();

			$pickup_product = $seo_response;
			$pickup_product_id = array();
			$pick_cat = array();
			$searchpickup_product = array();

			foreach($pickup_product as $k=>$val){
				$pick_cat[] = $val->id;
			}

			$all_products = $products;
			foreach($all_products as $k=>$product){
				$categories_id = $product->categories_id;

				if(in_array($categories_id, $pick_cat)){
					if(!in_array($product->id, $pickup_product_id)){
						$pickup_product_id[] = $product->id;
						$searchpickup_product[] = $all_products[$k];
					}
				}
			}
			$response['search_delivery'] = $searchpickup_product;

			$fp = fopen(dirname(__FILE__).'/../../../testdrive/shop_js/json/search_delivery_product_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($response));
			fclose($fp);

			$response = array();
		}

		$gene_setting = $this->get_shop_content_all_post( $company_id );
		$fp = fopen(dirname(__FILE__).'/../../../testdrive/shop_js/json/general_settings_'.$company_id.'.json', 'w');
		fwrite($fp, json_encode($gene_setting));
		fclose($fp);
	}

	private function update_files($company_id,$file_arr = array()){
		$this->CI->load->model('MFtp_settings');
		$companies_ftp_details = $this->CI->MFtp_settings->get_ftp_settings( array( 'shop_files_loc <>' => '', 'ftp_hostname <>' => '', 'ftp_username <>' => '', 'ftp_password <>' => '', 'access_permission' => 1 ) , array( 'company_id' => $company_id ) );

		$error = array();

		if( !empty($companies_ftp_details) ){
			$this->CI->load->library('ftp');

			foreach( $companies_ftp_details as $comp){
				$shop_files_loc = $comp->shop_files_loc;
				$ftp_hostname = $comp->ftp_hostname;
				$ftp_username = $comp->ftp_username;
				$ftp_password = $comp->ftp_password;

				$org_json_files = array();
				$updated_file_loc = dirname(__FILE__).'/../../../obs/online-bestellen/';

				$conn_id = ftp_connect( $ftp_hostname ) or die("Couldn't connect to $ftp_server");
				if ( @ftp_login($conn_id, $ftp_username, $ftp_password) ){
					$org_json_files = $file_arr;
				}
				else{
					$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t connect as  ').$ftp_username._(', login failed !');
				}

				if( !empty($org_json_files) )
					ftp_pasv($conn_id, true);
				foreach( $org_json_files as $id=>$imgf ){

					if ( @ftp_put($conn_id, $shop_files_loc.'/shop_js/json/'.$imgf, $updated_file_loc.'json/'.$imgf , FTP_ASCII) ){
					}
					else{
						$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').$imgf._(' on server !');
					}

					unlink('online-bestellen/json/'.$imgf);
				}
			}
		}
		return $error;
	}

	private function update_files_own($file_arr = array()){

		if(!empty($file_arr)){
			foreach ($file_arr as $fn){
				rename(dirname(__FILE__).'/../../../obs/online-bestellen/json/'.$fn, dirname(__FILE__).'/../../../iframe-shops/shop_js/json/'.$fn);
			}
			return true;
		}
		else{
			return false;
		}
	}

	function get_shop_content_all_post($company_id){
		$year = date("Y");
		$month = date("m");

		// Fetching company account type. This is for checking whether company is attached with FoodDESK
		$this->CI->db->select("ac_type_id");
		$company_info = $this->CI->db->get_where('company',array("id" => $company_id))->result();
		$is_fdd_associated = 0;
		if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
			$is_fdd_associated = 1;

		// Fetching Opening hours
		//$this->CI->Mgeneral_settings->get_general_settings();

		$general_settings = $this->get_general_settings($company_id);
		$promocode_settings = $this->get_promocode_settings($company_id);

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

		$custom_opening_hours = $this->get_custom_opening_hours($company_id);

		// Fetching Opening hours
		$sub_admins = array();
		$this->CI->db->select('role');
		$is_super = $this->CI->db->get_where('company', array('id' => $company_id))->result();
		if(!empty($is_super) && $is_super['0']->role == 'super')
			$sub_admins = $this->get_sub_admins($company_id);

		// Fetching Opening hours
		$order_settings = $this->get_order_settings($company_id,$general_settings[0]->calendar_country);
		// Fetching Opening hours
		//$pre_assigned_holidays = $this->CI->get_pre_assigned_holidays($company_id,$year,$month);

		// Fetching meat time setting
		//$meattime_settings=$this->get_meattime_settings($company_id);
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
				'custom_opening_hours'	=> $custom_opening_hours,
				'countries'				=> $countries,
				'sub_admins'			=> $sub_admins,
				'general_settings'		=> $general_settings,
				'meattime_settings'		=> $meattime_settings,
				'promocode_settings'	=> $promocode_settings,
				'order_settings'		=> $order_settings,
				'international'			=> $international,
				'company_countries'		=> $company_countries,
				'company_countries_int'	=> $company_countries_int,
				'all_countries'			=> $all_countries,
				//'pre_assigned_holidays' => $pre_assigned_holidays,
				'days' => $days,
				'cardgate'=>$cardgate
		);

		return $response_arr;
		//$this->CI->response( array('error' => 0, 'message'=> '' , 'data' => $response_arr ), 200 );
	}


	/**
	 * This private function is used to fetch all expected allergence words defined in admin settings
	 *@name get_meattimesettings
	 * @access private
	 * @return array $meattime_settings Array of meattime settings
	 */
	private function get_meattime_settings($comp_id=0)
	{
		$this->CI->db->select('company_id,status,type,slots');
		$meattime_settings=$this->CI->db->get_where('meattime_settings',array('company_id'=>$comp_id))->row_array();
		return $meattime_settings;
	}

	/**
	 * This private function is used to fetch all expected allergence words defined in admin settings
	 * @access private
	 * @return array $allergence_words Array of allergence words
	 */
	private function get_admin_defined_allergence(){
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

	private function cardgate_payment_option($company_id){

		$this->CI->load->model('Mpayment');
		$data = array();

		// Checking for cardgate enable or not
		$merchant_info = $this->CI->db->get_where('cp_merchant_info', array('company_id' => $company_id))->result_array();
		$cardgate_setting = $this->CI->Mpayment->get_cardgate_setting(array('company_id' => $company_id));

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
			$data['sandbox_active'] = $cardgate_setting[0]->sandbox_active;
			$get_banks = 0;
			$merchant_info = $this->CI->Mpayment->get_merchant_info($company_id);

			$payment_methods = $this->CI->Mpayment->get_selected_payment_methods($company_id);
			$i=0;
			$payment_gateway = array();
			foreach($payment_methods as $method){
				$get_info = $this->CI->Mpayment->get_payment_method_info($method);
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
	 * This private function is used to fetch Days
	 * @access private
	 * @return array $days Array of Days
	 */
	private function get_days(){
		$days = $this->CI->db->get('days')->result();
		return $days;
	}

	/**
	 * This private function is used to fetch Countries
	 * @access private
	 * @return array $countries Array of Countries
	 */
	private function get_countries(){
		$this->CI->db->where('id = 21 OR id = 150 OR id = 75');
		$countries = $this->CI->db->get('country')->result();
		return $countries;
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
			$this->CI->db->where( 'company_id', $company_id);
			$order_settings = $this->CI->db->get('order_settings')->result();
			if(!empty($order_settings)){
				$same_day_order_excep = array();
				$same_day_order_excep['allow_order_date']= $order_settings[0]->allow_order_date;
				$same_day_order_excep['date_time_diff_pickup']=$order_settings[0]->date_time_diff_pickup;
				$same_day_order_excep['same_date_start_time_pickup']=$order_settings[0]->same_date_start_time_pickup;
				$same_day_order_excep['same_date_end_time_pickup']=$order_settings[0]->same_date_end_time_pickup;
				$order_settings[0]->holiday_dates = $this->get_company_holiday_dates($company_id, $calendar_country);
				$order_settings[0]->shop_close_dates = $this->get_company_close_dates($company_id);
				$order_settings[0]->custom_order_settings = $this->get_custom_order_settings($company_id);
				$order_settings[0]->current_day_exception = $same_day_order_excep;
			}
		}

		return $order_settings;
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

			$this->CI->db->select('
			company.company_name,
			general_settings.delivery_service,
			general_settings.pickup_service,
			general_settings.company_id,
			general_settings.calendar_country,
			general_settings.activate_discount_card,
			general_settings.discount_card_message,
			general_settings.pay_option,
			general_settings.online_payment,
			general_settings.paypal_address,
			general_settings.apply_tax,
			general_settings.tax_percentage,
			general_settings.tax_amount,
			general_settings.minimum_amount_paypal,
			general_settings.disc_per_amount,
			general_settings.disc_after_amount,
			general_settings.disc_percent,
			general_settings.disc_price,
			general_settings.hide_availability,
			general_settings.order_timing_info,
			order_settings.*
			');
			$this->CI->db->join('order_settings','order_settings.company_id = company.id');
			$this->CI->db->join('general_settings','general_settings.company_id = company.id');
			$this->CI->db->where( $where_arr );
			$sub_admins = $this->CI->db->get('company')->result();
			if(!empty($sub_admins)){
				foreach ($sub_admins as $key => $sub_admin){
					$sub_admins[$key]->holiday_dates = $this->get_company_holiday_dates($sub_admin->company_id, $sub_admin->calendar_country);
					$sub_admins[$key]->shop_close_dates = $this->get_company_close_dates($sub_admin->company_id);
					$sub_admins[$key]->delivery_areas = $this->get_delivery_areas($sub_admin->company_id);
					$sub_admins[$key]->delivery_settings = $this->get_delivery_settings($sub_admin->company_id);
					$sub_admins[$key]->opening_hours =  $this->get_opening_hours($sub_admin->company_id);

					if(!empty($sub_admins[$key]->delivery_settings)){
						if($sub_admins[$key]->delivery_settings[0]->type == 'international'){
							$sub_admins[$key]->international = $this->get_international_settings($sub_admin->company_id);

							//Fetching international countries delivery settings
							$sub_admins[$key]->company_countries = $this->get_company_countries($sub_admin->company_id);

							//Fetching international countries delivery costs
							$sub_admins[$key]->company_countries_int = $this->get_company_countries_int($sub_admin->company_id);
							$sub_admins[$key]->all_countries = $this->get_all_countries();
							$sub_admins[$key]->countries = $this->get_countries();
						}
					}
				}
			}
		}

		return $sub_admins;
	}

	/**
	 * This private function is used to fetch company closing dates
	 * @access private
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
	 * This private function is used to fetch company closing dates
	 * @access private
	 */
	function get_custom_order_settings($company_id){
		$custom_order_settings = array();
		if($company_id){
			$this->CI->db->select('custom_days_pickup,custom_date_time_pickup,custom_date_days_pickup');
			$this->CI->db->where('company_id', $company_id);
			$custom_order_settings = $this->CI->db->get('custom_order_settings')->result();
		}
		return $custom_order_settings;
	}


	/**
	 * This private function is used to fetch company holidays (including pre-assigned holidays by MCP)
	 * @access private
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
	 * This private function is used to fetch Opening Hours
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $opening_hours Array of Opening hours  (pickup and delivery)
	 */
	private function get_opening_hours($company_id = null){
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
	 * This private function is used to fetch Custom Opening Hours
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $custom_opening_hours Array of Custom Opening hours  (pickup)
	 */
	private function get_custom_opening_hours($company_id = null){
		$custom_opening_hours = array();

		if($company_id && is_numeric($company_id)){
			$this->CI->db->where('company_id', $company_id);
			$custom_opening_hours = $this->CI->db->get('custom_pickup_timing')->result();
		}

		return $custom_opening_hours;
	}

	private function get_company_countries($company_id){

		$companies_country_int = array();
		$this->CI->db->select('company_countries.company_id,company_countries.country_id,country.country_name');
		$this->CI->db->where('company_id',$company_id);
		$this->CI->db->join('country','country.id =  company_countries.country_id','left');
		$companies_country_int = $this->CI->db->get(' company_countries')->result();

		return $companies_country_int;
	}

	private function get_company_countries_int($company_id){

		$companies_country_int = array();
		$this->CI->db->order_by('company_countries_int.lower_range','asc');
		$this->CI->db->order_by('company_countries_int.upper_range','asc');
		$this->CI->db->where('company_id',$company_id);
		$this->CI->db->join('country','country.id =  company_countries_int.country_id','left');
		$companies_country_int = $this->CI->db->get(' company_countries_int')->result();

		return $companies_country_int;
	}

	private function get_all_countries(){
		$this->CI->db->select('id as country_id,country_name');
		$all_countries = $this->CI->db->get('country')->result();
		return $all_countries;
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
// 			$this->CI->db->select('country.country_name, company_countries.country_id, company_countries.country_cost');
// 			$this->CI->db->join('country', 'country.id = company_countries.country_id');
// 			$this->CI->db->where('company_id',$company_id);
// 			$international_areas = $this->CI->db->get('company_countries')->result();
			$this->CI->db->select('country.country_name, company_countries_int.country_id');
			$this->CI->db->join('country', 'country.id = company_countries_int.country_id','left');
			$this->CI->db->where('company_id',$company_id);
			$this->CI->db->distinct('country_id');
			$international_areas = $this->CI->db->get('company_countries_int')->result();
		}

		return $international_areas;
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
			$this->CI->db->select('country.id, country.country_name');
			$this->CI->db->join('country', 'company_delivery_areas.country_id = country.id');
			$this->CI->db->group_by('company_delivery_areas.country_id');
			$this->CI->db->where('company_delivery_areas.company_id',$company_id);
			$countries = $this->CI->db->get('company_delivery_areas')->result();

			// Fetching Provinces
			$provinces = array();
			$this->CI->db->distinct();
			$this->CI->db->select('`company_delivery_areas`.`state_id`, `company_delivery_areas`.`company_id`, `states`.`state_name`, `states`.`country_id`');
			$this->CI->db->join('states','states.state_id = company_delivery_areas.state_id' );
			//$this->CI->db->group_by('company_delivery_areas.state_id');
			$this->CI->db->where('company_delivery_areas.company_id',$company_id);
			$provinces = $this->CI->db->get('company_delivery_areas')->result();

			// Fetching Cities
			$cities = array();
			$this->CI->db->select('postcodes.*');
			$this->CI->db->join('postcodes','postcodes.id = company_delivery_areas.postcode_id' );
			$this->CI->db->where('company_delivery_areas.company_id',$company_id);
			$cities = $this->CI->db->get('company_delivery_areas')->result();

			$delivery_areas = array( 'countries' => $countries, 'provinces' => $provinces, 'cities' => $cities);
		}

		return $delivery_areas;
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
			$this->CI->db->select( '
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
								amt_row_page,
								promocode,
								promocode_text,
								promocode_percent,
								promocode_price,
								promocode_start,
								promocode_end,
								introcode,
								introcode_text,
								introcode_percent,
								introcode_price,
								mobile_req,
								show_revocation,
								stock_show
							' );

			$this->CI->db->where( 'general_settings.company_id', $company_id);
			$this->CI->db->where_in( 'company.ac_type_id', array(3,5,6));//3-Pro,5-FDD Pro,6-FDD Premium
			$this->CI->db->where('company.ingredient_system','0');
			$this->CI->db->join('company', 'company.id = general_settings.company_id');

			$general_settings = $this->CI->db->get('general_settings')->result();
		}

		return $general_settings;
	}

	/**
	 * This private function is used to fetch Promocode Settings
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $promocode_settings Array of General Settings
	 */
	private function get_promocode_settings($company_id = null){

		$promocode_settings = array();

		if($company_id){
			$this->CI->db->where( 'company_id', $company_id);
			$promocode_settings = $this->CI->db->get('promocode')->result();
		}

		return $promocode_settings;
	}

	/**
	 * get all product
	 * @param number $company_id
	 * @return array
	 */
	function get_shop_content_json_new($company_id){
		$service_types = array('pickup', 'delivery');
		$data = array();

		$this->CI->db->select("ac_type_id");
		$company_info = $this->CI->db->get_where('company',array("id" => $company_id))->result();
		$is_fdd_associated = 0;
		if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
			$is_fdd_associated = 1;

		foreach($service_types as $service_type){
			$categories = $this->get_categories_json($company_id, $service_type);

			$categories = (array)$categories;
			foreach($categories as $k=>$category){
				$category = (array)$category;
				$categories_id = $category['id'];
				$subcategories_id = -1;

				$subcats = $this->get_subcategories_json($categories_id);
				$categories[$k]->sub_category = $subcats;

				$categories[$k]->product = $this->get_category_products_json($company_id, $categories_id, $subcategories_id,$is_fdd_associated);

				if(is_array($subcats)){
				foreach($subcats as $key=>$subcat){
					$subcategories_id = $subcat->id;
					$subcats[$key]->product = $this->get_category_products_json($company_id, $categories_id, $subcategories_id,$is_fdd_associated);
				}
				}
				$categories[$k]->sub_category = $subcats;
			}
			$result['category'] = $categories;

			$data[$service_type] = $result;
		}
		return $data;
	}

	function get_categories_json($company_id, $service_type){
		$query = '';

		$query = " Select * FROM `categories` WHERE ( `company_id` = ".$company_id." AND `status` = 1 ) ";

		if( !$service_type || $service_type == 'both' ){
		}
		elseif( $service_type == 'pickup' ){
			$query .= " AND ( `service_type` = '1' OR `service_type` = '0' ) ";
		}
		elseif( $service_type == 'delivery' ){
			$query .= " AND ( `service_type` = '2' OR `service_type` = '0' ) ";
		}

		$query .= " ORDER BY `order_display` ASC ";

		$categories = $this->CI->db->query( $query )->result();

		if( !empty($categories) ){
			$categories = $this->check_category_image_exist($categories);
			foreach($categories as $category)
			{
				$new_categories[$category->id] = $category;
			}
			return $new_categories;
		}
		else{
			return null;
		}
		exit;
	}

	function get_subcategories_json($categories_id){
		$this->CI->db->where( 'categories_id', $categories_id );
		$this->CI->db->where( 'status', 1 );
		$this->CI->db->order_by( 'suborder_display', 'ASC' );

		$subcategories = $this->CI->db->get('subcategories')->result();

		if( !empty($subcategories) ){
			$subcategories = $this->check_subcategory_image_exist($subcategories);
			foreach($subcategories as $subcategory){
				$new_subcategories[$subcategory->id] = $subcategory;
			}
			return $new_subcategories;
		}
		else{
			return null;
		}
	}

	/**
	 * This function is used to check whether product image saved in database is actually exists in desired location
	 * @param string $image image name
	 * @return string $final_image Final image name if exist otherwise null.
	 */
	private function existing_product_image($image = ''){

		$final_image = '';
		if($image != ''){
			$path = dirname(__FILE__)."/../../../obs/assets/cp/images/product/";
			if(@file_exists($path.$image))
				$final_image = $image;
		}
		return $final_image;
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
	private function get_k_allergence($product_id = 0,$lang = '_dch', $fdd_pro_quant = array()){
		$fdd_pro_quant 	= array_column( $fdd_pro_quant, 'fdd_pro_id' );
		$allergence 	= array();

		if( $product_id && !empty( $fdd_pro_quant ) ){
			$this->CI->fdb->select( 'a_id as ka_id, allergence.all_name'.$lang.' as ka_name' );
			$this->CI->fdb->join('allergence', 'allergence.all_id = prod_allergence.a_id' );
			$this->CI->fdb->where_in( 'prod_allergence.p_id', $fdd_pro_quant );
			$this->CI->fdb->order_by( 'prod_allergence.order', 'ASC' );
			$this->CI->fdb->group_by( 'prod_allergence.a_id' );
			$allergence = $this->CI->fdb->get( 'prod_allergence' )->result();
		}
		return $allergence;
	}

	/**
	 * This private function is used to fetch Keurslager Sub Allergence related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which sub allergence have to be fetched
	 * @return array $traces It is the array if sub allergence associated with the given product
	 */
	private function get_k_sub_allergence($product_id = 0,$lang = '_dch',$fdd_pro_quant = array()){
		$fdd_pro_quant 	= array_column( $fdd_pro_quant, 'fdd_pro_id' );
		$allergence 	= array();

		if( $product_id && !empty( $fdd_pro_quant ) ){
			$this->CI->fdb->select( 'a_id as sub_ka_id, parent_all_id as parent_ka_id,sub_allergence.all_name'.$lang.' as sub_ka_name' );
			$this->CI->fdb->join( 'sub_allergence', 'sub_allergence.all_id = prod_sub_allergence.a_id' );
			$this->CI->fdb->where_in( 'prod_sub_allergence.p_id', $fdd_pro_quant );
			$this->CI->fdb->order_by( 'prod_sub_allergence.order', 'ASC' );
			$this->CI->fdb->group_by('prod_sub_allergence.a_id');
			$allergence = $this->CI->fdb->get( 'prod_sub_allergence' )->result();
		}
		return $allergence;
	}

	function get_category_products_json($company_id, $categories_id, $subcategories_id,$is_k_assoc){

		$this->CI->db->select('language_id,display_fixed');
		$display_fixed_result = $this->CI->db->get_where('general_settings', array('company_id' => $company_id))->result();
		/**
		 * Get language
		 */
		$lang_id = '_dch';
		if( $display_fixed_result[0]->language_id == '1' ){
			$lang_id = '_dch';
		}elseif( $display_fixed_result[0]->language_id == '2' ){
			$lang_id = '_dch';
		}else{
			$lang_id = '_fr';
		}
		$this->CI->db->where( 'company_id', $company_id );
		$this->CI->db->where( 'categories_id', $categories_id );
		$this->CI->db->where( 'subcategories_id', $subcategories_id );
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->CI->db->where($where);
		$this->CI->db->where( 'status', 1 );
		$this->CI->db->order_by('pro_display','asc');
		$this->CI->db->order_by('id','asc');

		$products = $this->CI->db->get_where('products', array('sell_product_option !=' => '' ))->result();

		if(!empty($products)){
			foreach ($products as $key => $product){
				if($is_k_assoc){
					$complete = $this->get_fixed_status($product->id,$product->direct_kcp);

					if($display_fixed_result[0]->display_fixed){
						if(!$complete){
							unset($products[$key]);
							continue;
						}
					}
					$product->complete = $complete;
				}
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
					if($complete){
						$this->CI->db->distinct( 'fdd_pro_id' );
						$this->CI->db->select( 'fdd_pro_id' );
						$this->CI->db->where(array('obs_pro_id'=>$product->id));
						$fdd_pro_quant = $this->CI->db->get('fdd_pro_quantity')->result_array();

						$k_allergence = $this->get_k_allergence($product->id,$lang_id,$fdd_pro_quant);
						$k_sub_allergence = $this->get_k_sub_allergence($product->id,$lang_id,$fdd_pro_quant);
						$all = '';
						if(!empty($k_allergence)){
							foreach ($k_allergence as $allergence){
								$all_name = $this->get_all_name( $allergence->ka_id, $lang_id );
								foreach ($all_name as $va ) {
									$all .= $va;
								}

								if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
									$a1 = '';
									if(!empty($k_sub_allergence)){
										$a1 .= ' (';
										foreach ($k_sub_allergence as $sub_allergence){
											if($sub_allergence->parent_ka_id == $allergence->ka_id){
												$sub_all_name = $this->get_sub_all_name( $sub_allergence->sub_ka_id, $lang_id );
												foreach ($sub_all_name as $vs ) {
													$a1 .= $vs.', ';
												}
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
							$all = substr($all, 0, -2);
							$product->allergence = $all;
						} else {
							$product->allergence = '';
						}
					}
				}
			}
		}
		$category_products = $products;

		$sorted_p 	= array();
		$unsorted_p = array();
		if(isset($category_products)){
			foreach($category_products as $prod) {
				if($prod->pro_display != 0){
					$sorted_p[] = $prod;
				}else{
					$unsorted_p[] = $prod;
				}
			}
			$category_products = array_merge($sorted_p,$unsorted_p);
		}
		
		if( !empty($category_products) ){
			$category_products = $this->check_products_image_exist($category_products);
			foreach($category_products as $category_product){
				$category_product->grp_arr = $this->get_product_groups($category_product->id);
				$new_category_products[$category_product->id] = $category_product;
			}
			return $new_category_products;
		}
		else{
			return null;
		}
	}

	private function get_fixed_status($id = 0, $direct_kcp = 0){
		$complete = 1;
		if($id){
			if($direct_kcp == 1){
				$this->CI->db->where(array('obs_pro_id'=>$id,'is_obs_product'=>0));
				$result = $this->CI->db->get('fdd_pro_quantity')->result_array();
				if(empty($result)){
					$complete = 0;
				}
			}
			else{
				$this->CI->db->where(array('obs_pro_id'=>$id));
				$result_custom = $this->CI->db->get('fdd_pro_quantity')->result_array();
				if(!empty($result_custom)){
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
				}
				else{
					$complete = 0;
				}
			}
		}
		return $complete;
	}

	private function check_products_image_exist($checking_array){
		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->image){
					$path = dirname(__FILE__)."/../../../obs/assets/cp/images/product/";
					if(!file_exists($path.$items->image))
						$checking_array[$key]->image = '';
				}
			}
		}
		return $checking_array;
	}

	/**
	 * This private function is used to fetch extras of given product
	 * @access private
	 * @param integer $product_id Product ID
	 * @return array $grps_arr Array of extras
	 */
	private function get_product_groups( $product_id = null){
		$grps_arr = array();
		if($product_id && is_numeric($product_id)){
			$this->CI->db->where( 'groups_products.products_id', $product_id );
			$this->CI->db->join('groups', 'groups.id = groups_products.groups_id');
			$this->CI->db->order_by( 'groups.display_order', 'ASC' );
			$this->CI->db->order_by( 'groups_products.display_order', 'ASC' );
			$this->CI->db->order_by( 'groups_products.type', 'ASC' );

			$product_grps = $this->CI->db->get('groups_products')->result();
			//$this->CI->response($product_grps);
			if( !empty($product_grps) ){
				$hold_grp_id = array();
				$index = -1;

				foreach( $product_grps as $grp ){
					//if( $hold_grp_id != $grp->groups_id )
					if(!in_array($grp->groups_id,$hold_grp_id)){
						$hold_grp_id[] = $grp->groups_id;
						//$index = $index+1;

						//$grps_arr[$index] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'required' => $grp->required, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );
						$grps_arr[] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'required' => $grp->required, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );

					}
					elseif( in_array($grp->groups_id, $hold_grp_id) ){
						$key = array_search($grp->groups_id,$hold_grp_id);
						$grps_arr[$key]['attributes_arr'][] = array( $grp->attribute_name, $grp->attribute_value );
					}
				}
			}
		}

		return $grps_arr;
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
					$path = dirname(__FILE__)."/../../../obs/";
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
					$path = dirname(__FILE__)."/../../../obs/";
					if(!file_exists($path.$items->subimage))
						$checking_array[$key]->subimage = '';
				}
			}
		}
		return $checking_array;
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
			/*$this->CI->db->select('prefix,ki_name');
			 $this->CI->db->order_by('kp_display_order', 'ASC');
			$this->CI->db->order_by('display_order', 'ASC');
			$ingredients = $this->CI->db->get_where('products_ingredients', array('product_id' => $product_id))->result();*/
			$ingredients = $this->CI->db->query(
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
			$this->CI->db->select('prefix,kt_name');
			$this->CI->db->order_by('display_order', 'ASC');
			$this->CI->db->group_by('kt_id');
			$traces = $this->CI->db->get_where('products_traces', array('product_id' => $product_id))->result();
		}
		return $traces;
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

			$this->CI->db->where('company_id',$company_id);
			$delivery_settings = $this->CI->db->get('company_delivery_settings')->result();
		}

		return $delivery_settings;
	}

	function get_all_name( $ka_id = 0, $sel_lang = '_dch', $all = '' ){
		if( $all == 'all' ){
			$this->CI->db->select( 'all_name, all_name_fr, all_name_dch' );
		}
		else{
			$all_name = 'all_name'.$sel_lang;
			$this->CI->db->select( $all_name );
		}
		return $this->CI->db->get_where( 'allergence', array( 'all_id' => $ka_id ) )->row_array();
	}

	function get_sub_all_name( $ka_id = 0, $sel_lang = '_dch', $all = '' ){
		if( $all == 'all' ){
			$this->CI->db->select( 'all_name, all_name_fr, all_name_dch' );
		}
		else{
			$all_name = 'all_name'.$sel_lang;
			$this->CI->db->select( $all_name );	
		}
		return $this->CI->db->get_where( 'sub_allergence', array( 'all_id' => $ka_id ) )->row_array();
	}
}
/* End of file shop_all.php */
/* Location: ./application/controllers/cp/shop_all.php */
