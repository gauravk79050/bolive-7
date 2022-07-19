<?php 
class Calender extends CI_Controller{
	function __construct(){
	
		parent::__construct();
	
	}

	function index(){
	
		//$this->load->Model('Mcalender');
		//$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();
		//print_r($data['pickup_closed']); 
		//$this->load->view('cp/calender',$data);
	
	}
	
	function get_option($date=NULL,$month=null,$year=null)
	{
	$date = trim( $date, “\xC2\xA0\n” );
	 echo ($date.'-'.$month.'-'.$year);  
	  
	   $data['date']=date('d/m/Y',strtotime($date.' '.$month.' '.$year));
	 $this->load->view('cp/popup',$data);
		
	} 
	function get_calender_holiday_close_dates(){//----lines to get holidays and close days----//
		$this->load->model('Mcalender');
		$holiday_shop_closed_dates=$this->Mcalender->get_calender_holiday_shop_close_dates();
		echo	$holiday_shop_closed_dates;
	}
	
	/*
	function  get_calender_shop_close_dates(){
		$this->load->model('Mcalender');
		$shop_close_dates=$this->Mcalender->get_calender_shop_close_dates();
		echo  $shop_close_dates;
	}	*/

	function set_closed(){
		$this->load->model('Mcalender');
		$affected_rows=$this->Mcalender->set_closed_dates();
		//echo $this->input->post('close_date');
		
		if($affected_rows){
					echo 'successsfully_updated';
		}else{
				
					echo 'error_occured';
		}
	}
	function set_holiday(){
		$this->load->model('Mcalender');
		$affected_rows=$this->Mcalender->set_holiday_dates();
		if($affected_rows){
			echo 'successsfully_updated';
		}else{
			echo 'error_occured';
		}
	}
}

?>