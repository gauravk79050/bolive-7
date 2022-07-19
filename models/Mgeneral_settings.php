<?php

class Mgeneral_settings extends CI_Model{

	function __construct()
	{
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function dataloggers($comp_id){
		return $this->db->get_where('datalogger_bluecherry', array('comp_id' => $comp_id))->row();
	}
	function get_general_settings($params=array(), $select = null)
	{
		if($params)
		{
			foreach($params as $key=>$value)
			{
				$this->db->where(array($key=>$value));
			}//END OF FOREACH
		}
		else
		{
			$this->db->where(array('company_id'=>$this->company_id));
		}

		if($select){
			$this->db->select($select);
		}

		$query=$this->db->get('general_settings')->result();

		return($query);
	}

	function get_promocode_settings($params=array(), $select = null)
	{
		if($params)
		{
			foreach($params as $key=>$value)
			{
				$this->db->where(array($key=>$value));
			}
		}
		else
		{
			$this->db->where(array('company_id'=>$this->company_id));
		}

		if($select){
			$this->db->select($select);
		}

		$query=$this->db->get('promocode')->result();

		return($query);
	}

	function update_general_settings()
	{
		if($this->input->post('act') == "edit_general_setting")
		{
			$languages = '';
			if($this->input->post('lang_1'))
				$languages .= '1_';
			if($this->input->post('lang_2'))
				$languages .= '2_';
			if($this->input->post('lang_3'))
				$languages .= '3_';

			if($languages != '')
				$languages = substr($languages,0,-1);

			$update_general_settings=array(

			        'language_id'=>$this->input->post('language_id'),
					'emailid'=>$this->input->post('emailid'),
					'shop_offline'=>$this->input->post('shop_offline'),
					'shop_offline_message'=>$this->input->post('shop_offline_message'),
					'shop_visible'=>$this->input->post('shop_visible'),
					'shop_password'=>$this->input->post('shop_password'),
					'shop_visible_message'=>($this->input->post('shop_visible_message')=='<p>0</p>')?'':$this->input->post('shop_visible_message'),
					'show_message_front'=>$this->input->post('show_message_front'),
					'message_front'=>$this->input->post('message_front'),
					'msg_front_txt_color'=>$this->input->post('msg_front_txt_color'),
					'delivery_service'=>$this->input->post('delivery_service'),
					'pickup_service'=>$this->input->post('pickup_service'),
					'category_feature'=>$this->input->post('category_feature'),
					'disable_price'=>$this->input->post('disable_price'),
					//'hide_availability'=>$this->input->post('hide_availability'),
					'hide_intro' => $this->input->post('hide_intro'),
					'frontend_languages' => $languages,
					'hide_price_login' => $this->input->post("hide_price_login")
					/*,
					'pay_option' => (($this->input->post('pay_option'))?$this->input->post('pay_option'):0)*/
				);
			if($this->input->post('language_id'))
			{
				if( isset( $this->company_id ) ){
					// Update infodesk Language
					$this->db->where( 'company_id' , $this->company_id );
					$this->db->update( 'desk_settings', array( 'lang_id' => $this->input->post('language_id') ) );
					// Update Allergenkaart language
					$this->db->where( "company_id", $this->company_id );
					$this->db->update('allergenkaart_design', array( 'lang' => $this->input->post('language_id') ) );
				}

			   $this->db->where(array('id'=>$this->input->post('language_id')));
		       $query=$this->db->get('language')->row();

			   if($query->locale)
			   {
			      if(isset($_COOKIE['locale']))
					unset($_COOKIE['locale']);

				  setcookie('locale',$query->locale,time()+365*24*60*60,'/');
			   }
			}

			if(!$this->input->post('hide_intro')){
				$insert_general_settings['hide_intro'] = '0';
			}
			if(!$this->input->post('delivery_service')){
				$update_general_settings['delivery_service']='0';
			}
			if(!$this->input->post('pickup_service')){
				$update_general_settings['pickup_service']='0';
			}
			if(!$this->input->post('disable_price')){
				$update_general_settings['disable_price']='0';
			}

			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('general_settings',$update_general_settings);

			if($result){
				$return_data =  array('success' => _('General settings has been updated successfully.') );
			}else{
				$return_data =  array('error' => _('Error in updating general settings.') );
			}

		}//end if

		if($this->input->post('act')=="edit_mail_messages"){

			$update_general_settings=array(
					'subject_emails'=>$this->input->post('subject_emails'),
					'orderreceived_msg'=>$this->input->post('orderreceived_msg'),
					'ok_msg'=>$this->input->post('ok_msg'),
					'hold_msg'=>$this->input->post('hold_msg'),
					'completedpickup_msg'=>$this->input->post('completedpickup_msg'),
					'completeddelivery_msg'=>$this->input->post('completeddelivery_msg'),
					'payment_cancel_msg' => $this->input->post('pay_can'),
					'order_cancel_msg' => $this->input->post('ord_can'),
					);

			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('general_settings',$update_general_settings);

			if($result){
				$return_data =  array('success' => _('Mail Messages Updated Successfully.') );
			}else{
				$return_data =  array('error' => _('Error Occured in Updating Mail Messages.') );
			}
		}//end if;

		if( $this->input->post('act')=="edit_access_code" ){
		    $update_general_settings=array('access_super'=>$this->input->post('access_super'));

		    $this->db->where(array('id'=>$this->company_id));
			$result = $this->db->update('company',$update_general_settings);

			if($result){
				$return_data =  array('success' => _('Your super admin access code is been updated successfully.') );
			}else{
				$return_data =  array('error' => _('Error occured in updating access code. Please try again.') );
			}
		}

		if($this->input->post('act')=="update_payment_settings"){

			$update_general_settings=array(
					'online_payment'=>$this->input->post('online_payment'),
					//'paypal_address'=>$this->input->post('paypal_address'),
					'minimum_amount_paypal'=>$this->input->post('minimum_amount_paypal'),
					'apply_tax'=>( $this->input->post('apply_tax') )?'1':'0',
					'tax_percentage'=>$this->input->post('tax_percentage'),
					'tax_amount'=>$this->input->post('tax_amount'),

					//'payment_instructions'=>$this->input->post('payment_instructions'),
					//'pay_complete_msg'=>$this->input->post('pay_complete_msg'),
					//'pay_incomplete_msg'=>$this->input->post('pay_incomplete_msg')
					);

			$update_general_settings=array(
					'show_revocation'=>$this->input->post('show_revocation')
				);
			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('general_settings',$update_general_settings);

			if($result){
				$return_data =  array('success' => _('Payment Settings Updated Successfully.') );
			}else{
				$return_data =  array('error' => _('Error Occured in Updating Payment Settings.') );
			}
		}

		if($this->input->post('act')=="edit_other_settings"){

			if($this->input->post('image_name')){
				$this->db->select('comp_default_image');
				$comp_default_image = $this->db->get_where('general_settings', array('company_id' => $this->company_id))->result();

				if(!empty($comp_default_image[0]->comp_default_image)){
					unlink(dirname(__FILE__).'/../../assets/cp/images/infodesk_default_image/'.$comp_default_image[0]->comp_default_image);
				}

				$prefix = 'cropped_'.$this->company_id.'_';
				$str = $this->input->post('image_name');
				if (substr($str, 0, strlen($prefix)) == $prefix) {
					$str = substr($str, strlen($prefix));
				}
				if(isset($str) && $str != ''){
					$this->image = $this->company_id.'_'.$str;
					$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));
					file_put_contents(dirname(__FILE__).'/../../assets/cp/images/infodesk_default_image/'.$this->image, $image_file);
				}
			}

			if ($this->input->post('list_drop') != 0){
				$shop_view_default = $this->input->post('list_drop');
			}
			else{
				$shop_view_default = $this->input->post('default_list_drop');
			}

				$update_general_settings=array(
					'hide_availability'=>$this->input->post('hide_availability'),
					'activate_discount_card'=>$this->input->post('activate_dicount'),
					'discount_card_message'=>$this->input->post('discountcard_text'),
					'calendar_country' => $this->input->post('calendar_country'),
					'labeler_print_type' => $this->input->post('labeler_print_type'),
					'labeler_type' => $this->input->post('labeler_type'),
					'show_searchbox' => $this->input->post("show_searchbox"),
					'activate_suggetions' => $this->input->post("activate_suggetions"),
					'num_of_suggetions' => $this->input->post("num_of_suggetions"),
					'disc_per_amount' => $this->input->post("disc_per_amount"),
					'disc_after_amount' => $this->input->post("disc_after_amount"),
					'disc_percent' => $this->input->post("disc_percent"),
					'disc_price' => $this->input->post("disc_price"),
					'hide_register' => $this->input->post("hide_register"),
					'extra_field_popup' => $this->input->post("extra_field_popup"),
					'extra_field_popup_name' => $this->input->post("extra_field_popup_name"),
					'printer_orders' => $this->input->post("printer_orders"),
					'order_timing_info' => $this->input->post('order_timing_info'),
					'notify_req_producer'=> $this->input->post('notify_producer'),
					'notify_prod_assign'=> $this->input->post('notify_pro_assign'),
					'display_fixed' => $this->input->post('display_fixed'),
					'shop_view' => $this->input->post('list_drop'),
					'shop_view_default' => $shop_view_default,
					'amt_row_page' => $this->input->post('list_pages_drop'),
					'enbr_status' => $this->input->post('enbr_status'),
					'biggest_image' => $this->input->post('biggest_image'),
					'introcode' => $this->input->post('introcode_check'),
					'introcode_text' => $this->input->post('introcode_text'),
					'introcode_percent' => $this->input->post('introcode_percent'),
					'introcode_price' => $this->input->post('introcode_price'),
					'mobile_req' => $this->input->post('mobile_req'),
					'order_id_prefix' => $this->input->post('order_id_prefix'),
					'stock_show' => $this->input->post('obs_stock_show')
			);

			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('general_settings',$update_general_settings);

			$promocode_check = $promocode2_check = 0;
			if ($this->input->post('promocode_check'))
			{
				if ((empty($this->input->post('start_date'))  || empty($this->input->post('end_date') )))
				{
					$promocode_check = '0';
				}
				else {
					$promocode_check = '1';
				}
			}

			if ($this->input->post('promocode2_check'))
			{
				$promocode2_check = '1';
			}
			else {
				$promocode2_check = '0';
			}
			$update_promocode_settings=array(
					'promocode1' => $promocode_check,
					'promocode1_text' => $this->input->post('promocode_text'),
					'promocode1_percent' => $this->input->post('promocode_percent'),
					'promocode1_price' => $this->input->post('promocode_price'),
					'promocode2' => $promocode2_check,
					'promocode2_text' => $this->input->post('promocode2_text'),
					'promocode2_percent' => $this->input->post('promocode2_percent'),
					'promocode2_price' => $this->input->post('promocode2_price')
			);
			$update_promocode_settings['promocode1_start'] = $this->input->post('start_date');

			$update_promocode_settings['promocode1_end'] = $this->input->post('end_date');

			$promocode2_start_date = $this->input->post('promocode2_start_date');
			$promocode2_end_date = $this->input->post('promocode2_end_date');

			$searchObject = '';
			$keys = array();
			$keys1 = array();
			if ((isset($promocode2_start_date)  && !empty($promocode2_start_date)))
			{
				foreach($promocode2_start_date as $k => $v) {
					if($v == $searchObject) $keys[] = $k;
				}

				if (!empty($keys)){
					foreach ($keys as $val)
					{
						unset($promocode2_start_date[$val]);
						unset($promocode2_end_date[$val]);
					}
				}

				foreach($promocode2_end_date as $k1 => $v1) {
					if($v1 == $searchObject) $keys1[] = $k1;
				}

				if (!empty($keys1)){
					foreach ($keys1 as $val1)
					{
						unset($promocode2_start_date[$val1]);
						unset($promocode2_end_date[$val1]);
					}
				}

			}
			$promocode2_start_date = array_values($promocode2_start_date);
			$promocode2_end_date = array_values($promocode2_end_date);
			if((isset($promocode2_start_date) && !empty($promocode2_start_date))){
				$update_promocode_settings['promocode2_start'] = implode("#",$promocode2_start_date);
			}else {
				$update_promocode_settings['promocode2_start'] = '';
			}

			if((isset($promocode2_end_date) && !empty($promocode2_end_date))){
				$update_promocode_settings['promocode2_end'] = implode("#",$promocode2_end_date);
			}else {
				$update_promocode_settings['promocode2_end'] = '';
			}
			$this->db->where(array('company_id'=>$this->company_id));
			$result_upd = $this->db->update('promocode',$update_promocode_settings);

			if($result){
				// Updating pre assigned holidays by MCP
				if($this->input->post('calendar_country') == 'calendar_belgium'){
					$this->db->select('day,month,year');
					$this->db->where('month >=',date('n'));
					$this->db->where('year >=',date('Y'));
					$pre_assigned_holidays = $this->db->get('calendar_belgium')->result();
					if(!empty($pre_assigned_holidays)){
						foreach ($pre_assigned_holidays as $holiday_date){
							$data_array = array(
									'day' => $holiday_date->day,
									'month' => $holiday_date->month,
									'year' => $holiday_date->year,
									'company_id' => $this->company_id,
									'calendar' => 'calendar_belgium'
							);

							$this->db->where($data_array);
							$exist = $this->db->get('company_holidays')->result();
							if(empty($exist)){
								$data_array['timestamp'] = strtotime($holiday_date->year.'-'.$holiday_date->month.'-'.$holiday_date->day);
								$data_array['date_added'] = date('Y-m-d H:i:s');
								$this->db->insert('company_holidays', $data_array);
							}
						}
					}
				}else if($this->input->post('calendar_country') == 'calendar_netherland'){
					$this->db->select('day,month,year');
					$this->db->where('month >=',date('n'));
					$this->db->where('year >=',date('Y'));
					$pre_assigned_holidays = $this->db->get('calendar_netherland')->result();
					if(!empty($pre_assigned_holidays)){
						foreach ($pre_assigned_holidays as $holiday_date){
							$data_array = array(
									'day' => $holiday_date->day,
									'month' => $holiday_date->month,
									'year' => $holiday_date->year,
									'company_id' => $this->company_id,
									'calendar' => 'calendar_netherland'
							);

							$this->db->where($data_array);
							$exist = $this->db->get('company_holidays')->result();
							if(empty($exist)){
								$data_array['timestamp'] = strtotime($holiday_date->year.'-'.$holiday_date->month.'-'.$holiday_date->day);
								$data_array['date_added'] = date('Y-m-d H:i:s');
								$this->db->insert('company_holidays', $data_array);
							}
						}
					}
				}

				if($this->input->post('enbr_status') != $this->input->post('prev_enbr_status')){

					$this->db->select('id');
					$pro_id_result = $this->db->get_where('products',array('company_id'=>$this->company_id))->result_array();

					$this->db->select('language_id');
					$lang_id = $this->db->get_where( 'general_settings', array( 'company_id'=>$this->company_id ) )->row_array();

					if( $lang_id['language_id'] == '2' ){
						$ing_var = 'ing_name_dch';
					}
					if( $lang_id['language_id'] == '3' ){
						$ing_var = 'ing_name_fr';
					}
					if( $lang_id['language_id'] == '1' ){
						$ing_var = 'ing_name';
					}
					
					if(!empty($pro_id_result)){
						$pro_arr = array();
						for($i = 0; $i < count($pro_id_result); $i++){
							$pro_arr[] = $pro_id_result[$i]['id'];
						}

						$this->db->select( 'id, ki_id' );
						if( !empty( $pro_arr ) )
							$this->db->where_in('product_id',$pro_arr);
						$ing_arr = $this->db->get('products_ingredients')->result_array();

						// $ing_id = array();
						foreach ($ing_arr as $key1 => $value1 ) {
							// $ing_id[] = $value1['ki_id'];

							
							if( $this->input->post( 'prev_enbr_status' ) == '2' ){
								$this->fdb->select( 'enbrs_relation.enbr_id as ki_id, ingredients.ing_name_dch as name' );
								$this->fdb->join( 'ingredients', 'enbrs_relation.enbr_id = ingredients.ing_id' );
								$this->fdb->where( 'enbrs_relation.name_id', $value1['ki_id'] );
								$enbr_arr = $this->fdb->get( 'enbrs_relation' )->result_array();
								if(!empty($enbr_arr)){
									foreach ($enbr_arr as $ing){
										
										$this->db->where('id',$value1['id']);
										$this->db->update('products_ingredients',array('ki_id'=> $ing['ki_id'] , 'ki_name'=> $ing['name'] ));
									}
								}
							}
							if( $this->input->post( 'prev_enbr_status' ) == '1' ){
								$this->fdb->select( 'enbrs_relation.name_id as ki_id, ingredients.'.$ing_var.' as name' );
								$this->fdb->join( 'ingredients', 'enbrs_relation.name_id = ingredients.ing_id' );
								$this->fdb->where( 'enbr_id', $value1['ki_id'] );
								$enbr_arr = $this->fdb->get( 'enbrs_relation' )->result_array();
								if(!empty($enbr_arr)){
									foreach ($enbr_arr as $ing){
										$this->db->where('id',$value1['id']);
										$this->db->update('products_ingredients',array('ki_id'=> $ing['ki_id'] , 'ki_name'=> $ing['name'] ));
									}
								}
							}
							
						}
					}
				}

				$return_data =  array('success' => _('Other Settings Updated Successfully.') );
			}else{
				$return_data =  array('error' => _('Error Occured in Updating Other Settings.') );
			}
		}

