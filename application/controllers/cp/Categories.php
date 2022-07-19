<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Categories extends CI_Controller
{
	
	var $rows_per_page = '';
	var $company_id = '';
	var $ibsoft_active = false;
	
	function __construct(){
	
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->helper('phpmailer');
		$this->load->helper('cookie');
		
		$this->load->library('ftp');
		$this->load->library('Messages');
		$this->load->library('session');
        $this->load->library('messages'); 
		$this->load->library('utilities');
		$this->load->library('pagination');	
		$this->load->library('form_validation');
		//$this->load->library('upload');
		
		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mpackages');
		$this->load->model('Mcategories');
		$this->load->model('Mcalender');
		$this->load->model('Mclients');
		$this->load->model('mcp/Mcompanies');
		$this->load->model('M_fooddesk');
		
		if($this->input->get('token')){
			$privateKey = 'o!s$_7e(g5xx_p(thsj*$y&27qxgfl(ifzn!a63_25*-s)*sv8';
			$userId = $this->input->get('name');
			$hash = $this->createToken($privateKey, current_url(), $userId);
			
			if($this->input->get('token') == $hash){
				$this->product_validate($userId);
			}
		}
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}
		
		$this->company_id = $this->session->userdata('cp_user_id');
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
			$this->company = $company[0];
		
		/*if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/_categories/page_not_found');
		}*/
		$this->load->model('MFtp_settings');
		$this->rows_per_page = 20;
		
		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}
	
	/*===============function to show categories================*/
		
 	function index(){
 		$this->ccat();
// 		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
// 			redirect(base_url().'cp/cdashboard/page_not_found');
// 		}
		
// 	  	if( $this->company_role == 'master' || $this->company_role == 'super' ){
// 	  		if($this->input->post('btn_update')){
// 	  			if($this->input->post('act')=="update_categories"){
// 	  				$result = $this->Mcategories->change_category_order_new();
// 	  				$this->session->set_userdata('action', 'category_json');
// 	  			}
// 	  		}
	
// 			$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
			
// 			$pconfig['base_url'] = base_url()."cp/categories";		
// 			$pconfig['total_rows'] = $data['total_cat'] = count($this->Mcategories->get_categories());
// 			$pconfig['per_page'] = $this->rows_per_page; 
// 			$pconfig['uri_segment'] = 4;
			
// 			$this->pagination->initialize($pconfig); 
			
// 			$data['categories'] = $this->Mcategories->get_categories('',$param,$pconfig['per_page']);
// 			$data['content'] = 'cp/categories';
// 			$data['create_links'] = $this->pagination->create_links();
			
// 			$this->load->view('cp/cp_view',$data);
// 	  	}
// 	  	else{
// 	       // restricted
// 		   $data['content'] = 'cp/restricted';
// 		   $this->load->view('cp/cp_view',$data);
// 	  	}
 	}
	
	function ccat($param=NULL){

		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}
		
		if( $this->company_role == 'master' || $this->company_role == 'super' ){
			if($this->input->post('btn_update')){
				if($this->input->post('act')=="update_categories"){
					$result = $this->Mcategories->change_category_order_new();
					$this->session->set_userdata('action', 'category_json');
				}
			}
				
			$pconfig['base_url'] = base_url()."cp/categories/ccat";
			$pconfig['total_rows'] = $data['total_cat'] = count($this->Mcategories->get_categories());
			$pconfig['per_page'] = $this->rows_per_page;
			$pconfig['uri_segment'] = 4;
				
			$this->pagination->initialize($pconfig);
				
			//$data['categories'] = $this->Mcategories->get_categories('',$param,$pconfig['per_page']);
			$data['categories'] = $this->Mcategories->get_categories('',$param);
			
			$data['content'] = 'cp/categories';
			$data['create_links'] = $this->pagination->create_links();
				
			$this->load->view('cp/cp_view',$data);
		}
		else{
			// restricted
			$data['content'] = 'cp/restricted';
			$this->load->view('cp/cp_view',$data);
		}
	}
	
	/*==========================================================*/
		
	
	/*============function to  show category edit form============*/
	function categories_addedit($action=NULL,$id=NULL){
		
		$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed();//for calender
		
		if($this->input->post('name') && ($this->input->post('btn_update'))){				
			$this->company_id = $this->session->userdata('company');
			$ac_type_id 	= $this->company_id[0]->ac_type_id;
			if($query = $this->Mcategories->create_category( $ac_type_id )){
			 
				$this->messages->add(_('New Category added successfully.'), 'success');
				
				$this->session->set_userdata('action', 'category_json');
				
				redirect('cp/categories');
			}
			else
			{
			    $this->messages->add(_('Sorry ! Some error occured.'), 'error');
				
				redirect('cp/categories');
			}
		}
		
		if($this->input->post('update')){
			$result=$this->Mcategories->update_category();
			
			$this->messages->add(_('Updated: Category Updated Successfully.'), 'success');
			
			$this->session->set_userdata('action', 'category_json');
			
			redirect('cp/categories');
		}
		
		if($action=="update" && $id!=''){
			
			$category_id = $id;

			$data['category_data']=$this->Mcategories->get_categories($category_id);
			$data['content']='cp/categories_addedit';
			
			$this->load->view('cp/cp_view',$data);
		}
		else
		{
			$data['category_data']=null;//for the avoid of confliction with updation//	
			$data['content'] = 'cp/categories_addedit';
			
			$this->load->view('cp/cp_view',$data);
		}
	} 
	
	//-------these functions is to check whether the newly added thing alrealy exist or not---------//
	function check_category(){
	
		$result=$this->Mcategories->check_category();
		echo $result;
	
	}
	
	function change_category_order(){

		$affected_rows=$this->Mcategories->change_category_order();
		if($affected_rows=='1'){
			echo 'Status successfully updated';
		}else{
			echo "Error occurred while updating status";
		}
	}	

	function change_category_service_type(){
		$this->Mcategories->change_category_service_type();
		echo 'successfully updated';
	}	

	function delete_category(){
		$this->Mcategories->delete_category();
		$this->session->set_userdata('action', 'category_json');
		echo 'successfully deleted';
	}
	
	function update_status(){
		if($this->input->post('method')=='category'){
			$this->Mcategories->update_status();
			echo 'OK';
		}else if($this->input->post('method')=='subcategory'){
			$this->load->model('Msubcategories');
			$this->Msubcategories->update_status();
			echo 'OK';
		}
	}

	function update_tool_tip(){
		if($this->input->post('type')=="categories"){
			$display=$this->Mcategories->update_tool_tip();
			if($display=='yes'){
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'1'));
			}else{
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'0'));
			}
		}else if($this->input->post('type')=="subcategories"){
			$this->load->model('Msubcategories');
			$display=$this->Msubcategories->update_tool_tip();
			if($display=='yes'){
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'1'));
			}else{
			echo json_encode(array('RESULT'=>true,'display_tool_tip'=>'0'));
			}
		}		
	}

	function check_subcategory(){
		
		$this->load->model('Msubcategories');
		$result=$this->Msubcategories->check_subcategory();
		echo $result;
	}
	
	function delete_cat_image(){
		$category_id = $this->input->post('category_id');
		$get_product_image = $this->db->get_where('categories',array('id'=>$category_id))->result_array();
		$tmpp = explode('/',$get_product_image['0']['image']);
		$record_num = end($tmpp);
		$filename	= dirname(__FILE__).'/../../../assets/cp/images/categories/'.$record_num;
		$filename2	= dirname(__FILE__).'/../../../assets/cp/images/categories_100_100/'.$record_num;
		if (file_exists($filename)) {
			$output = unlink($filename);
			if($output){
				(file_exists($filename2))?unlink($filename2):null;
				
				$this->db->where('id',$category_id);
				$this->db->update('categories',array('image'=>''));
				echo "success";die;
			}
		}
		echo "error";
	}
	
	function rotate_uploaded_image(){
	
		$img = $this->input->post('src');
		
		if (file_exists(dirname(__FILE__).'/../../../assets/cp/images/categories/'.$img))
		{
			$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/cp/images/categories/'.$img);
			file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img,$file_cont);
		}
		//move_uploaded_file ( dirname(__FILE__).'/../../../assets/cp/images/product/'.$img , dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img );
		$angle= $this->input->post('angle');
	
		$angle=($angle=='acw')?'90':'270';
		 
		$this->load->library('image_lib');
		$config['image_library'] = 'gd2';
		 
		$config['source_image'] = dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img;
		$config['rotation_angle'] = $angle;
		$rot_img = '0'.$img;
		$config['new_image'] = dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$rot_img;
	
		$this->image_lib->initialize($config);
	
		if ( ! $this->image_lib->rotate())
		{
			echo $this->image_lib->display_errors();
			exit;
		}
		else{
			$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$rot_img);
			file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img,$file_cont);
			echo $rot_img;
		}
	}
	// function assign_category(){
	// 	$this->load->model('Mproducts');
	// 	$this->load->model('Msubcategories');
	// 	if($this->input->post('checkbox')){
	// 		if($this->input->post('cat-assign-button-up')){
	// 			$cat_id = $this->input->post('cat-assign-up');
	// 			$subcat_id = $this->input->post('subcat-assign-up');
	// 		}
	// 		elseif($this->input->post('cat-assign-button-down')){
	// 			$cat_id = $this->input->post('cat-assign-down');
	// 			$subcat_id = $this->input->post('subcat-assign-down');
	// 		}
	// 		$proid_arr = $this->input->post('checkbox');
	// 		if($cat_id != -1){
	// 			foreach($proid_arr as $pro_id){
	// 				$result = $this->Mproducts->update_product_details($pro_id,array('categories_id'=>$cat_id,'subcategories_id'=>$subcat_id));
	// 			}
	// 			if($result){
	// 				$this->session->set_flashdata('cat_update',_('Category and Subcategory Updated Successfully'));
	// 			}
	// 		}
	// 		$this->session->set_userdata('action', 'category_json');
			
	// 		if(count($this->uri->segments) == 5){
	// 			redirect('cp/categories/assign_category/category_id/'.end($this->uri->segments));
	// 		}
	// 		else{
	// 			redirect('cp/categories/assign_category/');
	// 		}
	// 	}
	// 	else {
	// 		$url_variable = $this->uri->uri_to_assoc(4);
			
	// 		$data['category_data'] = $this->Mcategories->get_categories();
	// 		if(array_key_exists('category_id', $url_variable)){
	// 			$data['cat_id']=$url_variable['category_id'];
	// 			$this->load->model('Msubcategories');
	// 			$data['subcategory_data']=$this->Msubcategories->get_sub_category($url_variable['category_id']);
				
	// 			$products2 = array();
				
	// 			$data['sub_cat_id']='-1';
	// 			$products1 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],'-1');//getting product of related to category
				
	// 			$this->db->select('id');
	// 			$subcategory_data = $this->db->get_where('subcategories',array('categories_id'=>$url_variable['category_id']))->result_array();
				
	// 			$id = array();
	// 			foreach($subcategory_data as $var) {
	// 				$id[] = $var['id']; 
	// 			}
	// 			if(!empty($id)){
	// 				$products2 = $this->Mproducts->get_products_to_assign($url_variable['category_id'],$id);//getting product of related to category
	// 			}
	// 			$data['products'] = array_merge($products1,$products2);
			
	// 		} else {
	// 			$data['products'] = $this->Mproducts->get_products_to_assign();
	// 		}
	//  			$data['category_data']=$this->Mcategories->get_categories();
	 			
	//  			$this->db->select(array( 'subcategories.id','subcategories.subname','subcategories.categories_id'));
	//  			$this->db->join('categories','subcategories.categories_id=categories.id');
	//  			$this->db->where('categories.company_id',$this->company_id);
	//  			$data['subcategory_data'] = $this->db->get('subcategories')->result_array();
				
	//  			$data['content'] = 'cp/assign_cat_subcat';
	// 			$this->load->view('cp/cp_view', $data); 
	// 		}
	// }


	// function get_sub_category()
	// {
	// 	$this->load->model('Msubcategories');
	// 	$subcategories=$this->Msubcategories->get_sub_category($this->input->post('cat_id'));
	// 	echo json_encode($subcategories);
	// }
}
