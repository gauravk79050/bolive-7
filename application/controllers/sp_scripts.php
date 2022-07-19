============================================================ TO UPDATE LIVE
<!-- TO UPDATE SP THAT CONTAINS SP
function live_sp_in_sp(){
	error_reporting(-1);
	ini_set('display_errors', 1);

	$this->db->distinct();
	$this->db->select('obs_pro_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '1', 'direct_kcp' => '1' ))->result_array(),'obs_pro_id');
	// echo "<pre>";
	// print_r ($query);die;
	// echo "</pre>";

	$this->db->distinct();
	$this->db->select('semi_product_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$this->db->where_in( 'obs_pro_id', $query);
	$res = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0' ))->result_array(),'semi_product_id');
	// echo "<pre>";
	// print_r ($res);die;
	// echo "</pre>";

	$this->db->select('products.id');
	$this->db->where(array( 'semi_product'=> '1', 'direct_kcp' => '1'));
	$this->db->where_in( 'id', $res);
	$res31 = array_column($this->db->get('products')->result_array(),'id');
	echo "<pre>";
	print_r ($res31);die;
	echo "</pre>";

	if( !empty($res31) ){
		foreach ($res31 as $key => $value) {
			$this->db->distinct();
			$this->db->select('semi_product_id');
			$semis = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id !=' => '0', 'obs_pro_id' => $value ) )->result_array();
			if( !empty( $semis ) ){
				foreach ($semis as $k1 => $v1) {
					$esp = $this->db->get_where('products', array( 'id' => $v1['semi_product_id'], 'semi_product' => '2', 'direct_kcp' => '1' ))->row_array();
					if( !empty($esp) ){
						$this->db->distinct();
						$this->db->select('obs_pro_id');
						$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
						if( !empty($obs_ids) ){
							foreach ($obs_ids as $k => $v) {
								$this->db->select('direct_kcp, semi_product');
								$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
								if( !empty( $obs_type ) ){
									if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
										$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
										if( empty($is_semi) ){
											$this->db->where('id', $v['obs_pro_id']);
											$this->db->update('products', array( 'semi_product' => '0' ));
										}
									}
									elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
										$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
										if( empty($is_semi) ){
											$this->db->where('id', $v['obs_pro_id']);
											$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
										}
										else{
											$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
											$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
										}
									}
								}
							}
						}
					}
				}
			}
			else{
				$this->db->distinct();
				$this->db->select('obs_pro_id');
				$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
				if( !empty($obs_ids) ){
					foreach ($obs_ids as $k => $v) {
						$this->db->select('direct_kcp, semi_product');
						$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
						if( !empty( $obs_type ) ){
							if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
								$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
								if( empty($is_semi) ){
									$this->db->where('id', $v['obs_pro_id']);
									$this->db->update('products', array( 'semi_product' => '0' ));
								}
							}
							elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
								$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
								if( empty($is_semi) ){
									$this->db->where('id', $v['obs_pro_id']);
									$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
								}
								else{
									$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
									$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
								}
							}
						}
					}
				}
			}
		}
	}
	die("done");
}
 -->

 <!-- TO UPDATE SP THAT HAVE CUSTOM SEMI
function live_sp_in_sp(){
	error_reporting(-1);
	ini_set('display_errors', 1);

	$this->db->distinct();
	$this->db->select('obs_pro_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '1', 'direct_kcp' => '1' ))->result_array(),'obs_pro_id');
	// echo "<pre>";
	// print_r ($query);die;
	// echo "</pre>";

	$this->db->distinct();
	$this->db->select('semi_product_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$this->db->where_in( 'obs_pro_id', $query);
	$res = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0' ))->result_array(),'semi_product_id');
	// echo "<pre>";
	// print_r ($res);die;
	// echo "</pre>";

	$this->db->select('products.id');
	$this->db->where(array( 'semi_product'=> '1', 'direct_kcp' => '0'));
	$this->db->where_in( 'id', $res);
	$res31 = array_column($this->db->get('products')->result_array(),'id');
	echo "<pre>";
	print_r ($res31);die;
	echo "</pre>";

	if( !empty($res31) ){
		foreach ($res31 as $key => $value) {
			$this->db->distinct();
			$this->db->select('semi_product_id');
			$semis = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id !=' => '0', 'obs_pro_id' => $value ) )->result_array();
			if( !empty( $semis ) ){
				// foreach ($semis as $k1 => $v1) {
				// 	$esp = $this->db->get_where('products', array( 'id' => $v1['semi_product_id'], 'semi_product' => '2', 'direct_kcp' => '1' ))->row_array();
				// 	if( !empty($esp) ){
				// 		$this->db->distinct();
				// 		$this->db->select('obs_pro_id');
				// 		$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
				// 		if( !empty($obs_ids) ){
				// 			foreach ($obs_ids as $k => $v) {
				// 				$this->db->select('direct_kcp, semi_product');
				// 				$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
				// 				if( !empty( $obs_type ) ){
				// 					if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
				// 						$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
				// 						if( empty($is_semi) ){
				// 							$this->db->where('id', $v['obs_pro_id']);
				// 							$this->db->update('products', array( 'semi_product' => '0' ));
				// 						}
				// 					}
				// 					elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
				// 						$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
				// 						if( empty($is_semi) ){
				// 							$this->db->where('id', $v['obs_pro_id']);
				// 							$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
				// 						}
				// 						else{
				// 							$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
				// 							$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
				// 						}
				// 					}
				// 				}
				// 			}
				// 		}
				// 	}
				// }
			}
			else{
				$this->db->distinct();
				$this->db->select('obs_pro_id');
				$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
				if( !empty($obs_ids) ){
					foreach ($obs_ids as $k => $v) {
						$this->db->select('direct_kcp, semi_product');
						$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
						if( !empty( $obs_type ) ){
							if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
								// $is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
								// if( empty($is_semi) ){
								// 	$this->db->where('id', $v['obs_pro_id']);
								// 	$this->db->update('products', array( 'semi_product' => '0' ));
								// }
							}
							elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
								// $is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
								// if( empty($is_semi) ){
								// 	$this->db->where('id', $v['obs_pro_id']);
								// 	$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
								// }
								// else{
									$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
									$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
								// }
							}
						}
					}
				}
			}
		}
	}
	die("done");
}
  -->

  <!-- TO UPDATE SEMI_PRO_ID = 0 IN CUSTOM SEMI PRODUCTS
	$this->db->distinct();
	$this->db->select('obs_pro_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '1', 'direct_kcp' => '0' ))->result_array(),'obs_pro_id');
	echo "<pre>";
	print_r ($query);die;
	echo "</pre>";

	$this->db->where_in( 'obs_pro_id', $query );
	$this->db->where( 'semi_product_id !=', '0' );
	$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
	die('done');
   -->

   <!-- TO UPDATE ESP TO SP WHICH ARE USED IN CUSTOM PRODUCTS
function live_sp_in_sp(){
	error_reporting(-1);
	ini_set('display_errors', 1);

	$this->db->distinct();
	$this->db->select('obs_pro_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '0', 'direct_kcp' => '0' ))->result_array(),'obs_pro_id');
	// echo "<pre>";
	// print_r ($query);die;
	// echo "</pre>";

	$this->db->distinct();
	$this->db->select('semi_product_id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$this->db->group_start();
	$query = array_chunk( $query, 500);
	foreach ($query as $key => $value){
		$this->db->or_where_in( 'obs_pro_id', $value );	
	}
	$this->db->group_end();
	$res = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0' ))->result_array(),'semi_product_id');
	// echo "<pre>";
	// print_r ($res);die;
	// echo "</pre>";

	$this->db->select('products.id');
	$this->db->where(array( 'semi_product'=> '2', 'direct_kcp' => '1'));
	$this->db->group_start();
	$res = array_chunk( $res, 500);
	foreach ($res as $key1 => $value1){
		$this->db->or_where_in( 'id', $value1 );	
	}
	$this->db->group_end();
	$res31 = array_column($this->db->get('products')->result_array(),'id');
	echo "<pre>";
	print_r ($res31);die;
	echo "</pre>";

	if( !empty($res31) ){
		foreach ($res31 as $key => $value) {
			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$semis = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value, 'semi_product' => '1', 'direct_kcp' => '1' ) )->result_array();
			if( !empty( $semis ) ){
				foreach ($semis as $k1 => $v1) {
					$this->db->where('obs_pro_id', $v1['obs_pro_id']);
					$this->db->where('semi_product_id', $value);
					$this->db->update('fdd_pro_quantity', array('semi_product_id'=>'0'));
				}
			}
		}
		$this->db->where_in('id', $res31);
		$this->db->update('products', array('semi_product' => '1'));
	}
	die("done");
}
    -->
==================================================== TO UPDATE LIVE END
==================================================== TO CUT THE LINK
<!-- function add_all_semi_to_custom(){
	ini_set('memory_limit', '20000M');
	set_time_limit(0);
	ini_set('max_execution_time', 0);

	$this->db->where( array( 'semi_product !=' => '0' ) );
	$prods = $this->db->get( 'products' )->result_array();

	$arr = array();
	foreach ( $prods as $key => $value) {
		$num = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->num_rows();
		
		$this->db->distinct();
		$this->db->select('obs_pro_id');
		$query = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value['id'] ) )->result_array();

		foreach ($query as $key1 => $value1) {
			$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
			$prods_to_update = $this->db->get( 'fdd_pro_quantity' )->num_rows();

			if( $num != $prods_to_update ){
				$arr[] = array( 'semi_id' => $value['id'], 'obs_pro_id' => $value1['obs_pro_id'], 'company_id' => $value['company_id'] );
			}
		}
	}

	foreach ($arr as $key1 => $value1) {
		$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value1['semi_id'] ) );
		$this->db->update( 'fdd_pro_quantity', array( 'semi_product_id' => '0' ) );
	}
	die("done");
} -->
==================================================== TO CUT THE LINK END
==================================================== to_update_included_semi
<!-- TO UPDATE INCLUDED SEMI FOR SP
function to_update_included_semi(){
$this->db->select('id');
$esp = $this->db->get_where( 'products', array( 'semi_product' => '2') )->result_array();
$esp = array_column( $esp, 'id' );

$this->db->distinct();
$this->db->select('obs_pro_id');
$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
$this->db->where('semi_product', '1');
$this->db->where_in( 'semi_product_id', $esp );
$sp_having_esp = $this->db->get( 'fdd_pro_quantity' )->result_array();
// echo "<pre>";
// print_r ($sp_having_esp);
// echo "</pre>";

foreach ($sp_having_esp as $k1 => $v1) {
	$this->db->where('obs_pro_id', $v1['obs_pro_id']);
	$this->db->where_in('semi_product_id', $esp);
	$a = $this->db->get('fdd_pro_quantity')->result_array();

	foreach ($a as $k2 => $v2) {
		$this->db->where('obs_pro_id', $v2['obs_pro_id']);
		$this->db->where('semi_product_id', $v2['semi_product_id']);
		$this->db->update('fdd_pro_quantity', array( 'included_semi' => $v2['semi_product_id'] ));
	}
} die("done");
 -->

<!-- TO UDPATE INCLUDED SEMI OF CUSTOM PRODS
function to_update_included_semi(){
$this->db->distinct();
$this->db->select('obs_pro_id');
$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
$this->db->where( 'semi_product_id !=', '0' );
$custom = $this->db->get_where( 'fdd_pro_quantity', array( 'semi_product' => '0' ) )->result_array();
$custom = array_column( $custom, 'obs_pro_id' );
// echo "<pre>";
// print_r ($custom);die;
// echo "</pre>";

foreach ($custom as $k1 => $v1) {
	$this->db->where('obs_pro_id', $v1);
	$this->db->where( 'semi_product_id !=', '0' );
	$a = $this->db->get('fdd_pro_quantity')->result_array();

	foreach ($a as $k2 => $v2) {
		$this->db->where('obs_pro_id', $v2['semi_product_id']);
		$this->db->where( 'semi_product_id !=', '0' );
		$having_esp = $this->db->get('fdd_pro_quantity')->result_array();
		
		if( !empty($having_esp) ){
			foreach ($having_esp as $k3 => $v3) {
				$included_semi = $v2['semi_product_id'].'-'.$v3['semi_product_id'];
				$this->db->where('obs_pro_id', $v1);
				$this->db->where('semi_product_id', $v2['semi_product_id']);
				$this->db->update('fdd_pro_quantity', array( 'included_semi' => $included_semi ));
			}
		}
		else{
			$this->db->where('obs_pro_id', $v1);
			$this->db->where('semi_product_id', $v2['semi_product_id']);
			$this->db->update('fdd_pro_quantity', array( 'included_semi' => $v2['semi_product_id'] ) );
		}
	}
		} die("done");
 -->
==================================================== to_update_included_semi END
==================================================== FOR CUSTOM + SEMI
 <!-- TO UPDATE CUSTOM SEMI TO CUSTOM + SEMI
// need to be run after fxn to_update_included_semi()	
function to_update_esp_to_sp(){
	// $this->db->where( array( 'semi_product' => '2' ) );
	// $this->db->update( 'products', array( 'semi_product' => '1' ) );
	// die("done");

	$this->db->where( array( 'direct_kcp' => '0', 'semi_product' => '1' ) );
	$custom_semis = $this->db->get( 'products' )->result_array();

	foreach ($custom_semis as $key1 => $value1) {
		$clone_id = $this->add_product_clone( $value1['id'] );
		echo $key1;
		if( !empty( $clone_id ) ){
			if( isset( $clone_id[ 'id' ] ) ){
				$cloned_id = $clone_id[ 'id' ];
				// move clone to SP
				$this->db->where( array( 'id' => $cloned_id ) );
				$this->db->update( 'products', array( 'categories_id' => 0, 'subcategories_id' => 0, 'direct_kcp' => 1, 'semi_product' => 1 ) );
				
				// update semi_product_id of original product by SP's id
				$fdds = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value1['id'] ) )->result_array();
								
				$obj = array();
				if( !empty( $fdds ) ){
					foreach( $fdds as $k1 => $v1 ){
						$obj[] = array(
								'id' 				=> $v1[ 'id' ],
								'semi_product_id' 	=> $cloned_id,
								'included_semi' 	=> ( $v1[ 'included_semi' ] != '0' ) ? $cloned_id.'-'.$v1[ 'included_semi' ] : $cloned_id
							);
					}
					if( !empty( $obj ) ){
						$this->db->update_batch( 'fdd_pro_quantity', $obj, 'id' );
					}
				}
				
				// to change custom semi to custom
				$this->db->where('id', $value1['id']);
				$this->db->update('products', array( 'semi_product' => '0' ));
			}
		}
	}
	die('done');
}

