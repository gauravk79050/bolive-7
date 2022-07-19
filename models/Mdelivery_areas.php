<?php 
class Mdelivery_areas extends CI_Model{
	var $company_id="";
	function __construct(){
	
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	}

	function get_delivery_areas($param=array()){
	
		if($param){
		
			foreach($param as $key=>$val){
			
				$this->db->where(array($key=>$val));
			
			}//end of foreach
		
		}else{
		
			$this->db->where(array('company_id'=>$this->company_id));
				
		}
	$query=$this->db->get('delivery_areas');
	return($query->result());
	}
	function update_delivery_areas(){
	
		if($this->input->post('area_name')&&$this->input->post('id')){
			$id=$this->input->post('id');
			$area_name=$this->input->post('area_name');
			//echo $id."   ".$area_name;
			
			$update_delivery_areas=array('area_name'=>$area_name);
			$this->db->where(array('id'=>$id));
		
		}else{
		$this->db->where(array('company_id'=>$this->company_id));
		}
		$this->db->update('delivery_areas',$update_delivery_areas);
	
	
	}
	
	function delete_delivery_areas(){
		
		$id=$this->input->post('id');
		$this->db->delete('delivery_areas',array('id'=>$id));
	
	}
	
	function insert_delivery_areas(){
		
		if($this->input->post('area_name')){
			$area_name=$this->input->post('area_name');
			$this->db->insert('delivery_areas',array('area_name'=>$area_name,'company_id'=>$this->company_id));
		
		}
			
	}
	function get_area_details(){
		$this->db->select('delivery_settings.*,delivery_areas.area_name,delivery_areas.company_id');
		$this->db->from('delivery_settings');
		$this->db->join('delivery_areas','delivery_settings.delivery_areas_id=delivery_areas.id','inner');
		$this->db->where(array('company_id'=>$this->company_id));
		$query=$this->db->get();
	  	return($query->result());
		
	}
	
	function get_states($country_id){
		return $this->db->get_where("states",array("country_id" => $country_id))->result_array();
	}
	
	function get_cities($params = array()){
		if(!empty($params)){
			foreach($params as $key=>$val){
					
				$this->db->where(array($key=>$val));
					
			}
			return $this->db->get("postcodes")->result_array();
		}
		
	}
	
	function get_delivery_area_settings(){
		return $this->db->get_where("company_delivery_settings",array("company_id" => $this->company_id))->result_array();
	}
	
	function update_delivery_settings(){
		$arr = array();
		//print_r($this->input->post()); die;
		$arr['delivery_status'] = $this->input->post("delivery_status");
		$arr['type'] = $this->input->post("type");
		$arr['current_delivery_charge'] = $this->input->post("current_delivery_charge");
		$arr['charge_km'] = $this->input->post("charge_km");
		$arr['charge_fixed'] = $this->input->post("charge_fixed");
		$arr['time_range'] = $this->input->post("time_range");
		$arr['min_amount_delivery'] = $this->input->post("min_amount_delivery");
		$arr['min_amount_delivery_int'] = $this->input->post("min_amount_delivery_int");
		
		$value_exist = $this->db->get_where("company_delivery_settings",array("company_id" => $this->company_id))->result_array();
		if(!empty($value_exist)){
			$this->db->where(array("company_id" => $this->company_id));
			$this->db->update("company_delivery_settings",$arr);
		}else{
			$arr['company_id'] = $this->company_id;
			$this->db->insert("company_delivery_settings",$arr);
		}
		
		// For International MOD
		if($arr['type'] == 'international'){
			$this->db->delete('company_countries', array('company_id' => $this->company_id));
			$country_ids = $this->input->post('country_selected');
			$country_costs = $this->input->post('country_cost');
			if(!empty($country_ids)){
				foreach ($country_ids as $key => $country_id){
					$insert_array = array(
							'company_id' => $this->company_id,
							'country_id' => $country_id,
							'country_cost' => isset($country_costs[$key])?$country_costs[$key]:0,
							'date' => date('Y-m-d H:i:s')
					);
					
					$this->db->insert('company_countries', $insert_array);
				}
			}
		}
	}
	
	/**
	 * This model function is used to get all countries
	 * @return array $countries An array of all countries
	 */
	function get_all_countries(){
		$this->db->order_by('country_name','ASC');
		$countries = $this->db->get('country')->result();
		return $countries;
	}
	
	/**
	 * This model function returns all countries selected by company for delivery
	 * @param Int $company_id ID of company
	 * @return mixed $countries Array of countries 
	 */
	function get_companies_countries($company_id = 0 ){
		$countries = array();
		if($company_id){
			$this->db->select('country.country_name, company_countries.country_id, company_countries.country_cost');
			$this->db->join('country', 'country.id = company_countries.country_id');
			$countries = $this->db->get_where('company_countries', array('company_id' => $company_id))->result();
		}
		return $countries;
	}
	/**
	 * This model function insert delivery rate according to criteria for countries selected by company for international delivery
	 */
	function int_del_add(){
		$response = false;
		$this->db->delete('company_countries_int', array('company_id' => $this->company_id));
		$rate_arr = $this->input->post('rate_arr');
		if($rate_arr){
			$arr = array();
			for($i = 0; $i<count($rate_arr); $i++){
				$add_del_rate[$i] = json_decode(urldecode($rate_arr[$i]));
				$insert_array = array(
					'company_id' => $this->company_id,
					'country_id' => $add_del_rate[$i]->rate_country_id,
					'rate_name' => $add_del_rate[$i]->rate_name,
					'criteria' => $add_del_rate[$i]->criteria,
					'lower_range' => $add_del_rate[$i]->lower_range,
					'upper_range' => $add_del_rate[$i]->upper_range,
					'rate_cost' => $add_del_rate[$i]->rate_cost,
					'date' => date('Y-m-d H:i:s')
				);				
				$this->db->insert('company_countries_int', $insert_array);
				$response = $this->db->affected_rows();
			}
		//return $response;
		}
	}
	/**
	 * This model function returns all countries selected by company for delivery
	 * @param Int $company_id ID of company
	 * @return mixed $countries Array of countries
	 */
	function get_companies_countries_int($company_id = 0 ){
		$countries = array();
		if($company_id){
			$this->db->select('country.country_name, company_countries_int.country_id, company_countries_int.rate_name, company_countries_int.id, company_countries_int.criteria, company_countries_int.lower_range, company_countries_int.upper_range, company_countries_int.rate_cost');
			$this->db->join('country', 'country.id = company_countries_int.country_id');
			$this->db->order_by('company_countries_int.lower_range','ASC');
			$countries = $this->db->get_where('company_countries_int', array('company_id' => $company_id))->result();
		}
		return $countries;
	}
}
