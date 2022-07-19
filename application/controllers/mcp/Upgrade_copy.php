<?php
class Upgrade_copy extends CI_Controller{

    var $tempUrl = '';
	var $template = '';
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		
		$this->tempUrl = base_url().'application/views/mcp';
		$this->template = "/mcp";
		$this->temp="/mcp/companies";
		
		$this->load->model('mcp/Mcompanies');
		$this->load->model('MFtp_settings');
        
		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');
		
		if( !$current_user || !$is_logged_in )
		  redirect('mcp/mcplogin','refresh');
	}
	
	function index()
	{
	    $this->update_client_files();
	}
			 
	function update_client_files()
	{
		$msg = '';
		if( $this->input->post('submit') )
		{
		    $company_ids = $this->input->post('company_id');
		
			if( !empty( $company_ids ) )
			{				
				$companies_ftp_details = $this->MFtp_settings->get_ftp_settings( array( 'shop_files_loc <>' => '', 'ftp_hostname <>' => '', 'ftp_username <>' => '', 'ftp_password <>' => '', 'access_permission' => 1 ) , array( 'company_id' => $company_ids ) );
				
				
				if( !empty($companies_ftp_details) )
				{
					$msg_arr = $this->upgrade_files( $companies_ftp_details );
					//$msg_arr = $this->upgrade_files( $companies_ftp_details );
					
					if( empty($msg_arr) )
					  $msg = array('success' => _('Success - FTP files of the selected companies are been updated successfully !')); 
					else
					  $msg = array('error' => implode(',<br />',$msg_arr) );  
				}
				else
				  $msg = array('error'=> _('Error - None of the companies have entered their full FTP Details !'));				
			}
			else
			{
				$msg = array('error'=> _('Error - Please select some companies to update their client files !'));
			}
		}
		
		$data['message'] = $msg;
		$data['company_ftp_settings'] = $this->Mcompanies->get_approved_company_ftp_settings();
		
		$data['tempUrl']=$this->tempUrl;		
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/client_list';
		$data['footer'] = $this->template.'/footer';
		
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view'); 
	}
	
	function upgrade_files( $companies_ftp_details )
	{
		$response = array();
		$seo_response = array();
		$search_products = array();
		$products = array();
		
		$pickup_url_key = array();
		$delivery_url_key = array();
		
		if( empty($companies_ftp_details) )
		  return false;
		else
		{
			 $this->load->library('ftp');
			
		    $error = array();
			
			foreach( $companies_ftp_details as $comp)
			{	
				$shop_files_loc = $comp->shop_files_loc;
				$ftp_hostname = $comp->ftp_hostname;
				$ftp_username = $comp->ftp_username;
				$ftp_password = $comp->ftp_password;
				$shop_url = $comp->shop_url;
				$company_id = $comp->company_id;
				
				$response_1 = $this->create_config_file( $shop_url, $company_id );
				$response_2 = $this->create_index_file( $shop_url, $company_id );

				if( !$response_1 || !$response_2 )
				{
				  $error[] = _('Problem on ').$ftp_hostname.' : '._('Can\'t create config/index file for this FTP server !');
				}
				else
				{
					//$updated_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/';
					$updated_file_loc = dirname(__FILE__).'/../../../online-bestellen/';
					
					//$org_root_files = array( 'new-index.html', 'new-config.php' );
					//$root_files = array( 'index.html', 'config.php' );
					
					//$org_js_files = array( 'obs-script.js.php', 'upgrade.php' );
					
					$org_json_files = array( 'obs_results_'.$company_id.'.json' );
					
					//$org_template_files = array( 'cart.html', 'change-password.html', 'checkout.html', 'edit-profile.html', 'login.html', 'productdetail.html', 'productlist.html', 'register.html', 'service.html' );
					
					//$org_includes_files = array( 'captcha.php', 'cart.php', 'functions.php', 'get-style.css.php', 'init.php', 'request.php', 'user.php' );
					//$org_includes_files = array( 'captcha.php', 'functions.php', 'get-style.css.php', 'init.php', 'request.php' );
					
					//$includes_files = array( 'captcha.php', 'cart.php', 'functions.php', 'get-style.css.php', 'init.php', 'request.php', 'user.php' );					
					//$includes_files = array( 'captcha.php', 'functions.php', 'get-style.css.php', 'init.php', 'request.php');
					// --- >> Connect FTP << --- //
					 $conn_id = ftp_connect( $ftp_hostname ) or die("Couldn't connect to $ftp_server"); 
					
					// --- >> Login FTP << --- //
					if ( @ftp_login($conn_id, $ftp_username, $ftp_password) )
					{
						if ( @ftp_chdir( $conn_id, $shop_files_loc ) ) 
						{
							//$dir_arr = array( 'images', 'includes', 'js', 'template', 'products' );  
							
							$dir_arr = array('products');
							
							// Getting directories and file names
							$files_in_dir = ftp_nlist($conn_id,".");
								
							// try to create the internal directories on ftp
							foreach( $dir_arr as $dir )
							{
								if(!in_array( $dir , $files_in_dir)){
									if ( @ftp_mkdir($conn_id, $dir) )
									{
										// DIR created
									}
									else
									{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('There was a problem while creating ')."'".$dir."'"._(' folder, on server ! May be it was already there.');
									}
								}
					
							}

							
							// --- >> Start updating ROOT files << --- //
							/* if( !empty($root_files) )
							foreach( $root_files as $id=>$rf )
							{
								if ( @ftp_put($conn_id, $rf, $updated_file_loc.$org_root_files[$id] , FTP_ASCII) )
								{							   
								   //@ftp_chmod($conn_id, 0777, $rf);	 // Set Read-Write permisssion  on the uploaded file.						   
								}
								else
								{
								   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').$rf._(' on server !');
								}
							}						
							 */
							
							// --- >> Start updating JS files << --- //
							/* if( !empty($org_js_files) )
							foreach( $org_js_files as $id=>$jf )
							{
								if ( @ftp_put($conn_id, 'js/'.$jf, $updated_file_loc.'js/'.$org_js_files[$id] , FTP_ASCII) )
								{							   
								   //@ftp_chmod($conn_id, 0777, $jf);	 // Set Read-Write permisssion  on the uploaded file.						   
								}
								else
								{
								   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'js/'.$jf._(' on server !');
								}
							}
							
							// --- >> Start updating template files << --- //
							if( !empty($org_template_files) )
							foreach( $org_template_files as $id=>$jf )
							{
								if ( @ftp_put($conn_id, 'template/'.$jf, $updated_file_loc.'template/'.$org_template_files[$id] , FTP_ASCII) )
								{
									//@ftp_chmod($conn_id, 0777, $jf);	 // Set Read-Write permisssion  on the uploaded file.
								}
								else
								{
									$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'template/'.$jf._(' on server !');
								}
							}
							
							// --- >> Start updating INCLUDES files << --- //
							if( !empty($includes_files) )
							foreach( $includes_files as $id=>$lf )
							{
								
								if ( @ftp_put($conn_id, 'includes/'.$lf, $updated_file_loc.'includes/'.$org_includes_files[$id] , FTP_ASCII) )
								{							   
								   //@ftp_chmod($conn_id, 0777, $lf);	 // Set Read-Write permisssion  on the uploaded file.						   
								}
								else
								{
								   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'includes/'.$lf._(' on server !');
								}
							} */
							
							// --- >> Start updating IMAGES files << --- //
							/* if( !empty($images_files) )
							foreach( $images_files as $id=>$imgf )
							{
								if ( @ftp_put($conn_id, 'images/'.$imgf, $updated_file_loc.'images/'.$org_images_files[$id] , FTP_ASCII) )
								{							   
								   //@ftp_chmod($conn_id, 0777, $imgf);	 // Set Read-Write permisssion  on the uploaded file.						   
								}
								else
								{
								   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'images/'.$imgf._(' on server !');
								}
							}
							 */
						}
						else
						{
							$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t change the directory to online-bestellen folder !');
						}
					}	
					else
					{
						$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t connect as  ').$ftp_username._(', login failed !');
					}
				} // End - If Config			
			}
			
			$response = $this->get_shop_content_json_new( $company_id );
			$response['shop_content'] = $this->get_shop_content_all_post( $company_id );
			
			$seo_pickup_data = $response['pickup'];
			

			foreach($seo_pickup_data as $key=>$seo_pickup)
			{
				foreach($seo_pickup as $k=>$seo_pick)
				{
					$seo_pickup_cat_product =  $seo_pick->product;
					$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
					$category_seo_key = strtolower(str_replace(' ', '_', $seo_pick->name));
			
					$seo_response[$category_seo_key] = $seo_pick;
					$seo_response[$category_seo_key]->slug = $category_seo_key;
					$seo_response[$category_seo_key]->product='';
					$seo_response[$category_seo_key]->sub_category='';
					if(isset($seo_pickup_cat_product))
					{
						foreach($seo_pickup_cat_product as $k=>$seo_pickup_product)
						{
							if(isset($seo_pickup_product))
							{
								$category_seo_product_key = strtolower(str_replace(' ', '_', $seo_pickup_product->proname));
			
								if (in_array($category_seo_product_key, $pickup_url_key))
								{
									$i=1;
									while(in_array($category_seo_product_key, $pickup_url_key))
									{
										$category_seo_product_key = $category_seo_product_key.'_'.$i;
										if($i > 1)
										{
											$tokens = explode('_', $category_seo_product_key);
											array_pop($tokens);
											array_pop($tokens);
											$category_seo_product_key = implode('_', $tokens);
											$category_seo_product_key = $category_seo_product_key.'_'.$i;
										}
										$i++;
									}
									$pickup_url_key[] = $category_seo_product_key;
								}
								else
								{
									$pickup_url_key[] = $category_seo_product_key;
								}
			
								$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
								$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
							}
						}
					}
						
						
						
					if(isset($seo_pickup_sub_cat_seo))
					{
						foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat)
						{
							if(isset($seo_pickup_sub_cat))
							{
								$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
								$category_seo_sub_cat_key =  strtolower(str_replace(' ', '_', $seo_pickup_sub_cat->subname));
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';
			
							}
								
							if(isset($seo_pickup_subcat_product))
							{
								foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product)
								{
									if(isset($seo_pickup_sub_product))
									{
										$category_seo_sub_cat_product_key =  strtolower(str_replace(' ', '_', $seo_pickup_sub_product->proname));
			
										if (in_array($category_seo_sub_cat_product_key, $pickup_url_key))
										{
											$i=1;
											while(in_array($category_seo_sub_cat_product_key, $pickup_url_key))
											{
												$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'_'.$i;
												if($i > 1)
												{
													$tokens = explode('_', $category_seo_sub_cat_product_key);
													array_pop($tokens);
													array_pop($tokens);
													$category_seo_sub_cat_product_key = implode('_', $tokens);
													$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'_'.$i;
												}
												$i++;
											}
											$pickup_url_key[] = $category_seo_sub_cat_product_key;
										}
										else
										{
											$pickup_url_key[] = $category_seo_sub_cat_product_key;
										}
			
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
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
			
			$response['pickup']['category'] = $seo_response;
			
			$seo_response = array();
			
			$seo_pickup_data = $response['delivery'];
			
			foreach($seo_pickup_data as $key=>$seo_pickup)
			{
				foreach($seo_pickup as $k=>$seo_pick)
				{
					$seo_pickup_cat_product =  $seo_pick->product;
					$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
					$category_seo_key = strtolower(str_replace(' ', '_', $seo_pick->name));
					$seo_response[$category_seo_key] = $seo_pick;
					$seo_response[$category_seo_key]->slug = $category_seo_key;
					$seo_response[$category_seo_key]->product='';
					$seo_response[$category_seo_key]->sub_category='';
						
					if(isset($seo_pickup_cat_product))
					{
						foreach($seo_pickup_cat_product as $k=>$seo_pickup_product)
						{
							if(isset($seo_pickup_product))
							{
								$category_seo_product_key = strtolower(str_replace(' ', '_', $seo_pickup_product->proname));
			
								if (in_array($category_seo_product_key, $delivery_url_key))
								{
									$i=1;
									while(in_array($category_seo_product_key, $delivery_url_key))
									{
										$category_seo_product_key = $category_seo_product_key.'_'.$i;
										if($i > 1)
										{
											$tokens = explode('_', $category_seo_product_key);
											array_pop($tokens);
											array_pop($tokens);
											$category_seo_product_key = implode('_', $tokens);
											$category_seo_product_key = $category_seo_product_key.'_'.$i;
										}
										$i++;
									}
									$delivery_url_key[] = $category_seo_product_key;
								}
								else
								{
									$delivery_url_key[] = $category_seo_product_key;
								}
			
								$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
								$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'delivery';
								$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
							}
						}
					}
						
						
						
					if(isset($seo_pickup_sub_cat_seo))
					{
						foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat)
						{
							if(isset($seo_pickup_sub_cat))
							{
								$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
								$category_seo_sub_cat_key =  strtolower(str_replace(' ', '_', $seo_pickup_sub_cat->subname));
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
								$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';
			
							}
								
							if(isset($seo_pickup_subcat_product))
							{
								foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product)
								{
									if(isset($seo_pickup_sub_product))
									{
										$category_seo_sub_cat_product_key =  strtolower(str_replace(' ', '_', $seo_pickup_sub_product->proname));
			
										if (in_array($category_seo_sub_cat_product_key, $delivery_url_key))
										{
											$i=1;
											while(in_array($category_seo_sub_cat_product_key, $delivery_url_key))
											{
												$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'_'.$i;
												if($i > 1)
												{
													$tokens = explode('_', $category_seo_sub_cat_product_key);
													array_pop($tokens);
													array_pop($tokens);
													$category_seo_sub_cat_product_key = implode('_', $tokens);
													$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'_'.$i;
												}
												$i++;
											}
											$delivery_url_key[] = $category_seo_sub_cat_product_key;
										}
										else
										{
											$delivery_url_key[] = $category_seo_sub_cat_product_key;
										}
			
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'delivery';
										$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
									}
								}
							}
			
						}
					}
				}
			}
				
			$response['delivery']['category'] = $seo_response;
			

			$pickup_product = $response['pickup']['category'];
			$pickup_product_id = array();
			
			foreach($pickup_product as $k=>$val)
			{
				$pick_cat[] = $val->id;
			}
			
			$all_products = $products;
			foreach($all_products as $k=>$product)
			{
				$categories_id = $product->categories_id;
			
				if($product->service == 'pickup')
				{
					if(in_array($categories_id, $pick_cat))
					{
						if(in_array($product->slug, $pickup_product_id))
						{
								
						}
						else
						{
							$pickup_product_id[] = $product->slug;
							unset($all_products[$k]->service);
							$searchpickup_product[] = $all_products[$k];
						}
					}
				}
			}
			
			$delivery_product = $response['delivery']['category'];
			
			
			foreach($delivery_product as $k=>$val)
			{
				$delivery_cat[] = $val->id;
			}
			
			$delivery_product_id = array();
			
			foreach($all_products as $k=>$product)
			{
				$categories_id = $product->categories_id;
				if($product->service == 'delivery')
				{
					if(in_array($categories_id, $delivery_cat))
					{
						if(in_array($product->slug, $delivery_product_id))
						{
			
						}
						else
						{
							$delivery_product_id[] = $product->slug;
							unset($all_products[$k]->service);
							$searchdelivery_product[] = $all_products[$k];
						}
					}
				}
			}
			
			$response['search_pickup'] = $searchpickup_product;
			$response['search_delivery'] = $searchdelivery_product;
				
			
			$fp = fopen('online-bestellen/products/obs_results_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($response));
			fclose($fp);
			
			if( !empty($org_json_files) )
			foreach( $org_json_files as $id=>$imgf )
			{
				if ( @ftp_put($conn_id, 'products/'.$imgf, $updated_file_loc.'products/'.$org_json_files[$id] , FTP_ASCII) )
				{
					//@ftp_chmod($conn_id, 0777, $imgf);	 // Set Read-Write permisssion  on the uploaded file.
				}
				else
				{
					$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'products/'.$imgf._(' on server !');
				}
			}
			
			unlink('online-bestellen/products/obs_results_'.$company_id.'.json');
			
			
			return $error;
		}
	}
	
	function create_config_file(  $shop_url = NULL, $company_id = NULL  )
	{
	   if( !$company_id )
	     return false;
	   
	   $this->load->model('MApi');
	   $comp_api = $this->MApi->get_company_api( $company_id );
	   
	   if( !empty($comp_api) )
	   {
	      $api_id = $comp_api->api_id;
		  $api_secret = $comp_api->api_secret; 
		  
		  if( !$api_id || !$api_secret )
		    return false;
		  
		  // ---- >> Set other URL settings << ---- //
		  		  
		  $api_server = base_url();                                //'http://www.onlinebestelsysteem.net/obs'
		  //--- $api_assets = base_url().'obs-api';                      //'http://www.onlinebestelsysteem.net/obs/obs-api'
		  //--- $cdn_api_assets = $this->config->item('cdn_api_assets'); //'http://cdn.onlinebestelsysteem.net/obs/obs-api'
		  $api_assets = base_url().'obs-api-new';                      //'http://www.onlinebestelsysteem.net/obs/obs-api'
		  $cdn_api_assets = $this->config->item('cdn_api_assets').'-new'; //'http://cdn.onlinebestelsysteem.net/obs/obs-api'
		  
		  // ---- >> Replace Last '/' from the URLs << ---- //
		  
		  if( substr( $shop_url, strlen($shop_url)-1 , 1) == '/' )
		    $shop_url = substr( $shop_url, 0, -1);
		  
		  // ---- >> Adding www in the URLs << ------------ //
		  //$shop_url = str_replace("http://","http://www.",$shop_url);
		  
		  if( substr( $api_server, strlen($api_server)-1 , 1) == '/' )
		    $api_server = substr( $api_server, 0, -1);
		  
		  if( substr( $api_assets, strlen($api_assets)-1 , 1) == '/' )
		    $api_assets = substr( $api_assets, 0, -1);		
			
		  // ---- >> File Locations << ---- //	
		  
		  //--- $org_config_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/config.php';
		  //--- $new_config_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/new-config.php';
		  $org_config_file_loc = dirname(__FILE__).'/../../../online-bestellen/config.php';
		  $new_config_file_loc = dirname(__FILE__).'/../../../online-bestellen/new-config.php';
		  
		  // ---- >> Read Sample Config File << ---- //	
		  
		  $rfobj = fopen( $org_config_file_loc, 'r' ) or exit( _('Error - Unable to open the original config file !') );		  
		  $fdata = fread( $rfobj, filesize($org_config_file_loc) );
		  fclose( $rfobj );
		  
		  // ---- >> Modify - Do settings << ---- //	
		  
		  $fdata = str_replace( "'CLIENT_URL'", "'".$shop_url."'", $fdata );
		  $fdata = str_replace( "'API_ID'", "'".$api_id."'", $fdata );
		  $fdata = str_replace( "'API_SECRET_KEY'", "'".$api_secret."'", $fdata );
		  
	      if( $api_server )
		    $fdata = str_replace( "'API_SERVER'", "'".$api_server."'", $fdata );

		  if( $api_assets )
		    $fdata = str_replace( "'API_ASSETS'", "'".$api_assets."'", $fdata );
			
		  if( $api_assets )
		    $fdata = str_replace( "'CDN_API_ASSETS'", "'".$cdn_api_assets."'", $fdata );		
		  
		  // ---- >> Create New Config File << ---- //	
		  
		  $wfobj = fopen( $new_config_file_loc, 'w' ) or die( _('Error - Cannot create a Config file for Company ID').' : '.$company_id );
		  fwrite($wfobj, $fdata);
		  fclose( $wfobj );
		  
		  return true;
	   }
	   else
	     return false;	  
	}
	
	function create_index_file(  $shop_url = NULL, $company_id = NULL  )
	{
	   if( !$company_id )
	     return false;
	   
	   $this->load->model('mcp/Mcompanies');
	   $company = $this->Mcompanies->get_company( array( 'id' => $company_id ) );
	   
	   if( !empty($company) )
	   {	
	      $company = $company[0];	  
		  $company_footer_text = $company->company_footer_text;
		  $company_footer_link = $company->company_footer_link;
		  
		  $obs_jquery = 'http://code.jquery.com/jquery-1.8.2.min.js';
		  $obs_script = 'js/obs-script.js.php';
		  $obs_style = 'includes/get-style.css.php';
		  
		  if( !$company_footer_text || !$company_footer_link )
		    return false;			
			
		  // ---- >> Replace Last '/' from the URLs << ---- //
		  
		  if( substr( $shop_url, strlen($shop_url)-1 , 1) == '/' )
		    $shop_url = substr( $shop_url, 0, -1);	
			
		  // ---- >> File Locations << ---- //	
		  
		  //--- $org_index_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/index.html';
		  //--- $new_index_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/new-index.html';
		  $org_index_file_loc = dirname(__FILE__).'/../../../online-bestellen/index.html';
		  $new_index_file_loc = dirname(__FILE__).'/../../../online-bestellen/new-index.html';
		  
		  // ---- >> Read Sample Config File << ---- //	
		  
		  $rfobj = fopen( $org_index_file_loc, 'r' ) or exit( _('Error - Unable to open the original index file !') );		  
		  $fdata = fread( $rfobj, filesize($org_index_file_loc) );
		  fclose( $rfobj );
		  
		  // ---- >> Modify - Do settings << ---- //	
			
		  $fdata = str_replace( "COMPANY_FOOTER_TEXT", $company_footer_text, $fdata );		
		  $fdata = str_replace( "COMPANY_FOOTER_LINK", $company_footer_link, $fdata );
		  
		  $fdata = str_replace( "OBS_JQUERY", $obs_jquery, $fdata );		
		  $fdata = str_replace( "OBS_SCRIPT", $shop_url.'/'.$obs_script, $fdata );	
		  
		  $fdata = str_replace( "OBS_STYLE", $shop_url.'/'.$obs_style, $fdata );		
		  
		  // ---- >> Create New Config File << ---- //	
		  
		  $wfobj = fopen( $new_index_file_loc, 'w' ) or die( _('Error - Cannot create a index file for Company ID').' : '.$company_id );
		  fwrite($wfobj, $fdata);
		  fclose( $wfobj );
		  
		  
		  return true;
	   }
	   else
	     return false;	  
	}
	
	//get all product
	
	function get_shop_content_json_new($company_id){
	
		$service_types = array('pickup', 'delivery');
		$data = array();
		
		foreach($service_types as $service_type)
		{
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
			
			
				
			
			$data[$service_type] = $result;
		}	
		
		return $data;
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
	
		$category_products = $this->db->get_where('products', array('sell_product_option !=' => '' ))->result();
		
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
	 * This private function is used to fetch all products of given company
	 * @access private
	 */
	private function get_products($company_id = null, $is_k_assoc = 0){
		$products = array();
		if($company_id && is_numeric($company_id)){
				
			$this->db->select('display_fixed');
			$display_fixed_result = $this->db->get_where('general_settings', array('company_id' => $company_id))->result();
				
			$this->db->select('products.*,categories.service_type as category_service_type');
				
			$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
			$this->db->where($where);
				
			$this->db->order_by('pro_display','asc');
			$this->db->order_by('id','asc');
				
			$this->db->join('categories','categories.id = products.categories_id','left');
				
			$products = $this->db->get_where('products', array('products.company_id' => $company_id, 'products.status' => 1, 'products.sell_product_option !=' => '' ))->result();
				
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
		return array_values($products);
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
	 * This private function is used to fetch Days
	 * @access private
	 * @return array $days Array of Days
	 */
	private function get_days(){
		$days = $this->db->get('days')->result();
		return $days;
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
	 * This function is used to get shop's all contents
	 * @access public
	 */
	function get_shop_content_all_post($company_id){
	
		$year = date("Y");
		$month = date("m");
	
		/*if($company_id == 3988)
		 $company_id = 87;*/
	
		// Fetching company account type. This is for checking whether company is attached with FoodDESK
		$this->db->select("ac_type_id");
		$company_info = $this->db->get_where('company',array("id" => $company_id))->result();
		$is_fdd_associated = 0;
		if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
			$is_fdd_associated = 1;
	
		// Fetching Opening hours
		$general_settings = $this->get_general_settings($company_id);
	
		// Fetching Categories
		$categories = $this->get_categories($company_id);
	
		// Fetching Subcategories
		$subcategories = $this->get_subcategories($company_id);
	
		// Fetching Products
		$products = $this->get_products($company_id, $is_fdd_associated);
	
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
	
		return $response_arr;
		//$this->response( array('error' => 0, 'message'=> '' , 'data' => $response_arr ), 200 );
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
	
	
	
}

?>