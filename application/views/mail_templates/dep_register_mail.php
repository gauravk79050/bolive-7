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
		
		<!-- <p>{m_p_2}</p>
		
		<p>{m_p_3}</p> -->
		
		<p><?php echo _("We are impressed by your contribution to add companies in out portal and we want to collaborate with you in future also.");?>.</p>
		
		<p><?php echo _("So we have created a ");?> <a href="<?php echo base_url();?>dep"><?php echo _("Control Panel");?></a> <?php echo _(" for you to add more companies and earn some dime."); ?></p>
		
		<p><?php echo _("Your login details are right here:");?></p>
		
		<p><b><?php echo _("Username").": ".$dep_username;?>.</b></p>
		
		<p><b><?php echo _("Password").": ".$dep_password;?>.</b></p>
		
		<p><?php echo _("(don't lose this info)");?></p>
		
		<p><?php echo _("You can change your username and password after login.");?></p>
		
		<p class="footer">Powered by <a href="http://www.fooddesk.be">FoodDESK</a></p>
	</body>
</html>