  <div style="width:100%">

    <!-- start of main body -->

    <table width="100%" cellspacing="0" cellpadding="0" border="0">

      <tbody>

        <tr>

          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">

              <tbody>

                <tr>

                  <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0">

                      <tbody>

                        <tr>

                          <td align="center" style="padding-bottom:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                         
                              <tbody>

                                <tr>

                                  <td width="50%" align="left"><h3><?php echo _('Language Manager'); ?></h3></td>

                                  <td width="50%" align="right"><div onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>

                                    <div style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="Add Language" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/languages/language_add'" id="btn_add"></div></td>

                                </tr>
                           
                              </tbody>

                            </table></td>

                        </tr>

                        <tr>

                          <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">

                              <tbody>

                                <tr>

                                  <td style="padding-top:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">

                                      <tbody>

                                        <tr>

                                          <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Search Language'); ?></td>

                                        </tr>

                                        <tr>

                                          <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid; padding:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <?php echo form_open("mcp/languages",array('name'=>'searchform','id'=>'searchform'));?>
                                             <tbody>

                                                <tr>

                                                  <td width="69" height="22" class="blackMediumNormal"><?php echo _('Search By'); ?></td>

                                                  <td width="126"><?php
													   $options=array('0'=>'-- SEARCH BY --','id'=>'ID','language'=>'Language Name');
													   echo form_dropdown('search_by', $options, '0');
										              ?></td>

                                                  <td width="109" class="blackMediumNormal"><?php echo _('Search Keyword'); ?></td>

                                                  <td width="160"><?php echo form_input(array('id'=>"search_keyword" ,'name'=>"search_keyword"));?></td>

                                                  <td width="345"><span style="padding:0px 3px 3px 0px">

                                                    <?php echo form_submit(array('id'=>'search','name'=>'search','value'=>'SEARCH','class'=>'btnWhiteBack'));?>&nbsp;
													<?php echo form_button(array('type'=>'reset','content'=>'RESET','value'=>'true','class'=>'btnWhiteBack'));?>
                                                    </span>

                                                    </td>
											 

                                                </tr>

                                              </tbody>
                                           <?php echo form_close();?>
										   
										   <script type="text/javascript">
												var frmvalidator = new Validator("searchform");
												frmvalidator.EnableMsgsTogether();
												frmvalidator.addValidation("search_by","dontselect=0","Please select a column on which to search");
												frmvalidator.addValidation("search_keyword","req","Please enter search keyword");

										   </script>
										   
                                            </table></td>

                                        </tr>

                                      </tbody>

                                    </table></td>

                                </tr>

                                <tr>

                                  <td height="22" align="right"><div style="float:right; width:80%"> <?php /*?><span class="paging_nolink">&lt;&lt;Vorige</span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink">Volgende&gt;&gt;</span><?php */?> </div></td>

                                </tr>

                                <tr>

                                  <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">

                                      <tbody>

                                        <tr>

                                          <td width="7%" class="whiteSmallBold"> <?php echo _('ID'); ?></td>

                                          <td width="13%" class="whiteSmallBold"><?php echo _('LanguageName'); ?></td>

                                          <td width="20%" class="whiteSmallBold"><?php echo _('Language Code'); ?></td>

                                          <td width="10%" class="whiteSmallBold"><?php echo _('Image'); ?></td>

                                          <td width="50%" align="right" style="padding-right:40px" class="whiteSmallBold"><?php echo _('Options'); ?></td>

                                        </tr>

                                        <tr>

                                          <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="5">
										     
											 <table width="100%" cellspacing="0" cellpadding="0" border="0">
											 <tbody>
											 <?php if(!empty($languages)) { ?>
                                             <?php foreach($languages as $d):?>

                                                <tr>
                                                 
                                                  <td width="7%" height="40" class="blackMediumNormal"><?php  echo $d->id;?></td>

                                                  <td width="13%" class="blackMediumNormal"><?php echo $d->lang_name;?></td>

                                                  <td width="20%" class="blackMediumNormal"><?php  echo  $d->lang_code;?></td>

                                                  <td width="10%" class="blackMediumNormal"><span style="padding:5px" class="blackMediumNormal"><img width="40" height="40" border="0" src="<?php  echo base_url()."assets/mcp/images/lang-images/".$d->flag; ?>"></span></td>

                                                  <td width="50%" align="right" style="padding-right:40px" class="blackMediumNormal"><span style="padding:5px" class="blackMediumNormal"><img width="16" height="16" border="0" style="cursor:pointer" onClick="window.location.href='<?php echo base_url();?>mcp/languages/language_update/<?php echo $d->id;?>'" title="Edit" alt="Edit" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg"></span></td>

                                                </tr>

                                              
                                         <?php endforeach ;?>
										 <?php } else { ?>
										 
										       <tr><td colspan="4" style="font-weight:bold;color:red;padding:5px;" align="center">
											     <?php echo _('Sorry ! No Language Found.'); ?>
											   </td></tr> 
												
										 <?php } ?>
										     </tbody>
                                             </table>
										  </td>

                                        </tr>

                                      </tbody>

                                    </table></td>

                                </tr>

                                <tr>

                                  <td>&nbsp;</td>

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

    <!-- end of main body -->

  </div>