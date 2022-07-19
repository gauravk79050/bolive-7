<?php
class Excel_import extends CI_Controller
{
	var $tempUrl = '';
	var $template = '';

	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('Messages');
		$this->fdb = $this->load->database('fdb',TRUE);
		$this->tempUrl = base_url().'application/views/mcp';
		$this->template = "/mcp";

		$this->load->model('mcp/Mcompanies');
		$this->load->model('Mgroups_products');
		$this->load->model('Mgroups');
		$this->load->model('Mproduct_discount');
		
		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');

		if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
	}

	function index()
	{
		if( $this->input->post('company_id') )
		{
			ini_set('memory_limit', '128M');
			
			$company_id_ = $this->input->post('company_id');
			
			// Taking bakup of table "Category", "subcategory" and "products"
			// Load the DB utility class
			$this->load->dbutil();
			
			$prefs = array(
					'tables'      => array('categories','subcategories','products','groups_products','products_discount'),      // Array of tables to backup.
					'ignore'      => array(),           // List of tables to omit from the backup
					'format'      => 'txt',             // gzip, zip, txt
					'filename'    => 'mybackup.sql',    // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
					'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
					'newline'     => "\n"               // Newline character used in backup file
			);
			// Backup your entire database and assign it to a variable
			$backup =& $this->dbutil->backup($prefs);
			
			// Path to save backup file
			$path = dirname(__FILE__).'/../../../assets/db-bkup/';
			$fileName = $company_id_."_".date("Y_m_d_H_i_s").".sql";
			// Load the file helper and write the file to your server
			$this->load->helper('file');
			write_file($path.$fileName, $backup);
			
			// Load the download helper and send the file to your desktop
			/*$this->load->helper('download');
			 force_download($path.$fileName, $backup);*/
			
			if( $_FILES['upload_excel']['name'] )
			{
				$upload_dir = dirname(__FILE__).'/../../../assets/mcp/excel-import/';
				$file_name = $_FILES['upload_excel']['name'];
				$tmp_name = $_FILES['upload_excel']['tmp_name'];
				$file_ext = end( explode('.',$file_name) );

				if( strtolower($file_ext) == 'xls' || strtolower($file_ext) == 'xlsx' )
				{
					//echo $upload_dir.$file_name ; echo "vini"; echo $tmp_name; die();
					if( move_uploaded_file( $tmp_name, $upload_dir.$file_name ) )
					{
						$this->load->library('excel');
						$inputFileName = $upload_dir.$file_name;
						$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
						$objReader = PHPExcel_IOFactory::createReader($inputFileType);
						$objReader->setReadDataOnly(true);
						/**  Load $inputFileName to a PHPExcel Object  **/
						$objPHPExcel = $objReader->load($inputFileName);

						$total_sheets=$objPHPExcel->getSheetCount(); // here 4
						$allSheetName=$objPHPExcel->getSheetNames(); // array ([0]=>'student',[1]=>'teacher',[2]=>'school',[3]=>'college')
						for($worksheet=0; $worksheet<$total_sheets;++$worksheet)
						{
							$objWorksheet = $objPHPExcel->setActiveSheetIndex($worksheet); // first sheet
							$highestRow = $objWorksheet->getHighestRow(); // here 5
							$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
							$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
							$arr_data=array();  // here 5
							for ($row = 1; $row <= $highestRow; ++$row) {
								for ($col = 0; $col <= $highestColumnIndex; ++$col) {
									$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
									if(is_array($arr_data) ) {
										$arr_data[$row-1][$col]=$value;
									}
								}
							}
							if($worksheet=='0')
							{
								$worksheet_arr0=$arr_data;
							}
							if($worksheet=='1')
							{
								$worksheet_arr1=$arr_data;
							}
							if($worksheet=='2')
							{
								$worksheet_arr2=$arr_data;
							}
						}
						
						// Insertion and Updation Starts here
						
						// ---------------- STARTING CATEGORIES --------------------------//
						if(!empty($worksheet_arr0) && count($worksheet_arr0) > 1 )
						{
							$this->load->model('Mcategories');
							
							for( $i = 1; $i < count($worksheet_arr0); $i++ )
							{
								$id = $worksheet_arr0[$i][0];
								$company_id = $worksheet_arr0[$i][1];
								$name = $worksheet_arr0[$i][2];
								$description = $worksheet_arr0[$i][3];
								$image = $worksheet_arr0[$i][4];
								$add_text = $worksheet_arr0[$i][5];
								$message = $worksheet_arr0[$i][6];
								$display_tool_tip = $worksheet_arr0[$i][7];
								
								if($image == NULL)
									$image = '';
								if($description == NULL)
									$description = '';
								if($add_text == NULL)
									$add_text = 0;
								if($message == NULL)
									$message = '';
								if($display_tool_tip == NULL)
									$display_tool_tip = 0;
								
								$categories_id = 0;
								
								$category = array();
								$category = $this->Mcategories->check_category_exist(null,null,array('company_id' => $company_id_, 'name' => $name));
								if( !empty($category) ) // --------------->>>>>  UPDATE
								{
									$update_arr = array(
											'name'=>$name,
											'description'=>$description,
											'image'=>$image,
											'add_text'=>$add_text,
											'message'=>$message,
											'updated'=>date( 'Y-m-d', time() ),
											'display_tool_tip'=>$display_tool_tip,
									);
										
									$flag = $this->Mcategories->update_cat( $update_arr,$id );
								}else{					// --------------->>>>>  INSERT
									$insert_arr = array(
											'company_id'=>$company_id_,
											'name'=>$name,
											'description'=>$description,
											'image'=>$image,
											'add_text'=>$add_text,
											'message'=>$message,
											'created'=>date( 'Y-m-d', time() ),
											'updated'=>date( 'Y-m-d', time() ),
									
									);
										
									if($display_tool_tip=='')
									{
										$insert_arr['display_tool_tip']='0';
									}
									else
									{
										$insert_arr['display_tool_tip']=$display_tool_tip;
									}
									
									$insert_arr['status']='1';
									$flag = $this->Mcategories->insert_cat( $insert_arr,$company_id_);
								}
						
							}
						
						}
						// ----------------    END CATEGORIES   --------------------------//
						
						
						// ----------------- START SUB-CATEGORY --------------------------//
						if(!empty($worksheet_arr1) && count($worksheet_arr1) > 1)
						{
							
							for( $i = 1; $i < count($worksheet_arr1); $i++ )
							{
								//print_r($worksheet_arr1[$i]);
								$id = $worksheet_arr1[$i][0];
								$categories_id = $worksheet_arr1[$i][1];
								$catname=$worksheet_arr1[$i][2];
								$subname = $worksheet_arr1[$i][3];
								$subdescription = $worksheet_arr1[$i][4];
								$sub_image_url = $worksheet_arr1[$i][5];
								$subaddtext = $worksheet_arr1[$i][6];
								$submessage = $worksheet_arr1[$i][7];
								$display_tool_tip = $worksheet_arr1[$i][8];
								
								
								if($subdescription == NULL)
									$subdescription = '';
								if($subaddtext == NULL)
									$subaddtext = 0;
								if($submessage == NULL)
									$submessage = '';
								
								$this->load->model('Mcategories');
								$this->load->model('Msubcategories');
								
								if($subname && $catname){
									$result_cat_id=$this->Mcategories->get_cat_id($catname,$company_id_);
									if($result_cat_id){
										$categories_id_=$result_cat_id['id'];
										
										$subcategory = $this->Msubcategories->check_sub_category_exist($categories_id_,null,$subname);
										
										if(!empty($subcategory)){
											
											$subimage = '';
											if($sub_image_url && base_url().$subcategory['subimage'] != $sub_image_url){
												$img_extension = end( explode('.',$sub_image_url) );
												$img_ext = strtolower($img_extension);
												
												if( $img_ext == 'jpg' || $img_ext == 'jpeg' || $img_ext == 'png' || $img_ext == 'gif' || $img_ext == 'bmp' )
												{
													$file_name = $this->create_slug($subname).'-'.time().'.'.$img_extension;
													$upload_path = dirname(__FILE__).'/../../../assets/cp/images/subcategories/'.$file_name;
														
													$hold_image = file_get_contents($sub_image_url);
													if($hold_image !== FALSE){
														if( file_put_contents( $upload_path, $hold_image ) )
														{
															$subimage = "assets/cp/images/".$file_name;
														}
													}
												}
											}
											
											$update_sub_arr = array(
													'subname'=>$subname,
													'subdescription'=>$subdescription,
													'subimage'=>$subimage,
													'subaddtext'=>$subaddtext,
													'submessage'=>$submessage,
													'subupdated'=>date( 'Y-m-d', time() ),
													'display_tool_tip'=>$display_tool_tip
											);
											$this->load->model('Msubcategories');
											$result_sub_cat=$this->Msubcategories->update_sub_cat($update_sub_arr,$subcategory['id']);
											
										}else{
											
											$subimage = '';
											if($sub_image_url != ''){
												$img_extension = end( explode('.',$sub_image_url) );
												$img_ext = strtolower($img_extension);
												
												$subimage = '';
												if( $img_ext == 'jpg' || $img_ext == 'jpeg' || $img_ext == 'png' || $img_ext == 'gif' || $img_ext == 'bmp' )
												{
													$file_name = $this->create_slug($subname).'-'.time().'.'.$img_extension;
													$upload_path = dirname(__FILE__).'/../../../assets/cp/images/subcategories/'.$file_name;
												
													$hold_image = file_get_contents($sub_image_url);
													if($hold_image !== FALSE){
														if( file_put_contents( $upload_path, $hold_image ) )
														{
															$subimage = "assets/cp/images/".$file_name;
														}
													}
												}
											}
											
											$insert_sub_arr = array(
													'categories_id'=>$categories_id_,
													'subname'=>$subname,
													'subdescription'=>$subdescription,
													'subimage'=>$subimage,
													'subaddtext'=>$subaddtext,
													'submessage'=>$submessage,
													'subupdated'=>date( 'Y-m-d', time() ),
											);

											if($display_tool_tip=='')
											{
												$insert_sub_arr['display_tool_tip']='0';
											}
											else{
												$insert_sub_arr['display_tool_tip']=$display_tool_tip;
											}
											$insert_arr['status']='1';
											$this->load->model('Msubcategories');
											$result_sub_cat=$this->Msubcategories->insert_sub_cat($insert_sub_arr);
										}
									}
								}
							}
						}
						// ------------------ END SUB-CATEGORY ---------------------------//

						// -------------------- START PRODUCT ----------------------------//
						if(!empty($worksheet_arr2) && count($worksheet_arr2) > 1)
						{
							$this->load->model("Mproducts");
							for( $i = 1; $i < count($worksheet_arr2); $i++ )
							{
								$id= $worksheet_arr2[$i][0];  // categories_id
								$company_id = $worksheet_arr2[$i][1];    // subcategories_id
								$categories_id = $worksheet_arr2[$i][2];
								
								$subcategories_id = $worksheet_arr2[$i][3];
								$catname = $worksheet_arr2[$i][4];  // categories_name
								$subcategory_name = $worksheet_arr2[$i][5];    // subcategories_name
								$pro_art_num = $worksheet_arr2[$i][6];
								
								$proname = $worksheet_arr2[$i][7];
								
								$prodescription = $worksheet_arr2[$i][8];
								if($prodescription == NULL)
									$prodescription = '';
								
								$image_url = $worksheet_arr2[$i][9];            // image
								if($image_url == NULL)
									$image_url = '';
								
								$sell_product_option = $worksheet_arr2[$i][10];
								if($sell_product_option == NULL)
									$sell_product_option = 'per_unit';
								
								$price_per_person = $worksheet_arr2[$i][11];
								if($price_per_person == NULL)
									$price_per_person = 0;
								
								$min_amount = $worksheet_arr2[$i][12];
								if($min_amount == NULL)
									$min_amount = 0;
								
								$max_amount = $worksheet_arr2[$i][13];
								if($max_amount == NULL)
									$max_amount = 0;
								
								$price_per_unit = $worksheet_arr2[$i][14];
								if($price_per_unit == NULL)
									$price_per_unit = 0;
								
								$price_weight = $worksheet_arr2[$i][15];
								if($price_weight == NULL)
									$price_weight = 0;
								
								//$weight_unit = $worksheet_arr2[$i][16];
								$type = $worksheet_arr2[$i][16];
								if($type == NULL)
									$type = "0";
								
								$allow_upload_image = $worksheet_arr2[$i][17];
								if($allow_upload_image == NULL)
									$allow_upload_image = 0;
								
								$discount = $worksheet_arr2[$i][18];
								if($discount == NULL)
									$discount = 0;
								
								$discount_person = $worksheet_arr2[$i][19];
								if($discount_person == NULL)
									$discount_person = 0;
								
								$discount_wt = $worksheet_arr2[$i][20];
								if($discount_wt == NULL)
									$discount_wt = 0;
								
								$unit_groups = $worksheet_arr2[$i][21];
								if($unit_groups == NULL)
									$unit_groups = '';
								
								$weight_groups = $worksheet_arr2[$i][22];
								if($weight_groups == NULL)
									$weight_groups = '';
								
								$person_groups = $worksheet_arr2[$i][23];
								if($person_groups == NULL)
									$person_groups = '';
								
								//$clone_of = $worksheet_arr2[$i][28];
								//$pro_display = $worksheet_arr2[$i][21];
								$image_display = $worksheet_arr2[$i][24];
								if($image_display == NULL)
									$image_display = "0";
								//$procreated = $worksheet_arr2[$i][23];
								//$proupdated= $worksheet_arr2[$i][24];
								//$status = $worksheet_arr2[$i][25];
								$allday_availability = $worksheet_arr2[$i][25];
								if($allday_availability == NULL)
									$allday_availability = "0";
								
								$availability = $worksheet_arr2[$i][26];
								
								$advance_payment = $worksheet_arr2[$i][27];
								if($advance_payment == NULL)
									$advance_payment = "0";
								
								//$clone_of = $worksheet_arr2[$i][28];
								$availability = explode(",",$availability);
								if(!empty($availability)){
									$availability = json_encode($availability);
								}else{
									$availability = "";
								}
								
								
								if($proname != '' && $catname != '' && $sell_product_option != '' && ( $advance_payment == '0' || $advance_payment == '1') ){
									
									$categories_id = 0;
									if( $catname )
									{
										$this->load->model('Mcategories');
										$category = $this->Mcategories->get_category( array('company_id'=>$company_id_,'name'=>$catname) );
									
										if( empty($category) )
										{
											$insert_arr = array( 'company_id'=>$company_id_,
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
									}else{
										$subcategories_id = "-1";
									}
									
									$where_array = array(
											'company_id' => $company_id_,
											'categories_id' => $categories_id,
											'subcategories_id'=>$subcategories_id,
											'proname' => $proname
									);
									//if($id)
										//$where_array['id'] = $id;
									$product = $this->Mproducts->check_product_exist($where_array);
									//print_r($product); die();
									if(!empty($product)){ // ------------------------------------------>>>>>>>>>>> UPDATE
										
										$products_id = $product['id'];
										
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
										
														$group = $this->Mgroups->get_groups( array('company_id' => $company_id_, 'group_name' => $group_name, 'type' => 0) );
														if( !empty($group) )
														{
															$group_id = $group[0]->id;
														}
														else
														{
															$where_arr = array('company_id' => $company_id_, 'group_name' => '', 'type' => 0);
															$update_arr = array( 'company_id' => $company_id_, 'group_name' => $group_name, 'type' => 0 );
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
															if($group_name == 'Extra'){
																$groups_arr[count($groups_arr)-1]['multiselect'] = 1;
															}
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
										
														$group = $this->Mgroups->get_groups( array('company_id' => $company_id_, 'group_name' => $group_name, 'type' => 1) );
														if( !empty($group) )
														{
															$group_id = $group[0]->id;
														}
														else
														{
															$where_arr = array('company_id' => $company_id_, 'group_name' => '', 'type' => 1);
															$update_arr = array( 'company_id' => $company_id_, 'group_name' => $group_name, 'type' => 1 );
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
															if($group_name == 'Extra'){
																$groups_arr[count($groups_arr)-1]['multiselect'] = 1;
															}
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
										
														$group = $this->Mgroups->get_groups( array('company_id' => $company_id_, 'group_name' => $group_name, 'type' => 2) );
														if( !empty($group) )
														{
															$group_id = $group[0]->id;
														}
														else
														{
															$where_arr = array('company_id' => $company_id_, 'group_name' => '', 'type' => 2);
															$update_arr = array( 'company_id' => $company_id_, 'group_name' => $group_name, 'type' => 2 );
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
															if($group_name == 'Extra'){
																$groups_arr[count($groups_arr)-1]['multiselect'] = 1;
															}
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
										
										$image = '';
										// UPLOADING IMAGE
										if( base_url()."assets/cp/images/product/".$product['image'] != $image_url )
										{
											$img_extension = end( explode('.',$image_url) );
											$img_ext = strtolower($img_extension);
										
											if( $img_ext == 'jpg' || $img_ext == 'jpeg' || $img_ext == 'png' || $img_ext == 'gif' || $img_ext == 'bmp' )
											{
												$file_name = $this->create_slug($proname).'-'.time().'.'.$img_extension;
												$upload_path = dirname(__FILE__).'/../../../assets/cp/images/product/'.$file_name;
										
												$hold_image = file_get_contents($image_url);
												if($hold_image !== FALSE){
													if( file_put_contents( $upload_path, $hold_image ) )
													{
														$image = $file_name;
													}
												}
											}
										}
										
										$update_pro_arr = array(
												'pro_art_num'=>$pro_art_num,
												'proname'=>$proname,
												'prodescription'=>$prodescription,
												'image'=>$image,            // image
												'sell_product_option'=>$sell_product_option,
												'price_per_person'=>str_replace(",",".",$price_per_person),
												'min_amount'=>$min_amount,
												'max_amount'=>$max_amount,
												'price_per_unit'=>str_replace(",",".",$price_per_unit),
												'price_weight'=>str_replace(",",".",$price_weight),
												'type'=>$type,
												'discount'=>$discount,
												'discount_person'=>$discount_person,
												'discount_wt'=>$discount_wt,
												//'pro_display'=>$pro_display,
												'image_display'=>$image_display,
												//'procreated'=>$procreated,
												'proupdated'=>date( 'Y-m-d', time() ),
												//'status'=>$status,
												'allday_availability'=>$allday_availability,
												'availability'=>$availability,
												'advance_payment'=>$advance_payment,
												//'clone_of'=>$clone_of,
												'allow_upload_image'=>$allow_upload_image
										
												//'display_tool_tip'=>$display_tool_tip
										);
										
										//print_r($update_pro_arr); die();
										$this->load->model('Mproducts');
										$result_pro=$this->Mproducts->update_pro($update_pro_arr,$products_id);
										
									}else{ // ---------------------------------------------------------------->>>>>>>>> INSERT
										
										$products_id = 0;
										
										$image = '';
										// UPLOADING IMAGE
										if( $image_url )
										{
											$img_extension = end( explode('.',$image_url) );
											$img_ext = strtolower($img_extension);
										
											if( $img_ext == 'jpg' || $img_ext == 'jpeg' || $img_ext == 'png' || $img_ext == 'gif' || $img_ext == 'bmp' )
											{
												$file_name = $this->create_slug($proname).'-'.time().'.'.$img_extension;
												$upload_path = dirname(__FILE__).'/../../../assets/cp/images/product/'.$file_name;
										
												$hold_image = file_get_contents($image_url);
												if($hold_image !== FALSE){
													if( file_put_contents( $upload_path, $hold_image ) )
													{
														$image = $file_name;
													}
												}
											}
										}
										
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
												'company_id'=>$company_id_,
												'categories_id'=>$categories_id,
												'subcategories_id'=>$subcategories_id,
												'pro_art_num'=>$pro_art_num,
												'proname'=>$proname,
												'prodescription'=>$prodescription,
												'image'=>$image,            // image
												'price_per_person'=>str_replace(",",".",$price_per_person),
												'min_amount'=>$min_amount,
												'max_amount'=>$max_amount,
												'price_per_unit'=>str_replace(",",".",$price_per_unit),
												'price_weight'=>str_replace(",",".",$price_weight),
												'type'=>$type,
												'discount' => $unit_discount,
												'discount_wt' => $wt_discount,
												'discount_person' => $person_discount,
												//'pro_display'=>$pro_display,
												'image_display'=>$image_display,
												'procreated'=>date( 'Y-m-d', time() ),
												'proupdated'=>date( 'Y-m-d', time() ),
												'status'=>'1',
												//'allday_availability'=>$allday_availability,
												'availability'=>$availability,
												'advance_payment'=>$advance_payment,
												//'clone_of'=>$clone_of,
												'allow_upload_image'=>$allow_upload_image
										);
										/*if($sell_product_option=='')
										{
											$product_arr['sell_product_option']='per_unit';
										}
										else
										{
											$product_arr['sell_product_option']=$sell_product_option;
										}*/
										//print_r($product_arr); die;
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
																
															$group = $this->Mgroups->get_groups( array('company_id' => $company_id_, 'group_name' => $group_name, 'type' => 0) );
															if( !empty($group) )
															{
																$group_id = $group[0]->id;
															}
															else
															{
																$where_arr = array('company_id' => $company_id_, 'group_name' => '', 'type' => 0);
																$update_arr = array( 'company_id' => $company_id_, 'group_name' => $group_name, 'type' => 0 );
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
																if($group_name == 'Extra'){
																	$groups_arr[count($groups_arr)-1]['multiselect'] = 1;
																}
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
																
															$group = $this->Mgroups->get_groups( array('company_id' => $company_id_, 'group_name' => $group_name, 'type' => 1) );
															if( !empty($group) )
															{
																$group_id = $group[0]->id;
															}
															else
															{
																$where_arr = array('company_id' => $company_id_, 'group_name' => '', 'type' => 1);
																$update_arr = array( 'company_id' => $company_id_, 'group_name' => $group_name, 'type' => 1 );
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
																if($group_name == 'Extra'){
																	$groups_arr[count($groups_arr)-1]['multiselect'] = 1;
																}
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
																
															$group = $this->Mgroups->get_groups( array('company_id' => $company_id_, 'group_name' => $group_name, 'type' => 2) );
															if( !empty($group) )
															{
																$group_id = $group[0]->id;
															}
															else
															{
																$where_arr = array('company_id' => $company_id_, 'group_name' => '', 'type' => 2);
																$update_arr = array( 'company_id' => $company_id_, 'group_name' => $group_name, 'type' => 2 );
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
																if($group_name == 'Extra'){
																	$groups_arr[count($groups_arr)-1]['multiselect'] = 1;
																}
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
												
										}
									}
								}
					
							}
						
						}
						// --------------------- END PRODUCT -----------------------------//
						
						// Insertion and Updation Ends here
					}
				}
				
			}

		}
		$data['companies']=$this->Mcompanies->get_company(array('role !'=>'sub','approved'=>1),array('company_name'=>'ASC','id'=>'DESC'));

		$data['tempUrl']=$this->tempUrl;
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/excel_import';
		$data['footer'] = $this->template.'/footer';
			
		$this->load->view( $this->template.'/mcp_view', $data );
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

	function create_slug( $string )
	{
		$str = strtolower(trim($string));
		$str = preg_replace("/[^a-z0-9-]/", "-", $str);
		$str = preg_replace("/-+/", "-", $str);
		$str = rtrim($str, "-");

		return $str;
	}
	
	function upload_file()
	{
		if( $this->input->post('company_id') )
		{
			//die("dere");

		}
		$data['tempUrl']=$this->tempUrl;
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/excel_import';
		$data['footer'] = $this->template.'/footer';
			
		$this->load->view( $this->template.'/mcp_view', $data );
			
	}
	
	function download_file()
	{
		ini_set('memory_limit', '128M');
		
		$company_id=$this->input->post('company_id_export');
		if($company_id == NULL)
		{
			redirect(base_url().'mcp/excel_import');
		}
		
		$groups=$this->Mgroups->get_groups(array('company_id'=>$company_id,'type'=>0));
		$groups_person=$this->Mgroups->get_groups(array('company_id'=>$company_id,'type'=>2));
		$groups_wt=$this->Mgroups->get_groups(array('company_id'=>$company_id,'type'=>1));
		
		$this->load->model('Mcategories');
		$result_cat=$this->Mcategories->get_category_name($company_id);
		
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle( _('Company Categories details') );

		$counter = 1;
		$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Category id') )->getStyle('A'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Company id') )->getStyle('B'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Category Name') )->getStyle('C'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('Description') )->getStyle('D'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('Image') )->getStyle('E'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('Add Text')." (1->Yes / 0->No)" )->getStyle('F'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('Message') )->getStyle('G'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('Display Tool Tip')." (1->Yes / 0->No)" )->getStyle('H'.$counter)->getFont()->setBold(true);
		$category_id = array();
		if(!empty($result_cat))
		{
			foreach($result_cat as $cat)
			{
				$counter++;
				
				$category_id[] = $cat['id'];
				$category_name[]=$cat['name'];
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $cat['id'] );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, $cat['company_id'] );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, $cat['name'] );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $cat['description'] );
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, ($cat['image'])?base_url().$cat['image']:'' );
				$this->excel->getActiveSheet()->setCellValue('F'.$counter, $cat['add_text'] );
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $cat['message'] );
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, $cat['display_tool_tip'] );
			}
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(80);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
		
		$this->load->model('Msubcategories');
		$result_sub_cat = array();
		foreach ($category_id as $cats)
		{
			$result_sub_cat_array = $this->Msubcategories->get_subcategory_name($cats);
			$result_sub_cat = array_merge($result_sub_cat, $result_sub_cat_array);
		}
		
		$this->excel->createSheet();
		$this->excel->setActiveSheetIndex(1);
		$this->excel->getActiveSheet()->setTitle( _('Company Sub Categories  details') );
			
		$subcounter = 1;
		
		$this->excel->getActiveSheet()->setCellValue('A'.$subcounter, _('Sub Category id') )->getStyle('A'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B'.$subcounter, _('Category id') )->getStyle('B'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C'.$subcounter, _('Category name') )->getStyle('C'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D'.$subcounter, _('Sub Category Name') )->getStyle('D'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E'.$subcounter, _('Sub Description') )->getStyle('E'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F'.$subcounter, _('Sub Image') )->getStyle('F'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G'.$subcounter, _('Sub Add Text')." (1->Yes / 0->No)" )->getStyle('G'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('H'.$subcounter, _('Sub Message') )->getStyle('H'.$subcounter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('I'.$subcounter, _('Display Tool Tip')." (1->Yes / 0->No)" )->getStyle('I'.$subcounter)->getFont()->setBold(true);

		if(!empty($result_sub_cat))
		{
			foreach($result_sub_cat as $sub_cat)
			{
				$subcounter++;
					
				$this->excel->getActiveSheet()->setCellValue('A'.$subcounter, $sub_cat['id'] );
				$this->excel->getActiveSheet()->setCellValue('B'.$subcounter, $sub_cat['categories_id'] );
				$cat_id=$sub_cat['categories_id'];
				$this->load->model('Mcategories');
				$result_cat_name=$this->Mcategories->get_cat_name($cat_id);
				$this->excel->getActiveSheet()->setCellValue('C'.$subcounter, $result_cat_name['name'] );
				$this->excel->getActiveSheet()->setCellValue('D'.$subcounter, $sub_cat['subname'] );
				$this->excel->getActiveSheet()->setCellValue('E'.$subcounter, $sub_cat['subdescription'] );
				$this->excel->getActiveSheet()->setCellValue('F'.$subcounter, ($sub_cat['subimage'])?base_url().$sub_cat['subimage']:'' );
				$this->excel->getActiveSheet()->setCellValue('G'.$subcounter, $sub_cat['subaddtext'] );
				$this->excel->getActiveSheet()->setCellValue('H'.$subcounter, $sub_cat['submessage'] );
				$this->excel->getActiveSheet()->setCellValue('I'.$subcounter, $sub_cat['display_tool_tip'] );
			}
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(80);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(40);

		
		$this->excel->createSheet();
		$this->excel->setActiveSheetIndex(2);
		$this->excel->getActiveSheet()->setTitle( _('Company Produt details') );
		$this->load->model('Mproducts');
		$result_pro=$this->Mproducts->get_company_product_detail($company_id);
		
		$counter = 1;
		
		$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Product Id') )->getStyle('A'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Company Id') )->getStyle('B'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Categories Id') )->getStyle('C'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('Sub Categories ID') )->getStyle('D'.$counter)->getFont()->setBold(true);

		$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('Category Name') )->getStyle('E'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('Sub Category Name') )->getStyle('F'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('Product Article Number') )->getStyle('G'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('Product Name') )->getStyle('H'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('I'.$counter, _('Product Description') )->getStyle('I'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('J'.$counter, _('Image Url') )->getStyle('J'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('K'.$counter, _('Sell Product Option')." (per_unit/per_person/weight_wise/client_may_choose)" )->getStyle('K'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('L'.$counter, _('Price /Person') )->getStyle('L'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('M'.$counter, _('Min Person') )->getStyle('M'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('N'.$counter, _('Max Person') )->getStyle('N'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('O'.$counter, _('Price /Unit') )->getStyle('O'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('P'.$counter, _('Price /Weight') )->getStyle('P'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('Q'.$counter, _('Show as New')." (1->Yes / 0->No)" )->getStyle('Q'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('R'.$counter, _('Allow Image Upload')." (1->Yes / 0->No)" )->getStyle('R'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('S'.$counter, _('Discount/Unit') )->getStyle('S'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('T'.$counter, _('Discount/Person') )->getStyle('T'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('U'.$counter, _('Discount/Kg') )->getStyle('U'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('V'.$counter, _('Group Extra for / Unit products as 7_color_+12#7_testing_+5 where 7=>group_id, color=> attribute_text,+12=>attribute_price seperated by "#" ') )->getStyle('V'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('W'.$counter, _('Group Extra for / Weight products 7_color_+12#7_testing_+5 where 7=>group_id, color=> attribute_text,+12=>attribute_price seperated by  "#" ') )->getStyle('W'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('X'.$counter, _('Group Extra for / Person products 7_color_+12#7_testing_+5 where 7=>group_id, color=> attribute_text,+12=>attribute_price seperated by "#"') )->getStyle('X'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('Y'.$counter, _('Display Image') )->getStyle('Y'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('Z'.$counter, _('All Day Availability') )->getStyle('Z'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('AA'.$counter, _('Availability')." (Enter comma separated day code starting from Sunday->1,Monday->2 and so on )" )->getStyle('AA'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('AB'.$counter, _('Advance Payment')." (1->Yes / 0->No)" )->getStyle('AB'.$counter)->getFont()->setBold(true);

		if(!empty($result_pro))
		{	
			foreach($result_pro as $pro)
			{
				$counter++;
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $pro['id'] );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, $pro['company_id'] );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, $pro['categories_id'] );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $pro['subcategories_id'] );
				$cat_id=$pro['categories_id'];
				$this->load->model('Mcategories');
				$result_cat_name=$this->Mcategories->get_cat_name($cat_id);
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, $result_cat_name['name'] );
				$subcat_id=$pro['subcategories_id'];
				$this->load->model('Msubcategories');
				$result_subcat_name=$this->Msubcategories->get_sub_cat_name($subcat_id);
				if(!empty($result_subcat_name))
				{
					$this->excel->getActiveSheet()->setCellValue('F'.$counter, $result_subcat_name['subname'] );
				}
				else
				{
					$this->excel->getActiveSheet()->setCellValue('F'.$counter);
				}
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $pro['pro_art_num'] );
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, $pro['proname'] );
				$this->excel->getActiveSheet()->setCellValue('I'.$counter, $pro['prodescription'] );
				$this->excel->getActiveSheet()->setCellValue('J'.$counter, ($pro['image'])?base_url()."assets/cp/images/product/".$pro['image']:'' );
				$this->excel->getActiveSheet()->setCellValue('K'.$counter, $pro['sell_product_option'] );
				$this->excel->getActiveSheet()->setCellValue('L'.$counter, $pro['price_per_person'] );
				//$this->excel->getActiveSheet()->setCellValue('M'.$counter );
				$this->excel->getActiveSheet()->setCellValue('M'.$counter, $pro['min_amount'] );
				$this->excel->getActiveSheet()->setCellValue('N'.$counter, $pro['max_amount'] );
				$this->excel->getActiveSheet()->setCellValue('O'.$counter, $pro['price_per_unit'] );
				$this->excel->getActiveSheet()->setCellValue('P'.$counter, $pro['price_weight'] );
				$this->excel->getActiveSheet()->setCellValue('Q'.$counter, $pro['type'] );
				$this->excel->getActiveSheet()->setCellValue('R'.$counter, $pro['allow_upload_image'] );
				
				$disc_unit = '';
				if($pro['discount'] == "multi"){
					$product_discount = $this->Mproduct_discount->get_product_discount($pro['id'],0);
					foreach($product_discount as $values){
						$disc_unit .=  $values->quantity."::".$values->discount_per_qty."::".$values->price_per_qty."##";
					}
					$disc_unit = substr($disc_unit , 0 , -2);
					$this->excel->getActiveSheet()->setCellValue('S'.$counter, $disc_unit );
				}else{
					$this->excel->getActiveSheet()->setCellValue('S'.$counter, $pro['discount'] );
				}
				
				
				$disc_person = '';
				if($pro['discount_person'] == "multi"){
					$product_discount_person = $this->Mproduct_discount->get_product_discount($pro['id'],2);
					foreach($product_discount_person as $values){
						$disc_person .=  $values->quantity."::".$values->discount_per_qty."::".$values->price_per_qty."##";
					}
					$disc_person = substr($disc_person , 0 , -2);
					$this->excel->getActiveSheet()->setCellValue('T'.$counter, $disc_person );
				}else{
					$this->excel->getActiveSheet()->setCellValue('T'.$counter, $pro['discount_person'] );
				}
				
				$disc_wt = '';
				if($pro['discount_wt'] == "multi"){
					$product_discount_wt = $this->Mproduct_discount->get_product_discount($pro['id'],1);
					foreach($product_discount_wt as $values){
						$disc_wt .=  $values->quantity."::".$values->discount_per_qty."::".$values->price_per_qty."##";
					}
					$disc_wt = substr($disc_wt , 0 , -2);
					$this->excel->getActiveSheet()->setCellValue('u'.$counter, $disc_wt );
				}else{
					$this->excel->getActiveSheet()->setCellValue('U'.$counter, $pro['discount_wt'] );
				}
		
				
				$groups_products=$this->Mgroups_products->get_groups_products(array('products_id'=>$pro['id'],'type'=>0));
				$groups_products_wt=$this->Mgroups_products->get_groups_products(array('products_id'=>$pro['id'],'type'=>1));
				$groups_products_person=$this->Mgroups_products->get_groups_products(array('products_id'=>$pro['id'],'type'=>2));
					
				// Extra Calculation for per unit
				$extra_per_unit_array = array();
				if(!empty($groups_products)){
					foreach($groups_products as $groups_product){
						foreach($groups as $key => $val){
							if($val->id == $groups_product->groups_id){
								$extra_per_unit_array[] = $groups[$key]->group_name."_".$groups_product->attribute_name."_".$groups_product->attribute_value;
							}
						}
					}
				}
				$extra_per_unit = '';
				if(!empty($extra_per_unit_array)){
					$extra_per_unit = implode("#",$extra_per_unit_array);
				}
				
				// Extra Calculation for per Weight
				$extra_per_wt_array = array();
				if(!empty($groups_products_wt)){
					foreach($groups_products_wt as $group_product_wt){
						foreach($groups_wt as $key => $val){
							if($val->id == $group_product_wt->groups_id){
								$extra_per_wt_array[] = $groups[$key]->group_name."_".$group_product_wt->attribute_name."_".$group_product_wt->attribute_value;
							}
						}
					}
				}
				$extra_per_wt = '';
				if(!empty($extra_per_wt_array)){
					$extra_per_wt = implode("#",$extra_per_wt_array);
				}
				
				// Extra Calculation for per person
				$extra_per_person_array = array();
				if(!empty($groups_products_person)){
					foreach($groups_products_person as $group_product_person){
						foreach($groups_person as $key => $val){
							if($val->id == $group_product_person->groups_id){
								$extra_per_person_array[] = $groups[$key]->group_name."_".$group_product_person->attribute_name."_".$group_product_person->attribute_value;
							}
						}
					}
				}
				$extra_per_person = '';
				if(!empty($extra_per_person_array)){
					$extra_per_person = implode("#",$extra_per_person_array);
				}
				
				$this->excel->getActiveSheet()->setCellValue('V'.$counter, $extra_per_unit )->getStyle('V'.$counter)->getAlignment()->setWrapText(true);
				$this->excel->getActiveSheet()->setCellValue('W'.$counter, $extra_per_wt )->getStyle('W'.$counter)->getAlignment()->setWrapText(true);
				$this->excel->getActiveSheet()->setCellValue('X'.$counter, $extra_per_person )->getStyle('X'.$counter)->getAlignment()->setWrapText(true);
				$this->excel->getActiveSheet()->setCellValue('Y'.$counter, $pro['image_display'] );
				$this->excel->getActiveSheet()->setCellValue('Z'.$counter, $pro['allday_availability'] );
				$avail = json_decode($pro['availability'],TRUE);
				$this->excel->getActiveSheet()->setCellValue('AA'.$counter, (!empty($avail))?implode(",",$avail):$pro['availability'] );
				$this->excel->getActiveSheet()->setCellValue('AB'.$counter, $pro['advance_payment'] );
					
			}
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(60);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(80);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(50);
		$this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(50);
		$this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(50);
		$this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('AA')->setWidth(80);
		$this->excel->getActiveSheet()->getColumnDimension('AB')->setWidth(30);

		$datestamp = date("d-m-Y");
		$filename = "Company-Details-".$company_id."-".$datestamp.".xls";

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');

	}
	
	/**
	 * This function is used to export clients information for any company.
	 * @return mixed it returns an excel sheet including clients info
	 */
	function download_client_info_file(){

		ini_set('memory_limit', '128M');
		
		$company_id = $this->input->post('company_id_export_client');
		if($company_id == NULL)
		{
			redirect(base_url().'mcp/excel_import');
		}
		
		$companyInfo = $this->Mcompanies->get_company(array('id'=>$company_id));
		
		$this->load->model("Mclients");
		$clients = $this->Mclients->get_company_clients($company_id);
		
		$counter = 1;
		$this->load->library ( 'excel' );
		$this->excel->setActiveSheetIndex ( 0 );
		$this->excel->getActiveSheet ()->setTitle ( _ ( 'Customer Info' ) );
		
		$this->excel->getActiveSheet ()->setCellValue ( 'A' . $counter, _ ( 'ID' ) )->getStyle ( 'A' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'B' . $counter, _ ( 'Company' ) )->getStyle ( 'B' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'C' . $counter, _ ( 'Firstname' ) )->getStyle ( 'C' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'D' . $counter, _ ( 'Lastname' ) )->getStyle ( 'D' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'E' . $counter, _ ( 'Password' ) )->getStyle ( 'E' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'F' . $counter, _ ( 'Address' ) )->getStyle ( 'F' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'G' . $counter, _ ( 'House Number' ) )->getStyle ( 'G' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'H' . $counter, _ ( 'Postal Code' ) )->getStyle ( 'H' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'I' . $counter, _ ( 'City' ) )->getStyle ( 'I' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'J' . $counter, _ ( 'Country' ) )->getStyle ( 'J' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'K' . $counter, _ ( 'Phone No' ) )->getStyle ( 'K' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'L' . $counter, _ ( 'Mobile No' ) )->getStyle ( 'L' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'M' . $counter, _ ( 'Fax' ) )->getStyle ( 'M' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'N' . $counter, _ ( 'Email' ) )->getStyle ( 'N' . $counter )->getFont ()->setBold ( true );
		$this->excel->getActiveSheet ()->setCellValue ( 'O' . $counter, _ ( 'Vat' ) )->getStyle ( 'O' . $counter )->getFont ()->setBold ( true );
			//$this->excel->getActiveSheet()->setCellValue('P'.$counter, _('Vat') )->getStyle('P'.$counter)->getFont()->setBold(true);
			
		if( !empty( $clients ) )
		{
			
			foreach( $clients as $c )
			{
				$counter++;
					
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $c->id );
				//$this->excel->getActiveSheet()->setCellValue('B'.$counter, (string)$c->fb_id, PHPExcel_Cell_DataType::TYPE_STRING);
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, $companyInfo['0']->company_name );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, $c->firstname_c );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $c->lastname_c );
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, $c->password_c );
				$this->excel->getActiveSheet()->setCellValue('F'.$counter, $c->address_c );
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $c->housenumber_c );
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, $c->postcode_c );
				$this->excel->getActiveSheet()->setCellValue('I'.$counter, $c->city_c );
				$this->excel->getActiveSheet()->setCellValue('J'.$counter, $c->country_id );
				$this->excel->getActiveSheet()->setCellValue('K'.$counter, $c->phone_c );
				$this->excel->getActiveSheet()->setCellValue('L'.$counter, $c->mobile_c );
				$this->excel->getActiveSheet()->setCellValue('M'.$counter, $c->fax_c );
				$this->excel->getActiveSheet()->setCellValue('N'.$counter, $c->email_c );
				$this->excel->getActiveSheet()->setCellValue('O'.$counter, $c->vat );
			}		
		}
		
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('k')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
		
		$datestamp = date("d-m-Y");
		$filename = "Clients-Info-".$company_id."-".$datestamp.".xls";
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	/**
	 * This function is used to import clients information for any company.
	 * @return string it returns a message (success or fail)
	 */
	function upload_client_info_file(){
		
		if($this->input->post("company_id_client")){

			ini_set('memory_limit', '128M');
				
			$company_id = $this->input->post('company_id_client');
			
			// Taking bakup of table "Category", "subcategory" and "products"
			// Load the DB utility class
			$this->load->dbutil();
				
			$prefs = array(
					'tables'      => array('clients','company', 'client_numbers'),      // Array of tables to backup.
					'ignore'      => array(),           // List of tables to omit from the backup
					'format'      => 'txt',             // gzip, zip, txt
					'filename'    => 'mybackup.sql',    // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
					'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
					'newline'     => "\n"               // Newline character used in backup file
			);
			// Backup your entire database and assign it to a variable
			$backup =& $this->dbutil->backup($prefs);
				
			// Path to save backup file
			$path = dirname(__FILE__).'/../../../assets/db-bkup/';
			$fileName = $company_id."_cc_".date("Y_m_d_H_i_s").".sql";
			// Load the file helper and write the file to your server
			$this->load->helper('file');
			write_file($path.$fileName, $backup);
				
			// Load the download helper and send the file to your desktop
			/*$this->load->helper('download');
			 force_download($path.$fileName, $backup);*/
				
			if( $_FILES['upload_excel_client']['name'] )
			{
				$upload_dir = dirname(__FILE__).'/../../../assets/mcp/excel-import/';
				$file_name = $_FILES['upload_excel_client']['name'];
				$tmp_name = $_FILES['upload_excel_client']['tmp_name'];
				$file_ext = end( explode('.',$file_name) );
			
				if( strtolower($file_ext) == 'xls' || strtolower($file_ext) == 'xlsx' )
				{
					if( move_uploaded_file( $tmp_name, $upload_dir.$file_name ) )
					{
						$this->load->library('excel');
						$inputFileName = $upload_dir.$file_name;
						$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
						$objReader = PHPExcel_IOFactory::createReader($inputFileType);
						$objReader->setReadDataOnly(true);
						/**  Load $inputFileName to a PHPExcel Object  **/
						$objPHPExcel = $objReader->load($inputFileName);
			
						$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
						$highestRow = $objWorksheet->getHighestRow(); // here 5
						$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
						$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
						
						//echo (string)$objWorksheet->getCellByColumnAndRow(3, 243)->getValue(); die;
						
						$arr_data=array();  // here 5
						for ($row = 1; $row <= $highestRow; ++$row) {
							for ($col = 0; $col <= $highestColumnIndex; ++$col) {
								$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
								if(is_array($arr_data) ) {
									$arr_data[$row-1][$col]=$value;
								}
							}
						}
						//print_r($arr_data); die;
						
						// Insertion and Updation Starts here
			
						if(!empty($arr_data) && count($arr_data) > 1 )
						{
							$this->load->model('mcp/Mclients');
								
							// Fetching pre associated clients with the given Company
							
							$companies_associated_clients = $this->Mclients->get_companies_client_ids($company_id);

							//$companies_associated_clients = explode(",",$companies_associated_clients['0']['clients_associated']);
							
							// An array of clients Ids which are going to be attcahed with the given company
							$client_ids_to_associate = array();
							
							$client_data = array();
							for( $i = 1; $i < count($arr_data); $i++ )
							{
								$address = '';
								$house_no= '';
								$addr = explode(' ',$arr_data[$i][5]);
								if(!empty($addr) && count($addr) > 1){
									$house_flag = false;
									foreach($addr as $addr_sub){
										if(!empty($addr_sub) && is_numeric($addr_sub['0'])){
											$house_flag = true;
										}
										
										if($house_flag){
											$house_no .= ($addr_sub != '')?(' '.$addr_sub):$addr_sub;
										}
										else{
											$address .= ($addr_sub != '')?(' '.$addr_sub):$addr_sub;
										}
									}
								}
								
								$clients_info_array = array();
								
								$clients_info_array['company_id'] 	 = $company_id;
								$clients_info_array['firstname_c'] 	 = addslashes($arr_data[$i][3]);
								$clients_info_array['lastname_c']  	 = addslashes($arr_data[$i][2]);
								//$clients_info_array['address_c']   	 = addslashes($arr_data[$i][5]);
								//$clients_info_array['housenumber_c'] = $arr_data[$i][6];
								$clients_info_array['address_c']   	 = $address;
								$clients_info_array['housenumber_c'] = $house_no;
								$clients_info_array['postcode_c'] 	 = $arr_data[$i][7];
								$clients_info_array['city_c'] 	 	 = addslashes($arr_data[$i][8]);
								$clients_info_array['phone_c']   	 = $arr_data[$i][10];
								if(substr($clients_info_array['phone_c'], 0 ,1) != 0)
									$clients_info_array['phone_c'] = "0".$clients_info_array['phone_c'];
								
								$clients_info_array['mobile_c']   	 = $arr_data[$i][11];
								if(substr($clients_info_array['mobile_c'], 0 ,1) != 0)
									$clients_info_array['mobile_c'] = "0".$clients_info_array['mobile_c'];
								//$clients_info_array['email_c']   	 = $arr_data[$i][13];
								$clients_info_array['email_c']   	 = $arr_data[$i][4];
								//$clients_info_array['password_c']    = $arr_data[$i][4];
								$clients_info_array['password_c']    = $arr_data[$i][13];
								if($clients_info_array['password_c'] == ''){
									$this->load->helper('string');
									$clients_info_array['password_c'] = random_string('alnum', 8);
									//$clients_info_array['password_c'] = 'password';
								}
							
								$clients_info_array['fb_id'] 		 = 0;
								//$clients_info_array['country_id'] 	 = 21;
								$clients_info_array['country_id'] 	 = addslashes($arr_data[$i][9]);
								$clients_info_array['created_c'] 	 = date("Y-m-d H:i:s");
								$clients_info_array['updated_c'] 	 = date("Y-m-d H:i:s");
								$clients_info_array['verified'] 	 = 1;
								$clients_info_array['newsletters'] 	 = 'subscribe';
								$clients_info_array['notifications'] = 'unsubscribe';
								
								// Getting clients info with email
								$clients_info  = $this->Mclients->check_client_exist(array("email_c" => $clients_info_array['email_c']));
								
								if(empty($clients_info)){  // Not exists
									
									// Inserting clients info and getting ID
									$client_id = $this->Mclients->insert_client($clients_info_array);
									
									if( $client_id != '' && is_numeric($client_id) ){
										$client_ids_to_associate[] = $client_id;
									}
								}else{
									$handle = fopen(dirname(__FILE__).'/../../../cl-info.txt', 'a');
									fwrite($handle, '\n ->'.$clients_info_array['email_c']);
									fclose($handle);
									$client_ids_to_associate[] = $clients_info['0']['id'];
								}
								$client_data[] = $clients_info_array;
							}
							
							
							// Associating new clients
							if(!empty($client_ids_to_associate)){
								
								foreach($client_ids_to_associate as $id){
									$insert_array = array(
										'client_id' => $id,
										'company_id' => $company_id,
										'newsletter' => 'subscribe',
										'associated' => '1'
									);
									$this->Mclients->insert_client_number($insert_array);
								}
								
								$this->session->set_flashdata("success",_("Clients are added successfully"));
							}else{
								$this->session->set_flashdata("error",_("Clients are not added successfully. Please try again"));
							}
						}
						// Insertion and Updation Ends here
					}
				}
			
			}
			
		}
		
		redirect(base_url()."mcp/excel_import");
	}
	
	function delete_c(){
		$this->load->model('mcp/Mcompanies');
		$companies = $this->db->query("SELECT * FROM `company` WHERE `id` >=177 AND `id` <=631")->result_array();
		if(!empty($companies)){
			foreach ($companies as $company){
				$result = $this->Mcompanies->delete($company['id']);
				echo $result."--".$company['id']."<br />"; 
			}
		}
	}
	
	function update_shortname(){
		
		$mysqlExportPath= dirname(__FILE__)."/../../../short_name_bckup-".date("d-m-Y").'.gz';
			
		$this->load->dbutil();
		$prefs = array(
				'tables'      => array('products_ingredients'),  // Array of tables to backup.
				'ignore'      => array(),           // List of tables to omit from the backup
				'format'      => 'zip',             // gzip, zip, txt
				'filename'    => 'products_ingredients-bckup-'.date("d-m-Y"),    // File name - NEEDED ONLY WITH ZIP FILES
				'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
				'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
				'newline'     => "\n"               // Newline character used in backup file
		);
			
		$backup =& $this->dbutil->backup($prefs);
		$this->load->helper('file');
		$res=write_file($mysqlExportPath, $backup);
		
		
		
		if($res){
		
			$this->db->where('kp_id !=',0);
			$this->db->where('ki_id',0);
			$this->db->where('ki_name !=','(');
			$this->db->where('ki_name !=',')');
			$results = $this->db->get('products_ingredients')->result_array();
			
			foreach ($results as $result){
				$this->fdb->select('p_short_name_dch');
				$this->fdb->where('p_id',$result['kp_id']);
				$get_names = $this->fdb->get('products')->result_array();
	
				if(!empty($get_names)){
					$short_name = $get_names[0]['p_short_name_dch'];
					
					if($short_name != ''){
						$update_array = array(
							'ki_name' => $short_name 
						);
						$this->db->where('id',$result['id']);
						$this->db->update('products_ingredients', $update_array);
					}
				}
			}
			echo '1';
		}else{
			echo '0';
		}
		
	}
	
	function imp_bvba_fresh_food_service(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4076-Articlelist-Fresh-Food-Service.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 120;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4076;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$sub_cat = trim($rows[2]);
			$option = 'per_unit';
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if($sub_cat == ''){
				$sub_cat_id = -1;
			}
			elseif ($sub_cat != '') {
				$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
				$r_sub = $this->db->get('subcategories')->result_array();
				if(!empty($r_sub)){
					$sub_cat_id = $r_sub[0]['id'];
				}else{
					$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
					$sub_cat_id = $this->db->insert_id();
				}
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_nv_select(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4069-articlelist-nvSelect.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 2718;
		$highestColumnIndex= 8;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
//		print_r($worksheet_arr0);die();
	
		$comp_id = 4069;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
				
			if(strlen($pro_name) > 0){
				if($count_rows%250 == 0){
					$num++;
				}
				$cat = 'nv-select cat-'.$num;
				$cat_id = 0;
				$option = 'per_unit';
				$type = trim($rows[6]);
				$price_per_unit = trim(str_replace(',','.',$rows[8]));
				//$price_per_unit = floatval($price_per_unit);
				$price_weight = 0;
	
				if($type == 'Gewicht'){
					$option = 'weight_wise';
					$price_weight = trim(str_replace(',','.',$rows[8]));
					//$price_weight = floatval($price_weight);
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(strtolower($pro_name)),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_bakkershuys37_2(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/404-artikellijst-Bakkershuys37.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 133;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4040;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakkerij_van_imschoot(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2414-Articlelist-Bakkerij-Van-Imschoot.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 171;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
//		print_r($worksheet_arr0);die();
	
		$comp_id = 2414;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_dhollander_leemans(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3629-Articlelist-DHollander-Leemans.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 915;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
//		print_r($worksheet_arr0);die();
	
		$comp_id = 3629;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = "d-hollander-leemans cat-1";
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim(str_replace(',', '.', $rows[2])):0;
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bkkerij_geert(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4021-Articlelist-Bakkerij-Geert.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 71;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4021;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_brood_en_banket_alex_en_inge(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4037-Article-Brood-en-Banket-Alex-en-Inge.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 210;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4037;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = "brood-en-banket-alex-en-inge cat-1";
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = (trim($rows[1]) != '')?trim($rows[1]):0;
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	function imp_bakkerij_jo_van_rompaey(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4068-Articlelist-Bakkerij-Van-Rompaey-Jo.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 182;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4068;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	function imp_keurslager_nico(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4039-keurslager-Nico.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 510;
		$highestColumnIndex= 10;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4039;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = 'keurslager-nico cat-1';
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[10]);
			$price_per_unit = trim($rows[8]);
			$price_weight = 0;
				
			if(strlen($pro_name) > 0){
				if($type == 'kg'){
					$option = 'weight_wise';
					$price_weight = trim($rows[8]);
					$price_per_unit = 0;
				}
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_slagerij_verelst_daems(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4046-Articlelist-Verelst-Daems.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 219;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4046;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
				
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	function imp_bakkerij_t_boomke(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4054-Articlelist-bakkerij-t-Boomke.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 308;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4054;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[1]);
			$price_per_unit = trim($rows[2]);
			$price_weight = 0;
	
			if(strlen($pro_name) > 0){
				if($type == 'kg'){
					$option = 'weight_wise';
					$price_weight = trim($rows[2]);
					$price_per_unit = 0;
				}
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	function imp_ijssalon_de_verwennerij(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4058-ARTICLELIST-ijssalon-De-Verwennerij.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 150;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4058;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim(str_replace(',','.',str_replace('€','',$rows[2])));
	
			if(strlen($pro_name) > 0){
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakker_geert_zwevegem(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4071-Articlelist-Bakker-Geert-Zwevegem.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 71;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4071;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_t_bakkerietj(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4073-Articlelist-t-Bakkerietje.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 52;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4073;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bon_appetit(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4079-Articlelist-Bon-Appetit.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 1634;
		$highestColumnIndex= 5;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4079;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
				
			if(strlen($pro_name) == 0){
				$res_arr = explode(' ', $art_no);
				if(isset($res_arr[2])){
					$pro_name = trim(str_replace($res_arr[0], '', $art_no));
					$art_no = trim($res_arr[0]);
				}
			}
				
			if(strlen($pro_name) > 0){
				if($count_rows%250 == 0){
					$num++;
				}
				$cat = 'bon-appetit cat-'.$num;
				$cat_id = 0;
				$option = 'per_unit';
				$type = trim($rows[4]);
				$price_per_unit = trim($rows[3]);
				$price_weight = 0;
	
				if($type == 'KG/kilogram'){
					$option = 'weight_wise';
					$price_weight = trim($rows[3]);
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_chocolade_atelier_vyverman(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4086-Articlelist-Chocolade-Atelier-Vyverman.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 315;
		$highestColumnIndex= 5;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4086;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				$cat = trim($rows[0]);
				$cat_id = 0;
				$option = 'per_unit';
				$type = trim($rows[3]);
				$price_per_unit = trim($rows[2]);
				$price_weight = 0;
	
				if($type == 'kg'){
					$option = 'weight_wise';
					$price_weight = trim($rows[2]);
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'order_display' => trim($rows[5]),'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_vanhoudt_verdeyen_bvba(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2030-Articlelist-Vanhoudt-Verdeyen.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 167;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 2030;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$cat_id = 0;
				$sub_cat = trim($rows[2]);
				$type = trim($rows[3]);
				$option = 'per_unit';
				$price_per_unit = trim($rows[3]);
				$price_per_person = 0;
	
				if(strpos($type,"p.p.")){
					$option = 'per_person';
					$price_per_person = trim(str_replace(',','.',str_replace('p.p.','',$rows[3])));
					$price_per_unit = 0;
				}
				elseif(strpos($type,"/st")){
					$option = 'per_unit';
					$price_per_unit = trim(str_replace(',','.',str_replace('/st','',$rows[3])));
					$price_per_person = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_slagerij_Van_Belle(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2737-articlelist-slagerij-Van-Belle.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 236;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
//		print_r($worksheet_arr0);die();
	
		$comp_id = 2737;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
				$product_article_number = trim($rows[1]);
				$cat = 'slagerij-van-belle cat-1';
				$cat_id = 0;
				$option = 'per_unit';
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(strtolower($pro_name)),
						'sell_product_option' =>$option,
						'pro_art_num'=>$product_article_number,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_brood_en_banket_peeters(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4070-Articlelist-Brood-en-banket-Peeters.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 94;
		$highestColumnIndex= 4;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4070;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
				
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[2]);
				$cat_id = 0;
				$option = 'per_unit';
				$type = trim($rows[4]);
				$price_per_unit = 0;
				$price_weight = 0;
	
				if(strpos($type,'stk')){
					$option = 'per_unit';
					$price_per_unit = trim(str_replace(',', '.', str_replace('stk','',$rows[4])));
					$price_weight = 0;
				}
				elseif(strpos($type,'/stuk')){
					$option = 'per_unit';
					$price_per_unit = trim(str_replace(',', '.', str_replace('/stuk','',$rows[4])));
					$price_weight = 0;
				}
				elseif(strpos($type,'/kg')){
					$option = 'weight_wise';
					$price_weight = trim(str_replace(',', '.', str_replace('/kg','',$rows[4])));
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_bakkerij_bekaert(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4080-Articlelist-bakkerij-Bekaert.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 606;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4080;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
				
			if(strlen($pro_name) > 0){
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_cremerie_francois(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4078-Articlelist-Cremerie-Francois.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 47;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4078;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_slagerij_henk(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1345-Articlelist-slagerij-Henk.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 483;
		$highestColumnIndex= 5;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
		$pro_arr = array();
		$comp_id = 1345;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if((!in_array($pro_name, $pro_arr)) && (strlen($pro_name) != 0)){
				$pro_arr[] = $pro_name;
	
				$art_no = trim($rows[0]);
				$cat = trim($rows[5]);
				$cat = ($cat != '')?$cat:"slagerij-henk cat-1";
				$cat_id = 0;
				$option = 'per_unit';
				$type = trim($rows[3]);
				$price_per_unit = 0;
				$price_weight = 0;
				$price_per_person = 0;
	
				if($type == 'STUK'){
					$option = 'per_unit';
					$price_per_unit = trim($rows[2]);
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif($type == 'KG'){
					$option = 'weight_wise';
					$price_weight = trim($rows[2]);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif($type == 'PERS'){
					$option = 'per_person';
					$price_per_person = trim($rows[2]);
					$price_per_unit = 0;
					$price_weight =0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_t_pistoleeke_bvba(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4057-Articlelist-t-Pistoleeke.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 168;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4057;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = "t-pistoleeke-bvba cat-1";
				$cat_id = 0;
				$option = 'weight_wise';
				$type = trim($rows[2]);
				$price_per_unit = 0;
				$price_weight = trim($rows[2]);
				$price_per_person = 0;
	
				if(strpos($type,'st')){
					$option = 'per_unit';
					$price_per_unit = trim(str_replace(',', '.', str_replace('/st','',$rows[2])));
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif(strpos($type,'pers')){
					$option = 'per_person';
					$price_per_person = trim(str_replace(',', '.', str_replace('/pers','',$rows[2])));
					$price_per_unit = 0;
					$price_weight =0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
	
				$count_rows++;
			}
		}
	}
	
	function imp_devriendt_brood_and_banket(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1008-articlelist-Devriendt.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 258;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 1008;
		foreach ($worksheet_arr0 as $rows){
			$product_article_number = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = 'devriendt-brood-and-banket cat-1';
			$cat_id = 0;
			$type = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = 0;
			$price_weight=0;
				
			if ($type == 'st') {
				$option = 'per_unit';
				$price_per_unit = trim(str_replace(',','.',$rows[2]));
				$price_weight=0;
			}
			elseif ($type == 'grew') {
				$option = 'weight_wise';
				$price_weight = trim(str_replace(',','.',$rows[2]));
				$price_per_unit=0;
			}
			
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'price_per_unit' => $this->number2db($price_per_unit),
					'price_weight' => $this->number2db($price_weight/1000),
					'pro_art_num'=>$product_article_number,
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_bakkerij_vermeersch(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2977-Articlelist-Bakkerij-Vermeersch.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 199;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 2977;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[2]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = trim($rows[3]);
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakkerij_caromel(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4029-Articlelist-bakkerij-Caromel.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 185;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4029;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
	
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_spar_de_vuyst(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4074-Articlelist-Spar-De-Vuyst.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 156;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//		print_r($worksheet_arr0);die();
	
		$comp_id = 4074;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				$cat = trim($rows[0]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> strtolower($cat), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_restaurant_mistral(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4089-Articlelist-restaurant-Mistral.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 56;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4089;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
				
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_traiteur_gobert(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4091-Articlelist-traiteur-Gobert.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 735;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4091;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
				
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[2]);
				$cat = trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakkerij_melice(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4093-Articlelist-Bakkerij-Melice.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 266;
		$highestColumnIndex= 6;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4093;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
	
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[6]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = trim($rows[3]);
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Brood_en_Banket_Lenaerts_Proost(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4087-Articlelist-Brood-en-Banket-Lenaerts-Proost.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		$arr_data = array();
	
		$highestRow = 126;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//echo '<pre>';print_r($worksheet_arr0);die();
	
		$comp_id = 4087;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
	
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Articlelist_T_Groot(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4092-Articlelist-T-Groot.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		$arr_data = array();
	
		$highestRow = 422;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//echo '<pre>';print_r($worksheet_arr0);die();
	
		$comp_id = 4092;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat='t-groot cat-1';
			$cat_id = 0;
			$option = 'per_unit';
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_artikellijst_Ks_Otten(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3929-artikellijst-Ks-Otten.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		$arr_data = array();
	
		$highestRow = 233;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//echo '<pre>';print_r($worksheet_arr0);die();
	
		$comp_id = 3929;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat='ks-otten cat-1';
			$cat_id = 0;
			$option = 'per_unit';
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_distillery_massy(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4096_Articlelist_Distillery_Massy.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		$arr_data = array();
	
		$highestRow = 139;
		$highestColumnIndex= 5;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
		
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4096;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$exploded_val = explode("/",trim($rows[1]));
			$pro_name = trim($exploded_val[0]);
			$cat=trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_bakkerij_vanHeuckelom(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1131-Articlelist-Bakkerij-VanHeuckelom.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 575;
		$highestColumnIndex= 14;
		for ($row = 3; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 1131;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
	
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[1]);
				$cat = trim($rows[14]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = trim($rows[7]);
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_sagerij_dries(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3053-Articlelist-Sagerij-Dries.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 472;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 3053;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[2]);
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakkershuis_Berlaar(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4056-Articlelist bakkershuisBerlaar.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 56;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4056;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat='Bakkershuis-Berlaar cat-1';
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_brood_en_banket_DeVylder(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4082-Articlelist-brood-en-banket-DeVylder.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 400;
		$highestColumnIndex= 5;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4082;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = 0;
			$price_weight = 0;
			$price_person = 0;
			if ($rows[2] != '')
			{
				$price = trim(str_replace(',','.',$rows[2]));
			}
			else {
				$price = 0;
			}
				
			if($type == 'Kg'){
				$option = 'weight_wise';
				$price_weight = $price;
				$price_per_unit = 0;
				$price_person = 0;
			}
			elseif ($type == 'Stuk'){
				$option = 'per_unit';
				$price_per_unit = $price;
				$price_weight = 0;
				$price_person = 0;
			}
			elseif ($type == 'Persoon'){
				$option = 'per_person';
				$price_weight = 0;
				$price_per_unit = 0;
				$price_person = $price;
			}
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'price_per_person' => $this->number2db($price_person),
					'price_per_unit' => $this->number2db($price_per_unit),
					'price_weight' => $this->number2db($price_weight/1000),
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_Articlelist_KS_Schoofs(){
		
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
		
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1725-Articlelist-KS-Schoofs.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
		
		$allSheetName=$objPHPExcel->getSheetNames();
		
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		
		$arr_data = array();  // here 5
	
		$highestRow = 835;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 1725;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[3]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = trim(str_replace(',','.',$rows[2]));
		
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
		
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakkerij_kemel(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/Articlelist-Bakkerij-Kemel.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 76;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4030;
		foreach ($worksheet_arr0 as $rows){
			$proname = trim($rows[0]);
			if(strlen($proname) > 0){
				$cat= trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($proname),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_ad_merelbeke(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4090-Articlelist-AD-Merelbeke.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 4171;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4090;
		$num = 0;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
				
			if((!in_array($art_no, $pro_arr)) && (strlen($pro_name) > 0)){
				$pro_arr[] = $art_no;
				if($count_rows%250 == 0){
					$num++;
				}
				$cat='VDM-bvba-AD-Delhaize-Merelbeke cat-'.$num;
				$cat_id = 0;
				$option = 'per_unit';
				$price1 = trim($rows[2]);
				$price2 = trim($rows[3]);
	
				if($price2 == ''){
					$price_per_unit = trim(str_replace(',','.',$rows[2]));
				}
				else{
					$price_per_unit = $price1.".".$price2;
					$price_per_unit = floatval($price_per_unit);
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_het_ylebemdje(){
		
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1694-Articlelist-Het-Ylebemdje.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 523;
		$highestColumnIndex= 4;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 1694;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat = ($cat == "")?"het-ylebemdje cat-1":$cat;
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[4]);
			$price_per_unit = ($price_per_unit != '')?str_replace(',','.',$price_per_unit):0;
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'price_per_unit' => $this->number2db($price_per_unit),
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_bakkerij_daniel_van_den_bergh(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4005.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 102;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4005;
		foreach ($worksheet_arr0 as $rows){
			$proname = trim($rows[1]);
			if(strlen($proname) > 0){
				$art_no = trim($rows[0]);
				$cat= trim($rows[2]);
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($proname),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_kwaliteitsslagerij_nico_annelore(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4081-Articlelist-kwaliteitsslagerij-nico-en-annelore.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 146;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4081;
		foreach ($worksheet_arr0 as $rows){
			$proname = trim($rows[0]);
			if(strlen($proname) > 0){
				$cat= trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($proname),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Articlelist_bakkerij_Van_Laer_Kurt(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2807-Articlelist-bakkerij-Van-Laer-Kurt.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 296;
		$highestColumnIndex= 3;
		for ($row = 3; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 2807;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			if (trim($rows[3]) != '')
			{
				$price_per_unit = trim(str_replace(',','.',$rows[3]));
			}
			else {
				$price_per_unit = 0;
			}
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> -1,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'price_per_unit' => $this->number2db($price_per_unit),
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_artikellijst_VRT_catering(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
		
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4105-artikellijst-VRT-catering.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 84;
		$highestColumnIndex= 4;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4105;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$sub_cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = 0;
			if ($rows[4] != '')
			{
				$price_per_unit = trim(str_replace(',','.',$rows[4]));
			}
			else {
				$price_per_unit = 0;
			}
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if($sub_cat == ''){
				$sub_cat_id = -1;
			}
			elseif ($sub_cat != '') {
				$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
				$r_sub = $this->db->get('subcategories')->result_array();
				if(!empty($r_sub)){
					$sub_cat_id = $r_sub[0]['id'];
				}else{
					$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
					$sub_cat_id = $this->db->insert_id();
				}
			}
				
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> $sub_cat_id,
					'pro_art_num'=> $art_no,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'price_per_unit' => $this->number2db($price_per_unit),
					'procreated'=>date('Y-m-d')
			);
			//$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_Articlelist_bakkerij_Clarysse(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2309-Articlelist-bakkerij-Clarysse.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 266;
		$highestColumnIndex= 5;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 2309;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			if(trim($rows[2]) == ''){
				$cat = 'bakkerij-Clarysse cat-1';
			}
			else {
				$cat = trim($rows[2]);
			}
			$cat_id = 0;
			$sub_cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = 0;
			$price_weight = 0;
			$price_per_person = 0;
			$weight_val_gr = 0;
	
			if (trim($rows[4]) == '')
			{
				$price_per_unit = 0;
			}
				
	
			if (trim($rows[5]) == '')
			{
				if (trim($rows[4]) == '')
				{
					$price_per_unit = 0;
				}
				else
				{
					$price_exp_arr=explode("/",trim($rows[4]));
					if ($price_exp_arr[1] == "kg" || $price_exp_arr[1] == "l")
					{
						$option = 'weight_wise';
						$price_per_unit = 0;
						$price_weight=trim(str_replace(",",".",trim($price_exp_arr[0])));
						$price_per_person = 0;
					}
					elseif ($price_exp_arr[1] == "st" || $price_exp_arr[1] == "zakje")
					{
						$option = 'per_unit';
						$price_per_unit = trim(str_replace(",",".",trim($price_exp_arr[0])));
						$price_weight = 0;
						$price_per_person = 0;
					}
					else
					{
						$option = 'weight_wise';
						$weight_val_gr=trim($price_exp_arr[1]);
						$weight_value=str_replace("gr","",$weight_val_gr);
						$price_weight=(trim(str_replace(",",".",trim($price_exp_arr[0])))/$weight_value)*1000;
						$price_per_unit = 0;
						$price_per_person = 0;
					}
				}
			}
			else
			{
				if (trim($rows[5]) == 'stuk'){
					$option = 'per_unit';
					$current_price=str_replace(",",".",trim($rows[4]));
					$price_per_unit = str_replace("eur","",$current_price);
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif (trim($rows[5]) == 'persoon') {
					$option = 'per_person';
					$price_per_unit = 0;
					$price_weight = 0;
					$current_price=str_replace(",",".",trim($rows[4]));
					$price_per_person = str_replace("eur","",$current_price);
				}
	
			}
				
				
				
			if(strlen($pro_name) > 0){
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bakkerij_lembrechts(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4067-Articlelist-bakkerij-Lembrechts.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 78;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4067;
		foreach ($worksheet_arr0 as $rows){
			$proname = trim($rows[0]);
			if(strlen($proname) > 0){
				$cat= trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($proname),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_vrt1(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4105-Artikellijst-VRT1.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 159;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4105;
		foreach ($worksheet_arr0 as $rows){
			$proname = trim($rows[1]);
			if(strlen($proname) > 0){
				$art_no = trim($rows[0]);
				$cat= 'VRT-NV cat-1';
				$cat_id = 0;
				$option = 'per_unit';
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($proname),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_frabak1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4108-Articlelist-FRABAK-LIJST-1.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		
		$arr_data = array();  // here 5
	
		$highestRow = 407;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
		
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4108;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[2]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
				
				if (trim($rows[3]) != ''){
					$price_per_unit = trim(str_replace(',','.',$rows[3]));
				}
		
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
		
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Articlelist_bakkerij_Michielsen(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/612-Articlelist-bakkerij-Michielsen.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 988;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 612;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$cat = "bakkerij_Michielsen-cat1";
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = trim(str_replace(',','.',$rows[2]));
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
	
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Articlelist_bakkerij_Peeters(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3207-Articlelist-bakkerij-Peeters(kontich).xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 581;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 3207;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$cat_id = 0;
				$option = 'per_unit';
				if (trim($rows[3]) !=null)
				{
					$price_per_unit=trim(str_replace(',','.',$rows[2]));
					$cat=trim($rows[3]);
				}
				else
				{
					if (trim($rows[2]) == null)
					{
						$price_per_unit = 0;
						$cat = "bakkerij_Peeters-cat1";
					}
					else
					{
						$exploded_arry=explode("  ",trim($rows[2]));
						$price_per_unit = trim(str_replace(',','.',trim($exploded_arry[0])));
						$cat=trim($exploded_arry[1]);
					}
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_Articlelist_slagerij_Ceursters_Daneels(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4106-Articlelist-slagerij-Ceursters-Daneels.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 590;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4106;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$sub_cat=trim($rows[2]);
				$cat_id = 0;
				$sub_cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> mb_strtolower($cat,'UTF-8'), 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>mb_strtolower($sub_cat,'UTF-8'),'subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Articlelist_bakkerij_Hein_Vandenberghe(){
	
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4116-Articlelist-bakkerij-Hein-Vandenberghe.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 345;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4116;
		foreach ($worksheet_arr0 as $rows){
			$pro_display=trim($rows[0]);
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[2]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_display'=>$pro_display,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
	
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_bvba_dockx_bakkerij(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/53-Articlelist-Bakkerij-Dockx.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 14;
		$highestColumnIndex= 0;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die;
	
		$comp_id = 53;
		foreach ($worksheet_arr0 as $key=>$rows){
				
			if ($key == 1 || $key == 4 || $key == 8 ){
				$rown = preg_split('/\R/', $rows[0]);
	
				foreach ($rown as $r_key=>$r_val){
					$exploded_arr=explode(" ", $r_val);
					$art_num=trim($exploded_arr[0]);
					$price=trim(end($exploded_arr));
					$pro_name=trim(str_replace($price, '', str_replace($art_num, '', $r_val)));
					
					$cat = "dockx-bakkerij cat1";
					$cat_id = 0;
					$option = 'per_unit';
					if ($price){
						$price_per_unit = str_replace(',', '.', $price);
					}
					else{
						$price_per_unit = 0;
					}
					$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
					$res = $this->db->get('categories')->result_array();
					if(!empty($res)){
						$cat_id = $res[0]['id'];
					}else{
						$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
						$cat_id = $this->db->insert_id();
					}
					
					$inser_array = array(
							'company_id'=>$comp_id,
							'categories_id'=>$cat_id,
							'subcategories_id'=> -1,
							'pro_art_num'=> $art_num,
							'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
							'sell_product_option' =>$option,
							'price_per_unit' => $this->number2db($price_per_unit),
							'procreated'=>date('Y-m-d')
					);
					
					//$this->db->insert('products',$inser_array);
				}
			}
			else{
				$row_p = preg_split('/\R/', $rows[0]);
				$c = array();
				for ($i =0; $i < count($row_p); $i++){
					$c[] = array('art'=> $row_p[$i],'proname'=> $row_p[$i+1],'price'=> $row_p[$i+2]);
					$i = $i + 2;
				}
	
				foreach ($c as $c_key => $c_val){
					$art_num=$c_val['art'];
					$pro_name=$c_val['proname'];
					$price=$c_val['price'];
					$cat = "dockx-bakkerij cat1";
					$cat_id = 0;
					$option = 'per_unit';
					if ($price){
						$price_per_unit = str_replace(',', '.', $price);
					}
					else{
						$price_per_unit = 0;
					}
					$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
					$res = $this->db->get('categories')->result_array();
					if(!empty($res)){
						$cat_id = $res[0]['id'];
					}else{
						$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
						$cat_id = $this->db->insert_id();
					}
	
					$inser_array = array(
							'company_id'=>$comp_id,
							'categories_id'=>$cat_id,
							'subcategories_id'=> -1,
							'pro_art_num'=> trim($art_num),
							'proname'=> trim(addslashes(mb_strtolower($pro_name,'UTF-8'))),
							'sell_product_option' =>$option,
							'price_per_unit' => $this->number2db($price_per_unit),
							'procreated'=>date('Y-m-d')
					);
	
					//$this->db->insert('products',$inser_array);
				}
			}
			$count_rows++;
		}
	}
	
	function imp_97_Articlelist_bakkerij_Willems(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/97-Articlelist-bakkerij-Willems.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 74;
		$highestColumnIndex= 4;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 97;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[2]);
	
				$sub_cat = trim($rows[3]);
				$cat_id = 0;
				$sub_cat_id = -1;
				$option = 'per_unit';
	
				if (trim($rows[4]) == '')
				{
					$price_per_unit = 0;
				}
				else
				{
					$price_per_unit = trim(str_replace(',','.',$rows[4]));
				}
				
				if ($cat == '')
				{
					$cat = 'bakkerij_Willems-cat1';
					$sub_cat_id = -1;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=> $sub_cat, 'subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1264_Bakkerij_Verbeeck_Lier_Artikelen_lijst(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1264-Bakkerij-Verbeeck-Lier-Artikelen-lijst.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 75;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 1264;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
				
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1101_Articlelist_Bakkerij_Vandecasteele(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1101-Articlelist-Bakkerij-Vandecasteele.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 120;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 1101;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[1]);
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_veresto(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
		
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/veresto.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
		
		$allSheetName=$objPHPExcel->getSheetNames();
		
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 2230;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
		
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
		
		$comp_id = 4125;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			
			if(strlen($pro_name) > 0){
				$art_no = trim($rows[0]);
				$cat = trim($rows[2]);
				
				$sub_cat = trim($rows[3]);
				$cat_id = 0;
				$sub_cat_id = -1;
				$option = 'per_unit';
				$price_per_unit = 0;
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
				
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=> $sub_cat, 'subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
				
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4083_Articlelist_slagerij_Frederik(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4083-Articlelist-slagerij-Frederik.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 181;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4083;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$cat = 'slagerij_Frederik cat-1';
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Articlelist_bloemen_Thomas(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/Articlelist-bloemen-Thomas.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 76;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4130;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$cat = trim($rows[0]);
				$cat_id = 0;
				$option = 'per_unit';
				if (trim($rows[2]) == '')
				{
					$price_per_unit = 0;
				}
				else
				{
					$price_per_unit = trim(str_replace(',','.',$rows[2]));
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_3853_Articlelist_bakkerij_Bella(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3853-Articlelist-bakkerij-Bella.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 317;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 3853;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			if(strlen($pro_name) > 0){
	
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				if (trim($rows[3]) != '')
				{
					$cat = trim($rows[3]);
				}
				else
				{
					$cat = 'bakkerij_Bella cat-1';
				}
	
				if (trim($rows[2]) == '')
				{
					$art_no = '';
				}
				else
				{
					$art_no = trim($rows[2]);
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_633_Fred_The_Finest_Market_DARE_bvba(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/633-Fred-The-Finest-Market-DARE-bvba-menu-lijst.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 122;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 633;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
	
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				if (trim($rows[1]) != '')
				{
					$cat = trim($rows[1]);
				}
				else
				{
					$cat = 'DARE_bvba cat-1';
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4123_Articlelist_Patisserie_De_Ruyter(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4123-Articlelist-Patisserie-De-Ruyter.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 296;
		$highestColumnIndex= 4;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4123;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat_id = 0;
			$sub_cat = trim($rows[3]);
			$cat = trim($rows[2]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				$price_per_unit = 0;
				if ($cat == '')
				{
					$cat = 'patisserie_de_ruyter cat-1';
				}
				else
				{
					$cat = trim($rows[2]);
				}
				$price_per_unit = trim($rows[4]);
				if ($price_per_unit == '')
				{
					$price_per_unit = 0;
					$price_weight = 0;
				}
				else
				{
					$exploded_price = explode('/', $price_per_unit);
					if (!array_key_exists(1,$exploded_price))
					{
						$price_per_unit = trim(str_replace(',','.',$exploded_price[0]));
						$price_weight = 0;
					}
					else
					{
						if(trim($exploded_price[1]) == 'kg'){
							$option = 'weight_wise';
							$price_weight = trim($exploded_price[0]);
							$price_weight = str_replace(',','.',str_replace("€","",$price_weight));
							$price_per_unit = 0;
						}
					}
				}
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'proname'=> strtolower(addslashes($pro_name)),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4117_articlelits_bakkerij_Lapeere(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4117-articlelits-bakkerij-Lapeere.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 162;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//print_r($worksheet_arr0);die();
	
		$comp_id = 4117;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			if(strlen($pro_name) > 0){
	
				$cat_id = 0;
				$option = 'per_unit';
				$price_per_unit = 0;
	
				if (trim($rows[1]) != '')
				{
					$cat = trim($rows[1]);
				}
				else
				{
					$cat = 'bakkerij_Lapeere cat-1';
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4115_Articlelist_bakkerij_Galloo(){
	
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4115-Articlelist-bakkerij-Galloo.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 125;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4115;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat_id = 0;
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				$price_per_unit = 0;
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4112_bakkerij_Stefaan_Ardooie(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4112-bakkerij-Stefaan-Ardooie.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 107;
		$highestColumnIndex= 2;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4112;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat_id = 0;
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				$price_per_unit = 0;
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_882_articlelist_schotse_vishandel(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/882-rticlelist-schotse-vishandel.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 211;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 882;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat_id = 0;
			$cat = trim($rows[2]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				$price_per_unit = 0;
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1585_Articlelist_de_druivelaar(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1585-Articlelist-de-druivelaar.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 2788;
		$highestColumnIndex= 5;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 1585;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[3]);
			$pro_name = trim($rows[4]);
			$cat_id = 0;
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[0]);
			$order_cat = trim($rows[1]);
			$price = trim($rows[5]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				if (trim($rows[5]) == ''){
					$price_per_unit = 0;
				}
				else{
					$price_per_unit = str_replace(',', '.', $price);
				}
	
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'order_display'=>$order_cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_2404_Articlelist_slagerij_Buermans(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2404-Articlelist-slagerij-Buermans.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 784;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 2404;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat_id = 0;
			$cat = trim($rows[2]);
			$price = trim($rows[3]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				if ($price)
				{
					$price_per_unit = $price;
				}
				else
				{
					$price_per_unit = 0;
				}
	
				if (trim($rows[2]) == '')
				{
					$cat = 'slagerij-Buermans-cat1';
				}
				else
				{
					$cat = trim($rows[2]);
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	
	
	function imp_2980_Articlelist_Van_Looveren_Kenis(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2980-Articlelist-Van-Looveren-Kenis.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 354;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 2980;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat_id = 0;
			$cat = trim($rows[1]);
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4129_Articlelist_slagerij_Bruynseels(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4129-Articlelist-slagerij-Bruynseels.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 373;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4129;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[1]);
			$pro_name = trim($rows[2]);
			$cat_id = 0;
			$cat = trim($rows[0]);
			$price = trim($rows[3]);
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price)
				{
					$price_per_unit = str_replace(',','.',$price);
				}
				else
				{
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_Articlelist_slagerijpatriek(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/Articlelist-slagerijpatriek.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 777;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 908;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat_id = 0;
			$cat = 'slagerijpatriek_cat1';
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_4134_Articlelist_bakkerij_De_Mokker(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4134-Articlelist-bakkerij-De-Mokker.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 464;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4134;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[1]);
			$pro_name = trim($rows[2]);
			$cat_id = 0;
			$cat = trim($rows[0]);
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
					
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_4135_Articlelist_sterslagerij_christophe(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4135-Articlelist-sterslagerij-christophe.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 1146;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4135;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$price = trim($rows[2]);
			$type = trim($rows[3]);
			$cat_id = 0;
			$cat = 'sterslagerij-christophe-cat1';
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$price_weight = 0;
				for ($i = 1; $i < 7; $i++)
				{
				if (strlen($price) == $i)
				{
				$price = substr($price, 0, ($i-2)) . '.' . substr($price, ($i-2));
				break;
				}
				}
				if($type == 'KG/kilogram'){
				$option = 'weight_wise';
					$price_weight = $price;
					$price_per_unit = 0;
				}
				elseif ($type == 'ST/stuk'){
				$option = 'per_unit';
						$price_per_unit = $price;
								$price_weight = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
						$cat_id = $res[0]['id'];
			}else{
			$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
			}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(',','.',$price_per_unit)),
						'price_weight' => $this->number2db(str_replace(',','.',$price_weight)/1000),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4028_Articlelist_bakkerij_Belmans(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4028-Articlelist-bakkerij-Belmans.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 254;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4028;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat_id = 0;
			$cat = trim($rows[2]);
			$option = 'per_unit';
			$price_per_unit = trim($rows[1]);
			if(strlen($pro_name) > 0){
				$price_per_unit = ($price_per_unit)?str_replace(',','.',$price_per_unit):0;
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1443_Articlelist_bakkerij_knapen(){
		
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1443-Articlelist-bakkerij-knapen.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 441;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 1443;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$price = trim($rows[2]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price){
					$price_per_unit = str_replace(",",".",$price);
				}else{
					$price_per_unit = 0;
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2303_Articlelist_Bakkerij_Stefaan_Beernem(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2303-Articlelist-Bakkerij-Stefaan-Beernem.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 518;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 2303;
		$pro_arr = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat_id = 0;
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
	
			if(strlen($pro_name) > 0){
	
				$price_per_unit = 0;
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2513_Articlelist_Bakkerij_Kuylen(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2513-Articlelist-Bakkerij-Kuylen.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 297;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//print_r($worksheet_arr0);die();
		$comp_id = 2513;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2816_Articlelist_laureyns12(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2816-Articlelist-laureyns12.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 1105;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 2816;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat_id = 0;
			$cat = 'laureyns-cat1';
			$price = trim($rows[1]);
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				if (in_array(mb_strtolower($pro_name,'UTF-8'), $product_array))
				{
					$this->db->select('id,proname,price_per_unit');
					$this->db->where(array('company_id'=>$comp_id,'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8'))));
					$query = $this->db->get('products',$inser_array)->result_array();
					$product_price = $query[0]['price_per_unit'];
					if ($this->number2db($price) > $product_price)
					{
						if ($price != '' && $price != 0)
						{
							$this->db->where(array('id'=>$query[0]['id'],'company_id'=>$comp_id,'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8'))));
							$updated_array = array(
									'company_id'=>$comp_id,
									'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
									'price_per_unit' => $this->number2db($price),
									'procreated'=>date('Y-m-d')
							);
							$this->db->update('products',$updated_array);
						}
					}
				}
				else
				{
	
					if ($price)
					{
						$price_per_unit = $price;
					}
					else
					{
						$price_per_unit = 0;
					}
	
					$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
					$res = $this->db->get('categories')->result_array();
					if(!empty($res)){
						$cat_id = $res[0]['id'];
					}else{
						$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
						$cat_id = $this->db->insert_id();
					}
	
					$inser_array = array(
							'company_id'=>$comp_id,
							'categories_id'=>$cat_id,
							'subcategories_id'=> -1,
							'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
							'sell_product_option' =>$option,
							'price_per_unit' => $this->number2db($price_per_unit),
							'procreated'=>date('Y-m-d')
					);
					//$this->db->insert('products',$inser_array);
					$product_array[] = mb_strtolower($pro_name,'UTF-8');
				}
			}
			$count_rows++;
		}
	}
	
	function imp_4113_Articlelist_Artisane(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4113-Articlelist-Artisane.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 166;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//print_r($worksheet_arr0);die();
		$comp_id = 4113;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4118_Articlelist_bakkerij_Kris_Evergem(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4118-Articlelist-bakkerij-Kris-Evergem.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 121;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4118;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$price = trim($rows[1]);
			$cat_id = 0;
			$cat = trim($rows[2]);
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price)
				{
					for ($i = 1; $i < 4; $i++)
					{
					if (strlen($price) == $i)
					{
					$price_per_unit = substr($price, 0, ($i-2)) . '.' . substr($price, ($i-2));
					break;
					}
					}
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
						if(!empty($res)){
						$cat_id = $res[0]['id'];
				}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
							'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
							'procreated'=>date('Y-m-d')
							);
							//$this->db->insert('products',$inser_array);
	}
									$count_rows++;
	}
	}
	
	function imp_4121_Articlelist_brood_en_banket_Meeus(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4121-Articlelist-brood-en-banket-Meeus.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 422;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4121;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			else{
				$cat = trim($rows[1]);
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
			}
			$count_rows++;
		}
	}
	
	function imp_4140_Articlelist_Buso_Aarschot_school(){
	
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4140-Articlelist-Buso-Aarschot-school.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
	
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$arr_data = array();  // here 5
	
		$highestRow = 148;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
	
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4140;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4143_Articlelist_charcuterie_Josue_deleu(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4143-Articlelist-charcuterie-Josue-deleu.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 588;
		$highestColumnIndex= 1;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//print_r($worksheet_arr0);die();
		$comp_id = 4143;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$price = trim($rows[1]);
			$cat = 'charcuterie-Josue-deleu-cat1';
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price){
					$price_per_unit = $price;
				}else{
					$price_per_unit = 0;
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4072_Articlelis_Bakkerij_Victor(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4072-Articlelis-Bakkerij-Victor.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 1315;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//print_r($worksheet_arr0);die();
		$comp_id = 4072;
		
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$price = trim($rows[2]);
			$cat = 'Bakkerij-Victor-cat1';
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price != ''){
					$price_per_unit = $price;
				}else{
					$price_per_unit = 0;
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4159_artikellist_Culinair_huis_Vandeputte(){
		
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4159-artikellist-Culinair-huis-Vandeputte.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 2411;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//print_r($worksheet_arr0);die();
		$comp_id = 4159;
		
		$pro_arr = array();
		$product_array = array();
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			if($count_rows%250 == 0){
				$num++;
			}
			$cat = 'Culinair-huis-Vandeputte-cat'.$num;
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4147_Artikellijst_patisserie_DeBrabander_6(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4147-Artikellijst-patisserie-DeBrabander-6.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 472;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);die();
		$comp_id = 87;
		
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='kg'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'persoon') {
					$option = 'per_person';
					$price_per_unit = 0;
					$price_weight = 0;
					$price_per_person = (trim($rows[2]) != '')?trim($rows[2]):0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(strtolower($pro_name)),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4090_VDM_delhaize_merelbeke(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4090-VDM-delhaize-merelbeke.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 384;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 4090;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = '4090-VDM-delhaize-merelbeke-cat1';
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='KG/kilogram'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'ST/stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4090_VDM_delhaize_merelbeke_part2(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4090-VDM-delhaize-merelbeke-part2.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 2081;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//print_r($worksheet_arr0);die();
		$comp_id = 4090;
	
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$price = trim($rows[2]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price != ''){
					$price_per_unit = $price;
				}else{
					$price_per_unit = 0;
				}
				$price_per_unit = str_replace(",",".",$price_per_unit);
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4160_articlelist_bakkerijpernot(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4160-articlelist-bakkerijpernot.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 194;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4160;
	
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_1215_slagerij_Demeulemeester(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1215-slagerij-Demeulemeester.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 1786;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 1215;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='KG/kilogram'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'ST/stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'PE/persoon') {
					$option = 'per_person';
					$price_per_unit = 0;
					$price_weight = 0;
					$price_per_person = str_replace(",",".",$price_per_person);
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4175_Articlelist_kwaliteitsslagerij_chris_en_carina(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4175-Articlelist-kwaliteitsslagerij-chris-en-carina.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 642;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4175;
	
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$price = trim($rows[2]);
			$cat = 'Kwaliteitsslagerij-chris-cat1';
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price != ''){
					$price_per_unit = $price;
				}else{
					$price_per_unit = 0;
				}
				$price_per_unit = str_replace(",",".",$price_per_unit);
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4139_Articlelist_Bart_en_Els(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4139-Articlelist-Bart-en-Els.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 963;
		$highestColumnIndex= 3;
		for ($row = 2; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4139;
	
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[1]);
			$pro_name = trim($rows[2]);
			$price = trim($rows[3]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price != ''){
					$price_per_unit = $price;
				}else{
					$price_per_unit = 0;
				}
	
				if ($cat == ''){
					$cat = 'Bart-en-els-cat1';
				}
	
				$price_per_unit = str_replace(",",".",$price_per_unit);
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	
	function imp_1829_Slagerij_Bovyn_Bereide_gerechten(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1829-Slagerij-Bovyn-Bereide-gerechten.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 772;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 1829;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[2]);
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='kg'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'st'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4126_vandamme_eddy(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4126-vandamme-eddy.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 329;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 4126;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='kg'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'piece'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'per person') {
					$option = 'per_person';
					$price_per_person = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_person = str_replace(",",".",$price_per_person);
					$price_per_unit = 0;
					$price_weight = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4077_Articlelist_foodbag(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4077-Articlelist-foodbag.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 82;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4077;
		$prod_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				if (!in_array($pro_name, $prod_array))
				{
						$prod_array[] = $pro_name;
						$price_per_unit = 0;
			
						$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
						$res = $this->db->get('categories')->result_array();
						if(!empty($res)){
							$cat_id = $res[0]['id'];
						}else{
							$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
							$cat_id = $this->db->insert_id();
						}
			
						$inser_array = array(
								'company_id'=>$comp_id,
								'categories_id'=>$cat_id,
								'subcategories_id'=> -1,
								'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
								'sell_product_option' =>$option,
								'price_per_unit' => $this->number2db($price_per_unit),
								'procreated'=>date('Y-m-d')
						);
						$this->db->insert('products',$inser_array);
				}
				else 
				{
					$prod_array[] = $pro_name;
				}
			}
			$count_rows++;
		}
	}
	
	function imp_2390_slagerijbrabo(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2390-slagerijbrabo.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 511;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 2390;
	
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2903_articlelist_bakkerij_Moeremans(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2903-articlelist_bakkerij-Moeremans.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 81;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 2903;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$price = trim($rows[3]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				if ($price){
					$price_per_unit = $price;
				}else{
					$price_per_unit = 0;
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1399_bakker_gert(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1399-bakker-gert.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 82;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 1399;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2322_articlelist_bakkerij_Geusens(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2322-articlelist-bakkerij-Geusens.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 203;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 2322;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_3629_Articlelist_DHollander_Leemans(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3629-Articlelist-DHollander-Leemans.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 923;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 3629;
	
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[2]);
			$price_per_unit = (trim($rows[1]) != '')?trim($rows[1]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($cat == ''){
					$cat = 'DHollander-Leemans-cat1';
				}
				if($type == 0){
					$option = 'weight_wise';
					$price_weight = (trim($rows[1]) != '')?trim($rows[1]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 1){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[1]) != '')?trim($rows[1]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4094_delepeleer(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4094-delepeleer.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 308;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4094;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4095_frankys_bakery(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4095-frankys-bakery.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 166;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4095;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[2]);
			$price_per_unit = (trim($rows[1]) != '')?trim($rows[1]):0;
			$price_per_unit = trim(str_replace("euro",".",$price_per_unit));
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				if ($cat == '') {
					$cat = 'frankys-bakery-cat1';
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4140_lijst_Buso_De_Brug(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4140-lijst-Buso-De-Brug.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 42;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4140;
		
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$sub_cat = trim($rows[2]);
			$option = 'per_unit';
			$price_per_unit = 0;
	
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if($sub_cat == ''){
				$sub_cat_id = -1;
			}
			elseif ($sub_cat != '') {
				$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
				$r_sub = $this->db->get('subcategories')->result_array();
				if(!empty($r_sub)){
					$sub_cat_id = $r_sub[0]['id'];
				}else{
					$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
					$sub_cat_id = $this->db->insert_id();
				}
			}
	
			$inser_array = array(
					'company_id'=>$comp_id,
					'categories_id'=>$cat_id,
					'subcategories_id'=> $sub_cat_id,
					'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
					'sell_product_option' =>$option,
					'price_per_unit' => $this->number2db($price_per_unit),
					'procreated'=>date('Y-m-d')
			);
			$this->db->insert('products',$inser_array);
			$count_rows++;
		}
	}
	
	function imp_4153_articlelist_bakkerij_stessens(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4153-articlelist-bakkerij-stessens.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 163;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4153;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4176_t_charcuterietje(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4176-t charcuterietje.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 405;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 4176;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = explode('/',trim($rows[4]));
			$type = $type[1];
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($cat == ''){
					$cat = 'DHollander-Leemans-cat1';
				}
				if($type == 'kg'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'Stk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4140_articlelist_Buso_De_Brug_soepen(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/articlelist_Buso_De_Brug_soepen.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 27;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4140;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
					
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	

	function imp_4183_Articlelist_bakkerij_Kiosk(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4183-Articlelist-bakkerij-Kiosk.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			

		$highestRow = 104;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";

	
	//	print_r($worksheet_arr0);die();
		$comp_id = 1189;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$price_per_unit = trim($rows[2]);
			$cat = trim($rows[3]);
			
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				if($price_per_unit == '')
				{
					$price_per_unit = 0;
				}
				if($cat == ''){
					$cat = 'slagerij-Verstappen-cat1';
				}
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2320_Artikellijst_bakkerij_Edelweiss(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2320-Artikellijst-bakkerij-Edelweiss.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 272;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		echo "<pre>";
		print_r($worksheet_arr0);
		die();
		$comp_id = 2320;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='Kg'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'Stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'Persoon') {
					$option = 'per_person';
					$price_per_person = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_person = str_replace(",",".",$price_per_person);
					$price_per_unit = 0;
					$price_weight = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4166_Articlelist_slagerij_uyttersprot(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4166-Articlelist-slagerij-uyttersprot.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 591;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		echo "<pre>";
		print_r($worksheet_arr0);
		die();
		$comp_id = 4166;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='KG/kilogram'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'ST/stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4179_Articlelist_slagerij_Van_den_Broeck(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4179-Articlelist-slagerij-Van-den-Broeck.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 400;
		$highestColumnIndex= 5;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		echo "<pre>";
		print_r($worksheet_arr0);
		die();
		$comp_id = 4179;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$sub_cat = trim($rows[5]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='KG/kilogram'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'ST/stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}

	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
				
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4137_bakkerij_Gevaert_Jan_Knokke_Heist(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4137-Articlelist-bakkerij-Gevaert-Jan-Knokke-Heist.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 281;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4137;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
				
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4180_Bakkerij_Ambachtelijke_bakker_Tessenderlo(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4180-Bakkerij-Ambachtelijke-bakker-Tessenderlo.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 793;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
	
		//print_r($worksheet_arr0);die();
		$comp_id = 4180;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
	
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$price_per_unit = 0;
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4183_Articlelist_bakkerij_Kiosk1(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4183-Articlelist-bakkerij-Kiosk.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 104;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4183;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
			$exploded_array = explode('/',$rows[2]);
			
			$price_per_unit = trim($exploded_array[0]);
			$price_weight = 0;
			$type = trim($exploded_array[1]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				if($cat == '')
				{
					$cat = 'bakkerij-Kiosk-cat1';
				}
				
				if($type =='KG'){
					$option = 'weight_wise';
					$price_weight = (trim($exploded_array[0]) != '')?trim($exploded_array[0]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
				}
				elseif ($type == 'per_unit'){
					$option = 'per_unit';
					$price_per_unit = (trim($exploded_array[0]) != '')?trim($exploded_array[0]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
				}
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1763_Articlelist_MelicatessenSh1741(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1763-Articlelist-MelicatessenSh1741.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 1634;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		echo "<pre>";
		print_r($worksheet_arr0);die();
		$comp_id = 1763;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);;
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$price_per_unit = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				if($cat == '')
				{
					$cat = 'MelicatessenSh-cat1';
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4098_bakkerij_Yves(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4098-bakkerij-Yves.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 131;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
	//	echo "<pre>";
	//	print_r($worksheet_arr0);die();
		$comp_id = 4098;
	
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[2]);
			$price_per_unit = (trim($rows[1]) != '')?trim($rows[1]):0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if ($type == 'per_unit'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[1]) != '')?trim($rows[1]):0;
					$price_per_person = 0;
				}
				elseif ($type == 'per_persoon') {
					$option = 'per_person';
					$price_per_unit = 0;
					$price_per_person = (trim($rows[1]) != '')?trim($rows[1]):0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(strtolower($pro_name)),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4182_Bakkerij_De_Vreese(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4182-Bakkerij-De-Vreese.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 100;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4182;
	
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$art_no = trim($rows[0]);
			$type = trim($rows[4]);
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
			$price_weight = 0;
	
			if(strlen($pro_name) > 0){
	
				if ($type == 'per_unit'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
				}
				if($type =='KG'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(strtolower($pro_name)),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2810_Articlelist_slagerij_thyssen(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2810-Articlelist-slagerij-thyssen.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 1522;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 2810;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[4]);
			$cat_id = 0;
			$option = 'per_unit';
			
			$type = trim($rows[3]);
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
			$price_weight = 0;
	
			if(strlen($pro_name) > 0){
	
				if ($type == 'ST/stuk'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
				}
				if($type =='KG/kilogram'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(strtolower($pro_name)),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4186_articlelist_slagerij_vancauwenbergh(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4186-articlelist-slagerij-vancauwenbergh.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 882;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
	//	echo "<pre>";
	//	print_r($worksheet_arr0);
	//	die();
		$comp_id = 4186;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$type = trim($rows[4]);
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
			$price_weight = 0;
			$price_per_person = 0;
	
			if(strlen($pro_name) > 0){
	
				if($type =='KG'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_person = 0;
				}
				elseif ($type == 'persoon') {
					$option = 'per_person';
					$price_per_person = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_per_person = str_replace(",",".",$price_per_person);
					$price_weight = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_Brood_en_Banket_Vanroy(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/Brood-en-Banket-Vanroy.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 248;
		$highestColumnIndex= 0;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 4107;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = 'Brood-en-Banket-cat1';
			$price_per_unit = 0;
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_2687_Articlelist_bakkerij_Seykens(){
			
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
			
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2687-Articlelist-bakkerij-Seykens.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
			
			
		$allSheetName=$objPHPExcel->getSheetNames();
			
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
			
		$highestRow = 121;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
			
		//echo "<pre>";
		//print_r($worksheet_arr0);die();
		$comp_id = 2687;
		$pro_arr = array();
		$product_array = array();
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$price_per_unit = 0;
			$cat_id = 0;
			$option = 'per_unit';
			if(strlen($pro_name) > 0){
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_1240_allergenen_PRALINES(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1240-allergenen-PRALINES.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 69;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 1240;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$sub_cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
	
			if(strlen($pro_name) > 0){
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>$sub_cat,'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4181_slagerij_gunther_schepdaal(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4181-Articlelist-slagerij-gunther-schepdaal.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 478;
		$highestColumnIndex= 3;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 4181;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[1]);
	
			if(strlen($pro_name) > 0){
				
				if ($price_per_unit == '') {
					$price_per_unit = 0;
				}
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4194_articlelist_chocolatier_ardelis(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4194-articlelist-chocolatier-ardelis.ods';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 45;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		echo "<pre>";
		print_r($worksheet_arr0);
		die();
		$comp_id = 4194;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = 'chocolatier-ardelis-cat1';
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
	
			if(strlen($pro_name) > 0){
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_785_bakkerij_coppe(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/785-bakkerij-coppe.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 124;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 785;
	
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
	
			if(strlen($pro_name) > 0){
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db($price_per_unit),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	
	function imp_4061_ARTICLELIST_PATISSERIE_DELUE(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4061-ARTICLELIST-PATISSERIE-DELUE.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 401;
		$highestColumnIndex= 2;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
		//echo "<pre>";
		//print_r($worksheet_arr0);
		//die();
		$comp_id = 4061;
	
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = 'patisserie-delue-cat1';
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[2]);
	
			if(strlen($pro_name) > 0){
	
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
	
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_bkkerij_enring_4204(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
	
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4204-articlelist bakkerij Enring Voorts.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
			
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
	
	
		$allSheetName=$objPHPExcel->getSheetNames();
	
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			
		$arr_data = array();  // here 5
	
		$highestRow = 14;
		$highestColumnIndex= 1;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}
			
		$worksheet_arr0 = $arr_data;
		$count_rows = 0;
	
				// print_r($worksheet_arr0);die();
	
		$comp_id = 4204;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
				
			$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			$res = $this->db->get('categories')->result_array();
			if(!empty($res)){
				$cat_id = $res[0]['id'];
			}else{
				$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
				$cat_id = $this->db->insert_id();
			}
	
			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes($pro_name),
						'sell_product_option' =>$option,
						'procreated'=>date('Y-m-d')
				);
				// print_r( $inser_array );die;
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4318_ARTICLELIST_patisserie_meheus(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4318-articlelist ptisserie Meheus.xlsx';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);

		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4


		$allSheetName=$objPHPExcel->getSheetNames();

		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

		$arr_data = array();  // here 5

		$highestRow = 126;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}

		$worksheet_arr0 = $arr_data;
		$count_rows = 0;

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4318;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			if( $cat == '' ) {
				$cat = 'patisserie-meheus-cat1';
			}
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[4]);

			if(strlen($pro_name) > 0){

				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace("€","",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}

			$count_rows++;
		}
	}

	function imp_4339_ARTICLELIST_ecofood(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4339-Articlelist  Ecofoods.xls';
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);

		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4


		$allSheetName=$objPHPExcel->getSheetNames();

		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
		//$highestRow = $objWorksheet->getHighestRow(); // here 5
		//$highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
		//$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

		$arr_data = array();  // here 5

		$highestRow = 425;
		$highestColumnIndex= 4;
		for ($row = 1; $row <= $highestRow; ++$row) {
			for ($col = 0; $col <= $highestColumnIndex; ++$col) {
				$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				if(is_array($arr_data) ) {
					$arr_data[$row-1][$col]=$value;
				}
			}
		}

		$worksheet_arr0 = $arr_data;
		$count_rows = 0;

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4339;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[3]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[2]);

			if(strlen($pro_name) > 0){

				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'pro_art_num'=> $art_no,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
}
?>