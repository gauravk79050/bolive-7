<html>
	<head>
	<?php if(isset($font_size)){?>
	<style type="text/css">
		body{
			font-family: calibri;
   			font-size: <?php echo $font_size; ?>pt;
		}
		table td {
		    font-size: <?php echo $font_size; ?>pt;
		    font-family: calibri;
		}
	</style>
	<?php }?>
	</head>
	<body>
		<?php echo "Dit order werd geannuleerd door de klant"; ?>
		<br /><br />
		<?php echo _('Here are some details of it'); ?>:
		<br /><br />
		
		<?php $this->load->view( 'mail_templates/print_order_custom' );?>
	</body>
</html>
