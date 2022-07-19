<html>
	<body>
		<p><?php echo _('An instant payment notification was successfully received from');?></p>
		<p><?php echo $this->paypal_lib->ipn_data['payer_email'] . ' on '.date('m/d/Y') . ' at ' . date('g:i A'); ?> </p>
		<br>
		<p><?php echo _(' Details'); ?>:</p>
		
		<?php foreach ($this->paypal_lib->ipn_data as $key=>$value){ ?>
			<p><?php echo $key.':'. $value; ?></p>
		<?php }?>
			
	</body>
</html>