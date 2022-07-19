<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function do_settings($company_id = null, $company_name = '', $company_type_ids = '')
{
	$CI =& get_instance();

	$CI->load->model('mcp/Mcompanies');
	$CI->load->model('Mgeneral_settings');
	$CI->load->model('Mgroups');
	$CI->load->model('Morder_settings');
	$CI->load->model('Mpickup_delivery_timings');


	/**
	 * Generating API Keys
	 */
	$CI->Mcompanies->generateApi($company_id);


	/**
	 * Making genearal settings
	 */

	if (in_array_any(getHCType(), $company_type_ids)) {
		$general_settings[ 'show_simple_list' ] = 2;
	} else {
		$general_settings[ 'show_simple_list' ] = 3;
	}

	$general_settings["company_id"] = $company_id;
	$general_settings["language_id"] = "2";
	$general_settings["lang_on_the_site"] = json_encode(array("2"));
	$general_settings["shop_offline"] = "1";
	$general_settings["shop_offline_message"] = "Dit is een Offline bericht die u zelf kan aanpassen in uw controlepaneel in 'instellingen' ";
	$general_settings["delivery_service"] = "0";
	$general_settings["theme_id"] = 0;
	$general_settings["calendar_country"] = 'calendar_belgium';

	/**
	 * Email subjects and messages
	 */
	$general_settings["subject_emails"] = "BESTELLINGEN - ".$company_name;

	$general_settings["orderreceived_msg"] = "<p>Beste,</p>
	<p>We hebben met succes de bestelling ontvangen, goedgekeurd en deze staat klaar op het opgegeven tijdstip. Indien u voor levering heeft gekozen moet u rekening houden met een marge van 30 minuten.</p>";

	$general_settings["ok_msg"] = "<p>Beste,</p>
	<p>Uw bestelling werd door ons nagekeken en goedgekeurd. We geven u nog een seintje wanneer deze klaar staat (ter bevestiging).</p>";

	$general_settings["hold_msg"] = "<p>Beste,</p>
	<p>Er werd een probleem vastgesteld ivm uw bestelling en de bestelling werd even on hold gezet.</p>
	<p>Wij zullen u even contacteren om te bekijken hoe we dit probleem kunnen oplossen.</p>";

	$general_settings["completedpickup_msg"] = "<p>Beste,</p>
	<p>Uw bestelling kan u komen afhalen op het afgesproken tijdstip!</p>
	<p>Gelieve aan de kassa duidelijk uw naam door te geven aub waarop u de bestelling heeft geplaatst.</p>";

	$general_settings["completeddelivery_msg"] = "<p>Beste,</p>
	<p>Uw bestelling wordt geleverd rond het afgesproken tijdstip. Gelieve er rekening me te houden dat we ongeveer 30 minuten vroeger/later de bestelling kunnen afleveren (hangt af van het aantal bestellingen die geleverd moeten worden en de lokaties).</p>";

	$CI->Mgeneral_settings->do_general_settings($general_settings);

	$promocode_settings["company_id"] = $company_id;
	$CI->Mgeneral_settings->do_promocode_settings($promocode_settings);

	/**
	 * Adding 10 rows for making groups
	 */
	$groups["company_id"] = $company_id;
	$groups["group_name"] = "";
	$inserted_ids = $CI->Mgroups->do_group_settings($groups); // This will add 10 blank rows regarding the company

	/**
	 * now updating four rows with default name
	 */
	$where_array = array( 'id' => $inserted_ids[0], 'company_id' => $company_id);
	$update_array = array('group_name' => 'Extra', 'type' => '0');
	$CI->Mgroups->for_insert_update_group($where_array, $update_array);

	$where_array = array( 'id' => $inserted_ids[1], 'company_id' => $company_id);
	$update_array = array('group_name' => 'Type', 'type' => '0');
	$CI->Mgroups->for_insert_update_group($where_array, $update_array);

	$where_array = array( 'id' => $inserted_ids[2], 'company_id' => $company_id);
	$update_array = array('group_name' => 'Grootte', 'type' => '0');
	$CI->Mgroups->for_insert_update_group($where_array, $update_array);

	$where_array = array( 'id' => $inserted_ids[3], 'company_id' => $company_id);
	$update_array = array('group_name' => 'Soort', 'type' => '0');
	$CI->Mgroups->for_insert_update_group($where_array, $update_array);

	/**
	 * Doing default order settings
	 */
	$order_settings["company_id"] = $company_id;
	$CI->Morder_settings->do_order_settings($order_settings);

	for($j=1;$j<8;$j++)
	{
		$pickup_delivery_timings = array();
		$pickup_delivery_timings["company_id"] = $company_id;
		if($j == 1)
			$CI->Mpickup_delivery_timings->do_pickup_delivery_timings_settings_custom($pickup_delivery_timings);

		$pickup_delivery_timings["day_id"] = $j;

		$CI->Mpickup_delivery_timings->do_pickup_delivery_timings_settings($pickup_delivery_timings);
	}

	$CI->load->model('Mthemes');
	$CI->Mthemes->set_company_css($company_id);

	$CI->load->model('Mcompany');

	$CI->db->select('ac_type_id,type_id');
	$ac_type = $CI->db->get_where('company',array('id'=>$company_id))->result();
	if(!empty($ac_type) && ($ac_type[0]->ac_type_id == 4 || $ac_type[0]->ac_type_id == 5 || $ac_type[0]->ac_type_id == 6 || $ac_type[0]->ac_type_id == 7)){
		$type_id = $ac_type[0]->type_id;
		$type_id = explode( '#' , $type_id );
		if($ac_type[0]->ac_type_id == '7' && (!in_array('20', $type_id) || !in_array('27', $type_id) || !in_array('28', $type_id))){
			$CI->db->where('company_id',$company_id);
			$CI->db->update('general_settings',array('hide_bp_intro' => '1'));  
		}
		if (in_array('20', $type_id) || in_array('27', $type_id)) {
			$update_desk_arr = array(
				'obsdesk_status'=> 1
			);
			$CI->db->where('id',$company_id);
			$CI->db->update('company',$update_desk_arr);
		}
		if($ac_type[0]->ac_type_id == 5 ){
			if (in_array('20', $type_id) || in_array('27', $type_id) || in_array('28', $type_id)){
				$CI->db->where('company_id',$company_id);
				$CI->db->update('general_settings',array('easing_measure' => '0'));  
			}	
		}
		if (in_array('20', $type_id) || in_array('27', $type_id) || in_array('28', $type_id)){
			$CI->db->where('company_id',$company_id);
			$CI->db->update('general_settings',array('show_traces' => '1'));  
		}
		if($ac_type[0]->ac_type_id != 1 ){
			if (!in_array('20', $type_id) || !in_array('27', $type_id)){
				$easybutler_status = array(
					'easybutler_status'=> '{"activate_easybutler":"1","easybutler_order_app":"0"}'
				);
				$CI->db->where('id',$company_id);
				$CI->db->update('company',$easybutler_status);  
			}	
		}
		$insrt_easybutler_array = array(
			'company_id'=>$company_id
		);
		$CI->db->insert('easybutler_settings',$insrt_easybutler_array);
		$CI->db->insert('menucard_layout_setting',$insrt_easybutler_array);
		$CI->db->insert('allergenencard_settings',$insrt_easybutler_array);
		$CI->load->model('mcp/Mcompanies');
		$CI->Mcompanies->insert_feedback_ques_row($company_id,$ac_type[0]->type_id);

	}
	if( !empty( $ac_type ) && isset( $ac_type[0]->type_id ) ){
		$type_id = $ac_type[0]->type_id;
		$type_id = explode( '#' , $type_id );
		if( in_array( '14', $type_id ) ){
			$default_cat_fr_restaurant = array( 
				'Voorgerecht',
				'Hoofdgerecht',
				'Nagerecht'
			);
			for ( $i = 0; $i < sizeof( $default_cat_fr_restaurant ); $i++ ) {
				$CI->db->insert( 'categories', array( 'company_id' => $company_id,'name' => $default_cat_fr_restaurant[ $i ] ) );
				$insert_id = $CI->db->insert_id();
				if( isset( $insert_id ) ){
					$CI->db->insert( 'categories_name',array( 'cat_id' => $insert_id ,'comp_id' => $company_id ,'name_dch'=> $default_cat_fr_restaurant[ $i ] ) );
				}
			}
		}

		if (in_array('20', $type_id) || in_array('27', $type_id) || in_array('28', $type_id)){
			$CI->db->where('id',$company_id);
			$CI->db->update('company',array('show_week_menu' => '1','show_infodesk' => '1'));
		}
		
		if (in_array('1', $type_id) || in_array('3', $type_id) || in_array('8', $type_id) || in_array('9', $type_id) || in_array('12', $type_id) || in_array('13', $type_id)  || in_array('23', $type_id) || in_array('24', $type_id) || in_array('25', $type_id) || in_array('26', $type_id)){
			$CI->db->where('id',$company_id);
			$CI->db->update('company',array('show_menukartt_maker' => 0));
		}else{
			$CI->db->where('id',$company_id);
			$CI->db->update('company',array('show_menukartt_maker' => 1));
		}

		if (in_array('20', $type_id) || in_array('26', $type_id) || in_array('27', $type_id)){
			$CI->db->where('id',$company_id);
			$CI->db->update('company',array('data_type' => 'premium'));
		}else{
			$CI->db->where('id',$company_id);
			$CI->db->update('company',array('data_type' => 'light'));
		}

	}
}

