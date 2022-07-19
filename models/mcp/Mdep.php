<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mdep
 * 
 * This is the model for Data Entry Partner
 * 
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
class Mdep extends CI_Model {

	/**
	 * This is the constructor that extends parent model's constructor
	 */
    function __construct()
    {
        parent::__construct();
		
    }
	
    /**
     * This function is used to add Data entry Partenrs
     * @param array $params This is the array of values that has to be inserted
     */
	public function add_dep( $params = array() )
	{
	    if(isset($params['add_dep']))
		  unset($params['add_dep']);
		
		$this->db->insert('dep_partners', $params);
		$id = $this->db->insert_id();
		return $id;
	}
	
	/**
	 * This public function is used to return data entry partners according to given parameters
	 * @param array $params It is the array of column => Fields, used to develop Where clause
	 * @param string $order_by It is the name of column for "Order By" Clause
	 * @param string $order It is the value for Sorting "Like: desc Or asc"
	 * @param int $start It is the start value for pagination
	 * @param int $limit It is the limit value for pagination
	 * @return array $dep_partners Return an array of Partenrs
	 */
	function get_dep( $params = array(), $order_by = 'id', $order = 'desc', $start = 0, $limit = 0 )
	{
	    if(!empty($params))
		  $this->db->where( $params );
		
		if( $order_by && $order )
		  $this->db->order_by( $order_by, $order );
		
		if( $start && $limit )
		  $this->db->limit( $limit, $start );
		elseif( $limit )
		  $this->db->limit( $limit );
		
		$dep_partners = $this->db->get('dep_partners')->result();
		
		return $dep_partners;
	}
	
	/**
	 * This model function is used to delete given DEP
	 * @param int $params This is the array used for where clause
	 */
	function delete_partner($params = array())
	{
	    if(!empty($params))
		  $this->db->where( $params );
		  
		$this->db->delete('dep_partners'); 
		
		return true;
	}
	
	/**
	 * This model function is used to update DEP's information
	 * @param int $partner_id This is the ID of DEP whose info has to be updated
	 * @param array $params This is the array of values that has to be updated. 
	 */
	function update_dep( $partner_id = null, $params = array() )
	{
	    if( $partner_id )
		{
			
			if(isset($params['update_dep']))
		      unset($params['update_dep']);
			
			$this->db->where('id', $partner_id);
			$this->db->update('dep_partners', $params); 
			
			return true;
        }
		else
		  return false;
	}
	
	
	/**
	 * This public function is used to return companies info added from Portal according to given parameters
	 * @param array $params It is the array of column => Fields, used to develop Where clause
	 * @param string $order_by It is the name of column for "Order By" Clause
	 * @param string $order It is the value for Sorting "Like: desc Or asc"
	 * @param int $start It is the start value for pagination
	 * @param int $limit It is the limit value for pagination
	 * @return array $dep_companies Return an array of Companies info
	 */
	function get_companies( $params = array(), $order_by = 'id', $order = 'desc', $start = 0, $limit = 0 )
	{
	    if(!empty($params))
		  $this->db->where( $params );
		
		if( $order_by && $order )
		  $this->db->order_by( $order_by, $order );
		
		if( $start && $limit )
		  $this->db->limit( $limit, $start );
		elseif( $limit )
		  $this->db->limit( $limit );
		
		$dep_companies = $this->db->get('dep_entry')->result();
		
		return $dep_companies;
	}
}
?>