<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CDashboard extends CI_Controller{

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
    $this->fdb = $this->load->database('fdb',TRUE);
	}

	function index(){

        $company = $this->Mcompany->get_company();
        $this->session->set_userdata('company',$company);
		if( !empty($company) ){
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

		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$general_settings = $this->Mgeneral_settings->get_general_settings();
			if($general_settings != array() && $general_settings[0]->hide_intro == '1'){
				$this->session->set_userdata('show_hide',true);

				if( $this->company_role == 'master' || $this->company_role == 'sub' || $this->company_role == 'super' )
				redirect("cp/orders");
				else
				redirect("cp/categories");

			}else{
				$this->session->set_userdata('show_hide',false);
				$this->intro();
			}
		}
		elseif( $this->company_role == 'sub')
		{
			$this->session->set_userdata('show_hide',true);
			redirect("cp/orders");
		}
	}


	/**
 	 * This function is used to send notification to mcp admin for changes.
 	 * @name notification
 	 * @author Monu Singh Yadav  <monuyadav@cedcoss.com>
 	 */
	function notification($product_id=0)
	{

		if($product_id!=0)
		{
			$this->M_fooddesk->get_notification($product_id);
		}
		else if($this->input->post('product_id'))
		{
			$pro_id=$this->input->post('product_id');
			$this->M_fooddesk->get_notification($pro_id);
		}

	}


	function intro(){
		$this->load->model("Mcompany_type");

	    if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
			$data['packages'] = $this->Mpackages->get_packages();
			$data['account_types'] = $this->Mpackages->get_account_types();
			$data['company'] = $company = $this->Mcompany->get_company();

			$data['company_types']=$this->Mcompany_type->get_company_type();

			$data['curr_account_type'] = array();

			if( !empty($company) )
			{
				$company = $company[0];
				$ac_type_id = $company->ac_type_id;
				$data['company_account_price'] = $this->Mcompany->get_company_account_price($ac_type_id);
				$account_type = $this->Mpackages->get_account_types( array('id'=>$ac_type_id) );
				if(!empty($account_type) && isset($account_type[0]))
				  $data['curr_account_type'] = $account_type[0];

				if($ac_type_id == 3 || $ac_type_id == 2){
					$this->load->model('Maddons');
					$data['general_settings'] = $this->Mgeneral_settings->get_general_settings(array(),'activated_addons, monthly_cost_addon, shop_testdrive');
					$data['addons'] = $this->Maddons->get_addons();
				}
			}

			$data['content'] = 'cp/intro';
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function upgrade()
	{
		if($this->input->post('upgrade_package'))
		{
		    $data_arr = array();

			$data_arr['company_id'] = $this->input->post('company_id');
			$data_arr['current_ac_type_id'] = $this->input->post('current_ac_type_id');
			$data_arr['requested_ac_type_id'] = $this->input->post('requested_ac_type_id');
			$data_arr['request_approved'] = 0;
			$data_arr['requested_on'] = date('Y-m-d H:i:s',time());

			$this->load->model('MUpgrade');
			$response = $this->MUpgrade->add_upgrade_request( $data_arr );
			if( $response ){
			  $this->messages->add(_('Your upgrade request has been forwarded successfully. Thank you for your order, we will contact you soon.'), 'success');

			  /*$this->load->model('Madmin');
			  $admin = $this->Madmin->get_admin();*/
			  $message = $this->load->view('mail_templates/'.$this->lang_u.'/upgrade_notification_to_admin',$data_arr,true);

			  send_email($this->config->item('site_admin_email'), $this->company->email ,_("OBS: Account Upgrade Request"),$message,NULL,NULL,NULL,'company','site_admin','account_upgrade_req_from_company');

			}else{
			  $this->messages->add(_('Sorry ! Some error occurred. Please try later.'), 'error');
			}

			redirect('cp/cdashboard');
		}
	}

	function packages(){

		if($this->input->post('act')=='order_package')
		{
			$this->Mpackages->order_packages();

			$company = '';
			$package = '';

			$company = $this->Mcompany->get_company(array('id'=>$this->company_id));
			$package = $this->Mpackages->get_packages(array('id'=>$this->input->post('package_id')));


			if(!empty($company) && !empty($package))
			{
			    $company = $company[0];
			    $package = $package[0];

				//$getMail = file_get_contents(base_url().'assets/mail_templates/cp_package_order_mail.html');
			    $getMail = $this->load->view('mail_templates/'.$this->lang_u.'/cp_package_order_mail',null,true);

				$parse_email_array = array();

				$parse_email_array = array( 'Message' => _('A company has ordered for a new package. Here are the details, related to this order').' :',
											'company_name' => $company->company_name,
											'first_name' => $company->first_name,
											'last_name' => $company->last_name,
											'email' => $company->email,
											'website' => $company->website,
											'address' => $company->address,
											'city' => $company->city,
											'zipcode' => $company->zipcode,
											'phone' => $company->phone,
											'username' => $company->username,
											'password' => $company->password,
											'package_name' => $package->package_name,
											'package_price' => $package->package_price,
											'package_desc' => $package->package_desc,
										  );

				$mail_body = $this->utilities->parseMailText($getMail,$parse_email_array);
				$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';

				send_email($this->config->item('site_admin_email'), $this->config->item('no_reply_email'), _('Company has ordered for a new package'), $mail_body, NULL, NULL, NULL, 'no_reply', 'site_admin', 'new_package_request_from_company');

			}

			$this->messages->add(_('Your new package order has been placed successfully. Thank you for your order, we will contact you soon.'), 'success');

			redirect('cp/cdashboard');
		}
	}

	function section_designs()
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

	    if( $this->company_role == 'master' || $this->company_role == 'super' ){

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$this->load->model('Msection_designs');

			if($this->input->post('change_designs')){

				$this->Msection_designs->add_update_section_designs();

				$this->messages->add(_('Updated: sections design Updated Successfully'), 'success');

				redirect('cp/cdashboard/section_designs');
			}

			$section_designs = $this->Msection_designs->get_section_designs();

			foreach($section_designs as $section_design){
				//echo 'login';
				if($section_design->section_id==1){//if it is a login section design (id of login section in table is '1')

					$data['login_design']=$section_design;

				}else if($section_design->section_id==2){//if it is a cart section design (id of login section in table is '2')
					//echo 'cart_design';
					$data['cart_design']=$section_design;

				}else if($section_design->section_id==3){//if it is a main section design (id of login section in table is '3')
					//echo 'main_section_design';
					$data['main_section_design']=$section_design;

				}
			}

			$general_settings = $this->Mgeneral_settings->get_general_settings();
			if($general_settings != array() && $general_settings[0]->hide_intro == '1'){
				$this->session->set_userdata('show_hide',true);
			}else{
				$this->session->set_userdata('show_hide',false);
			}
			//for the 1st form in settings.php//
			$data['general_settings']=$this->Mgeneral_settings->get_general_settings();

			$this->load->model('Mthemes');
			$data['themes'] = $this->Mthemes->get_themes();

			if($this->input->post('update'))
			{
			   $this->Mthemes->update_company_theme_css($this->company_id,$this->input->post('theme_id'));
			   $this->messages->add(_('Updated : Advanced theme settings saved successfully !'), 'success');
			}
			elseif($this->input->post('restore'))
			{
			   $this->Mthemes->restore_company_theme_css($this->company_id,$this->input->post('theme_id'));
			   $this->messages->add(_('Updated : Theme settings restored successfully !'), 'success');
			}

			$general_settings = $this->Mgeneral_settings->get_general_settings();

			if(!empty($general_settings))
			{
			   $curr_theme_id = $general_settings[0]->theme_id;
			   $data['adv_css'] = $this->Mthemes->get_company_theme_css($this->company_id,$curr_theme_id);
			}

			$data['content'] = 'cp/change_design';
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function orders($pending = '')
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
					$data['orderData'] = $this->Morders->get_orders($orders_id);
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
					$mail_body = $this->load->view('mail_templates/cancel_order_notification',$mail_data,true);
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

	function ajax_orders(){
		$this->load->model('Morders');

		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$exp_start_date = $this->input->post('start_date');
		$exp_end_date = $this->input->post('end_date');
		$orders = array();
		if($this->company_role == 'super'){
			$final_order = array();
			$sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

			foreach($sub_companies as $sub_company){

				$orders = $this->Morders->get_orders(null,$exp_start_date,$exp_end_date,$limit,$start,array(),$sub_company->company_id);

				$final_order = array_merge($final_order,$orders);
			}
			usort($final_order, create_function('$a, $b',
			'if ($a->created_date == $b->created_date) return 0; return ($a->created_date > $b->created_date) ? -1 : 1;'));
			$orders = $final_order;
		}else{
			$orders = $this->Morders->get_orders(null,$exp_start_date,$exp_end_date,$limit,$start);
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
			$order_total = (round((float)$orders[$i]->order_total,2))+(round((float)$orders[$i]->delivery_cost,2))-(float)( round($orders[$i]->disc_amount,2) + round($orders[$i]->disc_client_amount,2));

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
			$send_message = '<a href="'.base_url().'cp/cdashboard/send_mail?email='.$orders[$i]->email_c.'&type=ok&id='.$orders[$i]->id.'">'._('OK message').'</a><br>';
			}
			if($orders[$i]->completed == "1"){
			$send_message .='&nbsp;<img src="'.base_url().'assets/cp/images/'.$tickImage.'" border="0" title="message sent">';
			}else{
			$send_message .='<a href="'.base_url().'cp/cdashboard/send_mail?email='.$orders[$i]->email_c.'&type=completed&id='.$orders[$i]->id.'">'._('Complete').'</a>';
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
			                                   <a href="'.base_url().'cp/cdashboard/clients/client_details/'.$orders[$i]->clients_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
			                                </td>
										</tr>

									</table>
									<div class="thickbox_footer">
									<div class="thickbox_footer_text"><a href="'.base_url().'cp/cdashboard/send_mail?email='.$orders[$i]->email_c.'&type=hold&id='.$orders[$i]->id.'">'._('ON HOLD MESSAGE').'</a></div>

									</div>
								</div>';

			$response[] = array($id,$date_created,urlencode($client_name),$order_total,urlencode($pickup_content),urlencode($delivery_address_content),urlencode($send_message),urlencode($order_status),$class,urlencode($pay_html),$notifications,$red_dot,$company_name,(($orders[$i]->image)?1:0),$client_info_html,$orders[$i]->hidden);
		}
		echo json_encode($response);
	}

	/**
	 * Function to fetch Pending orders via ajax
	 */
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
			                                   <a href="'.base_url().'cp/cdashboard/clients/client_details/'.$orders[$i]->client_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
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

	/**
	 * Function to fetch cancelled orders via ajax
	 */
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
			                                   <a href="'.base_url().'cp/cdashboard/clients/client_details/'.$orders[$i]->client_id.'/'.$orders[$i]->company_id.'"><strong>'._('Client Details').'</strong></a>
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
				$print_body =  $this->load->view( 'mail_templates/print_order_custom_header', array(), true );
				$print_body .=  $this->load->view( 'mail_templates/print_order_custom', $print_data, true );
				$print_body .=  $this->load->view( 'mail_templates/print_order_custom_footer', array(), true );
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



			$print_body =  $this->load->view( 'mail_templates/print_order_custom_header', array(), true );
			for($i = 0; $i < count($order_ids); $i++){
				$print_data = $this->get_data( $order_ids[$i] );
				if( !empty( $print_data ) ){
					$print_body .=  $this->load->view( 'mail_templates/print_order_custom', $print_data, true );
					$print_body .= '<br class="break">';
				}
			}
			$print_body .=  $this->load->view( 'mail_templates/print_order_custom_footer', array(), true );
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

	public function get_data( $order_id = null ){
		if( $order_id != null ){
			$this->load->model('Morders');

			$order_data = $data['orderData']=$this->Morders->get_orders($order_id);
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

	/**
	 * This private function is used to fetch delivery settings
	 * @access private
	 * @param Integer $company_id Company ID
	 * @return array $delivery_settings Array of delivery areas
	 */
	private function get_delivery_settings($company_id = null){
		$delivery_settings = array();

		if($company_id && is_numeric($company_id)){

			$this->db->where('company_id',$company_id);
			$delivery_settings = $this->db->get('company_delivery_settings')->result();
		}

		return $delivery_settings;
	}

	/*=========to edit any order============*/
	function order_details_edit(){

		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender

		/*===========models================ */
		$this->load->model('Morders');
		$this->load->model('Morder_details');
		$this->load->model('Mdelivery_areas');
		$this->load->model('Mdelivery_settings');
		/*=================================*/

		if($this->uri->segment(4)=='update'){

			$order_id = $this->uri->segment(5);

			$data['orderData'] = $order_data = $this->Morders->get_orders($order_id);
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

			$this->load->model('Mproducts');
			$orders_id = $this->input->post('orderid');
			$products_id = $this->input->post('newproid');

			$product = $this->Mproducts->get_product_information($products_id);

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
					$field['default_price'] = ($product[0]->price_weight*1000);
					$field['discount'] = $product[0]->discount_wt;
					if($product[0]->discount_wt){
						if($product[0]->discount == 'multi'){
							/*$field['sub_total'] = $default_price_after_discount;
							$field['total'] = $default_price_after_discount;*/
							$field['sub_total'] = ($product[0]->price_weight*1000);
							$field['total'] = ($product[0]->price_weight*1000);
						}else{
							$field['sub_total'] = ($product[0]->price_weight*1000 - $product[0]->discount_wt);
							$field['total'] = ($product[0]->price_weight*1000 - $product[0]->discount_wt);
						}
					}else{
						$field['sub_total'] = ($product[0]->price_weight*1000);
						$field['total'] = ($product[0]->price_weight*1000);
					}
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
						/*$field['sub_total'] = $default_price_after_discount;
						$field['total'] = $default_price_after_discount;*/
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

			$isproductadded = $this->Morder_details->insert_order_details($field);
			//$isproductadded=1;
			if($isproductadded){

				$result = $this->Morders->get_orders($orders_id);

				//$order_total = (float)($result[0]->order_total+$product[0]->price);
				$order_total = (float)($result[0]->order_total + $field['total']);
				$field1['order_total'] = $this->number2db($order_total);
				$updated = $this->Morders->update_order($orders_id,$field1);
				redirect('cp/cdashboard/order_details_edit/update/'.$orders_id);

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

				$total = "";
				for($k=0;$k<count($order_details_data);$k++){
					$total += $order_details_data[$k]->total;
				}

				$fields2["order_total"] = $this->number2db($total);

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

			$order_data = $this->Morders->get_orders($order_id);

			$email = $order_data[0]->email_c;

			if($status == "y"){
				redirect("cp/cdashboard/send_mail?email=".$email."&type=completed&id=".$order_id);
			}else if($status == "n"){
				redirect("cp/cdashboard/send_mail?email=".$email."&type=hold&id=".$order_id);
			}
		}

	}

	/*===============function to be called by ajax request that updates order_details_edit form==============*/
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


				$order = $this->Morders->get_orders($_POST['id']);
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



				$order = $this->Morders->get_orders($_POST['id']);
				$order_total = $order[0]->order_total;

				$returned_data = json_encode(array('RESULT'=>'OK','order_total'=>$order_total));
			}else{
				$returned_data = json_encode(array('ERROR'=>'Error while updating.'));

			}

			echo $returned_data ;
		}
	}

	/*========function to send mail to the client about the status of the orders=========*/

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

		$order_data = $this->Morders->get_orders($order_id);

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
			$getMail = $this->load->view('mail_templates/cp_order_mail',null,true);//mail template

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
			$getMail = $this->load->view('mail_templates/pricefeature_cpOrderMail',null,true);
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
				redirect('cp/cdashboard/orders','refresh');
			}
		}else{
			$this->messages->add('Some error occured.','error');
			redirect('cp/cdashboard/orders','refresh');
		}
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

	/*======function to get delivery settings in order_details_edit.php page=======*/
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

	/*=============function to get sub categories of a category===============*/

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

	/*=========function to get products belongs to category and subcategory=========*/
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
	/*===============function to show categories================*/

	function categories($param=NULL){

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

	  	if( $this->company_role == 'master' || $this->company_role == 'super' ){
	  		if($this->input->post('btn_update')){
	  			if($this->input->post('act')=="update_categories"){
	  				$result = $this->Mcategories->change_category_order_new();
	  				$this->session->set_userdata('action', 'category_json');
	  			}
	  		}

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$pconfig['base_url'] = base_url()."cp/cdashboard/categories";
			$pconfig['total_rows'] = $data['total_cat'] = count($this->Mcategories->get_categories());
			$pconfig['per_page'] = $this->rows_per_page;
			$pconfig['uri_segment'] = 4;

			$this->pagination->initialize($pconfig);

			$data['categories'] = $this->Mcategories->get_categories('',$param,$pconfig['per_page']);
			$data['content'] = 'cp/categories';
			$data['create_links'] = $this->pagination->create_links();

			$this->load->view('cp/cp_view',$data);
	  	}
	  	else{
	       // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
	  	}
	}

	/*==========================================================*/


	/*============function to  show category edit form============*/
	function categories_addedit($action=NULL,$id=NULL){

		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

		if($this->input->post('name') && ($this->input->post('btn_update'))){

			if($query = $this->Mcategories->create_category()){

				$this->messages->add(_('New Category added successfully.'), 'success');

				$this->session->set_userdata('action', 'category_json');

				redirect('cp/cdashboard/categories');
			}
			else
			{
			    $this->messages->add(_('Sorry ! Some error occured.'), 'error');

				redirect('cp/cdashboard/categories');
			}
		}

		if($this->input->post('update')){

			$result=$this->Mcategories->update_category();

			$this->messages->add(_('Updated: Category Updated Successfully.'), 'success');

			$this->session->set_userdata('action', 'category_json');

			redirect('cp/cdashboard/categories');
		}

		if($action=="update" && $id!=''){

			$category_id = $id;

			$data['category_data']=$this->Mcategories->get_categories($category_id);
			$data['content']='cp/categories_addedit';

			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			$data['category_data']=null;//for the avoid of confliction with updation//
			$data['content'] = 'cp/categories_addedit';

			$this->load->view('cp/cp_view',$data);
		}
	}

	//-------these functions is to check whether the newly added thing alrealy exist or not---------//
	function check_category(){

		$result=$this->Mcategories->check_category();
		echo $result;

	}

	function check_subcategory(){

		$this->load->model('Msubcategories');
		$result=$this->Msubcategories->check_subcategory();
		echo $result;
	}

    //---------------------------------------------------------------------------------------------//

	function clients($action = NULL , $id = NULL, $comp_id = NULL)
	{
		if( $this->company_role == 'master' || $this->company_role == 'sub' )
		{
			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
			$data['clients']=array();
			$data['is_discount_card_activated'] = false;
			if( $action == 'client_details' )
			{
				$client_id = $id;
				$data['comp_id'] = $this->company_id;
				$data['is_set_discount_card_setting'] = false;
				$general_settings = $this->Mgeneral_settings->get_general_settings();
				if(!empty($general_settings)){
					$activated_addons = $general_settings['0']->activated_addons;
					$activated_addons = explode("#",$activated_addons);
					if(is_array($activated_addons) && !empty($activated_addons) && in_array("4",$activated_addons)){
						$data['is_discount_card_activated'] = true;
					}
					if($general_settings['0']->activate_discount_card != 0){
						$data['is_set_discount_card_setting'] = true;
					}
				}
				if($this->input->post('add_client_no'))
				{
					$where['client_id'] = $this->input->post('client_id');
					$where['company_id'] = $this->input->post('company_id');
					$update['client_number'] = $this->input->post('client_number');
				   	$updated = $this->Mclients->update_client_info($where,$update);

				   	if($updated)
				    	$this->messages->add(_('Client number updated successfully !'),'succes');
				   	else
				     	$this->messages->add(_('Some error occured while updating client number.'),'error');
				}

				if($this->input->post('add_discount_card_no'))
				{
					$where['client_id'] = $this->input->post('client_id');
					$where['company_id'] = $this->input->post('company_id');
					$update['discount_card_number'] = $this->input->post('discount_card_number');
				   	$updated = $this->Mclients->update_client_info($where,$update);

					if($updated)
						$this->messages->add(_('Discount card number updated successfully !'),'succes');
					else
						$this->messages->add(_('Some error occured while updating Discount card number.'),'error');
				}

				if($this->input->post('add_client_discount'))
				{
					$where['client_id'] = $this->input->post('client_id');
					$where['company_id'] = $this->input->post('company_id');
					$update['disc_per_client'] = $this->input->post('disc_per_client');
				   	$updated = $this->Mclients->update_client_info($where,$update);

					if($updated)
						$this->messages->add(_('Discount updated successfully !'),'succes');
					else
						$this->messages->add(_('Some error occured while updating Discount.'),'error');
				}

				$data['client_details'] = $this->Mclients->get_clients(array('id'=>$client_id));
				$data['client_number'] = $this->Mclients->get_client_number($client_id,$this->company_id);

				$data['content']='cp/client_details';

				$this->load->view('cp/cp_view',$data);
			}
			elseif( $action == 'send_mail')
			{
				/*===first we need to get the email ads ===*/
				$Company = $this->Mcompany->get_company();

				if( !empty($Company) && $Company[0]->email_ads == "1"){
					$this->load->model('Memail_messages');
					$email_messages_detail = $this->Memail_messages->get_email_messages();
					$Email_ads = $email_messages_detail[0]->emailads_text_message;
					//echo $Email_ads;
				}else{
					$Email_ads = "";
				}

				/*=====get email address of the perticular company form general settings=====*/

				$general_settings = $this->Mgeneral_settings->get_general_settings();
				/*===========================================================================*/
				if($this->input->post('all_clients')){

					$clients = $this->Mclients->get_company_clients($this->company_id,NULL,NULL,array('client_numbers.newsletter' => 'subscribe')); //$this->Mclients->get_clients();
					$this->messages->clear();

					if( !empty($clients) )
					foreach($clients as $client){
						$email_c = $client->email_c;
						$sender = $general_settings[0]->emailid;
						$subject = $this->input->post('subject');
						$fname = $client->firstname_c;
						$lname = $client->lastname_c;
						$message = $this->input->post('message');
						$verify_code = $client->verify_code;
						$CompanyName = $Company[0]->company_name;
						$flag = $this->send_client_mail($email_c,$sender,$subject,$fname,$lname,$message,$verify_code,$Email_ads,$CompanyName);
						if(array_key_exists('success',$flag)){
							$this->messages->add(_('Mail has been sent successfully to').' - '.$email_c,'success');
						}else{
							$this->messages->add(_('Error !! Mail couldn\'t be sent.'),'error');
						}
					}
				}else{
					$email_c = $this->input->post('client_id');
					$this->messages->clear();
					$client = $this->Mclients->get_clients(array('email_c'=>$email_c));

					if( !empty($client) ) foreach($client as $c) {

						$sender = $general_settings[0]->emailid;
						$subject = $this->input->post('subject');
						$fname = $c->firstname_c;
						$lname = $c->lastname_c;
						$message = $this->input->post('message');
						$verify_code = $c->verify_code;
						$CompanyName = $Company[0]->company_name;

						$flag = $this->send_client_mail($email_c,$sender,$subject,$fname,$lname,$message,$verify_code,$Email_ads,$CompanyName);

						if(array_key_exists('success',$flag)){
							$this->messages->add(_('Mail has been sent successfully.'),'success');
						}else{
							$this->messages->add(_('Error!! Mail couldn\'t be sent.'),'error');
						}

						break;
					}
				}

				redirect('cp/cdashboard/clients');
			}
			elseif( $action == 'search_client' )
			{
					if($this->input->post('search_by')=='Name')
					{
						$data['clients']=$this->Mclients->get_company_clients($this->company_id,'','','(firstname_c = "'.$this->input->post('search_keyword').'" OR lastname_c = "'.$this->input->post('search_keyword').'")');
					}
					elseif($this->input->post('search_by')=='Email')
					{
						$data['clients']=$this->Mclients->get_company_clients($this->company_id,'','',array( 'email_c LIKE' => '%'.$this->input->post('search_keyword').'%' ));
					}

				$data['content'] = 'cp/clients';
				$this->load->view('cp/cp_view',$data);

			}
			elseif( $action == 'downlaod_customer_info' )
			{
				$this->downlaod_customer_info();
			}
			elseif( $action == 'add' )
			{
				//$this->load->helper(array('form'));

				if($this->input->post('add_client')){
					$this->form_validation->set_rules('firstname_c', 'firstname_c', 'required');
					$this->form_validation->set_rules('lastname_c', 'lastname_c', 'required');
					$this->form_validation->set_rules('address_c', 'address_c', 'required');
					$this->form_validation->set_rules('housenumber_c', 'housenumber_c', 'required');
					$this->form_validation->set_rules('postcode_c', 'postcode_c', 'required');
					$this->form_validation->set_rules('city_c', 'city_c', 'required');
					$this->form_validation->set_rules('country_id', 'country_id', 'required|is_natural_no_zero');
					$this->form_validation->set_rules('phone_c', 'phone_c', 'required');
					$this->form_validation->set_rules('email_c', 'email_c', 'required|valid_email|callback_is_unique_email');
					$this->form_validation->set_rules('password_c', 'password_c', 'required');
					$this->form_validation->set_rules('conf_password_c', 'conf_password_c', 'required|matches[password_c]');

					if ($this->form_validation->run() == FALSE)
					{

					}
					else
					{
						$result = array();
						$insert_array = array(
								'company_id' => $this->company_id,
								'firstname_c' => $this->input->post('firstname_c'),
								'lastname_c' => $this->input->post('lastname_c'),
								'company_c' => $this->input->post('company_c'),
								'address_c' => $this->input->post('address_c'),
								'housenumber_c' => $this->input->post('housenumber_c'),
								'postcode_c' => $this->input->post('postcode_c'),
								'city_c' => $this->input->post('city_c'),
								'country_id' => $this->input->post('country_id'),
								'phone_c' => $this->input->post('phone_c'),
								'mobile_c' => $this->input->post('mobile_c'),
								'fax_c' => $this->input->post('fax_c'),
								'email_c' => $this->input->post('email_c'),
								'password_c' => $this->input->post('password_c'),
								'notifications' => ( ($this->input->post('notifications'))?'subscribe':'unsubscribe' ),
								'company_c' => $this->input->post('company_c'),
								'vat_c' => $this->input->post('vat_c'),
								'created_c' => date("Y-m-d H:i:s"),
								'updated_c' => date("Y-m-d H:i:s"),
								'verified' => "1"
						);

						$client_id = $this->Mclients->add($insert_array);
						if($client_id){
							$insert_array = array(
								'company_id' => $this->company_id,
								'client_id' => $client_id,
								'discount_card_number' => $this->input->post('discount_card_number'),
								'newsletter' => ( ($this->input->post('newsletter'))?'subscribe':'unsubscribe'),
								'client_number' => $this->input->post('client_number'),
								'disc_per_client' => $this->input->post('disc_per_client'),
								'associated' => "1"
							);
							$this->Mclients->add_clients_number($insert_array);

							$this->session->set_flashdata('success',_('Client Info added successfully'));
						}else{
							$this->session->set_flashdata('error',_('Client Info did not added successfully'));
						}
						//$this->session->set_flashdata('success',_('Client Info added successfully'));
						redirect(base_url().'cp/cdashboard/clients/add');
					}

				}

				$data['is_set_discount_card_setting'] = false;
				$general_settings = $this->Mgeneral_settings->get_general_settings();
				if(!empty($general_settings)){
					$activated_addons = $general_settings['0']->activated_addons;
					$activated_addons = explode("#",$activated_addons);
					if(is_array($activated_addons) && !empty($activated_addons) && in_array("4",$activated_addons)){
						$data['is_discount_card_activated'] = true;
					}
					if($general_settings['0']->activate_discount_card != 0){
						$data['is_set_discount_card_setting'] = true;
					}
				}

				// Getting delivery settings
				$this->load->model('Mdelivery_areas');
				$delivery_settings = $this->Mdelivery_areas->get_delivery_area_settings();

				if(!empty($delivery_settings)){
					if($delivery_settings[0]['type'] == 'international'){
						$this->load->model('Mcountry');
						$data['countries']=$this->Mcountry->get_country(null, 'international');
					}else{
						$this->load->model('Mcountry');
						$data['countries']=$this->Mcountry->get_country();
					}
				}else{
					$this->load->model('Mcountry');
					$data['countries']=$this->Mcountry->get_country();
				}

				$data['content'] = 'cp/client_add';
				$this->load->view('cp/cp_view',$data);

			}else{

				$data['clients']=$this->Mclients->get_company_clients($this->company_id);
				$data['content'] = 'cp/clients';
				$this->load->view('cp/cp_view',$data);
			}
		}
		elseif( $this->company_role == 'super' )
		{

			if( $action == 'client_details' )
			{
				$client_id = $id;
				$data['comp_id'] = $comp_id;

				if($this->input->post('add_client_no'))
				{
				   $updated = $this->Mclients->update_client_number();

				   if($updated)
				     $this->messages->add(_('Client number updated successfully !'),'success');
				   else
				     $this->messages->add(_('Some error occured while updating client number.'),'error');
				}

				$data['client_details'] = $this->Mclients->get_clients(array('id'=>$client_id));
				$data['client_number'] = $this->Mclients->get_client_number($client_id , $comp_id);

				$data['content']='cp/client_details';

				$this->load->view('cp/cp_view',$data);
			}
			else
			{
				$data['clients'] = array();
				$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed(); //for calender

				$data['clients'][ 0 ] = $this->Mclients->get_company_clients( $this->company_id );

				$data['sub_companies'] = $sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

				if( !empty( $sub_companies ))
				{
					foreach( $sub_companies as $sc )
					{
						$clients =  $this->Mclients->get_company_clients( $sc->company_id );
						$data['clients'][ $sc->company_name ] = $clients;
					}
				}

				$data['content'] = 'cp/clients';
				$this->load->view('cp/cp_view',$data);
			}

		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function is_unique_email($email){
		$info = $this->db->get_where('clients', array('email_c' => $email))->result();
		if(!empty($info)){
			$this->form_validation->set_message('is_unique_email', _('Email field is not valid') );
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function downlaod_customer_info()
	{
		$clients = $this->Mclients->get_company_clients($this->company_id);

		if( !empty( $clients ) )
		{
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Customer Info') );

			$counter = 1;

			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('ID') )->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Company') )->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Firstname') )->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('Lastname') )->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('Address') )->getStyle('E'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('House Number') )->getStyle('F'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('Postal Code') )->getStyle('G'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('City') )->getStyle('H'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('I'.$counter, _('Country') )->getStyle('I'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('J'.$counter, _('Phone No') )->getStyle('J'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('K'.$counter, _('Mobile No') )->getStyle('K'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('L'.$counter, _('Fax') )->getStyle('L'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('M'.$counter, _('Email') )->getStyle('M'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('N'.$counter, _('Vat') )->getStyle('N'.$counter)->getFont()->setBold(true);

			foreach( $clients as $c )
			{
				$counter++;

				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $c->id );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, $c->company_c );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, $c->firstname_c );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $c->lastname_c );
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, $c->address_c );
				$this->excel->getActiveSheet()->setCellValue('F'.$counter, $c->housenumber_c );
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $c->postcode_c );
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, $c->city_c );
				$this->excel->getActiveSheet()->setCellValue('I'.$counter, $c->country_id );
				$this->excel->getActiveSheet()->setCellValue('J'.$counter, $c->phone_c );
				$this->excel->getActiveSheet()->setCellValue('K'.$counter, $c->mobile_c );
				$this->excel->getActiveSheet()->setCellValue('L'.$counter, $c->fax_c );
				$this->excel->getActiveSheet()->setCellValue('M'.$counter, $c->email_c );
				$this->excel->getActiveSheet()->setCellValue('N'.$counter, $c->vat );
			}

			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);

			$datestamp = date("d-m-Y");
			$filename = "Clients-Info-".$datestamp.".xls";

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function sub_admins()
	{
	    if( $this->company_role == 'super' )
		{
		   if($this->input->post('act') == 'edit_access_code')
		   {
		      $result = $this->Mgeneral_settings->update_general_settings();
		   }

	  	   $data['sub_companies'] = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

		   $companies = $this->Mcompany->get_company( array( 'id' => $this->company_id ) );

		   if(!empty($companies))
		     $data['super_admin_login_code'] = $companies[0]->access_super;
		   else
		     $data['super_admin_login_code'] = '';

		   $data['content'] = 'cp/sub_admins';
		   $this->load->view('cp/cp_view',$data);
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}

	}

	function send_client_mail($email,$sender,$subject,$fname,$lname,$message,$verify_code,$Email_ads,$CompanyName)
	{
		$parse_email_array = array( "subject"=>$subject,
									"first_name"=>$fname,
									"last_name"=>$lname,
									"message"=>$message,
									"verifyurl"=>base_url('').'cp/login/unsubscribe/',
									"verifycode"=>$verify_code,
									"EmailAds"=>$Email_ads,
									"CompanyName"=>$CompanyName
							      );
		$getMail = $this->load->view('mail_templates/'.$this->lang_u.'/client_mail',null,true);
		$mail_body = $this->utilities->parseMailText($getMail,$parse_email_array);

		if($mail_body==NULL){
			return array('error' =>'Could not parse the mail template');
		}
		$flag = send_email($email, $sender, $subject, $mail_body, $CompanyName, NULL, NULL, 'company', 'client', 'message');
		if($flag){
			return array('success' => 'Mail has been sent successfully.');
		}else{
			return array('error' => 'Mail couldn\'t send successfully.');
		}
	}

	function delete_clients(){

		$this->Mclients->delete_clients();
		return 'successfully  deleted';
	}

	function remove_client(){

		$this->Mclients->remove_client();
		return 'successfully  deleted';
	}

	function settings()
	{
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

		/*
		*after submitting setting if intro hide has been changed then this will set the session variable accordingly
		*and intro setion will be shown accordingly
		*/

		//--these loc  are for the form which gets submitted in settings.php--//
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

				redirect('cp/cdashboard/settings');


			}else if($this->input->post('act')=="group_edit"){

				$this->load->model('Mgroups');
				$result = $this->Mgroups->update_groups();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',2));

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="group_person_edit"){

				$this->load->model('Mgroups');
				$result = $this->Mgroups->update_groups();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',2));

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="group_wt_edit"){

				$this->load->model('Mgroups');
				$result = $this->Mgroups->update_groups();

				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',2));

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="edit_pickup_settings"){

				$this->load->model('Morder_settings');
				$update_order_settings=array(
						'same_day_orders_pickup'=>$this->input->post('same_day_orders_pickup'),
						'allowed_days_pickup'=> ( ($this->input->post('same_day_orders_pickup'))?implode(",",$this->input->post('allowed_days_pickup')):'' ),
						'time_diff_pickup'=>$this->input->post('time_diff_pickup'),
						'same_day_time_pickup'=>$this->input->post('same_day_time_pickup'),
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
				$result = $this->Morder_settings->update_order_settings($update_order_settings);

				$this->load->model('Mpickup_delivery_timings');
				$this->Mpickup_delivery_timings->update_pickup_delivery_timings();

				if(array_key_exists('success',$result)){
					$this->messages->add(_('pickup settings has been updated successfully.'),'success');
				}else{
					$this->messages->add(_('error occured while updating pickup settings!'),'error');
				}

				$this->session->set_userdata(array('show_hide',3));

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="edit_delivery_settings"){

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

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="edit_mail_messages"){

				$result = $this->Mgeneral_settings->update_general_settings();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',5));

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="edit_holiday_settings"){

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

				redirect('cp/cdashboard/settings');

			}else if($this->input->post('act')=="edit_theme_settings"){

				$result = $this->Mgeneral_settings->update_theme();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}
				redirect('cp/cdashboard/section_designs');

			}
			else if( $this->input->post('act') == "update_payment_settings")
			{
				// Updating Paypal Settings
				$result = $this->Mgeneral_settings->update_general_settings();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				// Updating Cardgate Settings
				$this->load->model('Mpayment');
				$update_array = array(
						'cardgate_payment' => $this->input->post('cardgate_payment'),
						'minimum_amount_cardgate' => $this->input->post('minimum_amount_cardgate'),
						'c_apply_tax' => $this->input->post('c_apply_tax'),
						'c_tax_percentage' => $this->input->post('c_tax_percentage'),
						'c_tax_amount' => $this->input->post('c_tax_amount')
				);
				$this->Mpayment->update_cardgate_settings(array('company_id' => $this->company_id), $update_array);

				// Updating Advance Payment Settings
				$this->load->model('Morder_settings');
				$update_arr = array('adv_payment' => $this->input->post('adv_payment'));
				$this->Morder_settings->update_order_settings($update_arr);

				$this->session->set_userdata(array('show_hide',8));
				redirect('cp/cdashboard/settings');
			}
			elseif( $this->input->post('act') == "edit_labeler_settings")
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

				redirect('cp/cdashboard/settings');*/

				$result = $this->Mgeneral_settings->update_labeler_settings();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',12));

				redirect('cp/cdashboard/settings');
			}
			elseif( $this->input->post('act') == "edit_other_settings")
			{
				$result = $this->Mgeneral_settings->update_general_settings();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',9));

				redirect('cp/cdashboard/settings');
			}elseif( $this->input->post('act') == "edit_faq_settings")
			{
				$result = $this->Mgeneral_settings->update_general_settings();
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',10));

				redirect('cp/cdashboard/settings');
			}elseif( $this->input->post('act') == "edit_tnc_settings")
			{
				$tnc = $this->input->post('tnc_txt');
				$result = $this->Mgeneral_settings->update_tnc_settings($tnc);
				if(array_key_exists('success',$result)){
					$this->messages->add($result['success'],'success');
				}else{
					$this->messages->add($result['error'],'error');
				}

				$this->session->set_userdata(array('show_hide',11));

				redirect('cp/cdashboard/settings');
			}
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
		$data['holiday_timings']=$this->Morder_settings->get_holiday_timings();

		// This is for the cardgate settings at PAYMENT SETTINGS tab
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
		redirect('cp/cdashboard/settings/areadetails_addedit/'.$area_id);
	}


	function delete_delivery_areas(){
		$this->load->model('Mdelivery_areas');
		$this->Mdelivery_areas->delete_delivery_areas();
		echo 'deleted successfully';
	}

	function delete_delivery_settings(){
		$this->load->model('Mdelivery_settings');
		$this->Mdelivery_settings->delete_delivery_settings();
		echo 'deleted successfully';
	}

	//----------------for company table-------------------//

	function changepassword()
	{

		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
			if($this->input->post('btn_submit')){
				$data['msg']=$this->Mcompany->update_company();
				if($data['msg']){
					$data['content']='cp/changepassword';
					$this->load->view('cp/cp_view',$data);
				}else{
					redirect('cp/cdashboard/changepassword');
				}
			}else{
				$data['msg']="";
				$data['content'] = 'cp/changepassword';
				$this->load->view('cp/cp_view',$data);

			}
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function change_category_order(){

		$affected_rows=$this->Mcategories->change_category_order();
		if($affected_rows=='1'){
			echo 'Status successfully updated';
		}else{
			echo "Error occurred while updating status";
		}
	}
	function change_category_service_type(){
		$this->Mcategories->change_category_service_type();
		echo 'successfully updated';
	}
	function delete_category(){
		$this->Mcategories->delete_category();
		$this->session->set_userdata('action', 'category_json');
		echo 'successfully deleted';
	}
	function update_status(){
		if($this->input->post('method')=='category'){
			$this->Mcategories->update_status();
			echo 'OK';
		}else if($this->input->post('method')=='subcategory'){
			$this->load->model('Msubcategories');
			$this->Msubcategories->update_status();
			echo 'OK';
		}
	}
	function update_tool_tip(){
		if($this->input->post('type')=="categories"){
			$display=$this->Mcategories->update_tool_tip();
			if($display=='yes'){
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'1'));
			}else{
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'0'));
			}
		}else if($this->input->post('type')=="subcategories"){
			$this->load->model('Msubcategories');
			$display=$this->Msubcategories->update_tool_tip();
			if($display=='yes'){
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'1'));
			}else{
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'0'));
			}
		}
	}

	//--this is for products--//

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

	function products()
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->company_role == 'master' || $this->company_role == 'super' )
	    {
			$this->load->model('Mproducts');

			if($this->input->post('save'))
			{
				$ids = $this->input->post('ids');
			  	if( is_array($ids) && !empty($ids) )
			  	{
			    	foreach($ids as $key => $val)
				 	{
				 		$update_data = array();
				 		$pro_num = $val['pro_art_no'];
				 		if(strlen(trim($pro_num) < 8)){
				 			$pro_num = str_pad(trim($pro_num),8,"0",STR_PAD_LEFT);
				 		}

				 		$update_data['pro_art_num']=$pro_num;
				 		$update_data['proname']=$val['pro_name'];
				 		$update_data['prodescription']=$val['pro_desc'];

				 		$price_per_unit = 0;
				 		if ($val['pro_price_per_unit'] != "")
				 		{
				 			$price_per_unit = $this->number2db($val['pro_price_per_unit']);
				 		}
				 		$update_data['price_per_unit']=$price_per_unit;

				 		$price_weight = 0;
				 		if(isset($val['pro_weight']))
				 		{
					 		if ($val['pro_weight'] != "")
					 		{
					 			$price_weight = $this->number2db($val['pro_weight']);
							  	$price_weight = $price_weight/1000;
	 						  	$price_weight = $this->number2db($price_weight);
					 		}
				 		}
				 		$update_data['price_weight']=$price_weight;

				 		$price_per_person = 0;
				 		if ($val['pro_price_per_person'] != "")
				 		{
				 			$price_per_person = $this->number2db($val['pro_price_per_person']);
				 		}
				 		$update_data['price_per_person']=$price_per_person;
// 					    $update_data = array();
// 						$update_data['pro_art_num'] = $this->input->post('pro_art_num_'.$product_id);
// 						$update_data['proname'] = addslashes($this->input->post('proname_'.$product_id));
// 						$update_data['prodescription'] = addslashes($this->input->post('prodescription_'.$product_id));

// 						$price_per_unit = 0;
// 						$price_per_unit = @$this->input->post('price_per_unit_'.$product_id);
// 						if($price_per_unit)
// 							$price_per_unit = $this->number2db($price_per_unit);
// 						$update_data['price_per_unit'] = $price_per_unit;

// 						$price_weight = 0;
// 						$price_weight = @$this->input->post('price_weight_'.$product_id);
// 						if($price_weight)
// 						{
// 							$price_weight = $this->number2db($price_weight);
// 						  	$price_weight = $price_weight/1000;
// 						  	$price_weight = $this->number2db($price_weight);
// 						}
// 						$update_data['price_weight'] = $price_weight;
// 						$update_data['pro_display'] = ($key + 1);

						$this->Mproducts->update_product_details($val['pro_id'],$update_data);
						$update_data=array();
				 	}
				 	$this->messages->add(_('Products updated successfully.'), 'success');
			  	}
			  	$product_id_list = $this->input->post('product_id_list');
			  	if( is_array($product_id_list) && !empty($product_id_list) )
			  	{

			  		foreach($product_id_list as $key => $val)
			  		{
			  			$update_data['pro_display']=$key+1;
			  			$this->Mproducts->update_product_details($val,$update_data);
			  		}
			  	}
			}

			$data['products']=array();
			$data['cat_id']=NULL;
			$data['sub_cat_id']=NULL;
			$data['links']=NULL;

			$data['total_products'] = 0;

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$current_url=$this->uri->uri_string();
			$url_variable=$this->uri->uri_to_assoc(4);
			/*$this->load->library('pagination');
			if($url_variable){
				if(array_key_exists('subcategory_id', $url_variable) && array_key_exists('page', $url_variable)){
					$pieces = explode("page", $current_url);
					$pconfig['base_url'] = base_url().$pieces[0]."page/";
					$pconfig['uri_segment'] =9;//gives offset
					$pconfig['total_rows']= $data['total_products'] = count($this->Mproducts->get_products($url_variable['category_id'],$url_variable['subcategory_id']));

				}else if(array_key_exists('category_id', $url_variable) && array_key_exists('page', $url_variable)){
					$pieces = explode("page", $current_url);
					$pconfig['base_url'] = base_url().$pieces[0]."page/";
					$pconfig['uri_segment'] =7;
					$pconfig['total_rows'] = $data['total_products'] = count($this->Mproducts->get_products($url_variable['category_id']));
				}
			}*/

			$data['category_data'] = $this->Mcategories->get_categories();

			if(array_key_exists('category_id', $url_variable)){

				//----for the pagnation---//
				/*$pconfig['first_link'] = _('First');
				$pconfig['last_link'] = _('Last');
				$pconfig['next_link'] = _('Next');
				$pconfig['prev_link'] = _('Previous');
				$pconfig['per_page'] = $this->rows_per_page;
				$this->pagination->initialize($pconfig);
				$data['links'] = $this->pagination->create_links();*/

				//-----------------------//
				$data['cat_id']=$url_variable['category_id'];
				$this->load->model('Msubcategories');
				$data['subcategory_data']=$this->Msubcategories->get_sub_category($url_variable['category_id']);
				$products = array();

				if(array_key_exists('subcategory_id', $url_variable)){
					$sub_cat_id=$url_variable['subcategory_id'];
					$data['sub_cat_id']=$url_variable['subcategory_id'];
					$data['products']= $products = $this->Mproducts->get_products($url_variable['category_id'],$sub_cat_id);//getting product of the subcategory
				}else{

					$data['sub_cat_id']='-1';
					$data['products']= $products = $this->Mproducts->get_products($url_variable['category_id'],'-1');//getting product of related to category
				}
				if(!empty($products)){
					$products = $this->join_pdf_name_from_fdd($products);
					$products = $this->is_contains_semi($products);
				}

				$data['products'] = $products;
			}

			if(array_key_exists('filtered_product', $url_variable)){
				$filter_keyword=$url_variable['filtered_product'];
				$this->load->model('Mproducts');
				$data['products']=$products=$this->Mproducts->get_product_by_keyword($filter_keyword);
			}

			$data['general_settings'] = $this->Mgeneral_settings->get_general_settings();

			$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
			$this->db->where($where);
			$this->db->where(array('company_id'=>$this->company_id,'categories_id'=>0));
			$data['no_cat'] = $this->db->count_all_results('products');

			$company = $this->Mcompany->get_company();
			$data['ac_type_id']=$company[0]->ac_type_id;
			$data['trail_date']=$company[0]->trial;
			$data['on_trial']=$company[0]->on_trial;
			$type_id=$company[0];
			$company_type_id=$type_id->type_id;
			$result = $this->Mcompany->get_co_type($company_type_id);
			$type_name=$result['company_type_name'];
			$data['type_slug'] =str_replace(" ","+",$type_name);

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();

			$this->load->model('mnotify');
			$data['notifications'] = $this->mnotify->get_notifications(NULL, date('Y-m-d'));
			$data['closed_noti'] = $this->mnotify->get_closed_noti($this->company->id);

			$company_slug=$company[0];
			$data['company_slug']=$company_slug->company_slug;

			$data['shared_products'] = $this->Mproducts->get_shared_products_list();
			$data['content'] = 'cp/products';

			$this->load->view('cp/cp_view',$data);
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function report_export(){
		$this->db->select('*');
		$this->db->where(array('company_id' => $this->company_id));
		$download_clicked_products = $this->db->get('report_export_download')->result();
        $data['download_links'] = $download_clicked_products;
		$data['content'] = 'cp/report_export';
		$company = $this->Mcompany->get_company();
		$data['show_recipe']= $company[0]->show_recipe;

		$response=$this->load->view('cp/cp_view',$data);
		/*$data['content'] = 'cp/clients_view';
 		$response=$this->load->view('cp/cp_view',$data);*/
	}

	function video_tutorial(){
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();
		$data['content'] = 'cp/video_view';
		$response=$this->load->view('cp/cp_view',$data);
	}

	function semi_products(){
		$this->load->model('Mproducts');
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			if($this->input->post('save'))
			{
				$ids = $this->input->post('ids');

				if( is_array($ids) && !empty($ids) )
				{
					foreach($ids as $key => $product_id)
					{
						$update_data = array();
						$update_data['proname'] = addslashes($this->input->post('proname_'.$product_id));
						$update_data['prodescription'] = addslashes($this->input->post('prodescription_'.$product_id));

						$update_data['pro_display'] = ($key + 1);
						$this->Mproducts->update_product_details($product_id,$update_data);
					}

					$this->messages->add(_('Products updated successfully.'), 'success');
					$this->session->set_userdata('action', 'category_json');
				}
			}

			$data['products']= $this->Mproducts->get_semi_products();
 			$data['links']=NULL;

			$data['total_products'] = 0;

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$current_url=$this->uri->uri_string();
			$url_variable=$this->uri->uri_to_assoc(4);

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();

			$data['content'] = 'cp/semi_products';
			$data['page_id']=1;

			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			// restricted
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}

	function semi_products_extra(){

		$this->load->model('Mproducts');
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			if($this->input->post('save'))
			{
				$ids = $this->input->post('ids');

				if( is_array($ids) && !empty($ids) )
				{
					foreach($ids as $key => $product_id)
					{
						$update_data = array();
						$update_data['proname'] = addslashes($this->input->post('proname_'.$product_id));
						$update_data['prodescription'] = addslashes($this->input->post('prodescription_'.$product_id));

						$update_data['pro_display'] = ($key + 1);
						$this->Mproducts->update_product_details($product_id,$update_data);
					}

					$this->messages->add(_('Products updated successfully.'), 'success');
				}

			}

			$data['products']= $this->Mproducts->get_semi_products_extra();
			$data['links']=NULL;

			$data['total_products'] = 0;

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$current_url=$this->uri->uri_string();
			$url_variable=$this->uri->uri_to_assoc(4);

			$data['page_id']=2;

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();
			$data['content'] = 'cp/semi_products';
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			// restricted
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}
	function change_product_sequence()
	{
		$this->load->model('Mproducts');
		$this->Mproducts->change_product_sequence();
		echo 'successfully updated';
	}

	function delete_product(){
		if ($this->input->post('remove_product_arr')){
			$remove_product_arr=$this->input->post('remove_product_arr');
			if (!empty($remove_product_arr)){
				$this->load->model('Mproducts');
				foreach ($remove_product_arr as $product_id_key=>$product_id_val){
					$this->delete_child_parent_prod($product_id_val);
					$data = $this->Mproducts->delete_product($product_id_val);
					echo $data;
				}
			}
		}
		else {
			$this->load->model('Mproducts');
			$this->delete_child_parent_prod($this->input->post('id'));
			$data = $this->Mproducts->delete_product();
			echo $data;
		}
		$this->session->set_userdata('action', 'category_json');
	}

	function delete_child_parent_prod($id){

		$child_p = $this->Mproducts->check_prod_shared_stat($id);
		if(!empty($child_p)){
			foreach ($child_p as $ch_p){
				$this->Mproducts->delete_product($ch_p['id']);
			}
		}
		$this->db->select('parent_proid, company_id');
		$parent_p = $this->db->get_where('products',array('id'=> $id,'parent_proid !='=> 0))->result();
		if(!empty($parent_p)){
			$this->db->where(array('proid'=>$parent_p[0]->parent_proid,'to_comp_id'=>$parent_p[0]->company_id));
			$this->db->update('products_shared',array('status'=> '0'));
		}
	}

	function products_addedit(){
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

		$this->load->model('Msubcategories');
		$this->load->model('Mproducts');

		if($this->session->userdata('menu_type') == 'fooddesk_light'){
			$data['company'] = $company = $this->Mcompany->get_company();
			$company = $company[0];
			$ac_type_id = $company->ac_type_id;
			$data['company_account_price'] = $company = $this->Mcompany->get_company_account_price($ac_type_id);
			$account_type = $this->Mpackages->get_account_types( array('id'=>$ac_type_id) );
			if(!empty($account_type) && isset($account_type[0]))
				$data['curr_account_type'] = $account_type[0];
			$data['account_types'] = $this->Mpackages->get_account_types();
		}

		// Load these things only if Ingredients system is not enabled
		if($this->company->k_assoc && $this->company->ingredient_system){

		}else{
			$this->load->model('Mgroups');
			$this->load->model('Mproduct_discount');
			$this->load->model('Mgroups_products');
			$this->load->model('Mpickup_delivery_timings');

			$data['general_settings'] = $general_settings = $this->Mgeneral_settings->get_general_settings();

			//for product availability
			$pdt = array();
			$pickup_delivery_timings=$this->Mpickup_delivery_timings->get_pickup_delivery_timings(array('company_id'=>$this->company_id));
			if(!empty($pickup_delivery_timings))
			foreach($pickup_delivery_timings as $pd)
			if($pd->pickup1 == 'CLOSED' || $pd->delivery1 == 'CLOSED')
				$pdt[] = $pd->day_id;

			$data['pickup_delivery_timings']=$pdt;

			$products_per_group=array();
			$products_per_group_wt=array();
			$products_per_group_person = array();
		}
		$data['category_data']=$this->Mcategories->get_categories();
		$data['subcategory_data']=$this->Msubcategories->get_sub_category();

		if($this->uri->segment(4)=='product_id'){
			$data['product_id']=$this->uri->segment(5);

			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));
			$data['check_prod_share']=$this->Mproducts->check_prod_shared_stat($this->uri->segment(5));

			if(empty($data['product_information'])){
				redirect(('cp/cdashboard/products'));
			}

			// Fetching Subcat info
			$product_cat_id=$data['product_information'][0]->categories_id;
			$data['subcategory_data']=$this->Msubcategories->get_sub_category($product_cat_id);

			if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium' || $this->session->userdata('menu_type') == 'fooddesk_light'){
				$recipe_wt = $data['product_information'][0]->recipe_weight;

				if($recipe_wt != 0){
					$recipe_wt = $recipe_wt*1000;
				}else{
					$recipe_wt = 100;
				}

				$data['producers']=$this->M_fooddesk->get_supplier_name();
				$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
				$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));
				$nutri_values = array();
				if (!empty($has_fdd_quant)){
					$nutri_values['e_val_1'] = 0;
					$nutri_values['e_val_2'] = 0;
					$nutri_values['protiens'] = 0;
					$nutri_values['carbo'] = 0;
					$nutri_values['sugar'] = 0;
					$nutri_values['poly'] = 0;
					$nutri_values['farina'] = 0;
					$nutri_values['fats'] = 0;
					$nutri_values['sat_fats'] = 0;
					$nutri_values['single_fats'] = 0;
					$nutri_values['multi_fats'] = 0;
					$nutri_values['salt'] = 0;
					$nutri_values['fibers'] = 0;

					foreach ($has_fdd_quant as $has_fdd_qu){
						$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
						if( !empty( $fdd_pro_info ) ){
							$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
							$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
							$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
							$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
							$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
							$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
							$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
							$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
							$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
							$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
							//$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
							$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
							$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
						}
					}

					$data['nutri_values'] = $nutri_values;

					$fibers = ($nutri_values['fibers'] != 0)?". vezels ".defined_money_format($nutri_values['fibers'],1)."g":"";
					//$data['nutri_values_dist'] = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. waarvan - suikers ".defined_money_format($nutri_values['sugar'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
					$data['nutri_values_dist'] = "Voedingswaarden gem. per 100gr: energie: ".defined_money_format($nutri_values['e_val_2'],0)."kj/ ".defined_money_format($nutri_values['e_val_1'],0)."kcal. vetten ".defined_money_format($nutri_values['fats'],1)."g waarvan - verzadigde vetzuren ".defined_money_format($nutri_values['sat_fats'],1)."g. koolhydraten ".defined_money_format($nutri_values['carbo'],1)."g. eiwitten ".defined_money_format($nutri_values['protiens'],1)."g. zout ".defined_money_format($nutri_values['salt'],1)."g".$fibers;
				}

				if(!empty($data['product_information'])){
					if($data['product_information'][0]->direct_kcp_id != 0){
						$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
						if(!empty($fixed_pdf)){
							$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
						}
					}
					elseif($data['product_information'][0]->direct_kcp == 1 && $data['product_information'][0]->parent_proid == 0){
						$this->db->select('fdd_pro_id');
						$fixed_comp_pro = $this->db->get_where('fdd_pro_quantity',array('obs_pro_id'=>$data['product_information'][0]->id,'is_obs_product'=>0))->result_array();
						if(!empty($fixed_comp_pro)){
							$fixed_pdf = $this->M_fooddesk->fixed_pdf($fixed_comp_pro[0]['fdd_pro_id']);
							if(!empty($fixed_pdf)){
								$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
							}
						}
					}
				}

				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);

				// ----------------------------------------------
				$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
				$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
				$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
				if(!empty($ing_array)){
					$pro_ing =
					array(
							'id' => 1,
							'p_id' => $data['product_information'][0]->changed_fixed_product_id,
							'i_id' => 0,
							'ing_name_dch' =>  $ing_pro_name[0]['p_name']
					);

					$pro_ing = (object) $pro_ing;
					$pro_ing1[] = $pro_ing;
					$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
				}

				$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

				$data['fdd_credits'] = $this->Mproducts->fdd_credits();

				//ingredients, allergence, traces, nutrition values to copy
				$this->fdb->select('all_id,all_name_dch');
				$aller_arr = $this->fdb->get('allergence')->result_array();
				$product_ingredients_dist = $this->Mproducts->get_product_ingredients_dist($this->uri->segment(5));
				$product_ingredients_vetten_dist = $this->Mproducts->get_product_ingredients_vetten_dist($this->uri->segment(5));
				$product_additives_dist = $this->Mproducts->get_product_additives_dist($this->uri->segment(5));

				$product_allergences = $this->Mproducts->get_product_allergence_dist($this->uri->segment(5));
				$product_sub_allergences = $this->Mproducts->get_product_sub_allergence_dist($this->uri->segment(5));

				$all_id_arr = array();
				if(!empty($product_allergences)){
					foreach ($product_allergences as $aller){
						$all_id_arr[] = $aller->ka_id;
					}
				}

				$allergence_words = array();
				foreach ($aller_arr as $val){
					if(in_array($val['all_id'],$all_id_arr))
						$allergence_words[strtolower($val['all_name_dch'])] = $val['all_id'];
				}

				$ing = '';
				foreach ($product_ingredients_dist as $ingredients){

					if($ingredients->ki_name == ')' ){
						$ing = substr($ing, 0, -2);
						$ing .= ' ';
					}
					if($ingredients->ki_name == '(' ){
						$ing = substr($ing, 0, -2);
						$ing .= ' ';
					}

					if($ingredients->ki_id != 0){
						if($ingredients->prefix == ''){
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
						}else{
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words, $ingredients->prefix).'('.$ingredients->prefix.')'.', ';
						}
					}else if($ingredients->ki_name == ')'){

						$ing .= $ingredients->ki_name.', ';

					}else if($ingredients->ki_name == '('){

						$ing .= $ingredients->ki_name.' ';

					}else{
						if($ingredients->prefix == ''){
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words);
						}else{
							$ing .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words, $ingredients->prefix).'('.$ingredients->prefix.')'.', ';
						}
					}
				}

				$ing = substr($ing, 0, -2);

				$ing_end = "";
				if(!empty($product_ingredients_vetten_dist)){
					$ing_end .= "Plantaardige vetstof (";
					foreach ($product_ingredients_vetten_dist as $vetten){
						$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words);
					}
					$ing_end = rtrim(trim($ing_end),",");
					$ing_end .= ")";
				}

				if(!empty($product_additives_dist)){
					$additive_arr = array();
					foreach ($product_additives_dist as $add){
						if(!in_array($add->add_name,$additive_arr)){
							$additive_arr[] = $add->add_name;
						}
					}

					for($i = 0; $i < count($additive_arr); $i++){
						if($ing_end != ""){
							$ing_end .= ", ";
						}
						if($additive_arr[$i] != "others")
							$ing_end .= stripslashes($additive_arr[$i]);

						$count = 0;
						$add_ing = "";
						foreach ($product_additives_dist as $add){
							if(($add->add_name == $additive_arr[$i]) && ($add->ki_name != "")){
								$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words);
								$count = $count+1;
							}
						}
						$add_ing = rtrim(trim($add_ing),",");
						if($count == 1){
							if($additive_arr[$i] == "others"){
								$ing_end .= " ".$add_ing;
							}else{
								$ing_end .= ": ".$add_ing;
							}
						}
						elseif ($count >1 ){
							if($additive_arr[$i] == "others"){
								$ing_end .= $add_ing;
							}else{
								$ing_end .= ": ".$add_ing."";
							}
						}
					}
				}
				if($ing_end != ""){
					$ing_end = ", ".$ing_end;
				}

				$ing .= $ing_end;

				$ing = str_replace('<b>', '', str_replace('</b>', '', $ing));
				$data['product_ingredients_dist'] = $ing;

				$all = '';
				if(!empty($product_allergences)){
					foreach ($product_allergences as $allergence){
						$all .= $allergence->ka_name;

						if(($allergence->ka_id == 1) || ($allergence->ka_id == 8)){
							$a1 = '';
							if(!empty($product_sub_allergences)){
								$a1 .= ' (';
								foreach ($product_sub_allergences as $sub_allergence){
									if($sub_allergence->parent_ka_id == $allergence->ka_id){
										$a1 .=  $sub_allergence->sub_ka_name.', ';
									}
								}
								$a1 = rtrim($a1,', ');
								$a1 .= ')';
								$a1 = str_replace('()', '', $a1);
							}
							$all .= $a1;
						}
						$all .=  ', ';
					}
					if(strpos($all,'Melk') !== false && strpos($all,'Lactose') !== false){
						$all = str_replace('Melk', 'Melk (incl. lactose)', $all);
						$all = str_replace('Lactose, ', '', $all);
					}
					$all = substr($all, 0, -2);
				}

				$data['product_allergences_dist'] = $all;

				$product_traces_dist = $this->Mproducts->get_product_traces($this->uri->segment(5));
				$str_arr = array();
				foreach($product_traces_dist as $trace ){
					if(!in_array($trace->kt_name, $str_arr)){
						$str_arr[] = $trace->kt_name;
					}
				}
				$str = '';
				for($t = 0; $t < count($str_arr); $t++){
					$str .= stripslashes($str_arr[$t]).', ';
				}

				$data['product_traces_dist'] = rtrim($str,', ');
			}

			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5));
			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
			$add_ing = array();
			if(!empty($product_additives)){
				$additive_arr = array();
				foreach ($product_additives as $add){
					if(!in_array($add->add_name,$additive_arr)){
						$additive_arr[] = $add->add_name;
					}
				}

				for($i = 0; $i < count($additive_arr); $i++){
					$add_ing[] = array(
							'add_id' => 0,
							'kp_id' => 0,
							'ki_id' => 0,
							'ki_name'=> $additive_arr[$i]
					);

					foreach ($product_additives as $add){
						if($add->add_name == $additive_arr[$i]){
							$add_ing[] = array(
									'add_id' => $add->add_id,
									'kp_id' => $add->kp_id,
									'ki_id' => $add->ki_id,
									'ki_name'=> $add->ki_name
							);
						}
					}
				}
			}
			$data['product_additives'] = $add_ing;

			if($this->session->userdata('login_via') == 'mcp'){
				$data['add_ing'] = $product_additives;
			}

			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5));
			$data['product_allergences']=$this->Mproducts->get_product_allergence($this->uri->segment(5));
			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5));

			//Load label data
			$data['product_labeler']=$this->Mproducts->get_product_labeler($this->uri->segment(5));

			// Load seperate product add/edit view file if Ingredients system is enabled
			if($this->company->ingredient_system){
				$data['content'] = 'cp/products_addedit_ing';
			}
			else{
				$data['groups']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>0));
				$data['groups_person']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>2));
				$data['groups_wt']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>1));

				$data['product_discount']=$this->Mproduct_discount->get_product_discount($this->uri->segment(5),0);
				$data['product_discount_wt']=$this->Mproduct_discount->get_product_discount($this->uri->segment(5),1);
				$data['product_discount_person']=$this->Mproduct_discount->get_product_discount($this->uri->segment(5),2);

				//--this is load the groups for the particular product--//
				$data['groups_products']=$this->Mgroups_products->get_groups_products(array('products_id'=>$this->uri->segment(5),'type'=>0));
				$data['groups_products_wt']=$this->Mgroups_products->get_groups_products(array('products_id'=>$this->uri->segment(5),'type'=>1));
				$data['groups_products_person']=$this->Mgroups_products->get_groups_products(array('products_id'=>$this->uri->segment(5),'type'=>2));

				foreach($data['groups_products'] as $groups){
					$products_per_group[$groups->groups_id][]=array('attribute_name'=>$groups->attribute_name,'attribute_value'=>$groups->attribute_value,'multiselect'=>$groups->multiselect,'required'=>$groups->required);
				}
				$data['products_per_group']=$products_per_group;

				foreach($data['groups_products_person'] as $groups){
					$products_per_group_person[$groups->groups_id][]=array('attribute_name'=>$groups->attribute_name,'attribute_value'=>$groups->attribute_value,'multiselect'=>$groups->multiselect,'required'=>$groups->required);
				}
				$data['products_per_group_person']=$products_per_group_person;

				foreach($data['groups_products_wt'] as $groups){
					$products_per_group_wt[$groups->groups_id][]=array('attribute_name'=>$groups->attribute_name,'attribute_value'=>$groups->attribute_value,'multiselect'=>$groups->multiselect,'required'=>$groups->required);
				}
				$data['products_per_group_wt']=$products_per_group_wt;
				//-------------------------------------------------------//

				//--this is to check whether to show payment option--//

				// Fetching Advanced Payment
				$this->load->model('Morder_settings');
				$adv_payment = 0;
				$adv_payment = $this->Morder_settings->get_order_settings( array(), 'adv_payment' );
				if(!empty($adv_payment))
					$adv_payment = $adv_payment[0]->adv_payment;

				// Fetching Cargate payment enable or not
				$this->load->model('Mpayment');
				$cardgate_payment = 0;
				$cardgate_payment = $this->Mpayment->get_cardgate_setting( array('company_id' => $this->company_id), 'cardgate_payment' );
				if(!empty($cardgate_payment))
					$cardgate_payment = $cardgate_payment[0]->cardgate_payment;

				if($this->company_role == 'master' && !$adv_payment && ($general_settings['0']->online_payment == 1 || $cardgate_payment == 1)) {
					$data['show_payment_setting'] = 'true';

				}elseif($this->company_role == 'super'){

					$params = array('parent_id' => $this->company_id,'role' => 'sub' );
					$payment_option = $this->Mcompany->payment_option($params);
					if($payment_option){
						$data['show_payment_setting'] = 'true';
					}else{
						$data['show_payment_setting'] = 'false';
					}
				}

				$rel_prod_arr = explode('#', $data['product_information'][0]->related_products);

				if(!empty($rel_prod_arr)){
					foreach ($rel_prod_arr as $rel){
						$this->db->select('id,proname');
						$rel_result = $this->db->get_where('products',array('id'=>$rel))->row();
						if(!empty($rel_result)){
							$rel_prod[] = $rel_result;
						}
					}
				}
				$data['rel_prod'] = (isset($rel_prod))?$rel_prod:array();
				//-------------------------------------------------------//
				if ($this->session->userdata('menu_type') == 'fooddesk_light'){
					$data['count_left'] = 5 - $this->company->recipe_calculate;
					$data['content'] = 'cp/products_addedit_light';
				}
				else {
					$data['content'] = 'cp/products_addedit';
				}
			}
			$data['admin_mail'] = $this->company->email;

			$this->load->view('cp/cp_view',$data);
		}
		else if($this->uri->segment(4)=='add'){
			$this->session->set_userdata('action', 'category_json');
			$this->fdb->select('all_id,all_name_dch');
			$data['allergence'] = $aller_arr = $this->fdb->get('allergence')->result_array();
			$data['product_information']=array();

			if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
				$data['producers']=$this->M_fooddesk->get_supplier_name();
				$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
			}

			//this subcategories are corressponding to the category posted by the form product_add in products.php//
			if($this->input->post('categories_id'))
				$data['subcategory_data'] = $this->Msubcategories->get_sub_category($this->input->post('categories_id'));
			else
				$data['subcategory_data'] = array();

			// Load seperate product add/edit view file if Ingredients system is enabled
			if($this->company->ingredient_system){
				$data['content'] = 'cp/products_addedit_ing';
			}else{
				$data['groups']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>0));
				$data['groups_wt']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>1));
				$data['groups_person']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>2));
				$data['groups_products']=NULL;//this variable is used for the updation purpose so it has to set null//
				$data['groups_products_wt']=NULL;
				$data['groups_products_person']=NULL;

				//--this is to check whether to show payment option--//

				if($this->company_role == 'master' && $general_settings['0']->online_payment == 1){
					$data['show_payment_setting'] = 'true';
					//$data['company_role'] = 'master';
				}elseif($this->company_role == 'super'){
					//$data['company_role'] = 'super';
					$params = array('parent_id' => $this->company_id,'role' => 'sub' );
					$payment_option = $this->Mcompany->payment_option($params);
					if($payment_option){
						$data['show_payment_setting'] = 'true';
					}else{
						$data['show_payment_setting'] = 'false';
					}
				}
				//-------------------------------------------------------//

				if ($this->session->userdata('menu_type') == 'fooddesk_light'){
					$data['content'] = 'cp/products_addedit_light';
				}
				else {
					$data['content'] = 'cp/products_addedit';
				}
			}

			$data['fdd_credits'] = $this->Mproducts->fdd_credits();

			$data['admin_mail'] = $this->company->email;

			$this->load->view('cp/cp_view',$data);
		}

		if($this->input->post('add_update') == 'update'){
			if($this->company->ingredient_system){
				$show = $this->Mproducts->update_product_ing();
			}else{
				$show = $this->Mproducts->update_product();
				$this->Mgroups_products->add_update_groups_products();
			}

			$this->session->set_userdata('action', 'category_json');

			if($this->input->post('semi_products') == 1){
				redirect(('cp/cdashboard/semi_products'));
			}
			else if($this->input->post('semi_products') == 2){
				redirect(('cp/cdashboard/semi_products_extra'));
			}
			redirect(('cp/cdashboard/products/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}

		if($this->input->post('add_update') == 'add'){
			if($this->company->ingredient_system){
				$show = $this->Mproducts->add_product_ing();
			}else{
				$show = $this->Mproducts->add_product();
			}

			$this->session->set_userdata('action', 'category_json');

			if($this->input->post('semi_products') == 1){
				redirect(('cp/cdashboard/semi_products'));
			}
			elseif ($this->input->post('semi_products') == 2){
				redirect(('cp/cdashboard/semi_products_extra'));
			}
			redirect(('cp/cdashboard/products/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}

		if($this->input->post('ajax_add_update') == 'update'){
			if($this->input->post('action') == 'product_info'){
				$show = $this->Mproducts->update_product_info();
			}
			if($this->input->post('action') == 'recipe'){
				$show = $this->Mproducts->update_recipe();
			}
			if($this->input->post('action') == 'labeler'){
				$show = $this->Mproducts->update_labeler();
			}
			if($this->input->post('action') == 'webshop'){
				$show = $this->Mproducts->update_webshop();

				if($this->input->post('action_val') == _('Save & next')){
					$result = $this->Mproducts->get_next_product($show);
					if(!empty($result)){
						$show = $result->id;
					}
					else{
						$this->session->set_flashdata('webshop','webshop_success');
					}
				}
				else{
					$this->session->set_flashdata('webshop','webshop_success');
				}

				$this->session->set_userdata('action', 'category_json');

				redirect('cp/cdashboard/products_addedit/product_id/'.$show);
			}

			if($this->input->post('action') == 'allergence'){
				$show = $this->Mproducts->update_product_allergence();
			}

			if($this->input->post('action_val') == _('Save & next')){
				$result = $this->Mproducts->get_next_product($show);

				if(!empty($result)){
					$response = array('id'=>$result->id,'is_next'=>'true');
				}
				else{
					$response = array('id'=>$show,'is_next'=>'false');
				}
			}
			else{
				$response = array('id'=>$show,'is_next'=>'false');
			}

			echo json_encode($response);die;
			//redirect(('cp/cdashboard/products/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}

		if($this->input->post('ajax_add_update') == 'add'){
			if($this->input->post('action') == 'product_info'){
				$show = $this->Mproducts->add_product_info();
			}
			if($this->input->post('action') == 'recipe'){
				$show = $this->Mproducts->add_recipe();
			}
			if($this->input->post('action') == 'labeler'){
				$show = $this->Mproducts->add_labeler();
			}
			if($this->input->post('action') == 'webshop'){
				$show = $this->Mproducts->add_webshop();

				if($this->input->post('action_val') == _('Save & next')){
					$result = $this->Mproducts->get_next_product($show);
					if(!empty($result)){
						$show = $result->id;
					}
					else{
						$this->session->set_flashdata('webshop','webshop_success');
					}
				}
				else{
					$this->session->set_flashdata('webshop','webshop_success');
				}

				$this->session->set_userdata('action', 'category_json');

				redirect('cp/cdashboard/products_addedit/product_id/'.$show);
			}

			if($this->input->post('action') == 'allergence'){
				$show = $this->Mproducts->add_product_allergence();
			}

			if($this->input->post('action_val') == _('Save & next')){
				$result = $this->Mproducts->get_next_product($show);

				if(!empty($result)){
					$response = array('id'=>$result->id,'is_next'=>'true');
				}
				else{
					$response = array('id'=>$show,'is_next'=>'false');
				}
			}
			else{
				$response = array('id'=>$show,'is_next'=>'false');
			}

			echo json_encode($response);die;
			//redirect(('cp/cdashboard/products/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
		}
	}

// 	function semi_product_addedit(){
// 		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found' || ($this->company->ac_type_id != 4 && $this->company->ac_type_id != 5 && $this->company->ac_type_id != 6) ){
// 			redirect(base_url().'cp/cdashboard/page_not_found');
// 		}
// 		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

// 		$this->load->model('Mproducts');
// 		if($this->uri->segment(4)=='product_id'){
// 			$data['product_id']=$this->uri->segment(5);

// 			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));
// 			$recipe_wt = $data['product_information'][0]->recipe_weight;

// 			if($recipe_wt != 0){
// 				$recipe_wt = $recipe_wt*1000;
// 			}else{
// 				$recipe_wt = 100;
// 			}

// 			$data['producers']=$this->M_fooddesk->get_supplier_name();
// 			$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
// 			
// 			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));

// 			$nutri_values = array();
// 			if (!empty($has_fdd_quant)){
// 				$nutri_values['e_val_1'] = 0;
// 				$nutri_values['e_val_2'] = 0;
// 				$nutri_values['protiens'] = 0;
// 				$nutri_values['carbo'] = 0;
// 				$nutri_values['sugar'] = 0;
// 				$nutri_values['poly'] = 0;
// 				$nutri_values['farina'] = 0;
// 				$nutri_values['fats'] = 0;
// 				$nutri_values['sat_fats'] = 0;
// 				$nutri_values['single_fats'] = 0;
// 				$nutri_values['multi_fats'] = 0;
// 				$nutri_values['salt'] = 0;
// 				$nutri_values['fibers'] = 0;

// 				foreach ($has_fdd_quant as $has_fdd_qu){
// 					
// 					$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
// 					
// 					$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
// 					$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
// 					$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
// 					$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
// 					$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
// 					$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
// 					$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
// 					$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
// 					$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
// 					$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
// 					$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
// 					$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
// 					$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
// 				}

// 				$data['nutri_values'] = $nutri_values;

// 				if(!empty($data['product_information']) && $data['product_information'][0]->direct_kcp_id != 0){

// 					$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
// 					if(!empty($fixed_pdf)){
// 						$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
// 					}
// 				}
// 				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
// 			}

// 			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5),$this->company->k_assoc);

// 			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
// 			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
// 			$add_ing = array();
// 			if(!empty($product_additives)){
// 				$additive_arr = array();
// 				foreach ($product_additives as $add){
// 					if(!in_array($add->add_name,$additive_arr)){
// 						$additive_arr[] = $add->add_name;
// 					}
// 				}
// 				$add_ing = array();
// 				for($i = 0; $i < count($additive_arr); $i++){
// 					$add_ing[] = array(
// 							'add_id' => 0,
// 							'kp_id' => 0,
// 							'ki_id' => 0,
// 							'ki_name'=> $additive_arr[$i]
// 					);

// 					foreach ($product_additives as $add){
// 						if($add->add_name == $additive_arr[$i]){
// 							$add_ing[] = array(
// 									'add_id' => $add->add_id,
// 									'kp_id' => $add->kp_id,
// 									'ki_id' => $add->ki_id,
// 									'ki_name'=> $add->ki_name
// 							);
// 						}
// 					}
// 				}
// 			}
// 			$data['product_additives'] = $add_ing;

// 			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5),$this->company->k_assoc);
// 			$data['product_allergences']=$this->Mproducts->get_product_allergence($this->uri->segment(5),$this->company->k_assoc);
// 			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5),$this->company->k_assoc);
// 			$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
// 			
// 			$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
// 			$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
// 			
// 			if(!empty($ing_array)){
// 				$pro_ing =
// 				array(
// 						'id' => 1,
// 						'p_id' => $data['product_information'][0]->changed_fixed_product_id,
// 						'i_id' => 0,
// 						'ing_name_dch' =>  $ing_pro_name[0]['p_name']
// 				);

// 				$pro_ing = (object) $pro_ing;
// 				$pro_ing1[] = $pro_ing;
// 				$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
// 			}

// 			$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

// 		}
// 		else if($this->uri->segment(4)=='add'){
// 			$data['page_id']=$this->uri->segment(5);
// 			$data['product_information']=array();
// 		}

// 		$data['content'] = 'cp/semi_products_addedit';
// 		$data['fdd_credits'] = $this->Mproducts->fdd_credits();
// 		$this->load->view('cp/cp_view',$data);
// 	}

	function semi_product_addedit(){
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found' || ($this->company->ac_type_id != 4 && $this->company->ac_type_id != 5 && $this->company->ac_type_id != 6) ){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

		$this->load->model('Mproducts');
		if($this->uri->segment(4)=='product_id'){
			$data['product_id']=$this->uri->segment(5);
			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));

			$recipe_wt = $data['product_information'][0]->recipe_weight;

			if($recipe_wt != 0){
				$recipe_wt = $recipe_wt*1000;
			}else{
				$recipe_wt = 100;
			}

			$data['producers']=$this->M_fooddesk->get_supplier_name();
			$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));

			$nutri_values = array();
			if (!empty($has_fdd_quant)){
				$nutri_values['e_val_1'] = 0;
				$nutri_values['e_val_2'] = 0;
				$nutri_values['protiens'] = 0;
				$nutri_values['carbo'] = 0;
				$nutri_values['sugar'] = 0;
				$nutri_values['poly'] = 0;
				$nutri_values['farina'] = 0;
				$nutri_values['fats'] = 0;
				$nutri_values['sat_fats'] = 0;
				$nutri_values['single_fats'] = 0;
				$nutri_values['multi_fats'] = 0;
				$nutri_values['salt'] = 0;
				$nutri_values['fibers'] = 0;

				foreach ($has_fdd_quant as $has_fdd_qu){
					$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
					if( !empty( $fdd_pro_info ) ){
						$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
						$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
						$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
						$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
						$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
						$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
						$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
						$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
						$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
						$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
						$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
						$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
						$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
					}
				}

				$data['nutri_values'] = $nutri_values;

				if(!empty($data['product_information']) && $data['product_information'][0]->direct_kcp_id != 0){

					$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
					if(!empty($fixed_pdf)){
						$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
					}
				}
				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
			}

			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5),$this->company->k_assoc);

			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
			$add_ing = array();
			if(!empty($product_additives)){
				$additive_arr = array();
				foreach ($product_additives as $add){
					if(!in_array($add->add_name,$additive_arr)){
						$additive_arr[] = $add->add_name;
					}
				}
				$add_ing = array();
				for($i = 0; $i < count($additive_arr); $i++){
					$add_ing[] = array(
							'add_id' => 0,
							'kp_id' => 0,
							'ki_id' => 0,
							'ki_name'=> $additive_arr[$i]
					);

					foreach ($product_additives as $add){
						if($add->add_name == $additive_arr[$i]){
							$add_ing[] = array(
									'add_id' => $add->add_id,
									'kp_id' => $add->kp_id,
									'ki_id' => $add->ki_id,
									'ki_name'=> $add->ki_name
							);
						}
					}
				}
			}
			$data['product_additives'] = $add_ing;

			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5),$this->company->k_assoc);
			$data['product_allergences']=$this->Mproducts->get_product_allergence($this->uri->segment(5),$this->company->k_assoc);
			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5),$this->company->k_assoc);
			$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
			
			$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
			$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
			
			if(!empty($ing_array)){
				$pro_ing =
				array(
						'id' => 1,
						'p_id' => $data['product_information'][0]->changed_fixed_product_id,
						'i_id' => 0,
						'ing_name_dch' =>  $ing_pro_name[0]['p_name']
				);

				$pro_ing = (object) $pro_ing;
				$pro_ing1[] = $pro_ing;
				$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
			}

			$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

		}

		else if($this->uri->segment(4)=='add'){
			$data['page_id']=$this->uri->segment(5);
			$data['product_information']=array();
		}
		$data['content'] = 'cp/semi_products_addedit';
		$data['fdd_credits'] = $this->Mproducts->fdd_credits();
		$this->load->view('cp/cp_view',$data);
	}

	function semi_product_addedit_new(){

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found' || ($this->company->ac_type_id != 4 && $this->company->ac_type_id != 5 && $this->company->ac_type_id != 6) ){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

		$this->load->model('Mproducts');
		if($this->uri->segment(4)=='product_id'){
			$data['product_id']=$this->uri->segment(5);
			$data['page_id']=$this->uri->segment(6);
			$data['product_information']=$this->Mproducts->get_product_information($this->uri->segment(5));

			$recipe_wt = $data['product_information'][0]->recipe_weight;

			if($recipe_wt != 0){
				$recipe_wt = $recipe_wt*1000;
			}else{
				$recipe_wt = 100;
			}

			$data['producers']=$this->M_fooddesk->get_supplier_name();
			$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
			
			$has_fdd_quant = $this->M_fooddesk->get_fdd_quant($this->uri->segment(5));

			$nutri_values = array();
			if (!empty($has_fdd_quant)){
				$nutri_values['e_val_1'] = 0;
				$nutri_values['e_val_2'] = 0;
				$nutri_values['protiens'] = 0;
				$nutri_values['carbo'] = 0;
				$nutri_values['sugar'] = 0;
				$nutri_values['poly'] = 0;
				$nutri_values['farina'] = 0;
				$nutri_values['fats'] = 0;
				$nutri_values['sat_fats'] = 0;
				$nutri_values['single_fats'] = 0;
				$nutri_values['multi_fats'] = 0;
				$nutri_values['salt'] = 0;
				$nutri_values['fibers'] = 0;

				foreach ($has_fdd_quant as $has_fdd_qu){
					
					$fdd_pro_info = $this->M_fooddesk->get_fdd_prod_details($has_fdd_qu['fdd_pro_id']);
					
					if( !empty( $fdd_pro_info ) ){
						$nutri_values['e_val_1'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_1'])*(1/$recipe_wt);
						$nutri_values['e_val_2'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['e_val_2'])*(1/$recipe_wt);
						$nutri_values['protiens'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['proteins'])*(1/$recipe_wt);
						$nutri_values['carbo'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['carbohydrates'])*(1/$recipe_wt);
						$nutri_values['sugar'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['sugar'])*(1/$recipe_wt);
						$nutri_values['poly'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['polyolen'])*(1/$recipe_wt);
						$nutri_values['farina'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['farina'])*(1/$recipe_wt);
						$nutri_values['fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fats'])*(1/$recipe_wt);
						$nutri_values['sat_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['saturated_fats'])*(1/$recipe_wt);
						$nutri_values['single_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['single_unsaturated_fats'])*(1/$recipe_wt);
						$nutri_values['multi_fats'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['multi_unsaturated_fats'])*(1/$recipe_wt);
						$nutri_values['salt'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['salt'])*(1/$recipe_wt);
						$nutri_values['fibers'] += ($has_fdd_qu['quantity']*$fdd_pro_info[0]['fibers'])*(1/$recipe_wt);
					}
				}

				$data['nutri_values'] = $nutri_values;

				if(!empty($data['product_information']) && $data['product_information'][0]->direct_kcp_id != 0){

					$fixed_pdf = $this->M_fooddesk->fixed_pdf($data['product_information'][0]->direct_kcp_id);
					if(!empty($fixed_pdf)){
						$data['fixed_pdf'] = $fixed_pdf[0]['data_sheet'];
					}
				}
				// $data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
			}

			$data['product_ingredients']=$this->Mproducts->get_product_ingredients($this->uri->segment(5),$this->company->k_assoc);
			$data['product_ingredients_vetten']=$this->Mproducts->get_product_ingredients_vetten($this->uri->segment(5));
			$product_additives =$this->Mproducts->get_product_addtives($this->uri->segment(5));
			if(!empty($product_additives)){
				$additive_arr = array();
				foreach ($product_additives as $add){
					if(!in_array($add->add_name,$additive_arr)){
						$additive_arr[] = $add->add_name;
					}
				}
				$add_ing = array();
				for($i = 0; $i < count($additive_arr); $i++){
					$add_ing[] = array(
							'add_id' => 0,
							'kp_id' => 0,
							'ki_id' => 0,
							'ki_name'=> $additive_arr[$i]
					);

					foreach ($product_additives as $add){
						if($add->add_name == $additive_arr[$i]){
							$add_ing[] = array(
									'add_id' => $add->add_id,
									'kp_id' => $add->kp_id,
									'ki_id' => $add->ki_id,
									'ki_name'=> $add->ki_name
							);
						}
					}
				}
				$data['product_additives'] = $add_ing;
			}



			$data['product_traces']=$this->Mproducts->get_product_traces($this->uri->segment(5),$this->company->k_assoc);
			$data['product_allergences']=$this->Mproducts->get_product_allergence($this->uri->segment(5),$this->company->k_assoc);
			$data['product_sub_allergences']=$this->Mproducts->get_product_sub_allergence($this->uri->segment(5),$this->company->k_assoc);

			$data['used_fdd_pro_info'] = $this->M_fooddesk->used_fdd_pro_info($data['product_id']);
			
			$ing_array = $this->M_fooddesk->getIngredients(array('p_id'=>$data['product_information'][0]->changed_fixed_product_id));
			$ing_pro_name = $this->M_fooddesk->get_fdd_pro_details($data['product_information'][0]->changed_fixed_product_id);
			
			if(!empty($ing_array)){
				$pro_ing =
				array(
						'id' => 1,
						'p_id' => $data['product_information'][0]->changed_fixed_product_id,
						'i_id' => 0,
						'ing_name_dch' =>  $ing_pro_name[0]['p_name']
				);

				$pro_ing = (object) $pro_ing;
				$pro_ing1[] = $pro_ing;
				$data['used_fdd_pro_ing'] = array_merge($pro_ing1,$ing_array);
			}

			$data['used_own_pro_info'] = $this->M_fooddesk->used_own_pro_info($data['product_id']);

		}
		else if($this->uri->segment(4)=='add'){
			$data['page_id']=$this->uri->segment(5);
			$data['product_information']=array();
		}

		$data['content'] = 'cp/semi_products_addedit_new';
		$data['fdd_credits'] = $this->Mproducts->fdd_credits();
		$this->load->view('cp/cp_view',$data);

	}

	function product_clone($clone_of_id = null, $semi_product = 0){
		if($clone_of_id){
			$this->load->model('Mproducts');
			$show = $this->Mproducts->add_product_clone($clone_of_id);

			$this->session->set_userdata('action', 'category_json');

			if($semi_product == 1){
				redirect('cp/cdashboard/semi_products/');
			}
			elseif($semi_product == 2){
				redirect('cp/cdashboard/semi_products_extra/');
			}
			else{
				redirect(('cp/cdashboard/products/category_id/'.$show['categories_id'].'/subcategory_id/'.$show['subcategories_id'].'/page/'));
			}
		}else{
			echo "This is not the valid product id";
		}
	}

	function update_product_status()
	{
		$this->load->model('Mproducts');
		$this->Mproducts->update_product_status();
		echo 'OK';
	}

	function update_checkbox()
	{
		$this->load->model('Mproducts');
		$this->Mproducts->update_checkbox();
	}

	function get_sub_category()
	{
		$this->load->model('Msubcategories');
		$subcategories=$this->Msubcategories->get_sub_category($this->input->post('cat_id'));
		echo json_encode($subcategories);
	}

	function subcategories($category = NULL, $category_id=NULL, $param = NULL)
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

	    if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$this->load->model('Msubcategories');

			$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender
			$data['category_data'] = $this->Mcategories->get_categories();
			$data['subcategory_data'] = NULL;
			$data['cat_id']='';

			if($this->input->post('update')){
				$subcat_ids = $this->input->post('sub_cat_ids');
				if(is_array($subcat_ids) && !empty($subcat_ids)){
					foreach ($subcat_ids as $key => $subcat_id)
						$this->Msubcategories->update_sub_cat(array('suborder_display' => ($key+1)), $subcat_id);

					$this->session->set_userdata('action', 'category_json');
				}
			}

			if( $category == "category_id" )
			{
				$data['cat_id'] = $category_id;
				/*$pconfig['base_url'] = base_url()."cp/cdashboard/subcategories/category_id/".$category_id;

				$pconfig['total_rows'] = $data['total_sub_cat'] = count($this->Msubcategories->get_sub_category($category_id));
				$pconfig['per_page'] = $this->rows_per_page;
				$pconfig['uri_segment'] = 6;
				//echo $data['total_sub_cat'];
				$this->pagination->initialize($pconfig);*/
				$data['subcategory_data'] = $this->Msubcategories->get_sub_category($category_id,'',$param);
				//$data['links'] = $this->pagination->create_links();
				$data['links'] = NULL;
				$data['content'] = 'cp/subcategories';
				$this->load->view('cp/cp_view',$data);
			}
			else
			{
				$data['links'] = NULL;
				$data['content'] = 'cp/subcategories';
				$this->load->view('cp/cp_view',$data);
			}
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function subcategories_addedit( $action = NULL , $id = NULL)
	{
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
		$this->load->model('Msubcategories');

		if( $this->input->post('submit') == 'ADD' || $this->input->post('submit') == 'TOEVOEGEN' )
		{
			$returndata=$this->Msubcategories->add_subcategory();
			$this->messages->add(_('New Subcategory added successfully.'), 'success');

			$this->session->set_userdata('action', 'category_json');

			redirect('cp/cdashboard/subcategories/category_id/'.$returndata->categories_id);
		}

		if( $this->input->post('submit') == 'UPDATE' )
		{
			$returndata=$this->Msubcategories->update_subcategory();
			$this->messages->add(_('Subcategory updated successfully.'), 'success');

			$this->session->set_userdata('action', 'category_json');

			redirect('cp/cdashboard/subcategories/category_id/'.$returndata->categories_id);
		}

		if( $action == 'add' ){

			$data['category_data']=$this->Mcategories->get_categories();
			$data['subcategory_data'] = NULL;
			$data['content'] = 'cp/subcategories_addedit';

			$this->load->view('cp/cp_view',$data);
		}

		if( $action == 'update' && $id != '' )
		{
			$subcat_id = $id;
			$data['subcat_id']=$subcat_id;
			$data['category_data']=$this->Mcategories->get_categories();
			$data['subcategory_data']=$this->Msubcategories->get_sub_category('',$subcat_id);
			$data['content']='cp/subcategories_addedit';

			$this->load->view('cp/cp_view',$data);
		}
	}

	function delete_subcategory()
	{
		$this->load->model('Msubcategories');
		$returndata=$this->Msubcategories->delete_subcategory();

		$this->session->set_userdata('action', 'category_json');
		echo $returndata;
	}

	function change_subcategory_order(){
		$this->load->model('Msubcategories');
		$affected_rows=$this->Msubcategories->change_subcategory_order();
		if($affected_rows){
			echo 'successfully updated';
		}else{
			echo 'error in  updation';
		}

	}

    //--------------------------these functions are for sidebar--------------------------------------//
	function news(){
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
		$data['content']="cp/news";
		$this->load->view('cp/cp_view',$data);

	}

	function version(){
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
		$data['content']="cp/version";
		$this->load->view('cp/cp_view',$data);

	}

	function dwnld_labels(){
		$data['content'] = 'cp/dwnld_labels';
		$response=$this->load->view('cp/cp_view',$data);
	}

	function profile()
	{
		$data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed();//for calender

		if($this->input->post('act') == "upd_domain"){
			$this->db->where('company_id',$this->company_id);
			$this->db->update('api',array('domain'=>$this->input->post('domain')));
			redirect('cp/cdashboard/profile','refresh');
		}
		elseif($this->input->post('act') == "update_profile"){
			$this->load->Model('Mcompany');
			$affected_rows = $this->Mcompany->update_company_profile();
			$this->messages->clear();
			if($affected_rows){
				$this->messages->add(_('Profile has been updated successfully'),'success');
			}else{
				$this->messages->add(_('error!!couldn\'t update profile'),'error');
			}
			redirect('cp/cdashboard/profile','refresh');
		}
		elseif($this->input->post('act') == "send_files"){
			if($this->input->post("email_to_send") != ""){

				$Company = $this->Mcompany->get_company();

				$toEmail = $this->input->post("email_to_send");
				$fromEmail = $Company['0']->email;
				if($Company[0]->email_ads == "1"){
					$this->load->model('Memail_messages');
					$email_messages_detail = $this->Memail_messages->get_email_messages();
					$Options3 = $email_messages_detail[0]->emailads_text_message;
				}else{
					$Options3 = "";
				}
					/* Edited CARL */
				$this->load->model('Mapi');
				$api = $this->Mapi->get_api();

				$mail_data['api_id'] = $api['0']->api_id;
				$mail_data['api_secret'] = $api['0']->api_secret;
				$mail_data['Options3'] = $Options3;
				$mail_body = $this->load->view('mail_templates/'.$this->lang_u.'/send_api_files',$mail_data,true);
				/*$mail_body = "<html><head></head><body>";
				$mail_body .= "<p>"._("Dear").",</p>";
				$mail_body .= "<p>"._("These are the files and secret code for our webshop. Instructions how to implement it are included").",</p>";
				$mail_body .= "<p><strong>"._("API ID")."</strong>: <u>".$api['0']->api_id."</u></p>";
				$mail_body .= "<p><strong>"._("SECRET KEY")."</strong>: <u>".$api['0']->api_secret."</u></p>";
				$mail_body .= "<p>".$Options3."</p>";
				$mail_body .= "</body></html>";*/

				$attachment_path = "/../../online-bestellen.zip";
				$attachment_name = _("API_FILES.zip");
				if(send_email($toEmail, $fromEmail, _("API INFORMATION"), $mail_body, $Company[0]->company_name, $attachment_path, $attachment_name, 'company', 'random', 'send_api_info_n_files')){
					$this->messages->add(_('Information has been sent.'),'success');
				}else{
					$this->messages->add(_('error!!couldn\'t send information'),'error');
				}

			}

			redirect('cp/cdashboard/profile','refresh');
		}
		else
		{

			$data['company_profile'] = $company =$this->Mcompany->get_company();
			$this->load->model('Mcompany_type');
			$data['company_type']=$this->Mcompany_type->get_company_type();

			$this->load->model('Mcountry');
			$data['country']=$this->Mcountry->get_country();

			$this->load->model('Mapi');
			$data['api_codes'] = $this->Mapi->get_api();

			$data['trail_date']=$company[0]->trial;
			$data['on_trial']=$company[0]->on_trial;
			$type_id=$company[0];
			$company_type_id=$type_id->type_id;
			$result = $this->Mcompany->get_co_type($company_type_id);
			$type_name=$result['company_type_name'];
			$data['type_slug'] =str_replace(" ","+",$type_name);

			$company_slug=$company[0];
			$data['company_slug']=$company_slug->company_slug;
			$data['general_settings'] = $this->Mgeneral_settings->get_general_settings(array('company_id'=>$this->company_id),'hide_bp_intro');
			$data['content']="cp/profile";
			$data['ac_type_id'] = $this->company->ac_type_id;
			$this->load->view('cp/cp_view',$data);

		}

	}


	function loginSuper()
	{
	    if($this->input->post('company_id') && $this->input->post('company_parent_id') && $this->input->post('access'))
		{
	  	    $super_company = $this->Mcompany->get_company( array( 'id' => $this->input->post('company_parent_id') , 'access_super' => $this->input->post('access'), 'role' => 'super'  ) );

			if(!empty($super_company))
			{
			    $super_company = $super_company[0];

			    $data = array(
					'cp_user_id' => $super_company->id,
					'cp_username' => $super_company->username,
					'cp_user_role' => $super_company->role,
					'cp_user_parent_id' => $super_company->parent_id,
					'cp_is_logged_in' => true
				);

				$this->session->set_userdata($data);

				redirect('cp');

			}
			else
			{
			    ?>
				<script type="text/javascript">
					alert("<?php echo _('Invalid Login ! Please try again.'); ?>");
					window.location = '<?php echo base_url(); ?>cp';
				</script>
				<?php
			}
		}
		else
		{
		    ?>
			<script type="text/javascript">
			    alert("<?php echo _('Some error occured ! Please try again.'); ?>");
				window.location = '<?php echo base_url(); ?>cp';
			</script>
			<?php
		}
	}

	function ibsoft_module( $order_id = NULL, $action = NULL, $comp_id = NULL )
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		$this->load->library('Excel');

	    if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
		    $data['pickup_delivery_closed'] = $this->Mcalender->get_pickup_delivery_closed(); //for calender

			/*===========models================ */
			$this->load->model('Morders');
			$this->load->model('Morder_details');
			/*=================================*/


			if( $order_id != NULL && $action == 'invoice' )
			{
			    $order = $this->Morders->get_orders($order_id);

				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Ref');
				$objPHPExcel->getActiveSheet()->SetCellValue('B1', _('Quantity'));
				$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Benaming');
				$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Prijs');
				$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Memo');

				$client_number = '';

				if(!empty($order))
				  foreach($order as $o)
				  {
				     $client_email = $o->email_c;
					 $client_name = $o->firstname_c.' '.$o->lastname_c;

				     $company_id = $this->company_id;

					 if( $comp_id )
					   $company_id = $comp_id;

					 $client_number = $this->Mclients->get_client_number($o->clients_id,$company_id);

					 if(empty($client_number)) // Get company's default client number
					 {
						$company = $this->Mcompany->get_company( array('id'=>$this->company_id) );

						if( !empty($company) && $company[0]->ibsoft_active==1)
						  $client_number = $company[0]->client_number;
						else
						  $client_number = '';
					 }else{
					 	$client_number = $client_number->client_number;
					 }

					 $order_details = $this->Morder_details->get_order_details($o->id);

					 $total='';
					 $TempExtracosts=array();

					 for($i=0;$i<count($order_details);$i++){
						$unit = '';
						if($order_details[$i]->content_type == 1)
							$unit = '';
						else if($order_details[$i]->content_type == 2)
							$unit = ' personen';

						$pro_article_number = $order_details[$i]->pro_art_num;
						if($pro_article_number != ''){
							$length =  strlen($pro_article_number);
							if($length < 8){
								$required_zeros = (int)(8-$length);
								for($j = 0; $j < $required_zeros; $j++){
									$pro_article_number = '0'.$pro_article_number;
								}
							}
						}else{
							//$pro_article_number = '00000000';
						}

						$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.($i+2), (string)$pro_article_number, PHPExcel_Cell_DataType::TYPE_STRING);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.($i+2), $order_details[$i]->quantity.$unit);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.($i+2), $order_details[$i]->proname);
						$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.($i+2), round($order_details[$i]->sub_total,2));
						$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.($i+2), ($order_details[$i]->pro_remark));

						if($order_details[$i]->add_costs != ""){
							$rsExtracosts = explode("#",$order_details[$i]->add_costs);
							for($j=0;$j<count($rsExtracosts);$j++){
								$TempExtracosts[$i][$j] = explode("_",$rsExtracosts[$j]);
							}
						}
						$total += $order_details[$i]->total;
					 }

					 $data['TempExtracosts'] = $TempExtracosts;
					 $data['total'] = $total;
				  }

				$datestamp = '';
				if($order['0']->order_pickupdate)
					$order_date = $order['0']->order_pickupdate;
				else
					$order_date = $order['0']->delivery_date;

				$datestamp = date('dmY',strtotime($order_date.' 00:00:00'));
				$filename = $client_number."-".$datestamp.".XLS";
				$filepath = dirname(__FILE__).'/../../../assets/cp/ibsoft-invoices/'.$filename;

				$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
				$objWriter->save($filepath);

				$company = $this->Mcompany->get_company();
				$company_email = '';

				if(!empty($company))
					$company_email = $company[0]->email;
				// --> Send XLS to email address as attachment

				send_email($company_email, 'info@fooddesk.net' , _('Order IBSoft Invoice'), '', 'OBS - Online Bestelsysteem', $filepath, NULL, 'site_admin', 'company', 'ibsoft_order_invoice' );

				$this->messages->add('Invoice exported and send to client !','success');

				redirect('cp/cdashboard/ibsoft_module');
			}

			if( $this->input->post('chk_all') && $this->input->post('export_and_send') )
			{
			    /*$tsv = array();
				$title= array( _('Article Number'),_('Quantity'),_('Product'),_('Price'),_('Remark') );
				$tsv[] = implode("\t", $title);*/

				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Ref');
				$objPHPExcel->getActiveSheet()->SetCellValue('B1', _('Quantity'));
				$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Benaming');
				$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Prijs');
				$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Memo');

				$datestamp = '';
				$date_error = false;
				$hold_date = '';
				$product_arr = array();

			    foreach($this->input->post('chk_all') as $order_id)
				{
				    $o = array();
					$order = $this->Morders->get_orders($order_id);

					if(!empty($order))
					  $o = $order[0];

					if(!empty($o))
					{
					    if( $hold_date == '' )
							$hold_date = ($o->order_pickupdate)?($o->order_pickupdate):($o->delivery_date);

						if( $hold_date != $o->order_pickupdate && $hold_date != $o->delivery_date )
					    {
						    $date_error = true;
							break;
						}

					    $order_details = $this->Morder_details->get_order_details($o->id);

						if(!empty($order_details))
						for($i=0;$i<count($order_details);$i++)
						{
							$pro_art_num = $order_details[$i]->pro_art_num;
							if($pro_art_num != ''){
								$length =  strlen($pro_art_num);
								if($length < 8){
									$required_zeros = (int)(8-$length);
									for($j = 0; $j < $required_zeros; $j++){
										$pro_art_num = '0'.$pro_art_num;
									}
								}
							}else{
								//$pro_art_num = '00000000';
							}
							//$pro_art_num = $order_details[$i]->pro_art_num?$order_details[$i]->pro_art_num:'--';
							$pro_remark = $order_details[$i]->pro_remark?$order_details[$i]->pro_remark:'--';

							if( empty($product_arr) || !isset($product_arr[$order_details[$i]->products_id]) )
							{
								$product_arr[$order_details[$i]->products_id] = array(

									 'pro_art_num' => $pro_art_num,
									 'quantity' => $order_details[$i]->quantity,
									 'content_type' => $order_details[$i]->content_type,
									 'proname' => $order_details[$i]->proname,
									 'price' => round($order_details[$i]->sub_total,2),
									 'pro_remark' => $pro_remark

								);
							}
							elseif(isset($product_arr[$order_details[$i]->products_id]))
							{
							    $product_arr[$order_details[$i]->products_id] = array(

								     'pro_art_num' => $pro_art_num,
								     'quantity' => ($product_arr[$order_details[$i]->products_id]['quantity']+$order_details[$i]->quantity),
									 'content_type' => $order_details[$i]->content_type,
									 'proname' => $order_details[$i]->proname,
									 'price' => round($order_details[$i]->sub_total,2),
									 'pro_remark' => $product_arr[$order_details[$i]->products_id]['pro_remark']."; ".$pro_remark
								);
							}

						 }


					}

				}

				if( $date_error )
				{
					$this->messages->add('The orders selected for export has different pickup / delivery date, so not possible to export and send !','error');
				}
				else
				{
					if(!empty($product_arr))
					  foreach($product_arr as $p)
					  {
						  $unit = '';
						  if($p['content_type'] == 1)
							$unit = '';
						  else if($p['content_type'] == 2)
							$unit = ' personen';

						  /*$row = array( $p['pro_art_num'], $p['quantity'].$unit,$p['proname'],$p['price'],$p['pro_remark'] );
						  $tsv[] = implode("\t", $row);*/
						  $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$j, $p['pro_art_num'], PHPExcel_Cell_DataType::TYPE_STRING);
						  $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$j, $p['quantity'].$unit);
						  $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$j, $p['proname']);
						  $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$j, $p['price']);
						  $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$j, $p['pro_remark']);
						  $j = $j + 1;
					  }

					//$tsv = implode("\r\n", $tsv);

					$company = $this->Mcompany->get_company( array('id'=>$this->company_id) );

					if( !empty($company) && $company[0]->ibsoft_active==1)
						$client_number = $company[0]->client_number;
					else
						$client_number = '';

					//echo $hold_date;

					//$datestamp = date("dmY");
					$datestamp = date("dmY",strtotime($hold_date.' 00:00:00'));

					if($client_number)
						$filename = $client_number."-".$datestamp.".XLS";
					else
						$filename = _('Clients')."-".$datestamp.".XLS";

					$filepath = dirname(__FILE__).'/../../../assets/cp/ibsoft-invoices/'.$filename;

					// --> Create file
					/*$fp = fopen($filepath,"w+");
					fwrite($fp,$tsv);
					fclose($fp);*/
					$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
					$objWriter->save($filepath);

					// --> Send XLS to email address as attachment

					$company = $this->Mcompany->get_company();
					$company_email = '';

					if(!empty($company))
					  $company_email = $company[0]->email; //'info@fooddesk.net';

					if($company_email)
					{

						send_email($company_email, 'info@fooddesk.net' , _('IBSoft Orders'), '', 'OBS - Online Bestelsysteem', $filepath, NULL, 'site_admin', 'company', 'ibsoft_orders' );
					}

					$this->messages->add('Invoice exported and send to your mail address !','success');
				}
			}


			if($this->input->post('act')=='do_filter')
			{
				/*===============for the searching==========*/

				$exp_start_date = explode("/",$this->input->post('start_date'));
				$start_date = $exp_start_date[2]."-".$exp_start_date[1]."-".$exp_start_date[0];

				/*$exp_end_date = explode("/",$this->input->post('end_date'));
				$end_date = $exp_end_date[2]."-".$exp_end_date[1]."-".$exp_end_date[0];	*/

				if( $this->company_role == 'super' )
				{
					$data['orders'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$start_date);
				     $data['orders_n'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$start_date);

					$data['sub_companies'] = $sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

					if( !empty($sub_companies) )
					  foreach( $sub_companies as $sc )
					  {
					      $comp_id = $sc->company_id;

						  $orders = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),$comp_id,$start_date);
				          $orders_n = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),$comp_id,$start_date);

						  if( !empty($orders) )
						  $data['orders'] = array_merge( $data['orders'], $orders );

						  if( !empty($orders_n) )
						  $data['orders_n'] = array_merge( $data['orders_n'], $orders_n );
					  }
				}
				else
				{
				     $data['orders'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$start_date);
				     $data['orders_n'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$start_date);
				}

				$data['first_load'] = 0;
				$data['content'] = 'cp/ibsoft_module';
				$this->load->view('cp/cp_view',$data);
			}
			elseif($this->input->post('tomorrow'))
			{
				/*===============for the searching==========*/
				$date_tomorrow = $this->input->post('date_tomorrow');
				if( $this->company_role == 'super' )
				{
					$data['orders'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_tomorrow);
				     $data['orders_n'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$date_tomorrow);

					$data['sub_companies'] = $sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

					if( !empty($sub_companies) )
					  foreach( $sub_companies as $sc )
					  {
					      $comp_id = $sc->company_id;

						  $orders = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),$comp_id,$date_tomorrow);
				          $orders_n = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),$comp_id,$date_tomorrow);

						  if( !empty($orders) )
						  $data['orders'] = array_merge( $data['orders'], $orders );

						  if( !empty($orders_n) )
						  $data['orders_n'] = array_merge( $data['orders_n'], $orders_n );
					  }
				}
				else
				{
				     $data['orders'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_tomorrow);
				     $data['orders_n'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$date_tomorrow);
				}
				$data['first_load'] = 0;
				$data['content'] = 'cp/ibsoft_module';
				$this->load->view('cp/cp_view',$data);
			}elseif($this->input->post('day_after_tomorrow'))
			{
				/*===============for the searching==========*/
				$date_after_tomorrow = $this->input->post('date_after_tomorrow');

				if( $this->company_role == 'super' )
				{
					$data['orders'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_after_tomorrow);
				     $data['orders_n'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$date_after_tomorrow);

					$data['sub_companies'] = $sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );

					if( !empty($sub_companies) )
					  foreach( $sub_companies as $sc )
					  {
					      $comp_id = $sc->company_id;

						  $orders = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),$comp_id,$date_after_tomorrow);
				          $orders_n = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),$comp_id,$date_after_tomorrow);

						  if( !empty($orders) )
						  $data['orders'] = array_merge( $data['orders'], $orders );

						  if( !empty($orders_n) )
						  $data['orders_n'] = array_merge( $data['orders_n'], $orders_n );
					  }
				}
				else
				{
				     $data['orders'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'unsubscribe'),'',$date_after_tomorrow);
				     $data['orders_n'] = $this->Morders->get_orders('','','','','',array('clients.notifications'=>'subscribe'),'',$date_after_tomorrow);

				}
				$data['first_load'] = 0;
				$data['content'] = 'cp/ibsoft_module';
				$this->load->view('cp/cp_view',$data);
			}
			else
			{
				$data['first_load'] = 1;
				$data['orders'] = array();
				$data['orders_n'] = array();
				$data['content'] = 'cp/ibsoft_module';
				$this->load->view('cp/cp_view',$data);
			}

		}
		else
		{
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}


	function page_not_found( $page_accessed = '' )
	{
	    $data['account_types'] = $this->Mpackages->get_account_types();
		$data['company'] = $company = $this->Mcompany->get_company();
		$data['curr_account_type'] = array();
		$data['company_types'] = $this->db->get("company_type")->result_array();
		if( !empty($company) )
		{
			$company = $company[0];
			$ac_type_id = $company->ac_type_id;

			$account_type = $this->Mpackages->get_account_types( array('id'=>$ac_type_id) );
			if(!empty($account_type) && isset($account_type[0]))
			  $data['curr_account_type'] = $account_type[0];
		}

		$data['content'] = 'cp/page_not_found';
		$this->load->view('cp/cp_view',$data);
	}

	function calculate_price_after_multidiscount(){
		$this->load->model('Mproduct_discount');
		$product_id = $this->input->post('product_id');
		$content_type= $this->input->post('type');
		$qty = $this->input->post('qty');
		$response = $this->Mproduct_discount->get_product_multidiscount($product_id,$content_type,$qty);
		return $response;
	}

	function delete_image(){
		$product_id = $this->input->post('product_id');
		$get_product_image = $this->db->get_where('products',array('id'=>$product_id))->result_array();
		$tmpp = explode('/',$get_product_image['0']['image']);
		$record_num = end($tmpp);
		//echo $record_num;
		$filename	= dirname(__FILE__).'/../../../assets/cp/images/product/'.$record_num;
		$filename2	= dirname(__FILE__).'/../../../assets/cp/images/product_100_100/'.$record_num;
		$filename3	= dirname(__FILE__).'/../../../assets/cp/images/product_270_270/'.$record_num;
		$filename4	= dirname(__FILE__).'/../../../assets/cp/images/product_60_60/'.$record_num;
		if (file_exists($filename)) {
			$output = unlink($filename);
			if($output){
				(file_exists($filename2))?unlink($filename2):null;
				(file_exists($filename3))?unlink($filename3):null;
				(file_exists($filename4))?unlink($filename4):null;

				$this->db->where('id',$product_id);
				$this->db->update('products',array('image'=>''));
				echo "success";die;
			}
		}
		echo "error";
	}

	function delete_image_more(){
		$product_id = $this->input->post('product_id');
		$get_product_image = $this->input->post('src_path');
		//$get_product_image = $this->db->get_where('products',array('id'=>$product_id))->result_array();
		$record_num = end(explode('/',$get_product_image));
		//echo $record_num;
		if($record_num != '0'){
			$filename	= dirname(__FILE__).'/../../../assets/cp/images/product/'.$record_num;
			if (file_exists($filename)) {
				unlink($filename);

				$this->db->select('more_image');
				$more_image = $this->db->get_where('products',array('id'=>$product_id))->row();

				if(!empty($more_image)){
					$remain = str_replace($record_num, '0', $more_image->more_image);

					$this->db->where('id',$product_id);
					$this->db->update('products',array('more_image'=>$remain));
					echo "success";die;
				}
			}
		}
		echo "error";
	}

	function delete_cat_image(){
		$category_id = $this->input->post('category_id');
		$get_product_image = $this->db->get_where('categories',array('id'=>$category_id))->result_array();
		$record_num = end(explode('/',$get_product_image['0']['image']));
		$filename	= dirname(__FILE__).'/../../../assets/cp/images/categories/'.$record_num;
		$filename2	= dirname(__FILE__).'/../../../assets/cp/images/categories_100_100/'.$record_num;
		if (file_exists($filename)) {
			$output = unlink($filename);
			if($output){
				(file_exists($filename2))?unlink($filename2):null;

				$this->db->where('id',$category_id);
				$this->db->update('categories',array('image'=>''));
				echo "success";die;
			}
		}
		echo "error";
	}

	function delete_subcat_image(){
		$subcategory_id = $this->input->post('subcategory_id');
		$get_product_image = $this->db->get_where('subcategories',array('id'=>$subcategory_id))->result_array();
		$record_num = end(explode('/',$get_product_image['0']['subimage']));
		$filename	= dirname(__FILE__).'/../../../assets/cp/images/subcategories/'.$record_num;
		$filename2	= dirname(__FILE__).'/../../../assets/cp/images/subcategories_100_100/'.$record_num;
		if (file_exists($filename)) {
			$output = unlink($filename);
			if($output){
				(file_exists($filename2))?unlink($filename2):null;

				$this->db->where('id',$subcategory_id);
				$this->db->update('subcategories',array('subimage'=>''));
				echo "success";die;
			}
		}
		echo "error";
	}

	function webresizer(){
		$data = array();
		$data['image_url'] = ($_GET['url'])?$_GET['url']:'';
		$data['image_name'] = ($_GET['filename'])?$_GET['filename']:'';
		$data['image_size'] = ($_GET['filesize'])?$_GET['filesize']:'';
		$data['image_type'] = ($_GET['filetype'])?$_GET['filetype']:'';
		$data['image_height'] = ($_GET['height'])?$_GET['height']:'';
		$data['image_width'] = ($_GET['width'])?$_GET['width']:'';
		$this->load->view('cp/image_webresizer',$data);
	}

	function form_values(){
		$form_data_array = $this->input->post();
		if($this->session->set_userdata('form_data',$form_data_array))
			echo "success";
		else
			echo "failed";
	}

	function form_values_categories(){
		$form_data_array = $this->input->post();
		if($this->session->set_userdata('form_data_category',$form_data_array))
			echo "success";
		else
			echo "failed";
	}

	function form_values_subcategories(){
		$form_data_array = $this->input->post();
		if($this->session->set_userdata('form_data_subcategory',$form_data_array))
			echo "success";
		else
			echo "failed";
	}

	function set_company_css(){
		$themes = $this->db->get('themes')->result();
		$themess = $themes;
		$this->db->select('company_id');
		$this->db->distinct();
		//$this->db->where('company_id !=','105');
		$companyIds = $this->db->get('company_css')->result_array();
		foreach ($companyIds as $companyId){
			if($companyId['company_id'] && !empty($themes))
			{
				foreach($themess as $t)
				{
					if($t->id == 4 || $t->id == 5 || $t->id == 6){
						$insert = array();
						$insert['company_id'] = $companyId['company_id'];
						$insert['theme_id'] = $t->id;
						$insert['theme_custom_css'] = $t->theme_css;
						$insert['use_own_css'] = 0;

						$this->db->insert('company_css', $insert);
					}
				}
			}
		}
	}

	function print_labeler($order_id = null, $type = null){
		//$order_id = $this->input->post('order_id');
		$response = array();
		if($order_id && is_numeric($order_id)){
			$general_settings = $this->Mgeneral_settings->get_general_settings();
			if($general_settings['0']->activate_labeler){
				$doc_text = array();
				$height = (int)(41/25.4)*72;
				$width = (int)(89/25.4)*72;
				if($type == 'per_order'){

					// Adding Order ID
					$doc_text['order_id'] = $order_id;

					$this->load->model('Morders');
					$order_data = $this->Morders->get_orders($order_id);
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
					if($order_data['0']->phone_c){
						$add_text .= $order_data['0']->phone_c."\n";
					}elseif($order_data['0']->mobile_c){
						$add_text .= $order_data['0']->mobile_c."\n";
					}

					$doc_text['address'] = $add_text;
					// Adding pickup date
					$date_content = '--';
					if($order_data['0']->option == 1){

						$remark = str_replace("&","en",$order_data['0']->order_remarks);
						if(strlen($remark) >= 100){
							$remark = substr($remark,0,100)."...";
						}
						$remark = addslashes($remark);

						$doc_text['remark'] = '';
						if($remark != ''){
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

					}elseif($order_data['0']->option == 2){

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
				}elseif($type == 'per_product'){

					$this->load->model('Morder_details');
					$this->load->model('Morders');
					$order_data_pre = $this->Morders->get_orders($order_id);
					$order_data = $this->Morder_details->get_order_details($order_id);
					$data_array = array();
					$i = 0;

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

								$data_array[$i] = $doc_text;
								$i++;
							}
						}else{
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

							$data_array[$i] = $doc_text;
							$i++;
						}
					}
				$response = array('error'=>0,'message'=>'array','data'=>$data_array);
				}
			}else{
				$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
			}
		}else{
			$response = array('error'=>1,'message'=>_("No data found to print. Please try again."),'data'=>'');
		}
		echo json_encode($response);
	}

	function update_client_number(){
		$client_id = $this->input->post('client_id');
		$company_id = $this->input->post('company_id');
		$client_number = $this->input->post('client_number');
		if($client_id && $company_id && $client_number){
			$updated = $this->Mclients->update_client_number();

			if($updated)
				echo _('Client number updated successfully !');
			else
				echo _('Some error occured while updating client number.');
		}else{
			echo _("Some error occured. Please try again");
		}
	}

	function download_image(){
		$order_detail_id = $this->input->post('order_detail_id');
		if($order_detail_id){
			$this->load->model('Morder_details');
			$order_details_data = $this->Morder_details->get_single_order_detail($order_detail_id);
			$image = $order_details_data['0']->image;
			$image_name = end(explode('/',$image));
			//$extension = end(explode('.',$image_name));
			$image_value = file_get_contents($image);
			if (!is_dir(dirname(__FILE__).'/../../../assets/ordered_image')) {
				$oldumask = umask(0);
				mkdir(dirname(__FILE__).'/../../../assets/ordered_image',0777);
				umask($oldumask);
				//mkdir('mydir', 0777); // or even 01777 so you get the sticky bit set
			}

			if(file_exists(dirname(__FILE__).'/../../../assets/ordered_image/'.$image_name)){
				echo $image_name;
			}else{
				$save_path = dirname(__FILE__).'/../../../assets/ordered_image/'.$image_name;
				if(file_put_contents($save_path,$image_value))
					echo $image_name;
				else
					echo "error";
			}
		}else{
			echo "error";
		}
	}

	function addons(){

		if( $this->company_role == 'master' || $this->company_role == 'super' ){

			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$this->load->model('Maddons');
			$data['total_addons'] = $this->Maddons->get_addons();
			$activated_addons = $this->Maddons->get_activated_addons();
			$data['activated_addons'] = explode("#",$activated_addons['0']->activated_addons);
			$data['content'] = 'cp/addon';
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			// restricted
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}

	function activate_deactivate_addon(){
		if($this->input->post('addon_id')){
			$this->load->model('Maddons');
			if($this->input->post('action') == 'activate'){
				$addon_info = $this->Maddons->select($this->input->post('addon_id'));

				$mail_data['request_txt'] = _("Here is a request to activate addons: ");
				$mail_data['addon_title'] = $addon_info['0']->addon_title;
				$mail_data['company_name'] = $this->company->company_name;
				$mail_data['email_ads'] = '';
				if($this->company->email_ads == "1"){
					$this->load->model('Memail_messages');
					$email_messages_detail = $this->Memail_messages->get_email_messages();
					$signature = $email_messages_detail[0]->emailads_text_message;
					$mail_data['email_ads'] = '<tr>
													<td align="left">'.$signature.'</td>
												</tr>';
				}

				$mail_body = $this->load->view('mail_templates/'.$this->lang_u.'/addons_request',$mail_data,true);

				if(send_email($this->config->item('site_admin_email'), $this->company->email , _("Addon activate request"), $mail_body, NULL, NULL, NULL, 'company', 'site_admin', 'activate_addon_req' ))
					echo true;
				else
					echo false;
			}elseif($this->input->post('action') == 'deactivate'){
				$addon_info = $this->Maddons->select($this->input->post('addon_id'));
				$mail_data['request_txt'] = _("Here is a request to deactivate addons: ");
				$mail_data['addon_title'] = $addon_info['0']->addon_title;
				$mail_data['company_name'] = $this->company->company_name;
				$mail_data['email_ads'] = '';
				if($this->company->email_ads == "1"){
					$this->load->model('Memail_messages');
					$email_messages_detail = $this->Memail_messages->get_email_messages();
					$signature = $email_messages_detail[0]->emailads_text_message;
					$mail_data['email_ads'] = '<tr>
													<td align="left">'.$signature.'</td>
												</tr>';
				}

				$mail_body = $this->load->view('mail_templates/'.$this->lang_u.'/addons_request',$mail_data,true);

				if(send_email($this->config->item('site_admin_email'), $this->company->email , _("Addon deactivate request"), $mail_body, NULL, NULL, NULL, 'company', 'site_admin', 'deactivate_addon_req' ))
					echo true;
				else
					echo false;
			}else{
				echo false;
			}
		}else{
			echo false;
		}
	}

	function myaccount(){

		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

			$data['general_settings'] = $this->Mgeneral_settings->get_general_settings();
			$data['packages'] = $this->Mpackages->get_packages();
			$data['account_types'] = $this->Mpackages->get_account_types();
			$data['company'] = $company = $this->Mcompany->get_company();


			$data['curr_account_type'] = array();
			if( !empty($company) )
			{
				$company = $company[0];
				$data['trial_date']=$company->trial;
				$ac_type_id = $company->ac_type_id;
				$account_type = $this->Mpackages->get_account_types( array('id'=>$ac_type_id) );
				if(!empty($account_type) && isset($account_type[0]))
					$data['curr_account_type'] = $account_type[0];

			}
			$company = $this->Mcompany->get_company();
			//$trial_date=$company[0];
			$data['ac_type_id']=$company[0]->ac_type_id;
			$data['trail_date']=$company[0]->trial;
			$type_id=$company[0];
			$company_type_id=$type_id->type_id;
			$result = $this->Mcompany->get_co_type($company_type_id);
			$type_name=$result['company_type_name'];
			$data['type_slug'] =str_replace(" ","+",$type_name);

			$company_slug=$company[0];
			$data['company_slug']=$company_slug->company_slug;

			$data['content'] = 'cp/myaccount';
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}

	function change_account(){

		$response = array();
		if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$account_id = $this->input->post('account_type_id');
			if($account_id != null){
				$desired_account_type = $this->Mpackages->get_account_types( array('id'=>$account_id) );

				if(!empty($desired_account_type)){
					$current_account_type = $this->Mpackages->get_account_types( array('id'=>$this->company->ac_type_id) );

					$mail_data = array();
					if($desired_account_type['0']->id > $current_account_type['0']->id){
						$mail_data['request_txt'] = _("Here is a request to upgrade account: ");
					}else{
						$mail_data['request_txt'] = _("Here is a request to downgrade account: ");
					}
					$mail_data['company_id'] = $this->company->id;
					$mail_data['company_name'] = $this->company->company_name;
					$mail_data['current_ac_type_id'] = $current_account_type['0']->id;
					$mail_data['current_ac_type_title'] = $current_account_type['0']->ac_title;

					$mail_data['desired_ac_type_id'] = $desired_account_type['0']->id;
					$mail_data['desired_ac_type_title'] = $desired_account_type['0']->ac_title;

					$mail_data['email_ads'] = '';
					if($this->company->email_ads == "1"){
						$this->load->model('Memail_messages');
						$email_messages_detail = $this->Memail_messages->get_email_messages();
						$signature = $email_messages_detail[0]->emailads_text_message;
						$mail_data['email_ads'] = '<tr>
													<td align="left">'.$signature.'</td><td></td>
												</tr>';
					}

					$mail_body = $this->load->view('mail_templates/'.$this->lang_u.'/change_account_request',$mail_data,true);

					if(send_email($this->config->item('site_admin_email'), $this->company->email , _("Account Change Request"), $mail_body, NULL, NULL, NULL, 'company', 'site_admin', 'change_account_type_req' ))
						$response = array('error'=>0,'message'=>_("<b>Request succesfully sent</b> - We will review your request asap and you will be notified immediately after we have ".(($desired_account_type['0']->id > $current_account_type['0']->id)?'upgrade':'downgrade')." the system"));
					else
						$response = array('error'=>1,'message'=>_("Request is not sent succesfully. Please try again"));
				}else{
					$response = array('error'=>1,'message'=>_("No account found"));
				}
			}else{
				$response = array('error'=>1,'message'=>_("We can not proceed your request. Some error occured"));
			}
		}
		else
		{
			$response = array('error'=>1,'message'=>_("Sorry! You are not allowed to change type at this moment"));
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

	/**
	 * This function is used to set first login to be false
	 */
	function accept_mail_from_bestelonline(){
		$this->db->where("id",$this->company_id);
		if($this->db->update("company",array("login_first_time" => '0')))
			echo "success";
		else
			echo "error";
	}

	/**
	 * This function is used to get products name of the current company
	 */
	function get_product_name(){

		$this->db->select("products.id,products.proname");
		$this->db->where("((semi_product = 0) OR (semi_product = 1 AND direct_kcp = 0))");
		$this->db->where("company_id",$this->company_id);
		$pro_name = $this->db->get("products")->result_array();

		$proname_array = array();
		if(!empty($pro_name)){
			foreach($pro_name as $val){
				$arr['label'] = stripslashes($val['proname']);
				$arr['value'] = $val['id'];
				$proname_array[] = $arr;
			}
		}
		echo json_encode($proname_array);
	}

	/**
	 * This function is used to get semi products name of the current company
	 */
/*	function get_semi_product_name(){
		$this->db->select("products.id,products.proname");
		$semi_name = $this->db->get_where("products",array("company_id" => $this->company_id,"semi_product"=>1))->result_array();
		$seminame_array = array();
		if(!empty($semi_name)){
			foreach($semi_name as $val){
				$arr['label'] = stripslashes($val['proname']);
				$arr['value'] = $val['id'];
				$seminame_array[] = $arr;
			}
		}
		echo json_encode($seminame_array);
	}	*/

	function get_semi_product_name($page_id = 0){
		$this->db->select("products.id,products.proname");
		if ($page_id){
			$semi_name = $this->db->get_where("products",array("company_id" => $this->company_id,"semi_product"=>2))->result_array();
		}
		else {
			$semi_name = $this->db->get_where("products",array("company_id" => $this->company_id,"direct_kcp" => 1,"semi_product"=>1))->result_array();
		}
		$seminame_array = array();
		if(!empty($semi_name)){
			foreach($semi_name as $val){
				$arr['label'] = stripslashes($val['proname']);
				$arr['value'] = $val['id'];
				$seminame_array[] = $arr;
			}
		}
		echo json_encode($seminame_array);
	}

	/**
	 * @name faq_new
	 * @property Function to load help file- Integrating CI and Help and supportscript of Codecanyon
	 * @access public
	 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
	 *
	 */
	public function faq_new(){
		if ($this->company_role == 'master' || $this->company_role == 'sub') {

			$company = $this->Mcompany->get_company ();
			$company_slug = $company [0]->company_slug;

			if (! $company_slug)
				$company_slug = $company [0]->username;

			$data ['company_slug'] = $company_slug;
			$data ['general_settings'] = $this->Mgeneral_settings->get_general_settings ();
			$data ['content'] = 'cp/faq_new';

			$this->load->view ( 'cp/cp_view', $data );
		} elseif ($this->company_role == 'super') {
			$data ['content'] = 'cp/faq_new';
			$this->load->view ( 'cp/cp_view', $data );
		}
	}

	/**
	 * @name forum
	 * @property Function to load forum file- Integrating CI and Forum script of Codecanyon
	 * @access public
	 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
	 *
	 */
	public function forum(){
		//$_SESSION['forum_admin_logged_in'] = false;
		session_start();
		unset($_SESSION['forum_admin_logged_in']);
		if ($this->company_role == 'master' || $this->company_role == 'sub') {

			$company = $this->Mcompany->get_company ();
			$company_slug = $company [0]->company_slug;

			if (! $company_slug)
				$company_slug = $company [0]->username;

			$data ['company_slug'] = $company_slug;
			$data ['general_settings'] = $this->Mgeneral_settings->get_general_settings ();
			$data ['content'] = 'cp/forum';

			$this->load->view ( 'cp/forum_view', $data );
		} elseif ($this->company_role == 'super') {
			$data ['content'] = 'cp/forum';
			$this->load->view ( 'cp/forum_view', $data );
		}
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
						$order_data_pre = $this->Morders->get_orders($order_id);
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

	public function gouden_tips(){
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
		$data['content']="cp/tips";
		$this->load->view('cp/cp_view',$data);
	}



	public function print_product_download($action){
		$data=array('download_clicked'=>"1",'download_clicked_date'=>date('Y-m-d H:i:s'));
        $this->db->where(array('company_id' => $this->company_id));
        $this->db->update('products',$data);
        $filename = $this->print_product($action);
        $report_export_array = array(
        	                         "company_id" => $this->company_id,
        	                         "report_export_name" => 'print_product',
        	                         "type" => 'Report',
        	                         "filename" => $filename,
        	                         "date" => date('Y-m-d H:i:s')
        	                        );
        $this->db->insert('report_export_download',$report_export_array);
	}

	/**
	 * This function is used to print pdf of products with Ingredients
	 */
	public function print_product($action, $cat_id = 0, $subcat_id = 0, $ids = ''){
		$this->load->model('Mproducts');
		$this->load->model('Mcategories');
		$this->load->model('Msubcategories');

		$cat_name = '';
		$subcat_name = '';

		$row_html = '';

		//if($action == 'print_these'){
	/*
		//get allergence array from FDD db
		
		$this->fdb->select('all_id,all_name_dch');
		$aller_arr = $this->fdb->get('allergence')->result_array();
		foreach ($aller_arr as $val){
			$allergence_words[strtolower($val['all_name_dch'])] = $val['all_id'];
		}
		
	*/
		if($cat_id > 0){
			$cat_name = $this->Mcategories->get_cat_name($cat_id);
			if(!empty($cat_name))
				$cat_name = $cat_name['name'];
		}

		if($subcat_id > 0){
			$subcat_name = $this->Msubcategories->get_sub_cat_name($subcat_id);
			if(!empty($subcat_name))
				$subcat_name = ' > '.$subcat_name['subname'];
		}
		if($ids != ''){
			//print these..

			$ids = substr($ids, 1);
			$pro_ids = explode('.',$ids);

			//$pro_ingr = $this->Mproducts->get_product_ingredients($pro_ids[0]);

			if(!empty($pro_ids)){

				$row_html .= '<h3 style="font-family: arial;">'._('Product List').'<span style="font-size: 8pt;">&nbsp;'._('(including ingredients)').'</span></h3>';

				$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

				$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.$subcat_name.'</th></tr></thead><tbody>';

				//$row_html .= '<tr style="text-align: left;"><td style="width: 250px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
				$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

				//Looping products using product ids..
				foreach($pro_ids as $p_id){

					$row_html .= '<tr>';

					$product = $this->Mproducts->get_product_information($p_id);

					$product = $product[0];

					$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

					/*$k_ingredients = $this->Mproducts->get_product_ingredients_dist($p_id);
					$product_ingredients_vetten = $this->Mproducts->get_product_ingredients_vetten_dist($p_id);
	 				$product_additives = $this->Mproducts->get_product_additives_dist($p_id);
					if(!empty($k_ingredients)){
						$ing_str = '';
						//Looping product ingredients to create ingredient string..
						foreach ($k_ingredients as $ingredients){
							//$ing_str .= ', '.stripslashes($k_ingredient->prefix).' '.stripslashes($k_ingredient->ki_name);

							if($ingredients->ki_name == ')' ){
								$ing_str = substr($ing_str, 0, -2);
								$ing_str .= ' ';
							}
							if($ingredients->ki_name == '(' ){
								$ing_str = substr($ing_str, 0, -2);
								$ing_str .= ' ';
							}

							if($ingredients->ki_id != 0){
								if($ingredients->prefix == ''){
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
								}else{
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
								}
							}else if($ingredients->ki_name == ')'){

								$ing_str .= $ingredients->ki_name.', ';

							}else if($ingredients->ki_name == '('){

								$ing_str .= $ingredients->ki_name.' ';

							}else{
								if($ingredients->prefix == ''){
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
								}else{
									$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
								}
							}

						}
						$ing_str = substr($ing_str, 0, -2);

						$ing_end = "";
						if(!empty($product_ingredients_vetten)){
							$ing_end .= "Plantaardige vetstof(";
							foreach ($product_ingredients_vetten as $vetten){
								$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words).", ";
							}
							$ing_end = rtrim(trim($ing_end),",");
							$ing_end .= ")";
						}

						if(!empty($product_additives)){
							$additive_arr = array();
							foreach ($product_additives as $add){
								if(!in_array($add->add_name,$additive_arr)){
									$additive_arr[] = $add->add_name;
								}
							}

							for($i = 0; $i < count($additive_arr); $i++){
								if($ing_end != ""){
									$ing_end .= ", ";
								}
								if($additive_arr[$i] != "others")
									$ing_end .= stripslashes($additive_arr[$i]);

								$count = 0;
								$add_ing = "";
								foreach ($product_additives as $add){
									if(($add->add_name == $additive_arr[$i]) && ($add->ki_name != "")){
										$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words).", ";
										$count = $count+1;
									}
								}
								$add_ing = rtrim(trim($add_ing),",");
								if($count == 1){
									$ing_end .= " ".$add_ing;
								}
								elseif ($count >1 ){
									if($additive_arr[$i] == "others"){
										$ing_end .= $add_ing;
									}else{
										$ing_end .= "(".$add_ing.")";
									}
								}
							}
						}
						if($ing_end != ""){
							$ing_end = ", ".$ing_end;
						}

						$ing_str .= $ing_end;
						if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
							$product->ingredients = substr($ing_str, 2);
						else
							$product->ingredients = $product->ingredients;
					}

					if($product->ingredients == '')
						$product->ingredients = '--';
					$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->ingredients.'</td>';*/


					$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
					$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);
					if(!empty($k_allergence)){
						$allrg_str = '';
						//Looping product allergence to create allergence string..
						foreach ($k_allergence as $k_allerg){
							$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);

							if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
								$a1 = '';
								if(!empty($k_sub_allergence)){
									$a1 .= ' (';
									foreach ($k_sub_allergence as $k_sub_allerg){
										if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
											$a1 .=  $k_sub_allerg->sub_ka_name.', ';
										}
									}
									$a1 = rtrim($a1,', ');
									$a1 .= ')';
									$a1 = str_replace('()', '', $a1);
								}
								$allrg_str .= $a1;
							}
						}
						if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
							if(strpos($allrg_str,'Melk') != false && strpos($allrg_str,'Lactose') != false){
								$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
								$allrg_str = str_replace('Lactose, ', '', $allrg_str);
							}
							$product->allergence = substr($allrg_str, 2);
						}
						else
							$product->allergence = $product->allergence;
					}
					if($product->allergence == '')
						$product->allergence = '--';
					$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

					/*$k_traces = $this->Mproducts->get_product_traces($p_id);
					if(!empty($k_traces)){
						$tra_str = '';
						//Looping product trashes to create trashes string..
						foreach ($k_traces as $k_trace){
							$tra_str .= ', '.stripslashes($k_trace->prefix).' '.stripslashes($k_trace->kt_name);
						}
						if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
							$product->traces_of = substr($tra_str,2);
						else
							$product->traces_of = $product->traces_of;
					}
					if($product->traces_of == '')
						$product->traces_of = '--';
					$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->traces_of.'</td>';*/

					$row_html .= '</tr>';

				}
				$row_html .= '</tbody></table>';
			}
		} else {
			$row_html = '';

			$row_html .= '<h3 style="font-family: arial;">Productenlijst<span style="font-size: 8pt;">&nbsp;(inclusief ingredienten)</span></h3>';

			$cat_arr = $this->Mcategories->get_category(array('company_id' => $this->company_id));

			if(!empty($cat_arr)){
				//looping categories..
				foreach($cat_arr as $cat){

					$subcat_arr = $this->Msubcategories->get_subcategory(array('categories_id'=> $cat->id));
					//print_r($subcat_arr);

					if($cat->id > 0){
						$cat_name = $this->Mcategories->get_cat_name($cat->id);
						if(!empty($cat_name))
							$cat_name = $cat_name['name'];
					}

					if(!empty($subcat_arr)){

						//looping subcategories..
						foreach($subcat_arr as $subcat){
							if($subcat->id > 0){
								$subcat_name = $this->Msubcategories->get_sub_cat_name($subcat->id);
								if(!empty($subcat_name))
									$subcat_name = ' > '.$subcat_name['subname'];
							}

							$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

							$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.$subcat_name.'</th></tr></thead><tbody>';

							// $row_html .= '<tr style="text-align: left;"><td style="width: 150px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('INGREDIENTS').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
							//$row_html .= '<tr style="text-align: left;"><td style="width: 250px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
							$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

							$products = $this->Mproducts->get_products($cat->id, $subcat->id);

							if(!empty($products)){
								//Looping products using product ids..
								foreach($products as $product){

									$p_id = $product->id;

									$row_html .= '<tr>';

									$product = $this->Mproducts->get_product_information($p_id);

									$product = $product[0];

									$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

									/*$k_ingredients = $this->Mproducts->get_product_ingredients_dist($p_id);
									$product_ingredients_vetten = $this->Mproducts->get_product_ingredients_vetten_dist($p_id);
					 				$product_additives = $this->Mproducts->get_product_additives_dist($p_id);
									if(!empty($k_ingredients)){
										$ing_str = '';
										//Looping product ingredients to create ingredient string..
										foreach ($k_ingredients as $ingredients){
											if($ingredients->ki_name == ')' ){
												$ing_str = substr($ing_str, 0, -2);
												$ing_str .= ' ';
											}
											if($ingredients->ki_name == '(' ){
												$ing_str = substr($ing_str, 0, -2);
												$ing_str .= ' ';
											}

											if($ingredients->ki_id != 0){
												if($ingredients->prefix == ''){
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
												}else{
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
												}
											}else if($ingredients->ki_name == ')'){

												$ing_str .= $ingredients->ki_name.', ';

											}else if($ingredients->ki_name == '('){

												$ing_str .= $ingredients->ki_name.' ';

											}else{
												if($ingredients->prefix == ''){
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).', ';
												}else{
													$ing_str .= get_the_allergen($ingredients->ki_name,$ingredients->have_all_id,$allergence_words).'('.$ingredients->prefix.')'.', ';
												}
											}
										}
										$ing_str = substr($ing_str, 0, -2);

										$ing_end = "";
										if(!empty($product_ingredients_vetten)){
											$ing_end .= "Plantaardige vetstof(";
											foreach ($product_ingredients_vetten as $vetten){
												$ing_end .= get_the_allergen($vetten->ki_name,$vetten->have_all_id,$allergence_words).", ";
											}
											$ing_end = rtrim(trim($ing_end),",");
											$ing_end .= ")";
										}

										if(!empty($product_additives)){
											$additive_arr = array();
											foreach ($product_additives as $add){
												if(!in_array($add->add_name,$additive_arr)){
													$additive_arr[] = $add->add_name;
												}
											}

											for($i = 0; $i < count($additive_arr); $i++){
												if($ing_end != ""){
													$ing_end .= ", ";
												}
												if($additive_arr[$i] != "others")
													$ing_end .= stripslashes($additive_arr[$i]);
												$count = 0;
												$add_ing = "";
												foreach ($product_additives as $add){
													if(($add->add_name == $additive_arr[$i]) && ($add->ki_name != "")){
														$add_ing .= get_the_allergen($add->ki_name,$add->have_all_id,$allergence_words).", ";
														$count = $count+1;
													}
												}
												$add_ing = rtrim(trim($add_ing),",");
												if($count == 1){
													$ing_end .= " ".$add_ing;
												}
												elseif ($count >1 ){
													if($additive_arr[$i] == "others"){
														$ing_end .= $add_ing;
													}else{
														$ing_end .= "(".$add_ing.")";
													}
												}
											}
										}
										if($ing_end != ""){
											$ing_end = ", ".$ing_end;
										}

										$ing_str .= $ing_end;
										if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
											$product->ingredients = substr($ing_str,2);
										else
											$product->ingredients = $product->ingredients;
									}
									if($product->ingredients == '')
										$product->ingredients = '--';
									$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->ingredients.'</td>';*/

									$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
									$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);
									if(!empty($k_allergence)){
										$allrg_str = '';
										//Looping product allergence to create allergence string..
										foreach ($k_allergence as $k_allerg){
											$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);
											if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
												$a1 = '';
												if(!empty($k_sub_allergence)){
													$a1 .= ' (';
													foreach ($k_sub_allergence as $k_sub_allerg){
														if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
															$a1 .=  $k_sub_allerg->sub_ka_name.', ';
														}
													}
													$a1 = rtrim($a1,', ');
													$a1 .= ')';
													$a1 = str_replace('()', '', $a1);
												}
												$allrg_str .= $a1;
											}
										}
										if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
											if(strpos($allrg_str,'Melk') !== false && strpos($allrg_str,'Lactose') !== false){
												$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
												$allrg_str = str_replace(',  Lactose', '', $allrg_str);
											}
											$product->allergence = substr($allrg_str,2);
										}
										else
											$product->allergence = $product->allergence;
									}
									if($product->allergence == '')
										$product->allergence = '--';
									$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

									/*$k_traces = $this->Mproducts->get_product_traces($p_id);
									if(!empty($k_traces)){
										$tra_str = '';
										//Looping product trashes to create trashes string..
										foreach ($k_traces as $k_trace){
											$tra_str .= ', '.stripslashes($k_trace->prefix).' '.stripslashes($k_trace->kt_name);
										}
										if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
											$product->traces_of = substr($tra_str,2);
										else
											$product->traces_of = $product->traces_of;
									}
									if($product->traces_of == '')
										$product->traces_of = '--';
									$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->traces_of.'</td>';*/

									$row_html .= '</tr>';

								}
							} else {
								$row_html .= '<tr><td style="padding-left: 10px; vertical-align: top;">--</td><td style=" vertical-align: top;">--</td></tr>';
							}
							$row_html .= '</tbody></table><br/><br/>';
						}
					}

					//direct products of this category..
					$products = $this->Mproducts->get_products($cat->id, -1);

					//print_r($products);

					if(!empty($products)){

						$row_html .= '<table width="100%" cellpadding="3" style="font-size: 11pt; font-family: arial;">';

						$row_html .= '<thead><tr><th colspan="4" style="padding-left: 10px; background-color: #ccc; font-size: 13pt !important; text-align: left;">'.$cat_name.'</th></tr></thead><tbody>';

						// $row_html .= '<tr style="text-align: left;"><td style="width: 150px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('INGREDIENTS').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 230px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
						//$row_html .= '<tr style="text-align: left;"><td style="width: 250px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td><td style="width: 310px; padding: 10px 10px 20px 0; ">'._('TRACES OF').'</td></tr>';
						$row_html .= '<tr style="text-align: left;"><td style="width: 370px; padding: 10px 0px 20px 10px;">'._('PRODUCT').'</td><td style="width: 500px; padding: 10px 10px 20px 0; ">'._('ALLERGENCE').'</td></tr>';

						//Looping products using product ids..
						foreach($products as $product){

							$p_id = $product->id;

							$row_html .= '<tr>';

							$product = $this->Mproducts->get_product_information($p_id);

							$product = $product[0];

							$row_html .= '<td style="padding-left: 10px; vertical-align: top;">'.stripslashes($product->proname).'</td>';

							/*$k_ingredients = $this->Mproducts->get_product_ingredients_dist($p_id);
							if(!empty($k_ingredients)){
								$ing_str = '';
								//Looping product ingredients to create ingredient string..
								foreach ($k_ingredients as $ingredients){
									if($ingredients->ki_name == ')' ){
										$ing_str = substr($ing_str, 0, -2);
										$ing_str .= ' ';
									}
									if($ingredients->ki_name == '(' ){
										$ing_str = substr($ing_str, 0, -2);
										$ing_str .= ' ';
									}

									if($ingredients->ki_id != 0){
										if($ingredients->prefix == ''){
											$ing_str .= stripslashes($ingredients->ki_name).', ';
										}else{
											$ing_str .= stripslashes($ingredients->ki_name).'('.$ingredients->prefix.')'.', ';
										}
									}else if($ingredients->ki_name == ')'){

										$ing_str .= $ingredients->ki_name.', ';

									}else if($ingredients->ki_name == '('){

										$ing_str .= $ingredients->ki_name.' ';

									}else{
										if($ingredients->prefix == ''){
											$ing_str .= stripslashes($ingredients->ki_name).', ';
										}else{
											$ing_str .= stripslashes($ingredients->ki_name).'('.$ingredients->prefix.')'.', ';
										}
									}
								}
								$ing_str = substr($ing_str, 0, -2);

								if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
									$product->ingredients = substr($ing_str,2);
								else
									$product->ingredients = $product->ingredients;
							}
							if($product->ingredients == '')
								$product->ingredients = '--';
							$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->ingredients.'</td>';*/


							$k_allergence = $this->Mproducts->get_product_allergence_dist($p_id);
							$k_sub_allergence = $this->Mproducts->get_product_sub_allergence_dist($p_id);
							if(!empty($k_allergence)){
								$allrg_str = '';
								//Looping product allergence to create allergence string..
								foreach ($k_allergence as $k_allerg){
									$allrg_str .= ', '.stripslashes($k_allerg->prefix).' '.stripslashes($k_allerg->ka_name);
									if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
										$a1 = '';
										if(!empty($k_sub_allergence)){
											$a1 .= ' (';
											foreach ($k_sub_allergence as $k_sub_allerg){
												if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
													$a1 .=  $k_sub_allerg->sub_ka_name.', ';
												}
											}
											$a1 = rtrim($a1,', ');
											$a1 .= ')';
											$a1 = str_replace('()', '', $a1);
										}
										$allrg_str .= $a1;
									}
								}
								if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){
									if(strpos($allrg_str,'Melk') !== false && strpos($allrg_str,'Lactose') !== false){
										$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
										$allrg_str = str_replace(',  Lactose', '', $allrg_str);
									}
									$product->allergence = substr($allrg_str,2);
								}
								else
									$product->allergence = $product->allergenc;
							}
							if($product->allergence == '')
								$product->allergence = '--';
							$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->allergence.'</td>';

							/*$k_traces = $this->Mproducts->get_product_traces($p_id);
							if(!empty($k_traces)){
								$tra_str = '';
								//Looping product trashes to create trashes string..
								foreach ($k_traces as $k_trace){
									$tra_str .= ', '.stripslashes($k_trace->prefix).' '.stripslashes($k_trace->kt_name);
								}
								if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium')
									$product->traces_of = substr($tra_str,2);
								else
									$product->traces_of = $product->traces_of;
							}
							if($product->traces_of == '')
								$product->traces_of = '--';
							$row_html .= '<td style=" vertical-align: top;font-size: 9pt;">'.$product->traces_of.'</td>';*/


							$row_html .= '</tr>';

						}
						$row_html .= '</tbody></table><br/><br/>';
					}

				}
			}
		}

		//return $row_html;

		require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');

		//print_r($pro_ingr);

		//$this->load->library('mpdf');

		$report_name = 'report'.time().'.pdf';

		$mpdf=new mPDF('c');

		$mpdf->WriteHTML($row_html);

		//$mpdf->Output('Products.pdf', 'D');

		//}
	}

	function print_product_download_link($filename){

        require_once(dirname(__FILE__).'/../../../assets/MPDF57/mpdf.php');

		//print_r($pro_ingr);

		//$this->load->library('mpdf');

		$report_name = 'report'.time().'.pdf';

		$mpdf=new mPDF('c');

		$mpdf->WriteHTML($row_html);

		$mpdf->Output($filename,'D');

		/*header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');*/
	}


	function apply_group_settings($cat_id = null, $subcat_id = null, $type = 'per_unit'){
		$this->load->model('Mproducts');
		$this->load->model('Mgroups_products');

		$all_group_data = $this->input->post('all_group_data');

		$products = $this->Mproducts->get_products_for_changegroup($cat_id, $subcat_id, $type);

		$result = FALSE;
		foreach ($products as $product){
			$this->Mgroups_products->delete_selected_group($product->id);
		}

		foreach ($products as $product){
			foreach ($all_group_data as $group_data){
				$result = $this->Mgroups_products->update_selected_group($group_data, $product->id);
			}
		}


		echo $result;
	}
	function close_notification($noti_id = NULL){

		if($noti_id != NULL){
			$insert_array = array(
					'company_id' => $this->company->id,
					'notification_id' => $noti_id,
					'closing_date' => date("Y-m-d H:i:s")
			);
			$this->load->model("mnotify");
			$result = $this->mnotify->insert_into_close_notification($insert_array);
		}
		echo $result;
	}


	function empty_ingredients($pro_id = 0){
		$this->load->model('Morders');

		// Code for uploading product sheet
		if($this->input->post('upload') && $this->input->post('pro_id') && is_numeric($this->input->post('pro_id')) )
		{

			$config['upload_path'] = dirname(__FILE__).'/../../../assets/cp/fdd_pdf/';
			$config['allowed_types'] = 'pdf';
			$config['file_name'] = clean_pdf(pathinfo($_FILES['sheet']['name'], PATHINFO_FILENAME));
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('sheet'))
			{
				$data['sheet_error'] = array('error' => $this->upload->display_errors());
			}
			else
			{
				$uploaded_data = $this->upload->data();

				// Uploading column for this product, have done it here as we havent call the model mproducts instead used morders
				$product = $this->input->post('pro_id');

				$this->db->where('product_id',$product);
				$this->db->where('company_id',$this->company_id);
				$this->db->update('products_pending',array('prosheet_pws' => date("Y-m-d H:i:s").'##'.$uploaded_data['file_name']));

				$this->db->select('refused');
				$is_refused = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$product))->row_array();

				if(!empty($is_refused)){
					if($is_refused['refused'] == 1){
						$this->db->where('obs_pro_id',$product);
						$this->db->update('contacted_via_mail',array('status'=> date('Y-m-d H:i:s').'#new_sheet','refused' => 0));
					}
				}
				$this->db->select("proname");
				$proname = $this->db->get_where("products",array("id"=>$product))->row_array();
				// Sending mail to Tamara with attachment
				//$message = _('A new product sheet has been sent by:').'<br/>'._('Company ID: ').'<b>'.$this->company->id."</b><br/>"._('Company Name: ').'<b>'.$this->company->company_name."<b>";
				$this->notification($this->input->post('pro_id'));
				$message = "Een nieuw productfiche werd verzonden door:<br/>Winkel ID:&nbsp;<b>".$this->company->id."</b><br/>Bedrijfsnaam:&nbsp;<b>".$this->company->company_name."</b><br/><br/>Opgegeven productnaam in CP:&nbsp;&nbsp;<b>".$proname['proname']."</b>";
				send_email($this->config->item('fdd_checker_email'), $this->config->item('no_reply_email'), _("New Product Sheet from Admin"), $message, $this->config->item('site_admin_name'), dirname(__FILE__).'/../../../assets/cp/fdd_pdf/'.$uploaded_data['file_name']);

			}
		}
		// --------------------------------

		if($pro_id == 0 || $pro_id == -1){
			$data['products'] = $this->Morders->get_empty_ingredient_products($this->company->id);
		}else{
			$data['products'] = $this->Morders->get_empty_ingredient_products($this->company->id,$pro_id);
		}
		
		$data['fdd_products'] = $this->M_fooddesk->product_name('p_name_dch,p_s_id,p_id,s_name');
		
		// if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){

			$data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
		// }
		$data['content'] = 'cp/empty_ingredients_products';
		$this->load->view('cp/cp_view', $data);

	}

	function show_refuse_remark(){
		$pro_id = $this->input->post('obs_pro_id');
		if($pro_id){
			$this->db->select('remark_refused');
			$remark_arr = $this->db->get_where('contacted_via_mail',array('obs_pro_id'=>$pro_id))->row_array();
			echo $remark_arr['remark_refused'];
		}
		else
			echo '0';
	}

	function attach_ingredients(){
		$pro_id = $this->input->post('pro_id');
		$ingredients = $this->input->post('ingredients');
		$allergence = $this->input->post('aller');
		$traces = $this->input->post('traces');

		$ingredients = trim($ingredients);
		$allergence = trim($allergence);
		$traces = trim($traces);

		$ing_arr = explode(",",$ingredients);
		$i = 1;
		foreach ($ing_arr as $ing){
			$insert_array = array(
				'product_id' => $pro_id,
				'kp_id'=> '0',
				'ki_id'=> '0',
				'ki_name'=>trim($ing),
				'display_order'=>$i++
			);
			$this->db->insert('products_ingredients',$insert_array);
		}


		$all_arr = explode(",",$allergence);
		$i = 1;
		foreach ($all_arr as $ally){
			$insert_array = array(
					'product_id' => $pro_id,
					'kp_id'=> '0',
					'ka_id'=> '0',
					'ka_name'=>trim($ally),
					'display_order'=>$i++
			);
			$this->db->insert('products_allergence',$insert_array);
		}

		$trace_arr = explode(",",$traces);
		$i = 1;
		foreach ($trace_arr as $tr){
			$insert_array = array(
					'product_id' => $pro_id,
					'kp_id'=> '0',
					'kt_id'=> '0',
					'kt_name'=>trim($tr),
					'display_order'=>$i++
			);
			$this->db->insert('products_traces',$insert_array);
		}
		$updt = array(
			'ingredients'=>'',
			'traces_of'=>'',
			'allergence'=>''
		);

		if($this->db->update('products',$updt)){
			echo TRUE;
		}else{
			echo TRUE;
		}
	}

