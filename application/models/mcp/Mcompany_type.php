<?php
class Mcompany_type extends CI_Model
{ 
    
	 function __construct()
     {
        parent::__construct();
	    
     } 
	 
	 
	 //--function to add languages--//
	 function insert($params)
	 {
	     $query=$this->db->get_where('company_type',array('company_type_name'=>$params['company_type_name']));
		 if(!$query->result()){
		 	$slug_str = strtolower(trim($params['company_type_name']));
		 	
		 	$slug_str = preg_replace('/\s+/', '-', $slug_str);
		 	$slug_str = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $slug_str));
		 	$slug_str = preg_replace('/-+/', '-', $slug_str);
		 	$slug_str = rtrim($slug_str, "-");
		 	$this->db->query("INSERT INTO company_type(company_type_name,status,slug) VALUES('".$params['company_type_name']."','ACTIVE','".$slug_str."')");
		 }
		 
	 }
	 
	 
	 //--function to delete languages--//
	 function delete($id)
	 {
	     $this->db->query("DELETE FROM company_type WHERE id='".$id."'");
	 }
	 
	 
	 //--function to update languages--//
	 function update($params)
	 {
	 	$this->db->where("id" , $params['id']);
	 	unset($params['id']);
	 	unset($params['update']);
	     $this->db->update("company_type" , $params);
	 }
	  
	 //--function to list  languages--//
	 function select($arr=array())
	 {
	    if(sizeof($arr)>1)//--for the search form --//
		{  
	        if(isset($arr['search_by']) && $arr['search_by']=='id')
		    {   
		       $query=$this->db->get_where('company_type',array('id'=>$arr['search_keyword']));
			   return $query->result();
		    }
		    
			if(isset($arr['search_by']) && $arr['search_by']=='company_type')
		    {  
		       //$query=$this->db->get_where('company_type',array('company_type_name'=>$arr['search_keyword']));
			   
			   $this->db->like('company_type_name',$arr['search_keyword']);
			   $query = $this->db->get('company_type');
			   
			   return $query->result();
		    }

		    $query=$this->db->get_where('company_type',$arr);
	        return  $query->result();
		}
	    elseif($arr)//--for the update form--//
	    {
		    $query=$this->db->get_where('company_type',$arr);
	        return $query->result();
		}
		else//--for the simple listing--//
		{
		    $query=$this->db->get('company_type');
	        return $query->result();
		}
	 }
	 
	 //--function to update the status of a comapny--// 
	 function status_update($params)
	 {
	     $this->db->query("UPDATE company_type SET status='".$params['status']."' WHERE id='".$params['id']."' ");   
     }

     function get_company_type($params = array()){
     
     	if($params){
     			
     		foreach($params as $key=>$val){
     			$this->db->where(array($key=>$val));
     		}
     	}
     	$this->db->where(array('status' => 'ACTIVE'));
     	$query=$this->db->get('company_type');
     	//echo $this->db->last_query();
     	//print_r($query->result());
     	return ($query->result());
     
     }

     function save_theme_fr_type( $theme, $type_id ){
     	 $this->db->where('id', $type_id );
     	 $this->db->update( 'company_type', array( 'theme' => $theme ) );
     	 return $this->db->affected_rows();
     }

     function all_comp_grp($comp_id = 0){
     	$this->db->select('*');
     	return $this->db->get('company_type_group')->result_array();
     }
 }
?>