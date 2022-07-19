<script>jQuery.noConflict();</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/js/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.form.js" type="text/javascript"></script>	
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>

<script type="text/javascript">
var editorCursorPosition = 0;
	jQuery(function(){ //DOM Ready
		 
		jQuery("#submit").click(function(){

			var datas = tinymce.get('widget_content').getContent();
			
			if(datas == null ){
				alert("<?php echo _('Please made content for the newsletter');?>");
				return false;
			}
			
			if(jQuery("#title").val() == '' ){
				alert("<?php echo _('Please provide any title to this newsletter');?>");
				return false;
			}

			var attach = '';
			if(jQuery("#attachment").length > 0){
				var doc_name = $('#attachment').find('option[value="'+$('#attachment').val()+'"]').text();
				//attach = jQuery("#a_name").val();
			}
			
			jQuery.post(
					'<?php echo base_url();?>mcp/mail_manager/save_newsletter',
					{'info':datas, 'title': jQuery("#title").val(), 'ns_id':jQuery("#newsletter_id").val(), 'template_id':jQuery('#select_template').val(), 'from':jQuery('#from').val(), 'attachment': doc_name },
					function(response){
						if(response.error){
							
						}else{
							window.location = '<?php echo base_url();?>mcp/mail_manager/newsletters';
						}
					},
					'json'
				);
		});

		tinymce.init({
			mode : "exact",
			elements: "widget_content",
			theme : "advanced",
			plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template", 
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
	        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,mybutton,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",
			theme_advanced_statusbar_location : "bottom",
			relative_urls: false,
			remove_script_host : false,
			//convert_urls : false,
			theme_advanced_resizing : true,
			//theme_advanced_resizing_max_width: '400',
			//theme_advanced_resizing_max_height: '300',
			theme_advanced_resize_horizontal : false,
			width: '100%',
			content_css : base_url+"assets/mcp/new_css/content.css",
			/*paste_auto_cleanup_on_paste : true,
	        paste_strip_class_attributes : true,
	        paste_remove_styles_if_webkit : true, 
	        paste_preprocess : function(pl, o) {
	            // Content string containing the HTML from the clipboard
	            //alert(o.content);
	            o.content = o.content;
	        },
	        paste_postprocess : function(pl, o) {
	            // Content DOM node containing the DOM structure of the clipboard
	            //alert(o.node.innerHTML);
	            o.node.innerHTML = o.node.innerHTML;
	        },*/
	        setup : function(ed) {
	            // Add a custom button
	            ed.addButton('mybutton', {
	                title : 'My button',
	                image : base_url+'assets/cp/images/tm-image-icon.png',
	                onclick : function() {
	                	ed.nodeChanged();
	                	tb_show("<?php echo _("Select Image");?>", base_url+"mcp/mail_manager/image_manager_popup?height=160&width=675", "true");
	                }
	            });
	        }
		});

	});

	$(document).on('change','#attachment',function(){
		if($(this).val() != '0'){
			var doc_name = $('#attachment').find('option[value="'+$(this).val()+'"]').text();
			//$('.att_preview').html('');
			$('.att_preview').html(' \
					<a href="<?php echo base_url(); ?>assets/upload_center/docs/'+doc_name+'" target="_blank">'+doc_name+'</a> \
					<a onclick="remove_attachment();" href="javascript:void(0);"><img src="<?php echo base_url(); ?>assets/cp/images/delete-2.png" style="vertical-align:middle;"></a>');
		}
		else{
			$('.att_preview').html('');
		}
	});
	
	function remove_attachment(){
		$('.att_preview').html('');
		$('#attachment').val('0');
	}
	
	
	// The function called by MCE on "blur"
	function tinyMCE_onBlurCallback() {
	    // Aparently this is only needed for IE, and seems to give bugs when used if FF
	    if (editorCursorPosition == 0 && tinymce.isMSIE) {
	        editorCursorPosition = tinymce.selectedInstance.selection.getBookmark(false);
	    }
	}
	
	function insertHTML(editor_id, html) {
		tinymce.execInstanceCommand(editor_id,"mceInsertContent",false,html);
	}
	
	function doInsertTemplate(editor_id) {
	    
		var dataToInsert = jQuery('#'+editor_id+'_fields').val();
		
		if(dataToInsert != ''){
			dataToInsert = '{'+dataToInsert+'}';
		}
		
		if (editorCursorPosition) {
	        tinymce.selectedInstance.selection.moveToBookmark(editorCursorPosition);
	    }
	    insertHTML(editor_id, dataToInsert);
	    editorCursorPosition = 0;
	} 

	function doPreviewImage(){
		jQuery('#image_preview').toggle();
		jQuery('#image_preview').toggleClass('hide_img');
		var img_name = jQuery('#widget_insert_image').val();
		if(typeof img_name != 'undefined' && jQuery('#image_preview').hasClass('hide_img')){
			jQuery('#image_preview').html("<img src='<?php echo base_url(); ?>assets/upload_center/images/"+img_name+"' alt='<?php echo 'Image Not Found'; ?>' width='150px' height='150px' style='padding:5px;' >");
		}
	}

	function doInsertImage(img_name) {
		var imageToInsert = "<img src='<?php echo base_url(); ?>assets/upload_center/images/"+img_name+"' >";
		
		if (editorCursorPosition) {
	        tinymce.selectedInstance.selection.moveToBookmark(editorCursorPosition);
	    }
	    tinyMCE.execCommand('mceInsertContent', false, "<img src='<?php echo base_url(); ?>assets/upload_center/images/"+img_name+"' style='vertical-align: text-top;' >");
	    editorCursorPosition = 0;
	} 
