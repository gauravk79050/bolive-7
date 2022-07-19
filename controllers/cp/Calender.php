<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calender extends CI_Controller{

	function __construct(){

		parent::__construct();
		$this->load->model('Mcalender');
	}

	function index(){
	}

	function get_option($date=NULL,$month=null,$year=null)
	{
	   $data['date']=date('d/m/Y',strtotime($date.' '.$month.' '.$year));
	   $this->load->view('cp/popup',$data);
	}

	function get_calender_holiday_close_dates(){//----lines to get holidays and close days----//

		$holiday_shop_closed_dates=$this->Mcalender->get_calender_holiday_shop_close_dates();
		echo $holiday_shop_closed_dates;
	}

	function get_pickup_delivery_closed(){//----lines to get holidays and close days----//
		$get_pickup_delivery_closed=$this->Mcalender->get_pickup_delivery_closed();
		echo json_encode($get_pickup_delivery_closed);
	}

	function set_closed(){
		$affected_rows=$this->Mcalender->set_closed_dates();
		if($affected_rows){
		  echo 'successsfully_updated';
		}else{
		  echo 'error_occured';
		}
	}
	function set_holiday(){
		$affected_rows=$this->Mcalender->set_holiday_dates();
		if($affected_rows){
			echo 'successsfully_updated';
		}else{
			echo 'error_occured';
		}
	}
}
?>