<?php 
class Mgroups extends CI_Model{
	
	var $company_id;
	
	function __construct(){
			
		parent::__construct();
		$this->company_id=$this->session->userdata('cp_user_id');
	
	}
	function get_groups($params=array()){
		
		
		$check = false;
		$check_person = false;
		
		if($params){
			foreach($params as $key=>$val){
			 
				if($key=='type' && $val==1)
				  $check = true;
				
				if($key=='type' && $val==2)
					$check_person = true;
				  
				$this->db->where(array($key=>$val));
			}
		}
		else{
		   $this->db->where(array('company_id'=>$this->company_id));
		}
		
		$this->db->where('group_name !=','');
		//$this->db->order_by('group_name asc');
		$this->db->order_by('display_order asc');
		
		$query = $this->db->get('groups');
		$groups = $query->result();
		
		if( empty($groups) && $check_person )
		{
			$this->db->where(array('company_id'=>$this->company_id,'type'=>2));
		
			$group_wt = $this->db->get('groups')->result();
			 
			if(empty($group_wt))
			{
				for($i=1;$i<=10;$i++)
					$this->db->insert('groups',array('company_id'=>$this->company_id,'group_name'=>'','type'=>2));
			}
		}	
		
		if( empty($groups) && $check )
		{
		   $this->db->where(array('company_id'=>$this->company_id,'type'=>1));
			 
		   $group_wt = $this->db->get('groups')->result();
		   
		   if(empty($group_wt))
		   {
		     for($i=1;$i<=10;$i++)
		       $this->db->insert('groups',array('company_id'=>$this->company_id,'group_name'=>'','type'=>1));
		   }
		    
		}
		
		return $groups;
	}
	
	/*this function is used to create 10 rows in groups table(for settings)*/
	function do_group_settings($groups){
		
		$insert_id = array();//this will store all ids created
		for($i=0;$i<=9;$i++){//we want 10 rows related to a perticular company
	   		$this->db->insert('groups',$groups);
	   		$insert_id[] = $this->db->insert_id();
		}
		return $insert_id;
	}
	
	function update_groups()
	{
	    $result = '';	   
	
		if($this->input->post('type') && $this->input->post('type') == 1)
		{
		    $company_wt_groups = $this->db->get_where('groups',array('company_id'=>$this->company_id,'type'=>1))->result();
			$count = count($company_wt_groups);
			$group_wt = $this->input->post('group_wt_name');
			
			if( is_array($group_wt) && !empty($group_wt)){
				if( $count==10 && $this->input->post('act')=="group_wt_edit" )
				{
					$counter = 1;
					foreach($group_wt as $key => $val){
						$this->db->where(array('id'=>$key, 'company_id'=>$this->company_id));
						$result[] = $this->db->update('groups',array('group_name'=>$val,'type'=>1, 'display_order' => $counter));
						$counter++;
					}
				}else if($count==0){
					for($count_group=0;$count_group<=9;$count_group++){
						$result = $this->db->insert('groups',array('company_id'=>$this->company_id,'group_name'=>$group_wt[$count_group], 'type'=>1, 'display_order' => ($count_group+1)));
					}
				}	
			}
			
		}else if($this->input->post('type') && $this->input->post('type') == 2)
		{
		    $company_person_groups = $this->db->get_where('groups',array('company_id'=>$this->company_id,'type'=>2))->result();
			$count = count($company_person_groups);
			$group_person = $this->input->post('group_person_name');
			
			if( is_array($group_person) && !empty($group_person)){
				if( $count==10 && $this->input->post('act')=="group_person_edit" )
				{
					$counter = 1;
					foreach($group_person as $key => $val){
						$this->db->where(array('id'=>$key, 'company_id'=>$this->company_id));
						$result[] = $this->db->update('groups',array('group_name'=>$val,'type'=>2, 'display_order' => $counter));
						$counter++;
					}
				}else if($count==0){
					for($count_group=0;$count_group<=9;$count_group++){
						$result = $this->db->insert('groups',array('company_id'=>$this->company_id,'group_name'=>$group_person[$count_group],'type'=>2, 'display_order' => ($count_group+1)));
					}
				}				
			}
		}
		else
		{
			$company_gorups=$this->db->get_where('groups',array('company_id'=>$this->company_id,'type'=>0))->result();
			$count=count($company_gorups);
			$group = $this->input->post('group_name');
			
			if( is_array($group) && !empty($group)){
				//----------if that company's rows alreay exist then update them------------//
				if($count == 10 && $this->input->post('act')=="group_edit" ){
					$counter = 1;
					foreach($group as $key => $val){
						$this->db->where(array('id'=>$key, 'company_id'=>$this->company_id));
						$result[] = $this->db->update('groups',array('group_name'=>$val,'type'=>0, 'display_order' => $counter));
						$counter++;
					}
				}else if($count==0){
					for($count_group=0;$count_group<=9;$count_group++){
						$result = $this->db->insert('groups',array('company_id'=>$this->company_id,'group_name'=>$group[$count_group], 'display_order' => ($count_group+1)));
					}
				}
			}
			
		}
		
		if($result && !empty($result) ){
			return array('success' => _('Groups Has Been Updated Successfully') );
		}
		if($result && count($result) == 10){
			return array('success' => _('Groups Has Been Updated Successfully') );
		}else{
			return array('error' => _('Error Occured in updation. Please try again !') );
		}
	
	}// end of update function 	
	
	function for_insert_update_group( $where_arr = array(), $update_arr = array() )
	{
		if( empty($update_arr) )
		  return false;
		
		if( !empty( $where_arr ) )
		  foreach ( $where_arr as $col => $val )
			 $this->db->where( $col , $val );
		
		$group = $this->db->get('groups')->row();
		
		if( !empty($group) )
		{
			$group_id = $group->id;
			
			$this->db->where( 'id' , $group_id );
			if( $this->db->update('groups', $update_arr ) )
			  return $group_id;
			else
			  return false;
		}
		else
		{
		   return false;
		}
	}
	
	/**
	 * This is new function to fetch group because of some bugs using previous function <get_groups>
	 */
	function get_groups_new($params=array()){
	
	
		$check = false;
		$check_person = false;
	
		if($params){
			foreach($params as $key=>$val){
				$this->db->where(array($key=>$val));
			}
		}
		else{
			$this->db->where(array('company_id'=>$this->company_id));
		}
	
		$this->db->order_by('display_order asc');
	
		$query = $this->db->get('groups');
		$groups = $query->result();
	   	return $groups;
	}
}	
?>