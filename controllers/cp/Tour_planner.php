<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tour Planner Script
 * 
 * This is a controller for getting optimized route of various address via google map api 
 * 
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 * @package Tour_planner
 */
class Tour_planner extends CI_Controller{
	
	var $company_id = '';
	
	function __construct(){
	
		parent::__construct();
		
		$this->load->helper('cookie');
		
		$this->load->library('session');
		
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mcalender');

		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}

		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
		
		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
	}
	
	/**
	 * This function is used to fetch and show all saved report on the server for current company
	 */
	function index(){
		$data['content'] = 'cp/tour_planner_view';
		$this->load->view('cp/cp_view',$data);
	}

	/**
	 * Function to find address required for making tour
	 */
	function find_address(){
		
		$response = array('error' => 1, 'message' => "Some error occured");
		
		$from_hr = $this->input->post('from_hr');
		$to_hr = $this->input->post('to_hr');
		$from_min = $this->input->post('from_min');
		$to_min = $this->input->post('to_min');
		
		if($from_hr > $to_hr || ($from_hr == $to_hr && $from_min > $to_min)){
			$response = array('error' => 1, 'message' => _("Time range is not valid") );
		}else{
			$this->load->model('Mcountry');
			$countries = $this->Mcountry->get_country();
			
			$country_arr = array();
			if(!empty($countries)){
				foreach ($countries as $country){
					$country_arr[$country->id] = $country->country_name; 
				}
			}
				
			$address['origin'] = $this->company->address.' '.$this->company->city.' '.$this->company->zipcode.' '.((isset($country_arr[$this->company->country_id]))?$country_arr[$this->company->country_id]:'');
			
			$this->load->model('Morders');
			/*$start_date = date("Y-m-d")." ".$from_hr.":".$from_min.":00";
			$end_date = date("Y-m-d")." ".$to_hr.":".$to_min.":00";*/
			
			$start_date = "2014-06-13 08:00:00";
			$end_date = "2014-07-15 09:00:00";
			
			$orders = $this->Morders->get_orders( $select=array(), null,$start_date,$end_date,null,null,array('order_pickupdate' => "0000-00-00"),$this->company_id);
			if(isset($orders['0'])){
				//$address['destination'] = $orders['0']->address_c.' '.$orders['0']->housenumber_c.' '.$orders['0']->city_c.' '.$orders['0']->postcode_c.' '.((isset($country_arr[$orders['0']->country_id]))?$country_arr[$orders['0']->country_id]:'');
				$address['destination'] = 'Mechelsesteenweg 80 Antwerpen BELGIE 2018';
				$location				= array(
					'Gielisheide 5 Meeeuwen BELGIE 3670',
					'Krakelaarsveld 24 Herentals BELGIE 2200',
					'Boerenkrijglaan 18 turnhout BELGIE 2300',
					'paalseweg 81 tessenderlo BELGIE 3980',
					'Winkelomseheide 90 Geel BELGIE 2440',
					'generaal de wittelaan 15 Mechelen BELGIE 2800',
					'Nieuwe erven 22 Turnhout 2300',
					'Langdonk 40 Breda BELGIE 4824',
					'nijverheidsstraat 50 A oevel BELGIE 2260',
					'Harten 13 Turnhout BELGIE 2300',
					'varkensmarkt 14 ramsel BELGIE 2230',
					'Oudenaardsesteenweg 168 Avelgem BELGIE 8580',
					'Lindestraat 40  Meerhout BELGIE 2450',
					'Bexstraat 19 Antwerpen BELGIE 2018',
					'Steenweg op Ravels 50 b 5 Oud Turnhout BELGIE 2360',
					'corsendonk 2 Oud-Turnhout BELGIE 2360'
				);
				unset($orders['0']);
				if(!empty($orders)){
					$waypoints = array();
					$key = 0;
					foreach ($orders as $order){
						if($key <9){
						//$waypoints[$key]['location'] = $order->address_c.' '.$order->housenumber_c.' '.$order->city_c.' '.$order->postcode_c.' '.((isset($country_arr[$order->country_id]))?$country_arr[$order->country_id]:'');
						$waypoints[$key]['location'] = $key<count($location)-1?$location[$key]:$location[count($location)-1];
						$waypoints[$key]['stopover'] = true;
						$key++;
						}
					}
					$address['waypoints'] = $waypoints;
				}
				$response = array('error' => 0, 'message' => '', 'data' => $address);
			}else{
				$response = array('error' => 1, 'message' => _("No orders found to plan a tour") );
			}
			
		}
		
		echo json_encode($response);
	}
}			

?>