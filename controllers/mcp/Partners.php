<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Partners extends CI_Controller
	{		
		var $tempUrl = '';
		var $template = '';
		
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			
			$this->tempUrl = base_url().'application/views/mcp';
			$this->template = "/mcp";
			
			$this->load->model('mcp/MPartners');			
			/*$this->load->model('mcp/Mindex');
			
			if($this->session->userdata('username')||$this->session->userdata('is_logged_in'))
			{
			   $current_session_id=$this->session->userdata('session_id');//--getting current session id--//
			   $current_user=$this->session->userdata('username');//--getting current session user--//
			   
			   if(!$this->Mindex->session_validate($current_session_id,$current_user))
				 redirect('mcp/mcplogin','refresh');
			}
			else
			   redirect('mcp/mcplogin','refresh');	*/		
			   
			$current_user = $this->session->userdata('username');
		    $is_logged_in = $this->session->userdata('is_logged_in');
			
		    if( !$current_user || !$is_logged_in )
			  redirect('mcp/mcplogin','refresh');
		}
		
		function index()
		{	
		    $this->manage_partners();
		}
		
		function manage_partners()
		{
		    $partners = $this->MPartners->get_partners();
			
			$start = 0;
			$limit = 20;
			
			$page = ($this->uri->segment(4))?($this->uri->segment(4)):1;
			
		    $this->load->library('pagination');

			$config['base_url'] = base_url().'/mcp/partners/manage_partners/';
			$config['total_rows'] = count($partners);
			$config['per_page'] = $limit;
			$config['uri_segment'] = 4;
			$config['first_link'] = _('First');
			$config['last_link'] = _('Last');
			$config['next_link'] = _('Next').' &raquo;';
			$config['prev_link'] = '&laquo; '._('Prev');
			
			$this->pagination->initialize($config);
			
			if($page)
			{
			   $start = ($page-1)*$limit;
			}
			
			//$data['partners'] = $this->MPartners->get_partners( '', NULL, NULL, $start, $limit);
			$data['partners'] = $this->MPartners->get_partners_and_month_income( '', NULL, NULL, $start, $limit);
			
			$data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/partners';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');  
		}
		
		function add_partner()
		{
		    if($this->input->post('add_partner'))		
			{
				$data = $this->input->post();
				unset( $data[ 'image_name8' ] );
				unset( $data[ 'partner_logo' ] );
			   $partner_id = $this->MPartners->add_partner( $data );
			   
			   if($this->input->post('partner_logo')){
					$ext = pathinfo ( FCPATH.'assets/temp_uploads/'.$this->input->post('partner_logo'), PATHINFO_EXTENSION );
					$logo = 'partner_logo_'.$partner_id.'.'.$ext;
					$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('partner_logo'));
					file_put_contents( dirname(__FILE__).'/../../../assets/partner_logo/'.$logo, $image_file);
					$this->MPartners->update_logo( $partner_id, $logo );
				}

			   redirect('mcp/partners/manage_partners');
			     
			}
		
		    $data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/add_partner';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		function edit_partner($partner_id)
		{
		    if($this->input->post('update_partner'))		
			{ 
				$data = $this->input->post();
				if ( !$this->input->post( 'sho_check' ) ) {
					$data['sho_check'] = '0';
				}
				if($this->input->post('partner_logo')){
					$ext = pathinfo ( FCPATH.'assets/temp_uploads/'.$this->input->post('partner_logo'), PATHINFO_EXTENSION );
					$logo = 'partner_logo_'.$partner_id.'.'.$ext;
					$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('partner_logo'));
					file_put_contents( dirname(__FILE__).'/../../../assets/partner_logo/'.$logo, $image_file);
					$data[ 'p_logo_name' ] = $logo;
				}
				unset( $data[ 'image_name8' ] );
				unset( $data[ 'partner_logo' ] );
				if(!isset($data['p_manager']))
					$data['p_manager'] = 0;
		        $isUpd = $this->MPartners->update_partner($partner_id,$data);
			    redirect('mcp/partners/manage_partners');
				
			}
			
			$partner = $this->MPartners->get_partners( array( 'id' => $partner_id ) );
			$data['partner'] = $partner[0];
			
			$data['tempUrl']= $this->tempUrl;		
			$data['header'] = $this->template.'/header';
			$data['main'] = $this->template.'/edit_partner';
			$data['footer'] = $this->template.'/footer';
			
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		function delete_partner($partner_id)
		{
		   if(!empty($partner_id))
		   {
		      $isDel = $this->MPartners->delete_partner( array('id' => $partner_id) );
			  
			  redirect('mcp/partners/manage_partners');
		   }
		}
		
		function validate()
		{
			 //print_r($this->input->post());
			
			 $data = '';
			 
			 if($this->input->post('check') == 'email')
			 {
			    $partners = $this->MPartners->get_partners( array('p_email'=>$this->input->post('email')) );
				
				if(empty($partners))
				{
				   $data = 'notexist';
				}
				else
				{
				   $data = 'duplicate';
				}
			 }
							
			 if($this->input->post('check') == 'username')
			 {
			    $partners = $this->MPartners->get_partners( array('p_username'=>$this->input->post('username')) );
				
				if(empty($partners))
				{
				   $data = 'notexist';
				}
				else
				{
				   $data = 'duplicate';
				}
			 }
			 
			 echo json_encode(array("RESULT"=>$data));
		}
	}
?>