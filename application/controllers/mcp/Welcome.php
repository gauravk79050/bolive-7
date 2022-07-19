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
			//echo $this->input->post('type_id');
			///die();
			
			$this->load->library('form_validation');
			//echo "hello";
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
						
					$admin = $this->Madmin->get_admin();
						
					if(!empty($admin))
						$admin_email = $admin[0]->email;
					//$admin_email = "shyammishra@cedcoss.com";
					if($admin_email)
					{
							
						$this->load->library('utilities');
						$this->load->library('parser');
					
						$this->load->helper('email');
					
						$To = $admin_email; //'keertirastogi@cedcoss.com'; //admin_email
					
						if($insert_id && valid_email($To) )
						{
							$this->load->library('email');
							$From = $this->config->item("site_admin_email"); 
				   			$mailFrom = $this->config->item("site_admin_name");
								
							$subject = _('New Company Registered');
					
							$register = $this->input->post();
							$register['insert_id'] = $insert_id;
							$register['REQ_IP_ADD'] = $_SERVER['REMOTE_ADDR'];
					
							$mail_body = $this->load->view($this->mail_template.'new_company_registered', $register, true);
								
							$config['mailtype'] = 'html';
							$this->email->initialize($config);
							$this->email->from($From, $mailFrom);
							$this->email->to($To);
					
							$this->email->subject($subject);
							$this->email->message($mail_body);
								
							$this->email->send();
								
							//echo $this->email->print_debugger();
								
							$_SESSION['message'] = array('status'=>'success','response'=>_('You details are been saved successfully. And a mail has been sent to the administrator for your approval. So please wait, he will get back to you soon !'));
						}
					
					}
					else
					{
						$_SESSION['message'] = array('status'=>'success','response'=>_('You details are been saved successfully, we will get back to you soon !'));
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
			//echo 'prerna';
			$company_name = $this->input->post('comapny_name');
			$user_name = $this->input->post('user_name');
			$email = $this->input->post('email');
			$phone = $this->input->post('phone');
			$url = $this->input->post('url');
			$subject = $this->input->post('subject');
			$email_message = $this->input->post('message');
			
			$message = '<html><head><title>'._('Contact Mail').'</title></head>';
			$message .='<body>';
			$message .='<table>'; 
			$message .='<tr>';
			$message .='<td>'._('User Name').'</td>';
			$message .='<td>'.$user_name.'</td>';
			$message .='</tr>'; 
			$message .='<tr>';
			$message .='<td>'._('User phone no').'</td>';
			$message .='<td>'.$phone.'</td>';
			$message .='</tr>'; 
			$message .='<tr>';
			$message .='<td>'._('User\'s email address').'</td>';
			$message .='<td>'.$email.'</td>';
			$message .='</tr>'; 
			$message .='<tr>';
			$message .='<td>'._('User\'s Comapny url').'</td>';
			$message .='<td>'.$url.'</td>';
			$message .='</tr>'; 
			$message .='<tr>';
			$message .='<td>'._('User\'s message').'</td>';
			$message .='<td>'.$email_message.'</td>';
			$message .='</tr>'; 
			$message .='</table>'; 
			$message .='</body>'; 
			$message .='</html>'; 
			
			$captcha_field = $this->input->post('captcha_field');
			$this->messages->clear();
			//echo $this->session->userdata('captcha_field');
			if($captcha_field != $this->session->userdata('captcha_field')){
			//echo  $this->session->userdata('captcha_field').'  '.'not valid';
				$this->messages->add('error !! plese enter correct security code ','error');
				redirect('welcome/contact');
			}
			else{
				
				
				$this->load->model('Madmin');
				$admin = $this->Madmin->get_admin();
				//print_r($admin);
				if($admin != array()){
					$emailTo = $admin[0]->email;
					$config['mailtype'] = 'html';
					$this->email->initialize($config);	
					$this->email->from($email);
					$this->email->to($emailTo); 
					
					$this->email->subject($subject);
					$this->email->message($message);  
					//echo $this->email->send();
					if($this->email->send()){
						$this->messages->add('your mail request has been sent successfully ','success');
					}else{
						$this->messages->add('error in sending mail ','error');
					}
				}else{
					$this->messages->add('admin\'s email is temporary unavailable!! please try later ','success');
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
    			//'expiration' => 7200
    		);

		$data['captcha'] = $captcha =  create_captcha($vals);//there will be three keys 'word','time'.'image'
		$this->session->set_userdata(array('captcha_field' => $captcha['word']));
		//echo 'captcha testing';
		//print_r($captcha);
		//die();
		
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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */