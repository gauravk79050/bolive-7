<?php
class Measybutler extends CI_Model
{ 
    
	function __construct()
     {
        parent::__construct();
	    
	    $this->lang_u = get_lang( $_COOKIE['locale'] );
     }

     /**
	 * Function to get the autocontrole calibration
	 * @access Public
	 */

	function get_all_easybutlerInfo(  ) {
		$this->db->order_by("id", "desc");
		return $this->db->get( 'easybutler_recommendations' )->result_array();
	}

		/**
	 * Function to insert and update the detail of specific temperature group.
	 * @access Public
	 * @param  array $data
	 */

	function update_easybutlerinfo( $data ) {
	
		if( $data[ 'action' ] == 'Add' ) {
			unset( $data[ 'action' ] );
			return $this->db->insert( 'easybutler_recommendations', $data );
		} else if( $data[ 'action' ] == 'Edit' ) {
			unset( $data[ 'action' ] );
			$this->db->where( 'id', $data[ 'id' ] );
			return $this->db->update( 'easybutler_recommendations', $data );
		} else {
			return false;
		}
	}


	/**
	 * Function to get the detail of specific work room
	 * @access Public
	 * @param  int $id
	 */

	function get_easybutlerinfo( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->get( 'easybutler_recommendations' )->row_array();
	}

	/**
	 * Function to delete the detail of specific workroom
	 * @access Public
	 * @param  int $id
	 */

	function delete_easybutlerinfo( $id ) {

		$this->db->where( 'id', $id );
		return $this->db->delete( 'easybutler_recommendations' );
	}

	

 }
?>