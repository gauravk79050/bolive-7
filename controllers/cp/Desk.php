<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Desk extends CI_Controller
{	
	var $template_path;
	var $company_id;
	var $company_role;
	var $main_site_url;
	
	function __construct()
	{		
		parent::__construct();	
		
		//$this->load->helper('url');
		$this->load->library('form_validation');
		//$this->load->library('session');
		$this->load->library('messages');
		
		//$this->load->model('Mgeneral_settings');
		//$this->load->model('Mopening_hours');
		$this->load->model('Mcompany');
		$this->load->model('Mcalender');
		$this->load->model('Mproducts');
		$this->load->model('Msubcategories');
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		$this->load->model('Mcategories');
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}
		
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
		$this->upload_path = realpath(APPPATH .'../assets/cp/images/company-gallery');
		
		//$this->load->model('Company');
	}
	
	function index()
	{
		if($this->company->obsdesk_status != 1)
		{
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
			//exit;
		}
		else{
			$this->settings();
		}
	}
	
	function settings()
	{	
		if($this->company->obsdesk_status != 1)
		{ 
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
			//exit;
		}
		else
		{ 
			$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender
			
			if( $this->input->post('action') == 'desk_general_setting' )
			{ 
			    //$this->form_validation->set_rules('email_id', 'Email Address', 'required|valid_email');
				$this->form_validation->set_rules('lang_id', 'Language', 'required');
				$this->form_validation->set_error_delimiters('<p class="error">', '</p>');
				if ($this->form_validation->run() == FALSE)
				{
				}
				else
				{ 
					// if($this->input->post('image_name') != ''){

					//         $this->db->select('comp_default_image');
					//         $comp_default_image = $this->db->get_where('desk_settings', array('company_id' => $this->company_id))->result();
						
					//         if(!empty($comp_default_image[0]->comp_default_img)){
					// 	     unlink(dirname(__FILE__).'/../../../assets/cp/images/infodesk_default_image/'.$comp_default_image[0]->comp_default_image);
					//         }
	                        
	    //                     $prefix = 'cropped_'.$this->company_id.'_';
					//         $str = $this->input->post('image_name');
					//         if (substr($str, 0, strlen($prefix)) == $prefix) {
					// 	       $str = substr($str, strlen($prefix));
					//         }
					//         if(isset($str) && $str != ''){
					// 	       $this->image = $this->company_id.'_'.$str;
					// 	       $image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));
					// 	       file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/infodesk_default_image/'.$this->image, $image_file);
					// 	    }
			  //           }
			            
                        $update_arr = array(
					                      'email_id' => $this->input->post('email_id'),
					                      'lang_id ' => $this->input->post('lang_id'),
										  'show_message_front' => $this->input->post('show_message_front'),
										  'message_front' => $this->input->post('message_front'),
										  'activate_numbering' => $this->input->post('activate_numbering'),
										  'disable_price' => $this->input->post('disable_price'),
										  'send_orders_to_email' => $this->input->post('send_orders_to_email'),
										  'print_button' => $this->input->post('print_button'),
										  'act_as_infocenter' => $this->input->post('act_as_infocenter'),
										  'help_text' => $this->input->post('help_text'),
										  'use_pics' => $this->input->post('use_pics'),
										  'show_sheet' =>$this->input->post('sheet_in_desk')
									   );
                        /*if($this->input->post('image_name') != ''){
                        	$update_arr = $update_arr + array('comp_default_image' =>$this->image);
                        }*/
					
					$where_arr = array( 'company_id' => $this->company_id );
					$this->Mcompany->update_desk_settings( $where_arr, $update_arr );
					$this->session->set_userdata('action', 'general_setting_json');
					redirect(base_url()."cp/desk/settings");
				}
			}
			
			if( $this->input->post('action') == 'look_n_feel' )
			{
				$this->db->select('obsdesk_logo');
				$desk_logo = $this->db->get_where('company', array('id' => $this->company_id))->result();
				if(!empty($desk_logo[0]->obsdesk_logo)){
					$image = $desk_logo[0]->obsdesk_logo;
				}
				/*if( $this->input->post('old_obsdesk_logo') )
					$image = $this->input->post('old_obsdesk_logo');
				else
					$image = '';
				 
				if( $_FILES['obsdesk_logo']['name'] )
				{
					$company_slug = $this->form_url_slug( $this->input->post( 'company_name' ) );
					$uploads_dir = dirname(__FILE__).'/../../../assets/company-logos';
			
					$tmp_name = $_FILES["obsdesk_logo"]["tmp_name"];
					$org_name = $_FILES["obsdesk_logo"]["name"];
					$org_name = explode('.',$org_name);
					$ext = end($org_name);
					$name = $company_slug.'-'.time().'.'.$ext;
			
					if( @move_uploaded_file( $tmp_name, "$uploads_dir/$name" ) )
						$image = $name;
				}*/
				if($this->input->post('image_name5')){
					if(!empty($desk_logo[0]->obsdesk_logo)){
						unlink(dirname(__FILE__).'/../../../assets/company-logos/'.$desk_logo[0]->obsdesk_logo);
					}
				
					$prefix = 'cropped_'.$this->company_id.'_';
					$str = $this->input->post('image_name5');
					if (substr($str, 0, strlen($prefix)) == $prefix) {
						$str = substr($str, strlen($prefix));
					}
					if(isset($str) && $str != ''){
						$this->image = $this->company_id.'_'.$str;
						$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name5'));
						file_put_contents(dirname(__FILE__).'/../../../assets/company-logos/'.$this->image, $image_file);
					}
				}
				if($this->image){
					$image = $this->image;
				}
				$update_arr = array(
						'obsdesk_company_name' => $this->input->post('obsdesk_company_name'),
						'obsdesk_footer_text' => $this->input->post('obsdesk_footer_text'),
						'obsdesk_logo' => $image
				);
				
					
				$where_arr = array( 'id' => $this->company_id );
				$this->Mcompany->update_companies( $where_arr, $update_arr );
				
				$update = array(
									"head_bg_color_1" => $this->input->post("head_bg_color_1"),
									"head_text_color_1" => $this->input->post("head_text_color_1"),
									"head_bg_color_2" => $this->input->post("head_bg_color_2"),
									"head_text_color_2" => $this->input->post("head_text_color_2"),
									"button_bg_color_1" => $this->input->post("button_bg_color_1"),
									"button_text_color_1" => $this->input->post("button_text_color_1"),
									"button_bg_color_2" => $this->input->post("button_bg_color_2"),
									"button_text_color_2" => $this->input->post("button_text_color_2"),
									"availability_bg_color" => $this->input->post("availability_bg_color"),
									"availability_text_color" => $this->input->post("availability_text_color")
								);
				
				$where_arr = array( 'company_id' => $this->company_id );
                $this->Mcompany->update_desk_section_design($where_arr,$update);

                $this->Mcompany->update_desk_settings( $where_arr, array("apply_css" => $this->input->post("apply_css")) );
				
                $all_image = '';
                if($this->company->ac_type_id == 4 || $this->company->ac_type_id == 5 || $this->company->ac_type_id == 6){
                	$this->db->select('obsdesk_logo');
                	$desk_logo = $this->db->get_where('company',array('id'=>$this->company_id))->result_array();
                	$all_image = $desk_logo[0]['obsdesk_logo'];
                	$path = dirname(__FILE__)."/../../../assets/allergenkaart_logos/";
                	if(!file_exists($path.$all_image))
                	{
                		$image_file = file_get_contents(dirname(__FILE__).'/../../../assets/company-logos/'.$all_image);
                		file_put_contents(dirname(__FILE__).'/../../../assets/allergenkaart_logos/'.$all_image, $image_file);
                	}
                }
                $update_allergen = array(
                		"allergenkaart_logo" =>$all_image
                );
                $this->Mcompany->update_allergenkaart_design($where_arr,$update_allergen);
                
				$this->messages->add( _('Your OBSDesk settings saved successfully !'), 'success' );
				$this->session->set_userdata('action', 'general_setting_json');
				redirect(base_url()."cp/desk/settings");
			}


			if( $this->input->post('action') == 'allergenkaart_look_n_feel' ){
				$where_arr = array( 'company_id' => $this->company_id );
				
				/*if( $this->input->post('old_allergenkaart_logo') )
					$all_image = $this->input->post('old_allergenkaart_logo');
				else
					$all_image = '';
				 */
			    if($this->company->ac_type_id == 7){
					/*if( $_FILES['allergenkaart_logo']['name'] )
					{
						
						$all_company_slug = $this->form_url_slug( $this->input->post( 'company_name' ) );
						$all_uploads_dir = dirname(__FILE__).'/../../../assets/allergenkaart_logos';
				       
						$all_tmp_name = $_FILES["allergenkaart_logo"]["tmp_name"];
						$all_org_name = $_FILES["allergenkaart_logo"]["name"];
						$all_org_name = explode('.',$all_org_name);
						$all_ext = end($all_org_name);
						$all_name = $all_company_slug.'-'.time().'.'.$all_ext;
				        
						if( @move_uploaded_file( $all_tmp_name, "$all_uploads_dir/$all_name" ) )
							$all_image = $all_name;
					}*/
			    	if($this->input->post('image_name6')){
			    		$this->db->select('allergenkaart_logo');
			    		$allergenkaart_logo = $this->db->get_where('allergenkaart_design', array('company_id' => $this->company_id))->result();
			    		if(!empty($allergenkaart_logo[0]->allergenkaart_logo)){
			    			unlink(dirname(__FILE__).'/../../../assets/allergenkaart_logos/'.$allergenkaart_logo[0]->allergenkaart_logo);
			    		}
			    	
			    		$prefix = 'cropped_'.$this->company_id.'_';
			    		$str = $this->input->post('image_name6');
			    		if (substr($str, 0, strlen($prefix)) == $prefix) {
			    			$str = substr($str, strlen($prefix));
			    		}
			    		if(isset($str) && $str != ''){
			    			$this->image = $this->company_id.'_'.$str;
			    			$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name6'));
			    			file_put_contents(dirname(__FILE__).'/../../../assets/allergenkaart_logos/'.$this->image, $image_file);
			    		}
			    	}
                }elseif($this->company->ac_type_id == 4 || $this->company->ac_type_id == 5 || $this->company->ac_type_id == 6){
                	$this->db->select('obsdesk_logo');  
                	$desk_logo = $this->db->get_where('company',array('id'=>$this->company_id))->result_array();
                	$all_image = $desk_logo[0]['obsdesk_logo'];
                }

                if($this->input->post("apply_this_style") == ''){
                    $apply_this_style = "0";
                }else{
                	$apply_this_style = "1";
                }

                if($this->input->post("apply_product_image") == "1"){
                	$product_image = "1";
                }else{
                	$product_image = "0";
                }
                if($this->image){
                	$all_image = $this->image;
                }
				$update_allergen = array(
					                 "sidebar_bg_color_1" 	=> $this->input->post("sidebar_bg_color_1"),
					                 "sidebar_text_color_1" => $this->input->post("sidebar_text_color_1"),
					                 "filter_bg_color_1" 	=> $this->input->post("filter_bg_color_1"),
					                 "filter_text_color_1" 	=> $this->input->post("filter_text_color_1"),
					                 "apply_product_image" 	=> $product_image,
					                 "active_text_color_1" 	=> $this->input->post("active_text_color_1"),
					                 "active_bg_color_1" 	=> $this->input->post("active_bg_color_1"),
					                 "apply_css" 			=> $apply_this_style,
					                 "allergenkaart_logo" 	=> $all_image,
									 "lang"					=> $this->input->post("lang_id")
					                 );
				$this->Mcompany->update_allergenkaart_design($where_arr,$update_allergen);
				$this->messages->add( _('Your allergenkaart settings saved successfully !'), 'success' );
				$this->session->set_userdata('action', 'general_setting_json');
				redirect(base_url()."cp/desk/settings");
			}
			
			$where_arr = array( 'company_id' => $this->company_id );
			$desk_settings = $this->Mcompany->get_desk_settings( $where_arr );

			
			if( empty($desk_settings) )
			{
			    $desk_settings = array( 'company_id' => $this->company_id, 'use_pics' => 1 );
				$this->Mcompany->insert_desk_settings( $desk_settings );
			}
			else
			{
				$desk_settings = $desk_settings[0];
			}
			
			$data['desk_section_design'] = $this->Mcompany->get_desk_section_design(array("company_id" => $this->company_id));
			$data['type_id'] = $this->company->type_id; 
			$data['desk_settings'] = $desk_settings;
			$allkart_settings = $this->Mcompany->get_allergenkaart_settings( $where_arr );
			$data['allkaart_settings'] = $allkart_settings;
			$data['languages'] = $this->Mcompany->get_languages();
			
			$this->db->select('api_secret');
			$data['uw_code'] = $this->db->get_where('api',array('company_id'=>$this->company_id))->result_array();
			
			$data['content'] = 'cp/desk_settings_view';
			$this->load->view( 'cp/cp_view', $data );
		}
	}
    
    /**
	 * This function is used to fetch company default image values from general_settings table and import into desk_settings table in the corresponding column.
	 */
	function comp_default_image_data(){
		$this->db->select('company_id,comp_default_image');
		$gen = $this->db->get('general_settings')->result();
        
        foreach ($gen as $key) {
           $comp_exist = $this->db->get_where('desk_settings',array('company_id'=>$key->company_id))->result_array();
           if (!empty($comp_exist)) {
           		$this->db->where('company_id',$key->company_id);
           		$this->db->update('desk_settings',array('comp_default_image'=>$key->comp_default_image));
           }
        }
    }	
	
	/*function is_sync_obs_desk_data()
	{	    
		$company_details = $this->Mcompany->get_companies( array('id' => $this->company_id) );
		
		if( !empty( $company_details ) && isset($company_details[0]) )
		{
		    $company_details = $company_details[0];
			
			if( $company_details->sync_obs_desk_data )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		return false;
	}*/
	
	function orders( $action = null , $order_id = null){
		
		/*if($this->company->obsdesk_status != 1)
		{
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
			//exit;
		}
		else
		{*/
			$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender
			
			/*if( $this->company_role != 'super' )
			{*/
	
				if($action == "edit"){
					
					if( $this->input->post('add_product_in_order') )
					{
						$this->load->model('Mproduct_discount');
						
						$order_id = $this->input->post('order_id');
						$order_total = $this->input->post('order_total');
						$category_id = $this->input->post('category_id');
						$subcategory_id = $this->input->post('subcategory_id');
						$product_id = $this->input->post('product_id');
						$net_quantity = $this->input->post('quantity');
						$content_type = $this->input->post('content_type');
					
						$product_data = $this->Mproducts->get_product_information( $product_id );
						
						if(!empty($product_data)){ $product_data = $product_data[0]; }
					
						$total_amount = 0;
						$net_price = 0;
						
						if( $content_type == 0 )  // Per Unit
						{
							if(empty($product_data)){
								$this->messages->add( _('ERROR : Fields Cannot be null'), 'error' );
								redirect( base_url().'cp/desk/orders/edit/'.$order_id, 'refresh' );
							}
							$net_price = (float)$product_data->price_per_unit;
							$default_price = (float)$product_data->price_per_unit;
							
							$product_discount = $product_data->discount;
								
							if($product_data->discount == "multi")   // If multiple discounts exists for the product ?
							{
								$product_unit_type = 0;
								
								$discount = $this->Mproduct_discount->get_product_discount( $product_id,  0  );
								$apply_discount_unit = 0;
					
								if(!empty($discount))
									foreach($discount as $discnt)
									{
										if( $discnt->type == $product_unit_type )
											if($discnt->quantity==(int)$net_quantity && (int)$net_quantity!=1 && $discnt->quantity>$apply_discount_unit)
											{
												$apply_discount = (float)$discnt->discount_per_qty;
												$apply_discount_unit = $discnt->quantity;
												$net_price = (float)$discnt->price_per_qty;
											}
									}
							}
							elseif($product_data->discount != 0 && is_numeric($product_data->discount))  // If no multiple discounts & single discount for the product ?
							{
								$net_price = $net_price - (float)$product_data->discount;
							}
								
							$total_amount = (int)($net_quantity) * (float)($net_price) ;
						}
						elseif( $content_type == 1 ) // Weight Wise
						{
							$net_price = (float)$product_data->price_weight;
							$default_price = (float)$product_data->price_weight;
							$net_price_in_kg = $net_price*1000;
							$product_discount = $product_data->discount_wt;
								
							if( $product_data->discount_wt == 'multi' )   // If multiple discounts exists for the product ?
							{
								$product_unit_type = 1;
								$apply_discount_unit = 0; $apply_discount = 0;
					
								$discount_wt = $this->Mproduct_discount->get_product_discount( $product_id, 1 );
					
								if( !empty($discount_wt) )
									foreach($discount_wt as $discnt)
									{
										if( $discnt->type == $product_unit_type )
										{
											$quantity_in_comp_discount_unit = (int)($net_quantity/$discnt->quantity);
												
											if($quantity_in_comp_discount_unit>0 && $discnt->quantity>$apply_discount_unit)
											{
												$apply_discount = (float)$discnt->discount_per_qty; // per gram
												$apply_discount_unit = $discnt->quantity;           // in grams
												$net_price = (float)$discnt->price_per_qty;         //per gram
											}
										}
									}
					
									if($apply_discount && $apply_discount_unit)
									{
										$comp_dis_unit = @(int)($net_quantity/$apply_discount_unit);
										$rem_dis_unit = @($net_quantity%$apply_discount_unit);
											
										$comp_tot_amount = (($comp_dis_unit*$apply_discount_unit)*$net_price);
										$incomp_tot_amount = ($rem_dis_unit*$default_price);
										$total_amount = $comp_tot_amount+$incomp_tot_amount;
									}
									else
									{
										$total_amount = $net_quantity*$net_price;
									}
					
							}
							elseif( $product_data->discount_wt != 0 && is_numeric($product_data->discount_wt) )  // If no multiple discounts & single discount for the product ?
							{
					
								$quantity_in_comp_kg = (int)($net_quantity/1000);
								$quantity_left_in_gm = ($net_quantity%1000);
					
								if( $quantity_in_comp_kg > 0 )
								{
									$apply_discount = (float)$product_data->discount_wt;
									$net_price_in_kg = $net_price_in_kg - $apply_discount;
									$net_price = $net_price_in_kg/1000;
								}
					
								$comp_tot_amount = (float)($quantity_in_comp_kg*1000*$net_price);
								$incomp_tot_amount = (float)($quantity_left_in_gm*$default_price);
								$total_amount = $comp_tot_amount+$incomp_tot_amount;
							}
							else   // If no discounts for the product ?
							{
								$total_amount = $net_quantity*$net_price;
							}
								
							//$total_amount = (float)$total_amount+(float)$group_additional;
						}
						elseif( $content_type == 2 ) // Per Person
						{
							$net_price = (float)$product_data->price_per_person;
							$default_price = (float)$product_data->price_per_person;
							$product_discount = $product_data->discount_person;
							
							if($product_data->discount_person == "multi")   // If multiple discounts exists for the product ?
							{
								$product_unit_type = 2;
								$discount = $this->Mproduct_discount->get_product_discount( $product_id, 2 );
								$apply_discount_unit = 0;
					
								if(!empty($discount))
									foreach($discount as $discnt)
									{
										if( $discnt->type == $product_unit_type )
											if($discnt->quantity==(int)$net_quantity && (int)$net_quantity!=1 && $discnt->quantity>$apply_discount_unit)
											{
												$apply_discount = (float)$discnt->discount_per_qty;
												$apply_discount_unit = $discnt->quantity;
												$net_price = (float)$discnt->price_per_qty;
											}
									}
							}
							elseif($product_data->discount_person != 0 && is_numeric($product_data->discount_person))  // If no multiple discounts & single discount for the product ?
							{
								$net_price = $net_price - (float)$product_data->discount_person;
							}
								
							$total_amount = (int)($net_quantity) * (float)($net_price);
						}
					
						$where_arr = array( 'id' => $order_id );
						$update_order_arr = array( 'order_total' => $order_total+$total_amount );
						$isUpdated = $this->Morders->update_desk_order( $where_arr, $update_order_arr );
					
						if( $isUpdated )
						{
							$det_order_arr = array(
									'order_id' => $order_id,
									'product_id' => $product_id,
									'default_price' => ( ($content_type==1)?(round($default_price*1000,2)):($default_price) ),
									'discount' => $product_discount,
									'add_costs' => '',
									'quantity' => $net_quantity,
									'content_type' => strval($content_type),
									'sub_total' => ( ($content_type==1)?(round($net_price*1000,2)):($net_price) ),
									'total' => $total_amount,
									'pro_remark' => ''
							);
					
							$this->Morder_details->insert_desk_order_details( $det_order_arr );
								
							$this->messages->add( _('New product added in this order, successfully !'), 'success' );
						}
						else
						{
							$this->messages->add( _('ERROR : Can\'t add new product in this order !'), 'error' );
						}
					}
						
					if( $this->input->post('update_order') )
					{
						$order_id = $this->input->post('order_id');
						$completed = $this->input->post('order_status');
					
						$where_arr = array( 'id' => $order_id );
						$update_order_arr = array( 'completed' => $completed );
						$isUpdated = $this->Morders->update_desk_order( $where_arr, $update_order_arr );
					
						if( $isUpdated )
						{
							$this->messages->add( _('Order status updated successfully !'), 'success' );
						}
						else
						{
							$this->messages->add( _('ERROR : Can\'t update the order status !'), 'error' );
						}
					}
						
					$data['order'] = $this->Morders->get_desk_orders( array('desk_orders.id'=>$order_id,'desk_orders.company_id'=>$this->company_id ) );
					$data['order_details'] = $this->Morder_details->get_desk_order_details( array('order_id'=>$order_id) );
						
					$data['categories'] = $this->Mcategories->get_category( array('company_id' => $this->company_id) );
						
					$data['content'] = 'cp/edit_desk_order';
					$this->load->view( 'cp/cp_view', $data );
					
				}elseif($action == "delete"){
					if( $order_id )
					{
						$this->Morders->delete_desk_order( $order_id );
						$this->messages->add( _('Order deleted successfully !'), 'success' );
						redirect( base_url().'cp/desk/orders', 'refresh' );
					}
					else
					{
						$this->messages->add( _('ERROR : Can\'t delete the order !'), 'error' );
						redirect( base_url().'cp/desk/orders', 'refresh' );
					}
				}else{
				
					if($this->input->post('act')=='do_filter'){/*===============for the searching==========*/
					
						$exp_start_date = explode("/",$this->input->post('start_date'));
						$data['start_date'] = $start_date = $exp_start_date[2]."-".$exp_start_date[1]."-".$exp_start_date[0];
							
						$exp_end_date = explode("/",$this->input->post('end_date'));
						$data['end_date'] = $end_date = $exp_end_date[2]."-".$exp_end_date[1]."-".$exp_end_date[0];
					
						$data['orders'] = $orders = $this->Morders->get_desk_orders( array('desk_orders.company_id'=>$this->company_id ), array( 'created_date' => 'DESC' ), null, null, $start_date, $end_date );
						
						$data['obs_orders'] = $this->Morders->count_orders();
						
						$data['obs_pending_orders'] = $this->Morders->count_orders(null,null,null,null,1);
						
						$data['obs_cancelled_orders'] = $this->Morders->count_orders(null,null,null,null,0,true);
					
					}else{
						$data['orders'] = $orders = $this->Morders->get_desk_orders( array('desk_orders.company_id'=>$this->company_id ), array( 'created_date' => 'DESC' ) );
						$data['obs_orders'] = $this->Morders->count_orders();
						$data['obs_pending_orders'] = $this->Morders->count_orders(null,null,null,null,1);
						$data['obs_cancelled_orders'] = $this->Morders->count_orders(null,null,null,null,0,true);
					}
					
					$data['content'] = 'cp/desk_orders';
					$this->load->view( 'cp/cp_view', $data );
				}
			/*}
			else
			{
				$data['content'] = 'restricted';
				$this->load->view( $this->template_path.'main_view', $data );
			}*/
		/*}*/
	}
	
	function ajax_orders(){
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$exp_start_date = $this->input->post('start_date');
		$exp_end_date = $this->input->post('end_date');
		
		$where_arr = array('desk_orders.company_id'=>$this->company_id );
		$this->load->model('Morders');
		$orders = $this->Morders->get_desk_orders($where_arr, array( 'created_date' => 'DESC' ),$limit,$start,$exp_start_date,$exp_end_date);
		
		
		$response = array();
		for($i =0; $i<count($orders);$i++){
			
			$client_info_html = '';
			$client_info_html .= '<div id="show_client_'.$orders[$i]->id.'" style="display:none">';
			$client_info_html .= '<table width="100%" border="0" cellspacing="8" cellpadding="0" class="override">';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._("Name").':</strong></td>';
			$client_info_html .= '<td class="td_right">';
				
			$name = '-----';
			if($orders[$i]->firstname_c){
				$name = $orders[$i]->firstname_c;
			}
			if($orders[$i]->lastname_c){
				$name .= '&nbsp;&nbsp;'.$orders[$i]->lastname_c;
			}
			
			$client_info_html .= $name.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._("Address").':</strong></td>';
			$client_info_html .= '<td class="td_right">';
				
			$address ='---';
			if($orders[$i]->address_c){
				$address = $orders[$i]->address_c;
			}if($orders[$i]->housenumber_c){
				$address .= '&nbsp;&nbsp;'.$orders[$i]->housenumber_c;
			}
			
			$client_info_html .= $address.'<br />';
				
			$address2 = '---';
			if($orders[$i]->postcode_c){
				$address2 = $orders[$i]->postcode_c;
			}
			if($orders[$i]->city_c){
				$address2 .='&nbsp;&nbsp;'.$orders[$i]->city_c;
			}
			
			$client_info_html .= $address2.'<br />';
				
			$country = '---';
			if($orders[$i]->country_name){
				$country = $orders[$i]->country_name;
			}
				
			$client_info_html .= $country.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._("Telephone").':</strong></td>';
			$client_info_html .= '<td class="td_right">';
				
			$telephone = '---';
			if($orders[$i]->phone_c){
				$telephone = $orders[$i]->phone_c;
			}
				
			$client_info_html .= $telephone;
			$client_info_html .= '</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._("Email").':</strong></td>
											<td class="td_right">
			                                   <a href="mailto:'.$orders[$i]->email_c.'?subject=Re:%20BESTELLING&amp;body='._('Dear client').',"><strong>'.$orders[$i]->email_c.'</strong></a>
			                                   <br /><br />
			                                   <a href="'.base_url().'cp/clients/lijst/client_details/'.$orders[$i]->client_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
			                                </td>
										</tr>
			
									</table>
								</div>';
			
			$response[] = array($orders[$i]->id,$orders[$i]->order_counter,date('d M, y', strtotime($orders[$i]->created_date) ),date('h:i a', strtotime($orders[$i]->created_date) ),defined_money_format($orders[$i]->order_total),($orders[$i]->completed)?_('Completed'):_('Incomplete'),$orders[$i]->firstname_c.' '.$orders[$i]->lastname_c,$client_info_html,$orders[$i]->id);
		}
		echo json_encode($response);
	}
	
	function get_subcat_n_prod( $category_id = NULL )
	{
		if( !$category_id )
			return false;
		
		$data['subcategories'] = $this->Msubcategories->get_subcategory( array('categories_id' => $category_id) );
		$data['products'] = $this->Mproducts->get_products( $category_id );
	
		echo json_encode( $data );
		die();
	}
	
	function get_prod( $category_id = NULL, $subcategory_id = NULL )
	{
		if( !$category_id || !$subcategory_id )
			return false;
	
		$products = $this->Mproducts->get_products( $category_id, $subcategory_id );
	
		echo json_encode( $products );
		die();
	}
	
	function get_product_groups()
	{
		$product_id = $this->input->post('product_id');
		$content_type = $this->input->post('content_type');
	
		if( $product_id )
		{
			$this->load->model('Mgroups_products');
			
			$product_grps = $this->Mgroups_products->get_OBS_product_groups_to_list( array( 'products_id' => $product_id, 'type' => $content_type ) );
				
			//print_r($product_grps); die();
				
			if( !empty($product_grps) )
			{
				$product_grps_arr = array();
	
				foreach( $product_grps as $pg )
					$product_grps_arr[] = $pg;
	
				echo json_encode( $product_grps_arr );die();
			}
		}
		echo json_encode(0);
	
		die();
	}
	
	function edit_ordered_product()
	{
		//print_r( $this->input->post() );
	
		$order_index = $this->input->post('order_index');
		$order_id = $this->input->post('order_id');
		$product_id = $this->input->post('product_id');
		$quantity = $this->input->post('upd_quantity');
		$remark = $this->input->post('remark');
		$content_type = $this->input->post('content_type');
		$current_total = $this->input->post('current_total');
		$group = $this->input->post('group');
	
		$order = $this->Morders->get_desk_orders( array('desk_orders.id'=>$order_id,'desk_orders.company_id'=>$this->company_id ) );
		$order_details = $this->Morder_details->get_desk_order_details( array('desk_order_details.id'=>$order_index,'order_id'=>$order_id,'product_id'=>$product_id ) );
		$product_data = $this->Mproducts->get_product_information( $product_id );
	
		if( !empty($order) && !empty($order_details) && !empty($product_data) )
		{
			$this->load->model('Mproduct_discount');
			
			$order = $order[0];
			$order_details = $order_details[0];
			$product_data = $product_data[0];
				
			$net_quantity = $quantity;
			$total_amount = 0;
			$net_price = 0;
			$order_total = $order->order_total;
				
			$add_grp_arr = array();
			$add_grp_cost = 0;
			$add_costs = 0;
				
			if( is_array($group) && !empty($group) )
			{
				foreach( $group as $grp )
				{
					if( $grp )
					{
						$add_grp_arr[] = $grp;
	
						$grp_arr = explode('_',$grp);
						if( !empty($grp_arr) && isset($grp_arr[2]) && $grp_arr[2] )
							$add_grp_cost += (float)$grp_arr[2];
					}
				}
			}
				
			if( !empty($add_grp_arr) )
				$add_costs = implode('#',$add_grp_arr);
				
			if( $content_type == 0 )  // Per Unit
			{
				$net_price = (float)$product_data->price_per_unit;
				$default_price = (float)$product_data->price_per_unit;
				$product_discount = $product_data->discount;
	
				if($product_data->discount == "multi")   // If multiple discounts exists for the product ?
				{
					$product_unit_type = 0;
					$discount = $this->Mproduct_discount->get_product_discount( $product_id,  0  );
					$apply_discount_unit = 0;
						
					if(!empty($discount))
						foreach($discount as $discnt)
						{
							if( $discnt->type == $product_unit_type )
								if($discnt->quantity==(int)$net_quantity && (int)$net_quantity!=1 && $discnt->quantity>$apply_discount_unit)
								{
									$apply_discount = (float)$discnt->discount_per_qty;
									$apply_discount_unit = $discnt->quantity;
									$net_price = (float)$discnt->price_per_qty;
								}
						}
				}
				elseif($product_data->discount != 0 && is_numeric($product_data->discount))  // If no multiple discounts & single discount for the product ?
				{
					$net_price = $net_price - (float)$product_data->discount;
				}
	
				$total_amount = (int)($net_quantity) * ( (float)($net_price) + (float)($add_grp_cost) ) ;
			}
			elseif( $content_type == 1 ) // Weight Wise
			{
				$net_price = (float)$product_data->price_weight;
				$default_price = (float)$product_data->price_weight;
				$net_price_in_kg = $net_price*1000;
				$product_discount = $product_data->discount_wt;
	
				if( $product_data->discount_wt == 'multi' )   // If multiple discounts exists for the product ?
				{
					$product_unit_type = 1;
					$apply_discount_unit = 0; $apply_discount = 0;
						
					$discount_wt = $this->Mproduct_discount->get_product_discount( $product_id,  1  );
						
					if( !empty($discount_wt) )
						foreach($discount_wt as $discnt)
						{
							if( $discnt->type == $product_unit_type )
							{
								$quantity_in_comp_discount_unit = (int)($net_quantity/$discnt->quantity);
	
								if($quantity_in_comp_discount_unit>0 && $discnt->quantity>$apply_discount_unit)
								{
									$apply_discount = (float)$discnt->discount_per_qty; // per gram
									$apply_discount_unit = $discnt->quantity;           // in grams
									$net_price = (float)$discnt->price_per_qty;         //per gram
								}
							}
						}
							
						if($apply_discount && $apply_discount_unit)
						{
							$comp_dis_unit = @(int)($net_quantity/$apply_discount_unit);
							$rem_dis_unit = @($net_quantity%$apply_discount_unit);
	
							$comp_tot_amount = (($comp_dis_unit*$apply_discount_unit)*$net_price);
							$incomp_tot_amount = ($rem_dis_unit*$default_price);
							$total_amount = $comp_tot_amount+$incomp_tot_amount;
						}
						else
						{
							$total_amount = $net_quantity*$net_price;
						}
							
				}
				elseif( $product_data->discount_wt != 0 && is_numeric($product_data->discount_wt) )  // If no multiple discounts & single discount for the product ?
				{
						
					$quantity_in_comp_kg = (int)($net_quantity/1000);
					$quantity_left_in_gm = ($net_quantity%1000);
						
					if( $quantity_in_comp_kg > 0 )
					{
						$apply_discount = (float)$product_data->discount_wt;
						$net_price_in_kg = $net_price_in_kg - $apply_discount;
						$net_price = $net_price_in_kg/1000;
					}
						
					$comp_tot_amount = (float)($quantity_in_comp_kg*1000*$net_price);
					$incomp_tot_amount = (float)($quantity_left_in_gm*$default_price);
					$total_amount = $comp_tot_amount+$incomp_tot_amount;
				}
				else   // If no discounts for the product ?
				{
					$total_amount = $net_quantity*$net_price;
				}
	
				$total_amount = (float)$total_amount+(float)$add_grp_cost;
			}
			elseif( $content_type == 2 ) // Per Person
			{
				$net_price = (float)$product_data->price_per_person;
				$default_price = (float)$product_data->price_per_person;
				$product_discount = $product_data->discount_person;
	
				if($product_data->discount_person == "multi")   // If multiple discounts exists for the product ?
				{
					$product_unit_type = 2;
					$discount = $this->Mproduct_discount->get_product_discount( $product_id,  2  );
					$apply_discount_unit = 0;
						
					if(!empty($discount))
						foreach($discount as $discnt)
						{
							if( $discnt->type == $product_unit_type )
								if($discnt->quantity==(int)$net_quantity && (int)$net_quantity!=1 && $discnt->quantity>$apply_discount_unit)
								{
									$apply_discount = (float)$discnt->discount_per_qty;
									$apply_discount_unit = $discnt->quantity;
									$net_price = (float)$discnt->price_per_qty;
								}
						}
				}
				elseif($product_data->discount_person != 0 && is_numeric($product_data->discount_person))  // If no multiple discounts & single discount for the product ?
				{
					$net_price = $net_price - (float)$product_data->discount_person;
				}
	
				$total_amount = (int)($net_quantity) * ( (float)($net_price) + (float)($add_grp_cost) );
			}
				
			$where_arr = array( 'id' => $order_id );
			$update_order_arr = array( 'order_total' => ($order_total-$current_total)+$total_amount );
			$isUpdated = $this->Morders->update_desk_order( $where_arr, $update_order_arr );
				
			if( $isUpdated )
			{
				$det_order_arr = array(
						'default_price' => ( ($content_type==1)?(round($default_price*1000,2)):($default_price) ),
						'discount' => $product_discount,
						'add_costs' => $add_costs,
						'quantity' => $net_quantity,
						'content_type' => strval($content_type),
						'sub_total' => ( ($content_type==1)?(round($net_price*1000,2)):($net_price) ),
						'total' => $total_amount,
						'pro_remark' => $remark
				);
				$where_arr = array( 'id' => $order_index, 'order_id' => $order_id, 'product_id' => $product_id );
				$this->Morder_details->update_desk_order_details( $where_arr, $det_order_arr );
	
				$this->messages->add( _('Product details updated, successfully !'), 'success' );
			}
			else
			{
				$this->messages->add( _('ERROR : Can\'t update product details in this order !'), 'error' );
			}
		}
	
		redirect( base_url().'cp/desk/orders/edit/'.$order_id, 'refresh' );
	}
	
	function remove_prod( $order_prod_index = NULL, $order_id = NULL, $product_id = NULL )
	{
		if( $order_prod_index && $order_id && $product_id )
		{
			$order = $this->Morders->get_desk_orders( array('desk_orders.id'=>$order_id,'desk_orders.company_id'=>$this->company_id ) );
			$order_details = $this->Morder_details->get_desk_order_details( array('desk_order_details.id'=>$order_prod_index,'order_id'=>$order_id,'product_id'=>$product_id ) );
			
			if( !empty($order) && !empty($order_details) )
			{
			    $order = $order[0];
			    $order_total = $order->order_total;
			   
			    $order_details = $order_details[0];
			    $total = $order_details->total;
			   
			    $where_arr = array( 'id' => $order_id );
				$update_order_arr = array( 'order_total' => ($order_total-$total) );
				$isUpdated = $this->Morders->update_desk_order( $where_arr, $update_order_arr );
			}
			
			$isRemoved = $this->Morder_details->delete_desk_order_details( array('id'=>$order_prod_index,'order_id'=>$order_id,'product_id'=>$product_id ) );
			
			if( $isRemoved )
			{
				$this->messages->add( _('Product removed from the order successfully !'), 'success' );
			}
			else
			{
				$this->messages->add( _('ERROR : Can\'t remove the product from the order !'), 'error' );
			}
			
			redirect( base_url().'cp/desk/orders/edit/'.$order_id, 'refresh' );
		}
		else
		{
			$this->messages->add( _('ERROR : Can\'t remove the product from the order !'), 'error' );
			redirect( base_url().'cp/desk/orders', 'refresh' );
		}
	}
	
	private function form_url_slug($company_name = null){
	
		$company_name = str_replace(" ","-",$company_name);
		return $company_name;
	}
	
	function show_purchase(){
		/*==========to show order details in thick box===========*/
		$orders_id=$this->input->post('orders_id');
		
		$data['orderData'] = $this->Morders->get_desk_orders(array('desk_orders.id'=>$orders_id));
		$data['order_details_data'] = $order_details_data = $this->Morder_details->get_desk_order_details(array('order_id'=>$orders_id));
		
		$total='';
		$TempExtracosts=array();
		for($i=0;$i<count($order_details_data);$i++){
			if($order_details_data[$i]->add_costs != ""){
				$rsExtracosts = explode("#",$order_details_data[$i]->add_costs);
				for($j=0;$j<count($rsExtracosts);$j++){
					$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
				}
			}
			$total += $order_details_data[$i]->total;
		}
		
		$data['TempExtracosts'] = $TempExtracosts;
		$data['total'] = $total;
			
		$returned_data=$this->load->view('cp/desk_order_details',$data,true);
		echo $returned_data;
		exit;
			
	}
}