// 	function add_empty_ingredient_product(){
// 		$this->load->model('Mgroups');

// 		$data['product_information']=array();

// 		//this subcategories are corressponding to the category posted by the form product_add in products.php//
// 		if($this->input->post('categories_id'))
// 			$data['subcategory_data'] = $this->Msubcategories->get_sub_category($this->input->post('categories_id'));
// 		else
// 			$data['subcategory_data'] = array();



// 		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

// 		$this->load->model('Msubcategories');
// 		$this->load->model('Mproducts');

// 		// Load these things only if Ingredients system is not enabled
// 		if($this->company->k_assoc && $this->company->ingredient_system){

// 		}else{
// 			$this->load->model('Mgroups');
// 			$this->load->model('Mproduct_discount');
// 			$this->load->model('Mgroups_products');
// 			$this->load->model('Mpickup_delivery_timings');

// 			$data['general_settings'] = $general_settings = $this->Mgeneral_settings->get_general_settings();

// 			//for product availability
// 			$pdt = array();
// 			$pickup_delivery_timings=$this->Mpickup_delivery_timings->get_pickup_delivery_timings(array('company_id'=>$this->company_id));
// 			if(!empty($pickup_delivery_timings))
// 			foreach($pickup_delivery_timings as $pd)
// 			if($pd->pickup1 == 'CLOSED' || $pd->delivery1 == 'CLOSED')
// 				$pdt[] = $pd->day_id;

