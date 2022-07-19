<?php
class MOverview extends CI_Model {
	function __construct() {
		// Call the Model constructor
		parent::__construct ();
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function get_company($params = array(), $orderby = array(), $return_type = '', $start = 0, $limit = 0) {
		
		$company = array ();
		$text = array ();
		$orderbyarr = array ();

		$sql = " SELECT `company`.id,  `company`.company_name,`company`.email,`company`.phone,`company`.city,`company`.registration_date,`company`.first_name,`company`.last_name,`company_type`.company_type_name,`account_type`.ac_title, (SELECT COUNT(*) FROM `products` WHERE `products`.company_id = `company`.id) AS total_products FROM `company`
		       JOIN `company_type` ON `company`.type_id = `company_type`.id
		       JOIN `account_type` ON `company`.ac_type_id = `account_type`.id ";

		
		unset($params['flag']);

		if (! empty ( $params )) {
			foreach ( $params as $k => $v ) {
				$text [] = 'company.' . $k . '="' . $v . '"';
			}
			$r = implode ( " AND ", $text );
			$sql = $sql . " WHERE " . $r;
		}

		$sql .= ' AND ac_type_id IN (4,5,6)';

		if (sizeof ( $orderby ) > 0) {
			foreach ( $orderby as $k => $v ) {
				$orderbyarr [] = '`company`.' . $k . ' ' . $v;
			}
			$sql .= ' ORDER BY ' . implode ( ', ', $orderbyarr );
		}

		if ($start || $limit) {
			if ($start && $limit)
				$sql .= ' LIMIT ' . $start . ',' . $limit;
			else if ($limit)
				$sql .= ' LIMIT ' . $limit;
		}

		$execute = $this->db->query ( $sql );

		$companies_ids = array_column( $execute->result_array(), 'id' );
		$new_execute_array = $execute->result_array ();
          
		if (!empty($new_execute_array)) {
			foreach ($new_execute_array as $skey => $sval) {

				/* total recipie srart*/
				$this->db->distinct('id');
				$this->db->select( 'id' );
				$prod_ids = $this->db->get_where('products',array('company_id'=>$sval['id']))->result_array();
				$prod_ids = array_column( $prod_ids, 'id' );

				$filtered_obs_id = array();
				if (!empty($prod_ids)) {
					$this->db->select('fdd_pro_quantity.obs_pro_id,fdd_pro_quantity.fdd_pro_id');
					$this->db->where_in('obs_pro_id',$prod_ids);
					$obs_prod_ids = $this->db->get_where('fdd_pro_quantity',array( 'is_obs_product'=>0 ))->result_array();
					$filtered_obs_id = array_values(array_unique(array_column( $obs_prod_ids, 'obs_pro_id' )));

					$filtered_fdd_id = array_values(array_unique(array_column( $obs_prod_ids, 'fdd_pro_id' )));

					if (!empty($filtered_fdd_id)) {
						$this->fdb->select('p_id');
						$this->fdb->where_in('p_id',$filtered_fdd_id);
						$result_arr = $this->fdb->get_where('products',array('approval_status'=>'0'))->result_array();
						$un_approved_fdd_id = array_values(array_unique(array_column( $result_arr, 'p_id' )));

						if (!empty($un_approved_fdd_id)) {
							$this->db->distinct();
							$this->db->select('fdd_pro_quantity.obs_pro_id');
							$this->db->where_in('fdd_pro_id',$un_approved_fdd_id);
							$filter_unapprove_obs_prod_ids = $this->db->get_where('fdd_pro_quantity',array( 'is_obs_product'=>0,'comp_id'=> $sval['id']))->result_array();
							$filter_unapprove_obs_prod_ids = array_values(array_unique(array_column( $filter_unapprove_obs_prod_ids, 'obs_pro_id' )));
							$filtered_obs_id = array_diff($filtered_obs_id, $filter_unapprove_obs_prod_ids);
						}
					}
				}
				$new_execute_array[ $skey ]['recipes'] = count($filtered_obs_id);
				/* total recipe end*/

				/* total used product srart*/
				$this->db->distinct();
				$this->db->select('fdd_pro_id');
				$used_pro1 = $this->db->get_where('fdd_pro_quantity',array('comp_id'=> $sval['id'],'is_obs_product'=> 1 ))->num_rows();
				$this->db->distinct();
				$this->db->select('fdd_pro_id');
				$used_pro2 = $this->db->get_where('fdd_pro_quantity',array('comp_id'=> $sval['id'],'is_obs_product'=> 0))->num_rows();
				$new_execute_array[ $skey ]['total_used_product'] = $used_pro1 + $used_pro2;
				/* total used product end*/

				/* total pws srart*/
				$count2 = 0; 
				$count3 = 0;
				$count4 = 0;
				$count5 = 0;

				$this->db->distinct( 'fdd_pro_quantity.fdd_pro_id' );
				$this->db->select( 'fdd_pro_quantity.fdd_pro_id' );
				$this->db->join( 'products', 'fdd_pro_quantity.fdd_pro_id = products.id' );
				$this->db->join( 'contacted_via_mail', 'contacted_via_mail.obs_pro_id = products.id' );
				$this->db->where( array( 'comp_id' => $sval['id'], 'is_obs_product' => '1' ) );
				$this->db->where( 'fdd_pro_quantity.is_shared', '0' );
				$data = $this->db->get('fdd_pro_quantity')->result_array();

				foreach ($data as $key => $value) {
					$this->db->where('fdd_pro_id', $value['fdd_pro_id']);
					$this->db->where( array( 'comp_id' => $sval['id'], 'is_obs_product' => '1' ) );
					$this->db->where( 'products_pending.prosheet_favourite', "");
					$this->db->join( 'products', 'fdd_pro_quantity.obs_pro_id = products.id' );
					$this->db->join( 'products_pending', 'products_pending.product_id = fdd_pro_quantity.fdd_pro_id AND products_pending.company_id = fdd_pro_quantity.comp_id');
					$this->db->where('recipe_version!=', '0');
					$query = $this->db->get('fdd_pro_quantity')->result_array();
					if(empty($query)){
						unset($data[$key]);
					}
				}
				$count1 = sizeof($data);

                $this->db->distinct( 'gs1_pid' );
				$this->db->select( 'gs1_pid' );
				$this->db->where(array('company_id'=>$sval['id'],'request_status'=>1,'is_mail_sent !=' => '1'));
				$gs1_pds = $this->db->get( 'request_gs1' )->result_array();
				
				if( !empty( $gs1_pds ) ) {
					$gs1_pds = array_column( $gs1_pds, 'gs1_pid' );
					$this->fdb->select( 'p_id' );
					$this->fdb->join( 'suppliers', 'suppliers.s_id = products.p_s_id' );
					$this->fdb->where_in( 'p_id', $gs1_pds );
					$this->fdb->where( 'approval_status', 0 );
					$result2 = $this->fdb->get( 'products' )->result_array();
					
					foreach ( $result2 as $key => $value ) {
						$this->db->distinct( 'obs_pro_id' );
						$this->db->select( 'products.id' );
						$this->db->join( 'products', 'fdd_pro_quantity.obs_pro_id = products.id' );
						$this->db->join( 'products_name', 'products_name.product_id = products.id' );
						$this->db->where( 'fdd_pro_quantity.fdd_pro_id', $value[ 'p_id' ] );
						$this->db->where( 'fdd_pro_quantity.is_shared', '0' );
						$this->db->where( 'products.company_id', $sval['id'] );
						$prod_details = $this->db->get( 'fdd_pro_quantity' )->result_array();

						if( empty( $prod_details ) ) {
							unset( $result2[ $key ] );
						}
					}
					$result2 = array_values( $result2 );
					$count2 = sizeof($result2);
				}

				$this->db->distinct( 'ps1_pid' );
				$this->db->select( 'ps1_pid' );
				$this->db->where(array('company_id'=>$sval['id'],'request_status'=>1,'is_mail_sent !=' => '1'));
				$ps1_pds = $this->db->get( 'request_ps1' )->result_array();

				if( !empty( $ps1_pds ) ) {
					$ps1_pds = array_column( $ps1_pds, 'ps1_pid' );

					$this->fdb->select( 'p_id' );
					$this->fdb->join( 'suppliers', 'suppliers.s_id = products.p_s_id' );
					$this->fdb->where_in( 'p_id', $ps1_pds );
					$this->fdb->where( 'approval_status', 0 );
					$result3 = $this->fdb->get( 'products' )->result_array();
					foreach ( $result3 as $key => $value ) {
						$this->db->distinct( 'obs_pro_id' );
						$this->db->select( 'products.id' );
						$this->db->join( 'products', 'fdd_pro_quantity.obs_pro_id = products.id' );
						$this->db->where( 'fdd_pro_quantity.fdd_pro_id', $value[ 'p_id' ] );
						$this->db->where( 'fdd_pro_quantity.is_shared', '0' );
						$this->db->where( 'products.company_id', $sval['id'] );
						$prod_details = $this->db->get( 'fdd_pro_quantity' )->result_array();

						if( empty( $prod_details ) ) {
							unset( $result3[ $key ] );
						}
					}
				
				$result3 = array_values( $result3 );
				$count3 = sizeof($result3);
                }

				$this->db->distinct( 'gs1_pid' );
				$this->db->select( 'gs1_pid' );
				$this->db->where(array('company_id'=>$sval['id'],'request_status'=>0,'is_mail_sent' => '1'));
				$gs1_pds1 = $this->db->get( 'request_gs1' )->result_array();
				if( !empty( $gs1_pds1 ) ) {
					$gs1_pds1 = array_column( $gs1_pds1, 'gs1_pid' );

					$this->fdb->select( 'p_id' );
					$this->fdb->join( 'suppliers', 'suppliers.s_id = products.p_s_id' );
					$this->fdb->where_in( 'p_id', $gs1_pds1 );
					$result4 = $this->fdb->get( 'products' )->result_array();
					foreach ( $result4 as $key => $value ) {
						$this->db->distinct( 'obs_pro_id' );
						$this->db->select( 'products.id, products.proname' );
						$this->db->join( 'products', 'fdd_pro_quantity.obs_pro_id = products.id' );
						$this->db->where( 'fdd_pro_quantity.fdd_pro_id', $value[ 'p_id' ] );
						$this->db->where( 'fdd_pro_quantity.is_shared', '0' );
						$this->db->where( 'products.company_id', $sval['id'] );
						$prod_details 	= $this->db->get( 'fdd_pro_quantity' )->result_array();
						if( empty( $prod_details ) ) {
							unset( $result4[ $key ] );
						}
					}
					$result4 = array_values( $result4 );
					$count4 = sizeof($result4);
				}

		        $this->db->distinct( 'ps1_pid' );
				$this->db->select( 'ps1_pid' );
				$this->db->where(array('company_id'=>$sval['id'],'request_status'=>0,'is_mail_sent' => '1'));
				$ps1_pds1 = $this->db->get( 'request_ps1' )->result_array();
				if( !empty( $ps1_pds1 ) ) {
					$ps1_pds1 = array_column( $ps1_pds1, 'ps1_pid' );

					$this->fdb->select( 'p_id' );
					$this->fdb->join( 'suppliers', 'suppliers.s_id = products.p_s_id' );
					$this->fdb->where_in( 'p_id', $ps1_pds1 );
					$result5 = $this->fdb->get( 'products' )->result_array();
					foreach ( $result5 as $key => $value ) {
						$this->db->distinct( 'obs_pro_id' );
						$this->db->select( 'products.id, products.proname' );
						$this->db->join( 'products', 'fdd_pro_quantity.obs_pro_id = products.id' );
						$this->db->where( 'fdd_pro_quantity.fdd_pro_id', $value[ 'p_id' ] );
						$this->db->where( 'fdd_pro_quantity.is_shared', '0' );
						$this->db->where( 'products.company_id', $sval['id'] );
						$prod_details 	= $this->db->get( 'fdd_pro_quantity' )->result_array();
						if( empty( $prod_details ) ) {
							unset( $result5[ $key ] );
						}
					}
					$result5 = array_values( $result5 );
					$count5 = sizeof($result5);
				}
				$new_execute_array[ $skey ]['total_pws'] = $count1+$count2+$count3+$count4+$count5;
				/* total pws end*/

				/* total refused product srart*/
				$this->db->distinct();
				$this->db->select('fdd_pro_id');
				$this->db->join( 'company', 'fdd_pro_quantity.comp_id = company.id' );
				$this->db->join('contacted_via_mail', 'fdd_pro_quantity.fdd_pro_id = contacted_via_mail.obs_pro_id');
				$this->db->join('products', 'products.id = fdd_pro_quantity.fdd_pro_id');
				$this->db->where( array('fdd_pro_quantity.is_obs_product'=> 1,'fixed'=> 1 ));
				$this->db->where( array('contacted_via_mail.refused'=>1));
				$this->db->where_in('company.ac_type_id', array('4','5','6'));
				$result = $this->db->get_where( 'fdd_pro_quantity',array('company.status'=>'1','fdd_pro_quantity.comp_id'=> $sval['id']) )->result_array();
				$result = array_column($result, 'fdd_pro_id');

				$this->db->distinct();
				$this->db->select('pws_products_sheets.obs_pro_id');
				$this->db->join( 'fdd_pro_quantity', 'fdd_pro_quantity.fdd_pro_id = pws_products_sheets.obs_pro_id' );
				$this->db->join( 'company', 'fdd_pro_quantity.comp_id = company.id' );
				$this->db->where_in('company.ac_type_id', array('4','5','6'));
				$all_pws_used = $this->db->get_where( 'pws_products_sheets', array( 'pws_products_sheets.checked' => '0','fdd_pro_quantity.is_obs_product'=>1,'company.status'=>'1','fdd_pro_quantity.comp_id'=> $sval['id'] ) )->result_array( );
				$all_pws_used = array_column($all_pws_used, 'obs_pro_id');
				$total_refused_product = count(array_values(array_unique(array_diff($result, $all_pws_used))));
				$new_execute_array[ $skey ]['total_refused_product']  = $total_refused_product+$count4+$count5;
		        /* total refused product end*/

				/* total treatment product start*/
				$total_fdd_product = array();
				$this->db->distinct();
				$this->db->select('fdd_pro_id');
				$total_fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('comp_id'=> $sval['id'],'is_obs_product'=> 0))->result_array();
				if(!empty($total_fdd_pro_id)){
					$this->fdb->select('p_id');
 					$this->fdb->where(array('product_type' => '0','approval_status' => 0));
 					$this->fdb->group_start();
					$total_fdd_pro_idss = array_chunk(array_column($total_fdd_pro_id, 'fdd_pro_id'), 100);
					foreach ($total_fdd_pro_idss as $key => $value) {
						$this->fdb->or_where_in('p_id',$value);
					}
					$this->fdb->group_end();
					$total_fdd_product = $this->fdb->get('products')->result_array();
				}
             
				$count_fdd_pro = sizeof($total_fdd_product);
				$new_execute_array[ $skey ]['treatment'] = $count2+$count3+$count_fdd_pro;
				/* total treatment product end*/
				}
			}  
		return $new_execute_array;
	}

