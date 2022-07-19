<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model: Mail Manager
 * 
 * This is the Model class used to interact with tables: newsletters
 * @package Mmail_manager
 */
class Mmail_manager extends CI_model{
	
	function __construct(){
		parent::__construct();
		$this->company_id = $this->session->userdata('cp_user_id');
	}
	
	/**
	 * This function is used to retrieve News letters from db
	 * @param array $select_array It is the array of columns to be select from table
	 * @param array $where_array It is the array of columns name and their vlues used for Where clause
	 * @param array $order_by It is the array of Column name and values(ASC, DESC) for Order by clause
	 * @return array $newsLetter It is the array of News Letter that has to be return  
	 */
	function get_newsletters($select_array = array(), $where_array = array(), $order_by = array() ){
		
		if(!empty($select_array)){
			$this->db->select(implode(",",$select_array));
		}
		
		if(!empty($where_array)){
			foreach($where_array as $column => $value){
				$this->db->where($column, $value);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $column => $value){
				$this->db->order_by($column, $value);
			}
		}else{
			$this->db->order_by("date", "DESC");
		}
		
		$newsLetter = $this->db->get("newsletters")->result_array();
		
		return $newsLetter;
	}
	
	/**
	 * This function is used to save News letters into db
	 * @param array $insert_array It is the array of columns to be inserted into table
	 * @return mixed It wil return either true or false
	 */
	function save_newsletters($insert_array = array()){
		//print_r($insert_array);die;
		if(!empty($insert_array)){
			return $this->db->insert('newsletters',$insert_array);
		}else{
			return false;
		}
		
	}
	
	/**
	 * This model function is used to fetch all subscribers of current logged in company
	 * @param int $subscriber_id Subscriber Id
	 * @return array $subscribers Array of subscribers
	 */
	function get_subscribers($subscriber_id = null, $companyId = null, $type = 'subscribed', $count = FALSE){
		
		$subscribers = array();
		
		/*$clients = '';
		if($companyId){
			$this->db->select('clients_associated');
			$client_info = $this->db->get_where('company',array('id' => $companyId))->result_array();
			if(!empty($client_info))
				$clients = $client_info['0']['clients_associated'];
		}else{
			$clients = $this->company->clients_associated;
		}
		
		
		if($clients){
			
			if($type == 'subscribed')
				$this->db->where("newsletters","subscribe");
			elseif($type == 'unsubscribed')
				$this->db->where("newsletters","unsubscribe");
			
			if($subscriber_id){
				$associated_clients = explode(",",$clients);
				if(in_array($subscriber_id,$associated_clients)){
					$this->db->where("id",$subscriber_id);
				}
			}else{
				$this->db->where_in("id",explode(",",$clients));
			}
			
			$subscribers = $this->db->get("clients")->result_array();
		}*/
		
		$this->db->select('clients.*, country.country_name,company.company_name,company.website');
		$this->db->join('clients', 'clients.id = client_numbers.client_id');
		$this->db->join('country', 'country.id = clients.country_id', 'left');
		$this->db->join('company', 'company.id = clients.company_id', 'left');
		$this->db->where('client_numbers.company_id',$companyId);
		$this->db->where('client_numbers.associated','1');
		if($subscriber_id){
			$this->db->where('client_numbers.client_id',$subscriber_id);
		}
		if($type == 'subscribed')
			$this->db->where("client_numbers.newsletter","subscribe");
		elseif($type == 'unsubscribed')
			$this->db->where("client_numbers.newsletter","unsubscribe");
		
		$this->db->order_by('clients.created_c','DESC');

		if($count){
			$subscribers = $this->db->count_all_results('client_numbers');
		}
		else{
			$subscribers = $this->db->get('client_numbers')->result_array();
		}
		
		return $subscribers;
		
	}
	
	/**
	 * This model function is used to add widget into database
	 * @param array $insert_array This is the array with field and columns
	 */
	function add_widget($insert_array = array()){
		if(!empty($insert_array))
			return $this->db->insert("widgets",$insert_array);
	}
	
