<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function set_translation_language($language)
{ 
    $lang_path = APPPATH.'language/locales';    
	putenv('LANG='.$language.'.UTF-8');    
	setlocale(LC_ALL, $language.'.UTF-8');    
	bindtextdomain('lang', $lang_path);    
	textdomain('lang');
} 

function defined_money_format($number = 0,$precision = 2)
{
	$number = round((float)$number, $precision);
	
	if($precision == 2)
		$number = money_format("%!^2n",(float)$number);
	else
		$number = money_format("%=*!^.".$precision."n",(float)$number);
	return $number;
}

$CI =& get_instance();
$segments = $CI->uri->segment_array();
if($segments[1] == 'mcp')
{
	$locale = 'nl_NL';
	setcookie('locale_mcp',$locale,time()+365*24*60*60,'/');
}
else
{
	if(!isset($_COOKIE['locale']))
	{
		$locale = 'nl_NL';
		$ci=& get_instance();
		$ci->load->database();
		
		$ci->load->library('session');
		$ci->db->where(array('company_id'=>$ci->session->userdata('cp_user_id')));
		$ci->db->join('language','language.id = general_settings.language_id','left');
		$query = $ci->db->get('general_settings')->row();

		if($query && $query->locale)
		{
		   $locale = $query->locale;
		   setcookie('locale',$locale,time()+365*24*60*60,'/');
		}
		else{
			setcookie('locale',$locale,time()+365*24*60*60,'/');
		}

	}
	else
	{
	   $locale = $_COOKIE['locale'];
	}
}

if($locale)
set_translation_language($locale);

$CI =& get_instance();
$CI->load->library('session');
$CI->load->helper('url');
$segments = $CI->uri->segment_array();
if( isset($segments[2]) && ('mcp' == $segments[1]) && ('mcp' != $CI->session->userdata('admin_role') ) && !in_array($segments[2], array('dashboard', 'companies', 'mcplogin','partners','package' ,'api','assignee','overview','autocontrole','easybutler' ))){
	redirect(base_url().'mcp/dashboard');
}
