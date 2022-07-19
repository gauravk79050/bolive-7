<!--  AUTO-SUGGEST : START -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.widget.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js"></script>
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.autocomplete.js"></script>

<script>
	$(function() {

		var availableCompanies = new Array();
		
		<?php $count = 0; foreach($all_companies as $companies){?>
		availableCompanies['<?php echo $count; ?>'] = '<?php echo addslashes($companies->company_name); ?>';
	   	<?php $count++; }?>
	   	
		$( "#company_name" ).autocomplete({
			source: availableCompanies
		});
	});

	function show_hide_oh(select,value,class_name){
		if(value == "CLOSED"){
			$("#opening-hours").find("tr:eq("+(select-1)+")").find("td").hide();
			$("#opening-hours").find("tr:eq("+(select-1)+")").find("td").find("select").val("CLOSED");
			$("#opening-hours").find("tr:eq("+(select-1)+")").find("td:eq(0)").show();
		}else{
			$("#opening-hours").find("tr:eq("+(select-1)+")").find("td").show();
		}

		if(value == "0"){
			$("."+class_name).val("0");
			if( $("."+select+"_1").val() == "0" && $("."+select+"_2").val() == "0"){
				$("#opening-hours").find("tr:eq("+(select-1)+")").find("td").hide();
				$("#opening-hours").find("tr:eq("+(select-1)+")").find("td").find("select").val("CLOSED");
				$("#opening-hours").find("tr:eq("+(select-1)+")").find("td:eq(0)").show();
			}
		}
		
	}
	
</script>
<!--  AUTO-SUGGEST : END -->

<style type="text/css">
.textbox {
	width: 200px;
}

#dataRecord {
	padding-top: 20px;
	padding-bottom: 20px;
}

#dataRecord_length,#dataRecord_info {
	text-align: start;
}
.clear{
	clear:both;
}
.form_panel{
	/*width:72%;*/
}
.form_panel .form_panel_row{
	/* height: 31px; */
	height: auto;
	margin: 10px;
}
.form_panel_row .form_label{
	float:left;
	width:140px;
}
.form_panel_row .red_star{
	/*float:left;*/
}
.form_panel_row .textbox{
	float:left;
}
.form_panel_row_space{
	height:10px;
}
.form_panel_msg{
	width:100%;
}
.form_panel_msg .form_panel_row_msg{
	
}
.form_panel_msg .form_panel_row_msg .left_label_box{
	float:left;
	width: 13%;
}
.form_panel_msg .form_panel_row_msg .right_text_box{
	float: left;
    margin-left: 10px;
    width: 86%;
}
.form_panel_msg .form_panel_row_msg .right_text_box p{
	margin:0;
	padding:0;
}
.form_panel_sch{
	width:100%;
}
.form_panel_row_sch{
	
}
.form_panel_row_sch .left_label_box{
	float:left;
	width: 13%;
	
}
.form_panel_row_sch .left_label_box .form_label{
}
				
.form_panel_row_sch .right_text_box_sch{
	float: left;
    margin-left: 6px;
    width: 48%;
}
#opening-hours{
	width:100%;
}
.ui-autocomplete{
	font-size: 11px;
	max-height: 100px;
	overflow-y: auto;
}
span.error{
	float:left;
	margin-left:10px;
	color:#ff0000;
	font-size:12px;
}
#opening-hours th span{
	 background-color: #E0ECFF;
    display: block;
    font-family: arial;
    font-size: 12px;
    margin-right: 10px;
    padding: 5px 10px;
    text-align: left;
}


</style>

