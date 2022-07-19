<?php
	class Upload extends CI_Controller{
		function __construct(){
			parent::__construct();
			$this->load->helper(array('form', 'url', 'phpmailer'));
		}

		function index()
		{
			$this->load->view('upload_form', array('error' => ' ' ));
		}
		
		function mail_test(){
			echo "<pre>"; print_r($_SERVER);
			$URL = $_SERVER['HTTP_REFERER'];
			$host = parse_url($URL, PHP_URL_HOST);
			if(strpos($host, "www") !== FALSE)
				echo "testing";	$host = str_replace("www.","",$host);

			echo $host;

		}

		function do_upload()
		{
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png';
			/*$config['max_size']	= '100';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';*/

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload())
			{
				$error = array('error' => $this->upload->display_errors());

				print_r($error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());

				print_r($data);
			}
		}
	}
