<?php
/**
 * Bestelonline Controller
 * 
 * This controller is used to add settings handled by Master Admin
 * 
 * @package Bestelonline
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */

class Allergenenchecker extends CI_Controller {
	
	var $tempUrl = "";
	var $template = "";
	
	function __construct() {
		parent::__construct ();
		
		$this->load->helper ( 'url' );
		$this->load->helper ( 'form' );
		$this->tempUrl = base_url () . 'application/views/mcp';
		$this->template = "/mcp";
		
		$this->load->model($this->template."/Mallergenenchecker");
		
		$current_user = $this->session->userdata ( 'username' );
		$is_logged_in = $this->session->userdata ( 'is_logged_in' );
		
		if (! $current_user || ! $is_logged_in)
			redirect ( 'mcp/mcplogin', 'refresh' );
	}
	
	/**
	 * This function is used to fetch and show Banner images which should be display on Bestelonline.nu
	 */
	function index() {
		
		if($this->input->post()){
			if($_FILES['banner']['name']){
					  	
			  	$filename = 'allergenen_banner_'.time().'.'.end(explode(".",$_FILES['banner']['name']));
			  	$config['upload_path'] = dirname(__FILE__).'/../../../assets/mcp/images/';
			  	$config['allowed_types'] = 'gif|jpg|png|GIF|PNG|JPG|jpeg';
			  	$config['file_name']	= $filename;
			  	/*$config['max_size']	= '100';
			  	$config['max_width']  = '1024';
			  	$config['max_height']  = '768';*/
			  	
			  	$this->load->library('upload', $config);
			  	
			  	if ( ! $this->upload->do_upload("banner"))
			  	{
			  		$this->session->set_flashdata('error', $this->upload->display_errors());
			  	}
			  	else
			  	{
			  		$arr['banner'] = $filename;
			  		$arr[ 'type_id' ] = $this->input->post( 'type_id' );
					$arr['status'] = "1";
					$arr['date'] = date("Y-m-d h:i:s");
					$this->Mallergenenchecker->add($arr);
			  	}
	
				redirect(base_url().'mcp/allergenenchecker');
			}	
		}
		
		$data ['tempUrl'] 		= $this->tempUrl;
		$data ['content'] 		= $this->Mallergenenchecker->get();
		$data ['company_type'] 	= $this->Mallergenenchecker->get_company_type();

		$data ['header'] = $this->template . '/header';
		$data ['main'] = $this->template . '/allergenenchecker';
		$data ['footer'] = $this->template . '/footer';
		$this->load->vars ( $data );
		$this->load->view ( $this->template . '/mcp_view' );
	}
	
	/**
	 * This function is used to change the status of a Banner
	 */
	function change_status(){
		$response = array( "error" => 1, "message" => _("Error occured. Please try again."));
		if( $this->input->post("banner_id") ){
			if( $this->Mallergenenchecker->update( $this->input->post("banner_id"), $this->input->post("status") ) ){
				$response = array( "error" => 0, "message" => _("Status changed successfully."));
			}else{
				$response = array( "error" => 0, "message" => _("Status does not changed successfully."));
			}
		}else{
			$response = array( "error" => 0, "message" => _("Banner ID or status value is not found."));
		}
		
		echo json_encode($response);
	}
	
	/**
	 * This function is used to delete the given banner 
	 */
	function delete(){
		$response = array( "error" => 1, "message" => _("Error occured. Please try again."));
		if( $this->input->post("banner_id") ){
			if( $this->Mallergenenchecker->delete( $this->input->post("banner_id") ) ){
				$response = array( "error" => 0, "message" => _("Banner deleted successfully."));
			}else{
				$response = array( "error" => 0, "message" => _("Banner does not deleted successfully."));
			}
		}else{
			$response = array( "error" => 0, "message" => _("Banner ID is not found."));
		}
		
		echo json_encode($response);
	}
}
?>