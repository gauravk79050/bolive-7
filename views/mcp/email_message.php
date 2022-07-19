<script type="text/javascript" src="<?php echo base_url()?>/assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	 tinyMCE.init({
		theme : "advanced",
		mode : "textareas",
		script_url : '<?php echo base_url()?>assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js',
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
<script type="text/javascript" language="javascript">

editorCursorPosition=0;

// The function called by MCE on "blur"
function tinyMCE_onBlurCallback() {
    // Aparently this is only needed for IE, and seems to give bugs when used if FF
    if (editorCursorPosition == 0 && tinyMCE.isMSIE) {
        editorCursorPosition = tinyMCE.selectedInstance.selection.getBookmark(false);
    }
}

function insertHTML(editor_id, html) {
	tinyMCE.execInstanceCommand(editor_id,"mceInsertContent",false,html);
}

function doInsertTemplate(editor_id) {
    
	var dataToInsert = jQuery('#'+editor_id+'_fields').val();
	
	if(dataToInsert != ''){
		dataToInsert = '{'+dataToInsert+'}';
	}
	
	if (editorCursorPosition) {
        tinyMCE.selectedInstance.selection.moveToBookmark(editorCursorPosition);
    }
    insertHTML(editor_id, dataToInsert);
    editorCursorPosition = 0;
} 
</script>

