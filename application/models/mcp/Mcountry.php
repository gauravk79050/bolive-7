<?php 
  class Mcountry extends CI_Model
  {
        function __construct()
	  	{  
			// Call the Model constructor
			parent::__construct();
			
      	}
		 
	    function add($name)
	    {
		   $q=$this->db->get_where('country',array('country_name'=>$name));
		   if(!(sizeof($q->result())))
		   $this->db->query("INSERT INTO country VALUES('','','$name','','','')");
	    }
	  
	    function update($id,$name)
	    {
	    
		  $this->db->query("UPDATE country SET country_name='$name' WHERE id= '$id' ");
		  
	    }
	 
	    function delete($id)
	    {
	  
	       $this->db->query("DELETE FROM country  WHERE id='$id'");
	   
	    }
     
	    function select($arr=array())
	    {
	      if($arr)
	      {
		    if($arr['search_by']=='id')
		    {   
		      $query=$this->db->get_where('country',array('id'=>$arr['search_keyword']));
		 	  return $query->result();
		    }
		    
			if($arr['search_by']=='country')
		    {  
			  $this->db->like('country_name',$arr['search_keyword']);
			  $query = $this->db->get('country');
			  return $query->result();
		    }
		  }
	      else
		  {
		  	if( $this->router->fetch_class() != 'country' )
		  		$this->db->where_in( 'id', array( '21', '150' ) );
            $query = $this->db->get('country');
            return $query->result();
          }
       }
       
	/**
	 * This model function is used to return city name of given postcode
	 * @param $postcode PostCode of the city
	 * @return array an array containg information about the city.
	 */	
 	function get_city($postcode = null){
 		$return_array = array();
		if($postcode){
			$return_array = $this->db->get_where("postcodes" , array("post_code" => $postcode))->result_array();
		} 
		return $return_array;	
 	}
}
?>
