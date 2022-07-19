<?php 
class MCalendar extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
    }
    
    //-------------------------------to set the holiday that has been marked--------------------------------//
    function set_holiday_dates()
    {
    	$holiday_input=$this->input->post('holiday_date');
    	$country = $this->input->post('country');
    	if($holiday_input != ''){
    		$date = explode('/',$holiday_input);
    		 
    		$day = $date[0];
    		$month = $date[1];
    		$year = $date[2];
    		 
    		$data = array('day'=>$day,'month'=>$month,'year'=>$year);
    		$is_holiday = $this->db->get_where($country,$data)->row();
    		if( $is_holiday ){ //to check date exist in table if yes then delete it
    			$this->db->delete($country,$data);
    			
    			$data['calendar'] = $country;
    			$this->db->delete('company_holidays', $data);
    			
    			return true;
    		}else{ //Else add holiday
    			$data['holiday_mark_time'] = date("Y-m-d H:i:s");
   				$this->db->insert($country,$data);
   				
   				unset($data['holiday_mark_time']);
   				$this->db->select('company_id');
   				$company_ids = $this->db->get_where('general_settings', array('calendar_country' => $country))->result();

   				if(!empty($company_ids)){
   					foreach ($company_ids as $company_id){
   						$insert_data = array();
   						$insert_data = $data;
   						$insert_data['company_id'] = $company_id->company_id;
   						$insert_data['timestamp'] = strtotime($year.'-'.$month.'-'.$day);
   						$insert_data['date_added'] = date('Y-m-d H:i:s');
   						$insert_data['calendar'] = $country;
   						$this->db->insert('company_holidays', $insert_data);
   					}
   				}
    			return true;
    		}
    	}else{
    		return false;
    	}    	
    
    }
    
    //-----------------------------------------------------------------------------------------------------//
    
    //-function to get holiday dates from the table orders_settings & get_calender_shop_close_dates()-//
    function get_holiday_dates($month=null,$year=null,$country=null){
    
    	if($month){
    		if($month == 'Januari' || $month == 'January')
    			$m = 1;
    		if($month == 'Februari' || $month == 'February')
    			$m = 2;
    		if($month == 'Maart' || $month == 'March')
    			$m = 3;
    		if($month == 'April' || $month == 'April')
    			$m = 4;
    		if($month == 'Mei' || $month == 'May')
    			$m = 5;
    		if($month == 'Juni' || $month == 'June')
    			$m = 6;
    		if($month == 'Juli' || $month == 'July')
    			$m = 7;
    		if($month == 'Augustus' || $month == 'August')
    			$m = 8;
    		if($month == 'September')
    			$m = 9;
    		if($month == 'Oktober' || $month == 'Octuber')
    			$m = 10;
    		if($month == 'November')
    			$m = 11;
    		if($month == 'December')
    			$m = 12;

    		if($year && is_numeric($year) && $country){
    			$holidays = array();
    			$holiday_dates = $this->db->get_where($country,array('month'=>$m,'year'=>$year))->result_array();
    			if(!empty($holiday_dates)){
    				foreach($holiday_dates as $holiday_date){
    					$holidays[] = $holiday_date['day'];
    				}
    			}
    			return json_encode(array('holidays'=>$holidays));
    		}else{
    			return json_encode(array('error'=>''));
    		}
    	}else{
    		return json_encode(array('error'=>''));
    	}
    	
    }
    //----------------------------------------------------------------------------------------------//
	  
}

?>