		if($this->input->post('act')=="edit_faq_settings"){

			$update_general_settings=array(
					'faq_showhide'=>$this->input->post('faq_showhide'),
					'faq_txt'=>$this->input->post('faq_txt')
			);

			$this->db->where(array('company_id'=>$this->company_id));
			$result = $this->db->update('general_settings',$update_general_settings);

			if($result){
				$return_data =  array('success' => _('FAQ - clients Settings Updated Successfully.') );
			}else{
				$return_data =  array('error' => _('Error Occured in Updating FAQ - clients Settings.') );
			}
		}

		return $return_data;
	}

	function update_company_general_settings( $company_id = NULL, $params = array() )
	{
	    if(!$company_id)
		  $company_id = $this->company_id;

		if(!empty($params))
		{
		   $this->db->where( 'company_id', $company_id );
		   $result = $this->db->update('general_settings',$params);
		   return $result;
		}
		else
		  return false;
	}

	function update_theme(){
		$update_general_settings = array();
		$update_general_settings['theme_id'] = $this->input->post('theme_id');
		$this->db->where(array('company_id'=>$this->company_id));
		$result = $this->db->update('general_settings',$update_general_settings);
		if($result){
			return array('success' => _('Theme has been updated successfully.') );
		}else{
			return array('error' => _('Theme couldn\'t change properly.') );
		}
	}

	function do_general_settings($general_settings)
	{
	   $this->db->insert('general_settings',$general_settings);
	   return $insert_id = $this->db->insert_id();
	}
	function do_allergencard_settings($allergencard_settings)
	{
	   $this->db->insert('allergenencard_settings',$allergencard_settings);
	   return $insert_id = $this->db->insert_id();
	}

	function do_meantime_settings($general_settings)
	{
		$this->db->insert('meantime_settings',$general_settings);
		return $insert_id = $this->db->insert_id();
	}

	/**
	 * Function to update Terms and Conditions of shop which will show at frontend
	 * @param string $tnc Terms and Conditions
	 */
	function update_tnc_settings($tnc){
		$this->db->where(array('company_id'=>$this->company_id));
		$result = $this->db->update('general_settings',array('tnc_txt' => $tnc));

		if($result){
			$return_data =  array('success' => _('Terms and Conditions Updated Successfully.') );
		}else{
			$return_data =  array('error' => _('Error Occured in Updating Terms and Conditions.') );
		}

		return $return_data;
	}

	function update_labeler_settings($logoname = ''){

		if($this->input->post('image_name1')){
			$this->db->select('labeler_logo');
			$labeler = $this->db->get_where('general_settings', array('company_id' => $this->company_id))->result();

			if(!empty($labeler[0]->labeler_logo)){
				unlink(dirname(__FILE__).'/../../assets/cp/labeler_logo/'.$labeler[0]->labeler_logo);
			}

			$prefix = 'cropped_'.$this->company_id.'_';
			$str = $this->input->post('image_name1');
			if (substr($str, 0, strlen($prefix)) == $prefix) {
				$str = substr($str, strlen($prefix));
			}
			if(isset($str) && $str != ''){
				$this->image = $this->company_id.'_'.$str;
				$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name1'));
				file_put_contents(dirname(__FILE__).'/../../assets/cp/labeler_logo/'.$this->image, $image_file);
			}

			if($this->image){
				$this->db->where(array('company_id'=>$this->company_id));
				$result = $this->db->update('general_settings',array('labeler_logo'=> $this->image));
			}
		}

		if(isset($result)){
			$return_data =  array('success' => _('Labeler Logo Uploaded Successfully.') );
		}else{
			$return_data =  array('error' => _('Error Occured in Uploading Labeler Logo') );
		}
		return $return_data;
	}

	function update_sheet_banner_settings($company_id = NULL, $bannername = ''){
		$return_data = array();

		if($company_id != NULL){
			$this->db->select('sheet_banner');
			$ban = $this->db->get_where('general_settings', array('company_id' => $company_id))->result();

			if(!empty($ban[0]->sheet_banner)){
				unlink(dirname(__FILE__).'/../../assets/mcp/images/sheet_banner/'.$ban[0]->sheet_banner);
			}

			if($bannername){
				$update_general_settings=array(
						'sheet_banner'	=>	$bannername
				);

				$this->db->where(array('company_id'=>$company_id));
				$result = $this->db->update('general_settings',$update_general_settings);

				if($result){
					$return_data =  array('success' => _('Sheet Banner Uploaded Successfully') );
				}else{
					$return_data =  array('error' => _('Error Occured in Uploading Sheet Banner') );
				}
			}
		}
		return $return_data;
	}

	function get_general_settings_addedit($params=array(), $select = null)
	{
		if($params)
		{
			foreach($params as $key=>$value)
			{
				$this->db->select('hide_availability, online_payment');
				$this->db->where(array($key=>$value));
			}//END OF FOREACH
		}
		else
		{
			$this->db->select('hide_availability, online_payment');
			$this->db->where(array('company_id'=>$this->company_id));
		}

		if($select){
			$this->db->select($select);
		}

		$query=$this->db->get('general_settings')->result();

		return($query);
	}

