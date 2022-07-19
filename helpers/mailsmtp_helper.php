<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function mail_config(){
	$mailConfig = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'port80.smtpcorp.com',
			'smtp_port' => '80',
			'smtp_user' => 'sitematic@gmail.com',
			'smtp_pass' => 'cedcoss665544',
			'mailtype'  => 'html'
	);
	
	return $mailConfig;
}

	
function send_smtp_email($recipient, $cc = null, $bcc = null, $sender, $subject, $message, $fromName = NULL, $attachment_path = NULL, $attachment_name = NULL )
{
	$CI =& get_instance();
	require_once("phpmailer/class.phpmailer.php");
	$mailer = new phpmailer();

	//Establish settings for phpmailer to use to send the mail
	$mailer->Mailer = "smtp";
	$mailer->Host = "port80.smtpcorp.com";
	
	//Enter your SMTP2GO account's SMTP server.
	
	$mailer->Port = "80";
	// 8025, 587 and 25 can also be used. Use Port 465 for SSL.
	
	$mailer->SMTPAuth = true;
	//$mail->SMTPSecure = 'ssl';
	// Uncomment this line if you want to use SSL.
	
	$mailer->Username = "sitematic@gmail.com";
	$mailer->Password = "cedcoss665544";
	
	/*
	 $mailer->Port = '587';
	*/

	//Build the actual Email message

	$mailer->From = $sender;
	$mailer->FromName = ($fromName)?$fromName:$CI->config->item('site_admin_name');
	$mailer->Subject = ($subject)?$subject:$CI->config->item('mail_subject');
	$mailer->Body = $message;
	$mailer->WordWrap = 50;

	if(is_array($cc) && !empty($cc)){
		foreach ($cc as $ccadd){
			$mailer->AddCC($ccadd);
		}
	}
	
	if(is_array($bcc) && !empty($bcc)){
		foreach ($bcc as $bccadd){
			$mailer->AddBCC($bccadd);
			//$mailer->AddCC($bccadd);
		}
	}
	
	$mailer->AddAddress($recipient);
	$mailer->AddReplyTo(($sender)?$sender:$CI->config->item('reply_email'));

	if($attachment_path){
		if($attachment_name)
			$mailer->AddAttachment(dirname(__FILE__).$attachment_path, $attachment_name);
		else
			$mailer->AddAttachment($attachment_path);
	}
	$mailer->IsHTML(true);
	if(!$mailer->Send()){
		return false;
	} else {
		return true;
	}

}
