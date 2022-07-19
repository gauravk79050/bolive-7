<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Panel Login</title>
<meta name="keywords" content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop"  />
<meta name="description" content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!"  />
<meta name="google-site-verification" content="8xMT5ro3-13nEZPiQ5gvi_CwjTc7kQeENeZlKT05aiE" />
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/css/calender.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/css/tipsy.css">
<?php if($this->router->class == 'login'): ?>
<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/login.css">
<?php endif; ?>
<script src="<?php echo base_url(); ?>assets/cp/js/1.4.2jquery.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/SpryMenuBar.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/color_functions.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/general_functions.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/jquery.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/jquery.showhide.js" type="text/javascript"></script>
<!--<script src="<?php echo base_url(); ?>assets/cp/js/shbar.js" type="text/javascript"></script>-->
<script src="<?php echo base_url(); ?>assets/cp/js/validator.js" type="text/javascript"></script>

</head>
<body>
<div id="login">
  <div id="login-header">
    <h1><?php echo _('Admin Password Recovery');?></h1>
  </div>
  <p>&nbsp;</p>
  
  <?php $messages = $this->messages->get();?>
  <?php if($messages != array()):?>
  <?php foreach($messages as $key => $val):?>	
 	<table cellspacing="0" cellpadding="0" border="0">
		<tbody><tr>
		
			<?php if($key=="success" && !empty($val)):?>
				
				<td align="left" width="24" valign="top"><img src="<?php echo base_url()?>assets/cp/images/success.gif" /></td>
				<td style="font-family: Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; color:#006600; font-weight:normal"><?php echo $val[0];?></td>
			<?php endif;?>	
			<?php if($key=="error" && !empty($val)):?>
		
			<td align="left" width="24" valign="top"><img src="<?php echo base_url()?>assets/cp/images/error.gif"></td>
			<td style="font-family: Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; color:#FF0000; font-weight:normal"><?php echo $val[0];?></td>
			<?php endif;?>
		</tr></tbody>
	</table>
  <?php endforeach;?>
  <?php endif;?>
  <p>&nbsp;</p>
  <p><?php echo _('Enter your email address in which you registered');?>.</p>
  <form name="frm_forgot_password" method="post" action="<?php echo base_url()?>cp/login/forgot_password">
  <p>
    <label><b><?php echo _('Email Address')?>:</b><br />
    <input type="text" class ="login-input" size= "30" name = "email"  id="email"/>
    </label>
  </p>
  <p class="submit">
    <input type="submit" value="<?php echo _('send');?>" name="btn_submit" id="btn_submit" />
    <input type = "hidden" value = "forgot_password" name="act" id="act" />
  </p>
 </form>
  <script type="text/javascript">
	var frmvalidator = new Validator("frm_forgot_password");
	frmvalidator.EnableMsgsTogether();
	frmvalidator.addValidation("email","req","<?php echo _('Please Enter Email ID');?>");
	frmvalidator.addValidation("email","email","<?php echo _('Plese Enter Valid Email Address');?>");
  </script>
  <p><a href="<?php echo base_url()?>cp/login">&laquo;<?php echo _('Login');?></a></p>
</div>
<div id="footer"> Copyright &copy;SITEMATIC 2015</div>
</body>
</html>