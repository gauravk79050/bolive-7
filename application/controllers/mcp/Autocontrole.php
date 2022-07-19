<?php
	class Autocontrole extends CI_Controller
	{
	   var $template='mcp/';
	   
	   function __construct()
	   {
		  parent::__construct();
		  //$this->load->helper('url');
		  $this->load->helper('form');
		  $this->load->model( $this->template.'Mautocontrole' );
		  $current_user = $this->session->userdata( 'username' );
		  $is_logged_in = $this->session->userdata( 'is_logged_in' );
		  //$rp_user_id  	= $this->session->userdata( 'rp_user_id' );
		  if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
		}
	 
	    /**
	    *  Function to list all categories
	    */
	    function categories( )
	    {
			/*if( $this->input->post('search') )
			{
		  	   $this->search=$this->input->post();
			   $data[ 'autocontrole_categories' ] = $this->Mautocontrole->select($this->search);
			}
		    else
			{*/
			   $result = $this->Mautocontrole->getallTypes( );
			   $data[ 'autocontrole_categories' ] = $result[ 'autocontrole_type' ];
			//}
			
		   $data['header']= $this->template.'header';
		   $data['main']= $this->template.'autocontrole_categories';
		   $data['footer']= $this->template.'footer';
		   //$this->load->vars( $data );
		   $this->load->view( $this->template.'mcp_view', $data );	
	   }

	   /**
	    *  Function to add new categories and list categories and edit
	    */
	   function autocontrole_categories_addedit( ){
	   		if( $this->input->post( 'action' ) == 'ADD TYPE' ){
	   		//add type
		        $data = $this->input->post( );
		        unset( $data[ 'action' ] );
		        $result = $this->Mautocontrole->add_type( $data );
		        redirect( base_url().'mcp/autocontrole/categories' );
		        
		    }else{ 																	// edit type
		    	$type_id 		= $this->uri->segment( 4 );
		    	$result 		= $this->Mautocontrole->get_type( $type_id );
		    	$data[ 'type' ] =  $result[ 'autocontrole_type' ];
				$data['header']	= $this->template.'header';
				$data['main']	= $this->template.'autocontrole_type_add';
				$data['footer']	= $this->template.'footer';
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);	
			}
	   }

	    /**
	    *  Function to edit type
	    */
	   function updateType( ){
	   		$data 		= $this->input->post( );
	   		$type_id 	= $data[ 'id' ];
	   		unset( $data[ 'action' ] );
	   		unset( $data[ 'id' ] );
	   		$result = $this->Mautocontrole->edit_type( $type_id ,$data );
	   		if( $result ){
	   			$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/categories' );
	   }

	   /*----------  Predifined  ----------*/
	   /**
	    *  For listing the predifinde
	    */
	   	function predifined( ){
	   		$manually_entered	= array();
	   		$pred_task_name_arr = array();
	   		$company_ids_arr 	= array();

	   		$tasks_name 		= $this->Mautocontrole->getalltasksname();
	   		$type_name 			= $this->Mautocontrole->gettypename();
	   		$result 			= $this->Mautocontrole->getPredifinds();
	   		$predefined_ids 	= array_column( $result[ 'autocontrole_predifined' ] ,'id' );
	   		$deleted_values 	= $this->Mautocontrole->get_autocontrole_deleted_elements( 'predifined' );
		   	if( !empty( $deleted_values ) ){
		   		foreach ( $deleted_values as $key => $value) {
		   			$deleted_values[ $key ] = $value[ 'text' ].$value[ 'company_id' ];
		   		}
		   	}

	   		foreach ( $tasks_name['allTask'] as $key => $value ) {
	   			if( ! in_array( $value['pred_task_name'], $predefined_ids ) && ( ! in_array( $value['company_id'], $company_ids_arr ) || ! in_array( $value['pred_task_name'], $pred_task_name_arr ) ) && ! in_array( trim($value['pred_task_name']).$value['company_id'], $deleted_values ) ){
	   				$arr = array();
	   				array_push( $arr,$value['type_id'] );
	   				array_push( $arr,$value['pred_task_name'] );
	   				array_push( $arr,$value['company_id'] );
	   				array_push( $arr,$value['username'] );
	   				array_push( $manually_entered, $arr );
	   				array_push( $company_ids_arr, $value['company_id'] );
	   				array_push( $pred_task_name_arr, $value['pred_task_name'] );
	   			}
	   		}
	   		$data['manually']  = $manually_entered;
	   		$data['autocontrole_type']  = $type_name['autocontrole_type'];
	   		if( !empty( $result[ 'types' ] ) ){
	   			$type_ids = array( );
		   		foreach ( $result[ 'types' ] as $key => $type ) {
		   			array_push( $type_ids, $type[ 'type_id' ] );
		   		}
		   		$type_ids = array_unique( $type_ids );
		   		$type_ids = array_values( $type_ids );
		   		for( $i= 0; $i < sizeof( $type_ids ); $i++ ){	// making an array that has all simillar type predifined together
		   			$final_arr[ $i ] = array( );
		   			foreach ( $result[ 'autocontrole_predifined' ] as $key => $predifined ) {
		   				if( $predifined[ 'type_id' ] == $type_ids[ $i ] ){
		   					array_push( $final_arr[ $i ] , $predifined );
		   					$final_arr[ $i ][ 'typeName' ] = $predifined[ 'type_name' ];
		   				}
		   			}
		   			if( empty( $final_arr[ $i ] ) ){
	   					unset( $final_arr[ $i ] );
	   				}
		   		}
	   		}
	   		if( !empty( $final_arr ) ){
	   			$data[ 'predifineds' ] = $final_arr;
	   		}
			$data[ 'company_type' ] = $this->Mautocontrole->get_all_company_type();
			$objects  				= $this->Mautocontrole->getTypesObjects();
			$data['objects'] 		= $objects[ 'autocontrole_objects' ];
	   		$data['header'] 		= $this->template.'header';
		    $data['main'] 			= $this->template.'autocontrole_predifined';
		    $data['footer']  		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );	
	   	}
	    
	   	/**
	   	* to get predefined name(to show in autocontrole prdefined page)
	   	**/
	   	function get_predefined(){
	   		$type 	= $this->input->post('type_id');
	   		$result = $this->Mautocontrole->get_predefined($type);
	   		echo json_encode( $result['predifined'] );
	   	}
	   	/**
	   	* to get objects name(to show in autocontrole object page)
	   	**/
	   	function get_objects(){
	   		$type=$this->input->post('type_id');
	   		$result=$this->Mautocontrole->get_objects($type);
	   		echo json_encode($result['objects']);
	   	}
	   	/**
	   	* to insert manually objects to existing predef.
	   	**/
	   	function merge_object(){
	   		$obj_cat 		= $this->input->post('obj_cat');
	   		$obj_name 		= $this->input->post('obj_name');
	   		$company_id 	= $this->input->post('company_id');
	   		$group_id 		= $this->input->post('group_id');
	   		$company_type 	= $this->input->post('company_type');
	   		$company_type 	= implode( "#", $company_type );
	   		$type_ids 		= $this->input->post('related_type_id');
	   		if( $type_ids && $type_ids != 'null' ){
	   			$type_ids 		= implode( "#", $type_ids );
	   		}else{
	   			$type_ids = '';
	   		}
	   		$insert_arr		= array(
	   							'object_name_dch' 	=> $obj_name,
		   						'object_name' 		=> $obj_name,
		   						'object_name_fr' 	=> $obj_name,
		   						'type_id' 			=> $obj_cat,
		   						'company_type' 		=> $company_type,
		   						'group_id' 			=> $group_id, 
		   						'related_type_ids' 	=> $type_ids
			);
   			$result = $this->Mautocontrole->update_task_object( $insert_arr, $company_id );
   			if( $result ) {
   				echo "success";
   				exit();
   			} else {
   				echo "failed";
   				exit();
   			}
	   	}
	   	/**
	   	* to insert manually location to existing locations.
	   	**/
	   	function merge_location(){
	   		$loc_id 	= $this->input->post('loc_id');
	   		$loc_name 	= $this->input->post('loc_name');
	   		$company_id = $this->input->post('company_id');
	   		$update_arr = array(
			   			'location' => $loc_id
   			);
   			$result = $this->Mautocontrole->update_task_location( $company_id, $loc_name, $update_arr );
   			if( $result ) {
   				echo "success";
	   			exit();
   			} else {
   				echo "failed";
   				exit();
   			}
	   	}

	   	/**
	    *  For adding predifinde 
	    */
	   	function addeditPredifined( ){
	   		if( $this->input->post( 'action' ) == 'ADD Predifined' ){ 										//ADD Predifined
		        $data = $this->input->post( );
		        if( $data[ 'type_id' ] == 'Select Type'){
		        	$this->session->set_flashdata( 'msg',_( 'Please provide the type' ) );
					redirect( base_url().'mcp/autocontrole/predifined' );
		        }
		        if(!empty($_FILES[ 'upload_icon' ][ 'name' ])){
		        	$target_dir 	= base_url( )."assets/images/predifineAuto_img/";
			       	$file_name  	= mt_rand( ).'-'.$_FILES[ 'upload_icon' ][ 'name' ];
			       	$file_name_arr 	= explode( '.', $file_name );
		       		$file_name		= clean_pdf( $file_name_arr[ 0 ] );
		       		$file_name		= $file_name.'.'.$file_name_arr[ sizeof( $file_name_arr ) - 1 ];
					$config['upload_path'] = dirname(__FILE__).'/../../../assets/images/predifineAuto_img/';
				  	$config['allowed_types'] = 'gif|jpg|png|GIF|PNG|JPG|jpeg';
				  	$config['file_name']	= $file_name;
				  	$this->load->library('upload', $config);
				  	if ( ! $this->upload->do_upload( "upload_icon" ) )
				  	{
				  		$this->session->set_flashdata( 'msg',$this->upload->display_errors( ) );
						redirect( base_url().'mcp/autocontrole/predifined' );
				  	}
				  	$data[ 'icon' ] = $file_name;
				}else{
					$data[ 'icon' ] = '';
				}

		        unset( $data[ 'action' ] );	
		        unset( $data[ 'objects' ] );
		        $data[ 'company_type' ] = implode( "#", $this->input->post( 'company_type' ) );
		        $data[ 'object_ids' ] = json_encode( $this->input->post( 'objects' ) );
		        $result = $this->Mautocontrole->add_predifined( $data );
		        if( $result ){
	   			$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
		   		}else{
		   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
		   		}
		   		redirect( base_url().'mcp/autocontrole/predifined' );
		    }else{
		    	$predifined_id 			= $this->uri->segment( 4 );
		    	$result 				= $this->Mautocontrole->get_predifined( $predifined_id );
				$data[ 'predif' ]		= $result[ 'autocontrole_predifined' ];
		   		$data['header']			= $this->template.'header';
				$data['main']			= $this->template.'autocontrole_predifinedAddEdit';
				$data['footer']			= $this->template.'footer';
				$result 				= $this->Mautocontrole->getallTypes();
				$data[ 'types' ] 		=  $result[ 'autocontrole_type' ];
				$objects  				= $this->Mautocontrole->getTypesObjects();
				$data['objects'] 		= $objects[ 'autocontrole_objects' ];
				$data[ 'company_type' ] = $this->Mautocontrole->get_all_company_type();
				$data[ 'prev_n_nxt_id' ]= $this->Mautocontrole->get_previous_and_next_id_predefined( $predifined_id );
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);
		    }
	   	}

	   	/**
	    *  For Updating predifinde 
	    */
	   	function updatePredifined( ){
	   		$data 		   = $this->input->post( );
	   		if( $data[ 'type_id' ] == 'Select Type'){
	        	$this->session->set_flashdata( 'msg',_( 'Please provide the type' ) );
				redirect( base_url().'mcp/autocontrole/predifined' );
	        }
	   		$predifined_id = $data[ 'id' ]; 
	   		$file_name 	= '';
	   		if( !empty( $_FILES[ 'upload_icon' ] ) ){
		   		if( $_FILES[ 'upload_icon' ][ 'name' ] != ''  ){

		   			// for removing old file that was uploaded
		   			$old_file = $this->Mautocontrole->getFilenamePredifined( $predifined_id  );
		   			if( file_exists( FCPATH."assets/images/predifineAuto_img/". $old_file[ 'icon' ] ) ) {
						$unlink = unlink( FCPATH."/assets/images/predifineAuto_img/". $old_file[ 'icon' ] );
					}

		   			$target_dir 	= base_url( )."assets/images/predifineAuto_img/";
		       		$file_name  	= mt_rand( ).'-'.$_FILES[ 'upload_icon' ][ 'name' ];
		       		$file_name_arr 	= explode( '.', $file_name );
		       		$file_name		= clean_pdf( $file_name_arr[ 0 ] );
		       		$file_name		= $file_name.'.'.$file_name_arr[ 1 ];
					$config['upload_path'] = dirname(__FILE__).'/../../../assets/images/predifineAuto_img/';

				  	$config['allowed_types'] = 'gif|jpg|png|GIF|PNG|JPG|jpeg';
				  	$config['file_name']	= $file_name;
				  	$this->load->library('upload', $config);
				  	if ( ! $this->upload->do_upload( "upload_icon" ) )
				  	{
				  		$this->session->set_flashdata( 'msg',$this->upload->display_errors( ) );
						redirect( base_url().'mcp/autocontrole/predifined' );
				  	}
		   		}
	   		}
	   		$redirect_url = base_url().'mcp/autocontrole/predifined';
			if( $data[ 'action' ] == 'Save and next' ){
				$prev_n_nxt_id = $this->Mautocontrole->get_previous_and_next_id_predefined( $predifined_id );
				if( isset(  $prev_n_nxt_id['next_id'] ) ){
					$redirect_url = base_url().'mcp/autocontrole/addeditPredifined/'. $prev_n_nxt_id['next_id'];
				}
			} 
	   		unset( $data[ 'action' ] );
	   		unset( $data[ 'id' ] );
	   		unset( $data[ 'objects' ] );
	   		if( $file_name !='' ){
	        	$data[ 'icon' ] = $file_name;			// attaching file name if its change
	   		}
	   		$data[ 'company_type' ] = implode( "#", $this->input->post( 'company_type' ) );
	   		$data[ 'object_ids' ] 	= json_encode( $this->input->post( 'objects' ) );
	        $result = $this->Mautocontrole->update_predifined( $predifined_id,$data );
	        if( $result ){
   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( $redirect_url );
	   	}

	   	/**
	    *  For deleting predifined
	    */
	   	function delete_predifined( ){
	   		$predif_id = $this->input->post( 'predif_id' );
	   		$result = $this->Mautocontrole->delete_predifined( $predif_id );
	   		if ($result) {
	   			echo "success";
	   		}
	   		exit();
	   	}

	   	/**
	    *  Function to Search Predifined
	    */
	   function searchPredifined( ){
	   		$data = $this->input->post( );
	   		if( $data[ 'search_type' ] == '' ){
	   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By' ) );
	   			redirect( base_url().'mcp/autocontrole/predifined' );
	   		}

	   		if( $data[ 'search_type' ] == 'id' ) {
				if( !is_numeric( $data[ 'search_keyword' ] ) ){
		   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By Properly' ) );
		   			redirect( base_url().'mcp/autocontrole/predifined' );
				}
	   		}
	   		$result = $this->Mautocontrole->getSearchPredifineds( $data );

	   		if( !empty( $result[ 'types' ] ) ){
	   			$type_ids = array( );
		   		foreach ( $result[ 'types' ] as $key => $type ) {
		   			array_push( $type_ids, $type );
		   		}
		   		$type_ids = array_unique( $type_ids );
		   		
		   		for( $i=0; $i < sizeof( $type_ids ); $i++ ){		// making an array that has all simillar type predifined together
		   			$final_arr[ $i ] = array( );
		   			foreach ( $result[ 'autocontrole_predifined' ] as $key => $predifined ) {
		   				if( $predifined[ 'type_id' ] == $type_ids[ $i ] ){
		   					array_push( $final_arr[ $i ] , $predifined );
		   					$final_arr[ $i ][ 'typeName' ] = $predifined[ 'type_name' ];
		   				}
		   			}
		   			if( empty( $final_arr[ $i ] ) ){
	   					unlink( $final_arr[ $i ] );
	   				}
		   		}
	   		}
	   		if( !empty( $final_arr ) ){
	   			$data[ 'predifineds' ] = $final_arr;
	   		}
	   		$data['header']= $this->template.'header';
		    $data['main']= $this->template.'autocontrole_predifined';
		    $data['footer']= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   }
	   /*----------  / Predifined  ----------*/


	   /*----------  location  ----------*/

	   /**
	    * For listing Locations
	    */
	   function location( ){ 
	   		$tasks_name	= $this->Mautocontrole->getalltasksname();
	
	   		$result 	= $this->Mautocontrole->getLocations();
	   		$objects   	= $this->Mautocontrole->get_haccp_object_data();
	   		
	   		if( !empty( $result ) ){
		    	$data['locations'] 	= $result[ 'autocontrole_location' ];
		    	$loc_ids 			= array_column( $result[ 'autocontrole_location' ], 'id' );
	   		}
	   		$deleted_values = $this->Mautocontrole->get_autocontrole_deleted_elements( 'location' );
		   	if( !empty( $deleted_values ) ){
		   		foreach ( $deleted_values as $key => $value) {
		   			$deleted_values[ $key ] = $value[ 'text' ].$value[ 'company_id' ];
		   		}
		   	}
	   		$manually_entered 		= array();
	   		$company_ids_arr 		= array();
	   		$loc_arr 				= array();
		   
	   		$tasks_name['allTask'] 	= array_merge( $tasks_name['allTask'], $objects );
	   	
	   		foreach ($tasks_name['allTask'] as $key => $loc) {
	   			if( $loc['location'] != ''){
	   				$loctn = explode("#", $loc['location']);
		   			foreach ($loctn as $k => $value) {
		   				if( ! in_array( $value, $loc_ids ) && $value != '' && ( ! in_array( $loc['company_id'], $company_ids_arr ) || ! in_array( $value, $loc_arr ) ) && ! in_array( $value.$loc['company_id'], $deleted_values ) ){
			   				$arr = array();
			   				array_push( $arr, $value );
			   				array_push( $arr, $loc['company_id'] );
			   				array_push( $arr, $loc['username'] );
			   				array_push( $manually_entered, $arr );
							array_push( $company_ids_arr, $loc['company_id'] );
			   				array_push( $loc_arr, $value );
		   				}
		   			}
	   			}
	  
	   		}

	   		$cp_locations = $this->Mautocontrole->get_cp_locations( );
	   		$loc_names = array_column($cp_locations, 'loc_name');
			if(isset($loc_names) && !empty($loc_names)){
				foreach ( $manually_entered as $key => $man_entered ) {
					if( in_array( $man_entered[ 0 ], $loc_names ) ) {
						unset( $manually_entered[ $key ] );
					}	
				}
			}

	   		$data['manually'] 	= $manually_entered;
	   		$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_location';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   }

	   /**
	    * For adding and editing Locations
	    */
	   function addeditLocation( ){
	   		if( $this->input->post( 'action' ) == 'ADD LOCATION' ){ //ADD Predifined
		        $data = $this->input->post( );
		        unset( $data[ 'action' ] );
		        $result = $this->Mautocontrole->add_location( $data );
		        if( $result ){
	   				$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
		   		}else{
		   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
		   		}
		   		redirect( base_url().'mcp/autocontrole/location' );
		    }else{
		    	$location_id 		= $this->uri->segment( 4 );
		    	if( is_numeric( $location_id ) || $location_id == '' ) {
			    	$result 			= $this->Mautocontrole->get_location( $location_id );
					$data[ 'location' ]	=  $result[ 'autocontrole_location' ];
		    	} else {
		    		$data[ 'comp_loc_name' ] = urldecode( $location_id );
		    	}
		   		$data['header']		= $this->template.'header';
				$data['main']		= $this->template.'autocontrole_locationsAddEdit';
				$data['footer']		= $this->template.'footer';
				$this->load->view($this->template.'mcp_view', $data);
		    }	
	   }

	    /**
	    *  Function to update Location
	    */
	   function updateLocation( ){
	   		$data 		= $this->input->post( );
	   		$location_id 	= $data[ 'id' ];
	   		unset( $data[ 'action' ] );
	   		unset( $data[ 'id' ] );
	   		$result = $this->Mautocontrole->edit_location( $location_id ,$data );
	   		if( $result ){
	   			$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/location' );
	   }

	   /**
	    *  Function to Delete Location
	    */
	   function delete_location( ){
	   		$loc_id = $this->input->post( 'loc_id' );
	   		$result = $this->Mautocontrole->delete_location( $loc_id );
	   		if( $result ){
	   			echo 'success';
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   			echo 'failed';
	   		}
	   		exit();
	   }

	   /**
	    *  Function to Search Location
	    */
	   function searchLocation( ){
	   		$data = $this->input->post( );
	   		if( $data[ 'search_type' ] == '' ){
	   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By' ) );
	   			redirect( base_url().'mcp/autocontrole/location' );
	   		}

	   		if( $data[ 'search_type' ] == 'id' ) {
				if( !is_numeric( $data[ 'search_keyword' ] ) ){
		   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By' ) );
		   			redirect( base_url().'mcp/autocontrole/location' );
				}
	   		}
	   		$result = $this->Mautocontrole->getSearchLoc( $data );

	   		if( !empty( $result ) ){
		    	$data['locations'] 	= $result[ 'autocontrole_location' ];
	   		}
	   		$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_location';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   }
	   /*----------  / location  ----------*/

	   /*----------   Procedures  ----------*/

	   /**
	    *  Function to list all Location Procedure
	    */
	   function procedure( ){
	   		$result 			= $this->Mautocontrole->getallProcedures( );
	   		if( !empty( $result[ 'autocontrole_procedures' ] ) ){
		    	$data['procedures'] 	= $result[ 'autocontrole_procedures' ];
	   		}
	   		$result 			= $this->Mautocontrole->get_company_type( );
	   		if( !empty( $result[ 'types' ] ) ){
	   			$company_type = array( );
	   			foreach ( $result[ 'types' ] as $key => $value ) {
	   				$company_type[ $value[ 'id' ] ] =  $value[ 'company_type_name' ];
	   			}
		    	$data['company_type'] 	= $company_type;
	   		}
	   		$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_procedure';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   }

	   /**
	    *  Function to add Procedure
	    */
	   function addeditprocedure( ){
	   		if( $this->input->post( 'action' ) == 'ADD PROCEDURE' ){
		   		$data 		   = $this->input->post( );
		   		if( $_FILES[ 'upload_file' ][ 'name' ] != '' && $_FILES[ 'upload_file_fr' ][ 'name' ] != '' && $_FILES[ 'upload_file_dch' ][ 'name' ] != ''  ){
		   			$upload_arr = array('upload_file','upload_file_fr','upload_file_dch');
		   			$file_arr = array('file','file_fr','file_dch');
		   			foreach ($upload_arr as $key => $value) {
			   			$target_dir 				= base_url( )."assets/images/predifineAuto_img/";
			       		$file_name  				= mt_rand( ).'-'.$_FILES[ $value ][ 'name' ];
			       		$file_name_arr 				= explode( '.', $file_name );
			       		$file_name					= clean_pdf( $file_name_arr[ 0 ] );
			       		$file_name					= $file_name.'.'.$file_name_arr[ sizeof( $file_name_arr ) - 1 ];
						$config['upload_path'] 		= dirname(__FILE__).'/../../../assets/images/predifineAuto_img/';
					  	$config[ 'allowed_types' ] 	= 'pdf|PDF';
					  	$config[ 'file_name' ]		= $file_name;
					  	$this->load->library( 'upload', $config );
			   			$data[ $file_arr[$key] ] 	= $file_name;
					  	$this->load->library( 'upload', $config );
			   			$_FILES[ 'mediafile' ]['name'] =  $_FILES[ $value ][ 'name' ];
			            $_FILES[ 'mediafile' ]['type'] =  $_FILES[ $value ][ 'type' ];
			            $_FILES[ 'mediafile' ]['tmp_name'] = $_FILES[ $value ][ 'tmp_name' ];
			            $_FILES[ 'mediafile' ]['error'] =  $_FILES[ $value ][ 'error' ];
			            $_FILES[ 'mediafile' ]['size'] =  $_FILES[ $value ][ 'size' ];
			           
			            $this->upload->initialize( $config );
					  	if ( ! $this->upload->do_upload( 'mediafile' ) ){
					  		$this->session->set_flashdata( 'msg',$this->upload->display_errors( ) );
							redirect( base_url().'mcp/autocontrole/procedure' );
					  	}
		   			}
			   	}else{
		   			$this->session->set_flashdata( 'msg', _( 'Please provide a file to upload.' ) );
			   		redirect( base_url().'mcp/autocontrole/procedure' );
		   		}
		   		if( !empty( $data[ 'types' ] ) ){
		   			$data[ 'types' ] = json_encode(  $data[ 'types' ] );
		   		}else{
		   			$data[ 'types' ] = '';
		   		}
	   			unset( $data[ 'action' ] );
	   			
		        $result = $this->Mautocontrole->add_procedure( $data );
			    if( $result ){
		   			$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
			   }else{
			   		$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
			   	}
			   	redirect( base_url().'mcp/autocontrole/procedure' );
		    }else{ // for edit
		    	$procedure_id 		= $this->uri->segment( 4 );
		    	$result 			= $this->Mautocontrole->get_procedure( $procedure_id );
		    	if( !empty( $result ) ){
					$data[ 'procedure' ]	=  $result[ 'autocontrole_procedure' ];
		    	}
		    	$result 			= $this->Mautocontrole->get_company_type( );
		    	if( !empty( $result[ 'types' ] ) ){
			    	$data[ 'company_types' ]    = $result[ 'types' ];
			    }
			    $category 			= $this->Mautocontrole->getallcategory( );
		   		if( !empty( $category[ 'autocontrole_category' ] ) ){
			    	$data['category'] 	= $category[ 'autocontrole_category' ];
		   		}
		   		$data['header']		= $this->template.'header';
				$data['main']		= $this->template.'autocontrole_proceduresAddEdit';
				$data['footer']		= $this->template.'footer';
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);
		    }	
	   }

	   /**
	    *  Function to Update Procedure
	    */
	   function updateProcedure( ){
	   		$data 		   = $this->input->post( );
	   		if( !empty( $data[ 'types' ] ) ){
	   			$data[ 'types' ] = json_encode(  $data[ 'types' ] );
	   		}else{
	   			$data[ 'types' ] = '';
	   		}
	   		$procedure_id  = $data[ 'id' ]; 
	   		$file_name 	   = '';
	   		$upload_arr = array('upload_file','upload_file_fr','upload_file_dch');
	   		$file_arr = array('file','file_fr','file_dch');
	   		foreach ($upload_arr as $key => $value) {
	   			if( !empty( $_FILES[ $value ] ) ){
			   		if( $_FILES[ $value ][ 'name' ] != ''  ){
			   			// for removing old file that was uploaded
			   			$old_file = $this->Mautocontrole->getFilenameProcedure( $procedure_id,$file_arr[$key]  );
			   			if( file_exists( FCPATH."assets/images/predifineAuto_img/". $old_file[ $file_arr[$key] ] ) ) {
							$unlink = unlink( FCPATH."/assets/images/predifineAuto_img/". $old_file[ $file_arr[$key] ] );
						}
			   			$target_dir 	= base_url( )."assets/images/predifineAuto_img/";
			       		$file_name  	= mt_rand( ).'-'.$_FILES[ $value ][ 'name' ];
			       		$file_name_arr 	= explode( '.', $file_name );
			       		$file_name		= clean_pdf( $file_name_arr[ 0 ] );
			       		$file_name		= $file_name.'.'.$file_name_arr[ sizeof( $file_name_arr ) - 1 ];

						$config['upload_path'] = dirname(__FILE__).'/../../../assets/images/predifineAuto_img/';

					  	$config['allowed_types'] = 'pdf|PDF';
					  	$config['file_name']	= $file_name;
					  	$this->load->library('upload', $config);
					  	$this->load->library( 'upload', $config );
			   			$_FILES[ 'mediafile' ]['name'] =  $_FILES[ $value ][ 'name' ];
			            $_FILES[ 'mediafile' ]['type'] =  $_FILES[ $value ][ 'type' ];
			            $_FILES[ 'mediafile' ]['tmp_name'] = $_FILES[ $value ][ 'tmp_name' ];
			            $_FILES[ 'mediafile' ]['error'] =  $_FILES[ $value ][ 'error' ];
			            $_FILES[ 'mediafile' ]['size'] =  $_FILES[ $value ][ 'size' ];
			            $this->upload->initialize( $config );
					  	if ( ! $this->upload->do_upload( 'mediafile' ) ){
					  		$this->session->set_flashdata( 'msg',$this->upload->display_errors( ) );
							redirect( base_url().'mcp/autocontrole/procedure' );
					  	}else{ 
					  		if( $file_name != '' ){
					        	$data[ $file_arr[$key] ] = $file_name;			// attaching file name if its change
					   		}
					  	}
			   		}
		   		}
	   		}

	   		unset( $data[ 'action' ] );
	   		unset( $data[ 'id' ] );
	        $result = $this->Mautocontrole->update_procedure( $data, $procedure_id  );
	        if( $result ){
   			$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/procedure' );
	   }

	   /**
	    *  Function to Delete Procedure
	    */
	   function delete_procedure( ){
	   		$procedure_id = $this->input->post( 'procedure_id' );
	   		$file_arr = array('file','file_fr','file_dch');
	   		$old_file = $this->Mautocontrole->getFilenameProcedure( $procedure_id,$file_arr  );
	   		if(!empty($old_file)){
	   			foreach ($old_file as $key => $data) {
	   				if( file_exists( FCPATH."assets/images/predifineAuto_img/". $data  ) ) {
						$unlink = unlink( FCPATH."/assets/images/predifineAuto_img/". $data );
					}
	   			}
	   		}
	   		$result = $this->Mautocontrole->delete_procedure( $procedure_id );
	   		if( $result ){
	   			echo 'success';
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   			echo 'failed';
	   		}
	   }

	   
	   /**
	    *  Function to Search Procedure
	    */
	   function searchProcedure( ){
	   		$data = $this->input->post( );
	   		if( $data[ 'search_type' ] == '' ){
	   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By' ) );
	   			redirect( base_url().'mcp/autocontrole/procedure' );
	   		}

	   		if( $data[ 'search_type' ] == 'id' ) {
				if( !is_numeric( $data[ 'search_keyword' ] ) ){
		   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By Properly incorrect data provided.' ) );
		   			redirect( base_url().'mcp/autocontrole/procedure' );
				}
	   		}
	   		$result = $this->Mautocontrole->getSearchProc( $data );
	   		
	   		if( !empty( $result ) ){
		    	$data['procedures'] 	= $result[ 'autocontrole_procedures' ];
	   		}
	   		$result 			= $this->Mautocontrole->get_company_type( );
	   		if( !empty( $result[ 'types' ] ) ){
	   			$company_type = array( );
	   			foreach ( $result[ 'types' ] as $key => $value ) {
	   				$company_type[ $value[ 'id' ] ] =  $value[ 'company_type_name' ];
	   			}
		    	$data['company_type'] 	= $company_type;
	   		}
	   		$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_procedure';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   }

	   /**
	    *  Function to save Procedure order
	    */
	   function save_procedureOrder( ){
	   		$procedure_order = $this->input->post( 'procedure_order' );
	   		if( !empty( $procedure_order ) ){
	   			$result = $this->Mautocontrole->updateProcedure_order( $procedure_order );
	   			if( $result ){
	   				echo 'updated';
	   			}else{
	   				echo 'failed';
	   			}
	   		}
	   }
	   /*----------  / procedures  ----------*/

	   /*----------   OBJECTS  ----------*/

	    /**
	    *  Function to list all types
	    */
	   	function objects() {
	   		$data[ 'all_groups' ] 			= $this->Mautocontrole->get_all_temp_group( 1 );
	   		$tasks_name						= $this->Mautocontrole->getalltasksname();
	   		$result 						= $this->Mautocontrole->getTypesObjects();
		   	$data[ 'autocontrole_objects' ] = $result[ 'autocontrole_objects' ];
		   	$result 						= $this->Mautocontrole->get_object_category();
		   	$data[ 'objects_category' ] 	= $result[ 'objects_category' ];
		   	$related_types 					= $this->Mautocontrole->getall_objects_category( );
		   	$data[ 'autocontrole_type' ]	= $related_types[ 'autocontrole_type' ];
		   	$deleted_values 				= $this->Mautocontrole->get_autocontrole_deleted_elements( 'object' );
		   	$haccp_objects 	 				= $this->Mautocontrole->get_haccp_objects( );
		  
		   	if( !empty( $deleted_values ) ){
		   		foreach ( $deleted_values as $key => $value) {
		   			$deleted_values[ $key ] = $value[ 'text' ].$value[ 'company_id' ];
		   		}
		   	}
		   	$manually_entered = array();
		   	$stopping_rep_arr = array();
		   	if( !empty( $tasks_name['allTask'] ) ) {
		   		$object_ids = array_column( $data[ 'autocontrole_objects' ], 'o_id' );
				foreach ( $tasks_name['allTask'] as $key => $value ) {
		   			if( ! in_array( $value['object_name'], $object_ids ) && $value['object_name'] != ''  && ! is_numeric( $value['object_name'] ) ){
		   				if( !in_array( $value['object_name']. $value['company_id'], $deleted_values ) ){
			   				if( !in_array( strtolower( $value['object_name']. $value['company_id'] ), $stopping_rep_arr ) ){
				   				$arr = array();
			   					$manually_entered[ strtolower($value['object_name'].$value['company_id']) ] = array( );
				   				$arr[ 'type_name' ]		= array( $value['type_name'] );
				   				$arr[ 'object_name' ]	= $value['object_name'];
				   				$arr[ 'user_type_id' ]	= $value['user_type_id'];
				   				$arr[ 'company_id' ]	= $value['company_id'];
				   				$arr[ 'comp_username' ]	= ucfirst( $value['username'] );
				   				$arr[ 'type_id' ]		= array( $value['type_id'] );
			   					$manually_entered[ strtolower($value['object_name']. $value['company_id']) ] = $arr;
			   					
			   					array_push( $stopping_rep_arr , strtolower( $value['object_name']. $value['company_id'] ) );
			   				}else{
			   					if( isset($manually_entered[ $value['object_name']]) && ! in_array( $value['type_id'] , $manually_entered[ $value['object_name'] ][ 'type_id' ] ) ){
				   					array_push( $manually_entered[ strtolower( $value['object_name'].$value['company_id'] ) ][ 'type_name' ], $value['type_name'] );
				   					array_push( $manually_entered[ strtolower( $value['object_name'].$value['company_id'] ) ][ 'type_id' ], $value['type_id'] );
				   				}
			   				}
		   				}
		   			}
		   		}
		   	}
	   		if( isset( $haccp_objects ) && $haccp_objects != '' ){
	   			$company_ids = array_column( $haccp_objects, 'company_id' );
	   			$this->db->select( 'id,type_id' );
				$this->db->where_in( 'id', $company_ids );
				$company_data = $this->db->get( 'company' )->result_array();
				if( !empty( $company_data ) ){
					$company_data = array_column( $company_data, 'type_id' , 'id' );
				}
				
	   			foreach ( $haccp_objects as $x => $obj_data ) {
	   				if( $obj_data != '' ){
	   					$decode_obj = json_decode( $obj_data[ 'objects' ] ,true );
	   					foreach ( $decode_obj as $k => $content ) {
	   						if( isset( $content[ 'obj_name'] ) &&  $content[ 'obj_name'] != '' && ! is_numeric(  $content[ 'obj_name'] ) ){
	   							if( !in_array( $content[ 'obj_name'].''.$obj_data[ 'company_id' ], $deleted_values ) ){
	   								$obj_cat = '';
									$obj_grp = '';
	   								if( isset( $content[ 'obj_cat'] ) ){
	   									$obj_cat = $content[ 'obj_cat' ];
	   								}
   									if( isset( $content[ 'obj_grp'] ) ){
   										$obj_grp = $content[ 'obj_grp' ];
   									}
   									$l = strtolower( $content[ 'obj_name'].''.$obj_data[ 'company_id' ] );
			   						if( ! array_key_exists(  $l , $manually_entered ) ){
			   							$user_type_id  =array( );
			   							if( array_key_exists( $obj_data[ 'company_id' ], $company_data ) ){
			   								$user_type_id = explode( "#", $company_data[ $obj_data[ 'company_id' ] ] );
			   							}
			   							$manually_entered[ $l ] = array( 'type_name' => array( '--' ), 'object_name' =>  $content[ 'obj_name'], 'company_id' => $obj_data[ 'company_id' ], 'user_type_id' => $user_type_id , 'type_id' => array( ), 'comp_username' => ucfirst( $obj_data[ 'username' ] ) , 'obj_cat' => $obj_cat, 'obj_grp' => $obj_grp );
			   						}else{
			   							$manually_entered[ $l ][ 'obj_cat' ] = $obj_cat;
			   							$manually_entered[ $l ][ 'obj_grp' ] = $obj_grp;
			   						}							
	   								
	   							}
	   						}
	   					}
	   				}
	   			}
	   		}
		   	if( !empty( $manually_entered ) ){
		   		$manually_entered = array_map( 'unserialize', array_unique( array_map( 'serialize', $manually_entered ) ) );
		   	}
		   	$data['manually']			= $manually_entered;
		   	$data[ 'company_type' ] 	= $this->Mautocontrole->get_all_company_type();
		   	$data['header'] 			= $this->template.'header';
		   	$data['main'] 				= $this->template.'autocontrole_objects';
		   	$data['footer'] 			= $this->template.'footer';
		   	//$this->load->vars( $data );
		   	$this->load->view( $this->template.'mcp_view', $data );
	   	}

	   	function addObjects() {
	   		$result 						= $this->Mautocontrole->getall_objects_category( );
	   		$data[ 'company_type' ] 		= $this->Mautocontrole->get_all_company_type();
	   		$data[ 'all_groups' ] 			= $this->Mautocontrole->get_all_temp_group( 1 );
	   		$data[ 'all_nl_groups' ] 		= $this->Mautocontrole->get_all_temp_group( 1, 'NL' );
	   		$data[ 'objects_category' ] 	= $result[ 'objects_category' ];
	   		$data[ 'autocontrole_type' ]	= $result[ 'autocontrole_type' ];
			$data['header'] 				= $this->template.'header';
		   	$data['main']					= $this->template.'add_edit_objects';
		   	$data['footer']					= $this->template.'footer';
		   	$this->load->view( $this->template.'mcp_view', $data );
	   	}

	   	function editObjects( $id = '' ) {
	   		$result 						= $this->Mautocontrole->getall_objects_category( );
	   		$data[ 'company_type' ] 		= $this->Mautocontrole->get_all_company_type();
	   		$data[ 'all_groups' ] 			= $this->Mautocontrole->get_all_temp_group( 1 );
	   		$data[ 'all_nl_groups' ] 		= $this->Mautocontrole->get_all_temp_group( 1, 'NL' );
	   		$data[ 'objects_category' ] 	= $result[ 'objects_category' ];
	   		$data[ 'autocontrole_type' ]	= $result[ 'autocontrole_type' ];
			$data[ 'object_details' ] 		= $this->Mautocontrole->editObjects( $id );
			$data[ 'prev_n_nxt_id' ] 		= $this->Mautocontrole->get_previous_and_next_id( $id );
			if( !empty( $data ) && isset( $data[ 'object_details' ] ) && $data[ 'object_details' ][ 'related_type_ids' ] ){
		   		$related_type_ids               = explode( "#", $data[ 'object_details' ][ 'related_type_ids' ] );
		   		$data[ 'predifined_options' ]  = $this->Mautocontrole->get_related_type_predifineds( $related_type_ids );
			}
			$data['header'] 				= $this->template.'header';
		   	$data['main'] 					= $this->template.'add_edit_objects';
		   	$data['footer'] 				= $this->template.'footer';
		   	$this->load->view( $this->template.'mcp_view', $data );
	   	}

	   	function updateObjects() {
	   		$redirect_url 			= base_url().'mcp/autocontrole/objects';
	   		$post_data 				= $this->input->post();
	   		$company_type 			= $post_data[ 'company_type' ];
	   		$related_type_ids 		= $post_data[ 'related_type_ids' ];
	   		$related_to 			= $post_data[ 'related_to' ];
	   		$related_predifined 	= $post_data[ 'related_predifined' ];
	   		if( isset( $post_data[ 'related_to' ] ) ){
	   			unset( $post_data[ 'related_to' ] );	   			
	   		}
	   		if( isset( $post_data[ 'related_predifined' ] ) ){
	   			unset( $post_data[ 'related_predifined' ] );
	   		}
	   		$final_arr = array( );
	   		if( !empty( $related_to ) ){
	   			foreach ( $related_to as $key => $value) {
	   				if( isset( $related_predifined[ $key ] ) ){
	   					$final_arr[ $value ] = $related_predifined[ $key ];   					
	   				}
	   			}
	   		}
	   		if( !empty( $final_arr ) ){
	   			$post_data[ 'related_predifined' ] = json_encode( $final_arr );
	   		}
	   		
	   		if( isset( $company_type ) && !empty($company_type ) ){
	   			$post_data[ 'company_type' ] = implode( '#',  $post_data[ 'company_type' ] );
	   		}else{
	   			$post_data[ 'company_type' ] = '';
	   		}

	   		if( isset( $related_type_ids ) && !empty($related_type_ids ) ){
	   			$post_data[ 'related_type_ids' ] = implode( '#',  $post_data[ 'related_type_ids' ] );
	   		}else{
	   			$post_data[ 'related_type_ids' ] = '';
	   		}

	   		if( $post_data[ 'id' ] ) {
	   			$msg = _( "Successfully updated" );
	   		} else  {
	   			$msg = _( "Successfully added" );
	   		}
	   		
	   		if( isset(  $post_data[ 'action' ] ) && $post_data[ 'action' ] == 'Save and next' ){
	   			if( isset( $post_data['id']) && $post_data['id'] != '' ){
					$prev_n_nxt_id = $this->Mautocontrole->get_previous_and_next_id( $post_data['id'] );
					if( isset(  $prev_n_nxt_id['next_id'] ) ){
						$redirect_url = base_url().'mcp/autocontrole/editObjects/'. $prev_n_nxt_id['next_id'];
					}

	   			}
	   			unset( $post_data[ 'action' ] );
			} 
	   		if( !isset( $post_data[ 'type_id' ] ) || $post_data[ 'type_id' ] == '' ){
	   			$msg = _( "Please create some category first" );
	   			$this->session->set_flashdata( 'msg', $msg );
	   			redirect( $redirect_url );
	   		}
	   		if( $this->input->post( 'most_frequently' ) ){
	   			$post_data[ 'most_frequently' ] = 1;	
	   		} else {
	   			$post_data[ 'most_frequently' ] = 0;
	   		}
	   		$result = $this->Mautocontrole->updateObjects( $post_data );
	   		if( $result ) {
	   			$this->session->set_flashdata( 'msg', $msg );
	   			redirect( $redirect_url );
	   		}
	   	}

	   	function delete_object() {
	   		$id 	= $this->input->post( 'o_id' );
	   		$delete = $this->Mautocontrole->delete_object( $id );
	   		if( $delete ) {
	   			echo "success";
	   		} else {
	   			echo "failed";
	   		}
	   	}

	   	function searchObjects() {
	   		$search_type 	= $this->input->post( 'search_type' );
	   		$search_keyword = $this->input->post( 'search_keyword' );
	   		if( $search_type == '' ){
	   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By' ) );
	   			redirect( base_url().'mcp/autocontrole/objects' );
	   		}
	   		$search_result 	= $this->Mautocontrole->searchObjects( $search_type, $search_keyword );
	   		$data[ 'autocontrole_objects' ] = $search_result[ 'autocontrole_objects' ];
	   		$result = $this->Mautocontrole->get_object_category( );
	   		$data[ 'objects_category' ] 	= $result[ 'objects_category' ];
			$data['header']= $this->template.'header';
		   	$data['main']= $this->template.'autocontrole_objects';
		   	$data['footer']= $this->template.'footer';
		   	//$this->load->vars( $data );
		   	$this->load->view( $this->template.'mcp_view', $data );
	   	}
	   	 /**
	    *  Function to list Category
	    */
	   	function category(){
	   		$result 			= $this->Mautocontrole->getallcategory( );
	   		if( !empty( $result[ 'autocontrole_category' ] ) ){
		    	$data['category'] 	= $result[ 'autocontrole_category' ];
	   		}
	   		$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_category';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   	}
	   	 /**
	    *  Function to add/edit Category
	    */
	   function addeditcategory(){
	   	if( $this->input->post( 'action' ) == 'add category' ){
		   		$cat_name		   = $this->input->post('name');
		   		if( !empty( $cat_name ) ){
		   			$data=array(
		   					'name'=>$cat_name,
		   					'added_date'=>date('Y-m-d')
		   				);
		   		}
		        $result = $this->Mautocontrole->add_category( $data );
			    if( $result ){
		   			$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
			   }else{
			   		$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
			   	}
			   	redirect( base_url().'mcp/autocontrole/category' );
		    }else{
		    	$category_id 		= $this->uri->segment( 4 );
		    	$result 			= $this->Mautocontrole->get_category( $category_id );
		    	if( !empty( $result ) ){
					$data[ 'category' ]	=  $result[ 'autocontrole_category' ];
		    	}
		    	
		   		$data['header']		= $this->template.'header';
				$data['main']		= $this->template.'autocontrole_addeditcategory';
				$data['footer']		= $this->template.'footer';
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);
		    }
	   }
	    /**
	    *  Function to Delete Category
	    */
	   function delete_category( ){
	   		$cat_id = $this->input->post( 'cat_id' );
	   		$result = $this->Mautocontrole->delete_category( $cat_id );
	   		if( $result ){
	   			echo 'success';
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   			echo 'failed';
	   		}
	   }
	    /**
	    *  Function to update Category
	    */
	   function updatecategory( ){
	   		$cat_id 	   = $this->input->post('id');
	   		$name		   = $this->input->post('name');

	   		if( !empty( $cat_id ) ){
	   			$data=array(
	   					'name'=>$name
	   				);
	   		}
	        $result = $this->Mautocontrole->update_category( $data, $cat_id  );
	        if( $result ){
   			$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/category' );
	   }

	    /**
	    * Function List all Domain
	     * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	    */
	   function domains( ){
	   	   $result = $this->Mautocontrole->getallDomains( );
		   $data[ 'domains' ] = $result[ 'autocontrole_domains' ];
		   $data['header']= $this->template.'header';
		   $data['main']= $this->template.'autocontrole_domain';
		   $data['footer']= $this->template.'footer';
		   //$this->load->vars( $data );
		   $this->load->view( $this->template.'mcp_view', $data );	
	   }

	    /**
	    * Function to add sub type
	     * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	    */
	   function domain_addedit( ){
	   		if( $this->input->post( 'action' ) == 'ADD DOMAIN' ){ 										//add type
		        $data = $this->input->post( );
		        unset( $data[ 'action' ] );
		        $result = $this->Mautocontrole->add_domain( $data );
		        redirect( base_url().'mcp/autocontrole/domains' );
		        
		    }else{ 																	// edit type
		    	$domain_id 			= $this->uri->segment( 4 );
		    	$result 			= $this->Mautocontrole->get_single_domain( $domain_id );
		    	$data[ 'domains' ]  =  $result[ 'domains' ];
				$data['header']		= $this->template.'header';
				$data['main']		= $this->template.'autocontrole_domain_addEdit';
				$data['footer']		= $this->template.'footer';
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);	
			}
	   }
	    /**
	    * Function to update Domain
	     * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	    */
	   function updateDomain( ){
	   		$domain_id 	   			    = $this->input->post('id');
	   		$domain_name_dch	    = $this->input->post('domain_name_dch');
	   		$domain_name_fr		    = $this->input->post('domain_name_fr');
	   		$domain_name		    = $this->input->post('domain_name');

	   		if( !empty( $domain_id ) ){
	   			$data=array(
	   					'domain_name_dch' => $domain_name_dch,
	   					'domain_name_fr'  => $domain_name_fr,
	   					'domain_name'  => $domain_name
	   				);
	   		}
	        $result = $this->Mautocontrole->update_domain( $data, $domain_id  );
	        if( $result ){
   			$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/domains' );
	   }
	   /**
	    * Function to list all checklist
	     * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	    */
	    function checklist( ){
	    	if( $this->input->post( 'btn_search' ) ){
	    		$search_by                      = $this->input->post( 'search_by' );
	    		$search_keyword                 = $this->input->post( 'search_keyword' );
	    		$result  						= $this->Mautocontrole->get_searched_checklist( $search_by, $search_keyword );
		  		$data[ 'checklists' ] 			= $result;
	    	}else{
	    		$result  						= $this->Mautocontrole->getallchecklist( );
		  		$data[ 'checklists' ] 			= $result;
	    	}

    		$result 						= $this->Mautocontrole->getallDomainname( );
			$data[ 'domains' ] 				= $result[ 'domains' ];
			$data[ 'company_type' ] 		= $this->Mautocontrole->get_all_company_type();
			$result 						= $this->Mautocontrole->getallpredifined( );
			$data[ 'allTask' ] 				=  $result[ 'allTask' ];
	   	   

		   $data['header'] 					= $this->template.'header';
		   $data['main']					= $this->template.'autocontrole_checklist';
		   $data['footer']					= $this->template.'footer';
		   //$this->load->vars( $data );
		   $this->load->view( $this->template.'mcp_view', $data );	
	   }
	   /**
	    * Function to add/edit checklist
	     * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	    */
	   function checklist_add_edit( ){
	   		if( $this->input->post( 'action' ) == 'ADD CHECKLIST' ){ 										//ADD Predifined
		        $data = $this->input->post( );
		      
		       	if( $data[ 'connected_tasks' ] != '' ){
		       		$data[ 'connected_tasks' ] = json_encode( array_values( array_unique( $data[ 'connected_tasks' ] ) ) );
		       	}
		        if( !isset( $data[ 'easing_measure' ] ) ){
		       		$data[ 'easing_measure' ] = '0';
		       	} 
		       	unset( $data['action'] );
		      	$data[ 'company_type' ] = json_encode( $this->input->post( 'company_type' ) );
		        $result = $this->Mautocontrole->add_checklist( $data );
		        if( $result ){
	   			$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
		   		}else{
		   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
		   		}
		   		redirect( base_url().'mcp/autocontrole/checklist' );
		    }else{
		    	$checklist_id 					= $this->uri->segment( 4 );
		    	$data[ 'checklist_detail' ]		= $this->Mautocontrole->getSpecific_checklist( $checklist_id );
		    	$result 						= $this->Mautocontrole->getallDomainname( );
		    	$data[ 'domains' ] 				= $result[ 'domains' ];
				$data[ 'company_type' ] 		= $this->Mautocontrole->get_all_company_type();
				$result 						= $this->Mautocontrole->getallpredifined( );
				$data[ 'allTask' ] 			    = $result[ 'allTask' ];
		   		$data['header']					= $this->template.'header';
				$data['main']					= $this->template.'autocontrole_checkilst_addedit';
				$data['footer']					= $this->template.'footer';
				$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view');
		    }
	   }

	   /**
	    * Function to update checklist data
	     * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
	    */
	   function updateChecklist( ){
	   		$data = $this->input->post( );

	       	if( $data[ 'connected_tasks' ] != '' ){
		       	$connected_tasks = $data[ 'connected_tasks' ];
		       	if( !empty( $connected_tasks ) ){
		       		$unique_ids = array_values( array_unique( array_filter( $data[ 'connected_tasks' ] ) ) );
		       		if( !empty( $unique_ids ) ){
		       			$data[ 'connected_tasks' ] = json_encode( $unique_ids );
		       		}else{
		       			$data[ 'connected_tasks' ] = '';
		       		}
		       	}
	       	}
	       
	        if( !isset( $data[ 'easing_measure' ] ) ){
	       		$data[ 'easing_measure' ] = '0';
	       	} 
	       	$check_list_id = $data[ 'id' ];
	       	unset( $data['action'] );
	       	unset( $data['id'] );
	       	$data[ 'company_type' ] = json_encode( $this->input->post( 'company_type' ) );

	        $result = $this->Mautocontrole->update_checklist( $data , $check_list_id  );
	        if( $result ){
   			$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/checklist' );
		}

		/**
		 * Function to delete checklist
		 * @access Public
		 * @param $checklist_id int
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
		 */
		function delete_checklist(){
			$checklist_id = $this->input->post( 'id' );
			if( $checklist_id ){
				$result = $this->Mautocontrole->delete_checklist( $checklist_id  );
				if( $result ){
					echo "success";
					exit();
				}
			}
			redirect( base_url().'mcp/autocontrole/checklist' );
		}

		/*=======================================
		=            Object Category            =
		=======================================*/
		
		
		/**
		 * Function to add a category for objects
		 * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
		 */
		
		function addedit_objects_category( ){
			if( $this->input->post( 'action' ) == 'add category' ){ 										//add type
		        $data = $this->input->post( );
		        $data[ 'date_created' ] = date("Y-m-d");
		        unset( $data[ 'action' ] );
		        $result = $this->Mautocontrole->add_object_category( $data );
		        redirect( base_url().'mcp/autocontrole/object_categories' );
		        
		    }else{ 														// edit type
		    	$objects_category_id= $this->uri->segment( 4 );
		    	$data[ 'category' ] = $this->Mautocontrole->get_single_object_category( $objects_category_id );
				$data['header']		= $this->template.'header';
				$data['main']		= $this->template.'autocontrole_object_addedit_category';
				$data['footer']		= $this->template.'footer';
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);	
			}
		}

		/**
		 * Function to list all category for objects
		 * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
		 */
		function object_categories( ){
			$result 			= $this->Mautocontrole->get_objects_category_detail( );
	   		if( !empty( $result[ 'autocontrole_objects_category' ] ) ){
		    	$data['category'] 	= $result[ 'autocontrole_objects_category' ];
	   		}
	   		$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_objects_category';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
			
		}

		/**
		 * Function to update category of objects
		 * @access Public
		 * @return nothing
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
		 */
		function updatecategory_objects( ){
			$data 					= $this->input->post( );
	        $data[ 'date_created' ] = date("Y-m-d");
	        $object_id 				= $data[ 'id' ];
	        unset( $data[ 'action' ] );
	        unset( $data[ 'id' ] );
	        $result = $this->Mautocontrole->update_object_category( $data ,$object_id );
	        redirect( base_url().'mcp/autocontrole/object_categories' );
		}

		/**
		 * Function to delete single category of objects
		 * @access Public
		 * @param  $object_id  int
		 * @author Abhishek Singh <abhisheksingh@cedcoss.com>
		 */
		function delete_objects_category( $object_id ){
			if( $object_id ){
				$result = $this->Mautocontrole->delete_objects_category( $object_id );
			}
			redirect( base_url().'mcp/autocontrole/object_categories' );
		}
		
		/*=====  End of Object Category  ======*/
		
		/* Function to update the order of checklist */

		function save_checklistOrder() {
			$checklist_order = $this->input->post( 'checklist_order' );
	   		if( !empty( $checklist_order ) ){
	   			$result = $this->Mautocontrole->updateChecklist_order( $checklist_order );
	   			if( $result ){
	   				echo 'updated';
	   			}else{
	   				echo 'failed';
	   			}
	   		}
		}


		/**
		 * Function to change manually entered value to a predifined value
		 * @access Public
		 * @param $result array 
		 * @author Abhishek Singh 
		 */
		function add_manually_entered_value( ){
			$predif_text 	= trim( $this->input->post( 'predif_text' ) );
            $task_type		= $this->input->post( 'task_type' );
            $company_id		= $this->input->post( 'company_id' );
            if(  $task_type != '0' && $predif_text != '' ){
	            $insert_array 					= array( 'name' 		=> $predif_text, 
	            										  'name_fr' 	=> $predif_text, 
	            										  'name_dch' 	=> $predif_text,
	            										  'type_id'		=> $task_type );
	            $result = $this->Mautocontrole->add_predifined( $insert_array );
		        if( $result ){
		        	$this->Mautocontrole->update_manually_created_predif( $result, $predif_text, $task_type, $company_id );
	   			 	echo "success";
		   		}else{
		   			 echo "failed";
		   		}
            }
		}

		/**
		 * Function to hide the manually entered value from Predifined manual entry
		 * @access Public
		 * @param  String 
		 * @author Abhishek Singh 
		 */
		function mark_hidden_manually_entered_value( ){
			$predif_text 	= trim( $this->input->post( 'manually_created_text' ) );
			$company_id 	= $this->input->post( 'company_id' );
			$section 		= $this->input->post( 'section' );
			if( $predif_text != '' ){
				$result = $this->Mautocontrole->hidden_manually_entered_value( $predif_text, $company_id, $section );
				if( $result ) {
					echo "success";
				}
			}
		}

		function delete_type() {
			$type_id 	= $this->input->post( 'id' );
			$result 	= $this->Mautocontrole->delete_type( $type_id );
			if( $result ) {
				echo "success";
				exit();
			} else {
				echo "failed";
				exit();
			}
		}	

		function temperature_group( $country_code = '' ) {
			if( $country_code == 'be' || $country_code == 'nl' ) {
			   	$data[ 'country_code' ] = $country_code;
			   	$data[ 'autocontrole_temp_group' ] = $this->Mautocontrole->get_all_temp_group( '', strtoupper( $country_code ) );
		  	 	$data['header']			= $this->template.'header';
			   	$data['main']			= $this->template.'autocontrole_temp_group';
		  	 	$data['footer']			= $this->template.'footer';
			   	$this->load->view( $this->template.'mcp_view', $data );
			} else {
				if( isset( $_SERVER['HTTP_REFERER'] ) ) {
					redirect( $_SERVER['HTTP_REFERER'] );
				} else {
					redirect( base_url( 'mcp/companies' ) );					
				}
			}
		}

		 /**
	    *  Function to add new temperature group and edit
	    */
	   	function addedit_temp_group( $id = '', $country_code = '' ){
	   		if( is_numeric( $id ) ){ 													
		    	$data['temp_group'] 	= $this->Mautocontrole->get_temp_group( $id );
		    	$data[ 'country_code' ] = strtoupper( $country_code );
			} else if( $id == 'be' || $id == 'nl' ){
		    	$data[ 'country_code' ] = strtoupper( $id );
			} else if( $id != '' ) {
				if( isset( $_SERVER['HTTP_REFERER'] ) ) {
					redirect( $_SERVER['HTTP_REFERER'] );
				} else {
					redirect( base_url( 'mcp/companies' ) );					
				}
			}
			
			$data['header']	= $this->template.'header';
			$data['main']	= $this->template.'addedit_temp_group';
			$data['footer']	= $this->template.'footer';							
			$this->load->view($this->template.'mcp_view', $data);
	   }

		function update_temp_group() {
			$data = $this->input->post();
			$result = $this->Mautocontrole->update_temp_group( $data );
			if( $result ){
				if( $data[ 'action' ] == 'Add' ){
	   				$this->session->set_flashdata( 'msg',_( 'Added  successfully' ) );
				} else {
	   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
				}
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		$country_code = strtolower( $data[ 'country_code' ] );
	   		redirect( base_url().'mcp/autocontrole/temperature_group/'.$country_code );
		}	   

	   	function delete_temp_group() {
	   		$id 	= $this->input->post( 'id' );
	   		$result = $this->Mautocontrole->delete_temp_group( $id );
	   		if( $result ){
				echo "success";
				exit();
			} 
	   	}
	   	/**
	   	 *
	   	 * Function to add/edit CCP PVA and GHP
	   	 * @access Public
		 * @param  $id int 
		 * @author Abhishek Singh 
	   	 */
	   	
	   	function ccp_pva_ghp_add_edit( $id = '', $country_code = '' ){
	   		$data = $this->input->post( );
	   		if( $data[ 'connected_tasks' ] != '' ){
	       		$data[ 'connected_tasks' ] = json_encode( array_values( array_unique( $data[ 'connected_tasks' ] ) ) );
	       	}

	       	if( $data[ 'company_type' ] != '' ){
				$data[ 'company_type' ] = json_encode( $data[ 'company_type' ] );
			}

			if( $data[ 'kind_of_danger' ] != '' ){
				$data[ 'kind_of_danger' ] = json_encode( $data[ 'kind_of_danger' ] );
			} else {
				$data[ 'kind_of_danger' ] = '';
			}
			
	   		if( $data[ 'action_btn' ] == "ADD CCP PVA GHP" ){
	   			$result = $this->Mautocontrole->add_editccp_pva_ghp_data( $data );
	   			if( $result ){
	   				$this->session->set_flashdata( 'msg',_( 'Successfully Added' ) );
	   			}else{
	   				$this->session->set_flashdata( 'msg',_( 'Some error occured' ) );
	   			}
	   			redirect( base_url().'mcp/autocontrole/ccp_pva_ghp/'.strtolower( $data[ 'country_code' ] ) );
	   		}elseif( $data[ 'action_btn' ] == "EDIT CCP PVA GHP" ){
	   			$result = $this->Mautocontrole->add_editccp_pva_ghp_data( $data );
	   			if( $result ){
	   				$this->session->set_flashdata( 'msg',_( 'Successfully Edited' ) );
	   			}else{
	   				$this->session->set_flashdata( 'msg',_( 'Some error occured' ) );
	   			}
	   			redirect( base_url().'mcp/autocontrole/ccp_pva_ghp/'.strtolower( $data[ 'country_code' ] ) );
	   		}
	   		if( is_numeric( $id ) ){
	   			$data[ 'ccp_pva_ghp_data' ] = $this->Mautocontrole->get_ccp_pva_ghp( $id );
	   			$data[ 'country_code' ] = strtoupper( $country_code );
	   		} else if( $id == 'be' || $id == 'nl' ) {
	   			$data[ 'country_code' ] = strtoupper( $id );
	   		} else if( $id != '' ) {
	   			redirect( base_url().'mcp/autocontrole/ccp_pva_ghp/be' );
	   		}
	   		
	   		$data[ 'company_type' ] 		= $this->Mautocontrole->get_all_company_type( );
	   		$result 						= $this->Mautocontrole->getallpredifined( );
			$data[ 'allTask' ] 			    = $result[ 'allTask' ];
	   		$data['header']					= $this->template.'header';
			$data['main']					= $this->template.'autocontrole_ccp_pva_ghp_addEdit';
			$data['footer']					= $this->template.'footer';
			$this->load->vars( $data );
			$this->load->view($this->template.'mcp_view');
	   	}

	   	/**
	   	 *
	   	 * Function to list CCP PVA and GHP
	   	 * @access Public
		 * @param  $id int 
		 * @author Abhishek Singh 
	   	 */
   		function ccp_pva_ghp( $country_code = '' ){
   			if( $country_code == 'be' || $country_code == 'nl' ) {
   				$data[ 'country_code' ] = $country_code;
				if( $this->input->post( 'btn_search' ) ){
		    		$search_by                      = $this->input->post( 'search_by' );
		    		$search_keyword                 = $this->input->post( 'search_keyword' );
		    		$data[ 'search_by' ]            = $search_by;
		    		$data[ 'search_keyword' ]  		= $search_keyword;
		    		$data[ 'ccp_pva_ghp_data' ] 	= $this->Mautocontrole->get_searched_ccp_pva_ghp( $search_by, $search_keyword, strtoupper( $country_code ) );
		    	}else{
		    		$data[ 'search_by' ]            = '';
		    		$data[ 'search_keyword' ]  		= '';
	   		   		$data[ 'ccp_pva_ghp_data' ] 	= $this->Mautocontrole->get_all_ccp_pva_ghp( strtoupper( $country_code ) );
		    	}
	   		   $data[ 'company_type' ] 			= $this->Mautocontrole->get_all_company_type();
	   		   $data['header'] 					= $this->template.'header';
			   $data['main']					= $this->template.'autocontrole_ccp_pva_ghp_list';
			   $data['footer']					= $this->template.'footer';
			   $this->load->view( $this->template.'mcp_view', $data );
   			} else {
   				if( isset( $_SERVER['HTTP_REFERER'] ) ) {
					redirect( $_SERVER['HTTP_REFERER'] );
				} else {
					redirect( base_url( 'mcp/companies' ) );					
				}
   			}
   		}

   		/**
	   	 *
	   	 * Function to delete CCP PVA and GHP
	   	 * @access Public
		 * @param  $id int 
		 * @author Abhishek Singh 
	   	 */
   		function delete_ccp_pva_ghp( $id ){
   			if( $id ){
   				$result = $this->Mautocontrole->delete_ccp_pva_ghp( $id );
   				if( $result ){
   					$this->session->set_flashdata( 'msg',_( 'Deleted  Successfully' ) );
   				}else{
   					$this->session->set_flashdata( 'msg',_( 'Some error occured' ) );
   				}
   			}
   			redirect( base_url().'mcp/autocontrole/ccp_pva_ghp' );
   		}

   		/**
	   	 *
	   	 * Function to delete manually created values so as they are no longer present in mcp manually created section
	   	 * @access Public
		 * @param  $id int 
	   	 */
   		function del_manually_created_val_mcp( ){
   			$insert_att = $this->input->post( );
   			if( !empty( $insert_att ) ){
   				$insert_att[ 'delete_on' ] = date( "Y-m-d" );
   				$result = $this->Mautocontrole->mdel_manually_created_val_mcp( $insert_att );
   				if( $result ) {
	   				echo "success";
	   				exit();
	   			} else {
	   				echo "failed";
	   				exit();
	   			}
   			}
   		}
   		/**
   		 * Function to list all module
   		 *
   		 */
   		
   		function module( ){
   			$result 			= $this->Mautocontrole->getallmodules( );
	   		if( !empty( $result[ 'autocontrole_module' ] ) ){
		    	$data['modules'] 	= $result[ 'autocontrole_module' ];
	   		}

   			$data['header']		= $this->template.'header';
		    $data['main']		= $this->template.'autocontrole_module_list';
		    $data['footer']		= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
   		}

   		/**
	    *  Function to add edit module
	    */
	   function addeditmodule( ){
		   	$data 		   = $this->input->post( );
		   	$upload_arr = array('upload_file','upload_file_fr','upload_file_dch');
	   		$file_arr = array('file','file_fr','file_dch');
	   		if( $this->input->post( 'action' ) == 'ADD MODULE' ){
	   			foreach ($upload_arr as $key => $value) {
			   		if( $_FILES[ $value ][ 'name' ] != '' ){
			   			$config 					= array( ); 
			   			$target_dir 				= base_url( )."assets/images/predifineAuto_img/";
			       		$file_name  				= mt_rand( ).'-'.$_FILES[ $value ][ 'name' ];
			       		$file_name_arr 				= explode( '.', $file_name );
			       		$file_name					= clean_pdf( $file_name_arr[ 0 ] );
			       		$file_name					= $file_name.'.'.$file_name_arr[ sizeof( $file_name_arr ) - 1 ];

						$config['upload_path'] 		= dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/';
					  	$config[ 'allowed_types' ] 	= 'pdf|PDF';
					  	$config[ 'file_name' ]		= $file_name;
					  	$this->load->library( 'upload', $config );
			   			$data[ $file_arr[$key] ] 	= $file_name;
			   			$_FILES[ 'mediafile' ]['name'] =  $_FILES[ $value ][ 'name' ];
			            $_FILES[ 'mediafile' ]['type'] =  $_FILES[ $value ][ 'type' ];
			            $_FILES[ 'mediafile' ]['tmp_name'] = $_FILES[ $value ][ 'tmp_name' ];
			            $_FILES[ 'mediafile' ]['error'] =  $_FILES[ $value ][ 'error' ];
			            $_FILES[ 'mediafile' ]['size'] =  $_FILES[ $value ][ 'size' ];
			           
			            $this->upload->initialize( $config );
					  	if ( ! $this->upload->do_upload( 'mediafile' ) ){
					  		$this->session->set_flashdata( 'msg',$this->upload->display_errors( ) );
							redirect( base_url().'mcp/autocontrole/module' );
					  	}
				   	}else{
			   			$this->session->set_flashdata( 'msg', _( 'Please provide a file to upload.' ) );
				   		redirect( base_url().'mcp/autocontrole/module' );
			   		}
		   		}
	   			unset( $data[ 'action' ] );
	   			$data[ 'added_date' ] =  date('Y-m-d');
		        $result = $this->Mautocontrole->add_module( $data );
			    if( $result ){
		   			$this->session->set_flashdata( 'msg',_( 'Updated Successfully' ) );
			   }else{
			   		$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
			   	}
			   	redirect( base_url().'mcp/autocontrole/module' );
		    }elseif( $this->input->post( 'action' ) == 'EDIT MODULE'  ){
		    	$old_file_arr = array('file_name','file_name_fr','file_name_dch');  
		    	foreach ($upload_arr as $key => $value) {
			    	if( $_FILES[ $value ][ 'name' ] != ''  ){
			   			$target_dir 				= base_url( )."assets/images/predifineAuto_img/";
			       		$file_name  				= mt_rand( ).'-'.$_FILES[ $value ][ 'name' ];
			       		$file_name_arr 				= explode( '.', $file_name );
			       		$file_name					= clean_pdf( $file_name_arr[ 0 ] );
			       		$file_name					= $file_name.'.'.$file_name_arr[ sizeof( $file_name_arr ) - 1 ];
						$config['upload_path'] 		= dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/';
					  	$config[ 'allowed_types' ] 	= 'pdf|PDF';
					  	$config[ 'file_name' ]		= $file_name;
					  	$this->load->library( 'upload', $config );

			   			$_FILES[ 'mediafile' ]['name'] =  $_FILES[ $value ][ 'name' ];
			            $_FILES[ 'mediafile' ]['type'] =  $_FILES[ $value ][ 'type' ];
			            $_FILES[ 'mediafile' ]['tmp_name'] = $_FILES[ $value ][ 'tmp_name' ];
			            $_FILES[ 'mediafile' ]['error'] =  $_FILES[ $value ][ 'error' ];
			            $_FILES[ 'mediafile' ]['size'] =  $_FILES[ $value ][ 'size' ];
			           	$data[ $file_arr[$key] ] 	= $file_name;
			            $this->upload->initialize( $config );
					  	if ( ! $this->upload->do_upload( 'mediafile' ) ){
					  		$this->session->set_flashdata( 'msg',$this->upload->display_errors( ) );
							redirect( base_url().'mcp/autocontrole/module' );
					  	}
					  	if( isset( $data[ $old_file_arr[$key] ] ) && $data[ $old_file_arr[$key] ]!= '' && file_exists( dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/'.$data[ $old_file_arr[$key] ] ) ){
					  		unlink( dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/'.$data[ $old_file_arr[$key] ] );
					  	}
				   	}
		    	}
		    	unset($data[ 'action' ]);
		    	unset($data[ 'file_name' ]);
		    	unset($data[ 'file_name_fr' ]);
		    	unset($data[ 'file_name_dch' ]);
		   		$result = $this->Mautocontrole->add_module( $data );
		   		if( $result ){
		   			$this->session->set_flashdata( 'msg',_( 'Added Successfully' ) );
			   	}else{
			   		$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
			   	}
			   	redirect( base_url().'mcp/autocontrole/module' );
		    }else{ // for edit
		    	$module_id 		= $this->uri->segment( 4 );
		    	$result 			= $this->Mautocontrole->get_module( $module_id );
		    	if( !empty( $result ) ){
					$data[ 'module' ]	=  $result[ 'autocontrole_module' ];
		    	}
		   		$data['header']		= $this->template.'header';
				$data['main']		= $this->template.'autocontrole_modulesAddEdit';
				$data['footer']		= $this->template.'footer';
				//$this->load->vars( $data );
				$this->load->view($this->template.'mcp_view', $data);
		    }	
	   }
	   /**
	    * Function to delete module
	    * @param id int
	    *
	    */
	   
	   function delete_module( $id ){
	   		if( $id ){
	   			$get_module_result = $this->Mautocontrole->get_module( $id );
	   			if(!empty($get_module_result)){
	   				unset($get_module_result['autocontrole_module']['id']);
	   				unset($get_module_result['autocontrole_module']['module']);
	   				unset($get_module_result['autocontrole_module']['module_fr']);
	   				unset($get_module_result['autocontrole_module']['module_dch']);
	   				unset($get_module_result['autocontrole_module']['added_date']);
	   				foreach ($get_module_result['autocontrole_module'] as $key => $data) {
	   					if( isset( $data  ) && $data != '' && file_exists( dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/'.$data ) ){
					  		unlink( dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/'.$data );
					  	}
	   				}
	   			}
	   			$result = $this->Mautocontrole->delete_module( $id );
	   			if( $result ){
	   				$this->session->set_flashdata( 'msg', _( 'Deleted successfully' ) );
	   			}else{
	   				$this->session->set_flashdata( 'msg', _( 'Some error occured, plrase try again' ) );
	   			}
	   		}

	   		redirect( base_url().'mcp/autocontrole/module' );
	   }

	      /**
	    *  Function to Search Procedure
	    */
	   function searchmodule( ){
	   		$data = $this->input->post( );
	   		if( $data[ 'search_type' ] == '' ){
	   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By' ) );
	   			redirect( base_url().'mcp/autocontrole/module' );
	   		}

	   		if( $data[ 'search_type' ] == 'id' ) {
				if( !is_numeric( $data[ 'search_keyword' ] ) ){
		   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By Properly incorrect data provided.' ) );
		   			redirect( base_url().'mcp/autocontrole/module' );
				}
	   		}elseif( $data[ 'search_type' ] == 'name' ) {
	   			if(  $data[ 'search_keyword' ] == '' ){
		   			$this->session->set_flashdata( 'msg', _( 'Please provide Search By Properly incorrect data provided.' ) );
		   			redirect( base_url().'mcp/autocontrole/module' );
				}
	   		}

	   		$result = $this->Mautocontrole->getSearch_modules( $data );
	   		
	   		if( !empty( $result[ 'autocontrole_module' ] ) ){
		    	$data['modules'] 	= $result[ 'autocontrole_module' ];
	   		}
	   		$data[ 'search_type' ]  	= $data[ 'search_type' ];
	   		$data[ 'search_keyword' ] 	= $data[ 'search_keyword' ];
   			$data['header']				= $this->template.'header';
		    $data['main']				= $this->template.'autocontrole_module_list';
		    $data['footer']				= $this->template.'footer';
		    //$this->load->vars( $data );
		    $this->load->view( $this->template.'mcp_view', $data );
	   }

   		function pasteurization_group( $country_code = '' ) {
			if( $country_code == 'be' || $country_code == 'nl' ) {
				$data[ 'country_code' ] = $country_code;
			   	$data[ 'autocontrole_pasteur_group' ] = $this->Mautocontrole->get_pasteur_group( '', strtoupper( $country_code ));
			   	$data['header']	= $this->template.'header';
			   	$data['main']	= $this->template.'autocontrole_pasteur_group';
			   	$data['footer']	= $this->template.'footer';
			   	$this->load->view( $this->template.'mcp_view', $data );
			} else {
				if( isset( $_SERVER['HTTP_REFERER'] ) ) {
					redirect( $_SERVER['HTTP_REFERER'] );
				} else {
					redirect( base_url( 'mcp/companies' ) );					
				}
			}
		}

		 /**
	    *  Function to add new temperature group and edit
	    */
	   	function addedit_pasteur_group( $id = '', $country_code = '' ){
	   		if( is_numeric( $id ) ){ 													
		    	$data['pasteur_group'] = $this->Mautocontrole->get_pasteur_group( $id );
		    	$data[ 'country_code' ] = strtoupper( $country_code );
			} else if( $id == 'be' || $id == 'nl' ){
		    	$data[ 'country_code' ] = strtoupper( $id );
			} else if( $id != '' ){
				redirect( base_url( 'mcp/autocontrole/pasteurization_group' ) );
			}
			
			$data['header']	= $this->template.'header';
			$data['main']	= $this->template.'addedit_pasteur_group';
			$data['footer']	= $this->template.'footer';							
			$this->load->view($this->template.'mcp_view', $data);
	   }

		function update_pasteur_group() {
			$data = $this->input->post();
			$result = $this->Mautocontrole->update_pasteur_group( $data );
			if( $result ){
				if( $data[ 'action' ] == 'Add' ){
	   				$this->session->set_flashdata( 'msg',_( 'Added  successfully' ) );
				} else {
	   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
				}
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/pasteurization_group/'.strtolower( $data[ 'country_code' ] ) );
		}	   

	   	function delete_pasteur_group() {
	   		$id 	= $this->input->post( 'id' );
	   		$result = $this->Mautocontrole->delete_pasteur_group( $id );
	   		if( $result ){
				echo "success";
				exit();
			} 
	   	}

	   	/*========================================================
	   	=            Temperature workroom opperations            =
	   	========================================================*/
	   	/**
	   	 * List temperature co
	   	 *
	   	 */
	   	
	   	function temperature_workroom( ){
	   		$data[ 'autocontrole_temp_group' ] = $this->Mautocontrole->get_all_workroom();
			
		   $data['header']	= $this->template.'header';
		   $data['main']	= $this->template.'autocontrole_workroom_list';
		   $data['footer']	= $this->template.'footer';
		   //$this->load->vars( $data );
		   $this->load->view( $this->template.'mcp_view', $data );
	   	}
	   	
	   	/**
	   	 * Function to add edit workgroup 
	   	 *
	   	 */

	   	function addedit_workroom( $id = '' ){
	   		if( is_numeric( $id ) ){ 													
		    	$data['temp_group'] = $this->Mautocontrole->get_workroom( $id );
			} else if( $id != '' ){
				redirect( base_url( 'mcp/autocontrole/temperature_workroom' ) );
			}
			
			$data['header']	= $this->template.'header';
			$data['main']	= $this->template.'addedit_workroom';
			$data['footer']	= $this->template.'footer';							
	        //$this->load->vars( $data );
			$this->load->view($this->template.'mcp_view', $data);
	   	}

	   	 /**
	   	 * Function to update workgroup
	   	 *
	   	 */
	   	function update_workroom() {
			$data = $this->input->post();
			$result = $this->Mautocontrole->update_workgroup( $data );
			if( $result ){
				if( $data[ 'action' ] == 'Add' ){
	   				$this->session->set_flashdata( 'msg',_( 'Added  successfully' ) );
				} else {
	   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
				}
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/temperature_workroom' );
		}

		/**
		 *
		 * Function to delete workroom data
		 *
		 */
			function delete_workroom() {
		   		$id 	= $this->input->post( 'id' );
		   		$result = $this->Mautocontrole->delete_workroom( $id );
		   		if( $result ){
					echo "success";
					exit();
				} 
		   	}
	   	/*=====  End of Temperature workroom opperations  ======*/

	   	/*========================================================
	   	=            Autocontrole Calibration		            =
	   	========================================================*/

	   	function autocontrole_calibration( ){

	   	   $data[ 'autocontrole_temp_group' ] = $this->Mautocontrole->get_all_calibration();
			
		   $data['header']	= $this->template.'header';
		   $data['main']	= $this->template.'autocontrole_calibration';
		   $data['footer']	= $this->template.'footer';
		   //$this->load->vars( $data );
		   $this->load->view( $this->template.'mcp_view', $data );
	   	}

	   	/**
	   	 * Function to add edit calibration 
	   	 *
	   	 */

	   	function addedit_calibration( $id = '' ){
	   		if( is_numeric( $id ) ){ 													
		    	$data['temp_group'] = $this->Mautocontrole->get_calibration( $id );
			} else if( $id != '' ){
				redirect( base_url( 'mcp/autocontrole/temperature_workroom' ) );
			}
			
			$data['header']	= $this->template.'header';
			$data['main']	= $this->template.'addedit_calibration';
			$data['footer']	= $this->template.'footer';							
	        //$this->load->vars( $data );
			$this->load->view($this->template.'mcp_view', $data);
	   	}

	   	/**
	   	 * Function to update calibration
	   	 *
	   	 */
	   	function update_calibration() {
			$data = $this->input->post();
			$result = $this->Mautocontrole->update_calibration( $data );
			if( $result ){
				if( $data[ 'action' ] == 'Add' ){
	   				$this->session->set_flashdata( 'msg',_( 'Added  successfully' ) );
				} else {
	   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
				}
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/autocontrole_calibration' );
		}

		/**
		 *
		 * Function to delete calibration workroom data
		 *
		 */
			function delete_calibration() {
		   		$id 	= $this->input->post( 'id' );
		   		$result = $this->Mautocontrole->delete_calibration( $id );
		   		if( $result ){
					echo "success";
					exit();
				} 
		   	}
	   	/*=====  End of Temperature workroom opperations  ======*/

	   /*========================================================
	   	=            Autocontrole PH groups		            =
	   	========================================================*/

	   	function autocontrole_ph_groups( ){

	   	   $data[ 'autocontrole_ph_groups' ] = $this->Mautocontrole->get_all_ph_groups();
			
		   $data['header']	= $this->template.'header';
		   $data['main']	= $this->template.'autocontrole_ph_groups';
		   $data['footer']	= $this->template.'footer';
		   //$this->load->vars( $data );
		   $this->load->view( $this->template.'mcp_view', $data );
	   	}

	   	/**
	   	 * Function to add edit calibration 
	   	 *
	   	 */

	   	function addedit_ph_groups( $id = '' ){
	   		if( is_numeric( $id ) ){ 													
		    	$data['ph_group'] = $this->Mautocontrole->get_ph_groups( $id );
		    	
			} else if( $id != '' ){
				redirect( base_url( 'mcp/autocontrole/temperature_workroom' ) );
			}
			
			$data['header']	= $this->template.'header';
			$data['main']	= $this->template.'addedit_ph_groups';
			$data['footer']	= $this->template.'footer';							
	        //$this->load->vars( $data );
			$this->load->view($this->template.'mcp_view', $data);
	   	}

	   	/**
	   	 * Function to update calibration
	   	 *
	   	 */
	   	function update_ph_groups() {
			$data = $this->input->post();
			$result = $this->Mautocontrole->update_ph_groups( $data );
			if( $result ){
				if( $data[ 'action' ] == 'Add' ){
	   				$this->session->set_flashdata( 'msg',_( 'Added  successfully' ) );
				} else {
	   				$this->session->set_flashdata( 'msg',_( 'Updated  successfully' ) );
				}
	   		}else{
	   			$this->session->set_flashdata( 'msg', _( 'Error occured please try again.' ) );
	   		}
	   		redirect( base_url().'mcp/autocontrole/autocontrole_ph_groups' );
		}

		/**
		 *
		 * Function to delete calibration workroom data
		 *
		 */
		function delete_ph_groups() {
	   		$id 	= $this->input->post( 'id' );
	   		$result = $this->Mautocontrole->delete_ph_groups( $id );
	   		if( $result ){
				echo "success";
				exit();
			} 
	   	}
	   	/*=====  End of Autocontrole ph groups  ======*/


	   	function get_related_predifineds( ){
	   		$selected_type = $this->input->post( 'selected_type' );
	   		$result = $this->Mautocontrole->get_related_predifineds( $selected_type );
	   		if( !empty( $result ) ){
	   			echo json_encode( $result );
	   		}else{
	   			echo json_encode( array( ) );
	   		}
	   	}

	   	/**
	   	 *
	   	 * Function to update order of temperature group
	   	 *
	   	 */
	   	
	   	function save_temperature_group_order( ){
	   		$temperatureGroup_order = $this->input->post( 'temperatureGroup_order' );
	   		if( !empty( $temperatureGroup_order ) ){
	   			$result = $this->Mautocontrole->Update_temperatureGroup_order( $temperatureGroup_order );
	   			if( $result ){
	   				echo 'updated';
	   			}else{
	   				echo 'failed';
	   			}
	   		}
	   	}
    }
?>