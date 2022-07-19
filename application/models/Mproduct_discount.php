<?php
class Mproduct_discount extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	function update_product_discount($product_id,$type = 0){
	
	    if($type == 0)
		{
			//----------first of all we need to delete all the rpws previously existing in products_discount table----------//
			$this->db->where(array('products_id'=>$product_id,'type'=>0));
			$this->db->delete('products_discount');
			//-------------------------------------------------------------------------------------------------------------//
		
			$iteration=$this->input->post('count_discount_row');
			for ( $i=0; $i < $iteration; $i++){
				if ($this->input->post('qty'.$i) != 0) {
					$this->db->insert('products_discount',array('id'=>'','products_id'=>$product_id,'quantity'=>$this->input->post('qty'.$i),'discount_per_qty'=>$this->input->post('dd'.$i),'price_per_qty'=>$this->input->post('dp'.$i),'type'=>0));
				}
			}
		
		}elseif($type == 2)
		{
			//----------first of all we need to delete all the rpws previously existing in products_discount table----------//
			$this->db->where(array('products_id'=>$product_id,'type'=>2));
			$this->db->delete('products_discount');
			//-------------------------------------------------------------------------------------------------------------//
		
			$iteration=$this->input->post('count_discount_row_person');
			for ( $i=0; $i < $iteration; $i++){
				if ($this->input->post('qtyp'.$i) != 0) {
					$this->db->insert('products_discount',array('id'=>'','products_id'=>$product_id,'quantity'=>$this->input->post('qtyp'.$i),'discount_per_qty'=>$this->input->post('ddp'.$i),'price_per_qty'=>$this->input->post('dp_p'.$i),'type'=>2));
				}
			}
		
		}
		elseif($type ==1)
		{
			//----------first of all we need to delete all the rpws previously existing in products_discount table----------//
			$this->db->where(array('products_id'=>$product_id,'type'=>1));
			$this->db->delete('products_discount');
			//-------------------------------------------------------------------------------------------------------------//
		
			$iteration=$this->input->post('count_discount_row_wt');
			for ( $i=0; $i < $iteration; $i++){
				if ($this->input->post('qtyw'.$i) != 0) {
					$this->db->insert('products_discount',array('id'=>'','products_id'=>$product_id,'quantity'=>$this->input->post('qtyw'.$i),'discount_per_qty'=>$this->input->post('ddw'.$i),'price_per_qty'=>$this->input->post('dpw'.$i),'type'=>1));
				}
			}

		}	
	}
	
	function add_product_discount($prod_id,$type=0)
	{
		if($type == 0)
		{
			$iteration=$this->input->post('count_discount_row');
				
			for ($i=0;$i<$iteration;$i++){
				if ($this->input->post('qty'.$i) != 0) {
					$this->db->insert('products_discount',array('id'=>'','products_id'=>$prod_id,'quantity'=>$this->input->post('qty'.$i),'discount_per_qty'=>$this->input->post('dd'.$i),'price_per_qty'=>$this->input->post('dp'.$i),'type'=>0));
				}
			}
		}
		elseif($type == 1)
		{
			$iteration=$this->input->post('count_discount_row_wt');
				
			for ($i=0;$i<$iteration;$i++){
				if ($this->input->post('qtyw'.$i) != 0) {
					$this->db->insert('products_discount',array('id'=>'','products_id'=>$prod_id,'quantity'=>$this->input->post('qtyw'.$i),'discount_per_qty'=>$this->input->post('ddw'.$i),'price_per_qty'=>$this->input->post('dpw'.$i),'type'=>1));
				}
			}

		}elseif($type == 2)
		{
			$iteration=$this->input->post('count_discount_row_person');
				
			for ($i=0;$i<$iteration;$i++){
				if ($this->input->post('qtyp'.$i) != 0) {
					$this->db->insert('products_discount',array('id'=>'','products_id'=>$prod_id,'quantity'=>$this->input->post('qtyp'.$i),'discount_per_qty'=>$this->input->post('ddp'.$i),'price_per_qty'=>$this->input->post('dp_p'.$i),'type'=>2));
				}
			}

		}
	}
	
	function get_product_discount($prod_id,$type=0)
	{	
	    $this->db->where('type',$type);
		$this->db->where('products_id',$prod_id);
		$query=$this->db->get('products_discount');
		return $query->result();
	}
	
	function get_product_multidiscount($product_id,$type,$qty,$order_by = '')
	{
		$data['discounted_price_per_piece'] = 0;
		$data['qty']=0;
		$this->db->where('type',$type);
		$this->db->where('products_id',$product_id);
		if($order_by == ''){
			$this->db->order_by('quantity','desc');
		}else{
			$this->db->order_by('quantity',$order_by);
		}
		$query=$this->db->get('products_discount');
		$result = $query->result_array();
		foreach($query->result_array() as $row){
			if($qty >= $row['quantity']){
				$data['discounted_price_per_piece'] = $row['price_per_qty'];
				$data['qty']=($qty % $row['quantity']);
				break;
			}
		}
		return $data;
	}
	
	function insert_product_discounts( $insert_arr = array() )
	{
		if( empty($insert_arr) )
		  return false;
		  
		$this->db->insert('products_discount', $insert_arr );
		return $this->db->insert_id();
	}	
}
?>