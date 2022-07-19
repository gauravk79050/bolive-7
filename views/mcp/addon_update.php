   <div style="width:100%">
   <?php echo form_open('',array('method'=>"post",'id'=>"frm_addon_addedit",'name'=>"frm_addon_addedit"));?>
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
	    <tr>
		 <?php  echo validation_errors(); ?>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
                   <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                <td width="94%" align="left"><h3><?php echo _('Update Addon'); ?></h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Addon Information')?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr> <?php if(!empty($content)): ?>
							  			 <?php  foreach($content as $row):?>
							       
                                <td width="21%" height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo $row->addon_id; ?></td>
								<?php echo form_hidden('addon_id',$row->addon_id)?>
                              </tr>
                              <tr>
							  
                                <td width="21%" height="30" class="wd_text"><?php echo _('Addon Name'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('style'=>"width:160px",'class'=>"textbox",'id'=>"addon_title",'name'=>"addon_title",'value'=>((form_error('addon_title') || form_error('addon_description') || form_error('addon_price') || (form_error('addon_diplay_order'))?set_value('addon_title'):$row->addon_title)))); ?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Addon Desciption'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_textarea(array('cols'=>"30",'rows'=>"3",'class'=>"textbox",'id'=>"addon_description",'name'=>"addon_description",'value'=>((form_error('addon_title') || form_error('addon_description') || form_error('addon_price') || (form_error('addon_diplay_order'))?set_value('addon_description'):$row->addon_description)))); ?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Addon Price'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('style'=>"width:160px",'class'=>"textbox",'id'=>"addon_price",'name'=>"addon_price",'value'=>((form_error('addon_title') || form_error('addon_description') || form_error('addon_price') || (form_error('addon_diplay_order'))?set_value('addon_price'):$row->addon_price)))); ?></td>
                              
							 </tr>
							  <tr>
							  
                                <td width="21%" height="30" class="wd_text"><?php echo _('Addon Display Order'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('style'=>"width:160px",'class'=>"textbox",'id'=>"addon_display_order",'name'=>"addon_display_order",'value'=>((form_error('addon_title') || form_error('addon_description') || form_error('addon_price') || (form_error('addon_diplay_order'))?set_value('addon_title'):$row->addon_display_order)))); ?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>
                                                <td><?php echo form_submit(array('class'=>"btnWhiteBack",'id'=>"update",'name'=>"update",'value'=>_('UPDATE')));?>
                                                 </td>
                                              </tr>
											    <?php endforeach;?>
											  <?php endif;?>
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
            </td>
        </tr>
      </tbody>
    </table>
    </td>
    </tr>
    </tbody>
   </table>
   <?php echo form_close();?>   
   
   <script type="">
    var frmUserFormValidator = new Validator("frm_addon_addedit");
	frmUserFormValidator.EnableMsgsTogether();
	frmUserFormValidator.addValidation("addon_title","req","<?php echo _('Please enter the Addon Name'); ?>");	
	frmUserFormValidator.addValidation("addon_description","req","<?php echo _('Please enter the Addon Description'); ?>");	
	frmUserFormValidator.addValidation("addon_price","req","<?php echo _('Please enter the Addon Price'); ?>");	
    frmUserFormValidator.addValidation("addon_display_order","req","<?php echo _('Please enter the Addon Display Order'); ?>");	
  </script>
        
</div>
