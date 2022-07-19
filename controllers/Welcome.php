<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
		// $this->load->library('recaptcha');

		$this->load->helper('url');
		$this->load->helper('captcha');
		$this->load->helper('phpmailer');


		$this->load->library('messages');
		$this->load->library('form_validation');

		$this->mail_template = 'mail_templates/'; // base_url().'application/views/mail_templates/';

		$this->load->model('mcp/Mcompanies');
		$this->load->model('Madmin');
		$this->fdb = $this->load->database('fdb',TRUE);
    }

	public function index()
	{
	    $this->load->view('header');
		$this->load->view('welcome');
	    $this->load->view('sidebar');
		$this->load->view('footer');
	}

	function renew_captcha(){
		$this->load->helper('captcha');

		$captcha_arr = array(
				'img_path' => dirname(__FILE__).'/../../assets/captcha/',
				'img_url' => base_url().'assets/captcha/',
				'img_width' => '100',
				'img_height' => 30,
				'expiration' => 7200
		);

		$captcha = create_captcha($captcha_arr);
		$html=$captcha['image'].'<input type="text" class="input" name="captcha" id="captcha" /> <?php echo form_error("captcha");?><input type="hidden" name="captcha-enc" id="captcha-enc" value="'.md5($captcha['word']).'" />';
		echo $html;
	}

	function valid_url($str){
		if($str == ''){
			return false;
		}else{
			$match='^(http://){0,1}(https://){0,1}(w){3}\.[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4}(/\S*)?$^';
			if(!(preg_match($match, $str))){
				return false;
			}
			return true;
		}
	}

	function register()
	{
		if($this->input->post('act')=="add")
		{
			$this->load->library('form_validation');

			$this->form_validation->set_message('is_natural',_('Please select any option.'));
			$this->form_validation->set_rules('company_name',_('Company Name'), 'required');
			$this->form_validation->set_rules('type_id',_('Type'),'is_natural');
			$this->form_validation->set_rules('first_name', _('First Name'), 'required');
			$this->form_validation->set_rules('last_name', _('Last Name'), 'required');
			$this->form_validation->set_rules('email', _('E-mail'), 'required|valid_email|is_unique[company.email]');
			$this->form_validation->set_rules('phone',_('Telephone Number'), 'required');
			if($this->input->post('have_website') == 1){
				$this->form_validation->set_rules('website','Website','callback_valid_url');
			}else{
				$this->form_validation->set_rules('domain',_('Domain Name'), 'callback_valid_url');
				$this->form_validation->set_rules('package',_('Package Name'), 'is_natural');
			}
			$this->form_validation->set_rules('address',_('Address'), 'required');
			$this->form_validation->set_rules('zipcode',_('Postcode'), 'required');
			$this->form_validation->set_rules('city',_('City Name'), 'required');
			$this->form_validation->set_rules('country_id',_('Country Name'), 'is_natural');
			$this->form_validation->set_rules('username',_('User Name'), 'required|is_unique[company.username]');
			$this->form_validation->set_rules('password',_('Password'), 'required');
			$this->form_validation->set_rules('confirm_password',_('Password Confirmation'), 'required|matches[password]');
			$this->form_validation->set_rules('captcha',_('Captcha Word'), 'required');
			if ($this->form_validation->run() == FALSE){

			}else{
				if(MD5($this->input->post('captcha')) == $this->input->post('captcha-enc')){

					$this->load->model('Mregister');

					$insert_id = $this->Mregister->register();

					// Sending Mail to admin

					if($insert_id)
					{
						$this->load->library('email');
						$From = $this->config->item("no_reply_email");
			   			$mailFrom = $this->config->item("site_admin_name");
			   			$To = $this->config->item("site_admin_email");

						$subject = _('New Company Registered');

						$register = $this->input->post();
						$register['insert_id'] = $insert_id;
						$register['REQ_IP_ADD'] = $_SERVER['REMOTE_ADDR'];

						$mail_body = $this->load->view($this->mail_template.'new_company_registered', $register, true);

						send_email( $To, $From, $subject, $mail_body, $mailFrom, NULL, NULL, 'no_reply', 'site_admin', 'welcome_new_company_registered');

						$this->session->set_flashdata('success', _('You details are been saved successfully. And a mail has been sent to the administrator for your approval. So please wait, he will get back to you soon !'));

						redirect(base_url().'welcome/register');
					}
				}else{
					$_SESSION['message'] = array('status'=>'error','response'=>_('Please Re-enter Captcha word. It is case sensitive !'));
				}

			}
		}

		$this->load->helper('captcha');

		$captcha_arr = array(
				'img_path' => dirname(__FILE__).'/../../assets/captcha/',
				'img_url' => base_url().'assets/captcha/',
				'img_width' => '100',
				'img_height' => 30,
				'expiration' => 7200
		);

		$captcha = create_captcha($captcha_arr);

		$data['captcha'] = $captcha;

		//$this->load->model('Mcategories');
		$this->load->model('Mcountry');
		//$data['categories'] = $this->Mcategories->get_categories();
		$data['country'] = $this->Mcountry->get_country();

		$this->load->model('Mcompany_type');
		$data['company_type']  = $this->Mcompany_type->get_company_type();
		//print_r($data['company_type']);
		$this->load->model('Mpackages');
		$data['packages'] = $this->Mpackages->get_packages();

		$this->load->view('header');
		$this->load->view('register',$data);
		$this->load->view('sidebar');
		$this->load->view('footer');

	}

	function features(){

	$this->load->view('header');
	$this->load->view('features');
	$this->load->view('sidebar');
	$this->load->view('footer');

	}

	function faq(){
	$this->load->view('header');
	$this->load->view('faq');
	$this->load->view('sidebar');
	$this->load->view('footer');
	}

	function overons(){
	$this->load->view('header');
	$this->load->view('overons');
	$this->load->view('sidebar');
	$this->load->view('footer');
	}

	function links(){
	$this->load->view('header');
	$this->load->view('links');
	$this->load->view('sidebar');
	$this->load->view('footer');
	}

	function gebruikersvoorwaarden(){
	$this->load->view('header');
	$this->load->view('gebruikersvoorwaarden');
	$this->load->view('sidebar');
	$this->load->view('footer');
	}

	function contact(){
		$this->load->library('email');
		if($this->input->post('submit')){

			$captcha_field = $this->input->post('captcha_field');
			$this->messages->clear();

			if($captcha_field != $this->session->userdata('captcha_field')){
				$this->messages->add('error !! plese enter correct security code ','error');
				redirect('welcome/contact');
			}
			else{

				$company_name = $this->input->post('comapny_name');
				$user_name = $this->input->post('user_name');
				$email = $this->input->post('email');
				$phone = $this->input->post('phone');
				$url = $this->input->post('url');
				$subject = $this->input->post('subject');
				$email_message = $this->input->post('message');

				$mail_data['user_name'] = $user_name;
				$mail_data['phone'] = $phone;
				$mail_data['email'] = $email;
				$mail_data['url'] = $url;
				$mail_data['email_message'] = $email_message;

				$message = $this->load->view('mail_templates/welcome_contact_form_mail', $mail_data, true);
				$query = send_email( $this->config->item('site_admin_email'), $email, $subject, $message, NULL, NULL, NULL, 'company', 'site_admin', 'welcome_company_contact_mail');
				if($query){
					$this->messages->add('your mail request has been sent successfully ','success');
				}else{
					$this->messages->add('error in sending mail ','error');
				}
				redirect('welcome/contact');
			}

		}

		else{

		/*=====code for captcha======*/
			$path = dirname(__FILE__);
		  	$vals = array(
    			'img_path'   => $path.'/../../assets/images/captcha/',
    			'img_url'    => base_url().'assets/images/captcha/',
    			'img_width'  => '150',
    			'img_height' => 50
    		);

		$data['captcha'] = $captcha =  create_captcha($vals);//there will be three keys 'word','time'.'image'
		$this->session->set_userdata(array('captcha_field' => $captcha['word']));

		/*===========================*/
		$this->load->view('header');
		$this->load->view('contact',$data);
		$this->load->view('sidebar');
		$this->load->view('footer');
		}
	}


    private function validateCaptcha($value) {
		if ($this->recaptcha->check_answer($_SERVER['REMOTE_ADDR'],$this->input->post('recaptcha_challenge_field'),$value)) {
			return TRUE;
		} else {
			$this->form_validation->set_message(__FUNCTION__, lang('recaptcha_incorrect_response'));
			return FALSE;
		}
	}

	 function validate()
	 {
		 $data = '';
		 if($this->input->post('check') == 'email')
		 {
			$text=array();
			$text['email']=$this->input->post('email');
			$result=$this->Mcompanies->get_company(array('email'=>$text['email']));
			$data = '';

			if(!empty($result))
			{
				$data = 'duplicate';
			}
			else
			{
				$data = 'notexist';
			}
		  }

		  if($this->input->post('check') == 'username')
		  {
			 $text=array();
			 $text['username']=$this->input->post('username');
			 $result=$this->Mcompanies->get_company($text);

			 if(!empty($result))
			 {
				$data = 'duplicate';
			 }
			 else
			 {
				$data = 'notexist';
			 }
		  }

		  echo json_encode(array("RESULT"=>$data));
	  }

	function verify_email($verificationCode = null){
		if($verificationCode){
			$codePart = explode("-",$verificationCode);

			//$this->db->where("DATEDIFF(CURDATE(),date) <= 2");
			$this->db->where("DATEDIFF('".date("Y-m-d")."',date) <= 2");
			$this->db->where("company_id",$codePart['0']);
			$this->db->order_by("date","DESC");
			$this->db->limit("1");
			$info = $this->db->get("email_verification")->result_array();
			if(!empty($info) && isset($codePart['1'])){
				if($verificationCode == $info['0']['verification_code']){
					$this->db->where("company_id",$codePart['0']);
					$this->db->delete("email_verification");

					$this->db->where("id",$codePart['0']);
					$this->db->update("company",array("email_verification" => '2'));
				}
			}/*else{
				$this->db->where("DATEDIFF('".date("Y-m-d")."',date) > 2");
				$this->db->where("company_id",$codePart['0']);
				$this->db->where("verification_code",$verificationCode);
				$info = $this->db->get("email_verification")->result_array();
			}
			$this->db->where("DATEDIFF('".date("Y-m-d")."',date) > 2");
			$this->db->delete("email_verification");*/

		}
		redirect(base_url().'cp');
	}

	function check_mail_verification(){
		$this->db->where("DATEDIFF('".date("Y-m-d")."',date) > 2");
		$info = $this->db->get("email_verification")->result_array();
		if(!empty($info)){
			foreach($info as $value){
				$this->db->where('id',$value['company_id']);
				$this->db->update("company",array("email_verification" => '3'));
			}
		}
		$this->db->where("DATEDIFF('".date("Y-m-d")."',date) > 2");
		$this->db->delete("email_verification");
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

	private function create_username($first,$last){
		$first = preg_replace("/[^A-Za-z0-9 ]/", '', $first);
		$last = preg_replace("/[^A-Za-z0-9 ]/", '', $last);

		$first = substr($first,0,3);
		$last = substr($last,0,3);

		$num = rand(111,999);

		$username = $first.$num.$last;
		$username = strtolower($username);

		return $username;
	}

	private function create_password($first,$last){
		$first = preg_replace("/[^A-Za-z0-9 ]/", '', $first);
		$last = preg_replace("/[^A-Za-z0-9 ]/", '', $last);

		$first = substr($first,0,3);
		$last = substr($last,0,3);

		$num = rand(111,9999);

		$password = $first.$num;
		$password = strtolower($password);

		return $password;
	}

	/**
	 * Function used to generate mail content on any order for a company admin and then passed to a JAVA App to genrate the mail without showing window print dialogue box.
	 *
	 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
	 *
	 * @param mixed $api_key api_id provided to any company admin
	 *
	 * @return null returns nothing but echo the whole mail content for an order for a company admin
	 *
	 */
	public function print_cp_mails($api_key = null, $font_size = 12, $today_or_all = 1, $update_db = "check" ){

		$fp = fopen(dirname(__FILE__).'/../../mail2print.txt', 'a');
		fwrite($fp, date('Y-m-d H:i:s').'--> '.$api_key.'--'.$font_size.'--'.$today_or_all.'--'.$update_db."\n\n");
		fclose($fp);

		header('Content-Type: text/html; charset=utf-8');

		//checking for an apikey..
		if($api_key != null){
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

									$subadminsId = implode(',', array_map(array("Welcome","get_id"), $subadminsIds));

									//foreach ($subadminsIds as $subadminsId){
										if($today_or_all == 2){
											//$res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
											$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `print_ready` = 1 AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
										}else{
											// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
											$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `print_ready` = 1 ORDER BY `id` DESC LIMIT 1")->result_array();
										}
										//$res_order = array_merge($res_order, $res_order_tmp);
									//}

									$fp = fopen(dirname(__FILE__).'/../../mail2printd.txt', 'a');
									fwrite($fp, date('Y-m-d H:i:s').'--> '.json_encode($res_order)."\n\n");
									fclose($fp);
								}
							}else{
								if($today_or_all == 2){
									$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `print_ready` = 1 AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
								}else{
									// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
									$res_order = $this->db->query("SELECT `orders`.*,`delivery_country`.`country_name` as delivery_country_name FROM `orders` LEFT JOIN `country` as delivery_country ON `orders`.`delivery_country` = `delivery_country`.`id` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `print_ready` = 1 ORDER BY `id` DESC LIMIT 1")->result_array();
								}
							}
						}


						if (!empty($res_order))
						{
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
									/* $countries = array();
									if(!empty($order_data) && $order_data['phone_reciever'] != '' && $order_data['option'] == "2"){
										$mail_data['is_international'] = true;
										$this->db->select('id,country_name');
										$country_arr = $this->db->get('country')->result();
										foreach($country_arr as $country){
											$countries[$country->id] = $country;
										}
									}
									$mail_data['countries'] = $countries; */

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

									$this->db->insert('print_master_log', $insert_log);

									header("Content-Type: text/html; charset=UTF-8"); // For showing dutch accent as it is..
									echo $mail_body;

								}

								//updating the order table to set "printed" column value as 1 for the current row..
								/*if($api_key != 874187){*/
									//$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order['id']);
								/*}*/

								//update db row depending on the parameter passed..
								/*
								if((!isset($update_db)) || ($update_db == 0) || ($update_db == null) || ($update_db == '')){
									$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order['id']);
								}*/
								if($update_db == "print"){
									$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order['id']);
								}
							}
						}else{
							// IF NO RECORDS FOUND TO PRINT
							show_404();
							exit;

						}

					}
				}
			}
		} else {
				//nothing found for this api id..
				//echo "nothing found";
		}
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


	function get_id($entry) {
		return $entry['id'];
	}

	/**
	 * Function used to generate TEST mail content on any order for a company admin and then passed to mail2print app to genrate the mail without showing window print dialogue box.
	 *
	 * @param int $font_size font size to be set for this test print
	 * @param int $num_of_orders number of orders for test order report
	 *
	 * @return null returns nothing but echo the whole mail content for an order for a company admin
	 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
	 */
	public function test_print_order_mails($font_size = 12, $num_of_orders = 2){
		$this->load->library('utilities');

		$getMail = file_get_contents( base_url().'assets/mail_templates/new_order_success_mail_print_test.html' );

		$mail_body = '';
		$order_row = '';

		if($num_of_orders > 0 ){
			for($i = 0; $i < $num_of_orders; $i++){
				$order_row .= '<tr>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="10%" align="center">1</td>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="30%">Productnaam</td>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="15%" align="center">10&nbsp; &euro;</td>
							<td style="border-bottom:1px solid #ccc; padding:5px 0;" valign="top" width="35%" align="left">--</td>
							<td width="8%" valign="top" style="border-bottom:1px solid #ccc; padding:5px 0;" align="right">10&euro;</td>
							</tr >';
			}
		}

		$order_total = $num_of_orders * 10;

		$parse_email_array = array(
				"font_size" => "font-size: ".$font_size."pt ",
				"order_row" => $order_row,
				"order_total" => $order_total
		);

		$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );

		echo $mail_body;
	}

	/**
	 * Function used to generate mail content on any order for a company admin and then passed to a JAVA App to genrate the mail without showing window print dialogue box.
	 *
	 * @author Priyanka Srivastava <priyankasrivastava@cedcoss.com>
	 *
	 * @param mixed $api_key api_id provided to any company admin
	 *
	 * @return null returns nothing but echo the whole mail content for an order for a company admin
	 *
	 */
	public function print_cp_mails_v_2($api_key = null, $font_size = 12, $today_or_all = 1, $order_id = 0 ){


		if($api_key && !$order_id){

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

									$subadminsId = implode(',', array_map(array("Welcome","get_id"), $subadminsIds));

									//foreach ($subadminsIds as $subadminsId){
									if($today_or_all == 2){
										//$res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0  AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
										$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `print_ready` = 1 AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
									}else{
										// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
										$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` IN (".$subadminsId.") AND `printed` = 0 AND `print_ready` = 1 ORDER BY `id` DESC LIMIT 1")->result_array();
									}

								}
							}else{
								if($today_or_all == 2){
									$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `print_ready` = 1 AND ( `order_pickupdate` = '".date('Y-m-d', time())."' OR `delivery_date` = '".date('Y-m-d', time())."') ORDER BY `id` DESC LIMIT 1")->result_array();
								}else{
									// $res_order = $this->db->query("SELECT * FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `created_date` LIKE '".date('Y-m-d', time())."%' ORDER BY `id` DESC LIMIT 1")->result_array();
									$res_order = $this->db->query("SELECT `orders`.`id` FROM `orders` WHERE `company_id` = '".$row->company_id."' AND `printed` = 0 AND `print_ready` = 1 ORDER BY `id` DESC LIMIT 1")->result_array();
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
		elseif ($api_key && $order_id && is_numeric($order_id)){

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
								'meta' => $api_key.'=='.$font_size.'=='.$today_or_all.'=='.$action,
								'order_details' => json_encode($order_details_data),
								'date' => date("Y-m-d H:i:s")
						);

						$this->db->insert('print_master_log', $insert_log);

						$res_order_updated = $this->db->query("UPDATE `orders` SET `printed` = '1' WHERE `orders`.`id` = ".$order_id);

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

	function update_shop_via_fdd(){
		if(isset($_POST) && !empty($_POST)) {
			$this->fdb->from('login_via_obs');
			$this->fdb->where(array('unique_str'=>$_POST['unique_str']));
			$query = $this->fdb->get()->result();
			if(!empty($query)){
				$this->fdb->delete('login_via_obs',array('id'=>$query[0]->id));

				$this->load->library('shop');

				$product_id = $_POST['id'];

				if(is_array($product_id) && !empty($product_id)){
					$this->db->distinct();
					$this->db->select('products.company_id');
					$this->db->where('is_obs_product',0);
					$this->db->where_in('fdd_pro_id',$product_id);
					$this->db->join('products','fdd_pro_quantity.obs_pro_id=products.id');
					$cp_prod_ids = $this->db->get('fdd_pro_quantity')->result();
				}
				if(!empty($cp_prod_ids)){
					$check_arr = array();
					foreach ($cp_prod_ids as $val){
						$companyid = $val->company_id;
						if(!in_array($companyid,$check_arr)){
							$check_arr[] = $companyid;
							$this->db->select('shop_version,parent_id,role,obsdesk_status');
							$this->db->where(array('id'=>$companyid));
							$data = $this->db->get('company')->result();
							if(!empty($data)) {
								foreach($data as $key => $value){
									$version = $value->shop_version;
									$parent_id = $value->parent_id;
									$role = $value->role;
									$this->shop->update_json_files($version,$companyid,$role,$parent_id);
								}
							}
						}
					}
				}
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */