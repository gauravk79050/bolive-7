<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_c_ap extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	var $template = NULL;

	public function __construct()
	{
		parent::__construct();
    $this->fdb = $this->load->database('fdb',TRUE);
	}


	public function index(){
		$arr = array(1,2,3,4,5);

		unset($arr[2]);

		print_r(json_decode(json_encode($arr)));

    	/*$data['content'] = 'labeler_test';
    	$this->load->view('cp/cp_view',$data);*/
    	// $this->load->view('labeler_test');
    }

    public function print_auto_labeler($type = 'per_ordered_product',$timestamp = null){

    	$response = array();
    	$general_settings = $this->get_general_settings();
		// $this->load->model('Morders');
    	$orders = $this->get_pending_orders_for_labeler($general_settings[0]->printer_orders);
		/*echo count($orders);
		print_r($orders); die;*/
		if(!empty($orders)){

			if($general_settings['0']->activate_labeler){
				$labeler_array = array();
				$order_id_array = array();
				$count = 0;
				foreach ($orders as $order){
					$doc_text = array();
					$order_id_array[] = $order_id = $order->id;
					if($type == 'per_order' || $type == 'all'){
						// Adding Order ID
						$doc_text['order_id'] = $order_id;

						// Adding user name and total amount
						$doc_text['name'] = str_replace('&','',$order->firstname_c." ".$order->lastname_c);

						// Adding Total amount
						$total_price = round($order->order_total,2);
						$total_price = (string)$total_price;
						//$total_price = str_replace('.',',',$total_price);
						$doc_text['amount'] = $total_price;

						// Adding address
						$add_text = '';
						$add_text .= addslashes($order->address_c)." ".addslashes($order->housenumber_c)."\n".addslashes($order->postcode_c)." ".$order->city_c."\n";

						// Adding phone number
						if($order->phone_c){
							$add_text .= $order->phone_c."\n";
						}elseif($order->mobile_c){
							$add_text .= $order->mobile_c."\n";
						}

						$doc_text['address'] = $add_text;

						$remark = $order->order_remarks;
						if(strlen($remark) >= 100){
							$remark = substr($order->order_remarks,0,100)."...";
						}
						$remark = addslashes($remark);

						$doc_text['remark'] = '';
						if($remark != ''){
							$doc_text['remark'] = _("Opmerking").": ".$remark;
						}

						// Adding pickup date
						$date_content = '--';
						if($order->option == 1){



							$pickup_content = date("d / m",strtotime($order->order_pickupdate))." om ".$order->order_pickuptime;

							$pickup_day = date("D",strtotime($order->order_pickupdate));

							if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
							{
								if( $pickup_day == 'Mon' )
									$pickup_day = 'Ma';
								if( $pickup_day == 'Tue' )
									$pickup_day = 'Di';
								if( $pickup_day == 'Wed' )
									$pickup_day = 'Wo';
								if( $pickup_day == 'Thu' )
									$pickup_day = 'Do';
								if( $pickup_day == 'Fri' )
									$pickup_day = 'Vr';
								if( $pickup_day == 'Sat' )
									$pickup_day = 'Za';
								if( $pickup_day == 'Sun' )
									$pickup_day = 'Zo';
							}

							$date_content = _("Pickup")."\n".$pickup_day.' '.$pickup_content;

						}elseif($order->option == 2){

							$delivery_date_content =date("d/m",strtotime($order->delivery_date))." om ".$order->delivery_hour.":".$order->delivery_minute;

							$delivery_day = date("D",strtotime($order->delivery_date));

							if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
							{
								if( $delivery_day == 'Mon' )
									$delivery_day = 'Ma';
								if( $delivery_day == 'Tue' )
									$delivery_day = 'Di';
								if( $delivery_day == 'Wed' )
									$delivery_day = 'Wo';
								if( $delivery_day == 'Thu' )
									$delivery_day = 'Do';
								if( $delivery_day == 'Fri' )
									$delivery_day = 'Vr';
								if( $delivery_day == 'Sat' )
									$delivery_day = 'Za';
								if( $delivery_day == 'Sun' )
									$delivery_day = 'Zo';
							}

							$date_content = _("Delivery")."\n".$delivery_day.' '.$delivery_date_content;
						}
						$doc_text['date'] = $date_content;

						$labeler_array[$count]['items'] = $doc_text;
						$labeler_array[$count]['type'] = 'per_order';
						$count++;

					}

					if($type == 'per_ordered_product' || $type == 'all'){

						/*$this->load->model('Morder_details');
						$this->load->model('Morders');*/

						$order_data_pre = $this->get_orders($order_id);
						/*echo $order_id; die;
						print_r($order_data_pre);*/
						$order_data = $this->get_order_details($order_id);
						foreach($order_data as $values){

							$quantity_unit = '';
							if($values->content_type != 1 && $values->content_type != 2){
								for($j = 0; $j < (int)$values->quantity; $j++){
									$doc_text = array();

									$doc_text['c_name'] = str_replace("&","",$order_data_pre['0']->firstname_c." ".$order_data_pre['0']->lastname_c);
									$doc_text['company'] = $order_data_pre['0']->company_c;
									$total_price = round($values->total,2)/$values->quantity;
									$total_price = (string)$total_price;
									$total_price = str_replace('.',',',$total_price);

									// Adding product name and total amount
									$doc_text['name'] = $values->proname;
									$doc_text['default_price'] = round($values->default_price,2);
									$doc_text['amount'] = $total_price;

									// Adding groups/options
									$doc_text['extra'] = '';
									if($values->add_costs != ''){
										$grp_text = '';
										$extra_array = explode("#",$values->add_costs);
										foreach($extra_array as $extra_value){
											$extra_value_array = explode("_",$extra_value);
											$grp_text .= $extra_value_array['0'].':'.$extra_value_array['1'].'='.$extra_value_array['2']."\n";
										}
										$doc_text['extra'] = $grp_text;
									}

									// Adding remark
									$remark = $values->pro_remark;
									$length = strlen($remark);
									$loop = floor($length/50);
									for($counter = 1; $counter <= $loop; $counter++){
										$remark = substr_replace($remark,"\n",($counter*50),0);
									}

									$doc_text['remark'] = '';
									if($remark != ''){
										$doc_text['remark'] = "Opmerking: ".addslashes($remark);
									}

									$doc_text['extra_field_text'] = '';
									if($values->extra_field != '')
										$doc_text['extra_field_text'] = $values->extra_field.' : '.$values->extra_name;

									$labeler_array[$count]['items'] = $doc_text;
									$labeler_array[$count]['type'] = 'per_ordered_product';
									$count++;
								}
							}else{
								if($values->content_type == 1)
									$quantity_unit = 'gr.';
								if($values->content_type == 2)
									$quantity_unit = 'pers.';

								$doc_text = array();

								$doc_text['c_name'] = str_replace('&','',$order_data_pre['0']->firstname_c." ".$order_data_pre['0']->lastname_c);
								$doc_text['company'] = $order_data_pre['0']->company_c;
								$total_price = round($values->default_price,2);
								$total_price = (string)$total_price;
								$total_price = str_replace('.',',',$total_price);

								// Adding product name and total amount
								$quantity_unit = 'x';
								if($values->content_type == 1)
									$quantity_unit = 'gr.';
								if($values->content_type == 2)
									$quantity_unit = 'pers.';
								$doc_text['name'] = $values->quantity." ".$quantity_unit." ".$values->proname;
								$doc_text['default_price'] = round($values->default_price,2);
								$doc_text['amount'] = $total_price;

								// Adding groups/options
								$doc_text['extra'] = '';
								if($values->add_costs != ''){
									$grp_text = '';
									$extra_array = explode("#",$values->add_costs);
									foreach($extra_array as $extra_value){
										$extra_value_array = explode("_",$extra_value);
										$grp_text .= $extra_value_array['0'].':'.$extra_value_array['1'].'='.$extra_value_array['2']."\n";
									}
									$doc_text['extra'] = $grp_text;
								}

								// Adding remark
								$remark = $values->pro_remark;
								$length = strlen($remark);
								$loop = floor($length/50);
								for($counter = 1; $counter <= $loop; $counter++){
									$remark = substr_replace($remark,"\n",($counter*50),0);
								}

								$doc_text['remark'] = '';
								if($remark != ''){
									$doc_text['remark'] = "Opmerking: ".addslashes($remark);
								}

								$doc_text['extra_field_text'] = '';
								if($values->extra_field != '')
									$doc_text['extra_field_text'] = $values->extra_field.' : '.$values->extra_name;

								$labeler_array[$count]['items'] = $doc_text;
								$labeler_array[$count]['type'] = 'per_ordered_product';
								$count++;
							}
						}
					}
				}

				/*$this->db->where_in('id',$order_id_array);
				$this->db->update('orders', array('labeler_printed' => 1));*/

				$response = array('error'=>0,'message'=>'array','data'=>$labeler_array);
			}else{
				$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
			}
		}else{
			$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
		}
		echo json_encode($response);

	}


	function get_pending_orders_for_labeler($printer_orders = NULL){
		$this->db->select('orders.*,clients.id AS client_id,clients.firstname_c,clients.lastname_c,clients.company_c,clients.address_c,clients.housenumber_c,clients.postcode_c,clients.phone_c,clients.mobile_c,clients.city_c');
		$this->db->join('clients','clients.id=orders.clients_id');
		// $this->db->where('orders.labeler_printed', 0);
		$this->db->where('orders.company_id', 113);

		/*if($printer_orders !=NULL && $printer_orders == 1){
			$this->db->where("( order_pickupdate = '".date('Y-m-d')."' OR delivery_date = '".date('Y-m-d')."')" );
		}*/
		$this->db->like("created_date",'2014-12-01');
		$this->db->limit(5);
		return $this->db->get('orders')->result();
	}

	function get_general_settings($params=array('company_id' => 113), $select = null)
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
		//print_r($query);
		return($query);

	}

	function get_orders_per_client($start_date = null, $end_date = null, $hide_zero_orders = null){
		$orderdata = array();
		$company_id = 113;

		//fetching clients of current company..
		$this->db->select('client_numbers.*');
		$this->db->where('client_numbers.company_id', $company_id);
		$clients =  $this->db->get('client_numbers')->result();

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
				$this->db->select('orders.created_date, SUM((orders.order_total + orders.pic_apply_tax + orders.del_apply_tax + orders.delivery_cost) - (orders.disc_amount + orders.disc_client) ) AS order_total1, orders.* ');

				$this->db->where('company_id', $company_id);
				$this->db->where('clients_id', $client->client_id);
				//$this->db->where('created_date BETWEEN ' . $start_date . ' AND ' . $end_date);
				$this->db->where('created_date >=', $start_date);

				$end_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));

				$this->db->where('created_date <', $end_date);
				$this->db->group_by('DATE(orders.created_date)');

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

	function get_order_details($orders_id,$cancelled = false){

		$order_details_table = (($cancelled)?'order_details_tmp':'order_details');

		//echo $orders_id;
		if($orders_id){
			$this->db->select($order_details_table.'.*,products.id as proid,products.company_id,products.categories_id,products.subcategories_id,products.pro_art_num,products.proname,products.price_per_unit,products.price_weight,products.type,products.discount');
			$this->db->join('products',$order_details_table.'.products_id=products.id');
			$this->db->where(array($order_details_table.'.orders_id'=>$orders_id));
			$order_details = $this->db->get($order_details_table)->result();


			if(!empty($order_details)){
				foreach($order_details as $key=>$val){
					if($order_details[$key]->discount == 'multi'){
						$discounts = $this->db->where(array('products_id'=>$order_details[$key]->products_id))->order_by('quantity','asc')->get('products_discount')->result();
						foreach($discounts as $discount){
							if($order_details[$key]->quantity >= $discount->quantity){

								$order_details[$key]->discount_per_qty = $discount->discount_per_qty;
								$order_details[$key]->price_per_qty = $discount->price_per_qty;
								$order_details[$key]->discount_on_items = $order_details[$key]->quantity - ($order_details[$key]->quantity % $discount->quantity);

							}else if($order_details[$key]->quantity == '1'){

								$order_details[$key]->discount_per_qty = 'No discount';
								$order_details[$key]->price_per_qty = ($order_details[$key]->content_type==1)?$order_details[$key]->price_weight:$order_details[$key]->price_per_unit;

							}//end of if
						}//end of foreach of discount
					}//end of if
					// $this->load->model('Mgroups_products');
					$products_id = $order_details[$key]->products_id;
					$order_details[$key]->product_groups = $this->get_product_group($products_id);
				}//end of foreach
			}//end of if
		}//end of if
		//print_r($order_details);
		return $order_details;
	}

	function get_orders($id=null,$start_date=null,$end_date=null,$num=null,$offset=null,$where_params = array(), $company_id = NULL, $current_date = NULL, $is_ibsoft = NULL){

		if($num){
			$this->db->limit($num,$offset);
		}
		$this->db->select('
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
			payment_transaction.billing_option
			');

		// $this->db->join('delivery_areas','delivery_areas.id=orders.delivery_area','left');
		// $this->db->join('delivery_settings','delivery_settings.id=orders.delivery_city','left');
		$this->db->join('postcodes','postcodes.id=orders.delivery_city','left');
		$this->db->join('states','states.state_id=orders.delivery_area','left');

		$this->db->join('company','company.id=orders.company_id','left');
		$this->db->join('clients','clients.id=orders.clients_id');
		$this->db->join('country','clients.country_id = country.id','left');
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
			$this->db->where('orders.company_id',113);
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
			$order_detail = $this->db->get_where("order_details",array("orders_id" => $val->id))->result_array();
			if(!empty($order_detail)){
				foreach($order_detail as $image_detail){
					if($image_detail['image'] != '' && ( strpos($image_detail['image'],"http:") !== false || strpos($image_detail['image'],"https:") !== false )){
						$orders[$key]->image = true;
						break;
					}
				}
			}
		}
	}

	return $orders;
}

function get_delivery_area_details(){

	$this->db->select('delivery_areas.area_name as delivery_area_name,delivery_areas.id as delivery_area_id,delivery_settings.*');
	$this->db->join('delivery_settings','delivery_settings.delivery_areas_id=delivery_areas.id');
	$this->db->where(array('company_id'=>$this->company_id));
	$result=$this->db->get('delivery_areas')->result();
	return $result;
}

function get_product_group($product_id){
		//echo $product_id;
	$groups = $this->db->select('id')->where('company_id',$this->company_id)->get('groups')->result();
	$this->db->select('groups.id as group_id,groups.group_name,groups.type,groups_products.*');
	$this->db->join('groups','groups.id=groups_products.groups_id','inner');
	$this->db->where('groups_products.products_id',$product_id);
	$groups_products = $this->db->get('groups_products')->result();
	if($groups_products){
		foreach($groups_products as $group_product){

			$attributes[$group_product->group_id][]=array('group_name'=>$group_product->group_name,
				'attribute_name'=>$group_product->attribute_name,
				'attribute_value'=>$group_product->attribute_value,
				'type'=>$group_product->type
			);


		}
	}else{

		$attributes='';

	}
		//print_r($attributes);
	return $attributes;
		//die();

}

