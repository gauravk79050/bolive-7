<?php
class Mlanguages extends CI_Model
 { 
     function __construct()
     {
      parent::__construct();
	  
     } 
	 
	 
	 //--function to add languages--//
	 function insert($params)
	 {
	     $this->db->query("INSERT INTO language(lang_name,lang_code,flag,locale,language_file,language_file_2) VALUES('".$params['lang_name']."','".$params['lang_code']."','".$params['flag']."','".$params['locale']."','".$params['language_file']."','".$params['language_file_2']."')");
		 
		 return true;
	 }
	 
	 
	  //--function to delete languages--//
	 function delete($id)
	 {
	     
		 $language = $this->db->where('id',$id)->get('language')->result();
		 if(!empty($language)){
		 
		 	$flag_name = $language[0]->flag;
			
			$language_locale = $language[0]->locale;
		 	$language_file_name = $language[0]->language_file;
			$language_file_name_2 = $language[0]->language_file_2;
			
			$flag_filepath = dirname(__FILE__).'/../../../assets/mcp/images/lang-images/';
			$lang_filepath = dirname(__FILE__).'/../../language/locales/'.$language_locale.'/LC_MESSAGES/';
		 	
			@unlink($flag_filepath.$flag_name);
			@unlink($lang_filepath.$language_file_name);
			@unlink($lang_filepath.$language_file_name_2);
			
			$this->db->query("DELETE FROM language WHERE id='".$id."'");
		 	return true;
	 	}else{
			return false;
		}
	 }
	 
	 
	  //--function to update languages--//
	 function update($params){
		 $id = $params['id'];
		 $language = $this->db->where('id',$id)->get('language')->result();
		 if(!empty($language)){
		 
		 	$flag_name = $language[0]->flag;
			
		 	$language_locale = $language[0]->locale;
		 	$language_file_name = $language[0]->language_file;
			$language_file_name_2 = $language[0]->language_file_2;
			
			$flag_filepath = dirname(__FILE__).'/../../../assets/mcp/images/lang-images/';
			$lang_filepath = dirname(__FILE__).'/../../language/locales/'.$language_locale.'/LC_MESSAGES/';
		 	
			@unlink($flag_filepath.$flag_name);
			@unlink($lang_filepath.$language_file_name);
			@unlink($lang_filepath.$language_file_name_2);
		 }
		 
		 $update = '';
		 if(isset($params['lang_name']))
		 {
		    $update .= "`lang_name`='".$params['lang_name']."',";
		 }
		 
		 if(isset($params['lang_code']))
		 {
		    $update .= "`lang_code`='".$params['lang_code']."',";
		 }
		 
		 if(isset($params['flag']))
		 {
		    $update .= "`flag`='".$params['flag']."',";
		 }
		 
		 if(isset($params['language_file']))
		 {
		    $update .= "`language_file`='".$params['language_file']."',";
		 }
		 
		 if(isset($params['language_file_2']))
		 {
		    $update .= "`language_file_2`='".$params['language_file_2']."',";
		 }
		 
		 if($update)
			$update = substr($update,0,-1);
		 
		 $this->db->query("UPDATE language SET ".$update." WHERE id='".$params['id']."' ");
		 
		 return true;
	 }
	 
	 
	  //--function to list  languages--//
	 function select($arr=array())
	 {
	    if(sizeof($arr)>1)//--for the search form --//
		{  
		
	        if($arr['search_by']=='id')
		    {   
		       $query=$this->db->get_where('language',array('id'=>$arr['search_keyword']));
			   return $query->result();
		    }
		    
			if($arr['search_by']=='language')
		    {  
		      //$query=$this->db->get_where('language',array('lang_name'=>$arr['search_keyword']));
			  
			  $this->db->like('lang_name',$arr['search_keyword']);
			  $query = $this->db->get('language');
			  			  
			  return $query->result();
		    }
		
		}
	    else if($arr)//--for the updation --//
	    {
	         $query=$this->db->get_where('language',array('id'=>$arr['id']));
	         return $query->result();
	    }
		else//--for the simple listing--//
		{
		    $query=$this->db->get('language');
	        return $query->result();
		}  
	 }
	 
		
 }
?>
