<?php
class Mautocontrole extends CI_Model
{ 
    
	function __construct()
     {
        parent::__construct();
	    
	    $this->lang_u = get_lang( $_COOKIE['locale'] );
     }
	 
	 /**
	  * Function to add new type in autoontrole section
	  */
	 
	function add_type( $data ){
		if( !empty( $data ) ){
			$this->db->insert( 'autocontrole_type', $data );
			return $this->db->insert_id( );
		}	 
	}

	/**
	  * Function to add new type in autoontrole section
	  */
	 
	function edit_type( $type_id ,$data ){
		if( !empty( $data ) && isset( $type_id ) ){
			$this->db->where( 'id', $type_id );
			$this->db->update( 'autocontrole_type', $data );
			return $this->db->affected_rows( );
		}	 
	}

	/**
	 * Function to all categories
	 */
	function getallTypes( ){
		//$this->db->select( '*' );
		$result[ 'autocontrole_type' ] = $this->db->get( 'autocontrole_type' )->result_array( );
		return $result;
	}

	/**
	 * Function to all types
	 */
	function get_type( $type_id = 0){
		if( $type_id ){
			//$this->db->select( '*' );
			$result[ 'autocontrole_type' ] = $this->db->get_where(  'autocontrole_type', array( 'id' => $type_id ) )->row_array( );
			return $result;
		}
	}

	/*----------  predifinds  ----------*/
	
	/**
	 * Function to add predifined
	 */
	function add_predifined( $data ){
		if( !empty( $data ) ){
			$this->db->insert( 'autocontrole_predifined', $data );
			return $this->db->insert_id( );
		}
	}


	/**
	 * Function to get all predifined
	 */
	function getPredifinds( ){
		$this->db->select( 'autocontrole_predifined.* ,autocontrole_type.type_name'.$this->lang_u.' as type_name' );
		$this->db->join( 'autocontrole_type','autocontrole_type.id = autocontrole_predifined.type_id' );
		$result[ 'autocontrole_predifined' ] = $this->db->get( 'autocontrole_predifined' )->result_array( );

		$this->db->select( 'type_id' );
		$result[ 'types' ] =  $this->db->get( 'autocontrole_predifined' )->result_array( );
		return $result;
	}

	/**
	 * Function to get predifined
	 */
	function get_predifined( $predifined_id ){
		if( $predifined_id ){
			$this->db->select( '*' );
			$result[ 'autocontrole_predifined' ] = $this->db->get_where(  'autocontrole_predifined',array( 'id' => $predifined_id ) )->row_array( );
			return $result;
		}
	}

	/**
	 * Function to update predifined
	 */
	function update_predifined( $predifined_id,$data ){
		if( !empty( $data ) && isset( $predifined_id ) ){
			$this->db->where( 'id', $predifined_id );
			$this->db->update( 'autocontrole_predifined', $data );
			return $this->db->trans_status( );
		}
	}

	/**
	 * Function to delete predifined
	 */
	function delete_predifined( $predifined_id ){
		if(  isset( $predifined_id ) ){
			$this->db->select( 'icon' );
			$result = $this->db->get_where(  'autocontrole_predifined',array( 'id' => $predifined_id ) )->row_array( );
			if( !empty( $result ) ){
				if( file_exists( FCPATH."assets/images/predifineAuto_img/". $result[ 'icon' ] ) ) {
					$unlink = unlink( FCPATH."/assets/images/predifineAuto_img/". $result[ 'icon' ] );
				}
			}
			$this->db->where( 'id', $predifined_id );
	  		$query = $this->db->delete( 'autocontrole_predifined' );
	  		return $predifined_id; 
			
		}
	}

	/**
	 * Function to get the icon that was attached with the predifined that is edited, tis is for unlinking that icon
	 */
	function getFilenamePredifined( $predifined_id  ){
		if( $predifined_id ){
			$this->db->select( 'icon' );
			$result = $this->db->get_where(  'autocontrole_predifined',array( 'id' => $predifined_id ) )->row_array( );
			return $result;
		}
	}

	/**
	 * Function to search the Predifineds
	 */
	function getSearchPredifineds( $data ){

		if( $data[ 'search_type' ] == 'id' ){
			$this->db->select( 'autocontrole_predifined.* ,autocontrole_type.type_name,autocontrole_type.type_name_fr,autocontrole_type.type_name_dch' );
			$this->db->join( 'autocontrole_type','autocontrole_type.id = autocontrole_predifined.type_id' );
			$result[ 'autocontrole_predifined' ] = $this->db->get_where(  'autocontrole_predifined',array( 'autocontrole_predifined.id' => $data[ 'search_keyword' ]) )->result_array( );

			$this->db->select( 'type_id' );
			$result[ 'types' ] =  $this->db->get_where(  'autocontrole_predifined',array( 'autocontrole_predifined.id' => $data[ 'search_keyword' ]) )->result_array( );
			$result[ 'types' ] = array_column( $result[ 'types' ], 'type_id' );
			return $result;

		}else if( $data[ 'search_type' ] == 'name' ){

			$search_txt = explode( ' ', trim( $data[ 'search_keyword' ] ) );
			if( !empty( $search_txt ) ){
				$final_arr_predif = array( );
				$final_arr_types = array( );
				foreach ( $search_txt as $key => $word ){
					$this->db->select( 'autocontrole_predifined.* ,autocontrole_type.type_name,autocontrole_type.type_name_fr,autocontrole_type.type_name_dch' );
					$this->db->join( 'autocontrole_type','autocontrole_type.id = autocontrole_predifined.type_id' );
					$this->db->like( 'name', $word );
					$this->db->or_like( 'name_fr', $word );
					$this->db->or_like( 'name_dch', $word );
					$temp_result = $this->db->get( 'autocontrole_predifined' )->result_array( );

					if(!empty( $temp_result ) ){
						foreach ( $temp_result as $k => $value ) {
							$final_arr_predif[ $value[ 'id' ] ] = $value;
						}
					}
					
					$this->db->select( 'type_id' );
					$this->db->like( 'name', $word );
					$this->db->or_like( 'name_fr', $word );
					$this->db->or_like( 'name_dch', $word );
					$temp_res_types = $this->db->get( 'autocontrole_predifined' )->result_array( );

					if( !empty( $temp_res_types ) ){
						foreach ( $temp_res_types as $y => $type ) {
							array_push( $final_arr_types, $type[ 'type_id' ] );
						}
					}
				}
			
			$result[ 'autocontrole_predifined' ] =  $final_arr_predif;
			$result[ 'types' ] =  array_unique( $final_arr_types );
			}
			

			return $result;
		}else if(  $data[ 'search_type' ] == 'description' ){

			$search_txt = explode( ' ', trim( $data[ 'search_keyword' ] ) );
			if( !empty( $search_txt ) ){
				$final_arr_predif = array( );
				$final_arr_types = array( );
				foreach ( $search_txt as $key => $word ){
					$this->db->select( 'autocontrole_predifined.* ,autocontrole_type.type_name,autocontrole_type.type_name_fr,autocontrole_type.type_name_dch' );
					$this->db->join( 'autocontrole_type','autocontrole_type.id = autocontrole_predifined.type_id' );
					$this->db->like( 'description', $word );
					$this->db->or_like( 'description_fr', $word );
					$this->db->or_like( 'description_dch', $word );
					$temp_result = $this->db->get( 'autocontrole_predifined' )->result_array( );

					if(!empty( $temp_result ) ){
						foreach ( $temp_result as $k => $value ) {
							$final_arr_predif[ $value[ 'id' ] ] = $value;
						}
					}

					$this->db->select( 'type_id' );
					$this->db->like( 'description', $word );
					$this->db->or_like( 'description_fr', $word );
					$this->db->or_like( 'description_dch', $word );
					$temp_res_types = $this->db->get( 'autocontrole_predifined' )->result_array( );

					if( !empty( $temp_res_types ) ){
						foreach ( $temp_res_types as $y => $type ) {
							array_push( $final_arr_types, $type[ 'type_id' ] );
						}
					}
				}
				$result[ 'autocontrole_predifined' ] 	= $final_arr_predif;
				$result[ 'types' ] 						= array_unique( $final_arr_types );
			}
			return $result;
		}
	}
	/*----------  /predifinds  ----------*/


