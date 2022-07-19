<?php
class Reseller extends CI_Controller
{
	var $template="";
	var $tempUrl = '';
	var $rp_user_id = '';
	var $rp_username = '';
	
	var $partner = '';
	
	function __construct()
	{
		parent::__construct();
		
	    $this->template="/rp";
	    $this->tempUrl = base_url().'application/views/rp';
		$this->load->model('mcp/MPartners');
		$this->load->model('mcp/Mcompanies');
		$this->load->model('mcp/MReports');
		$this->load->model('mcp/Mpackage');
		$this->load->model('mcp/Mcountry');
		$this->load->model('mcp/Mcompany_type');
		$this->load->model('Mopening_hours');
		$this->load->model('Mgeneral_settings');
		
	    $this->load->helper('form');
	    $this->load->helper('url');
	    //$this->load->library('email');
	    $this->load->library('session');
		$this->load->library('messages');
		
		if($this->session->userdata('rp_username') && $this->session->userdata('is_rp_user_logged_in'))
		{
		   $current_session_id=$this->session->userdata('rp_session_id');
		   $this->rp_user_id = $this->session->userdata('rp_user_id');
		   $this->rp_username = $this->session->userdata('rp_username');
		   
		   $partners = $this->MPartners->get_partners( array( 'id' => $this->rp_user_id ) );
		   
		   
		   if(!empty($partners))
		   {
		   	
		      $this->partner = $partners[0];
		   }
		   
		}
		else
		   redirect('rp/rplogin','refresh');
	}

	function index(){
	    $this->companies();		
	}
	
	function companies()
	{
		$data['p_manager']=$this->partner->p_manager;
		
		//match below where conditions to be similar with mpartners model's get_partners() function and monthly income calculated in both the places should be same
	    //$companies = $this->Mcompanies->get_company(array('approved'=>1,'status'=>'1','partner_id'=>$this->rp_user_id, 'invoice_end_date' => '>= DATE(CURDATE())'),array('id'=>'DESC'));

		// $companies = $this->Mcompanies->get_company(array('status'=>'1','partner_id'=>$this->rp_user_id, 'invoice_end_date' => '>= DATE(CURDATE())'),array('id'=>'DESC'));
		$companies = $this->Mcompanies->get_company(array('status'=>'1','partner_id'=>$this->rp_user_id),array('id'=>'DESC'));		
		
		$start = 0;
		$limit = 0;		
		$page = ($this->uri->segment(4))?($this->uri->segment(4)):1;
			
		$this->load->library('pagination');

		$config['base_url'] = base_url().'/rp/reseller/companies/';
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
		$monthly_income = 0;
		$paid_clients = 0;
		if(!empty($companies))
		  foreach($companies as $c)
		    //if($c->partner_status == 1 && ($c->partner_total_commission || $c->partner_total_amount))
				//$monthly_income += !empty($c->partner_total_commission)?$c->partner_total_commission:($c->partner_total_amount/3);
// 		    if($c->partner_status == 1 && $c->partner_total_amount){
// 		    	$monthly_income += ($c->partner_total_amount/3);
// 			  	++$paid_clients;
// 		    }
			if($c->partner_total_commission){
				$monthly_income += ($c->partner_total_commission);
				++$paid_clients;
			}
		
		/*$owned_clients = $paid_clients;	  
		$pay_per_client = (isset($this->partner->p_monthly_income)?($this->partner->p_monthly_income):0);*/
		$data['suggestion']=$this->MReports->get_suggestion();
		$data['monthly_income'] = round($monthly_income,2);
		// --$data['monthly_income'] = ($pay_per_client*$paid_clients); 
		//$data['monthly_income'] = $this->MPartners->calc_partner_monthly_income( $this->rp_user_id, $companies );		
		$data['owned_clients'] = $paid_clients;

		//$data['companies'] = $this->Mcompanies->get_company(array('approved'=>1,'status'=>'1','partner_id'=>$this->rp_user_id, 'invoice_end_date' => '>= DATE(CURDATE())'),array('id'=>'DESC'), '', $start, $limit);
		$data['companies'] = $this->Mcompanies->get_company(array('status'=>'1','partner_id'=>$this->rp_user_id ),array('id'=>'DESC'), '', $start, $limit);
		$data['company_count'] = count($data['companies']);
		
		$data['hide_header_menu'] = false ;
		$data['company_suggested_corrections'] = $this->MReports->get_suggestion();
		$data['account_types'] = $this->Mcompanies->get_account_types();
		
		$data['header']=$this->template.'/header';
		$data['main']=$this->template.'/rp_companies';
		$data['footer']=$this->template.'/footer';
		$this->load->vars($data);
		$this->load->view($this->template.'/rp_view');
	}	
	
