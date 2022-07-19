<div style="width:100%">
  <form action="<?php echo base_url();?>mcp/allergenswords/allergens_add" method="post" id="frm_aller_add" name="frm_aller_add">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <?php echo validation_errors(); ?>
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
                                  <td width="94%" align="left"><h3><?php echo _('Add Allergens'); ?></h3></td>
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
                                  <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Allergens Information'); ?></td>
                                </tr>
                                <tr>
                                  <td height="10" colspan="2"></td>
                                </tr>                                
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Allergens'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;"><textarea cols="30" rows="3" class="" id="allergens" name="allergens" ></textarea></td>
                                </tr>
                                <tr>
                                  <td height="10" colspan="2"><?php echo _('Note: Allergens must be seperated by comma')?></td>
                                </tr>                                
                                <tr>
                                  <td height="30" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                      <tbody>
                                        <tr>
                                          <td valign="middle" height="50"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                              <tbody>
                                                <tr>
                                                  <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>                                                  
                                                  	<td><input type="submit" value="<?php echo _('Add Allergens'); ?>" class="btnWhiteBack" id="aller_add" name="aller_add" ></td>
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
  <script>
    var frmUserFormValidator = new Validator("frm_aller_add");
	frmUserFormValidator.EnableMsgsTogether();
	frmUserFormValidator.addValidation("allergens","req","<?php echo _('Please enter allergens words'); ?>");		
  </script>
  </div>