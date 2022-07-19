<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Shop all Controller
 *
 * This is a shop all controller
 *
 * @package Bolive CP
 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
 */
class Shop_all extends CI_Controller{
	
	function __construct(){
	
		parent::__construct();
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			$is_logged_in = $this->session->userdata('is_logged_in');
			if(!$is_logged_in){
				redirect('cp/login');
			}
		}
	}
	
	function update_json_files($shop_version = 0,$company_id = 0,$company_role = '',$company_parent_id = ''){
		$this->load->library('shop');
		
		$company_id = ($company_id)?$company_id:$this->session->userdata('cp_user_id');
		$company_role = ($company_role != '')?$company_role:$this->session->userdata('cp_user_role');
		$company_parent_id = ($company_parent_id != '')?$company_parent_id:$this->session->userdata('cp_user_parent_id');
		
		echo $this->shop->update_json_files($shop_version,$company_id,$company_role,$company_parent_id);
	}
	
	function update_desk_files($infodesk_status = 0,$company_id = 0){
		$this->load->library('desk');
		
		$company_id = ($company_id)?$company_id:$this->session->userdata('cp_user_id');
		
		$this->desk->update_desk_files($infodesk_status,$company_id);
	}
	
	function update_allergenkart_files(){
		$this->load->library('allergenkart');
	
		$company_id =$this->session->userdata('cp_user_id');
	
		$this->allergenkart->update_allergenkart_json_files($company_id);
	}
}

/* End of file shop_all.php */
/* Location: ./application/controllers/cp/shop_all.php */