<script>
jQuery(document).ready(function($){

		$("#zipcode").keyup(function(){
			if($("#zipcode").val() != ''){
				$.ajax({
				    url:'<?php echo base_url();?>rp/reseller/fetching_city',
				    data:{postCode:$("#zipcode").val()},
				    type:'POST',
				    dataType:'json',
			    	success:function(response)
			    		{
			    			if(!response.error){
			    				$("#city").val(response.data.replace(/\\/g, ''));
				    		}else{
				    			$("#city").val("");
					    	}
						}
				});
			}else{
				$("#city").val("");				
			}
		});

		$("#existing_order_page").blur(function(){
			if($("#existing_order_page").val() != ''){
				$.ajax({
				    url:'<?php echo base_url();?>rp/reseller/checking_competitor_url',
				    data:{c_url:$("#existing_order_page").val()},
				    type:'POST',
				    dataType:'json',
			    	success:function(response)
			    		{
			    			if(response.error){
			    				$("#url_found").html(response.data);
			    				$("#existing_order_page").val('');
				    		}else{
				    			$("#url_found").html("");
					    	}
						}
				});
			}else{
				$("#url_found").html("");				
			}
		});
		
	   $("#search").keyup(function(){
		
    	});
});

function generate_passwod(){
	var random = Math.floor(Math.random()*100000000 + Math.random()*10);
	jQuery("#password").val(random);
}
</script>



