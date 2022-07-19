<script type="text/javascript">
var remove_image = "<?php echo _( 'Do you want to remove the image ?' ); ?>"
  var jcrop_api,boundx,boundy,xsize,ysize,$preview,$pcnt,$pimg;

    function updateCoords(c){
      $('#x').val(c.x);
      $('#y').val(c.y);
      $('#w').val(c.w);
      $('#h').val(c.h);
    }

    function checkCoords(){
      if (parseInt($('#w').val())) return true;
      alert("<?php echo _('Please select a crop region then press submit.');?>");
      return false;
    }

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
    }

    function crop(i){
	    if(i == 1){
	      $("#uploaded_img").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
	    }
	    else{
	      $("#uploaded_image").append('<img src="<?php echo base_url();?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping");?>...."/>');
	    }
	    
	    $.ajax({
	      url : base_url+'cp/image_upload/crop_image',
	      data : {'image_name': $("#image_name").val(), 'x': $("#x").val(), 'y': $("#y").val(), 'w': $("#w").val(), 'h': $("#h").val()},
	      type: 'POST',
	      success: function(response){
	        if(i == 1){
	          $("#uploaded_img").html(response);
	          $("#uploaded_img").focus();
	        }
	        else{
	          $("#uploaded_image").html(response);
	          $("#uploaded_image").focus();
	        }
	      }
	    });
  	}
