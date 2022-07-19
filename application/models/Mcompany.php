<?php
class Mcompany extends CI_Model{

	var $company_id;

	function __construct(){

		parent::__construct();

		$this->company_id=$this->session->userdata('cp_user_id');
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function if_ibsoft_active($company_id = NULL)
	{
	    if(!$company_id)
		  $company_id = $this->company_id;

		$this->db->where(array('id'=>$this->company_id));
		$query = $this->db->get('company');
		$result = $query->row();

		if($result->ibsoft_active == 1)
		  return true;
		else
		  return false;
	}

	function get_company($param=array()){

		if($param){

			foreach($param as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}else{

			$this->db->where(array('id'=>$this->company_id));

		}

		if(array_key_exists('parent_id',$param))
		{
		  $this->db->join('general_settings','general_settings.company_id = company.id','left');
		}

		$query = $this->db->get('company');
		$result = $query->result();

		return $result;

	}

	
	function remove_uploaded_images($where){

			
		$this->db->select( 'comp_default_image' );
		$this->db->where( 'company_id', $where );
		$image_name = $this->db->get( 'general_settings' )->row_array();
		$this->db->where( 'company_id', $where );
		$response = $this->db->update( 'general_settings',array('comp_default_image' => '') );
		if( file_exists( $this->config->item( 'image_upload_path' ).'cp/images/infodesk_default_image/'.$image_name['comp_default_image'] ) ){ 
		unlink($this->config->item( 'image_upload_path' ).'cp/images/infodesk_default_image/'.$image_name['comp_default_image']);
		
		}
		return $response;

			
	}

	function get_company_account_price($ac_type_id = 0){
		if ($ac_type_id)
		{
			$this->db->select('ac_price');
			$result = $this->db->get_where('account_type',array('id' => $ac_type_id))->result_array();
			if (!empty($result) )
			{
				return $result[0]['ac_price'];
			}
			else{
				return false;
			}
		}
		else {
			return false;
		}
	}

	function get_company_name_and_type($param=array()){
		if($param){
			foreach($param as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}else{
			$this->db->where(array('id'=>$this->company_id));
		}
		$this->db->select('type_id,company_name');
		$company = $this->db->get('company');
		$company = $company->result();
		$compony_type_id=$company[0]->type_id;
		$compony_name=$company[0]->company_name;

		$this->db->select('company_type_name');
		$company_type_name = $this->db->get_where('company_type',array('id' => $compony_type_id))->result();
		$compony_type_name=$company_type_name[0]->company_type_name;
		return " ".$compony_type_name." ".$compony_name;
	}

	function payment_option($param=array()){
		$sub_company_ids = array();
		$flag = 0;
		if($param){

			foreach($param as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}else{

			$this->db->where(array('id'=>$this->company_id));

		}
		$query = $this->db->get('company')->result_array();
		if($query){
			foreach ($query as $values){
				$this->db->join('cp_cardgate_settings','general_settings.company_id = cp_cardgate_settings.company_id', 'left');
				$this->db->where('general_settings.company_id = '.$values['id'].' AND (online_payment = "1" OR cp_cardgate_settings.cardgate_payment = "1")');
				$result = $this->db->get('general_settings')->result_array();
				if($result){

					$this->db->select('adv_payment');
					$adv_payment = $this->db->get_where('order', array('company_id' => $this->company_id))->result();
					if(!empty($adv_payment)){
						$adv_payment = $adv_payment[0]->adv_payment;
						if(!$adv_payment){
							$flag = 1;
							break;
						}
					}
				}
			}
			if($flag == 1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}

	}

	function update_company_details( $company_id = NULL, $params = array() )
	{
	    if(!$company_id)
		  $company_id = $this->company_id;

		if(!empty($params))
		{
		   $this->db->where( 'id', $company_id );
		   $result = $this->db->update('company',$params);
		   return $result;
		}
		else
		  return false;
	}

	function update_company(){

		if($this->input->post('act')=="set_password"){

			$current_password=$this->input->post('current_password');
			$company=$this->get_company();
			//print_r($company);
			if($current_password==$company[0]->password){
				if($this->input->post('new_password')==$this->input->post('confirm_password')){
					$new_password=$this->input->post('new_password');
					$this->db->where(array('id'=>$this->company_id));
					$this->db->update('company',array('password'=>$new_password));
					return  'password changed successfully';


				}else{

					return  'new password dosent match';

				}

			}else{

			return 'current password is not correct';


			}
		}else if($this->input->post('act')=="update_profile"){



		}

	}

	function update_company_profile($arr=null){

		//echo $this->input->post('country_id');
		//die();
		$update_company_profile=array('company_name'=>$this->input->post('company_name'),
									  'type_id'=>$this->input->post('type_id'),
									  'first_name'=>$this->input->post('first_name'),
									  'last_name'=>$this->input->post('last_name'),
									  'email'=>$this->input->post('email'),
									  'phone'=>$this->input->post('phone'),
									  'address'=>$this->input->post('address'),
									  'zipcode'=>$this->input->post('zipcode'),
									  'city'=>$this->input->post('city'),
									  'country_id'=>$this->input->post('country_id'),
									  'vat'=>$this->input->post('vat'),
									  'username'=>$this->input->post('username'),
									  'password'=>$this->input->post('password'),
									  'client_register_notification'=>$this->input->post('client_register_notification'),
									  );

		$this->db->where(array('id'=>$this->company_id));
		$result = $this->db->update('company',$update_company_profile);
		return $result;
	}

	function get_co_type($company_type_id)
	{
	  $query= $this->db->get_where('company_type',array('id'=>$company_type_id))->row_array();
	  return $query;
	}


	/*--------- >>>>>>>>>>>>>>>>>>>>>>>>>       FUNCTIONS FOR DESK     <<<<<<<<<<<<<<<<<<<<<<<----------------*/
	function get_companies( $where = array(), $order = array(), $limit = '', $offset = '' )
	{
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}

		if( !empty($order) )
		{
			foreach( $order as $ob => $o )
				$this->db->order_by( $ob, $o );
		}

		if( $limit && $offset )
			$this->db->limit( $limit, $offset );
		elseif( $limit )
		$this->db->limit( $limit );

		$companies = $this->db->get('company');
		return( $companies->result() );
	}

	function update_companies( $where_arr = array(), $update_arr = array() )
	{
		if( empty($update_arr) )
			return false;

		if( !empty($where_arr))
			$this->db->where( $where_arr );

		return $this->db->update( 'company', $update_arr );
	}

	function get_desk_settings( $where = array())
	{
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}

		$desk_settings = $this->db->get('desk_settings');
		return( $desk_settings->result_array() );
	}

	function get_allergenkaart_settings($where_arr){
		$this->db->where( "company_id", $where_arr['company_id'] );
		$allkaart_settings = $this->db->get('allergenkaart_design')->result();
		return $allkaart_settings;
	}

	function insert_desk_settings( $insert_arr = array())
	{
		if( empty($insert_arr) )
			return false;

		$this->db->insert( 'desk_settings', $insert_arr );
		return $this->db->insert_id();
	}

	function update_desk_settings( $where_arr = array(), $update_arr = array() )
	{	
		if( empty($update_arr) )
			return false;

		if( !empty($where_arr))
			$this->db->where( $where_arr );

		return $this->db->update( 'desk_settings', $update_arr );
	}

	function update_company_default_image( $where_arr = array(), $update_arr = array() )
	{	
		if( empty($update_arr) )
			return false;

		if( !empty($where_arr))
			$this->db->where( $where_arr );

		return $this->db->update( 'general_settings', $update_arr );
	}

	function get_languages()
	{
		$this->db->where( array( 'locale <>' => '',	'language_file <>' => '', 'language_file_2 <>' => '' ) );

		$languages = $this->db->get('language');
		return( $languages->result() );
	}

	function get_company_types( $where = array())
	{
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}

		$company_types = $this->db->get('company_type');
		return( $company_types->result() );
	}

	function get_countries( $where = array())
	{
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}

		$countries = $this->db->get('country');
		return( $countries->result() );
	}

	function get_desk_section_design( $where = array() ){
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}

		$countries = $this->db->get('desk_section_design');
		return( $countries->result() );
	}

