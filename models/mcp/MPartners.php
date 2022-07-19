<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MPartners extends CI_Model {

    function __construct()
    {
        parent::__construct();
		
    }
	
	function add_partner( $params = array() )
	{
	    if(isset($params['add_partner']))
		  unset($params['add_partner']);
		
		$this->db->insert('partners', $params);
		$id = $this->db->insert_id();
		return $id;
	}
	
	function get_partners( $params = array(), $order_by = 'id', $order = 'desc', $start = 0, $limit = 0 )
	{
	    if(!empty($params))
		  $this->db->where( $params );
		
		if( $order_by && $order )
		  $this->db->order_by( $order_by, $order );
		
		if( $start && $limit )
		  $this->db->limit( $limit, $start );
		elseif( $limit )
		  $this->db->limit( $limit );
		
		$partners = $this->db->get('partners')->result();
		
		return $partners;
	}

	/**
	 * Updated code for monthly income @rishabh
	 * match join conditions for company table to be similar with reseller controller's companies() function and monthly income calculated in both the functions should be same
	 * partner status is checked explicitly inside loop in reseller's company() function
	 * @param array $params
	 * @param string $order_by
	 * @param string $order
	 * @param number $start
	 * @param number $limit
	 * @return array of matched companies
	 */
	function get_partners_and_month_income( $params = array(), $order_by = 'id', $order = 'desc', $start = 0, $limit = 0 )
	{
	
		if(!empty($params))
			$this->db->where( $params );
	
		if( $order_by && $order )
			$this->db->order_by( $order_by, $order );
	
		if( $start && $limit )
			$this->db->limit( $limit, $start );
		elseif( $limit )
		$this->db->limit( $limit );
	
		//$this->db->select('partners.*,GROUP_CONCAT(DISTINCT company.partner_total_amount) AS partner_total_amt');
		$this->db->select('partners.*,GROUP_CONCAT(DISTINCT company.partner_total_commission) AS partner_total_amt');
		$this->db->group_by('partners.id');
		//$this->db->join('company','company.partner_id = partners.id AND invoice_end_date >= DATE(CURDATE()) AND company.approved = "1" AND company.partner_status = "1" AND company.status = "1"','left outer');
		$this->db->join('company','company.partner_id = partners.id AND invoice_end_date >= DATE(CURDATE()) AND company.status = "1"','left outer');
	
		$partners = $this->db->get('partners')->result();
	
		return $partners;
	}
	
	function delete_partner($params = array())
	{
	    if(!empty($params))
		  $this->db->where( $params );
		  
		$this->db->delete('partners'); 
		
		return true;
	}
	
	function update_partner( $partner_id, $params = array() )
	{
	    if( $partner_id )
		{
			
			if(isset($params['update_partner']))
		      unset($params['update_partner']);
			
			$this->db->where('id', $partner_id);
			$this->db->update('partners', $params); 

			return true;
        }
		else
		  return false;
	}
	
	function calc_partner_monthly_income( $partner_id, $companies_assigned = NULL)
	{
	    $income = array();
		$monthly_inc_amt = 0;
		$monthly_income = array('amount'=>0,'detail'=>array());
		
		if( empty($companies_assigned) )
		{
		    $search_partner_id = str_replace( $partner_id, "\"$partner_id\"", $partner_id );
			$companies_assigned = $this->db->query( "SELECT * FROM `company` WHERE `partner_id` LIKE '%". $this->db->escape_like_str($search_partner_id)."%'" )->result();
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

	function update_logo( $partner_id, $logo ) {
		$this->db->where( 'id', $partner_id );
		return $this->db->update( 'partners', array( 'p_logo_name' => $logo ) );
	}

	function get_rp_data( $rp_id )
	{
		return $this->db->get_where('partners',array( 'id' => $rp_id ))->row_array();
	}
}
?>