	/**
	 * This model function is sued to get widget of specific comapny
	 * @param int $company_id This is the Company Id for which widget have to be fetched
	 * @return array $widgets Array of widgets..
	 */
	function get_widgets($company_id = null, $where_array = array()){
		$widgets = array();
		if($company_id && is_numeric($company_id)){
			if(!empty($where_array)){
				foreach($where_array as $col => $val){
					$this->db->where($col , $val);
				}	
			}
			$this->db->where("company_id",$company_id);
			$widgets = $this->db->get("widgets")->result_array();
		}
		return $widgets;
	}
	
	/**
	 * This function is sued to update widget
	 */
	function update_widget($where_array = array(), $update_array = array()){
		
		if(!empty($where_array)){
			$this->db->where($where_array);
			return $this->db->update("widgets",$update_array);
		}
		
		return false;
	}
	
	/**
	 * This model function is used to delete NewsLetter
	 */
	function delete_newLetter($id = null){
		if($id)
			$this->db->delete('newsletters', array('id' => $id, 'company_id' => $this->company_id));
		
	}
	
	/**
	 * This function is used to fetch and return companies using mail manager
	 * @param int $companyId It is the ID of company whose subscription is to be fetched
	 * @return array $response Array of subscription info
	 */
	function get_subscription($where_array = array(),$join = false){
		$response = array();
		
		if(!empty($where_array)){
			foreach ($where_array as $cols => $vals){
				$this->db->where($cols,$vals);
			}
		}

		if($join){
			$this->db->select('mail_manager.*,company.company_name,company.username,company.password');
			$this->db->join('company','mail_manager.company_id = company.id');
		}
		
		$response = $this->db->get('mail_manager')->result_array();
		return $response;
	}
	
	/**
	 * This function is used to insert into mail manager table
	 * @param array $insert_array This is the array of cols and values to insert
	 */
	function insert_mail_manager($insert_array = array()){
		if(!empty($insert_array)){
			$this->db->insert('mail_manager',$insert_array);
		}
	}
	
	
	/**
	 * This function is used to update mail manager table
	 * @param array $where_array This is the array of cols and values for where clause
	 * @param array $update_array This is the array of cols and values for update clause
	 */
	function update_mail_manager($where_array = array(),$update_array = array()){
		if(!empty($where_array) && !empty($update_array)){
			$this->db->where($where_array);
			if($this->db->update('mail_manager',$update_array))
				return true;
			else 
				return true;
		}
	}
	
	/**
	 * This function is used to save sent mails info
	 * @param array $insert_array It is array of data to be save
	 */
	function insert_mails_send($insert_array = array()){
		if(!empty($insert_array)){
			$this->db->insert('mail_manager_sent_mail',$insert_array);
		}
	}
	
	/**
	 * This function is used to get all mails send for given month for any specific company
	 * @param array $where_array array of cols and values
	 * @param string $month month for which data to be fetched
	 * @return array $response array of sent mails
	 */
	function get_mail_send_month_wise($where_array = array(), $month = null){
		$response = array();
		
		if(!empty($where_array)){
			$this->db->where($where_array);
		}
		
		if($month){
			$this->db->like('date',$month,'after');
		}
		
		$response = $this->db->get('mail_manager_sent_mail')->result_array();
		
		return $response;
	}
	
	/**
	 * This function is used to retrieve Templates from db
	 * @param array $select_array It is the array of columns to be select from table
	 * @param array $where_array It is the array of columns name and their vlues used for Where clause
	 * @param array $order_by It is the array of Column name and values(ASC, DESC) for Order by clause
	 * @return array $templates It is the array of Templates that has to be return
	 */
	function get_templates($select_array = array(), $where_array = array(), $order_by = array() ){
	
		if(!empty($select_array)){
			$this->db->select(implode(",",$select_array));
		}
	
		if(!empty($where_array)){
			foreach($where_array as $column => $value){
				$this->db->where($column, $value);
			}
		}
	
		if(!empty($order_by)){
			foreach($order_by as $column => $value){
				$this->db->order_by($column, $value);
			}
		}else{
			$this->db->order_by("date", "DESC");
		}
	
		$newsLetter = $this->db->get("mail_templates")->result_array();
		//echo count($newsLetter);
		//echo $this->db->last_query();die;
	
		return $newsLetter;
	}
	