	/*----------  Location  ----------*/

	/**
	 * Function to add Location
	 */
	function add_location( $data ){
		if( !empty( $data ) ){
			$company_id = '';
			$loc_name 	= '';
			if( array_key_exists( 'comp_loc_name', $data ) ) {
				$loc_name 	= explode( "_", $data[ 'comp_loc_name' ] )[0];
				$company_id = explode( "_", $data[ 'comp_loc_name' ] )[1];
				unset( $data[ 'comp_loc_name' ] );
			}

			$this->db->insert( 'autocontrole_location', $data );
			$inserted_id = $this->db->insert_id( );

			if( $company_id != '' && $loc_name != '' ) {
				$this->db->where( 'location', $loc_name );
				$this->db->where( 'company_id', $company_id );
				$this->db->update( 'haccp_tasks', array( 'location' => $inserted_id ) );

				$this->db->select( 'objects' );
				$this->db->where( 'company_id', $company_id );
				$query = $this->db->get( 'haccp_object_data' )->row_array();

				if( !empty( $query ) ) {
					$objects = json_decode( $query[ 'objects' ], true );

					foreach ( $objects as $key => $value ) {
						if( $value[ 'obj_loc' ] == $loc_name ) {
							$objects[ $key ][ 'obj_loc' ] = $inserted_id;
						}
					}
					$this->db->where( 'company_id', $company_id );
					$this->db->update( 'haccp_object_data', array( 'objects' => json_encode( $objects ) ) );
				}
			}
			return $inserted_id;
		}
	}

	/**
	 * Function to get all Location for listing
	 */
	function getLocations( ){
		$this->db->select( '*' );
		$result[ 'autocontrole_location' ] = $this->db->get( 'autocontrole_location' )->result_array( );
		return $result;
	}

	/**
	 * Function to get the Location for edit
	 */
	function get_location( $location_id ){
		if( $location_id ){
			$this->db->select( '*' );
			$result[ 'autocontrole_location' ] = $this->db->get_where(  'autocontrole_location',array( 'id' => $location_id ) )->row_array( );
			return $result;
		}
	}

	/**
	 * Function to edit the Location 
	 */
	function edit_location( $location_id ,$data ){
		if( !empty( $data ) && isset( $location_id ) ){
			$this->db->where( 'id', $location_id );
			$this->db->update( 'autocontrole_location', $data );
			return $this->db->affected_rows( );
		}	
	}

	/**
	 * Function to delete the Location 
	 */
	function delete_location( $loc_id ){
		if(  isset( $loc_id ) ){
			$this->db->where( 'id', $loc_id );
	  		$query = $this->db->delete( 'autocontrole_location' );
	  		return $loc_id; 
		}
	}

	/**
	 * Function to search the Location 
	 */
	function getSearchLoc( $data ){
		if( $data[ 'search_type' ] == 'id' ){
			$this->db->select( '*' );
			$result[ 'autocontrole_location' ] = $this->db->get_where(  'autocontrole_location',array( 'id' => $data[ 'search_keyword' ]) )->result_array( );
			return $result;
		}else{
			$search_txt = explode( ' ', trim( $data[ 'search_keyword' ] ) );
			if( !empty( $search_txt ) ){
				$final_arr_loc = array( );
				foreach ( $search_txt as $key => $word ){
					$this->db->select( '*' );
					$this->db->like( 'loc_name', $word );
					$this->db->or_like( 'loc_name_fr', $word );
					$this->db->or_like( 'loc_name_dch', $word );
					$res_temp_loc = $this->db->get_where(  'autocontrole_location' )->result_array( );

					if(!empty( $res_temp_loc ) ){
						foreach ( $res_temp_loc as $k => $value ) {
							$final_arr_loc[ $value[ 'id' ] ] = $value;
						}
					}
				}
				$result[ 'autocontrole_location' ] = $final_arr_loc;
			}

			return $result;
		}
	}
	/*----------  /Location  ----------*/


	/*----------  /Procedure  ----------*/
	
	
	/**
	 * Function to add Procedure
	 */
	function add_procedure( $data ){
		if( !empty( $data ) ){
			$maxOrder = $this->db->query('SELECT MAX(`order`) AS `maxOrder` FROM `autocontrole_procedures`')->row()->maxOrder;
			if( $maxOrder ){
				$data[ 'order' ] = $maxOrder + 1;
			}else{
				$data[ 'order' ] = 1;
			}
			$this->db->insert( 'autocontrole_procedures', $data );
			return $this->db->insert_id( );
		}
	}

	/**
	 * Function to grt all Procedure
	 */
	function getallProcedures( ){
		$this->db->select( '*' );
		$this->db->order_by( 'order',  "asc" );
		$result[ 'autocontrole_procedures' ] = $this->db->get( 'autocontrole_procedures' )->result_array( );
		return $result;
	}

	/**
	 * Function to get Procedure for edit
	 */
	function get_procedure( $procedure_id ){
		if( $procedure_id ){
			$this->db->select( '*' );
			$result[ 'autocontrole_procedure' ] = $this->db->get_where(  'autocontrole_procedures',array( 'id' => $procedure_id ) )->row_array( );
			return $result;
		}
	}


	/**
	 * Function to get company_type
	 */
	function get_company_type( ){
		$this->db->select( 'id,company_type_name' );
		$result[ 'types' ] = $this->db->get( 'company_type' )->result_array( );
		return $result;
		
	}

	/**
	 * Function to update Procedure
	 */
	function update_procedure( $data, $procedure_id ){
		if( !empty( $data ) && isset( $procedure_id ) ){
			$this->db->where( 'id', $procedure_id );
			$this->db->update( 'autocontrole_procedures', $data );
			return $this->db->affected_rows( );
		}
	}
	/**
	 * Function to get the file that was attached with the procedure that is edited, tis is for unlinking that file
	 */
	function getFilenameProcedure( $procedure_id ,$file ){
		if( $procedure_id ){
			$this->db->select( $file );
			$result = $this->db->get_where(  'autocontrole_procedures',array( 'id' => $procedure_id ) )->row_array( );
			return $result;
		}
	}

	/**
	 * Function to delete Procedure
	 */
	function delete_procedure( $procedure_id ){
		if(  isset( $procedure_id ) ){
			$this->db->select( 'file' );
			$result = $this->db->get_where(  'autocontrole_procedures',array( 'id' => $procedure_id ) )->row_array( );
			if( !empty( $result[ 'file' ] != '' ) ){
				if( file_exists( FCPATH."assets/images/predifineAuto_img/". $result[ 'file' ] ) ) {
					$unlink = unlink( FCPATH."assets/images/predifineAuto_img/". $result[ 'file' ] );
				}
			}
			$this->db->where( 'id', $procedure_id );
	  		$query = $this->db->delete( 'autocontrole_procedures' );
	  		return $procedure_id; 
			
		}
	}

