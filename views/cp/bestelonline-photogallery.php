<!-- ----------------------------- NEW IMAGE UPLOAD FUNCTIONALITY --------------------------------- -->
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.form.js"></script>
<script type="text/javascript" >
jQuery(document).ready(function($) { 
		
	if($.browser.msie && $.browser.version < 9){
		$("#imageform .button").remove();
	}else{
		$("#imageform .buttons").remove();
	}

	$('.del-img').click(function(){
	      
		  var id = $(this).attr('rel');
		  					  
		  $.post('<?php echo base_url(); ?>cp/bestelonline/photogallery',
		         {
				    'action':'delete_image',
					'image_index':id,
					'image_str':$('#old_gallery_images').val()
				 },
				 function(data)
				 {
		             if(data.status == 'success')
					 {
					    $('#old_gallery_images').val(data.result);
					    $('#'+id).fadeOut('slow');
					 }
					 else
					 if(data.status.trim() == 'error')
					 {
					    alert("<?php echo _('Some error occurred, could not delete the image !'); ?>");
					 }

		         },'json');
		  
	  });
}); 

function do_upload(obj){

	if($.browser.msie){
	
		 
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

		url: '<?php echo base_url()?>cp/image_upload/ajax_image_upload',
		type:'post',
		
		success: function(response){
			jQuery("#uploaded_image").html(response);
			jQuery("#preview").html('');
		    jQuery('#target').Jcrop({
		        onSelect: updateCoords,
		        setSelect: [ 60, 70, 330, 340 ],
		        aspectRatio: 1
		      });
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
    /*width: 300px;*/
}
.file_input_textbox {
	height: 25px;
	width: 0px;
	float: left;
	background:#ffffff;
	border:none;
}
.file_input_div {
	height: 167px;
    overflow: hidden;
    position: relative;
    width: 181px;
}
.file_input_button {
	/*background: none repeat scroll 0 0 #4173A5;
    border: 1px solid #4173A5;*/
   color: #FFFFFF;
    font-weight: bold;
    height:167px;
    left: 5px;
    position: absolute;
    top: 0;
   width: 176px;
}
.file_input_button_hover {
	width: 176px;
	position: absolute;
	top: 0px;
	left: 5px;
	/*border: 1px solid #2D6CB1;
	background-color: #2D6CB1;*/
	color: #FFFFFF;
	/*padding: 2px 8px 5px;*/
	height:167px;
	margin: 0px;
	font-weight: bold;
}
.file_input_hidden {
	cursor: pointer;
    font-size: 45px;
    height: 167px;
    left: 0;
    opacity: 0;
    position: absolute;
    top: 0;
    width: 176px;
	filter: alpha(opacity=0);
	-ms-filter: "alpha(opacity=0)";
	-khtml-opacity: 0;
	-moz-opacity: 0;
}
</style>
<!-- ---------------------------------------------------------------------------------------------- -->
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
			url : base_url+'cp/bestelonline/crop_image',
			data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
			type: 'POST',
			success: function(response){
				//$("#uploaded_image").toggle("slow");
				$("#gallery_images").val($("#image_name").val());
				$("#uploaded_image").html(response);
				$("#uploaded_image").focus();
				$("#save_img_form").show();
				//$("#uploaded_image").toggle("slow");
			}
		});
	};
  
</script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/cp/new_css/jquery.Jcrop.css" type="text/css" />
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
</style>
<!-- -------------------------------------------------------------------------------------------- -->

<style type="text/css">
#sidebar, #sidebarSet{
	display:none;
}

#uploaded_preview{
}

#uploaded_preview img{
	margin-right:5px;
	margin-bottom:5px;
}

#save_images{
	background:#18517E;
	color:#fff;
	font-weight:bold;
	border:0px;
	padding:10px 25px;
}

div.gallery-cell{
	float: left;
    padding: 5px;
    position: relative;
}

img.gallery-img{
	border: 1px solid #EEEEEE;
    height: 200px;
    width: 200px;
}

a.del-img{
	position: absolute;
    right: -3px;
    text-decoration: none;
    top: -1px;
}

</style>

