<script>jQuery.noConflict();</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/jquery-1.9.1.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/js/tiny_mce/tiny_mce.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.gridster.js" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/js/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.form.js" type="text/javascript"></script>	
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.ui.1.10.4.js" type="text/javascript"></script>	
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/cp/new_css/jquery.ui.1.10.4.css"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/js/thickbox/css/thickbox.css" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/thickbox/javascript/thickbox.js"></script>

<script type="text/javascript">

var gridster;
var datas = new Array();
<?php if(isset($newsLetter) && !empty($newsLetter)){ 
	$content = json_decode($newsLetter['0']['content'],true);
}
?>
var widget_txt_num = <?php echo (!empty($content))?count($content)+1:'1'; ?>;
//var widget_txt_num = 1;
var widget_img_num = 1;
var editorCursorPosition = 0;

	jQuery(function(){ //DOM Ready
		 
		gridster = jQuery(".gridster > ul").gridster({
			namespace: '.gridster',
	        widget_margins: [10, 10],
	        widget_base_dimensions: [140, 140],
	        resize: {
	            enabled: true
	        }/*,
	        draggable:{
	        	 handle: '.drag'
		    }*/
	    }).data('gridster');

		jQuery("#submit").click(function(){

			var gridster_array = gridster.serialize();
			var counter = 0;
			jQuery("#layout li").each(function(index){
				datas[counter] = [ jQuery(this).find(".widgetContent").html(), jQuery(this).attr("data-col"), jQuery(this).attr("data-row"), jQuery(this).attr("data-sizex"), jQuery(this).attr("data-sizey"), jQuery(this).find(".widgetContent").css("background-color"), jQuery(this).find(".widgetContent").css("color")];
				counter++; 
			});

			//alert(gridster.serialize().toSource());
			if(datas.length === 0 ){
				alert("<?php echo _('Please made content for the newsletter');?>");
				return false;
			}
			
			if(jQuery("#title").val() == '' ){
				alert("<?php echo _('Please provide any title to this newsletter');?>");
				return false;
			}
			
			jQuery.post(
					'<?php echo base_url();?>mcp/mail_manager/save_templates',
					{'info':datas, 'title': jQuery("#title").val(), 'ns_id':jQuery("#newsletter_id").val()},
					function(response){
						if(response.error){
							
						}else{
							window.location = '<?php echo base_url();?>mcp/mail_manager/templates';
						}
					},
					'json'
				);
		});

		jQuery('.js-resize-random').on('click', function() {
            gridster.resize_widget(gridster.$widgets.eq(getRandomInt(0, 4)),
                getRandomInt(1, 4), getRandomInt(1, 4))
        });

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
			relative_urls: false,
			remove_script_host : false,
			convert_urls : false,
			theme_advanced_resizing : true,
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

		jQuery(".colors").minicolors();

	});

	jQuery(document).on("click",".edit_me",function(e){
		var content = jQuery(this).parents('li').find('div.widgetContent').html();

		tinyMCE.get('widget_content').setContent(content);
		jQuery("#bg_color").val(jQuery(this).parents('li').css('background-color'));
		//jQuery("#bg_color").val(jQuery(this).parents('li').find('div.widgetContent').css('background-color'));
		jQuery("#text_color").val(jQuery(this).parents('li').find('div.widgetContent').css('color'));
		
		jQuery("#edit_w").attr("onclick","edit_widget('"+jQuery(this).parents('li').attr('id')+"')");
		jQuery("#edit_w").show();
		jQuery("#add_w").hide();
		jQuery("#add_widget_txt").show();
		
	});
	
	jQuery(document).on("click",".remove_me",function(e){
 		//gridster.remove_widget( jQuery(this).parent().parent().parent() );
 		gridster.remove_widget( jQuery(this).parents('li')  );	
 		tinyMCE.get('widget_content').setContent('');
 		jQuery("#add_widget_txt").hide();
	});
	
	function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

	function show_preview(){
		var gridster_array = gridster.serialize();
		var counter = 0;
		datas = [];
		
		jQuery("#layout li").each(function(index){
			datas[counter] = [ jQuery(this).find(".widgetContent").html(), jQuery(this).attr("data-col"), jQuery(this).attr("data-row"), jQuery(this).attr("data-sizex"), jQuery(this).attr("data-sizey"), jQuery(this).css("background-color"), jQuery(this).find(".widgetContent").css("color")];
			//datas[counter] = [ jQuery(this).find(".widgetContent").html(), jQuery(this).attr("data-col"), jQuery(this).attr("data-row"), jQuery(this).attr("data-sizex"), jQuery(this).attr("data-sizey"), jQuery(this).find(".widgetContent").css("background-color"), jQuery(this).find(".widgetContent").css("color")];
			counter++; 
		});
		
		jQuery("#progress").show();
		jQuery.post(
				'<?php echo base_url();?>mcp/mail_manager/generate_preview',
				{'info':datas},
				function(response){
					if(response.trim() == 'error'){
						alert("<?php echo _('Sorry!!! Preview can not be generated. Please try again.');?>");
					}else{
						jQuery("#preview").html(response);
						jQuery("#preview").show();
						jQuery("#progress").hide();
					}
				}
			);
	}

	function add_widget_img_new(){
		
		var li_content = "";
		var wid = $('.resizable_img').width();
		var hei = $('.resizable_img').height();
		li_content += "<li id='w_t_"+widget_img_num+"' class='new' data-minx="+Math.ceil(wid/140)+" data-miny="+Math.ceil(hei/140)+" >";
		li_content += " <span class='action'>";
		li_content += "		<a href='javascript: void(0);' class='remove_me' ><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("remove");?>' /></a>";
		li_content += " </span>";
		li_content += " <div rel='"+widget_img_num+"' class='widgetContent' width="+wid+" height="+hei+" style='background-color:"+$("#bg_color_img").val()+";'><img src='<?php echo base_url();?>assets/images/mail_manager/"+$("#image_name").val()+"' style='max-width: 100%; max-height: 100%;width: "+wid+"px;height: "+hei+"px' /></div>";
		li_content += "</li>";
		gridster.add_widget(li_content, Math.ceil(wid/140), Math.ceil(hei/140));
		jQuery("#uploaded_image").html('');
		jQuery("#x").val('');
		jQuery("#y").val('');
		jQuery("#w").val('');
		jQuery("#h").val('');
		
		jQuery("#add_widget_img").hide();
		widget_img_num++;
	}
	
	function add_widget_new(){

		if(tinymce.get('widget_content').getContent() == ''){
			jQuery("#widget_content_parent").addClass("ns_error");
			return false;
		}

		var li_content = "";
		li_content += "<li id='w_t_"+widget_txt_num+"' class='new' style='background-color:"+$("#bg_color").val()+";'>";
		li_content += " <span class='action'>";
		li_content += "		<a href='javascript: void(0);' class='edit_me' ><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' width='50px' /></a>";
		li_content += "		<a href='javascript: void(0);' class='remove_me' ><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("remove");?>' /></a>";
		li_content += " </span>";
		li_content += " <div rel='"+widget_txt_num+"' class='widgetContent' style='color:"+jQuery("#text_color").val()+";'>"+tinymce.get('widget_content').getContent()+"</div>";
		li_content += "</li>";
		gridster.add_widget(li_content, 5, 1);
		tinyMCE.get('widget_content').setContent('');
		jQuery("#add_widget_txt").hide();
		widget_txt_num++;
	}

	function edit_widget(widgetId){
		if(tinymce.get('widget_content').getContent() == ''){
			jQuery("#widget_content_parent").addClass("ns_error");
			return false;
		}

		jQuery("#"+widgetId).find(".widgetContent").html(tinymce.get('widget_content').getContent());
		jQuery("#"+widgetId).css('background-color',jQuery("#bg_color").val());
		//jQuery("#"+widgetId).find(".widgetContent").css('background-color',jQuery("#bg_color").val());
		jQuery("#"+widgetId).find(".widgetContent").css('color',jQuery("#text_color").val());
		
		tinyMCE.get('widget_content').setContent('');
		jQuery("#add_widget_txt").hide();
	}
	
	
	// The function called by MCE on "blur"
	/*function tinyMCE_onBlurCallback() {
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
	} */

	function show_content(type){
		if(type == 'txt'){
			//jQuery("#edit_w").hide();
			//jQuery("#add_w").show();
			jQuery("#add_widget_img").hide();
			jQuery("#add_widget_txt").show();
		}else{
			jQuery("#add_widget_img").show();
			jQuery("#add_widget_txt").hide();
		}
	}
	
