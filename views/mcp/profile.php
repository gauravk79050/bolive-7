  <div style="width:100%">
  	<?php echo form_open(base_url('')."mcp/profile/update",array('method'=>"post",'id'=>"frm_admin",'name'=>"frm_admin"))?>

    <!-- start of main body -->

    <table width="100%" cellspacing="0" cellpadding="0" border="0">

      <tbody>

        <tr>
        <?php  echo validation_errors(); ?>
          <td height="122">&nbsp;</td>

        </tr>

        <tr>

          <td align="center"><table width="332" cellspacing="0" cellpadding="0" border="0">

              <tbody>

                <tr>

                  <td class="blackMediumNormal"><strong>:: <?php echo _('Admin Profile');?> ::</strong></td>

                </tr>

                <tr>

                  <td height="10"><img height="5" alt="" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                </tr>

                <tr>

                  <td><table width="328" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat;">

                      <tbody>

                        <tr>

                          <td width="6" valign="top" align="left"><img width="12" height="12" src="<?php echo base_url('')?>assets/mcp/images/table_tl.png"></td>

                          <td valign="top" bgcolor="#FFFFFF" style="border-top:#003366 1px solid"><img width="1" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                          <td width="6" valign="top" align="right"><img width="12" height="12" src="<?php echo base_url('')?>assets/mcp/images/table_tr.png"></td>

                        </tr>

                        <tr>

                          <td valign="top" bgcolor="#FFFFFF" style="border-left:#003366 1px solid"><img width="5" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                          <td width="316" valign="middle" height="126" bgcolor="#FFFFFF" align="center"><table width="299" height="87" cellspacing="0" cellpadding="0" border="0">

                              <tbody>

                                <tr>

                                  <td height="10" colspan="2"></td>

                                </tr>

                                <tr>
								<?php foreach($content as $row):?>
                                  <td width="134" height="22"  align="left" style="padding-left:10px" class="blackMediumNormal"><?php echo _('Username');?> </td>

                                  <td width="165" align="left"><?php echo form_input(array('name'=>'login_username','value'=>$row->login_username,'style'=>"width:140px" ,'class'=>"textbox",'id'=>"login_username"))?></td>

                                </tr>

                                <tr>

                                  <td height="22" align="left" style="padding-left:10px" class="blackMediumNormal"><?php echo _('Admin Name');?><br>(<?php echo _("Show in mails")?>)</td>

                                  <td align="left"><?php echo form_input(array('value'=>$row->admin_name,'style'=>"width:140px",'class'=>"textbox",'id'=>"admin_name",'name'=>"admin_name"))?></td>
 
                                </tr>
                                
                                <tr>

                                  <td height="22" align="left" style="padding-left:10px" class="blackMediumNormal"><?php echo _('Password');?></td>

                                  <td align="left"><?php echo form_password(array('value'=>$row->login_password,'style'=>"width:140px",'class'=>"textbox",'id'=>"login_password",'name'=>"login_password"))?></td>

                                </tr>

                                <tr>

                                  <td height="22" align="left" style="padding-left:10px" class="blackMediumNormal"><?php echo _('Email');?></td>

                                  <td align="left"><?php echo form_input(array('value'=>$row->email,'style'=>"width:140px",'class'=>"textbox",'id'=>"email",'name'=>"email"))?></td>
 
                                </tr>

                                <tr>

                                  <td valign="middle" height="30">&nbsp;</td>

                                  <td valign="middle" align="left"><span style="padding:0px 3px 3px 0px">

                                    <?php echo form_submit(array('id'=>'btn_update','value'=>'Update','class'=>'btnWhiteBack','name'=>'btn_update'))?>
                                   </span></td>
									<?php endforeach;?>

                                </tr>

								   </tbody>

                            </table>
							
							</td>

                          <td valign="top" bgcolor="#FFFFFF" style="border-right:#003366 1px solid"><img width="5" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                        </tr>

                        <tr>

                          <td valign="bottom" bgcolor="#FFFFFF" style="border-left:#003366 1px solid; border-bottom:#003366 1px solid"><img height="5" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                          <td valign="bottom" bgcolor="#FFFFFF" style="border-bottom:#003366 1px solid"><img height="5" alt="" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                          <td valign="bottom" bgcolor="#FFFFFF" align="left" style="border-right:#003366 1px solid; border-bottom:#003366 1px solid"><img height="5" alt="" src="<?php echo base_url('')?>assets/mcp/images/spacer.gif"></td>

                        </tr>

                      </tbody>

                    </table></td>

                </tr>

              </tbody>

            </table></td>

        </tr>

      </tbody>

    </table>

    <!-- end of main body -->
  <?php echo form_close();?>
  
  <script type="text/javascript">
    var frmvalidator = new Validator("frm_admin");
	frmvalidator.EnableMsgsTogether();
	frmvalidator.addValidation("login_username","req","<?php echo _('Please enter a login Username'); ?>");
	frmvalidator.addValidation("login_password","req","<?php echo _('Please enter a login Password'); ?>");
	frmvalidator.addValidation("email","req","<?php echo _('Please enter your email address'); ?>");
	frmvalidator.addValidation("email","email","<?php echo _('Please enter a valid email address'); ?>");
  </script>
  
  </div>

