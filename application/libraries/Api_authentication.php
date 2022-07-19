<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Api_authentication 
{	
	var $flag;
	var $hash;

	function __construct()
	{
       $this->flag = 0;
	}

	function authenticate_login( $verification_data ){
		
		if( empty($verification_data) )
		  return $this->flag;
		
		$this->_ci =& get_instance();
			
		$this->_ci->db->where( 'api_id', $verification_data['api_id'] );
		$this->_ci->db->where( 'domain', $verification_data['domain'] );
		$verification_codes = $this->_ci->db->get('api')->result();	
		
		if( !empty($verification_codes) )
		{
			$api_secret = $verification_codes[0]->api_secret;
							
			$this->hash = hash_hmac('sha256','api_id='.$verification_data['api_id'].'!timestamp='.$verification_data['timestamp'].'!domain='.str_replace('www.','',$verification_data['domain']),$api_secret);
			
			if( $verification_data['hash'] == $this->hash ){
				$this->flag = 1;
			}			
		}
				
		return $this->flag;
	}
}
?>