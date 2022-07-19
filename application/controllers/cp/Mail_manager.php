<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MAIL MANAGER
 *
 * This package is for Mail Manager.
 * This package includes creating New Letters and sending it to subscribers
 *
 * @package Mail Manager
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */
class Mail_manager extends CI_controller
{
	/**
	 * This the default constructor
	 */
	function __construct(){
		parent::__construct();

		$this->load->helper('html');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('cookie');
		$this->load->helper('directory');
		$this->load->helper('phpmailer');

		$this->load->library('email');
		$this->load->library('session');
		$this->load->library('utilities');
		$this->load->library('Messages');

		$this->load->model('Mgeneral_settings');
		$this->load->model('Mcompany');
		$this->load->model('Mcalender');
		$this->load->model('Mmail_manager');
		$this->load->model('mclients');

		$this->lang_u = get_lang( $_COOKIE['locale'] );

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

		$this->template = "cp/";

		$this->status = $this->getStatus();
		$this->subscription = $this->isSubscribe();
		$this->templates = $this->Mmail_manager->get_templates(array(),array('company_id' => $this->company->id));
		//$this->subscriber_count = $this->mclients->get_company_clients($this->company_id,NULL,NULL,array('client_numbers.newsletter' => 'subscribe'),true);
		$this->subscriber_count = $this->Mmail_manager->get_subscribers(NULL, $this->company_id,'subscribed',TRUE);

	}

	/**
	 * This public index function is sued to show the dashboard page of Mail Manager where user can view Info regarding using this mail manager
	 * @access public
	 */
	public function index(){
		$data['content'] = $this->template.'mail_dashboard';
		$this->load->view($this->template.'cp_view',$data);
	}

	/**
	 * This function is used to show NewsLetters
	 * @access public
	 */
	public function newsLetters(){
		$data['newsLetters'] = $this->Mmail_manager->get_newsletters(array(),array('company_id' => $this->company_id));
		$data['content'] = $this->template.'mail_newsletter';
		$this->load->view($this->template.'cp_view',$data);
	}

	/**
	 * This public function is used to show Templates
	 * @access public
	 */
	public function templates(){
		//$defaultTemplates = $this->Mmail_manager->get_templates(array(),array('company_id' => 0));

		$data['templates'] = $this->templates;
		//$data['templates'] = array_merge($defaultTemplates,$templates);
		$data['content'] = $this->template.'mail_templates';
		$this->load->view($this->template.'cp_view',$data);
	}

	/**
	 * This function is used to show subscribers
	 * @access public
	 * @param int $subscriber_id Subscriber ID for showing detail page
	 */
	public function subscribers($subscriber_id = null){

		if($subscriber_id){
			$data['subscriber'] = $this->Mmail_manager->get_subscribers($subscriber_id);
			$data['content'] = $this->template.'mail_subscribers_details';
			$this->load->view($this->template.'cp_view',$data);
		}else{
			$data['subscribers'] = $this->Mmail_manager->get_subscribers(NULL, $this->company_id);
			$data['content'] = $this->template.'mail_subscribers';
			$this->load->view($this->template.'cp_view',$data);
		}

	}

	/**
	 * This function is taking ajax-post request and send mail whenever company owner orders for credits
	 * @access public
	 */
	public function place_order(){
		$credits = $this->input->post("credits");

		if($credits){
			$data = $this->subscription;
			if(empty($data)){
    			$insert_array = array(
    					'company_id' => $this->company_id,
    					'mail_type' => 'not_active',
    					'total_mails_send' => 0,
    					'mail_sent_for_current_type' => 0,
    					'registration_date' => date("Y-m-d H:i:s")
    			);

    			$this->Mmail_manager->insert_mail_manager($insert_array);
    		}
		}

		$mail_data['credits'] = $credits;
		$mail_data['admin_name'] = "Admin";
		$body = $this->load->view('mail_templates/'.$this->lang_u.'/mail_manager_order_credit',$mail_data,true);

		$query = send_email($this->config->item('site_admin_email'), $this->company->email, _("OBS: Buying Credits Request"), $body, NULL, NULL, NULL, 'company', 'site_admin', 'credit_orders');
		if($query){
			echo _("You request has been sent successfully");
		}else{
			echo _("Sorry!!!. Request has not been sent. Please try again later.");
		}
	}

