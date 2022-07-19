<!-- -------------------------------------- CROPPING IMAGE ------------------------------------------------------ -->
<script src="<?php echo base_url();?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version;?>"></script>
<script type="text/javascript">
var jcrop_api,
boundx,
boundy,xsize,ysize,$preview,$pcnt,$pimg;

$(document).ready(function(){
	$('.remove_image').click(function(){
	  		$.post('<?php echo base_url();?>cp/cdashboard/delete_subcat_image',{subcategory_id: $(this).attr('rel') },function(response){
	  	  		if(response.trim() == 'success'){
	  	  	  		window.location.reload();
	  	  	  	}else{
	  	  	  		alert("<?php echo _('Image can not be deleted successfully');?>");
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
		//alert("shyam");
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
<!-- -------------------------------------------------------------------------------------------- -->
<script src="<?php echo base_url()?>assets/cp/js/jquery.tooltip.js?version=<?php echo version;?>"></script>
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
// function show_webresizer(){
// 	var $form = $('#frm_subcategories_addedit');
// 	$.ajax({
//		url: '<?php echo base_url()?>/cp/cdashboard/form_values_subcategories',
// 		type: 'POST',
// 		data: $form.serialize(),
// 		success: function(data){
// 			tb_show('Details','TB_inline?width=700&height=555&inlineId=show_webresizer','');
// 			}
// 	});	
// }
</script>
  <link href="<?php echo base_url()?>assets/cp/css/qtip.css" rel="stylesheet" type="text/css">
  
   <script type="text/javascript">

  jQuery(document).ready(function($) {

    $('#help').tipsy({gravity: 'w'});

    $('#help2').tipsy({gravity: 'w'});

	$('#help3').tipsy({gravity: 'w'});

    $('#help4').tipsy({gravity: 'w'});

    $('#help5').tipsy({gravity: 'w'});

    $('#help6').tipsy({gravity: 'w'});

    $('#help7').tipsy({gravity: 'w'});

    $('#help8').tipsy({gravity: 'w'});

    $('#help9').tipsy({gravity: 'w'});

    $('#help10').tipsy({gravity: 'w'});

	$('#help11').tipsy({gravity: 'w'});

    $('#help12').tipsy({gravity: 'w'});

    $('#help13').tipsy({gravity: 'w'});

    $('#help14').tipsy({gravity: 'w'});

// categories

    $('#help-cat1').tipsy({gravity: 'w'});

    $('#help-cat2').tipsy({gravity: 'w'});

    $('#help-cat3').tipsy({gravity: 'w'});

    $('#help-cat4').tipsy({gravity: 'w'});

// producten

    $('#help-prod1').tipsy({gravity: 'w'});

    $('#help-prod2').tipsy({gravity: 'w'});

    $('#help-prod3').tipsy({gravity: 'w'});

    $('#help-prod4').tipsy({gravity: 'w'});
  });
</script>
  <!-- MAIN -->
  <div id="main">
  <?php $url_chk = explode('?',$_SERVER['REQUEST_URI']); if(sizeof($url_chk) == 1){$this->session->set_userdata('form_data_subcategory','');}?>
<?php $form_data = array(); $image_error = 0;?>
<?php //echo $_SERVER['REQUEST_URI'];?>
<?php if($this->session->userdata('form_data_subcategory')){ $form_data = $this->session->userdata('form_data_subcategory'); $this->session->set_userdata('form_data_subcategory',''); }?>
    <div id="main-header">

       <h2><?php if($subcategory_data):?><?php echo _("UPDATE SUBCATEGORY")?> <?php else:?><?php echo _("ADD SUBCATEGORY")?> <?php endif;?></h2>

      <span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/cdashboard/subcategories"><?php echo _(' Subcategory')?></a> &raquo; <?php if($subcategory_data):?><?php echo _("Update subcategories")?> <?php else:?><?php echo _("Add subcategories")?> <?php endif;?></span></div>

	<div style="display:none"  id="error" class="notification"><?php echo _('Error Occured:sub category already exist');?></div>
    <div id="content">
	
	  <div id="content-container">

        <div class="box">

          <h3><?php echo _('Information')?></h3>

          <div class="table">
            <form action="<?php echo base_url('')?>cp/subcategories/subcategories_addedit" enctype="multipart/form-data" method="post" id="frm_subcategories_addedit" name="frm_subcategories_addedit">
			<?php if ($subcategory_data):?>
              <input type="hidden" value="<?php echo  $subcat_id?>" name="subcat_id" id="subcat_id">
			<?php endif;?>
              <table border="0">
                <tbody>
                 <tr>
                    <td width="140px" class="textlabel"><?php echo _('Select Category')?></td>
                    <td colspan="2"><select class="select" type="select" id="categories_id" name="categories_id">
		                    <option value="-1">-- <?php echo _('Select Category')?> --</option>
							<?php if($category_data):?>
								<?php foreach($category_data as $category):?>
                       				<?php if($subcategory_data):?>
					    				<option value="<?php echo $category->id?>" <?php if(($form_data) && $form_data['categories_id']==$category->id): ?>selected="selected"<?php elseif($subcategory_data&&$category->id==$subcategory_data['0']->categories_id):?> selected="selected"<?php endif;?>><?php echo $category->name?></option>
									<?php else:?>
										<option value="<?php echo $category->id?>" <?php if(($form_data) && $form_data['categories_id']==$category->id): ?>selected="selected"<?php elseif($category->id==$this->input->post('categories_id')):?> selected="selected"<?php endif;?>><?php echo $category->name;?></option>
									<?php endif;?>
								<?php endforeach;?>
							<?php else:?>
								<option value="-1">--<?php echo _("No Category")?>--</option>
							<?php endif;?>	
                      </select></td>
                 </tr>
                 <tr>
                    <td class="textlabel"><?php echo _('Sub-Category Name')?></td>
                    <td colspan="2"><input type="text" class="text medium" size="30" id="subname" name="subname" <?php if(($form_data) && @$form_data['subname']):?>value="<?php  echo $form_data['subname'];?>"<?php elseif($subcategory_data):?>value="<?php echo $subcategory_data['0']->subname?>" <?php endif;?> onkeyup="check_subcategory()"></td>
				</tr>
				
				<tr>
					<td class="textlabel"><?php echo _('Image'); ?></td>
					
					<td colspan="2" style="padding-right:00px">
                   		<div id="uploaded_image"></div>
                   		<input type="hidden" id="x" name="x" />
				  		<input type="hidden" id="y" name="y" />
				  		<input type="hidden" id="w" name="w" />
				  		<input type="hidden" id="h" name="h" />
                   		<div><a href="javascript:;" class="thickboxed" ><input type="button" name="upload_image" id="upload_image" value="<?php echo _("Upload Image Here");?>" /></a></div>
                   	</td>
				</tr>
				
				<?php if($subcategory_data && $subcategory_data['0']->subimage):?>
              	<tr>
                	<td class="textlabel"><?php echo _('Current image')?></td>
                	<td style="padding-right:250px" id="current_cat_img">
                		<img src="<?php echo base_url(); ?><?php echo $subcategory_data['0']->subimage; ?>" height="100" width="100" />
						<input type="hidden" name="old_subimage" id="old_subimage" value="<?php echo $subcategory_data['0']->subimage; ?>" />
	                  	<a href="#" class="remove_image" rel="<?php echo $subcategory_data['0']->id;?>"><?php echo _('Remove'); ?></a>
                  		<!-- <img src="<?php echo base_url(''); ?>assets/cp/images/product/no_image.jpg" alt="<?php echo _('No image available.Please upload one.')?>"/> -->
               			<input class="rotated_image_hid" type="hidden" value="" name="rotated_image">
				        <input type="hidden" name="current_prod_img" id="current_prod_image" value="<?php echo basename($subcategory_data['0']->subimage);?>">
               		</td>
               		<td>
	               		<?php if(!empty($subcategory_data['0']->subimage)) { ?> 
	                		<!-- For not showing the rotate images if uploaded images not there -->
							<a href="javascript:;" class="pro_rotate_img" onClick="srotcw(this)" data-img1="<?php echo basename($subcategory_data['0']->subimage);?>" title="<?php echo _('Rotate image Clock-wise')?>">
							<img src="<?php echo base_url();?>/assets/cp/images/cw.png"></a>
							<a href="javascript:;" class="pro_rotate_img" onClick="srotacw(this)" data-img2="<?php echo basename($subcategory_data['0']->subimage);?>" title="<?php echo _('Rotate image Anti-clockwise')?>">
							<img src="<?php echo base_url();?>/assets/cp/images/acw.png">
							</a>
						<?php } ?>
	               	</td>
              	</tr>
              	<?php endif;?>
				              	
				<tr>
                    <td class="textlabel"><?php echo _('Description')?></td>
                    <td colspan="2"><textarea style="width:390px" rows="5" cols="50" type="textarea" id="subdescription" name="subdescription"><?php if(($form_data) && @$form_data['subdescription']): echo $form_data['subdescription']; elseif($subcategory_data):echo $subcategory_data['0']->subdescription; endif;?></textarea></td>

                </tr>

                  <tr> </tr>

                  <tr>

                    <td class="textlabel"><?php echo _('Popup')?></td>

                    <td colspan="2"><input type="checkbox"  class="checkbox" id="display_tool_tip" name="display_tool_tip" <?php if(($form_data) && @$form_data['display_tool_tip'] && @$form_data['display_tool_tip'] == '1'):?>checked="checked"  <?php elseif($subcategory_data&&$subcategory_data['0']->display_tool_tip=='1'):?> checked="checked"<?php endif;?>value="1">

                      &nbsp;&nbsp;&nbsp;<a title="<?php echo _('If you check this box, your customers will see this description when the mouse cursor over a subcategory link to (there is then a popup to appear).')?>" href="#" id="help-cat3"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a></td>

                  </tr>

                  <tr>

                    <td class="textlabel"><?php echo _('Add Text')?></td>

                    <td colspan="2"><input type="checkbox" onChange="javascript:show_hide();" value="1" class="checkbox" id="subaddtext" name="subaddtext"<?php if(($form_data) && @$form_data['subaddtext'] && @$form_data['subaddtext'] == '1'):?>checked="checked"  <?php elseif($subcategory_data&&$subcategory_data['0']->submessage):?> checked="checked"<?php endif;?>>
&nbsp;&nbsp;&nbsp;<a title="<?php echo _('If your clients have a subcategory clickable on this page a message at the top. This is a promotional text related to this subcategory. The message is below post.')?>" href="#" id="help-cat4"><img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png"></a></td>

				 </tr>

                 <tr>

                    <td class="textlabel"><span <?php if(($form_data) && @$form_data['subaddtext'] && @$form_data['subaddtext'] == '1'){?><?php }else{?>style="display:none"<?php }?> id="decision1"><?php echo _('Message')?></span></td>

                    <td colspan="2"><span <?php if(($form_data) && @$form_data['subaddtext'] && @$form_data['subaddtext'] == '1'){?><?php }else{?>style="display:none"<?php }?> id="decision2">

                      <textarea style="width:390px" rows="5" cols="50" type="textarea" id="submessage" name="submessage"><?php if(($form_data) && @$form_data['submessage']): echo $form_data['submessage']; elseif($subcategory_data):echo $subcategory_data['0']->submessage; endif;?></textarea>
						</span>	  
				</td>

                  </tr>

                  <tr>

                    <td class="save_b" colspan="3"><input type="submit" value="<?php if($subcategory_data):echo _('UPDATE');else:echo _('ADD');endif;?>" class="submit" id="<?php if($subcategory_data):echo _('UPDATE');else:echo _('ADD');endif;?>" name="submit" onsubmit="check()">

                      <input type="hidden" value="add_edit" id="act" name="act">

                      <input type="hidden" value="" id="id" name="id"></td>

                  </tr>

                </tbody>

              </table>

            </form>
            <script type="text/javascript">
                function check(){
                      
                        $.post("<?php echo base_url()?>cp/subcategories/check_subcategory",
                            {'categories_id':document.getElementById('categories_id').value,
                             'subname':document.getElementById('subname').value},
                            function(data){ 
                                            if(data.trim()=="exist"){
                                              alert("SUBCATEGORY already exist !");
                                              return false;
                              }
                        });
                      }
                      
            </script>

            <script type="text/javascript" language="javascript">

				var frmValidator = new Validator("frm_subcategories_addedit");
        
				frmValidator.EnableMsgsTogether(validate_mess);

				
				frmValidator.addValidation("categories_id","dontselect=-1","<?php echo _('Select A category')?>");

				frmValidator.addValidation("subname","req","<?php echo _('Please give a name')?>");

				//frmValidator.addValidation("subdescription","req","<?php echo _('Please give a description please')?>");

				/* START : show / hide message box */ 

				function show_hide(){

				var add_text = document.getElementById('subaddtext').checked;

					if(add_text == true){

						document.getElementById('decision1').style.display = 'block';

						document.getElementById('decision2').style.display = 'block';

					}else if(add_text == false){

						document.getElementById('decision1').style.display = 'none';

						document.getElementById('decision2').style.display = 'none';
					}
				}
				function validate_mess(result){
				
					if(result == true){
						var isChecked = document.getElementById('subaddtext').checked;
						
						if(isChecked == true){
							var text = tinyMCE.get('submessage').getContent();
							//var text = document.getElementById('submessage').value;
							//alert(text);
							if(text == ""){								
								alert("<?php echo _('Please leave a message')?>");
								return false;
							}
							return true;
						}else{
							return true;
						}
					}
					return false;
				}

				function check_subcategory(){
				
					$.post("<?php echo base_url()?>cp/subcategories/check_subcategory",
							{'categories_id':document.getElementById('categories_id').value,
							 'subname':document.getElementById('subname').value},
							function(data){	
                            	if(data.trim()=="exist"){
									jQuery("#error").css({'display':'block'});
									//jQuery("#subname").val('');
									jQuery("#subname").focus();
                  alert("Category already exist");
                  $('#subname').val("");
									jQuery("#error").css({'display':'none'});
								}
					});
				}
				/* END : show / hide message box */
				$(document).ready(function(){
					show_hide();
				});
				function srotcw(obj) {
					$.ajax({
							type:'POST',
							url: base_url+'cp/subcategories/rotate_uploaded_image',
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
							url: base_url+'cp/subcategories/rotate_uploaded_image',
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