	function ajax_companies(){

		$this->load->library('utilities');
		$this->load->helper('phpmailer');
			
		$start   = $this->input->post('start');
		$limit   = $this->input->post('limit');
		$rp_data = $this->MPartners->get_rp_data($this->rp_user_id);

		if($this->input->post('btn_search'))
		{	
			$params = $this->input->post();
			// $where_array = array('status'=>'1','flag'=>"1",'partner_id'=>$this->rp_user_id, 'invoice_end_date' => '>= DATE(CURDATE())');
			$where_array = array('status'=>'1','flag'=>"1",'partner_id'=>$this->rp_user_id);

			//'>= DATE(CURDATE())' had to write this way as $query is constructed in Mcompanies
			$order_by = array();
			if ($params ['search_by'] == 'id') {
				$where_array['id'] = $params ['search_keyword'];
			} elseif ($params ['search_by'] == 'company_name' || $params ['search_by'] == 'email' || $params ['search_by'] == 'username' || $params ['search_by'] == 'city') {
				$where_array['like_columns'] = $params ['search_by'];
				$where_array['like_value'] = $params ['search_keyword'];
					
				if ($params ['ac_type_id'])
					$where_array['ac_type_id'] = $params ['ac_type_id'];
					
				if ($params ['order_by']) {
					$order = 'desc';
					if ($params ['order_by'] == 'id' || $params ['order_by'] == 'city')
						$order = 'asc';
						
					$order_by[$params ['order_by']] = $order;
				} else
					$order_by['id'] = 'desc';
			}

			if ( $rp_data['sho_check'] != '1' ) {
				$where_array[ 'show_sho_leads !' ] = '1';
			}
				
			$content = $this->Mcompanies->get_company($where_array,$order_by,null,$start,$limit);
		}
		else
		{
			//$content = $this->Mcompanies->get_company(array('approved'=>1,'status'=>'1','flag'=>"1",'partner_id'=>$this->rp_user_id, 'invoice_end_date' => '>= DATE(CURDATE())'),array('id'=>'DESC'), '', $start, $limit);
			// $content = $this->Mcompanies->get_company(array('status'=>'1','flag'=>"1",'partner_id'=>$this->rp_user_id, 'invoice_end_date' => '>= DATE(CURDATE())'),array('id'=>'DESC'), '', $start, $limit);

			if ( $rp_data['sho_check'] != '1' ) {
				$comp_data= array( 'show_sho_leads !' => '1','status'=>'1','flag'=>"1",'partner_id'=>$this->rp_user_id );
			}
			else {
				$comp_data= array( 'status'=>'1','flag'=>"1",'partner_id'=>$this->rp_user_id );
			}

			$content = $this->Mcompanies->get_company( $comp_data,array('id'=>'DESC'), '', $start, $limit);
		}
			
		$result=$this->Mcompanies->get_company_trial();
		$todays_date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
		$todays_time=strtotime($todays_date);
		$date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y')));
			
		$current=strtotime($date);
		if(!empty($content)){
			foreach($content as $key => $value)
			{
				$trial=strtotime($value->trial);
				if($todays_time==$trial)
				{
					$update_on_trail_=array('on_trial'=>'0');
					$update=$this->Mcompanies->update_on_trial($update_on_trail_,$value->id);
				}
					
				$content[$key]->last_30_day_order = $this->Mcompanies->last_30_days_order($value->id);
					
			}
		}
		
		$account_types = $this->Mcompanies->get_account_types();
		$response = array();
		$counter = 0;
		if(!empty($content)) {
			foreach($content as $cont){
		
				$ct = $this->Mcompany_type->select(array('id'=>$cont->type_id));
				if(!empty($ct))
					$company_type = $ct[0]->company_type_name;
				else
					$company_type = _('NONE');
		
				$status = '<select style="width:90px" class="textbox" type="select" id="status" name="status" onchange="company_status('.$cont->id.',this.value);"><option value="0" '.(($cont->status==0)?'selected="selected"':'').'>'._('INACTIVE').'</option><option value="1" '.(($cont->status==1)?'selected="selected"':'').'>'._('ACTIVE').'</option></select>';
		
				$action = '<a href="'.base_url().'mcp/companies/update/'.$cont->id.'">MOREINFO</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="get_login_fdd(\''.$cont->id.'\',\''.$cont->username.'\',\''.$cont->password.'\');" id="login_'.$cont->id.'">LOGIN</a>';
		
				$ac_type = '';
					
				if(!empty($account_types))
				{
					$ac_type = '<select class="textbox" name="ac_type_id" onchange="change_ac_type(\''.$cont->id.'\',this.value);">';
		
					foreach($account_types as $at)
					{
						$ac_type .= '<option value="'.$at->id.'" '.(($cont->ac_type_id==$at->id)?'selected="selected"':'').'>'.strtoupper($at->ac_title).'</option>';
					}
		
					$ac_type .= '</select>';
				}
		
				$obsdesk_status = '';
		
				$obsdesk_status .= '<select class="textbox" name="obsdesk_status" onchange="change_obsdesk_status(\''.$cont->id.'\',this.value);">';
				$obsdesk_status .= '<option value="0" '.(($cont->obsdesk_status==0)?'selected="selected"':'').'>'._('INACTIVE').'</option>';
				$obsdesk_status .= '<option value="1" '.(($cont->obsdesk_status==1)?'selected="selected"':'').'>'._('ACTIVE').'</option>';
				$obsdesk_status .= '</select>';
		
				$bestelonline_shop_status = '<select class="textbox" name="bestelonline_status" onchange="change_bo_shop_status(\''.$cont->id.'\',this.value);">';
				$bestelonline_shop_status .= '<option value="0" '.(($cont->shop_testdrive==0)?'selected="selected"':'').'>'._('Active').'</option>';
				$bestelonline_shop_status .= '<option value="1" '.(($cont->shop_testdrive==1)?'selected="selected"':'').'>'._('TestDrive').'</option>';
				$bestelonline_shop_status .= '</select>';

				$this->db->select( 'id, direct_kcp' );
				$this->db->where( 'company_id' , $cont->id );
				$this->db->where( "( ( products.direct_kcp = 0 AND products.semi_product = 1 ) OR ( products.semi_product = 0 ) )" );
				$this->db->where( array( 'products.categories_id !=' => '0' ,'products.proname !=' => '' ) );
				$prod_ids = $this->db->get('products')->result();
				$total_products = sizeof( $prod_ids );
				$fixed_products = 0;
				$temp_arr = array( );
				$temp_arr4 = array( );
				$oo = array( );
				foreach ( $prod_ids as $k => $qr ){
					$complete = 1;
					if( $qr->direct_kcp == 1 ){
						$this->db->where( array( 'obs_pro_id' => $qr->id,'is_obs_product' => 0 ) );
						$result = $this->db->get('fdd_pro_quantity')->result_array();
						if(empty($result)){
							$complete = 0;
							array_push( $temp_arr, $qr->id );
						}
					}
					else{
						$this->db->where(array( 'obs_pro_id' => $qr->id ) );
						$result_custom = $this->db->get('fdd_pro_quantity')->result_array();
						if(empty($result_custom)){
							$complete = 0;
							array_push( $temp_arr4, $qr->id );
						}
						else{
						}
					}

					if($complete == 1){
						$fixed_products++;
						array_push( $oo, $qr->id );
					}
						
				}
				if( $total_products != 0 && $fixed_products != 0 ) {
					$complete_per = ( $fixed_products / $total_products ) * 100;
					if( is_float( $complete_per ) ) {
						$complete_per = round( $complete_per, 2 );
					}
				} else {
					$complete_per = 0;
				}
		
				$trail_date1= strtotime($cont->trial);
				$date = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
				$current=strtotime($date);
					
				$response[] = array(
						$cont->id,//0
						($cont->company_name),//1
						($company_type),//2
						($cont->city),//3
						date('d-m-Y',strtotime($cont->registration_date)),//4
						($status),//5
						($ac_type),//6
						($action),//7
						($cont->address.'<br />'.$cont->city.'<br />'.$cont->zipcode),//8
						($cont->phone),//9
						($cont->email),//10
						($cont->first_name.' '.$cont->last_name),//11
						($obsdesk_status),//12
						$cont->ac_type_id,//13
						date('d-m-Y',$trail_date1),//14
						$cont->excel_import_file_name,//15
						$current,//16
						$trail_date1,//17
						$cont->on_trial,//18
						($bestelonline_shop_status),//19
						$cont->last_30_day_order,//20
						$cont->partner_invoice_date != '0000-00-00' ? date('d/m/Y',strtotime($cont->partner_invoice_date)) : '-',//21
						$cont->invoice_end_date != '0000-00-00' ? date('d/m/Y',strtotime($cont->invoice_end_date)) : '-',//22


						//defined_money_format(round(($cont->partner_total_amount/3),2)),//
						//defined_money_format(round(($cont->partner_total_commission),2)),//
						$cont->partner_message,//23
						$cont->partner_status,//24
						$cont->username,//25
						$cont->password,//26
						$cont->reseller_remarks,//27
						$total_products,//28
						$fixed_products,//29
						$complete_per, //30
				);
			}
		}
		echo json_encode($response);
	}
	
