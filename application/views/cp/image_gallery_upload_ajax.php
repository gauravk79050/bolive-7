<html>
	<head>
	</head>
	<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.form.js"></script>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		if(navigator.userAgent.msie && navigator.userAgent.version < 9){
			$("#imageform .button").remove();
		}else{
			$("#imageform .buttons").remove();
		}
	});
	function do_upload(obj){
		 //alert($(obj).attr('data-num'));
		var num = jQuery(obj).attr('data-num');
		var wid = jQuery(obj).attr('data-width');
		//alert(num);
		if(navigator.userAgent.msie){}
		else{
			fileSize = obj.files[0].size;
			var maxFileSize = (4 * 1048576); // 4 MB
			
			if(fileSize > maxFileSize){
				$("#uploaded_image"+num).html("<span style='color: red'><?php echo _("File size is too big... Please upload image less than 4 MB")?></span>");
				tb_remove();
				return false;
	    	}
		}
		
	   	jQuery("#preview").html('');
	    jQuery("#preview").html('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="Uploading...."/>');
		jQuery("#imageform").ajaxSubmit({
			url: '<?php echo base_url()?>cp/image_upload/ajax_gallery_image_upload/'+num+'/'+wid,
			type:'post',
			success: function(response){
				jQuery("#uploaded_image"+num).html(response);
				if( num == 5 ) {
					jQuery(".hide_aller_banner_div").hide();

				}
				if(num == 5 || num == 6)
				{	
         		 jQuery(".hide_aller_banner").hide();
					jQuery('#target').Jcrop({
				        boxWidth 	: 800,
						boxHeight 	: 300,
				        onSelect: updateCoords,
				        onChange: updateCoords,
				        setSelect: [ 0, 0, 1600, 1600 ],
				        aspectRatio: (1920/786),
				      });
				}
				if(num == 7)
				{	
					jQuery(".hide_image_box").hide();
					jQuery('#target').Jcrop({
				        onSelect: updateCoords,
				        onChange: updateCoords,
				        setSelect: [ 0, 0, 250, 250 ],
				        aspectRatio: (250/250),
				      });
				}
				 if(num == 4)
				{	
					jQuery(".hide_image_box").hide();
					jQuery('#target').Jcrop({
				        onSelect: updateCoords,
				        onChange: updateCoords,
				        setSelect: [ 0, 0, 250, 250 ],
				        aspectRatio: (2/1)
				      });
				}
				if(num != 7 && num != 5 && num != 6 && num != 4  && num != 8){
					jQuery('#target').Jcrop({
				        onSelect: updateCoords,
				        onChange: updateCoords,
				        setSelect: [ 0, 0, 150, 150 ],
				        aspectRatio: 1
				      });
				}
				if(num == 8){
					jQuery(".hide_image_box").hide();
					jQuery('#target').Jcrop({
				        onSelect: updateCoords,
				        onChange: updateCoords,
				        setSelect: [ 100,100,50,50 ],
					   	 minSize: [860,135]
				      });
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
		  <form id="imageform" method="post" enctype="multipart/form-data" action='<?php echo base_url()?>cp/image_upload/ajax_image_upload/'>
		    <input type="text" id="fileName" class="file_input_textbox" readonly>
		    <div class="file_input_div">
		      <input id="fileInputButton" type="button" value="<?php echo _("Browse");?>" class="file_input_button" />
		      <input type="file" name="photoimg" id="photoimg" onChange="do_upload(this);" class="file_input_hidden" 
		      onchange="javascript: document.getElementById('fileName').value = this.value" 
		      onmouseover="document.getElementById('fileInputButton').className='file_input_button_hover';"
		      onmouseout="document.getElementById('fileInputButton').className='file_input_button';" data-width="<?php echo $width;?> " data-num="<?php echo $num;?>"/>
		    </div>    
		    <!-- <a><?php echo _("Browse");?></a>
			<input type="file" name="photoimg" id="photoimg" onChange="do_upload(this);" /> -->
		    
		  </form>
		  <div id='preview'> </div>
		</div>
	</body>
</html>