<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js"></script>

<script type="text/javascript">
var jcrop_api,
boundx,
boundy,xsize,ysize,$preview,$pcnt,$pimg;

$(document).ready(function(){

	$('.remove_image').click(function(){
  	  	
	  		$.post('<?php echo base_url();?>cp/categories/delete_cat_image',{category_id: $(this).attr('rel') },function(response){
	  	  		if(response.trim() == 'success'){
	  	  	  		window.location.reload();
	  	  	  	}else{
	  	  	  		alert("<?php echo _("Image can not be deleted successfully");?>");
	  	  	  	}
		});
	});
	
    /*$('#cropbox').Jcrop({
      aspectRatio: 1,
      onSelect: updateCoords,
      setSelect: [ 60, 70, 540, 330 ],
      minSize: [ 80, 80 ],
      maxSize: [ 300, 300 ]
    });*/
	$(".thickboxed").click(function(){
		//tb_show('Details','TB_inline?width=700&height=555&inlineId=ajax_upload_image_div','');
		tb_show("Upload Image", "<?php echo base_url(); ?>cp/image_upload/ajax_img_upload?height=400&width=600", "true");
	});
		
  });

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

	function updatePreview(c){
    	if (parseInt(c.w) > 0){
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
			url : base_url+'cp/image_upload/crop_image',
			data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
			type: 'POST',
			success: function(response){
				//$("#uploaded_image").toggle("slow");
				$("#uploaded_image").html(response);
				$("#uploaded_image").focus();
				//$("#uploaded_image").toggle("slow");
			}
		});
	};

	function rotcw(obj) {
			      		$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" />');
						console.log($(obj).attr('data-img'));
						$.ajax({
								type:'POST',
								url: base_url+'cp/image_upload/rotate_image',
								data:{src:$(obj).attr('data-img1'),angle:'cw'},
								success: function(response){
									$("#uploaded_image").html(response);
									
									jQuery('#target').Jcrop({
							       	//onChange: updatePreview,
							       	onSelect: updateCoords,
								    setSelect: [ $('#x').val(), $('#y').val(), $('#w').val(), $('#h').val() ],
								   
								    aspectRatio: 1
								    });
								},
							});
			  		}
			  		function rotacw(obj) {

			      		$("#uploaded_image").html('<img src="'+base_url+'assets/cp/images/loader.gif" />');
						
						$.ajax({
								type:'POST',
								url: base_url+'cp/image_upload/rotate_image',
								data:{src:$(obj).attr('data-img2'),angle:'acw'},
								success: function(response){
									$("#uploaded_image").html(response);
									
									jQuery('#target').Jcrop({
							       	//onChange: updatePreview,
							       	onSelect: updateCoords,
								    setSelect: [ $('#x').val(), $('#y').val(), $('#w').val(), $('#h').val() ],
								   
								    aspectRatio: 1
								    });
								},
							});
			  		}

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
#TB_window{
	top: 80% !important;
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
</style>
<!----------------------------------------------------------------------------------------------- -->
<script src="<?php echo base_url()?>assets/cp/js/jquery.tooltip.js"></script>
<link href="<?php echo base_url()?>assets/cp/css/qtip.css" rel="stylesheet" type="text/css">
<style>
#TB_window{
	margin-top: -270px !important;
}
.save_b{
	padding: 20px 60px 20px 20px;
    text-align: right;
}
</style>
<script type="text/javascript" >
/*function show_webresizer(){
	var $form = $('#frm_categories_addedit');
	$.ajax({
		url: '<?php echo base_url()?>/cp/cdashboard/form_values_categories',
		type: 'POST',
		data: $form.serialize(),
		success: function(data){
			tb_show('Details','TB_inline?width=700&height=555&inlineId=show_webresizer','');
			}
	});	
}*/
</script>
<!-- MAIN -->
<div id="main">
<?php $url_chk = explode('?',$_SERVER['REQUEST_URI']); if(sizeof($url_chk) == 1){$this->session->set_userdata('form_data_category','');}?>
<?php $form_data = array(); $image_error = 0;?>
<?php if($this->session->userdata('form_data_category')){ $form_data = $this->session->userdata('form_data_category'); $this->session->set_userdata('form_data_category',''); }?>
	<div id="main-header">
    	<h2><?php if($category_data): echo _('UPDATE CATEGORY'); else: echo _('ADD CATEGORY'); endif;?></h2>
	  	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard/"><?php echo _('Home');?></a> &raquo; <a href="<?php echo base_url()?>cp/_categories/categories"><?php echo _('Categories')?> </a> &raquo; <?php if($category_data): echo _('update category'); else: echo _('add category'); endif;?>				</span>
	</div>
 	<div style="display:none" id="error"><?php echo _('category already exist')?></div>
    <div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Information'); ?></h3>
          		<div class="table">
					<?php
					$attributes = array('id' => 'frm_categories_addedit','name'=>'frm_categories_addedit','enctype'=>'multipart/form-data');
					echo form_open_multipart('cp/categories/categories_addedit', $attributes);			
					?>
						<input type="hidden" value="categories_addedit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
						<table border="0">
							<tbody>
								<tr>
									<td width="120px" class="textlabel"><?php echo _('Category Name'); ?></td>
									<td colspan="2"><input type="text" class="text medium" size="30" id="name" name="name" value="<?php if(($form_data) && $form_data['name']): echo $form_data['name']; elseif($category_data): echo $category_data['0']->name; endif;?>"onkeyup="return check_category()"></td>
								</tr>
								
								<tr>
									<td width="120px" class="textlabel"><?php echo _('Category Image'); ?></td>
									<td style="padding-right:00px" colspan="2">
		                   				<div id="uploaded_image"></div>
		                   				<input type="hidden" id="x" name="x" />
						  				<input type="hidden" id="y" name="y" />
						  				<input type="hidden" id="w" name="w" />
						  				<input type="hidden" id="h" name="h" />
		                   				<div><a href="javascript:;" class="thickboxed" ><input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a></div>
		                   			</td>
								</tr>
								<?php if($category_data && $category_data['0']->image):?>
				              	<tr>
				                	<td class="textlabel"><?php echo _('Current image')?></td>
				                	<td style="padding-right:250px" id="current_cat_img">
				                		<img src="<?php echo base_url(); ?><?php echo $category_data['0']->image; ?>" height="100" width="100" />
										<input type="hidden" name="old_image" id="old_image" value="<?php echo $category_data['0']->image; ?>" />
					                  	<a href="#" class="remove_image" rel="<?php echo $category_data['0']->id;?>"><?php echo _('Remove'); ?></a>
				                  		<!-- <img src="<?php echo base_url(''); ?>assets/cp/images/product/no_image.jpg" alt="<?php echo _('No image available.Please upload one.')?>"/> -->
				               			<input class="rotated_image_hid" type="hidden" value="" name="rotated_image">
				                  		<input type="hidden" name="current_prod_img" id="current_prod_image" value="<?php echo basename($category_data['0']->image);?>">
				               		</td>
				               		<td>
					               		<?php if(!empty($category_data['0']->image)) { ?> 
				                		<!-- For not showing the rotate images if uploaded images not there -->
										<a href="javascript:;" class="pro_rotate_img" onClick="srotcw(this)" data-img1="<?php echo basename($category_data['0']->image);?>" title="<?php echo _('Rotate image Clock-wise')?>">
											<img src="<?php echo base_url();?>/assets/cp/images/cw.png"></a>
										<a href="javascript:;" class="pro_rotate_img" onClick="srotacw(this)" data-img2="<?php echo basename($category_data['0']->image);?>" title="<?php echo _('Rotate image Anti-clockwise')?>">
											<img src="<?php echo base_url();?>/assets/cp/images/acw.png">
										</a>
									<?php } ?>
				               		</td>
				              	</tr>
				              	<?php endif;?>
			              
								<tr>
									<td class="textlabel"><?php echo _('Description'); ?></td>
									<td colspan="2"><textarea style="width:400px" rows="5" cols="50" type="textarea" id="description" name="description"><?php if(($form_data) && @$form_data['description']): echo $form_data['description']; elseif ($category_data): echo $category_data['0']->description;  endif;?></textarea></td>
								</tr>
								<tr>
									<td class="textlabel"><?php echo _('Popup');?></td>
									<td colspan="2">
										<input type="checkbox"  value="1" class="checkbox" id="display_tool_tip" name="display_tool_tip" <?php if(($form_data) && @$form_data['display_tool_tip'] && @$form_data['display_tool_tip'] == '1'):?>checked="checked"  <?php elseif ($category_data&&$category_data['0']->display_tool_tip=='1'):?>checked="checked"  <?php endif;?>>
						  				&nbsp;&nbsp;&nbsp;
						  				<a title="<?php echo _('If you check this box, your customers will see this description when the mouse cursor over a link to category (there is a popup appeared.');?>" href="#" id="help-cat1"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a>
						  			</td>
								</tr>
								<tr>
									<td class="textlabel"><?php echo _('Add Text');?></td>
									<td colspan="2">
										<input type="checkbox" onChange="javascript:show_hide();" class="checkbox" id="add_text" name="add_text"  value="1" <?php if(($form_data) && @$form_data['add_text'] && @$form_data['add_text'] == '1'):?>checked="checked"  <?php elseif ($category_data&&$category_data['0']->add_text):?>checked="checked"<?php endif;?>>
						 				&nbsp;&nbsp;&nbsp;
						 				<a title="<?php echo _('If your customers have clicked on a category you can on this page a message at the top. This is a promotional text related to this category. The message below may place.');?>" href="#" id="help-cat2"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a>
						 			</td>
								</tr>
								<tr>
									<td class="textlabel"><span <?php if(($form_data) && @$form_data['add_text'] && @$form_data['add_text'] == '1'){?><?php }else{?>style="display:none"<?php }?> id="decision1"><?php echo _('Message'); ?></span></td>
									<td colspan="2"><span style="display:none" id="decision2"><textarea style="width: 600px; height: 200px;" type="textarea" id="message" name="message"><?php if(($form_data) && @$form_data['message']): echo $form_data['message']; elseif ($category_data): echo $category_data['0']->message;  endif;?></textarea></span></td>
								</tr>
								<tr>
								<?php if($category_data):?>
									<td class="save_b" colspan="3">
										<input type="submit" value="<?php echo _('Update');?>" class="submit" id="update" name="update">
						  				<input type="hidden" value="<?php echo $category_data['0']->id;?>" id="category_id" name="category_id">
						  			</td>
								<?php else:?>
									<td class="save_b" colspan="3">
										<input type="submit" value="<?php echo _('Add');?>" class="submit" id="btn_update" name="btn_update">
										<input type="hidden" value="add_edit" id="act" name="act">
										<input type="hidden" value="" id="id" name="id">
									</td>
								 <?php endif;?>
								</tr>
							</tbody>
						</table>
					</form>
					<script type="text/javascript" language="javascript">
					
						var frmValidator = new Validator("frm_categories_addedit");
						frmValidator.EnableMsgsTogether(validate_mess);
						
						frmValidator.addValidation("name","req","<?php echo _('Enter the name');?>");
						//frmValidator.addValidation("description","req","<?php echo _('Please give a description please');?>");
	
					/* START : show / hide message box used in check box*/ 
					
						function show_hide(){
							var add_text = document.getElementById('add_text').checked;
							if(add_text == true){
								document.getElementById('decision1').style.display = 'block';
								document.getElementById('decision2').style.display = 'block';
							}else if(add_text == false){
								document.getElementById('decision1').style.display = 'none';
								document.getElementById('decision2').style.display = 'none';
							}
						}
					/* END : show / hide message box */	
						function validate_mess(result){
							if(result == true){
								var isChecked = document.getElementById('add_text').checked;
								if(isChecked == true){
									var text = tinyMCE.get('message').getContent();
									if(text == ""){								
										alert("<?php echo _('Please leave a message please give');?>");
										return false;
									}
									return true;
								}else{
									return true;
								}
							}
							return false;
						}
					
						<!--this js function is to check that the category name already exist or not-->
						function check_category(){
								jQuery.post("<?php echo base_url()?>cp/categories/check_category",
								{'name':document.getElementById('name').value},
								function(data){
									if(data.trim()=="exist"){
										jQuery("#error").css({'display':'block'});
										$("#name").focus();
										alert("Category already Exists");
										return false;
									}else{
										jQuery("#error").css({'display':'none'});
										return true;
									}
							});
						
						}
						<!---------------------------------------------------------------------------->
						jQuery(document).ready(function($){
							$('#help-cat1').tipsy({gravity: 'w'});
	
							$('#help-cat2').tipsy({gravity: 'w'});
			
							show_hide();
						});
						function srotcw(obj) {
							$.ajax({
									type:'POST',
									url: base_url+'cp/categories/rotate_uploaded_image',
									data:{src:$(obj).attr('data-img1'),angle:'cw'},
									success: function(response){
										$(obj).parent().children('a').eq(0).attr('data-img1',response);
										$(obj).parent().children('a').eq(1).attr('data-img2',response);
										$("#current_cat_img").children('img').replaceWith('<img  height="100" width="100" src="'+base_url+"assets/cp/images/product/rotated/"+response+'"/>');
										$('.rotated_image_hid').val(response);
									},
								});
						}
						function srotacw(obj) {
							$.ajax({
									type:'POST',
									url: base_url+'cp/categories/rotate_uploaded_image',
									data:{src:$(obj).attr('data-img2'),angle:'acw'},
									success: function(response){
										$(obj).parent().children('a').eq(0).attr('data-img1',response);
										$(obj).parent().children('a').eq(1).attr('data-img2',response);
										$("#current_cat_img").children('img').replaceWith('<img  height="100" width="100" src="'+base_url+"assets/cp/images/product/rotated/"+response+'"/>');
										$('.rotated_image_hid').val(response);
									},
								});
						}
					</script>

          		</div>
        	</div>
		</div>
 	</div>
    <!-- /content -->
