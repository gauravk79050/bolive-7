<html>
	<head></head>
	<body>
		<h1> <?php echo _("Dear").' '.$admin_name; ?> </h1>
		<h3> <?php echo _('A request has been done to subscribe for monthly basis.'); ?></h3>
		<h4><?php echo _('Currently this company has').' '.$clients.' '._('clients').'.'; ?></h4>
		<p> <?php echo _("Company ID"); ?>: <strong><?php echo $this->company->id; ?></strong></p>
		<p> <?php echo _("Company Name"); ?>: <strong><?php echo $this->company->company_name; ?></strong></p>
	</body>
</html>