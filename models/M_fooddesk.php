<?php
/**
 *
 * @author Aniket Singh <aniketsingh@cecdcoss.com>, Abhay Hayaran <abhayhayaran@cedcoss.com>
 *
 */
class M_fooddesk extends CI_Model{

	function __construct(){
		parent::__construct();
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function get_products( $select = '', $param = array(), $start = null, $limit = null, $count = false){
		if($select != ''){
			$this->fdb->select($select);
		}

		if(!empty($param)){
			if(isset($param['letter'])){
				$this->fdb->like('p_name',$param['letter'],'after');
				unset($param['letter']);
			}
			$this->fdb->where($param);
		}

		$this->fdb->join('suppliers', 'products.p_s_id = suppliers.s_id');

		if($count){
			$products = $this->fdb->count_all_results('products');
		}else{
			if($start != null && $limit != null)
				$this->fdb->limit($limit, $start);

			$products = $this->fdb->get('products')->result();

			if(!empty($products)){
				foreach($products as $key => $product){
					if( $product->approval_status == '1' ){
						$products[$key]->ingredients = $this->getIngredients(array('p_id' => $product->p_id));
						$products[$key]->ingredients_vetten = $this->getIngredientsVetten(array('p_id' => $product->p_id));
						$products[$key]->additives = $this->getAdditives(array('p_id' => $product->p_id));
						$products[$key]->allergence = $this->getAllergence(array('p_id' => $product->p_id,'by_ingredient'=>0));
						$products[$key]->sub_allergence = $this->getsubAllergence(array('p_id' => $product->p_id));
						$products[$key]->traces = $this->getTraces(array('p_id' => $product->p_id));
					}
				}
			}
		}
		return $products;
	}


	/**
	 *This function is used to insert notification in contacted via mail
	 *@name get_notification
	 *@author MOnu Singh Yadav <monuyadav@cedcoss.com>
	*/
	function get_notification($pro_id=0)
	{
		$this->db->where('obs_pro_id',$pro_id);
		$this->db->update('contacted_via_mail',array('notification'=>1));
	}


	/**
	 * This model function is used to fetch ingredients
	 * @param array $param Array of columns and values to be used in where clause
	 */
	function getIngredients($param = array(), $select = ''){
		$ingredients = array();

		$this->fdb->order_by("order", "asc");
		if($select != ''){
			$this->fdb->select($select);
		}
		else
		{
			$this->fdb->select('prod_ingredients.* , ingredients.ing_id as ing_id, ingredients.ing_name as ing_name, ingredients.ing_name_dch as ing_name_dch, ingredients.ing_name_fr as ing_name_fr, ingredients.have_all_id as all_id');
		}

		if(!empty($param))
			$this->fdb->where($param);

		$this->fdb->join('ingredients','prod_ingredients.i_id = ingredients.ing_id');

		$ingredients = $this->fdb->get('prod_ingredients')->result();


		return $ingredients;
	}

	function getIngredientsVetten($param = array(), $select = ''){
		$ingredients = array();

		$this->fdb->order_by("order", "asc");
		if($select != ''){
			$this->fdb->select($select);
		}
		else{
			$this->fdb->select('prod_ingredients_vetten.* , ingredients.ing_id as ing_id, ingredients.ing_name as ing_name, ingredients.ing_name_dch as ing_name_dch, ingredients.ing_name_fr as ing_name_fr, ingredients.have_all_id as all_id');
		}

		if(!empty($param))
			$this->fdb->where($param);

		$this->fdb->join('ingredients','prod_ingredients_vetten.i_id = ingredients.ing_id');

		$ingredients = $this->fdb->get('prod_ingredients_vetten')->result();

		return $ingredients;
	}

	function getAdditives($param = array(), $select = ''){
		$ingredients = array();

		$this->fdb->order_by("order", "asc");
		if($select != ''){
			$this->fdb->select($select);
		}
		else{
			$this->fdb->select('prod_additives.* , ingredients.ing_id as ing_id, ingredients.ing_name as ing_name, ingredients.ing_name_dch as ing_name_dch, ingredients.ing_name_fr as ing_name_fr, additives.add_name_dch,additives.add_name_fr,additives.add_name, ingredients.have_all_id as all_id');
		}

		if(!empty($param))
			$this->fdb->where($param);

		$this->fdb->join('ingredients','prod_additives.i_id = ingredients.ing_id');
		$this->fdb->join('additives','prod_additives.add_id = additives.add_id');

		$ingredients = $this->fdb->get('prod_additives')->result();

		return $ingredients;
	}

	/**
	 * This model function is used to fetch traces
	 * @param array $param Array of columns and values to be used in where clause
	 */
	function getTraces($param = array(), $select = ''){
		$traces = array();
		$this->fdb->order_by("order", "asc");
		if($select != ''){
			$this->fdb->select($select);
		}
		else
		{
			$this->fdb->select('prod_traces.* , traces.t_id as t_id, traces.t_name as t_name, traces.t_name_dch as t_name_dch, traces.t_name_fr as t_name_fr');
		}

		if($select != ''){
			$this->fdb->select($select);
		}

		if(!empty($param))
			$this->fdb->where($param);

		$this->fdb->join('traces','prod_traces.t_id = traces.t_id');

		$traces = $this->fdb->get('prod_traces')->result();

		return $traces;
	}

	/**
	 * This model function is used to fetch Allergence
	 * @param array $param Array of columns and values to be used in where clause
	 */
	function getAllergence($param = array(), $select = ''){
		$allergence = array();
		$this->fdb->order_by("order", "asc");
		if($select != ''){
			$this->fdb->select($select);
		}
		else
		{
			$this->db->select('prod_allergence.* , allergence.all_id as all_id, allergence.all_name as all_name, allergence.all_name_dch as all_name_dch, allergence.all_name_fr as all_name_fr');
		}
		if(!empty($param))
			$this->fdb->where($param);

		$this->fdb->join('allergence','prod_allergence.a_id = allergence.all_id');

		$allergence = $this->fdb->get('prod_allergence')->result();

		return $allergence;
	}

	function getsubAllergence($param = array(), $select = ''){
		$allergence = array();
		$this->fdb->order_by("order", "asc");
		if($select != ''){
			$this->fdb->select($select);
		}
		else{
			$this->fdb->select('prod_sub_allergence.* , sub_allergence.all_id as all_id, sub_allergence.all_name as all_name, sub_allergence.all_name_dch as all_name_dch, sub_allergence.all_name_fr as all_name_fr, sub_allergence.parent_all_id');
		}

		if(!empty($param))
			$this->fdb->where($param);

		$this->fdb->join('sub_allergence','prod_sub_allergence.a_id = sub_allergence.all_id');
		$allergence = $this->fdb->get('prod_sub_allergence')->result();

		return $allergence;
	}

	/**
	 * Function to get all Suppliers
	 * @param string $select Comma seperated string of column names to be selected
	 * @param array $where_array Array of columns and values to be used in where clause
	 * @param mixed $limit Its the limit value in case of limited members to be fetched
	 */
	function get_suppliers_data($where_array = array(), $select = '',$limit = ''){

		if($select != '')
			$this->fdb->select($select);
		if ($limit != '')
		{
			$this->fdb->limit($limit);
		}
		if(!empty($where_array)){
			$this->fdb->where($where_array);
		}
		
		$this->fdb->order_by('s_name','asc');
		$data = $this->fdb->get('suppliers')->result_array();
		if(empty($data) && !empty($where_array) && array_key_exists('LOWER(s_name)', $where_array)){
			$where = "`merged_names` REGEXP '##".$where_array['LOWER(s_name)']."$' OR `merged_names` REGEXP '^".$where_array['LOWER(s_name)']."##' OR `merged_names` REGEXP '##".$where_array['LOWER(s_name)']."##'";
			$this->fdb->select( 's_id' );
			$this->fdb->where( $where );
			$this->fdb->or_where( 'merged_names', $where_array['LOWER(s_name)'] );
			return $this->fdb->get( 'suppliers' )->result_array();
		}else{
			return $data;
		}

	}

	/**
	 * Function to get all Real Suppliers
	 * @param string $select Comma seperated string of column names to be selected
	 * @param array $where_array Array of columns and values to be used in where clause
	 * @param mixed $limit Its the limit value in case of limited members to be fetched
	 */
	function get_real_suppliers_data($where_array = array(), $select = '',$limit = ''){

		if($select != '')
			$this->fdb->select($select);
		if ($limit != '')
		{
			$this->fdb->limit($limit);
		}
		if(!empty($where_array)){
			$this->fdb->where($where_array);
		}
		
		$this->fdb->order_by('rs_name','asc');
		$data = $this->fdb->get('real_suppliers')->result_array();
		if(empty($data) && !empty($where_array) && array_key_exists('LOWER(rs_name)', $where_array)){
			$where = "`merged_names` REGEXP '##".$where_array['LOWER(rs_name)']."$' OR `merged_names` REGEXP '^".$where_array['LOWER(rs_name)']."##' OR `merged_names` REGEXP '##".$where_array['LOWER(rs_name)']."##'";
			$this->fdb->select( 'rs_id' );
			$this->fdb->where( $where );
			$this->fdb->or_where( 'merged_names', $where_array['LOWER(rs_name)'] );
			return $this->fdb->get( 'real_suppliers' )->result_array();
		}else{
			return $data;
		}

	}

	function add_productsheet($inser_array = array()){
		$this->fdb->insert('obs_productsheets',$inser_array);
	}


	function product_name($select = ''){
		if($select != '')
			$this->fdb->select($select);

		$this->fdb->join('suppliers', 'products.p_s_id = suppliers.s_id');
		$this->fdb->where(array('approval_status'=>1,'is_nubel'=>0));
		return $this->fdb->get('products')->result_array();
	}

	function get_supplier_name($select = ''){
		

		if($select != '')
			$this->fdb->select($select);

		$this->fdb->select('s_name,s_id');
		$this->fdb->where('is_controller_cp', 0);
		$this->fdb->order_by("s_name", "asc");
		return $this->fdb->get('suppliers')->result_array();
	}

	function get_real_supplier_name($select = ''){
		

		if($select != '')
			$this->fdb->select($select);

		$this->fdb->order_by("rs_name", "asc");
		return $this->fdb->get('real_suppliers')->result_array();
	}

	function insert_pending_product($insert_array = array()){
		RETURN $this->fdb->insert('pending_products',$insert_array);
	}

	function get_suggestion($pro_name, $art_num_p = '',$art_num_s = '', $pro_id = 0, $sup_id = 0){
		
		$where_condition = "(`approval_status` = 1)
		AND  (`p_name`  LIKE '%".$pro_name."%'
		OR  `p_name_dch`  LIKE '%".$pro_name."%'
		OR  `p_name_fr`  LIKE '%".$pro_name."%')";

		if($art_num_p != ''){
			$where_condition .= "AND (`plu` = '".$art_num_p."')";
		}

		if($pro_id){
			$where_condition .= "AND (`p_s_id` = ".$pro_id.")";
		}

		if($sup_id){
			if($art_num_s != ''){
				$where_condition .= "AND (`art_nbr_supp` = '".$art_num_s."')";
			}
			$where_condition .= "AND (`supplier_id` = ".$sup_id.")";
			$this->fdb->join('products_suppliers','products.p_id = products_suppliers.product_id');
		}

		$this->fdb->select('p_id,p_name,p_name_dch,p_name_fr,s_name,plu');
		$this->fdb->where($where_condition);
		$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
		return $this->fdb->get('products')->result_array();
	}

	function get_searched_fdd_products($search_str){

		$where_condition = "(`approval_status` = 1)
		AND  (`p_name`  LIKE '%".$search_str."%'
		OR  `p_name_dch`  LIKE '%".$search_str."%'
		OR  `p_name_fr`  LIKE '%".$search_str."%'
		OR  `s_name`  LIKE '%".$search_str."%'
		OR  `rs_name`  LIKE '%".$search_str."%'
		OR  `barcode`  LIKE '%".$search_str."%'
		OR  `plu`  LIKE '%".$search_str."%')";

		$this->fdb->select('p_id,p_name,p_name_fr,p_name_dch,s_name,barcode,plu,product_type');
		$this->fdb->where($where_condition);
		$this->fdb->where('is_nubel',0);
		$this->fdb->where('hide_from_recipe',0);
		$this->fdb->join('real_suppliers','products.p_rs_id = real_suppliers.rs_id','left');
		$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
		return $this->fdb->get('products')->result_array();
	}

	function get_searched_new_products($search_str){

		$this->db->select(array('id','proname'));
		$this->db->where( 'company_id', 0 );
		$this->db->like( 'proname', $search_str );

		return $this->db->get('products')->result_array();
	}

	function get_searched_supplier($search_str){
		
		$this->fdb->select('s_name,s_id');
		$this->fdb->where( 'is_controller_cp', 0 );
		$this->fdb->like( 's_name', $search_str );
		$this->fdb->order_by("s_name", "asc");
		return $this->fdb->get('suppliers')->result_array();
	}

	function get_searched_realsupplier($search_str){
		
		$this->fdb->select('rs_name,rs_id');
		$this->fdb->like( 'rs_name', $search_str );
		$this->fdb->order_by("rs_name", "asc");
		return $this->fdb->get('real_suppliers')->result_array();
	}

	function get_searched_fdd_products_custom($search_str,$recipe_option,$company_id){
		$fav_arr = array();
		if ($recipe_option == 4)
		{
			$this->db->select('fdd_pro_id');
			$fav_products = $this->db->get_where('fdd_pro_fav',array('company_id'=>$company_id))->row_array();
			if ( !empty( $fav_products ) ){
				$fav_arr = json_decode( $fav_products[ 'fdd_pro_id' ] );
			}
		}
		$locale = $_COOKIE['locale'];
		if( $locale == 'fr_FR' ){
			$p_name = 'p_name_fr';
		}
		else{
			$p_name = 'p_name_dch';
		}
		$where_condition = "((`product_type` = '0' AND `approval_status` = '1') OR (`product_type` != '0'))";

		$where_condition .=	" AND  (";
		foreach ($search_str as $key => $value) {
			if( $locale == 'fr_FR' ) {
				$where_condition .= "`name_french_produ_supp_bar_plu` LIKE '%".$value."%'";
			}
			else {
				$where_condition .= "`name_produ_supp_bar_plu` LIKE '%".$value."%'";
			}
			$where_condition .=" AND";
		}

		$where_condition = chop($where_condition," AND");
		$where_condition .= ")";
		
		$this->fdb->select('p_id,p_name_fr,p_name_dch,s_name,barcode,plu,supplier_id,art_nbr_supp,product_type,rs_name');
		$this->fdb->where($where_condition);
		$this->fdb->where('hide_from_recipe', '0' );
		if ($recipe_option == 4 && !empty($fav_arr)){
			$this->fdb->where_in('p_id',$fav_arr);
		}
		$this->fdb->join('products_suppliers','products_suppliers.product_id=products.p_id','left');
		$this->fdb->join('real_suppliers','products_suppliers.supplier_id = real_suppliers.rs_id','left');
		$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
		$this->fdb->order_by($p_name, 'asc');
		$data=$this->fdb->get('products')->result_array();
		return $data;
	}

	function get_searched_extra_semi_products_custom($search_str){
		
		$this->db->select('id,proname');
		$this->db->like('proname', $search_str);
		return $this->db->get_where('products',array('semi_product'=>2))->result_array();

	}

	function get_searched_recipe_ingre($search_str, $type){
		$this->company_id=$this->session->userdata('cp_user_id');
		$result = array();

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
		if($type == 1)
			$this->db->where('products.direct_kcp',0);
		elseif($type == 2)
			$this->db->where('products.semi_product',1);
		elseif($type == 3)
			$this->db->where('products.semi_product',2);

		$this->db->where(array('fdd_pro_quantity.is_obs_product'=>0,'products.company_id'=>$this->company_id));
		if((!empty($k_id)) && ($type == 1))
			$this->db->where_not_in('fdd_pro_quantity.obs_pro_id', $k_id);
		$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
		$result = $this->db->get('fdd_pro_quantity')->result_array();

		$ingrename_array = array();
		if(!empty($result)){
			$whole_arr = array();
			foreach ($result as $key=>$val){
				$this->db->select(array('products_ingredients.ki_id','products_ingredients.ki_name'));
				$this->db->like('products_ingredients.ki_name',$search_str);
				if($type == 1)
					$this->db->where(array('products.direct_kcp'=>0));
				elseif($type == 2)
					$this->db->where(array('products.direct_kcp'=>1,'products.semi_product'=>1));
				elseif($type == 3)
					$this->db->where(array('products.direct_kcp'=>1,'products.semi_product'=>2));
				$this->db->where(array('products_ingredients.ki_id !='=>0,'products_ingredients.kp_id'=>$val['fdd_pro_id'],'products.company_id'=>$this->company_id,'products_ingredients.is_obs_ing'=>0));
				$this->db->join('products', 'products.id = products_ingredients.product_id');
				$ingre_arr = $this->db->get('products_ingredients')->result_array();
				if(!empty($ingre_arr))
					$whole_arr = array_merge($ingre_arr,$whole_arr);
			}

			if(!empty($whole_arr)){
				foreach ($whole_arr as $key=>$val){
					$arr['label'] = stripslashes($val['ki_name']);
					$arr['value'] = $val['ki_id'];
					$ingrename_array[] = $arr;
				}
			}
		}

		return $ingrename_array;
	}

	function get_ingre_product($ingre_id = 0, $type = 0){
		$this->company_id=$this->session->userdata('cp_user_id');
		$pro_arr = array();

		if($ingre_id != 0){
			$this->db->distinct();
			$this->db->select(array('products.id','products.pro_art_num','products.proname','products.prodescription'));
			if($type == 1)
				$this->db->where(array('products.direct_kcp'=>0));
			elseif($type == 2)
				$this->db->where(array('products.direct_kcp'=>1,'products.semi_product'=>1));
			elseif($type == 3)
				$this->db->where(array('products.direct_kcp'=>1,'products.semi_product'=>2));
			$this->db->where(array('products_ingredients.ki_id'=>$ingre_id,'products.company_id'=>$this->company_id));
			$this->db->join('products', 'products.id = products_ingredients.product_id');
			$this->db->order_by('products.id');
			$pro_arr = $this->db->get('products_ingredients')->result_array();
		}
		return $pro_arr;
	}

	function get_quant_details($obs_pro_id){
		$this->db->where(array('obs_pro_id'=>$obs_pro_id,'is_obs_product'=>0));
		return $this->db->get('fdd_pro_quantity')->result_array();
	}

	function get_own_quant_details($obs_pro_id){
		$this->db->where(array('obs_pro_id'=>$obs_pro_id,'is_obs_product'=>1));
		return $this->db->get('fdd_pro_quantity')->result_array();
	}

	function get_fdd_pro_details($fdd_pro_id){
		$this->fdb->select('p_id,p_name,p_name_fr,p_name_dch,s_name,barcode,plu,product_type');
		$this->fdb->where('p_id',$fdd_pro_id);
		$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
		return $this->fdb->get('products')->result_array();
	}

	function clean_all_fdd_quant($obs_pro_id){
		$this->db->where('obs_pro_id',$obs_pro_id);
		$this->db->delete('fdd_pro_quantity');

	}

	function add_fdd_quant($nsert_array){
		$this->db->insert('fdd_pro_quantity',$nsert_array);
	}

	function get_fdd_quant($obs_pro_id){
		$this->db->where(array('obs_pro_id'=>$obs_pro_id,'is_obs_product'=>0));
		return $this->db->get('fdd_pro_quantity')->result_array();
	}

	function get_fdd_prod_details( $fdd_pro_id, $select = array() ){
		if( !empty( $select ) ) {
			$this->fdb->select( $select );	
		}
        $this->fdb->where( array( 'p_id' => $fdd_pro_id, 'approval_status' => '1' ) );
		return $this->fdb->get('products')->result_array();
	}

	function get_prfx($where_array){
		$this->fdb->where($where_array);
		RETURN $this->fdb->get('ingredient_prefixes')->result_array();
	}

	function used_fdd_pro_info($obs_pro_id){
		$this->db->where(array('obs_pro_id'=>$obs_pro_id,'is_obs_product'=>0));
		$this->db->order_by("quantity", "desc");
		$result = $this->db->get('fdd_pro_quantity')->result_array();

		
		foreach ($result as $key=>$val){
			$this->fdb->select('products.p_name_dch,products.p_name_fr,products.p_name,products.data_sheet,products.barcode,products.plu,products.pdf_date,products.product_type,products.review&fixed,products.approval_status,suppliers.s_name,gs1_products.gs1_response');
			$this->fdb->where('products.p_id',$val['fdd_pro_id']);
			$this->fdb->join('suppliers', 'products.p_s_id = suppliers.s_id');
			$this->fdb->join('gs1_products', 'gs1_products.p_id = products.p_id','left');
			$fdd_result = $this->fdb->get('products')->result_array();
			if( !empty( $fdd_result ) ) {
				$result[$key]['p_name_dch'] = $fdd_result[0]['p_name_dch'];
				$result[$key]['p_name_fr'] = $fdd_result[0]['p_name_fr'];
				$result[$key]['p_name'] = $fdd_result[0]['p_name'];
				$result[$key]['data_sheet'] = $fdd_result[0]['data_sheet'];
				$result[$key]['s_name'] = $fdd_result[0]['s_name'];
				$result[$key]['barcode'] = $fdd_result[0]['barcode'];
				$result[$key]['plu'] = $fdd_result[0]['plu'];
				$result[$key]['pdf_date'] = $fdd_result[0]['pdf_date'];
				$result[$key]['product_type'] = $fdd_result[0]['product_type'];
				$result[$key]['fixed'] = $fdd_result[0]['review&fixed'];
				$result[$key]['approval_status'] = $fdd_result[0]['approval_status'];
				$result[$key]['gs1_response'] = $fdd_result[0]['gs1_response'];
			}
		}

		

		return $result;
	}


	function used_own_pro_info($obs_pro_id){
		$this->db->order_by("quantity", "desc");
		$this->db->where(array('obs_pro_id'=>$obs_pro_id,'is_obs_product'=>1));
		$this->db->join('products', 'products.id = fdd_pro_quantity.fdd_pro_id');
		$result = $this->db->get('fdd_pro_quantity')->result_array();
		foreach ($result as $key=>$val)
		{
			$query=array();
			
			if ($val['fdd_producer_id'] && $val['fdd_supplier_id'])
			{
				$this->fdb->select('s_name');
				$query = $this->fdb->get_where('suppliers', array('s_id' => $val['fdd_producer_id'],'is_controller_cp' => 0))->result();
			}elseif ($val['fdd_producer_id']){
				$this->fdb->select('s_name');
				$query = $this->fdb->get_where('suppliers', array('s_id' => $val['fdd_producer_id'],'is_controller_cp' => 0))->result();
			}elseif ($val['fdd_supplier_id']){
				$this->fdb->select('rs_name as s_name');
				$query = $this->fdb->get_where('real_suppliers', array('rs_id' => $val['fdd_supplier_id']))->result();
			}
			if (!empty($query))
			{
				$result[$key]['s_name']=$query[0]->s_name;
			}
			else {
				$result[$key]['s_name']="";
			}
		}
		return $result;
	}


	function get_custom_pending($c_id, $p_id = 0){
		$this->db->select('id,proname,changed_fixed_product_id');
		if($p_id != 0){
			$this->db->where('id',$p_id);
		}
		$this->db->where(array('company_id'=>$c_id, 'is_custom_pending'=>1));
		$results = $this->db->get('products')->result_array();

		foreach ($results as $key=>$result){
			
			$this->fdb->select('p_id,p_name');
			$this->fdb->where('p_id',$result['changed_fixed_product_id']);
			$res = $this->fdb->get('products')->result_array();
			
			if(!empty($res)){
				$results[$key]['changed_product'] = $res[0]['p_name'];
			}
		}
		RETURN $results;
	}

	function get_recent_approved(){
		$today = date('Y-m-d H:i:s');
		$today_7 = date('Y-m-d H:i:s',strtotime('-30 days'));
		$this->fdb->order_by('products.approval_date_time', 'desc');
		$this->fdb->where('products.approval_date_time >=', $today_7);
		$this->fdb->where('products.approval_date_time <=', $today);
		$this->fdb->where('products.approval_status', 1);
	//	$this->fdb->join('product_approval_log','product_approval_log.product_id = products.p_id');
		$this->fdb->join('real_suppliers','products.p_rs_id = real_suppliers.rs_id','left');
		$this->fdb->join('suppliers','products.p_s_id = suppliers.s_id');
		RETURN $this->fdb->get('products')->result();
	}

	function fixed_pdf($p_id = 0){

		
		$this->fdb->select('data_sheet');
		$this->fdb->where('p_id',$p_id);
		$result = $this->fdb->get('products')->result_array();
		
		RETURN $result;
	}

	function get_fdd_pro_prefixes($where_array = array()){
		$this->db->select('prefix');
		$this->db->order_by('display_order');
		$this->db->where($where_array);
		RETURN $this->db->get('products_ingredients')->result_array();
	}


	function semi_contains($pro_id){
		$this->db->where('obs_pro_id',$pro_id);
		$this->db->where_not_in('semi_product_id',array(0));
		RETURN $this->db->get('fdd_pro_quantity')->result_array();
	}

	function fdd_contains($pro_id){
		$this->db->where(array('obs_pro_id'=>$pro_id,'semi_product_id'=>0,'is_obs_product'=>0));
		RETURN $this->db->get('fdd_pro_quantity')->result_array();
	}

	function own_contains($pro_id){
		$this->db->where(array('obs_pro_id'=>$pro_id,'semi_product_id'=>0,'is_obs_product'=>1));
		RETURN $this->db->get('fdd_pro_quantity')->result_array();
	}

	function is_marked($pro_id){
		$this->db->where('obs_pro_id',$pro_id);
		$this->db->where('is_obs_product',0);
		$results = $this->db->get('fdd_pro_quantity')->result_array();

		$fdd_ids = array();
		if(!empty($results)){
			$fdd_ids = array_column( $results, 'fdd_pro_id' );
		}

		
		if(!empty($fdd_ids)){
			$this->fdb->where_in('p_id',$fdd_ids);
		}
		$this->fdb->where('is_nubel',1);
		$nubels = $this->fdb->get('products')->result_array();
		if(!empty($nubels)){
			$marked = 1;
		}else {
			$marked = 0;
		}

		
		RETURN $marked;
	}

	function get_login_details($select = '',$where = array()){
		if($select != '')
			$this->db->select($select);

		if($where != '')
			$this->db->where($where);

		$this->db->order_by('id');
		return $this->db->get('login_details')->result_array();
	}

	function add_login_details(){
		$this->company_id = $this->session->userdata('cp_user_id');
		$prod_arr = $this->input->post("att_prod");
		$supp_arr = $this->input->post("att_supp");
		$uname_arr = $this->input->post("att_name");
		$pass_arr = $this->input->post("att_pass");
		
		$this->db->delete('login_details',array('company_id'=>$this->company_id));
		$insert_arr = array();
		for ( $i = 0; $i < count($prod_arr); $i++ ){
			if( ( $prod_arr[ $i ] || $supp_arr[ $i ] ) && ($uname_arr[$i] != '') && ($pass_arr[$i] != '') ){
				$insert_arr[ $i ] = array(
								'company_id' 		=> $this->company_id,
								'fdd_producer_id' 	=> $prod_arr[ $i ],
								'fdd_supplier_id' 	=> $supp_arr[ $i ],
								'username' 			=> trim( $uname_arr[ $i ] ),
								'password' 			=> trim( $pass_arr[ $i ] )
						);
			}
		}
		if( !empty( $insert_arr  ) ){
			$this->db->insert_batch( 'login_details', $insert_arr );
			$response = $this->db->affected_rows( );
			$this->db->delete( 'login_details' , array(
								'company_id' 		=> $this->company_id,
								'fdd_producer_id' 	=> 0,
								'fdd_supplier_id' 	=> 0,
								'username' 			=> '',
								'password' 			=> ''
							)
						);
			if( $response ){
				return true;
			}else{
				return false;
			}
			
		}else{
			return true;
		}
	}

	function check_via_fdd($str = '',$obs_pro_id=0){
		$company_id = 0;
		if($str != ''){
			
			$query = $this->fdb->get_where('login_via_obs', array('unique_str'=>$str))->row_array();
			if(!empty($query)){
				$this->fdb->delete('login_via_obs',array('id'=>$query['id']));

				
				$this->db->distinct();
				$this->db->select('company_id');
				$this->db->join('company','company.id=products_pending.company_id');
				$result = $this->db->get_where('products_pending',array('product_id'=>$obs_pro_id,'via_api'=>'0'))->result_array();
				if (!empty($result)){
					$company_id = $result[0]['company_id'];
				}
			}
		}
		return $company_id;
	}

	function get_real_supplier_name_addedit($select = ''){
		

		if($select != '')
			$this->fdb->select($select);

		$this->fdb->select('rs_id, rs_name');
		$this->fdb->order_by("rs_name", "asc");
		return $this->fdb->get('real_suppliers')->result_array();
	}

	function get_prod_temp( $pro_id ){
		$this->db->select( 'conserve_min, conserve_max' );
		$this->db->where( 'product_id', $pro_id );
		return $this->db->get( 'products_labeler' )->result_array();
	}

	/**
	 *
	 * Function to update product that are marked as favourate for a company
	 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	 */

	function update_fdd_pro_fav( $status_array ){
		if( isset( $status_array ) && !empty(  $status_array ) ){
			$fdd_pro_ids = array();

			$this->db->select( 'fdd_pro_id' );
			$result = $this->db->get_where( 'fdd_pro_fav' ,array( 'company_id' => $status_array[ 'company_id' ] ) )->row_array();
			if( !empty( $result ) ){
				$fdd_pro_ids = json_decode( $result[ 'fdd_pro_id' ] );
				if( $status_array[ 'is_favourate' ] == '1' ){
					if( ! in_array( $status_array[ 'fdd_pro_id' ], $fdd_pro_ids ) ){
						array_push( $fdd_pro_ids, $status_array[ 'fdd_pro_id' ] );
					}
				} else if( $status_array[ 'is_favourate' ] == '0' ) {
					if( in_array( $status_array[ 'fdd_pro_id' ], $fdd_pro_ids ) ){
						$unset_key = array_search( $status_array[ 'fdd_pro_id' ], $fdd_pro_ids );
						unset( $fdd_pro_ids[ $unset_key ] );
						$fdd_pro_ids = array_values( $fdd_pro_ids );
					}
				}
				$this->db->where( 'company_id', $status_array[ 'company_id' ] );
				if( sizeof( $fdd_pro_ids ) == 0 ){
					$this->db->delete( 'fdd_pro_fav' );
				} else {
					$this->db->update( 'fdd_pro_fav' ,array( 'fdd_pro_id' => json_encode( $fdd_pro_ids ), 'date_added' => date( 'Y-m-d H:i:s' ) ) );
				}
				return 'success';
			}else{
				$fdd_pro_ids = array( $status_array[ 'fdd_pro_id' ] );
				$this->db->insert( 'fdd_pro_fav', array( 'company_id' => $status_array[ 'company_id' ], 'fdd_pro_id' => json_encode( $fdd_pro_ids ), 'date_added' => date( 'Y-m-d H:i:s' ) ) );
				return 'success';
			}
		}
	}
	/**
	 *
	 * Function to get the product status for all related product
	 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	 */
	function get_productStatus( $company_id ){
		if( $company_id ){
			$this->db->select( 'fdd_pro_id' );
			return  $this->db->get_where( 'fdd_pro_fav' ,array( 'company_id' => $company_id ) )->row_array( );
		}
	}

	/**
	 *
	 * Function to get the product status for single  product that is added by add_row function
	 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	 */
	function getSingleProduct_status( $fdd_pro_id ,$company_id ){
		if( isset( $fdd_pro_id ) && isset( $company_id ) ){
			$this->db->select( 'fdd_pro_id' );
			$result = $this->db->get_where( 'fdd_pro_fav', array( 'company_id' => $company_id ) )->row_array( );
			if( !empty( $result ) ) {
				$fdd_pro_ids = json_decode( $result[ 'fdd_pro_id' ] );
				if( in_array( $fdd_pro_id , $fdd_pro_ids ) ) {
					return 'exist';
				} else {
					return 'does_not_exist';
				}
			} else {
				return 'does_not_exist';
			}
		}
	}

	function get_fav_pro_data( $company_id ) {
		if( $company_id ){
			return  $this->db->get_where( 'fdd_pro_fav' ,array( 'company_id' => $company_id ) )->result_array( );
		} else {
			return false;
		}
	}

	function check_via_newobs($str = '',$obs_comp_id=0 ){
		$company_id = 0;
		if($str != ''){
			$str = trim( $str );
			$query = $this->db->get_where('login_via_newobs', array('unique_str'=>$str) )->row_array();
			if(!empty($query)){
				$this->db->delete('login_via_newobs',array('id'=>$query['id']));
				$company_id = $obs_comp_id;
			}
		}
		return $company_id;
	}
}