	/**
	 * Function to search the Location 
	 */
	function getSearchProc( $data ){
		if( $data[ 'search_type' ] == 'id' ){
			$this->db->select( '*' );
			$result[ 'autocontrole_procedures' ] = $this->db->get_where(  'autocontrole_procedures',array( 'id' => $data[ 'search_keyword' ]) )->result_array( );
			return $result;
		}else if( $data[ 'search_type' ] == 'types' ){
			$search_txt = explode( ' ', trim( $data[ 'search_keyword' ] ) );
			
			if( !empty( $search_txt ) ){		// Making an array that contains the ids of search types
				$comp_arr_type = array( );
				foreach ( $search_txt as $key => $word ){ 
					$this->db->select( 'id' );
					$this->db->like( 'company_type_name', trim( $word ) );
					$temp_res_comp = $this->db->get( 'company_type' )->result_array( );
					if(!empty( $temp_res_comp ) ){
						foreach ( $temp_res_comp as $k => $value ) {
							array_push( $comp_arr_type, $value[ 'id' ] );
						}
					}
				}
			}

			if( !empty( $comp_arr_type ) ){
				$this->db->select( '*' );
				$autocontrole_procedures = $this->db->get( 'autocontrole_procedures' )->result_array( );
				
				$final_procedure_array = array( );
				foreach ( $autocontrole_procedures as $key => $procedure ) {
					if( $procedure[ 'types' ] != '' ){
						$auto_types = json_decode( $procedure[ 'types' ],true );
						foreach ( $auto_types as $x => $typ ) {
							if( in_array( $typ,$comp_arr_type ) ){
								$final_procedure_array[ $procedure[ 'id' ] ] = $procedure;
							}
							
						}
					}
				}
				$result[ 'autocontrole_procedures' ] = $final_procedure_array;
				return $result;
			}
			
		}else{
			$search_txt = explode( ' ', trim( $data[ 'search_keyword' ] ) );
			if( !empty( $search_txt ) ){
				$final_arr_proc = array( );
				foreach ( $search_txt as $key => $word ){
					$this->db->select( '*' );
					$this->db->like( 'name', $word );
					$temp_result = $this->db->get(  'autocontrole_procedures' )->result_array( );

					if( !empty( $temp_result ) ){
						foreach ( $temp_result as $k => $value ){
							$final_arr_proc[ $value[ 'id' ] ] = $value;
						}
					}
				}
				$result[ 'autocontrole_procedures' ] = $final_arr_proc;
			}
			return $result;
		}
	}

	/**
	 * Function to Update ProcedureOrder 
	 */
	function updateProcedure_order( $procedure_order ){
		if( is_array( $procedure_order  ) ){
			foreach ( $procedure_order  as $key => $prosOrd ) {
				$this->db->where( 'id', $prosOrd[ 'id' ] );
				$this->db->update( 'autocontrole_procedures', array( 'order' => $prosOrd[ 'order' ] ) );
			}
			return true;
		}
	}
	/*----------  /procedure  ----------*/

	/*----------  OBJECTS  ----------*/

	/**
	 * Function to delete manually created value
	 */
	function mdel_manually_created_val_mcp( $insert_att ){
		$this->db->insert( 'autocontrole_deleted_elements', $insert_att );
		return $this->db->insert_id();
	}

	/**
	 * Function to get deleted elements
	 * @return $deleted_elements array
	 * @param $section  string
	 */
	function get_autocontrole_deleted_elements( $section ){
		$this->db->select( 'company_id,text' );
		$deleted_elements = $this->db->get_where( 'autocontrole_deleted_elements', array( 'section' => $section ) )->result_array( );

		return $deleted_elements;
	}
	/**
	 * Function to all types and its objects
	 */
	function getTypesObjects( ){
		$this->db->select( '*' );
		$result[ 'autocontrole_objects' ] = $this->db->get( 'autocontrole_objects' )->result_array();
		return $result;
	}
	/**
    * Function to get category that are created for object
     * @access Public
	 * @return $result array
	 * @author Abhishek Singh 
    */
	function get_object_category( ){
		$this->db->select( 'id, object_name'.$this->lang_u.' as name' );
		$objects_category = $this->db->get( 'autocontrole_objects_category' )->result_array();
		$final = array( );
		if( !empty( $objects_category ) ){
			foreach ( $objects_category as $key => $value ) {
				$final[ $value[ 'id' ] ] = $value;
			}
		}
		$result[ 'objects_category' ] = $final;
		return $result;
	}
	
	/**
     * Function to get get object for edit
     * @access Public
     * @param $id int
	 * @return array
	 * @author Abhishek Singh 
    */
	function editObjects( $id ) {
		$this->db->select( '*' );
		$this->db->where( 'o_id', $id );
		return $this->db->get( 'autocontrole_objects' )->row_array();
	}

	function get_previous_and_next_id( $id ){
		if($id){
			$this->db->select( 'o_id' );
			$this->db->where( 'o_id >', $id );
			$this->db->limit(1);  
			$nxt_id = $this->db->get( 'autocontrole_objects' )->row_array();
			$this->db->select( 'o_id' );
			$this->db->where( 'o_id <', $id );
			$this->db->order_by('o_id','desc');
			$this->db->limit(1);  
			$prev_id = $this->db->get( 'autocontrole_objects' )->row_array();
			return array('prev_id' => $prev_id['o_id'],'next_id'=>$nxt_id['o_id']);
			
		}
	}

	function get_previous_and_next_id_predefined( $id ){
		if($id){
			$this->db->select( 'id' );
			$this->db->where( 'id >', $id );
			$this->db->limit(1);  
			$nxt_id = $this->db->get( 'autocontrole_predifined' )->row_array();
			$this->db->select( 'id' );
			$this->db->where( 'id <', $id );
			$this->db->order_by('id','desc');
			$this->db->limit(1);
			$prev_id = $this->db->get( 'autocontrole_predifined' )->row_array();
			return array('prev_id' => $prev_id['id'],'next_id'=>$nxt_id['id']);
			
		}
	}

	/**
     * Function to get get object for edit
     * @access Public
     * @param $id int
	 * @return $id int
	 * @author Abhishek Singh 
    */
	function updateObjects( $post_data ) {
		if( $post_data[ 'id' ] ) {
			$id = $post_data[ 'id' ];
			unset( $post_data[ 'id' ] );
			$this->db->where( 'o_id', $id );
			$this->db->update( 'autocontrole_objects', $post_data );
			return $id;
		} else {
			unset( $post_data[ 'id' ] );
			$this->db->insert( 'autocontrole_objects', $post_data );
			return $this->db->insert_id();
		}
	}

	function delete_object( $id ) {
		$this->db->where( 'o_id', $id );
		return $this->db->delete( 'autocontrole_objects' );
	}

	function searchObjects( $search_type, $search_keyword ) {
		$this->db->select( '*' );
		if( $search_type == 'id' ){
			$this->db->where( 'o_id', $search_keyword );
		} elseif ( $search_type == 'name' ) {
			$condition = "(`object_name_dch`  LIKE '%".$search_keyword."%' OR  `object_name` LIKE '%".$search_keyword."%' OR  `object_name_fr`  LIKE '%".$search_keyword."%')";
			$this->db->where( $condition );
		}
		$result[ 'autocontrole_objects' ] =$this->db->get( 'autocontrole_objects' )->result_array();
		return $result;
	}
	/**
	 * Function to fetch tasks names
     * @access Public
	 * @return $data array
	 * @author Abhishek Singh 
    */
	function getalltasksname(){
		$this->db->select( 'haccp_tasks.id,haccp_tasks.type_id,object_name, pred_task_name,location,company_id, autocontrole_type.type_name'.$this->lang_u.' as type_name, company.username' );
		$this->db->join( 'autocontrole_type', 'autocontrole_type.id = haccp_tasks.type_id' );
		$this->db->join( 'company', 'company.id = haccp_tasks.company_id' );
		$result[ 'task_name' ] = $this->db->get( 'haccp_tasks' )->result_array();
		$final_task = array( );
		if( !empty( $result[ 'task_name' ] ) ){
			foreach ( $result[ 'task_name' ] as $key => $value ) {
				$this->db->select( 'type_id' );
				$this->db->where( 'id', $value[ 'company_id' ] );
				$user_type_id = $this->db->get( 'company' )->row_array();
				$user_type_id = explode( "#", $user_type_id[ 'type_id' ] );
				$result[ 'task_name' ][ $key ][ 'user_type_id' ] = $user_type_id;

				$final_task[ $value[ 'id' ] ] = $result[ 'task_name' ][ $key ];
			}
		}
		
		$data[ 'allTask' ] = $final_task;
		return $data;
	}

