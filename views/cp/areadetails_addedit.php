<!-- MAIN -->
<div id="main">
	<div id="main-header">
		<h2><?php echo _('Edit Region Details')?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url();?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url() ?>cp/cdashboard/settings"><?php echo _('Settings')?></a> &raquo; <?php echo _('Add  Area Details')?></span>
	</div>
	<?php $messages = $this->messages->get() ?>
	<?php if($messages != array()):?>
	<?php foreach($messages as $key => $val):?>
	<?php foreach($val as $v):?>
		<div  class = "<?php echo $key;?>"><strong><?php echo $key;?> : </strong><?php echo $v;?></div>
	<?php endforeach;?>	
	<?php endforeach;?>
    <?php endif;?>
	<div id="content">
		<div id="content-container">
        	<div class="box">
				<h3><?php echo _('Edit State Details');?></h3>
          		<div class="table">
            		<form action="<?php echo base_url()?>cp/cdashboard/areadetails_addedit" enctype="multipart/form-data" method="post" id="frm_areadetails_addedit" name="frm_areadetails_addedit">
              			<input type="hidden" value="areadetails_addedit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">              
              			<table border="0">
                			<tbody>
                  				<tr>
									<td class="textlabel"><?php echo _('State')?></td>
                    				<td style="padding-right:250px"><select class="select" type="select" id="delivery_areas_id" name="delivery_areas_id">
                      					<option value="-1">--<?php echo _('select')?>--</option>
                    					<?php if($delivery_areas):?>
					  					<?php foreach($delivery_areas as $delivery_area):?>  
										<option value="<?php echo $delivery_area->id?>" <?php if($delivery_area_details&&$delivery_area->id==$delivery_area_details['0']->delivery_areas_id):?>selected=""<?php endif;?>><?php echo $delivery_area->area_name?></option>
                        				<?php endforeach;?>
										<?php else:?>
										<option value="-10">-<?php echo _('no delivery area available')?></option>
										<?php endif;?>	
                      				</select></td>
                  				</tr>
								<tr>
                    				<td class="textlabel"><?php echo _('City')?></td>
                    				<td style="padding-right:250px"><input type="text" class="text short" size="30" id="city_name" name="city_name" <?php if($delivery_area_details):?>value="<?php echo $delivery_area_details['0']->city_name?>"<?php endif;?>></td>
                  				</tr>
                  				<tr>
                    				<td class="textlabel"><?php echo _('Zip Code')?></td>
                    				<td style="padding-right:250px"><input type="text" class="text short" size="30" id="zipcode" name="zipcode" <?php if($delivery_area_details):?>value="<?php echo $delivery_area_details['0']->zipcode?>"<?php endif;?>></td>
                 				</tr>

                 				<tr>
                    				<td class="textlabel"><?php echo _('Costs')?></td>
									<td style="padding-right:250px"><input type="text"  class="text short" size="30" id="cost" name="cost" <?php if($delivery_area_details):?>value="<?php echo $delivery_area_details['0']->cost?>"<?php endif;?>>
              &nbsp;&euro;
              						</td>
                  				</tr>
                  				<tr>
                    				<td class="textlabel"><?php echo _('Time Range')?></td>
                    				<td style="padding-right:250px"><input type="text" onBlur="close_tip();" onFocus="open_tip();" class="text short" size="30" id="timerange" name="timerange" <?php if($delivery_area_details):?>value="<?php echo $delivery_area_details['0']->timerange?>"<?php endif;?>>
                      					<span style="width:250px" class="myTooltip" id="time-range"><?php echo _('Enter time in format');?> 00:00:00 Eg: 23:12:00</span>
                      				</td>

                  				</tr>
                  				<?php if($delivery_area_details):?>
				  				<tr>
                    				<td style="padding-left:20px" colspan="2"><input type="submit" value="UPDATE " class="submit" id="update" name="update">
                      					<input type="hidden" value="add_edit" id="act" name="act">
                      					<input type="hidden" value="<?php echo  $delivery_area_details['0']->id?>" id="id" name="id"></td>
                  				</tr>
				  				<?php else:?>
				  				<tr>
				  	 				<td style="padding-left:20px" colspan="2"><input type="submit" value="ADD " class="submit" id="add" name="add"></td>
				  				<?php endif;?>
				  				</tr>
                			</tbody>
              			</table>
            		</form>

            		<script type="text/javascript" language="javascript">
						var frmValidator = new Validator("frm_areadetails_addedit");
						frmValidator.EnableMsgsTogether();
						frmValidator.addValidation("delivery_areas_id","dontselect=-1","<?php echo _('Please Select Area');?>");
						frmValidator.addValidation("city_name","req","<?php echo _('Please enter city name');?>");
						frmValidator.addValidation("zipcode","req","<?php echo _('Please enter Zipcode');?>");
						frmValidator.addValidation("cost","req","<?php echo _('Please enter Cost');?>");
						frmValidator.addValidation("timerange","req","<?php echo _('Please enter Time Range');?>");
					</script>
          		</div><!--/table -->
        	</div><!-- /box -->
      	</div><!-- /content-container -->
    </div><!-- /content -->
