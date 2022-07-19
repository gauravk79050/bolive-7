<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Panel Login</title>
<meta name="robots" content="noindex">
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
<!--<script src="<?php echo base_url(); ?>assets/cp/js/jquery.showhide.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/cp/js/shbar.js" type="text/javascript"></script>-->
<script src="<?php echo base_url(); ?>assets/cp/js/validator.js" type="text/javascript"></script>
<style>
.error{
	color:#FF0000;
}
</style>
</head>
<body>
<div align="center"><div id="login">
  <div id="login-header">
    <h1>FoodDESK<br/></h1>
    <p>&nbsp;</p>
  </div>
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
  <p align="center"></p>
  <p><?php echo _('Enter your username and password.')?></p>
  <?php echo form_open('cp/login/validate', array('name'=>"frm_login", 'id'=>"frm_login", 'method'=>"post")); ?>
	<?php echo form_hidden('OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW','default'); ?>
    <p>
      <label><b><?php echo _('Username:')?></b><br />
      <?php echo form_input(array('name'=>'username','id'=>'username','size'=>'30')); ?>
	  </label>
    </p>
    <p>
      <label><b><?php echo _('Password:')?></b><br />
      <?php echo form_password(array('name'=>'password','id'=>'password','size'=>'20')); ?>
	  </label>
    </p>
    <!--<p class="remember">
      <label>
      <input type="checkbox" na   me="rememberme" id="rememberme" value="forever" tabindex="30" />
      Remember Me</label>
    </p>-->
    <p class="submit">
      <?php echo form_input(array('type'=>'hidden','name'=>'act','id'=>'act','value'=>'do_login')); ?>
	  <?php echo form_submit(array('name'=>'submit','id'=>'btn_submit','value'=>'LOGIN','class'=>'btnWhiteBack')); ?>
	</p>
  <?php echo form_close(); ?>
  <script type="text/javascript">
		var frmvalidator = new Validator("frm_login");
		frmvalidator.EnableMsgsTogether();
		frmvalidator.addValidation("username","req","<?php echo _('Please Enter Username');?>");
		frmvalidator.addValidation("password","req","<?php echo _('Please Enter Password');?>");
	</script>
	 <!-- <p>&nbsp;</p><a href="<?php echo base_url()?>cp/login/forgot_password"><?php echo _('Forgot Password');?>?</a> -->

</div>
</div></body>
</html>

<?php 
	/*
	echo form_open('cp/login/validate');
	echo form_input('username', 'Username');
	echo form_password('password', 'Password');
	echo form_submit('submit', 'Login');
	echo form_close();
	*/
?>