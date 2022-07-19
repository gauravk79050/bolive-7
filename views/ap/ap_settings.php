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
                        <td align="center" style="padding-bottom:10px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr height="26">
                                <td width="50%" align="left"><h3><?php echo _('Settings'); ?></h3></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center">
						   <table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">                           <form method="post" action="<?php echo base_url(); ?>ap/affiliate/settings" name="frm_edit_affiliate" id="frm_edit_affiliate">
                            <tbody>
							  <tr><td colspan="2" align="center"><div id="dup_msg" style="color:red;"></div></td></tr>
                              <tr>
                                 <td width="35%">
								    <table align="right">
									   <tr>
									      <th align="right"><?php echo _('First Name'); ?> : </th>
										  <td><input type="text" name="a_first_name" id="a_first_name" value="<?php echo $affiliate->a_first_name; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Last Name'); ?> : </th>
										  <td><input type="text" name="a_last_name" id="a_last_name" value="<?php echo $affiliate->a_last_name; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Address'); ?> : </th>
										  <td><input type="text" name="a_address" id="a_address" value="<?php echo $affiliate->a_address; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('City'); ?> : </th>
										  <td><input type="text" name="a_city" id="a_city" value="<?php echo $affiliate->a_city; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Phone'); ?> : </th>
										  <td><input type="text" name="a_phone" id="a_phone" value="<?php echo $affiliate->a_phone; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Email'); ?> : </th>
										  <td><input type="text" name="a_email" id="a_email" value="<?php echo $affiliate->a_email; ?>" class="textbox" readonly="1" disabled="disabled"></td>
									   </tr>
									</table>
								 </td>
								 <td width="50%">
								    <table align="center">
									   <tr>
									      <th align="right"><?php echo _('Username'); ?> : </th>
										  <td><input type="text" name="a_username" id="a_username" value="<?php echo $affiliate->a_username; ?>" class="textbox" readonly="1" disabled="disabled"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Password'); ?> : </th>
										  <td><input type="text" name="a_password" id="a_password" value="<?php echo $affiliate->a_password; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Affiliate Code'); ?> : </th>
										  <td><input type="text" name="a_code" id="a_code" value="<?php echo $affiliate->a_code; ?>" class="textbox"></td>
									   </tr>
									   <tr>
									      <td>&nbsp;</td>
										  <td>&nbsp;</td>
									   </tr>
									   <tr>
									      <th align="right"><?php echo _('Monthly Income per Client'); ?> : </th>
										  <td><input type="text" name="a_monthly_income" id="a_monthly_income" value="<?php echo $affiliate->a_monthly_income; ?>" class="textbox" disabled="disabled"></td>
									   </tr>
									   <!--<tr>
									      <th align="right"><?php //echo _('Region Assigned'); ?> : </th>
										  <td><input type="text" name="a_region" id="a_region" value="<?php //echo $affiliate->a_region; ?>" class="textbox"></td>
									   </tr>-->
									</table>
								 </td>
                              </tr>
							  <tr>
							    <td colspan="2" align="center">
								   <input type="hidden" name="a_status" id="a_status" value="1" />
								   <input type="submit" name="update_affiliate" id="update_affiliate" value="<?php echo _('SAVE CHANGES'); ?>" class="btnWhiteBack">
								</td>
							  </tr>
							  <tr>
							    <td colspan="2">&nbsp;</td>
							  </tr>
                            </tbody>
							</form>
							<script type="text/javascript">
						
								var frmValidator = new Validator("frm_edit_affiliate");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("a_first_name","req","<?php echo _('Please enter first name.'); ?>");
								frmValidator.addValidation("a_last_name","req","<?php echo _('Please enter last name.'); ?>");
								frmValidator.addValidation("a_address","req","<?php echo _('Please enter address.'); ?>");
								frmValidator.addValidation("a_city","req","<?php echo _('Please enter city.'); ?>");
								frmValidator.addValidation("a_phone","req","<?php echo _('Please enter phone number.'); ?>");
								frmValidator.addValidation("a_phone","num","<?php echo _('Please enter phone number in digits.'); ?>");
								frmValidator.addValidation("a_email","req","<?php echo _('Please enter email address.'); ?>");
								frmValidator.addValidation("a_email","email","<?php echo _('Please enter valid email address.'); ?>");	
								
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