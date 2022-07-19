<?php
class Affiliate extends CI_Controller
{
	var $template = "";
	
	var $rp_user_id = '';
	var $rp_username = '';
	
	var $partner = '';
	
	function __construct()
	{
		parent::__construct();
		
	    $this->template="/ap";
	    
		$this->load->model('mcp/MAffiliates');
		$this->load->model('mcp/Mcompanies');
		
	    $this->load->helper('form');
	    $this->load->helper('url');
	    $this->load->library('email');
	    $this->load->library('session');
		$this->load->library('messages');
		
		if($this->session->userdata('ap_username') && $this->session->userdata('is_ap_user_logged_in'))
		{
		   $current_session_id=$this->session->userdata('ap_session_id');
		   
		   $this->ap_user_id = $this->session->userdata('ap_user_id');
		   $this->ap_username = $this->session->userdata('ap_username');
		   
		   $affiliates = $this->MAffiliates->get_affiliates( array( 'id' => $this->ap_user_id ) );
		   
		   if(!empty($affiliates))
		   {
		      $this->affiliate = $affiliates[0];
		   }
		}
		else
		   redirect('ap/aplogin','refresh');
	}

	function index(){
	
	    $this->companies();		
	}
	
	function companies()
	{

	    $companies = $this->Mcompanies->get_company(array('approved'=>1,'status'=>'1','affiliate_id'=>$this->ap_user_id),array('id'=>'DESC'));
		
		$start = 0;
		$limit = 20;		
		$page = ($this->uri->segment(4))?($this->uri->segment(4)):1;
			
		$this->load->library('pagination');

		$config['base_url'] = base_url().'/ap/affiliate/companies/';
		$config['total_rows'] = count($companies);
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
		
		$owned_clients = 0;
		$paid_clients = 0;
		if(!empty($companies))
		  foreach($companies as $c)
		    if($c->affiliate_status == 1)
			  $paid_clients++;
			  
		$owned_clients = $paid_clients;
		$pay_per_client = (isset($this->affiliate->a_monthly_income)?($this->affiliate->a_monthly_income):0);
		$data['monthly_income'] = ($pay_per_client*$paid_clients); 
		$data['owned_clients'] = $owned_clients;
		$data['companies'] = $this->Mcompanies->get_company(array('approved'=>1,'status'=>'1','affiliate_id'=>$this->ap_user_id),array('id'=>'DESC'), '', $start, $limit);
		
		$data['hide_header_menu'] = false ;
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/ap_companies';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/ap_view');
	}	
	
	function settings()
	{
		if($this->input->post('update_affiliate'))		
		{
		   $isUpd = $this->MAffiliates->update_affiliate($this->ap_user_id,$this->input->post());
		   
		   redirect('ap/affiliate/settings');
			 
		}
		
		$affiliate = $this->MAffiliates->get_affiliates( array( 'id' => $this->ap_user_id ) );
		$data['affiliate'] = $affiliate[0];
			
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/ap_settings';
		$data['footer'] = $this->template.'/footer';
		
		$this->load->vars($data);
		$this->load->view($this->template.'/ap_view');
	}	
}
?>