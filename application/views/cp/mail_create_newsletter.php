<script>$.noConflict();</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/jquery-1.9.1.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.gridster.js" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.form.js" type="text/javascript"></script>	
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.ui.1.10.4.js" type="text/javascript"></script>	
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/cp/new_css/jquery.ui.1.10.4.css"></script>
<!-- <link rel="stylesheet" href="<?php echo base_url().'assets/cp/js/thickbox/css/thickbox.css'?>" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url().'assets/cp/js/thickbox/javascript/thickbox.js'?>"></script> -->


<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css"/>

<script type="text/javascript">
	var editing_nws = 0;
	
	<?php if(isset($newsLetter) && !empty($newsLetter) && $newsLetter['0']['company_id']){ ?>
		editing_nws = <?php echo $newsLetter[0]['id'];?>;
		
	<?php }?>
	
	
	$(document).ready(function(){
		make_editable();
	}); 


	function load_template(tpl_id){	
		$.post(
				'<?php echo base_url();?>cp/mail_manager/ajax_temp_preview/'+tpl_id,
				{'info':'nothing'},
				function(response){
					$("#layout").html(response);
					make_editable();
					
				}
			);

	}

	function make_editable(){

		var counter = 1;
		$('#layout table tr td table tr td').click(function(event){
			//alert($(this).html());
			$('#layout table tr td table tr td').css("cursor","initial");
		   	$('#layout table tr td table tr td').attr('contenteditable','false');
		   	$('#layout table tr td table tr td').css('border','0');
		    $('#layout table tr td table tr td').css('border-radius', '0');
		   	$(this).attr('contenteditable','true');
		   	$(this).css('border-radius', '10px 10px 10px 10px');
		   	$(this).css('border', '4px ridge #2D6CB1');
		   	$("#make_editable").attr('contenteditable','true');
		   	event.stopPropagation();
		});
		$('#layout table tr td table tr td').mouseenter(function(event){
			//alert($(this).html());
		  	$(this).css("cursor","url('<?php echo base_url();?>assets/images/pcl.png'),auto");
		  	event.stopPropagation();
		});
		
		$('body').click(function() {
			 $('#layout table tr td table tr td').attr('contenteditable','false');
			 $('#layout table tr td table tr td').css('border','0');
			 $('#layout table tr td table tr td').css('border-radius', '0');
		});
		$('#layout table tr td table tr td').mouseenter(function(event){
			var is_img = $(this).find('img').first().attr("src");
			if(is_img){
				$(this).append("<div class='edit_img' style='width:10%;height:80%;background:#2D6CB1;float:right'><a href='javascript:;' onclick='edit_img(this);' style='color:white'>Edit</a></div>");
			}
		  	event.stopPropagation();
		});
		$('#layout table tr td table tr td').mouseleave(function(event){
			$('#layout .edit_img').remove();
		  	event.stopPropagation();
		});

		
		$('#layout').click(function(event){
			    event.stopPropagation();
		});
		
		$('#layout').ready(function(event){
			var div_counter = 1;
		    $(this).find('#layout table tr td table tr td').each(function(){
					$(this).attr("id", "div_"+div_counter);
					div_counter++;
			    })
		});
/*
		$('#layout').ready(function(event){
		    $(this).find('td').each(function(){
//			    var width_arr = new Array();
		    	$(this).find('div').each(function(){
		    		var w = $(this).css('height','100%');
//		    		width_arr.push(parseInt(w));
			    });
// 			    var max_w = Math.max.apply(Math, width_arr);
// 			    $(this).find('div').each(function(){
// 		    		$(this).css('height',max_w);
		    		
// 			    });
			});
		});
*/
		

	}
	
	function show_preview(){
		$("#progress").show();
/*
		$('#layout').ready(function(event){
		    $(this).find('td').each(function(){
		    	$(this).find('div').each(function(){
		    		var w = $(this).css('height','100%');
			    });
			});
		});
*/		
		$('#layout table tr td table tr td').css("cursor","initial");
		$('#layout table tr td table tr td').attr('contenteditable','false');
	   	$('#layout table tr td table tr td').css('border','0');
	   	$('#layout table tr td table tr td').css('border-radius', '0');
	   	
		var preview = $("#layout").html();
		response = '<div style="barder: 5px solid;padding:10px;width:80%;margin:0 auto;">'+preview+'</div>';
		var myWindow = window.open("", "_blank", "scrollbars=yes, resizable=yes, width=1000, height=800");
		myWindow.document.write(response);
		$("#progress").hide();
		
	} 

	function edit_img(div_id){
		var d_id = $(div_id).parent().parent().attr('id');
		$('#current_editing_div').val(d_id);
		tb_show("Select Image", "<?php echo base_url();?>cp/mail_manager/image_manager_popup?height=200&width=675", "true");
	}

	function doInsertImage(img_name) {
		var image_div_id = $('#current_editing_div').val();
		var imageToInsert = "<img src='<?php echo base_url();?>/assets/upload_center/images/"+img_name+"' style='max-width:86%'>";
		$("#"+image_div_id).html(imageToInsert);
		
		
	} 
	
	function save_newsletter(){
		var title = $('#title').val();
		if(title == ''){
			alert("<?php echo _("Please input Title of News Letter");?>");
			return false;
		}

		var from = $('#from').val();
		if(from == ''){
			alert("<?php echo _("Please input Name of sender in From field");?");
			return false;
		}

		var template_used = $('#select_template').val();
		var attachment = $('#attachment').val();
/*		
		$('#layout').ready(function(event){
		    $(this).find('td').each(function(){
		    	$(this).find('div').each(function(){
		    		var w = $(this).css('height','100%');
			    });
			});
		});
*/		
		$('#layout table tr td table tr td').css("cursor","initial");
		$('#layout table tr td table tr td').attr('contenteditable','false');
	   	$('#layout table tr td table tr td').css('border','0');
	   	$('#layout table tr td table tr td').css('border-radius', '0');
	   	
		var all_preview = $("#layout").html();
		//var all_content = '<div style="barder: 5px solid;padding:10px;width:70%;margin:0 auto;">'+all_preview+'</div>';
		
		$.post(
				'<?php echo base_url();?>cp/mail_manager/save_newsletter',
				{'title':title,'info':all_preview,'from':from,'template':template_used,'attachment':attachment,'editing':editing_nws},
				function(response){
					if(!response[0].error){
						window.location.href = "<?php echo base_url();?>cp/mail_manager/newsLetters";
					}
				}
			);

	}
