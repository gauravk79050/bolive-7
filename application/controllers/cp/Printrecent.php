<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Printrecent Controller
 * 
 * This is a controller to print all recent orders. The records get checked at a certain interval of time and if not 
 * printed record founds in the db, this controller prints those orders.
 * 
 * @package Controllers-CP-Printrecent
 * @author Surabhi Srivastava <surabhisrivastava@cedcoss.com>
 */
class Printrecent extends CI_Controller{
	
	var $rows_per_page = '';
	var $company_id = '';
	var $ibsoft_active = false;
	
	function __construct(){
	
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->helper('phpmailer');
		$this->load->helper('cookie');
		
		
		$this->load->library('ftp');
		$this->load->library('Messages');
		//$this->load->library('email');
		$this->load->library('session');
        $this->load->library('messages'); 
		$this->load->library('utilities');
		$this->load->library('pagination');	
		$this->load->library('form_validation');
		//$this->load->library('upload');
		
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mpackages');
		$this->load->model('Mcategories');
		$this->load->model('Mcalender');
		$this->load->model('Mclients');
		$this->load->model('mcp/Mcompanies');
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		$this->load->model('Mclients');
		$this->load->model('Morder_update');

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
	
		$this->rows_per_page = 20;
		
		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}
	
	/**
	 * Function to print recent records
	 */
	public function index()
	{
		$general_settings = $this->Mgeneral_settings->get_general_settings();
		$discount_number_array = array();
		$activate_discount_card = 0;
		if($general_settings['0']->activate_discount_card  != 0)
			$activate_discount_card = 1;
		$order_ids = $this->Morder_update->get_id();		
		
		$select = array(
					'orders.id',
					'clients.id AS client_id',
					'orders.created_date',
					'orders.delivery_cost',
					'orders.order_pickuptime',
					'orders.order_pickupdate',
					'orders.delivery_streer_address',
					'orders.delivery_zip',
					'orders.delivery_date',
					'orders.delivery_hour',
					'orders.delivery_minute',
					'orders.phone_reciever',
					'orders.delivery_day',
					'orders.delivery_remarks',
					'orders.order_pickupday',
					'orders.order_remarks',
					'orders.option',
					'clients.firstname_c',
					'clients.lastname_c',
					'clients.company_c',
					'clients.address_c',
					'clients.housenumber_c',
					'clients.postcode_c',
					'clients.phone_c',
					'clients.mobile_c',
					'clients.city_c',
					'clients.email_c',
					'country.country_name as country_name',
					'postcodes.area_name as delivery_city_name',
					'states.state_name as delivery_area_name'
		);

		foreach($order_ids as $id)
		{
			$orderData[] = $order_data = $this->Morders->get_orders( $select, $id->id );
			//print_r($orderData); die;
			if($activate_discount_card){
				$client_id = $order_data['0']->client_id;
				$client_info = $this->Mclients->get_clients(array('id'=>$client_id));
				foreach($client_info as $key=>$values)
					$discount_number_array[] = $values->discount_card_number;
			}
			$order_details_data[] = $this->Morder_details->get_order_details($id->id);
		}
		//print_r($orderData); die;
		$updated = $this->Morder_update->update_order($order_ids);
		$total=array();
		$TempExtracosts=array();
		if(isset($orderData) && !empty($orderData))
		{		
			for($i=0;$i<count($orderData);$i++){
			
				$total[$i]='';
				for($j = 0;$j < count($order_details_data[$i]); $j ++){
					if($order_details_data[$i][$j]->add_costs!=""){
						$rsExtracosts=explode('#',$order_details_data[$i][$j]->add_costs);
						for($k = 0; $k < count($rsExtracosts); $k++){
							$TempExtracosts[$i][$j][$k] = explode("_",$rsExtracosts[$k]);
						}
					}
					$total[$i] += $order_details_data[$i][$j]->total;
				}
			
			}
		$data['total']=$total;
		$data['TempExtracosts']=$TempExtracosts;
		
		$data['print_count'] = 'all';
		
		$data['orderData']=$orderData;
		$data['order_details_data']=$order_details_data;
		$data['activate_discount_card'] = $activate_discount_card;
		$data['discount_card_number'] = $discount_number_array;
		}
		$data['no_record'] = 'no record';
		$this->load->view('cp/recent_print',$data);
	}
	
	public function recent_orders(){
		$order_ids = $this->Morder_update->get_id();
		if(!empty($order_ids))
		{
			echo '1';
		}
		else 
		{
			echo '0';
		}
	}
}