function check_status($merchant_id = ''){
	$this->load->helper('curo');
	$response = check_status($merchant_id);
	print_r($response);

		/*$sSecret =  'MySecretKey';

		// Arguments to the API call
		$aData  = Array(
				'action' =>  'status',
				'format' =>  'serialized',
				'hash' => 'Y8f0cnqB0WKAKZ1WwHTFNL2jge2pzOzXuEnBNHfWAuLNroo3Fjbm4iTJzcWqrR7l',
				'merchant_id' =>  'sitematic',
				'id' => $merchant_id
		);

		// Sort by array key
		ksort( $aData );

		// Build the hash and overwrite original secret with assign
		$aData['hash' ] = md5(  implode('|' , $aData) );

		// Build API URL to idealpro module
		$sURL  = 'https://secure.curopayments.net/v1/api/merchants/?' .http_build_query( $aData );

		$rCh = curl_init();
		curl_setopt( $rCh, CURLOPT_URL, $sURL );
		//curl_setopt( $rCh, CURLOPT_PORT, $iPort_ );
		//curl_setopt( $rCh, CURLOPT_SSL_VERIFYPEER, FALSE );
		//curl_setopt( $rCh, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $rCh, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $rCh, CURLOPT_TIMEOUT, 60 );
		curl_setopt( $rCh, CURLOPT_HEADER, FALSE );
		curl_setopt( $rCh, CURLOPT_POST, TRUE );
		curl_setopt( $rCh, CURLOPT_POSTFIELDS, $aData_ );
		$sResults = curl_exec( $rCh );

		print_r($sResults);*/


	}

	function test(){
		$this->load->helper('phpmailer');

		$fname = "Testsss";
		$username = "Testsss";
		$email = "noreply@fooddesk.be";
		$tel = "Test";

		$message = '
		<html>
		<head>
		<title>CONTACTEER-INVULFORMULIER</title>
		<style>
		td{text-align:left; padding:5px;}
		h2{font-size:12px;}
		</style>
		</head>
		<body>
		<h2>CONTACTEER:</h2>
		<table>

		<tr>
		<td align="left">Naam :</td>
		<td>'.$fname.'</td>
		</tr>

		<tr>
		<td align="left">gebruikersnaam :</td>
		<td>'.$username.'</td>
		</tr>
		<tr>
		<td align="left">E-mail :</td>
		<td>'.$email.'</td>
		</tr>
		<tr>
		<td align="left">Telefoon :</td>
		<td>'.$tel.'</td>
		</tr>
		</table>
		</body>
		</html>
		';

		// if(send_email('info@fooddesk.be', $email, 'contact', $message, $fname))
		if(send_email('shyammishra@cedcoss.com', $email, 'contact', $message, $fname))
		{
			echo "Something";
		}
		else
		{
			echo "No";
		}

		/*print_r($associated_clients); die;
		 $this->db->where_in('id',$associated_clients);
		$this->db->delete("clients");
		echo $this->db->last_query();*/
	}

	function trasnsfer_holidays($what = 'holiday'){
		if($what == 'holiday')
			$this->db->select('company_id,holiday_dates');
		elseif ($what == 'close')
			$this->db->select('company_id,shop_close_dates');

		$holidays_info = $this->db->get('order_settings')->result();
		foreach ($holidays_info as $holidays){
			$company_id = $holidays->company_id;
			if($what == 'holiday')
				$holiday_dates = $holidays->holiday_dates;
			elseif ($what == 'close')
				$holiday_dates = $holidays->shop_close_dates;

			$holiday_dates = explode(',',$holiday_dates);
			if(!Empty($holiday_dates)){
				foreach ($holiday_dates as $holiday_date){
					$hd = explode('/',$holiday_date);
					if(!empty($hd) && isset($hd[0]) && isset($hd[1]) && isset($hd[2])){
						$day = $hd[0];
						if(strlen($day) == 1)
							$day = '0'.$day;

						$month = $hd[1];
						$year = $hd[2];
						$timestamp = strtotime($year.'-'.$month.'-'.$day);
						$insert = array(
							'company_id' => $company_id,
							'day' => $day,
							'month' => $month,
							'year' => $year,
							'timestamp' => $timestamp,
							'date_added' =>date('Y-m-d H:i:s')
						);

						if($what == 'holiday')
							$this->db->insert('company_holidays', $insert);
						elseif ($what == 'close')
							$this->db->insert('company_closedays', $insert);

					}
				}
			}
		}
	}

	function transfer_company(){

		$this->load->model('mcp/Mcompanies');
		$this->load->helper('string');

		$a = array(
			'parent_id' => 0,
			'role' => 'master',
			'type_id' => 8,
			'country_id' => 21,
			'ac_type_id' => 3,
			'packages_id' => 0,
			'company_img' => "",
			'company_desc' => "",
			'company_fb_url' => "",
			'vat' => "",
			'registered_by' => "master_admin",
			'approved' => "1",
			'status' => "1",
			'email_ads' => "1",
			'k_assoc' => "1"
		);

		$a['last_name']  	= "Van Dijck";
		$a['first_name'] 	= "Bart";
		$a['company_name']	= "Keurslager Bart";
		$a['company_slug']	= "keurslager-bart-gravenwezel";
		$a['address']		= "Wijnegemsteenweg 35";
		$a['zipcode']		= "2970";
		$a['city']			= "'s Gravenwezel";
		$a['phone']			= "03 344 11 15";
		$a['email']			= "keurslagerbart@telenet.be";

		$a['username']		= strtolower(random_string('alpha', 8));
		$a['password']		= strtolower(random_string('alnum', 8));

		// 			$a['have_website']  = 1;
		// 			$a['website']		= "http://www.keurslagerij-abel.be/";

		$a['have_website'] = 0;

		$address = $a['address']." ".$a['zipcode']." ".$a['city']." BELGIE";
		// $address = "Brugsesteenweg 420 2900 Mariakerke (Gent) BELGIE";
		$this->load->helper("geolocation");
		$location = get_geolocation($address);
		$a["geo_location"] = json_encode($location);

		$company_id = $this->Mcompanies->insert($a);


		do_settings($company_id,$a['company_name']);

		echo $a['username'].' -- '.$a['password'];

	}

	function company_upd(){
		header('Content-Type: text/html; charset=utf-8');

		$this->db->select('id,company_name,company_slug,first_name,last_name');
		$companies = $this->db->get_where('company_temp',array('k_assoc' => 1))->result();
		foreach ($companies as $company){
			$this->db->where('id' , $company->id);
			$update_aray = array(
				'company_name' => $company->company_name,
				'company_slug' => $company->company_slug,
				'first_name' => $company->first_name,
				'last_name' => $company->last_name
			);
			// echo '<pre>'; print_r($update_aray);
			$this->db->update('company', $update_aray);
		}
	}

	function details_script(){
		$this->db->select('id,temp_id');
		$this->db->where('temp_id !=',0);
		$this->db->where('company_id',26);
		$this->db->distinct('temp_id');
		$result = $this->db->get('orders')->result();
		// 			echo $this->db->last_query();
		// 			print_r($result); die;
		foreach($result as $row){
			$this->db->where('orders_id',$row->temp_id);
			$temp_rows = $this->db->get('order_details_tmp')->result();
			//print_r($temp_rows);
			if(!empty($temp_rows)){
				foreach($temp_rows as $temp_row){
					unset($temp_row->id);
					//$temp_row->orders_id = $row->id;
					//print_r($temp_row);
					//$this->db->insert('order_details',$temp_row);
					//echo $this->db->insert_id();

					$this->db->where(array('orders_id' => $row->id,'products_id' => $temp_row->products_id));
					$this->db->set('quantity',$temp_row->quantity);
					$this->db->update('order_details');

					echo $this->db->last_query()."<br/>";
				}
			}
		}
	}

	function __duplicate($company_id1 = null,$company_id2 = null){

			// updating General settings
		$this->db->where ( 'company_id', $company_id1 );
		$general_settings_data1 = $this->db->get ( 'general_settings' )->result_array ();

		unset ( $general_settings_data1 [0] ['id'] );
		unset ( $general_settings_data1 [0] ['company_id'] );

		$this->db->where ( 'company_id', $company_id2 );
		$general_settings_data2 = $this->db->get ( 'general_settings' )->result_array ();

		if ($general_settings_data1 [0] != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$this->db->update ( 'general_settings', $general_settings_data1 [0] );
		}

		// //**********************updating Categories and subcategories**************************************//////
		$this->db->where ( 'company_id', $company_id1 );
		$categories_data1 = $this->db->get ( 'categories' )->result_array ();

		$this->db->where ( 'company_id', $company_id2 );
		$categories_data2 = $this->db->get ( 'categories' )->result_array ();

		if ($categories_data2 != null) {
			foreach ( $categories_data2 as $key => $value ) {
				$this->db->where ( 'categories_id', $categories_data2 [$key] ['id'] );
				$this->db->delete ( 'subcategories' );
			}

			$this->db->where ( 'company_id', $company_id2 );
			$this->db->delete ( 'categories' );
		}

		if ($categories_data1 != null) {
			foreach ( $categories_data1 as $key => $value ) {
				$categories_data1 [$key] ['company_id'] = $company_id2;

				if ($categories_data1 [$key] ['image'] != "") {
					$old_image = $categories_data1 [$key] ['image'];

					if (file_exists ( "./" . $old_image )) {
						$old_image = str_replace ( "assets/cp/images/categories/", "", $old_image );

						$categories_data1 [$key] ['image'] = "assets/cp/images/categories/" . $company_id2 . "_" . $old_image;

						$file = './assets/cp/images/categories/' . $old_image;
						$newfile = './assets/cp/images/categories/' . $company_id2 . '_' . $old_image;

						if (! copy ( $file, $newfile )) {
							echo "failed to copy";
						}
					}
				}
			}
		}

		if ($categories_data1 != null) {
			foreach ( $categories_data1 as $categories ) {
				$this->db->where ( 'categories_id', $categories ['id'] );
				$subcategories_data1 = $this->db->get ( 'subcategories' )->result_array ();

				unset ( $categories ['id'] );

				$this->db->insert ( 'categories', $categories );
				$new_cat_id = $this->db->insert_id ();

				if ($subcategories_data1 != null) {
					foreach ( $subcategories_data1 as $key => $value ) {
						unset ( $subcategories_data1 [$key] ['id'] );
						$subcategories_data1 [$key] ['categories_id'] = $new_cat_id;

						if ($subcategories_data1 [$key] ['subimage'] != "") {
							$old_subimage = $subcategories_data1 [$key] ['subimage'];

							if (file_exists ( "./" . $old_subimage )) {
								$old_subimage = str_replace ( "assets/cp/images/subcategories/", "", $old_subimage );

								$subcategories_data1 [$key] ['subimage'] = "assets/cp/images/subcategories/" . $new_cat_id . "_" . $old_subimage;

								$file = './assets/cp/images/subcategories/' . $old_subimage;
								$newfile = './assets/cp/images/subcategories/' . $new_cat_id . '_' . $old_subimage;

								if (! copy ( $file, $newfile )) {
									echo "failed to copy";
								}
							} else {
								$subcategories_data1 [$key] ['subimage'] = "";
							}
						}
					}
					foreach ( $subcategories_data1 as $subcategories ) {
						$this->db->insert ( 'subcategories', $subcategories );
					}
				}
			}
		}
		/**
		 * *******************************************************************************************
		 */

		// updating Products
		$this->db->where ( 'company_id', $company_id1 );
		$products_data1 = $this->db->get ( 'products' )->result_array ();

		foreach ( $products_data1 as $key => $value ) {
			$old_prod_id = $products_data1 [$key] ['id'];
			unset ( $products_data1 [$key] ['id'] );
			$products_data1 [$key] ['company_id'] = $company_id2;

			if ($products_data1 [$key] ['image'] != "") {
				$old_image = $products_data1 [$key] ['image'];
				$products_data1 [$key] ['image'] = $company_id2 . "_" . $products_data1 [$key] ['image'];

				if (file_exists ( './assets/cp/images/product/' . $old_image )) {
					$file = './assets/cp/images/product/' . $old_image;
					$newfile = './assets/cp/images/product/' . $products_data1 [$key] ['image'];

					if (! copy ( $file, $newfile )) {
						echo "failed to copy normal -- " . $old_image;
					}
				}

				if (file_exists ( './assets/cp/images/product_46_46/' . $old_image )) {
					$file = './assets/cp/images/product_46_46/' . $old_image;
					$newfile = './assets/cp/images/product_46_46/' . $products_data1 [$key] ['image'];

					if (! copy ( $file, $newfile )) {
						echo "failed to copy 46*46 -- " . $old_image;
					}
				}

				if (file_exists ( './assets/cp/images/product_60_60/' . $old_image )) {
					$file = './assets/cp/images/product_60_60/' . $old_image;
					$newfile = './assets/cp/images/product_60_60/' . $products_data1 [$key] ['image'];

					if (! copy ( $file, $newfile )) {
						echo "failed to copy 60*60 -- " . $old_image;
					}
				}

				if (file_exists ( './assets/cp/images/product_100_100/' . $old_image )) {
					$file = './assets/cp/images/product_100_100/' . $old_image;
					$newfile = './assets/cp/images/product_100_100/' . $products_data1 [$key] ['image'];

					if (! copy ( $file, $newfile )) {
						echo "failed to copy 100*100 -- " . $old_image;
					}
				}

				if (file_exists ( './assets/cp/images/product_270_270/' . $old_image )) {
					$file = './assets/cp/images/product_270_270/' . $old_image;
					$newfile = './assets/cp/images/product_270_270/' . $products_data1 [$key] ['image'];

					if (! copy ( $file, $newfile )) {
						echo "failed to copy 270*270 -- " . $old_image;
					}
				}
			}

			$old_cat_id = 0;
			if ($products_data1 [$key] ['categories_id']) {
				$new_cat_id = $this->db->query ( 'SELECT id from categories WHERE company_id = ' . $company_id2 . ' AND name IN (SELECT name from categories WHERE id = ' . $products_data1 [$key] ['categories_id'] . ') ' )->result_array ();
				if (! empty ( $new_cat_id )) {
					$old_cat_id = $products_data1 [$key] ['categories_id'];
					$products_data1 [$key] ['categories_id'] = $new_cat_id ['0'] ['id'];
				}
			}

			if ($products_data1 [$key] ['subcategories_id'] != '-1') {
				$new_subcat_id = $this->db->query ( 'SELECT id from subcategories WHERE categories_id = ' . $products_data1 [$key] ['categories_id'] . ' AND subname IN (SELECT subname from subcategories WHERE id = ' . $products_data1 [$key] ['subcategories_id'] . ') ' )->result_array ();
				if (! empty ( $new_subcat_id )) {
					$products_data1 [$key] ['subcategories_id'] = $new_subcat_id ['0'] ['id'];
				}
			}

			if ($this->db->insert ( 'products', $products_data1 [$key] )) {
				$new_prod_id = $this->db->insert_id ();

				// $grps_products = $this->db->get_where('',array('products_id' => $old_prod_id))->result_array();
				/*
				 * $grps_products = $this->db->query('SELECT * FROM `groups_products` JOIN `groups` ON `groups_products`.`groups_id` = `groups`.`id` WHERE `groups_products`.`products_id` = '.$old_prod_id)->result_array(); // SELECT * FROM `groups_products` JOIN `groups` ON `groups_products`.`groups_id` = `groups`.`id` WHERE `groups_products`.`groups_id` IN (SELECT `groups`.`id` FROM `groups` WHERE `company_id` = 3841) if(!empty($grps_products)){ foreach ($grps_products as $grps_product){ $grps_product['products_id'] = $new_prod_id; $old_grp_id = $grps_product['groups_id']; $grps_names = $this->db->get_where('groups', array('company_id' => $company_id2, 'group_name' => $grps_product['group_name']))->result_array(); $insert_array = array( 'products_id' =>$new_prod_id, 'groups_id' =>$grps_names['0']['id'], 'attribute_name' =>$grps_product['attribute_name'], 'attribute_value' =>$grps_product['attribute_value'], 'multiselect' =>$grps_product['multiselect'], 'type' =>$grps_product['type'], 'display_order' =>$grps_product['display_order'], 'required' =>$grps_product['required'] ); $this->db->insert('groups_products',$insert_array); } }
				 */
			}
		}

		// Copying CSS
		$this->db->where ( 'company_id', $company_id1 );
		$company_css_data1 = $this->db->get ( 'company_css' )->result_array ();

		foreach ( $company_css_data1 as $key => $value ) {
			unset ( $company_css_data1 [$key] ['id'] );
			$company_css_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_css_data2 = $this->db->get ( 'company_css' )->result_array ();

		if ($company_css_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_css' );
		}

		foreach ( $company_css_data1 as $company_css ) {
			$this->db->insert ( 'company_css', $company_css );
		}

		// Copying Order Settings
		$this->db->where ( 'company_id', $company_id1 );
		$order_settings_data1 = $this->db->get ( 'order_settings' )->result_array ();

		foreach ( $order_settings_data1 as $key => $value ) {
			unset ( $order_settings_data1 [$key] ['id'] );
			$order_settings_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$order_settings_data2 = $this->db->get ( 'order_settings' )->result_array ();

		if ($order_settings_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'order_settings' );
		}

		if ($order_settings_data1 != null) {
			foreach ( $order_settings_data1 as $order_settings ) {
				$this->db->insert ( 'order_settings', $order_settings );
			}
		}

		// Copying Pickup Delievery timmings
		$this->db->where ( 'company_id', $company_id1 );
		$pickup_delivery_timings_data1 = $this->db->get ( 'pickup_delivery_timings' )->result_array ();

		foreach ( $pickup_delivery_timings_data1 as $key => $value ) {
			unset ( $pickup_delivery_timings_data1 [$key] ['id'] );
			$pickup_delivery_timings_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$pickup_delivery_timings_data2 = $this->db->get ( 'pickup_delivery_timings' )->result_array ();

		if ($pickup_delivery_timings_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'pickup_delivery_timings' );
		}

		if ($pickup_delivery_timings_data1 != null) {
			foreach ( $pickup_delivery_timings_data1 as $pickup_delivery_timings ) {
				$this->db->insert ( 'pickup_delivery_timings', $pickup_delivery_timings );
			}
		}

		// Copying Groups
		$this->db->where ( 'company_id', $company_id1 );
		$groups_data1 = $this->db->get ( 'groups' )->result_array ();

		foreach ( $groups_data1 as $key => $value ) {
			unset ( $groups_data1 [$key] ['id'] );
			$groups_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$groups_data2 = $this->db->get ( 'groups' )->result_array ();

		if ($groups_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'groups' );
		}

		if ($groups_data1 != null) {
			foreach ( $groups_data1 as $groups ) {
				$this->db->insert ( 'groups', $groups );
			}
		}

		// copying_clients
		/*$this->db->where ( 'company_id', $company_id1 );
		$clients_data1 = $this->db->get ( 'clients' )->result_array ();

		foreach ( $clients_data1 as $key => $value ) {
			unset ( $clients_data1 [$key] ['id'] );
			$clients_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$clients_data2 = $this->db->get ( 'clients' )->result_array ();

		if ($clients_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'clients' );
		}

		foreach ( $clients_data1 as $clients ) {
			$this->db->insert ( 'clients', $clients );
		}

		// copying client numbers -------------------- PENDING
		$this->db->where ( 'company_id', $company_id1 );
		$client_numbers_data1 = $this->db->get ( 'client_numbers' )->result_array ();

		foreach ( $client_numbers_data1 as $key => $value ) {
			unset ( $client_numbers_data1 [$key] ['id'] );
			$client_numbers_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$client_numbers_data2 = $this->db->get ( 'client_numbers' )->result_array ();

		if ($client_numbers_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'client_numbers' );
		}

		foreach ( $client_numbers_data1 as $client_numbers ) {
			$this->db->insert ( 'client_numbers', $client_numbers );
		}

		// copying closed notifications
		$this->db->where ( 'company_id', $company_id1 );
		$closed_notifications_data1 = $this->db->get ( 'closed_notifications' )->result_array ();

		foreach ( $closed_notifications_data1 as $key => $value ) {
			unset ( $closed_notifications_data1 [$key] ['closed_id'] );
			$closed_notifications_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$closed_notifications_data2 = $this->db->get ( 'closed_notifications' )->result_array ();

		if ($closed_notifications_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'closed_notifications' );
		}

		foreach ( $closed_notifications_data1 as $closed_notifications ) {
			$this->db->insert ( 'closed_notifications', $closed_notifications );
		}*/

		// copying company_closedays
		$this->db->where ( 'company_id', $company_id1 );
		$company_closedays_data1 = $this->db->get ( 'company_closedays' )->result_array ();

		foreach ( $company_closedays_data1 as $key => $value ) {
			unset ( $company_closedays_data1 [$key] ['id'] );
			$company_closedays_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_closedays_data2 = $this->db->get ( 'company_closedays' )->result_array ();

		if ($company_closedays_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_closedays' );
		}

		foreach ( $company_closedays_data1 as $company_closedays ) {
			$this->db->insert ( 'company_closedays', $company_closedays );
		}

		// copying company_countries
		$this->db->where ( 'company_id', $company_id1 );
		$company_countries_data1 = $this->db->get ( 'company_countries' )->result_array ();

		foreach ( $company_countries_data1 as $key => $value ) {
			unset ( $company_countries_data1 [$key] ['id'] );
			$company_countries_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_countries_data2 = $this->db->get ( 'company_countries' )->result_array ();

		if ($company_countries_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_countries' );
		}

		foreach ( $company_countries_data1 as $company_countries ) {
			$this->db->insert ( 'company_countries', $company_countries );
		}

		// copying company_delivery_areas
		$this->db->where ( 'company_id', $company_id1 );
		$company_delivery_areas_data1 = $this->db->get ( 'company_delivery_areas' )->result_array ();

		foreach ( $company_delivery_areas_data1 as $key => $value ) {
			unset ( $company_delivery_areas_data1 [$key] ['id'] );
			$company_delivery_areas_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_delivery_areas_data2 = $this->db->get ( 'company_delivery_areas' )->result_array ();

		if ($company_delivery_areas_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_delivery_areas' );
		}

		foreach ( $company_delivery_areas_data1 as $company_delivery_areas ) {
			$this->db->insert ( 'company_delivery_areas', $company_delivery_areas );
		}

		// copying company_delivery_settings
		$this->db->where ( 'company_id', $company_id1 );
		$company_delivery_settings_data1 = $this->db->get ( 'company_delivery_settings' )->result_array ();

		foreach ( $company_delivery_settings_data1 as $key => $value ) {
			unset ( $company_delivery_settings_data1 [$key] ['id'] );
			$company_delivery_settings_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_delivery_settings_data2 = $this->db->get ( 'company_delivery_settings' )->result_array ();

		if ($company_delivery_settings_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_delivery_settings' );
		}

		foreach ( $company_delivery_settings_data1 as $company_delivery_settings ) {
			$this->db->insert ( 'company_delivery_settings', $company_delivery_settings );
		}

		// copying company_holidays
		$this->db->where ( 'company_id', $company_id1 );
		$company_holidays_data1 = $this->db->get ( 'company_holidays' )->result_array ();

		foreach ( $company_holidays_data1 as $key => $value ) {
			unset ( $company_holidays_data1 [$key] ['id'] );
			$company_holidays_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_holidays_data2 = $this->db->get ( 'company_holidays' )->result_array ();

		if ($company_holidays_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_holidays' );
		}

		foreach ( $company_holidays_data1 as $company_holidays ) {
			$this->db->insert ( 'company_holidays', $company_holidays );
		}

		// copying company_language
		$this->db->where ( 'company_id', $company_id1 );
		$company_language_data1 = $this->db->get ( 'company_language' )->result_array ();

		foreach ( $company_language_data1 as $key => $value ) {
			unset ( $company_language_data1 [$key] ['id'] );
			$company_language_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$company_language_data2 = $this->db->get ( 'company_language' )->result_array ();

		if ($company_language_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'company_language' );
		}

		foreach ( $company_language_data1 as $company_language ) {
			$this->db->insert ( 'company_language', $company_language );
		}

		// copying delivery_areas
		$this->db->where ( 'company_id', $company_id1 );
		$delivery_areas_data1 = $this->db->get ( 'delivery_areas' )->result_array ();

		foreach ( $delivery_areas_data1 as $key => $value ) {
			unset ( $delivery_areas_data1 [$key] ['id'] );
			$delivery_areas_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$delivery_areas_data2 = $this->db->get ( 'delivery_areas' )->result_array ();

		if ($delivery_areas_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'delivery_areas' );
		}

		foreach ( $delivery_areas_data1 as $delivery_areas ) {
			$this->db->insert ( 'delivery_areas', $delivery_areas );
		}

		// copying desk_section_design
		$this->db->where ( 'company_id', $company_id1 );
		$desk_section_design_data1 = $this->db->get ( 'desk_section_design' )->result_array ();

		foreach ( $desk_section_design_data1 as $key => $value ) {
			unset ( $desk_section_design_data1 [$key] ['id'] );
			$desk_section_design_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$desk_section_design_data2 = $this->db->get ( 'desk_section_design' )->result_array ();

		if ($desk_section_design_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'desk_section_design' );
		}

		foreach ( $desk_section_design_data1 as $desk_section_design ) {
			$this->db->insert ( 'desk_section_design', $desk_section_design );
		}

		// copying desk_settings
		$this->db->where ( 'company_id', $company_id1 );
		$desk_settings_data1 = $this->db->get ( 'desk_settings' )->result_array ();

		foreach ( $desk_settings_data1 as $key => $value ) {
			unset ( $desk_settings_data1 [$key] ['desk_id'] );
			$desk_settings_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$desk_settings_data2 = $this->db->get ( 'desk_settings' )->result_array ();

		if ($desk_settings_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'desk_settings' );
		}

		foreach ( $desk_settings_data1 as $desk_settings ) {
			$this->db->insert ( 'desk_settings', $desk_settings );
		}

		// copying opening_hours
		$this->db->where ( 'company_id', $company_id1 );
		$opening_hours_data1 = $this->db->get ( 'opening_hours' )->result_array ();

		foreach ( $opening_hours_data1 as $key => $value ) {
			unset ( $opening_hours_data1 [$key] ['id'] );
			$opening_hours_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$opening_hours_data2 = $this->db->get ( 'opening_hours' )->result_array ();

		if ($opening_hours_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'opening_hours' );
		}

		foreach ( $opening_hours_data1 as $opening_hours ) {
			$this->db->insert ( 'opening_hours', $opening_hours );
		}

		// copying section_designs
		$this->db->where ( 'company_id', $company_id1 );
		$section_designs_data1 = $this->db->get ( 'section_designs' )->result_array ();

		foreach ( $section_designs_data1 as $key => $value ) {
			unset ( $section_designs_data1 [$key] ['id'] );
			$section_designs_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$section_designs_data2 = $this->db->get ( 'section_designs' )->result_array ();

		if ($section_designs_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'section_designs' );
		}

		foreach ( $section_designs_data1 as $section_designs ) {
			$this->db->insert ( 'section_designs', $section_designs );
		}

		// copying groups_order
		/*$this->db->where ( 'company_id', $company_id1 );
		$groups_order_data1 = $this->db->get ( 'groups_order' )->result_array ();

		foreach ( $groups_order_data1 as $key => $value ) {
			unset ( $groups_order_data1 [$key] ['id'] );
			$groups_order_data1 [$key] ['company_id'] = $company_id2;
		}

		$this->db->where ( 'company_id', $company_id2 );
		$groups_order_data2 = $this->db->get ( 'groups_order' )->result_array ();

		if ($groups_order_data2 != null) {
			$this->db->where ( 'company_id', $company_id2 );
			$suc = $this->db->delete ( 'groups_order' );
		}

		foreach ( $groups_order_data1 as $groups_order ) {
			$this->db->insert ( 'groups_order', $groups_order );
		}*/
	}

	function entry_details_get(){
		//$details_array = json_decode('[{"product_id":"7880","product_name":"Kippenkoek","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0164","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006084.gif","product_type":"","prodescription":"Vleesbrood van kipfilet en mager varkensvlees","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0164","product_price":"0.0164","group_additional":"0","total_amount":"1.6400000000000001","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"8007","product_name":"Maredsous","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.022","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386031733.gif","product_type":"","prodescription":"MaredsousHalfharde Belgische abdijkaas van de paters van Maredsous.  Zachte typische smaak.  Maredsous kaas heeft een vetgehalte van 45 % en is gemaakt van koemelk.  De kaas wordt sinds 1953 bereid en lijkt van smaak een beetje op de Franse kaas Port Salut.De Benedictijner abdijen liggen aan de basis van onze westerse beschaving ! Gedurende jaren zorgden zij naast gastvrijheid ook voor de spirituele, educatieve en economische ontwikkeling van onze maatschappij. De Abdij van Maredsous, ligt in een prachtig natuurgebied ten zuiden van Namen.  Sinds zijn ontstaan in 1872 is gastvrijheid de norm, daarbij horen ook bier en kaas. De kaas van Maredsous heeft een zachte en romige smaak. Tijdens de drie weken durende rijping in de kaaskelders van de abdij wordt de korst meermaals met bronwater gewassen en is de kaas klaar om gegeten te worden.  Een goed glas bier of wijn erbij en U geniet !","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.022","product_price":"0.022","group_additional":"0","total_amount":"2.1999999999999997","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7897","product_name":"Ossenvlees","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0324","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006651.gif","product_type":"","prodescription":"ambachtelijk gerookt rundsvlees uit eigen werkhuis","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0324","product_price":"0.0324","group_additional":"0","total_amount":"3.2399999999999998","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"8153","product_name":"Boomstammetjes (+- 115 gr)","price_per_unit":"1.75","weight_per_unit":"","price_per_person":"0","price_weight":"0","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386775817.jpg","product_type":"","prodescription":"Gehakt gevuld met kaas en ham.  Gepanneerd met papikapanneermeel","product_discount":"0","product_quantity":"4","content_type":"0","sell_product_option":"per_unit","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"1.75","product_price":"1.75","group_additional":"0","total_amount":"7","add_costs":"","pro_remark":"","advance_payment":"0","weight_unit":"","available_after":"","image":"","extra_field":"","extra_name":""}]');
		//$details_array = json_decode('[{"product_id":"8114","product_name":"Gemengd gehakt varken\/kalfs","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0096","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386412739.jpg","product_type":"","prodescription":"Gekruid gehakt van varken\/kalf","product_discount":"0","product_quantity":"400","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0096","product_price":"0.0096","group_additional":"0","total_amount":"3.84","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7950","product_name":"Roompat\u00e9","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.01485","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386777777.JPG","product_type":"","prodescription":"","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.01485","product_price":"0.01485","group_additional":"0","total_amount":"1.485","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7883","product_name":"Nootham","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0179","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006193.gif","product_type":"","prodescription":"Deze nootham is de kleine broer van de gekookte ham. Deze delicatesse wordt op een ambachtelijke wijze en met veel zorg bereid.  Na het inzouten van dit hammetjes laten we deze specialiteit een 3-weken rijpen.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0179","product_price":"0.0179","group_additional":"0","total_amount":"1.79","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7966","product_name":"Salami","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.01425","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386778140.JPG","product_type":"","prodescription":"heerlijk en uit eigen werkhuis","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.01425","product_price":"0.01425","group_additional":"0","total_amount":"1.425","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7919","product_name":"Gelderse ringworst","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0143","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386007716.gif","product_type":"","prodescription":"Lunchworst, een typisch Antwerps product, bereid uit eerste keuze mager varkensvlees, vermengd met blokjes spek.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0143","product_price":"0.0143","group_additional":"0","total_amount":"1.43","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7902","product_name":"Breugelkop","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0141","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006941.gif","product_type":"","prodescription":"Kempische fijne kop uit eigen werkhuis.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0141","product_price":"0.0141","group_additional":"0","total_amount":"1.41","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"8087","product_name":"Tonijnsalade","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0179","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386803219.JPG","product_type":"","prodescription":"Huisbereide salade op basis van tonijn en eigengemaakte mayonaise.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0179","product_price":"0.0179","group_additional":"0","total_amount":"1.79","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"8077","product_name":"Vleessalade","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0142","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386415921.jpg","product_type":"","prodescription":"Huisbereide salade op basis van gekookte ham en eigengemaakte mayonaise.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0142","product_price":"0.0142","group_additional":"0","total_amount":"1.4200000000000002","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""}]');
		//$details_array = json_decode('[{"product_id":"7887","product_name":"Breydelham","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.01965","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006302.gif","product_type":"","prodescription":"ham, gepekeld met kruidenbouillon en daarna gegaard in de oven","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.01965","product_price":"0.01965","group_additional":"0","total_amount":"1.965","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7888","product_name":"Ardeense ham","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0234","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006346.gif","product_type":"","prodescription":"Een ambachtelijk, gezouten en gerookte ham, en dat proef je.  Zeer lekkere smaak door de zachtheid en de malsheid van het zoutingsproces","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0234","product_price":"0.0234","group_additional":"0","total_amount":"2.34","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7881","product_name":"Kippenwit","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0232","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386006117.gif","product_type":"","prodescription":"magere kippenfilet, bereid in eigen werkhuis","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0232","product_price":"0.0232","group_additional":"0","total_amount":"2.32","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7922","product_name":"Granenkoek","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0164","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386007882.gif","product_type":"","prodescription":"Vleesbrood afgegarneerd met granen","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0164","product_price":"0.0164","group_additional":"0","total_amount":"1.6400000000000001","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7928","product_name":"Kaslerrib","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.01895","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386777623.JPG","product_type":"","prodescription":"voorgegaard en gekruid varkensgebraad van de rug","product_discount":"0","product_quantity":"60","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.01895","product_price":"0.01895","group_additional":"0","total_amount":"1.137","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"7933","product_name":"Panchetta","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0239","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386028228.gif","product_type":"","prodescription":"Buikspek, drooggezouten en lichtjes ingewreven met kruiden.  Opgerold en gedurende 6 weken gerijpt.  Smeu\u00efge, volle smaak.","product_discount":"0","product_quantity":"120","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0239","product_price":"0.0239","group_additional":"0","total_amount":"2.8680000000000003","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"8033","product_name":"Kip curry","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0153","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386032728.gif","product_type":"","prodescription":"Huisbereid slaatje op basis van gekookte kipfilet in koude currysaus.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0153","product_price":"0.0153","group_additional":"0","total_amount":"1.53","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"8035","product_name":"Mexicaanse kipsalade","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0154","discount":"0","discount_person":"0","discount_wt":"0","image_display":"1","min_amount":"0","max_amount":"0","product_image":"prod_1386032792.gif","product_type":"","prodescription":"Huisbereide salade op basis van gekookte kipfilet in een goed afsmakende saus, afgewerkt met champignons en gekookte eitjes.","product_discount":"0","product_quantity":"100","content_type":"1","sell_product_option":"weight_wise","allday_availability":"","availability":"[\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0154","product_price":"0.0154","group_additional":"0","total_amount":"1.54","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""}]');

		//$details_array = json_decode('[{"product_id":"2407","product_name":"Eieren van scharrelkippen   PLU 702","price_per_unit":"0.21","weight_per_unit":"","price_per_person":"0","price_weight":"0","discount":"0","discount_person":"","discount_wt":"0","image_display":"","min_amount":"0","max_amount":"0","product_image":"","product_type":"","prodescription":" 0.21 euro\/stuk","product_discount":"0","product_quantity":"6","content_type":"0","sell_product_option":"per_unit","allday_availability":"1","availability":"[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.21","product_price":"0.21","group_additional":"0","total_amount":"1.26","add_costs":"","pro_remark":"","advance_payment":"0","weight_unit":"","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"2414","product_name":"Beemsterkaas jong (Gouda)   PLU 500","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0135","discount":"0","discount_person":"","discount_wt":"0","image_display":"","min_amount":"0","max_amount":"0","product_image":"","product_type":"","prodescription":" 13.5 euro\/kg","product_discount":"0","product_quantity":"300","content_type":"1","sell_product_option":"weight_wise","allday_availability":"1","availability":"[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0135","product_price":"0.0135","group_additional":"0","total_amount":"4.05","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"2337","product_name":"Gezouten buikspek   PLU 152","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0089","discount":"0","discount_person":"","discount_wt":"0","image_display":"","min_amount":"0","max_amount":"0","product_image":"","product_type":"","prodescription":"8.9 euro\/kg","product_discount":"0","product_quantity":"250","content_type":"1","sell_product_option":"weight_wise","allday_availability":"1","availability":"[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0089","product_price":"0.0089","group_additional":"0","total_amount":"2.225","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"2501","product_name":"Bereid gehakt varken\/rund\/kalf   PLU 200","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0069","discount":"0","discount_person":"","discount_wt":"0","image_display":"","min_amount":"0","max_amount":"0","product_image":"","product_type":"","prodescription":" 6.6 euro\/kg","product_discount":"0","product_quantity":"500","content_type":"1","sell_product_option":"weight_wise","allday_availability":"1","availability":"[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0069","product_price":"0.0069","group_additional":"0","total_amount":"3.4499999999999997","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"2338","product_name":"Ontvette hesp   PLU 300","price_per_unit":"0","weight_per_unit":"","price_per_person":"0","price_weight":"0.0144","discount":"0","discount_person":"","discount_wt":"0","image_display":"","min_amount":"0","max_amount":"0","product_image":"","product_type":"","prodescription":"14.4 euro\/kg","product_discount":"0","product_quantity":"200","content_type":"1","sell_product_option":"weight_wise","allday_availability":"1","availability":"[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"0.0144","product_price":"0.0144","group_additional":"0","total_amount":"2.88","add_costs":"","pro_remark":"","advance_payment":"0","available_after":"","image":"","extra_field":"","extra_name":""},{"product_id":"2679","product_name":"Kalkoenlapje   PLU 424","price_per_unit":"11.6","weight_per_unit":"","price_per_person":"0","price_weight":"0.0116","discount":"0","discount_person":"","discount_wt":"0","image_display":"","min_amount":"0","max_amount":"0","product_image":"","product_type":"","prodescription":"11.6 euro\/kg","product_discount":"0","product_quantity":"4","content_type":"0","sell_product_option":"per_unit","allday_availability":"1","availability":"[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]","default_price":"11.6","product_price":"11.6","group_additional":"0","total_amount":"46.4","add_costs":"","pro_remark":"","advance_payment":"0","weight_unit":"gm","available_after":"","image":"","extra_field":"","extra_name":""}]');
		//print_r($details_array); die("234");
		/*if(!empty($details_array)){
		 foreach($details_array as $row){
		 $inser_odet_arr = (array) $row;

		 $subtotal = 0;
		 $defaultPrice = 0;
		 if($inser_odet_arr['content_type'] == 0 || $inser_odet_arr['content_type'] == 2){
		 $subtotal = $inser_odet_arr['product_price'];
		 $defaultPrice = $inser_odet_arr['default_price'];
		 }
		 else{
		 $subtotal = $this->number2db(round($inser_odet_arr['product_price']*1000,2));
		 $defaultPrice = $this->number2db(round($inser_odet_arr['default_price']*1000,2));
		 }

		 $order_detail_arr = array(
		 'orders_id' => '17661',
		 'products_id' => $inser_odet_arr['product_id'],
		 'default_price' => $this->number2db($defaultPrice),
		 'discount' => $this->number2db($inser_odet_arr['product_discount']),
		 'weight_unit' => ( (isset($inser_odet_arr['weight_unit']))?$inser_odet_arr['weight_unit']:'' ),
		 'add_costs' => $inser_odet_arr['add_costs'],
		 'quantity' => $inser_odet_arr['product_quantity'],
		 'sub_total' => $this->number2db($subtotal),
		 'total' => $this->number2db($inser_odet_arr['total_amount']),
		 'pro_remark' => $inser_odet_arr['pro_remark'],
		 'content_type' => $inser_odet_arr['content_type'],
		 'image' => ( (isset($inser_odet_arr['image']))?$inser_odet_arr['image']:'' ),
		 'extra_field' => ( (isset($inser_odet_arr['extra_field']))?$inser_odet_arr['extra_field']:'' ),
		 'extra_name' => ( (isset($inser_odet_arr['extra_name']))?$inser_odet_arr['extra_name']:'' ),
		 'weight_per_unit' => ( (isset($inser_odet_arr['weight_per_unit']))?$inser_odet_arr['weight_per_unit']:'' )
		);*/

		/*echo "<pre>";
		print_r($order_detail_arr);*/
		/*$this->db->insert( 'order_details' , $order_detail_arr );
		 $order_details_id = $this->db->insert_id();
		 echo $order_details_id."<br/>";
		 }
		 }
		 die("ygeuyweu");*/
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
	/**
	 *
	 * Function to copy data from one company to another on same server
	 *
	 */
	
	function copy_data($from = 0, $to = 0){
		if($from && $to){
			
			$categories = $this->db->get_where('categories', array('company_id' => $from))->result_array();

			if(!empty($categories)){
				foreach ($categories as $category){
					$old_category_id = $category['id'];

					echo $old_category_id.'--------';

					// Unsetting category ID
					unset($category['id']);

					// Setting company ID
					$category['company_id'] = $to;

					// Created date update
					$category['created'] = date('Y-m-d');
					$category['updated'] = date('Y-m-d');

					if($this->db->insert('categories', $category)){
						$new_cat_id = $this->db->insert_id();

						$querydata = $this->db->get_where( 'categories_name',array('cat_id'=>$old_category_id,'comp_id'=>$from) )->row_array();
						if (!empty($querydata)) {
							$querydata['comp_id'] = $to;
							unset($querydata['id']);
							$querydata['cat_id'] = $new_cat_id;
							$this->db->insert('categories_name', $querydata);
						}

						// Subcategory
						$subcategories = $this->db->get_where('subcategories', array('categories_id' => $old_category_id))->result_array();
						if(!empty($subcategories)){
							foreach ($subcategories as $subcategory){

								$old_subcat_id = $subcategory['id'];
								unset($subcategory['id']);
								$subcategory['categories_id'] = $new_cat_id;

								// Created date update
								$subcategory['subcreated'] = date('Y-m-d');
								$subcategory['subupdated'] = date('Y-m-d');

								if($this->db->insert('subcategories', $subcategory)){
									$new_subcat_id = $this->db->insert_id();

									$querydata = $this->db->get_where( 'subcategories_name',array('subcat_id'=>$old_subcat_id, 'categ_id'=>$old_category_id) )->row_array();
									if (!empty($querydata)) {
										unset($querydata['id']);
										$querydata['subcat_id'] = $new_subcat_id;
										$querydata['categ_id'] = $new_cat_id;
										$this->db->insert('subcategories_name', $querydata);
									}

									// Products
									$this->addProducts($from,$to,$old_category_id,$new_cat_id,$old_subcat_id,$new_subcat_id);

								}
							}
						}

						// Products
						$this->addProducts($from,$to,$old_category_id,$new_cat_id,'-1','-1');

					}
				}
			}
		}
	}

	function addProducts($from,$to,$old_category_id,$new_cat_id,$old_subcat_id,$new_subcat_id){
		$products = $this->db->get_where('products', array('company_id' => $from, 'categories_id' => $old_category_id, 'subcategories_id' => $old_subcat_id))->result_array();

		if(!empty($products)){
			foreach ($products as $product){
				$old_pro_id = $product['id'];

				unset($product['id']);

				$product['company_id'] = $to;

				$product['categories_id'] = $new_cat_id;

				$product['subcategories_id'] = $new_subcat_id;

				$product['procreated'] = date('Y-m-d');
				$product['proupdated'] = date('Y-m-d');

				if($this->db->insert('products', $product)){
					$new_product_id = $this->db->insert_id();

					$querydata = $this->db->get_where( 'products_name',array('product_id'=>$old_pro_id) )->row_array();
					if (!empty($querydata)) {
						unset($querydata['id']);
						$querydata['product_id'] = $new_product_id;
						$this->db->insert('products_name', $querydata);
					}

					//Inserting data in products_discount table
					$get_product_discount_info = $this->db->get_where('products_discount',array('products_id' => $old_pro_id ))->result_array();
					if($get_product_discount_info){
						foreach($get_product_discount_info as $values){
							$insert_array = array(
								'products_id' => $new_product_id,
								'quantity' => $values['quantity'],
								'discount_per_qty' => $values['discount_per_qty'],
								'price_per_qty' => $values['price_per_qty'],
								'type' => $values['type']
							);
							$this->db->insert('products_discount',$insert_array);
						}
					}

					//Inserting data in groups_products table
					$get_product_group_info = $this->db->get_where('groups_products',array('products_id' => $old_pro_id ))->result_array();
					if($get_product_group_info){
						foreach($get_product_group_info as $values){
							$insert_array = array(
								'products_id' => $new_product_id,
								'groups_id' => $values['groups_id'],
								'attribute_name' => $values['attribute_name'],
								'attribute_value' => $values['attribute_value'],
								'type' => $values['type']
							);
							$this->db->insert('groups_products',$insert_array);
						}
					}

					//Inserting data in groups_order table
					$get_product_group_order_info = $this->db->get_where('groups_order',array('products_id' => $old_pro_id ))->result_array();
					if($get_product_group_order_info){
						foreach($get_product_group_order_info as $values){
							$insert_array = array(
								'company_id' => $values['company_id'],
								'products_id' => $new_product_id,
								'group_id' => $values['group_id'],
								'order_display' => $values['order_display'],
								'type' => $values['type']
							);
							$this->db->insert('groups_order',$insert_array);
						}
					}

					$this->db->where('obs_pro_id', $old_pro_id);
					$fdd_quants = $this->db->get('fdd_pro_quantity')->result();
					if(!empty($fdd_quants)){
						foreach ($fdd_quants as $fdd_quant){
							$insert_array_new = array(
								'is_obs_product' 		=> $fdd_quant->is_obs_product,
								'obs_pro_id' 			=> $new_product_id,
								'fdd_pro_id' 			=> $fdd_quant->fdd_pro_id,
								'real_supp_id' 			=> $fdd_quant->real_supp_id,
								'quantity' 				=> $fdd_quant->quantity,
								'product_prefix' 		=> $fdd_quant->product_prefix,
								'semi_product_id' 		=> $fdd_quant->semi_product_id,
								'included_semi' 		=> $fdd_quant->included_semi,
								'unit'					=> $fdd_quant->unit,
								'fixed'					=> $fdd_quant->fixed,
								'comp_id'				=> $to,
								'created_dated'			=> $fdd_quant->created_dated,
								'updated_dated' 		=> $fdd_quant->updated_dated,
								'is_shared'				=> $fdd_quant->is_shared,
							);

							$this->db->insert('fdd_pro_quantity', $insert_array_new);
						}
					}

					$this->db->where('company_id', $from);
					$fdd_quants = $this->db->get('fdd_pro_fav')->result();
					if(!empty($fdd_quants)){
						$insert_array_new = array(
							'fdd_pro_id' => $fdd_quants[0]->fdd_pro_id,
							'obs_pro_id' => $fdd_quants[0]->obs_pro_id,
							'company_id'=>$to,
							'date_added'=> $fdd_quants[0]->date_added
						);
						$this->db->insert('fdd_pro_fav', $insert_array_new);

						if( isset( $fdd_quants[0]->obs_pro_id ) && $fdd_quants[0]->obs_pro_id!= '' ){
							$products_pending = $this->db->get_where( 'products_pending', array( 'company_id' => $from ) )->result_array();
							if(  !empty( $products_pending ) ){
								foreach ($products_pending as $key => $value) {
									$value[ 'date' ] = date( 'Y-m-d H:i:s' );
									$value[ 'company_id' ] =$to;
									unset( $value[ 'id' ] );
									$this->db->insert('products_pending', $value );
								}
							}
						}
					}
				}
			}
		}
	}

	function remove_data($cid = 0){
		if( $cid ){
		//if( 0 ){
			$categories = $this->db->get_where('categories', array('company_id' => $cid))->result_array();
			if(!empty($categories)){
				foreach ($categories as $category){
					//Image removing
					if($category['image'] != ''){
						$image_name = end(explode("/",$category['image']));
						@unlink(FCPATH.'assets/cp/images/categories/'.$image_name);
					}

					if($this->db->delete('categories', array('id' => $category['id']))){
						// Subcategory
						$subcategories = $this->db->get_where('subcategories', array('categories_id' => $category['id']))->result_array();
						if(!empty($subcategories)){
							foreach ($subcategories as $subcategory){
								// Image
								if($subcategory['subimage'] != ''){
									$image_name = end(explode("/",$subcategory['subimage']));
									@unlink(FCPATH.'assets/cp/images/categories/'.$image_name);
								}

								if($this->db->delete('subcategories', array('id' => $subcategory['id']))){
									// Products
									$this->delProducts($cid,$category['id'],$subcategory['id']);
								}
							}
						}
						// Products
						$this->delProducts($cid,$category['id'],'-1');
					}
				}
			}
		}
	}

	function delProducts($cid = 0,$category_id = 0,$subcat_id = 0){

		// $products = $this->db->get_where('products', array('company_id' => $cid, 'categories_id' => $category_id, 'subcategories_id' => $subcat_id))->result_array();
		$products = $this->db->get_where('products', array('company_id' => $cid))->result_array();
		// $products = $this->db->get_where('products', array('company_id' => 0))->result_array();

		if(!empty($products)){
			foreach ($products as $product){
				//Removing image
				if($product['image'] != ''){
					@unlink(dirname(__FILE__).'/../../assets/cp/images/product/'.$product['image']);
				}

				if($this->db->delete('products', array('id' => $product['id']))){
					$this->db->delete('products_discount',array('products_id' => $product['id']));

					$this->db->delete('groups_products',array('products_id' => $product['id'] ));

					$this->db->delete('groups_order',array('products_id' => $product['id'] ));

					$this->db->delete('products_ingredients', array('product_id' => $product['id']));

					$this->db->delete('products_traces', array('product_id' => $product['id']));

					$this->db->delete('products_allergence', array('product_id' => $product['id']));

					$this->db->delete('fdd_pro_quantity', array('obs_pro_id'=> $product['id']));
				}
			}
		}
	}

	function show_html($order_id = 0){
		// $this->db->like("meta","print","after");
		$details = $this->db->get_where('print_master_log', array('order_id' => $order_id))->result_array();
		// print_r($details); die;
		echo htmlspecialchars_decode($details['0']['template']);
	}

	public function print_cp_mails_v_2($api_key = null, $font_size = 12, $today_or_all = 1, $update_db = "check", $order_id = 0 ){


		//$json = file_get_contents('php://input');
		//$obj = json_decode($json, true);

		$obj['data']['apiid'] = '961316';
		$obj['data']['today_or_all'] = 1;


		if(!empty($obj) && isset($obj['data']['apiid']) && !isset($obj['data']['order_id'])){
			$api_key = $obj['data']['apiid'];

			if(isset($obj['data']['today_or_all']))
				$today_or_all = $obj['data']['today_or_all'];

			$result = $this->db->query("SELECT `company_id` FROM `api` WHERE `api_id` = '".$api_key."' LIMIT 1");

			//if result is found..
			if ($result->num_rows() > 0)
			{
				foreach ($result->result() as $row)
				{

					//fetching order if company id found..
					if($row->company_id > 0){

						$res_order = array();

						// Fetching ROLE of company
						$companyInfo = $this->db->query("SELECT `role` FROM `company` WHERE `id` = '".$row->company_id."'")->result();
						if(!empty($companyInfo)){

							// If ROLE is SUPER then
							if($companyInfo[0]->role == 'super'){

								// Fetching sub-shop's ID
								$subadminsIds = $this->db->query("SELECT `id` FROM `company` WHERE `parent_id` = '".$row->company_id."' AND `role` = 'sub'")->result_array();
								if(!empty($subadminsIds)){

									$subadminsId = implode(',', array_map(array("Test_c","get_id"), $subadminsIds));
									print_r($subadminsId); die;
									//foreach ($subadminsIds as $subadminsId){
									if($today_or_all == 2){
										//$res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
										$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
									}else{
										// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
										$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 ORDER BY `id` DESC LIMIT 1")->result_array();
									}

								}
							}else{
								if($today_or_all == 2){
									$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
								}else{
									// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
									$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 ORDER BY `id` DESC LIMIT 1")->result_array();
								}
							}
						}


						if (!empty($res_order))
						{
							// Sending order id as response
							echo $res_order['0']['id'];
							exit();
						}else{
							// IF NO RECORDS FOUND TO PRINT
							show_404();
							exit;
						}
					}else{
						// IF NO RECORDS FOUND TO PRINT
						show_404();
						exit;
					}
				}
			}else{
				// IF NO RECORDS FOUND TO PRINT
				show_404();
				exit;
			}

		}
		elseif ($api_key && $order_id){

			$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `orders`.`id` = ".$order_id." LIMIT 1")->result_array();

			if (!empty($res_order)){

				$order_data = array();
				$order_details_data = array();

				foreach ($res_order as $order)
				{
					$order_data = $order;

					//if order id found
					if($order['id'] > 0){

						//fetching order details for this order id..
						$res_order_details = $this->db->query("SELECT * FROM `order_details`,`products` WHERE `orders_id` = '".$order['id']."' AND `order_details`.`products_id` = `products`.`id`");
						$query_log = $this->db->last_query();

						//if order details found for this order id..
						//if order found..
						if ($res_order_details->num_rows() > 0)
						{
							//$order_details_data = $res_order_details;
							foreach ($res_order_details->result_array() as $order_details)
							{
								//$res_disc = $this->db->query("SELECT * FROM `products_discount` WHERE `products_id` = '".$order_details['products_id']."'");
								$res_disc = $this->db->get_where('products_discount', array('products_id' => $order_details['products_id']), 1, 0);
								$res_disc_arr = $res_disc->result_array();
								if(isset($res_disc_arr[0])){
									$order_details['product_discount'] = $res_disc_arr[0]['discount_per_qty'];
								} else {
									$order_details['product_discount'] = 0;
								}

								$order_details_data[] = $order_details;
							}
						}
					}

					//preparing mail content..
					$order_id = $order['id'];
					$client_id = $order['clients_id'];

					if( !$order_id || !$client_id ){
						return false;
					}

					$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
					$this->db->join('country','country.id = clients.country_id','left');
					$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
					$company = $this->db->where( array('id'=>$order['company_id']) )->get( 'company' )->row();

					$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order['company_id']) )->get( 'client_numbers' )->result();
					$c_discount_number = '--';
					if(!empty($client_discount_number)){
						$c_discount_number = $client_discount_number['0']->discount_card_number;
					}

					$sub_admin = array();
					if($company->role == 'super'){
						$this->db->select("company_name,email_ads");
						$sub_admin = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row_array();
					}

					$this->db->select('emailid, subject_emails, orderreceived_msg, disable_price, activate_discount_card, disc_per_amount, disc_after_amount');
					$company_gs = $this->db->where( array('company_id'=>$order['company_id']) )->get( 'general_settings' )->result();
					if(!empty($company_gs))
						$company_gs = $company_gs['0'];

					if( !empty($client) && !empty($company) && !empty($company_gs) )
					{
						$company_email = $company_gs->emailid;
						$company_name = $company->company_name;
						if(!empty($sub_admin))
							$company_name = $sub_admin['company_name'];

						$orderreceived_subject = $company_gs->subject_emails; //'E-mail onderwerp';
						$orderreceived_msg = $company_gs->orderreceived_msg;
						$products_pirceshow_status = $company_gs->disable_price;
						$is_set_discount_card = $company_gs->activate_discount_card;

						$Options4 = '';

						if(!empty($sub_admin)){
							if($sub_admin['company_name'] == "1")
							{
								$email_messages = $this->db->get('email_messages')->row();
								$Options4 = $email_messages->emailads_text_message;
							}
						}else{
							if($company->email_ads == "1")
							{
								$email_messages = $this->db->get('email_messages')->row();
								$Options4 = $email_messages->emailads_text_message;
							}
						}

						// CARDGATE PAYMENT
						$order_data['billing_option'] = 'Cardgate';
						if($order_data['payment_via_paypal'] == 2){
							$this->db->select('billing_option');
							$transaction_info = $this->db->get_where('payment_transaction', array('order_id' => $order_data['temp_id']))->result();
							if(!empty($transaction_info))
								$order_data['billing_option'] = $transaction_info['0']->billing_option;
						}


						$mail_data['order_id'] = $order_id;
						$mail_data['order_data'] = $order_data;
						$mail_data['ip_address'] = $order_data['ip_address'];
						$mail_data['client'] = $client;
						$mail_data['company_name'] = $company_name;
						$mail_data['products_pirceshow_status'] = $products_pirceshow_status;
						$mail_data['order_details_data'] = $order_details_data;
						//$mail_data['disc_per_client_amount'] = $disc_per_client_amount;
						$mail_data['company_gs'] = $company_gs;
						$mail_data['c_discount_number'] = $c_discount_number;
						$mail_data['Options4'] = $Options4;
						$mail_data['orderreceived_msg'] = $orderreceived_msg;
						$mail_data['is_set_discount_card'] = $is_set_discount_card;
						$mail_data['font_size'] = $font_size;
						$mail_data['is_international'] = false;
						$mail_data['is_send_order_mail'] = true;

						//countries details for international delivery
						if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
							$mail_data['is_international'] = true;
						}

						//$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
						if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
							$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin_int', $mail_data, true );
						}
						else{
							$mail_body = $this->load->view( 'mail_templates/order_success_mail_to_admin', $mail_data, true );
						}

						$insert_log = array(
							'order_id' => $order_id,
							'template' => htmlspecialchars($mail_body),
							'order_detail_query' => $query_log,
							'meta' => $api_key.'=='.$font_size.'=='.$today_or_all.'=='.$update_db,
							'order_details' => json_encode($order_details_data),
							'date' => date("Y-m-d H:i:s")
						);

						//$this->db->insert('print_master_log', $insert_log);

						//$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order_id);

						//echo "succcess";
						header("Content-Type: text/html; charset=UTF-8"); // For showing dutch accent as it is..
						echo $mail_body;
					}
				}
			}else{
				// IF NO RECORDS FOUND TO PRINT
				show_404();
				exit;
			}
		}else{
			show_404();
			exit;
		}
	}

	function get_id($entry) {
		return $entry['id'];
	}

	function get_specific_pro_det(){
		$this->db->distinct();
		$this->db->select('products.id');
		$this->db->join('fdd_pro_quantity','fdd_pro_quantity.obs_pro_id = products.id');
		$where = '((products.semi_product = 1 AND products.direct_kcp = 0) OR (products.semi_product = 0))';
		$this->db->where($where);
		$this->db->where(array('products.company_id'=>4018));
		$this->db->order_by('products.id');
		$result = $this->db->get('products')->result_array();
		echo $this->db->last_query();
		echo '<pre>';print_r($result);die('111');
	}

	function test_reply_mail(){
		$this->load->helper('phpmailer');
		send_email("abhayhayaran@gmail.com","shyammishra@cedcoss.com", "TESTING", "TESTING TOO");
		echo "OK";
	}

	function add_allergenkart_default_setting(){
		$all_image = '';
		$this->db->select('id,ac_type_id,obsdesk_logo');
		$company_list = $this->db->get('company')->result_array();
		foreach($company_list as $company_id){
			$insrt_allerkart_array = array(
				'company_id'=>$company_id['id'],
				'sidebar_bg_color_1'=>'#313d4c',
				'sidebar_text_color_1'=>'#ffffff',
				'filter_bg_color_1'=>'#228be1',
				'filter_text_color_1'=>'#fff6fb',
				'apply_product_image'=>'1',
				'active_text_color_1'=>'#ffffff',
				'apply_css'=>'0',
				'allergenkaart_logo'=>$company_id['obsdesk_logo']
			);
			$this->db->insert('allergenkaart_design',$insrt_allerkart_array);

		}
	}

	function test_recipe(){
		$this->company_id = 87;
		$this->load->model('Mproducts');
		echo $show = $this->Mproducts->update_recipe();
	}

	function add_company_id(){
		$this->db->select('id');
		$comp_data = $this->db->get('company')->result_array();
		foreach($comp_data as $comp_val){

			$order_settings["company_id"] = $comp_val['id'];
			//$this->db->insert('custom_order_settings',$order_settings);
			//$this->db->insert('custom_pickup_timing',$order_settings);
		}
	}

	function add_promocode(){
		$this->db->select('company_id,promocode,promocode_text,promocode_percent,promocode_price,promocode_start,promocode_end');
		$comp_data = $this->db->get('general_settings')->result_array();
		foreach($comp_data as $comp_val){

			$order_settings["company_id"] = $comp_val['company_id'];
			$order_settings["promocode1"] = $comp_val['promocode'];
			$order_settings["promocode1_text"] = $comp_val['promocode_text'];
			$order_settings["promocode1_percent"] = $comp_val['promocode_percent'];
			$order_settings["promocode1_price"] = $comp_val['promocode_price'];
			$order_settings["promocode1_start"] = $comp_val['promocode_start'];
			$order_settings["promocode1_end"] = $comp_val['promocode_end'];

			$this->db->insert('promocode',$order_settings);
		}
	}

	function remove_pws_product(){
		$this->db->select('from_obs_pro_id');
		$query = $this->db->get('pws_assign_log')->result_array();
		foreach ($query as $val)
		{
			$obs_pro_id = $val['from_obs_pro_id'];
			$this->db->delete('products', array('id'=> $obs_pro_id));
			$this->db->delete('products_labeler', array('product_id' => $obs_pro_id));
			$this->db->delete('products_pending', array('product_id' => $obs_pro_id));
			$this->db->delete('contacted_via_mail', array('obs_pro_id' => $obs_pro_id));
		}
	}


	function update_desk_json_files(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$this->db->select('id');
		$this->db->order_by('id','asc');
		$query = $this->db->get_where('company',array('obsdesk_status'=>1))->result_array();
		$this->load->library('desk');

		foreach($query as $val)
		{
			$company_id = $val['id'];
			$infodesk_status = 1;
			$this->desk->update_desk_files_via_script($infodesk_status,$company_id);
		}
	}

	function update_allergenkaart_json_files(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$this->db->select('id');
		$this->db->order_by('id','asc');
		$this->db->where_in('ac_type_id',array('4','5','6','7'));
		$query = $this->db->get_where('company',array('status'=>'1','id'=>4612))->result_array();
		// echo "<pre>";
		// print_r($query);die;
		$this->load->library('allergenkart');

		foreach($query as $key => $val)
		{
			echo $key."||";
			if( $val['id'] != '' ) {
				$company_id = $val['id'];
				$this->allergenkart->update_allergenkart_json_files($company_id);
			}
		}
		die('DONE');
	}

	function update_shop_json_files($action = 'category_json',$comp_id = 0){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$this->db->select('shop_version,id,role,parent_id');
		$this->db->order_by('id','asc');
		$query = $this->db->get_where('company',array('status'=>'1','approved'=>'1','shop_version'=>2))->result_array();
		$this->load->library('shop');

		foreach($query as $val)
		{
			$shop_version = $val['shop_version'];
			$company_id = $val['id'];
			$role = $val['role'];
			$parent_id = $val['parent_id'];
			if ($comp_id)
			{
				$company_id = $comp_id;
			}
			$this->shop->update_json_files($shop_version,$company_id,$role,$parent_id,$action);
		}
	}

	function insert_additives(){
		$this->db->select('add_name,add_id');
		$add_t = $this->db->get('additives')->result_array();
		if (!empty($add_t)){
			foreach ($add_t as $val){
				$insert_array = array('add_name_dch' => $val['add_name']);
				$this->db->where('add_id',$val['add_id']);
				$this->db->update('additives',$insert_array);

				$insert_array = array('add_name_fr' => $val['add_name']);
				$this->db->where('add_id',$val['add_id']);
				$this->db->update('additives',$insert_array);
			}
		}
	}

	function update_semi_pro(){
		$this->db->distinct();
		$this->db->select( 'obs_pro_id' );
		$query = $this->db->get( 'fdd_pro_quantity', 10, 0 )->result_array();
		$a = array();
		foreach ($query as $key => $obs_id) {
			$this->db->select( 'fdd_pro_id, quantity' );
			$this->db->where( 'obs_pro_id', $obs_id['obs_pro_id'] );
			$this->db->order_by('quantity', 'asc');
			$a[$obs_id['obs_pro_id']] = $this->db->get( 'fdd_pro_quantity' )->result_array();
		}
		foreach ($a as $key => $value) {
			foreach ($value as $key1 => $value1) {
				$this->db->where( array( 'product_id' => $key, 'kp_id' => $value1['fdd_pro_id'] ) );
				$this->db->update( 'products_ingredients', array( 'kp_display_order' => (count($value)-$key1) ));
			}
		}
	}

		// echo "<pre>";
		// print_r($total_products);die;


	function update_products_ingredients_order(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$this->db->select('company.id,general_settings.language_id,general_settings.enbr_status');
		$this->db->join( 'general_settings', 'general_settings.company_id = company.id' );
		$this->db->where_in('ac_type_id',array('4','5','6'));
		$this->db->limit(10,0);
		$this->db->order_by("id", "asc");
		$company_data = $this->db->get_where('company',array('status'=>'1'))->result_array();
		// echo "<pre>";
		// print_r($company_data);die;
		if(!empty($company_data))
		{
			foreach($company_data as $company_data_k => $company_data_v)
			{
				$company_id = $company_data_v['id'];
				$language_id = $company_data_v['language_id'];
				$enbr_status = $company_data_v['enbr_status'];

				$fp = fopen(dirname(__FILE__)."/log_ft8.txt","a");
				fwrite($fp,"company_id: ".$company_id."\n");
				fclose($fp);

				$this->db->select( 'id' );
				$this->db->order_by( 'id', 'ASC' );
				$total_products = $this->db->get_where( 'products',array('company_id'=>$company_id) )->result_array();
				if(!empty($total_products))
				{
					foreach($total_products as $total_pro => $total_val)
					{
						$this->db->distinct();
						$this->db->select( 'fdd_pro_id,is_obs_product' );
						$this->db->order_by( 'quantity', 'DESC' );
						$total_fdd_products = $this->db->get_where( 'fdd_pro_quantity',array('obs_pro_id'=>$total_val['id']) )->result_array();
						if(!empty($total_fdd_products))
						{
							$kp_order = 0;
							foreach($total_fdd_products as $total_fdd_products_key => $total_fdd_products_val)
							{
								$kp_order ++;
								if( $total_fdd_products_val['is_obs_product'] == 1)
								{
									$product_id = $total_fdd_products_val['fdd_pro_id'];
									$this->db->select( 'proname' );
									$own_product = $this->db->get_where( 'products', array( 'id' => $product_id ) )->row_array();
									if(!empty($own_product)){
										$own_array = array(
											'product_id'		=> $total_val['id'],
											'kp_id'				=> $product_id,
											'ki_id'				=> 0,
											'ki_name'			=> $own_product['proname'],
											'display_order'		=> 1,
											'kp_display_order'	=> $kp_order,
											'date_added'		=> date('Y-m-d H:i:s'),
											'is_obs_ing'		=> 0,
											'have_all_id' 		=> 0,
											'aller_type'		=> '0',
											'aller_type_fr'		=> '0',
											'aller_type_dch'	=> '0',
											'allergence'		=> '0',
											'allergence_fr'		=> '0',
											'allergence_dch'	=> '0',
											'sub_allergence'	=> '0',
											'sub_allergence_fr'	=> '0',
											'sub_allergence_dch'=> '0'
										);
									}
									$this->db->insert('products_ingredients1', $own_array);
								}
								else
								{
									$product_id = $total_fdd_products_val['fdd_pro_id'];
									$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch' );
									$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

									if($language_id == '3'){
										$ing_var 	= '_fr';
										$short_name = $res_short['p_short_name_fr'];
									}elseif($language_id == '2'){
										$ing_var 	= '_dch';
										$short_name = $res_short['p_short_name_dch'];
									}
									else{
										$ing_var 	= '';
										$short_name = $res_short['p_short_name'];
									}
									$this->fdb->select('*');
									$this->fdb->order_by('order','ASC');
									$products_ing = $this->fdb->get_where( 'prod_ingredients', array( 'p_id' => $product_id ) )->result_array();
									$insert_aray = array();
									$disp_order = 1;
									$insert_aray[] = array(
										'product_id'		=> $total_val['id'],
										'kp_id'				=> $product_id,
										'ki_id'				=> 0,
										'ki_name'			=> $short_name,
										'display_order'		=> $disp_order,
										'kp_display_order'	=> $kp_order,
										'date_added'		=> date('Y-m-d H:i:s'),
										'is_obs_ing'		=> 0,
										'have_all_id' 		=> 0,
										'aller_type'		=> '0',
										'aller_type_fr'		=> '0',
										'aller_type_dch'	=> '0',
										'allergence'		=> '0',
										'allergence_fr'		=> '0',
										'allergence_dch'	=> '0',
										'sub_allergence'	=> '0',
										'sub_allergence_fr'	=> '0',
										'sub_allergence_dch'=> '0'
									);

									$insert_aray[] = array(
										'product_id'		=> $total_val['id'],
										'kp_id'				=> $product_id,
										'ki_id'				=> 0,
										'ki_name'			=> '(',
										'display_order'		=> $disp_order+1,
										'kp_display_order'	=> $kp_order,
										'date_added'		=> date('Y-m-d H:i:s'),
										'is_obs_ing'		=> 0,
										'have_all_id' 		=> 0,
										'aller_type'		=> '0',
										'aller_type_fr'		=> '0',
										'aller_type_dch'	=> '0',
										'allergence'		=> '0',
										'allergence_fr'		=> '0',
										'allergence_dch'	=> '0',
										'sub_allergence'	=> '0',
										'sub_allergence_fr'	=> '0',
										'sub_allergence_dch'=> '0'
									);
									if(!empty($products_ing)){
										foreach($products_ing as $products_ing_key => $products_ing_val)
										{
											$disp_order = $products_ing_key+3;
											$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
											$this->fdb->where('ing_id',$products_ing_val['i_id']);
											$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
											$ing_index 		= 'ing_name'.$ing_var;
											$display_name 	= $ingredient_info_to_add[0]->$ing_index;
											$ing_name 		= $ingredient_info_to_add[0]->ing_name;
											$ing_name_fr 	= $ingredient_info_to_add[0]->ing_name_fr;
											$ing_name_dch 	= $ingredient_info_to_add[0]->ing_name_dch;

											$ki_id = $ingredient_info_to_add[0]->ing_id;
											$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_status,$ing_var);
											if(!empty($enbr_result)){
												$ki_id = $enbr_result['ki_id'];
												$display_name = $enbr_result['ki_name'];
											}
											$insert_aray[] = array(
												'product_id'		=> $total_val['id'],
												'kp_id'				=> $product_id,
												'ki_id'				=> $ki_id,
												'ki_name'			=> $display_name,
												'display_order'		=> $disp_order,
												'kp_display_order'	=> $kp_order,
												'date_added'		=> date('Y-m-d H:i:s'),
												'is_obs_ing'		=> 0,
												'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
												'aller_type'		=> $products_ing_val['aller_type'],
												'aller_type_fr'		=> $products_ing_val['aller_type_fr'],
												'aller_type_dch'	=> $products_ing_val['aller_type_dch'],
												'allergence'		=> $products_ing_val['allergence'],
												'allergence_fr'		=> $products_ing_val['allergence_fr'],
												'allergence_dch'	=> $products_ing_val['allergence_dch'],
												'sub_allergence'	=> $products_ing_val['sub_allergence'],
												'sub_allergence_fr'	=> $products_ing_val['sub_allergence_fr'],
												'sub_allergence_dch'=> $products_ing_val['sub_allergence_dch']
											);
										}
									}
									$disp_order ++;
									$insert_aray[] = array(
										'product_id'		=> $total_val['id'],
										'kp_id'				=> $product_id,
										'ki_id'				=> 0,
										'ki_name'			=> ')',
										'display_order'		=> $disp_order,
										'kp_display_order'	=> $kp_order,
										'date_added'		=> date('Y-m-d H:i:s'),
										'is_obs_ing'		=> 0,
										'have_all_id' 		=> 0,
										'aller_type'		=> '0',
										'aller_type_fr'		=> '0',
										'aller_type_dch'	=> '0',
										'allergence'		=> '0',
										'allergence_fr'		=> '0',
										'allergence_dch'	=> '0',
										'sub_allergence'	=> '0',
										'sub_allergence_fr'	=> '0',
										'sub_allergence_dch'=> '0'
									);
								}
								$this->db->insert_batch('products_ingredients', $insert_aray);
							}
						}
					}
				}
			}
		}
	}

	private function get_e_current_nbr($id = 0,$enbr_setting = 0,$ing_var = '_dch'){
		if($id){
			$this->fdb->select('a.ing_id as enbr_id, a.ing_name'.$ing_var.' as enbr_ing_name, b.ing_id as name_id, b.ing_name'.$ing_var.' as name_ing_name');
			$this->fdb->join('ingredients a','a.ing_id = enbrs_relation.enbr_id');
			$this->fdb->join('ingredients b','b.ing_id = enbrs_relation.name_id');
			$this->fdb->where('enbrs_relation.enbr_id',$id);
			$result = $this->fdb->get('enbrs_relation')->result_array();

			if(!empty($result)){
				if($enbr_setting == 1){
					return array('ki_id'=>$result[0]['enbr_id'],'ki_name'=>$result[0]['enbr_ing_name'],'enbr_rel_ki_id'=>$result[0]['name_id'],'enbr_rel_ki_name'=>$result[0]['name_ing_name']);
				}
				elseif($enbr_setting == 2){
					return array('ki_id'=>$result[0]['name_id'],'ki_name'=>$result[0]['name_ing_name'],'enbr_rel_ki_id'=>$result[0]['enbr_id'],'enbr_rel_ki_name'=>$result[0]['enbr_ing_name']);
				}
			}
			else{
				$this->fdb->select('a.ing_id as enbr_id, a.ing_name'.$ing_var.' as enbr_ing_name, b.ing_id as name_id, b.ing_name'.$ing_var.' as name_ing_name');
				$this->fdb->join('ingredients a','a.ing_id = enbrs_relation.enbr_id');
				$this->fdb->join('ingredients b','b.ing_id = enbrs_relation.name_id');
				$this->fdb->where('enbrs_relation.name_id',$id);
				$result = $this->fdb->get('enbrs_relation')->result_array();

				if(!empty($result)){
					if($enbr_setting == 1){
						return array('ki_id'=>$result[0]['enbr_id'],'ki_name'=>$result[0]['enbr_ing_name'],'enbr_rel_ki_id'=>$result[0]['name_id'],'enbr_rel_ki_name'=>$result[0]['name_ing_name']);
					}
					elseif($enbr_setting == 2){
						return array('ki_id'=>$result[0]['name_id'],'ki_name'=>$result[0]['name_ing_name'],'enbr_rel_ki_id'=>$result[0]['enbr_id'],'enbr_rel_ki_name'=>$result[0]['enbr_ing_name']);
					}
				}
			}
		}
		return array();
	}


	function update_5656545(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		$query = $this->db->get_where( 'products_ingredients', array( 'id >' => '6192148' ) )->result_array();
		// echo '<pre>';
		// echo count($query); die;
		$this->db->insert_batch('products_ingredients2', $query );
	}
// 	function update_recipe(){
// 		if( $this->input->post('is_custom_semi') == 1 ){
// 			$custom_pro_total_wt_arr = $this->update_semi_product_contains();
// 		}

// 		$pro_id = $this->input->post('prod_id');
// 		$this->db->select('id,company_id');
// 		$shared_product_id = $this->db->get_where('products',array('parent_proid'=>$pro_id))->result_array();
// 		if (!empty($shared_product_id)){
// 			//$shared_product_id[] = array('id'=>$pro_id,'company_id'=>$this->company_id);
// 			$this->update_shared_product_recipe($shared_product_id);
// 		}

// 		//updating fdd products quantity
// 		$quant_array = $this->input->post('hidden_fdds_quantity');
// 		if($quant_array != ''){
// 			if($quant_array == '&&'){
// 				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
// 				$this->db->delete('fdd_pro_quantity');
// 			}else{
// 				$quant_array = substr($quant_array, 0, -2);
// 				$quant_arr = explode('**', $quant_array);

// 				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>0));
// 				$this->db->delete('fdd_pro_quantity');
// 				foreach ($quant_arr as $quant_ar){
// 					$quant_ar_ar = explode('#', $quant_ar);

// 					$semi_pro_id = 0;
// 					if($quant_ar_ar[5] != NULL){
// 						$semi_pro_id = $quant_ar_ar[5];
// 					}

// 					$insrt_quant_array = array(
// 							'obs_pro_id'=>$this->input->post('prod_id'),
// 							'fdd_pro_id'=>$quant_ar_ar[0],
// 							'quantity'=>$quant_ar_ar[1],
// 							'unit'=>$quant_ar_ar[4],
// 							'semi_product_id'=>$semi_pro_id
// 					);
// 					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);
// 				}
// 			}
// 		}

// 		$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
// 		if($obs_quant_array != ''){
// 			if($obs_quant_array == '&&'){
// 				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
// 				$this->db->delete('fdd_pro_quantity');

// 				$this->delete_notcontaining_products();
// 			}else{
// 				$obs_quant_array = substr($obs_quant_array, 0, -2);
// 				$quant_arr = explode('**', $obs_quant_array);

// 				$this->db->where(array('obs_pro_id'=>$this->input->post('prod_id'),'is_obs_product'=>1));
// 				$this->db->delete('fdd_pro_quantity');

// 				$this->delete_notcontaining_products();

// 				foreach ($quant_arr as $quant_ar){
// 					$quant_ar_ar = explode('#', $quant_ar);

// 					$semi_pro_id = 0;
// 					if($quant_ar_ar[5] != NULL){
// 						$semi_pro_id = $quant_ar_ar[5];
// 					}

// 					$insrt_quant_array = array(
// 							'obs_pro_id'=>$this->input->post('prod_id'),
// 							'fdd_pro_id'=>$quant_ar_ar[0],
// 							'quantity'=>$quant_ar_ar[1],
// 							'unit'=>$quant_ar_ar[4],
// 							'is_obs_product'=>1,
// 							'semi_product_id'=>$semi_pro_id
// 					);
// 					$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

// 					if($semi_pro_id == 0){
// 						$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$this->company_id))->result_array();
// 						if(empty($info)){
// 							$this->db->insert('products_pending', array('product_id' => $quant_ar_ar[0], 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
// 						}

// 						$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
// 						if(empty($con_info)){
// 							$this->db->insert('contacted_via_mail', array('obs_pro_id' => $quant_ar_ar[0]));
// 						}
// 					}
// 				}
// 			}
// 		}

// 		/*
// 		 *updating product
// 		*/
// 		$update_product_data = array(
// 				'company_id' => $this->company_id,
// 				'categories_id'=>$this->input->post('categories_id'),
// 				'subcategories_id'=>$this->input->post('subcategories_id'),
// 				'proname' => addslashes($this->input->post('proname')),
// 				'proupdated' => date('Y-m-d',time()),
// 				'is_custom_pending'=>0,
// 				'changed_fixed_product_id'=>0,
// 				'recipe_method' => $this->input->post('recipe_method_txt')
// 		);

// 		if($this->input->post('is_custom_semi') == 1 ){
// 			$update_product_data['semi_product'] = 1;
// 		}
// 		else{
// 			$update_product_data['semi_product'] = 0;
// 			$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$this->input->post('prod_id')))->result_array();
// 			foreach ($semi_info as $semi){
// 				$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 				$this->db->delete('products_ingredients_vetten',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 				$this->db->delete('products_additives',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 				$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 				$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 				$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 			}
// 			$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$this->input->post('prod_id')));
// 		}
// 		// If company not keurslager associate then add normal ingredients
// 		If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' ||$this->session->userdata('menu_type') == 'fdd_premium')){
// 			$update_product_data['ingredients'] = $this->input->post('ingredients');
// 			$update_product_data['allergence'] = $this->input->post('allergence');
// 			$update_product_data['traces_of'] = $this->input->post('traces_of');
// 		}

// 		if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
// 			$update_product_data['direct_kcp'] = $this->input->post('product_type');
// 		}

// 		$update_product_data['recipe_weight'] = $this->input->post('recipe_weight');

// 		$this->db->where('id',$this->input->post('prod_id'));
// 		$this->db->update('products', $update_product_data);

// 		if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){

// 			/**
// 			 * Updating Product Ingredients
// 			 */
// 			$this->db->delete('products_ingredients',array('product_id' => $this->input->post('prod_id')));
// 			$this->db->delete('products_ingredients_vetten',array('product_id' => $this->input->post('prod_id')));
// 			$this->db->delete('products_additives',array('product_id' => $this->input->post('prod_id')));

// 			// Adding Ingredients
// 			$this->adding_ingredients($this->input->post('prod_id'));

// 			/**
// 			 * Updating Product Traces
// 			*/
// 			$this->db->delete('products_traces',array('product_id' => $this->input->post('prod_id')));

// 			// Adding Traces
// 			$this->adding_traces($this->input->post('prod_id'));

// 			/**
// 			 * Updating Product Allergence
// 			*/
// 			$this->db->delete('products_allergence',array('product_id' => $this->input->post('prod_id')));
// 			$this->db->delete('product_sub_allergence',array('product_id' => $this->input->post('prod_id')));

// 			// Adding Allergence
// 			$this->adding_allergence($this->input->post('prod_id'));
// 		}

// 		if($this->input->post('is_custom_semi') == 1){
// 			$this->update_semi_product_quant($custom_pro_total_wt_arr);
// 			$this->update_kp_display_order($this->input->post('prod_id'));
// 		}

// 		return $this->input->post('prod_id');
// 	}

// 	function update_semi_product_contains(){
// 		$prod_id = $this->input->post('prod_id');
// 		$fdd_str = $this->input->post('hidden_fdds_quantity');
// 		$own_str = $this->input->post('hidden_own_pro_quantity');
// 		$custom_pro_total_wt_arr = array();
// 		$custom_pro_total_wt_arr_cust = array();

// 		if($fdd_str == '' && $own_str == ''){
// 			//no need to update(no changes in containing products)
// 		}else{
// 			// when all products form a semi-products got removed
// 			if($fdd_str == '&&' && $own_str == '&&'){
// 				$this->db->where('semi_product_id',$prod_id);
// 				$this->db->group_by('obs_pro_id');
// 				$results = $this->db->get('fdd_pro_quantity')->result_array();

// 				foreach ($results as $my_result){
// 					$this->db->select_sum('quantity');
// 					$this->db->where(array('obs_pro_id'=>$my_result['obs_pro_id'],'semi_product_id'=>$prod_id));
// 					$sums = $this->db->get('fdd_pro_quantity')->result_array();

// 					if(!empty($sums)){
// 						$new_short_arr = array(
// 								'obs_id' => $my_result['obs_pro_id'],
// 								'total'=>$sums[0]['quantity']
// 						);

// 						$custom_pro_total_wt_arr[] =$new_short_arr;
// 					}

// 					$this->db->where('semi_product_id',$my_result['obs_pro_id']);
// 					$this->db->group_by('obs_pro_id');
// 					$my_custom_results = $this->db->get('fdd_pro_quantity')->result_array();

// 					foreach ($my_custom_results as $cust_result){
// 						$this->db->select_sum('quantity');
// 						$this->db->where(array('obs_pro_id'=>$cust_result['obs_pro_id'],'semi_product_id'=>$my_result['obs_pro_id']));
// 						$cust_sums = $this->db->get('fdd_pro_quantity')->result_array();

// 						if(!empty($cust_sums)){
// 							$new_short_array = array(
// 									'obs_id' => $cust_result['obs_pro_id'],
// 									'total'=>$cust_sums[0]['quantity']
// 							);

// 							$custom_pro_total_wt_arr_cust[] =$new_short_array;
// 						}
// 					}
// 				}

// 				foreach ($results as $result){
// 					$total_wt = 0;
// 					$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'semi_product_id'=>$prod_id));
// 					$res1 = $this->db->get('fdd_pro_quantity')->result_array();

// 					foreach ($res1 as $res){
// 						$total_wt += $res['quantity'];
// 					}

// 					$this->db->select('recipe_weight');
// 					$this->db->where('id',$result['obs_pro_id']);
// 					$wt = $this->db->get('products')->result_array();
// 					if(!empty($wt)){
// 						$re_wt = $wt[0]['recipe_weight'];
// 					}else{
// 						$re_wt = 0;
// 					}
// 					$new_wt = ($re_wt*1000)-$total_wt;
// 					if($new_wt >= 0){
// 						$new_wt = $new_wt/1000;

// 						//update custom product recipe weight
// 						$this->db->where('id',$result['obs_pro_id']);
// 						$this->db->update('products', array('recipe_weight'=>$this->number2db($new_wt)));

// 						//Delete products used in custom product due to semi product
// 						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'semi_product_id'=>$prod_id));
// 						$this->db->delete('fdd_pro_quantity');
// 						$rest_arr = array();
// 						foreach ($res1 as $res){
// 							$rest_arr[] = $res['fdd_pro_id'];
// 							//deleting ingredients from custom products
// 							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
// 							$this->db->delete('products_ingredients');

// 							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
// 							$this->db->delete('products_ingredients_vetten');

// 							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
// 							$this->db->delete('products_additives');

// 							//deleting allergens from custom products
// 							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
// 							$this->db->delete('products_allergence');

// 							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
// 							$this->db->delete('product_sub_allergence');

// 							//deleting traces from custom products
// 							$this->db->where(array('product_id'=>$res['obs_pro_id'],'kp_id'=>$res['fdd_pro_id']));
// 							$this->db->delete('products_traces');
// 						}
// 					}
// 					$this->db->where('semi_product_id',$result['obs_pro_id']);
// 					$this->db->group_by('obs_pro_id');
// 					$results_cust = $this->db->get('fdd_pro_quantity')->result_array();
// 					foreach ($results_cust as $results_cust_val){
// 						$total_wt = 0;
// 						$this->db->where(array('obs_pro_id'=>$results_cust_val['obs_pro_id'],'semi_product_id'=>$result['obs_pro_id']));
// 						$res1 = $this->db->get('fdd_pro_quantity')->result_array();
// 						$count = 0;
// 						foreach ($res1 as $res){
// 							$total_wt += $res['quantity'];
// 							$count++;
// 						}

// 						$this->db->select('recipe_weight');
// 						$this->db->where('id',$results_cust_val['obs_pro_id']);
// 						$wt = $this->db->get('products')->result_array();
// 						if(!empty($wt)){
// 							$re_wt = $wt[0]['recipe_weight'];
// 						}else{
// 							$re_wt = 0;
// 						}
// 						$new_wt = ($re_wt*1000)-$total_wt;
// 						if($new_wt >= 0){
// 							$new_wt = $new_wt/1000;

// 							if($count == count($rest_arr)){
// 								//update custom product recipe weight
// 								$this->db->where('id',$results_cust_val['obs_pro_id']);
// 								$this->db->update('products', array('recipe_weight'=>$new_wt));
// 							}
// 							foreach ($rest_arr as $rest){
// 								//Delete products used in custom product due to semi product
// 								$this->db->where(array('obs_pro_id'=>$results_cust_val['obs_pro_id'],'fdd_pro_id'=>$rest,'semi_product_id'=>$result['obs_pro_id']));
// 								$this->db->delete('fdd_pro_quantity');

// 								//foreach ($res1 as $res){
// 								//deleting ingredients from custom products
// 								$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
// 								$this->db->delete('products_ingredients');

// 								$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
// 								$this->db->delete('products_ingredients_vetten');

// 								$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
// 								$this->db->delete('products_additives');

// 								//deleting allergens from custom products
// 								$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
// 								$this->db->delete('products_allergence');

// 								$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
// 								$this->db->delete('product_sub_allergence');

// 								//deleting traces from custom products
// 								$this->db->where(array('product_id'=>$results_cust_val['obs_pro_id'],'kp_id'=>$rest));
// 								$this->db->delete('products_traces');
// 								//}
// 							}
// 						}
// 					}
// 				}
// 			}else{

// 				//making a array of total weight of semi product used in custom products with their id
// 				$this->db->where('semi_product_id',$prod_id);
// 				$this->db->group_by('obs_pro_id');
// 				$my_results = $this->db->get('fdd_pro_quantity')->result_array();

// 				foreach ($my_results as $my_result){
// 					$this->db->select_sum('quantity');
// 					$this->db->where(array('obs_pro_id'=>$my_result['obs_pro_id'],'semi_product_id'=>$prod_id));
// 					$sums = $this->db->get('fdd_pro_quantity')->result_array();

// 					if(!empty($sums)){
// 						$new_short_arr = array(
// 								'obs_id' => $my_result['obs_pro_id'],
// 								'total'=>$sums[0]['quantity']
// 						);

// 						$custom_pro_total_wt_arr[] =$new_short_arr;
// 					}

// 					$this->db->where('semi_product_id',$my_result['obs_pro_id']);
// 					$this->db->group_by('obs_pro_id');
// 					$my_custom_results = $this->db->get('fdd_pro_quantity')->result_array();

// 					foreach ($my_custom_results as $cust_result){
// 						$this->db->select_sum('quantity');
// 						$this->db->where(array('obs_pro_id'=>$cust_result['obs_pro_id'],'semi_product_id'=>$my_result['obs_pro_id']));
// 						$cust_sums = $this->db->get('fdd_pro_quantity')->result_array();

// 						if(!empty($cust_sums)){
// 							$new_short_array = array(
// 									'obs_id' => $cust_result['obs_pro_id'],
// 									'total'=>$cust_sums[0]['quantity']
// 							);

// 							$custom_pro_total_wt_arr_cust[] =$new_short_array;
// 						}
// 					}
// 				}

// 				/*
// 				 * get total products used inside semi product
// 				*/
// 				$this->db->where(array('obs_pro_id'=>$prod_id));
// 				$results = $this->db->get('fdd_pro_quantity')->result_array();

// 				$old_added_array = array();
// 				foreach ($results as $result){
// 					$old_added_array[]= $result['fdd_pro_id'];
// 				}

// 				/*
// 				 * create array of new fdd products inside semi product when insert
// 				*/
// 				$new_added_array1 = array();
// 				if($fdd_str != '' && $fdd_str != '&&'){
// 					$new_added= explode('**', $fdd_str);
// 					foreach ($new_added as $new_added_item){
// 						if($new_added_item != ''){
// 							$short_arr = explode("#", $new_added_item);
// 							$new_added_array1[] = $short_arr[0];
// 						}
// 					}
// 				}

// 				/*
// 				 * create array of new own products inside semi product when insert
// 				*/
// 				$new_added_array2 = array();
// 				if($own_str != '' && $own_str != '&&'){
// 					$new_added = explode('**', $own_str);
// 					foreach ($new_added as $new_added_item){
// 						if($new_added_item != ''){
// 							$short_arr = explode("#", $new_added_item);
// 							$new_added_array2[] = $short_arr[0];
// 						}
// 					}
// 				}

// 				$new_added_array = array_merge($new_added_array1, $new_added_array2);

// 				$to_del_arr = array_diff($old_added_array, $new_added_array);
// 				$to_add_arr = array_diff($new_added_array, $old_added_array);

// 				$this->db->where('semi_product_id',$prod_id);
// 				$this->db->group_by('obs_pro_id');
// 				$results1 = $results2 = $this->db->get('fdd_pro_quantity')->result_array();
// 				$extra_arr = array();

// 				foreach ( $to_del_arr as $to_del){
// 					/*$this->db->where('semi_product_id',$prod_id);
// 						$this->db->group_by('obs_pro_id');
// 					$results1 = $this->db->get('fdd_pro_quantity')->result_array();*/

// 					foreach ($results1 as $result){

// 						$result_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id !='=>$prod_id))->result_array();
// 						if(!empty($result_s)){
// 							$extra_arr[] = $to_del;
// 							$extra_ids[$result['obs_pro_id']][] = $to_del;
// 							for($c = 0; $c < count($result_s); $c++){
// 								$to_add_arr[] = $to_del;
// 								if($result_s[$c]['is_obs_product'] == 0)
// 									$new_added_array1[] = $to_del;
// 								elseif($result_s[$c]['is_obs_product'] == 1)
// 								$new_added_array2[] = $to_del;
// 							}
// 						}

// 						$this->db->where(array('obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id'=>$prod_id));
// 						$this->db->delete('fdd_pro_quantity');

// 						//deleting ingredients, allergens and traces
// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
// 						$this->db->delete('products_ingredients');

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
// 						$this->db->delete('products_ingredients_vetten');

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
// 						$this->db->delete('products_additives');

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
// 						$this->db->delete('products_allergence');

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
// 						$this->db->delete('product_sub_allergence');

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],'kp_id'=>$to_del));
// 						$this->db->delete('products_traces');


