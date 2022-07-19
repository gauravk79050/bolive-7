<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Printing
 *
 * This is a controller for showing FLyers added by MCP and let CPs to order them
 *
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 * @package Printing
 */
class Printing extends CI_Controller{

	var $rows_per_page = '';
	var $company_id = '';
	var $ibsoft_active = false;

	function __construct(){

		parent::__construct();

		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('cookie');

		$this->load->library('session');
		$this->load->library('utilities');
		$this->load->library('Messages');

		$this->load->model('Mcompany');
		$this->load->model('Mcalender');
		$this->load->model('mcp/Mflyers');

		$this->lang_u = get_lang( $_COOKIE['locale'] );

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

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}

	/**
	 * This function is used to fetch and show all saved report on the server for current company
	 */
	function index(){

		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender

		$data['flyers'] = $this->Mflyers->get();

		$data['content']="cp/printing";
		$this->load->view('cp/cp_view',$data);

	}

	function make_order()
	{
		$flyer_id = $this->input->post('tip_id');
		if($flyer_id && is_numeric($flyer_id)){

			$flyer_info = $this->Mflyers->get('',array('id' => $flyer_id));

			$mail_data['company_name'] = $this->company->company_name;
			$mail_data['flyer_name'] = $flyer_info['0']->name;
			$mail_data['flyer_id'] = $flyer_info['0']->id;
			$mail_data['admin_name'] = "Admin";
			$body = $this->load->view('mail_templates/'.$this->lang_u.'/flyer_order',$mail_data,true);

			$query = send_email($this->config->item('site_admin_email'), $this->company->email, _("OBS: Buying Flyers Request"), $body, NULL, NULL, NULL, 'company', 'site_admin', 'flyers_orders');
			if($query){
				echo _("You request has been sent successfully");
			}else{
				echo _("Sorry!!!. Request has not been sent. Please try again later.");
			}

		}else{
			echo _("Sorry! Flyer Id not found. Please try again");
		}
	}

}

?>