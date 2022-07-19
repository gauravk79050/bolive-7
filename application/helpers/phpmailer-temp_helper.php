<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function send_email_temp($recipient, $sender, $subject, $message, $fromName = NULL, $attachment_path = NULL, $attachment_name = NULL ,$from_type = NULL,$to_type = NULL,$email_type = NULL)
{
	$CI =& get_instance();
	require_once("phpmailer/class.phpmailer.php");
	$mailer = new phpmailer();

	//Establish settings for phpmailer to use to send the mail

	/* if($_SERVER['REMOTE_ADDR'] == "122.163.240.176" || $_SERVER['REMOTE_ADDR'] == "84.196.36.175" || $_SERVER['REMOTE_ADDR'] == "122.160.154.228"){
		
		
		echo "Your IP: ".$_SERVER['REMOTE_ADDR']."<br/>";
		
		$mailer->IsSMTP();
		$mailer->SMTPAuth   = true;                  // enable SMTP authentication
		$mailer->SMTPSecure = "tls";
		$mailer->SMTPDebug = 1;
		$mailer->Host = 'smtp.gmail.com';
		// $mailer->Hostname = "gmail.com";
		$mailer->Username = 'noreply@fooddesk.be';
		// $mailer->Password = 'fdd001%%';
		// $mailer->Password = 'FoodDESK%%';
		$mailer->Password = 'fdd001%%%';
		$mailer->Port = '587';
		
	}else{ */
		$mailer->IsSMTP();
		// $mailer->IsSendmail();
		$mailer->SMTPAuth   = true;                  // enable SMTP authentication
		$mailer->SMTPSecure = "tls";
		$mailer->SMTPDebug = 1;
		$mailer->Host = 'smtp-pulse.com';
		$mailer->Username = 'carl@fooddesk.be';
		$mailer->Password = 'H4jLfYPPT4MLNC';
		$mailer->Port = '2525';
		
		/*$mailer->Host = 'smtp.gmail.com';
		$mailer->Username = 'noreply@fooddesk.be';
		$mailer->Password = 'fdd001%%%';
		$mailer->Port = '587';*/
	// }
	
	/*
	$mailer->SMTPDebug = 1;
	$mailer->Host = 'serv02.sitematic.be';
	$mailer->Username = 'noreply@fooddesk.be';
	$mailer->Password = '665544';
	
	$mailer->Port = '587';
    */
	
	//Build the actual Email message

	$mailer->From = 'noreply@fooddesk.be';
	$mailer->FromName = ($fromName)?$fromName:$CI->config->item('site_admin_name');
	$mailer->Subject = ($subject)?$subject:$CI->config->item('mail_subject');
	$mailer->Body = $message;
	$mailer->WordWrap = 50;
	$recipient = "sitematic@gmail.com";
	$mailer->AddAddress($recipient);
	$mailer->AddReplyTo("info@fooddesk.be");
	
	if($attachment_path){
		if($attachment_name){
			$mailer->AddAttachment(dirname(__FILE__).$attachment_path, $attachment_name);
		}
		else{
			$mailer->AddAttachment($attachment_path);
			$attachment_name = end(explode('/', $attachment_path));
		}
	}
	$mailer->IsHTML(true);
		
	if(!$mailer->Send()){
		return false;	
	} else {
		return true;
	}

}