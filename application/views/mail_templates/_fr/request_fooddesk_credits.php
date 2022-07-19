<html>
	<head></head>
	<body>
		<h2><?php echo _("Dear Admin"); ?>,</h2>
		<p><?php echo _('This company has requested to add fooddesk products credits ').$credit.".";?></p>
		<table border="0" cellspacing="0" cellpadding="2">
		
			<tr><td><b><?php echo _("Company Info: "); ?></b></td><td></td>
			<tr><td></td><td><b><?php echo _("Id: "); ?></b><?php echo $this->company->id; ?></td>
			<tr><td></td><td><b><?php echo _("Name: "); ?></b><?php echo $this->company->company_name; ?></td></tr>
					
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<td align="left">
					Met vriendelijke groeten,<br />
					<?php echo $this->company->company_name; ?><br /><br />
				</td><td></td>
			</tr>
		</table>
	</body>
</html>