<html>
	<head>
	</head>
	<body>
	<h1>Image Resizer Demo</h1>
		<script type="text/javascript">
 
var webresizerAPI = new Object;
webresizerAPI.parameters = {
	apikey : 'e0be5152cccb41872fc1b9e1b6980587',
	uplink_text : 'Add to My Gallery',
	default_image_size : '400',
	uplink_url : '<?php echo base_url();?>cp/cdashboard/webresizer'
};

</script>
<script type="text/javascript"
src="http://api.webresizer.com/ext1.0/js/webresizer_api.js"></script>

<hr>
<img src="<?php echo @$image_url;?>"><br />
<?php echo @$image_name;?><br />
<?php echo @$image_size;?><br />
<?php echo @$image_type;?><br />
<?php echo @$image_height;?><br />
<?php echo @$image_width;?>
	</body>
</html>