<script>$.noConflict();</script>
<script type="text/javascript"
	src="<?php echo base_url();?>assets/cp/js/jquery-1.9.1.js"></script>
<script type="text/javascript"
	src="<?php echo base_url();?>assets/cp/new_js/jquery.gridster.js"></script>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/cp/new_js/jquery.form.js"
	type="text/javascript"></script>
<script type="text/javascript"
	src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js"></script>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/cp/new_js/jquery.ui.1.10.4.js"
	type="text/javascript"></script>
<link rel="stylesheet" type="text/css"
	href="<?php echo base_url(); ?>assets/cp/new_css/jquery.ui.1.10.4.css">


<link rel="stylesheet"
	href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script
	src="<?php echo base_url();?>assets/js/ui-1.10.2/jquery.ui.core.js"></script>
<script
	src="<?php echo base_url();?>assets/js/ui-1.10.2/jquery.ui.widget.js"></script>
<script
	src="<?php echo base_url();?>assets/js/ui-1.10.2/jquery.ui.mouse.js"></script>
<script
	src="<?php echo base_url();?>assets/js/ui-1.10.2/jquery.ui.sortable.js"></script>
<script
	src="<?php echo base_url();?>assets/js/ui-1.10.2/jquery.ui.resizable.js"></script>


<script type="text/javascript">


<?php if(isset($newsLetter) && !empty($newsLetter)){ 
		$content = json_decode($newsLetter['0']['content'],true);
}


?>
var editing_tmp = 0;
<?php if(isset($newsLetter)){ ?>
	editing_tmp = <?php echo $newsLetter[0]['id'];?>;
<?php }?>


var counter_widget = <?php if(!empty($content)){ echo (count($content)+1); }else{echo '1';} ?>;
var editorCursorPosition = 0;
var image_via_tinymce = 0;

	$(function(){ 
		$('.handler').show();
		$( "#layout1" ).sortable({
			handle: '.handle',
			cursor: "move"
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
	        },
	        setup : function(ed) {
	             // Add a custom button
	             ed.addButton('mybutton', {
	                 title : 'My button',
	                 image : '<?php echo base_url();?>assets/cp/images/tm-image-icon.png',
	                 onclick : function() {
	                  ed.nodeChanged();
	                  image_via_tinymce = 1;
	                  tb_show("Select Image", "<?php echo base_url();?>cp/mail_manager/image_manager_popup?height=160&width=675", "true");
	                 }
	             });
	         }
		});

		$(".colors").miniColors();

	});