// 			$data['pickup_delivery_timings']=$pdt;

// 			$products_per_group=array();
// 			$products_per_group_wt=array();
// 			$products_per_group_person = array();
// 		}
// 		$data['general_settings'] = $general_settings = $this->Mgeneral_settings->get_general_settings();

// 		$data['producers']=$this->M_fooddesk->get_supplier_name();
// 		$data['suppliers']=$this->M_fooddesk->get_real_supplier_name();
// 		
// 		$data['category_data']=$this->Mcategories->get_categories();
// 		//$data['subcategory_data']=$this->Msubcategories->get_sub_category();
// 		// Load seperate product add/edit view file if Ingredients system is enabled
// 		if($this->company->ingredient_system){
// 			$data['content'] = 'cp/products_addedit_ing';
// 		}else{
// 			$data['groups']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>0));
// 			$data['groups_wt']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>1));
// 			$data['groups_person']=$this->Mgroups->get_groups(array('company_id'=>$this->company_id,'type'=>2));
// 			$data['groups_products']=NULL;//this variable is used for the updation purpose so it has to set null//
// 			$data['groups_products_wt']=NULL;
// 			$data['groups_products_person']=NULL;

// 			//--this is to check whether to show payment option--//

// 			if($this->company_role == 'master' && $general_settings['0']->online_payment == 1){
// 				$data['show_payment_setting'] = 'true';
// 				//$data['company_role'] = 'master';
// 			}elseif($this->company_role == 'super'){
// 				//$data['company_role'] = 'super';
// 				$params = array('parent_id' => $this->company_id,'role' => 'sub' );
// 				$payment_option = $this->Mcompany->payment_option($params);
// 				if($payment_option){
// 					$data['show_payment_setting'] = 'true';
// 				}else{
// 					$data['show_payment_setting'] = 'false';
// 				}
// 			}
// 			//-------------------------------------------------------//

