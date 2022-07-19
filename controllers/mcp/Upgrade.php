<?php
class Upgrade extends CI_Controller{

    var $tempUrl = '';
	var $template = '';

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

		$this->tempUrl = base_url().'application/views/mcp';
		$this->template = "/mcp";
		$this->temp="/mcp/companies";

		$this->load->model('mcp/Mcompanies');
		$this->load->model('MFtp_settings');

		$current_user = $this->session->userdata('username');
		$is_logged_in = $this->session->userdata('is_logged_in');

		if( !$current_user || !$is_logged_in )
		  redirect('mcp/mcplogin','refresh');
	}

	function index()
	{
	    $this->update_client_files();
	}

	function update_client_files()
	{
		$msg = '';
		if( $this->input->post('submit') )
		{
		    $company_ids = $this->input->post('company_id');

			if( !empty( $company_ids ) )
			{
				$companies_ftp_details = $this->MFtp_settings->get_ftp_settings( array( 'shop_files_loc <>' => '', 'ftp_hostname <>' => '', 'ftp_username <>' => '', 'ftp_password <>' => '', 'access_permission' => 1 ) , array( 'company_id' => $company_ids ) );

				if( !empty($companies_ftp_details) )
				{
					$msg_arr = $this->upgrade_files( $companies_ftp_details );

					if( empty($msg_arr) )
					  $msg = array('success' => _('Success - FTP files of the selected companies are been updated successfully !'));
					else
					  $msg = array('error' => implode(',<br />',$msg_arr) );
				}
				else
				  $msg = array('error'=> _('Error - None of the companies have entered their full FTP Details !'));
			}
			else
			{
				$msg = array('error'=> _('Error - Please select some companies to update their client files !'));
			}
		}

		$data['message'] = $msg;
		$data['company_ftp_settings'] = $this->Mcompanies->get_approved_company_ftp_settings();

		$data['tempUrl']=$this->tempUrl;
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/client_list';
		$data['footer'] = $this->template.'/footer';

		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
	}

	function upgrade_files( $companies_ftp_details )
	{
	    if( empty($companies_ftp_details) )
		  return false;
		else
		{
			 $this->load->library('ftp');

		    $error = array();

			foreach( $companies_ftp_details as $comp)
			{

				/*$config['hostname'] = $comp->ftp_hostname;
				$config['username'] = $comp->ftp_username;
				$config['password'] = $comp->ftp_password;
				$config['debug'] = TRUE;

				$this->ftp->connect($config);


				//$this->ftp->upload(dirname(__FILE__).'/../../../new-online-bestellen/includes/cart.php', 'includes/cart.php', 'ascii', 0775);

				$this->ftp->close();
				echo $comp->shop_files_loc;
				die;*/
				$shop_files_loc = $comp->shop_files_loc;
				$ftp_hostname = $comp->ftp_hostname;
				$ftp_username = $comp->ftp_username;
				$ftp_password = $comp->ftp_password;
				$shop_url = $comp->shop_url;
				$company_id = $comp->company_id;

				$response_1 = $this->create_config_file( $shop_url, $company_id );
				//$response_2 = $this->create_index_file( $shop_url, $company_id );

				if( !$response_1 ) //if( !$response_1 || !$response_2 )
				{
				  $error[] = _('Problem on ').$ftp_hostname.' : '._('Can\'t create config/index file for this FTP server !');
				}
				else
				{
					//$updated_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/';
					$updated_file_loc = dirname(__FILE__).'/../../../online-bestellen/';

					$org_root_files = array( 'new-config.php' ); //$org_root_files = array( 'new-index.html', 'new-config.php' );
					$root_files = array( 'config.php' ); //$root_files = array( 'index.html', 'config.php' );

					$org_js_files = array( 'obs-script.js.php' );

					//$org_includes_files = array( 'captcha.php', 'cart.php', 'functions.php', 'get-style.css.php', 'init.php', 'request.php', 'user.php' );
					$org_includes_files = array('request.php' );

					//$includes_files = array( 'captcha.php', 'cart.php', 'functions.php', 'get-style.css.php', 'init.php', 'request.php', 'user.php' );
					$includes_files = array('request.php');
					// --- >> Connect FTP << --- //
					$conn_id = ftp_connect( $ftp_hostname ) or die("Couldn't connect to $ftp_server");

					// --- >> Login FTP << --- //
					if ( @ftp_login($conn_id, $ftp_username, $ftp_password) )
					{
						if ( @ftp_chdir( $conn_id, $shop_files_loc ) )
						{
							$dir_arr = array( 'images', 'includes', 'js' );

							// Getting directories and file names
							$files_in_dir = ftp_nlist($conn_id,".");

							ftp_pasv($conn_id, true);

							// try to create the internal directories on ftp


							// foreach( $dir_arr as $dir )
							// {
							// 	if(!in_array( $dir , $files_in_dir)){
							// 		if ( @ftp_mkdir($conn_id, $dir) )
							// 		{
							// 			// DIR created
							// 		}
							// 		else
							// 		{
							// 			$error[] = _('Problem on ').$ftp_hostname.' : '._('There was a problem while creating ')."'".$dir."'"._(' folder, on server ! May be it was already there.');
							// 		}
							// 	}

							// }

							// // --- >> Start updating ROOT files << --- //
							// if( !empty($root_files) )
							// 	foreach( $root_files as $id=>$rf )
							// 	{
							// 		if ( @ftp_put($conn_id, $rf, $updated_file_loc.$org_root_files[$id] , FTP_ASCII) )
							// 		{
							// 		   //@ftp_chmod($conn_id, 0777, $rf);	 // Set Read-Write permisssion  on the uploaded file.
							// 		}
							// 		else
							// 		{
							// 		   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').$rf._(' on server !');
							// 		}
							// 	}

							// --- >> Start updating JS files << --- //
							// if( !empty($org_js_files) )
							// 	foreach( $org_js_files as $id=>$jf )
							// 	{
							// 		if ( @ftp_put($conn_id, 'js/'.$jf, $updated_file_loc.'js/'.$org_js_files[$id] , FTP_ASCII) )
							// 		{
							// 		   //@ftp_chmod($conn_id, 0777, $jf);	 // Set Read-Write permisssion  on the uploaded file.
							// 		}
							// 		else
							// 		{
							// 		   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'js/'.$jf._(' on server !');
							// 		}
							// 	}

							// --- >> Start updating INCLUDES files << --- //
							if( !empty($includes_files) )
								foreach( $includes_files as $id=>$lf )
								{

									if ( @ftp_put($conn_id, 'includes/'.$lf, $updated_file_loc.'includes/'.$org_includes_files[$id] , FTP_ASCII) )
									{
									   //@ftp_chmod($conn_id, 0777, $lf);	 // Set Read-Write permisssion  on the uploaded file.
									}
									else
									{
									   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'includes/'.$lf._(' on server !');
									}
								}

							// --- >> Start updating IMAGES files << --- //
							// if( !empty($images_files) )
							// 	foreach( $images_files as $id=>$imgf )
							// 	{
							// 		if ( @ftp_put($conn_id, 'images/'.$imgf, $updated_file_loc.'images/'.$org_images_files[$id] , FTP_ASCII) )
							// 		{
							// 		   //@ftp_chmod($conn_id, 0777, $imgf);	 // Set Read-Write permisssion  on the uploaded file.
							// 		}
							// 		else
							// 		{
							// 		   $error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'images/'.$imgf._(' on server !');
							// 		}
							// 	}
						}
						else
						{
							$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t change the directory to online-bestellen folder !');
						}
					}
					else
					{
						$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t connect as  ').$ftp_username._(', login failed !');
					}
				} // End - If Config
			}

			return $error;
		}
	}

	function create_config_file(  $shop_url = NULL, $company_id = NULL  )
	{
	   if( !$company_id )
	     return false;

	   $this->load->model('Mapi');
	   $comp_api = $this->Mapi->get_company_api( $company_id );

	   if( !empty($comp_api) )
	   {
	      $api_id = $comp_api->api_id;
		  $api_secret = $comp_api->api_secret;

		  if( !$api_id || !$api_secret )
		    return false;

		  // ---- >> Set other URL settings << ---- //

		  $api_server = base_url();                                //'http://www.onlinebestelsysteem.net/obs'
		  //--- $api_assets = base_url().'obs-api';                      //'http://www.onlinebestelsysteem.net/obs/obs-api'
		  //--- $cdn_api_assets = $this->config->item('cdn_api_assets'); //'http://cdn.onlinebestelsysteem.net/obs/obs-api'
		  $api_assets = base_url().'obs-api-new';                      //'http://www.onlinebestelsysteem.net/obs/obs-api'
		  $cdn_api_assets = $this->config->item('cdn_api_assets').'-new'; //'http://cdn.onlinebestelsysteem.net/obs/obs-api'

		  // ---- >> Replace Last '/' from the URLs << ---- //

		  if( substr( $shop_url, strlen($shop_url)-1 , 1) == '/' )
		    $shop_url = substr( $shop_url, 0, -1);

		  // ---- >> Adding www in the URLs << ------------ //
		  //$shop_url = str_replace("http://","http://www.",$shop_url);

		  if( substr( $api_server, strlen($api_server)-1 , 1) == '/' )
		    $api_server = substr( $api_server, 0, -1);

		  if( substr( $api_assets, strlen($api_assets)-1 , 1) == '/' )
		    $api_assets = substr( $api_assets, 0, -1);

		  // ---- >> File Locations << ---- //

		  //--- $org_config_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/config.php';
		  //--- $new_config_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/new-config.php';
		  $org_config_file_loc = dirname(__FILE__).'/../../../online-bestellen/config.php';
		  $new_config_file_loc = dirname(__FILE__).'/../../../online-bestellen/new-config.php';

		  // ---- >> Read Sample Config File << ---- //

		  $rfobj = fopen( $org_config_file_loc, 'r' ) or exit( _('Error - Unable to open the original config file !') );
		  $fdata = fread( $rfobj, filesize($org_config_file_loc) );
		  fclose( $rfobj );

		  // ---- >> Modify - Do settings << ---- //

		  $fdata = str_replace( "'CLIENT_URL'", "'".$shop_url."'", $fdata );
		  $fdata = str_replace( "'API_ID'", "'".$api_id."'", $fdata );
		  $fdata = str_replace( "'API_SECRET_KEY'", "'".$api_secret."'", $fdata );

	      if( $api_server )
		    $fdata = str_replace( "'API_SERVER'", "'".$api_server."'", $fdata );

		  if( $api_assets )
		    $fdata = str_replace( "'API_ASSETS'", "'".$api_assets."'", $fdata );

		  if( $api_assets )
		    $fdata = str_replace( "'CDN_API_ASSETS'", "'".$cdn_api_assets."'", $fdata );

		  // ---- >> Create New Config File << ---- //

		  $wfobj = fopen( $new_config_file_loc, 'w' ) or die( _('Error - Cannot create a Config file for Company ID').' : '.$company_id );
		  fwrite($wfobj, $fdata);
		  fclose( $wfobj );

		  return true;
	   }
	   else
	     return false;
	}

	function create_index_file(  $shop_url = NULL, $company_id = NULL  )
	{
	   if( !$company_id )
	     return false;

	   $this->load->model('mcp/Mcompanies');
	   $company = $this->Mcompanies->get_company( array( 'id' => $company_id ) );

	   if( !empty($company) )
	   {
	      $company = $company[0];
		  $company_footer_text = $company->company_footer_text;
		  $company_footer_link = $company->company_footer_link;

		  $obs_jquery = 'http://code.jquery.com/jquery-1.8.2.min.js';
		  $obs_script = 'js/obs-script.js.php';
		  $obs_style = 'includes/get-style.css.php';

		  if( !$company_footer_text || !$company_footer_link )
		    return false;

		  // ---- >> Replace Last '/' from the URLs << ---- //

		  if( substr( $shop_url, strlen($shop_url)-1 , 1) == '/' )
		    $shop_url = substr( $shop_url, 0, -1);

		  // ---- >> File Locations << ---- //

		  //--- $org_index_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/index.html';
		  //--- $new_index_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/new-index.html';
		  $org_index_file_loc = dirname(__FILE__).'/../../../online-bestellen/index.html';
		  $new_index_file_loc = dirname(__FILE__).'/../../../online-bestellen/new-index.html';

		  // ---- >> Read Sample Config File << ---- //

		  $rfobj = fopen( $org_index_file_loc, 'r' ) or exit( _('Error - Unable to open the original index file !') );
		  $fdata = fread( $rfobj, filesize($org_index_file_loc) );
		  fclose( $rfobj );

		  // ---- >> Modify - Do settings << ---- //

		  $fdata = str_replace( "COMPANY_FOOTER_TEXT", $company_footer_text, $fdata );
		  $fdata = str_replace( "COMPANY_FOOTER_LINK", $company_footer_link, $fdata );

		  $fdata = str_replace( "OBS_JQUERY", $obs_jquery, $fdata );
		  $fdata = str_replace( "OBS_SCRIPT", $shop_url.'/'.$obs_script, $fdata );

		  $fdata = str_replace( "OBS_STYLE", $shop_url.'/'.$obs_style, $fdata );

		  // ---- >> Create New Config File << ---- //

		  $wfobj = fopen( $new_index_file_loc, 'w' ) or die( _('Error - Cannot create a index file for Company ID').' : '.$company_id );
		  fwrite($wfobj, $fdata);
		  fclose( $wfobj );

		  return true;
	   }
	   else
	     return false;
	}

	function update_client_files_new()
	{
		$msg = '';
		if( $this->input->post('submit') )
		{
			$company_ids = $this->input->post('company_id');

			if( !empty( $company_ids ) )
			{
				$companies_ftp_details = $this->MFtp_settings->get_ftp_settings( array( 'shop_files_loc <>' => '', 'ftp_hostname <>' => '', 'ftp_username <>' => '', 'ftp_password <>' => '', 'access_permission' => 1 ) , array( 'company_id' => $company_ids ) );

				if( !empty($companies_ftp_details) )
				{
					$msg_arr = $this->upgrade_files_new( $companies_ftp_details );

					if( empty($msg_arr) )
						$msg = array('success' => _('Success - FTP files of the selected companies are been updated successfully !'));
					else
						$msg = array('error' => implode(',<br />',$msg_arr) );
				}
				else
					$msg = array('error'=> _('Error - None of the companies have entered their full FTP Details !'));
			}
			else
			{
				$msg = array('error'=> _('Error - Please select some companies to update their client files !'));
			}
		}

		$data['message'] = $msg;
		$data['company_ftp_settings'] = $this->Mcompanies->get_approved_company_ftp_settings_new();

		$data['tempUrl']=$this->tempUrl;
		$data['header'] = $this->template.'/header';
		$data['main'] = $this->template.'/client_list';
		$data['footer'] = $this->template.'/footer';

		$this->load->vars($data);
		$this->load->view($this->template.'/mcp_view');
	}

	function upgrade_files_new( $companies_ftp_details ){
		if( empty($companies_ftp_details) )
			return false;
		else{
			$this->load->library('ftp');

			$error = array();

			foreach( $companies_ftp_details as $comp){
				$shop_files_loc = $comp->shop_files_loc;
				$ftp_hostname = $comp->ftp_hostname;
				$ftp_username = $comp->ftp_username;
				$ftp_password = $comp->ftp_password;
				$shop_url = $comp->shop_url;
				$company_id = $comp->company_id;

				$response_1 = $this->create_config_file_new( $shop_url, $company_id );
				//$response_2 = $this->create_index_file( $shop_url, $company_id );
				$response_2 = true;

				if( !$response_1 || !$response_2 ){
					$error[] = _('Problem on ').$ftp_hostname.' : '._('Can\'t create config/index file for this FTP server !');
				}
				else{
					$updated_file_loc = dirname(__FILE__).'/../../../online-bestellen-new/';

					$org_root_files = array( 'new-config.php' );
					$root_files = array( 'config.php' );

					//$includes_files = array( 'captcha.php', 'frontpage.php', 'functions.php', 'init.php', 'payment.php', 'request.php');
					$includes_files = array( 'init.php');

					//$css_files = array( 'bootstrap.css', 'custom.css', 'flexslider.css','font-awesome.css','ngDialog.css','ngDialog-theme-plain.min.css','responsive.css','style.css','style.min.css');
					$css_files = array( 'custom.css');

					//$js_files = array( 'obs-script.js.php', 'shop-angular.js', 'shop-checkout.js', 'shop-dutch-calender.js', 'shop-function.js');
					$js_files = array('shop-checkout.js');

					//$template_files = array( 'allcategorylist.html', 'cart.html', 'categorylist.html', 'change-password.html', 'checkout.html', 'edit-profile.html', 'forgot-password.html', 'login.html', 'order-detail.html', 'order-history.html', 'productdetail.html', 'product-detail-popup.html', 'productlist.html', 'queries.html', 'redirect.html', 'register.html', 'service.html','shop-password.html');
					$template_files = array('cart.html','categorylist.html','product-detail-popup.html','productdetail.html','productlist.html');

					// --- >> Connect FTP << --- //
					$conn_id = ftp_connect( $ftp_hostname ) or die("Couldn't connect to $ftp_server");

					// --- >> Login FTP << --- //
					if ( @ftp_login($conn_id, $ftp_username, $ftp_password) ){
						if ( @ftp_chdir( $conn_id, $shop_files_loc ) ){
							$dir_arr = array( 'includes', 'shop_css', 'shop_js', 'template' );

							// Getting directories and file names
							$files_in_dir = ftp_nlist($conn_id,".");

							ftp_pasv($conn_id, true);

							// try to create the internal directories on ftp
							/*foreach( $dir_arr as $dir ){
								if(!in_array( $dir , $files_in_dir)){
									if ( @ftp_mkdir($conn_id, $dir) ){
										// DIR created
									}
									else{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('There was a problem while creating ')."'".$dir."'"._(' folder, on server ! May be it was already there.');
									}
								}
							}*/

							// --- >> Start updating ROOT files << --- //
							/*if( !empty($root_files) )
								foreach( $root_files as $id=>$rf ){
									if ( @ftp_put($conn_id, $rf, $updated_file_loc.$org_root_files[$id] , FTP_ASCII) ){
										//@ftp_chmod($conn_id, 0777, $rf);	 // Set Read-Write permisssion  on the uploaded file.
									}
									else{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').$rf._(' on server !');
									}
								}*/

							// --- >> Start updating CSS files << --- //
							/*if( !empty($css_files) )
								foreach( $css_files as $id=>$cf ){
									if ( @ftp_put($conn_id, 'shop_css/'.$cf, $updated_file_loc.'shop_css/'.$css_files[$id] , FTP_ASCII) ){
										//@ftp_chmod($conn_id, 0777, $jf);	 // Set Read-Write permisssion  on the uploaded file.
									}
									else{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'js/'.$cf._(' on server !');
									}
								}*/

							// --- >> Start updating JS files << --- //
						/*	if( !empty($js_files) )
								foreach( $js_files as $id=>$jf ){
									if ( @ftp_put($conn_id, 'shop_js/'.$jf, $updated_file_loc.'shop_js/'.$js_files[$id] , FTP_ASCII) ){
										//@ftp_chmod($conn_id, 0777, $jf);	 // Set Read-Write permisssion  on the uploaded file.
									}
									else{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'js/'.$jf._(' on server !');
									}
								} */

							// --- >> Start updating INCLUDES files << --- //
							if( !empty($includes_files) )
								foreach( $includes_files as $id=>$lf ){
									if ( @ftp_put($conn_id, 'includes/'.$lf, $updated_file_loc.'includes/'.$includes_files[$id] , FTP_ASCII) ){
										//@ftp_chmod($conn_id, 0777, $lf);	 // Set Read-Write permisssion  on the uploaded file.
									}
									else{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'includes/'.$lf._(' on server !');
									}
								}

							/* if( !empty($template_files) )
								foreach( $template_files as $id=>$lf ){
									if ( @ftp_put($conn_id, 'template/'.$lf, $updated_file_loc.'template/'.$template_files[$id] , FTP_ASCII) ){
										//@ftp_chmod($conn_id, 0777, $lf);	 // Set Read-Write permisssion  on the uploaded file.
									}
									else{
										$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t update ').'includes/'.$lf._(' on server !');
									}
								} */
						}
						else{
							$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t change the directory to online-bestellen folder !');
						}
					}
					else{
						$error[] = _('Problem on ').$ftp_hostname.' : '._('Couldn\'t connect as  ').$ftp_username._(', login failed !');
					}
				} // End - If Config
			}

			return $error;
		}
	}

	function create_config_file_new(  $shop_url = NULL, $company_id = NULL  )
	{
		if( !$company_id )
			return false;

		$this->load->model('Mapi');
		$comp_api = $this->Mapi->get_company_api( $company_id );

		if( !empty($comp_api) )
		{
			$api_id = $comp_api->api_id;
			$api_secret = $comp_api->api_secret;

			if( !$api_id || !$api_secret )
				return false;

			// ---- >> Set other URL settings << ---- //

			$api_server = base_url();                                //'http://www.onlinebestelsysteem.net/obs'
			//--- $api_assets = base_url().'obs-api';                      //'http://www.onlinebestelsysteem.net/obs/obs-api'
			//--- $cdn_api_assets = $this->config->item('cdn_api_assets'); //'http://cdn.onlinebestelsysteem.net/obs/obs-api'
			$api_assets = base_url().'obs-api-new';                      //'http://www.onlinebestelsysteem.net/obs/obs-api'
			$cdn_api_assets = $this->config->item('cdn_api_assets').'-new'; //'http://cdn.onlinebestelsysteem.net/obs/obs-api'

			// ---- >> Replace Last '/' from the URLs << ---- //

			if( substr( $shop_url, strlen($shop_url)-1 , 1) == '/' )
				$shop_url = substr( $shop_url, 0, -1);

			// ---- >> Adding www in the URLs << ------------ //
			//$shop_url = str_replace("http://","http://www.",$shop_url);

			if( substr( $api_server, strlen($api_server)-1 , 1) == '/' )
				$api_server = substr( $api_server, 0, -1);

			if( substr( $api_assets, strlen($api_assets)-1 , 1) == '/' )
				$api_assets = substr( $api_assets, 0, -1);

			// ---- >> File Locations << ---- //

			//--- $org_config_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/config.php';
			//--- $new_config_file_loc = dirname(__FILE__).'/../../../new-online-bestellen/new-config.php';
			$org_config_file_loc = dirname(__FILE__).'/../../../online-bestellen-new/config.php';
			$new_config_file_loc = dirname(__FILE__).'/../../../online-bestellen-new/new-config.php';

			// ---- >> Read Sample Config File << ---- //

			$rfobj = fopen( $org_config_file_loc, 'r' ) or exit( _('Error - Unable to open the original config file !') );
			$fdata = fread( $rfobj, filesize($org_config_file_loc) );
			fclose( $rfobj );

			// ---- >> Modify - Do settings << ---- //

			$fdata = str_replace( "'CLIENT_URL'", "'".$shop_url."'", $fdata );
			$fdata = str_replace( "'API_ID'", "'".$api_id."'", $fdata );
			$fdata = str_replace( "'API_SECRET_KEY'", "'".$api_secret."'", $fdata );

			if( $api_server )
				$fdata = str_replace( "'API_SERVER'", "'".$api_server."'", $fdata );

			if( $api_assets )
				$fdata = str_replace( "'API_ASSETS'", "'".$api_assets."'", $fdata );

			if( $api_assets )
				$fdata = str_replace( "'CDN_API_ASSETS'", "'".$cdn_api_assets."'", $fdata );

			// ---- >> Create New Config File << ---- //

			$wfobj = fopen( $new_config_file_loc, 'w' ) or die( _('Error - Cannot create a Config file for Company ID').' : '.$company_id );
			fwrite($wfobj, $fdata);
			fclose( $wfobj );

			return true;
		}
		else
			return false;
	}
}
?>