	/**
	 * This function is used to count all companies
	 * @param array $where_array Array of conditions
	 */
	function get_company_count($where_array = array()){
		if (! empty ( $where_array )){
			foreach ( $where_array as $col => $val )
				$this->db->where ( $col, $val );
		}

		$companies = $this->db->get('company')->result_array();
		return count($companies);

	}

	function get_account_types($params = array()) {
		$this->db->flush_cache();
		if (! empty ( $params )){
			foreach ( $params as $col => $val )
				$this->db->where ( $col, $val );
		}

		$query = $this->db->get ( 'account_type' );

		if ($query->num_rows() >= 1) {
			return $query->result ();
		} else {
			return false;
		}
	}

	function get_empty_recipes_xls( $company_id )
	{
		$this->db->select('name,id');
		$this->db->where('company_id',$company_id);
		$cat = $this->db->get('categories');
		$cat_arr = $cat->result_array();

		foreach ($cat_arr as $key => $value) {
			$category[$value['id']] = $value['name'];
		}

		$prod = array();
		$prod_cat_without_recepi = array();
		$prod_without_recepi = array();

		foreach ($cat_arr as $key)
		{
			$this->db->select('proname,id,company_id,subcategories_id,categories_id');
			$this->db->where(array('categories_id'=>$key['id'],'company_id'=>$company_id,'direct_kcp'=>0));
			$prod[] = $this->db->get('products')->result_array();
		}

		foreach ($prod as $key => $value)
		{
			foreach ($value as $key1=>$value1)
			{
				$this->db->where('obs_pro_id',$value1['id']);
				$recipe_product = $this->db->get('fdd_pro_quantity')->result_array();
				if(empty($recipe_product))
				{
					$prod_without_recepi[] = $value1;
				}
			}
		}
		$products = array();
		$products = array('0'=>$category,'1'=>$prod_without_recepi);
		return $products;

	}
	
}

?>