<script src="<?php echo base_url(); ?>assets/cp/js/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script type="text/javascript">    
	$(document).ready(function() {
		tinymce.init({
			mode : "exact",
			elements: "widget_content",
			theme : "advanced",
			plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template", 
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
	        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",
			theme_advanced_statusbar_location : "bottom",
			resize: "both",
			paste_auto_cleanup_on_paste : true,
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
	        }
		});
	});

	editorCursorPosition=0;
	
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

	function validate(){
		if(tinymce.get('widget_content').getContent() == ''){
			$("#widget_content_parent").addClass("ns_error");
			return false;
		}

		if($("#widget_name").val() == ''){
			$("#widget_name").addClass("ns_error");
			return false;
		}

		return true;
	}
	/*// Sets the HTML contents of the activeEditor editor
	tinyMCE.activeEditor.setContent('<span>some</span> html');

	// Sets the raw contents of the activeEditor editor
	tinyMCE.activeEditor.setContent('<span>some</span> html', {format : 'raw'});

	// Sets the content of a specific editor (my_editor in this example)
	tinyMCE.get('my_editor').setContent(data);

	// Get the HTML contents of the currently active editor
	console.debug(tinyMCE.activeEditor.getContent());

	// Get the raw contents of the currently active editor
	tinyMCE.activeEditor.getContent({format : 'raw'});

	// Get content of a specific editor:
	tinyMCE.get('content id').getContent()*/
</script>

<style>
	.box{
		background-color:#fff;
	}
	.template_wrap{
	  	margin: 30px auto;
    	width: 917px;
    	color: #000000;
	}
	.div_HTML {
	    background-color: #EDF0F9;
	    font-weight: bold;
	    padding: 10px 8px;
	    text-align: left;
	    margin-bottom: 20px;
	    font-size: 14px;
	}
	
	
	.template_wrap #widget_name {
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
	}
	   
	.placeholder_div{
		float: right;
		text-align: center;
	}  
	
	#widget_content{
		width:730px;
	} 	 
	#edit_content_fields option {
		padding-left:10px;
	}
	
</style>
  <!-- MAIN -->
  <div id="main">
    <div id="main-header">
      <h2><?php echo _('Mail Manager')?></h2>
      <span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo('Customer')?></span>
	</div>
    <?php $messages = $this->messages->get();?>
	<?php if(is_array($messages)):?>
	<?php foreach($messages as $key=>$val):?>
		<?php if($val != array()):?>
		<div id="succeed_order_update" class="<?php echo $key;?>"><?php echo $val[0];?></div>
		<?php endif;?>
    <?php endforeach;?>
	<?php endif;?>
	<div id="content">
      <div id="content-container">
        <div class="box">
          <h3><?php echo _('Widget')?></h3>
          <div class="table">
            <div class="template_wrap">
            	<form action="" method="post">
	            	<div class="div_html">
	            		<?php if(!empty($widgets)){?>
	            			<?php echo _("Edit Widget");?>
	            		<?php }else{?>
	            		<?php echo _("Add Widget");?>
	            		<?php }?>
	            		<div class="add_widget">
	            			<div class="edit_content_div">
	            				<textarea id="widget_content" name="widget_content" cols="30" rows="20" ><?php if(!empty($widgets) && isset($widgets['0']['widget_content']) ){ echo $widgets['0']['widget_content']; }?></textarea>
	            			</div>
	            			<div class="placeholder_div">
	            				<select multiple="multiple" style="width:150px; height:146px;border:1px solid #96d945" type="select" id="widget_content_fields" name="widget_content_fields">
									<option value="company_name"><?php echo _('Company Name'); ?></option>
									<option value="first_name"><?php echo _('First Name'); ?></option>
									<option value="last_name"><?php echo _('Last Name'); ?></option>
									<option value="email"><?php echo _('Email'); ?></option>
									<option value="phone"><?php echo _('Phone Number'); ?></option>
									<option value="website"><?php echo _('Website'); ?></option>
									<option value="address"><?php echo _('Address'); ?></option>
									<option value="zipcode"><?php echo _('Zip Code'); ?></option>
									<option value="city"><?php echo _('City'); ?></option>
								</select>
								<br>
								<input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('widget_content');">
	            			</div>
	            			<div style="clear:both"></div>
	            			<?php echo _("Widget Title");?> : <span class="red">*</span>
	            			<input type="text" name="widget_name" id="widget_name" value="<?php if(!empty($widgets) && isset($widgets['0']['widget_name']) ){ echo $widgets['0']['widget_name']; }?>" class="text" />
	            			<?php if(!empty($widgets)){?>
	            			<input type="hidden" id="widget_id" name="widget_id" value="<?php if(!empty($widgets) && isset($widgets['0']['widget_id']) ){ echo $widgets['0']['widget_id']; }?>" />
	            			<input type="submit" id="edit_w" name="edit_w" value="<?php echo _("Edit Widget");?>" onclick="return validate();" />
	            			<?php }else{?>
	            			<input type="submit" id="add_w" name="add_w" value="<?php echo _("Add Widget");?>" onclick="return validate();" />
	            			<?php }?>
	            		</div>
	            	</div>
            	</form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
