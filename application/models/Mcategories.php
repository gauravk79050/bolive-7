<?php
	class Mcategories extends CI_Model{
		var $exist=null;
		var $company_id=null;

		function __construct(){
			parent::__construct();
			$this->exist=null;
			$this->company_id=$this->session->userdata('cp_user_id');
		}

		function create_category( $ac_type_id = 0 ){
			foreach ($this->get_categories() as $category)
			{
				if($category->name==$this->input->post('name')){
					$this->exist=1;
				}
			}//endforeach
			if(!$this->exist)
			{
				$new_category_insert_data = array(
					'company_id' =>$this->company_id,
					'name' => $this->input->post('name'),
					'description' => $this->input->post('description'),
					'display_tool_tip' => $this->input->post('display_tool_tip'),
					'add_text' => $this->input->post('add_text'),
					'message' => $this->input->post('message'),
					'created' => date('Y-m-d',time())
				);
				
				if( isset( $ac_type_id ) && $ac_type_id == '7' ){
					$new_category_insert_data[ 'status' ] = 1 ;
				}
				
				$general_settings = $this->db->where('company_id',$this->company_id)->get('general_settings')->result();
				if($general_settings != array()){
					if($general_settings[0]->pickup_service == '1' && $general_settings[0]->delivery_service == '1'){
						$new_category_insert_data['service_type'] = "0";
					}else if($general_settings[0]->pickup_service == '1' && $general_settings[0]->delivery_service != '1'){
						$new_category_insert_data['service_type'] = "1";
					}else if($general_settings[0]->pickup_service != '1' && $general_settings[0]->delivery_service == '1'){
						$new_category_insert_data['service_type'] = "2";
					}
				}

				$new_category_insert_data['image'] = ''; //$this->upload_image();

				$insert = $this->db->insert('categories', $new_category_insert_data);

				/*if( $_FILES['image']['name'] != '' )
				{
				  $new_cat_id = $this->db->insert_id();
				  $this->upload_image($new_cat_id, $this->input->post('name'));
				}*/
				$new_cat_id = $this->db->insert_id();

				if($this->input->post('image_name')){

					//$this->image = $this->input->post('image_name');
					//$image = str_replace("cropped_","",$this->input->post('image_name'));



					$prefix = 'cropped_'.$this->company_id.'_';
					$str = $this->input->post('image_name');

					if (substr($str, 0, strlen($prefix)) == $prefix) {
						$str = substr($str, strlen($prefix));
					}

					if(isset($str) && $str != ''){
						$image_name = $this->company_id.'_'.$new_cat_id.'_'.$str;
						$image = 'assets/cp/images/categories/'.$image_name;//$image;
						//$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));//403 when directory access via url disabled
						$image_file = file_get_contents(dirname(__FILE__).'/../../assets/temp_uploads/'.$this->input->post('image_name'));
						file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);

						//$this->resize_category_images($image_name);
						$this->load->helper('resize');
						resize_images('categories',$image_name);
						$update_cat_data['image'] = $image;
					}

					$this->db->where('id',$new_cat_id);
					$this->db->update('categories', $update_cat_data);
				}
				/*if($this->input->post('image_name') && $this->input->post('image_url') && $this->input->post('image_size') <= 150 && ($this->input->post('image_type') == 'jpg' || $this->input->post('image_type') == 'jpeg' || $this->input->post('image_type') == 'png' || $this->input->post('image_type') == 'gif')){
					$image = 'assets/cp/images/categories/'.$new_cat_id.'_'.$this->input->post('name').'.'.$this->input->post('image_type');
					$image_file = file_get_contents($this->input->post('image_url'));
					file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);
					$update_cat_data['image'] = $image;
					$this->db->where('id',$new_cat_id);
					$this->db->update('categories', $update_cat_data);
				}*/

				return $insert;
			}else{
				return null;

			}
		}

		function upload_image( $cat_id = NULL, $cat_name = NULL ){

			$image = '';
			$config['upload_path']='./assets/cp/images/categories/';
			$config['allowed_types'] = 'gif|jpg|jpeg|JPG|GIF|png';
			$config['max_size']	= '150';
			$config['max_width']  = '300';
			$config['max_height']  = '300';
			$config['remove_spaces']  = true;

		    if($cat_id && $cat_name)
		    {
		       $config['file_name'] = $cat_id.'_'.$cat_name.'.jpg';
		    }

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('image')){
				//$error = array('error' => $this->upload->display_errors());
				$image = '';
				$this->load->library('messages');
			    $this->messages->add($this->upload->display_errors(), 'error');
			}else{
				$data = array('upload_data' => $this->upload->data());
				$image = "assets/cp/images/categories/".$data['upload_data']['file_name'];

				if($cat_id && $cat_name)
				{
					$update_cat_data['image'] = $image;
					$this->db->where('id',$cat_id);
					$this->db->update('categories', $update_cat_data);
				}
			}

			return $image;
		}

		function update_category(){

			$new_category_insert_data = array(
				'company_id' =>$this->company_id,
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'display_tool_tip' => $this->input->post('display_tool_tip'),
				'add_text' => $this->input->post('add_text'),
				'message' => $this->input->post('message'),
				'updated' => date('Y-m-d',time())
			);

			$rotated_image = $this->input->post('rotated_image');
			$current_prod_img = $this->input->post('current_prod_img');
			if ($rotated_image != "")
			{
				if (file_exists(dirname(__FILE__).'/../../assets/cp/images/categories/'.$current_prod_img))
				{
					unlink(dirname(__FILE__).'/../../assets/cp/images/categories/'.$current_prod_img);
					$file_cont = file_get_contents(dirname(__FILE__).'/../../assets/cp/images/product/rotated/'.$rotated_image);
					file_put_contents(dirname(__FILE__).'/../../assets/cp/images/categories/'.$current_prod_img,$file_cont);
				}
			}

			if($this->input->post('add_text')){

				$new_category_insert_data['add_text']=$this->input->post('add_text');
				$new_category_insert_data['message']= $this->input->post('message');

			}

			$image = '';
			$old_image = $this->input->post('old_image');

			/*if( $_FILES['image']['name'] )
			  $image = $this->upload_image($this->input->post('category_id'), $this->input->post('name'));*/
			if($this->input->post('image_name')){

					//$this->image = $this->input->post('image_name');
					//$image = str_replace("cropped_","",$this->input->post('image_name'));


					$prefix = 'cropped_'.$this->company_id.'_';
					$str = $this->input->post('image_name');

					if (substr($str, 0, strlen($prefix)) == $prefix) {
						$str = substr($str, strlen($prefix));
					}

					if(isset($str) && $str != ''){
						$image_name = $this->company_id.'_'.$this->input->post('category_id').'_'.$str;
						$image = 'assets/cp/images/categories/'.$image_name;//$image
						//$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));//403
						$image_file = file_get_contents(dirname(__FILE__).'/../../assets/temp_uploads/'.$this->input->post('image_name'));
						file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);
						//$this->resize_category_images($image_name);
						$this->load->helper('resize');
						resize_images('categories',$image_name,false,$this->input->post('category_id'));
					}
			}
			elseif( $old_image )
			  $image = $this->input->post('old_image');

			$new_category_insert_data['image'] = $image;

			$this->db->where('id',$this->input->post('category_id'));
			$result = $this->db->update('categories', $new_category_insert_data);
			return $result;
		}

		function get_categories($id=null,$offset=null,$num=null){
			if($id){
				$this->db->where('id',$id);
			}

			if($num){//offset could be 0
			$this->db->limit($num,$offset);
			}
			if($this->company_id){
				$this->db->where(array('company_id'=>$this->company_id));
			}
			$this->db->order_by('order_display asc');
			$query = $this->db->get('categories');
			return($query->result());

		}

		function get_category( $params = array() )
		{
		    if( !empty( $params ) )
			  foreach ( $params as $col => $val )
			     $this->db->where( $col , $val );

			$query = $this->db->get('categories');
			return($query->result());
		}

		function insert_category( $insert_arr = array() )
		{
		    if( empty($insert_arr) )
			  return false;

			$this->db->insert('categories', $insert_arr );
			return $this->db->insert_id();
		}

		function delete_category(){
			$categories_id=$this->input->post('id');//that is to be deleted//
			//we have to delete all the subcategories of the category and the products //

			$this->load->helper('resize');
			delete_rsz_imgs('categories',$categories_id);

			//deleting from the subcategories table
			$this->db->delete('subcategories',array('categories_id'=>$categories_id));

			//deleting from the products table
			$this->db->select('id,image');
			$products = $this->db->get_where('products',array('categories_id'=>$categories_id))->result();
			foreach($products as $product){
				$this->db->select('id');
				$child_p = $this->db->get_where('products',array('parent_proid'=>$product->id))->result_array();
				if(!empty($child_p)){
					foreach ($child_p as $ch_p){
						$this->delete_product($ch_p['id']);
					}
				}
				$this->db->select('parent_proid, company_id');
				$parent_p = $this->db->get_where('products',array('id'=> $product->id,'parent_proid !='=> 0))->result();
				if(!empty($parent_p)){
					$this->db->where(array('proid'=>$parent_p[0]->parent_proid,'to_comp_id'=>$parent_p[0]->company_id));
					$this->db->update('products_shared',array('status'=> '0'));
				}

				$this->delete_product($product->id);
			}

		 	//deleting from the categories table
		    $this->db->delete('categories',array('id'=>$categories_id));
	   }

	   function delete_product($product_id = null){
		   if($product_id){
			   	//$product_id=$this->input->post('id');
			   	$this->db->select('categories_id,subcategories_id,image,direct_kcp,semi_product');
			   	$this->db->where(array('id'=>$product_id));
			   	$returndata = $this->db->get('products')->result();

			   	if(($returndata[0]->direct_kcp == 0) && ($returndata[0]->semi_product == 1)){
			   		$this->db->where('id',$product_id);
			   		$this->db->update('products', array('semi_product'=>0));
			   		$semi_info = $this->db->get_where('fdd_pro_quantity',array('semi_product_id'=>$product_id))->result_array();
			   		if(!empty($semi_info)){
			   			foreach ($semi_info as $semi){
			   				$this->db->delete('products_ingredients',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
			   				$this->db->delete('products_traces',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
			   				$this->db->delete('products_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
			   				$this->db->delete('product_sub_allergence',array('product_id'=>$semi['obs_pro_id'],'kp_id'=>$semi['fdd_pro_id']));
			   			}
			   		}
			   		$this->db->delete('fdd_pro_quantity',array('semi_product_id'=>$product_id));
			   	}

			   	//----deleting product's coressponding rowa from various related tables---//
			   	$filepath = dirname(__FILE__);
			   	$this->db->delete('products_labeler',array('product_id'=>$product_id));
			   	$this->db->delete('products_pending',array('product_id'=>$product_id));
			   	$this->db->delete('groups_products',array('products_id'=>$product_id));
			   	$this->db->delete('products_discount',array('products_id'=>$product_id));
			   	$this->db->delete('groups_order',array('products_id'=>$product_id));
			   	$this->db->delete('products_ingredients',array('product_id'=>$product_id));
			   	$this->db->delete('products_traces',array('product_id'=>$product_id));
			   	$this->db->delete('products_allergence',array('product_id'=>$product_id));
			   	$this->db->delete('product_sub_allergence',array('product_id'=>$product_id));
			   	$this->db->delete('fdd_pro_quantity',array('obs_pro_id'=>$product_id));

			   	// Deleting from orders
			   	$order_detail_info = $this->db->get_where('order_details', array('products_id' => $product_id))->result();
			   	$this->db->delete('order_details',array('products_id'=>$product_id));
			   	if(!empty($order_detail_info)){
			   		foreach ($order_detail_info as $order_info){
			   			$order_detail = $this->db->get_where('order_details', array('orders_id' => $order_info->orders_id))->result();
			   			if(empty($order_detail))
			   				$this->db->delete('orders', array('id' => $order_info->orders_id));
			   		}
			   	}

			   	/*======================function to remove uploaded images================*/
			   	if($returndata[0]->image != ''){
			   		//$record_num = end(explode('/',$returndata[0]->image));
			   		//$output = unlink($filepath.'/../../assets/cp/images/product/'.$record_num);

			   		$this->load->helper('resize');
			   		delete_rsz_imgs('product',$product_id);//,$returndata[0]->image
			   	}

			   	/*=======================================================================*/
			   	$this->db->delete('products_shared',array('proid'=>$product_id));
			   	if($this->db->delete('products',array('id'=>$product_id))){
			   		return true;
			   	}
			   	return false;
			   	//return json_encode($returndata[0]);
		   }
	   }
//--function updates the order of dispaly--//

		function change_category_order(){

		    $this->db->where('id',$this->input->post('id'));
			$this->db->update('categories',array('order_display'=>$this->input->post('order')));
			return $this->db->affected_rows();

	   }
	   //--function updates the order of dispaly--//

	   function change_category_order_new(){
		   	$result=$this->input->post('category_sort');
		   	if (!empty($result))
		   	{
		   		foreach ($result as $order_display=>$cat_id)
		   		{
		   			$this->db->where('id',$cat_id);
		   			$this->db->update('categories',array('order_display'=>$order_display));
		   		}
		   	}
	   }
//--to update the service type like pick deliver or both --//
	   function change_category_service_type(){
	         $this->db->where('id',$this->input->post('id'));
			 $this->db->update('categories',array('service_type'=>$this->input->post('service_type')));
	   }


//---------------------------------------------------------------//

//--function that updates status whether it has to show or hide--//

	   function update_status(){
	   		$this->id=$this->input->post('id');
			$this->status=$this->input->post('status');
			$this->method=$this->input->post('method');
			$this->db->where('id',$this->input->post('id'));
			$this->db->update('categories',array('status'=>$this->input->post('status')));
	  }

//--------------------------------------------------------------------------------------------//

//--function that updates the tool tip when it is clicked used in the js function in header with same name--//
	  function update_tool_tip(){
	   		$this->db->where('id',$this->input->post('id'));
			$this->db->select('display_tool_tip');
			$query=$this->db->get('categories');
			$result=$query->result();
			//print_r($result);
			//die();
			if($result['0']->display_tool_tip=='1'){
				$this->db->where('id',$this->input->post('id'));
				$this->db->update('categories',array('display_tool_tip'=>'0'));
				return 'no';
			}else{
				$this->db->where('id',$this->input->post('id'));
				$this->db->update('categories',array('display_tool_tip'=>'1'));
				return 'yes';
			}

	  }
//---------------------------------------------------------------------------//

//--this function is to check that the category name already exist  or not--//
	  function check_category(){
	  	$name=$this->input->post('name');
		foreach ($this->get_categories() as $category){
				if($category->name==$name){
					return 'exist';

				}
			}
	  	return 'doesnt exist';
	  }

//-------------------------------------------------------------------------//
		function get_category_name($company_id)
		{
			//$this->db->where('id',$categories_id);
			$this->db->where('company_id',$company_id);
			$query=$this->db->get('categories')->result_array();
			return $query;
		}
		function check_category_exist($company_id = '', $id = '', $where_array = array())
		{
			if($company_id)
				$this->db->where('company_id',$company_id);
			if($id)
				$this->db->where('id',$id);
			if(!empty($where_array)){
				foreach($where_array as $col => $val){
					$this->db->where($col,$val);
				}
			}

			$query = $this->db->get('categories')->row_array();
			return $query;
		}
		function update_cat($params,$id)
		{
			//print_r($params);
			$this->db->where('id', $id);
			$this->db->update('categories', $params);
			return true;

		}
		function insert_cat( $insert_arr,$company_id_)
		{
			$general_settings = $this->db->where('company_id',$company_id_)->get('general_settings')->result();
			if($general_settings != array()){
				if($general_settings[0]->pickup_service == '1' && $general_settings[0]->delivery_service == '1'){
					$insert_arr['service_type'] = "0";
				}else if($general_settings[0]->pickup_service == '1' && $general_settings[0]->delivery_service != '1'){
					$insert_arr['service_type'] = "1";
				}else if($general_settings[0]->pickup_service != '1' && $general_settings[0]->delivery_service == '1'){
					$insert_arr['service_type'] = "2";
				}
			}
			$this->db->insert('categories', $insert_arr );
			return true;
		}
		function get_cat_name($cat_id)
		{
			$this->db->where('id',$cat_id);
			$query=$this->db->get('categories')->row_array();
			return $query;
		}
		function get_cat_id($catname,$company_id_)
		{
			$this->db->where('name',$catname);
			$this->db->where('company_id',$company_id_);
			$query=$this->db->get('categories')->row_array();
			return $query;
		}

		function get_categories_addedit($id=null,$offset=null,$num=null){
			if($id){
				$this->db->where('id',$id);
			}

			if($num){//offset could be 0
			$this->db->limit($num,$offset);
			}
			if($this->company_id){
				$this->db->where(array('company_id'=>$this->company_id));
			}
			$this->db->select('id, name');
			$this->db->order_by('order_display asc');
			$query = $this->db->get('categories');
			return($query->result());

		}
	}
?>