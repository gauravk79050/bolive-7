<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * MAIL MANAGER
	 * 
	 * This package is for managing SMTP mails
	 * 
	 * @package Mail_manager
	 * @author Shyam Mishra <shyammishra@cedcoss.com>
	 */
	class Mail_manager extends CI_Controller
	{		
		var $template='mcp/';
		
		/**
		 * This is the constructor
		 */
		function __construct()
		{
			parent::__construct();
			
			$this->load->library('email');
			
			$this->load->helper('html');  
			$this->load->helper('url');
			$this->load->helper('directory');
			$this->load->helper('phpmailer');
			
			$this->load->model($this->template.'Mcompanies');
			$this->load->model($this->template.'Mcompany_type');
			$this->load->model($this->template.'Mmail_manager');
			
			$current_user = $this->session->userdata('username');
			$is_logged_in = $this->session->userdata('is_logged_in');
			
			if( !$current_user || !$is_logged_in )
				redirect('mcp/mcplogin','refresh');
		}
		
		/**
		 * This public function is used to show main page of Mail manager
		 */
		function index()
		{	 
			$companies = array();
			
			if($this->input->post('btn_search'))
			{
				if($this->input->post('search_by') == 'id'){
					$companies = $this->Mmail_manager->get_subscription(array('company_id' => $this->input->post('search_keyword')),true);
				}elseif($this->input->post('search_by') == 'subs_type'){
					$companies = $this->Mmail_manager->get_subscription(array('mail_type' => $this->input->post('mail_type')),true);
				}
			}
			else
			{
				$companies = $this->Mmail_manager->get_subscription(array(),true);
			}
			
			if(!empty($companies)){
				foreach ($companies as $key => $company){
					$mail_send_last_month = $this->Mmail_manager->get_mail_send_month_wise(array('company_id' => $company['id']),date("Y-m", strtotime(date("Y-m-d")."-1 months")));
					$companies[$key]['mail_send_last_month'] = count($mail_send_last_month);
					$subscriber = $this->Mmail_manager->get_subscribers(null,$company['company_id'],'all');
					$companies[$key]['subscribers'] = count($subscriber);
				}
			}
			
			$data['content'] = $companies;
			
			$data['header'] = $this->template.'header';
			$data['main'] = $this->template.'mail_manager';
			$data['footer'] = $this->template.'footer';	
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');
		}

		/**
		 * This function is used to change mail type for any company
		 */
		function change_mail_type(){
			$companyId = $this->input->post('company_id');
			$mailType = $this->input->post('mail_type');
			$credits = $this->input->post('credits');
			
			$update_array = array(
				'mail_type' => $mailType,
				'mail_sent_for_current_type' => 0,
				'type_change_date' => date("Y-m-d H:i:s") 
			);
			if($mailType == 'credits'){
				$update_array['credits'] = $credits;
			}
			
			if($this->Mmail_manager->update_mail_manager(array('company_id' => $companyId), $update_array)){
				echo _("Mail type changed successfully");
			}else{
				echo _("Mail type did not changed successfully");
			}
		}
		
		/**
		 * This function is sued to chnage the amount of credits left for any company
		 */
		function change_credits(){
			$companyId = $this->input->post('company_id');
			$credits = $this->input->post('credits');

			$data = $this->Mmail_manager->get_subscription(array('company_id' => $companyId));
			if(!empty($data)){
				$current_credits = $data['0']['credits'];
				$used_credits = $data['0']['mail_sent_for_current_type'];
				$new_credits = ($used_credits + $credits);
				$update_array = array(
					'credits' => $new_credits
				);
				
				if($this->Mmail_manager->update_mail_manager(array('company_id' => $companyId), $update_array)){
					echo _("Credits have been changed");
				}else{
					echo _("Credits have not been changed");
				} 
			}else{
				echo _("This company is not using Mail manager");
			}
		}
		
		/**
		 * This public function is used to fetch Subscribed/Unsubscribes/Bounced companies, Default: Subscribed
		 * @access public
		 * @param string $type Type of company Subscribed OR Unsubscribes OR Bounced
		 */
		// public function companies($type = 'subscribed'){

		// 	if($type == 'subscription_list'){
		// 		$this->output_subscription_list();
		// 		die;
		// 	}
		// 	$data = array();
		// 	$segment_array	= $this->uri->segment_array();
		// 	$key = array_search($type,$segment_array);

		// 	if(isset($segment_array[$key+1]) && is_numeric($segment_array[$key+1]) && $this->input->post('is_subscribed')){
		// 		$select = 'id,company_name,email,mail_subscription';
		// 		$company_id = $segment_array[$key+1];
		// 		$is_subscribed	= ($this->input->post('is_subscribed') == 'subscribed')?'subscribed':'unsubscribed';
		// 		$is_updated		= $this->Mmail_manager->update_company(array('mail_subscription' => $is_subscribed),array('approved' => '1', 'status' => '1','id' => $company_id));
		// 		echo $is_updated?'success':'error';die;
		// 	}

		// 	$select = 'id,company_name,email,mail_subscription';
		// 	$data['count_subscribed'] = $this->Mmail_manager->get_company($select,array('mail_subscription' => 'subscribed','approved' => '1', 'status' => '1'),true);
		// 	$data['count_unsubscribed'] = $this->Mmail_manager->get_company($select,array('mail_subscription' => 'unsubscribed','approved' => '1', 'status' => '1'),true);
		// 	$data['count_bounced'] = $this->Mmail_manager->get_company($select,array('mail_subscription' => 'bounced','approved' => '1', 'status' => '1'),true);
		// 	$data['companies'] = $this->Mmail_manager->get_company($select,array('mail_subscription' => $type,'approved' => '1', 'status' => '1'));
		// 	$data['type'] = $type;

		// 	$data['header'] = $this->template.'header';
		// 	$data['main'] = $this->template.'mail_manager_companies';
		// 	$data['footer'] = $this->template.'footer';
		// 	$this->load->vars($data);
		// 	$this->load->view($this->template.'mcp_view');
		// }
		
		function output_subscription_list(){
			$select = 'id,company_name,email,mail_subscription';
			$Companies = $this->Mmail_manager->get_company( $select, array('approved'=>'1','status'=>'1'),false );

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle( _('Companies Subscription List') );

			$counter = 1;

			$this->excel->getActiveSheet()->setCellValue('A'.$counter, _('Company ID') )->getStyle('A'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B'.$counter, _('Email Address') )->getStyle('B'.$counter)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C'.$counter, _('Subscribed/Unsubscribed') )->getStyle('C'.$counter)->getFont()->setBold(true);

			if( !empty($Companies) )
			{
				$counter = 1;
				//print_r($Companies);
				foreach( $Companies as $C )
				{
					$counter++;
					$this->excel->getActiveSheet()->setCellValue('A'.$counter, $C['id'] );
					$this->excel->getActiveSheet()->setCellValue('B'.$counter, $C['email'] );
					$this->excel->getActiveSheet()->setCellValue('C'.$counter, $C['mail_subscription'] );
					if($C['mail_subscription'] == 'subscribed'){
						$this->cellColor('C'.$counter, '46B525');
					}
					else if($C['mail_subscription'] == 'unsubscribed'){
						$this->cellColor('C'.$counter, 'F28A8C');
					}
				}
			}

			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);

			$datestamp = date("d-m-Y");
			$filename = "Subscription-list-".$datestamp.".xls";

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
			$objWriter->save('php://output');
		}
		
		function cellColor($cells,$color){
			//global $objPHPExcel;
			$this->excel->getActiveSheet()->getStyle($cells)->getFill()
			->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array('rgb' => $color)
			));
		}
		
		/**
		 * This function is used to fetch and return a form to send quick mail..
		 */
		public function get_mail_div($email = null){
			//$data['email'] = $this->input->get("email");
			$data['email'] = urldecode($email);
			$this->load->view("mcp/mail_manager_send_mail",$data);
		}
		
		/**
		 * This function is used to send mail.
		 */
		public function send_quick_mail(){

			/*echo $this->input->post("send_mail_txt");
			die;*/
			$response = array("error"=>1,"message"=>_("Sorry mail did not send successfully"));
			if($this->input->post("send_mail_txt") != '' && $this->input->post('send_mail_sub') != ''){

				$msg = "<html><head></head><body>".$this->input->post("send_mail_txt")."</body></html>";

				$query = send_email($this->input->post('to_id_hidden'),$this->config->item('site_admin_email'),$this->input->post('send_mail_sub'),$msg,NULL,NULL,NULL,'site_admin','subscriber','quick_mail');
				if($query)
					$response = array("error"=>0,"message"=>_("Mail send successfully"));
			}
			echo json_encode($response);
		}
		
		/**
		 * This function is used to show NewsLetters
		 * @access public
		 */
		public function newsletters(){
			$data['newsLetters'] = $this->Mmail_manager->get_newsletters();
			
			$data['header'] = $this->template.'header';
			$data['main'] = $this->template.'mail_manager_newsletter';
			$data['footer'] = $this->template.'footer';
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');
			
		}
		
		/**
		 * This public function is used to show Templates
		 * @access public
		 */
		public function templates(){
			$data['templates'] = $this->Mmail_manager->get_templates();
			
			$data['header'] = $this->template.'header';
			$data['main'] = $this->template.'mail_manager_templates';
			$data['footer'] = $this->template.'footer';
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');
		}
		
		/**
		 * This function is used to let user to create NewsLetters of their own choices..
		 */
		public function create_newsletters($id = null){

			if($id)
				$data['newsLetter'] = $this->Mmail_manager->get_newsletters(array(),array('id' => $id));

			$data['docs']	= $this->Mmail_manager->get_uploaded_docs('0');
			
			$data['templates'] = $this->Mmail_manager->get_templates();
			//$data['templates'] = array_merge($defaultTemplates,$templates);
			$data['header'] = $this->template.'header';
			$data['main'] = $this->template.'mail_manager_create_newsletter';
			$data['footer'] = $this->template.'footer';
			$data['images']	 = $this->Mmail_manager->get_uploaded_images();
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');
		}
		
		/**
		 * This function is used to let user to create Templates of their own choices..
		 */
		public function create_templates($id = null){

			if($id)
				$data['newsLetter'] = $this->Mmail_manager->get_templates(array(),array('id' => $id));
			
			$data['header'] = $this->template.'header';
			$data['main'] = $this->template.'mail_manager_create_template';
			$data['footer'] = $this->template.'footer';
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');
		}
		
		/**
		 * This privcate function is used to create slug for inputed string
		 * @access private
		 * @param string $string This is the string for which slug is developed
		 * @return string $slug_str
		 */
		private function create_slug($string){
			$slug_str = strtolower(trim($string));
			$slug_str = preg_replace("/[^a-z0-9-]/", "-", $slug_str);
			$slug_str = preg_replace("/-+/", "-", $slug_str);
			$slug_str = rtrim($slug_str, "-");
			return $slug_str;
		}
		
		/**
		 * This public function is used to generate preview of the newsletter
		 * @access public
		 */
		public function generate_preview(){
			$datas = $this->input->post('info');

			$response = $this->create_html($datas);

			if($response['error'])
				echo "error";
			else
				echo $response['data'];
		}
		
		
		/**
		 * This private function is used to generate html of the template
		 * @access private
		 * @param  $datas array This is the array of data from which html will be generated
		 * @return array $response This is the array containing html and error flag
		 */
		private function create_html($datas){
			if(count($datas) > 1){
				for ($i = 0; $i < (count($datas)-1); $i++){
					if($datas[$i][2] > $datas[$i+1][2]){
						$temp = $datas[$i];
						$datas[$i] = $datas[$i+1];
						$datas[$i+1] = $temp;
					}
				}
			}
			//usort($datas, create_function('$a, $b','if ($a["2"] == $b["2"]) return 0; return ($a["2"] < $b["2"]) ? -1 : 1;'));
			$response = array('error' => 1, 'data' => _('No Preview'));
			$new_html = '';
			$total_rows = $datas[count($datas)-1][2];

			if(!empty($datas)){
				$new_html .= "<table border='0' cellspacing='0' cellpadding='0' width='100%' >";
				$new_html .= "<tr><td width='20%'></td><td width='20%'></td><td width='20%'></td><td width='20%'></td><td width='20%'></td></tr>";
				for($i = 1; $i <= $total_rows; $i++){
					$new_html .= "<tr id='".$i."'>";
					$td = 1;
					$tr_array = array();
					// --------- getting all values for current row
					foreach ($datas as $k => $v){
						if($v[2] == $i){
							$tr_array[] = $datas[$k];
							unset($datas[$k]);
						}
					}

					// --------- Sorting columns wise
					if(count($tr_array) > 1){
						for ($j = 0; $j < (count($tr_array)-1); $j++){
							if($tr_array[$j][1] > $tr_array[$j+1][1]){
								$temp = $tr_array[$j];
								$tr_array[$j] = $tr_array[$j+1];
								$tr_array[$j+1] = $temp;
							}
						}
					}
					while($td <= 5){
						foreach ($tr_array as $key => $value){
							if($value[1] == $td){
								$new_html .= "<td colspan='".$value[3]."' valign='top' style='height:".(160*$value[4]-20)."px; background-color:".$value[5].";'><div style='background-color:".$value[5]."; color:".$value[6].";'>";
								$widget_content = $value[0];
								/*$widget_content = str_replace("{company_name}",$this->company->company_name,$widget_content);
								$widget_content = str_replace("{first_name}",$this->company->first_name,$widget_content);
								$widget_content = str_replace("{last_name}",$this->company->last_name,$widget_content);
								$widget_content = str_replace("{email}",$this->company->email,$widget_content);
								$widget_content = str_replace("{phone}",$this->company->phone,$widget_content);
								$widget_content = str_replace("{website}",$this->company->website,$widget_content);
								$widget_content = str_replace("{address}",$this->company->address,$widget_content);
								$widget_content = str_replace("{zipcode}",$this->company->zipcode,$widget_content);
								$widget_content = str_replace("{city}",$this->company->city,$widget_content);
								$widget_content = str_replace("{unsubscribe}","<a href='".$this->config->item('portal_url')."registreren/unsubscribe'>"._("Unsubscribe")."</a>",$widget_content);*/
								$new_html .= $widget_content;
								$new_html .= "</div></td>";
								$td = $td+$value[3];
							}else{
								$new_html .= "<td colspan='1'>&nbsp;</td>";
								$td++;
							}
						}
						$td = 6;
					}
					$new_html .= "</tr><tr><td style='height:20px;'></td></tr>";
				}
				$new_html .= "</table>";
				$new_html .= '<br />';
				/*$new_html .= '<p>';
				$new_html .= $this->company->company_name.'<br />';
				$new_html .= $this->company->address.'<br />';
				$new_html .= $this->company->zipcode.' '.$this->company->city.'<br />';
				$new_html .= _("Email").': '.$this->company->email.'<br />';
				$new_html .= _("Tel").': '.$this->company->phone;
				$new_html .= '</p>';*/
				$response = array('error' => 0, 'data' => $new_html);
			}

			return $response;
		}
		
		/**
		 * This function is used to save newsletter, It accepts an post request and return back the result.
		 * @access public
		 */
		public function save_newsletter(){
			
			$response = array('error' => 1, 'data' => _("Newsletter cannot be saved. Please try again..."));
			if($this->input->post('title') && $this->input->post('info')){
				$content = $this->input->post('info');
				if($content){
					if($this->input->post('ns_id') != '' && is_numeric($this->input->post('ns_id'))){
						$update_array = array(
							'content' => addslashes($content),
							'name' => addslashes($this->input->post('title')),
							'attachment' => $this->input->post('attachment'),
						);
						$this->db->where('id', $this->input->post('ns_id'));
						$this->db->update('newsletters_mcp',$update_array);
						$response = array('error' => 0, 'data' => _("Newsletter has been updated successfully."));
					}else{
						$insert_array = array(
							'content' => addslashes($content),
							'name' => addslashes($this->input->post('title')),
							'template_id' => $this->input->post('template_id'),
							'from' => addslashes($this->input->post('from')),
							'attachment' => $this->input->post('attachment'),
							'date' => date("Y-m-d H:i:s")
						);
						if($this->Mmail_manager->save_newsletters($insert_array)){
							$response = array('error' => 0, 'data' => _("Newsletter has been saved successfully."));
						}
					}
				}
			}
			echo json_encode($response);
		}
		
		/**
		 * This public function is used to delete NewsLetter
		 * @access public
		 */
		public function delete_new_letter($id = null){
			if($id && is_numeric($id)){
				$this->Mmail_manager->delete_newletter_mcp($id);
			}
			redirect(base_url().'mcp/mail_manager/newsletters');
		}
		
		/**
		 * This public function is for loading a view that will show in popup.
		 * @see ajax_image_upload()
		 */
		function ajax_img_upload(){
			$this->load->view('mcp/mail_manager_image_upload_ajax');
		}
		
		/**
		 * This function is used for Uploading image via ajax
		 * @return string An error message or an HTML of containing image and some information
		 */
		function ajax_image_upload(){

			$path = dirname(__FILE__).'/../../../assets/images/mail_manager/';
			$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "JPG", "PNG", "GIF");
			if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
			{
				$name = $_FILES['photoimg']['name'];
				$size = $_FILES['photoimg']['size'];

				if(strlen($name))
				{
					$txt = '';
					$ext = '';
					$parts = explode(".", $name);
					if(is_array($parts) && isset($parts['0']) && isset($parts['1']))
						list($txt, $ext) = explode(".", $name);

					if(in_array($ext,$valid_formats))
					{
						if($size<(4 * 1048576))
						{
							$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
							$tmp = $_FILES['photoimg']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name))
							{
								$size = getimagesize($path.$actual_image_name);
								$maxWidth = 895;
								//$maxHeight = 600;
								if ($size[0] > $maxWidth)
								{
									// Find maximum height
									$maxHeight=($size[1]/$size[0])*$maxWidth;

									$this->load->library('image_lib');

									$config['image_library'] = 'gd2';
									$config['source_image'] = $path.$actual_image_name;
									$config['maintain_ratio'] = TRUE;
									//$config['new_image'] = 'resized_'.$actual_image_name;
									//$config['overwrite'] = TRUE;
									$config['quality'] = 100;
									$config['width'] = $maxWidth;
									$config['height'] = $maxHeight;

									$this->load->library('image_lib', $config);

									$this->image_lib->clear();
									$this->image_lib->initialize($config);

									if(! $this->image_lib->resize()){
										echo $this->image_lib->display_errors();
										exit;
									}

								}
								echo '<img src="'.base_url()."assets/images/mail_manager/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" />
								<input type="hidden" name="image_name" id="image_name" value="'.$actual_image_name.'" /><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="crop();" /></div>';
							}
							else
								echo "<span style='color: red'>"._('Image did not uploaded. Please try again')."</span>";
						}
						else
							echo "<span style='color: red'>"._('Image file size max 4 MB')."</span>";
					}
					else
						echo "<span style='color: red'>"._('Invalid file format..')."</span>";
				}
				else
					echo "<span style='color: red'>"._('Invalid file format..')."</span>";

				exit;
			}
		}
		
		/**
		 * This function is user for copping image via ajax
		 * @return string HTML of cropped image.
		 */
		function crop_image(){
			$image = $this->input->post('image_name');

			$targ_w = $this->input->post('w');
			$targ_h = $this->input->post('h');
			$jpeg_quality = 90;
			$tmpp = explode(".",$image);
			$extension = end($tmpp);

			$src = dirname(__FILE__).'/../../../assets/images/mail_manager/'.$image;
			if($extension == "png" || $extension == "PNG")
				$img_r = imagecreatefrompng($src);
			if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG")
				$img_r = imagecreatefromjpeg($src);
			if($extension == "gif" || $extension == "GIF")
				$img_r = imagecreatefromgif($src);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			imagecopyresampled($dst_r,$img_r,0,0,$this->input->post('x'),$this->input->post('y'),
				$targ_w,$targ_h,$this->input->post('w'),$this->input->post('h'));

			//header('Content-type: image/jpeg');
			imagejpeg($dst_r,dirname(__FILE__).'/../../../assets/images/mail_manager/cropped_'.$image,$jpeg_quality);

			// Resizing image to  270*270
			$size = getimagesize(dirname(__FILE__).'/../../../assets/images/mail_manager/cropped_'.$image);

			unlink($src); 
			$new_html = "";
			$new_html .= "<img src='".base_url()."assets/images/mail_manager/cropped_".$image."' alt='"._("Sorry !!! image has not been cropped. Please try again")."'/> <input type='hidden' name='image_name' id='image_name' value='cropped_".$image."' />";
			$new_html .= '<input type="button" id="add_w_img" name="add_w_img" value="'._("Add Widget").'" onclick="add_widget_img_new();" />';
			echo $new_html;

		}
		
		/**
		 * This function is user for cropping upload center image via ajax
		 * @return string HTML of cropped image.
		 */
		function crop_upload_center_image(){
			$image = $this->input->post('image_name');
			if (substr($image, 0, strlen('temp_')) == 'temp_') {
				$actual_image_name = substr($image, strlen('temp_'));
			}

			//die($actual_image_name);
			$targ_w = $this->input->post('w');
			$targ_h = $this->input->post('h');
			$jpeg_quality = 90;
			$tmpp = explode(".",$image);
			$extension = end($tmpp);

			$src = dirname(__FILE__).'/../../../assets/upload_center/images/'.$image;
			if($extension == "png" || $extension == "PNG")
				$img_r = imagecreatefrompng($src);
			if($extension == "jpg" || $extension == "jpeg" || $extension == "JPG")
				$img_r = imagecreatefromjpeg($src);
			if($extension == "gif" || $extension == "GIF")
				$img_r = imagecreatefromgif($src);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			imagecopyresampled($dst_r,$img_r,0,0,$this->input->post('x'),$this->input->post('y'),
				$targ_w,$targ_h,$this->input->post('w'),$this->input->post('h'));

			//header('Content-type: image/jpeg');
			imagejpeg($dst_r,dirname(__FILE__).'/../../../assets/upload_center/images/'.$actual_image_name,$jpeg_quality);

			// Resizing image to  270*270
			$size = getimagesize(dirname(__FILE__).'/../../../assets/upload_center/images/'.$actual_image_name);

			unlink($src);
			
			$new_html = "";
			//$new_html .= "<img src='".base_url()."assets/images/upload_center/cropped_".$image."' alt='"._("Sorry !!! image has not been cropped. Please try again")."'/> <input type='hidden' name='image_name' id='image_name' value='cropped_".$image."' />";
			$new_html .= '<div class="image_holder" image_name='.$actual_image_name.'><img src="'.base_url().'assets/upload_center/images/'.$actual_image_name .'" alt="image_" width="150px" height="150px" style="padding:4px;" /><a href="javascript:void(0);" class="remove_me"><img alt="remove" src="'.base_url().'assets/cp/images/delete-2.png"></a></div>';
			echo $new_html;
			$result = $this->Mmail_manager->insert_image_details($actual_image_name);
		}
		
		
		/**
		 * This public function is for loading a view that will show in popup.
		 * @see ajax_image_upload()
		 */
		function ajax_file_upload_view(){
			$this->load->view('mcp/mail_manager_file_upload_ajax');
		}
		
		/**
		 * This function is used for Uploading image via ajax
		 * @return string An error message or an HTML of containing image and some information
		 */
		function ajax_file_upload(){

			$path = dirname(__FILE__).'/../../../assets/images/mail_manager/';
			$valid_formats = array("pdf", "doc", "docx", "PDF", "DOC", "DOCX");

			if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
			{
				$name = $_FILES['photoimg']['name'];
				$size = $_FILES['photoimg']['size'];

				if(strlen($name))
				{
					$txt = '';
					$ext = '';
					$parts = explode(".", $name);
					if(is_array($parts) && isset($parts['0']) && isset($parts['1']))
						list($txt, $ext) = explode(".", $name);

					if(in_array($ext,$valid_formats))
					{
						//$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
						$actual_image_name = $name;
						$tmp = $_FILES['photoimg']['tmp_name'];
						if(move_uploaded_file($tmp, $path.$actual_image_name))
						{
							//echo '<span style="color: #3C763D">'._('File uploaded successfully.').'</span>
							echo '<a href="'.base_url().'assets/images/mail_manager/'.addslashes($actual_image_name).'" target="_blank">'.addslashes($actual_image_name).'</a>'.'<input type="hidden" id="a_name" name="a_name" value="'.addslashes($actual_image_name).'" />
							<a href="javascript:void(0);" onclick="remove_attachment();"><img src="'.base_url().'assets/cp/images/delete-2.png" style="vertical-align:middle;"></a>';
						}
						else
							echo "<span style='color: red'>"._('File did not uploaded. Please try again')."</span>";
					}
					else
						echo "<span style='color: red'>"._('Invalid file format..')."</span>";
				}
				else
					echo "<span style='color: red'>"._('Invalid file format..')."</span>";

				exit;
			}
		}
		
		/**
		 * This public function is send newsletter to company itself
		 */
		public function send_newsletter_me(){
			$newsLetterId = $this->input->post("id");
			$newsLetter = $this->Mmail_manager->get_newsletters(array(),array('id' => $newsLetterId));

			if(!empty($newsLetter)){
				$this->load->library('utilities');
				$config = Array(
						/*'protocol' => 'smtp',
						'smtp_host' => 'serv01.sitematic.be',
						'smtp_port' => '465',
						'smtp_user' => 'noreply@onlinebestelsysteem.net',
						'smtp_pass' => '665544',*/
						'mailtype'  => 'html'
					);
				
				$this->email->initialize($config);
				
				$mail_template = stripslashes($newsLetter['0']['content']);
				/*$response = $datas;
				
				$mail_template = '<html><head><style type="text/css">p { margin: 0; }</style></head><body><div style="width:760px; margin: 20px auto;">';
				$mail_template .= $response['data'];
				$mail_template .= '</div></body></html>';*/
				
				$select = 'id,company_name,username,password,first_name,last_name,email,phone,website,address,zipcode,city';
				$subscriber_array = $this->Mmail_manager->get_company($select,array('company.id' => 87));
				
				$isSend = false;
				//echo "asdas"; die;
				if(!empty($subscriber_array))
					$isSend = $this->send_newsletter_to_subscriber($subscriber_array[0],$mail_template,$newsLetter,true);
				
				if($isSend)
					echo _("Mail has been send successfully");
				else
					echo _("Sorry!!! Mail has not been send successfully. Please try again");
			}else{
				echo _("Sorry!!! Cant found the template.");
			}

		}
		
		/**
		 * This public function is used to send newsletter to all its subscriber
		 */
		public function send_newsletter_all(){
			$this->load->library('utilities');
			//$this->load->helper('phpmailer');
			
			set_time_limit ( 0 );
			if($this->input->post('id') && $this->input->post('types')){
				
				$this->load->helper('mailsmtp');
				$config = mail_config();
				$this->email->initialize($config);
				
				$newsLetterId = $this->input->post("id");
				$companyTypes = $this->input->post('types');
				$newsLetter = $this->Mmail_manager->get_newsletters(array(),array('id'=>$newsLetterId));
				$datas = json_decode($newsLetter['0']['content'],true);
				$response = $this->create_html($datas);
				$mail_template = '<html><head><style type="text/css">p { margin: 0; }</style></head><body><div style="width:760px; margin: 20px auto;">';
				$mail_template .= $response['data'];
				$mail_template .= '</div></body></html>';
				
				$subscribers = array();
				
				$select = 'id,company_name,username,password,first_name,last_name,email,phone,website,address,zipcode,city';
				if(!empty($companyTypes)){
					foreach($companyTypes as $ac_type){
						$companies = $this->Mmail_manager->get_company($select,array('ac_type_id' => $ac_type,'approved' => '1', 'status' => '1','mail_subscription' => 'subscribed'));
						$subscribers = array_merge($subscribers,$companies);
					}
				}
				
				//$email_list = array();
				$count = 0;
				$fail_count = 0;
				if(!empty($subscribers)){
					foreach ($subscribers as $subscriber){
						if($this->send_newsletter_to_subscriber($subscriber,$mail_template,$newsLetter)){
							++$count;
						}
						else{
							++$fail_count;
						}
					}
				}
				if($count && !$fail_count){
					echo _("All Mails have been send successfully");
				}else{
					echo _("Sorry!!! Some mails have not been send successfully. Please try again");
				}
			}else{
				echo _("Sorry!!! Some mails have not been send successfully. please try again");
				die();
			}
		}
		
		/**
		 * This public function is used to copy template
		 */
		public function newsletter_clone($newsLetterId = null){

			if($newsLetterId){
				$newsLetterInfo = $this->Mmail_manager->get_newsletters(array(),array('id'=>$newsLetterId));
				if(!empty($newsLetterInfo)){
					$insert_array = array(
						'content' => $newsLetterInfo['0']['content'],
						'name' => 'clone_'.$newsLetterInfo['0']['name'],
						'date' => date("Y-m-d H:i:s")
					);
					$this->Mmail_manager->save_newsletters($insert_array);
				}
			}

			redirect(base_url().'mcp/mail_manager/newsletters');
		}
		
		/**
		 * This function is used to delete Templates
		 */
		public function delete_template($id = null){
			if($id && is_numeric($id)){
				$template = $this->Mmail_manager->get_templates(array(),array( 'id' => $id ));
				if(!empty($template)){
					$this->Mmail_manager->delete_templates(array( 'id' => $id ));
				}
			}

			redirect(base_url().'mcp/mail_manager/templates');
		}
		
		/**
		 * This function is used to save templates, It accepts an post request and return back the result.
		 * @access public
		 */
		public function save_templates(){
			$response = array('error' => 1, 'data' => _("Template cannot be saved. Please try again..."));
			if($this->input->post('title') && $this->input->post('info')){
				$content = $this->input->post('info');
				if(!empty($content)){
					/*foreach ($content as $k => $v){
					 $content[$k][0] = mysql_real_escape_string($content[$k][0]);
					}*/
					if($this->input->post('ns_id') != '' && is_numeric($this->input->post('ns_id'))){
						$this->db->where('id', $this->input->post('ns_id'));
						$this->db->update('mail_templates_mcp',array('content' => json_encode($content),'name' => addslashes($this->input->post('title'))));
						$response = array('error' => 0, 'data' => _("Templates has been updated successfully."));
					}else{
						$insert_array = array(
							'content' => json_encode($content),
							'name' => addslashes($this->input->post('title')),
							'date' => date("Y-m-d H:i:s")
						);
						if($this->Mmail_manager->save_templates($insert_array)){
							$response = array('error' => 0, 'data' => _("Templates has been saved successfully."));
						}
					}
				}
			}
			echo json_encode($response);
		}
		
		/**
		 * This public function is used to select template while creating newsletter
		 * @param Integer $templateId ID of the template that is being loaded
		 * @return array $response Array of response with error indicator and data
		 */
		public function get_template($templateId = 0){
			$response = array('error' => 1, 'data' => '');
			if($templateId){
				$template = $this->Mmail_manager->get_templates(array(), array('id' => $templateId));
				if(!empty($template)){
					$content = json_decode($template['0']['content'],true);
					$counter = 1;
					$widgetArray = array();
					if(!empty($content)){
						foreach ($content as $value){
							$newHtml = '';
	            			//$newHtml .= '<li id="w_t_'.$counter.'" class="new" data-row="'.$value['2'].'" data-col="'.$value['1'].'" data-sizex="'.$value['3'].'" data-sizey="'.$value['4'].'">';
							$newHtml .= '<li id="w_t_'.$counter.'" class="new" data-row="'.$value['2'].'" data-col="'.$value['1'].'">';
							$newHtml .= '	<span class="action">';
							$newHtml .= '		<a href="javascript: void(0);" class="edit_me" ><img src="'.base_url().'assets/cp/images/edit.gif" alt="'._("Edit").'" width="50px" /></a>';
							$newHtml .= '		<a href="javascript: void(0);" class="remove_me" ><img src="'.base_url().'assets/cp/images/delete-2.png" alt="'._("remove").'" /></a>';
							$newHtml .= '	</span>';
							$newHtml .= '	<div rel="'.$counter.'" class="widgetContent" style="background-color: '.$value['5'].'; color: '.$value['6'].'">'.stripslashes($value['0']).'</div>';
							$newHtml .= '</li>';
							$widgetArray[] = array($newHtml,$value['3'],$value['4']);  
							$counter++;
						}
					}
					$response = array('error' => 0, 'data' => $widgetArray);
				}
			}
			echo json_encode($response);
		}
		
		/**
		 * @access public
		 * This public function is used to upload images which can be used later while creating newsletter
		 */
		public function image_manager(){
			//$path = dirname(__FILE__).'/../../../assets/images/upload_center/';
			$path = dirname(__FILE__).'/../../../assets/upload_center/images/';
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "JPG", "PNG", "GIF");
			if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
			{
				$name = $_FILES['photoimg']['name'];
				$size = $_FILES['photoimg']['size'];

				if(strlen($name))
				{
					$txt = '';
					$ext = '';
					$parts = explode(".", $name);
					if(is_array($parts) && isset($parts['0']) && isset($parts['1']))
						list($txt, $ext) = explode(".", $name);

					if(in_array($ext,$valid_formats))
					{
						if($size<(4 * 1048576))
						{
	    					//$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
							$actual_image_name = $txt.".".$ext;

							$new_image_name = $path.$actual_image_name;
							$count = 0;
							while(file_exists($new_image_name)){
								$new_image_name = $path.$txt.'_'.++$count.".".$ext;
							}
							$actual_image_name = $count?$txt.'_'.$count.".".$ext:$actual_image_name;
							$actual_image_name = 'temp_'.$actual_image_name;

							$tmp = $_FILES['photoimg']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name))
							{
								$size = getimagesize($path.$actual_image_name);
								$maxWidth = 895;
	    						//$maxHeight = 600;
								if ($size[0] > $maxWidth)
								{
	    							//echo "shyam"; die;
	    							// Find maximum height
									$maxHeight=($size[1]/$size[0])*$maxWidth;

									$this->load->library('image_lib');

									$config['image_library'] = 'gd2';
									$config['source_image'] = $path.$actual_image_name;
									$config['maintain_ratio'] = TRUE;
	    							//$config['new_image'] = 'resized_'.$actual_image_name;
	    							//$config['overwrite'] = TRUE;
									$config['quality'] = 100;
									$config['width'] = $maxWidth;
									$config['height'] = $maxHeight;

									$this->load->library('image_lib', $config);

									$this->image_lib->clear();
									$this->image_lib->initialize($config);

									if(! $this->image_lib->resize()){
										echo $this->image_lib->display_errors();
										exit;
									}

								}
	    						//echo $this->db->last_query();
	    						//echo '<div class="image_holder" image_name='.$actual_image_name.'><img src="'.base_url()."assets/images/upload_center/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" width="150px" height="150px" style="padding:5px;" />
								//				</div>';
								echo '<img src="'.base_url()."assets/upload_center/images/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" />
								<input type="hidden" name="image_name" id="image_name" value="'.$actual_image_name.'" /><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="crop();" /></div>';
							}
							else
								echo "<span style='color: red'>"._('Image did not uploaded. Please try again')."</span>";
						}
						else
							echo "<span style='color: red'>"._('Image file size max 4 MB')."</span>";
					}
					else
						echo "<span style='color: red'>"._('Invalid file format..')."</span>";
				}
				else
					echo "<span style='color: red'>"._('Invalid file format..')."</span>";

				exit;
			}
			else{

				$data['images'] = $this->Mmail_manager->get_uploaded_images();
				$data['header'] = $this->template.'header';
				$data['main'] = $this->template.'mail_manager_upload_center';
				$data['footer'] = $this->template.'footer';
				$this->load->view($this->template.'mcp_view',$data);
			}
		}
		
		function delete_image(){
			$path = dirname(__FILE__).'/../../../assets/upload_center/images/';
			if($this->input->post('action') == 'delete' && $this->input->post('image_name')){
				$this->db->where('image_name', $this->input->post('image_name'));
				$this->db->delete('upload_center_details');
				$image_path = $path.$this->input->post('image_name');
				if(file_exists($image_path)){
					if(unlink($image_path)){
						echo json_encode(array('success' => 'Image deleted'));
					}
					else{
						echo json_encode(array('error' => 'Could not delete.'));;
					}
				}
				else{
					echo json_encode(array('error' => 'Image does not exist.'));
				}
			}
		}
		
		/**
		 * @access public
		 * This public function is used to delete attachments of newsletters
		 */
		function remove_attachment(){
			$this->db->select('attachment');
			$this->db->where('id', $this->input->post('ns_id'));
			$request_data = $this->db->get('newsletters_mcp')->row_array();

			if(isset($request_data['attachment']) && $request_data['attachment'] != ''){
				$path = dirname(__FILE__).'/../../../assets/images/mail_manager/';

				$image_path = $path.$request_data['attachment'];

				if(file_exists($image_path)){
					if(unlink($image_path)){
						echo json_encode(array('success' => 'Attachment removed.'));
					}
					else{
						echo json_encode(array('error' => 'Could not delete.'));;
					}
				}
				else{
					echo json_encode(array('error' => 'Attachment does not exist.'));
				}
			}
			else{
				echo json_encode(array('success' => 'File is unlinked.'));
			}

			$this->db->where('id', $this->input->post('ns_id'));
			$this->db->update('newsletters_mcp',array('attachment' => ''));
		}
		
		/**
		 * @access public
		 * This public function is used to upload Docs and pdfs which can be attached later while creating newsletter
		 */
		public function doc_manager(){
			$path = dirname(__FILE__).'/../../../assets/upload_center/docs/';
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			$valid_formats = array("doc", "pdf", "docx", "DOC", "PDF", "DOCX");
			if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
			{
				$name = $_FILES['docs']['name'];
				$size = $_FILES['docs']['size'];

				if(strlen($name))
				{
					$txt = '';
					$ext = '';
					$parts = explode(".", $name);
					if(is_array($parts) && isset($parts['0']) && isset($parts['1']))
						list($txt, $ext) = explode(".", $name);

					if(in_array($ext,$valid_formats) && $size<(4 * 1048576))
					{
						//$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
						$actual_doc_name = $txt.".".$ext;

						$new_doc_name = $path.$actual_doc_name;
						$count = 0;
						while(file_exists($new_doc_name)){
							$new_doc_name = $path.$txt.'_'.++$count.".".$ext;
						}
						$actual_doc_name = $count?$txt.'_'.$count.".".$ext:$actual_doc_name;
						//$actual_doc_name = 'temp_'.$actual_doc_name;

						$tmp = $_FILES['docs']['tmp_name'];
						//print_r($new_doc_name);
						//print_r($actual_doc_name);
						if(move_uploaded_file($tmp, $new_doc_name))
						{
							$size;
							$maxWidth = 895;
							//$maxHeight = 600;
							if ($size[0] > $maxWidth)
							{
								//echo "shyam"; die;
								// Find maximum height
								$maxHeight=($size[1]/$size[0])*$maxWidth;

							}
							//echo '<div class="image_holder" image_name='.$actual_image_name.'><img src="'.base_url()."assets/images/upload_center/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" width="150px" height="150px" style="padding:4px;" />
							//			</div><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="crop();" /></div>';
							$insert_array = array(
								'company_id' => '0',
								'doc_name' => $actual_doc_name,
							);
							if($this->Mmail_manager->save_uploaded_doc($insert_array)){
								//$response = array('error' => 0, 'data' => _("Doc. uploaded successfully."));
								$this->session->set_flashdata('success' , _("Doc. uploaded successfully."));//$actual_doc_name
							}
							else{
								$this->session->set_flashdata('error' , _("Doc with this name is already present in the upload center."));
							}
						}
						else
							$this->session->set_flashdata('error' , _("Doc did not uploaded. Please try again."));
					}
					else{
						if(in_array($ext,$valid_formats)){
							$this->session->set_flashdata('error' , _("Doc file size max 4 MB."));
							//echo "<span style='color: red'>"._('Doc file size max 4 MB')."</span>";
						}
						else{
							$this->session->set_flashdata('error' , _("Invalid file format..."));
							//echo "<span style='color: red'>"._('Invalid file format..')."</span>";
						}
					}
				}
				else
					$this->session->set_flashdata('error' , _("Invalid file format..."));
					//echo "<span style='color: red'>"._('Invalid file format..')."</span>";

				//exit;
				redirect(current_url());
			}
			else{
				//$data['content'] = $this->template.'doc_upload_center';
				$data['header'] = $this->template.'header';
				$data['main'] = $this->template.'doc_upload_center';
				$data['footer'] = $this->template.'footer';
				//$data['images']  = $this->Mmail_manager->get_uploaded_images($this->company_id);
				$data['docs']	= $this->Mmail_manager->get_uploaded_docs();
				$this->load->view($this->template.'mcp_view',$data);
			}
		}
		
		/**
		 * @access public
		 * This public function is used to delete attachment docs of upload center
		 */
		function delete_doc(){
			$path = dirname(__FILE__).'/../../../assets/upload_center/docs/';
			$doc_id = $this->input->post('doc_rel');
			if($doc_id){
				$this->db->where('id', $doc_id);
				$result = $this->db->get('upload_center_docs')->result();
				if(count($result)){
					$doc_path = $path.$result[0]->doc_name;
					$this->db->where('id', $doc_id);
					$this->db->delete('upload_center_docs');
					if(file_exists($doc_path)){
						if(unlink($doc_path)){
							echo json_encode(array('success' => 'Doc deleted'));
						}
						else{
							echo json_encode(array('error' => 'Could not delete.'));;
						}
					}
					else{
						echo json_encode(array('error' => 'Doc does not exist.'));
					}
				}
			}
		}
		
		/**
		 * @access public
		 * This public function is used to show all images in a popup uploaded in the image manager
		 */
		function image_manager_popup(){
			$data['images']	 = $this->Mmail_manager->get_uploaded_images();
			$this->load->view('mcp/image_manager_popup',$data);
		}
		
		/**
		 * This function generates a unique md5 string
		 */
		private function generate_unique_md5_string(){
			$this->load->helper('string');
			return random_string('unique');
		}
		
		/**
		 * Send newsletter to any subscriber 
		 * 
		 */
		private function send_newsletter_to_subscriber($subscriber = array(),$mail_template = NULL,$newsLetter = array(),$newsletter_me = false){
			$response = false;
			if(!empty($subscriber) && $mail_template && !empty($newsLetter) && $subscriber['email'] != ''){

				/* -------------------------------------Get direct login/unsubscribe link -------------------------------------*/
				$login_url = base_url().'cp/login/validate?username='.$subscriber['username'].'&direct_login='.md5($subscriber['password']);
				$unsubscribe_url = $this->config->item('portal_url').'unsubscribe?username='.$subscriber['username'].'&subscriber_id='.md5($subscriber['password']);
				$direct_login_link = "<a href='".$login_url ."'>".$login_url."</a>";
				$unsubscribe_link = "<a href='".$unsubscribe_url ."'>"._('unsubscribe')."</a>";
				/*--------------------------------------End direct login/unsubscribe link -------------------------------------*/

				//mail template parse array
				$data = array(
					'company_name'		=> $subscriber['company_name'],
					'username'				=> $subscriber['username'],
					'first_name'				=> $subscriber['first_name'],
					'last_name'				=> $subscriber['last_name'],
					'email'						=> $subscriber['email'],
					'phone'					=> $subscriber['phone'],
					'website'					=> $subscriber['website'],
					'address'					=> $subscriber['address'],
					'zipcode'					=> $subscriber['zipcode'],
					'city'							=> $subscriber['city'],
					'direct_link'				=> $direct_login_link,
					'unsubscribe_link'	=> $unsubscribe_link
				);
				
				//email variables
				$toEmail 	= $subscriber['email'];
				$mail_body 	= $this->utilities->parseMailText($mail_template, $data);
				$sender		= $this->config->item('site_admin_email');
				$recipient	= $subscriber['email'];
				$subject	= _("News Letter: ").$newsLetter['0']['name'];
				$message	= $mail_body; 

				if( $newsletter_me ) {
					$recipient = $this->config->item('site_admin_email');
				}
				//email details
				// $this->email->clear(TRUE);
				// $this->email->from($sender);
				// if($newsletter_me)
				// 	$this->email->to($this->config->item('site_admin_email'));
				// else 
				// 	$this->email->to($recipient);
				// //$this->email->to("shyammishra@cedcoss.com");
				// $this->email->subject($subject);
				// $this->email->message($mail_body);
				
				//attachment
				$path = dirname(__FILE__).'/../../../assets/upload_center/docs/';
				$attachment = NULL;
				$attachment_path = NULL;
				if($newsLetter['0']['attachment'] != null && file_exists($path.$newsLetter['0']['attachment'])){
					// $this->email->attach($path.$newsLetter['0']['attachment']);
					$attachment = $newsLetter['0']['attachment'];
				}
				
				//log data
				$log_data = array(
					'email_from'			=> $sender,
					'email_to'					=> $recipient,
					'from_type'				=> 'site_admin',
					'to_type'					=> 'company',
					'email_type'				=> $newsletter_me?'newsletter_me':'newsletter_to_company',
					'subject'					=> $subject,
					'message'				=> $message,
					'datetime'				=> date('Y-m-d H:i:s',time()),
					'attachment	'			=> isset($attachment)?$attachment:'',
					'company_rel_id'		=> $subscriber['id'],
					'client_rel_id'			=> NULL,
					'newsletter_rel_id'	=> $newsLetter['0']['id']
				);
				$isSend = send_email( $recipient, $sender, $subject, $message, NULL, $attachment );
				// $isSend = $this->email->send();
				if($isSend){
					$this->db->insert('email_logs_newsletter',$log_data);
					$response = true;
				}
			}
			return $response;
		}

		public function companies(){
			$data = array();
			$data['header'] 		= $this->template.'header';
			$data['main']	 		= $this->template.'mail_chimp_companies';
			$data['footer'] 		= $this->template.'footer';
			$data['apiKey'] 		= $this->config->item('apiKey');
			$data['healthListId_fr'] 	= $this->config->item('healthListId_fr');
			$data['healthListId_dch'] 	= $this->config->item('healthListId_dch');
			$data['retailListId_fr'] 	= $this->config->item('retailListId_fr');
			$data['retailListId_dch'] 	= $this->config->item('retailListId_dch');
			$this->load->vars($data);
			$this->load->view($this->template.'mcp_view');
		}

        public function get_mailchimp_email( $apiKey, $listId, $count, $status){
        	$dc 	= substr($apiKey,strpos($apiKey,'-')+1);
        	$emails = array();
			for( $offset = 0; $offset < $count; $offset += 250 ){

				$data = array(
					'offset' => $offset,
					'count'  => 250,
					'status' => $status
				);

				$url = 'https://'.$dc.'.api.mailchimp.com/3.0/lists/'.$listId.'/members';
				$body = json_decode( $this->mailchimp_curl_connect( $url, 'GET', $apiKey, $data ) );
				
				foreach ( $body->members as $member ) {
					$emails[] = $member->email_address;
				}
			}
			return $emails;
        }

		public function chimp_mail_api($listId, $list_type,$lang_id){
			$apiKey = $this->config->item('apiKey');
			$dc = substr($apiKey,strpos($apiKey,'-')+1);
			$url = 'https://'.$dc.'.api.mailchimp.com/3.0/lists/'.$listId;
			$body = json_decode( $this->mailchimp_curl_connect( $url, 'GET', $apiKey) );
			
			$unsubscribed_count 	= $body->stats->unsubscribe_count;
			$subscribed_count 		= $body->stats->member_count;
			$unsubscribed_emails 	= $this->get_mailchimp_email($apiKey, $listId, $unsubscribed_count, 'unsubscribed');
			$subscribed_emails     	= $this->get_mailchimp_email($apiKey, $listId, $subscribed_count, 'subscribed');

			$select = 'company.id,first_name,last_name,email,type_id,language_id,ac_type_id';
			$this->db->select($select);
			$this->db->join( 'general_settings', 'general_settings.company_id = company.id' );
			$this->db->where(array('approved' => '1', 'company.status' => '1' ));
			$this->db->where_in('ac_type_id', array( 4, 5, 6, 7) );
			if( $lang_id == 2 ) {
				$this->db->where_in('language_id', array( 2, 5) );
			} else {
				$this->db->where('language_id', $lang_id);
			}
			$this->db->group_start();
			if($list_type == 'healthcare'){
				$this->db->like('type_id', '20');
				$this->db->or_like('type_id', '27');
				$this->db->or_like('type_id', '28');
			}else if($list_type == 'retail'){
				$this->db->not_like('type_id', '20');
				$this->db->not_like('type_id', '27');
				$this->db->not_like('type_id', '28');
			}
			$this->db->group_end();
			$companies = $this->db->get('company')->result_array();
			$db_emails = array_column( $companies, 'email');
			$email_to_unsubscribe = array_diff($subscribed_emails, $db_emails);
			$email_to_unsubscribe = array_values($email_to_unsubscribe);

			$final_arr = array();
			foreach ($email_to_unsubscribe as $key => $val_email) {
			 	$has_mail = md5( strtolower( $val_email ));
				$data = array(
					"status"  => "unsubscribed",
				);
				$json_data = json_encode($data);
				$arr = array(
					"method" => "PATCH",
					"path" => "/lists/$listId/members/".$has_mail,
					"body" => $json_data
				);
				$final_arr[] = $arr;
			}

			foreach ($companies as $key => $value) {
				if (!in_array($value['email'], $unsubscribed_emails) && !in_array($value['email'], $subscribed_emails)){
					$data = array(
						"email_address" => $value['email'],
						"status"        => "subscribed",
						"merge_fields"  => array(                
							'FNAME' => $value['first_name'],
							'LNAME' => $value['last_name'],
						)
					);
					$json_data = json_encode($data);
					$arr = array(
						"method" => "POST",
						"path" => "/lists/$listId/members/",
						"body" => $json_data
					);
					$final_arr[] = $arr;
				}elseif(in_array($value['email'], $unsubscribed_emails) && !in_array($value['email'], $subscribed_emails)){
					$has_mail = md5( strtolower( $value['email'] ) );
					$data = array(
						"status"  => "subscribed",
					);
					$json_data = json_encode($data);
					$arr = array(
						"method" => "PATCH",
						"path" => "/lists/$listId/members/".$has_mail,
						"body" => $json_data
					);
					$final_arr[] = $arr;
				}
			}

			$url 		= 'https://'.$dc.'.api.mailchimp.com/3.0/batches';
			$oper_arr	= array( "operations" => $final_arr);
			$json_post 	= json_encode($oper_arr);
			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_post);
			$result = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$result = json_decode( $result, true );
			if(isset($result['id'])){
				$response = true;
			}else{
				$response = false;
			}
			echo $response;
		}

		function mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
			if( $request_type == 'GET' )
				$url .= '?' . http_build_query($data);

			$mch = curl_init();
			$headers = array(
				'Content-Type: application/json',
				'Authorization: Basic '.base64_encode( 'user:'. $api_key )
			);
			curl_setopt($mch, CURLOPT_URL, $url );
			curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); 
			curl_setopt($mch, CURLOPT_TIMEOUT, 10);
			curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); 

			if( $request_type != 'GET' ) {
				curl_setopt($mch, CURLOPT_POST, true);
				curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) );
			}

			return curl_exec($mch);
		}
	}
	?>
