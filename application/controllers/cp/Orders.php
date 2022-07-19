<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Orders extends CI_Controller
{

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

		$this->lang_u = get_lang( $_COOKIE['locale'] );

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

function index(){
	$this->lijst();
}

	function lijst($pending = '')
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

	    if( $this->company_role == 'master' || $this->company_role == 'sub' || $this->company_role == 'super' )
		{
			$cancelled = ($pending == 'cancelled')?true:false;

			$data['company_role'] = $this->company_role;
			$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender

			/*===========models================ */
			$this->load->model('Morders');
			$this->load->model('Morder_details');
			/*=================================*/

			$company = $this->Mcompany->get_company();
			if( !empty($company) )
			{
				$company = $company[0];
				$ac_type_id = $company->ac_type_id;

				if($ac_type_id == 1)
				 	$this->session->set_userdata('menu_type','free');
				elseif($ac_type_id == 2)
				  	$this->session->set_userdata('menu_type','basic');
				elseif($ac_type_id == 3)
				 	$this->session->set_userdata('menu_type','pro');
				elseif($ac_type_id == 4)
					$this->session->set_userdata('menu_type','fdd_light');
				elseif($ac_type_id == 5)
					$this->session->set_userdata('menu_type','fdd_pro');
				elseif($ac_type_id == 6)
					$this->session->set_userdata('menu_type','fdd_premium');
				elseif($ac_type_id == 7)
					$this->session->set_userdata('menu_type','fooddesk_light');
			}

			if($this->input->post('act')=='do_filter'){/*===============for the searching==========*/

				$exp_start_date = explode("/",$this->input->post('start_date'));
				$data['start_date'] = $start_date = $exp_start_date[2]."-".$exp_start_date[1]."-".$exp_start_date[0];

				$exp_end_date = explode("/",$this->input->post('end_date'));
				$data['end_date'] = $end_date = $exp_end_date[2]."-".$exp_end_date[1]."-".$exp_end_date[0];

				$final_pending_order = 0;
				if($this->company_role == 'super'){
					$final_order = 0 ;
					$final_desk_order = 0;
					$final_pending_order	= 0;
					$cancelled_orders		= 0;
					$final_cancelled_orders = 0;

					$sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

					foreach($sub_companies as $sub_company){
						$orders = $this->Morders->count_orders($sub_company->company_id,$start_date,$end_date);
						$final_orders = $final_order + $orders;
						$desk_orders = $this->Morders->get_desk_orders( array('desk_orders.company_id'=>$this->company_id ), array( 'created_date' => 'DESC' ) );
						$final_desk_order = $final_desk_order + count($desk_orders);

						// Order Count that are tried to pay online but didnt succeeded
						$pending_orders = $this->Morders->count_orders($sub_company->company_id,null,null,null,1);
						$final_pending_order = $final_pending_order + $pending_orders;

						$cancelled_orders = $this->Morders->count_orders($sub_company->company_id,null,null,null,0,true);
						$final_cancelled_orders = $final_cancelled_orders + $cancelled_orders;
					}

					$data['orders'] = $final_order;
					$data['desk_orders'] = $final_desk_order;
					$data['pending_orders'] = $final_pending_order;
				}else{
					$data['orders'] = $this->Morders->count_orders($this->company_id,$start_date,$end_date);
					$data['desk_orders'] = $this->Morders->get_desk_orders( array('desk_orders.company_id'=>$this->company_id ), array( 'created_date' => 'DESC' ) );
					$data['pending_orders'] = $this->Morders->count_orders(null,null,null,null,1);
					$data['cancelled_orders'] = $this->Morders->count_orders(null,null,null,null,0,true);
				}

			}else if($this->input->post('act')=='delete_order'){/*======for the deletion of the orders========*/

				if($this->input->post('delete_row')=='single'){
					$id = $this->input->post('id');
					$result=$this->Morders->delete_order($id,$cancelled);

					if($result){
						echo  json_encode(array('success'=>_('Order has been deleted successfully.')));
					}else{
						echo  jon_encode(array('error'=>_('Error in deletion of order! Please try again.')));
					}
				}else if($this->input->post('delete_row')=='all'){
					$order_ids = explode(',',$this->input->post('order_ids'));

					$counter=0;
					foreach($order_ids as $order_id){
						$result=$this->Morders->delete_order($order_id,$cancelled);
						if($result){
							$counter++;
						}else{
							break;
						}
					}//end of foreach

					if($counter==count($order_ids)){
						echo 'success';
					}else{
						echo 'error';
					}

				}
				exit;

			}else if($this->input->post('act') == 'show_order_details'){

				/*==========to show order details in thick box===========*/

				$orders_id=$this->input->post('orders_id');
				$cancelled_orders = false;

				if($this->input->post('pending')){
					$data['orderData'] = $this->Morders->get_pending_orders($orders_id);
				}
				elseif($cancelled){
					$data['orderData'] = $this->Morders->get_cancelled_orders($orders_id);

				}
				else{
					$select = array(
								'orders.created_date',
								'orders.phone_reciever',
								'orders.option',
								'orders.delivery_cost',
								'orders.disc_client_amount',
								'orders.disc_amount',
								'orders.disc_client',
								'orders.disc_other_code',
								'orders.other_code_type',
								'orders.disc_price',
								'orders.disc_percent',
								'orders.order_remarks'
					);

					$data['orderData'] = $this->Morders->get_orders( $select, $orders_id );
				}

				$data['order_details_data'] = $order_details_data = $this->Morder_details->get_order_details($orders_id,$cancelled);

				$total='';
				$TempExtracosts=array();

				for($i=0;$i<count($order_details_data);$i++){
					if($order_details_data[$i]->add_costs != ""){
						$rsExtracosts = explode("#",$order_details_data[$i]->add_costs);
						for($j=0;$j<count($rsExtracosts);$j++){
							$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
						}
					}
					$total += $order_details_data[$i]->total;
				}

				$data['TempExtracosts'] = $TempExtracosts;
				$data['total'] = $total;

				$returned_data=$this->load->view('cp/order_details',$data,true);
				echo $returned_data;
				exit;
			}else if($this->input->post('act') == 'update_order'){

				$order_id = $this->input->post('order_id');
				if($this->Morders->update_order($order_id, array('payment_status' => '1'),$cancelled)){
					echo 'success';
				}else{
					echo 'error';
				}
				exit;
			}else if($this->input->post('act')=='cancel_order')
			{
				$order_id=$this->input->post('order_id');
				$this->db->select('clients.email_c,orders_tmp.company_id');
				$this->db->join('orders_tmp', 'orders_tmp.clients_id = clients.id');
				$this->db->where('orders_tmp.id',$order_id);
				$data = $this->db->get('clients')->row_array();
				if(!empty($data)){
					$email = $data['email_c'];
					$this->db->select('company_name');
					$this->db->where('id',$data['company_id']);
					$company_data = $this->db->get('company')->row_array();
					$mail_data['company_name']=$company_data['company_name'];
					$mail_body = $this->load->view('mail_templates/'.$this->lang_u.'/cancel_order_notification',$mail_data,true);
					send_email($email, $this->company->email ,_("Cancelled Order"),$mail_body,NULL,NULL,NULL,'company','client','order_cancelled');
					$this->db->where('id',$order_id);
					$this->db->update('orders_tmp',array('cancel_mail_status'=>1));
					echo "success";
					die;
				}
				else
				{
					echo "error";
					die;
				}
				return;
			}else{
				if($this->company_role == 'super'){
					$final_order			= 0;
					$final_desk_order		= 0;
					$final_pending_order	= 0;
					$cancelled_orders		= 0;
					$final_cancelled_orders = 0;

					$sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

					foreach($sub_companies as $sub_company){

						// Orders that are either payed online successfully or subjected to COD
						$orders = $this->Morders->count_orders($sub_company->company_id);
						$final_order = $final_order + $orders;

						// Count of orders coming from DESK
						$desk_orders = $this->Morders->get_desk_orders( array('desk_orders.company_id'=>$this->company_id ), array( 'created_date' => 'DESC' ) );
						$final_desk_order = $final_desk_order + count($desk_orders);

						// Order Count that are tried to pay online but didnt succeeded
						$pending_orders = $this->Morders->count_orders($sub_company->company_id,null,null,null,1);
						$final_pending_order = $final_pending_order + $pending_orders;

						// Orders Count that are cancelled by the user before online payment
						$cancelled_orders = $this->Morders->count_orders($sub_company->company_id,null,null,null,0,true);
						$final_cancelled_orders = $final_cancelled_orders + $cancelled_orders;
					}

					$data['orders']				= $final_order;
					$data['desk_orders']		= $final_desk_order;
					$data['pending_orders'] 	= $final_pending_order;
					$data['cancelled_orders']	= $final_cancelled_orders;
				}else{
					// Orders that are either payed online successfully or subjected to COD
					$data['orders'] = $this->Morders->count_orders();

					// Count of orders coming from DESK
					$data['desk_orders'] = $this->Morders->get_desk_orders( array('desk_orders.company_id'=>$this->company_id ), array( 'created_date' => 'DESC' ) );

					// Order Count that are tried to pay online but didnt succeeded
					$data['pending_orders'] = $this->Morders->count_orders(null,null,null,null,1);

					// Orders Count that are cancelled by the user before online payment
					$data['cancelled_orders'] = $this->Morders->count_orders(null,null,null,null,0,true);
				}
			}

			$this->load->model('mnotify');
			$data['notifications'] = $this->mnotify->get_notifications(NULL, date('Y-m-d'));
			$data['closed_noti'] = $this->mnotify->get_closed_noti($this->company->id);
			$data['settings'] = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$this->company_id));
			$data['pending'] = $pending;

			if($pending == 'pending'){
				$data['content'] = 'cp/pending_orders';
			}
			elseif($cancelled){
				$data['content'] = 'cp/cancelled_orders';
			}
			else{
				$data['content'] = 'cp/orders';
			}

			$this->load->view('cp/cp_view',$data);

		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
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

	function order_details_edit()
	{
		// 		$this->load->library('form_validation');
		// 		$this->form_validation->set_rules('select_subcat', 'Select Subcategory', 'required');
		// 		$this->form_validation->set_rules('newproid', 'Product', 'required');
		// 		if ($this->form_validation->run() == FALSE)
		// {
		// $this->load->view('valform');
		// }
		// else
		// {
		//echo '1';
		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender

		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		$this->load->model('Mdelivery_areas');
		$this->load->model('Mdelivery_settings');
		/*=================================*/

		if($this->uri->segment(4)=='update'){

			$order_id = $this->uri->segment(5);

			$select = array( 
					'orders.id',
					'orders.order_status',
					'orders.created_date',
					'orders.disc_amount',
					'orders.delivery_cost',
					'orders.order_total',
					'orders.disc_client_amount',
					'orders.order_pickuptime',
					'orders.order_pickupdate',
					'orders.delivery_streer_address',
					'orders.delivery_zip',
					'orders.delivery_date',
					'orders.delivery_hour',
					'orders.option',
					'orders.delivery_minute',
					'orders.phone_reciever',
					'orders.delivery_day',
					'orders.delivery_remarks',
					'orders.delivery_city',
					'orders.disc_other_code',
					'orders.order_pickupday',
					'orders.disc_price',
					'orders.disc_percent',
					'orders.other_code_type',
					'orders.disc_client',
					'orders.order_remarks',
					'orders.delivery_country',
					'orders.name',
					'company.company_name',
					'clients.id AS client_id',
					'clients.firstname_c',
					'clients.lastname_c',
					'clients.company_c',
					'clients.address_c',
					'clients.housenumber_c',
					'clients.postcode_c',
					'clients.phone_c',
					'clients.mobile_c',
					'clients.city_c',
					'clients.email_c',
					'clients.notifications',
					'country.country_name as country_name',
					'postcodes.area_name as delivery_city_name',
					'states.state_name as delivery_area_name',
					'payment_transaction.billing_option',
					'delivery_country.country_name as delivery_country_name'
			);
			$data['orderData'] = $order_data = $this->Morders->get_orders( $select, $order_id );
			$data['order_details_data'] = $order_details_data = $this->Morder_details->get_order_details($order_id);

			/*====these fields required only when there is order is being delivery =====*/

			if($order_data[0]->option == '2'){
				$data['delivery_areas'] = $this->Mdelivery_areas->get_delivery_areas();
				$data['delivery_cities'] = array();

			    if(isset($order_data[0]->delivery_area_id))
				$data['delivery_cities'] = $this->Mdelivery_settings->get_delivery_settings(array('delivery_areas_id'=>$order_data[0]->delivery_area_id));
			}

			//countries details for international delivery
			$countries = array();
			if( !empty($order_data) && $order_data[0]->option == "2" && $order_data[0]->phone_reciever != '' ){
				$this->db->select('id,country_name');
				$country_arr = $this->db->get('country')->result();
				foreach($country_arr as $country){
					$countries[$country->id] = $country;
				}
			}
			$data['countries'] = $countries;

			$data['ShowProd'] = 1;

			if( $this->company_role == 'sub' )
			{
				$data['CategoryArray'] = $this->Mcategories->get_category( array( 'company_id' => $this->company_parent_id ) );
			}
			else
			{
				$data['CategoryArray'] = $this->Mcategories->get_categories();
			}

			$total='';
			$TempExtracosts=array();
				for($i = 0; $i < count($order_details_data); $i++){
					if($order_details_data[$i]->add_costs != ""){
						$rsExtracosts = explode("#",$order_details_data[$i]->add_costs);
						for($j = 0; $j < count($rsExtracosts); $j++){
							$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
						}
					}else{
						$TempExtracosts[$i]=''; //this condition will be checked in order_details_edit.php
					}
					$total += $order_details_data[$i]->total;
				}

				$data['TempExtracosts'] = $TempExtracosts;
				$data['total'] = $total;

			$data['content'] = 'cp/order_details_edit';
			$this->load->view('cp/cp_view',$data);
		}
		if($this->input->post('act') == 'addProduct'){
		//echo "2";
			$this->load->model('Mproducts');

			$orders_id = $this->input->post('orderid');
			$products_id = $this->input->post('newproid');

			$product = $this->Mproducts->get_product_information($products_id);
		//echo "3";
			$field['orders_id'] = $orders_id;
			$field['products_id'] = $product[0]->id;

			if( $product[0]->sell_product_option == 'per_unit' )
			{
			    $field['quantity'] = 1;
				$field['content_type'] = '0';
				$field['default_price'] = $product[0]->price_per_unit;
				$field['discount'] = $product[0]->discount;
				if($product[0]->discount){
					if($product[0]->discount == 'multi'){

						$field['sub_total'] = $product[0]->price_per_unit;
						$field['total'] = $product[0]->price_per_unit;
					}else{
						$field['sub_total']=($product[0]->price_per_unit - $product[0]->discount);
						$field['total'] = $field['sub_total'];
					}
				}else{
					$field['sub_total'] = $product[0]->price_per_unit;
					$field['total'] = $product[0]->price_per_unit;
				}
			}
			elseif( $product[0]->sell_product_option == 'weight_wise' )
			{
				if( $product[0]->weight_unit == 'gm')
				{
					$field['quantity'] = 1000;
					$field['content_type'] = '1';
					$field['weight_unit'] = 'gm';
					$field['default_price'] = $this->number2db($product[0]->price_weight*1000);
					$field['discount'] = $product[0]->discount_wt;
					if($product[0]->discount_wt){
						if($product[0]->discount == 'multi'){
							/*$field['sub_total'] = $default_price_after_discount;
							$field['total'] = $default_price_after_discount;*/
							$field['sub_total'] = $this->number2db($product[0]->price_weight*1000);
							$field['total'] = $this->number2db($product[0]->price_weight*1000);
						}else{
							$field['sub_total'] = ($this->number2db($product[0]->price_weight*1000) - $product[0]->discount_wt);
							$field['total'] = ($this->number2db($product[0]->price_weight*1000) - $product[0]->discount_wt);
						}
					}else{
						$field['sub_total'] = $this->number2db($product[0]->price_weight*1000);
						$field['total'] = $this->number2db($product[0]->price_weight*1000);
					}
			    }
			}elseif( $product[0]->sell_product_option == 'per_person'){
				$field['quantity'] = 1;
				$field['content_type'] = '2';
				$field['default_price'] = $product[0]->price_per_person;
				$field['discount'] = $product[0]->discount;
				if($product[0]->discount_wt){
					if($product[0]->discount == 'multi'){
						$field['sub_total'] = $product[0]->price_per_person;
						$field['total'] = $product[0]->price_per_person;
					}else{
						$field['sub_total'] = $product[0]->price_per_person - $product[0]->discount_wt;
						$field['total'] = $product[0]->price_per_person - $product[0]->discount_wt;
					}
				}else{
					$field['sub_total'] = $product[0]->price_per_person;
					$field['total'] = $product[0]->price_per_person;
				}
			}
			elseif( $product[0]->sell_product_option == 'client_may_choose')
			{
				$field['quantity'] = 1;
				$field['content_type'] = '0';
				$field['default_price'] = $product[0]->price_per_unit;
				$field['discount'] = $product[0]->discount;
				if($product[0]->discount){
					if($product[0]->discount == 'multi'){
						$field['sub_total'] = $product[0]->price_per_unit;
						$field['total'] = $product[0]->price_per_unit;
					}else{
						$field['sub_total']=($product[0]->price_per_unit - $product[0]->discount);
						$field['total'] = $field['sub_total'];
					}
				}else{
					$field['sub_total'] = $product[0]->price_per_unit;
					$field['total'] = $product[0]->price_per_unit;
				}
			}
			//echo '4';
			$isproductadded = $this->Morder_details->insert_order_details($field);
			//$isproductadded=1;
			if($isproductadded){
				//echo '5';
				$select = array( 'orders.order_total','orders.disc_client','orders.disc_percent','orders.disc_price','orders.disc_promo_perc','orders.disc_promo_price' );
				$result = $this->Morders->get_orders( $select, $orders_id );

				//$order_total = (float)($result[0]->order_total+$product[0]->price);
				$order_total = (float)($result[0]->order_total + $field['total']);
				//echo "6";
				$field1['order_total'] = $this->number2db($order_total);


				$disc_per_client_amount = $disc_amount = $disc_other_code = 0;
				if($result[0]->disc_client > 0){
					$disc_per_client_amount = ( $result[0]->disc_client * $order_total ) / 100;
				}
				
				if ($order_total > $result[0]->disc_after_amount) {
					if($result[0]->disc_percent > 0){
						$disc_amount = ( $result[0]->disc_percent * ($order_total - $disc_per_client_amount) ) / 100;
					}
					elseif ($result[0]->disc_price > 0) {
						$disc_amount = $result[0]->disc_price;
					}
				}

				if($result[0]->disc_promo_perc > 0){
					$disc_other_code = ( $result[0]->disc_promo_perc * $order_total  ) / 100;
				}
				elseif ($result[0]->disc_promo_price > 0) {
					$disc_other_code = $result[0]->disc_promo_price;
				}

				$field1["disc_client_amount"] = $this->number2db($disc_per_client_amount);
				$field1["disc_amount"] = $this->number2db($disc_amount);
				$field1["disc_other_code"] = $this->number2db($disc_other_code);

				//print_r($field1);die;

				//echo '7';
				$updated = $this->Morders->update_order($orders_id,$field1);
					//die('ok');
				redirect('cp/orders/order_details_edit/update/'.$orders_id);

			}
		}
		if($this->input->post('act') == 'delete_ordered_product'){

			$order_details_data_id = $this->input->post('order_detail_data_id');
			$orders_id = $this->input->post('order_id');
			$price = $this->input->post('price');

			$result = $this->Morders->delete_ordered_product($order_details_data_id,$orders_id,$price);
			
			if($result){
				echo json_encode(array('success'=>'Deleted successfully.'));
			}
			else {
				echo json_encode(array('error'=>'Couldn\'t delete the product from the order'));
			}
		}
		if($this->input->post('act') == 'edit_quantity'){

			$this->load->model('Mproduct_discount');
			$final_price_after_discount = '';
			$response = array('discounted_price_per_piece'=>0,'qty'=>0);
			//$response = array();
			$orderid = $this->input->post('orderid');
			$default_price= $this->input->post('default_price');
			$discount= $this->input->post('discount');
			$content_type = $this->input->post('content_type');
			$qty = $this->input->post('qty');
			$product_id = $this->input->post('prod_id');
			$price =  $this->input->post('price');
			$product_group = $this->input->post('product_group');
			$order_details_id = $this->input->post('order_details_id');

			if($discount == 'multi'){
				$response = $this->Mproduct_discount->get_product_multidiscount($product_id,$content_type,$qty);
				if($response['discounted_price_per_piece'] == 0){
					$final_price_after_discount = $default_price;
				}else{
					$final_price_after_discount = round($response['discounted_price_per_piece']);
				}
			}else{
				$final_price_after_discount = ($default_price - $discount);
			}


			$product_group_temp = array();
			$TempExtracosts = 0;

			if($product_group != ""){
				//echo $product_group;
				$rsExtracosts = explode("#",$product_group);

				for($j=0;$j<count($rsExtracosts);$j++){
					if($rsExtracosts[$j] != "0"){
						$temp = explode("_",$rsExtracosts[$j]);
						$TempExtracosts = $TempExtracosts + $temp[2];
						array_push($product_group_temp, $rsExtracosts[$j]);
					}
				}
			}
			$fields["add_costs"] = implode("#", $product_group_temp);
			$fields["quantity"] = $qty;

			if($content_type == '1'){
				if($discount == 'multi'){
					$fields["total"] = (float)(($qty - $response['qty'])/1000)*$final_price_after_discount + (float)($response['qty']/1000)*$default_price + $TempExtracosts;
					$fields["sub_total"] = $default_price;
				}else{
					$fields["total"] = (float)($qty/1000)*$final_price_after_discount + $TempExtracosts;
					$fields["sub_total"] = $final_price_after_discount;
				}
			}else{
				if($discount == 'multi'){
					$new_total_price = (($qty - $response['qty']) * ($final_price_after_discount + $TempExtracosts)) + ($response['qty'] * ($default_price + $TempExtracosts));
					$fields["sub_total"] = $default_price + $TempExtracosts;
				}else{
					$new_total_price = ($qty * ($final_price_after_discount + $TempExtracosts));
					$fields["sub_total"] = $final_price_after_discount + $TempExtracosts;
				}
				$fields["total"] = $new_total_price;
			}

			$fields["total"] = str_replace(",",".",$fields["total"]);
			$fields["sub_total"] = str_replace(",",".",$fields["sub_total"]);

			$order_details_updated = $this->Morder_details->update_order_details($order_details_id,$fields);

			if($order_details_updated){
				$order_details_data=$this->Morder_details->get_order_details($orderid);
				$order_data=$this->Morders->get_orders(array(),$orderid);

				$total = "";
				for($k=0;$k<count($order_details_data);$k++){
					$total += $order_details_data[$k]->total;
				}

				$disc_per_client_amount = $disc_amount = $disc_other_code = 0;
				if($order_data[0]->disc_client > 0){
					$disc_per_client_amount = ( $order_data[0]->disc_client * $total ) / 100;
				}

				if ($total > $order_data[0]->disc_after_amount) {
					if($order_data[0]->disc_percent > 0){
						$disc_amount = ( $order_data[0]->disc_percent * ($total - $disc_per_client_amount) ) / 100;
					}
					elseif ($order_data[0]->disc_price > 0) {
						$disc_amount = $order_data[0]->disc_price;
					}
				}

				if($order_data[0]->disc_promo_perc > 0){
					$disc_other_code = ( $order_data[0]->disc_promo_perc * $total  ) / 100;
				}
				elseif ($order_data[0]->disc_promo_price > 0) {
					$disc_other_code = $order_data[0]->disc_promo_price;
				}

				$fields2["order_total"] = $this->number2db($total);
				$fields2["disc_client_amount"] = $this->number2db($disc_per_client_amount);
				$fields2["disc_amount"] = $this->number2db($disc_amount);
				$fields2["disc_other_code"] = $this->number2db($disc_other_code);

				$result=$this->Morders->update_order($orderid,$fields2);

				if($result){

					$returned_data = json_encode(array('success'=>_('Details has been updated successfully.')));

				}else{

					$returned_data = json_encode(array('error'=>_('Some error occured in updating order.')));
				}

			}else{
				$returned_data = json_encode(array('error'=>_('Some error occured while updating order details.')));
			}
			echo $returned_data;
		}
		if($this->input->post('act') == 'update_order_status'){

			$order_id = $this->input->post("oid");
			$status = $this->input->post('order_status');

			$select 	= array( 'clients.email_c' );	 
			$order_data = $this->Morders->get_orders( $select, $order_id );

			$email = $order_data[0]->email_c;

			if($status == "y"){
				redirect("cp/orders/send_mail?email=".$email."&type=completed&id=".$order_id);
			}else if($status == "n"){
				redirect("cp/orders/send_mail?email=".$email."&type=hold&id=".$order_id);
			}
		}

	}

	function calculate_price_after_multidiscount(){
		$this->load->model('Mproduct_discount');
		$product_id = $this->input->post('product_id');
		$content_type= $this->input->post('type');
		$qty = $this->input->post('qty');
		$response = $this->Mproduct_discount->get_product_multidiscount($product_id,$content_type,$qty);
		return $response;
	}

	function ordered_products()
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->input->post('act') == 'format_date' )
		{
		   $date = $this->input->post('date');
		   $format = $this->input->post('format');

		   echo date( $format, strtotime($date.' 00:00:00') );
		   die();
		}

		$this->load->model('Mproducts');

		if( $this->input->post('tomorrow') )
		{
		   $date_tomorrow = $this->input->post('date_tomorrow');
		   $data['dontShowRemark'] = $dontShowRemark = $this->input->post("single_hide_remark");

		   $data['products'] = $this->Mproducts->get_ordered_products($date_tomorrow,'','',$dontShowRemark);

		   $data['action_set'] = 'ordered_on';
		   $data['date_set_1'] = $date_tomorrow;
		   $data['date_set_2'] = '';
		}
		else
		if( $this->input->post('day_after_tomorrow') )
		{
		   $date_after_tomorrow = $this->input->post('date_after_tomorrow');
		   $data['dontShowRemark'] = $dontShowRemark = $this->input->post("single_hide_remark");

		   $data['products'] = $this->Mproducts->get_ordered_products($date_after_tomorrow,'','',$dontShowRemark);

		   $data['action_set'] = 'ordered_on';
		   $data['date_set_1'] = $date_after_tomorrow;
		   $data['date_set_2'] = '';
		}
		else
		if( $this->input->post('act') == 'get_products' )
		{
		    if( $this->input->post('start_date') && $this->input->post('end_date') )
			{
			    $start_date = date('Y-m-d',strtotime($this->input->post('start_date').' 00:00:00') );
				$end_date = date('Y-m-d',strtotime($this->input->post('end_date')) );
				$data['dontShowRemark'] = $dontShowRemark = $this->input->post("e_hide_remark");

				$data['products'] = $this->Mproducts->get_ordered_products('',$start_date,$end_date,$dontShowRemark);

				$data['action_set'] = 'ordered_from_to';
			    $data['date_set_1'] = $start_date;
			    $data['date_set_2'] = $end_date;
			}
		}

		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender
		$data['content'] = 'cp/ordered_products';
		$this->load->view('cp/cp_view',$data);
	}

	/**
	 * @name ordered_products_list
	 * @property This Function is for generating labels and excels of per product separately
	 * @access public
	 * @author Monu Singh Yadav <monuyadav@cedcoss.com>
	 *
	 */
	function ordered_products_list($type=NULL,$start_date=NULL,$end_date=NULL,$prod_id=NULL)
	{

        if($this->input->post('type')=='label')
        {
			$this->load->model('Mproducts');
			$prod_id=$this->input->post('product_id');
			if( $this->input->post('start_date') && $this->input->post('end_date') )
			{
			    $start_date = date('Y-m-d',strtotime($this->input->post('start_date').' 00:00:00') );
				$end_date = date('Y-m-d',strtotime($this->input->post('end_date')) );
				$products = $this->Mproducts->get_ordered_products_list('',$start_date,$end_date,$prod_id);
			}
			else
			{
				$start_date = date('Y-m-d',strtotime($this->input->post('start_date').' 00:00:00') );
				$products = $this->Mproducts->get_ordered_products_list($start_date,'','',$prod_id);
			}
        	echo json_encode($products);
        	die();
        }
		if($type=='excel')
		{
			if($end_date=='NULL')
			{
				$end_date=NULL;
			}
			$this->load->model('Mproducts');
			if(!empty($start_date) && !empty($end_date))
			{
			    $start_date = date('Y-m-d',strtotime($start_date.' 00:00:00') );
				$end_date = date('Y-m-d',strtotime($end_date) );
				$products = $this->Mproducts->get_ordered_products_list_excel('',$start_date,$end_date,$prod_id);
			}
			else
			{
				$start_date = date('Y-m-d',strtotime($start_date.' 00:00:00') );
				$products = $this->Mproducts->get_ordered_products_list_excel($start_date,'','',$prod_id);
			}
			if(!empty($products))
			{
				$this->load->library('excel');
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->setTitle(_('Product Report'));
				$counter=1;
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Product:') )->getStyle('A'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, stripslashes($products[1]->proname))->getStyle('B'.$counter)->getFont()->setBold(true);
				$counter++;
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Name') )->getStyle('A'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Order Number') )->getStyle('B'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Amount') )->getStyle('C'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('Remarks') )->getStyle('D'.$counter)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

				if(!empty($products[0]))
				{
					$obs_counter='E';
					foreach ($products[0] as $p => $v)
					{
						# code...
						$this->excel->getActiveSheet()->setCellValue($obs_counter.$counter, stripslashes(($v)) )->getStyle($obs_counter.$counter)->getFont()->setBold(true);
						$this->excel->getActiveSheet()->getColumnDimension($obs_counter)->setWidth(20);
						$obs_counter++;
					}
				}

				foreach ($products as $k => $value)
				{
					# code...
					if($k!=0)
					{

						$counter++;
						$this->excel->getActiveSheet()->setCellValue('A'.$counter,stripslashes($value->client_name));
						$this->excel->getActiveSheet()->setCellValue('B'.$counter,stripslashes($value->orders_id));
						$this->excel->getActiveSheet()->setCellValue('C'.$counter,stripslashes($value->quantity.$value->qty_unit));
						$this->excel->getActiveSheet()->setCellValue('D'.$counter,stripslashes($value->pro_remark));
						$obs_counter='E';
						if(!empty($products[0]))
						{
							foreach ($products[0] as $p1 => $v2)
							{
								# code...
								if(array_key_exists($v2, $value->related_option))
								{
									$obs_group_value='';
									foreach ($value->related_option[$v2] as $k2 => $value2)
									{
										# code...
	                                    $obs_group_value.=$value2.', ';
				                    }
				                    $this->excel->getActiveSheet()->setCellValue($obs_counter.$counter,rtrim($obs_group_value,', '));
									$obs_counter++;
								}
							}
						}
					}
				}
				$datestamp = date("d-m-Y");
				$filename = "export-labels-".$this->company_id."-".$datestamp.".xls";
				$path = base_url().'assets/cp/order_pro_report/';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
				$objWriter->save('php://output');
				readfile($path.$filename); // push it out
				exit();

            }

		}
	}


	function print_ordered_products( $dont_show_remarks = 0, $date_1 = NULL, $date_2 = NULL )
	{
	    $this->load->model('Mproducts');

		$start_date = $date_1;
		$end_date = $date_2;

		$data['date_set_1'] = date('d-m-Y',strtotime($start_date.' 00:00:00'));
		$data['date_set_2'] = date('d-m-Y',strtotime($end_date.' 00:00:00'));
		//echo $start_date."-----".$end_date."-------".$dont_show_remarks; die;
		if($start_date && $end_date)
		{
			$data['products'] = $this->Mproducts->get_ordered_products('',$start_date,$end_date,$dont_show_remarks);
		}
		elseif($start_date && !$end_date)
		{
		    $data['products'] = $this->Mproducts->get_ordered_products($start_date,'','',$dont_show_remarks);
		}
		$data['dont_show_remarks'] = $dont_show_remarks;
		$this->load->view('cp/print_ordered_products',$data);
	}

	function ordered_products_per_supp(){
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->input->post('act') == 'format_date' ){
		   $date = $this->input->post('date');
		   $format = $this->input->post('format');

		   echo date( $format, strtotime($date.' 00:00:00') );
		   die();
		}

		$this->load->model('Mproducts');

		if( $this->input->post('act') == 'get_products' ){
		    if( $this->input->post('start_date') && $this->input->post('end_date') ){
			    $start_date = date('Y-m-d',strtotime($this->input->post('start_date').' 00:00:00') );
				$end_date = date('Y-m-d',strtotime($this->input->post('end_date')) );
				$data['dontShowRemark'] = $dontShowRemark = $this->input->post("e_hide_remark");
				$data['supp_products'] = $this->Mproducts->get_supp_ordered_products($start_date,$end_date,$dontShowRemark);
				$data['action_set'] = 'ordered_from_to';
			    $data['date_set_1'] = $start_date;
			    $data['date_set_2'] = $end_date;
			}
		}

		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender
		$data['content'] = 'cp/ordered_supp_products';
		$this->load->view('cp/cp_view',$data);
	}

	public function print_auto_labeler($type = 'per_ordered_product',$timestamp = null){

		$response = array();
		$general_settings = $this->Mgeneral_settings->get_general_settings();
		$this->load->model('Morders');
		$orders = $this->Morders->get_pending_orders_for_labeler($general_settings[0]->printer_orders);
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

						//Adding company name
						$doc_text['company_c'] = $order->company_c;

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

						$remark = "";


						// Adding pickup date
						$date_content = '--';
						if($order->option == 1){

							$remark = str_replace('&','',$order->order_remarks);

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

							$remark = str_replace('&','',$order->delivery_remarks);

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

						if(strlen($remark) >= 100){
							$remark = substr($remark,0,100)."...";
						}
						$remark = addslashes($remark);

						$doc_text['remark'] = '';
						if($remark != ''){
							$doc_text['remark'] = _("Opmerking").": ".$remark;
						}

						$labeler_array[$count]['items'] = $doc_text;
						$labeler_array[$count]['type'] = 'per_order';
						$count++;

					}

					if($type == 'per_ordered_product' || $type == 'all'){

						$this->load->model('Morder_details');
						$this->load->model('Morders');
						$select = array( 'clients.firstname_c', 'clients.lastname_c', 'clients.company_c' );

						$order_data_pre = $this->Morders->get_orders( $select, $order_id );
						$order_data = $this->Morder_details->get_order_details($order_id);

						foreach($order_data as $values){

							$quantity_unit = '';
							if($values->content_type != 1 && $values->content_type != 2){
								for($j = 0; $j < (int)$values->quantity; $j++){
									$doc_text = array();

									$doc_text['c_name'] = str_replace("&","en",$order_data_pre['0']->firstname_c." ".$order_data_pre['0']->lastname_c);
									$doc_text['company'] = $order_data_pre['0']->company_c;
									$total_price = round($values->total,2)/$values->quantity;
									$total_price = (string)$total_price;
									$total_price = str_replace('.',',',$total_price);

									// Adding product name and total amount
									$doc_text['name'] = str_replace("&","en",$values->proname);
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
									$remark = str_replace("&","en",$values->pro_remark);
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
								$doc_text['name'] = $values->quantity." ".$quantity_unit." ".str_replace("&","en",$values->proname);
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
								$remark = str_replace("&","en",$values->pro_remark);
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

				$this->db->where_in('id',$order_id_array);
				$this->db->update('orders', array('labeler_printed' => 1));

				$response = array('error'=>0,'message'=>'array','data'=>$labeler_array);
			}else{
				$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
			}
		}else{
			$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
		}
		echo json_encode($response);
	}

	function getPostcodes(){
		$postcodes = array();
		$new_html = '';
		if($this->input->post("stateId")){
			$state_id = $this->input->post("stateId");
			$country_id = $this->input->post("countryId");
			$this->load->model('Mdelivery_areas');
			$postcodes = $this->Mdelivery_areas->get_cities(array("state_id" => $state_id));

			$existing_postcode_ids = $this->db->get_where("company_delivery_areas",array("company_id" => $this->company_id, "country_id"=> $country_id, "state_id" => $state_id ))->result_array();
			$formated_postcode_array = array();
			if(!empty($existing_postcode_ids)){
				foreach($existing_postcode_ids as $values){
					$formated_postcode_array[] = $values['postcode_id'];
				}
			}

			if(!empty($postcodes)){
				foreach($postcodes as $postcode){
					$new_html .= '<option value='.$postcode['id'].' '.((in_array($postcode['id'],$formated_postcode_array))?'selected=\"selected\"':'').' >'.$postcode['post_code'].'&nbsp;&nbsp;&nbsp;&nbsp;'.stripslashes($postcode['area_name']).'</option>';
				}
			}

		}

		echo $new_html;
	}

	function set_delivery_areas(){
		$country_id = $this->input->post("countryId");
		$state_id = $this->input->post("stateId");
		$postcode_ids = array();
		if($this->input->post("postcodeIds"))
			$postcode_ids = $this->input->post("postcodeIds");

		$this->db->select("postcode_id");
		$existing_postcode_ids = $this->db->get_where("company_delivery_areas",array("company_id" => $this->company_id, "country_id"=> $country_id, "state_id" => $state_id ))->result_array();
		$formated_postcode_array = array();
		if(!empty($existing_postcode_ids)){
			foreach($existing_postcode_ids as $values){
				$formated_postcode_array[] = $values['postcode_id'];
			}
		}

		// Deleting Ids
		$postcode_ids_to_delete = array_diff($formated_postcode_array , $postcode_ids);
		if(!empty($postcode_ids_to_delete)){
			foreach($postcode_ids_to_delete as $deleting_id){
				$this->db->delete("company_delivery_areas", array("company_id" => $this->company_id, "country_id"=> $country_id, "state_id" => $state_id, "postcode_id" => $deleting_id) );
			}
		}

		// Inserting Ids
		$postcode_ids_to_insert = array_diff($postcode_ids, $formated_postcode_array);
		if(!empty($postcode_ids_to_insert)){
			foreach($postcode_ids_to_insert as $inserting_id){
				$this->db->insert("company_delivery_areas", array("company_id" => $this->company_id, "country_id"=> $country_id, "state_id" => $state_id, "postcode_id" => $inserting_id) );
			}
		}

		echo _("Areas updated successfully !!!");
	}

	function ajax_orders(){
		$this->load->model('Morders');

		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$exp_start_date = $this->input->post('start_date');
		$exp_end_date = $this->input->post('end_date');
		$orders = array();
		$select = array( 
					'orders.id',
					'orders.payment_status',
					'orders.payment_via_paypal',
					'orders.transaction_id',
					'orders.completed',
					'orders.order_status',
					'orders.created_date',
					'orders.disc_amount',
					'orders.disc_other_code',
					'orders.delivery_cost',
					'orders.order_total',
					'orders.disc_client_amount',
					'orders.order_pickuptime',
					'orders.order_pickupdate',
					'orders.delivery_streer_address',
					'orders.delivery_zip',
					'orders.delivery_date',
					'orders.delivery_hour',
					'orders.delivery_minute',
					'orders.phone_reciever',
					'orders.delivery_day',
					'orders.delivery_remarks',
					'orders.delivery_city',
					'orders.ok_msg',
					'orders.company_id',
					'orders.hidden',
					'company.company_name',
					'clients.id AS client_id',
					'clients.firstname_c',
					'clients.lastname_c',
					'clients.company_c',
					'clients.address_c',
					'clients.housenumber_c',
					'clients.postcode_c',
					'clients.phone_c',
					'clients.mobile_c',
					'clients.city_c',
					'clients.email_c',
					'clients.notifications',
					'country.country_name as country_name',
					'postcodes.area_name as delivery_city_name',
					'states.state_name as delivery_area_name',
					'payment_transaction.billing_option',
					'delivery_country.country_name as delivery_country_name' 
		);


		if($this->company_role == 'super'){
			$final_order = array();
			$sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

			foreach($sub_companies as $sub_company){

				$orders = $this->Morders->get_orders( $select, null, $exp_start_date, $exp_end_date, $limit, $start, array(), $sub_company->company_id );

				$final_order = array_merge($final_order,$orders);
			}
			usort($final_order, create_function('$a, $b',
			'if ($a->created_date == $b->created_date) return 0; return ($a->created_date > $b->created_date) ? -1 : 1;'));
			$orders = $final_order;
		}else{
			$orders = $this->Morders->get_orders( $select, null, $exp_start_date, $exp_end_date, $limit, $start);
		}

		//$orders = $this->Morders->get_orders(null,null,null,$limit,$start);

		$response = array();
		for($i =0; $i<count($orders);$i++){

			if($orders[$i]->completed == "1" && $orders[$i]->order_status == "y"){
				$tickImage = "tick1.gif";
				$class ="completed";//this will apply a class on a row if it's order has been completed
			}else{
				$tickImage = "tick.gif";
				$class = '';
			}
			/*====store id===*/
			$id = $orders[$i]->id;
			/*===============*/

			$payment_via_paypal = $orders[$i]->payment_via_paypal;
			$payment_status = $orders[$i]->payment_status;
			$transaction_id = $orders[$i]->transaction_id;

			$pay_html = '';
			if( $payment_via_paypal == 1 && $payment_status == 1 && $transaction_id != NULL )
			{
			   $pay_html = '&nbsp;<img src="'.base_url().'assets/cp/images/via-paypal.PNG" title="'._('Paid via Paypal').'" alt="'._('Paid via Paypal').'">';

			}elseif( $payment_via_paypal == 2 && $payment_status == 1 )
			{
				if($orders[$i]->billing_option != '')
					$alt_txt = _('via').' '.ucfirst($orders[$i]->billing_option);
				else
					$alt_txt = _('via').' Cardgate';
			   $pay_html = '&nbsp;<img src="'.base_url().'assets/cp/images/via-cardgate.png" title="'.$alt_txt.'" alt="'.$alt_txt.'" height="16">';
			}

			/*======date created=======*/
			$date_created = date("d/m/y",strtotime($orders[$i]->created_date));
			/*=========================*/

			/*=======store name=======*/
			$client_name = $orders[$i]->firstname_c." ".$orders[$i]->lastname_c;
			/*========================*/

			//echo $client_order_count[$orders[$i]->client_id];
			$red_dot = (!empty($client_order_count) && isset($client_order_count[$orders[$i]->client_id]) && $client_order_count[$orders[$i]->client_id]==1)?1:0;
			$notifications = $orders[$i]->notifications;

			/*=========store total========*/
			$order_total = (round((float)$orders[$i]->order_total,2))+(round((float)$orders[$i]->delivery_cost,2))-(float)( round($orders[$i]->disc_amount,2) + round($orders[$i]->disc_client_amount,2) + round($orders[$i]->disc_other_code,2));

			$order_total =  defined_money_format($order_total);

			/*============================*/

			/*==========pickup content==========*/
			if($orders[$i]->order_pickuptime != ""){

				$pickup_content = date("d / m",strtotime($orders[$i]->order_pickupdate))." <br>om ".$orders[$i]->order_pickuptime;

				$pickup_day = date("D",strtotime($orders[$i]->order_pickupdate));

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

				$pickup_content = $pickup_day.' '.$pickup_content;

			}else{
				$pickup_content = "--";
			}
			/*===================================*/

			/*=======delivery_content============*/
			if($orders[$i]->delivery_streer_address!= ""){

				$delivery_address_content = ''.$orders[$i]->delivery_streer_address.'&nbsp;';

				if($orders[$i]->delivery_zip)
				{
				    $delivery_address_content .= _('-').'  '.$orders[$i]->delivery_zip.'<br />';

					$delivery_date_content =date("d/m",strtotime($orders[$i]->delivery_date))." ".$orders[$i]->delivery_hour.":".$orders[$i]->delivery_minute;

					$delivery_day = date("D",strtotime($orders[$i]->delivery_date));

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

					//$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;
				}
				if (isset($orders[$i]->phone_reciever) && $orders[$i]->phone_reciever != '' && isset($orders[$i]->delivery_city) && $orders[$i]->delivery_city){
					//$delivery_address_content .='&nbsp;'.$orders[$i]->delivery_city.', '.$orders[$i]->delivery_area.'';
					$delivery_address_content .='&nbsp;'.$orders[$i]->delivery_city.', ';
				}elseif(isset($orders[$i]->delivery_area_name) || isset($orders[$i]->delivery_city_name)){
					$delivery_address_content .='&nbsp;'.$orders[$i]->delivery_city_name.', '.$orders[$i]->delivery_area_name.', ';
				}

				if(isset($orders[$i]->phone_reciever) && isset($orders[$i]->delivery_country_name)){
					$delivery_address_content .= $orders[$i]->delivery_country_name;
				}
				elseif(isset($orders[$i]->country_name)){
					$delivery_address_content .= $orders[$i]->country_name;
				}
				$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;

			}else{
				$delivery_address_content =  "--";
			}
			/*===================================*/

			/*=======send message(email_id)=====*/

			if($orders[$i]->ok_msg == "1"){
			$send_message = '<img src="'.base_url().'assets/cp/images/'.$tickImage.'" border="0" title="message sent">';
			}else{
			$send_message = '<a href="'.base_url().'cp/orders/send_mail?email='.$orders[$i]->email_c.'&type=ok&id='.$orders[$i]->id.'">'._('OK message').'</a><br>';
			}
			if($orders[$i]->completed == "1"){
			$send_message .='&nbsp;<img src="'.base_url().'assets/cp/images/'.$tickImage.'" border="0" title="message sent">';
			}else{
			$send_message .='<a href="'.base_url().'cp/orders/send_mail?email='.$orders[$i]->email_c.'&type=completed&id='.$orders[$i]->id.'">'._('Complete').'</a>';
			}
			$email = $orders[$i]->email_c;
			/*================================*/

			/*=========status=================*/

			if($orders[$i]->order_status == "y"){
				$order_status = '<img src="'. base_url().'assets/cp/images/thumbup.jpg" border="0">';
			}elseif($orders[$i]->order_status == "n"){
				$order_status = '---';
			}
			/*================================*/
			$company_name = '';
			if(isset($orders[$i]->company_name)){
				$company_name = $orders[$i]->company_name;
			}

			$client_info_html = '';
			$client_info_html .= '<div id="show_client_'.$orders[$i]->id.'" style="display:none">';
			$client_info_html .= '<table width="100%" border="0" cellspacing="8" cellpadding="0" class="override">';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Name').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

								$name = '-----';
								if($orders[$i]->firstname_c){
									$name = $orders[$i]->firstname_c;
								}
								if($orders[$i]->lastname_c){
									$name .= '&nbsp;&nbsp;'.$orders[$i]->lastname_c;
								}

			$client_info_html .= $name.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Address').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

								$address ='---';
								if($orders[$i]->address_c){
									$address = $orders[$i]->address_c;
								}if($orders[$i]->housenumber_c){
									$address .= '&nbsp;&nbsp;'.$orders[$i]->housenumber_c;
								}

			$client_info_html .= $address.'<br />';

								$address2 = '---';
								if($orders[$i]->postcode_c){
									$address2 = $orders[$i]->postcode_c;
								}
								if($orders[$i]->city_c){
									$address2 .='&nbsp;&nbsp;'.$orders[$i]->city_c;
								}

			$client_info_html .= $address2.'<br />';

								$country = '---';
								if($orders[$i]->country_name){
									$country = $orders[$i]->country_name;
								}

			$client_info_html .= $country.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Telephone').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

								$telephone = '---';
						 		if($orders[$i]->phone_c){
						 			$telephone = $orders[$i]->phone_c;
								}

			$client_info_html .= $telephone;
			$client_info_html .= '</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Email').':</strong></td>
											<td class="td_right">
			                                   <a href="mailto:'.$orders[$i]->email_c.'?subject=Re:%20BESTELLING&amp;body='._('Dear client').',"><strong>'.$orders[$i]->email_c.'</strong></a>
			                                   <br /><br />
			                                   <a href="'.base_url().'cp/clients/lijst/client_details/'.$orders[$i]->clients_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
			                                </td>
										</tr>

									</table>
									<div class="thickbox_footer">
									<div class="thickbox_footer_text"><a href="'.base_url().'cp/orders/send_mail?email='.$orders[$i]->email_c.'&type=hold&id='.$orders[$i]->id.'">'._('ON HOLD MESSAGE').'</a></div>

									</div>
								</div>';

			$response[] = array($id,$date_created,urlencode($client_name),$order_total,urlencode($pickup_content),urlencode($delivery_address_content),urlencode($send_message),urlencode($order_status),$class,urlencode($pay_html),$notifications,$red_dot,$company_name,(($orders[$i]->image)?1:0),$client_info_html,$orders[$i]->hidden);
		}
		echo json_encode($response);
	}

	function print_ordered_products_per_supp( $dont_show_remarks = 0, $date_1 = NULL, $date_2 = NULL ){
		$this->load->model('Mproducts');

		$start_date = $date_1;
		$end_date = $date_2;

		$data['date_set_1'] = date('d-m-Y',strtotime($start_date.' 00:00:00'));
		$data['date_set_2'] = date('d-m-Y',strtotime($end_date.' 00:00:00'));

		if($start_date && $end_date){
			$data['supp_products'] = $this->Mproducts->get_supp_ordered_products($start_date,$end_date,$dont_show_remarks);
		}

		$data['dont_show_remarks'] = $dont_show_remarks;
		$this->load->view('cp/print_ordered_supp_products',$data);
	}

	function print_order_details(){

// 		$this->load->model('Morders');
// 		$this->load->model('Morder_details');
// 		$this->load->model('Mclients');
// 		$general_settings = $this->Mgeneral_settings->get_general_settings();
		/*=======to print order that has been clicked=============*/
		if($this->input->get('print_count') =='single'){

			$orders_id = $this->input->get('id');
			$print_data = $this->get_data( $orders_id );

			if( !empty( $print_data ) ){
				$print_body =  $this->load->view( 'mail_templates/'.$this->lang_u.'/print_order_custom_header', array(), true );
				$print_body .=  $this->load->view( 'mail_templates/'.$this->lang_u.'/print_order_custom', $print_data, true );
				$print_body .=  $this->load->view( 'mail_templates/'.$this->lang_u.'/print_order_custom_footer', array(), true );
				echo $print_body;
			}

// 			$order_data = $data['orderData']=$this->Morders->get_orders($orders_id);
// 			$client_id = $data['orderData']['0']->client_id;
// 			$data['activate_discount_card'] = 0;
// 			$data['discount_card_number'] = '';
// 			if($general_settings['0']->activate_discount_card  != 0){
// 				$data['activate_discount_card'] = 1;
// 				$client_info = $this->Mclients->get_clients(array('id'=>$client_id));
// 				foreach($client_info as $key=>$values)
// 					$data['discount_card_number'] = $values->discount_card_number;
// 			}

// 			$order_details_data=$data['order_details_data'] = $this->Morder_details->get_order_details($orders_id);

// 			$data['print_count'] = 'single';

// 			$total='';
// 			$TempExtracosts=array();
// 			for($i = 0; $i < count($order_details_data); $i++){
// 				if($order_details_data[$i]->add_costs != ""){
// 					$rsExtracosts = explode("#",$order_details_data[$i]->add_costs);
// 					for($j = 0; $j < count($rsExtracosts); $j++){
// 						$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
// 					}
// 				}
// 				$total += $order_details_data[$i]->total;
// 			}
// 			$data['TempExtracosts'] = $TempExtracosts;

// 			$data['total'] = $total;
// 			if($data['orderData']['0']->temp_id != 0){
// 				$this->db->select('payment_transaction.billing_option');
// 				$data['billing_option'] = $this->db->get_where('payment_transaction', array('order_id' => $data['orderData']['0']->temp_id))->result_array();
// 			}

// 			$this->load->view('cp/print_order_new',$data);
		}else if($this->input->get('print_count') =='all'){//if print all has been clicked
			$order_ids = explode(',',$this->input->get('order_ids'));

// 			$discount_number_array = array();
// 			$activate_discount_card = 0;
// 			if($general_settings['0']->activate_discount_card  != 0)
// 				$activate_discount_card = 1;

			$print_body =  $this->load->view( 'mail_templates/'.$this->lang_u.'/print_order_custom_header', array(), true );
			for($i = 0; $i < count($order_ids); $i++){
				$print_data = $this->get_data( $order_ids[$i] );
				if( !empty( $print_data ) ){
					$print_body .=  $this->load->view( 'mail_templates/'.$this->lang_u.'/print_order_custom', $print_data, true );
					$print_body .= '<br class="break">';
				}
			}
			$print_body .=  $this->load->view( 'mail_templates/'.$this->lang_u.'/print_order_custom_footer', array(), true );
			echo $print_body;die;

// 			for($i = 0; $i < count($order_ids); $i++){

// 				$order_data = $this->Morders->get_orders($order_ids[$i]);

// 				if($order_data['0']->temp_id != 0){
// 					$this->db->select('payment_transaction.billing_option');
// 					$billing_option = $this->db->get_where('payment_transaction', array('order_id' => $order_data['0']->temp_id))->result_array();
// 					if(!empty($billing_option))
// 						$order_data['0']->billing_option = $billing_option['0']['billing_option'];
// 				}

// 				$orderData[] = $order_data;
// 				if($activate_discount_card){
// 					$client_id = $order_data['0']->client_id;
// 					$client_info = $this->Mclients->get_clients(array('id'=>$client_id));
// 					foreach($client_info as $key=>$values)
// 						$discount_number_array[] = $values->discount_card_number;
// 				}
// 				$order_details_data[] = $this->Morder_details->get_order_details($order_ids[$i]);

// 			}
// 			$total=array();
// 			$TempExtracosts=array();

// 			for($i=0;$i<count($orderData);$i++){

// 				$total[$i]='';
// 				for($j = 0;$j < count($order_details_data[$i]); $j ++){
// 					if($order_details_data[$i][$j]->add_costs!=""){
// 						$rsExtracosts=explode('#',$order_details_data[$i][$j]->add_costs);
// 						for($k = 0; $k < count($rsExtracosts); $k++){
// 							$TempExtracosts[$i][$j][$k] = explode("_",$rsExtracosts[$k]);
// 						}
// 					}
// 					$total[$i] += $order_details_data[$i][$j]->total;
// 				}

// 			}

// 			$data['total']=$total;
// 			$data['TempExtracosts']=$TempExtracosts;

// 			$data['print_count'] = 'all';

// 			$data['orderData']=$orderData;
// 			$data['order_details_data']=$order_details_data;
// 			$data['activate_discount_card'] = $activate_discount_card;
// 			$data['discount_card_number'] = $discount_number_array;
// 			$this->load->view('cp/print_order_new',$data);
		}
	}
		function update_order_form(){

		if($this->input->post('act') == 'get_day_name'){
			$tempdate = explode('/',$this->input->post('date'));
			$day = date('l',strtotime($tempdate[2].'-'.$tempdate[1].'-'.$tempdate[0]));
			echo json_encode(array('day'=>$day));
		}


		$this->load->model('Morders');

		if($this->input->post('act') == 'update_default_form'){
			$dateArray = explode('/',$_POST['order_pickupdate']);
			$order_pickup_date = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];
			$update_order = array(  'order_pickupdate'=>$order_pickup_date,
									'order_pickupday'=>$_POST['order_pickupday'],
									'order_pickuptime'=>$_POST['order_pickuptime'],
									'order_remarks' =>$_POST['order_remarks']
			                     );
			$result = $this->Morders->update_order($_POST['id'],$update_order);
			if($result){
				$order_pickupdate = $_POST['order_pickupdate'];
				$returned_data = json_encode(array('RESULT' => 'OK','order_pickupdate'=>$order_pickupdate));
			}else{
				$returned_data = json_encode(array('ERROR' => 'Couldn\'t update data'));
			}
			echo $returned_data ;
		}
		if($this->input->post('act') == 'update_pickup_form'){

			$dateArray = explode('/',$_POST['order_pickupdate']);
			$order_pickup_date = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];

			$update_order = array(  'order_pickupdate'=>$order_pickup_date,
									'order_pickupday'=>$_POST['order_pickupday'],
									'order_pickuptime'=>$_POST['order_pickuptime'],
									'order_remarks' =>$_POST['order_remarks']
			);

			$result = $this->Morders->update_order($_POST['id'],$update_order);

			if($result){
				$order_pickupdate = $_POST['order_pickupdate'];
				$returned_data = json_encode(array('RESULT'=>'OK','order_pickupdate'=>$order_pickupdate));
			}else{
				$returned_data = json_encode(array('ERROR' => 'couldn\'t update data'));
			}

			echo $returned_data ;
		}
		if($this->input->post('act') == 'update_delivery_form'){


			// GET DELIVERY COST ================//
			$this->load->model('Mdelivery_settings');

			$delivery_settings = $this->Mdelivery_settings->get_delivery_settings(array('id'=>$_POST['delivery_city']));

			$delivery_cost = $delivery_settings[0]->cost;
			// ========================================//

			$dateArray = explode('/',$_POST['delivery_date']);
			$delivery_date = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];


			$update_order = array(  'delivery_date'=>$delivery_date,
									'delivery_streer_address'=>$_POST['delivery_streer_address'],
									'delivery_area'=>$_POST['delivery_area'],
									'delivery_city' =>$_POST['delivery_city'],
									'delivery_zip' => $_POST['delivery_zip'],
									'delivery_day' => $_POST['delivery_day'],
									'delivery_hour' => $_POST['delivery_hour'],
									'delivery_minute' => $_POST['delivery_minute'],
									'delivery_remarks' => $_POST['delivery_remarks'],
									'delivery_cost' => $delivery_cost,
			);
			$result = $this->Morders->update_order($_POST['id'],$update_order);
			if($result){
				$delivery_date1 = $_POST['delivery_date'];

				$select = array( 'orders.order_total' );
				$order = $this->Morders->get_orders( $select, $_POST['id'] );
				$order_total = $order[0]->order_total;

				$returned_data = json_encode(array('RESULT'=>'OK','delivery_date'=>$delivery_date1,'delivery_cost'=>$delivery_cost,'order_total'=>$order_total));
			}else{
				$returned_data = json_encode(array('ERROR'=>'Error while updating.'));

			}

			echo $returned_data ;
		}
		if($this->input->post('act') == 'update_delivery'){


			// GET DELIVERY COST ================//
			$this->load->model('Mdelivery_settings');

			// ========================================//



			$update_order = array(
									'delivery_remarks' => $_POST['delivery_remarks'],

			);
			$result = $this->Morders->update_order($_POST['id'],$update_order);
			if($result){


				$select = array( 'orders.order_total' );
				$order = $this->Morders->get_orders( $select, $_POST['id'] );
				$order_total = $order[0]->order_total;

				$returned_data = json_encode(array('RESULT'=>'OK','order_total'=>$order_total));
			}else{
				$returned_data = json_encode(array('ERROR'=>'Error while updating.'));

			}

			echo $returned_data ;
		}
	}

	function ajax_cancelled_order(){

		$this->load->model('Morders');

		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$orders = array();
		if($this->company_role == 'super'){
			$final_order = array();
			$sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

			foreach($sub_companies as $sub_company){
				$orders = $this->Morders->get_cancelled_orders(null,$limit,$start,$sub_company->company_id);
				$final_order = array_merge($final_order,$orders);
			}

			usort($final_order, create_function('$a, $b',
			'if ($a->created_date == $b->created_date) return 0; return ($a->created_date > $b->created_date) ? -1 : 1;'));
			$orders = $final_order;
		}else{
			$orders = $this->Morders->get_cancelled_orders(null,$limit,$start);
		}


		$response = array();
		for($i =0; $i<count($orders);$i++){

			$is_passed = false;

			/*====store id===*/
			$id = $orders[$i]->id;
			/*===============*/

			/*====cancelled mail status===*/
			$cancel_mail_status = $orders[$i]->cancel_mail_status;
			/*===============*/

			/*======date created=======*/
			$date_created = date("d/m/y",strtotime($orders[$i]->created_date));
			/*=========================*/

			/*=======store name=======*/
			$client_name = $orders[$i]->firstname_c." ".$orders[$i]->lastname_c;
			/*========================*/

			//echo $client_order_count[$orders[$i]->client_id];
			$red_dot = (!empty($client_order_count) && isset($client_order_count[$orders[$i]->client_id]) && $client_order_count[$orders[$i]->client_id]==1)?1:0;
			$notifications = $orders[$i]->notifications;

			/*=========store total========*/
			$order_total = ((float)$orders[$i]->order_total)+((float)$orders[$i]->delivery_cost)-(float)( $orders[$i]->disc_amount + $orders[$i]->disc_client_amount);
			/*============================*/

			/*==========pickup content==========*/
			if($orders[$i]->order_pickuptime != ""){

				$pickup_content = date("d / m",strtotime($orders[$i]->order_pickupdate))." <br>om ".$orders[$i]->order_pickuptime;

				$pickup_day = date("D",strtotime($orders[$i]->order_pickupdate));

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

				$pickup_content = $pickup_day.' '.$pickup_content;
				$date1 = new DateTime('now');
				$date2 = new DateTime(date("Y-m-d H:i:s",strtotime($orders[$i]->order_pickupdate.' '.$orders[$i]->order_pickuptime)));
				$is_passed = ($date1 > $date2)?true:false;
			}else{
				$pickup_content = "--";
			}
			/*===================================*/

			/*=======delivery_content============*/
			if($orders[$i]->delivery_streer_address!= ""){

				$delivery_address_content = ''.$orders[$i]->delivery_streer_address.'&nbsp;';

				if($orders[$i]->delivery_zip)
				{
					$delivery_address_content .= _('-').'  '.$orders[$i]->delivery_zip.'<br />';

					$delivery_date_content =date("d/m",strtotime($orders[$i]->delivery_date))." ".$orders[$i]->delivery_hour.":".$orders[$i]->delivery_minute;

					$delivery_day = date("D",strtotime($orders[$i]->delivery_date));

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

					//$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;
				}

				if (isset($orders[$i]->phone_reciever) && $orders[$i]->phone_reciever != '' && isset($orders[$i]->delivery_city) && $orders[$i]->delivery_city){
					$delivery_address_content .='&nbsp;'.$orders[$i]->delivery_city.', ';
				}elseif(isset($orders[$i]->delivery_area_name) || isset($orders[$i]->delivery_city_name)){
					$delivery_address_content .='&nbsp;'.$orders[$i]->delivery_city_name.', '.$orders[$i]->delivery_area_name.', ';
				}

				if(isset($orders[$i]->phone_reciever) && isset($orders[$i]->delivery_country_name)){
					$delivery_address_content .= $orders[$i]->delivery_country_name;
				}
				elseif(isset($orders[$i]->country_name)){
					$delivery_address_content .= $orders[$i]->country_name;
				}

				$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;

				$date1 = new DateTime('now');
				$date2 = new DateTime(date("Y-m-d H:i:s",strtotime($orders[$i]->delivery_date.' '.$orders[$i]->delivery_hour.':'.$orders[$i]->delivery_minute)));
				$is_passed = ($date1 > $date2)?true:false;
			}else{
				$delivery_address_content =  "--";
			}

			/*=======send message(email_id)=====*/
			$email = $orders[$i]->email_c;

			/*================================*/
			$company_name = '';
			if(isset($orders[$i]->company_name)){
				$company_name = $orders[$i]->company_name;
			}

			$client_info_html = '';
			$client_info_html .= '<div id="show_client_'.$orders[$i]->id.'" style="display:none">';
			$client_info_html .= '<table width="100%" border="0" cellspacing="8" cellpadding="0" class="override">';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Name').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

			$name = '-----';
			if($orders[$i]->firstname_c){
				$name = $orders[$i]->firstname_c;
			}
			if($orders[$i]->lastname_c){
				$name .= '&nbsp;&nbsp;'.$orders[$i]->lastname_c;
			}

			$client_info_html .= $name.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Address').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

			$address ='---';
			if($orders[$i]->address_c){
				$address = $orders[$i]->address_c;
			}if($orders[$i]->housenumber_c){
				$address .= '&nbsp;&nbsp;'.$orders[$i]->housenumber_c;
			}

			$client_info_html .= $address.'<br />';

			$address2 = '---';
			if($orders[$i]->postcode_c){
				$address2 = $orders[$i]->postcode_c;
			}
			if($orders[$i]->city_c){
				$address2 .='&nbsp;&nbsp;'.$orders[$i]->city_c;
			}

			$client_info_html .= $address2.'<br />';

			$country = '---';
			if($orders[$i]->country_name){
				$country = $orders[$i]->country_name;
			}

			$client_info_html .= $country.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Telephone').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

			$telephone = '---';
			if($orders[$i]->phone_c){
				$telephone = $orders[$i]->phone_c;
			}

			$client_info_html .= $telephone;
			$client_info_html .= '</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Email').':</strong></td>
											<td class="td_right">
			                                   <a href="mailto:'.$orders[$i]->email_c.'?subject=Re:%20BESTELLING&amp;body='._('Dear client').',"><strong>'.$orders[$i]->email_c.'</strong></a>
			                                   <br /><br />
			                                   <a href="'.base_url().'cp/clients/lijst/client_details/'.$orders[$i]->client_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
			                                </td>
										</tr>

									</table>';
			/* $client_info_html .= '	<div class="thickbox_footer">';
			$client_info_html .= '	<div class="thickbox_footer_text"><a href="'.base_url().'cp/cdashboard/send_mail?email='.$orders[$i]->email_c.'&type=hold&id='.$orders[$i]->id.'">'._('ON HOLD MESSAGE').'</a></div>';
			$client_info_html .= '	</div>'; */
			$client_info_html .= '</div>';

			$response[] = array($id,$date_created,urlencode($client_name),round($order_total,2),urlencode($pickup_content),urlencode($delivery_address_content),'','','','','',$red_dot,$company_name,(($orders[$i]->image)?1:0),$client_info_html,$is_passed,$cancel_mail_status);
		}
		echo json_encode($response);
	}
	function send_mail(){

		header('Content-Type: text/html; charset=utf-8');

		/*=======data sent by href========*/
		$email= $this->input->get('email');
		$type = $this->input->get('type');
		$order_id = $this->input->get('id');
		/*================================*/

		/*============loading models=============*/
		$this->load->model('Morders');
		$this->load->model('Mmail_queue');
		$this->load->model('Morder_details');
		$this->load->model('Mdelivery_areas');
		$this->load->model('Mdelivery_settings');
		/*========================================*/

		$select = array( 
					'orders.company_id',
					'orders.option',
					'orders.lang_id',
					'company.company_name',
					'orders.order_pickuptime',
					'orders.order_pickupdate',
					'orders.order_pickupday',
					'orders.order_remarks',
					'orders.delivery_streer_address',
					'orders.delivery_date',
					'orders.delivery_hour',
					'orders.delivery_minute',
					'orders.delivery_day',
					'orders.delivery_remarks', 
					'orders.order_total', 
					'orders.delivery_cost',
					'orders.id',
					'orders.created_date',
					'clients.firstname_c',
					'clients.lastname_c',
					'clients.company_c',
					'clients.address_c',
					'clients.housenumber_c',
					'clients.postcode_c',
					'clients.phone_c',
					'clients.mobile_c',
					'clients.city_c',
					'clients.email_c',
					'country.country_name as country_name'
				);
		$order_data = $this->Morders->get_orders( $select, $order_id );

		$order_details_data = $this->Morder_details->get_order_details($order_id);

		$general_settings = $this->Mgeneral_settings->get_general_settings(array("company_id" => $order_data[0]->company_id));

		$is_set_activate_card_number = $general_settings['0']->activate_discount_card;
		$client_info = $this->db->where('email_c',$email)->get('clients')->row();
		$client_discount_card_number = '';
		$client_discount_card = $this->db->get_where("client_numbers", array("client_id" => $client_info->id, "company_id" => $this->company_id))->result_array();
		if(!empty($client_discount_card)){
			$client_discount_card = $client_discount_card['0'];
			$client_discount_card_number = $client_discount_card['discount_card_number'];
		}

		$TempExtracosts=array();
		for($i = 0; $i < count($order_details_data); $i++){

			if($order_details_data[$i]->add_costs != ""){
				$rsExtracosts = explode("#",$order_details_data[$i]->add_costs);
				for($j = 0; $j < count($rsExtracosts); $j++){
					$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
				}
			}else{
				$TempExtracosts[$i]=''; //this condition will be checked in order_details_edit.php
			}
		}

		$disable_price = $general_settings[0]->disable_price;
		$option = $order_data[0]->option;

		// Selecting language file
		if($order_data[0]->lang_id == 1)
			$this->lang->load('mail', 'english' );
		elseif($order_data[0]->lang_id == 2)
			$this->lang->load('mail', 'dutch' );
		elseif($order_data[0]->lang_id == 3)
			$this->lang->load('mail', 'french' );
		else
			$this->lang->load('mail', 'dutch' );

		$company_name = $order_data['0']->company_name;
		/*==============mail maeessage send according to the type ==============*/

		$subject_status = '';
		if($type == 'hold'){
			$subject_status = $this->lang->line('mail_order_hold');
			$OrderMessage = $general_settings[0]->hold_msg;

		}else if($type == 'completed'){
		    $subject_status = $this->lang->line('mail_order_completed');
			if($option == '2'){
				$OrderMessage = $general_settings[0]->completeddelivery_msg;
			}else if($option == '1'){
				$OrderMessage = $general_settings[0]->completedpickup_msg;
			}else if($option == '0'){
				$OrderMessage = $general_settings[0]->completedpickup_msg;
			}
		}else if($type == 'ok'){
			$subject_status = $this->lang->line('mail_order_completed');
			$OrderMessage = $general_settings[0]->ok_msg;
		}
		/*$Options parameter used in the mail boby*/
		/* EDITED BY CARL !!!*/

		$Options='<tr><td>';
		if($order_data[0]->option == "0"){

			$Options .= '<ul>';
			$Options .= '<li>'.$this->lang->line('mail_pickup_date').' : '.date('d/m/Y',strtotime($order_data[0]->order_pickupdate.' 00:00:00')).' '.$this->lang->line('mail_day_on').' '.$order_data[0]->order_pickuptime.''.$this->lang->line('mail_time_hour').'</li>';
			$Options .= '<li>'.$this->lang->line('mail_comment').' : '.$order_data[0]->order_remarks.'</li>';
			$Options .= '<li>'.$this->lang->line('mail_shop').' : '.$company_name.'</li>';
			$Options .= '</ul>';

		}else if($order_data[0]->option == "1"){

			$Options .= '<ul>';
			$Options .= '<li>'.$order_data[0]->order_pickupday.' '.$this->lang->line('mail_pickup_date').' : '.date('d/m/Y',strtotime($order_data[0]->order_pickupdate.' 00:00:00')).' '.$this->lang->line('mail_day_on').' '.$order_data[0]->order_pickuptime.''.$this->lang->line('mail_time_hour').'</li>';
			$Options .= '<li>'.$this->lang->line('mail_comment').' : '.$order_data[0]->order_remarks.'</li>';
			$Options .= '<li>'.$this->lang->line('mail_shop').' : '.$company_name.'</li>';
			$Options .= '</ul>';

		}else if($order_data[0]->option == "2"){

			$Options .= '<ul>';
			$Options .= '<li>'.$this->lang->line('mail_delivery_date').' : '.date('d/m/Y',strtotime($order_data[0]->delivery_date.' 00:00:00')).'</li>';
			$Options .= '<li>'.$this->lang->line('mail_delivery_day').' : '.$order_data[0]->delivery_day.'</li>';
			$Options .= '<li>'.$this->lang->line('mail_delivery_address').' : '.$order_data[0]->delivery_streer_address.'</li>';
			$Options .= '<li>'.$this->lang->line('mail_area_name').' : '.$order_data[0]->order_remarks.'</li>';
			$Options .= '<li>'.$this->lang->line('mail_delivery_time').' : '.$order_data[0]->delivery_hour.':'.$order_data[0]->delivery_minute.'</li>';
			$Options .= '<li>'.$this->lang->line('mail_comment').' : '.$order_data[0]->delivery_remarks.'</li>';
			$Options .= '</ul>';

		}
		$Options .= '</td></tr>';
		//Options2 shows the price row in mail
		$Options2='';

		if($order_data[0]->option == '2' && $disable_price != '1'){

			$total_cost = (float)$order_data[0]->delivery_cost+$order_data[0]->order_total;

			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_order_total').'</td><td align="right">'.round($order_data[0]->order_total,2).'&euro;</td></tr>';
			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_delivery_cost').'</td><td align="right">'.round($order_data[0]->delivery_cost,2).'&euro;</td></tr>';
			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_total').'</td><td align="right">'.round($total_cost,2).'&euro;</td></tr>';
			if($is_set_activate_card_number != 0){
				$Options2 .= '<tr>';
				$Options2 .= '<td colspan=5 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.(($client_discount_card_number != '')?$client_discount_card_number:'--').'</td>';
				$Options2 .= '</tr>';
			}

		}else if($order_data[0]->option == '1' && $disable_price != '1'){

			$total_cost = $order_data[0]->order_total;

			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_order_total').'</td><td align="right">'.round($order_data[0]->order_total,2).'&euro;</td></tr>';
			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_total').'</td><td align="right">'.round($total_cost,2).'&euro;</td></tr>';
			if($is_set_activate_card_number != 0){
				$Options2 .= '<tr>';
				$Options2 .= '<td colspan=5 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.(($client_discount_card_number != '')?$client_discount_card_number:'--').'</td>';
				$Options2 .= '</tr>';
			}

		}
		else if($order_data[0]->option == '0' && $disable_price != '1'){

			$total_cost = $order_data[0]->order_total;

			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_order_total').'</td><td align="right">'.round($order_data[0]->order_total,2).'&euro;</td></tr>';
			$Options2 .= '<tr>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td>&nbsp;</td>';
			$Options2 .= '<td align="right">'.$this->lang->line('mail_total').'</td><td align="right">'.round($total_cost,2).'&euro;</td></tr>';
			if($is_set_activate_card_number != 0){
				$Options2 .= '<tr>';
				$Options2 .= '<td colspan=5 align="left"><b>'.$this->lang->line('mail_discount_card_number').':</b>&nbsp;'.(($client_discount_card_number != '')?$client_discount_card_number:'--').'</td>';
				$Options2 .= '</tr>';
			}

		}

		/*mail Body for the mail which needs to be send*/
		if($disable_price == "0"){
			//$getMail = file_get_contents(base_url().'assets/mail_templates/cp_order_mail.html');//mail template
			$getMail = $this->load->view('mail_templates/'.$this->lang_u.'/cp_order_mail',null,true);//mail template

			$MailBody = "";
			for($i=0;$i<count($order_details_data);$i++){
				$MailBody .='<tr>';
				$MailBody .='<td>'.$order_details_data[$i]->proname;
				if($order_details_data[$i]->type == '1'){
					$MailBody .='&nbsp;&nbsp;<img src='.base_url().'/assets/cp/images/new.png>';
				}
				if($order_details_data[$i]->discount != '' && $order_details_data[$i]->discount !='multi'){
					$MailBody .='&nbsp;&nbsp;<img src='.base_url().'/assets/cp/images/message.png>';
				}else if($order_details_data[$i]->discount != '' && $order_details_data[$i]->discount == 'multi'){
					$MailBody .='&nbsp;&nbsp;<img src='.base_url().'/assets/cp/images/discount.png>';
				}
				$MailBody .='</td>';
				$MailBody .='<td>'.$order_details_data[$i]->quantity.'</td>';
				$unit = '';
				if($order_details_data[$i]->content_type == 0){
					$unit = '&euro;';
				}else if( $order_details_data[$i]->content_type == 2 ){
					$unit = '&euro;/Per p.';
				}else{
					$unit = '&euro;/Kg';
				}
				$MailBody .='<td>'.round($order_details_data[$i]->sub_total,2).'&nbsp;'.$unit.'</td>';
				$MailBody .='<td>';

				if($TempExtracosts[$i] != ''){
					for($j=0;$j<count($TempExtracosts[$i]);$j++){
						if(array_key_exists($j,$TempExtracosts[$i])){
							$MailBody .='<span style\"color:red\">'.$TempExtracosts[$i][$j][0].'</span>';
							$MailBody .='&nbsp;&nbsp;&nbsp;<span style\"color:red\">'.$TempExtracosts[$i][$j][1].'</span>';
							$MailBody .='&nbsp;&nbsp;&nbsp;<span> = '.$TempExtracosts[$i][$j][2].'</span><br/>';
						}
					}
				}

				if($order_details_data[$i]->discount == "multi" && !empty($order_details_data[$i]->discount_per_qty)){
					$MailBody .='<span style=\"color:red;\">'.$this->lang->line('mail_extra_discount').':</span>&nbsp;'.round( ((float)$order_details_data[$i]->discount_per_qty), 2).'&euro;';
				}

				$MailBody .='<br />';

				if($order_details_data[$i]->pro_remark)
				   $MailBody .='<b>'.$this->lang->line('mail_note').':</b> '.$order_details_data[$i]->pro_remark;
				else
				   $MailBody .=' -- ';

				$MailBody .='</td>';
				$MailBody .='<td align="right">'.round($order_details_data[$i]->total,2).'&euro;</td>';
				$MailBody .='</tr>';
			}

		}else if($disable_price == "1"){

			//$getMail = file_get_contents(base_url().'assets/mail_templates/pricefeature_cpOrderMail.html');
			$getMail = $this->load->view('mail_templates/'.$this->lang_u.'/pricefeature_cpOrderMail',null,true);
			//mail template for disabled price mail
			$MailBody = "";
			for($i=0;$i<count($order_details_data);$i++){

				$MailBody .='<tr>';
				$MailBody .='<td>'.$order_details_data[$i]->proname;
				if($order_details_data[$i]->type == '1'){
					$MailBody .='&nbsp;&nbsp;<img src='.base_url().'/assets/cp/images/new.png>';
				}
				if($order_details_data[$i]->discount != '' && $order_details_data[$i]->discount !='multi'){
					$MailBody .='&nbsp;&nbsp;<img src='.base_url().'/assets/cp/images/message.png>';
				}else if($order_details_data[$i]->discount != '' && $order_details_data[$i]->discount == 'multi'){
					$MailBody .='&nbsp;&nbsp;<img src='.base_url().'/assets/cp/images/discount.jpg>';
				}
				$MailBody .='</td>';
				$MailBody .='<td align="center">'.$order_details_data[$i]->quantity.'</td>';

				$MailBody .='<td>'.$order_details_data[$i]->pro_remark.'</td>';
				$MailBody .='</tr>';
			}
		}
		/*=========Email  ads =============*/
		if($this->company->email_ads == "1"){
			$this->load->model('Memail_messages');
			$email_messages_detail = $this->Memail_messages->get_email_messages();
			$Options3 = $email_messages_detail[0]->emailads_text_message;
		}else{
			$Options3 = "";
		}

		/*========array to be parsed in  template=========*/
		$parse_email_array = array(
									"order_no_text" => $this->lang->line('mail_order_no'),
									"date_created_text" => $this->lang->line('mail_order_date'),
									"customer_data_text" => $this->lang->line('mail_customer_data'),
									"mobile_text" => $this->lang->line('mail_mobile'),
									"phone_text" => $this->lang->line('mail_phone'),
									"email_text" => $this->lang->line('mail_email'),
									"product_text" => $this->lang->line('mail_product'),
									"qauntity_text" => $this->lang->line('mail_quantity'),
									"price_text" => $this->lang->line('mail_rate'),
									"extra_text" => $this->lang->line('mail_extra'),
									"total_text" => $this->lang->line('mail_total'),
									"regard_text" => $this->lang->line('mail_regards'),
									"the_text" => $this->lang->line('mail_the'),
									"OrderMessage"=>$OrderMessage,
									"Name"=>$order_data[0]->firstname_c." ".$order_data[0]->lastname_c,
									"order_no"=>$order_data[0]->id,
									"date_created"=>date('d-m-Y',strtotime($order_data[0]->created_date)),
									"client_company"=>$order_data[0]->company_c,
									"first_name"=>$order_data[0]->firstname_c,
									"last_name"=>$order_data[0]->lastname_c,
									"house_no"=>$order_data[0]->housenumber_c,
									"address"=>$order_data[0]->address_c,
									"city"=>$order_data[0]->city_c,
									"postcode"=>$order_data[0]->postcode_c,
									"country"=>$order_data[0]->country_name,
									"mobile"=>$order_data[0]->mobile_c,
									"phone"=>$order_data[0]->phone_c,
									"email"=>$order_data[0]->email_c,
									"Options"=>$Options,
									"Options2"=>$Options2,
									"MailBody"=>$MailBody,
									"CompanyName"=>$company_name,
									"total"=>$order_data[0]->order_total,
									"Options3"=>$Options3
								  );

		$mail_body = $this->utilities->parseMailText($getMail,$parse_email_array);

		$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';

		if(!$mail_body){
			echo _('Couldn\'t parse the mail template');
		}

		if($type == "hold" || $type == 'completed' || $type == 'ok'){

			$flag = send_email($email, $general_settings[0]->emailid, $general_settings[0]->subject_emails, $mail_body, ($this->lang->line('mail_status').' - '.$subject_status), NULL, NULL, 'company', 'client', 'send_order_status');
		}else{

			$mail_queue_array = array(  'batch_name' => 'Client Order',
										'mail_subject' => ($general_settings[0]->subject_emails)?($general_settings[0]->subject_emails):($this->lang->line('mail_status').' - '.$subject_status),
										'mail_to' => $email,
										'mail_body_html' => addslashes($mail_body),
										'mail_body_text' => '',
										'mail_cc' => '',
										'mail_bcc' => '',
										'mailfrom_email' => ($general_settings[0]->emailid)?$general_settings[0]->emailid:$this->config->item('reply_email'),
										'mailfrom_subject' => 'Bestelling '.$company_name
									  );

			$flag = $this->Mmail_queue->insert_mail($mail_queue_array);
		}

		if($flag){
			if($type == 'completed'){
				$fields["completed"] = '1';
				$fields["order_status"] = 'y';
			}else if($type == 'hold'){
				$fields["hold_msg"] = '1';
				$fields["ok_msg"] = '0';
				$fields["completed"] = '0';
				$fields["order_status"] = 'n';
			}else{
				$fields["ok_msg"] = '1';
			}
			$this->messages->clear();
			$updated = $this->Morders->update_order($order_id,$fields);
			if($updated){
				$this->messages->add(_('Mail has been sent successfully.'),'success');
				redirect('cp/orders','refresh');
			}
		}else{
			$this->messages->add('Some error occured.','error');
			redirect('cp/orders','refresh');
		}
	}

	function print_labeler($order_id = null, $type = null)
	{
		//$order_id = $this->input->post('order_id');
		$response = array();
		if($order_id && is_numeric($order_id))
		{
			$general_settings = $this->Mgeneral_settings->get_general_settings();
			if($general_settings['0']->activate_labeler)
			{

				$doc_text = array();
				$height = (int)(41/25.4)*72;
				$width = (int)(89/25.4)*72;
				if($type == 'per_order')
				{

					// Adding Order ID
					$doc_text['order_id'] = $order_id;

					$this->load->model('Morders');

					$select = array( 
						'orders.company_id',
						'orders.option',
						'orders.order_pickuptime',
						'orders.order_pickupdate',
						'orders.order_pickupday',
						'orders.order_remarks',
						'orders.delivery_date',
						'orders.delivery_hour',
						'orders.delivery_minute',
						'orders.delivery_day',
						'orders.delivery_remarks', 
						'orders.order_total', 
						'orders.id',
						'clients.firstname_c',
						'clients.lastname_c',
						'clients.company_c',
						'clients.address_c',
						'clients.housenumber_c',
						'clients.postcode_c',
						'clients.phone_c',
						'clients.mobile_c',
						'clients.city_c',
						'clients.email_c'
					);

					$order_data = $this->Morders->get_orders( $select, $order_id );
					$total_price = round($order_data['0']->order_total,2);
					$total_price = (string)$total_price;
					//$total_price = str_replace('.',',',$total_price);
					// Adding user name and total amount
					$doc_text['name'] = str_replace("&","en",$order_data['0']->firstname_c." ".$order_data['0']->lastname_c);

					//Adding company name
					$doc_text['company_c'] = $order_data['0']->company_c;

					// Adding Total amount
					$doc_text['amount'] = $total_price;

					// Adding address
					$add_text = '';
					$add_text .= addslashes($order_data['0']->address_c)." ".addslashes($order_data['0']->housenumber_c)."\n".addslashes($order_data['0']->postcode_c)." ".$order_data['0']->city_c."\n";

					// Adding phone number
					if($order_data['0']->phone_c)
					{
						$add_text .= $order_data['0']->phone_c."\n";
					}
					elseif($order_data['0']->mobile_c)
					{
						$add_text .= $order_data['0']->mobile_c."\n";
					}

					$doc_text['address'] = $add_text;
					// Adding pickup date
					$date_content = '--';
					if($order_data['0']->option == 1)
					{

						$remark = str_replace("&","en",$order_data['0']->order_remarks);
						if(strlen($remark) >= 100)
						{
							$remark = substr($remark,0,100)."...";
						}
						$remark = addslashes($remark);

						$doc_text['remark'] = '';
						if($remark != '')
						{
							$doc_text['remark'] = _("Opmerking").": ".$remark;
						}

						$pickup_content = date("d / m",strtotime($order_data['0']->order_pickupdate))." om ".$order_data['0']->order_pickuptime;

						$pickup_day = date("D",strtotime($order_data['0']->order_pickupdate));

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

					}
					elseif($order_data['0']->option == 2)
					{

						$remark = str_replace("&","en",$order_data['0']->delivery_remarks);
						if(strlen($remark) >= 100){
							$remark = substr($remark,0,100)."...";
						}
						$remark = addslashes($remark);

						$doc_text['remark'] = '';
						if($remark != ''){
							$doc_text['remark'] = _("Opmerking").": ".$remark;
						}

						$delivery_date_content =date("d/m",strtotime($order_data['0']->delivery_date))." om ".$order_data['0']->delivery_hour.":".$order_data['0']->delivery_minute;

						$delivery_day = date("D",strtotime($order_data['0']->delivery_date));

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

					$response = array('error'=>0,'message'=>'','data'=>$doc_text);
				}
				elseif($type == 'per_product')
				{

					$this->load->model('Morder_details');
					$this->load->model('Morders');
 		
 					$select = array( 
						'clients.firstname_c',
						'clients.lastname_c',
						'clients.company_c'
					);

					$order_data_pre = $this->Morders->get_orders( $select, $order_id );
					$order_data = $this->Morder_details->get_order_details($order_id);
					$data_array = array();
					$i = 0;

					foreach($order_data as $values)
					{

						$quantity_unit = '';
						if($values->content_type != 1 && $values->content_type != 2)
						{
							for($j = 0; $j < (int)$values->quantity; $j++)
							{
								$doc_text = array();

								$doc_text['c_name'] = str_replace("&","en",$order_data_pre['0']->firstname_c." ".$order_data_pre['0']->lastname_c);
								$doc_text['company'] = $order_data_pre['0']->company_c;
								$total_price = round($values->total,2)/$values->quantity;
								$total_price = (string)$total_price;
								$total_price = str_replace('.',',',$total_price);

								// Adding product name and total amount
								$doc_text['name'] = str_replace("&","en",$values->proname);
								$doc_text['default_price'] = round($values->default_price,2);
								$doc_text['amount'] = $total_price;

								// Adding groups/options
								$doc_text['extra'] = '';
								if($values->add_costs != '')
								{
									$grp_text = '';
									$extra_array = explode("#",$values->add_costs);
									foreach($extra_array as $extra_value)
									{
										$extra_value_array = explode("_",$extra_value);
										$grp_text .= $extra_value_array['0'].':'.$extra_value_array['1'].'='.$extra_value_array['2']."\n";
									}
									$doc_text['extra'] = $grp_text;
								}

								// Adding remark
								$remark = str_replace("&","en",$values->pro_remark);
								$length = strlen($remark);
								$loop = floor($length/50);
								for($counter = 1; $counter <= $loop; $counter++)
								{
									$remark = substr_replace($remark,"\n",($counter*50),0);
								}

								$doc_text['remark'] = '';
								if($remark != '')
								{
									$doc_text['remark'] = "Opmerking: ".addslashes($remark);
								}

								$doc_text['extra_field_text'] = '';
								if($values->extra_field != '')
									$doc_text['extra_field_text'] = $values->extra_field.' : '.$values->extra_name;

								$data_array[$i] = $doc_text;
								$i++;
							}
						}
						else
						{
							if($values->content_type == 1)
								$quantity_unit = 'gr.';
							if($values->content_type == 2)
								$quantity_unit = 'pers.';

							$doc_text = array();

							$doc_text['c_name'] = $order_data_pre['0']->firstname_c." ".$order_data_pre['0']->lastname_c;
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
							$doc_text['name'] = $values->quantity." ".$quantity_unit." ".str_replace("&","en",$values->proname);
							$doc_text['default_price'] = round($values->default_price,2);
							$doc_text['amount'] = $total_price;

							// Adding groups/options
							$doc_text['extra'] = '';
							if($values->add_costs != '')
							{
								$grp_text = '';
								$extra_array = explode("#",$values->add_costs);
								foreach($extra_array as $extra_value)
								{
									$extra_value_array = explode("_",$extra_value);
									$grp_text .= $extra_value_array['0'].':'.$extra_value_array['1'].'='.$extra_value_array['2']."\n";
								}
								$doc_text['extra'] = $grp_text;
							}

							// Adding remark
							$remark = str_replace("&","en",$values->pro_remark);
							$length = strlen($remark);
							$loop = floor($length/50);
							for($counter = 1; $counter <= $loop; $counter++)
							{
								$remark = substr_replace($remark,"\n",($counter*50),0);
							}

							$doc_text['remark'] = '';
							if($remark != '')
							{
								$doc_text['remark'] = "Opmerking: ".addslashes($remark);
							}

							$doc_text['extra_field_text'] = '';
							if($values->extra_field != '')
								$doc_text['extra_field_text'] = $values->extra_field.' : '.$values->extra_name;

							$data_array[$i] = $doc_text;
							$i++;
						}
					}
				$response = array('error'=>0,'message'=>'array','data'=>$data_array);
				}
			}
			else
			{
				$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
			}
		}
		else
		{
			$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
		}
		echo json_encode($response);
	}

	function get_subcategory(){
		$this->load->model('Mproducts');
		$this->load->model('Msubcategories');

		$catid = $this->input->post('catid');
		if($catid != '' && $catid != '0'){

		    $catid = (int)$catid;
			$result=$this->Msubcategories->get_sub_category($catid);

			if( !empty( $result ) ){

				$return_data = (json_encode(array('success'=>$result)));

			}else{

				$products = $this->Mproducts->get_products( $catid, '', '', '', false );

				if( !empty($products) ){
					$return_data = json_encode(array('error'=>'no_sub_cat','products'=>$products));
				}else{
					$return_data = json_encode(array('error'=>'no_sub_cat','products'=>'no_products'));
				}
			}
		}else{
			$return_data = json_encode(array('error'=>'no_cat_id'));
		}
		echo $return_data;
	}

	function get_products(){

		$order_id=$this->input->post('id');
		$catid=$this->input->post('catid');
		$subcatid=$this->input->post('subcatid');

		if($catid){
			$this->load->model('Mproducts');
			$products=$this->Mproducts->get_products($catid,$subcatid);

			if($products != array()){
				$return_data = json_encode(array('success'=>$products));
			}else{
				$return_data = json_encode(array('error'=>'no_products'));
			}

		}else{
			$return_data = json_encode(array('error'=>'no_catid'));
		}
		echo $return_data;
	}

	function ajax_pending_order(){

		$this->load->model('Morders');

		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$orders = array();
		if($this->company_role == 'super'){
			$final_order = array();
			$sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

			foreach($sub_companies as $sub_company){
				$orders = $this->Morders->get_pending_orders(null,$limit,$start,$sub_company->company_id);
				$final_order = array_merge($final_order,$orders);
			}

			usort($final_order, create_function('$a, $b',
			'if ($a->created_date == $b->created_date) return 0; return ($a->created_date > $b->created_date) ? -1 : 1;'));
			$orders = $final_order;
		}else{
			$orders = $this->Morders->get_pending_orders(null,$limit,$start);
		}


		$response = array();
		for($i =0; $i<count($orders);$i++){

			/*====store id===*/
			$id = $orders[$i]->id;
			/*===============*/

			/*======date created=======*/
			$date_created = date("d/m/y",strtotime($orders[$i]->created_date));
			/*=========================*/

			/*=======store name=======*/
			$client_name = $orders[$i]->firstname_c." ".$orders[$i]->lastname_c;
			/*========================*/

			//echo $client_order_count[$orders[$i]->client_id];
			$red_dot = (!empty($client_order_count) && isset($client_order_count[$orders[$i]->client_id]) && $client_order_count[$orders[$i]->client_id]==1)?1:0;
			$notifications = $orders[$i]->notifications;

			/*=========store total========*/
			$order_total = ((float)$orders[$i]->order_total)+((float)$orders[$i]->delivery_cost)-(float)( $orders[$i]->disc_amount + $orders[$i]->disc_client_amount);
			/*============================*/

			/*==========pickup content==========*/
			if($orders[$i]->order_pickuptime != ""){

				$pickup_content = date("d / m",strtotime($orders[$i]->order_pickupdate))." <br>om ".$orders[$i]->order_pickuptime;

				$pickup_day = date("D",strtotime($orders[$i]->order_pickupdate));

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

				$pickup_content = $pickup_day.' '.$pickup_content;

			}else{
				$pickup_content = "--";
			}
			/*===================================*/

			/*=======delivery_content============*/
			if($orders[$i]->delivery_streer_address!= ""){

				$delivery_address_content = ''.$orders[$i]->delivery_streer_address.'&nbsp;';

				if($orders[$i]->delivery_zip)
				{
					$delivery_address_content .= _('-').'  '.$orders[$i]->delivery_zip.'<br />';

					$delivery_date_content =date("d/m",strtotime($orders[$i]->delivery_date))." ".$orders[$i]->delivery_hour.":".$orders[$i]->delivery_minute;

					$delivery_day = date("D",strtotime($orders[$i]->delivery_date));

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

					//$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;
				}

				if (isset($orders[$i]->phone_reciever) && $orders[$i]->phone_reciever != '' && isset($orders[$i]->delivery_city) && $orders[$i]->delivery_city){
					$delivery_address_content .='&nbsp;'.$orders[$i]->delivery_city.', ';
				}elseif(isset($orders[$i]->delivery_area_name) || isset($orders[$i]->delivery_city_name)){
					$delivery_address_content .='/&nbsp;'.$orders[$i]->delivery_city_name.', '.$orders[$i]->delivery_area_name.', ';
				}

				if(isset($orders[$i]->phone_reciever) && isset($orders[$i]->delivery_country_name)){
					$delivery_address_content .= $orders[$i]->delivery_country_name;
				}
				elseif(isset($orders[$i]->country_name)){
					$delivery_address_content .= $orders[$i]->country_name;
				}

				$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;

			}else{
				$delivery_address_content =  "--";
			}

			/*=======send message(email_id)=====*/
			$email = $orders[$i]->email_c;

			/*================================*/
			$company_name = '';
			if(isset($orders[$i]->company_name)){
				$company_name = $orders[$i]->company_name;
			}

			$client_info_html = '';
			$client_info_html .= '<div id="show_client_'.$orders[$i]->id.'" style="display:none">';
			$client_info_html .= '<table width="100%" border="0" cellspacing="8" cellpadding="0" class="override">';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Name').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

			$name = '-----';
			if($orders[$i]->firstname_c){
				$name = $orders[$i]->firstname_c;
			}
			if($orders[$i]->lastname_c){
				$name .= '&nbsp;&nbsp;'.$orders[$i]->lastname_c;
			}

			$client_info_html .= $name.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Address').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

			$address ='---';
			if($orders[$i]->address_c){
				$address = $orders[$i]->address_c;
			}if($orders[$i]->housenumber_c){
				$address .= '&nbsp;&nbsp;'.$orders[$i]->housenumber_c;
			}

			$client_info_html .= $address.'<br />';

			$address2 = '---';
			if($orders[$i]->postcode_c){
				$address2 = $orders[$i]->postcode_c;
			}
			if($orders[$i]->city_c){
				$address2 .='&nbsp;&nbsp;'.$orders[$i]->city_c;
			}

			$client_info_html .= $address2.'<br />';

			$country = '---';
			if($orders[$i]->country_name){
				$country = $orders[$i]->country_name;
			}

			$client_info_html .= $country.'</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Telephone').':</strong></td>';
			$client_info_html .= '<td class="td_right">';

			$telephone = '---';
			if($orders[$i]->phone_c){
				$telephone = $orders[$i]->phone_c;
			}

			$client_info_html .= $telephone;
			$client_info_html .= '</td>';
			$client_info_html .= '</tr>';
			$client_info_html .= '<tr>';
			$client_info_html .= '<td class="td_left"><strong>'._('Email').':</strong></td>
											<td class="td_right">
			                                   <a href="mailto:'.$orders[$i]->email_c.'?subject=Re:%20BESTELLING&amp;body='._('Dear client').',"><strong>'.$orders[$i]->email_c.'</strong></a>
			                                   <br /><br />
			                                   <a href="'.base_url().'cp/clients/lijst/client_details/'.$orders[$i]->client_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
			                                </td>
										</tr>

									</table>';
			/* $client_info_html .= '	<div class="thickbox_footer">';
			$client_info_html .= '	<div class="thickbox_footer_text"><a href="'.base_url().'cp/cdashboard/send_mail?email='.$orders[$i]->email_c.'&type=hold&id='.$orders[$i]->id.'">'._('ON HOLD MESSAGE').'</a></div>';
			$client_info_html .= '	</div>'; */
			$client_info_html .= '</div>';

			$response[] = array($id,$date_created,urlencode($client_name),round($order_total,2),urlencode($pickup_content),urlencode($delivery_address_content),'','','','','',$red_dot,$company_name,(($orders[$i]->image)?1:0),$client_info_html);
		}
		echo json_encode($response);

	}

	public function get_data( $order_id = null ){
		if( $order_id != null ){
			$this->load->model('Morders');

			$order_data = $data['orderData']=$this->Morders->get_orders( $select = array(), $order_id );
			$client_id = $data['orderData']['0']->client_id;

			$this->db->where( array('orders_id'=>$order_id) );
			$this->db->join( 'products', 'products.id = order_details.products_id' );
			$order_details_data = $this->db->get( 'order_details' )->result_array();

			$order_data = (array) $order_data[0];
			$this->db->select('clients.id, clients.company_c, clients.firstname_c, clients.lastname_c, clients.address_c, clients.housenumber_c, clients.postcode_c, clients.city_c, clients.vat_c, clients.phone_c, clients.email_c, country.country_name as country_c');
			$this->db->join('country','country.id = clients.country_id','left');
			$client = $this->db->where( array('clients.id'=>$client_id) )->get( 'clients' )->row();
			// $company = $this->db->where( array('id'=>$this->company_id) )->get( 'company' )->row();
			$company = $this->db->where( array('id'=>$order_data['company_id']) )->get( 'company' )->row();

			$client_discount_number = $this->db->where( array('id'=>$client_id , 'company_id' => $order_data['company_id']) )->get( 'client_numbers' )->result();
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
			$company_gs = $this->db->where( array('company_id'=>$order_data['company_id']) )->get( 'general_settings' )->result();
			if(!empty($company_gs))
				$company_gs = $company_gs['0'];

			if( !empty($client) && !empty($company) && !empty($company_gs) )
			{
				$this->load->library('utilities');
				$this->load->helper('phpmailer');

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

				//countries details for international delivery
				$is_international = false;
				if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
					$is_international = true;
				}
				/* $countries = array();
				$is_international = false;
				if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
					$is_international = true;

					$this->db->select('id,country_name');
					$country_arr = $this->db->get('country')->result();
					foreach($country_arr as $country){
						$countries[$country->id] = $country;
					}
				}
				$mail_data['countries'] = $countries; */

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
				$mail_data['font_size'] = 12;//$font_size;
				$mail_data['is_international'] = $is_international;

				return $mail_data;
			}
		}
		return array();
	}

	function get_areazip(){

		$this->load->model('Mdelivery_settings');

		if($this->input->post('action') == 'getzip'){

			$area_id = $this->input->post('area_id');
			$cities = $this->Mdelivery_settings->get_delivery_settings(array('delivery_areas_id' => $area_id));
			if($cities != array()){
				$returned_data = json_encode(array('RESULT'=>'OK','MSG'=>$cities));
			}else{
				$returned_data = json_encode(array('RESULT'=>'ERROR','MSG'=>'no cities available'));
			}

		}else if($this->input->post('action') == 'getprice'){

			$zip_id = $this->input->post('zip_id');
			$result = $this->Mdelivery_settings->get_delivery_settings(array('id' => $zip_id));
			if($result != array()){
				$returned_data = json_encode(array('RESULT'=>'OK','MSG' => $result));
			}else
				$returned_data = json_encode(array('RESULT' => 'ERROR','MSG' => 'no zip dat available'));
		    }

		echo $returned_data;
	}


}
