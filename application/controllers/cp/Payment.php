<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Payment extends CI_Controller{
	
	function __construct(){
	
		parent::__construct();
		$this->load->helper('curo');
		
		$this->load->library('session');
		$this->load->model('Mcompany');
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}
		
		// If Online Payment in not activated then redirect to settings
		/*if(!is_payment_activate($this->session->userdata('cp_user_id')))
			redirect('cp/cdashboard/settings');*/
		
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
		$this->load->model('Mpayment');
	}
	
	
	public function payment_method(){
	
	
		$data['title']="Merchant Info";
		$data['show_form'] = 'yes';
		$data['enabled'] = 0;
		$already_registered = 0;
		
		
		if($this->input->post('already_registered')){
			$already_registered = 1;
			
		}
		if($this->input->post('save_registered')){

			$merchant_name 	= $this->input->post('merchant_name');
			$curo_id 		= $this->input->post('curo_id');
			$secret_key 	= $this->input->post('secret_key');
			$username 		= $this->input->post('username');
			$pwd			= $this->input->post('pwd');
			$site_id 		= $this->input->post('site_id');
			$site_name 		= $this->input->post('site_name');
			$site_url 		= $this->input->post('site_url');
			$site_hash_key = $this->input->post('site_hash_key');
			$insert_data = array(
								'company_id'=>$this->company_id,
								'curo_id'=>$curo_id,
								'merchant'=>$merchant_name,
								'secret'=>$secret_key,
								'username'=>$username,
								'password'=>$pwd,
								'site_id'=>$site_id,
								'site_name'=>$site_name,
								'site_url'=>$site_url,
								'site_hash_key'=>$site_hash_key);
			$this->db->insert('cp_merchant_info', $insert_data);
			$data['show_form'] = 'no';
		}
		
		$data['merchant_info_all'] = $merchant_info = $this->Mpayment->get_merchant_info($this->company_id);
		
		if($this->input->post('btn_set')){
			$selected = $this->input->post('payment_method');
			$check_entry = $this->Mpayment->check_exsistence($this->company_id);
			if($check_entry == 0){
				$this->Mpayment->insert_payment_method($selected,$this->company_id);
			}else{
				$this->Mpayment->update_selected_payment_methods($selected,$this->company_id);
			}
			
			if(in_array('5',$selected)){
				$update_array = array(
						'paypal_username' => $this->input->post('paypal_username'),
						'paypal_password' => $this->input->post('paypal_password'),
						'paypal_sign' => $this->input->post('paypal_sign')
				);
				$this->Mpayment->update_merchant_info(array('company_id' => $this->company_id), $update_array);
			}
			$data['success'] = 1;
		}
		
		
		if(!empty($merchant_info)){
			
			// die("??");
			$data['enabled'] = $merchant_info[0]['cardgate_payment'];
			$merchant_id = $merchant_info[0]['curo_id'];
			$response = check_status($merchant_id);
			if($response['msg'] == 'success'){
				$data['merchant_info'] = $response['response'];
				$data['available_methods'] = $this->Mpayment->get_available_payment_methods();
				$data['selected_methods'] = $this->Mpayment->get_selected_payment_methods($this->company_id);
			}

			

						if($response['response']['merchant']['stage'] == 'Awaiting.Merchant'){
							$data['class'] = 'label-primary';
						}elseif($response['response']['merchant']['stage'] == 'Awaiting.CDD.Officer' || $response['response']['merchant']['stage']== 'Awaiting.Compliance.Officer'){
							$data['class'] = 'label-info';
						}elseif($response['response']['merchant']['stage'] == 'Awaiting.Risk.Officer'){
							$data['class'] = 'label-warning';
						}elseif($response['response']['merchant']['stage'] == 'Cancelled'){
							$data['class'] = 'label-danger';
						}elseif($response['response']['merchant']['stage'] == 'Enabled'){
							$data['class'] = 'label-success';
						}

	 			

			$data['show_form'] = 'no';
		
	
		}
		// $status = check_status();
		if($this->input->post('submit')){
	
			$posted_data = $this->input->post();
			
			$response = create_merchant($posted_data);
			
			if($response['msg'] == 'failed'){
				$data['failed_msg'] = $response['msg'];
			}else{
				$data['success_msg'] = $response['msg'];
				$this->Mpayment->store_merchant_payment_info($response['response'],$this->company_id);
				$this->load->helper('phpmailer');
				
				/* SEND MAIL TO CP */
				$company_data = $this->Mcompany->get_company(array('id'=>$this->company_id));
				$cmp_mail_subject = _('Cardgate account created');
				$mail_body = _('Please login to your account to complete the data required to start receiving payments using cardgate');
				/*$mail_body .= '<br/>'._('Merchant ID').':'.$response['response']['id'];*/
				$mail_body .= '<br/>'._('Username').':'.$response['response']['username'];
				$mail_body .= '<br/>'._('Password').' :'.$response['response']['password'];
				$mail_body = _('It is very important that you now complete the forms and upload the required docs before proceeding. In your Controlepanel go to Merchant gegevens - CURO Backoffice and then go to Gegevens. This is the place where you need to fill in all required info<br><br>.');
				$mail_body = _('If any problems please contact us.');

				$cp_email   = $company_data[0]->email;
				send_email( $cp_email, $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
				
				/* SEND MAIL TO MCP */
				$cmp_mail_subject = _('Cardgate account created by Merchant');
				$mail_body = _('Please login to the account to set the control Url');
				$mail_body .= '<br/>'._('Merchant ID').':'.$response['response']['id'];
				$mail_body .= '<br/>'._('Username').':'.$response['response']['username'];
				$mail_body .= '<br/>'._('Password').' :'.$response['response']['password'];
				$mcp_mail = $this->config->item('site_admin_email');
				send_email( $mcp_mail, $this->config->item('site_admin_email'), $cmp_mail_subject, $mail_body);
				
			} 
			
		
			
				
		}
		
		if($already_registered==1){
			$data['content'] = 'cp/already_registered';
		}else{
			$data['content'] = 'cp/payment_view';
		}
		
		$this->load->view('cp/cp_view',$data);
	
	}
	
	
	function enable_payment($set=null){
	
		$this->db->where('company_id', $this->company_id);
		$this->db->update('cp_merchant_info', array('cardgate_payment'=>$set));
	
	}
}