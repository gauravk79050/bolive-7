<?php
class Admin extends CI_Controller
{
    var $template="";
	var $tempUrl="";
    function __construct()
	{
	 parent::__construct();
	// $this->load->helper('url');
	 $this->tempUrl=base_url().'application/views/mcp';
	 $this->template="/mcp";
	}
	function index()
	{
	 $data['header']=$this->template.'/header';
	 $data['main']=$this->template.'/index';
	 $data['footer']=$this->template.'/footer';
	 $this->load->vars($data);
	 $this->load->view($this->template.'/mcp_view');
	}
}
?>