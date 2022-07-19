<?php
class Msubcategories extends CI_Model{
	
	
	
	function __construct(){
		parent::__construct();
		
	}
	function get_sub_category($cat_id=null,$subcat_id=null,$offset=0,$limit=0){
		if($cat_id && $subcat_id){
			$this->db->where('categories_id',$cat_id);
			$this->db->where('id',$subcat_id);
		}else if($cat_id&&(!$subcat_id)){
			$this->db->where('categories_id',$cat_id);
		}else if((!$cat_id)&&$subcat_id){
			$this->db->where('id',$subcat_id);
		}
		if($limit){
		//echo $offset.'  '.$limit;
			$this->db->limit($limit,$offset);
		}
		$this->db->order_by('suborder_display','ASC');
		$query=$this->db->get('subcategories')->result();
		
		$sorted_p = array();
		$unsorted_p = array();
		foreach($query as $prod) {
			if($prod->suborder_display != 0){
				$sorted_p[] = $prod;
			}else{
				$unsorted_p[] = $prod;
			}
		
		}
		$query = array_merge($sorted_p,$unsorted_p);
		
		//echo $this->db->last_query();
		return($query);
	}
	
	
	function delete_subcategory(){
		$subcategories_id=$this->input->post('id');
		if($subcategories_id){
			$this->db->where(array('subcategories_id'=>$subcategories_id));
			$this->db->update('products',array("subcategories_id"=>'-1'));
		}
		//get category of  this category to show in the page after deletion//
		$this->db->select('categories_id');
		$returndata=$this->db->get_where('subcategories',array('id'=>$subcategories_id))->result();
		//-----------------------------------------------------------------//
		
		$this->load->helper('resize');
		delete_rsz_imgs('subcategories',$subcategories_id);
		
		$this->db->delete('subcategories',array('id'=>$subcategories_id));
		return json_encode($returndata[0]);//returns category id of deleted subcategory
		
	}
	
	function change_subcategory_order(){
		$suborder_display=$this->input->post('order');
		$subcategories_id=$this->input->post('id');
		$this->db->where(array('id'=>$subcategories_id));
		$this->db->update('subcategories',array('suborder_display'=>$suborder_display));
		return $this->db->affected_rows();
	}
	
	function update_tool_tip(){
		$subcategories_id=$this->input->post('id');
		$this->db->where('id',$subcategories_id);
		$this->db->select('display_tool_tip');
		$query=$this->db->get('subcategories');
		$result=$query->result();
		if($result['0']->display_tool_tip=='1'){
			$this->db->where('id',$subcategories_id);
			$this->db->update('subcategories',array('display_tool_tip'=>'0'));
			return 'no';
		}else{
			$this->db->where('id',$subcategories_id);
			$this->db->update('subcategories',array('display_tool_tip'=>'1'));
			return 'yes';
		}
	}
	
	function update_status(){
		$subcategories_id=$this->input->post('id');
		$status=$this->input->post('status');
		$this->db->where(array('id'=>$subcategories_id));
		$this->db->update('subcategories',array('status'=>$status));
	}
	
	function add_subcategory( $ac_type_id =0 )
	{
		$new_subcategory=array('id'=>'',
							'categories_id'=>$this->input->post('categories_id'),
							'subname'=>$this->input->post('subname'),
							'subdescription'=>$this->input->post('subdescription'),
							'suborder_display'=>'0',
							'subcreated'=>mdate('%Y-%m-%d', time()),
							'display_tool_tip'=>$this->input->post('display_tool_tip'));
		
		if( isset( $ac_type_id ) && $ac_type_id == '7' ){
			$new_subcategory[ 'status' ] = '1';
		}
							
		if($this->input->post('subaddtext')){
		
			$new_subcategory['subaddtext']=$this->input->post('subaddtext');
			$new_subcategory['submessage']=$this->input->post('submessage');
			
		}else{
		
			$new_subcategory['submessage']="";
		}
		
		$new_subcategory['subimage'] =  ''; //$this->upload_image();
		
		$this->db->insert('subcategories',$new_subcategory);
		$new_subcategory_id = $this->db->insert_id();
		
		/*if( $_FILES['subimage']['name'] != '' )
		{		   
		   $this->upload_image($new_subcategory_id,$this->input->post('subname'));
		}*/
		/*if($this->input->post('image_name') && $this->input->post('image_url') && $this->input->post('image_size') <= 150 && ($this->input->post('image_type') == 'jpg' || $this->input->post('image_type') == 'jpeg' || $this->input->post('image_type') == 'png' || $this->input->post('image_type') == 'gif')){
			$image = 'assets/cp/images/subcategories/'.$new_subcategory_id.'_'.$this->input->post('subname').'.'.$this->input->post('image_type');
			$image_file = file_get_contents($this->input->post('image_url'));
			file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);
			$update_subcat_data['subimage'] = $image;
			$this->db->where('id',$new_subcategory_id);
			$this->db->update('subcategories', $update_subcat_data);
		}*/
		if($this->input->post('image_name')){
		
			//$this->image = $this->input->post('image_name');
			//$image = str_replace("cropped_","",$this->input->post('image_name'));
			
			//$temp_img_name = str_replace("cropped_","",$this->input->post('image_name'));
			//$temp_arr	= explode($this->company_id.'_', $temp_img_name);
			//$image_name = (!empty($temp_arr) && isset($temp_arr[1]))?($this->company_id.'_'.$this->input->post('categories_id').'_'.$new_subcategory_id.'_'.$temp_arr[1]):$temp_img_name;
			
			$prefix = 'cropped_'.$this->company_id.'_';
			$str = $this->input->post('image_name');
			
			if (substr($str, 0, strlen($prefix)) == $prefix) {
				$str = substr($str, strlen($prefix));
			}
				
			if(isset($str)){
				$image_name = $this->company_id.'_'.$this->input->post('categories_id').'_'.$new_subcategory_id.'_'.$str;
				$image = 'assets/cp/images/subcategories/'.$image_name;//$image
				//$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));//403
				$image_file = file_get_contents(dirname(__FILE__).'/../../assets/temp_uploads/'.$this->input->post('image_name'));
				file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);
					
				//$this->resize_subcategory_images($image_name);
				$this->load->helper('resize');
				resize_images('subcategories',$image_name);
				
				$update_subcat_data['subimage'] = $image;
			}
			$this->db->where('id',$new_subcategory_id);
			$this->db->update('subcategories', $update_subcat_data);
		}
		
