<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This controller is for managing the allergens words added by mcp.
 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
 *
*/
class Allergenswords extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('mcp/mallergens');
		$this->load->helper('html');

		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');
			
		if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
	}
	
	function index(){
		$data['content'] = $this->mallergens->get_allergens();
		$data['header'] = 'mcp/header';
		$data['main'] = 'mcp/allergenswords';
		$data['footer'] = 'mcp/footer';
		$this->load->vars($data);
		$this->load->view('mcp/mcp_view');
	}
	
	function allergens_add($a_id = NULL){
		$this->load->library('form_validation');
		if($this->input->post('aller_add')){			
			$this->form_validation->set_rules('allergens', 'Allergens Words', 'required');
	
			if($this->form_validation->run() != FALSE){
				$aller['allergens'] = $this->input->post('allergens');
				$result = $this->mallergens->insert($aller);
				
				if($result==1){
					redirect(base_url().'mcp/allergenswords');
				}
			}
		}	
		$data['header'] = 'mcp/header';
		$data['main'] = 'mcp/allergenswords_add';
		$data['footer'] = 'mcp/footer';
		$this->load->vars($data);
		$this->load->view('mcp/mcp_view');
	}
	
	function update(){
		$id = $this->input->get('id');
		$aller = $this->input->get('aller');
		if($id != NULL && $aller != NULL){
			$this->mallergens->update_allergens($id, $aller);
		}
		redirect(base_url().'mcp/allergenswords');
	}
	
	function delete($a_id = NULL){	
		if($a_id != NULL){
			$this->mallergens->delete_allergens($a_id);
		}
		redirect(base_url().'mcp/allergenswords');
	}
}