// 	function getRandomInt(min, max) {
//         return Math.floor(Math.random() * (max - min + 1)) + min;
//     }

	function show_preview(){
		var tmp_content = new Array();
		var i = 0;

		$('#layout1 p').each(function(){
			$(this).css('text-wrap','break-word');
			});
		$('#layout1 .add_new_widget').each(function(){
			var new_tmp = new Array();
			var row_color = $(this).find('.cols_container').css('background-color');
			$(this).find('.cols_container .cols').each(function(){
				tmp = new Array();
				
				tmp={'width':$(this).find('.width_of').val(),
					'bg_color':$(this).find('.bg_color_of').val(),
					'color':$(this).find('.color_of').val(),
					'content': $(this).find('.text_container').html(),
					'row_color':row_color
				}

				new_tmp.push(tmp);
			});
			tmp_content.push(new_tmp);
		});

		$("#progress").show();
		$.post(
				'<?php echo base_url();?>cp/mail_manager/generate_preview',
				{'info':tmp_content},
				function(response){
					if(response.trim() == 'error'){
						alert("<?php echo _("Sorry!!! Preview can not be generated. Please try again.");?>");
					}else{

						response = '<div style="barder: 5px solid;padding:10px;width:70%;margin:0 auto;">'+response+'</div>';
						var myWindow = window.open("", "_blank", "scrollbars=yes, resizable=yes, width=1000, height=800");
						myWindow.document.write(response);
						$("#progress").hide();
						
						
					}
				}
			);
		
	}

	function add_widget_img_new(){
		var current_div_id = $('#current_editing_div').val();
		$("#uploaded_image div div").remove();
		var uploaded_img = $("#uploaded_image div").html();

		var its_width = $("#uploaded_image div").css('width');
		var parent_width = $("#uploaded_image").css('width');
		var per_width = (parseInt(its_width)/parseInt(parent_width)*100);
		
		$('#'+current_div_id+' .text_container').html(uploaded_img);
		$("#uploaded_image").html('');
		$("#uploaded_image").hide();
		var bg_clr = $('#bg_color_img').val();
		$('#'+current_div_id+' .text_container').css("background-color",bg_clr); 
		$('#'+current_div_id+' .text_container img').css("width",per_width+'%'); 
		$('#'+current_div_id+' .bg_color_of').val(bg_clr); 
		$("#add_widget_img").hide();
		$("#widget_container").hide();

		$('#'+current_div_id).find('.edit_me').show();

		/*var li_content = "";
		var wid = $('.resizable_img').width();
		var hei = $('.resizable_img').height();*/
		//li_content += "<img src='<?php /*echo base_url(); */ ?>assets/images/mail_manager/"+$("#image_name").val()+"' style='max-width: 100%; max-height: 100%;width: "+wid+"px;height: "+hei+"px' />";
		
		/*gridster.add_widget(li_content, Math.ceil(wid/140), Math.ceil(hei/140));
		
		var current_div_id = $('#current_editing_div').val();
		$('#'+current_div_id +' .text_container').html(li_content);
		$("#uploaded_image").html('');


		var bg_clr = $('#bg_color_img').val();
		$('#'+current_div_id+' .text_container').css("background-color",bg_clr); 
		$('#'+current_div_id+' .bg_color_of').val(bg_clr); 
		
		$("#x").val('');
		$("#y").val('');
		$("#w").val('');
		$("#h").val('');
		
		$("#add_widget_img").hide();*/
	}
	
	function add_widget_new(){

		if(tinymce.get('widget_content').getContent() == ''){
			$("#widget_content_parent").addClass("ns_error");
			return false;
		}

		var current_div_id = $('#current_editing_div').val();
		$('#'+current_div_id+' .text_container').html(tinymce.get('widget_content').getContent());

		var bg_clr = $('#bg_color').val();
		var txt_clr = $('#text_color').val();
		$('#'+current_div_id+' .text_container').css("background-color",bg_clr); 
		$('#'+current_div_id+' .text_container').css("color",txt_clr);

		$('#'+current_div_id+' .color_of').val(txt_clr);
		$('#'+current_div_id+' .bg_color_of').val(bg_clr); 

		$('#'+current_div_id).find('.edit_me').show();
		
		tinyMCE.get('widget_content').setContent('');
		$("#add_widget_txt").hide();
		$("#widget_container").hide();
		counter_widget++;
	}

	function edit_widget(widgetId){
		if(tinymce.get('widget_content').getContent() == ''){
			$("#widget_content_parent").addClass("ns_error");
			return false;
		}

		$("#"+widgetId).find(".widgetContent").html(tinymce.get('widget_content').getContent());
		$("#"+widgetId).css('background-color',$("#bg_color").val());
		//$("#"+widgetId).find(".widgetContent").css('background-color',$("#bg_color").val());
		$("#"+widgetId).find(".widgetContent").css('color',$("#text_color").val());
		
		tinyMCE.get('widget_content').setContent('');
		$("#add_widget_txt").hide();
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
		//var data_array = dataToInsert.split(','); 
		
		//alert(dataToInsert.toSource());
		var new_data_array = new Array();

		if(dataToInsert != ''){
			for(var i=0;i<dataToInsert.length;i++){
				new_data_array[i] = '{'+dataToInsert[i]+'}';
				//alert(new_data_array[i]);
			}
		}
		var new_data = new_data_array.join(' ');
			
		if (editorCursorPosition) {
	        tinymce.selectedInstance.selection.moveToBookmark(editorCursorPosition);
	    }
	    insertHTML(editor_id, new_data);
	    editorCursorPosition = 0;
	} 

	function show_content(ids, type){

		var par_id = ids.parentNode.parentNode;
		$('#current_editing_div').val(par_id.getAttribute('id'));
		
		if(type == 'txt'){

			/*
			var current_div_id = $('#current_editing_div').val();
			var pre_text = $('#'+current_div_id +' .text_container').html();; 
			tinyMCE.get('widget_content').setContent(pre_text);
			*/
			
			$("#edit_w").hide();
			$("#add_w").show();

			//$('#current_content_type').val(1);
			
			$('#bg_color').val('#ffffff');
			$('#text_color').val('#000000');
			$('#bg_color').miniColors('value','#ffffff');
			$('#text_color').miniColors('value','#000000');
			$("#add_widget_img").hide();
			$("#add_widget_txt").show();

			
		}else{

			//$('#current_content_type').val(2);
			$('#bg_color_img').val('#ffffff');
			$('#bg_color_img').miniColors('value','#ffffff');
			image_via_tinymce = 0;
			$('#add_image_btn').hide()
			$("#add_widget_img").show();
			$("#add_widget_txt").hide();
		}
	}
	function show_content1(type){

		if(type == 'txt'){
			var current_div_id = $('#current_editing_div').val();
			var pre_text = $('#'+current_div_id +' .text_container').html();; 
			tinyMCE.get('widget_content').setContent(pre_text);
			$("#edit_w").hide();
			$("#add_w").show();
			$("#add_widget_img").hide();
			$("#add_widget_txt").show();
		}else{
			$("#add_widget_img").show();
			$("#add_widget_txt").hide();
		}
	}
	function add_widget_type(type){

		var in_edit = $('#current_editing_row').val();

		if(in_edit != 0){
			alert("<?php echo _("Already a widget in editing queue, first complete or remove that.")?>");
			return false;
		}
		
		$('#current_editing_row').val(counter_widget);
		
		$('#current_widget_type').val(type);
		var t = $('#current_widget_type').val();
		
		var li_content = "";
		li_content += "<div id='w_t_"+counter_widget+"' class='add_new_widget' style=''>";
		li_content += "	<div class='cols_container'>";
		

		if(type == 1){
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:99.5%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='98'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		}else if(type == 2){
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:49.5%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='49'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_2' class='cols' style='width:49.5%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_2' type='hidden' value='49'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		}else if(type == 3){
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:66%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='68'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_2' class='cols' style='width:33%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_2' type='hidden' value='32'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		}else if(type == 4){
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:33%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='32'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_2' class='cols' style='width:66%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_2' type='hidden' value='68'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		}else if(type == 5){
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:32.5%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='32'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_2' class='cols' style='width:33%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_2' type='hidden' value='33'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_3' class='cols' style='width:33%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_3' type='hidden' value='33'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_3' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_3' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		} else if(type == 6){
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:24%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='24'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_2' class='cols' style='width:49.5%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)' ><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_2' type='hidden' value='49'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_3' class='cols' style='width:25%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_3' type='hidden' value='25'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_3' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_3' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		}else {
			li_content += "		<div id='cols_"+counter_widget+"_1' class='cols' style='width:24%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_1' type='hidden' value='24'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_1' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_2' class='cols' style='width:25%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_2' type='hidden' value='25'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_2' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_3' class='cols' style='width:24%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_3' type='hidden' value='24'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_3' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_3' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div id='cols_"+counter_widget+"_4' class='cols' style='width:25%'>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float:right'/></a>";
			li_content += "			<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float:right'/></a>";
			li_content += "			<input class='width_of' id='width_of_"+counter_widget+"_4' type='hidden' value='24'>";
			li_content += "			<input class='color_of' id='color_of_"+counter_widget+"_4' type='hidden' value=''>";
			li_content += "			<input class='bg_color_of' id='bg_color_of_"+counter_widget+"_4' type='hidden' value=''>";
			li_content += " 		<div class='text_container'><?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a></div>";
			li_content += "		</div>";
			li_content += "		<div class='clear'></div>";
		}


		
		li_content += "	</div>";
		li_content += " <div class='handler'>";
		li_content += "		<div class='handle'></div>";
		li_content += " 	<div class='remove_me' onclick='remove_this_div(this)'>";
		li_content += " 	</div>";
		li_content += " 	<div class='edit_div' onclick='edit_row("+counter_widget+")'>";
		li_content += " 	</div>";
		li_content += "	</div>";
		li_content += "	<div class='clear'></div>";
		li_content += "</div>";
		$('#bg_color_div').val('#ffffff');
		$( "#layout" ).append(li_content);
		$('#cmplt_btns').show();
		counter_widget++;
		
	}
 function edit_widget_text(ids){
	var par_id = ids.parentNode;
	$('#current_editing_div').val(par_id.getAttribute('id'));
	var current_div_id = $('#current_editing_div').val();

	//var its_img = $('#'+current_div_id +' .text_container img:first').attr("src");
	var its_img = $('#'+current_div_id +' .text_container').children().first().attr("src");
	
	//var check = $('#current_content_type').val();
	if(its_img){

		//var img_content = $('#'+current_div_id +' .text_container').html();
		var img_content_src = $('#'+current_div_id +' .text_container img').attr('src');
		var img_bg_color = $('#'+current_div_id +' .text_container').css('background-color');
		img_bg_color = rgb2hex(img_bg_color);

		$('#bg_color_img').val(img_bg_color);
		$('#bg_color_img').miniColors('value', img_bg_color);
		
		var img_content = "<img src='"+img_content_src+"' height='90%' width='90%'>";
		$("#uploaded_image").html(img_content);
		$('#uploaded_image img').resizable({
			containment: "#uploaded_image"
			});
		$('#add_image_btn').show();
		$("#uploaded_image").show();
		$("#add_widget_img").show();
		$("#add_widget_txt").hide();
		
	}else{
		
		var pre_text = $('#'+current_div_id +' .text_container').html();
		tinyMCE.get('widget_content').setContent(pre_text);
		var text1_color = $('#'+current_div_id +' .color_of').val();
		var bck_color = $('#'+current_div_id +' .bg_color_of').val();
		$('#bg_color').val(bck_color);
		$('#text_color').val(text1_color);
		
		$('#text_color').miniColors('value', text1_color);
		$('#bg_color').miniColors('value', bck_color);
		
		$("#add_widget_img").hide();
		$("#add_widget_txt").show();
	}
		
	//$('#widget_container').show();

 }

 function remove_this_div(obj){
	var obj1 = obj.parentNode.parentNode;
	obj1.remove();
 }

 function save_template(){
	var title = $('#title').val();
	if(title == ''){
		alert("<?php echo _("Please input Title of template");?>");
	}else{
		var tmp_content = new Array();

		$('#layout1 p').each(function(){
			$(this).css('text-wrap','break-word');
			});
		
		$('#layout1 .add_new_widget').each(function(){
			var new_tmp = new Array();
			var row_color = $(this).find('.cols_container').css('background-color');
			$(this).find('.cols_container .cols').each(function(){
				tmp = new Array();
				
				tmp={'width':$(this).find('.width_of').val(),
					'bg_color':$(this).find('.bg_color_of').val(),
					'color':$(this).find('.color_of').val(),
					'content': $(this).find('.text_container').html(),
					'row_color':row_color
				}

				new_tmp.push(tmp);
			});
			tmp_content.push(new_tmp);
		});

		
		$.post(
				'<?php echo base_url();?>cp/mail_manager/save_templates',
				{'title':title,'info':tmp_content,'editng':editing_tmp},
				function(response){
					//alert(response);
					window.location.href = "<?php echo base_url();?>cp/mail_manager/templates";
				}
			);
	
	}
 }

 function complete_this_widget(){
		var row_bg_color = $('#bg_color_div').val(); 

		$('#layout div div').first('div').css('background-color',row_bg_color);
		
		$('#layout1').append($('#layout').html());
		$('#layout').html('');
		$('#current_editing_row').val(0);
		$('#cmplt_btns').hide();
		$('#layout1').find('.edit_me').hide();
		$('#layout1').find('.handler').show();
		$("#add_widget_img").hide();
		$("#add_widget_txt").hide();
 }
 function remove_this_widget(){
		
		$('#layout').html('');
		$('#current_editing_row').val(0);
		$('#cmplt_btns').hide();
		$("#add_widget_img").hide();
		$("#add_widget_txt").hide();
}
 function edit_row(row_no){
	 var in_edit = $('#current_editing_row').val();

		if(in_edit != 0){
			alert("<?php echo _("Already a widget in editing queue, first complete or remove that.")?>");
			return false;
		}
	var contain_html = $("#layout1").find('#w_t_'+row_no).html();
	var bg_color_row = $("#layout1").find('#w_t_'+row_no).find('.cols_container').css('background-color');
	bg_color_row = rgb2hex(bg_color_row);
	$('#bg_color_div').val(bg_color_row);
	$('#bg_color_div').miniColors('value', bg_color_row);
	new_contain_html = "";
	new_contain_html += "<div id='w_t_"+row_no+"' class='add_new_widget'>"+contain_html+"</div>";
	$("#layout").html(new_contain_html);
	$('#current_editing_row').val(row_no);
	$('#layout').find('.handler').hide();
	$('#layout').find('.edit_me').show();
	$("#layout1").find('#w_t_'+row_no).remove();
	$('#cmplt_btns').show();
 }
	 function doInsertImage(img_name) {
		 if(image_via_tinymce){
			 var imageToInsert1 = "<img src='<?php echo base_url();?>/assets/upload_center/images/"+img_name+"' style='max-width:100%'>";
			//tinyMCE.get('widget_content').setContent(imageToInsert1);
			tinyMCE.execInstanceCommand("widget_content","mceInsertContent",false,imageToInsert1);
		 }else{
			var imageToInsert = "<img src='<?php echo base_url();?>/assets/upload_center/images/"+img_name+"' style='max-width:100%'>";
			$("#uploaded_image").html(imageToInsert);
			$("#uploaded_image").show();
			$('#uploaded_image img').resizable({
				containment: "#uploaded_image"
				});
			$("#add_image_btn").show();
	 	}
		
	} 

  function remove_content_type(obj1){
		//$('#current_content_type').val(0);
		var first_html = "<?php echo _("Add "); ?><a href=\"javascript:;\" onclick=\"show_content(this,'txt')\">Text</a> / <a href=\"javascript:;\" onclick=\"show_content(this,'img')\">Image</a>";

		var current_div_id = $(obj1).parent().attr('id');
		$('#'+current_div_id +' .text_container').html(first_html); 
		$('#'+current_div_id+' .text_container').css("background-color",'white'); 
		$('#'+current_div_id+' .text_container').css("color",'black');
		$("#add_widget_img").hide();
		$("#add_widget_txt").hide();
		$('#'+current_div_id).find('.edit_me').hide();
		
	}
  var hexDigits = new Array
  ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 
	
	//Function to convert hex format to a rgb color
	function rgb2hex(rgb) {
		rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
	
	function hex(x) {
		return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
	}
</script>
<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js"></script>
<script type="text/javascript">
var jcrop_api,
boundx,
boundy,xsize,ysize,$preview,$pcnt,$pimg;

$(document).ready(function(){

	$(".thickboxed").click(function(){
		tb_show("Select Image", "<?php echo base_url();?>cp/mail_manager/image_manager_popup?height=200&width=675", "true");
		//tb_show("<?php echo _("Upload Image");?>", "<?php echo base_url(); ?>cp/mail_manager/ajax_img_upload?height=80&width=600", "true");
	});
	
  });

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
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
		$("#uploaded_image").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
		$.ajax({
			url : base_url+'cp/mail_manager/crop_image',
			data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
			type: 'POST',
			success: function(response){
				//$("#uploaded_image").toggle("slow");
				$("#uploaded_image").html(response);
				$("#uploaded_image").focus();
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

<link rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css" />
<style type="text/css">
.preview_title {
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
	border: 1px rgba(0, 0, 0, .4) solid;
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

#TB_window {
	z-index: 999 !important;
}

#crop_button {
	background-color: #007a96;
	padding: 12px 26px;
	color: #fff;
	font-size: 14px;
	border-radius: 2px;
	cursor: pointer;
	display: inline-block;
	line-height: 1;
	border: none;
}

.crop_div {
	margin-top: 30px;
	text-align: center;
}

#GroupsTable input.medium,#GroupsPersonTable input.medium,#WGroupsTable input.medium
	{
	width: 100%;
}

.box {
	background-color: #fff;
}

.div_content {
	background-color: #F4F4F4;
	border: 1px solid #CCCCCC;
	padding: 30px;
}

.div_content ul li {
	background-color: #FFFFFF;
}

.template_wrap {
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
	position: relative;
}

p.content_tag {
	font-size: 12px;
	font-weight: bold;
	padding: 10px 0 4px;
}

.gridster .preview-holder {
	border: none !important;
	background: red !important;
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
/*
.gridster li p.drag {
	display: block;
	line-height: normal;
	cursor: move;
	text-align: center;
	height: 25px;
}

.gridster li {
	cursor: move;
}

.gridster li span.action {
	position: absolute;
	right: 0;
	top: 0;
}

.gridster .remove_me img,.gridster .edit_me img {
	vertical-align: middle;
	width: 15px;
}
*/
.edit_content_div {
	float: left;
	margin-bottom: 20px;
}

.placeholder_div {
	float: right;
	text-align: center;
	width: 18%;
}

#edit_content {
	width: 730px;
}

#edit_content_fields option {
	padding-left: 10px;
}

.add_widget {
	
}

.show_widget .widget {
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

.show_widget .widget input:hover {
	background: none repeat scroll 0 0 #cccccc;
	color: #2D6CB1;
}

.show_widget .widget p {
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

#preview {
	background-color: #FFFFFF;
	margin: 50px;
	padding: 10px;
	display: none;
}

#preview p {
	margin: 0;
}

#preview table td {
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

#add_widget_img .ui-icon,#add_widget_img .ui-widget-content .ui-icon {
	background-color: #FFFFFF !important;
}

#add_widget_img .ui-icon-gripsmall-diagonal-se {
	background-position: -80px -224px !important;
}

