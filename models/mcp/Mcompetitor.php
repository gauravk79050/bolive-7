<?php 
  class Mcompetitor extends CI_Model
  {
        function __construct()
	  	{  
			// Call the Model constructor
			parent::__construct();
			
      	}
		 
	    function add($name)
	    {
		   $q=$this->db->get_where('competitor',array('competitor_url'=>$name));
		   if(!(sizeof($q->result())))
		   $this->db->query("INSERT INTO competitor VALUES('','$name')");
	    }
	  
	    function update($id,$name)
	    {
	    
		  $this->db->query("UPDATE competitor SET competitor_url='$name' WHERE id= '$id' ");
		  
	    }
	 
	    function delete($id)
	    {
	  
	       $this->db->query("DELETE FROM competitor  WHERE id='$id'");
	   
	    }
     
	  function select($arr=array())
	    {
	      if($arr)
	      {
		    if($arr['search_by']=='id')
		    {   
		      $query=$this->db->get_where('competitor',array('id'=>$arr['search_keyword']));
		 	  return $query->result();
		    }
		    
			if($arr['search_by']=='competitor')
		    {  
		      //$query=$this->db->get_where('country',array('country_name'=>$arr['search_keyword']));
			  
			  $this->db->like('competitor_url',$arr['search_keyword']);
			  $query = $this->db->get('competitor');
			  return $query->result();
		    }
		  }
	      else
		  {
            $query = $this->db->get('competitor');
            return $query->result();
          }
       }
       function get_competitor()
       {
       	
       	$query = $this->db->get('competitor')->result_array();
       	if(!empty($query))
       	{
       	
       		return $query;
       	}
       	else 
       	{
       		return false;
       	}
       }
 
 }
?>
