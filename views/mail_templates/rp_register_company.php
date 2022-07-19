<html>
	<head>
	<style type="text/css">
	body{
		font-family: Calibri;
		font-size: 14.5px;
	}
	.footer{
		font-size: 12px;
	}
	</style>
	</head>
	<body>
		<p><?php echo _("Dear");?>,</p>
		
		<!-- <p><b><i>{mail_h_1}?</i></b></p>
		
		<p>{mail_p_2} :)</p> -->
		
		<p><?php echo _("We launched a portalsite called <a href=\"http://www.bestelonline.nu\">Bestelonline.nu</a> and we just added your info regarding your shop right here: "); ?> <a href="<?php echo $portal_url.$company_type_slug.'/'.$company_slug; ?>"><?php echo $portal_url.$company_type_slug.'/'.$company_slug; ?></a></p>
		
		<p>
			<?php echo _("You can manage every aspect of that detailpage easy for FREE").' '._("You can login at"); ?> <a href="<?php echo base_url(); ?>cp"><?php echo base_url(); ?>cp</a> <?php echo _("with login"); ?>:
			<br/>
			------
			<br />
			<b><?php echo _('Username').' = '.$username; ?></b>
			<br />
			<b><?php echo _('Password').' = '.$password; ?></b>
			<br/>
			------
			<br/>
			<?php echo _("(don't lose this info)"); ?>.
		</p>
		
		<p><b><i><?php echo _("Wait a minute.. this is spam right");?> ?</i></b></p>
		
		<p><?php echo _("Not at all. We are sending this mail to you personally to notify you that we have been working very hard the last 4 years on a portal and unique ordersystem build for shopowners like you. What makes it unique is that is build up with the latest techniques and we even can implement a webshop in your existing website by adding a few codes ("); ?> <?php echo _("check the video at"); ?> <a href="www.fooddesk.be">www.fooddesk.be</a>)<br> <?php echo _("Wouldn't it be fantastic if you could have a full featured webshop for your clients at a low price of 19€/mnth (no commissions) and where you don't have to look at your PC to handle your orders? Well.. we have it."); ?></p>
		
		<p><b><i><?php echo _("Hey.. wait a minute - do I have to pay anything here or even in the future");?> ?</i></b></p>
		
		<p><?php echo _("Not at all. The detailpage you see is for free for always and is meant to let your clients find info about your company very quickly when searching for keywords like <type> <companyname> (high searchengine rankings). Offcourse if you want to have a webshop in the near future you can upgrade your account. Please check our (cheap) plans: ");?> <a href="<?php echo $portal_url; ?>diensten"><?php echo $portal_url; ?>diensten</a> </p>
		
		<p><b><i><?php echo _("I don't need any webshop mate - my clients are emailing me already.");?>.</i></b></p>
		
		<p><?php echo _("That's right - but you still have to spend hours/day or week before your desktop to reply, manage all orders seperatly right? Also you have to collect all emailaddresses, names, phonenumbers seperatly if you want to send them a mail or contact them individually. With our system you don't even have to look at your PC as all orders can be printed out immediately after they came in and in case you want to send a mail to your clients, an advanced mailmanager is build in. A promotion or holiday? Just setup your mail and send it to everyone in 5 seconds. .. and a lot more advantages the system has.");?>.</p>
		
		<p><b><i><?php echo _("Sounds interesting - but I still have a question....");?></i></b></p>
		
		<p><?php echo _("If you are interested in our system we prefer a personal approach by having a meeting on some day (without any obligations). Please call us at 0473/250528 or fill in the form at "); ?> <a href="<?php echo $portal_url;?>contact_us"><?php echo $portal_url;?>contact_us</a> <?php echo _("so we can help you further"); ?>.</p>
		
		<p><?php echo _("Some FAQS you can also find at "); ?> <a href="<?php echo $portal_url;?>help"><?php echo $portal_url;?>help</a></p>
		
		<p><?php echo _("Don't hesitate - participate!");?></p>
		
		<p class="footer">Powered by <a href="http://www.fooddesk.be">FoodDESK</a></p>
	</body>
</html>