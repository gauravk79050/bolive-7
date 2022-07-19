<?php
class Mflyers extends CI_Model
{ 
     
	function __construct()
    {
        parent::__construct();
    } 
	 
    /**
     * Function to get all flyers
     * @param string $select This string parameter is used to define columns names to be selected. (If not mentioned then all columns will be fetched)
     * @param array $param This is the array containing cols and values for WHERE clause
     * @return array $response This is the array containing desired rows
     */
	function get($select = "", $param = array())
	{
		$response = array();
		
		if($select != '')
			$this->db->select($select);
		
		if(!empty($param)){
			foreach ($param as $cols => $vals){
				$this->db->where($cols, $vals);
			}
		}
		
		/*if($limit)
			$this->db->limit($limit);*/
		
		$this->db->order_by('display_order', 'ASC');
		$response = $this->db->get('flyers')->result();
		
		return $response;
	}
	
	/**
	 * Function to insert flyers in DB
	 * @param array $insert_array an array of values to be insert
	 */
	function insert($insert_array = array()){
		if(!empty($insert_array)){
			$this->db->insert("flyers", $insert_array);
			return $this->db->insert_id();	
		}else{
			return 0;
		}	
			
	}
	
	/**
	 * Function to update flyers in DB
	 * @param array $where_array an array of columns and there values for WHERE Clause
	 * @param array $update_array an array of columns and there values to be updated
	 */
	function update($where_array = array(), $update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			foreach ($where_array as $cols => $vals)
				$this->db->where($cols, $vals);
			
			return $this->db->update("flyers", $update_array);
		}else{
			return 0;
		}	
	}
	
	/**
	 * Function to delete flyers from DB
	 * @param array $flyer_id ID of flyers to be deleted
	 */
	function delete($flyer_id = null){
		if($flyer_id){
			return $this->db->delete("flyers", array('id' => $flyer_id));
		}else{
			return 0;
		}
	}
}
?>