function get_dep_company_count(){
	$CI =& get_instance();
	$CI->load->model ( 'mcp/Mdep' );
	$dep_companies = $CI->Mdep->get_companies();
	return count ( $dep_companies );
}

function get_eb_leads_count(){
	$CI =& get_instance();
	$eb_leads_count= $CI->db->get('easybutler_recommendations')->num_rows();
	return $eb_leads_count ;
}

function is_payment_activate($company_id = 0){

	$response = false;
	if($company_id){
		$CI =& get_instance();
		$CI->load->model('Mgeneral_settings');
		$setting = $CI->Mgeneral_settings->get_general_settings(array('company_id' => $company_id), 'online_payment');
		if(!empty($setting))
			if($setting[0]->online_payment)
				$response = true;
		}
		return $response;
	}

// Function to get count of pending products
	function get_pending_product_count($company_id = 0){
		$CI =& get_instance();
		$CI->db->where('company_id', $company_id);
		$CI->db->from('products_pending');
		return $CI->db->count_all_results();
	}

// Function to get link of FDD video
	function get_video_token(){
		$CI =& get_instance();
		$CI->fdb = $CI->load->database('fdb',TRUE);
		$CI->fdb->where('id',1);
		$token_res = $CI->fdb->get('tokens')->result_array();
		$CI->fdb->close();

		return $token_res[0]['token'];

	}