	function settings()
	{
		if($this->input->post('update_partner'))		
		{
			$data = $this->input->post();
			if($this->input->post('partner_logo')){
				$ext = pathinfo ( FCPATH.'assets/temp_uploads/'.$this->input->post('partner_logo'), PATHINFO_EXTENSION );
				$logo = 'partner_logo_'.$partner_id.'.'.$ext;
				$image_file = file_get_contents(base_url().'assets/temp_uploads/'.$this->input->post('partner_logo'));
				file_put_contents( dirname(__FILE__).'/../../../assets/partner_logo/'.$logo, $image_file);
				$data[ 'p_logo_name' ] = $logo;
			}
			unset( $data[ 'image_name8' ] );
			unset( $data[ 'partner_logo' ] );
		   $isUpd = $this->MPartners->update_partner($this->rp_user_id, $data );
		   
		   redirect('rp/reseller/settings');
			 
		}
		$data['p_manager']=$this->partner->p_manager;
		
		$partner = $this->MPartners->get_partners( array( 'id' => $this->rp_user_id ) );
		$data['partner'] = $partner[0];
			
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/rp_settings';
		$data['footer'] = $this->template.'/footer';
		$data['company_suggested_corrections'] = $this->MReports->get_suggestion();
		$this->load->vars($data);
		$this->load->view($this->template.'/rp_view');
	}
   function suggested_corrections()
    {   
    	$data['days'] = $days = $this->Mopening_hours->get_days();
    	$data['company_suggested_corrections'] = $this->MReports->get_suggestion();
    	//$data['company_suggested_corrections']=$company_suggested_corrections;
    	$data['p_manager']=$this->partner->p_manager;
    	$data['header'] = $this->template.'/header';
    	$data['main'] = $this->template.'/rp_suggested_corrections';
    	$data['footer'] = $this->template.'/footer';
    	$this->load->vars($data);
    	$this->load->view($this->template.'/rp_view');
    	if($this->input->post('update_reports'))
    	{
    	
    		$data=$this->input->post();
    		
    		if(!isset($data['status']))
    			$data['status']=0;
    		$isUpd=$this->MReports->update_suggestion($data);
    		if($isUpd)
    		{
    			redirect('rp/reseller/index');
    		}
    	
    	}
    }
    function trial_date_insert()
    {
			$company_id=$this->input->post('company_id');
			$date = $this->input->post('date');
	        $createDate = new DateTime($date);
			$trial1=$createDate->format('d-m-Y');
			$data['trial']=$this->input->post('date');
			$co_email=$this->input->post('co_email');
			$result=$this->Mcompanies->update_trial($data,$company_id);
			$from_mail = $this->config->item('no_reply_email');
			if($result)
			{
				/*$getMail = file_get_contents( base_url().'assets/mail_templates/trial_prolongate_mail.html' );
				$parse_email_array = array(
						"date"=>$trial1
				);
					
				$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );
				$mail_body = '<html><head></head><body>'.$mail_body.'</body></html>';*/
				$mail_data = array(
									"date"=>$trial1
								);
				$mail_body = $this->load->view('mail_templates/trial_prolongate_mail', $mail_data, true );
				
				$query = send_email( $co_email, $from_mail, _("OBS: Company Trial Prolongated"), $mail_body, NULL, NULL, NULL, 'no_reply', 'company', 'company_trial_prolongated');
				if($query)
					echo json_encode(array('RESULT'=>"success"));
				else 
					echo json_encode(array('RESULT'=>"fail"));
			}
			else
			{
				echo json_encode(array('RESULT'=>"fail"));
			}
		}
    function approve_suggestion()
    {
    	if($this->input->post('suggestion_id'))
    	{
    		$id = $this->input->post('suggestion_id');
    		$suggestion_info = $this->MReports->get_suggestion_approval_data($id);
    		$company_id = $suggestion_info['company_id'];
    		$update_array = array();
    		if($this->input->post('company_name') && $this->input->post('company_name') != '')
    			$update_array['company_name'] = $this->input->post('company_name');
    		if($this->input->post('address') && $this->input->post('address') != '')
    			$update_array['address'] = $this->input->post('address');
    		if($this->input->post('zipcode') && $this->input->post('zipcode') != '')
    			$update_array['zipcode'] = $this->input->post('zipcode');
    		if($this->input->post('city') && $this->input->post('city') != '')
    			$update_array['city'] = $this->input->post('city');
    		if($this->input->post('website') && $this->input->post('website') != '')
    			$update_array['website'] = $this->input->post('website');
    		if($this->input->post('description') && $this->input->post('description') != '')
    			$update_array['company_desc'] = $this->input->post('description');
    	    
    		$this->db->where('id' , $company_id);
    		$this->db->update('company',$update_array);
    		
    		$days = $this->Mopening_hours->get_days();
    		
    		$time_1 = $this->input->post('time_1');
    		$time_2 = $this->input->post('time_2');
    		$time_3 = $this->input->post('time_3');
    		$time_4 = $this->input->post('time_4');
    		foreach($days as $d)
    		{
    			$opening_hours = array(
    					'time_1'=>$time_1[$d->id],
    					'time_2'=>$time_2[$d->id],
    					'time_3'=>$time_3[$d->id],
    					'time_4'=>$time_4[$d->id]
    			);
    			
    			$this->db->where('company_id' , $company_id);
    			$this->db->where('day_id' , $d->id);
    			$this->db->update('opening_hours',$opening_hours);
    			 
    		}
    		$flag=$this->MReports->disapprove_suggestion($id);
    	}
    	
    	redirect(base_url()."rp/reseller/suggested_corrections");
    }
    
    
    function disapprove_suggestion($id = null)
    {
	    /*$id=$this->input->post('id');
	    $flag=$this->MReports->disapprove_suggestion($id);
	    if($flag)
	    {
	    	echo json_encode(array('RESULT'=>"success"));
	    }
	    else
	    {
	    	echo json_encode(array('RESULT'=>"fail"));
	    }*/
    	if($id && is_numeric($id)){
    		$this->MReports->disapprove_suggestion($id);
    	}
    	
    	redirect(base_url()."rp/reseller/suggested_corrections");
    }
    function manage_companies()
    { 
    	
    	$data['days'] = $days = $this->db->get('days')->result();
    	
    	$this->load->library('form_validation');
    		
    	if($this->input->post())
    	{
    	
    		$data['tempUrl'] = $this->tempUrl;
    		$this->form_validation->set_rules('company_name','Company Name','required');
    		$this->form_validation->set_rules('first_name','First name','required');
    		$this->form_validation->set_rules('last_name','Last Name','required');
    		$this->form_validation->set_rules('email','Email Id','required');
    		$this->form_validation->set_rules('phone','Phone Number','required');
    		$this->form_validation->set_rules('website','Website','required');
    		$this->form_validation->set_rules('address','Address','required');
    		$this->form_validation->set_rules('zipcode','Zipcode','required');
    		$this->form_validation->set_rules('city','City','required');
    		$this->form_validation->set_rules('country_id','Country','required');
    		$this->form_validation->set_rules('username','Username','required');
    		$this->form_validation->set_rules('password','Password','required');
    		/*$this->form_validation->set_rules('registration_date','Date of registration','required');
    		$this->form_validation->set_rules('earnings_year','Earnings per Year','required');*/
    			
    		if($this->form_validation->run()==FALSE)
    		{
    			$this->form_validation->set_message('required','Required');
    		}
    		else
    		{
    			//print_r($this->input->post()); die;
    			$a['company_slug'] 			= 	$this->create_slug($this->input->post('company_name'));
    			$a['company_name'] 			= 	$this->input->post('company_name');
    			$a['type_id'] 				=	implode("#",$this->input->post('type_id'));
    			$a['first_name']			=	$this->input->post('first_name');
    			$a['last_name']				=	$this->input->post('last_name');
    			$a['email']					=	$this->input->post('email');
    			$a['phone']					=	$this->input->post('phone');
    			//$a['website']=$this->input->post('website');
    			$a['address']				=	$this->input->post('address');
    			$a['admin_remarks']			=	$this->input->post('admin_remarks');
    			$a['city']					=	$this->input->post('city');
    			$a['country_id']			=	$this->input->post('country_id');
    			$a['existing_order_page']	=	$this->input->post('existing_order_page');
    			$a['username']				=	$this->input->post('username');
    			$a['password']				=	$this->input->post('password');
    			$a['packages_id']			=	$this->input->post('packages_id');
    			$a['ac_type_id']			=	$this->input->post('ac_type_id');
    			if($a['ac_type_id'] != 1)
    				$a['trial'] 			=	date("Y-m-d H:i:s", strtotime("+1 months", time()));
    			
    			$a['registered_by']			=	$this->input->post('registered_by');
    			$a['manager_id']			=	$this->partner->id;
    			
    			if($this->input->post('have_website'))
    			{
    				$a['have_website'] 		=	1;
    			}
    			else
    			{
    				$a['have_website'] 		= 	0;
    				$a['package'] 			=	$this->input->post('package');
    				$a['domain'] 			=	$this->input->post('domain');
    				$a['canregister'] 		=	$this->input->post('canregister');
    			}
    				
    			if(!isset($a['domain']) || $a['domain'] == '')
    			{
    				$a['website']			=	$this->input->post('website');
    			}
    				
    			$a['email_ads']				=	$this->input->post('email_ads');
    			$a['footer_text']			=	$this->input->post('footer_text');
    			$a['registration_date']		=	$registration_date	=	date("Y-m-d H:i:s");
    				
    			$date = $registration_date;
    	
    			$expiry_date = '';
    			if($this->input->post('5year_subscription'))
    			{
    				$a['5year_subscription']=	'1';
    				//$newdate = strtotime ( '+5 year' , strtotime ( $date ) ) ;
    				$newdate = strtotime ( '+2 year' , strtotime ( $date ) ) ;
    			}
    			else
    			{
    				$a['5year_subscription']=	'0';
    				$newdate = strtotime ( '+1 year' , strtotime ( $date ) ) ;
    			}
    			$expiry_date = date ( 'Y-m-d' , $newdate );
    				
    			$a['expiry_date']			=	 $expiry_date; //$this->input->post('expiry_date');
    			$a['earnings_year']			=	$this->input->post('earnings_year');
    			$a['zipcode']				=	$this->input->post('zipcode');
    			$a['parent_id']				=	$this->input->post('parent_id');
    				
    			if($this->input->post('role'))
    			{
    				$a['role'] 				=	'super';
    			}
    			elseif($a['parent_id']==0)
    			{
    				$a['role'] 				=	'master';
    			}
    			elseif($a['parent_id']!=0)
    			{
    				$a['role'] 				=	'sub';
    			}
    				
    			$a['company_fb_url'] 		=	$this->input->post('company_fb_url');
    			$a['existing_order_page'] 	=	$this->input->post('existing_order_page');
    			$a['company_desc'] 			=	$this->input->post('company_desc');
    			$a['partner_id']			=	json_encode( array( '0' => $this->partner->id ) );
    			$a['login_first_time']		=	'1';
    			$a['approved'] 				=	'1';
    			
    			$address = $this->input->post('address')." ".$this->input->post('zipcode')." ".$this->input->post('city')." ".( ($this->input->post('country_id') == "21")?"BELGIE":"NEDERLAND" );
    			$this->load->helper("geolocation");
    			$location = get_geolocation($address);
    			$a["geo_location"] = json_encode($location);
    			
    			$company_id 				=	$this->Mcompanies->insert($a);
    			
    			if($company_id){

    				/**
    				 * Doing default settings for the company
    				 */
    				$this->load->helper('default_setting');
    				do_settings($company_id,$a['company_name']);
    				 
    				$opening_hours = array();
    				if(!empty($days))
    				{
    					$time_1 = $this->input->post('time_1');
    					$time_2 = $this->input->post('time_2');
    					$time_3 = $this->input->post('time_3');
    					$time_4 = $this->input->post('time_4');
    						
    					foreach($days as $d)
    					{
    						$opening_hours[$d->id] = array(
    								'time_1'=>$time_1[$d->id],
    								'time_2'=>$time_2[$d->id],
    								'time_3'=>$time_3[$d->id],
    								'time_4'=>$time_4[$d->id]
    						);
    					}
    				}
    				 
    				if(!empty($opening_hours)){
    					foreach($opening_hours as $id=>$index)
    					{
    						$row = array(
    								'company_id' => $company_id,
    								'day_id' => $id,
    								'time_1' => $index['time_1'],
    								'time_2' => $index['time_2'],
    								'time_3' => $index['time_3'],
    								'time_4' => $index['time_4']
    						);
    						$this->db->insert('opening_hours',$row);
    						$insert_ids[] = $this->db->insert_id();
    					}
    				}
    				
    				/*  >>>>> MAIL HAS TO BE SEND <<<<<<<<<<<<<<*/
    				//$this->load->library('utilities');
    				$this->load->helper('phpmailer');
    				/*$getMail = file_get_contents( base_url().'assets/mail_templates/rp_register_company.html' );
    				$mail_p_1 = _("Dear,");
    				$mail_p_3 = _("We launched a portalsite called <a href=\"http://www.bestelonline.nu\">Bestelonline.nu</a> and we just added your info regarding your shop right here: ");
    				$mail_p_3 .= "<a href=\"http://www.bestelonline.nu/<companyname>\">http://www.bestelonline.nu/<companyname></a> ";
    				$mail_p_4 = _("You can manage every aspect of that detailpage easy for FREE");
    				$mail_p_4 .= _("You can login at");
    				$mail_p_4 .= "<a href=\"".base_url()."cp\">".base_url()."cp</a> ";
    				$mail_p_4 .= _("with login");
    				$mail_p_4 .= ": <b>".$a['username']."</b> / <b>".$a['password']."</b> ";
    				$mail_p_4 .= _("(don't lose this info)");
    				$mail_h_2 = _("Wait a minute.. this is spam right");
    				$mail_p_5 = _("Not at all. We are sending this mail to you personally to notify you that we have been working very hard the last 4 years on a portal and unique ordersystem build for shopowners like you. What makes it unique is that is build up with the latest techniques and we even can implement a webshop in your existing website by adding a few codes (");
    				$mail_p_5 .= _("check the video at");
    				$mail_p_5 .= " <a href=\"www.onlinebestelsysteem.net\">www.onlinebestelsysteem.net</a>)<br>";
					$mail_p_5 .= _("Wouldn't it be fantastic if you could have a full featured webshop for your clients at a low price of 19€/mnth (no commissions) and where you don't have to look at your PC to handle your orders? Well.. we have it.");
    				$mail_h_3 = _("Hey.. wait a minute - do I have to pay anything here or even in the future");
    				$mail_p_6 = _("Not at all. The detailpage you see is for free for always and is meant to let your clients find info about your company very quickly when searching for keywords like <type> <companyname> (high searchengine rankings). Offcourse if you want to have a webshop in the near future you can upgrade your account. Please check our (cheap) plans: ");
    				$mail_p_6 .= "<a href=\"".$this->config->item("portal_url")."services\">".$this->config->item("portal_url")."services</a> ";
    				$mail_h_4 = _("I don't need any webshop mate - my clients are emailing me already.");
    				$mail_p_7 = _("That's right - but you still have to spend hours/day or week before your desktop to reply, manage all orders seperatly right? Also you have to collect all emailaddresses, names, phonenumbers seperatly if you want to send them a mail or contact them individually. With our system you don't even have to look at your PC as all orders can be printed out immediately after they came in and in case you want to send a mail to your clients, an advanced mailmanager is build in. A promotion or holiday? Just setup your mail and send it to everyone in 5 seconds. .. and a lot more advantages the system has.");
    				$mail_h_5 = _("Sounds interesting - but I still have a question....");
    				$mail_p_8 = _("If you are interested in our system we prefer a personal approach by having a meeting on some day (without any obligations). Please call us at 0473/250528 or fill in the form at ");
    				$mail_p_8 .= "<a href=\"".$this->config->item("portal_url")."contact_us\">".$this->config->item("portal_url")."contact_us</a> ";
    				$mail_p_8 .= _("so we can help you further");
    				$mail_p_9 = _("Some FAQS you can also find at ");
    				$mail_p_9 .= "<a href=\"".$this->config->item("portal_url")."help\">".$this->config->item("portal_url")."help</a>";
    				$mail_p_10 = _("Don't hesitate - participate!");
    				
    				$parse_email_array = array(
    						"mail_p_1" => $mail_p_1,
    						"mail_p_2" => $mail_p_2,
    						"mail_p_3" => $mail_p_3,
    						"mail_p_4" => $mail_p_4,
    						"mail_p_5" => $mail_p_5,
    						"mail_p_6" => $mail_p_6,
    						"mail_p_7" => $mail_p_7,
    						"mail_p_8" => $mail_p_8,
    						"mail_p_9" => $mail_p_9,
    						"mail_p_10" => $mail_p_10,
    						"mail_h_1" => $mail_h_1,
    						"mail_h_2" => $mail_h_2,
    						"mail_h_3" => $mail_h_3,
    						"mail_h_4" => $mail_h_4,
    						"mail_h_5" => $mail_h_5
    				);

    				$mail_body = $this->utilities->parseMailText( $getMail, $parse_email_array );
    				
    				$query = send_email( $a['email'], "carl@onlinebestelsysteem.net", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'site_admin', 'company', 'rp_company_registered_notice');*/
    				
    				$mail_data['username'] = $a['username'];
    				$mail_data['password'] = $a['password'];
    				$mail_data['portal_url'] = $this->config->item("portal_url");
    				$mail_data['company_slug'] = $a['company_slug'];
    				
    				/**
    				 * Fetching company typr slug
    				 */
    				$company_types = explode("#",$a['type_id']);
    				if(!empty($company_types))
    					$company_type_id = $company_types[0];
    				else
    					$company_type_id = 1;
    				
    				//$this->load->model('mcompany_type');
    				$company_type_info = $this->mcompany_type->get_company_type(array('id' => $company_type_id));
    				if(!empty($company_type_info))
    					$mail_data['company_type_slug'] = $company_type_info[0]->slug;
    				
    				$mail_body = $this->load->view('mail_templates/rp_register_company', $mail_data, true );
    					
    				$query = send_email( $a['email'], "noreply@bestelonline.nu", "Bestelonline.nu", $mail_body, NULL, NULL, NULL, 'no_reply_bo', 'company', 'approval_message');
    				
    			}
    			
    			redirect(base_url()."rp/reseller/manage_companies");
    		}
    	}
    		
    	$data['super_companies'] = $this->Mcompanies->get_company(array('role'=>'super'));
    	$data['all_companies'] = $this->Mcompanies->get_company();
    	$data['package']=$this->Mpackage->select();
    	$data['country']=$this->Mcountry->select();
    	$data['company_type']=$this->Mcompany_type->select(array('status'=>'ACTIVE'));
    	$data['account_types'] = $this->Mcompanies->get_account_types();
    	$data['company_suggested_corrections'] = $this->MReports->get_suggestion();
    	$data['p_manager']=$this->partner->p_manager;
		$data['manager_id']=$this->partner->id;
    	$data['header'] = $this->template.'/header';
    	$data['main'] = $this->template.'/rp_manage_companies';
    	$data['footer'] = $this->template.'/footer';
    	$this->load->vars($data);
    	$this->load->view($this->template.'/rp_view');
    }
    
