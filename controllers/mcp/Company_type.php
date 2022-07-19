<?php
	class Company_type extends CI_Controller
	{
	   var $template='mcp/';
	   
	   function __construct()
	   {
		  parent::__construct();
		  $this->load->helper('url');
		  $this->load->helper('form');
		  $this->load->model($this->template.'Mcompany_type');	
		  
		  //--for the session authentication--//
		  /*$this->load->model($this->template.'Mindex');
		  if($this->session->userdata('username')||$this->session->userdata('is_logged_in'))
		  {
			   $current_session_id=$this->session->userdata('session_id');//--getting current session id--//
			   $current_user=$this->session->userdata('username');//--getting current session user--//
			  if(!$this->Mindex->session_validate($current_session_id,$current_user))
				redirect('mcp/mcplogin','refresh');
		  }
		  else
			 redirect('mcp/mcplogin','refresh');*/
			 
		  $current_user = $this->session->userdata('username');
		  $is_logged_in = $this->session->userdata('is_logged_in');
			
		  if( !$current_user || !$is_logged_in )
			redirect('mcp/mcplogin','refresh');
	 
		}
	 
	    //--default function--//
	    function index()
	    {
			if($this->input->post('search'))
			{
		  	   $this->search=$this->input->post();
			   $data['company_type']=$this->Mcompany_type->select($this->search);
			}
		    else
			{
			   $data['company_type']=$this->Mcompany_type->select();
			}
			
		   $data['header']= $this->template.'header';
		   $data['main']= $this->template.'company_type';
		   $data['footer']= $this->template.'footer';
		   $this->load->vars($data);
		   $this->load->view($this->template.'mcp_view');	
	   }
	 
       
	   //--function to add languages--//
	   function company_type_add()
	   {
	
	      if($this->input->post('add'))
	      {
	         $arr=$this->input->post();
	         $this->Mcompany_type->insert($arr);
	         redirect(base_url().'mcp/company_type');
	      }
	      else
	      {
			 $data['header']= $this->template.'header';
			 $data['main']= $this->template.'company_type_add';
			 $data['footer']= $this->template.'footer';
			 $this->load->vars($data);
			 $this->load->view($this->template.'mcp_view');	
		  }
		  
       }	


       //--function to update & delete languages--//
       function company_type_update()
	   {
	       if($this->input->post())
	       {
	           if($this->input->post('update'))
	           {
	           	  $arr=$this->input->post();
	           	  
				  if($_FILES['banner']['name']){
				  	
				  	$filename = 'company_type_banner_'.$this->input->post("id").'_'.time().'.'.end(explode(".",$_FILES['banner']['name']));
				  	
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
				  		redirect(base_url().'mcp/company_type/company_type_update/'.$this->input->post("id"));
				  	}
				  	else
				  	{
				  		$arr['banner'] = $filename;
				  	}
				  }	
				  
				  $this->Mcompany_type->update($arr);
			   }
	           
			   if($this->input->post('delete'))
	           {
		          $this->Mcompany_type->delete($this->input->post('id'));
	           }
	          
			   redirect(base_url().'mcp/company_type');
	        }
			else
			{
				$data['header']= $this->template.'header';
				$data['main']=$this->template.'company_type_update';
				$data['footer']= $this->template.'footer';
				$data['id']=$this->uri->segment(4);
				$data['company_type']=$this->Mcompany_type->select(array('id'=>$data['id']));
				$this->load->vars($data);
				$this->load->view($this->template.'mcp_view');
			}
        }	
	
	    function company_status()
	    {
		   $status=array('id'=>$this->input->post('id'),'status'=>$this->input->post('status'));
		   $this->Mcompany_type->status_update($status);
		   echo 'status_updated';
		}	
		/**
		 *
		 * Function to save theme the themes are Retail 1,Catering 2,Medical 3
		 *
		 */
		
		function save_theme( ){
			$theme 		= $this->input->post( 'theme' );
			$type_id 	= $this->input->post( 'type_id' );
			$result = $this->Mcompany_type->save_theme_fr_type( $theme, $type_id );
			if( $result ){
		   		echo 'success';
			}else{
		   		echo 'failed';
			}
		}

    }
?>