// 						$this->db->where('semi_product_id',$result['obs_pro_id']);
// 						$this->db->group_by('obs_pro_id');
// 						$results3 = $this->db->get('fdd_pro_quantity')->result_array();

// 						if(!empty($results3)){
// 							foreach ($results3 as $result_new){

// 								$result_new_s = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$result_new['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id !='=>$result_new['semi_product_id']))->result_array();
// 								if(!empty($result_new_s)){
// 									$extra_new_ids[$result_new['obs_pro_id']][] = $to_del;
// 									for($c = 0; $c < count($result_new_s); $c++){
// 										$to_add_new_arr[] = $to_del;
// 										if($result_new_s[$c]['is_obs_product'] == 0)
// 											$new_added_new_array1[] = $to_del;
// 										elseif($result_new_s[$c]['is_obs_product'] == 1)
// 										$new_added_new_array2[] = $to_del;
// 									}
// 								}

// 								$this->db->where(array('obs_pro_id'=>$result_new['obs_pro_id'],'fdd_pro_id'=>$to_del,'semi_product_id'=>$result_new['semi_product_id']));
// 								$this->db->delete('fdd_pro_quantity');

// 								//deleting ingredients, allergens and traces
// 								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
// 								$this->db->delete('products_ingredients');

// 								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
// 								$this->db->delete('products_ingredients_vetten');

