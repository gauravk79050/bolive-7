<?php
class Morder_details extends CI_model{

	var $company_id='';
	function __construct(){
	
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}

	function get_order_details($orders_id,$cancelled = false){
		
		$order_details_table = (($cancelled)?'order_details_tmp':'order_details'); 
		
		//echo $orders_id;
		if($orders_id){
			$this->db->select($order_details_table.'.*,products.id as proid,products.company_id,products.categories_id,products.subcategories_id,products.pro_art_num,products.proname,products.price_per_unit,products.price_weight,products.type,products.discount');
			$this->db->join('products',$order_details_table.'.products_id=products.id');
			$this->db->where(array($order_details_table.'.orders_id'=>$orders_id));
			$order_details = $this->db->get($order_details_table)->result();
			
		
			if(!empty($order_details)){
				foreach($order_details as $key=>$val){
					if($order_details[$key]->discount == 'multi'){
						$discounts = $this->db->where(array('products_id'=>$order_details[$key]->products_id))->order_by('quantity','asc')->get('products_discount')->result();
						foreach($discounts as $discount){
							if($order_details[$key]->quantity >= $discount->quantity){
							
								$order_details[$key]->discount_per_qty = $discount->discount_per_qty;
								$order_details[$key]->price_per_qty = $discount->price_per_qty;
								$order_details[$key]->discount_on_items = $order_details[$key]->quantity - ($order_details[$key]->quantity % $discount->quantity);
							
							}else if($order_details[$key]->quantity == '1'){
							
								$order_details[$key]->discount_per_qty = 'No discount';
								$order_details[$key]->price_per_qty = ($order_details[$key]->content_type==1)?$order_details[$key]->price_weight:$order_details[$key]->price_per_unit;
							
							}//end of if
						}//end of foreach of discount
					}//end of if
					$this->load->model('Mgroups_products');
					$products_id = $order_details[$key]->products_id;
					$order_details[$key]->product_groups = $this->get_product_group($products_id);	
				}//end of foreach
			}//end of if
		}//end of if
		//print_r($order_details);
		return $order_details;
	}//end of function

	function insert_order_details($params){
	
		if( !empty( $params ) ){
			return ($this->db->insert('order_details',$params));
		}else{
			return 0;
		}
	
	}
	
	function update_order_details($id,$params){
	
		$this->db->where('id',$id);
		$result = $this->db->update('order_details',$params);
		
		if($result){
			return true;
		}else{
			return false;
		}	
	
	
	}
	
	function get_product_group($product_id){
		//echo $product_id;
		$groups = $this->db->select('id')->where('company_id',$this->company_id)->get('groups')->result();
		$this->db->select('groups.id as group_id,groups.group_name,groups.type,groups_products.*');
		$this->db->join('groups','groups.id=groups_products.groups_id','inner');
		$this->db->where('groups_products.products_id',$product_id);
		$groups_products = $this->db->get('groups_products')->result();
		if($groups_products){
			foreach($groups_products as $group_product){
			
				$attributes[$group_product->group_id][]=array('group_name'=>$group_product->group_name,
															  'attribute_name'=>$group_product->attribute_name,			
															  'attribute_value'=>$group_product->attribute_value,
															  'type'=>$group_product->type
				);
			
			
			}
		}else{
		
			$attributes='';
		
		}	
		//print_r($attributes);
		return $attributes;
		//die();
	
	}

	function get_single_order_detail($order_detail_id = null){
		return $this->db->get_where('order_details',array('id'=>$order_detail_id))->result();
	}
	
	function get_desk_order_details( $where = array() )
	{
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}
	
		$this->db->select( 'products.id AS product_id, desk_order_details.id AS ID, desk_order_details.*, products.*' );
		$this->db->join( 'products', 'products.id = desk_order_details.product_id' );
		$desk_order_details = $this->db->get('desk_order_details');
		return( $desk_order_details->result() );
	}
	
	function insert_desk_order_details( $insert_arr )
	{
		if( empty($insert_arr) )
			return false;
	
		$this->db->insert( 'desk_order_details', $insert_arr );
		return $this->db->insert_id();
	}
	
	function update_desk_order_details( $where = array(), $update = array() )
	{
		if( empty($update) )
			return false;
	
		if( !empty($where) )
		{
			foreach( $where as $key => $val ){
				$this->db->where( $key, $val );
			}

			return $this->db->update( 'desk_order_details', $update );
		}
	
		return false;
	}
	
	function delete_desk_order_details( $where = array() )
	{
		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}
	
		return $this->db->delete( 'desk_order_details' );
	}
	
}	
?>