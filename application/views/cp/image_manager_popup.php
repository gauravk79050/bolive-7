<html>
	<head>
	</head>
	<!-- <script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script> -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery-migrate-1.2.1.js"></script>
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
	/*custom css*/
	.image_cont img{
		cursor: pointer;
	}
	.image_cont{
		float: left;
		margin: 5px;
	}
	#image_container > div {
	    text-align: left;
	}
	
	
	</style>
	<script>
	$(document).ready(function(){
		
		$('.image_cont').on('click',function(){
			doInsertImage($(this).find('img').attr('rel'));
			self.parent.tb_remove();
		});
	
		$('#TB_ajaxContent').bind('DOMMouseScroll', function(e){
	
		    if(e.originalEvent.detail > 0) {
		        //alert('down');
		 		this.scrollLeft += 30;
		    }else {
		 		this.scrollLeft -= 30;
		    	//alert('up');
		    }
	
		     //prevent page fom scrolling
		     return false;
		 });
	
		/*$('#prev_cont').on('click',function(){
			$('html, body, *').mousewheel(function(e, delta) {
				$('html, body, *').scrollLeft -= (delta * 40);
				e.preventDefault();
			});
			//alert($('#main_image_cont').scrollLeft);
			$('#main_image_cont').stop().animate({
				this.scrollLeft -= (delta * 30);
	            scrollLeft: $($anchor.attr('href')).offset().left
	        }, 1000);
		});*/
	});
	</script>
	<body>
		<div id="main_image_cont" style="overflow-x: auto;">
			<div id="image_container" style="text-align: center; height: 160px;width:<?php if(!empty($images)){echo count($images)*173;}else{echo '675';}?>px;" >
				<?php 
					$path = base_url().'assets/upload_center/images/';
					if(!empty($images)){
						foreach($images as $image){
							if(isset($image['image_name'])){
								echo '<div class="image_cont" style="float: left;">';
								echo "<img rel='".$image['image_name']."' src='".$path.$image['image_name']."' alt='Image Not Found' width='150px' height='150px' style='padding:5px;' >";
								echo '</div>';
							}
							else{
								echo $path.$image['image_name'];die;
							}
						}	
					}else{
					echo '<h2>No images.</h2>';
					}
				?>
			</div>
			
		</div>
		<div style="text-align: right">
				<a href="<?php echo base_url();?>cp/mail_manager/image_manager"><?php echo ('Go to Image Manager to Add/Delete images.');?></a>
		</div>
	</body>
</html>