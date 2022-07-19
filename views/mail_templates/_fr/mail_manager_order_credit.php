<html>
	<head></head>
	<body>
		<h1> <?php echo _("Dear").' '.$admin_name; ?> </h1>
		<h3> <?php echo _('A request has been done to buy credits for sending newsletters.'); ?></h3>
		<p> <?php echo _("Company ID"); ?>: <strong><?php echo $this->company->id; ?></strong></p>
		<p> <?php echo _("Company Name"); ?>: <strong><?php echo $this->company->company_name; ?></strong></p>
		<p> <?php echo _("Number of credits"); ?>: <strong><?php echo $credits; ?></strong></p>
	</body>
</html>