	/**
	 * This model function is used to delete template
	 * @param array $where_array It is the array of columns and values for the condition of deletion
	 */
	function delete_templates($where_array = array()){
		if(!empty($where_array)){
			foreach ($where_array as $cols => $vals)
				$this->db->where($cols, $vals);
			$this->db->delete("mail_templates");
		}
	}
	
	/**
	 * This function is used to save templates into db
	 * @param array $insert_array It is the array of columns to be inserted into table
	 * @return mixed It wil return either true or false
	 */
	function save_templates($insert_array = array()){
	
		if(!empty($insert_array)){
			return $this->db->insert('mail_templates',$insert_array);
		}else{
			return false;
		}
		
	}
	
	/**
	 * This model function is used to fetch count of subscribed/unsubscribed/bounced companies
	 */
	function get_company($select = null, $type = null, $count = false){
		
		if($select != null)
			$this->db->select($select);
		
		if($type != null)
			$this->db->where('mail_subscription',$type);
		
		if($count)
			return $this->db->count_all_results('company');
		else
			return $this->db->get('company')->result_array();
	}
	
	/**
	 * This function is used to insert image details of uploaded images in the upload center into db
	 * @param int $company_id This is the company id of client
	 * @param string $image_name This is the image name being uploded to the upload center
	 * @return mixed It wil return either true or false
	 */
	function insert_image_details($company_id = null, $image_name = null){
	
		if($company_id && $image_name){
			$insert_array = array(
				'company_id' => $company_id,
				'image_name' => $image_name
			);
			return $this->db->insert('upload_center_details',$insert_array);
		}else{
			return false;
		}
	
	}
	
	/**
	 * This function is used to fetch image details of uploaded images in the upload center
	 * @param int $company_id This is the company id of client
	 * @return $images It is the array of images that has to be return
	 */
	function get_uploaded_images($company_id = null){
		$images = array();
		
		if($company_id){
			$this->db->where('company_id',$company_id);
			$images = $this->db->get('upload_center_details')->result_array();
		}
		
		return $images;
	}
	
	/**
	 * This function is used to fetch doc details of uploaded docs in the upload center
	 * @param int $company_id This is the company id of client
	 * @return $images It is the array of images that has to be return
	 */
	function get_uploaded_docs($company_id = null){
		$docs = array();
	
		if($company_id){
			$this->db->where('company_id',$company_id);
			$docs = $this->db->get('upload_center_docs')->result_array();
		}
	
		return $docs;
	}
	
	/**
	 * This function is used to fetch doc details of uploaded docs in the upload center
	 * @param int $company_id This is the company id of client
	 * @return $images It is the array of images that has to be return
	 */
	function save_uploaded_doc($insert_array = array()){
		$insert_id = null;
		
		if(!empty($insert_array)){
			$this->db->insert('upload_center_docs',$insert_array);
			$insert_id = $this->db->insert_id();
			//$docs = $this->db->get('upload_center_docs')->result_array();
		}
	
		return $insert_id;
	}
	
	function get_template_by_id($tmp_id = 1){
		$this->db->select('content');
		$this->db->where('id',$tmp_id);
		return $this->db->get('mail_templates')->result_array();
		
	}
	
	function update_template($id = 1,$updt_array = array()){
		$this->db->where('id',$id);
		return $this->db->update('mail_templates',$updt_array);
		
	}
	
	function update_newsletters($id = 1,$updt_array = array()){
		$this->db->where('id',$id);
		return $this->db->update('newsletters',$updt_array);
	
	}
	function get_doc_name($d_id = NULL){
		$this->db->select('doc_name');
		$this->db->where('id',$d_id);
		return $this->db->get('upload_center_docs')->result_array();
	}
}
?>