.widget_name {
	margin: 62px auto;
	text-align: center;
	width: auto;
}

#menu ul.options-widgets li {
	background-image: none;
	border-left: 1px solid #888888;
	border-right: 1px solid #707070;
	color: #2D6CB1;
	display: block;
	float: left;
	font-size: 12px;
	height: 38px;
	line-height: 14px;
	list-style: none outside none;
	margin-top: 5px;
	padding-left: 58px;
	padding-top: 10px;
	position: relative;
	width: 68px;
}

#menu ul li:hover {
	background-color: #44bcf2;
	background-position: 0 -48px;
	color: white;
}

#menu ul li img {
	left: 5px;
	position: absolute;
	top: 10px;
}

#menu ul.options-widgets li span {
	color: #aaaaaa;
	font-size: 10px;
	font-weight: normal;
	line-height: 11px;
}

#menu ul li a {
	text-decoration: none;
}

.add_new_widget {
	background-color: #666;
	border-bottom: 1px solid #444;
	border-top: 1px solid #777;
	padding: 5px;
	width: 100%;
}

.cols_container {
	float: left;
	display: block;
	width: 97%;
}

.cols {
	float: left;
	min-height: 30px;
	border: 2px solid #333;
}

.text_container {
	background: none repeat scroll 0 0 #eee;
	padding: 20px;
	color: balck;
	background-color: white;
}

