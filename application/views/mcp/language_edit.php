 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">

              <tbody>
                <tr>
                  <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				    <?php echo form_open_multipart("mcp/languages/language_update",array('method'=>"post" ,'id'=>"update_language", 'name'=>"update_language"));?>
                    <?php form_hidden(array('value'=>'','name'=>'id'));?>
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>

                              <tr>
                                <td width="94%" align="left"><h3><?php echo _('Edit Language'); ?></h3></td>
                                <td width="3%" align="right"></td>
                                <td width="3%" align="left"><div class="icon_button"> <img width="16" height="16" border="0" style="cursor:pointer" onclick="javascript:history.back();" title="<?php echo _('Go Back'); ?>" alt="Go Back" src="<?php echo base_url();?>assets/mcp/images/undo.jpg"> </div></td>
                              </tr>
                            </tbody>
                          </table></td>

                      </tr>
                      <tr>
                        <td align="center" style="padding-bottom:15px"><table width="98%" cellspacing="0" cellpadding="5" border="0" align="center" style="border:1px solid #003366; text-align:left;">
                            <tbody>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Language Information'); ?></td>
                              </tr>
                              <tr>

                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                <td width="79%" height="31" style="padding-left:10px;"></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Language Name'); ?><span class="red_star">*</span></td>

                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"lang_name",'name'=>"lang_name"));?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Language Code'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox", 'id'=>"lang_code", 'name'=>"lang_code"));?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Image'); ?><span class="red_star">*</span></td>

                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_upload(array('id'=>'flag','name'=>'flag'))?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Upload Language File'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_upload(array('id'=>"language_file", 'name'=>"language_file"))?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">

                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>
                                                <td><?php echo form_submit("update",'edit language');?></td>
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
                          </table>
                       
                  </td>
                </tr>
              </tbody>
            </table>
            <?php echo form_close();?></td>
        </tr>
      </tbody>

    </table>
    </td>
    </tr>
    </tbody>
    </table>
    <!-- end of main body -->
  </div>