</script>
<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js"></script>
<script type="text/javascript">
var jcrop_api,
boundx,
boundy,xsize,ysize,$preview,$pcnt,$pimg;

jQuery(document).ready(function(){

	jQuery(".thickboxed").click(function(){
		tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>mcp/mail_manager/ajax_img_upload?height=80&width=600", "true");
	});
	
  });

  function updateCoords(c)
  {
	jQuery('#x').val(c.x);
	jQuery('#y').val(c.y);
	jQuery('#w').val(c.w);
	jQuery('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt(jQuery('#w').val())) return true;
    alert("<?php echo _('Please select a crop region then press submit.');?>");
    return false;
  };

  function updatePreview(c)
  {
    if (parseInt(c.w) > 0)
    {
      var rx = xsize / c.w;
      var ry = ysize / c.h;

      $pimg.css({
        width: Math.round(rx * boundx) + 'px',
        height: Math.round(ry * boundy) + 'px',
        marginLeft: '-' + Math.round(rx * c.x) + 'px',
        marginTop: '-' + Math.round(ry * c.y) + 'px'
      });
    }
  };

	function crop(){
		//alert("cropping");
		jQuery("#uploaded_image").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
		jQuery.ajax({
			url : base_url+'mcp/mail_manager/crop_image',
			data : {'image_name': jQuery("#image_name").val(), 'x': jQuery("#x").val(), 'y': jQuery("#y").val(), 'w': jQuery("#w").val(), 'h': jQuery("#h").val()},
			type: 'POST',
			success: function(response){
				//$("#uploaded_image").toggle("slow");
				jQuery("#uploaded_image").html(response);
				jQuery("#uploaded_image").focus();
				//$("#uploaded_image").toggle("slow");
				$('#uploaded_image').addClass('resizable');
			    $('#uploaded_image img').addClass('resizable_img');
			    $("#uploaded_image").draggable();
				$("#uploaded_image img").resizable();
				$('#uploaded_image img').width($("#w").val());
				$('#uploaded_image img').height($("#h").val());
				$('#uploaded_image img').parent('div').width($("#w").val());
				$('#uploaded_image img').parent('div').height($("#h").val());
			}
		});
	};
  
</script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css"/>
<style type="text/css">
ul, ol {
    list-style: none outside none;
}

.preview_title{
    display: block;
    font-size: 20px;
    font-weight: bold;
    margin: 10px auto;
    text-align: center;
    text-decoration: underline;
}

.jcrop-holder #preview-pane {
  display: block;
  position: absolute;
  /*z-index: 2000;*/
  top: -2px;
  right: -260px;
  padding: 6px;
  border: 1px rgba(0,0,0,.4) solid;
  background-color: white;

  -webkit-border-radius: 6px;
  -moz-border-radius: 6px;
  border-radius: 6px;

  -webkit-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
}

