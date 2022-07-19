<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Flyers
 * 
 * This is the pakage for MCP admin to Manage Flyers
 * 
 * @package Flyers
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */

class Flyers extends CI_Controller
{		
	var $tempUrl = '';
	var $template = '';
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		
		$this->template = "mcp/";
		$this->load->model('mcp/Mflyers');
		$current_user = $this->session->userdata('username');
	    $is_logged_in = $this->session->userdata('is_logged_in');
		
	    if( !$current_user || !$is_logged_in )
		  redirect('mcp/mcplogin','refresh');
	}
	
	/**
	 * Function to fetch fylers and list
	 */
	function index()
	{
		$data['flyers'] = $this->Mflyers->get();
		
		$data['header'] = $this->template.'header';
		$data['main'] = $this->template.'flyers';
		$data['footer'] = $this->template.'footer';
			
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}

	/**
	 * Function to add flyers
	 */
	function add($flyer_id = 0)
	{
		if($this->input->post('add'))
		{
			if($this->input->post('name') != '' && $this->input->post('price') != ''){
				
				$insert_array = array();
				$insert_array['name'] = $this->input->post('name');
				$insert_array['price'] = $this->input->post('price');
				$insert_array['description'] = $this->input->post('description');
				$insert_array['image'] = '';
				
				if($_FILES['flyer_image']){
					$config['upload_path'] = dirname(__FILE__).'/../../../assets/mcp/images/flyers/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg|GIF|JPG|PNG|JPEG';
					/*$config['max_size']	= '100';
					$config['max_width']  = '1024';
					$config['max_height']  = '768';*/
						
					$this->load->library('upload', $config);
						
					if ($this->upload->do_upload('flyer_image'))
					{
						$result = $this->upload->data();
						$insert_array['image']  = $result['file_name'];
					}
				}
				
				$insert_array['date'] = date("Y-m-d H:i:s");
				$existing_flyers = $this->Mflyers->get();
				$insert_array['display_order'] = (count($existing_flyers) + 1);
				$this->Mflyers->insert($insert_array);
				redirect(base_url().'mcp/flyers');
			}else{
				$data['error'] = _("Name or Price field is missing");
			}
		}elseif($this->input->post('update')){
			if($this->input->post('name') != '' && $this->input->post('price') != ''){
			
				$update_array = array();
				$update_array['name'] = $this->input->post('name');
				$update_array['price'] = $this->input->post('price');
				$update_array['description'] = $this->input->post('description');
			
				if($_FILES['flyer_image']){
					$config['upload_path'] = dirname(__FILE__).'/../../../assets/mcp/images/flyers/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg|GIF|JPG|PNG|JPEG';
					/*$config['max_size']	= '100';
					 $config['max_width']  = '1024';
					$config['max_height']  = '768';*/
			
					$this->load->library('upload', $config);
			
					if ($this->upload->do_upload('flyer_image'))
					{
						$result = $this->upload->data();
						$update_array['image']  = $result['file_name'];
					}
				}
			
				if($this->Mflyers->update(array('id' => $this->input->post('flyer_id')),$update_array))
					$data['success'] = _("Flyer updated successfull");
				else
					$data['error'] = _("Flyer did not updated successfull");
			}else{
				$data['error'] = _("Name or Price field is missing");
			}
		}
		
		$data['flyers_info'] = array();
		if($flyer_id){
			$flyers_info = $this->Mflyers->get('',array('id' => $flyer_id));
			if(!empty($flyers_info))
				$data['flyers_info'] = $flyers_info[0];
		}
	
		$data['header'] = $this->template.'header';
		$data['main'] = $this->template.'flyers_add';
		$data['footer'] = $this->template.'footer';
			
		$this->load->vars($data);
		$this->load->view($this->template.'mcp_view');
	}
	
	/**
	 * Function to change dislpay order of flyers
	 */
	function change_order(){
		$flyer_id = $this->input->post('flyer_id');
		$display_order = $this->input->post('order');
		if($flyer_id){
			if($this->Mflyers->update(array('id' => $flyer_id),array('display_order' => $display_order)))
				echo _("Display Order is changed successfully");
			else
				echo _("Display Order did not changed successfully");
		}else{
			echo _("Flyer ID missing");
		}
		
	}
	
	/**
	 * Function to delete Flyer
	 */
	function delete($flyer_id = 0){
		
		if($flyer_id){
			$this->Mflyers->delete($flyer_id);
		}
		
		redirect(base_url().'mcp/flyers');
	}
}
?>