.handler {
	width: 2%;
	float: left;
	display: none;
	position: relative;
}

.handle {
	background-image:
		url("<?php echo base_url();?>assets/images/mail_manager/icon/reorder.png");
	height: 12px;
	left: 5px;
	position: absolute;
	top: 5px;
	width: 12px;
	cursor: move;
}

.remove_me {
	background-image:
		url("<?php echo base_url();?>assets/images/mail_manager/icon/delete.png");
	background-position: 0 -12px;
	cursor: pointer;
	height: 13px;
	left: 5px;
	position: absolute;
	top: 28px;
	width: 13px;
}

.edit_me {
	display: none;
}

.edit_div {
	background-image:
		url("<?php echo base_url();?>assets/images/mail_manager/icon/options.png");
	background-position: 0 -12px;
	cursor: pointer;
	height: 12px;
	left: 4px;
	position: absolute;
	top: 47px;
	width: 12px;
}

* {
	word-wrap: break-word;
}
</style>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
		<h2><?php echo _('Mail Manager')?></h2>
		<span class="breadcrumb"><a
			href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo('Customer')?></span>
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
				<h3><?php echo _('Templates')?></h3>
				<div class="table">
					<div class="template_wrap">

						<div class="div_head">
							<input type="button" name="submit" id="submit"
								value="<?php echo _("Save");?>" onclick="save_template()" />
						</div>
						<div class="div_title">
							<lable class="span_t">
	            			<?php echo _("Title");?> : <span class="red">*</span> </lable>
							<lable class="span_ta"> <input type="text" name="title"
								id="title"
								value="<?php if(isset($newsLetter) && !empty($newsLetter)){ echo $newsLetter['0']['name']; }?>"
								class="text" /> <input type="hidden" name="newsletter_id"
								id="newsletter_id"
								value="<?php if(isset($newsLetter) && !empty($newsLetter)){ echo $newsLetter['0']['id']; }?>" />
							</lable>
						</div>
						<div class="div_html">
							<a href="javascript: void(0);"
								onClick="javascript: show_preview();"><?php echo _("HTML Version");?></a>
							<div id="progress"><?php echo _("Generating Preview");?> <img
									src="<?php echo base_url();?>assets/cp/images/20122139137.GIF"
									alt="..." />
							</div>
							<div id="preview" style="position: relative;"></div>
						</div>

						<div class="div_html">
	            		<?php echo _("Widgets Type");?>
	            			<div id="menu">
								<ul
									class="options-layout main-menu-selected options-widgets topmenuitem">
									<li id="layout1x100" class="layoutwidget ui-draggable"
										style="width: 63px"><a href="javascript:;"
										onclick="add_widget_type(1);"> <img
											src="<?php echo base_url();?>assets/images/col/layout1x100.png">
											1 column <br> <span>100%</span>
									</a></li>
									<li id="layout2x50" class="layoutwidget ui-draggable"><a
										href="javascript:;" onclick="add_widget_type(2);"> <img
											src="<?php echo base_url();?>assets/images/col/layout2x50.png">
											2 columns <br> <span>2 x 50%</span>
									</a></li>
									<li id="layout67x33" class="layoutwidget ui-draggable"><a
										href="javascript:;" onclick="add_widget_type(3);"> <img
											src="<?php echo base_url();?>assets/images/col/layout67x33.png">
											2 columns <br> <span>67% + 33%</span>
									</a></li>
									<li id="layout33x67" class="layoutwidget ui-draggable"><a
										href="javascript:;" onclick="add_widget_type(4);"> <img
											src="<?php echo base_url();?>assets/images/col/layout33x67.png">
											2 columns <br> <span>33% + 67%</span></li>
									</a>
									<li id="layout3x33" class="layoutwidget ui-draggable"><a
										href="javascript:;" onclick="add_widget_type(5);"> <img
											src="<?php echo base_url();?>assets/images/col/layout3x33.png">
											3 columns <br> <span>3 x 33%</span>
									</a></li>
									<li id="layout25x50x25" class="layoutwidget ui-draggable"
										style="width: 72px"><a href="javascript:;"
										onclick="add_widget_type(6);"> <img
											src="<?php echo base_url();?>assets/images/col/layout25x50x25.png">
											3 columns <br> <span>25%+50%+25%</span>
									</a></li>
									<li id="layout4x25" class="layoutwidget ui-draggable"><a
										href="javascript:;" onclick="add_widget_type(7);"> <img
											src="<?php echo base_url();?>assets/images/col/layout4x25.png">
											4 columns <br> <span>4 x 25%</span>
									</a></li>
								</ul>
								<div class="clear"></div>

							</div>

						</div>


						<p class="content_tag"><?php echo _("Edit widget");?></p>
						<div class="div_content">
							<div class="show_widget">

								<div id='widget_container' style="display: none">
											<?php echo _("Content Type");?>
											<div class="clear"></div>
									<div id="widget_txt" class="widget">
										<p><?php echo _("Text");?></p>
										<small class="description"><?php echo _("Arbitrary text or HTML");?></small>
										<input type="button" id="add_w_txt" name="add_w_txt"
											value="<?php echo _("Add");?>"
											onclick="show_content1('txt');" />
									</div>
									<div id="widget_img" class="widget">
										<p><?php echo _("Image");?></p>
										<small class="description"><?php echo _("Displays a simple images");?></small>
										<input type="button" id="add_w_img" name="add_w_img"
											value="<?php echo _("Add");?>"
											onclick="show_content1('img');" />
									</div>
									<div style="clear: both;"></div>
								</div>
								<!-- _*_*_*_*_*_*_*_*_*_*_*_*_* Text/HTML section *_*_*_*_*_*_*_*_*_*_*_*_ -->
								<div id="add_widget_txt" class="add_widget"
									style="display: none;">
									<div class="edit_content_div">
										<textarea id="widget_content" name="widget_content" cols="20"
											rows="20"></textarea>
									</div>
									<div class="placeholder_div">
										<div class="box">
											<h3><?php echo _('INSERT CONTENT FIELDS'); ?></h3>
											<div class="inside" style="margin-top: 50px">
												<select multiple="multiple"
													style="width: 115px; height: 165px; border: 1px solid #96d945"
															type="select" id="widget_content_fields"
															name="widget_content_fields">
															<option value="company_name"><?php echo _('Company Name'); ?></option>
															<option value="first_name"><?php echo _('First Name'); ?></option>
															<option value="last_name"><?php echo _('Last Name'); ?></option>
															<option value="email"><?php echo _('Email'); ?></option>
															<option value="phone"><?php echo _('Phone Number'); ?></option>
															<option value="website"><?php echo _('Website'); ?></option>
															<option value="address"><?php echo _('Address'); ?></option>
															<option value="zipcode"><?php echo _('Zip Code'); ?></option>
															<option value="city"><?php echo _('City'); ?></option>
															<option value="unsubscribe"><?php echo _('Unsubscribe'); ?></option>
														</select> <br> <input type="button" value="&lt;&lt; <?php echo _('INSERT'); ?>" class="btnGreyBack" id="btn_save" name="btn_save" onclick="doInsertTemplate('widget_content');">
													</div>
												</div>
											</div>
											<div style="clear: both"></div>
					            			<?php echo _('Background Color')?> 
					            			<input type="text" class="text short colors" id="bg_color" name="bg_color" value="#FFFFFF">
											<?php echo _('Text Color')?>
											 <input type="text" class="text short colors" id="text_color" name="text_color" value="#000000">
											 <input type="button" id="add_w" name="add_w" value="<?php echo _("Add Widget");?>" onclick="add_widget_new();" />
											 <input type="button" id="edit_w" name="edit_w"value="<?php echo _("Edit Widget");?>" onclick="" style="display: none;" />
										</div>
	
										<!-- _*_*_*_*_*_*_*_*_*_*_*_*_* Text/HTML section *_*_*_*_*_*_*_*_*_*_*_*_ -->
										<div id="add_widget_img" class="add_widget" style="display: none">
											<div id="uploaded_image" style="width:300px;height:300px;border:2px solid blue;display: none"></div>
											<div id="add_image_btn" style="display: none">
												<input type="button" id="add_w_img" name="add_w_img" value="<?php echo _("Add widget");?>" onclick="add_widget_img_new();" />
											</div>
											<input type="hidden" id="x" name="x" /> 
											<input type="hidden" id="y" name="y" />
											 <input type="hidden" id="w" name="w" />
											 <input type="hidden" id="h" name="h" /> 
											 <div>
		                   						 <?php echo _('Background Color')?>
		                   						 <input type="text" class="text short colors" id="bg_color_img" name="bg_color_img" value="#FFFFFF"> <a href="javascript:;" class="thickboxed">
		                   						 <input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a>
											</div>
										</div>
									</div>
									<div id="layout" style="margin-top:50">

						    		</div>
						    		<div id="cmplt_btns" hidden="hidden">
						    			<p>
						    				<?php echo _('Row Background Color')?>
						    				<input type="text" class="text short colors" id="bg_color_div" name="bg_color_div" value="#FFFFFF">
						    			</p>
						    			<p style="text-decoration: none">
							    			<input type="button" onclick="complete_this_widget()" id="done_button" value="Done">
							    			<input type="button" onclick="remove_this_widget()" id="rem_button" value="Remove">
							    		</p>
						    		</div>
							</div>
							
							<p class="content_tag"><?php echo _("Content");?></p>
							<div class="div_content">
									<div id="layout1">
			            				<?php if(isset($newsLetter) && !empty($newsLetter)){ $counter = 1; ?>
				            			<?php $content = json_decode($newsLetter['0']['content'],true);?>
					            			<?php if(!empty($content)){?>
						            			<?php foreach ($content as $value){ 
						            				$cols_counter = 1;
						            				$row_color = "white";
						            				if(isset($value[0]['row_color'])){
														$row_color = $value[0]['row_color'];
													} ?>
						            				<div id='w_t_<?php echo $counter;?>' class='add_new_widget' style=''>
														<div class='cols_container' style='background-color: <?php echo $row_color;?>'>
							            					<?php foreach ($value as $inner_data){ ?>
							            						<div id='cols_<?php echo $counter;?>_<?php echo $cols_counter;?>' class='cols' style='width:<?php echo ($inner_data['width']-1);?>%;'>
																	<a href='javascript: void(0);' class='edit_me'onclick='edit_widget_text(this)'><img src='<?php echo base_url();?>assets/cp/images/edit.gif' alt='<?php echo _("Edit");?>' style='float: right' /></a>
																	<a href='javascript: void(0);' class='edit_me' onclick='remove_content_type(this)'><img src='<?php echo base_url();?>assets/cp/images/delete.gif' alt='<?php echo _("Delete");?>' style='float: right' /></a>
																	<input class='width_of' id='width_of_<?php echo $counter;?>_<?php echo $cols_counter;?>' type='hidden' value='<?php echo $inner_data['width'];?>'> 
																	<input class='color_of' id='color_of_<?php echo $counter;?>_<?php echo $cols_counter;?>' type='hidden' value='<?php echo $inner_data['color'];?>'> 
																	<input class='bg_color_of' id='bg_color_of_<?php echo $counter;?>_<?php echo $cols_counter;?>' type='hidden' value='<?php echo $inner_data['bg_color'];?>'>
																	<div class='text_container' style='background-color:<?php echo $inner_data['bg_color'];?>;color:<?php echo $inner_data['color'];?>'><?php echo $inner_data['content'];?></div>
																</div>
																<?php $cols_counter++; ?>
							            					<?php }?>
							            					<div class='clear'></div>
														</div>
														<div class='handler'>
															<div class='handle'></div>
															<div class='remove_me' onclick='remove_this_div(this)'></div>
															<div class='edit_div' onclick='edit_row(<?php echo $counter;?>)'></div>
														</div>
														<div class='clear'></div>
													</div>
						           				<?php $counter++;?>
					            			<?php }?>
				            			<?php }?>
			            			<?php }?>
						    	</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="current_content_type" name="current_content_type" value="0"/>
	<input type="hidden" id="current_widget_type" name="current_widget_type" value=""/>
	<input type="hidden" id="current_editing_div" name="current_editing_div" value="0"/>
	<input type="hidden" id="current_editing_row" name="current_editing_row" value="0"/>
	<!-- /content -->