/* The Javascript code will set the aspect ratio of the crop
   area based on the size of the thumbnail preview,
   specified here */
#preview-pane .preview-container {
  width: 220px;
  height: 209px;
  overflow: hidden;
}
#TB_window{
	z-index: 999 !important;
}
#crop_button{
	background-color:#007a96;
    padding:12px 26px;
    color:#fff;
    font-size:14px;
    border-radius:2px;
    cursor:pointer;
    display:inline-block;
    line-height:1;
    border: none;
}
.crop_div{
	margin-top: 30px; 
	text-align: center;
}

#GroupsTable input.medium, #GroupsPersonTable input.medium, #WGroupsTable input.medium{
	width: 100%;
}
</style>
<!-- -------------------------------------------------------------------------------------------- -->

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/cp/new_css/jquery.gridster.css">
<style>
	.box{
		background-color:#fff;
	}
	.div_content{
		background-color:#F4F4F4;
		border: 1px solid #CCCCCC;
		padding: 50px;
	}
	.div_content ul li {
		background-color:#FFFFFF;
	}
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
		
	.div_HTML {
	    background-color: #EDF0F9;
	    font-weight: bold;
	    padding: 10px 8px;
	    text-align: left;
	    margin-bottom: 20px;
	    font-size: 14px;
	    position:relative;
	}
	
	p.content_tag{
		font-size: 12px;
  		font-weight: bold;
  		padding: 10px 0 4px;
	}
	.gridster .preview-holder {
	    border: none!important;
	    background: red!important;
	}
		
	.template_wrap #title {
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
	
	.gridster li p.drag {
	    /*background: #2D6CB1;*/
	    display: block;
	    /*font-size: 20px;*/
	    line-height: normal;
	    /*padding: 4px 0 6px ;*/
		cursor: move;
		text-align: center;
		height:25px;
	}
	   
	.gridster li {
		cursor: move;
	}
	 
	.gridster li span.action{
	    /*float: right;*/
		/*margin: 0 10px 0 0;*/
		position: absolute;
	    right: 0;
	    top: 0;
	}
	
	.gridster .remove_me img, .gridster .edit_me img{
	    vertical-align: middle;
	    width: 15px;
	}
	
	.edit_content_div{
		float:left;
		margin-bottom: 20px;
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
	
	.add_widget{
		
	}
	.show_widget .widget{
		background: none repeat scroll 0 0 #F4F4F4;
	    border: 1px solid #CCCCCC;
	    float: left;
	    margin: 10px;
	    padding: 10px 10px 4px;
	    /*width: 138px;*/
	}
	
	.edit_w {
	    float: right;
	}
	.show_widget .widget input {
		background: none repeat scroll 0 0 #2D6CB1;
	    border: medium none;
	    color: #FFFFFF;
	    padding: 3px;
	}
		.show_widget .widget input:hover{
		background: none repeat scroll 0 0 #cccccc;
		color:#2D6CB1;
	}
	.show_widget .widget p{
	    margin-bottom: 18px;
	}
	
	.widgetContent {
		align-items: center;
	    /*background-color: #F4F4F4;*/
	    height: auto;
	    /*margin: auto 10px;
	    padding: 14px;*/
	    width: auto;
	}
	
	#preview{
		background-color: #FFFFFF;
	    margin: 50px;
	    padding: 10px;
	    display:none;
	}
	
	#preview p{
		margin: 0;
	}
	
	#preview table td{
		border: none;
		padding: 0;
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
	
	#add_widget_img .ui-icon, #add_widget_img .ui-widget-content .ui-icon {
    background-color: #FFFFFF !important;
	}
	#add_widget_img .ui-icon-gripsmall-diagonal-se {
	    background-position: -80px -224px !important;
	}
