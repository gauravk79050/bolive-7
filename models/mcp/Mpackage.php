<?php
class Mpackage extends CI_Model
{
	
	function __construct(){
		parent::__construct();
		
	}
	
	
	
	/*	Display the package	*/
	function select($id=NULL)
	{
		$sql = "SELECT * FROM `packages`";
	
		if($id)
		{
			$sql .= " WHERE `id`='".$id."'";
		}
		
		$query=$this->db->query( $sql );
		$content=array();
		
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$content[] = $row;
			}
		}
		
		return $content;
	}
	
	
	
	/*	Insert the package	*/
	function insert($params=array())
	{
		$query=$this->db->query("INSERT INTO `packages`(`package_name`,`package_desc`,`package_price`) VALUES(".$this->db->escape($params['package_name']).",".$this->db->escape($params['package_desc']).",'".$params['package_price']."')");
	
		return 1;
	}
	
	
	/*	Update the package	*/
	function update($params)
	{
		$query=$this->db->query("UPDATE `packages` SET `package_name` = ".$this->db->escape($params['package_name']).",
        `package_desc` = ".$this->db->escape($params['package_desc']).",
        `package_price` = '".$params['package_price']."'
		 WHERE id = '".$params['id']."'");
		 return 1;
	}
	
	
	/*	Delete the package	*/
	function delete($id=NULL)
	{
	    $this->db->query("DELETE FROM `packages` WHERE id='".$id."'");
		return 1;
	}

}
?>