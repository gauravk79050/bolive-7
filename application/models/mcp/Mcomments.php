<?php 
class Mcomments extends CI_model{
	function __construct(){
		parent::__construct();
	}
	
	function get_comments($select = '', $param = array()){
		if($select != '')
			$this->db->select($select);
		if(!empty($param)){
			foreach ($param as $cols => $vals)
				$this->db->where($cols, $vals);
		}
		
		$this->db->join('company', 'company.id = comments.company_id');
		
		$this->db->order_by("commented_on", "desc");
		return $this->db->get('comments')->result();
	}
	
	function update_comment($where = array(), $update_array = array()){
		if(!empty($where) && !empty($update_array)){
			foreach ($where as $cols => $vals){
				$this->db->where($cols, $vals);
			}
			
			$this->db->update('comments', $update_array);
		}
	}
	
	function delete_comment($param = array()){
		if(!empty($param)){
			foreach ($param as $cols => $vals)
				$this->db->where($cols, $vals);
		}
		
		$this->db->delete('comments');
	}
	
}
?>