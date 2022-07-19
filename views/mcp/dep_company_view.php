<?php
$dep_company = $dep_company ['0'];
?>
<style type="text/css">
.textbox {
	width: 200px;
}

.clear {
	clear: both;
}

.form_panel {
	/*width:72%;*/
	
}
#content{
     font-family:verdana;
     font-size:12px;
}
.form_panel .form_panel_row {
     margin-bottom:10px;
}

.form_panel_row .form_label {
	float: left;
	width: 140px;
}

.form_panel_row .red_star {
	/*float:left;*/
	
}

.form_panel_row .textbox {
	float: left;
}

.form_panel_row_space {
	height: 10px;
}

.form_panel_msg {
	width: 100%;
}

.form_panel_msg .form_panel_row_msg {
	
}

.form_panel_msg .form_panel_row_msg .left_label_box {
	float: left;
	width: 13%;
}

.form_panel_msg .form_panel_row_msg .right_text_box {
	float: left;
	margin-left: 10px;
	width: 86%;
}

.form_panel_msg .form_panel_row_msg .right_text_box p {
	margin: 0;
	padding: 0;
}

.form_panel_sch {
	width: 100%;
}

.form_panel_row_sch {
	
}

.form_panel_row_sch .left_label_box {
	float: left;
	width: 13%;
}

.form_panel_row_sch .left_label_box .form_label {
	
}

.form_panel_row_sch .right_text_box_sch {
	float: left;
	margin-left: 6px;
	width: 48%;
}

#opening-hours {
	width: 100%;
}

.ui-autocomplete {
	font-size: 11px;
	max-height: 100px;
	overflow-y: auto;
}

span.error {
	float: left;
	margin-left: 10px;
	color: #ff0000;
	font-size: 12px;
}

#opening-hours td span {
	background-color: #E0ECFF;
	display: block;
	font-family: arial;
	font-size: 12px;
	margin-right: 10px;
	padding: 5px 10px;
	text-align: left;
}
#type_id{
	 width: 204px;
}

