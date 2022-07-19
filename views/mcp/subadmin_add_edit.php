<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php

if (isset($subid) && $subid !='' && $subid != 0 ) { ?>
  <script type="text/javascript">
    var base_url = "<?php echo base_url(); ?>"
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.7.1.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/general_functions.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/validator.js"></script>

  <?php
}
?>
<!-- start of main body -->
<div style="display:block!important;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-top:25px">
    <tr>
      <td align="center" valign="top"><table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" valign="top" style="border:#8F8F8F 1px solid">

            <?php $sub = ''; if(isset($subcompany)) { $sub = $subcompany[0]; } ?>

            <?php echo form_open('mcp/companies/subadmin_add_edit/'.$ID, array('id'=>'frm_subdomain_add_edit')); ?>

            <input type="hidden" name="approved" size="25" value="<?php if(isset($subcompany)) {echo ($sub->approved)?1:0; } else { echo 0; } ?>" />
            <input type="hidden" name="status" size="25" value="<?php if(isset($subcompany)) {echo ($sub->status)?1:0; } else { echo 0; } ?>" />

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
              <td align="center" style="padding:15px 0px 5px 0px"><table class="page_caption" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="94%" align="left"><h3>
                    <?php echo ( $subid != 0 && $subid != '')?"Edit ":"Add "; ?>Sub Domain</h3></td>
                    <td width="3%" align="right"></td>
                    <td width="3%" align="left"></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="center"><table width="98%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                   <td colspan="2" align="center"><span id="dup_msg" style="color:#FF0000"></span></td>
                 </tr>
                 <tr>
                  <td colspan="2" align="center"><span id="dup_msg_email" style="color:#FF0000"></span></td>
                </tr>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                  <td width="50%"><table width="100%" border="0" cellspacing="0" cellpadding="0">

                    <tr>
                      <td width="30%" height="30" class="wd_text">Company Name</td>
                      <td width="20%"><input type="text" name="company_name" size="25" value="<?php if(isset($subcompany))echo $sub->company_name; ?>" /></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">First Name</td>
                      <td><input type="text" name="first_name" size="25" value="<?php if(isset($subcompany))echo $sub->first_name; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">Last Name</td>
                      <td><input type="text" name="last_name" size="25" value="<?php if(isset($subcompany))echo $sub->last_name; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">Email</td>
                      <td><input type="text" name="email" id="email" size="25" value="<?php if(isset($subcompany))echo $sub->email; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">Phone</td>
                      <td><input type="text" name="phone" size="25" value="<?php if(isset($subcompany))echo $sub->phone; ?>"/></td>
                    </tr>
                  </table></td>
                  <td width="50%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="25%" height="30" class="wd_text">Address</td>
                      <td width="25%"><input type="text" name="address" size="25" value="<?php if(isset($subcompany))echo $sub->address; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">City</td>
                      <td><input type="text" name="city" size="25" value="<?php if(isset($subcompany))echo $sub->city; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">Zipcode</td>
                      <td><input type="text" name="zipcode" size="25" value="<?php if(isset($subcompany))echo $sub->zipcode; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">Username</td>
                      <td><input id="username" type="text" name="username" size="25" onchange="check_username(this.value);" value="<?php if(isset($subcompany))echo $sub->username; ?>"/></td>
                    </tr>
                    <tr>
                      <td height="30" class="wd_text">Password</td>
                      <td><input type="text" name="password" size="25" placeholder="New Password" value=""/></td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td align="center" style="padding:20px;"><input type="submit" name="btn_add_update" size="25" class="btnWhiteBack sub_admin_comp" value="<?php echo ($subid != 0 && $subid != '')?"UPDATE ":"TOEVOEGEN "; ?>" />
                <input type="hidden" name="act" value="add_edit" />
                <input type="hidden" name="ID" value="<?php echo $ID; ?>" />
                <input type="hidden" name="subid" value="<?php echo $subid; ?>" />
              </td>
            </tr>
          </table>
        </form>
        <script language="javascript" type="text/javascript">
          var email_dub = "<?php echo _('Email already taken'); ?>";
          var subid = "<?php if($subid != 0 && $subid != '') echo $subid; ?>";

          jQuery(document).on('change','#email',function (e) {
            check_email_comp(e);
          });

          var frmValidator = new Validator("frm_subdomain_add_edit");
          frmValidator.EnableMsgsTogether();
          frmValidator.addValidation("company_name","req","Please enter the Company Name");
          frmValidator.addValidation("first_name","req","Please enter the First Name");	
          frmValidator.addValidation("last_name","req","Please enter the Last Name");	
          frmValidator.addValidation("email","email","Please enter a valid Email Address");	
          frmValidator.addValidation("phone","req","Please enter the Phone Number");
          frmValidator.addValidation("address","req","Please enter the Address");	
          frmValidator.addValidation("zipcode","req","Please enter the Zipcode");	
          frmValidator.addValidation("city","req","Please enter the City");	
          frmValidator.addValidation("username","req","Please enter Username");	
          frmValidator.addValidation("password","req","Please enter Password");
          function check_email_comp(e) {
            if ( jQuery('#email').val() != '' ) {
              jQuery.ajax({
                url: '<?php echo base_url();?>mcp/companies/check_email',
                type:'POST',
                data:{
                  email: jQuery('#email').val(),
                  comp_id : subid ? subid : ''
                },
                async :false,
                success: function(data){
                  if ( data == 'duplicate_email' ) {
                    jQuery('#dup_msg_email').html(email_dub);      
                    jQuery('#email').val("");
                    jQuery('#email').focus();
                    e.preventDefault();
                  }
                  else {
                    jQuery('#dup_msg_email').html(''); 
                  }
                }
              });
            }
          }
        </script>
      </td>
    </tr>
  </table></td>
</tr>
</table>
</div>