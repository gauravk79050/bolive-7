<?php 
class MAffiliates extends CI_Model {

    function __construct()
    {
        parent::__construct();
		
    }
	
	function add_affiliate( $params = array() )
	{
	    if(isset($params['add_affiliate']))
		  unset($params['add_affiliate']);
		
		$this->db->insert('affiliates', $params);
		$id = $this->db->insert_id();
		return $id;
	}
	
	function get_affiliates( $params = array(), $order_by = 'id', $order = 'desc', $start = 0, $limit = 0 )
	{
	    if(!empty($params))
		  $this->db->where( $params );
		
		if( $order_by && $order )
		  $this->db->order_by( $order_by, $order );
		
		if( $start && $limit )
		  $this->db->limit( $limit, $start );
		elseif( $limit )
		  $this->db->limit( $limit );
		
		$partners = $this->db->get('affiliates')->result();
		
		return $partners;
	}
	
	function delete_affiliate($params = array())
	{
	    if(!empty($params))
		  $this->db->where( $params );
		  
		$this->db->delete('affiliates'); 
		
		return true;
	}
	
	function update_affiliate( $affiliate_id, $params = array() )
	{
	    if( $affiliate_id )
		{
			if(isset($params['update_affiliate']))
		      unset($params['update_affiliate']);
			
			$this->db->where('id', $affiliate_id);
			$this->db->update('affiliates', $params); 
			
			return true;
        }
		else
		  return false;
	}
	
	function calc_affiliate_monthly_income( $affiliate_id, $companies_assigned = NULL)
	{
	    $income = array();
		$monthly_inc_amt = 0;
		$monthly_income = array('amount'=>0,'detail'=>array());
		
		if( empty($companies_assigned) )
		{
		    $this->db->where( 'affiliate_id', $affiliate_id );
			$companies_assigned = $this->db->get('company')->result();
		}
		
		foreach($companies_assigned as $c)
		{
		    $company_id = $c->id;
			
			$this->db->where( array( 'company_id' => $company_id, 'completed' => 1 ) );
			$company_orders = $this->db->get('orders')->result();
			
			$company_monthly_inc = 0;
			
			if(!empty($company_orders))
			{
			   
			   foreach($company_orders as $o)
			   {
			       $company_monthly_inc += $o->order_total;
			   }
			}
			
			$income[$company_id] = $company_monthly_inc;
			$monthly_inc_amt += $company_monthly_inc;
		}
		
		$monthly_income = array('amount'=>$monthly_inc_amt,'detail'=>$income);
		
		return $monthly_income;
	}
}
?>