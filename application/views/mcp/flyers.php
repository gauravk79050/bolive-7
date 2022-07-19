<script>
function change_order(flyer_id, value){
	jQuery.post(
				base_url+'mcp/flyers/change_order',
				{
					'flyer_id': flyer_id,
					'order': value
				},
				function(response){
					alert(response);
				}
			);
}
</script>
<div style="width:100%">

    <!-- start of main body -->

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
    	<tbody>
        	<tr>
          		<td valign="top" align="center">
          			<table width="98%" cellspacing="0" cellpadding="0" border="0">
	              		<tbody>
			            	<tr>
			                	<td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
			                		<table width="98%" cellspacing="0" cellpadding="0" border="0">
                      					<tbody>
                      					
                        					<tr>
                          						<td align="center" style="padding-bottom:5px">
                          							<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
														<tbody>
															<tr>
																<td width="50%" align="left"><h3><?php echo _("Flyer Manager");?></h3></td>
																<td width="50%" align="right"><div "="" onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>
																	<div "="" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/add.png);float:right;" title="Add Language" class="icon_button" onClick="window.location.href='<?php echo base_url();?>mcp/flyers/add'" id="btn_add"></div>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
											
											<tr>
					                        	<td align="center">
													<table width="100%" cellspacing="0" cellpadding="0" border="0">
			                              				<tbody>
							                                <tr>
																<td bgcolor="#003366">
																	<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">
																		<tbody>
																			<tr>
																				<td width="7%" class="whiteSmallBold"><?php echo _("Display Order");?></td>

                                          										<td width="13%" class="whiteSmallBold"><?php echo _("Name");?></td>

																				<td width="30%" class="whiteSmallBold"><?php echo _("Description");?></td>

																				<td width="30%" class="whiteSmallBold"><?php echo _("Image");?></td>
                                          
																				<td width="10%" class="whiteSmallBold"><?php echo _("Price");?></td>

																				<td width="10%" align="right" style="padding-right:40px" class="whiteSmallBold">Options</td>
																			</tr>

																			<tr>
																				<td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="6">
																					<table width="100%" cellspacing="0" cellpadding="0" border="0">
																						<tbody>
																						<?php if(!empty($flyers)) { ?>
                                             												<?php foreach($flyers as $flyer):?>
																							<tr>
																								<td width="7%" height="40" class="blackMediumNormal">
                                                  													<select onchange="change_order(<?php echo $flyer->id;?>,this.value)">
                                                  														<option val="<?php echo ( count($flyers) + 1 );?>"><?php echo _("Select");?></option>
                                                  														<?php for($i = 1; $i <= count($flyers); $i++ ){?>
                                                  														<option val="<?php echo $i;?>" <?php if($flyer->display_order == $i){ echo 'selected="selected"'; }?>><?php echo $i;?></option>
                                                  														<?php }?>
                                                  													</select>
                                                  												</td>
																								<td width="13%" class="blackMediumNormal"><?php echo $flyer->name;?></td>
																								<td width="30%" class="blackMediumNormal"><?php echo $flyer->description;?></td>
																								<td width="30%" class="blackMediumNormal">
																									<span style="padding:5px" class="blackMediumNormal"><img width="160" height="245" border="0" src="<?php  echo base_url()."assets/mcp/images/flyers/".$flyer->image; ?>"></span>
																								</td>
																								<td width="10%" class="blackMediumNormal"><?php echo $flyer->price;?> &euro;</td>
																								<td width="10%" align="right" style="padding-right:40px" class="blackMediumNormal">
																									<span style="padding:5px" class="blackMediumNormal">
																										<a href="<?php echo base_url();?>mcp/flyers/add/<?php echo $flyer->id;?>" title="Edit" alt="Edit" ><img width="16" height="16" border="0" style="cursor:pointer" src="<?php echo base_url(); ?>assets/mcp/images/edit.jpg"></a>
																									</span>
																									<span style="padding:5px" class="blackMediumNormal">
																										<a href="<?php echo base_url();?>mcp/flyers/delete/<?php echo $flyer->id;?>" title="Delete" alt="Delete" onclick="return confirm('<?php echo _("Do you really want to delete it ?");?>');"><img width="16" height="16" border="0" style="cursor:pointer" src="<?php echo base_url(); ?>assets/mcp/images/delete1.png"></a>
																									</span>
																								</td>
																							</tr>
                                         													<?php endforeach ;?>
										 												<?php } else { ?>
																							<tr>
																								<td colspan="6" style="font-weight:bold;color:red;padding:5px;" align="center">
											     													<?php echo _('Sorry ! No Flyers Found.'); ?>
											   													</td>
											   												</tr> 
																						<?php } ?>
										     											</tbody>
																					</table>
																				</td>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
							                                <tr>
																<td>&nbsp;</td>
							                                </tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
        	</tr>
      	</tbody>
    </table>
    <!-- end of main body -->

  </div>