//Function for get pickup and delivery timings
	if ( ! function_exists("get_pickup_delivery_closed")){
		function get_pickup_delivery_closed() {
			$CI =& get_instance();
			$CI->load->model('Mcalender');
			return $CI->Mcalender->get_pickup_delivery_closed();
		}
	}

//Function for get custom pickup timings
	if ( ! function_exists("get_custom_pickup_closed")){
		function get_custom_pickup_closed() {
			$CI =& get_instance();
			$CI->load->model('Mcalender');
			return $CI->Mcalender->get_custom_pickup_closed();
		}
	}

	if ( ! function_exists("get_pickup_delivery_closed")){
		function get_pickup_delivery_closed() {
			$CI =& get_instance();
			$CI->load->model('Mcalender');
			return $CI->Mcalender->get_pickup_delivery_closed();
		}
	}

	if ( ! function_exists("get_custom_pickup_closed")){
		function get_custom_pickup_closed() {
			$CI =& get_instance();
			$CI->load->model('Mcalender');
			return $CI->Mcalender->get_custom_pickup_closed();
		}
	}

//function to check ingredients having allergence
	if ( ! function_exists("get_the_allergence")){
		function get_the_allergence($ki_name ='',$aller_type ='0',$allergence ='',$sub_allergence ='', $lang_var = ''){
			$CI =& get_instance();
			$CI->load->model('Mproducts');
			if($lang_var == ''){
				$lang_var = $_COOKIE['locale'];
			}
			else{
				if($lang_var == '_dch'){
					$lang_var = 'nl_NL';
				}
				elseif($lang_var == '_fr'){
					$lang_var = 'fr_FR';
				}
				elseif($lang_var == '_en'){
					$lang_var = 'en_US';
				}
			}
			
			if( $lang_var == 'nl_NL' ){
				$aller_name = 'all_name_dch';
			}
			if( $lang_var == 'en_US' ){
				$aller_name = 'all_name';
			}
			if( $lang_var == 'fr_FR' ){
				$aller_name = 'all_name_fr';
			}
			$all_id = explode('-', $allergence);
			$sub_all_id = explode('-', $sub_allergence);
			$end_all = $ki_name;
			$all_type = $aller_type;
			$flag = 0;
			$ing_exception = $CI->Mproducts->get_ing_exception($lang_var);
			foreach ($ing_exception as $excpt) {
				if( mb_strtolower($ki_name,'UTF-8') == mb_strtolower(stripslashes($excpt->ingredient_name),'UTF-8') ){
					if( strpos(mb_strtolower($ki_name,'UTF-8'), mb_strtolower(stripslashes($excpt->ingredient_text),'UTF-8')) > -1){
						$flag = 1;
						$end_all = str_replace(mb_strtolower(stripslashes($excpt->ingredient_text),'UTF-8'), mb_strtoupper(stripslashes($excpt->ingredient_text),'UTF-8'), $end_all);
					}
				}
			}
			if( $flag == 0 ){
				if( $all_type == 2 ){
					$end_all = mb_strtoupper($end_all,'UTF-8');
				}
				elseif( (!empty($all_id[0]) || !empty($sub_all_id[0])) && ($all_type == 1) ){
					$end_all .= " (";
					$end_alll = '';
					$sub_arr = array();
					$sub_end_all = '';
					foreach ($all_id as $k => $all_val){
						$end_sub_alll = '';
						$all_name = $CI->Mproducts->all_name($all_val);

						if( sizeof( $all_id ) == 1 && $all_name[0]->$aller_name == 'Gluten' ) {
							$end_alll .= mb_strtolower($all_name[0]->$aller_name,'UTF-8');
						} else {
							$end_alll .= mb_strtoupper($all_name[0]->$aller_name,'UTF-8');
						}
						if( ($all_name[0]->all_name_dch == 'Gluten' || $all_name[0]->all_name_dch == 'Noten') && !empty($sub_all_id) ){
							$countss=0;
							foreach ($sub_all_id as $key => $sub_all_val){
								$sub_all_name = $CI->Mproducts->sub_all_name($sub_all_val,$all_val);
								if( !empty($sub_all_name) ){
									if( $countss == 0 ){
										$len = strlen(mb_strtoupper($all_name[0]->$aller_name,'UTF-8'));
										$end_alll = substr($end_alll, 0 , -$len);
										$countss = 1;
									}
									$end_sub_alll .= mb_strtoupper($sub_all_name[0]->$aller_name,'UTF-8');
									if( $sub_all_name[0]->$aller_name != '' ){
										$sub_arr[] = $sub_all_name[0]->$aller_name;
									}
									if(  ($sub_all_name[0]->$aller_name != '') ) {
										$end_sub_alll .= ', ';
									}
								}
							}
							$end_sub_alll = rtrim( trim($end_sub_alll), ',' );
						}
						$end_alll .= $end_sub_alll;
						if( $k < count($all_id)-1 ){
							$end_alll .= ', ';
						}
					}
					foreach ($sub_all_id as $key => $sub_all_val){
						$sub_all = $CI->Mproducts->sub_all_name($sub_all_val);
						if( !empty($sub_all) ){
							if( !in_array($sub_all[0]->$aller_name, $sub_arr) ){
								$sub_end_all .= mb_strtoupper($sub_all[0]->$aller_name,'UTF-8').', ';
							}
						}
					}
					$end_all .= $end_alll;
					if( $sub_end_all != '' ){
						$end_all .= ', ';
						$end_all .= rtrim(trim($sub_end_all),',');
					}
					$end_all .= ")";
				}
				elseif( (!empty($all_id[0]) || !empty($sub_all_id[0])) && ($all_type == 3) ){
					foreach ($all_id as $k => $all_val){
						$all_name = $CI->Mproducts->all_name($all_val);
						if( sizeof( $all_id ) == 1 && $all_name[0]->$aller_name == 'Gluten' ) {
						} else {
							$end_all = str_replace(mb_strtolower($all_name[0]->$aller_name,'UTF-8'), mb_strtoupper($all_name[0]->$aller_name,'UTF-8'), $end_all);
						}
					}
					foreach ($sub_all_id as $key => $sub_all_val){
						$sub_all_name = $CI->Mproducts->sub_all_name($sub_all_val);
						if( !empty($sub_all_name) ){
							$end_all = str_replace(mb_strtolower($sub_all_name[0]->$aller_name,'UTF-8'), mb_strtoupper($sub_all_name[0]->$aller_name,'UTF-8'), mb_strtolower($end_all,'UTF-8'));
						}
					}
				}
			}
			$end_all = str_replace(" ()","", $end_all);
			if( $end_all == 'bevat' || $end_all == 'contient' || $end_all == 'contains' || $end_all == 'o.a.' || $end_all == 'in wisselende verhoudingen' || $end_all == 'in varying proportions' || $end_all == 'dans des proportions variables' ){
				return stripslashes($end_all.' ');
			} else {
				return stripslashes($end_all.', ');
			}
		}
	}
	if ( ! function_exists("get_the_allergen")){
		function get_the_allergen($ki_name,$have_all_id,$allergence_words, $prefix = ''){
			$all_id = explode('#', $have_all_id);
			$end_all = $ki_name.' ';
			if(!in_array("0",$all_id)){
				$end_all .= "(";
				$end_alll = '';
				foreach ($all_id as $all_val){
					$all_word = array_search($all_val, $allergence_words);
					if( ($ki_name == 'cacaoboter' || $ki_name == 'beurre de cacao') && $all_word == 'melk' ) {

					} else if( ($ki_name == 'salboter' || $ki_name == 'beurre sal') && $all_word == 'melk' ) {
					} else {
						if((strtolower($ki_name) != $all_word) && ($all_word != false))
							$end_alll .= "<b>".$all_word."</b>,";
					}
				}
				$end_alll = trim($end_alll,",");
				$end_all .= $end_alll;
				$end_all .= ")";
			}

			$end_all = str_replace(" ()","", $end_all);

			$allergence_words = array_flip($allergence_words);
			if(in_array(strtolower($ki_name),$allergence_words)){
				$end_all = str_replace($ki_name, "<b>".$ki_name."</b>", $end_all);
			}

			if( $prefix == '' ) {
				if( $end_all == 'bevat' || $end_all == 'contient' || $end_all == 'contains' || $end_all == 'o.a.' || $end_all == 'in wisselende verhoudingen' || $end_all == 'in varying proportions' || $end_all == 'dans des proportions variables' ){
					return stripslashes($end_all.' ');
				} else {
					return stripslashes($end_all.', ');
				}
			} else {
				return stripslashes($end_all);
			}
		}
	}

	if (! function_exists("clean_pdf")){
		function clean_pdf($string = ''){
			$string = str_replace(' ', '-', $string);
			$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
			return preg_replace('/-+/', '-', $string);
		}
	}

	if (! function_exists("get_lang")){
		function get_lang($string = '_dch'){
			if ($string == 'en_US') {
				return '_dch';
			}elseif ($string == 'nl_NL') {
				return '_dch';
			}elseif ($string == 'fr_FR') {
				return '_fr';
			}
			else { 
				return '_dch';
			}
		}
	}

	function getHCType() {
		return [20,26,27,28];
	}

	/**
	* Function for filter type id
	*/

	function in_array_any($needles, $haystack) {
		$haystack = explode('#', $haystack);
		return !!array_intersect($needles, $haystack);
	}