    function create_slug($companyname){
    	$slug_str = strtolower(trim($companyname));
		$slug_str = preg_replace('/\s+/', '-', $slug_str);
		$slug_str = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $slug_str));
		$slug_str = preg_replace('/-+/', '-', $slug_str);
		$slug_str = rtrim($slug_str, "-");
		
    	$company_slugs_array = $this->db->select('company_slug')->get('company')->result();
    	$company_slugs = array();
    	foreach($company_slugs_array as $company_slug){
    		$company_slugs[] = $company_slug->company_slug;
    	}
    	$old_str = $slug_str;
    	for($company_counter=2;;$company_counter++){
    		if(in_array($slug_str,$company_slugs)){
    			$slug_str = $old_str.'-'.$company_counter;
    		}else{
    			break;
    		}
    
    	}
    	return $slug_str;
    }
    
    function edit_manage_companies($id=null)
    {
    	$this->load->helper(array('form', 'url'));
    	$this->load->library('form_validation');
    	$this->load->model('Mgeneral_settings');
    	
    	$data['days'] = $days = $this->db->get('days')->result();
    	
    	if($this->input->post())
    	{
    	
				$this->form_validation->set_rules('company_name','Company Name','required');
				$this->form_validation->set_rules('first_name','First name','required');
				$this->form_validation->set_rules('last_name','Last Name','required');
				$this->form_validation->set_rules('email','Email Id','required');
				$this->form_validation->set_rules('phone','Phone Number','required');
				$this->form_validation->set_rules('website','Website','required');
				$this->form_validation->set_rules('address','Address','required');
				$this->form_validation->set_rules('zipcode','Zipcode','required');
				$this->form_validation->set_rules('city','City','required');
				$this->form_validation->set_rules('country_id','Country','required');
				$this->form_validation->set_rules('username','Username','required');
				$this->form_validation->set_rules('password','Password','required');
				/*$this->form_validation->set_rules('registration_date','Date of registration','required');
				$this->form_validation->set_rules('earnings_year','Earnings per Year','required');*/
				
				if($this->form_validation->run()==FALSE){
					//echo 'error';
				}else{
				
					$gs = array();
					$id_ = $a['id'] = $this->input->post('id');
					$a['company_name']=$this->input->post('company_name');
					$a['type_id']=implode("#",$this->input->post('type_id'));
					$a['first_name']=$this->input->post('first_name');
					$a['last_name']=$this->input->post('last_name');
					$a['email']=$this->input->post('email');
					$a['phone']=$this->input->post('phone');
					$a['website']=$this->input->post('website');
					$a['address']=$this->input->post('address');
					$a['admin_remarks']=$this->input->post('admin_remarks');
					$a['city']=$this->input->post('city');
					$a['country_id']=$this->input->post('country_id');
					$a['existing_order_page']=$this->input->post('existing_order_page');					
					$a['username']=$this->input->post('username');
					if($this->input->post('password') != '')
						$a['password']=$this->input->post('password');
					$a['ac_type_id']=$this->input->post('ac_type_id');
					if($a['ac_type_id'] == 1)
						$a['trial'] = date("Y-m-d H:i:s", strtotime("+1 months", time())); 
						
					$a['packages_id']=$this->input->post('packages_id');
					$a['email_ads']=$this->input->post('email_ads');
					$a['footer_text']=$this->input->post('footer_text');
					$a['zipcode']=$this->input->post('zipcode');
					$a['parent_id']=$this->input->post('parent_id');
					
					if($this->input->post('role'))
					{
					   $a['role'] = 'super';					   
					}
					elseif($a['parent_id']!=0)
					{
					   $a['role'] = 'sub';
					}
					else
					{
					   $a['role'] = 'master';
					}
					
					$a['company_fb_url'] 		=	$this->input->post('company_fb_url');
					$a['existing_order_page'] 	=	$this->input->post('existing_order_page');
					$a['company_desc'] 			=	$this->input->post('company_desc');

					$address = $this->input->post('address')." ".$this->input->post('zipcode')." ".$this->input->post('city')." ".( ($this->input->post('country_id') == "21")?"BELGIE":"NEDERLAND" );
					$this->load->helper("geolocation");
					$location = get_geolocation($address);
					$a["geo_location"] = json_encode($location);
					
					$this->Mcompanies->update($a);	

					$gs["hide_bp_intro"] = ($this->input->post('hide_bp_intro'))?1:0;
					
					$this->Mgeneral_settings->update_company_general_settings($id,$gs);
						
					$opening_hours = array();
					if(!empty($days))
					{
						$time_1 = $this->input->post('time_1');
						$time_2 = $this->input->post('time_2');
						$time_3 = $this->input->post('time_3');
						$time_4 = $this->input->post('time_4');
					
						foreach($days as $d)
						{
							$opening_hours[$d->id] = array(
								'time_1'=>$time_1[$d->id],
								'time_2'=>$time_2[$d->id],
								'time_3'=>$time_3[$d->id],
								'time_4'=>$time_4[$d->id]
							);
						}
					}
							
					if(!empty($opening_hours)){
						
						$is_exists = $this->db->get_where("opening_hours" , array('company_id' => $id_))->result_array();

						if(!empty($is_exists)){
							foreach($opening_hours as $id=>$index)
							{
								$row = array(
										'time_1' => $index['time_1'],
										'time_2' => $index['time_2'],
										'time_3' => $index['time_3'],
										'time_4' => $index['time_4']
								);
								$this->db->where('company_id' , $id_);
								$this->db->where('day_id' , $id);
								$this->db->update('opening_hours',$row);
							}
						}else{
							foreach($opening_hours as $id=>$index)
							{
								$row = array(
										'company_id' => $id_,
										'day_id' => $id,
										'time_1' => $index['time_1'],
										'time_2' => $index['time_2'],
										'time_3' => $index['time_3'],
										'time_4' => $index['time_4']
								);
								$this->db->insert('opening_hours',$row);
							}
						}
						
					}
					
					redirect('rp/reseller/edit_manage_companies/'.$id_);
				}
			
    	}
    	
    	$data['id'] = $id;
    	$data['general_setting'] = $this->Mgeneral_settings->get_general_settings(array("company_id" => $id),"hide_bp_intro");
    	$data['opening_hours'] = $this->db->get_where("opening_hours" , array("company_id" => $id))->result_array();
    	$data['package']=$this->Mpackage->select();
    	$data['country']=$this->Mcountry->select();
    	$data['company_type']=$this->Mcompany_type->select(array('status'=>'ACTIVE'));
    	$data['account_types'] = $this->Mcompanies->get_account_types();
    	$data['company_suggested_corrections'] = $this->MReports->get_suggestion();
    	$result = $this->Mcompanies->get_company(array('id'=>$id));
    	$data['companies']=$result[0];
    	$data['p_manager']=$this->partner->p_manager;
    	$data['manager_id']=$this->partner->id;
    	$data['header'] = $this->template.'/header';
    	$data['main'] = $this->template.'/rp_manage_companies_detail';
    	$data['footer'] = $this->template.'/footer';
    	
    	$this->load->vars($data);
    	$this->load->view($this->template.'/rp_view');
    	 

    }
    
    /**
     * This function is used to fetch city name via postcode
     * @return array an array containing city name if found otherwise null
     */
    function fetching_city()
    {
    	$response = array("error" => 1, "data" => '');
    	
    	if($this->input->post("postCode")){
    		$city_info = $this->Mcountry->get_city($this->input->post("postCode"));
    		if(!empty($city_info)){
    			$response = array("error" => 0, "data" => $city_info['0']['area_name']);
    		}
    	}
    	
    	echo json_encode($response);
    }
    
    /**
     * This function is used to detect whether a given url is admin's competitors url
     * @return boolean $exist true if exist and false if not
     */
    function checking_competitor_url(){
    	
    	$response = array("error" => 0, "data" => '');
    	
    	if($this->input->post("c_url")){
    		
    		$urlToCheck = $this->input->post("c_url");
    		$urlToCheck = $this->addhttp($urlToCheck);
    		$urlToCheck = parse_url($urlToCheck);
    		
    		$this->load->model("mcp/Mcompetitor");
    		
    		$all_competitor = $this->Mcompetitor->get_competitor();
    		
    		if(!empty($all_competitor)){
    			foreach($all_competitor as $competitor){
    				if(strpos($competitor['competitor_url'],$urlToCheck['host']) !== false){
    					$response = array("error" => 1, "data" => _("you cannot enter this domain"));
    					break;
    				}
    			}
    		}
    	}
    	
    	echo json_encode($response);
    }
    
    /**
     * This private function is used to add http:// | https:// | ftp:// | ftps:// id not found in the given url
     * @param string $url URL to which add
     * @return string $url URL after adding
     */
    private function addhttp($url) {
    	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    		$url = "http://" . $url;
    	}
    	return $url;
    }
    
    /**
     * This public function is used to show particular corrections in details
     * @access public
     */
    public function suggested_corrections_detail($id = null){
    	$data['days'] = $days = $this->Mopening_hours->get_days();
    	if($id){
    		$data['suggestion'] = $this->MReports->get_suggestion_approval_data($id);
    		$company = $this->Mcompanies->get_company(array('id'=>$data['suggestion']['company_id']),array('id'=>'DESC'));
    		$data['companyInfo'] = $company[0]; 
    		$data['openingHours'] = $this->Mopening_hours->get_opening_hours($data['suggestion']['company_id']);
    		
    	}
    	$this->load->vars($data);
		$this->load->view($this->template.'/rp_suggested_corrections_detail');
    }
    
    /**
     * This public function is used to block IP of particular suggestions
     * @access public
     */
    public function block_ip($id = null){
    	if($id){
    		$suggestion = $this->MReports->get_suggestion_approval_data($id);
    		$ip = $suggestion['ip_address']; 
    		$this->db->insert("block_ips",array('ip_address' => $ip));
    	}
    	redirect(base_url()."rp/reseller/suggested_corrections");
    }
    
    /**
     * This public function is used to block IP of particular suggestions
     * @access public
     */
    public function delete_company($id = null){
    	if($id){
    		$suggestion = $this->MReports->get_suggestion_approval_data($id);
    		$company_id = $suggestion['company_id'];
    		$this->Mcompanies->delete($company_id);
    	}
    	redirect(base_url()."rp/reseller/suggested_corrections");
    }
    
    /**
     * This public function is used to update reseller remark
     * @access public
     */
    public function update_remark(){
    	$response = array("error" => 1, "data" => _('Some error occured! Please try again.'));
    	if($this->input->post('company_id')){
    		$res = $this->Mcompanies->update_remark($this->input->post('company_id'),$this->input->post('remark'));
    		if($res){
    			$response = array("error" => 0, "data" => _('Remark Updated Succesfully.'));
    		}
    	}
    	
    	echo json_encode($response);
    }
}
?>