<?php 
class Mclients extends CI_Model{
	
	function __construct(){
		parent::__construct();
	
	}
	
	/**
	 * This model function is used to fetch all clients that are associated with the given company
	 * @param int $company_id company ID of the company
	 * @return array $clients return array of associated client ids
	 */
	function get_companies_client_ids($company_id=null){
		
		$clients_ids = array();
		
		if( $company_id && is_numeric($company_id) )
		{
			$this->db->select('client_id');
			
			$clients_ids = $this->db->get_where("client_numbers",array("company_id" => $company_id, 'associated' => '1') )->result_array();
				
		}
		
		return $clients_ids;	
	}
	
	/**
	 * Check whether client exist via email or something else which is given
	 * @param array $params array of fields on behalf of which client's existence will be checked
	 * @return array $client_info if empty then client not exist or we get info that client which exists 
	 */
	function check_client_exist($params=array())
	{    
		$client_info = array();
		
		if(!empty($params))
		foreach($params as $col=>$val){
			$this->db->where($col,$val);
		}
		
		$client_info = $this->db->get("clients")->result_array();
		
		return $client_info;
		
	}
	
	/**
	 * This function is used to insert new client's informations
	 * @param $insert_array an array containung the information of client
	 * @return int $client_id It is the ID of client that is inserted
	 */
	function insert_client( $insert_array = array() )
	{
		$client_id = '';
		
		if(!empty($insert_array)){
			
			if($this->db->insert("clients",$insert_array)){
				$client_id = $this->db->insert_id();
			}
			
		}
		
		return $client_id;
		
	}
	
	/**
	 * This model function is used to insert row in client_numbers table.. specially for associating client with company
	 */
	function insert_client_number( $insert_array = array() )
	{
		if(!empty($insert_array)){
			$this->db->insert("client_numbers",$insert_array);
		}
	}
	
}
?>