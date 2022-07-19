<html>
	<body>
		<p><?php echo $this->lang->line("mail_dear").' '.$firstname_c; ?>,</p>
		<p><?php echo $this->lang->line("mail_pswd_change_msg"); ?></p>
		<p></p>
		<p><?php echo $this->lang->line("mail_new_password"); ?> : <strong><?php echo $password_c; ?></strong></p>
		<p></p>
		<p></p>
		<p><?php echo $this->lang->line("mail_powered_obs"); ?> - <a href="http://www.FoodDESK.be">FoodDESK</a></p>
		<p><?php echo $this->config->item('site_admin_email'); ?></p>
	</body>
</html>