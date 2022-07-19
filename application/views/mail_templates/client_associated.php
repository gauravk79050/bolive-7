<html>
	<head></head>
	<body>
		<p><?php echo _('Dear'); ?> <?php echo $first_name; ?>,</p>
		<p><?php echo _('A new client has registered via your online shop.'); ?></p>
		<p></p>
		<p><strong><?php echo _('Client Details'); ?> :</strong></p>
		<p><strong>-------------------------</strong></p>
		<p><?php echo _('Name'); ?> : <?php echo $firstname_c; ?> <?php echo $lastname_c; ?></p>
		<p><?php echo _('Company'); ?> : <?php echo $company_c; ?></p>
		<p><?php echo _('I want a invoice'); ?> : <?php echo ( ($notifications == 'subscribe')?_("Yes"):_("No") ); ?></p>
		<p><?php echo _('VAT'); ?> : <?php echo $vat_c; ?></p>
		<p><?php echo _('Address'); ?> : <?php echo $address_c; ?> <?php echo $housenumber_c; ?>, <?php echo $postcode_c; ?> <?php echo $city_c; ?></p>
		<p><?php echo _('Telephone'); ?> : <?php echo $phone_c; ?></p>
		<p><?php echo _('GSM'); ?> : <?php echo $mobile_c; ?></p>
		<p><?php echo _('Email'); ?> : <?php echo $email_c; ?></p>
				
		<?php if($discount_num != ''){?>
		<p><?php echo _('Discount Card Number'); ?> : <?php echo $discount_num ?></p>
		<?php }?>
		<p></p>
		<p></p>
		<p><?php echo _('Powered by FoodDESK'); ?> - <a href="<?php echo base_url();?>cp">FoodDESK</a></p>
		<p><?php echo $this->config->item('site_admin_email'); ?></p>
	</body>
</html>