/**
*Function to insert default company row in promocode table
*	@author Abhay Hayaran <abhayhayaran@cedcoss.com>
*/
	function do_promocode_settings($promocode_settings){
		if(!empty($promocode_settings)){
			$this->db->insert('promocode',$promocode_settings);
		}
	}

	function upload_aller_banner($comp_id,$aller_banner_name, $transparency){
		if($aller_banner_name){
			$update_general_settings=array(
				'aller_banner_sheet'	=>	$aller_banner_name,
				'transparency'			=>  $transparency
			);

				$this->db->where(array('company_id'=>$comp_id));
				$result = $this->db->update('general_settings',$update_general_settings);
		}
	}
	function aller_upload_image($comp_id,$aller_upload_image){
		if($aller_upload_image){
			$update_general_settings=array(
				'aller_upload_image'	=>	$aller_upload_image
			);

				$this->db->where(array('company_id'=>$comp_id));
				$result = $this->db->update('general_settings',$update_general_settings);	
		}
	}
	function basic_to_light( $comp_id ){

		$this->db->select( 'id' );
		$res = $this->db->get_where( 'products', array( 'company_id' => $comp_id ) )->result_array();
		$obj = array();
		foreach ( $res as $key => $value ) {
			$str = '';
			$this->db->select( 'ka_id' );
			$query = $this->db->get_where( 'products_allergence', array( 'product_id' => $value['id'] ) )->result_array();
			
			if( !empty($query) ){
				$unique = array_map("unserialize", array_unique(array_map("serialize", $query)));
				$str = $str.implode('#', array_map(function($el){ 
														return $el['ka_id'];
													}, $unique));

				$obj[] = array( 
							'id' 		 => $value['id'],
							'allergence' => $str
						);
			}
		}
		if ( is_array( $obj ) && !empty($obj) ) {
			$this->db->update_batch( 'products', $obj, 'id' );
		}
	}
}
?>