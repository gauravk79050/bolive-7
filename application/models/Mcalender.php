<?php
class Mcalender extends CI_Model{

	var $comapny_id=null;

	function __construct(){

		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');

	}

//---function to get pickup and delivery closed weekdays and set them according to their classs---//


	function get_pickup_delivery_closed(){

		$pickup_delivery_closed=array();
		$this->db->select('pickup_delivery_timings.pickup1,pickup_delivery_timings.delivery1,pickup_delivery_timings.company_id,days.short_name');
		$this->db->from('pickup_delivery_timings');
		$this->db->join('days', 'pickup_delivery_timings.day_id = days.id');
		$this->db->where("(pickup1 ='CLOSED' OR delivery1 ='CLOSED')");
		$this->db->where(array('company_id'=>$this->company_id));
		$pick_delivery_closed = $this->db->get()->result();
		if($pick_delivery_closed){
			foreach($pick_delivery_closed as $weekday){
				if($weekday->pickup1=="CLOSED"&&$weekday->delivery1=="CLOSED"){
					$pickup_delivery_closed[$weekday->short_name]='BOth';
				}else if($weekday->pickup1=="CLOSED"&&$weekday->delivery1!="CLOSED"){
					$pickup_delivery_closed[$weekday->short_name]='pickup';
				}else if($weekday->pickup1!="CLOSED"&&$weekday->delivery1=="CLOSED"){
					$pickup_delivery_closed[$weekday->short_name]='delivery';
				}
			}
		}else{
		}

		return($pickup_delivery_closed);

	}


	function get_custom_pickup_closed() {
		$custom_pickup_closed = array();
		$this->db->select( 'custom_pickup_timing.pickup1, custom_pickup_timing.pickup_days' );
		$this->db->from( 'custom_pickup_timing' );
		$this->db->where( array( 'company_id' => $this->company_id ) );
		$custom_pickup_closed = $this->db->get()->result();
		if( $custom_pickup_closed ) {
			foreach ($custom_pickup_closed as $day) {
				$closed = explode( '#' , $day->pickup1);
				$date = explode( '#' , $day->pickup_days);

				$custom_pickup_closed = array();
				foreach ( $closed as $key=>$close ) {
					if ( $close == 'CLOSED' ) {
						$date_closed = $date[$key];
						$old_format = explode( '/', $date_closed);
						$new_format = $old_format[2].'-'.$old_format[1].'-'.$old_format[0] ;
						$d = date( 'D', strtotime($new_format) ) ;
						$a = $this->get_pickup_delivery_closed();
						$custom_pickup_closed[ $new_format ] = 'pickup';
						foreach ($a as $key => $value) {
							if( $key == $d ) {
								if( $value == 'delivery' ) {
									$custom_pickup_closed[ $new_format ] = 'BOth';
								}
								elseif ( $value == 'BOth' ) {
									$custom_pickup_closed[ $new_format ] = 'BOth';
								}
								elseif ( $value == 'pickup' ) {
									$custom_pickup_closed[ $new_format ] = 'pickup';
								}
								else {
									$custom_pickup_closed[ $new_format ] = 'pickup';
								}
							}
						}
					}
					else {
						$date_closed = $date[$key];
						$old_format = explode( '/', $date_closed);
						if(!is_null($old_format[0]) && $old_format[0] != ''){
							$new_format = $old_format[2].'-'.$old_format[1].'-'.$old_format[0] ;
							$d = date( 'D', strtotime($new_format) ) ;
							$a = $this->get_pickup_delivery_closed();
							$custom_pickup_closed[ $new_format ] = '';
							foreach ($a as $key => $value) {
								if( $key == $d ) {
									if( $value == 'delivery' ) {
										$custom_pickup_closed[ $new_format ] = 'delivery';
									}
									elseif ( $value == 'BOth' ) {
										$custom_pickup_closed[ $new_format ] = 'delivery';
									}
									elseif ( $value == 'pickup' ) {
										$custom_pickup_closed[ $new_format ] = '';
									}
									else {
										$custom_pickup_closed[ $new_format ] = '';
									}
								}
							}
						}
					}
				}
			}
		}
		return( $custom_pickup_closed );
	}


//-------------------------------to set the holiday that has been marked--------------------------------//
	function set_holiday_dates()
	{
		$holiday_input = $this->input->post('holiday_date');

		$hd = explode('/',$holiday_input);
		if(!empty($hd) && isset($hd[0]) && isset($hd[1]) && isset($hd[2])){
			if(strlen($hd[0]) == 1)
				$hd[0] = '0'.$hd[0];

			$data_array = array(
					'day' => $hd[0],
					'month' => $hd[1],
					'year' => $hd[2],
					'company_id' => $this->company_id
			);

			$this->db->where($data_array);
			$exist = $this->db->get('company_holidays')->result();
			if(!empty($exist)){
				$this->db->delete('company_holidays', array('id' => $exist[0]->id));
				return $this->db->affected_rows();
			}else{
				$data_array['timestamp'] = strtotime($hd[2].'-'.$hd[1].'-'.$hd[0]);
				$data_array['date_added'] = date('Y-m-d H:i:s');
				$this->db->insert('company_holidays', $data_array);
				return $this->db->affected_rows();
			}
		}
	}

//-------------------------------function to set the shop closed dates---------------------------------//
	function set_closed_dates(){

		$shop_close_input=$this->input->post('close_date');

		$hd = explode('/',$shop_close_input);
		if(!empty($hd) && isset($hd[0]) && isset($hd[1]) && isset($hd[2])){
			if(strlen($hd[0]) == 1)
				$hd[0] = '0'.$hd[0];

			$data_array = array(
					'day' => $hd[0],
					'month' => $hd[1],
					'year' => $hd[2],
					'company_id' => $this->company_id
			);
			$this->db->where($data_array);
			$exist = $this->db->get('company_closedays')->result();
			if(!empty($exist)){
				$this->db->delete('company_closedays', array('id' => $exist[0]->id));
				return $this->db->affected_rows();
			}else{
				$data_array['timestamp'] = strtotime($hd[2].'-'.$hd[1].'-'.$hd[0]);
				$data_array['date_added'] = date('Y-m-d H:i:s');
				$this->db->insert('company_closedays', $data_array);
				return $this->db->affected_rows();
			}
		}
	}

//-----------------------------------------------------------------------------------------------------//

//-function to get holiday dates from the table orders_settings & get_calender_shop_close_dates()-//
	function get_holiday_closed_dates(){

		$this->db->select('holiday_dates,shop_close_dates');
		$this->db->where(array('company_id'=>$this->company_id));
		$holiday_closed_dates=$this->db->get('order_settings');

		return($holiday_closed_dates->result());

	}

//------function to get holiday dates from the table orders_settings --------//

