<link type="text/css" rel="stylesheet" href="<?php echo base_url();?>assets/cp/new_css/dhtmlgoodies_calendar.css?random=20051112">
<script src="<?php echo base_url();?>assets/cp/new_js/dhtmlgoodies_calendar.js?random=20060118" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	 tinyMCE.init({
		theme : "advanced",
		mode : "textareas",
		script_url : '<?php echo base_url();?>assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js',
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
  <form action="<?php echo base_url();?>mcp/notifications/notification_addedit" enctype="multipart/form-data" method="post" id="frm_noti_addedit" name="frm_noti_addedit">
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
                                  <td width="94%" align="left"><h3><?php echo _('Add Notification'); ?></h3></td>
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
                                  <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="2"><?php echo _('Notification Information'); ?></td>
                                </tr>
                                <tr>
                                  <td height="10" colspan="2"></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Subject'); ?>&nbsp;&nbsp;</td>
                                  <td width="79%" height="31" style="padding-left:10px;"><input type="text" style="width:630px;height: 20px" class="textbox" id="subject" name="subject" value="<?php if(isset($content)){ echo $content[0]['subject'] ;}else { echo set_value('subject'); } ?>" /></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Notification'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;"><textarea cols="30" rows="3" class="textbox" id="noties" name="noties" > <?php if(isset($content)){ echo $content[0]['notification'] ;}else { echo set_value('noties'); } ?> </textarea></td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('For Account Type'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;">
                                  	<table>
	                                  	<?php if(isset($type)){
	                                  			foreach($type as $option){?>
	                                  			<tr>
	                                  				<td><?php echo $option->ac_title; ?></td>
	                                  				<td><input <?php if(in_array($option->id, json_decode($content[0]['company_type']))){ ?> checked <?php } ?> type="checkbox" name="acc_type[]" value="<?php echo $option->id ;?>" /></td>
	                                  			</tr>
	                                  	<?php }}?>
                                  	</table>
                                  </td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('For Company Type'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;">
                                    <table>
                                      <?php if(isset($comp_type)){
                                          foreach($comp_type as $option){?>
                                          <tr>
                                            <td><?php echo $option->company_type_name; ?></td>
                                            <td><input <?php if(in_array($option->id, json_decode($content[0]['company_type_id']))){ ?> checked <?php } ?> type="checkbox" name="company_type_name[]" value="<?php echo $option->id ;?>"/></td>
                                          </tr>
                                      <?php }}?>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td width="21%" height="30" class="wd_text"><?php echo _('Up To'); ?><span class="red_star">*</span></td>
                                  <td width="79%" height="31" style="padding-left:10px;">
                                  	<div style="float:left">
                                      <input type="text" class="text" readonly="readonly" name="upto_date" id="upto_date" value="<?php if(isset($content)){ echo $content[0]['upto_date'] ;}?>">
                                    </div>
					  				                 <div style="float:left">
                                      <input type="button" value="..." onclick="displayCalendar(document.frm_noti_addedit.upto_date,'yyyy-mm-dd',this)" name="button1" id="button1">
                                    </div>
                                    <div style="float:left">
                                      <select name="companies_language" class="textbox"  style="margin-left: 90px;margin-top: 3px;">
                                          <option <?php if(  $content[0]['companies_lang'] == '2' ){ echo 'selected="selected"'; } ?> value="2">DU</option>
                                          <option <?php if(  $content[0]['companies_lang'] == '3' ){ echo 'selected="selected"'; } ?> value="3">FR</option>
                                          <option <?php if(  $content[0]['companies_lang'] == '1' ){ echo 'selected="selected"'; } ?> value="1">EN</option>
                                        </select>
                                    </div>
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
                                                  <?php if(isset($content)){?>
                                                  	<td>
                                                  	<input type="hidden" value="<?php echo $content[0]['id'];?>" id="n_id" name="n_id">
                                                  	<input type="submit" value="<?php echo _('Update Notification'); ?>" class="btnWhiteBack" id="btn_update" name="btn_update" >
                                                  	</td>
                                                  <?php }else{?>
                                                  	<td><input type="submit" value="<?php echo _('Add Notification'); ?>" class="btnWhiteBack" id="btn_add" name="btn_add" ></td>
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
 
</div>
</body></html>