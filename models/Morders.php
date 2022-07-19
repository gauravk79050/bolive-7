<?php
class Morders extends CI_Model{

	var $company_id='';

	function __construct(){

		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
		$this->lang = get_lang( $_COOKIE['locale'] );
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function count_orders($company_id = null, $start_date=null, $end_date=null, $current_date = null, $payed = 0, $cancelled = false){

		$order_table = ($cancelled)?'orders_tmp':'orders';

		if($start_date && $end_date){
				$this->db->where("( `".$order_table."`.`created_date` BETWEEN '". $start_date."' AND '". $end_date."' )");
		}else if($current_date){
			$this->db->where("( `".$order_table."`.`order_pickupdate` = '".$current_date."' OR `".$order_table."`.`delivery_date` = '".$current_date."' )");
		}

		if( $company_id )
		{
		    $this->db->where($order_table.'.company_id',$company_id);
		}
		else
		{
			$this->db->where($order_table.'.company_id',$this->company_id);
		}

		if($payed){
			$this->db->where("payment_via_paypal != '0' AND payment_status != '1'");
		}elseif($cancelled){
			$this->db->where("payment_via_paypal != '0' AND payment_status = '0'");
		}else {
			$this->db->where("( payment_via_paypal = '0' OR (payment_via_paypal != '0' AND payment_status = '1') )");
		}

		$this->db->join('company','company.id='.$order_table.'.company_id','left');
		$this->db->join('clients','clients.id='.$order_table.'.clients_id');
		$this->db->join('country','clients.country_id = country.id','left');

		$this->db->from($order_table);
		return $this->db->count_all_results();
	}

	function get_orders( $select=array(), $id=null, $start_date=null, $end_date=null, $num=null, $offset=null, $where_params = array(), $company_id = NULL, $current_date = NULL, $is_ibsoft = NULL){

		if($num){
			$this->db->limit($num,$offset);
		}
		if( !empty( $select ) ) {
			$this->db->select( $select );
		} else {
			$this->db->select('
				orders.*,
				company.company_name,
				clients.id AS client_id,
				clients.firstname_c,
				clients.lastname_c,
				clients.company_c,
				clients.address_c,
				clients.housenumber_c,
				clients.postcode_c,
				clients.phone_c,
				clients.mobile_c,
				clients.city_c,
				clients.email_c,
				clients.country_id,
				clients.notifications,
				clients.created_c,
				clients.fax_c,
				clients.vat_c,
				country.country_name as country_name,
				postcodes.area_name as delivery_city_name,
				states.state_name as delivery_area_name,
				payment_transaction.billing_option,
				delivery_country.country_name as delivery_country_name'
			);
		}
		
		$this->db->distinct('id');
		// $this->db->join('delivery_areas','delivery_areas.id=orders.delivery_area','left');
		// $this->db->join('delivery_settings','delivery_settings.id=orders.delivery_city','left');
		$this->db->join('postcodes','postcodes.id=orders.delivery_city','left');
		$this->db->join('states','states.state_id=orders.delivery_area','left');

		$this->db->join('company','company.id=orders.company_id','left');
		$this->db->join('clients','clients.id=orders.clients_id');
		$this->db->join('country','clients.country_id = country.id','left');
		$this->db->join('country as delivery_country','orders.delivery_country = delivery_country.id','left');//used in delivery address
		$this->db->join('payment_transaction','payment_transaction.order_id=orders.temp_id','left');
		/*$this->db->join('client_numbers','client_numbers.client_id=orders.clients_id');
		$this->db->where('client_numbers.company_id',$this->company_id);*/

		if($start_date&&$end_date){
			if($is_ibsoft)
				$this->db->where("( orders.order_pickupdate BETWEEN '" . $start_date . "' AND '" . $end_date."' OR orders.delivery_date BETWEEN '" . $start_date . "' AND '" . $end_date."' )");
			else
				$this->db->where("orders.created_date BETWEEN '" . $start_date . "' AND '" . $end_date."'");
		}else if($current_date){
			$this->db->where("( orders.order_pickupdate = '".$current_date."' OR orders.delivery_date = '".$current_date."' )");
			//$this->db->or_where("orders.delivery_date",$current_date);
		}
		if($id){
			$this->db->where(array('orders.id'=>$id));
		}
		elseif( $company_id )
		{
		    $this->db->where('orders.company_id',$company_id);
		}
		else
		{
			$this->db->where('orders.company_id',$this->company_id);
		}

		if(!empty($where_params))
		{
		    $this->db->where($where_params);
		}

		// Order that are not online payed, or if payed then must not be pending..
		$this->db->where("( payment_via_paypal = '0'  OR (payment_via_paypal != '0' AND payment_status = '1') )");

		$this->db->order_by("created_date", "desc");
		$orders=$this->db->get('orders')->result();
			if($orders){
				//$countries=$this->db->get('country')->result();

				$delivery_areas=$this->get_delivery_area_details();

				foreach($orders as $key=>$val){

					$orders[$key]->client_number = '';

					$client_number = $this->db->get_where('client_numbers',array('company_id'=>$orders[$key]->company_id,'client_id'=>$orders[$key]->clients_id))->result_array();
					if(!empty($client_number)){
						$orders[$key]->client_number = $client_number['0']['client_number'];
					}

					/*foreach($countries as $country){
						if($country->id==$orders[$key]->country_id){
							$orders[$key]->country_name=$country->country_name;
							break;
						}else{
							$orders[$key]->country_name = '';
						}
					}*/

					foreach($delivery_areas as $delivery_area){

						if($delivery_area->delivery_areas_id == $orders[$key]->delivery_area){

							$orders[$key]->delivery_area_id = $delivery_area->delivery_area_id;
							$orders[$key]->delivery_area_name = $delivery_area->delivery_area_name;

							$orders[$key]->delivery_city_id = $delivery_area->id;
							$orders[$key]->delivery_city_name = $delivery_area->city_name;

						}

					}

					$orders[$key]->image =false;
					$this->db->select("image");
					$this->db->where( 'orders_id', $val->id );
					$this->db->where( 'image !=', '' );
					$this->db->group_start();
					$this->db->like( 'image', 'http:' );
					$this->db->or_like( 'image', 'https:' );
					$this->db->group_end();
					$order_detail = $this->db->get("order_details")->result_array();
					if(!empty($order_detail)){
						$orders[$key]->image = true;
					}
				}
			}
		return $orders;
	}

	function get_orders_pickup($id=null,$start_date=null,$end_date=null,$num=null,$offset=null,$where_params = array(), $company_id = NULL, $current_date = NULL, $is_ibsoft = NULL)
	{
		if($num)
		{
			$this->db->limit($num,$offset);
		}
		$this->db->select('
				orders.*,
				company.company_name,
				clients.id AS client_id,
				clients.firstname_c,
				clients.lastname_c,
				clients.company_c,
				clients.address_c,
				clients.housenumber_c,
				clients.postcode_c,
				clients.phone_c,
				clients.mobile_c,
				clients.city_c,
				clients.email_c,
				clients.country_id,
				clients.notifications,
				clients.created_c,
				clients.fax_c,
				clients.vat_c,
				clients.company_c,
				country.country_name as country_name,
				postcodes.area_name as delivery_city_name,
				states.state_name as delivery_area_name,
				payment_transaction.billing_option,
				delivery_country.country_name as delivery_country_name'
		);
		$this->db->distinct('id');
		// $this->db->join('delivery_areas','delivery_areas.id=orders.delivery_area','left');
		// $this->db->join('delivery_settings','delivery_settings.id=orders.delivery_city','left');
		$this->db->join('postcodes','postcodes.id=orders.delivery_city','left');
		$this->db->join('states','states.state_id=orders.delivery_area','left');

		$this->db->join('company','company.id=orders.company_id','left');
		$this->db->join('clients','clients.id=orders.clients_id');
		$this->db->join('country','clients.country_id = country.id','left');
		$this->db->join('country as delivery_country','orders.delivery_country = delivery_country.id','left');//used in delivery address
		$this->db->join('payment_transaction','payment_transaction.order_id=orders.temp_id','left');
		/*$this->db->join('client_numbers','client_numbers.client_id=orders.clients_id');
		$this->db->where('client_numbers.company_id',$this->company_id);*/

		if($start_date&&$end_date)
		{
			if($is_ibsoft)
				$this->db->where("( orders.order_pickupdate BETWEEN '" . $start_date . "' AND '" . $end_date."' OR orders.delivery_date BETWEEN '" . $start_date . "' AND '" . $end_date."' )");
			else
				$this->db->where("orders.order_pickupdate BETWEEN '" . $start_date . "' AND '" . $end_date."'");
		}
		else if($current_date)
		{
			$this->db->where("( orders.order_pickupdate = '".$current_date."' OR orders.delivery_date = '".$current_date."' )");
			//$this->db->or_where("orders.delivery_date",$current_date);
		}
		if($id)
		{
			$this->db->where(array('orders.id'=>$id));
		}
		elseif( $company_id )
		{
		    $this->db->where('orders.company_id',$company_id);
		}
		else
		{
			$this->db->where('orders.company_id',$this->company_id);
		}

		if(!empty($where_params))
		{
		    $this->db->where($where_params);
		}

		// Order that are not online payed, or if payed then must not be pending..
		$this->db->where("( payment_via_paypal = '0'  OR (payment_via_paypal != '0' AND payment_status = '1') )");

		$this->db->order_by("order_pickupdate", "desc");
		$orders=$this->db->get('orders')->result();

			if($orders){
				//$countries=$this->db->get('country')->result();

				$delivery_areas=$this->get_delivery_area_details();

				foreach($orders as $key=>$val){

					$orders[$key]->client_number = '';

					$client_number = $this->db->get_where('client_numbers',array('company_id'=>$orders[$key]->company_id,'client_id'=>$orders[$key]->clients_id))->result_array();
					if(!empty($client_number)){
						$orders[$key]->client_number = $client_number['0']['client_number'];
					}

					/*foreach($countries as $country){
						if($country->id==$orders[$key]->country_id){
							$orders[$key]->country_name=$country->country_name;
							break;
						}else{
							$orders[$key]->country_name = '';
						}
					}*/

					foreach($delivery_areas as $delivery_area){

						if($delivery_area->delivery_areas_id == $orders[$key]->delivery_area){

							$orders[$key]->delivery_area_id = $delivery_area->delivery_area_id;
							$orders[$key]->delivery_area_name = $delivery_area->delivery_area_name;

							$orders[$key]->delivery_city_id = $delivery_area->id;
							$orders[$key]->delivery_city_name = $delivery_area->city_name;

						}

					}

					$orders[$key]->image =false;
					$this->db->select("image");
					$this->db->where( 'orders_id', $val->id );
					$this->db->where( 'image !=', '' );
					$this->db->group_start();
					$this->db->like( 'image', 'http:' );
					$this->db->or_like( 'image', 'https:' );
					$this->db->group_end();
					$order_detail = $this->db->get("order_details")->result_array();
					if(!empty($order_detail)){
						$orders[$key]->image = true;
					}
				}
			}

		return $orders;
	}

	/*================function to get delivery area name and delivery city for the orders=====================*/

	function get_delivery_area_details(){

	$this->db->select('delivery_areas.area_name as delivery_area_name,delivery_areas.id as delivery_area_id,delivery_settings.*');
	$this->db->join('delivery_settings','delivery_settings.delivery_areas_id=delivery_areas.id');
	$this->db->where(array('company_id'=>$this->company_id));
	$result=$this->db->get('delivery_areas')->result();
	return $result;
	}

	/*=======================================================================================================*/
	function delete_order($id, $cancelled = false){

		$order_table			= ($cancelled)?'orders_tmp':'orders';
		$order_details_table	= ($cancelled)?'order_details_tmp':'order_details';

		if($id){
			$order_del = $this->db->delete($order_table,array('id'=>$id));
			if($order_del){
				$order_details_del = $this->db->delete($order_details_table,array("orders_id"=>$id));
				return true;
			}
		}
		return false;
	}

	function number2db($value)
	{
		$larr = localeconv();
		$search = array(
			$larr['decimal_point'],
			$larr['mon_decimal_point'],
			$larr['thousands_sep'],
			$larr['mon_thousands_sep'],
			$larr['currency_symbol'],
			$larr['int_curr_symbol']
		);
		$replace = array('.', '.', '', '', '', '');

		return str_replace($search, $replace, $value);
	}

	function update_order($id,$params,$cancelled = false){

		$order_table			= ($cancelled)?'orders_tmp':'orders';
		$order_details_table	= ($cancelled)?'order_details_tmp':'order_details';

		if($cancelled)
			$params['created_date'] = date('Y-m-d H:i:s',time());

	    if(isset($params['order_total']) && $id)
		  $params['order_total'] = $this->number2db($params['order_total']);

		$this->db->where(array('id'=>$id));
		$result = $this->db->update($order_table,$params);

		if($cancelled && $id){//Move order from orders_tmp to orders
			$order_details = $this->db->get_where($order_table,array('id'=>$id))->result();
			if(!empty($order_details)){
				$order_details_tmp = $this->db->get_where($order_details_table,array("orders_id"=>$id))->result();//Get order details

				unset($order_details[0]->id);
				$this->db->insert('orders',$order_details[0]);
				$insert_id = $this->db->insert_id();
				if($insert_id){
					if(!empty($order_details_tmp)){
						foreach($order_details_tmp as $insert_array){
							unset($insert_array->id);
							$insert_array->orders_id = $insert_id;//update new order id
							$this->db->insert('order_details',$insert_array);
						}
					}

					$order_details[0]->id = $insert_id;
					$this->delete_order($id,true);
				}
			}
		}

		if($result){
			return true;
		}else{
			return false;
		}
	}

	function delete_ordered_product($order_details_data_id,$order_id,$price){
		$order_det = $this->db->where(array('id'=>$order_details_data_id))->get('order_details')->row();
		$order_details_del = $this->db->delete('order_details',array("id"=>$order_details_data_id));

		if($order_details_del){
			$order = $this->db->where(array('id'=>$order_id))->get('orders')->row();
			$price = $order_det->total;
			$order_total = $order->order_total - $price;
			
			$disc_per_client_amount = $disc_amount = $disc_other_code = 0;
			if($order->disc_client > 0){
				$disc_per_client_amount = ( $order->disc_client * $order_total ) / 100;
			}
			
			if ($order_total > $order->disc_after_amount) {
				if($order->disc_percent > 0){
					$disc_amount = ( $order->disc_percent * ($order_total - $disc_per_client_amount) ) / 100;
				}
				elseif ($order->disc_price > 0) {
					$disc_amount = $order->disc_price;
				}
			}

			if($order->disc_promo_perc > 0){
				$disc_other_code = ( $order->disc_promo_perc * $order_total  ) / 100;
			}
			elseif ($order->disc_promo_price > 0) {
				$disc_other_code = $order->disc_promo_price;
			}
			
			$result = $this->update_order($order_id,array('order_total'=>$order_total , 'disc_amount' => $disc_amount, 'disc_client_amount' => $disc_per_client_amount, 'disc_other_code' => $disc_other_code));
			
			if($result){
				return true;
			}
		}
		return false;
	}

	function update_status($status,$order_id){
		$this->db->select('clients.email_c');
		$this->db->join('clients','clients.id = orders.clients_id','inner');
		$this->db->where(array('orders.id'=>$order_id));
		$email_c=$this->db->get('orders')->row();

		if($status == "y"){
			$this->app->redirect("index.php?view=orders&act=send_mail&email=".$email."&type=completed&id=".$order_id);
		}else if($status == "n"){
			$this->app->redirect("index.php?view=orders&act=send_mail&email=".$email."&type=hold&id=".$order_id);
		}
	}

	function get_desk_orders( $where = array(), $order_arr = array(), $num=null, $offset=null, $start_date = null, $end_date = null)
	{
		if($start_date&&$end_date){
			$this->db->where("created_date BETWEEN '" . $start_date . "' AND '" . $end_date."'");
		}

		if( !empty($where) )
		{
			foreach( $where as $key => $val )
				$this->db->where( $key, $val );
		}

		if( !empty($order_arr) )
			foreach( $order_arr as $key => $order )
			$this->db->order_by( $key, $order );

		if($num){
			$this->db->limit($num,$offset);
		}
		$this->db->select('desk_orders.*,clients.*, desk_orders.id as id,country.country_name');
		$this->db->join('clients','desk_orders.client_id = clients.id');
		$this->db->join('country','clients.country_id = country.id');

		$desk_orders = $this->db->get('desk_orders');
		return( $desk_orders->result() );
	}

	function update_desk_order( $where = array(), $update = array() )
	{
		if( empty($update) )
			return false;

		if( !empty($where) )
		{
			foreach( $where as $key => $val ){
				$this->db->where( $key, $val );
			}

			return $this->db->update( 'desk_orders', $update );
		}

		return false;
	}

	function delete_desk_order( $order_id = NULL )
	{
		if( $order_id )
		{
			$this->db->where( 'order_id' , $order_id );
			$this->db->delete( 'desk_order_details' );

			$this->db->where( 'id' , $order_id );
			$this->db->delete( 'desk_orders' );
		}
		else
			return false;
	}

	/**
	 * This function is used to fetch all saved reports of given company
	 * @param int $companyId It is the Id of company whose reports have to fetched.
	 * @return array $savedReports It is the array of saved reports.
	 */
	function get_saved_reports($companyId = null){
		$savedReports = array();
		if($companyId){
			$this->db->order_by("date","DESC");
			$savedReports = $this->db->get_where("saved_reports",array("company_id" => $companyId))->result_array();
		}
		return $savedReports;
	}

	/**
	 * This function is used to fetch orders of all companies for a given range of date
	 */
	function count_orders_all($start_date=null, $end_date=null, $current_date = null){

		if($start_date && $end_date){
			$this->db->where("orders.created_date BETWEEN '" . $start_date . "' AND '" . $end_date."'");
		}else if($current_date){
			$this->db->where("( orders.order_pickupdate = '".$current_date."' OR orders.delivery_date = '".$current_date."' )");
		}

		$this->db->from('orders');
		return $this->db->count_all_results();
	}

	function get_pending_orders_for_labeler($printer_orders = NULL){
		$this->db->select('orders.*,clients.id AS client_id,clients.firstname_c,clients.lastname_c,clients.company_c,clients.address_c,clients.housenumber_c,clients.postcode_c,clients.phone_c,clients.mobile_c,clients.city_c');
		$this->db->join('clients','clients.id=orders.clients_id');
		$this->db->where('orders.labeler_printed', 0);
		$this->db->where('orders.company_id', $this->company_id);
		if($printer_orders !=NULL && $printer_orders == 1){
			$this->db->where("( order_pickupdate = '".date('Y-m-d')."' OR delivery_date = '".date('Y-m-d')."')" );
		}
		return $this->db->get('orders')->result();
	}


	/**
	 * @name get_saved_reports_per_client
	 * @property This function is used to fetch all saved reports of given company per client basis
	 * @param int $companyId It is the Id of company whose reports have to fetched.
	 * @return array $savedReports It is the array of saved reports.
	 */
	function get_saved_reports_per_client($companyId = null){
		$savedReports = array();
		if($companyId){
			$this->db->order_by("date","DESC");
			$savedReports = $this->db->get_where("saved_reports_per_client",array("company_id" => $companyId))->result_array();
		}
		return $savedReports;
	}

	/**
	 * @name get_orders_per_client
	 * @author Priyanka Srivastava
	 * @property Function to return an array or orders as per client of a client..
	 * @param $start_date starting date
	 * @param $end_date ending date
	 * @return array|mixed order data arranged by per-clients basis
	 *
	 */
	function get_orders_per_client($start_date = null, $end_date = null, $hide_zero_orders = null)
	{
		$orderdata = array();
		$company_id = $this->company_id;

		//fetching clients of current company..
		$this->db->select('client_numbers.*');
		$this->db->where('client_numbers.company_id', $company_id);
		$clients =  $this->db->get('client_numbers')->result();

		if(!empty($clients)){
			//looping clients..
			$i = 0;
			$end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
			foreach($clients as $client){

				//fetching client details..
				$this->db->select('*');
				$this->db->where('id', $client->client_id);
				$client_details =  $this->db->get('clients')->result();

				//fetching orders of this client for this company..
				//$this->db->select('orders.created_date, SUM(orders.order_total ) AS order_total, orders.* ');
				$this->db->distinct('DATE(orders.created_date)');
				$this->db->select('orders.created_date, SUM((orders.order_total + orders.pic_apply_tax + orders.del_apply_tax + orders.delivery_cost) - (orders.disc_amount + orders.disc_client) ) AS order_total1, orders.* ');

				$this->db->where('company_id', $company_id);
				$this->db->where('clients_id', $client->client_id);
				//$this->db->where('created_date BETWEEN ' . $start_date . ' AND ' . $end_date);
				$this->db->where('created_date >=', $start_date);
				$this->db->where('created_date <', $end_date);
				$this->db->group_by('DATE(orders.created_date)');
				$this->db->order_by('id','DESC');
				$orders =  $this->db->get('orders')->result();
				if(!empty($client_details)){
				if(($hide_zero_orders != null) && ($hide_zero_orders == 'hide_zero_orders')){
					if(!empty($orders) && $orders[0]->id != ''){

						//appending rows in orderdata with client details..
						$orderdata[] = array(
								'client_details' => $client_details,
								'order_details' => $orders
						);
					}
				} else {
					$orderdata[] = array(
							'client_details' => $client_details,
							'order_details' => $orders
					);
				}

				}


			}

		}

		return $orderdata;

	}
	function get_orders_per_client_pickup($start_date = null, $end_date = null, $hide_zero_orders = null){
		$orderdata = array();
		$company_id = $this->company_id;

		//fetching clients of current company..
		$this->db->select('client_numbers.*');
		$this->db->where('client_numbers.company_id', $company_id);
		$clients =  $this->db->get('client_numbers')->result();

		if(!empty($clients)){
			//looping clients..
			$i = 0;
			$end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
			foreach($clients as $client){

				//fetching client details..
				$this->db->select('*');
				$this->db->where('id', $client->client_id);
				$client_details =  $this->db->get('clients')->result();

				//fetching orders of this client for this company..
				//$this->db->select('orders.created_date, SUM(orders.order_total ) AS order_total, orders.* ');
				$this->db->select('orders.created_date, SUM((orders.order_total + orders.pic_apply_tax + orders.del_apply_tax + orders.delivery_cost) - (orders.disc_amount + orders.disc_client) ) AS order_total1, orders.* ');

				$this->db->where('company_id', $company_id);
				$this->db->where('clients_id', $client->client_id);
				//$this->db->where('created_date BETWEEN ' . $start_date . ' AND ' . $end_date);
				$this->db->where('order_pickupdate >=', $start_date);
				$this->db->where('order_pickupdate <', $end_date);
				$this->db->group_by('DATE(orders.created_date)');

				$this->db->order_by('id','DESC');
				$orders =  $this->db->get('orders')->result();
				if(!empty($client_details))
				{

					if(($hide_zero_orders != null) && ($hide_zero_orders == 'hide_zero_orders'))
					{
						if(!empty($orders) && $orders[0]->id != '')
						{

						//appending rows in orderdata with client details..
						$orderdata[] = array(
								'client_details' => $client_details,
								'order_details' => $orders
							);
						}
					}
					else
					{

						$orderdata[] = array(
							'client_details' => $client_details,
							'order_details' => $orders
						);
					}
				}
			}

		}
		return $orderdata;
	}

	function get_orders_per_client_by($searchBy=null, $searchKeyword=null, $hide_zero_orders = null){
		$orderdata = array();
		$company_id = $this->company_id;

		//fetching clients of current company..
		$this->db->select('client_numbers.*');
		if($searchBy=='Name')
		{
			$this->db->join('clients', 'clients.id = client_numbers.client_id');
			$this->db->where('client_numbers.company_id', $company_id);
			$this->db->where('(firstname_c LIKE "'.$searchKeyword.'" OR lastname_c LIKE "'.$searchKeyword.'")');
			$clients =  $this->db->get('client_numbers')->result();
		}
		elseif($searchBy=='Email')
		{
			$this->db->join('clients', 'clients.id = client_numbers.client_id');
			$this->db->where('client_numbers.company_id', $company_id);
			$this->db->where(array( 'email_c LIKE' => '%'.$searchKeyword.'%' ));
			$clients =  $this->db->get('client_numbers')->result();
		}
		elseif($searchBy=='order_number')
		{
			$this->db->join('orders', 'orders.clients_id = client_numbers.client_id');
			$this->db->where('client_numbers.company_id', $company_id);
			$this->db->where(array('orders.id'=>$searchKeyword));
			$clients =  $this->db->get('client_numbers')->result();
		}
		if(!empty($clients)){
			//looping clients..
			$i = 0;
			foreach($clients as $client){

				//fetching client details..
				$this->db->select('*');
				$this->db->where('id', $client->client_id);
				$client_details =  $this->db->get('clients')->result();

				//fetching orders of this client for this company..
				//$this->db->select('orders.created_date, SUM(orders.order_total ) AS order_total, orders.* ');
				/*$this->db->select('orders.created_date, SUM((orders.order_total + orders.pic_apply_tax + orders.del_apply_tax + orders.delivery_cost) - (orders.disc_amount + orders.disc_client) ) AS order_total1, orders.* ');

				$this->db->where('company_id', $company_id);
				$this->db->where('clients_id', $client->client_id);
				//$this->db->where('created_date BETWEEN ' . $start_date . ' AND ' . $end_date);
				$this->db->where('created_date >=', $start_date);

				$end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));

				$this->db->where('created_date <', $end_date);*/
				$this->db->select('*,SUM((orders.order_total + orders.pic_apply_tax + orders.del_apply_tax + orders.delivery_cost) - (orders.disc_amount + orders.disc_client) ) AS order_total1');

				$this->db->where('clients_id',$client->client_id);
				if ($searchBy=='order_number'){
					$this->db->where('id',$searchKeyword);
				}
				$this->db->where("( payment_via_paypal = '0'  OR (payment_via_paypal != '0' AND payment_status = '1') )");
				$this->db->where('company_id',$company_id);
				//$this->db->group_by('DATE(orders.created_date)');
				$this->db->order_by('id','DESC');
				$orders =  $this->db->get('orders')->result();

				//echo $this->db->last_query();

				if(!empty($client_details)){

				if(($hide_zero_orders != null) && ($hide_zero_orders == 'hide_zero_orders')){
					if(!empty($orders)){

						//appending rows in orderdata with client details..
						$orderdata[] = array(
								'client_details' => $client_details,
								'order_details' => $orders
						);
					}
				} else {
					$orderdata[] = array(
							'client_details' => $client_details,
							'order_details' => $orders
					);
				}

				}


			}

		}
		return $orderdata;

	}


	/**
	 * Function to fetch Orders that are subjected to pay online but are still pending..
	 *
	 */
	function get_pending_orders($id=null,$num = null, $offset = null, $company_id = NULL){

		if($num){
			$this->db->limit($num,$offset);
		}

		$this->db->select('
				orders.id,
				orders.company_id,
				orders.order_total,
				orders.order_remarks,
				orders.order_pickupdate,
				orders.order_pickupday,
				orders.order_pickuptime,
				orders.delivery_streer_address,
				orders.delivery_area,
				orders.delivery_city,
				orders.delivery_zip,
				orders.delivery_day,
				orders.delivery_hour,
				orders.delivery_minute,
				orders.delivery_date,
				orders.delivery_remarks,
				orders.delivery_cost,
				orders.del_apply_tax,
				orders.del_tax_amount_added,
				orders.pic_apply_tax,
				orders.pic_tax_amount_added,
				orders.created_date,
				orders.subadmin_id,
				orders.disc_percent,
				orders.disc_price,
				orders.disc_amount,
				orders.disc_client,
				orders.disc_client_amount,
				orders.disc_other_code,
				orders.other_code_type,
				orders.get_invoice,
				orders.name,
				orders.option,
				orders.phone_reciever,
				company.company_name,
				clients.id AS client_id,
				clients.firstname_c,
				clients.lastname_c,
				clients.company_c,
				clients.address_c,
				clients.housenumber_c,
				clients.postcode_c,
				clients.phone_c,
				clients.mobile_c,
				clients.city_c,
				clients.email_c,
				clients.country_id,
				clients.notifications,
				clients.created_c,
				clients.fax_c,
				clients.vat_c,
				clients.company_c,
				country.country_name as country_name,
				delivery_country.country_name as delivery_country_name
		');

		//$this->db->join('delivery_areas','delivery_areas.id=orders.delivery_area','left');
		$this->db->join('delivery_settings','delivery_settings.id=orders.delivery_city','left');
		$this->db->join('company','company.id=orders.company_id','left');
		$this->db->join('clients','clients.id=orders.clients_id');
		$this->db->join('country','clients.country_id = country.id','left');//used in client details popup
		$this->db->join('country as delivery_country','orders.delivery_country = delivery_country.id','left');//used in delivery address


		if($id){
			$this->db->where(array('orders.id'=>$id));
		}

		if( $company_id )
		{
			$this->db->where('orders.company_id',$company_id);
		}
		else
		{
			$this->db->where('orders.company_id',$this->company_id);
		}


		// Order that are not online payed, or if payed then must not be pending..
		$this->db->where("payment_via_paypal != '0' AND payment_status != '1'");

		$this->db->order_by("created_date", "desc");
		$orders=$this->db->get('orders')->result();

		if($orders){

			//$delivery_areas = $this->get_delivery_area_details();

			foreach($orders as $key=>$val){

				/*foreach($delivery_areas as $delivery_area){

					if($delivery_area->delivery_areas_id == $orders[$key]->delivery_area){

						$orders[$key]->delivery_area_id = $delivery_area->delivery_area_id;
						$orders[$key]->delivery_area_name = $delivery_area->delivery_area_name;

						$orders[$key]->delivery_city_id = $delivery_area->id;
						$orders[$key]->delivery_city_name = $delivery_area->city_name;

					}

				}*/

				$orders[$key]->image =false;
				$this->db->select("image");
				$this->db->where( 'orders_id', $val->id );
				$this->db->where( 'image !=', '' );
				$this->db->group_start();
				$this->db->like( 'image', 'http:' );
				$this->db->or_like( 'image', 'https:' );
				$this->db->group_end();
				$order_detail = $this->db->get("order_details")->result_array();
				if(!empty($order_detail)){
					$orders[$key]->image = true;
				}
			}
		}

		return $orders;
	}


	function get_empty_ingredient_products($c_id, $p_id = 0){
		if($p_id == 0){
		 	RETURN $this->db->query('SELECT products.id,products.fdd_supplier_id,products.fdd_producer_id,products.fdd_prod_art_num,products.fdd_supp_art_num, products.proname, products_pending.prosheet_pws, contacted_via_mail.refused, contacted_via_mail.remark_refused FROM products JOIN products_pending ON products.id = products_pending.product_id JOIN contacted_via_mail ON contacted_via_mail.obs_pro_id = products.id WHERE products_pending.company_id = '.$c_id)->result();
		}else{
			RETURN $this->db->query('SELECT products.proname, products.id,products.fdd_supplier_id,products.fdd_producer_id,products.fdd_prod_art_num,products.fdd_supp_art_num, products_pending.prosheet_pws, contacted_via_mail.refused, contacted_via_mail.remark_refused FROM products JOIN products_pending ON products.id = products_pending.product_id JOIN contacted_via_mail ON contacted_via_mail.obs_pro_id = products.id WHERE products_pending.company_id = '.$c_id.' AND products.id ='.$p_id)->result();
		}
	}

	/*function get_empty_ingredient_products_count($c_id){

		RETURN $this->db->query('SELECT COUNT(product_id) as t_count FROM products_pending WHERE company_id = '.$c_id)->result_array();

	}*/

	function get_custom_pending_products_count($c_id){
		$this->db->where(array('company_id'=>$c_id,'is_custom_pending'=>1));
		RETURN $this->db->get('products')->num_rows();
	}

	/**
	 * Function to fetch Orders that are subjected to pay online but cancelled before payment..
	 *
	 */
	function get_cancelled_orders($id=null,$num = null, $offset = null, $company_id = NULL){

		if($num){
			$this->db->limit($num,$offset);
		}

		$this->db->select('
				orders_tmp.id,
				orders_tmp.company_id,
				orders_tmp.order_total,
				orders_tmp.order_remarks,
				orders_tmp.order_pickupdate,
				orders_tmp.order_pickupday,
				orders_tmp.order_pickuptime,
				orders_tmp.delivery_streer_address,
				orders_tmp.delivery_area,
				orders_tmp.delivery_city,
				orders_tmp.delivery_zip,
				orders_tmp.delivery_day,
				orders_tmp.delivery_hour,
				orders_tmp.delivery_minute,
				orders_tmp.delivery_date,
				orders_tmp.delivery_remarks,
				orders_tmp.delivery_cost,
				orders_tmp.del_apply_tax,
				orders_tmp.del_tax_amount_added,
				orders_tmp.pic_apply_tax,
				orders_tmp.pic_tax_amount_added,
				orders_tmp.created_date,
				orders_tmp.subadmin_id,
				orders_tmp.disc_percent,
				orders_tmp.disc_price,
				orders_tmp.disc_other_code,
				orders_tmp.other_code_type,
				orders_tmp.disc_amount,
				orders_tmp.disc_client,
				orders_tmp.disc_client_amount,
				orders_tmp.get_invoice,
				orders_tmp.name,
				orders_tmp.option,
				orders_tmp.phone_reciever,
				orders_tmp.cancel_mail_status,
				company.company_name,
				clients.id AS client_id,
				clients.firstname_c,
				clients.lastname_c,
				clients.company_c,
				clients.address_c,
				clients.housenumber_c,
				clients.postcode_c,
				clients.phone_c,
				clients.mobile_c,
				clients.city_c,
				clients.email_c,
				clients.country_id,
				clients.notifications,
				clients.created_c,
				clients.fax_c,
				clients.vat_c,
				clients.company_c,
				country.country_name as country_name,
				delivery_country.country_name as delivery_country_name
		');

		//$this->db->join('delivery_areas','delivery_areas.id=orders.delivery_area','left');
		$this->db->join('delivery_settings','delivery_settings.id=orders_tmp.delivery_city','left');
		$this->db->join('company','company.id=orders_tmp.company_id','left');
		$this->db->join('clients','clients.id=orders_tmp.clients_id');
		$this->db->join('country','clients.country_id = country.id','left');//used in client details popup
		$this->db->join('country as delivery_country','orders_tmp.delivery_country = delivery_country.id','left');//used in delivery address

		if($id){
			$this->db->where(array('orders_tmp.id'=>$id));
		}
		else{
			$this->db->where('(0 = (SELECT COUNT(`orders`.`id`) FROM `orders` WHERE `orders`.`temp_id` = `orders_tmp`.`id`))');
		}

		if( $company_id )
		{
			$this->db->where('orders_tmp.company_id',$company_id);
		}
		else
		{
			$this->db->where('orders_tmp.company_id',$this->company_id);
		}

		// Order that are not online payed, or if payed then must not be pending..
		$this->db->where("payment_via_paypal != '0' AND payment_status != '1'");

		$this->db->order_by("created_date", "desc");
		$orders=$this->db->get('orders_tmp')->result();
		if($orders){

			//$delivery_areas = $this->get_delivery_area_details();

			foreach($orders as $key=>$val){

				/*foreach($delivery_areas as $delivery_area){

				if($delivery_area->delivery_areas_id == $orders[$key]->delivery_area){

				$orders[$key]->delivery_area_id = $delivery_area->delivery_area_id;
				$orders[$key]->delivery_area_name = $delivery_area->delivery_area_name;

				$orders[$key]->delivery_city_id = $delivery_area->id;
				$orders[$key]->delivery_city_name = $delivery_area->city_name;

				}

				}*/

				$orders[$key]->image =false;
				$this->db->select("image");
				$this->db->where( 'orders_id', $val->id );
				$this->db->where( 'image !=', '' );
				$this->db->group_start();
				$this->db->like( 'image', 'http:' );
				$this->db->or_like( 'image', 'https:' );
				$this->db->group_end();
				$order_detail = $this->db->get("order_details")->result_array();
				if(!empty($order_detail)){
					$orders[$key]->image = true;
				}
			}
		}

		return $orders;
	}

	function get_gs1_products_recipe( $company_id ) {

		$this->db->distinct( 'gs1_pid' );
		$this->db->select( 'gs1_pid' );
		$this->db->where( 'company_id', $company_id );
		$this->db->where( 'request_status', 1 );
		$gs1_pds = $this->db->get( 'request_gs1' )->result_array();

		if( !empty( $gs1_pds ) ) {
			$gs1_pds = array_column( $gs1_pds, 'gs1_pid' );

			$this->fdb->select( 'p_id, p_name'.$this->lang.' as p_name' );
			$this->fdb->where_in( 'p_id', $gs1_pds );
			$this->fdb->where( 'approval_status', 0 );
			$result = $this->fdb->get( 'products' )->result_array();

			foreach ( $result as $key => $value ) {
				$this->db->distinct( 'obs_pro_id' );
				$this->db->select( 'products.id, products.proname' );
				$this->db->join( 'products', 'fdd_pro_quantity.obs_pro_id = products.id' );
				$this->db->where( 'fdd_pro_quantity.fdd_pro_id', $value[ 'p_id' ] );
				$this->db->where( 'products.company_id', $company_id );
				$prod_details 	= $this->db->get( 'fdd_pro_quantity' )->result_array();

				if( !empty( $prod_details ) ) {
					$proname 		= array_column( $prod_details, 'proname' );
					$result[ $key ][ 'in_recipe' ] = implode( ", ", $proname );
				} else {
					unset( $result[ $key ] );
				}
			}
			$result = array_values( $result );
			return $result;
		}
		return false;
	}
}
?>