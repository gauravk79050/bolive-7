<?php
class Excel_import_new extends CI_Controller
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
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}



	function imp_1407_part(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1407-articlelist-charcuterie-bakkerij-Bierinckx.xls';
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

		$highestRow = 376;
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
		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 1407;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat_id = '';

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
						'pro_art_num'=> $art_no,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

		function imp_4326_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4326-articlelist.ods';

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
		$highestRow = 613;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4326;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);

			$cat = trim($rows[2]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4328_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4328-articlesli.xls';

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
		$highestRow = 723;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4328;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = trim($rows[3]);
			$cat = trim($rows[2]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[4]) != '')?trim($rows[4]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4330_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4330-articleslijs.xlsx';

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
		$highestRow = 98;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4330;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4282_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4282-Articlelist.xlsx';

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
		$highestRow = 639;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4282;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = '';
			$cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_4304_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4304-ARTIKELLIJ.xlsx';

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
		$highestRow = 310;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4304;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$sub_cat = '';
			$cat = trim($rows[1]);
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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_1992_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1992-articlelist-bakkerij-Gilissen-tongeren.xlsx';

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
		$highestRow = 276;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 1992;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = '';
			$cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;


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
						'pro_art_num'=> $art_num,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_4327_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4327-Artikellijst-frituur-t-Klaverke.xlsx';

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
		$highestRow = 218;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4327;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
			$price_per_unit = 0;
			if( $cat == '' ) {
				$cat = 'frituur-t-Klaverke-cat1';
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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_1789_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1789-Artikellijst-Govaert.xlsx';

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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 1789;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = '';
			$cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;


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
						'pro_art_num'=> $art_num,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4312articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4312-restaurant.xlsx';
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

		$highestRow = 77;
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
		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4312;
		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[2]);
			$sub_cat = '';
			$cat_id = 0;
			$option = 'per_unit';

			$price_per_unit = trim($rows[1]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace("€","",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4322_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4322-Articlelis.xlsx';

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
		$highestRow = 108;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4322;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);

			$cat = trim($rows[3]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4323_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4323-articlelist.xls';

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
		$highestRow = 1051;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4323;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = trim($rows[4]);
			$cat = trim($rows[3]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4325_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4325-Articleslijst.xlsx';

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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4325;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);

			$cat = trim($rows[2]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	/*function imp_4326_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4326-articlelist.ods';

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
		$highestRow = 613;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4326;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);

			$cat = trim($rows[2]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}*/

	/*function imp_4328_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4328-articlesli.xls';

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
		$highestRow = 723;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4328;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = trim($rows[3]);
			$cat = trim($rows[2]);
			$sub_cat = '';
			$option = 'per_unit';
			$price_per_unit = (trim($rows[4]) != '')?trim($rows[4]):0;;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'pro_art_num'=> $art_no,
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}*/

	/*function imp_4330_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4330-articleslijs.xlsx';

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
		$highestRow = 98;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4330;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
			$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;


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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_1992_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1992-articlelist-bakkerij-Gilissen-tongeren.xlsx';

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
		$highestRow = 276;
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

		// echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 1992;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = '';
			$cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;


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
						'pro_art_num'=> $art_num,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_4327_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4327-Artikellijst-frituur-t-Klaverke.xlsx';

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
		$highestRow = 218;
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

		// echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4327;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$sub_cat = trim($rows[2]);
			$cat = trim($rows[1]);
			$option = 'per_unit';
			$price_per_unit = 0;
			if( $cat == '' ) {
				$cat = 'frituur-t-Klaverke';
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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}*/

	function imp_4335_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4335-articlelist-Defaaz-jacquinet.xls';

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
		$highestRow = 334;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4335;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = trim($rows[3]);
			$cat = trim($rows[2]);
			$option = 'per_unit';
			$price_per_unit = trim($rows[4]);

			if(strlen($pro_name) > 0){

				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> addslashes(mb_strtolower($cat,'UTF-8')), 'created'=> date('Y-m-d')));
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_num,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4337_articlelist1(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4337-articlelist-boulagerie-au-four-et-au-moulin.xlsx';

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
		$highestRow = 41;
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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4337;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[1]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = trim($rows[4]);

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
						'pro_art_num'=> $art_num,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				//$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_bkkerij_enring_4204(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4204-articlelist-bakkerij-Enring-Voorts.xlsx';
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

		//echo "<pre>";
	//	print_r($worksheet_arr0);die();

		$comp_id = 4204;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
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

			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $price_per_unit,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4318_ARTICLELIST_patisserie_meheus(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4318-articlelist-ptisserie-Meheus.xlsx';
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

		//echo "<pre>";print_r($worksheet_arr0);die();

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

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4339-Articlelist-Ecofoods.xls';
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

		//echo "<pre>";print_r($worksheet_arr0);die();
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

	function imp_4329_ARTICLELIST_ecofood(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4329-articlelist-bakkerij-Emmerix-Lanaken.xlsx';
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

		$highestRow = 1161;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 4329;

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

	function imp_1494_a(){
		ini_set('memory_limit', '512M');
		$this->load->library("excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1494-Articlelist-bakkerij-wijckmans.xlsx';
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

		$highestRow = 854;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 1494;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
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

	function imp_4350_a(){
		ini_set('memory_limit', '512M');
		$this->load->library("excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4350-Produits-Bakkerij-Lefevre.xlsx';
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

		$highestRow = 91;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 4350;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  0;

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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_edelweiss_2672(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2672-Edelweiss-artikellijst.xlsx';
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

		$highestRow = 7;
		$highestColumnIndex=3;
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

		$comp_id = 2672;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$type = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = trim($rows[2]);
			if(strlen($pro_name) > 0){

				if($type == 'per_weight'){
					$option = 'weight_wise';
					$price_weight = trim($rows[2]);
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'price_weight' => $this->number2db(str_replace(",",".",$price_weight/1000)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_thienpont_3108(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3108-articlelistthienpont.xlsx';
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

		$highestRow = 201;
		$highestColumnIndex=3;
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

		$comp_id = 3108;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$type = trim($rows[3]);
			$price_per_unit = trim($rows[2]);
			if(strlen($pro_name) > 0){

				if($type =='per_weight'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'per_unit'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[2]) != '')?trim($rows[2]):0;
					$price_per_unit = str_replace(",",".",$price_per_unit);
					$price_weight = 0;
					$price_per_person = 0;
				}
				elseif ($type == 'per_person') {
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


	function imp_adam_3081(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/3081-bakkerij-adam-productenlijst.xlsx';
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
		$highestColumnIndex=2;
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

		$comp_id = 3081;
		foreach ($worksheet_arr0 as $rows){

			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[2]);
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
						'price_per_unit' => $price_per_unit,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_swaene_artikellist_2803(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2803-Bakkerij-De-Swaene-articlelist.xlsx';
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

		$highestRow = 110;
		$highestColumnIndex=1;
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

		$comp_id = 2803;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
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

			if(strlen($pro_name) > 0){
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $price_per_unit,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4332_ARTICLELIST_Bakkerij_Beckers(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4332-Bakkerij_Beckers_artikellijst.xlsx';
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

		$highestRow = 1013;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 4332;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[3]);
			$price_per_unit = $price_per_unit ? $price_per_unit : 0;
			if(strlen($pro_name) > 0 && $pro_name != 'zz' ){

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

	function imp_4315_ARTICLELIST_Bakkerij_Beckers(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4315-Bekaert_artikellijst.xlsx';
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

		$highestRow = 57;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 4315;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			// $cat = trim($rows[1]);
			$cat = 'Bekaert_cat1';
			$cat_id = 0;
			$type = strtolower( trim($rows[3]) );
			// $option = 'per_unit';
			$option = 'per_unit';
			$price_per_unit =  trim($rows[3]);
			if(strlen($pro_name) > 0 && $pro_name != 'zz' ){

				if($type =='weight'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_per_unit = 0;
				}
				elseif ($type == 'unit'){
					$option = 'per_unit';
					$price_per_unit = (trim($rows[3]) != '')?trim($rows[3]):0;
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

	function imp_4351_ARTICLELIST_theunis(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4351-Bakkerij_Theunis_artikelijst.xlsx';
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

		$highestRow = 3053;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 4351;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[3]);
			$price_per_unit = $price_per_unit ? $price_per_unit : 0;
			if(strlen($pro_name) > 0 && $pro_name != 'zz' ){

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

	function imp_4352_vervisch(){
		ini_set('memory_limit','512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4352-Articlelist-Bakkerij-Vervisch-Niville.xlsx';

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

		//echo "<pre>";print_r($worksheet_arr0);die();

		$pro_arr = array();
		$comp_id = 4352;

		$num = 0;
		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$sub_cat = trim($rows[3]);
			$cat = trim($rows[2]);
			$cat = $cat ? $cat : 'Vervisch-Niville_cat1';
			$option = 'per_unit';
			$price_per_unit = trim($rows[4]);
			$price_per_unit = $price_per_unit ? $price_per_unit : 0;
			if(strlen($pro_name) > 0){

				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> addslashes(mb_strtolower($cat,'UTF-8')), 'created'=> date('Y-m-d')));
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'pro_art_num'=> $art_num,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => str_replace(",",".",$this->number2db($price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4338_ARTICLELIST(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4338-artikellijst-berteneniris.xlsx';
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

		$highestRow = 362;
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

		//echo "<pre>";print_r($worksheet_arr0);die();
		$comp_id = 4338;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[3]);
			$price_per_unit = $price_per_unit ? $price_per_unit : 0;
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

	function imp_4252_boulangerie_sohet(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4252-boulangerie-sohet-artikellijst.xlsx';
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

		$highestRow = 34;
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
		$comp_id = 4252;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  0;

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
				// print_r( $inser_array );die;
			}
			$count_rows++;
		}
	}

	function imp_4275_spar_frooninckx(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4275-artikellijst_spar_frooninckx.xlsx';
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

		$highestRow = 712;
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
		$comp_id = 4275;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[1]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  0;

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

	function imp_4353_De_Smet_Wouter(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4353-De-Smet-Wouter_Artikellijst.xlsx';
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

		$highestRow = 346;
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
		$comp_id = 4353;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$price_per_unit = $price_per_unit ? $price_per_unit : 0;
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

		function imp_4354_Panibel_artikellijst(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4354-Panibel-artikellijst.xlsx';
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

		$highestRow = 360;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4354;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[2]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4355_Eric_Frankar(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4355-Produits-Eric-Frankar.xlsx';
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

		$highestRow = 88;
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
		$comp_id = 4355;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[2]);
			$price_per_unit = $price_per_unit ? $price_per_unit : 0;
			$type = trim( $rows[3] );
			$price_weight = 0;
			if(strlen($pro_name) > 0){

				if($type == 'Gewicht'){
					$option = 'weight_wise';
					$price_weight = trim(str_replace(',','.',$rows[2]));
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
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'price_weight' => $this->number2db($price_weight/1000),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4356_Latinne(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4356-Produits-Latinne.xlsx';
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

		$highestRow = 209;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4356;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4358_Bakkerij_Schatteman(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4358-Bakkerij-Schatteman.xlsx';
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4358;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[2]);
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4363_Vayamundo_Oostende(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4363-Artikellijst_Vayamundo_Oostende.xlsx';
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

		$highestRow = 223;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4363;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$sub_cat = trim($rows[1]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}


				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4364_Vayamundo_Oostende_kopie(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4364-Artikellijst_Vayamundo_Oostende-kopie.xlsx';
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

		$highestRow = 223;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4364;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$sub_cat = trim($rows[1]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}


				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4365_slagerij_Calbert(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4365-artikellijst-slagerij-Calbert.xlsx';
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

		$highestRow = 198;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4365;

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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4366_boulangerie_FERYN(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4366-articles-boulangerie-FERYN.xlsx';
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

		$highestRow = 372;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4366;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_1638_de_vleeshoeve(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1638-artikellijst-de-vleeshoeve.xlsx';
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

		$highestRow = 2464;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 1638;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
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

	function imp_4327_frituur_tklaverke(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4327-artikellijst-frituur-tklaverke.xlsx';
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/

		$comp_id = 4327;

		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4413_Au_Patissier(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4413-liste-produits-Au-Patissier.xlsx';
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

		$highestRow = 123;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/

		$comp_id = 4413;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_1449_lisette_grimbergen(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1449-artikellijst-bakkerij-lisette-grimbergen.xlsx';
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

		$highestRow = 318;
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

		$comp_id = 1449;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);
			$cat_id 	= 0;
			$type 		= trim($rows[4]);
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			$price_per_unit = str_replace("€", '', $price_per_unit );
			$price_weight	= 0;
			$price_per_person = 0;
			if(strlen($pro_name) > 0){
				
				if($type == 'per_weight'){
					$option = 'weight_wise';
					$price_weight = trim($rows[3]);
					$price_weight = str_replace("€", '', $price_weight );
					$price_per_unit = 0;
				} else if ($type == 'per_person') {
					$option = 'per_person';
					$price_per_person = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_per_person = str_replace(",",".",$price_per_person);
					$price_per_person = str_replace("€", '', $price_per_person );
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

				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4415_Crosset(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4415-artikellijst-Crosset.xlsx';
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

		$highestRow = 346;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4415;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[0]);
			$cat 		= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
			echo $count_rows;
		}
	}

	function imp_4416_bakkerij_straet(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4416-bakkerij-straet.xlsx';
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

		$highestRow = 153;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4416;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_1753_bvba_lints(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1753-bvba-lints.xlsx';
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

		$highestRow = 877;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/

		$comp_id = 1753;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4321_Flavour_artikellijst(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4321-Full-Flavour-artikellijst.xlsx';
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
		$comp_id = 4321;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[3]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4359_Stefaan_Vandewalle(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4359-Artikellijst-Bakkerij-Stefaan-Vandewalle.xlsx';
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/

		$comp_id = 4359;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4419_kris_declercq(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4419-bakkerij-kris-declercq.xlsx';
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

		$highestRow = 353;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/

		$comp_id = 4419;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4420_Delhaize_Poperinge(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4420-AD-Delhaize-Poperinge.xlsx';
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/

		$comp_id = 4420;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			if( $price_per_unit == '' ) {
				$price_per_unit = 0;
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

	function imp_4421_bakkerij_misseeuw(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4421-bakkerij-misseeuw.xlsx';
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4421;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_1054_artikellijst(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1054_artikellijst.xlsx';
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

		$highestRow = 508;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();
		$comp_id = 1054;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4424_artikellijst(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4424_Arti.xlsx';
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

		$highestRow = 263;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();
		$comp_id = 4424;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[0]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4425_amaryllis(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4425-amaryllis.xlsx';
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

		$highestRow = 327;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4425;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[0]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4426_Debeur(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4426-Debeur.xlsx';
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
		$comp_id = 4426;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[0]);
			$cat 		= trim($rows[1]);
			$sub_cat_id = -1;
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4427_Grandjean_Philippe(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4427-Grandjean-Philippe.xlsx';
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

		$highestRow = 87;
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

		/*echo "<pre>";
		print_r($worksheet_arr0);
		die();*/
		$comp_id = 4427;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4174_Grandjean_Philippe(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4174Baevegems.xlsx';
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

		$highestRow = 23;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();
		$comp_id = 4174;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=> $art_no,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => 0,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4288_Grandjean_Philippe(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4288hemelsoet.xlsx';
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

		$highestRow = 215;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();
		$comp_id = 4288;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => 0,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4259(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4259.xlsx';
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

		$highestRow = 647;
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

		
		$comp_id = 4259;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$price_per_unit	= trim($rows[2]);
			$cat 		= trim($rows[3]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			
			if(strlen($pro_name) > 0){
				
				$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
				$res = $this->db->get('categories')->result_array();
				if(!empty($res)){
					$cat_id = $res[0]['id'];
				}else{
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}

				// if($sub_cat == ''){
				// 	$sub_cat_id = -1;
				// }
				// elseif ($sub_cat != '') {
				// 	$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
				// 	$r_sub = $this->db->get('subcategories')->result_array();
				// 	if(!empty($r_sub)){
				// 		$sub_cat_id = $r_sub[0]['id'];
				// 	}else{
				// 		$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
				// 		$sub_cat_id = $this->db->insert_id();
				// 	}
				// }

				$inser_array = array(
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit))
				);
				$this->db->where(array('company_id'=>$comp_id,'categories_id'=>$cat_id,'subcategories_id'=>-1,'pro_art_num'=>$art_no));
				$this->db->update('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_1430_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/1430-artikellijst-hanssens.xlsx';
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

		$highestRow = 136;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();
		$comp_id = 1430;

		foreach ($worksheet_arr0 as $rows){
			$art_no 	= trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => 0,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4124_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4124BakkerijHelsen.xlsx';
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

		$highestRow = 43;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();
		$comp_id = 4124;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit	= 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => 0,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4433(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4433.xlsx';
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

		$highestRow = 190;
		$highestColumnIndex=3;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4433;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$type = trim($rows[3]);
			$option = 'per_unit';
			$price_per_unit = trim($rows[2]);
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
					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> -1,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'price_weight' => $this->number2db(str_replace(",",".",$price_weight/1000)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	function imp_4443(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4443-De-Coster-Artikellijst.xlsx';
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

		$highestRow = 359;
		$highestColumnIndex=3;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4443;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = trim($rows[3]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' => $price_per_unit,
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_2362(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2362-Vanderveken.xlsx';
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
		$highestColumnIndex=3;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 2362;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4216(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4216-broodenbanketmarc.xlsx';
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

		$highestRow = 155;
		$highestColumnIndex=3;
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

		$comp_id = 4216;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4334(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4334-bakkerij-marka.xlsx';
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

		$highestRow = 674;
		$highestColumnIndex=3;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4334;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4442(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4442-de-appel.xlsx';
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
		$highestColumnIndex=3;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4442;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4448(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4448-rieten-dakje-artikellijst.xlsx';
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

		$highestRow = 147;
		$highestColumnIndex=3;
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

		$comp_id = 4448;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_4453(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4453-artikellijst-koert-tinne-hemiksem.xlsx';
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

		$highestRow = 110;
		$highestColumnIndex=3;
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

		$comp_id = 4453;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_2242le(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2242-Le-Muselet.xlsx';
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

		$highestRow = 173;
		$highestColumnIndex=1;
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

		$comp_id = 2242;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4231(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4231-bakkerij-taveirne-artikellijst.xlsx';
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

		$highestRow = 50;
		$highestColumnIndex=2;
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

		$comp_id = 4231;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat =  trim($rows[1]);;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4454(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4454-Chez-Dominique.xlsx';
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
		$highestColumnIndex=1;
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

		$comp_id = 4454;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4455(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4455-Bakkerij-Antoine.xlsx';
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

		$highestRow = 379;
		$highestColumnIndex=3;
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

		$comp_id = 4455;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4456(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4456-Baert-Daniel.xlsx';
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

		$highestRow = 96;
		$highestColumnIndex=1;
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

		$comp_id = 4456;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4484(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4484-Gempemolen.xlsx';
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

		$highestRow = 176;
		$highestColumnIndex=3;
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

		$comp_id = 4484;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit =  trim($rows[3]);
			$sub_cat =  trim($rows[1]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_2416(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2416-Traiteur-Verdure.xlsx';
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

		$highestRow = 97;
		$highestColumnIndex=1;
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

		$comp_id = 2416;
		foreach ($worksheet_arr0 as $rows){
			$art_no = '';
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_2592(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2592-rtikellijstBaerts-Geboes.xlsx';
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

		$highestRow = 169;
		$highestColumnIndex=2;
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

		$comp_id = 2592;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4458(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4458-Noiret.xlsx';
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

		$highestRow = 224;
		$highestColumnIndex=2;
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

		$comp_id = 4458;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
	function imp_4461(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4461-traiteurdidier.xlsx';
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

		$highestRow = 65;
		$highestColumnIndex=1;
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

		$comp_id = 4461;
		foreach ($worksheet_arr0 as $rows){
			$art_no = '';
			$pro_name = trim($rows[1]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4491(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4491.xlsx';
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
		$highestColumnIndex=2;
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

		$comp_id = 4491;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4447(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4447-Mandeldaele.xlsx';
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

		$highestRow = 547;
		$highestColumnIndex=2;
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

		$comp_id = 4447;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4492(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4492-Boeverbos.xlsx';
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

		$highestRow = 699;
		$highestColumnIndex=2;
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

		$comp_id = 4492;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[2]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4498(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4498-Zandlopertje.xlsx';
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

		$highestRow = 57;
		$highestColumnIndex=3;
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

		$comp_id = 4498;
		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name = trim($rows[1]);
			$cat = trim($rows[2]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = trim($rows[3]);
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
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
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4502(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4502-bakkerij-dierckx.xlsx';
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

		$highestRow = 304;
		$highestColumnIndex=1;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4502;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}


	function imp_4504(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4504-Demerhof.xlsx';
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

		$highestRow = 67;
		$highestColumnIndex=1;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4504;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[0]);
			$cat = trim($rows[1]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4507(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4507-Healthyway.xlsx';
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
		$highestColumnIndex=2;
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

		// echo "<pre>";
		// print_r($worksheet_arr0);die();

		$comp_id = 4507;
		foreach ($worksheet_arr0 as $rows){
			$pro_name = trim($rows[2]);
			$cat = trim($rows[0]);
			$cat_id = 0;
			$option = 'per_unit';
			$price_per_unit = 0;
			$sub_cat = trim($rows[1]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

			
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' =>  $this->number2db(str_replace(",",".",$price_per_unit)),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4510(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4510-boucherie-Maistriaux-2017.xlsx';
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

		$highestRow = 207;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4510;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$type 		= trim($rows[4]);
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			$price_per_unit = str_replace("€", '', $price_per_unit );
			$price_weight	= 0;
			$price_per_person = 0;
			if(strlen($pro_name) > 0){
				
				if($type == 'KG'){
					$option = 'weight_wise';
					$price_weight = trim($rows[3]);
					$price_weight = str_replace(",",".",$price_weight);
					$price_weight = str_replace("€", '', $price_weight );
					$price_per_unit = 0;
				} else if ($type == 'PC') {
					$option = 'per_person';
					$price_per_person = (trim($rows[3]) != '')?trim($rows[3]):0;
					$price_per_person = str_replace(",",".",$price_per_person);
					$price_per_person = str_replace("€", '', $price_per_person );
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

				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'pro_art_num'=> $art_no,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4519(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4519-Buulse-Bowling.xlsx';
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

		$highestRow = 157;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4519;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_2633(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/2633-Artikellijst-Boeckaert.xlsx';
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

		$highestRow = 213;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 2633;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[2]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4511(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4511-Joelle Massaux.xlsx';
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

		$highestRow = 257;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4511;

		foreach ($worksheet_arr0 as $rows){
			$art_num = trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[4]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'pro_art_num'=>$art_no,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4505(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4505-MOLENHOF.xlsx';
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

		$highestRow = 184;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4505;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4476(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4476-PLU-Devigne.xlsx';
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

		$highestRow = 99;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4476;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[0]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4460(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4460-Boulangerie_Bodart.xlsx';
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4460;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4509(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4509-croqnmore.xlsx';
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

		$highestRow = 60;
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
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4509;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[2]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4534(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4534-qornr.xlsx';
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

		$highestRow = 28;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4534;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num = trim($rows[0]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4212_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4212-bakker-rudy.xlsx';
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

		$highestRow = 334;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4212;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			$art_num = trim($rows[0]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4462_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4462-wzc-jacky-maes.xlsx';
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

		$highestRow = 157;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4462;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num = '';
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4470_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4470-Tarterie-de-Buzet.xlsx';
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

		$highestRow = 232;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4470;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);;
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			$art_num = '';
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4536_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4536-traiteur-claude.xlsx';
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

		$highestRow = 249;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4536;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  trim($rows[0]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4513(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4513-Briclet.xlsx';
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

		$highestRow = 459;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4513;

		foreach ($worksheet_arr0 as $rows){
			$art_no = trim($rows[0]);
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
			$cat_id 	= 0;
			$type 		= trim($rows[4]);
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$price_weight	= 0;
			$price_per_person = 0;
			if(strlen($pro_name) > 0){
				
				if($type == 'Kg'){
					$option = 'weight_wise';
				} else if ($type == 'Pc') {
					$option = 'per_person';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'pro_art_num'=> $art_no,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
						'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}

	function imp_4514_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4514-Niesten.xlsx';
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

		$highestRow = 129;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4514;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4541_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4541-Buyl-Ninove.xlsx';
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

		$highestRow = 488;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4541;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  trim($rows[0]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4039_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4039_KS_Nico_Adegem.xlsx';
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

		$highestRow = 443;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4039;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  trim($rows[0]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4451_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4451-Exelshof.xlsx';
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

		$highestRow = 89;
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4451;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4545_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4545-Otuz.xlsx';
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

		// echo "<pre>";
		 print_r($worksheet_arr0);
		 die();

		$comp_id = 4545;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[3]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  trim($rows[0]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4546_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4546-Santa-Lucia.xlsx';
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

		$highestRow = 31;
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

		$comp_id = 4546;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= '';
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4549_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4549-bakker_erwin_scherpenheuvel.xlsx';
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

		$comp_id = 4549;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[3]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[4]);
			$art_num =  trim($rows[0]);
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_89_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/89-Frit-style.xlsx';
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

		$highestRow = 348;
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

		echo "<pre>";
		print_r($worksheet_arr0);
		die();

		$comp_id = 89;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = 0;
			$art_num =  '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_4555_art(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../../assets/excels/4555-De-Koerier.xlsx';
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

		$highestRow = 93;
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
		print_r($worksheet_arr0);
		die();

		$comp_id = 4555;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[2]);
			$cat 		= trim($rows[0]);
			$sub_cat 	= trim($rows[1]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[3]);
			$art_num =  '';
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
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}

				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_no,
						'subcategories_id'=> $sub_cat_id,
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

	function imp_common_art($list,$hig_row,$hig_col,$comp_id_dem){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount(); // here 4
		$allSheetName=$objPHPExcel->getSheetNames();
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); 
		$arr_data = array(); 

		$highestRow = $hig_row;
		$highestColumnIndex= $hig_col;
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

		$comp_id = $comp_id_dem;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[3]);
			$cat 		= trim($rows[1]);
			$sub_cat 	= trim($rows[2]);
			$cat_id 	= 0;
			$option 	= 'per_unit';
			$price_per_unit = trim($rows[4]);
			$art_num =  trim($rows[0]);
			$type 		= trim($rows[5]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
			}
			$price_per_unit = str_replace("€", '', $price_per_unit );
			$price_weight	= 0;
			$price_per_person = 0;
			if(strlen($pro_name) > 0){
				if($type == 'KG'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[4]) != '')?trim($rows[4]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_weight = str_replace("€", '', $price_weight );
					$price_per_unit = 0;
				} else if ($type == 'PC') {
					$option = 'per_person';
					$price_per_person = (trim($rows[4]) != '')?trim($rows[4]):0;
					$price_per_person = str_replace(",",".",$price_per_person);
					$price_per_person = str_replace("€", '', $price_per_person );
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
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> $sub_cat));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>'0','subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
				$inser_array = array(
						'company_id'=>$comp_id,
						'categories_id'=>$cat_id,
						'pro_art_num'=>$art_num,
						'subcategories_id'=> $sub_cat_id,
						'proname'=> addslashes(mb_strtolower($pro_name,'UTF-8')),
						'sell_product_option' =>$option,
						'price_per_unit' => $this->number2db(str_replace(",",".",$price_per_unit)),
							'price_weight' => $this->number2db($price_weight/1000),
						'price_per_person' => $this->number2db($price_per_person),
						'procreated'=>date('Y-m-d')
				);
				$this->db->insert('products',$inser_array);
			}
			$count_rows++;
		}
	}
}

?>