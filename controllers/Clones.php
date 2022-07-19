<?php
	class Clones extends CI_Controller{
		function __construct(){
			parent::__construct();
			$this->load->helper(array('form', 'url', 'phpmailer'));
		}

		function index()
		{
			$query = "SELECT `categories_id`, `subname`, `subdescription`, `subimage`, `subaddtext`, `submessage`, `suborder_display`, `subcreated`, `subupdated`, `status`, `display_tool_tip` FROM `subcategories` WHERE `categories_id` IN (SELECT `id` FROM `categories` WHERE `company_id` = 88)";
			$output = $this->db->query($query)->result_array();
			foreach($output as $key=>$value){
				$this->db->select("name,description,image");
				$this->db->where("id", $value['categories_id']);
				$cat_name = $this->db->get("categories")->row_array();
				
				$this->db->select("id");
				$this->db->where("name", $cat_name['name']);
				$this->db->where("description", $cat_name['description']);
				$this->db->where("image", $cat_name['image']);
				$this->db->where("id !=", $value['categories_id']);
				$cat_ids = $this->db->get("categories")->row_array();
				$output[$key]['categories_id'] =  $cat_ids['id'];
			}
			
			/*echo "<pre>";
			print_r($output);
			echo "</pre>";
			die;*/
			foreach($output as $insert_array){
				$this->db->insert("subcategories",$insert_array);
			}
		}
		
	}