function add_product_clone( $clone_of_id, $from_update_recipe = 0){
	//Fetching Information about the products of which clone is to be add
	$product_info = $this->db->get_where('products',array('id'=>$clone_of_id))->result_array();
	$fdd_pro_ids = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$clone_of_id))->result_array();

	if($product_info){
		$categories_id = $product_info['0']['categories_id'];
		$subcategories_id = $product_info['0']['subcategories_id'];
		$product_infos	= $product_info['0'];
		foreach ($product_infos as $key => $info){
			if($key != 'id')
			$add_product_data[$key]	= $info;
		}
		if( $from_update_recipe != 1 ){
			$add_product_data['proname'] = $add_product_data['proname'];
		}
		if($add_product_data['direct_kcp'] == 0){
			$add_product_data['semi_product'] = 0;
		}

		//Inserting data in products table
		$query=$this->db->insert('products', $add_product_data);
		$new_product_id=$this->db->insert_id();//method to get id of last inserted row//

		if ($new_product_id)
		{
			$this->db->where(array('id'=>$new_product_id));
			$this->db->update('products',array('procreated' => date('Y-m-d'),'proupdated'=>'0000-00-00'));

			$multi_names = $this->db->get_where( 'products_name', array( 'product_id' => $clone_of_id ) )->row_array();
			$multi_obj = array(
							'product_id' 		=> $new_product_id,
							'proname' 			=> ( $multi_names[ 'proname' ] != '' ) ? ($multi_names[ 'proname' ] ) : '',
							'proname_dch' 		=> ( $multi_names[ 'proname_dch' ] != '' ) ? ($multi_names[ 'proname_dch' ] ) : '',
							'proname_fr' 		=> ( $multi_names[ 'proname_fr' ] != '' ) ? ($multi_names[ 'proname_fr' ] ) : '',
							'prodescription' 	=> ( $multi_names[ 'prodescription' ] == NULL ) ? '' :$multi_names[ 'prodescription' ],
							'prodescription_dch'=> ( $multi_names[ 'prodescription_dch' ] == NULL ) ? '' :$multi_names[ 'prodescription_dch' ],
							'prodescription_fr' => ( $multi_names[ 'prodescription_fr' ] == NULL ) ? '' :$multi_names[ 'prodescription_fr' ]
						);
			$this->db->insert( 'products_name', $multi_obj );
		}

		if( $from_update_recipe != 1 ){
			//Copying image
			if($product_info['0']['image'] != ''){
				$extension = pathinfo($product_info['0']['image'], PATHINFO_EXTENSION);
				$imagename = clean_pdf(pathinfo($product_info['0']['image'], PATHINFO_FILENAME));
				$image_name = $new_product_id.'_'.'Clone_'.$imagename.'.'.$extension;

				$image = file_get_contents($this->config->item('image_upload_url').'cp/images/product/'.$product_info['0']['image']);
				
				file_put_contents($this->config->item('old_bolive_img_path').'cp/images/product/'.$image_name, $image);
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
		}

		/**
		 * Adding Ingredients
		 */
		$ingredients = $this->get_product_ingredients($clone_of_id,1,$fdd_pro_ids);
		if(!empty($ingredients)){
			foreach ($ingredients as $ingredient){
				$insert_array = array(
						'product_id' 		=> $new_product_id,
						'kp_id' 			=> $ingredient->kp_id,
						'ki_id' 			=> $ingredient->ki_id,
						'display_order' 	=> $ingredient->display_order,
						'kp_display_order'	=>$ingredient->kp_display_order,
						'date_added' 		=> date('Y-m-d H:i:s'),
						'is_obs_ing' 		=> $ingredient->is_obs_ing,
						'have_all_id' 		=> $ingredient->have_all_id,
						'aller_type' 		=> $ingredient->aller_type,
						'aller_type_fr' 	=> $ingredient->aller_type_fr,
						'aller_type_dch' 	=> $ingredient->aller_type_dch,
						'allergence' 		=> $ingredient->allergence,
						'allergence_fr' 	=> $ingredient->allergence_fr,
						'allergence_dch' 	=> $ingredient->allergence_dch,
						'sub_allergence' 	=> $ingredient->sub_allergence,
						'sub_allergence_fr' => $ingredient->sub_allergence_fr,
						'sub_allergence_dch'=> $ingredient->sub_allergence_dch
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
						'display_order' => $trace->display_order,
						'date_added' => date('Y-m-d H:i:s')
				);
				$this->db->insert('products_traces', $insert_array);
			}
		}

		/**
		 * Adding Allergence
		 */
		$allergence = $this->get_product_allergence_share($clone_of_id);
		if(!empty($allergence)){
			foreach ($allergence as $allg){
				$insert_array = array(
						'product_id' => $new_product_id,
						'kp_id' => $allg->kp_id,
						'ka_id' => $allg->ka_id,
						'display_order' => $allg->display_order,
						'date_added' => date('Y-m-d H:i:s')
				);
				$this->db->insert('products_allergence', $insert_array);
			}
		}
		/**
		 * Adding sub-allergence
		 */
		$sub_allergence = $this->get_product_sub_allergence_share($clone_of_id);
		if(!empty($sub_allergence)){
			foreach ($sub_allergence as $allg){
				$insert_array = array(
						'product_id' => $new_product_id,
						'kp_id' => $allg->kp_id,
						'parent_ka_id' => $allg->parent_ka_id,
						'sub_ka_id' => $allg->sub_ka_id,
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
					'is_obs_product' 	=> $fdd_quant->is_obs_product,
					'obs_pro_id' 		=> $new_product_id,
					'fdd_pro_id' 		=> $fdd_quant->fdd_pro_id,
					'quantity'			=> $fdd_quant->quantity,
					'unit'				=> $fdd_quant->unit,
					'semi_product_id'	=> $fdd_quant->semi_product_id,
					'included_semi'		=> $fdd_quant->included_semi,
					'fixed' 			=> $fdd_quant->fixed,
					'comp_id' 			=> $fdd_quant->comp_id,
					'created_dated' 	=> date("Y-m-d H:i:s")
				);
				$this->db->insert('fdd_pro_quantity', $insert_array_new);
			}
		}
		//-------------------------------------------------------------------------//
		return ( array( 'id'=>$new_product_id ) );
	}else{
		return(array('error'=>'Could not made clone. Try again'));
	}
}

