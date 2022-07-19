<?php
	class Easybutler extends CI_Controller
	{ 
	   var $template='mcp/';
	   
	   function __construct()
	   { 
		  parent::__construct();
		  //$this->load->helper('url');
		  $this->load->helper('form');
		  $this->load->model( $this->template.'Measybutler' );
		  $current_user = $this->session->userdata( 'username' );
		  $is_logged_in = $this->session->userdata( 'is_logged_in' );
		  //$rp_user_id  	= $this->session->userdata( 'rp_user_id' );
		  if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
		}


	   	function index( ){

	   	   $data[ 'easybutlerinfo' ] = $this->Measybutler->get_all_easybutlerInfo();
		   $data['header']	= $this->template.'header';
		   $data['main']	= $this->template.'easybutler_recommendation';
		   $data['footer']	= $this->template.'footer';
		   $this->load->view( $this->template.'mcp_view', $data );
	   	}

	   	/**
	   	 * Function to add edit calibration 
	   	 *
	   	 */

	   	function addedit_easybutlerinfo( $id = '' ){
	   		if( is_numeric( $id ) ){ 													
		    	$data['easybutlerinfo'] = $this->Measybutler->get_easybutlerinfo( $id );
			} else if( $id != '' ){
				redirect( base_url( 'mcp/easybutler/' ) );
			}
			
			$data['header']	= $this->template.'header';
			$data['main']	= $this->template.'addedit_easybutlerinfo';
			$data['footer']	= $this->template.'footer';							
	        //$this->load->vars( $data );
			$this->load->view($this->template.'mcp_view', $data);
	   	}

	   	/**
	   	 * Function to update calibration
	   	 *
	   	 */
	   	function update_easybutlerinfo() {
			$data = $this->input->post();
			$result = $this->Measybutler->update_easybutlerinfo( $data );
			if( $result ){
				if( $data[ 'action' ] == 'Add' ){
	   				$this->session->set_flashdata( 'msg',_( 'Added  successfully' ) );
				} else {
	   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
				}
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/easybutler' );
		}

		/**
		 *
		 * Function to delete calibration workroom data
		 *
		 */
			function delete_easybutlerinfo() {
		   		$id 	= $this->input->post( 'id' );
		   		$result = $this->Measybutler->delete_easybutlerinfo( $id );
		   		if( $result ){
					echo "success";
					exit();
				} 
		   	}
	   	/*=====  End of Temperature workroom opperations  ======*/
	   	
    }