<div id="main" style="text-transform:none;">		
 <div id="main-header">
	<h2><?php echo _('BESTELONLINE SETTINGS')?></h2>
      <p class="breadcrumb" style="float:left;margin-left:10px;"><a href="<?php echo base_url()?>cp/bestelonline/bp_settings"><?php echo _('Settings')?></a>&nbsp;&nbsp;<a href="<?php echo base_url()?>cp/bestelonline/photogallery"><b><?php echo _('Photogallery')?></b></a></p>
    </div>
	
	<?php $messages = $this->messages->get();?>
	<?php if($messages != array()):?>
		<?php foreach($messages as $key => $val):?>
			<?php foreach($val as $v):?>
				<div  class = "<?php echo $key;?>"><strong><?php echo ucfirst($key);?> : </strong><?php echo $v;?></div>
			<?php endforeach;?>	
		<?php endforeach;?>
    <?php endif;?>
	
	<div id="content" style="width: 100%;">
      <div id="content-container">
	     
		 <div class="box">
			<h3><?php echo _('Photogallery')?></h3>
			<div class="table">
			
			<table cellspacing="0" cellpadding="0" border="0">
			<tr>
			  <td>
			  
			  <?php
			        $gallery_imgs = '';
			        
			        if( !empty($general_settings) ) 
					{
					   $gallery_imgs =  $general_settings[0]->company_gallery_imgs;
					}			  
			  ?>
			
			  <p><strong><?php echo _('Upload images to be displayed in company\'s gallery, on Bestelonline : '); ?></strong></p>
			  <br />
			
			  <?php /*?><link href="<?php echo base_url(); ?>assets/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
			  <script type="text/javascript" src="<?php echo base_url(); ?>assets/uploadify/jquery-1.4.2.min.js"></script>
			  <script type="text/javascript" src="<?php echo base_url(); ?>assets/uploadify/swfobject.js"></script>
			  <script type="text/javascript" src="<?php echo base_url(); ?>assets/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
			  <script type="text/javascript">
			
				$(document).ready(function() {
				 
				  $('#file_upload').uploadify({
					  'uploader'  : '<?php echo base_url(); ?>assets/uploadify/uploadify.swf',
					  'script'    : '<?php echo base_url(); ?>assets/uploadify/uploadify.php',
					  'cancelImg' : '<?php echo base_url(); ?>assets/uploadify/cancel.png',
					  'folder'    : '<?php echo $gallery_upload_path; ?>',
					  'fileDesc'  : 'Image Files',
					  'fileExt'   : '*.jpg;*.jpeg;*.gif;*.png',
					  'sizeLimit' : 100 * 1024 * 1024,
					  'buttonText': '<?php echo _('BROWSE').'...'; ?>',
					  'multi'     : true, 
					  'auto'      : true,
					  'onComplete': function(event, ID, fileObj, response, data){
			
						  $("#uploaded_preview").append("<img src='"+response+"' width='150px' height='150px' />");
						  
						  var new_str = $("#gallery_images").val();						  
						  new_str = new_str+response+', ';
						  
						  $("#gallery_images").val( new_str );
						  
					  }
				  });
				
				});
			  </script>
			  
			  <div id="uploaded_preview">
			  </div>
			  
			  <input id="file_upload" name="file_upload" type="file" /> <?php */?>
			  <div id="uploaded_image"></div>
              
		      <form id="imageform" method="post" enctype="multipart/form-data" action='<?php echo base_url()?>cp/bestelonline/ajax_image_upload'>
			    <input type="text" id="fileName" class="file_input_textbox" readonly>
			    <div class="file_input_div">
			      <!-- <input id="fileInputButton" type="button" value="<?php echo _("Browse");?>" class="file_input_button" /> -->
			      <img id="fileInputButton" src="<?php echo base_url();?>assets/images/plus.png" alt="<?php echo _("Browse");?>" class="file_input_button" />
			      <input type="file" name="photoimg" id="photoimg" onChange="do_upload(this);" class="file_input_hidden" 
			      onchange="javascript: document.getElementById('fileName').value = this.value" 
			      onmouseover="document.getElementById('fileInputButton').className='file_input_button_hover';"
			      onmouseout="document.getElementById('fileInputButton').className='file_input_button';" />
			    </div>    
			  </form>
			  <div id='preview'> </div>
			  <br />
		
			  <form id="save_img_form" action="<?php echo base_url(); ?>cp/bestelonline/photogallery" method="post" style="display: none;">
			      <input type="hidden" class="image" name="old_gallery_images" id="old_gallery_images" value="<?php echo $gallery_imgs; ?>"/>
			      <input type="hidden" id="x" name="x" />
				  <input type="hidden" id="y" name="y" />
				  <input type="hidden" id="w" name="w" />
				  <input type="hidden" id="h" name="h" />
				  <input type="hidden" class="image" name="gallery_images" id="gallery_images" value=""/>
				  <input type="submit" name="save_images" id="save_images" value="<?php echo _('SAVE'); ?>" />
			  </form>
			  
			  </td>
			</tr>
			<tr>
			  <td>
			      <?php    if($gallery_imgs)
						     $gallery_imgs = explode(', ',$gallery_imgs);
						   
						   if(!empty($gallery_imgs))
						   {
						       foreach( $gallery_imgs as $id=>$img )
							   {
							      ?>
								  <div class="gallery-cell" id="<?php echo $id; ?>">
								  <img src="<?php echo base_url(); ?>assets/cp/images/company-gallery/<?php echo $company_slug; ?>/<?php echo $img; ?>" class="gallery-img" />
								  <a href="javascript:void(0);" rel="<?php echo $id; ?>" class="del-img" title="<?php echo _('Delete Image'); ?>">
								     <img width="16" height="16" src="<?php echo base_url(); ?>assets/mcp/thickbox/javascript/Delete.gif">
								  </a>
								  </div>
								  <?php
							   }
						   }
						   else
						   {
						       ?>
							   <p><strong><?php echo _('No gallery images, uploaded yet !'); ?></strong></p>
							   <?php
						   }						
				  ?>
			  </td>
			</tr>
			</table>
  
			</div>
		 </div>
		 
	  </div>