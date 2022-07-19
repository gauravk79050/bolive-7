<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Excel_import extends CI_Controller
{
	var $tempUrl = '';
	var $template = '';
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->helper('phpmailer');
		$this->load->helper('cookie');
		
		$this->load->library('ftp');
		//$this->load->library('email');
		$this->load->library('session');
		$this->load->library('messages');
		$this->load->library('utilities');
		$this->load->library('pagination');
		$this->load->library('form_validation');
		
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mpackages');
		$this->load->model('Mcategories');
		$this->load->model('Mcalender');
		$this->load->model('Mclients');
		//$this->load->model('mcp/Mcompanies');
		$this->load->model('mcp/Mcompanies');
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		$this->rows_per_page = 20;
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		
		
		
		$this->load->library('Messages');
			
		//$this->tempUrl = base_url().'application/views/cp';
		//$this->template = "/cp";
		
		
		
		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');
		
		if( !$current_user || !$is_logged_in )
		  redirect('cp/login','refresh');
	}
	
	function index()
	{
		if( $this->input->post('company_id') )
		{

			//echo $_COOKIE['locale'];
			$company = $this->Mcompany->get_company();
			if( !empty($company) )
			{
				$company = $company[0];
				$ac_type_id = $company->ac_type_id;
					
				//$this->load->model('mcp/Mcompanies');
				//$account_type = $this->Mcompanies->get_account_types( array('id'=>$ac_type_id) );
					
				if($ac_type_id == 1)
					$this->session->set_userdata('menu_type','free');
				elseif($ac_type_id == 2)
				$this->session->set_userdata('menu_type','basic');
				elseif($ac_type_id == 3)
				$this->session->set_userdata('menu_type','pro');
			}
			
			if( $this->company_role == 'master' || $this->company_role == 'super' )
			{
					
				$general_settings = $this->Mgeneral_settings->get_general_settings();
				if($general_settings != array() && $general_settings[0]->hide_intro == '1'){
					$this->session->set_userdata('show_hide',true);
			
					if( $this->company_role == 'master' || $this->company_role == 'sub' )
						$this->orders();
					else
						$this->categories();
			
				}else{
					$this->session->set_userdata('show_hide',false);
					$this->intro();
				}
			}
			elseif( $this->company_role == 'sub')
			{
				$this->session->set_userdata('show_hide',true);
				$this->orders();
			}
			
			
		}
		    $company_id = $this->input->post('company_id');
			
			if( $_FILES['upload_excel']['name'] )
			{
				$upload_dir = dirname(__FILE__).'/excel-import/';
				$file_name = $_FILES['upload_excel']['name'];
				$tmp_name = $_FILES['upload_excel']['tmp_name'];
				$file_ext = end( explode('.',$file_name) );
				
				if( strtolower($file_ext) == 'xls' || strtolower($file_ext) == 'xlsx' )
				{
					if( move_uploaded_file( $tmp_name, $upload_dir.$file_name ) )
					{						
						$this->load->library('excel');
						
						$inputFileName = $upload_dir.$file_name;
						$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
						
						//$objPHPExcel->getActiveSheet();
						
						$i = 0;
						$data_arr = array();
						
						foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row)
						{
						 	 //print_r( $row );
							 $cellIterator = $row->getCellIterator();
						     $cellIterator->setIterateOnlyExistingCells(false); 
						     $j = 0;
							 
							 foreach ($cellIterator as $cell)
							 {
								$data_arr[$i][$j] = $cell->getValue();
								$j++;
						     }
						     
							 $i++;
						}
						
						//print_r( $data_arr );
						//die();
						
						if( !empty($data_arr) )
						{
						    if( $data_arr[0][0] == 'Category Name' &&  $data_arr[0][1] == 'Subcategory Name' )
							{
							   if( count($data_arr) != 1 )
							   for( $i = 1; $i < count($data_arr); $i++ )
							   {
							   		$category_name = $data_arr[$i][0];  // categories_id
									$subcategory_name = $data_arr[$i][1];    // subcategories_id
									$pro_art_num = $data_arr[$i][2];
									$proname = $data_arr[$i][3];
									$prodescription = $data_arr[$i][4];
									$image_url = $data_arr[$i][5];            // image
									$sell_product_option = $data_arr[$i][6];
								    $price_per_unit = $data_arr[$i][7]; 
									$price_weight = $data_arr[$i][8];
									$price_per_person = $data_arr[$i][9];
									$min_amount = $data_arr[$i][10];
									$max_amount = $data_arr[$i][11];
									$type = $data_arr[$i][12];
									$discount = $data_arr[$i][13];
									$discount_wt = $data_arr[$i][14];
									$discount_person = $data_arr[$i][15];
									$pro_display = $data_arr[$i][16];
									$image_display = $data_arr[$i][17];
									$status = $data_arr[$i][18];
									$allday_availability = $data_arr[$i][19];
									$availability = $data_arr[$i][20];
									$advance_payment = $data_arr[$i][21];
									$allow_upload_image = $data_arr[$i][22];
									$unit_groups = $data_arr[$i][23];
									$weight_groups = $data_arr[$i][24];
									$person_groups = $data_arr[$i][25];
									
									// -------- >>> Process Data & Insert  <<< ------ //
									
									/* ====== Category Set ====== */
									
									$categories_id = 0;
									if( $category_name )
									{
										$this->load->model('Mcategories');
										$category = $this->Mcategories->get_category( array('company_id'=>$company_id,'name'=>$category_name) );
										
										if( empty($category) )
										{
											$insert_arr = array( 'company_id'=>$company_id,
																 'name'=>$category_name,
																 'created'=>date('Y-m-d',time())
															   );
											$new_category_id = $this->Mcategories->insert_category( $insert_arr );
											
											if( $new_category_id )
											  $categories_id = $new_category_id;
										}
										else
										{
											$category = $category[0];
											$categories_id = $category->id;
										}
									}
									//echo $categories_id.'<br />';
									
									/* ====== Subcategory Set ====== */
									
									$subcategories_id = 0;
									if( $categories_id && $subcategory_name )
									{
										$this->load->model('Msubcategories');
										$subcategory = $this->Msubcategories->get_subcategory( array('categories_id'=>$categories_id,'subname'=>$subcategory_name) );
										
										if( empty($subcategory) )
										{
											$insert_arr = array(	 'categories_id'=>$categories_id,
																	 'subname'=>$subcategory_name,
																	 'subcreated'=>date('Y-m-d',time())
																);
											$new_subcategory_id = $this->Msubcategories->insert_subcategory( $insert_arr );
											
											if( $new_subcategory_id )
											  $subcategories_id = $new_subcategory_id;
										}
										else
										{
											$subcategory = $subcategory[0];
											$subcategories_id = $subcategory->id;
										}
									}
									//echo $subcategories_id.'<br />';
									
									/* ====== Product Image Set ====== */
									
									$image = '';
									
									if( $proname && $image_url )
									{
										$img_extension = end( explode('.',$image_url) );
										$img_ext = strtolower($img_extension);
										
										if( $img_ext == 'jpg' || $img_ext == 'jpeg' || $img_ext == 'png' || $img_ext == 'gif' || $img_ext == 'bmp' )
										{
											$file_name = $this->create_slug($proname).'-'.time().'.'.$img_extension;					
											$upload_path = dirname(__FILE__).'/../../../assets/cp/images/product/'.$file_name;
																				
											$hold_image = file_get_contents($image_url);
											if( file_put_contents( $upload_path, $hold_image ) )
											{
												$image = $file_name;
											}
										}
									}									
									//echo $image.'<br />';
									
									/* ====== Form Product Array & Insert ====== */
									
									$products_id = 0;
									
									if( $company_id && $categories_id && $proname && $sell_product_option  )
									{
										$unit_discount = 0;
										if( $discount != 0 && ( is_int($discount) || is_float($discount) ) )
										{
											$unit_discount = $discount;
										}
										elseif( is_string($discount) )
										{
											$discount_arr = explode( '##', $discount );
											
											if( !empty($discount_arr) )
											{
												$unit_discount = 'multi';
											}
											else
											  $unit_discount = 0;
										}
										
										$wt_discount = 0;
										if( $discount_wt != 0 && ( is_int($discount_wt) || is_float($discount_wt) ) )
										{
											$wt_discount = $discount_wt;
										}
										elseif( is_string($discount_wt) )
										{
											$discount_arr = explode( '##', $discount_wt );
											
											if( !empty($discount_arr) )
											{
												$wt_discount = 'multi';
											}
											else
											  $wt_discount = 0;
										}
										
										$person_discount = 0;
										if( $discount_person != 0 && ( is_int($discount_person) || is_float($discount_person) ) )
										{
											$person_discount = $discount_person;
										}
										elseif( is_string($discount_person) )
										{
											$discount_arr = explode( '##', $discount_person );
											
											if( !empty($discount_arr) )
											{
												$person_discount = 'multi';
											}
											else
											  $person_discount = 0;
										}
										
										$product_arr = array(
																'company_id' => $company_id,
																'categories_id' => $categories_id,
																'subcategories_id' => $subcategories_id,
																'pro_art_num' => $pro_art_num,
																'proname' => $proname,
																'prodescription' => $prodescription,
																'image' => $image,
																'sell_product_option' => $sell_product_option,
																'price_per_unit' => ($price_per_unit)?$price_per_unit:0,
																'price_weight' => ($price_weight)?($price_weight/1000):0,
																'weight_unit' => 'gm',
																'price_per_person' => ($price_per_person)?$price_per_person:0,
																'min_amount' => $min_amount,
																'max_amount' => $max_amount,
																'type' => strval($type),
																'discount' => $unit_discount,
																'discount_wt' => $wt_discount,
																'discount_person' => $person_discount,
																'pro_display' => $pro_display,
																'image_display' => strval($image_display),
																'procreated' => date( 'Y-m-d', time() ),
																'proupdated' => date( 'Y-m-d', time() ),
																'status' => $status,
																'allday_availability' => strval($allday_availability),
																'availability' => $availability,
																'advance_payment' => $advance_payment,
																'allow_upload_image' => strval($allow_upload_image)
															);
										
										$this->load->model('Mproducts');					
										$products_id = $this->Mproducts->insert_product( $product_arr );
										
										if( $products_id )
										{
											/* ====== Product Discounts ====== */
									
											$multi_discounts_arr = array(); // products_id
											
											if( $sell_product_option == 'per_unit' || $sell_product_option == 'client_may_choose' )
											{
												if( $discount != 0 && ( is_int($discount) || is_float($discount) ) )
												{
												}
												elseif( is_string($discount) )
												{
													$discount_arr = explode( '##', $discount );
													$discount = 'multi';
													
													if( !empty($discount_arr) )
													{
														foreach( $discount_arr as $da )
														{
															$da = explode( '::', $da );
															
															if( isset($da[0]) && isset($da[1]) && isset($da[2]) )
															$multi_discounts_arr[] = array(
																							 'products_id' => $products_id,
																							 'quantity' => $da[0],
																							 'discount_per_qty' => $da[1],
																							 'price_per_qty' => $da[2],
																							 'type' => 0
																						  );
														}
													}
													else
													  $discount = 0;
												}
											}
											
											if( $sell_product_option == 'weight_wise' || $sell_product_option == 'client_may_choose' )
											{										
												if( $discount_wt != 0 && ( is_int($discount_wt) || is_float($discount_wt) ) )
												{
												}
												elseif( is_string($discount_wt) )
												{
													$discount_arr = explode( '##', $discount_wt );
													$discount_wt = 'multi';
													
													if( !empty($discount_arr) )
													{
														foreach( $discount_arr as $da )
														{
															$da = explode( '::', $da );
															
															if( isset($da[0]) && isset($da[1]) && isset($da[2]) )
															$multi_discounts_arr[] = array(
																							 'products_id' => $products_id,
																							 'quantity' => $da[0],
																							 'discount_per_qty' => $da[1],
																							 'price_per_qty' => $da[2],
																							 'type' => 1
																						  );
														}
													}
													else
													  $discount_wt = 0;
												}
											}
											
											if( $sell_product_option == 'per_person' )
											{
												if( $discount_person != 0 && ( is_int($discount_person) || is_float($discount_person) ) )
												{
												}
												elseif( is_string($discount_person) )
												{
													$discount_arr = explode( '##', $discount_person );
													$discount_person = 'multi';
													
													if( !empty($discount_arr) )
													{
														foreach( $discount_arr as $da )
														{
															$da = explode( '::', $da );
															
															if( isset($da[0]) && isset($da[1]) && isset($da[2]) )
															$multi_discounts_arr[] = array(
																							 'products_id' => $products_id,
																							 'quantity' => $da[0],
																							 'discount_per_qty' => $da[1],
																							 'price_per_qty' => $da[2],
																							 'type' => 2
																						  );
														}
													}
													else
													  $discount_person = 0;
												}
											}
											
											//echo $discount.' - '.$discount_wt.' - '.$discount_person.'<br />';
											//print_r( $multi_discounts_arr );
											
											if( !empty($multi_discounts_arr) )
											{
												$this->load->model('Mproduct_discount');
												foreach( $multi_discounts_arr as $dis_arr )
												{
													$this->Mproduct_discount->insert_product_discounts( $dis_arr );
												}
											}
											
											/* ====== Product Groups ====== */
											
											$groups_arr = array();  //products_id
											$this->load->model('Mgroups');
											
											if( $unit_groups && ( $sell_product_option == 'per_unit' || $sell_product_option == 'client_may_choose' ) )
											{										
												$grp_arr = explode( '#', $unit_groups );
												
												if( !empty($grp_arr) )
												{
													foreach( $grp_arr as $grp )
													{
														$grp = explode( '_', $grp );
														
														if( isset($grp[0]) && isset($grp[1]) && isset($grp[2]) )
														{
															$group_id = 0;
															$group_name = $grp[0];
															$attribute_name = $grp[1];
															$attribute_value = $grp[2];
															
															$group = $this->Mgroups->get_groups( array('company_id' => $company_id, 'group_name' => $group_name, 'type' => 0) );
															if( !empty($group) )
															{
																$group_id = $group[0]->id;
															}
															else
															{
																$where_arr = array('company_id' => $company_id, 'group_name' => '', 'type' => 0);
																$update_arr = array( 'company_id' => $company_id, 'group_name' => $group_name, 'type' => 0 );
																$group_id = $this->Mgroups->for_insert_update_group( $where_arr, $update_arr );
															}
															
															if( $group_id )
															{
																$groups_arr[] = array(
																						 'products_id' => $products_id,
																						 'groups_id' => $group_id,
																						 'attribute_name' => $attribute_name,
																						 'attribute_value' => $attribute_value,
																						 'multiselect' => 0,
																						 'type' => 0
																					  );
															}
														}
													}
												}
											}
											
											if( $weight_groups && ( $sell_product_option == 'weight_wise' || $sell_product_option == 'client_may_choose' ) )
											{
												$grp_arr = explode( '#', $weight_groups );
												
												if( !empty($grp_arr) )
												{
													foreach( $grp_arr as $grp )
													{
														$grp = explode( '_', $grp );
														
														if( isset($grp[0]) && isset($grp[1]) && isset($grp[2]) )
														{
															$group_id = 0;
															$group_name = $grp[0];
															$attribute_name = $grp[1];
															$attribute_value = $grp[2];
															
															$group = $this->Mgroups->get_groups( array('company_id' => $company_id, 'group_name' => $group_name, 'type' => 1) );
															if( !empty($group) )
															{
																$group_id = $group[0]->id;
															}
															else
															{
																$where_arr = array('company_id' => $company_id, 'group_name' => '', 'type' => 1);
																$update_arr = array( 'company_id' => $company_id, 'group_name' => $group_name, 'type' => 1 );
																$group_id = $this->Mgroups->for_insert_update_group( $where_arr, $update_arr );
															}
															
															if( $group_id )
															{
																$groups_arr[] = array(
																						 'products_id' => $products_id,
																						 'groups_id' => $group_id,
																						 'attribute_name' => $attribute_name,
																						 'attribute_value' => $attribute_value,
																						 'multiselect' => 0,
																						 'type' => 1
																					  );
															}
														}
													}
												}
											}
											
											if( $person_groups && $sell_product_option == 'per_person' )
											{
												$grp_arr = explode( '#', $person_groups );
												
												if( !empty($grp_arr) )
												{
													foreach( $grp_arr as $grp )
													{
														$grp = explode( '_', $grp );
														
														if( isset($grp[0]) && isset($grp[1]) && isset($grp[2]) )
														{
															$group_id = 0;
															$group_name = $grp[0];
															$attribute_name = $grp[1];
															$attribute_value = $grp[2];
															
															$group = $this->Mgroups->get_groups( array('company_id' => $company_id, 'group_name' => $group_name, 'type' => 2) );
															if( !empty($group) )
															{
																$group_id = $group[0]->id;
															}
															else
															{
																$where_arr = array('company_id' => $company_id, 'group_name' => '', 'type' => 2);
																$update_arr = array( 'company_id' => $company_id, 'group_name' => $group_name, 'type' => 2 );
																$group_id = $this->Mgroups->for_insert_update_group( $where_arr, $update_arr );
															}
															
															if( $group_id )
															{
																$groups_arr[] = array(
																						 'products_id' => $products_id,
																						 'groups_id' => $group_id,
																						 'attribute_name' => $attribute_name,
																						 'attribute_value' => $attribute_value,
																						 'multiselect' => 0,
																						 'type' => 2
																					  );
															}
														}
													}
												}
											}
											
											//print_r( $groups_arr );
											
											if( !empty($groups_arr) )
											{
											    $this->load->model('Mgroups_products');
												
												foreach( $groups_arr as $grp_arr )
												{
													$this->Mgroups_products->insert_product_groups( $grp_arr );
												}
											}
											
											// success 
										}
										else
										{
											// error 
										}
									}							
									
									// ................... to be continued
							   }
							   
							   $this->messages->add('Products for the selected company, has been uploaded successfully !','success');
							}
						}
					}
				}
				else
				{
					$this->messages->add('The file you are uploading is not in correct format ! Please upload only \'xls\' file.','error');
				}
			}
			else
			{
			    $this->messages->add('Please select an excel-sheet to upload data to the selected company !','error');
			}
		}
		
		$data['companies']=$this->Mcompanies->get_company(array('role !'=>'sub','approved'=>1),array('company_name'=>'ASC','id'=>'DESC'));
		
		$data['tempUrl']=$this->tempUrl;		
		$data['header'] = $this->template.'/header';
		//$data['main'] = $this->template.'/excel_import';
		$data['footer'] = $this->template.'/footer';
		$data['content'] = 'cp/excel_import';
			
		$this->load->view( 'cp/cp_view', $data );  
	}
	
	function create_slug( $string )
	{
		$str = strtolower(trim($string));
		$str = preg_replace("/[^a-z0-9-]/", "-", $str);
		$str = preg_replace("/-+/", "-", $str);
		$str = rtrim($str, "-");
		
		return $str;
	}
}
?>