<!-- start of main body -->

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td valign="top" align="center">
				<table width="98%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td valign="top" align="center" style="border: #8F8F8F 1px solid">
								<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tbody>
										<tr>
											<td align="center" style="padding: 15px 0px 5px 0px">
												<table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url('');?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
													<tbody>
														<tr>
															<td width="94%" align="left"><h3><?php echo _('Add Company'); ?></h3></td>
															<td width="3%" align="right"></td>
															<td width="3%" align="left">
																<div class="icon_button">
																	<img width="16" height="16" border="0"
																		style="cursor: pointer"
																		onClick="javascript:history.back();" title="Go Back"
																		alt="Go Back"
																		src="<?php echo base_url('');?>assets/mcp/images/undo.jpg">
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>

										<tr>
											<td align="center" style="padding: 15px 0px 5px 0px">
												<table width="98%" cellspacing="0" cellpadding="0"
													border="0" class="page_caption">
													<tbody>
														<tr>
															<td width="94%" align="left"><h3><?php echo _('List Of Companies'); ?></h3></td>
															<td width="3%" align="right"></td>
															<td width="3%" align="left">
																<div class="icon_button">
																	<img width="16" height="16" border="0"
																		style="cursor: pointer"
																		onClick="javascript:show_co_detail();" class="fold"
																		title="down" alt="down"
																		src="<?php echo base_url('');?>assets/mcp/images/arrow-down.gif">
																</div>
															</td>
														</tr>
														<tr>
															<td colspan="3">
																<div id="search" style="display: none">
																	<table border="0" width="100%" id="dataRecord"
																		class="display">
																		<thead>
																			<tr>
																				<th width="30%"><?php echo _('company_name');?></th>
																				<th width="40%"><?php echo _('address');?></th>
																				<th width="20%"><?php echo _('zipcode');?></th>
																				<th width="10%"><?php echo _('Action');?></th>
																				<!-- <th width="10%">&nbsp;</th> -->
																			</tr>
																		</thead>
																		<tbody>
																	   	<?php foreach($all_companies as $companies){?>
																	  	<tr>
																				<td width="30%"><?php echo $companies->company_name;?></td>
																				<td width="40%"><?php echo $companies->address;?></td>
																				<td width="20%" align="center"><?php echo $companies->zipcode;?></td>
																				<td width="10%" align="center"><a
																					href="<?php echo base_url();?>rp/reseller/edit_manage_companies/<?php echo $companies->id;?>"
																					alt="edit"><img
																						src="<?php echo base_url();?>assets/images/edit.gif"
																						width="20" height="20" /></a></td>
																				<!-- <td width="10%">&nbsp;</td> -->
																			</tr>
																	   	<?php }?>
																	   	</tbody>
																	</table>
																	<script type="text/javascript">
																	$(document).ready(function() {
																		$(".fold").click(function(){
																			$("#search").toggle();
																		});
																		$('#dataRecord').dataTable();
																	});
																	</script>
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>

										<tr>
											<td style="padding: 10px;"><?php echo validation_errors(); ?></td>
										</tr>

										<tr>
											<td width="100%" valign="top" align="center">
												<form name="frm_companies_add_update" id="frm_companies_add_update" method="post" enctype="multipart/form-data" action="<?php echo base_url().'rp/reseller/manage_companies'; ?>">
												  <input type="hidden" value="master_admin" id="registered_by" name="registered_by">
												  <table width="98%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #003366; text-align: left">
												    <tbody>
												      <tr>
												        <td height="20" bgcolor="#003366" align="left" colspan="5" class="whiteSmallBold" style="padding-left: 10px;"><?php echo _('Company Information'); ?></td>
												      </tr>
												      <tr>
												        <td height="10" colspan="5">&nbsp;</td>
												      </tr>
												      <tr>
												        <td height="10" align="center" colspan="5"><span id="dup_msg" style="color: #FF0000"></span></td>
												      </tr>
												      <tr>
												        <td width="10%">&nbsp;
												          <input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit"></td>
												        <td width="37%">
														  
														  	<div class="form_panel" >
												<!-- 				<div class="form_panel_row"> 
																	<span class="form_label"><?php echo _('ID'); ?></span>
												 					</div> -->
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Company Name'); ?> <b class="red_star">*</b></span>
																	<input type="text" value="<?php echo set_value('company_name'); ?>" name="company_name" id="company_name" class="textbox" size="30">
																	
																	<div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Company Type');?><b class="red_star">*</b></span>
																	<select name="type_id[]" id="type_id" multiple type="select" class="textbox" style="width: 204px">
												                    	<option value="-1">-- <?php echo _('Select Company Type'); ?> --</option>
												                    	<?php if(!empty($company_type)) { foreach($company_type as $ct) { ?>
																    	<option value="<?php echo $ct->id; ?>" <?php if(set_value('type_id') == $ct->id){ echo 'selected="selected"'; } ?> ><?php echo $ct->company_type_name; ?></option>
																    	<?php } } ?>
												                    </select>
																  
																  <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('First Name'); ?><b class="red_star">*</b></span>
												                	<input type="text" value="<?php echo set_value('first_name'); ?>" name="first_name" id="first_name" class="textbox required" size="30">
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Last Name');?><b class="red_star">*</b></span>
												                	<input type="text" value="<?php echo set_value('last_name'); ?>" name="last_name" id="last_name" class="textbox required" size="30">
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Email');?><b class="red_star">*</b></span>
												                	<input type="text" value="<?php echo set_value('email'); ?>" name="email" id="email" class="textbox required" size="30" onchange="check_email(this.value);">
																	<span id="email_msg"></span>
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Phone');?><b class="red_star">*</b></span>
												                	<input type="text" value="<?php echo set_value('phone'); ?>" name="phone" id="phone" class="textbox" size="30">
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Website');?><b class="red_star">*</b></span>
												                	<input type="text" value="<?php echo set_value('website'); ?>" name="website" id="website" class="textbox" size="30">
																	 <div class="clear"></div>
																</div>
															</div>		  
														  
														  </td>
														  
														  
														  
												        <td width="2%">&nbsp;</td>
												        <td width="37%">
														
															<div class="form_panel" >
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Address');?><b class="red_star">*</b></span>
												               		<input type="text" value="<?php echo set_value('address'); ?>" name="address" id="address" class="textbox" size="30">
																	
																	<div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Zipcode');?><b class="red_star">*</b></span>
												           			<input type="text" value="<?php echo set_value('zipcode'); ?>" name="zipcode" id="zipcode" class="textbox" size="30">
																  
																  <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('City');?><b class="red_star">*</b></span>
												            		<input type="text" value="<?php echo set_value('city'); ?>" name="city" id="city" class="textbox" size="30">
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Country');?><b class="red_star">*</b></span>
												             		<select name="country_id" id="country_id" type="select" class="textbox" style="width: 209px">
													                    <option value="-1">-- <?php echo _('Select Country'); ?> --</option>
													                    <?php if(!empty($country)):?>
														                    <?php foreach($country as $cont1):?>
														                    	<option value="<?php echo $cont1->id; ?>" <?php if(set_value('country_id') == $cont1->id){ echo 'selected="selected"'; } ?> ><?php echo $cont1->country_name; ?></option>
																			<?php endforeach; ?>
																		<?php endif;?>
												                  	</select>
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Username');?><b class="red_star">*</b></span>
												                	<input type="text" value="<?php echo set_value('username'); ?>" name="username" id="username" class="textbox" size="30" onchange="check_username(this.value);">
												                  	<span id="user_msg"></span>
																	 <div class="clear"></div>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Password');?><b class="red_star">*</b></span>
												                	<input type="text" value="" name="password" id="password" class="textbox" size="30">
												                	<a href="javascript: void(0);" style="color: #FF0000; float: left; font-size: 11px; font-weight: bold; margin-left: 10px; text-decoration: underline;" onclick="generate_passwod();"><?php echo _("generate one");?></a>
																	 <div class="clear"></div>
																</div>
															</div>		  		  
														  
														  </td>
												        <td width="10%">&nbsp;</td>
												      </tr>
												      <tr>
												        <td valign="middle" colspan="5"><table width="100%" cellspacing="0" cellpadding="0" border="0">
												            <tbody>
												              <tr>
												                <td height="10" colspan="4">&nbsp;</td>
												              </tr>
												              <tr>
												                <td width="270" height="30">&nbsp;</td>
												                <td class="wd_text"><?php echo _('Account Type');?>&nbsp;&nbsp;</td>
												                <td>
												                	<select name="ac_type_id" id="ac_type_id" type="select" class="textbox" style="width: 250px">
													                    <?php if(!empty($account_types)):?>
														                    <?php foreach($account_types as $at):?>
														                    	<option value="<?php echo $at->id?>" <?php if(set_value('ac_type_id') == $at->id){ echo 'selected="selected"'; } ?> ><?php echo $at->ac_title; ?></option>
																		    <?php endforeach;?>
																		<?php endif;?>
												                    </select>
												                </td>
												                <td width="281">&nbsp;</td>
												              </tr>
												              <tr>
												                <td>&nbsp;</td>
												                <td height="30" class="wd_text" style="padding: 20px"><input type="checkbox" name="role" id="role" value="super" <?php if(set_value('role') == 1){ echo 'checked="checked"'; } ?>></td>
												                <td style="padding-top: 20px; padding-bottom: 20px"><strong><?php echo _('Activate As \'SUPER ADMIN\'');?></strong></td>
												                <td>&nbsp;</td>
												              </tr>
												              <tr>
												                <td>&nbsp;</td>
												                <td height="30" class="wd_text" style="padding: 20px"><input type="checkbox" value="1" id="hide_bp_intro" name="hide_bp_intro" <?php if(set_value('hide_bp_intro') == 1){ echo 'checked="checked"'; } ?>></td>
												                <td style="padding-top: 20px; padding-bottom: 20px"><strong><?php echo _('Hide my company from the Bestelpunt Portal');?></strong></td>
												                <td>&nbsp;</td>
												              </tr>
												            </tbody>
												          </table></td>
												      </tr>
												      <tr>
												        <td width="10%"></td>
												        <td width="37%">
														
														
														
														<div class="form_panel" >
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Order Online URL'); ?></span>
																	<input type="text" value="<?php echo set_value('existing_order_page'); ?>" name="existing_order_page" id="existing_order_page" class="textbox" size="30">
																	<span id="url_found" name="url_found" class="error"></span>
																</div>
																<div class="form_panel_row">
																	<span class="form_label"><?php echo _('Company Facebook URL'); ?></span>
												                	<input type="text" value="<?php echo set_value('company_fb_url'); ?>" name="company_fb_url" id="company_fb_url" class="textbox" size="30">
																	
																	<div class="clear"></div>
																</div>
																<div class="form_panel_row_space">
																	
																</div>
																
															</div>		  
														  
														  
														  </td>
												        <td width="2%"></td>
												        <td width="37%"></td>
												        <td width="10%"></td>
												      </tr>
													  
													  <tr>
															<td width="10%"></td>
															<td colspan="3">
															
															<div class="form_panel_msg" >
																<div class="form_panel_row_msg">
																	<div class="left_label_box">
																		<span class="form_label_msg"><?php echo _('Company short description'); ?></span>
																	</div>
																	<div class="right_text_box">
																		<p>(<?php echo _('maximum 500 characters'); ?>)</p>
																		<textarea style="width: 430px;" class="textbox" rows="5" cols="50" id="company_desc" name="company_desc"><?php echo set_value('company_desc'); ?></textarea>
																	</div>
																	<div class="clear"></div>
															
															</div>
															
																
															</td>
															
												        <td width="10%"></td>
													</tr>
													  
													  <tr>
															<td width="10%"></td>
															<td colspan="3">
															
															<div class="form_panel_sch" >
																<div class="form_panel_row_sch">
																	<div class="form_panel_row_space"></div>
																	<div class="left_label_box">
																		<span class="form_label"><?php echo _('Opening Hours'); ?></span>
																	</div>
																	<div class="right_text_box_sch">
																	<table id="opening-hours">
												                    <tbody>
												                    <?php if(!empty($days)) { foreach($days as $d) { $opening_hours = array(); ?>
												                      <tr>
												                        <th><span><?php echo $d->name; ?></span></th>
												                        <td>
												                        	<select name="time_1[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_1')" class="<?php echo $d->id; ?>_1">
												                        		<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      											<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
													                            <?php for($i=0;$i<=23;$i++) { ?>
																					<?php for($j=0;$j<=30;$j=$j+30) { ?>
																						<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																						<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_1'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
																					<?php } ?>
																				<?php } ?>
												                            </select>
												                        </td>
												                        <td><?php echo _('to'); ?></td>
												                        <td>
												                        	<select name="time_2[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_1')" class="<?php echo $d->id; ?>_1">
												                        	<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      										<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
													                            <?php for($i=0;$i<=23;$i++) { ?>
																					<?php for($j=0;$j<=30;$j=$j+30) { ?>
																						<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																						<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_2'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
																					<?php } ?>
																				<?php } ?>	
																	  		</select>
																	    </td>
												                        <td><?php echo _('and'); ?></td>
												                        <td>
												                        	<select name="time_3[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_2')" class="<?php echo $d->id; ?>_2">
												                        		<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      											<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
																			    <?php for($i=0;$i<=23;$i++) { ?>
																					<?php for($j=0;$j<=30;$j=$j+30) { ?>
																						<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																						<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_3'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
																					<?php } ?>
																				<?php } ?>
																	  		</select>
																	  	</td>
												                        <td><?php echo _('to'); ?></td>
												                        <td>
												                        	<select name="time_4[<?php echo $d->id; ?>]" onchange="show_hide_oh('<?php echo $d->id;?>',this.value,'<?php echo $d->id; ?>_2')" class="<?php echo $d->id; ?>_2">
												                        		<option value="CLOSED" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == "CLOSED" ) echo 'selected="selected"'; } ?> ><?php echo _("CLOSED"); ?></option>
								      											<option value="0" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == "0" ) echo 'selected="selected"'; } ?> ><?php echo _("NONE"); ?></option>
																			    <?php for($i=0;$i<=23;$i++) { ?>
																					<?php for($j=0;$j<=30;$j=$j+30) { ?>
																						<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																						<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours[$d->id]['time_4'] == ($i.':'.$j) ) echo 'selected="selected"'; } ?> ><?php echo $i.':'.$j; ?></option>
																					<?php } ?>
																				<?php } ?>
																	  		</select>
																	  	</td>
												                      </tr>
												                      <?php } } ?>
												                    </tbody>
												                    </table>
																</div>
																<div class="clear"></div>
															
															</div>
															
															
															
															</td>
															<td width="10%"></td>
													  </tr>
													
												      <tr>
												        <td valign="top" align="center" colspan="5"><table width="100%" cellspacing="0" cellpadding="0" border="0">
												            <tbody>
												              <tr>
												                <td width="270">&nbsp;</td>
												                <td valign="middle" height="60"><table width="100%" cellspacing="0" cellpadding="0" border="0">
												                    <tbody>
												                      <tr>
												                        <td width="31%" align="right">&nbsp;</td>
												                        <td style="padding-left: 20px"><input type="hidden" value="<?php echo $manager_id;?>" name="manager_id" id="manager_id">
												                          <input type="submit" name="btn_add_update" id="btn_add_update" class="btnWhiteBack" value="<?php echo _('ADD COMPANY'); ?>"></td>
												                      </tr>
												                    </tbody>
												                  </table></td>
												                <td width="20%">&nbsp;</td>
												              </tr>
												            </tbody>
												          </table></td>
												      </tr>
												    </tbody>
												  </table>
												</form>
											</td>
										</tr>
									</tbody>
								</table> 
								<br />

								<script type="text/javascript">
								var frmValidator = new Validator("frm_companies_add_update");
								frmValidator.EnableMsgsTogether();
								frmValidator.addValidation("company_name","req","<?php echo _('Please enter the Company Name'); ?>");
								frmValidator.addValidation("type_id","dontselect=-1","<?php echo _('Please enter the Company Type'); ?>");	
								frmValidator.addValidation("first_name","req","<?php echo _('Please enter the First Name'); ?>");	
								frmValidator.addValidation("last_name","req","<?php echo _('Please enter the Last Name'); ?>");	
								frmValidator.addValidation("email","req","<?php echo _('Please enter the Email'); ?>");	
								frmValidator.addValidation("email","email","<?php echo _('Please enter a valid Email Address'); ?>");	
								frmValidator.addValidation("phone","req","<?php echo _('Please enter the Phone Number'); ?>");
								frmValidator.addValidation("phone","num","<?php echo _('Please enter the Phone Number in Digits'); ?>");	
								frmValidator.addValidation("website","req","<?php echo _('Please enter the Website'); ?>");	
								frmValidator.addValidation("address","req","<?php echo _('Please enter the Address'); ?>");	
								frmValidator.addValidation("zipcode","req","<?php echo _('Please enter the Zipcode'); ?>");	
								frmValidator.addValidation("city","req","<?php echo _('Please enter the City'); ?>");	
								frmValidator.addValidation("country_id","dontselect=-1","<?php echo _('Please Select Country'); ?>");	
								frmValidator.addValidation("username","req","<?php echo _('Please enter Username'); ?>");	
								frmValidator.addValidation("password","req","<?php echo _('Please enter Password'); ?>");
								//frmValidator.addValidation("expiry_date","req","Please enter Date of Expiry");	
								frmValidator.addValidation("registration_date","req","<?php echo _('Please enter Date of Registration'); ?>");
								frmValidator.addValidation("earnings_year","req","<?php echo _('Please enter Earnings/Year'); ?>");	
								
								function validate_data(){
									var validate = true;
									var MSG = Array();
									field = "";
									var msg = "";	
									
									MSG[1] = "";
									if(document.getElementById("companyid") != null ){
										if(document.getElementById("companyid").value == '-1'){
											MSG[1] = "- <?php echo _('Please select the Company'); ?> \n";
											if(field == ""){
												field = "companyid";
												validate = false;
											}
										}
									}else{
										MSG[1] = "";
									}
									
									MSG[2] = "";
									if(document.getElementById("webdesigner_id").value == '-1'){
										MSG[2] = "- <?php echo _('Please select the Webdesigner'); ?> \n";
										if(field == ""){
											field = "webdesigner_id";
											validate = false;
										}
									}else{
										MSG[2] = "";
									}
									
									MSG[3] = ""	;	
									if(document.getElementById("photographer").value == ""){
										MSG[3] = "- <?php echo _('Please enter the Photographer Name'); ?> \n";
										if(field == ""){
											field = "photographer";
											validate = false;
										}
									}else{
										MSG[3] = "";
									}
									
									for(i=1; i<=3; i++){
										if(MSG[i] != ""){
											msg += MSG[i];
										}
									}
									
									if(msg != ""){
										alert(msg);
									}
									
									if(msg != "" && field !=""){
										document.getElementById(field).focus();
										return false;
									}
									
									if(validate == true){
										document.frm2_companies_add_edit.submit();
									}
								}
								</script>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
	</tbody>
</table>
<!-- end of main body -->
</div>
<div id="push"></div>
</div>