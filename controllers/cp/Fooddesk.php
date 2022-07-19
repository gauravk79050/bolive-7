<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Keurslager Association
 *
 * This is a controller for fetching Keurslager Products and related things
 *
 * @author Aniket Singh <aniketsingh@cedcoss.com>, Abhay Hayaran <abhayhayaran@cedcoss.com>
 * @package OBS
 */
class Fooddesk extends CI_Controller{

	var $company_id = '';

	function __construct(){

		parent::__construct();

		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('cookie');
		$this->load->helper('file');
		$this->load->library('session');
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mcalender');
		$this->load->model('M_fooddesk');

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

		$this->rows_per_page = 20;

		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
		$this->fdb = $this->load->database('fdb',TRUE);
		$this->lang_u = get_lang( $_COOKIE['locale'] );
	}

	/**
	 * This function is used to fetch and show all saved report on the server for current company
	 */
	function products(){
		$common_array1 = array();
		$producers = $this->M_fooddesk->get_supplier_name();
		foreach ($producers as $producer){
			array_push($common_array1, $producer['s_name']);
		}
		$data['producers'] = $common_array1;

		$common_array2 = array();
		$suppliers = $this->M_fooddesk->get_real_supplier_name();
		foreach ($suppliers as $supplier){
			array_push($common_array2, $supplier['rs_name']);
		}
		$data['suppliers'] = $common_array2;
		

		$this->load->model('Mproducts');
		$this->load->model('Mcategories');
		$this->load->model('Msubcategories');

		$data['category_data'] = $this->Mcategories->get_categories();
		if ($this->input->post('id')){
			$data['subcategory_data']=$this->Msubcategories->get_sub_category($this->input->post('id'));
			echo json_encode($data['subcategory_data']);
			die;
		}

		$data['own_products'] = $this->Mproducts->get_product_without_sheets();

		$this->load->view('cp/fdd_products',$data);
	}

	/**
	 * This function is used to fetch products via ajax request
	 */
	function getAjaxProducts(){

		$s_id = $this->input->post('s_id');
		if($s_id)
			$products = $this->M_fooddesk->get_products('s_name,p_id,p_name,p_description', array('products.p_s_id' => $s_id,'approval_status'=>1));
		else
			$products = $this->M_fooddesk->get_products('s_name,p_id,p_name,p_description', array('approval_status'=>1));
		echo json_encode($products);
	}

