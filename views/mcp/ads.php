<script type="text/javascript" src="<?php echo base_url()?>/assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		theme : "advanced",
		mode : "textareas",					
		script_url : '<?php echo base_url()?>/assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js',
		convert_urls : false,
					
					 
					  plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
   }); 
</script>

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
                                <td width="50%" align="left" height="30"><h3><?php echo _('ADVERTISEMENTS'); ?></h3></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody>
                                      <tr>
                                        <td class="whiteSmallBold"><?php echo _('E Mails Ads Message'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <?php if(!empty($ads)) foreach($ads as $ad):?>
                                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <?php echo  form_open("mcp/ads/update",array('name'=>'ads','id'=>'ads'));?>
                                                    <tbody>
                                                      <tr>
                                                        <td height="30" align="center" colspan="3"></td>
                                                      </tr>
                                                      <tr>
                                                        <td width="15%" class="wd_text"><?php echo _('Message'); ?>:</td>
                                                        <td><!-- color menu -->
                                                          <div style="position: absolute; left: -500px; top: -500px; visibility: hidden;" id="colormenuemailads_text_message"> </div>
                                                          <div style="position:absolute;left:-500px;top:-500px;visibility:hidden" id="_de_popup_container"></div>
                                                          <div style="position:absolute;top:-500px;left:-500px;visibility:hidden;"> </div>
                                                          <table width="400" height="330" cellspacing="0" cellpadding="0" border="0" id="emailads_text_messagetable" style="">
                                                            <tbody>
                                                              <tr>
                                                                <td style="width:100%;height:100%;"><div id="emailads_text_messagemain" style="width: 799px; height: 100%;"> <?php echo form_hidden('id',$ad->id)?> <?php echo form_textarea(array("name" => "emailads_text_message",'id'=>'emailads_text_message', "cols" => "10","value"=>$ad->emailads_text_message)); ?> 
																</div></td>
                                                              </tr>
                                                            </tbody>
                                                          </table></td>
                                                        <td>&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                        <td height="40">&nbsp;</td>
                                                        <td><?php echo form_submit('update_emailads_text_message',_('UPDATE'))?></td>
                                                        <td>&nbsp;</td>
                                                      </tr>
                                                    </tbody>
                                                    <?php echo form_close();?>
                                                  </table></td>
                                                <?php endforeach;?>
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
                      <?php /*?><tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
                                    <tbody>
									
                                      <tr>
                                        <td class="whiteSmallBold"><?php echo _('FrontEnd Footer'); ?></td>
                                      </tr>
                                      <tr>
                                        <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <?php if(!empty($ads)) foreach($ads as $ad): ?>
                                                <td><table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <?php echo  form_open("mcp/ads/update",array('name'=>'ads','id'=>'ads'));?>
                                                    <tbody>
                                                      <tr>
                                                        <td height="30" align="center" colspan="3">
														   <?php echo form_hidden('id',$ad->id)?>
														</td>
                                                      </tr>
                                                    												  
													  <tr>
														<td width="15%" class="wd_text"><?php echo _('Copyright Link Text'); ?> :</td>
														<td><input type="text" name="frontend_footer_copyright_link_text" id="frontend_footer_copyright_link_text" value="<?php echo $ad->frontend_footer_copyright_link_text; ?>" size="90"/></td>
														<td>&nbsp;</td>
													  </tr>
													  
													  <tr>
                                                        <td height="30" align="center" colspan="3"></td>
                                                      </tr>
													  
													  <tr>
														<td width="15%" class="wd_text"><?php echo _('Copyright Link URL'); ?> :</td>
														<td><input type="text" name="frontend_footer_copyright_link_url" id="frontend_footer_copyright_link_url" value="<?php echo $ad->frontend_footer_copyright_link_url; ?>" size="90"/></td>
														<td>&nbsp;</td>
													  </tr>
													  
													  <tr>
                                                        <td height="30" align="center" colspan="3"></td>
                                                      </tr>
													  
                                                      <tr>
                                                        <td height="40">&nbsp;</td>
                                                        <td><?php echo form_submit('update_footer_text_message',_('UPDATE'))?></td>
                                                        <td>&nbsp;</td>
                                                      </tr>
													  
                                                    </tbody>
                                                    <?php echo form_close();?>
                                                  </table></td>
                                                <?php endforeach;?>
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
                      <tr>
                        <td height="22" align="right"></td>
                      </tr><?php */?>
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
  </table>
  </td>
  </tr>
  </tbody>
  </table>
  <!-- end of main body -->
</div>
