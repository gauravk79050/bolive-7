<html>
<head>
</head>
<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.form.js"></script>
<script type="text/javascript" >

function do_upload(obj){

   	jQuery("#preview").html('');
    jQuery("#preview").html('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="Uploading...."/>');
	jQuery("#imageform").ajaxSubmit({

		url: '<?php echo base_url()?>mcp/mail_manager/ajax_file_upload',
		type:'post',
		
		success: function(response){
			jQuery("#attach_span").html(response);
			if(!jQuery('#attach_span span').length){
			//if(response != '<?php echo _('Invalid file format..'); ?>'){
				jQuery(".mail_attachment").hide();
			}
			
		    tb_remove();
		},
		 error: function(responseText){
		        alert(responseText.status+'  ::  '+responseText.statusText);
		    }
	});
    
}
</script>
<style>
body {
	font-family: arial;
}
.preview {
	width: 200px;
	border: solid 1px #dedede;
	padding: 10px;
}
#preview {
	color: #cc0000;
	font-size: 12px
}
#imageform {
	margin: 0 auto;
    width: 300px;
}
.file_input_textbox {
	height: 25px;
	width: 110px;
	float: left;
	background:#2E3134;
	border:none;
}
.file_input_div {
	position: relative;
	width: 80px;
	height: 26px;
	overflow: hidden;
}
.file_input_button {
	background: none repeat scroll 0 0 #4173A5;
    border: 1px solid #4173A5;
    color: #FFFFFF;
    font-weight: bold;
    height: 25px;
    left: 5px;
    margin: 0 5px 0 0;
    padding: 2px 8px 5px;
    position: absolute;
    top: 0;
    width: 77px;
}
.file_input_button_hover {
	width: 77px;
	position: absolute;
	top: 0px;
	left: 5px;
	border: 1px solid #2D6CB1;
	background-color: #2D6CB1;
	color: #FFFFFF;
	padding: 2px 8px 5px;
	height: 25px;
	margin: 0px;
	font-weight: bold;
}
.file_input_hidden {
	cursor: pointer;
    font-size: 45px;
    height: 26px;
    left: 0;
    position: absolute;
    top: 0;
    width: 80px;
	opacity: 0;
	filter: alpha(opacity=0);
	-ms-filter: "alpha(opacity=0)";
	-khtml-opacity: 0;
	-moz-opacity: 0;
}
</style>
<body>
<div style="text-align: center; background-color: #2E3134; padding: 20px;">
  <form id="imageform" method="post" enctype="multipart/form-data" action='<?php echo base_url()?>mcp/mail_manager/ajax_file_upload'>
    <input type="text" id="fileName" class="file_input_textbox" readonly>
    <div class="file_input_div">
      <input id="fileInputButton" type="button" value="<?php echo _("Browse");?>" class="file_input_button" />
      <input type="file" name="photoimg" id="photoimg" onChange="do_upload(this);" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" onmouseover="document.getElementById('fileInputButton').className='file_input_button_hover';" onmouseout="document.getElementById('fileInputButton').className='file_input_button';" />
    </div>    
  </form>
  <div id='preview'> </div>
</div>
</body>
</html>