<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Terms and Conditions
 * 
 * This package is for showing terms and conditions to users
 * @package Terms_conditions
 * @author Shyam Mishra <shyammishra@cedcoss.com>
 */

class Terms_conditions extends CI_Controller
{	
	function __construct()
	{		
		parent::__construct();	
		
	}
	
	function index()
	{
		$this->load->view('terms_condition_view');
	}
}
?>