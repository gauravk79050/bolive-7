 <div style="width:100%">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tbody>
        <tr>
          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">

              <tbody>
                <tr>
                  <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
				    <?php echo form_open_multipart("mcp/company_type/",array('method'=>"post" ,'id'=>"frm_company_type_addedit", 'name'=>"frm_company_type_addedit"));?>
                    <?php echo form_hidden('id',$id);?>
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>

                              <tr>
                                <td width="94%" align="left"><h3>&nbsp;</h3></td>
                                <td width="3%" align="right"></td>
                                <td width="3%" align="left"><div class="icon_button"> <img width="16" height="16" border="0" style="cursor:pointer" onclick="javascript:history.back();" title="Go Back" alt="Go Back" src="<?php echo base_url();?>assets/mcp/images/undo.jpg"> </div></td>
                              </tr>
                            </tbody>
                          </table></td>

                      </tr>
                      <tr>
                        <td align="center" style="padding-bottom:15px"><table width="98%" cellspacing="0" cellpadding="5" border="0" align="center" style="border:1px solid #003366; text-align:left;">
						<?php foreach($company_type as $ct):?>
                            <tbody>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Edit Company Type'); ?></td>
                              </tr>
                              <?php if($this->session->flashdata("error")):?>
                              <tr>
                               <td class="wd_text" width="21%" height="30">&nbsp;&nbsp;</td>
                               <td width="79%" height="31" style="padding-left:10px;">
                               	<?php echo $this->session->flashdata("error");?>
                               </td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <?php endif;?>
							  <tr>
                               <td class="wd_text" width="21%" height="30">ID&nbsp;&nbsp;</td>
                               <td width="79%" height="31" style="padding-left:10px;"><?php echo $id;?></td>
                              </tr>
                              <tr>

                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Company Type'); ?><span class="red_star">*</span></td>

                                <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('type'=>"text",'style'=>"width:140px", 'class'=>"textbox",'id'=>"company_type_name",'name'=>"company_type_name",'value'=>$ct->company_type_name));?></td>
                              </tr>
                              <tr>

                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Banner for Bestelonline'); ?></td>

                                <td width="79%" height="31" style="padding-left:10px;">
                                	<img src="<?php echo base_url();?>assets/mcp/images/<?php echo $ct->banner;?>" alt="<?php echo _("No Image");?>" style="max-width: 950px;"/>
                                	<br/>
                                	<input type="file" name="banner" id="banner" />
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
                                                <td>
												    <?php echo form_submit(array('id'=>"update",'name'=>"update",'value'=>'UPDATE','class'=>'btnWhiteBack'));?>
													<?php echo form_submit(array('id'=>"delete",'name'=>"delete",'value'=>'DELETE COMPANY TYPE','class'=>'btnWhiteBack'));?>
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
							<?php endforeach;?>
                          </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <?php echo form_close();?>
			
		    <script type="text/javascript">
				var frmUserFormValidator = new Validator("frm_company_type_addedit");
				frmUserFormValidator.EnableMsgsTogether();
				frmUserFormValidator.addValidation("company_type_name","req","<?php echo _('Please enter the Company Type name'); ?>");	
			</script>
			
		</td>
        </tr>
      </tbody>

    </table>
    </td>
    </tr>
    </tbody>
    </table>
    <!-- end of main body -->
  </div>