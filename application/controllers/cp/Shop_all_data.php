<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Shop all Controller
 *
 * This is a shop all controller
 *
 * @package Bolive CP
 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
 */
class Shop_all_data extends CI_Controller{
	
	function __construct(){
	
		parent::__construct();
	}
	
	function update_json_files($shop_version = 0,$company_id = 0,$company_role = '',$company_parent_id = ''){
		$this->load->library('shop');
		$this->shop->update_json_files($shop_version,$company_id,$company_role,$company_parent_id);
	}
}

/* End of file shop_all.php */
/* Location: ./application/controllers/cp/shop_all.php */