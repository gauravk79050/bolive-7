<!-- MAIN -->
<script>$.noConflict();</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.ui.1.10.4.js" type="text/javascript"></script>	
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/cp/new_css/jquery.ui.1.10.4.css"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.form.js"></script>
<script>
function do_upload(obj){

	if(typeof $.browser != 'undefined' && $.browser.msie){
	
		 
	}else{
		
		fileSize = obj.files[0].size;
		var maxFileSize = (4 * 1048576); // 4 MB
		if(fileSize > maxFileSize){
			$("#uploaded_image").html("<span style='color: red'><?php echo _("File size is too big... Please upload image less than 4 MB")?></span>");
			tb_remove();
			return false;
	    }
	    
	}
	
   	jQuery("#preview").html('');
    jQuery("#preview").html('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="Uploading...."/>');
	jQuery("#imageform").ajaxSubmit({

		url: '<?php echo base_url()?>cp/mail_manager/image_manager',
		type:'post',
		
		success: function(response){
			jQuery("#uploaded_image").append(response);

			jQuery('#target').Jcrop({
		        //onChange: updatePreview,
		        onSelect: updateCoords,
		        setSelect: [ 60, 70, 330, 340 ]//,
		        //minSize: [ 80, 80 ],
		        //maxSize: [ 270, 270 ],
		        //aspectRatio: 1
		      });
		    tb_remove();
			
			jQuery("#preview").html('');
		},
		 error: function(responseText){
		        alert(responseText.status+'  ::  '+responseText.statusText);
		        jQuery("#preview").html('');
		    }
	});
    
}

$(document).ready(function(){
	$('.remove_me').live('click',function(){
		if(confirm("Are you sure you want to delete this image?")){
			var parent_ref = $(this).parent();
			var image_name = $(this).parent().attr('image_name');
			$.post('<?php echo base_url(); ?>cp/mail_manager/delete_image',{'action':'delete','image_name':image_name},function(data){
				if(data.success){
					$(parent_ref).remove();
					alert(data.success);
				}
				else{
					alert(data.error);
				}
			},'json');
		}
		//alert($(this).parent().attr('image_name'));
	});	
});
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
.image_holder{
	float: left;
	position: relative;
}
.remove_me{
	position: absolute;
	top: 0;
	right: 0;
}
</style>
  <div id="main">

    <div id="main-header">

      <h2><?php echo _('Image Upload Center')?></h2>

      <p class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Add and Manage Images')?></p>

    </div>

    <div id="content">

      <div id="content-container">
      <div class="box">
          		<h3><?php echo _('Here you can upload images and use them in the newsletters\' text widgets.'  )?></h3>
          		<div class="inside">
        <!--POST -->

        <div class="post">

			<div style="text-align: center; background-color: #2E3134; padding: 20px;">
			  <form id="imageform" method="post" enctype="multipart/form-data" action='<?php echo base_url()?>cp/mail_manager/ajax_image_upload'>
			    <input type="text" id="fileName" class="file_input_textbox" readonly>
			    <div class="file_input_div">
			      <input id="fileInputButton" type="button" value="<?php echo _("Browse");?>" class="file_input_button" />
			      <input type="file" name="photoimg" id="photoimg" onChange="do_upload(this);" class="file_input_hidden" 
			      onchange="javascript: document.getElementById('fileName').value = this.value" 
			      onmouseover="document.getElementById('fileInputButton').className='file_input_button_hover';"
			      onmouseout="document.getElementById('fileInputButton').className='file_input_button';" />
			    </div>    
			  </form>
			  <div id='preview'> </div>
			</div>
				<div id="uploaded_image"></div>
				<input type="hidden" id="x" name="x" />
  				<input type="hidden" id="y" name="y" />
  				<input type="hidden" id="w" name="w" />
  				<input type="hidden" id="h" name="h" />
			<div class="clear"></div>
			<div id="uploaded_images">
				<?php //print_r($images); ?>
				<?php if(count($images)){ 
						foreach($images as $k=>$image){
							$image_div = '';
							$image_div .= '<div class="image_holder" image_name='.$image['image_name'].'>';
							$image_div .= '<img src="'.base_url().'assets/upload_center/images/'.$image['image_name'] .'" alt="image_'.$k.'" width="150px" height="150px" style="padding:4px;" />';
							$image_div .= '<a href="javascript:void(0);" class="remove_me"><img alt="remove" src="'.base_url().'assets/cp/images/delete-2.png"></a>';
							$image_div .= '</div>';
							echo $image_div;
						}
				}?>
			</div>
        </div>

        <!-- ///POST -->
		</div>
		</div>
      </div>

    </div>

    <!-- /content -->
    <!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js"></script>
<script type="text/javascript">
var jcrop_api,
boundx,
boundy,xsize,ysize,$preview,$pcnt,$pimg;

  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
	//$('.ui-icon-gripsmall-diagonal-se').css('z-index','5000');
  };

  function checkCoords()
  {
    if (parseInt($('#w').val())) return true;
    alert("Please select a crop region then press submit.");
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
			url : base_url+'cp/mail_manager/crop_upload_center_image',
			data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
			type: 'POST',
			success: function(response){
				//$("#uploaded_image").toggle("slow");
				$("#uploaded_images").append(response);
				$('#uploaded_image').html('');;
				//$("#uploaded_image").focus();
				//$("#uploaded_image").toggle("slow");
			}
		});
	};
  
</script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css"/>
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