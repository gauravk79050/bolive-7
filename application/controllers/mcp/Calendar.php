<?php

    class Calendar extends CI_Controller
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
				
			  $current_user = $this->session->userdata('username');
			  $is_logged_in = $this->session->userdata('is_logged_in');
				
			  if( !$current_user || !$is_logged_in )
				redirect('mcp/mcplogin','refresh');
		 
		    }
		 
		  
		  function index()
		  { 
		   	   /*if($this->input->post('search'))
	           {
			      $this->search=$this->input->post();
			      $data['languages']=$this->Mlanguages->select($this->search);
   		       }
	           else
			   {
			      $data['languages']=$this->Mlanguages->select();
			   }*/
			   
			   $data['header']= $this->template.'header';
			   $data['main']= $this->template.'calendar_view';
			   $data['footer']= $this->template.'footer';
			   $this->load->vars($data);
			   $this->load->view($this->template.'mcp_view');	
		  }
		  
		  function set_holiday(){
			  $this->load->model($this->template.'Mcalendar');
			  $affected_rows=$this->Mcalendar->set_holiday_dates();
			  if($affected_rows){
				echo 'successsfully_updated';
			  }else{
			  	echo 'error_occured';
			  }
		  }
		  
		  function get_holiday(){
		  	  $this->load->model($this->template.'Mcalendar');
		  	  $month = $this->input->post('month');
		  	  $year = $this->input->post('year');
		  	  $country = $this->input->post('country');
		  	 
		  	  $response=$this->Mcalendar->get_holiday_dates($month,$year,$country);
		  	  echo $response;
		  }
		
     }
?>