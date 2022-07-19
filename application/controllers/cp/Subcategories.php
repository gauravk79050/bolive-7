<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class subcategories extends CI_Controller{

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
			redirect(base_url().'cp/cdashboard/page_not_found');
		}*/
		$this->load->model('MFtp_settings');
		$this->rows_per_page = 20;

		$this->tempUrl = base_url().'application/views/cp';
		$this->template = "/cp";
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
	}

	function index()
	{
		$this->lijst();
	}
	function lijst($category = NULL, $category_id=NULL, $param = NULL)
	{
		if($this->company->ac_type_id == 1 && $this->router->fetch_method() != 'page_not_found'){
			redirect(base_url().'cp/cdashboard/page_not_found');
		}

	    if( $this->company_role == 'master' || $this->company_role == 'super' )
		{
			$this->load->model('Msubcategories');

			$data['category_data'] = $this->Mcategories->get_categories();
			$data['subcategory_data'] = NULL;
			$data['cat_id']='';

			if($this->input->post('update')){
				$subcat_ids = $this->input->post('sub_cat_ids');
				if(is_array($subcat_ids) && !empty($subcat_ids)){
					foreach ($subcat_ids as $key => $subcat_id)
						$this->Msubcategories->update_sub_cat(array('suborder_display' => ($key+1)), $subcat_id);

					$this->session->set_userdata('action', 'category_json');
				}
			}

			if( $category == "category_id" )
			{
				$data['cat_id'] = $category_id;
				/*$pconfig['base_url'] = base_url()."cp/cd/subcategories/category_id/".$category_id;

				$pconfig['total_rows'] = $data['total_sub_cat'] = count($this->Msubcategories->get_sub_category($category_id));
				$pconfig['per_page'] = $this->rows_per_page;
				$pconfig['uri_segment'] = 6;
				//echo $data['total_sub_cat'];
				$this->pagination->initialize($pconfig);*/
				$data['subcategory_data'] = $this->Msubcategories->get_sub_category($category_id,'',$param);
				//$data['links'] = $this->pagination->create_links();
				$data['links'] = NULL;
				$data['content'] = 'cp/subcategories';
				$this->load->view('cp/cp_view',$data);
			}
			else
			{
				$data['links'] = NULL;
				$data['content'] = 'cp/subcategories';
				$this->load->view('cp/cp_view',$data);
			}
		}
		else
		{
		   // restricted
		   $data['content'] = 'cp/restricted';
		   $this->load->view('cp/cp_view',$data);
		}
	}

	function subcategories_addedit( $action = NULL , $id = NULL)
	{
		$this->load->model('Msubcategories');

		if( $this->input->post('submit') == 'ADD' || $this->input->post('submit') == 'TOEVOEGEN' || $this->input->post('submit') == 'AJOUTER'   )
		{
			$this->company_id = $this->session->userdata('company');
			$ac_type_id 	= $this->company_id[0]->ac_type_id;
			$returndata=$this->Msubcategories->add_subcategory( $ac_type_id  );
			$this->messages->add(_('New Subcategory added successfully.'), 'success');

			$this->session->set_userdata('action', 'category_json');

			redirect('cp/subcategories/lijst/category_id/'.$returndata->categories_id);
		}

		if( $this->input->post('submit') == 'UPDATE' )
		{
			$returndata=$this->Msubcategories->update_subcategory();
			$this->messages->add(_('Subcategory updated successfully.'), 'success');

			$this->session->set_userdata('action', 'category_json');

			redirect('cp/subcategories/lijst/category_id/'.$returndata->categories_id);
		}

		if( $action == 'add' ){

			$data['category_data']=$this->Mcategories->get_categories();
			$data['subcategory_data'] = NULL;
			$data['content'] = 'cp/subcategories_addedit';

			$this->load->view('cp/cp_view',$data);
		}

		if( $action == 'update' && $id != '' )
		{
			$subcat_id = $id;
			$data['subcat_id']=$subcat_id;
			$data['category_data']=$this->Mcategories->get_categories();
			$data['subcategory_data']=$this->Msubcategories->get_sub_category('',$subcat_id);
			$data['content']='cp/subcategories_addedit';

			$this->load->view('cp/cp_view',$data);
		}
	}

	function delete_subcategory()
	{
		$this->load->model('Msubcategories');
		$returndata=$this->Msubcategories->delete_subcategory();

		$this->session->set_userdata('action', 'category_json');
		echo $returndata;
	}

	function change_subcategory_order()
	{
		$this->load->model('Msubcategories');
		$affected_rows=$this->Msubcategories->change_subcategory_order();
		if($affected_rows){
			echo 'successfully updated';
		}else{
			echo 'error in  updation';
		}

	}
	function check_subcategory(){

		$this->load->model('Msubcategories');
		$result=$this->Msubcategories->check_subcategory();
		echo $result;
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

	function rotate_uploaded_image(){

		$img = $this->input->post('src');

		if (file_exists(dirname(__FILE__).'/../../../assets/cp/images/subcategories/'.$img))
		{
			$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/cp/images/subcategories/'.$img);
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

}