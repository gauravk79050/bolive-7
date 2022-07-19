<body>
<div style="width:100%">
<!-- start of main body -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
            <tr>
                <td valign="top" align="center">
                    <table width="98%" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <tr>
                                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
                                    <table width="98%" cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="padding-bottom:5px; width:100%;" colspan="2">
                                                    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url();?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                                                    <tbody>
                                                       <tr>                                                          
                                                          <td width="50%" align="left"><h3><?php echo _('Edit Competitor'); ?></h3></td>
                                                          <td width="50%" align="right">&nbsp;</td>
                                                       </tr>
                                                       </tbody>
                                                     </table>
                                                   </td>
                                                </tr>       
                                                <tr>
                                                    <td class="whiteSmallBold" height="20" bgcolor="#003366" align="left" style="padding-left:10px;" colspan="2"><?php echo _('Competitor'); ?></td>
                                                     <td height="10" colspan="2"><br></td>
                                                </tr>                                               
                                                <?php echo form_open('mcp/competitor/update',array("id"=>'frm_competitor_addedit'));?>
                                              
                                                <tr>
                                                   <td class="wd_text" width="21%" height="30">ID&nbsp;&nbsp;</td>
                                                   <td width="79%" height="31" style="padding-left:10px;"><?php echo $id;?></td>
                                                </tr>
                                                <tr> <?php echo form_hidden('competitor_id',$id);?>
                                                    <td class="wd_text" width="21%" height="30"><?php echo _('Competitor Url'); ?><span class="red_star">*</span> </td>
                                                    <td width="79%" height="31" style="padding-left:10px;"><?php echo form_input(array('name'=>"competitor_url",'id'=>"competitor_url",'value'=>$competitor_url));?> <?php if(@$host=='1'){?><span class="red_star">*<?php  echo "enter valid url like 'http://www.example.com'";?></span><?php }?> </td>
                                                </tr>
                                                <tr>
                                                    <td height="30" colspan="2">
                                                     <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                       <tbody>
                                                         <tr>
                                                           <td valign="middle" height="50">
                                                              <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                                  <tbody>
                                                                      <tr>
                                                                          <td width="18%" align="right" style="padding-right:25px">&nbsp;</td>
                                                                          <td>																		  
																		  <?php echo form_submit(array('id'=>"update",'name'=>"update",'value'=>_('UPDATE'),'class'=>'btnWhiteBack'));?>
																		  <?php echo form_submit(array('id'=>"delete",'name'=>"delete",'value'=>_('DELETE COMPETITOR'),'class'=>'btnWhiteBack'));?>
												
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
                                                <?php echo form_close();?>
												
												<script type="text/javascript">
												
												var frmUserFormValidator = new Validator("frm_competitor_addedit");
												frmUserFormValidator.EnableMsgsTogether();
												frmUserFormValidator.addValidation("competitor_url","req","<?php echo _('Please select url.'); ?>");		
												</script>
												
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                             
                                                
                                                <tr>
                                                  <td height="22" align="right"><div style="float:right; width:80%"> <?php /*?><span class="paging_nolink">&lt;&lt;Vorige</span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink">Volgende&gt;&gt;</span><?php */?> </div></td>
                                                </tr> 
                                         </tbody>                                     
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
        
<!-- end of main body -->
</div>
<div id="push"></div>
</body>
