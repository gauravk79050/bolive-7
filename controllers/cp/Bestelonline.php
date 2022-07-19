<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Bestelonline extends CI_Controller {
	var $company_id = '';
	var $ibsoft_active = false;
	var $upload_path = '';
	function __construct() {
		parent::__construct ();
		
		$this->load->helper ( 'url' );
		
		$this->load->library ( 'session' );
		$this->load->library ( 'messages' );
		
		$this->load->model ( 'Mgeneral_settings' );
		$this->load->model ( 'Mopening_hours' );
		$this->load->model ( 'Mcompany' );
		$this->load->model ( 'mcp/Mcompetitor' );
		
		$is_logged_in = $this->session->userdata ( 'cp_is_logged_in' );
		if (! isset ( $is_logged_in ) || $is_logged_in != true) {
			redirect ( 'cp/login' );
		}
		
		$this->company_id = $this->session->userdata ( 'cp_user_id' );
		$this->company_role = $this->session->userdata ( 'cp_user_role' );
		$this->company_parent_id = $this->session->userdata ( 'cp_user_parent_id' );
		
		$this->company = array ();
		$company = $this->Mcompany->get_company ();
		if (! empty ( $company ))
			$this->company = $company [0];
	
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active ( $this->company_id );
		$this->upload_path = realpath ( APPPATH . '../assets/cp/images/company-gallery' );
	}
	function index() {
		$this->bp_settings ();
	}
	function bp_settings() {
		if ($this->company_role == 'master' || $this->company_role == 'sub') {
			$data ['days'] = $days = $this->Mopening_hours->get_days ();
			
			if ($this->input->post ( 'save_bp_settings' )) {
				// print_r($this->input->post());
				$data ['url'] = '';
				$company_img = $this->input->post ( 'old_company_img' );
				
				$new_img_name = '';
				if($this->input->post('image_name')){
				
					//list($name,$ext) = explode(".",$this->input->post('image_name'));
					//$img_name = str_replace("cropped_","",$this->input->post('image_name'));
					
					$prefix = 'cropped_';
					$str = $this->input->post('image_name');
						
					if (substr($str, 0, strlen($prefix)) == $prefix) {
						$str = substr($str, strlen($prefix));
					}
					
					$img_name = isset($str)?$str:$this->input->post('image_name');
					//$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));//403
					$image_file = file_get_contents(dirname(__FILE__).'/../../../assets/temp_uploads/'.$this->input->post('image_name'));
					if(file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/company_img/'.$img_name, $image_file)){
						$new_img_name = $img_name;
						
						//$this->resize_company_images($img_name);
						$this->load->helper('resize');
						resize_images('company_img',$img_name,false,$this->company_id);
					}
				}
				
				//$new_img_name = $this->upload_image ( 'company_img', './assets/cp/images/company_img/' );
				if ($new_img_name != '')
					$company_img = $new_img_name;
				if ($this->input->post ( 'existing_order_page' ) != '') {
					
					$url = $this->input->post ( 'existing_order_page' );
					if (! filter_var ( $url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED )) {
						$data ['wrong_url_status'] = '1';
					} else {
						$result = $this->Mcompetitor->get_competitor ();
						// $url1=$url;
						$url = parse_url ( $url );
						$url_host = $url ['host'];
						foreach ( $result as $results ) {
							$result1 = array (
									'parse' => parse_url ( $results ['competitor_url'] ) 
							);
							if ($result1 ['parse'] ['host'] == $url ['host']) {
								$flag = '1';
							}
						}
						if (isset ( $flag )) {
							$data ['url_status'] = '1';
							$data ['url'] = '';
						} else {
							$data ['url'] = $this->input->post ( 'existing_order_page' );
						}
					}
				}
				
				$update_company = array (
						'company_img' => $company_img,
						'company_desc' => $this->input->post ( 'company_desc' ),
						'company_fb_url' => $this->input->post ( 'company_fb_url' ),
						'existing_order_page' => $data ['url'] 
				);
				
				$this->Mcompany->update_company_details ( $this->company_id, $update_company );
				
				if($this->company->ac_type_id != 3){
					if($update_company['existing_order_page'] != ''){
						$this->session->set_userdata('cp_website',$update_company['existing_order_page']);
					}else{
						$this->load->model('Mcompany_type');
						$company_type_ids = explode("#",$this->company->type_id);
						$company_type = $this->Mcompany_type->get_company_type(array('id' => $company_type_ids[0]));
						if(!empty($company_type)){
							$cp_website = $this->config->item('portal_url').$company_type['0']->slug.'/'.$this->company->company_slug;
							$this->session->set_userdata('cp_website',$cp_website);
						}
					}
				}
				
				$pay_methods = $this->input->post ( 'pay_methods' );
				
				$update_settings = array();
				if(($this->company->ac_type_id == 3 || $this->company->ac_type_id == 2) && $this->company->on_trial == 0){
					$update_settings = array (
							'hide_bp_intro' => ($this->input->post ( 'hide_bp_intro' )) ? 1 : 0,
							'pay_methods' => (! empty ( $pay_methods )) ? json_encode ( $pay_methods ) : '',
							'company_account_num' => $this->input->post ( 'company_account_num' ),
							'set_terms_and_conditions' => ($this->input->post ( 'set_terms_and_conditions' )) ? 1 : 0,
							'terms_and_conditions' => $this->input->post ( 'terms_and_conditions' ),
							'show_hide_bp_shop' => $this->input->post("show_hide_bp_shop")
					);
				}else{
					$update_settings = array (
							'hide_bp_intro' => ($this->input->post ( 'hide_bp_intro' )) ? 1 : 0,
							'pay_methods' => (! empty ( $pay_methods )) ? json_encode ( $pay_methods ) : '',
							'company_account_num' => $this->input->post ( 'company_account_num' ),
							'set_terms_and_conditions' => ($this->input->post ( 'set_terms_and_conditions' )) ? 1 : 0,
							'terms_and_conditions' => $this->input->post ( 'terms_and_conditions' ),
							'allow_orders_bo' => $this->input->post ( 'allow_orders_bo' ),
							'portal_free_order_type' => $this->input->post ( 'portal_free_order_type' ),
							'portal_free_ordertime' => $this->input->post ( 'portal_free_ordertime' )
							//'shop_testdrive' => $this->input->post("shop_testdrive")
					);
					if($this->company->ac_type_id != 1){
						$update_settings['shop_testdrive'] = $this->input->post("shop_testdrive");
					}
				}
				
				
				/*if($this->company->ac_type_id == 1 || ($this->company->ac_type_id == 2 && $this->company->on_trial == 1) || ($this->company->ac_type_id == 3 && $this->company->on_trial == 1) ){
					$update_settings = array (
							'hide_bp_intro' => ($this->input->post ( 'hide_bp_intro' )) ? 1 : 0,
							'pay_methods' => (! empty ( $pay_methods )) ? json_encode ( $pay_methods ) : '',
							'company_account_num' => $this->input->post ( 'company_account_num' ),
							'set_terms_and_conditions' => ($this->input->post ( 'set_terms_and_conditions' )) ? 1 : 0,
							'terms_and_conditions' => $this->input->post ( 'terms_and_conditions' ),
							'allow_orders_bo' => $this->input->post ( 'allow_orders_bo' ),
							'portal_free_order_type' => $this->input->post ( 'portal_free_order_type' ),
							'portal_free_ordertime' => $this->input->post ( 'portal_free_ordertime' ),
							'shop_testdrive' => $this->input->post("shop_testdrive")
					);
				}else{
					$update_settings = array (
							'hide_bp_intro' => ($this->input->post ( 'hide_bp_intro' )) ? 1 : 0,
							'pay_methods' => (! empty ( $pay_methods )) ? json_encode ( $pay_methods ) : '',
							'company_account_num' => $this->input->post ( 'company_account_num' ),
							'set_terms_and_conditions' => ($this->input->post ( 'set_terms_and_conditions' )) ? 1 : 0,
							'terms_and_conditions' => $this->input->post ( 'terms_and_conditions' ),
							'shop_testdrive' => $this->input->post("shop_testdrive")
					);
				}*/
				
				$this->Mgeneral_settings->update_company_general_settings ( $this->company_id, $update_settings );
				
				$opening_hours = array ();
				if (! empty ( $days )) {
					$time_1 = $this->input->post ( 'time_1' );
					$time_2 = $this->input->post ( 'time_2' );
					$time_3 = $this->input->post ( 'time_3' );
					$time_4 = $this->input->post ( 'time_4' );
					
					foreach ( $days as $d ) {
						$opening_hours [$d->id] = array (
								'time_1' => $time_1 [$d->id],
								'time_2' => $time_2 [$d->id],
								'time_3' => $time_3 [$d->id],
								'time_4' => $time_4 [$d->id] 
						);
					}
				}
				
				$this->Mopening_hours->update_opening_hours ( $this->company_id, $opening_hours );
				
				$this->messages->add ( _ ( 'Changes saved successfully !' ), 'success' );
			}
			
			$data ['general_settings'] = $this->Mgeneral_settings->get_general_settings ();
			$data ['company'] = $this->Mcompany->get_company ();
			
			$opening_hours = $this->Mopening_hours->get_opening_hours ();
			
			$open_hr = array ();
			if (! empty ( $opening_hours ))
				foreach ( $opening_hours as $oh )
					$open_hr [$oh->day_id] = array (
							'time_1' => $oh->time_1,
							'time_2' => $oh->time_2,
							'time_3' => $oh->time_3,
							'time_4' => $oh->time_4 
					);
			
			$data ['opening_hours'] = $open_hr;
			
			$company = $this->Mcompany->get_company ();
			$data ['ac_type_id'] = $company [0]->ac_type_id;
			$data ['trail_date'] = $company [0]->trial;
			
			$company_type_id = $company [0]->type_id;
			$result = $this->Mcompany->get_co_type ( $company_type_id );
			$type_name = $result ['company_type_name'];
			$data ['type_slug'] = str_replace ( " ", "+", $type_name );
			$data ['company_slug'] = $company [0]->company_slug;
			$data ['content'] = 'cp/bestelonline-settings';
			$this->load->view ( 'cp/cp_view', $data );
		} elseif ($this->company_role == 'super') {
			$data ['content'] = 'cp/restricted';
			$this->load->view ( 'cp/cp_view', $data );
		}
	}
	
	function refresh_all_images($type = null,$company_id = null){
		set_time_limit(0);
		
		if($type){
			$this->load->helper('resize');
			refresh_all_images($type,$company_id);
		}
		else{
			die('PLEASE PROVIDE IMAGE TYPE BY ADDING /{TYPE} TO THE URL OR /{TYPE}/{COMPANY_ID} IF WANT TO REFRESH FOR ONLY A SPECIFIC COMPANY');
		}
	}
	
	function photogallery() {
		if ($this->company_role == 'master' || $this->company_role == 'sub') {
			if ($this->input->post ( 'save_images' )) {
				if ($this->input->post ( 'gallery_images' )) {
					$old_gallery_images = $this->input->post ( 'old_gallery_images' );
					
					$gallery_images = $this->input->post ( 'gallery_images' );
					/*
					 * $gallery_images = @substr($gallery_images,0,-2); $gallery_images = @explode(', ',$gallery_images); if(!empty($gallery_images)) { $img_names = array(); foreach($gallery_images as $path) $img_names[] = basename($path); if(!empty($img_names)) { $img_names = implode(', ',$img_names); if($old_gallery_images) $img_names = $old_gallery_images.', '.$img_names; $update_settings = array( 'company_gallery_imgs' => $img_names ); $this->Mgeneral_settings->update_company_general_settings($this->company_id,$update_settings); $this->messages->add(_('Gallery updated successfully !'), 'success'); } } else $this->messages->add(_('No images uploaded !'), 'error');
					 */
					
					$image_file = file_get_contents ( base_url () . 'assets/temp_uploads/cropped_' . $gallery_images );
					if (file_put_contents ( dirname ( __FILE__ ) . '/../../../assets/cp/images/company-gallery/' . $this->company->company_slug . '/' . $gallery_images, $image_file )) {
						$img_names = $old_gallery_images . ', ' . $gallery_images;
						$update_settings = array (
								'company_gallery_imgs' => $img_names 
						);
						$this->Mgeneral_settings->update_company_general_settings ( $this->company_id, $update_settings );
						
						$this->messages->add ( _ ( 'Gallery updated successfully !' ), 'success' );
					} else {
						$this->messages->add ( _ ( 'Gallery not updated successfully !' ), 'error' );
					}
				} else
					$this->messages->add ( _ ( 'Upload some images first !' ), 'error' );
			}
			
			if ($this->input->post ( 'action' ) == 'delete_image') {
				$image_index = $this->input->post ( 'image_index' );
				$image_str = $this->input->post ( 'image_str' );
				
				$image_str = explode ( ', ', $image_str );
				unset ( $image_str [$image_index] );
				
				$img_names = implode ( ', ', $image_str );
				
				$update_settings = array (
						'company_gallery_imgs' => $img_names 
				);
				$updated = $this->Mgeneral_settings->update_company_general_settings ( $this->company_id, $update_settings );
				
				if ($updated)
					$data = array (
							'status' => 'success',
							'result' => $img_names 
					);
				else
					$data = array (
							'status' => 'error',
							'result' => $img_names 
					);
				
				echo json_encode ( $data );
				die ();
			}
			
			$gallery_upload_path = '';
			$company = $this->Mcompany->get_company ();
			$company_slug = $company [0]->company_slug;
			
			if (! $company_slug)
				$company_slug = $company [0]->username;
			
			if (! is_dir ( $this->upload_path . '/' . $company_slug ))
				mkdir ( $this->upload_path . '/' . $company_slug, 0777 );
			
			$data ['gallery_upload_path'] = '/../../../assets/cp/images/company-gallery/' . $company_slug;
			
			$data ['company_slug'] = $company_slug;
			$data ['general_settings'] = $this->Mgeneral_settings->get_general_settings ();
			$data ['content'] = 'cp/bestelonline-photogallery';
			
			$this->load->view ( 'cp/cp_view', $data );
		} elseif ($this->company_role == 'super') {
			$data ['content'] = 'cp/restricted';
			$this->load->view ( 'cp/cp_view', $data );
		}
	}
	
	function upload_image($img_name = NULL, $upload_path = NULL, $save_as_name = NULL) {
		if (! $img_name || ! $upload_path)
			return false;
		
		if ($_FILES [$img_name] ['name']) {
			$config ['upload_path'] = $upload_path;
			$config ['allowed_types'] = 'gif|jpg|jpeg|JPG|png';
			$config ['max_size'] = '100';
			$config ['max_width'] = '250';
			$config ['max_height'] = '250';
			$config ['remove_spaces'] = true;
			
			if ($save_as_name)
				$config ['file_name'] = $save_as_name;
			
			$this->load->library ( 'upload', $config );
			if (! $this->upload->do_upload ( $img_name )) {
				
				$image = '';
				$this->load->library ( 'messages' );
				$this->messages->add ( $this->upload->display_errors (), 'error' );
			} else {
				
				$data = array (
						'upload_data' => $this->upload->data () 
				);
				$image = $data ['upload_data'] ['file_name'];
			}
			
			return $image;
		} else
			return false;
	}
	function uploadify() {
		$this->load->helper ( 'uploadify' );
		$img_data = img_uploadify ();
		echo $img_data ['file_name'];
		
		$config = array (
				'source_image' => $img_data ['full_path'],
				'new_image' => $this->upload_path . '/thumbs',
				'maintain_ration' => true,
				'width' => 150,
				'height' => 100 
		);
		
		$this->load->library ( 'image_lib', $config );
		$this->image_lib->resize ();
	}
	
	/**
	 * This function is used for Uploading image via ajax
	 * 
	 * @return string An error message or an HTML of containing image and some information
	 */
	function ajax_image_upload() {
		$path = dirname ( __FILE__ ) . '/../../../assets/temp_uploads/';
		$valid_formats = array (
				"jpg",
				"png",
				"gif",
				"bmp",
				"jpeg",
				"JPG",
				"PNG",
				"GIF" 
		);
		if (isset ( $_POST ) and $_SERVER ['REQUEST_METHOD'] == "POST") {
			$name = $_FILES ['photoimg'] ['name'];
			$size = $_FILES ['photoimg'] ['size'];
			
			if (strlen ( $name )) {
				$txt = '';
				$ext = '';
				$parts = explode ( ".", $name );
				if (is_array ( $parts ) && isset ( $parts ['0'] ) && isset ( $parts ['1'] ))
					list ( $txt, $ext ) = explode ( ".", $name );
				
				if (in_array ( $ext, $valid_formats )) {
					if ($size < (4 * 1048576)) {
						$actual_image_name = time () . substr ( str_replace ( " ", "_", $txt ), 5 ) . "." . $ext;
						$tmp = $_FILES ['photoimg'] ['tmp_name'];
						if (move_uploaded_file ( $tmp, $path . $actual_image_name )) {
							$size = getimagesize ( $path . $actual_image_name );
							$maxWidth = 890;
							if ($size [0] > $maxWidth) {
								$maxHeight = ($size [1] / $size [0]) * $maxWidth;
								
								$this->load->library ( 'image_lib' );
								
								$config ['image_library'] = 'gd2';
								$config ['source_image'] = $path . $actual_image_name;
								$config ['maintain_ratio'] = TRUE;
								$config ['quality'] = 100;
								$config ['width'] = $maxWidth;
								$config ['height'] = $maxHeight;
								
								$this->load->library ( 'image_lib', $config );
								
								$this->image_lib->clear ();
								$this->image_lib->initialize ( $config );
								
								if (! $this->image_lib->resize ()) {
									echo $this->image_lib->display_errors ();
									exit ();
								}
							}
							echo '<img src="' . base_url () . "assets/temp_uploads/" . $actual_image_name . '" id="target" alt="' . _ ( "No image !!! please try again" ) . '" />
											<input type="hidden" name="image_name" id="image_name" value="' . $actual_image_name . '" /><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="' . _ ( "Crop" ) . '" onClick="crop();" /></div>';
						} else
							echo "<span style='color: red'>" . _ ( 'Image did not uploaded. Please try again' ) . "</span>";
					} else
						echo "<span style='color: red'>" . _ ( 'Image file size max 4 MB' ) . "</span>";
				} else
					echo "<span style='color: red'>" . _ ( 'Invalid file format..' ) . "</span>";
			} else
				echo "<span style='color: red'>" . _ ( 'Invalid file format..' ) . "</span>";
			
			exit ();
		}
	}
	
	/**
	 * This function is user for copping image via ajax
	 * 
	 * @return string HTML of cropped image.
	 */
	function crop_image() {
		$image = $this->input->post ( 'image_name' );
		
		$targ_w = $this->input->post ( 'w' );
		$targ_h = $this->input->post ( 'h' );
		$jpeg_quality = 90;
		$extension = end ( explode ( ".", $image ) );
		
		$src = dirname ( __FILE__ ) . '/../../../assets/temp_uploads/' . $image;
		if ($extension == "png" || $extension == "PNG")
			$img_r = imagecreatefrompng ( $src );
		if ($extension == "jpg" || $extension == "jpeg" || $extension == "JPG")
			$img_r = imagecreatefromjpeg ( $src );
		if ($extension == "gif" || $extension == "GIF")
			$img_r = imagecreatefromgif ( $src );
		$dst_r = ImageCreateTrueColor ( $targ_w, $targ_h );
		
		imagecopyresampled ( $dst_r, $img_r, 0, 0, $this->input->post ( 'x' ), $this->input->post ( 'y' ), $targ_w, $targ_h, $this->input->post ( 'w' ), $this->input->post ( 'h' ) );
		
		$image = 'cropped_'.$image;
		// header('Content-type: image/jpeg');
		imagejpeg ( $dst_r, dirname ( __FILE__ ) . '/../../../assets/temp_uploads/' . $image, $jpeg_quality );
		
		// Resizing image to 270*270
		$size = getimagesize ( dirname ( __FILE__ ) . '/../../../assets/temp_uploads/' . $image );
		$maxWidth = 270;
		$maxHeight = 270;
		if ($size [0] > $maxWidth || $size [1] > $maxHeight) {
			
			$this->load->library ( 'image_lib' );
			
			$config ['image_library'] = 'gd2';
			$config ['source_image'] = dirname ( __FILE__ ) . '/../../../assets/temp_uploads/' . $image;
			$config ['maintain_ratio'] = TRUE;
			$config ['quality'] = 100;
			$config ['width'] = $maxWidth;
			$config ['height'] = $maxHeight;
			
			$this->load->library ( 'image_lib', $config );
			
			$this->image_lib->clear ();
			$this->image_lib->initialize ( $config );
			
			if (! $this->image_lib->resize ()) {
				echo $this->image_lib->display_errors ();
				exit ();
			}
		}
		
		$new_html = "";
		$new_html .= "<img src='" . base_url () . "assets/temp_uploads/" . $image . "' alt='" . _ ( "Sorry !!! image has not been cropped. Please try again" ) . "' style='float: left; margin-right:20px;' /> <input type='hidden' name='image_name' id='image_name' value='cropped_" . $image . "' />";
		$new_html .= '<span style=" display: block; float: left; margin-top: 70px; width: 192px;">' . _ ( "Note: This image will be saved when you click the Save button below. To change image you can upload another image again" ) . '</span> <div style="clear:both;"></div><br/><br/>';
		
		echo $new_html;
	}
	
	/**
	 * Function to load help file- Integrating CI and Help and supportscript of Codecanyon
	 */
	function faq_new(){
		if ($this->company_role == 'master' || $this->company_role == 'sub') {
			
			$company = $this->Mcompany->get_company ();
			$company_slug = $company [0]->company_slug;
			
			if (! $company_slug)
				$company_slug = $company [0]->username;
			
			$data ['company_slug'] = $company_slug;
			$data ['general_settings'] = $this->Mgeneral_settings->get_general_settings ();
			$data ['content'] = 'cp/faq_new';
			
			$this->load->view ( 'cp/cp_faq_new_view', $data );
		} elseif ($this->company_role == 'super') {
			$data ['content'] = 'cp/faq_new';
			$this->load->view ( 'cp/cp_faq_new_view', $data );
		}
	}
	
	function testing(){
		// rename (dirname(__FILE__)."/../../../assets/cp/images/product/8360_Clone_Product X.008", dirname(__FILE__)."/../../../assets/cp/images/product/8360_Clone_Product X.008.jpg");
		
		
		
		/*$products = $this->db->select('id,company_id,image')->where('image <>','')->get('products')->result();
		echo "<pre>";
		$count = 1;
		$srcpath = dirname(__FILE__)."/../../../assets/cp/images/product/";
		foreach ($products as $product){
			if(file_exists($srcpath.$product->image)){
				if(end(explode(".",$product->image)) != 'png' && end(explode(".",$product->image)) != 'jpg' && end(explode(".",$product->image)) != 'jpeg' && end(explode(".",$product->image)) != 'gif' && end(explode(".",$product->image)) != 'JPG' && end(explode(".",$product->image)) != 'PNG' && end(explode(".",$product->image)) != 'JPEG' && end(explode(".",$product->image)) != 'GIF'){
					$img_type	= exif_imagetype($srcpath.$product->image);
					$extension	= image_type_to_extension($img_type);
					$new_img_name = $product->image.((isset($extension) && $extension != 'jpeg')?"$extension":'.jpg');
				
					rename($srcpath.$product->image,$srcpath.$new_img_name);
					
					$this->db->where("id",$product->id);
					$this->db->update("products", array("image" => $new_img_name));
				}
			}
				
		}*/
		// print_r($products);
	}
}

?>