	function getallpredifined(){
		$this->db->select( 'id,type_id,name'.$this->lang_u.' as predifined_name' );
		$result[ 'task_name' ] = $this->db->get( 'autocontrole_predifined' )->result_array();
		$final_task = array( );
		if( !empty( $result[ 'task_name' ] ) ){
			foreach ( $result[ 'task_name' ] as $key => $value ) {
				$final_task[ $value[ 'id' ] ] = $result[ 'task_name' ][ $key ];
			}
		}
		$data[ 'allTask' ] 			=   $final_task;
		return $data;
	}

	function get_pre_object(){
		$this->db->select( 'o_id,object_name' );
		$result[ 'object_name' ] = $this->db->get( 'autocontrole_objects' )->result_array();
		$this->db->select( 'o_id,object_name_fr' );
		$result[ 'object_name_fr' ] = $this->db->get( 'autocontrole_objects' )->result_array();
		$this->db->select( 'o_id,object_name_dch' );
		$result[ 'object_name_dch' ] = $this->db->get( 'autocontrole_objects' )->result_array();
		return $result;
	}
	
	function gettypename(){
		$this->db->select( 'id, type_name'.$this->lang_u. ' as type_name' );
		$result[ 'autocontrole_type' ] = $this->db->get( 'autocontrole_type' )->result_array();
		return $result;
	}
	
	function get_predefined($type_id){
		$this->db->select( 'id, name'.$this->lang_u. ' as name' );
		$this->db->where('type_id',$type_id);
		$result[ 'predifined' ] = $this->db->get( 'autocontrole_predifined' )->result_array();
		return $result;
	}

	function get_objects($type_id){
		$this->db->select( 'o_id,object_name'.$this->lang_u. ' as name' );
		$this->db->where('type_id',$type_id);
		$result[ 'objects' ] = $this->db->get( 'autocontrole_objects' )->result_array();
		return $result;
	}
	
	function update_task( $id, $update_arr ){
		$this->db->where( 'id', $id );
		return $this->db->update( 'haccp_tasks', $update_arr );
	}

	function update_task_object( $insert_data, $company_id ){

		$this->db->insert( 'autocontrole_objects', $insert_data );
		$insert_id = $this->db->insert_id();
		
		$this->db->select( 'objects' );
		$this->db->like( 'objects', $insert_data[ 'object_name_dch' ] );
		$this->db->where( 'company_id', $company_id );
		$query = $this->db->get( 'haccp_object_data' )->row_array();

		if( !empty( $query ) ) {
			$objects 	= json_decode( $query[ 'objects' ], true );
			$obj_names 	= array_column( $objects, 'obj_name' );
			if( in_array( $insert_data[ 'object_name_dch' ] , $obj_names ) ) {
				foreach ( $objects as $key => $object ) {
					if(  ( strtolower( $object[ 'obj_name' ] ) == strtolower( $insert_data[ 'object_name_dch' ] ) ) && $object[ 'obj_name' ] == $insert_data[ 'group_id' ] ){
						$objects[ $key ][ 'obj_name' ] = $insert_id;
					}
				}
				
				$this->db->where( 'company_id', $company_id );
				$this->db->update( 'haccp_object_data', array( 'objects' => json_encode( $objects ) ) );

				$this->db->select( 'id,fixed_check_data' );
				$this->db->like( 'fixed_check_data', $insert_data[ 'object_name_dch' ] );
				$this->db->where( 'company_id', $company_id );
				$fixed_check_data_all = $this->db->get( 'haccp_fixed_check' )->result_array();

				if( !empty( $fixed_check_data_all ) ){
					foreach ( $fixed_check_data_all as $k => $fixed_check ) {
						$fixed_check_data 	= json_decode( $fixed_check[ 'fixed_check_data' ], true );
						$fixed_check_obj 	= array_column( $fixed_check_data, 'obj' );
						if( in_array( $insert_data[ 'object_name_dch' ] , $fixed_check_obj ) ) {
							foreach ( $fixed_check_data as $x => $value ) {
								if( strtolower( $value[ 'obj' ] )  == strtolower( $insert_data[ 'object_name_dch' ] ) ){
									$fixed_check_data[ $x ][ 'obj' ] = $insert_id;
								}
							$this->db->where( array( 'company_id' => $company_id , 'id' => $fixed_check[ 'id' ] ) );
							$this->db->update( 'haccp_fixed_check', array( 'fixed_check_data' => json_encode( $fixed_check_data ) ) );
							}
						}
					}
				}	
			}
		}
		$this->db->where( 'company_id', $company_id );
		$this->db->where( 'LOWER(object_name)', strtolower( $insert_data[ 'object_name_dch' ] ) );
		return $this->db->update( 'haccp_tasks', array( 'object_name' => $insert_id ) );
	}

	function update_task_location( $company_id, $loc_name, $data ){
		$this->db->select( 'objects' );
		$this->db->where( 'company_id', $company_id );
		$query = $this->db->get( 'haccp_object_data' )->row_array();
		if( !empty( $query ) ) {
			$objects = json_decode( $query[ 'objects' ], true );
			foreach ( $objects as $key => $value ) {
				if( $value[ 'obj_loc' ] == $loc_name ) {
					$objects[ $key ][ 'obj_loc' ] = $data[ 'location' ];
				}
			}
			$this->db->where( 'company_id', $company_id );
			$this->db->update( 'haccp_object_data', array( 'objects' => json_encode( $objects ) ) );
		}
		$this->db->select('location,id');
		$this->db->where( 'company_id', $company_id );
		$this->db->like( 'location',$loc_name  );
		$res = $this->db->get( 'haccp_tasks')->result_array();
		if(!empty($res)){
			foreach ($res as $key => $loc) {
				if(isset($loc)){
					$loctn = explode('#', $loc ['location']);
					foreach ($loctn as $key => $value) {
						if($value == $loc_name ){
							$loctn[$key] = $data[ 'location' ];
							$location['location'] = implode('#', $loctn);
							$this->db->where('id', $loc['id']);
						 	$this->db->update('haccp_tasks', $location);
						}
					}
				}
			}
			return 'success';
		}
	}
	/**
	 * Function to get category for edit
	 */
	function get_category($category_id){
		if( $category_id ){
			$this->db->select( '*' );
			$result[ 'autocontrole_category' ] = $this->db->get_where(  'autocontrole_category',array( 'id' => $category_id ) )->row_array( );
			return $result;
		}
	}
	function add_category($data){
		if( !empty( $data ) ){
			$this->db->insert( 'autocontrole_category', $data );
			return $this->db->insert_id();
		}	
	}
	function getallcategory(){
		$this->db->select( '*' );
		$result[ 'autocontrole_category' ] = $this->db->get( 'autocontrole_category' )->result_array( );
		return $result;
	}
	/**
	 * Function to delete Category
	 */
	function delete_category( $cat_id ){
		if( isset( $cat_id ) ){
			$this->db->where( 'id', $cat_id );
	  		$query = $this->db->delete( 'autocontrole_category' );
	  		return $query; 
			
		}
	}
	function update_category($data,$cat_id){
		if( !empty( $data )){
			$this->db->where( 'id', $cat_id );
			$this->db->update( 'autocontrole_category', $data );
			return $this->db->affected_rows( );
		}
	}

	/*======================================
	=            Domain section            =
	======================================*/
	/**
	 * Function to get all domain for listing
	 */
	function getallDomains( ){
		$this->db->select( '*' );
		$result[ 'autocontrole_domains' ] = $this->db->get( 'autocontrole_domains' )->result_array( );
		return $result;

	}
	/**
	 * Function to add new domain
	 */
	function add_domain( $data ){
		if( !empty( $data ) ){
			$this->db->insert( 'autocontrole_domains', $data );
			return $this->db->insert_id( );
		}
	}