<div style="">
<!-- start of main body -->
<form action="" enctype="multipart/form-data" method="post" id="frm_email_messages" name="frm_email_messages">
  <input type="hidden" value="email_message" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
  <input type="hidden" value="email_messages" id="act" name="act">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
	  <tr>
		<td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
			<tbody>
			  <tr>
				<td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
					  <tr>
						<td align="center" style="padding-bottom:10px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url();?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
							<tbody>
							  <tr>
								<td width="50%" height="30" align="left"><h3><?php echo _('MAIL MESSAGES - ADMIN'); ?> </h3></td>
								<td width="50%" align="right"><!--<div class="icon_button" style="float:right" ><img src="images/undo.jpg" alt="Go Back" title="Go Back" onClick="history.back()" width="16" height="16" border="0"  /></div>-->
								  <div onClick="document.forms['frm_email_messages'].submit();" title="Save" style="background-image:url(<?php echo base_url();?>assets/mcp/images/save.jpg); cursor:pointer;float:right" class="icon_button"></div></td>
							  </tr>
							</tbody>
						  </table></td>
					  </tr>
					  <tr>
						<td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
							  <tr>
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('New Company Approval Message'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
														<?php if(!empty($content)) { $content = $content[0]; }  ?>
														
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->company_approval_subject; ?>" style="width:635px" class="textbox" id="company_approval_subject" name="company_approval_subject"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_approval_message_fields" name="company_approval_message_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('company_approval_message');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="company_approval_message" id="company_approval_message"><?php echo $content->company_approval_message; ?></textarea>
														
														</td>
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
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"><?php echo _('New Company Disapproval Message'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td><input type="text" value="<?php echo $content->company_disapproval_subject; ?>" style="width:635px" class="textbox" id="company_disapproval_subject" name="company_disapproval_subject"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_disapproval_message_fields" name="company_disapproval_message_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" onClick="doInsertTemplate('company_disapproval_message');" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														
														<!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->
														<textarea name="company_disapproval_message" id="company_disapproval_message"><?php echo $content->company_disapproval_message; ?></textarea>
														</td>
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
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('New Company Approval Message - FREE'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
																												
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->company_approval_subject_free; ?>" style="width:635px" class="textbox" id="company_approval_subject_free" name="company_approval_subject_free"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_approval_message_free_fields" name="company_approval_message_free_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('company_approval_message_free');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="company_approval_message_free" id="company_approval_message_free"><?php echo $content->company_approval_message_free; ?></textarea>
														
														</td>
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
							  
							  <!-- NEW COMPANY APPROVAL MESSAGE - BASIC : START -->
							  <tr>
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('New Company Approval Message - BASIC'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
																												
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->company_approval_subject_basic; ?>" style="width:635px" class="textbox" id="company_approval_subject_basic" name="company_approval_subject_basic"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_approval_message_basic_fields" name="company_approval_message_basic_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('company_approval_message_basic');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="company_approval_message_basic" id="company_approval_message_basic"><?php echo $content->company_approval_message_basic; ?></textarea>
														
														</td>
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
							  <!-- NEW COMPANY APPROVAL MESSAGE - BASIC : END -->
                              
                              <!-- NEW COMPANY APPROVAL MESSAGE - PRO : START -->
							  <tr>
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('New Company Approval Message - PRO'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
																												
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->company_approval_subject_pro; ?>" style="width:635px" class="textbox" id="company_approval_subject_pro" name="company_approval_subject_pro"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_approval_message_pro_fields" name="company_approval_message_pro_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('company_approval_message_pro');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="company_approval_message_pro" id="company_approval_message_pro"><?php echo $content->company_approval_message_pro; ?></textarea>
														
														</td>
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
							  <!-- NEW COMPANY APPROVAL MESSAGE - PRO : END --> <?php */ ?>
							  
                              <tr>
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('Trail Message For PRO And Basic'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
																												
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->company_trial_subject_basic_pro; ?>" style="width:635px" class="textbox" id="company_trial_subject_basic_pro" name="company_trial_subject_basic_pro"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_trial_message_basic_pro_fields" name="company_trial_message_basic_pro_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('company_trial_message_basic_pro');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="company_trial_message_basic_pro" id="company_trial_message_basic_pro"><?php echo $content->company_trial_message_basic_pro; ?></textarea>
														
														</td>
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
							  
							  <!-- Company trial mail sent to admin -->
							  <tr>
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('Trail Message For PRO And Basic to MCP-Admin'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
																												
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->company_trial_subject_basic_pro_mcp; ?>" style="width:635px" class="textbox" id="company_trial_subject_basic_pro_mcp" name="company_trial_subject_basic_pro_mcp"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="company_trial_message_basic_pro_mcp_fields" name="company_trial_message_basic_pro_mcp_fields">
																<option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('company_trial_message_basic_pro_mcp');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="company_trial_message_basic_pro_mcp" id="company_trial_message_basic_pro_mcp"><?php echo $content->company_trial_message_basic_pro_mcp; ?></textarea>
														
														</td>
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
							  <!-- -------------------------------- -->
							  
							  <?php /* // order online mail message ?>
							  <tr>
								<td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(images/pink_table_bg.jpg) left repeat; text-align:left; text-align:left;">
									<tbody>
									  <tr>
										<td class="whiteSmallBold"> <?php echo _('Order Online Message For Free'); ?> </td>
									  </tr>
									  <tr>
										<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid"><table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
											  <tr>
												<td><table width="100%" cellspacing="0" cellpadding="0" border="0">
													<tbody>
													  <tr>
														<td width="15%" height="40" class="wd_text"><?php echo _('Subject'); ?> :</td>
														<td>
																												
														<input type="hidden" name="id" value="<?php echo $content->id;?>" />
														
														<input type="text" value="<?php echo $content->order_online_subject_free; ?>" style="width:635px" class="textbox" id="order_online_subject_free" name="order_online_subject_free"></td>
														<td width="30%" valign="top" height="100%" rowspan="2"><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															  <tr>
																<td valign="top" style="padding:10px 0px 0px 30px">
																<select multiple="multiple" style="width:150px; height:290px;border:1px solid #96d945" type="select" id="order_online_message_free_fields" name="order_online_message_free_fields">
																<option value="name"><?php echo _('Name'); ?></option>
																<option value="base_url"><?php echo _('Base Url'); ?></option>
																<!-- <option value="company_name"><?php echo _('Company Name'); ?></option>
																<option value="username"><?php echo _('Username'); ?></option>
																<option value="password"><?php echo _('Password'); ?></option>
																<option value="first_name"><?php echo _('First Name'); ?></option>
																<option value="last_name"><?php echo _('Last Name'); ?></option>
																<option value="email"><?php echo _('Email'); ?></option>
																<option value="phone"><?php echo _('Phone Number'); ?></option>
																<option value="website"><?php echo _('Website'); ?></option>
																<option value="address"><?php echo _('Address'); ?></option>
																<option value="zipcode"><?php echo _('Zip Code'); ?></option>
																<option value="city"><?php echo _('City'); ?></option>
																<option value="admin_remarks"><?php echo _('Admin\'s Remark'); ?></option>
																<option value="expiry_date"><?php echo _('Expiry Date'); ?></option>
																<option value="registration_date"><?php echo _('Registeration Date'); ?></option>
																<option value="earnings_year"><?php echo _('Earnings Yearly'); ?></option>
																<option value="photographer"><?php echo _('Photographer'); ?></option>
																<option value="link"><?php echo _('Secure Content Link'); ?></option>
																<!-- <option value="direct_login_link"><?php echo _('Direct Login Link'); ?></option> -->
																  </select>
																  <br>
																  <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('order_online_message_free');"><br /><br /></td>
															  </tr>
															</tbody>
														  </table></td>
													  </tr>
													  <tr>
														<td width="15%" height="30" class="wd_text"><?php echo _('Message'); ?>:</td>
														<td>
														  <!--<img src="<?php //echo base_url();?>assets/mcp/images/message.png" />-->                                                       
														  <textarea name="order_online_message_free" id="order_online_message_free"><?php echo $content->order_online_message_free; ?></textarea>
														
														</td>
													  </tr>
													</tbody>
												  </table></td>
											  </tr>
											</tbody>
										  </table></td>
									  </tr>
									</tbody>
								  </table></td>
							  </tr> <?php */ ?>
							</tbody>
						  </table></td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
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
  </table>
</form>
<!-- end of main body -->
</div>