<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function send_email($recipient, $sender, $subject, $message, $fromName = NULL, $attachment_path = NULL, $attachment_name = NULL ,$from_type = NULL,$to_type = NULL,$email_type = NULL,$reply_to_client = 0,$client_email= '')
{
	$CI =& get_instance();
	//if($_SERVER['REMOTE_ADDR'] != '122.160.154.227'){
	
	require_once 'phpmailer/PHPMailerAutoload.php';
	
	$mailer = new PHPMailer;
	
	$mailer->SetLanguage( 'en', 'phpmailer/language/' );
	
	//$mailer->SMTPDebug = 3;                               // Enable verbose debug output
	
	$mailer->isSMTP();                                      // Set mailer to use SMTP
	//if($_SERVER['REMOTE_ADDR'] != '122.160.154.228'){
	$mailer->Host = 'smtp-pulse.com';  // Specify main and backup SMTP servers
	$mailer->SMTPAuth = true;                               // Enable SMTP authentication
	$mailer->Username = 'carl@fooddesk.be';                 // SMTP username
	$mailer->Password = 'H4jLfYPPT4MLNC';                           // SMTP password
	
	$mailer->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
	$mailer->Port = 2525;                                    // TCP port to connect to
	$mailer->SMTPOptions = array(
			'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
			)
	);
	//}
	/*else{
		$mailer->SMTPDebug = 3;
		$mailer->SMTPAuth = true;
		$mailer->Host = 'mail.smtp2go.com';
			$mailer->Username = 'sitematic@gmail.com';
			$mailer->Password = 'cedcoss665544';
			$mailer->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
			$mailer->Port = 2525;
	}*/
	
	$smtp_used = "sendpulse";
	if (($reply_to_client && $client_email !='')){
		$mailer->addReplyTo($client_email,'');
	}
	else {
		$mailer->addReplyTo(($sender)?$sender:$CI->config->item('reply_email'),($fromName)?$fromName:$CI->config->item('site_admin_name'));
	}
	
	$mailer->setFrom($CI->config->item('no_reply_email'), $CI->config->item('site_admin_name'));
	//$mailer->setFrom(($sender)?$sender:$CI->config->item('reply_email'), ($fromName)?$fromName:$CI->config->item('site_admin_name'));
	$mailer->addAddress($recipient, '');     // Add a recipient
	//$mailer->addAddress('ellen@example.com');               // Name is optional
	
	//$mailer->addCC('cc@example.com');
	//$mailer->addBCC('bcc@example.com');
	if($attachment_path){
		if($attachment_name){
			$mailer->addAttachment(dirname(__FILE__).$attachment_path, $attachment_name);
		}
		else{
			$mailer->addAttachment($attachment_path);
			$attachment_name = end(explode('/', $attachment_path));
		}
	}
	
	$mailer->isHTML(true);                                  // Set email format to HTML
	
	$mailer->Subject = ($subject)?$subject:$CI->config->item('mail_subject');
	$mailer->Body    = $message;
	$mailer->WordWrap = 50;
	//$mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';

	/*Log data array*/
	$log_data = array(
			'email_from'	=> $sender,
			'email_to'		=> $recipient,
			'from_type'		=> (isset($from_type)?$from_type:''),
			'to_type'		=> (isset($to_type)?$to_type:''),
			'email_type'	=> (isset($email_type)?$email_type:''),
			'subject'		=> $subject,
			'message'		=> $message,
			'datetime'		=> date('Y-m-d H:i:s',time()),
			'smtp_used'		=> $smtp_used,
			'attachment'	=> (isset($attachment_name)?$attachment_name:'')
	);

	if($from_type == "no_reply_bo")
		$log_data['from_bo'] = 1;
	
	if(!$mailer->Send()){
		/* Log data */
		$log_data = json_encode($log_data);
		$log_data .= "\n-------------------------------------------------------------------\n";
		$myfile = fopen ( FCPATH . 'mail_error.txt', "a" ) or die ( "Unable to open file!" );
		fwrite ( $myfile, $log_data );
		fclose ( $myfile );
		return false;
	} else {
		$CI->db->insert('email_logs',$log_data);
		return true;
	}
	//}
	/*else{
		
		require "phpmailerr/class.phpmailer.php";
		$mailerr = new phpmailer();
		
		//$mailerr->IsSMTP();
		$mailerr->Host = 'outbound.hostedby.eu';
		$smtp_used = "outboubd";
		
		//Establish settings for phpmailer to use to send the mail
		
		$mailerr->From = ($sender)?$sender:$CI->config->item('reply_email');
		$mailerr->FromName = ($fromName)?$fromName:$CI->config->item('site_admin_name');
		$mailerr->Subject = ($subject)?$subject:$CI->config->item('mail_subject');
		$mailerr->Body = $message;
		$mailerr->WordWrap = 50;
		
		$mailerr->AddAddress($recipient);
		if (($reply_to_client && $client_email !=''))
		{
			$mailerr->AddReplyTo($client_email);
		}
		else {
			$mailerr->AddReplyTo(($sender)?$sender:$CI->config->item('reply_email'));
		}
		
		if($attachment_path){
			if($attachment_name){
				$mailerr->AddAttachment(dirname(__FILE__).$attachment_path, $attachment_name);
			}
			else{
				$mailerr->AddAttachment($attachment_path);
				$attachment_name = end(explode('/', $attachment_path));
			}
		}
		$mailerr->IsHTML(true);
		$mailerr->SMTPDebug  = 3;
		
		$log_data = array(
				'email_from'	=> $sender,
				'email_to'		=> $recipient,
				'from_type'		=> (isset($from_type)?$from_type:''),
				'to_type'		=> (isset($to_type)?$to_type:''),
				'email_type'	=> (isset($email_type)?$email_type:''),
				'subject'		=> $subject,
				'message'		=> $message,
				'datetime'		=> date('Y-m-d H:i:s',time()),
				'smtp_used'		=> $smtp_used,
				'attachment'	=> (isset($attachment_name)?$attachment_name:'')
		);
		
		if($from_type == "no_reply_bo")
			$log_data['from_bo'] = 1;
		
		if(!$mailerr->Send()){
			return false;
		} else {
			$CI->db->insert('email_logs',$log_data);
			return true;
		}
	}*/
}