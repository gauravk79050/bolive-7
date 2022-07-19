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
		<h><?php echo _("Dear admin")?>,</h>
		
		<p><?php echo _("Following Data Entry Partner has requested for Payment");?></p>
		
		<p><b><?php echo _("ID");?> : </b><?php echo $dep_id;?></p>
		
		<p><b><?php echo _("Name");?> : </b><?php echo $dep_name;?></p>
		
		<p class="footer">Powered by <a href="http://www.fooddesk.be">FoodDESK</a></p>
	</body>
</html>