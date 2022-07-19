<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Allergenkart Library
 *
 * This is a for allergenkart json files update
 *
 * @package Libraries
 * @author Ankush Katiyar <ankushkatiyar@cedcoss.com>
 */

class Allergenkart {

	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->helper('url');
		$this->CI->load->library('session');
		$this->CI->load->database();
	}

	function update_allergenkart_json_files($company_id = 0,$company_role = '',$company_parent_id = ''){
		$data = array();

		$this->CI->db->where(array('id'=> $company_id));
		$company = $this->CI->db->get('company')->result_array();
		if($company[0]['ac_type_id'] == 4 || $company[0]['ac_type_id'] == 5 || $company[0]['ac_type_id'] == 6 || $company[0]['ac_type_id'] == 7)
		{
			$data['company'] = $company[0];
			$data['allergenkaart_settings'] = $this->get_allergenkaart_settings( '', array('company_id' => $company_id) );
			$data['products'] = $this->get_products($company_id,$company[0]['ac_type_id']);
			if(!empty($data['products'])){
				$data['category'] = $this->get_category($company_id);
				$sub_category= array();
				foreach ($data['category'] as $key=> $value){
					$sub_category[$value['id']] = $this->get_sub_category($value['id']);
				}
				$data['sub_category'] = $sub_category;
			}
			else{
				$data['category'] = array();
				$data['sub_category'] = array();
			}

			$data['general_settings'] = $this->get_general_settings('comp_default_image',$company_id);

			$fp = fopen(dirname(__FILE__).'/../../assets/allergenkart_json/allergenkart_content_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($data));
			fclose($fp);
		}
	}

	function update_allergenkart_json_files_via_script($company_id = 0,$company_role = '',$company_parent_id = ''){
		$data = array();

		$this->CI->db->where(array('id'=> $company_id));
		$company = $this->CI->db->get('company')->result_array();
		if($company[0]['ac_type_id'] == 4 || $company[0]['ac_type_id'] == 5 || $company[0]['ac_type_id'] == 6 || $company[0]['ac_type_id'] == 7)
		{
			$data['company'] = $company[0];
			$data['allergenkaart_settings'] = $this->get_allergenkaart_settings( '', array('company_id' => $company_id) );
			try{
				$data['products'] = $this->get_products($company_id,$company[0]['ac_type_id']);
			}
			catch(Exception $e){
				echo 'Message: ' .$e->getMessage();
			}

			if(!empty($data['products'])){
				$data['category'] = $this->get_category($company_id);
				$sub_category= array();
				foreach ($data['category'] as $key=> $value){
					$sub_category[$value['id']] = $this->get_sub_category($value['id']);
				}
				$data['sub_category'] = $sub_category;
			}

			$data['general_settings'] = $this->get_general_settings('comp_default_image',$company_id);

			$fp = fopen(dirname(__FILE__).'/../../assets/allergenkart_json3/allergenkart_content_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($data));
			fclose($fp);
		}
	}


	/**
	 * @name get_company
	 * @param string $slug_name
	 * @return array
	 */
	function get_company($slug_name = ''){
		$company_id = array();

		if($slug_name != ''){
			$this->CI->db->select('id,obsdesk_logo,ac_type_id');
			$this->CI->db->where_in('ac_type_id',array(4,5,6,7));
			$this->CI->db->where(array('company_slug'=> $slug_name));
			$company_id = $this->CI->db->get('company')->result_array();
		}
		return $company_id;
	}

	/**
	 * @name get_desk_settings
	 * @param string $select
	 * @param array $where
	 */
	function get_allergenkaart_settings( $select = '', $where = array()){
		if($select != '')
			$this->CI->db->select($select);

		if( !empty($where) ){
			foreach( $where as $key => $val )
				$this->CI->db->where( $key, $val );
		}

		$allergenkaart_settings = $this->CI->db->get('allergenkaart_design');
		return( $allergenkaart_settings->result_array() );
	}

	/**
	 * @name get_products
	 * @param number $comp_id
	 * @return array
	 */
	function get_products($comp_id = 0,$ac_type_id = 0){
		$prod_details = array();
		if($comp_id){
			if($ac_type_id==7){
				$this->CI->db->select('company_id,id,proname,categories_id,subcategories_id,image,allergence,direct_kcp');
				$this->CI->db->where(array('company_id'=> $comp_id, 'categories_id !='=>0,'subcategories_id !='=>0,'status' =>1));
				$this->CI->db->order_by('pro_display');
				$prod_details = $this->CI->db->get('products')->result_array();
				if(!empty($prod_details)){
					foreach ($prod_details as $key => $product){
						$prod_details[$key]['product_fixed'] = 'Yes';
					}
				}
			}
			elseif($ac_type_id==4 || $ac_type_id==5 || $ac_type_id==6){
				$this->CI->db->select('company_id,id,proname,categories_id,subcategories_id,image,direct_kcp');
				$this->CI->db->where(array('company_id'=> $comp_id, 'categories_id !='=>0,'subcategories_id !='=>0,'status' =>1));
				$this->CI->db->order_by('pro_display');
				$prod_details = $this->CI->db->get('products')->result_array();
				foreach ($prod_details as $pro_key => $pro_value) {
					$this->CI->db->distinct();
					$this->CI->db->select('ka_id');
					$this->CI->db->where('product_id',$pro_value['id']);
					$this->CI->db->order_by('ka_id');
					$aller_data=$this->CI->db->get('products_allergence')->result();
					$all_str = '';
					if(!empty($aller_data)){

						foreach ($aller_data as $value) {
							$all_str.= $value->ka_id.'#';
						}

						$all_str = chop($all_str, "#");
					}

					$prod_details[$pro_key]['allergence'] = $all_str;

					$complete = 1;
					if($pro_value['direct_kcp'] == 1){
						$this->CI->db->where(array('obs_pro_id'=>$pro_value['id'],'is_obs_product'=>0));
						$result = $this->CI->db->get('fdd_pro_quantity')->result_array();
						if(empty($result)){
							$complete = 0;
						}
					}
					else{
						$this->CI->db->where(array('obs_pro_id'=>$pro_value['id']));
						$result_custom = $this->CI->db->get('fdd_pro_quantity')->result_array();
						if(!empty($result_custom)){
							$this->CI->fdb = $this->CI->load->database('fdb', true);
							foreach ($result_custom as $val){
								if($val['is_obs_product'] == 1){
									$complete = 0;
									break;
								}
								else {
									
									$this->CI->fdb->where( 'p_id', $val[ 'fdd_pro_id' ] );
									$this->CI->fdb->where( 'approval_status', 1 );
									$count = $this->CI->fdb->count_all_results( 'products' );
									if( $count == 0 ) {
										$complete = 0;
										break;
									}
									
								}
							}
							$this->CI->fdb->close();
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

		}
		return $prod_details;
	}

	/**
	 * @name get_category
	 * @param number $comp_id
	 * @return array
	 */
	function get_category($comp_id = 0){
		$cat_details = array();
		if($comp_id){
			$this->CI->db->select('id,name');
			$this->CI->db->where(array('company_id'=> $comp_id,'status'=>'1'));
			$this->CI->db->order_by('order_display');
			$cat_details = $this->CI->db->get('categories')->result_array();
		}
		return $cat_details;
	}

	/**
	 * @name get_sub_category
	 * @param number $cat_id
	 * @return array
	 */
	function get_sub_category($cat_id = 0){
		$sub_cat_details = array();
		if($cat_id){
			$this->CI->db->select('id,subname');
			$this->CI->db->where(array('categories_id'=> $cat_id,'status'=>'1'));
			$this->CI->db->order_by('suborder_display');
			$sub_cat_details = $this->CI->db->get('subcategories')->result_array();
		}
		return $sub_cat_details;
	}

	function get_general_settings( $select = '', $company_id = null){
		$response = array();
		if($company_id){
			if($select != ''){
				$this->CI->db->select($select);
			}
			$response = $this->CI->db->get_where('general_settings',array('company_id' => $company_id))->result();
		}
		return $response;
	}
}
?>