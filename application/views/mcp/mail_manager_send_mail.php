<html>
	<head>
		<script src="<?php echo base_url(); ?>assets/cp/js/jquery-1.6.2.min.js" type="text/javascript"></script>
		<script src="<?php echo base_url(); ?>assets/cp/js/tiny_mce/tiny_mce.js" type="text/javascript"></script>
		<script src="<?php echo base_url(); ?>assets/cp/new_js/jquery.form.js" type="text/javascript"></script>		
		<script type="text/javascript">
		/* Custom filtering function which will filter data in column four between two values */
			$(document).ready(function() {
				tinymce.init({
					mode : "exact",
					elements: "send_mail_txt",
					theme : "advanced",
					plugins : "autolink,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template", 
					theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,|,link,image",
					theme_advanced_buttons2 : "media,|,forecolor,backcolor,emoticons,tablecontrols",
					theme_advanced_buttons3 : "",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "center",
					theme_advanced_resizing : true,
					theme_advanced_statusbar_location : "bottom"
				});
			});

			function send_mail_fun(){
				$.post(
						'<?php echo base_url();?>mcp/mail_manager/send_quick_mail',
						{'to_id_hidden':$("#to_id_hidden").val(), 'send_mail_sub':$("#send_mail_sub").val(), 'send_mail_txt': tinymce.get('send_mail_txt').getContent()},
						function(response){
							if(response.error){
								$("#response_e").html(response.message);
								$("#response_s").html('');
							}else{
								$("#response_e").html('');
								$("#response_s").html(response.message);
							}
						},
						'json'
					);
			}
		</script>
		<style type="text/css">
			body{
				 font: 12px normal Verdana,Arial,Helvetica,sans-serif;
			}
		</style>
	</head>
	<body>
		<div id="mail_wrapper">
			<form id="send_mail_form" action="<?php echo base_url();?>mcp/mail_manager/send_quick_mail" method="post">
				<p id="response_e" style="color: red;"></p>
				<p id="response_s" style="color: green;"></p>
				<p><?php echo _("Mail to");?>: <span id="to_id"><strong><?php echo $email;?></strong></span></p>
				<input type="hidden" name="to_id_hidden" id="to_id_hidden" value="<?php echo $email;?>" />
				<lable style="display: block; margin-bottom: 10px;">
					<span style="float: left; width:75px; margin-right:10px"><?php echo _("Subject");?></span>
					<input type="text" id="send_mail_sub" name="send_mail_sub" />
					<div style="clear: both;"></div>
				</lable>
				<lable style="display: block; margin-bottom: 10px;">
					<span style="float: left; width:75px; margin-right:10px"><?php echo _("Message");?></span>
					<textarea id="send_mail_txt" name="send_mail_txt" cols="100" style="width: 500px;"> 
					</textarea>
					<div style="clear: both;"></div>
				</lable>
				<lable style="display: block;">
					<input type="button" id="send_mail_btn" name="send_mail_btn" onclick="send_mail_fun();" value="<?php echo _("Send");?>" style="float: right; margin-right:26px;" />
				</lable>
			</form>
		</div>
	</body>
</html>