function get_product_sub_allergence_share($product_id = 0, $k_type = 1){
	$allergence = array();
	if($product_id){
		$this->db->order_by('display_order', 'ASC');
		$allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
	}
	return $allergence;
}

function get_product_allergence_share($product_id = 0, $k_type = 1){
	$allergence = array();
	if($product_id){
		$this->db->order_by('display_order', 'ASC');
		$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
	}
	return $allergence;
}

function get_product_traces($product_id = 0, $k_type = 1){
	$traces = array();
	if($product_id){
		$this->db->select( 'products_traces.*' );
		$this->db->order_by('display_order', 'ASC');
		$this->db->join( 'traces', 'traces.t_id = products_traces.kt_id' );
		$traces = $this->db->get_where('products_traces', array('product_id' => $product_id))->result();
	}
	return $traces;
}

function get_product_ingredients( $product_id = 0, $k_type = 1, $fdd_pro_quant = array() ){
	$fdd_pro_quant = array_column( $fdd_pro_quant, 'fdd_pro_id' );
	$ingredients = array();
	if($product_id){
		$this->db->select( 'products_ingredients.*,ingredients.ki_name,ingredients.ki_name_dch,ingredients.ki_name_fr' );
		$this->db->join( 'ingredients', 'ingredients.ki_id = products_ingredients.ki_id' );
		$this->db->order_by( 'kp_display_order', 'ASC');
		$this->db->order_by( 'display_order', 'ASC');
		$ingredients = $this->db->get_where('products_ingredients', array('products_ingredients.product_id' => $product_id, 'products_ingredients.ki_id !=' => 0 ))->result();
	}

	$same_arr = array();
	$new_ingredients = array();

	if( !empty( $fdd_pro_quant ) ){
		foreach ($fdd_pro_quant as $key => $value) {
			foreach( $ingredients as $key1 => $value1 ){
				if( $value == $value1->kp_id ){
					$search_string = $value1->product_id.'-'.$value1->kp_id.'-'.$value1->display_order;
					if( !in_array( $search_string, $same_arr ) ){
						$new_ingredients[] = $value1;
						$same_arr[] = $search_string;
					}
				}
			}
		}
	}

	return $new_ingredients;
}
  -->