	function update_desk_section_design($where_arr,$update){
		$is_exist = $this->db->get_where('desk_section_design',$where_arr)->result();
		if(empty($is_exist)){
			$update['company_id'] = $this->company_id;
			$this->db->insert('desk_section_design',$update);
		}
		else{
			$this->db->where( "company_id", $where_arr['company_id'] );
			$countries = $this->db->update('desk_section_design',$update);
		}
	}

	function update_allergenkaart_design($where_arr,$update_allergen){
		$is_exist = $this->db->get_where('allergenkaart_design',$where_arr)->result();
		if(empty($is_exist)){
			$update_allergen['company_id'] = $this->company_id;
			$this->db->insert('allergenkaart_design',$update_allergen);
		}
		else{
			$this->db->where( "company_id", $where_arr['company_id'] );
			$this->db->update('allergenkaart_design',$update_allergen);
		}
	}

	function get_company_name($search_str = ''){
		$this->db->select('id,company_name');
		if($search_str != ''){
			$this->db->like('company_name',urldecode($search_str));
		}
		$this->db->where_in('ac_type_id',array('4','5','6'));
		$this->db->where_in('role',array('master','super'));
		$result = $this->db->get_where('company',array('id !='=>$this->company_id, 'approved !='=>0,'status'=>'1'))->result_array();
		return $result;
	}
	// trail
	/**
		 * This private function is used to fetch Keurslager Ingredients related with the given product ID
		 * @access private
		 * @param int $product_id It is the ID of product for which ingredients have to be fetch
		 * @return array $ingredients It is the array if ingredients associated with the given product
		 */
		function get_k_ingredients($product_id = 0){
			$ingredients = array();
			if($product_id){
/* 				$this->db->select('prefix,ki_name');
				$this->db->order_by('kp_display_order', 'ASC');
				$this->db->order_by('display_order', 'ASC');
				$ingredients = $this->db->get_where('products_ingredients', array('product_id' => $product_id))->result(); */
				
				$ingredients = $this->db->query(
				"SELECT ki_id, prefix, ki_name, have_all_id, aller_type, aller_type_fr, aller_type_dch, allergence, allergence_fr, allergence_dch, sub_allergence, sub_allergence_fr, sub_allergence_dch FROM
					(SELECT a.ki_id,a.prefix,a.ki_name,a.kp_display_order, a.display_order,a.have_all_id,a.aller_type,a.aller_type_fr,a.aller_type_dch,a.allergence,a.allergence_fr,a.allergence_dch,a.sub_allergence,a.sub_allergence_fr,a.sub_allergence_dch
					FROM products_ingredients a
					WHERE product_id = ".$product_id."
					AND ((display_order=1 AND is_obs_ing=1) OR (display_order !=0 AND is_obs_ing=0) OR (0 = (SELECT COUNT(b.ki_id) FROM products_ingredients b WHERE b.product_id = ".$product_id." AND a.kp_id = b.kp_id AND b.ki_id != 0)+(SELECT COUNT(c.ki_id) FROM products_ingredients_vetten c WHERE c.product_id = ".$product_id." AND a.kp_id = c.kp_id AND c.ki_id != 0)+(SELECT COUNT(d.ki_id) FROM products_additives d WHERE d.product_id = ".$product_id." AND a.kp_id = d.kp_id AND d.ki_id != 0)))
					ORDER BY kp_display_order ASC) AS INGTAB
					ORDER BY kp_display_order ASC, display_order ASC")->result();
			}
			return $ingredients;
		}
		
		/**
		 * This private function is used to fetch Keurslager Ingredients (Vetten) related with the given product ID
		 * @access private
		 * @param int $product_id It is the ID of product for which ingredients (vetten) have to be fetch
		 * @return array $vetten It is the array if ingredients (vetten) associated with the given product
		 */
		function get_k_ingredients_vetten($product_id = 0){
			$vetten = array();
			if($product_id){
				$vetten = $this->db->query(
				"SELECT * FROM (SELECT *
				FROM products_ingredients_vetten
				WHERE product_id = ".$product_id." ORDER BY kp_display_order ASC) AS VETTAB
				GROUP BY ki_name
				ORDER BY kp_display_order ASC, display_order ASC")->result();
			}
			return $vetten;
		}
		/**
		 * This private function is used to fetch Keurslager Ingredients (additives) related with the given product ID
		 * @access private
		 * @param int $product_id It is the ID of product for which ingredients (additives) have to be fetch
		 * @return array $additives It is the array if ingredients (additives) associated with the given product
		 */
		function get_k_additives($product_id = 0){
			$additives = array();
			if($product_id){
				$additives = $this->db->query(
				"SELECT * FROM (SELECT products_additives.*, additives.add_name
				FROM products_additives
				JOIN additives ON additives.add_id = products_additives.add_id
				AND products_additives.product_id = ".$product_id." ORDER BY kp_display_order ASC) AS ADDTAB
				GROUP BY ki_name
				ORDER BY kp_display_order ASC, display_order ASC")->result();
			}
			return $additives;
		}
		/**
		 * This private function is used to fetch Keurslager Allergence related with the given product ID
		 * @access private
		 * @param int $product_id It is the ID of product for which allergence have to be fetch
		 * @return array $traces It is the array if allergence associated with the given product
		 */
		function get_k_allergence($product_id = 0){
			$allergence = array();
			if($product_id){
				$this->db->select('prefix,ka_id,ka_name');
				$this->db->order_by('display_order', 'ASC');
				$this->db->group_by('ka_id');
				$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
			}
			return $allergence;
		}
		/**
		 * This private function is used to fetch Keurslager Sub Allergence related with the given product ID
		 * @access private
		 * @param int $product_id It is the ID of product for which sub allergence have to be fetched
		 * @return array $traces It is the array if sub allergence associated with the given product
		 */
		function get_k_sub_allergence($product_id = 0){
			$allergence = array();
			if($product_id){
				$this->db->select('parent_ka_id,sub_ka_name');
				$this->db->order_by('display_order', 'ASC');
				$this->db->group_by('sub_ka_id');
				$allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
			}
			return $allergence;
		}
		
		/**
		 * This private function is used to fetch Nutrition value related with the given product ID
		 * @access private
		 * @param int $product_id It is the ID of product for which allergence have to be fetch
		 * @return array $recipe_wt It is the weight of the recipe
		 */
		function get_nutritions($product_id = 0, $recipe_wt = 0){
			$nutri_values = array();
			if($product_id){
				$this->db->where(array('obs_pro_id'=>$product_id,'is_obs_product'=>0));
				$has_fdd_quant = $this->db->get('fdd_pro_quantity')->result_array();
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
						$this->fdb->where('p_id',$has_fdd_qu['fdd_pro_id']);
						$fdd_pro_info = $this->fdb->get('products')->result_array();
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
				}
			}
			return $nutri_values;
		}

}
?>