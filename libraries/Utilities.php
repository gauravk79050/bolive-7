<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	class Utilities {
		
		var $_ci;
		
		function __construct(){
			$this->_ci =& get_instance();
			$this->_ci->load->library('session');			
		}
		
		function parseMailText($Text, $Custom=""){	
			$GeneralKywords = array();
			$GeneralKywords["SERVER_ROOT"]=BASEPATH;
					
			$TemplateBody = $Text;

			$HTMLBody=$TemplateBody;
			
			if(is_array($Custom)){
				foreach($Custom as $Find=>$ReplaceWith){
					$TemplateBody = str_replace("{".$Find."}",$ReplaceWith,$TemplateBody);
				}
			}
			
			foreach($GeneralKywords as $Find=>$ReplaceWith){
				$TemplateBody = str_replace("{".$Find."}",$ReplaceWith,$TemplateBody);
			}

			return $TemplateBody;
		}
	}