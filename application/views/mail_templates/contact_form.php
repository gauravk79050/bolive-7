<html>
	<head>
	</head>
	<body>
		<span><?php echo $this->lang->line('hello_txt');?>,</span>
		<br/>
		<br/>
		<span><?php echo $this->lang->line('mail_txt1');?> <?php echo $company_url_portal;?>.</span>
		<br/>
		<br/>
		<span><strong><?php echo $this->lang->line('name_txt');?></strong> : <?php echo $fname.' '.$lname; ?></span>
		<br/>
		<span><strong><?php echo $this->lang->line('email_txt');?></strong> : <?php echo $email; ?></span>
		<br/>
		<span><strong><?php echo $this->lang->line('phone_txt');?></strong> : <?php echo $phone; ?></span>
		<br/>
		<br/>
		<span><strong><?php echo $this->lang->line('message_txt'); ?></strong></span>
		<br/>
		<span><?php echo $user_message;?></span>
		<?php if($register_user == 1){?>
		<br/>
		<span><?php echo $this->lang->line('mail_txt2');?></span>
		<?php }?>
		<br/>
		
		--
		<br/>
		<br/>
		<span><small><?php echo $this->lang->line('know_service_txt'); ?>&nbsp;:&nbsp;<a href="<?php echo $this->config->item('portal_url'); ?>diensten"><?php echo $this->config->item('portal_url'); ?>diensten</a></small></span>
		<br/>
		<br/>
		<span><small>Heeft u verder nog vragen of opmerkingen geefs ons maar een seintje!</small></span>
		<br/>
		<br/>
		<span><small>Met vriendelijke groeten,</small></span>
		<br/>
		<br/>
		<span><small>Bestelonline.nu</small></span>
		<br/>
		<span><small>Molenbergstraat 1</small></span>
		<br/>
		<span><small>2260 Westerlo</small></span>
		<br/>
		<span><small>0473/25.05.28</small></span>
		<br/>
		<br/>
		<span><small>Check ook</small></span>
		<br/>
		<span><small><a href="http://www.fooddesk.be">www.fooddesk.be</a></small></span>
		<br/>
		<span><small><a href="http://www.sitematic.be">www.SiteMatic.be</a></small></span>
		<br/>
		<br/>
		<span><i><small>Onze support-module kan u downloaden via <a href="http://www.sitematic.be/TeamViewerQS_nl.exe">http://www.sitematic.be/TeamViewerQS_nl.exe</a></small></i></span>
		<br/>
	</body>
</html>