// 			$data['content'] = 'cp/products_addedit';
// 		}
// 		$data['fdd_credits'] = $this->Mproducts->fdd_credits();
// 		$this->load->view('cp/cp_view',$data);


// 	}

	function rename_product(){
		$pro_id = $this->input->post('product_id');
		$field_val = trim(addslashes($this->input->post('field_val')));
		$pro_field = $this->input->post('field');

		if(($pro_field == 'price_per_unit') || ($pro_field == 'price_per_person')){
			$field_val = $this->number2db($field_val);
		}
		elseif($pro_field == 'price_weight'){
			$field_val = $this->number2db($field_val/1000);
		}

		$this->load->model('Mproducts');
		if($this->Mproducts->rename_product($pro_id, array($pro_field=>$field_val))){
			echo $pro_id;
		}else{
			echo '0';
		}
	}

	function custom_pending($pro_id = 0){
		if($pro_id == 0 || $pro_id == -1){
			$data['products'] = $this->M_fooddesk->get_custom_pending($this->company->id);
		}else{
			$data['products'] = $this->M_fooddesk->get_custom_pending($this->company->id,$pro_id);
		}

		if($this->session->userdata('menu_type') == 'fdd_light' || $this->session->userdata('menu_type') == 'fdd_pro' || $this->session->userdata('menu_type') == 'fdd_premium'){

			$data['custom_pending_product_count'] = $this->Morders->get_custom_pending_products_count($this->company->id);
		}
		$data['content'] = 'cp/custom_pending_products';
		$this->load->view('cp/cp_view', $data);
	}

	function add_supplier($is_supplier = 0){
		if($is_supplier == 1){
			$pid = $this->input->post('obs_pro_id');
			$supplier_name = $this->input->post('new_supplier');

			$supplier_id = 0;
			$supplier_name = trim($supplier_name);
			if($supplier_name != ''){
				
				$res = $this->M_fooddesk->get_suppliers_data(array('LOWER(s_name)' => addslashes(mb_strtolower($supplier_name,'UTF-8'))));
				if(!empty($res)){
					$supplier_id = $res[0]['s_id'];
				}else{
					$insrt_array = array(
							's_name'=>addslashes($supplier_name),
							's_username' => str_replace(' ', '_', $supplier_name),
							's_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
							's_date_added' => date('Y-m-d h:i:s')
					);
					$this->fdb->insert('suppliers', $insrt_array);
					$supplier_id = $this->fdb->insert_id();
				}
				

				$this->db->where('id',$pid);
				$this->db->update('products',array('fdd_producer_id'=>$supplier_id));
			}

			echo $supplier_id;
		}
		elseif($is_supplier == 2){
			$pid = $this->input->post('obs_pro_id');
			$real_supplier_name = $this->input->post('new_real_supplier');

			$real_supplier_id = 0;
	 		$real_supplier_name = trim($real_supplier_name);
	 		if($real_supplier_name != ''){
				
				$res1 = $this->M_fooddesk->get_real_suppliers_data(array('LOWER(rs_name)' => addslashes(mb_strtolower($real_supplier_name,'UTF-8'))));
	 			if(!empty($res1)){
	 				$real_supplier_id = $res1[0]['rs_id'];
	 			}else{
	 				$insrt_array = array(
	 					'rs_name'=>addslashes($real_supplier_name),
	 					'rs_username' => str_replace(' ', '_', $real_supplier_name),
	 					'rs_password' => substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6),
	 					'rs_date_added' => date('Y-m-d h:i:s')
	 				);
	 				$this->fdb->insert('real_suppliers', $insrt_array);
	 				$real_supplier_id = $this->fdb->insert_id();
	 			}
	 			

	 			$this->db->where('id',$pid);
	 			$this->db->update('products',array('fdd_supplier_id'=>$real_supplier_id));
	 		}

			echo $real_supplier_id;
		}
	}

	function assign_category(){
		$this->load->model('Mproducts');
		$this->load->model('Msubcategories');
		if($this->input->post('checkbox')){
			if($this->input->post('cat-assign-button-up')){
				$cat_id = $this->input->post('cat-assign-up');
				$subcat_id = $this->input->post('subcat-assign-up');
			}
			elseif($this->input->post('cat-assign-button-down')){
				$cat_id = $this->input->post('cat-assign-down');
				$subcat_id = $this->input->post('subcat-assign-down');
			}
			$proid_arr = $this->input->post('checkbox');
			if($cat_id != -1){
				foreach($proid_arr as $pro_id){
					$result = $this->Mproducts->update_product_details($pro_id,array('categories_id'=>$cat_id,'subcategories_id'=>$subcat_id));
				}
				if($result){
					$this->session->set_flashdata('cat_update',_('Category and Subcategory Updated Successfully'));
				}
			}
			$this->session->set_userdata('action', 'category_json');

			if(count($this->uri->segments) == 5){
				redirect('cp/cdashboard/assign_category/category_id/'.end($this->uri->segments));
			}
			else{
				redirect('cp/cdashboard/assign_category/');
			}
		}
		else {
			$url_variable = $this->uri->uri_to_assoc(4);

			$data['category_data'] = $this->Mcategories->get_categories();
			if(array_key_exists('category_id', $url_variable)){
				$data['cat_id']=$url_variable['category_id'];
				$this->load->model('Msubcategories');
				$data['subcategory_data']=$this->Msubcategories->get_sub_category($url_variable['category_id']);

				$products2 = array();

				$data['sub_cat_id']='-1';
				$products1 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],'-1');//getting product of related to category

				$this->db->select('id');
				$subcategory_data = $this->db->get_where('subcategories',array('categories_id'=>$url_variable['category_id']))->result_array();

				$id = array();
				foreach($subcategory_data as $var) {
					$id[] = $var['id'];
				}
				if(!empty($id)){
					$products2 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],$id);//getting product of related to category
				}
				$data['products'] = array_merge($products1,$products2);

			} else {
				$data['products'] = $this->Mproducts->get_products_to_assign();
			}
	 			$data['category_data']=$this->Mcategories->get_categories();

	 			$this->db->select(array( 'subcategories.id','subcategories.subname','subcategories.categories_id'));
	 			$this->db->join('categories','subcategories.categories_id=categories.id');
	 			$this->db->where('categories.company_id',$this->company_id);
	 			$data['subcategory_data'] = $this->db->get('subcategories')->result_array();

	 			$data['content'] = 'cp/assign_cat_subcat';
				$this->load->view('cp/cp_view', $data);
			}
	}
	function login_details(){
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();
		if($this->input->post("approve")){
			$sender = $this->company->email;
			$subject = _('Mail for approval');
			$msg = _('Hello,')."<br/><br/>";
			$msg .= _('We approve to share login details to login and retrieve the sheets.')."<br/><br/>";
			$msg .= _("Regards,")."<br/><br/>";
			$msg .= $this->company->company_name;
			$msg = nl2br($msg);
			send_email($this->config->item('site_admin_email'), $sender ,$subject,$msg,NULL,NULL,NULL,'company','site_admin','mail_for_approval');
			$this->db->where("id",$this->company_id);
			$this->db->update('company',array('is_approve_login_details'=>"1"));
			echo "sent";die;
		}
		if($this->input->post("add_login_details") == "add_details"){
			$prod_arr = $this->input->post("att_prod");

			if(!empty($prod_arr)){
				$success = $this->M_fooddesk->add_login_details();
				if($success)
					$this->session->set_flashdata('login_details','login_details_success');
			}
			redirect("cp/cdashboard/login_details");
		}
		$result = $this->M_fooddesk->get_login_details('',array('company_id'=>$this->company_id));

		$result_appr = $this->db->get_where('company',array('id'=>$this->company_id,'is_approve_login_details'=>"1"))->result_array();
		if(!empty($result)){
			$data['is_approved'] = 1;
			$data['login'] = $result;
		}
		elseif(!empty($result_appr))
			$data['is_approved'] = 1;
		else
			$data['is_approved'] = 0;
		$data['producers'] = $this->M_fooddesk->get_supplier_name(array("s_id","s_name"));
		$data['suppliers'] = $this->M_fooddesk->get_real_supplier_name(array("rs_id","rs_name"));
		$data['content'] = 'cp/login_details_view';
		$this->load->view('cp/cp_view', $data);
	}

	function product_recipe($ingre_id = 0){
		if($ingre_id != 0){
			$data['recipe_products'] = $this->M_fooddesk->get_ingre_product($ingre_id,1);

			$this->db->select('ki_name');
			$result = $this->db->get_where('products_ingredients',array('ki_id'=>$ingre_id))->result_array();
			$data['ingre_name'] = $result[0]['ki_name'];
		}

		$data['content'] = 'cp/product_recipe';
		$this->load->view('cp/cp_view', $data);
	}

	function semi_product_recipe($ingre_id = 0,$type = 0){
		if($ingre_id != 0){
			$data['semi_products'] = $this->M_fooddesk->get_ingre_product($ingre_id,$type);

			$this->db->select('ki_name');
			$result = $this->db->get_where('products_ingredients',array('ki_id'=>$ingre_id))->result_array();
			$data['ingre_name'] = $result[0]['ki_name'];
		}

		$data['content'] = 'cp/product_recipe';
		$this->load->view('cp/cp_view', $data);
	}

	function get_recipe_AjaxIngre($type = 0){
		if($type){
			$search_str = $this->input->post('search_str');
			$result_array = array();

			$srch_arr = explode(' ', $search_str);
			foreach ($srch_arr as $srch_val){
				if($srch_val != ''){
					$result_array1 = $this->M_fooddesk->get_searched_recipe_ingre($srch_val, $type);
					if(!empty($result_array)){
						$result_array = array_values(array_uintersect($result_array, $result_array1, function($val1, $val2){ return strcmp($val1['ki_id'], $val2['ki_id']); }));
					}else{
						$result_array = $result_array1;
					}
				}
			}
			$result_array = array_values(array_map("unserialize",array_unique(array_map("serialize", $result_array))));
			echo json_encode($result_array);
		}
	}

	function send_remark_by_mail(){
		$sender		= $this->input->post('sender');
		$subject	= $this->input->post('subject');
		$msg		= $this->input->post('message');
		$prod_id	= $this->input->post('prod_id');
		if($sender && $subject && $msg){
			$privateKey = 'o!s$_7e(g5xx_p(thsj*$y&27qxgfl(ifzn!a63_25*-s)*sv8';
			$url = base_url().'cp/cdashboard/products_addedit/product_id/'.$prod_id;
			$userId = $this->company_id;
			$url_token = $this->createUrl($privateKey, $url, $userId);

			$msg .= "<br/>----------------------";
			$msg .= "<br/><strong>"._('Company Name')."</strong>:&nbsp;".$this->company->company_name;
			if($prod_id)
				$msg .= "<br/><br/><strong>"._('Product Link')."</strong>:&nbsp;<a href=\"".$url_token."\">".base_url()."cp/cdashboard/products_addedit/product_id/".$prod_id."</a>";
			$msg = nl2br($msg);
			send_email($this->config->item('site_admin_email'), $sender ,$subject,$msg,NULL,NULL,NULL,'company','site_admin','remark_by_admin');
			echo 'sent';
		}
		else
			echo false;
	}

	private function createToken($privateKey, $url, $userId){
		return hash('sha256', $privateKey.$url.$userId);
	}

	private function createUrl($privateKey, $url, $userId){
		$hash = $this->createToken($privateKey, $url, $userId);
		$autoLoginUrl = http_build_query(array(
        		'name' => $userId,
        		'token' => $hash
    	));
    return $url.'?'.$autoLoginUrl;
	}

	function login_at_fdd( $fdd_pro_id = 0){
		if($fdd_pro_id){
			$ran_str = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
			$unique_arr = array(
				'unique_str' => $ran_str,
				'datetime' => date("Y-m-d H:i:s")
			);
			
			$this->fdb->insert('login_via_obs',$unique_arr);
			echo $ran_str;
		}
		else{
			echo false;
		}
	}

	private function product_validate($userId){
		$userdata = $this->db->get_where( 'company', array('id'=> $userId))->row_array();
		$userName = $userdata['username'];
		$params = array('company_id'=>$userdata['id']);
		$general_settings = $this->Mgeneral_settings->get_general_settings($params);

		if($general_settings['0']->language_id == 1){
			setcookie('locale','en_US',time()+365*24*60*60,'/');
		}elseif($general_settings['0']->language_id == 2){
			setcookie('locale','nl_NL',time()+365*24*60*60,'/');
		}else{
			setcookie('locale','fr_FR',time()+365*24*60*60,'/');
		}

		$_SESSION['cp_username'] = $userName;
		$res_sforum = $this->db->get_where('sforum_users',array('username'=>$userName))->result_array();

		if(is_array($res_sforum) && !empty($res_sforum)){
			// For forum login merging..
			$_SESSION['forum_user_logged_in'] = true;
			$_SESSION['sforum_logged_in'] = true;
			$_SESSION['sforum_user_id'] = $res_sforum[0]['id'];
			$_SESSION['sforum_user_role'] = $res_sforum[0]['role'];
			$_SESSION['sforum_user_username'] = $userName;
		}
		else{
			$param_sforum = array('username' => $userName, 'role' => 'user');
			$this->db->insert('sforum_users',$param_sforum);
			$sfroum_id = $this->db->insert_id();

			$_SESSION['forum_user_logged_in'] = true;
			$_SESSION['sforum_logged_in'] = true;
			$_SESSION['sforum_user_id'] = $sfroum_id;
			$_SESSION['sforum_user_role'] = 'user';
			$_SESSION['sforum_user_username'] = $userName;
		}

		$data = array(
				'cp_user_id' => $userdata['id'],
				'cp_username' => $userName,
				'cp_user_role' => $userdata['role'],
				'cp_user_parent_id' => $userdata['parent_id'],
				'cp_is_logged_in' => true,
				'cp_website' => '',
				'login_via' => 'mcp'
		);

		if($userdata['ac_type_id'] == 3){
			$this->load->model('MFtp_settings');
			$ftp_settings = $this->MFtp_settings->get_ftp_settings($params);
			if(!empty($ftp_settings) && isset($ftp_settings['0']->obs_shop) && $ftp_settings['0']->obs_shop != ''){
				$data['cp_website'] = $ftp_settings['0']->obs_shop;
			}
		}else{
			if($userdata['existing_order_page'] != ''){
				$data['cp_website'] = $userdata['existing_order_page'];
			}else{
				$this->load->model('Mcompany_type');
				$company_type_ids = explode("#",$userdata['type_id']);
				$company_type = $this->Mcompany_type->get_company_type(array('id' => $company_type_ids[0]));
				if(!empty($company_type)){
					$data['cp_website'] = $this->config->item('portal_url').$company_type['0']->slug.'/'.$userdata['company_slug'];
				}
			}
		}
		$this->session->set_userdata($data);
		redirect(current_url());
	}

	/*function product_recipe($fdd_pro_id = 0){
		$this->load->model('Mproducts');
		if($fdd_pro_id == 0){
			$data['recipe'] = $this->Mproducts->get_recipe_data();
		}
		else{
			$data['recipe_products'] = $this->Mproducts->get_recipe_product($fdd_pro_id);

			
			$this->fdb->select('p_name_dch');
			$fdd_result = $this->fdb->get_where('products',array('p_id'=>$fdd_pro_id))->result_array();
			$data['fdd_pro_name'] = $fdd_result[0]['p_name_dch'];
		}

		$data['content'] = 'cp/product_recipe';
		$this->load->view('cp/cp_view', $data);
	}*/

	private function join_pdf_name_from_fdd($products){
		
		if(!empty($products))
			foreach($products as $i => $product){
				if($product->direct_kcp == 1 && $product->direct_kcp_id != 0){
					$this->fdb->select('products.data_sheet');
					$this->fdb->where(array('p_id'=> $product->direct_kcp_id));
					$result = $this->fdb->get('products')->result();
					if(!empty($result))
						$products[$i]->pdf_name = $result[0]->data_sheet;
				}
			}
		
		return $products;
	}

	private function is_contains_semi($products){
		if(!empty($products))
		foreach($products as $i => $product){
			if($product->direct_kcp == 0){
				$this->db->select('id');
				$this->db->where(array('obs_pro_id'=> $product->id,'semi_product_id !='=>0));
				$result = $this->db->get('fdd_pro_quantity')->result();
				if(!empty($result))
					$products[$i]->can_move = 0;
				else
					$products[$i]->can_move = 1;
			}
		}
		return $products;
	}

	function producer_consumer_box($pro_id = 0){
		if($pro_id != 0){
			$data = array();

			$data['producers_list'] = $this->M_fooddesk->get_supplier_name('s_id,s_name');
			$data['supplier_list'] = $this->M_fooddesk->get_real_supplier_name('rs_id,rs_name');

			
			$this->db->select('proname,fdd_producer_id,fdd_supplier_id,fdd_prod_art_num,fdd_supp_art_num');
			$data['filtered_list'] = $this->db->get_where('products',array('id'=>$pro_id))->row_array();
			$data['pro_id'] = $pro_id;
			$this->load->view('cp/empty_ingredients_art_view', $data);
		}
	}

	function prod_supp_art_no(){
		$this->load->model('Morders');

		$pro_id = $this->input->post('obs_pro_id');
		$producer_id = $this->input->post('producer_id');
		$fdd_prod_art_nbr = $this->input->post('fdd_prod_art_nbr');
		$supplier_id = $this->input->post('supplier_id');
		$fdd_supp_art_nbr = $this->input->post('fdd_supp_art_nbr');
		$status = 'error';
		if($pro_id != ''){
			$prod_supp=array(
					'pro_id'=>$pro_id,
					'producer_id'=>$producer_id,
					'fdd_prod_art_nbr'=>$fdd_prod_art_nbr,
					'supplier_id'=>$supplier_id,
					'fdd_supp_art_nbr'=>$fdd_supp_art_nbr
			);
			$status = $this->Morders->insert_fdd_prod_supp_art_nbr($prod_supp);
		}
		echo $status;
	}

	function get_sub_category_product(){

		$cat_id = $this->input->post('cat_id');
		$subcat_id = $this->input->post('subcat_id');
		$cu_pid = $this->input->post('cu_pid');

		$subcat_array = array();
		if($subcat_id == -1){
			$this->db->select('id,subname');
			$this->db->where('categories_id',$cat_id);
			$this->db->order_by('suborder_display','ASC');
			$subcat = $this->db->get('subcategories')->result();

			if(!empty($subcat)){
				foreach ($subcat as $val){
					$subcat_array[$val->id] = $val->subname;
				}
			}
		}

		$this->db->select('id,proname');
		if($cu_pid != 0)
			$this->db->where('id !=',$cu_pid);
		$this->db->where(array('company_id'=>$this->company_id,'categories_id'=>$cat_id,'subcategories_id'=>$subcat_id));
		$this->db->order_by('pro_display','ASC');
		$product = $this->db->get('products')->result();

		$product_array = array();
		if(!empty($product)){
			foreach ($product as $val){
				$product_array[$val->id] = $val->proname;
			}
		}

		echo json_encode(array('subcat'=>$subcat_array,'product'=>$product_array));
	}

	// Code by mohd jafar

	function clean($string) {
		$string = strtolower(str_replace(' ', '-', $string)); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}

	function upgrade_files()
	{
		$company_id = $this->company_id;
		$response = array();
		$responser = array();
		$seo_response = array();
		$search_products = array();
		$products = array();
		$pickup_url_key = array();
		$delivery_url_key = array();
		$result1 = $result2 = array();

		if($this->input->post('action') == 'category_json' || $this->input->post('action') == 'general_setting_json')
		{
			if( $this->company_role == 'master' || $this->company_role == 'super' ) {
				$responser = $this->get_shop_content_json_new ( $company_id );
				$seo_pickup_data = $responser['pickup'];

				foreach($seo_pickup_data as $key=>$seo_pickup)
				{
					foreach($seo_pickup as $k=>$seo_pick)
					{
						$seo_pickup_cat_product =  $seo_pick->product;
						$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
						$category_seo_key = $this->clean($seo_pick->name);
						$seo_response[$category_seo_key] = $seo_pick;
						$seo_response[$category_seo_key]->slug = $category_seo_key;
						$seo_response[$category_seo_key]->product='';
						$seo_response[$category_seo_key]->sub_category='';

						if(isset($seo_pickup_cat_product))
						{
							foreach($seo_pickup_cat_product as $k=>$seo_pickup_product)
							{
								if(isset($seo_pickup_product))
								{
									if(!empty($seo_pickup_product->proname))
									{
										$category_seo_product_key = $this->clean($seo_pickup_product->proname);

										if (in_array($category_seo_product_key, $pickup_url_key))
										{
											$i=1;
											while(in_array($category_seo_product_key, $pickup_url_key))
											{
												$category_seo_product_key = $category_seo_product_key.'-'.$i;
												if($i > 1)
												{
													$tokens = explode('-', $category_seo_product_key);
													array_pop($tokens);
													array_pop($tokens);
													$category_seo_product_key = implode('-', $tokens);
													$category_seo_product_key = $category_seo_product_key.'-'.$i;
												}
												$i++;
											}
											$pickup_url_key[] = $category_seo_product_key;
										}
										else
										{
											$pickup_url_key[] = $category_seo_product_key;
										}

										if($category_seo_product_key && $seo_pickup_product)
											$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
										$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
									}
								}
							}
						}

						if(isset($seo_pickup_sub_cat_seo))
						{
							foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat)
							{
								if(isset($seo_pickup_sub_cat))
								{
									$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
									$category_seo_sub_cat_key = $this->clean($seo_pickup_sub_cat->subname);

									if($category_seo_sub_cat_key && $seo_pickup_sub_cat)
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
									$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
									$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';

								}

								if(isset($seo_pickup_subcat_product))
								{
									foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product)
									{
										if(isset($seo_pickup_sub_product))
										{
											if(!empty($seo_pickup_sub_product->proname))
											{
												$category_seo_sub_cat_product_key = $this->clean($seo_pickup_sub_product->proname);

												if (in_array($category_seo_sub_cat_product_key, $pickup_url_key))
												{
													$i=1;
													while(in_array($category_seo_sub_cat_product_key, $pickup_url_key))
													{
														$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
														if($i > 1)
														{
															$tokens = explode('-', $category_seo_sub_cat_product_key);
															array_pop($tokens);
															array_pop($tokens);
															$category_seo_sub_cat_product_key = implode('-', $tokens);
															$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
														}
														$i++;
													}
													$pickup_url_key[] = $category_seo_sub_cat_product_key;
												}
												else
												{
													$pickup_url_key[] = $category_seo_sub_cat_product_key;
												}

												if($category_seo_sub_cat_product_key && $seo_pickup_sub_product)
													$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'pickup';
												$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
											}
										}
									}
								}

							}
						}
					}
				}

				$response['pickup']['category'] = $seo_response;


			$fp = fopen(dirname(__FILE__).'/../../../online-bestellen/json/pickup_product_cat_'.$company_id.'.json', 'w');
				fwrite($fp, json_encode($response));
				fclose($fp);

				$response = array();

				$pickup_product = $seo_response;
				$pickup_product_id = array();
				$pick_cat = array();
				$searchpickup_product = array();
				foreach($pickup_product as $k=>$val)
				{
					$pick_cat[] = $val->id;
				}

				$all_products = $products;
				foreach($all_products as $k=>$product)
				{
					$categories_id = $product->categories_id;

					if(in_array($categories_id, $pick_cat))
					{
						if(in_array($product->id, $pickup_product_id))
						{

						}
						else
						{
							$pickup_product_id[] = $product->id;
							$searchpickup_product[] = $all_products[$k];
						}
					}
				}


				$response['search_pickup'] = $searchpickup_product;


			$fp = fopen(dirname(__FILE__).'/../../../online-bestellen/json/search_pickup_product_'.$company_id.'.json', 'w');
				fwrite($fp, json_encode($response));
				fclose($fp);

				$response = array();

				$seo_response = array();
				$search_products = array();
				$products = array();

				$seo_pickup_data = $responser['delivery'];

				foreach($seo_pickup_data as $key=>$seo_pickup)
				{
					foreach($seo_pickup as $k=>$seo_pick)
					{
						$seo_pickup_cat_product =  $seo_pick->product;
						$seo_pickup_sub_cat_seo =  $seo_pick->sub_category;
						$category_seo_key = $this->clean($seo_pick->name);
						$seo_response[$category_seo_key] = $seo_pick;
						$seo_response[$category_seo_key]->slug = $category_seo_key;
						$seo_response[$category_seo_key]->product='';
						$seo_response[$category_seo_key]->sub_category='';

						if(isset($seo_pickup_cat_product))
						{
							foreach($seo_pickup_cat_product as $k=>$seo_pickup_product)
							{
								if(isset($seo_pickup_product))
								{
									if(!empty($seo_pickup_product->proname))
									{
										$category_seo_product_key = $this->clean($seo_pickup_product->proname);

										if (in_array($category_seo_product_key, $pickup_url_key))
										{
											$i=1;
											while(in_array($category_seo_product_key, $pickup_url_key))
											{
												$category_seo_product_key = $category_seo_product_key.'-'.$i;
												if($i > 1)
												{
													$tokens = explode('-', $category_seo_product_key);
													array_pop($tokens);
													array_pop($tokens);
													$category_seo_product_key = implode('-', $tokens);
													$category_seo_product_key = $category_seo_product_key.'-'.$i;
												}
												$i++;
											}
											$pickup_url_key[] = $category_seo_product_key;
										}
										else
										{
											$pickup_url_key[] = $category_seo_product_key;
										}

										if($category_seo_product_key && $seo_pickup_product)
											$seo_response[$category_seo_key]->product->$category_seo_product_key = $seo_pickup_product;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->slug = $category_seo_product_key;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->catslug = $category_seo_key;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->subcatslug = null;
										$seo_response[$category_seo_key]->product->$category_seo_product_key->service = 'pickup';
										$products[] = $seo_response[$category_seo_key]->product->$category_seo_product_key;
									}
								}
							}
						}

						if(isset($seo_pickup_sub_cat_seo))
						{
							foreach($seo_pickup_sub_cat_seo as $k=>$seo_pickup_sub_cat)
							{
								if(isset($seo_pickup_sub_cat))
								{
									$seo_pickup_subcat_product =  $seo_pickup_sub_cat->product;
									$category_seo_sub_cat_key = $this->clean($seo_pickup_sub_cat->subname);

									if($category_seo_sub_cat_key && $seo_pickup_sub_cat)
										$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key = $seo_pickup_sub_cat;
									$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->slug = $category_seo_sub_cat_key;
									$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product = '';

								}

								if(isset($seo_pickup_subcat_product))
								{
									foreach($seo_pickup_subcat_product as $k=>$seo_pickup_sub_product)
									{
										if(isset($seo_pickup_sub_product))
										{
											if(!empty($seo_pickup_sub_product->proname))
											{
												$category_seo_sub_cat_product_key = $this->clean($seo_pickup_sub_product->proname);

												if (in_array($category_seo_sub_cat_product_key, $pickup_url_key))
												{
													$i=1;
													while(in_array($category_seo_sub_cat_product_key, $pickup_url_key))
													{
														$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
														if($i > 1)
														{
															$tokens = explode('-', $category_seo_sub_cat_product_key);
															array_pop($tokens);
															array_pop($tokens);
															$category_seo_sub_cat_product_key = implode('-', $tokens);
															$category_seo_sub_cat_product_key = $category_seo_sub_cat_product_key.'-'.$i;
														}
														$i++;
													}
													$pickup_url_key[] = $category_seo_sub_cat_product_key;
												}
												else
												{
													$pickup_url_key[] = $category_seo_sub_cat_product_key;
												}

												if($category_seo_sub_cat_product_key && $seo_pickup_sub_product)
													$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key = $seo_pickup_sub_product;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->slug = $category_seo_sub_cat_product_key;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->catslug = $category_seo_key;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->subcatslug = $category_seo_sub_cat_key;
												$seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key->service = 'pickup';
												$products[] = $seo_response[$category_seo_key]->sub_category->$category_seo_sub_cat_key->product->$category_seo_sub_cat_product_key;
											}
										}
									}
								}

							}
						}
					}
				}

				$response['delivery']['category'] = $seo_response;

			$fp = fopen(dirname(__FILE__).'/../../../online-bestellen/json/delivery_product_cat_'.$company_id.'.json', 'w');
				fwrite($fp, json_encode($response));
				fclose($fp);

				$response = array();

				$pickup_product = $seo_response;
				$pickup_product_id = array();
				$pick_cat = array();
				$searchpickup_product = array();

				foreach($pickup_product as $k=>$val)
				{
					$pick_cat[] = $val->id;
				}

				$all_products = $products;
				foreach($all_products as $k=>$product)
				{
					$categories_id = $product->categories_id;

					if(in_array($categories_id, $pick_cat))
					{
						if(in_array($product->id, $pickup_product_id))
						{

						}
						else
						{
							$pickup_product_id[] = $product->id;
							$searchpickup_product[] = $all_products[$k];
						}
					}
				}
				$response['search_delivery'] = $searchpickup_product;

			$fp = fopen(dirname(__FILE__).'/../../../online-bestellen/json/search_delivery_product_'.$company_id.'.json', 'w');
				fwrite($fp, json_encode($response));
				fclose($fp);

				$result1 = $this->update_files(array('pickup_product_cat_'.$company_id.'.json','search_pickup_product_'.$company_id.'.json','delivery_product_cat_'.$company_id.'.json','search_delivery_product_'.$company_id.'.json'));
				$response = array();
			}
		}

		if($this->input->post('action') == 'general_setting_json'){
			if( $this->company_role == 'sub') {
				$company_id = $this->company_parent_id;
			}
			$gene_setting = $this->get_shop_content_all_post( $company_id );
			$fp = fopen(dirname(__FILE__).'/../../../online-bestellen/json/general_settings_'.$company_id.'.json', 'w');
			fwrite($fp, json_encode($gene_setting));
			fclose($fp);

			$result2 = $this->update_files(array('general_settings_'.$company_id.'.json'));
		}
		echo json_encode(array('category_json'=>$result1,'general_setting_json'=>$result2));
	}

	private function update_files($file_arr = array()){
		$this->load->model('MFtp_settings');
		$companies_ftp_details = $this->MFtp_settings->get_ftp_settings( array( 'shop_files_loc <>' => '', 'ftp_hostname <>' => '', 'ftp_username <>' => '', 'ftp_password <>' => '', 'access_permission' => 1 ) , array( 'company_id' => $this->company_id ) );
		$error = array();
		if( !empty($companies_ftp_details) ){
			$this->load->library('ftp');

			foreach( $companies_ftp_details as $comp){
				$shop_files_loc = $comp->shop_files_loc;
				$ftp_hostname = $comp->ftp_hostname;
				$ftp_username = $comp->ftp_username;
				$ftp_password = $comp->ftp_password;

				$org_json_files = array();
				$updated_file_loc = dirname(__FILE__).'/../../../online-bestellen/';

				$conn_id = ftp_connect( $ftp_hostname ) or die("Couldn't connect to $ftp_server");
				if ( @ftp_login($conn_id, $ftp_username, $ftp_password) ){
					$org_json_files = $file_arr;
				}
				else{
					$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t connect as  ').$ftp_username._(', login failed !');
				}

				if( !empty($org_json_files) )
				foreach( $org_json_files as $id=>$imgf ){

					if ( @ftp_put($conn_id, $shop_files_loc.'/shop_js/json/'.$imgf, $updated_file_loc.'json/'.$imgf , FTP_ASCII) ){
					}
					else{
						$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'products/'.$imgf._(' on server !');
					}

					unlink('online-bestellen/json/'.$imgf);
				}
			}
		}
		return $error;
	}

	//get all settings

	function get_shop_content_all_post($company_id){

		$year = date("Y");
		$month = date("m");

		/*if($company_id == 3988)
		 $company_id = 87;*/

		// Fetching company account type. This is for checking whether company is attached with FoodDESK
		$this->db->select("ac_type_id");
		$company_info = $this->db->get_where('company',array("id" => $company_id))->result();
		$is_fdd_associated = 0;
		if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
			$is_fdd_associated = 1;

		// Fetching Opening hours
		//$this->Mgeneral_settings->get_general_settings();

		$general_settings = $this->get_general_settings($company_id);

		// Fetching Delivery settings
		$delivery_settings = $this->get_delivery_settings($company_id);


		// Fetching Delivery areas
		$delivery_areas = $this->get_delivery_areas($company_id);


		// Fetching International Delivery settings
		$international = array();
		$company_countries = array();
		$company_countries_int = array();
		$all_countries = array();
		if(!empty($delivery_settings)){
			if($delivery_settings[0]->type == 'international'){
				$international = $this->get_international_settings($company_id);

				//Fetching international countries delivery settings
				$company_countries = $this->get_company_countries($company_id);

				//Fetching international countries delivery costs
				$company_countries_int = $this->get_company_countries_int($company_id);

				//all countries list for register page dropdown
				$all_countries = $this->get_all_countries();

			}
		}

		// Fetching Opening hours
		$opening_hours = $this->get_opening_hours($company_id);

		// Fetching Opening hours
		$sub_admins = array();
		$this->db->select('role');
		$is_super = $this->db->get_where('company', array('id' => $company_id))->result();
		if(!empty($is_super) && $is_super['0']->role == 'super')
			$sub_admins = $this->get_sub_admins($company_id);

		// Fetching Opening hours
		$order_settings = $this->get_order_settings($company_id,$general_settings[0]->calendar_country);

		// Fetching Opening hours
		//$pre_assigned_holidays = $this->get_pre_assigned_holidays($company_id,$year,$month);

		// Fetching Opening hours
		$days = $this->get_days();

		// Fetching Countries
		$countries = $this->get_countries();

		// Fetching Available Payment Options for Cardgate
		$cardgate = $this->cardgate_payment_option($company_id);

		// Fetching all allergence words
		$allergence_words = $this->get_admin_defined_allergence();

		$response_arr = array(
				'delivery_areas'		=> $delivery_areas,
				'delivery_settings'		=> $delivery_settings,
				'opening_hours'			=> $opening_hours,
				'countries'				=> $countries,
				'sub_admins'			=> $sub_admins,
				'general_settings'		=> $general_settings,
				'order_settings'		=> $order_settings,
				'international'			=> $international,
				'company_countries'		=> $company_countries,
				'company_countries_int'	=> $company_countries_int,
				'all_countries'			=> $all_countries,
				//'pre_assigned_holidays' => $pre_assigned_holidays,
				'days' => $days,
				'cardgate'=>$cardgate,
				'allergence_words' => $allergence_words
		);

		return $response_arr;
		//$this->response( array('error' => 0, 'message'=> '' , 'data' => $response_arr ), 200 );
	}

	private function get_admin_defined_allergence(){
		$results = array();
		$allergence_words = array();

		$this->db->select('allergens_word');
		$results = $this->db->get('allergens_words')->result();

		if(!empty($results))
		foreach($results as $row){
			$allergence_words[] = $row->allergens_word;
		}
		return $allergence_words;
	}


	private function cardgate_payment_option($company_id){

		$this->load->model('Mpayment');
		$this->load->helper('curo');
		$data = array();

		// Checking for cardgate enable or not
		$merchant_info = $this->db->get_where('cp_merchant_info', array('company_id' => $company_id))->result_array();

		$cardgate_setting = $this->Mpayment->get_cardgate_setting(array('company_id' => $company_id));


		if(!empty($merchant_info) && !empty($cardgate_setting) && $cardgate_setting[0]->cardgate_payment == 1){
			//$merchant_status = get_status($merchant_info[0]['curo_id']);//echo $merchant_status;die;
			//if($merchant_status == 'Approved' || $merchant_status == 'approved')
			$data['enabled'] = 1;
			//else
			//$data['enabled'] = 0;
		}else{
			$data['enabled'] = 0;
		}

		// Temporary code for Delrey
		/*if($company_id == 26)
			$data['enabled'] = 1;*/

		if($data['enabled']){

		$data['minimum_amount_cardgate'] = $cardgate_setting[0]->minimum_amount_cardgate;
		$data['c_apply_tax'] = $cardgate_setting[0]->c_apply_tax;
		$data['c_tax_percentage'] = $cardgate_setting[0]->c_tax_percentage;
		$data['c_tax_amount'] = $cardgate_setting[0]->c_tax_amount;

		$get_banks = 0;
		$merchant_info = $this->Mpayment->get_merchant_info($company_id);

		$payment_methods = $this->Mpayment->get_selected_payment_methods($company_id);
		$i=0;
		$payment_gateway = array();
		foreach($payment_methods as $method){
		$get_info = $this->Mpayment->get_payment_method_info($method);
		if(!empty($get_info)){
		$payment_gateway[$i] = $get_info[0];
		if($get_info[0]['value']=='ideal'){
		$get_banks = 1;
		}
		$i++;
		}
		}

		$data['available_options'] = $payment_gateway;
		if($get_banks==1){
			$issuer = cargate_curl('https://api.cardgate.com/rest/v1/ideal/issuers/');
				$data['issuer'] = $issuer->issuers;
			}

				$data['merchant_curo_id'] = $merchant_info[0]['site_id'];

				// $data['merchant_name'] = 'sitematic';
			// $data['merchant_hash'] = 'Y8f0cnqB0WKAKZ1WwHTFNL2jge2pzOzXuEnBNHfWAuLNroo3Fjbm4iTJzcWqrR7l';
		}

		return $data;
	}

	private function get_days(){
		$days = $this->db->get('days')->result();
		return $days;
	}


	private function get_countries(){
		$this->db->where('id = 21 OR id = 150');
		$countries = $this->db->get('country')->result();
		return $countries;
	}

	private function get_order_settings($company_id = null, $calendar_country = 'calendar_belgium'){

		$order_settings = array();

		if($company_id){
			$this->db->where( 'company_id', $company_id);
			$order_settings = $this->db->get('order_settings')->result();
			if(!empty($order_settings)){
				$order_settings[0]->holiday_dates = $this->get_company_holiday_dates($company_id, $calendar_country);
				$order_settings[0]->shop_close_dates = $this->get_company_close_dates($company_id);
			}
		}

		return $order_settings;
	}

	private function get_sub_admins($company_id = null){

		$sub_admins = array();

		if($company_id){
			$where_arr['parent_id'] = $company_id;
			$where_arr['role'] = 'sub';
			$where_arr['status'] = '1';
			$where_arr['approved'] = '1';

			$this->db->select('
company.company_name,
general_settings.delivery_service,
general_settings.pickup_service,
general_settings.company_id,
general_settings.calendar_country,
general_settings.activate_discount_card,
general_settings.discount_card_message,
general_settings.pay_option,
general_settings.online_payment,
general_settings.paypal_address,
general_settings.apply_tax,
general_settings.tax_percentage,
general_settings.tax_amount,
general_settings.minimum_amount_paypal,
general_settings.disc_per_amount,
general_settings.disc_after_amount,
general_settings.disc_percent,
general_settings.disc_price,
general_settings.hide_availability,
general_settings.order_timing_info,
order_settings.*
');
			$this->db->join('order_settings','order_settings.company_id = company.id');
			$this->db->join('general_settings','general_settings.company_id = company.id');
			$this->db->where( $where_arr );
			$sub_admins = $this->db->get('company')->result();
			if(!empty($sub_admins)){
				foreach ($sub_admins as $key => $sub_admin){
					$sub_admins[$key]->holiday_dates = $this->get_company_holiday_dates($sub_admin->company_id, $sub_admin->calendar_country);
					$sub_admins[$key]->shop_close_dates = $this->get_company_close_dates($sub_admin->company_id);
					$sub_admins[$key]->delivery_areas = $this->get_delivery_areas($sub_admin->company_id);
					$sub_admins[$key]->delivery_settings = $this->get_delivery_settings($sub_admin->company_id);
					$sub_admins[$key]->opening_hours =  $this->get_opening_hours($sub_admin->company_id);

					if(!empty($sub_admins[$key]->delivery_settings)){
						if($sub_admins[$key]->delivery_settings[0]->type == 'international'){
							$sub_admins[$key]->international = $this->get_international_settings($sub_admin->company_id);

							//Fetching international countries delivery settings
							$sub_admins[$key]->company_countries = $this->get_company_countries($sub_admin->company_id);

							//Fetching international countries delivery costs
							$sub_admins[$key]->company_countries_int = $this->get_company_countries_int($sub_admin->company_id);
							$sub_admins[$key]->all_countries = $this->get_all_countries();
							$sub_admins[$key]->countries = $this->get_countries();
						}
					}
				}
			}
		}

		return $sub_admins;
	}

	function get_company_close_dates($company_id){
		$holidays = array();
		if($company_id){
			$this->db->select('day,month,year');
			$this->db->where('company_id', $company_id);
			$holidays_dates = $this->db->get('company_closedays')->result();
			if(!empty($holidays_dates)){
				foreach($holidays_dates as $holiday_date){
					$holidays[] = ( (strlen($holiday_date->day) == 1)?'0'.$holiday_date->day:$holiday_date->day ).'/'.( (strlen($holiday_date->month) == 1)?'0'.$holiday_date->month:$holiday_date->month ).'/'.$holiday_date->year;
				}
			}
		}
		return implode(',',$holidays);
	}

	function get_company_holiday_dates($company_id = 0, $calendar_country = 'calendar_belgium'){
		$holidays = array();
		if($company_id){
			$this->db->select('day,month,year');
			$this->db->where('company_id', $company_id);
			$this->db->where("( `calendar` = 'own' OR `calendar` = '".$calendar_country."' )");
			$holidays_dates = $this->db->get('company_holidays')->result();
			if(!empty($holidays_dates)){
				foreach($holidays_dates as $holiday_date){
					$holidays[] = ( (strlen($holiday_date->day) == 1)?'0'.$holiday_date->day:$holiday_date->day ).'/'.( (strlen($holiday_date->month) == 1)?'0'.$holiday_date->month:$holiday_date->month ).'/'.$holiday_date->year;
				}
			}
		}
		return implode(',',$holidays);
	}

	private function get_opening_hours($company_id = null){
		$opening_hours = array();

		if($company_id && is_numeric($company_id)){

			$this->db->join('days','pickup_delivery_timings.day_id = days.id');
			$this->db->where('pickup_delivery_timings.company_id', $company_id);
			$this->db->order_by('pickup_delivery_timings.day_id','ASC');
			$opening_hours = $this->db->get('pickup_delivery_timings')->result();
		}

		return $opening_hours;
	}

	private function get_company_countries($company_id){

		$companies_country_int = array();
		$this->db->select('company_countries.company_id,company_countries.country_id,country.country_name');
		$this->db->where('company_id',$company_id);
		$this->db->join('country','country.id =  company_countries.country_id','left');
		$companies_country_int = $this->db->get(' company_countries')->result();

		return $companies_country_int;
	}


	private function get_company_countries_int($company_id){

		$companies_country_int = array();
		$this->db->order_by('company_countries_int.lower_range','asc');
		$this->db->order_by('company_countries_int.upper_range','asc');
		$this->db->where('company_id',$company_id);
		$this->db->join('country','country.id =  company_countries_int.country_id','left');
		$companies_country_int = $this->db->get(' company_countries_int')->result();

		return $companies_country_int;
	}

	private function get_all_countries(){
		$this->db->select('id as country_id,country_name');
		$all_countries = $this->db->get('country')->result();
		return $all_countries;
	}

	private function get_international_settings($company_id = null){
		$international_areas = array();

		if($company_id){
			// 			$this->db->select('country.country_name, company_countries.country_id, company_countries.country_cost');
			// 			$this->db->join('country', 'country.id = company_countries.country_id');
			// 			$this->db->where('company_id',$company_id);
			// 			$international_areas = $this->db->get('company_countries')->result();
			$this->db->select('country.country_name, company_countries_int.country_id');
			$this->db->join('country', 'country.id = company_countries_int.country_id','left');
			$this->db->where('company_id',$company_id);
			$this->db->distinct('country_id');
			$international_areas = $this->db->get('company_countries_int')->result();
		}

		return $international_areas;
	}


	private function get_delivery_areas($company_id = null){

		$delivery_areas = array();

		if($company_id && is_numeric($company_id)){

			// Fetching countries names
			$countries = array();
			$this->db->select('country.id, country.country_name');
			$this->db->join('country', 'company_delivery_areas.country_id = country.id');
			$this->db->group_by('company_delivery_areas.country_id');
			$this->db->where('company_delivery_areas.company_id',$company_id);
			$countries = $this->db->get('company_delivery_areas')->result();

			// Fetching Provinces
			$provinces = array();
			$this->db->distinct();
			$this->db->select('`company_delivery_areas`.`state_id`, `company_delivery_areas`.`company_id`, `states`.`state_name`, `states`.`country_id`');
			$this->db->join('states','states.state_id = company_delivery_areas.state_id' );
			//$this->db->group_by('company_delivery_areas.state_id');
			$this->db->where('company_delivery_areas.company_id',$company_id);
			$provinces = $this->db->get('company_delivery_areas')->result();

			// Fetching Cities
			$cities = array();
			$this->db->select('postcodes.*');
			$this->db->join('postcodes','postcodes.id = company_delivery_areas.postcode_id' );
			$this->db->where('company_delivery_areas.company_id',$company_id);
			$cities = $this->db->get('company_delivery_areas')->result();

			$delivery_areas = array( 'countries' => $countries, 'provinces' => $provinces, 'cities' => $cities);
		}

		return $delivery_areas;
	}

	private function get_general_settings($company_id = null){

		$general_settings = array();

		if($company_id){
			$this->db->select( '
								company.address,
								company.zipcode,
								company.city,
								company.country_id,
								company.show_bo_link_in_shop,
								company.k_assoc,
								activate_discount_card,
								discount_card_message,
								pay_option, online_payment,
								paypal_address,
								apply_tax,
								tax_percentage,
								tax_amount,
								minimum_amount_paypal,
								disc_per_amount,
								disc_after_amount,
								disc_percent,
								disc_price,
								hide_availability,
								activate_suggetions,
								num_of_suggetions,
								calendar_country,
								extra_field_popup,
								extra_field_popup_name,
								tnc_txt,
								order_timing_info,
								shop_view,
								shop_view_default,
								amt_row_page,
								promocode,
								promocode_text,
								promocode_percent,
								promocode_price,
								promocode_start,
								promocode_end,
								introcode,
								introcode_text,
								introcode_percent,
								introcode_price
							' );

			$this->db->where( 'general_settings.company_id', $company_id);
			$this->db->where_in( 'company.ac_type_id', array(3,5,6));//3-Pro,5-FDD Pro,6-FDD Premium
			$this->db->where('company.ingredient_system','0');
			$this->db->join('company', 'company.id = general_settings.company_id');

			$general_settings = $this->db->get('general_settings')->result();
		}

		return $general_settings;
	}


//get all product

	function get_shop_content_json_new($company_id){

		$service_types = array('pickup', 'delivery');
		$data = array();

		$this->db->select("ac_type_id");
		$company_info = $this->db->get_where('company',array("id" => $company_id))->result();
		$is_fdd_associated = 0;
		if($company_info[0]->ac_type_id == 5 || $company_info[0]->ac_type_id == 6)
			$is_fdd_associated = 1;

		foreach($service_types as $service_type)
		{
			$categories = $this->get_categories_json($company_id, $service_type);

			$categories = (array)$categories;
			foreach($categories as $k=>$category)
			{
				$category = (array)$category;
				$categories_id = $category['id'];
				$subcategories_id = -1;

				$subcats = $this->get_subcategories_json($categories_id);
				$categories[$k]->sub_category = $subcats;

				$categories[$k]->product = $this->get_category_products_json($company_id, $categories_id, $subcategories_id,$is_fdd_associated);

				foreach($subcats as $key=>$subcat)
				{
					$subcategories_id = $subcat->id;
					$subcats[$key]->product = $this->get_category_products_json($company_id, $categories_id, $subcategories_id,$is_fdd_associated);
				}

				$categories[$k]->sub_category = $subcats;
			}
			$result['category'] = $categories;

			$data[$service_type] = $result;
		}
		return $data;
	}

	function get_categories_json($company_id, $service_type)
	{

		$query = '';

		$query = " Select * FROM `categories` WHERE ( `company_id` = ".$company_id." AND `status` = 1 ) ";

		if( !$service_type || $service_type == 'both' )
		{
		}
		elseif( $service_type == 'pickup' )
		{
			$query .= " AND ( `service_type` = '1' OR `service_type` = '0' ) ";
		}
		elseif( $service_type == 'delivery' )
		{
			$query .= " AND ( `service_type` = '2' OR `service_type` = '0' ) ";
		}

		$query .= " ORDER BY `order_display` ASC ";

		$categories = $this->db->query( $query )->result();

		if( !empty($categories) )
		{
			$categories = $this->check_category_image_exist($categories);
			foreach($categories as $category)
			{
				$new_categories[$category->id] = $category;
			}
			return $new_categories;
		}
		else
		{
			return null;
		}

		exit;
	}

	function get_subcategories_json($categories_id)
	{
		$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'status', 1 );

		$this->db->order_by( 'suborder_display', 'ASC' );

		$subcategories = $this->db->get('subcategories')->result();
		if( !empty($subcategories) )
		{
			$subcategories = $this->check_subcategory_image_exist($subcategories);
			foreach($subcategories as $subcategory)
			{
				$new_subcategories[$subcategory->id] = $subcategory;
			}
			return $new_subcategories;
		}
		else
		{
			return null;
		}
	}

	/**
	 * This function is used to check whether product image saved in database is actually exists in desired location
	 * @param string $image image name
	 * @return string $final_image Final image name if exist otherwise null.
	 */
	private function existing_product_image($image = ''){

		$final_image = '';
		if($image != ''){
			$path = dirname(__FILE__)."/../../../assets/cp/images/product/";
			if(@file_exists($path.$image))
				$final_image = $image;
		}
		return $final_image;
	}

	/**
	 * This private function is used to get multidiscount
	 * @access private
	 * @param integer $product_id Product ID
	 * @param integer $discount_type It is the type of discount to be fetched (0 => Unit Wise, 1 => Weight wise, 2 => Per Person wise)
	 * @return array $products_discount Array of multi discounts
	 */
	private function get_product_multi_discounts($product_id = null, $discount_type = null)
	{
		$products_discount = array();

		if($product_id && is_numeric($product_id)){

			if( ($discount_type == 0 || $discount_type == 1 || $discount_type == 2) && $discount_type != null )
			{
				$this->db->where( 'type', $discount_type );
			}

			$this->db->order_by('quantity','ASC');
			$this->db->where( 'products_id', $product_id );
			$products_discount = $this->db->get('products_discount')->result();
		}

		return $products_discount;
	}

	/**
	 * This private function is used to fetch Keurslager Allergence related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which allergence have to be fetch
	 * @return array $traces It is the array if allergence associated with the given product
	 */
	private function get_k_allergence($product_id = 0){
		$allergence = array();
		if($product_id){
			$this->db->select('prefix,ka_id,ka_name');
			$this->db->order_by('display_order', 'ASC');
			$this->db->group_by('ka_id');
			$allergence = $this->db->get_where('products_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	/**
	 * This private function is used to fetch Keurslager Sub Allergence related with the given product ID
	 * @access private
	 * @param int $product_id It is the ID of product for which sub allergence have to be fetched
	 * @return array $traces It is the array if sub allergence associated with the given product
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	private function get_k_sub_allergence($product_id = 0){
		$allergence = array();
		if($product_id){
			$this->db->select('parent_ka_id,sub_ka_name');
			$this->db->order_by('display_order', 'ASC');
			$this->db->group_by('sub_ka_id');
			$allergence = $this->db->get_where('product_sub_allergence', array('product_id' => $product_id))->result();
		}
		return $allergence;
	}

	function get_category_products_json($company_id, $categories_id, $subcategories_id,$is_k_assoc = 0)
	{
		$this->db->select('display_fixed');
		$display_fixed_result = $this->db->get_where('general_settings', array('company_id' => $company_id))->result();

		$this->db->where( 'company_id', $company_id );
		$this->db->where( 'categories_id', $categories_id );
		$this->db->where( 'subcategories_id', $subcategories_id );
		$where = '((semi_product = 1 AND direct_kcp = 0) OR (semi_product = 0))';
		$this->db->where($where);
		$this->db->where( 'status', 1 );
		$this->db->order_by('pro_display','asc');
		$this->db->order_by('id','asc');

		$products = $this->db->get_where('products', array('sell_product_option !=' => '' ))->result();

		if(!empty($products)){
			foreach ($products as $key => $product){
				if($is_k_assoc){
					$complete = $this->get_fixed_status($product->id,$product->direct_kcp);

					if($display_fixed_result[0]->display_fixed){
						if(!$complete){
							unset($products[$key]);
							continue;
						}
					}

					$product->complete = $complete;
				}
				/**
				 * Checking if product's image exist
				 */
				if($product->image != '' && $product->image != null){
					$products[$key]->image = $this->existing_product_image($product->image);
				}else{
					$products[$key]->image = '';
				}

				/**
				 * Fetching Extras
				 */
				$products[$key]->grp_arr = $this->get_product_groups($product->id);

				/**
				 * Fetching multidiscounts
				*/
				if($product->sell_product_option == 'per_unit'){
					if($product->discount == 'multi'){
						$products[$key]->multi_discount = $this->get_product_multi_discounts($product->id,'0');
					}
				}elseif($product->sell_product_option == 'weight_wise'){
					if($product->discount_wt == 'multi'){
						$products[$key]->multi_discount_wt = $this->get_product_multi_discounts($product->id,'1');
					}
				}elseif($product->sell_product_option == 'per_person'){
					if($product->discount_person == 'multi'){
						$products[$key]->multi_discount_person = $this->get_product_multi_discounts($product->id,'2');
					}
				}elseif($product->sell_product_option == 'client_may_choose'){
					if($product->discount == 'multi'){
						$products[$key]->multi_discount = $this->get_product_multi_discounts($product->id,'0');
					}
					if($product->discount_wt == 'multi'){
						$products[$key]->multi_discount_wt = $this->get_product_multi_discounts($product->id,'1');
					}
				}

				/**
				 * Fetching Keurslager Ingredients
				 */
				if($is_k_assoc){
					/*$products[$key]->k_ingredients = $this->get_k_ingredients($product->id);
					 $products[$key]->k_traces = $this->get_k_traces($product->id);
					$products[$key]->k_allergence = $this->get_k_allergence($product->id);*/
					/*$k_ingredients = $this->get_k_ingredients($product->id);
					 if(!empty($k_ingredients)){
					$ing_str = '';
					$add_comma = true;
					foreach ($k_ingredients as $k_ingredient){
					if(trim($k_ingredient->ki_name) != ''){
					if($k_ingredient->ki_name == '(' || $k_ingredient->ki_name == ')'){
					$ing_str .= '  '.$k_ingredient->ki_name;
					if($k_ingredient->ki_name == '(')
							$add_comma = false;
							}else{
							if($add_comma)
						$ing_str .= ', '.$k_ingredient->prefix.' '.$k_ingredient->ki_name;
							else
						$ing_str .= $k_ingredient->prefix.' '.$k_ingredient->ki_name;
							$add_comma = true;
							}
							}
							}
							//if($product->ingredients && trim($product->ingredients) != '')
						//	$product->ingredients = $product->ingredients.$ing_str;
							//else
						$product->ingredients = substr($ing_str,2);
							}

							$k_traces = $this->get_k_traces($product->id);
							if(!empty($k_traces)){
							$tra_str = '';
							$add_comma = true;
							foreach ($k_traces as $k_trace){
							if(trim($k_trace->kt_name) != ''){
							if($k_trace->kt_name == '(' || $k_trace->kt_name == ')'){
							$tra_str .= '  '.$k_trace->kt_name;
							if($k_trace->kt_name == '(')
									$add_comma = false;
									}else{
									if($add_comma)
						$tra_str .= ', '.$k_trace->prefix.' '.$k_trace->kt_name;
									else
						$tra_str .= $k_trace->prefix.' '.$k_trace->kt_name;
									$add_comma = true;
									}
									}
									}
									//if($product->traces_of && trim($product->traces_of) != '')
						//	$product->traces_of = $product->traces_of.$tra_str;
									//else
						$product->traces_of = substr($tra_str,2);
									}*/

					if($complete){
						$k_allergence = $this->get_k_allergence($product->id);
						$k_sub_allergence = $this->get_k_sub_allergence($product->id);
						if(!empty($k_allergence)){
							$allrg_str = '';
							$add_comma = true;
							$count = 0;
							foreach ($k_allergence as $k_allerg){
								if(trim($k_allerg->ka_name) != ''){
									++$count;
									//$allrg_str .= ', '.$k_allerg->prefix.' '.$k_allerg->ka_name;
									if($k_allerg->ka_name == '(' || $k_allerg->ka_name == ')'){
										$allrg_str .= '  '.$k_allerg->ka_name;
										if($k_allerg->ka_name == '(')
											$add_comma = false;
									}else{
										if($add_comma)
											$allrg_str .= ', '.$k_allerg->prefix.' '.$k_allerg->ka_name;
										else
											$allrg_str .= $k_allerg->prefix.' '.$k_allerg->ka_name;
										$add_comma = true;
									}
									if(($k_allerg->ka_id == 1) || ($k_allerg->ka_id == 8)){
										$a1 = '';
										if(!empty($k_sub_allergence)){
											$a1 .= ' (';
											foreach ($k_sub_allergence as $k_sub_allerg){
												if($k_sub_allerg->parent_ka_id == $k_allerg->ka_id){
													$a1 .=  $k_sub_allerg->sub_ka_name.', ';
												}
											}
											$a1 = rtrim($a1,', ');
											$a1 .= ')';
											$a1 = str_replace('()', '', $a1);
										}
										$allrg_str .= $a1;
									}
								}
							}
							if(strpos($allrg_str,'Melk') !== false && strpos($allrg_str,'Lactose') !== false){
								$allrg_str = str_replace('Melk', 'Melk (incl. lactose)', $allrg_str);
								$allrg_str = str_replace(',  Lactose', '', $allrg_str);
							}
							//if($product->allergence && trim($product->allergence) != '')//product 5340 in cedcoss shop was prefixing 0
							//	$product->allergence = $product->allergence.$allrg_str;
							//else
							$product->allergence = substr($allrg_str,2);
						}
					}
				}
			}
		}

		$category_products = $products;

		$sorted_p = array();
		$unsorted_p = array();
		if(isset($category_products))
		{
			foreach($category_products as $prod) {
				if($prod->pro_display != 0){
					$sorted_p[] = $prod;
				}else{
					$unsorted_p[] = $prod;
				}

			}
			$category_products = array_merge($sorted_p,$unsorted_p);
		}



		if( !empty($category_products) )
		{
			$category_products = $this->check_products_image_exist($category_products);
			foreach($category_products as $category_product)
			{
				$category_product->grp_arr = $this->get_product_groups($category_product->id);
				$new_category_products[$category_product->id] = $category_product;

			}
			return $new_category_products;

		}
		else
		{
			return null;
		}
	}

	private function get_fixed_status($id = 0, $direct_kcp = 0){
		$complete = 1;
		if($id){
			if($direct_kcp == 1){
				$this->db->where(array('obs_pro_id'=>$id,'is_obs_product'=>0));
				$result = $this->db->get('fdd_pro_quantity')->result_array();
				if(empty($result)){
					$complete = 0;
				}
			}
			else{
				$this->db->where(array('obs_pro_id'=>$id));
				$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
				if(!empty($result_custom)){
					foreach ($result_custom as $val){
						if($val['is_obs_product'] == 1){
							$complete = 0;
							break;
						}
					}
				}
				else{
					$complete = 0;
				}
			}
		}
		return $complete;
	}

	private function check_products_image_exist($checking_array){

		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->image){
					$path = dirname(__FILE__)."/../../../assets/cp/images/product/";
					if(!file_exists($path.$items->image))
						$checking_array[$key]->image = '';
				}

			}
		}
		return $checking_array;
	}

	private function get_product_groups( $product_id = null)
	{
		$grps_arr = array();
		if($product_id && is_numeric($product_id)){
			$this->db->where( 'groups_products.products_id', $product_id );
			$this->db->join('groups', 'groups.id = groups_products.groups_id');
			$this->db->order_by( 'groups.display_order', 'ASC' );
			$this->db->order_by( 'groups_products.display_order', 'ASC' );
			$this->db->order_by( 'groups_products.type', 'ASC' );

			$product_grps = $this->db->get('groups_products')->result();
			//$this->response($product_grps);
			if( !empty($product_grps) )
			{
				$hold_grp_id = array();
				$index = -1;

				foreach( $product_grps as $grp )
				{
					//if( $hold_grp_id != $grp->groups_id )
					if(!in_array($grp->groups_id,$hold_grp_id))
					{
						$hold_grp_id[] = $grp->groups_id;
						//$index = $index+1;

						//$grps_arr[$index] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'required' => $grp->required, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );
						$grps_arr[] = array( 'grp_id' => $grp->groups_id, 'grp_name' => $grp->group_name, 'grp_multiselect' => $grp->multiselect, 'required' => $grp->required, 'grp_type' => $grp->type, 'attributes_arr' => array( 0 => array( $grp->attribute_name, $grp->attribute_value) ) );

					}
					elseif( in_array($grp->groups_id, $hold_grp_id) )
					{
						$key = array_search($grp->groups_id,$hold_grp_id);
						$grps_arr[$key]['attributes_arr'][] = array( $grp->attribute_name, $grp->attribute_value );
					}
				}
			}
		}

		return $grps_arr;
	}

	private function check_category_image_exist($checking_array){

		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->image){
					$path = dirname(__FILE__)."/../../../";
					if(!file_exists($path.$items->image))
						$checking_array[$key]->image = '';
				}

			}
		}
		return $checking_array;
	}

	private function check_subcategory_image_exist($checking_array){

		if(!empty($checking_array)){
			foreach($checking_array as $key => $items){
				if($items->subimage){
					$path = dirname(__FILE__)."/../../../";
					if(!file_exists($path.$items->subimage))
						$checking_array[$key]->subimage = '';
				}

			}
		}
		return $checking_array;
	}

