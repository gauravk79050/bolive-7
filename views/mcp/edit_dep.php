<style type="text/css">
.textbox{
	width:200px;
}
</style>

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
                                <td width="50%" align="left"><h3><?php echo _('Edit Partner Details'); ?></h3></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center">
                        	<form method="post" action="" name="frm_edit_partner" id="frm_edit_partner">
							    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
		                            <tbody>
									  <tr><td colspan="2" align="center"><div id="dup_msg" style="color:red;"></div></td></tr>
		                              <tr>
		                                 <td width="35%">
										    <table align="right">
											   <tr>
											      <th align="right"><?php echo _('First Name'); ?> : </th>
												  <td><input type="text" name="dep_first_name" id="dep_first_name" value="<?php echo $dep->dep_first_name; ?>" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Last Name'); ?> : </th>
												  <td><input type="text" name="dep_last_name" id="dep_last_name" value="<?php echo $dep->dep_last_name; ?>" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Address'); ?> : </th>
												  <td><input type="text" name="dep_address" id="dep_address" value="<?php echo $dep->dep_address; ?>" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('City'); ?> : </th>
												  <td><input type="text" name="dep_city" id="dep_city" value="<?php echo $dep->dep_city; ?>" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Phone'); ?> : </th>
												  <td><input type="text" name="dep_phone" id="dep_phone" value="<?php echo $dep->dep_phone; ?>" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Email'); ?> : </th>
												  <td><input type="text" name="dep_email" id="dep_email" value="<?php echo $dep->dep_email; ?>" class="textbox" readonly="1" disabled="disabled"></td>
											   </tr>
											</table>
										 </td>
										 <td width="50%">
										    <table align="center">
											   <tr>
											      <th align="right"><?php echo _('Username'); ?> : </th>
												  <td><input type="text" name="dep_username" id="dep_username" value="<?php echo $dep->dep_username; ?>" class="textbox" readonly="1" disabled="disabled"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Password'); ?> : </th>
												  <td><input type="text" name="dep_password" id="dep_password" value="<?php echo $dep->dep_password; ?>" class="textbox"></td>
											   </tr>
											   
											   <tr>
											      <th align="right"><?php echo _('Bank'); ?> : </th>
												  <td><input type="text" name="bank" id="bank" value="<?php echo $dep->bank; ?>" class="textbox"></td>
											   </tr>
											   <tr>
											      <th align="right"><?php echo _('Bank-nbr'); ?> : </th>
												  <td><input type="text" name="bank_nbr" id="bank_nbr" value="<?php echo $dep->bank_nbr; ?>" class="textbox"></td>
											   </tr>
											</table>
										 </td>
		                              </tr>
									  <tr>
									    <td colspan="2" align="center">
										   <input type="hidden" name="dep_status" id="dep_status" value="1" />
										   <input type="submit" name="update_dep" id="update_dep" value="<?php echo _('UPDATE PARTNER'); ?>" class="btnWhiteBack">
										</td>
									  </tr>
									  <tr>
									    <td colspan="2">&nbsp;</td>
									  </tr>
		                            </tbody>
	                            </table>
							</form>
							
							<script type="text/javascript">
						
								var frmValidator = new Validator("frm_edit_partner");
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