<body>
<div style="width:100%">
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>          
              
              <td valign="middle" align="center" style="border:#8F8F8F 1px solid;">
                    <input type="hidden" value="country_add_edit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding:15px 0px 10px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url();?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr>
                                <td width="94%" align="left"><h3><?php echo _('Add Country'); ?></h3></td>
                                <td width="3%" align="right"></td>
                                <td width="3%" align="left"><div class="icon_button"> <img width="16" height="16" border="0" style="cursor:pointer" onClick="javascript:history.back();" title="<?php echo _('Go Back'); ?>" alt="<?php echo _('Go Back'); ?>" src="<?php echo base_url();?>assets/mcp/images/undo.jpg"> </div></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center" style="padding-bottom:15px">
						
						<?php echo form_open('mcp/country/add',array('method'=>"post" ,'id'=>"frm_country_addedit", 'name'=>"country_add_edit"));?>
						
						<table width="98%" cellspacing="0" cellpadding="5" border="0" align="center" style="border:1px solid #003366; text-align:left;">
                            <tbody>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Country Information'); ?></td>
                              </tr>
                              <tr>
                                <td height="10" colspan="2"></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                <td width="79%" height="31" style="padding-left:10px;"></td>
                              </tr>
                              <tr>
                                <td width="21%" height="30" class="wd_text"><?php echo _('Country Name'); ?><span class="red_star">*</span></td>
                                <td width="79%" height="31" style="padding-left:10px;">
								<!--<input type="text" style="width:160px" class="textbox" id="country_name" name="country_name">-->
								<?php echo form_input(array('name'=>"country_name",'id'=>"country_name",'width'=>'160px','class'=>'textbox'));?>
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
												  <?php echo form_submit(array('name'=>'add','value'=>_('ADD COUNTRY'),'class'=>'btnWhiteBack')); ?>
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
                          </table>
					
					  <?php echo form_close();?>	  
					
					<script type="text/javascript">
					    var frmUserFormValidator = new Validator("frm_country_addedit");
						frmUserFormValidator.EnableMsgsTogether();
						frmUserFormValidator.addValidation("country_name","req","<?php echo _('Please select Country.'); ?>");		
					</script>
                  </td>
                </tr>
              </tbody>
            </table>
            </td>
              
              
              
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
  <!-- end of main body -->
</div>
<div id="push"></div>
</body>
