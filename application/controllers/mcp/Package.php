<?php
class Package extends CI_Controller
{		
	var $template='';
	function __construct()
	{
		parent::__construct();
		$this->template="/mcp";
		$this->load->helper('url');
		$this->load->library('image_lib');
		$this->load->helper('html');  
		$this->tempUrl = base_url().'application/views/mcp';
		$this->load->model($this->template.'/Mpackage');
		
		$this->temp=base_url().'mcp/package';
		
		//--for the session authentication--//
		/*$this->load->model($this->template.'/Mindex');
		if($this->session->userdata('username')||$this->session->userdata('is_logged_in'))
		{
			$current_session_id=$this->session->userdata('session_id');//--getting current session id--//
		    $current_user=$this->session->userdata('username');//--getting current session user--//
			if(!$this->Mindex->session_validate($current_session_id,$current_user))
			{
				redirect('mcp/mcplogin','refresh');
			}
		}
		else
		{
			redirect('mcp/mcplogin','refresh');
		}*/
		
		  $current_user = $this->session->userdata('username');
		  $is_logged_in = $this->session->userdata('is_logged_in');
			
		  if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
			
	}
	
	
	/*DEFAULT FUNCTION*/
	function index()
	{
	    
		$data['content']=$this->Mpackage->select();
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/package';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
	}
	
	
	/* Display the Add package Form */
	function add_package()
	{
		$this->load->library('form_validation');
	    $data['header']=$this->template.'/header';
		$data['main']=$this->template.'/add_package';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
	}
	
	
	/* Insert the data in package  */
	function insert()
	{
		$this->load->library('form_validation');	
		if($this->input->post())
		{
			$this->form_validation->set_rules('package_name', 'package_name', 'required');	
			$this->form_validation->set_rules('package_desc', 'package_desc', 'required');	
			$this->form_validation->set_rules('package_price', 'package_price', 'required|numeric');
				 
		    if($this->form_validation->run()==FALSE)
			{
				$data['content']=$this->Mpackage->select();
				$data['header']=$this->template.'/header';
				$data['main']=$this->template.'/add_package';
				$data['footer']=$this->template.'/footer';
				$this->load->vars($data);
				$this->load->view($this->template.'/mcp_view');
				
			}else{
			
				$dummy['package_name'] = $this->input->post('package_name');
				$dummy['package_desc'] = $this->input->post('package_desc');
				$dummy['package_price'] = $this->input->post('package_price');
				$data['tempUrl'] = $this->tempUrl;
				
				$result=$this->Mpackage->insert($dummy);
				
				if($result==1)
				{
					redirect($this->temp.'/index/insert','refresh');
			    }
			}
		}
	}
	
	
	/* Update the data of package */	
	function update()
	{
		 $this->load->library('form_validation');
		 if ($this->input->post('update'))
		 {
		 	$this->form_validation->set_rules('package_name', 'package_name', 'required');	
			$this->form_validation->set_rules('package_desc', 'package_desc', 'required');	
			$this->form_validation->set_rules('package_price', 'package_price', 'required|numeric');
				 
			if($this->form_validation->run()==FALSE)
			{
				$data['content']=$this->Mpackage->select();
				$data['header']=$this->template.'/header';
				$data['main']=$this->template.'/update_package';
				$data['footer']=$this->template.'/footer';
				$this->load->vars($data);
				$this->load->view($this->template.'/mcp_view');
				
			}else{
			
				$dummy['id'] =$this->input->post('id');
				$dummy['package_name'] = $this->input->post('package_name');
				$dummy['package_desc'] = $this->input->post('package_desc');
				$dummy['package_price'] = $this->input->post('package_price');
				
				$result=$this->Mpackage->update($dummy);
				
				if($result==1)
				{
					 redirect($this->temp.'/index/update','refresh');
				}
		    }//end of else
	    
		}
		elseif($this->input->post('delete'))
		{
		
			$this->Mpackage->delete($this->input->post('id'));
			redirect($this->temp.'/index/delete','refresh');
		
		}else{
		
				$data['main']=$this->template.'/update_package';
				$this->id=$this->uri->segment(4);
				$data['content']=$this->Mpackage->select($this->id);
				$data['header']=$this->template.'/header';
				$data['footer']=$this->template.'/footer';
				$this->load->vars($data);
				$this->load->view($this->template.'/mcp_view');
		}
		
	}//end of function
}
?>