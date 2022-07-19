<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This controller is used for image manipulation
 * It extends CI's base controller
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 * @package Image Upload
 */

class Image_upload extends CI_controller{

	var $company_id = '';

	/**
	 * Initialize contructor
	 */
	function __construct(){
		parent::__construct();
		$this->load->model('Mproducts');
		$this->company_id = $this->session->userdata('cp_user_id');
	}

	/**
	 * This public function is for loading a view that will show in popup.
	 * @example calender.php
	 * @see ajax_image_upload()
	 * @link http://www.google.com
	 * @name $baz
	 */
	function ajax_img_upload($view_page = 'cp',$num = 0){
		$data['attr_det'] = '';
		if (isset($_GET['attr_det']))
		{
			$attr_data = $_GET['attr_det'];
			$data['attr_det'] = $attr_data;
		}
		if($num){
			$data['num'] = $num;
			$this->load->view($view_page.'/image_gallery_upload_ajax',$data);
		}
		else
			$this->load->view($view_page.'/image_upload_ajax',$data);
	}

	/**
	 * This function is used for Uploading image via ajax
	 * @return string An error message or an HTML of containing image and some information
	 */
	function ajax_image_upload($i = 0){
		$path = dirname(__FILE__).'/../../../assets/temp_uploads/';
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

				if(!preg_match('/[^a-zA-Z0-9\s-_]+/', $txt)){

					if(in_array($ext,$valid_formats))
					{
						if($size<(4 * 1048576))
						{
							$actual_image_name = $this->company_id.'_'.str_replace(" ", "_", $txt).".".$ext;
							$tmp = $_FILES['photoimg']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name))
							{
								$size = getimagesize($path.$actual_image_name);
								$maxWidth = 660;
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
								
								echo '<img src="'.base_url()."assets/temp_uploads/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" />
											<input type="hidden" name="image_name" id="image_name" value="'.$actual_image_name.'" /><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="crop('.$i.');" /></div>


												<div class="rotate_div" align="center">
												<a href="javascript:;" onClick="rotcw(this)" data-img1="'.$actual_image_name.'" title="'._("Rotate Clock Wise").'">
												<img src="'.base_url().'assets/cp/images/cw.png" ></a>
													<a href="javascript:;" onClick="rotacw(this)" data-img2="'.$actual_image_name.'" title="'._("Rotate Anti Clock Wise").'">
													<img src="'.base_url().'assets/cp/images/acw.png">
													</a>
													</div>';
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
					echo "<span style='color: red'>"._('filename is not legit. Please remove those special symbols')."</span>";
			}
			else
				echo "<span style='color: red'>"._('Invalid file format..')."</span>";

			exit;
		}
	}

	/**
	 * This function is used for Uploading image via ajax
	 * @return string An error message or an HTML of containing image and some information
	 */
	function ajax_gallery_image_upload($num = 1){
		$path = dirname(__FILE__).'/../../../assets/temp_uploads/';
		$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "JPG", "PNG", "GIF");
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
			$name = $_FILES['photoimg']['name'];
			$size = $_FILES['photoimg']['size'];