	/**
	 * This function is used to send mail whenever company owner orders for monthly basis
	 * @access public
	 */
	public function buy_monthly(){

		$data = $this->subscription;
		if(empty($data)){
			$insert_array = array(
					'company_id' => $this->company_id,
					'mail_type' => 'not_active',
					'total_mails_send' => 0,
					'mail_sent_for_current_type' => 0,
					'registration_date' => date("Y-m-d H:i:s")
			);

			$this->Mmail_manager->insert_mail_manager($insert_array);
		}

		//$mail_data['credits'] = $this->input->post("credits");
		$mail_data['clients'] = $this->subscriber_count;
		$mail_data['admin_name'] = "Admin";
		$body = $this->load->view('mail_templates/'.$this->lang_u.'/mail_manager_order_monthly',$mail_data,true);

		$query = send_email($this->config->item('site_admin_email'), $this->company->email, _("OBS: Buying Monthly Basis Request"), $body, NULL, NULL, NULL, 'company', 'site_admin', 'monthly_order');

		if($query){
			echo ("You request has been sent successfully.");
		}else{
			echo _("Sorry!!!. Request has not been sent. Please try again later.");
		}

	}

	/**
	 * This function is used to fetch and return a form to send quick mail..
	 */
	public function get_mail_div($email = null){
		//$data['email'] = $this->input->get("email");
		$data['email'] = urldecode($email);
		$this->load->view("cp/mail_send_mail",$data);
	}

	/**
	 * This function is used to send mail.
	 */
	public function send_quick_mail(){

		$response = array("error"=>1,"message"=>_("Sorry mail did not send successfully"));
		if($this->input->post("send_mail_txt") != '' && $this->input->post('send_mail_sub') != ''){

			$msg = "<html><head></head><body>".$this->input->post("send_mail_txt")."</body></html>";

			$query = send_email($this->input->post('to_id_hidden'), $this->company->email, $this->input->post('send_mail_sub'), $msg, NULL, NULL, NULL, 'company', 'subscriber', 'quick_mail');
			if($query)
				$response = array("error"=>0,"message"=>_("Mail send successfully"));
		}
		echo json_encode($response);
	}

	/**
	 * This function is used to let user to create NewsLetters of their own choices..
	 */
	public function create_newsletters($id = null){

		if($id)
			$data['newsLetter'] = $this->Mmail_manager->get_newsletters(array(),array('id' => $id));

		$data['defaultTemplates'] = $this->Mmail_manager->get_templates(array(),array('company_id' => 0));
		$data['docs']	= $this->Mmail_manager->get_uploaded_docs($this->company_id);

		$data['templates'] = $this->Mmail_manager->get_templates(array(),array('company_id' => $this->company->id));
		//$data['templates'] = array_merge($defaultTemplates,$templates);
		$data['widgets'] = $this->Mmail_manager->get_widgets($this->company_id);
		$data['content'] = $this->template.'mail_create_newsletter';
		$data['images']	 = $this->Mmail_manager->get_uploaded_images($this->company_id);
		$this->load->view($this->template.'cp_view',$data);
	}

	/**
	 * This function is used to let user to create Templates of their own choices..
	 */
	public function create_templates($id = null){

		if($id){
			$data['newsLetter'] = $this->Mmail_manager->get_templates(array(),array('id' => $id));
		}

		//$data['widgets'] = $this->Mmail_manager->get_widgets($this->company_id);

		$data['content'] = $this->template.'mail_create_template';
		$this->load->view($this->template.'cp_view',$data);
	}

