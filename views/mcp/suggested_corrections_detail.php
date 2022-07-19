<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.tipsy.js"></script>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/tipsy.css" type="text/css"/>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-theme.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

		<style type="text/css">
		
			.container {
				margin-bottom:20px;
				width:auto !important;
			}
			.tipsy { 
				font-size: 15px !important;
			}
			.changed{
				background-color: #FFC9C9;
			}
			#TB_footer {
				/*position: fixed;
				bottom:0;*/
				background-color:#F1F1F1;
				height:30px;
				text-align: center;
    			width: 100%;
    			padding-top: 5px;
			}
			#TB_footer a{
			    text-decoration:underline !important;
			    margin:10px;
			    
			}
			#TB_footer a:hover{
			    text-decoration:none !important;
			    
			}
			.wrap{
				margin-bottom: 10px;
			}
			
			.containers {
				text-align: center;
			}
			.form-horizontal .control-label {
				text-align:left !important;
			}
		</style>
		
		<script type="text/javascript">
		$(document).ready(function(){
			jQuery(".tool_s").tipsy({gravity: 's'});
			jQuery(".tool_w").tipsy({gravity: 'w'});
		});
		
		</script>
		
	</head>
	
	<body>
		<div class="container">
		<!-- start of main body -->
			<div id="show_company_<?php echo $suggestion['id'];?>" style="padding: 20px 0px;">
			
		  		<form class="form-horizontal" action="<?php echo base_url(); ?>mcp/dashboard/approve_suggestion"  method="post" id="frm_suggestion_add" name="frm_suggestion_add">
			  		<div class="wrap">
			        	<div class="form-group">
						    <label for="company_name" class="col-xs-3 control-label"><?php echo _("Company Name");?></label>
						    <div class="col-xs-4">
						      <input type="text" size="30" class="form-control input tool_w <?php if($suggestion['company_name'] != $companyInfo->company_name){ echo "changed"; }?>" id="company_name" name="company_name" value="<?php echo $suggestion['company_name'];?>" title="<?php echo $companyInfo->company_name;?>">
						    </div>
						</div>
						
						<div class="form-group">
						    <label for="address" class="col-xs-3 control-label"><?php echo _("Address"); ?>+ nr&nbsp;</label>
						    <div class="col-xs-4">
						      <input type="text" size="30" class="form-control input tool_w <?php if($suggestion['address'] != $companyInfo->address){ echo "changed"; }?>" id="address" name="address" value="<?php echo $suggestion['address'];?>" title="<?php echo $companyInfo->address;?>">
						    </div>
						</div>
						
						<div class="form-group">
						    <label for="zipcode" class="col-xs-3 control-label"><?php echo _("Postcode"); ?></label>
						    <div class="col-xs-4">
						      <input type="text" size="30" class="form-control input tool_w <?php if($suggestion['zipcode'] != $companyInfo->zipcode){ echo "changed"; }?>" id="zipcode" name="zipcode" value="<?php echo $suggestion['zipcode'];?>" title="<?php echo $companyInfo->zipcode;?>">
						    </div>
						</div>
						
						<div class="form-group">
						    <label for="city" class="col-xs-3 control-label"><?php echo _("City");?><b class="red_star">&nbsp;</b></label>
						    <div class="col-xs-4">
						      <input type="text" size="30" class="form-control input tool_w <?php if($suggestion['city'] != $companyInfo->city){ echo "changed"; }?>" id="city" name="city" value="<?php echo $suggestion['city'];?>" title="<?php echo $companyInfo->city;?>">
						    </div>
						</div>
						
						<div class="form-group">
						    <label for="website" class="col-xs-3 control-label"><?php echo _("Website"); ?></label>
						    <div class="col-xs-4">
						      <input type="text" size="30" class="form-control input tool_w <?php if($suggestion['website'] != $companyInfo->website){ echo "changed"; }?>" id="website" name="website" value="<?php echo $suggestion['website'];?>" title="<?php echo $companyInfo->website;?>">
						    </div>
						</div>
						
						<div class="form-group">
						    <label for="description" class="col-xs-3 control-label"><?php echo _("Description");?></label>
						    <div class="col-xs-4">
						      <textarea row="4" cols="50" name="description" id="description" class="form-control tool_w" title="<?php echo $companyInfo->company_desc;?>"><?php echo $suggestion['description'];?></textarea>
						    </div>
						</div>
			            <div style="clear:both;"></div>
			            
			            <span class="sbig" style="display:block; margin-bottom:15px; color:#6F9300; font-size:20px;"><b><?php echo _("Opening Hours"); ?></b></span>
			            
			            <?php $opening_hours = json_decode($suggestion['opening_hours'],true);?>
			            <div id="opening-hours" class="container" style="margin-bottom: 20px;">
			              <?php if(!empty($days)) { foreach($days as $d) { ?>
	
			            	<div class="row" style="margin-bottom: 10px;">
			              
			              		<label class="col-xs-1 control-label" style="width: 90px; padding-right: 0px;"><strong><?php echo _($d->name); ?></strong></label>
							  	<div class="col-xs-2" style="padding-right: 5px;">
							  
									<select name="time_1[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_1')" class="form-control <?php echo $d->id; ?>_1 tool_s <?php if($openingHours[($d->id - 1)]->time_1 != $opening_hours[$d->id]['time_1'] ){ echo "changed";}?>" title="<?php echo $openingHours[($d->id - 1)]->time_1;?>">
									
					                	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("Closed"); ?></option>
										<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("None"); ?></option>
					                    <?php for($i=0;$i<=23;$i++) { ?>
					                    <?php for($j=0;$j<=30;$j=$j+30) { ?>
					                    <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
					                    <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
					                    <?php } ?>
					                    <?php } ?>
					                    
					               </select>
				                 
							  	</div>
							  
							  
							  	<label class="col-xs-1 control-label" style="padding-right: 0px; padding-left: 0px; width: 15px; margin-left: 5px;"><?php echo _('to'); ?></label>
							  	<div class="col-xs-2" style="padding-right: 5px;">
							  		
							    	<select name="time_2[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_1')" class="form-control <?php echo $d->id; ?>_1 tool_s <?php if($openingHours[($d->id - 1)]->time_2 != $opening_hours[$d->id]['time_2'] ){ echo "changed";}?>" title="<?php echo $openingHours[($d->id - 1)]->time_2;?>">
							    	
					                	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("Closed"); ?></option>
										<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("None"); ?></option>
					                    <?php for($i=0;$i<=23;$i++) { ?>
					                    <?php for($j=0;$j<=30;$j=$j+30) { ?>
					                    <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
					                    <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
					                    <?php } ?>
					                    <?php } ?>
					                    
					                </select>
							  	</div>
							  
							  	<label class="col-xs-1 control-label" style="padding-right: 0px; padding-left: 0px; width: 25px; margin-left: 5px;"><?php echo _('and'); ?></label>
							  	<div class="col-xs-2" style="padding-right: 5px;">
							  	
							    	<select name="time_3[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_2')" class="form-control <?php echo $d->id; ?>_2 tool_s <?php if($openingHours[($d->id - 1)]->time_3 != $opening_hours[$d->id]['time_3'] ){ echo "changed";}?>" title="<?php echo $openingHours[($d->id - 1)]->time_3;?>">
							    	
					                	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("Closed"); ?></option>
										<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("None"); ?></option>
					                    <?php for($i=0;$i<=23;$i++) { ?>
					                    <?php for($j=0;$j<=30;$j=$j+30) { ?>
					                    <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
					                    <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
					                    <?php } ?>
					                    <?php } ?>
					                    
						            </select>
							  	</div>
							  
							  <label class="col-xs-1 control-label" style="padding-right: 0px; padding-left: 0px; width: 25px; margin-left: 5px;"><?php echo _('to'); ?></label>
							  <div class="col-xs-2" style="padding-right: 5px; ">
							    <select name="time_4[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_2')" class="form-control <?php echo $d->id; ?>_2 tool_s <?php if($openingHours[($d->id - 1)]->time_4 != $opening_hours[$d->id]['time_4'] ){ echo "changed";}?>" title="<?php echo $openingHours[($d->id - 1)]->time_4;?>">
				                	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("Closed"); ?></option>
									<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("None"); ?></option>
				                    <?php for($i=0;$i<=23;$i++) { ?>
				                    <?php for($j=0;$j<=30;$j=$j+30) { ?>
				                    <?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
				                    <option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
				                    <?php } ?>
				                    <?php } ?>
				                </select>
							  </div>
	
						  </div>
			              <?php } } ?>
			            </div>
	
			            <div class="form-group">
						    <label for="subject" class="col-xs-3 control-label"><?php echo  _("Other Subject"); ?></label>
						    <div class="col-xs-4">
						      	<select name="subject" id="subject" class="form-control input">
						      		<option <?php if($suggestion['subject'] == 'selecteer'){?>selected="selected"<?php }?>><?php echo _('Select')?></option>
			              			<option <?php if($suggestion['subject'] == 'failliet'){?>selected="selected"<?php }?>><?php echo _('Winkel bestaat niet meer')?></option>
			              			<option <?php if($suggestion['subject'] == 'overige'){?>selected="selected"<?php }?>><?php echo _('Overige')?></option>
				            	</select>
						    </div>
						</div>
	
						<div class="form-group">
						    <label for="remark" class="col-xs-3 control-label"><?php echo _("Remark"); ?></label>
						    <div class="col-xs-6">
						      	<textarea class="form-control " row="4" cols="50" name="remark" id="remark"><?php echo $suggestion['remark'];?></textarea>
						    </div>
						</div>
						
						<div class="containers">
							<input type="hidden" value="<?php echo $suggestion['id'];?>" id="suggestion_id" name="suggestion_id">
			            	<input type="submit" class="btn btn-default send2" value="<?php echo _('Approve');?>" id="submit" name="submit">
			        	</div>
			        </div>
		        </form>				
			</div>
		</div>
		
		<div id="TB_footer">
        	<a href="<?php echo base_url();?>mcp/dashboard/disapprove_suggestion/<?php echo $suggestion['id'];?>"><?php echo _("Disapprove");?></a>
        	<a href="<?php echo base_url();?>mcp/dashboard/block_ip/<?php echo $suggestion['id'];?>"><?php echo _("Block IP");?></a>
        	<a href="<?php echo base_url();?>mcp/dashboard/delete_company/<?php echo $suggestion['id'];?>"><?php echo _("Delete Company");?></a>
        </div>
	</body>
</html>