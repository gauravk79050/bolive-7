<style type="text/css">
.textbox{
	width:200px;
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
		   url: base_url+"mcp/affiliates/validate",
		   dataType: 'json',
		   data: 'email='+u_email+'&check=email',
		   success: function(response){
			   if(response.RESULT == "duplicate"){				  
				  $jq('#dup_msg').html('<img border="0" src="'+base_url+'assets/mcp/images/notice.jpg" width="20" height="20">&nbsp;<?php echo _('This email address already exists !'); ?>');			
				  $jq('#a_email').val("");
				  $jq('#a_email').focus();
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
		   url: base_url+"mcp/affiliates/validate",
		   dataType: 'json',
		   data: 'username='+username+'&check=username',
		   success: function(response){
			   if(response.RESULT == "duplicate"){
				  $jq('#dup_msg').html('<img border="0" src="'+base_url+'assets/mcp/images/notice.jpg" width="20" height="20">&nbsp;<?php echo _('This username already exists. Please choose a different one.'); ?>');			
				  $jq('#a_username').val("");
				  $jq('#a_username').focus();
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
                                <td width="50%" align="left"><h3><?php echo _('Add New Affiliate'); ?></h3></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center">
						   <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">                           <form method="post" action="<?php echo base_url(); ?>mcp/affiliates/add_affiliate" name="frm_add_affiliate" id="frm_add_affiliate">
                            <tbody>
							  <tr><td colspan="2" align="center"><div id="dup_msg" style="color:red;"></div></td></tr>
                              <tr>
                                 <td width="35%">
								    <table align="right">
									   <tr>
									      <th align="right"><?php echo _('First Name'); ?> : </th>
										  <td><input type="text" name="a_first_name" id="a_first_name" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Last Name'); ?> : </th>
										  <td><input type="text" name="a_last_name" id="a_last_name" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Address'); ?> : </th>
										  <td><input type="text" name="a_address" id="a_address" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('City'); ?> : </th>
										  <td><input type="text" name="a_city" id="a_city" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Phone'); ?> : </th>
										  <td><input type="text" name="a_phone" id="a_phone" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Email'); ?> : </th>
										  <td><input type="text" name="a_email" id="a_email" value="" class="textbox" onChange="check_email(this.value);"></td>
									   </tr>
									</table>
								 </td>
								 <td width="50%">
								    <table align="center">
									   <tr>
									      <th align="right"><?php echo _('Username'); ?> : </th>
										  <td><input type="text" name="a_username" id="a_username" value="" class="textbox" onChange="check_username(this.value);"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Password'); ?> : </th>
										  <td><input type="text" name="a_password" id="a_password" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Affiliate Code'); ?> : </th>
										  <td><input type="text" name="a_code" id="a_code" value="" class="textbox"></td>
									   </tr>
									   <tr>
									      <td>&nbsp;</td>
										  <td>&nbsp;</td>
									   </tr> 
									   <!-- <tr>
									      <th align="right"><?php echo _('Monthly Income per Client'); ?> : </th>
										  <td><input type="text" name="a_monthly_income" id="a_monthly_income" value="" class="textbox"></td>
									   </tr> -->
									   <!--<tr>
									      <th align="right"><?php //echo _('Region Assigned'); ?> : </th>
										  <td><input type="text" name="a_region" id="a_region" value="" class="textbox"></td>
									   </tr>-->
									</table>
								 </td>
                              </tr>
							  <tr>
							    <td colspan="2" align="center">
								   <input type="hidden" name="a_status" id="a_status" value="1" />
								   <input type="submit" name="add_affiliate" id="add_affiliate" value="<?php echo _('ADD AFFILIATE'); ?>" class="btnWhiteBack">
								</td>
							  </tr>
							  <tr>
							    <td colspan="2">&nbsp;</td>
							  </tr>
                            </tbody>
							</form>
							
							<script type="text/javascript">
						
								var frmValidator = new Validator("frm_add_affiliate");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("a_first_name","req","<?php echo _('Please enter affiliate first name.'); ?>");
								frmValidator.addValidation("a_last_name","req","<?php echo _('Please enter affiliate last name.'); ?>");
								frmValidator.addValidation("a_address","req","<?php echo _('Please enter affiliate address.'); ?>");
								frmValidator.addValidation("a_city","req","<?php echo _('Please enter affiliate city.'); ?>");
								frmValidator.addValidation("a_phone","req","<?php echo _('Please enter affiliate phone number.'); ?>");
								frmValidator.addValidation("a_phone","num","<?php echo _('Please enter affiliate phone number in digits.'); ?>");
								frmValidator.addValidation("a_email","req","<?php echo _('Please enter affiliate email address.'); ?>");
								frmValidator.addValidation("a_email","email","<?php echo _('Please enter affiliate valid email address.'); ?>");	
								
								frmValidator.addValidation("a_username","req","<?php echo _('Please enter a unique username.'); ?>");
								frmValidator.addValidation("a_password","req","<?php echo _('Please enter password.'); ?>");
								frmValidator.addValidation("a_code","req","<?php echo _('Please enter affiliate code.'); ?>");
								frmValidator.addValidation("a_monthly_income","req","<?php echo _('Please enter affiliate monthly income.'); ?>");
								/*frmValidator.addValidation("a_region","req","<?php //echo _('Please enter affiliate\'s region assigned.'); ?>");*/
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