<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This controller is for managing the notifations added by mcp to show to cp.
 * @author Aniket Singh <aniketsingh@cedcoss.com>
 *
 */
class Notifications extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('mnotify');
		$this->load->helper('html');
		
		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');
			
		if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
		
		
	}
	
	
	function index(){
		
		
		$data['content'] = $noties = $this->mnotify->get_notifications();
		foreach ($noties as $noti){
			$new_arr = json_decode($noti['company_type']);
			$new_arr1 = json_decode($noti['company_type_id']);
			$acc_str = '';
			$comp_type = '';
			foreach ($new_arr as $arr){
				$account = $this->mnotify->get_account($arr);
				if($acc_str != ''){
					$acc_str = $acc_str.', '.$account[0]->ac_title;
				}else{
					$acc_str = $account[0]->ac_title;
				}
			}

			foreach ($new_arr1 as $arr){
				$types = $this->mnotify->get_types($arr);
				if ($comp_type != '') {
					$comp_type = $comp_type.', '.$types[0]->company_type_name;
				}
				else{
					$comp_type = $types[0]->company_type_name;
				}
			}
			$data['ac_type'][] = $acc_str;
			$data['comp_type_id'][] = $comp_type;
		}
		
		$data['header'] = 'mcp/header';
		$data['main'] = 'mcp/notifications';
		$data['footer'] = 'mcp/footer';
		
		$this->load->vars($data);
		$this->load->view('mcp/mcp_view');
	}
	function notification_addedit($n_id = NULL){
		
		$this->load->library('form_validation');
		if($this->input->post('btn_add'))
		{
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('noties', 'Notification', 'required');
			$this->form_validation->set_rules('upto_date', 'Date', 'required');
				
			if($this->form_validation->run() != FALSE)
			{
				$dummy['subject'] = $this->input->post('subject');
				$dummy['notification'] = $this->input->post('noties');
				$dummy['company_type'] = json_encode($this->input->post('acc_type'));
				$dummy['company_type_id'] = json_encode($this->input->post('company_type_name'));
				$dummy['upto_date'] = $this->input->post('upto_date');
				$dummy['companies_lang']  		= $this->input->post('companies_language');
				$dummy['created_date'] = date('Y-m-d H:i:s');
		
				$result=$this->mnotify->insert($dummy);
		
				if($result==1)
				{
					redirect(base_url().'mcp/notifications');
				}
			}
		}
		if($this->input->post('btn_update')){
			$n_id = $this->input->post('n_id');
			
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('noties', 'Notification', 'required');
			$this->form_validation->set_rules('upto_date', 'Date', 'required');
			
			if($this->form_validation->run() != FALSE)
			{
				$dummy['subject'] 				= $this->input->post('subject');
				$dummy['notification'] 			= $this->input->post('noties');
				$dummy['company_type'] 			= json_encode($this->input->post('acc_type'));
				$dummy['company_type_id'] = json_encode($this->input->post('company_type_name'));
				$dummy['upto_date'] 			= $this->input->post('upto_date');
				$dummy['companies_lang']  		= $this->input->post('companies_language');
				$result=$this->mnotify->update($dummy, $n_id);
			
				if($result==1)
				{
					redirect(base_url().'mcp/notifications');
				}
			}
			
		}
		
		
		if($n_id != NULL){
			$data['content'] = $this->mnotify->get_notifications($n_id);
		}
		
		$data['type'] = $this->mnotify->get_account();
		$data['comp_type'] = $this->mnotify->get_types();

		$data['header'] = 'mcp/header';
		$data['main'] = 'mcp/notification_addedit';
		$data['footer'] = 'mcp/footer';
		
		$this->load->vars($data);
		$this->load->view('mcp/mcp_view');
	}
	function delete($n_id = NULL){
		
		if($n_id != NULL){
			$this->mnotify->delete_this($n_id);
		}
		redirect(base_url().'mcp/notifications');
	}
}