<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Settings extends CI_Controller{

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
		$this->load->model('M_fooddesk');

		if($this->input->get('token')){
			$privateKey = 'o!s$_7e(g5xx_p(thsj*$y&27qxgfl(ifzn!a63_25*-s)*sv8';
			$userId = $this->input->get('name');
			$hash = $this->createToken($privateKey, current_url(), $userId);

			if($this->input->get('token') == $hash){
				$this->product_validate($userId);
			}
		}

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

		/*if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}*/
		$this->load->model('MFtp_settings');
		$this->rows_per_page = 20;

		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}

	function index()
	{

		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();
		$data[ 'custom_pickup_closed' ] = $this->Mcalender->get_custom_pickup_closed();

		/*
		*after submitting setting if intro hide has been changed then this will set the session variable accordingly
		*and intro setion will be shown accordingly
		*/

		//--these loc  are for the form which gets submitted in settings.php--//
		if($this->input->post('btn_update'))
		{ 
			$this->session->set_userdata('action', 'general_setting_json');

			$this->messages->clear();//clears all messages

			if($this->input->post('act')=="edit_theme_settings"){

				$result = $this->Mgeneral_settings->update_theme();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}
				redirect('cp/cdashboard/section_designs');
			}
		}

		if($this->input->post('act')=="edit_holiday_settings"){
			$this->load->model('Morder_settings');
			$update_order_settings=array();
			if($this->input->post('holiday')=="open"){
				$update_order_settings['holiday_timings']=$this->input->post('h1').",".$this->input->post('h2').",".$this->input->post('h3').",".$this->input->post('h4');

			}else if($this->input->post('holiday')=="close"){
				$update_order_settings['holiday_timings']="close";

			}
			$result = $this->Morder_settings->update_order_settings($update_order_settings);
			if(array_key_exists('success',$result)){
				$this->messages->add(_('holiday settings has been updated successfully.'),'success');
			}else{
				$this->messages->add(_('error occured while updating holiday settings!'),'error');
			}

			$this->session->set_userdata(array('show_hide',6));

			redirect('cp/settings');
		}

		$general_settings = $this->Mgeneral_settings->get_general_settings();
		$activated_addon=$general_settings[0]->activated_addons;
		$activated_addons = explode("#", $activated_addon);
		if(in_array("4",$activated_addons))
		{
			$data['activated_addons_status']='1';
		}else{
			$data['activated_addons_status']='0';
		}
		if($general_settings != array() && $general_settings[0]->hide_intro == '1'){
			$this->session->set_userdata('show_hide',true);
		}else{
			$this->session->set_userdata('show_hide',false);
		}
		//for the 1st form in settings.php//
		$data['general_settings']=$this->Mgeneral_settings->get_general_settings();
		$data['promocode_settings']=$this->Mgeneral_settings->get_promocode_settings();
		$this->load->model('mcp/Mlanguages');
		$data['languages'] = $this->Mlanguages->select();

		//for the 2nd form in settings.php//
		$this->load->model('Mgroups');
		$data['company_groups']=$this->Mgroups->get_groups_new(array('company_id'=>$this->company_id,'type'=>0));
		$data['company_wt_groups']=$this->Mgroups->get_groups_new(array('company_id'=>$this->company_id,'type'=>1));
		$data['company_person_groups']=$this->Mgroups->get_groups_new(array('company_id'=>$this->company_id,'type'=>2));

		//for the 3rd form of settings.php //
		$this->load->model('Mpickup_delivery_timings');
		$data['pickup_settings']=$this->Mpickup_delivery_timings->pickup_timings();
		$data['custom_pickup_settings']=$this->Mpickup_delivery_timings->custom_pickup_timings();

		//for the 4th form of settings.php i.e. delivery settings//
		$this->load->model('Mdelivery_areas');
		$data['delivery_areas']=$this->Mdelivery_areas->get_delivery_areas();
		$data['area_details']=$this->Mdelivery_areas->get_area_details();//this is specifically for the table that gets printed after delivery areas
		$data['belgium_states']=$this->Mdelivery_areas->get_states(21);
		$data['netherlands_states']=$this->Mdelivery_areas->get_states(150);
		$data['delivery_area_settings'] = $this->Mdelivery_areas->get_delivery_area_settings();
		$data['all_countries'] = $this->Mdelivery_areas->get_all_countries();
		$data['companies_countries'] = $companies_countries = $this->Mdelivery_areas->get_companies_countries($this->company_id);
		$country_ids = array();
		if(!empty($companies_countries)){
			foreach ($companies_countries as $companies_country){
				$country_ids[] = $companies_country->country_id;
			}
		}
		$data['country_ids'] = $country_ids;

		//change
		$data['companies_countries_int'] = $companies_countries_int = $this->Mdelivery_areas->get_companies_countries_int($this->company_id);
		$country_ids = array();
		if(!empty($companies_countries_int)){
			foreach ($companies_countries_int as $companies_country){
				$country_ids[] = $companies_country->country_id;
			}
		}
		$data['country_ids_int'] = $country_ids;
		//for the 5th form in settings.php//
		//--the mail msg section is updated and retrieved through table general_settings --//

		//for the 6th form in settings.php//
		$this->load->model('Morder_settings');
		$data['order_settings']=$this->Morder_settings->get_order_settings();
		$data['custom_order_settings']=$this->Morder_settings->get_custom_order_settings();
		$data['holiday_timings']=$this->Morder_settings->get_holiday_timings();

		// This is for the cardgate settings at PAYMENT settings tab
		$this->load->model('Mpayment');
		$cardgate_setting = $this->Mpayment->get_cardgate_setting(array('company_id' => $this->company_id));
		if(!empty($cardgate_setting))
			$data['cardgate_setting'] = $cardgate_setting[0];
		else
			$data['cardgate_setting'] = array();

		$this->db->select('show_sheet');
		$data['sheet_in_desk'] = $this->db->get_where('desk_settings', array( 'company_id' => $this->company_id ) )->result();

		$data['content'] = 'cp/settings';

		$data['show'] = $this->session->userdata(1)?$this->session->userdata(1):0;

		if($this->session->userdata(1))$this->session->set_userdata(array('show_hide',0));
		$this->load->view('cp/cp_view',$data);
	}

	public function general_settings()
	{
		if($this->input->post('btn_update'))
		{
			$this->session->set_userdata('action', 'general_setting_json');

			$this->messages->clear();//clears all messages

			if($this->input->post('act')=="edit_general_setting"){
				$result = $this->Mgeneral_settings->update_general_settings();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',1));

				redirect('cp/settings');
			}
		}
	}

	function remove_images(){
		$reponse = $this->Mcompany->remove_uploaded_images($this->company_id);
		$this->session->set_userdata('action', 'general_setting_json');
		echo json_encode($reponse);
	}

	public function configuregroups()
	{
		$this->session->set_userdata('action', 'general_setting_json');
		if($this->input->post('act')=="group_edit"){

			$this->load->model('Mgroups');
			$result = $this->Mgroups->update_groups();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',2));

			redirect('cp/settings');
		}

		if($this->input->post('act')=="group_wt_edit"){

			$this->load->model('Mgroups');
			$result = $this->Mgroups->update_groups();

			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',2));

			redirect('cp/settings');
		}

		if($this->input->post('act')=="group_person_edit"){

			$this->load->model('Mgroups');
			$result = $this->Mgroups->update_groups();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',2));

			redirect('cp/settings');
		}
	}

	public function pickupsettings()
	{
 		if($this->input->post('act')=="edit_pickup_settings"){
 			if(!$this->input->post('same_day_time_pickup_start')){
 				$same_day_time_pickup_start = '06:00';
 			}
 			else{
 				$same_day_time_pickup_start = $this->input->post('same_day_time_pickup_start');
 			}
 			$this->session->set_userdata('action', 'general_setting_json');
			$this->load->model('Morder_settings');
			$update_order_settings=array(
					'same_day_orders_pickup'=>$this->input->post('same_day_orders_pickup'),
					'allowed_days_pickup'=> ( ($this->input->post('same_day_orders_pickup'))?implode(",",$this->input->post('allowed_days_pickup')):'' ),
					'time_diff_pickup'=>$this->input->post('time_diff_pickup'),
					'same_day_time_pickup'=>$this->input->post('same_day_time_pickup'),
					'same_day_time_pickup_start'=>$same_day_time_pickup_start,
					'allow_order_date'=>implode("#",$this->input->post('same_day_excep')),
					'date_time_diff_pickup'=>implode("#",$this->input->post('date_time_diff_pickup')),
					'same_date_start_time_pickup'=>implode("#",$this->input->post('same_date_time_pickup_start_exp')),
					'same_date_end_time_pickup'=>implode("#",$this->input->post('same_date_time_pickup_end_exp')),
					/*'customise_pickup'=>$pickup_string,*/
					'custom_time_pickup'=>implode("#",$this->input->post('custom_time_pickup')),
					'custom_days_pickup'=>implode("#",$this->input->post('custom_days_pickup')),
					'all_day_starttime_p'=>$this->input->post('all_day_starttime_p'),
					'all_day_endtime_p'=>$this->input->post('all_day_endtime_p'),
					'time_restriction_p'=>$this->input->post('time_restriction_p'),
					'custom_pickup_holiday'=>$this->input->post('custom_pickup_holiday'),
					'custom_holidays_pickup'=>$this->input->post('custom_holidays_pickup')
			);

			if(!$this->input->post('same_day_orders_pickup')){
				$update_order_settings['time_diff_pickup']="0";
			}

			if(!$this->input->post('customise_pickup')){
				$update_order_settings['customise_pickup']='0';
			}

			$custom_new_days_pickup_array = $this->input->post('custom_new_days_pickup');
			$custom_date_time_pickup_array = $this->input->post('custom_date_time_pickup');
			$custom_date_days_pickup_array = $this->input->post('custom_date_days_pickup');
			$searchObject = '';
			$keys = array();

			if ((isset($custom_new_days_pickup_array)  && !empty($custom_new_days_pickup_array)))
			{
				foreach($custom_new_days_pickup_array as $k => $v) {
					if($v == $searchObject) $keys[] = $k;
				}

				if (empty($keys)){
					$this->custom_date_time_pickup_pri($custom_new_days_pickup_array,$custom_date_time_pickup_array,$custom_date_days_pickup_array);
				}
				else {
					foreach ($keys as $val)
					{
						unset($custom_new_days_pickup_array[$val]);
						unset($custom_date_time_pickup_array[$val]);
						unset($custom_date_days_pickup_array[$val]);
						$this->custom_date_time_pickup_pri($custom_new_days_pickup_array,$custom_date_time_pickup_array,$custom_date_days_pickup_array);
					}
				}
			}
			else {
				$update_custom_order_settings=array(
					'custom_days_pickup'=>'',
					'custom_date_time_pickup'=>'',
					'custom_date_days_pickup'=>''
				);
				$custom_order_settings_res = $this->Morder_settings->update_custom_order_settings($update_custom_order_settings,1);
			}

			$result = $this->Morder_settings->update_order_settings($update_order_settings);

			$this->load->model('Mpickup_delivery_timings');
			$this->Mpickup_delivery_timings->update_pickup_delivery_timings();
			$this->Mpickup_delivery_timings->update_custom_pickup_timings();

			if(array_key_exists('success',$result)){
				$this->messages->add(_('pickup settings has been updated successfully.'),'success');
			}else{
				$this->messages->add(_('error occured while updating pickup settings!'),'error');
			}

			$this->session->set_userdata(array('show_hide',3));

			redirect('cp/settings');
		}
	}

	private function custom_date_time_pickup_pri($custom_new_days_pickup_array = array(),$custom_date_time_pickup_array = array(),$custom_date_days_pickup_array = array()){
		$searchObject = '';
		$keys1 = array();
		foreach($custom_date_time_pickup_array as $k1 => $v1) {
			if($v1 == $searchObject) $keys1[] = $k1;
		}
		if (empty($keys1)){
			$searchObject = '';
			$keys2 = array();
			foreach($custom_date_days_pickup_array as $k2 => $v2) {
				if($v2 == $searchObject) $keys2[] = $k2;
			}
		}
		else {
			foreach ($keys1 as $val1)
			{
				unset($custom_new_days_pickup_array[$val1]);
				unset($custom_date_time_pickup_array[$val1]);
				unset($custom_date_days_pickup_array[$val1]);
				$searchObject = '';
				$keys2 = array();
				foreach($custom_date_days_pickup_array as $k2 => $v2) {
					if($v2 == $searchObject) $keys2[] = $k2;
				}
			}
		}
		if (empty($keys2)){
			$update_custom_order_settings=array(
					'custom_days_pickup'=>implode("#",$custom_new_days_pickup_array),
					'custom_date_time_pickup'=>implode("#",$custom_date_time_pickup_array),
					'custom_date_days_pickup'=>implode("#",$custom_date_days_pickup_array)
			);
			$custom_order_settings_res = $this->Morder_settings->update_custom_order_settings($update_custom_order_settings);
		}
		else {
			foreach ($keys2 as $val2)
			{
				unset($custom_new_days_pickup_array[$val2]);
				unset($custom_date_time_pickup_array[$val2]);
				unset($custom_date_days_pickup_array[$val2]);
			}
			$update_custom_order_settings=array(
					'custom_days_pickup'=>implode("#",$custom_new_days_pickup_array),
					'custom_date_time_pickup'=>implode("#",$custom_date_time_pickup_array),
					'custom_date_days_pickup'=>implode("#",$custom_date_days_pickup_array)
			);
			$custom_order_settings_res = $this->Morder_settings->update_custom_order_settings($update_custom_order_settings);
		}
	}

	public function deliverysettings()
	{
		if($this->input->post('act')=="edit_delivery_settings"){

			$this->session->set_userdata('action', 'general_setting_json');

		 	$this->load->model('Morder_settings');
		 	$update_order_settings=array(
		 			'same_day_orders_delivery'=>$this->input->post('same_day_orders_delivery'),
		 			'allowed_days_delivery'=> ( ($this->input->post('same_day_orders_delivery'))?implode(",",$this->input->post('allowed_days_delivery')):'' ),
		 			'time_diff_delivery'=>$this->input->post('time_diff_delivery'),
		 			'same_day_time_delivery'=>$this->input->post('same_day_time_delivery'),
		 			/*'customise_delivery'=>$delivery_string,*/
		 			'custom_time_delivery'=>implode("#",$this->input->post('custom_time_delivery')),
		 			'custom_days_delivery'=>implode("#",$this->input->post('custom_days_delivery')),
		 			'all_day_starttime_d'=>$this->input->post('all_day_starttime_d'),
		 			'all_day_endtime_d'=>$this->input->post('all_day_endtime_d'),
		 			'time_restriction_d'=>$this->input->post('time_restriction_d'),
		 			'custom_delivery_holiday'=>$this->input->post('customise_delivery_holiday'),
		 			'custom_holidays_delivery'=>$this->input->post('custom_holidays_delivery'),
		 			'hide_hrs_min_delivery'=>$this->input->post('hide_hrs_min_delivery')
		 	);

		 	if(!$this->input->post('same_day_orders_delivery')){
		 		$update_order_settings['time_diff_delivery']="0";
		 	}
		 	if(!$this->input->post('customise_delivery')){

		 		$update_order_settings['customise_delivery']='0';

		 	}
			$result = $this->Morder_settings->update_order_settings($update_order_settings);

			$this->load->model('Mpickup_delivery_timings');
			$this->Mpickup_delivery_timings->update_pickup_delivery_timings();

			$this->load->model('Mdelivery_areas');
			$this->Mdelivery_areas->update_delivery_settings();
			$this->Mdelivery_areas->int_del_add();

			if(array_key_exists('success',$result)){
				$this->messages->add(_('delivery settings has been updated successfully.'),'success');
			}else{
				$this->messages->add(_('error occured while updating delivery settings!'),'error');
			}

			$this->session->set_userdata(array('show_hide',4));

			redirect('cp/settings');

		}
	}

	public function mailmessages()
	{
	 	if($this->input->post('act')=="edit_mail_messages"){

			$result = $this->Mgeneral_settings->update_general_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',5));

			redirect('cp/settings');
		}
	}

	public function faqclients()
	{
		if( $this->input->post('act') == "edit_faq_settings")
		{
			$this->session->set_userdata('action', 'general_setting_json');

			$result = $this->Mgeneral_settings->update_general_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',10));

			redirect('cp/settings');
		}
	}

	public function termsandConditions()
	{
		if( $this->input->post('act') == "edit_tnc_settings")
		{
			$this->session->set_userdata('action', 'general_setting_json');

			$tnc = $this->input->post('tnc_txt');
			$result = $this->Mgeneral_settings->update_tnc_settings($tnc);
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',11));

			redirect('cp/settings');
		}
	}

	public function paymentsettings()
	{
	 	if( $this->input->post('act') == "update_payment_settings")
		{
			$this->session->set_userdata('action', 'general_setting_json');

			// Updating Paypal settings
			$result = $this->Mgeneral_settings->update_general_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			// Updating Cardgate settings
			$sandbox_active = $this->input->post('sandbox_active');
			if($sandbox_active == "")
			{
				$sandbox_active = '0';
			}
			$this->load->model('Mpayment');
			$update_array = array(
					'cardgate_payment' => $this->input->post('cardgate_payment'),
					'minimum_amount_cardgate' => $this->input->post('minimum_amount_cardgate'),
					'c_apply_tax' => $this->input->post('c_apply_tax'),
					'c_tax_percentage' => $this->input->post('c_tax_percentage'),
					'c_tax_amount' => $this->input->post('c_tax_amount'),
					'sandbox_active' => $sandbox_active,
			);

			$this->Mpayment->update_cardgate_settings(array('company_id' => $this->company_id), $update_array);

			// Updating Advance Payment settings
			$this->load->model('Morder_settings');
			$update_arr = array('adv_payment' => $this->input->post('adv_payment'));
			$this->Morder_settings->update_order_settings($update_arr);

			$this->session->set_userdata(array('show_hide',8));
			redirect('cp/settings');
		}
	}

	public function labeler()
	{
	 	if( $this->input->post('act') == "edit_labeler_settings")
		{
			/*$config['upload_path'] = dirname(__FILE__).'/../../../assets/cp/labeler_logo/';
			$config['allowed_types'] = 'gif|jpg|png';

			$this->load->library('upload', $config);

			if ( $this->upload->do_upload('upload_logo')){
				$upload_data = $this->upload->data();
				$result['logo'] = array('upload_data' => $upload_data);
				$result = $this->Mgeneral_settings->update_labeler_settings($upload_data['file_name']);
			}
			else{
				$result =  array('error' => $this->upload->display_errors() );
			}
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}
			$this->session->set_userdata(array('show_hide',12));

			redirect('cp/settings');*/

			$result = $this->Mgeneral_settings->update_labeler_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',12));

			redirect('cp/settings');
		}
	}

	public function othersettings()
	{ 
		
		if( $this->input->post('act') == "edit_other_settings")
		{
			$this->session->set_userdata('action', 'general_setting_json');

			$result = $this->Mgeneral_settings->update_general_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add($result['success'],'success');
			}else{
				$this->messages->add($result['error'],'error');
			}

			$this->session->set_userdata(array('show_hide',9));

			if($this->input->post('image_name') != '')
			{ 
		        $this->db->select('comp_default_image');
		        $comp_default_image = $this->db->get_where('desk_settings', array('company_id' => $this->company_id))->result();
		        if(!empty($comp_default_image[0]->comp_default_img)){
			     unlink(dirname(__FILE__).'/../../../assets/cp/images/infodesk_default_image/'.$comp_default_image[0]->comp_default_image);
		        }
	            
	            $prefix = 'cropped_'.$this->company_id.'_';
		        $str = $this->input->post('image_name');
		        if (substr($str, 0, strlen($prefix)) == $prefix) {
			       $str = substr($str, strlen($prefix));
		        }
		        if(isset($str) && $str != ''){
			       $this->image = $this->company_id.'_'.$str;
			       $image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));
			       file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/infodesk_default_image/'.$this->image, $image_file);

			    }

			    if($this->input->post('image_name') != ''){
	        	$update_arr = array('comp_default_image' =>$this->image);
		        }
		        $where_arr = array( 'company_id' => $this->company_id );
		        $this->Mcompany->update_company_default_image( $where_arr, $update_arr );
		        $this->session->set_userdata('action', 'general_setting_json');
				
		     }
			redirect('cp/settings');
		}      

	}

	function areadetails_addedit(){

		$this->messages->clear();//clears all previous messages

		if($this->input->post('add')){
			$this->load->model('Mdelivery_settings');
			$result = $this->Mdelivery_settings->insert_delivery_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add(_('Delivery area details has been added successfully.'),'success');
				$area_id = $result['success'];
			}else{
				$this->messages->add(_('Error. Delivery area details could not be added.'),'error');
				$area_id = $result['error'];
			}
		}else if($this->input->post('update')&&$this->input->post('id')){

			$this->load->model('Mdelivery_settings');
			$result = $this->Mdelivery_settings->update_delivery_settings();
			if(array_key_exists('success',$result)){
				$this->messages->add(_('Delivery area details has been updated successfully.'),'success');
				$area_id = $result['success'];
			}else{
				$this->messages->add(_('Error. Delivery area details could not  be updated.'),'error');
				$area_id = $result['error'];
			}

		}
		redirect('cp/settings/areadetails_addedit/'.$area_id);
	}

	public function edit_holiday_setting()
	{
		if($this->input->post('act')=="edit_holiday_settings"){

			$this->session->set_userdata('action', 'general_setting_json');

			$this->load->model('Morder_settings');
			$update_order_settings=array();
			if($this->input->post('holiday')=="open"){
				$update_order_settings['holiday_timings']=$this->input->post('h1').",".$this->input->post('h2').",".$this->input->post('h3').",".$this->input->post('h4');

			}else if($this->input->post('holiday')=="close"){
				$update_order_settings['holiday_timings']="close";

			}
			$result = $this->Morder_settings->update_order_settings($update_order_settings);
			if(array_key_exists('success',$result)){
				$this->messages->add(_('holiday settings has been updated successfully.'),'success');
			}else{
				$this->messages->add(_('error occured while updating holiday settings!'),'error');
			}

			$this->session->set_userdata(array('show_hide',6));

			redirect('cp/settings');
		}
	}
}