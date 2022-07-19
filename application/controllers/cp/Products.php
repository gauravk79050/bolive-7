<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class products extends CI_Controller{

	var $rows_per_page = '';
	var $company_id = '';
	var $ibsoft_active = false;

	function __construct(){

		parent::__construct();
    $this->fdb = $this->load->database('fdb',TRUE);
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->helper('phpmailer');
		$this->load->helper('cookie');

		$this->load->library('ftp');
		$this->load->library('Messages');
		$this->load->library('session');
        $this->load->library('messages');
		$this->load->library('utilities');
		$this->load->library('pagination');
		$this->load->library('form_validation');
		//$this->load->library('upload');

		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mpackages');
		$this->load->model('Mcategories');
		$this->load->model('Mcalender');
		$this->load->model('Mclients');
		$this->load->model('mcp/Mcompanies');
		$this->load->model('M_fooddesk');
		$this->load->model('Mproducts');

		if($this->input->get('token')){
			$privateKey = 'o!s$_7e(g5xx_p(thsj*$y&27qxgfl(ifzn!a63_25*-s)*sv8';
			$userId = $this->input->get('name');
			$hash = $this->createToken($privateKey, current_url(), $userId);

			if($this->input->get('token') == $hash){
				$this->product_validate($userId);
			}
		}
		
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

		/*if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/products/page_not_found');
		}*/
		$this->load->model('MFtp_settings');
		$this->rows_per_page = 20;

		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
		$this->lang_u = get_lang( $_COOKIE['locale'] );


	}
	function index(){
		$this->lijst();
	}


	function lijst()
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->company_role == 'master' || $this->company_role == 'super' )
	    {

			if($this->input->post('save'))
			{
				$ids = $this->input->post('ids');
			  	if( is_array($ids) && !empty($ids) )
			  	{
			    	foreach($ids as $key => $val)
				 	{

				 		$update_data = array();
				 		$pro_num = $val['pro_art_no'];
				 		if(strlen(trim($pro_num) < 8)){
				 			$pro_num = str_pad(trim($pro_num),8,"0",STR_PAD_LEFT);
				 		}
				 		$update_data['pro_art_num']=$pro_num;
				 		$update_data['proname']=$val['pro_name'];
				 		$update_data['prodescription']=$val['pro_desc'];

				 		$price_per_unit = 0;
				 		if ($val['pro_price_per_unit'] != "")
				 		{

				 			$price_per_unit = $this->number2db($val['pro_price_per_unit']);
				 		}

				 		$update_data['price_per_unit']=$price_per_unit;

				 		$price_weight = 0;
				 		if(isset($val['pro_weight']))
				 		{

					 		if ($val['pro_weight'] != "")
					 		{
					 			$price_weight = $this->number2db($val['pro_weight']);

							  	$price_weight = $price_weight/1000;

	 						  	$price_weight = $this->number2db($price_weight);
					 		}
				 		}

				 		$update_data['price_weight']=$price_weight;

				 		$price_per_person = 0;
				 		if ($val['pro_price_per_person'] != "")
				 		{
				 			$price_per_person = $this->number2db($val['pro_price_per_person']);
				 		}
				 		$update_data['price_per_person']=$price_per_person;
// 					    $update_data = array();
// 						$update_data['pro_art_num'] = $this->input->post('pro_art_num_'.$product_id);
// 						$update_data['proname'] = addslashes($this->input->post('proname_'.$product_id));
// 						$update_data['prodescription'] = addslashes($this->input->post('prodescription_'.$product_id));

// 						$price_per_unit = 0;
// 						$price_per_unit = @$this->input->post('price_per_unit_'.$product_id);
// 						if($price_per_unit)
// 							$price_per_unit = $this->number2db($price_per_unit);
// 						$update_data['price_per_unit'] = $price_per_unit;

// 						$price_weight = 0;
// 						$price_weight = @$this->input->post('price_weight_'.$product_id);
// 						if($price_weight)
// 						{
// 							$price_weight = $this->number2db($price_weight);
// 						  	$price_weight = $price_weight/1000;
// 						  	$price_weight = $this->number2db($price_weight);
// 						}
// 						$update_data['price_weight'] = $price_weight;
// 						$update_data['pro_display'] = ($key + 1);

						$this->Mproducts->update_product_details($val['pro_id'],$update_data);
						$update_data=array();
				 	}
				 	$this->messages->add(_('Products updated successfully.'), 'success');
			  	}
			  	$product_id_list = $this->input->post('product_id_list');
			  	if( is_array($product_id_list) && !empty($product_id_list) )
			  	{

			  		foreach($product_id_list as $key => $val)
			  		{
			  			$update_data['pro_display']=$key+1;
			  			$this->Mproducts->update_product_details($val,$update_data);
			  		}
			  	}
			}

			$data['products']=array();
			$data['cat_id']=NULL;
			$data['sub_cat_id']=NULL;
			$data['links']=NULL;

			$data['total_products'] = 0;
			$current_url=$this->uri->uri_string();
			$url_variable=$this->uri->uri_to_assoc(4);

			//print_r($url_variable);die();
			/*$this->load->library('pagination');
			if($url_variable){
				if(array_key_exists('subcategory_id', $url_variable) && array_key_exists('page', $url_variable)){
					$pieces = explode("page", $current_url);
					$pconfig['base_url'] = base_url().$pieces[0]."page/";
					$pconfig['uri_segment'] =9;//gives offset
					$pconfig['total_rows']= $data['total_products'] = count($this->Mproducts->get_products($url_variable['category_id'],$url_variable['subcategory_id']));

				}else if(array_key_exists('category_id', $url_variable) && array_key_exists('page', $url_variable)){
					$pieces = explode("page", $current_url);
					$pconfig['base_url'] = base_url().$pieces[0]."page/";
					$pconfig['uri_segment'] =7;
					$pconfig['total_rows'] = $data['total_products'] = count($this->Mproducts->get_products($url_variable['category_id']));
				}
			}*/

			$data['category_data'] = $this->Mcategories->get_categories();
			//print_r($data);die();
			if(array_key_exists('category_id', $url_variable)){
				//----for the pagnation---//
				/*$pconfig['first_link'] = _('First');
				$pconfig['last_link'] = _('Last');
				$pconfig['next_link'] = _('Next');
				$pconfig['prev_link'] = _('Previous');
				$pconfig['per_page'] = $this->rows_per_page;
				$this->pagination->initialize($pconfig);
				$data['links'] = $this->pagination->create_links();*/

				//-----------------------//
				$data['cat_id']=$url_variable['category_id'];
				$this->load->model('Msubcategories');
				$data['subcategory_data']=$this->Msubcategories->get_sub_category($url_variable['category_id']);
				$products = array();

				if(array_key_exists('subcategory_id', $url_variable)){
					$sub_cat_id=$url_variable['subcategory_id'];
					$data['sub_cat_id']=$url_variable['subcategory_id'];
					$data['products']= $products = $this->Mproducts->get_products($url_variable['category_id'],$sub_cat_id);//getting product of the subcategory
				}else{

					$data['sub_cat_id']='-1';
					$data['products']= $products = $this->Mproducts->get_products($url_variable['category_id'],'-1');//getting product of related to category
				}

				if(!empty($products)){
					$products = $this->join_pdf_name_from_fdd($products);
					$products = $this->is_contains_semi($products);
				}

				$data['products'] = $products;

			}

			if(array_key_exists('filtered_product', $url_variable)){
				$filter_keyword=$url_variable['filtered_product'];

				$data['products']=$products=$this->Mproducts->get_product_by_keyword($filter_keyword);
				if(!empty($products)){
					$products = $this->join_pdf_name_from_fdd($products);
				}
				$data['products'] = $products;
			}

			$data['general_settings'] = $this->Mgeneral_settings->get_general_settings();

			$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
			$this->db->where($where);
			$this->db->where(array('company_id'=>$this->company_id,'categories_id'=>0));
			$data['no_cat'] = $this->db->count_all_results('products');

			$company = $this->Mcompany->get_company();
			$data['ac_type_id']  = $company[0]->ac_type_id;
			$data['show_recipe'] = $company[0]->show_recipe;
			$data['trail_date']=$company[0]->trial;
			$data['on_trial']=$company[0]->on_trial;
			$type_id=$company[0];
			$company_type_id=$type_id->type_id;
			$result = $this->Mcompany->get_co_type($company_type_id);
			$type_name=$result['company_type_name'];
			$data['type_slug'] =str_replace(" ","+",$type_name);

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();

			$this->load->model('mnotify');
			$company_lang = '2';
			if( $_COOKIE['locale'] == 'fr_FR' ){
				$company_lang = '3';
			}elseif( $_COOKIE['locale'] == 'en_US'  ){
				$company_lang = '1';
			}
			$data['notifications'] = $this->mnotify->get_notifications( NULL, date('Y-m-d'), $company_lang );
			$data['closed_noti'] = $this->mnotify->get_closed_noti($this->company->id);

			$company_slug=$company[0];
			$data['company_slug']=$company_slug->company_slug;

			$data['shared_products'] = $this->Mproducts->get_shared_products_list();
	    	if(($this->company_id == 105 || $this->company_id == 87))
			{
				$data['content'] = 'cp/view_products';
				
				$this->load->view('cp/cp_view',$data);
			}
			else
			{
				$xyz = $this->db->get_where('maintenence',array('id'=>1))->result_array();
				if($xyz[0]['checked'] == '1')
				{
					$data['cont'] = 1;
					$data['content'] = 'cp/under_maintenence';
					
					$this->load->view('cp/_cp_view',$data);
				}
				else {
					$data['content'] = 'cp/view_products';
					
					$this->load->view('cp/cp_view',$data);
				}
			}
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}
	function get_sub_category_product(){

		$cat_id = $this->input->post('cat_id');
		$subcat_id = $this->input->post('subcat_id');
		$cu_pid = $this->input->post('cu_pid');

		$subcat_array = array();
		if($subcat_id == -1){
			$this->db->select('id,subname');
			$this->db->where('categories_id',$cat_id);
			$this->db->order_by('suborder_display','ASC');
			$subcat = $this->db->get('subcategories')->result();

			if(!empty($subcat)){
				foreach ($subcat as $val){
					$subcat_array[$val->id] = $val->subname;
				}
			}
		}

		$this->db->select('id,proname');
		if($cu_pid != 0)
			$this->db->where('id !=',$cu_pid);
		$this->db->where(array('company_id'=>$this->company_id,'categories_id'=>$cat_id,'subcategories_id'=>$subcat_id));
		$this->db->order_by('pro_display','ASC');
		$product = $this->db->get('products')->result();

		$product_array = array();
		if(!empty($product)){
			foreach ($product as $val){
				$product_array[$val->id] = $val->proname;
			}
		}

		echo json_encode(array('subcat'=>$subcat_array,'product'=>$product_array));
	}

	private function join_pdf_name_from_fdd($products){ 
		if(!empty($products)) 
			foreach($products as $i => $product){ 
				if($product->direct_kcp == 1 && $product->direct_kcp_id != 0){
					$this->fdb->select( 'products.data_sheet,products.product_type' );
					$this->fdb->where(array('p_id'=> $product->direct_kcp_id));
					$result = $this->fdb->get('products')->result();
					if(!empty($result))
						$products[$i]->pdf_name = $result[0]->data_sheet;
						$products[$i]->product_type = $result[0]->product_type;
				}
			}
		return $products;
	}
	function get_product_name(){

		$this->db->select("products.id,products.proname");
		$this->db->where("((semi_product = 0) OR (semi_product = 1 AND direct_kcp = 0))");
		$id=$this->db->where("company_id",$this->company_id);

		$pro_name = $this->db->get("products")->result_array();

		$proname_array = array();
		if(!empty($pro_name)){
			foreach($pro_name as $val){
				$arr['label'] = stripslashes($val['proname']);
				$arr['value'] = $val['id'];
				$proname_array[] = $arr;
			}
		}
		echo json_encode($proname_array);
	}
	function get_recipe_AjaxIngre($type = 0){
		if($type){
			$search_str = $this->input->post('search_str');
			$result_array = array();

			$srch_arr = explode(' ', $search_str);
			foreach ($srch_arr as $srch_val){
				if($srch_val != ''){
					$result_array1 = $this->M_fooddesk->get_searched_recipe_ingre($srch_val, $type);
					if(!empty($result_array)){
						$result_array = array_values(array_uintersect($result_array, $result_array1, function($val1, $val2){ return strcmp($val1['ki_id'], $val2['ki_id']); }));
					}else{
						$result_array = $result_array1;
					}
				}
			}
			$result_array = array_values(array_map("unserialize",array_unique(array_map("serialize", $result_array))));
			echo json_encode($result_array);
		}
	}
	function product_recipe($ingre_id = 0){
		if($ingre_id != 0){
			$data['recipe_products'] = $this->M_fooddesk->get_ingre_product($ingre_id,1);

			$this->db->select('ki_name');
			$result = $this->db->get_where('products_ingredients',array('ki_id'=>$ingre_id))->result_array();
			$data['ingre_name'] = $result[0]['ki_name'];
		}

		$data['content'] = 'cp/product_recipe';
		$this->load->view('cp/cp_view', $data);
	}
	function update_checkbox()
	{

		$this->Mproducts->update_checkbox();
	}
	function update_product_status()
	{

		$this->Mproducts->update_product_status();
		echo 'OK';
	}
		public function print_product($action, $cat_id = 0, $subcat_id = 0, $ids = ''){

		$this->load->model('Mcategories');
		$this->load->model('Msubcategories');

		$cat_name = '';
		$subcat_name = '';

		$row_html = '';

		//if($action == 'print_these'){
	/*
		//get allergence array from FDD db
		
		$this->fdb->select('all_id,all_name_dch');
		$aller_arr = $this->fdb->get('allergence')->result_array();
		foreach ($aller_arr as $val){
			$allergence_words[strtolower($val['all_name_dch'])] = $val['all_id'];
		}
		
	*/
		if($cat_id > 0){
			$cat_name = $this->Mcategories->get_cat_name($cat_id);
			if(!empty($cat_name))
				$cat_name = $cat_name['name'];
		}

		if($subcat_id > 0){
			$subcat_name = $this->Msubcategories->get_sub_cat_name($subcat_id);
			if(!empty($subcat_name))
				$subcat_name = ' > '.$subcat_name['subname'];
		}
		if($ids != ''){
			//print these..

			$ids = substr($ids, 1);
			$pro_ids = explode('.',$ids);

			//$pro_ingr = $this->Mproducts->get_product_ingredients($pro_ids[0]);

			if(!empty($pro_ids)){

				$row_html .= '<h3 style="font-family: arial;">'._('Product List').'<span style="font-size: 8pt;">&nbsp;'._('(including ingredients)').'</span></h3>';

				$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

				$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.$subcat_name.'</th></tr></thead><tbody>';

				//$row_html .= '<tr style="text-align: left;"><td style="width: 250px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
				$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

				//Looping products using product ids..
				foreach($pro_ids as $p_id){

					$row_html .= '<tr>';

					$product = $this->Mproducts->get_product_information($p_id);

					$product = $product[0];

					$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

					/*$k_ingredients = $this->Mproducts->get_product_ingredients_dist($p_id);
					$product_ingredients_vetten = $this->Mproducts->get_product_ingredients_vetten_dist($p_id);
	 				$product_additives = $this->Mproducts->get_product_additives_dist($p_id);
					if(!empty($k_ingredients)){
						$ing_str = '';
						//Looping product ingredients to create ingredient string..
						foreach ($k_ingredients as $ingredients){
							//$ing_str .= ', '.stripslashes($k_ingredient->prefix).' '.stripslashes($k_ingredient->ki_name);

							if($ingredients->ki_name == ')' ){
								$ing_str = substr($ing_str, 0, -2);
								$ing_str .= ' ';
							}
							if($ingredients->ki_name == '(' ){
								$ing_str = substr($ing_str, 0, -2);
								$ing_str .= ' ';
							}

							if($ingredients->ki_id != 0){
								if($ingredients->prefix == ''){
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
								}else{
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
								}
							}else if($ingredients->ki_name == ')'){

								$ing_str .= $ingredients->ki_name.', ';

							}else if($ingredients->ki_name == '('){

								$ing_str .= $ingredients->ki_name.' ';

							}else{
								if($ingredients->prefix == ''){
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
								}else{
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
								}
							}

						}
						$ing_str = substr($ing_str, 0, -2);

						$ing_end = "";
						if(!empty($product_ingredients_vetten)){
							$ing_end .= "Plantaardige vetstof(";
							foreach ($product_ingredients_vetten as $vetten){
								$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words).", ";
							}
							$ing_end = rtrim(trim($ing_end),",");
							$ing_end .= ")";
						}

						if(!empty($product_additives)){
							$additive_arr = array();
							foreach ($product_additives as $add){
								if(!in_array($add->add_name,$additive_arr)){
									$additive_arr[] = $add->add_name;
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
									if(($add->add_name == $additive_arr[$i]) && ($add->ki_name != "")){
										$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words).", ";
										$count = $count+1;
									}
								}
								$add_ing = rtrim(trim($add_ing),",");
								if($count == 1){
									$ing_end .= " ".$add_ing;
								}
								elseif ($count >1 ){
									if($additive_arr[$i] == "others"){
										$ing_end .= $add_ing;
									}else{
										$ing_end .= "(".$add_ing.")";
									}
								}
							}
						}
						if($ing_end != ""){
							$ing_end = ", ".$ing_end;
						}

						$ing_str .= $ing_end;
						if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
							$product->ingredients = substr($ing_str, 2);
						else
							$product->ingredients = $product->ingredients;
					}

					if($product->ingredients == '')
						$product->ingredients = '--';
					$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->ingredients.'</td>';*/


					$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
					$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);
					if(!empty($k_allergence)){
						$allrg_str = '';
						//Looping product allergence to create allergence string..
						foreach ($k_allergence as $k_allerg){
							$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);

							if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
								$a1 = '';
								if(!empty($k_sub_allergence)){
									$a1 .= ' (';
									foreach ($k_sub_allergence as $k_sub_allerg){
										if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
											$a1 .=  $k_sub_allerg->sub_ka_name.', ';
										}
									}
									$a1 = rtrim($a1,', ');
									$a1 .= ')';
									$a1 = str_replace('()', '', $a1);
								}
								$allrg_str .= $a1;
							}
						}
						if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
							if(strpos($allrg_str,'Melk') != false && strpos($allrg_str,'Lactose') != false){
								$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
								$allrg_str = str_replace('Lactose, ', '', $allrg_str);
							}
							$product->allergence = substr($allrg_str, 2);
						}
						else
							$product->allergence = $product->allergence;
					}
					if($product->allergence == '')
						$product->allergence = '--';
					$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

					/*$k_traces = $this->Mproducts->get_product_traces($p_id);
					if(!empty($k_traces)){
						$tra_str = '';
						//Looping product trashes to create trashes string..
						foreach ($k_traces as $k_trace){
							$tra_str .= ', '.stripslashes($k_trace->prefix).' '.stripslashes($k_trace->kt_name);
						}
						if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
							$product->traces_of = substr($tra_str,2);
						else
							$product->traces_of = $product->traces_of;
					}
					if($product->traces_of == '')
						$product->traces_of = '--';
					$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->traces_of.'</td>';*/

					$row_html .= '</tr>';

				}
				$row_html .= '</tbody></table>';
			}
		} else {
			$row_html = '';

			$row_html .= '<h3 style="font-family: arial;">Productenlijst<span style="font-size: 8pt;">&nbsp;(inclusief ingredienten)</span></h3>';

			$cat_arr = $this->Mcategories->get_category(array('company_id' => $this->company_id));

			if(!empty($cat_arr)){
				//looping categories..
				foreach($cat_arr as $cat){

					$subcat_arr = $this->Msubcategories->get_subcategory(array('categories_id'=> $cat->id));
					//print_r($subcat_arr);

					if($cat->id > 0){
						$cat_name = $this->Mcategories->get_cat_name($cat->id);
						if(!empty($cat_name))
							$cat_name = $cat_name['name'];
					}

					if(!empty($subcat_arr)){

						//looping subcategories..
						foreach($subcat_arr as $subcat){
							if($subcat->id > 0){
								$subcat_name = $this->Msubcategories->get_sub_cat_name($subcat->id);
								if(!empty($subcat_name))
									$subcat_name = ' > '.$subcat_name['subname'];
							}

							$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

							$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.$subcat_name.'</th></tr></thead><tbody>';

							// $row_html .= '<tr style="text-align: left;"><td style="width: 150px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('INGREDIENTS').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
							//$row_html .= '<tr style="text-align: left;"><td style="width: 250px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
							$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

							$products = $this->Mproducts->get_products($cat->id, $subcat->id);

							if(!empty($products)){
								//Looping products using product ids..
								foreach($products as $product){

									$p_id = $product->id;

									$row_html .= '<tr>';

									$product = $this->Mproducts->get_product_information($p_id);

									$product = $product[0];

									$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

									/*$k_ingredients = $this->Mproducts->get_product_ingredients_dist($p_id);
									$product_ingredients_vetten = $this->Mproducts->get_product_ingredients_vetten_dist($p_id);
					 				$product_additives = $this->Mproducts->get_product_additives_dist($p_id);
									if(!empty($k_ingredients)){
										$ing_str = '';
										//Looping product ingredients to create ingredient string..
										foreach ($k_ingredients as $ingredients){
											if($ingredients->ki_name == ')' ){
												$ing_str = substr($ing_str, 0, -2);
												$ing_str .= ' ';
											}
											if($ingredients->ki_name == '(' ){
												$ing_str = substr($ing_str, 0, -2);
												$ing_str .= ' ';
											}

											if($ingredients->ki_id != 0){
												if($ingredients->prefix == ''){
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
												}else{
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
												}
											}else if($ingredients->ki_name == ')'){

												$ing_str .= $ingredients->ki_name.', ';

											}else if($ingredients->ki_name == '('){

												$ing_str .= $ingredients->ki_name.' ';

											}else{
												if($ingredients->prefix == ''){
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
												}else{
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
												}
											}
										}
										$ing_str = substr($ing_str, 0, -2);

										$ing_end = "";
										if(!empty($product_ingredients_vetten)){
											$ing_end .= "Plantaardige vetstof(";
											foreach ($product_ingredients_vetten as $vetten){
												$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words).", ";
											}
											$ing_end = rtrim(trim($ing_end),",");
											$ing_end .= ")";
										}

										if(!empty($product_additives)){
											$additive_arr = array();
											foreach ($product_additives as $add){
												if(!in_array($add->add_name,$additive_arr)){
													$additive_arr[] = $add->add_name;
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
													if(($add->add_name == $additive_arr[$i]) && ($add->ki_name != "")){
														$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words).", ";
														$count = $count+1;
													}
												}
												$add_ing = rtrim(trim($add_ing),",");
												if($count == 1){
													$ing_end .= " ".$add_ing;
												}
												elseif ($count >1 ){
													if($additive_arr[$i] == "others"){
														$ing_end .= $add_ing;
													}else{
														$ing_end .= "(".$add_ing.")";
													}
												}
											}
										}
										if($ing_end != ""){
											$ing_end = ", ".$ing_end;
										}

										$ing_str .= $ing_end;
										if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
											$product->ingredients = substr($ing_str,2);
										else
											$product->ingredients = $product->ingredients;
									}
									if($product->ingredients == '')
										$product->ingredients = '--';
									$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->ingredients.'</td>';*/

									$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
									$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);
									if(!empty($k_allergence)){
										$allrg_str = '';
										//Looping product allergence to create allergence string..
										foreach ($k_allergence as $k_allerg){
											$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);
											if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
												$a1 = '';
												if(!empty($k_sub_allergence)){
													$a1 .= ' (';
													foreach ($k_sub_allergence as $k_sub_allerg){
														if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
															$a1 .=  $k_sub_allerg->sub_ka_name.', ';
														}
													}
													$a1 = rtrim($a1,', ');
													$a1 .= ')';
													$a1 = str_replace('()', '', $a1);
												}
												$allrg_str .= $a1;
											}
										}
										if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
											if(strpos($allrg_str,'Melk') !== false && strpos($allrg_str,'Lactose') !== false){
												$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
												$allrg_str = str_replace(',  Lactose', '', $allrg_str);
											}
											$product->allergence = substr($allrg_str,2);
										}
										else
											$product->allergence = $product->allergence;
									}
									if($product->allergence == '')
										$product->allergence = '--';
									$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

									/*$k_traces = $this->Mproducts->get_product_traces($p_id);
									if(!empty($k_traces)){
										$tra_str = '';
										//Looping product trashes to create trashes string..
										foreach ($k_traces as $k_trace){
											$tra_str .= ', '.stripslashes($k_trace->prefix).' '.stripslashes($k_trace->kt_name);
										}
										if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
											$product->traces_of = substr($tra_str,2);
										else
											$product->traces_of = $product->traces_of;
									}
									if($product->traces_of == '')
										$product->traces_of = '--';
									$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->traces_of.'</td>';*/

									$row_html .= '</tr>';

								}
							} else {
								$row_html .= '<tr><td style="padding-left: 10px; vertical-align: top;">--</td><td style=" vertical-align: top;">--</td></tr>';
							}
							$row_html .= '</tbody></table><br/><br/>';
						}
					}

					//direct products of this category..
					$products = $this->Mproducts->get_products($cat->id, -1);

					//print_r($products);

					if(!empty($products)){

						$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

						$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.'</th></tr></thead><tbody>';

						// $row_html .= '<tr style="text-align: left;"><td style="width: 150px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('INGREDIENTS').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
						//$row_html .= '<tr style="text-align: left;"><td style="width: 250px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
						$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

						//Looping products using product ids..
						foreach($products as $product){

							$p_id = $product->id;

							$row_html .= '<tr>';

							$product = $this->Mproducts->get_product_information($p_id);

							$product = $product[0];

							$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

							/*$k_ingredients = $this->Mproducts->get_product_ingredients_dist($p_id);
							if(!empty($k_ingredients)){
								$ing_str = '';
								//Looping product ingredients to create ingredient string..
								foreach ($k_ingredients as $ingredients){
									if($ingredients->ki_name == ')' ){
										$ing_str = substr($ing_str, 0, -2);
										$ing_str .= ' ';
									}
									if($ingredients->ki_name == '(' ){
										$ing_str = substr($ing_str, 0, -2);
										$ing_str .= ' ';
									}

									if($ingredients->ki_id != 0){
										if($ingredients->prefix == ''){
											$ing_str .= stripslashes($ingredients->ki_name).', ';
										}else{
											$ing_str .= stripslashes($ingredients->ki_name).'('.$ingredients->prefix.')'.', ';
										}
									}else if($ingredients->ki_name == ')'){

										$ing_str .= $ingredients->ki_name.', ';

									}else if($ingredients->ki_name == '('){

										$ing_str .= $ingredients->ki_name.' ';

									}else{
										if($ingredients->prefix == ''){
											$ing_str .= stripslashes($ingredients->ki_name).', ';
										}else{
											$ing_str .= stripslashes($ingredients->ki_name).'('.$ingredients->prefix.')'.', ';
										}
									}
								}
								$ing_str = substr($ing_str, 0, -2);

								if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
									$product->ingredients = substr($ing_str,2);
								else
									$product->ingredients = $product->ingredients;
							}
							if($product->ingredients == '')
								$product->ingredients = '--';
							$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->ingredients.'</td>';*/


							$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
							$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);
							if(!empty($k_allergence)){
								$allrg_str = '';
								//Looping product allergence to create allergence string..
								foreach ($k_allergence as $k_allerg){
									$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);
									if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
										$a1 = '';
										if(!empty($k_sub_allergence)){
											$a1 .= ' (';
											foreach ($k_sub_allergence as $k_sub_allerg){
												if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
													$a1 .=  $k_sub_allerg->sub_ka_name.', ';
												}
											}
											$a1 = rtrim($a1,', ');
											$a1 .= ')';
											$a1 = str_replace('()', '', $a1);
										}
										$allrg_str .= $a1;
									}
								}
								if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
									if(strpos($allrg_str,'Melk') !== false && strpos($allrg_str,'Lactose') !== false){
										$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
										$allrg_str = str_replace(',  Lactose', '', $allrg_str);
									}
									$product->allergence = substr($allrg_str,2);
								}
								else
									$product->allergence = $product->allergenc;
							}
							if($product->allergence == '')
								$product->allergence = '--';
							$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

							/*$k_traces = $this->Mproducts->get_product_traces($p_id);
							if(!empty($k_traces)){
								$tra_str = '';
								//Looping product trashes to create trashes string..
								foreach ($k_traces as $k_trace){
									$tra_str .= ', '.stripslashes($k_trace->prefix).' '.stripslashes($k_trace->kt_name);
								}
								if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
									$product->traces_of = substr($tra_str,2);
								else
									$product->traces_of = $product->traces_of;
							}
							if($product->traces_of == '')
								$product->traces_of = '--';
							$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->traces_of.'</td>';*/


							$row_html .= '</tr>';

						}
						$row_html .= '</tbody></table><br/><br/>';
					}

				}
			}
		}

		require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');

		//print_r($pro_ingr);

		//$this->load->library('mpdf');

		$report_name = 'report'.time().'.pdf';

		$mpdf=new mPDF('c');

		$mpdf->WriteHTML($row_html);

		$mpdf->Output('Products.pdf', 'D');

		//}
	}
	function delete_product(){
		if ($this->input->post('remove_product_arr')){
			$remove_product_arr=$this->input->post('remove_product_arr');
			if (!empty($remove_product_arr)){

				foreach ($remove_product_arr as $product_id_key=>$product_id_val){
					$this->delete_child_parent_prod($product_id_val);
					$data = $this->Mproducts->delete_product($product_id_val);
					echo $data;
				}
			}
		}
		else {

			$this->delete_child_parent_prod($this->input->post('id'));
			$data = $this->Mproducts->delete_product();
			echo $data;
		}
		$this->session->set_userdata('action', 'category_json');

		$comp_id = $this->company->id;
		$company = $this->Mcompany->get_company();
		$ac_type_id = $company[0]->ac_type_id;

		$categories_id = $this->input->post('categories_id');
		$subcategories_id =  $this->input->post('subcategories_id');
		$this->get_category_prod_status( $comp_id , $ac_type_id , $categories_id , $subcategories_id );
		$this->session->set_userdata('action', 'category_json');
	}
	function delete_child_parent_prod($id){

		$child_p = $this->Mproducts->check_prod_shared_stat($id);
		if(!empty($child_p)){
			foreach ($child_p as $ch_p){
				$this->Mproducts->delete_product($ch_p['id']);
			}
		}
		$this->db->select('parent_proid, company_id');
		$parent_p = $this->db->get_where('products',array('id'=> $id,'parent_proid !='=> 0))->result();
		if(!empty($parent_p)){
			$this->db->where(array('proid'=>$parent_p[0]->parent_proid,'to_comp_id'=>$parent_p[0]->company_id));
			$this->db->update('products_shared',array('status'=> '0'));
		}
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

	function rename_product(){
		$pro_id = $this->input->post('product_id');
		$field_val = trim(addslashes($this->input->post('field_val')));
		$pro_field = $this->input->post('field');

		if(($pro_field == 'price_per_unit') || ($pro_field == 'price_per_person')){
			$field_val = $this->number2db($field_val);
		}
		elseif($pro_field == 'price_weight'){
			$field_val = $this->number2db($field_val/1000);
		}


		if($this->Mproducts->rename_product($pro_id, array($pro_field=>$field_val))){
			echo $pro_id;
		}else{
			echo '0';
		}
	}
	function close_notification($noti_id = NULL){

		if($noti_id != NULL){
			$insert_array = array(
					'company_id' => $this->company->id,
					'notification_id' => $noti_id,
					'closing_date' => date("Y-m-d H:i:s")
			);
			$this->load->model("mnotify");
			$result = $this->mnotify->insert_into_close_notification($insert_array);
		}
		echo $result;
	}
	function get_subcategory(){

		$this->load->model('Msubcategories');

		$catid = $this->input->post('catid');
		if($catid != '' && $catid != '0'){

		    $catid = (int)$catid;
			$result=$this->Msubcategories->get_sub_category($catid);

			if( !empty( $result ) ){

				$return_data = (json_encode(array('success'=>$result)));

			}else{

				$products = $this->Mproducts->get_products( $catid, '', '', '', false );

				if( !empty($products) ){
					$return_data = json_encode(array('error'=>'no_sub_cat','products'=>$products));
				}else{
					$return_data = json_encode(array('error'=>'no_sub_cat','products'=>'no_products'));
				}
			}
		}else{
			$return_data = json_encode(array('error'=>'no_cat_id'));
		}
		echo $return_data;
	}
	function assign_category(){

		$this->load->model('Msubcategories');
		if($this->input->post('checkbox')){
			if($this->input->post('cat-assign-button-up')){
				$cat_id = $this->input->post('cat-assign-up');
				$subcat_id = $this->input->post('subcat-assign-up');
			}
			elseif($this->input->post('cat-assign-button-down')){
				$cat_id = $this->input->post('cat-assign-down');
				$subcat_id = $this->input->post('subcat-assign-down');
			}
			$proid_arr = $this->input->post('checkbox');
			if($cat_id != -1){
				foreach($proid_arr as $pro_id){
					$result = $this->Mproducts->update_product_details($pro_id,array('categories_id'=>$cat_id,'subcategories_id'=>$subcat_id));
				}
				if($result){
					$this->session->set_flashdata('cat_update',_('Category and Subcategory Updated Successfully'));
				}
			}
			$this->session->set_userdata('action', 'category_json');

			if(count($this->uri->segments) == 5){
				redirect('cp/products/assign_category/category_id/'.end($this->uri->segments));
			}
			else{
				redirect('cp/products/assign_category/');
			}
		}
		else {
			$url_variable = $this->uri->uri_to_assoc(4);

			$data['category_data'] = $this->Mcategories->get_categories();
			if(array_key_exists('category_id', $url_variable)){
				$data['cat_id']=$url_variable['category_id'];
				if($url_variable['category_id'] !='-1'){
					$this->load->model('Msubcategories');
					$data['subcategory_data']=$this->Msubcategories->get_sub_category($url_variable['category_id']);

					$products2 = array();

					$data['sub_cat_id']='-1';
					$products1 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],'-1');//getting product of related to category

					$this->db->select('id');
					$subcategory_data = $this->db->get_where('subcategories',array('categories_id'=>$url_variable['category_id']))->result_array();

					$id = array();
					foreach($subcategory_data as $var) {
						$id[] = $var['id'];
					}
					if(!empty($id)){
						$products2 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],$id);//getting product of related to category
					}
					$data['products'] = array_merge($products1,$products2);
				}
				else {
					$data['products'] = $this->Mproducts->get_products_to_assign();
				}
			}
	 			$data['category_data']=$this->Mcategories->get_categories();

	 			$this->db->select(array( 'subcategories.id','subcategories.subname','subcategories.categories_id'));
	 			$this->db->join('categories','subcategories.categories_id=categories.id');
	 			$this->db->where('categories.company_id',$this->company_id);
	 			$data['subcategory_data'] = $this->db->get('subcategories')->result_array();

	 			$data['content'] = 'cp/assign_cat_subcat';
				$this->load->view('cp/cp_view', $data);
			}
	}

	function product_clone($clone_of_id = null, $semi_product = 0){
		//print_r($clone_of_id);die;
		if($clone_of_id){

			$show = $this->Mproducts->add_product_clone($clone_of_id);

			$this->session->set_userdata('action', 'category_json');

			if($semi_product == 1){
				redirect('cp/products/semi_products/');
			}
			elseif($semi_product == 2){
				redirect('cp/products/semi_products_extra/');
			}
			else{
				redirect(('cp/products/lijst/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
			}
		}else{
			echo "This is not the valid product id";
		}
	}
	function get_sub_category()
	{
		$this->load->model('Msubcategories');
		$subcategories=$this->Msubcategories->get_sub_category($this->input->post('cat_id'));
		echo json_encode($subcategories);
	}
	function subcategories($category = NULL, $category_id=NULL, $param = NULL)
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

	    if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$this->load->model('Msubcategories');

			$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender
			$data['category_data'] = $this->Mcategories->get_categories();
			$data['subcategory_data'] = NULL;
			$data['cat_id']='';

			if($this->input->post('update')){
				$subcat_ids = $this->input->post('sub_cat_ids');
				if(is_array($subcat_ids) && !empty($subcat_ids)){
					foreach ($subcat_ids as $key => $subcat_id)
						$this->Msubcategories->update_sub_cat(array('suborder_display' => ($key+1)), $subcat_id);

					$this->session->set_userdata('action', 'category_json');
				}
			}

			if( $category == "category_id" )
			{
				$data['cat_id'] = $category_id;
				/*$pconfig['base_url'] = base_url()."cp/cdashboard/subcategories/category_id/".$category_id;

				$pconfig['total_rows'] = $data['total_sub_cat'] = count($this->Msubcategories->get_sub_category($category_id));
				$pconfig['per_page'] = $this->rows_per_page;
				$pconfig['uri_segment'] = 6;
				//echo $data['total_sub_cat'];
				$this->pagination->initialize($pconfig);*/
				$data['subcategory_data'] = $this->Msubcategories->get_sub_category($category_id,'',$param);
				//$data['links'] = $this->pagination->create_links();
				$data['links'] = NULL;
				$data['content'] = 'cp/subcategories';
				$this->load->view('cp/cp_view',$data);
			}
			else
			{
				$data['links'] = NULL;
				$data['content'] = 'cp/subcategories';
				$this->load->view('cp/cp_view',$data);
			}
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}
	private function is_contains_semi($products){
		if(!empty($products))
		foreach($products as $i => $product){
			if($product->direct_kcp == 0){
				$this->db->select('id');
				$this->db->where(array('obs_pro_id'=> $product->id,'semi_product_id !='=>0));
				$result = $this->db->get('fdd_pro_quantity')->result();
				if(!empty($result))
					$products[$i]->can_move = 0;
				else
					$products[$i]->can_move = 1;
			}
		}
		return $products;
	}

	function products_addedit(){
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		$fdd_pro_fav_data  =  $this->M_fooddesk->get_productStatus( $this->company_id );
		$final_fdd_pro_fav = array( );
		if( !empty( $fdd_pro_fav_data ) ){
			$final_fdd_pro_fav = json_decode( $fdd_pro_fav_data[ 'fdd_pro_id' ] );
		}
		$data[ 'fdd_pro_fav' ] = $final_fdd_pro_fav;
		$this->load->model('Msubcategories');

		if($this->session->userdata('menu_type') == 'fooddesk_light'){
			$data['company'] = $company = $this->Mcompany->get_company();
			$company = $company[0];
			$ac_type_id = $company->ac_type_id;
			$data['company_account_price'] = $company = $this->Mcompany->get_company_account_price($ac_type_id);
			$account_type = $this->Mpackages->get_account_types( array('id'=>$ac_type_id) );
			if(!empty($account_type) && isset($account_type[0]))
				$data['curr_account_type'] = $account_type[0];
			$data['account_types'] = $this->Mpackages->get_account_types();
		}

		// Load these things only if Ingredients system is not enabled
		if($this->company->k_assoc && $this->company->ingredient_system){

		}else{
			$this->load->model('Mgroups');
			$this->load->model('Mproduct_discount');
			$this->load->model('Mgroups_products');
			$this->load->model('Mpickup_delivery_timings');

			$data['general_settings'] = $general_settings = $this->Mgeneral_settings->get_general_settings_addedit();

			//for product availability
			$pdt = array();
			$pickup_delivery_timings=$this->Mpickup_delivery_timings->get_pickup_delivery_timings(array('company_id'=>$this->company_id));
			if(!empty($pickup_delivery_timings))
			foreach($pickup_delivery_timings as $pd)
			if($pd->pickup1 == 'CLOSED' || $pd->delivery1 == 'CLOSED')
				$pdt[] = $pd->day_id;

			$data['pickup_delivery_timings']=$pdt;

			$products_per_group=array();
			$products_per_group_wt=array();
			$products_per_group_person = array();
		}
		$data['category_data']=$this->Mcategories->get_categories_addedit();
		$data['subcategory_data']=$this->Msubcategories->get_sub_category();

		if($this->uri->segment(4)=='product_id'){ 

			$data['product_id']=$this->uri->segment(5);

			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));
			
			
			$data['product_type']=$this->Mproducts->get_product_type($data['product_information'][0]->direct_kcp_id);



			$data['check_prod_share']=$this->Mproducts->check_prod_shared_stat($this->uri->segment(5));
			if(empty($data['product_information'])){
				redirect(('cp/products/lijst'));
			}
			if( $data[ 'product_information' ][0]->parent_proid != 0 ){
				$data[ 'shared_by' ] = $this->Mproducts->get_shared_by( $data[ 'product_information' ][0]->parent_proid );
			}
			// Fetching Subcat info
			$product_cat_id=$data['product_information'][0]->categories_id;
			$data['subcategory_data']=$this->Msubcategories->get_sub_category($product_cat_id);

			if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fooddesk_light'){
				$recipe_wt = $data['product_information'][0]->recipe_weight;

				if($recipe_wt != 0){
					$recipe_wt = $recipe_wt*1000;
				}else{
					$recipe_wt = 100;
				}

				$data['producers']=$this->M_fooddesk->get_supplier_name();
				$data['suppliers']=$this->M_fooddesk->get_real_supplier_name_addedit();
				
				$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));
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
							//$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
							$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
						}
					}

					$data['nutri_values'] = $nutri_values;

					$fibers = ($nutri_values['fibers'] != 0)?". vezels ".defined_money_format($nutri_values['fibers'],1)."g":"";
					//$data['nutri_values_dist'] = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. waarvan - suikers ".defined_money_format($nutri_values['sugar'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
					$data['nutri_values_dist'] = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
				}

				if(!empty($data['product_information'])){
					if($data['product_information'][0]->direct_kcp_id != 0){
						$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
						if(!empty($fixed_pdf)){
							$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
						}
					}
					elseif($data['product_information'][0]->direct_kcp == 1 && $data['product_information'][0]->parent_proid == 0){
						$this->db->select('fdd_pro_id');
						$fixed_comp_pro = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$data['product_information'][0]->id,'is_obs_product'=>0))->result_array();
						if(!empty($fixed_comp_pro)){
							$fixed_pdf = $this->M_fooddesk->fixed_pdf($fixed_comp_pro[0]['fdd_pro_id']);
							if(!empty($fixed_pdf)){
								$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
							}
						}
					}
				}

				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);

				// ----------------------------------------------
				$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
				
				$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
				$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
				
				if(!empty($ing_array))
				{
					$pro_ing =
					array(
							'id' => 1,
							'p_id' => $data['product_information'][0]->changed_fixed_product_id,
							'i_id' => 0,
							'ing_name_dch' =>  $ing_pro_name[0]['p_name']
					);

					$pro_ing = (object) $pro_ing;
					$pro_ing1[] = $pro_ing;
					$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
				}

				$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

				$data['fdd_credits'] = $this->Mproducts->fdd_credits();

				//ingredients, allergence, traces, nutrition values to copy
				
				$this->fdb->select('all_id,all_name_dch');
				$aller_arr = $this->fdb->get('allergence')->result_array();
				
				$product_ingredients_dist = $this->Mproducts->get_product_ingredients_dist_addedit($this->uri->segment(5));
				$product_ingredients_vetten_dist = $this->Mproducts->get_product_ingredients_vetten_dist($this->uri->segment(5));
				$product_additives_dist = $this->Mproducts->get_product_additives_dist($this->uri->segment(5));

				$product_allergences = $this->Mproducts->get_product_allergence_dist($this->uri->segment(5));
				$product_sub_allergences = $this->Mproducts->get_product_sub_allergence_dist($this->uri->segment(5));

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
				foreach ($product_ingredients_dist as $ingredients){

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
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words,$ingredients->prefix).'('.$ingredients->prefix.')'.', ';
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
				if(!empty($product_ingredients_vetten_dist)){
					$ing_end .= "Plantaardige vetstof (";
					foreach ($product_ingredients_vetten_dist as $vetten){
						$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words);
					}
					$ing_end = rtrim(trim($ing_end),",");
					$ing_end .= ")";
				}

				if(!empty($product_additives_dist)){
					$add_name = 'add_name'.$this->lang_u;
					$additive_arr = array();
					foreach ($product_additives_dist as $add){
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
						foreach ($product_additives_dist as $add){
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

				$ing = str_replace('<b>', '', str_replace('</b>', '', $ing));
				$data['product_ingredients_dist'] = $ing;

				$all = '';
				if(!empty($product_allergences)){
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
						$all = str_replace('Melk', 'Melk (incl. lactose)', $all);
						$all = str_replace('Lactose, ', '', $all);
					}
					$all = substr($all, 0, -2);
				}

				$data['product_allergences_dist'] = $all;

				$product_traces_dist = $this->Mproducts->get_product_traces($this->uri->segment(5));
				$str_arr = array();
				foreach($product_traces_dist as $trace ){
					if(!in_array($trace->kt_name, $str_arr)){
						$str_arr[] = $trace->kt_name;
					}
				}
				$str = '';
				for($t = 0; $t < count($str_arr); $t++){
					$str .= stripslashes($str_arr[$t]).', ';
				}

				$data['product_traces_dist'] = rtrim($str,', ');
			}

			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5));
			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
			$add_ing = array();
			if(!empty($product_additives)){
				$add_name = 'add_name'.$this->lang_u;
				$additive_arr = array();
				foreach ($product_additives as $add){
					if(!in_array($add->$add_name,$additive_arr)){
						$additive_arr[] = $add->$add_name;
					}
				}

				for($i = 0; $i < count($additive_arr); $i++){
					$add_ing[] = array(
							'add_id' => 0,
							'kp_id' => 0,
							'ki_id' => 0,
							'ki_name'=> $additive_arr[$i]
					);

					foreach ($product_additives as $add){
						if($add->$add_name == $additive_arr[$i]){
							$add_ing[] = array(
									'add_id' => $add->add_id,
									'kp_id' => $add->kp_id,
									'ki_id' => $add->ki_id,
									'ki_name'=> $add->ki_name
							);
						}
					}
				}
			}
			$data['product_additives'] = $add_ing;

			if($this->session->userdata('login_via') == 'mcp'){
				$data['add_ing'] = $product_additives;
			}

			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5));
			$data['product_allergences']=$this->Mproducts->get_product_allergence_addedit($this->uri->segment(5));
			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5));

			//Load label data
			$data['product_labeler']=$this->Mproducts->get_product_labeler($this->uri->segment(5));

			// Load seperate product add/edit view file if Ingredients system is enabled
			if($this->company->ingredient_system){

				$data['content'] = 'cp/products_addedit_ing';
			}
			else{

				$data['groups']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>0));
				$data['groups_person']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>2));
				$data['groups_wt']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>1));

				$data['product_discount']=$this->Mproduct_discount->get_product_discount($this->uri->segment(5),0);
				$data['product_discount_wt']=$this->Mproduct_discount->get_product_discount($this->uri->segment(5),1);
				$data['product_discount_person']=$this->Mproduct_discount->get_product_discount($this->uri->segment(5),2);

				//--this is load the groups for the particular product--//
				$data['groups_products']=$this->Mgroups_products->get_groups_products(array('products_id'=>$this->uri->segment(5),'type'=>0));
				$data['groups_products_wt']=$this->Mgroups_products->get_groups_products(array('products_id'=>$this->uri->segment(5),'type'=>1));
				$data['groups_products_person']=$this->Mgroups_products->get_groups_products(array('products_id'=>$this->uri->segment(5),'type'=>2));

				foreach($data['groups_products'] as $groups){
					$products_per_group[$groups->groups_id][]=array('attribute_name'=>$groups->attribute_name,'attribute_value'=>$groups->attribute_value,'multiselect'=>$groups->multiselect,'required'=>$groups->required);
				}
				$data['products_per_group']=$products_per_group;

				foreach($data['groups_products_person'] as $groups){
					$products_per_group_person[$groups->groups_id][]=array('attribute_name'=>$groups->attribute_name,'attribute_value'=>$groups->attribute_value,'multiselect'=>$groups->multiselect,'required'=>$groups->required);
				}
				$data['products_per_group_person']=$products_per_group_person;

				foreach($data['groups_products_wt'] as $groups){
					$products_per_group_wt[$groups->groups_id][]=array('attribute_name'=>$groups->attribute_name,'attribute_value'=>$groups->attribute_value,'multiselect'=>$groups->multiselect,'required'=>$groups->required);
				}
				$data['products_per_group_wt']=$products_per_group_wt;
				//-------------------------------------------------------//

				//--this is to check whether to show payment option--//

				// Fetching Advanced Payment
				$this->load->model('Morder_settings');
				$adv_payment = 0;
				$adv_payment = $this->Morder_settings->get_order_settings( array(), 'adv_payment' );
				if(!empty($adv_payment))
					$adv_payment = $adv_payment[0]->adv_payment;

				// Fetching Cargate payment enable or not
				$this->load->model('Mpayment');
				$cardgate_payment = 0;
				$cardgate_payment = $this->Mpayment->get_cardgate_setting( array('company_id' => $this->company_id), 'cardgate_payment' );
				if(!empty($cardgate_payment))
					$cardgate_payment = $cardgate_payment[0]->cardgate_payment;
				if($this->company_role == 'master' && !$adv_payment && ($general_settings['0']->online_payment == 1 || $cardgate_payment == 1)) {
					$data['show_payment_setting'] = 'true';

				}elseif($this->company_role == 'super'){

					$params = array('parent_id' => $this->company_id,'role' => 'sub' );
					$payment_option = $this->Mcompany->payment_option($params);
					if($payment_option){
						$data['show_payment_setting'] = 'true';
					}else{
						$data['show_payment_setting'] = 'false';
					}
				}

				$rel_prod_arr = explode('#', $data['product_information'][0]->related_products);

				if(!empty($rel_prod_arr)){
					foreach ($rel_prod_arr as $rel){
						$this->db->select('id,proname');
						$rel_result = $this->db->get_where('products',array('id'=>$rel))->row();
						if(!empty($rel_result)){
							$rel_prod[] = $rel_result;
						}
					}
				}
				$data['rel_prod'] = (isset($rel_prod))?$rel_prod:array();
				//print_r($data['rel_prod']);die;
				//-------------------------------------------------------//
				//echo $this->session->userdata('menu_type');
				if ($this->session->userdata('menu_type') == 'fooddesk_light'){
					$data['count_left'] = 5 - $this->company->recipe_calculate;
					$data['content'] = 'cp/products_addedit_light';
				}
				else {
					$data['content'] = 'cp/products_addedit';
				}
			}
			$data['admin_mail'] = $this->company->email;
			$this->load->view('cp/cp_view',$data);
		}
		else if($this->uri->segment(4)=='add'){
			$this->session->set_userdata('action', 'category_json');

			
			$this->fdb->select('all_id,all_name_dch');
			$data['allergence'] = $aller_arr = $this->fdb->get('allergence')->result_array();
			

			$data['product_information']=array();

			if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
				$data['producers']=$this->M_fooddesk->get_supplier_name();
				$data['suppliers']=$this->M_fooddesk->get_real_supplier_name_addedit();
				
			}

			//this subcategories are corressponding to the category posted by the form product_add in products.php//
			if($this->input->post('categories_id'))
				$data['subcategory_data'] = $this->Msubcategories->get_sub_category($this->input->post('categories_id'));
			else
				$data['subcategory_data'] = array();

			// Load seperate product add/edit view file if Ingredients system is enabled
			if($this->company->ingredient_system){
				$data['content'] = 'cp/products_addedit_ing';
			}else{
				$data['groups']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>0));
				$data['groups_wt']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>1));
				$data['groups_person']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>2));
				$data['groups_products']=NULL;//this variable is used for the updation purpose so it has to set null//
				$data['groups_products_wt']=NULL;
				$data['groups_products_person']=NULL;

				//--this is to check whether to show payment option--//

				if($this->company_role == 'master' && $general_settings['0']->online_payment == 1){
					$data['show_payment_setting'] = 'true';
					//$data['company_role'] = 'master';
				}elseif($this->company_role == 'super'){
					//$data['company_role'] = 'super';
					$params = array('parent_id' => $this->company_id,'role' => 'sub' );
					$payment_option = $this->Mcompany->payment_option($params);
					if($payment_option){
						$data['show_payment_setting'] = 'true';
					}else{
						$data['show_payment_setting'] = 'false';
					}
				}
				//-------------------------------------------------------//

				if ($this->session->userdata('menu_type') == 'fooddesk_light'){
					$data['content'] = 'cp/products_addedit_light';
				}
				else {
					$data['content'] = 'cp/products_addedit';
				}
			}

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();

			$data['admin_mail'] = $this->company->email;

			$this->load->view('cp/cp_view',$data);
		}

		if($this->input->post('add_update') == 'update'){
			if($this->company->ingredient_system){
				$show = $this->Mproducts->update_product_ing($_COOKIE['locale']);
			}else{
				$show = $this->Mproducts->update_product($_COOKIE['locale']);
				$this->Mgroups_products->add_update_groups_products();
			}

			$this->session->set_userdata('action', 'category_json');

			if($this->input->post('semi_products') == 1){
				redirect(('cp/products/semi_products'));
			}
			else if($this->input->post('semi_products') == 2){
				redirect(('cp/products/semi_products_extra'));
			}
			redirect(('cp/products/lijst/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}

		if($this->input->post('add_update') == 'add'){
			if($this->company->ingredient_system){
				$show = $this->Mproducts->add_product_ing($_COOKIE['locale']);
			}else{
				$show = $this->Mproducts->add_product($_COOKIE['locale']);
			}

			$this->session->set_userdata('action', 'category_json');

			if($this->input->post('semi_products') == 1){
				redirect(('cp/products/semi_products'));
			}
			elseif ($this->input->post('semi_products') == 2){
				redirect(('cp/products/semi_products_extra'));
			}
			redirect(('cp/products/lijst/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}

		if($this->input->post('ajax_add_update') == 'update'){
			if($this->input->post('action') == 'product_info'){
				$show = $this->Mproducts->update_product_info();
			}
			if($this->input->post('action') == 'recipe'){
				$comp_id 		= $this->session->userdata( 'cp_user_id' );
				$company_ac 	= $this->session->userdata( 'company' );
				$ac_type_id = $company_ac[0]->ac_type_id;
				$prod_id 		= $this->input->post( 'prod_id' );
				$this->db->select( 'categories_id,subcategories_id' );
				$cat_sub_id = $this->db->get_where( 'products' ,array( 'id' => $prod_id ) )->row_array( );
				
				if( !empty( $cat_sub_id ) ){
					$categories_id 		= $cat_sub_id[ 'categories_id' ];
					$subcategories_id 	= $cat_sub_id[ 'subcategories_id' ];
					$this->get_category_prod_status( $comp_id , $ac_type_id , $categories_id , $subcategories_id );
				}
				$show = $this->Mproducts->update_recipe($_COOKIE['locale']);
			}
			if($this->input->post('action') == 'labeler'){
				$show = $this->Mproducts->update_labeler();
			}
			if($this->input->post('action') == 'webshop'){
				$show = $this->Mproducts->update_webshop();

				if($this->input->post('action_val') == _('Save & next')){
					$result = $this->Mproducts->get_next_product($show);
					if(!empty($result)){
						$show = $result->id;
					}
					else{
						$this->session->set_flashdata('webshop','webshop_success');
					}
				}
				else{
					$this->session->set_flashdata('webshop','webshop_success');
				}

				$this->session->set_userdata('action', 'category_json');

				redirect('cp/products/products_addedit/product_id/'.$show);
			}

			if($this->input->post('action') == 'allergence'){
				$show = $this->Mproducts->update_product_allergence();
			}

			if($this->input->post('action_val') == _('Save & next')){
				$result = $this->Mproducts->get_next_product($show);

				if(!empty($result)){
					$response = array('id'=>$result->id,'is_next'=>'true');
				}
				else{
					$response = array('id'=>$show,'is_next'=>'false');
				}
			}
			else{
				$response = array('id'=>$show,'is_next'=>'false');
			}

			echo json_encode($response);die;
			//redirect(('cp/products/lijst/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}

		if($this->input->post('ajax_add_update') == 'add'){
			if($this->input->post('action') == 'product_info'){
				$show = $this->Mproducts->add_product_info();
			}
			if($this->input->post('action') == 'recipe'){
				$show = $this->Mproducts->add_recipe($_COOKIE['locale']);
			}
			if($this->input->post('action') == 'labeler'){
				$show = $this->Mproducts->add_labeler();
			}
			if($this->input->post('action') == 'webshop'){
				$show = $this->Mproducts->add_webshop();

				if($this->input->post('action_val') == _('Save & next')){
					$result = $this->Mproducts->get_next_product($show);
					if(!empty($result)){
						$show = $result->id;
					}
					else{
						$this->session->set_flashdata('webshop','webshop_success');
					}
				}
				else{
					$this->session->set_flashdata('webshop','webshop_success');
				}

				$this->session->set_userdata('action', 'category_json');

				redirect('cp/products/products_addedit/product_id/'.$show);
			}

			if($this->input->post('action') == 'allergence'){
				$show = $this->Mproducts->add_product_allergence();
			}

			if($this->input->post('action_val') == _('Save & next')){
				$result = $this->Mproducts->get_next_product($show);

				if(!empty($result)){
					$response = array('id'=>$result->id,'is_next'=>'true');
				}
				else{
					$response = array('id'=>$show,'is_next'=>'false');
				}
			}
			else{
				$response = array('id'=>$show,'is_next'=>'false');
			}

			echo json_encode($response);die;
			//redirect(('cp/products/lijst/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}
	}
	function semi_products(){

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			if($this->input->post('save'))
			{
				$ids = $this->input->post('ids');

				if( is_array($ids) && !empty($ids) )
				{
					foreach($ids as $key => $product_id)
					{
						$update_data = array();
						$update_data['proname'] = addslashes($this->input->post('proname_'.$product_id));
						$update_data['prodescription'] = addslashes($this->input->post('prodescription_'.$product_id));

						$update_data['pro_display'] = ($key + 1);
						$this->Mproducts->update_product_details($product_id,$update_data);
					}

					$this->messages->add(_('Products updated successfully.'), 'success');
					$this->session->set_userdata('action', 'category_json');
				}
			}

			$data['products']= $this->Mproducts->get_semi_products();
 			$data['links']=NULL;

			$data['total_products'] = 0;

			$current_url=$this->uri->uri_string();
			$url_variable=$this->uri->uri_to_assoc(4);

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();

			$data['content'] = 'cp/semi_products';
			$data['page_id']=1;
			$company = $this->Mcompany->get_company();
			$data['show_recipe']= $company[0]->show_recipe;

			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			// restricted
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}

	function semi_products_extra(){
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		$fdd_pro_fav_data  =  $this->M_fooddesk->get_productStatus( $this->company_id );
		$final_fdd_pro_fav = array( );
		if( !empty( $fdd_pro_fav_data ) ){
			$final_fdd_pro_fav = json_decode( $fdd_pro_fav_data[ 'fdd_pro_id' ] );
		}
		$data[ 'fdd_pro_fav' ] = $final_fdd_pro_fav;
		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			if($this->input->post('save'))
			{
				$ids = $this->input->post('ids');

				if( is_array($ids) && !empty($ids) )
				{
					foreach($ids as $key => $product_id)
					{
						$update_data = array();
						$update_data['proname'] = addslashes($this->input->post('proname_'.$product_id));
						$update_data['prodescription'] = addslashes($this->input->post('prodescription_'.$product_id));

						$update_data['pro_display'] = ($key + 1);
						$this->Mproducts->update_product_details($product_id,$update_data);
					}

					$this->messages->add(_('Products updated successfully.'), 'success');
				}

			}

			$data['products']= $this->Mproducts->get_semi_products_extra();
			$data['links']=NULL;

			$data['total_products'] = 0;

			$current_url=$this->uri->uri_string();
			$url_variable=$this->uri->uri_to_assoc(4);

			$data['page_id']=2;

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();
			$company = $this->Mcompany->get_company();
			$data['show_recipe']= $company[0]->show_recipe;
			$data['content'] = 'cp/semi_products';
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			// restricted
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}

	function semi_product_addedit(){
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found' || ($this->company->ac_type_id != 4 && $this->company->ac_type_id != 5 && $this->company->ac_type_id != 6) ){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		$fdd_pro_fav_data  =  $this->M_fooddesk->get_productStatus( $this->company_id );
		$final_fdd_pro_fav = array( );
		if( !empty( $fdd_pro_fav_data ) ){
			$final_fdd_pro_fav = json_decode( $fdd_pro_fav_data[ 'fdd_pro_id' ] );
		}
		$data[ 'fdd_pro_fav' ] = $final_fdd_pro_fav;
		if($this->uri->segment(4)=='product_id'){
			$data['product_id']=$this->uri->segment(5);
			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));


			$recipe_wt = $data['product_information'][0]->recipe_weight;

			if($recipe_wt != 0){
				$recipe_wt = $recipe_wt*1000;
			}else{
				$recipe_wt = 100;
			}

			$data['producers']=$this->M_fooddesk->get_supplier_name();
			$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
			
			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));

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
					}
				}

				$data['nutri_values'] = $nutri_values;

				if(!empty($data['product_information']) && $data['product_information'][0]->direct_kcp_id != 0){

					$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
					if(!empty($fixed_pdf)){
						$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
					}
				}
				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
			}

			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5),$this->company->k_assoc);

			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
			$add_ing = array();
			if(!empty($product_additives)){
				$add_name = 'add_name'.$this->lang_u;
				$additive_arr = array();
				foreach ($product_additives as $add){
					if(!in_array($add->$add_name,$additive_arr)){
						$additive_arr[] = $add->$add_name;
					}
				}
				$add_ing = array();
				for($i = 0; $i < count($additive_arr); $i++){
					$add_ing[] = array(
							'add_id' => 0,
							'kp_id' => 0,
							'ki_id' => 0,
							'ki_name'=> $additive_arr[$i]
					);

					foreach ($product_additives as $add){
						if($add->$add_name == $additive_arr[$i]){
							$add_ing[] = array(
									'add_id' => $add->add_id,
									'kp_id' => $add->kp_id,
									'ki_id' => $add->ki_id,
									'ki_name'=> $add->ki_name
							);
						}
					}
				}
			}
			$data['product_additives'] = $add_ing;

			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5),$this->company->k_assoc);
			$data['product_allergences']=$this->Mproducts->get_product_allergence($this->uri->segment(5),$this->company->k_assoc);
			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5),$this->company->k_assoc);
			$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
			
			$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
			$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
			
			if(!empty($ing_array)){
				$pro_ing =
				array(
						'id' => 1,
						'p_id' => $data['product_information'][0]->changed_fixed_product_id,
						'i_id' => 0,
						'ing_name_dch' =>  $ing_pro_name[0]['p_name']
				);

				$pro_ing = (object) $pro_ing;
				$pro_ing1[] = $pro_ing;
				$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
			}

			$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

		}

		else if($this->uri->segment(4)=='add'){
			$data['page_id']=$this->uri->segment(5);
			$data['product_information']=array();
		}
		$data['content'] = 'cp/semi_products_addedit';
		$data['fdd_credits'] = $this->Mproducts->fdd_credits();
		$this->load->view('cp/cp_view',$data);
	}

	function semi_product_recipe($ingre_id = 0,$type = 0){
		if($ingre_id != 0){
			$data['semi_products'] = $this->M_fooddesk->get_ingre_product($ingre_id,$type);

			$this->db->select('ki_name');
			$result = $this->db->get_where('products_ingredients',array('ki_id'=>$ingre_id))->result_array();
			$data['ingre_name'] = $result[0]['ki_name'];
		}

		$data['content'] = 'cp/product_recipe';
		$this->load->view('cp/cp_view', $data);
	}
	function semi_product_addedit_new(){

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found' || ($this->company->ac_type_id != 4 && $this->company->ac_type_id != 5 && $this->company->ac_type_id != 6) ){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		$fdd_pro_fav_data  =  $this->M_fooddesk->get_productStatus( $this->company_id );
		$final_fdd_pro_fav = array( );
		if( !empty( $fdd_pro_fav_data ) ){
			$final_fdd_pro_fav = json_decode( $fdd_pro_fav_data[ 'fdd_pro_id' ] );
		}
		$data[ 'fdd_pro_fav' ] = $final_fdd_pro_fav;
		if($this->uri->segment(4)=='product_id'){
			$data['product_id']=$this->uri->segment(5);
			$data['page_id']=$this->uri->segment(6);
			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));

			$recipe_wt = $data['product_information'][0]->recipe_weight;

			if($recipe_wt != 0){
				$recipe_wt = $recipe_wt*1000;
			}else{
				$recipe_wt = 100;
			}

			$data['producers']=$this->M_fooddesk->get_supplier_name();
			$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
			
			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));

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
					}
				}

				$data['nutri_values'] = $nutri_values;

				if(!empty($data['product_information']) && $data['product_information'][0]->direct_kcp_id != 0){

					$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
					if(!empty($fixed_pdf)){
						$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
					}
				}
				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
			}

			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5),$this->company->k_assoc);
			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
			if(!empty($product_additives)){
				$add_name = 'add_name'.$this->lang_u;
				$additive_arr = array();
				foreach ($product_additives as $add){
					if(!in_array($add->$add_name,$additive_arr)){
						$additive_arr[] = $add->$add_name;
					}
				}
				$add_ing = array();
				for($i = 0; $i < count($additive_arr); $i++){
					$add_ing[] = array(
							'add_id' => 0,
							'kp_id' => 0,
							'ki_id' => 0,
							'ki_name'=> $additive_arr[$i]
					);

					foreach ($product_additives as $add){
						if($add->$add_name == $additive_arr[$i]){
							$add_ing[] = array(
									'add_id' => $add->add_id,
									'kp_id' => $add->kp_id,
									'ki_id' => $add->ki_id,
									'ki_name'=> $add->ki_name
							);
						}
					}
				}
				$data['product_additives'] = $add_ing;
			}



			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5),$this->company->k_assoc);
			$data['product_allergences']=$this->Mproducts->get_product_allergence($this->uri->segment(5),$this->company->k_assoc);
			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5),$this->company->k_assoc);

			$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
			
			$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
			$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
			
			if(!empty($ing_array)){
				$pro_ing =
				array(
						'id' => 1,
						'p_id' => $data['product_information'][0]->changed_fixed_product_id,
						'i_id' => 0,
						'ing_name_dch' =>  $ing_pro_name[0]['p_name']
				);

				$pro_ing = (object) $pro_ing;
				$pro_ing1[] = $pro_ing;
				$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
			}

			$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

		}
		else if($this->uri->segment(4)=='add'){
			$data['page_id']=$this->uri->segment(5);
			$data['product_information']=array();
		}

		$data['content'] = 'cp/semi_products_addedit_new';
		$data['fdd_credits'] = $this->Mproducts->fdd_credits();
		$this->load->view('cp/cp_view',$data);

	}
	function empty_ingredients($pro_id = 0, $view = 0){

		$products_processing 	= array();
		$products_not_pending 	= array();

		$this->load->model('Morders');
		if($this->input->post('upload') && $this->input->post('pro_id') && is_numeric($this->input->post('pro_id')) ){

			$config['upload_path'] = dirname(__FILE__).'/../../../assets/cp/fdd_pdf/';
			$config['allowed_types'] = 'pdf';
			$config['file_name'] = clean_pdf(pathinfo($_FILES['sheet']['name'], PATHINFO_FILENAME)).strtotime('now');
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('sheet'))
			{
				$data['sheet_error'] = array('error' => $this->upload->display_errors());
			}
			else
			{
				$uploaded_data = $this->upload->data();

				// Uploading column for this product, have done it here as we havent call the model mproducts instead used morders
				$product = $this->input->post('pro_id');

				$this->db->where('product_id',$product);
				$this->db->where('company_id',$this->company_id);
				$this->db->update('products_pending',array('prosheet_pws' => date("Y-m-d H:i:s").'##'.$uploaded_data['file_name']));

				$this->db->select('refused');
				$is_refused = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$product))->row_array();

				if(!empty($is_refused)){
					if($is_refused['refused'] == 1){
						$this->db->where('obs_pro_id',$product);
						$this->db->update('contacted_via_mail',array('status'=> date('Y-m-d H:i:s').'#new_sheet','refused' => 0));
					}
				}
				$this->db->select("proname");
				$proname = $this->db->get_where("products",array("id"=>$product))->row_array();
				// Sending mail to Tamara with attachment
				//$message = _('A new product sheet has been sent by:').'<br/>'._('Company ID: ').'<b>'.$this->company->id."</b><br/>"._('Company Name: ').'<b>'.$this->company->company_name."<b>";
				$message = "Een nieuw productfiche werd verzonden door:<br/>Winkel ID:&nbsp;<b>".$this->company->id."</b><br/>Bedrijfsnaam:&nbsp;<b>".$this->company->company_name."</b><br/><br/>Opgegeven productnaam in CP:&nbsp;&nbsp;<b>".$proname['proname']."</b>";
				send_email($this->config->item('fdd_checker_email'), $this->config->item('no_reply_email'), _("New Product Sheet from Admin"), $message, $this->config->item('site_admin_name'), dirname(__FILE__).'/../../../assets/cp/fdd_pdf/'.$uploaded_data['file_name']);
			}
		}
		// --------------------------------
		$data[ 'products_processing' ] = array();
		if($pro_id == 0 || $pro_id == -1){
			$data['products'] = $this->Morders->get_empty_ingredient_products($this->company->id);
		}else{
			$data['products'] = $this->Morders->get_empty_ingredient_products($this->company->id,$pro_id);
		}
		
		$data[ 'gs1_products' ] = $this->Morders->get_gs1_products_recipe( $this->company->id );
		
		
		$data['fdd_products'] = $this->M_fooddesk->product_name('p_name_dch,p_s_id,p_id,s_name');
		
		// if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){

		$data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
		// }
		$remark_recipe = $this->Mproducts->get_pro_recipe( $this->company->id );

		if (!empty($data['products']))
		{
			$products = $data['products'];
			foreach ( $products as $key => $value )
			{
				$products[$key]->recipes = $remark_recipe[$products[$key]->id];
			}
		}
		$data['products'] = $products;

		if (!empty($data['products']))
		{
			$pws_prod = $data['products'];

			foreach ($pws_prod as $pws_key => $pws_val){
				$this->db->select('obs_pro_id');
				$pend_prod = $this->db->get_where('pws_products_sheets',array('obs_pro_id' => $pws_val->id,'checked' => '0'))->result_array();
				if (!empty($pend_prod))
				{
					$pws_prod[$pws_key]->exist_in_pending = 1;
					array_push( $products_processing, $pws_val );
				}
				else {
					$pws_prod[$pws_key]->exist_in_pending = 0;
					array_push( $products_not_pending, $pws_val );
				}
			}
			$data[ 'products' ] 			= $products_not_pending;
			$data[ 'products_processing' ] 	= $products_processing;
		}
		$data['content'] = 'cp/empty_ingredients_products1';
		if( $view == 1 ){
			$data['products'] = array_values( array_merge( $products_not_pending, $products_processing ) );
			return $data['products'];
  		}
  		else{
			$this->load->view('cp/cp_view', $data);
  		}
	}

	function pws_excel(){
		$products = $this->empty_ingredients(0,1);
		$datestamp = date("d-m-Y");
		$filename = "Pws-products-".$datestamp.".xls";
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle( _('Pws Excel') );

		$counter = 1;
		$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('PRODUCT ID') )->getStyle('A'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('PRODUCTNAAM') )->getStyle('B'.$counter)->getFont()->setBold(true);
		foreach ($products as $key => $value) {
			$counter++;
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, $value->id );
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, stripslashes($value->proname) );
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');



	}

	function send_remark_by_mail(){

		$sender		= $this->input->post('sender');
		$subject	= $this->input->post('subject');
		$msg		= $this->input->post('message');
		$prod_id	= $this->input->post('prod_id');
		$proname	= ucfirst( $this->input->post('proname') );
		if($sender && $subject && $msg){

			$privateKey = 'o!s$_7e(g5xx_p(thsj*$y&27qxgfl(ifzn!a63_25*-s)*sv8';
			$url = base_url().'cp/products/products_addedit/product_id/'.$prod_id;
			$userId = $this->company_id;

			$url_token = $this->createUrl($privateKey, $url, $userId);

			$msg .= "<br/>----------------------";
			$msg .= "<br/><strong>"._('Company Name')."</strong>:&nbsp;".$this->company->company_name;

			if($prod_id)
				$msg .= "<br/><br/><strong>"._('Product Name')."</strong>:&nbsp;".$proname;
				$msg .= "<br/><strong>"._('Product Link')."</strong>:&nbsp;<a href=\"".$url_token."\">".base_url()."cp/products/products_addedit/product_id/".$prod_id."</a>";
			$msg = nl2br($msg);
			send_email($this->config->item('site_admin_email'), $sender ,$subject,$msg,NULL,NULL,NULL,'company','site_admin','remark_by_admin');
			echo 'sent';
		}
		else
			echo false;
	}
	private function createUrl($privateKey, $url, $userId){
		$hash = $this->createToken($privateKey, $url, $userId);
		$autoLoginUrl = http_build_query(array(
        		'name' => $userId,
        		'token' => $hash
    	));
    return $url.'?'.$autoLoginUrl;
	}
	private function createToken($privateKey, $url, $userId){
		return hash('sha256', $privateKey.$url.$userId);
	}

	function get_semi_product_name($page_id = 0){
		$this->db->select("products.id,products.proname");
		if ($page_id){
			$semi_name = $this->db->get_where("products",array("company_id" => $this->company_id,"semi_product"=>2))->result_array();
		}
		else {
			$semi_name = $this->db->get_where("products",array("company_id" => $this->company_id,"direct_kcp" => 1,"semi_product"=>1))->result_array();
		}
		$seminame_array = array();
		if(!empty($semi_name)){
			foreach($semi_name as $val){
				$arr['label'] = stripslashes($val['proname']);
				$arr['value'] = $val['id'];
				$seminame_array[] = $arr;
			}
		}
		echo json_encode($seminame_array);
	}

	function producer_consumer_box($pro_id = 0 , $product_refused = 0){
		if($pro_id != 0){
			$data = array();

			$data['producers_list'] = $this->M_fooddesk->get_supplier_name('s_id,s_name');
			$data['supplier_list'] = $this->M_fooddesk->get_real_supplier_name('rs_id,rs_name');

			
			$this->db->select('proname,fdd_producer_id,fdd_supplier_id,fdd_prod_art_num,fdd_supp_art_num');
			$data['filtered_list'] = $this->db->get_where('products',array('id'=>$pro_id))->row_array();
			$data['pro_id'] = $pro_id;
			$data['product_refused'] = $product_refused;
			$this->load->view('cp/empty_ingredients_art_view', $data);
		}
	}


	function prod_supp_art_no(){
		$change_pws = 0;
		$this->load->model('Morders');
		$this->load->model('Mproducts');
		$pro_id = $this->input->post('obs_pro_id');
		$old_producer_id = $this->input->post('old_producer_id');
		$producer_id = $this->input->post('producer_id');
		$fdd_prod_art_nbr = $this->input->post('fdd_prod_art_nbr');
		$old_fdd_prod_art_nbr = $this->input->post('old_fdd_prod_art_nbr');

		$old_supplier_id = $this->input->post('old_supplier_id');
		$supplier_id = $this->input->post('supplier_id');
		$fdd_supp_art_nbr = $this->input->post('fdd_supp_art_nbr');
		$old_fdd_sup_art_nbr = $this->input->post('old_fdd_sup_art_nbr');


		if ($old_producer_id != $producer_id)
		{
			$this->Mproducts->pws_clone($pro_id);
			$change_pws = 1;
		}

		if ($old_supplier_id != $supplier_id)
		{
			$this->Mproducts->pws_clone($pro_id);
			$change_pws = 1;
		}

		if ($fdd_prod_art_nbr != $old_fdd_prod_art_nbr)
		{
			$change_pws = 1;
		}

		if ($fdd_supp_art_nbr != $old_fdd_sup_art_nbr)
		{
			$change_pws = 1;
		}

		$status = 'error';
		if($pro_id != ''){
			$prod_supp=array(
					'pro_id'=>$pro_id,
					'producer_id'=>$producer_id,
					'fdd_prod_art_nbr'=>$fdd_prod_art_nbr,
					'supplier_id'=>$supplier_id,
					'fdd_supp_art_nbr'=>$fdd_supp_art_nbr,
					'proupdated'=> date('Y-m-d')
			);
			$status = $this->Mproducts->insert_fdd_prod_supp_art_nbr($prod_supp);
		}
		echo json_encode(array('status'=>$status,'change_pws'=>$change_pws));
	}

	private function product_validate($userId){
		$userdata = $this->db->get_where( 'company', array('id'=> $userId))->row_array();
		$userName = $userdata['username'];
		$params = array('company_id'=>$userdata['id']);
		$general_settings = $this->Mgeneral_settings->get_general_settings($params);

		if($general_settings['0']->language_id == 1){
			setcookie('locale','en_US',time()+365*24*60*60,'/');
		}elseif($general_settings['0']->language_id == 2){
			setcookie('locale','nl_NL',time()+365*24*60*60,'/');
		}else{
			setcookie('locale','fr_FR',time()+365*24*60*60,'/');
		}

		$_SESSION['cp_username'] = $userName;
		$res_sforum = $this->db->get_where('sforum_users',array('username'=>$userName))->result_array();

		if(is_array($res_sforum) && !empty($res_sforum)){
			// For forum login merging..
			$_SESSION['forum_user_logged_in'] = true;
			$_SESSION['sforum_logged_in'] = true;
			$_SESSION['sforum_user_id'] = $res_sforum[0]['id'];
			$_SESSION['sforum_user_role'] = $res_sforum[0]['role'];
			$_SESSION['sforum_user_username'] = $userName;
		}
		else{
			$param_sforum = array('username' => $userName, 'role' => 'user');
			$this->db->insert('sforum_users',$param_sforum);
			$sfroum_id = $this->db->insert_id();

			$_SESSION['forum_user_logged_in'] = true;
			$_SESSION['sforum_logged_in'] = true;
			$_SESSION['sforum_user_id'] = $sfroum_id;
			$_SESSION['sforum_user_role'] = 'user';
			$_SESSION['sforum_user_username'] = $userName;
		}

		$data = array(
				'cp_user_id' => $userdata['id'],
				'cp_username' => $userName,
				'cp_user_role' => $userdata['role'],
				'cp_user_parent_id' => $userdata['parent_id'],
				'cp_is_logged_in' => true,
				'cp_website' => '',
				'login_via' => 'mcp'
		);

		if($userdata['ac_type_id'] == 3){
			$this->load->model('MFtp_settings');
			$ftp_settings = $this->MFtp_settings->get_ftp_settings($params);
			if(!empty($ftp_settings) && isset($ftp_settings['0']->obs_shop) && $ftp_settings['0']->obs_shop != ''){
				$data['cp_website'] = $ftp_settings['0']->obs_shop;
			}
		}else{
			if($userdata['existing_order_page'] != ''){
				$data['cp_website'] = $userdata['existing_order_page'];
			}else{
				$this->load->model('Mcompany_type');
				$company_type_ids = explode("#",$userdata['type_id']);
				$company_type = $this->Mcompany_type->get_company_type(array('id' => $company_type_ids[0]));
				if(!empty($company_type)){
					$data['cp_website'] = $this->config->item('portal_url').$company_type['0']->slug.'/'.$userdata['company_slug'];
				}
			}
		}
		$this->session->set_userdata($data);
		redirect(current_url());
	}

	public function favourite_products() {
		$result = $this->Mproducts->get_favourite_products( $this->company_id );
		if( !empty( $result ) ) {
			$data[ 'products' ] = $result;
		}
		$data['content'] = 'cp/favourite_products';
		$this->load->view('cp/cp_view',$data);
	}
	public function delete_favourite_products() {
		$fdd_pro_id = $this->input->post( 'fdd_pro_id' );
		$result 	= $this->Mproducts->delete_favourite_products( $fdd_pro_id, $this->company_id );
		if( $result ) {
			echo 'success';
			exit();
		} 
	}

	/**
	 * 
	 * Function to see that all product of a category are fixed for a particular company
	 * @param int $comp_id
	 * @param int $ac_type_id 
	 * @param int $categories_id
	 * @param int $subcategories_id
	 * @return nothing
	 */
	function get_category_prod_status( $comp_id = 0 , $ac_type_id = 0 , $categories_id = 0 , $subcategories_id = 0 ){
		
	

		$prod_details = array();
		if( $subcategories_id > 0 ){
			$subcategories_id = $subcategories_id;
		}else{
			$subcategories_id = -1;
		}
		
		if($comp_id){

			if($ac_type_id==4 || $ac_type_id==5 || $ac_type_id==6 || $ac_type_id==7){

				$this->db->select('company_id,id,proname,direct_kcp');
				if( $subcategories_id > 0 ){
					$this->db->where(array('company_id'=> $comp_id, 'categories_id'=> $categories_id ,'subcategories_id'=>$subcategories_id,'status' =>1));
				}else{
					$this->db->where(array('company_id'=> $comp_id, 'categories_id'=> $categories_id ,'subcategories_id'=>$subcategories_id,'status' =>1));
				}
				
				$this->db->order_by('pro_display');
				$prod_details = $this->db->get('products')->result_array();

				if(!empty($prod_details))
				{
					foreach ($prod_details as $pro_key => $pro_value) {
						$complete = 1;
						if($pro_value['direct_kcp'] == 1){
							$this->db->where(array('obs_pro_id'=>$pro_value['id'],'is_obs_product'=>0));
							$result = $this->db->get('fdd_pro_quantity')->result_array();
							if(empty($result)){
								$complete = 0;
							}
						}
						else{
							$this->db->where(array('obs_pro_id'=>$pro_value['id']));
							$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
							if(!empty($result_custom)){
								
								foreach ($result_custom as $val){
									if($val['is_obs_product'] == 1){
										$complete = 0;
										break;
									}
									else {
										
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
						if(!$complete){
							$prod_details[$pro_key]['product_fixed'] = 'No';
						}
						else{
							$prod_details[$pro_key]['product_fixed'] = 'Yes';
						}
					}
				}
				else{
					$prod_details[0]['product_fixed'] = 'No';
				}
			}

		}


		if( !empty( $prod_details ) ){
			$product_fixed  = array_column( $prod_details, 'product_fixed' );
			if( !in_array( 'Yes' ,  $product_fixed ) ){
				if( $subcategories_id > 0 ){
					$this->db->where( array( 'id' => $subcategories_id ) );
					$this->db->update( 'subcategories', array( 'status' => '0' ) );
					$this->check_all_product_in_cat( $comp_id , $ac_type_id, $categories_id ); 
				}else{
					$this->db->where( array( 'id' => $categories_id ) );
					$this->db->update( 'categories', array( 'status' => '0' ) );
				}
			}else{
				if( $subcategories_id > 0 ){
					$this->db->where( array( 'id' => $subcategories_id ) );
					$this->db->update( 'subcategories', array( 'status' => '1' ) );
					$this->check_all_product_in_cat( $comp_id , $ac_type_id, $categories_id ); 
				}else{
					$this->db->where( array( 'id' => $categories_id ) );
					$this->db->update( 'categories', array( 'status' => '1' ) );
				}
			}
		}
	}

	/**
	 * 
	 * Function to see that all product of a category are fixed for a particular company if semi product id is greater than 0
	 * @param int $comp_id
	 * @param int $ac_type_id 
	 * @param int $categories_id
	 * @param int $subcategories_id
	 * @return nothing
	 */
	function check_all_product_in_cat( $comp_id = 0 , $ac_type_id = 0 , $categories_id = 0 ){
		$prod_details_check = array( );
		if($comp_id){
			if($ac_type_id==4 || $ac_type_id==5 || $ac_type_id==6 || $ac_type_id==7){
				$this->db->select('company_id,id,proname,direct_kcp');
				$this->db->where(array('company_id'=> $comp_id, 'categories_id'=> $categories_id ,'status' =>1));
				
				$this->db->order_by('pro_display');
				$prod_details_check = $this->db->get('products')->result_array();
				if(!empty($prod_details_check))
				{
					foreach ($prod_details_check as $pro_key => $pro_value) {
						$complete = 1;
						if($pro_value['direct_kcp'] == 1){
							$this->db->where(array('obs_pro_id'=>$pro_value['id'],'is_obs_product'=>0));
							$result = $this->db->get('fdd_pro_quantity')->result_array();
							if(empty($result)){
								$complete = 0;
							}
						}
						else{
							$this->db->where(array('obs_pro_id'=>$pro_value['id']));
							$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
							if(!empty($result_custom)){
								foreach ($result_custom as $val){
									if($val['is_obs_product'] == 1){
										$complete = 0;
										break;
									}
									else {
										
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
						if(!$complete){
							$prod_details_check[$pro_key]['product_fixed'] = 'No';
						}
						else{
							$prod_details_check[$pro_key]['product_fixed'] = 'Yes';
						}
					}
				}
				else{
					$prod_details_check[0]['product_fixed'] = 'No';
				}
			}
		}
		if( !empty( $prod_details_check ) ){
			$product_fixed_check   = array_column( $prod_details_check, 'product_fixed' );
			if( !in_array( 'Yes' ,  $product_fixed_check  ) ){
				$this->db->where( array( 'id' => $categories_id ) );
				$this->db->update( 'categories', array( 'status' => '0' ) );
			}else{
				$this->db->where( array( 'id' => $categories_id ) );
				$this->db->update( 'categories', array( 'status' => '1' ) );
			}
		}
	}

	/**
	 * Function to remove the labeler logo
	 * @access Public
	 * @param $result int 
	 * @author Abhishek Singh
	 */

	function remove_labeler_logo( ){
		$prod_id 	= $this->input->post( 'prod_id' );
		$image_name = $this->input->post( 'image_name' );

		if( $prod_id ){
			$result  = $this->Mproducts->remove_labeler_logo_img( $prod_id, $image_name  );
			if( $result ){
				echo 'success';
			}else{
				echo 'fail';
			}
		}
	}

	function mark_sent_prod_as_approved() {
		$prod_id = $this->input->post( 'prod_id' );
		$result = $this->Mproducts->mark_sent_prod_as_approved( $prod_id );
		if( $result ) {
			echo "success";
			exit();
		}
	}
	
}