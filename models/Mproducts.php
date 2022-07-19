<?php
class Mproducts extends CI_Model{

	var $image="";

	function __construct(){
		parent::__construct();
		$image="";
		
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->lang_u = get_lang( $_COOKIE['locale'] );
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function get_shop_image_size(){
		$this->db->where(array('company_id'=>$this->company_id));
		$query=$this->db->get('general_settings')->result_array();
		return $query;
	}
	function get_products_for_changegroup($cat_id = null,$sub_cat_id = null, $type = 'per_unit' ){

		$this->db->select('id');
		if($cat_id != null && $sub_cat_id != null){
			$this->db->where('categories_id',$cat_id);
			$this->db->where('subcategories_id',$sub_cat_id);
		}
		$this->db->where('sell_product_option', $type);
		$this->db->where(array('company_id'=>$this->company_id));
		$query=$this->db->get('products')->result();
		return $query;
	}

	function get_products($cat_id=null,$sub_cat_id=null,$limit=null,$offset=null,$chk_company_id = true){
		if($cat_id && $sub_cat_id){
			$this->db->where('categories_id',$cat_id);
			$this->db->where('subcategories_id',$sub_cat_id);
		}else if($cat_id){
			$this->db->where('categories_id',$cat_id);
		}
		if($limit){
			$this->db->limit($limit,$offset);
		}

		if( $chk_company_id )
		$this->db->where(array('company_id'=>$this->company_id));

		$this->db->order_by( 'pro_display', 'ASC' );
		$query=$this->db->get('products')->result();
		$sorted_p = array();
		$unsorted_p = array();
		foreach($query as $prod) {
			if(isset($prod->pro_display) && $prod->pro_display != 0){
				$sorted_p[] = $prod;
			}else{
				$unsorted_p[] = $prod;
			}

		}
		$query = array_merge($sorted_p,$unsorted_p);
		foreach ($query as $k=>$qr){
			$query[$k]->no_fdd_con = 0;
			$query[$k]->via_api_pro = 0;
			if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
				$via_api_pro = 0;
				$complete = 1;
				if($qr->direct_kcp == 1){
					$this->db->where(array('obs_pro_id'=>$qr->id,'is_obs_product'=>0));
					$result = $this->db->get('fdd_pro_quantity')->result_array();
					if(empty($result)){
						$complete = 0;
					}
				}
				else{

					$this->db->where(array('obs_pro_id'=>$qr->id));
					$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
					if(!empty($result_custom)){
						
						foreach ($result_custom as $val){
							
							if($val['is_obs_product'] == 1){
								$this->db->where(array('product_id'=>$val['fdd_pro_id']));
								$result_product = $this->db->get('products_pending')->row();
								if (!empty( $result_product )) {
									if ($result_product->via_api == 1) {
										$via_api_pro = 1; 
									}
									else{
										$via_api_pro = 0;
									}
								}
								$complete = 0;
								break;
							} else {
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

				if($complete == 0){
					$query[$k]->no_fdd_con = 1;
				}
				if ($via_api_pro != 0) {
					$query[$k]->via_api_pro = 1;
				}

			}
		}

		/*
		foreach ($query as $k=>$qr){
			$query[$k]->make_it_blue = 0;
			$query[$k]->no_fdd_con = 0;

			if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
				$this->db->where(array('obs_pro_id'=>$qr->id,'is_obs_product'=>0));
				$result = $this->db->get('fdd_pro_quantity')->result();
				if(empty($result)){
					$query[$k]->no_fdd_con = 1;
				}else{
					$total = 0;
					foreach ($result as $res){
						$total += $res->quantity;
					}
					if($qr->recipe_weight*1000 != $total){
						$query[$k]->make_it_blue = 1;
					}
				}

			}
		} */
		return($query);
	}

	/**
	 * This function fetches products to assign
	 * @return array $query
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_products_to_assign($cat = 0,$sub_cat = 0, $shared_prod = 0){
		$shared_product_arr = array();
		$where_condition ="((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))";

		$this->db->select(array('id','categories_id','subcategories_id','pro_art_num','proname','prodescription','direct_kcp'));
		$this->db->where('company_id',$this->company_id);
		if($cat != 0){
			if(is_array($sub_cat)){
				if(!empty($sub_cat)){
					$this->db->where(array('categories_id'=>$cat));
					$this->db->where_in('subcategories_id',$sub_cat);
				}
			}
			elseif($sub_cat != 0){
				$this->db->where(array('categories_id'=>$cat,'subcategories_id'=>$sub_cat));
			}
		}

		$this->db->order_by('id','asc');
		if ($shared_prod){
			$query = $this->db->get_where('products',array('direct_kcp' => 0,'parent_proid' => 0, 'semi_product'=>0))->result_array();

			if(!empty($query)){
				foreach($query as $i => $product){
					if($product['direct_kcp'] == 0){
						$this->db->select('id');
						$this->db->where(array('obs_pro_id'=> $product['id'],'semi_product_id !='=>0));
						$result = $this->db->get('fdd_pro_quantity')->result();
						if(empty($result)){
							$shared_product_arr[] = $query[$i];
						}
					}
				}
			}

			if ($shared_prod){
				foreach ($shared_product_arr as $key=>$val){
					$pro_check = $this->db->get_where('products_shared',array('proid'=>$val['id']))->result_array();
					if (!empty($pro_check)){
						$shared_product_arr[$key]['shared_status'] = 1;
					}
				}
			}
			return $shared_product_arr;
		}
		else{
			$this->db->where($where_condition);
			$query = $this->db->get('products')->result_array();
			return $query;
		}

		/*$this->db->select(array('products.id','products.categories_id','products.subcategories_id','products.pro_art_num','products.proname','products.prodescription','categories.name','subcategories.subname'));
		$this->db->where(array('products.company_id'=>$this->company_id,'products.semi_product' => 0));
		$this->db->join('subcategories','subcategories.id = products.subcategories_id');
		$this->db->join('categories','categories.id = products.categories_id');
		$query	=	$this->db->get('products')->result_array();*/
	}

	function change_product_sequence(){
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('products',array('pro_display'=>$this->input->post('order')));
	}

	/**
	 * This function deletes the current product
	 * @param number $prod_id
	 * @return string|boolean
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function delete_product($prod_id=0){
		if ($prod_id){
			$product_id=$prod_id;
		}
		else{
			$product_id = $this->input->post('id');
		}
		$this->db->select('categories_id,subcategories_id,image,direct_kcp,semi_product');
		$this->db->where(array('id'=>$product_id));
		$returndata = $this->db->get('products')->result();

		if(!empty($returndata)){
			if(($returndata[0]->direct_kcp == 0) && ($returndata[0]->semi_product == 1)){
				$this->db->where('id',$product_id);
				$this->db->update('products', array('semi_product'=>0));
				$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$product_id))->result_array();
				if(!empty($semi_info)){
					foreach ($semi_info as $semi){
						$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
						$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
						$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
						$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
					}
				}
				$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$product_id));
			}

			//----deleting product's corresponding rowa from various related tables---//
			$filepath = dirname(__FILE__);
			$this->db->delete('products_labeler',array('product_id'=>$product_id));
			$this->db->delete('products_pending',array('product_id'=>$product_id));
			$this->db->delete('groups_products',array('products_id'=>$product_id));
			$this->db->delete('products_discount',array('products_id'=>$product_id));
			$this->db->delete('groups_order',array('products_id'=>$product_id));
			$this->db->delete('products_ingredients',array('product_id'=>$product_id));
			$this->db->delete('products_traces',array('product_id'=>$product_id));
			$this->db->delete('products_allergence',array('product_id'=>$product_id));
			$this->db->delete('product_sub_allergence',array('product_id'=>$product_id));
			$this->db->delete('fdd_pro_quantity',array('obs_pro_id'=>$product_id));

			// Deleting from products which contain this semi product (Only applicable for semi products)
			$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$product_id))->result_array();
			if(!empty($semi_info)){
				foreach ($semi_info as $semi){
					$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
					$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
					$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
					$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
				}
			}
			$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$product_id));

			// Deleting from orders
			$order_detail_info = $this->db->get_where('order_details', array('products_id' => $product_id))->result();
			$this->db->delete('order_details',array('products_id'=>$product_id));
			if(!empty($order_detail_info)){
				foreach ($order_detail_info as $order_info){
					$order_detail = $this->db->get_where('order_details', array('orders_id' => $order_info->orders_id))->result();
					if(empty($order_detail))
						$this->db->delete('orders', array('id' => $order_info->orders_id));
				}
			}

			//Deleting unattached products from table products_pending

			$this->delete_notcontaining_products();

			/*======================function to remove uploaded images================*/
			if($returndata[0]->image != ''){
				//$record_num = end(explode('/',$returndata[0]->image));
				//$output = unlink($filepath.'/../../assets/cp/images/product/'.$record_num);

				$this->load->helper('resize');
				delete_rsz_imgs('product',$product_id);//,$returndata[0]->image
			}

			/*=======================================================================*/
			$this->db->delete('products_shared',array('proid'=>$product_id));
			$this->db->delete('products',array('id'=>$product_id));

			return json_encode($returndata[0]);
		}
		return false;
	}

	function get_product_information( $id, $select = array() ){ 
		if( !empty( $select ) ) {
			$this->db->select( $select );	
		}
		$this->db->where(array('id'=>$id,'company_id'=>$this->company_id));
		$query=$this->db->get('products');
		return $query->result();
	}

	function get_product_type($id){
		
		$this->fdb->select('product_type');
		$this->fdb->where(array('p_id' => $id));
		$query = $this->fdb->get('products'); 
		return $query->result();	
	}

	function get_default_image(  ){
		$this->db->select( 'comp_default_image' );	
		$this->db->where(array('company_id'=>$this->company_id));
		$query=$this->db->get('general_settings');
		return $query->row_array();
	}

	function get_product_information_own($id){
		$this->db->where(array('id'=>$id,'company_id'=>0));
		$query=$this->db->get('products')->result();

		$result=array();
		foreach ($query as $key=>$val)
		{
			
			if ($val->fdd_producer_id && $val->fdd_supplier_id)
			{
				$this->fdb->select('s_name');
				$result= $this->fdb->get_where('suppliers', array('s_id' => $val->fdd_producer_id,'is_controller_cp' => 0))->result();
			}elseif ($val->fdd_producer_id){
				$this->fdb->select('s_name');
				$result = $this->fdb->get_where('suppliers', array('s_id' => $val->fdd_producer_id,'is_controller_cp' => 0))->result();
			}elseif ($val->fdd_supplier_id){
				$this->fdb->select('rs_name as s_name');
				$result = $this->fdb->get_where('real_suppliers', array('rs_id' => $val->fdd_supplier_id))->result();
			}

			if (!empty($result))
			{
				$query[$key]->s_name=$result[0]->s_name;
			}
			else {
				$query[$key]->s_name="";
			}
		}
		

		return $query;

	}

	function upload_image( $prod_id = NULL, $prod_name = NULL ){

		if($_FILES['image']['name']){
			$config['upload_path']='./assets/cp/images/product/';
			$config['allowed_types'] = 'gif|jpg|jpeg|JPG|GIF|png';
			$config['max_size']	= '150';
			$config['max_width']  = '300';
			$config['max_height']  = '300';
			$config['remove_spaces']  = true;

			if($prod_id && $prod_name){
			  $config['file_name'] = $prod_id.'_'.$prod_name.'.jpg';
			}

			$this->load->library('upload', $config);
			if(!$this->upload->do_upload('image')){

				$this->image = "";
				$this->load->library('messages');
				$this->messages->add($this->upload->display_errors(), 'error');

			}else{
				$data = array('upload_data' => $this->upload->data());
				$this->image = $data['upload_data']['file_name'];

				if($prod_id && $prod_name){
					$update_product_data['image'] = $this->image;
					$this->db->where('id',$prod_id);
					$this->db->update('products', $update_product_data);
				}
			}
		}
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

	function update_product($sel_lang = 'nl_NL'){
		if ($sel_lang == 'fr_FR'){
			$sel_lang_upd = '_fr';
		}elseif($sel_lang == 'nl_NL'){
			$sel_lang_upd = '_dch';
		}else{
			$sel_lang_upd = '_dch';
		}
		if($this->input->post('semi_products') == 1){
			$custom_pro_total_wt_arr = $this->update_semi_product_contains($sel_lang_upd);
		}
		if($this->input->post('semi_products') == 2){
			$custom_pro_total_wt_array = $this->update_semi_product_contains($sel_lang_upd);
		}
		$quant_array =$this->input->post('hidden_fdds_quantity');

		if($quant_array != ''){
			if($quant_array == '&&'){
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_gs1_products();
			}else{
				$quant_array = substr($quant_array, 0, -2);
				$quant_arr = explode('**', $quant_array);
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
				$this->db->delete('fdd_pro_quantity');


				foreach ($quant_arr as $quant_ar){
					$insrt_gs1_array = array();
					$quant_ar_ar = explode('#', $quant_ar);
					$gs1_pro_check = explode('--', $quant_ar_ar[2]);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}
					$insrt_quant_array = array(
							'obs_pro_id'=>$this->input->post('prod_id'),
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'semi_product_id'=>$semi_pro_id,
							'fixed'=>$quant_ar_ar[6]
					);
					
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

					if ((isset($gs1_pro_check[4]) && $gs1_pro_check[4] == 'GS1')){
						$insrt_gs1_array = array(
								'gs1_pid'=>$quant_ar_ar[0],
								'company_id'=>$this->company_id
						);
					}
					if(!empty($insrt_gs1_array)){
						$this->db->select('request_status');
						$gs1_exist = $this->db->get_where('request_gs1',array('gs1_pid'=>$quant_ar_ar[0]))->result_array();

						if (empty($gs1_exist)){
							$insrt_gs1_array[ 'date_added' ] = date( "Y-m-d H:i:s" );
							$this->db->insert('request_gs1',$insrt_gs1_array);
						}
						else{
							$request_status = $gs1_exist[0]['request_status'];
							$gs1_exist = $this->db->get_where('request_gs1',array('company_id'=>$this->company_id,'gs1_pid'=>$quant_ar_ar[0]))->result_array();
							if (empty($gs1_exist))
							{
								$insrt_gs1_array['request_status'] 	= $request_status;
								$insrt_gs1_array[ 'date_added' ] 	= date( "Y-m-d H:i:s" );
								$this->db->insert('request_gs1',$insrt_gs1_array);
							}
						}
					}
				}
				$this->delete_notcontaining_gs1_products();
			}
		}


		$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
		if($obs_quant_array != ''){
			if($obs_quant_array == '&&'){
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_products();
			}else{
				$obs_quant_array = substr($obs_quant_array, 0, -2);
				$quant_arr = explode('**', $obs_quant_array);

				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_products();

				foreach ($quant_arr as $quant_ar){
					$quant_ar_ar = explode('#', $quant_ar);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}

					$insrt_quant_array = array(
							'obs_pro_id'=>$this->input->post('prod_id'),
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'is_obs_product'=>1,
							'semi_product_id'=>$semi_pro_id,
							'fixed'=>$quant_ar_ar[6]
					);
					
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);
					if($semi_pro_id == 0){
						$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$this->company_id))->result_array();
						if(empty($info)){
							$this->db->insert('products_pending', array('product_id' => $quant_ar_ar[0], 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
						}

						$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
						if(empty($con_info)){
							$this->db->insert('contacted_via_mail', array('obs_pro_id' => $quant_ar_ar[0]));
						}
					}

				}
			}
		}

		$update_product_data = array(
			'proname' => addslashes($this->input->post('proname')),
			'prodescription' => addslashes($this->input->post('prodescription')),
			'proupdated' => date('Y-m-d',time()),
			'recipe_method' => $this->input->post('recipe_method_txt')
		);