	/**
	 * Function to get single domain for edit
	 */
	function get_single_domain( $domain_id ){
		if( $domain_id ){
			$this->db->select( '*' );
			$result[ 'domains' ] = $this->db->get_where(  'autocontrole_domains',array( 'id' => $domain_id ) )->row_array( );
			return $result;
		}
	}

	/**
	 * Function to update sub type
	 */
	function update_domain( $data,$domain_id ){
		if( !empty( $data ) && isset( $domain_id ) ){
			$this->db->where( 'id', $domain_id );
			$this->db->update( 'autocontrole_domains', $data );
			return $this->db->affected_rows( );
		}
	}
	
	/*=====  End of Domain section  ======*/

	/*=========================================
	=            Checklist Section            =
	=========================================*/
	/**
	 * Function to get the domain name as per the locale
	 * @param $id int
	 * @return $data array
	 * @author Abhishek Singh 
	 */
	function getallDomainname(){
		$this->db->select( 'id, domain_name'.$this->lang_u. ' as domain_name' );
		$result[ 'autocontrole_domains' ] = $this->db->get( 'autocontrole_domains' )->result_array();
		$final_domain = array( );
		if( !empty( $result[ 'autocontrole_domains' ] ) ){
			foreach ( $result[ 'autocontrole_domains' ] as $key => $value ) {
				$final_domain[ $value[ 'id' ] ] = $result[ 'autocontrole_domains' ][ $key ];
			}
		}
		$data[ 'domains' ] 				=  $final_domain;
		return $data;
	}
	
	/**
	 * Function to add checklist
	 */
	function add_checklist( $data ){
		if ( !empty( $data ) ) {
			$this->db->insert( 'autocontrole_checklist', $data );
			return $this->db->insert_id( );
		}
	}
	/**
	 * Function to Return all checklist
	 */
	function getallchecklist( ){
		$this->db->select( '*' );
		$this->db->order_by( 'display_order',  "asc" );
		return $this->db->get( 'autocontrole_checklist' )->result_array( );
	}
	/**
	 * Function to Return Specific checklist
	 */
	function getSpecific_checklist( $checklist_id ){
		if( $checklist_id ){
			$this->db->select( '*' );
			return $this->db->get_where( 'autocontrole_checklist', array( 'id' => $checklist_id ) )->row_array( );
		}
	}

	/**
	 * Function to update Specific checklist
	 */
	function update_checklist( $data , $check_list_id  ){
		if( isset(  $check_list_id ) && !empty( $data ) ){
			$this->db->where( 'id' , $check_list_id );
			$this->db->update( 'autocontrole_checklist' , $data );
			return $this->db->affected_rows();
		}
	}
	/**
	 * Function to get search result of checklist
	 */
	function get_searched_checklist( $search_by, $search_keyword ){
		if( isset(  $search_by ) && isset( $search_keyword ) ){
			if( $search_by == 'id' ){
				$this->db->select( '*' );
				return $this->db->get_where( 'autocontrole_checklist' ,array( 'id' => $search_keyword ) )->result_array( );
			}elseif( $search_by == 'checklist_item' ){
				$this->db->select( '*' );
				$this->db->like( 'question', $search_keyword  );
				$this->db->or_like( 'question_fr', $search_keyword );
				$this->db->or_like( 'question_dch', $search_keyword );
				$this->db->order_by( 'display_order',  "asc" );
				return $this->db->get( 'autocontrole_checklist' )->result_array( );
			}elseif( $search_by == 'domain' ){
				$autocontrole_checklis = array( );
				$this->db->select( 'id' );
				$this->db->like( 'domain_name', $search_keyword  );
				$this->db->or_like( 'domain_name_fr', $search_keyword  );
				$this->db->or_like( 'domain_name_dch', $search_keyword  );
				$domains = $this->db->get( 'autocontrole_domains' )->result_array();
				if( !empty( $domains ) ){
					$domains = array_column( $domains, 'id' );
					$this->db->select( '*' );
					$this->db->where_in( 'domain_id', $domains );
					$this->db->order_by( 'display_order',  "asc" );
					$autocontrole_checklist =  $this->db->get( 'autocontrole_checklist' )->result_array( );

				}
				return $autocontrole_checklist;
			}elseif( $search_by == 'companytype' ){
				$autocontrole_checklist = array( );
				$this->db->select( 'id' );
				$this->db->like( 'company_type_name', $search_keyword  );
				$company_type = $this->db->get( 'company_type' )->result_array();
				if( !empty( $company_type ) ){
					$company_type_id = array_column( $company_type, 'id' );
					$allchecklist = $this->getallchecklist( );
					if( !empty( $allchecklist ) ){
						foreach ( $allchecklist as $key => $value ) {
							if( $value[ 'company_type' ] != '' ){
								$companytype_ids = json_decode( $value[ 'company_type' ] );
								$interse = array_intersect( $companytype_ids, $company_type_id );

								if( sizeof( $interse ) > 0 ){
									array_push( $autocontrole_checklist, $allchecklist[ $key ] );
								}
							}
						}
					}
				}
				return $autocontrole_checklist;
			}
		}
	}

	/**
	 * Function to delete checklist
	 */
	function delete_checklist( $checklist_id  ){
		$this->db->where( 'id', $checklist_id );
	  	return $this->db->delete( 'autocontrole_checklist' );
	}

	/**
	 * Function to Update ChecklistOrder 
	 */
	function updateChecklist_order( $checklist_order ){
		if( is_array( $checklist_order  ) ){
			foreach ( $checklist_order  as $key => $checkOrd ) {
				$this->db->where( 'id', $checkOrd[ 'id' ] );
				$this->db->update( 'autocontrole_checklist', array( 'display_order' => $checkOrd[ 'order' ] ) );
			}
			return true;
		}
	}

	/**
	 * Function get all subtype
	 * @access Public
	 * @return $result array 
	 * @author Sudhansh Awasthi
	 */
	function get_all_company_type( ){
		$this->db->select( 'id, company_type_name' );
		return $this->db->get( 'company_type' )->result_array( );
	}
	/**
	 * Function get all category for objects
	 * @access Public
	 * @return $result array 
	 * @author Abhishek Singh
	 */
	
	function getall_objects_category(){
		$this->db->select( 'id,object_name'.$this->lang_u.' as object_cat_name' );
		$result[ 'objects_category' ] = $this->db->get( 'autocontrole_objects_category' )->result_array( );

		$this->db->select( 'id,type_name'.$this->lang_u.' as type_name' );
		$result[ 'autocontrole_type' ] = $this->db->get( 'autocontrole_type' )->result_array( );

		return $result;
	}



	/*=======================================
	=            Category Object            =
	=======================================*/

	/**
	 * Function to add object category
	 * @access Public
	 * @param   $data  array
	 * @return  inserted id int 
	 * @author Abhishek Singh  
	 */
	function add_object_category( $data ){
		if( !empty( $data  ) ){
			$this->db->insert( 'autocontrole_objects_category', $data );
			return $this->db->insert_id( );
		}
	}

	/**
	 * Function to get all object category for listing
	 * @access Public
	 * @return  $result array 
	 * @author Abhishek Singh  
	 */
	function get_objects_category_detail( ){
		$this->db->select( '*' );
		$this->db->order_by( 'cat_type' );
		$result[ 'autocontrole_objects_category' ] = $this->db->get( 'autocontrole_objects_category' )->result_array( );
		return $result;
	}
	

	/**
	 * Function to get single category of objects
	 * @access Public
	 * @param   $objects_category_id  int
	 * @return $result array 
	 * @author Abhishek Singh  
	 */
	function get_single_object_category( $objects_category_id ){
		if(  $objects_category_id ){
			$this->db->select( '*' );
			return $this->db->get_where( 'autocontrole_objects_category', array( 'id' => $objects_category_id ) )->row_array( );
		}
	}