</script>


<style type="text/css">

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
		width: 23%;
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
	/*Custom css*/
	#TB_ajaxContent{
		overflow: hidden;
		background-color: #2E3134;
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
          <h3><?php echo _('News Letter')?></h3>
          <div class="table">
            <div class="template_wrap">
            	<form>
	            	<div class="div_head">
	            		<input type="button" name="submit" id="submit" onclick="save_newsletter();" value="<?php echo _("Save");?>" />
	            	</div>
	            	<div class="div_title">
	            		<lable class="span_t">
	            			<?php echo _("Title");?> : <span class="red">*</span>
	            		</lable>
	            		<lable class="span_ta">
	            			<input type="text" name="title" id="title" value="<?php if(isset($newsLetter) && !empty($newsLetter) && $newsLetter['0']['company_id']){ echo $newsLetter['0']['name']; }?>" class="text" />
	            			<input type="hidden" name="newsletter_id" id="newsletter_id" value="<?php if(isset($newsLetter) && !empty($newsLetter) && $newsLetter['0']['company_id']){ echo $newsLetter['0']['id']; }?>"/>
	            		</lable>
	            	</div>
	            	
	            	<div class="div_title">
	            		<lable class="span_t">
	            			<?php echo _("From");?> : <span class="red">*</span>
	            		</lable>
	            		<lable class="span_ta">
	            			<input type="text" name="from" id="from" value="<?php if(isset($newsLetter) && !empty($newsLetter) && $newsLetter['0']['company_id']){ echo $newsLetter['0']['from']; }?>" class="text" />
	            		</lable>
	            	</div>
	            	
	            	<div class="div_title">
	            		<lable class="span_t">
	            			<?php echo _("Use templates");?> : <span class="red">*</span>
	            		</lable>
	            		<lable class="span_ta"><?php //echo count($defaultTemplates);die; ?>
	            			<select id="select_template" name="select_template" class="text" onChange="javascript: load_template(this.value);" >
	            				<option value="0">--<?php echo _('Select template');?>--</option>
	            				<?php if(!empty($defaultTemplates)){?>
	            				<optgroup label="<?php echo _("Default");?>">
	            					<?php foreach ($defaultTemplates as $defaultTemplate):?>
									<option value="<?php echo $defaultTemplate['id'];?>" <?php if(isset($newsLetter) && $newsLetter['0']['template_id'] == $defaultTemplate['id']){?>selected="selected"<?php }?>><?php echo $defaultTemplate['name'];?></option>
									<?php endforeach; ?>
								</optgroup>
								<?php }?>
								<?php if(!empty($templates)){?>
	            				<optgroup label="<?php echo _("My");?>">
	            					<?php foreach ($templates as $template):?>
									<option value="<?php echo $template['id'];?>" <?php if(isset($newsLetter) && $newsLetter['0']['template_id'] == $template['id']){?>selected="selected"<?php }?>><?php echo $template['name'];?></option>
									<?php endforeach; ?>
								</optgroup>
								<?php }?>
	            			</select>
	            		</lable>
	            	</div>
	            	
	            	<div class="div_title">
	            		<?php $doc_name = isset($newsLetter[0]['attachment'])?$newsLetter[0]['attachment']:null; ?>
	            		<lable class="span_t">
	            			<?php echo _("Attachment");?> : 
	            		</lable>
	            		<!-- <lable class="att_preview">
	            		<?php if($doc_name){ ?>
	            			<a href="<?php echo base_url(); ?>assets/upload_center/docs/<?php echo $doc_name; ?>" target="_blank"><?php echo $doc_name; ?></a> 
	            			<a onclick="remove_attachment();" href="javascript:void(0);"><img src="<?php echo base_url(); ?>assets/cp/images/delete-2.png" style="vertical-align:middle;"></a>
	            		<?php } ?>
	            		</lable>  -->
	            		<lable class="span_ta">
	            			<span id="attach_span">
							<select name="attachment" id="attachment" class="text">
								<option value="0" selected="selected"><?php echo _('No Attachment'); ?></option>
			            		<?php if(!empty($docs)){ ?>
			            			<?php foreach($docs as $doc){ ?>
			            					<option value="<?php echo $doc['id'];?>" <?php if(isset($newsLetter) && $newsLetter['0']['attachment'] == $doc['id']){?>selected="selected"<?php }?>><?php echo $doc['doc_name'];?></option>
			            			<?php } ?>
			            		<?php } ?>
			            	</select>
							</span>
	            		</lable>
	            	</div>
	            	
	            	<div class="div_html">
	            		<a href="javascript: void(0);" onClick="javascript: show_preview();"><?php echo _("HTML Version");?></a>
	            		<div id="progress"><?php echo _("Generating Preview");?> <img src="<?php echo base_url();?>assets/cp/images/20122139137.GIF" alt="..."/></div>
	            		<div id="preview" style="position:relative;"></div>
	            	</div>
	            	
	            	<div id="test"></div>
	            	

	            	<p class="content_tag"><?php echo _("Content");?></p>
	            	<div class="div_content">
	            		<div class="gridster">
	            			<div id="layout">
		            			<?php if(isset($newsLetter) && !empty($newsLetter)){ 
		            			 	echo $newsLetter[0]['content'];
								 }?>
	            				
						    </div>
						    <input type="hidden" id="make_editable">
						    <input type="hidden" id="current_editing_div" value="">
	            		</div>
	            	</div>
            	</form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