// 								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
// 								$this->db->delete('products_additives');

// 								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
// 								$this->db->delete('products_allergence');

// 								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
// 								$this->db->delete('product_sub_allergence');

// 								$this->db->where(array('product_id'=>$result_new['obs_pro_id'],'kp_id'=>$to_del));
// 								$this->db->delete('products_traces');
// 							}
// 						}
// 					}
// 				}
// 				//	$joined_str = $fdd_str.$own_str;
// 				//	$joined_arr = explode('**', $joined_str);

// 				//E-nbr status
// 				$enbr_setting = $this->get_enbr_status($this->company_id);

// 				$extra_arr = array_unique($extra_arr);

// 				foreach ($to_add_arr as $to_add){
// 					/*$this->db->where('semi_product_id',$prod_id);
// 						$this->db->group_by('obs_pro_id');
// 					$results2 = $this->db->get('fdd_pro_quantity')->result_array();*/
// 					$short_name= '';
// 					$ingis = array();
// 					$alls = array();
// 					$trs = array();

// 					if (in_array($to_add, $new_added_array1)) {
// 						//collecting information of fdd product from fooddesk database
// 						$this->fdb = $this->load->database('fdb',TRUE);
// 						$this->fdb->select('p_short_name_dch');;
// 						$this->fdb->where('p_id',$to_add);
// 						$res_short_name = $this->fdb->get('products')->result_array();
// 						if(!empty($res_short_name)){
// 							$short_name = $res_short_name[0]['p_short_name_dch'];
// 						}

