 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">

              <tbody>
                <tr>
                  <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				    <?php echo form_open_multipart("",array('method'=>"post" ,'id'=>"add_flyer", 'name'=>"add_flyer"));?>
                    <input type="hidden" name="flyer_id" id="flyer_id" value="<?php echo ((!empty($flyers_info))?$flyers_info->id:'');?>" />
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>

                              <tr>
                              	<?php if(!empty($flyers_info)){?>
                                <td width="94%" align="left"><h3><?php echo _('Update Flyers');?></h3></td>
                                <?php }else{?>
                                <td width="94%" align="left"><h3><?php echo _('Add Flyers');?></h3></td>
                                <?php }?>
                                <td width="3%" align="right"></td>
                                <td width="3%" align="left"><div class="icon_button"> <img width="16" height="16" border="0" style="cursor:pointer" onclick="javascript:history.back();" title="Go Back" alt="Go Back" src="<?php echo base_url();?>assets/mcp/images/undo.jpg"> </div></td>
                              </tr>
                            </tbody>
                          </table></td>

                      </tr>
                      <tr>
                        <td align="center" style="padding-bottom:15px"><table width="98%" cellspacing="0" cellpadding="5" border="0" align="center" style="border:1px solid #003366; text-align:left;">
                            <tbody>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Flyer Information');?></td>
                              </tr>
                              <tr>

                                <td height="10" colspan="2"></td>
                              </tr>
                              
                              <?php if(isset($success)){?>
                              <tr>
                              	<td>&nbsp;</td>
                                <td><span style="color: #336D03;" ><?php echo $success;?></td>
                              </tr>
                              <?php }?>
                              <?php if(isset($error)){?>
                              <tr>
                              	<td>&nbsp;</td>
                                <td style="color: #E2572D;"><?php echo $error;?></td>
                              </tr>
                              <?php }?>
                              
                              
                              <?php if(!empty($flyers_info)){?>
                              <tr>
                                <td width="21%" height="30" class="wd_text">ID&nbsp;&nbsp;</td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo $flyers_info->id;?></td>
                              </tr>
                              <?php }?>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _("Name");?><span class="red_star">*</span></td>

                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"name",'name'=>"name", 'value' => ((!empty($flyers_info))?$flyers_info->name:'')));?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _("Description");?></td>
                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_textarea(array('class'=>"textbox", 'id'=>"description", 'name'=>"description", 'value' => ((!empty($flyers_info))?$flyers_info->description:'')));?></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _("Price");?><span class="red_star">*</span></td>

                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('type'=>"text",'style'=>"width:40px", 'class'=>"textbox",'id'=>"price",'name'=>"price", 'value' => ((!empty($flyers_info))?$flyers_info->price:'')));?> &euro;</td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _("Image");?></td>

                                <td width="79%" height="31" style="padding-left:10px;">
                                	<?php if(!empty($flyers_info)){?>
                                	<img width="160" height="245" border="0" src="<?php  echo base_url()."assets/mcp/images/flyers/".$flyers_info->image; ?>">
                                	<br />
                                	<?php }?>
                                	<?php echo form_upload(array('id'=>'flyer_image','name'=>'flyer_image'))?>
                                </td>
                              </tr>
                              <tr>
                                <td height="30" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0">

                                    <tbody>
                                      <tr>
                                        <td valign="middle" height="50"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>
                                                <?php if(!empty($flyers_info)){?>
                                                <td><?php echo form_submit(array('id'=>"update",'name'=>"update",'value'=> _("Update Flyer"),'class'=>'btnWhiteBack'));?></td>
                                                <?php }else{?>
                                                <td><?php echo form_submit(array('id'=>"add",'name'=>"add",'value'=> _("Add Flyer"),'class'=>'btnWhiteBack'));?></td>
                                                <?php }?>
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
                       <script type="text/javascript" language="JavaScript">
						 var frmUserFormValidator = new Validator("add_flyer");
						 frmUserFormValidator.EnableMsgsTogether();
						 frmUserFormValidator.addValidation("name","req",_("Please enter Name") );	
						 frmUserFormValidator.addValidation("price","req", _("Please enter Price") );
					   </script>
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