	/**
	 * Function to update single category of objects
	 * @access Public
	 * @param  $data  array
	 * @param  $object_id  int
	 * @return rows affected int 
	 * @author Abhishek Singh  
	 */
	function update_object_category( $data ,$object_id ){
		if( isset( $object_id ) && !empty( $data ) ){
			$this->db->where( 'id' , $object_id );
			$this->db->update( 'autocontrole_objects_category' , $data );
			return $this->db->affected_rows();
		}
	}

	/**
	 * Function to delete single category of objects
	 * @access Public
	 * @param  $object_id  int
	 * @author Abhishek Singh  
	 */
	function delete_objects_category( $object_id ){
		if( $object_id ){
			$this->db->where( 'type_id', $object_id );
	  		$this->db->delete( 'autocontrole_objects' );

			$this->db->where( 'id', $object_id );
	  		$query = $this->db->delete( 'autocontrole_objects_category' );
		}
	
	}
	/*=====  End of Category Object  ======*/

	/**
	 * Function to modify the manually created value in haccp task
	 * @access Public
	 * @param  $object_id  int
	 * @author Abhishek Singh  
	 */
	function update_manually_created_predif( $id , $manual_text , $type_id, $company_id ){
		$this->db->select( 'id' );
		$predifined = $this->db->get_where( 'haccp_tasks' , array( 'pred_task_name' => $manual_text, 'company_id' => $company_id ) )->result_array( );
		if( !empty( $predifined ) ){
			$predifined = array_column(  $predifined,  'id' );
			$this->db->where_in( 'id' , $predifined );
			$this->db->update( 'haccp_tasks' , array( 'type_id' => $type_id ,'pred_task_name' => $id ) );
		}
	}

	/**
		 * Function to hide the manually entered value from Predifined manual entry
		 * @access Public
		 * @param  String 
		 * @author Abhishek Singh 
		 */
	function hidden_manually_entered_value( $predif_text, $company_id, $section ){

		$this->db->insert( 'autocontrole_deleted_elements', array( 'text' => $predif_text, 'company_id' => $company_id, 'section' => $section, 'delete_on' => date("Y-m-d") ) );
		return $this->db->insert_id();
	}

	/**
		 * Function to delete the autocontrole type and the tasks related to it.
		 * @access Public
		 * @param  int 
		 * @author Sudhansh Awasthi 
		 */

	function delete_type( $type_id ) {

		$this->db->select( 'id' );
		$this->db->where( 'type_id', $type_id );
		$task_ids = $this->db->get( 'autocontrole_predifined' )->result_array();
		$task_ids = array_column( $task_ids, 'id' );

		$this->db->select( 'id, connected_tasks' );
		$this->db->where( 'connected_tasks !=', '' );
		$checklist = $this->db->get( 'autocontrole_checklist' )->result_array();

		foreach ( $checklist as $key => $value ) {	
			if( !empty( json_decode( $value[ 'connected_tasks' ] ) ) ) {
				$connected_tasks 	= json_decode( $value[ 'connected_tasks' ] );
				$intersect 			= array_intersect( $connected_tasks, $task_ids );
				if( !empty( $intersect ) ) {
					$new_connected_tasks = array_values( array_diff( $connected_tasks, $intersect ) );
					$this->db->where( 'id', $value[ 'id' ] );
					$this->db->update( 'autocontrole_checklist', array( 'connected_tasks' => json_encode( $new_connected_tasks ) ) );
				}
			}
		}
		$this->db->where( 'type_id', $type_id );
		$this->db->delete( 'autocontrole_predifined' );

		$this->db->where( 'type_id', $type_id );
		$this->db->delete( 'haccp_tasks' );

		$this->db->where( 'id', $type_id );
		return $this->db->delete( 'autocontrole_type' );


	}

	/**
	 * Function to get the autocontrole temperature group.
	 * @access Public
	 * @author Sudhansh Awasthi,Abhishek Singh
	 */

	function get_all_temp_group( $specific_lang = '', $country_code = 'BE' ) {
		if( $specific_lang ){
			$this->db->select('id, temp_group_name'.$this->lang_u.' as temp_group_name');
		}
		$this->db->order_by( 'display_order', 'asc');
		$this->db->where( 'country_code', $country_code );
		return $this->db->get( 'autocontrole_temp_group' )->result_array();
	}

	/**
	 * Function to get the detail of specific temperature group.
	 * @access Public
	 * @param  int $id
	 * @author Sudhansh Awasthi 
	 */

	function get_temp_group( $id ) {
		$this->db->where( 'id', $id );
		$this->db->order_by( 'display_order', 'asc');
		return $this->db->get( 'autocontrole_temp_group' )->row_array();
	}

	/**
	 * Function to insert and update the detail of specific temperature group.
	 * @access Public
	 * @param  array $data
	 * @author Sudhansh Awasthi 
	 */

	function update_temp_group( $data ) {
		if( isset( $data[ 'ideal_temp'] ) ){
			$data[ 'ideal_temp'] = str_replace( ',', '.', $data[ 'ideal_temp'] );
		}
		if( isset( $data[ 'min_temp'] ) ){
			$data[ 'min_temp'] = str_replace( ',', '.', $data[ 'min_temp'] );
		}
		if( isset( $data[ 'max_temp'] ) ){
			$data[ 'max_temp'] = str_replace( ',', '.', $data[ 'max_temp'] );
		}
		if( $data[ 'action' ] == 'Add' ) {
			$data[ 'added_date' ] = date( 'Y-m-d H:i:s' );
			unset( $data[ 'action' ] );
			return $this->db->insert( 'autocontrole_temp_group', $data );
		} else if( $data[ 'action' ] == 'Edit' ) {
			unset( $data[ 'action' ] );
			$this->db->where( 'id', $data[ 'id' ] );
			return $this->db->update( 'autocontrole_temp_group', $data );
		} else {
			return false;
		}
	}

	/**
	 * Function to delete the detail of specific temperature group.
	 * @access Public
	 * @param  int $id
	 * @author Sudhansh Awasthi 
	 */

	function delete_temp_group( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->delete( 'autocontrole_temp_group' );
	}

	/*===========================================
	=            CCP PVA GHP Section            =
	===========================================*/
	/**
	 * Function to save data for add edit
	 * @access Public
	 * @param  array $data
	 * @author Abhishek Singh
	 */
	function add_editccp_pva_ghp_data( $data ){
		if( !empty( $data ) ){
			unset( $data[ 'action_btn' ] );
			if( isset( $data[ 'id' ] ) ){
				$id = $data[ 'id' ];
				unset( $data[ 'id' ] );
				$data[ 'updated_date' ] = date( 'Y-m-d H:i:s' );
				$this->db->where( 'id', $id );
				return $this->db->update( 'autocontrole_ccp_pva_ghp', $data );
			}else{
				$data[ 'added_date' ] = date( 'Y-m-d H:i:s' );
				return $this->db->insert( 'autocontrole_ccp_pva_ghp', $data );
			}
		}
	}

	/**
	 * Function to get all  CCP PVA GHP data
	 * @access Public
	 * @param  int $id
	 * @return $autocontrole_ccp_pva_ghp array
	 * @author  Abhishek Singh
	 */
	function get_all_ccp_pva_ghp( $country_code ){
		$this->db->where( 'country_code', $country_code );
		$autocontrole_ccp_pva_ghp = $this->db->get( 'autocontrole_ccp_pva_ghp' )->result_array( );
		return $autocontrole_ccp_pva_ghp;
	}

	/**
	 * Function to get specific  CCP PVA GHP data
	 * @access Public
	 * @param  int $id
	 * @return $autocontrole_ccp_pva_ghp array
	 * @author  Abhishek Singh
	 */

