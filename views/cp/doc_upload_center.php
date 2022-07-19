<!-- MAIN -->
<script>$.noConflict();</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/cp/new_js/jquery.ui.1.10.4.js" type="text/javascript"></script>	
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/cp/new_css/jquery.ui.1.10.4.css"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/jquery.form.js"></script>
<script>
function do_upload(){
	$('#imageform').submit();
}

$(document).ready(function(){
	$('.delete_doc').on('click',function(){
		//alert($(this).attr('rel'));
		var parent_ref = $(this).parent().parent();
		$.post('<?php echo base_url(); ?>cp/mail_manager/delete_doc',{'doc_rel':$(this).attr('rel')},function(data){
			if(data.success){
				$(parent_ref).remove();
				alert(data.success);
				window.location.reload();
			}
			else{
				alert(data.error);
			}
		},'json');
		
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

      <h2><?php echo _('Doc. Upload Center')?></h2>

      <p class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Add and Manage Docs')?></p>

    </div>

    <div id="content">

      <div id="content-container">
      <?php if($this->session->flashdata('success')):?>
      <div id="succeed"><strong><?php echo $this->session->flashdata('success');?></strong></div>
      <?php elseif($this->session->flashdata('error')):?>
      <div id="error"><strong><?php echo $this->session->flashdata('error');?></strong></div>
      <?php endif;?>
      
      <div class="box">
          		<h3><?php echo _('Here you can upload docs and use them as attachment in the newsletters.'  )?></h3>
          		<div class="inside">
        <!--POST -->

        <div class="post">

			<div style="text-align: center; background-color: #2E3134; padding: 20px;">
			  <form id="imageform" method="post" enctype="multipart/form-data" action='<?php echo base_url()?>cp/mail_manager/doc_manager'>
			    <input type="text" id="fileName" class="file_input_textbox" readonly>
			    <div class="file_input_div">
			      <input id="fileInputButton" type="button" value="<?php echo _("Browse");?>" class="file_input_button" />
			      <input type="file" name="docs" id="docs" onChange="do_upload();" class="file_input_hidden" 
			      onmouseover="document.getElementById('fileInputButton').className='file_input_button_hover';"
			      onmouseout="document.getElementById('fileInputButton').className='file_input_button';" /><!-- onchange="javascript: document.getElementById('fileName').value = this.value" -->
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
				<?php //print_r($docs); ?>
				<?php if(count($docs)){ ?>
						<table>
							<thead>
								<tr>
									<th><?php echo _('S.No.'); ?></th>
									<th><?php echo _('Doc. Name'); ?></th>
									<th><?php echo _('Action');?></th>
								</tr>
							</thead>
							<tbody>
						<?php $counter = 0;
						foreach($docs as $k=>$doc){
							?>
							
								<tr>
									<td><?php echo ++$counter; ?></td>
									<td><a href="<?php echo base_url(); ?>assets/upload_center/docs/<?php echo $doc['doc_name']; ?>"><?php echo $doc['doc_name']; ?></td>
									<td><a href="javascript:void(0);" class="delete_doc" rel="<?php echo $doc['id']; ?>"><img alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete-2.png" /></a></td>
								</tr>
							
							<?php 
						}
						?>
							</tbody>
						</table>
						<?php 
				}?>
			</div>
        </div>

        <!-- ///POST -->
		</div>
		</div>
      </div>

    </div>

<!-- /content -->