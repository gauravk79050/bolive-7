<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ftp_settings extends CI_Controller{

	var $company_id = '';
	var $ibsoft_active = false;
	var $upload_path = '';
	
	function __construct(){
	
		parent::__construct();
		
		$this->load->helper('url');
		
		$this->load->library('session');
        $this->load->library('messages');
		
		$this->load->model('Mcompany');
		$this->load->model('MFtp_settings');
		
		$is_logged_in = $this->session->userdata('cp_is_logged_in');
		$this->company_id = $this->session->userdata('cp_user_id');		
		$this->company_role = $this->session->userdata('cp_user_role');
		$this->company_parent_id = $this->session->userdata('cp_user_parent_id');
		
		$this->company = array();
		$company =  $this->Mcompany->get_company();
		if( !empty($company) )
		  $this->company = $company[0];	
		
		if(!isset($is_logged_in) || $is_logged_in != true){
			redirect('cp/login');
		}
		
		$this->ibsoft_active = $this->Mcompany->if_ibsoft_active($this->company_id);
		$this->upload_path = realpath(APPPATH .'../assets/cp/images/company-gallery');
		
    }
	
	function index()
	{		
		$this->ftp_settings();
	}
	
	function ftp_settings()
	{
	    if( $this->input->post( 'submit' ) )
		{
			$submit_arr = array(
			                       'shop_url' => $this->input->post('shop_url'),
								   'shop_files_loc' => $this->input->post('shop_files_loc'),
								   'ftp_hostname' => $this->input->post('ftp_hostname'),
								   'ftp_username' => $this->input->post('ftp_username'),
								   'ftp_password' => $this->input->post('ftp_password'),
								   'access_permission' => ($this->input->post('access_permission'))?1:0,
									'obs_shop' => $this->input->post('obs_shop')
							   );
			
			$ftp_settings = $this->MFtp_settings->get_ftp_settings( array( 'company_id' => $this->company_id ) );
		    if( !empty( $ftp_settings ) )
		    {
			    // Update FTP Settings
				$this->MFtp_settings->update_ftp_settings( $submit_arr, array( 'company_id' => $this->company_id ) );
				
				$this->messages->add(_('FTP Settings updated successfully !'), 'success');
			}
			else
			{
				// Add FTP Settings
				$submit_arr['company_id'] = $this->company_id;
				$this->MFtp_settings->add_ftp_settings( $submit_arr );
				
				$this->messages->add(_('FTP Settings saved successfully !'), 'success');
			}

			$this->session->set_userdata('cp_website',$submit_arr['obs_shop']);
		}
		// start
		if($this->input->post('act') == "upd_domain"){
			$this->db->where('company_id',$this->company_id);
			$this->db->update('api',array('domain'=>$this->input->post('domain')));
			redirect('cp/ftp_settings','refresh');
		}

		// end
		
		$ftp_settings = $this->MFtp_settings->get_ftp_settings( array( 'company_id' => $this->company_id ) );
		if( !empty( $ftp_settings ) )
		   $ftp_settings = $ftp_settings[0];

			// start
			$this->load->model('Mapi');
			$data['api_codes'] = $this->Mapi->get_api();
		   // end
		$data['company'] = $ftp_settings; 
		$data['content'] = 'cp/ftp-settings';
		$this->load->view('cp/cp_view',$data);
	}
}
?>