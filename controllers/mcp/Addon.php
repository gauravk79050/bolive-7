<?php 
	class Addon extends CI_controller{
		
		function __construct(){
			parent::__construct();
			
			$this->tempUrl=base_url().'application/views/mcp';
			$this->template="/mcp";
			$this->load->helper('url');
			$this->load->library('image_lib');
			$this->load->helper('html');
			
			$this->load->model('mcp/Maddon');
			
			$this->temp=base_url().'mcp/addon';
			
		}
		
		function index(){

			$data['addons'] = $this->Maddon->get_addons();
			
			$data['header']=$this->template.'/header';
			$data['main']=$this->template.'/addons';
			$data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		/* Update the data of addons */
		function update()
		{
			$this->load->library('form_validation');
			if ($this->input->post('update'))
			{
				$this->form_validation->set_rules('addon_title', 'addon_title', 'required');
				$this->form_validation->set_rules('addon_description', 'addon_description', 'required');
				$this->form_validation->set_rules('addon_price', 'addon_price', 'required|numeric');
				$this->form_validation->set_rules('addon_display_order', 'addon_display_order', 'required|numeric|is_unique[addon_manager.addon_display_order]');
				
					
				if($this->form_validation->run()==FALSE)
				{
					
				}else{
						
					$addon_id =$this->input->post('addon_id');
					$dummy['addon_title'] = $this->input->post('addon_title');
					$dummy['addon_description'] = $this->input->post('addon_description');
					$dummy['addon_price'] = $this->input->post('addon_price');
					$dummy['addon_display_order'] = $this->input->post('addon_display_order');
					
		
					$result=$this->Maddon->update($addon_id,$dummy);
		
				}//end of else
			  
			}
	
			$data['main']=$this->template.'/addon_update';
			$this->id=$this->uri->segment(4);
			$data['content']=$this->Maddon->select($this->id);
			$data['header']=$this->template.'/header';
			$data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		
		
		}//end of function
		
		function add_addon(){
			$this->load->library('form_validation');
			$data['header']=$this->template.'/header';
			$data['main']=$this->template.'/add_addon';
			$data['footer']=$this->template.'/footer';
			$this->load->vars($data);
			$this->load->view($this->template.'/mcp_view');
		}
		
		function insert()
		{
			$this->load->library('form_validation');
			if($this->input->post())
			{
				$this->form_validation->set_rules('addon_name', 'addon_name', 'required');
				$this->form_validation->set_rules('addon_desc', 'addon_desc', 'required');
				$this->form_validation->set_rules('addon_price', 'addon_price', 'required|numeric');
					
				if($this->form_validation->run()==FALSE)
				{
					$data['header']=$this->template.'/header';
					$data['main']=$this->template.'/add_package';
					$data['footer']=$this->template.'/footer';
					$this->load->vars($data);
					$this->load->view($this->template.'/mcp_view');
		
				}else{
						
					$dummy['addon_title'] = $this->input->post('addon_name');
					$dummy['addon_description'] = $this->input->post('addon_desc');
					$dummy['addon_price'] = $this->input->post('addon_price');
					$dummy['addon_slug'] = str_replace(" ","#",$dummy['addon_title']);
					
					$result=$this->Maddon->insert_addon($dummy);
		
					if($result)
					{
						redirect($this->temp.'/add_addon','refresh');
					}else{
						redirect($this->temp.'/add_addon');
					}
				}
			}
		}
		
	}
?>