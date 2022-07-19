<html>
	<head>
	</head>
	<body>
		<h1>Dear Carl,</h1>
		<p>A new upgrade request has been made from a Company</p>
		
		<p>--------------</p>
		<p>Company ID: <?php echo $company_id;?></p>
		<p>Current Account Type: <?php if($current_ac_type_id == 1){ echo "FREE"; }elseif($current_ac_type_id == 2){ echo "BASIC"; }elseif($current_ac_type_id == 3){ echo "PRO"; }elseif($current_ac_type_id == 4){ echo "FoodDESK BASIC"; }elseif($current_ac_type_id == 5){ echo "FoodDESK PRO"; }elseif($current_ac_type_id == 6){ echo "FoodDESK PREMIUM"; }elseif($current_ac_type_id == 7){ echo "FoodDESK LIGHT"; }?></p>
		<p>Requested Account Type: <?php if($requested_ac_type_id == 1){ echo "FREE"; }elseif($requested_ac_type_id == 2){ echo "BASIC"; }elseif($requested_ac_type_id == 3){ echo "PRO"; }elseif($requested_ac_type_id == 4){ echo "FoodDESK BASIC"; }elseif($requested_ac_type_id == 5){ echo "FoodDESK PRO"; }elseif($requested_ac_type_id == 6){ echo "FoodDESK PREMIUM"; }elseif($requested_ac_type_id == 7){ echo "FoodDESK LIGHT"; }?></p>
		<p>--------------</p>
	</body>
</html>