// 						$this->fdb->select('prod_ingredients.i_id,ip1.prefix,ingredients.ing_name_dch,ingredients.have_all_id');
// 						$this->fdb->where('p_id',$to_add);
// 						$this->fdb->join('ingredients','prod_ingredients.i_id = ingredients.ing_id');
// 						$this->fdb->join('ingredient_prefixes as ip1','prod_ingredients.i_id = ip1.ing_id AND prod_ingredients.p_id = ip1.product_id','left');
// 						$ingis = $this->fdb->get('prod_ingredients')->result_array();

// 						$this->fdb->select('prod_ingredients_vetten.i_id,ingredients.ing_name_dch,ingredients.have_all_id');
// 						$this->fdb->where('p_id',$to_add);
// 						$this->fdb->join('ingredients','prod_ingredients_vetten.i_id = ingredients.ing_id');
// 						$vetten = $this->fdb->get('prod_ingredients_vetten')->result_array();

// 						$this->fdb->select('prod_additives.i_id,prod_additives.add_id,ingredients.ing_name_dch,ingredients.have_all_id');
// 						$this->fdb->where('p_id',$to_add);
// 						$this->fdb->join('ingredients','prod_additives.i_id = ingredients.ing_id');
// 						$add = $this->fdb->get('prod_additives')->result_array();

// 						$this->fdb->where('p_id',$to_add);
// 						$this->fdb->join('allergence','prod_allergence.a_id = allergence.all_id');
// 						$alls = $this->fdb->get('prod_allergence')->result_array();

// 						$this->fdb->where('p_id',$to_add);
// 						$this->fdb->join('sub_allergence','prod_sub_allergence.a_id = sub_allergence.all_id');
// 						$sub_alls = $this->fdb->get('prod_sub_allergence')->result_array();

// 						$this->fdb->where('p_id',$to_add);
// 						$this->fdb->join('traces','prod_traces.t_id = traces.t_id');
// 						$trs = $this->fdb->get('prod_traces')->result_array();
// 						$this->db = $this->load->database('default',TRUE);
// 					}elseif (in_array($to_add, $new_added_array2)){
// 						$this->db->select('proname');
// 						$this->db->where('id',$to_add);
// 						$pro_name = $this->db->get('products')->result_array();
// 						if(!empty($pro_name)){
// 							$short_name = $pro_name[0]['proname'];
// 						}
// 					}
// 					foreach($results2 as $key=>$result){
// 						$flag = true;

// 						if(in_array($to_add, $extra_arr)){
// 							if(isset($extra_ids) && isset($extra_ids[$result['obs_pro_id']]) && in_array($to_add, $extra_ids[$result['obs_pro_id']])){
// 								$flag = false;
// 							}
// 							else{
// 								continue;
// 							}
// 						}

// 						if (in_array($to_add, $new_added_array1)) {
// 							//added new fdd product as a product of semi product in custom product
// 							if($flag){
// 								$new_added= explode('**', $fdd_str);
// 								$unit = 'g';
// 								foreach ($new_added as $new_added_item){
// 									if($new_added_item != ''){
// 										$short_arr = explode("#", $new_added_item);
// 										if($short_arr[0] == $to_add){
// 											$unit = $short_arr[4];
// 											break;
// 										}
// 									}
// 								}
// 								$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>0,'obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$prod_id,'unit'=>$unit));
// 							}
// 							//adding ingredients for new added fdd product
// 							$ing_insert_array = array(
// 									'product_id'=>$result['obs_pro_id'],
// 									'kp_id'=> $to_add,
// 									'ki_id'=>0,
// 									'ki_name'=> $short_name,
// 									'display_order'=>1,
// 									'kp_display_order'=>$key+100,
// 									'date_added'=>date('Y-m-d h:i:s')
// 							);

// 							$this->db->insert('products_ingredients',$ing_insert_array);

// 							if(!empty($ingis)){
// 								$total_ing = 0;
// 								$ing_insert_array = array(
// 										'product_id'=>$result['obs_pro_id'],
// 										'kp_id'=> $to_add,
// 										'ki_id'=>0,
// 										'ki_name'=> '(',
// 										'display_order'=>2,
// 										'kp_display_order'=>$key+100,
// 										'date_added'=>date('Y-m-d h:i:s')
// 								);
// 								$this->db->insert('products_ingredients',$ing_insert_array);

// 								foreach ($ingis as $ing_key=>$ingi){
// 									if(!$ingi['prefix']){
// 										$ingi['prefix'] = '';
// 									}

// 									$display_name = $ingi['ing_name_dch'];
// 									$display_result = $this->get_display_name($ingi['i_id']);
// 									if($display_result != ''){
// 										$display_name = $display_result;
// 									}

// 									$ing_insert_array = array(
// 											'product_id'=>$result['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'ki_id'=>$ingi['i_id'],
// 											'ki_name'=> $display_name,
// 											'prefix'=> $ingi['prefix'],
// 											'display_order'=>$ing_key+3,
// 											'kp_display_order'=>$key+100,
// 											'date_added'=>date('Y-m-d h:i:s'),
// 											'have_all_id'=>$ingi['have_all_id']
// 									);
// 									$this->db->insert('products_ingredients',$ing_insert_array);
// 									$total_ing++;
// 								}

// 								$ing_insert_array = array(
// 										'product_id'=>$result['obs_pro_id'],
// 										'kp_id'=> $to_add,
// 										'ki_id'=>0,
// 										'ki_name'=> ')',
// 										'display_order'=>$total_ing+3,
// 										'kp_display_order'=>$key+100,
// 										'date_added'=>date('Y-m-d h:i:s')
// 								);
// 								$this->db->insert('products_ingredients',$ing_insert_array);
// 							}

// 							foreach ($vetten as $ing_key=>$vet){
// 								$display_name = $vet['ing_name_dch'];
// 								$display_result = $this->get_display_name($vet['i_id']);
// 								if($display_result != ''){
// 									$display_name = $display_result;
// 								}

// 								$ing_insert_array = array(
// 										'product_id'=>$result['obs_pro_id'],
// 										'kp_id'=> $to_add,
// 										'ki_id'=>$vet['i_id'],
// 										'ki_name'=> $display_name,
// 										'display_order'=>$ing_key+3,
// 										'kp_display_order'=>$key+100,
// 										'date_added'=>date('Y-m-d h:i:s'),
// 										'have_all_id'=>$vet['have_all_id']
// 								);
// 								$this->db->insert('products_ingredients_vetten',$ing_insert_array);
// 							}

// 							foreach ($add as $ing_key=>$ingi){
// 								$display_name = $ingi['ing_name_dch'];
// 								$ki_id = $ingi['i_id'];
// 								$display_name1 = '';
// 								$enbr_rel_ki_id = 0;

// 								$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_setting['enbr_status']);
// 								if(!empty($enbr_result)){
// 									$ki_id = $enbr_result['ki_id'];
// 									$display_name = $enbr_result['ki_name'];
// 									$display_result = $this->get_display_name($enbr_result['ki_id']);
// 									if($display_result != ''){
// 										$display_name = $display_result;
// 									}

// 									$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
// 									$display_name1 = $enbr_result['enbr_rel_ki_name'];
// 									$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id']);
// 									if($display_result != ''){
// 										$display_name1 = $display_result;
// 									}
// 								}
// 								else{
// 									$display_result = $this->get_display_name($ki_id);
// 									if($display_result != ''){
// 										$display_name = $display_result;
// 									}
// 								}

// 								$ing_insert_array = array(
// 										'product_id'=>$result['obs_pro_id'],
// 										'kp_id'=> $to_add,
// 										'add_id'=>$ingi['add_id'],
// 										'ki_id'=>$ki_id,
// 										'ki_name'=> $display_name,
// 										'enbr_rel_ki_id' => $enbr_rel_ki_id,
// 										'enbr_rel_ki_name' => $display_name1,
// 										'display_order'=>$ing_key+3,
// 										'kp_display_order'=>$key+100,
// 										'date_added'=>date('Y-m-d h:i:s'),
// 										'have_all_id'=>$ingi['have_all_id']
// 								);
// 								$this->db->insert('products_additives',$ing_insert_array);
// 							}

// 							//adding allergence
// 							if(!empty($alls)){
// 								foreach ($alls as $all_key=>$all){

// 									$ing_insert_array = array(
// 											'product_id'=>$result['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'ka_id'=>$all['a_id'],
// 											'ka_name'=> $all['all_name_dch'],
// 											'display_order'=>$all_key+1,
// 											'date_added'=>date('Y-m-d h:i:s')
// 									);
// 									$this->db->insert('products_allergence',$ing_insert_array);
// 								}
// 							}

// 							//adding suballergence
// 							if(!empty($sub_alls)){
// 								foreach ($sub_alls as $all_key=>$all){

// 									$ing_insert_array = array(
// 											'product_id'=>$result['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'parent_ka_id'=>$all['parent_all_id'],
// 											'sub_ka_id'=>$all['a_id'],
// 											'sub_ka_name'=> $all['all_name_dch'],
// 											'display_order'=>$all_key+1,
// 											'date_added'=>date('Y-m-d h:i:s')
// 									);
// 									$this->db->insert('product_sub_allergence',$ing_insert_array);
// 								}
// 							}

// 							//adding traces of product
// 							if(!empty($trs)){
// 								foreach ($trs as $tr_key=>$tr){

// 									$ing_insert_array = array(
// 											'product_id'=>$result['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'kt_id'=>$tr['t_id'],
// 											'kt_name'=> $tr['t_name_dch'],
// 											'display_order'=>$tr_key+1,
// 											'date_added'=>date('Y-m-d h:i:s')
// 									);
// 									$this->db->insert('products_traces',$ing_insert_array);
// 								}
// 							}
// 						}else if (in_array($to_add, $new_added_array2)) {
// 							//added new own product as a product od semi product in custom product
// 							if($flag){
// 								$new_added = explode('**', $own_str);
// 								$unit = 'g';
// 								foreach ($new_added as $new_added_item){
// 									if($new_added_item != ''){
// 										$short_arr = explode("#", $new_added_item);
// 										if($short_arr[0] == $to_add){
// 											$unit = $short_arr[4];
// 											break;
// 										}
// 									}
// 								}
// 								$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>1,'obs_pro_id'=>$result['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$prod_id,'unit'=>$unit));
// 							}
// 							$info = $this->db->get_where('products_pending',array('product_id'=>$to_add,'company_id'=>$this->company_id))->result_array();
// 							if(empty($info)){
// 								$this->db->insert('products_pending', array('product_id' => $to_add, 'company_id' => $this->company_id, 'date' => date('Y-m-d h:i:s')));
// 							}

// 							$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$to_add))->result_array();
// 							if(empty($con_info)){
// 								$this->db->insert('contacted_via_mail', array('obs_pro_id' => $to_add));
// 							}

// 							//adding product name of fixed product in ingredints list
// 							$ing_insert_array = array(
// 									'product_id'=>$result['obs_pro_id'],
// 									'kp_id'=> $to_add,
// 									'ki_id'=>0,
// 									'ki_name'=> $short_name,
// 									'display_order'=>1,
// 									'kp_display_order'=>$key+100,
// 									'date_added'=>date('Y-m-d h:i:s'),
// 									'is_obs_ing'=>1
// 							);

// 							$this->db->insert('products_ingredients',$ing_insert_array);
// 						}

// 						$this->db->where('semi_product_id',$result['obs_pro_id']);
// 						$this->db->group_by('obs_pro_id');
// 						$results4 = $this->db->get('fdd_pro_quantity')->result_array();

// 						foreach ($results4 as $key_p=>$result4){
// 							if (in_array($to_add, $new_added_array1)) {
// 								//added new fdd product as a product of semi product in custom product
// 								if($flag){
// 									$new_added= explode('**', $fdd_str);
// 									$unit = 'g';
// 									foreach ($new_added as $new_added_item){
// 										if($new_added_item != ''){
// 											$short_arr = explode("#", $new_added_item);
// 											if($short_arr[0] == $to_add){
// 												$unit = $short_arr[4];
// 												break;
// 											}
// 										}
// 									}
// 									$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>0,'obs_pro_id'=>$result4['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$result4['semi_product_id'],'unit'=>$unit));
// 								}
// 								//adding ingredients for new added fdd product
// 								$ing_insert_array = array(
// 										'product_id'=>$result4['obs_pro_id'],
// 										'kp_id'=> $to_add,
// 										'ki_id'=>0,
// 										'ki_name'=> $short_name,
// 										'display_order'=>1,
// 										'kp_display_order'=>$key+100,
// 										'date_added'=>date('Y-m-d h:i:s')
// 								);

// 								$this->db->insert('products_ingredients',$ing_insert_array);

// 								if(!empty($ingis)){
// 									$total_ing = 0;
// 									$ing_insert_array = array(
// 											'product_id'=>$result4['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'ki_id'=>0,
// 											'ki_name'=> '(',
// 											'display_order'=>2,
// 											'kp_display_order'=>$key+100,
// 											'date_added'=>date('Y-m-d h:i:s')
// 									);
// 									$this->db->insert('products_ingredients',$ing_insert_array);

// 									foreach ($ingis as $ing_key=>$ingi){
// 										if(!$ingi['prefix']){
// 											$ingi['prefix'] = '';
// 										}

// 										$display_name = $ingi['ing_name_dch'];
// 										$display_result = $this->get_display_name($ingi['i_id']);
// 										if($display_result != ''){
// 											$display_name = $display_result;
// 										}

// 										$ing_insert_array = array(
// 												'product_id'=>$result4['obs_pro_id'],
// 												'kp_id'=> $to_add,
// 												'ki_id'=>$ingi['i_id'],
// 												'ki_name'=> $display_name,
// 												'prefix'=> $ingi['prefix'],
// 												'display_order'=>$ing_key+3,
// 												'kp_display_order'=>$key+100,
// 												'date_added'=>date('Y-m-d h:i:s'),
// 												'have_all_id'=>$ingi['have_all_id']
// 										);
// 										$this->db->insert('products_ingredients',$ing_insert_array);
// 										$total_ing++;
// 									}

// 									$ing_insert_array = array(
// 											'product_id'=>$result4['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'ki_id'=>0,
// 											'ki_name'=> ')',
// 											'display_order'=>$total_ing+3,
// 											'kp_display_order'=>$key+100,
// 											'date_added'=>date('Y-m-d h:i:s')
// 									);
// 									$this->db->insert('products_ingredients',$ing_insert_array);
// 								}

// 								foreach ($vetten as $ing_key=>$vet){
// 									$display_name = $vet['ing_name_dch'];
// 									$display_result = $this->get_display_name($vet['i_id']);
// 									if($display_result != ''){
// 										$display_name = $display_result;
// 									}

// 									$ing_insert_array = array(
// 											'product_id'=>$result4['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'ki_id'=>$vet['i_id'],
// 											'ki_name'=> $display_name,
// 											'display_order'=>$ing_key+3,
// 											'kp_display_order'=>$key+100,
// 											'date_added'=>date('Y-m-d h:i:s'),
// 											'have_all_id'=>$vet['have_all_id']
// 									);
// 									$this->db->insert('products_ingredients_vetten',$ing_insert_array);
// 								}

// 								foreach ($add as $ing_key=>$ingi){
// 									$display_name = $ingi['ing_name_dch'];
// 									$ki_id = $ingi['i_id'];
// 									$display_name1 = '';
// 									$enbr_rel_ki_id = 0;

// 									$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_setting['enbr_status']);
// 									if(!empty($enbr_result)){
// 										$ki_id = $enbr_result['ki_id'];
// 										$display_name = $enbr_result['ki_name'];
// 										$display_result = $this->get_display_name($enbr_result['ki_id']);
// 										if($display_result != ''){
// 											$display_name = $display_result;
// 										}

// 										$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
// 										$display_name1 = $enbr_result['enbr_rel_ki_name'];
// 										$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id']);
// 										if($display_result != ''){
// 											$display_name1 = $display_result;
// 										}
// 									}
// 									else{
// 										$display_result = $this->get_display_name($ki_id);
// 										if($display_result != ''){
// 											$display_name = $display_result;
// 										}
// 									}

// 									$ing_insert_array = array(
// 											'product_id'=>$result4['obs_pro_id'],
// 											'kp_id'=> $to_add,
// 											'add_id'=>$ingi['add_id'],
// 											'ki_id'=>$ki_id,
// 											'ki_name'=> $display_name,
// 											'enbr_rel_ki_id' => $enbr_rel_ki_id,
// 											'enbr_rel_ki_name' => $display_name1,
// 											'display_order'=>$ing_key+3,
// 											'kp_display_order'=>$key+100,
// 											'date_added'=>date('Y-m-d h:i:s'),
// 											'have_all_id'=>$ingi['have_all_id']
// 									);
// 									$this->db->insert('products_additives',$ing_insert_array);
// 								}

// 								//adding allergence
// 								if(!empty($alls)){
// 									foreach ($alls as $all_key=>$all){

// 										$ing_insert_array = array(
// 												'product_id'=>$result4['obs_pro_id'],
// 												'kp_id'=> $to_add,
// 												'ka_id'=>$all['a_id'],
// 												'ka_name'=> $all['all_name_dch'],
// 												'display_order'=>$all_key+1,
// 												'date_added'=>date('Y-m-d h:i:s')
// 										);
// 										$this->db->insert('products_allergence',$ing_insert_array);
// 									}
// 								}

// 								//adding suballergence
// 								if(!empty($sub_alls)){
// 									foreach ($sub_alls as $all_key=>$all){

// 										$ing_insert_array = array(
// 												'product_id'=>$result4['obs_pro_id'],
// 												'kp_id'=> $to_add,
// 												'parent_ka_id'=>$all['parent_all_id'],
// 												'sub_ka_id'=>$all['a_id'],
// 												'sub_ka_name'=> $all['all_name_dch'],
// 												'display_order'=>$all_key+1,
// 												'date_added'=>date('Y-m-d h:i:s')
// 										);
// 										$this->db->insert('product_sub_allergence',$ing_insert_array);
// 									}
// 								}

// 								//adding traces of product
// 								if(!empty($trs)){
// 									foreach ($trs as $tr_key=>$tr){

// 										$ing_insert_array = array(
// 												'product_id'=>$result4['obs_pro_id'],
// 												'kp_id'=> $to_add,
// 												'kt_id'=>$tr['t_id'],
// 												'kt_name'=> $tr['t_name_dch'],
// 												'display_order'=>$tr_key+1,
// 												'date_added'=>date('Y-m-d h:i:s')
// 										);
// 										$this->db->insert('products_traces',$ing_insert_array);
// 									}
// 								}
// 							}else if (in_array($to_add, $new_added_array2)) {
// 								//added new own product as a product od semi product in custom product
// 								if($flag){
// 									$new_added = explode('**', $own_str);
// 									$unit = 'g';
// 									foreach ($new_added as $new_added_item){
// 										if($new_added_item != ''){
// 											$short_arr = explode("#", $new_added_item);
// 											if($short_arr[0] == $to_add){
// 												$unit = $short_arr[4];
// 												break;
// 											}
// 										}
// 									}
// 									$this->db->insert('fdd_pro_quantity',array('is_obs_product'=>1,'obs_pro_id'=>$result4['obs_pro_id'],'fdd_pro_id'=>$to_add,'quantity'=>100,'semi_product_id'=>$result4['semi_product_id'],'unit'=>$unit));
// 								}
// 								$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$to_add))->result_array();
// 								if(empty($con_info)){
// 									$this->db->insert('contacted_via_mail', array('obs_pro_id' => $to_add));
// 								}

// 								//adding product name of fixed product in ingredints list
// 								$ing_insert_array = array(
// 										'product_id'=>$result4['obs_pro_id'],
// 										'kp_id'=> $to_add,
// 										'ki_id'=>0,
// 										'ki_name'=> $short_name,
// 										'display_order'=>1,
// 										'kp_display_order'=>$key+100,
// 										'date_added'=>date('Y-m-d h:i:s'),
// 										'is_obs_ing'=>1
// 								);

// 								$this->db->insert('products_ingredients',$ing_insert_array);
// 							}
// 						}
// 					}
// 				}
// 				if(isset($to_add_new_arr)){
// 					foreach ($to_add_new_arr as $to_add){
// 						$short_name= '';
// 						$ingis = array();
// 						$alls = array();
// 						$trs = array();

// 						if (in_array($to_add, $new_added_new_array1)) {
// 							//collecting information of fdd product from fooddesk database
// 							$this->fdb = $this->load->database('fdb',TRUE);
// 							$this->fdb->select('p_short_name_dch');;
// 							$this->fdb->where('p_id',$to_add);
// 							$res_short_name = $this->fdb->get('products')->result_array();
// 							if(!empty($res_short_name)){
// 								$short_name = $res_short_name[0]['p_short_name_dch'];
// 							}

// 							$this->fdb->select('prod_ingredients.i_id,ip1.prefix,ingredients.ing_name_dch,ingredients.have_all_id');
// 							$this->fdb->where('p_id',$to_add);
// 							$this->fdb->join('ingredients','prod_ingredients.i_id = ingredients.ing_id');
// 							$this->fdb->join('ingredient_prefixes as ip1','prod_ingredients.i_id = ip1.ing_id AND prod_ingredients.p_id = ip1.product_id','left');
// 							$ingis = $this->fdb->get('prod_ingredients')->result_array();

// 							$this->fdb->select('prod_ingredients_vetten.i_id,ingredients.ing_name_dch,ingredients.have_all_id');
// 							$this->fdb->where('p_id',$to_add);
// 							$this->fdb->join('ingredients','prod_ingredients_vetten.i_id = ingredients.ing_id');
// 							$vetten = $this->fdb->get('prod_ingredients_vetten')->result_array();

// 							$this->fdb->select('prod_additives.i_id,prod_additives.add_id,ingredients.ing_name_dch,ingredients.have_all_id');
// 							$this->fdb->where('p_id',$to_add);
// 							$this->fdb->join('ingredients','prod_additives.i_id = ingredients.ing_id');
// 							$add = $this->fdb->get('prod_additives')->result_array();

// 							$this->fdb->where('p_id',$to_add);
// 							$this->fdb->join('allergence','prod_allergence.a_id = allergence.all_id');
// 							$alls = $this->fdb->get('prod_allergence')->result_array();

// 							$this->fdb->where('p_id',$to_add);
// 							$this->fdb->join('sub_allergence','prod_sub_allergence.a_id = sub_allergence.all_id');
// 							$sub_alls = $this->fdb->get('prod_sub_allergence')->result_array();

// 							$this->fdb->where('p_id',$to_add);
// 							$this->fdb->join('traces','prod_traces.t_id = traces.t_id');
// 							$trs = $this->fdb->get('prod_traces')->result_array();
// 							$this->db = $this->load->database('default',TRUE);
// 						}elseif (in_array($to_add, $new_added_new_array2)){
// 							$this->db->select('proname');
// 							$this->db->where('id',$to_add);
// 							$pro_name = $this->db->get('products')->result_array();
// 							if(!empty($pro_name)){
// 								$short_name = $pro_name[0]['proname'];
// 							}
// 						}

// 						foreach($results2 as $key=>$result){

// 							$this->db->where('semi_product_id',$result['obs_pro_id']);
// 							$this->db->group_by('obs_pro_id');
// 							$results4 = $this->db->get('fdd_pro_quantity')->result_array();

// 							foreach ($results4 as $key_p=>$result4){
// 								$flag = true;

// 								if(isset($extra_new_ids) && isset($extra_new_ids[$result4['obs_pro_id']]) && in_array($to_add, $extra_new_ids[$result4['obs_pro_id']])){
// 									$flag = false;
// 								}
// 								else{
// 									continue;
// 								}

// 								if(!$flag){
// 									if (in_array($to_add, $new_added_new_array1)) {
// 										//adding ingredients for new added fdd product
// 										$ing_insert_array = array(
// 												'product_id'=>$result4['obs_pro_id'],
// 												'kp_id'=> $to_add,
// 												'ki_id'=>0,
// 												'ki_name'=> $short_name,
// 												'display_order'=>1,
// 												'kp_display_order'=>$key+100,
// 												'date_added'=>date('Y-m-d h:i:s')
// 										);

// 										$this->db->insert('products_ingredients',$ing_insert_array);

// 										if(!empty($ingis)){
// 											$total_ing = 0;
// 											$ing_insert_array = array(
// 													'product_id'=>$result4['obs_pro_id'],
// 													'kp_id'=> $to_add,
// 													'ki_id'=>0,
// 													'ki_name'=> '(',
// 													'display_order'=>2,
// 													'kp_display_order'=>$key+100,
// 													'date_added'=>date('Y-m-d h:i:s')
// 											);
// 											$this->db->insert('products_ingredients',$ing_insert_array);

// 											foreach ($ingis as $ing_key=>$ingi){
// 												if(!$ingi['prefix']){
// 													$ingi['prefix'] = '';
// 												}

// 												$display_name = $ingi['ing_name_dch'];
// 												$display_result = $this->get_display_name($ingi['i_id']);
// 												if($display_result != ''){
// 													$display_name = $display_result;
// 												}

// 												$ing_insert_array = array(
// 														'product_id'=>$result4['obs_pro_id'],
// 														'kp_id'=> $to_add,
// 														'ki_id'=>$ingi['i_id'],
// 														'ki_name'=> $display_name,
// 														'prefix'=> $ingi['prefix'],
// 														'display_order'=>$ing_key+3,
// 														'kp_display_order'=>$key+100,
// 														'date_added'=>date('Y-m-d h:i:s'),
// 														'have_all_id'=>$ingi['have_all_id']
// 												);
// 												$this->db->insert('products_ingredients',$ing_insert_array);
// 												$total_ing++;
// 											}

// 											$ing_insert_array = array(
// 													'product_id'=>$result4['obs_pro_id'],
// 													'kp_id'=> $to_add,
// 													'ki_id'=>0,
// 													'ki_name'=> ')',
// 													'display_order'=>$total_ing+3,
// 													'kp_display_order'=>$key+100,
// 													'date_added'=>date('Y-m-d h:i:s')
// 											);
// 											$this->db->insert('products_ingredients',$ing_insert_array);
// 										}

// 										foreach ($vetten as $ing_key=>$vet){
// 											$display_name = $vet['ing_name_dch'];
// 											$display_result = $this->get_display_name($vet['i_id']);
// 											if($display_result != ''){
// 												$display_name = $display_result;
// 											}

// 											$ing_insert_array = array(
// 													'product_id'=>$result4['obs_pro_id'],
// 													'kp_id'=> $to_add,
// 													'ki_id'=>$vet['i_id'],
// 													'ki_name'=> $display_name,
// 													'display_order'=>$ing_key+3,
// 													'kp_display_order'=>$key+100,
// 													'date_added'=>date('Y-m-d h:i:s'),
// 													'have_all_id'=>$vet['have_all_id']
// 											);
// 											$this->db->insert('products_ingredients_vetten',$ing_insert_array);
// 										}

// 										foreach ($add as $ing_key=>$ingi){
// 											$display_name = $ingi['ing_name_dch'];
// 											$ki_id = $ingi['i_id'];
// 											$display_name1 = '';
// 											$enbr_rel_ki_id = 0;

// 											$enbr_result = $this->get_e_current_nbr($ki_id,$enbr_setting['enbr_status']);
// 											if(!empty($enbr_result)){
// 												$ki_id = $enbr_result['ki_id'];
// 												$display_name = $enbr_result['ki_name'];
// 												$display_result = $this->get_display_name($enbr_result['ki_id']);
// 												if($display_result != ''){
// 													$display_name = $display_result;
// 												}

// 												$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
// 												$display_name1 = $enbr_result['enbr_rel_ki_name'];
// 												$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id']);
// 												if($display_result != ''){
// 													$display_name1 = $display_result;
// 												}
// 											}
// 											else{
// 												$display_result = $this->get_display_name($ki_id);
// 												if($display_result != ''){
// 													$display_name = $display_result;
// 												}
// 											}

