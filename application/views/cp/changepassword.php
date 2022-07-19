<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Change Password')?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Change Password')?></span> </div>
      <?php if($msg&&$msg=="password changed successfully"):?><div id="notice"><strong><?php echo $msg;?></strong></div><?php endif;?>
    <div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Profile')?></h3>
          		<div class="inside">
              		<form action="<?php echo base_url()?>cp/cdashboard/changepassword" enctype="multipart/form-data" method="post" id="change_password" name="change_password">
						<input type="hidden" value="change_password" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
              			<div class="form-item">
                			<label><?php echo _('Current Password');?></label>
              				<input type="password" class="text short" id="current_password" name="current_password"><span class="field-error"><?php if($msg&&$msg=="current password is not correct"):?><?php echo $msg;?><?php endif;?></span>
						</div>
              			<div class="form-item">
                			<label><?php echo _('New Password');?></label>
              				<input type="password" class="text short" id="new_password" name="new_password">
						</div>
              			<div class="form-item">
                			<label><?php echo _('Confirm Password');?></label>
              				<input type="password" class="text short" id="confirm_password" name="confirm_password"><span class="field-error"> <?php if($msg&&$msg=="new password dosent match"):?><?php echo $msg;?><?php endif;?></span>
						</div>
              				<input type="hidden" value="set_password" id="act" name="act"><input type="submit" value="Change Password" class="submit" id="btn_submit" name="btn_submit">
					</form>			 
			  		<script type="text/javascript">
						var frmvalidator = new Validator("change_password");
						frmvalidator.EnableMsgsTogether();
						frmvalidator.addValidation("current_password","req","<?php echo _('Enter the current password please');?>");
						frmvalidator.addValidation("new_password","req","<?php echo _('Enter your new password');?>");
						frmvalidator.addValidation("confirm_password","req","<?php echo _('Please confirm your new password');?>");
			  		</script>
			  <p>&nbsp;</p>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
