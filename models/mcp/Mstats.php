<?php
class Mstats extends CI_Model
{ 
     
	function __construct()
    {
        parent::__construct();
    } 
	 
    /**
     * Function to get orders either from OBSShop or BESTELONLINE.NU
     * @param string $from This is used to specify order from OBSSHOP or from BESTELONLINE.NU
     * @param integer $limit This is the limit of rows to be fetch
     * @return array $response This is the array containing desired rows 
     */
	function get_latest_order($from = "obs", $limit = 0)
	{
		$response = array();
		
		$this->db->select('company.id as company_id, company.company_name, clients.id as client_id, clients.firstname_c, clients.lastname_c,orders.created_date');
		$this->db->join('company', 'company.id = orders.company_id');
		$this->db->join('clients', 'clients.id = orders.clients_id');
		if($from == "obs")
			$this->db->where('orders.from_bo', '0');
		else
			$this->db->where('orders.from_bo', '1');
		
		if($limit)
			$this->db->limit($limit);
		$this->db->order_by('orders.created_date', 'DESC');
		$response = $this->db->get('orders')->result();
		
		return $response;
	}
	
	/**
	 * Function to get latest mail sent in the system
	 */
	function get_latest_mail_sent($from = "obs", $limit = 0){
		$response = array();
		
			$this->db->select("
					email_logs.*,
					clients.firstname_c, clients.lastname_c,
					company.company_name, company.id as company_id,
					dep_partners.dep_first_name, dep_partners.dep_last_name,
					partners.p_first_name, partners.p_last_name,
					affiliates.a_first_name, affiliates.a_last_name,
				");
			
			$this->db->join("affiliates","email_logs.email_to = affiliates.a_email",'left');
			$this->db->join("partners","email_logs.email_to = partners.p_email",'left');
			$this->db->join("dep_partners","email_logs.email_to = dep_partners.dep_email",'left');
			$this->db->join("clients","email_logs.email_to = clients.email_c",'left');
			$this->db->join("general_settings","email_logs.email_to = general_settings.emailid",'left');
			//$this->db->join("company","email_logs.email_to = company.email",'left');
			$this->db->join("company","general_settings.company_id = company.id",'left');
			
			if($from == "obs")
				$this->db->where_in('email_logs.from_bo','0');
			elseif($from == "bo")
				$this->db->where_in('email_logs.from_bo','1');
			
			//$this->db->where_in('email_logs.to_type',array('company','client'));
			//$this->db->where('email_logs.id <','800');
			//$this->db->where('email_logs.id >','770');
			$this->db->group_by('email_logs.id');
			$this->db->order_by("email_logs.datetime", "DESC");
			if($limit)
				$this->db->limit($limit);
			$response = $this->db->get("email_logs")->result();
		
		return $response;
	}
	
	/**
	 * Function to get company info in the decending order of ORDERS placed
	 */
	function get_top_order_company($last_30_days = false, $limit = 0){
		//$query = "SELECT DISTINCT(`company_id`), `company`.`company_name`, COUNT(*) number FROM `company` JOIN `orders` on `orders`.`company_id` = `company`.`id` GROUP BY `orders`.`company_id` ORDER BY number DESC";
		$response = array();
		$this->db->select("DISTINCT(`company_id`), `company`.`company_name`, COUNT(*) number");
		$this->db->join("orders","orders.company_id = company.id");
		
		if($last_30_days)
			$this->db->where('`orders.created_date` BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
		
		//$this->db->like("orders.created_date",date("Y-m"));
		$this->db->group_by("orders.company_id");
		$this->db->order_by("number", "DESC");
		if($limit)
			$this->db->limit($limit);
		$response = $this->db->get("company")->result();
		return $response;
		
	}
	
	/**
	 * This function is used to fetch latest orders made for FREE companies from Bestelonline.nu
	 */
	function get_latest_order_free($limit = 0){

		$response = array();
		
		$this->db->select('company.id as company_id, company.company_name, clients.id as client_id, clients.firstname_c, clients.lastname_c,orders_bo.created_date');
		$this->db->join('company', 'company.id = orders_bo.company_id');
		$this->db->join('clients', 'clients.id = orders_bo.clients_id');
		if($limit)
			$this->db->limit($limit);
		$this->db->order_by('orders_bo.created_date', 'DESC');
		$response = $this->db->get('orders_bo')->result();
		
		return $response;
		
	}
	
	/**
	 * Function to get last login details of the admins (CP)
	 */
	function get_last_login_companies($limit = 0){
		$response = array();
		
		$this->db->select('company.id as company_id, company_name, last_login');
		$this->db->where('last_login !=','0000-00-00 00:00:00');
		
		if($limit)
			$this->db->limit($limit);
		$this->db->order_by('last_login', 'DESC');
		$response = $this->db->get('company')->result();
		
		return $response;
	}
}
?>