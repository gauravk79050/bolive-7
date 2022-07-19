<?php 
/**
 * Model- Morder_update
 * Updates the print status of orders
 * @author Surabhi Srivastava- Cedcoss Technologies
 */
class Morder_update extends CI_Model{

	var $company_id='';
	
	function __construct(){
	
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}
	
	/**
	 * Update the printed orders to set printed
	 */
	public function update_order($id=array())
	{
		foreach ($id as $ids)
		{
			$this->db->where('id',$ids->id);			
			$result = $this->db->update('orders', array('printed' =>'1'));			
		}
		return true;
	}
	
	/**
	 * Function to get id of not printed orders
	 */
	public function get_id()
	{
		$company = $this->company_id;
		//echo $company; die;
		$this->db->where(array('company_id'=> $company, 'printed'=>'0'));
		//$this->db->where();
		$query= $this->db->get('orders');
		return $query->result();
	}
}