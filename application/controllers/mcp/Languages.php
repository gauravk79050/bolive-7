<?php

    class Languages extends CI_Controller
 	{
		  
	   var $template='mcp/';
	   
	   var $uploadImgPath="./assets/mcp/"; 
	   var $uploadFilePath="language/locales/"; 
	   
	   var $arr=array();
		    
			
			function __construct()
			{
			   parent::__construct();
			   $this->load->helper('url');
			   $this->load->helper('form');
			   $this->load->model($this->template.'Mlanguages');
			  
			  //--for the session authentication--//
			  /*$this->load->model($this->template.'Mindex');
			  if($this->session->userdata('username') || $this->session->userdata('is_logged_in'))
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
		 
		  
		  function index()
		  { 
		   	   if($this->input->post('search'))
	           {
			      $this->search=$this->input->post();
			      $data['languages']=$this->Mlanguages->select($this->search);
   		       }
	           else
			   {
			      $data['languages']=$this->Mlanguages->select();
			   }
			   
			   $data['header']= $this->template.'header';
			   $data['main']= $this->template.'languages';
			   $data['footer']= $this->template.'footer';
			   $this->load->vars($data);
			   $this->load->view($this->template.'mcp_view');	
		  }
		 
		  
		  //--function to upload files--//
		  function upload()
	      {
		  	  $this->load->library('upload');	
		      $arr = array('flag'=>'','language_file'=>'','language_file_2'=>'','locale'=>'');
			  
			  if(!empty($_FILES['flag']['name']))
			  {
			    //--to uplaod image file --//
			    $config['upload_path'] = $this->uploadImgPath.'images/lang-images/';
			    $config['allowed_types'] = 'gif|jpg|png';
			     $this->upload->initialize($config);//this function sets the config array
			    if ( ! $this->upload->do_upload('flag'))
				{
				  $error = array('error' => $this->upload->display_errors());
				  print_r($error);
				}
			    else
				{
				  $data = array('upload_data' => $this->upload->data());
				  // print_r($data);
				  $arr['flag']=$data['upload_data']['file_name'];
				}
	          }
			 
			  $arr['locale'] = $new_dir = $this->input->post('lang_code').'_'.$this->create_slug($this->input->post('lang_name'));
			  
			  $file_upload_path = dirname(__FILE__).'/../../'.$this->uploadFilePath.$new_dir.'/LC_MESSAGES';
			  
			  if(!is_dir($file_upload_path))
			    mkdir($file_upload_path, 0700, true);
			 
			  if(!empty($_FILES['language_file']['name']))
			  {
			     //--to upload language file--//				 
			     $config1['upload_path'] = $file_upload_path.'/';
			     $config1['allowed_types'] = 'po|gif|jpg|png';
			     $this->upload->initialize($config1);//this function sets the config
			     if ( ! $this->upload->do_upload('language_file'))
			     {
			        $error = array('error' => $this->upload->display_errors());
			        print_r($error);
			     }
			     else
			     {
			        $data1 = array('upload_data' => $this->upload->data());
					// print_r($data1);
			        $arr['language_file']= $data1['upload_data']['file_name'];
					//print_r($arr);
		         }
			  }
			  
			  if(!empty($_FILES['language_file_2']['name']))
			  {
			     //--to upload language file--//				 
			     $config1['upload_path'] = $file_upload_path.'/';
			     $config1['allowed_types'] = 'mo|gif|jpg|png';
			     $this->upload->initialize($config1);//this function sets the config
			     if ( ! $this->upload->do_upload('language_file_2'))
			     {
			        $error = array('error' => $this->upload->display_errors());
			        print_r($error);
			     }
			     else
			     {
			        $data2 = array('upload_data' => $this->upload->data());
					// print_r($data1);
			        $arr['language_file_2'] = $data2['upload_data']['file_name'];
					//print_r($arr);
		         }
			  }
			  
			  return $arr; 
		  }   
		
	      //--function to add languages--//
    	  function language_add()
 		  {
		     if($this->input->post('add'))
		     {
				  $arr=$this->input->post();
				  $uarr=$this->upload();
				  $arr=array_merge($arr,$uarr);
				  
				  $this->Mlanguages->insert($arr);
				  redirect(base_url().'mcp/languages');
		     } 
	         else
	   	     {
				 $data['header']= $this->template.'header';
				 $data['main']= $this->template.'language_add';
				 $data['footer']= $this->template.'footer';
				 $this->load->vars($data);
				 $this->load->view($this->template.'mcp_view');	
			 }
	      }	
	
	      
		  //--function to update & delete languages--//
		  function language_update()
		  {
			   if($this->input->post())
			   {
				 if($this->input->post('update'))
				 {
					$arr=$this->input->post();
					
					if(!empty($_FILES['language_file']['name']) || !empty($_FILES['language_file_2']['name']) || !empty($_FILES['flag']['name']))
					{
					   $uarr = $this->upload();
					   $arr = array_merge($arr,$uarr);
					}
				
					$this->Mlanguages->update($arr);
				 }
				 
				 if($this->input->post('delete'))
				 {
					$this->Mlanguages->delete($this->input->post('id'));
				 }
			  
				 redirect(base_url().'mcp/languages');
			   }
		   
			   $data['header']= $this->template.'header';
			   $data['main']=$this->template.'language_update';
			   $data['footer']= $this->template.'footer';
			   $data['id']=$this->uri->segment(4);
			   $data['languages']=$this->Mlanguages->select(array('id'=>$data['id']));
			   $this->load->vars($data);
			   $this->load->view($this->template.'mcp_view');
		  }
		  
		  
		  function create_slug($string)
		  {
				$str = strtolower(trim($string));
				$str = preg_replace("/[^a-z0-9-]/", "-", $str);
				$str = preg_replace("/-+/", "-", $str);
				$str = rtrim($str, "-");
				
				return $str;
		  }
	 
     }
?>