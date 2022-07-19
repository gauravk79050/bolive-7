<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients extends CI_Controller
{
	
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
			redirect(base_url().'cp/_categories/page_not_found');
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

	function lijst($action = NULL , $id = NULL, $comp_id = NULL)
	{
		if( $this->company_role == 'master' || $this->company_role == 'sub' )
		{
			$data['clients']=array();
			$data['is_discount_card_activated'] = false;
			if( $action == 'client_details' )
			{
				$client_id = $id;
				$data['comp_id'] = $this->company_id;
				$data['is_set_discount_card_setting'] = false;
				$general_settings = $this->Mgeneral_settings->get_general_settings();
				if(!empty($general_settings)){
					$activated_addons = $general_settings['0']->activated_addons;
					$activated_addons = explode("#",$activated_addons);
					if(is_array($activated_addons) && !empty($activated_addons) && in_array("4",$activated_addons)){
						$data['is_discount_card_activated'] = true;
					}
					if($general_settings['0']->activate_discount_card != 0){
						$data['is_set_discount_card_setting'] = true;
					}
				}
				if($this->input->post('add_client_no'))
				{
					$where['client_id'] = $this->input->post('client_id');
					$where['company_id'] = $this->input->post('company_id');
					$update['client_number'] = $this->input->post('client_number');
				   	$updated = $this->Mclients->update_client_info($where,$update);
				   
				   	if($updated)
				    	$this->messages->add(_('Client number updated successfully !'),'succes');
				   	else
				     	$this->messages->add(_('Some error occured while updating client number.'),'error'); 
				}
				
				if($this->input->post('add_discount_card_no'))
				{
					$where['client_id'] = $this->input->post('client_id');
					$where['company_id'] = $this->input->post('company_id');
					$update['discount_card_number'] = $this->input->post('discount_card_number');
				   	$updated = $this->Mclients->update_client_info($where,$update);
				
					if($updated)
						$this->messages->add(_('Discount card number updated successfully !'),'succes');
					else
						$this->messages->add(_('Some error occured while updating Discount card number.'),'error');
				}
				
				if($this->input->post('add_client_discount'))
				{
					$where['client_id'] = $this->input->post('client_id');
					$where['company_id'] = $this->input->post('company_id');
					$update['disc_per_client'] = $this->input->post('disc_per_client');
				   	$updated = $this->Mclients->update_client_info($where,$update);
				
					if($updated)
						$this->messages->add(_('Discount updated successfully !'),'succes');
					else
						$this->messages->add(_('Some error occured while updating Discount.'),'error');
				}
				
				$data['client_details'] = $this->Mclients->get_clients(array('id'=>$client_id));
				$data['client_number'] = $this->Mclients->get_client_number($client_id,$this->company_id);
				
				$data['content']='cp/client_details';
				
				$this->load->view('cp/cp_view',$data);
			}
			elseif( $action == 'send_mail')
			{
				/*===first we need to get the email ads ===*/
				$Company = $this->Mcompany->get_company();
				
				if( !empty($Company) && $Company[0]->email_ads == "1"){
					$this->load->model('Memail_messages');		
					$email_messages_detail = $this->Memail_messages->get_email_messages();
					$Email_ads = $email_messages_detail[0]->emailads_text_message;
					//echo $Email_ads;
				}else{
					$Email_ads = "";
				}
				
				/*=====get email address of the perticular company form general settings=====*/
				
				$general_settings = $this->Mgeneral_settings->get_general_settings();
				/*===========================================================================*/
				if($this->input->post('all_clients')){
					
					$clients = $this->Mclients->get_company_clients($this->company_id,NULL,NULL,array('client_numbers.newsletter' => 'subscribe')); //$this->Mclients->get_clients();
					$this->messages->clear();
					
					if( !empty($clients) )
					foreach($clients as $client){
						$email_c = $client->email_c;
						$sender = $general_settings[0]->emailid;
						$subject = $this->input->post('subject');
						$fname = $client->firstname_c;
						$lname = $client->lastname_c;
						$message = $this->input->post('message');    	
						$verify_code = $client->verify_code;
						$CompanyName = $Company[0]->company_name;	
						$flag = $this->send_client_mail($email_c,$sender,$subject,$fname,$lname,$message,$verify_code,$Email_ads,$CompanyName);
						if(array_key_exists('success',$flag)){
							$this->messages->add(_('Mail has been sent successfully to').' - '.$email_c,'success');
						}else{
							$this->messages->add(_('Error !! Mail couldn\'t be sent.'),'error');
						}
					}
				}else{
					$email_c = $this->input->post('client_id');
					$this->messages->clear();
					$client = $this->Mclients->get_clients(array('email_c'=>$email_c));
					
					if( !empty($client) ) foreach($client as $c) {
						
						$sender = $general_settings[0]->emailid;
						$subject = $this->input->post('subject');
						$fname = $c->firstname_c;
						$lname = $c->lastname_c;
						$message = $this->input->post('message');    	
						$verify_code = $c->verify_code;
						$CompanyName = $Company[0]->company_name;	
						
						$flag = $this->send_client_mail($email_c,$sender,$subject,$fname,$lname,$message,$verify_code,$Email_ads,$CompanyName);
						
						if(array_key_exists('success',$flag)){
							$this->messages->add(_('Mail has been sent successfully.'),'success');
						}else{
							$this->messages->add(_('Error!! Mail couldn\'t be sent.'),'error');
						}
						
						break;
					} 
				}	
					
				redirect('cp/clients');		
			}
			elseif( $action == 'search_client' )
			{

				function sanitize($string)
				{
				    $string = filter_var($string, FILTER_SANITIZE_STRING);
				    $string = trim($string);
				    $string = stripslashes($string);
				    $string = strip_tags($string);

				    return $string;
				}



					if($this->input->post('search_by')=='Name')
					{
						$name=sanitize($this->input->post('search_keyword'));
						$data['clients']=$this->Mclients->get_company_clients($this->company_id,'','','(firstname_c LIKE "'.$name.'" OR lastname_c LIKE "'.$name.'")');
							// $sql = $this->db->last_query();
							// echo $sql;die;
					}
					elseif($this->input->post('search_by')=='Email')
					{
						$email=sanitize($this->input->post('search_keyword'));
						$data['clients']=$this->Mclients->get_company_clients($this->company_id,'','',array( 'email_c LIKE' => '%'.$email.'%' ));
					}
					
				$data['content'] = 'cp/clients';
				$this->load->view('cp/cp_view',$data);	
						
			}
			elseif( $action == 'downlaod_customer_info' )
			{
				$this->downlaod_customer_info();			
			}
			elseif( $action == 'add' )
			{
				//$this->load->helper(array('form'));
				
				if($this->input->post('add_client')){
					$this->form_validation->set_rules('firstname_c', 'firstname_c', 'required');
					$this->form_validation->set_rules('lastname_c', 'lastname_c', 'required');
					$this->form_validation->set_rules('address_c', 'address_c', 'required');
					$this->form_validation->set_rules('housenumber_c', 'housenumber_c', 'required');
					$this->form_validation->set_rules('postcode_c', 'postcode_c', 'required');
					$this->form_validation->set_rules('city_c', 'city_c', 'required');
					$this->form_validation->set_rules('country_id', 'country_id', 'required|is_natural_no_zero');
					$this->form_validation->set_rules('phone_c', 'phone_c', 'required');
					$this->form_validation->set_rules('email_c', 'email_c', 'required|valid_email|callback_is_unique_email');
					$this->form_validation->set_rules('password_c', 'password_c', 'required');
					$this->form_validation->set_rules('conf_password_c', 'conf_password_c', 'required|matches[password_c]');
					
					if ($this->form_validation->run() == FALSE)
					{
						
					}
					else
					{
						$result = array();
						$insert_array = array(
								'company_id' => $this->company_id,
								'firstname_c' => $this->input->post('firstname_c'),
								'lastname_c' => $this->input->post('lastname_c'),
								'company_c' => $this->input->post('company_c'),
								'address_c' => $this->input->post('address_c'),
								'housenumber_c' => $this->input->post('housenumber_c'),
								'postcode_c' => $this->input->post('postcode_c'),
								'city_c' => $this->input->post('city_c'),
								'country_id' => $this->input->post('country_id'),
								'phone_c' => $this->input->post('phone_c'),
								'mobile_c' => $this->input->post('mobile_c'),
								'fax_c' => $this->input->post('fax_c'),
								'email_c' => $this->input->post('email_c'),
								'password_c' => $this->input->post('password_c'),
								'notifications' => ( ($this->input->post('notifications'))?'subscribe':'unsubscribe' ),
								'company_c' => $this->input->post('company_c'),
								'vat_c' => $this->input->post('vat_c'),
								'created_c' => date("Y-m-d H:i:s"),
								'updated_c' => date("Y-m-d H:i:s"),
								'verified' => "1"
						);
						
						$client_id = $this->Mclients->add($insert_array);
						if($client_id){
							$insert_array = array(
								'company_id' => $this->company_id,
								'client_id' => $client_id,
								'discount_card_number' => $this->input->post('discount_card_number'),
								'newsletter' => ( ($this->input->post('newsletter'))?'subscribe':'unsubscribe'),
								'client_number' => $this->input->post('client_number'),
								'disc_per_client' => $this->input->post('disc_per_client'),
								'associated' => "1"
							);
							$this->Mclients->add_clients_number($insert_array);
							
							$this->session->set_flashdata('success',_('Client Info added successfully'));
						}else{
							$this->session->set_flashdata('error',_('Client Info did not added successfully'));
						}
						//$this->session->set_flashdata('success',_('Client Info added successfully'));
						redirect(base_url().'cp/clients/lijst/add');
					}
					
				}
				
				$data['is_set_discount_card_setting'] = false;
				$general_settings = $this->Mgeneral_settings->get_general_settings();
				if(!empty($general_settings)){
					$activated_addons = $general_settings['0']->activated_addons;
					$activated_addons = explode("#",$activated_addons);
					if(is_array($activated_addons) && !empty($activated_addons) && in_array("4",$activated_addons)){
						$data['is_discount_card_activated'] = true;
					}
					if($general_settings['0']->activate_discount_card != 0){
						$data['is_set_discount_card_setting'] = true;
					}
				}
				
				// Getting delivery settings
				$this->load->model('Mdelivery_areas');
				$delivery_settings = $this->Mdelivery_areas->get_delivery_area_settings();
				
				if(!empty($delivery_settings)){
					if($delivery_settings[0]['type'] == 'international'){
						$this->load->model('Mcountry');
						$data['countries']=$this->Mcountry->get_country(null, 'international');
					}else{
						$this->load->model('Mcountry');
						$data['countries']=$this->Mcountry->get_country();
					}
				}else{
					$this->load->model('Mcountry');
					$data['countries']=$this->Mcountry->get_country();
				}
				
				$data['content'] = 'cp/client_add';
				$this->load->view('cp/cp_view',$data);	
						
			}else{
			
				$data['clients']=$this->Mclients->get_company_clients($this->company_id);
				$data['content'] = 'cp/clients';
				$this->load->view('cp/cp_view',$data);	
			}
		}
		elseif( $this->company_role == 'super' )
		{
		    
			if( $action == 'client_details' )
			{
				$client_id = $id;
				$data['comp_id'] = $comp_id;
				
				if($this->input->post('add_client_no'))
				{
				   $updated = $this->Mclients->update_client_number();
				   
				   if($updated)
				     $this->messages->add(_('Client number updated successfully !'),'success');
				   else
				     $this->messages->add(_('Some error occured while updating client number.'),'error'); 
				}
				
				$data['client_details'] = $this->Mclients->get_clients(array('id'=>$client_id));
				$data['client_number'] = $this->Mclients->get_client_number($client_id , $comp_id);
				
				$data['content']='cp/client_details';
				
				$this->load->view('cp/cp_view',$data);
			}
			else
			{			
				$data['clients'] = array();
				$data['pickup_delivery_closed']=$this->Mcalender->get_pickup_delivery_closed(); //for calender
				
				$data['clients'][ 0 ] = $this->Mclients->get_company_clients( $this->company_id );
				
				$data['sub_companies'] = $sub_companies = $this->Mcompany->get_company( array( 'parent_id' => $this->company_id ) );
				
				if( !empty( $sub_companies ))
				{
					foreach( $sub_companies as $sc )
					{
						$clients =  $this->Mclients->get_company_clients( $sc->company_id );
						$data['clients'][ $sc->company_name ] = $clients;
					}
				}
				
				$data['content'] = 'cp/clients';
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
	
	function downlaod_customer_info()
	{
		$clients = $this->Mclients->get_company_clients($this->company_id);
	
		if( !empty( $clients ) )
		{
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Customer Info') );
				
			$counter = 1;
				
			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('ID') )->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Company') )->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Firstname') )->getStyle('C'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D'.$counter, _('Lastname') )->getStyle('D'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E'.$counter, _('Address') )->getStyle('E'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F'.$counter, _('House Number') )->getStyle('F'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G'.$counter, _('Postal Code') )->getStyle('G'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H'.$counter, _('City') )->getStyle('H'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('I'.$counter, _('Country') )->getStyle('I'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('J'.$counter, _('Phone No') )->getStyle('J'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('K'.$counter, _('Mobile No') )->getStyle('K'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('L'.$counter, _('Fax') )->getStyle('L'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('M'.$counter, _('Email') )->getStyle('M'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('N'.$counter, _('Vat') )->getStyle('N'.$counter)->getFont()->setBold(true);
				
			foreach( $clients as $c )
			{
				$counter++;
					
				$this->excel->getActiveSheet()->setCellValue('A'.$counter, $c->id );
				$this->excel->getActiveSheet()->setCellValue('B'.$counter, $c->company_c );
				$this->excel->getActiveSheet()->setCellValue('C'.$counter, $c->firstname_c );
				$this->excel->getActiveSheet()->setCellValue('D'.$counter, $c->lastname_c );
				$this->excel->getActiveSheet()->setCellValue('E'.$counter, $c->address_c );
				$this->excel->getActiveSheet()->setCellValue('F'.$counter, $c->housenumber_c );
				$this->excel->getActiveSheet()->setCellValue('G'.$counter, $c->postcode_c );
				$this->excel->getActiveSheet()->setCellValue('H'.$counter, $c->city_c );
				$this->excel->getActiveSheet()->setCellValue('I'.$counter, $c->country_id );
				$this->excel->getActiveSheet()->setCellValue('J'.$counter, $c->phone_c );
				$this->excel->getActiveSheet()->setCellValue('K'.$counter, $c->mobile_c );
				$this->excel->getActiveSheet()->setCellValue('L'.$counter, $c->fax_c );
				$this->excel->getActiveSheet()->setCellValue('M'.$counter, $c->email_c );
				$this->excel->getActiveSheet()->setCellValue('N'.$counter, $c->vat );
			}
				
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
				
			$datestamp = date("d-m-Y");
			$filename = "Clients-Info-".$datestamp.".xls";
				
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
				
			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function delete_clients(){
	
		$this->Mclients->delete_clients();
		return 'successfully  deleted';
	}

	function remove_client(){

		$this->Mclients->remove_client();
		return 'successfully  deleted';
	}


 }
?>