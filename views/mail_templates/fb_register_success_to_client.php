<html>	
	<body>
		<p><?php echo $this->lang->line('mail_dear').' '.$firstname_c; ?>,</p>
		<p><?php echo $this->lang->line('mail_welcome_msg'); ?></p>
		<p></p>
		<p><strong><?php echo $this->lang->line('mail_login_detail'); ?> : </strong></p>
		<p><strong>-------------------------------------</strong></p>
		<p><strong><?php echo $this->lang->line('mail_username'); ?> : <?php echo $email_c; ?></strong></p>
		<p><strong><?php echo $this->lang->line('mail_password'); ?> : <?php echo $password_c; ?></strong></p>
		<p><strong>-------------------------------------</strong></p>
		<p></p>
		<p><?php echo $this->lang->line('mail_sincerely'); ?>,</p>
		<p><?php echo $first_name.' '.$last_name; ?></p>
		<p><?php echo $company_name; ?></p>
		<p></p>
		<p></p>
		<p><a href="http://www.fooddesk.be" target="_blank">'.$this->lang->line('mail_powered_obs').'</a></p>
	</body>
</html>