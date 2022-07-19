<?php 
class Mpayment extends CI_Model{

	function __construct(){
	
		parent::__construct();
	
	}
	
	function store_merchant_payment_info($response,$company_id){
		
		$data = array('company_id'=>$company_id,
						'curo_id'=>$response['id'],
						'merchant'=>$response['merchant'],
						'secret'=>$response['secret'],
						'username'=>$response['username'],
						'password'=>$response['password'],
						'site_id'=>$response['sites'][0]['id'],
						'site_name'=>$response['sites'][0]['name'],
						'site_url'=>$response['sites'][0]['url'],
						'site_hash_key'=>$response['sites'][0]['hash_key']
				);
		
		$this->db->insert('cp_merchant_info',$data);
	}

	
	function get_merchant_info($company_id){
		
		$query   = $this->db->get_where('cp_merchant_info', array('company_id' => $company_id));
		$result	 = $query->result_array();
		return $result;
		
	}
	
	function get_available_payment_methods($all=null){
	
		if(!empty($all)){
				
			$query = $this->db->get_where('available_payment_method');
		}else{
			$query = $this->db->get_where('available_payment_method',array('available'=>1));
		}
	
		$result = $query->result_array();
	//	echo "<check <pre>";print_r($result);die("//");
		return $result;
	
	}
	
	function update_selected_payment_methods($selected,$company_id){
		
		$update_data = array('selected_payment_method'=>implode(',',$selected));
		$this->db->where('company_id', $company_id);
		$this->db->update('cp_cardgate_settings', $update_data);
	}
	
	function insert_payment_method($selected,$company_id){
		
		/* $available_methods = $this->Mpayment->get_available_payment_methods();
		foreach($available_methods as $method){
			$avail_ids[] = $method['id'];
		} */
		$data = array(
				'company_id' => $company_id,
				'selected_payment_method' => implode(',',$selected)
		);
		$this->db->insert('cp_cardgate_settings', $data);
		
	}
	
	function check_exsistence($company_id){
		
		$this->db->where('company_id', $company_id);
		$this->db->from('cp_cardgate_settings');
		return $this->db->count_all_results();
	}
	
	function get_selected_payment_methods($company_id){
		
		$this->db->select('selected_payment_method');
		$this->db->where('company_id',$company_id);
		$row = $this->db->get('cp_cardgate_settings')->row();
		return explode(',',$row->selected_payment_method);
	}

	function get_payment_method_info($method_id){
		
		$query = $this->db->get_where('available_payment_method',array('id'=>$method_id,'available' => '1'));
		$result = $query->result_array();
		return $result;
	}
	
	function save_transaction($data){
		$this->db->insert('payment_transaction', $data);
	}
	
	function update_order_info($transaction_id,$payment_status,$order_id){
		$update_data = array('transaction_id'=>$transaction_id,'payment_status'=>$payment_status);
		$this->db->where('id', $order_id);
		$this->db->update('orders_tmp', $update_data);
	}
	
	function save_transition_info($data){
	
		$this->db->insert('payment_transition', $data);
		
	}
	
	function get_transition_info($ref){
		$query = $this->db->get_where('payment_transition',array('ref'=>$ref));
		$result = $query->result_array();
	//	echo $this->db->last_query();
	//	$this->db->delete('payment_transition', array('user_email' => $customer_email)); 
		return $result;
	}
	
	/**
	 * Function to update Merchant Info
	 * @param array $where_array Array of cols and value for WHERE clause
	 * @param array $update_array Array of cols and value to UPDATE
	 */
	function update_merchant_info($where_array = array(), $update_array = array()){
		if(!empty($where_array)){
			$this->db->where($where_array);
			$this->db->update('cp_merchant_info', $update_array);
		}
	}
	
	/**
	 * Function to get cardgate payment settings
	 * @param array $where_array Array of cols and values used in WHERE clause
	 */
	function get_cardgate_setting($where_array = array(), $select = ''){
		$response = array();
		
		if($select != '')
			$this->db->select($select);
		
		if(!empty($where_array)){
			$this->db->where($where_array);
		}
		
		$response = $this->db->get('cp_cardgate_settings')->result();
		
		return $response;
	}
	
	/**
	 * Function to update Cardgate Settings
	 */
	function update_cardgate_settings($where_array = array(), $update_array = array()){
		
		if(!empty($where_array)){
			$this->db->where($where_array);
			$this->db->update('cp_cardgate_settings', $update_array);
			$afftectedRows=$this->db->affected_rows();
			if (!$afftectedRows) {
				$update_array['company_id'] = $where_array['company_id'];
				$this->db->insert('cp_cardgate_settings', $update_array);
			}
			return;
		}
	}
}


?>