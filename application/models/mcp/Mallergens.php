<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MODEL : Allergenswords
 *
 * This model is used to manage Allergens Words
 * @package Mallergens
 * @author Abhay Hayaran <abhayhayaran@cedcoss.com>
*/

class Mallergens extends CI_model{

	function __construct(){
		parent::__construct();
	}
	
	/**
	 * This model function is used to fetch all allergens
	 */
	function get_allergens(){
		return $this->db->get('allergens_words')->result_array();
	}
	
	function insert($aller = array()){
		$response = false;
		
		if(!empty($aller)){
			$aller_arr = explode(',', $aller['allergens']);
			for($i = 0; $i< count($aller_arr); $i++){
				$data=array(
					'allergens_word'=>trim($aller_arr[$i]),
					'date_created'=>date('Y-m-d H:i:s')
				);
				$this->db->insert('allergens_words',$data);
				$response = true;
			}
		}
		return $response;
	}
	
	function update_allergens($id = NULL, $aller = NULL){
		if($id != NULL && $aller != NULL){
			$this->db->where('id',$id);
			$data=array(
				'allergens_word'=>trim($aller)
			);
			return $this->db->update('allergens_words',$data);
		}
	}
	
	function delete_allergens($a_id = NULL){
		if($a_id != NULL){
			$this->db->where('id',$a_id);
			return $this->db->delete('allergens_words');
		}
	}
}