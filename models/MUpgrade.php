<?php 
class MUpgrade extends CI_Model{

	var $company_id;

	function __construct(){
	
		parent::__construct();
		
		$this->company_id = $this->session->userdata('cp_user_id');	
	}
	
	function add_upgrade_request( $ins_arr = array() )
	{
		$query = $this->db->insert('upgrade_requests', $ins_arr);
		$upgrade_request_id = $this->db->insert_id();
		
		return $upgrade_request_id;
	}
	
	function get_upgrade_request( $params = array() )
	{
		$this->db->select( 'upgrade_requests.*, account_type.id as at_id, account_type.ac_title, company.id as company_id, company.company_name, company.first_name, company.last_name, company.email, company.phone, company.city ' );
		
		if(!empty($params))
		  $this->db->where( $params );
		
		$this->db->join('company','upgrade_requests.company_id = company.id','left');
		$this->db->join('account_type','upgrade_requests.requested_ac_type_id = account_type.id','left');
		$upgrade_request = $this->db->get('upgrade_requests')->result();
		
		return $upgrade_request;
	}
	
	function approve_upgrade_request( $request_id = NULL )
	{
	    if( !$request_id )
		  return false;
		  
	    $this->db->where( 'id', $request_id );
		$upgrade_request = $this->db->get('upgrade_requests')->row();
		
		if( !empty( $upgrade_request ) )
		{
		   	$company_id  = $upgrade_request->company_id  ;
		   	$current_ac_type_id = $upgrade_request->current_ac_type_id ;
		   	$requested_ac_type_id = $upgrade_request->requested_ac_type_id ;
		   		   
		   	$upd_arr = array( 'ac_type_id'=>$requested_ac_type_id );
		   	if($current_ac_type_id == 2 || $current_ac_type_id == 3){
				$upd_arr['on_trial'] = "1";
		   		$upd_arr['trial'] = date("Y-m-d H:i:s",strtotime("+1 month", time()));
		   	}else{
		   		$upd_arr['on_trial'] = "0";
		   	}
		   	$this->db->where( array('id'=>$company_id, 'ac_type_id'=>$current_ac_type_id) );
		   	$update = $this->db->update('company', $upd_arr);
		   
		   	if( $update )
		   	{
		      	$updateArr = array( 'request_approved ' => 1, 'approved_on' => date('Y-m-d H:i:s',time()) );
		      	$this->db->where( 'id', $request_id );
		      	$updated = $this->db->update('upgrade_requests', $updateArr);
			  
			  	return true;
		   	}
		   	else
		     	return false;
		}
		
		return false;
	}
}
?>