	/**
	 * This public function is used to add or edit widgets..
	 * @access public
	 */
	public function widget($id = null){

		if($this->input->post("add_w")){
			$insert_array = array(
				'company_id' => $this->company_id,
				'widget_name' => $this->input->post("widget_name"),
				'widget_content' => $this->input->post("widget_content"),
				'widget_slug' => $this->create_slug($this->input->post("widget_name")),
				'date' => date("Y-m-d H:i:s")
			);

			if($this->Mmail_manager->add_widget($insert_array))
				redirect(base_url()."cp/mail_manager/create_newsletters");
			else
				$this->message->add(_("Widget is not added successfully. PLease try again"));
		}

		if($this->input->post("edit_w")){
			$update_array = array(
					'widget_name' => $this->input->post("widget_name"),
					'widget_content' => $this->input->post("widget_content"),
					'widget_slug' => $this->create_slug($this->input->post("widget_name"))
			);

			$where_array = array(
					'company_id' => $this->company_id,
					'widget_id' => $this->input->post("widget_id")
			);

			if($this->Mmail_manager->update_widget($where_array, $update_array))
				redirect(base_url()."cp/mail_manager/create_newsletters");
			else
				$this->message->add(_("Widget is not Edited successfully. PLease try again"));
		}

		if($id && is_numeric($id)){
			$data['widgets'] = $this->Mmail_manager->get_widgets($this->company_id,array("widget_id" => $id));
		}

		$data['default'] = '';
		$data['content'] = $this->template.'mail_add_widget';
		$this->load->view($this->template.'cp_view',$data);
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

    	$response = array('error' => 1, 'data' => _('No Preview'));
    	$new_html = '';

    	if(!empty($datas)){
    		$new_html .= "<html><head></head><body><table border='0' cellspacing='0' cellpadding='0' width='100%'>";

    		foreach ($datas as $data){
    			$row_color = "white";
    			if(isset($data[0]['row_color'])){
    				$row_color = $data[0]['row_color'];
    			}
    			$new_html .= "<tr width='100%'><td style='background-color:".$row_color."'><table style='width:100%;margin:-2px'><tr>";
    			foreach ($data as $inner_data){

    				$widget_content = $inner_data['content'];

    				$widget_content = str_replace("{company_name}",$this->company->company_name,$widget_content);
    				$widget_content = str_replace("{first_name}",$this->company->first_name,$widget_content);
    				$widget_content = str_replace("{last_name}",$this->company->last_name,$widget_content);
    				$widget_content = str_replace("{email}",$this->company->email,$widget_content);
    				$widget_content = str_replace("{phone}",$this->company->phone,$widget_content);
    				$widget_content = str_replace("{website}",$this->company->website,$widget_content);
    				$widget_content = str_replace("{address}",$this->company->address,$widget_content);
    				$widget_content = str_replace("{zipcode}",$this->company->zipcode,$widget_content);
    				$widget_content = str_replace("{city}",$this->company->city,$widget_content);
    				$widget_content = str_replace("{unsubscribe}","{unsubscribe_link}",$widget_content);

    				$new_html .= "<td style='padding:2%;width:".($inner_data['width']-4)."%;background-color:".$inner_data['bg_color'].";color:".$inner_data['color']."'>".$widget_content."</td>";
    			}
    			$new_html .= "</tr></table></tr>";
    		}

    		$new_html .= "</table></body></html>";

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


    	$name = $this->input->post('title');
    	$from = $this->input->post('from');
    	$content = $this->input->post('info');

    	if($name != '' && $from != ''){
	    	if($this->input->post('editing') == 0){
	    		$insert_array = array(
	    				'company_id' => $this->company_id,
	    				'template_id' => $this->input->post('template'),
	    				'from' => $from,
	    				'content' => $content,
	    				'name' => $name,
	    				'attachment' => $this->input->post('attachment'),
	    				'date' => date("Y-m-d H:i:s")
	    		);
	    		if($this->Mmail_manager->save_newsletters($insert_array)){
	    			$response = array('error' => 0, 'data' => _("Newsletter has been saved successfully."));
	    		}
	    	}else{
	    		$update_array = array(
	    				'company_id' => $this->company_id,
	    				'template_id' => $this->input->post('template'),
	    				'from' => $from,
	    				'content' => $content,
	    				'name' => $name,
	    				'attachment' => $this->input->post('attachment'),
	    				'date' => date("Y-m-d H:i:s")
	    		);
	    		if($this->Mmail_manager->update_newsletters($this->input->post('editing'), $update_array)){
	    			$response = array('error' => 0, 'data' => _("Newsletter has been updated successfully."));
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
    		$this->Mmail_manager->delete_newLetter($id);
    	}
    	redirect(base_url().'cp/mail_manager/newsLetters');
    }

    /**
     * This public function is send newsletter to company itself
     */
    public function send_newsletter_me(){
    	//$this->load->helper('phpmailer');
    	$config['mailtype'] = 'html';
    	$this->email->initialize($config);

    	$newsLetterId = $this->input->post("id");
    	$newsLetter = $this->Mmail_manager->get_newsletters(array(),array('company_id' => $this->company_id,'id'=>$newsLetterId));

    	if(!empty($newsLetter)){
    		$response = $newsLetter['0']['content'];

    		$mail_template = '<html><head><style type="text/css">p { margin: 0; }</style></head><body><div style="width:70%; margin: 20px auto;">';
    		$mail_template .= $response;
    		$mail_template .= '</div></body></html>';

    		//dummy subscriber array
    		$subscriber = array(
    				'id'							=> NULL,
    				'company_name'		=> 'dummy_text',
    				'firstname_c'			=> 'dummy_text',
    				'lastname_c'				=> 'dummy_text',
    				'email_c'					=> $this->company->email,
    				'password_c'			=> 'dummy_pass',
    				'phone_c'					=> 'dummy_text',
    				'website'					=> 'dummy_text',
    				'address_c'				=> 'dummy_text',
    				'postcode_c'			=> 'dummy_text',
    				'city_c'						=> 'dummy_text',
    		);

    		$isSend = $this->send_newsletter_to_subscriber($subscriber,$mail_template,$newsLetter,true);

    		if($isSend)
    			echo _("Mail has been send successfully");
    		else
    			echo _("Sorry!!! Mail has not been send successfully. Please try again");
    	}else{
    		echo _("Sorry!!! Cant found the template.");
    	}

    }

    /**
     * This public function is for loading a view that will show in popup.
     * @see ajax_image_upload()
     */
    function ajax_img_upload(){
    	$this->load->view('cp/mail_image_upload_ajax');
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
    	$extension = end(explode(".",$image));

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
    	/*$maxWidth = 270;
    	$maxHeight = 270;
    	if ($size[0] > $maxWidth || $size[1] > $maxHeight )
    	{

    		$this->load->library('image_lib');

    		$config['image_library'] = 'gd2';
    		$config['source_image'] = dirname(__FILE__).'/../../../assets/images/mail_manager/cropped_'.$image;
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

    	}*/

    	$new_html = "";
    	$new_html .= "<img src='".base_url()."assets/images/mail_manager/cropped_".$image."' alt='"._("Sorry !!! image has not been cropped. Please try again")."'/> <input type='hidden' name='image_name' id='image_name' value='cropped_".$image."' />";
    	$new_html .= '<input type="button" id="add_w_img" name="add_w_img" value="'._("Add Widget").'" onclick="add_widget_img_new();" />';
    	echo $new_html;

    }

    /**
     * This function is user for copping image via ajax
     * @return string HTML of cropped image.
     */
    function crop_upload_center_image(){
    	$image = $this->input->post('image_name');
    	if (substr($image, 0, strlen('temp_')) == 'temp_') {
    		$actual_image_name = substr($image, strlen('temp_'));
    	}

    	$targ_w = $this->input->post('w');
    	$targ_h = $this->input->post('h');
    	$jpeg_quality = 90;
    	$extension = end(explode(".",$image));

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
    	$result = $this->Mmail_manager->insert_image_details($this->company_id,$actual_image_name);
    }



    /**
     * This public function is for loading a view that will show in popup.
     * @see ajax_image_upload()
     */
    function ajax_file_upload_view(){
    	$this->load->view('cp/mail_file_upload_ajax');
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
     * This public function is used to send newsletter to all its subscriber
     */
    public function send_newsletter_all(){

    	if($this->status == 'not_active'){
    		echo _("Sorry!!! Your subscription is not activated this time. Please buy any plan if not requested.");
    		die();
    	}else{
    		$data = $this->subscription;

    		if(!empty($data)){
    			$creditsLeft = $data['0']['credits'] - $data['0']['mail_sent_for_current_type'];

    			if($data['0']['mail_type'] == 'credits' && $creditsLeft <= 0){
    				 echo _("Sorry!!! You have no credits left. Please buy again.");
    				 die();
    			}
    		}

    		$this->load->helper('mailsmtp');
    		$config = mail_config();
    		$this->email->initialize($config);

    		$newsLetterId = $this->input->post("id");
    		$newsLetter = $this->Mmail_manager->get_newsletters(array(),array('company_id' => $this->company_id,'id'=>$newsLetterId));

    		$response = $newsLetter['0']['content'];
    		$mail_template = '<html><head><style type="text/css">p { margin: 0; }</style></head><body><div style="width:760px; margin: 20px auto;">';
    		$mail_template .= $response;
    		$mail_template .= '</body></html>';

    		$subscribers = $this->Mmail_manager->get_subscribers(NULL, $this->company_id);
    		//$email_list = array();
    		$count = 0;
    		$fail_count = 0;
    		if(!empty($subscribers)){
    			foreach ($subscribers as $subscriber){
    				if($subscriber['email_c'] != ''){

    					if(isset($creditsLeft) && $creditsLeft == 0){
    						break;
    					}

    					$insert_array = array(
    							'company_id' => $this->company_id,
    							'client_id' => $subscriber['id'],
    							'client_mail' => $subscriber['email_c'],
    							'date' => date("Y-m-d H:i:s")
    					);

    					$this->Mmail_manager->insert_mails_send($insert_array);

    					if($this->send_newsletter_to_subscriber($subscriber,$mail_template,$newsLetter)){
    						if(isset($creditsLeft)){
    							$creditsLeft = $creditsLeft - 1;
    						}
    						++$count;

    					}else{
    						++$fail_count;
    					}
    					/*if($subscriber['email_c']){

    						$count = count($email_list);

    						$this->email->from($this->company->email);
    						$this->email->subject(_("News Letter: ").$newsLetter['0']['name']);
    						$this->email->message($mail_template);
    						$this->email->to($email_list['0']);
    						unset($email_list['0']);
    						$this->email->bcc($email_list);

    						$isSend = $this->email->send();

    						if(!empty($data)){
    							$where_array = array(
    									'company_id' => $this->company_id
    							);
    							$update_array = array(
    									'total_mails_send' => ($data['0']['total_mails_send'] + $count),
    									'mail_sent_for_current_type' => ($data['0']['mail_sent_for_current_type'] + $count)
    							);

    							$this->Mmail_manager->update_mail_manager($where_array,$update_array);
    						}else{

    							$insert_array = array(
    									'company_id' => $this->company_id,
    									'mail_type' => 'free',
    									'total_mails_send' => $count,
    									'mail_sent_for_current_type' => $count,
    									'registration_date' => date("Y-m-d H:i:s")
    							);

    							$this->Mmail_manager->insert_mail_manager($insert_array);
    						}

    						echo _("Mail has been send successfully");
    					}else{
    						echo _("Sorry!!! Mail has not been send successfully. Please try again");
    					}*/
    				}
    			}
    			if(!empty($data)){
    				$where_array = array(
    						'company_id' => $this->company_id
    				);
    				$update_array = array(
    						'total_mails_send' => ($data['0']['total_mails_send'] + $count),
    						'mail_sent_for_current_type' => ($data['0']['mail_sent_for_current_type'] + $count)
    				);

    				$this->Mmail_manager->update_mail_manager($where_array,$update_array);
    			}else{

    				$insert_array = array(
    						'company_id' => $this->company_id,
    						'mail_type' => 'free',
    						'total_mails_send' => $count,
    						'mail_sent_for_current_type' => $count,
    						'registration_date' => date("Y-m-d H:i:s")
    				);

    				$this->Mmail_manager->insert_mail_manager($insert_array);
    			}

    		}

    		if($count && $creditsLeft == 0){
    			echo ($count == 1)?$count.' '._("mail sent."):$count.' '._("mails sent.");
    			echo ' '._("You have no credits left. Please buy again.");
    		}
    		elseif ($fail_count){
    			echo _("Sorry!!! Some mails have not been send successfully. Please try again");
    		}
    		else{
    			echo _("All Mails have been sent successfully");
    		}
    	}
    }

    /**
     * This public function is used to copy template
     */
    public function newsletter_clone($newsLetterId = null){

    	if($newsLetterId){
    		$newsLetterInfo = $this->Mmail_manager->get_newsletters(array(),array('company_id' => $this->company_id,'id'=>$newsLetterId));
    		if(!empty($newsLetterInfo)){
    			$insert_array = array(
    				'company_id' => $this->company_id,
    				'content' => $newsLetterInfo['0']['content'],
    				'name' => 'clone_'.$newsLetterInfo['0']['name'],
    				'date' => date("Y-m-d H:i:s")
    			);
    			$this->Mmail_manager->save_newsletters($insert_array);
    		}
    	}

    	redirect(base_url().'cp/mail_manager/newsLetters');
    }

    /**
     * This private function is used to check whether this company is subscribed for mail_manager or not
     */
    private function isSubscribe(){
    	$data = $this->Mmail_manager->get_subscription(array('company_id' => $this->company_id));
    	return $data;
    }

    /**
     * This private function is used to fethc current status of the company regarding mail manager
     */
    private function getStatus(){
    	$status = 'free';
    	$data = $this->Mmail_manager->get_subscription(array('company_id' => $this->company_id));
    	if(!empty($data)){
    		$status = $data['0']['mail_type'];
    	}

    	//$clients = explode(",",$this->company->clients_associated);
    	$clients = $this->mclients->get_company_clients($this->company_id,NULL,NULL,array(),true);
    	if(count($clients) > 20 && $status == 'free'){
    		if(!empty($data)){
    			$where_array = array(
    					'company_id' => $this->company_id
    			);
    			$update_array = array(
    					'mail_type' => 'not_active'
    			);
    			$this->Mmail_manager->update_mail_manager($where_array,$update_array);
    		}else{

    			$insert_array = array(
    					'company_id' => $this->company_id,
    					'mail_type' => 'not_active',
    					'registration_date' => date("Y-m-d H:i:s")
    			);

    			$this->Mmail_manager->insert_mail_manager($insert_array);
    		}

    		$status == 'not_active';
    	}

    	return $status;
    }

    /**
     * This public function is used to show FAQs
     * @access public
     */
    public function faq(){
    	$data['content'] = $this->template.'mail_faq';
    	$this->load->view($this->template.'cp_view',$data);
    }

    /**
     * Testing function
     */
    public function generate_preview_temp(){
    	$datas = $this->input->post('info');

    	/*if(count($datas) > 1){
    		for ($i = 0; $i < (count($datas)-1); $i++){
    			if($datas[$i][2] > $datas[$i+1][2]){
    				$temp = $datas[$i];
    				$datas[$i] = $datas[$i+1];
    				$datas[$i+1] = $temp;
    			}
    		}
    	}*/
    	//usort($datas, create_function('$a, $b','if ($a["2"] == $b["2"]) return 0; return ($a["2"] < $b["2"]) ? -1 : 1;'));
    	$new_html = '';
    	if(!empty($datas)){
    		foreach ($datas as $key => $value){
    			$new_html .= "<div style='background-color:".$value[5]."; color:".$value[6]."; position:absolute; top:".$value[1]."; left:".$value[2]."; width:".$value[3]."; height:".$value[5].";'>";
    			$widget_content = $value[0];
    			$widget_content = str_replace("{company_name}",$this->company->company_name,$widget_content);
    			$widget_content = str_replace("{first_name}",$this->company->first_name,$widget_content);
    			$widget_content = str_replace("{last_name}",$this->company->last_name,$widget_content);
    			$widget_content = str_replace("{email}",$this->company->email,$widget_content);
    			$widget_content = str_replace("{phone}",$this->company->phone,$widget_content);
    			$widget_content = str_replace("{website}",$this->company->website,$widget_content);
    			$widget_content = str_replace("{address}",$this->company->address,$widget_content);
    			$widget_content = str_replace("{zipcode}",$this->company->zipcode,$widget_content);
    			$widget_content = str_replace("{city}",$this->company->city,$widget_content);
    			$new_html .= $widget_content;
    			$new_html .= "</div>";
    		}
    		/*$new_html .= '<br />';
    		$new_html .= '<p>';
    		$new_html .= $this->company->company_name.'<br />';
    		$new_html .= $this->company->address.'<br />';
    		$new_html .= $this->company->zipcode.' '.$this->company->city.'<br />';
    		$new_html .= _("Email").': '.$this->company->email.'<br />';
    		$new_html .= _("Tel").': '.$this->company->phone;
    		$new_html .= '</p>';*/
    	}

    	echo $new_html;
    }

    /**
     * This function is used to delete Templates
     */
    public function delete_template($id = null){
    	if($id && is_numeric($id)){
    		$template = $this->Mmail_manager->get_templates(array(),array( 'id' => $id ,'company_id' => $this->company_id));
    		if(!empty($template)){
    			$this->Mmail_manager->delete_templates(array( 'id' => $id ,'company_id' => $this->company_id));
    		}
    	}

    	redirect(base_url().'cp/mail_manager/templates');
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
    			if($this->input->post('editng') == 0){
	    			$insert_array = array(
	    					'company_id' => $this->company_id,
	    					'content' => json_encode($content),
	    					'name' => addslashes($this->input->post('title')),
	    					'date' => date("Y-m-d H:i:s")
	    			);
	    			if($this->Mmail_manager->save_templates($insert_array)){
	    				$response = array('error' => 0, 'data' => _("Templates has been saved successfully."));
	    			}
    			}else{
    				$updt_array = array(
    						'content' => json_encode($content),
    						'name' => addslashes($this->input->post('title')),
    						'date' => date("Y-m-d H:i:s")
    				);
    				if($this->Mmail_manager->update_template($this->input->post('editng'), $updt_array)){
    					$response = array('error' => 0, 'data' => _("Templates has been Updated successfully."));
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
    						//echo '<div class="image_holder" image_name='.$actual_image_name.'><img src="'.base_url()."assets/images/upload_center/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" width="150px" height="150px" style="padding:4px;" />
								//			</div><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="crop();" /></div>';
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
    		$data['content'] = $this->template.'mail_upload_center';
    		$data['images']  = $this->Mmail_manager->get_uploaded_images($this->company_id);
    		$this->load->view($this->template.'cp_view',$data);
    	}
    }

    /**
     * @access public
     * This public function is used to delete images of upload center
     */
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
    	$this->db->where('company_id', $this->company_id);
    	$this->db->where('id', $this->input->post('ns_id'));
    	$request_data = $this->db->get('newsletters')->row_array();

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

    	$this->db->where('company_id', $this->company_id);
    	$this->db->where('id', $this->input->post('ns_id'));
    	$this->db->update('newsletters',array('attachment' => ''));
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
    								'company_id' => $this->company_id,
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
    		$data['content'] = $this->template.'doc_upload_center';
    		//$data['images']  = $this->Mmail_manager->get_uploaded_images($this->company_id);
    		$data['docs']	= $this->Mmail_manager->get_uploaded_docs($this->company_id);
    		$this->load->view($this->template.'cp_view',$data);
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
    	$data['images']	 = $this->Mmail_manager->get_uploaded_images($this->company_id);
    	$this->load->view('cp/image_manager_popup',$data);
    }

    /**
     * Send newsletter to any subscriber
     *
     */
    private function send_newsletter_to_subscriber($subscriber = array(),$mail_template = NULL,$newsLetter = array(),$newsletter_me = false){
    	$response = false;
    	if(!empty($subscriber) && $mail_template && !empty($newsLetter) && $subscriber['email_c'] != ''){

    		/* -------------------------------------Get direct login/unsubscribe link -------------------------------------*/
    		$unsubscribe_url = $this->config->item('portal_url').'unsubscribe?email='.$subscriber['email_c'].'&subscriber_id='.md5($subscriber['password_c'].'&comp_id='.$this->company_id);
    		$unsubscribe_link = "<a href='".$unsubscribe_url ."'>"._('unsubscribe')."</a>";
    		/*--------------------------------------End direct login/unsubscribe link -------------------------------------*/

    		//mail template parse array
    		$data = array(
    				'company_name'		=> $subscriber['company_name'],
    				'first_name'		=> $subscriber['firstname_c'],
    				'last_name'			=> $subscriber['lastname_c'],
    				'email'				=> $subscriber['email_c'],
    				'phone'				=> $subscriber['phone_c'],
    				'website'			=> $subscriber['website'],
    				'address'			=> $subscriber['address_c'],
    				'zipcode'			=> $subscriber['postcode_c'],
    				'city'				=> $subscriber['city_c'],
    				'unsubscribe_link'	=> $unsubscribe_link,
    				'website'			=> $subscriber['website']
    		);

    		//email variables
    		$sender		= $this->config->item('site_admin_email');
    		$recipient	= $subscriber['email_c'];

    		$subject		= _("News Letter: ").$newsLetter['0']['name'];
    		$mail_body = $this->utilities->parseMailText($mail_template, $data);

    		//email details
    		$this->email->clear(TRUE);
    		$this->email->from($sender);
    		$this->email->to($recipient);
    		$this->email->subject($subject);
    		$this->email->message($mail_body);

    		//attachment
    		$attachment_names = $this->Mmail_manager->get_doc_name($newsLetter['0']['attachment']);
    		if(!empty($attachment_names)){

    			$attachment_name = $attachment_names[0]['doc_name'];
	    		$path = dirname(__FILE__).'/../../../assets/upload_center/docs/';
	    		$attachment = NULL;
	    		$attachment_path = NULL;

	    		if($newsLetter['0']['attachment'] != null && file_exists($path.$attachment_name)){
	    			$this->email->attach($path.$attachment_name);
	    			$attachment = $attachment_name;
	    		}
    		}
    		//log data
    		$log_data = array(
    				'email_from'			=> $sender,
    				'email_to'					=> $recipient,
    				'from_type'				=> 'company',
    				'to_type'					=> 'client',
    				'email_type'				=> $newsletter_me?'newsletter_me':'newsletter_to_clients',
    				'subject'					=> $subject,
    				'message'				=> $mail_body,
    				'datetime'				=> date('Y-m-d H:i:s',time()),
    				'attachment	'			=> isset($attachment)?$attachment:'',
    				'company_rel_id'		=> $this->company_id,
    				'client_rel_id'			=> $subscriber['id'],
    				'newsletter_rel_id'	=> $newsLetter['0']['id']
    		);

    		$isSend = $this->email->send();

    		if($isSend){
    			$this->db->insert('email_logs_newsletter',$log_data);
    			$response = true;
    		}
    	}
    	return $response;
    }

    function ajax_temp_preview($id = 1){
		$selected_template = $this->Mmail_manager->get_templates(array(),array('id' => $id));
		$tpl_data = $this->create_html(json_decode($selected_template[0]['content'],true));
		echo $tpl_data['data'];
    }
}
?>