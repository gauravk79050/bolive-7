<div style="width:100%">
  <form action="<?php echo base_url(''); ?>mcp/package/insert" enctype="multipart/form-data" method="post" id="frm_package_addedit" name="frm_package_addedit">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <?php  echo validation_errors(); ?>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td valign="middle" align="center" style="border:#8F8F8F 1px solid;"><input type="hidden" value="package_addedit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td align="center" style="padding:15px 0px 10px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url('<?php echo site_url('mcp/images/bg.jpg');?>') left top repeat-x;" class="page_caption">
                              <tbody>
                                <tr>
                                  <td width="94%" align="left"><h3><?php echo _('Add Packages'); ?></h3></td>
                                  <td width="3%" align="right"></td>
                                  <td width="3%" align="left"><div class="icon_button"> <img width="16" height="16" border="0" style="cursor:pointer" onClick="javascript:history.back();" title="Go Back" alt="Go Back" src="<?php echo base_url(''); ?>assets/mcp/images/undo.jpg"> </div></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td align="center" style="padding-bottom:15px"><table width="98%" cellspacing="0" cellpadding="5" border="0" align="center" style="border:1px solid #003366; text-align:left;">
                              <tbody>
                                <tr>
                                  <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Package Information'); ?></td>
                                </tr>
                                <tr>
                                  <td height="10" colspan="2"></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                  <td width="79%" height="31" style="padding-left:10px;"></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Package Name'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;"><input type="text" style="width:160px" class="textbox" id="package_name" name="package_name"></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Package Desciption'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;"><textarea cols="30" rows="3" class="textbox" type="textarea" id="package_desc" name="package_desc"></textarea></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Package Price'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;"><input type="text" style="width:160px" class="textbox" id="package_price" name="package_price"></td>
                                </tr>
                                <tr>
                                  <td height="30" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                      <tbody>
                                        <tr>
                                          <td valign="middle" height="50"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                              <tbody>
                                                <tr>
                                                  <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>
                                                  <td><input type="submit" value="<?php echo _('ADD PACKAGE'); ?>" class="btnWhiteBack" id="btn_add_update" name="btn_add_update" >
                                                    <input type="hidden" value="add_edit" id="act" name="act">
                                                    <input type="hidden" value="" id="ID" name="ID">
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table></td>
                                        </tr>
                                      </tbody>
                                    </table></td>
                                </tr>
                                <tr>
                                  <td height="10" colspan="2"></td>
                                </tr>
                              </tbody>
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
  </form>
  
  <script type="">
    var frmUserFormValidator = new Validator("frm_package_addedit");
	frmUserFormValidator.EnableMsgsTogether();
	frmUserFormValidator.addValidation("package_name","req","<?php echo _('Please enter the Package Name'); ?>");	
	frmUserFormValidator.addValidation("package_desc","req","<?php echo _('Please enter the Package Description'); ?>");	
	frmUserFormValidator.addValidation("package_price","req","<?php echo _('Please enter the Package Price'); ?>");	
  </script>
  
</div>
</body></html>