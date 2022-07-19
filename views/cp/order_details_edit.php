<script type="text/javascript">
  var base_url = '<?php echo base_url();?>';
</script>
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/order_details_edit.js"></script>
<script>
	var msg1="<?php echo _('no products available')?>";
	var msg2="<?php echo _('did not get any category.please select a category')?>";
	var msg3="<?php echo _('Are you sure you want to delete?')?>";
	var msg4="<?php echo _('Please use only numbers')?>";
	var msg5="<?php echo _('Image can not be downloaded. Please try again');?>";

	function showHIdeDiv(){
		if(<?php echo $ShowProd?>){
			document.getElementById('display_product').style.display = "block";
			document.getElementById('display_product1').style.display = "block";
		}else{
			document.getElementById('display_product').style.display = "none";
			document.getElementById('display_product1').style.display = "none";
		}
	}
</script>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Edit Orders')?></h2>
      	<span class="breadcrumb">
			<a href="<?php echo base_url()?>cp/cdashboard/"><?php echo _('Home')?></a> &raquo;
			<a href="<?php echo base_url()?>cp/orders"><?php echo _('Orders')?></a> &raquo;
			<?php echo _('Orders edit')?>
      	</span>
	</div>
    <?php if(isset($_SESSION['SuccessMsgOE'])){?>
    <div id="notice"><strong>
      <?=$this->lang["UPDATED"]?>
      </strong>:
      <?=$_SESSION['SuccessMsgOE']?>
    </div>
    <?php unset($_SESSION['SuccessMsgOE']); }?>
    <?php if(isset($_SESSION['ErorMsgOE'])){?>
    <div id="error"><strong>
      <?=$this->lang["ERROR"]?>
      </strong>:
      <?=$_SESSION['ErorMsgOE']?>
    </div>
    <?php unset($_SESSION['ErorMsgOE']);}?>
	<div id="content">
    	<div id="content-container">
       
			<div class="box">
          		<h3><?php echo _('Order Details')?></h3>
          		<div class="table">
            		<table border="0">
              			<tr>
                			<td>
								<span style="padding-left:20px" class="textlabel"><?php echo _('ORDERS-NO')?>&nbsp;:&nbsp;</span> <?php echo $orderData[0]->id?>
							</td>
              			</tr>
              			<tr>
               				<td>
								<span style="padding-left:20px" class="textlabel"><?php echo _('ORDER-DATE')?>&nbsp;:&nbsp;</span>
                  				<?php					
                  					$is_international = false;
                  					$flag=false;
									if( !empty($orderData) && $orderData[0]->option == "2" && $orderData[0]->phone_reciever != '' ){
										$is_international = true;
										$flag=true;
										

									}	
                  				
									$da = date("d",strtotime($orderData[0]->created_date));
									$mo = date("F",strtotime($orderData[0]->created_date));
									$yr = date("y",strtotime($orderData[0]->created_date));
																										
									if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
									{
									   if( $mo == 'January' )
										 $mo = 'Jan';
									   if( $mo == 'February' )
										 $mo = 'Febr';
									   if( $mo == 'March' )
										 $mo = 'Maart';
									   if( $mo == 'April' )
										 $mo = 'April';
									   if( $mo == 'May' )
										 $mo = 'Mei';
									   if( $mo == 'June' )
										 $mo = 'Juni';
									   if( $mo == 'July' )
										 $mo = 'Juli';
									   if( $mo == 'August' )
										 $mo = 'Augustus';
									   if( $mo == 'September' )
										 $mo = 'September';
									   if( $mo == 'October' )
										 $mo = 'Oktober';
									   if( $mo == 'November' )
										 $mo = 'November';
									   if( $mo == 'December' )
										 $mo = 'December';
									}
									
									echo $date_dutch = $da." ".$mo." ".$yr;	
								?>
								<script type="text/javascript">

								var flag1="<?php echo $flag; ?>";
								
								</script>
								
							</td>
              			</tr>
              			<tr>
                			<td>&nbsp;</td>
              			</tr>
              			<tr>
                			<td>
	                			<table border="0" cellpadding="0" cellspacing="0">
	                    			<tr>
	                      				<td>
											<span style="padding-left:20px" class="textlabel"><?php echo _('Customer Address');?>&nbsp;&nbsp;</span>
										</td>
										<?php if( $is_international ):?>
										<td>
											<span style="padding-left:20px" class="textlabel"><?php echo _('Delivery Address');?>&nbsp;&nbsp;</span>
										</td>
										<?php endif;?>
	                    			</tr>
	                    			<tr>
	                      				<td style="padding-left:40px">
	                      					<span >
		                        				<?php echo $orderData[0]->company_c;?>
		                        				<br/>
												<?php echo stripslashes( $orderData[0]->lastname_c ) ?>
												&nbsp;
												<?php echo stripslashes( $orderData[0]->firstname_c )?>
												<br />
												<?php echo stripslashes( $orderData[0]->address_c )?>
												&nbsp;
												<?php echo stripslashes( $orderData[0]->housenumber_c )?>
												<br />
												<?php echo $orderData[0]->postcode_c?>
												&nbsp;
												<?php echo stripslashes( $orderData[0]->city_c )?>
												<br/>
												
												<?php echo $orderData[0]->country_name?>
												<br/>
												<br/>
												<strong><?php echo _('GSM')?></strong>&nbsp;&nbsp;: <?php echo $orderData[0]->phone_c?>
		                        				<br/>
		                        				<?php /*if($orderData[0]->mobile_c != ''):?><strong>GSM</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?php echo $orderData[0]->mobile_c?>
		                        				<br/><?php endif;*/?>
		                        				<strong><?php echo _('EMAIL')?></strong>&nbsp;&nbsp;:&nbsp;
												<a class="edit" href="mailto:<?php echo $orderData[0]->email_c?>?subject=Re:&nbsp;<?php echo _('ORDERS-NO')?>&nbsp;<?php echo $orderData[0]->id?>&amp;body=<?php echo _('Dear client');?>,">
		 											<?php echo $orderData[0]->email_c?>
		 										</a>
											</span>
										</td>
										<?php if( $is_international ):?>
										<td style="padding-left:40px">
											<span>
												<?php if(isset($orderData[0]->name)):echo stripslashes($orderData[0]->name);?>&nbsp;<br /><?php endif;?>
								    			<?php echo stripslashes($orderData[0]->delivery_streer_address);?>&nbsp;<?php echo stripslashes($orderData[0]->housenumber_c);?><br />
								    			<?php echo $orderData[0]->delivery_zip; if($orderData[0]->delivery_city != '0'){?> - <?php echo stripslashes($orderData[0]->delivery_city);}?><br/>
								    			<?php echo isset($countries[$orderData[0]->delivery_country])?$countries[$orderData[0]->delivery_country]->country_name:'';?><br/>
								    			<?php echo _('Mobile');?> :&nbsp;<?php echo $orderData[0]->phone_reciever;?><br />
											</span>
										</td>
										<?php endif;?>
	                    			</tr>
	                    			<tr>
	                      				<td>&nbsp;</td><?php if( $is_international ):?><td>&nbsp;</td><?php endif;?>
	                    			</tr>
	                    			
	                    			<?php if($orderData[0]->option == "2"):?>
	                    			<tr>
	                      				<td>
											<div class="display_none" id="succeed"></div>
	                       				 	<div class="display_none" id="error"></div>
	                        				<div>
												<span id="open_form_2" >
	                          						<input type="button" value="<?php echo _('EDIT')?>" name="open_form_2" id="open_form_2" onclick="open_close_delivery_form('open')"/>
	                          					</span>
												<span id="close_form_2" class="display_none">
	                          						<input type="button" value="<?php echo _('CLOSE')?>" name="close_form_2" id="close_form_2" onclick="open_close_delivery_form('close')"/>
	                          					</span>
											</div>
	                        				<form name="frm_order_details_edit"  id="frm_order_details_edit">
	                        					<table width="100%" cellpadding="0" cellspacing="0" border="0" >
	                        					<?php if( !$is_international ):?>
	                          						<tr>
	                            						<td width="40%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="50%" align="left">
																		<strong style="padding-left:10px" class="textlabel"><?php echo _('DELIEVRY-DATE');?></strong>
																	</td>																	
		                                  							<td width="50%" align="left">
		                                  							<?php if($orderData[0]->delivery_date != '0000-00-00'):?>
																		<span id="date_text">
		                                    								<?php
																			
																			$da = date("d",strtotime($orderData[0]->delivery_date));
																			$mo = date("F",strtotime($orderData[0]->delivery_date));
																			$yr = date("y",strtotime($orderData[0]->delivery_date));
																																				
																			if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
																			{
																			   if( $mo == 'January' )
																				 $mo = 'Jan';
																			   if( $mo == 'February' )
																				 $mo = 'Febr';
																			   if( $mo == 'March' )
																				 $mo = 'Maart';
																			   if( $mo == 'April' )
																				 $mo = 'April';
																			   if( $mo == 'May' )
																				 $mo = 'Mei';
																			   if( $mo == 'June' )
																				 $mo = 'Juni';
																			   if( $mo == 'July' )
																				 $mo = 'Juli';
																			   if( $mo == 'August' )
																				 $mo = 'Augustus';
																			   if( $mo == 'September' )
																				 $mo = 'September';
																			   if( $mo == 'October' )
																				 $mo = 'Oktober';
																			   if( $mo == 'November' )
																				 $mo = 'November';
																			   if( $mo == 'December' )
																				 $mo = 'December';
																			}
																			
																			echo $date_dutch = $da." ".$mo." ".$yr;		
																			
																			?>
		                                    							</span>
																		<span id="date_text_box" class="display_none">
		                                    								<input type="text" value="<?php echo date("d/m/Y", strtotime($orderData[0]->delivery_date))?>" style="width:80px" readonly="readonly" onChange="get_day_name('delivery');" name="delivery_date" id="delivery_date"/>
		                                    								<input type=button value="..." onclick="displayCalendar(document.frm_order_details_edit.delivery_date,'dd/mm/yyyy',this);" name="calender" id="calender"/>
		                                    							</span>
		                                    							<?php endif;?>
																	</td>																	
		                                						</tr>
		                              						</table>
	                              						</td>
	                            						<td width="60%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="25%" align="left"><strong style="padding-left:10px;  vertical-align:top" class="textlabel"><?php echo _('DELIEVRY-ADDRESS')?></strong></td>
		                                  							<td width="50%" align="left">
																		<span id="address_text"><?php echo stripslashes( $orderData[0]->delivery_streer_address );?></span>
																		<span id="address_text_box" class="display_none">
		                                    								<textarea  style ="width:100%" name="delivery_streer_address" id="delivery_streer_address"><?php echo stripslashes( $orderData[0]->delivery_streer_address );?></textarea>
		                                    							</span>
																	</td>
		                                						</tr>
		                              						</table>
	                              						</td>
	                          						</tr>
	                          						<tr>
	                            						<td width="40%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="50%" align="left"><strong style="padding-left:10px" class="textlabel"><?php echo _('DELIEVRY-DAY')?></strong></td>
		                                  							<td width="50%" align="left">
		                                  								<span id="day_text">
		                                    								<?php /*if($_SESSION["language_id"] == "2")
			    	                               								 	echo $day = getDayInDutch($this->orderData[0]["delivery_day"]);
		                    	                								else*/
																				$day = $orderData[0]->delivery_day;
																				echo $day;
																			?>
		                                    							</span>
																		<span id="day_text_box" class="display_none">
		                                    								<input type="text" value="<?php echo $orderData[0]->delivery_day?>" class="text medium"  readonly="readonly" name="delivery_day" id="delivery_day"/>
		                                    							</span>
																	</td>
		                                						</tr>
		                              						</table>
	                              						</td>
	                            						<td width="60%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="25%" align="left"><strong style="padding-left:10px" class="textlabel"><?php echo _('DELIEVRY-AREA')?></strong></td>
		                                  							<td width="50%" align="left">
																		<span id="area_name_text"><?php if(isset($orderData[0]->delivery_area_name)) { echo stripslashes( $orderData[0]->delivery_area_name ); } ?></span>
																		<span id="area_name_text_box" class="display_none" >
			                                    							<select onChange="getAreaZip(this.value,this.form.delivery_city,'0')"name="delivery_area" id="delivery_area">									
																				<option value="-1">-- Select Area --</option>
																				<?php if($delivery_areas):foreach($delivery_areas as $delivery_area):?>
																				
																				<option value="<?php echo $delivery_area->id;?>" <?php if(isset($orderData[0]->delivery_area_id) && $delivery_area->id == $orderData[0]->delivery_area_id):?>selected="selected"<?php endif;?>><?php echo stripslashes( $delivery_area->area_name );?></option>
																				
																				<?php endforeach; else:?>
																				<option value="0"><?php echo _('No delivery area available')?></option>
																				<?php endif;?>
																			</select>
		                                    							</span>
																	</td>
		                                						</tr>
		                              						</table>
	                              						</td>
	                          						</tr>
	                          						<tr>
	                            						<td width="40%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="50%" align="left"><strong style="padding-left:10px" class="textlabel"><?php echo _('DELIEVRY-CITY');?></strong></td>
																	<td width="50%" align="left">
																		<span id="city_text"><?php if(isset($orderData[0]->delivery_area_name)) { echo stripslashes( $orderData[0]->delivery_city_name ); } ?></span>
																		<span id="city_text_box" class="display_none">
		                                    								<select onChange="getAreaZipPrice(this.value,this.form.delivery_zip,'0')" name="delivery_city" id="delivery_city">									
																			<?php if($delivery_cities): foreach($delivery_cities as $delivery_city):?>	
																			<option value = "<?php echo $delivery_city->id;?>" <?php if(isset($orderData[0]->delivery_city_id) && $delivery_city->id == $orderData[0]->delivery_city_id):?>selected="selected"<?php endif;?>><?php echo stripslashes( $delivery_city->city_name );?></option>
																			<?php endforeach; else:?>
																			<option value= "-1">----<?php echo _('No city available')?>----</option>	
																			<?php endif;?>
																			</select>
																		</span>
																	</td>
		                                						</tr>
		                              						</table>
	                              						</td>
	                            						<td width="60%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="25%" align="left"><strong style="padding-left:10px" class="textlabel"><?php echo _('DELIEVRY-ZIP')?></strong></td>
		                                  							<td width="50%" align="left">
																		<span id="zip_text"><?php echo $orderData[0]->delivery_zip?></span>
																		<span id="zip_text_box" class="display_none">
		                                    								<input type="text" class="text medium"  value="<?php echo $orderData[0]->delivery_zip?>" readonly="readonly"name="delivery_zip" id="delivery_zip"/>
		                                    							</span>
																	</td>
		                                						</tr>
		                              						</table>
	                              						</td>
	                          						</tr>
	                          						<tr>
	                            						<td width="30%">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                  							<td width="50%" align="left"><strong style="padding-left:10px" class="textlabel"><?php echo _('TIMEFRAME');?></strong></td>
		                                  							<td width="50%" align="left">
																		<span id="hour_text"><?php echo $orderData[0]->delivery_hour?>:<?php echo $orderData[0]->delivery_minute?>&nbsp;</span>
																		<?php if($orderData[0]->delivery_hour != ''): ?>
																		<span id="hour_unit"><?php echo _('HOURS')?></span>
																		<?php endif;?>
																		<span id="hour_text_box" class="display_none">
		                                    								<input type="text" value="<?php echo $orderData[0]->delivery_hour?>" class="text medium"  maxlength="5" name="delivery_hour" id="delivery_hour"/>&nbsp;<?php echo _('HOURS')?>
																			<input type="text" value="<?php echo $orderData[0]->delivery_minute;?>" class="text medium" maxlength="5"name="delivery_minute"  id="delivery_minute"/>&nbsp;<?php echo _('MINS');?>
		                                    							</span>
																	</td>
		                                						</tr>
															</table>
														</td>
	                            						<td>&nbsp;</td>
	                          						</tr>
	                          						<?php endif;?>

							  						<tr>
	                            						<td colspan="2">
		                            						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
		                                						<tr>
		                                 	 						<td width="22%" align="left"><strong style="padding-left:10px" class="textlabel"><?php echo _('DELIEVRY REMARKS')?></strong></td>
		                                  							<td width="78%" align="left">
																		<span id="remark_text"><?php echo stripslashes( $orderData[0]->delivery_remarks )?></span>
																		<span id="remark_text_box" class="display_none">
		                                    								<textarea style="width:100%" name="delivery_remarks" id="delivery_remarks"><?php echo stripslashes( $orderData[0]->delivery_remarks );?></textarea>
																		</span>
																	</td>
		                                						</tr>
		                              						</table>
	                              						</td>
													</tr>
											
	                          						<tr>
	                            						<td colspan="2">
															<span id="save_form_2" class="display_none" style="margin-left:70px">
	                              								<input type="button" value="<?php echo _('SAVE')?>" onClick="validate_form_delivery();" name"save" id="save" />
															</span>
														</td>
	                          						</tr>
	                          					
	                          						<tr>
	                            						<td colspan="2">
															<span id="save_form_3" class="display_none" style="margin-left:70px">
	                              								<input type="button" value="<?php echo _('SAVE')?>" onClick="validate_only_delivery();" name"save" id="save" />
															</span>
														</td>
	                          						</tr>
	                          						
	                        					</table>
	                        				</form>
	                      				</td>
	                      				<?php if( $is_international ):?>
	                      				<td>&nbsp;</td>
	                      				<?php endif;?>
	                    			</tr>
	                    			<?php elseif($orderData[0]->option == "1"):?>
									<tr>
	                      				<td>
											<div class="display_none" id="succeed"></div>
											<div class="display_none" id="error"></div>
											<div>
												<span id="open_form_1">
													<input type="button" value="<?php echo _('EDIT')?>" name="open_form_1" id="open_form_1" onclick="open_close_pickup_form('open')"/>
												</span>
												<span id="close_form_1" class="display_none">
													<input type="button" value="<?php echo _("CLOSE")?>" name="close_form_1" id="close_form_1" onclick="open_close_pickup_form('close')"/>
												</span>
											</div>
											<form name="frm_order_details_edit" id="frm_order_details_edit">
												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td width="100%">
															<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
																<tr>
																	<td align="left" width="15%" class="textlabel"><?php echo _('PICK-DATE')?></td>
																	<td align="left" width="85%">
																		<span id="date_text">
																			<?php 
																			
																			$da = date("d",strtotime($orderData[0]->order_pickupdate));
																			$mo = date("F",strtotime($orderData[0]->order_pickupdate));
																			$yr = date("y",strtotime($orderData[0]->order_pickupdate));
																																				
																			if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
																			{
																			   if( $mo == 'January' )
																				 $mo = 'Jan';
																			   if( $mo == 'February' )
																				 $mo = 'Febr';
																			   if( $mo == 'March' )
																				 $mo = 'Maart';
																			   if( $mo == 'April' )
																				 $mo = 'April';
																			   if( $mo == 'May' )
																				 $mo = 'Mei';
																			   if( $mo == 'June' )
																				 $mo = 'Juni';
																			   if( $mo == 'July' )
																				 $mo = 'Juli';
																			   if( $mo == 'August' )
																				 $mo = 'Augustus';
																			   if( $mo == 'September' )
																				 $mo = 'September';
																			   if( $mo == 'October' )
																				 $mo = 'Oktober';
																			   if( $mo == 'November' )
																				 $mo = 'November';
																			   if( $mo == 'December' )
																				 $mo = 'December';
																			}
																			
																			echo $date_dutch = $da." ".$mo." ".$yr;				
																			
																			?>
																		</span>
																		<span id="date_text_box" class="display_none">
																			<input type="text" value="<?php echo date("d/m/Y", strtotime($orderData[0]->order_pickupdate))?>" class="text veryshort" readonly="readonly" onChange="get_day_name('pickup');" name="order_pickupdate" id="order_pickupdate"/>
																			<input type="button" value="<?php echo _('Calender');?>" onclick="displayCalendar(document.frm_order_details_edit.order_pickupdate,'dd/mm/yyyy',this); " name="calender" id="calender"/>
																		</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
																<tr>
																	<td align="left" width="15%" class="textlabel"><?php echo _('PICKDAY');?></td>
																	<td align="left" width="85%">
																		<span id="day_text">
																			<?php    /*  if($_SESSION["language_id"] == "2")
																			echo $day = getDayInDutch($this->orderData[0]["order_pickupday"]);
																			else*/
																			$day = $orderData[0]->order_pickupday;
																			echo _($day);
																			 ?>
																		</span>
																		<span id="day_text_box" class="display_none">
																			<input type="text" value="<?php echo $day;?>" class="text veryshort" readonly="readonly" name="order_pickupday" id="order_pickupday"/>
																		</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
																<tr>
																	<td align="left" width="15%" class="textlabel"><?php echo _('PICKTIME');?></td>
																	<td align="left" width="85%">
																		<span id="hour_text">
																			<?php echo $orderData[0]->order_pickuptime?>&nbsp;
																		</span>
																		<span id="hour_unit"><?php echo _("HOUR")?></span>
																		<span id="hour_text_box" class="display_none">
																			<input type="text" value="<?php echo $orderData[0]->order_pickuptime;?>" class="text veryshort" maxlength="5" name="order_pickuptime" id="order_pickuptime"/>
																		</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
																<tr>
																	<td align="left" width="15%" class="textlabel"><?php echo _('NOTE')?></td>
																	<td align="left" width="85%">
																		<span id="remark_text"><?php echo stripslashes( $orderData[0]->order_remarks )?></span>
																		<span id="remark_text_box" class="display_none">
																			<textarea  class="text medium"  name="order_remarks" id="order_remarks"><?php echo stripslashes( $orderData[0]->order_remarks );?></textarea>
																		</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<span id="save_form_1" class="display_none" style="margin-left:70px">
																<input type="button" value="<?php echo _('SAVE')?>" onClick="validate_form_pickup();" name="save" id="save"/>
															</span>
														</td>
													</tr>
												</table>
											</form>
	                      				</td>
									</tr>
	                    			<?php elseif($orderData[0]->option == "0"):?>
									<!--<tr>
									  <td>
										<div class="display_none" id="succeed"></div>
										<div class="display_none" id="error"></div>
										<div>
											<span id="open_form_0">
												<input type="button" value="<?php echo _('EDIT');?>" name="open_form_0" id="open_form_0" onclick="open_close_default_form('open')"/>
											</span>
											<span id="close_form_0" class="display_none">
												<input type="button" value="<?php echo _('CLOSE');?>" name="close_form_0" id="close_form_0" onclick="open_close_pickup_form('close')"/>
											</span>
										</div>
										<form id"frm_order_details_edit" name="frm_order_details_edit">
											<table border="0" cellpadding="0" cellspacing="0" >
												<tr>
													<td>
														<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
															<tr>
																<td align="left" width="15%" class="textlabel"><?php echo _('DATE');?></td>
																<td align="left" width="85%">
																	<span id="date_text">
																		<?php  $da = date("d",strtotime($orderData[0]->order_pickupdate));
																			$mo = date("F",strtotime($orderData[0]->order_pickupdate));
																			$yr = date("y",strtotime($orderData[0]->order_pickupdate));
																																				
																			if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
																			{
																			   if( $mo == 'January' )
																				 $mo = 'Jan';
																			   if( $mo == 'February' )
																				 $mo = 'Febr';
																			   if( $mo == 'March' )
																				 $mo = 'Maart';
																			   if( $mo == 'April' )
																				 $mo = 'April';
																			   if( $mo == 'May' )
																				 $mo = 'Mei';
																			   if( $mo == 'June' )
																				 $mo = 'Juni';
																			   if( $mo == 'July' )
																				 $mo = 'Juli';
																			   if( $mo == 'August' )
																				 $mo = 'Augustus';
																			   if( $mo == 'September' )
																				 $mo = 'September';
																			   if( $mo == 'October' )
																				 $mo = 'Oktober';
																			   if( $mo == 'November' )
																				 $mo = 'November';
																			   if( $mo == 'December' )
																				 $mo = 'December';
																			}
																			
																			echo $date_dutch = $da." ".$mo." ".$yr;
																	?>
																	</span>
																	<span id="date_text_box" class="display_none">
																		<input type="text" class="text veryshort" readonly="readonly" name="order_pickupdate" id="order_pickupdate"  value="<?php echo date('d/m/Y',strtotime($orderData[0]->order_pickupdate));?>"/>
																		<input type="button" value="Kalender" name="calender" id="calender" onclick="displayCalendar(document.frm_order_details_edit.order_pickupdate,'dd/mm/yyyy',this)"/>
																	</span>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td>
														<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
															<tr>
																<td align="left" width="15%" class="textlabel"><?php echo _('HOURS');?></td>
																<td align="left" width="85%">
																	<span id="hour_text">
																		<?php echo $orderData[0]->order_pickuptime;?>&nbsp;<?php echo _('HOUR');?>
																	</span>
																	<span id="hour_text_box" class="display_none">
																		<input type="text" value="<?php echo $orderData[0]->order_pickuptime;?>" class="text veryshort" name="order_pickuptime" id="order_pickuptime"/>
																	</span>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td>
														<table width="100%" cellpadding="0" cellspacing="0" border="0" class="override">
															<tr>
																<td align="left" width="15%" class="textlabel"><?php echo _('NOTE');?></td>
																<td align="left" width="85%">
																	<span id="remark_text"><?php echo stripslashes( $orderData[0]->order_remarks );?></span>
																	<span id="remark_text_box" class="display_none">
																		<textarea value="<?php echo stripslashes( $orderData[0]->order_remarks );?>" class="text medium" name="order_remarks" id="order_remarks"></textarea>
																	</span>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<span id="save_form_0" class="display_none" style="margin-left:70px">
															<input type="button"  value="<?php echo _('SAVE');?>" onClick="validate_form_default();" name="save" id="save"/>
														</span>
													</td>
												</tr>
											</table>
										</form>
									  </td>
									</tr>-->
	                    			<?php endif;?>
	                  			</table>
	                  		</td>
              			</tr>
              			<span id="show1">
						<tr>
                			<td>
	                			<form id="frm_order_product_add" name="frm_order_product_add" action = '<?php echo base_url()?>cp/orders/order_details_edit' method="post">
	                  				<table border="0" cellpadding="0" cellspacing="0" class="override">
	                    				<tr>
	                      					<td width="20%" style="padding-left:10px"><?php echo _('CATEGORY')?>
											: <br />
	                        				  <br />
	                        				  <select required onChange="select_new_subcategory('<?php echo $orderData[0]->id;?>',this.value);" id="select_cat" name="select_cat">
												<?php if($CategoryArray):?>
												
													<option value="0">----<?php echo _('select')?>-----</option>
													<?php foreach($CategoryArray as $category):?>
													
													<option value="<?php echo $category->id?>"><?php echo $category->name;?></option>
													
													<?php endforeach;?>
														
												<?php else:?>
													<option value="-1"><?php echo _('no category available')?></option>
												<?php endif;?>
																									  
											  </select>
											</td>
	                      					<td width="20%" style="padding-left:10px"><?php echo _('SUBCATEGORY');?>
		                        				: <br />
		                       					 <br />
												
		                        				 <select name="select_subcat" id="select_subcat" onchange="select_new_product('<?php echo $orderData[0]->id?>',this.form.select_cat.value,this.value);" required>
		                        				 <option value="0">----<?php echo _('select')?>-----</option>
													<!--------subcategories will be added by method 'select_new_subcategory'----------->
												</select>
											</td>
	                      					<td width="20%" style="padding-left:10px">
												<span id="display_product" style="display:none;"><?php echo _('Product')?>
												 : <br />
	                       						   <br />
	                        					   <select id="newproid" name="newproid" required>
												   <!--products will be added by post request send by method 'select_new_product'-->
												   <option value="0">----<?php echo _('select')?>-----</option>
												   </select>
	                       						</span>
											</td>
	                      					<td width="20%" style="padding-left:10px">
												<span id="display_product1" style="display:none;">
	                        						<input type="submit" class="submit" value="<?php echo _('Add')?>" name="product_add" id="product_add"/>
	                        						<input type="hidden" value="addProduct" name="act" id="act"/>
	                        						<input type="hidden" value="<?php echo $orderData[0]->id?>" name="orderid" id="orderid">
	                        					</span>
											</td>
	                    				</tr>
	                  				</table>
	                  			</form>
                  			</td>
              			</tr>
              			<script language="javascript" type="text/javascript">
				   			var frmValidator = new Validator("frm_order_product_add");
				  	 		frmValidator.EnableMsgsTogether();
				   			frmValidator.addValidation("newproid","dontselect=0","Please select Product");
						</script>
              			</span>
              			<tr>
                			<td>
	                			<table border="1" cellpadding="0" cellspacing="0" >
	                    			<tr>
	                      				<td width="25%" class="textlabel"><?php echo _('PRODUCT')?></td>
	                      				<td width="8%" class="textlabel"><?php echo _('AMOUNT')?></td>
	                      				<td width="35%" class="textlabel">
		                      				<table border="0" cellpadding="0" cellspacing="0" class="override">
		                          				<tr>
		                            				<td width="50%" style="font-size: 14px; font-weight: bold;"><?php echo _('RATE')?></td>
		                            				<td width="50%" style="font-size: 14px; font-weight: bold;"><?php echo _('EXTRA')?></td>
		                          				</tr>
		                        			</table>
	                        			</td>
	                     				<td>&nbsp;</td>
	                      				<td class="textlabel" nowrap="nowrap"><?php echo _('SUB-TOTAL');?></td>
	                      				<td width="20%">&nbsp;</td>
	                    			</tr>
	                    			<form name="frm_order_edit" id="frm_order_edit">
	                    			<?php for($i=0;$i<count($order_details_data);$i++):?>
	                    			<tr>
	                      				<td style="padding-left:20px"><?=$order_details_data[$i]->proname?>
		                        			&nbsp;
		                        			<?php if($order_details_data[$i]->type == "1"):?>
		                        				<img src="<?php echo base_url()?>assets/cp/images/new.png" alt="<?php echo _('New');?>" width="16" height="16" />
		                        			<?php endif;?>
		                        			&nbsp;
		                        			<?php if($order_details_data[$i]->discount != "0" && $order_details_data[$i]->discount != "multi"):?>
		                        				<img src="<?php echo base_url()?>assets/cp/images/message.png" alt="<?php echo _('Discount')?>" width="16" height="16" title="<?php echo $order_details_data[$i]->discount?>" />
		                       				<?php elseif($order_details_data[$i]->discount == "multi"):?>
												<img src="<?php echo base_url()?>assets/cp/images/discount.jpg" width="16" height="16" title="<?php echo  $order_details_data[$i]->discount_per_qty?>" border="0" />
											<?php endif; ?>
										</td>
	                      				<td style="padding-left:20px">
											<span id="qty_edit_id_<?php echo $order_details_data[$i]->id;?>" style="display:inline;"><?php echo $order_details_data[$i]->quantity?><?php if( $order_details_data[$i]->content_type == '1'): echo '&nbsp;gr.';  endif;?>
											</span>
											<span id="qty_update_id_<?php echo $order_details_data[$i]->id?>" style="display:none;">
	                        					<input type="text" style="width:40px" value="<?php echo $order_details_data[$i]->quantity?>" name="qty_<?php echo $order_details_data[$i]->id;?>"  id="qty_<?php echo $order_details_data[$i]->id;?>"/><?php if( $order_details_data[$i]->content_type == '1'):?>&nbsp;&nbsp;gr.<?php endif;?>
	                       					</span>
										</td>
	                      				<td style="padding-left:10px">
		                      				<table border="0" cellpadding="0" cellspacing="0" class="override">
		                          				<tr>
		                            				<td width="20%"><b><?php $actual_price = round(((float)($order_details_data[$i]->sub_total)),2); if( $order_details_data[$i]->content_type == '1'): echo $actual_price.'&nbsp;&euro;/Kg'; elseif($order_details_data[$i]->content_type == '2'):echo $actual_price.'&nbsp;&euro;/'._('Person');else: echo $actual_price.'&nbsp;&euro;';  endif; ?></b></td>
		                            				<td width="80%" style="text-align:center">
		                            					<?php $order_details_id = $order_details_data[$i]->id; ?>
		                            					<div id="pro_group_<?php echo $order_details_id; ?>" style="display:none">
		                            						<table width="100%" border="0" cellpadding="0" cellspacing="0" class="override">
		                            							<?php 
																	$product_groups = $order_details_data[$i]->product_groups; 		
																	$j = 0;
																	if($product_groups):
																	foreach($product_groups as $key => $value):
																	
																	   if( $order_details_data[$i]->content_type == $value[0]['type'])
																	   {
																	   	
																?>
		                                						<tr>                                
																	<td width="40%"><?php echo $value[0]['group_name']; ?></td>
			    	                        						<td width="60%">
																		<?php 
																			if($TempExtracosts[$i] !='' && array_key_exists($j,$TempExtracosts[$i])){
																				$selected_value=implode('_',$TempExtracosts[$i][$j]);
																			}else{
																				$selected_value='0';
																			}
																		?>
																		<input type="hidden" value="<?php echo $selected_value;?>" id="selected_group_<?php echo $order_details_id;?>" name="selected_group" />
																		<select id="groups['<?php echo $order_details_id?>'][]" name="groups['<?php echo $order_details_id?>'][]">
																			<option value = "0"><?php echo _('select').'  '.$value[0]['group_name'];?></option>
																			<?php foreach($value as $attribute):?>
																			<?php 
																			$att_val=$attribute['group_name'].'_'.$attribute['attribute_name'].'_'.$attribute['attribute_value'];
																			$att_string=$attribute['attribute_name'].'('.$attribute['attribute_value'].')';
																			?>
																			<option value="<?php echo $att_val;?>" <?php if( $selected_value == $att_val):?>selected="selected"<?php endif;?>><?php echo $att_string?></option>
																			<?php endforeach;?>																			
																		</select>																			
																	</td>                                
																</tr>
		                                						<?php    
		                                						$j++;
																	    }
																	    
																endforeach;endif;
																?>
		                            						</table>
		                            					</div>
		                            					<div style="display:block" id="pro_group_none_<?php echo $order_details_id; ?>">
															<?php if($TempExtracosts != array() && $TempExtracosts[$i] != ""):?>
			                              						<?php for($j=0;$j<count($TempExtracosts[$i]);$j++): 
								  									echo "<span style=\"color:red;\">".$TempExtracosts[$i][$j][0]."</span> : ".$TempExtracosts[$i][$j][1]." = ".(float)$TempExtracosts[$i][$j][2]."<br>";
									   							endfor; ?>
															<?php endif;?>
			                              					<?php if($order_details_data[$i]->discount == "multi" && !empty($order_details_data[$i]->discount_per_qty)):?>
																
																<?php if($order_details_data[$i]->discount && isset($order_details_data[$i]->discount_per_qty)):?>
																	<span style="color:red;"><?php echo _('Extra Discount')?> :</span>&nbsp;
																	<?php if($order_details_data[$i]->type) { ?>
																	<?=round(((float)$order_details_data[$i]->discount_per_qty)*1000,2); ?>&nbsp;&euro;<br />
																	<?php } else { ?>
																	<?=round((float)$order_details_data[$i]->discount_per_qty,2); ?>&nbsp;&euro;&nbsp;<?php if(round((float)$order_details_data[$i]->discount_per_qty,2) ) { ?>(<?=_('on ').$order_details_data[$i]->discount_on_items._(' items');?>)<?php } ?><br />
																	<?php } ?>
																<?php endif;?>
										
															<?php endif;?>
			                              					<?php if($order_details_data[$i]->pro_remark != ""):?>
																<span><?php echo _('Note')?> :</span>&nbsp;
																<span><?php echo stripslashes( $order_details_data[$i]->pro_remark )?></span><br><br>
															<?php else:?>
															    <span>--</span>													
																<br><br>
															<?php endif;?>
		                              					</div>
		                              				</td>
		                          				</tr>
		                        			</table>
	                        			</td>
	                      				<td>&nbsp;</td>
	                      				<td><b>&euro;&nbsp;<?php echo round((float)$order_details_data[$i]->total,2); ?></b></td>
	                      				<td nowrap="nowrap">
											<span id="edit_id_<?php echo $order_details_data[$i]->id?>" style="display:inline;">
	                        					<?php //echo $order_details_data[$i]->default_price?>
												<a href="javascript:void(0);" onClick="return edit_order_product_hide('<?php echo $order_details_data[$i]->id?>');">
													<img src="<?php echo base_url(); ?>assets/cp/images/edit.gif" title="<?php echo _('EDIT')?>" alt="<?php echo _('EDIT')?>" />
												</a>
	                        				</span>
											<span id="update_edit_id_<?php echo $order_details_data[$i]->id?>" style="display:none;">
												<input type="button" class="submit" value="<?php echo _('UPDATED')?>" onClick="return edit_order_product('<?php echo $order_details_data[$i]->id?>','<?php echo $orderData[0]->id?>','<?php echo $actual_price;?>','<?php echo $order_details_data[$i]->content_type?>','<?php echo $order_details_data[$i]->default_price?>','<?php echo $order_details_data[$i]->discount?>','<?php echo $order_details_data[$i]->products_id?>')" name="Update" id="Update"/>
												<input type="button" class="submit" value="<?php echo _('CLOSE')?>" onClick="return close_edit_order_product_hide('<?php echo $order_details_data[$i]->id?>');" name="close" id="close"/>
	                        				</span>
											&nbsp;|&nbsp;
											<span>
												<a href="javascript:void(0);" onClick="return delete_ordered_product_confirmation('<?php echo $order_details_data[$i]->id?>','<?php echo $orderData[0]->id?>','<?php echo $order_details_data[$i]->total;//echo $actual_price; ?>');">
													<img src="<?php echo base_url(); ?>assets/cp/images/delete.gif" alt="<?php echo _('REMOVE')?>" width="16" height="16" title="<?php echo _('REMOVE')?>" />
												</a>
											</span>
											<?php if($order_details_data[$i]->image != '' && ( strpos($order_details_data[$i]->image,"http:") !== false || strpos($order_details_data[$i]->image,"https:") !== false ) ){?>
											<span>
												<a href="javascript: void(0);" class="download_image" rel="<?php echo $order_details_data[$i]->id;?>" >
													<span class="download_toggle"><img src="<?php echo base_url(); ?>assets/cp/images/download_labeler.png" alt="<?php echo _('Download')?>" width="16" height="16" title="<?php echo _('DOWNLOAD IMAGE')?>" /></span>
													<span class="download_toggle" style="display:none;" ><img src="<?php echo base_url(); ?>assets/cp/images/loading-circle.gif" width="16" height="16" alt="<?php echo _('Wait')?>"/></span>
												</a>
											</span>
											<?php }?>
										</td>
	                    			</tr>
	                    			<?php endfor;?>
	                    			</form>
	                    			<tr>
	                      				<td>&nbsp;</td>
	                      				<td>&nbsp;</td>
	                     				<td>
	                     					<form name="frm_order_status_update" id="frm_order_status_update" action="<?php echo base_url()?>cp/orders/order_details_edit" method="post">
		                        				<table border="0" cellpadding="0" cellspacing="0" class="override">
		                          					<tr>
		                            					<td style="vertical-align:top;"><strong><?php echo _('STATUS')?></strong></td>
		                            					<td style="vertical-align:middle;">
															<?php if($orderData[0]->order_status == 'y'){
																$status = 'y';													
															}else if($orderData[0]->order_status == 'n'){
																$status = 'n';		
															}
															?>
															<select  name="order_status" id="order_status">
																<option value="0"><?php echo _('--select--')?></option>
																<option value="y" <?php if($status == 'y'):?>selected = 'selected'<?php endif;?>><?php echo _('is ready')?></option>
																<option value="n" <?php if($status == 'n'):?>selected = 'selected'<?php endif;?>><?php echo _('pending')?></option>															
															</select>
														</td>
		                            					<td style="vertical-align:top;">
															<input type="submit" class="submit" value="<?php echo _('UPDATED')?>" name="status_update" id="status_update" />
		                              						<input type="hidden" value="update_order_status" name="act" id="act"/>
															<input type="hidden" value="<?php echo $orderData[0]->id?>" name="oid" id="oid">
														</td>
		                          					</tr>
		                        				</table>
		                        			</form>
	                        			</td>
	                      				<?php if($orderData[0]->option == "2"):?>
						  				<td><?php echo _('ORDER-TOTAL')?></td>
	                      				<td><b>&euro;&nbsp;<?php echo round((float)$total,2)?></b></td>
										<?php else:?>
										<td colspan="2">&nbsp;</td>
										<?php endif;?>
										
	                      				<td>&nbsp;</td>
	                    			</tr>
	                    			<script language="javascript" type="text/javascript">
						   				var frmValidator1 = new Validator("frm_order_status_update");
						   				frmValidator1.EnableMsgsTogether();
						   				frmValidator1.addValidation("order_status","dontselect=0","Please select Status Value !!");
									</script>
	                    			<?php if($orderData[0]->option == "2"):?>
	                    			<tr>
	                      				<td>&nbsp;</td>
	                      				<td>&nbsp;</td>
	                      				<td>&nbsp;</td>
	                      				<td><?php echo _('DELIVERY-TOTAL');?></td>
	                      				<td><b>&euro;&nbsp;	<span id="delivery_cost">
	                        			<?php echo round((float)$orderData[0]->delivery_cost,2)?>
	                        			</span> </b></td>
	                      				<td>&nbsp;</td>
	                    			</tr>
	                    			<?php endif; ?>
	                    			
	                    			<!-- --------------- DISCOUNT PER CLIENT ------------------------------ -->
									<?php
										$disc_client_amount = 0;
										if($orderData[0]->disc_client_amount > 0 ){
											$disc_client_amount = $orderData[0]->disc_client_amount;
										}
									?>
									
									<?php if($disc_client_amount > 0 && $orderData[0]->disc_client > 0){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2" style="text-align:right;"><?php echo _('LOYALITY DISCOUNT')?>(<?php echo $orderData[0]->disc_client;?>%)</td>
										<td>-<b> &euro;&nbsp;<?php echo round(( (float)$disc_client_amount ),2)?></b></td>
										<td>&nbsp;</td>
									</tr>
									<?php }?>
									<!-- ------------------------------------------------------------------ -->
									
									<!-- --------------- DISCOUNT INTROCODE/PROMOCODE ------------------------------ -->
									<?php
										$disc_other_code = 0;
										if($orderData[0]->disc_other_code > 0 ){ 
											$disc_other_code = $orderData[0]->disc_other_code;
										}
									?>
									
									<?php if($disc_other_code > 0 && $orderData[0]->other_code_type != ''){?>
									<?php if($orderData[0]->other_code_type == 'introcode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2" style="text-align:right;"><?php echo _('INTROCODE DISCOUNT')?></td>
										<td>-<b> &euro;&nbsp;<?php echo round(( (float)$disc_other_code ),2)?></b></td>
										<td>&nbsp;</td>
									</tr>
									<?php }?>
									<?php if($orderData[0]->other_code_type == 'promocode'){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2" style="text-align:right;"><?php echo _('PROMOCODE DISCOUNT')?></td>
										<td>-<b> &euro;&nbsp;<?php echo round(( (float)$disc_other_code ),2)?></b></td>
										<td>&nbsp;</td>
									</tr>
									<?php }?>
									<?php }?>
									<!-- ------------------------------------------------------------------ -->
									
									<!-- --------------- DISCOUNT PER AMOUNT ------------------------------- -->
									<?php
										$disc_amount = 0;
										if($orderData[0]->disc_amount > 0 ){
											$disc_amount = $orderData[0]->disc_amount;
										}
									?>
									
									<?php if($disc_amount > 0 && $orderData[0]->disc_percent > 0){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2" style="text-align:right;"><?php echo _('DISCOUNT')?>(<?php echo $orderData[0]->disc_percent;?>%)</td>
										<td>-<b> &euro;&nbsp;<?php echo round(( (float)$disc_amount ),2)?></b></td>
										<td>&nbsp;</td>
									</tr>
									<?php }elseif($disc_amount > 0 && $orderData[0]->disc_price > 0){?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="2" style="text-align:right;"><strong><?php echo _('DISCOUNT')?>(<?php echo $orderData[0]->disc_price;?>&euro;)</strong></td>
										<td>-<b> &euro;&nbsp;<?php echo round(( (float)$disc_amount ),2)?></b></td>
									</tr>
									<?php }?>
									<!-- ------------------------------------------------------------------ -->
		
	                    			<tr>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td><?php echo _('TOTAL')?></td>
									  <td >
									  	<b>&euro;&nbsp;
										  	<span id="total_cost">
												<?php echo round((float)($total+$orderData[0]->delivery_cost - (round(( (float)$orderData[0]->disc_client_amount ),2)+round(( (float)$orderData[0]->disc_other_code ),2)+round(( (float)$orderData[0]->disc_amount ),2))),2)?>
											</span>
										</b>
									  </td>
									  <td>&nbsp;</td>
	                    			</tr>
	                  			</table>
                  			</td>
              			</tr>
            		</table>
            		<script language="javascript" type="text/javascript">
				/*var frmValidator = new Validator("frm_order_edit");
				frmValidator.EnableMsgsTogether();*/
					</script>
          		</div>
        	</div>
    	
		
		</div><!--/content_container --->
	</div><!-- /content -->
   
<script language="javascript" type="text/javascript">
showHIdeDiv();
</script>

<script type="text/javascript" src="<?php echo base_url();?>assets/cp/new_js/order_details_new.js">
</script>