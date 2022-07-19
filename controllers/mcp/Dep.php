<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * Data Entry Partner
 *
 * This is the controller for managing Data entry partner
 *
 * @package Dep
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 *        
 */
class Dep extends CI_Controller {
	
	var $tempUrl = '';
	var $template = '';
	
	/**
	 * This is the constructor which extends parent class's constructor
	 */
	function __construct() {
		parent::__construct ();
		$this->load->helper ( 'url' );
		
		$this->tempUrl = base_url () . 'application/views/mcp';
		$this->template = "/mcp";
		
		$this->load->model ( 'mcp/Mdep' );
		$this->load->model('mcp/Mcompanies');
		
		$current_user = $this->session->userdata ( 'username' );
		$is_logged_in = $this->session->userdata ( 'is_logged_in' );
		
		if (! $current_user || ! $is_logged_in)
			redirect ( 'mcp/mcplogin', 'refresh' );
	}
	
	/**
	 * This is the public function that shows all Partners
	 * @param int $page This is the page number for pagination
	 */
	public function index($page = null) {
		$dep_partners = $this->Mdep->get_dep ();
		
		$start = 0;
		$limit = 20;
		
		//$page = ($this->uri->segment ( 3 )) ? ($this->uri->segment ( 3 )) : 1;
		
		$this->load->library ( 'pagination' );
		
		$config ['base_url'] = base_url () . 'mcp/dep';
		$config ['total_rows'] = count ( $dep_partners );
		$config ['per_page'] = $limit;
		$config ['uri_segment'] = 3;
		$config ['first_link'] = _ ( 'First' );
		$config ['last_link'] = _ ( 'Last' );
		$config ['next_link'] = _ ( 'Next' ) . ' &raquo;';
		$config ['prev_link'] = '&laquo; ' . _ ( 'Prev' );
		
		$this->pagination->initialize ( $config );
		
		if ($page) {
			$start = ($page - 1) * $limit;
		}
		
		$data ['dep_partners'] = $this->Mdep->get_dep ( '', NULL, NULL, $start, $limit );
		
		$data ['tempUrl'] = $this->tempUrl;
		$data ['header'] = $this->template . '/header';
		$data ['main'] = $this->template . '/dep_partners';
		$data ['footer'] = $this->template . '/footer';
		
		$this->load->vars ( $data );
		$this->load->view ( $this->template . '/mcp_view' );
	}
	
	/**
	 * This function is used to show DEP adition form and add them
	 */
	function add() {
		if ($this->input->post ( 'add_dep' )) {
			$partner_id = $this->Mdep->add_dep ( $this->input->post () );
			
			// if($partner_id)
			
			redirect ( 'mcp/dep' );
		}
		
		$data ['tempUrl'] = $this->tempUrl;
		$data ['header'] = $this->template . '/header';
		$data ['main'] = $this->template . '/add_dep';
		$data ['footer'] = $this->template . '/footer';
		
		$this->load->vars ( $data );
		$this->load->view ( $this->template . '/mcp_view' );
	}
	
	/**
	 * This function is used to edit DEP
	 * @param int $partner_id This is the ID of DEP, that is going to be edited
	 */
	function edit($partner_id = null) {
		if ($this->input->post ( 'update_dep' )) {
			$data = $this->input->post ();
			$isUpd = $this->Mdep->update_dep ( $partner_id, $data );
			redirect ( 'mcp/dep' );
		}
		
		$partner = $this->Mdep->get_dep ( array (
				'id' => $partner_id 
		) );
		$data ['dep'] = $partner [0];
		
		$data ['tempUrl'] = $this->tempUrl;
		$data ['header'] = $this->template . '/header';
		$data ['main'] = $this->template . '/edit_dep';
		$data ['footer'] = $this->template . '/footer';
		
		$this->load->vars ( $data );
		$this->load->view ( $this->template . '/mcp_view' );
	}
	
	/**
	 * This function is used to delete particular DEP
	 * @param int $partner_id This is the ID of partner that is going to deleted
	 */
	function delete($partner_id = null) {
		if (! empty ( $partner_id )) {
			$isDel = $this->Mdep->delete_partner ( array (
					'id' => $partner_id 
			) );
		}
		redirect ( 'mcp/dep' );
	}
	