	function get_calender_holiday_shop_close_dates(){//----lines to get holidays and close days----//

		$holidays 			= array();
		$shop_close_days	= array();

		$year=$this->input->post('year');

		if($this->input->post('month') == 'Januari')
		  $month = 1;
		if($this->input->post('month') == 'Februari')
		  $month = 2;
		if($this->input->post('month') == 'Maart')
		  $month = 3;
		if($this->input->post('month') == 'April')
		  $month = 4;
		if($this->input->post('month') == 'Mei')
		  $month = 5;
		if($this->input->post('month') == 'Juni')
		  $month = 6;
		if($this->input->post('month') == 'Juli')
		  $month = 7;
		if($this->input->post('month') == 'Augustus')
		  $month = 8;
		if($this->input->post('month') == 'September')
		  $month = 9;
		if($this->input->post('month') == 'Oktober')
		  $month = 10;
		if($this->input->post('month') == 'November')
		  $month = 11;
		if($this->input->post('month') == 'December')
		  $month = 12;

		if(!$year&&!$month){
			$year	= date('Y',time());
			$month	= date('m',time());
		}

		$this->db->select('calendar_country');
		$country_selected = $this->db->get_where('general_settings',array('company_id'=>$this->company_id))->result_array();

		// Fetching Holiday dates
		$this->db->select('day');
		$this->db->where('month', $month);
		$this->db->where('year', $year);
		$this->db->where('company_id', $this->company_id);

		if(!empty($country_selected) && $country_selected['0']['calendar_country'] != ''){
			$this->db->where("( `calendar` = 'own' OR `calendar` = '".$country_selected['0']['calendar_country']."' )");
		}else{
			$this->db->where('calendar', 'own');
		}
		$holidays_dates = $this->db->get('company_holidays')->result_array();

		if( !empty( $holidays_dates ) ) {
			$holidays = array_column( $holidays_dates, 'day' );
		}

		// Fetching Closed dates
		$this->db->select('day');
		$this->db->where('month', $month);
		$this->db->where('year', $year);
		$this->db->where('company_id', $this->company_id);
		$shop_closed_dates = $this->db->get('company_closedays')->result();
		if(!empty($shop_closed_dates)){
			$shop_close_days = array_column( $shop_closed_dates, 'day' );
		}
		echo json_encode( array( 'holidays' => $holidays, 'shop_closed' => $shop_close_days ) );
	}

//-------------------------------------------------------------------------------------------//

	function get_pre_assigned_holidays($year,$month){
		$this->db->select('calendar_country');
		$country_selected = $this->db->get_where('general_settings',array('company_id'=>$this->company_id))->result_array();
		if($country_selected['0']['calendar_country'] != '')
			return $this->db->get_where($country_selected['0']['calendar_country'],array('year'=>$year,'month'=>$month))->result_array();
		else
			return array();
	}
}

?>