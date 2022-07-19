<html>
	<body>
		<p><?php echo $this->lang->line('mail_dear').' '.$firstname_c; ?>,</p>
		<p><?php echo $this->lang->line('mail_welcome_msg'); ?></p>
		<p></p>
		<p><?php echo $this->lang->line('verification_message'); ?></p>
		<p><strong>-------------------------------------</strong></p>
		<p><strong><?php echo $this->lang->line('verification_code_txt'); ?> : <?php echo $verfication_code; ?></strong></p>
		<p><strong>-------------------------------------</strong></p>
		<p></p>
		<p><?php echo $this->lang->line('mail_sincerely'); ?>,</p>
		<p><?php echo $first_name.' '.$last_name; ?></p>
		<p><?php echo $company_name; ?></p>
		<p></p>
		<p></p>
		<p>
			<a href="http://www.fooddesk.be" target="_blank"><?php echo $this->lang->line('mail_powered_obs'); ?></a>
		</p>
	</body>
</html>