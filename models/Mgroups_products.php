<?php 
class Mgroups_products extends CI_Model{
	
	function __construct(){
		parent::__construct();		
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
	
	function get_groups_products($params=array()){

		$this->db->select('groups_products.*');
		$this->db->from('groups_products');
		$this->db->join('groups','groups_products.groups_id = groups.id');
		if($params){
			foreach($params as $key=>$val){
				$this->db->where(array('groups_products.'.$key=>$val));
			}
		}
		$this->db->order_by('groups.display_order asc');
		$this->db->order_by('groups_products.display_order asc');
		$query=$this->db->get();
		
		/*if(!empty($params)){
			foreach($params as $key=>$val){
				$this->db->where($key, $val);
			}
		}
		$query=$this->db->get('groups_products');*/
		
		return $query->result();
	}
	
	function get_groups_ofproduct($prod_id=null,$group_id=null){
		if($prod_id&&$group_id){
			
			$this->db->where('products_id',$prod_id);
			$this->db->where('groups_id',$group_id);
		}else if($prod_id){
			$this->db->where('products_id',$prod_id);
		}
		$query=$this->db->get('groups_products');
		
		return $query->result();
	}
	
	function add_update_groups_products($products_id=null){
		$prod_id = $this->input->post('prod_id');
		/*=====delete all the rows of groups====*/
		$this->db->delete('groups_products',array('products_id'=>$prod_id));
		/*==========================================*/
		
		//---------------------------------------------------------------------------------------------//
		
		// For Unit Groups
		if( $this->input->post('sell_product_option') == 'per_unit' || $this->input->post('sell_product_option') == 'client_may_choose' ){
	
			$total_groups = $this->input->post('count_groups');
			if($total_groups == '0' && ($this->input->post('count_row_per_group0') == '0') && ($this->input->post('att_text_0')[0] == '')){
				//this condition is cheked if no group is selected
				if($this->input->post('watt_text_0')[0]==""){
					return;
				}
			}else{
				if($this->input->post('prod_id')){//for updation of product
			
					for($group_no = 0;$group_no<=$total_groups;$group_no++){
						$groups_id = $this->input->post('m_select'.$group_no);
						$multi_select = $this->input->post('ms'.$group_no);
						$required = $this->input->post('required_'.$group_no);
						
						$attributes_txt = $this->input->post('att_text_'.$group_no);
						$attributes_values = $this->input->post('att_price_'.$group_no);

						$count_row_per_group = $this->input->post('count_row_per_group'.$group_no);
						if ($groups_id != '0')
						{
							for($row_no = 0;$row_no <= $count_row_per_group;$row_no++){
								/*$attribute_name = $this->input->post('att_text_'.$row_no.'_'.$group_no);
								$attribute_value = $this->input->post('att_price_'.$row_no.'_'.$group_no);*/
								
								$attribute_name = $attributes_txt[$row_no];
								$attribute_value = $attributes_values[$row_no];
								$attribute_value = $this->number2db( $attribute_value );
								$display_order = $row_no+1;
								if ($attribute_name != '')
								{
									$query = $this->db->insert('groups_products',array('id'=>'','products_id'=>$prod_id,'groups_id'=>$groups_id,'attribute_name'=>$attribute_name,'attribute_value'=>$attribute_value,'multiselect'=>$multi_select,'type'=>0,'display_order' => $display_order, 'required' => $required));
								}
							}
						}
					}
				}else{//for addition of product
						
					for($group_no=0;$group_no<=$total_groups;$group_no++){
						$groups_id=$this->input->post('m_select'.$group_no);
						$multi_select = $this->input->post('ms'.$group_no);
						$required = $this->input->post('required_'.$group_no);
						
						$attributes_txt = $this->input->post('att_text_'.$group_no);
						$attributes_values = $this->input->post('att_price_'.$group_no);
						
						$count_row_per_group=$this->input->post('count_row_per_group'.$group_no);
						for($row_no = 0;$row_no <= $count_row_per_group;$row_no++){
							$attribute_name = $attributes_txt[$row_no];
							$attribute_value = $attributes_values[$row_no];
							$attribute_value = $this->number2db( $attribute_value );
							$display_order = $row_no+1;
							
							if($products_id){
								$query=$this->db->insert('groups_products',array('id'=>'','products_id'=>$products_id,'groups_id'=>$groups_id,'attribute_name'=>$attribute_name,'attribute_value'=>$attribute_value,'multiselect'=>$multi_select,'type'=>0,'display_order' => $display_order, 'required' => $required));
							}
						}
					}
						
				}
			}
				
		}
				
		// For Weight Groups
		
		if( $this->input->post('sell_product_option') == 'weight_wise' || $this->input->post('sell_product_option') == 'client_may_choose' )
		{
		
			$total_groups = $this->input->post('count_wt_groups');
		
			if($total_groups == '0' && ($this->input->post('count_row_per_wt_group0') == '0') && ($this->input->post('watt_text_0')[0] == '')){
				//this condition is cheked if no group is selected
				return;	
			}else{
				if($this->input->post('prod_id')){//for updation of product
					
					for($group_no = 0;$group_no<=$total_groups;$group_no++){
						$groups_id = $this->input->post('wm_select'.$group_no);
						$multi_select = $this->input->post('ms_wt'.$group_no);
						$required = $this->input->post('required_wt_'.$group_no);
						
						$attributes_txt = $this->input->post('watt_text_'.$group_no);
						$attributes_values = $this->input->post('watt_price_'.$group_no);
						
						$count_row_per_group = $this->input->post('count_row_per_wt_group'.$group_no);
						
						for($row_no = 0;$row_no <= $count_row_per_group;$row_no++){
							$attribute_name = $attributes_txt[$row_no];
							$attribute_value = $attributes_values[$row_no];
							$attribute_value = $this->number2db( $attribute_value );
							$display_order = $row_no+1;
							$insert_array = array('id'=>'','products_id'=>$prod_id,'groups_id'=>$groups_id,'attribute_name'=>$attribute_name,'attribute_value'=>$attribute_value,'multiselect'=>$multi_select,'type'=>1, 'display_order' => $display_order, 'required' => $required);

							$query=$this->db->insert('groups_products',$insert_array);
						}
					}

				}else{//for addition of product
					echo "here??";
					for($group_no=0;$group_no<=$total_groups;$group_no++){
						$groups_id=$this->input->post('wm_select'.$group_no);
						$multi_select = $this->input->post('ms_wt'.$group_no);
						$required = $this->input->post('required_wt_'.$group_no);
						
						$attributes_txt = $this->input->post('watt_text_'.$group_no);
						$attributes_values = $this->input->post('watt_price_'.$group_no);
						
						$count_row_per_group=$this->input->post('count_row_per_wt_group'.$group_no);
						for($row_no = 0;$row_no <= $count_row_per_group;$row_no++){
							$attribute_name = $attributes_txt[$row_no];
							$attribute_value = $attributes_values[$row_no];
							$attribute_value = $this->number2db( $attribute_value );
							$display_order = $row_no+1;
							
							if($products_id){
								$query=$this->db->insert('groups_products',array('id'=>'','products_id'=>$products_id,'groups_id'=>$groups_id,'attribute_name'=>$attribute_name,'attribute_value'=>$attribute_value,'multiselect'=>$multi_select,'type'=>1, 'display_order' => $display_order, 'required' => $required));
								//echo $this->db->last_query();die;
							}
						}
					}
				
				}	
			}
		
		}
		
		// For Person Groups
		
		if( $this->input->post('sell_product_option') == 'per_person' || $this->input->post('sell_product_option') == 'client_may_choose' )
		{
		
			$total_groups=$this->input->post('count_person_groups');
			if($total_groups == '0' && ($this->input->post('count_row_per_person_group0') == '0') && ($this->input->post('att_person_text_0')[0] == '')){
				//this condition is cheked if no group is selected
				return;
			}else{
				if($this->input->post('prod_id')){//for updation of product
						
					for($group_no = 0;$group_no<=$total_groups;$group_no++){
						$groups_id = $this->input->post('m_person_select'.$group_no);
						$multi_select = $this->input->post('ms_p'.$group_no);
						$required = $this->input->post('required_p_'.$group_no);
						
						$attributes_txt = $this->input->post('att_person_text_'.$group_no);
						$attributes_values = $this->input->post('att_person_price_'.$group_no);
						
						$count_row_per_group = $this->input->post('count_row_per_person_group'.$group_no);
						for($row_no = 0;$row_no <= $count_row_per_group;$row_no++){
							$attribute_name = $attributes_txt[$row_no];
							$attribute_value = $attributes_values[$row_no];
							$attribute_value = $this->number2db( $attribute_value );
							$display_order = $row_no+1;
								
							$query=$this->db->insert('groups_products',array('id'=>'','products_id'=>$prod_id,'groups_id'=>$groups_id,'attribute_name'=>$attribute_name,'attribute_value'=>$attribute_value,'multiselect'=>$multi_select,'type'=>2,'display_order' => $display_order, 'required' => $required));
					
						}
					}
				}else{//for addition of product
			
					for($group_no=0;$group_no<=$total_groups;$group_no++){
						$groups_id=$this->input->post('m_person_select'.$group_no);
						$multi_select = $this->input->post('ms_p'.$group_no);
						$required = $this->input->post('required_p_'.$group_no);
						
						$attributes_txt = $this->input->post('att_person_text_'.$group_no);
						$attributes_values = $this->input->post('att_person_price_'.$group_no);
						
						$count_row_per_group=$this->input->post('count_row_per_person_group'.$group_no);
						for($row_no = 0;$row_no <= $count_row_per_group;$row_no++){
							$attribute_name = $attributes_txt[$row_no];
							$attribute_value = $attributes_values[$row_no];
							$attribute_value = $this->number2db( $attribute_value );
							$display_order = $row_no+1;
								
							if($products_id){
								$query=$this->db->insert('groups_products',array('id'=>'','products_id'=>$products_id,'groups_id'=>$groups_id,'attribute_name'=>$attribute_name,'attribute_value'=>$attribute_value,'multiselect'=>$multi_select,'type'=>2,'display_order' => $display_order, 'required' => $required));
							}
						}
					}
			
				}
			}
		}
		
		
				
	}//end of add_update function
	
	function insert_product_groups( $insert_arr = array() )
	{
		
		if( empty($insert_arr) )
		  return false;
		  
		$this->db->insert('groups_products', $insert_arr );
		return $this->db->insert_id();
	}
	
	function get_OBS_product_groups_to_list( $where = array() )
	{

		$this->db->select( 'DISTINCT(groups_id)' );
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}
		
		$product_groups = $this->db->get('groups_products')->result();
	
		if( !empty($product_groups) )
		{
			$group_row = array();
			
			foreach( $product_groups as $grp )
			{
				$group_id = $grp->groups_id;
	
				$this->db->where( 'id', $group_id );
				$group_arr = $this->db->get('groups')->row_array();
				 
				if( !empty($where) )
				{
					
					foreach( $where as $key => $val )
						$this->db->where( $key, $val );
				}
				
				$this->db->where( 'groups_id', $group_id );
				$this->db->order_by( 'id', 'ASC' );
				$option_arr = $this->db->get('groups_products')->result_array();
				
				if( !empty($group_arr) && !empty($option_arr) )
				{
					$group_row[$group_id] = $group_arr;
					$group_row[$group_id]['option_arr'] = $option_arr;
				}
			}
			 
			return $group_row;
		}
		else
			return false;
	}
	
	function update_selected_group($update_array = array(), $id = NULL) {
	
		$update_array ['products_id'] = $id;
		$result = $this->db->insert ( 'groups_products', $update_array );
		return $result;
	}
	
	
	function delete_selected_group($id = NULL)
	{
		$this->db->where ( 'products_id', $id );
		$result = $this->db->delete ( 'groups_products' );
		return $result;
	}
	
}	
?>