==================================================== FOR CUSTOM + SEMI END
==================================================== CHANGE CUSTOM IN CUSTOM
<!-- 
function to_update_CustomSemiId_to_SemiId_in_semi_product_id(){
	$this->db->distinct();
	$this->db->select('products.id');
	$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
	$this->db->where('semi_product', '0');
	$this->db->where('semi_product_id !=', '0');
	$customs_having_custom = $this->db->get('fdd_pro_quantity')->result_array();

	foreach ($customs_having_custom as $key => $value) {
		echo $key;
		$this->db->distinct();
		$this->db->select('semi_product_id');
		$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
		$this->db->where('obs_pro_id', $value['id']);
		$this->db->where('semi_product_id !=', '0');
		$res = $this->db->get('fdd_pro_quantity')->result_array();
		
		foreach ($res as $key1 => $value1) {
			$this->db->select('id');
			$this->db->where('id', $value1['semi_product_id']);
			$this->db->where('semi_product', '0');
			$is_custom = $this->db->get('products')->row_array();
			
			if( !empty( $is_custom ) ){
				$this->db->select('semi_product_id, included_semi');
				$this->db->where('obs_pro_id', $is_custom['id']);
				$this->db->where('semi_product_id !=', '0');
				$new_semi_id = $this->db->get('fdd_pro_quantity')->row_array();

				if( !empty($new_semi_id) ){
					$this->db->where('obs_pro_id', $value['id']);
					$this->db->where('semi_product_id', $value1['semi_product_id']);
					$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => $new_semi_id['semi_product_id'], 'included_semi' => $new_semi_id['included_semi'] ));
				}
			}
		}
	}
	die("done");
	}
 -->
 ==================================================== CHANGE CUSTOM IN CUSTOM END
 ==================================================== TO LIST ALL CLIENTS EMAIL, PHONE WHO HAVE ESP
 <!-- function list_of_clients(){
	$this->db->distinct();
	$this->db->select( 'products.id, proname, company_id' );
	$this->db->join( 'company', 'company.id = products.company_id' );
	$esp = $this->db->get_where('products', array( 'semi_product' => '2' ) )->result_array();
	$esp = array_values(array_unique( array_column( $esp, 'company_id' ) ));

	foreach ($esp as $key => $value) {
		$this->db->select('email, phone');
		$mail = $this->db->get_where( 'company', array( 'id' => $value ) )->row_array();
		if( $mail['email'] == '' ){
			echo 'NA'.'<br>';
		}
		else{
			echo $mail['email'].'<br>';	
		}
	}
	die("kp");
} -->
 ==================================================== TO LIST ALL CLIENTS EMAIL, PHONE WHO HAVE ESP END
 ==================================================== LIST ALL CLIENTS WHO HAVE ESP WITH SAME NAME SP
