<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<!-- <script src="<?php echo base_url()?>assets/cp/new_js/jquery.knob.js"></script> -->
		
		<!-- jQuery File Upload Dependencies -->
		<!-- <script src="<?php echo base_url()?>assets/cp/new_js/jquery.ui.widget.1.js"></script> -->
		<!-- <script src="<?php echo base_url()?>assets/cp/new_js/jquery.iframe-transport.js"></script> -->
		<!-- <script src="<?php echo base_url()?>assets/cp/new_js/jquery.fileupload.js"></script> -->
		
		<!-- Our main JS file -->
		<!-- <script src="<?php echo base_url()?>assets/cp/new_js/file_upload_script.js"></script> -->
		<!-- <link href="<?php echo base_url()?>assets/cp/new_css/upload_style.css" rel="stylesheet" /> -->
		<script type="text/javascript">
		$(document).ready(function(){
			$('#iii').bind('change', function() {

				  //this.files[0].size gets the size of your file.
				  //alert(this.files[0].size);
					$("#ss").val(this.files[0].size);
				});
		});
		var ttlSize = 0;
		function validat(){
			
			/*alert($('#iii').files[0].size);
			return false;*/
// 			var ttlSize = image.size;
// 			alert(image.size()); 
// 			return false;
// 			if (ttlSize > 4 * 1048576) { // >4MB
// 			    // this will exceed post_max_size PHP setting, now handle...
// 			} else if ($('form').find('input[type=file]').length >= 10) {
// 			    // this will exceed max_file_uploads PHP setting, now handle...
// 			}
		}
		
		</script>
	</head>
	<body>
		<form id="upload" method="post" action="" enctype="multipart/form-data">
			<div id="drop">
				Drop Here

				<a>Browse</a>
				<input type="file" name="upl" id="iii" />
			</div>

			<ul>
				<!-- The file uploads will be shown here -->
			</ul>
			<input type="hidden" name="sss" id="ss" value="ssssssss" />
			<input type="submit" value="SUBMIT" onclick="return validat();" />
		</form>
	</body>
</html>