// 											$ing_insert_array = array(
// 													'product_id'=>$result4['obs_pro_id'],
// 													'kp_id'=> $to_add,
// 													'add_id'=>$ingi['add_id'],
// 													'ki_id'=>$ki_id,
// 													'ki_name'=> $display_name,
// 													'enbr_rel_ki_id' => $enbr_rel_ki_id,
// 													'enbr_rel_ki_name' => $display_name1,
// 													'display_order'=>$ing_key+3,
// 													'kp_display_order'=>$key+100,
// 													'date_added'=>date('Y-m-d h:i:s'),
// 													'have_all_id'=>$ingi['have_all_id']
// 											);
// 											$this->db->insert('products_additives',$ing_insert_array);
// 										}

// 										//adding allergence
// 										if(!empty($alls)){
// 											foreach ($alls as $all_key=>$all){

// 												$ing_insert_array = array(
// 														'product_id'=>$result4['obs_pro_id'],
// 														'kp_id'=> $to_add,
// 														'ka_id'=>$all['a_id'],
// 														'ka_name'=> $all['all_name_dch'],
// 														'display_order'=>$all_key+1,
// 														'date_added'=>date('Y-m-d h:i:s')
// 												);
// 												$this->db->insert('products_allergence',$ing_insert_array);
// 											}
// 										}

// 										//adding suballergence
// 										if(!empty($sub_alls)){
// 											foreach ($sub_alls as $all_key=>$all){

// 												$ing_insert_array = array(
// 														'product_id'=>$result4['obs_pro_id'],
// 														'kp_id'=> $to_add,
// 														'parent_ka_id'=>$all['parent_all_id'],
// 														'sub_ka_id'=>$all['a_id'],
// 														'sub_ka_name'=> $all['all_name_dch'],
// 														'display_order'=>$all_key+1,
// 														'date_added'=>date('Y-m-d h:i:s')
// 												);
// 												$this->db->insert('product_sub_allergence',$ing_insert_array);
// 											}
// 										}

// 										//adding traces of product
// 										if(!empty($trs)){
// 											foreach ($trs as $tr_key=>$tr){

// 												$ing_insert_array = array(
// 														'product_id'=>$result4['obs_pro_id'],
// 														'kp_id'=> $to_add,
// 														'kt_id'=>$tr['t_id'],
// 														'kt_name'=> $tr['t_name_dch'],
// 														'display_order'=>$tr_key+1,
// 														'date_added'=>date('Y-m-d h:i:s')
// 												);
// 												$this->db->insert('products_traces',$ing_insert_array);
// 											}
// 										}
// 									}else if (in_array($to_add, $new_added_new_array2)) {

// 										//adding product name of fixed product in ingredints list
// 										$ing_insert_array = array(
// 												'product_id'=>$result4['obs_pro_id'],
// 												'kp_id'=> $to_add,
// 												'ki_id'=>0,
// 												'ki_name'=> $short_name,
// 												'display_order'=>1,
// 												'kp_display_order'=>$key+100,
// 												'date_added'=>date('Y-m-d h:i:s'),
// 												'is_obs_ing'=>1
// 										);

// 										$this->db->insert('products_ingredients',$ing_insert_array);
// 									}
// 								}
// 							}
// 						}
// 					}
// 				}
// 			}
// 			/*
// 				if($fdd_str == '&&'){
// 			// 			$this->db->where(array('semi_product_id'=>$prod_id, 'is_obs_product'=>0));
// 			// 			$results = $this->db->get('fdd_pro_quantity')->result_array();
// 			// 			if(!empty($results)){
// 			// 			 	foreach ($results as $result){
// 			// 			 		$this->db->where(array('product_id'=>$result['obs_pro_id'], 'kp_id'=>$result['fdd_pro_id'],'is_obs_ing'=>0));
// 			// 			 		$this->db->delete('products_ingredients');
// 			// 			 	}
// 			// 			}

// 			// 			$this->db->where(array('semi_product_id'=>$prod_id, 'is_obs_product'=>0));
// 			// 			$this->db->delete('fdd_pro_quantity');


// 			}

// 			if($own_str == '&&'){
// 			// 			$this->db->where(array('semi_product_id'=>$prod_id, 'is_obs_product'=>1));
// 			// 			$results = $this->db->get('fdd_pro_quantity')->result_array();
// 			// 			if(!empty($results)){
// 			// 				foreach ($results as $result){
// 			// 					$this->db->where(array('product_id'=>$result['obs_pro_id'], 'kp_id'=>$result['fdd_pro_id'],'is_obs_ing'=>1));
// 			// 					$this->db->delete('products_ingredients');
// 			// 				}
// 			// 			}

// 			// 			$this->db->where(array('semi_product_id'=>$prod_id, 'is_obs_product'=>1));
// 			// 			$this->db->delete('fdd_pro_quantity');
// 			}

// 			if($fdd_str != '' && $fdd_str != '&&'){

// 			$this->db->where(array('obs_pro_id'=>$prod_id, 'is_obs_product'=>0));
// 			$results = $this->db->get('fdd_pro_quantity')->result_array();

// 			$old_added_array = array();
// 			foreach ($results as $result){
// 			$old_added_array[]= $result['fdd_pro_id'];
// 			}

// 			$new_added= explode('**', $fdd_str);
// 			$new_added_array = array();
// 			foreach ($new_added as $new_added_item){
// 			if($new_added_item != ''){
// 			$short_arr = explode("#", $new_added_item);
// 			$new_added_array[] = $short_arr[0];
// 			}
// 			}

// 			$to_del_arr = array_diff($old_added_array, $new_added_array);
// 			$to_add_arr = array_diff($new_added_array, $old_added_array);

// 			if(!empty($to_del_arr)){
// 			foreach ($to_del_arr as $to_del){
// 			// 					$this->db->where(array('fdd_pro_id'=>$to_del, 'semi_product_id'=>$prod_id));
// 			// 					$obs_pro_ids = $this->db->get('fdd_pro_quantity')->result_array();
// 			// 					if(!empty($obs_pro_ids)){
// 			// 						foreach ($obs_pro_ids as $obs_pro_id){
// 			// 							$this->db->where(array('product_id'=>$obs_pro_id['obs_pro_id'], 'kp_id'=>$obs_pro_id['fdd_pro_id'],'is_obs_ing'=>0));
// 			// 							$this->db->delete('products_ingredients');
// 			// 						}
// 			// 					}
// 			}
// 			}

// 			if(!empty($to_add_arr)){
// 			foreach ($to_add_arr as $to_add){

// 			}
// 			}
// 			print_r($to_add_arr);
// 			// 			echo "<br/>";
// 			// 			print_r($new_added_array);
// 			die();
// 			}
// 			*/
// 		}
// 		return array(
// 				'custom_pro_total_wt_arr' => $custom_pro_total_wt_arr,
// 				'custom_pro_total_wt_arr_cust' => $custom_pro_total_wt_arr_cust
// 		);
// 		//RETURN $custom_pro_total_wt_arr;
// 	}

// 	function update_shared_product_recipe($shared_product_id = array()){
// 		if (!empty($shared_product_id)){
// 			foreach ($shared_product_id as $key => $val)
// 			{
// 				$quant_array = $this->input->post('hidden_fdds_quantity');
// 				if($quant_array != ''){
// 					if($quant_array == '&&'){
// 						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>0));
// 						$this->db->delete('fdd_pro_quantity');
// 					}else{
// 						$quant_array = substr($quant_array, 0, -2);
// 						$quant_arr = explode('**', $quant_array);
// 						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>0));
// 						$this->db->delete('fdd_pro_quantity');
// 						foreach ($quant_arr as $quant_ar){
// 							$quant_ar_ar = explode('#', $quant_ar);

// 							$semi_pro_id = 0;
// 							if($quant_ar_ar[5] != NULL){
// 								$semi_pro_id = $quant_ar_ar[5];
// 							}

// 							$insrt_quant_array = array(
// 									'obs_pro_id'=>$val['id'],
// 									'fdd_pro_id'=>$quant_ar_ar[0],
// 									'quantity'=>$quant_ar_ar[1],
// 									'unit'=>$quant_ar_ar[4],
// 									'semi_product_id'=>$semi_pro_id
// 							);
// 							$this->db->insert('fdd_pro_quantity',$insrt_quant_array);
// 						}
// 					}
// 				}

// 				$obs_quant_array =$this->input->post('hidden_own_pro_quantity');
// 				if($obs_quant_array != ''){
// 					if($obs_quant_array == '&&'){
// 						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>1));
// 						$this->db->delete('fdd_pro_quantity');

// 						$this->delete_notcontaining_products();
// 					}else{
// 						$obs_quant_array = substr($obs_quant_array, 0, -2);
// 						$quant_arr = explode('**', $obs_quant_array);

// 						$this->db->where(array('obs_pro_id'=>$val['id'],'is_obs_product'=>1));
// 						$this->db->delete('fdd_pro_quantity');

// 						$this->delete_notcontaining_products();

// 						foreach ($quant_arr as $quant_ar){
// 							$quant_ar_ar = explode('#', $quant_ar);

// 							$semi_pro_id = 0;
// 							if($quant_ar_ar[5] != NULL){
// 								$semi_pro_id = $quant_ar_ar[5];
// 							}

// 							$insrt_quant_array = array(
// 									'obs_pro_id'=>$val['id'],
// 									'fdd_pro_id'=>$quant_ar_ar[0],
// 									'quantity'=>$quant_ar_ar[1],
// 									'unit'=>$quant_ar_ar[4],
// 									'is_obs_product'=>1,
// 									'semi_product_id'=>$semi_pro_id
// 							);
// 							$this->db->insert('fdd_pro_quantity',$insrt_quant_array);

// 							if($semi_pro_id == 0){
// 								/*$info = $this->db->get_where('products_pending',array('product_id'=>$quant_ar_ar[0],'company_id'=>$val['company_id']))->result_array();
// 									if(empty($info)){
// 								$this->db->insert('products_pending', array('product_id' => $quant_ar_ar[0], 'company_id' => $val['company_id'], 'date' => date('Y-m-d h:i:s')));
// 								}

// 								$con_info = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$quant_ar_ar[0]))->result_array();
// 								if(empty($con_info)){
// 								$this->db->insert('contacted_via_mail', array('obs_pro_id' => $quant_ar_ar[0]));
// 								}*/
// 							}
// 						}
// 					}
// 				}

// 				/*
// 				 *updating product
// 				*/
// 				$update_product_data = array(
// 						'recipe_method' => $this->input->post('recipe_method_txt')
// 				);

// 				if($this->input->post('is_custom_semi') == 1 ){
// 					$update_product_data['semi_product'] = 1;
// 				}
// 				else{
// 					$update_product_data['semi_product'] = 0;
// 					$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$val['id']))->result_array();
// 					foreach ($semi_info as $semi){
// 						$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 						$this->db->delete('products_ingredients_vetten',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 						$this->db->delete('products_additives',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 						$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 						$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 						$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
// 					}
// 					$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$val['id']));
// 				}
// 				// If company not keurslager associate then add normal ingredients
// 				If(!($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' ||$this->session->userdata('menu_type') == 'fdd_premium')){
// 					$update_product_data['ingredients'] = $this->input->post('ingredients');
// 					$update_product_data['allergence'] = $this->input->post('allergence');
// 					$update_product_data['traces_of'] = $this->input->post('traces_of');
// 				}

// 				if($this->input->post('product_type') != NULL && $this->input->post('product_type') != ''){
// 					//$update_product_data['direct_kcp'] = $this->input->post('product_type');
// 				}

// 				$update_product_data['recipe_weight'] = $this->input->post('recipe_weight');

// 				$this->db->where('id',$val['id']);
// 				$this->db->update('products', $update_product_data);

// 				if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' ){

// 					/**
// 					 * Updating Product Ingredients
// 					 */
// 					$this->db->delete('products_ingredients',array('product_id' => $val['id']));
// 					$this->db->delete('products_ingredients_vetten',array('product_id' => $val['id']));
// 					$this->db->delete('products_additives',array('product_id' => $val['id']));

// 					// Adding Ingredients
// 					$this->adding_ingredients($val['id']);

// 					/**
// 					 * Updating Product Traces
// 					*/
// 					$this->db->delete('products_traces',array('product_id' => $val['id']));

// 					// Adding Traces
// 					$this->adding_traces($val['id']);

// 					/**
// 					 * Updating Product Allergence
// 					*/
// 					$this->db->delete('products_allergence',array('product_id' => $val['id']));
// 					$this->db->delete('product_sub_allergence',array('product_id' => $val['id']));

// 					// Adding Allergence
// 					$this->adding_allergence($val['id']);
// 				}

// 				if($this->input->post('is_custom_semi') == 1){
// 					$this->update_semi_product_quant($custom_pro_total_wt_arr);
// 					$this->update_kp_display_order( $val['id']);
// 				}
// 			}
// 		}
// 	}

// 	/**
// 	 * This function deletes those products from table products_pending which are no more contained within products of this company
// 	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
// 	 */
// 	function delete_notcontaining_products(){
// 		$this->db->select('fdd_pro_quantity.fdd_pro_id');
// 		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
// 		$this->db->join('products_pending','products_pending.product_id = fdd_pro_quantity.fdd_pro_id');
// 		$this->db->where(array('products.company_id' => $this->company_id, 'fdd_pro_quantity.is_obs_product'=>1));
// 		$pending_left = $this->db->get('fdd_pro_quantity')->result_array();

// 		$this->db->select(array('product_id','company_id'));
// 		$pending_arr = $this->db->get_where('products_pending',array('company_id' => $this->company_id))->result_array();

// 		foreach ($pending_arr as $val){
// 			if(!$this->in_array_r($val['product_id'], $pending_left)){
// 				$this->db->delete('products_pending', array('product_id' => $val['product_id'],'company_id'=>$this->company_id));
// 			}
// 		}
// 	}

// 	/**
// 	 * Function to add Ingredients
// 	 * @param number $new_product_id
// 	 */
// 	function adding_ingredients($new_product_id){
// 		// Adding ingredients
// 		$all_ings = $this->input->post('ingredients');
// 		if($all_ings != ''){
// 			//echo $all_ings."<br />";
// 			// something,#test product 1#0#9,lp#1,23#ingredient 3#3#9,13%#ingredient 4rrr#4#9,rp#1,#test product 3#0#11,lp#2,13%#ingredient 1#1#11,54%#ingredient 4#4#11,rp#2
// 			$ingredients = explode(':::',$all_ings);
// 			if(!empty($ingredients)){
// 				$enbr_setting = $this->get_enbr_status($this->company_id);

// 				$kp_array = array();
// 				$pro_order = 0;
// 				$disp_order = 1;
// 				$check_repeat = array();
// 				foreach ($ingredients as $key => $ingredient){

// 					$ing_arr = explode('#',$ingredient);

// 					$insert_array = array();
// 					if(!empty($ing_arr) && count($ing_arr) == 5){

// 						if(in_array($ing_arr[3], $kp_array)){
// 							$pro_order = array_search($ing_arr[3], $kp_array);
// 						}else{
// 							$kp_array[] = $ing_arr[3];
// 							$pro_order = array_search($ing_arr[3], $kp_array);
// 							$disp_order = 1;
// 						}

// 						$display_name = ( (isset($ing_arr[1]))?addslashes(stripslashes($ing_arr[1])):'' );
// 						$ki_id = $ing_arr[2];
// 						$display_name1 = '';
// 						$enbr_rel_ki_id = 0;

// 						if($ing_arr[2] != 0){
// 							if(($ing_arr[4] == 3) && ($ing_arr[3] != 0)){
// 								$enbr_result = $this->get_e_current_nbr($ing_arr[2],$enbr_setting['enbr_status']);
// 								if(!empty($enbr_result)){
// 									$ki_id = $enbr_result['ki_id'];
// 									$display_name = $enbr_result['ki_name'];
// 									$display_result = $this->get_display_name($enbr_result['ki_id']);
// 									if($display_result != ''){
// 										$display_name = $display_result;
// 									}

// 									$enbr_rel_ki_id = $enbr_result['enbr_rel_ki_id'];
// 									$display_name1 = $enbr_result['enbr_rel_ki_name'];
// 									$display_result = $this->get_display_name($enbr_result['enbr_rel_ki_id']);
// 									if($display_result != ''){
// 										$display_name1 = $display_result;
// 									}
// 								}
// 								else{
// 									$display_result = $this->get_display_name($ing_arr[2]);
// 									if($display_result != ''){
// 										$display_name = $display_result;
// 									}
// 								}
// 							}
// 							else{
// 								$display_result = $this->get_display_name($ing_arr[2]);
// 								if($display_result != ''){
// 									$display_name = $display_result;
// 								}
// 							}
// 						}

// 						if($ing_arr[4] == 2){
// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => ( (is_numeric($ing_arr[3]))?$ing_arr[3]:0 ),
// 									'ki_id' => $ing_arr[2],
// 									'ki_name' => $display_name,
// 									'display_order' => $disp_order,
// 									'kp_display_order'=> $pro_order+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);

// 							$this->db->insert('products_ingredients_vetten', $insert_array);
// 							$disp_order++;
// 						}
// 						elseif ($ing_arr[4] == 3){
// 							if($ing_arr[3] != 0){
// 								if($display_name != '')
// 									$ing_name = $this->get_ing_name_mod(stripslashes($display_name));
// 								else
// 									$ing_name = $display_name;

// 								if(!in_array($ing_arr[3].'#'.$ki_id,$check_repeat)){
// 									$insert_array = array(
// 											'product_id' => $new_product_id,
// 											'kp_id' => ( (is_numeric($ing_arr[3]))?$ing_arr[3]:0 ),
// 											'add_id' => $ing_arr[0],
// 											'ki_id' => $ki_id,
// 											'ki_name' => $ing_name,
// 											'enbr_rel_ki_id' => $enbr_rel_ki_id,
// 											'enbr_rel_ki_name' => $display_name1,
// 											'display_order' => $disp_order,
// 											'kp_display_order'=> $pro_order+1,
// 											'date_added' => date('Y-m-d H:i:s')
// 									);

// 									$this->db->insert('products_additives', $insert_array);
// 									$disp_order++;

// 									$check_repeat[] = $ing_arr[3].'#'.$ki_id;
// 								}
// 							}
// 						}
// 						else{
// 							if($display_name != '')
// 								$ing_name = $this->get_ing_name_mod(stripslashes($display_name));
// 							else
// 								$ing_name = $display_name;
// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => ( (is_numeric($ing_arr[3]))?$ing_arr[3]:0 ),
// 									'ki_id' => $ing_arr[2],
// 									'prefix' => ( (isset($ing_arr[0]))?addslashes(stripslashes($ing_arr[0])):'' ),
// 									'ki_name' => $ing_name,
// 									'is_obs_ing' => $ing_arr[4],
// 									'display_order' => $disp_order,
// 									'kp_display_order'=> $pro_order+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);

// 							$this->db->insert('products_ingredients', $insert_array);

// 							if($ing_arr[3] != 0 && $ing_arr[2] == 0 && $ing_arr[1] != '(' && $ing_arr[1] != ')'){
// 								$this->db->where(array('obs_pro_id'=>$new_product_id,'fdd_pro_id'=>$ing_arr[3],'is_obs_product'=>$ing_arr[4]));
// 								$this->db->update('fdd_pro_quantity',array('product_prefix'=>$ing_arr[0]));
// 							}
// 							$disp_order++;
// 						}
// 					}
// 				}
// 				$this->update_have_all_id($new_product_id);
// 			}
// 		}
// 	}

// 	function adding_traces($new_product_id){

// 		$all_traces = $this->input->post('traces_of');
// 		if($all_traces != ''){
// 			$traces = explode(':::',$all_traces);
// 			if(!empty($traces)){
// 				foreach ($traces as $key => $trace){

// 					$traces_arr = explode('#',$trace);

// 					$insert_array = array();
// 					if(!empty($traces_arr) && count($traces_arr) == 4){
// 						$insert_array = array(
// 								'product_id' => $new_product_id,
// 								'kp_id' => ( (is_numeric($traces_arr[3]))?$traces_arr[3]:0 ),
// 								'kt_id' => $traces_arr[2],
// 								'prefix' => ( (isset($traces_arr[0]))?addslashes($traces_arr[0]):'' ),
// 								'kt_name' => ( (isset($traces_arr[1]))?addslashes($traces_arr[1]):'' ),
// 								'display_order' => $key+1,
// 								'date_added' => date('Y-m-d H:i:s')
// 						);
// 					}else{

// 						if(strpos($trace,'lp#') !== FALSE && strpos($trace,'lp#') == 0){
// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => 0,
// 									'kt_id' => 0,
// 									'prefix' => '',
// 									'kt_name' => '(',
// 									'display_order' => $key+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);
// 						}elseif( strpos($trace,'rp#') !== FALSE && strpos($trace,'rp#') == 0){

// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => 0,
// 									'kt_id' => 0,
// 									'prefix' => '',
// 									'kt_name' => ')',
// 									'display_order' => $key+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);
// 						}else{
// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => 0,
// 									'kt_id' => 0,
// 									'prefix' => '',
// 									'kt_name' => addslashes($trace),
// 									'display_order' => $key+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);
// 						}
// 					}
// 					$this->db->insert('products_traces', $insert_array);
// 				}
// 			}
// 		}
// 	}

// 	function adding_allergence($new_product_id){

// 		$all_allg = $this->input->post('allergence');
// 		if($all_allg != ''){
// 			$check_repeat = array();
// 			$allergences = explode(':::',$all_allg);
// 			if(!empty($allergences)){
// 				foreach ($allergences as $key => $allergence){

// 					$allg_arr = explode('#',$allergence);

// 					$insert_array = array();
// 					if(!empty($allg_arr) && count($allg_arr) == 5){
// 						if($allg_arr[4] != 0){
// 							if($allg_arr[2] != 0){
// 								$insert_array = array(
// 										'product_id' => $new_product_id,
// 										'kp_id' => ( (is_numeric($allg_arr[3]))?$allg_arr[3]:0 ),
// 										'parent_ka_id' => $allg_arr[4],
// 										'sub_ka_id' => $allg_arr[2],
// 										'sub_ka_name' => ( (isset($allg_arr[1]))?addslashes($allg_arr[1]):'' ),
// 										'display_order' => $key+1,
// 										'date_added' => date('Y-m-d H:i:s')
// 								);
// 								$this->db->insert('product_sub_allergence', $insert_array);
// 							}
// 						}
// 						else{
// 							if($allg_arr[3] != 0){
// 								$insert_array = array(
// 										'product_id' => $new_product_id,
// 										'kp_id' => ( (is_numeric($allg_arr[3]))?$allg_arr[3]:0 ),
// 										'ka_id' => $allg_arr[2],
// 										'prefix' => ( (isset($allg_arr[0]))?addslashes($allg_arr[0]):'' ),
// 										'ka_name' => ( (isset($allg_arr[1]))?addslashes($allg_arr[1]):'' ),
// 										'display_order' => $key+1,
// 										'date_added' => date('Y-m-d H:i:s'),
// 										'by_ingredient'=>0
// 								);
// 								$this->db->insert('products_allergence', $insert_array);
// 							}
// 						}
// 					}else{
// 						if(strpos($allergence,'lp#') !== FALSE && strpos($allergence,'lp#') == 0){
// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => 0,
// 									'ka_id' => 0,
// 									'prefix' => '',
// 									'ka_name' => '(',
// 									'display_order' => $key+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);
// 						}elseif( strpos($allergence,'rp#') !== FALSE && strpos($allergence,'rp#') == 0){

// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => 0,
// 									'ka_id' => 0,
// 									'prefix' => '',
// 									'ka_name' => ')',
// 									'display_order' => $key+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);
// 						}else{
// 							$insert_array = array(
// 									'product_id' => $new_product_id,
// 									'kp_id' => 0,
// 									'ka_id' => 0,
// 									'prefix' => '',
// 									'ka_name' => addslashes($allergence),
// 									'display_order' => $key+1,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);
// 						}
// 						$this->db->insert('products_allergence', $insert_array);
// 					}
// 				}
// 			}
// 		}
// 	}

// 	function update_semi_product_quant($custom_pro_total_wt_total_array = array()){
// 		$prod_id = $this->input->post('prod_id');

// 		$this->db->select_sum('quantity');
// 		$this->db->where('obs_pro_id',$prod_id);
// 		$semi_total = $this->db->get('fdd_pro_quantity')->result_array();
// 		$semi_pro_total = 100;
// 		if(!empty($semi_total)){
// 			$semi_pro_total = $semi_total[0]['quantity'];
// 		}

// 		$custom_pro_total_wt_arr=$custom_pro_total_wt_total_array['custom_pro_total_wt_arr'];
// 		$custom_pro_total_wt_arr_cust=$custom_pro_total_wt_total_array['custom_pro_total_wt_arr_cust'];

// 		if(!empty($custom_pro_total_wt_arr)){
// 			foreach ($custom_pro_total_wt_arr as $total_arr){
// 				$this->db->where(array('obs_pro_id'=>$total_arr['obs_id'],'semi_product_id'=>$prod_id));
// 				$results = $this->db->get('fdd_pro_quantity')->result_array();
// 				if(!empty($results)){
// 					foreach ($results as $result){

// 						$this->db->select('quantity');
// 						$this->db->where(array('fdd_pro_id'=>$result['fdd_pro_id'],'obs_pro_id'=>$prod_id));
// 						$wt = $this->db->get('fdd_pro_quantity')->result_array();
// 						if(!empty($wt)){
// 							$pro_wt = $wt[0]['quantity'];
// 							$calculated_weight = $this->number2db(($total_arr['total']/$semi_pro_total)*$pro_wt);

// 							$this->db->where(array('fdd_pro_id'=>$result['fdd_pro_id'],'obs_pro_id'=>$total_arr['obs_id'],'semi_product_id'=>$prod_id));
// 							$this->db->update('fdd_pro_quantity',array('quantity'=>$calculated_weight));
// 						}
// 					}
// 				}
// 				$this->db->select_sum('quantity');
// 				$this->db->where('obs_pro_id',$total_arr['obs_id']);
// 				$semi_total = $this->db->get('fdd_pro_quantity')->result_array();
// 				$semi_pro_total_cup = 100;
// 				if(!empty($semi_total)){
// 					$semi_pro_total_cup = $semi_total[0]['quantity'];
// 				}

// 				if(!empty($custom_pro_total_wt_arr_cust)){
// 					foreach ($custom_pro_total_wt_arr_cust as $total_arr_cust){
// 						$this->db->where(array('obs_pro_id'=>$total_arr_cust['obs_id'],'semi_product_id'=>$total_arr['obs_id']));
// 						$results_cup = $this->db->get('fdd_pro_quantity')->result_array();
// 						if(!empty($results_cup)){
// 							foreach ($results_cup as $result){
// 								$this->db->select('quantity');
// 								$this->db->where(array('fdd_pro_id'=>$result['fdd_pro_id'],'obs_pro_id'=>$total_arr['obs_id']));
// 								$wt = $this->db->get('fdd_pro_quantity')->result_array();
// 								if(!empty($wt)){
// 									$pro_wt = $wt[0]['quantity'];
// 									$calculated_weight = $this->number2db(($total_arr_cust['total']/$semi_pro_total_cup)*$pro_wt);
// 									$this->db->where(array('fdd_pro_id'=>$result['fdd_pro_id'],'obs_pro_id'=>$total_arr_cust['obs_id'],'semi_product_id'=>$total_arr['obs_id']));
// 									$this->db->update('fdd_pro_quantity',array('quantity'=>$calculated_weight));
// 								}
// 							}
// 						}
// 					}
// 				}

// 			}
// 		}
// 	}

// 	private function update_kp_display_order($prod_id = 0){
// 		$this->db->select('obs_pro_id');
// 		$this->db->where('semi_product_id',$prod_id);
// 		$this->db->group_by('obs_pro_id');
// 		$results = $this->db->get('fdd_pro_quantity')->result_array();

