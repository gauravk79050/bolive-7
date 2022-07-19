<html>
	<head></head>
	<body>
		<h2><?php echo _("Dear Admin"); ?>,</h2>
		<p><?php echo $request_txt;?></p>
		<p><?php echo _("From Company: "); ?>  <b><?php echo $company_name; ?></b></p>
		<p><?php echo _("For addon: "); ?>  <b><?php echo $addon_title; ?></b></p>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr>
				<td align="left">
					Met vriendelijke groeten,<br />
					<?php echo $company_name; ?><br /><br />
				</td>
			</tr>
			<?php echo $email_ads; ?>
		</table>
	</body>
</html>