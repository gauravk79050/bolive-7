<html>
	<head></head>
	<body>
		<p> <?php echo _("Dear").' '.$admin_name; ?> </p>
		<p> <?php echo _('A request has been done to buy a flyer. Its information is'); ?>:</p>
		<p> <?php echo _("Company ID"); ?>: <strong><?php echo $this->company->id; ?></strong></p>
		<p> <?php echo _("Company Name"); ?>: <strong><?php echo $company_name; ?></strong></p>
		<p> <?php echo _("Flyer ID"); ?>: <strong><?php echo $flyer_id; ?></strong></p>
		<p> <?php echo _("Flyer Name"); ?>: <strong><?php echo $flyer_name; ?></strong></p>
	</body>
</html>