// 		if(!empty($results)){
// 			foreach ($results as $result){
// 				$this->db->select('fdd_pro_id');
// 				$this->db->where('obs_pro_id',$result['obs_pro_id']);
// 				$this->db->order_by('quantity','desc');
// 				$result_fdd = $this->db->get('fdd_pro_quantity')->result_array();

// 				if(!empty($result_fdd)){
// 					foreach ($result_fdd as $key=>$res){
// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
// 						$this->db->update('products_ingredients',array('kp_display_order'=>$key+1));

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
// 						$this->db->update('products_ingredients_vetten',array('kp_display_order'=>$key+1));

// 						$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
// 						$this->db->update('products_additives',array('kp_display_order'=>$key+1));
// 					}
// 				}

// 				$this->db->select('obs_pro_id');
// 				$this->db->where('semi_product_id',$result['obs_pro_id']);
// 				$this->db->group_by('obs_pro_id');
// 				$results_cup = $this->db->get('fdd_pro_quantity')->result_array();
// 				if(!empty($results_cup)){
// 					foreach ($results_cup as $result_cup){
// 						$this->db->select('fdd_pro_id');
// 						$this->db->where('obs_pro_id',$result_cup['obs_pro_id']);
// 						$this->db->order_by('quantity','desc');
// 						$result_fdd_cup = $this->db->get('fdd_pro_quantity')->result_array();

// 						if(!empty($result_fdd_cup)){
// 							foreach ($result_fdd_cup as $key=>$res){
// 								$this->db->where(array('product_id'=>$result_cup['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
// 								$this->db->update('products_ingredients',array('kp_display_order'=>$key+1));

// 								$this->db->where(array('product_id'=>$result_cup['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
// 								$this->db->update('products_ingredients_vetten',array('kp_display_order'=>$key+1));

// 								$this->db->where(array('product_id'=>$result_cup['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
// 								$this->db->update('products_additives',array('kp_display_order'=>$key+1));
// 							}
// 						}
// 					}
// 				}
// 			}
// 		}
// 	}

// 	function number2db($value){
// 		$larr = localeconv();
// 		$search = array(
// 				$larr['decimal_point'],
// 				$larr['mon_decimal_point'],
// 				$larr['thousands_sep'],
// 				$larr['mon_thousands_sep'],
// 				$larr['currency_symbol'],
// 				$larr['int_curr_symbol']
// 		);
// 		$replace = array('.', '.', '', '', '', '');

// 		return str_replace($search, $replace, $value);
// 	}

/**
 * This function is used to print all product of company and there category and subcategory
 * 
 *
 */

function get_product_category( ){
	$company_id = '1785';
	$this->db->select( 'products.id ,products.proname,categories.name,subcategories.subname,products.semi_product' );
	$this->db->join('categories', 'categories.id = products.categories_id', 'left');
	$this->db->join('subcategories', 'subcategories.id = products.subcategories_id', 'left');
	$products = $this->db->get_where( 'products', array( 'products.company_id' => $company_id ) )->result_array( );
			//echo $this->db->last_query();

	echo '<pre>';
	foreach ( $products as $key => $product ) {
				//echo  $product[ 'id' ].' ';   			// product id
				// if( $product[ 'proname' ] != ''){ 		// product name
				// 	echo  $product[ 'proname' ].' ';
				// }else{
				// 	echo '--'.' ';
				// }

				// if( $product[ 'name' ] != ''){ 				// product Category
				// 	echo  $product[ 'name' ].' ';
				// }else{
				// 	echo '--'.' ';
				// }

				// if( $product[ 'subname' ] != ''){  		// product SubCategory
				// 	echo  $product[ 'subname' ].' ';
				// }else{
				// 	echo '--'.' ';
				// }
				if( $product[ 'semi_product' ] != ''){  		// product type semi(extrasemi)
					if( $product[ 'semi_product' ] == '0' ){
						echo 'product'.' ';
					}elseif( $product[ 'semi_product' ] == '1' ){
						echo 'semi-product'.' ';
					}if( $product[ 'semi_product' ] == '2' ){
						echo 'extrasemi-product'.' ';
					}
				}else{
					echo '--'.' ';
				}
				echo '<br>';
			}
		}

		function get_product_category2( ){
			error_reporting(E_ALL);
			$company_id = '1785';
			$this->db->select( 'products.id ,products.proname,categories.name,subcategories.subname,products.semi_product' );
			$this->db->join('categories', 'categories.id = products.categories_id', 'left');
			$this->db->join('subcategories', 'subcategories.id = products.subcategories_id', 'left');

			$this->db->where( "( ( products.direct_kcp = 0 AND products.semi_product = 1 ) OR ( products.semi_product = 0 ) )" );
			$this->db->where( array( 'products.categories_id !=' => '0' ,'products.proname !=' => '' ) );

			$products = $this->db->get_where( 'products', array( 'products.company_id' => $company_id ) )->result_array( );
			//echo $this->db->last_query();
			echo sizeof( $products );
			// echo '<pre>';
			echo '<table>';
			foreach ( $products as $key => $product ) {
				echo  '<tr><td>'. $product[ 'id' ].' </td>';   			// product id
				echo  '<td>';
				if( $product[ 'proname' ] != ''){ 		// product name
					echo  $product[ 'proname' ].' ';
				}else{
					echo '--'.' ';
				}
				echo  '</td>';
				echo  '<td>';
				if( $product[ 'name' ] != ''){ 				// product Category
					echo  $product[ 'name' ].' ';
				}else{
					echo '--'.' ';
				}
				echo  '</td>';
				echo  '<td>';
				if( $product[ 'subname' ] != ''){  		// product SubCategory
					echo  $product[ 'subname' ].' ';
				}else{
					echo '--'.' ';
				}
				echo  '</td>';
				echo  '<td>';
				if( $product[ 'semi_product' ] != ''){  		// product type semi(extrasemi)
					if( $product[ 'semi_product' ] == '0' ){
						echo 'product'.' ';
					}elseif( $product[ 'semi_product' ] == '1' ){
						echo 'semi-product'.' ';
					}if( $product[ 'semi_product' ] == '2' ){
						echo 'extrasemi-product'.' ';
					}
				}else{
					echo '--'.' ';
				}
				// echo '<br>';
				echo  '</td>';
				echo  '<td>';
			}
			echo '</table>';
		}

		function bckup_86(){
			$this->db->select('id');
			$query = $this->db->get_where( 'products', array('company_id' => 86))->result_array();
			foreach ($query as $key => $value) {
				echo $value['id'].'<br>';
			}
		}

		function update_prod_table() {
			set_time_limit(0);
			ini_set('memory_limit', -1);

			$this->db->select( 'products.id, products.proname, products.prodescription, language.locale' );
			//$this->db->where( 'products.company_id', 4194 );
			$this->db->join( 'general_settings', 'products.company_id = general_settings.company_id' );
			$this->db->join( 'language', 'language.id = general_settings.language_id' );
			$query = $this->db->get_where( 'products',array('products.company_id' => 4194) )->result_array();

			foreach ( $query as $key => $value ) {
				if( $value[ 'locale' ] == 'en_US' ){
					$insert_array = array( 
						'product_id' 		=> $value[ 'id' ],
						'proname'			=> $value[ 'proname' ],
						'prodescription' 	=> $value[ 'prodescription' ]
					);
				} else if( $value[ 'locale' ] == 'nl_NL' ) {
					$insert_array = array( 
						'product_id' 		=> $value[ 'id' ],
						'proname_dch'		=> $value[ 'proname' ],
						'prodescription_dch' => $value[ 'prodescription' ]
					);

				} else if( $value[ 'locale' ] == 'fr_FR' ) {
					$insert_array = array( 
						'product_id' 		=> $value[ 'id' ],
						'proname_fr'		=> $value[ 'proname' ],
						'prodescription_fr' => $value[ 'prodescription' ]
					);
				}
				$this->db->insert( 'products_name', $insert_array );
			}

			// $this->db->select( 'company_id' );
			// $company_ids = $this->db->get( 'general_settings' )->result_array();
			// $company_ids = array_column( $company_ids, 'company_id' );

			// $this->db->select( 'id, proname, prodescription' ); 
			// $this->db->where( 'company_id !=', 0 ); 
			// $this->db->where_not_in( 'company_id', $company_ids );
			// $query2 = $this->db->get( 'products' )->result_array();

			// foreach ( $query2 as $key => $value) {
			// 	$insert_array = array( 
			// 						'product_id' 		=> $value[ 'id' ],
			// 						'proname_dch'		=> $value[ 'proname' ],
			// 						'prodescription_dch' => $value[ 'prodescription' ]
			// 	 				);
			// 	$this->db->insert( 'products_name', $insert_array );
			// }


		}

		// function tests(){
		// 	$this->db->select('id');
		// 	$this->db->where(array('products.company_id'=>4194));
		// 	$prod = $this->db->get('products')->result_array();
		// 	foreach ($prod as $key => $value) {
		// 		$this->db->where('product_id',$value['id']);
		// 		$this->db->delete('products_name');
		// 	}
		// }

		function update_lang_on_site(){
			$this->db->select( 'company_id, language_id' );
			$comp_lang = $this->db->get( 'general_settings' )->result_array();

			foreach( $comp_lang as $key => $value) {
				$lang_on_site = json_encode( array( $value[ 'language_id' ] ) );
				$this->db->where( 'company_id', $value[ 'company_id' ] );
				$this->db->update( 'general_settings', array( 'lang_on_the_site' => $lang_on_site ) );
			}
		}

		function imp_common_art1(){
			error_reporting(E_ALL);
			ini_set('memory_limit', '512M');
			$this->load->library("Excel");
			$inputFileName = dirname(__FILE__).'/../../assets/excels/eng.xlsx';
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($inputFileName);
			$total_sheets=$objPHPExcel->getSheetCount();
			$allSheetName=$objPHPExcel->getSheetNames();
			$objWorksheet = $objPHPExcel->setActiveSheetIndex(0); 
			$arr_data = array(); 
			$highestRow = 3430;
			$highestColumnIndex= 2;
			for ($row = 1; $row <= $highestRow; ++$row) {
				for ($col = 0; $col <= $highestColumnIndex; ++$col) {
					$value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					if(is_array($arr_data) ) {
						$arr_data[$row-1][$col]=$value;
					}
				}
			}

			$worksheet_arr0 = $arr_data;
			$count_rows = 0;	


			$comp_id = 4616;

			foreach ($worksheet_arr0 as $rows){
				$pro_name_fr 	= trim($rows[0]);
				$pro_name_nl 	= trim($rows[1]);
				$pro_name 		= trim($rows[2]);


				$this->db->where(array('company_id'=> $comp_id));
				$res = $this->db->get_where('products',array('proname'=>addslashes(mb_strtolower($pro_name_fr,'UTF-8'))))->row();
				if(!empty($res)){
					echo $res->id.'---';
					$this->db->where(array('product_id'=> $res->id));
					$this->db->update('products_name',array('proname'=>addslashes(mb_strtolower($pro_name,'UTF-8')),'proname_dch'=>addslashes(mb_strtolower($pro_name_nl,'UTF-8'))  ));
				}
			}
		}

		function delete_fav_prod( ){
			error_reporting( E_ALL );
		// $this->db->select( 'obs_pro_id' );
		// $this->db->where( 'company_id', '4612' );
		// $fav_prod_ids = $this->db->get( 'fdd_pro_fav' )->row_array( );
		// echo $this->db->last_query();
		// echo "<pre>";
		// print_r ($fav_prod_ids);
		// if( !empty( $fav_prod_ids ) ){
		// 	$obs_pro_id = json_decode( $fav_prod_ids[ 'obs_pro_id' ] , true );

		// 	$this->db->where_in('id', $obs_pro_id );
		// 	$this->db->where('company_id', '0' );
		// 	$this->db->delete('products');
		// 	echo $this->db->last_query().'<br>';

		// 	$this->db->where_in('obs_pro_id', $obs_pro_id );
		// 	$this->db->delete('contacted_via_mail');
		// 	echo $this->db->last_query().'<br>';
		// 	echo "end";
		// }

			$this->db->select( 'product_id' );
			$this->db->where('company_id', '4612' );
			$product = $this->db->get('products_pending' )->result_array( );
			$product = array_column( $product, 'product_id');
			print_r (json_encode( $product) );
			echo "</pre>";
		}

		function update_client_password(){
			$this->db->select( 'id, password_c' );
			$passwords = $this->db->get( 'clients1' )->result_array();
// echo "<pre>";
// print_r ($passwords);die;
// echo "</pre>";
			foreach( $passwords as $key => $value) {
				echo $key;
				$this->db->where( 'id', $value[ 'id' ] );
				$this->db->update( 'clientsori', array( 'password_c' => $value['password_c'] ) );
			}
		}

		function update_client_password_from_mail_log(){
			error_reporting(-1);
			ini_set('display_errors', 1);
			$this->db->select( 'id, email_c' );
			$this->db->where( 'password_c', '' );
			$mails = $this->db->get( 'clientsori' )->result_array();
// echo "<pre>";
// 			print_r ($mails);
// 			echo "</pre>";
			$email_pass = array();
			$password1 = array();
			foreach ($mails as $key => $value) {
				$this->db->select( 'message' );
				$this->db->like( 'subject', 'Welkom op de webwinkel van');
				$this->db->where( array( 'email_to' => $value['email_c'] ) );
				$email_pass = $this->db->get( 'email_logs' )->row_array();

				$word = 'Wachtwoord : ';
				$pos = strpos( $email_pass['message'], $word );
				if($pos!==false){
					$password = substr($email_pass['message'], $pos+strlen($word));
					$new_pos = strpos($password, '</strong>');
					$password1[$key]['id'] = $value['id'];
					$password1[$key]['password_c'] = substr($password, 0, $new_pos );
				}
			}
// echo "<pre>";
// print_r ($password1);die;
// echo "</pre>";
			foreach ($password1 as $key1 => $value1) {
				$this->db->where( 'id', $value1[ 'id' ] );
				$this->db->update( 'clientsori', array( 'password_c' => $value1['password_c'] ) );
			}
			die("done");
		}

	// need to be run after fxn to_update_included_semi()	
	// function to_update_esp_to_sp(){
	// 	$this->db->where( array( 'semi_product' => '2', 'company_id' => '1523' ) );
	// 	$this->db->update( 'products', array( 'semi_product' => '1' ) );

	// 	$this->db->where( array( 'direct_kcp' => '0', 'semi_product' => '1', 'company_id' => '1523' ) );
	// 	$custom_semis = $this->db->get( 'products' )->result_array();
	// 	foreach ($custom_semis as $key1 => $value1) {
	// 		$clone_id = $this->add_product_clone( $value1['id'] );
	// 		if( !empty( $clone_id ) ){
	// 			if( isset( $clone_id[ 'id' ] ) ){
	// 				$cloned_id = $clone_id[ 'id' ];
	// 				// move clone to SP
	// 				$this->db->where( array( 'id' => $cloned_id ) );
	// 				$this->db->update( 'products', array( 'categories_id' => 0, 'subcategories_id' => 0, 'direct_kcp' => 1, 'semi_product' => 1 ) );

	// 				// update semi_product_id of original product by SP's id
	// 				$obj = array(
	// 						'semi_product_id' 	=> $cloned_id,
	// 						'included_semi' 	=> $cloned_id
	// 					);
	// 				$this->db->where( array( 'obs_pro_id' => $value1['id'] ) );
	// 				$this->db->update( 'fdd_pro_quantity', $obj );

	// 				// update SP's details
	// 				$obj1 = array( 
	// 						'semi_product_id' 	=> '0',
	// 						'included_semi' 	=> '0'
	// 					);
	// 				$this->db->where( array( 'obs_pro_id' => $cloned_id ) );
	// 				$this->db->update( 'fdd_pro_quantity', $obj1 );
	// 			}
	// 		}
	// 	}
	// }

	// need to be run after fxn add_all_semi_to_custom()	
// 	function to_update_included_semi(){
// 		error_reporting(-1);
// 		ini_set('display_errors', 1);
		
// // 		$this->db->select('id');
// // 		$esp = $this->db->get_where( 'products', array( 'semi_product' => '2', 'company_id' => '1523' ) )->result_array();
// // 		$esp = array_column( $esp, 'id' );
// // // echo "<pre>";
// // // print_r ($esp);die;
// // // echo "</pre>";

// // 		$this->db->select('fdd_pro_quantity.*, products.semi_product');
// // 		$this->db->join( 'products', 'products.id = fdd_pro_quantity.obs_pro_id' );
// // 		$this->db->where( array( 'semi_product' => '1', 'direct_kcp' => '1', 'company_id' => '1523' ) );
// // 		// $this->db->where_in( 'semi_product_id', $esp );
// // 		$sp = $this->db->get( 'fdd_pro_quantity' )->result_array();
// // 		$sp = array_unique(array_column( $sp, 'obs_pro_id' ));
// // 		// echo "<pre>";
// // 		// print_r ($sp);
// // 		// echo count($sp);
// // 		// die("done");

// // 		$this->db->distinct();
// // 		$this->db->select('fdd_pro_quantity.obs_pro_id');
// // 		$this->db->join( 'products', 'products.id = fdd_pro_quantity.obs_pro_id' );
// // 		$this->db->where( array( 'semi_product' => '0', 'direct_kcp' => '0', 'company_id' => '1523' ) );
// // 		$this->db->where_in( 'semi_product_id', $sp );
// // 		$p = array_unique(array_column($this->db->get( 'fdd_pro_quantity' )->result_array(),'obs_pro_id'));
// // 		// echo "<pre>";
// // 		// print_r ($p);
// // 		// echo count($p);
// // 		// die("done");

// // 		foreach ($p as $key => $value) {
// // 			$this->db->order_by( 'quantity', 'desc');
// // 			$prod = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value, 'semi_product_id !=' => '0' ) )->result_array();
// // 			echo "<pre>";
// // 			print_r ($prod);
// // 			echo "</pre>";