		$returndata = $this->db->get_where('subcategories',array('id'=>$new_subcategory_id))->result();
		return $returndata[0];	
	}
	
	function update_subcategory(){
		if($this->input->post('subcat_id')){
		$update_subcategory=array('categories_id'=>$this->input->post('categories_id'),
							'subname'=>$this->input->post('subname'),
							'subdescription'=>$this->input->post('subdescription'),
							'subupdated'=>mdate('%Y-%m-%d', time()),
							'display_tool_tip'=>$this->input->post('display_tool_tip'));
		}if($this->input->post('subaddtext')){
		
			$update_subcategory['subaddtext']=$this->input->post('subaddtext');
			$update_subcategory['submessage']=$this->input->post('submessage');
		}else{
			
			$update_subcategory['submessage']=NULL;
		
		}
		
		$rotated_image = $this->input->post('rotated_image');
		$current_prod_img = $this->input->post('current_prod_img');
		if ($rotated_image != "")
		{
			if (file_exists(dirname(__FILE__).'/../../assets/cp/images/subcategories/'.$current_prod_img))
			{
				unlink(dirname(__FILE__).'/../../assets/cp/images/subcategories/'.$current_prod_img);
				$file_cont = file_get_contents(dirname(__FILE__).'/../../assets/cp/images/product/rotated/'.$rotated_image);
				file_put_contents(dirname(__FILE__).'/../../assets/cp/images/subcategories/'.$current_prod_img,$file_cont);
			}
		}
		
		$image = '';
        $old_subimage = $this->input->post('old_subimage');
		
		/*if( $_FILES['subimage']['name'] )
		  $image = $this->upload_image($this->input->post('subcat_id'),$this->input->post('subname'));*/
        /*if($this->input->post('image_name') && $this->input->post('image_url') && $this->input->post('image_size') <= 150 && ($this->input->post('image_type') == 'jpg' || $this->input->post('image_type') == 'jpeg' || $this->input->post('image_type') == 'png' || $this->input->post('image_type') == 'gif')){
        	$image = 'assets/cp/images/subcategories/'.$this->input->post('subcat_id').'_'.$this->input->post('subname').'.'.$this->input->post('image_type');
        	$image_file = file_get_contents($this->input->post('image_url'));
        	file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);
        }*/
        if($this->input->post('image_name')){
        
        	//$this->image = $this->input->post('image_name');
        	//$image = str_replace("cropped_","",$this->input->post('image_name'));
        	
        	//$temp_img_name = str_replace("cropped_","",$this->input->post('image_name'));
        	//$temp_arr	= explode($this->company_id.'_', $temp_img_name);
        	//$image_name = (!empty($temp_arr) && isset($temp_arr[1]))?($this->company_id.'_'.$this->input->post('categories_id').'_'.$this->input->post('subcat_id').'_'.$temp_arr[1]):$temp_img_name;
        	
        	$prefix = 'cropped_'.$this->company_id.'_';
        	$str = $this->input->post('image_name');
        		
        	if (substr($str, 0, strlen($prefix)) == $prefix) {
        		$str = substr($str, strlen($prefix));
        	}
        	
        	if(isset($str)){
        		$image_name = $this->company_id.'_'.$this->input->post('categories_id').'_'.$this->input->post('subcat_id').'_'.$str;
				$image = 'assets/cp/images/subcategories/'.$image_name;//$image
	        	//$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('image_name'));//403
				$image_file = file_get_contents(dirname(__FILE__).'/../../assets/temp_uploads/'.$this->input->post('image_name'));
	        	file_put_contents(dirname(__FILE__).'/../../'.$image, $image_file);
	        	
	        	//$this->resize_subcategory_images($image_name);
	        	$this->load->helper('resize');
	        	resize_images('subcategories',$image_name,false,$this->input->post('subcat_id'));
        	}
        }
        elseif($old_subimage)
		  $image = $old_subimage;
		
		$update_subcategory['subimage'] = $image;		
		
 		$this->db->where(array('id'=>$this->input->post('subcat_id')));
		$this->db->update('subcategories',$update_subcategory);		
		$returndata=$this->db->get_where('subcategories',array('id'=>$this->input->post('subcat_id')))->result();
		//print_r($returndata->result());
		return $returndata[0];					
	}
	
	function upload_image( $sub_cat_id = NULL, $sub_cat_name = NULL ){
			
		$image = '';
		$config['upload_path']='./assets/cp/images/subcategories/';
			$config['allowed_types'] = 'gif|jpg|jpeg|JPG|GIF|png';
			$config['max_size']	= '150';
			$config['max_width']  = '300';
			$config['max_height']  = '300';
		$config['remove_spaces']  = true;
		
		if($sub_cat_id && $sub_cat_name)
		{
		   $config['file_name'] = $sub_cat_id.'_'.$sub_cat_name.'.jpg';
		}
			
		$this->load->library('upload', $config);
		
		if(!$this->upload->do_upload('subimage')){
			//$error = array('error' => $this->upload->display_errors());
			$image = '';
			$this->load->library('messages');
			$this->messages->add($this->upload->display_errors(),'error');
			
		}else{
			
			$data = array('upload_data' => $this->upload->data());
			$image = "assets/cp/images/subcategories/".$data['upload_data']['file_name'];
			
			if($sub_cat_id && $sub_cat_name)
			{
				$update_subcat_data['subimage'] = $image;
				$this->db->where('id',$sub_cat_id);
				$this->db->update('subcategories', $update_subcat_data);
			}
		}
		
		return $image;
	}
	
	function check_subcategory(){
	
		if($this->input->post('categories_id')&&$this->input->post('subname')){
			
			$categories_id=$this->input->post('categories_id');
			$subname=$this->input->post('subname');
			$subcategories=$this->get_sub_category($categories_id);
			if($subcategories){//if subcategories exist in dat category
			
				foreach($subcategories as $subcategory){
					if($subcategory->subname==$subname){
					
						return "exist";
					}
				
				}
			}else{
				return "no sub categroy exist";
			}
		
		}
	return "not exist";
	}
	
	function get_subcategory( $params = array() )
	{
		if( !empty( $params ) )
		  foreach ( $params as $col => $val )
			 $this->db->where( $col , $val );
		
		$query = $this->db->get('subcategories');
		return($query->result());
	}
	
	function insert_subcategory( $insert_arr = array() )
	{
		if( empty($insert_arr) )
		  return false;
		  
		$this->db->insert('subcategories', $insert_arr );
		return $this->db->insert_id();
	}
	function get_subcategory_name($category_id)
	{
		
	//	$this->db->where('id',$subcategory_id);
		if( !empty( $category_id ) )
			$this->db->where_in('categories_id',$category_id);
		$query=$this->db->get('subcategories')->result_array();
	//	print_r($query); die();
		return $query;
	}
	function insert_sub_cat($insert_arr)
	{
		//print_r($insert_arr); die();
		$this->db->insert('subcategories',$insert_arr);
		return true;
	}
	function check_sub_category_exist($categories_id_ = null, $id = null ,$cat_name = null)
	{
		$this->db->where('categories_id',$categories_id_);
		if($id != null)
			$this->db->where('id',$id);
		if($cat_name != null)
			$this->db->where('subname',$cat_name );
		$query=$this->db->get('subcategories')->row_array();
		return $query;
	}
	function update_sub_cat($params,$id)
	{
		$this->db->where('id', $id);
		$this->db->update('subcategories', $params);
	    return true;
	}
	function get_sub_cat_name($subcat_id)
	{
		$this->db->where('id',$subcat_id);
		$query=$this->db->get('subcategories')->row_array();
		return $query;
	}
	function get_sub_cat_id($subcatname,$categories_id)
	{
		$this->db->where('categories_id',$categories_id);
		$this->db->where('subname',$subcatname);
		$query=$this->db->get('subcategories')->row_array();
		return $query;
	}
}
?>