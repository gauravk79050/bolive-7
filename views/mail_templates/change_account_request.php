<html>
	<head></head>
	<body>
		<h2><?php echo _("Dear Admin"); ?>,</h2>
		<p><?php echo $request_txt;?></p>
		<table border="0" cellspacing="0" cellpadding="2">
			<tr><td><b><?php echo _("Company Info: "); ?></b></td><td></td>
			<tr><td></td><td><b><?php echo _("Id: "); ?></b><?php echo $company_id; ?></td>
			<tr><td></td><td><b><?php echo _("Name: "); ?></b><?php echo $company_name; ?></td></tr>
					
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					
			<tr><td><b><?php echo _("Current account: "); ?></b></td><td></td>
			<tr><td></td><td><b><?php echo _("Id: "); ?></b><?php echo $current_ac_type_id; ?></td>
			<tr><td></td><td><b><?php echo _("Name: "); ?></b><?php echo $current_ac_type_title; ?></td></tr>
					
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					
			<tr><td><b><?php echo _("Desired account: "); ?></b></td><td></td>
			<tr><td></td><td><b><?php echo _("Id: "); ?></b><?php echo $desired_ac_type_id; ?></td>
			<tr><td></td><td><b><?php echo _("Name: "); ?></b><?php echo $desired_ac_type_title; ?></td></tr>
					
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<td align="left">
					Met vriendelijke groeten,<br />
					<?php echo $company_name; ?><br /><br />
				</td><td></td>
			</tr>
			<?php echo $email_ads; ?>
		</table>
	</body>
</html>