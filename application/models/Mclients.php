<?php 
class Mclients extends CI_Model{
	
	var $company_id=NULL;
	function __construct(){
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}
	function get_clients($params=array(),$limit=null,$offset=null){
		
		if($limit){
			$this->db->limit($limit,$offset);
		}
		
		if(!empty($params))
		{
			$this->db->from('clients');
			
			foreach($params as $key=>$value){
				$this->db->where(array('clients.'.$key=>$value));
			}			
			
			$query=$this->db->get();			
			$result = $query->result();
			
			if(!empty($result))
			foreach($result as $client)
			{
			    $country_name = '';
				
				if(isset($client->country_id) && $client->country_id)
				{
				  $this->db->select('country_name');
				  $this->db->where('id',$client->country_id);
				  $country = $this->db->get('country')->row();
				  $country_name = $country->country_name;
				}
				
				$client->country_name = $country_name;  
				$clients[$client->id] = $client;  
			}
				
		}else{
		
			$this->db->where(array('company_id'=>$this->company_id));
			$query=$this->db->get('clients');
		    $clients = $query->result();
		}
		
		return $clients;	
	}
	
	function get_company_clients($company_id,$limit=NULL,$offset=NULL,$params=array(),$count = false)
	{    
		$clients = array();
		
		if($company_id)
		{
			$clients_associated_arr = array();
			$this->db->select('clients.*, country.country_name');
			$this->db->join('clients', 'clients.id = client_numbers.client_id');
			$this->db->join('country', 'country.id = clients.country_id', 'left');
			$this->db->where('client_numbers.company_id',$company_id);
			$this->db->where('client_numbers.associated','1');
			if(is_array($params)){
				if(!empty($params)){
					$this->db->where($params);
				}
			}
			elseif($params != ''){
				$this->db->where($params);
			}
			$this->db->order_by('clients.created_c','DESC');
			
			if($count)
				$clients = $this->db->count_all_results('client_numbers');
			else 
				$clients = $this->db->get('client_numbers')->result();
			//print_r($clients_associated_arr); die;   
		}
		
		return $clients;
	}
	
	function get_client_number( $client_id, $company_id=NULL )
	{
		 if(!$client_id)
	     return false;
		 
	   if(!$company_id)
	     $company_id = $this->company_id;
	   
	   $this->db->where( array('company_id'=>$company_id,'client_id'=>$client_id) );
	   $client_number_row = $this->db->get('client_numbers')->row();
		
	   if(!empty($client_number_row))
		 return $client_number_row;
	   else
	     return false;
	}
	
	function update_client_info($where_array = array(), $update_array = array())
	{
		if(!empty($where_array)){
			foreach ($where_array as $column => $value){
				$this->db->where( $column, $value );
			}
			
			$client_number_row = $this->db->get('client_numbers')->row();
			if(!empty($client_number_row))
			{
				//Update
			
				$this->db->where('id',$client_number_row->id);
				return $query_run = $this->db->update('client_numbers',$update_array);
			}
			else
			{
				//Insert
			
				$add_client_number  = array(
						'client_id' => $where_array['client_id'],
						'company_id' => $where_array['company_id']
				);
				
				foreach ($update_array as $cols => $vals ){
					$add_client_number[$cols] = $vals;
				}
				
				return $query = $this->db->insert('client_numbers', $add_client_number);
			}
		}else{
			return false;
		}
	}
	
	function get_company_clients_subscribed_for_newsletter($company_id,$limit=NULL,$offset=NULL)
	{    
		$clients = array();
		
		if($company_id)
		{
			$this->db->select('clients.*,country.country_name,client_numbers.newsletter');
			$this->db->join('clients', 'clients.id = client_numbers.client_id');
			$this->db->join('country', 'country.id = clients.country_id', 'left');
			$this->db->where('client_numbers.company_id',$company_id);
			$this->db->where('client_numbers.newsletter','subscribe');
			$this->db->where('client_numbers.associated','1');
			$this->db->order_by('clients.firstname_c','ASC');
			$clients = $this->db->get('client_numbers')->result();
		}
		
		return $clients;
	}

	function delete_clients(){
		$client_id=$this->input->post('id');
		
		/*=====delete orders relates to that client=====*/
		$orders_id = $this->db->select('id')->where(array('clients_id' =>$client_id))->get('orders')->result();
		if(!empty($orders_id)){
		
			foreach($orders_id as $order_id){
				$this->db->delete('order_details',array('orders_id' => $order_id->id));
				$this->db->delete('orders',array('id' => $order_id->id));
			}
		
		}
		/*============================================*/
		$this->db->where(array('id'=>$client_id));
		$this->db->delete('clients');
		//echo 'prerna';
		//die();
	}
	
	function remove_client(  )
	{
		$client_id = $this->input->post('id');
		
		if( $this->input->post('company_id') )
		  $company_id = $this->input->post('company_id');
		else
		  $company_id = $this->company_id;
		
		$this->db->where(array('client_id' =>$client_id,'company_id'=>$company_id));
		$this->db->delete('client_numbers');
		
		/*=====delete orders relates to that client=====*/
		$orders_id = $this->db->select('id')->where(array('clients_id' =>$client_id,'company_id'=>$company_id))->get('orders')->result();
		if(!empty($orders_id)){
		
			foreach($orders_id as $order_id){
				$this->db->delete('order_details',array('orders_id' => $order_id->id));
				$this->db->delete('orders',array('id' => $order_id->id));
			}
		
		}
	}
	
	function get_client_excel(){
	}
	
	
	function unsubscribe($verify_code)
	{
	    if($verify_code)
		{
			$this->db->where('verify_code',$verify_code);
			$query_run = $this->db->update('clients',array('notifications'=>'unsubscribe'));
			
			if($query_run)
			  return true;
			else
			  return false;		  
		}
		else
		  return false;
	}
		
	function add($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('clients', $insert_array))
				return $this->db->insert_id();
			else 
				return false;
		}else
			return false;
	}
	
	function add_clients_number($insert_array = array()){
		if(!empty($insert_array)){
			if($this->db->insert('client_numbers', $insert_array))
				return $this->db->insert_id();
			else
				return false;
		}else
			return false;
	}
}
?>