	function get_ccp_pva_ghp( $id ){
		$autocontrole_ccp_pva_ghp = $this->db->get_where( 'autocontrole_ccp_pva_ghp' , array( 'id' => $id ) )->row_array( );
		return $autocontrole_ccp_pva_ghp;
	}

	/**
	 * Function to delete specific  CCP PVA GHP data
	 * @access Public
	 * @param  int $id
	 * @author  Abhishek Singh
	 */
	function delete_ccp_pva_ghp( $id ){
		$this->db->where( 'id', $id );
		return $this->db->delete( 'autocontrole_ccp_pva_ghp' );
	}

	function get_searched_ccp_pva_ghp( $search_by, $search_keyword, $country_code = 'BE' ){
		if( isset(  $search_by ) && isset( $search_keyword ) ){
			if( $search_by == 'id' ){
				$this->db->select( '*' );
				$this->db->where( 'country_code', $country_code  );
				return $this->db->get_where( 'autocontrole_ccp_pva_ghp' ,array( 'id' => $search_keyword ) )->result_array( );
			}elseif( $search_by == 'processtep' ){
				$this->db->select( '*' );
				$this->db->where( 'country_code', $country_code  );
				$this->db->group_start();
				$this->db->like( 'processtep', $search_keyword  );
				$this->db->or_like( 'processtep_fr', $search_keyword  );
				$this->db->or_like( 'processtep_dch', $search_keyword  );
				$this->db->group_end();
				return $this->db->get( 'autocontrole_ccp_pva_ghp' )->result_array( );
			}elseif( $search_by == 'danger' ){
				$this->db->select( '*' );
				$this->db->where( 'country_code', $country_code  );
				$this->db->group_start();
				$this->db->like( 'danger', $search_keyword  );
				$this->db->or_like( 'danger_fr', $search_keyword  );
				$this->db->or_like( 'danger_dch', $search_keyword  );
				$this->db->group_end();
				return  $this->db->get( 'autocontrole_ccp_pva_ghp' )->result_array( );
			}elseif( $search_by == 'companytype' ){
				$autocontrole_ccp_pva_ghp = array( );
				$this->db->select( 'id' );
				$this->db->like( 'company_type_name', $search_keyword  );
				$company_type = $this->db->get( 'company_type' )->result_array();
				if( !empty( $company_type ) ){
					$company_type_id = array_column( $company_type, 'id' );
					$all_ccp_pva_ghp = $this->get_all_ccp_pva_ghp( $country_code );
					if( !empty( $all_ccp_pva_ghp ) ){
						foreach ( $all_ccp_pva_ghp as $key => $value ) {
							if( $value[ 'company_type' ] != '' ){
								$companytype_ids = json_decode( $value[ 'company_type' ] );
								$interse = array_intersect( $companytype_ids, $company_type_id );

								if( sizeof( $interse ) > 0 ){
									array_push( $autocontrole_ccp_pva_ghp, $all_ccp_pva_ghp[ $key ] );
								}
							}
						}
					}
				}
				return $autocontrole_ccp_pva_ghp;
			}elseif( $search_by == 'ccp_pva_ghp' ){
				$this->db->select( '*' );
				$this->db->where( 'country_code', $country_code );
				$this->db->like( 'ccp_pva_ghp', strtolower( $search_keyword ) );
				return  $this->db->get( 'autocontrole_ccp_pva_ghp' )->result_array( );
			}
		}
	}
	/*=====  End of CCP PVA GHP Section  ======*/

	/**
	 *
	 * Function to get all objects
	 *
	 */
	
	function get_haccp_objects( ){
		$this->db->select( 'company_id,objects,company.username' );
		$this->db->join( 'company', 'company.id = haccp_object_data.company_id' );
		return $this->db->get( 'haccp_object_data' )->result_array();
		
	}
	
	/* get admins object data to get the manually added location */

	function get_haccp_object_data() {
		$final_loc = array();
		$this->db->select( 'objects, company_id, company.username' );
		$this->db->join( 'company', 'company.id = haccp_object_data.company_id' );
		$query = $this->db->get( 'haccp_object_data' )->result_array();

		foreach ( $query as $key => $value ) {
			if( !empty( json_decode( $value[ 'objects' ], true ) ) ) {
				$objects 	= json_decode( $value[ 'objects' ], true );
				$locations 	= array_column( $objects, 'obj_loc' );
				foreach ( $locations as $k => $loc_name ) {
					if( ! is_numeric( $loc_name ) ) {
						array_push( $final_loc, array( 'company_id' => $value[ 'company_id' ], 'location' => $loc_name, 'username' => $value['username'] ) );
					}
				}
			}
		}
		return $final_loc;
	}

	/**
	 * Function to add Procedure
	 */
	function add_module( $data ){
		if( !empty( $data ) ){
			if( isset(  $data[ 'id' ] ) ){
				$this->db->where('id',   $data[ 'id' ]  );
				$this->db->update('autocontrole_module', $data );
				return $this->db->trans_status();
			}else{
				$this->db->insert( 'autocontrole_module', $data );
				return $this->db->insert_id( );
			}
		}
	}

	/**
	 * Function to get module for edit
	 */
	function get_module( $module_id ){
		if( $module_id ){
			$this->db->select( '*' );
			$result[ 'autocontrole_module' ] = $this->db->get_where(  'autocontrole_module',array( 'id' => $module_id ) )->row_array( );
			return $result;
    }
  }

  	/*
	 * Function to get the detail of specific pasteurization group.
	 * @access Public
	 * @param  int $id
	 * @author Sudhansh Awasthi 
	 */

	function get_pasteur_group( $id = '', $country_code = '' ) {
		if( $id == '' ) {
			$this->db->where( 'country_code', $country_code );
			return $this->db->get( 'pasteur_group' )->result_array();
		} else {
			$this->db->where( 'id', $id );
			return $this->db->get( 'pasteur_group' )->row_array();
		}
	}