</script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/cp/new_css/jquery.gridster.css">
<style>
	.template_wrap{
	  	margin: 30px auto;
    	width: 917px;
    	color: #000000;
	}
		
	.div_head {
		background-color: #EDF0F9;
	   	padding: 10px 8px;
	    text-align: right;
	}
		
	.div_title {
		height: 51px;
		padding: 10px 8px;
		text-align: left;
	}
		
	
	.template_wrap .text {
		float: right;
    	margin-right: 10px;
    	width: 72%;
	}
	
	.template_wrap #w_title {
    	margin-right: 10px;
    	width: 59%;
	}
	
	.template_wrap .red {
		color: red;
	}	
	
	.template_wrap .ns_error {
		border: 1px solid red;
	}
	
	.edit_content_div{
		float:left;
		margin-bottom: 20px;
		margin-left: 10px;
	}
	   
	.placeholder_div{
		float: right;
		text-align: center;
	}  
	
	#edit_content{
		width:730px;
	} 	 
	#edit_content_fields option {
		padding-left:10px;
	}
	
	
	#progress {
		text-align: center;
		font-weight: bold;
		display: none;
		padding: 10px 0;
	}
	
	#progress img {
		vertical-align: middle;
	}
	
	/*Custom css*/
	/*#TB_ajaxContent{
		overflow: hidden;
		background-color: #2E3134;
	}*/
	
	div.box .div_html {
	    background-color: #EDF0F9;
	    font-size: 14px;
	    font-weight: bold;
	    margin-bottom: 20px;
	    padding: 10px 8px;
	    position: relative;
	    text-align: left;
	}
	
	.placeholder_div{
		position: absolute;
		top: 0;
		right: -195px;
	}
	
	div.box .div_html a {
	    color: #005DA0;
		font-size: 14px;
		font-weight: bold;
		text-decoration:underline;
	}

	.placeholder_div > h3 {
		margin: 0 !important;
	}

	.defaultSkin table, .defaultSkin tbody, .defaultSkin a, .defaultSkin img, .defaultSkin tr, .defaultSkin div, .defaultSkin td, .defaultSkin iframe, .defaultSkin span, .defaultSkin *, .defaultSkin .mceText{
		white-space: inherit;
	}
	
	/*.mceIframeContainer iframe{
		width: 880px !important;
	}*/
</style>
  <!-- MAIN -->
  <div id="main">
	<div id="content">
      <div id="content-container">
        <div class="box" style="width: 98%;">
          <h3><?php echo _('Templates')?></h3>
          <div class="table">
            <div class="template_wrap">
            	<form>
	            	<div class="div_head">
	            		<input type="button" name="submit" id="submit" value="<?php echo _("Save");?>" />
	            	</div>
	            	<div class="div_title">
	            		<lable class="span_t">
	            			<?php echo _("Title");?> : <span class="red">*</span>
	            		</lable>
	            		<lable class="span_ta">
	            			<input type="text" name="title" id="title" value="<?php if(isset($newsLetter) && !empty($newsLetter)){ echo $newsLetter['0']['name']; }?>" class="text" />
	            			<input type="hidden" name="newsletter_id" id="newsletter_id" value="<?php if(isset($newsLetter) && !empty($newsLetter)){ echo $newsLetter['0']['id']; }?>"/>
	            		</lable>
	            	</div>
	            	
	            	<div class="div_title">
	            		<lable class="span_t">
	            			<?php echo _("From");?> : <span class="red">*</span>
	            		</lable>
	            		<lable class="span_ta">
	            			<input type="text" name="from" id="from" value="<?php echo $this->config->item('site_admin_name'); ?>" class="text" />
	            		</lable>
	            	</div>
	            	
	            	<div class="div_title">
	            		<lable class="span_t">
	            			<?php echo _("Use templates");?> : <span class="red">*</span>
	            		</lable>
	            		<lable class="span_ta">
	            			<select id="select_template" name="select_template" class="text" onChange="javascript: load_template(this.value);">
	            				<option value="0" ><?php echo _('Select Template');?></option>
								<?php if(!empty($templates)){?>
	            					<?php foreach ($templates as $template):?>
									<option value="<?php echo $template['id'];?>" <?php if(isset($newsLetter) && $newsLetter['0']['template_id'] == $template['id']){?>selected="selected"<?php }?>><?php echo $template['name'];?></option>
								<?php endforeach; 
								}?>
	            			</select>
	            		</lable>
	            	</div>
	            	
	            	<div class="div_title">
	            		<?php $doc_name = (isset($newsLetter)?$newsLetter[0]['attachment']:' '); ?>
	            		<lable class="span_t">
	            			<?php echo _("Attachment");?> : 
	            		</lable>
	            		<lable class="att_preview"><?php if($doc_name){ ?>
	            			<a href="<?php echo base_url(); ?>assets/upload_center/docs/<?php echo $doc_name; ?>" target="_blank"><?php echo $doc_name; ?></a> 
	            			<a onclick="remove_attachment();" href="javascript:void(0);"><img src="<?php echo base_url(); ?>assets/cp/images/delete-2.png" style="vertical-align:middle;"></a>
	            		<?php } ?></lable>
	            		<lable class="span_ta">
	            			<span id="attach_span">
								<select name="attachment" id="attachment" class="text">
									<option value="0" selected="selected"><?php echo _('Select Document'); ?></option>
									<?php if(!empty($docs)){ ?>
		            				<?php foreach($docs as $doc){ 
		            					if($doc['doc_name'] == $doc_name){
		            						echo '<option value="'.$doc['id'].'" selected="selected">'.$doc['doc_name'].'</option>';
		            					}
		            					else{
											echo '<option value="'.$doc['id'].'">'.$doc['doc_name'].'</option>';			            						
		            					}
		            				}?>
		            				<?php }?>
		            			</select>
							</span>
	            		</lable>
	            	</div>
	            	
	            	<div class="div_html">
	            		<a href="javascript: void(0);"><?php echo _("HTML Version");?></a>
	            		<textarea id="widget_content" name="widget_content"><?php if(isset($newsLetter) && !empty($newsLetter)){ echo stripslashes($newsLetter['0']['content']); }?></textarea>
	            		
	            		<div class="placeholder_div">
            				<h3><?php echo _('INSERT CONTENT FIELDS'); ?></h3>
            				<select multiple="multiple" style="width:150px; height:180px;border:1px solid #96d945" type="select" id="widget_content_fields" name="widget_content_fields">
								<option value="company_name"><?php echo _('Company Name'); ?></option>
								<option value="first_name"><?php echo _('First Name'); ?></option>
								<option value="last_name"><?php echo _('Last Name'); ?></option>
								<option value="email"><?php echo _('Email'); ?></option>
								<option value="phone"><?php echo _('Phone Number'); ?></option>
								<option value="website"><?php echo _('Website'); ?></option>
								<option value="address"><?php echo _('Address'); ?></option>
								<option value="zipcode"><?php echo _('Zip Code'); ?></option>
								<option value="city"><?php echo _('City'); ?></option>
								<option value="direct_link"><?php echo _('Direct Login Link'); ?></option>
								<option value="unsubscribe_link"><?php echo _('Unsubscription Link'); ?></option>
							</select>
							<br>
							<input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('widget_content');">
            			</div>
	            	</div>
            	</form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
