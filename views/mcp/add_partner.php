<script type="text/javascript">
	var upload="<?php echo _('Upload Logo');?>";
	var cropping = "<?php echo _('Cropping');?>";
</script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css?version=<?php echo version;?>" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css?version=<?php echo version;?>" type="text/css" />
<style type="text/css">
.textbox{
	width:200px;
}
#uploaded_image8 {
  margin-left: 320px;
}
</style>

<script type="text/javascript">
// ======  START: CHECKING DUPLICATE EMAIL EXISTS===================//
function check_email(email){
	var u_email = email;
	var $jq=jQuery.noConflict();
	$jq(document).ready(function(){
		$jq.ajax({
		   type: "POST",
		   url: base_url+"mcp/partners/validate",
		   dataType: 'json',
		   data: 'email='+u_email+'&check=email',
		   success: function(response){
			   if(response.RESULT == "duplicate"){				  
				  $jq('#dup_msg').html('<img border="0" src="'+base_url+'assets/mcp/images/notice.jpg" width="20" height="20">&nbsp;<?php echo _('This email address already exists !'); ?>');			
				  $jq('#p_email').val("");
				  $jq('#p_email').focus();
			   }else if(response.RESULT == "notexist"){				 
				 $jq('#dup_msg').html("");
			   }
			}
		});
	});
}
// ======  END: CHECKING DUPLICATE EMAIL EXISTS===================//

// ======  START: CHECKING DUPLICATE USERNAME EXISTS===================//
function check_username(uname){
	var username = uname;
	var $jq=jQuery.noConflict();
	$jq(document).ready(function(){
		$jq.ajax({
		   type: "POST",
		   url: base_url+"mcp/partners/validate",
		   dataType: 'json',
		   data: 'username='+username+'&check=username',
		   success: function(response){
			   if(response.RESULT == "duplicate"){
				  $jq('#dup_msg').html('<img border="0" src="'+base_url+'assets/mcp/images/notice.jpg" width="20" height="20">&nbsp;<?php echo _('This username already exists. Please choose a different one.'); ?>');			
				  $jq('#p_username').val("");
				  $jq('#p_username').focus();
			   }else if(response.RESULT == "notexist"){
				 $jq('#dup_msg').html("");
			   }
			}
		});
	});
}

// ======  END : CHECKING DUPLICATE USERNAME EXISTS===================//
</script>

<div style="width:100%">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" 
				 cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding-bottom:10px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background
						  :url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr height="26">
                                <td width="50%" align="left"><h3><?php echo _('Add New Partner'); ?></h3></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center">
						   <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">                           <form method="post" action="<?php echo base_url(); ?>mcp/partners/add_partner" name="frm_add_partner" id="frm_add_partner">
                            <tbody>
							  <tr><td colspan="2" align="center"><div id="dup_msg" style="color:red;"></div></td></tr>
                              <tr>
                                 <td width="35%">
								    <table align="right">
									   <tr>
									      <th align="left"><?php echo _('First Name'); ?> : </th>
										  <td><input type="text" name="p_first_name" id="p_first_name" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Last Name'); ?> : </th>
										  <td><input type="text" name="p_last_name" id="p_last_name" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Address'); ?> : </th>
										  <td><input type="text" name="p_address" id="p_address" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('City'); ?> : </th>
										  <td><input type="text" name="p_city" id="p_city" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Phone'); ?> : </th>
										  <td><input type="text" name="p_phone" id="p_phone" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Email'); ?> : </th>
										  <td><input type="text" name="p_email" id="p_email" value="" class="textbox" onChange="check_email(this.value);"></td>
									   </tr>
									    <tr>
									   <th align="left"><?php echo _("Add logo");?> : </th>
                              			<td>
		                              		<a href="javascript:;" class="thickboxed_label" attr_id="8" attr_width="150" style="text-decoration: none;"><input id="upload_img" class="btnWhiteBack" value="<?php echo _('LOGO upload');?>" type="button"></a>
		                              		<input type="hidden" value="" name="partner_logo" id="partner_logo"></input>
                              			</td>
                              		</tr>
									</table>
								 </td>
								 <td width="50%">
								    <table style="margin-left: 50px;">
									   <tr>
									      <th align="left"><?php echo _('Username'); ?> : </th>
										  <td><input type="text" name="p_username" id="p_username" value="" class="textbox" onChange="check_username(this.value);"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Password'); ?> : </th>
										  <td><input type="text" name="p_password" id="p_password" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Partner Code'); ?> : </th>
										  <td><input type="text" name="p_code" id="p_code" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="left"><?php echo _('Region Assigned'); ?> : </th>
										  <td><input type="text" name="p_region" id="p_region" value="" class="textbox"></td>
									   </tr>
									   <tr>
									   	<th>&nbsp</th>
									   	<td>&nbsp</td>
									   </tr>
									   <tr>
									   	<th>&nbsp</th>
									   	<td>&nbsp</td>
									   </tr>
									   <tr>
									   	<th>&nbsp</th>
									   	<td>&nbsp</td>
									   </tr>
									</table>
								 </td>
                              </tr>
                             
                              <tr>
                              	<td colspan="2">
                                  <div id="uploaded_image8"></div>
                                	<input id="x" type="hidden">
                                  <input id="y" type="hidden">
                                  <input id="w" type="hidden">
                                  <input id="h" type="hidden">
                                </td>
                              </tr>
							  <tr>
							    <td colspan="2" align="center">
								   <input type="hidden" name="p_status" id="p_status" value="1" />
								   <input type="submit" name="add_partner" id="add_partner" value="<?php echo _('ADD PARTNER'); ?>" class="btnWhiteBack">
								</td>
							  </tr>
							  <tr>
							    <td colspan="2">&nbsp;</td>
							  </tr>
                            </tbody>
							</form>
							
							<script type="text/javascript">
						
								var frmValidator = new Validator("frm_add_partner");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("p_first_name","req","<?php echo _('Please enter partner first name.'); ?>");
								frmValidator.addValidation("p_last_name","req","<?php echo _('Please enter partner last name.'); ?>");
								frmValidator.addValidation("p_address","req","<?php echo _('Please enter partner address.'); ?>");
								frmValidator.addValidation("p_city","req","<?php echo _('Please enter partner city.'); ?>");
								frmValidator.addValidation("p_phone","req","<?php echo _('Please enter partner phone number.'); ?>");
								frmValidator.addValidation("p_phone","num","<?php echo _('Please enter partner phone number in digits.'); ?>");
								frmValidator.addValidation("p_email","req","<?php echo _('Please enter partner email address.'); ?>");
								frmValidator.addValidation("p_email","email","<?php echo _('Please enter partner valid email address.'); ?>");	
								
								frmValidator.addValidation("p_username","req","<?php echo _('Please enter a unique username.'); ?>");
								frmValidator.addValidation("p_password","req","<?php echo _('Please enter password.'); ?>");
								frmValidator.addValidation("p_code","req","<?php echo _('Please enter partner code.'); ?>");
								frmValidator.addValidation("p_monthly_income","req","<?php echo _('Please enter partner monthly income.'); ?>");
								frmValidator.addValidation("p_region","req","<?php echo _('Please enter partner\'s region assigned.'); ?>");
							</script>
							
                          </table></td>
                      </tr>
                    </tbody>
					
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
</div>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/js/thickbox/javascript/thickbox.js?version=<?php echo version;?>"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/mcp/js/partner_logo.js?version=<?php echo version;?>"></script>

<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>