	/**
	 * This function takes parameters via post request and adds keurslager product into current admin's account
	 */
	function add_k_prods(){
		$review_fixed = 'review&fixed';
		$cat_id = $this->input->post('cat_id');
		$subcat_id = $this->input->post('subcat_id');
		$kp_id = $this->input->post('kp_id');

		if(!$cat_id || !$subcat_id || !$kp_id)
			echo false;
		else{
			$kp_detail = $this->M_fooddesk->get_products('',array('p_id' => $kp_id));
			foreach ($kp_detail[0]->ingredients as $k=>$ing){
				$prefix = $this->M_fooddesk->get_prfx(array('product_id'=>$kp_detail[0]->p_id, 'ing_id'=>$ing->ing_id));
				if(!empty($prefix)){
					$pre = $prefix[0]['prefix'];
				}else{
					$pre = '';
				}
				$kp_detail[0]->ingredients[$k]->prefix = $pre;
			}
			if(!empty($kp_detail)){
				$kp_detail = $kp_detail[0];

				$current_pro_name = '';
				if($kp_detail->p_name.$this->lang_u != NULL && $kp_detail->p_name.$this->lang_u != '') {
					$current_pro_name = $kp_detail->p_name.$this->lang_u;
				}

				$current_pro_desc = '';
				if($kp_detail->p_description.$this->lang_u != NULL && $kp_detail->p_description.$this->lang_u != ''){
					$current_pro_desc = $kp_detail->p_description.$this->lang_u;
				}

				$insert_array = array(
					'company_id' => $this->company_id,
					'categories_id' => $cat_id,
					'subcategories_id' => $subcat_id,
					'proname' => $current_pro_name,
					'prodescription' => $current_pro_desc,
					'sell_product_option' => 'per_unit',
					'procreated' => date('Y-m-d H:i:s'),
					'direct_kcp' => 1,
					'direct_kcp_id' => $kp_detail->p_id,
					'recipe_weight' => 100,
					'fdd_producer_id' => $kp_detail->p_s_id,
					'fdd_supplier_id' => $kp_detail->p_rs_id,
					'fdd_prod_art_num' => $kp_detail->plu
				);

				if($kp_detail->image != ''){
//					copy($this->config->item('fdd_url').'assets/cp/images/products/'.$kp_detail->image, dirname(__FILE__).'/../../../assets/cp/images/product/'.date('Y_m_d_H_i_s').$kp_detail->image);

 					$image_file = @file_get_contents($this->config->item('fdd_url').'assets/cp/images/products/'.$kp_detail->image);

 					if($image_file != NULL){
	 					
	 					@file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/product/'.date('Y_m_d_H_i_s').$kp_detail->image, $image_file);
	 					$this->load->helper('resize');
	 					resize_images('product',date('Y_m_d_H_i_s').$kp_detail->image,false);
	 					$insert_array['image'] = date('Y_m_d_H_i_s').$kp_detail->image;
 					}
				}

				

				if($this->db->insert('products', $insert_array)){

					$prod_id = $this->db->insert_id();

					$this->load->model('Mproducts');

					// Now inserting Ingredients
					$ingredients = $kp_detail->ingredients;

					if(!empty($ingredients)){
					/*	$insert_array = array(
								'product_id' => $prod_id,
								'kp_id' => $kp_detail->p_id,
								'ki_id' => 0,
								'ki_name' => $current_pro_name,
								'display_order' => 1,
								'date_added' => date('Y-m-d H:i:s')
						);
						$this->db->insert('products_ingredients', $insert_array);*/
						foreach ($ingredients as $key => $ingredient){
							$display_name = $ingredient->ing_name.$this->lang_u;
							$display_result = $this->Mproducts->get_display_name($ingredient->i_id);
							if($display_result != ''){
								$display_name = $display_result;
							}

							$insert_array = array(
								'product_id' => $prod_id,
								'kp_id' => $ingredient->p_id,
								'ki_id' => $ingredient->i_id,
								'ki_name' => $display_name,
								'prefix' => $ingredient->prefix,
								'display_order' => ($key+2),
								'date_added' => date('Y-m-d H:i:s'),
								'have_all_id' => $ingredient->all_id
							);

							/*
							$pre = $this->M_fooddesk->get_prfx(array('product_id'=>$ingredient->p_id, 'ing_id'=>$ingredient->i_id));
							if(!empty($pre)){
								$insert_array['prefix'] = $pre[0]['prefix'];
							}

							*/
							$this->db->insert('products_ingredients', $insert_array);
						}
					}

					$ingredients_vetten = $kp_detail->ingredients_vetten;
					if(!empty($ingredients_vetten)){
						foreach ($ingredients_vetten as $key => $ingredient){
							$display_name = $ingredient->ing_name.$this->lang_u;
							$display_result = $this->Mproducts->get_display_name($ingredient->i_id);
							if($display_result != ''){
								$display_name = $display_result;
							}

							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $ingredient->p_id,
									'ki_id' => $ingredient->i_id,
									'ki_name' => $display_name,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s'),
									'have_all_id' => $ingredient->all_id
							);

							$this->db->insert('products_ingredients_vetten', $insert_array);
						}
					}

					$additives = $kp_detail->additives;
					if(!empty($additives)){
						//E-nbr status
						$enbr_setting = $this->Mproducts->get_enbr_status($this->company_id);
						foreach ($additives as $key => $ingredient){
							$display_name = $ingredient->ing_name.$this->lang_u;
							$ki_id = $ingredient->i_id;
							$display_name1 = '';
							$enbr_rel_ki_id = 0;

							$enbr_result = $this->Mproducts->get_e_current_nbr($ki_id,$enbr_setting['enbr_status']);
							if(!empty($enbr_result)){
								$ki_id = $enbr_result['ki_id'];
								$display_name = $enbr_result['ki_name'];
								$display_result = $this->Mproducts->get_display_name($enbr_result['ki_id']);
								if($display_result != ''){
									$display_name = $display_result;
								}

								$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
								$display_name1 = $enbr_result['enbr_rel_ki_name'];
								$display_result = $this->Mproducts->get_display_name($enbr_result['enbr_rel_ki_id']);
								if($display_result != ''){
									$display_name1 = $display_result;
								}
							}
							else{
								$display_result = $this->Mproducts->get_display_name($ki_id);
								if($display_result != ''){
									$display_name = $display_result;
								}
							}

							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $ingredient->p_id,
									'add_id' => $ingredient->add_id,
									'ki_id' => $ki_id,
									'ki_name' => $display_name,
									'enbr_rel_ki_id' => $enbr_rel_ki_id,
									'enbr_rel_ki_name' => $display_name1,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s'),
									'have_all_id' => $ingredient->all_id
							);

							$this->db->insert('products_additives', $insert_array);
						}
					}

					// Now inserting Traces
					$traces = $kp_detail->traces;
					if(!empty($traces)){

						foreach ($traces as $key => $trace){
							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $trace->p_id,
									'kt_id' => $trace->t_id,
									'kt_name' => $trace->t_name.$this->lang_u,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_traces', $insert_array);
						}
					}

					// Now inserting Allergence
					$allergences = $kp_detail->allergence;
					if(!empty($allergences)){
						foreach ($allergences as $key => $allergence){
							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $allergence->p_id,
									'ka_id' => $allergence->a_id,
									'ka_name' => $allergence->all_name.$this->lang_u,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_allergence', $insert_array);
						}
					}

					$sub_allergence = $kp_detail->sub_allergence;
					if(!empty($sub_allergence)){
						foreach ($sub_allergence as $key => $allergence){
							$insert_array = array(
									'product_id' => $prod_id,
									'kp_id' => $allergence->p_id,
									'parent_ka_id' => $allergence->parent_all_id,
									'sub_ka_id' => $allergence->all_id,
									'sub_ka_name' => $allergence->all_name.$this->lang_u,
									'display_order' => ($key+2),
									'date_added' => date('Y-m-d H:i:s')
							);

							$this->db->insert('product_sub_allergence', $insert_array);
						}
					}

					$quant_insert_array = array(
							'obs_pro_id'=>$prod_id,
							'fdd_pro_id'=>$kp_id,
							'quantity'=>100,
							'fixed'=>$kp_detail->$review_fixed
						);
					$this->db->insert('fdd_pro_quantity',$quant_insert_array);

					$this->session->set_userdata('action', 'category_json');

					echo $prod_id;
				}
				else{
					echo false;
				}
			}
			else{
				echo false;
			}
		}
	}

	/**
	 * This function takes parameters via post request and adds new fixed product into current admin's account with PWS product
	 */
	function add_new_fixed_product_comp(){
		$cat_id = $this->input->post('cat_id');
		$subcat_id = $this->input->post('subcat_id');
		$pro_name = trim($this->input->post('pro_name'));
	 	$supplier_name = $this->input->post('supp_name');
	 	$real_supplier_name = $this->input->post('real_supp_name');
	 	$art_no_p = trim($this->input->post('art_no_p'));
	 	$art_no_p = (($art_no_p != _('Article Number Producer')) && ($art_no_p != '') && ($art_no_p != '000'))?$art_no_p:'';

	 	$art_no_s = trim($this->input->post('art_no_s'));
	 	$art_no_s = (($art_no_s != _('Article Number Supplier')) && ($art_no_s != '') && ($art_no_s != '000'))?$art_no_s:'';

		if(!$cat_id || !$subcat_id || !$pro_name)
			echo false;
		else{
			$supplier_id = 0;
			$supplier_name = trim($supplier_name);
			if(($supplier_name != '') && ($supplier_name != _('Producer'))){
				$res = $this->M_suppliers->get_suppliers_data(array('LOWER(s_name)' => addslashes(mb_strtolower($supplier_name,'UTF-8')),'is_controller_cp'=>0));
				if(!empty($res)){
					$supplier_id = $res[0]['s_id'];
				}else{
					$insrt_array = array(
							's_name'=>addslashes($supplier_name),
							's_username' => str_replace(' ', '_', $supplier_name),
							's_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
							's_date_added' => date('Y-m-d h:i:s')
					);
					$this->fdb->insert('suppliers', $insrt_array);
					$supplier_id = $this->fdb->insert_id();
				}
			}

			$real_supplier_id = 0;
			$real_supplier_name = trim($real_supplier_name);
			if(($real_supplier_name != '') && ($real_supplier_name != _('Supplier'))){
				$res1 = $this->M_fooddesk->get_real_suppliers_data(array('LOWER(rs_name)' => addslashes(mb_strtolower($real_supplier_name,'UTF-8'))));
				if(!empty($res1)){
					$real_supplier_id = $res1[0]['rs_id'];
				}else{
					$insrt_array = array(
							'rs_name'=>addslashes($real_supplier_name),
							'rs_username' => str_replace(' ', '_', $real_supplier_name),
							'rs_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
							'rs_date_added' => date('Y-m-d h:i:s')
					);
					$this->fdb->insert('real_suppliers', $insrt_array);
					$real_supplier_id = $this->fdb->insert_id();
				}
			}

			

			$this->db->select('id');
			$this->db->where('LOWER(proname)',addslashes(strtolower($pro_name)));
			$this->db->where(array('company_id'=>$this->company_id,'fdd_producer_id'=>$supplier_id,'fdd_supplier_id'=>$real_supplier_id));
			$products = $this->db->get('products')->result_array();

			if(empty($products)){
				$insert_array = array(
						'company_id'=>$this->company_id,
						'categories_id'=> $cat_id,
						'subcategories_id'=>$subcat_id,
						'proname'=> addslashes(strtolower($pro_name)),
						'procreated'=>date('Y-m-d'),
						'direct_kcp'=>1,
						'status'=>'1',
						'fdd_producer_id'=>$supplier_id,
						'fdd_supplier_id'=>$real_supplier_id
				);
				$this->db->insert('products',$insert_array);
				$comp_pro_id = $this->db->insert_id();

				if($comp_pro_id){
					$this->db->select('id');
					if($art_no_p != ''){
				 		$this->db->where('fdd_prod_art_num',$art_no_p);
				 	}
				 	if($art_no_s != ''){
				 		$this->db->where('fdd_supp_art_num',$art_no_s);
				 	}
					$this->db->where('LOWER(proname)',addslashes(strtolower($pro_name)));
					$this->db->where(array('company_id'=>0,'fdd_producer_id'=>$supplier_id,'fdd_supplier_id'=>$real_supplier_id));
					$product_pws = $this->db->get('products')->result_array();

					if(empty($product_pws)){
						$insert_array = array(
								'company_id'=>0,
								'categories_id'=> 0,
								'subcategories_id'=>0,
								'proname'=> addslashes(strtolower($pro_name)),
								'procreated'=>date('Y-m-d'),
								'direct_kcp'=>1,
								'status'=>'0',
								'fdd_producer_id'=>$supplier_id,
								'fdd_supplier_id'=>$real_supplier_id,
								'fdd_prod_art_num'=>$art_no_p,
								'fdd_supp_art_num'=>$art_no_s
						);
						$this->db->insert('products',$insert_array);
						$pws_pro_id = $this->db->insert_id();
						$this->db->insert('contacted_via_mail',array('obs_pro_id'=>$pws_pro_id));
					}
					else{
						$pws_pro_id = $product_pws[0]['id'];

						$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$pws_pro_id))->result_array();
						if(empty($con_info)){
							$this->db->insert('contacted_via_mail', array('obs_pro_id' => $pws_pro_id));
						}
					}

					$insrt_quant_array = array(
							'obs_pro_id'=>$comp_pro_id,
							'fdd_pro_id'=>$pws_pro_id,
							'quantity'=>100,
							'is_obs_product'=>1,
							'semi_product_id'=>0,
							'fixed' => 1
					);
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

					$info = $this->db->get_where('products_pending',array('product_id'=>$pws_pro_id,'company_id'=>$this->company_id))->result_array();
					if(empty($info)){
						$this->db->insert('products_pending', array('product_id' => $pws_pro_id, 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
					}

					$insert_array = array(
							'product_id' => $comp_pro_id,
							'kp_id' => $pws_pro_id,
							'ki_id' => 0,
							'ki_name' => addslashes(strtolower($pro_name)),
							'display_order' => 0,
							'kp_display_order'=>1,
							'date_added' => date('Y-m-d H:i:s'),
							'is_obs_ing'=> 1
					);
					$this->db->insert('products_ingredients', $insert_array);

					$this->session->set_userdata('action', 'category_json');

					echo $comp_pro_id;
				}
				else{
					echo false;
				}
			}
			else{
				echo 'Already exists';
			}
		}
	}

	/**
	 * This function is used to fetch ingredients of particluar product
	 * @param int $prod_id It is the ID of product for which ingredients are to be fetched
	 */
	function get_ing_traces_allergence($prod_id = 0){
		$ingredients = array();
		$traces = array();
		$allergence = array();
		$products = array();
		if($prod_id){
			$products = $this->M_fooddesk->get_products('s_name,p_id,p_name,p_name_dch,p_name_fr,p_short_name,p_short_name_dch,p_short_name_fr,barcode,plu,data_sheet', array('products.p_id' => $prod_id));
			foreach ($products[0]->ingredients as $k=>$ing){
				$prefix = $this->M_fooddesk->get_prfx(array('product_id'=>$products[0]->p_id, 'ing_id'=>$ing->ing_id));
				if(!empty($prefix)){
					$pre = $prefix[0]['prefix'];
				}else{
					$pre = '';
				}
				$products[0]->ingredients[$k]->prefix = $pre;
			}

		}
		$response = array( 'product' => $products);
		echo json_encode($response);
	}

	function get_ing_traces_allergence_semi($prod_id = 0){
		$ingredients = array();
		$traces = array();
		$allergence = array();
		$products = array();
		if($prod_id){
			$products = $this->M_fooddesk->get_products('s_name,p_id,p_name,p_name_dch,p_name_fr,p_short_name,p_short_name_dch,p_short_name_fr,barcode,plu,product_type,data_sheet,approval_status', array('products.p_id' => $prod_id));
			if( isset( $products[0]->ingredients ) ){
				foreach ($products[0]->ingredients as $k=>$ing){
					$prefix = $this->M_fooddesk->get_prfx(array('product_id'=>$products[0]->p_id, 'ing_id'=>$ing->ing_id));
					if(!empty($prefix)){
						$pre = $prefix[0]['prefix'];
					}else{
						$pre = '';
					}
					$products[0]->ingredients[$k]->prefix = $pre;
				}
			}
		}
		$response = array( 'product' => $products);
		//echo json_encode($response);
		RETURN $response;
	}

	function add_new_product( $uploaded_files = 0 ){
		$msg = '';
		if($uploaded_files == -1){
			$msg = 'No file selected';
		}elseif($uploaded_files > 0){
			$msg = $uploaded_files.' files uploaded sucessfully.';
		}
		
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
		$data['msg']= $msg;
		$data['content'] = 'cp/fdd_add_products';
		$this->load->view('cp/cp_view',$data);
	}

	function update_own_prod_info(){
		$s_id = $rs_id = 0;
		$own_prod_id = $this->input->post('id');
		$supp_name = $this->input->post('supp_name');
		$real_supp_name = $this->input->post('real_supp_name');
		$art_no_p = $this->input->post('art_no_p');
		$art_no_s = $this->input->post('art_no_s');

		
		$this->fdb->select('s_id');
		$result= $this->fdb->get_where('suppliers', array('s_name' => $supp_name,'is_controller_cp' => 0))->result();
		if (!empty($result)){
			$s_id = $result[0]->s_id;
		}

		$this->fdb->select('rs_id');
		$result = $this->fdb->get_where('real_suppliers', array('rs_name' => $real_supp_name))->result();
		if (!empty($result)){
			$rs_id = $result[0]->rs_id;
		}

		$data = array(
				'fdd_producer_id'=>$s_id,
				'fdd_supplier_id'=>$rs_id,
				'fdd_prod_art_num'=>$art_no_p,
				'fdd_supp_art_num'=>$art_no_s
		);
		$this->db->where('id',$own_prod_id);
		$this->db->update('products',$data);
	}

	function upload_pdf(){
		define ("FILEREPOSITORY","./assets/cp/fdd_pdf");
		$msg = '';
		$file_counter = 0;
		for($i = 1;$i <= 5;$i++){
			if($_FILES['pdf'.$i]['name']){
				if (is_uploaded_file($_FILES['pdf'.$i]['tmp_name'])) {

					if ($_FILES['pdf'.$i]['type'] != "application/pdf") {
						//$msg =  "Productsheet must be uploaded in PDF format.";
					} else {
						$name = $_FILES['pdf'.$i]['name'];
						$name = str_replace(' ', '', $name);
						$file_name = substr($name, 0, -3).date('Y-m-d_H:i:s');
						$result = move_uploaded_file($_FILES['pdf'.$i]['tmp_name'], FILEREPOSITORY."/$file_name.pdf");
						if ($result == 1){
							// connect and login to FTP server
							$ftp_server = "webilyst.com";
							$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
							$login = ftp_login($ftp_conn, "fooddesk@webilyst.com", "sW2wgZ]]U5Fa");

							$file = FILEREPOSITORY."/$file_name.pdf";

							ftp_pasv($ftp_conn, true);

							// upload file
							if (ftp_put($ftp_conn, "assets/obs_productsheets/".$file_name.".pdf", $file, FTP_BINARY))
							{
								$insert_array = array(
									'sheet_name'=>$file_name.".pdf",
									'uploaded_by'=>$this->company->id,
									'date' => date("Y-m-d H:i:s")
								);
								$this->M_fooddesk->add_productsheet($insert_array);

								//$msg = "File uploades sucessfully".$name;
								$file_counter++;
							}
							else
							{
							//$msg = "Error uploading $name.";
							}

							// close connection
							ftp_close($ftp_conn);


						}else{
							//$msg = "There was a problem uploading the file.";
						}
					} #endIF
				} #endIF

			}
			//echo $msg;
		}
		if($file_counter == 0){
			$file_counter = -1;
		}
		redirect(base_url().'cp/fooddesk/add_new_product/'.$file_counter);
	}

	function send_request_for_credit($crdt = 0){
		$response = array();
		if( $this->company_role == 'master' || $this->company_role == 'super' ){

			if($crdt != 0){
				$mail_data['credit'] = $crdt;

				$mail_body = $this->load->view('mail_templates/'.$this->lang_u.'/request_fooddesk_credits',$mail_data,true);

				if(send_email($this->config->item('site_admin_email'), $this->company->email , _("Account Change Request"), $mail_body, NULL, NULL, NULL, 'company', 'site_admin', 'change_account_type_req' ))
					$response = array('error'=>0,'message'=>_("<b>Request succesfully sent</b> - We will review your request asap and your credit will be added."));
				else
					$response = array('error'=>1,'message'=>_("Request is not sent succesfully. Please try again"));

			}else{
				$response = array('error'=>1,'message'=>_("We can not proceed your request. Some error occured"));
			}
		}
		else{
			$response = array('error'=>1,'message'=>_("Sorry! You are not allowed to change type at this moment"));
		}
		echo json_encode($response);
	}

	function make_same(){
		if($_COOKIE['language'] == 'en'){
			$ing_var = "";
		}elseif($_COOKIE['language'] == 'nl'){
			$ing_var = "_dch";
		}else{
			$ing_var = "_fr";
		}
		$obs_pro_id = $this->input->post('obs_pro_id');
		$fdd_pro_id = $this->input->post('fdd_pro_id');


		if(!$obs_pro_id || !$fdd_pro_id){
			echo false;
		}
		else{
			$kp_detail 	= $this->M_fooddesk->get_products('',array('p_id' => $fdd_pro_id));
			$fixed_s 		= $this->get_fixed_ajax( $fdd_pro_id );
			if($fixed_s['product_type'] == 1)
			{
				$fixed = 1;
			}else{
				$fixed = $fixed_s['review&fixed'];
			}
			if(!empty($kp_detail)){
				$kp_detail = $kp_detail[0];

				$current_pro_name = '';
				$pro_ind = 'p_short_name'.$ing_var;
				if($kp_detail->$pro_ind == NULL || $kp_detail->$pro_ind == ''){
					$pro_ind = 'p_name'.$ing_var;
				}

				if($kp_detail->$pro_ind != NULL && $kp_detail->$pro_ind != ''){
					$current_pro_name = $kp_detail->$pro_ind;
				}

				$current_pro_desc = '';
				$pro_ind = 'p_description'.$ing_var;
				if($kp_detail-> $pro_ind != NULL && $kp_detail-> $pro_ind != ''){
					$current_pro_desc = $kp_detail-> $pro_ind;
				}

				
				if($obs_pro_id){
					$prod_id = $obs_pro_id;

					//adding ingredients, traces and allergence in products which contains this fixed products
					$this->db->select('fdd_pro_quantity.*,general_settings.language_id,general_settings.enbr_status');
					$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
					$this->db->join('general_settings','general_settings.company_id = products.company_id');
					$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$obs_pro_id,'fdd_pro_quantity.is_obs_product'=>1));
					$used_obs_pro = $this->db->get('fdd_pro_quantity')->result_array();
					if(!empty($used_obs_pro)){
						foreach ($used_obs_pro as $u_obs_pro){
							if($u_obs_pro['language_id'] == '3'){
								$ing_var = '_fr';
							}else{
								$ing_var = '_dch';
							}
							$this->load->model('Mproducts');

							$order = 0;
							$this->db->where(array('product_id' => $u_obs_pro['obs_pro_id'],'kp_id' => $u_obs_pro['fdd_pro_id'],'ki_id' => 0,'is_obs_ing'=>1));
							$res = $this->db->get('products_ingredients')->result_array();

							if(!empty($res)){
								$order = $res[0]['kp_display_order'];
							}

							$ingredients = $kp_detail->ingredients;

							$insert_array = array(
										'product_id' => $u_obs_pro['obs_pro_id'],
										'kp_id' => $kp_detail->p_id,
										'ki_id' => 0,
										'ki_name' => $current_pro_name,
										'display_order' => 1,
										'kp_display_order'=>$order,
										'date_added' => date('Y-m-d H:i:s'),
										'is_obs_ing'=> 0
							);

							$this->db->where(array('product_id' => $u_obs_pro['obs_pro_id'],'kp_id' => $u_obs_pro['fdd_pro_id'], 'ki_id' => 0,'is_obs_ing'=>1));
							$this->db->update('products_ingredients', $insert_array);

							
							$insert_array = array(
									'product_id' => $u_obs_pro['obs_pro_id'],
									'kp_id' => $kp_detail->p_id,
									'ki_id' => 0,
									'ki_name' => '(',
									'display_order' => 2,
									'kp_display_order'=>$order,
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_ingredients', $insert_array);

							$key = 3;
							if(!empty($ingredients)){
								foreach ($ingredients as $ingredient){
									$ing_ind = 'ing_name'.$ing_var;
									$display_name = $ingredient-> $ing_ind;
									$ing_name 		= $ingredient->ing_name;
									$ing_name_fr 	= $ingredient->ing_name_fr;
									$ing_name_dch 	= $ingredient->ing_name_dch;
									$ki_id = $ingredient->i_id;
								
									$enbr_result = $this->get_e_current_nbr($ki_id,$u_obs_pro['enbr_status'],$ing_var);
									if(!empty($enbr_result)){
										$ki_id 			= $enbr_result['ki_id'];
										$display_name 	= $enbr_result['ki_name'];
									}

									$insert_array = array(
											'product_id' => $u_obs_pro['obs_pro_id'],
											'kp_id' => $ingredient->p_id,
											'ki_id' => $ki_id,
											'ki_name' => $display_name,
											'display_order' => $key,
											'kp_display_order'=>$order,
											'date_added' => date('Y-m-d H:i:s'),
											'have_all_id'=>$ingredient->all_id,
											'aller_type'		=> $ingredient->aller_type,
											'aller_type_fr'		=> $ingredient->aller_type_fr,
											'aller_type_dch'	=> $ingredient->aller_type_dch,
											'allergence'		=> $ingredient->allergence,
											'allergence_fr'		=> $ingredient->allergence_fr,
											'allergence_dch'	=> $ingredient->allergence_dch,
											'sub_allergence'	=> $ingredient->sub_allergence,
											'sub_allergence_fr'	=> $ingredient->sub_allergence_fr,
											'sub_allergence_dch'=> $ingredient->sub_allergence_dch
									);
									$this->db->select('ki_id');
									$res_ingre = $this->db->get_where('ingredients', array('ki_id' => $ki_id) )->row_array();

									if( empty($res_ingre) ){
										if( $u_obs_pro['enbr_status'] == 1 ){
											if( !empty( $enbr_result ) ){
												$object = array(
															'ki_id' 	 => $ki_id,
															'ki_name'	 => $display_name,
															'ki_name_fr' => $display_name,
															'ki_name_dch'=> $display_name
														);
											}
											else{
												$object = array(
														'ki_id' 	 => $ki_id,
														'ki_name'	 => $ing_name,
														'ki_name_fr' => $ing_name_fr,
														'ki_name_dch'=> $ing_name_dch
													);
											}
										}
										if( $u_obs_pro['enbr_status'] == 2 ){
											$object = array(
														'ki_id' 	 => $ki_id,
														'ki_name'	 => $ing_name,
														'ki_name_fr' => $ing_name_fr,
														'ki_name_dch'=> $ing_name_dch
													);
										}
										$this->db->insert('ingredients', $object);
									}
									$this->db->insert('products_ingredients', $insert_array);
									$key++;
								}
							}

							$insert_array = array(
									'product_id' => $u_obs_pro['obs_pro_id'],
									'kp_id' => $kp_detail->p_id,
									'ki_id' => 0,
									'ki_name' => ')',
									'display_order' => $key,
									'kp_display_order'=>$order,
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_ingredients', $insert_array);
							

							$allergences = $kp_detail->allergence;
							if(!empty($allergences)){
								foreach ($allergences as $key => $allergence){
									$aller_ind = 'all_name'.$ing_var;
									$insert_array = array(
											'product_id' => $u_obs_pro['obs_pro_id'],
											'kp_id' => $allergence->p_id,
											'ka_id' => $allergence->all_id,
											'ka_name' => $allergence-> $aller_ind,
											'by_ingredient' => $allergence->by_ingredient,
											'display_order' => ($key+2),
											'date_added' => date('Y-m-d H:i:s')
									);
									$this->db->insert('products_allergence', $insert_array);
								}
							}

							$sub_allergence = $kp_detail->sub_allergence;
							if(!empty($sub_allergence)){
								foreach ($sub_allergence as $key => $allergence){
									$aller_ind_sub = 'all_name'.$ing_var;
									$insert_array = array(
											'product_id' => $u_obs_pro['obs_pro_id'],
											'kp_id' => $allergence->p_id,
											'parent_ka_id' => $allergence->parent_all_id,
											'sub_ka_id' => $allergence->all_id,
											'sub_ka_name' => $allergence-> $aller_ind_sub,
											'display_order' => ($key+2),
											'date_added' => date('Y-m-d H:i:s')
									);
									$this->db->insert('product_sub_allergence', $insert_array);
								}
							}

							$traces = $kp_detail->traces;
							if(!empty($traces)){
								foreach ($traces as $key => $trace){
									$t_index = 't_name'.$ing_var;
									$insert_array = array(
											'product_id' => $u_obs_pro['obs_pro_id'],
											'kp_id' => $trace->p_id,
											'kt_id' => $trace->t_id,
											'kt_name' => $trace-> $t_index,
											'display_order' => ($key+2),
											'date_added' => date('Y-m-d H:i:s')
									);
									$this->db->insert('products_traces', $insert_array);
								}
							}
						}
 						$this->db->where(array('fdd_pro_id'=>$obs_pro_id,'is_obs_product'=>1));
 						$this->db->update( 'fdd_pro_quantity', array('fdd_pro_id'=>$fdd_pro_id, 'is_obs_product'=>0, 'fixed'=>$fixed ) );
					}

					//$this->db->delete('products', array('id'=> $obs_pro_id));
					//$this->db->delete('products_labeler', array('product_id' => $obs_pro_id));
					$this->db->delete('products_pending', array('product_id' => $obs_pro_id));

					//
					//$this->db->delete('contacted_via_mail', array('obs_pro_id' => $obs_pro_id));

					$this->session->set_userdata('action', 'category_json');

					echo $prod_id;
				}
				else{
					echo "0";
				}
			}
			else{
				echo "0";
			}
		}
	}

	function pending_products(){
		$common_array1 = array();
		$common_array2 = array();
		$producers = $this->M_fooddesk->get_supplier_name();
		foreach ($producers as $producer){
			array_push($common_array1, $producer['s_name']);
		}
		$suppliers = $this->M_fooddesk->get_real_supplier_name();
		foreach ($suppliers as $supplier){
			array_push($common_array2, $supplier['rs_name']);
		}
		$data['producers'] = array_merge($common_array1,$common_array2);

		$this->load->view('cp/fdd_pending_products',$data);
	}

	function save_pending_products(){
		$pending_products = $this->input->post('data_array');
		foreach ($pending_products as $pending_product){
			if($pending_product['product_name'] != '' && $pending_product['supplier_name'] != ''){
				$insert_array = array(
					'pp_name'=>addslashes($pending_product['product_name']),
					'pp_supplier'=>addslashes($pending_product['supplier_name']),
					'ordered_by'=>$this->company->id
				);
				$this->M_fooddesk->insert_pending_product($insert_array);
			}
		}

		echo _("Pending Products submitted successfully");
	}

	function check_more_suggestion(){
		$data['obs_pro_id'] = $this->input->post('obs_pro_id');
		if($data['obs_pro_id'] != ''){
			

			$this->db->select('proname, fdd_producer_id, fdd_supplier_id, fdd_prod_art_num, fdd_supp_art_num');
			$pro_data = $this->db->get_where('products',array('id' => $data['obs_pro_id']))->row_array();
			$obs_pro_name = $pro_data['proname'];
			$pr_id = $pro_data['fdd_producer_id'];
			$sr_id = $pro_data['fdd_supplier_id'];
			$art_num_p = $pro_data['fdd_prod_art_num'];
			$art_num_s = $pro_data['fdd_supp_art_num'];

			$searched = array();
			$i = 0;
			while (count($searched) < 10 && $i < 100 ) {
				$obs_pro_name_str = substr($obs_pro_name,0,strlen($obs_pro_name)-$i);
				$searched2 = $this->M_fooddesk->get_suggestion($obs_pro_name_str, $art_num_p, $art_num_s, $pr_id, $sr_id);
				$searched = array_unique(array_merge($searched,$searched2), SORT_REGULAR);
				$i++;
			}

			if(count($searched) > 10){
				$searched = array_splice($searched, count($searched)-10);//shows results not more than 10
			}

			$data['searched'] = $searched;
			$this->load->view('cp/check_more_suggestion_view',$data);
		}
	}

	function products_suggestions($obs_pro_id = 0){
		$data = array();

		if($obs_pro_id != 0){
			
			$total_used_pro = $this->M_fooddesk->get_quant_details($obs_pro_id);

			foreach ($total_used_pro as $key => $val){
				

				$prefixes = $this->M_fooddesk->get_fdd_pro_prefixes(array('kp_id'=>$val['fdd_pro_id'],'product_id'=>$obs_pro_id,'is_obs_ing'=>0));
				if(!empty($prefixes)){
					$total_used_pro[$key]['prefix'] = $prefixes[0]['prefix'];
				}else{
					$total_used_pro[$key]['prefix'] = '';
				}

				

				$names = $this->M_fooddesk->get_fdd_pro_details($val['fdd_pro_id']);
				$pro_display_name = '';

				if($names[0]['p_name'.$this->lang_u] != ''){
					$pro_display_name .= $names[0]['p_name'.$this->lang_u].'--';
				}

				$pro_display_name .= $names[0]['s_name'].'--EAN: ';

				if($names[0]['barcode'] != NULL){
					$pro_display_name .= $names[0]['barcode'].'--PLU: ';
				}else {
					$pro_display_name .= 'No EAN Found!--PLU: ';
				}

				if($names[0]['plu'] != NULL){
					$pro_display_name .= $names[0]['plu'];
				}else {
					$pro_display_name .= 'No PLU Found!';
				}

				$total_used_pro[$key]['pro_display_name'] = $pro_display_name;

			}

			$data['total_used_pro']=$total_used_pro;
		}
		
		$data['fdd_lang']=$this->lang_u;
		$data['weight_list'] = $this->db->get('weights_and_measures')->result_array();

		$this->load->view('cp/fdd_products_suggestions', $data);
	}

	function get_serched_AjaxProducts( $type = 'fdd_prod' ){
		if($type == 'fdd_prod')
		{
			$search_str = addslashes($this->input->post('search_str'));
			$direct_add = $this->input->post('direct_add');
			$recipe_option = $this->input->post('recipe_option');
			$sel_lang = $this->input->post('sel_lang');
			$result_array = array();
			if($direct_add){
				$srch_arr = explode(' ', $search_str);

				foreach ($srch_arr as $srch_val){
					if($srch_val != ''){
						$result_array1 = $this->M_fooddesk->get_searched_fdd_products($srch_val,$this->lang_u);
						if(!empty($result_array)){
							$result_array = array_values(array_uintersect($result_array, $result_array1, function($val1, $val2){ return strcmp($val1['p_id'], $val2['p_id']); }));
						}else{
							$result_array = $result_array1;
						}
					}
				}
			}else{
				$srch_arr = explode(' ', $search_str);
					if(!empty($srch_arr)){
						$result_array1 = $this->M_fooddesk->get_searched_fdd_products_custom($srch_arr,$recipe_option,$this->company_id);
						$result_array = $result_array1;
					}

					$availableTags = array();
					for($i=0;$i<count($result_array);$i++){
						$new_label = '';
						$new_label .= '<strong>';
						if($result_array[$i]['p_name'.$sel_lang] != ''){
							$new_label .= stripslashes($result_array[$i]['p_name'.$sel_lang]);
						}else{
							$new_label .= stripslashes($result_array[$i]['p_name_dch']);
						}
						$new_label .= '</strong>';
						$new_label .= '<span>';
						$new_label .= '--'.stripslashes($result_array[$i]['s_name']);

						if($result_array[$i]['barcode'] != null && $result_array[$i]['barcode'] != ''){
							$new_label .= '--EAN: '.$result_array[$i]['barcode'];
						}else {
							$new_label .= '--EAN:-- ';
						}

						if($result_array[$i]['plu'] != null && $result_array[$i]['plu'] != ''){
							$new_label .= '--Article nbr: '.$result_array[$i]['plu'];
						}else{
							$new_label .= '--Article nbr:-- ';
						}

						if($result_array[$i]['rs_name'] != null && $result_array[$i]['rs_name'] != ''){
							$new_label .= '--'._("Supplier").': '.stripslashes($result_array[$i]['rs_name']);
						}else{
							$new_label .= '--'._("Supplier").':-- ';
						}

						if($result_array[$i]['art_nbr_supp'] != null && $result_array[$i]['art_nbr_supp'] != ''){
							$new_label .= '--Article nbr: '.$result_array[$i]['art_nbr_supp'];
						}else{
							$new_label .= '--Article nbr:-- ';
						}

						if($result_array[$i]['product_type'] != null && $result_array[$i]['product_type'] != 0){
							$new_label .= '--GS1 ';
						}

						$new_label .= '</span>';
						$short_arr =  array('value'=> $result_array[$i]['p_id'], 'label'=> $new_label );
						$availableTags[] = $short_arr;
					}
				echo json_encode($availableTags);die;
			}

		}
		else if($type == 'new_prod')
		{
			$search_str = $this->input->post('search_str');
			if(!empty($search_str))
			{
				$result_array1 = $this->M_fooddesk->get_searched_new_products($search_str);
				$result_array = $result_array1;
			}
			$result_array = array_values($result_array);
			echo json_encode($result_array);
		}
	}

	function get_serched_supplier(){
		$search_str = $this->input->post('search_str');
		if(!empty($search_str))
		{
			$result_array1 = $this->M_fooddesk->get_searched_supplier($search_str);
			$result_array = $result_array1;
		}
		$result_array = array_values($result_array);
		echo json_encode($result_array);
	}

	function get_serched_realsupplier(){
		$search_str = $this->input->post('search_str');
		if(!empty($search_str))
		{
			$result_array1 = $this->M_fooddesk->get_searched_realsupplier($search_str);
			$result_array = $result_array1;
		}
		$result_array = array_values($result_array);
		echo json_encode($result_array);
	}

	function get_extra_semi_serched_AjaxProducts(){
		$search_str = addslashes($this->input->post('search_str'));
		$result_array = array();
		$srch_arr = explode(' ', $search_str);
		foreach ($srch_arr as $srch_val){
			if($srch_val != ''){
				$result_array1 = $this->M_fooddesk->get_searched_extra_semi_products_custom($srch_val);
			}

		}
		$result_array = array_values($result_array1);
		echo json_encode($result_array);
	}

	function get_reciepe_weight(){
		
		$obs_pro_id=$this->input->post('pro_id');
		$this->db->select('recipe_weight');
		$this->db->where('id', $obs_pro_id);
		$query = $this->db->get('products')->result_array();
		return $query[0]['recipe_weight'];
	}

	function compareDeepValue($val1, $val2){
		return strcmp($val1['value'], $val2['value']);
	}


	 function update_fdd_pro_quant(){
	 	$nutri_values = array();
	 	$update_array = $this->input->post('update_array');

	 	$nutri_values['e_val_1']  		= 0;
	 	$nutri_values['e_val_2']  		= 0;
	 	$nutri_values['protiens'] 		= 0;
	 	$nutri_values['carbo']    		= 0;
	 	$nutri_values['sugar']    		= 0;
	 	$nutri_values['poly'] 	  		= 0;
	 	$nutri_values['farina']   		= 0;
	 	$nutri_values['fats']    		= 0;
	 	$nutri_values['sat_fats'] 		= 0;
	 	$nutri_values['single_fats'] 	= 0;
	 	$nutri_values['multi_fats']  	= 0;
	 	$nutri_values['salt']      		= 0;
	 	$nutri_values['fibers']    		= 0;

	 	if(!empty($update_array)){
	 		if (!empty($update_array)){
	 			foreach ($update_array as $has_fdd_qu){
	 				$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
	 				if( !empty( $fdd_pro_info ) ){
		 				$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])/100;
		 				$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])/100;
		 				$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])/100;
		 				$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])/100;
		 				$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])/100;
		 				$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])/100;
		 				$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])/100;
		 				$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])/100;
		 				$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])/100;
		 				$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])/100;
		 				$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])/100;
		 				$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])/100;
		 				$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])/100;
		 			}
	 			}
	 		}
	 	}

	 	echo json_encode($nutri_values);
	 }

	 function own_products_suggestions($obs_pro_id = 0){
	 	$data = array();

	 	if($obs_pro_id != 0){
	 		
	 		$total_used_pro = $this->M_fooddesk->used_own_pro_info($obs_pro_id);

	 		foreach ($total_used_pro as $key => $val){

	 			$prefixes = $this->M_fooddesk->get_fdd_pro_prefixes(array('kp_id'=>$val['fdd_pro_id'],'product_id'=>$obs_pro_id,'is_obs_ing'=>0));
	 			if(!empty($prefixes)){
	 				$total_used_pro[$key]['prefix'] = $prefixes[0]['prefix'];
	 			}else{
	 				$total_used_pro[$key]['prefix'] = '';
	 			}
	 		}

	 		$data['total_used_pro']=$total_used_pro;
	 	}

	 	
	 	$this->load->model('Mproducts');
	 	$data['own_products'] = $this->Mproducts->get_own_products();
	 	$this->load->view('cp/own_products_suggestions', $data);
	 }

	 function get_own_ing_traces_allergence_semi($prod_id = 0){
	 	
	 	if($prod_id){
	 		$this->load->model('Mproducts');
	 		$products = $this->Mproducts->get_product_information_own($prod_id);
	 		$ingredients = $this->Mproducts->get_product_ingredients($prod_id);
	 		$traces = $this->Mproducts->get_product_traces($prod_id);
	 		$allergence = $this->Mproducts->get_product_allergence($prod_id);
	 		$products[0]->ingredients = $ingredients;
	 		$products[0]->traces = $traces;
	 		$products[0]->allergence = $allergence;

	 		foreach ($products[0]->ingredients as $k=>$ing){
	 			$prefix = $ing->prefix;  //$this->M_fooddesk->get_prfx(array('product_id'=>$products[0]->p_id, 'ing_id'=>$ing->ing_id));

	 			$products[0]->ingredients[$k]->prefix = $prefix;
	 		}
	 	}
	 	$response = array( 'product' => $products);
	 	//echo json_encode($response);
	 	RETURN $response;
	 }

	 function get_own_ing_traces_allergence($prod_id = 0){
	 	
	 	if($prod_id){
	 		$this->load->model('Mproducts');
	 		$products = $this->Mproducts->get_product_information($prod_id);
	 		$ingredients = $this->Mproducts->get_product_ingredients($prod_id);
	 		$traces = $this->Mproducts->get_product_traces($prod_id);
	 		$allergence = $this->Mproducts->get_product_allergence($prod_id);
	 		$products[0]->ingredients = $ingredients;
	 		$products[0]->traces = $traces;
	 		$products[0]->allergence = $allergence;

	 		foreach ($products[0]->ingredients as $k=>$ing){
	 			$prefix = $ing->prefix;  //$this->M_fooddesk->get_prfx(array('product_id'=>$products[0]->p_id, 'ing_id'=>$ing->ing_id));

	 			$products[0]->ingredients[$k]->prefix = $prefix;
	 		}
	 	}
	 	$response = array( 'product' => $products);
	 	echo json_encode($response);
	 }

	 function log(){
	 	$this->load->model('M_fooddesk');
	 	$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();
	 	$data['products'] = $this->M_fooddesk->get_recent_approved();
	 	$language_id = $this->Mgeneral_settings->get_general_settings(array(),'language_id');
	 	if(!empty($language_id))
	 	{
	 		$data['language']=$language_id[0]->language_id;
	 	}
	 	
	 	if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
	 		$this->load->model("Morders");
	 		$data['empty_product_count'] = get_pending_product_count($this->company->id);
	 		$data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
	 	}

	 	$data['content'] = 'cp/fdd_log';
	 	$this->load->view('cp/cp_view', $data);
	 }

	 function copy_ing_name(){
	 	$ings = $this->fdb->get("suppliers")->result();
	 	echo "<br/><pre>";
	 	print_r($ings);


	 	$product_ing = $this->db->get("products_ingredients")->result();
	 	echo "sdad".count($product_ing);

	 }

	 function fdd_own_products($obs_pro_id = 0 ,$page_id = 0){
	 	$data = array();
	 	if ($page_id){
	 		$data['page_id']=$page_id;
	 	}
	 	if($obs_pro_id != 0){
	 		
	 		$total_used_pro = $this->M_fooddesk->get_quant_details($obs_pro_id);

	 		foreach ($total_used_pro as $key => $val){
	 			

	 			$prefixes = $this->M_fooddesk->get_fdd_pro_prefixes(array('kp_id'=>$val['fdd_pro_id'],'product_id'=>$obs_pro_id,'is_obs_ing'=>0));
	 			if(!empty($prefixes)){
	 				$total_used_pro[$key]['prefix'] = $prefixes[0]['prefix'];
	 			}else{
	 				$total_used_pro[$key]['prefix'] = '';
	 			}

	 			

	 			$names = $this->M_fooddesk->get_fdd_pro_details($val['fdd_pro_id']);
	 			$pro_display_name = '';

	 			if($names[0]['p_name'.$this->lang_u] != ''){
	 				$pro_display_name .= $names[0]['p_name'.$this->lang_u].'--';
	 			}

	 			$pro_display_name .= $names[0]['s_name'].'--EAN: ';

	 			if($names[0]['barcode'] != NULL){
	 				$pro_display_name .= $names[0]['barcode'].'--PLU: ';
	 			}else {
	 				$pro_display_name .= 'No EAN Found!--PLU: ';
	 			}

	 			if($names[0]['plu'] != NULL){
	 				$pro_display_name .= $names[0]['plu'];
	 			}else {
	 				$pro_display_name .= 'No PLU Found!';
	 			}

	 			if($names[0]['product_type'] != "0"){
	 				$pro_display_name .= '-GS1';
	 			}

	 			$total_used_pro[$key]['pro_display_name'] = $pro_display_name;
	 		}

	 		$data['total_used_pro']=$total_used_pro;
	 		
	 		$total_used_pro_own = $this->M_fooddesk->used_own_pro_info($obs_pro_id);

	 		foreach ($total_used_pro_own as $key => $val){
	 			$prefixes = $this->M_fooddesk->get_fdd_pro_prefixes(array('kp_id'=>$val['fdd_pro_id'],'product_id'=>$obs_pro_id,'is_obs_ing'=>0));
	 			if(!empty($prefixes)){
	 				$total_used_pro_own[$key]['prefix'] = $prefixes[0]['prefix'];
	 			}else{
	 				$total_used_pro_own[$key]['prefix'] = '';
	 			}
	 		}
	 		$data['total_used_pro_own']=$total_used_pro_own;
	 	}

	 	$common_array1 = array();
	 	$producers = $this->M_fooddesk->get_supplier_name();
	 	foreach ($producers as $producer){
	 		array_push($common_array1, $producer['s_name']);
	 	}
	 	$data['producers'] = $common_array1;

	 	$common_array2 = array();
	 	$suppliers = $this->M_fooddesk->get_real_supplier_name();
	 	foreach ($suppliers as $supplier){
	 		array_push($common_array2, $supplier['rs_name']);
	 	}
	 	$data['suppliers'] = $common_array2;

	 	
	 	$data[ 'fdd_pro_fav_data' ] = $this->M_fooddesk->get_fav_pro_data( $this->company_id );
	 	$data['weight_list'] = $this->db->get('weights_and_measures')->result_array();
	 	$data['fdd_lang']=$this->lang_u;
	 	$this->load->model('Mproducts');
	 	$data['own_products'] = $this->Mproducts->get_own_products_semi();
	 	$data['semi_products'] = $this->db->where(array('semi_product'=>2,'company_id'=>$this->company_id))->get('products')->result_array();
	 	$this->load->view('cp/fdd_own_products', $data);
	 }

	 function update_supplier_name(){
	 	
	 	$pro_id = $this->input->post('pro_id');
	 	$this->db->select('id,fdd_prod_art_num,fdd_supp_art_num,fdd_producer_id,fdd_supplier_id,ean_code');
	 	$res = $this->db->get_where('products',array('id'=>$pro_id))->result_array();
	 	$producer_name = '';

	 	
	 	if(!empty($res)){
	 		$this->fdb->select('s_name');
		 	$res2 = $this->fdb->get_where('suppliers',array('s_id'=>$res[0]['fdd_producer_id'],'is_controller_cp'=>0))->result_array();
		 	$res3 = $this->fdb->get_where('real_suppliers',array('rs_id'=>$res[0]['fdd_supplier_id']))->result_array();

		 	if(!empty($res2)){
		 		$producer_name = $res2[0]['s_name'];
		 	}
		 	$producer_name .= '/';
		 	if(!empty($res3)){
		 		$producer_name .= $res3[0]['rs_name'];
		 	}
		 	else{
		 		$producer_name .= '';
		 	}
		 	$producer_name .= '/';
		 	if($res[0]['fdd_prod_art_num'] != ''){
		 		$producer_name .= $res[0]['fdd_prod_art_num'];
		 	}
		 	else{
		 		$producer_name .= '';
		 	}
		 	$producer_name .= '/';
		 	if($res[0]['fdd_supp_art_num'] != ''){
		 		$producer_name .= $res[0]['fdd_supp_art_num'];
		 	}
		 	else{
		 		$producer_name .= '';
		 	}
		 	$producer_name .= '/';
		 	if( $res[0]['ean_code'] != '' ){
		 		$producer_name .= $res[0]['ean_code'];
		 	}else{
		 		$producer_name .= '';
		 	}
	 	}
	 	echo $producer_name;
	 }

	 function get_art_num_suggestion(){
	 	$art_nbr_p = array();
	 	$art_nbr_s = array();
	 	if($this->input->post('art_type') == 'producer'){
		 	$art_no_p = trim($this->input->post('art_no_p'));
		 	$art_no_p = (($art_no_p != _('Article Number Producer')) && ($art_no_p != '') && ($art_no_p != '000'))?$art_no_p:'';

		 	if($art_no_p != ''){
		 		
		 		$this->fdb->select('products.p_id,products.p_name_dch,products.barcode,products.plu,suppliers.s_name');
		 		$this->fdb->join('suppliers','suppliers.s_id = products.p_s_id');
		 		$art_nbr_p = $this->fdb->get_where('products',array('products.plu'=>$art_no_p))->result_array();
		 	}
	 	}
		elseif($this->input->post('art_type') == 'supplier'){
			$art_no_s = trim($this->input->post('art_no_s'));
		 	$art_no_s = (($art_no_s != _('Article Number Supplier')) && ($art_no_s != '') && ($art_no_s != '000'))?$art_no_s:'';

		 	if($art_no_s != ''){
		 		
		 		$this->fdb->select('products.p_id,products.p_name_dch,products.barcode,products_suppliers.art_nbr_supp as plu,real_suppliers.rs_name');
		 		$this->fdb->join('products','products.p_id = products_suppliers.product_id');
		 		$this->fdb->join('real_suppliers','real_suppliers.rs_id = products_suppliers.supplier_id');
				$art_nbr_s = $this->fdb->get_where('products_suppliers',array('products_suppliers.art_nbr_supp'=>$art_no_s))->result_array();
				if(!empty($art_nbr_s)){
					$this->fdb->select('suppliers.s_name');
		 			$this->fdb->join('suppliers','suppliers.s_id = products.p_s_id');
		 			$producer_arr = $this->fdb->get_where('products',array('products.p_id'=>$art_nbr_s[0]['p_id']))->row_array();
		 			$art_nbr_s[0]['s_name'] = $producer_arr['s_name'];
				}
		 	}
		}
	 	if(!empty($art_nbr_p)){
	 		echo json_encode($art_nbr_p[0]);
	 	}
	 	elseif(!empty($art_nbr_s)){
	 		echo json_encode($art_nbr_s[0]);
	 	}
	 	else{
	 		echo json_encode(array());
	 	}
	 }

	 function add_new_fixed_product(){
	 	$pro_name 			= trim(addslashes(strtolower($this->input->post('pro_name'))));
	 	$supplier_name 		= trim($this->input->post('supp_name'));
	 	$real_supplier_name = trim($this->input->post('real_supp_name'));
	 	$art_no_p 			= trim($this->input->post('art_no_p'));
	 	$ean_code 			= trim($this->input->post('ean_code'));
	 	$art_no_p 			= (($art_no_p != _('Article Number Producer')) && ($art_no_p != '') && ($art_no_p != '000'))?$art_no_p:'';

	 	$art_no_s 			= trim($this->input->post('art_no_s'));
	 	$art_no_s 			= (($art_no_s != _('Article Number Supplier')) && ($art_no_s != '') && ($art_no_s != '000'))?$art_no_s:'';

	 	$supplier_id = 0;
	 	$supplier_name = trim($supplier_name);
	 	if(($supplier_name != '') && ($supplier_name != _('Producer'))){
		 	$res = $this->M_fooddesk->get_suppliers_data(array('LOWER(s_name)' => addslashes(mb_strtolower($supplier_name,'UTF-8')),'is_controller_cp' => 0));
		 	if(!empty($res)){
		 		$supplier_id = $res[0]['s_id'];
		 	}else{
		 		$insrt_array = array(
		 				's_name'=>addslashes($supplier_name),
		 				's_username' => str_replace(' ', '_', $supplier_name),
		 				's_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
		 				's_date_added' => date('Y-m-d h:i:s')
		 		);
		 		$this->fdb->insert('suppliers', $insrt_array);
		 		$supplier_id = $this->fdb->insert_id();
		 	}
	 	}

	 	$real_supplier_id = 0;
	 	$real_supplier_name = trim($real_supplier_name);
	 	if(($real_supplier_name != '') && ($real_supplier_name != _('Supplier'))){
	 		$res1 = $this->M_fooddesk->get_real_suppliers_data(array('LOWER(rs_name)' => addslashes(mb_strtolower($real_supplier_name,'UTF-8'))));
	 		if(!empty($res1)){
	 			$real_supplier_id = $res1[0]['rs_id'];
	 		}else{
	 			$insrt_array = array(
	 					'rs_name'=>addslashes($real_supplier_name),
	 					'rs_username' => str_replace(' ', '_', $real_supplier_name),
	 					'rs_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
	 					'rs_date_added' => date('Y-m-d h:i:s')
	 			);
	 			$this->fdb->insert('real_suppliers', $insrt_array);
	 			$real_supplier_id = $this->fdb->insert_id();
	 		}
	 	}

	 	

	 	$this->db->select('id');
	 	if($art_no_p != ''){
	 		$this->db->where('fdd_prod_art_num',$art_no_p);
	 	}
	 	if($art_no_s != ''){
	 		$this->db->where('fdd_supp_art_num',$art_no_s);
	 	}
	 	$this->db->where(array('company_id'=>0,'LOWER(proname)'=>$pro_name,'fdd_producer_id'=>$supplier_id,'fdd_supplier_id'=>$real_supplier_id));
	 	$products = $this->db->get('products')->result_array();

	 	if(empty($products)){
		 	$insert_array = array(
		 		'company_id' 		=> 0,
		 		'categories_id'		=> 0,
		 		'subcategories_id'	=> 0,
		 		'proname'			=> $pro_name,
		 		'procreated'		=> date('Y-m-d'),
		 		'direct_kcp'		=> 1,
		 		'status'			=> '0',
		 		'fdd_producer_id'	=> $supplier_id,
		 		'fdd_supplier_id'	=> $real_supplier_id,
		 		'fdd_prod_art_num'	=> $art_no_p,
		 		'fdd_supp_art_num'	=> $art_no_s,
		 		'ean_code' 			=> $ean_code,
		 	);
		 	$this->db->insert('products',$insert_array);
		 	$insert_pro_id = $this->db->insert_id();

		 	$this->db->insert('contacted_via_mail',array('obs_pro_id'=>$insert_pro_id));
		 	echo $insert_pro_id.'_new';
	 	}
	 	else{
	 		echo $products[0]['id'].'_exist';
	 	}
	 }

	 function delete_new_fixed_product(){
	 	$pro_id = $this->input->post('pro_id');
	 	
	 	$this->db->where('id',$pro_id);
	 	$this->db->delete('products');
	 	$this->db->delete('products_labeler',array('product_id'=>$pro_id));
	 	$this->db->delete('products_pending',array('product_id'=>$pro_id));
	 	$this->db->delete('contacted_via_mail',array('obs_pro_id'=>$pro_id));
	 }

	 function fdd_own_products_all($obs_pro_id = 0){
	 	$data = array();
	 	$id_for_semi = $obs_pro_id;
	 	$obs_pro_id = 0; // for temporary use

	 	if($obs_pro_id != 0){
	 		
	 		$total_used_pro = $this->M_fooddesk->get_quant_details($obs_pro_id);

	 		foreach ($total_used_pro as $key => $val){
	 			

	 			$prefixes = $this->M_fooddesk->get_fdd_pro_prefixes(array('kp_id'=>$val['fdd_pro_id'],'product_id'=>$obs_pro_id,'is_obs_ing'=>0));
	 			if(!empty($prefixes)){
	 				$total_used_pro[$key]['prefix'] = $prefixes[0]['prefix'];
	 			}else{
	 				$total_used_pro[$key]['prefix'] = '';
	 			}

	 			

	 			$names = $this->M_fooddesk->get_fdd_pro_details($val['fdd_pro_id']);
	 			$pro_display_name = '';

	 			if($names[0]['p_name'.$this->lang_u] != ''){
	 				$pro_display_name .= $names[0]['p_name'.$this->lang_u].'--';
	 			}

	 			$pro_display_name .= $names[0]['s_name'].'--EAN: ';

	 			if($names[0]['barcode'] != NULL){
	 				$pro_display_name .= $names[0]['barcode'].'--PLU: ';
	 			}else {
	 				$pro_display_name .= 'No EAN Found!--PLU: ';
	 			}

	 			if($names[0]['plu'] != NULL){
	 				$pro_display_name .= $names[0]['plu'];
	 			}else {
	 				$pro_display_name .= 'No PLU Found!';
	 			}

	 			$total_used_pro[$key]['pro_display_name'] = $pro_display_name;

	 		}

	 		$data['total_used_pro']=$total_used_pro;

	 		
	 		$total_used_pro_own = $this->M_fooddesk->used_own_pro_info($obs_pro_id);


	 		foreach ($total_used_pro_own as $key => $val){
	 			$prefixes = $this->M_fooddesk->get_fdd_pro_prefixes(array('kp_id'=>$val['fdd_pro_id'],'product_id'=>$obs_pro_id,'is_obs_ing'=>0));
	 			if(!empty($prefixes)){
	 				$total_used_pro_own[$key]['prefix'] = $prefixes[0]['prefix'];
	 			}else{
	 				$total_used_pro_own[$key]['prefix'] = '';
	 			}
	 		}

	 		$data['total_used_pro_own']=$total_used_pro_own;

	 	}

	 	
	 	$data[ 'fdd_pro_fav_data' ] = $this->M_fooddesk->get_fav_pro_data( $this->company_id );
	 	$data['weight_list'] = $this->db->get('weights_and_measures')->result_array();
	 	$data['fdd_lang']=$this->lang_u;
	 	$data['semi_products'] = $this->db->where(array('semi_product'=>1,'company_id'=>$this->company_id,'id !='=>$id_for_semi))->get('products')->result_array();
	 	$this->load->model('Mproducts');
	 	$this->load->view('cp/fdd_own_products_all', $data);
	 }

	 function semi_products_assets(){
	 	
	 	$all_data = $this->input->post("data");
	 	$new_array = array();
	 	foreach ($all_data as $data){
	 		$this->db->where('obs_pro_id', $data['fdd_pro_id']);
	 		$res = $this->db->get('fdd_pro_quantity')->result_array();
	 		array_push($new_array, $res);
	 	}
	 	echo json_encode($new_array);
	 }

	 function number2db($value){
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

	 private function check_if_completed($pro_id = 0){
	 	$complete = 1;

	 	if($pro_id != 0){
		 	if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
		 		$this->db->select('direct_kcp,semi_product');
		 		$pro_arr = $this->db->get_where('products',array('id'=>$pro_id))->row_array();
			 	if(!empty($pro_arr)){
			 		if(($pro_arr['direct_kcp'] == 1) && ($pro_arr['semi_product'] == 0)){
			 			$this->db->where(array('obs_pro_id'=>$pro_id,'is_obs_product'=>0));
			 			$result = $this->db->get('fdd_pro_quantity')->result_array();
			 			if(empty($result)){
			 				$complete = 0;
			 			}
			 		}
			 		else{
			 			$this->db->where(array('obs_pro_id'=>$pro_id));
			 			$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
			 			if(!empty($result_custom)){
			 				
			 				foreach ($result_custom as $val){
			 					if($val['is_obs_product'] == 1){
			 						$complete = 0;
			 						break;
			 					}else {
									$this->fdb->where( 'p_id', $val[ 'fdd_pro_id' ] );
									$this->fdb->where( 'approval_status', 1 );
									$count = $this->fdb->count_all_results( 'products' );
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
		 	}
	 	}
	 	return $complete;
	 }

	 function technical_sheet($pro_id){
	 	

	 	$this->load->model('Mproducts');

	 	$select = array(
	 				'direct_kcp',
					'parent_proid',
					'recipe_weight',
					'proname',
					'image',
					'prodescription' 
		);

	 	$data['product_id']=$pro_id;
	 	$data['fixed'] = $this->Mproducts->fixed($pro_id);
	 	$data['approval_stat'] = $this->Mproducts->check_approval_status($pro_id);
	 	$data['product_information']=$this->Mproducts->get_product_information( $pro_id, $select );
	 	$data['product_ingredients']=$this->Mproducts->get_product_ingredients_dist($pro_id,$this->company->k_assoc);

	 	$data['product_traces']=$this->Mproducts->get_product_traces($pro_id,$this->company->k_assoc);
	 	$data['product_allergences']=$this->Mproducts->get_product_allergence_dist($pro_id,$this->company->k_assoc);
	 	$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence_dist($pro_id,$this->company->k_assoc);

	 	if($data['product_information'][0]->direct_kcp == 1 && $data['product_information'][0]->parent_proid != 0){
	 		$this->db->select('company.id,company_name,address,zipcode,city,phone');
	 		$this->db->join('products','products.company_id = company.id');
	 		$data['comp_det'] =  $this->db->get_where('company',array('products.id'=>$data['product_information'][0]->parent_proid))->result();

	 		$data['sheet_banner'] = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$data['comp_det'][0]->id),array('sheet_banner','comp_default_image'));
	 	}
	 	else{
	 		$data['sheet_banner'] = $this->Mgeneral_settings->get_general_settings(0,array('sheet_banner','comp_default_image'));
	 	}

	 	$data['contains']= $this->M_fooddesk->used_own_pro_info($pro_id);

	 	$data['marked'] = $this->M_fooddesk->is_marked($pro_id);
	 	
	 	$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($pro_id);

	 	$recipe_wt = $data['product_information'][0]->recipe_weight;
	 	if($recipe_wt != 0){
	 		$recipe_wt = $recipe_wt*1000;
	 	}else{
	 		$recipe_wt = 100;
	 	}

	 	$nutri_values = array();
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

	 		$last_modified_date = array();

	 		$select_qty = array(
								'e_val_1',
								'e_val_2',
								'proteins',
								'carbohydrates',
								'sugar',
								'polyolen',
								'farina',
								'fats',
								'saturated_fats',
								'single_unsaturated_fats',
								'multi_unsaturated_fats',
								'salt',
								'fibers',
								'pdf_date'
			);

	 		foreach ($has_fdd_quant as $has_fdd_qu){
	 			
	 			$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details( $has_fdd_qu['fdd_pro_id'], $select_qty );
	 			
	 			if( !empty( $fdd_pro_info ) ){
		 			$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
		 			$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
		 			$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
		 			$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
		 			$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
		 			$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
		 			$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
		 			$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
		 			$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
		 			$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
		 			$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
		 			$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
		 			$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);

		 			if( explode( "-", $fdd_pro_info[0][ 'pdf_date' ] )[0] != '0000' ){
			 			array_push( $last_modified_date, strtotime( $fdd_pro_info[0][ 'pdf_date' ] ) );
		 			}
		 		}
	 		}
	 		rsort( $last_modified_date );
	 		$data['nutri_values'] 			= $nutri_values;
	 		if( !empty( $last_modified_date ) ) {
				$data[ 'last_modified_date' ] = date( 'd/m/Y', $last_modified_date[0] );
			}
	 	}
	 	$results = $this->db->get('allergens_words')->result_array();
	 	$all_words = array();

	 	$all_words = array_column( $results, 'allergens_word' );
	 	
	 	$data['all_words'] = $all_words;

	 	$data['is_fixed'] = $this->check_if_completed($pro_id);

	 	
	 	$this->fdb->select( array( 'all_id', 'all_name'.$this->lang_u ) );
	 	$aller_arr = $this->fdb->get('allergence')->result_array();

	 	$all_id_arr = array();
	 	if(!empty($data['product_allergences'])){
	 		foreach ($data['product_allergences'] as $aller){
	 			$all_id_arr[] = $aller->ka_id;
	 		}
	 	}
	 	$data['allergence_words'] = array();
	 	foreach ($aller_arr as $val){
	 		if(in_array($val['all_id'],$all_id_arr))
	 			$data['allergence_words'][strtolower($val['all_name'.$this->lang_u])] = $val['all_id'];
	 	}
	 
	 	$data['temperature'] = $this->M_fooddesk->get_prod_temp($pro_id);

	 	if(empty($data['fixed']))
	 	{
	 		$product_allergences=$this->Mproducts->get_product_allergence_dist($pro_id,$this->company->k_assoc);

	 		if (!empty($product_allergences))
	 		{
	 			foreach ($product_allergences as $allergences_key=>$allergences_val)
	 			{
	 				$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
	 				$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
	 			}
	 		}
	 		$product_sub_allergences=$this->Mproducts->get_product_sub_allergence_dist($pro_id,$this->company->k_assoc);

	 		if (!empty($product_sub_allergences))
	 		{
	 			foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
	 			{
	 				$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
	 				$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
	 			}
	 		}

	 		$all_aller = $this->Mproducts->get_ing_allergen($pro_id,$this->lang_u );
	 		foreach ($all_aller as $all_aller_k => $all_aller_v ) {
				$allergence_lang_d = 'allergence';
				$sub_allergence_d = 'sub_allergence';
	 			if ($this->lang_u == '_dch') {
	 				$allergence_lang_d = 'allergence_dch';
	 				$sub_allergence_d = 'sub_allergence_dch';

	 			}else if ($this->lang_u == '_fr') {
	 				$allergence_lang_d = 'allergence_fr';
	 				$sub_allergence_d = 'sub_allergence_fr';
	 			}

	 			if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
	 				$all_allergence[] = $all_aller_v->$allergence_lang_d;
	 			}
	 			if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
	 				$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
	 			}
	 		}
	 		$all_allergence = array_unique($all_allergence);

	 		$final_all = array();
	 		foreach ($all_allergence as $key1 => $value1 ) {
	 			$ing_aller = explode('-', $value1);
	 			$final_all = array_merge($ing_aller, $final_all);
	 		}
	 		$final_all = array_unique($final_all);
	 		$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);


			if (empty($final_aller)) {
				$data['product_allergences'] = array_values(array_unique($product_allergence, SORT_REGULAR));
			}
			elseif (empty($product_allergence)) 
			{
		 		$data['product_allergences'] = array_values(array_unique($final_aller, SORT_REGULAR));
			}
	 		else{
				$data['product_allergences'] = array_merge($product_allergence,$final_aller);
		 		$data['product_allergences'] = array_values(array_unique($data['product_allergences'], SORT_REGULAR));
	 		}

	 		$final_sub_all = array();
	 		foreach ($all_sub_allergence as $key1 => $value1 ) {
	 			$ing_sub_aller = explode('-', $value1);
	 			$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
	 		}
	 		$final_sub_all = array_unique($final_sub_all);
	 		$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);



			if (empty($final_sub_aller)) {
				$data['product_sub_allergences'] = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
			}
			elseif (empty($product_sub_allergence)) 
			{
		 		$data['product_sub_allergences'] = array_values(array_unique($final_sub_aller, SORT_REGULAR));
			}
	 		else{
				$data['product_sub_allergences'] = array_merge($product_sub_allergence,$final_sub_aller);
		 		$data['product_sub_allergences'] = array_values(array_unique($data['product_sub_allergences'], SORT_REGULAR));
	 		}
	
	 	}
	 	$data['extra_notification_free_field'] = $this->Mproducts->extra_notification_free_field($pro_id);


	 	$pdf_html = $this->load->view('cp/technical_sheet_view', $data, true);
	 	require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');
	 	$report_name = 'report'.time().'.pdf';
	 	$mpdf=new mPDF('c');
	 	$mpdf->WriteHTML($pdf_html);
	 	$mpdf->Output('Technische-fiche-'.$data['product_information'][0]->proname.'.pdf', 'D');

	 }

	 function recipe_sheet($pro_id){
	 	
	 	$data['sheet_banner'] = $this->Mgeneral_settings->get_general_settings(0,array('sheet_banner','comp_default_image'));

	 	$this->load->model('Mproducts');
	 	$data['product_id']=$pro_id;
	 	$data['fixed'] = $this->Mproducts->fixed($pro_id);
	 	$data['product_information']= $this->Mproducts->get_product_information($pro_id);
	 	$data['product_default_image']=$this->Mproducts->get_default_image( );
	 	$data['product_ingredients']= $this->Mproducts->get_product_ingredients_dist($pro_id,$this->company->k_assoc);
	 	$data['product_traces']=$this->Mproducts->get_product_traces($pro_id,$this->company->k_assoc);
	 	$data['product_allergences']=$this->Mproducts->get_product_allergence_dist($pro_id,$this->company->k_assoc);
	 	$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence_dist($pro_id,$this->company->k_assoc);

	 	$this->load->model('M_fooddesk');
	 	$data['marked'] = $this->M_fooddesk->is_marked($pro_id);
	 	$data['producers']=$this->M_fooddesk->get_supplier_name();
	 	$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
	 	
	 	$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($pro_id);

	 	$recipe_wt = $data['product_information'][0]->recipe_weight;
	 	if($recipe_wt != 0){
	 		$recipe_wt = $recipe_wt*1000;
	 	}else{
	 		$recipe_wt = 100;
	 	}

	 	$nutri_values = array();
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

	 		$last_modified_date = array();

	 		foreach ($has_fdd_quant as $has_fdd_qu){
	 			
	 			$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
	 			
	 			if( !empty( $fdd_pro_info ) ){
		 			$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
		 			$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
		 			$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
		 			$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
		 			$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
		 			$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
		 			$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
		 			$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
		 			$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
		 			$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
		 			$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
		 			$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
		 			$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);

					if( explode( "-", $fdd_pro_info[0][ 'pdf_date' ] )[0] != '0000' ){
			 			array_push( $last_modified_date, strtotime( $fdd_pro_info[0][ 'pdf_date' ] ) );
		 			}
		 		}
	 		}
	 		rsort( $last_modified_date );
	 		$data['nutri_values'] = $nutri_values;
	 		if( !empty( $last_modified_date ) ) {
				$data[ 'last_modified_date' ] = date( 'd/m/Y', $last_modified_date[0] );
			}
	 	}

	 	$data['is_fixed'] = $this->check_if_completed($pro_id);
	 	$data['semi_contains']= $this->M_fooddesk->semi_contains($pro_id);
	 	$data['fdd_contains']= $this->M_fooddesk->fdd_contains($pro_id);
	 	$data['own_contains']= $this->M_fooddesk->own_contains($pro_id);

	 	$semi_pro_arr = array();
	 	
	 	foreach ($data['semi_contains'] as $key=>$semi_p){
	 		if($semi_p['is_obs_product'] == 0){
	 			$this->fdb->where('p_id',$semi_p['fdd_pro_id']);
	 			$this->fdb->select( array( 's_name', 'p_name'.$this->lang_u ) );
	 			$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
	 			$res = $this->fdb->get('products')->result_array();
	 			if(!empty($res)){

	 				$data['semi_contains'][$key]['fabrikant'] = $res[0]['s_name'];
	 				$data['semi_contains'][$key]['p_name'] = $res[0]['p_name'.$this->lang_u];
	 			}
	 		}else{
	 			
	 			$this->db->select('proname');
	 			$this->db->where('id',$semi_p['fdd_pro_id']);
	 			$fix_pro_names = $this->db->get('products')->result_array();
	 			$fix_pro_name = $fix_pro_names[0]['proname'];
	 			

	 			$data['semi_contains'][$key]['fabrikant'] = 'eigen';
	 			$data['semi_contains'][$key]['p_name'] = $fix_pro_name;
	 		}

	 		if(!in_array($semi_p['semi_product_id'], $semi_pro_arr)){
	 			array_push($semi_pro_arr, $semi_p['semi_product_id']);
	 		}
	 	}

	 	$semi_pro_array = array();
	 	
	 	foreach ($semi_pro_arr as $semi_id){
	 		$this->db->select('proname');
	 		$this->db->where('id',$semi_id);
	 		$semi_pro_names = $this->db->get('products')->result_array();

	 		$this->db->select_sum('quantity');
	 		$this->db->where(array('obs_pro_id'=>$pro_id,'semi_product_id'=>$semi_id));
	 		$sums = $this->db->get('fdd_pro_quantity')->result_array();

	 		$total = (!empty($sums))?$sums[0]['quantity']:0;

	 		$new_arr = array(
	 			'semi_id'=>$semi_id,
	 			'semi_name'=>$semi_pro_names[0]['proname'],
	 			'quant' => $total
	 		);

	 		array_push($semi_pro_array, $new_arr);
	 	}
	 	

	 	$data['semi_pro_array'] = $semi_pro_array;

	 	foreach ($data['fdd_contains'] as $key=>$semi_p){
	 		$this->fdb->where('p_id',$semi_p['fdd_pro_id']);
	 		$this->fdb->select( array( 's_name', 'p_name'.$this->lang_u ) );
	 		$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
	 		$res = $this->fdb->get('products')->result_array();
	 		if(!empty($res)){
	 			$data['fdd_contains'][$key]['fabrikant'] = $res[0]['s_name'];
	 			$data['fdd_contains'][$key]['p_name'] = $res[0]['p_name'.$this->lang_u];
	 		}
	 	}

	 	foreach ($data['own_contains'] as $key=>$semi_p){
	 		
	 		$this->db->select('proname');
	 		$this->db->where('id',$semi_p['fdd_pro_id']);
	 		$fix_pro_names = $this->db->get('products')->result_array();
	 		$fix_pro_name = $fix_pro_names[0]['proname'];

	 		

	 		$data['own_contains'][$key]['fabrikant'] = 'eigen';
	 		$data['own_contains'][$key]['p_name'] = $fix_pro_name;
	 	}

	 	
	 	$this->fdb->select( array( 'all_id', 'all_name'.$this->lang_u ) );
	 	$aller_arr = $this->fdb->get('allergence')->result_array();

	 	$all_id_arr = array();
	 	if(!empty($data['product_allergences'])){
	 		foreach ($data['product_allergences'] as $aller){
	 			$all_id_arr[] = $aller->ka_id;
	 		}
	 	}
	 	$data['allergence_words'] = array();
	 	foreach ($aller_arr as $val){
	 		if(in_array($val['all_id'],$all_id_arr))
	 			$data['allergence_words'][strtolower($val['all_name'.$this->lang_u])] = $val['all_id'];
	 	}

		if(empty($data['fixed']))
		{
			$product_allergences=$this->Mproducts->get_product_allergence_dist($pro_id,$this->company->k_assoc);
			if (!empty($product_allergences))
			{
				foreach ($product_allergences as $allergences_key=>$allergences_val)
				{
					$product_allergence[$allergences_key]->ka_id = $allergences_val->ka_id;
					$product_allergence[$allergences_key]->ka_name = $allergences_val->ka_name;
				}
			}
			$product_sub_allergences=$this->Mproducts->get_product_sub_allergence_dist($pro_id,$this->company->k_assoc);
			if (!empty($product_sub_allergences))
			{
				foreach ($product_sub_allergences as $allergences_key=>$allergences_val)
				{
					$product_sub_allergence[$allergences_key]->parent_ka_id = $allergences_val->parent_ka_id;
					$product_sub_allergence[$allergences_key]->sub_ka_name = $allergences_val->sub_ka_name;
				}
			}

			$all_aller = $this->Mproducts->get_ing_allergen($pro_id,$this->lang_u );
	 		foreach ($all_aller as $all_aller_k => $all_aller_v ) {
				$allergence_lang_d = 'allergence';
				$sub_allergence_d = 'sub_allergence';
	 			if ($this->lang_u == '_dch') {
	 				$allergence_lang_d = 'allergence_dch';
	 				$sub_allergence_d = 'sub_allergence_dch';

	 			}else if ($this->lang_u == '_fr') {
	 				$allergence_lang_d = 'allergence_fr';
	 				$sub_allergence_d = 'sub_allergence_fr';
	 			}

	 			if($all_aller_v->$allergence_lang_d != '' && $all_aller_v->$allergence_lang_d != '0'){
	 				$all_allergence[] = $all_aller_v->$allergence_lang_d;
	 			}
	 			if($all_aller_v->$sub_allergence_d != '' && $all_aller_v->$sub_allergence_d != '0'){
	 				$all_sub_allergence[] = $all_aller_v->$sub_allergence_d;
	 			}
	 		}
			$all_allergence = array_unique($all_allergence);

			$final_all = array();
			foreach ($all_allergence as $key1 => $value1 ) {
				$ing_aller = explode('-', $value1);
				$final_all = array_merge($ing_aller, $final_all);
			}
			$final_all = array_unique($final_all);
			$final_aller = $this->Mproducts->get_allergen($final_all,$this->lang_u);
			if (empty($final_aller)) {
				$data['product_allergences'] = array_values(array_unique($product_allergence, SORT_REGULAR));
			}
			elseif (empty($product_allergence)) 
			{
		 		$data['product_allergences'] = array_values(array_unique($final_aller, SORT_REGULAR));
			}
	 		else{
				$data['product_allergences'] = array_merge($product_allergence,$final_aller);
		 		$data['product_allergences'] = array_values(array_unique($data['product_allergences'], SORT_REGULAR));
	 		}

			$final_sub_all = array();
			foreach ($all_sub_allergence as $key1 => $value1 ) {
				$ing_sub_aller = explode('-', $value1);
				$final_sub_all = array_merge($ing_sub_aller, $final_sub_all);
			}
			$final_sub_all = array_unique($final_sub_all);
			$final_sub_aller = $this->Mproducts->get_sub_allergen($final_sub_all,$this->lang_u);
			if (empty($final_sub_aller)) {
				$data['product_sub_allergences'] = array_values(array_unique($product_sub_allergence, SORT_REGULAR));
			}
			elseif (empty($product_sub_allergence)) 
			{
		 		$data['product_sub_allergences'] = array_values(array_unique($final_sub_aller, SORT_REGULAR));
			}
	 		else{
				$data['product_sub_allergences'] = array_merge($product_sub_allergence,$final_sub_aller);
		 		$data['product_sub_allergences'] = array_values(array_unique($data['product_sub_allergences'], SORT_REGULAR));
	 		}
		}
		$data['extra_notification_free_field'] = $this->Mproducts->extra_notification_free_field($pro_id);
	 	$pdf_html = $this->load->view('cp/recipe_sheet_view', $data, true);

	 	require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');
	 	$report_name = 'report'.time().'.pdf';
	 	$mpdf=new mPDF('c');
	 	$mpdf->WriteHTML($pdf_html);
	 	$mpdf->Output('Recipe-'.$data['product_information'][0]->proname.'.pdf', 'D');
	}

	function producer_sheet($pro_id){
		//http://webilyst.com/projects/fooddesk/dwpdf/?pdf=artikelfiche_zeisner_12kg_tomatenketchup_nl.pdf
		if(isset($pro_id)){

			$fdd_url = $this->config->item('fddesk_url');
			$url = $fdd_url.'dwpdf?pid='.$pro_id;

			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);

			$output = curl_exec($ch);
			curl_close($ch);

			print_r($output);die;
		}
		else{
			show_404();
		}
	}

	function update_shortname(){
		$p_id = $this->input->post('p_id');
		$shortname = $this->input->post('shortname');

		$res = $this->fdb->insert('shortname_suggestions', array('p_id'=>$p_id,'shortname'=>addslashes($shortname)));

		if($res){
			echo '1';
		}else{
			echo '0';
		}
	}

	function get_ing_traces_allergence_all(){
		$fdd_data   		= $this->input->post('fdd_prods');
		$own_data   		= $this->input->post('own_prods');
		$status_arr   		= $this->input->post('status_arr');
		$final_status_arr 	= array( );
		if( !empty( $status_arr ) ){
			foreach ( $status_arr as $key => $value ) {
				$final_status_arr[ $value[ 'prod_id' ] ] = $value[ 'is_favourate' ];
			}
		}

		$fdd_pro_infos = array();
		if(!empty($fdd_data)){
			foreach ($fdd_data as $fdd_pro)
			{
				if ($fdd_pro['unit'] == '')
				{
					//
					$obs_product_id=$fdd_pro['fdd_pro_id'];
					$this->db->select('fdd_pro_id');
					$query=$this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$obs_product_id))->result_array();
					foreach ($query as $val)
					{
						$new_small_arr = array();
						$new_small_arr['info'] = $this->get_ing_traces_allergence_semi($val['fdd_pro_id']);
						$new_small_arr['quantity'] = $fdd_pro['quantity'];
						$new_small_arr['hidden_pro_pre'] = $fdd_pro['hidden_pro_pre'];
						if(isset($fdd_pro['hidden_semi_pro_id'])){
							$new_small_arr['hidden_semi_pro_id'] = $fdd_pro['hidden_semi_pro_id'];
						}else{
							$new_small_arr['hidden_semi_pro_id'] = 0;
						}
						$fixed_s = $this->get_fixed_ajax($val['fdd_pro_id']);
						if($fixed_s['product_type'] == 1)
						{
							$new_small_arr['fixed'] = 1;
						}else{
							$new_small_arr['fixed'] = $fixed_s['review&fixed'];
						}
						$fdd_pro_infos[] = $new_small_arr;
					}
				}
				else{
					$new_small_arr = array();
					$new_small_arr['info'] = $this->get_ing_traces_allergence_semi($fdd_pro['fdd_pro_id']);
					$new_small_arr['quantity'] = $fdd_pro['quantity'];
					$new_small_arr['unit'] = $fdd_pro['unit'];
					$new_small_arr['hidden_pro_pre'] = $fdd_pro['hidden_pro_pre'];
					if(isset($fdd_pro['hidden_semi_pro_id'])){
						$new_small_arr['hidden_semi_pro_id'] = $fdd_pro['hidden_semi_pro_id'];
					}else{
						$new_small_arr['hidden_semi_pro_id'] = 0;
					}
					$fixed_s = $this->get_fixed_ajax($fdd_pro['fdd_pro_id']);
					if($fixed_s['product_type'] == 1)
					{
						$new_small_arr['fixed'] = 1;
					}else{
						$new_small_arr['fixed'] = $fixed_s['review&fixed'];
					}
					$fdd_pro_infos[] = $new_small_arr;
				}
			}
		}
		$own_pro_infos = array();
		if(!empty($own_data)){
			foreach ($own_data as $own_pro){
				$new_small_arr1 = array();
				$new_small_arr1['info'] = $this->get_own_ing_traces_allergence_semi($own_pro['fdd_pro_id']);
				$new_small_arr1['quantity'] = $own_pro['quantity'];
				$new_small_arr1['unit'] = $own_pro['unit'];
				$new_small_arr1['hidden_pro_pre'] = $own_pro['hidden_pro_pre'];
				if(isset($own_pro['hidden_semi_pro_id'])){
					$new_small_arr1['hidden_semi_pro_id'] = $own_pro['hidden_semi_pro_id'];
				}else{
					$new_small_arr1['hidden_semi_pro_id'] = 0;
				}
				$new_small_arr1['fixed'] = '1';
				$own_pro_infos[] = $new_small_arr1;
			}
		}
		echo json_encode(array($fdd_pro_infos,$own_pro_infos,$final_status_arr));
	}

	function get_products_of_semi(){
		$quantity = $this->input->post('quantity');
		$semi_pro_id = $this->input->post('semi_pro_id');

		

		$this->db->where(array('obs_pro_id'=>$semi_pro_id,'is_obs_product'=>0));
		$fdd_prods = $this->db->get('fdd_pro_quantity')->result_array();

		$this->db->where(array('obs_pro_id'=>$semi_pro_id,'is_obs_product'=>1));
		$own_prods = $this->db->get('fdd_pro_quantity')->result_array();

		$this->db->select_sum('quantity');
		$this->db->where('obs_pro_id', $semi_pro_id);
		$total = $this->db->get('fdd_pro_quantity')->result_array();

		$semi_total = 100;
		if(!empty($total)){
			$semi_total = $total[0]['quantity'];
		}

		
		foreach ($fdd_prods as $key=>$fdd_prod){

			$this->fdb->select('p_id,p_name,p_name_fr,p_name_dch,s_name,barcode,plu');
			$this->fdb->where('p_id',$fdd_prod['fdd_pro_id']);
			$this->fdb->join('suppliers','suppliers.s_id = products.p_s_id');
			$res = $this->fdb->get('products')->result_array();

			if(!empty($res)){
				$pro_name = '';
				if($res[0]['p_name'.$this->lang_u] != '' && $res[0]['p_name'.$this->lang_u] != NULL){
					$pro_name = $res[0]['p_name'.$this->lang_u];
				}else{
					$pro_name = $res[0]['p_name_dch'];
				}

				$fdd_prods[$key]['disp_name'] = $pro_name.' -- '.$res[0]['s_name'].' -- EAN: '.$res[0]['barcode'].' -- PLU: '.$res[0]['plu'];

			}
			$fdd_prods[$key]['total_quant'] = $this->number2db(round(($fdd_prod['quantity']/$semi_total)*$quantity,2));
			if ($fdd_prods[$key]['total_quant'] == 0)
			{
				$fdd_prods[$key]['total_quant'] = 0.01;
			}
		}
		
		foreach ($own_prods as $key=>$own_prod){
			$this->db->select('id,proname,fdd_producer_id,fdd_supplier_id');
			$this->db->where('id',$own_prod['fdd_pro_id']);
			$res = $this->db->get('products')->result_array();
			if(!empty($res)){
				$own_prods[$key]['disp_name'] = $res[0]['proname'];
				foreach ($res as $r_key=>$val)
				{
					$query=array();
					
					if ($val['fdd_producer_id']!=0 && $val['fdd_supplier_id']!=0)
					{
						$this->fdb->select('s_name');
						$query = $this->fdb->get_where('suppliers', array('s_id' => $val['fdd_producer_id'],'is_controller_cp' => 0))->result();
					}elseif ($val['fdd_producer_id']!=0){
						$this->fdb->select('s_name');
						$query = $this->fdb->get_where('suppliers', array('s_id' => $val['fdd_producer_id'],'is_controller_cp' => 0))->result();
					}elseif ($val['fdd_supplier_id']!=0){
						$this->fdb->select('rs_name as s_name');
						$query = $this->fdb->get_where('real_suppliers', array('rs_id' => $val['fdd_supplier_id']))->result();
					}
					if (!empty($query))
					{
						$own_prods[$key]['s_name']=$query[0]->s_name;
					}
					else {
						$own_prods[$key]['s_name']="";
					}
				}
			
			}
			$own_prods[$key]['total_quant'] = $this->number2db(round(($own_prod['quantity']/$semi_total)*$quantity, 2));
			if ($own_prods[$key]['total_quant'] == 0)
			{
				$own_prods[$key]['total_quant'] = 0.01;
			}
		}
		echo json_encode(array($fdd_prods,$own_prods,$final_status_arr));
	}

	function get_semi_name(){
		

		$semi_ids = $this->input->post('semi_ids');

		$response = array();
		foreach ($semi_ids as $semi_id){
			$this->db->select('id,proname');
			$this->db->where('id',$semi_id);
			$res = $this->db->get('products')->result_array();
			if(!empty($res)){
				$response[] = $res[0];
			}
		}

		echo json_encode($response);

	}

	/**
	 * @name move_from_custom
	 * @param number $pid
	 * @param number $type
	 */
	function move_from_custom($pid = 0, $type = 0){
		if($pid && $type){
			
			$this->db->where(array('id'=>$pid,'company_id'=>$this->company_id));
			$this->db->update('products',array('categories_id'=>0,'subcategories_id'=>0,'direct_kcp'=>1,'semi_product'=>$type));
			$affectedRows = $this->db->affected_rows();
			if($affectedRows){
				echo true;die;
			}
		}
		echo false;die;
	}

	/**
	 * @name recipe_calculate
	 */
	function recipe_calculate(){
		
		$this->db->select('recipe_calculate');
		$result = $this->db->get_where('company',array('id'=>$this->company_id))->row();

		if(!empty($result)){
			$this->db->where('id',$this->company_id);
			$this->db->update('company',array('recipe_calculate'=>$result->recipe_calculate +1));

			$recipe_calculate = 5-(intval($result->recipe_calculate) +1);
			echo $recipe_calculate;die;
		}
		$recipe_calculate = 5-intval($result->recipe_calculate);
		echo $recipe_calculate;die;
	}


	function label_export_download(){
		$data=array('download_clicked'=>"1",'download_clicked_date'=>date('Y-m-d H:i:s'));
        $this->db->where(array('company_id' => $this->company_id));
        $this->db->update('products',$data);
        $filename = $this->label_export();

        $report_export_array = array(
        	                         "company_id" => $this->company_id,
        	                         "report_export_name" => 'label_export',
        	                         "type" => 'Export',
        	                         "filename" => $filename,
        	                         "date" => date('Y-m-d H:i:s')
        	                        );
        $this->db->insert('report_export_download',$report_export_array);

	}

	function label_export($product_id= 0, $lotnr = 0, $duedate = 0, $conserve_min = 0, $conserve_max = 0){
		
		$this->db->select('company.company_name,company.address,company.zipcode,company.city,company.phone,company.website,general_settings.labeler_logo');
		$this->db->join('general_settings','general_settings.company_id=company.id');
		$this->db->where('company.id',$this->company_id);
		$company = $this->db->get('company')->row_array();

		$this->db->select('id,pro_art_num,proname,prodescription,sell_product_option,price_per_person,price_per_unit,price_weight,recipe_weight');
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where);
		$this->db->where(array('company_id'=>$this->company_id));
		if ($product_id)
			$this->db->where('id',$product_id);
		$this->db->order_by('proname');
		$products = $this->db->get('products')->result_array();

		if(!empty($products)){
			$this->load->model('Mproducts');

			
			$this->fdb->select( array( 'all_id', 'all_name'.$this->lang_u ) );
			$aller_arr = $this->fdb->get('allergence')->result_array();

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Labels Export') );

			$counter = 1;
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('PLU') )->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('PRODUCTNAAM') )->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('OMSCHRIJVING') )->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('TARIEF') )->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('INGREDIENTEN') )->getStyle('E'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('ALLERGENEN') )->getStyle('F'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('VOEDINGWAARDEN') )->getStyle('G'.$counter)->getFont()->setBold(true);
			if ($product_id){
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('THT') )->getStyle('H'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('I'.$counter, _('Lotnr') )->getStyle('I'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('J'.$counter, _('Bewaren') )->getStyle('J'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('K'.$counter, _('Companyname') )->getStyle('K'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('L'.$counter, _('Address') )->getStyle('L'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('M'.$counter, _('zipcode-city') )->getStyle('M'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('N'.$counter, _('Phone') )->getStyle('N'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('O'.$counter, _('URL') )->getStyle('O'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('P'.$counter, _('LOGO') )->getStyle('P'.$counter)->getFont()->setBold(true);
			}

			foreach ($products as $pro_key=>$product){
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
						$allergence_words[strtolower($val['all_name'.$this->lang_u])] = $val['all_id'];
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
					$additive_arr = array();
					$add_name = 'add_name'.$this->lang_u;
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
				$all = ($all != '')?substr($all, 0, -2):'';

				$recipe_wt = $product['recipe_weight'];
				if($recipe_wt != 0){
					$recipe_wt = $recipe_wt*1000;
				}else{
					$recipe_wt = 100;
				}

				$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($product['id']);
				$nutri_values = array();
				$nutri_str = '';
				if (!empty($has_fdd_quant)){
					$nutri_values['e_val_1'] = 0;
					$nutri_values['e_val_2'] = 0;
					$nutri_values['protiens'] = 0;
					$nutri_values['carbo'] = 0;
					$nutri_values['sugar'] = 0;
					$nutri_values['fats'] = 0;
					$nutri_values['sat_fats'] = 0;
					$nutri_values['salt'] = 0;
					$nutri_values['fibers'] = 0;

                    echo '<pre>';
                   // print_r($has_fdd_quant);die;
					foreach ($has_fdd_quant as $has_fdd_qu){
						echo '<pre>';
						echo $has_fdd_qu['fdd_pro_id'];
						echo '<br>';
						
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
							$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
						}
					}

					$fibers = ($nutri_values['fibers'] != 0)?". vezels ".defined_money_format($nutri_values['fibers'],1)."g":"";
					//$nutri_str = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. waarvan - suikers ".defined_money_format($nutri_values['sugar'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
					$nutri_str = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
				}

				if ($product_id){
					$zipcity = trim($company['zipcode']." ".$company['city']);
					$labeler_logo = ($company['labeler_logo'] != '')?base_url()."assets/cp/labeler_logo/".$company['labeler_logo']:"";
					$duedate = date('d/m/Y', strtotime('+'.$duedate.' days'));
					$bewaren = "Bewaren tussen ".$conserve_min." en ".$conserve_max."°C";
				}

				$counter++;
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $product['pro_art_num'] );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, stripslashes($product['proname']) );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, stripslashes($product['prodescription']) );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $tarief );
				$this->excel->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->setWrapText(true);
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, $ing );
				$this->excel->getActiveSheet()->setCellValue('F'.$counter, $all );
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $nutri_str );
				if ($product_id){
					$this->excel->getActiveSheet()->setCellValue('H'.$counter, $duedate );
					$this->excel->getActiveSheet()->setCellValue('I'.$counter, $lotnr );
					$this->excel->getActiveSheet()->setCellValue('J'.$counter, $bewaren );
					$this->excel->getActiveSheet()->setCellValue('K'.$counter, $company['company_name'] );
					$this->excel->getActiveSheet()->setCellValue('L'.$counter, trim($company['address']) );
					$this->excel->getActiveSheet()->setCellValue('M'.$counter, $zipcity );
					$this->excel->getActiveSheet()->setCellValue('N'.$counter, $company['phone'] );
					$this->excel->getActiveSheet()->setCellValue('O'.$counter, $company['website'] );
					$this->excel->getActiveSheet()->setCellValue('P'.$counter, $labeler_logo );
				}
			}
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
			if ($product_id){
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(40);
			}
			$datestamp = date("d-m-Y");
			$filename = "export-labels-".$this->company_id."-".$datestamp.".xls";
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
		}
		else{
			die("No products available");
		}
	}


	function label_export_download_link($filename){
		$filepath = base_url().'assets/cp/rep_exp_files/label_export/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function print_product_download_link($filename){
        $filepath = base_url().'assets/cp/rep_exp_files/print_all_report_import/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/pdf");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}
	function tech_import_download_link($filename){
		$filepath = base_url().'assets/cp/rep_exp_files/tech_import/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();

	}

	function recipe_import_download_link($filename){
        $filepath = base_url().'assets/cp/rep_exp_files/recipe_import/';
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Transfer-Encoding: binary");
		readfile($filepath.$filename); // push it out
		exit();
	}

	function delete_dir($path = ''){
		$files = glob(dirname(__FILE__).'/../../../assets/technical_and_recipe_sheets/'.$path.'/*'); // get all file names
		foreach($files as $file){ // iterate files
			if(is_file($file))
				unlink($file); // delete file
		}

		if(!rmdir(dirname(__FILE__).'/../../../assets/technical_and_recipe_sheets/'.$path)){
			echo "could not remove";
		}
	}

	function assign_to_recheck($fdd_pro_id = 0){
		if($fdd_pro_id != 0){
			$this->fdb->where('p_id', $fdd_pro_id);
			$this->fdb->update('products',array('re_checked'=>0));
			echo "1";
		}
	}

	function update_pdf(){

		define ("FILEREPOSITORY","./assets/cp/fdd_pdf");

		$fdd_pro_id =  $this->input->post('fdd_pro_pdf_id');
		$cur_url =  $this->input->post('cur_url');

		if($_FILES['pdf']['name']){
			if (is_uploaded_file($_FILES['pdf']['tmp_name'])) {

				if ($_FILES['pdf']['type'] != "application/pdf" && $_FILES['pdf']['type'] != "application/x-download") {
					$msg =  "Productsheet must be uploaded in PDF format.";
				} else {
					$name = $_FILES['pdf']['name'];
					$name = str_replace(' ', '', $name);
					$file_name = substr($name, 0, -3).date('Y-m-d_H_i_s');
					$result = move_uploaded_file($_FILES['pdf']['tmp_name'], FILEREPOSITORY."/$file_name.pdf");
					if ($result == 1){
						$ftp_server = $this->config->item('ftp_server');
						$ftpuser = $this->config->item('ftpuser');
						$pass = $this->config->item('pass');
						$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
						$login = ftp_login($ftp_conn, $ftpuser, $pass);

						$file = FILEREPOSITORY."/$file_name.pdf";

						ftp_pasv($ftp_conn, true);

						// upload file
						if (ftp_put($ftp_conn, "public_html/assets/cp/uploads/".$file_name.".pdf", $file, FTP_BINARY))
						{
							$insert_array = array(
									'sheet_name'=>$file_name.".pdf",
									'uploaded_by'=>$this->company->id,
									'date' => date("Y-m-d H:i:s")
							);
							$q = 'update products set old_pdf = data_sheet, re_checked=0 where p_id = '.$fdd_pro_id;
							$this->fdb->query($q);

							$this->fdb->where('p_id',$fdd_pro_id);
							$this->fdb->update('products',array('data_sheet'=>$file_name.".pdf"));
							redirect($cur_url);
						}
						else
						{
							echo "not send by ftp";
						}

						ftp_close($ftp_conn);


					}else{
						echo "unable to move";
					}
				}
			}else{
				echo "no_temp_file";
			}
		}
	}


    function download_later(){
      $name = $this->input->post('name');
      $this->db->select('company_id');
      $this->db->where('company_id',$this->company_id);
      $result = $this->db->get('report_export')->result();

      if(empty($result)){
         $data=array('company_id'=>$this->company_id,$name =>'1');
         $this->db->insert('report_export',$data);
      }
      elseif(!empty($result)){
          $data=array($name =>'1');
          $this->db->where(array('company_id' => $this->company_id));
          $this->db->update('report_export',$data);
      }
    }

	function remove_downloaded_file(){
		$download_id = $this->input->post('name');
		$this->db->where('download_id',$download_id);
		$this->db->delete('report_export_download');
		echo "removed";
	}

	function get_enbr_status($company_id = 0){
		$this->db->select('enbr_status');
		return $this->db->get_where('general_settings',array('company_id'=>$company_id))->row_array();
	}

	/**
	 * Function to get array containing e-nbr value and name for corresponding ingredient if available
	 * @param number $id
	 * @param number $enbr_setting
	 * @return multitype:NULL |multitype:array
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_e_current_nbr($id = 0,$enbr_setting = 0,$sel_lang_upd = '_dch'){
		if($id){
			

			$this->fdb->select('a.ing_id as enbr_id, a.ing_name'.$sel_lang_upd.' as enbr_ing_name, b.ing_id as name_id, b.ing_name'.$sel_lang_upd.' as name_ing_name');
			$this->fdb->join('ingredients a','a.ing_id = enbrs_relation.enbr_id');
			$this->fdb->join('ingredients b','b.ing_id = enbrs_relation.name_id');
			$this->fdb->where('enbrs_relation.enbr_id',$id);
			$result = $this->fdb->get('enbrs_relation')->result_array();

			if(!empty($result)){
				if($enbr_setting == 1){
					return array('ki_id'=>$result[0]['enbr_id'],'ki_name'=>$result[0]['enbr_ing_name'],'enbr_rel_ki_id'=>$result[0]['name_id'],'enbr_rel_ki_name'=>$result[0]['name_ing_name']);
				}
				elseif($enbr_setting == 2){
					return array('ki_id'=>$result[0]['name_id'],'ki_name'=>$result[0]['name_ing_name'],'enbr_rel_ki_id'=>$result[0]['enbr_id'],'enbr_rel_ki_name'=>$result[0]['enbr_ing_name']);
				}
			}
			else{
				$this->fdb->select('a.ing_id as enbr_id, a.ing_name'.$sel_lang_upd.' as enbr_ing_name, b.ing_id as name_id, b.ing_name'.$sel_lang_upd.' as name_ing_name');
				$this->fdb->join('ingredients a','a.ing_id = enbrs_relation.enbr_id');
				$this->fdb->join('ingredients b','b.ing_id = enbrs_relation.name_id');
				$this->fdb->where('enbrs_relation.name_id',$id);
				$result = $this->fdb->get('enbrs_relation')->result_array();

				if(!empty($result)){
					if($enbr_setting == 1){
						return array('ki_id'=>$result[0]['enbr_id'],'ki_name'=>$result[0]['enbr_ing_name'],'enbr_rel_ki_id'=>$result[0]['name_id'],'enbr_rel_ki_name'=>$result[0]['name_ing_name']);
					}
					elseif($enbr_setting == 2){
						return array('ki_id'=>$result[0]['name_id'],'ki_name'=>$result[0]['name_ing_name'],'enbr_rel_ki_id'=>$result[0]['enbr_id'],'enbr_rel_ki_name'=>$result[0]['enbr_ing_name']);
					}
				}
			}
		}
		return array();
	}

	/**
	 *
	 * Function to mark product as favourate
	 *@author Abhishek Singh <abhisheksingh@cedcoss.com>
	 */

	function Save_Prod_Status( ){
		$status_array = $this->input->post('status_array');
	 	if( !empty( $status_array ) ){
	 		$status_array[ 'company_id' ] = $this->company_id;
	 		$this->M_fooddesk->update_fdd_pro_fav( $status_array );
	 	}
	}

	/**
	 *
	 * Function to get product status
	 *@author Abhishek Singh <abhisheksingh@cedcoss.com>
	 */

	function get_product_status( ){
		$fdd_pro_id = $this->input->post( 'fdd_pro_id' );
		$company_id = $this->company_id;
		if( $fdd_pro_id ){
			$result = $this->M_fooddesk->getSingleProduct_status( $fdd_pro_id , $company_id );
			echo $result;
		}

	}

	function get_fixed_ajax( $fdd_pro_id ){
		$this->fdb->select( 'product_type,review&fixed' );
		$query = $this->fdb->get_where( 'products', array( 'p_id' => $fdd_pro_id ) )->row_array();
		return $query;
	}
}