// // 			$semi_done = array();
// // 			foreach ($prod as $key1 => $value1) {
// // 				if( !in_array( $value1['semi_product_id'], $semi_done) ){
// // 					array_push( $semi_done, $value1['semi_product_id'] );
// // 					$this->db->order_by( 'quantity', 'desc');
// // 					$semi = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value1['semi_product_id'] ) )->result_array();
// // 					echo "<pre>";
// // 					print_r ($semi);
// // 					echo "</pre>";
// // 					foreach ($semi as $key2 => $value2) {
// // 						$included_semi = ($value2['semi_product_id'] != '0' ) ? $prod[$key2]['semi_product_id'].'-'.$value2['semi_product_id'] : $prod[$key2]['semi_product_id'];
// // 						$this->db->where( 'id', $prod[$key2]['id'] );
// // 						$this->db->update( 'fdd_pro_quantity', array( 'included_semi' => $included_semi ) );
// // 					}
// // 				}
// // 			}
// // 		}
// // 		echo "<pre>";
// // 		print_r ($semi_done);
// // 		echo "</pre>";
// 		die("done");
// 	}

	//first run this
	// function add_all_semi_to_custom(){
	// 	ini_set('memory_limit', '20000M');
	// 	set_time_limit(0);
	// 	ini_set('max_execution_time', 0);

	// 	$this->db->where( array( 'semi_product !=' => '0' ) );
	// 	$prods = $this->db->get( 'products' )->result_array();

	// 	$arr = array();
	// 	foreach ( $prods as $key => $value) {
	// 		$num = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->num_rows();

	// 		$this->db->distinct();
	// 		$this->db->select('obs_pro_id');
	// 		$query = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value['id'] ) )->result_array();

	// 		foreach ($query as $key1 => $value1) {
	// 			$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
	// 			$prods_to_update = $this->db->get( 'fdd_pro_quantity' )->num_rows();

	// 			if( $num != $prods_to_update ){
	// 				$arr[] = array( 'semi_id' => $value['id'], 'obs_pro_id' => $value1['obs_pro_id'], 'company_id' => $value['company_id'] );
	// 			}
	// 		}
	// 	}
	// 	echo "<pre>";
	// 	print_r ($arr);die;
	// 	echo "</pre>";
	// 	echo "<pre>";
	// 	print_r (array_values(array_unique(array_column($arr,'semi_id'))));
	// 	echo "</pre>";
	// 	die;
	// }

		function list_of_clients(){
			ini_set('memory_limit', '20000M');
			set_time_limit(0);
			ini_set('max_execution_time', 0);

			$this->db->order_by('company_id', 'asc');
			$this->db->where( array( 'semi_product !=' => '0' ) );
			$prods = $this->db->get( 'products' )->result_array();

			$arr = array();
			$fdd_ids = array();
			$fdd_ids1 = array();
			foreach ( $prods as $key => $value) {
				$num = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->num_rows();

				$this->db->distinct();
				$this->db->select('obs_pro_id');
				$query = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value['id'] ) )->result_array();

				foreach ($query as $key1 => $value1) {
					$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
					$prods_to_update = $this->db->get( 'fdd_pro_quantity' )->num_rows();

					if( $num != $prods_to_update ){
					// $this->db->select('fdd_pro_id');
					// $num_fdd_ids = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->result_array();

					// $this->db->select('fdd_pro_id');
					// $this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
					// $prods_to_update_fdd_ids = $this->db->get( 'fdd_pro_quantity' )->result_array();

						$this->db->select_sum( 'quantity' );
						$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
						$quantity = $this->db->get( 'fdd_pro_quantity' )->result_array();
						$quantity = $quantity[0]['quantity'];
					// if( !empty( $num_fdd_ids ) && !empty( $prods_to_update_fdd_ids ) ){
					// 	$num_fdd_ids = $num_fdd_ids1 = array_column( $num_fdd_ids, 'fdd_pro_id');
					// 	$prods_to_update_fdd_ids = $prods_to_update_fdd_ids1 = array_column( $prods_to_update_fdd_ids, 'fdd_pro_id');

					// 	$fdd_ids = array_filter($num_fdd_ids, 
					// 	  function ($val) use (&$prods_to_update_fdd_ids) { 
					// 	    $key = array_search($val, $prods_to_update_fdd_ids);
					// 	    if ( $key === false ) return true;
					// 	    unset($prods_to_update_fdd_ids[$key]);
					// 	    return false;
					// 	  }
					// 	);

					// 	$fdd_ids1 = array_filter($prods_to_update_fdd_ids1, 
					// 	  function ($val) use (&$num_fdd_ids1) { 
					// 	    $key = array_search($val, $num_fdd_ids1);
					// 	    if ( $key === false ) return true;
					// 	    unset($num_fdd_ids1[$key]);
					// 	    return false;
					// 	  }
					// 	);
					// }

						$arr[] = array( 'semi_id' => $value['id'], 'obs_pro_id' => $value1['obs_pro_id'], 'company_id' => $value['company_id'], 'fdd_ids' => $fdd_ids, 'fdd_ids1' => $fdd_ids1, 'quantity'=>$quantity );
					}
				}
			}

		// $this->fdb = $this->load->database('fdb',TRUE);
			foreach ($arr as $key => $value) {
			// $this->db->select('products.id, proname, company_name, products.company_id, email, phone, general_settings.language_id');
			// $this->db->join( 'company', 'company.id = products.company_id' );
			// $this->db->join( 'general_settings', 'general_settings.company_id = company.id' );
			// $is_same = $this->db->get_where('products', array( 'products.company_id' => $value['company_id'], 'products.id' => $value['obs_pro_id'] ) )->row_array();

			// if( $is_same['language_id'] == 1 ){
			// 	$this->fdb->select( 'p_name' );
			// }
			// if( $is_same['language_id'] == 2 ){
			// 	$this->fdb->select( 'p_name_dch as p_name' );
			// }
			// if( $is_same['language_id'] == 3 ){
			// 	$this->fdb->select( 'p_name_fr as p_name' );
			// }
			// if( !empty( $value['fdd_ids'] ) ){
			// 	$this->fdb->where_in( 'p_id', $value[ 'fdd_ids' ] );
			// }
			// else{
			// 	$this->fdb->where_in( 'p_id', $value[ 'fdd_ids1' ] );
			// }
			// $fdd_names = $this->fdb->get( 'products')->result_array();

			// $fdd_names = implode( ', ', array_column( $fdd_names, 'p_name' ) );
				if( $value['quantity'] != '' ){
					echo str_replace( ',', '.', round( $value['quantity'], 2 ) ).'<br>';
				}
				else{
					echo 'NA'.$value['obs_pro_id'].'--'.$value['company_id'].'<br>';
				}
			}
			die("kp");
		}

		function live_sp_in_sp(){
			error_reporting(-1);
			ini_set('display_errors', 1);

			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '1', 'direct_kcp' => '1' ))->result_array(),'obs_pro_id');
		// echo "<pre>";
		// print_r ($query);die;
		// echo "</pre>";

			$this->db->distinct();
			$this->db->select('semi_product_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$this->db->where_in( 'obs_pro_id', $query);
			$res = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0' ))->result_array(),'semi_product_id');
		// echo "<pre>";
		// print_r ($res);die;
		// echo "</pre>";

			$this->db->select('products.id');
			$this->db->where(array( 'semi_product'=> '1', 'direct_kcp' => '1'));
			$this->db->where_in( 'id', $res);
			$res31 = array_column($this->db->get('products')->result_array(),'id');
		// echo "<pre>";
		// print_r ($res31);die;
		// echo "</pre>";

			if( !empty($res31) ){
				foreach ($res31 as $key => $value) {
					$this->db->distinct();
					$this->db->select('semi_product_id');
					$semis = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id !=' => '0', 'obs_pro_id' => $value ) )->result_array();
					if( !empty( $semis ) ){
						foreach ($semis as $k1 => $v1) {
							$esp = $this->db->get_where('products', array( 'id' => $v1['semi_product_id'], 'semi_product' => '2', 'direct_kcp' => '1' ))->row_array();
							if( !empty($esp) ){
								$this->db->distinct();
								$this->db->select('obs_pro_id');
								$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
								if( !empty($obs_ids) ){
									foreach ($obs_ids as $k => $v) {
										$this->db->select('direct_kcp, semi_product');
										$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
										if( !empty( $obs_type ) ){
											if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
												$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
												if( empty($is_semi) ){
													$this->db->where('id', $v['obs_pro_id']);
													$this->db->update('products', array( 'semi_product' => '0' ));
												}
											}
											elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
												$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
												if( empty($is_semi) ){
													$this->db->where('id', $v['obs_pro_id']);
													$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
												}
												else{
													$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
													$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
												}
											}
										}
									}
								}
							}
						}
					}
					else{
						$this->db->distinct();
						$this->db->select('obs_pro_id');
						$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
						if( !empty($obs_ids) ){
							foreach ($obs_ids as $k => $v) {
								$this->db->select('direct_kcp, semi_product');
								$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
								if( !empty( $obs_type ) ){
									if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
										$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
										if( empty($is_semi) ){
											$this->db->where('id', $v['obs_pro_id']);
											$this->db->update('products', array( 'semi_product' => '0' ));
										}
									}
									elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
										$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
										if( empty($is_semi) ){
											$this->db->where('id', $v['obs_pro_id']);
											$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
										}
										else{
											$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
											$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
										}
									}
								}
							}
						}
					}
				}
			}
			die("done");
		}

		function live_sp_in_sp1(){
			error_reporting(-1);
			ini_set('display_errors', 1);

			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '1', 'direct_kcp' => '1' ))->result_array(),'obs_pro_id');
		// echo "<pre>";
		// print_r ($query);die;
		// echo "</pre>";

			$this->db->distinct();
			$this->db->select('semi_product_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$this->db->where_in( 'obs_pro_id', $query);
			$res = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0' ))->result_array(),'semi_product_id');
		// echo "<pre>";
		// print_r ($res);die;
		// echo "</pre>";

			$this->db->select('products.id');
			$this->db->where(array( 'semi_product'=> '1', 'direct_kcp' => '0'));
			$this->db->where_in( 'id', $res);
			$res31 = array_column($this->db->get('products')->result_array(),'id');
		// echo "<pre>";
		// print_r ($res31);die;
		// echo "</pre>";

			if( !empty($res31) ){
				foreach ($res31 as $key => $value) {
					$this->db->distinct();
					$this->db->select('semi_product_id');
					$semis = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id !=' => '0', 'obs_pro_id' => $value ) )->result_array();
					if( !empty( $semis ) ){
					// foreach ($semis as $k1 => $v1) {
					// 	$esp = $this->db->get_where('products', array( 'id' => $v1['semi_product_id'], 'semi_product' => '2', 'direct_kcp' => '1' ))->row_array();
					// 	if( !empty($esp) ){
					// 		$this->db->distinct();
					// 		$this->db->select('obs_pro_id');
					// 		$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
					// 		if( !empty($obs_ids) ){
					// 			foreach ($obs_ids as $k => $v) {
					// 				$this->db->select('direct_kcp, semi_product');
					// 				$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
					// 				if( !empty( $obs_type ) ){
					// 					if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
					// 						$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
					// 						if( empty($is_semi) ){
					// 							$this->db->where('id', $v['obs_pro_id']);
					// 							$this->db->update('products', array( 'semi_product' => '0' ));
					// 						}
					// 					}
					// 					elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
					// 						$is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
					// 						if( empty($is_semi) ){
					// 							$this->db->where('id', $v['obs_pro_id']);
					// 							$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
					// 						}
					// 						else{
					// 							$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
					// 							$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }
					}
					else{
						$this->db->distinct();
						$this->db->select('obs_pro_id');
						$obs_ids = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value ))->result_array();
						if( !empty($obs_ids) ){
							foreach ($obs_ids as $k => $v) {
								$this->db->select('direct_kcp, semi_product');
								$obs_type = $this->db->get_where('products', array('id' => $v['obs_pro_id']))->row_array();
								if( !empty( $obs_type ) ){
									if( $obs_type['direct_kcp'] == '0' && $obs_type['semi_product'] == '1' ){
									// $is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
									// if( empty($is_semi) ){
									// 	$this->db->where('id', $v['obs_pro_id']);
									// 	$this->db->update('products', array( 'semi_product' => '0' ));
									// }
									}
									elseif( $obs_type['direct_kcp'] == '1' && $obs_type['semi_product'] == '1' ){
									// $is_semi = $this->db->get_where('fdd_pro_quantity', array( 'obs_pro_id' => $v['obs_pro_id'], 'semi_product_id !=' => '0' ))->result_array();
									// if( empty($is_semi) ){
									// 	$this->db->where('id', $v['obs_pro_id']);
									// 	$this->db->update('products', array( 'semi_product' => '0', 'direct_kcp' => '0' ));
									// }
									// else{
										$this->db->where(array('obs_pro_id' => $v['obs_pro_id'], 'semi_product_id' => $value )  );
										$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
									// }
									}
								}
							}
						}
					}
				}
			}
			die("done");
		}

		function live_sp_in_sp2(){
			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '1', 'direct_kcp' => '0' ))->result_array(),'obs_pro_id');
		// echo "<pre>";
		// print_r ($query);die;
		// echo "</pre>";

			$this->db->where_in( 'obs_pro_id', $query );
			$this->db->where( 'semi_product_id !=', '0' );
			$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => '0' ));
			die('done');
		}

		function live_sp_in_sp3(){
			error_reporting(-1);
			ini_set('display_errors', 1);

			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$query = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0', 'semi_product' => '0', 'direct_kcp' => '0' ))->result_array(),'obs_pro_id');
		// echo "<pre>";
		// print_r ($query);die;
		// echo "</pre>";

			$this->db->distinct();
			$this->db->select('semi_product_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$this->db->group_start();
			$query = array_chunk( $query, 500);
			foreach ($query as $key => $value){
				$this->db->or_where_in( 'obs_pro_id', $value );	
			}
			$this->db->group_end();
			$res = array_column($this->db->get_where('fdd_pro_quantity', array('semi_product_id !=' => '0' ))->result_array(),'semi_product_id');
		// echo "<pre>";
		// print_r ($res);die;
		// echo "</pre>";

			$this->db->select('products.id');
			$this->db->where(array( 'semi_product'=> '2', 'direct_kcp' => '1'));
			$this->db->group_start();
			$res = array_chunk( $res, 500);
			foreach ($res as $key1 => $value1){
				$this->db->or_where_in( 'id', $value1 );	
			}
			$this->db->group_end();
			$res31 = array_column($this->db->get('products')->result_array(),'id');
		// echo "<pre>";
		// print_r ($res31);die;
		// echo "</pre>";

			if( !empty($res31) ){
				foreach ($res31 as $key => $value) {
					$this->db->distinct();
					$this->db->select('obs_pro_id');
					$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
					$semis = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value, 'semi_product' => '1', 'direct_kcp' => '1' ) )->result_array();
					if( !empty( $semis ) ){
						foreach ($semis as $k1 => $v1) {
							$this->db->where('obs_pro_id', $v1['obs_pro_id']);
							$this->db->where('semi_product_id', $value);
							$this->db->update('fdd_pro_quantity', array('semi_product_id'=>'0'));
						}
					}
				}
				$this->db->where_in('id', $res31);
				$this->db->update('products', array('semi_product' => '1'));
			}
			die("done");
		}

		function add_all_semi_to_custom(){
			ini_set('memory_limit', '20000M');
			set_time_limit(0);
			ini_set('max_execution_time', 0);

			$this->db->where( array( 'semi_product !=' => '0' ) );
			$this->db->limit( 1000, 12000 );
			$prods = $this->db->get( 'products' )->result_array();
			echo "<pre>";
			print_r ($prods);die("lp");
			echo "</pre>";
			$arr = array();
			foreach ( $prods as $key => $value) {
				echo $key;
				$num = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value['id'] ) )->num_rows();

				$this->db->distinct();
				$this->db->select('obs_pro_id');
				$query = $this->db->get_where('fdd_pro_quantity', array( 'semi_product_id' => $value['id'] ) )->result_array();

				foreach ($query as $key1 => $value1) {
					$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value['id'] ) );
					$prods_to_update = $this->db->get( 'fdd_pro_quantity' )->num_rows();

					if( $num != $prods_to_update ){
						$arr[] = array( 'semi_id' => $value['id'], 'obs_pro_id' => $value1['obs_pro_id'], 'company_id' => $value['company_id'] );
					}
				}
			}

			foreach ($arr as $key1 => $value1) {
				echo $key1;
				$this->db->where( array( 'obs_pro_id' => $value1['obs_pro_id'], 'semi_product_id' => $value1['semi_id'] ) );
				$this->db->update( 'fdd_pro_quantity', array( 'semi_product_id' => '0' ) );
			}
			die("done");
		}

		function to_update_included_semi(){
			$this->db->select('id');
			$esp = $this->db->get_where( 'products', array( 'semi_product' => '2') )->result_array();
			$esp = array_column( $esp, 'id' );

			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$this->db->where('semi_product', '1');
			$this->db->where_in( 'semi_product_id', $esp );
			$sp_having_esp = $this->db->get( 'fdd_pro_quantity' )->result_array();
		// echo "<pre>";
		// print_r ($sp_having_esp);die;
		// echo "</pre>";

			foreach ($sp_having_esp as $k1 => $v1) {
				$this->db->where('obs_pro_id', $v1['obs_pro_id']);
				$this->db->where_in('semi_product_id', $esp);
				$a = $this->db->get('fdd_pro_quantity')->result_array();

				foreach ($a as $k2 => $v2) {
					echo $k2;
					$this->db->where('obs_pro_id', $v2['obs_pro_id']);
					$this->db->where('semi_product_id', $v2['semi_product_id']);
					$this->db->update('fdd_pro_quantity', array( 'included_semi' => $v2['semi_product_id'] ));
				}
			} die("done");
		}

		function to_update_included_semi1(){
			$this->db->distinct();
			$this->db->select('obs_pro_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$this->db->where( 'semi_product_id !=', '0' );
			$this->db->limit( 2000, 16000 );
			$custom = $this->db->get_where( 'fdd_pro_quantity', array( 'semi_product' => '0' ) )->result_array();
			$custom = array_column( $custom, 'obs_pro_id' );
		// echo "<pre>";
		// print_r ($custom);die;
		// echo "</pre>";

			foreach ($custom as $k1 => $v1) {
				echo $k1;
				$this->db->where('obs_pro_id', $v1);
				$this->db->where( 'semi_product_id !=', '0' );
				$a = $this->db->get('fdd_pro_quantity')->result_array();

				foreach ($a as $k2 => $v2) {
					$this->db->where('obs_pro_id', $v2['semi_product_id']);
					$this->db->where( 'semi_product_id !=', '0' );
					$having_esp = $this->db->get('fdd_pro_quantity')->result_array();

					if( !empty($having_esp) ){
						foreach ($having_esp as $k3 => $v3) {
							$included_semi = $v2['semi_product_id'].'-'.$v3['semi_product_id'];
							$this->db->where('obs_pro_id', $v1);
							$this->db->where('semi_product_id', $v2['semi_product_id']);
							$this->db->update('fdd_pro_quantity', array( 'included_semi' => $included_semi ));
						}
					}
					else{
						$this->db->where('obs_pro_id', $v1);
						$this->db->where('semi_product_id', $v2['semi_product_id']);
						$this->db->update('fdd_pro_quantity', array( 'included_semi' => $v2['semi_product_id'] ) );
					}
				}
			} die("done");
		}

		function to_update_esp_to_sp(){
		// $this->db->where( array( 'semi_product' => '2' ) );
		// $this->db->update( 'products', array( 'semi_product' => '1' ) );
		// die("done");

			$this->db->where( array( 'direct_kcp' => '0', 'semi_product' => '1' ) );
			$this->db->limit( 1000 );
			$custom_semis = $this->db->get( 'products' )->result_array();
			echo "<pre>";
			print_r ($custom_semis);die;
			echo "</pre>";
			foreach ($custom_semis as $key1 => $value1) {
				$clone_id = $this->add_product_clone( $value1['id'] );
				echo $key1;
				if( !empty( $clone_id ) ){
					if( isset( $clone_id[ 'id' ] ) ){
						$cloned_id = $clone_id[ 'id' ];
					// move clone to SP
						$this->db->where( array( 'id' => $cloned_id ) );
						$this->db->update( 'products', array( 'categories_id' => 0, 'subcategories_id' => 0, 'direct_kcp' => 1, 'semi_product' => 1 ) );

					// update semi_product_id of original product by SP's id
						$fdds = $this->db->get_where( 'fdd_pro_quantity', array( 'obs_pro_id' => $value1['id'] ) )->result_array();

						$obj = array();
						if( !empty( $fdds ) ){
							foreach( $fdds as $k1 => $v1 ){
								$obj[] = array(
									'id' 				=> $v1[ 'id' ],
									'semi_product_id' 	=> $cloned_id,
									'included_semi' 	=> ( $v1[ 'included_semi' ] != '0' ) ? $cloned_id.'-'.$v1[ 'included_semi' ] : $cloned_id
								);
							}
							if( !empty( $obj ) ){
								$this->db->update_batch( 'fdd_pro_quantity', $obj, 'id' );
							}
						}

					// to change custom semi to custom
						$this->db->where('id', $value1['id']);
						$this->db->update('products', array( 'semi_product' => '0' ));
					}
				}
			}
			die('done');
		}

		function add_product_clone( $clone_of_id, $from_update_recipe = 0){
		//Fetching Information about the products of which clone is to be add
			$product_info = $this->db->get_where('products',array('id'=>$clone_of_id))->result_array();
			$fdd_pro_ids = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$clone_of_id))->result_array();

			if($product_info){
				$categories_id = $product_info['0']['categories_id'];
				$subcategories_id = $product_info['0']['subcategories_id'];
				$product_infos	= $product_info['0'];
				foreach ($product_infos as $key => $info){
					if($key != 'id')
						$add_product_data[$key]	= $info;
				}
				if( $from_update_recipe != 1 ){
					$add_product_data['proname'] = $add_product_data['proname'];
				}
				if($add_product_data['direct_kcp'] == 0){
					$add_product_data['semi_product'] = 0;
				}

			//Inserting data in products table
				$query=$this->db->insert('products', $add_product_data);
			$new_product_id=$this->db->insert_id();//method to get id of last inserted row//

			if ($new_product_id)
			{
				$this->db->where(array('id'=>$new_product_id));
				$this->db->update('products',array('procreated' => date('Y-m-d'),'proupdated'=>'0000-00-00'));

				$multi_names = $this->db->get_where( 'products_name', array( 'product_id' => $clone_of_id ) )->row_array();
				$multi_obj = array(
					'product_id' 		=> $new_product_id,
					'proname' 			=> ( $multi_names[ 'proname' ] != '' ) ? ($multi_names[ 'proname' ] ) : '',
					'proname_dch' 		=> ( $multi_names[ 'proname_dch' ] != '' ) ? ($multi_names[ 'proname_dch' ] ) : '',
					'proname_fr' 		=> ( $multi_names[ 'proname_fr' ] != '' ) ? ($multi_names[ 'proname_fr' ] ) : '',
					'prodescription' 	=> ( $multi_names[ 'prodescription' ] == NULL ) ? '' :$multi_names[ 'prodescription' ],
					'prodescription_dch'=> ( $multi_names[ 'prodescription_dch' ] == NULL ) ? '' :$multi_names[ 'prodescription_dch' ],
					'prodescription_fr' => ( $multi_names[ 'prodescription_fr' ] == NULL ) ? '' :$multi_names[ 'prodescription_fr' ]
				);
				$this->db->insert( 'products_name', $multi_obj );
			}

			if( $from_update_recipe != 1 ){
				//Copying image
				if($product_info['0']['image'] != ''){
					$extension = pathinfo($product_info['0']['image'], PATHINFO_EXTENSION);
					$imagename = clean_pdf(pathinfo($product_info['0']['image'], PATHINFO_FILENAME));
					$image_name = $new_product_id.'_'.'Clone_'.$imagename.'.'.$extension;

					$image = file_get_contents($this->config->item('image_upload_url').'cp/images/product/'.$product_info['0']['image']);
					
					file_put_contents($this->config->item('old_bolive_img_path').'cp/images/product/'.$image_name, $image);
					$update_product_data['image'] = $image_name;
					$this->db->where('id',$new_product_id);
					$this->db->update('products', $update_product_data);
				}

				//Inserting data in products_labeler table
				$labeler = $this->db->get_where('products_labeler', array('product_id' => $clone_of_id))->result_array();
				$insert_label_data['product_id'] =	$new_product_id;
				if(!empty($labeler)){
					$labeler	= $labeler['0'];
					foreach ($labeler as $key => $label){
						if(($key != 'id') && ($key != 'product_id'))
							$insert_label_data[$key]	= $label;
					}
					$this->db->insert('products_labeler', $insert_label_data);
				}
				//Inserting data in products_discount table
				$get_product_discount_info = $this->db->get_where('products_discount',array('products_id' => $clone_of_id ))->result_array();
				if($get_product_discount_info){
					foreach($get_product_discount_info as $values){
						$insert_array = array(
							'products_id' => $new_product_id,
							'quantity' => $values['quantity'],
							'discount_per_qty' => $values['discount_per_qty'],
							'price_per_qty' => $values['price_per_qty'],
							'type' => $values['type']
						);
						$this->db->insert('products_discount',$insert_array);
					}
				}
				//Inserting data in groups_products table
				$get_product_group_info = $this->db->get_where('groups_products',array('products_id' => $clone_of_id ))->result_array();
				if($get_product_group_info){
					foreach($get_product_group_info as $values){
						$insert_array = array(
							'products_id' => $new_product_id,
							'groups_id' => $values['groups_id'],
							'attribute_name' => $values['attribute_name'],
							'attribute_value' => $values['attribute_value'],
							'type' => $values['type']
						);
						$this->db->insert('groups_products',$insert_array);
					}
				}

				//Inserting data in groups_order table
				$get_product_group_order_info = $this->db->get_where('groups_order',array('products_id' => $clone_of_id ))->result_array();
				if($get_product_group_order_info){
					foreach($get_product_group_order_info as $values){
						$insert_array = array(
							'company_id' => $values['company_id'],
							'products_id' => $new_product_id,
							'group_id' => $values['group_id'],
							'order_display' => $values['order_display'],
							'type' => $values['type']
						);
						$this->db->insert('groups_order',$insert_array);
					}
				}
			}

			/**
			 * Adding Ingredients
			 */
			$ingredients = $this->get_product_ingredients($clone_of_id,1,$fdd_pro_ids);
			if(!empty($ingredients)){
				foreach ($ingredients as $ingredient){
					$insert_array = array(
						'product_id' 		=> $new_product_id,
						'kp_id' 			=> $ingredient->kp_id,
						'ki_id' 			=> $ingredient->ki_id,
						'display_order' 	=> $ingredient->display_order,
						'kp_display_order'	=>$ingredient->kp_display_order,
						'date_added' 		=> date('Y-m-d H:i:s'),
						'is_obs_ing' 		=> $ingredient->is_obs_ing,
						'have_all_id' 		=> $ingredient->have_all_id,
						'aller_type' 		=> $ingredient->aller_type,
						'aller_type_fr' 	=> $ingredient->aller_type_fr,
						'aller_type_dch' 	=> $ingredient->aller_type_dch,
						'allergence' 		=> $ingredient->allergence,
						'allergence_fr' 	=> $ingredient->allergence_fr,
						'allergence_dch' 	=> $ingredient->allergence_dch,
						'sub_allergence' 	=> $ingredient->sub_allergence,
						'sub_allergence_fr' => $ingredient->sub_allergence_fr,
						'sub_allergence_dch'=> $ingredient->sub_allergence_dch
					);
					$this->db->insert('products_ingredients', $insert_array);
				}
			}

			/**
			 * Adding Traces
			 */
			$traces = $this->get_product_traces($clone_of_id);
			if(!empty($traces)){
				foreach ($traces as $trace){
					$insert_array = array(
						'product_id' => $new_product_id,
						'kp_id' => $trace->kp_id,
						'kt_id' => $trace->kt_id,
						'display_order' => $trace->display_order,
						'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_traces', $insert_array);
				}
			}

			/**
			 * Adding Allergence
			 */
			$allergence = $this->get_product_allergence_share($clone_of_id);
			if(!empty($allergence)){
				foreach ($allergence as $allg){
					$insert_array = array(
						'product_id' => $new_product_id,
						'kp_id' => $allg->kp_id,
						'ka_id' => $allg->ka_id,
						'display_order' => $allg->display_order,
						'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_allergence', $insert_array);
				}
			}
			/**
			 * Adding sub-allergence
			 */
			$sub_allergence = $this->get_product_sub_allergence_share($clone_of_id);
			if(!empty($sub_allergence)){
				foreach ($sub_allergence as $allg){
					$insert_array = array(
						'product_id' => $new_product_id,
						'kp_id' => $allg->kp_id,
						'parent_ka_id' => $allg->parent_ka_id,
						'sub_ka_id' => $allg->sub_ka_id,
						'display_order' => $allg->display_order,
						'date_added' => date('Y-m-d H:i:s')
					);
					$this->db->insert('product_sub_allergence', $insert_array);
				}
			}
			$this->db->where('obs_pro_id', $clone_of_id);
			$fdd_quants = $this->db->get('fdd_pro_quantity')->result();
			if(!empty($fdd_quants)){
				foreach ($fdd_quants as $fdd_quant){
					$insert_array_new = array(
						'is_obs_product' 	=> $fdd_quant->is_obs_product,
						'obs_pro_id' 		=> $new_product_id,
						'fdd_pro_id' 		=> $fdd_quant->fdd_pro_id,
						'quantity'			=> $fdd_quant->quantity,
						'unit'				=> $fdd_quant->unit,
						'semi_product_id'	=> $fdd_quant->semi_product_id,
						'included_semi'		=> $fdd_quant->included_semi,
						'fixed' 			=> $fdd_quant->fixed,
						'comp_id' 			=> $fdd_quant->comp_id,
						'created_dated' 	=> date("Y-m-d H:i:s")
					);
					$this->db->insert('fdd_pro_quantity', $insert_array_new);
				}
			}
			//-------------------------------------------------------------------------//
			return ( array( 'id'=>$new_product_id ) );
		}else{
			return(array('error'=>'Could not made clone. Try again'));
		}
	}

	function get_product_sub_allergence_share($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			$this->db->order_by('display_order', 'ASC');
			$allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	function get_product_allergence_share($product_id = 0, $k_type = 1){
		$allergence = array();
		if($product_id){
			$this->db->order_by('display_order', 'ASC');
			$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	function get_product_traces($product_id = 0, $k_type = 1){
		$traces = array();
		if($product_id){
			$this->db->select( 'products_traces.*' );
			$this->db->order_by('display_order', 'ASC');
			$this->db->join( 'traces', 'traces.t_id = products_traces.kt_id' );
			$traces = $this->db->get_where('products_traces', array('product_id' => $product_id))->result();
		}
		return $traces;
	}

	function get_product_ingredients( $product_id = 0, $k_type = 1, $fdd_pro_quant = array() ){
		$fdd_pro_quant = array_column( $fdd_pro_quant, 'fdd_pro_id' );
		$ingredients = array();
		if($product_id){
			$this->db->select( 'products_ingredients.*,ingredients.ki_name,ingredients.ki_name_dch,ingredients.ki_name_fr' );
			$this->db->join( 'ingredients', 'ingredients.ki_id = products_ingredients.ki_id' );
			$this->db->order_by( 'kp_display_order', 'ASC');
			$this->db->order_by( 'display_order', 'ASC');
			$ingredients = $this->db->get_where('products_ingredients', array('products_ingredients.product_id' => $product_id, 'products_ingredients.ki_id !=' => 0 ))->result();
		}

		$same_arr = array();
		$new_ingredients = array();

		if( !empty( $fdd_pro_quant ) ){
			foreach ($fdd_pro_quant as $key => $value) {
				foreach( $ingredients as $key1 => $value1 ){
					if( $value == $value1->kp_id ){
						$search_string = $value1->product_id.'-'.$value1->kp_id.'-'.$value1->display_order;
						if( !in_array( $search_string, $same_arr ) ){
							$new_ingredients[] = $value1;
							$same_arr[] = $search_string;
						}
					}
				}
			}
		}

		return $new_ingredients;
	}

	function to_update_CustomSemiId_to_SemiId_in_semi_product_id(){
		$this->db->distinct();
		$this->db->select('products.id');
		$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
		$this->db->limit( 4000, 18000 );
		$this->db->where('semi_product', '0');
		$this->db->where('semi_product_id !=', '0');
		$customs_having_custom = $this->db->get('fdd_pro_quantity')->result_array();
// echo "<pre>";
// print_r ($customs_having_custom);die;
// echo "</pre>";
		foreach ($customs_having_custom as $key => $value) {
			echo $key;
			$this->db->distinct();
			$this->db->select('semi_product_id');
			$this->db->join('products', 'products.id = fdd_pro_quantity.obs_pro_id');
			$this->db->where('obs_pro_id', $value['id']);
			$this->db->where('semi_product_id !=', '0');
			$res = $this->db->get('fdd_pro_quantity')->result_array();
			
			foreach ($res as $key1 => $value1) {
				$this->db->select('id');
				$this->db->where('id', $value1['semi_product_id']);
				$this->db->where('semi_product', '0');
				$is_custom = $this->db->get('products')->row_array();
				
				if( !empty( $is_custom ) ){
					$this->db->select('semi_product_id, included_semi');
					$this->db->where('obs_pro_id', $is_custom['id']);
					$this->db->where('semi_product_id !=', '0');
					$new_semi_id = $this->db->get('fdd_pro_quantity')->row_array();

					if( !empty($new_semi_id) ){
						$this->db->where('obs_pro_id', $value['id']);
						$this->db->where('semi_product_id', $value1['semi_product_id']);
						$this->db->update('fdd_pro_quantity', array( 'semi_product_id' => $new_semi_id['semi_product_id'], 'included_semi' => $new_semi_id['included_semi'] ));
					}
				}
			}
		}
		die("done");
	}

	function get_filenae( ){
		error_reporting(E_ALL);
		ini_set('display_error', 1 );
		$allergenkart_json2 = array( );
		$allergenkart_json = array( );
		if ($handle = opendir('/var/web/vd10018/public_html/obs/assets/allergenkart_json2/')) {
			while (false !== ($fileName = readdir($handle))) {
		       // $newName = str_replace("SKU#","",$fileName);
		        // rename('/opt/lampp/htdocs/fooddesk/application/application_fdd/models/mcp/'.$fileName, '/opt/lampp/htdocs/fooddesk/application/application_fdd/models/mcp/'.ucfirst( $fileName ) );
				array_push($allergenkart_json2, $fileName );
			}
			closedir($handle);
		}

		if ($handle = opendir('/var/web/vd10018/public_html/obs/assets/allergenkart_json/')) {
			while (false !== ($fileName = readdir($handle))) {
		       // $newName = str_replace("SKU#","",$fileName);
		        // rename('/opt/lampp/htdocs/fooddesk/application/application_fdd/models/mcp/'.$fileName, '/opt/lampp/htdocs/fooddesk/application/application_fdd/models/mcp/'.ucfirst( $fileName ) );
		        // echo $fileName.'<br>';
				array_push($allergenkart_json, $fileName );
			}
			closedir($handle);
		}
		echo "<pre>";
		print_r( array_diff( $allergenkart_json, $allergenkart_json2) );
	}

	function do_se(){
		$this->load->helper('default_setting');
		do_settings(4742,'Catering test clone');
	}

	function update_shop_post(){
		$company_id = 4226;
		if ($company_id) {
			$this->load->library('shop');
			$this->db->select('shop_version,parent_id,role,obsdesk_status');
			$this->db->where(array('id'=>$company_id));
			$data = $this->db->get('company')->result();
			if(!empty($data)) {
				foreach($data as $key => $value){
					$version = $value->shop_version;
					$parent_id = $value->parent_id;
					$role = $value->role;
					$action = 'category_json';
					$setting_action = 'general_setting_json';
					$this->shop->update_json_files($version,$company_id,$role,$parent_id,$action,$setting_action);
				}
			}
		}
	}
	function update_show_traces(){
		$this->db->select('id');
		$this->db->where("type_id LIKE '%20%'");
		$this->db->or_where("type_id LIKE '%27%'");
		$this->db->or_where("type_id LIKE '%28%'");
		$data = $this->db->get('company')->result_array();
		foreach($data as $key => $value){
			$this->db->where('company_id',$value['id']);
			$this->db->update('general_settings',array('show_traces' => '1'));
		}
		
	}
	//function to clone data of specific category of one company to another
	function copy_data_category($from = 0, $to = 0){
		
		if($from && $to){
			
			$categories = $this->db->get_where('categories', array('company_id' => $from))->result_array();

			if(!empty($categories)){
				foreach ($categories as $category){
					$old_category_id = $category['id'];

					echo $old_category_id.'--------';

					$new_cat_id = 18302;

					// Subcategory
					$subcategories = $this->db->get_where('subcategories', array('categories_id' => $old_category_id))->result_array();
					if(!empty($subcategories)){
						foreach ($subcategories as $subcategory){

							$old_subcat_id = $subcategory['id'];
							$new_subcat_id = '-1';

							// Products
							$this->addProducts($from,$to,$old_category_id,$new_cat_id,$old_subcat_id,$new_subcat_id);
						}
					}
					// Products
					$this->addProducts($from,$to,$old_category_id,$new_cat_id,'-1','-1');
				}
			}
		}
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */