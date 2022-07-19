<?php 
class Country extends CI_Controller{ 
     var $template='mcp/';
	 var $search='';
	
	function __construct()
	{
	     parent::__construct();
		 $this->load->helper('url');
		 $this->load->helper('form');
		 $this->template = 'mcp/';
		 $this->load->model('mcp/Mcountry');
		 
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
	 
	 
	 //----- Default fun ---//
	 function index()
	 {    
	     if($this->input->post('search'))
	     {
	     	//print_r($this->input->post()); die();
		    $this->search = $this->input->post();
		    $data['country']=$this->Mcountry->select($this->search);
		 }
		 else
		 { 
		    $data['country']=$this->Mcountry->select();
		 }
		 
		  $data['header']= $this->template.'header';
		  $data['main']= $this->template.'country';
		  $data['footer']= $this->template.'footer';
          $this->load->vars($data);
		  $this->load->view($this->template.'mcp_view');
	  	  
      }
	  
	  
	 //--function to delete value --//
	 function delete()
	 {
	 
	    $id= $this->uri->segment(4);	 
	    $this->Mcountry->delete($id);
	    redirect($this->template.'country', 'location');
	 }
	 
	 
	 //--function to add value--//
	 function add()
	 { 
	    if($this->input->post())
	    {
	       $this->Mcountry->add($this->input->post('country_name'));
		   redirect($this->template.'country', 'location');
		}
		
		    $data['header']= $this->template.'header';
		    $data['main']= $this->template.'country_add';
		    $data['footer']= $this->template.'footer';
		    $this->load->vars($data);
		    $this->load->view($this->template.'/mcp_view');
	 }
	 
	 
	 //--function to update  value--// 
     function update()
     {
	   
         $this->load->model($this->template.'Mindex');
	      /*if($this->session_validate())
		   {*/
     		 if($this->input->post())
			  {
			    if($this->input->post('update'))
		          $this->Mcountry->update($this->input->post('country_id'),$this->input->post('country_name'));
			   else if($this->input->post('delete'))
			       $this->Mcountry->delete($this->input->post('country_id'));
	          redirect('mcp/country','location');
	         }
		     $data['id']= $this->uri->segment(4);
			 $data['country_name']=$this->uri->segment(5);
			 $data['header']= $this->template.'header';
		     $data['main']= $this->template.'country_edit';
		     $data['footer']= $this->template.'footer';
             $this->load->vars($data);
		  	 $this->load->view($this->template.'/mcp_view');
	     /*}*/
	 }
	
}
?>
