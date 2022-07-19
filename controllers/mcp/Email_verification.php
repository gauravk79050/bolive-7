<?php

	class Email_verification extends CI_Controller
	{		
		var $tempUrl = '';
		var $template = '';
		
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			
			$this->template = "mcp/";
			$this->load->model($this->template.'Memail_verification');
			
			$current_user = $this->session->userdata('username');
			$is_logged_in = $this->session->userdata('is_logged_in');
			
			if( !$current_user || !$is_logged_in )
			  redirect('mcp/mcplogin','refresh');
		}
		
		function index()
		{
			
			if($this->input->post('admin_submit')){
				
				$config['upload_path'] = dirname(__FILE__).'/../../../assets/mcp/csv-import/';
				$config['allowed_types'] = 'csv';
				
				$this->load->library('upload', $config);
				
				if ( ! $this->upload->do_upload('admin_csv'))
				{
					$error = array('error' => $this->upload->display_errors());
				
					$this->session->set_flashdata('upload_error',$error['error']);
					redirect('mcp/email_verification');
				}
				else
				{
					$uploaded_data = $this->upload->data();
					$file = fopen($uploaded_data['full_path'],"r");
					$incorrect_emails = array();
					$incorrect_contents = array();
					while(! feof($file))
					{
						$elements = fgetcsv($file);
						if($elements[1] != 'result' && $elements[1] != '200' && $elements[1] != '207' && $elements[1] != '215' && $elements[1] != '316'){
							if(isset($elements) && isset($elements[0]) && $elements[0] != ''){
								$incorrect_contents[] = array(
									'email' => $elements[0],
									'code' => $elements[1],
									'message' => $elements[2],
									'date' => date("Y-m-d H:i:s")
								);
								$incorrect_emails[] = $elements[0];
							}
								
						}
					}
					
					if(!empty($incorrect_contents)){
						$this->db->insert_batch('email_verification', $incorrect_contents);
					}
				}
				
			}elseif($this->input->post('delete_selected')){
				$company_ids = $this->input->post('bulk_email');
				
				if(!empty($company_ids)){
					$this->load->model($this->template.'mcompanies');
					foreach ($company_ids as $company_id){
						$email = $this->Memail_verification->get_email($company_id);
						if($email)
							$this->Memail_verification->delete($email);
						$result = $this->mcompanies->delete($company_id);
					}
				}
			}
			elseif($this->input->post('keep_selected'))
			{
				$company_ids = $this->input->post('bulk_email');
					
				if(!empty($company_ids))
				{
					$this->load->model($this->template.'mcompanies');
					foreach ($company_ids as $company_id)
					{
						$email = $this->Memail_verification->get_email($company_id);
						if($email)
							$this->Memail_verification->delete($email);
					}
				}
			}
			
			$data['content'] = $this->Memail_verification->get_company_infos();
			$data['client'] = $this->Memail_verification->get_client_infos();
			$data['tempUrl']=$this->tempUrl;		
			$data['header'] = $this->template.'header';
			$data['main'] = $this->template.'email_verification_view';
			$data['footer'] = $this->template.'footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');  
		}	

		/**
		 * This function is used to change email of company
		 * 		This function needs email and company ID and it returns array with message of success/failure
		 */
		function change_email(){
			$new_email = $this->input->post('new_email');
			$company_id = $this->input->post('company_id');
			$response = array('error' => 1, 'message' => _('Email did not updated successfully!!!'));
			if($new_email && $company_id){
				$result = $this->Memail_verification->change_email($new_email, $company_id);
				//$result = 1;
				if($result){
					$response = array('error' => 0, 'message' => _('Email updated successfully'));
				}
			}
			echo json_encode($response);
		}
		
		/**
		 * This function is used to delete given company and all its components
		 */
		function delete(){
			$response = array('error' => 1, 'message' => _('Company can not be deleted. please try again'));
			$company_id = $this->input->post('comapny_id');
			if($company_id){
				$this->load->model($this->template.'mcompanies');
				$result1 = $this->mcompanies->delete($company_id);
				
				$email = $this->input->post("email");
				$result2 = $this->Memail_verification->delete($email);
				
				if($result1 && $result2)
					$response = array('error' => 0, 'message' => _('Company deleted successfully'));
			}
			else 
			{
				$response = array('error' => 1, 'message' => _('Company can not be deleted. please try again'));
			}
			
			echo json_encode($response);
			
		}
		
		/**
		 * This function is used to keep given company
		 */
		function keep(){
			$response = array('error' => 1, 'message' => _('Company can not be keeped. please try again'));
			$email = $this->input->post("email");
			if($email){
				$result = $this->Memail_verification->delete($email);
			
				if($result)
					$response = array('error' => 0, 'message' => _('Company keeped successfully'));
			}
				
			echo json_encode($response);
		}
		
		/**
		 * This function is used to check for the email validation through API
		 * @link http://www.email-validator.net/
		 * @param string $Email email to validate
		 * @return array an array containing the response from API (valid or invalid along with details)
		 */
		function email_verify_api($Email = null){

			$response_array = array("error" => 0, "message" => "", "details" => "");
			if($Email){
			
				// build API request
				$APIUrl = 'http://www.email-validator.net/api/verify';
				$Params = array('EmailAddress' => $Email,
						'APIKey' => '8609834f9ea2da6326ec1f27a4164d1c');
				$Request = @http_build_query($Params);
				$ctxData = array(
						'method' => "POST",
						'header' => "Connection: close\r\n".
						"Content-Length: ".strlen($Request)."\r\n",
						'content'=> $Request);
				$ctx = @stream_context_create(array('http' => $ctxData));
				
				// send API request
				$result = json_decode(@file_get_contents(
						$APIUrl, false, $ctx));
				
				// check API result
				if ($result && $result->{'status'} > 0) {
					switch ($result->{'status'}) {
						// valid addresses have a {200, 207, 215} result code
						case 200:
						case 207:
						case 215:
							$response_array = array("error" => 0, "message" => "Address is valid", "details" => '');
							break;
						default:
							$response_array = array("error" => 1, "message" => $result->{'info'}, "details" => $result->{'details'});
							break;
					}
				} else {
					$response_array = array("error" => 1, "message" => $result->{'info'}, "details" => "");
				}
			}
			return $response_array;
		}

	}	 	
?>