		// If company not keurslager associate then add normal ingredients
		If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' ||$this->session->userdata('menu_type') == 'fdd_premium')){
			$update_product_data['ingredients'] = $this->input->post('ingredients');
			$update_product_data['allergence'] = $this->input->post('allergence');
			$update_product_data['traces_of'] = $this->input->post('traces_of');
		}

		$update_product_data['recipe_weight'] = $this->input->post('recipe_weight');

		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){

			$this->db->delete('products_ingredients',array('product_id' => $this->input->post('prod_id')));

			// Adding Ingredients
			$this->adding_ingredients($this->input->post('prod_id'),$sel_lang_upd);


			$this->db->delete('products_traces',array('product_id' => $this->input->post('prod_id')));

			// Adding Traces
			$this->adding_traces($this->input->post('prod_id'));


			$this->db->delete('products_allergence',array('product_id' => $this->input->post('prod_id')));
			$this->db->delete('product_sub_allergence',array('product_id' => $this->input->post('prod_id')));

			// Adding Allergence
			$this->adding_allergence($this->input->post('prod_id'));
		}

		if($this->input->post('semi_products') == 1){
			 $this->update_semi_product_quant($custom_pro_total_wt_arr);
			 $this->update_kp_display_order($this->input->post('prod_id'));
		}

		if($this->input->post('semi_products') == 2){
			$this->update_semi_product_quant($custom_pro_total_wt_array);
			$this->update_kp_display_order($this->input->post('prod_id'));
		}

		return(array('categories_id'=>$this->input->post('categories_id'),'subcategories_id'=>$this->input->post('subcategories_id')));
	}

	/* function resize_product_images($image_name = null,$is_refresh = false){
		if(isset($image_name)){
			$this->resize_product_image($image_name,46,46,$is_refresh);
			$this->resize_product_image($image_name,60,null,$is_refresh);
			$this->resize_product_image($image_name,100,null,$is_refresh);
			$this->resize_product_image($image_name,270,null,$is_refresh);
		}
	}

	private function resize_product_image($image_name = null,$width = null,$height = null,$is_refresh = false){
		if(isset($image_name) && isset($width) ){
			$srcpath = dirname(__FILE__).'/../../assets/cp/images/product/'.$image_name;//$data['upload_data']['full_path'];
			$newpath = dirname(__FILE__).'/../../assets/cp/images/product_'.$width.'_'.(isset($height)?$height:$width).'/'.$image_name;
			if(file_exists($srcpath) && (!$is_refresh || !file_exists($newpath))){//if is refresh then check if file does not exists already
				$config['image_library'] = 'gd2';
				$config['source_image']    = $srcpath;
				$config['new_image'] = $newpath;
				$config['maintain_ratio'] = TRUE;
				$config['width']     = $width;
				$config['height']    = isset($height)?$height:$width;
				$this->load->library('image_lib');
				$this->image_lib->initialize($config);
				if ( ! $this->image_lib->resize())//is_writable($newpath)
				{
					echo ' ERROR:(for image:-'.$image_name.') : ';
					echo $this->image_lib->display_errors();
				}
				$this->image_lib->clear();
			}
		}
	} */


	function update_product_details($product_id,$param = array()){
	   if($product_id && !empty($param)){
	     $this->db->where('id',$product_id);
	     $this->db->update('products',$param);

		 return true;
	   }
	   else
	     return false;
	}

	function update_product_status(){
	   	$this->id=$this->input->post('id');
		$this->status=$this->input->post('status');
		$this->method=$this->input->post('method');
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('products',array('status'=>$this->input->post('status')));
	}

	function add_product($sel_lang = 'nl_NL'){
		if ($sel_lang == 'fr_FR'){
			$sel_lang_upd = '_fr';
		}elseif($sel_lang == 'nl_NL'){
			$sel_lang_upd = '_dch';
		}else{
			$sel_lang_upd = '_dch';
		}
		$add_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'prodescription' => addslashes($this->input->post('prodescription')),
				'procreated'=>date('Y-m-d',time()),
				'recipe_method' => $this->input->post('recipe_method_txt')
		);
		if($this->input->post('semi_products') == 1){
			$add_product_data['semi_product'] = 1;
			$add_product_data['recipe_weight'] = $this->input->post('recipe_weight');
		}

		if($this->input->post('semi_products') == 2){
			$add_product_data['semi_product'] = 2;
			$add_product_data['recipe_weight'] = $this->input->post('recipe_weight');
		}

		if(!$this->input->post('product_type')){
			$add_product_data['direct_kcp'] = $this->input->post('direct_kcp');
		}

		// If company not keurslager associate then add normal ingredients
		If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')){
			$add_product_data['ingredients'] = $this->input->post('ingredients');
			$add_product_data['allergence'] = $this->input->post('allergence');
			$add_product_data['traces_of'] = $this->input->post('traces_of');
		}
		$query=$this->db->insert('products', $add_product_data);
		$new_product_id=$this->db->insert_id();//method to get id of last inserted row


		$this->delete_notcontaining_gs1_products();

		//adding fdd products quantity
		$quant_array =$this->input->post('hidden_fdds_quantity');
		if($quant_array != '' && $quant_array != '&&'){
			$quant_array = substr($quant_array, 0, -2);
			$quant_arr = explode('**', $quant_array);
			foreach ($quant_arr as $quant_ar){
				$insrt_gs1_array = array();
				$quant_ar_ar = explode('#', $quant_ar);
				$gs1_pro_check = explode('--', $quant_ar_ar[2]);

				$semi_pro_id = 0;
				if($quant_ar_ar[5] != NULL){
					$semi_pro_id = $quant_ar_ar[5];
				}

				$insrt_quant_array = array(
						'obs_pro_id'=>$new_product_id,
						'fdd_pro_id'=>$quant_ar_ar[0],
						'quantity'=>$quant_ar_ar[1],
						'unit'=>$quant_ar_ar[4],
						'semi_product_id'=>$semi_pro_id,
						'fixed'=>$quant_ar_ar[6]
				);
				
				$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

				if ((isset($gs1_pro_check[4]) && $gs1_pro_check[4] == 'GS1')){
					$insrt_gs1_array = array(
							'gs1_pid'=>$quant_ar_ar[0],
							'company_id'=>$this->company_id
					);
				}
				if(!empty($insrt_gs1_array)){
					$this->db->select('request_status');
					$gs1_exist = $this->db->get_where('request_gs1',array('gs1_pid'=>$quant_ar_ar[0]))->result_array();

					if (empty($gs1_exist)){
						$insrt_gs1_array[ 'date_added' ] = date( "Y-m-d H:i:s" );
						$this->db->insert('request_gs1',$insrt_gs1_array);
					}
					else{
						$request_status = $gs1_exist[0]['request_status'];
						$gs1_exist = $this->db->get_where('request_gs1',array('company_id'=>$this->company_id,'gs1_pid'=>$quant_ar_ar[0]))->result_array();
						if (empty($gs1_exist))
						{
							$insrt_gs1_array[ 'date_added' ] 	= date( "Y-m-d H:i:s" );
							$insrt_gs1_array['request_status'] 	= $request_status;
							$this->db->insert('request_gs1',$insrt_gs1_array);
						}
					}
				}
			}
		}

		$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
		if($obs_quant_array != '' && $obs_quant_array != '&&'){
			$obs_quant_array = substr($obs_quant_array, 0, -2);
			$quant_arr = explode('**', $obs_quant_array);
			foreach ($quant_arr as $quant_ar){
				$quant_ar_ar = explode('#', $quant_ar);

				$semi_pro_id = 0;
				if($quant_ar_ar[5] != NULL){
					$semi_pro_id = $quant_ar_ar[5];
				}

				$insrt_quant_array = array(
						'obs_pro_id'=>$new_product_id,
						'fdd_pro_id'=>$quant_ar_ar[0],
						'quantity'=>$quant_ar_ar[1],
						'unit'=>$quant_ar_ar[4],
						'is_obs_product'=>1,
						'semi_product_id'=>$semi_pro_id,
						'fixed'=>1
				);
				
				$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

				if($semi_pro_id == 0){
					$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$this->company_id))->result_array();
					if(empty($info)){
						$this->db->insert('products_pending', array('product_id' => $quant_ar_ar[0], 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
					}

					$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
					if(empty($con_info)){
						$this->db->insert('contacted_via_mail', array('obs_pro_id' => $quant_ar_ar[0]));
					}
				}
			}
		}

		If($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
			// Adding Ingredients
			$this->adding_ingredients($new_product_id,$sel_lang_upd);

			// Adding Traces
			$this->adding_traces($new_product_id);

			// Adding Allergence
			$this->adding_allergence($new_product_id);
		}
		//-------------------------------------------------------------------------//
		return(array('categories_id'=>$this->input->post('categories_id'),'subcategories_id'=>$this->input->post('subcategories_id')));
	}

	function add_product_clone($clone_of_id){
		//Fetching Information about the products of which clone is to be add
		$product_info = $this->db->get_where('products',array('id'=>$clone_of_id))->result_array();
		if($product_info){

			$categories_id = $product_info['0']['categories_id'];
			$subcategories_id = $product_info['0']['subcategories_id'];
			$product_infos	= $product_info['0'];
			foreach ($product_infos as $key => $info){
				if($key != 'id')
				$add_product_data[$key]	= $info;
			}

			$add_product_data['proname'] = $add_product_data['proname']." - CLONE";

			if($add_product_data['direct_kcp'] == 0){
				$add_product_data['semi_product'] = 0;
			}
			/* $add_product_data = array(
					'id'=>'',
					'company_id' => $product_info['0']['company_id'],
					'categories_id'=> $categories_id,
					'subcategories_id'=> $subcategories_id,
					'pro_art_num'=> $product_info['0']['pro_art_num'],
					'proname' => 'Clone '.$product_info['0']['proname'],
					'prodescription' => $product_info['0']['prodescription'],
					'image'=> $product_info['0']['image'],
					'min_amount'=> $product_info['0']['min_amount'],
					'max_amount'=> $product_info['0']['max_amount'],
					'old_price' => $product_info['0']['old_price'],
					'old_content_type' => $product_info['0']['old_content_type'],
					'sell_product_option'=> $product_info['0']['sell_product_option'],
					'price_per_unit'=> $product_info['0']['price_per_unit'],
					'price_per_person'=> $product_info['0']['price_per_person'],
					'price_weight'=> $product_info['0']['price_weight'],
					'weight_unit'=> $product_info['0']['weight_unit'],
					'pro_display'=> $product_info['0']['pro_display'],
					'image_display'=> $product_info['0']['image_display'],
					'procreated'=> date('Y-m-d',time()),
					'type'=> $product_info['0']['type'],
					'discount'=> $product_info['0']['discount'],
					'discount_person'=> $product_info['0']['discount_person'],
					'discount_wt'=> $product_info['0']['discount_wt'],
					'status'=> $product_info['0']['status'],
					'custom_group_order'=> $product_info['0']['custom_group_order'],
					'allow_upload_image'=> $product_info['0']['allow_upload_image'],
					'allday_availability' => $product_info['0']['allday_availability'],
					'availability' => $product_info['0']['availability'],
					'advance_payment' => $product_info['0']['advance_payment'],
					'clone_of' => $clone_of_id,
					'available_after' => $product_info['0']['available_after'],
					'ingredients' => $product_info['0']['ingredients'],
					'allergence' => $product_info['0']['allergence'],
					'traces_of' => $product_info['0']['traces_of'],
					'recommend' => $product_info['0']['recommend']
			); */

			//Inserting data in products table
			$query=$this->db->insert('products', $add_product_data);
			$new_product_id=$this->db->insert_id();//method to get id of last inserted row//

			if ($new_product_id)
			{
				$this->db->where(array('id'=>$new_product_id));
				$this->db->update('products',array('procreated' => date('Y-m-d'),'proupdated'=>'0000-00-00'));
			}

			//Copying image
			if($product_info['0']['image'] != ''){
				$extension = pathinfo($product_info['0']['image'], PATHINFO_EXTENSION);
				$imagename = clean_pdf(pathinfo($product_info['0']['image'], PATHINFO_FILENAME));
				$image_name = $new_product_id.'_'.'Clone_'.$imagename.'.'.$extension;

				$image = file_get_contents(base_url().'assets/cp/images/product/'.$product_info['0']['image']);
				file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$image_name, $image);
				$update_product_data['image'] = $image_name;
				$this->db->where('id',$new_product_id);
				$this->db->update('products', $update_product_data);
			}

			//Inserting data in products_labeler table
			$labeler = $this->db->get_where('products_labeler', array('product_id' => $clone_of_id))->result_array();

			$insert_label_data['product_id'] =	$new_product_id;
			if(!empty($labeler)){
				$labeler	= $labeler['0'];
				foreach ($labeler as $key => $label){
					if(($key != 'id') && ($key != 'product_id'))
						$insert_label_data[$key]	= $label;
				}
				$this->db->insert('products_labeler', $insert_label_data);
			}
			//Inserting data in products_discount table
			//if($product_info['0']['discount'] == 'multi'){//Comment or Add for all discount|discount_person|discount_wt also
				$get_product_discount_info = $this->db->get_where('products_discount',array('products_id' => $clone_of_id ))->result_array();
				if($get_product_discount_info){
					foreach($get_product_discount_info as $values){
						$insert_array = array(
								'products_id' => $new_product_id,
								'quantity' => $values['quantity'],
								'discount_per_qty' => $values['discount_per_qty'],
								'price_per_qty' => $values['price_per_qty'],
								'type' => $values['type']
						);
						$this->db->insert('products_discount',$insert_array);
					}
				}
			//}

			//Inserting data in groups_products table
			$get_product_group_info = $this->db->get_where('groups_products',array('products_id' => $clone_of_id ))->result_array();
			if($get_product_group_info){
				foreach($get_product_group_info as $values){
					$insert_array = array(
							'products_id' => $new_product_id,
							'groups_id' => $values['groups_id'],
							'attribute_name' => $values['attribute_name'],
							'attribute_value' => $values['attribute_value'],
							'type' => $values['type']
					);
					$this->db->insert('groups_products',$insert_array);
				}
			}

			//Inserting data in groups_order table
			$get_product_group_order_info = $this->db->get_where('groups_order',array('products_id' => $clone_of_id ))->result_array();
			if($get_product_group_order_info){
				foreach($get_product_group_order_info as $values){
					$insert_array = array(
							'company_id' => $values['company_id'],
							'products_id' => $new_product_id,
							'group_id' => $values['group_id'],
							'order_display' => $values['order_display'],
							'type' => $values['type']
					);
					$this->db->insert('groups_order',$insert_array);
				}
			}

			/**
			 * Adding Ingredients
			 */
			$ingredients = $this->get_product_ingredients($clone_of_id);
			if(!empty($ingredients)){
				foreach ($ingredients as $ingredient){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $ingredient->kp_id,
							'ki_id' => $ingredient->ki_id,
							'prefix' => $ingredient->prefix,
							'ki_name' => $ingredient->ki_name,
							'display_order' => $ingredient->display_order,
							'kp_display_order'=>$ingredient->kp_display_order,
							'date_added' => date('Y-m-d H:i:s'),
							'is_obs_ing' => $ingredient->is_obs_ing,
							'have_all_id' => $ingredient->have_all_id
					);
					$this->db->insert('products_ingredients', $insert_array);
				}
			}

			/**
			 * Adding Traces
			 */
			$traces = $this->get_product_traces($clone_of_id);
			if(!empty($traces)){
				foreach ($traces as $trace){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $trace->kp_id,
							'kt_id' => $trace->kt_id,
							'prefix' => $trace->prefix,
							'kt_name' => $trace->kt_name,
							'display_order' => $trace->display_order,
							'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_traces', $insert_array);
				}
			}

			/**
			 * Adding Allergence
			 */
			$allergence = $this->get_product_allergence($clone_of_id);
			if(!empty($allergence)){
				foreach ($allergence as $allg){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $allg->kp_id,
							'ka_id' => $allg->ka_id,
							'prefix' => $allg->prefix,
							'ka_name' => $allg->ka_name,
							'display_order' => $allg->display_order,
							'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_allergence', $insert_array);
				}
			}

			/**
			 * Adding sub-allergence
			 */
			$sub_allergence = $this->get_product_sub_allergence($clone_of_id);
			if(!empty($sub_allergence)){
				foreach ($sub_allergence as $allg){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $allg->kp_id,
							'parent_ka_id' => $allg->parent_ka_id,
							'sub_ka_id' => $allg->sub_ka_id,
							'sub_ka_name' => $allg->sub_ka_name,
							'display_order' => $allg->display_order,
							'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('product_sub_allergence', $insert_array);
				}
			}

			$this->db->where('obs_pro_id', $clone_of_id);
			$fdd_quants = $this->db->get('fdd_pro_quantity')->result();
			if(!empty($fdd_quants)){
				foreach ($fdd_quants as $fdd_quant){
					$insert_array_new = array(
					'is_obs_product' => $fdd_quant->is_obs_product,
					'obs_pro_id' => $new_product_id,
					'fdd_pro_id' => $fdd_quant->fdd_pro_id,
					'quantity'=> $fdd_quant->quantity,
					'unit'=>$fdd_quant->unit,
					'semi_product_id'=>$fdd_quant->semi_product_id,
					'fixed'=>$fdd_quant->fixed
					);
					
					$this->db->insert('fdd_pro_quantity', $insert_array_new);
				}
			}

			//-------------------------------------------------------------------------//
			return(array('categories_id'=>$categories_id,'subcategories_id'=>$subcategories_id));
		}else{
			return(array('error'=>'Could not made clone. Try again'));
		}
	}


	function pws_clone($clone_of_id=0){
		$product_info = $this->db->get_where('products',array('id'=>$clone_of_id))->result_array();
		if(!empty($product_info)){
			$product_infos	= $product_info['0'];
			foreach ($product_infos as $key => $info){
				if($key != 'id')
					$add_product_data[$key]	= $info;
			}
			$query=$this->db->insert('products', $add_product_data);
			$new_prod_id = $this->db->insert_id();
			if ($new_prod_id)
			{
				$this->db->where(array('id'=>$new_prod_id));
				$this->db->update('products',array('procreated' => date('Y-m-d'),'proupdated'=>'0000-00-00'));
			}

			$this->db->select('obs_pro_id');
			$this->db->where(array('products.company_id !='=> $this->company_id,'fdd_pro_quantity.fdd_pro_id'=>$clone_of_id,'fdd_pro_quantity.is_obs_product'=>1));
			$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
			$produt_det = $this->db->get('fdd_pro_quantity')->result_array();
			if(!empty($produt_det))
			{
				foreach ($produt_det as $prod_val)
				{
					$this->db->where(array('obs_pro_id'=>$prod_val['obs_pro_id'],'is_obs_product'=>1,'fdd_pro_quantity.fdd_pro_id'=>$clone_of_id));
					
					$this->db->update('fdd_pro_quantity',array('fdd_pro_id'=>$new_prod_id));
				}
			}


			$this->db->select('product_id');
			$this->db->where(array('products.company_id !='=> $this->company_id,'products_ingredients.kp_id'=>$clone_of_id));
			$this->db->join('products','products.id = products_ingredients.product_id');
			$produt_ing = $this->db->get('products_ingredients')->result_array();
			if(!empty($produt_ing))
			{
				foreach ($produt_ing as $prod_ing_val)
				{
					$this->db->where(array('product_id'=>$prod_ing_val['product_id'],'is_obs_ing'=>1,'products_ingredients.kp_id'=>$clone_of_id));
					$this->db->update('products_ingredients',array('products_ingredients.kp_id'=>$new_prod_id));
				}
			}

			$this->db->select('company_id');
			$comp_det = $this->db->get_where('products_pending',array('company_id !=' => $this->company_id,'product_id'=>$clone_of_id))->result_array();
			if (!empty($comp_det))
			{
				foreach ($comp_det as $comp_val)
				{
					$this->db->where(array('company_id ' => $comp_val['company_id'],'product_id'=>$clone_of_id));
					$this->db->update('products_pending',array('product_id'=>$new_prod_id));
				}
			}

			$this->db->where(array('obs_pro_id'=>$clone_of_id));
			$contact_det = $this->db->get('contacted_via_mail')->result_array();
			if (!empty($contact_det))
			{
				$this->db->insert('contacted_via_mail',array('obs_pro_id'=>$new_prod_id));
			}
		}
	}

	function add_product_shared($shared_id, $product_update){
		//Fetching Information about the products of which clone is to be add
		$product_info = $this->db->get_where('products',array('id'=>$shared_id))->result_array();

		if($product_info){

			$categories_id = $product_info['0']['categories_id'];
			$subcategories_id = $product_info['0']['subcategories_id'];

			$add_product_data = array(
					'parent_proid' => $product_update['parent_proid'],
					'company_id' => $product_update['company_id'],
					'categories_id'=> $product_update['categories_id'],
					'subcategories_id'=> $product_update['subcategories_id'],
					'pro_art_num'=> $product_info['0']['pro_art_num'],
					'proname' => $product_info['0']['proname'],
					'prodescription' => $product_info['0']['prodescription'],
					'image'=> $product_info['0']['image'],
					'sell_product_option'=> $product_info['0']['sell_product_option'],
					'pro_display'=> $product_info['0']['pro_display'],
					'image_display'=> "1",
					'procreated'=> date('Y-m-d',time()),
					'type'=> $product_info['0']['type'],
					'status'=> $product_info['0']['status'],
					'direct_kcp' => $product_update['direct_kcp'],
					'semi_product' => 0,
					'recipe_method' => $product_info['0']['recipe_method'],
					'recipe_weight' => $product_info['0']['recipe_weight']
			);

			//Inserting data in products table
			$query=$this->db->insert('products', $add_product_data);
			$new_product_id=$this->db->insert_id();//method to get id of last inserted row//

			//Copying image
			if($product_info['0']['image'] != ''){
				$extension = pathinfo($product_info['0']['image'], PATHINFO_EXTENSION);
				$imagename = clean_pdf($product_info['0']['proname']);
				$image_name = $new_product_id.'_'.'Clone_'.$imagename.'.'.$extension;
				$image = file_get_contents(base_url().'assets/cp/images/product/'.$product_info['0']['image']);
				file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$image_name, $image);
				$update_product_data['image'] = $image_name;
				$this->db->where('id',$new_product_id);
				$this->db->update('products', $update_product_data);
			}

			/**
			 * Adding Ingredients
			 */
			$ingredients = $this->get_product_ingredients($shared_id);
			if(!empty($ingredients)){
				foreach ($ingredients as $ingredient){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $ingredient->kp_id,
							'ki_id' => $ingredient->ki_id,
							'prefix' => $ingredient->prefix,
							'ki_name' => $ingredient->ki_name,
							'display_order' => $ingredient->display_order,
							'kp_display_order'=>$ingredient->kp_display_order,
							'date_added' => date('Y-m-d H:i:s'),
							'is_obs_ing' => $ingredient->is_obs_ing,
							'have_all_id' => $ingredient->have_all_id
					);
					$this->db->insert('products_ingredients', $insert_array);
				}
			}

			/**
			 * Adding Traces
			 */
			$traces = $this->get_product_traces($shared_id);
			if(!empty($traces)){
				foreach ($traces as $trace){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $trace->kp_id,
							'kt_id' => $trace->kt_id,
							'prefix' => $trace->prefix,
							'kt_name' => $trace->kt_name,
							'display_order' => $trace->display_order,
							'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_traces', $insert_array);
				}
			}

			/**
			 * Adding Allergence
			 */
			$allergence = $this->get_product_allergence($shared_id);
			if(!empty($allergence)){
				foreach ($allergence as $allg){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $allg->kp_id,
							'ka_id' => $allg->ka_id,
							'prefix' => $allg->prefix,
							'ka_name' => $allg->ka_name,
							'display_order' => $allg->display_order,
							'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_allergence', $insert_array);
				}
			}

			/**
			 * Adding sub-allergence
			 */
			$sub_allergence = $this->get_product_sub_allergence($shared_id);
			if(!empty($sub_allergence)){
				foreach ($sub_allergence as $allg){
					$insert_array = array(
							'product_id' => $new_product_id,
							'kp_id' => $allg->kp_id,
							'parent_ka_id' => $allg->parent_ka_id,
							'sub_ka_id' => $allg->sub_ka_id,
							'sub_ka_name' => $allg->sub_ka_name,
							'display_order' => $allg->display_order,
							'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('product_sub_allergence', $insert_array);
				}
			}

			$this->db->where('obs_pro_id', $shared_id);
			$fdd_quants = $this->db->get('fdd_pro_quantity')->result();
			if(!empty($fdd_quants)){
				foreach ($fdd_quants as $fdd_quant){
					$insert_array_new = array(
							'is_obs_product' => $fdd_quant->is_obs_product,
							'obs_pro_id' => $new_product_id,
							'fdd_pro_id' => $fdd_quant->fdd_pro_id,
							'quantity'=> $fdd_quant->quantity,
							'unit'=>$fdd_quant->unit,
							'semi_product_id'=>$fdd_quant->semi_product_id,
							'fixed'=>$fdd_quant->fixed
					);
					
					$this->db->insert('fdd_pro_quantity', $insert_array_new);
				}
			}

			return(array('categories_id'=>$categories_id,'subcategories_id'=>$subcategories_id,'last_inserted_id'=>$new_product_id,'from_company'=>$product_info[0]['company_id']));
		}
		else{
			return(array('error'=>'Could not made clone. Try again'));
		}
	}

	function update_checkbox(){
		$key=$this->input->post('key');
		$value=$this->input->post('value');
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('products',array($key=>$value));
		echo $this->db->last_query();
	}

	function get_ordered_products($on = NULL, $from = NULL, $to = NULL, $dontShowRemark = 0)
	{
		$products = array();

		if($on)
		{
		   $this->db->where( array('order_pickupdate'=>$on,'delivery_date'=>'0000-00-00') );
		   $this->db->or_where( 'order_pickupdate','0000-00-00' );
		   $this->db->where( 'delivery_date',$on );
		   $this->db->where("( payment_via_paypal = '0'  OR (payment_via_paypal != '0' AND payment_status = '1') )");
		   $orders = $this->db->get('orders')->result();

		   //echo $this->db->last_query();
		}
		else
		if($from && $to)
		{

		   $this->db->where( array('order_pickupdate BETWEEN \''.$from.'\' AND '=>$to,'delivery_date'=>'0000-00-00') );
		   $this->db->or_where( 'delivery_date BETWEEN \''.$from.'\' AND ',$to );
		   $this->db->where( 'order_pickupdate','0000-00-00' );
		   $this->db->where("( payment_via_paypal = '0'  OR (payment_via_paypal != '0' AND payment_status = '1') )");
		   $orders = $this->db->get('orders')->result();

		   //echo $this->db->last_query();
		}
		if( !empty($orders) )
		{
		   foreach($orders as $ord)
		   if($ord->company_id == $this->company_id)
		   {
			   $order_id = $ord->id;

			   $this->db->select('order_details.*,products.proname,products.price_per_unit,products.price_per_person,products.price_weight,products.weight_unit');
			   $this->db->join('products','order_details.products_id = products.id','left');
			   $this->db->where( 'orders_id',$ord->id );
		       $prod_order_details = $this->db->get('order_details')->result();

			   if(!empty($prod_order_details))
			   {
			      foreach($prod_order_details as $K=>$det)
				  {
				       $product_id = $det->products_id;
					   $content_type = $det->content_type;
					   $quantity = $det->quantity;
					   $total = $det->total;

					   $found_id_arr = false;

					   if(!empty($products))
					   {
						  foreach($products as $key=>$prod)
						  {
							  if(isset($prod->products_id))
							  if($prod->products_id == $product_id && $prod->content_type == $content_type)
							  {
							     $products[$key]->counter = $products[$key]->counter+1;
								 $products[$key]->quantity = $products[$key]->quantity+$quantity;
								 $products[$key]->total = $products[$key]->total+$total;
								 if(!$dontShowRemark && !in_array($prod->pro_remark,$products[$key]->pro_remark_arr))
								 	$products[$key]->pro_remark_arr[] = $prod->pro_remark;
								 $found_id_arr = true;
								 break;
							  }
						  }

						  if(!$found_id_arr)
						  {
						     $prod_order_details[$K]->counter = 1;
							 $prod_order_details[$K]->quantity = $quantity;
							 if(!$dontShowRemark)
							 	$prod_order_details[$K]->pro_remark_arr[] = $det->pro_remark;
							 $products[] = $prod_order_details[$K];
						  }
					   }
					   else
					   {
					      $prod_order_details[$K]->counter = 1;
						  $prod_order_details[$K]->quantity = $quantity;
						  if(!$dontShowRemark)
						  	$prod_order_details[$K]->pro_remark_arr[] = $det->pro_remark;
					      $products[] = $prod_order_details[$K];
					   }
					}
				}
			}
		}

		return $products;
	}

	/**
	 * @name get_ordered_products_list
	 * @property This Function is used for generating labels of per product separately
	 * @access public
	 * @author Monu Singh Yadav <monuyadav@cedcoss.com>
	 *
	 */
	function get_ordered_products_list($on = NULL, $from = NULL, $to = NULL,$prod=NULL)
	{
		if($on)
		{
		   $this->db->where( array('order_pickupdate'=>$on,'delivery_date'=>'0000-00-00') );
		   $this->db->or_where( 'order_pickupdate','0000-00-00' );
		   $this->db->where( 'delivery_date',$on );

		   $orders = $this->db->get('orders')->result();
		   $this->db->select('company_name');
		   $this->db->where('id',$this->company_id);
		   $obs_comp_name=$this->db->get('company')->row_array();


		}
		elseif($from && $to)
		{
		   $this->db->where( array('order_pickupdate BETWEEN \''.$from.'\' AND '=>$to,'delivery_date'=>'0000-00-00') );
		   $this->db->or_where( 'delivery_date BETWEEN \''.$from.'\' AND ',$to );
		   $this->db->where( 'order_pickupdate','0000-00-00' );

		   $orders = $this->db->get('orders')->result();
		   $this->db->select('company_name');
		   $this->db->where('id',$this->company_id);
		   $obs_comp_name=$this->db->get('company')->row_array();
		   //echo $this->db->last_query();
		}
		if(!empty($orders))
		{
		   foreach($orders as $ord)
		   {
			   	if($ord->company_id == $this->company_id)
			    {
				   $order_id = $ord->id;

				   $this->db->select('order_details.*,orders.order_pickupdate,orders.order_pickuptime,orders.order_remarks,orders.clients_id,products.proname,products.price_per_unit,products.price_per_person,products.price_weight,products.weight_unit');
				   $this->db->join('products','order_details.products_id = products.id','left');
				   $this->db->join('orders','order_details.orders_id = orders.id','left');
				   $this->db->where( 'order_details.products_id',$prod);
				   $this->db->where( 'orders_id',$ord->id );
			      $product = $this->db->get('order_details')->result();
			      if(!empty($product))
			      {

			      	foreach ($product as $key => $value)
			      	{
			      		# code...
			      		$this->db->select('firstname_c,lastname_c');
			      		$this->db->where('id',$value->clients_id);
			      		$qnty_unit='';
			      		if($value->content_type==1)
			      		{
			      			$qnty_unit = ' gr.';
			      		}
			      		elseif ($value->content_type==2)
			      		{
			      			# code...
			      			$qnty_unit = ' person';
			      		}
			      		$obs_prod_query=$this->db->get('clients')->row_array();
			      		$product[$key]->client_name=$obs_prod_query['firstname_c'].' '.$obs_prod_query['lastname_c'];
			      		$product[$key]->com_name=$obs_comp_name['company_name'];
			      		$product[$key]->qty_unit=$qnty_unit;
			      		$products[]=$product[$key];
			      	}
			      }
			    }
		   }
		}
		return $products;
	}


    /**
	 * @name get_ordered_products_list
	 * @property This Function is used for generating excel of per product separately
	 * @access public
	 * @author Monu Singh Yadav <monuyadav@cedcoss.com>
	 *
	 */
	function get_ordered_products_list_excel($on = NULL, $from = NULL, $to = NULL,$prod=NULL)
	{
		if($on)
		{
		   $this->db->where( array('order_pickupdate'=>$on,'delivery_date'=>'0000-00-00') );
		   $this->db->or_where( 'order_pickupdate','0000-00-00' );
		   $this->db->where( 'delivery_date',$on );
		   $this->db->order_by('id','DESC');
		   $orders = $this->db->get('orders')->result();
		}
		elseif($from && $to)
		{
		   $this->db->where( array('order_pickupdate BETWEEN \''.$from.'\' AND '=>$to,'delivery_date'=>'0000-00-00') );
		   $this->db->or_where( 'delivery_date BETWEEN \''.$from.'\' AND ',$to );
		   $this->db->where( 'order_pickupdate','0000-00-00' );
		   $this->db->order_by('id','DESC');
		   $orders = $this->db->get('orders')->result();
		}
		if( !empty($orders) )
		{
		    $this->db->select('groups_id')->distinct();
	      	$this->db->where('products_id',$prod);
	      	$obs_query=$this->db->get('groups_products')->result_array();
	        $obs_query2=array();
	        $products=array();
	      	if(!empty($obs_query))
	      	{
		      	foreach ($obs_query as $obs => $query)
		      	{
		      		# code...
		      		$this->db->select('group_name');
		      	    $this->db->where('id',$query['groups_id']);
		      		$obs_query1=$this->db->get('groups')->row_array();
		      	    if(!empty($obs_query1))
		      	    {
		      	    	$obs_query2[]=$obs_query1['group_name'];
		      	    }
		      	}
	      	}
	       $products[]=$obs_query2;
		   foreach($orders as $ord)
		   {
			   	if($ord->company_id == $this->company_id)
			    {
				   $order_id = $ord->id;

				   $this->db->select('order_details.*,orders.clients_id,products.proname');
				   $this->db->join('products','order_details.products_id = products.id','left');
				   $this->db->join('orders','order_details.orders_id = orders.id','left');
				   $this->db->where( 'order_details.products_id',$prod);
				   $this->db->where( 'orders_id',$ord->id );
			      $product = $this->db->get('order_details')->result();
			      if(!empty($product))
			      {


			      	foreach ($product as $key => $value)
			      	{
			      		# code...
			      		$this->db->select('firstname_c,lastname_c');
			      		$this->db->where('id',$value->clients_id);
			      		$obs_prod_query=$this->db->get('clients')->row_array();
			      		$qnty_unit='';
			      		if($value->content_type==1)
			      		{
			      			$qnty_unit = ' gr.';
			      		}
			      		elseif ($value->content_type==2)
			      		{
			      			# code...
			      			$qnty_unit = ' person';
			      		}
			      		$product[$key]->client_name=$obs_prod_query['firstname_c'].' '.$obs_prod_query['lastname_c'];
			      		$product[$key]->qty_unit=$qnty_unit;
			      		$obs_grp_name=array();
			      		if(!empty($value->add_costs))
			      		{

			      			$obs_arr=explode('#', $value->add_costs);
			      			foreach ($obs_arr as $key1 => $value)
			      			{
			      				# code...
			      				$obs_arr_val=explode('_', $value);
			      				$obs_grp_name[$obs_arr_val[0]][]=$obs_arr_val[1];

			      			}
			      		}

			      		$product[$key]->related_option=$obs_grp_name;
			      		$products[]=$product[$key];
			      	}

			      }
			    }

		   }

		}
		return $products;

	}

	function get_supp_ordered_products($from = NULL, $to = NULL, $dontShowRemark = 0){
		$supp_products = array();
		$supp_arr = array();

		if($from && $to){
			$this->db->where( array('order_pickupdate BETWEEN \''.$from.'\' AND '=>$to,'delivery_date'=>'0000-00-00') );
			$this->db->or_where( 'delivery_date BETWEEN \''.$from.'\' AND ',$to );
			$this->db->where( 'order_pickupdate','0000-00-00' );
			$this->db->where("( payment_via_paypal = '0'  OR (payment_via_paypal != '0' AND payment_status = '1') )");
			$orders = $this->db->get('orders')->result();
		}

		if( !empty($orders) ){
			$this->db->distinct();
			$this->db->select('fdd_supplier_id');
			$all_supp_arr = $this->db->get_where('products',array('company_id'=>$this->company_id,'fdd_supplier_id !='=>0))->result();

			if(!empty($all_supp_arr)){
				$supp_ids = array();
				foreach ($all_supp_arr as $supp){
					$products = array();
					foreach($orders as $ord){
						if($ord->company_id == $this->company_id){
							$order_id = $ord->id;

							$this->db->select('order_details.*,products.proname,products.price_per_unit,products.price_per_person,products.price_weight,products.weight_unit');
							$this->db->join('products','order_details.products_id = products.id','left');
							$this->db->where( 'order_details.orders_id',$ord->id );
							$this->db->where( 'products.fdd_supplier_id',$supp->fdd_supplier_id );
							$prod_order_details = $this->db->get('order_details')->result();

							if(!empty($prod_order_details)){
								foreach($prod_order_details as $K=>$det){
									$product_id = $det->products_id;
									$content_type = $det->content_type;
									$quantity = $det->quantity;
									$total = $det->total;

									$found_id_arr = false;

									if(!empty($products)){
										foreach($products as $key=>$prod){
											if(isset($prod->products_id)){
												if($prod->products_id == $product_id && $prod->content_type == $content_type){
													$products[$key]->counter = $products[$key]->counter+1;
													$products[$key]->quantity = $products[$key]->quantity+$quantity;
													$products[$key]->total = $products[$key]->total+$total;
													if(!$dontShowRemark && !in_array($prod->pro_remark,$products[$key]->pro_remark_arr))
														$products[$key]->pro_remark_arr[] = $prod->pro_remark;
													$found_id_arr = true;
													break;
												}
											}
										}

										if(!$found_id_arr){
											$prod_order_details[$K]->counter = 1;
											$prod_order_details[$K]->quantity = $quantity;
											if(!$dontShowRemark)
												$prod_order_details[$K]->pro_remark_arr[] = $det->pro_remark;
											$products[] = $prod_order_details[$K];
										}
									}
									else{
										$prod_order_details[$K]->counter = 1;
										$prod_order_details[$K]->quantity = $quantity;
										if(!$dontShowRemark)
											$prod_order_details[$K]->pro_remark_arr[] = $det->pro_remark;
										$products[] = $prod_order_details[$K];
									}
								}
							}
						}
					}
					$supp_products[$supp->fdd_supplier_id] = $products;
					if(!empty($products))
						$supp_ids[] = $supp->fdd_supplier_id;
				}

				if(!empty($supp_ids)){
					
					$this->fdb->select('rs_id,rs_name');
					$this->fdb->where_in('rs_id',$supp_ids);
					$supp_arr = $this->fdb->get('real_suppliers')->result_array();

					
				}
			}

			$products = array();
			foreach($orders as $ord){
				if($ord->company_id == $this->company_id){
					$order_id = $ord->id;

					$this->db->select('order_details.*,products.proname,products.price_per_unit,products.price_per_person,products.price_weight,products.weight_unit');
					$this->db->join('products','order_details.products_id = products.id','left');
					$this->db->where( 'order_details.orders_id',$ord->id );
					$this->db->where( 'products.fdd_supplier_id',0 );
					$prod_order_details = $this->db->get('order_details')->result();

					if(!empty($prod_order_details)){
						foreach($prod_order_details as $K=>$det){
							$product_id = $det->products_id;
							$content_type = $det->content_type;
							$quantity = $det->quantity;
							$total = $det->total;

							$found_id_arr = false;

							if(!empty($products)){
								foreach($products as $key=>$prod){
									if(isset($prod->products_id)){
										if($prod->products_id == $product_id && $prod->content_type == $content_type){
											$products[$key]->counter = $products[$key]->counter+1;
											$products[$key]->quantity = $products[$key]->quantity+$quantity;
											$products[$key]->total = $products[$key]->total+$total;
											if(!$dontShowRemark && !in_array($prod->pro_remark,$products[$key]->pro_remark_arr))
												$products[$key]->pro_remark_arr[] = $prod->pro_remark;
											$found_id_arr = true;
											break;
										}
									}
								}

								if(!$found_id_arr){
									$prod_order_details[$K]->counter = 1;
									$prod_order_details[$K]->quantity = $quantity;
									if(!$dontShowRemark)
										$prod_order_details[$K]->pro_remark_arr[] = $det->pro_remark;
									$products[] = $prod_order_details[$K];
								}
							}
							else{
								$prod_order_details[$K]->counter = 1;
								$prod_order_details[$K]->quantity = $quantity;
								if(!$dontShowRemark)
									$prod_order_details[$K]->pro_remark_arr[] = $det->pro_remark;
								$products[] = $prod_order_details[$K];
							}
						}
					}
				}
			}
			$supp_products[0] = $products;

			if(!empty($products))
				$supp_arr[] = array('rs_id'=>0,'rs_name'=>'-');
		}

		return array('supp_det'=>$supp_arr,'prod_det'=>$supp_products);
	}

	function insert_product( $insert_arr = array() )
	{
		if( empty($insert_arr) )
		  return false;

		$this->db->insert('products', $insert_arr );
		return $this->db->insert_id();
	}

	function create_slug($string)
	{
		$str = strtolower(trim($string));
		$str = preg_replace("/[^a-z0-9-]/", "-", $str);
		$str = preg_replace("/-+/", "-", $str);
		$str = rtrim($str, "-");

		return $str;
	}

	function get_company_product_detail($company_id)
	{
		$this->db->where('company_id',$company_id);
		//$this->db->where('categories_id',$category_id);
		$query=$this->db->get('products')->result_array();
		return $query;
	}

	function insert_pro($insert_arr)
	{
		//print_r($insert_arr); die();
		$this->db->insert('products',$insert_arr);
		return $this->db->insert_id();
	}

	function check_product_exist($params = array())
	{
		$query = array();
		if(!empty($params)){
			foreach($params as $key => $value){
				$this->db->where($key,$value);
			}
			$query=$this->db->get('products')->row_array();
		}
		return $query;
	}

	function update_pro($params,$id)
	{
		$this->db->where('id', $id);
		$this->db->update('products', $params);
		return true;
	}

	/**
	 * This model function is for fetching products Ingredients
	 * @param number $product_id
	 * @param number $k_type
	 * @return array $ingredients
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_ingredients($product_id = 0, $k_type = 1){
		$ingredients = array();
		// if($product_id){
		// 	$this->db->order_by('kp_display_order', 'ASC');
		// 	$this->db->order_by('display_order', 'ASC');

		// 	//if($k_type){
		// 		$ingredients = $this->db->get_where('products_ingredients', array('product_id' => $product_id))->result();
		// //	}else{
		// 	//	$ingredients = $this->db->get_where('i_products_ingredients', array('product_id' => $product_id))->result();
		// 	//}
		// }
		return $ingredients;
	}

	/**
	 * This model function is for fetching products Ingredients Vetten
	 * @param number $product_id
	 * @param number $k_type
	 * @return array $ingredients
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_ingredients_vetten($product_id = 0, $k_type = 1){
		$ingredients = array();
		// if($product_id){
		// 	$this->db->order_by('kp_display_order', 'ASC');
		// 	$this->db->order_by('display_order', 'ASC');

		// 	$ingredients = $this->db->get_where('products_ingredients_vetten', array('product_id' => $product_id))->result();
		// }
		return $ingredients;
	}

	/**
	 * This model function is for fetching products Ingredients Additives
	 * @param number $product_id
	 * @param number $k_type
	 * @return array $ingredients
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_addtives($product_id = 0, $k_type = 1){
		$ingredients = array();
		// 
		
		return $ingredients;
	}

	/**
	 * This model function is for fetching products distict Ingredients
	 * @param int $product_id ID of product
	 * @return array $ingredients
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_ingredients_dist($product_id = 0, $k_type = 1){
		$ingredients = array();
		// if($product_id){
		// 	$ingredients = $this->db->query(
		// 		"SELECT ki_id, prefix, ki_name, have_all_id, aller_type, aller_type_fr, aller_type_dch, allergence, allergence_fr, allergence_dch, sub_allergence, sub_allergence_fr, sub_allergence_dch FROM
		// 			(SELECT a.ki_id,a.prefix,a.ki_name,a.kp_display_order, a.display_order,a.have_all_id,a.aller_type,a.aller_type_fr,a.aller_type_dch,a.allergence,a.allergence_fr,a.allergence_dch,a.sub_allergence,a.sub_allergence_fr,a.sub_allergence_dch
		// 			FROM products_ingredients a
		// 			WHERE product_id = ".$product_id."
		// 			AND ((display_order=1 AND is_obs_ing=1) OR (display_order !=0 AND is_obs_ing=0) OR (0 = (SELECT COUNT(b.ki_id) FROM products_ingredients b WHERE b.product_id = ".$product_id." AND a.kp_id = b.kp_id AND b.ki_id != 0)+(SELECT COUNT(c.ki_id) FROM products_ingredients_vetten c WHERE c.product_id = ".$product_id." AND a.kp_id = c.kp_id AND c.ki_id != 0)+(SELECT COUNT(d.ki_id) FROM products_additives d WHERE d.product_id = ".$product_id." AND a.kp_id = d.kp_id AND d.ki_id != 0)))
		// 			ORDER BY kp_display_order ASC) AS INGTAB
		// 			ORDER BY kp_display_order ASC, display_order ASC")->result();
		// }
		// print_r($ingredients); die;
		return $ingredients;
	}

	/**
	 * This model function is for fetching products distict Ingredients Vetten
	 * @param number $product_id
	 * @return array $vetten
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_ingredients_vetten_dist($product_id = 0, $k_type = 1){
		$vetten = array();
		// if($product_id){
		// 	$vetten = $this->db->query(
		// 		"SELECT * FROM (SELECT *
		// 		FROM products_ingredients_vetten
		// 		WHERE product_id = ".$product_id." ORDER BY kp_display_order ASC) AS VETTAB
		// 		GROUP BY ki_name
		// 		ORDER BY kp_display_order ASC, display_order ASC")->result();
		// }
		return $vetten;
	}

	/**
	 * This model function is for fetching products distict Ingredients Additives
	 * @param number $product_id
	 * @return array $additives
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_additives_dist($product_id = 0, $k_type = 1){
		$add_name = 'add_name'.$this->lang_u;
		$additives = array();
		// if($product_id){
		// 	$additives = $this->db->query(
		// 		"SELECT * FROM (SELECT products_additives.*, additives.".$add_name."
		// 		FROM products_additives
		// 		JOIN additives ON additives.add_id = products_additives.add_id
		// 		AND products_additives.product_id = ".$product_id." ORDER BY kp_display_order ASC) AS ADDTAB
		// 		GROUP BY ki_name
		// 		ORDER BY kp_display_order ASC, display_order ASC")->result();
		// }
		return $additives;
	}

	/**
	 * This model function is for fetching products Traces
	 * @param int $product_id ID of product
	 * @return array $traces
	 */
	function get_product_traces($product_id = 0, $k_type = 1){
		$traces = array();
		// if($product_id){
		// 	$this->db->order_by('display_order', 'ASC');
		// //	if($k_type){
		// 		$traces = $this->db->get_where('products_traces', array('product_id' => $product_id))->result();
		// //	}else{
		// //		$traces = $this->db->get_where('i_products_traces', array('product_id' => $product_id))->result();
		// //	}
		// }
		return $traces;
	}

	/**
	 * This model function is for fetching products Allergence
	 * @param int $product_id ID of product
	 */
	function get_product_allergence($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			$this->db->order_by('display_order', 'ASC');
		//	if($k_type){
				$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
		//	}else{
		//		$allergence = $this->db->get_where('i_products_allergence', array('product_id' => $product_id))->result();
		//	}
		}
		return $allergence;
	}

	/**
	 * This model function is for fetching products Allergence
	 * @param number $product_id
	 * @param number $k_type
	 * @return array $allergence
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_sub_allergence($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			$this->db->order_by('display_order', 'ASC');
			$allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	/**
	 * This model function is for fetching distinct products Allergence distinct
	 * @param number $product_id
	 * @param number $k_type
	 * @return array $allergence
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_allergence_dist($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			//$this->db->select('ka_id');
			$this->db->order_by('display_order', 'ASC');
		//	$this->db->distinct('ka_id');
			$this->db->group_by('ka_id');

			$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();

		}
		return $allergence;
	}

	/**
	 * This model function is for fetching products sub Allergence distinct
	 * @param number $product_id
	 * @param number $k_type
	 * @return array $allergence
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_sub_allergence_dist($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			$this->db->order_by('display_order', 'ASC');
			$this->db->group_by('sub_ka_id');
			$allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	/**
	 * Function to add Ingredients
	 * @param number $new_product_id
	 */
	function adding_ingredients($new_product_id,$sel_lang_upd = '_dch'){
		$all_ings = $this->input->post('ingredients');
		if($all_ings != ''){
			$ingredients = explode(':::',$all_ings);
			if(!empty($ingredients)){
				$enbr_setting = $this->get_enbr_status($this->company_id);

				$kp_array = array();
				$pro_order = 0;
				$disp_order = 1;
				$check_repeat = array();
				$ing_vet_insert = array();
				$addi_insert = array();
				$ingre_insert = array();
				foreach ($ingredients as $key => $ingredient){
					$ing_arr = explode('#',$ingredient);

					$insert_array = array();
					if(!empty($ing_arr) && (count($ing_arr) == 5 || count($ing_arr) == 10) ){

						if($ing_arr[4] == 1 ){
							if(in_array($ing_arr[3].'#1', $kp_array)){
								$pro_order = array_search($ing_arr[3].'#1', $kp_array);
							}else{
								$kp_array[] = $ing_arr[3].'#1';
								$pro_order = array_search($ing_arr[3].'#1', $kp_array);
								$disp_order = 1;
							}
						}
						else{
							if(in_array($ing_arr[3].'#0', $kp_array)){
								$pro_order = array_search($ing_arr[3].'#0', $kp_array);
							}else{
								$kp_array[] = $ing_arr[3].'#0';
								$pro_order = array_search($ing_arr[3].'#0', $kp_array);
								$disp_order = 1;
							}
						}

						$display_name = ( (isset($ing_arr[1]))?addslashes(stripslashes($ing_arr[1])):'' );
						$ki_id = $ing_arr[2];

						if($ing_arr[2] != 0){
							$enbr_result = $this->get_e_current_nbr($ing_arr[2],$enbr_setting['enbr_status'],$sel_lang_upd);
							if(!empty($enbr_result)){
								$ki_id = $enbr_result['ki_id'];
								$display_name = $enbr_result['ki_name'];
							}
						}

						if($display_name != '')
							$ing_name = $this->get_ing_name_mod(stripslashes($display_name));
						else
							$ing_name = $display_name;

						$ingre_insert[$key] = array(
								'product_id' => $new_product_id,
								'kp_id' => ( (is_numeric($ing_arr[3]))?$ing_arr[3]:0 ),
								'ki_id' => $ki_id,
								'prefix' => ( (isset($ing_arr[0]))?addslashes(stripslashes($ing_arr[0])):'' ),
								'ki_name' => $ing_name,
								'is_obs_ing' => $ing_arr[4],
								'display_order' => $disp_order,
								'kp_display_order'=> $pro_order+1,
								'date_added' => date('Y-m-d H:i:s'),
								'aller_type' => '0',
								'aller_type_fr' => '0',
								'aller_type_dch' => '0',
								'allergence' => '0',
								'allergence_fr' => '0',
								'allergence_dch' => '0',
								// 'new_allergence'] => $ing_arr[9],
								'sub_allergence' => '0',
								'sub_allergence_fr' => '0',
								'sub_allergence_dch' => '0'
						);

						if( count($ing_arr) == 10 ){
							if( $_COOKIE['locale'] == 'en_US' ){
								$ingre_insert[$key]['aller_type'] 	  	= $ing_arr[6];
								$ingre_insert[$key]['allergence'] 	  	= $ing_arr[7];
								$ingre_insert[$key]['sub_allergence'] 	= $ing_arr[8];
							}
							if( $_COOKIE['locale'] == 'fr_FR' ){
								$ingre_insert[$key]['aller_type_fr'] 	= $ing_arr[6];
								$ingre_insert[$key]['allergence_fr'] 	= $ing_arr[7];
								$ingre_insert[$key]['sub_allergence_fr']= $ing_arr[8];
							}
							if( $_COOKIE['locale'] == 'nl_NL' ){
								$ingre_insert[$key]['aller_type_dch'] 	= $ing_arr[6];
								$ingre_insert[$key]['allergence_dch'] 	= $ing_arr[7];
								$ingre_insert[$key]['sub_allergence_dch']= $ing_arr[8];
							}
							// $ingre_insert['new_allergence'] = $ing_arr[9];
						}

						if($ing_arr[3] != 0 && $ing_arr[2] == 0 && $ing_arr[0] != '' && $ing_arr[1] != '(' && $ing_arr[1] != ')'){
							$this->db->where(array('obs_pro_id'=>$new_product_id,'fdd_pro_id'=>$ing_arr[3],'is_obs_product'=>$ing_arr[4]));
							$this->db->update('fdd_pro_quantity',array('product_prefix'=>$ing_arr[0]));
						}
						$disp_order++;

						if( $ki_id != '0' ){
							$this->db->select('ki_id');
							$res = $this->db->get_where('ingredients', array('ki_id' => $ki_id) )->row_array();

							if( empty($res) ){
								
								$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch');
								$query_ing = $this->fdb->get_where('ingredients', array( 'ing_id' => $ki_id ) )->row_array();

								if( $enbr_setting['enbr_status'] == 1 ){
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
												'ki_name'	 => $query_ing['ing_name'],
												'ki_name_fr' => $query_ing['ing_name_fr'],
												'ki_name_dch'=> $query_ing['ing_name_dch']
											);
									}
								}
								if( $enbr_setting['enbr_status'] == 2 ){
									$object = array(
												'ki_id' 	 => $ki_id,
												'ki_name'	 => $query_ing['ing_name'],
												'ki_name_fr' => $query_ing['ing_name_fr'],
												'ki_name_dch'=> $query_ing['ing_name_dch']
											);
								}
								$this->db->insert('ingredients', $object);
							}
						}
					}
				}

				if(!empty($ingre_insert))
					$this->db->insert_batch('products_ingredients', $ingre_insert);

				$this->update_have_all_id($new_product_id);
			}
		}
	}

	/**
	 * Function to add Traces
	 * @param number $new_product_id
	 */
	function adding_traces($new_product_id){

		$all_traces = $this->input->post('traces_of');
		if($all_traces != ''){
			$traces = explode(':::',$all_traces);
			if(!empty($traces)){
				$trace_insert = array();
				foreach ($traces as $key => $trace){

					$traces_arr = explode('#',$trace);

					$insert_array = array();
					if(!empty($traces_arr) && count($traces_arr) == 4){
						$trace_insert[] = array(
								'product_id' => $new_product_id,
								'kp_id' => ( (is_numeric($traces_arr[3]))?$traces_arr[3]:0 ),
								'kt_id' => $traces_arr[2],
								'prefix' => ( (isset($traces_arr[0]))?addslashes($traces_arr[0]):'' ),
								'kt_name' => ( (isset($traces_arr[1]))?addslashes($traces_arr[1]):'' ),
								'display_order' => $key+1,
								'date_added' => date('Y-m-d H:i:s')
						);
					}else{
						if(strpos($trace,'lp#') !== FALSE && strpos($trace,'lp#') == 0){
							$trace_insert[] = array(
									'product_id' => $new_product_id,
									'kp_id' => 0,
									'kt_id' => 0,
									'prefix' => '',
									'kt_name' => '(',
									'display_order' => $key+1,
									'date_added' => date('Y-m-d H:i:s')
							);
						}elseif( strpos($trace,'rp#') !== FALSE && strpos($trace,'rp#') == 0){

							$trace_insert[] = array(
									'product_id' => $new_product_id,
									'kp_id' => 0,
									'kt_id' => 0,
									'prefix' => '',
									'kt_name' => ')',
									'display_order' => $key+1,
									'date_added' => date('Y-m-d H:i:s')
							);
						}else{
							$trace_insert[] = array(
									'product_id' => $new_product_id,
									'kp_id' => 0,
									'kt_id' => 0,
									'prefix' => '',
									'kt_name' => addslashes($trace),
									'display_order' => $key+1,
									'date_added' => date('Y-m-d H:i:s')
							);
						}
					}
				}

				if(!empty($trace_insert))
					$this->db->insert_batch('products_traces', $trace_insert);
			}
		}
	}

	/**
	 * Function to add Allergence
	 * @param number $new_product_id
	 */
	function adding_allergence($new_product_id){

		$all_allg = $this->input->post('allergence');
		if($all_allg != ''){
			$check_repeat = array();
			$allergences = explode(':::',$all_allg);
			if(!empty($allergences)){
				$all_insert = array();
				$sub_all_insert = array();
				foreach ($allergences as $key => $allergence){

					$allg_arr = explode('#',$allergence);

					$insert_array = array();
					if(!empty($allg_arr) && count($allg_arr) == 5){
						if($allg_arr[4] != 0){
							if($allg_arr[2] != 0){
								$sub_all_insert[] = array(
										'product_id' => $new_product_id,
										'kp_id' => ( (is_numeric($allg_arr[3]))?$allg_arr[3]:0 ),
										'parent_ka_id' => $allg_arr[4],
										'sub_ka_id' => $allg_arr[2],
										'sub_ka_name' => ( (isset($allg_arr[1]))?addslashes($allg_arr[1]):'' ),
										'display_order' => $key+1,
										'date_added' => date('Y-m-d H:i:s')
								);
							}
						}
						else{
							if($allg_arr[3] != 0){
								$all_insert[] = array(
										'product_id' => $new_product_id,
										'kp_id' => ( (is_numeric($allg_arr[3]))?$allg_arr[3]:0 ),
										'ka_id' => $allg_arr[2],
										'prefix' => ( (isset($allg_arr[0]))?addslashes($allg_arr[0]):'' ),
										'ka_name' => ( (isset($allg_arr[1]))?addslashes($allg_arr[1]):'' ),
										'display_order' => $key+1,
										'date_added' => date('Y-m-d H:i:s'),
										'by_ingredient'=>0
								);
							}
						}
					}else{
						if(strpos($allergence,'lp#') !== FALSE && strpos($allergence,'lp#') == 0){
							$all_insert[] = array(
									'product_id' => $new_product_id,
									'kp_id' => 0,
									'ka_id' => 0,
									'prefix' => '',
									'ka_name' => '(',
									'display_order' => $key+1,
									'date_added' => date('Y-m-d H:i:s')
							);
						}elseif( strpos($allergence,'rp#') !== FALSE && strpos($allergence,'rp#') == 0){

							$all_insert[] = array(
									'product_id' => $new_product_id,
									'kp_id' => 0,
									'ka_id' => 0,
									'prefix' => '',
									'ka_name' => ')',
									'display_order' => $key+1,
									'date_added' => date('Y-m-d H:i:s')
							);
						}else{
							$all_insert[] = array(
									'product_id' => $new_product_id,
									'kp_id' => 0,
									'ka_id' => 0,
									'prefix' => '',
									'ka_name' => addslashes($allergence),
									'display_order' => $key+1,
									'date_added' => date('Y-m-d H:i:s')
							);
						}
					}
				}

				if(!empty($all_insert))
					$this->db->insert_batch('products_allergence', $all_insert);

				if(!empty($sub_all_insert))
					$this->db->insert_batch('product_sub_allergence', $sub_all_insert);
			}
		}
	}

	/**
	 * This model function is used to add product when Ingredient system is enables
	 */
	function add_product_ing($sel_lang = 'nl_NL'){
		if ($sel_lang == 'fr_FR'){
			$sel_lang_upd = '_fr';
		}elseif($sel_lang == 'nl_NL'){
			$sel_lang_upd = '_dch';
		}else{
			$sel_lang_upd = '_dch';
		}
		$add_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'prodescription' => addslashes($this->input->post('prodescription')),
				'procreated'=>date('Y-m-d',time()),
				'recipe_method' => $this->input->post('recipe_method_txt')
		);

		if($this->input->post('semi_products') == 1){
			$add_product_data['semi_product'] = 1;
			$add_product_data['recipe_weight'] = $this->input->post('recipe_weight');
		}

		if($this->input->post('semi_products') == 2){
			$add_product_data['semi_product'] = 2;
			$add_product_data['recipe_weight'] = $this->input->post('recipe_weight');
		}

		if(!$this->input->post('product_type')){
			$add_product_data['direct_kcp'] = $this->input->post('direct_kcp');
		}

		If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')){
			$add_product_data['ingredients'] = $this->input->post('ingredients');
			$add_product_data['allergence'] = $this->input->post('allergence');
			$add_product_data['traces_of'] = $this->input->post('traces_of');
		}

		$query=$this->db->insert('products', $add_product_data);
		$new_product_id=$this->db->insert_id();//method to get id of last inserted row//

		//CODE TO ADD QUANTITY OF FDD PRODUCTS USED IN THIS PRODUCT
		$quant_array =$this->input->post('hidden_fdds_quantity');

		$this->delete_notcontaining_gs1_products();

		if($quant_array != '' && $quant_array != '&&'){
			$quant_array = substr($quant_array, 0, -2);
			$quant_arr = explode('**', $quant_array);
			foreach ($quant_arr as $quant_ar){
				$insrt_gs1_array = array();
				$quant_ar_ar = explode('#', $quant_ar);
				$gs1_pro_check = explode('--', $quant_ar_ar[2]);

				$semi_pro_id = 0;
				if($quant_ar_ar[5] != NULL){
					$semi_pro_id = $quant_ar_ar[5];
				}

				$insrt_quant_array = array(
						'obs_pro_id'=>$new_product_id,
						'fdd_pro_id'=>$quant_ar_ar[0],
						'quantity'=>$quant_ar_ar[1],
						'unit'=>$quant_ar_ar[4],
						'semi_product_id'=>$semi_pro_id,
						'fixed'=>$quant_ar_ar[6]
				);
				
				$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

				if ((isset($gs1_pro_check[4]) && $gs1_pro_check[4] == 'GS1')){
					$insrt_gs1_array = array(
							'gs1_pid'=>$quant_ar_ar[0],
							'company_id'=>$this->company_id
					);
				}

				if(!empty($insrt_gs1_array)){
					$this->db->select('request_status');
					$gs1_exist = $this->db->get_where('request_gs1',array('gs1_pid'=>$quant_ar_ar[0]))->result_array();

					if (empty($gs1_exist)){
						$insrt_gs1_array[ 'date_added' ] = date( "Y-m-d H:i:s" );
						$this->db->insert('request_gs1',$insrt_gs1_array);
					}
					else{
						$request_status = $gs1_exist[0]['request_status'];
						$gs1_exist = $this->db->get_where('request_gs1',array('company_id'=>$this->company_id,'gs1_pid'=>$quant_ar_ar[0]))->result_array();
						if (empty($gs1_exist))
						{
							$insrt_gs1_array[ 'date_added' ] 	= date( "Y-m-d H:i:s" );
							$insrt_gs1_array['request_status'] 	= $request_status;
							$this->db->insert('request_gs1',$insrt_gs1_array);
						}
					}
				}
			}
		}

		$obs_quant_array =$this->input->post('hidden_own_pro_quantity');

		if($obs_quant_array != '' && $obs_quant_array != '&&'){
			$obs_quant_array = substr($obs_quant_array, 0, -2);
			$quant_arr = explode('**', $obs_quant_array);
			foreach ($quant_arr as $quant_ar){
				$quant_ar_ar = explode('#', $quant_ar);

				$semi_pro_id = 0;
				if($quant_ar_ar[5] != NULL){
					$semi_pro_id = $quant_ar_ar[5];
				}

				$insrt_quant_array = array(
						'obs_pro_id'=>$new_product_id,
						'fdd_pro_id'=>$quant_ar_ar[0],
						'quantity'=>$quant_ar_ar[1],
						'unit'=>$quant_ar_ar[4],
						'is_obs_product'=>1,
						'semi_product_id'=>$semi_pro_id,
						'fixed'=>1
				);
				
				$this->db->insert('fdd_pro_quantity',$insrt_quant_array);
			}
		}

		If($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
			// Adding Ingredients
			$this->adding_ingredients($new_product_id,$sel_lang_upd);

			// Adding Traces
			$this->adding_traces($new_product_id);

			// Adding Allergence
			$this->adding_allergence($new_product_id);
		}

		//-------------------------------------------------------------------------//
		return(array('categories_id'=>$this->input->post('categories_id'),'subcategories_id'=>$this->input->post('subcategories_id')));
	}

	/**
	 * This model function is used to update product when Ingredient system in enabled
	 */
	function update_product_ing($sel_lang = 'nl_NL'){
		if ($sel_lang == 'fr_FR'){
			$sel_lang_upd = '_fr';
		}elseif($sel_lang == 'nl_NL'){
			$sel_lang_upd = '_dch';
		}else{
			$sel_lang_upd = '_dch';
		}
		if($this->input->post('semi_products') == 1){
			$custom_pro_total_wt_arr = $this->update_semi_product_contains($sel_lang_upd);
		}
		if($this->input->post('semi_products') == 2){
			$custom_pro_total_wt_array = $this->update_semi_product_contains($sel_lang_upd);
		}

		//updating fdd products quantity
		$quant_array =$this->input->post('hidden_fdds_quantity');


		if($quant_array != ''){
			if($quant_array == '&&'){
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_gs1_products();
			}else{
				$quant_array = substr($quant_array, 0, -2);
				$quant_arr = explode('**', $quant_array);
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
				$this->db->delete('fdd_pro_quantity');


				foreach ($quant_arr as $quant_ar){
					$insrt_gs1_array = array();
					$quant_ar_ar = explode('#', $quant_ar);
					$gs1_pro_check = explode('--', $quant_ar_ar[2]);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}
					$insrt_quant_array = array(
							'obs_pro_id'=>$this->input->post('prod_id'),
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'semi_product_id'=>$semi_pro_id,
							'fixed'=>$quant_ar_ar[6]
					);
				
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

					if ((isset($gs1_pro_check[4]) && $gs1_pro_check[4] == 'GS1')){
						$insrt_gs1_array = array(
								'gs1_pid'=>$quant_ar_ar[0],
								'company_id'=>$this->company_id
						);
					}

					if(!empty($insrt_gs1_array)){
						$this->db->select('request_status');
						$gs1_exist = $this->db->get_where('request_gs1',array('gs1_pid'=>$quant_ar_ar[0]))->result_array();

						if (empty($gs1_exist)){
							$insrt_gs1_array[ 'date_added' ] = date( "Y-m-d H:i:s" );
							$this->db->insert('request_gs1',$insrt_gs1_array);
						}
						else{
							$request_status = $gs1_exist[0]['request_status'];
							$gs1_exist = $this->db->get_where('request_gs1',array('company_id'=>$this->company_id,'gs1_pid'=>$quant_ar_ar[0]))->result_array();
							if (empty($gs1_exist))
							{
								$insrt_gs1_array[ 'date_added' ] 	= date( "Y-m-d H:i:s" );
								$insrt_gs1_array['request_status'] 	= $request_status;
								$this->db->insert('request_gs1',$insrt_gs1_array);
							}
						}
					}
				}
				$this->delete_notcontaining_gs1_products();
			}
		}

		$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
		if($obs_quant_array != ''){
			if($obs_quant_array == '&&'){
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_products();
			}else{
				$obs_quant_array = substr($obs_quant_array, 0, -2);
				$quant_arr = explode('**', $obs_quant_array);

				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_products();

				foreach ($quant_arr as $quant_ar){
					$quant_ar_ar = explode('#', $quant_ar);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}

					$insrt_quant_array = array(
							'obs_pro_id'=>$this->input->post('prod_id'),
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'is_obs_product'=>1,
							'semi_product_id'=>$semi_pro_id,
							'fixed'=>1
					);
				
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);
				}
			}
		}

		$update_product_data = array(
			'proname' => addslashes($this->input->post('proname')),
			'prodescription' => addslashes($this->input->post('prodescription')),
			'proupdated' => date('Y-m-d',time()),
			'recipe_method' => $this->input->post('recipe_method_txt')
		);

		If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' ||$this->session->userdata('menu_type') == 'fdd_premium')){
			$update_product_data['ingredients'] = $this->input->post('ingredients');
			$update_product_data['allergence'] = $this->input->post('allergence');
			$update_product_data['traces_of'] = $this->input->post('traces_of');
		}

		$update_product_data['recipe_weight'] = $this->input->post('recipe_weight');

		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
			/**
			 * Updating Product Ingredients
			 */
			$this->db->delete('products_ingredients',array('product_id' => $this->input->post('prod_id')));

			// Adding Ingredients
			$this->adding_ingredients($this->input->post('prod_id'),$sel_lang_upd);

			/**
			 * Updating Product Traces
			*/
			$this->db->delete('products_traces',array('product_id' => $this->input->post('prod_id')));

			// Adding Traces
			$this->adding_traces($this->input->post('prod_id'));

			/**
			 * Updating Product Allergence
			*/
			$this->db->delete('products_allergence',array('product_id' => $this->input->post('prod_id')));
			$this->db->delete('product_sub_allergence',array('product_id' => $this->input->post('prod_id')));

			// Adding Allergence
			$this->adding_allergence($this->input->post('prod_id'));
		}

		if($this->input->post('semi_products') == 1){
			 $this->update_semi_product_quant($custom_pro_total_wt_arr);
			 $this->update_kp_display_order($this->input->post('prod_id'));
		}

		if($this->input->post('semi_products') == 2){
			$this->update_semi_product_quant($custom_pro_total_wt_array);
			//if(!empty($custom_pro_total_wt_array['custom_pro_total_wt_arr']))
				$this->update_kp_display_order($this->input->post('prod_id'));
		}

		return(array('categories_id'=>$this->input->post('categories_id'),'subcategories_id'=>$this->input->post('subcategories_id')));
	}

	function fdd_credits(){
		/*$this->db->select('fdd_product_credit');
		$this->db->where('id',$this->company->id);
		$credits = $this->db->get('company')->result_array();
		$crd = $credits[0]['fdd_product_credit'];

		$this->db->distinct();
		$this->db->select('fdd_pro_id');
		$this->db->where(array('products.company_id'=>$this->company->id,'is_obs_product'=>0));
		$this->db->join('fdd_pro_quantity','products.id = fdd_pro_quantity.obs_pro_id');
		$used_credits = $this->db->get('products')->num_rows();

		return $crd-$used_credits;*/
		return 400;
	}

	function rename_product($p_id, $update_array= array()){
		$this->db->where('id',$p_id);
		RETURN $this->db->update('products',$update_array);
	}

	function get_own_products(){

		$this->db->where('company_id',$this->company->id);
		$this->db->where('direct_kcp',1);
		$this->db->where('direct_kcp_id',0);
		RETURN $this->db->get('products')->result_array();
	}

	function get_own_products_semi(){

		$this->db->where('company_id',0);
		RETURN $this->db->get('products')->result_array();
	}

	function get_semi_products(){

		$this->db->where(array('semi_product'=>1, 'company_id'=> $this->company_id));
		RETURN $this->db->get('products')->result();
	}

	function get_semi_products_extra(){

		$this->db->where(array('semi_product'=>2, 'company_id'=> $this->company_id));
		RETURN $this->db->get('products')->result();
	}

	function update_semi_product_contains($sel_lang_upd = '_dch'){
		$prod_id = $this->input->post('prod_id');
		$fdd_str = $this->input->post('hidden_fdds_quantity');
		$own_str = $this->input->post('hidden_own_pro_quantity');
		$custom_pro_total_wt_arr = array();
		$custom_pro_total_wt_arr_cust = array();

		if($fdd_str == '' && $own_str == ''){
			//no need to update(no changes in containing products)
		}else{
		// when all products form a semi-products got removed
			if($fdd_str == '&&' && $own_str == '&&'){
				$this->db->select('obs_pro_id');
				$this->db->where('semi_product_id',$prod_id);
				$this->db->group_by('obs_pro_id');
				$results = $this->db->get('fdd_pro_quantity')->result_array();

				foreach ($results as $my_result){
					$this->db->select_sum('quantity');
					$this->db->where(array('obs_pro_id'=>$my_result['obs_pro_id'],'semi_product_id'=>$prod_id));
					$sums = $this->db->get('fdd_pro_quantity')->result_array();

					if(!empty($sums)){
						$new_short_arr = array(
								'obs_id' => $my_result['obs_pro_id'],
								'total'=>$sums[0]['quantity']
						);

						$custom_pro_total_wt_arr[] =$new_short_arr;
					}

					$this->db->select('obs_pro_id');
					$this->db->where('semi_product_id',$my_result['obs_pro_id']);
					$this->db->group_by('obs_pro_id');
					$my_custom_results = $this->db->get('fdd_pro_quantity')->result_array();

					foreach ($my_custom_results as $cust_result){
						$this->db->select_sum('quantity');
						$this->db->where(array('obs_pro_id'=>$cust_result['obs_pro_id'],'semi_product_id'=>$my_result['obs_pro_id']));
						$cust_sums = $this->db->get('fdd_pro_quantity')->result_array();

						if(!empty($cust_sums)){
							$new_short_array = array(
									'obs_id' => $cust_result['obs_pro_id'],
									'total'=>$cust_sums[0]['quantity']
							);

							$custom_pro_total_wt_arr_cust[] =$new_short_array;
						}
					}
				}

				foreach ($results as $result){
					$total_wt = 0;
					$this->db->select('obs_pro_id,fdd_pro_id,quantity');
					$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'semi_product_id'=>$prod_id));
					$res1 = $this->db->get('fdd_pro_quantity')->result_array();

					foreach ($res1 as $res){
						$total_wt += $res['quantity'];
					}

					$this->db->select('recipe_weight');
					$this->db->where('id',$result['obs_pro_id']);
					$wt = $this->db->get('products')->result_array();
					if(!empty($wt)){
						$re_wt = $wt[0]['recipe_weight'];
					}else{
						$re_wt = 0;
					}
					$new_wt = ($re_wt*1000)-$total_wt;
					if($new_wt >= 0){
						$new_wt = $new_wt/1000;

						//update custom product recipe weight
						$this->db->where('id',$result['obs_pro_id']);
						$this->db->update('products', array('recipe_weight'=>$this->number2db($new_wt)));

						//Delete products used in custom product due to semi product
						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'semi_product_id'=>$prod_id));
						$this->db->delete('fdd_pro_quantity');

						$rest_arr = array();
						foreach ($res1 as $res){
							$rest_arr[] = $res['fdd_pro_id'];
							//deleting ingredients from custom products
							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('products_ingredients');

							//deleting allergens from custom products
							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('products_allergence');

							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('product_sub_allergence');

							//deleting traces from custom products
							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('products_traces');
						}
					}

					$this->db->select('obs_pro_id');
					$this->db->where('semi_product_id',$result['obs_pro_id']);
					$this->db->group_by('obs_pro_id');
					$results_cust = $this->db->get('fdd_pro_quantity')->result_array();

					foreach ($results_cust as $results_cust_val){
						$total_wt = 0;
						$this->db->select('obs_pro_id,fdd_pro_id,quantity');
						$this->db->where(array('obs_pro_id'=>$results_cust_val['obs_pro_id'],'semi_product_id'=>$result['obs_pro_id']));
						$res1 = $this->db->get('fdd_pro_quantity')->result_array();
						$count = 0;
						foreach ($res1 as $res){
							$total_wt += $res['quantity'];
							$count++;
						}

						$this->db->select('recipe_weight');
						$this->db->where('id',$results_cust_val['obs_pro_id']);
						$wt = $this->db->get('products')->result_array();
						if(!empty($wt)){
							$re_wt = $wt[0]['recipe_weight'];
						}else{
							$re_wt = 0;
						}
						$new_wt = ($re_wt*1000)-$total_wt;
						if($new_wt >= 0){
							$new_wt = $new_wt/1000;

							if($count == count($rest_arr)){
							//update custom product recipe weight
								$this->db->where('id',$results_cust_val['obs_pro_id']);
								$this->db->update('products', array('recipe_weight'=>$new_wt));
							}
							foreach ($rest_arr as $rest){
								//Delete products used in custom product due to semi product
								$this->db->where(array('obs_pro_id'=>$results_cust_val['obs_pro_id'],'fdd_pro_id'=>$rest,'semi_product_id'=>$result['obs_pro_id']));
								$this->db->delete('fdd_pro_quantity');

								//foreach ($res1 as $res){
									//deleting ingredients from custom products
									$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
									$this->db->delete('products_ingredients');

									//deleting allergens from custom products
									$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
									$this->db->delete('products_allergence');

									$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
									$this->db->delete('product_sub_allergence');

									//deleting traces from custom products
									$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
									$this->db->delete('products_traces');
								//}
							}
						}
					}
				}
			}
			else{
				//making a array of total weight of semi product used in custom products with their id
				$this->db->select('obs_pro_id');
				$this->db->where('semi_product_id',$prod_id);
				$this->db->group_by('obs_pro_id');
				$my_results = $this->db->get('fdd_pro_quantity')->result_array();

				foreach ($my_results as $my_result){
					$this->db->select_sum('quantity');
					$this->db->where(array('obs_pro_id'=>$my_result['obs_pro_id'],'semi_product_id'=>$prod_id));
					$sums = $this->db->get('fdd_pro_quantity')->result_array();

					if(!empty($sums)){
						$new_short_arr = array(
								'obs_id' => $my_result['obs_pro_id'],
								'total'=>$sums[0]['quantity']
						);

						$custom_pro_total_wt_arr[] =$new_short_arr;
					}

					$this->db->select('obs_pro_id');
					$this->db->where('semi_product_id',$my_result['obs_pro_id']);
					$this->db->group_by('obs_pro_id');
					$my_custom_results = $this->db->get('fdd_pro_quantity')->result_array();

					foreach ($my_custom_results as $cust_result){
						$this->db->select_sum('quantity');
						$this->db->where(array('obs_pro_id'=>$cust_result['obs_pro_id'],'semi_product_id'=>$my_result['obs_pro_id']));
						$cust_sums = $this->db->get('fdd_pro_quantity')->result_array();

						if(!empty($cust_sums)){
							$new_short_array = array(
									'obs_id' => $cust_result['obs_pro_id'],
									'total'=>$cust_sums[0]['quantity']
							);

							$custom_pro_total_wt_arr_cust[] =$new_short_array;
						}
					}
				}

				/*
				 * get total products used inside semi product
				 */
				$this->db->select('fdd_pro_id');
				$this->db->where( array( 'obs_pro_id' => $prod_id,'is_obs_product' => 0 ) );
				$results = $this->db->get('fdd_pro_quantity')->result_array();

				$old_added_array1 = array();
				foreach ($results as $result){
					$old_added_array1[]= $result['fdd_pro_id'];
				}

				$this->db->select('fdd_pro_id');
				$this->db->where( array( 'obs_pro_id' => $prod_id,'is_obs_product' => 1 ) );
				$results = $this->db->get('fdd_pro_quantity')->result_array();

				$old_added_array2 = array();
				foreach ($results as $result){
					$old_added_array2[]= $result['fdd_pro_id'];
				}

				/*
				 * create array of new fdd products inside semi product when insert
				 */
				$new_added_array1 = array();
				if($fdd_str != '' && $fdd_str != '&&'){
					$new_added= explode('**', $fdd_str);
					foreach ($new_added as $new_added_item){
						if($new_added_item != ''){
							$short_arr = explode("#", $new_added_item);
							$new_added_array1[] = $short_arr[0];
						}
					}
				}

				/*
				 * create array of new own products inside semi product when insert
				*/
				$new_added_array2 = array();
				if($own_str != '' && $own_str != '&&'){
					$new_added = explode('**', $own_str);
					foreach ($new_added as $new_added_item){
						if($new_added_item != ''){
							$short_arr = explode("#", $new_added_item);
							$new_added_array2[] = $short_arr[0];
						}
					}
				}

				$to_del_arr1 = array_diff($old_added_array1, $new_added_array1);
				$to_del_arr2 = array_diff($old_added_array2, $new_added_array2);
				$to_add_arr1 = array_diff($new_added_array1, $old_added_array1);
				$to_add_arr2 = array_diff($new_added_array2, $old_added_array2);
				$extra_arr = array();
				$extra_arr1 = array();
				$extra_arr2 = array();

				foreach ( $to_del_arr1 as $to_del){

					foreach ($my_results as $result){

						$this->db->select('is_obs_product');
						$result_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product' => 0,'semi_product_id !='=>$prod_id))->result_array();
						if(!empty($result_s)){
							$extra_arr1[] = $to_del;
							$extra_ids1[$result['obs_pro_id']][] = $to_del;
							for($c = 0; $c < count($result_s); $c++){
								$to_add_arr1[] = $to_del;
							}
						}

						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id'=>$prod_id,'is_obs_product' => 0));
						$this->db->delete('fdd_pro_quantity');

						//deleting ingredients, allergens and traces
						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del,'is_obs_ing'=>0));
						$this->db->delete('products_ingredients');

						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('products_allergence');

						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('product_sub_allergence');

						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('products_traces');

						$this->db->select('obs_pro_id,semi_product_id');
						$this->db->where('semi_product_id',$result['obs_pro_id']);
						$this->db->group_by('obs_pro_id');
						$results3 = $this->db->get('fdd_pro_quantity')->result_array();

						if(!empty($results3)){
							foreach ($results3 as $result_new){

								$this->db->select('is_obs_product');
								$result_new_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result_new['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product'=>0,'semi_product_id !='=>$result_new['semi_product_id']))->result_array();
								if(!empty($result_new_s)){
									$extra_new_ids1[$result_new['obs_pro_id']][] = $to_del;
									for($c = 0; $c < count($result_new_s); $c++){
										$to_add_new_arr1[] = $to_del;
									}
								}

								$this->db->where(array('obs_pro_id'=>$result_new['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product'=>0,'semi_product_id'=>$result_new['semi_product_id']));
								$this->db->delete('fdd_pro_quantity');

								//deleting ingredients, allergens and traces
								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del,'is_obs_ing'=>0));
								$this->db->delete('products_ingredients');

								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
								$this->db->delete('products_allergence');

								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
								$this->db->delete('product_sub_allergence');

								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
								$this->db->delete('products_traces');
							}
						}
					}
				}

				foreach ( $to_del_arr2 as $to_del){

					foreach ($my_results as $result){

						$this->db->select('is_obs_product');
						$result_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product'=>1,'semi_product_id !='=>$prod_id))->result_array();

						if(!empty($result_s)){
							$extra_arr2[] = $to_del;
							$extra_ids2[$result['obs_pro_id']][] = $to_del;
							for($c = 0; $c < count($result_s); $c++){
								$to_add_arr2[] = $to_del;
							}
						}

						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product'=>1,'semi_product_id'=>$prod_id));
						$this->db->delete('fdd_pro_quantity');

						//deleting ingredients
						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del,'is_obs_ing'=>1));
						$this->db->delete('products_ingredients');

						$this->db->select('obs_pro_id,semi_product_id');
						$this->db->where('semi_product_id',$result['obs_pro_id']);
						$this->db->group_by('obs_pro_id');
						$results3 = $this->db->get('fdd_pro_quantity')->result_array();

						if(!empty($results3)){
							foreach ($results3 as $result_new){

								$this->db->select('is_obs_product');
								$result_new_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result_new['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product'=>1,'semi_product_id !='=>$result_new['semi_product_id']))->result_array();
								if(!empty($result_new_s)){
									$extra_new_ids2[$result_new['obs_pro_id']][] = $to_del;
									for($c = 0; $c < count($result_new_s); $c++){
										$to_add_new_arr2[] = $to_del;
									}
								}

								$this->db->where(array('obs_pro_id'=>$result_new['obs_pro_id'],'fdd_pro_id'=>$to_del,'is_obs_product'=>1,'semi_product_id'=>$result_new['semi_product_id']));
								$this->db->delete('fdd_pro_quantity');

								//deleting ingredients
								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del,'is_obs_ing'=>1));
								$this->db->delete('products_ingredients');
							}
						}
					}
				}

				//E-nbr status
				$enbr_setting = $this->get_enbr_status($this->company_id);

				$extra_arr1 = array_unique($extra_arr1);
				$extra_arr2 = array_unique($extra_arr2);

				$ing_insert_array = array();
				$vet_insert_array = array();
				$add_insert_array = array();
				$all_insert_array = array();
				$all_sub_insert_array = array();
				$trs_insert_array = array();
				foreach ($to_add_arr1 as $to_add){
					$short_name= '';
					$ingis = array();
					$vetten = array();
					$add = array();
					$alls = array();
					$sub_alls = array();
					$trs = array();

					//collecting information of fdd product from fooddesk database
					

					$this->fdb->select('p_short_name'.$sel_lang_upd.',review&fixed');;
					$this->fdb->where('p_id',$to_add);
					$res_short_name = $this->fdb->get('products')->result_array();
					if(!empty($res_short_name)){
						$short_name = $res_short_name[0]['p_short_name'.$sel_lang_upd.''];
					}

					$this->fdb->select('prod_ingredients.i_id, prod_ingredients.aller_type, prod_ingredients.aller_type_fr, prod_ingredients.aller_type_dch, prod_ingredients.allergence, prod_ingredients.allergence_fr, prod_ingredients.allergence_dch, prod_ingredients.sub_allergence, prod_ingredients.sub_allergence_fr, prod_ingredients.sub_allergence_dch, prod_ingredients.new_allergence, prod_ingredients.new_allergence_fr, prod_ingredients.new_allergence_dch, ip1.prefix, ingredients.ing_name'.$sel_lang_upd.', ingredients.have_all_id');
					$this->fdb->where('p_id',$to_add);
					$this->fdb->join('ingredients','prod_ingredients.i_id = ingredients.ing_id');
					$this->fdb->join('ingredient_prefixes as ip1','prod_ingredients.i_id = ip1.ing_id AND prod_ingredients.p_id = ip1.product_id','left');
					$this->fdb->order_by('order','ASC');
					$ingis = $this->fdb->get('prod_ingredients')->result_array();


					$this->fdb->select('prod_allergence.a_id,allergence.all_name'.$sel_lang_upd.'');
					$this->fdb->where('p_id',$to_add);
					$this->fdb->join('allergence','prod_allergence.a_id = allergence.all_id');
					$alls = $this->fdb->get('prod_allergence')->result_array();

					$this->fdb->select('sub_allergence.parent_all_id,prod_sub_allergence.a_id,sub_allergence.all_name'.$sel_lang_upd.'');
					$this->fdb->where('p_id',$to_add);
					$this->fdb->join('sub_allergence','prod_sub_allergence.a_id = sub_allergence.all_id');
					$sub_alls = $this->fdb->get('prod_sub_allergence')->result_array();

					$this->fdb->select('prod_traces.t_id, traces.t_name'.$sel_lang_upd.'');
					$this->fdb->where('p_id',$to_add);
					$this->fdb->join('traces','prod_traces.t_id = traces.t_id');
					$trs = $this->fdb->get('prod_traces')->result_array();
					

					foreach($my_results as $key=>$result){
						$flag = true;

						if(in_array($to_add, $extra_arr1)){
							if(isset($extra_ids1) && isset($extra_ids1[$result['obs_pro_id']]) && in_array($to_add, $extra_ids1[$result['obs_pro_id']])){
								$flag = false;
							}
							else{
								continue;
							}
						}

						//added new fdd product as a product of semi product in custom product
						if($flag){
							$new_added= explode('**', $fdd_str);
							$unit = 'g';
							foreach ($new_added as $new_added_item){
								if($new_added_item != ''){
									$short_arr = explode("#", $new_added_item);
									if($short_arr[0] == $to_add){
										$unit = $short_arr[4];
										break;
									}
								}
							}
							$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>0,'obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$prod_id,'unit'=>$unit,'fixed'=>$res_short_name[0]['review&fixed']));
						}
						//adding ingredients for new added fdd product
						$ing_insert_array[] = array(
								'product_id'=>$result['obs_pro_id'],
								'kp_id'=> $to_add,
								'ki_id'=>0,
								'prefix' => '',
								'ki_name'=> $short_name,
								'display_order'=>1,
								'kp_display_order'=>$key+100,
								'date_added'=>date('Y-m-d h:i:s'),
								'have_all_id' => 0,
								'aller_type' => '0',
								'aller_type_fr' => '0',
								'aller_type_dch' => '0',
								'allergence' => '0',
								'allergence_fr' => '0',
								'allergence_dch' => '0',
								// 'new_allergence'] => $ing_arr[9],
								'sub_allergence' => '0',
								'sub_allergence_fr' => '0',
								'sub_allergence_dch' => '0'
						);
						if(!empty($ingis)){
							$total_ing = 0;
							$ing_insert_array[] = array(
									'product_id'=>$result['obs_pro_id'],
									'kp_id'=> $to_add,
									'ki_id'=>0,
									'prefix' => '',
									'ki_name'=> '(',
									'display_order'=>2,
									'kp_display_order'=>$key+100,
									'date_added'=>date('Y-m-d h:i:s'),
									'have_all_id' => 0,
									'aller_type' => '0',
									'aller_type_fr' => '0',
									'aller_type_dch' => '0',
									'allergence' => '0',
									'allergence_fr' => '0',
									'allergence_dch' => '0',
									// 'new_allergence'] => $ing_arr[9],
									'sub_allergence' => '0',
									'sub_allergence_fr' => '0',
									'sub_allergence_dch' => '0'
							);

							foreach ($ingis as $ing_key=>$ingi){
								if(!$ingi['prefix']){
									$ingi['prefix'] = '';
								}

								$display_name = $ingi['ing_name'.$sel_lang_upd.''];
								$enbr_setting = $this->get_enbr_status($this->company_id);
								$enbr_result = $this->get_e_current_nbr($ingi['i_id'],$enbr_setting['enbr_status'],$sel_lang_upd);
								if(!empty($enbr_result)){
									$ki_id = $enbr_result['ki_id'];
									$display_name = $enbr_result['ki_name'];
								}

								$ing_insert_array[] = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=> $ingi['i_id'],
										'prefix' => '',
										'ki_name'=> $display_name,
										'display_order'=>$ing_key+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id'=>$ingi['have_all_id'],
										'aller_type' => $ingi['aller_type'],
										'aller_type_fr' => $ingi['aller_type_fr'],
										'aller_type_dch' => $ingi['aller_type_dch'],
										'allergence' => $ingi['allergence'],
										'allergence_fr' => $ingi['allergence_fr'],
										'allergence_dch' => $ingi['allergence_dch'],
										// 'new_allergence' => $ingi['new_allergence'],
										'sub_allergence' => $ingi['sub_allergence'],
										'sub_allergence_fr' => $ingi['sub_allergence_fr'],
										'sub_allergence_dch' => $ingi['sub_allergence_dch']
								);

								$total_ing++;
							}

							$ing_insert_array[] = array(
									'product_id'=>$result['obs_pro_id'],
									'kp_id'=> $to_add,
									'ki_id'=>0,
									'prefix' => '',
									'ki_name'=> ')',
									'display_order'=>$total_ing+3,
									'kp_display_order'=>$key+100,
									'date_added'=>date('Y-m-d h:i:s'),
									'have_all_id' => 0,
									'aller_type' => '0',
									'aller_type_fr' => '0',
									'aller_type_dch' => '0',
									'allergence' => '0',
									'allergence_fr' => '0',
									'allergence_dch' => '0',
									// 'new_allergence'] => $ing_arr[9],
									'sub_allergence' => '0',
									'sub_allergence_fr' => '0',
									'sub_allergence_dch' => '0'
							);
						}

						//adding allergence
						if(!empty($alls)){
							foreach ($alls as $all_key=>$all){

								$all_insert_array[] = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'ka_id'=>$all['a_id'],
										'ka_name'=> $all['all_name'.$sel_lang_upd.''],
										'display_order'=>$all_key+1,
										'date_added'=>date('Y-m-d h:i:s')
								);
							}
						}

						//adding suballergence
						if(!empty($sub_alls)){
							foreach ($sub_alls as $all_key=>$all){

								$all_sub_insert_array[] = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'parent_ka_id'=>$all['parent_all_id'],
										'sub_ka_id'=>$all['a_id'],
										'sub_ka_name'=> $all['all_name'.$sel_lang_upd.''],
										'display_order'=>$all_key+1,
										'date_added'=>date('Y-m-d h:i:s')
								);
							}
						}

						//adding traces of product
						if(!empty($trs)){
							foreach ($trs as $tr_key=>$tr){

								$trs_insert_array[] = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'kt_id'=>$tr['t_id'],
										'kt_name'=> $tr['t_name'.$sel_lang_upd.''],
										'display_order'=>$tr_key+1,
										'date_added'=>date('Y-m-d h:i:s')
								);
							}
						}

						$this->db->where('semi_product_id',$result['obs_pro_id']);
						$this->db->group_by('obs_pro_id');
						$results4 = $this->db->get('fdd_pro_quantity')->result_array();

						foreach ($results4 as $key_p=>$result4){
							//added new fdd product as a product of semi product in custom product
							if($flag){
								$new_added= explode('**', $fdd_str);
								$unit = 'g';
								foreach ($new_added as $new_added_item){
									if($new_added_item != ''){
										$short_arr = explode("#", $new_added_item);
										if($short_arr[0] == $to_add){
											$unit = $short_arr[4];
											break;
										}
									}
								}
								$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>0,'obs_pro_id'=>$result4['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$result4['semi_product_id'],'unit'=>$unit,'fixed'=>$res_short_name[0]['review&fixed']));
							}
							//adding ingredients for new added fdd product
							$ing_insert_array[] = array(
									'product_id'=>$result4['obs_pro_id'],
									'kp_id'=> $to_add,
									'ki_id'=>0,
									'prefix' => '',
									'ki_name'=> $short_name,
									'display_order'=>1,
									'kp_display_order'=>$key+100,
									'date_added'=>date('Y-m-d h:i:s'),
									'have_all_id' => 0,
									'aller_type' => '0',
									'aller_type_fr' => '0',
									'aller_type_dch' => '0',
									'allergence' => '0',
									'allergence_fr' => '0',
									'allergence_dch' => '0',
									// 'new_allergence'] => $ing_arr[9],
									'sub_allergence' => '0',
									'sub_allergence_fr' => '0',
									'sub_allergence_dch' => '0'
							);

							if(!empty($ingis)){
								$total_ing = 0;
								$ing_insert_array[] = array(
										'product_id'=>$result4['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=>0,
										'prefix' => '',
										'ki_name'=> '(',
										'display_order'=>2,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id' => 0,
										'aller_type' => '0',
										'aller_type_fr' => '0',
										'aller_type_dch' => '0',
										'allergence' => '0',
										'allergence_fr' => '0',
										'allergence_dch' => '0',
										// 'new_allergence'] => $ing_arr[9],
										'sub_allergence' => '0',
										'sub_allergence_fr' => '0',
										'sub_allergence_dch' => '0'
								);

								foreach ($ingis as $ing_key=>$ingi){
									if(!$ingi['prefix']){
										$ingi['prefix'] = '';
									}

									$display_name = $ingi['ing_name'.$sel_lang_upd.''];
									$enbr_setting = $this->get_enbr_status($this->company_id);
									$enbr_result = $this->get_e_current_nbr($ingi['i_id'],$enbr_setting['enbr_status'],$sel_lang_upd);
									if(!empty($enbr_result)){
										$ki_id = $enbr_result['ki_id'];
										$display_name = $enbr_result['ki_name'];
									}

									$ing_insert_array[] = array(
											'product_id'=>$result4['obs_pro_id'],
											'kp_id'=> $to_add,
											'ki_id'=>$ingi['i_id'],
											'prefix' => '',
											'ki_name'=> $display_name,
											'display_order'=>$ing_key+3,
											'kp_display_order'=>$key+100,
											'date_added'=>date('Y-m-d h:i:s'),
											'have_all_id'=>$ingi['have_all_id'],
											'aller_type' => $ingi['aller_type'],
											'aller_type_fr' => $ingi['aller_type_fr'],
											'aller_type_dch' => $ingi['aller_type_dch'],
											'allergence' => $ingi['allergence'],
											'allergence_fr' => $ingi['allergence_fr'],
											'allergence_dch' => $ingi['allergence_dch'],
											// 'new_allergence' => $ingi['new_allergence'],
											'sub_allergence' => $ingi['sub_allergence'],
											'sub_allergence_fr' => $ingi['sub_allergence_fr'],
											'sub_allergence_dch' => $ingi['sub_allergence_dch']
									);
									$total_ing++;
								}

								$ing_insert_array[] = array(
										'product_id'=>$result4['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=>0,
										'prefix' => '',
										'ki_name'=> ')',
										'display_order'=>$total_ing+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id' => 0,
										'aller_type' => '0',
										'aller_type_fr' => '0',
										'aller_type_dch' => '0',
										'allergence' => '0',
										'allergence_fr' => '0',
										'allergence_dch' => '0',
										// 'new_allergence'] => $ing_arr[9],
										'sub_allergence' => '0',
										'sub_allergence_fr' => '0',
										'sub_allergence_dch' => '0'
								);
							}

							/*

							foreach ($vetten as $ing_key=>$vet){
								$display_name = $vet['ing_name'.$sel_lang_upd.''];
								$display_result = $this->get_display_name($vet['i_id'],$sel_lang_upd);
								if($display_result != ''){
									$display_name = $display_result;
								}

								$vet_insert_array[] = array(
										'product_id'=>$result4['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=>$vet['i_id'],
										'ki_name'=> $display_name,
										'display_order'=>$ing_key+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id'=>$vet['have_all_id']
								);
							}

							foreach ($add as $ing_key=>$ingi){
								$display_name = $ingi['ing_name'.$sel_lang_upd.''];
								$ki_id = $ingi['i_id'];
								$display_name1 = '';
								$enbr_rel_ki_id = 0;

								$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_setting['enbr_status'],$sel_lang_upd);
								if(!empty($enbr_result)){
									$ki_id = $enbr_result['ki_id'];
									$display_name = $enbr_result['ki_name'];
									$display_result = $this->get_display_name($enbr_result['ki_id'],$sel_lang_upd);
									if($display_result != ''){
										$display_name = $display_result;
									}

									$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
									$display_name1 = $enbr_result['enbr_rel_ki_name'];
									$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id'],$sel_lang_upd);
									if($display_result != ''){
										$display_name1 = $display_result;
									}
								}
								else{
									$display_result = $this->get_display_name($ki_id,$sel_lang_upd);
									if($display_result != ''){
										$display_name = $display_result;
									}
								}

								$add_insert_array[] = array(
										'product_id'=>$result4['obs_pro_id'],
										'kp_id'=> $to_add,
										'add_id'=>$ingi['add_id'],
										'ki_id'=>$ki_id,
										'ki_name'=> $display_name,
										'enbr_rel_ki_id' => $enbr_rel_ki_id,
										'enbr_rel_ki_name' => $display_name1,
										'display_order'=>$ing_key+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id'=>$ingi['have_all_id']
								);
							}

							*/

							//adding allergence
							if(!empty($alls)){
								foreach ($alls as $all_key=>$all){

									$all_insert_array[] = array(
											'product_id'=>$result4['obs_pro_id'],
											'kp_id'=> $to_add,
											'ka_id'=>$all['a_id'],
											'ka_name'=> $all['all_name'.$sel_lang_upd.''],
											'display_order'=>$all_key+1,
											'date_added'=>date('Y-m-d h:i:s')
									);
								}
							}

							//adding suballergence
							if(!empty($sub_alls)){
								foreach ($sub_alls as $all_key=>$all){

									$all_sub_insert_array[] = array(
											'product_id'=>$result4['obs_pro_id'],
											'kp_id'=> $to_add,
											'parent_ka_id'=>$all['parent_all_id'],
											'sub_ka_id'=>$all['a_id'],
											'sub_ka_name'=> $all['all_name'.$sel_lang_upd.''],
											'display_order'=>$all_key+1,
											'date_added'=>date('Y-m-d h:i:s')
									);
								}
							}

							//adding traces of product
							if(!empty($trs)){
								foreach ($trs as $tr_key=>$tr){

									$trs_insert_array[] = array(
											'product_id'=>$result4['obs_pro_id'],
											'kp_id'=> $to_add,
											'kt_id'=>$tr['t_id'],
											'kt_name'=> $tr['t_name'.$sel_lang_upd.''],
											'display_order'=>$tr_key+1,
											'date_added'=>date('Y-m-d h:i:s')
									);
								}
							}
						}
					}
				}

				if(!empty($ing_insert_array))
					$this->db->insert_batch('products_ingredients', $ing_insert_array);

				if(!empty($all_insert_array))
					$this->db->insert_batch('products_allergence',$all_insert_array);

				if(!empty($all_sub_insert_array))
					$this->db->insert_batch('product_sub_allergence',$all_sub_insert_array);

				if(!empty($trs_insert_array))
					$this->db->insert_batch('products_traces',$trs_insert_array);

				foreach ($to_add_arr2 as $to_add){
					$short_name= '';

					$this->db->select('proname');
					$this->db->where('id',$to_add);
					$pro_name = $this->db->get('products')->result_array();
					if(!empty($pro_name)){
						$short_name = $pro_name[0]['proname'];
					}

					foreach($my_results as $key=>$result){
						$flag = true;

						if(in_array($to_add, $extra_arr2)){
							if(isset($extra_ids2) && isset($extra_ids2[$result['obs_pro_id']]) && in_array($to_add, $extra_ids2[$result['obs_pro_id']])){
								$flag = false;
							}
							else{
								continue;
							}
						}

						//added new own product as a product od semi product in custom product
						if($flag){
							$new_added = explode('**', $own_str);
							$unit = 'g';
							foreach ($new_added as $new_added_item){
								if($new_added_item != ''){
									$short_arr = explode("#", $new_added_item);
									if($short_arr[0] == $to_add){
										$unit = $short_arr[4];
										break;
									}
								}
							}
							$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>1,'obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$prod_id,'unit'=>$unit,'fixed'=>1));
						}
						$info = $this->db->get_where('products_pending',array('product_id'=>$to_add,'company_id'=>$this->company_id))->result_array();
						if(empty($info)){
							$this->db->insert('products_pending', array('product_id' => $to_add, 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
						}

						$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$to_add))->result_array();
						if(empty($con_info)){
							$this->db->insert('contacted_via_mail', array('obs_pro_id' => $to_add));
						}

						//adding product name of fixed product in ingredints list
						$ing_insert_array = array(
								'product_id'=>$result['obs_pro_id'],
								'kp_id'=> $to_add,
								'ki_id'=>0,
								'ki_name'=> $short_name,
								'display_order'=>1,
								'kp_display_order'=>$key+100,
								'date_added'=>date('Y-m-d h:i:s'),
								'is_obs_ing'=>1
						);

						$this->db->insert('products_ingredients',$ing_insert_array);

						$this->db->where('semi_product_id',$result['obs_pro_id']);
						$this->db->group_by('obs_pro_id');
						$results4 = $this->db->get('fdd_pro_quantity')->result_array();

						foreach ($results4 as $key_p=>$result4){

							//added new own product as a product od semi product in custom product
							if($flag){
								$new_added = explode('**', $own_str);
								$unit = 'g';
								foreach ($new_added as $new_added_item){
									if($new_added_item != ''){
										$short_arr = explode("#", $new_added_item);
										if($short_arr[0] == $to_add){
											$unit = $short_arr[4];
											break;
										}
									}
								}
								$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>1,'obs_pro_id'=>$result4['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$result4['semi_product_id'],'unit'=>$unit,'fixed'=>1));
							}

							$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$to_add))->result_array();
							if(empty($con_info)){
								$this->db->insert('contacted_via_mail', array('obs_pro_id' => $to_add));
							}

							//adding product name of fixed product in ingredints list
							$ing_insert_array = array(
									'product_id'=>$result4['obs_pro_id'],
									'kp_id'=> $to_add,
									'ki_id'=>0,
									'ki_name'=> $short_name,
									'display_order'=>1,
									'kp_display_order'=>$key+100,
									'date_added'=>date('Y-m-d h:i:s'),
									'is_obs_ing'=>1
							);

							$this->db->insert('products_ingredients',$ing_insert_array);
						}
					}
				}

				$ing_insert_array = array();
				$vet_insert_array = array();
				$add_insert_array = array();
				$all_insert_array = array();
				$all_sub_insert_array = array();
				$trs_insert_array = array();

				if(isset($to_add_new_arr1)){
					foreach ($to_add_new_arr1 as $to_add){
						$short_name= '';
						$ingis = array();
						$vetten = array();
						$add = array();
						$alls = array();
						$sub_alls = array();
						$trs = array();

						//collecting information of fdd product from fooddesk database
						
						$this->fdb->select('p_short_name'.$sel_lang_upd.'');;
						$this->fdb->where('p_id',$to_add);
						$res_short_name = $this->fdb->get('products')->result_array();
						if(!empty($res_short_name)){
							$short_name = $res_short_name[0]['p_short_name'.$sel_lang_upd.''];
						}

						$this->fdb->select('prod_ingredients.i_id, prod_ingredients.aller_type, prod_ingredients.aller_type_fr, prod_ingredients.aller_type_dch, prod_ingredients.allergence, prod_ingredients.allergence_fr, prod_ingredients.allergence_dch, prod_ingredients.sub_allergence, prod_ingredients.sub_allergence_fr, prod_ingredients.sub_allergence_dch, prod_ingredients.new_allergence, prod_ingredients.new_allergence_fr, prod_ingredients.new_allergence_dch, ip1.prefix, ingredients.ing_name'.$sel_lang_upd.', ingredients.have_all_id');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('ingredients','prod_ingredients.i_id = ingredients.ing_id');
						$this->fdb->join('ingredient_prefixes as ip1','prod_ingredients.i_id = ip1.ing_id AND prod_ingredients.p_id = ip1.product_id','left');
						$this->fdb->order_by('order','ASC');
						$ingis = $this->fdb->get('prod_ingredients')->result_array();

						/*
						$this->fdb->select('prod_ingredients_vetten.i_id,ingredients.ing_name'.$sel_lang_upd.',ingredients.have_all_id');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('ingredients','prod_ingredients_vetten.i_id = ingredients.ing_id');
						$vetten = $this->fdb->get('prod_ingredients_vetten')->result_array();

						$this->fdb->select('prod_additives.i_id,prod_additives.add_id,ingredients.ing_name'.$sel_lang_upd.',ingredients.have_all_id');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('ingredients','prod_additives.i_id = ingredients.ing_id');
						$add = $this->fdb->get('prod_additives')->result_array();

						*/

						$this->fdb->select('prod_allergence.a_id,allergence.all_name'.$sel_lang_upd.'');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('allergence','prod_allergence.a_id = allergence.all_id');
						$alls = $this->fdb->get('prod_allergence')->result_array();

						$this->fdb->select('sub_allergence.parent_all_id,prod_sub_allergence.a_id,sub_allergence.all_name'.$sel_lang_upd.'');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('sub_allergence','prod_sub_allergence.a_id = sub_allergence.all_id');
						$sub_alls = $this->fdb->get('prod_sub_allergence')->result_array();

						$this->fdb->select('prod_traces.t_id, traces.t_name'.$sel_lang_upd.'');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('traces','prod_traces.t_id = traces.t_id');
						$trs = $this->fdb->get('prod_traces')->result_array();
						

						foreach($my_results as $key=>$result){

							$this->db->where('semi_product_id',$result['obs_pro_id']);
							$this->db->group_by('obs_pro_id');
							$results4 = $this->db->get('fdd_pro_quantity')->result_array();

							foreach ($results4 as $key_p=>$result4){
								$flag = true;

								if(isset($extra_new_ids1) && isset($extra_new_ids1[$result4['obs_pro_id']]) && in_array($to_add, $extra_new_ids1[$result4['obs_pro_id']])){
									$flag = false;
								}
								else{
									continue;
								}

								if(!$flag){
									//adding ingredients for new added fdd product
									$ing_insert_array[] = array(
											'product_id'=>$result4['obs_pro_id'],
											'kp_id'=> $to_add,
											'ki_id'=>0,
											'prefix' => '',
											'ki_name'=> $short_name,
											'display_order'=>1,
											'kp_display_order'=>$key+100,
											'date_added'=>date('Y-m-d h:i:s'),
											'have_all_id' => 0,
											'aller_type' => '0',
											'aller_type_fr' => '0',
											'aller_type_dch' => '0',
											'allergence' => '0',
											'allergence_fr' => '0',
											'allergence_dch' => '0',
											// 'new_allergence'] => $ing_arr[9],
											'sub_allergence' => '0',
											'sub_allergence_fr' => '0',
											'sub_allergence_dch' => '0'
									);

									if(!empty($ingis)){
										$total_ing = 0;
										$ing_insert_array[] = array(
												'product_id'=>$result4['obs_pro_id'],
												'kp_id'=> $to_add,
												'ki_id'=>0,
												'prefix' => '',
												'ki_name'=> '(',
												'display_order'=>2,
												'kp_display_order'=>$key+100,
												'date_added'=>date('Y-m-d h:i:s'),
												'have_all_id' => 0,
												'aller_type' => '0',
												'aller_type_fr' => '0',
												'aller_type_dch' => '0',
												'allergence' => '0',
												'allergence_fr' => '0',
												'allergence_dch' => '0',
												// 'new_allergence'] => $ing_arr[9],
												'sub_allergence' => '0',
												'sub_allergence_fr' => '0',
												'sub_allergence_dch' => '0'
										);

										foreach ($ingis as $ing_key=>$ingi){
											if(!$ingi['prefix']){
												$ingi['prefix'] = '';
											}

											$display_name = $ingi['ing_name'.$sel_lang_upd.''];
											$enbr_setting = $this->get_enbr_status($this->company_id);
											$enbr_result = $this->get_e_current_nbr($ingi['i_id'],$enbr_setting['enbr_status'],$sel_lang_upd);
											if(!empty($enbr_result)){
												$ki_id = $enbr_result['ki_id'];
												$display_name = $enbr_result['ki_name'];
											}

											$ing_insert_array[] = array(
												'product_id'=>$result4['obs_pro_id'],
												'kp_id'=> $to_add,
												'ki_id'=>$ingi['i_id'],
												'prefix' => '',
												'ki_name'=> $display_name,
												'display_order'=>$ing_key+3,
												'kp_display_order'=>$key+100,
												'date_added'=>date('Y-m-d h:i:s'),
												'have_all_id'=>$ingi['have_all_id'],
												'aller_type' => $ingi['aller_type'],
												'aller_type_fr' => $ingi['aller_type_fr'],
												'aller_type_dch' => $ingi['aller_type_dch'],
												'allergence' => $ingi['allergence'],
												'allergence_fr' => $ingi['allergence_fr'],
												'allergence_dch' => $ingi['allergence_dch'],
												// 'new_allergence' => $ingi['new_allergence'],
												'sub_allergence' => $ingi['sub_allergence'],
												'sub_allergence_fr' => $ingi['sub_allergence_fr'],
												'sub_allergence_dch' => $ingi['sub_allergence_dch']
											);

											$total_ing++;
										}

										$ing_insert_array[] = array(
												'product_id'=>$result4['obs_pro_id'],
												'kp_id'=> $to_add,
												'ki_id'=>0,
												'prefix' => '',
												'ki_name'=> ')',
												'display_order'=>$total_ing+3,
												'kp_display_order'=>$key+100,
												'date_added'=>date('Y-m-d h:i:s'),
												'have_all_id' => 0,
												'aller_type' => '0',
												'aller_type_fr' => '0',
												'aller_type_dch' => '0',
												'allergence' => '0',
												'allergence_fr' => '0',
												'allergence_dch' => '0',
												// 'new_allergence'] => $ing_arr[9],
												'sub_allergence' => '0',
												'sub_allergence_fr' => '0',
												'sub_allergence_dch' => '0'
										);
									}

									/*
									foreach ($vetten as $ing_key=>$vet){
										$display_name = $vet['ing_name'.$sel_lang_upd.''];
										$display_result = $this->get_display_name($vet['i_id'],$sel_lang_upd);
										if($display_result != ''){
											$display_name = $display_result;
										}

										$vet_insert_array[] = array(
												'product_id'=>$result4['obs_pro_id'],
												'kp_id'=> $to_add,
												'ki_id'=>$vet['i_id'],
												'ki_name'=> $display_name,
												'display_order'=>$ing_key+3,
												'kp_display_order'=>$key+100,
												'date_added'=>date('Y-m-d h:i:s'),
												'have_all_id'=>$vet['have_all_id']
										);
									}

									foreach ($add as $ing_key=>$ingi){
										$display_name = $ingi['ing_name'.$sel_lang_upd.''];
										$ki_id = $ingi['i_id'];
										$display_name1 = '';
										$enbr_rel_ki_id = 0;

										$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_setting['enbr_status'],$sel_lang_upd);
										if(!empty($enbr_result)){
											$ki_id = $enbr_result['ki_id'];
											$display_name = $enbr_result['ki_name'];
											$display_result = $this->get_display_name($enbr_result['ki_id'],$sel_lang_upd);
											if($display_result != ''){
												$display_name = $display_result;
											}

											$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
											$display_name1 = $enbr_result['enbr_rel_ki_name'];
											$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id'],$sel_lang_upd);
											if($display_result != ''){
												$display_name1 = $display_result;
											}
										}
										else{
											$display_result = $this->get_display_name($ki_id,$sel_lang_upd);
											if($display_result != ''){
												$display_name = $display_result;
											}
										}

										$add_insert_array[] = array(
												'product_id'=>$result4['obs_pro_id'],
												'kp_id'=> $to_add,
												'add_id'=>$ingi['add_id'],
												'ki_id'=>$ki_id,
												'ki_name'=> $display_name,
												'enbr_rel_ki_id' => $enbr_rel_ki_id,
												'enbr_rel_ki_name' => $display_name1,
												'display_order'=>$ing_key+3,
												'kp_display_order'=>$key+100,
												'date_added'=>date('Y-m-d h:i:s'),
												'have_all_id'=>$ingi['have_all_id']
										);
									}
									*/

									//adding allergence
									if(!empty($alls)){
										foreach ($alls as $all_key=>$all){

											$all_insert_array[] = array(
													'product_id'=>$result4['obs_pro_id'],
													'kp_id'=> $to_add,
													'ka_id'=>$all['a_id'],
													'ka_name'=> $all['all_name'.$sel_lang_upd.''],
													'display_order'=>$all_key+1,
													'date_added'=>date('Y-m-d h:i:s')
											);
										}
									}

									//adding suballergence
									if(!empty($sub_alls)){
										foreach ($sub_alls as $all_key=>$all){

											$all_sub_insert_array[] = array(
													'product_id'=>$result4['obs_pro_id'],
													'kp_id'=> $to_add,
													'parent_ka_id'=>$all['parent_all_id'],
													'sub_ka_id'=>$all['a_id'],
													'sub_ka_name'=> $all['all_name'.$sel_lang_upd.''],
													'display_order'=>$all_key+1,
													'date_added'=>date('Y-m-d h:i:s')
											);
										}
									}

									//adding traces of product
									if(!empty($trs)){
										foreach ($trs as $tr_key=>$tr){

											$trs_insert_array[] = array(
													'product_id'=>$result4['obs_pro_id'],
													'kp_id'=> $to_add,
													'kt_id'=>$tr['t_id'],
													'kt_name'=> $tr['t_name'.$sel_lang_upd.''],
													'display_order'=>$tr_key+1,
													'date_added'=>date('Y-m-d h:i:s')
											);
										}
									}
								}
							}
						}
					}
				}

				if(!empty($ing_insert_array))
					$this->db->insert_batch('products_ingredients', $ing_insert_array);

				if(!empty($all_insert_array))
					$this->db->insert_batch('products_allergence',$all_insert_array);

				if(!empty($all_sub_insert_array))
					$this->db->insert_batch('product_sub_allergence',$all_sub_insert_array);

				if(!empty($trs_insert_array))
					$this->db->insert_batch('products_traces',$trs_insert_array);

				if(isset($to_add_new_arr2)){
					foreach ($to_add_new_arr2 as $to_add){
						$short_name= '';

						$this->db->select('proname');
						$this->db->where('id',$to_add);
						$pro_name = $this->db->get('products')->result_array();
						if(!empty($pro_name)){
							$short_name = $pro_name[0]['proname'];
						}

						foreach($my_results as $key=>$result){

							$this->db->where('semi_product_id',$result['obs_pro_id']);
							$this->db->group_by('obs_pro_id');
							$results4 = $this->db->get('fdd_pro_quantity')->result_array();

							foreach ($results4 as $key_p=>$result4){
								$flag = true;

								if(isset($extra_new_ids2) && isset($extra_new_ids2[$result4['obs_pro_id']]) && in_array($to_add, $extra_new_ids2[$result4['obs_pro_id']])){
									$flag = false;
								}
								else{
									continue;
								}

								if(!$flag){
									//adding product name of fixed product in ingredints list
									$ing_insert_array = array(
											'product_id'=>$result4['obs_pro_id'],
											'kp_id'=> $to_add,
											'ki_id'=>0,
											'ki_name'=> $short_name,
											'display_order'=>1,
											'kp_display_order'=>$key+100,
											'date_added'=>date('Y-m-d h:i:s'),
											'is_obs_ing'=>1
									);

									$this->db->insert('products_ingredients',$ing_insert_array);
								}
							}
						}
					}
				}
			}
		}

		return array(
			'custom_pro_total_wt_arr' => $custom_pro_total_wt_arr,
			'custom_pro_total_wt_arr_cust' => $custom_pro_total_wt_arr_cust
		);
	}

	/*
	function update_semi_product_contains(){
		$prod_id = $this->input->post('prod_id');
		$fdd_str = $this->input->post('hidden_fdds_quantity');
		$own_str = $this->input->post('hidden_own_pro_quantity');
		$custom_pro_total_wt_arr = array();

		if($fdd_str == '' && $own_str == ''){
			//no need to update(no changes in containing products)
		}else{
			// when all products form a semi-products got removed
			if($fdd_str == '&&' && $own_str == '&&'){
				$this->db->where('semi_product_id',$prod_id);
				$this->db->group_by('obs_pro_id');
				$results = $this->db->get('fdd_pro_quantity')->result_array();

				foreach ($results as $result){
					$total_wt = 0;
					$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'semi_product_id'=>$prod_id));
					$res1 = $this->db->get('fdd_pro_quantity')->result_array();

					foreach ($res1 as $res){
						$total_wt += $res['quantity'];
					}

					$this->db->select('recipe_weight');
					$this->db->where('id',$result['obs_pro_id']);
					$wt = $this->db->get('products')->result_array();
					if(!empty($wt)){
						$re_wt = $wt[0]['recipe_weight'];
					}else{
						$re_wt = 0;
					}

					$new_wt = ($re_wt*1000)-$total_wt;
					if($new_wt >= 0){
						$new_wt = $new_wt/1000;

						//update custom product recipe weight
						$this->db->where('id',$result['obs_pro_id']);
						$this->db->update('products', array('recipe_weight'=>$new_wt));

						//Delete products used in custom product due to semi product
						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'semi_product_id'=>$prod_id));
						$this->db->delete('fdd_pro_quantity');

						foreach ($res1 as $res){
							//deleting ingredients from custom products
							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('products_ingredients');

							//deleting allergens from custom products
							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('products_allergence');

							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('product_sub_allergence');

							//deleting traces from custom products
							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
							$this->db->delete('products_traces');
						}
					}
				}
			}else{

				//making a array of total weight of semi product used in custom products with their id
				$this->db->where('semi_product_id',$prod_id);
				$this->db->group_by('obs_pro_id');
				$my_results = $this->db->get('fdd_pro_quantity')->result_array();
				foreach ($my_results as $my_result){
					$this->db->select_sum('quantity');
					$this->db->where(array('obs_pro_id'=>$my_result['obs_pro_id'],'semi_product_id'=>$prod_id));
					$sums = $this->db->get('fdd_pro_quantity')->result_array();

					if(!empty($sums)){
						$new_short_arr = array(
								'obs_id' => $my_result['obs_pro_id'],
								'total'=>$sums[0]['quantity']
						);

						$custom_pro_total_wt_arr[] =$new_short_arr;
					}
				}

				$this->db->where(array('obs_pro_id'=>$prod_id));
				$results = $this->db->get('fdd_pro_quantity')->result_array();

				$old_added_array = array();
				foreach ($results as $result){
					$old_added_array[]= $result['fdd_pro_id'];
				}


				$new_added_array1 = array();
				if($fdd_str != '' && $fdd_str != '&&'){
					$new_added= explode('**', $fdd_str);
					foreach ($new_added as $new_added_item){
						if($new_added_item != ''){
							$short_arr = explode("#", $new_added_item);
							$new_added_array1[] = $short_arr[0];
						}
					}
				}


				$new_added_array2 = array();
				if($own_str != '' && $own_str != '&&'){
					$new_added = explode('**', $own_str);
					foreach ($new_added as $new_added_item){
						if($new_added_item != ''){
							$short_arr = explode("#", $new_added_item);
							$new_added_array2[] = $short_arr[0];
						}
					}
				}

				$new_added_array = array_merge($new_added_array1, $new_added_array2);

				$to_del_arr = array_diff($old_added_array, $new_added_array);
				$to_add_arr = array_diff($new_added_array, $old_added_array);

				$this->db->where('semi_product_id',$prod_id);
				$this->db->group_by('obs_pro_id');
				$results1 = $results2 = $this->db->get('fdd_pro_quantity')->result_array();


				foreach ( $to_del_arr as $to_del){

					foreach ($results1 as $result){

						$result_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id !='=>$prod_id))->result_array();
						if(!empty($result_s)){
							$extra_ids[$result['obs_pro_id']] = $to_del;
							for($c = 0; $c < count($result_s); $c++){
								$to_add_arr[] = $to_del;
							}
						}

						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id'=>$prod_id));
						$this->db->delete('fdd_pro_quantity');

						//deleting ingredients, allergens and traces
						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('products_ingredients');

						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('products_allergence');

						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('product_sub_allergence');

						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
						$this->db->delete('products_traces');
					}
				}

				//E-nbr status
				$enbr_setting = $this->get_enbr_status($this->company_id);

				foreach ($to_add_arr as $to_add){

					$short_name= '';
					$ingis = array();
					$alls = array();
					$trs = array();

					if (in_array($to_add, $new_added_array1)) {

						//collecting information of fdd product from fooddesk database
						
						$this->fdb->select('p_short_name_dch');;
						$this->fdb->where('p_id',$to_add);
						$res_short_name = $this->fdb->get('products')->result_array();
						if(!empty($res_short_name)){
							$short_name = $res_short_name[0]['p_short_name_dch'];
						}

						$this->fdb->select('prod_ingredients.i_id,ip1.prefix,ingredients.ing_name_dch,ingredients.have_all_id');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('ingredients','prod_ingredients.i_id = ingredients.ing_id');
						$this->fdb->join('ingredient_prefixes as ip1','prod_ingredients.i_id = ip1.ing_id AND prod_ingredients.p_id = ip1.product_id','left');
						$ingis = $this->fdb->get('prod_ingredients')->result_array();

						$this->fdb->select('prod_ingredients_vetten.i_id,ingredients.ing_name_dch,ingredients.have_all_id');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('ingredients','prod_ingredients_vetten.i_id = ingredients.ing_id');
						$vetten = $this->fdb->get('prod_ingredients_vetten')->result_array();

						$this->fdb->select('prod_additives.i_id,prod_additives.add_id,ingredients.ing_name_dch,ingredients.have_all_id');
						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('ingredients','prod_additives.i_id = ingredients.ing_id');
						$add = $this->fdb->get('prod_additives')->result_array();

						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('allergence','prod_allergence.a_id = allergence.all_id');
						$alls = $this->fdb->get('prod_allergence')->result_array();

						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('sub_allergence','prod_sub_allergence.a_id = sub_allergence.all_id');
						$sub_alls = $this->fdb->get('prod_sub_allergence')->result_array();

						$this->fdb->where('p_id',$to_add);
						$this->fdb->join('traces','prod_traces.t_id = traces.t_id');
						$trs = $this->fdb->get('prod_traces')->result_array();
						
					}elseif (in_array($to_add, $new_added_array2)){
						$this->db->select('proname');
						$this->db->where('id',$to_add);
						$pro_name = $this->db->get('products')->result_array();
						if(!empty($pro_name)){
							$short_name = $pro_name[0]['proname'];
						}
					}

					foreach($results2 as $key=>$result){
						$flag = true;

						if(isset($extra_ids) && isset($extra_ids[$result['obs_pro_id']])){
							$flag = false;
						}

						if (in_array($to_add, $new_added_array1)) {
							//added new fdd product as a product of semi product in custom product
							if($flag){
								$new_added= explode('**', $fdd_str);
								$unit = 'g';
								foreach ($new_added as $new_added_item){
									if($new_added_item != ''){
										$short_arr = explode("#", $new_added_item);
										if($short_arr[0] == $to_add){
											$unit = $short_arr[4];
											break;
										}
									}
								}
								$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>0,'obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$prod_id,'unit'=>$unit));
							}
							//adding ingredients for new added fdd product
							$ing_insert_array = array(
									'product_id'=>$result['obs_pro_id'],
									'kp_id'=> $to_add,
									'ki_id'=>0,
									'ki_name'=> $short_name,
									'display_order'=>1,
									'kp_display_order'=>$key+100,
									'date_added'=>date('Y-m-d h:i:s')
							);

							$this->db->insert('products_ingredients',$ing_insert_array);

							if(!empty($ingis)){
								$total_ing = 0;
								$ing_insert_array = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=>0,
										'ki_name'=> '(',
										'display_order'=>2,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s')
								);
								$this->db->insert('products_ingredients',$ing_insert_array);

								foreach ($ingis as $ing_key=>$ingi){
									if(!$ingi['prefix']){
										$ingi['prefix'] = '';
									}

									$display_name = $ingi['ing_name_dch'];
									$display_result = $this->get_display_name($ingi['i_id']);
									if($display_result != ''){
										$display_name = $display_result;
									}

									$ing_insert_array = array(
											'product_id'=>$result['obs_pro_id'],
											'kp_id'=> $to_add,
											'ki_id'=>$ingi['i_id'],
											'ki_name'=> $display_name,
											'prefix'=> $ingi['prefix'],
											'display_order'=>$ing_key+3,
											'kp_display_order'=>$key+100,
											'date_added'=>date('Y-m-d h:i:s'),
											'have_all_id'=>$ingi['have_all_id']
									);
									$this->db->insert('products_ingredients',$ing_insert_array);
									$total_ing++;
								}

								$ing_insert_array = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=>0,
										'ki_name'=> ')',
										'display_order'=>$total_ing+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s')
								);
								$this->db->insert('products_ingredients',$ing_insert_array);
							}

							foreach ($vetten as $ing_key=>$vet){
								$display_name = $vet['ing_name_dch'];
								$display_result = $this->get_display_name($vet['i_id']);
								if($display_result != ''){
									$display_name = $display_result;
								}

								$ing_insert_array = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'ki_id'=>$vet['i_id'],
										'ki_name'=> $display_name,
										'display_order'=>$ing_key+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id'=>$vet['have_all_id']
								);
								$this->db->insert('products_ingredients_vetten',$ing_insert_array);
							}

							foreach ($add as $ing_key=>$ingi){
								$display_name = $ingi['ing_name_dch'];
								$ki_id = $ingi['i_id'];
								$display_name1 = '';
								$enbr_rel_ki_id = 0;

								$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_setting['enbr_status']);
								if(!empty($enbr_result)){
									$ki_id = $enbr_result['ki_id'];
									$display_name = $enbr_result['ki_name'];
									$display_result = $this->get_display_name($enbr_result['ki_id']);
									if($display_result != ''){
										$display_name = $display_result;
									}

									$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
									$display_name1 = $enbr_result['enbr_rel_ki_name'];
									$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id']);
									if($display_result != ''){
										$display_name1 = $display_result;
									}
								}
								else{
									$display_result = $this->get_display_name($ki_id);
									if($display_result != ''){
										$display_name = $display_result;
									}
								}

								$ing_insert_array = array(
										'product_id'=>$result['obs_pro_id'],
										'kp_id'=> $to_add,
										'add_id'=>$ingi['add_id'],
										'ki_id'=>$ki_id,
										'ki_name'=> $display_name,
										'enbr_rel_ki_id' => $enbr_rel_ki_id,
										'enbr_rel_ki_name' => $display_name1,
										'display_order'=>$ing_key+3,
										'kp_display_order'=>$key+100,
										'date_added'=>date('Y-m-d h:i:s'),
										'have_all_id'=>$ingi['have_all_id']
								);
								$this->db->insert('products_additives',$ing_insert_array);
							}

							//adding allergence
							if(!empty($alls)){
								foreach ($alls as $all_key=>$all){

									$ing_insert_array = array(
											'product_id'=>$result['obs_pro_id'],
											'kp_id'=> $to_add,
											'ka_id'=>$all['a_id'],
											'ka_name'=> $all['all_name_dch'],
											'display_order'=>$all_key+1,
											'date_added'=>date('Y-m-d h:i:s')
									);
									$this->db->insert('products_allergence',$ing_insert_array);
								}
							}

							//adding suballergence
							if(!empty($sub_alls)){
								foreach ($sub_alls as $all_key=>$all){

									$ing_insert_array = array(
											'product_id'=>$result['obs_pro_id'],
											'kp_id'=> $to_add,
											'parent_ka_id'=>$all['parent_all_id'],
											'sub_ka_id'=>$all['a_id'],
											'sub_ka_name'=> $all['all_name_dch'],
											'display_order'=>$all_key+1,
											'date_added'=>date('Y-m-d h:i:s')
									);
									$this->db->insert('product_sub_allergence',$ing_insert_array);
								}
							}

							//adding traces of product
							if(!empty($trs)){
								foreach ($trs as $tr_key=>$tr){

									$ing_insert_array = array(
											'product_id'=>$result['obs_pro_id'],
											'kp_id'=> $to_add,
											'kt_id'=>$tr['t_id'],
											'kt_name'=> $tr['t_name_dch'],
											'display_order'=>$tr_key+1,
											'date_added'=>date('Y-m-d h:i:s')
									);
									$this->db->insert('products_traces',$ing_insert_array);
								}
							}
						}else if (in_array($to_add, $new_added_array2)) {
							//added new own product as a product od semi product in custom product
							$new_added = explode('**', $own_str);
							$unit = 'g';
							foreach ($new_added as $new_added_item){
								if($new_added_item != ''){
									$short_arr = explode("#", $new_added_item);
									if($short_arr[0] == $to_add){
										$unit = $short_arr[4];
										break;
									}
								}
							}
							$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>1,'obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$prod_id,'unit'=>$unit));
							$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$to_add))->result_array();
							if(empty($con_info)){
								$this->db->insert('contacted_via_mail', array('obs_pro_id' => $to_add));
							}

							//adding product name of fixed product in ingredints list
							$ing_insert_array = array(
									'product_id'=>$result['obs_pro_id'],
									'kp_id'=> $to_add,
									'ki_id'=>0,
									'ki_name'=> $short_name,
									'display_order'=>1,
									'kp_display_order'=>$key+100,
									'date_added'=>date('Y-m-d h:i:s'),
									'is_obs_ing'=>1
							);

							$this->db->insert('products_ingredients',$ing_insert_array);
						}
					}
				}
			}
		}
		RETURN $custom_pro_total_wt_arr;
	} */


	/**
	 * This function updates semi product quantity
	 * @param array $custom_pro_total_wt_total_array
	 */
	function update_semi_product_quant($custom_pro_total_wt_total_array = array()){
		$prod_id = $this->input->post('prod_id');

		$this->db->select_sum('quantity');
		$this->db->where('obs_pro_id',$prod_id);
		$semi_total = $this->db->get('fdd_pro_quantity')->result_array();
		$semi_pro_total = 100;
		if(!empty($semi_total)){
			$semi_pro_total = $semi_total[0]['quantity'];
		}

		$custom_pro_total_wt_arr=$custom_pro_total_wt_total_array['custom_pro_total_wt_arr'];
		$custom_pro_total_wt_arr_cust=$custom_pro_total_wt_total_array['custom_pro_total_wt_arr_cust'];

		if(!empty($custom_pro_total_wt_arr)){
			foreach ($custom_pro_total_wt_arr as $total_arr){
				$this->db->select('id,fdd_pro_id,is_obs_product');
				$this->db->where(array('obs_pro_id'=>$total_arr['obs_id'],'semi_product_id'=>$prod_id));
				$results = $this->db->get('fdd_pro_quantity')->result_array();
				if(!empty($results)){
					foreach ($results as $result){

						$this->db->select('quantity');
						$this->db->where(array('is_obs_product'=>$result['is_obs_product'],'fdd_pro_id'=>$result['fdd_pro_id'],'obs_pro_id'=>$prod_id));
						$wt = $this->db->get('fdd_pro_quantity')->result_array();
						if(!empty($wt)){
							$pro_wt = $wt[0]['quantity'];
							$calculated_weight = $this->number2db(round(($total_arr['total']/$semi_pro_total)*$pro_wt,2));
							if ($calculated_weight == 0)
							{
								$calculated_weight = $this->number2db(0.01);
							}
							$this->db->where(array('id'=>$result['id']));
							$this->db->update('fdd_pro_quantity',array('quantity'=>$calculated_weight));
						}
					}
				}
				$this->db->select_sum('quantity');
				$this->db->where('obs_pro_id',$total_arr['obs_id']);
				$semi_total = $this->db->get('fdd_pro_quantity')->result_array();
				$semi_pro_total_cup = 100;
				if(!empty($semi_total)){
					$semi_pro_total_cup = $semi_total[0]['quantity'];
				}

				if(!empty($custom_pro_total_wt_arr_cust)){
					foreach ($custom_pro_total_wt_arr_cust as $total_arr_cust){

						$this->db->select('id,fdd_pro_id,is_obs_product');
						$this->db->where(array('obs_pro_id'=>$total_arr_cust['obs_id'],'semi_product_id'=>$total_arr['obs_id']));
						$results_cup = $this->db->get('fdd_pro_quantity')->result_array();

						if(!empty($results_cup)){
							foreach ($results_cup as $result){

								$this->db->select('quantity');
								$this->db->where(array('is_obs_product'=>$result['is_obs_product'],'fdd_pro_id'=>$result['fdd_pro_id'],'obs_pro_id'=>$total_arr['obs_id']));
								$wt = $this->db->get('fdd_pro_quantity')->result_array();

								if(!empty($wt)){
									$pro_wt = $wt[0]['quantity'];
									$calculated_weight = $this->number2db(round(($total_arr_cust['total']/$semi_pro_total_cup)*$pro_wt,2));

									if ($calculated_weight == 0)
									{
										$calculated_weight = $this->number2db(0.01);
									}
									$this->db->where(array('id'=>$result['id']));
									$this->db->update('fdd_pro_quantity',array('quantity'=>$calculated_weight));
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * This function updates kp_display_order for obs products having current semi product
	 * @param number $prod_id
	 */
	private function update_kp_display_order($prod_id = 0){
		$this->db->select('obs_pro_id');
		$this->db->where('semi_product_id',$prod_id);
		$this->db->group_by('obs_pro_id');
		$results = $this->db->get('fdd_pro_quantity')->result_array();

		if(!empty($results)){
			foreach ($results as $result){
				$this->db->select('fdd_pro_id,is_obs_product');
				$this->db->where('obs_pro_id',$result['obs_pro_id']);
				$this->db->order_by('quantity','asc');
				$result_fdd = $this->db->get('fdd_pro_quantity')->result_array();

				if(!empty($result_fdd)){
					foreach ($result_fdd as $key=>$res){
						$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id'],'is_obs_ing'=>$res['is_obs_product']));
						$this->db->update('products_ingredients',array('kp_display_order'=>sizeof($result_fdd)-$key));

						if($res['is_obs_product'] == 0){
							$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
							$this->db->update('products_ingredients_vetten',array('kp_display_order'=>sizeof($result_fdd)-$key));

							$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
							$this->db->update('products_additives',array('kp_display_order'=>sizeof($result_fdd)-$key));
						}
					}
				}

				$this->db->select('obs_pro_id');
				$this->db->where('semi_product_id',$result['obs_pro_id']);
				$this->db->group_by('obs_pro_id');
				$results_cup = $this->db->get('fdd_pro_quantity')->result_array();

				if(!empty($results_cup)){
					foreach ($results_cup as $result_cup){

						$this->db->select('fdd_pro_id,is_obs_product');
						$this->db->where('obs_pro_id',$result_cup['obs_pro_id']);
						$this->db->order_by('quantity','asc');
						$result_fdd_cup = $this->db->get('fdd_pro_quantity')->result_array();

						if(!empty($result_fdd_cup)){
							foreach ($result_fdd_cup as $key=>$res){
								$this->db->where(array('product_id'=>$result_cup['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id'],'is_obs_ing'=>$res['is_obs_product']));
								$this->db->update('products_ingredients',array('kp_display_order'=>sizeof($result_fdd_cup)-$key));

								if($res['is_obs_product'] == 0){
									$this->db->where(array('product_id'=>$result_cup['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
									$this->db->update('products_ingredients_vetten',array('kp_display_order'=>sizeof($result_fdd_cup)-$key));

									$this->db->where(array('product_id'=>$result_cup['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
									$this->db->update('products_additives',array('kp_display_order'=>sizeof($result_fdd_cup)-$key));
								}
							}
						}
					}
				}
			}
		}
	}

/* 	function get_recipe_data(){
		$this->db->select('id');
		$this->db->where('company_id',$this->company_id);
		$this->db->where('direct_kcp_id !=',0);
		$k_product = $this->db->get('products')->result_array();

		$k_id = array();
		foreach ($k_product as $k_val){
			$k_id[] = $k_val['id'];
		}

		$this->db->distinct();
		$this->db->select('fdd_pro_quantity.fdd_pro_id');
		$this->db->order_by('fdd_pro_quantity.fdd_pro_id');
		$this->db->where(array('fdd_pro_quantity.is_obs_product'=>0,'products.company_id'=>$this->company_id,'products.direct_kcp'=>0));
		if(!empty($k_id))
			$this->db->where_not_in('fdd_pro_quantity.obs_pro_id', $k_id);
		$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
		$result = $this->db->get('fdd_pro_quantity')->result_array();

		
		foreach ($result as $key=>$val){
			$this->fdb->where('p_id',$val['fdd_pro_id']);
			$this->fdb->join('suppliers', 'products.p_s_id = suppliers.s_id');
			$fdd_result = $this->fdb->get('products')->result_array();
			$result[$key]['p_name_dch'] = $fdd_result[0]['p_name_dch'];
			$result[$key]['s_name'] = $fdd_result[0]['s_name'];
			$result[$key]['barcode'] = $fdd_result[0]['barcode'];
			$result[$key]['data_sheet'] = $fdd_result[0]['data_sheet'];
		}
		return $result;
	}

	function get_recipe_product($fdd_pro_id = 0){
		$result =array();

		if($fdd_pro_id != 0){
			$this->db->select(array('products.id','products.pro_art_num','products.proname','products.prodescription'));
			$this->db->order_by('products.proname','asc');
			$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$fdd_pro_id,'products.company_id'=>$this->company_id,'direct_kcp'=>0));
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$result = $this->db->get('fdd_pro_quantity')->result_array();
		}
		return $result;
	} */

	/**
	 * This function adds product info section of single product
	 * @return number $new_product_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function add_product_info(){
		$pro_num = $this->input->post('pro_art_num');
		if(strlen(trim($pro_num) < 8)){
			$pro_num = str_pad(trim($pro_num),8,"0",STR_PAD_LEFT);
		}
		$add_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'pro_art_num' => $pro_num,
				'proname' => addslashes($this->input->post('proname')),
				'prodescription' => addslashes($this->input->post('prodescription')),
				'image_display'=>"1",
				'procreated'=>date('Y-m-d',time()),
				'type'=>($this->input->post('type'))?$this->input->post('type'):'0'
		);

		if($this->input->post('producer') != NULL && $this->input->post('producer') != 0 && $this->input->post('producer') != ''){
			$prdcr_id = $this->input->post('producer');

			if($prdcr_id == -1){
				$new_producer = $this->input->post('new_producer');

				$s_username = str_replace(' ', '', $new_producer);
				$insrt_array = array(
						's_name'=>addslashes($new_producer),
						's_username' => $s_username,
						's_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
						's_date_added' => date('Y-m-d h:i:s')
				);
				
				$this->fdb->insert('suppliers', $insrt_array);
				$prdcr_id = $this->fdb->insert_id();

				
			}

			$add_product_data['fdd_producer_id'] = $prdcr_id;

		}

		if($this->input->post('supplier') != NULL && $this->input->post('supplier') != 0 && $this->input->post('supplier') != ''){
			$splr_id = $this->input->post('supplier');

			if($splr_id == -1){
				$new_supplier = $this->input->post('new_supplier');

				$s_username = str_replace(' ', '', $new_supplier);
				$insrt_array = array(
						'rs_name'=>addslashes($new_supplier),
						'rs_username' => $s_username,
						'rs_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
						'rs_date_added' => date('Y-m-d h:i:s')
				);
				
				$this->fdb->insert('real_suppliers', $insrt_array);
				$splr_id = $this->fdb->insert_id();

				
			}

			$add_product_data['fdd_supplier_id'] = $splr_id;
		}


		if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
			$add_product_data['direct_kcp'] = $this->input->post('product_type');
		}

		if($this->input->post('prod_id') && $this->input->post('prod_id') != 'undefined'){
			$new_product_id = $this->input->post('prod_id');
			$this->db->where('id',$new_product_id);
			$this->db->update('products', $add_product_data);
		}
		else{
			$query = $this->db->insert('products', $add_product_data);
			$new_product_id = $this->db->insert_id();//method to get id of last inserted row//
		}

		if($this->input->post('image_name')){
			$prefix = 'cropped_'.$this->company_id.'_';
			$str = $this->input->post('image_name');

			if (substr($str, 0, strlen($prefix)) == $prefix) {
				$str = substr($str, strlen($prefix));
			}
			if(isset($str) && $str != ''){
				$this->image = $this->company_id.'_'.$new_product_id.'_'.$str;
				$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));
				file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$this->image, $image_file);

				if($this->image){
					//$this->resize_product_images($this->image);
					$this->load->helper('resize');
					resize_images('product',$this->image);

					$update_product_data['image'] = $this->image;
					$this->db->where('id',$new_product_id);
					$this->db->update('products', $update_product_data);
				}
			}
		}

		return $new_product_id;
	}

	/**
	 * This function updates product info section of single product
	 * @return number
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function update_product_info(){
		$rotated_image = $this->input->post('rotated_image');
		$current_prod_img = $this->input->post('current_prod_img');

		if ($rotated_image != "")
		{
			if (file_exists(dirname(__FILE__).'/../../assets/cp/images/product/'.$current_prod_img))
			{
				unlink(dirname(__FILE__).'/../../assets/cp/images/product/'.$current_prod_img);
				$file_cont = file_get_contents(dirname(__FILE__).'/../../assets/temp_uploads/'.$rotated_image);
				file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$current_prod_img,$file_cont);
			}
		}

		if($this->input->post('image_name')){
			$prefix = 'cropped_'.$this->company_id.'_';
			$str = $this->input->post('image_name');

			if (substr($str, 0, strlen($prefix)) == $prefix) {
				$str = substr($str, strlen($prefix));
			}
			if(isset($str) && $str != ''){
				$this->image = $this->company_id.'_'.$this->input->post('prod_id').'_'.$str;
				$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));
				file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$this->image, $image_file);

				if($this->image){
					//$this->resize_product_images($this->image);
					$this->load->helper('resize');
					resize_images('product',$this->image,false,$this->input->post('prod_id'));
				}
			}
		}


		/*
		 *updating product
		*/
		$pro_num = $this->input->post('pro_art_num');
		if(strlen(trim($pro_num) < 8)){
			$pro_num = str_pad(trim($pro_num),8,"0",STR_PAD_LEFT);
		}
		$update_product_data = array(
				'company_id' => $this->company_id,
				'categories_id' => $this->input->post('categories_id'),
				'subcategories_id' => $this->input->post('subcategories_id'),
				'pro_art_num' => $pro_num,
				'proname' => addslashes($this->input->post('proname')),
				'prodescription' => addslashes($this->input->post('prodescription')),
				'image_display' => "1",
				'proupdated' => date('Y-m-d',time()),
				'type' => ($this->input->post('type'))?$this->input->post('type'):'0',
				'is_custom_pending'=>0,
				'changed_fixed_product_id'=>0
		);

		if($this->image){
			$update_product_data['image']=$this->image;
		}

		if($this->input->post('producer') != NULL && $this->input->post('producer') != 0 && $this->input->post('producer') != ''){
			$prdcr_id = $this->input->post('producer');

			if($prdcr_id == -1){
				$new_producer = $this->input->post('new_producer');

				$s_username = str_replace(' ', '', $new_producer);
				$insrt_array = array(
						's_name'=>addslashes($new_producer),
						's_username' => $s_username,
						's_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
						's_date_added' => date('Y-m-d h:i:s')
				);
				
				$this->fdb->insert('suppliers', $insrt_array);
				$prdcr_id = $this->fdb->insert_id();

				
			}

			$update_product_data['fdd_producer_id'] = $prdcr_id;

		}

		if($this->input->post('supplier') != NULL && $this->input->post('supplier') != 0 && $this->input->post('supplier') != ''){
			$splr_id = $this->input->post('supplier');

			if($splr_id == -1){
				$new_supplier = $this->input->post('new_supplier');

				$s_username = str_replace(' ', '', $new_supplier);
				$insrt_array = array(
						'rs_name'=>addslashes($new_supplier),
						'rs_username' => $s_username,
						'rs_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
						'rs_date_added' => date('Y-m-d h:i:s')
				);
				
				$this->fdb->insert('real_suppliers', $insrt_array);
				$splr_id = $this->fdb->insert_id();

				
			}

			$update_product_data['fdd_supplier_id'] = $splr_id;
		}

		if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
			$update_product_data['direct_kcp'] = $this->input->post('product_type');
		}
		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		return $this->input->post('prod_id');
	}

	/**
	 * This function add allergence in custom product
	 * @return number $new_product_id
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function add_product_allergence(){
		if($this->input->post('prod_id')){
			$new_product_id = $this->update_product_allergence();
		}
		else
		{
			$add_product_data = array(
					'company_id' => $this->company_id,
					'categories_id'=>$this->input->post('categories_id'),
					'subcategories_id'=>$this->input->post('subcategories_id'),
					'proname' => addslashes($this->input->post('proname')),
					'procreated'=>date('Y-m-d',time())
			);
			if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
				$add_product_data['direct_kcp'] = $this->input->post('product_type');
			}
			$cust_prod_aller = $this->input->post('allergence_list');

			$aller_str = '';
			if (!empty($cust_prod_aller)){
				foreach ($cust_prod_aller as $key => $val){
					$aller_str.=$val."#";
				}
				$aller_str = rtrim($aller_str,"#");
			}

			$add_product_data['allergence'] = $aller_str;

			$query = $this->db->insert('products', $add_product_data);
			$new_product_id = $this->db->insert_id();

			return $new_product_id;
		}
	}

	/**
	 * This function update allergence in custom product
	 * @return number
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function update_product_allergence(){
		/*
		 *Updating product
		*/
		$update_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'proupdated' => date('Y-m-d',time()),
				'is_custom_pending'=>0,
				'changed_fixed_product_id'=>0
		);
		if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
			$update_product_data['direct_kcp'] = $this->input->post('product_type');
		}

		$cust_prod_aller = $this->input->post('allergence_list');
		$aller_str = '';
		if (!empty($cust_prod_aller)){
			foreach ($cust_prod_aller as $key => $val){
				if( $val != 'on' ){
					$aller_str.=$val."#";
				}
			}
			$aller_str = rtrim($aller_str,"#");
		}

		$update_product_data['allergence'] = $aller_str;

		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		return $this->input->post('prod_id');
	}

	/**
	 * This function adds recipe section of single product
	 * @return number $new_product_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function add_recipe($sel_lang = 'nl_NL'){
		if ($sel_lang == 'fr_FR'){
			$sel_lang_upd = '_fr';
		}elseif($sel_lang == 'nl_NL'){
			$sel_lang_upd = '_dch';
		}else{
			$sel_lang_upd = '_dch';
		}

		if($this->input->post('prod_id')){
			$new_product_id = $this->update_recipe($sel_lang);
		}
		else{
			$pro_num = $this->input->post('pro_art_num');
			if(strlen(trim($pro_num) < 8)){
				$pro_num = str_pad(trim($pro_num),8,"0",STR_PAD_LEFT);
			}
			$add_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'pro_art_num' => $pro_num,
				'proname' => addslashes($this->input->post('proname')),
				'prodescription' => addslashes($this->input->post('prodescription')),
				'procreated'=>date('Y-m-d',time()),
				'recipe_method' => $this->input->post('recipe_method_txt')
			);

			if($this->input->post('is_custom_semi') == 1){
				$add_product_data['semi_product'] = 1;
				$add_product_data['recipe_weight'] = $this->input->post('recipe_weight');
			}
			else{
				$add_product_data['semi_product'] = 0;
			}

			// If company not keurslager associate then add normal ingredients
			if(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')){
				$add_product_data['ingredients'] = $this->input->post('ingredients');
				$add_product_data['allergence'] = $this->input->post('allergence');
				$add_product_data['traces_of'] = $this->input->post('traces_of');
			}

			if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
				$add_product_data['direct_kcp'] = $this->input->post('product_type');
				if($add_product_data['direct_kcp'] == 0){
					$add_product_data['recipe_weight'] = $this->input->post('recipe_weight');
				}
			}

			$query = $this->db->insert('products', $add_product_data);
			$new_product_id = $this->db->insert_id();//method to get id of last inserted row//


			$this->delete_notcontaining_gs1_products();
			//adding fdd products quantity
			$quant_array =$this->input->post('hidden_fdds_quantity');
			if($quant_array != '' && $quant_array != '&&'){
				$quant_array = substr($quant_array, 0, -2);
				$quant_arr = explode('**', $quant_array);
				foreach ($quant_arr as $quant_ar){
					$insrt_gs1_array = array();
					$quant_ar_ar = explode('#', $quant_ar);
					$gs1_pro_check = explode('--', $quant_ar_ar[2]);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}

					$insrt_quant_array = array(
							'obs_pro_id'=>$new_product_id,
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'semi_product_id'=>$semi_pro_id,
							'fixed'=>$quant_ar_ar[6]
					);
					
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

					if ((isset($gs1_pro_check[4]) && $gs1_pro_check[4] == 'GS1')){
						$insrt_gs1_array = array(
								'gs1_pid'=>$quant_ar_ar[0],
								'company_id'=>$this->company_id
						);
					}

					if(!empty($insrt_gs1_array)){
						$this->db->select('request_status');
						$gs1_exist = $this->db->get_where('request_gs1',array('gs1_pid'=>$quant_ar_ar[0]))->result_array();

						if (empty($gs1_exist)){
							$insrt_gs1_array[ 'date_added' ] = date( "Y-m-d H:i:s" );
							$this->db->insert('request_gs1',$insrt_gs1_array);
						}
						else{
							$request_status = $gs1_exist[0]['request_status'];
							$gs1_exist = $this->db->get_where('request_gs1',array('company_id'=>$this->company_id,'gs1_pid'=>$quant_ar_ar[0]))->result_array();
							if (empty($gs1_exist))
							{
								$insrt_gs1_array[ 'date_added' ] 	= date( "Y-m-d H:i:s" );
								$insrt_gs1_array['request_status'] 	= $request_status;
								$this->db->insert('request_gs1',$insrt_gs1_array);
							}
						}
					}
				}
			}

			$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
			if($obs_quant_array != '' && $obs_quant_array != '&&'){
				$obs_quant_array = substr($obs_quant_array, 0, -2);
				$quant_arr = explode('**', $obs_quant_array);
				foreach ($quant_arr as $quant_ar){
					$quant_ar_ar = explode('#', $quant_ar);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}

					$insrt_quant_array = array(
							'obs_pro_id'=>$new_product_id,
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'is_obs_product'=>1,
							'semi_product_id'=>$semi_pro_id,
							'fixed'=>1
					);
					
					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

					if($semi_pro_id == 0){
						$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$this->company_id))->result_array();
						if(empty($info)){
							$this->db->insert('products_pending', array('product_id' => $quant_ar_ar[0], 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
						}

						$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
						if(empty($con_info)){
							$this->db->insert('contacted_via_mail', array('obs_pro_id' => $quant_ar_ar[0]));
						}
					}
				}
			}

			If($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
				// Adding Ingredients

				$this->adding_ingredients($new_product_id,$sel_lang_upd);

				// Adding Traces
				$this->adding_traces($new_product_id);

				// Adding Allergence
				$this->adding_allergence($new_product_id);
			}
		}
		//-------------------------------------------------------------------------//
		return $new_product_id;
	}

	/**
	 * This function updates recipe section of single product
	 * @return number
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function update_recipe($sel_lang = 'nl_NL'){
		if ($sel_lang == 'fr_FR'){
			$sel_lang_upd = '_fr';
		}elseif($sel_lang == 'nl_NL'){
			$sel_lang_upd = '_dch';
		}else{
			$sel_lang_upd = '_dch';
		}

		if( $this->input->post('is_custom_semi') == 1 ){
			$custom_pro_total_wt_arr = $this->update_semi_product_contains($sel_lang_upd);
		}

		$pro_id = $this->input->post('prod_id');
		
		$this->db->select( 'prod_sent' );
		$this->db->where( 'id', $pro_id );
		$is_prod_sent = $this->db->get( 'products' )->row_array();
		
		if( $is_prod_sent[ 'prod_sent' ] == '1' ){
			$sent_prod_approved = $this->mark_sent_prod_as_approved( $pro_id );
		}
		
		$this->db->select('id,company_id');
		$shared_product_id = $this->db->get_where('products',array('parent_proid'=>$pro_id))->result_array();
		if (!empty($shared_product_id)){
			//$shared_product_id[] = array('id'=>$pro_id,'company_id'=>$this->company_id);
			$this->update_shared_product_recipe($shared_product_id);
		}

		//updating fdd products quantity
		$insrt_quant_array = array();


		$quant_array = $this->input->post('hidden_fdds_quantity');

		if($quant_array != ''){
			if($quant_array == '&&'){
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_gs1_products();
			}else{
				$quant_array = substr($quant_array, 0, -2);
				$quant_arr = explode('**', $quant_array);

				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
				$this->db->delete('fdd_pro_quantity');


				foreach ($quant_arr as $quant_ar){
					$insrt_gs1_array = array();
					$quant_ar_ar = explode('#', $quant_ar);
					$gs1_pro_check = explode('--', $quant_ar_ar[2]);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}

					$insrt_quant_array[] = array(
							'obs_pro_id'=>$this->input->post('prod_id'),
							'fdd_pro_id'=>$quant_ar_ar[0],
							'quantity'=>$quant_ar_ar[1],
							'unit'=>$quant_ar_ar[4],
							'is_obs_product'=>0,
							'semi_product_id'=>$semi_pro_id,
							'fixed' => $quant_ar_ar[6]
					);

					if ((isset($gs1_pro_check[4]) && $gs1_pro_check[4] == 'GS1')){
						$insrt_gs1_array = array(
							'gs1_pid'=>$quant_ar_ar[0],
							'company_id'=>$this->company_id
						);
					}

					if(!empty($insrt_gs1_array)){

						$this->db->select('request_status');
						$gs1_exist = $this->db->get_where('request_gs1',array('gs1_pid'=>$quant_ar_ar[0]))->result_array();

						if (empty($gs1_exist)){
							$insrt_gs1_array[ 'date_added' ] = date( "Y-m-d H:i:s" );
							$this->db->insert('request_gs1',$insrt_gs1_array);
						}
						else{
							$request_status = $gs1_exist[0]['request_status'];
							$gs1_exist = $this->db->get_where('request_gs1',array('company_id'=>$this->company_id,'gs1_pid'=>$quant_ar_ar[0]))->result_array();
							if (empty($gs1_exist))
							{
								$insrt_gs1_array[ 'date_added' ] 	= date( "Y-m-d H:i:s" );
								$insrt_gs1_array['request_status'] 	= $request_status;
								$this->db->insert('request_gs1',$insrt_gs1_array);
							}
						}
					}
				}
			}
		}

		$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
		if($obs_quant_array != ''){
			if($obs_quant_array == '&&'){
				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_products();
			}else{
				$obs_quant_array = substr($obs_quant_array, 0, -2);
				$quant_arr = explode('**', $obs_quant_array);

				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
				$this->db->delete('fdd_pro_quantity');
				$this->delete_notcontaining_products();

				$pending_insert = array();
				$contacted_insert = array();
				foreach ($quant_arr as $quant_ar){
					$quant_ar_ar = explode('#', $quant_ar);

					$semi_pro_id = 0;
					if($quant_ar_ar[5] != NULL){
						$semi_pro_id = $quant_ar_ar[5];
					}

					$insrt_quant_array[] = array(
							'obs_pro_id'		=> $this->input->post('prod_id'),
							'fdd_pro_id'		=> $quant_ar_ar[0],
							'quantity'			=> $quant_ar_ar[1],
							'unit'				=> $quant_ar_ar[4],
							'is_obs_product'	=> 1,
							'semi_product_id'	=> $semi_pro_id,
							'fixed'				=> '1'
					);

					if($semi_pro_id == 0){
						$this->db->select('id');
						$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$this->company_id))->result_array();
						if(empty($info)){
							$pending_insert[] = array(
									'product_id' => $quant_ar_ar[0],
									'company_id' => $this->company_id,
									'date' => date('Y-m-d h:i:s')
							);
						}

						$this->db->select('id');
						$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
						if(empty($con_info)){
							$contacted_insert[] = array(
									'obs_pro_id' => $quant_ar_ar[0]
							);
						}
					}
				}

				if(!empty($pending_insert))
					$this->db->insert_batch('products_pending', $pending_insert);

				if(!empty($contacted_insert))
					$this->db->insert_batch('contacted_via_mail', $contacted_insert);
			}
		}

		if(!empty($insrt_quant_array)){
			$this->db->insert_batch('fdd_pro_quantity',$insrt_quant_array);
		}

		$this->delete_notcontaining_gs1_products();

		/*
		 *updating product
		*/
		$update_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'proupdated' => date('Y-m-d',time()),
				'is_custom_pending'=>0,
				'changed_fixed_product_id'=>0,
				'recipe_method' => $this->input->post('recipe_method_txt')
		);

		if($this->input->post('is_custom_semi') == 1 ){
			$update_product_data['semi_product'] = 1;
		}
		else{
			$update_product_data['semi_product'] = 0;

			$this->db->select('obs_pro_id,fdd_pro_id');
			$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$this->input->post('prod_id')))->result_array();
			foreach ($semi_info as $semi){
				$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
				$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
				$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
				$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
			}
			$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$this->input->post('prod_id')));
		}
		// If company not keurslager associate then add normal ingredients
		If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' ||$this->session->userdata('menu_type') == 'fdd_premium')){
			$update_product_data['ingredients'] = $this->input->post('ingredients');
			$update_product_data['allergence'] = $this->input->post('allergence');
			$update_product_data['traces_of'] = $this->input->post('traces_of');
		}

		if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
			$update_product_data['direct_kcp'] = $this->input->post('product_type');
		}

		$update_product_data['recipe_weight'] = $this->input->post('recipe_weight');

		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){
			/**
			 * Updating Product Ingredients
			 */
			$this->db->delete('products_ingredients',array('product_id' => $this->input->post('prod_id')));

			// Adding Ingredients
			$this->adding_ingredients($this->input->post('prod_id'), $sel_lang_upd);

			/**
			 * Updating Product Traces
			*/
			$this->db->delete('products_traces',array('product_id' => $this->input->post('prod_id')));

			// Adding Traces
			$this->adding_traces($this->input->post('prod_id'));

			/**
			 * Updating Product Allergence
			*/
			$this->db->delete('products_allergence',array('product_id' => $this->input->post('prod_id')));
			$this->db->delete('product_sub_allergence',array('product_id' => $this->input->post('prod_id')));

			// Adding Allergence
			$this->adding_allergence($this->input->post('prod_id'));
		}

		if($this->input->post('is_custom_semi') == 1){
			$this->update_semi_product_quant($custom_pro_total_wt_arr);
			$this->update_kp_display_order($this->input->post('prod_id'));
		}

		return $this->input->post('prod_id');
	}

	function update_shared_product_recipe($shared_product_id = array()){
		if (!empty($shared_product_id)){
			foreach ($shared_product_id as $key => $val){
				$quant_array = $this->input->post('hidden_fdds_quantity');
				if($quant_array != ''){
					if($quant_array == '&&'){
						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>0));
						$this->db->delete('fdd_pro_quantity');
					}else{
						$quant_array = substr($quant_array, 0, -2);
						$quant_arr = explode('**', $quant_array);
						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>0));
						$this->db->delete('fdd_pro_quantity');
						foreach ($quant_arr as $quant_ar){
							$quant_ar_ar = explode('#', $quant_ar);

							$semi_pro_id = 0;
							if($quant_ar_ar[5] != NULL){
								$semi_pro_id = $quant_ar_ar[5];
							}

							$insrt_quant_array = array(
									'obs_pro_id'=>$val['id'],
									'fdd_pro_id'=>$quant_ar_ar[0],
									'quantity'=>$quant_ar_ar[1],
									'unit'=>$quant_ar_ar[4],
									'semi_product_id'=>$semi_pro_id,
									'fixed'=>$quant_ar_ar[6]
							);
							
							$this->db->insert('fdd_pro_quantity',$insrt_quant_array);
						}
					}
				}

				$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
				if($obs_quant_array != ''){
					if($obs_quant_array == '&&'){
						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>1));
						$this->db->delete('fdd_pro_quantity');
						$this->delete_notcontaining_products();
					}else{
						$obs_quant_array = substr($obs_quant_array, 0, -2);
						$quant_arr = explode('**', $obs_quant_array);

						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>1));
						$this->db->delete('fdd_pro_quantity');
						$this->delete_notcontaining_products();

						foreach ($quant_arr as $quant_ar){
							$quant_ar_ar = explode('#', $quant_ar);

							$semi_pro_id = 0;
							if($quant_ar_ar[5] != NULL){
								$semi_pro_id = $quant_ar_ar[5];
							}

							$insrt_quant_array = array(
									'obs_pro_id'=>$val['id'],
									'fdd_pro_id'=>$quant_ar_ar[0],
									'quantity'=>$quant_ar_ar[1],
									'unit'=>$quant_ar_ar[4],
									'is_obs_product'=>1,
									'semi_product_id'=>$semi_pro_id,
									'fixed'=>1
							);
							
							$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

							if($semi_pro_id == 0){
								/*$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$val['company_id']))->result_array();
								if(empty($info)){
									$this->db->insert('products_pending', array('product_id' => $quant_ar_ar[0], 'company_id' => $val['company_id'], 'date' => date('Y-m-d h:i:s')));
								}

								$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
								if(empty($con_info)){
									$this->db->insert('contacted_via_mail', array('obs_pro_id' => $quant_ar_ar[0]));
								}*/
							}
						}
					}
				}

				/*
				 *updating product
				*/
				$update_product_data = array(
						'recipe_method' => $this->input->post('recipe_method_txt')
				);

				if($this->input->post('is_custom_semi') == 1 ){
					$update_product_data['semi_product'] = 1;
				}
				else{
					$update_product_data['semi_product'] = 0;
					$this->db->select('obs_pro_id,fdd_pro_id');
					$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$val['id']))->result_array();
					foreach ($semi_info as $semi){
						$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
						$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
						$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
						$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
					}
					$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$val['id']));
				}
				// If company not keurslager associate then add normal ingredients
				If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' ||$this->session->userdata('menu_type') == 'fdd_premium')){
					$update_product_data['ingredients'] = $this->input->post('ingredients');
					$update_product_data['allergence'] = $this->input->post('allergence');
					$update_product_data['traces_of'] = $this->input->post('traces_of');
				}

				if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
					//$update_product_data['direct_kcp'] = $this->input->post('product_type');
				}

				$update_product_data['recipe_weight'] = $this->input->post('recipe_weight');

				$this->db->where('id',$val['id']);
				$this->db->update('products', $update_product_data);

				if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){

					/**
					 * Updating Product Ingredients
					 */
					$this->db->delete('products_ingredients',array('product_id' => $val['id']));

					// Adding Ingredients
					$this->adding_ingredients($val['id']);

					/**
					 * Updating Product Traces
					*/
					$this->db->delete('products_traces',array('product_id' => $val['id']));

					// Adding Traces
					$this->adding_traces($val['id']);

					/**
					 * Updating Product Allergence
					*/
					$this->db->delete('products_allergence',array('product_id' => $val['id']));
					$this->db->delete('product_sub_allergence',array('product_id' => $val['id']));

					// Adding Allergence
					$this->adding_allergence($val['id']);
				}

				if($this->input->post('is_custom_semi') == 1){
					$this->update_semi_product_quant($custom_pro_total_wt_arr);
					$this->update_kp_display_order( $val['id']);
				}
			}
		}
	}

	/**
	 * This function adds labeler section of single product
	 * @return number $new_product_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function add_labeler(){
		if($this->input->post('prod_id')){
			$new_product_id = $this->update_labeler();
		}
		else{
			$add_product_data = array(
					'company_id' => $this->company_id,
					'categories_id'=>$this->input->post('categories_id'),
					'subcategories_id'=>$this->input->post('subcategories_id'),
					'proname' => addslashes($this->input->post('proname')),
					'procreated'=>date('Y-m-d',time())
			);
			$query = $this->db->insert('products', $add_product_data);
			$new_product_id = $this->db->insert_id();//method to get id of last inserted row//

			if($this->input->post('labeler_logo')){
				$this->db->select('extra_logo_image');
				$labeler = $this->db->get_where('products_labeler', array('product_id' => $product_id))->result();

				if(!empty($labeler[0]->extra_logo)){
					unlink(dirname(__FILE__).'/../../assets/cp/labeler_logo_extra/'.$labeler[0]->extra_logo);
				}

				$prefix = 'cropped_'.$this->company_id.'_';
				$str = $this->input->post('labeler_logo');
				if (substr($str, 0, strlen($prefix)) == $prefix) {
					$str = substr($str, strlen($prefix));
				}
				if(isset($str) && $str != ''){
					$this->image = $this->company_id.'_'.$str;
					$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('labeler_logo'));
					file_put_contents(dirname(__FILE__).'/../../assets/cp/labeler_logo_extra/'.$this->image, $image_file);
				}
			}

			$labeler_logo_status = $this->input->post('labeler_logo_status');

			if ($this->input->post('labeler_logo_status') == '')
			{
				$labeler_logo_status = '0';
			}

			$insert_label_data = array(
					'product_id'	=>	$new_product_id,
					'conserve_min' 	=> 	$this->input->post('conserve_min'),
					'conserve_max'	=> 	$this->input->post('conserve_max'),
					'weight' 		=> 	$this->number2db($this->input->post('weight')),
					'weight_unit' 	=> 	$this->input->post('weight_unit'),
					'show_bcode' 	=> 	$this->input->post('show_bcode'),
					'extra_notification' => $this->input->post('extra_noti'),
					'extra_notification_free_field' => $this->input->post('extra_noti_free_field'),
					'extra_logo_status' => $labeler_logo_status
			);

			if($this->image){
				$insert_label_data['extra_logo_image'] = $this->image;
			}

			if($this->input->post('duedate'))
				$insert_label_data['duedate'] = $this->input->post("duedate");

			if($this->input->post('duedate_type'))
				$insert_label_data['duedate_type'] = $this->input->post("duedate_type");

			if($this->input->post('prod_date')){
				$dt = DateTime::createFromFormat("d/m/Y", $this->input->post('prod_date'));
				$insert_label_data['production_date'] = date('Y-m-d',$dt->getTimestamp());
			}
			$this->db->insert('products_labeler', $insert_label_data);
		}

		return $new_product_id;
	}

	/**
	 * This function updates labeler section of single product
	 * @return number $product_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function update_labeler(){
		/*
		 *Updating product
		*/

		$update_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'proupdated' => date('Y-m-d',time()),
				'is_custom_pending'=>0,
				'changed_fixed_product_id'=>0
		);

		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		$product_id = $this->input->post('prod_id');
		$labeler = $this->db->get_where('products_labeler', array('product_id' => $product_id))->result();

		if($this->input->post('labeler_logo')){
			$this->db->select('extra_logo_image');
			$labeler = $this->db->get_where('products_labeler', array('product_id' => $product_id))->result();

			if(!empty($labeler[0]->extra_logo)){
				unlink(dirname(__FILE__).'/../../assets/cp/labeler_logo_extra/'.$labeler[0]->extra_logo);
			}

			$prefix = 'cropped_'.$this->company_id.'_';
			$str = $this->input->post('labeler_logo');
			if (substr($str, 0, strlen($prefix)) == $prefix) {
				$str = substr($str, strlen($prefix));
			}
			if(isset($str) && $str != ''){
				$this->image = $this->company_id.'_'.$str;
				$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('labeler_logo'));
				file_put_contents(dirname(__FILE__).'/../../assets/cp/labeler_logo_extra/'.$this->image, $image_file);
			}
		}
		$labeler_logo_status = $this->input->post('labeler_logo_status');

		if ($this->input->post('labeler_logo_status') == '')
		{
			$labeler_logo_status = '0';
		}
		if(empty($labeler)){
			$update_label_data = array(
					'product_id'	=> $product_id,
					'conserve_min' 	=> $this->input->post('conserve_min'),
					'conserve_max' 	=> $this->input->post('conserve_max'),
					'weight' 		=> 	$this->number2db($this->input->post('weight')),
					'weight_unit' 	=> 	$this->input->post('weight_unit'),
					'show_bcode' 	=> 	$this->input->post('show_bcode'),
					'extra_notification' => $this->input->post('extra_noti'),
					'extra_notification_free_field' => $this->input->post('extra_noti_free_field'),
					'extra_logo_status' => $labeler_logo_status
			);

			if($this->image){
				$update_label_data['extra_logo_image'] = $this->image;
			}

			if($this->input->post('duedate'))
				$update_label_data['duedate'] = $this->input->post("duedate");

			if($this->input->post('duedate_type'))
				$update_label_data['duedate_type'] = $this->input->post("duedate_type");

			if($this->input->post('prod_date')){
				$dt = DateTime::createFromFormat("d/m/Y", $this->input->post('prod_date'));
				$update_label_data['production_date'] = date('Y-m-d',$dt->getTimestamp());
			}

			$this->db->insert('products_labeler', $update_label_data);
		}
		else{
			$update_label_data = array(
					'conserve_min' 	=> $this->input->post('conserve_min'),
					'conserve_max' 	=> $this->input->post('conserve_max'),
					'weight' 		=> 	$this->number2db($this->input->post('weight')),
					'weight_unit' 	=> 	$this->input->post('weight_unit'),
					'show_bcode' 	=> 	$this->input->post('show_bcode'),
					'extra_notification' => $this->input->post('extra_noti'),
					'extra_notification_free_field' => $this->input->post('extra_noti_free_field'),
					'extra_logo_status' => $labeler_logo_status
			);
			if($this->image){
				$update_label_data['extra_logo_image'] = $this->image;
			}

			if($this->input->post('duedate'))
				$update_label_data['duedate'] = $this->input->post("duedate");

			if($this->input->post('duedate_type'))
				$update_label_data['duedate_type'] = $this->input->post("duedate_type");

			if($this->input->post('prod_date')){
				$dt = DateTime::createFromFormat("d/m/Y", $this->input->post('prod_date'));
				$update_label_data['production_date'] = date('Y-m-d',$dt->getTimestamp());
			}

			$this->db->where('product_id',$product_id);
			$this->db->update('products_labeler', $update_label_data);
		}

		return $product_id;
	}

	/**
	 * This function adds webshop section of single product
	 * @return number $new_product_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function add_webshop(){

		if($this->input->post('dis')=='2'){
			$discount='multi';
			$this->load->model('Mproduct_discount');
		}else if($this->input->post('dis')=='1'){
			$discount=$this->input->post('discount');
		}else if($this->input->post('dis')=='0'){
			$discount=0;
		}

		if($this->input->post('dis_wt')=='2'){
			$discount_wt='multi';
			$this->load->model('Mproduct_discount');
		}else if($this->input->post('dis_wt')=='1'){
			$discount_wt=$this->input->post('discount_wt');
		}else if($this->input->post('dis_wt')=='0'){
			$discount_wt=0;
		}

		if($this->input->post('dis_p')=='2'){
			$discount_person='multi';
			$this->load->model('Mproduct_discount');
		}else if($this->input->post('dis_p')=='1'){
			$discount_person=$this->input->post('discount_p');
		}else if($this->input->post('dis_p')=='0'){
			$discount_person=0;
		}

		$price = $this->input->post('price');
		$price = $this->number2db($price);

		$price_per_unit = $this->input->post('price_per_unit');
		$price_per_unit = $this->number2db($price_per_unit);

		$price_per_person = $this->input->post('price_per_person');
		$price_per_person = $this->number2db($price_per_person);

		$price_weight = $this->input->post('price_weight');
		$price_weight = $this->number2db($price_weight);
		$price_weight = $price_weight/1000;
		$price_weight = $this->number2db($price_weight);

		$min_weight = $this->input->post('min_weight');
		$min_weight = $this->number2db($min_weight);

		$max_weight = $this->input->post('max_weight');
		$max_weight = $this->number2db($max_weight);

		$weight_per_unit = $this->input->post('weight_per_unit');
		$weight_per_unit = $this->number2db($weight_per_unit);

		$product_available = $this->input->post('product_available');
		$searchObject = '';
		$keys = array();
		if ((isset($product_available) && !empty($product_available)))
		{
			foreach($product_available as $k => $v) {
				if($v == $searchObject) $keys[] = $k;
			}
			if (!empty($keys))
			{
				foreach ($keys as $val)
				{
					unset($product_available[$val]);
				}
			}
		}

		$product_notavailable = $this->input->post('product_notavailable');
		$searchObject = '';
		$keys = array();
		if ((isset($product_notavailable) && !empty($product_notavailable)))
		{
			foreach($product_notavailable as $k => $v) {
				if($v == $searchObject) $keys[] = $k;
			}
			if (!empty($keys))
			{
				foreach ($keys as $val)
				{
					unset($product_notavailable[$val]);
				}
			}
		}
		if ($this->input->post('Holiday_availability') == '')
		{
			$holiday_availability = '0';
		}
		else{
			$holiday_availability = $this->input->post('Holiday_availability');
		}
		$add_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'min_amount'=> $this->input->post('min_amount'),
				'max_amount'=> $this->input->post('max_amount'),
				'sell_product_option'=>$this->input->post('sell_product_option'),
				'price_per_unit'=>$price_per_unit,
				'price_per_person'=>$price_per_person,
				'price_weight'=>$price_weight,
				'min_weight'=>$min_weight,
				'max_weight'=>$max_weight,
				'weight_unit'=>$this->input->post('weight_unit'),
				'weight_per_unit'=>$weight_per_unit,
				'procreated'=>date('Y-m-d',time()),
				'type'=>($this->input->post('type'))?$this->input->post('type'):'0',
				'discount'=>$discount,
				'discount_person'=>$discount_person,
				'discount_wt'=>$discount_wt,
				'allday_availability' => $this->input->post('allday_availability'),
				'holiday_availability'=>$holiday_availability,
				'date_available'=>implode('#', $product_available),
				'date_unavailable'=>implode('#', $product_notavailable),
				'advance_payment' => $this->input->post('advance_payment'),
				'allow_upload_image' => $this->input->post('allow_upload_image'),
				'available_after' => $this->input->post('available_after'),
				'recommend' => $this->input->post('recommend'),
				'related_products'=>$this->input->post('sel_prod'),
				'stock_qty'=>$this->input->post('obs_stock_qty')
		);

		$availability = $this->input->post('day');
		if($availability && !empty($availability)){
			$add_product_data['availability'] = json_encode($availability);
		}else{
			$add_product_data['availability'] = '';
		}

		if($this->input->post('prod_id')){
			$new_product_id = $this->input->post('prod_id');
			$this->db->where('id',$new_product_id);
			$this->db->update('products', $add_product_data);
		}
		else{
			$query=$this->db->insert('products', $add_product_data);
			$new_product_id=$this->db->insert_id();//method to get id of last inserted row//
		}
		//here the product gets entered//
		//--now the value returned frm that inseted product is its id so it is going to be used in inserting in discount table--//

		if($this->input->post('dis')=='2'){
			$this->Mproduct_discount->add_product_discount($new_product_id,0);
		}

		if($this->input->post('dis_p')=='2'){
			$this->Mproduct_discount->add_product_discount($new_product_id,2);
		}

		if($this->input->post('dis_wt')=='2'){
			$this->Mproduct_discount->add_product_discount($new_product_id,1);
		}

		//this is to add discount in product_discount  passing id of inseted row//
		//--this is to add groups in the newly added prduct--//

		$this->load->model('Mgroups_products');
		$this->Mgroups_products->add_update_groups_products($new_product_id);
		//---------------------------------------------------//

		//---this is to add group's display order in groups_order table--------------//

		$this->load->model('Mgroups_order');
		$this->Mgroups_order->add_groups_order($new_product_id);


		$more_image_arr = array($this->input->post('image_name1'),$this->input->post('image_name2'),$this->input->post('image_name3'));
		if(!empty($more_image_arr)){
			$more_img = '';
			for($c = 0; $c <3; $c++){
				if($more_image_arr[$c]){
					$prefix = 'cropped_'.$this->company_id.'_';
					$str = $more_image_arr[$c];

					if (substr($str, 0, strlen($prefix)) == $prefix) {
						$str = substr($str, strlen($prefix));
					}
					if(isset($str) && $str != ''){
						$this->image1 = $this->company_id.'_'.$new_product_id.'_'.$str;
						$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$more_image_arr[$c]);
						file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$this->image1, $image_file);

						/* if($this->image){
						 //$this->resize_product_images($this->image);
						$this->load->helper('resize');
						resize_images('product',$this->image,false,$this->input->post('prod_id'));
						} */
					}
					$more_img .= $this->image1.':::';
				}
				else{
					$more_img .= '0:::';
				}
			}
			$more_img = rtrim($more_img,':::');

			$update_product_data['more_image']=$more_img;
			$this->db->where('id',$new_product_id);
			$this->db->update('products', $update_product_data);
		}


		return $new_product_id;
	}

	/**
	 * This function updates webshop section of single product
	 * @return number
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function update_webshop(){
		/*
		 * discount of product
		* variable '$discount' is being used in updation array
		*/
		if($this->input->post('dis')=='2'){
			$discount='multi';
			$this->load->model('Mproduct_discount');
			$this->Mproduct_discount->update_product_discount($this->input->post('prod_id'),0);
		}else if($this->input->post('dis')=='1'){
			$discount=$this->input->post('discount');
		}else if($this->input->post('dis')=='0'){
			$discount=0;
		}

		if($this->input->post('dis_p')=='2'){
			$discount_person='multi';
			$this->load->model('Mproduct_discount');
			$this->Mproduct_discount->update_product_discount($this->input->post('prod_id'),2);
		}else if($this->input->post('dis_p')=='1'){
			$discount_person=$this->input->post('discount_p');
		}else if($this->input->post('dis_p')=='0'){
			$discount_person=0;
		}

		/*
		 * discount of product
		* variable '$discount' is being used in updation array
		*/
		if($this->input->post('dis_wt')=='2'){
			$discount_wt='multi';
			$this->load->model('Mproduct_discount');
			$this->Mproduct_discount->update_product_discount($this->input->post('prod_id'),1);
		}else if($this->input->post('dis_wt')=='1'){
			$discount_wt=$this->input->post('discount_wt');
		}else if($this->input->post('dis_wt')=='0'){
			$discount_wt=0;
		}

		/*
		 *updating product
		*/
		$price = $this->input->post('price');
		$price = $this->number2db($price);

		$price_per_unit = $this->input->post('price_per_unit');
		$price_per_unit = $this->number2db($price_per_unit);

		$price_per_person = $this->input->post('price_per_person');
		$price_per_person = $this->number2db($price_per_person);

		$price_weight = $this->input->post('price_weight');
		$price_weight = $this->number2db($price_weight);
		$price_weight = $price_weight/1000;
		$price_weight = $this->number2db($price_weight);

		$min_weight = $this->input->post('min_weight');
		$min_weight = $this->number2db($min_weight);

		$max_weight = $this->input->post('max_weight');
		$max_weight = $this->number2db($max_weight);

		$weight_per_unit = $this->input->post('weight_per_unit');
		$weight_per_unit = $this->number2db($weight_per_unit);

		$product_available = $this->input->post('product_available');
		$searchObject = '';
		$keys = array();
		if ((isset($product_available) && !empty($product_available)))
		{
			foreach($product_available as $k => $v) {
				if($v == $searchObject) $keys[] = $k;
			}
			if (!empty($keys))
			{
				foreach ($keys as $val)
				{
					unset($product_available[$val]);
				}
			}
		}

		$product_notavailable = $this->input->post('product_notavailable');
		$searchObject = '';
		$keys = array();
		if ((isset($product_notavailable) && !empty($product_notavailable)))
		{
			foreach($product_notavailable as $k => $v) {
				if($v == $searchObject) $keys[] = $k;
			}
			if (!empty($keys))
			{
				foreach ($keys as $val)
				{
					unset($product_notavailable[$val]);
				}
			}
		}
		if ($this->input->post('Holiday_availability') == '')
		{
			$holiday_availability = '0';
		}
		else{
			$holiday_availability = $this->input->post('Holiday_availability');
		}

		$update_product_data = array(
				'company_id' => $this->company_id,
				'categories_id'=>$this->input->post('categories_id'),
				'subcategories_id'=>$this->input->post('subcategories_id'),
				'proname' => addslashes($this->input->post('proname')),
				'min_amount'=> $this->input->post('min_amount'),
				'max_amount'=> $this->input->post('max_amount'),
				'sell_product_option'=>$this->input->post('sell_product_option'),
				'price_per_unit'=>$price_per_unit,
				'price_per_person'=>$price_per_person,
				'price_weight'=>$price_weight,
				'min_weight'=>$min_weight,
				'max_weight'=>$max_weight,
				'weight_unit'=>$this->input->post('weight_unit'),
				'weight_per_unit'=>$this->input->post('weight_per_unit'),
				'proupdated' => date('Y-m-d',time()),
				'type' => ($this->input->post('type'))?$this->input->post('type'):'0',
				'discount' => $discount,
				'discount_person'=>$discount_person,
				'discount_wt' => $discount_wt,
				'allday_availability' => $this->input->post('allday_availability'),
				'holiday_availability'=>$holiday_availability,
				'date_available'=>implode('#', $product_available),
				'date_unavailable'=>implode('#', $product_notavailable),
				'advance_payment' => $this->input->post('advance_payment'),
				'allow_upload_image' => $this->input->post('allow_upload_image'),
				'available_after' => $this->input->post('available_after'),
				'recommend' => $this->input->post('recommend'),
				'is_custom_pending'=>0,
				'changed_fixed_product_id'=>0,
				'related_products'=>$this->input->post('sel_prod'),
				'stock_qty'=>$this->input->post('obs_stock_qty')
		);

		$availability = $this->input->post('day');
		if($availability && !empty($availability)){
			$update_product_data['availability'] = json_encode($availability);
			if (count($availability) == 7){
				$update_product_data['allday_availability'] = '1';
			}
			else {
				$update_product_data['allday_availability'] = '0';
			}
		}else{
			$update_product_data['availability'] = '';
			$update_product_data['allday_availability'] = $this->input->post('allday_availability');
		}

		$more_img = '';

		$more_image_arr = array($this->input->post('image_name1'),$this->input->post('image_name2'),$this->input->post('image_name3'));

		if(!empty($more_image_arr)){
			$this->db->select('more_image');
			$prev = $this->db->get_where('products',array('id'=>$this->input->post('prod_id')))->row();
			if($prev->more_image != '')
				$prev_more = explode(':::', $prev->more_image);
			else
				$prev_more = ['0','0','0'];

			for($c = 0; $c <3; $c++){
				if($more_image_arr[$c] != '0'){
					$prefix = 'cropped_'.$this->company_id.'_';
					$str = $more_image_arr[$c];

					if (substr($str, 0, strlen($prefix)) == $prefix) {
						$str = substr($str, strlen($prefix));
					}
					if(isset($str) && $str != ''){
						$this->image1 = $this->company_id.'_'.$this->input->post('prod_id').'_'.$str;
						$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$more_image_arr[$c]);
						file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$this->image1, $image_file);

						/* if($this->image){
							//$this->resize_product_images($this->image);
							$this->load->helper('resize');
							resize_images('product',$this->image,false,$this->input->post('prod_id'));
						} */
					}
					$more_img .= $this->image1.':::';
				}
				else{
					if($prev_more[$c] == '0')
						$more_img .= '0:::';
					else
						$more_img .= $prev_more[$c].':::';
				}
			}
			$more_img = rtrim($more_img,':::');
		}


		$update_product_data['more_image']=$more_img;

		$this->db->where('id',$this->input->post('prod_id'));
		$this->db->update('products', $update_product_data);

		$this->load->model('Mgroups_products');
		$this->Mgroups_products->add_update_groups_products($this->input->post('prod_id'));

		$this->load->model('Mgroups_order');
		$this->Mgroups_order->update_groups_order($this->input->post('prod_id'));

		return $this->input->post('prod_id');
	}

	/**
	 * This function fetches the next product for current company
	 * @param number $present
	 * @return array $next
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_next_product($present = 0){
		$next = array();
		$cat_id=0;
		if($present){
			$this->db->select('id');
			$this->db->where("categories_id",$this->input->post('categories_id'));
			$this->db->order_by('categories_id','asc');
			$this->db->where("((semi_product = 0) OR (semi_product = 1 AND direct_kcp = 0))");
			$this->db->where("company_id",$this->company_id);
			$this->db->where('id >',$present);
			$this->db->order_by('id','asc');
			$next = $this->db->get('products')->row();
			if (empty($next))
			{
					$this->db->select('id');
					$this->db->where("categories_id >", $this->input->post('categories_id'));
					$this->db->order_by('categories_id','asc');
					$this->db->where("((semi_product = 0) OR (semi_product = 1 AND direct_kcp = 0))");
					$this->db->where("company_id",$this->company_id);
					$this->db->order_by('id','asc');
					$next = $this->db->get('products')->row();
			}
		}
		return $next;
	}

	/**
	 * This function is used to fetch all products from OBS which dont have productsheets
	 * @return array $products
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_without_sheets(){
		/* $products = $this->db->query('
				SELECT products.id, products.proname, products.fdd_producer_id
				FROM products JOIN company ON products.company_id = company.id
				WHERE products.direct_kcp = 1
				AND products.semi_product = 0
				AND products.direct_kcp_id = 0
				AND products.id NOT IN (SELECT products_ingredients.product_id FROM products_ingredients WHERE kp_id != 0 AND ki_id != 0)
				AND products.company_id IN (SELECT id FROM `company` WHERE `ac_type_id` IN (4,5,6))
				')->result_array(); */
		$this->db->select(array('id','proname','fdd_producer_id','categories_id'));
		$products = $this->db->get_where('products', array('company_id' => 0))->result_array();
		return $products;
	}

	/**
	 * This function fetches the labeler information of products
	 * @param number $product_id
	 * @return array $labeler
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_labeler($product_id = 0){
		$labeler = array();
		if($product_id){
			$labeler = $this->db->get_where('products_labeler', array('product_id' => $product_id))->result();
		}
		return $labeler;
	}

	/**
	 * This function make first letter of additives ingredients capital
	 * @param string $ing_name
	 * @return string
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	private function get_ing_name_mod($ing_name){
		$ing_name = strtolower($ing_name);
		if(preg_match("/(|\.*)e(|\s)\d{3}(|.*)/", trim($ing_name) ,$matches)){
			$ing_name = preg_replace_callback('/(e(|\s)\d{3})/', function ($match) {return ucfirst($match[0]);}, trim($ing_name));
		}
		elseif(strpos($ing_name,"vitamin")!== FALSE){
			$ing_name = ucwords($ing_name);
		}
		else{
			$ing_name = $ing_name;
		}
		return addslashes($ing_name);
	}

	/**
	 * Function to fetch e-nbr status of current company
	 * @param number $company_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
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
	 * Function to get display name for current ingredient
	 * @param number $ing_id
	 * @return string $display_name
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_display_name($ing_id = 0, $sel_lang_upd = '_dch'){
		$display_name = '';
		if ($sel_lang_upd == '_dch'){
			$disp_ing_id = 'display_ing_id';
		}elseif ($sel_lang_upd == '_fr'){
			$disp_ing_id = 'display_ing_id_fr';
		}else{
			$disp_ing_id = 'display_ing_id_en';
		}
		if($ing_id != 0){
			
			$this->fdb->select('a.'.$disp_ing_id.'');
			$this->fdb->get_where('ingredients as a',array('a.ing_id'=>$ing_id));
			$sub_query = $this->fdb->last_query();
			$this->fdb->select('b.ing_name'.$sel_lang_upd.'');
			$this->fdb->where('b.ing_id = ('.$sub_query.')',NULL,FALSE);
			$display_name_arr = $this->fdb->get('ingredients as b')->row_array();

			if(!empty($display_name_arr)){
				$display_name = $display_name_arr['ing_name'.$sel_lang_upd.''];
			}
			
		}
		return $display_name;
	}

	/**
	 * Function to update available allergens in ingredients of current product
	 * @param number $product_id
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	private function update_have_all_id($product_id = 0){
		if($product_id != 0){
			$this->db->select('ki_id');
			$ing_arr1 = $this->db->get_where('products_ingredients',array('product_id'=>$product_id,'ki_id !='=>0,'is_obs_ing'=>0))->result_array();

			if(!empty($ing_arr1)){
				foreach ($ing_arr1 as $val){
					
					$this->fdb->select('have_all_id');
					$all_id_arr = $this->fdb->get_where('ingredients',array('ing_id'=>$val['ki_id']))->row_array();

					
					$this->db->where(array('product_id'=>$product_id,'ki_id'=>$val['ki_id']));
					$this->db->update('products_ingredients',array('have_all_id'=>$all_id_arr['have_all_id']));
				}
			}
			/*

			$this->db->select('ki_id');
			$ing_arr2 = $this->db->get_where('products_ingredients_vetten',array('product_id'=>$product_id))->result_array();

			if(!empty($ing_arr2)){
				foreach ($ing_arr2 as $val){
					
					$this->fdb->select('have_all_id');
					$all_id_arr = $this->fdb->get_where('ingredients',array('ing_id'=>$val['ki_id']))->row_array();

					
					$this->db->where(array('product_id'=>$product_id,'ki_id'=>$val['ki_id']));
					$this->db->update('products_ingredients_vetten',array('have_all_id'=>$all_id_arr['have_all_id']));
				}
			}

			$this->db->select('ki_id');
			$ing_arr3 = $this->db->get_where('products_additives',array('product_id'=>$product_id,'ki_id !='=>0))->result_array();

			if(!empty($ing_arr3)){
				foreach ($ing_arr3 as $val){
					
					$this->fdb->select('have_all_id');
					$all_id_arr = $this->fdb->get_where('ingredients',array('ing_id'=>$val['ki_id']))->row_array();

					
					$this->db->where(array('product_id'=>$product_id,'ki_id'=>$val['ki_id']));
					$this->db->update('products_additives',array('have_all_id'=>$all_id_arr['have_all_id']));
				}
			}
			*/
		}
	}

	/**
	 * This function deletes those products from table products_pending which are no more contained within products of this company
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function delete_notcontaining_products(){
		$this->db->select('fdd_pro_quantity.fdd_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$this->db->join('products_pending','products_pending.product_id = fdd_pro_quantity.fdd_pro_id');
		$this->db->where(array('products.company_id' => $this->company_id, 'fdd_pro_quantity.is_obs_product'=>1));
		$pending_left = $this->db->get('fdd_pro_quantity')->result_array();

		$this->db->select(array('product_id','company_id'));
		$pending_arr = $this->db->get_where('products_pending',array('company_id' => $this->company_id))->result_array();

		foreach ($pending_arr as $val){
			if(!$this->in_array_r($val['product_id'], $pending_left)){
				$this->db->delete('products_pending', array('product_id' => $val['product_id'],'company_id'=>$this->company_id));
			}
		}
	}

	/**
	 * This function deletes those products from table request_gs1 which are no more contained within products of this company
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function delete_notcontaining_gs1_products(){
		$this->db->select('fdd_pro_quantity.fdd_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$this->db->where(array('products.company_id' => $this->company_id, 'fdd_pro_quantity.is_obs_product'=>0));
		$all_fdd = $this->db->get('fdd_pro_quantity')->result_array();
		$this->db->select('gs1_pid');
		$selected_gs1 = $this->db->get_where('request_gs1',array('company_id' => $this->company_id,'request_status'=>0))->result_array();
		foreach ($selected_gs1 as $val){
			if(!$this->in_array_r($val['gs1_pid'], $all_fdd)){
				$this->db->delete('request_gs1', array('gs1_pid' => $val['gs1_pid'],'company_id'=>$this->company_id));
			}
		}
	}

	/**
	 * This function checks a value within multidimentional array
	 * @param string $needle
	 * @param array $haystack
	 * @param boolean $strict
	 * @return boolean
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	private function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @name get_product_by_keyword
	 * @param string $product_keyword
	 * @param string $chk_company_id
	 * @return array
	 * @author Ankush Katiyar
	 */
	function get_product_by_keyword($product_keyword = null){
		$query = array();
		if ($product_keyword){
			$product_keyword = urldecode($product_keyword);
			$this->db->like('proname', $product_keyword);
			$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
			$this->db->where($where);
			$this->db->where(array('company_id'=>$this->company_id));

			$this->db->order_by( 'pro_display', 'ASC' );
			$query=$this->db->get('products')->result();
			//echo $this->db->last_query();die();
			$sorted_p = array();
			$unsorted_p = array();
			foreach($query as $prod) {
				if(isset($prod->pro_display) && $prod->pro_display != 0){
					$sorted_p[] = $prod;
				}else{
					$unsorted_p[] = $prod;
				}

			}
			$query = array_merge($sorted_p,$unsorted_p);
			foreach ($query as $k=>$qr){
				$query[$k]->no_fdd_con = 0;
				if( $this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
					$complete = 1;
					if($qr->direct_kcp == 1){
						$this->db->where(array('obs_pro_id'=>$qr->id,'is_obs_product'=>0));
						$result = $this->db->get('fdd_pro_quantity')->result_array();
						if(empty($result)){
							$complete = 0;
						}
					}
					else{
						$this->db->where(array('obs_pro_id'=>$qr->id));
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

					if($complete == 0){
						$query[$k]->no_fdd_con = 1;
					}
				}
			}
		}
		return($query);
	}

	/**
	 * This function display list of all shared products in products page
	 * @name get_shared_products_list
	 * @return array
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function get_shared_products_list(){
		$prod_comp_arr = array();
		$this->db->distinct();
		$this->db->select('from_comp_id');
		$from_comp_id = $this->db->get_where('products_shared',array('to_comp_id'=> $this->company_id))->result_array();
		foreach ($from_comp_id as $key=>$val){
			$product_details = $this->db->get_where('products_shared',array('from_comp_id'=>$val['from_comp_id'],'to_comp_id'=>$this->company_id,'status' => "0"))->result_array();
			if (!empty($product_details)){
				foreach ($product_details as $key=>$val){

					$this->db->select('proname');
					$product_name = $this->db->get_where('products',array('id'=> $val['proid']))->result_array();
					if (!empty($product_name)){
						$product_details[$key]['product_name'] = stripslashes($product_name[0]['proname']);
					}
					$this->db->select('company_name');
					$company_name = $this->db->get_where('company',array('id'=> $val['from_comp_id']))->result_array();
					if (!empty($product_name)){
						$product_details[$key]['from_company_name'] = stripslashes($company_name[0]['company_name']);
					}
				}
				$prod_comp_arr[stripslashes($company_name[0]['company_name'])."##".$val['from_comp_id']] = $product_details;
			}
		}
		return $prod_comp_arr;
	}

	/**
	 * This function checks product is shared or not
	 * @name check_prod_shared_stat
	 * @param $product_id
	 * @return array
	 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
	 */
	function check_prod_shared_stat($product_id = 0){
		if($product_id){
			$result = $this->db->get_where('products',array('parent_proid'=>$product_id))->result_array();
			return $result;
		}
	}

	/**
	 * This model function is for fetching products distict Ingredients
	 * @param int $product_id ID of product
	 * @return array $ingredients
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	function get_product_ingredients_dist_addedit($product_id = 0, $k_type = 1){
		$ingredients = array();
		// if($product_id){
		// 	$ingredients = $this->db->query(
		// 		"SELECT ki_id, ki_name, have_all_id, prefix FROM
		// 			(SELECT *
		// 			FROM products_ingredients a
		// 			WHERE product_id = ".$product_id."
		// 			AND ki_name!= '('
		// 			AND ki_name!= ')'
		// 			AND ((display_order=1 AND is_obs_ing=1) OR (display_order !=0 AND display_order !=1 AND is_obs_ing=0) OR (0 = (SELECT COUNT(b.ki_id) FROM products_ingredients b WHERE b.product_id = ".$product_id." AND a.kp_id = b.kp_id AND b.ki_id != 0)+(SELECT COUNT(c.ki_id) FROM products_ingredients_vetten c WHERE c.product_id = ".$product_id." AND a.kp_id = c.kp_id AND c.ki_id != 0)+(SELECT COUNT(d.ki_id) FROM products_additives d WHERE d.product_id = ".$product_id." AND a.kp_id = d.kp_id AND d.ki_id != 0)))
		// 			ORDER BY kp_display_order ASC) AS INGTAB
		// 			GROUP BY ki_name
		// 			ORDER BY kp_display_order ASC, display_order ASC")->result();
		// }
		return $ingredients;
	}

	/**
	 * This model function is for fetching products Allergence
	 * @param int $product_id ID of product
	 */
	function get_product_allergence_addedit($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			$this->db->select('kp_id, ka_name, ka_id, prefix');
			$this->db->order_by('display_order', 'ASC');
		//	if($k_type){
				$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
		//	}else{
		//		$allergence = $this->db->get_where('i_products_allergence', array('product_id' => $product_id))->result();
		//	}
		}
		return $allergence;
	}

	/**
	 * Function to update producer id, supplier id and their article number
	 * @param array $pro_supp
	 */
	function insert_fdd_prod_supp_art_nbr($pro_supp=array()){

		if(!empty($pro_supp)){
			$pro_id = $pro_supp['pro_id'];
			$producer_id=$pro_supp['producer_id'];
			$fdd_prod_art_nbr=$pro_supp['fdd_prod_art_nbr'];
			$supplier_id=$pro_supp['supplier_id'];
			$fdd_supp_art_nbr=$pro_supp['fdd_supp_art_nbr'];
			$proupdated=$pro_supp['proupdated'];

			$data = array(
					'fdd_producer_id'=>$producer_id,
					'fdd_prod_art_num'=>$fdd_prod_art_nbr,
					'fdd_supplier_id'=>$supplier_id,
					'fdd_supp_art_num'=>$fdd_supp_art_nbr,
					'proupdated'=>$proupdated
			);

			$this->db->where('id',$pro_id);
			$this->db->update('products', $data);
			return 'success';
		}
		return 'error';
	}

	function get_pro_recipe( $cid ) {
		$this->db->select( 'products_pending.product_id');
		$this->db->where( 'products_pending.company_id', $cid );
		$query = $this->db->get( 'products_pending' )->result_array();
		$new_query = '';
		foreach ($query as $key => $value) {
			$this->db->select( 'products.proname, products.id' );
			$this->db->join( 'fdd_pro_quantity', 'fdd_pro_quantity.obs_pro_id = products.id' );
			$this->db->where( array( 'fdd_pro_quantity.is_obs_product' => 1, 'fdd_pro_quantity.fdd_pro_id' => $value['product_id'], 'products.company_id' => $cid ) );
			$new_query[$value['product_id']] = $this->db->get( 'products' )->result_array();
		}
		return $new_query;
	}

	function all_name($all_val = 0){
		
		$this->fdb->select('all_name, all_name_fr, all_name_dch');
		$this->fdb->where('all_id', $all_val);
		$query = $this->fdb->get('allergence')->result();
		return $query;
	}

	function sub_all_name($sub_all_val = 0,$all_val = 0){
		
		$this->fdb->select('all_name, all_name_fr, all_name_dch');
		$this->fdb->where('all_id', $sub_all_val );
		if( $all_val != 0 ){
			$this->fdb->where( 'parent_all_id', $all_val );
		}
		$sub_query = $this->fdb->get('sub_allergence')->result();
		return $sub_query;
	}

	function get_ing_exception($lang_var = ''){
		if( $lang_var != ''){
			if( $lang_var == 'nl_NL' ){
				$lang_id = 2;
			}
			if( $lang_var == 'en_US' ){
				$lang_id = 1;
			}
			if( $lang_var == 'fr_FR' ){
				$lang_id = 3;
			}
			
			$this->fdb->select('ingredient_text,ingredient_name');
			$except_query = $this->fdb->get_where('ingredient_exception', array('language_id' => $lang_id))->result();
			return $except_query;
		}
	}

	function get_ing_allergen($p_id = 0,$lan='_dch'){
		if ($p_id != 0){
			$this->db->select('allergence'.$lan.', sub_allergence'.$lan.'');
			$query = $this->db->get_where('products_ingredients', array( 'product_id' => $p_id ))->result();
			return $query;
		}
	}

	function get_allergen($final_all = array(),$lang='_dch'){
		if(!empty($final_all)){
			
			$this->fdb->select('all_id as ka_id, all_name'.$lang.' as ka_name');
			$this->fdb->where_in('all_id', $final_all);
			return $this->fdb->get('allergence')->result();
		}
	}

	function get_sub_allergen($final_sub_all = array(),$lang='_dch'){
		if(!empty($final_sub_all)){
			
			$this->fdb->select('all_name'.$lang.' as sub_ka_name, parent_all_id as parent_ka_id');
			$this->fdb->where_in('all_id', $final_sub_all);
			return $this->fdb->get('sub_allergence')->result();
		}
	}

	function fixed($pro_id = 0){
		if( $pro_id != 0 ){
			$this->db->select('id');
			$this->db->where(array('obs_pro_id' => $pro_id, 'fixed' => '0'));
			$query = $this->db->get('fdd_pro_quantity')->result_array();
			return $query;
		}
	}

	function get_favourite_products( $company_id ) {
		$this->db->select( 'fdd_pro_id' );
		$this->db->where( 'company_id', $company_id );
		$result = $this->db->get( 'fdd_pro_fav' )->row_array();
		if( !empty( $result ) ) {
			$pro_ids = json_decode( $result[ 'fdd_pro_id' ] );
			if( !empty( $pro_ids ) ) {
				$this->fdb->select( 'p_id, p_name'.$this->lang_u. ' AS pro_name' );
				$this->fdb->where_in( 'p_id', $pro_ids );
				$this->fdb->order_by( 'p_name'.$this->lang_u );
				$query = $this->fdb->get( 'products' )->result_array();
				return $query;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function delete_favourite_products( $fdd_pro_id, $company_id ) {
		$this->db->select( 'fdd_pro_id' );
		$result = $this->db->get_where( 'fdd_pro_fav' ,array( 'company_id' => $company_id ) )->row_array();
		if( !empty( $result ) ){
			$fdd_pro_ids = json_decode( $result[ 'fdd_pro_id' ] );
			$unset_key = array_search( $fdd_pro_id, $fdd_pro_ids );
			unset( $fdd_pro_ids[ $unset_key ] );
			$fdd_pro_ids = array_values( $fdd_pro_ids );

			$this->db->where( 'company_id', $company_id );
			return $this->db->update( 'fdd_pro_fav', array( 'fdd_pro_id' => json_encode( $fdd_pro_ids ), 'date_added' => date( 'Y-m-d H:i:s' ) ) );
		} else {
			return false;
		}
	}

		function get_product_ingredients_exp($product_id = 0, $k_type = 1){
		$ingredients = array();
		if($product_id){
			$ingredients = $this->db->query(
				"SELECT INGTAB.ki_id, prefix, INGTAB.ki_name, ingredients.ki_name as ki_name_en, ingredients.ki_name_dch,  ingredients.ki_name_fr, have_all_id, aller_type, aller_type_fr, aller_type_dch, allergence, allergence_fr, allergence_dch, sub_allergence, sub_allergence_fr, sub_allergence_dch FROM
					(SELECT a.ki_id,a.prefix,a.ki_name,a.kp_display_order, a.display_order,a.have_all_id,a.aller_type,a.aller_type_fr,a.aller_type_dch,a.allergence,a.allergence_fr,a.allergence_dch,a.sub_allergence,a.sub_allergence_fr,a.sub_allergence_dch
					FROM products_ingredients a
					WHERE product_id = ".$product_id."
					AND ((display_order=1 AND is_obs_ing=1) OR (display_order !=0 AND is_obs_ing=0) OR (0 = (SELECT COUNT(b.ki_id) FROM products_ingredients b WHERE b.product_id = ".$product_id." AND a.kp_id = b.kp_id AND b.ki_id != 0)+(SELECT COUNT(c.ki_id) FROM products_ingredients_vetten c WHERE c.product_id = ".$product_id." AND a.kp_id = c.kp_id AND c.ki_id != 0)+(SELECT COUNT(d.ki_id) FROM products_additives d WHERE d.product_id = ".$product_id." AND a.kp_id = d.kp_id AND d.ki_id != 0)))
					ORDER BY kp_display_order ASC) AS INGTAB
					LEFT JOIN ingredients ON ingredients.ki_id = INGTAB.ki_id
					ORDER BY kp_display_order ASC, display_order ASC")->result();
		}
		return $ingredients;
	}

	function get_all_name( $ka_id = 0, $sel_lang = '_dch', $all = '' ){
		if( $all == 'all' ){
			$this->db->select( 'all_name, all_name_fr, all_name_dch' );
		}
		else{
			$all_name = 'all_name'.$sel_lang;
			$this->db->select( $all_name );
		}
		return $this->db->get_where( 'allergence', array( 'all_id' => $ka_id ) )->row_array();
	}

	function get_sub_all_name( $ka_id = 0, $sel_lang = '_dch', $all = '' ){
		if( $all == 'all' ){
			$this->db->select( 'all_name, all_name_fr, all_name_dch' );
		}
		else{
			$all_name = 'all_name'.$sel_lang;
			$this->db->select( $all_name );	
		}
		return $this->db->get_where( 'sub_allergence', array( 'all_id' => $ka_id ) )->row_array();
	}

	function check_approval_status($id){
			$this->db->select( 'products_ingredients.kp_id' );
			$this->db->join( 'products', 'products.id = products_ingredients.product_id' );
			$query = $this->db->get_where( 'products_ingredients', 
											array( 
												'products_ingredients.product_id' 	=> $id, 
												'products.company_id' 				=> $this->company_id ,
												'products_ingredients.is_obs_ing' 	=> 0
												) )->result_array();
			if( !empty($query) ){
				$query = array_map("unserialize", array_unique(array_map("serialize", $query)));
				$query = array_column($query, 'kp_id');
				
				$this->fdb->where_in( 'p_id', $query );
				$this->fdb->where( 'approval_status', 0 );
				$res = $this->fdb->get( 'products' )->result_array();
				if( !empty( $res ) ) {
					return '0';
				} else {
					return '1';
				}
				return '2';
			}
		}

	function get_shared_by( $parent_proid ) {
		$this->db->select( 'company.company_name' );
		$this->db->where( 'products.id', $parent_proid );
		$this->db->join( 'company', 'company.id = products.company_id' );
		return $this->db->get( 'products' )->row_array();
	}

	/**
	 * Function to remove the labeler logo
	 * @access Public
	 * @return  int 
	 * @author Abhishek Singh
	 */
	function remove_labeler_logo_img( $prod_id ,$image_name ){
		if( $image_name != '' && file_exists( FCPATH.'assets/cp/labeler_logo_extra/'.$image_name ) ){
			unlink( FCPATH.'assets/cp/labeler_logo_extra/'.$image_name );
		}
		$this->db->where( 'product_id', $prod_id );
		$this->db->update( 'products_labeler', array( 'extra_logo_image' => '' ) );
		return  $this->db->affected_rows();
	}

	function mark_sent_prod_as_approved( $prod_id ) {
		$this->db->where( 'id', $prod_id );
		return $this->db->update( 'products', array( 'prod_sent' => '2' ) );		
	}

	function extra_notification_free_field($pro_id){
		$this->db->select('extra_notification_free_field');
		$this->db->where('product_id',$pro_id);
		return $this->db->get("products_labeler")->result();
	}
}