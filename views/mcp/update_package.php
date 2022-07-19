   <div style="width:100%">
   <?php echo form_open(base_url('')."mcp/package/update",array('method'=>"post",'id'=>"frm_package_addedit",'name'=>"frm_package_addedit"));?>
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
                                <td width="94%" align="left"><h3><?php echo _('Update Packages'); ?></h3></td>
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
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Package Information')?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr> <?php if(!empty($content)): ?>
							  			 <?php  foreach($content as $row):?>
							       
                                <td width="21%" height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo $row->id ?></td>
								<?php echo form_hidden('id',$row->id)?>
                              </tr>
                              <tr>
							  
                                <td width="21%" height="30" class="wd_text"><?php echo _('Package Name'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('style'=>"width:160px",'class'=>"textbox",'id'=>"package_name",'name'=>"package_name",'value'=>$row->package_name)); ?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Package Desciption'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_textarea(array('cols'=>"30",'rows'=>"3",'class'=>"textbox",'id'=>"package_desc",'name'=>"package_desc",'value'=>$row->package_desc)); ?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Package Price'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('style'=>"width:160px",'class'=>"textbox",'id'=>"package_price",'name'=>"package_price",'value'=>$row->package_price)); ?></td>
                              
							  
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
												    <?php echo form_submit(array('class'=>"btnWhiteBack",'id'=>"delete",'name'=>"delete",'value'=>_('DELETE')));?>
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
    var frmUserFormValidator = new Validator("frm_package_addedit");
	frmUserFormValidator.EnableMsgsTogether();
	frmUserFormValidator.addValidation("package_name","req","<?php echo _('Please enter the Package Name'); ?>");	
	frmUserFormValidator.addValidation("package_desc","req","<?php echo _('Please enter the Package Description'); ?>");	
	frmUserFormValidator.addValidation("package_price","req","<?php echo _('Please enter the Package Price'); ?>");	
  </script>
        
</div>
