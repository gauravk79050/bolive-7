<?php
class Article extends CI_Controller
{
	var $tempUrl = '';
	var $template = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('M_fooddesk');
		$this->fdb = $this->load->database('fdb',TRUE);
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

	function imp_common_art($list,$hig_row,$hig_col,$comp_id_dem,$active = 0){
		error_reporting(E_ALL);
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
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
		if (!$active) {
			echo "<pre>";print_r($worksheet_arr0);die();	
		}

		$cat_subcat_id_arr = array();

		$comp_id = $comp_id_dem;

		foreach ($worksheet_arr0 as $rows){
			$pro_name 	= trim($rows[1]);
			$cat 		= trim($rows[2]);
			$sub_cat 	= trim($rows[3]);
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
					$this->db->where(array('company_id'=> $comp_id));
					$total_cat = $this->db->get('categories')->num_rows();
					$order_display = $total_cat+1;

					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat, 'order_display'=> $order_display, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();
				}
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {

					$this->db->where(array('categories_id'=>$cat_id,'subname'=> addslashes(mb_strtolower($sub_cat,'UTF-8'))));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->where(array('categories_id'=> $cat_id));
						$total_subcat = $this->db->get('subcategories')->num_rows();
						$suborder_display = $total_subcat+1;

						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=> $suborder_display,'subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();
					}
				}
				$exist = $this->db->get_where('products',array('pro_art_num'=>$art_num,'categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id,'proname'=>addslashes(mb_strtolower($pro_name,'UTF-8'))))->result_array();
				
				$total_products = $this->db->get_where('products',array('categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id))->num_rows();
				$pro_display = $total_products+1;


				if (empty($exist)) {

					$total_products = $this->db->get_where('products',array('categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id))->num_rows();
					$pro_display = $total_products+1;

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
						'pro_display'=> $pro_display,
						'procreated'=>date('Y-m-d')
					);
					echo "<pre>";
					print_r($inser_array);
					echo "<br>";
					$this->db->insert('products',$inser_array);

					if( ! in_array( $cat_id.'#'.$sub_cat_id, $cat_subcat_id_arr ) ) {
						array_push( $cat_subcat_id_arr, $cat_id.'#'.$sub_cat_id );
					}
				}
			}
			$count_rows++;
		}
		$this->update_multilingual_script($i = 0,$comp_id);

		if( in_array( $comp_id, array( '5062', '4866' ) ) ) {
			$this->update_prod_list_sorting( $cat_subcat_id_arr, $comp_id );
		}

	}

	function update_multilingual_script($i = 0,$comp_id) {
		$tap = array();
		$all_comp = $this->db->get_where('categories',array('company_id'=>$comp_id))->result_array();

		$all_comp_cat = $this->db->get_where('categories_name',array('comp_id'=>$comp_id))->result_array();

		if (sizeof($all_comp) != sizeof($all_comp_cat)) {
			$tap = array($comp_id);
		}

		if (!empty($tap)) {
			foreach ($tap as $comp_key => $comp_value) {
				$this->db->select( 'categories.id, categories.name, categories.description, categories.company_id, language.locale' );
				$this->db->where( 'categories.company_id', $comp_value );
				$this->db->join( 'general_settings', 'categories.company_id = general_settings.company_id' );
				$this->db->join( 'language', 'language.id = general_settings.language_id' );
				$query = $this->db->get( 'categories' )->result_array();

				foreach ( $query as $key => $value ) {
					$querydata = $this->db->get_where( 'categories_name',array('cat_id'=>$value['id']) )->result_array();
					if (empty($querydata)) {
						if( $value[ 'locale' ] == 'en_US' ){
							$insert_array = array( 
								'cat_id' 		=> $value[ 'id' ],
								'name'			=> $value[ 'name' ],
								'description' 	=> $value[ 'description' ],
								'comp_id'		=> $value[ 'company_id' ]
							);
						} else if( $value[ 'locale' ] == 'nl_NL' || $value[ 'locale' ] == 'nl_BE' ) {
							$insert_array = array( 
								'cat_id' 			=> $value[ 'id' ],
								'name_dch'			=> $value[ 'name' ],
								'description_dch' 	=> $value[ 'description' ],
								'comp_id'			=> $value[ 'company_id' ]
							);

						} else if( $value[ 'locale' ] == 'fr_FR' ) {
							$insert_array = array( 
								'cat_id' 			=> $value[ 'id' ],
								'name_fr'			=> $value[ 'name' ],
								'description_fr' 	=> $value[ 'description' ],
								'comp_id'			=> $value[ 'company_id' ]
							);
						}
						$this->db->insert( 'categories_name', $insert_array );
					}
				}
			}

			foreach ($tap as $comp_key => $comp_value) {
				$this->db->select( 'subcategories.id, subcategories.categories_id, subcategories.subname, subcategories.subdescription, categories.company_id, language.locale' );
				$this->db->where( 'categories.company_id', $comp_value );
				$this->db->join( 'categories', 'categories.id = subcategories.categories_id' );
				$this->db->join( 'general_settings', 'categories.company_id = general_settings.company_id' );
				$this->db->join( 'language', 'language.id = general_settings.language_id' );
				$query = $this->db->get( 'subcategories' )->result_array();
				foreach ( $query as $key => $value ) {
					$querysub = $this->db->get_where( 'subcategories_name',array('categ_id'=>$value['categories_id'],'subcat_id'=>$value[ 'id' ]) )->result_array();
					if (empty($querysub)) {
						if( $value[ 'locale' ] == 'en_US' ){
							$insert_array = array( 
								'subcat_id' 	=> $value[ 'id' ],
								'categ_id'			=> $value[ 'categories_id' ],
								'subname'		=> $value[ 'subname' ],
								'subdescription' => $value[ 'subdescription' ],
							);
						} else if( $value[ 'locale' ] == 'nl_NL' || $value[ 'locale' ] == 'nl_BE' ) {
							$insert_array = array( 
								'subcat_id' 		=> $value[ 'id' ],
								'categ_id'			=> $value[ 'categories_id' ],
								'subname_dch'		=> $value[ 'subname' ],
								'subdescription_dch' => $value[ 'subdescription' ],
							);

						} else if( $value[ 'locale' ] == 'fr_FR' ) {
							$insert_array = array( 
								'subcat_id' 		=> $value[ 'id' ],
								'categ_id'			=> $value[ 'categories_id' ],
								'subname_fr'		=> $value[ 'subname' ],
								'subdescription_fr' => $value[ 'subdescription' ]
							);
						}
						$this->db->insert( 'subcategories_name', $insert_array );
					}
				}
			}

			foreach ($tap as $comp_key => $comp_value) {
				$this->db->select( 'products.id, products.proname, products.prodescription, language.locale' );
				$this->db->join( 'general_settings', 'products.company_id = general_settings.company_id' );
				$this->db->join( 'language', 'language.id = general_settings.language_id' );
				$query = $this->db->get_where( 'products',array('products.company_id' => $comp_value) )->result_array();

				foreach ( $query as $key => $value ) {
					$queryprod = $this->db->get_where( 'products_name',array('product_id' => $value[ 'id' ]) )->result_array();

					if (empty($queryprod)) {
						if( $value[ 'locale' ] == 'en_US' ){
							$insert_array = array( 
								'product_id' 		=> $value[ 'id' ],
								'proname'			=> $value[ 'proname' ],
								'prodescription' 	=> $value[ 'prodescription' ]
							);
						} else if( $value[ 'locale' ] == 'nl_NL' || $value[ 'locale' ] == 'nl_BE' ) {
							$insert_array = array( 
								'product_id' 		=> $value[ 'id' ],
								'proname_dch'		=> $value[ 'proname' ],
								'prodescription_dch' => $value[ 'prodescription' ]
							);

						} else if( $value[ 'locale' ] == 'fr_FR' ) {
							$insert_array = array( 
								'product_id' 		=> $value[ 'id' ],
								'proname_fr'		=> $value[ 'proname' ],
								'prodescription_fr' => $value[ 'prodescription' ]
							);
						}
						$this->db->insert( 'products_name', $insert_array );
					}
				}
			}
		} 
	}

	function imp_common_art1($list,$hig_row,$hig_col,$comp_id_dem,$active = 0){
		error_reporting(E_ALL);
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
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
		if (!$active) {
			echo "<pre>";print_r($worksheet_arr0);die();	
		}

		$comp_id = $comp_id_dem;

		foreach ($worksheet_arr0 as $rows){

			// $pro_name 	= trim($rows[3]);
			// $cat 		= trim($rows[1]);
			// $sub_cat 	= trim($rows[2]);
			// $cat_id 	= 0;
			// $option 	= 'per_unit';
			// $price_per_unit = trim($rows[4]);
			// $art_num =  trim($rows[0]);
			// $type 		= trim($rows[5]);
			// if ($price_per_unit == '') {
			// 	$price_per_unit = 0;
			// }
			// $price_per_unit = str_replace("€", '', $price_per_unit );
			// $price_weight	= 0;
			// $price_per_person = 0;
			// if(strlen($pro_name) > 0){
			// 	if($type == 'KG'){
			// 		$option = 'weight_wise';
			// 		$price_weight = (trim($rows[4]) != '')?trim($rows[4]):0;
			// 		$price_weight = str_replace(",",".",$price_weight);
			// 		$price_weight = str_replace("€", '', $price_weight );
			// 		$price_per_unit = 0;
			// 	} else if ($type == 'PC') {
			// 		$option = 'per_person';
			// 		$price_per_person = (trim($rows[4]) != '')?trim($rows[4]):0;
			// 		$price_per_person = str_replace(",",".",$price_per_person);
			// 		$price_per_person = str_replace("€", '', $price_per_person );
			// 		$price_per_unit = 0;
			// 		$price_weight = 0;
			// 	}
			// 	$this->db->where(array('company_id'=> $comp_id, 'name'=> $cat));
			// 	$res = $this->db->get('categories')->result_array();
			// 	if(!empty($res)){
			// 		$cat_id = $res[0]['id'];
			// 	}
			// 	if($sub_cat == ''){
			// 		$sub_cat_id = -1;
			// 	}
			// 	if ($cat_id) {
			// 		$exist = $this->db->get_where('products',array('categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id,'proname'=>addslashes(mb_strtolower($pro_name,'UTF-8'))))->result_array();
			// 		if (!empty($exist)) {
			// 			if ($exist[0]['pro_art_num'] != '' && ($exist[0]['pro_art_num'] != $art_num) ) {
			// 				echo $pro_name;
			// 				echo "<br>";
			// 					// $upd_array = array(
			// 					// 	'pro_art_num'=>$art_num
			// 					// );
			// 					//$this->db->where(array('id'=>$exist[0]['id'],'company_id'=>$comp_id,'categories_id'=>$cat_id,'subcategories_id'=> $sub_cat_id));
			// 					//$this->db->update('products',$upd_array);
			// 			}
			// 		}
			// 	}
			// }
			// $count_rows++;

			$pro_name =  trim($rows[0]);
			$art_num =  trim($rows[1]);
			$exist = $this->db->get_where('products',array('proname'=>$pro_name,'company_id'=> 4114 ))->row_array();
			if (!empty($exist)) {
				$this->db->where(array('id'=>$exist['id']));
				$this->db->update('products',array('pro_art_num'=>$art_num));
			}

		}
	}

	function imp_common_art2($list,$hig_row,$hig_col,$comp_id_dem,$active = 0){
		
		error_reporting(E_ALL);
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
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
		if (!$active) {
			echo "<pre>";print_r($worksheet_arr0);die();	
		}

		$user_id = $comp_id_dem;
		$exi_usr = array();
		$counter = 1;

		foreach ($worksheet_arr0 as $rows){

			if($counter == 2){
				$counter = 1;
				$pws_name = $pws_name." ".$rows[0];
				

				$pro_name = addslashes(mb_strtolower(trim($pws_name),'UTF-8'));
				$real_supplier_name = trim($supplier_name);

				$art_no_s = trim($supplier_art_num);
				$art_no_s = (($art_no_s != '') && ($art_no_s != '000'))?$art_no_s:'';

				$real_supplier_id = 0;
				if($real_supplier_name != ''){
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

				if($art_no_s != '')
					$this->db->where('fdd_supp_art_num',$art_no_s);

				$this->db->where(array('company_id'=>0,'LOWER(proname)'=>$pro_name,'fdd_supplier_id'=>$real_supplier_id));
				$products = $this->db->get('products')->result_array();


				if(!empty($products)){
					$id_pro = $products[0]['id'];
					// $insert_array = array(
					// 		'company_id'=>0,
					// 		'categories_id'=> 0,
					// 		'subcategories_id'=>0,
					// 		'proname'=> $pro_name,
					// 		'procreated'=>date('Y-m-d'),
					// 		'direct_kcp'=>1,
					// 		'status'=>'0',
					// 		'fdd_supplier_id'=>$real_supplier_id,
					// 		'fdd_supp_art_num'=>$art_no_s
					// );

					// $this->db->insert('products',$insert_array);
					// $insert_pro_id = $this->db->insert_id();
					

					$this->db->insert('products_pending',array('product_id'=>$id_pro,'company_id'=> $user_id,'date'=>date('Y-m-d H:i:s')));

					// $this->db->insert('contacted_via_mail',array('obs_pro_id'=>$insert_pro_id));
					$exi_usr[] = $id_pro;
				}
			}else{
				$pws_name = $rows[0];
				$supplier_name = $rows[3];
				$supplier_art_num = $rows[2];
				++$counter;
			}
		}


		$this->db->select( 'obs_pro_id' );
		$this->db->where( 'company_id', $user_id );
		$pro_ids = $this->db->get( 'fdd_pro_fav' )->row_array();
		if( !empty( $pro_ids ) ) {
			$favorite_ids = json_decode( $pro_ids[ 'obs_pro_id' ] );
			if (!empty($favorite_ids)) {
				$own_arr = array_values(array_unique( array_merge($exi_usr,$favorite_ids) ));
			}
		}

		$this->db->where( 'company_id', $user_id );
		$this->db->update( 'fdd_pro_fav', array( 'obs_pro_id' => json_encode( $own_arr ), 'date_added' => date( 'Y-m-d H:i:s' ) ) );
	}


	function imp_common_art3($list,$hig_row,$hig_col,$comp_id_dem,$active = 0){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
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
		if (!$active) {
			echo "<pre>";print_r($worksheet_arr0);die();	
		}

		$user_id = $comp_id_dem;
		$exi_usr = array();
		$counter = 1;
		$fav_list = '';
		foreach ($worksheet_arr0 as $rows){
			$supplier_art_num 	= $rows[2];
			$supplier_name 		= $rows[1];
			$pws_name 			= $rows[0];
			$ean_code 			= trim($rows[3]);
			$producer_name 		= trim($rows[5]);
			$producer_art_num 	= $rows[4];

			$pro_name = addslashes(mb_strtolower(trim($pws_name),'UTF-8'));
			$real_supplier_name = trim($supplier_name);

			$art_no_s = trim($supplier_art_num);
			$art_no_s = (($art_no_s != '') && ($art_no_s != '000'))?$art_no_s:'';

			$art_no_p = trim($producer_art_num);
			$art_no_p = (($art_no_p != '') && ($art_no_p != '000'))?$art_no_p:'';

			$real_supplier_id = 0;
			$producer_id = 0;

			if($real_supplier_name != ''){
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

			if($producer_name != ''){
				$res1 = $this->M_fooddesk->get_suppliers_data(array('LOWER(s_name)' => addslashes(mb_strtolower($producer_name,'UTF-8'))));
				if(!empty($res1)){
					$producer_id = $res1[0]['s_id'];
				}else{
					$insrt_array = array(
						's_name'=>addslashes($producer_name),
						's_username' => str_replace(' ', '_', $producer_name),
						's_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
						's_date_added' => date('Y-m-d h:i:s')
					);

					$this->fdb->insert('suppliers', $insrt_array);
					$producer_id = $this->fdb->insert_id();
				}
			}

			$this->db->select('id');

			if($art_no_s != '')
				$this->db->where('fdd_supp_art_num',$art_no_s);

			$this->db->where(array('company_id'=>0,'LOWER(proname)'=>$pro_name,'fdd_supplier_id'=>$real_supplier_id));
			$products = $this->db->get('products')->result_array();


			if(!empty($products)){
				$id_pro = $products[0]['id'];

				$this->db->where(array('company_id'=>$user_id,'product_id'=>$id_pro));
				$exist_pending = $this->db->get('products_pending')->result_array();

				if( empty( $exist_pending ) ) {
					$this->db->insert('products_pending',array('product_id'=>$id_pro,'company_id'=> $user_id,'date'=>date('Y-m-d H:i:s')));
				}

				$exi_usr[] = $id_pro;

				if ($user_id == '4640') {
					$this->db->where('company_id', $user_id);
					$this->db->where('proname', $real_supplier_name);
					$recipe = $this->db->get('products')->row_array();

					if (! empty($recipe)) {
						$this->db->where('obs_pro_id', $recipe['id']);
						$this->db->where('fdd_pro_id', $id_pro);
						$exist_fdd = $this->db->get('fdd_pro_quantity')->row_array();

						if (empty($exist_fdd)) {
							$fdd_insrt_arr = array(
								'fdd_pro_id' => $id_pro,
								'obs_pro_id' => $recipe['id'],
								'comp_id' => $user_id,
								'is_obs_product' => 1,
								'quantity' => 1,
								'real_supp_id' => $real_supplier_id,
								'unit' => 'g',
								'created_dated' => date( "Y-m-d H:i:s" )
							);

							$this->db->insert('fdd_pro_quantity', $fdd_insrt_arr);
						}
					}
				}
				if($fav_list != ''){
					$insrt_array = array(
			          'fdd_pro_id'   => $id_pro,
			          'real_supp_id' => $real_supplier_id,
			          'sel_favlist'  => $fav_list,
			          'company_id'   => $user_id,
			          'fav_type'     => 'PWS'
			        );
			        $this->db->insert('selected_fav_list', $insrt_array);
				}
			}else{
				$insert_array = array(
					'company_id'=>0,
					'categories_id'=> 0,
					'subcategories_id'=>0,
					'proname'=> $pro_name,
					'procreated'=>date('Y-m-d'),
					'direct_kcp'=>1,
					'status'=>'0',
					'ean_code' => $ean_code,
					'fdd_supplier_id'=>$real_supplier_id,
					'fdd_supp_art_num'=>$art_no_s,
					'fdd_producer_id'=>$producer_id,
					'fdd_prod_art_num'=>$art_no_p,
				);

				$this->db->insert('products',$insert_array);
				$insert_pro_id = $this->db->insert_id();

				$this->db->insert('products_pending',array('product_id'=>$insert_pro_id,'company_id'=> $user_id,'date'=>date('Y-m-d H:i:s')));

				$this->db->insert('contacted_via_mail',array('obs_pro_id'=>$insert_pro_id));
				$exi_usr[] = $insert_pro_id;

				if ($user_id == '4640') {
					$this->db->where('company_id', $user_id);
					$this->db->where('proname', $real_supplier_name);
					$recipe = $this->db->get('products')->row_array();

					if (! empty($recipe)) {
						$fdd_insrt_arr = array(
							'fdd_pro_id' => $insert_pro_id,
							'obs_pro_id' => $recipe['id'],
							'comp_id' => $user_id,
							'is_obs_product' => 1,
							'quantity' => 1,
							'real_supp_id' => $real_supplier_id,
							'unit' => 'g',
							'created_dated' => date( "Y-m-d H:i:s" )
						);

						$this->db->insert('fdd_pro_quantity', $fdd_insrt_arr);
					}
				}
				if($fav_list != ''){
					$insrt_array = array(
			          'fdd_pro_id'   => $insert_pro_id,
			          'real_supp_id' => $real_supplier_id,
			          'sel_favlist'  => $fav_list,
			          'company_id'   => $user_id,
			          'fav_type'     => 'PWS'
			        );
			        $this->db->insert('selected_fav_list', $insrt_array);
			    }
			}
		}

		$this->db->select( 'obs_pro_id' );
		$this->db->where( 'company_id', $user_id );
		$pro_ids = $this->db->get( 'fdd_pro_fav' )->row_array();
		if( !empty( $pro_ids ) ) {
			$favorite_ids = json_decode( $pro_ids[ 'obs_pro_id' ], true );
			if (!empty($favorite_ids)) {
				$own_arr = array_values(array_unique( array_merge($exi_usr,$favorite_ids) ));
			} else {
				$own_arr = $exi_usr;
			}
			$this->db->where( 'company_id', $user_id );
			$this->db->update( 'fdd_pro_fav', array( 'obs_pro_id' => json_encode( $own_arr, true ), 'date_added' => date( 'Y-m-d H:i:s' ) ) );
		}else{
			$this->db->insert( 'fdd_pro_fav', array( 'obs_pro_id' => json_encode( $exi_usr, true ), 'date_added' => date( 'Y-m-d H:i:s' ), 'company_id' => $user_id  ) );
		}
	}

	function delete_import_art_3($status = 0){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		ini_set('memory_limit', '512M');
		$this->db->like('date', '2019-06-22');
		$this->db->where(array('company_id' => '4946'));
		$products = $this->db->get('products_pending')->result_array();
		$prod_ids = array_column($products, 'product_id');
		if(!$status){
			echo "<pre>";
			print_r ($prod_ids);
			echo "</pre>";
		}
		$pro_id =array_chunk($prod_ids, 500);
		foreach ($pro_id as $key => $prod_id) {
			$this->db->where_in('id', $prod_id);
			$this->db->delete('products');
			$this->db->where_in('obs_pro_id', $prod_id);
			$this->db->delete('contacted_via_mail');
			$this->db->where_in('product_id', $prod_id);
			$this->db->delete('products_pending');
		}
	}

	function imp_art_all_languages($list,$hig_row,$hig_col,$comp_id_dem,$active = 0) {
		error_reporting(E_ALL);
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
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
		if (!$active) {
			echo "<pre>";print_r($worksheet_arr0);die();	
		}

		$comp_id = $comp_id_dem;

		$this->db->select( 'language_id' );
		$this->db->where( 'company_id', $comp_id );
		$lang = $this->db->get( 'general_settings' )->row_array();

		foreach ($worksheet_arr0 as $rows){
			$pro_name_dch 	= trim($rows[3]);
			$pro_name_fr 	= trim($rows[6]);
			$pro_name_en 	= trim($rows[9]);
			$cat_dch 		= trim($rows[1]);
			$cat_fr 		= trim($rows[4]);
			$cat_en 		= trim($rows[7]);
			$sub_cat_dch 	= trim($rows[2]);
			$sub_cat_fr 	= trim($rows[5]);
			$sub_cat_en 	= trim($rows[8]);
			$cat_id 		= 0;
			$option 		= 'per_unit';
			$price_per_unit = trim($rows[10]);
			$art_num 		= trim($rows[0]);
			$type 			= trim($rows[11]);
			if ($price_per_unit == '') {
				$price_per_unit = 0;
			}
			$price_per_unit = str_replace("€", '', $price_per_unit );
			$price_weight	= 0;
			$price_per_person = 0;

			if( $lang[ 'language_id' ] == '2' || $lang[ 'language_id' ] == '5' ){
				$cat 		= $cat_dch;
				$sub_cat 	= $sub_cat_dch;
				$pro_name 	= $pro_name_dch;
			} else if( $lang[ 'language_id' ] == '3' ) {
				$cat 		= $cat_fr;
				$sub_cat 	= $sub_cat_fr;
				$pro_name 	= $pro_name_fr;
			} else if ( $lang[ 'language_id' ] == '1' ) {
				$cat 		= $cat_en;
				$sub_cat 	= $sub_cat_en;
				$pro_name 	= $pro_name_en;
			}

			if(strlen($pro_name) > 0){
				if($type == 'KG'){
					$option = 'weight_wise';
					$price_weight = (trim($rows[10]) != '')?trim($rows[10]):0;
					$price_weight = str_replace(",",".",$price_weight);
					$price_weight = str_replace("€", '', $price_weight );
					$price_per_unit = 0;
				} else if ($type == 'PC') {
					$option = 'per_person';
					$price_per_person = (trim($rows[10]) != '')?trim($rows[10]):0;
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
					$this->db->where(array('company_id'=> $comp_id));
					$total_cat = $this->db->get('categories')->num_rows();
					$order_display = $total_cat+1;

					$this->db->insert('categories', array('company_id'=> $comp_id, 'name'=> $cat,'order_display'=> $order_display, 'created'=> date('Y-m-d')));
					$cat_id = $this->db->insert_id();

					$cat_insert_array = array( 
						'cat_id' 		=> $cat_id,
						'name_dch'		=> $cat_dch,
						'name_fr'		=> $cat_fr,
						'name'			=> $cat_en,
						'comp_id'		=> $comp_id
					);
					$this->db->insert( 'categories_name', $cat_insert_array );
				}
				if($sub_cat == ''){
					$sub_cat_id = -1;
				}
				elseif ($sub_cat != '') {
					$this->db->where(array('categories_id'=>$cat_id,'subname'=> addslashes(mb_strtolower($sub_cat,'UTF-8'))));
					$r_sub = $this->db->get('subcategories')->result_array();
					if(!empty($r_sub)){
						$sub_cat_id = $r_sub[0]['id'];
					}else{
						$this->db->where(array('categories_id'=> $cat_id));
						$total_subcat = $this->db->get('subcategories')->num_rows();
						$suborder_display = $total_subcat+1;

						$this->db->insert('subcategories', array('categories_id'=> $cat_id, 'subname'=>addslashes(mb_strtolower($sub_cat,'UTF-8')),'suborder_display'=>$suborder_display, 'subcreated'=>date('Y-m-d'),'status'=>'1'));
						$sub_cat_id = $this->db->insert_id();

						$subcat_insert_array = array( 
							'subcat_id' 		=> $sub_cat_id,
							'categ_id'			=> $cat_id,
							'subname_dch'		=> addslashes(mb_strtolower($sub_cat_dch,'UTF-8')),
							'subname_fr'		=> addslashes(mb_strtolower($sub_cat_fr,'UTF-8')),
							'subname'		=> addslashes(mb_strtolower($sub_cat_en,'UTF-8'))
						);
						$this->db->insert( 'subcategories_name', $subcat_insert_array );
					}
				}
				$exist = $this->db->get_where('products',array('pro_art_num'=>$art_num,'categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id,'proname'=>addslashes(mb_strtolower($pro_name,'UTF-8'))))->result_array();

				$total_products = $this->db->get_where('products',array('categories_id'=>$cat_id,'subcategories_id'=>$sub_cat_id))->num_rows();
				$pro_display = $total_products+1;

				if (empty($exist)) {
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
						'procreated'=>date('Y-m-d'),
						'pro_display'=> $pro_display
					);
					
					$this->db->insert('products',$inser_array);
					$prod_id = $this->db->insert_id();

					$insert_prod_array = array( 
						'product_id' 	=> $prod_id,
						'proname_dch'	=> addslashes(mb_strtolower($pro_name_dch,'UTF-8')),
						'proname_fr'	=> addslashes(mb_strtolower($pro_name_fr,'UTF-8')),
						'proname'		=> addslashes(mb_strtolower($pro_name_en,'UTF-8'))
					);
					$this->db->insert( 'products_name', $insert_prod_array );
				}
			}
			$count_rows++;
		}
	}

	function abccc() {

		$query = "SELECT *  FROM `products` WHERE  `pro_art_num` != '' AND `procreated` LIKE '%2019-10-05%' AND company_id = 0 ORDER BY `id`  DESC";

		$res = $this->db->query( $query )->result_array();

		foreach ($res as $key => $value) {
			$this->db->where( 'id', $value[ 'id' ] );
			$this->db->update( 'products', array( 'pro_art_num' => '', 'fdd_supp_art_num' => $value[ 'pro_art_num' ] ) );
		}
	}

	function update_prod_list_sorting( $cat_subcat_id_arr, $company_id ) {

		foreach ($cat_subcat_id_arr as $cat_key => $cat_value) {
			$cat_id 	= explode( "#", $cat_value )[0];
			$subcat_id 	= explode( "#", $cat_value )[1];
			
			$this->db->select('id, REPLACE(pro_art_num,"-",".") as plu');
			$this->db->where('categories_id', $cat_id );
			$this->db->where('subcategories_id', $subcat_id );
			$this->db->order_by('pro_display', 'asc' );
			$product_list = $this->db->get('products')->result_array();

			if( sizeof( $product_list ) > 1 ) {
				$plu = array_column( $product_list, 'plu');

				if( $plu[0] > $plu[1] ) {
					array_multisort( $product_list, SORT_DESC, SORT_NUMERIC, $plu, SORT_NUMERIC );
				} else {
					array_multisort( $product_list, SORT_ASC, SORT_NUMERIC, $plu, SORT_NUMERIC );
				}

				foreach ( $product_list as $key => $value ) {
					$this->db->where( 'id', $value['id'] );
					$this->db->update( 'products', array( 'pro_display'=> $key+1 ) );
				}
			}
		}
		return true;
	}
	function imp_art_name($list,$hig_row,$hig_col,$comp_id_dem,$active = 0){
		error_reporting(E_ALL);
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");
		$inputFileName = dirname(__FILE__).'/../../../assets/excels/'.$list;
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
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
		if (!$active) {
			echo "<pre>";print_r($worksheet_arr0);die();	
		}

		$comp_id = $comp_id_dem;

		foreach ($worksheet_arr0 as $rows){
			$art_num 		= trim($rows[0]);
			$proname_dch 	= trim($rows[1]);
			$proname_fr 	= trim($rows[2]);

			$this->db->select('products.id');
			$this->db->join('products_name', 'products_name.product_id = products.id');
			$exist = $this->db->get_where('products',array('company_id' => $comp_id, 'pro_art_num'=>$art_num, 'proname_dch'=>addslashes(mb_strtolower($proname_dch,'UTF-8'))))->row_array();

			if (! empty($exist)) {
				$this->db->where('product_id', $exist['id']);
				$this->db->update('products_name', array('proname_fr'=> addslashes($proname_fr)));
				$count_rows++;
			}
		}
	}
}