<!--  function list_of_clients(){
	$this->db->distinct();
	$this->db->select( 'id, proname, company_id' );
	$esp = $this->db->get_where('products', array( 'semi_product' => '2' ) )->result_array();

	foreach ($esp as $key => $value) {
		$this->db->select('products.id, proname, company_name, company_id, email, phone');
		$this->db->join( 'company', 'company.id = products.company_id' );
		$is_same = $this->db->get_where('products', array( 'proname' => $value[ 'proname' ], 'company_id' => $value['company_id'], 'semi_product' => '1' ) )->row_array();

		if( !empty( $is_same ) ){
			echo $is_same['proname'].'<br>';
		}
	}
	die("kp");
} -->
 ==================================================== LIST ALL CLIENTS WHO HAVE ESP WITH SAME NAME SP END
 ==================================================== LIST ALL CLIENTS CUSTOM SEMI NAME SAME AS SP/ESP
<!--  function list_of_clients(){
	$this->db->distinct();
	$this->db->select( 'id, proname, company_id' );
	$esp = $this->db->get_where('products', array( 'semi_product' => '1', 'direct_kcp' => '0' ) )->result_array();

	foreach ($esp as $key => $value) {
		$this->db->select('products.id, proname, company_name, company_id, email, phone');
		$this->db->join( 'company', 'company.id = products.company_id' );
		$this->db->where_in( 'semi_product', array( '1', '2' ) );
		$is_same = $this->db->get_where('products', array( 'proname' => $value[ 'proname' ], 'company_id' => $value['company_id'], 'direct_kcp' => '1' ) )->row_array();

		if( !empty( $is_same ) ){
			echo $is_same['proname'].'<br>';
		}
	}
	die("kp");
 }-->
 ==================================================== LIST ALL CLIENTS CUSTOM SEMI NAME SAME AS SP/ESP END
 ==================================================== to get all products where semi product needs to be re-added
 <!-- function add_all_semi_to_custom(){
	ini_set('memory_limit', '20000M');
	set_time_limit(0);
	ini_set('max_execution_time', 0);

	$this->db->where( array( 'semi_product !=' => '0' ) );
	$prods = $this->db->get( 'products' )->result_array();

	$arr = array();
	foreach ( $prods as $key => $value) {
		$num = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->num_rows();
		
		$this->db->distinct();
		$this->db->select('obs_pro_id');
		$query = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value['id'] ) )->result_array();

		foreach ($query as $key1 => $value1) {
			$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
			$prods_to_update = $this->db->get( 'fdd_pro_quantity' )->num_rows();

			if( $num != $prods_to_update ){
				$arr[] = array( 'semi_id' => $value['id'], 'obs_pro_id' => $value1['obs_pro_id'], 'company_id' => $value['company_id'] );
			}
		}
	}
	echo "<pre>";
	print_r ($arr);die;
	echo "</pre>";
	echo "<pre>";
	print_r (array_values(array_unique(array_column($arr,'semi_id'))));
	echo "</pre>";
	die;
} -->
 ==================================================== to get all products where semi product needs to be re-added END
 ==================================================== LIST ALL CLIENTS THAT HAVE SP INGREDIENT MISSING 
 <!-- function list_of_clients(){
	ini_set('memory_limit', '20000M');
	set_time_limit(0);
	ini_set('max_execution_time', 0);

	$this->db->order_by('company_id', 'asc');
	$this->db->where( array( 'semi_product !=' => '0' ) );
	$prods = $this->db->get( 'products' )->result_array();

	$arr = array();
	foreach ( $prods as $key => $value) {
		$num = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->num_rows();
		
		$this->db->distinct();
		$this->db->select('obs_pro_id');
		$query = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value['id'] ) )->result_array();
		
		foreach ($query as $key1 => $value1) {
			$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
			$prods_to_update = $this->db->get( 'fdd_pro_quantity' )->num_rows();

			if( $num != $prods_to_update ){
				$arr[] = array( 'semi_id' => $value['id'], 'obs_pro_id' => $value1['obs_pro_id'], 'company_id' => $value['company_id'] );
			}
		}
	}

	foreach ($arr as $key => $value) {
		$this->db->select('products.id, proname, company_name, company_id, email, phone');
		$this->db->join( 'company', 'company.id = products.company_id' );
		$is_same = $this->db->get_where('products', array( 'company_id' => $value['company_id'], 'products.id' => $value['obs_pro_id'] ) )->row_array();

		if( !empty( $is_same ) ){
			echo $is_same['phone'].'<br>';
		}
		else{
			echo 'NA'.$value['obs_pro_id'].'--'.$value['company_id'].'<br>';
		}
	}
	die("kp");
} -->
 ==================================================== LIST ALL CLIENTS THAT HAVE SP INGREDIENT MISSING END