	/**
	 * Function to get all Module
	 */
	function getallmodules( ){
		$this->db->select( '*' );
		$result[ 'autocontrole_module' ] = $this->db->get( 'autocontrole_module' )->result_array( );
		return $result;
	}
	/**
	 * Function to delete Procedure
	 */
	function delete_module( $id ){
		$data = $this->get_module( $id  );
		if( !empty( $data ) && isset( $data['autocontrole_module'] ) && !empty( $data['autocontrole_module'] ) ){
			if( isset( $data['autocontrole_module'][ 'file' ] ) && $data['autocontrole_module'][ 'file' ] != '' && file_exists( dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/'.$data['autocontrole_module'][ 'file' ] ) ){
			  		unlink( dirname(__FILE__).'/../../../assets/autocontrole/module_pdf/'.$data['autocontrole_module'][ 'file' ] );
			}
			$this->db->where('id', $id );
			$this->db->delete('autocontrole_module');
			return $this->db->affected_rows();
		}

	}

	/**
	 * Function to search the modules as per id and text 
	 */
	function getSearch_modules( $data ){
		if( $data[ 'search_type' ] == 'id' ){
			$this->db->select( '*' );
			$result[ 'autocontrole_module' ] = $this->db->get_where(  'autocontrole_module',array( 'id' => $data[ 'search_keyword' ]) )->result_array( );
			return $result;
		}else{
			$search_txt = explode( ' ', trim( $data[ 'search_keyword' ] ) );
			$final_arr_proc = array( );
			if( !empty( $search_txt ) ){
				foreach ( $search_txt as $key => $word ){
					$this->db->select( '*' );
					$this->db->where( " `module` LIKE '%".$word."%' OR `module_dch` LIKE '%".$word."%' OR `module_fr` LIKE '%".$word."%'" );
					$temp_result = $this->db->get(  'autocontrole_module' )->result_array( );
					$final_arr_proc = array_merge($final_arr_proc, $temp_result);
					
				}
			}
			if( !empty( $final_arr_proc ) ){
				$final_arr_proc = array_map('unserialize', array_unique(array_map('serialize', $final_arr_proc )));
			}
			$result[ 'autocontrole_module' ] = $final_arr_proc;
			return $result;
		}
	}
	/*
	 * Function to insert and update the detail of specific pasteurization group.
	 * @access Public
	 * @param  array $data
	 * @author Sudhansh Awasthi 
	 */

	function update_pasteur_group( $data ) {
		if( isset( $data[ 'ideal_temp'] ) ){
			$data[ 'ideal_temp'] = str_replace( ',', '.', $data[ 'ideal_temp'] );
		}
		if( isset( $data[ 'min_temp'] ) ){
			$data[ 'min_temp'] = str_replace( ',', '.', $data[ 'min_temp'] );
		}
		if( isset( $data[ 'max_temp'] ) ){
			$data[ 'max_temp'] = str_replace( ',', '.', $data[ 'max_temp'] );
		}
		if( $data[ 'action' ] == 'Add' ) {
			$data[ 'added_date' ] = date( 'Y-m-d H:i:s' );
			unset( $data[ 'action' ] );
			return $this->db->insert( 'pasteur_group', $data );
		} else if( $data[ 'action' ] == 'Edit' ) {
			unset( $data[ 'action' ] );
			$this->db->where( 'id', $data[ 'id' ] );
			return $this->db->update( 'pasteur_group', $data );
		} else {
			return false;
		}
	}

	/**
	 * Function to delete the detail of specific pasteurization group.
	 * @access Public
	 * @param  int $id
	 * @author Sudhansh Awasthi 
	 */

	function delete_pasteur_group( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->delete( 'pasteur_group' );
	}


	/**
	 * Function to get the autocontrole work room
	 * @access Public
	 * @author Abhisheks singh
	 */

	function get_all_workroom( $specific_lang = '' ) {
		if( $specific_lang ){
			$this->db->select('id, workroom_name'.$this->lang_u.' as workroom_name');
		}
		return $this->db->get( 'autocontrole_temperature_workroom' )->result_array();
	}

	/**
	 * Function to insert and update the detail of specific temperature group.
	 * @access Public
	 * @param  array $data
	 * @authorAbhisheks singh
	 */

	function update_workgroup( $data ) {
		if( isset( $data[ 'ideal_temp'] ) ){
			$data[ 'ideal_temp'] = str_replace( ',', '.', $data[ 'ideal_temp'] );
		}
		if( isset( $data[ 'min_temp'] ) ){
			$data[ 'min_temp'] = str_replace( ',', '.', $data[ 'min_temp'] );
		}
		if( isset( $data[ 'max_temp'] ) ){
			$data[ 'max_temp'] = str_replace( ',', '.', $data[ 'max_temp'] );
		}
		if( $data[ 'action' ] == 'Add' ) {
			$data[ 'added_date' ] = date( 'Y-m-d H:i:s' );
			unset( $data[ 'action' ] );
			return $this->db->insert( 'autocontrole_temperature_workroom', $data );
		} else if( $data[ 'action' ] == 'Edit' ) {
			unset( $data[ 'action' ] );
			$this->db->where( 'id', $data[ 'id' ] );
			return $this->db->update( 'autocontrole_temperature_workroom', $data );
		} else {
			return false;
		}
	}

	/**
	 * Function to get the autocontrole calibration
	 * @access Public
	 */

	function get_all_calibration( $specific_lang = '' ) {
		if( $specific_lang ){
			$this->db->select('id, workroom_name'.$this->lang_u.' as workroom_name');
		}
		return $this->db->get( 'autocontrole_calibration_workroom' )->result_array();
	}

		/**
	 * Function to insert and update the detail of specific temperature group.
	 * @access Public
	 * @param  array $data
	 */

	function update_calibration( $data ) {
		if( isset( $data[ 'ideal_temp'] ) ){
			$data[ 'ideal_temp'] = str_replace( ',', '.', $data[ 'ideal_temp'] );
		}
		if( isset( $data[ 'reference_temp'] ) ){
			$data[ 'reference_temp'] = str_replace( ',', '.', $data[ 'reference_temp'] );
		}
		if( $data[ 'action' ] == 'Add' ) {
			$data[ 'added_date' ] = date( 'Y-m-d H:i:s' );
			unset( $data[ 'action' ] );
			return $this->db->insert( 'autocontrole_calibration_workroom', $data );
		} else if( $data[ 'action' ] == 'Edit' ) {
			unset( $data[ 'action' ] );
			$this->db->where( 'id', $data[ 'id' ] );
			return $this->db->update( 'autocontrole_calibration_workroom', $data );
		} else {
			return false;
		}
	}

	/**
	 * Function to get the detail of specific work room
	 * @access Public
	 * @param  int $id
	 */

	function get_workroom( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->get( 'autocontrole_temperature_workroom' )->row_array();
	}

	/**
	 * Function to get the detail of specific work room
	 * @access Public
	 * @param  int $id
	 */

	function get_calibration( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->get( 'autocontrole_calibration_workroom' )->row_array();
	}

	/**
	 * Function to delete the detail of specific workroom
	 * @access Public
	 * @param  int $id
	 */

	function delete_workroom( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->delete( 'autocontrole_temperature_workroom' );
	}

	/**
	 * Function to delete the detail of specific workroom
	 * @access Public
	 * @param  int $id
	 */

	function delete_calibration( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->delete( 'autocontrole_calibration_workroom' );
	}

	function get_cp_locations( ){
		return $this->db->get_where( 'cp_dependent_location' )->result_array();			
		
	}	

	function get_related_predifineds( $selected_type ){
		if( !empty( $selected_type ) ){
			$this->db->select('id,name'.$this->lang_u.' as name');
			$this->db->where( 'type_id', $selected_type );
			return $this->db->get_where( 'autocontrole_predifined' )->result_array();
		}
	}

	function get_related_type_predifineds( $related_type_ids ){
		
		if( !empty( $related_type_ids ) ){
			$final_arr = array( );
			foreach ($related_type_ids as $key => $value) {
				$final_arr[ $value ] = $this->get_related_predifineds( $value );
			}
			return $final_arr;
		}
	}



	function get_all_ph_groups( $specific_lang = '' ) {
		if( $specific_lang ){
			$this->db->select('id,ph_grp_value ph_grp_name'.$this->lang_u.' as ph_grp_name');
		}
		return $this->db->get( 'autocontrole_ph_groups' )->result_array();
	}

	function delete_ph_groups( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->delete( 'autocontrole_ph_groups' );
	}

	function get_ph_groups( $id ) {
		$this->db->where( 'id', $id );
		return $this->db->get( 'autocontrole_ph_groups' )->row_array();
	}

	function update_ph_groups( $data ) {
		if( isset( $data[ 'grp_value'] ) ){
			$data[ 'grp_value'] = str_replace( ',', '.', $data[ 'grp_value'] );
		}
		
		if( $data[ 'action' ] == 'Add' ) {
			$data[ 'added_date' ] = date( 'Y-m-d H:i:s' );
			unset( $data[ 'action' ] );
			return $this->db->insert( 'autocontrole_ph_groups', $data );
		} else if( $data[ 'action' ] == 'Edit' ) {
			unset( $data[ 'action' ] );
			$this->db->where( 'id', $data[ 'id' ] );
			return $this->db->update( 'autocontrole_ph_groups', $data );
		} else {
			return false;
		}
	}

	/**
	 * Function to Update ChecklistOrder 
	 */
	function Update_temperatureGroup_order( $temperatureGroup_order ){
		if( is_array( $temperatureGroup_order  ) ){
			foreach ( $temperatureGroup_order  as $key => $order ) {
				$this->db->where( 'id', $order[ 'id' ] );
				$this->db->update( 'autocontrole_temp_group', array( 'display_order' => $order[ 'order' ] ) );
			}
			return true;
		}
	}

 }
?>