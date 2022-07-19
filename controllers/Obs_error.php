<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Obs_error extends CI_Controller {

	public function index()
	{   
	    $this->load->view('page_not_found');
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */