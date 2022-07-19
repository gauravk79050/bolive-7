<?php 
class Mgroups_order extends CI_Model{
	var $company_id;

	function __construct(){
	
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}
	
	function update_groups_order(){
	
		$products_id=$this->input->post('prod_id');
		
		if($products_id){
			$this->db->where('products_id',$products_id);
			$this->db->delete('groups_order');
		}
		
		// For Per unit groups
		
		$total_groups=$this->input->post('count_groups');
		for($group_no=0;$group_no<=$total_groups;$group_no++){
			$groups_id=$this->input->post('m_select'.$group_no);
			$groups_array=array('company_id'=>$this->company_id,'products_id'=>$products_id,'group_id'=>$groups_id,'order_display'=>$group_no,'type'=>0);
			$this->db->insert('groups_order',$groups_array);
		}
		
		// For Person groups
		
		if( $this->input->post('sell_product_option') == 'per_person' || $this->input->post('sell_product_option') == 'client_may_choose' )
		{
			$total_groups=$this->input->post('count_person_groups');
			for($group_no=0;$group_no<=$total_groups;$group_no++){
				$groups_id=$this->input->post('m_person_select'.$group_no);
				$groups_array=array('company_id'=>$this->company_id,'products_id'=>$products_id,'group_id'=>$groups_id,'order_display'=>$group_no,'type'=>2);
				$this->db->insert('groups_order',$groups_array);
			}
		}
	    
		// For weight groups
	
	    if( $this->input->post('sell_product_option') == 'weight_wise' || $this->input->post('sell_product_option') == 'client_may_choose' )
		{
		    $total_groups=$this->input->post('count_wt_groups');
			for($group_no=0;$group_no<=$total_groups;$group_no++){
				$groups_id=$this->input->post('wm_select'.$group_no);
				$groups_array=array('company_id'=>$this->company_id,'products_id'=>$products_id,'group_id'=>$groups_id,'order_display'=>$group_no,'type'=>1);
				$this->db->insert('groups_order',$groups_array);
			}
		}
	}
	
	function add_groups_order($products_id){
		
		$total_groups=$this->input->post('count_groups');
		for($group_no=0;$group_no<=$total_groups;$group_no++){
			$groups_id=$this->input->post('m_select'.$group_no);
			$groups_array=array('company_id'=>$this->company_id,'products_id'=>$products_id,'group_id'=>$groups_id,'order_display'=>$group_no,'type'=>0);
			$this->db->insert('groups_order',$groups_array);
		}
		
		if( $this->input->post('sell_product_option') == 'per_person' || $this->input->post('sell_product_option') == 'client_may_choose' )
		{
		
			$total_groups=$this->input->post('count_person_groups');
			for($group_no=0;$group_no<=$total_groups;$group_no++){
				$groups_id=$this->input->post('m_person_select'.$group_no);
				$groups_array=array('company_id'=>$this->company_id,'products_id'=>$products_id,'group_id'=>$groups_id,'order_display'=>$group_no,'type'=>2);
				$this->db->insert('groups_order',$groups_array);
			}
		
		}
		
		if( $this->input->post('sell_product_option') == 'weight_wise' || $this->input->post('sell_product_option') == 'client_may_choose' )
		{
		
			$total_groups=$this->input->post('count_wt_groups');
			for($group_no=0;$group_no<=$total_groups;$group_no++){
				$groups_id=$this->input->post('wm_select'.$group_no);
				$groups_array=array('company_id'=>$this->company_id,'products_id'=>$products_id,'group_id'=>$groups_id,'order_display'=>$group_no,'type'=>1);
				$this->db->insert('groups_order',$groups_array);
			}	
		
		}
	}


}

?>