			if(strlen($name)){
				$txt = '';
				$ext = '';
				$parts = explode(".", $name);
				if(is_array($parts) && isset($parts['0']) && isset($parts['1']))
					list($txt, $ext) = explode(".", $name);

				if(!preg_match('/[^a-zA-Z0-9\s-_]+/', $txt)){

					if(in_array($ext,$valid_formats)){
						if($size<(4 * 1048576)){
							$actual_image_name = $this->company_id.'_'.str_replace(" ", "_", $txt).".".$ext;
							$tmp = $_FILES['photoimg']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name)){
								$size = getimagesize($path.$actual_image_name);
								$maxWidth = 660;
								//$maxHeight = 600;
								if ($size[0] > $maxWidth){
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

								echo '<img src="'.base_url()."assets/temp_uploads/".$actual_image_name.'" id="target" alt="'._("No image !!! please try again").'" />
											<input type="hidden" name="image_name'.$num.'" id="image_name'.$num.'" value="'.$actual_image_name.'" /><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="gal_crop(this);"/></div>';
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
					echo "<span style='color: red'>"._('filename is not legit. Please remove those special symbols')."</span>";
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
	function crop_image($num = 0){
		$image = $this->input->post('image_name');
		$targ_w = $this->input->post('w');
		$targ_h = $this->input->post('h');
		$shop_result=$this->Mproducts->get_shop_image_size();
		$jpeg_quality = 90;
		$extension = end(explode(".",$image));

		$src = dirname(__FILE__).'/../../../assets/temp_uploads/'.$image;
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
		imagejpeg($dst_r,dirname(__FILE__).'/../../../assets/temp_uploads/cropped_'.$image,$jpeg_quality);

		// Resizing image to  270*270
		$size = getimagesize(dirname(__FILE__).'/../../../assets/temp_uploads/cropped_'.$image);
		if ($shop_result[0]['biggest_image'])
		{
			$maxWidth = 600;
			$maxHeight = 600;
		}else
		{
			$maxWidth = 2000;
			$maxHeight = 270;
		}
		if ($size[0] > $maxWidth || $size[1] > $maxHeight )
		{

			$this->load->library('image_lib');

			$config['image_library'] = 'gd2';
			$config['source_image'] = dirname(__FILE__).'/../../../assets/temp_uploads/cropped_'.$image;
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

		$new_html = "";
		if($num)
			$new_html .= "<img src='".base_url()."assets/temp_uploads/cropped_".$image."' alt='"._("Sorry !!! image has not been cropped. Please try again")."' style='float: left; margin-right:20px;' /> <input type='hidden' name='image_name".$num."' id='image_name".$num."' value='cropped_".$image."' />";
		else
			$new_html .= "<img src='".base_url()."assets/temp_uploads/cropped_".$image."' alt='"._("Sorry !!! image has not been cropped. Please try again")."' style='float: left; margin-right:20px;' /> <input type='hidden' name='image_name' id='image_name' value='cropped_".$image."' />";
			$new_html .= '<span style=" display: block; float: left; margin-top: 70px; width: 192px;">'._("Note: This image will be saved when you click the update button below. To change image you can upload another image again").'</span> <div style="clear:both;"></div><br/><br/>';

		echo $new_html;

	}
	 //Function for rotating image which appear at time of first upload

	function rotate_image(){

    	$img = $this->input->post('src');
        $angle= $this->input->post('angle');

        $angle=($angle=='acw')?'90':'270';

        $this->load->library('image_lib');
        $config['image_library'] = 'gd2';

        $config['source_image'] = dirname(__FILE__).'/../../../assets/temp_uploads/'.$img;
        $config['rotation_angle'] = $angle;
        $rot_img = '0'.$img;
        $config['new_image'] = dirname(__FILE__).'/../../../assets/temp_uploads/'.$rot_img;

        $this->image_lib->initialize($config);

        if ( ! $this->image_lib->rotate())
        {
        	echo $this->image_lib->display_errors();
        	exit;
        }
        else{

        echo '<img src="'.base_url()."assets/temp_uploads/".$rot_img.'" id="target" alt="'._("No image !!! please try again").'" />
											<input type="hidden" name="image_name" id="image_name" value="'.$rot_img.'" /><div class="crop_div" ><input type="button" name="crop_button" id="crop_button" value="'._("Crop").'" onClick="crop(0);" /></div>


												<div class="rotate_div" align="center">
													<a href="javascript:;" onClick="rotcw(this)" data-img1="'.$rot_img.'" title="'._("Rotate Clock Wise").'">
													<img src="'.base_url().'assets/cp/images/cw.png"></a>
													<a>
													<a href="javascript:;" onClick="rotacw(this)" data-img2="'.$rot_img.'" title="'._("Rotate Anti Clock Wise").'">
													<img src="'.base_url().'assets/cp/images/acw.png">
													</a>
													</div>';
    }}
    	// start Function for rotating image which was saved
    /*function rotate_uploaded_image(){

    	$img = $this->input->post('src');
    	if (file_exists(dirname(__FILE__).'/../../../assets/cp/images/product/'.$img))
    	{
    		$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/cp/images/product/'.$img);
    		file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img,$file_cont);
    	}
    	//move_uploaded_file ( dirname(__FILE__).'/../../../assets/cp/images/product/'.$img , dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img );
        $angle= $this->input->post('angle');

        $angle=($angle=='acw')?'90':'270';

        $this->load->library('image_lib');
        $config['image_library'] = 'gd2';

        $config['source_image'] = dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img;
        $config['rotation_angle'] = $angle;
        $rot_img = '0'.$img;
        $config['new_image'] = dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$rot_img;

        $this->image_lib->initialize($config);

        if ( ! $this->image_lib->rotate())
        {
        	echo $this->image_lib->display_errors();
        	exit;
        }
        else{
        	$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$rot_img);
        	file_put_contents(dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img,$file_cont);
        	echo $rot_img;
    	}
    }*/

    function rotate_uploaded_image(){

    	$img = $this->input->post('src');
    	if (file_exists(dirname(__FILE__).'/../../../assets/cp/images/product/'.$img))
    	{
    		$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/cp/images/product/'.$img);
    		file_put_contents(dirname(__FILE__).'/../../../assets/temp_uploads/'.$img,$file_cont);
    	}
    	//move_uploaded_file ( dirname(__FILE__).'/../../../assets/cp/images/product/'.$img , dirname(__FILE__).'/../../../assets/cp/images/product/rotated/'.$img );
    	$angle= $this->input->post('angle');

    	$angle=($angle=='acw')?'90':'270';

    	$this->load->library('image_lib');
    	$config['image_library'] = 'gd2';

    	$config['source_image'] = dirname(__FILE__).'/../../../assets/temp_uploads/'.$img;
    	$config['rotation_angle'] = $angle;
    	$rot_img = '0'.$img;
    	$config['new_image'] = dirname(__FILE__).'/../../../assets/temp_uploads/'.$rot_img;

    	$this->image_lib->initialize($config);

    	if ( ! $this->image_lib->rotate())
    	{
    		echo $this->image_lib->display_errors();
    		exit;
    	}
    	else{
    		$file_cont = file_get_contents(dirname(__FILE__).'/../../../assets/temp_uploads/'.$rot_img);
    		file_put_contents(dirname(__FILE__).'/../../../assets/temp_uploads/'.$img,$file_cont);
    		echo $rot_img;
    	}
    }
    // end
}
?>