// 	function script(){
// 		$data = $this->input->post('data');

// 		foreach ($data as $da){
// 			$insert_array = array(
// 				'nutrient'=> preg_replace('/\s\s+/', ' ', $da['nutrient']),
// 				'capacity'=> preg_replace('/\s\s+/', ' ', $da['capacity']),
// 				'weight'=> preg_replace('/\s\s+/', ' ', $da['weight']),
// 			);

// 			$this->db->insert('weights_and_measures',$insert_array);
// 		}

// 	}

/* 	function script_product_without_sheets(){
		$pro_ids = $this->db->query('
					SELECT products.id
					FROM products JOIN company ON products.company_id = company.id
					WHERE products.direct_kcp = 1
					AND products.semi_product = 0
					AND products.direct_kcp_id = 0
					AND products.id NOT IN (SELECT products_ingredients.product_id FROM products_ingredients WHERE kp_id != 0 AND ki_id != 0)
					AND products.company_id IN (SELECT id FROM `company` WHERE `ac_type_id` IN (4,5,6))
					')->result_array();
		foreach ($pro_ids as $val){
			$this->db->where('id',$val['id']);
			$this->db->update('products',array('company_id' => 0));
		}

		$new_fized = $this->db->query('
					SELECT products.id, fdd_pro_quantity.obs_pro_id
					FROM products JOIN fdd_pro_quantity ON products.id = fdd_pro_quantity.fdd_pro_id
					WHERE fdd_pro_quantity.is_obs_product= 1
					AND company_id = 0
					AND fdd_pro_quantity.obs_pro_id IN (SELECT id FROM products)
					')->result_array();

		for ($i = 0 ; $i < count($new_fized); $i++){
			$comp_id[$i][0] = $new_fized[$i]['id'];
			$comp_id[$i][1] = $this->db->get_where('products',array('id' => $new_fized[$i]['obs_pro_id']))->row()->company_id;

			$pending_arr = $this->db->get_where('products_pending',array('product_id' => $comp_id[$i][0],'company_id' => $comp_id[$i][1]))->result_array();
			if(empty($pending_arr)){
				$this->db->insert('products_pending', array('product_id' => $comp_id[$i][0], 'company_id' => $comp_id[$i][1], 'date' => date('Y-m-d h:i:s')));
			}
		}
	} */
}
