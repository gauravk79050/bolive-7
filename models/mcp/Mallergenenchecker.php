<?php
/**
 * This is a model to interact with tabel "bestelonline"
 */
class Mallergenenchecker extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * This function is used to add banners into database
	 * @param array $insert_array it is the array of values to be inserted into database
	 * @return boolean true or false;
	 */
	function add($insert_array = array()) {
	
		if(!empty($insert_array)){
			if($this->db->insert("allergen_banners",$insert_array)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * This function is used to fetch all banners from db
	 * @param array $where it is the array of conditions to get from database
	 * @return array An array of banners
	 */
	function get() {
		$this->db->select( 'allergen_banners.*, company_type.company_type_name' );
		$this->db->join( 'company_type', 'company_type.id = allergen_banners.type_id' );
		$this->db->order_by("date", "DESC");
		$query = $this->db->get ( 'allergen_banners' );
		return $query->result ();
	}
	
	/**
	 * This function is used to make any banner active/inactive
	 * @param int $bannerId It is the ID of banner.
	 * @param int $status It is the status to be changes ( 0 or 1)
	 * @return boolean True or False
	 */
	function update($bannerId = null, $status = 0) {
		
		if($bannerId){
			$this->db->where("id" ,$bannerId );
			if($this->db->update("allergen_banners" , array("status" => $status) ))
				return true;
			else
				return false;
		}else{
			return false;
		}
		
	}
	
	/**
	 * this function is used to delete any banner
	 * @param $bannerId This is the Id of the banner
	 * @return boolean True or Fasle
	 */
	function delete($bannerId = null) {
		if($bannerId){
			$this->db->delete( "allergen_banners" , array("id" => $bannerId ) );
			return true;
		}else{
			return false;
		}
	}

	function get_company_type() {
		$this->db->select( 'type_id' );
		$query = $this->db->get ( 'allergen_banners' )->result_array();
		$query = array_column( $query, 'type_id' );

		$this->db->select( 'id, company_type_name' );
		if( !empty( $query ) ) {
			$this->db->where_not_in( 'id', $query );
		}
		return $this->db->get( 'company_type' )->result_array();
	}
}

?>