</style>

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
											<td style="padding: 10px;"><?php //echo validation_errors(); ?></td>
										</tr>
										<tr>
											<td width="100%" valign="top" align="center">
												<form name="frm_companies_add_update" id="frm_companies_add_update" method="post" enctype="multipart/form-data" action="">
													<table width="98%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #003366; text-align: left">
														<tbody>
															<tr>
																<td height="20" bgcolor="#003366" align="left" colspan="5" class="whiteSmallBold" style="padding-left: 10px;"><?php echo _('Company Information'); ?></td>
															</tr>
															<tr>
																<td height="10" colspan="5">&nbsp;</td>
															</tr>
															<tr>
																<td height="10" align="center" colspan="5">
																	<span id="dup_msg" style="color: #FF0000"></span>
																</td>
															</tr>
															<tr>
																<td width="10%">&nbsp; <input type="hidden" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW" value="companies_add_edit"></td>
																<td width="37%">
																	<div class="form_panel">
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('ID'); ?></span>
																			<?php echo $dep_company->id;?>
						 												</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Company Name'); ?> <b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->company_name;?>" name="company_name" id="company_name" class="textbox" size="30">
																			<div class="clear"></div>
																		</div>
																		<?php $company_types = explode("#",$dep_company->type_id);?>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Company Type');?><b class="red_star">*</b></span> 
																			<select name="type_id" id="type_id" type="select" class="textbox" multiple>
																				<option value="-1">-- <?php echo _('Select Company Type'); ?> --</option>
														                    	<?php if(!empty($company_type)) { foreach($company_type as $ct) { ?>
																		    	<option value="<?php echo $ct->id; ?>" <?php if(in_array($ct->id,$company_types)){?>selected="selected" <?php }?>><?php echo $ct->company_type_name; ?></option>
																		    	<?php } } ?>
														                    </select>
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('First Name'); ?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->first_name;?>" name="first_name" id="first_name" class="textbox required" size="30">
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Last Name');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->last_name;?>" name="last_name" id="last_name" class="textbox required" size="30">
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Email');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->email;?>" name="email" id="email" class="textbox required" size="30" onchange="check_email(this.value);"> 
																			<span id="email_msg"></span>
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Phone');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->phone;?>" name="phone" id="phone" class="textbox" size="30">
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Website');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->website;?>" name="website" id="website" class="textbox" size="30">
																			<div class="clear"></div>
																		</div>
																	</div>

																</td>

																<td width="2%">&nbsp;</td>
																<td width="37%">
																	<div class="form_panel">
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Address');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->address;?>" name="address" id="address" class="textbox" size="30">
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Zipcode');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->zipcode;?>" name="zipcode" id="zipcode" class="textbox" size="30">
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('City');?><b class="red_star">*</b></span> 
																			<input type="text" value="<?php echo $dep_company->city;?>" name="city" id="city" class="textbox" size="30">
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row">
																			<span class="form_label"><?php echo _('Country');?><b class="red_star">*</b></span> 
																			<select name="country_id" id="country_id" type="select" class="textbox" style="width: 209px">
																				<option value="-1">-- <?php echo _('Select Country'); ?> --</option>
															                    <?php if(!empty($country)):?>
																                    <?php foreach($country as $cont1):?>
																                    	<option value="<?php echo $cont1->id; ?>" <?php if($dep_company->country_id == $cont1->id){?>selected="selected" <?php }?>><?php echo $cont1->country_name; ?></option>
																					<?php endforeach; ?>
																				<?php endif;?>
														                  	</select>
																			<div class="clear"></div>
																		</div>
																		<div class="form_panel_row"></div>
																	</div>
																</td>
																<td width="10%">&nbsp;</td>
															</tr>
															<tr>
																<td valign="middle" colspan="5">
																	<table width="100%" cellspacing="0" cellpadding="0" border="0">
																		<tbody>
																			<tr>
																				<td height="10" colspan="4">&nbsp;</td>
																			</tr>
																			<tr>
																				<td>&nbsp;</td>
																				<td height="30" class="wd_text" style="padding: 20px">
																					<input type="checkbox" name="role" id="role" value="super" <?php if($dep_company->as_supercompany){?> checked <?php }?>>
																				</td>
																				<td style="padding-top: 20px; padding-bottom: 20px"><strong><?php echo _('Activate As \'SUPER ADMIN\'');?></strong></td>
																				<td>&nbsp;</td>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>

															<tr>
																<td width="10%"></td>
																<td colspan="3">
																	<div class="form_panel_sch">
																		<div class="form_panel_row_sch">
																			<div class="form_panel_row_space"></div>
																			<div class="left_label_box">
																				<span class="form_label"><?php echo _('Opening Hours'); ?></span>
																			</div>
																			<div class="right_text_box_sch">
																				<table id="opening-hours">
																					<tbody>
																                    <?php $opening_hours = json_decode($dep_company->opening_hours,true);?>
																                    <?php if(!empty($days)) { foreach($days as $d) { //$opening_hours = array(); ?>
																                    	<tr>
																							<td><span><?php echo $d->name; ?></span></td>
																							<td>
																								<select name="time_1[<?php echo $d->id; ?>]"> 
																								<?php for($i=0;$i<=23;$i++) { ?>
																									<?php for($j=0;$j<=30;$j=$j+30) { ?>
																										<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																										<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours['time_1'][$d->id] == ($i.':'.$j) ) echo 'selected="selected"'; } ?>><?php echo $i.':'.$j; ?></option>
																									<?php } ?>
																								<?php } ?>
																                            	</select>
																                            </td>
																							<td><?php echo _('to'); ?></td>
																							<td>
																								<select name="time_2[<?php echo $d->id; ?>]">
																	                            <?php for($i=0;$i<=23;$i++) { ?>
																									<?php for($j=0;$j<=30;$j=$j+30) { ?>
																										<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																										<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours['time_2'][$d->id] == ($i.':'.$j) ) echo 'selected="selected"'; } ?>><?php echo $i.':'.$j; ?></option>
																									<?php } ?>
																								<?php } ?>	
																					  			</select>
																					  		</td>
																							<td><?php echo _('and'); ?></td>
																							<td>
																								<select name="time_3[<?php echo $d->id; ?>]">
																							    <?php for($i=0;$i<=23;$i++) { ?>
																									<?php for($j=0;$j<=30;$j=$j+30) { ?>
																										<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																										<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours['time_3'][$d->id] == ($i.':'.$j) ) echo 'selected="selected"'; } ?>><?php echo $i.':'.$j; ?></option>
																									<?php } ?>
																								<?php } ?>
																					  			</select>
																					  		</td>
																							<td><?php echo _('to'); ?></td>
																							<td>
																								<select name="time_4[<?php echo $d->id; ?>]">
																							    <?php for($i=0;$i<=23;$i++) { ?>
																									<?php for($j=0;$j<=30;$j=$j+30) { ?>
																										<?php if(strlen($i)==1){$i='0'.$i;} if(strlen($j)==1){$j='0'.$j;} ?>
																										<option value="<?php echo $i.':'.$j; ?>" <?php if(!empty($opening_hours)){ if($opening_hours['time_4'][$d->id] == ($i.':'.$j) ) echo 'selected="selected"'; } ?>><?php echo $i.':'.$j; ?></option>
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
																	</div>
																</td>
																<td width="10%"></td>
															</tr>

															<tr>
																<td valign="top" align="center" colspan="5">
																	<table width="100%" cellspacing="0" cellpadding="0" border="0">
																		<tbody>
																			<tr>
																				<td width="270">&nbsp;</td>
																				<td valign="middle" height="60">
																					<table width="100%" cellspacing="0" cellpadding="0" border="0">
																						<tbody>
																							<tr>
																								<td width="31%" align="right">
																									<a href="<?php echo base_url();?>mcp/dep/approve/<?php echo $dep_company->id;?>"><input type="button" id="btn_approve" class="btnWhiteBack" value="<?php echo _('APPROVE'); ?>"></a>
																									<a href="<?php echo base_url();?>mcp/dep/disapprove/<?php echo $dep_company->id;?>"><input type="button" id="btn_disapporve" class="btnWhiteBack" value="<?php echo _('DISAPPROVE'); ?>"></a>
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</td>
																				<td width="20%">&nbsp;</td>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
														</tbody>
													</table>
												</form>
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
			</td>
		</tr>
	</tbody>
</table>
<!-- end of main body -->
</div>
<div id="push"></div>
</div>