</script>
<style>
#label_extra_logo_set{
	display: inline-flex;
}
#label_extra_logo_set > input{
  	margin-right: 25px;
  	margin-top: 5px;
}
</style>
<div class="boxed">
      			<h3 id="labeler"> <?php echo _('Labeler')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       				
			       				 <?php  if ($this->company->ac_type_id == 2 || $this->company->ac_type_id == 3) { ?>
			       				 <tr>
			       					<td class="textlabel"></td>
			                		<td style="padding-right:250px"><b>== <?php echo _('The setting below will printed out in FoodDESK labelprinter');?> ==</b></td>
			              		</tr>
			              		
			              		<tr>
			              			<?php 
			              			if(isset($product_labeler) && !empty($product_labeler)){
										if((!empty($product_labeler[0]->duedate)) && ($product_labeler[0]->duedate != 0)){
			              					$datediff	= $product_labeler[0]->duedate;
			              				}
			              			}
			              			?>
			                		<td class="textlabel"><?php echo _('Duedate')?></td>
			                		<td style="padding-right:250px"><?php echo '+'?><input type="text" class="text medium" style="width:60px;" id="duedate" name="duedate" value="<?php if(isset($datediff)){echo $datediff;}?>">&nbsp;<?php echo _('days from today so on the label due date will be shown as')?>&nbsp;<b id="changeddate"><?php if(isset($datediff)){echo date('d/m/Y', strtotime('+'.$datediff.' days'));}?></b></td>
			              		</tr>
			              		
			              		<tr>
			                		<td class="textlabel"><?php echo _('Duedate Type')?></td>
			                		<td style="padding-right:250px">
			                			<select id="duedate_type" name="duedate_type" style="margin-left: 0px;">
								  			<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->duedate_type == 'THT'):?>selected="selected"<?php endif;?> value="THT"><?php echo _("THT");?></option>
						  		 		 	<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->duedate_type == 'TGT'):?>selected="selected"<?php endif;?> value="TGT"><?php echo _("TGT");?></option>
						 				</select>
			                		</td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel"><?php echo _('Conserve between')?></td>
			                		<td style="padding-right:250px"><input type="text" class="text medium"  style="width:60px;" id="conserve_min" name="conserve_min" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->conserve_min;}?>">&nbsp;<?php echo _('and')?>&nbsp;<input type="text" class="text medium" style="width:60px;" id="conserve_max" name="conserve_max" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->conserve_max;}?>">&nbsp;<?php echo '&deg;C'?></td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel"><?php echo _('Weight')?></td>
			                		<td style="padding-right:250px">
			                			<input type="text" class="text medium" style="width:60px; display:inline-block;" id="weight" name="weight" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->weight;}?>">&nbsp;
			                			<select id="weight_unit" name="weight_unit" style="margin-left: 0px; display:inline-block;">
								  			<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->weight_unit == 'kg'):?>selected="selected"<?php endif;?> value="kg"><?php echo _("kg");?></option>
						  		 		 	<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->weight_unit == 'gr'):?>selected="selected"<?php endif;?> value="gr"><?php echo _("gr");?></option>
						  		 		 	<option <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->weight_unit == 'l'):?>selected="selected"<?php endif;?> value="l"><?php echo _("l");?></option>
						 				</select>
			                		</td>
			              		</tr>
			              		<tr>
			                		<td class="textlabel">
			                			<?php echo _('Barcode')?>
										<a id="help-prodl0" href="#" title="<?php echo _('Select if you need to show barcode on label');?>">
											<img width="16" height="16" src="<?php echo base_url()?>assets/cp/images/help.png">
										</a>
									</td>
			                		<td style="padding-right:250px"><input type="checkbox" id="show_bcode" name="show_bcode" value="1" <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->show_bcode == 1){?>checked="checked"<?php }?>></td>
			              		</tr>
			              		<tr>
					            	<td class="textlabel">
					              		<?php echo _("Extra Notification");?>:
					              	</td>
				              		<td>
				              		<?php $extra = "0"; if(isset($product_labeler)){if(!empty($product_labeler)){$extra = $product_labeler[0]->extra_notification;}}?>
				              			<select name="extra_noti" id="extra_noti">
				              				<option value="0"><?php echo _("select a phrase")?></option>
					              			<option value="1" <?php if($extra == "1"){echo 'selected="selected"';}?>><?php echo _("verpakt onder beschermende atmosfeer");?></option>
											<option value="2" <?php if($extra == "2"){echo 'selected="selected"';}?>><?php echo _("met zoetstoffen");?></option>
											<option value="3" <?php if($extra == "3"){echo 'selected="selected"';}?>><?php echo _("met suiker(s) en zoetstof(fen)");?></option>
											<option value="4" <?php if($extra == "4"){echo 'selected="selected"';}?>><?php echo _("bevat aspartaam (een bron van fenylalanine)");?></option>
											<option value="5" <?php if($extra == "5"){echo 'selected="selected"';}?>><?php echo _("bevat een bron van fenylalanine");?></option>
											<option value="6" <?php if($extra == "6"){echo 'selected="selected"';}?>><?php echo _("overmatig gebruik kan een laxerend effect hebben");?></option>
											<option value="7" <?php if($extra == "7"){echo 'selected="selected"';}?>><?php echo _("bevat zoethout — mensen met hoge bloeddruk dienen overmatig gebruik te vermijden");?></option>
											<option value="8" <?php if($extra == "8"){echo 'selected="selected"';}?>><?php echo _("Hoog cafeïnegehalte. Niet aanbevolen voor kinderen en vrouwen die zwanger zijn of borstvoeding geven");?></option>
											<option value="9" <?php if($extra == "9"){echo 'selected="selected"';}?>><?php echo _("Bevat cafeïne. Niet aanbevolen voor kinderen en zwangere vrouwen");?></option>
											<option value="10" <?php if($extra == "10"){echo 'selected="selected"';}?>><?php echo _("met toegevoegde plantensterolen");?></option>
											<option value="11" <?php if($extra == "11"){echo 'selected="selected"';}?>><?php echo _("levensmiddel uit voedingsoogpunt mogelijk niet geschikt is voor zwangere en borstvoedende vrouwen en kinderen jonger dan vijf jaar");?></option>
											<option value="12" <?php if($extra == "12"){echo 'selected="selected"';}?>><?php echo _("Bewaren in een koel constante (12c° à 18C°) droge (max 70%) ruimte");?></option>
											<option value="13" <?php if($extra == "13"){echo 'selected="selected"';}?>><?php echo _("ambachtelijk product");?></option>
										</select>
				              		</td>
			              		</tr>
			              		<tr>
					            	<td class="textlabel">
					              		<?php echo _("Extra Notification(free field)");?>:
					              	</td>
				              		<td>
				              			<input type="text" class="text medium" style="width:100%;display:inline-block;" id="extra_field" name="extra_field" value="<?php if(isset($product_labeler) && !empty($product_labeler)){echo $product_labeler[0]->extra_notification_free_field;}?>">&nbsp;
				              			<div class="show_checkbox">
											<input id="xtraCheckbox" type="checkbox" <?php if(isset($product_labeler[0]->extra_notification_free_field) && !empty($product_labeler[0]->extra_notification_free_field)){ ?> checked="checked" <?php } ?> ></input>
											<span><?php echo _("Show it also on the data sheet");?></span>
				              			</div>

				              		</td>
				              		
				              		
			              		</tr>
			              		
			              		<tr>
		                  			<td class="textlabel" width="22%" valign="top"><?php echo _('Extra Logo')?></td>
		                  			<td style="padding-right:250px">
		                  				<div id="label_extra_logo_set">
			                  				<input type="checkbox" id="labeler_logo_status" name="labeler_logo_status" value="1" <?php if(isset($product_labeler) && !empty($product_labeler) && $product_labeler[0]->extra_logo_status == 1){?>checked="checked"<?php }?>>
			                  			  	<div id="uploaded_image4"></div>
					                            <input type="hidden" id="x" name="x" />
							                    <input type="hidden" id="y" name="y" />
							                    <input type="hidden" id="w" name="w" />
							                    <input type="hidden" id="h" name="h" />
				                            <div>
			                              		<a href="javascript:;" class=thickboxed_label attr_id="4" style="text-decoration: none;"><input type="button" name="upload_img" id="upload_img" value="<?php echo _("Image upload");?>" /></a>
			                            	</div>
			                            </div>
		                  			</td>
		                      		</tr>
		                      	<?php if($product_labeler && !empty($product_labeler[0]->extra_logo_image)){?>
	                        	<tr>
		                         	 <td>&nbsp;</td>
		                         	 <td>
		                            	<img alt="labeler logo" src="<?php echo base_url().'assets/cp/labeler_logo_extra/'.$product_labeler[0]->extra_logo_image?>" style="height:150px">
		                            	<a href="javascript:void(0);"  style="padding-left: 16px;" class="remove_labeler_logo" data-image_name="<?php echo $product_labeler[0]->extra_logo_image; ?>">
		                            		<span> <?php echo _('Remove Image')?> </span>
		                            	</a>
		                          	</td>
	                       		</tr>
                      <?php }
                      }else{  ?>
                      	 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>

                      <?php } ?> 
			       			</tbody>
			       		</table>	
        			</div>
        			<div class="sub_div">
			    		<div class="sub__div" colspan="2">
        				<?php if($product_information):?>
        					 <?php  if ($this->company->ac_type_id == 2 || $this->company->ac_type_id == 3) { ?>
				        		 <input type="button" value="<?php echo _("Update");?>" class="submit" id="labeler_update" name="labeler_update">
				        		<input type="hidden" value="add_edit" id="labeler_act" name="labeler_act">
				    			<input type="hidden" value="update" id="labeler_add_update" name="labeler_add_update"> 
				    			<?php } ?>
						<?php else:?>
							 <?php  if ($this->company->ac_type_id == 2 || $this->company->ac_type_id == 3) { ?>
				        		 <input type="button" value="<?php echo _('Send')?>" class="submit" id="labeler_add" name="labeler_add">
				      			<input type="hidden" value="add" id="labeler_add_update" name="labeler_add_update">
				      			<?php } ?>
						<?php endif;?>
					</div>
					</div>
        		</div>
        	</div>
        	<script type="text/javascript">
				jQuery(document).ready(function(){
					$("#duedate").keypress(function (e) {
						if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					        return false;
					    }
					});
					$("#conserve_min, #conserve_max").keypress(function (e) {
					    if ((e.which != 45 || $(this).val().indexOf('-') != -1) && e.which != 0 && e.which != 8 && (e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
							return false;
					    }
					});
					$("#duedate").change(function(){
						var date	= new Date(),
							days	= parseInt(this.value.replace(/[^0-9\.]/g,''));
						if(!isNaN(date.getTime())){
				            date.setDate(date.getDate() + days);
				            $("#changeddate").text(date.toInputFormat());
						};
					});

					Date.prototype.toInputFormat = function() {
					       var yyyy = this.getFullYear().toString();
					       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
					       var dd  = this.getDate().toString();
					       return (dd[1]?dd:"0"+dd[0]) + "/" + (mm[1]?mm:"0"+mm[0]) + "/" + yyyy; // padding
					};
				});
        	</script>
        	