	function validate() {
		// print_r($this->input->post());
		$data = '';
		
		if ($this->input->post ( 'check' ) == 'email') {
			$partners = $this->MPartners->get_partners ( array (
					'p_email' => $this->input->post ( 'email' ) 
			) );
			
			if (empty ( $partners )) {
				$data = 'notexist';
			} else {
				$data = 'duplicate';
			}
		}
		
		if ($this->input->post ( 'check' ) == 'username') {
			$partners = $this->MPartners->get_partners ( array (
					'p_username' => $this->input->post ( 'username' ) 
			) );
			
			if (empty ( $partners )) {
				$data = 'notexist';
			} else {
				$data = 'duplicate';
			}
		}
		
		echo json_encode ( array (
				"RESULT" => $data 
		) );
	}
	
	/**
	 * This function is used to fetch all companies added by people from Potral
	 */
	function companies($page = null){
		$dep_companies = $this->Mdep->get_companies ();
		
		$start = 0;
		$limit = 20;
		
		//$page = ($this->uri->segment ( 3 )) ? ($this->uri->segment ( 3 )) : 1;
		
		$this->load->library ( 'pagination' );
		
		$config ['base_url'] = base_url () . 'mcp/dep/companies';
		$config ['total_rows'] = count ( $dep_companies );
		$config ['per_page'] = $limit;
		$config ['uri_segment'] = 3;
		$config ['first_link'] = _ ( 'First' );
		$config ['last_link'] = _ ( 'Last' );
		$config ['next_link'] = _ ( 'Next' ) . ' &raquo;';
		$config ['prev_link'] = '&laquo; ' . _ ( 'Prev' );
		
		$this->pagination->initialize ( $config );
		
		if ($page) {
			$start = ($page - 1) * $limit;
		}
		
		$data ['dep_companies'] = $this->Mdep->get_companies ( '', NULL, NULL, $start, $limit );
		
		$data ['tempUrl'] = $this->tempUrl;
		$data ['header'] = $this->template . '/header';
		$data ['main'] = $this->template . '/dep_companies';
		$data ['footer'] = $this->template . '/footer';
		
		$this->load->vars ( $data );
		$this->load->view ( $this->template . '/mcp_view' );
	}
	
	/**
	 * This function is used show company details that is enterd by DEPs from portal
	 * @param int $companId this is the Id of that company...
	 */
	function company_view($companId = null){
		if($companId && is_numeric($companId)){
			$this->load->model('mcp/Mcountry');
			$this->load->model('mcp/Mcompany_type');
			$data['company_type'] = $this->Mcompany_type->select();
			$data['country'] = $this->Mcountry->select();
			$data['days'] = $this->db->get("days")->result();
			
			$data ['dep_company'] = $this->Mdep->get_companies ( array("id" => $companId) );
			
			$data ['tempUrl'] = $this->tempUrl;
			$data ['header'] = $this->template . '/header';
			$data ['main'] = $this->template . '/dep_company_view';
			$data ['footer'] = $this->template . '/footer';
			
			$this->load->vars ( $data );
			$this->load->view ( $this->template . '/mcp_view' );
		}else{
			redirect(base_url()."mcp/dep/companies");
		}
		
	}
	
	/**
	 * This function is used to approve company information
	 * 
	 * After Approving company's CP will be created with auto-generated username and password
	 * @param int $companyId This is the ID of the company that has to be approved.
	 */
	function approve($companyId = null){
		if($companyId && is_numeric($companyId)){
			
			$dep_company = $this->Mdep->get_companies ( array("id" => $companyId) );
			
			if(!empty($dep_company)){
			
				$dep_company = $dep_company['0'];
				
				$a['company_slug'] 			= 	$this->create_slug($dep_company->company_name);
				$a['company_name'] 			= 	$dep_company->company_name;
				$a['type_id'] 				=	$dep_company->type_id;
				$a['first_name']			=	$dep_company->first_name;
				$a['last_name']				=	$dep_company->last_name;
				$a['email']					=	$dep_company->email;
				$a['phone']					=	$dep_company->phone;
				$a['address']				=	$dep_company->address;
				$a['city']					=	$dep_company->city;
				$a['country_id']			=	$dep_company->country_id;
				$a['username']				=	$this->create_username($dep_company->company_name);
				$a['password']				=	$this->create_password($dep_company->company_name);
				$a['packages_id']			=	0;
				$a['ac_type_id']			=	1;
				$a['registered_by']			=	"user";
				 
				if($dep_company->have_website)
				{
					$a['have_website'] 		=	$dep_company->have_website;
				}
				else
				{
					$a['have_website'] 		= 	0;
					$a['package'] 			=	'starter';
					$a['domain'] 			=	$dep_company->domain;
					$a['canregister'] 		=	$dep_company->canregister;
				}
				
				if($dep_company->domain == '')
				{
					$a['website']			=	$dep_company->website;
				}
				
				$a['email_ads']				=	0;
				$a['footer_text']			=	0;
				$a['registration_date']		=	$dep_company->date;
				
				$a['zipcode']				=	$dep_company->zipcode;
				
				if($dep_company->as_supercompany)
				{
					$a['role'] 				=	'super';
				}
				else
				{
					$a['role'] 				=	'master';
				}
				
				$a['login_first_time']		=	'1';
				$a['approved'] 				=	'1';
				$a['from_bo'] 				=	'1';
				
				$address = $dep_company->address." ".$dep_company->zipcode." ".$dep_company->city." ".( ($dep_company->country_id == "21")?"BELGIE":"NEDERLAND" );
				$this->load->helper("geolocation");
				$location = get_geolocation($address);
				$a["geo_location"] = json_encode($location);

				$company_id 				=	$this->Mcompanies->insert($a);
				 
				if($company_id){
				
					/**
					 * Doing default settings for the company
					 */
					//$this->load->helper('default_setting');
					do_settings($company_id,$a['company_name']);
				
					/*  >>>>> MAIL HAS TO BE SEND <<<<<<<<<<<<<<*/
					/* Edited CARL */	
					//$this->load->library('utilities');
					$this->load->helper('phpmailer');
					
					$mail_data['username'] = $a['username'];
					$mail_data['password'] = $a['password'];
					$mail_data['portal_url'] = $this->config->item("portal_url");
					$mail_data['company_slug'] = $a['company_slug'];
					
					/**
					 * Fetching company typr slug
					 */
					$company_types = explode("#",$a['type_id']);
					if(!empty($company_types))
						$company_type_id = $company_types[0];
					else 
						$company_type_id = 1;
					
					$this->load->model('mcompany_type');
					$company_type_info = $this->mcompany_type->get_company_type(array('id' => $company_type_id));
					if(!empty($company_type_info))
						$mail_data['company_type_slug'] = $company_type_info[0]->slug;
					
					/*$getMail = file_get_contents( base_url().'assets/mail_templates/rp_register_company.html' );
					
    				$mail_p_1 = _("Dear,");
					$mail_p_3 = _("We launched a portalsite called <a href=\"http://www.bestelonline.nu\">Bestelonline.nu</a> and we just added your info regarding your shop right here: ");
					$mail_p_3 .= "<a href=\"http://www.bestelonline.nu/<companyname>\">http://www.bestelonline.nu/<companyname></a> ";
    				$mail_p_4 = _("You can manage every aspect of that detailpage easy for FREE");
    				$mail_p_4 .= _("You can login at");
    				$mail_p_4 .= "<a href=\"".base_url()."cp\">".base_url()."cp</a> ";
			    	$mail_p_4 .= _("with login");
    				$mail_p_4 .= ": <b>".$a['username']."</b> / <b>".$a['password']."</b> ";
			    	$mail_p_4 .= _("(don't lose this info)");
			    	$mail_h_2 = _("Wait a minute.. this is spam right");
			    	$mail_p_5 = _("Not at all. We are sending this mail to you personally to notify you that we have been working very hard the last 4 years on a portal and unique ordersystem build for shopowners like you. What makes it unique is that is build up with the latest techniques and we even can implement a webshop in your existing website by adding a few codes (");
    				$mail_p_5 .= _("check the video at");
    				$mail_p_5 .= " <a href=\"www.onlinebestelsysteem.net\">www.onlinebestelsysteem.net</a>)<br>";
					$mail_p_5 .= _("Wouldn't it be fantastic if you could have a full featured webshop for your clients at a low price of 19/mnth (no commissions) and where you don't have to look at your PC to handle your orders? Well.. we have it.");
    				$mail_h_3 = _("Hey.. wait a minute - do I have to pay anything here or even in the future");
    				$mail_p_6 = _("Not at all. The detailpage you see is for free for always and is meant to let your clients find info about your company very quickly when searching for keywords like <type> <companyname> (high searchengine rankings). Offcourse if you want to have a webshop in the near future you can upgrade your account. Please check our (cheap) plans: ");
    				$mail_p_6 .= "<a href=\"".$this->config->item("portal_url")."services\">".$this->config->item("portal_url")."services</a> ";
    				$mail_h_4 = _("I don't need any webshop mate - my clients are emailing me already.");
    				$mail_p_7 = _("That's right - but you still have to spend hours/day or week before your desktop to reply, manage all orders seperatly right? Also you have to collect all emailaddresses, names, phonenumbers seperatly if you want to send them a mail or contact them individually. With our system you don't even have to look at your PC as all orders can be printed out immediately after they came in and in case you want to send a mail to your clients, an advanced mailmanager is build in. A promotion or holiday? Just setup your mail and send it to everyone in 5 seconds. .. and a lot more advantages the system has.");
    				$mail_h_5 = _("Sounds interesting - but I still have a question....");
    				$mail_p_8 = _("If you are interested in our system we prefer a personal approach by having a meeting on some day (without any obligations). Please call us at 0473/250528 or fill in the form at ");
    				$mail_p_8 .= "<a href=\"".$this->config->item("portal_url")."contact_us\">".$this->config->item("portal_url")."contact_us</a> ";
    				$mail_p_8 .= _("so we can help you further");
    				$mail_p_9 = _("Some FAQS you can also find at ");
			    	$mail_p_9 .= "<a href=\"".$this->config->item("portal_url")."help\">".$this->config->item("portal_url")."help</a>";
    				$mail_p_10 = _("Don't hesitate - participate!");
				
				    $parse_email_array = array(
	    						"mail_p_1" => $mail_p_1,
	    						"mail_p_2" => $mail_p_2,
	    						"mail_p_3" => $mail_p_3,
				    			"mail_p_4" => $mail_p_4,
				    			"mail_p_5" => $mail_p_5,
	    						"mail_p_6" => $mail_p_6,
				    			"mail_p_7" => $mail_p_7,
				    			"mail_p_8" => $mail_p_8,
				    			"mail_p_9" => $mail_p_9,
    							"mail_p_10" => $mail_p_10,
    							"mail_h_1" => $mail_h_1,
    							"mail_h_2" => $mail_h_2,
    							"mail_h_3" => $mail_h_3,
				    			"mail_h_4" => $mail_h_4,
				    			"mail_h_5" => $mail_h_5
				    );
				
				    $mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );*/
				    $mail_body = $this->load->view('mail_templates/rp_register_company', $mail_data, true );
				
				    $mail_to_company = send_email( $a['email'], "info@bestelonline.nu", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'no_reply_bo', 'company', 'approval_message');
				    //$mail_to_company = send_email( "shyammishra@cedcoss.com", "info@bestelonline.nu", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'no_reply_bo', 'company', 'approval_message');
				
				}
				
				$dep_detail = array();
				$dep_detail['dep_first_name'] = $dep_company->dep_first_name;
				$dep_detail['dep_last_name']  = $dep_company->dep_last_name;
				$dep_detail['dep_email'] 	  = $dep_company->dep_email;
				$dep_detail['dep_username']   = $this->create_username($dep_company->dep_first_name);
				$dep_detail['dep_password']   = $this->create_password($dep_company->dep_first_name);
				$dep_detail['dep_status'] 	  = 1;
				
				if($this->Mdep->add_dep($dep_detail)){
					
					/*$getMail = file_get_contents( base_url().'assets/mail_templates/dep_register_mail.html' );
					
					$m_p_1 = _("Dear,");
					$m_p_4 = _("We are impressed by your contribution to add companies in out portal and we want to collaborate with you in future also.");
					$m_p_5 = _("So we have created a ");
					$m_p_5 .= "<a href='".base_url()."dep'>"._("Control Panel");
					$m_p_5 .= _(" for you to add more companies and earn some dime.");
					$m_p_6 = _("Your login details are right here:");				
					$m_p_7 = _("Username").": ".$a['username'];
					$m_p_8 = _("Password").": ".$a['password'];
					$m_p_9 = _("(don't lose this info)");
					$m_p_10 =_("You can change your username and password after login.");			
					
					$parse_email_array = array(
							"m_p_1" => $m_p_1,
							"m_p_2" => $m_p_2,
							"m_p_3" => $m_p_3,
							"m_p_4" => $m_p_4,
							"m_p_5" => $m_p_5,
							"m_p_6" => $m_p_6,
							"m_p_7" => $m_p_7,
							"m_p_8" => $m_p_8,
							"m_p_9" => $m_p_9,
							"m_p_10" => $m_p_10
					);
					
					$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );*/
					
					$mail_body = $this->load->view( 'mail_templates/dep_register_mail', $dep_detail, true );
					$mail_to_dep = send_email( $dep_detail['dep_email'], "info@bestelonline.nu", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'no_reply_bo', 'dep' , 'approval_message');
					//$mail_to_dep = send_email( "shyammishra@cedcoss.com", "carl@onlinebestelsysteem.net", "Bestelonline.nu", $mail_body);
					
				}
				
				if($company_id){
					$this->db->delete("dep_entry",array("id" => $companyId));
				}
				
				if( $company_id && $mail_to_company && $mail_to_dep){
					$this->session->set_flashdata("result","success");
					$this->session->set_flashdata("message",_("Company has been approved successfully. Mails are sent to CP and DEP for their CPs"));
				}else{
					$this->session->set_flashdata("result","error");
					$this->session->set_flashdata("message",_("Company has not been approved successfully. Please try again or Contact to developer."));
				}
			} 
		}
		
		redirect(base_url()."mcp/dep/companies");
	}
	
	/**
	 * This function is used to disapprove company details entered by DEP from portal
	 * @param int $companyId This is the ID of company which has to be disapproved
	 */
	function disapprove($companyId = null){
		if($companyId && is_numeric($companyId)){
			$dep_company = $this->Mdep->get_companies ( array("id" => $companyId) );
			if( $this->db->delete("dep_entry",array("id" => $companyId)) ){
				
				// Sending mail to DEP for disapproval
				$dep_company = $dep_company['0'];
				$dep_detail['dep_first_name'] = $dep_company->dep_first_name;
				$dep_detail['dep_last_name']  = $dep_company->dep_last_name;
				$dep_detail['dep_email'] 	  = $dep_company->dep_email;
				
				$this->load->helper('phpmailer');
				$mail_body = $this->load->view( 'mail_templates/dep_disapproval_mail', $dep_detail, true );
				$mail_to_dep = send_email( $dep_detail['dep_email'], "info@bestelonline.nu", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'no_reply_bo', 'dep' , 'disapproval_message');
				//$mail_to_dep = send_email( "shyammishra@cedcoss.com", "info@bestelonline.nu", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'no_reply_bo', 'dep' , 'disapproval_message');
				
				$this->session->set_flashdata("result","success");
				$this->session->set_flashdata("message",_("Company has been disapproved successfully."));
			}else{
				$this->session->set_flashdata("result","error");
				$this->session->set_flashdata("message",_("Company has not been disapproved successfully. Please try again or Contact to developer."));
			}
		}
		redirect(base_url()."mcp/dep/companies");
	}
	
	/**
	 * This private function is used to create company slug
	 * 
	 * @access private
	 * @param string $companyname
	 * @return string $slug_str
	 */
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
	
	/**
	 * This private function is used to create username
	 * 
	 * @access private
	 * @param string $string It is string for which function create username
	 * @return string $username It is the final created username
	 */
	private function create_username($string = null){
		$this->load->helper('string'); 
		$username = $string.random_string('alnum',6);
		$found = true;
		while($found){
			$info = $this->Mcompanies->get_company(array('username'=>$username));
			if(!empty($info))
				$username = $string.random_string('alnum',6);
			else
				$found = false;
		} 
		return $username;
	}
	
	/**
	 * This private function is used to create password
	 * 
	 * @access private
	 * @param string $string It is string for which function create username
	 * @return string $password It is the final created password
	 */
	private function create_password($string = null){
		$this->load->helper('string');
		$password = $string.random_string('alnum',6);
		return $password;
	}
}
?>