</style>
  <!-- MAIN -->
  <div id="main">
	<div id="content">
      <div id="content-container">
        <div class="box" style="width: 98%">
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
	            	<div class="div_html">
	            		<a href="javascript: void(0);" onClick="javascript: show_preview();"><?php echo _("HTML Version");?></a>
	            		<div id="progress"><?php echo _("Generating Preview");?> <img src="<?php echo base_url();?>assets/cp/images/20122139137.GIF" alt="..."/></div>
	            		<div id="preview" style="position:relative;"></div>
	            	</div>
	            	
	            	<div class="div_html">
	            		<?php echo _("Widgets");?>
	            		<div class="show_widget">
	            			<div id="widget_txt" class="widget">
								<p ><?php echo _("Text");?></p>
								<small class="description"><?php echo _("Arbitrary text or HTML");?></small>
								<input type="button" id="add_w_txt" name="add_w_txt" value="<?php echo _("Add");?>" onclick="show_content('txt');" />
							</div>
							<div id="widget_img" class="widget">
								<p ><?php echo _("Image");?></p>
								<small class="description"><?php echo _("Displays a simple images");?></small>
								<input type="button" id="add_w_img" name="add_w_img" value="<?php echo _("Add");?>" onclick="show_content('img');" />
							</div>
							<div style="clear: both;"></div>
							
							<!-- _*_*_*_*_*_*_*_*_*_*_*_*_* Text/HTML section *_*_*_*_*_*_*_*_*_*_*_*_ -->
							<div id="add_widget_txt" class="add_widget" style="display:none;">
		            			<div class="edit_content_div">
		            				<textarea id="widget_content" name="widget_content" cols="30" rows="20" ></textarea>
		            			</div>
		            			<!-- <div class="placeholder_div">
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
		            			</div> -->
		            			<div style="clear:both"></div>
		            			<?php echo _('Background Color')?></td>
								<input type="text" class="text short colors" id="bg_color" name="bg_color" value="#FFFFFF">
								<?php echo _('Text Color')?></td>
								<input type="text" class="text short colors" id="text_color" name="text_color" value="#000000">
		            			<input type="button" id="add_w" name="add_w" value="<?php echo _("Add Widget");?>" onclick="add_widget_new();" />
		            			<input type="button" id="edit_w" name="edit_w" value="<?php echo _("Edit Widget");?>" onclick="" style="display: none;" />
		            		</div>
		            		
		            		<!-- _*_*_*_*_*_*_*_*_*_*_*_*_* Text/HTML section *_*_*_*_*_*_*_*_*_*_*_*_ -->
		            		<div id="add_widget_img" class="add_widget" style="display:none" >
                   				<div id="uploaded_image"></div>
                   				<input type="hidden" id="x" name="x" />
				  				<input type="hidden" id="y" name="y" />
				  				<input type="hidden" id="w" name="w" />
				  				<input type="hidden" id="h" name="h" />
                   				<div>
                   					<?php echo _('Background Color')?></td>
									<input type="text" class="text short colors" id="bg_color_img" name="bg_color_img" value="#FFFFFF">
                   					<a href="javascript:;" class="thickboxed" ><input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a>
                   				</div>
				            </div>
	            		</div>
	            	</div>
	            	
	            	<p class="content_tag"><?php echo _("Content");?></p>
	            	<div class="div_content">
	            		<div class="gridster">
	            			<ul id="layout">
	            				<?php if(isset($newsLetter) && !empty($newsLetter)){ $counter = 1;?>
	            				<?php $content = json_decode($newsLetter['0']['content'],true);?>
	            				<?php if(!empty($content)){?>
	            				<?php foreach ($content as $value){?>
	            				<li id='w_t_<?php echo $counter;?>' class='new' data-row="<?php echo $value['2'];?>" data-col="<?php echo $value['1'];?>" data-sizex="<?php echo $value['3'];?>" data-sizey="<?php echo $value['4'];?>">
	            					<span class='action'>
											<a href='javascript: void(0);' class='edit_me' ><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' width='50px' /></a>
											<a href='javascript: void(0);' class='remove_me' ><img src='<?php echo base_url();?>assets/cp/images/delete-2.png' alt='<?php echo _("remove");?>' /></a>
										</span>
									<div rel='<?php echo $counter;?>' class='widgetContent' style="background-color: <?php echo $value['5'];?>; color: <?php echo $value['6'];?>"><?php echo stripslashes($value['0']);?></div>
	            				</li>
	            				<?php $counter++;?>
	            				<?php }?>
	            				<?php }?>
	            				<?php }?>
						    </ul>
	            		</div>
	            	</div>
            	</form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
