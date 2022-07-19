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
		   url: base_url+"mcp/dep/validate",
		   dataType: 'json',
		   data: {'email':u_email,'check':'email'},
		   success: function(response){
			   if(response.RESULT == "duplicate"){				  
				  $jq('#dup_msg').html('<img border="0" src="'+base_url+'assets/mcp/images/notice.jpg" width="20" height="20">&nbsp;<?php echo _('This email address already exists !'); ?>');			
				  $jq('#dep_email').val("");
				  $jq('#dep_email').focus();
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
		   url: base_url+"mcp/dep/validate",
		   dataType: 'json',
		   data: { 'username':username, 'check':'username' },
		   success: function(response){
			   if(response.RESULT == "duplicate"){
				  $jq('#dup_msg').html('<img border="0" src="'+base_url+'assets/mcp/images/notice.jpg" width="20" height="20">&nbsp;<?php echo _('This username already exists. Please choose a different one.'); ?>');			
				  $jq('#dep_username').val("");
				  $jq('#dep_username').focus();
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
                                <td width="50%" align="left"><h3><?php echo _('Add New DEP'); ?></h3></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center">
                        	<form method="post" action="" name="frm_add_partner" id="frm_add_partner">
							    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">                           
							   
		                            <tbody>
									  <tr><td colspan="2" align="center"><div id="dup_msg" style="color:red;"></div></td></tr>
		                              <tr>
		                                 <td width="35%">
										    <table align="right">
											   <tr>
											      <th align="right"><?php echo _('First Name'); ?> : </th>
												  <td><input type="text" name="dep_first_name" id="dep_first_name" value="" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Last Name'); ?> : </th>
												  <td><input type="text" name="dep_last_name" id="dep_last_name" value="" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Address'); ?> : </th>
												  <td><input type="text" name="dep_address" id="dep_address" value="" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('City'); ?> : </th>
												  <td><input type="text" name="dep_city" id="dep_city" value="" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Phone'); ?> : </th>
												  <td><input type="text" name="dep_phone" id="dep_phone" value="" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Email'); ?> : </th>
												  <td><input type="text" name="dep_email" id="dep_email" value="" class="textbox" onChange="check_email(this.value);"></td>
											   </tr>
											</table>
										 </td>
										 <td width="50%">
										    <table align="center">
											   <tr>
											      <th align="right"><?php echo _('Username'); ?> : </th>
												  <td><input type="text" name="dep_username" id="dep_username" value="" class="textbox" onChange="check_username(this.value);"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Password'); ?> : </th>
												  <td><input type="text" name="dep_password" id="dep_password" value="" class="textbox"></td>
											   </tr>
											   
											   <tr>
											      <th align="right"><?php echo _('Bank'); ?> : </th>
												  <td><input type="text" name="bank" id="bank" value="" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Bank-nbr'); ?> : </th>
												  <td><input type="text" name="bank_nbr" id="bank_nbr" value="" class="textbox"></td>
											   </tr>
											</table>
										 </td>
		                              </tr>
									  <tr>
									    <td colspan="2" align="center">
										   <input type="hidden" name="dep_status" id="dep_status" value="1" />
										   <input type="submit" name="add_dep" id="add_dep" value="<?php echo _('ADD DEP'); ?>" class="btnWhiteBack">
										</td>
									  </tr>
									  <tr>
									    <td colspan="2">&nbsp;</td>
									  </tr>
		                            </tbody>
	                            </table>
							</form>
							
							<script type="text/javascript">
						
								var frmValidator = new Validator("frm_add_partner");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("dep_first_name","req","<?php echo _('Please enter partner first name.'); ?>");
								frmValidator.addValidation("dep_last_name","req","<?php echo _('Please enter partner last name.'); ?>");
								frmValidator.addValidation("dep_address","req","<?php echo _('Please enter partner address.'); ?>");
								frmValidator.addValidation("dep_city","req","<?php echo _('Please enter partner city.'); ?>");
								frmValidator.addValidation("dep_phone","req","<?php echo _('Please enter partner phone number.'); ?>");
								frmValidator.addValidation("dep_phone","num","<?php echo _('Please enter partner phone number in digits.'); ?>");
								frmValidator.addValidation("dep_email","req","<?php echo _('Please enter partner email address.'); ?>");
								frmValidator.addValidation("dep_email","email","<?php echo _('Please enter partner valid email address.'); ?>");	
								
								frmValidator.addValidation("dep_username","req","<?php echo _('Please enter a unique username.'); ?>");
								frmValidator.addValidation("dep_password","req","<?php echo _('Please enter password.'); ?>");
							</script>
							
                          </td>
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
