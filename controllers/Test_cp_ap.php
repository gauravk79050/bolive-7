<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_cp_ap extends CI_Controller {

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
		// $this->load->library('recaptcha');
    }


    public function index(){

    	$arr = array(1,2,3,4,5);

    	unset($arr[2]);

    	print_r(json_decode(json_encode($arr)));die;

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

	function copy_data($from = 0, $to = 0){
		$this->staging = $this->load->database('staging',true);
		if($from && $to){
			die('here');
			// Deleting Data from $to company
			// $this->remove_data($to);

			$categories = $this->staging->get_where('categories', array('company_id' => $from))->result_array();
			if(!empty($categories)){
				foreach ($categories as $category){
					$old_category_id = $category['id'];
					echo $old_category_id;
					// Unsetting category ID
					unset($category['id']);

					// Setting company ID
					$category['company_id'] = $to;

					// Image copying
					if($category['image'] != ''){
						$image_name = end(explode("/",$category['image']));
						$image_name = $to."_".$image_name;

						if($image_file = @file_get_contents(base_url().$category['image'])){
							if(@file_put_contents(FCPATH.'assets/cp/images/categories/'.$image_name, $image_file)){
								$category['image'] = 'assets/cp/images/categories/'.$image_name;
							}
						}
					}

					// Created date update
					$category['created'] = date('Y-m-d');
					$category['updated'] = date('Y-m-d');

					if($this->db->insert('categories', $category)){
					//if(0){
						$new_cat_id = $this->db->insert_id();

						$this->staging->select('*');
						$this->staging->where( 'cat_id', $old_category_id );
						$cat_name_table = $this->staging->get( 'categories_name' )->result_array();

						if( !empty( $cat_name_table ) ) {
							foreach ( $cat_name_table as $cat_key => $cat_data ) {
								unset( $cat_data[ 'id' ] );
								$cat_data[ 'comp_id' ] = $to;
								$cat_data[ 'cat_id' ] = $new_cat_id;

								$this->db->insert( 'categories_name', $cat_data );
							}
						}  



						// Subcategory
						$subcategories = $this->staging->get_where('subcategories', array('categories_id' => $old_category_id))->result_array();
						if(!empty($subcategories)){
							foreach ($subcategories as $subcategory){

								$old_subcat_id = $subcategory['id'];
								unset($subcategory['id']);
								$subcategory['categories_id'] = $new_cat_id;

								// Image
								if($subcategory['subimage'] != ''){
									$image_name = end(explode("/",$subcategory['subimage']));
									$image_name = $to."_".$image_name;

									if($image_file = @file_get_contents(base_url().$subcategory['subimage'])){
										if(@file_put_contents(FCPATH.'assets/cp/images/categories/'.$image_name, $image_file)){
											$subcategory['subimage'] = 'assets/cp/images/categories/'.$image_name;
										}
									}
								}

								// Created date update
								$subcategory['subcreated'] = date('Y-m-d');
								$subcategory['subupdated'] = date('Y-m-d');

								if($this->db->insert('subcategories', $subcategory)){
									$new_subcat_id = $this->db->insert_id();



									$this->staging->select('*');
									$this->staging->where( 'subcat_id', $old_subcat_id );
									$subcat_name_table = $this->staging->get( 'subcategories_name' )->result_array();

									if( !empty( $subcat_name_table ) ) {
										foreach ( $subcat_name_table as $subcat_key => $subcat_data ) {
											unset( $subcat_data[ 'id' ] );
											$subcat_data[ 'categ_id' ] = $new_cat_id;
											$subcat_data[ 'subcat_id' ] = $new_subcat_id;

											$this->db->insert( 'subcategories_name', $subcat_data );
										}
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
			$this->addProducts($from,$to,0,0,0,0);
			echo"done";
		}
	}

	function addProducts($from,$to,$old_category_id,$new_cat_id,$old_subcat_id,$new_subcat_id){
		$products = $this->staging->get_where('products', array('company_id' => $from, 'categories_id' => $old_category_id, 'subcategories_id' => $old_subcat_id))->result_array();
		
		if(!empty($products)){
			foreach ($products as $product){
				$old_pro_id = $product['id'];
				echo $old_pro_id;

				unset($product['id']);

				$product['company_id'] = $to;

				$product['categories_id'] = $new_cat_id;

				$product['subcategories_id'] = $new_subcat_id;

				//Copying image
				if($product['image'] != ''){
					$extension = substr($product['image'],-3);
					$image_name = $to.'_'.$product['proname'].'.'.$extension;
					$image = @file_get_contents(base_url().'/assets/cp/images/product/'.$product['image']);
					@file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$image_name, $image);
					$product['image'] = $image_name;
				}

				$product['procreated'] = date('Y-m-d');
				$product['proupdated'] = date('Y-m-d');

				if($this->db->insert('products', $product)){
					$new_product_id = $this->db->insert_id();

					$this->staging->select('*');
					$this->staging->where( 'product_id', $old_pro_id );
					$prod_name_table = $this->staging->get( 'products_name' )->result_array();

					if( !empty( $prod_name_table ) ) {
						foreach ( $prod_name_table as $prod_key => $prod_data ) {
							unset( $prod_data[ 'id' ] );
							$prod_data[ 'product_id' ] = $new_product_id;

							$this->db->insert( 'products_name', $prod_data );
						}
					} 

					//Inserting data in products_discount table
					//if($product['discount'] == 'multi'){//Comment or Add for all discount|discount_person|discount_wt also
					$get_product_discount_info = $this->staging->get_where('products_discount',array('products_id' => $old_pro_id ))->result_array();
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
					//}

					//Inserting data in groups_products table
					$get_product_group_info = $this->staging->get_where('groups_products',array('products_id' => $old_pro_id ))->result_array();
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
					$get_product_group_order_info = $this->staging->get_where('groups_order',array('products_id' => $old_pro_id ))->result_array();
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

					/**
					 * Adding Ingredients
					 */
					$ingredients = $this->staging->get_where('products_ingredients', array('product_id' => $old_pro_id))->result();
					if(!empty($ingredients)){
						foreach ($ingredients as $ingredient){
							$insert_array = array(
									'product_id' => $new_product_id,
									'kp_id' => $ingredient->kp_id,
									'ki_id' => $ingredient->ki_id,
									'prefix' => $ingredient->prefix,
									'ki_name' => $ingredient->ki_name,
									'display_order' => $ingredient->display_order,
									'kp_display_order'=>$ingredient->kp_display_order,
									'date_added' => date('Y-m-d H:i:s'),
									'is_obs_ing'		=> $ingredient->is_obs_ing,
									'have_all_id' 		=> $ingredient->have_all_id,
									'aller_type'		=> $ingredient->aller_type,
									'aller_type_fr'		=> $ingredient->aller_type_fr,
									'aller_type_dch'	=> $ingredient->aller_type_dch,
									'allergence'		=> $ingredient->allergence,
									'allergence_fr'		=> $ingredient->allergence_fr,
									'allergence_dch'	=> $ingredient->allergence_dch,
									'sub_allergence'	=> $ingredient->sub_allergence,
									'sub_allergence_fr'	=> $ingredient->sub_allergence_fr,
									'sub_allergence_dch'=> $ingredient->sub_allergence_dch
							);
							$this->db->insert('products_ingredients', $insert_array);
						}
					}

					/**
					 * Adding Traces
					 */
					$traces = $this->staging->get_where('products_traces', array('product_id' => $old_pro_id))->result();
					if(!empty($traces)){
						foreach ($traces as $trace){
							$insert_array = array(
									'product_id' => $new_product_id,
									'kp_id' => $trace->kp_id,
									'kt_id' => $trace->kt_id,
									'prefix' => $trace->prefix,
									'kt_name' => $trace->kt_name,
									'display_order' => $trace->display_order,
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_traces', $insert_array);
						}
					}

					/**
					 * Adding Allergence
					 */
					$allergence = $this->staging->get_where('products_allergence', array('product_id' => $old_pro_id))->result();
					if(!empty($allergence)){
						foreach ($allergence as $allg){
							$insert_array = array(
									'product_id' => $new_product_id,
									'kp_id' => $allg->kp_id,
									'ka_id' => $allg->ka_id,
									'prefix' => $allg->prefix,
									'ka_name' => $allg->ka_name,
									'display_order' => $allg->display_order,
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_allergence', $insert_array);
						}
					}


					$this->staging->where('obs_pro_id', $old_pro_id);
					$fdd_quants = $this->staging->get('fdd_pro_quantity')->result();
					if(!empty($fdd_quants)){
						foreach ($fdd_quants as $fdd_quant){
							$insert_array_new = array(
									'is_obs_product' => $fdd_quant->is_obs_product,
									'obs_pro_id' => $new_product_id,
									'fdd_pro_id' => $fdd_quant->fdd_pro_id,
									'quantity'=> $fdd_quant->quantity,
									'semi_product_id'=>$fdd_quant->semi_product_id,
									'unit'=>$fdd_quant->unit,
									'fixed'=>$fdd_quant->fixed
							);

							$this->db->insert('fdd_pro_quantity', $insert_array_new);
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
	/*update product fdd product short name in products_ingredients table*/
// 	function testing_pro(){
// 		ini_set('memory_limit', '20000M');
// 		set_time_limit(0);
// 		ini_set('max_execution_time', 0);

// 		$this->db->select('products_ingredients.product_id');
// 		$this->db->join('products','products.id = products_ingredients.product_id');
// 		$this->db->group_by('products_ingredients.product_id');
// 		$this->db->order_by('products_ingredients.product_id','desc');
// 		//$this->db->where('products.id >=','10000');
// 		$this->db->where('products.id <','15000');
// 		$result = $this->db->get_where('products_ingredients',array('products_ingredients.is_obs_ing'=>0,'products.direct_kcp_id'=>0))->result();
// // 		echo '<pre>';
// // 		print_r($result);die('11');
// 		$x = array();
// 		foreach ($result as $res){
// 			$this->db->select('kp_id');
// 			$this->db->group_by('kp_id');
// 			$this->db->order_by('kp_id');
// 			$result1 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'is_obs_ing'=>0))->result();
// 			foreach ($result1 as $res1){
// 				$result2 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id,'ki_id !='=>0));
// 				$num = $result2->num_rows();
// 				if($num > 0){
// 					$this->db->where_not_in('ki_name',array('(',')'));
// 					$result3 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id,'ki_id'=>0))->result_array();
// 					//$result4 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id,'ki_name'=>')'))->result_array();
// 					if(empty($result3)){
// 						$x[] = array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id);
// 						
// 						$this->fdb->select('p_short_name_dch');
// 						$result5= $this->fdb->get_where('products', array('p_id' => $res1->kp_id))->row();

// 						if(!empty($result5)){
// 							$this->db=$this->load->database('default',TRUE);
// 							$this->db->select('kp_display_order');
// 							$result6= $this->db->get_where('products_ingredients', array('product_id'=>$res->product_id,'kp_id' => $res1->kp_id))->result();

// 							$kp_display_order = (!empty($result6))?$result6[0]->kp_display_order:0;
// 							$insert_array = array(
// 										'product_id' => $res->product_id,
// 										'kp_id' => $res1->kp_id,
// 										'ki_id' => 0,
// 										'prefix' => '',
// 										'ki_name' => $result5->p_short_name_dch,
// 										'is_obs_ing' => 0,
// 										'display_order' => 1,
// 										'kp_display_order'=> $kp_display_order,
// 										'date_added' => date('Y-m-d H:i:s')
// 							);

// 							$this->db->insert('products_ingredients', $insert_array);
// 						}
// 					}
// 				}
// 			}
// 			//if(count($x) > 20)
// 				//break;
// 		}
// 		echo '<pre>';
// 		print_r($x);die('1');
// 	}

// 	function testing_pro(){
// 		ini_set('memory_limit', '20000M');
// 		set_time_limit(0);
// 		ini_set('max_execution_time', 0);

// 		$this->db->select('products_ingredients.product_id');
// 		$this->db->join('products','products.id = products_ingredients.product_id');
// 		$this->db->group_by('products_ingredients.product_id');
// 		$this->db->order_by('products_ingredients.product_id','asc');
// 		$this->db->where('products.id >=','89000');
// 		$this->db->where('products.id <','95000');
// 		$result = $this->db->get_where('products_ingredients',array('products_ingredients.is_obs_ing'=>0,'products.direct_kcp_id'=>0))->result();
// 		// 		echo '<pre>';
// 		// 		print_r($result);die('11');
// 		$x = array();
// 		foreach ($result as $res){
// 			$this->db->select('kp_id');
// 			$this->db->group_by('kp_id');
// 			$this->db->order_by('kp_id');
// 			$result1 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'is_obs_ing'=>0))->result();
// 			foreach ($result1 as $res1){
// 				$result2 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id,'ki_id !='=>0));
// 				$num = $result2->num_rows();
// 				if($num > 0){
// 					$result3 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id,'ki_name'=>'('))->result_array();
// 					$result4 = $this->db->get_where('products_ingredients',array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id,'ki_name'=>')'))->result_array();
// 					if(empty($result3) || empty($result4)){
// 						$x[] = array('product_id'=>$res->product_id,'kp_id'=>$res1->kp_id);

// 						$this->db=$this->load->database('default',TRUE);
// 						$this->db->select('kp_display_order');
// 						$result6= $this->db->get_where('products_ingredients', array('product_id'=>$res->product_id,'kp_id' => $res1->kp_id))->result();

// 						$kp_display_order = (!empty($result6))?$result6[0]->kp_display_order:0;

// 						if(empty($result3)){
// 							$insert_array = array(
// 									'product_id' => $res->product_id,
// 									'kp_id' => $res1->kp_id,
// 									'ki_id' => 0,
// 									'prefix' => '',
// 									'ki_name' => '(',
// 									'is_obs_ing' => 0,
// 									'display_order' => 2,
// 									'kp_display_order'=> $kp_display_order,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);

// 							$this->db->insert('products_ingredients', $insert_array);
// 							$display_order = (!empty($result6))?count($result6)+2:20;
// 						}
// 						else{
// 							$display_order = (!empty($result6))?count($result6)+1:20;
// 						}

// 						if(empty($result4)){
// 							$insert_array = array(
// 									'product_id' => $res->product_id,
// 									'kp_id' => $res1->kp_id,
// 									'ki_id' => 0,
// 									'prefix' => '',
// 									'ki_name' => ')',
// 									'is_obs_ing' => 0,
// 									'display_order' => $display_order,
// 									'kp_display_order'=> $kp_display_order,
// 									'date_added' => date('Y-m-d H:i:s')
// 							);

// 							$this->db->insert('products_ingredients', $insert_array);
// 						}
// 					}
// 				}
// 			}
// 			//if(count($x) > 20)
// 			//break;
// 		}
// 		echo '<pre>';
// 		print_r($x);die('1');
// 	}

/* 	function testing_pro(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);

		$this->db->select('fdd_pro_quantity.obs_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$this->db->group_by('fdd_pro_quantity.obs_pro_id');
		$this->db->order_by('fdd_pro_quantity.obs_pro_id','asc');
		$this->db->where('products.id >=','70000');
		$this->db->where('obs_pro_id <','74000');
		$results = $this->db->get_where('fdd_pro_quantity',array('products.direct_kcp_id'=>0))->result_array();
// 		echo '<pre>';
// 		print_r($results);die('11');
		$x = array();
		if(!empty($results)){
			foreach ($results as $result){
				$this->db->select('fdd_pro_id');
				$this->db->where('obs_pro_id',$result['obs_pro_id']);
				$this->db->order_by('quantity','desc');
				$result_fdd = $this->db->get('fdd_pro_quantity')->result_array();

				$x = array();
				if(!empty($result_fdd)){
					$skip = false;
					foreach ($result_fdd as $key=>$res){
						if(!in_array($res['fdd_pro_id'],$x)){
							$x[] = $res['fdd_pro_id'];
						}
						else{
							$skip = true;
							break;
						}
					}

					if(!$skip){
						foreach ($result_fdd as $key=>$res){
							$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
							$this->db->update('products_ingredients',array('kp_display_order'=>$key+1));

							$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
							$this->db->update('products_ingredients_vetten',array('kp_display_order'=>$key+1));

							$this->db->where(array('product_id'=>$result['obs_pro_id'],	'kp_id'=> $res['fdd_pro_id']));
							$this->db->update('products_additives',array('kp_display_order'=>$key+1));

						}
					}
				}
			}
		}
		die('1');
	} */

// 	function remove_bad_pws(){
// 		$this->db->select('fdd_pro_id');
// 		$this->db->group_by('fdd_pro_id');
// 		$this->db->order_by('fdd_pro_id','asc');
// 		$result = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>1))->result();
// // 		echo '<pre>';
// // 		print_r($result);die;

// 		foreach ($result as $res){
// 			$this->db->select('id');
// 			$result1 = $this->db->get_where('products',array('id'=>$res->fdd_pro_id))->result();

// 			if(empty($result1)){
// 				echo $res->fdd_pro_id.'<br/>';
// // 				$product_id = $res->kp_id;

// // 				$this->db->delete('products_pending',array('product_id'=>$product_id));
// // 				$this->db->delete('products_ingredients',array('kp_id'=>$product_id,'is_obs_ing'=>1));
// 			}
// 		}
// 	}

	/*function update_duedate(){
		$this->db->select('products.procreated,products_labeler.duedate,products.id');
		$this->db->join('products_labeler','products_labeler.product_id = products.id');
		$this->db->where('duedate !=','0000-00-00');
		$result = $this->db->get('products')->result_array();
		//print_r($result);
		foreach ($result as $res){
			$procreated = strtotime($res['procreated']);
			$duedate = strtotime($res['duedate']);
			$daylen = 60*60*24;

			$diff = $duedate-$procreated;
			$diff_now = $duedate - time();
			//if($diff_now > 0){
				if($diff > 0){
					$new_due = $diff/$daylen;
				}
				else{
					$new_due = 0;
				}
			//}
			//else{
			//	$new_due = 0;
			//}
			//echo $res['id'].'<br/>';
			//echo $new_due.'<br/>';
			$this->db->where('product_id',$res['id']);
			$this->db->update('products_labeler',array('duedate1'=>$new_due));
		}
	}*/

/* 	function test_lang(){
		$number = 1.556669;
		$number = round((float)$number, 1);
		echo $number;
		echo '<br/>';
		$number = money_format("%!.0n",(float)$number);
		echo $number;
		die;
	} */

/* 	function my_qur(){
		$sql = "UPDATE `general_settings` SET `msg_front_txt_color`= '#000000' WHERE `msg_front_txt_color` = '#ffffff'";
		//$sql = "ALTER TABLE `products` ADD `more_image` TEXT NOT NULL";
		$this->db->query($sql);
		//$x = $this->db->get_where('products',array('id'=>42549))->row();
		//print_r($x);die;
	} */

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

/* 	function update_desc(){
		$this->db->select('products.id,products.prodescription as desc1,products_temp.prodescription as desc2');
		$this->db->where(array('products.prodescription'=>''));
		$this->db->where(array('products_temp.prodescription !='=>''));
		//$this->db->where(array('products.id <'=> 114221));
		$this->db->join('products_temp','products.id = products_temp.id','left');
		$result = $this->db->get('products')->result_array();
		echo '<pre>';print_r($result);die;

		if(!empty($result)){
			foreach ($result as $res){
				$this->db->where(array('id'=>$res['id']));
				$this->db->update('products',array('prodescription'=>$res['desc2']));
			}
		}
	} */

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

	function add_company_id($table_name){
		$this->db->select('id');
		$comp_data = $this->db->get('company')->result_array();
		foreach($comp_data as $comp_val){

			$order_settings["company_id"] = $comp_val['id'];
			$this->db->insert($table_name,$order_settings);
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

	function clean_image($string = ''){
		$string = str_replace(' ', '-', $string);
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		return preg_replace('/-+/', '-', $string);
	}

	function update_french_lone_product(){
		$this->db->select('id');
		$this->db->like('image','Clone');
		$this->db->order_by('id','asc');
		$query_res = $this->db->get('products')->result_array();
		foreach($query_res as $val)
		{
			$this->db->select('image');
			$product_image = $this->db->get_where('products',array('id'=>$val['id']))->result_array();
			if(!empty($product_image))
			{
				$product_image = $product_image[0]['image'];
				$path = dirname(__FILE__).'/../../assets/cp/images/product/';
				if(file_exists($path.$product_image))
				{
					$extension = pathinfo($product_image, PATHINFO_EXTENSION);
					$new_image = $this->clean_image(pathinfo($product_image, PATHINFO_FILENAME));
					$this->db->where('id',$val['id']);
					$this->db->update('products',array('image'=>$new_image.'.'.$extension));
					rename($path.$product_image,$path.$new_image.'.'.$extension);
				}
			}
		}
	}

	function update_holiday_settings($activate = '0')
	{
		$company_arr = array();
		$company_arr[]=601;
		$company_arr[]=4050;
		$company_arr[]=86;
		$company_arr[]=1008;
		$company_arr[]=1523;
		$company_arr[]=1040;
		$company_arr[]=1345;
		$company_arr[]=602;
		$company_arr[]=135;
		$company_arr[]=113;
		foreach($company_arr as $comp_id){
			$this->db->select('id');
			$comp_prod = $this->db->get_where('products',array('company_id'=>$comp_id))->result_array();
			if(!empty($comp_prod)){
				foreach($comp_prod as $val){
					$this->db->where('id',$val['id']);
					$this->db->update('products',array('holiday_availability'=>$activate));
				}
			}
		}
	}

	function add_prod_pend(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		error_reporting(E_ALL);
		$this->db->select('id');
		$comp_res = $this->db->get_where('company')->result_array();
		foreach ($comp_res as $val_d)
		{
			$comp_id = $val_d['id'];
			$check_exist = array();
			$this->db->select('id');
			$comp_prod = $this->db->get_where('products',array('company_id' => $comp_id))->result_array();
			if(!empty($comp_prod)){
				foreach ($comp_prod as $val){
					$this->db->select('fdd_pro_id,obs_pro_id');
					$comp_prod = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id' => $val['id'],'is_obs_product' => 1))->result_array();
					if (!empty($comp_prod)){
						foreach ($comp_prod as $comp_val){
							$this->db->select('product_id');
							$pend_exist = $this->db->get_where('products_pending',array('product_id'=>$comp_val['fdd_pro_id'],'company_id'=> $comp_id))->result_array();

							$this->db->select('obs_pro_id');
							$mail_exist = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$comp_val['fdd_pro_id']))->result_array();

							if (empty($pend_exist) && empty($mail_exist))
							{
								$insert_array = array('product_id'=>$comp_val['fdd_pro_id'],'company_id'=> $comp_id, 'prosheet_pws'=>'');
								$this->db->insert('products_pending',$insert_array);

								$insert_array1 = array('obs_pro_id'=>$comp_val['fdd_pro_id']);
								$this->db->insert('contacted_via_mail',$insert_array1);
							}
						}
					}
				}
			}
		}
	}

	function check_recipe($comp_id = 0){

		$check_exist = array();
		$this->db->select('id');
		$comp_prod = $this->db->get_where('products',array('company_id' => $comp_id))->result_array();
		if(!empty($comp_prod)){
			foreach ($comp_prod as $val){
				$this->db->select('fdd_pro_id');
				$comp_prod = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id' => $val['id']))->result_array();
				if (!empty($comp_prod)){
					$check_exist [] = $val['id'];
				}
			}
			if(!empty($check_exist)){
				print_r($check_exist);die;
			}
			else{
				echo "Recipe not exist";
			}
		}
	}

	function copy_data_1($from = 0, $to = 0){
		// die();
		if($from && $to){
			// Deleting Data from $to company
			// $this->remove_data($to);

			$categories = $this->db->get_where('categories', array('company_id' => $from))->result_array();

			if(!empty($categories)){
				foreach ($categories as $category){
					$old_category_id = $category['id'];

					// Unsetting category ID
					unset($category['id']);

					// Setting company ID
					$category['company_id'] = $to;

					// Image copying
					if($category['image'] != ''){
						$image_name = end(explode("/",$category['image']));
						$image_name = $to."_".$image_name;

						if($image_file = @file_get_contents(base_url().$category['image'])){
							if(@file_put_contents(FCPATH.'assets/cp/images/categories/'.$image_name, $image_file)){
								$category['image'] = 'assets/cp/images/categories/'.$image_name;
							}
						}
					}

					// Created date update
					$category['created'] = date('Y-m-d');
					$category['updated'] = date('Y-m-d');

					if($this->db->insert('categories', $category)){
						//if(0){
						$new_cat_id = $this->db->insert_id();

						$this->db->select('*');
						$this->db->where( 'cat_id', $old_category_id );
						$cat_name_table = $this->db->get( 'categories_name' )->result_array();

						if( !empty( $cat_name_table ) ) {
							foreach ( $cat_name_table as $cat_key => $cat_data ) {
								unset( $cat_data[ 'id' ] );
								$cat_data[ 'comp_id' ] = $to;
								$cat_data[ 'cat_id' ] = $new_cat_id;

								$this->db->insert( 'categories_name', $cat_data );
							}
						}

						// Subcategory
						$subcategories = $this->db->get_where('subcategories', array('categories_id' => $old_category_id))->result_array();
						if(!empty($subcategories)){
							foreach ($subcategories as $subcategory){

								$old_subcat_id = $subcategory['id'];
								unset($subcategory['id']);
								$subcategory['categories_id'] = $new_cat_id;

								// Image
								if($subcategory['subimage'] != ''){
									$image_name = end(explode("/",$subcategory['subimage']));
									$image_name = $to."_".$image_name;

									if($image_file = @file_get_contents(base_url().$subcategory['subimage'])){
										if(@file_put_contents(FCPATH.'assets/cp/images/categories/'.$image_name, $image_file)){
											$subcategory['subimage'] = 'assets/cp/images/categories/'.$image_name;
										}
									}
								}

								// Created date update
								$subcategory['subcreated'] = date('Y-m-d');
								$subcategory['subupdated'] = date('Y-m-d');

								if($this->db->insert('subcategories', $subcategory)){
									$new_subcat_id = $this->db->insert_id();

									$this->db->select('*');
									$this->db->where( 'subcat_id', $old_subcat_id );
									$subcat_name_table = $this->db->get( 'subcategories_name' )->result_array();

									if( !empty( $subcat_name_table ) ) {
										foreach ( $subcat_name_table as $subcat_key => $subcat_data ) {
											unset( $subcat_data[ 'id' ] );
											$subcat_data[ 'categ_id' ] = $new_cat_id;
											$subcat_data[ 'subcat_id' ] = $new_subcat_id;

											$this->db->insert( 'subcategories_name', $subcat_data );
										}
									}

									// Products
									$this->addProducts_1($from,$to,$old_category_id,$new_cat_id,$old_subcat_id,$new_subcat_id);

								}
							}
						}

						// Products
						$this->addProducts_1($from,$to,$old_category_id,$new_cat_id,'-1','-1');
					}
				}
			}

			$this->db->where('company_id', $from);
			$pend_products = $this->db->get('products_pending')->result();
			if (!empty($pend_products)){
				foreach ($pend_products as $pend_prod){
					$insert_array = array(
							'product_id' => $pend_prod->product_id,
							'company_id' => $to,
							'prosheet_pws' => $pend_prod->prosheet_pws,
							'date'		 => date('Y-m-d H:i:s')
					);
					$this->db->insert('products_pending', $insert_array);
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
			}

			$this->db->where('company_id', $from);
			$request_gs1 = $this->db->get('request_gs1')->result_array();
			if(!empty($request_gs1)){
				foreach ($request_gs1 as $key => $gs1Data) {
					$gs1Data['company_id'] = $to;
					unset($gs1Data['id']);
					$this->db->insert('request_gs1', $gs1Data);
				}
			}

			$this->db->where('company_id', $from);
			$request_ps1 = $this->db->get('request_ps1')->result_array();
			if(!empty($request_ps1)){
				foreach ($request_ps1 as $key => $ps1Data) {
					$ps1Data['company_id'] = $to;
					unset($ps1Data['id']);
					$this->db->insert('request_ps1', $ps1Data);
				}
			}
		}
	}

	function addProducts_1($from,$to,$old_category_id,$new_cat_id,$old_subcat_id,$new_subcat_id){
		$products = $this->db->get_where('products', array('company_id' => $from, 'categories_id' => $old_category_id, 'subcategories_id' => $old_subcat_id))->result_array();

		if(!empty($products)){
			foreach ($products as $product){
				$old_pro_id = $product['id'];

				unset($product['id']);

				$product['company_id'] = $to;

				$product['categories_id'] = $new_cat_id;

				$product['subcategories_id'] = $new_subcat_id;

				//Copying image
				if($product['image'] != ''){
					$extension = substr($product['image'],-3);
					$image_name = $to.'_'.$product['proname'].'.'.$extension;
					$image = @file_get_contents(base_url().'/assets/cp/images/product/'.$product['image']);
					@file_put_contents(dirname(__FILE__).'/../../assets/cp/images/product/'.$image_name, $image);
					$product['image'] = $image_name;
				}

				$product['procreated'] = date('Y-m-d');
				$product['proupdated'] = date('Y-m-d');

				if($this->db->insert('products', $product)){
					$new_product_id = $this->db->insert_id();

					$this->db->select('*');
					$this->db->where( 'product_id', $old_pro_id );
					$prod_name_table = $this->db->get( 'products_name' )->result_array();

					if( !empty( $prod_name_table ) ) {
						foreach ( $prod_name_table as $prod_key => $prod_data ) {
							unset( $prod_data[ 'id' ] );
							$prod_data[ 'product_id' ] = $new_product_id;

							$this->db->insert( 'products_name', $prod_data );
						}
					}

					//Inserting data in products_labeler table
					$labeler = $this->db->get_where('products_labeler', array('product_id' => $old_pro_id))->result_array();

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
					//if($product['discount'] == 'multi'){//Comment or Add for all discount|discount_person|discount_wt also
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
					//}

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

					// /**
					//  * Adding Allergence
					//  */
					$allergence = $this->db->get_where('products_allergence', array('product_id' => $old_pro_id))->result();
					if(!empty($allergence)){
						foreach ($allergence as $allg){
							$insert_array = array(
									'product_id' => $new_product_id,
									'kp_id' => $allg->kp_id,
									'ka_id' => $allg->ka_id,
									'prefix' => $allg->prefix,
									'ka_name' => $allg->ka_name,
									'display_order' => $allg->display_order,
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('products_allergence', $insert_array);
						}
					}

					$sub_allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $old_pro_id))->result();
					if(!empty($sub_allergence)){
						foreach ($sub_allergence as $allg){
							$insert_array = array(
									'product_id' => $new_product_id,
									'kp_id' => $allg->kp_id,
									'parent_ka_id' => $allg->parent_ka_id,
									'sub_ka_id' => $allg->sub_ka_id,
									'sub_ka_name' => $allg->sub_ka_name,
									'display_order' => $allg->display_order,
									'date_added' => date('Y-m-d H:i:s')
							);
							$this->db->insert('product_sub_allergence', $insert_array);
						}
					}

					$this->db->where('obs_pro_id', $old_pro_id);
					$fdd_quants = $this->db->get('fdd_pro_quantity')->result();
					if(!empty($fdd_quants)){
						foreach ($fdd_quants as $fdd_quant){
							$insert_array_new = array(
									'is_obs_product' => $fdd_quant->is_obs_product,
									'obs_pro_id' => $new_product_id,
									'fdd_pro_id' => $fdd_quant->fdd_pro_id,
									'real_supp_id' => $fdd_quant->real_supp_id,
									'quantity'=> $fdd_quant->quantity,
									'fixed'=> $fdd_quant->fixed,
									'unit'=>$fdd_quant->unit,
									'comp_id'=>$to,
									'semi_product_id'=>$fdd_quant->semi_product_id
							);

							$this->db->insert('fdd_pro_quantity', $insert_array_new);
						}
					}
				}
			}
		}
	}

	function update_desk_settings(){
		$this->db->select('company_id,lang_id');
		$comp_prod = $this->db->get('desk_settings')->result_array();
		if(!empty($comp_prod)){
			foreach ($comp_prod as $val){
				if($val['lang_id'] == 0){
					$this->db->select('language_id');
					$comp_lang_id = $this->db->get_where('general_settings',array('company_id'=>$val['company_id']))->result_array();
					$this->db->where('company_id',$val['company_id']);
					$this->db->update('desk_settings',array('lang_id'=>$comp_lang_id[0]['language_id']));
				}
			}
		}
	}


	function update_weight_measures(){
		ini_set('memory_limit', '512M');
		$this->load->library("Excel");

		$inputFileName = dirname(__FILE__).'/../../assets/excels/poids.xlsx';

		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);

		$objPHPExcel = $objReader->load($inputFileName);
		$total_sheets=$objPHPExcel->getSheetCount();
		$allSheetName=$objPHPExcel->getSheetNames();

		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
		$arr_data = array();

		$highestRow = 182;
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
		$arr = array();

		foreach ($worksheet_arr0 as $key => $val){$flag = false;
			$this->db->select('id');
			$res = $this->db->get_where('weights_and_measures',array('weight'=>$val['2']))->result_array();
			if (!empty($res))
			{
				foreach ($res as $k=>$v){
					if ($flag)
					{

					}
					else
					{
						if (!in_array($v['id'],$arr))
						{
							$this->db->where('id',$v['id']);
							$this->db->update('weights_and_measures',array('nutrient_fr'=>$val['0'],'capacity_fr'=>$val['1']));
							$arr[] = $v['id'];
							$flag = true;
						}
					}
				}
			}
		}
	}


	function get_company_prodyucts(){
		$this->db->select('id');
		$this->db->order_by('id','ASC');
		$query = $this->db->get_where('products',array('company_id'=>4184))->result_array();
		if(!empty($query)){
			$i = 1;
			foreach($query as $val)
			{
				$this->db->where('id',$val['id']);
				$this->db->update('products',array('pro_art_num'=>$i));
				$i++;
			}
		}
	}

	function delete_orders($i = 0){
		$this->load->model('Morders');
		$this->db->select('id');
		$this->db->order_by('id','ASC');
		$query = $this->db->get_where('orders',array('company_id'=>135))->result_array();
		foreach($query as $k=>$v)
		{
			$cancelled = false;
			if($i)
			{
				$this->Morders->delete_order($v['id'],$cancelled);
			}
		}
	}

	function article_no_8d(){
		$this->db->select('id,pro_art_num');
		$this->db->order_by('id','ASC');
		$query = $this->db->get_where('products',array('pro_art_num !='=>''))->result_array();
		foreach($query as $val){
			if(strlen(trim($val['pro_art_num'])) < 8){
				$new_art = str_pad(trim($val['pro_art_num']),8,"0",STR_PAD_LEFT);
				$this->db->where('id',$val['id']);
				$this->db->update('products',array('pro_art_num'=>$new_art));
			}
		}
	}

	function update_existing_products_3912(){
		$arr = array();
		$cat = array();
		$this->db->select('id');
		$this->db->order_by('id','ASC');
		$query = $this->db->get_where('categories',array('company_id'=>3912))->result_array();
		if(!empty($query))
		{
			foreach($query as $val){
				$cat[] = $val['id'];
			}
		}
		$this->db->select('id,categories_id');
		$this->db->order_by('id','ASC');
		$this->db->where(array('company_id'=>3912));
		$where1 = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where1);
		$this->db->where_not_in('categories_id',$cat);
		$products = $this->db->get_where('products')->result_array();
		foreach($products as $val){
			$this->db->select('categories_id,proname');
			$this->db->where('id',$val['id']);
			$this->db->where('company_id',3912);
			$exiating = $this->db->get('products_tmp')->result_array();
			if(!empty($exiating))
			{
				$this->db->where('id',$val['id']);
				$this->db->update('products',array('proname'=>$exiating[0]['proname'],'categories_id'=>$exiating[0]['categories_id']));
			}
			else{
				$arr[] = $val['id'];
			}
		}
		print_r($arr);die;
	}


	function reanme_prod_comp(){
		$pro = array();
		$this->db->select('id,company_id,categories_id,proname');
		$this->db->order_by('company_id','ASC');
		$query = $this->db->get_where('products',array('company_id !='=>0,'categories_id !='=>0))->result_array();
		foreach($query as $val){
			$query_new = $this->db->get_where('categories',array('company_id '=>$val['company_id'],'id'=>$val['categories_id']))->result_array();
			if(empty($query_new)){
// 				$query = $this->db->get_where('products_tmp',array('company_id'=>$val['company_id'],'id'=>$val['id']))->result_array();
// 				if(!empty($query)){
// 					unset($query[0]['id']);
// 					unset($query[0]['company_id']);
// 					unset($query[0]['extra_notification']);
// 					$this->db->where(array('id'=>$val['id'],'company_id'=>$val['company_id']));
// 					$this->db->update('products',$query[0]);
// 				}

				$pro[$val['company_id']][] = array(
					'cat_id'=>	$val['categories_id'],
					'proname'=>$val['proname'],
					'id'=>$val['id']
				);
			}

		}
		echo "<pre>";
		print_r($pro);die;
// 		foreach($pro as $key=>$val){
// 			foreach($val as $k=>$v){
// 				$query = $this->db->get_where('products_tmp',array('company_id'=>$key,'id'=>$v['id']))->result_array();
// 				if(!empty($query)){
// 					$this->db->where(array('id'=>$v['id'],'company_id'=>$key));
// 					$this->db->update('products',array('categories_id'=>$query[0]['categories_id'],'proname'=>$query[0]['proname']));
// 				}
// 			}
// 		}
	}

	function prod_art(){
		$this->db->select('id,pro_art_num');
		$this->db->order_by('id','ASC');
		$this->db->where('pro_art_num !=','');
		$quuery = $this->db->get('products_tmp')->result_array();
		foreach($quuery as $res){
			$this->db->select('pro_art_num');
			$this->db->where('id',$res['id']);
			$qery = $this->db->get('products')->result_array();
			if (!empty($qery))
			{
				if (strcmp($qery[0]['pro_art_num'],$res['pro_art_num']) == 0)
				{

				}
				else{
					$str = ltrim($qery[0]['pro_art_num'], '0');
					if (strcmp($str,$res['pro_art_num']) == 0)
					{
						$this->db->where(array('id'=>$res['id']));
						$this->db->update('products',array('pro_art_num'=>$res['pro_art_num']));
					}
				}
			}
		}
	}

	function update_pws_fdd(){
		$this->db->select('fdd_pro_quantity.*');
		$this->db->order_by('id','ASC');
		$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
		$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>6083,'fdd_pro_quantity.is_obs_product'=>0,'products.company_id'=>3929));
		$used_obs_pro = $this->db->get('fdd_pro_quantity')->result_array();
		echo "<pre>";
		print_r($used_obs_pro);die;
	}

	function update_art_no_8_digit(){
		$this->db->select('id,pro_art_num');
		$this->db->order_by('id','ASC');
		$this->db->where('pro_art_num !=','');
		$quuery = $this->db->get_where('products',array('company_id'=>1416))->result_array();
		if(!empty($quuery)){
			foreach($quuery as $val)
			{
// 				if(strlen(trim($val['pro_art_num'])) < 8){
// 					$art_no = str_pad(trim($val['pro_art_num']),8,"0",STR_PAD_LEFT);
// 					$this->db->where(array('id'=>$val['id'],'company_id'=>2260));
// 					$quuery = $this->db->update('products',array('pro_art_num'=>$art_no));
// 				}

// 				$this->db->select('pro_art_num');
// 				$this->db->where('id',$val['id']);
// 				$qery = $this->db->get('products_tmp')->result_array();
// 				if(!empty($qery))
// 				{
					$pro_art_num = $val['pro_art_num'];
					$pro_art_num = ltrim($pro_art_num, '0');

					$this->db->where(array('id'=>$val['id'],'company_id'=>1416));
	 				$quuery = $this->db->update('products',array('pro_art_num'=>$pro_art_num));
				//}
			}
		}
	}


	function add_additive_vetten_in_ingredient(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		error_reporting(E_ALL);
		$this->db->select('fdd_pro_id,obs_pro_id');
		$this->db->order_by('obs_pro_id','ASC');
		$obs_products = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0,'obs_pro_id >'=>73506))->result_array();


		if(!empty($obs_products))
		{
			foreach($obs_products as $obs_products_key => $obs_products_val){
				$this->db->select('language_id');
				$this->db->join('products','products.company_id = general_settings.company_id');
				$this->db->where('products.id',$obs_products_val['obs_pro_id']);
				$lang = $this->db->get('general_settings')->result_array();
				if (isset($lang) && !empty($lang))
				{
					$language_id = $lang[0]['language_id'];
				}
				else{
					$language_id = 2;
				}

				if ($language_id == 2)
				{
					$lang = 'ing_name_dch';
				}
				elseif ($language_id == 3)
				{
					$lang = 'ing_name_fr';
				}else{
					$lang = 'ing_name_dch';
				}

				$kp_display_order = $disp_ord = 1;
				$exist = $this->db->get_where('products_ingredients',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id'],'ki_name'=>')'))->result_array();
				if (!empty($exist))
				{
					$data['obs_pro_id'] = $obs_products_val['obs_pro_id'];
					$fp = fopen(dirname(__FILE__).'/a.txt', 'a');
					fwrite($fp, json_encode($data));
					fclose($fp);
					$this->db->distinct();
					$this->db->select('kp_display_order');
					$kp_display_order = $this->db->get_where('products_ingredients',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id']))->result_array();
					if (!empty($kp_display_order)){
						$kp_display_order = intval($kp_display_order[0]['kp_display_order']);
					}

					$this->db->select('display_order');
					$disp_ord = $this->db->get_where('products_ingredients',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id'],'ki_id'=>0,'ki_name'=>')'))->result_array();
					if (!empty($disp_ord)){
						$disp_ord = intval($disp_ord[0]['display_order']);
					}

					$set_vet = array();
					$this->db->select('product_id,kp_id,ki_id,ki_name,date_added,have_all_id');
					$this->db->order_by('display_order','ASC');
					$ing_vetten = $this->db->get_where('products_ingredients_vetten',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id']))->result_array();
					if(!empty($ing_vetten)){
						foreach($ing_vetten as $key => $val)
						{
							$set_vet = array(
									'product_id'=>$val['product_id'],
									'kp_id' => $val['kp_id'],
									'ki_id' => $val['ki_id'],
									'prefix' => '',
									'ki_name' => $val['ki_name'],
									'display_order' => $disp_ord,
									'kp_display_order' => $kp_display_order,
									'date_added' => $val['date_added'],
									'is_obs_ing' => 0,
									'have_all_id' => $val['have_all_id']
							) ;
							$this->db->insert('products_ingredients',$set_vet);
							$disp_ord++;
						}
						$this->db->delete('products_ingredients_vetten',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id']));
					}

					$set_vet = array();
					$this->db->distinct();
					$this->db->select('add_id,date_added');
					$this->db->order_by('id','ASC');
					$ing_additives = $this->db->get_where('products_additives',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id']))->result_array();
					if (!empty($ing_additives)) {
						foreach($ing_additives as $add_key => $add_val){

							$this->db->select('add_name_dch');
							$ing_additives_name = $this->db->get_where('additives',array('add_id'=>$add_val['add_id']))->result_array();
							$ing_additives_name = $ing_additives_name[0]['add_name_dch'];

							
							$this->fdb->select('ing_id,ing_name_dch,ing_name_fr,ing_name');
							$ing_name_d = $this->fdb->get_where('ingredients',array('ing_name_dch'=>$ing_additives_name))->result_array();

							$set_vet = array(
									'product_id'=>$obs_products_val['obs_pro_id'],
									'kp_id' => $obs_products_val['fdd_pro_id'],
									'ki_id' => $ing_name_d[0]['ing_id'],
									'prefix' => '',
									'ki_name' => $ing_name_d[0][$lang],
									'display_order' => $disp_ord,
									'kp_display_order' => $kp_display_order,
									'date_added' => $add_val['date_added'],
									'is_obs_ing' => 0,
									'have_all_id' => 0
							) ;

							$this->db->insert('products_ingredients',$set_vet);

							
							$this->fdb->select('ing_id,ing_name_dch,ing_name_fr,ing_name');
							$ing_name_col = $this->fdb->get_where('ingredients',array('ing_name_dch'=>':'))->result_array();
							

							$disp_ord++;
							$set_vet = array(
									'product_id'=>$obs_products_val['obs_pro_id'],
									'kp_id' => $obs_products_val['fdd_pro_id'],
									'ki_id' => $ing_name_col[0]['ing_id'],
									'prefix' => '',
									'ki_name' => $ing_name_col[0][$lang],
									'display_order' => $disp_ord,
									'kp_display_order' => $kp_display_order,
									'date_added' => $add_val['date_added'],
									'is_obs_ing' => 0,
									'have_all_id' => 0
							) ;
							$this->db->insert('products_ingredients',$set_vet);

							$set_vet = array();
							$disp_ord++;
							$this->db->select('product_id,kp_id,ki_id,ki_name,date_added,have_all_id');
							$this->db->order_by('display_order','ASC');
							$ing_additives = $this->db->get_where('products_additives',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id'],'add_id'=>$add_val['add_id']))->result_array();
							if(!empty($ing_additives)){
								foreach($ing_additives as $key => $val)
								{
									$set_vet = array(
											'product_id'=>$val['product_id'],
											'kp_id' => $val['kp_id'],
											'ki_id' => $val['ki_id'],
											'prefix' => '',
											'ki_name' => $val['ki_name'],
											'display_order' => $disp_ord,
											'kp_display_order' => $kp_display_order,
											'date_added' => $val['date_added'],
											'is_obs_ing' => 0,
											'have_all_id' => $val['have_all_id']
									) ;
									$this->db->insert('products_ingredients',$set_vet);
									$disp_ord++;
								}

							}

						}
						$this->db->delete('products_additives',array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id']));
					}
					$this->db->where(array('product_id'=>$obs_products_val['obs_pro_id'],'kp_id'=>$obs_products_val['fdd_pro_id'],'ki_id'=>0,'ki_name'=>')'));
					$this->db->update('products_ingredients',array('display_order'=> $disp_ord ));

				}
			}
		}
	}


	function check_fixed_update(){
		
		$this->fdb->select('p_id');
		$this->fdb->order_by('p_id','ASC');
		$es = $this->fdb->get_where('products',array('review&fixed'=>1))->result_array();
	
		if(!empty($es)){
			foreach($es as $k=>$v)
			{
				$this->db->where(array('fdd_pro_id'=>$v['p_id'],'is_obs_product'=>0));
				$this->db->update('fdd_pro_quantity',array('fixed'=>1));
			}
		}
	}

	function check_already_fixed_update(){
		$this->db->select('fdd_pro_id');
		$this->db->order_by('fdd_pro_id','ASC');
		$es = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0,'fixed'=>1))->result_array();
		if(!empty($es)){
			
			foreach($es as $k=>$v)
			{
				$this->fdb->select('p_id');
				$this->fdb->where(array('p_id'=>$v['fdd_pro_id'],'review&fixed'=>0));
				$res = $this->fdb->get('products')->result_array();
				if(!empty($res)){
					print_r($res);die;
				}
			}
		}
	}

	function insert_ing(){
		$this->db->select('ki_id');
		$this->db->where('ki_id !=',0);
		$this->db->order_by('ki_id','ASC');
		$this->db->distinct('ki_id');
		$result = $this->db->get('products_ingredients')->result_array();
		if(!empty($result)){
			
			foreach ($result as $key => $value) {
				
				$this->fdb->select('ing_name,ing_name_dch,ing_name_fr');
				$this->fdb->where(array('ing_id'=>$value['ki_id']));
				$result_arr = $this->fdb->get('ingredients')->result_array();
				if(!empty($result_arr)){
					$result1 = $this->db->get_where('ingredients',array('ki_id'=>$value['ki_id']))->result_array();
					if(empty($result1)){
						$ing_insert_array = array(
								'ki_id'=>$value['ki_id'],
								'ki_name'=>$result_arr[0]['ing_name'],
								'ki_name_dch'=>$result_arr[0]['ing_name_dch'],
								'ki_name_fr'=>$result_arr[0]['ing_name_fr']
						);
						$this->db->insert('ingredients',$ing_insert_array);
					}
				}
			}
		}
	}

	function ing_similar(){
		$arr = array();
		$ing = $this->db->get('ingredients')->result_array();
		if (!empty($ing)) {
			
			foreach ($ing as $key => $value) {
				$flag = false;
				$ing_same = $this->fdb->get_where('ingredients',array('ing_id'=>$value['ki_id']))->row_array();
				if (!empty($ing_name)) {
					if ($value['ki_name'] != $ing_same['ing_name']) {
						$flag = true;
					}
					if ($value['ki_name_dch'] != $ing_same['ing_name_dch']) {
						$flag = true;
					}
					if ($value['ki_name_fr'] != $ing_same['ing_name_fr']) {
						$flag = true;
					}
				}
				if ($flag) {
					$arr [] = $value['ki_id'];
				}
			}
			
		}
		echo "<pre>";
		print_r($arr);die;
	}

	function check_ing_brancket(){
		$this->db->select('id,product_id,kp_id,display_order');
		$this->db->where(array('ki_name'=>'(','is_obs_ing'=>0,'display_order'=>1));
		$this->db->order_by('product_id','ASC');
		$result = $this->db->get('products_ingredients')->result_array();
		if(!empty($result)){
			foreach ($result as $key => $value) {

				$this->db->select('id,ki_name');
				$this->db->where(array('is_obs_ing'=>0,'product_id'=>$value['product_id'],'kp_id'=>$value['kp_id']));
				$this->db->order_by('id','ASC');
				$result1 = $this->db->get('products_ingredients')->result_array();

				$disp_order = 1;
				foreach ($result1 as $key1 => $value1) {
					if($value1['ki_name'] != ')')
					{
						$this->db->where(array('is_obs_ing'=>0,'product_id'=>$value['product_id'],'kp_id'=>$value['kp_id'],'ki_name'=>$value1['ki_name'],'id'=>$value1['id']));
						$this->db->update('products_ingredients',array('display_order'=>$disp_order));
						$disp_order++;
					}
					if($value1['ki_name'] == ')'){
						$this->db->where(array('is_obs_ing'=>0,'product_id'=>$value['product_id'],'kp_id'=>$value['kp_id'],'ki_name'=>$value1['ki_name'],'id'=>$value1['id']));
						$this->db->update('products_ingredients',array('display_order'=>count($result1)));
					}
				}
			}
		}
	}

	function get_ing_s(){
		$arr = array();
		$this->db->select('id,product_id,kp_id,ki_name');
		$this->db->order_by('kp_id','ASC');

		$es = $this->db->get_where('products_ingredients',array('ki_id'=>0,'is_obs_ing'=>0,'display_order'=>0))->result_array();
		foreach ($es as $key => $value) {
			$es1 = $this->db->get_where('products_ingredients',array('ki_id'=>0,'is_obs_ing'=>0,'display_order'=>2,'ki_name'=>'(','product_id'=>$value['product_id']))->result_array();
			if(!empty($es1))
			{
				$this->db->where(array('ki_id'=>0,'is_obs_ing'=>0,'product_id'=>$value['product_id'],'kp_id'=>$value['kp_id'],'ki_name'=>$value['ki_name']));
				$this->db->update('products_ingredients',array('display_order'=>1));
			}
		}
	}

	function update_is_obs_ing(){
		$this->db->select('id');
		$this->db->order_by('id','ASC');
		$es = $this->db->get_where('products_ingredients',array('is_obs_ing'=>1,'display_order'=>0))->result_array();
		foreach ($es as $key => $value) {
			$this->db->where(array('ki_id'=>0,'is_obs_ing'=>1,'id'=>$value['id']));
			$this->db->update('products_ingredients',array('display_order'=>1));
		}
	}

	function find_unapprovefdd_product( ){
		ini_set('memory_limit', '-1');
		$this->db->select( 'obs_pro_id,fdd_pro_id' ); 
		$fdd_pro_quantity = $this->db->get_where( 'fdd_pro_quantity',array('is_obs_product'=>0) )->result_array( );
		if( !empty( $fdd_pro_quantity )  ){
			$unapprove_products = array( );
			
			foreach ( $fdd_pro_quantity as $key => $product ) {
				$this->fdb->select( 'p_id' );
				$products = $this->fdb->get_where( 'products' ,
					array( 'approval_status' =>0 ,'p_id' => $product[ 'fdd_pro_id'],'product_type'=> '0' ))->result_array( );
				if( !empty( $products ) ){
					array_push( $unapprove_products ,  $product );
				}
			}

			
			
			if( !empty( $unapprove_products ) ){
				foreach ( $unapprove_products as $key => $u_product ) {
					echo $key;
					//INGREDIENTs
					$this->db->where( array( 'product_id' => $u_product[ 'obs_pro_id' ] , 'kp_id'=>$u_product[ 'fdd_pro_id' ], 'is_obs_ing' => '0' ) );
					$this->db->where( 'ki_id !=', '0' );
					$this->db->delete( 'products_ingredients' );
					// DELETE FROM `products_ingredients` WHERE `product_id` = '289' AND `is_obs_ing` = '0' AND `ki_id` != '0'
					
					// ALLERGEN
					$this->db->where(array('product_id'=>$u_product[ 'obs_pro_id' ],'kp_id'=>$u_product[ 'fdd_pro_id' ]));
					$this->db->delete( 'products_allergence' );
					// DELETE FROM `products_allergence` WHERE `kp_id` = '289'

					// TRACES
					$this->db->where(array('product_id'=>$u_product[ 'obs_pro_id' ],'kp_id'=>$u_product[ 'fdd_pro_id' ]));
					$this->db->delete( 'products_traces' );
					// DELETE FROM `products_traces` WHERE `kp_id` = '289'
					
					
					// SUB ALLERGENCE
					$this->db->where(array('product_id'=>$u_product[ 'obs_pro_id' ],'kp_id'=>$u_product[ 'fdd_pro_id' ]));
					$this->db->delete( 'product_sub_allergence' );
					//DELETE FROM `product_sub_allergence` WHERE `kp_id` = '289'DELETE FROM `products_traces` WHERE `kp_id` = '26017'
				}
			}
		}
		echo "done";
	}

	function replace_fdd_product($old_id = 1474, $new_id = 14125, $company_id = 4069, $language_id = 2, $enbr_status = 1){
		$this->db->select('obs_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$obs_produts = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.fdd_pro_id'=>$old_id,'products.company_id'=>$company_id))->result_array();
		// echo "<pre>";
		// print_r ($obs_produts);die;
		// echo "</pre>";
		if(!empty($obs_produts))
		{
			foreach ($obs_produts as $key => $value) {

				$this->db->where(array('fdd_pro_id'=>$old_id,'is_obs_product'=>0,'obs_pro_id'=>$value['obs_pro_id']));
				$this->db->update('fdd_pro_quantity',array('fdd_pro_id'=>$new_id));


				$this->db->select('kp_display_order');
				$kp_display_order = $this->db->get_where('products_ingredients',array('product_id'=>$value['obs_pro_id'],'kp_id'=>$old_id,'is_obs_ing'=>0))->result_array();
				$kp_display_order = $kp_display_order[0]['kp_display_order'];


				$this->db->where(array('product_id'=>$value['obs_pro_id'],'kp_id'=>$old_id,'is_obs_ing'=>0));
				$this->db->delete('products_ingredients');

				$this->db->where(array('product_id'=>$value['obs_pro_id'],'kp_id'=>$old_id));
				$this->db->delete('products_allergence');

				$this->db->where(array('product_id'=>$value['obs_pro_id'],'kp_id'=>$old_id));
				$this->db->delete('product_sub_allergence');

				$this->db->where(array('product_id'=>$value['obs_pro_id'],'kp_id'=>$old_id));
				$this->db->delete('products_traces');

				

				$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch' );
				$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $new_id ) )->row_array();

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
				$products_ing = $this->fdb->get_where( 'prod_ingredients', array( 'p_id' => $new_id ) )->result_array();
				$insert_aray = array();
				$disp_order = 1;
				$insert_aray[] = array(
						'product_id'		=> $value['obs_pro_id'],
						'kp_id'				=> $new_id,
						'ki_id'				=> 0,
						'ki_name'			=> $short_name,
						'display_order'		=> $disp_order,
						'kp_display_order'	=> $kp_display_order,
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
						'product_id'		=> $value['obs_pro_id'],
						'kp_id'				=> $new_id,
						'ki_id'				=> 0,
						'ki_name'			=> '(',
						'display_order'		=> $disp_order+1,
						'kp_display_order'	=> $kp_display_order,
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
							'product_id'		=> $value['obs_pro_id'],
							'kp_id'				=> $new_id,
							'ki_id'				=> $ki_id,
							'ki_name'			=> $display_name,
							'display_order'		=> $disp_order,
							'kp_display_order'	=> $kp_display_order,
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

						$this->db->select('ki_id');
						$res = $this->db->get_where('ingredients', array('ki_id' => $ki_id) )->row_array();

						if( empty($res) ){
							if( $enbr_status == 1 ){
								if( !empty( $enbr_result ) ){
									$object = array(
												'ki_id' 	 => $ki_id,
												'ki_name'	 => $display_name,
												'ki_name_fr' => $display_name,
												'ki_name_dch'=> $display_name
											);
								}
								else{
									$object = array(
											'ki_id' 	 => $ki_id,
											'ki_name'	 => $ing_name,
											'ki_name_fr' => $ing_name_fr,
											'ki_name_dch'=> $ing_name_dch
										);
								}
							}
							if( $enbr_status == 2 ){
								$object = array(
											'ki_id' 	 => $ki_id,
											'ki_name'	 => $ing_name,
											'ki_name_fr' => $ing_name_fr,
											'ki_name_dch'=> $ing_name_dch
										);
							}
							$this->db->insert('ingredients', $object);
						}
					}
				}
				$disp_order ++;
				$insert_aray[] = array(
					'product_id'		=> $value['obs_pro_id'],
					'kp_id'				=> $new_id,
					'ki_id'				=> 0,
					'ki_name'			=> ')',
					'display_order'		=> $disp_order,
					'kp_display_order'	=> $kp_display_order,
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

				
				$this->db->insert_batch('products_ingredients', $insert_aray);
				

				$this->fdb->select('*');
				$this->fdb->order_by('order','ASC');
				$products_aller = $this->fdb->get_where( 'prod_allergence', array( 'p_id' => $new_id ) )->result_array();
				$insert_aray = array();
				$disp_order = 1;
				if(!empty($products_aller)){
					foreach($products_aller as $products_ing_key => $products_ing_val)
					{
						$this->fdb->select('all_id, all_name, all_name_fr, all_name_dch');
						$this->fdb->where('all_id',$products_ing_val['a_id']);
						$allergence_info_to_add = $this->fdb->get('allergence')->result();
						$all_index 		= 'all_name'.$ing_var;
						$display_name 	= $allergence_info_to_add[0]->$all_index;

						$insert_aray[] = array(
							'product_id' => $value['obs_pro_id'],
							'kp_id' => $new_id,
							'ka_id' => $allergence_info_to_add[0]->all_id,
							'ka_name' => $allergence_info_to_add[0]->$all_index,
							'display_order' => $disp_order,
							'date_added' => date('Y-m-d H:i:s')
						);
						$disp_order ++;
					}
					
					$this->db->insert_batch('products_allergence', $insert_aray);
					
				}

				

				$this->fdb->select('*');
				$this->fdb->order_by('order','ASC');
				$products_aller = $this->fdb->get_where( 'prod_sub_allergence', array( 'p_id' => $new_id ) )->result_array();
				$insert_aray = array();
				$disp_order = 1;
				if(!empty($products_aller)){
					foreach($products_aller as $products_ing_key => $products_ing_val)
					{
						$this->fdb->select('all_id, all_name, all_name_fr, all_name_dch,parent_all_id');
						$this->fdb->where('all_id',$products_ing_val['a_id']);
						$allergence_info_to_add = $this->fdb->get('sub_allergence')->result();
						$all_index 		= 'all_name'.$ing_var;
						$display_name 	= $allergence_info_to_add[0]->$all_index;

						$insert_aray[] = array(
							'product_id' => $value['obs_pro_id'],
							'kp_id' => $new_id,
							'parent_ka_id'=>$allergence_info_to_add[0]->parent_all_id,
							'sub_ka_id' => $allergence_info_to_add[0]->all_id,
							'sub_ka_name' => $allergence_info_to_add[0]-> $all_index,
							'display_order' => $disp_order,
							'date_added' => date('Y-m-d H:i:s')
						);
						$disp_order ++;
					}
					
					$this->db->insert_batch('product_sub_allergence', $insert_aray);
					
				}

				

				$this->fdb->select('*');
				$this->fdb->order_by('order','ASC');
				$products_aller = $this->fdb->get_where( 'prod_traces', array( 'p_id' => $new_id ) )->result_array();
				$insert_aray = array();
				$disp_order = 1;
				if(!empty($products_aller)){
					foreach($products_aller as $products_ing_key => $products_ing_val)
					{
						$this->fdb->select('t_id, t_name, t_name_fr, t_name_dch');
						$this->fdb->where('t_id',$products_ing_val['t_id']);
						$allergence_info_to_add = $this->fdb->get('traces')->result();
						$all_index 		= 't_name'.$ing_var;
						$display_name 	= $allergence_info_to_add[0]->$all_index;

						$insert_aray[] = array(
							'product_id' => $value['obs_pro_id'],
							'kp_id' => $new_id,
							'kt_id' => $allergence_info_to_add[0]->t_id,
							'kt_name' => addslashes($allergence_info_to_add[0]-> $all_index),
							'display_order' => $disp_order,
							'date_added' => date('Y-m-d H:i:s')
						);
						$disp_order ++;
					}
					
					$this->db->insert_batch('products_traces', $insert_aray);
					
				}
				echo $key;
			}
		}
		echo "done";
	}

	private function get_e_current_nbr1($id = 0,$enbr_setting = 0,$ing_var = '_dch'){
		
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
		//
		return array();
	}


	function fdd_used_in_obs(){
		
		$this->fdb->select('p_id');
		$res = $this->fdb->get_where('products',array('p_s_id'=>140))->result_array();
		
		if(!empty($res))
		{
			foreach ($res as $key => $value) {
				$pro = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_id'=>$value['p_id']))->result_array();
				if(empty($pro))
				{
					echo $value['p_id'];
					echo "<br>";
				}
			}
		}
	}

	function empty_category_stataus($a=0,$b=0){
		$arr = array();
		$this->db->select('id');
		$this->db->order_by('id','ASC');
		$this->db->limit($a, $b);
		$this->db->where('status','1');
		$cat_id = $this->db->get('categories')->result_array();
		//print_r($cat_id);die;
		if(!empty($cat_id))
		{
			foreach ($cat_id as $key => $value) {
				$this->db->select('id');
				$product_fetch = $this->db->get_where('products',array('categories_id'=>7181))->row_array();
				if(empty($product_fetch))
				{
					$this->db->where(array('id'=>$value['id']));
					$this->db->update('categories',array('status'=>'0'));
				}
			}
		}
	}

	function empty_subcategory_stataus($a=0,$b=0){
		$arr = array();
		$this->db->select('id');
		$this->db->order_by('id','ASC');
		$this->db->limit($a, $b);
		$this->db->where('status','1');
		$cat_id = $this->db->get('subcategories')->result_array();
		if(!empty($cat_id))
		{
			foreach ($cat_id as $key => $value) {
				$this->db->select('id');
				$product_fetch = $this->db->get_where('products',array('subcategories_id'=>$value['id']))->row_array();
				if(empty($product_fetch))
				{
					$this->db->where(array('id'=>$value['id']));
					$this->db->update('subcategories',array('status'=>'0'));
				}
			}
		}
	}

	function update_pws_date(){
		$this->db->select('products.procreated,products.id');
		$this->db->join('products_pending','products_pending.product_id = products.id');
		$this->db->where(array('products_pending.date'=>'0000-00-00 00:00:00'));
		$query = $this->db->get('products')->result_array();
		if(!empty($query))
		{
			foreach ($query as $key => $value) {
				$this->db->where(array('product_id'=>$value['id']));
				$this->db->update('products_pending',array('date'=>$value['procreated'].' '.'00:00:00'));
			}
		}
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
// 						
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
// 							
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

	/* 
	 * function to get all products ,whose review fixed changed in fdd.be but not in fdd.net
	*/
	function get_review_n_fixed_prods2(){
		
		$this->fdb->select( 'p_id, review&fixed' );
		$this->fdb->where( 'review&fixed', '1' );
		$query = $this->fdb->get( 'products' )->result_array();
		
		$res = array();
		foreach ($query as $key => $value) {
			$this->db->select( 'id, obs_pro_id, fdd_pro_id, fixed' );
			$this->db->where( array( 'fdd_pro_id' => $value['p_id'], 'fixed' => '0' ,'is_obs_product' => 0) );
			$result = $this->db->get( 'fdd_pro_quantity' )->result_array();
			// print_r($result);die;
			$result = array_unique( array_column($result, 'fdd_pro_id' ) );
			if( !empty($result) ){
				$res[] = $result;
				$this->db->where( 'fdd_pro_id', $result[0] );
				$this->db->update( 'fdd_pro_quantity', array( 'fixed' => $value['review&fixed'] ) );
			}
		}
		echo count($res); die;
	}


	/*
	* function to change status of cat of light packages to 1
	*/
	function status_of_light(){
		$this->db->select( 'categories.id' );
		$this->db->join( 'company','categories.company_id = company.id' );
		$this->db->where( array( 'company.ac_type_id' => '7' ) );
		$query = $this->db->get_where( 'categories', array( 'categories.status'=> '0' ) )->result_array();
		foreach ($query as $key => $value) {
			$this->db->where( 'categories.id', $value['id'] );
			$this->db->update( 'categories', array( 'categories.status' => '1' ) );
		}
	}

	/*
	* function to change status of subcat of light packages to 1
	*/
	function status_of_light_sub(){
		$this->db->select( 'subcategories.id' );
		$this->db->join( 'categories','categories.id = subcategories.categories_id' );
		$this->db->join( 'company','company.id = categories.company_id' );
		$this->db->where( array( 'company.ac_type_id' => '7' ) );
		$query = $this->db->get_where( 'subcategories', array( 'subcategories.status'=> '0' ) )->result_array();
		foreach ($query as $key => $value) {
			$this->db->where( 'subcategories.id', $value['id'] );
			$this->db->update( 'subcategories', array( 'subcategories.status' => '1' ) );
		}
	}


	/* 
	 * function to get all products ,whose review fixed changed in fdd.be but not in fdd.net
	*/
	function get_review_n_fixed_prods1(){
		
		$this->fdb->select( 'p_id, review&fixed' );
		$this->fdb->where( 'review&fixed', '1' );
		$query = $this->fdb->get( 'products' )->result_array();
		
		$res = array();
		foreach ($query as $key => $value) {
			$this->db->select( 'id, obs_pro_id, fdd_pro_id, fixed' );
			$this->db->where( array( 'fdd_pro_id' => $value['p_id'], 'fixed' => '0','is_obs_product'=>0 ) );
			$result = $this->db->get( 'fdd_pro_quantity' )->result_array();
			$result = array_values( array_unique( array_column($result, 'fdd_pro_id' ) ) );
			if( !empty($result) ){
				$res = array_merge( $res, $result );
			}
		}

		foreach ($res as $k1 => $product_id) {
			$ing_var = '_dch';

			$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
			$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
			$this->db->join('general_settings','general_settings.company_id = products.company_id');
			$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0));
			$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

			$this->fdb->select( 'p_short_name, p_short_name_dch, p_short_name_fr' );
			$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

			$this->fdb->select( '*' );
			$prod_ing = $this->fdb->get_where( 'prod_ingredients', array( 'p_id' => $product_id ) )->result_array();

			foreach ($cp_prod_ids as $cp_prod_id){
				if($cp_prod_id->language_id == '3'){
					$ing_var 	= '_fr';
					$short_name = $res_short['p_short_name_fr'];
				}elseif($cp_prod_id->language_id == '2'){
					$ing_var 	= '_dch';
					$short_name = $res_short['p_short_name_dch'];
				}
				else{
					$ing_var 	= '';
					$short_name = $res_short['p_short_name'];
				}

				$this->db->select( 'fdd_pro_id' );
				$this->db->order_by( 'quantity', 'desc' );
				$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
				$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
				$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
				$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
				$kp_order 	= array_search( $product_id , $product_ids) + 1;

				$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$product_id));
				$this->db->delete('products_ingredients');
				
				if(!empty($prod_ing)){
					$insert_aray = array();
					$disp_order = 1;
					$insert_aray[] = array(
							'product_id'		=> $cp_prod_id->product_id,
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
							'product_id'		=> $cp_prod_id->product_id,
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

					foreach( $prod_ing as $key => $val2 ){
						$disp_order 	 = $key+3;

						$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
						$this->fdb->where('ing_id', $val2['i_id']);
						$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
						$ing_index 		= 'ing_name'.$ing_var;
						$display_name 	= $ingredient_info_to_add[0]->$ing_index;
						$ing_name 		= $ingredient_info_to_add[0]->ing_name;
						$ing_name_fr 	= $ingredient_info_to_add[0]->ing_name_fr;
						$ing_name_dch 	= $ingredient_info_to_add[0]->ing_name_dch;

						$ki_id = $ingredient_info_to_add[0]->ing_id;
						$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

						if(!empty($enbr_result)){
							$ki_id = $enbr_result['ki_id'];
							$display_name = $enbr_result['ki_name'];
						}

						$insert_aray[] = array(
								'product_id'		=> $cp_prod_id->product_id,
								'kp_id'				=> $product_id,
								'ki_id'				=> $ki_id,
								'ki_name'			=> $display_name,
								'display_order'		=> $disp_order,
								'kp_display_order'	=> $kp_order,
								'date_added'		=> date('Y-m-d H:i:s'),
								'is_obs_ing'		=> 0,
								'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
								'aller_type'		=> $val2['aller_type'],
								'aller_type_fr'		=> $val2['aller_type_fr'],
								'aller_type_dch'	=> $val2['aller_type_dch'],
								'allergence'		=> $val2['allergence'],
								'allergence_fr'		=> $val2['allergence_fr'],
								'allergence_dch'	=> $val2['allergence_dch'],
								'sub_allergence'	=> $val2['sub_allergence'],
								'sub_allergence_fr'	=> $val2['sub_allergence_fr'],
								'sub_allergence_dch'=> $val2['sub_allergence_dch']
						);

						$this->db->select('ki_id');
						$res = $this->db->get_where('ingredients', array('ki_id' => $ki_id) )->row_array();

						if( empty($res) ){
							if( $cp_prod_id->enbr_status == 1 ){
								if( !empty( $enbr_result ) ){
									$object = array(
												'ki_id' 	 => $ki_id,
												'ki_name'	 => $display_name,
												'ki_name_fr' => $display_name,
												'ki_name_dch'=> $display_name
											);
								}
								else{
									$object = array(
											'ki_id' 	 => $ki_id,
											'ki_name'	 => $ing_name,
											'ki_name_fr' => $ing_name_fr,
											'ki_name_dch'=> $ing_name_dch
										);
								}
							}
							if( $cp_prod_id->enbr_status == 2 ){
								$object = array(
											'ki_id' 	 => $ki_id,
											'ki_name'	 => $ing_name,
											'ki_name_fr' => $ing_name_fr,
											'ki_name_dch'=> $ing_name_dch
										);
							}
							$this->db->insert('ingredients', $object);
						}
						$disp_order++;
					}

					$insert_aray[] = array(
							'product_id'		=> $cp_prod_id->product_id,
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
					$this->db->insert_batch('products_ingredients', $insert_aray);
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

	private function get_e_current_nbr2($id = 0,$enbr_setting = 0,$ing_var = '_dch'){
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

	/*
	* function to update prod name to shortname
	*/
	function name_to_short(){
		ini_set('memory_limit', '20000M');
		set_time_limit(0);
		ini_set('max_execution_time', 0);

		$this->db->select( 'id' );
		$companies = $this->db->get( 'company' )->result_array();

		for( $i = 3737; $i < 3738 ; $i++ ) {
			$this->db->select( 'products_ingredients.id, products_ingredients.product_id, products_ingredients.kp_id, products_ingredients.ki_name, general_settings.language_id' );
			$this->db->join( 'products', 'products_ingredients.product_id = products.id' );
			$this->db->join( 'company', 'products.company_id = company.id' );
			$this->db->join( 'general_settings', 'company.id = general_settings.company_id' );
			$this->db->where( array( 'ki_id' => '0', 'ki_name !=' => '(', 'is_obs_ing' => '0', 'company.id' => $companies[$i]['id'] ) );
			$this->db->where( 'ki_name !=', ')');
			$query = $this->db->get( 'products_ingredients' )->result_array();
			
			$res = array();
			$no_res = array();

			
			foreach ($query as $key => $value) {
				if( $value['language_id'] == '1' ){
					$shortname = 'p_short_name';
				}
				if( $value['language_id'] == '2' ){
					$shortname = 'p_short_name_dch';
				}
				if( $value['language_id'] == '3' ){
					$shortname = 'p_short_name_fr';
				}
				$this->fdb->select( 'p_id, '.$shortname );
				$result = $this->fdb->get_where( 'products', array( 'p_id' => $value['kp_id'], $shortname.'!=' => $value['ki_name'] ) )->result_array();
				if( !empty($result) ){
					if( $result[0][$shortname] != '' ){
						$result[0]['obs_pro_id'] = $value['product_id'];
						$result[0]['language_id']= $value['language_id'];	
						$res = array_merge( $result, $res );
					}
				}
			}
			

			foreach ($res as $k1 => $v1) {

				if( $v1['language_id'] == '1' ){
					$shortname = 'p_short_name';
				}
				if( $v1['language_id'] == '2' ){
					$shortname = 'p_short_name_dch';
				}
				if( $v1['language_id'] == '3' ){
					$shortname = 'p_short_name_fr';
				}

				$fp = fopen(dirname(__FILE__)."/log_ft2.txt","a");
				fwrite($fp,"obs_prod_id: ".$v1['obs_pro_id'].'--'."fdd_pro_id:".$v1['p_id'].'--'."name:".$v1[$shortname]."--"."comp_id:".$companies[$i]['id']."\n");
				fclose($fp);
				
				$this->db->where( array( 'kp_id' => $v1['p_id'], 'product_id' => $v1['obs_pro_id'], 'ki_id' => '0', 'ki_name !=' => '(', 'is_obs_ing' => '0' ) );
				$this->db->where( 'ki_name !=', ')');
				$this->db->update( 'products_ingredients', array( 'ki_name' => $v1[$shortname] ) );
			}
		}
	}

	function get_Company_with_no_desk_img( ){
	

		$this->db->select( 'id,obsdesk_logo' );
		$this->db->where( 'approved', '1' );
		$this->db->where( 'obsdesk_status', '1' );
		$company_data = $this->db->get_where( 'company', array( 'company.obsdesk_logo !=' => '' ) )->result_array( );
		if( !empty( $company_data ) ){
			$final_array = array( );
			foreach ( $company_data as $key => $data ) {
				if( !file_exists( FCPATH.'assets/company-logos/'.$data[ 'obsdesk_logo' ] ) ){
					array_push( $final_array,  $data  );
				}
			}
			echo "<pre>";
			print_r( $final_array );die;
		}
	}
	function get_comp_detail( ){
		
			$this->db->select('*');
			$result = $this->db->get_where( 'clients', array( 'company_id' => '1523' ) )->result_array();
			
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Client details') );
			
			$counter = 1;
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, 'Email')->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, 'Password')->getStyle('B'.$counter)->getFont()->setBold(true);
			
			if(!empty($result))
			{
				foreach($result as $result_row)
				{ 
					if( $result_row['fax_c'] != '' ){
						echo $result_row['fax_c'].'<br>';
						
					}else{
						echo'--<br>';
					}
					// $this->excel->getActiveSheet()->setCellValue('A'.$counter, $result_row['email_c'] );
					// $this->excel->getActiveSheet()->setCellValue('B'.$counter, $result_row['password_c'] );
				}
			}die;
			//$filename = 'Brebels-Truyen-clients';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
		
	} 
	function update_general_setting( ){
			$this->db->select( 'id, email' );
			$company = $this->db->get( 'company' )->result_array();

			if( !empty( $company ) ){
				foreach ( $company as $key => $value) {
					$this->db->select( 'id' );
					$general_settings = $this->db->get_where( 'general_settings', array( 'company_id' => $value[ 'id' ] ) )->result_array();
					
					if( empty( $general_settings ) ){
						echo implode( ' ' ,  $value ).'<br>';
						$insert_arr = array( 'company_id' =>  $value[ 'id' ] , 'language_id' => '2' , 'emailid' => $value[ 'email' ] );
						$this->db->insert('general_settings', $insert_arr );
						// INSERT INTO `general_settings` (`company_id`, `language_id`, `emailid`) VALUES ('870', '2', 'info@patisserie-delva.be')
					}
				}
			}
			echo '<h1>END</h1>';
		}


	public function update_fav() {
		$this->db->distinct();
		$this->db->select( 'company_id' );
		$this->db->where( 'is_favourate', '1' );
		$result = $this->db->get( 'fdd_pro_fav' )->result_array();
		foreach ( $result as $key => $value ) {
			$this->db->select( 'fdd_pro_id' );
			$this->db->where( 'is_favourate', '1' );
			$this->db->where( 'company_id', $value[ 'company_id' ] );
			$fdd_pro_ids = $this->db->get( 'fdd_pro_fav' )->result_array();


			if( !empty( $fdd_pro_ids ) ) {
				$fdd_pro_ids = array_column( $fdd_pro_ids, 'fdd_pro_id' );
				$this->db->insert( 'fdd_pro_fav2', array( 'company_id' => $value[ 'company_id' ], 'fdd_pro_id' => json_encode( $fdd_pro_ids ), 'date_added' => date( 'Y-m-d H:i:s' ) ) );
			}
		}
	}

	public function fav_not(){
		$arr = array();
		$this->db->distinct();
		$this->db->select( 'company_id' );
		$result = $this->db->get( 'fdd_pro_fav1' )->result_array();
		foreach ( $result as $key => $value ) {
			$result1 = $this->db->get_where( 'fdd_pro_fav',array('company_id'=>$value['company_id']) )->result_array();
			if( !empty( $result1 ) ) {
				$arr[] = $value['company_id'];
			}
		}
		echo "<pre>";
		print_r($arr);die;
	}

	function update_french_comp_allergen_name(){
		$this->db->select( 'company.id as comp_id, products_traces.id, products_traces.kt_id, products_traces.kt_name' );
		$this->db->join( 'products_traces', 'products.id = products_traces.product_id' );
		$this->db->join( 'company', 'company.id = products.company_id' );
		$this->db->join( 'general_settings', 'general_settings.company_id = company.id' );
		$this->db->where( array( 'general_settings.language_id' => '3', 'products_traces.kt_id !=' => '1' ) );
		$this->db->where( 'products_traces.kt_id !=', '6' );
		$query = $this->db->get( 'products' )->result_array();
		echo '<pre>';
		print_r($query); die;

		$this->db->select( 't_id, t_name_fr' );
		$aller = $this->db->get( 'traces' )->result_array();
		$aller = array_column($aller, 't_name_fr', 't_id' );
		// echo '<pre>';
		// print_r($aller); die;
		foreach ($query as $key => $value) {
			// echo '<pre>';
			// print_r($value); die;
			if( $value['kt_name'] != $aller[$value['kt_id']] ){
				$this->db->where( 'products_traces.id', $value['id'] );
				$this->db->update( 'products_traces', array( 'kt_name' => $aller[$value['kt_id']] ) );
			}
			// die;
		}
	}

	function script_gs1() {
   		$this->db->distinct( 'fdd_pro_id' );
   		$this->db->select( 'fdd_pro_id' );
   		$this->db->where( 'is_obs_product', 0 );
   		$fdd_pro_ids = $this->db->get( 'fdd_pro_quantity' )->result_array();
   		$fdd_pro_ids = array_column( $fdd_pro_ids, 'fdd_pro_id' );
   		

   		$this->fdb->select( 'p_id' );
   		$this->fdb->where_in( 'p_id', $fdd_pro_ids );
   		$this->fdb->where( array('product_type'=> '1','approval_status'=>0 ));
   		$new_fdd_pro_ids = $this->fdb->get( 'products' )->result_array();

   		$new_fdd_pro_ids = array_column( $new_fdd_pro_ids, 'p_id' );

   		$this->db->distinct();
   		$this->db->select('fdd_pro_quantity.fdd_pro_id as gs1_pid, products.company_id');
   		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
   		$this->db->where( 'is_obs_product', 0 );
   		$this->db->where_in( 'fdd_pro_id', $new_fdd_pro_ids );
   		$comp_fdd_pro_ids = $this->db->get( 'fdd_pro_quantity' )->result_array();
   		
   		$this->db->distinct();
   		$this->db->select( 'gs1_pid, company_id' );
   		$gs1_pids = $this->db->get( 'request_gs1' )->result_array();

   		
		function my_serialize(&$arr,$pos){
			$arr = serialize($arr);
		}

		function my_unserialize(&$arr,$pos){
		  	$arr = unserialize($arr);
		}
		array_walk( $gs1_pids, 'my_serialize' );
		array_walk( $comp_fdd_pro_ids, 'my_serialize' );

		$arr1 = array_values(array_unique(array_diff( $comp_fdd_pro_ids , $gs1_pids)));
   		array_walk( $arr1, 'my_unserialize' );

   		$gs1_to_add = array_column( $arr1, 'gs1_pid' );
   		$intersect = array();
   		$intersect1 = array();
   		if (!empty($gs1_to_add)) {
   			$this->fdb->select( 'p_id' );
	   		$this->fdb->where_in( 'p_id', $gs1_to_add);
	   		$this->fdb->where( 'gs1_response !=', '' );
	   		$gs1_ids_to_add = $this->fdb->get( 'gs1_products' )->result_array();
	   		$intersect = array_column( $gs1_ids_to_add, 'p_id' );

	   		$this->fdb->select( 'p_id' );
	   		$this->fdb->where_in( 'p_id', $gs1_to_add);
	   		$this->fdb->where( 'gs1_response', '' );
	   		$gs1_ids_to_add1 = $this->fdb->get( 'gs1_products' )->result_array();
	   		$intersect1 = array_column( $gs1_ids_to_add1, 'p_id' );
	   	}
   		
   		foreach ($intersect as $key => $value) {
   			$this->db->select( 'fdd_pro_quantity.fdd_pro_id as gs1_pid,products.company_id' );
   			$this->db->distinct( 'products.company_id' );
   			$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
   			$this->db->where( 'is_obs_product', 0 );
   			$fdd_pro_ids = $this->db->get_where( 'fdd_pro_quantity',array('fdd_pro_quantity.fdd_pro_id' => $value) )->result_array();
   			foreach ($fdd_pro_ids as $key1 => $value1) {
	   			$fdd_pro_ids[$key1]['request_status'] = 1;
	   			$this->db->select( 'id' );
	   			$query = $this->db->get_where( 'request_gs1', $fdd_pro_ids[$key1] )->result_array();
	   			if( empty( $query ) ){
   					$this->db->insert('request_gs1',$fdd_pro_ids[$key1]);
	   			}
   			}
   		}

   		foreach ($intersect1 as $key => $value) {
   			$this->db->select( 'fdd_pro_quantity.fdd_pro_id as gs1_pid,products.company_id' );
   			$this->db->distinct( 'products.company_id' );
   			$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
   			$this->db->where( 'is_obs_product', 0 );
   			$fdd_pro_ids = $this->db->get_where( 'fdd_pro_quantity',array('fdd_pro_quantity.fdd_pro_id' => $value) )->result_array();
   			foreach ($fdd_pro_ids as $key1 => $value1) {

		   		if( $check_status[ 'approval_status' ] == 1 ) {
	   				$fdd_pro_ids[$key1]['request_status'] = 1;
		   		} else {
	   				$fdd_pro_ids[$key1]['request_status'] = 0;
		   		}

	   			$this->db->select( 'id' );
	   			$query = $this->db->get_where( 'request_gs1', $fdd_pro_ids[$key1] )->result_array();
	   			if( empty( $query ) ){
   					$this->db->insert('request_gs1',$fdd_pro_ids[$key1]);
	   			}
   			}
   		}
   	}

   	function script_gs2() {

   		$this->db->distinct( 'gs1_pid' );
   		$this->db->select( 'gs1_pid' );
   		$gs1_pids = $this->db->get( 'request_gs2' )->result_array();
   		$gs1_pids = array_column( $gs1_pids, 'gs1_pid' );
   		foreach ($gs1_pids as $key => $value) {
   			$this->db->distinct( 'fdd_pro_id' );
	   		$this->db->select( 'fdd_pro_id' );
	   		$this->db->where( 'is_obs_product', 0 );
	   		$this->db->where( 'fdd_pro_id', $value );
	   		$fdd_pro_ids = $this->db->get( 'fdd_pro_quantity' )->result_array();
	   		if(empty($fdd_pro_ids))
	   		{
	   			$arr[] = $value;
	   		}
   		}

   		echo "<pre>";
   		print_r($arr);die;

   		foreach ($arr as $key => $value) {
   			$this->db->where( 'gs1_pid', $value );
   			$this->db->delete( 'request_gs2' );
   		}
   		
   	}

   	/* get prod ids which not exist in products table*/
   	function get_not_exist_prod_ids() {
   		$this->db->distinct( 'obs_pro_id' );
   		$this->db->select( 'obs_pro_id' );
   		$this->db->where( 'is_obs_product', 0 );
   		$obs_pro_id = $this->db->get( 'fdd_pro_quantity' )->result_array();
   		$obs_pro_id = array_column( $obs_pro_id, 'obs_pro_id' );

   		$this->db->select( 'id' );
   		// $this->db->where( 'procreated >', '2017-09-12' );
   		$this->db->where( 'company_id !=', 0 );
   		$all_prod_ids = $this->db->get( 'products' )->result_array();
   		$all_prod_ids = array_column( $all_prod_ids, 'id' );

   		$arr_diff = array_values( array_diff( $obs_pro_id, $all_prod_ids ) );

   		$this->db->select( '*' );
   		$this->db->where_in( 'id', $arr_diff );
   		$all_prod_ids = $this->db->get( 'products' )->result_array();
   		echo $this->db->last_query();die;

   	}

   	function pws_exist_unapprove(){
   		$arr = array();
   		$this->db->select( 'data_sheet' );
   		$this->db->where( 'checked', '0' );
   		$sheets = $this->db->get( 'pws_products_sheets' )->result_array();
   		
   		foreach ($sheets as $key => $value) {
   			$this->fdb->select('p_id');
   			$rew = $this->fdb->get_where('products',array('data_sheet'=>$value['data_sheet']))->result_array();
   			
   			if(!empty($rew))
   			{
   				$arr[] = $rew[0]['p_id'];
   			}
   			
   		}
   		
   		echo "<pre>";
   		print_r($arr);die;
   	}

   	  function pws_exist_unapprove1(){
   		$arr = array();
   		$this->db->select( 'obs_pro_id' );
   		$this->db->where( 'checked', '0' );
   		$sheets = $this->db->get( 'pws_products_sheets' )->result_array();
   		foreach ($sheets as $key => $value) {	
   			//$exist = $this->db->get_where( 'products',array('id'=>$value['obs_pro_id']) )->result_array();

			$exist = $this->db->get_where( 'fdd_pro_quantity',array('fdd_pro_id'=>$value['obs_pro_id'],'is_obs_product'=>1) )->result_array();

   			if(empty($exist))
   			{
   				$arr[] = $value['obs_pro_id'];
   			}
   		}

   		// foreach ($arr as $key => $value) {
   		// 	$this->db->where(array('obs_pro_id'=>$value));
   		// 	$this->db->delete('pws_products_sheets');
   		// }


   		
   		echo "<pre>";
   		print_r($arr);die;
   	}

   	function removed_products_pro(){
   		$this->db->distinct();
   		$this->db->select('products_id');
   		$res = $this->db->get('order_details')->result_array();

   		$arr = array();
   		if(!empty($res))
   		{
   			foreach ($res as $key => $value) {
   				$res1 = $this->db->get_where('products',array('id'=>$value['products_id']))->result_array();
   				if(empty($res1))
   				{
   					$arr[] = $value['products_id'];
   				}
   			}
   		}

   		//84 displaying

   		//echo "<pre>";
   		//print_r($arr);die;

   		$this->db->where_in('id',$arr);
   		$res2 = $this->db->get('products1')->result_array();
   		//echo "<pre>";
   		//print_r($res2);die;

   		foreach ($res2 as $key => $value) {
   			$this->db->insert('products',$value);
   		 	echo $new_p = $this->db->insert_id();
   		 	echo '<br/>';
   		}
   		die;
   	}

   	// function removed_products(){
   	// 	$this->db->distinct();
   	// 	$this->db->select('obs_pro_id');
   	// 	$res = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0))->result_array();
   	// 	if(!empty($res))
   	// 	{
   	// 		foreach ($res as $key => $value) {
   	// 			$res1 = $this->db->get_where('products',array('id'=>$value['obs_pro_id']))->result_array();
   	// 			if(empty($res1))
   	// 			{
   	// 				$arr[] = $value['obs_pro_id'];
   	// 			}
   	// 		}
   	// 	}

   	// 	//84 displaying

   	// 	echo "<pre>";
   	// 	print_r($arr);die;

   	// 	$this->db->where_in('id',$arr);
   	// 	$res2 = $this->db->get('products1')->result_array();
   	// 	//echo "<pre>";
   	// 	//print_r($res2);die;

   	// 	// foreach ($res2 as $key => $value) {
   	// 	// 	$this->db->insert('products',$value);
   	// 	//  	echo $new_p = $this->db->insert_id();
   	// 	//  	echo '<br/>';
   	// 	// }
   	// 	// die;
   	// }


   	function obs_products_stat(){
   		$this->db->distinct();
   		$this->db->select('fdd_pro_id');
   		$res = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0))->result_array();
   		if(!empty($res))
   		{
   			foreach ($res as $key => $value) {
   				$exists = $this->db->get_where('products',array('id' => $value['fdd_pro_id'],'company_id'=>0))->result_array();
   				if(!empty($exists))
   				{
   					$arr[] = $value['fdd_pro_id'];
   				}
   			}
   		}
   		
   		foreach ($arr as $key => $value) {
   			$exists = $this->fdb->get_where('products',array('p_id' => $value))->result_array();
   			if(empty($exists))
			{
				$arr1[] = $value;
			}
   		}
   		
   		echo "<pre>";
   		print_r($arr1);die;
   	}


   	/*
   	* function for check gs1 products whose fixed is 0 in fdd pro quantity but these are gs1
   	*/

   	function check_review_and_fixed($product_type = '0'){
   		
   		$this->db->distinct();
   		$this->db->select('fdd_pro_id,fixed');
   		$res = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0 ,'fixed' => 0))->result_array();
   		if(!empty($res))
   		{
   			
   			foreach ($res as $key => $value) {
   				$exists = $this->fdb->get_where('products',array('p_id' => $value['fdd_pro_id'],'product_type'=>'1'))->result_array();
	   			if(!empty($exists))
				{
					$arr1[] = $value['fdd_pro_id'];
				}
   			}
   			
   			if(!empty($arr1))
   			{
	   			foreach ($arr1 as $key => $value) {
	   				$this->db->where(array('fdd_pro_id'=>$value,'is_obs_product'=>0));
	   				$this->db->update('fdd_pro_quantity',array('fixed'=>1));
	   			}
	   		}
   		}

   	}

   	/*
   	* function for check  products whose fixed is 0 in fdd pro quantity but these are review and fixed in products table in fdd.be for product type 0 products
   	*/

   	function check_review_and_fixed1($product_type = '0'){
   		$this->db->distinct();
   		$this->db->select('fdd_pro_id,fixed');
   		$res = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0 ,'fixed' => 0))->result_array();
   		if(!empty($res))
   		{
   			
   			foreach ($res as $key => $value) {
   				$exists = $this->fdb->get_where('products',array('p_id' => $value['fdd_pro_id'],'product_type'=>'0'))->result_array();
	   			if(!empty($exists))
				{
					$exist = $this->fdb->get_where('products',array('p_id' => $value['fdd_pro_id'],'review&fixed'=>1,'product_type'=>'0'))->result_array();
					if(!empty($exist))
					{
						$arr1[] = $value['fdd_pro_id'];
					}
				}
   			}
   			
   			if(!empty($arr1))
   			{
	   			foreach ($arr1 as $key => $value) {
	   				$this->db->where(array('fdd_pro_id'=>$value,'is_obs_product'=>0));
	   				$this->db->update('fdd_pro_quantity',array('fixed'=>1));
	   			}
	   		}
   		}
   	}


   	/*
   	* function for check  products whose fixed is 1 in fdd pro quantity but these are not review and fixed in products table in fdd.be for product type 0 products
   	*/

   	function check_review_and_fixed2($product_type = '0'){
   		
   		$this->db->distinct();
   		$this->db->select('fdd_pro_id,fixed');
   		$res = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0 ,'fixed' => 1))->result_array();
   		if(!empty($res))
   		{
   			
   			foreach ($res as $key => $value) {
   				$exists = $this->fdb->get_where('products',array('p_id' => $value['fdd_pro_id'],'product_type'=>'0'))->result_array();
	   			if(!empty($exists))
				{
					$exist = $this->fdb->get_where('products',array('p_id' => $value['fdd_pro_id'],'review&fixed'=>0,'product_type'=>'0'))->result_array();
					if(!empty($exist))
					{
						$arr1[] = $value['fdd_pro_id'];
					}
				}
   			}
   			
   			if(!empty($arr1))
   			{
	   			foreach ($arr1 as $key => $value) {
	   				$this->db->where(array('fdd_pro_id'=>$value,'is_obs_product'=>0));
	   				$this->db->update('fdd_pro_quantity',array('fixed'=>0));
	   			}
	   		}
   		}
   	}

	/*
	* function for check repeated ingredinets with closed brackets
	*/

	function get_repeted_ingredients_closed_bracket(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$this->db->select( 'product_id' );
						$array = $this->db->get_where( 'products_ingredients',array( 
										'product_id' => $obs_pro_id,
										'kp_id'      => $fdd_pro_id,
										'ki_name'    => ')' ,
										'ki_id'      => 0
										) 
						)->result_array();
						if( sizeof( $array ) > 1 ){
							$fp = fopen(dirname(__FILE__)."/script_logs/get_repeted_ingredients_closed_bracket.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/get_repeted_ingredients_closed_bracket.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							
							
							// $product_id = $fdd_pro_id;
							// $this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
							// $this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
							// $this->db->join('general_settings','general_settings.company_id = products.company_id');
							// $this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.obs_pro_id'=>$obs_pro_id));
							// $cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

							// 

							// $this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch,approval_status' );
							// $res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

							// $this->fdb->select('i_id,aller_type_fr,aller_type_dch,aller_type,allergence,allergence_dch,allergence_fr,sub_allergence,sub_allergence_dch,sub_allergence_fr');
							// $this->fdb->order_by('order','ASC');
							// $update_array = $this->fdb->get_where('prod_ingredients',array('p_id'=>$product_id))->result();
					
							// 

							// foreach ($cp_prod_ids as $cp_prod_id){
							// 	if($cp_prod_id->language_id == '3'){
							// 		$ing_var 	= '_fr';
							// 		$disp_id 	= '_fr';
							// 		$short_name = $res_short['p_short_name_fr'];
							// 	}elseif($cp_prod_id->language_id == '2'){
							// 		$ing_var 	= '_dch';
							// 		$disp_id 	= '';
							// 		$short_name = $res_short['p_short_name_dch'];
							// 	}
							// 	else{
							// 		$ing_var 	= '';
							// 		$disp_id 	= '_en';
							// 		$short_name = $res_short['p_short_name'];
							// 	}
							// 	$this->db->select( 'fdd_pro_id' );
							// 	$this->db->order_by( 'quantity', 'desc' );
							// 	$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
							// 	$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
							// 	$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
							// 	$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
							// 	$kp_order 	= array_search( $product_id , $product_ids) + 1;
								
								
							// 		$insert_aray = array();
							// 		$disp_order = 1;
							// 		$insert_aray[] = array(
							// 				'product_id'		=> $cp_prod_id->product_id,
							// 				'kp_id'				=> $product_id,
							// 				'ki_id'				=> 0,
							// 				'ki_name'			=> $short_name,
							// 				'display_order'		=> $disp_order,
							// 				'kp_display_order'	=> $kp_order,
							// 				'date_added'		=> date('Y-m-d H:i:s'),
							// 				'is_obs_ing'		=> 0,
							// 				'have_all_id' 		=> 0,
							// 				'aller_type'		=> '0',
							// 				'aller_type_fr'		=> '0',
							// 				'aller_type_dch'	=> '0',
							// 				'allergence'		=> '0',
							// 				'allergence_fr'		=> '0',
							// 				'allergence_dch'	=> '0',
							// 				'sub_allergence'	=> '0',
							// 				'sub_allergence_fr'	=> '0',
							// 				'sub_allergence_dch'=> '0'
							// 		);

							// 		$insert_aray[] = array(
							// 				'product_id'		=> $cp_prod_id->product_id,
							// 				'kp_id'				=> $product_id,
							// 				'ki_id'				=> 0,
							// 				'ki_name'			=> '(',
							// 				'display_order'		=> ++$disp_order,
							// 				'kp_display_order'	=> $kp_order,
							// 				'date_added'		=> date('Y-m-d H:i:s'),
							// 				'is_obs_ing'		=> 0,
							// 				'have_all_id' 		=> 0,
							// 				'aller_type'		=> '0',
							// 				'aller_type_fr'		=> '0',
							// 				'aller_type_dch'	=> '0',
							// 				'allergence'		=> '0',
							// 				'allergence_fr'		=> '0',
							// 				'allergence_dch'	=> '0',
							// 				'sub_allergence'	=> '0',
							// 				'sub_allergence_fr'	=> '0',
							// 				'sub_allergence_dch'=> '0'
							// 		);

							// 		

									
							// 		if ($res_short['approval_status'] == 1) {
							// 			if(!empty($update_array)){
							// 				foreach ($update_array as $key => $update_array_id){
							// 					$disp_order 	 = $key+3; 

							// 					$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
							// 					$this->fdb->where('ing_id', $update_array_id->i_id);
							// 					$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
							// 					$ing_index 		= 'ing_name'.$ing_var;
							// 					$aller_type_index = 'aller_type'.$ing_var;
							// 					$allergence_type_index = 'allergence'.$ing_var;
							// 					$sub_aller_type_index = 'sub_allergence'.$ing_var;
							// 					$display_name 	= $ingredient_info_to_add[0]->$ing_index;

							// 					$display_name1 	= $update_array[0]->$aller_type_index;
							// 					$display_name2 	= $update_array[0]->$allergence_type_index;

							// 					$display_name3 	= $update_array[0]->$sub_aller_type_index;

							// 					$ki_id = $ingredient_info_to_add[0]->ing_id;
							// 					$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

							// 					if(!empty($enbr_result)){
							// 						$ki_id = $enbr_result['ki_id'];
							// 						$display_name = $enbr_result['ki_name'];
							// 					}

							// 					$insert_aray[] = array(
							// 							'product_id'		=> $cp_prod_id->product_id,
							// 							'kp_id'				=> $product_id,
							// 							'ki_id'				=> $ki_id,
							// 							'ki_name'			=> $display_name,
							// 							'display_order'		=> $disp_order,
							// 							'kp_display_order'	=> $kp_order,
							// 							'date_added'		=> date('Y-m-d H:i:s'),
							// 							'is_obs_ing'		=> 0,
							// 							'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
							// 							'aller_type'		=> '0',
							// 							'aller_type_fr'		=> '0',
							// 							'aller_type_dch'	=> '0',
							// 							'allergence'		=> '0',
							// 							'allergence_fr'		=> '0',
							// 							'allergence_dch'	=> '0',
							// 							'sub_allergence'	=> '0',
							// 							'sub_allergence_fr'	=> '0',
							// 							'sub_allergence_dch'=> '0'
							// 					);

							// 					if ($ing_var == '_dch')
							// 					{

							// 						$insert_aray[$key+2]['aller_type_dch'] = $display_name1;
							// 						$insert_aray[$key+2]['allergence_dch']	= $display_name2;
							// 						$insert_aray[$key+2]['sub_allergence_dch']= $display_name3;
							// 					}
							// 					if ($ing_var == '_fr')
							// 					{
							// 						$insert_aray[$key+2]['aller_type_fr'] = $display_name1;
							// 						$insert_aray[$key+2]['allergence_fr']	= $display_name2;
							// 						$insert_aray[$key+2]['sub_allergence_fr']= $display_name3;
							// 					}
							// 					if ($ing_var == '')
							// 					{
							// 						$insert_aray[$key+2]['aller_type'] = $display_name1;
							// 						$insert_aray[$key+2]['allergence']	= $display_name2;
							// 						$insert_aray[$key+2]['sub_allergence']= $display_name3;
							// 					}
							// 				}
							// 			}
							// 		}

							// 		

							// 		$insert_aray[] = array(
							// 				'product_id'		=> $cp_prod_id->product_id,
							// 				'kp_id'				=> $product_id,
							// 				'ki_id'				=> 0,
							// 				'ki_name'			=> ')',
							// 				'display_order'		=> ++$disp_order,
							// 				'kp_display_order'	=> $kp_order,
							// 				'date_added'		=> date('Y-m-d H:i:s'),
							// 				'is_obs_ing'		=> 0,
							// 				'have_all_id' 		=> 0,
							// 				'aller_type'		=> '0',
							// 				'aller_type_fr'		=> '0',
							// 				'aller_type_dch'	=> '0',
							// 				'allergence'		=> '0',
							// 				'allergence_fr'		=> '0',
							// 				'allergence_dch'	=> '0',
							// 				'sub_allergence'	=> '0',
							// 				'sub_allergence_fr'	=> '0',
							// 				'sub_allergence_dch'=> '0'
							// 		);


							// 		$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$product_id,'is_obs_ing'=>0));
							// 		$this->db->delete('products_ingredients');

							// 		$this->db->insert_batch('products_ingredients', $insert_aray);	
							// 	}
						}
					}
				}
			}
		}
	}

	/*
	* function for check ingredients present in prod ingredinets but its unapproved
	*/

	function get_unapprove_products_ingredients(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						echo $fdd_pro_id;

						// 
						// $fdd_data =  $this->fdb->get_where('products', array( 'p_id' => $fdd_pro_id, 'approval_status' => '0' ) )->row_array();
						// 
						//if( !empty( $fdd_data ) ){
						$array = $this->db->get_where( 'products_ingredients',array( 
										'product_id' => $obs_pro_id,
										'kp_id'      => $fdd_pro_id
										) 
						)->result_array();
						if( empty( $array )){
							$fp = fopen(dirname(__FILE__)."/script_logs/get_unapprove_products_ingredients.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/get_unapprove_products_ingredients.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);


							$product_id = $fdd_pro_id;
							$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
							$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
							$this->db->join('general_settings','general_settings.company_id = products.company_id');
							$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.obs_pro_id'=>$obs_pro_id));
							$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

							

							$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch,approval_status' );
							$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

							$this->fdb->select('i_id,aller_type_fr,aller_type_dch,aller_type,allergence,allergence_dch,allergence_fr,sub_allergence,sub_allergence_dch,sub_allergence_fr');
							$this->fdb->order_by('order','ASC');
							$update_array = $this->fdb->get_where('prod_ingredients',array('p_id'=>$product_id))->result();
					
							

							foreach ($cp_prod_ids as $cp_prod_id){
								if($cp_prod_id->language_id == '3'){
									$ing_var 	= '_fr';
									$disp_id 	= '_fr';
									$short_name = $res_short['p_short_name_fr'];
								}elseif($cp_prod_id->language_id == '2'){
									$ing_var 	= '_dch';
									$disp_id 	= '';
									$short_name = $res_short['p_short_name_dch'];
								}
								else{
									$ing_var 	= '';
									$disp_id 	= '_en';
									$short_name = $res_short['p_short_name'];
								}
								$this->db->select( 'fdd_pro_id' );
								$this->db->order_by( 'quantity', 'desc' );
								$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
								$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
								$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
								$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
								$kp_order 	= array_search( $product_id , $product_ids) + 1;
								
								
									$insert_aray = array();
									$disp_order = 1;
									$insert_aray[] = array(
											'product_id'		=> $cp_prod_id->product_id,
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
											'product_id'		=> $cp_prod_id->product_id,
											'kp_id'				=> $product_id,
											'ki_id'				=> 0,
											'ki_name'			=> '(',
											'display_order'		=> ++$disp_order,
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

									

									
									if ($res_short['approval_status'] == 1) {
										if(!empty($update_array)){
											foreach ($update_array as $key => $update_array_id){
												$disp_order 	 = $key+3; 

												$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
												$this->fdb->where('ing_id', $update_array_id->i_id);
												$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
												$ing_index 		= 'ing_name'.$ing_var;
												$aller_type_index = 'aller_type'.$ing_var;
												$allergence_type_index = 'allergence'.$ing_var;
												$sub_aller_type_index = 'sub_allergence'.$ing_var;
												$display_name 	= $ingredient_info_to_add[0]->$ing_index;

												$display_name1 	= $update_array[0]->$aller_type_index;
												$display_name2 	= $update_array[0]->$allergence_type_index;

												$display_name3 	= $update_array[0]->$sub_aller_type_index;

												$ki_id = $ingredient_info_to_add[0]->ing_id;
												$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

												if(!empty($enbr_result)){
													$ki_id = $enbr_result['ki_id'];
													$display_name = $enbr_result['ki_name'];
												}

												$insert_aray[] = array(
														'product_id'		=> $cp_prod_id->product_id,
														'kp_id'				=> $product_id,
														'ki_id'				=> $ki_id,
														'ki_name'			=> $display_name,
														'display_order'		=> $disp_order,
														'kp_display_order'	=> $kp_order,
														'date_added'		=> date('Y-m-d H:i:s'),
														'is_obs_ing'		=> 0,
														'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
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

												if ($ing_var == '_dch')
												{

													$insert_aray[$key+2]['aller_type_dch'] = $display_name1;
													$insert_aray[$key+2]['allergence_dch']	= $display_name2;
													$insert_aray[$key+2]['sub_allergence_dch']= $display_name3;
												}
												if ($ing_var == '_fr')
												{
													$insert_aray[$key+2]['aller_type_fr'] = $display_name1;
													$insert_aray[$key+2]['allergence_fr']	= $display_name2;
													$insert_aray[$key+2]['sub_allergence_fr']= $display_name3;
												}
												if ($ing_var == '')
												{
													$insert_aray[$key+2]['aller_type'] = $display_name1;
													$insert_aray[$key+2]['allergence']	= $display_name2;
													$insert_aray[$key+2]['sub_allergence']= $display_name3;
												}
											}
										}
									}

									

									$insert_aray[] = array(
											'product_id'		=> $cp_prod_id->product_id,
											'kp_id'				=> $product_id,
											'ki_id'				=> 0,
											'ki_name'			=> ')',
											'display_order'		=> ++$disp_order,
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

									$this->db->insert_batch('products_ingredients', $insert_aray);	
								}
								
							}
						//}
					}
				}
			}
		}
	}

	/*
	* function for check ingredients present in prod ingredinets but its unapproved
	*/

	function get_unapprove_products_traces(){
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						echo $fdd_pro_id;

						
						$this->fdb->join('prod_allergence','prod_allergence.p_id = products.p_id');
						$fdd_data =  $this->fdb->get_where('products', array( 'products.p_id' => $fdd_pro_id, 'approval_status' => 1 ) )->result_array();
						
						if( !empty( $fdd_data ) ){
							$array = $this->db->get_where( 'products_allergence',array( 
											'product_id' => $obs_pro_id,
											'kp_id'      => $fdd_pro_id,
											'ka_id !='      => 0
											) 
							)->result_array();
							if( empty( $array )){
								$fp = fopen(dirname(__FILE__)."/script_logs/get_unapprove_products_allergence.txt","a");
								fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
								fclose($fp);
								$fp = fopen(dirname(__FILE__)."/script_logs/get_unapprove_products_allergence.txt","a");
								fwrite($fp,"product_id: ".$obs_pro_id."------");
								fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
								fclose($fp);
							}
						}
					}
				}
			}
		}
	}

	/*
	* fuction for check which contains open bracket but not contains shortnames
	*/

	function check_ingredients_shortname_contains_open_bracket(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$array1 = $this->db->get_where( 'products_ingredients',array( 
							'product_id' => $obs_pro_id,
							'kp_id'      => $fdd_pro_id,
							'ki_name '      => '(',
							'ki_id'      => 0
							) 
						)->result_array();
						if (!empty($array1)) {
							$array = $this->db->get_where( 'products_ingredients',array( 
								'product_id' => $obs_pro_id,
								'kp_id'      => $fdd_pro_id,
								'ki_name !='      => '(',
								'ki_name !='      => ')',
								'ki_id'      => 0
								) 
							)->result_array();
							if( empty( $array )){
								$fp = fopen(dirname(__FILE__)."/script_logs/check_ingredients_shortname_contains_open_bracket.txt","a");
								fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
								fclose($fp);
								$fp = fopen(dirname(__FILE__)."/script_logs/check_ingredients_shortname_contains_open_bracket.txt","a");
								fwrite($fp,"product_id: ".$obs_pro_id."------");
								fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
								fclose($fp);
							}
						}
					}
				}
			}
		}
	}

	/*
	* function for check extra products which present in products ingredinets but not in fdd pro quantity
	*/

	function check_extra_fdd_product_in_product_ingre(){
	
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');

		$this->db->distinct();
		$this->db->select('product_id,kp_id');
		$fdd_pro_id = $this->db->get_where('products_ingredients',array('is_obs_ing'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'product_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'product_id' ] ] = array( $pro_id[ 'kp_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'product_id' ] ], $pro_id[ 'kp_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$this->db->distinct();
						$this->db->select('obs_pro_id,fdd_pro_id');
						$fdd_pro = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'0','obs_pro_id'=>$obs_pro_id,'fdd_pro_id'=>$fdd_pro_id))->result_array();
						
						if( empty( $fdd_pro )){
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_fdd_product_in_product_ingre.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_fdd_product_in_product_ingre.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);
							// $this->db->where(array('product_id'=>$obs_pro_id,'kp_id'=>$fdd_pro_id,'is_obs_ing'=>0));
							// $this->db->delete( 'products_ingredients');
						}
					}
				}
			}
		}
	}


	/*
	* function for check extra products which present in fdd pro quantity but not in products ingredinets
	*/

   	function check_extra_fdd_product_in_fdd_prod(){
   		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {

						$array1 = $this->db->get_where( 'products_ingredients',array( 
								'product_id' => $obs_pro_id,
								'kp_id'      => $fdd_pro_id,
								'is_obs_ing'	=> 0
							) 
						)->result_array();

						if(empty($array1))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_fdd_product_in_fdd_prod.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_fdd_product_in_fdd_prod.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);

							// $product_id = $fdd_pro_id;
							// $this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
							// $this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
							// $this->db->join('general_settings','general_settings.company_id = products.company_id');
							// $this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.obs_pro_id'=>$obs_pro_id));
							// $cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

							// 

							// $this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch,approval_status' );
							// $res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

							// $this->fdb->select('i_id,aller_type_fr,aller_type_dch,aller_type,allergence,allergence_dch,allergence_fr,sub_allergence,sub_allergence_dch,sub_allergence_fr');
							// $this->fdb->order_by('order','ASC');
							// $update_array = $this->fdb->get_where('prod_ingredients',array('p_id'=>$product_id))->result();
					
							// 

							// foreach ($cp_prod_ids as $cp_prod_id){
							// 	if($cp_prod_id->language_id == '3'){
							// 		$ing_var 	= '_fr';
							// 		$disp_id 	= '_fr';
							// 		$short_name = $res_short['p_short_name_fr'];
							// 	}elseif($cp_prod_id->language_id == '2'){
							// 		$ing_var 	= '_dch';
							// 		$disp_id 	= '';
							// 		$short_name = $res_short['p_short_name_dch'];
							// 	}
							// 	else{
							// 		$ing_var 	= '';
							// 		$disp_id 	= '_en';
							// 		$short_name = $res_short['p_short_name'];
							// 	}
							// 	$this->db->select( 'fdd_pro_id' );
							// 	$this->db->order_by( 'quantity', 'desc' );
							// 	$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
							// 	$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
							// 	$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
							// 	$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
							// 	$kp_order 	= array_search( $product_id , $product_ids) + 1;
								
								
							// 	$insert_aray = array();
							// 	$disp_order = 1;
							// 	$insert_aray[] = array(
							// 			'product_id'		=> $cp_prod_id->product_id,
							// 			'kp_id'				=> $product_id,
							// 			'ki_id'				=> 0,
							// 			'ki_name'			=> $short_name,
							// 			'display_order'		=> $disp_order,
							// 			'kp_display_order'	=> $kp_order,
							// 			'date_added'		=> date('Y-m-d H:i:s'),
							// 			'is_obs_ing'		=> 0,
							// 			'have_all_id' 		=> 0,
							// 			'aller_type'		=> '0',
							// 			'aller_type_fr'		=> '0',
							// 			'aller_type_dch'	=> '0',
							// 			'allergence'		=> '0',
							// 			'allergence_fr'		=> '0',
							// 			'allergence_dch'	=> '0',
							// 			'sub_allergence'	=> '0',
							// 			'sub_allergence_fr'	=> '0',
							// 			'sub_allergence_dch'=> '0'
							// 	);

							// 	$insert_aray[] = array(
							// 			'product_id'		=> $cp_prod_id->product_id,
							// 			'kp_id'				=> $product_id,
							// 			'ki_id'				=> 0,
							// 			'ki_name'			=> '(',
							// 			'display_order'		=> ++$disp_order,
							// 			'kp_display_order'	=> $kp_order,
							// 			'date_added'		=> date('Y-m-d H:i:s'),
							// 			'is_obs_ing'		=> 0,
							// 			'have_all_id' 		=> 0,
							// 			'aller_type'		=> '0',
							// 			'aller_type_fr'		=> '0',
							// 			'aller_type_dch'	=> '0',
							// 			'allergence'		=> '0',
							// 			'allergence_fr'		=> '0',
							// 			'allergence_dch'	=> '0',
							// 			'sub_allergence'	=> '0',
							// 			'sub_allergence_fr'	=> '0',
							// 			'sub_allergence_dch'=> '0'
							// 	);

							// 	

									
							// 	if ($res_short['approval_status'] == 1) {
							// 		if(!empty($update_array)){
							// 			foreach ($update_array as $key => $update_array_id){
							// 				$disp_order 	 = $key+3; 

							// 				$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
							// 				$this->fdb->where('ing_id', $update_array_id->i_id);
							// 				$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
							// 				$ing_index 		= 'ing_name'.$ing_var;
							// 				$aller_type_index = 'aller_type'.$ing_var;
							// 				$allergence_type_index = 'allergence'.$ing_var;
							// 				$sub_aller_type_index = 'sub_allergence'.$ing_var;
							// 				$display_name 	= $ingredient_info_to_add[0]->$ing_index;

							// 				$display_name1 	= $update_array[0]->$aller_type_index;
							// 				$display_name2 	= $update_array[0]->$allergence_type_index;

							// 				$display_name3 	= $update_array[0]->$sub_aller_type_index;

							// 				$ki_id = $ingredient_info_to_add[0]->ing_id;
							// 				$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

							// 				if(!empty($enbr_result)){
							// 					$ki_id = $enbr_result['ki_id'];
							// 					$display_name = $enbr_result['ki_name'];
							// 				}

							// 				$insert_aray[] = array(
							// 						'product_id'		=> $cp_prod_id->product_id,
							// 						'kp_id'				=> $product_id,
							// 						'ki_id'				=> $ki_id,
							// 						'ki_name'			=> $display_name,
							// 						'display_order'		=> $disp_order,
							// 						'kp_display_order'	=> $kp_order,
							// 						'date_added'		=> date('Y-m-d H:i:s'),
							// 						'is_obs_ing'		=> 0,
							// 						'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
							// 						'aller_type'		=> '0',
							// 						'aller_type_fr'		=> '0',
							// 						'aller_type_dch'	=> '0',
							// 						'allergence'		=> '0',
							// 						'allergence_fr'		=> '0',
							// 						'allergence_dch'	=> '0',
							// 						'sub_allergence'	=> '0',
							// 						'sub_allergence_fr'	=> '0',
							// 						'sub_allergence_dch'=> '0'
							// 				);

							// 				if ($ing_var == '_dch')
							// 				{

							// 					$insert_aray[$key+2]['aller_type_dch'] = $display_name1;
							// 					$insert_aray[$key+2]['allergence_dch']	= $display_name2;
							// 					$insert_aray[$key+2]['sub_allergence_dch']= $display_name3;
							// 				}
							// 				if ($ing_var == '_fr')
							// 				{
							// 					$insert_aray[$key+2]['aller_type_fr'] = $display_name1;
							// 					$insert_aray[$key+2]['allergence_fr']	= $display_name2;
							// 					$insert_aray[$key+2]['sub_allergence_fr']= $display_name3;
							// 				}
							// 				if ($ing_var == '')
							// 				{
							// 					$insert_aray[$key+2]['aller_type'] = $display_name1;
							// 					$insert_aray[$key+2]['allergence']	= $display_name2;
							// 					$insert_aray[$key+2]['sub_allergence']= $display_name3;
							// 				}
							// 			}
							// 		}
							// 	}

							// 	

							// 	$insert_aray[] = array(
							// 		'product_id'		=> $cp_prod_id->product_id,
							// 		'kp_id'				=> $product_id,
							// 		'ki_id'				=> 0,
							// 		'ki_name'			=> ')',
							// 		'display_order'		=> ++$disp_order,
							// 		'kp_display_order'	=> $kp_order,
							// 		'date_added'		=> date('Y-m-d H:i:s'),
							// 		'is_obs_ing'		=> 0,
							// 		'have_all_id' 		=> 0,
							// 		'aller_type'		=> '0',
							// 		'aller_type_fr'		=> '0',
							// 		'aller_type_dch'	=> '0',
							// 		'allergence'		=> '0',
							// 		'allergence_fr'		=> '0',
							// 		'allergence_dch'	=> '0',
							// 		'sub_allergence'	=> '0',
							// 		'sub_allergence_fr'	=> '0',
							// 		'sub_allergence_dch'=> '0'
							// 	);
							// 	$this->db->insert_batch('products_ingredients', $insert_aray);
							// }
						}
					}
				}
			}
		}
	}

	function removed_ingredinets(){
		error_reporting( E_ALL );
		ini_set( "display_errors", 1);
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		

		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$this->db->join('company','company.id = products.company_id');
		$this->db->order_by('obs_pro_id','ASC');
		// $this->db->where('company.id', '87');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'0','company.status' => '1'))->result_array();
		
		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}
			
		$final_array = array();

		foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
			if( !empty( $fdd_pro_id_arr ) ){

				foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
					echo $key;

					$this->db->select( 'language_id, enbr_status' );
					$this->db->join( 'company', 'products.company_id = company.id' );
					$this->db->join( 'general_settings', 'general_settings.company_id = company.id' );
					$this->db->where( 'products.id', $obs_pro_id );
					$comp_detail = $this->db->get( 'products' )->row_array();

					if( !empty( $comp_detail ) ) {

						if($comp_detail['language_id'] == '3'){
							$ing_var 	= '_fr';
						}elseif($comp_detail['language_id'] == '2'){
							$ing_var 	= '_dch';
						}
						else{
							$ing_var 	= '';
						}

						$this->db->distinct( 'ki_id' );
						$this->db->select( 'ki_id' );
						$result = $this->db->get_where( 'products_ingredients', array(
										'product_id' 	=> $obs_pro_id,
										'kp_id'      	=> $fdd_pro_id,
										'ki_id !='  	=> 0
								))->result_array();
						if( !empty( $result ) ) {
							$result = array_column( $result, 'ki_id' );
						} else {
							$result = array();				
						}
						
						$this->fdb->distinct( 'i_id' );
						$this->fdb->select( 'i_id' );
						$this->fdb->join( 'products', 'products.p_id = prod_ingredients.p_id' );
						$this->fdb->where( 'prod_ingredients.p_id', $fdd_pro_id );
						$this->fdb->where( 'approval_status', 1 );
						$fdd_ing_ids = $this->fdb->get( 'prod_ingredients' )->result_array();

						if( !empty( $fdd_ing_ids ) ) {
							$final_array[ $obs_pro_id ][ $fdd_pro_id ] = array();
							foreach ( $fdd_ing_ids as $k => $fdd_i_id ) {
								$ki_id = $fdd_i_id[ 'i_id' ];

								if( ! in_array( $ki_id, $result ) ) {
									$enbr_result = $this->get_e_current_nbr($ki_id,$comp_detail['enbr_status'],$ing_var);
									if(!empty($enbr_result)){
										$ki_id = $enbr_result['ki_id'];

										if( ! in_array( $ki_id, $result ) ) {
											array_push( $final_array[ $obs_pro_id ][ $fdd_pro_id ], $ki_id ); 
										}
									} else {
											array_push( $final_array[ $obs_pro_id ][ $fdd_pro_id ], $ki_id ); 
									}
								}
							}
							if( empty( $final_array[ $obs_pro_id ][ $fdd_pro_id ] ) ) {
								unset( $final_array[ $obs_pro_id ] );
							}
						}
					}
				}
			}
		}
		$fdd_pro_ids = array();
		foreach ( $final_array as $k2 => $value2 ) {
			
			 array_push( $fdd_pro_ids, array_keys( $value2 ) );
		}
			$result6 = array();
		 foreach ($fdd_pro_ids as $key => $value) { 
		   $result6 = array_merge($result6, $value);
		} 
		$result6 = array_values(array_unique( $result6 ));

		// $json_arr = json_encode($result6);
		// $file = fopen( APPPATH. "ingr_test.txt","w" );
		// echo fwrite( $file, print_r($json_arr, true ) );
		// fclose($file);

		// $file = fopen( FCPATH. "ingr_test.txt","w" );
		// echo fwrite( $file, print_r($json_arr, true ) );
		// fclose($file);

		echo "<pre>";
		print_r (implode(",", $result6));
		print_r ($final_array);
		echo "</pre>";die;
		// $result6 = $this->get_fdd_prod_ids_arr();
		/*echo "<pre>";
		print_r ($result6);
		echo "</pre>";die;*/
		// foreach ($result6 as $k3 => $value3) {
		// 	echo $value3;
		// 	$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
		// 	$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
		// 	$this->db->join('general_settings','general_settings.company_id = products.company_id');
		// 	$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$value3,'fdd_pro_quantity.is_obs_product'=>0));
		// 	// $this->db->where('products.company_id', '87');
		// 	$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

		// 	$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch' );
		// 	$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $value3 ) )->row_array();

		// 	foreach ($cp_prod_ids as $cp_prod_id){
				
		// 		$short_name = '';
		// 		if($cp_prod_id->language_id == '3'){
		// 			$ing_var 	= '_fr';
		// 			$disp_id 	= '_fr';
		// 			$short_name = $res_short['p_short_name_fr'];
		// 		}elseif($cp_prod_id->language_id == '2'){
		// 			$ing_var 	= '_dch';
		// 			$disp_id 	= '';
		// 			$short_name = $res_short['p_short_name_dch'];
		// 		}
		// 		else{
		// 			$ing_var 	= '';
		// 			$disp_id 	= '_en';
		// 			$short_name = $res_short['p_short_name'];
		// 		}
		// 		$this->db->select( 'fdd_pro_id' );
		// 		$this->db->order_by( 'quantity', 'desc' );
		// 		$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
		// 		$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
		// 		$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
		// 		$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
		// 		$kp_order 	= array_search( $value3 , $product_ids) + 1;

		// 		$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$value3,'is_obs_ing' => 0));
		// 		$this->db->delete('products_ingredients');
				
		// 		$insert_aray = array();

		// 		$this->fdb->select('prod_ingredients.*, ingredients.have_all_id, ingredients.ing_name, ingredients.ing_name_fr, ingredients.ing_name_dch');
		// 		$this->fdb->join( 'ingredients', 'ingredients.ing_id = prod_ingredients.i_id' );
		// 		$this->fdb->order_by('order', 'asc');
		// 		$update_array = $this->fdb->get_where( 'prod_ingredients', array( 'p_id' => $value3 ) )->result_array();

		// 		$disp_order = 1;
		// 		$insert_aray[] = array(
		// 				'product_id'		=> $cp_prod_id->product_id,
		// 				'kp_id'				=> $value3,
		// 				'ki_id'				=> 0,
		// 				'ki_name'			=> $short_name,
		// 				'display_order'		=> $disp_order,
		// 				'kp_display_order'	=> $kp_order,
		// 				'date_added'		=> date('Y-m-d H:i:s'),
		// 				'is_obs_ing'		=> 0,
		// 				'have_all_id' 		=> 0,
		// 				'aller_type'		=> '0',
		// 				'aller_type_fr'		=> '0',
		// 				'aller_type_dch'	=> '0',
		// 				'allergence'		=> '0',
		// 				'allergence_fr'		=> '0',
		// 				'allergence_dch'	=> '0',
		// 				'sub_allergence'	=> '0',
		// 				'sub_allergence_fr'	=> '0',
		// 				'sub_allergence_dch'=> '0'
		// 		);

		// 		$insert_aray[] = array(
		// 				'product_id'		=> $cp_prod_id->product_id,
		// 				'kp_id'				=> $value3,
		// 				'ki_id'				=> 0,
		// 				'ki_name'			=> '(',
		// 				'display_order'		=> ++$disp_order,
		// 				'kp_display_order'	=> $kp_order,
		// 				'date_added'		=> date('Y-m-d H:i:s'),
		// 				'is_obs_ing'		=> 0,
		// 				'have_all_id' 		=> 0,
		// 				'aller_type'		=> '0',
		// 				'aller_type_fr'		=> '0',
		// 				'aller_type_dch'	=> '0',
		// 				'allergence'		=> '0',
		// 				'allergence_fr'		=> '0',
		// 				'allergence_dch'	=> '0',
		// 				'sub_allergence'	=> '0',
		// 				'sub_allergence_fr'	=> '0',
		// 				'sub_allergence_dch'=> '0'
		// 		);

		// 		if(!empty($update_array)){
		// 			foreach ($update_array as $key => $update_array_id){
		// 				$disp_order 	 = $key+3;
		// 				$ki_id = $update_array_id['i_id'];
		// 				$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

		// 				if(!empty($enbr_result)){
		// 					$ki_id = $enbr_result['ki_id'];
		// 					$display_name = $enbr_result['ki_name'];
		// 				}

		// 				$insert_aray[] = array(
		// 						'product_id'		=> $cp_prod_id->product_id,
		// 						'kp_id'				=> $value3,
		// 						'ki_id'				=> $ki_id,
		// 						'ki_name'			=> '',
		// 						'display_order'		=> $disp_order,
		// 						'kp_display_order'	=> $kp_order,
		// 						'date_added'		=> date('Y-m-d H:i:s'),
		// 						'is_obs_ing'		=> 0,
		// 						'have_all_id' 		=> $update_array_id['have_all_id'],
		// 						'aller_type'		=> $update_array_id['aller_type'],
		// 						'aller_type_fr'		=> $update_array_id['aller_type_fr'],
		// 						'aller_type_dch'	=> $update_array_id['aller_type_dch'],
		// 						'allergence'		=> $update_array_id['allergence'],
		// 						'allergence_fr'		=> $update_array_id['allergence_fr'],
		// 						'allergence_dch'	=> $update_array_id['allergence_dch'],
		// 						'sub_allergence'	=> $update_array_id['sub_allergence'],
		// 						'sub_allergence_fr'	=> $update_array_id['sub_allergence_fr'],
		// 						'sub_allergence_dch'=> $update_array_id['sub_allergence_dch']
		// 				);

		// 				$this->db->select('ki_id');
		// 				$res = $this->db->get_where('ingredients', array('ki_id' => $ki_id) )->row_array();

		// 				if( empty($res) ){
		// 					if( $cp_prod_id->enbr_status == 1 ){
		// 						if( !empty( $enbr_result ) ){
		// 							$object = array(
		// 										'ki_id' 	 => $ki_id,
		// 										'ki_name'	 => $display_name,
		// 										'ki_name_fr' => $display_name,
		// 										'ki_name_dch'=> $display_name
		// 									);
		// 						}
		// 						else{
		// 							$object = array(
		// 									'ki_id' 	 => $ki_id,
		// 									'ki_name'	 => $update_array_id['ing_name'],
		// 									'ki_name_fr' => $update_array_id['ing_name_fr'],
		// 									'ki_name_dch'=> $update_array_id['ing_name_dch']
		// 								);
		// 						}
		// 					}
		// 					if( $cp_prod_id->enbr_status == 2 ){
		// 						$object = array(
		// 									'ki_id' 	 => $ki_id,
		// 									'ki_name'	 => $update_array_id['ing_name'],
		// 									'ki_name_fr' => $update_array_id['ing_name_fr'],
		// 									'ki_name_dch'=> $update_array_id['ing_name_dch']
		// 								);
		// 					}
		// 					$this->db->insert('ingredients', $object);
		// 				}
		// 				$disp_order++;
		// 			}
		// 		}

		// 		$insert_aray[] = array(
		// 				'product_id'		=> $cp_prod_id->product_id,
		// 				'kp_id'				=> $value3,
		// 				'ki_id'				=> 0,
		// 				'ki_name'			=> ')',
		// 				'display_order'		=> $disp_order,
		// 				'kp_display_order'	=> $kp_order,
		// 				'date_added'		=> date('Y-m-d H:i:s'),
		// 				'is_obs_ing'		=> 0,
		// 				'have_all_id' 		=> 0,
		// 				'aller_type'		=> '0',
		// 				'aller_type_fr'		=> '0',
		// 				'aller_type_dch'	=> '0',
		// 				'allergence'		=> '0',
		// 				'allergence_fr'		=> '0',
		// 				'allergence_dch'	=> '0',
		// 				'sub_allergence'	=> '0',
		// 				'sub_allergence_fr'	=> '0',
		// 				'sub_allergence_dch'=> '0'
		// 		);
		// 		$this->db->insert_batch('products_ingredients', $insert_aray);
		// 	}
		// }
	}

	

	/*
	* function for check which using old cp but open bracket not in products ingredients 
	*/

	function check_open_brac_prod_ing_oldcp(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'0','products.new_cp'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {

						$array1 = $this->db->get_where( 'products_ingredients',array( 
								'product_id' => $obs_pro_id,
								'kp_id'      => $fdd_pro_id,
								'is_obs_ing' => 0,
								'ki_name' => '(',
								'ki_id'   => 0
							) 
						)->result_array();

						if(empty($array1))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_open_brac_prod_ing_oldcp.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_open_brac_prod_ing_oldcp.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);


							$product_id = $fdd_pro_id;
							$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
							$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
							$this->db->join('general_settings','general_settings.company_id = products.company_id');
							$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.obs_pro_id'=>$obs_pro_id));
							$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

							

							$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch,approval_status' );
							$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

							$this->fdb->select('i_id,aller_type_fr,aller_type_dch,aller_type,allergence,allergence_dch,allergence_fr,sub_allergence,sub_allergence_dch,sub_allergence_fr');
							$this->fdb->order_by('order','ASC');
							$update_array = $this->fdb->get_where('prod_ingredients',array('p_id'=>$product_id))->result();
					
							

							foreach ($cp_prod_ids as $cp_prod_id){
								if($cp_prod_id->language_id == '3'){
									$ing_var 	= '_fr';
									$disp_id 	= '_fr';
									$short_name = $res_short['p_short_name_fr'];
								}elseif($cp_prod_id->language_id == '2'){
									$ing_var 	= '_dch';
									$disp_id 	= '';
									$short_name = $res_short['p_short_name_dch'];
								}
								else{
									$ing_var 	= '';
									$disp_id 	= '_en';
									$short_name = $res_short['p_short_name'];
								}
								$this->db->select( 'fdd_pro_id' );
								$this->db->order_by( 'quantity', 'desc' );
								$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
								$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
								$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
								$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
								$kp_order 	= array_search( $product_id , $product_ids) + 1;
								
								
								$insert_aray = array();
								$disp_order = 1;
								$insert_aray[] = array(
										'product_id'		=> $cp_prod_id->product_id,
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
										'product_id'		=> $cp_prod_id->product_id,
										'kp_id'				=> $product_id,
										'ki_id'				=> 0,
										'ki_name'			=> '(',
										'display_order'		=> ++$disp_order,
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

								

									
								if ($res_short['approval_status'] == 1) {
									if(!empty($update_array)){
										foreach ($update_array as $key => $update_array_id){
											$disp_order 	 = $key+3; 

											$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
											$this->fdb->where('ing_id', $update_array_id->i_id);
											$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
											$ing_index 		= 'ing_name'.$ing_var;
											$aller_type_index = 'aller_type'.$ing_var;
											$allergence_type_index = 'allergence'.$ing_var;
											$sub_aller_type_index = 'sub_allergence'.$ing_var;
											$display_name 	= $ingredient_info_to_add[0]->$ing_index;

											$display_name1 	= $update_array[0]->$aller_type_index;
											$display_name2 	= $update_array[0]->$allergence_type_index;

											$display_name3 	= $update_array[0]->$sub_aller_type_index;

											$ki_id = $ingredient_info_to_add[0]->ing_id;
											$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

											if(!empty($enbr_result)){
												$ki_id = $enbr_result['ki_id'];
												$display_name = $enbr_result['ki_name'];
											}

											$insert_aray[] = array(
													'product_id'		=> $cp_prod_id->product_id,
													'kp_id'				=> $product_id,
													'ki_id'				=> $ki_id,
													'ki_name'			=> $display_name,
													'display_order'		=> $disp_order,
													'kp_display_order'	=> $kp_order,
													'date_added'		=> date('Y-m-d H:i:s'),
													'is_obs_ing'		=> 0,
													'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
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

											if ($ing_var == '_dch')
											{

												$insert_aray[$key+2]['aller_type_dch'] = $display_name1;
												$insert_aray[$key+2]['allergence_dch']	= $display_name2;
												$insert_aray[$key+2]['sub_allergence_dch']= $display_name3;
											}
											if ($ing_var == '_fr')
											{
												$insert_aray[$key+2]['aller_type_fr'] = $display_name1;
												$insert_aray[$key+2]['allergence_fr']	= $display_name2;
												$insert_aray[$key+2]['sub_allergence_fr']= $display_name3;
											}
											if ($ing_var == '')
											{
												$insert_aray[$key+2]['aller_type'] = $display_name1;
												$insert_aray[$key+2]['allergence']	= $display_name2;
												$insert_aray[$key+2]['sub_allergence']= $display_name3;
											}
										}
									}
								}

								

								$insert_aray[] = array(
									'product_id'		=> $cp_prod_id->product_id,
									'kp_id'				=> $product_id,
									'ki_id'				=> 0,
									'ki_name'			=> ')',
									'display_order'		=> ++$disp_order,
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
								$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$product_id,'is_obs_ing'=>0));
								$this->db->delete('products_ingredients');
								$this->db->insert_batch('products_ingredients', $insert_aray);
							}
						}
					}
				}
			}
		}
	}


	/*
	* function for check which using old cp but closed bracket not in products ingredients 
	*/
	function check_close_brac_prod_ing_oldcp(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'0','products.new_cp'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {

						$array1 = $this->db->get_where( 'products_ingredients',array( 
								'product_id' => $obs_pro_id,
								'kp_id'      => $fdd_pro_id,
								'is_obs_ing' => 0,
								'ki_name' => ')',
								'ki_id'   => 0
							) 
						)->result_array();

						if(empty($array1))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_closed_brac_prod_ing_oldcp.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_closed_brac_prod_ing_oldcp.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);

							$product_id = $fdd_pro_id;
							$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
							$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
							$this->db->join('general_settings','general_settings.company_id = products.company_id');
							$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.obs_pro_id'=>$obs_pro_id));
							$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

							

							$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch,approval_status' );
							$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

							$this->fdb->select('i_id,aller_type_fr,aller_type_dch,aller_type,allergence,allergence_dch,allergence_fr,sub_allergence,sub_allergence_dch,sub_allergence_fr');
							$this->fdb->order_by('order','ASC');
							$update_array = $this->fdb->get_where('prod_ingredients',array('p_id'=>$product_id))->result();
					
							

							foreach ($cp_prod_ids as $cp_prod_id){
								if($cp_prod_id->language_id == '3'){
									$ing_var 	= '_fr';
									$disp_id 	= '_fr';
									$short_name = $res_short['p_short_name_fr'];
								}elseif($cp_prod_id->language_id == '2'){
									$ing_var 	= '_dch';
									$disp_id 	= '';
									$short_name = $res_short['p_short_name_dch'];
								}
								else{
									$ing_var 	= '';
									$disp_id 	= '_en';
									$short_name = $res_short['p_short_name'];
								}
								$this->db->select( 'fdd_pro_id' );
								$this->db->order_by( 'quantity', 'desc' );
								$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
								$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
								$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
								$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
								$kp_order 	= array_search( $product_id , $product_ids) + 1;
								
								
								$insert_aray = array();
								$disp_order = 1;
								$insert_aray[] = array(
										'product_id'		=> $cp_prod_id->product_id,
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
										'product_id'		=> $cp_prod_id->product_id,
										'kp_id'				=> $product_id,
										'ki_id'				=> 0,
										'ki_name'			=> '(',
										'display_order'		=> ++$disp_order,
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

								

									
								if ($res_short['approval_status'] == 1) {
									if(!empty($update_array)){
										foreach ($update_array as $key => $update_array_id){
											$disp_order 	 = $key+3; 

											$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
											$this->fdb->where('ing_id', $update_array_id->i_id);
											$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
											$ing_index 		= 'ing_name'.$ing_var;
											$aller_type_index = 'aller_type'.$ing_var;
											$allergence_type_index = 'allergence'.$ing_var;
											$sub_aller_type_index = 'sub_allergence'.$ing_var;
											$display_name 	= $ingredient_info_to_add[0]->$ing_index;

											$display_name1 	= $update_array[0]->$aller_type_index;
											$display_name2 	= $update_array[0]->$allergence_type_index;

											$display_name3 	= $update_array[0]->$sub_aller_type_index;

											$ki_id = $ingredient_info_to_add[0]->ing_id;
											$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

											if(!empty($enbr_result)){
												$ki_id = $enbr_result['ki_id'];
												$display_name = $enbr_result['ki_name'];
											}

											$insert_aray[] = array(
													'product_id'		=> $cp_prod_id->product_id,
													'kp_id'				=> $product_id,
													'ki_id'				=> $ki_id,
													'ki_name'			=> $display_name,
													'display_order'		=> $disp_order,
													'kp_display_order'	=> $kp_order,
													'date_added'		=> date('Y-m-d H:i:s'),
													'is_obs_ing'		=> 0,
													'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
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

											if ($ing_var == '_dch')
											{

												$insert_aray[$key+2]['aller_type_dch'] = $display_name1;
												$insert_aray[$key+2]['allergence_dch']	= $display_name2;
												$insert_aray[$key+2]['sub_allergence_dch']= $display_name3;
											}
											if ($ing_var == '_fr')
											{
												$insert_aray[$key+2]['aller_type_fr'] = $display_name1;
												$insert_aray[$key+2]['allergence_fr']	= $display_name2;
												$insert_aray[$key+2]['sub_allergence_fr']= $display_name3;
											}
											if ($ing_var == '')
											{
												$insert_aray[$key+2]['aller_type'] = $display_name1;
												$insert_aray[$key+2]['allergence']	= $display_name2;
												$insert_aray[$key+2]['sub_allergence']= $display_name3;
											}
										}
									}
								}

								

								$insert_aray[] = array(
									'product_id'		=> $cp_prod_id->product_id,
									'kp_id'				=> $product_id,
									'ki_id'				=> 0,
									'ki_name'			=> ')',
									'display_order'		=> ++$disp_order,
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
								$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$product_id,'is_obs_ing'=>0));
								$this->db->delete('products_ingredients');
								$this->db->insert_batch('products_ingredients', $insert_aray);
							}
						 }
					}
				}
			}
		}
	}

	/*
	* function for check kp display order
	*/

	function check_kp_display_order(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$array2 = $this->db->get_where( 'products_ingredients',array( 
								'product_id' => $obs_pro_id,
								'kp_id'      => $fdd_pro_id,
								'is_obs_ing' => 0
							) 
						)->result_array();

						if(!empty($array2))
						{
						
							$product_id = $fdd_pro_id;

							$this->db->distinct();
							$this->db->select('kp_display_order');
							$kp = $this->db->get_where('products_ingredients',array('product_id'=>$obs_pro_id,'kp_id'=>$product_id,'is_obs_ing'=>0))->row_array();

								
							$this->db->select( 'fdd_pro_id' );
							$this->db->order_by( 'quantity', 'desc' );
							$this->db->where(array( 'obs_pro_id' => $obs_pro_id ));
							$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
							$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
							$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
							$kp_order 	= array_search( $product_id , $product_ids) + 1;

							if ($kp_order != $kp['kp_display_order']) {

								$fp = fopen(dirname(__FILE__)."/script_logs/check_kp_display_order.txt","a");
								fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
								fclose($fp);

								$fp = fopen(dirname(__FILE__)."/script_logs/check_kp_display_order.txt","a");
								fwrite($fp,"product_id: ".$obs_pro_id."------");
								fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
								fclose($fp);

								$this->db->where(array('product_id'=>$obs_pro_id,'kp_id'=>$product_id,'is_obs_ing'=>0));
								$this->db->update('products_ingredients',array('kp_display_order'=>$kp_order));
							}
						}
					}
				}
			}
		}
	}

	/*
	* function for check which contains extra own products which not in products table
	*/

	function check_extra_own_products_in_fdd(){
	
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'1'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$array2 = $this->db->get_where( 'products',array( 
								'id'=>$fdd_pro_id,
								'company_id'=>0,
								'categories_id'=>0,
								'subcategories_id'=>0
							) 
						)->result_array();

						if(empty($array2))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_own_products_in_fdd.txt","a");
								fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
								fclose($fp);
						
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_own_products_in_fdd.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);
						}
					}
				}
			}
		}
	}


	function check_recipe_weight_fdd(){
	
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		$array2 = $this->db->get_where( 'products',array( 
				'company_id !='=>0,
				'categories_id !='=>0,
				'recipe_weight !='=>0
			) 
		)->result_array();
		if (!empty($array2)) {
			foreach ($array2 as $key => $value) {
				$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.obs_pro_id'=>$value['id']))->result_array();
				if (empty($fdd_pro_id)) {

					// $this->db->where('id',$value['id']);
					// $this->db->update('products',array('recipe_weight'=>0));
					
					$fp = fopen(dirname(__FILE__)."/script_logs/check_empty_recipe.txt","a");
								fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
								fclose($fp);
						
					$fp = fopen(dirname(__FILE__)."/script_logs/check_empty_recipe.txt","a");
					fwrite($fp,"product_id: ".$value['id']."------");
					fclose($fp);
				}
			}
		}
	}

	/*
	* function for check which contains extra own products which not in fdd pro quantity table
	*/

	function check_extra_own_products_in_prod_ing(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');

		$this->db->distinct();
		$this->db->select('product_id,kp_id');
		$fdd_pro_id = $this->db->get_where('products_ingredients',array('is_obs_ing'=>'1'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'product_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'product_id' ] ] = array( $pro_id[ 'kp_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'product_id' ] ], $pro_id[ 'kp_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$this->db->distinct();
						$this->db->select('obs_pro_id,fdd_pro_id');
						$fdd_pro = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'1','obs_pro_id'=>$obs_pro_id,'fdd_pro_id'=>$fdd_pro_id))->result_array();
						
						if( empty( $fdd_pro )){
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_own_products_in_prod_ing.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_own_products_in_prod_ing.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"product_id: ".$fdd_pro_id."\n");
							fclose($fp);

							$this->db->where(array('product_id'=>$obs_pro_id,'kp_id'=>$fdd_pro_id,'is_obs_ing'=>1));
							$this->db->delete( 'products_ingredients');
						}
					}
				}
			}
		}
	}

	/*
	* function for check which contains extra own products which present in fdd pro quantity table but not in products ingredinets
	*/

	function check_extra_own_products_in_fdd_not_prodingre(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'1'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$array2 = $this->db->get_where( 'products_ingredients',array( 
								'product_id' => $obs_pro_id,
								'kp_id' => $fdd_pro_id,
								'is_obs_ing' => 1
							) 
						)->result_array();

						if(empty($array2))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_own_products_in_fdd_not_prodingre.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_extra_own_products_in_fdd_not_prodingre.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);
						}
					}
				}
			}
		}
	}

	/*
	* function for check own products in fdd pro quantity but not in products pending
	*/

	function check_own_prod_pending(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'1'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$array2 = $this->db->get_where( 'products_pending',array( 
								'product_id' => $fdd_pro_id
							) 
						)->result_array();

						if(empty($array2))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_own_prod_pending.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"product_id: ".$fdd_pro_id."\n");
							fclose($fp);
						}
					}
				}
			}
		}
	}

	function check_gs1_products_request_gs1(){
		$this->db->distinct( 'fdd_pro_id' );
   		$this->db->select( 'fdd_pro_id' );
   		$this->db->where( 'is_obs_product', 0 );
   		$fdd_pro_ids = $this->db->get( 'fdd_pro_quantity' )->result_array();
   		$fdd_pro_ids = array_column( $fdd_pro_ids, 'fdd_pro_id' );
   		

   		$this->fdb->select( 'p_id' );
   		$this->fdb->where_in( 'p_id', $fdd_pro_ids );
   		$this->fdb->where(array('product_type'=> '1', 'approval_status'=>0) );
   		$new_fdd_pro_ids = $this->fdb->get( 'products' )->result_array();

   		$new_fdd_pro_ids = array_column( $new_fdd_pro_ids, 'p_id' );

   		$this->db->select('fdd_pro_quantity.fdd_pro_id as gs1_pid, products.company_id');
   		$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
   		$this->db->where( 'is_obs_product', 0 );
   		$this->db->where_in( 'fdd_pro_id', $new_fdd_pro_ids );
   		//$this->db->where( array('products.prod_sent'=> '0','products.new_cp'=>'0' ));
   		$comp_fdd_pro_ids = $this->db->get( 'fdd_pro_quantity' )->result_array();
   		
   		$this->db->select( 'gs1_pid, company_id' );
   		$gs1_pids = $this->db->get( 'request_gs1' )->result_array();


   		
		function my_serialize(&$arr,$pos){
			$arr = serialize($arr);
		}

		function my_unserialize(&$arr,$pos){
		  	$arr = unserialize($arr);
		}
		array_walk( $gs1_pids, 'my_serialize' );
		array_walk( $comp_fdd_pro_ids, 'my_serialize' );

		$arr1 = array_values(array_unique(array_diff( $comp_fdd_pro_ids , $gs1_pids)));
   		array_walk( $arr1, 'my_unserialize' );

   		echo "<pre>";
   		print_r($arr1);die;


   		// foreach ($arr1 as $key => $value) {
   		// 	$value['date_added'] =  date( "Y-m-d H:i:s" );
   		// 	$this->db->insert('request_gs1',$value);
   		// }
	}

	function check_fdd_products_fddpro_new(){
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		

		$this->fdb->select( 'p_id' );
		$new_data = $this->fdb->get( 'products' )->result_array( );
		$new_data = array_column($new_data, 'p_id');

		$this->fdb->select( 'p_id' );
		$old_data = $this->fdb->get( 'products2' )->result_array( );
		$old_data = array_column($old_data, 'p_id');
		
		$result  = array_diff( $old_data,  $new_data );
		$result = array_values($result );

		foreach ($result as $key => $id) {
			$old_dat = $this->fdb->get_where( 'products2',array('p_id'=>$id) )->row_array( );
			if(!empty($old_dat))
			{
				$this->fdb->insert('products',$old_dat);
			}
			
		}
		
		echo "aa";;die;
	}

	/*
	* function for check deleted products
	*/

	function check_fdd_products_fddpro(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_quantity.is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}
		}

		if( !empty( $product_final_array ) ){
			$repeated_ing = array( );
			
			foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
				if( !empty( $fdd_pro_id_arr ) ){
					foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
						$array2 = $this->fdb->get_where( 'products',array( 
								'p_id' => $fdd_pro_id
							) 
						)->result_array();

						if(empty($array2))
						{
							$fp = fopen(dirname(__FILE__)."/script_logs/check_fdd_products_fddpro.txt","a");
							fwrite($fp,"date: ".date('Y-m-d H:i:s')."\n");
							fclose($fp);
							$fp = fopen(dirname(__FILE__)."/script_logs/check_fdd_products_fddpro.txt","a");
							fwrite($fp,"product_id: ".$obs_pro_id."------");
							fwrite($fp,"fdd_pro_id: ".$fdd_pro_id."\n");
							fclose($fp);
						}
					}
				}
			}
			
		}
	}

	function get_gs1_not_in_products(){
		$arr = array();
		$this->db->distinct('gs1_pid');
		$this->db->select('gs1_pid');
		$res = $this->db->get('request_gs1')->result_array();
		foreach ($res as $key => $value) {
			$da = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_id'=>$value['gs1_pid'],'is_obs_product'=>0))->row_array();
			if(empty($da))
			{
				$arr[] = $value['gs1_pid'];
				//$this->db->where(array('gs1_pid'=>$value['gs1_pid']));
				//$this->db->delete('request_gs1');
			}
		}
		echo "<pre>";
		print_r($arr);die;
	}

	function get_gs1_not_in_products1(){
		$arr = array();
		$this->db->distinct('gs1_pid');
		$this->db->select('gs1_pid');
		$res = $this->db->get('request_gs1')->result_array();
		
		foreach ($res as $key => $value) {
			$da = $this->fdb->get_where('products',array('p_id'=>$value['gs1_pid']))->row_array();
			if(empty($da))
			{
				$arr[] = $value['gs1_pid'];
				//$this->db->where(array('gs1_pid'=>$value['gs1_pid']));
				//$this->db->delete('request_gs1');
			}
		}
		
		echo "<pre>";
		print_r($arr);die;
	}

	function get_fdd_pro_quan_not_in_products1(){
		$arr = array();
		$this->db->distinct('fdd_pro_id');
		$this->db->select('fdd_pro_id');
		$res = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0))->result_array();
		
		foreach ($res as $key => $value) {
			$da = $this->fdb->get_where('products',array('p_id'=>$value['fdd_pro_id']))->row_array();
			if(empty($da))
			{
				$arr[] = $value['fdd_pro_id'];
				//$this->db->where(array('gs1_pid'=>$value['gs1_pid']));
				//$this->db->delete('request_gs1');
			}
		}
		
		echo "<pre>";
		print_r($arr);die;
	}

	function gs1_remove_list(){
		error_reporting(E_ALL);
		$this->db->distinct( 'request_gs1.company_id' );
		$this->db->select( 'request_gs1.company_id, company.email, general_settings.language_id' );		
		$this->db->where( 'request_gs1.is_mail_sent', '1' );
		$this->db->join( 'company', 'request_gs1.company_id = company.id' );
		$this->db->join( 'general_settings', 'company.id = general_settings.company_id' );
		$companies = $this->db->get( 'request_gs1' )->result_array();
		$counter = 1;
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle( _('removed gs1') );

		
		$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('COMPANY') )->getStyle('A'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('RECIPE NAME') )->getStyle('B'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('GS1 PRODUCT') )->getStyle('C'.$counter)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('EAN') )->getStyle('D'.$counter)->getFont()->setBold(true);
		if( !empty( $companies ) ) {
			foreach ( $companies as $key => $value ) {
				$this->db->distinct();
				$this->db->select( 'gs1_pid' );
				$this->db->where( 'company_id', $value[ 'company_id' ] );
				$this->db->where( 'is_mail_sent', '1' );
				$gs1_pids = $this->db->get( 'request_gs1' )->result_array();
				$gs1_pids = array_column( $gs1_pids, 'gs1_pid' );

				if( !empty( $gs1_pids ) ){
					foreach ($gs1_pids as $k1 => $v1) {
						$this->db->select( 'products.proname, products.id' );
						$this->db->distinct( 'products.id' );
						$this->db->join( 'fdd_pro_quantity', 'fdd_pro_quantity.obs_pro_id = products.id' );
						$this->db->join( 'request_gs1', 'request_gs1.gs1_pid = fdd_pro_quantity.fdd_pro_id' );
						$this->db->where( 'products.company_id', $value[ 'company_id' ] );
						$this->db->where( 'fdd_pro_quantity.fdd_pro_id', $v1 );
						$recipes = $this->db->get( 'products' )->result_array();

						if( $value[ 'language_id' ] == 1 || $value[ 'language_id' ] == 2 ){
							$p_name = 'p_name_dch';
						} else if( $value[ 'language_id' ] == 3 ){
							$p_name = 'p_name_fr';
						} else {
							$p_name = 'p_name_dch';
						}
						
						$this->fdb->select( 'products.p_id, products.barcode, products.'.$p_name.' as p_name' );
						$this->fdb->where( 'p_id', $v1 );
						$products_info 	= $this->fdb->get( 'products' )->row_array();

						if( !empty($recipes) ){
							foreach ($recipes as $k2 => $v2) {
								$counter++;
								$this->excel->getActiveSheet()->setCellValue('A'.$counter, $value[ 'company_id' ] );
								$this->excel->getActiveSheet()->setCellValue('B'.$counter, stripslashes($v2['proname']) );
								$this->excel->getActiveSheet()->setCellValue('C'.$counter, stripslashes($products_info['p_name']) );
								$this->excel->getActiveSheet()->setCellValue('D'.$counter, $products_info['barcode'] );
							}
						}
					}
				}
			}
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		
		$filename = "removed_gs1.xls";
		$path = dirname(__FILE__).'/../../assets/';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save($path.$filename,'php://output');

		$content =  file_get_contents($path.$filename);

		file_put_contents($path.$filename, $content);
		
		echo 'done'; die;
	}

	function sheets_status_unapprove1(){
   		$arr = array();
   		$this->db->select( 'data_sheet' );
   		$this->db->where( 'checked', '0' );
   		$sheets = $this->db->get( 'pws_products_sheets' )->result_array();
   		
   		foreach ($sheets as $key => $value) {	
   			//$exist = $this->db->get_where( 'products',array('id'=>$value['obs_pro_id']) )->result_array();

			$exist = $this->fdb->get_where( 'products',array('data_sheet'=>$value['data_sheet'],'approval_status'=>1) )->result_array();

   			if(!empty($exist))
   			{
   				$arr[] = $value['data_sheet'];
   			}
   		}
   		

   		// foreach ($arr as $key => $value) {
   		// 	$this->db->where(array('obs_pro_id'=>$value));
   		// 	$this->db->delete('pws_products_sheets');
   		// }


   		
   		echo "<pre>";
   		print_r($arr);die;
   	}

   	function get_delete_products_ing(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		$product_final_array = array();

		
		$this->fdb->select('*');
		$this->fdb->like('p_date_added','2017-11-15');
		$res = $this->fdb->get_where('products16',array('p_date_added !='=>'2017-11-15 00:00:00'))->result_array();
		
		foreach ($res as $key => $value) {
			
			$res2 = $this->fdb->get_where('products',array('p_id'=>$value['p_id'],'approval_status'=>$value['approval_status']))->row_array();
			
			if (!empty($res2)) {

				$this->db->distinct();
				$this->db->select('obs_pro_id,fdd_pro_id');
				$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_id'=>$value['p_id'],'is_obs_product'=>'0'))->result_array();

				$product_final_array = array( );
				if( !empty( $fdd_pro_id ) ){
					foreach ( $fdd_pro_id as $key => $pro_id ) {
						if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
							$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
						}else{
							array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
						}
					}
				}	
				if( !empty( $product_final_array ) ){
					$repeated_ing = array( );
					foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
						if( !empty( $fdd_pro_id_arr ) ){
							foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {
								$product_id = $fdd_pro_id;
								$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
								$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
								$this->db->join('general_settings','general_settings.company_id = products.company_id');
								$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0,'fdd_pro_quantity.obs_pro_id'=>$obs_pro_id));
								$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

								


								$this->fdb->replace('products',$value);


								$this->fdb->select( 'p_short_name, p_short_name_fr, p_short_name_dch,approval_status' );
								$res_short = $this->fdb->get_where( 'products', array( 'p_id' => $product_id ) )->row_array();

								$this->fdb->select('i_id,aller_type_fr,aller_type_dch,aller_type,allergence,allergence_dch,allergence_fr,sub_allergence,sub_allergence_dch,sub_allergence_fr');
								$this->fdb->order_by('order','ASC');
								$update_array = $this->fdb->get_where('prod_ingredients',array('p_id'=>$product_id))->result();
						
								

								foreach ($cp_prod_ids as $cp_prod_id){
									if($cp_prod_id->language_id == '3'){
										$ing_var 	= '_fr';
										$disp_id 	= '_fr';
										$short_name = $res_short['p_short_name_fr'];
									}elseif($cp_prod_id->language_id == '2'){
										$ing_var 	= '_dch';
										$disp_id 	= '';
										$short_name = $res_short['p_short_name_dch'];
									}
									else{
										$ing_var 	= '';
										$disp_id 	= '_en';
										$short_name = $res_short['p_short_name'];
									}
									$this->db->select( 'fdd_pro_id' );
									$this->db->order_by( 'quantity', 'desc' );
									$this->db->where(array( 'obs_pro_id' => $cp_prod_id->product_id ));
									$res 		= $this->db->get( 'fdd_pro_quantity' )->result_array();
									$res 		= array_map("unserialize", array_unique(array_map("serialize", $res)));
									$product_ids= array_column( json_decode( json_encode($res), true ), 'fdd_pro_id');
									$kp_order 	= array_search( $product_id , $product_ids) + 1;
									
									$insert_aray = array();
									$disp_order = 1;
									$insert_aray[] = array(
											'product_id'		=> $cp_prod_id->product_id,
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
											'product_id'		=> $cp_prod_id->product_id,
											'kp_id'				=> $product_id,
											'ki_id'				=> 0,
											'ki_name'			=> '(',
											'display_order'		=> ++$disp_order,
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

									

									
									if ($res_short['approval_status'] == 1) {
										if(!empty($update_array)){
											foreach ($update_array as $key => $update_array_id){
												$disp_order 	 = $key+3; 

												$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
												$this->fdb->where('ing_id', $update_array_id->i_id);
												$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
												$ing_index 		= 'ing_name'.$ing_var;
												$aller_type_index = 'aller_type'.$ing_var;
												$allergence_type_index = 'allergence'.$ing_var;
												$sub_aller_type_index = 'sub_allergence'.$ing_var;
												$display_name 	= $ingredient_info_to_add[0]->$ing_index;

												$display_name1 	= $update_array[0]->$aller_type_index;
												$display_name2 	= $update_array[0]->$allergence_type_index;

												$display_name3 	= $update_array[0]->$sub_aller_type_index;

												$ki_id = $ingredient_info_to_add[0]->ing_id;
												$enbr_result = $this->get_e_current_nbr($ki_id,$cp_prod_id->enbr_status,$ing_var);

												if(!empty($enbr_result)){
													$ki_id = $enbr_result['ki_id'];
													$display_name = $enbr_result['ki_name'];
												}

												$insert_aray[] = array(
														'product_id'		=> $cp_prod_id->product_id,
														'kp_id'				=> $product_id,
														'ki_id'				=> $ki_id,
														'ki_name'			=> $display_name,
														'display_order'		=> $disp_order,
														'kp_display_order'	=> $kp_order,
														'date_added'		=> date('Y-m-d H:i:s'),
														'is_obs_ing'		=> 0,
														'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
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

												if ($ing_var == '_dch')
												{

													$insert_aray[$key+2]['aller_type_dch'] = $display_name1;
													$insert_aray[$key+2]['allergence_dch']	= $display_name2;
													$insert_aray[$key+2]['sub_allergence_dch']= $display_name3;
												}
												if ($ing_var == '_fr')
												{
													$insert_aray[$key+2]['aller_type_fr'] = $display_name1;
													$insert_aray[$key+2]['allergence_fr']	= $display_name2;
													$insert_aray[$key+2]['sub_allergence_fr']= $display_name3;
												}
												if ($ing_var == '')
												{
													$insert_aray[$key+2]['aller_type'] = $display_name1;
													$insert_aray[$key+2]['allergence']	= $display_name2;
													$insert_aray[$key+2]['sub_allergence']= $display_name3;
												}
											}
										}
									}

									

									$insert_aray[] = array(
											'product_id'		=> $cp_prod_id->product_id,
											'kp_id'				=> $product_id,
											'ki_id'				=> 0,
											'ki_name'			=> ')',
											'display_order'		=> ++$disp_order,
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


									$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$product_id,'is_obs_ing'=>0));
									$this->db->delete('products_ingredients');

									$this->db->insert_batch('products_ingredients', $insert_aray);
								}
							}
						}
					}
				}
			}
		}
	}

	function all_prod_approve(){
		
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		$product_final_array = array();

		
		$this->fdb->select('*');
		$res = $this->fdb->get_where('products',array('approval_status'=>0,'product_type'=>'0'))->result_array();
		if (!empty($res)) {
			foreach ($res as $key => $value) {
				$res2 = $this->fdb->get_where('products1',array('p_id'=>$value['p_id'],'approval_status'=>1,'product_type'=>'0'))->result_array();
				if (!empty($res2)) {
					$product_final_array[] = $value['p_id'];
				}
			}
		}
		
		echo "<pre>";
		print_r($product_final_array);die;
	}

	function count_product_ingredinets_fdd(){
		ini_set('max_execution_time', 1000);
		ini_set('memory_limit', '-1');
		
		$this->db->distinct();
		$this->db->select('obs_pro_id,fdd_pro_id');
		$fdd_pro_id = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>'0'))->result_array();

		$product_final_array = array( );
		if( !empty( $fdd_pro_id ) ){
			foreach ( $fdd_pro_id as $key => $pro_id ) {
				if( empty( $product_final_array[  $pro_id[ 'obs_pro_id' ] ] ) ){
					$product_final_array[  $pro_id[ 'obs_pro_id' ] ] = array( $pro_id[ 'fdd_pro_id' ] );
				}else{
					array_push( $product_final_array[  $pro_id[ 'obs_pro_id' ] ], $pro_id[ 'fdd_pro_id' ] );
				}
			}

			if (!empty($product_final_array)) {
				$arr = array();
				foreach ( $product_final_array as $obs_pro_id => $fdd_pro_id_arr ) {
					if( !empty( $fdd_pro_id_arr ) ){
						foreach ( $fdd_pro_id_arr as $key => $fdd_pro_id ) {

							$array1 = $this->db->get_where( 'products_ingredients',array( 
									'product_id' => $obs_pro_id,
									'kp_id'      => $fdd_pro_id,
									'is_obs_ing' => 0,
									'ki_id !='   => 0
								) 
							)->result_array();

							
							$app =$this->fdb->get_where('products',array('approval_status'=>1,'p_id'=>$fdd_pro_id,'review&fixed'=>1))->result_array();

							if (!empty($app)) {
								
								$array2 = $this->fdb->get_where( 'prod_ingredients',array( 
										'p_id'      => $fdd_pro_id
									) 
								)->result_array();
								

								if (count($array1) != count($array2)) {
									if (!in_array($fdd_pro_id, $arr)) {
										$arr[] = $fdd_pro_id;
									}
								}
							}
						}
					}
				}
				echo "<pre>";
				print_r($arr);die;
			}
		}
	}

	function check_deleted_products(){
		$arr = array('2838','2839','2840','2841','2842','2843','2844','2845','2846','2847','2848','2849','2850','70983','273385','273386');
		
			foreach ($arr as $key => $value) {
				$exist = $this->db->get_where('fdd_pro_quantity_17jan',array('obs_pro_id' => $value))->result_array();
			
				if (!empty($exist)) {
					foreach ($exist as $key => $value) {
						$this->db->insert('fdd_pro_quantity',$value);
					}
				}
			}
		}

	function updateIngredients($update_array = array(), $product_id = 0,$ing_var = '_dch'){
		

		$this->db->select('fdd_pro_quantity.obs_pro_id AS product_id,general_settings.language_id,general_settings.enbr_status');
		$this->db->join('products','fdd_pro_quantity.obs_pro_id = products.id');
		$this->db->join('general_settings','general_settings.company_id = products.company_id');
		$this->db->where(array('fdd_pro_quantity.fdd_pro_id'=>$product_id,'fdd_pro_quantity.is_obs_product'=>0));
		$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();

		$this->fdb->where(array('p_id'=>$product_id));
		$this->fdb->delete('prod_ingredients');

		if(!empty($update_array)){
			foreach ($update_array as $key => $ing_info){
				$exp_data = explode('##',$ing_info);
				$ing_info = $exp_data[0];
				$ing_aller = $exp_data[1];

				$this->fdb->select('ing_id,ing_name'.$ing_var.'');
				$this->fdb->where('ing_id', $ing_info);
				$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
				if ($ing_aller != '0')
				{
					$ing_aller = explode('#',$ing_aller);
					$aller_type = $ing_aller[0];
					$allergence = $ing_aller[1];
					$sub_allergence = $ing_aller[2];
					$new_allergence = $ing_aller[3];
					$this->fdb->insert('prod_ingredients', array('p_id' => $product_id, 'i_id' => $ingredient_info_to_add[0]->ing_id,'order'=>$key+1, 'date_added' => date('Y-m-d H:i:s'),'aller_type'=>$aller_type,'allergence'=>$allergence,'sub_allergence'=>$sub_allergence,'new_allergence'=>$new_allergence));
				}
				else {
					if(!empty($ingredient_info_to_add)){
						$this->fdb->insert('prod_ingredients', array('p_id' => $product_id, 'i_id' => $ingredient_info_to_add[0]->ing_id,'order'=>$key+1, 'date_added' => date('Y-m-d H:i:s')));
					}
				}
			}
		}
		foreach ($cp_prod_ids as $cp_prod_id){
			if($cp_prod_id->language_id == '3'){
				$ing_var = '_fr';
				$disp_id = '_fr';
			}elseif($cp_prod_id->language_id == '2'){
				$ing_var = '_dch';
				$disp_id = '';
			}
			else{
				$ing_var = '_dch';
				$disp_id = '_en';
			}


			$this->db->where(array('product_id'=>$cp_prod_id->product_id,'kp_id'=>$product_id,'ki_id'=>0));
			$res = $this->db->get('products_ingredients')->result_array();
			if(!empty($res)){
				$kp_order = $res[0]['kp_display_order'];
			}else{
				$kp_order = 0;
			}
			$this->db->where( array( 'product_id' => $cp_prod_id->product_id, 'kp_id' => $product_id ) );
			$this->db->where( 'ki_id !=', 0 );
			$this->db->delete( 'products_ingredients' );
			if(!empty($update_array)){
				foreach ($update_array as $key => $update_array_id){
					$exp_data 			= explode('##',$update_array_id);
					$update_array_id 	= $exp_data[0];
					$ing_aller 			= $exp_data[1];

					$this->fdb->select('ing_id, ing_name, ing_name_fr, ing_name_dch, have_all_id');
					$this->fdb->where('ing_id', $update_array_id);
					$ingredient_info_to_add = $this->fdb->get('ingredients')->result();
					$display_name 	= '';
					$ing_name 		= $ingredient_info_to_add[0]->ing_name;
					$ing_name_fr 	= $ingredient_info_to_add[0]->ing_name_fr;
					$ing_name_dch 	= $ingredient_info_to_add[0]->ing_name_dch;

					$ki_id 		 = $ingredient_info_to_add[0]->ing_id;
					$enbr_result = $this->get_e_current_nbr( $ki_id, $cp_prod_id->enbr_status, $ing_var );

					if( !empty( $enbr_result ) ){
						$ki_id 			= $enbr_result[ 'ki_id' ];
						$display_name 	= $enbr_result[ 'ki_name' ];
					}

					if ( $ing_aller != '0' )
					{
						$ing_aller 		= explode('#',$ing_aller);
						$aller_type 	= $ing_aller[0];
						$allergence 	= $ing_aller[1];
						$sub_allergence = $ing_aller[2];
						$insert_aray 	= array(
							'product_id'		=> $cp_prod_id->product_id,
							'kp_id'				=> $product_id,
							'ki_id'				=> $ki_id,
							'display_order'		=> $key+3,
							'kp_display_order'	=> $kp_order,
							'date_added'		=> date('Y-m-d H:i:s'),
							'is_obs_ing'		=> 0,
							'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id,
							'aller_type'		=> $aller_type,
							'allergence'		=> $allergence,
							'sub_allergence'	=> $sub_allergence
						);
					}
					else
					{
						$insert_aray = array(
								'product_id'		=> $cp_prod_id->product_id,
								'kp_id'				=> $product_id,
								'ki_id'				=> $ki_id,
								'display_order'		=> $key+3,
								'kp_display_order'	=> $kp_order,
								'date_added'		=> date('Y-m-d H:i:s'),
								'is_obs_ing'		=> 0,
								'have_all_id' 		=> $ingredient_info_to_add[0]->have_all_id
						);
					}
					$this->db->insert('products_ingredients', $insert_aray);

					$this->db->select('ki_id');
					$res = $this->db->get_where('ingredients', array('ki_id' => $ki_id) )->row_array();

					if( empty($res) ){
						if( $cp_prod_id->enbr_status == 1 ){
							if( !empty( $enbr_result ) ){
								$object = array(
											'ki_id' 	 => $ki_id,
											'ki_name'	 => $display_name,
											'ki_name_fr' => $display_name,
											'ki_name_dch'=> $display_name
										);
							}
							else{
								$object = array(
										'ki_id' 	 => $ki_id,
										'ki_name'	 => $ing_name,
										'ki_name_fr' => $ing_name_fr,
										'ki_name_dch'=> $ing_name_dch
									);
							}
						}
						if( $cp_prod_id->enbr_status == 2 ){
							$object = array(
										'ki_id' 	 => $ki_id,
										'ki_name'	 => $ing_name,
										'ki_name_fr' => $ing_name_fr,
										'ki_name_dch'=> $ing_name_dch
									);
						}
						$this->db->insert('ingredients', $object);
					}
				}
			}
		}
	}

	function get_fdd_prod_ids_arr() {
		$result = explode(",", '184019,11170,14694,15155,153681,44883,155071,13118,52511,40341,14541,159094,142422,23256,4711,60061,147687,194388,50904,138684,14486,3453,17744,167231,166200,177664,66623,25815,187120,157262,52222,38889,58329,194757,6578,26947,28268,59966,17955,14933,8908,187244,48252,22448,184237,147935,24972,182710,35103,59505,61616,184790,47396,185595,15877,185827,7140,22630,195314,195315,50641,37999,9193,17106,12961,11502,16977,57667,44567,42070,199917,168475,47733,53045,149770,54023,16224,166040,40003,141808,153232,187198,195127,195125,195126,19650,145836,7025,7250,8978,2329,4897,138931,33837,139524,159674,64853,63786,200007,199922,14314,2666,11516,8053,5162,191736,14435,53097,140255,12384,200083,200159,200004,186754,186753,3139,7023,187197,544,17354,186957,195148,187342,199866,19831,138757,149982,12191,9410,199820,9443,151156,199811,199824,199807,199809,199821,199822,199814,199819,199808,199812,199810,199813,15610,195066,186838,13730,13740,64011,183075,18160,15156,168771,6562,35428,9697,64812,47434,56597,142217,48904,1218,45827,185633,184620,184630,185815,7894,14472,15536,18300,149329,29152,184622,17434,17708,17646,186096,186163,16225,17709,12045,199803,185783,6304,186750,200128,185590,18086,200499,17131,10762,4295,14111,2330,5329,185614,7182,10239,7069,10594,10946,406,6586,5165,7294,6975,6900,6553,6515,7099,6901,9948,6982,7178,6912,31163,138793');
		return $result;
	}


	function update_system_selected_col_in_company_table(){
		$this->db->select('id,system_selected');
		$result = $this->db->get('company')->result_array();
		foreach ($result as $value) {
			$this->db->where(array('id' => $value['id']));
			$sys = json_encode(array($value['system_selected'],""));
			$data = array('system_selected' => $sys);
			$this->db->update('company',$data);
		}
	}

	function update_fdd_quantity() {
		$this->db->distinct( 'obs_pro_id' );
		$this->db->select( 'obs_pro_id' );
		$this->db->join( 'products', 'products.id = fdd_pro_quantity.obs_pro_id' );
		$result = array_column( $this->db->get_where( 'fdd_pro_quantity',array('comp_id' => '') )->result_array(), 'obs_pro_id' );
		
		foreach ( $result as $key => $obs_pro_id ) {
			$this->db->select( 'company_id' );
			$this->db->where( 'id', $obs_pro_id );
			$comp_id = $this->db->get( 'products' )->row_array();

			$this->db->where( 'obs_pro_id', $obs_pro_id );
			$this->db->update( 'fdd_pro_quantity', array( 'comp_id' => $comp_id[ 'company_id' ] ) );
		}
	}

	function tests(){
		$arr = array();
		
		$this->db->distinct();
		$this->db->select('gs1_pid');
		$res = $this->db->get_where('request_gs1',array('request_status'=>1,'fixed_prod'=>0))->result_array();
		if (!empty($res)) {
			foreach ($res as $key => $value) {
				$exist = $this->fdb->get_where('products',array('p_id'=>$value['gs1_pid'],'approval_status'=>0,'assigned_to_dietist'=>0))->row_array();	
				if (!empty($exist)) {
					$arr[] = $value['gs1_pid'];
				}
			}
		}
		echo count($arr);die;
	}


	function update_semi(){
		$res = $this->db->query("SELECT * FROM `products` WHERE `direct_kcp` = 1 AND `semi_product` = 0 AND company_id != 0 ORDER BY `direct_kcp` ASC")->result_array();
		foreach ($res as $key => $value) {
			$exi = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$value['id']))->result_array();
			if (empty($exi)) {
				 $this->db->where('id',$value['id']);
				 $this->db->update('products',array('direct_kcp'=>0));
			}
		}
	}

	function gs1_date(){
		$sel = $this->db->get_where('request_gs1',array('date_added'=>'0000-00-00 00:00:00'))->result_array();
		if (!empty($sel)) {
			foreach ($sel as $key => $value) {
				$this->db->select('products.procreated');
				$this->db->join('products','products.id = fdd_pro_quantity.obs_pro_id');
				$sel1 = $this->db->get_where('fdd_pro_quantity',array('fdd_pro_id'=>$value['gs1_pid'],'is_obs_product'=>0))->row_array();
				if (!empty($sel1)) {
					$this->db->where(array('gs1_pid'=>$value['gs1_pid']));
					$this->db->update('request_gs1',array('date_added'=>$sel1['procreated']));
				}
			}
		}
	}

	function update_semi_id_in_quantity( $from, $to ) { 
		error_reporting(-1);
		ini_set('display_errors', 1);
		// die('sds');
		$this->staging = $this->load->database('staging',true);

		$this->staging->select( 'proname, products.id, semi_product, categories.name, subcategories.subname' );
		$this->staging->join( 'categories', 'categories.id = products.categories_id' );
		$this->staging->join( 'subcategories', 'subcategories.id = products.subcategories_id' );
		$this->staging->where( 'semi_product !=', 0 );
		$this->staging->where( 'products.company_id', $from );
		$products = $this->staging->get('products')->result_array();
// echo "<pre>";
// print_r ($products);die;
// echo "</pre>";
		// $new_products = array();
		foreach ( $products as $key => $value ) {
			$this->db->select( 'products.id' );
			$this->db->join( 'categories', 'categories.id = products.categories_id' );
			$this->db->join( 'subcategories', 'subcategories.id = products.subcategories_id' );
			$this->db->where( 'proname', $value[ 'proname' ] );
			$this->db->where( 'name', $value[ 'name' ] );
			$this->db->where( 'subname', $value[ 'subname' ] );
			$this->db->where( 'semi_product', $value[ 'semi_product' ] );
			$this->db->where( 'products.company_id', $to );
			$new_products = $this->db->get('products')->row_array();
			// echo "<pre>";
			// print_r ($new_products);die;
			// echo "</pre>";
			if( !empty( $new_products ) ) {
				$this->db->where( 'semi_product_id', $value[ 'id' ] );
				$this->db->update( 'fdd_pro_quantity', array( 'semi_product_id' => $new_products[ 'id' ] ) );	
			}
		}
		die("lp");
		// echo "<pre>";
		// print_r (($new_products));die;
		// echo "</pre>";
	}

	public function updateInfoDesk()
	{
	    $companyids = [14,15,16,17,18,20,21,22,26,27,28];
	    $this->db->where_not_in('type_id', $companyids);
	    $this->db->update('company',array('enable_infodesk' => 1));
	}

	function sub_admin_slug(){
		$this->db->select('id, company_name');
		$comp_name = $this->db->get_where('company', array( 'company_slug' => '', 'via_api' => '0' ) )->result_array();
		
		foreach ($comp_name as $key => $value) {
			$slug = $this->create_slug( $value['company_name'] );

			$this->db->where('id', $value['id']);
			$this->db->update('company', array( 'company_slug' => $slug ));
		}
		echo 'done'; die;
	}

	function create_slug($companyname){
		$slug_str = strtolower(trim($companyname));
	
		$slug_str = preg_replace('/\s+/', '-', $slug_str);
		$slug_str = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $slug_str));
		$slug_str = preg_replace('/-+/', '-', $slug_str);
		$slug_str = rtrim($slug_str, "-");
		
		
		$company_slugs_array = $this->db->select('company_slug')->get('company')->result();
		$company_slugs = array();
		foreach($company_slugs_array as $company_slug){
			$company_slugs[] = $company_slug->company_slug;
		}
		$old_str = $slug_str;
		for($company_counter=2;;$company_counter++){
			if(in_array($slug_str,$company_slugs)){
			  $slug_str = $old_str.'-'.$company_counter;
			}else{
			  break;
			 }
		
		}
        return $slug_str;
    }

    function api_for_all(){
    	$this->db->select('company_id');
    	$in_api_table = $this->db->get('api')->result_array();
    	$in_api_table = array_column( $in_api_table, 'company_id' );

    	$this->db->select('id, company_name');
    	$this->db->where_not_in('id', $in_api_table);
		$comp_name = $this->db->get_where('company', array( 'via_api' => '0' ) )->result_array();
		echo "<pre>";
		print_r ($comp_name);die;
		echo "</pre>";
		foreach ($comp_name as $key => $value) {
			$this->generateApi($value['id']);			
		}
		echo "done"; die;
    }

    function genRandomString($length = 10, $type = 'Both') {
		$string = '';

		if ($type == 'Both')
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		elseif ($type == 'Num')
			$characters = '0123456789';
		elseif ($type == 'Str')
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		for($p = 0; $p < $length; $p ++) {
			$string .= @$characters [mt_rand ( 0, strlen ( $characters ) )];
		}

		return $string;
	}

    function generateApi($company_id) {
		do {
			$api_secret = $this->genRandomString ( 10, 'Both' );
			$this->db->where ( 'api_secret', $api_secret );
			$api1 = $this->db->get ( 'api' )->result ();
		} while ( ! empty ( $api1 ) || strlen ( $api_secret ) < 10 );

		do {
			$api_id = $this->genRandomString ( 6, 'Num' );
			$this->db->where ( 'api_id', $api_id );
			$api2 = $this->db->get ( 'api' )->result ();
		} while ( ! empty ( $api2 ) || strlen ( $api_id ) < 6 );

		$this->db->where ( 'id', $company_id );
		$company = $this->db->get ( 'company' )->row ();

		$domain = '';
		if (! empty ( $company )) {
			$website = ($company->website) ? ($company->website) : ($company->domain);
			$url = parse_url ( $website ); // , PHP_URL_HOST);

			// print_r($url);

			$domain = str_replace ( 'www.', '', isset ( $url ['path'] ) ? $url ['path'] : $url ['host'] );
		}

		// echo $api_secret.'--'.$api_id.'--'.$domain;
		// die();

		$insert = array ();

		$insert ['api_id'] = $api_id;
		$insert ['api_secret'] = $api_secret;
		$insert ['domain'] = $domain;
		$insert ['company_id'] = $company_id;

		$this->db->insert ( 'api', $insert );
	}

	function approval_review(){
		
		$this->db->distinct('fdd_pro_id');
		$this->db->select('fdd_pro_id');
		$all_fdd = $this->db->get_where('fdd_pro_quantity',array('is_obs_product'=>0,'fixed'=>0))->result_array();
		if (!empty($all_fdd)) {
			foreach ($all_fdd as $key => $value) {
				$this->fdb->select('review&fixed,product_type');
				$all_fdd1 = $this->fdb->get_where('products',array('p_id'=>$value['fdd_pro_id']))->row_array();
				if ($all_fdd1['product_type'] == '0' ) {
					echo $value['fdd_pro_id'];die("dfdf");
				}
			}
		}
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */