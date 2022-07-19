<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Expiration Cron
 * This Package is meant for auto incrementing companies expiration date and auto change the company type from PRO/BASIC to FREE after trial period is over
 *
 * @package Expiration_cron
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
class Expiration_cron extends CI_Controller
{		
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('mcp/Mcompanies');
		$this->load->model('mcp/Mpackage');
		$this->load->model('mcp/Mcountry');
		$this->load->model('mcp/Mcompany_type');
		$this->load->model('mcp/Maddon');
		$this->load->model('Mapi');
		
		$this->load->model('Mgeneral_settings');
	}
	
	/**
	 * This is the default method that will exceute via cron-job
	 */
	function index(){
		$currentMonth = date("Y-m");
		$currentDate = date("Y-m-d");
		$dateAfterTwoDay = date("Y-m-d" , strtotime( $currentDate."+2 days" ));
		 
		$currentDateTime = date("Y-m-d H:i:s");
		// ------- Extending expriy date with one year...
		$this->db->query("UPDATE `company` SET `expiry_date` = DATE_ADD( `expiry_date`, INTERVAL 1 YEAR) WHERE `expiry_date` = '".$currentDate."' ");
		
		// ------- Making company type as FREE when trial period has over
		$this->db->query("UPDATE `company` SET `ac_type_id` = 1, `on_trial` = 0 WHERE `trial` <= '".$currentDateTime."' AND `on_trial` = 1 AND `ac_type_id` <> 1");
		
		// ------- Fetching companies having only 2 days trial left
		$this->db->select('company_name,email,phone');
		$this->db->like('trial',$dateAfterTwoDay,'after');
		$this->db->where('on_trial',1);
		$companies = $this->db->get('company')->result_array();
		if(!empty($companies)){
			$this->load->library('utilities');
			$this->load->helper('phpmailer');
			$this->load->model('mcp/Memail_message');
			$email_templates = $this->Memail_message->select();
			$mail_template = $email_templates[0]->company_trial_message_basic_pro;
			$mail_subject = $email_templates[0]->company_trial_subject_basic_pro;
			
			$mail_template_mcp = $email_templates[0]->company_trial_message_basic_pro_mcp;
			$mail_subject_mcp = $email_templates[0]->company_trial_subject_basic_pro_mcp;
			foreach ($companies as $company){
				/* -------------------------------------Get direct login link -------------------------------------
				$direct_login_id = NULL;
				$company['direct_login_link'] = '';
				if($company['direct_login_id'] == NULL){
					do{
						$direct_login_id = $this->generate_unique_md5_string();
						$this->db->where('direct_login_id',$direct_login_id);
						$match_result = $this->db->get('company')->result_array();
					}while(!empty($match_result));
					$this->db->where('id',$id);
					$this->db->set('direct_login_id',$direct_login_id);
					$this->db->update('company');
				}
				else{
					$direct_login_id = $company['direct_login_id'];
				}
				$login_link = base_url().'cp/login/validate?direct_login='.$direct_login_id;
				$company['direct_login_link'] = "<a href='".$login_link ."'>".$login_link."</a>";
				/*------------------------------------- End direct login link -------------------------------------*/
				
				$mail_body = $this->utilities->parseMailText($mail_template, $company);
				$send=send_email($company['email'], $this->config->item('no_reply_email'), $mail_subject, $mail_body, NULL, NULL, NULL, 'no_reply', 'company', 'company_trial_message_basic_pro');
				
				$mail_body_mcp = $this->utilities->parseMailText($mail_template_mcp, $company);
				$send=send_email($this->config->item('site_admin_email'), $this->config->item('no_reply_email'), $mail_subject, $mail_body_mcp, NULL, NULL, NULL, 'no_reply', 'site_admin', 'company_trial_message_basic_pro');
				
			}
		}
	}
	
}	 	
?>